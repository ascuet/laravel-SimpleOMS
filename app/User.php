<?php namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
class User extends Model implements AuthenticatableContract, CanResetPasswordContract {
	//用户模型
	use Authenticatable, CanResetPassword;

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

	public function statusType(){
		return [];
	}
}
