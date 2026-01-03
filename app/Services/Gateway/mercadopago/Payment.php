<?php

namespace App\Services\Gateway\mercadopago;

use App\Models\Voucher;
use Facades\App\Services\BasicCurl;
use Facades\App\Services\BasicService;

class Payment
{
	const SANDBOX = true;

	public static function prepareData($deposit, $gateway)
	{
		$basic = basicControl();
		$url = "https://api.mercadopago.com/checkout/preferences?access_token=" . $gateway->parameters->access_token;
		$headers = [
			"Content-Type: application/json",
		];
		$postParam = [
			'items' => [
				[
					'id' => $deposit->utr,
					'title' => "Pay to $basic->site_title",
					'description' => "Payment To $basic->site_title Account",
					'quantity' => 1,
					'currency_id' => $deposit->payment_method_currency,
					'unit_price' => round($deposit->payable_amount, 2)
				]
			],
			'payer' => [
				'email' => optional($deposit->receiver)->email ?? optional($deposit->depositable)->email ?? $basic->sender_email,
			],
			'back_urls' => [
				'success' => route('success'),
				'pending' => '',
				'failure' => route('failed'),
			],
			'notification_url' => route('ipn', [$gateway->code, $deposit->utr]),
			'auto_return' => 'approved',
		];
		$response = BasicCurl::curlPostRequestWithHeaders($url, $headers, $postParam);
		$response = json_decode($response);
		$send['preference'] = $preference->id ?? '';
		$send['view'] = 'user.payment.mercado';
		if ($response->auto_return == 'approved') {
			if (self::SANDBOX) {
				$send['redirect'] = true;
				$send['redirect_url'] = $response->sandbox_init_point;
			} else {
				$send['redirect'] = true;
				$send['redirect_url'] = $response->init_point;
			}
		} else {
			$send['error'] = true;
			$send['message'] = 'Invalid Request';
		}
		return json_encode($send);
	}

	public static function ipn($request, $gateway, $deposit = null, $trx = null, $type = null)
	{
		$url = "https://api.mercadopago.com/v1/payments/" . $request['data']['id'] . "?access_token=" . $gateway->parameters->access_token;
		$response = BasicCurl::curlGetRequest($url);
		$paymentData = json_decode($response);

		if (isset($paymentData->status) && $paymentData->status == 'approved') {
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
