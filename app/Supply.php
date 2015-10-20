<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supply extends Model {
	use SoftDeletes;
	//
	protected $fillable=['name','slocation','saddress','is_self','supply'];


	public function hasManyLog(){
		return $this->hasMany('App\Userlog','object_id','id');
	}
	public function statusType(){
		return [];
	}
}
