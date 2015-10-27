<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model {
	use SoftDeletes;

	//
	protected $fillable = ['pid','traffic','memo','house','pstatus','country'];

	protected $casts = [
		'logs'=>'array'
	];
	protected $statusName=['0'=>'在库','1'=>'禁用','out'=>'出库',''=>''];
	protected $dates=['sent_at'];
	protected $array=[
		'belongsToSupply_name'=>[],
		'pstatus'=>['0'=>'在库','1'=>'禁用'],
	];
	public function belongsToSupply(){

		return $this->belongsTo('App\Supply','house','id');
	}

	public function belongsToOrder(){
		return $this->belongsTo('App\Order','pstatus','oid');
	}

	public function orders(){
		return $this->belongsToMany('App\Order')->withPivot('return_at');
	}
	public function hasManyLog(){
		return $this->hasMany('App\UserLog','object_id','id');
	}

	public function statusType(){
		return array_keys($this->statusName);
	}
	public function statusName($status=null){
		if(is_null($status)){
			return $this->statusName[$this->status];
		}
		else{
			return $this->statusName[$status];
		}
	}
	public function arrayField($name){
		if(!isset($this->array[$name]))return [];

		if(empty($this->array[$name])){
			$method = explode('_', $name)[0];
			$fieldName = explode('_', $name)[1];
			switch ($method) {
				case 'belongsToSupply':
					$list= \App\Supply::distinct()->lists($fieldName);
					$rtn = array();
					foreach ($list as $key => $value) {
						$rtn[$value]=$value;
					}
					return $rtn;
					break;
				
				default:
					# code...
					break;
			}			
		}

		return $this->array[$name];
	}
}
