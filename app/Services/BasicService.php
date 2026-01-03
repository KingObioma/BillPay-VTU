<?php

namespace App\Services;

use App\Models\BillPay;
use App\Models\Fund;
use App\Models\Transaction;
use App\Models\User;
use App\Traits\Notify;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Image;

class BasicService
{
	use Notify;

	public function validateImage(object $getImage, string $path)
	{
		if ($getImage->getClientOriginalExtension() == 'jpg' or $getImage->getClientOriginalName() == 'jpeg' or $getImage->getClientOriginalName() == 'png') {
			$image = uniqid() . '.' . $getImage->getClientOriginalExtension();
		} else {
			$image = uniqid() . '.jpg';
		}
		Image::make($getImage->getRealPath())->resize(300, 250)->save($path . $image);
		return $image;
	}

	public function validateDate(string $date)
	{
		if (preg_match("/^[0-9]{1,2}\/[0-9]{1,2}\/[0-9]{2,4}$/", $date)) {
			return true;
		} else {
			return false;
		}
	}

	public function cryptoQR($wallet, $amount, $crypto = null)
	{

		$varb = $wallet . "?amount=" . $amount;
		return "https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=$varb&choe=UTF-8";
	}

	public function validateKeyword(string $search, string $keyword)
	{
		return preg_match('~' . preg_quote($search, '~') . '~i', $keyword);
	}

	public function prepareOrderUpgradation($deposit)
	{
		$basicControl = basicControl();
		try {
			$deposit->status = 1;
			if ($deposit->depositable_type == BillPay::class) {

				$billPay = $deposit->depositable;
				$billPayMethod = $billPay->method;
				$billPay->status = 2;
				$billPay->save();

				$fund = new Fund();
				$fund->user_id = $deposit->user_id;
				$fund->percentage = $deposit->percentage;
				$fund->charge_percentage = $deposit->charge_percentage;
				$fund->charge_fixed = $deposit->charge_fixed;
				$fund->charge = $deposit->charge;
				$fund->amount = $deposit->amount;
				$fund->email = $deposit->email;
				$fund->status = 1;
				$fund->utr = $deposit->utr;
				$fund->save();

				$transaction = new Transaction();
				$transaction->amount = $fund->amount;
				$transaction->charge = $fund->charge;
				$transaction->remark = getAmount($billPay->pay_amount_in_base, 3) . ' ' . config('basic.base_currency') . ' payment for ' . $billPay->type;
				$billPay->transactional()->save($transaction);
				$deposit->save();

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
			} elseif ($deposit->depositable_type == Fund::class) {
				$user = $deposit->user;
				$user->balance += $deposit->amount;
				$user->save();

				$fund = new Fund();
				$fund->user_id = $deposit->user_id;
				$fund->percentage = $deposit->percentage;
				$fund->charge_percentage = $deposit->charge_percentage;
				$fund->charge_fixed = $deposit->charge_fixed;
				$fund->charge = $deposit->charge;
				$fund->amount = $deposit->amount;
				$fund->email = $deposit->email;
				$fund->status = 1;
				$fund->utr = $deposit->utr;
				$fund->save();

				$transaction = new Transaction();
				$transaction->amount = $fund->amount;
				$transaction->charge = $fund->charge;
				$transaction->remark = getAmount($deposit->amount, 2) . ' ' . config('basic.base_currency') . ' add wallet';
				$fund->transactional()->save($transaction);
				$deposit->save();
			}
			$deposit->save();
			return true;
		} catch (\Exception $e) {
			return true;
		}
	}
}
