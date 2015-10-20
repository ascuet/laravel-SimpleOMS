<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserLogsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_logs', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('user_id');
			$table->string('operation');
			$table->string('object')->nullable();
			$table->string('object_id')->nullable();
			$table->string('client_ip');
			$table->json('actions');
			$table->timestamp('log_at');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('user_logs');
	}

}
