<?php

namespace App\Services\Gateway\coingate;

use CoinGate\CoinGate;
use CoinGate\Merchant\Order;
use Facades\App\Services\BasicCurl;
use Facades\App\Services\BasicService;

require_once('coingate-php/init.php');

class Payment
{
	public static function prepareData($deposit, $gateway)
	{
		try {
			$basic = basicControl();
			$env = $gateway->environment == 'live' ? 'live' : 'sandbox';
			CoinGate::config(array(
				'environment' => $env,
				'auth_token' => $gateway->parameters->api_key
			));

			$postParams = array(
				'order_id' => $deposit->utr,
				'price_amount' => round($deposit->payable_amount, 2),
				'price_currency' => $deposit->payment_method_currency,
				'receive_currency' => $deposit->payment_method_currency,
				'callback_url' => route('ipn', [$gateway->code, $deposit->utr]),
				'cancel_url' => twoStepPrevious($deposit),
				'success_url' => route('success'),
				'title' => "Pay To $basic->site_title",
				'token' => $deposit->utr
			);

			$order = Order::create($postParams);
			if ($order) {
				$send['redirect'] = true;
				$send['redirect_url'] = $order->payment_url;
			} else {
				$send['error'] = true;
				$send['message'] = 'Unexpected Error! Please Try Again';
			}
			return json_encode($send);
		} catch (\Exception $e) {
			$send['error'] = true;
			$send['message'] = $e->getMessage();
			return json_encode($send);
		}
	}

	public static function ipn($request, $gateway, $deposit = null, $trx = null, $type = null)
	{
		$ip = $request->ip();
		$url = 'https://api.coingate.com/v2/ips-v4';
		$response = BasicCurl::curlGetRequest($url);
		if (strpos($response, $ip) !== false) {
			if ($request->status == 'paid' && $request->price_amount == round($deposit->payable_amount, 2) && $deposit->status == 0) {
				BasicService::prepareOrderUpgradation($deposit);
			}
		}
	}
}
