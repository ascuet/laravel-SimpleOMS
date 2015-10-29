<?php namespace App\Http\Controllers;
use Auth;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\ProductField;
use App\Services\ProductService;
use Illuminate\Http\Request;
use App\Services\OrderService;
class ProductController extends Controller {
	protected $service,$user;
	protected $errorMessage = [];

	public function __construct(ProductService $service){
		$this->service = $service;
		$this->user = Auth::user();
	}

	/**
	 * 入库时获得相关信息(订单,订单所含设备)
	 *
	 *
	 */
	public function getEntry(Request $request ,ProductField $fieldService){
		$pid = $request->input('pid',null);
		if(is_null($pid)){
			return view('product-entry');			
		}
		$product = $this->service->fetchOne(['pid'=>$pid]);
		$order = $this->service->currentOrder($product);
		if(is_null($order)){
			return redirect()->back()->withErrors('未找到当前订单');
		}
		$products = $order->products()->with('belongsToSupply')->get();
		$data=[];
		$data['currentProduct']=$product;
		$data['actions']=['entryProduct','backpage'];
		$data['order']=$order;
		$data['products']=$products;
		return view('product-entry')->with($data)->withInput($request->flash());
	}

	/**
	 * 操作入库
	 *
	 *
	 */
	public function postEntry($id,OrderService $orderService){
		$rtn = $this->service->performEntry($id,$orderService);
		if(gettype($rtn)=='string'&&isset($this->errorMessage[$rtn])){
			return redirect()->back()->withErrors($this->errorMessage[$rtn]);
		}elseif($rtn===true){
			return redirect('product/entry')->withSuccess('入库成功');
		}
		else{
			return redirect()->back()->withErrors('发生未知错误');
		}
	}

	/**
	 * 显示 selecttable
	 *
	 *
	 */
	public function getSelecttable(Request $request, ProductField $fieldService){
		$arrRequest = $request->all();
		$fieldService->currentRole($this->user->auth);
		$fieldService->currentStatus(0);
		$data['title']='选择设备';
		$data['class']='product';
		$data['field']=$fieldService;
		$data['multi']=false;
		$data['data']=$this->service->lists($arrRequest,'',20);
		return view('partials.select-modal')->with($data)->withInput($request->flash());

	}
	/**
	 * selecttable 筛选
	 *
	 */
	public function postSelecttable(){

	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index(Request $request,ProductField $fieldService)
	{
		$arrRequest = $request->all();
		$status = isset($arrRequest['pstatus'])?$arrRequest['pstatus']:'';
		is_array($status)&&$status='';
		$fieldService->currentRole($this->user->auth);
		$fieldService->currentStatus($status===''?$status:intval($status));
		$data=array();
		$data['title']='设备';
		$data['stitle']=$fieldService->statusName($status);
		$data['class']='product';
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
	public function create(ProductField $fieldService)
	{
		$fieldService->currentRole($this->user->auth);
		$fieldService->currentStatus('');
		$data=[];
		$data['field']=$fieldService;
		$data['actions']=['submit','backpage'];
		return view('create.product')->with($data);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store(Request $request,ProductField $fieldService)
	{
		//
		$fields = $fieldService->parseValidator('add');
		$this->validate($request,$fields);

		if($this->service->create($request->all())){
			return redirect('product')->withSuccess('添加成功');
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
	public function edit($id,ProductField $fieldService)
	{
		//
		$product = $this->service->listOne($id);
		$fieldService->currentRole($this->user->auth);
		$fieldService->currentStatus($product->pstatus);
		$data['product']=$product;
		$data['field']=$fieldService;
		$data['actions']=['submit','backpage'];
		return view('detail.product')->with($data);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id,Request $request,ProductField $fieldService)
	{
		//
		$fieldService->currentRole($this->user->auth);
		$fieldService->currentStatus($request->input('pstatus'));
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
