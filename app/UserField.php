<?php namespace App;

use App\Services\FieldService;
use App\User;
class UserField extends FieldService {


	protected $model;
	//
	public function __construct(User $model){
		parent::__construct();
		$this->model = $model;
		$this->status = $model->statusType();
	}

	/**
	 * set Fields
	 *
	 *
	 */
	protected function setFields($method){
		switch ($method) {
			case 'select':
				$this->setFieldsBatch('select',['text'=>'fuzzy'],['uid']);
				$this->setFieldsBatch('select',['checkbox'=>''],['auth']);
				break;
			case 'data':
				# code...
				$this->setFieldsBatch('data',['string'=>''],['uid','created_at']);
				$this->setFieldsBatch('data',['array'=>''],['auth']);
				break;
			case 'add':
				# code...
				$this->setFieldsBatch('add',['text'=>'required|unique:users,uid,NULL,id,deleted_at,NULL,id'],['uid'],[],[0]);
				$this->setFieldsBatch('add',['select'=>'required'],['auth'],[],[0]);
				$this->setFieldsBatch('add',['password'=>'required|confirmed'],['password'],[],[0]);
				break;
			case 'edit':
				# code...
				$this->setFieldsBatch('edit',['text'=>'required|readonly'],['uid'],[],[0]);
				$this->setFieldsBatch('edit',['password'=>'confirmed'],['password'],[],[0]);
				$this->setFieldsBatch('edit',['select'=>'disabled'],['auth'],[],[0]);
				break;
			default:
				# code...
				break;
		}

	}
}
