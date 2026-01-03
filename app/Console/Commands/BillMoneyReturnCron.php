<?php

namespace App\Console\Commands;

use App\Models\BillPay;
use App\Models\Transaction;
use App\Traits\Notify;
use Carbon\Carbon;
use Illuminate\Console\Command;

class BillMoneyReturnCron extends Command
{
	use Notify;

	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'bill:return';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Pending Bill Money Return';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return int
	 */
	public function handle()
	{
		$billPays = BillPay::where('status', 2)->where('created_at', '<', Carbon::now()->subHours(3))->get();

		if ($billPays) {
			foreach ($billPays as $bill) {
				try {
					updateWallet($bill->user_id, $bill->pay_amount_in_base, 1);
					$bill->status = 4;

					$transaction = new Transaction();
					$transaction->amount = $bill->pay_amount_in_base;
					$transaction->charge = 0;
					$transaction->currency_code = config('basic.base_currency');
					$transaction->remark = getAmount($bill->pay_amount_in_base, 3) . ' ' . config('basic.base_currency') . ' added your wallet from bill return';
					$bill->transactional()->save($transaction);
					$bill->save();

					$params = [
						'type' => $bill->type,
						'amount' => getAmount($bill->payable_amount, 2),
						'currency' => $bill->currency,
						'return_currency_amount' => getAmount($bill->pay_amount_in_base, 2),
						'return_currency' => config('basic.base_currency'),
						'transaction' => $bill->utr,
					];
					$action = [
						"link" => "#",
						"icon" => "fa fa-money-bill-alt text-white"
					];

					$billReturnCron = new BillMoneyReturnCron();
					$billReturnCron->sendMailSms($bill->user, 'BILL_PAYMENT_RETURN', $params);
					$billReturnCron->userPushNotification($bill->user, 'BILL_PAYMENT_RETURN', $params, $action);
					$billReturnCron->userFirebasePushNotification($bill->user, 'BILL_PAYMENT_RETURN', $params);
				} catch (\Exception $e) {
					continue;
				}
			}
		}
		return 0;
	}
}
