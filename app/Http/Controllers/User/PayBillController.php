<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\BillService;
use App\Models\Deposit;
use App\Models\Fund;
use App\Models\Gateway;
use App\Models\Transaction;
use App\Traits\BillPay;
use App\Models\BillPay as BillPayModel;
use App\Traits\Notify;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Stevebauman\Purify\Facades\Purify;

class PayBillController extends Controller
{
	use BillPay, Notify;

	public function __construct()
	{
		$this->middleware(['auth']);
		$this->middleware(function ($request, $next) {
			$this->user = auth()->user();
			return $next($request);
		});
		$this->theme = template();
	}

	public function payBill()
	{
		try {
			$responses = config('billservices.' . $this->getActiveMethod()->code);
			$activeServices = [];

			foreach ($this->getActiveServices() as $service) {
				$activeServices[$service] = [
					'name' => $responses[$service]['name'],
					'image' => asset(config('billservices.imagePath') . $responses[$service]['image'])
				];
			}

			return view($this->theme . 'user.pay_bill.index', compact('activeServices'));
		} catch (\Exception $e) {
			return back()->with('alert', $e->getMessage());
		}
	}

	public function payBillSelect($code)
	{
		if (!BillService::where('status', 1)->where('service', $code)->exists()) {
			abort(404);
		}

		try {
			$countryLists = [];
			$countries = BillService::select(['id', 'bill_method_id', 'status', 'country'])
				->where('bill_method_id', $this->getActiveMethod()->id)
				->where('service', $code)
				->where('status', 1)->groupBy('country')->get()->map(function ($query) use ($countryLists) {
					foreach (config('country') as $county) {
						if ($county['code'] == $query->country) {
							$countryLists[$query->country] = [
								'name' => $county['name'],
								'currency' => $county['iso_code'],
							];
						}
					}
					return $countryLists;
				});

			$data['countries'] = collect($countries)->collapse();
			$data['category'] = $code;
			return view($this->theme . 'user.pay_bill.form', $data);
		} catch (\Exception $e) {
			return back()->with('alert', $e->getMessage());
		}
	}

	public function fetchServices(Request $request)
	{
		$category = $request->category;
		$code = $request->code;
		$services = BillService::where(['service' => $category, 'country' => $code, 'status' => 1])->get();
		return response()->json([
			'status' => 'success',
			'data' => $services
		]);
	}

	public function fetchAmount(Request $request)
	{
		$services = BillService::where('status', 1)->find($request->serviceId);
		return response()->json([
			'status' => 'success',
			'data' => $services->extra_response
		]);
	}

	public function payBillSubmit(Request $request)
	{
		$purifiedData = Purify::clean($request->all());
		$service = BillService::with(['method'])->findOrFail($purifiedData['service']);
		$validationRules = [
			'country' => 'required',
			'service' => 'required',
			'amount' => 'sometimes|required',
		];

		if ($service->label_name != null) {
			foreach ($service->label_name as $key => $cus) {
				$validationRules[$cus] = ['required'];
				array_push($validationRules[$cus], 'max:250');
				$input[$cus] = [
					$cus => $request->$cus
				];
			}
		}
		$validate = Validator::make($purifiedData, $validationRules);
		if ($validate->fails()) {
			return back()->withErrors($validate)->withInput();
		}

		try {
			$amount = ($service->amount > 0) ? $service->amount : $purifiedData['amount'];
			if ($service->method->code == 'reloadly' && $service->amount == -1) {
				$amount = getReloadlyAmount($service, $purifiedData['amount']);
			}

			if ($service->min_amount > $amount) {
				session()->flash('alert', 'Amount must be greater than ' . $service->min_amount . ' ' . $service->currency);
				return back()->withInput();
			}
			if ($service->max_amount > 0 && $service->max_amount < $amount) {
				session()->flash('alert', 'Amount must be smaller than ' . $service->max_amount . ' ' . $service->currency);
				return back()->withInput();
			}

			if (!$this->convertRate($service)) {
				return back()->withInput()->with('alert', 'Something went wrong');
			}

			if ($service->label_name != null) {
				foreach ($service->label_name as $key => $cus) {
					$customerInput[$cus] = [
						$cus => $request->$cus
					];
				}
			}

			$charge = $this->calculateCharge($amount, $service);
			$rate = $this->convertRate($service);

			$billPay = new BillPayModel();
			$billPay->method_id = $service->bill_method_id;
			$billPay->user_id = $this->user->id;
			$billPay->service_id = $service->id;
			$billPay->customer = $customerInput;
			$billPay->type = $service->type;
			$billPay->category_name = $service->service;
			$billPay->country_name = $purifiedData['country'];
			$billPay->amount = $amount;
			$billPay->charge = $charge;
			$billPay->payable_amount = $amount + $charge;
			$billPay->currency = $service->currency;
			$billPay->exchange_rate = $rate;
			$billPay->pay_amount_in_base = $billPay->payable_amount / $rate;
			$billPay->utr = Str::random(16);
			$billPay->status = 0;
			$billPay->amount_id = ($service->amount == -1) ? $purifiedData['amount'] : null;

			$billPay->save();

			return redirect()->route('pay.bill.preview', $billPay->utr);
		} catch (\Exception $e) {
			return back()->with('alert', $e->getMessage());
		}
	}

	public function payBillPreview($utr)
	{
		$data['billPay'] = BillPayModel::with(['service', 'method'])->where('utr', $utr)->whereIn('status', [0, 1])->latest()->firstOrFail();
		$data['gateways'] = Gateway::select(['id', 'image', 'driver', 'name', 'sort_by'])->where('status', 1)->orderBy('sort_by', 'asc')->get();
		return view($this->theme . 'user.pay_bill.preview', $data);
	}

	public function gatewayChargeFetch(Request $request)
	{
		$gateway = Gateway::where('status', 1)->find($request->gatewayId);
		try {
			if ($gateway) {
				return [
					'status' => 'success',
					'min_amount' => getAmount($gateway->min_amount, 2),
					'max_amount' => getAmount($gateway->max_amount, 2),
					'percentage_charge' => getAmount($gateway->percentage_charge, 2),
					'fixed_charge' => getAmount($gateway->fixed_charge, 2),
					'totalPay' => getAmount(($request->billInBase) + (($gateway->percentage_charge * $request->billInBase) / 100) + ($gateway->fixed_charge), 2),
				];
			}

			return [
				'status' => 'error',
				'msg' => 'Gateway not found'
			];
		} catch (\Exception $e) {
			return [
				'status' => 'error',
				'msg' => $e->getMessage()
			];
		}
	}

	public function payBillPreviewConfirm(Request $request, $utr)
	{
		$purifiedData = Purify::clean($request->all());
		$validationRules = [
			'gatewayId' => 'required',
		];
		$validate = Validator::make($purifiedData, $validationRules);
		if ($validate->fails()) {
			return back()->withErrors($validate)->withInput();
		}

		DB::beginTransaction();
		try {
			$billPay = BillPayModel::with(['method', 'service'])->whereIn('status', [0, 1])->where('utr', $utr)->latest()->firstOrFail();
			$billPay->status = 1;
			$billPay->payment_method_id = $request->gatewayId;
			$billPay->save();

			if ($request->gatewayId == -1) { //Wallet payment
				if ($billPay->pay_amount_in_base > auth()->user()->balance) {
					return back()->with('alert', 'Insufficient wallet balance. Available balance ' . getAmount(auth()->user()->balance, 2) . config('basic.base_currency'));
				}

				if ($this->payWithWallet($billPay)) {
					DB::commit();
					session()->flash('success', 'Payment has been receive');
					return redirect()->route('pay.bill.list');
				}
				return back()->with('alert', 'Something went wrong');
			}

			$checkAmountValidate = $this->checkAmountValidate($billPay->pay_amount_in_base, $request->gatewayId);
			if (!$checkAmountValidate['status']) {
				return back()->withInput()->with('alert', $checkAmountValidate['message']);
			}

			$method = Gateway::where('status', 1)->findOrFail($request->gatewayId);
			$deposit = new Deposit();
			$deposit->user_id = $this->user->id;
			$deposit->payment_method_id = $method->id;
			$deposit->amount = $billPay->pay_amount_in_base;
			$deposit->percentage = $checkAmountValidate['percentage'];
			$deposit->charge_percentage = $checkAmountValidate['percentage_charge'];
			$deposit->charge_fixed = $checkAmountValidate['fixed_charge'];
			$deposit->charge = $checkAmountValidate['charge'];
			$deposit->payable_amount = $checkAmountValidate['payable_amount'] * $checkAmountValidate['convention_rate'];
			$deposit->utr = Str::random(16);
			$deposit->status = 0;// 1 = success, 0 = pending
			$deposit->email = $this->user->email;
			$deposit->payment_method_currency = $method->currency;
			$deposit->depositable_type = BillPayModel::class;
			$deposit->depositable_id = $billPay->id;

			$deposit->save();
			DB::commit();
			return redirect(route('payment.process', $deposit->utr));
		} catch (\Exception $e) {
			DB::rollBack();
			return back()->with('alert', $e->getMessage());
		}
	}

	public function payWithWallet($billPay)
	{
		try {
			updateWallet($this->user->id, $billPay->pay_amount_in_base, 0);
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

	public function payBillList(Request $request)
	{
		$search = $request->all();
		$created_date = isset($search['created_at']) ? preg_match("/^[0-9]{2,4}-[0-9]{1,2}-[0-9]{1,2}$/", $search['created_at']) : 0;

		$data['bills'] = BillPayModel::own()->with('method')
			->when(isset($search['category']), function ($query) use ($search) {
				return $query->where('category_name', 'LIKE', "%{$search['category']}%");
			})
			->when(isset($search['type']), function ($query) use ($search) {
				return $query->where('type', 'LIKE', "%{$search['type']}%");
			})
			->when(isset($search['status']), function ($query) use ($search) {
				if ($search['status'] == 'pending') {
					return $query->where('status', 2);
				} elseif ($search['status'] == 'success') {
					return $query->where('status', 3);
				} elseif ($search['status'] == 'return') {
					return $query->where('status', 4);
				}
			})
			->when($created_date == 1, function ($query) use ($search) {
				return $query->whereDate("created_at", $search['created_at']);
			})
			->whereIn('status', [2, 3, 4, 5])
			->latest()->paginate(config('basic.paginate'));
		return view($this->theme . 'user.pay_bill.list', $data);
	}

	public function payBillDetails($id)
	{
		$data['billDetails'] = BillPayModel::own()->with(['gateway', 'method'])->findOrFail($id);
		return view($this->theme . 'user.pay_bill.details', $data);
	}

	public function payBillRequest()
	{
		$data['deposits'] = Deposit::own()->whereIn('status', [2, 3])
			->where('payment_method_id', '>', 999)->where('depositable_type', BillPayModel::class)
			->latest()->with('receiver', 'gateway')->paginate(config('basic.paginate'));
		return view($this->theme . 'user.pay_bill.requestList', $data);
	}
}
