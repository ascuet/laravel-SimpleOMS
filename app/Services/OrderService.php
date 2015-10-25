<?php namespace App\Services;

use App\Order;
use Validator;
use App\OrderField;
use Excel;
use Carbon\Carbon;
use Log;
use Queue;
class OrderService extends BasicService{
	protected $class='App\Order';
	protected $importPath;
	protected $importField=[
			'oid','gid','sum','message','gname','address','gmobile','order_date','country','amount','memo','source'
			],
			$exportField=[
			'oid','gid','gname','gmobile','order_date','country','amount','sum','go_date','back_date','days','send_date','supply','house_name','address','is_deliver','memo','source','products'
			];
	public function __construct(OrderField $fieldService){
		parent::__construct();
		$this->fieldService = $fieldService;
		$this->importPath = storage_path('uploads/excel').DIRECTORY_SEPARATOR;
		$this->logAction['backward']=[
			'body'=>'回退 {object}, 操作原因: {reasons}',
			'reasons'=>''
		];
		$this->logAction['combine']=[
			'body'=>'分配设备 {products} 到 {object}',
			'products'=>''
		];
		$this->logAction['send']=[
			'body'=>'发货 {object}, 操作意见: {reasons}',
			'reasons'=>''
		];
		$this->logAction['prepare']=[
			'body'=>'将 {object} 转入待发货状态, 操作意见: {reasons}',
			'reasons'=>''
		];
		$this->logAction['cancel']=[
			'body'=>'取消 {object}, 原因: {reasons}',
			'reasons'=>''
		];
		$this->logAction['finish']=[
			'body'=>' 完成 {object}, 操作意见: {reasons}',
			'reasons'=>''
		];
	}

	/**
	 * 从上传的Excel中导入订单
	 * @param string $fileName
	 * @return string
	 */
	public function importOrders($fileName){
		$sheets = Excel::selectSheetsByIndex(0)->load($this->importPath.$fileName)->get();
		foreach ($sheets as $sheet) {
			foreach ($sheet as $row) {
				if(!$this->parseBeforeImport($row->toArray())){
					return ['errcode'=>'importError','msg'=>'订单号:'.$row[0]];
				}
				
			}
		}
		return true;
	}

	/**
	 * 导入单条订单
	 * @param array $data
	 * @return string|bool
	 */
	public function parseBeforeImport($data){
		if(empty(implode($data))){
			return true;
		}
		$import = array();
		$fields = $this->fieldService->getFieldsByMethod('import',$this->user->auth);
			foreach ($fields as $k => $v) {
				$options = explode('|',current($v['type']));
				$required = in_array('required', $options)?true:false;
				$index =array_search($k, $this->importField);
				if($index!==false){
					if($required&&empty($data[$index])){
						Log::info('importing error: '.$k.' is required',$data);
						return false;
					}
					$import[$k] = $data[$index];
				}
				else{
					return false;
				}
				
			}
		return $this->create($import);
	}

	/**
	 * 回退订单 已发货->待发货
	 * @param int $id
	 * @param string $reasons
	 * @param App\Services\ProductService $productService
	 * @return bool
	 */
	public function backwardOrder($id,$reasons,$productService){
		DB::beginTransaction();
		$order= $this->listOne($id);
		//回退条件(已发货)
		if($order->status!=2){
			Log::info($order->oid.' status='.$order->status);
			DB::rollback();
			return 'statusChanged';
		}
		//修改订单状态并入库所有设备
		$order->status = 1;
		$order->reasons = $reasons;
		$order->modified_at = Carbon::now();
		$this->backwardLog($order);
		$order->save();
		$products = $order->products()->get();
		if(!$products->isEmpty()){
			foreach ($products as $product) {
				$productService->productEntry($product->id,$order,'订单回退');
			}
		}
		DB::commit();
		return true;
	}

	/**
	 * 在待发货时关联订单与设备 
	 * @param int $order_id
	 * @param string $product_id
	 * @param App\Services\ProductService $productService
	 * @return bool
	 */
	public function combineProducts($order_id,$product_id,$productService){
		//条件(待发货)
		DB::beginTransaction();
		$order= $this->listOne($id);
		if($order->status!=1){
			Log::info($order->oid.' status='.$order->status);
			DB::rollback();
			return 'statusChanged';
		}
		if($order->belongsToSupply()->first()->is_self!=1){
			Log::info($order->oid.' supply not self');
			DB::rollback();
			return 'supplyInvalid';
		}
		//检查设备是否符合条件(在库,未分配)
		$arrProduct=explode(',', $product_id);

		if(!$productService->distributeProducts($arrProduct,$order)){
			DB::rollback();
			return 'productChanged';
		}
		$this->combineLog($order);
		DB::commit();
		return true;		
	}

	/**
	 * 发货
	 * @param int $id
	 * @param string $reasons
	 * @param App\Serivces\ProductService;
	 * @return bool|string
	 */
	public function sendOrder($id,$reasons,$productService){
		//发货条件(待发货,库存,设备数符合)
		DB::beginTransaction();
		$order = $this->listOne($id);
		if($order->status!=1){
			Log::info($order->oid.' status='.$order->status);
			DB::rollback();
			return 'statusChanged';
		}
		$supply = $order->belongsToSupply()->first();
		switch ($supply->is_self) {
			case 0://非自有
				if($order->is_deliver==1&&!empty($order->delivery_company)&&!empty($order->delivery_no)){
					continue;
				}
				elseif($order->is_deliver==0){
					continue;
				}
				else{
					Log::info($order->oid.' delivery invalid: no='.$order->delivery_no.' company='.$order->delivery_company);
					DB::rollback();
					return 'deliveryInvalid';
				}
				break;
			case 1://自有
				$products = $order->products()->get();
				if($order->is_deliver==1&&!empty($order->delivery_company)&&!empty($order->delivery_no)){
					if(!$products->isEmpty()&&$products->count()==$order->amount){
						foreach ($products as $product) {
								if(!$productService->sendProduct($product->id,$order)){
									DB::rollback();
									return 'productInvalid';
								}
							}	
					}
					else{
						return 'productInvalid';
					}
				}
				elseif($order->is_deliver==0){
					continue;
				}
				else{
					Log::info($order->oid.' delivery invalid: no='.$order->delivery_no.' company='.$order->delivery_company);
					DB::rollback();
					return 'deliveryInvalid';
				}
				break;
			default:
				return false;
				break;
		}
		$order->status=2;
		$order->reasons = $reasons;
		$order->modified_at = Carbon::now();
		$this->sendLog($order);
		$order->save();
		DB::commit();
		return true;
	}

	/**
	 * 转入待发货状态
	 * @param int $id
	 * @param string $reasons
	 * @return bool|string
	 */
	public function prepareOrder($id,$reasons){
		//条件(必填项)
		DB::beginTransaction();
		$order = $this->listOne($id);
		if($order->status!=0){
			Log::info($order->oid.' status='.$order->status);
			DB::rollback();
			return 'statusChanged';
		}
		$prepare = ['oid','gid','gname','order_date','country','amount','sum','days','go_date','back_date','gmobile','address','house','send_date','is_deliver'];
		foreach ($prepare as $field) {
			if($order->$field==''||is_null($order->$field)){
				Log::info($field.' not set, prepare failed');
				DB::rollback();
				return 'fieldNotSet';
			}
		}
		$order->status = 1;
		$order->reasons = $reasons;
		$order->modified_at = Carbon::now();
		$this->prepareLog($order);
		$order->save();
		DB::commit();
		return true;
	}

	/**
	 * 在待发货状态时取消订单
	 * @param int $id
	 * @param string $reasons
	 * @return bool|string
	 */
	public function cancelOrder($id,$reasons,$productService){
		//条件(待发货)
		DB::beginTransaction();
		$order = $this->listOne($id);
		if($order->status!=1){
			Log::info($order->oid.' status='.$order->status);
			DB::rollback();
			return 'statusChanged';
		}
		//取消设备关联
		$order->products()->detach();
		$order->status = -1;
		$order->reasons = $reasons;
		$order->modified_at = Carbon::now();
		$this->cancelLog($order);
		$order->save();
		DB::commit();
		return true;
	}

	/**
	 * 完成订单并入库所有设备
	 * @param int $id
	 * @param string $reasons
	 * @param App\Services\ProductService
	 * @return bool|string
	 */
	public function finishOrder($id,$reasons,$productService){
		//条件(已发货)
		DB::beginTransaction();
		$order = $this->listOne($id);
		if($order->status!=2){
			Log::info($order->oid.' status='.$order->status);
			DB::rollback();
			return 'statusChanged';
		}
		//入库所有设备
		$arrProduct = $order->products()->wherePivot('return_at',null)->get('id')->toArray();
		if($productService->batchProductEntry($arrProduct,$order)){
			Log::info($order->oid.' error when entry products');
			DB::rollback();
			return 'productError';
		}
		$order->status = 3;
		$order->reasons = $reasons;
		$order->modified_at = Carbon::now();
		$this->finishLog($order);
		$order->save();
		DB::commit();
		return true;
		

	}
	/**
	 * 检查是否订单所有设备均已入库, 若是, 则完成订单
	 * @param int $id
	 * @return bool
	 */
	public function attemptFinish($id){
		DB::beginTransaction();
		$order = $this->listOne($id);
		if($order->status!=2){
			Log::info($order->oid.' status='.$order->status);
			DB::rollback();
			return false;
		}
		$products = $order->products()->wherePivot('return_at',null)->get();
		if(!$products->isEmpty()){
			DB::rollback();
			return false;
		}
		$order->status = 3;
		$order->reasons = '所有设备入库';
		$order->modified_at = Carbon::now();
		$this->finishLog($order);
		$order->save();
		DB::commit();
		return true;
	}


	/**
	* List object
	* @param array $col
	* @param array $opt
	* @return object
	*/
	public function lists( $opt=array(),$col=array('*'),$page=false){
		$obj = $this->selectQuery($opt);
		if($page){
			return $obj->with('belongsToSupply')->latest('modified_at')->latest('order_date')->paginate($page);
		}
		return $obj->get($col);
	}
	/**
	 * 回退订单操作记录
	 * @param App\Order $order
	 * @return void
	 */
	public function backwardLog($order){
		$tpl = $this->logAction['backward'];
		$tpl['reasons']=$order->reasons;
		$tpl['log_at']=$order->modified_at;
		$this->appendLog($obj,$tpl,'backward');
	}

	/**
	 * 绑定设备记录
	 * @param App\Order $order
	 * @return void
	 */
	public function combineLog($order){
		$tpl = $this->logAction['combine'];
		$tpl['products']=$order->products()->get(['pid','id'])->toArray();
		$this->appendLog($obj,$tpl,'combine');
	}

	/**
	 * 发货记录
	 * @param App\Order $order
	 * @return void
	 */
	public function sendLog($order){
		$tpl = $this->logAction['send'];
		$tpl['reasons']=$order->reasons;
		$tpl['log_at']=$order->modified_at;
		$this->appendLog($obj,$tpl,'send');
	}

	/**
	 * 转入待发货记录
	 * @param App\Order $order
	 * @return void
	 */
	public function prepareLog($order){
		$tpl = $this->logAction['prepare'];
		$tpl['reasons']=$order->reasons;
		$tpl['log_at']=$order->modified_at;
		$this->appendLog($obj,$tpl,'prepare');
	}
	/**
	 * 取消记录
	 * @param App\Order $order
	 * @return void
	 */
	public function cancelLog($order){
		$tpl = $this->logAction['cancel'];
		$tpl['reasons']=$order->reasons;
		$tpl['log_at']=$order->modified_at;
		$this->appendLog($obj,$tpl,'cancel');
	}
	/**
	 * 完成记录
	 * @param App\Order $order
	 * @return void
	 */
	public function finishLog($order){
		$tpl = $this->logAction['finish'];
		$tpl['reasons']=$order->reasons;
		$tpl['log_at']=$order->modified_at;
		$this->appendLog($obj,$tpl,'finish');
	}
	public function checkStatus($obj,$data){
		if(is_array($data)&&isset($data['status'])){
			if($obj->status == $data['status']){
				return $data['status'];
			}
			else{
				return false;
			}
		}
		elseif(is_int($data)||is_string($data)){
			if($obj->status == $data){
				return $data;
			}
			else{
				return false;
			}
		}
		else{
			return true;
		}
	}
}