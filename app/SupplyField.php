<?php namespace App;

use App\Services\FieldService;
use App\Supply;
class SupplyField extends FieldService {


	//
	
	protected $model;
	//
	public function __construct(Supply $model){
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
				$this->setFieldsBatch('select',['text'=>''],['name']);
				$this->setFieldsBatch('select',['checkbox'=>''],['is_self']);
				$this->setFieldsBatch('select',['select'=>''],['supply']);
				break;
			case 'data':
				$this->setFieldsBatch('data',['string'=>''],['name','supply','slocation']);
				$this->setFieldsBatch('data',['array'=>''],['is_self']);
				break;
			case 'add':
				# name slocation saddress is_self supply
				$this->setFieldsBatch('add',['text'=>'required'],['name'],[],[0,4]);
				$this->setFieldsBatch('add',['text'=>''],['slocation','saddress'],[],[0,4]);
				$this->setFieldsBatch('add',['text'=>'required|autocomplete'],['supply'],[],[0,4]);
				$this->setFieldsBatch('add',['select'=>'required'],['is_self'],[],[0,4]);
				break;
			case 'edit':
				# code...
				$this->setFieldsBatch('edit',['text'=>'required|readonly'],['name','supply']);
				$this->setFieldsBatch('edit',['text'=>''],['slocation','saddress'],[],[0,4]);
				$this->setFieldsBatch('edit',['text'=>'readonly'],['slocation','saddress'],[],[1,2,3,5]);
				$this->setFieldsBatch('edit',['select'=>'disabled'],['is_self']);
				break;
			default:
				# code...
				break;
		}

	}
}
