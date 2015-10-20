<?php namespace App\Http\Controllers;
use App\Services\ExcelService;
class HomeController extends Controller {

	/*
	|--------------------------------------------------------------------------
	| Home Controller
	|--------------------------------------------------------------------------
	|
	| This controller renders your application's "dashboard" for users that
	| are authenticated. Of course, you are free to change or remove the
	| controller as you wish. It is just here to get your app started!
	|
	*/
	protected $fileinput = 'fileinput';
	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
	}

	/**
	 * Show the application dashboard to the user.
	 *
	 * @return Response
	 */
	public function index()
	{
		return view('home');
	}

	public function postUpload(Request $request,ExcelService $service){
		if(!$request->hasFile($this->fileinput)){
			return response('Error!',400);
		}
		$error=$service->validator($request->file($this->fileinput));
		if (count($error)>0)
		{

			return response($error,400);
		}

		if(!$fileName=$service->save($request->file($this->fileinput))){
			return response('保存失败',400);
		}

		return response($fileName,200);

	}
}
