<?php

namespace App\Services\Gateway\payumoney;

use App\Models\Voucher;
use Facades\App\Services\BasicService;

class Payment
{
	public static function prepareData($deposit, $gateway)
	{
		$basic = basicControl();
		$hashSequence = "key|txnid|amount|productinfo|firstname|email|udf1|udf2|udf3|udf4|udf5|udf6|udf7|udf8|udf9|udf10";
		$hashVarsSeq = explode('|', $hashSequence);
		$hash_string = '';
		$send['val'] = [
			'key' => $gateway->parameters->merchant_key ?? '',
			'txnid' => $deposit->utr,
			'amount' => round($deposit->payable_amount, 2),
			'firstname' => optional($deposit->receiver)->name ?? $basic->site_title,
			'email' => optional($deposit->receiver)->email ?? optional($deposit->depositable)->email ?? $basic->sender_email,
			'productinfo' => "Pay to $basic->site_title",
			'surl' => route('ipn', [$gateway->code, $deposit->utr]),
			'furl' => route('failed'),
			'service_provider' => $basic->site_title ?? $basic->site_title,
		];
		foreach ($hashVarsSeq as $hash_var) {
			$hash_string .= $send['val'][$hash_var] ?? '';
			$hash_string .= '|';
		}
		$hash_string .= $gateway->parameters->salt ?? '';

		$send['val']['hash'] = strtolower(hash('sha512', $hash_string));
		$send['view'] = 'user.payment.redirect';
		$send['method'] = 'post';
		$send['url'] = 'https://test.payu.in/_payment';
        if ($gateway->environment == 'live') {
            $send['url'] = 'https://secure.payu.in/_payment';
        } else {
            $send['url'] = 'https://test.payu.in/_payment';
        }
		return json_encode($send);
	}

	public static function ipn($request, $gateway, $deposit = null, $trx = null, $type = null)
	{
		if (isset($request->status) && $request->status == 'success') {
			if (($gateway->parameters->merchant_key ?? '') == $request->key) {
				if ($deposit->utr == $request->txnid) {
					if (round($deposit->payable_amount, 2) <= $request->amount) {
						BasicService::prepareOrderUpgradation($deposit);

						$data['status'] = 'success';
						$data['msg'] = 'Transaction was successful.';
						$data['redirect'] = route('success');
					} else {
						$data['status'] = 'error';
						$data['msg'] = 'invalid amount.';
						$data['redirect'] = route('failed');
					}
				} else {
					$data['status'] = 'error';
					$data['msg'] = 'invalid trx id.';
					$data['redirect'] = route('failed');
				}
			} else {
				$data['status'] = 'error';
				$data['msg'] = 'deposit into wrong account.';
				$data['redirect'] = route('failed');
			}
		} else {
			$data['status'] = 'error';
			$data['msg'] = 'unexpected error.';
			$data['redirect'] = route('failed');
		}
		return $data;
	}
}
