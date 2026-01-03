<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

class CreateEmailTemplatesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('email_templates', function (Blueprint $table) {
			$table->id();
			$table->foreignId('language_id')->nullable()->constrained('languages');
			$table->string('template_key', 40)->nullable();
			$table->string('email_from', 60)->nullable();
			$table->string('name')->nullable();
			$table->string('subject')->nullable();
			$table->text('template')->nullable();
			$table->text('sms_body')->nullable();
			$table->text('short_keys')->nullable();
			$table->boolean('mail_status')->default('0');
			$table->boolean('sms_status')->default('0');
			$table->string('lang_code', 10)->nullable();
			$table->timestamps();
		});

		Artisan::call('db:seed', [
			'--class' => 'EmailTemplateSeeder',
			'--force' => true
		]);
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('email_templates');
	}
}
