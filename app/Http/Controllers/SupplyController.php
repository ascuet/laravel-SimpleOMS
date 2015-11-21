<?php namespace App\Http\Controllers;
use Auth;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Services\SupplyService;
use App\SupplyField;
use Illuminate\Http\Request;
use Session;
use URL;
use App\Services\Authorize;
class SupplyController extends Controller {

	protected $service,$user,$permission;
	protected $errorMessage = [];

	public function __construct(SupplyService $service,Authorize $permission){
		$this->service = $service;
		$this->user = Auth::user();
		$this->permission=$permission;
	}

	/**
	 * 显示 selecttable
	 *
	 *
	 */
	public function getSelecttable(Request $request, SupplyField $fieldService){
		$arrRequest = $request->all();
		$fieldService->currentRole($this->user->auth);
		$fieldService->currentStatus('');
		$data['title']='选择仓库';
		$data['class']='supply';
		$data['field']=$fieldService;
		$data['multi']=false;
		if(isset($arrRequest['belongsToSupply_supply'])){			
			$arrRequest['supply']=$arrRequest['belongsToSupply_supply'];
			unset($arrRequest['belongsToSupply_supply']);
		}
		$data['data']=$this->service->lists($arrRequest);
		return view('partials.select-modal')->with($data);

	}
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index(Request $request,SupplyField $fieldService)
	{
		$arrRequest = $request->all();
		$fieldService->currentRole($this->user->auth);
		$fieldService->currentStatus('');
		$data=array();
		$data['title']='仓库';
		$data['stitle']='';
		$data['class']='supply';
		$data['field']=$fieldService;
		$data['data']=$this->service->lists($arrRequest,'',20);
		$data['actions']=array_only($this->permission->get($this->user->auth),['SupplyController@create','SupplyController@destroy']);
		Session::put('index_url',URL::full());
		return view('home')->with($data)->withInput($request->flash());
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create(SupplyField $fieldService)
	{
		$fieldService->currentRole($this->user->auth);
		$fieldService->currentStatus('');
		$data=[];
		$data['class']='supply';
		$data['field']=$fieldService;
		$data['actions']=array_only($this->permission->get($this->user->auth),['SupplyController@store']);
		$data['actions'][]='backpage';
		return view('create.supply')->with($data);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store(Request $request,SupplyField $fieldService)
	{
		//
		$fieldService->currentRole($this->user->auth);
		$fieldService->currentStatus('');
		$fields = $fieldService->parseValidator('add');
		$this->validate($request,$fields);

		if($this->service->create($request->all())){
			return redirect('supply')->withSuccess('添加成功');
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
	public function edit($id,SupplyField $fieldService)
	{
		//
		$supply = $this->service->listOne($id);
		$fieldService->currentRole($this->user->auth);
		$fieldService->currentStatus('');
		$data['class']='supply';
		$data['supply']=$supply;
		$data['field']=$fieldService;
		$data['actions']=array_only($this->permission->get($this->user->auth),['SupplyController@store']);
		$data['actions'][]='backpage';
		return view('detail.supply')->with($data);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id,Request $request,SupplyField $fieldService)
	{
		//
		$fieldService->currentRole($this->user->auth);
		$fieldService->currentStatus('');
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
	public function destroy(Request $request)
	{
		//
		$arrId = $request->input('id',[]);
		$i=0;
		if(empty($arrId)){
			return redirect()->back();
		}
		foreach ($arrId as $v) {
			if($this->service->delete($v)){
				$i++;
			}
		}
		return redirect()->back()->withSuccess('成功删除 '.$i.' 条数据');
	}


}
