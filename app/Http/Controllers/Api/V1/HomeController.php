<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\BillPay;
use App\Models\Fund;
use App\Models\Language;
use App\Models\Transaction;
use App\Traits\ApiValidation;
use App\Traits\Notify;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
	use ApiValidation, Notify;

	public function transaction()
	{
		$basic = basicControl();
		$user = Auth::user();
		try {
			$array = [];
			$transactions = tap(Transaction::with(['transactional' => function (MorphTo $morphTo) {
				$morphTo->morphWith([
					Fund::class => ['sender', 'receiver'],
					BillPay::class => ['user']
				]);
			}])
				->whereHasMorph('transactional',
					[
						Fund::class,
						BillPay::class,
					], function ($query, $type) use ($user) {
						if ($type === Fund::class || BillPay::class) {
							$query->where('user_id', $user->id);
						}
					})
				->latest()
				->paginate(config('basic.paginate')), function ($paginatedInstance) use ($array, $basic) {
				return $paginatedInstance->getCollection()->transform(function ($query) use ($array, $basic) {
					$array['transactionId'] = optional($query->transactional)->utr ?? null;
					$array['amount'] = getAmount($query->amount, 2);
					$array['currency'] = $basic->base_currency ?? null;
					$array['symbol'] = $basic->currency_symbol ?? null;
					$array['type'] = str_replace('App\Models\\', '', $query->transactional_type) ?? null;
					$array['remarks'] = $query->remark ?? null;
					$array['status'] = optional($query->transactional)->status ? 'Success' : 'Pending';
					$array['time'] = $query->created_at ?? null;
					return $array;
				});
			});

			if ($transactions) {
				return response()->json($this->withSuccess($transactions));
			} else {
				return response()->json($this->withErrors('No data found'));
			}
		} catch (\Exception $e) {
			return response()->json($this->withErrors($e));
		}
	}

	public function transactionSearch(Request $request)
	{
		$basic = basicControl();
		try {
			$filterData = $this->_filter($request);
			$search = $filterData['search'];
			$user = $filterData['user'];
			$array = [];
			$data['transactions'] = tap($filterData['transactions']
				->latest()
				->paginate(config('basic.paginate')), function ($paginatedInstance) use ($array, $basic) {
				return $paginatedInstance->getCollection()->transform(function ($query) use ($array, $basic) {
					$array['transactionId'] = optional($query->transactional)->utr ?? null;
					$array['amount'] = getAmount($query->amount, 2);
					$array['currency'] = $basic->base_currency ?? null;
					$array['symbol'] = $basic->currency_symbol ?? null;
					$array['type'] = str_replace('App\Models\\', '', $query->transactional_type) ?? null;
					$array['remarks'] = $query->remark ?? null;
					$array['status'] = optional($query->transactional)->status ? 'Success' : 'Pending';
					$array['time'] = $query->created_at ?? null;
					return $array;
				});
			});
			return response()->json($this->withSuccess($data));
		} catch (\Exception $exception) {
			return response()->json($this->withErrors($exception->getMessage()));
		}
	}

	public function _filter($request)
	{
		$user = Auth::user();
		$search = $request->all();
		$created_date = isset($search['created_at']) ? preg_match("/^[0-9]{2,4}-[0-9]{1,2}-[0-9]{1,2}$/", $search['created_at']) : 0;

		if (isset($search['type'])) {
			if ($search['type'] == 'BillPay') {
				$morphWith = [BillPay::class => ['user']];
				$whereHasMorph = [BillPay::class];
			}
		} else {
			$morphWith = [
				BillPay::class => ['user'],
			];
			$whereHasMorph = [
				BillPay::class,
			];
		}

		$transactions = Transaction::with(['transactional' => function (MorphTo $morphTo) use ($morphWith, $whereHasMorph) {
			$morphTo->morphWith($morphWith);
		}])
			->whereHasMorph('transactional', $whereHasMorph, function ($query, $type) use ($search, $created_date, $user) {
				if ($type === BillPay::class) {
					$query->where('user_id', $user->id);
				}

				$query->when(isset($search['utr']), function ($query) use ($search) {
					return $query->where('utr', 'LIKE', $search['utr']);
				})
					->when(isset($search['min']), function ($query) use ($search) {
						return $query->where('amount', '>=', $search['min']);
					})
					->when(isset($search['max']), function ($query) use ($search) {
						return $query->where('amount', '<=', $search['max']);
					})
					->when($created_date == 1, function ($query) use ($search) {
						return $query->whereDate("created_at", $search['created_at']);
					});
			}
			);

		$data = [
			'user' => $user,
			'transactions' => $transactions,
			'search' => $search,
		];
		return $data;
	}

	public function appConfig()
	{
		try {
			$basic = basicControl();
			$data['siteTitle'] = $basic->site_title;
			$data['primaryColor'] = $basic->primaryColor;
			$data['btnColor'] = $basic->btnColor;
			$data['btnHoverColor'] = $basic->btnHoverColor;
			$data['version'] = config('basic.version');
			$data['paymentSuccessUrl'] = route('success');
			$data['paymentFailedUrl'] = route('failed');

			$data['appColor'] = config('basic.appColor');
			$data['appVersion'] = config('basic.appVersion');
			$data['appBuild'] = config('basic.appBuild');
			$data['isMajor'] = config('basic.isMajor');
			return response()->json($this->withSuccess($data));
		} catch (\Exception $exception) {
			return response()->json($this->withErrors($exception->getMessage()));
		}
	}

	public function pusherConfig()
	{
		try {
			$data['apiKey'] = env('PUSHER_APP_KEY');
			$data['cluster'] = env('PUSHER_APP_CLUSTER');
			$data['channel'] = 'user-notification.' . Auth::id();
			$data['event'] = 'UserNotification';

			return response()->json($this->withSuccess($data));
		} catch (\Exception $e) {
			return response()->json($this->withErrors($e->getMessage()));
		}
	}

	public function language($id = null)
	{
		try {
			if (!$id) {
				$data['languages'] = Language::select(['id', 'name', 'short_name'])->where('is_active', 1)->get();
				return response()->json($this->withSuccess($data));
			}
			$lang = Language::where('is_active', 1)->find($id);
			if (!$lang) {
				return response()->json($this->withErrors('Record not found'));
			}

			$json = file_get_contents(resource_path('lang/') . $lang->short_name . '.json');
			if (empty($json)) {
				return response()->json($this->withErrors('File Not Found.'));
			}

			$json = json_decode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
			return response()->json($this->withSuccess($json));
		} catch (\Exception $e) {
			return response()->json($this->withErrors($e->getMessage()));
		}
	}

	public function dashboard()
	{
		$user = Auth::user();

		$today = Carbon::now();
		$sixMonthsAgo = $today->copy()->subMonths(6);
		$chartOrders = BillPay::selectRaw("YEAR(created_at) as year, MONTH(created_at) as month")
			->where('status', '!=', 0)
			->where(function ($query) use ($sixMonthsAgo) {
				$query->whereYear('created_at', '>=', $sixMonthsAgo->year)
					->whereMonth('created_at', '>=', $sixMonthsAgo->month)
					->orWhere(function ($query) {
						$query->whereYear('created_at', '=', now()->year)
							->whereMonth('created_at', '=', now()->month);
					});
			})
			->where('user_id', $user->id)
			->groupBy('year', 'month')
			->selectRaw("SUM(CASE WHEN status = 2 THEN 1 ELSE 0 END) AS pendingBills")
			->selectRaw("SUM(CASE WHEN status = 4 THEN 1 ELSE 0 END) AS returnBills")
			->selectRaw("SUM(CASE WHEN status = 3 THEN 1 ELSE 0 END) AS completeBills")
			->get();

		//return $chartOrders;

		// Generate month labels
		$monthLabels = collect(range(0, 5))
			->map(function ($offset) use ($today) {
				return $today->copy()->subMonths($offset)->format('F');
			})
			->reverse()
			->values()
			->toArray();

		$yearLabels = collect(range(0, 5))
			->map(function ($offset) use ($today) {
				return $today->copy()->subMonths($offset)->format('n');
			})
			->reverse()
			->values()
			->toArray();


		$data['monthLabels'] = $monthLabels;
		$data['yearLabels'] = $yearLabels;

		$yearPendingBills = [];
		$yearReturnBills = [];
		$yearCompleteBills = [];


		foreach ($data['yearLabels'] as $yearLabel) {
			$currentYearPendingBills = 0;
			$currentYearReturnBills = 0;
			$currentYearCompleteBills = 0;

			if (isset($chartOrders)) {
				foreach ($chartOrders as $key => $itemOrder) {

					if ($itemOrder->month == $yearLabel) {
						$currentYearPendingBills += $itemOrder->pendingBills;
						$currentYearReturnBills += $itemOrder->returnBills;
						$currentYearCompleteBills += $itemOrder->completeBills;
					}

				}
			}
			$yearPendingBills[] = round($currentYearPendingBills, 2);
			$yearReturnBills[] = round($currentYearReturnBills, 2);
			$yearCompleteBills[] = round($currentYearCompleteBills, 2);
		}

		$data['yearPendingBills'] = $yearPendingBills;
		$data['yearReturnBills'] = $yearReturnBills;
		$data['yearCompleteBills'] = $yearCompleteBills;
		$value['chart'] = $data;

		$bills = BillPay::where('user_id', $user->id)->selectRaw('COUNT(CASE WHEN status = 2  THEN id END) AS pendingBills')
			->selectRaw('COUNT(CASE WHEN status = 3  THEN id END) AS completeBills')
			->selectRaw('COUNT(CASE WHEN status = 4  THEN id END) AS returnBills')
			->selectRaw('SUM(CASE WHEN payment_method_id = -1 AND status != 0 THEN pay_amount_in_base END) AS totalWalletPays')
			->get()->toArray();
		$value['billRecord'] = collect($bills)->collapse();
		$value['billRecord']['walletBalance'] = number_format($user->balance, 2);

		return response()->json($this->withSuccess($value));
	}
	
	public function userData(Request $request)
	{
		$user = auth()->user();
		$data['status'] = $user->status;
		$data['email_verification'] = $user->email_verification;
		$data['sms_verification'] = $user->sms_verification;
		$data['two_fa'] = $user->two_fa;
		$data['two_fa_verify'] = $user->two_fa_verify;
		$data['two_fa_verify_msg'] = $user->two_fa_verify ? 'Verified' : 'Unverified';
		$data['kyc_verified'] = $user->kyc_verified;
		if ($user->kyc_verified == 0) {
			$data['kyc_verified_msg'] = 'Unverified';
		} elseif ($user->kyc_verified == 1) {
			$data['kyc_verified_msg'] = 'Pending';
		} elseif ($user->kyc_verified == 2) {
			$data['kyc_verified_msg'] = 'Verified';
		} elseif ($user->kyc_verified == 3) {
			$data['kyc_verified_msg'] = 'Rejected';
		}

		return response()->json($this->withSuccess($data));
	}
}
