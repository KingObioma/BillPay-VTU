<?php

namespace App\Services\Bill\bloc;

use Facades\App\Services\BasicCurl;

class Card
{
	public static function fetchOperators($category, $billMethod)
	{
		$sec_key = optional($billMethod->parameters)->Secret_Key;
		$url = 'https://api.blochq.io/v1/bills/operators?bill=' . $category;

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');


		$headers = array();
		$headers[] = 'Accept: application/json';
		$headers[] = 'Authorization: Bearer ' . $sec_key;
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

		$response = curl_exec($ch);
		curl_close($ch);
		$result = json_decode($response);

		if (!$result) {
			return [
				'status' => 'error',
				'data' => 'Something went wrong please contact with provider'
			];
		}
		if ($result->success) {
			return [
				'status' => 'success',
				'data' => $result->data
			];

		} else {
			return [
				'status' => 'error',
				'data' => $result->message
			];
		}
	}

	public static function fetchServices($request, $billMethod)
	{
		$sec_key = optional($billMethod->parameters)->Secret_Key;
		$url = 'https://api.blochq.io/v1/bills/operators/' . $request['operator_id'] . '/products?bill=' . $request['api_service'];

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');


		$headers = array();
		$headers[] = 'Accept: application/json';
		$headers[] = 'Authorization: Bearer ' . $sec_key;
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

		$response = curl_exec($ch);
		curl_close($ch);
		$result = json_decode($response);

		if (!$result) {
			return [
				'status' => 'error',
				'data' => 'Something went wrong please contact with provider'
			];
		}
		if ($result->success) {
			return [
				'status' => 'success',
				'data' => $result->data
			];
		} else {
			return [
				'status' => 'error',
				'data' => $result->message
			];
		}
	}

	public static function payBill($billPay, $billMethod)
	{
		$billService = $billPay->service;
		$sec_key = optional($billMethod->parameters)->Secret_Key;
		$url = 'https://api.blochq.io/v1/bills/payment?bill=' . $billService->service;
		$deviceDetails = [];
		foreach ($billPay->customer as $key => $info) {
			$deviceDetails[$key] = $info->$key;
		}

		$postParams = [
			"amount" => (int)($billPay->amount * 100),
			"product_id" => $billService->code,
			"operator_id" => $billService->info->operator,
			"account_id" => "",
			"device_details" => $deviceDetails,
			"meta_data" => [

			]
		];

		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_POSTFIELDS => json_encode($postParams),
			CURLOPT_HTTPHEADER => array(
				'Authorization: Bearer ' . $sec_key,
				'Content-Type: application/json'
			),
		));

		$response = curl_exec($curl);

		curl_close($curl);
		$result = json_decode($response);

		if (!$result) {
			return [
				'status' => 'error',
				'data' => 'Something went wrong please contact with provider'
			];
		}
		if (isset($result->status) && isset($result->data) && $result->data->status == 'successful') {
			return [
				'status' => 'success',
				'data' => $result->data
			];
		} else {
			return [
				'status' => 'error',
				'data' => $result->message
			];
		}
	}

	public static function getBalance($billMethod, $currencyCode = null)
	{
		$object = new Card();
		$sec_key = optional($billMethod->parameters)->Secret_Key;
		$url = 'https://api.blochq.io/v1/accounts/organization/default';

		$curl = curl_init();

		curl_setopt_array($curl, [
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "GET",
			CURLOPT_HTTPHEADER => [
				"accept: application/json",
				"authorization: Bearer " . $sec_key
			],
		]);

		$response = curl_exec($curl);
		$err = curl_error($curl);
		curl_close($curl);

		$result = json_decode($response);
		if (isset($result) && isset($result->success) && isset($result->data)) {
			return [
				'status' => 'success',
				'balance' => $result->data[1]->balance / 100,
				'currencyCode' => "NGN",
			];
		}

		return [
			'status' => 'error'
		];
	}

}
