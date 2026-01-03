<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NotifyTemplateSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		$notifyTemplates = [
			[
				'language_id' => 1,
				'name' => 'Send money',
				'template_key' => 'TRANSFER_TO',
				'body' => '[[sender]] send money to your account amount [[amount]] [[currency]] . Transaction: #[[transaction]]',
				'short_keys' => '{"sender":"Sender Name","amount":"Received Amount","currency":"Transfer Currency","transaction":"Transaction Number"}',
				'status' => 1,
				'notify_for' => 0, // 1 = Admin, 0 = User
				'lang_code' => 'en',
			],
			[
				'language_id' => 1,
				'name' => 'Send money',
				'template_key' => 'TRANSFER_FROM',
				'body' => 'You have send money to [[receiver]] account amount [[amount]] [[currency]].Transaction: #[[transaction]]',
				'short_keys' => '{"receiver":"Receiver Name","amount":"Send Amount","currency":"Transfer Currency","transaction":"Transaction Number"}',
				'status' => 1,
				'notify_for' => 0, // 1 = Admin, 0 = User
				'lang_code' => 'en',
			],
			[
				'language_id' => 1,
				'name' => 'Request Money Initialise',
				'template_key' => 'REQUEST_MONEY_INIT',
				'body' => '[[sender]] request for send money to account amount [[amount]] [[currency]] . Transaction: #[[transaction]]',
				'short_keys' => '{"sender":"Sender Name","amount":"Received Amount","currency":"Transfer Currency","transaction":"Transaction Number"}',
				'status' => 1,
				'notify_for' => 0, // 1 = Admin, 0 = User
				'lang_code' => 'en',
			],
			[
				'language_id' => 1,
				'name' => 'Request Money Confirm',
				'template_key' => 'REQUEST_MONEY_CONFIRM',
				'body' => '[[sender]] confirm your request money amount [[amount]] [[currency]] . Transaction: #[[transaction]]',
				'short_keys' => '{"sender":"Sender Name","amount":"Received Amount","currency":"Transfer Currency","transaction":"Transaction Number"}',
				'status' => 1,
				'notify_for' => 0, // 1 = Admin, 0 = User
				'lang_code' => 'en',
			],
			[
				'language_id' => 1,
				'name' => 'Request Money Cancel',
				'template_key' => 'REQUEST_MONEY_CANCEL',
				'body' => '[[sender]] cancel your request money amount [[amount]] [[currency]] . Transaction: #[[transaction]]',
				'short_keys' => '{"sender":"Sender Name","amount":"Received Amount","currency":"Transfer Currency","transaction":"Transaction Number"}',
				'status' => 1,
				'notify_for' => 0, // 1 = Admin, 0 = User
				'lang_code' => 'en',
			],
			[
				'language_id' => 1,
				'name' => 'Money Exchange',
				'template_key' => 'MONEY_EXCHANGE',
				'body' => 'You are exchange [[from_amount]] [[from_currency]] to [[to_amount]] [[to_currency]]. Transaction: #[[transaction]]',
				'short_keys' => '{"from_amount":"Amount Exchange From","from_currency":"Currency Exchange From","to_amount":"Amount Exchange To","to_currency":"Currency Exchange To","transaction":"Transaction Number"}',
				'status' => 1,
				'notify_for' => 0, // 1 = Admin, 0 = User
				'lang_code' => 'en',
			],
			[
				'language_id' => 1,
				'name' => 'Redeem Code Generate',
				'template_key' => 'REDEEM_CODE_GENERATE',
				'body' => 'You have generate a redeem code amount [[amount]] [[currency]].Transaction: #[[transaction]]',
				'short_keys' => '{"amount":"Request Amount","currency":"Request Currency","transaction":"Redeem Code"}',
				'status' => 1,
				'notify_for' => 0, // 1 = Admin, 0 = User
				'lang_code' => 'en',
			],
			[
				'language_id' => 1,
				'name' => 'Redeem code sender',
				'template_key' => 'REDEEM_CODE_SENDER',
				'body' => '[[receiver]] used your redeem code amount [[amount]] [[currency]] . Transaction: #[[transaction]]',
				'short_keys' => '{"receiver":"Receiver Name who used code","amount":"Request Amount","currency":"Request Currency","transaction":"Transaction Number"}',
				'status' => 1,
				'notify_for' => 0, // 1 = Admin, 0 = User
				'lang_code' => 'en',
			],
			[
				'language_id' => 1,
				'name' => 'Redeem code used by',
				'template_key' => 'REDEEM_CODE_USED_BY',
				'body' => 'You have used a redeem code amount [[amount]] [[currency]] . Transaction: #[[transaction]]',
				'short_keys' => '{"amount":"Request Amount","currency":"Request Currency","transaction":"Redeem Code"}',
				'status' => 1,
				'notify_for' => 0, // 1 = Admin, 0 = User
				'lang_code' => 'en',
			],
			[
				'language_id' => 1,
				'name' => 'Escrow request sender',
				'template_key' => 'ESCROW_REQUEST_SENDER',
				'body' => 'Your escrow request to [[receiver]] amount [[amount]] [[currency]] . Transaction: #[[transaction]]',
				'short_keys' => '{"receiver":"Receiver Name","amount":"Request Amount","currency":"Request Currency","transaction":"Transaction Number"}',
				'status' => 1,
				'notify_for' => 0, // 1 = Admin, 0 = User
				'lang_code' => 'en',
			],
			[
				'language_id' => 1,
				'name' => 'Escrow request receiver',
				'template_key' => 'ESCROW_REQUEST_RECEIVER',
				'body' => 'You have escrow request from [[sender]] amount [[amount]] [[currency]] . Transaction: #[[transaction]]',
				'short_keys' => '{"sender":"Sender Name","amount":"Request Amount","currency":"Request Currency","transaction":"Transaction Number"}',
				'status' => 1,
				'notify_for' => 0, // 1 = Admin, 0 = User
				'lang_code' => 'en',
			],
			[
				'language_id' => 1,
				'name' => 'Escrow Request Accept from',
				'template_key' => 'ESCROW_REQUEST_ACCEPT_FROM',
				'body' => '[[sender]] accept your request amount [[amount]] [[currency]] . Transaction: #[[transaction]]',
				'short_keys' => '{"sender":"Request sender Name","amount":"Received Amount","currency":"Transfer Currency","transaction":"Transaction Number"}',
				'status' => 1,
				'notify_for' => 0, // 1 = Admin, 0 = User
				'lang_code' => 'en',
			],
			[
				'language_id' => 1,
				'name' => 'Escrow Request Accept by',
				'template_key' => 'ESCROW_REQUEST_ACCEPT_BY',
				'body' => 'You accept escrow request amount [[amount]] [[currency]] . Transaction: #[[transaction]]',
				'short_keys' => '{"amount":"Received Amount","currency":"Transfer Currency","transaction":"Transaction Number"}',
				'status' => 1,
				'notify_for' => 0, // 1 = Admin, 0 = User
				'lang_code' => 'en',
			],
			[
				'language_id' => 1,
				'name' => 'Escrow Request Cancel from',
				'template_key' => 'ESCROW_REQUEST_CANCEL_FROM',
				'body' => '[[sender]] Cancel your request amount [[amount]] [[currency]] . Transaction: #[[transaction]]',
				'short_keys' => '{"sender":"Request sender Name","amount":"Received Amount","currency":"Transfer Currency","transaction":"Transaction Number"}',
				'status' => 1,
				'notify_for' => 0, // 1 = Admin, 0 = User
				'lang_code' => 'en',
			],
			[
				'language_id' => 1,
				'name' => 'Escrow Request Cancel by',
				'template_key' => 'ESCROW_REQUEST_CANCEL_BY',
				'body' => 'You Cancel escrow request amount [[amount]] [[currency]] . Transaction: #[[transaction]]',
				'short_keys' => '{"amount":"Received Amount","currency":"Transfer Currency","transaction":"Transaction Number"}',
				'status' => 1,
				'notify_for' => 0, // 1 = Admin, 0 = User
				'lang_code' => 'en',
			],
			[
				'language_id' => 1,
				'name' => 'Escrow payment disburse from',
				'template_key' => 'ESCROW_PAYMENT_DISBURSED_REQUEST_FROM',
				'body' => '[[sender]] request to disburse your amount [[amount]] [[currency]] . Transaction: #[[transaction]]',
				'short_keys' => '{"sender":"Request sender Name","amount":"Request Amount","currency":"Request Currency","transaction":"Transaction Number"}',
				'status' => 1,
				'notify_for' => 0, // 1 = Admin, 0 = User
				'lang_code' => 'en',
			],
			[
				'language_id' => 1,
				'name' => 'request to payment disburse by',
				'template_key' => 'ESCROW_PAYMENT_DISBURSED_REQUEST_BY',
				'body' => 'You request escrow disburse amount [[amount]] [[currency]] . Transaction: #[[transaction]]',
				'short_keys' => '{"amount":"Request Amount","currency":"Request Currency","transaction":"Transaction Number"}',
				'status' => 1,
				'notify_for' => 0, // 1 = Admin, 0 = User
				'lang_code' => 'en',
			],
			[
				'language_id' => 1,
				'name' => 'Escrow payment disburse from',
				'template_key' => 'ESCROW_PAYMENT_DISBURSED_FROM',
				'body' => '[[sender]] disburse your request amount [[amount]] [[currency]] . Transaction: #[[transaction]]',
				'short_keys' => '{"sender":"Request sender Name","amount":"Request Amount","currency":"Request Currency","transaction":"Transaction Number"}',
				'status' => 1,
				'notify_for' => 0, // 1 = Admin, 0 = User
				'lang_code' => 'en',
			],
			[
				'language_id' => 1,
				'name' => 'request to payment disburse by',
				'template_key' => 'ESCROW_PAYMENT_DISBURSED_BY',
				'body' => 'You disburse escrow request amount [[amount]] [[currency]] . Transaction: #[[transaction]]',
				'short_keys' => '{"amount":"Request Amount","currency":"Request Currency","transaction":"Transaction Number"}',
				'status' => 1,
				'notify_for' => 0, // 1 = Admin, 0 = User
				'lang_code' => 'en',
			],
			[
				'language_id' => 1,
				'name' => 'Dispute request to admin',
				'template_key' => 'DISPUTE_REQUEST_TO_ADMIN',
				'body' => '[[sender]] dispute escrow request amount [[amount]] [[currency]] . Transaction: #[[transaction]] click to reply [[link]]',
				'short_keys' => '{"sender":"Sender Name","amount":"Request Amount","currency":"Request Currency","transaction":"Transaction Number","link":"Dispute reply link"}',
				'status' => 1,
				'notify_for' => 1, // 1 = Admin, 0 = User
				'lang_code' => 'en',
			],
			[
				'language_id' => 1,
				'name' => 'Dispute request to user',
				'template_key' => 'DISPUTE_REQUEST_TO_USER',
				'body' => '[[sender]] reply dispute escrow request amount. Transaction: #[[transaction]] click to reply [[link]]',
				'short_keys' => '{"sender":"Sender Name","transaction":"Transaction Number","link":"Dispute reply link"}',
				'status' => 1,
				'notify_for' => 0, // 1 = Admin, 0 = User
				'lang_code' => 'en',
			],
			[
				'language_id' => 1,
				'name' => 'Voucher payment request to',
				'template_key' => 'VOUCHER_PAYMENT_REQUEST_TO',
				'body' => '[[sender]] request to voucher payment amount [[amount]] [[currency]] . Transaction: #[[transaction]] click to payment [[link]]',
				'short_keys' => '{"sender":"Sender Name","amount":"Request Amount","currency":"Request Currency","transaction":"Transaction Number","link":"Dispute reply link"}',
				'status' => 1,
				'notify_for' => 0, // 1 = Admin, 0 = User
				'lang_code' => 'en',
			],
			[
				'language_id' => 1,
				'name' => 'Voucher payment request from',
				'template_key' => 'VOUCHER_PAYMENT_REQUEST_FROM',
				'body' => 'You request to [[receiver]] voucher payment amount [[amount]] [[currency]] . Transaction: #[[transaction]]',
				'short_keys' => '{"receiver":"Receiver Name","amount":"Request Amount","currency":"Request Currency","transaction":"Transaction Number"}',
				'status' => 1,
				'notify_for' => 0, // 1 = Admin, 0 = User
				'lang_code' => 'en',
			],
			[
				'language_id' => 1,
				'name' => 'Voucher payment to',
				'template_key' => 'VOUCHER_PAYMENT_TO',
				'body' => '[[receiver]] payment to your voucher amount [[amount]] [[currency]] . Transaction: #[[transaction]]',
				'short_keys' => '{"receiver":"Request receiver name","amount":"Request Amount","currency":"Request Currency","transaction":"Transaction Number"}',
				'status' => 1,
				'notify_for' => 0, // 1 = Admin, 0 = User
				'lang_code' => 'en',
			],
			[
				'language_id' => 1,
				'name' => 'Voucher payment from',
				'template_key' => 'VOUCHER_PAYMENT_FROM',
				'body' => 'You payment to [[sender]] voucher amount [[amount]] [[currency]] . Transaction: #[[transaction]]',
				'short_keys' => '{"sender":"Request sender Name","amount":"Request Amount","currency":"Request Currency","transaction":"Transaction Number"}',
				'status' => 1,
				'notify_for' => 0, // 1 = Admin, 0 = User
				'lang_code' => 'en',
			],
			[
				'language_id' => 1,
				'name' => 'Voucher payment cancel to',
				'template_key' => 'VOUCHER_PAYMENT_CANCEL_TO',
				'body' => '[[receiver]] payment cancel to your voucher amount [[amount]] [[currency]] . Transaction: #[[transaction]]',
				'short_keys' => '{"receiver":"Request receiver name","amount":"Request Amount","currency":"Request Currency","transaction":"Transaction Number"}',
				'status' => 1,
				'notify_for' => 0, // 1 = Admin, 0 = User
				'lang_code' => 'en',
			],
			[
				'language_id' => 1,
				'name' => 'Voucher payment cancel from',
				'template_key' => 'VOUCHER_PAYMENT_CANCEL_FROM',
				'body' => 'You payment cancel to [[sender]] voucher amount [[amount]] [[currency]] . Transaction: #[[transaction]]',
				'short_keys' => '{"sender":"Request sender Name","amount":"Request Amount","currency":"Request Currency","transaction":"Transaction Number"}',
				'status' => 1,
				'notify_for' => 0, // 1 = Admin, 0 = User
				'lang_code' => 'en',
			],
			[
				'language_id' => 1,
				'name' => 'Payout Request Admin',
				'template_key' => 'PAYOUT_REQUEST_TO_ADMIN',
				'body' => '[[sender]] request for payment amount [[amount]] [[currency]] . Transaction: #[[transaction]]',
				'short_keys' => '{"sender":"Sender Name","amount":"Received Amount","currency":"Transfer Currency","transaction":"Transaction Number"}',
				'status' => 1,
				'notify_for' => 1, // 1 = Admin, 0 = User
				'lang_code' => 'en',
			],
			[
				'language_id' => 1,
				'name' => 'Payout Request from',
				'template_key' => 'PAYOUT_REQUEST_FROM',
				'body' => 'You request for payout amount [[amount]] [[currency]] . Transaction: #[[transaction]]',
				'short_keys' => '{"amount":"Received Amount","currency":"Transfer Currency","transaction":"Transaction Number"}',
				'status' => 1,
				'notify_for' => 0, // 1 = Admin, 0 = User
				'lang_code' => 'en',
			],
			[
				'language_id' => 1,
				'name' => 'Payout Confirm',
				'template_key' => 'PAYOUT_CONFIRM',
				'body' => '[[sender]] confirm your payout amount [[amount]] [[currency]] . Transaction: #[[transaction]]',
				'short_keys' => '{"sender":"Sender Name","amount":"Received Amount","currency":"Transfer Currency","transaction":"Transaction Number"}',
				'status' => 1,
				'notify_for' => 0, // 1 = Admin, 0 = User
				'lang_code' => 'en',
			],
			[
				'language_id' => 1,
				'name' => 'Payout Cancel',
				'template_key' => 'PAYOUT_CANCEL',
				'body' => '[[sender]] cancel your payout amount [[amount]] [[currency]] . Transaction: #[[transaction]]',
				'short_keys' => '{"sender":"Sender Name","amount":"Received Amount","currency":"Transfer Currency","transaction":"Transaction Number"}',
				'status' => 1,
				'notify_for' => 0, // 1 = Admin, 0 = User
				'lang_code' => 'en',
			],
			[
				'language_id' => 1,
				'name' => 'Add Fund user user',
				'template_key' => 'ADD_FUND_USER_USER',
				'short_keys' => '{"amount":"Request Amount","currency":"Request Currency","transaction":"Transaction Number"}',
				'body' => 'you add fund money amount [[amount]] [[currency]] . Transaction: #[[transaction]]',
				'status' => 1,
				'notify_for' => 0, // 1 = Admin, 0 = User
				'lang_code' => 'en',
			],
			[
				'language_id' => 1,
				'name' => 'Add Fund user admin',
				'template_key' => 'ADD_FUND_USER_ADMIN',
				'short_keys' => '{"user":"User full name","amount":"Request Amount","currency":"Request Currency","transaction":"Transaction Number"}',
				'body' => '[[user]] add fund money amount [[amount]] [[currency]] . Transaction: #[[transaction]]',
				'status' => 1,
				'notify_for' => 1, // 1 = Admin, 0 = User
				'lang_code' => 'en',
			],
			[
				'language_id' => 1,
				'name' => 'Deposit Bonus',
				'template_key' => 'DEPOSIT_BONUS',
				'body' => 'Deposit Commission From [[sender]] amount [[amount]] [[currency]] . Transaction: #[[transaction]]',
				'short_keys' => '{"sender":"Sender Name","amount":"Received Amount","currency":"Transfer Currency","transaction":"Transaction Number"}',
				'status' => 1,
				'notify_for' => 0, // 1 = Admin, 0 = User
				'lang_code' => 'en',
			],
			[
				'language_id' => 1,
				'name' => 'Login Bonus',
				'template_key' => 'LOGIN_BONUS',
				'body' => 'Login Commission From [[sender]] amount [[amount]] [[currency]] . Transaction: #[[transaction]]',
				'short_keys' => '{"sender":"Sender Name","amount":"Received Amount","currency":"Transfer Currency","transaction":"Transaction Number"}',
				'status' => 1,
				'notify_for' => 0, // 1 = Admin, 0 = User
				'lang_code' => 'en',
			],
			[
				'language_id' => 1,
				'name' => 'Security answer reset',
				'template_key' => 'SECURITY_ANSWER_RESET',
				'body' => 'Admin reset your security answer [[answer]]',
				'short_keys' => '{"answer":"Reset answer"}',
				'status' => 1,
				'notify_for' => 0, // 1 = Admin, 0 = User
				'lang_code' => 'en',
			],
			[
				'language_id' => 1,
				'name' => 'Security pin reset',
				'template_key' => 'SECURITY_PIN_RESET',
				'body' => 'Admin reset your security pin [[pin]]',
				'short_keys' => '{"pin":"Reset pin"}',
				'status' => 1,
				'notify_for' => 0, // 1 = Admin, 0 = User
				'lang_code' => 'en',
			],
		];

		DB::table('notify_templates')->insert($notifyTemplates);
	}
}
