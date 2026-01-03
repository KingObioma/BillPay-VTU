<?php

namespace App\Services\Gateway\blockio;

use App\Models\Deposit;
use App\Models\Voucher;
use Facades\App\Services\BasicService;

class Payment
{
	public static function prepareData($deposit, $gateway)
	{
		$apiKey = $gateway->parameters->api_key ?? '';
		$apiPin = $gateway->parameters->api_pin ?? '';
		$version = 2;
		$blockIo = new BlockIo($apiKey, $apiPin, $version);

		if ($deposit->btc_amount == 0 || $deposit->btc_wallet == "") {
			$btcdata = $blockIo->get_current_price(array('price_base' => 'USD'));
			if ($btcdata->status != 'success') {
				$send['error'] = true;
				$send['message'] = 'Unable to Process';
			}
			$btcrate = $btcdata->data->prices[0]->price;
			$usd = round($deposit->payable_amount, 2);
			$btc = round($usd / $btcrate, 8);

			$address = $blockIo->get_new_address();

			if ($address->status == 'success') {
				$blockIoAdress = $address->data;
				$wallet = $blockIoAdress->address;
				$deposit['btc_wallet'] = $wallet;
				$deposit['btc_amount'] = $btc;
				$deposit->update();
			} else {
				$send['error'] = true;
				$send['message'] = 'Unable to Process';
			}
		}

		$send['amount'] = $deposit->btc_amount;
		$send['sendto'] = $deposit->btc_wallet;
		$send['img'] = BasicService::cryptoQR($deposit->btc_wallet, $deposit->btc_amount);
		$send['currency'] = $deposit->payment_method_currency ?? 'BTC';

		$send['view'] = 'user.payment.crypto';
		return json_encode($send);
	}

	public static function ipn($request, $gateway, $deposit = null, $trx = null, $type = null)
	{
		$apiKey = $gateway->parameters->api_key ?? '';
		$apiPin = $gateway->parameters->api_pin ?? '';
		$version = 2;
		$block_io = new BlockIo($apiKey, $apiPin, $version);
		$deposit = Deposit::with('gateway')
			->whereHas('gateway', function ($query) {
				$query->where('code', 'blockio');
			})
			->where('status', 0)
//			->where('btc_amount', '>', 0)
			->where('btc_wallet', '!=', '')
			->latest()
			->get();
		foreach ($deposit as $data) {
			$balance = $block_io->get_address_balance(array('addresses' => $data->btc_wallet));
			if (@$balance->data->available_balance >= $data->btc_amount && $data->status == '0') {
				BasicService::prepareOrderUpgradation($deposit);
			}
		}
	}
}
