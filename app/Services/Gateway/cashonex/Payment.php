<?php

namespace App\Services\Gateway\cashonex;

use Facades\App\Services\BasicCurl;
use Facades\App\Services\BasicService;


class Payment
{
	public static function prepareData($deposit, $gateway)
	{
		$basic = (object)config('basic');
		$val['name'] = optional($deposit->receiver)->username ?? $basic->site_title;
		$val['amount'] = round($deposit->payable_amount, 2);
		$val['currency'] = $deposit->payment_method_currency;
		$send['val'] = $val;
		$send['view'] = 'user.payment.cashonex';
		$send['method'] = 'post';
		$send['url'] = route('ipn', [$gateway->code, $deposit->utr]);
		return json_encode($send);
	}

	public static function ipn($request, $gateway, $deposit = null, $trx = null, $type = null)
	{
		$idempotency_key = $gateway->parameters->idempotency_key;
		$salt = $gateway->parameters->salt;
		$request->validate([
			'name' => 'required',
			'cardNumber' => 'required',
			'cardExpiry' => 'required',
			'cardCVC' => 'required'
		], [
			'cardCVC.required' => "The card CVC field is required."
		]);


		$card_number = $request->cardNumber;
		$exp = $request->cardExpiry;
		$cvc = $request->cardCVC;

		$exp = $pieces = explode("/", $_POST['cardExpiry']);
		$expiry_year = trim($exp[1]);
		$expiry_month = trim($exp[0]);
		$amount = round($deposit->payable_amount, 2);
		$headers = [
			'Content-Type: application/json',
			"Idempotency-Key: $idempotency_key",
		];

		$postParam = [
			"salt" => $salt,
			"first_name" => optional($deposit->receiver)->username,
			"last_name" => optional($deposit->receiver)->username,
			"email" => optional($deposit->receiver)->email ?? 'email@gmail.com',
			"phone" => optional($deposit->receiver->profile)->phone ?? '9999999999',
			"address" => optional($deposit->receiver->profile)->address ?? '123, address',
			"city" => optional($deposit->receiver->profile)->city ?? 'City',
			"state" => optional($deposit->receiver->profile)->city ?? 'State',
			"country" => optional($deposit->receiver->profile)->country ?? 'GB',
			"zip_code" => optional($deposit->receiver->profile)->zip_code ?? '90210',
			"amount" => $amount,
			"currency" => $deposit->payment_method_currency,
			"pay_by" => "VISA",
			"card_name" => $request->name,
			"card_number" => $card_number,
			"cvv_code" => $cvc,
			"expiry_year" => $expiry_year,
			"expiry_month" => $expiry_month,
			"orderid" => $deposit->utr,
			"clientip" => request()->ip(),
			"redirect_url" => route('success'),
			"webhook_url" => route('ipn', [$gateway->code, $deposit->utr])
		];

		$url = "https://cashonex.com/api/rest/payment";
		$result = BasicCurl::curlPostRequestWithHeadersJson($url, $headers, $postParam);
		$response = json_decode($result);
		if (isset($response->success) && $response->success == true) {
			$deposit->btc_wallet = @$response->data->paymentId;
			$deposit->update();
			BasicService::prepareOrderUpgradation($deposit);
			$data['status'] = 'success';
			$data['msg'] = ' Payment Proceed.';
			$data['redirect'] = $response->data->redirectUrl;
		} else {
			$data['status'] = 'error';
			$data['msg'] = 'Unsuccessful transaction.';
			$data['redirect'] = route('failed');
		}

		return $data;

	}
}
