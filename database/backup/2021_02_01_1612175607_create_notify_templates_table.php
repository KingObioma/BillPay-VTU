<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

class CreateNotifyTemplatesTable extends Migration
{
	public function up()
	{
		Schema::create('notify_templates', function (Blueprint $table) {
			$table->id();
			$table->foreignId('language_id')->nullable()->constrained('languages');
			$table->string('name')->nullable();
			$table->string('template_key')->nullable();
			$table->text('body')->nullable();
			$table->text('short_keys')->nullable();
			$table->boolean('status')->default('1');
			$table->boolean('notify_for')->default('0');
			$table->string('lang_code')->nullable();
			$table->timestamps();
		});
		Artisan::call('db:seed', [
			'--class' => 'NotifyTemplateSeeder',
			'--force' => true
		]);
	}

	public function down()
	{
		Schema::dropIfExists('notify_templates');
	}
}
