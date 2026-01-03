<?php

namespace App\Services\Bill\tpaga;

use Facades\App\Services\BasicCurl;

class Card
{
	protected $baseUrl;

	public function __construct($mood = 'live')
	{
		if ($mood == 'test') {
			$this->baseUrl = 'https://staging.apiv2.tpaga.co/api/gateway_bill_payment/v1/';
		} else {
			$this->baseUrl = 'https://production.apiv2.tpaga.co/api/gateway_bill_payment/v1/';
		}
	}

	public static function fetchServices($request, $billMethod)
	{
		$object = new Card();
		$apiKey = base64_encode(optional($billMethod->parameters)->Api_Key);
		$url = $object->baseUrl . 'utility_providers';

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');


		$headers = array();
		$headers[] = 'Authorization: Basic ' . $apiKey;
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

		$response = curl_exec($ch);
		curl_close($ch);
		$result = json_decode($response);

		if (isset($result->service_providers) && isset($result->categories)) {
			foreach ($result->categories as $cat) {
				if ($cat->id == $request['api_service']) {
					foreach ($result->service_providers as $provider) {
						if (in_array($provider->id, $cat->service_providers)) {
							$myArray[] = $provider;
						}
					}
					break;
				}
			}
			if (count($myArray) > 0) {
				return [
					'status' => 'success',
					'data' => $myArray
				];
			}
		}
		return [
			'status' => 'error',
			'data' => 'Something went wrong please contact with provider'
		];
	}

	public static function payBill($billPay, $billMethod)
	{
		$object = new Card();
		$billService = $billPay->service;
		$apiKey = base64_encode(optional($billMethod->parameters)->Api_Key);
		$url = $object->baseUrl . 'pay_bill';

		$postParams = [
			"idempotency_token" => strRandom(10),
			"amount" => (int)$billPay->amount,
			"utility_provider" => $billService->code,
			"short_bill_reference" => "111100005555",
			"payment_token" => $billPay->customer->Payment_Token->Payment_Token ?? null,
			"payment_origin" => "API Call By Client"
		];

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postParams));

		$headers = array();
		$headers[] = 'Authorization: Basic ' . $apiKey;
		$headers[] = 'Content-Type: application/json';
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

		$response = curl_exec($ch);
		curl_close($ch);
		$result = json_decode($response);

		if (isset($result->status) && ($result->status == 'IN_PROGRESS' || $result->status == 'AUTHORIZED')) {
			return [
				'status' => 'success',
				'data' => $result
			];
		}

		return [
			'status' => 'error',
			'data' => $result->error_message ?? 'Unknown error Found',
		];
	}

	public static function getBalance($billMethod, $currencyCode = null)
	{
		$object = new Card();
		$apiKey = base64_encode(optional($billMethod->parameters)->Api_Key);
		$url = $object->baseUrl . 'merchant/balance';

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');


		$headers = array();
		$headers[] = 'Authorization: Basic ' . $apiKey;
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

		$response = curl_exec($ch);

		curl_close($ch);
		$result = json_decode($response);

		if (isset($result) && isset($result->balance)) {
			return [
				'status' => 'success',
				'balance' => $result->balance,
				'currencyCode' => 'COP',
			];
		}

		return [
			'status' => 'error'
		];
	}

}
