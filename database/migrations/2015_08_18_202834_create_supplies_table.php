<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSuppliesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('supplies', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name');
			$table->string('slocation')->nullable();
			$table->string('saddress')->nullable();
			$table->boolean('is_self')->default(true);
			$table->string('supply');
			$table->json('logs')->nullable();
			$table->softDeletes();
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
		Schema::drop('supplies');
	}

}
