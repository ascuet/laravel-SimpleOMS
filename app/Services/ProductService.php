<?php namespace App\Services;

use App\Product;
use Validator;
use App\ProductField;
use Log;
use Carbon\Carbon;
use DB;
class ProductService extends BasicService{
	protected $class='App\Product';

	public function __construct(ProductField $fieldService){
		parent::__construct();
		$this->fieldService = $fieldService;
		$this->logAction['entry']=[
			'body'=>'入库 {object}, 相关订单 {order}, 原因 {reasons}',
			'order'=>'',
			'reasons'=>''
		];
		$this->logAction['send']=[
			'body'=>'出库 {object}, 相关订单 {order}, 原因 {reasons}',
			'order'=>'',
			'reasons'=>''
		];
	}
	
	/**
	 * 入库操作
	 * @param int $id
	 * @param App\Services\OrderService;
	 * @return string|bool
	 */
	public function performEntry($id,$orderService){
		DB::beginTransaction();
		$product =$this->listOne($id);
		//检查入库条件(出库)
		if($product->pstatus=='0'||$product->pstatus=='1'){
			DB::rollback();
			Log::info($product->pid.' already in');
			return 'productInvalid';
		}
		$order = $this->currentOrder($product);
		if(is_null($order)){
			DB::rollback();
			return 'orderNotFound';
		}
		$product->pstatus=0;
		$product->orders()->updateExistingPivot($order->id,['return_at'=>Carbon::now()]);
		$this->entryLog($product,$order);
		//入库设备
		$product->save();
		DB::commit();
		//检查订单是否已完成
		$orderService->attemptFinish($order->id);
		return true;
	}
	/**
	 * 获取设备当前订单
	 * @param App\Product $product
	 * @return App\Order
	 */
	public function currentOrder($product){
		if(is_null($product))return false;
		$order = $product->belongsToOrder()->where('status',2)->first();
		return $order;
	}
	/**
	 * 获取设备当前绑定订单
	 * @param App\Product $product
	 * @return App\Order
	 */
	public function currentBind($product){
		return $product->orders()->wherePivot('return_at',null)->where('status',1)->first();		
	}
	/**
	 * 设备入库
	 * @param int $id
	 * @param App\Order $order
	 * @return bool
	 */
	public function productEntry($id,$order,$reason=false){
		$product = $this->listOne($id);
		if(in_array($product->pstatus,[0,1])){
			Log::info($product->pid.' already in');
			return false;
		}
		$product->pstatus=0;
		$product->orders()->updateExistingPivot($order->id,['return_at'=>Carbon::now()]);
		if($reason){
			$this->entryLog($product,$order,$reason);
		}
		else{
			$this->entryLog($product,$order);	
		}

		return $product->save();
	}
	/**
	 * 批量入库设备
	 * @param array $ids
	 * @param App\Order $order
	 * @return void
	 */
	public function batchProductEntry($ids,$order){
		if(!is_array($ids)){
			return false;
		}
		foreach ($ids as $id) {
			if(!$this->productEntry($id,$order)){
				return false;
			}
		}

	}

	/**
	 * 设备分配
	 * @param array $ids
	 * @param App\Order $order
	 * @return bool
	 */
	public function distributeProducts($ids,$order){
		if(!is_array($ids)){
			return false;
		}
		foreach ($ids as $id) {
			$product= $this->listOne($id);
			//状态必须是在库
			if($product->pstatus!=0){
				Log::info($product->code.' not available');
				return false;
			}
			//当前没有分配给其他订单(return_at没有值)
			$distributions = $this->currentBind($product);
			if(!is_null($distributions)){
				Log::info($product->code.' has distributions',$distributions);
				return false;
			}		
		}
		$order->products()->sync($ids);
		return true;
	}

	/**
	 * 设备分配
	 * @param int $id
	 * @param App\Order $order
	 * @return bool
	 */
	public function combineProduct($id,$order){
		$product= $this->listOne($id);
		if(is_null($product))return false;
			//状态必须是在库
		if($product->pstatus!=0){
			Log::info($product->code.' not available');
			return false;
		}
		//当前没有分配给其他订单(return_at没有值)
		$distributions = $this->currentBind($product);
		if(!is_null($distributions)){
			Log::info($product->code.' has distributions',$distributions->toArray());
			return false;
		}		
		
		$order->products()->attach($id);
		return true;
	}

	
	/**
	 * 设备发货
	 * @param int $id
	 * @param App\Order $order
	 * @return bool
	 */
	public function sendProduct($id,$order){
		$product = $this->listOne($id);
		if($product->pstatus!=0){
			Log::info($product->code.' not available');
			return false;
		}
		$product->pstatus= $order->oid;
		$product->sent_at=Carbon::now();
		$product->orders()->updateExistingPivot($order->id,['return_at'=>NULL]);
		$this->sendLog($product,$order);
		return $product->save();

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
			if(isset($opt['pstatus'])&&$opt['pstatus']=='out'){
				return $obj->with(['belongsToSupply'=>function($query){$query->withTrashed();}])->latest('sent_at')->paginate($page);				
			}
			return $obj->with(['belongsToSupply'=>function($query){$query->withTrashed();}])->orderBy('pid')->paginate($page);
		}
		return $obj->get($col);
	}
	/**
	 * 入库记录
	 * @param App\Product $product
	 * @param App\Order $order
	 * @param string $reason
	 * @return void
	 */
	public function entryLog($obj,$order,$reasons='常规入库'){
		$tpl = $this->logAction['entry'];
		$tpl['reasons']=$reasons;
		$tpl['order']=['text'=>$order->oid,'id'=>$order->id];
		$this->appendLog($obj,$tpl,'entry');
	}

	/**
	 * 出库记录
	 * @param App\Product $product
	 * @param App\Order $order
	 * @param string $reason
	 * @return void
	 */
	public function sendLog($obj,$order,$reasons='常规出库'){
		$tpl = $this->logAction['send'];
		$tpl['reasons']=$reasons;
		$tpl['order']=['text'=>$order->oid,'id'=>$order->id];
		$this->appendLog($obj,$tpl,'send');
	}

	
}