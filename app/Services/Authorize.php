<?php namespace App\Services;
use Illuminate\Routing\Route;

class Authorize{

	protected $permission=array();

	public function __construct(){


	}


	/**
	 * check permission
	 * @param App\User $user
	 * @param Illuminate\Http\Request
	 * @return bool
	 */
	public function check($user,$request){
		if(empty($this->permission)){
			$this->setRoles();
		}
		$userPermission = $this->permission[$user->auth];
		$currentAction=last(explode('\\',$request->route()->getActionName()));

		return in_array($currentAction, $userPermission);
	}

	/**
	 * set roles' permission
	 *
	 *
	 */
	protected function setRoles(){
		$actions=[
			'HomeController@postUpload'=>[0,1,2],
			'OrderController@getImport'=>[0,1,2],
			'OrderController@postImport'=>[0,1,2],
			'OrderController@getExport'=>[0,1,2,3],
			'OrderController@postReady'=>[0,1,2],
			'OrderController@postCombine'=>[0,1,3],
			'OrderController@postUnbind'=>[0,1,3],
			'OrderController@postCancel'=>[0,1,2],
			'OrderController@postSend'=>[0,1,3],
			'OrderController@postFinish'=>[0,1,3],
			'OrderController@postBackward'=>[0,1,2],
			'OrderController@postToggleStar'=>[0,1,2,3,4,5],
			'OrderController@index'=>[0,1,2,3,4,5],
			'OrderController@create'=>[0,1,2],
			'OrderController@store'=>[0,1,2],
			'OrderController@edit'=>[0,1,2,3,4,5],
			'OrderController@update'=>[0,1,2,3,4,5],
			'OrderController@destroy'=>[0,1,2],
			'SupplyController@getSelecttable'=>[0,1,2,3,4,5],
			'SupplyController@index'=>[0,1,2,3,4,5],
			'SupplyController@create'=>[0,4],
			'SupplyController@store'=>[0,4],
			'SupplyController@edit'=>[0,1,2,3,4,5],
			'SupplyController@update'=>[0,4],
			'SupplyController@destroy'=>[0,4],
			'ProductController@getEntry'=>[0,1,3],
			'ProductController@postEntry'=>[0,1,3],
			'ProductController@getSelecttable'=>[0,1,2,3,4,5],
			'ProductController@index'=>[0,1,2,3,4,5],
			'ProductController@create'=>[0,4],
			'ProductController@store'=>[0,4],
			'ProductController@edit'=>[0,1,2,3,4,5],
			'ProductController@update'=>[0,4],
			'ProductController@destroy'=>[0,4],
			'UserController@getUserLog'=>[0],
			'UserController@index'=>[0],
			'UserController@create'=>[0],
			'UserController@store'=>[0],
			'UserController@edit'=>[0],
			'UserController@update'=>[0],
			'UserController@destroy'=>[0],
		];
		foreach ($actions as $action =>$roles) {
			foreach ($roles as $role) {
				$this->permission[$role][]=$action;
			}
		}
	}
	public function get($auth){
		if(empty($this->permission)){
			$this->setRoles();
		}
		$userPermission= $this->permission[$auth];
		$rtn=[];
		foreach ($userPermission as $per) {
			$rtn[$per]=$per;
		}
		return $rtn;
	}
}