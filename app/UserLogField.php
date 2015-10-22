<?php namespace App;

use App\Services\FieldService;
use App\UserLog;
class UserLogField extends FieldService {


	//
	
	protected $model;
	//
	public function __construct(UserLog $model){
		parent::__construct();
		$this->model = $model;
	}

	/**
	 * set Fields
	 *
	 *
	 */
	protected function setFields(){


	}
}
