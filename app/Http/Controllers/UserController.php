<?php namespace App\Http\Controllers;
use Auth;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Services\UserService;
use Illuminate\Http\Request;
use App\UserField;
class UserController extends Controller {
	protected $service,$user;
	protected $errorMessage = [];

	public function __construct(UserService $service){
		$this->service = $service;
		$this->user = Auth::user();
	}
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index(Request $request,UserField $fieldService)
	{
		$arrRequest = $request->all();

		$fieldService->currentRole($this->user->auth);
		$fieldService->currentStatus('');
		$data=array();
		$data['title']='用户';
		$data['stitle']='';
		$data['class']='user';
		$data['field']=$fieldService;
		$data['data']=$this->service->lists($arrRequest,'',20);
		$data['actions']=['create','delete'];
		return view('home')->with($data)->withInput($request->flash());
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create(UserField $fieldService)
	{
		$fieldService->currentRole($this->user->auth);
		$fieldService->currentStatus('');
		$data=[];
		$data['field']=$fieldService;
		$data['actions']=['submit','backpage'];
		return view('create.user')->with($data);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store(Request $request,UserField $fieldService)
	{
		//
		$fields = $fieldService->parseValidator('add');
		$this->validate($request,$fields);

		if($this->service->create($request->all())){
			return redirect('user')->withSuccess('添加成功');
		}
		else{
			return redirect()->back()->withErrors('操作失败');
		}
	}
	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id,UserField $fieldService)
	{
		//
		$user = $this->service->listOne($id);
		$fieldService->currentRole($this->user->auth);
		$fieldService->currentStatus('');
		$data['user']=$user;
		$data['field']=$fieldService;
		$data['actions']=['submit','backpage'];
		return view('detail.user')->with($data);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id,Request $request,UserField $fieldService)
	{
		//
		$fields = $fieldService->parseValidator('edit');
		$this->validate($request,$fields);

		if($this->service->edit($request->all(),$id)){
			return redirect()->back()->withSuccess('更新成功');
		}
		else{
			return redirect()->back()->withErrors('操作失败');
		}
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  string  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
		$i=0;
		$arrId = explode(',', $id);
		foreach ($arrId as $v) {
			if($this->service->delete($v)){
				$i++;
			}
		}
		return redirect()->back()->withSuccess('成功删除 '.$i.' 条数据');
	}

}
