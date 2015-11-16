<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Services\OrderService;
use App\Services\ProductService;
use Illuminate\Http\Request;
use App\OrderField;
use Auth;
use Illuminate\Validation\Validator as ValidatorClass;
use DB;
use Validator;
use Session;
use URL;
use App\Services\Authorize;
class OrderController extends Controller {

	protected $service,$user,$permission;
	protected $errorMessage = [
		'importError'=>'导入时错误:',
		'deliveryInvalid'=>'快递信息未填完整',
		'productInvalid'=>'设备数不符'
	];

	public function __construct(OrderService $service,Authorize $permission){
		$this->service = $service;
		$this->user = Auth::user();
		$this->permission = $permission;
	}

	/**
	 * 切换高亮
	 *
	 *
	 */
	public function postToggleStar($id){
		DB::beginTransaction();
		$order = $this->service->listOne($id);
		if(!is_null($order)){
			$order->is_important = $order->is_important?false:true;
			$order->save();
			DB::commit();
			return view('partials.star')->with(['data'=>['is_important'=>$order->is_important]]);
		}
	}


	/**
	 * 回退订单
	 *
	 */
	public function postBackward($id,Request $request,ProductService $productService){
		$this->validate($request,[
			'reasons'=>'string|max:150'
			]);
		if(!$this->service->edit($request->all(),$id)){
			return redirect()->back()->withErrors('更新数据失败');			
		}
		if($this->service->backwardOrder($id,$request->input('reasons',''),$productService)){
			return redirect()->back()->withSuccess('回退成功');
		}
		else{
			return redirect()->back()->withErrors('发生错误,请刷新重试');
		}
	}
	/**
	 * 保存设备列表
	 *
	 */
	public function postSaveSend(Request $request,ProductService $productService){
		$this->validate($request,[
			'order_id'=>'required',
			'product_id'=>'required'
			]);
		$order_id = $request->input('order_id');
		$product_id = $request->input('product_id');
		$rtn = $this->service->combineProducts($order_id,$product_id,$productService);
		if(gettype($rtn)=='string'&&isset($this->errorMessage[$rtn])){
			return response($this->errorMessage[$rtn],400) ;
		}elseif($rtn===true){
			return response('操作成功',200);
		}else{
			return response('未知错误',500);
		}
		
	}

	/**
	 * 添加设备关联
	 *
	 *
	 */
	public function postCombine($id,Request $request,ProductService $productService){
		$this->validate($request,[
			'id'=>'exists:orders',
			'product_id'=>'required'
			]);
		$product_id = $request->input('product_id');
		DB::beginTransaction();
		$order = $this->service->listOne($id);
		if($order->amount == $order->products()->count()){
			DB::rollback();
			return response('设备数已满足, 请先移除',422);
		}
		if($order->status!=1||$order->belongsToSupply->is_self!=1){
			DB::rollback();
			return response('订单状态变化,请刷新重试',422);
		}
		if(!$rtn = $productService->combineProduct($product_id,$order)){
			DB::rollback();
			return response('设备状态变化或已绑定其他订单',422);
		}
		else{
			$this->service->combineLog($order);
			DB::commit();
			$products = $order->products()->with('belongsToSupply')->get();
			return view('partials.productCombinition')->with(['products'=>$products,'order'=>$order,'actions'=>['OrderController@postUnbind']]);
		}

	}

	/**
	 * 添加设备关联
	 *
	 *
	 */
	public function postUnbind($id,Request $request,ProductService $productService){
		$this->validate($request,[
			'id'=>'exists:orders',
			'product_id'=>'required'
			]);
		$product_id = $request->input('product_id');
		DB::beginTransaction();
		$order = $this->service->listOne($id);
		if($order->status!=1||$order->belongsToSupply->is_self!=1){
			return response('订单状态变化,请刷新重试',422);
		}
		$product = $order->products()->find($product_id);
		$order->products()->detach($product_id);
		$this->service->unbindLog($order,$product);
		DB::commit();
		$products = $order->products()->with('belongsToSupply')->get();
		return view('partials.productCombinition')->with(['products'=>$products,'order'=>$order,'actions'=>['unbindProduct']]);
		

	}
	/**
	 * 发货/批量发货
	 *
	 */
	public function postSend(Request $request,ProductService $productService){
		$this->validate($request,[
			'id'=>'required',
			'reasons'=>'string|max:150'
			]);
		$ids = $request->input('id');
		$reasons = $request->input('reasons','');

		if(is_array($ids)){
			$i = 0;
			foreach ($ids as $id) {
				$rtn = $this->service->sendOrder($id,'批量发货',$productService);
				if(gettype($rtn)=='string'&&isset($this->errorMessage[$rtn])){
					return redirect()->back()->withErrors('成功发货 '.$i.' 个订单, 其余订单不符合发货要求');
				}
				elseif($rtn===true){
					$i++;
				}else{
					return redirect()->back()->withErrors('发生错误');
				}
			}
			return redirect()->back()->withSuccess('成功发货 '.$i.' 个订单');
		}
		else{
			if(!$this->service->edit($request->all(),$ids)){
				return redirect()->back()->withErrors('更新数据失败');			
			}
			$rtn = $this->service->sendOrder($ids,$reasons,$productService);
			if(gettype($rtn)=='string'&&isset($this->errorMessage[$rtn])){
				return redirect()->back()->withErrors($this->errorMessage[$rtn]);
			}
			if($rtn===true){
				
			}else{
				return redirect()->back()->withErrors('发生错误');
			}	
		}
		return redirect()->back()->withSuccess('发货成功');	

	}

	/**
	 * 转入待发货
	 *
	 */
	public function postReady($id,Request $request){
		$this->validate($request,[
			'reasons'=>'string|max:150'
			]);
		$reasons = $request->input('reasons','');
		if(!$this->service->edit($request->all(),$id)){
			return redirect()->back()->withErrors('更新数据失败');			
		}
		$rtn = $this->service->prepareOrder($id,$reasons);
		if($rtn instanceof ValidatorClass){
			return redirect()->back()->withErrors($rtn->messages()->all());
		}elseif($rtn===true){
			return redirect()->back()->withSuccess('进入待发货');				
		}
		else{
			return redirect()->back()->withErrors('发生错误');
		}
	}

	/**
	 * 取消
	 *
	 */
	public function postCancel($id,Request $request,ProductService $productService){
		$this->validate($request,[
			'reasons'=>'string|max:150'
			]);
		$reasons = $request->input('reasons','');
		if(!$this->service->edit($request->all(),$id)){
			return redirect()->back()->withErrors('更新数据失败');			
		}
		$rtn = $this->service->cancelOrder($id,$reasons,$productService);
		if(gettype($rtn)=='string'&&isset($this->errorMessage[$rtn])){
			return redirect()->back()->withErrors($this->errorMessage[$rtn]);
		}elseif($rtn===true){
			return redirect()->back()->withSuccess('取消成功');	
		}else{
			return redirect()->back()->withErrors('发生错误');
		}
	}

	/**
	 * 完成
	 *
	 */
	public function postFinish($id,Request $request,ProductService $productService){
		$this->validate($request,[
			'reasons'=>'string|max:150'
			]);
		$reasons = $request->input('reasons','');
		if(!$this->service->edit($request->all(),$id)){
			return redirect()->back()->withErrors('更新数据失败');			
		}
		$rtn = $this->service->finishOrder($id,$reasons,$productService);
		if(gettype($rtn)=='string'&&isset($this->errorMessage[$rtn])){
			return redirect()->back()->withErrors($this->errorMessage[$rtn]);
		}elseif($rtn===true){
			return redirect()->back()->withSuccess('订单完成');				
		}else{
			return redirect()->back()->withErrors('发生错误');			
		}
	}

	/**
	 * 导入页面
	 *
	 *
	 */
	public function getImport(){
		return view('import.order');

	}

	/**
	 * 导入
	 *
	 */
	public function postImport(Request $request){
		$this->validate($request,[
			'file_name'=>'required'
			]);

		$rtn = $this->service->importOrders($request->input('file_name'));
		if($rtn instanceof ValidatorClass){
			$messages = $rtn->messages();
			$invalid = $rtn->invalid();
			return response()->json(array_merge($messages->all(),$invalid),422);
		}elseif($rtn===true){
			return response('导入成功',200);			
		}else{
			return response('发生错误',500);
		}

	}

	/**
	 * 导出
	 *
	 */
	public function getExport(Request $request,OrderField $fieldService){		
		$arrRequest = $request->all();
		$status = isset($arrRequest['status'])?$arrRequest['status']:'';
		$fieldService->currentRole($this->user->auth);
		is_array($status)&&$status='';
		$fieldService->currentStatus($status===''?$status:intval($status));
				
		return $this->service->exportOrder($arrRequest);
	}
	
	/**
	 * selecttable操作
	 *
	 */
	public function getSelecttable(){

	}
	/**
	 * selecttable 筛选
	 *
	 */
	public function postSelecttable(){
	}
	/**
	 * 部分更新查询页面的列表
	 *
	 */
	public function getTable(Request $request,OrderField $fieldService){
		$arrRequest = $request->all();
		$status = isset($arrRequest['status'])?$arrRequest['status']:'';
		$data=array();
		$data['table']=$fieldService->getFieldsByMethod('data',$this->user->auth,$status);
		$data['data']=$this->service->lists($arrRequest);
		return view('order')->with($data);
	}
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index(Request $request,OrderField $fieldService)
	{
		//
		$arrRequest = $request->all();
		$status = isset($arrRequest['status'])?$arrRequest['status']:'';
		$fieldService->currentRole($this->user->auth);
		is_array($status)&&$status='';
		$fieldService->currentStatus($status===''?$status:intval($status));
		$data=array();
		$data['title']='订单';
		$data['stitle']=$fieldService->statusName($status);
		$data['class']='order';
		$data['field']=$fieldService;
		$data['data']=$this->service->lists($arrRequest,'',20);
		$data['actions']=array_only($this->permission->get($this->user->auth),['OrderController@create','OrderController@destroy','OrderController@getImport','OrderController@postSend','OrderController@getExport']);
		Session::put('index_url',URL::full());
		return view('home')->with($data)->withInput($request->flash());
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create(OrderField $fieldService)
	{
		$fieldService->currentRole($this->user->auth);
		$fieldService->currentStatus('');
		$data=[];
		$data['class']='order';
		$data['field']=$fieldService;
		$data['actions']=array_only($this->permission->get($this->user->auth),['OrderController@store']);
		$data['actions'][]='backpage';
		return view('create.order')->with($data);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store(Request $request,OrderField $fieldService)
	{
		//
		$fieldService->currentRole($this->user->auth);
		$fieldService->currentStatus('');
		$fields = $fieldService->parseValidator('add');
		$this->validate($request,$fields);

		if($this->service->create($request->all())){
			return redirect('order')->withSuccess('添加成功');
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
	public function show($id,OrderField $fieldService)
	{
		//
		
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id,OrderField $fieldService)
	{
		//
		$order = $this->service->listOne($id,['belongsToSupply']);
		$fieldService->currentRole($this->user->auth);
		$fieldService->currentStatus($order->status);
		$data['class']='order';
		$data['order']=$order;
		$data['field']=$fieldService;
		$data['actions']=array_only($this->permission->get($this->user->auth),['OrderController@update','OrderController@postReady','OrderController@postCancel','OrderController@postBackward','OrderController@postSend','OrderController@postFinish','OrderController@postCombine','OrderController@postUnbind']);
		$data['actions'][]='backpage';
		return view('detail.order')->with($data);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id,Request $request,OrderField $fieldService)
	{
		//
		$fieldService->currentRole($this->user->auth);
		$fieldService->currentStatus($request->input('status'));
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
		if(empty($arrId)){
			return redirect()->back();
		}
		$i=0;
		foreach ($arrId as $v) {
			if($this->service->delete($v)){
				$i++;
			}
		}
		return redirect()->back()->withSuccess('成功删除 '.$i.' 条数据');
	}

}
