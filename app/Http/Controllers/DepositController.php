<?php

namespace App\Http\Controllers;

use App\Models\Deposit;
use App\Models\Gateway;
use Illuminate\Http\Request;

class DepositController extends Controller
{
	public function __construct()
	{
		$this->middleware(['auth']);
		$this->middleware(function ($request, $next) {
			$this->user = auth()->user();
			return $next($request);
		});
		$this->theme = template();
	}

	public function checkAmount(Request $request)
	{
		if ($request->ajax()) {
			$amount = $request->amount;
			$methodId = $request->methodId;
			$data = $this->checkAmountValidate($amount, $methodId);
			return response()->json($data);
		}
	}

	public function checkAmountValidate($amount, $methodId)
	{
		$limit = config('basic.fraction_number');
		$gateway = Gateway::where('status', 1)->find($methodId);
		if (!$gateway) {
			$status = false;
			$message = "Gateway currently disable or something went wrong. Please try later";
		}

		$balance = 0;

		$status = false;
		$amount = getAmount($amount, $limit);
		$charge = 0;
		$min_limit = 0;
		$max_limit = 0;
		$fixed_charge = 0;
		$percentage = 0;
		$percentage_charge = 0;

		$percentage = getAmount($gateway->percentage_charge, $limit);
		$percentage_charge = getAmount(($amount * $percentage) / 100, $limit);
		$fixed_charge = getAmount($gateway->fixed_charge, $limit);
		$min_limit = getAmount($gateway->min_amount, $limit);
		$max_limit = getAmount($gateway->max_amount, $limit);
		$charge = getAmount($percentage_charge + $fixed_charge, $limit);

		$payable_amount = getAmount($amount + $charge, $limit);

		$new_balance = getAmount($balance + $amount, $limit);

		if ($amount < $min_limit || $amount > $max_limit) {
			$message = "minimum payment $min_limit and maximum payment limit $max_limit";
		} else {
			$status = true;
			$message = "Updated balance : $new_balance";
		}

		$data['status'] = $status;
		$data['message'] = $message;
		$data['fixed_charge'] = $fixed_charge;
		$data['percentage'] = $percentage;
		$data['percentage_charge'] = $percentage_charge;
		$data['min_limit'] = $min_limit;
		$data['max_limit'] = $max_limit;
		$data['balance'] = $balance;
		$data['payable_amount'] = $payable_amount;
		$data['new_balance'] = $new_balance;
		$data['charge'] = $charge;
		$data['amount'] = $amount;
		$data['convention_rate'] = $gateway->convention_rate;
		$data['currency_limit'] = $limit;

		return $data;
	}

	public function confirmDeposit(Request $request, $utr)
	{
		$deposit = Deposit::with('receiver')->where('utr', $utr)->first();

		if (!$deposit || $deposit->status) {
			return back()->with('success', 'Transaction already complete');
		}

		if ($request->isMethod('get')) {
			return view($this->theme . 'user.deposit.confirm', compact(['utr', 'deposit']));
		} elseif ($request->isMethod('post')) {

			$checkAmountValidate = $this->checkAmountValidate($deposit->amount, $deposit->payment_method_id);
			if (!$checkAmountValidate['status']) {
				return back()->withInput()->with('alert', $checkAmountValidate['message']);
			}

			return redirect(route('payment.process', $utr));
		}
	}
}
