<?php namespace App;

use App\Services\FieldService;
use App\Product;
class ProductField extends FieldService {


	protected $model;
	//
	public function __construct(Product $model){
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
				$this->setFieldsBatch('select',['text'=>''],['pid']);
				$this->setFieldsBatch('select',['checkbox'=>''],['belongsToSupply_name']);
				$this->setFieldsBatch('select',['checkbox'=>''],['pstatus'],['']);
				break;
			case 'data':
				$this->setFieldsBatch('data',['string'=>''],['pid','belongsToSupply_name']);
				$this->setFieldsBatch('data',['array'=>''],['pstatus'],['']);
				break;
			case 'add':
				$this->setFieldsBatch('add',['text'=>'required'],['pid'],[''],[0,4]);
				$this->setFieldsBatch('add',['text'=>''],['traffic'],[''],[0,4]);
				$this->setFieldsBatch('add',['selecttable'=>'table:supplies|field:name|related:belongsToSupply_name|required'],['house'],[''],[0,4]);
				$this->setFieldsBatch('add',['select'=>'required'],['pstatus'],[''],[0,4]);
				break;
			case 'edit':
				$this->setFieldsBatch('edit',['text'=>'required|readonly'],['pid'],[],[0,1,2,3,4,5]);
				$this->setFieldsBatch('edit',['text'=>''],['traffic'],[0,1],[0,4]);	
				$this->setFieldsBatch('edit',['text'=>'readonly'],['traffic'],[],[1,2,3,5]);	
				$this->setFieldsBatch('edit',['text'=>'readonly'],['traffic'],['out'],[0,4]);
				$this->setFieldsBatch('edit',['selecttable'=>'table:supplies|field:name|related:belongsToSupply_name'],['house'],[0,1],[0,4]);
				$this->setFieldsBatch('edit',['text'=>'readonly'],['belongsToSupply_name'],['out'],[0,4]);
				$this->setFieldsBatch('edit',['text'=>'readonly'],['belongsToSupply_name'],[],[1,2,3,5]);
				$this->setFieldsBatch('edit',['select'=>'required'],['pstatus'],[0,1],[0,4]);
				$this->setFieldsBatch('edit',['text'=>'required|readonly'],['pstatus'],['out'],[0,4]);
				$this->setFieldsBatch('edit',['select'=>'required|readonly'],['pstatus'],[],[0,1,2,3,4,5]);
				$this->setFieldsBatch('edit',['textarea'=>''],['memo'],[0,1],[0,4]);	
				$this->setFieldsBatch('edit',['textarea'=>'readonly'],['memo'],[],[1,2,3,5]);	
				$this->setFieldsBatch('edit',['textarea'=>'readonly'],['memo'],['out'],[0,4]);
				break;
			default:
				# code...
				break;
		}

	}

	public function statusName($status){
		return $this->model->statusName($status);
	}
}
