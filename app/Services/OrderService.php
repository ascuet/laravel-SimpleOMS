<?php namespace App\Services;

use App\Order;
use Validator;
use Illuminate\Validation\Validator as ValidatorClass;
use App\OrderField;
use Excel;
use Carbon\Carbon;
use Log;
use Queue;
use DB;
class OrderService extends BasicService{
	protected $class='App\Order';
	protected $importPath;
	protected $importField=[
			'oid','gid','sum','message','gname','address','gmobile','order_date','country','amount','memo','source'
			],
			$exportField=[
			'oid','gid','gname','gmobile','order_date','country','amount','sum','go_date','back_date','days','send_date','belongsToSupply_supply','belongsToSupply_name','address','is_deliver','memo','source','products_pid'
			];
	protected $fieldName=['oid'=>'订单号','gid'=>'淘宝ID','gname'=>'客户姓名','gmobile'=>'客户电话','order_date'=>'订单时间','country'=>'国家','amount'=>'数量','sum'=>'金额','go_date'=>'出国日期','back_date'=>'回国日期','days'=>'天数','send_date'=>'发货日期','belongsToSupply_supply'=>'供应商','belongsToSupply_name'=>'仓库名','address'=>'地址','is_deliver'=>'发货方式','memo'=>'买家留言','message'=>'客服备注','status'=>'订单状态','source'=>'来源','house'=>'库存','delivery_no'=>'快递单号','delivery_company'=>'快递公司','modified_at'=>'操作时间','reasons'=>'操作意见','is_important'=>'星标','products_pid'=>'相关设备'];
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
		$this->logAction['unbind']=[
			'body'=>'从 {object} 移除设备 {products}',
			'products'=>''
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
				$rtn = $this->parseBeforeImport($row->toArray());
				if($rtn instanceof ValidatorClass){
					return $rtn;
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
			$index =array_search($k, $this->importField);
			if($index!==false){
				$import[$k] = $data[$index];
			}		
		}
		$validator=Validator::make($import,$this->fieldService->parseValidator('import',$this->user->auth));
		if($validator->fails()){
			return $validator;
		}
		return $this->create($import);
	}
	/**
	 * 导出订单
	 * @param array $opt
	 * @return response
	 */
	public function exportOrder($opt){
		$obj = $this->selectQuery($opt);
		$orders = $obj->with(['belongsToSupply'=>function($query){$query->withTrashed();},'products'=>function($query){$query->withTrashed();}])->latest('order_date')->get();
		$fields = $this->fieldService->getFieldsByMethod('export',$this->user->auth,'');
	//	dd($fields);
		$export=[];
		$export[] =array_values(array_only($this->fieldName,$this->exportField));
		foreach ($orders as $order) {
			$row=[];
			foreach ($this->exportField as $key) {
				$options=[];
				switch (key($fields[$key]['type'])) {
				case 'array':
					if(is_null($order->$key)){
					$rtn = '';
						continue;
					}
					$array = $order->arrayField($key);
					$rtn = isset($array[$order->$key])?$array[$order->$key]:$order->$key;
					break;
				case 'date':
					if(is_null($order->$key)){
						$rtn = '';
						continue;
					}
					$options = explode('|', current($fields[$key]['type']));
					$full = in_array('full', $options)?true:false;
					$rtn = $full?$order->$key->toDatetimeString():$order->$key->toDateString();
					break;
				default:
					if($key=='products_pid'){
						$rtn = $order->products_pid();
					}
					else{
						$rtn = $order->$key;
						$method = explode('_', $key,2)[0];
						if(method_exists($order, $method)){
							$fieldName = explode('_', $key,2)[1];

							if(is_null($rtn)){
								$relation = $order->$method;
								if(is_null($relation)){
									$rtn = '';
								}
								else{
									$rtn = $relation->$fieldName;
								}

							}
						}
					}
						
					break;
				}
				$row[$key]=$rtn;
			}
			$export[]=$row;
		}
		return Excel::create(\Carbon\Carbon::now()->format('YmdHis'),function($excel) use ($export){
			$excel->sheet('导出数据',function($sheet)use ($export){
				$sheet->fromArray($export,'','A1',true,false);
			});
		})->download('xls');
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
	 * @param int $product_id
	 * @param App\Services\ProductService $productService
	 * @return bool
	 */
	public function combineProduct($order_id,$product_id,$productService){
		//条件(待发货)
		DB::beginTransaction();
		$order= $this->listOne($id);
		if($order->status!=1){
			Log::info($order->oid.' status='.$order->status);
			DB::rollback();
			return 'statusChanged';
		}
		if($order->belongsToSupply()->withTrashed()->first()->is_self!=1){
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
		$supply = $order->belongsToSupply()->withTrashed()->first();
		switch ($supply->is_self) {
			case 0://非自有
				$order->products()->detach();
				if($order->is_deliver==1&&!empty($order->delivery_company)&&!empty($order->delivery_no)){
					continue;
				}
				elseif($order->is_deliver==0){
					$order->delivery_company='';
					$order->delivery_no='';
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
					$order->delivery_company='';
					$order->delivery_no='';
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
		$prepare = [
		'oid'=>'required',
		'gid'=>'required',
		'gname'=>'required',
		'order_date'=>'required',
		'country'=>'required',
		'amount'=>'required|integer',
		'sum'=>'required|numeric',
		'days'=>'required|integer|min:1',
		'go_date'=>'required',
		'back_date'=>'required',
		'gmobile'=>'required',
		'address'=>'required',
		'house'=>'required|exists:supplies,id',
		'send_date'=>'required|after:'.\Carbon\Carbon::now()->addDays(-1)->toDateString(),
		'is_deliver'=>'required|in:0,1'];
		$validator=Validator::make($order->toArray(),$prepare);
		if($validator->fails()){
			return $validator;
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
		$arrProduct = $order->products()->wherePivot('return_at',null)->get()->lists('id');
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
	* Edit object
	* @param array $data
	* @param int $id
	* @return object|bool
	*/
	public function edit( $data,$id){
		//保持数据一致性
		DB::beginTransaction();
		$class = $this->listOne($id);

		if(!$class = $this->updateInstance($data,$class)){
			DB::rollback();
			return false;
		}
		else{
			if($class->status==1){
				if($class->belongsToSupply()->withTrashed()->first()->is_self!=1){
					$class->products()->detach();
				}
				if($class->is_deliver!=1){
					$class->delivery_no='';
					$class->delivery_company='';
					$class->save();
				}

			}
			DB::commit();
			return $class;
		}

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
			return $obj->with(['belongsToSupply'=>function($query){$query->withTrashed();}])->latest('modified_at')->latest('order_date')->paginate($page);
		}
		return $obj->get($col);
	}

	/**
	* Delete object
	* @param int $id
	* @return object|string
	*/
	public function delete($id){
		$class=$this->listOne($id);
		if($class->status!=0){
			return false;
		}
		$this->deleteLogs($class);
		if(!$class){
			Log::info('savingLogError');
			return false;
		}
		return $class::where('id',$id)->delete();

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
		$this->appendLog($order,$tpl,'backward');
	}

	/**
	 * 绑定设备记录
	 * @param App\Order $order
	 * @return void
	 */
	public function combineLog($order){
		$tpl = $this->logAction['combine'];
		$tpl['products']=$order->products()->get(['pid','product_id'])->toArray();
		$this->appendLog($order,$tpl,'combine');
	}
	/**
	 * 绑定设备记录
	 * @param App\Order $order
	 * @param App\Product $product
	 * @return void
	 */
	public function unbindLog($order,$product){
		$tpl = $this->logAction['unbind'];
		$tpl['products']=[['pid'=>$product->pid,'product_id'=>$product->id]];
		$this->appendLog($order,$tpl,'unbind');
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
		$this->appendLog($order,$tpl,'send');
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
		$this->appendLog($order,$tpl,'prepare');
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
		$this->appendLog($order,$tpl,'cancel');
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
		$this->appendLog($order,$tpl,'finish');
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


	public function migrate(){
		$this->migrateSupplies();
		$this->migrateProducts();
		$this->migrateOrders();
		$this->migrateOrderProduct();
	}
	public function migrateSupplies(){
		$supplies = DB::table('mht_supply')->get();
		$i = 0;
		foreach ($supplies as $supply) {
			$insert = new \App\Supply;
			$insert->fill((array)$supply);
			$insert->id = $supply->id;
			if($supply->recycled==1){
				$insert->deleted_at = Carbon::now();
			}
			$insert->save();
			$i++;
		}
		echo $i." supplies migrated\n";
	}

	public function migrateProducts(){
		$products = DB::table('mht_products')->get();
		$i = 0;
		foreach ($products as $product) {
			$insert = new \App\Product;
			$insert->fill((array)$product);
			$insert->id = $product->id;
			if($product->recycled==1){
				$insert->deleted_at = Carbon::now();
			}
			$insert->save();
			$i++;
		}
		echo $i." products migrated\n";
	}

	public function migrateOrders(){
		$i = 0;
		DB::table('mht_order_view')->where('recycled',0)->chunk(100,function($orders) use (&$i){
			foreach ($orders as $order) {
				$insert = new Order;
				$insert->id = $order->id;
				$insert->oid = $order->oid;
				$insert->gid =$order->gid;
				$insert->gname = $order->gname;
				$insert->country = $order->country;
				$insert->amount = $order->amount;
				$insert->sum = $order->sum;
				$insert->days = $order->days;
				$insert->go_date = empty($order->go_date)?null:Carbon::createFromTimestamp($order->go_date);
				$insert->back_date = empty($order->back_date)?null:Carbon::createFromTimestamp($order->back_date);
				$insert->order_date=empty($order->order_date)?$insert->go_date:Carbon::createFromTimestamp($order->order_date);
				$insert->gmobile = $order->gmobile;
				$insert->address = $order->address;
				$insert->memo = $order->memo;
				$insert->message = $order->message;
				$insert->status = $order->status;
				$insert->source = $order->source;
				$insert->house = $order->house;	
				$insert->send_date = empty($order->send_date)?null:Carbon::createFromTimestamp($order->send_date);
				$insert->is_deliver = $order->is_deliver;
				if($order->is_deliver==1){
					$delivery = DB::table('mht_deliver')->find($order->id);
					if(!is_null($delivery)){
						$insert->delivery_no = $delivery->tracking;
						$insert->delivery_company = $delivery->company;
					}
				}
				switch ($order->status) {
					case -1:
						$insert->modified_at = empty($order->cancel_date)?Carbon::createFromTimestamp($order->order_date):Carbon::createFromTimestamp($order->cancel_date);
						$insert->reasons = $order->reasons;
						break;
					case 0:
						# code...
						break;
					case 1:
						$insert->modified_at =Carbon::createFromTimestamp($order->order_date);
						break;
					case 2:
						$insert->modified_at =Carbon::createFromTimestamp($order->send_date);
						break;
					case 3:
						is_null($insert->go_date)&&$insert->go_date = $insert->order_date;
						is_null($insert->send_date)&&$insert->send_date = $insert->go_date;
						$insert->modified_at = empty($order->finished_date)?$insert->back_date:Carbon::createFromTimestamp($order->finished_date);
						break;
					default:
						# code...
						break;
				}
				if($order->recycled==1){
					$insert->deleted_at=Carbon::now();
				}
				$insert->save();
			$i++;
			}
			echo $i." orders migrated\n";
		});

	}

	public function migrateOrderProduct(){
		$order_products = DB::table('mht_order_products')->get();
		$i = 0;
		foreach ($order_products as $order_product) {
			$insert = [];
			$insert['order_id']=$order_product->order_id;
			$insert['product_id']=$order_product->products_id;
			$insert['return_at']=empty($order_product->return_date)?null:Carbon::createFromTimestamp($order_product->return_date);
			$insert['created_at']=Carbon::now();
			$insert['updated_at']=Carbon::now();
			DB::table('order_product')->insert($insert);
			$i++;
		}
		echo $i." order_product migrated\n";
	}

}