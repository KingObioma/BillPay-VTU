<?php

namespace App\Services\Gateway\stripe;

use App\Models\Voucher;
use Facades\App\Services\BasicService;
use StripeJS\Charge;
use StripeJS\Customer;
use StripeJS\StripeJS;

require_once('stripe-php/init.php');

class Payment
{
	public static function prepareData($deposit, $gateway)
	{
		$basic = basicControl();
		$val['key'] = $gateway->parameters->publishable_key ?? '';
		$val['name'] = optional($deposit->receiver)->name ?? $basic->site_title;
		$val['description'] = "Payment with Stripe";
		$val['amount'] = (int)($deposit->payable_amount * 100);
		$val['currency'] = $deposit->payment_method_currency;
		$send['val'] = $val;
		$send['src'] = "https://checkout.stripe.com/checkout.js";
		$send['view'] = 'user.payment.stripe';
		$send['method'] = 'post';
		$send['url'] = route('ipn', [$gateway->code, $deposit->utr]);
		return json_encode($send);
	}

	public static function ipn($request, $gateway, $deposit = null, $trx = null, $type = null)
	{
		StripeJS::setApiKey($gateway->parameters->secret_key);

		$customer = Customer::create([
			'email' => $request->stripeEmail,
			'source' => $request->stripeToken,
		]);

		$charge = Charge::create([
			'customer' => $customer->id,
			'description' => 'Payment with Stripe',
			'amount' => (int)($deposit->payable_amount * 100),
			'currency' => $deposit->payment_method_currency,
		]);


		if ($charge['status'] == 'succeeded') {
			BasicService::prepareOrderUpgradation($deposit);

			$data['status'] = 'success';
			$data['msg'] = 'Transaction was successful.';
			$data['redirect'] = route('success');
		} else {
			$data['status'] = 'error';
			$data['msg'] = 'unsuccessful transaction.';
			$data['redirect'] = route('failed');
		}
		return $data;
	}
}
