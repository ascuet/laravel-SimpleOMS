<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class UserLog extends Model {

	//
	protected $casts = [
		'actions'=>'array'
	];
	public $timestamps = false;
	protected $fillable=['user_id','operation','object','object_id','client_ip','actions','log_at'];
	protected $dates=['log_at'];

	public function belongsToUser(){

		return $this->belongsTo('App\User','user_id','id');
	}
}
