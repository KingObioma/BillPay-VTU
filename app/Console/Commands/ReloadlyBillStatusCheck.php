<?php

namespace App\Console\Commands;

use App\Models\BillPay;
use App\Services\Bill\reloadly\Card;
use App\Traits\Notify;
use Illuminate\Console\Command;

class ReloadlyBillStatusCheck extends Command
{
	use Notify;

	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'bill:check';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Reloadly Bill Status Check ';

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
		$billPays = BillPay::with('method')->whereHas('method', function ($query) {
			return $query->where('code', 'reloadly');
		})->where('status', 5)->where('reference_id', '!=', null)
			->get()->map(function ($query) {
				$reloadly = new Card();
				$apiRes = $reloadly->getStatus($query, $query->method);
				if ($apiRes['status'] == 'SUCCESSFUL') {
					$query->status = 3;
					$query->save();

					$reloadlyBillStatusCheck = new ReloadlyBillStatusCheck();
					$reloadlyBillStatusCheck->notify($query);

				} elseif ($apiRes['status'] == 'FAILED' || $apiRes['status'] == 'REFUNDED') {
					$query->status = 2;
					$query->last_api_error = $apiRes['message'];
					$query->save();
				}

				return $query;
			});

		return 0;
	}

	public function notify($query)
	{
		try {
			$params = [
				'type' => $query->type,
				'amount' => getAmount($query->payable_amount, 2),
				'currency' => $query->currency,
				'transaction' => $query->utr,
			];
			$action = [
				"link" => "#",
				"icon" => "fa fa-money-bill-alt text-white"
			];

			$this->sendMailSms($query->user, 'BILL_PAYMENT', $params);
			$this->userPushNotification($query->user, 'BILL_PAYMENT', $params, $action);
			$this->userFirebasePushNotification($query->user, 'BILL_PAYMENT', $params);
			$this->adminPushNotification('BILL_PAYMENT', $params, $action);
			return 0;
		} catch (\Exception $exception) {
			return 0;
		}
	}
}
