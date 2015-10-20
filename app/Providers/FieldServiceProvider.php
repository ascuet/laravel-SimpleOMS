<?php namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class FieldServiceProvider extends ServiceProvider {

	protected $defer = true;

	/**
	 * Bootstrap the application services.
	 *
	 * @return void
	 */
	public function boot()
	{
		//
	}

	/**
	 * Register the application services.
	 *
	 * @return void
	 */
	public function register()
	{
		//

		$this->app->singleton('App\OrderField');
		$this->app->singleton('App\ProductField');
		$this->app->singleton('App\SupplyField');
		$this->app->singleton('App\UserField');
		$this->app->singleton('App\UserLogField');

	}

	public function provides(){

		return ['App\OrderField','App\ProductField','App\SupplyField','App\UserField','App\UserLogField'];
	}

}
