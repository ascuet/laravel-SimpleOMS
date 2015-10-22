<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supply extends Model {
	use SoftDeletes;
	//
	protected $fillable=['name','slocation','saddress','is_self','supply'];
	protected $array=[
		'is_self'=>['供应商','自有'],
		'supply'=>[],
	];

	public function hasManyLog(){
		return $this->hasMany('App\UserLog','object_id','id');
	}
	public function statusType(){
		return [''=>''];
	}

	public function arrayField($name){
		if(!isset($this->array[$name]))return [];

		if(empty($this->array[$name])){
			$method = explode('_', $name)[0];
			switch ($method) {
				case 'supply':
					$list= Self::distinct()->lists('supply');
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
