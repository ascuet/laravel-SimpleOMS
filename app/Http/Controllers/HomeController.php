<?php namespace App\Http\Controllers;
use App\Services\ExcelService;
use Illuminate\Http\Request;
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
	protected $fileinput = 'files';
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
		$files = $request->file($this->fileinput);
		$rtn=[];
		foreach ($files as  $file) {
			$validator=$service->validator(['file'=>$file]);
			$rtnFile = [];
			if($validator->fails()){
				$rtnFile['error']=$validator->getMessageBag()->all();
				$rtnFile['old_name']=$file->getClientOriginalName();
				$rtn[]=$rtnFile;
				return response()->json($rtn);
			}
			if(!$fileName=$service->save($file)){
				return response('保存失败',400);
			}
			
			$rtnFile['old_name']=$file->getClientOriginalName();
			$rtnFile['name']=$fileName;
			$rtn[]=$rtnFile;
		}
			
		
		return response()->json($rtn);

	}
}
