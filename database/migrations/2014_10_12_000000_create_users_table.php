<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('users', function (Blueprint $table) {
			$table->id();
			$table->foreignId('ref_by')->nullable()->constrained('users');
			$table->string('name');
			$table->string('email', 50)->unique();
			$table->string('username', 50)->unique();
			$table->boolean('status')->default(1)->comment('0 = inactive, 1 = active');
			$table->bigInteger('language_id')->nullable();
			$table->timestamp('email_verified_at')->nullable();
			$table->boolean('sms_verification')->default(0)->comment('0 = inactive, 1 = active');
			$table->boolean('email_verification')->default(0)->comment('0 = inactive, 1 = active');
			$table->string('verify_code',10)->nullable();
			$table->dateTime('sent_at')->nullable();
			$table->string('password');
			$table->rememberToken();
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('users');
	}
}
