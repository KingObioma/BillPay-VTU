<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Deposit;
use App\Models\Fund;
use App\Models\Transaction;
use App\Traits\Notify;
use Facades\App\Services\BasicService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Stevebauman\Purify\Facades\Purify;

class PaymentLogController extends Controller
{
	use Notify;

	public function index()
	{
		$page_title = "Payment Logs";
		$funds = Deposit::where('status', '!=', 0)->orderBy('id', 'DESC')->with('receiver', 'gateway')->paginate(config('basic.paginate'));
		return view('admin.payment.logs', compact('funds', 'page_title'));
	}

	public function pending()
	{
		$page_title = "Payment Pending";
		$funds = Deposit::where('status', 2)->where('payment_method_id', '>', 999)->orderBy('id', 'DESC')->with('receiver', 'gateway')->paginate(config('basic.paginate'));
		return view('admin.payment.logs', compact('funds', 'page_title'));
	}


	public function search(Request $request)
	{
		$search = $request->all();
		$dateSearch = $request->date_time;
		$date = preg_match("/^[0-9]{2,4}\-[0-9]{1,2}\-[0-9]{1,2}$/", $dateSearch);

		$funds = Deposit::when(isset($search['name']), function ($query) use ($search) {
			return $query->where('utr', 'LIKE', $search['name'])
				->orWhereHas('receiver', function ($q) use ($search) {
					$q->where('email', 'LIKE', "%{$search['name']}%")
						->orWhere('username', 'LIKE', "%{$search['name']}%");
				});
		})
			->when($date == 1, function ($query) use ($dateSearch) {
				return $query->whereDate("created_at", $dateSearch);
			})
			->when($search['status'] != -1, function ($query) use ($search) {
				return $query->where('status', $search['status']);
			})
			->where('status', '!=', 0)
			->with('receiver', 'gateway')
			->paginate(config('basic.paginate'));
		$funds->appends($search);
		$page_title = "Search Payment Logs";
		return view('admin.payment.logs', compact('funds', 'page_title'));
	}


	public function action(Request $request, $id)
	{
		$this->validate($request, [
			'id' => 'required',
			'status' => ['required', Rule::in(['1', '3'])],
		]);
		$data = Deposit::where('id', $request->id)->whereIn('status', [2])->with('receiver', 'gateway')->firstOrFail();

		try {
			$user = $data->receiver;
			$req = Purify::clean($request->all());

			if ($request->status == '1') {
				$data->feedback = @$req['feedback'];
				$data->update();

				BasicService::prepareOrderUpgradation($data);
				session()->flash('success', 'Approve Successfully');
				return back();

			} elseif ($request->status == '3') {
				$data->status = 3;
				$data->feedback = $request->feedback;
				$data->update();

				$this->sendMailSms($user, $type = 'PAYMENT_REJECTED', [
					'amount' => getAmount($data->amount),
					'currency' => config('basic.base_currency'),
					'method' => optional($data->gateway)->name,
					'transaction' => $data->transaction,
					'feedback' => $data->feedback
				]);

				$msg = [
					'amount' => getAmount($data->amount),
					'currency' => config('basic.base_currency'),
					'feedback' => $data->feedback,
				];
				$action = [
					"link" => '#',
					"icon" => "fas fa-money-bill-alt text-white"
				];
				$this->userPushNotification($user, 'PAYMENT_REJECTED', $msg, $action);

				session()->flash('success', 'Reject Successfully');
				return back();
			}
		} catch (\Exception $e) {
			return back()->with('alert', $e->getMessage());
		}
	}
}
