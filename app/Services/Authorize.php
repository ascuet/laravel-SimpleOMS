<?php namespace App\Services;
use Illuminate\Routing\Route;

class Authorize{

	protected $roles=array();

	public function __construct(){


	}


	/**
	 * check permission
	 * @param App\User $user
	 * @param Illuminate\Http\Request
	 * @return bool
	 */
	public function check($user,$request){

		return true;
	}

	/**
	 * set roles' permission
	 *
	 *
	 */
	protected function setRoles(){
		$actions=[
			'OrderController@Index'=>[0,1,2,3,4,5,6],
			'OrderController@'
		];
	}

}