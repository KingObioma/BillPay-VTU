<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BillPay;
use App\Models\Transaction;
use App\Traits\Notify;
use Illuminate\Http\Request;

class BillPayController extends Controller
{
	use Notify;

	public function billPayList(Request $request, $type = 'all')
	{
		switch ($type) {
			case 'all':
				$arr = ['2', '3', '4', '5'];
				break;
			case 'pending':
				$arr = ['2', '5'];
				break;
			case 'completed':
				$arr = ['3'];
				break;
			case 'return':
				$arr = ['4'];
				break;
		}
		$search = $request->all();
		$created_date = isset($search['created_at']) ? preg_match("/^[0-9]{2,4}-[0-9]{1,2}-[0-9]{1,2}$/", $search['created_at']) : 0;

		$data['bills'] = BillPay::with(['user', 'method'])->when(isset($search['category']), function ($query) use ($search) {
			return $query->where('category_name', 'LIKE', "%{$search['category']}%");
		})
			->when(isset($search['username']), function ($query) use ($search) {
				$query->whereHas('user', function ($qq) use ($search) {
					$qq->where('username', 'LIKE', "%{$search['username']}%");
				});
			})
			->when(isset($search['searchType']), function ($query) use ($search) {
				return $query->where('type', 'LIKE', "%{$search['searchType']}%");
			})
			->when(isset($search['status']), function ($query) use ($search) {
				if ($search['status'] == 'generate') {
					return $query->where('status', 0);
				} elseif ($search['status'] == 'pending') {
					return $query->where('status', 1);
				} elseif ($search['status'] == 'processing') {
					return $query->where('status', 5);
				} elseif ($search['status'] == 'payment_completed') {
					return $query->where('status', 2);
				} elseif ($search['status'] == 'bill_completed') {
					return $query->where('status', 3);
				} elseif ($search['status'] == 'bill_return') {
					return $query->where('status', 4);
				}
			})
			->when($created_date == 1, function ($query) use ($search) {
				return $query->whereDate("created_at", $search['created_at']);
			})
			->whereIn('status', $arr)
			->latest()->paginate(config('basic.paginate'));
		return view('admin.bill_payment.index', $data);
	}

	public function billPayByUser($userId)
	{
		$data['bills'] = BillPay::with(['user', 'method'])->whereIn('status', ['2', '3', '4'])
			->where('user_id', $userId)
			->latest()->paginate(config('basic.paginate'));
		return view('admin.bill_payment.index', $data);
	}

	public function billPayView($utr)
	{
		$data['billDetails'] = BillPay::with(['user', 'method', 'service'])->where('utr', $utr)->firstOrFail();
		return view('admin.bill_payment.show', $data);
	}

	public function billPayReturn($utr)
	{
		$bill = BillPay::where('status', 2)->where('utr', $utr)->firstOrFail();
		try {
			updateWallet($bill->user_id, $bill->pay_amount_in_base, 1);
			$bill->status = 4;
			$bill->save();

			$transaction = new Transaction();
			$transaction->amount = $bill->pay_amount_in_base;
			$transaction->charge = 0;
			$transaction->currency_code = config('basic.base_currency');
			$transaction->remark = getAmount($bill->pay_amount_in_base, 3) . ' ' . config('basic.base_currency') . ' added your wallet from bill return';
			$bill->transactional()->save($transaction);
			$bill->save();

			$params = [
				'type' => $bill->type,
				'amount' => getAmount($bill->payable_amount, 2),
				'currency' => $bill->currency,
				'return_currency_amount' => getAmount($bill->pay_amount_in_base, 2),
				'return_currency' => config('basic.base_currency'),
				'transaction' => $bill->utr,
			];
			$action = [
				"link" => "#",
				"icon" => "fa fa-money-bill-alt text-white"
			];

			$this->sendMailSms($bill->user, 'BILL_PAYMENT_RETURN', $params);
			$this->userPushNotification($bill->user, 'BILL_PAYMENT_RETURN', $params, $action);
			$this->userFirebasePushNotification($bill->user, 'BILL_PAYMENT_RETURN', $params);

			return back()->with('success', 'Bill has been return '
				. getAmount($bill->pay_amount_in_base, 2)
				. ' ' . config('basic.base_currency') . ' amount has been added user wallet');

		} catch (\Exception $e) {
			return back()->with('alert', $e->getMessage());
		}
	}

	public function billPayConfirm($utr)
	{
		$bill = BillPay::with(['method'])->where('status', 2)->where('utr', $utr)->firstOrFail();

		try {
			$billPayMethod = $bill->method;
			$methodObj = 'App\\Services\\Bill\\' . $billPayMethod->code . '\\Card';
			if ($billPayMethod->code == 'reloadly' && $bill->category_name == 'AIRTIME') {
				$response = $methodObj::payAirtimeBill($bill, $billPayMethod);
			} else {
				$response = $methodObj::payBill($bill, $billPayMethod);
			}

			if ($response['status'] == 'success') {
				$bill->status = 3;
				$bill->save();
				$params = [
					'type' => $bill->type,
					'amount' => getAmount($bill->payable_amount, 2),
					'currency' => $bill->currency,
					'transaction' => $bill->utr,
				];
				$action = [
					"link" => "#",
					"icon" => "fa fa-money-bill-alt text-white"
				];

				$this->sendMailSms($bill->user, 'BILL_PAYMENT', $params);
				$this->userPushNotification($bill->user, 'BILL_PAYMENT', $params, $action);
				$this->userFirebasePushNotification($bill->user, 'BILL_PAYMENT', $params);
				$this->adminPushNotification('BILL_PAYMENT', $params, $action);
				return back()->with('success', 'Bill has been paid successfully');
			} elseif ($response['status'] == 'processing') {
				$bill->status = 5;
				$bill->reference_id = $response['data'];
				$bill->save();
				return back()->with('success', 'Bill has been processing');
			} else {
				$bill->last_api_error = $response['data'];
				$bill->save();
				return back()->with('alert', $response['data']);
			}

		} catch (\Exception $e) {
			return back()->with('alert', $e->getMessage());
		}
	}
}
