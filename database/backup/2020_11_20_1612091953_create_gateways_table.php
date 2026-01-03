<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

class CreateGatewaysTable extends Migration
{
	public function up()
	{
		Schema::create('gateways', function (Blueprint $table) {
			$table->id();
			$table->string('code', 191);
			$table->string('name', 191);
			$table->integer('sort_by')->default(1);
			$table->string('image', 191)->nullable();
			$table->tinyInteger('status')->default(1);
			$table->text('parameters')->nullable();
			$table->text('currencies')->nullable();
			$table->text('extra_parameters')->nullable();
			$table->string('currency', 191);
			$table->string('symbol', 191);
			$table->decimal('min_amount', 18, 8);
			$table->decimal('max_amount', 18, 8);
			$table->decimal('percentage_charge', 8, 4)->default('0.0000');
			$table->decimal('fixed_charge', 18, 8)->default('0.00000000');
			$table->decimal('convention_rate', 18, 8)->default('1.00000000');
			$table->timestamps();
		});

		Artisan::call('db:seed', [
			'--class' => 'GatewaySeeder',
			'--force' => true
		]);
	}

	public function down()
	{
		Schema::dropIfExists('gateways');
	}
}
