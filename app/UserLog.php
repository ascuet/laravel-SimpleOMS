<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class UserLog extends Model {

	//
	protected $casts = [
		'actions'=>'array'
	];

	protected $dates=['log_at'];
}
