<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model {
	use SoftDeletes;

	//
	protected $fillable = ['oid','gid','gname','order_date','country','amount','sum','go_date','back_date','send_date','gmobile','address','memo','message','source','house','is_deliver'];

	protected $casts = [
		'is_important'=>'boolean'
	];
	protected $statusName=['0'=>'待处理','1'=>'待发货','2'=>'已发货','3'=>'已完成','-1'=>'已取消',''=>''];
	protected $array=[
		'belongsToSupply_supply'=>[],
		'is_deliver'=>['自取','快递'],
		'country'=>[],
		'source'=>['淘宝','天猫','线下','微信'],
		'status'=>['0'=>'待处理','1'=>'待发货','2'=>'已发货','3'=>'已完成','-1'=>'已取消']
	];
	protected $dates=['order_date','go_date','back_date','send_date','modified_at'];
	public function products(){
		return $this->belongsToMany('App\Product')->withPivot('return_at')->withTimestamps();
	}

	public function belongsToSupply(){

		return $this->belongsTo('App\Supply','house','id');
	}

	public function hasManyLog(){
		return $this->hasMany('App\UserLog','object_id','id');
	}
	public function statusName($status=null){
		if(is_null($status)){
			return $this->statusName[$this->status];
		}
		else{
			return $this->statusName[$status];
		}
	}
	public function statusType(){
		return array_keys($this->statusName);
	}

	public function arrayField($name){
		if(!isset($this->array[$name]))return [];

		if(empty($this->array[$name])){
			$method = explode('_', $name)[0];
			switch ($method) {
				case 'belongsToSupply':
					$fieldName = explode('_', $name)[1];
					$list= \App\Supply::distinct()->lists($fieldName);
					$rtn = array();
					foreach ($list as $key => $value) {
						$rtn[$value]=$value;
					}
					return $rtn;
					break;
				case 'country':
					$list= Self::distinct()->lists('country');
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

	public function products_pid(){
		return implode(',',$this->products->lists('pid'));
	}
}
