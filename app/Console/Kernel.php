<?php

namespace App\Console;

use App\Console\Commands\BillMoneyReturnCron;
use App\Console\Commands\BlockIoIPN;
use App\Console\Commands\ReloadlyBillStatusCheck;
use App\Models\Gateway;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
	/**
	 * The Artisan commands provided by your application.
	 *
	 * @var array
	 */
	protected $commands = [
		BlockIoIPN::class,
		BillMoneyReturnCron::class,
		ReloadlyBillStatusCheck::class,
	];

	/**
	 * Define the application's command schedule.
	 *
	 * @param \Illuminate\Console\Scheduling\Schedule $schedule
	 * @return void
	 */
	protected function schedule(Schedule $schedule)
	{
		$basicControl = basicControl();
		$blockIoGateway = Gateway::where(['code' => 'blockio', 'status' => 1])->count();
		if ($blockIoGateway == 1) {
			$schedule->command('blockIo:ipn')->everyThirtyMinutes();
		}

		$schedule->command('bill:return')->everySixHours();
		$schedule->command('bill:check')->everyMinute();
	}

	/**
	 * Register the commands for the application.
	 *
	 * @return void
	 */
	protected function commands()
	{
		$this->load(__DIR__ . '/Commands');

		require base_path('routes/console.php');
	}
}
