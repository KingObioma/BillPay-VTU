<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\BillPay as BillPayModel;
use App\Models\BillService;
use App\Models\Deposit;
use App\Models\Fund;
use App\Models\Gateway;
use App\Traits\ApiValidation;
use App\Traits\BillPay;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Stevebauman\Purify\Facades\Purify;

class BillController extends Controller
{
	use ApiValidation, BillPay;

	public function billRequest()
	{
		try {
			$array = [];
			$data['deposits'] = tap(Deposit::own()->whereIn('status', [2, 3])->where('payment_method_id', '>', 999)
				->where('depositable_type', BillPayModel::class)
				->latest()->with('receiver', 'gateway')->paginate(config('basic.paginate')), function ($paginatedInstance) use ($array) {
				return $paginatedInstance->getCollection()->transform(function ($query) use ($array) {
					$array['date'] = $query->created_at ?? null;
					$array['trxNumber'] = $query->utr ?? null;
					$array['method'] = optional($query->gateway)->name ?? null;
					$array['amount'] = getAmount($query->amount, config('basic.fraction_number')) ?? null;
					$array['charge'] = getAmount($query->charge, config('basic.fraction_number')) ?? null;
					$array['currency'] = config('basic.base_currency');
					$array['currencySymbol'] = config('basic.currency_symbol');
					$array['payable'] = getAmount($query->payable_amount, config('basic.fraction_number'));
					$array['payableCurrency'] = $query->payment_method_currency;
					$array['rejectedCause'] = $query->feedback;
					if ($query->status == 2) {
						$array['status'] = 'Pending';
					} elseif ($query->status == 1) {
						$array['status'] = 'Approved';
					} elseif ($query->status == 3) {
						$array['status'] = 'Rejected';
					}
					return $array;
				});
			});

			return response()->json($this->withSuccess($data));
		} catch (\Exception $e) {
			return response()->json($e->getMessage());
		}
	}

	public function billPayList(Request $request)
	{
		$search = $request->all();
		$created_date = isset($search['created_at']) ? preg_match("/^[0-9]{2,4}-[0-9]{1,2}-[0-9]{1,2}$/", $search['created_at']) : 0;
		$array = [];
		try {
			$data['bills'] = tap(BillPayModel::own()->with('method')
				->when(isset($search['category']), function ($query) use ($search) {
					return $query->where('category_name', 'LIKE', "%{$search['category']}%");
				})
				->when(isset($search['type']), function ($query) use ($search) {
					return $query->where('type', 'LIKE', "%{$search['type']}%");
				})
				->when(isset($search['status']), function ($query) use ($search) {
					if ($search['status'] == 'pending') {
						return $query->where('status', 2);
					} elseif ($search['status'] == 'completed') {
						return $query->where('status', 3);
					} elseif ($search['status'] == 'return') {
						return $query->where('status', 4);
					}
				})
				->when($created_date == 1, function ($query) use ($search) {
					return $query->whereDate("created_at", $search['created_at']);
				})
				->whereIn('status', [2, 3, 4, 5])
				->latest()->paginate(config('basic.paginate')), function ($paginatedInstance) use ($array) {
				return $paginatedInstance->getCollection()->transform(function ($query) use ($array) {
					$array['id'] = $query->id;
					$array['category'] = str_replace('_', ' ', ucfirst($query->category_name));
					$array['type'] = $query->type ?? null;
					$array['amount'] = getAmount($query->amount, config('basic.fraction_number'));
					$array['currency'] = $query->currency;
					$array['charge'] = getAmount($query->charge, config('basic.fraction_number'));
					if ($query->status == 2) {
						$array['status'] = 'Pending';
					} elseif ($query->status == 3) {
						$array['status'] = 'Completed';
					} elseif ($query->status == 4) {
						$array['status'] = 'Return';
					} elseif ($query->status == 5) {
						$array['status'] = 'Processing';
					}
					$array['date'] = $query->created_at;
					return $array;
				});
			});
			return response()->json($this->withSuccess($data));
		} catch (\Exception $e) {
			return response()->json($this->withErrors($e->getMessage()));
		}
	}

	public function billPayDetails($id)
	{
		try {
			$billDetails = BillPayModel::own()->with(['gateway', 'method'])->find($id);
			if (!$billDetails) {
				return response()->json($this->withErrors('Record not found'));
			}

			$basic = basicControl();
			$data['date'] = $billDetails->created_at;
			$data['billMethod'] = optional($billDetails->method)->methodName ?? 'Unknown';
			$data['paymentGateway'] = $billDetails->payment_method_id == -1 ? 'Wallet' : optional($billDetails->gateway)->name;
			$data['transactionId'] = $billDetails->utr;
			if ($billDetails->status == 2) {
				$data['status'] = 'Pending';
			} elseif ($billDetails->status == 3) {
				$data['status'] = 'Completed';
			} elseif ($billDetails->status == 4) {
				$data['status'] = 'Return';
			} elseif ($billDetails->status == 5) {
				$data['status'] = 'Processing';
			}
			$data['exchangeRate'] = getAmount($billDetails->exchange_rate, config('basic.fraction_number'));
			$data['baseCurrency'] = $basic->base_currency;
			$data['baseCurrencySymbol'] = $basic->currency_symbol;
			$data['paymentCurrency'] = $billDetails->currency;
			$data['payInBase'] = getAmount($billDetails->pay_amount_in_base, config('basic.fraction_number'));

			$data['category'] = ucfirst(str_replace("_", " ", $billDetails->category_name));
			$data['type'] = $billDetails->type;
			foreach ($billDetails->customer as $key => $customer) {
				$data['customers'][] = [
					'fieldName' => snake2Title($key),
					'fieldValue' => $customer->{$key},
				];
			}
			$data['countryCode'] = $billDetails->country_name;
			$data['amount'] = getAmount($billDetails->amount, config('basic.fraction_number'));
			$data['charge'] = getAmount($billDetails->charge, config('basic.fraction_number'));
			$data['payableAmount'] = getAmount($billDetails->payable_amount, config('basic.fraction_number'));

			return response()->json($this->withSuccess($data));
		} catch (\Exception $e) {
			return response()->json($this->withErrors($e->getMessage()));
		}
	}

	public function billCategory()
	{
		try {
			$responses = config('billservices.' . $this->getActiveMethod()->code);
			$activeServices = [];

			foreach ($this->getActiveServices() as $service) {
				$activeServices[$service] = [
					'key' => $service,
					'name' => $responses[$service]['name'],
					'image' => asset(config('billservices.appImagePath') . $responses[$service]['image'])
				];
			}
			$data['activeServices'] = $activeServices;
			return response()->json($this->withSuccess($data));
		} catch (\Exception $e) {
			return response()->json($e->getMessage());
		}
	}

	public function payBillForm($code)
	{
		if (!BillService::where('status', 1)->where('service', $code)->exists()) {
			return response()->json($this->withErrors('Record not found'));
		}

		try {
			$data['services'] = BillService::where('bill_method_id', $this->getActiveMethod()->id)
				->where('status', 1)->where('service', $code)->get()->map(function ($query) {
					foreach (config('country') as $cou) {
						if ($cou['code'] == $query->country) {
							$query->countryName = $cou['name'];
							$query->phoneCode = $cou['phone_code'];
							break;
						}
					}
					return $query;
				});

			return response()->json($this->withSuccess($data));
		} catch (\Exception $e) {
			return response()->json($this->withErrors($e->getMessage()));
		}
	}

	public function payBillFormSubmit(Request $request)
	{
		$purifiedData = Purify::clean($request->all());
		$service = BillService::with(['method'])->find(@$purifiedData['service']);
		if (!$service) {
			return response()->json($this->withErrors('Record not found'));
		}
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
			return response()->json($this->withErrors(collect($validate->errors())->collapse()[0]));
		}

		try {
			$amount = ($service->amount > 0) ? $service->amount : $purifiedData['amount'];
			if ($service->method->code == 'reloadly' && $service->amount == -1) {
				$amount = getReloadlyAmount($service, $purifiedData['amount']);
			}

			if ($service->min_amount > $amount) {
				return response()->json($this->withErrors('Amount must be greater than ' . $service->min_amount . ' ' . $service->currency));
			}
			if ($service->max_amount > 0 && $service->max_amount < $amount) {
				return response()->json($this->withErrors('Amount must be smaller than ' . $service->max_amount . ' ' . $service->currency));
			}

			if (!$this->convertRate($service)) {
				return response()->json($this->withErrors('Something went wrong'));
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
			$billPay->user_id = auth()->id();
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

			$data['utr'] = $billPay->utr;
			return response()->json($this->withSuccess($data));
		} catch (\Exception $e) {
			return response()->json($this->withErrors($e->getMessage()));
		}
	}

	public function billPayPreview($utr)
	{
		try {
			if ($utr == 'add-fund') {
				$data['gateways'] = Gateway::select(['id', 'image', 'driver', 'name', 'min_amount', 'max_amount', 'percentage_charge', 'fixed_charge', 'convention_rate', 'sort_by', 'parameters'])
					->where('status', 1)->orderBy('sort_by', 'asc')->get()->map(function ($query) {
						$query->imagePath = getFile($query->driver, $query->image);
						return $query;
					});
				return response()->json($this->withSuccess($data));
			}

			$billPay = BillPayModel::with(['service', 'method'])->where('utr', $utr)->whereIn('status', [0, 1])->latest()->first();
			if (!$billPay) {
				return response()->json($this->withErrors('Record not found'));
			}
			$data['id'] = $billPay->id;
			$data['utr'] = $utr;
			$data['category'] = str_replace('_', ' ', ucfirst($billPay->category_name));
			$data['service'] = optional($billPay->service)->type;
			$data['countryCode'] = $billPay->country_name;
			$data['amount'] = getAmount($billPay->amount, config('basic.fraction_number'));
			$data['currency'] = $billPay->currency;
			$data['charge'] = getAmount($billPay->charge, config('basic.fraction_number'));
			$data['exchangeRate'] = getAmount($billPay->exchange_rate, config('basic.fraction_number'));

			$data['gateways'] = Gateway::select(['id', 'image', 'driver', 'name', 'min_amount', 'max_amount', 'percentage_charge', 'fixed_charge', 'convention_rate', 'sort_by', 'parameters'])
				->where('status', 1)->orderBy('sort_by', 'asc')->get()->map(function ($query) {
					$query->imagePath = getFile($query->driver, $query->image);
					return $query;
				});
			return response()->json($this->withSuccess($data));
		} catch (\Exception $e) {
			return response()->json($this->withErrors($e->getMessage()));
		}
	}

	public function fundRequest()
	{
		try {
			$array = [];
			$data['deposits'] = tap(Deposit::own()->whereIn('status', [2, 3])->where('payment_method_id', '>', 999)
				->where('depositable_type', Fund::class)
				->latest()->with('receiver', 'gateway')->paginate(config('basic.paginate')), function ($paginatedInstance) use ($array) {
				return $paginatedInstance->getCollection()->transform(function ($query) use ($array) {
					$array['date'] = $query->created_at ?? null;
					$array['trxNumber'] = $query->utr ?? null;
					$array['method'] = optional($query->gateway)->name ?? null;
					$array['amount'] = getAmount($query->amount, config('basic.fraction_number')) ?? null;
					$array['charge'] = getAmount($query->charge, config('basic.fraction_number')) ?? null;
					$array['currency'] = config('basic.base_currency');
					$array['currencySymbol'] = config('basic.currency_symbol');
					$array['payable'] = getAmount($query->payable_amount, config('basic.fraction_number'));
					$array['payableCurrency'] = $query->payment_method_currency;
					$info = null;
					if ($query->detail) {
						foreach ($query->detail as $key => $detail) {
							if ($detail->type == 'file') {
								$info[] = [
									'fieldName' => preg_replace('/([a-z])([A-Z])/', '$1 $2', $key),
									'fieldValue' => getFile($detail->field_name->driver, $detail->field_name->path)
								];
							} else {
								$info[] = [
									'fieldName' => preg_replace('/([a-z])([A-Z])/', '$1 $2', $key),
									'fieldValue' => $detail->field_name
								];
							}
						}
					}
					$array['information'] = $info;
					$array['rejectedCause'] = $query->feedback;
					if ($query->status == 2) {
						$array['status'] = 'Pending';
					} elseif ($query->status == 1) {
						$array['status'] = 'Approved';
					} elseif ($query->status == 3) {
						$array['status'] = 'Rejected';
					}
					return $array;
				});
			});

			return response()->json($this->withSuccess($data));
		} catch (\Exception $e) {
			return response()->json($e->getMessage());
		}
	}
}
