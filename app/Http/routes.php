<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/
Route::get('test-excel',function(){
	$reader = Excel::selectSheetsByIndex(0)->load(storage_path('uploads/excel').'/1444899822.xlsx');
	$sheets=$reader->get();
	dd($sheets);
});

Route::controllers([
	'auth' => 'Auth\AuthController',
]);

Route::group(['middleware'=>'auth'],function(){
	Route::get('/','OrderController@index');
	Route::post('/upload','HomeController@postUpload');
	Route::group(['prefix'=>'order'],function(){
		Route::get('import','OrderController@getImport');
		Route::post('import','OrderController@postImport');

	});
	Route::resources([
			'order'=>'OrderController',
			'product'=>'ProductController',
			'supply'=>'SupplyController',
			'user'=>'UserController'
		]);


});