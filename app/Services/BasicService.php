<?php namespace App\Services;
use Auth;
use Log;
use DB;
use Carbon\Carbon;
use Request;
use App\Commands\WriteLog;
use Queue;
 class BasicService{

	protected $fieldService,$user,
	$logAction=[
		'create'=>[
			'body'=>'创建 {object}'
		],
		'update'=>[
			'body'=>'修改 {object}, {dirty}',
			'dirty'=>''
		],
		'delete'=>[
			'body'=>'删除 {object}'
		],
	];

	public function __construct(){
		$this->user = Auth::user();
	}
	/**
	 * 基础操作类
	*/
	/**
	* Create object
	* @param array $data
	* @param bool $import
	* @return object|string
	*/
	public function create( $data){
		$class=new $this->class;
		$data = array_where($data,function($key,$value){
			return $value!=='';
		});
		$class->fill($data);
		
		if($class->save()){
			$this->createLogs($class);
			return $class;
		}
		else{
			Log::info('savingObjectError');
			return false;
		}
	}

	/**
	* Delete object
	* @param int $id
	* @return object|string
	*/
	public function delete($id){
		$class=$this->listOne($id);
		$this->deleteLogs($class);
		if(!$class){
			Log::info('savingLogError');
			return false;
		}
		return $class::where('id',$id)->delete();

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
			DB::commit();
			return $class;
		}

	}

	/**
	 * update object with array
	 * @param array $data
	 * @param Object $obj
	 * @return bool
	 */
	public function updateInstance($data,$obj){
		$role = $this->user->auth;
		$status = $this->checkStatus($obj,$data);
		if($status===false){
			Log::info( 'statusChanged');
			return false;
		}
		//获取可以更新的字段
		$fields = $this->fieldService->getFieldsByMethod('edit',$role,$status);
		//只更新允许/变化字段
		foreach ($fields as $k => $v) {
			$options  = explode('|',current($v['type']));
			$readonly = in_array('readonly', $options)?true:false;
			if($readonly){
				continue;
			}
			if(key($v['type'])=='password'){
				isset($data[$k])&&!empty($data[$k])&&$obj->$k=$data[$k];
			}
			else{
				isset($data[$k])&&$obj->$k=$data[$k];				
			}
			
		}

		$this->updateLogs($obj);
		if(!$obj){
			Log::info( 'savingLogError');
			return false;
		}
		if($obj->save()){
			return $obj;
		}
		else{
			return false;
		}
	}

	/**
	 * List one object for update
	 * @param int $id
	 * @return object
	 */
	public function listOne($id,$with=false){
		$class = new $this->class;
		$class = $class->where('id',$id);
		if(is_array($with)){
			foreach ($with as $w) {
				$class = $class->with([$w=>function($query){$query->withTrashed();}]);
			}
		}
		return $class->lockForUpdate()->first();
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
			return $obj->paginate($page);
		}
		return $obj->get($col);
	}

	/**
	 * build select query
	 * @param $opt
	 * @return query
	 *
	 */
	public function selectQuery($opt=array()){
		$obj= new $this->class;
		$status;
		$role = $this->user->auth;
		if(!empty($opt)){
			$fields = $this->fieldService->getFieldsByMethod('select',$role,$this->fieldService->currentStatus());
			foreach ($fields as $k => $v) {
				switch (key($v['type'])) {
					case 'checkbox':
						if(!isset($opt[$k])) continue;
						if(isset($opt[$k])&&empty($opt[$k])) continue;
						$method=explode('_',$k)[0];
						if(method_exists(new $this->class, $method)){
							$has = explode('_', $k,2)[1];
							$obj=$obj->whereHas($method,function($q)use ($has,$opt,$k){
								$q->whereIn($k,$opt[$k]);
							});
						}else{
							isset($opt[$k])&&!empty($opt[$k])&&$obj = $obj->whereIn($k,$opt[$k]);						
						}
							break;
					case 'select':
						if(!isset($opt[$k])) continue;
						if(isset($opt[$k])&&$opt[$k]=='') continue;
						$method=explode('_',$k)[0];
						if(method_exists(new $this->class, $method)){
							$has = explode('_', $k,2)[1];
							$obj = $obj->whereHas($method,function($q)use($has,$opt,$k){
								$q->where($has,$opt[$k]);
							});
						}else{
							isset($opt[$k])&&!empty($opt[$k])&&$obj = $obj->where($k,$opt[$k]);							
						}
					break;
					case 'date':
						$options  = explode('|',current($v['type']));
						if(in_array('fuzzy', $options)){
							if(isset($opt[$k.'_start'])&&!empty($opt[$k.'_start'])){
								$obj = $obj->where($k,'>=',$opt[$k.'_start']);
							}
							
							if(isset($opt[$k.'_end'])&&!empty($opt[$k.'_end'])){
								$obj = $obj->where($k,'<',$opt[$k.'_end']);
							}
							
						}
						else{
							$obj = $obj->where($k,$opt[$k]);
						}
						break;
					default:
						if(!isset($opt[$k])) continue;
						if(isset($opt[$k])&&$opt[$k]=='') continue;
						$options  = explode('|',current($v['type']));
						$method=explode('_',$k)[0];
						if(method_exists(new $this->class, $method)){
							$has = explode('_',$k)[1];
							if(!in_array('fuzzy', $options)){
								$obj = $obj->whereHas($method,function($q)use($has,$opt,$k){
									$q->where($has,$opt[$k]);
								});
							}
							else{
								$obj = $obj->whereHas($method,function($q)use($has,$opt,$k){
									$q->where($has,'like','%'.$opt[$k].'%');
								});
							}
						}
						else{
							if(!in_array('fuzzy', $options)){
								$obj = $obj->where($k,$opt[$k]);
							}
							else{
								$obj= $obj->where($k,'like','%'.$opt[$k].'%');

							}
						}
						break;
				}
			}
		}
		return $obj;
	}
	/**
	 * fetch one record by options
	 * @param array $opt
	 * @return object
	 *
	 */
	public function fetchOne($opt){
		$class = new $this->class;
		foreach ($opt as $key => $value) {
			$class=$class->where($key,$value);
		}
		return $class->first();
	}
	/**
	 * append create logs for object
	 * @param Object $obj
	 * @return void
	 */
	public function createLogs($obj){
		$tpl = $this->logAction['create'];
		$this->appendLog($obj,$tpl,'create');
	}

	/**
	 * append update logs 
	 * @param Object $obj
	 * @return Object|bool
	 */
	public function updateLogs($obj){
		$dirty = $obj->getDirty();
		if(empty($dirty)){
			return;
		}
		$tpl = $this->logAction['update'];
		$tpl['dirty']=$dirty;
		$this->appendLog($obj,$tpl,'update');
	}

	/**
	 * append delete logs
	 * @param Object $obj
	 * @return Object|bool
	 */
	public function deleteLogs($obj){
		$tpl = $this->logAction['delete'];
		$this->appendLog($obj,$tpl,'delete');

	}

	/**
	 * check object status
	 * @param Object $object 
	 * @param array $data
	 * @return bool|string
	 */
	public function checkStatus($object,$data){
		return '';
	} 

	/**
	 * append log
	 * @param Object $obj
	 * @param array $tpl
	 * @param string $operation
	 * @return void
	 */
	public function appendLog($obj,$tpl,$operation){
		$arr=[];
		switch ($obj->getTable()) {
			case 'orders':
				$tpl['object']='订单->'.$obj->oid;
				break;
			case 'products':
				# code...
				$tpl['object']='设备->'.$obj->pid;
				break;
			case 'supplies':
				# code...
				$tpl['object']='库存->'.$obj->name;
				break;
			case 'users':
				# code...
				$tpl['object']='用户->'.$obj->uid;
				break;
			default:
				return;
				break;
		}
		$arr['user_id']=$this->user->id;
		$arr['object']=str_singular($obj->getTable());
		$arr['object_id']=$obj->id;
		$arr['operation']=$operation;
		$arr['client_ip']=Request::ip();
		$arr['log_at']=isset($tpl['log_at'])?$tpl['log_at']:Carbon::now();
		unset($tpl['log_at']);
		$arr['actions']=$tpl;
		Queue::push(new WriteLog($arr));
	}
}