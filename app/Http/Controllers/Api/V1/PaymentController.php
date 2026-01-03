<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\BillPay as BillPayModel;
use App\Models\Deposit;
use App\Models\Fund;
use App\Models\Gateway;
use App\Models\Transaction;
use App\Traits\ApiValidation;
use App\Traits\BillPay;
use App\Traits\Notify;
use App\Traits\Upload;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Stevebauman\Purify\Facades\Purify;
use Facades\App\Services\BasicService;

class PaymentController extends Controller
{
	use ApiValidation, Notify, BillPay, Upload;

	public function walletPayment(Request $request)
	{
		$purifiedData = Purify::clean($request->all());
		$validationRules = [
			'billPayId' => 'required',
		];
		$validate = Validator::make($purifiedData, $validationRules);
		if ($validate->fails()) {
			return response()->json($this->withErrors(collect($validate->errors())->collapse()[0]));
		}

		try {
			$billPay = BillPayModel::whereIn('status', ['0,1'])->find($purifiedData['billPayId']);
			if (!$billPay) {
				return response()->json($this->withErrors('Record not found'));
			}

			if ($billPay->pay_amount_in_base > auth()->user()->balance) {
				return response()->json($this->withErrors('Insufficient wallet balance. Available balance ' . getAmount(auth()->user()->balance, 2) . config('basic.base_currency')));
			}

			if ($this->payWithWallet($billPay)) {
				return response()->json($this->withSuccess('Payment has been receive'));
			}
			return response()->json($this->withErrors('Something went wrong'));
		} catch (\Exception $e) {
			return response()->json($this->withErrors($e->getMessage()));
		}
	}

	public function payWithWallet($billPay)
	{
		try {
			updateWallet(auth()->id(), $billPay->pay_amount_in_base, 0);
			$billPay->status = 2;
			$billPay->save();

			$transaction = new Transaction();
			$transaction->amount = $billPay->pay_amount_in_base;
			$transaction->charge = 0;
			$transaction->remark = getAmount($billPay->pay_amount_in_base, 3) . ' ' . config('basic.base_currency') . ' payment for ' . $billPay->type;
			$billPay->transactional()->save($transaction);

			$billPayMethod = $billPay->method;

			$methodObj = 'App\\Services\\Bill\\' . $billPayMethod->code . '\\Card';

			if ($billPayMethod->code == 'reloadly' && $billPay->category_name == 'AIRTIME') {
				$response = $methodObj::payAirtimeBill($billPay, $billPayMethod);
			} else {
				$response = $methodObj::payBill($billPay, $billPayMethod);
			}

			if ($response['status'] == 'success') {
				$billPay->status = 3;
				$billPay->save();

				$params = [
					'type' => $billPay->type,
					'amount' => getAmount($billPay->payable_amount, 2),
					'currency' => $billPay->currency,
					'transaction' => $billPay->utr,
				];
				$action = [
					"link" => "#",
					"icon" => "fa fa-money-bill-alt text-white"
				];

				$this->sendMailSms($billPay->user, 'BILL_PAYMENT', $params);
				$this->userPushNotification($billPay->user, 'BILL_PAYMENT', $params, $action);
				$this->userFirebasePushNotification($billPay->user, 'BILL_PAYMENT', $params);
				$this->adminPushNotification('BILL_PAYMENT', $params, $action);

			} elseif ($response['status'] == 'processing') {
				$billPay->status = 5;
				$billPay->reference_id = $response['data'];
				$billPay->save();
			} else {
				$billPay->last_api_error = $response['data'];
				$billPay->save();
			}
			return true;
		} catch (\Exception $e) {
			return false;
		}
	}

	public function manualPaymentSubmit(Request $request)
	{
		$purifiedData = Purify::clean($request->all());
		$validationRules = [
			'billPayId' => 'required',
			'gatewayId' => 'required|numeric|min:1000',
			'amount' => 'sometimes|min:0|not_in:0'
		];
		$validate = Validator::make($purifiedData, $validationRules);
		if ($validate->fails()) {
			return response()->json($this->withErrors(collect($validate->errors())->collapse()[0]));
		}

		try {
			if ($purifiedData['billPayId'] == "-1") {
				if (!$request->amount) {
					return response()->json($this->withErrors('Amount Field is Required'));
				}
				$baseAmount = $request->amount;
				$model = Fund::class;
			} else {
				$billPay = BillPayModel::whereIn('status', ['0,1'])->find($purifiedData['billPayId']);
				if (!$billPay) {
					return response()->json($this->withErrors('Record not found'));
				}
				$baseAmount = $billPay->pay_amount_in_base;
				$model = BillPayModel::class;
			}

			$checkAmountValidate = $this->checkAmountValidate($baseAmount, $request->gatewayId);
			if (!$checkAmountValidate['status']) {
				return response()->json($this->withErrors($checkAmountValidate['message']));
			}
			$method = Gateway::where('status', 1)->find($purifiedData['gatewayId']);
			if (!$method) {
				return response()->json($this->withErrors('Gateway not found'));
			}
			$params = $method->parameters;
			$rules = [];
			$inputField = [];

			$verifyImages = [];

			if ($params != null) {
				foreach ($params as $key => $cus) {
					$rules[$key] = [$cus->validation];
					if ($cus->type == 'file') {
						array_push($rules[$key], 'image');
						array_push($rules[$key], 'mimes:jpeg,jpg,png');
						array_push($rules[$key], 'max:2048');
						array_push($verifyImages, $key);
					}
					if ($cus->type == 'text') {
						array_push($rules[$key], 'max:191');
					}
					if ($cus->type == 'textarea') {
						array_push($rules[$key], 'max:300');
					}
					$inputField[] = $key;
				}
			}

			$validate = Validator::make($request->all(), $rules);
			if ($validate->fails()) {
				return response()->json($this->withErrors(collect($validate->errors())->collapse()[0]));
			}

			$deposit = new Deposit();
			$deposit->user_id = auth()->id();
			$deposit->payment_method_id = $method->id;
			$deposit->amount = $baseAmount;
			$deposit->percentage = $checkAmountValidate['percentage'];
			$deposit->charge_percentage = $checkAmountValidate['percentage_charge'];
			$deposit->charge_fixed = $checkAmountValidate['fixed_charge'];
			$deposit->charge = $checkAmountValidate['charge'];
			$deposit->payable_amount = $checkAmountValidate['payable_amount'] * $checkAmountValidate['convention_rate'];
			$deposit->utr = Str::random(16);
			$deposit->status = 0;// 1 = success, 0 = pending
			$deposit->email = auth()->user()->email;
			$deposit->payment_method_currency = $method->currency;
			$deposit->depositable_type = $model;
			$deposit->depositable_id = $billPay->id ?? null;
			$deposit->save();


			$path = config('location.deposit.path') . date('Y') . '/' . date('m') . '/' . date('d');
			$collection = collect($request);

			$reqField = [];
			if ($params != null) {
				foreach ($collection as $k => $v) {
					foreach ($params as $inKey => $inVal) {
						if ($k != $inKey) {
							continue;
						} else {
							if ($inVal->type == 'file') {
								if ($request->hasFile($inKey)) {
									try {
										$reqField[$inKey] = [
											'field_name' => $this->fileUpload($request[$inKey], $path),
											'type' => $inVal->type,
										];
									} catch (\Exception $exp) {
										return response()->json($this->withErrors('Could not upload your ' . $inKey));
									}
								}
							} else {
								$reqField[$inKey] = [
									'field_name' => $v,
									'type' => $inVal->type,
								];
							}
						}
					}
				}
				$deposit->detail = $reqField;
			} else {
				$deposit->detail = null;
			}

			$deposit->created_at = Carbon::now();
			$deposit->status = 2; // pending
			$deposit->update();


			$msg = [
				'username' => $deposit->receiver->username,
				'amount' => getAmount($deposit->amount, config('basic.fraction_number')),
				'currency' => config('basic.base_currency'),
				'gateway' => $method->name
			];
			$action = [
				"link" => "#",
				"icon" => "fa fa-money-bill-alt text-white"
			];
			$this->adminPushNotification('PAYMENT_REQUEST', $msg, $action);
			return response()->json($this->withSuccess('You request has been taken.'));
		} catch (\Exception $e) {
			return response()->json($this->withErrors($e->getMessage()));
		}
	}

	public function automationPayment(Request $request)
	{
		$validateUser = Validator::make($request->all(),
			[
				'billPayId' => 'required',
				'gatewayId' => 'required',
				'amount' => 'sometimes|min:0|not_in:0'
			]);

		if ($validateUser->fails()) {
			return response()->json($this->withErrors(collect($validateUser->errors())->collapse()[0]));
		}
		$method = Gateway::select('id', 'currency')->where('id', $request->gatewayId)->toBase()->first();
		if (!$method) {
			return response()->json($this->withErrors('Gateway not found'));
		}

		if ($request->billPayId == "-1") {
			if (!$request->amount) {
				return response()->json($this->withErrors('Amount Field is Required'));
			}
			$baseAmount = $request->amount;
			$model = Fund::class;
		} else {
			$billPay = BillPayModel::whereIn('status', ['0,1'])->find($request->billPayId);
			$billPay->payment_method_id = $request->gatewayId;
			$billPay->save();
			$baseAmount = $billPay->pay_amount_in_base;
			$model = BillPayModel::class;
		}


		$checkAmountValidate = $this->checkAmountValidate($baseAmount, $request->gatewayId);
		if (!$checkAmountValidate['status']) {
			return response()->json($this->withErrors($checkAmountValidate['message']));
		}

		if ($billPay && $method) {
			$deposit = new Deposit();
			$deposit->user_id = auth()->id();
			$deposit->payment_method_id = $method->id;
			$deposit->amount = $baseAmount;
			$deposit->percentage = $checkAmountValidate['percentage'];
			$deposit->charge_percentage = $checkAmountValidate['percentage_charge'];
			$deposit->charge_fixed = $checkAmountValidate['fixed_charge'];
			$deposit->charge = $checkAmountValidate['charge'];
			$deposit->payable_amount = $checkAmountValidate['payable_amount'] * $checkAmountValidate['convention_rate'];
			$deposit->utr = Str::random(16);
			$deposit->status = 0;// 1 = success, 0 = pending
			$deposit->email = auth()->user()->email;
			$deposit->payment_method_currency = $method->currency;
			$deposit->depositable_type = $model;
			$deposit->depositable_id = $billPay->id ?? null;
			$deposit->save();

			$val['url'] = route('paymentView', $deposit->utr);
			return response()->json($this->withSuccess($val));
		}

		return response()->json($this->withErrors('Record not found'));
	}

	public function paymentView($utr)
	{
		$deposit = Deposit::latest()->where('utr', $utr)->first();
		try {
			if ($deposit) {
				$getwayObj = 'App\\Services\\Gateway\\' . $deposit->gateway->code . '\\Payment';
				$data = $getwayObj::prepareData($deposit, $deposit->gateway);
				$data = json_decode($data);
				if (isset($data->error)) {
					return response()->json($this->withErrors($data->message));
				}

				if (isset($data->redirect)) {
					return redirect($data->redirect_url);
				}

				if ($data->view) {
					$parts = explode(".", $data->view);
					$desiredValue = end($parts);
					$newView = 'mobile-payment.' . $desiredValue;
					return view($newView, compact('data', 'deposit'));
				}
				abort(404);
			}
		} catch (\Exception $e) {
			return response()->json($this->withErrors($e->getMessage()));
		}
	}

	public function cardPayment(Request $request)
	{
		$validateUser = Validator::make($request->all(),
			[
				'billPayId' => 'required',
				'gatewayId' => 'required',
				'amount' => 'sometimes|min:0|not_in:0'
			]);

		if ($validateUser->fails()) {
			return response()->json($this->withErrors(collect($validateUser->errors())->collapse()[0]));
		}
		$method = Gateway::where('id', $request->gatewayId)->first();
		if (!$method) {
			return response()->json($this->withErrors('Gateway not found'));
		}
		if ($request->billPayId == "-1") {
			if (!$request->amount) {
				return response()->json($this->withErrors('Amount Field is Required'));
			}
			$baseAmount = $request->amount;
			$model = Fund::class;
		} else {
			$billPay = BillPayModel::whereIn('status', ['0,1'])->latest()->find($request->billPayId);
			$billPay->payment_method_id = $request->gatewayId;
			$billPay->save();

			if (!$billPay) {
				return response()->json($this->withErrors('Record not found'));
			}
			$baseAmount = $billPay->pay_amount_in_base;
			$model = BillPayModel::class;
		}


		$checkAmountValidate = $this->checkAmountValidate($baseAmount, $request->gatewayId);
		if (!$checkAmountValidate['status']) {
			return response()->json($this->withErrors($checkAmountValidate['message']));
		}

		$deposit = new Deposit();
		$deposit->user_id = auth()->id();
		$deposit->payment_method_id = $method->id;
		$deposit->amount = $baseAmount;
		$deposit->percentage = $checkAmountValidate['percentage'];
		$deposit->charge_percentage = $checkAmountValidate['percentage_charge'];
		$deposit->charge_fixed = $checkAmountValidate['fixed_charge'];
		$deposit->charge = $checkAmountValidate['charge'];
		$deposit->payable_amount = $checkAmountValidate['payable_amount'] * $checkAmountValidate['convention_rate'];
		$deposit->utr = Str::random(16);
		$deposit->status = 0;// 1 = success, 0 = pending
		$deposit->email = auth()->user()->email;
		$deposit->payment_method_currency = $method->currency;
		$deposit->depositable_type = $model;
		$deposit->depositable_id = $billPay->id ?? null;
		$deposit->save();

		$getwayObj = 'App\\Services\\Gateway\\' . $method->code . '\\Payment';
		$data = $getwayObj::mobileIpn($request, $method, $deposit);
		if ($data == 'success') {
			return response()->json($this->withSuccess('Payment has been complete'));
		} else {
			return response()->json($this->withErrors('unsuccessful transaction.'));
		}
	}

	public function paymentDone(Request $request)
	{
		try {
			$validateUser = Validator::make($request->all(),
				[
					'billPayId' => 'required',
					'gatewayId' => 'required',
					'amount' => 'sometimes|min:0|not_in:0'
				]);

			if ($validateUser->fails()) {
				return response()->json($this->withErrors(collect($validateUser->errors())->collapse()[0]));
			}

			$method = Gateway::where('id', $request->gatewayId)->first();
			if (!$method) {
				return response()->json($this->withErrors('Gateway not found'));
			}

			if ($request->billPayId == "-1") {
				if (!$request->amount) {
					return response()->json($this->withErrors('Amount Field is Required'));
				}
				$baseAmount = $request->amount;
				$model = Fund::class;
			} else {
				$billPay = BillPayModel::whereIn('status', ['0,1'])->latest()->find($request->billPayId);
				$billPay->payment_method_id = $request->gatewayId;
				$billPay->save();

				if (!$billPay) {
					return response()->json($this->withErrors('Record not found'));
				}
				$baseAmount = $billPay->pay_amount_in_base;
				$model = BillPayModel::class;
			}


			$checkAmountValidate = $this->checkAmountValidate($baseAmount, $request->gatewayId);
			if (!$checkAmountValidate['status']) {
				return response()->json($this->withErrors($checkAmountValidate['message']));
			}

			$deposit = new Deposit();
			$deposit->user_id = auth()->id();
			$deposit->payment_method_id = $method->id;
			$deposit->amount = $baseAmount;
			$deposit->percentage = $checkAmountValidate['percentage'];
			$deposit->charge_percentage = $checkAmountValidate['percentage_charge'];
			$deposit->charge_fixed = $checkAmountValidate['fixed_charge'];
			$deposit->charge = $checkAmountValidate['charge'];
			$deposit->payable_amount = $checkAmountValidate['payable_amount'] * $checkAmountValidate['convention_rate'];
			$deposit->utr = Str::random(16);
			$deposit->status = 0;// 1 = success, 0 = pending
			$deposit->email = auth()->user()->email;
			$deposit->payment_method_currency = $method->currency;
			$deposit->depositable_type = $model;
			$deposit->depositable_id = $billPay->id ?? null;
			$deposit->save();

			BasicService::prepareOrderUpgradation($deposit);
			return $this->withSuccess('Payment has been completed');
		} catch (\Exception $e) {
			return $this->withErrors($e->getMessage());
		}
	}

}
