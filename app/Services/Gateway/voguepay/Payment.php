<?php

namespace App\Services\Gateway\voguepay;

use App\Models\Voucher;
use Facades\App\Services\BasicCurl;
use Facades\App\Services\BasicService;

class Payment
{
	public static function prepareData($deposit, $gateway)
	{
		$basic = basicControl();
		$send['v_merchant_id'] = $gateway->parameters->merchant_id ?? '';
		$send['notify_url'] = route('ipn', [$gateway->code, $deposit->utr]);
		$send['cur'] = $deposit->payment_method_currency;
		$send['merchant_ref'] = $deposit->utr;
		$send['memo'] = "Pay to {$basic->site_title}";
		$send['custom'] = $deposit->utr;
		$send['customer_name'] = optional($deposit->receiver)->name ?? $basic->site_title;
		$send['customer_address'] = optional($deposit->receiver)->profile->address ?? '';
		$send['customer_email'] = optional($deposit->receiver)->email ?? optional($deposit->depositable)->email ?? basicControl()->sender_email;
		$send['Buy'] = round($deposit->payable_amount, 2);
		$send['view'] = 'user.payment.voguepay';
		return json_encode($send);
	}

	public static function ipn($request, $gateway, $deposit = null, $trx = null, $type = null)
	{
		$trx = $request->transaction_id;
		$url = "https://voguepay.com/?v_transaction_id={$type}&type=json";
		$response = BasicCurl::curlGetRequest($url);
		$response = json_decode($response);
		$merchantId = $gateway->parameters->merchant_id ?? '';
		if ($response->status == "Approved" && $response->merchant_id == $merchantId && $response->total == round($deposit->payable_amount, 2) && $response->cur_iso == $deposit->payment_method_currency) {
			BasicService::prepareOrderUpgradation($deposit);
			$data['status'] = 'success';
			$data['msg'] = 'Transaction was successful.';
			$data['redirect'] = route('success');
		} else {
			$data['status'] = 'error';
			$data['msg'] = 'unexpected error!';
			$data['redirect'] = route('failed');
		}

		return $data;
	}
}
