<?php namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\SoftDeletes;
use Hash;
class User extends Model implements AuthenticatableContract, CanResetPasswordContract {
	//用户模型
	use Authenticatable, CanResetPassword,SoftDeletes;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'users';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['uid', 'password','auth'];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = ['password', 'remember_token'];

	protected $casts = [
		'logs'=>'array'
	];
	protected $array=[
		'auth'=>['管理员','柜台','订单','物流','库管','客服']
	];
	protected $roleName=['管理员','柜台','订单','物流','库管','客服'];

	public function roleName(){
		return $this->roleName[$this->auth];
	}

	public function roleType(){
		return array_keys($this->roleName);
	}

	public function hasManyLog(){
		return $this->hasMany('App\Userlog','object_id','id');
	}
	public function didManyLog(){
		return $this->hasMany('App\Userlog','user_id','id');
	}
	public function statusType(){
		return [''=>''];
	}
	public function setPasswordAttribute($value){
		$this->attributes['password']=Hash::make($value);
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
