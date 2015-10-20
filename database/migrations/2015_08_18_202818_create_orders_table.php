<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('orders', function(Blueprint $table)
		{
			$table->increments('id');
	        $table->string('oid');
	        $table->string('gid')->nullable();
	        $table->string('gname')->nullable();
	        $table->timestamp('order_date');
	        $table->string('country');
	        $table->integer('amount');
	        $table->decimal('sum',5,2)->default(0);
	        $table->integer('days')->default(0);
	        $table->timestamp('go_date')->nullable();
	        $table->timestamp('back_date')->nullable();
	        $table->integer('gmobile')->nullable();
	        $table->text('address')->nullable();
	        $table->text('memo')->nullable();
	        $table->text('message')->nullable();
	        $table->integer('status')->default(1);
	        $table->integer('source')->default(0);
	        $table->integer('house')->default(0);
	        $table->timestamp('send_date')->nullable();
	        $table->integer('is_deliver')->default(0);
	        $table->string('delivery_no')->nullable();
	        $table->string('delivery_company')->nullable();
	        $table->timestamp('modified_at')->nullable();
	        $table->string('reasons')->nullable();
	        $table->json('logs')->nullable();
	        $table->boolean('is_important')->default(false);
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
		Schema::drop('orders');
	}

}
