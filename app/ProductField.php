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
				$this->setFieldsBatch('select',['text'=>'fuzzy'],['pid','country']);
				$this->setFieldsBatch('select',['checkbox'=>'row'],['belongsToSupply_name']);
				$this->setFieldsBatch('select',['checkbox'=>''],['pstatus'],['']);
				$this->setFieldsBatch('select',['hidden'=>''],['pstatus'],[0,1]);
				break;
			case 'data':
				$this->setFieldsBatch('data',['string'=>''],['pid','belongsToSupply_name','country']);
				$this->setFieldsBatch('data',['array'=>''],['pstatus'],['']);
				break;
			case 'add':
				$this->setFieldsBatch('add',['text'=>'required|unique:products,pid,NULL,id,deleted_at,NULL'],['pid'],[''],[0,4]);
				$this->setFieldsBatch('add',['text'=>'required'],['country'],[''],[0,4]);
				$this->setFieldsBatch('add',['text'=>''],['traffic'],[''],[0,4]);
				$this->setFieldsBatch('add',['selecttable'=>'table:supply|field:name|related:belongsToSupply_name|required|filter:is_self[]=1'],['house'],[''],[0,4]);
				$this->setFieldsBatch('add',['select'=>'required'],['pstatus'],[''],[0,4]);
				$this->setFieldsBatch('add',['textarea'=>''],['memo'],[''],[0,4]);	
				break;
			case 'edit':
				$this->setFieldsBatch('edit',['text'=>'required|readonly'],['pid'],[],[0,1,2,3,4,5]);
				$this->setFieldsBatch('edit',['text'=>'required'],['country'],[0,1],[0,4]);
				$this->setFieldsBatch('edit',['text'=>'required|readonly'],['country'],[],[1,2,3,5]);
				$this->setFieldsBatch('edit',['text'=>'required|readonly'],['country'],['out'],[0,4]);
				$this->setFieldsBatch('edit',['text'=>''],['traffic'],[0,1],[0,4]);	
				$this->setFieldsBatch('edit',['text'=>'readonly'],['traffic'],[],[1,2,3,5]);	
				$this->setFieldsBatch('edit',['text'=>'readonly'],['traffic'],['out'],[0,4]);
				$this->setFieldsBatch('edit',['selecttable'=>'table:supply|field:name|related:belongsToSupply_name|filter:is_self[]=1'],['house'],[0,1],[0,4]);
				$this->setFieldsBatch('edit',['text'=>'readonly'],['belongsToSupply_name'],['out'],[0,4]);
				$this->setFieldsBatch('edit',['text'=>'readonly'],['belongsToSupply_name'],[],[1,2,3,5]);
				$this->setFieldsBatch('edit',['select'=>'required'],['pstatus'],[0,1],[0,4]);
				$this->setFieldsBatch('edit',['text'=>'required|readonly'],['pstatus'],['out']);
				$this->setFieldsBatch('edit',['select'=>'disabled'],['pstatus'],[0,1],[1,2,3,5]);
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
	/**
	 * 设置/获取当前状态
	 * @param int $status
	 * @return int
	 */
	public function currentStatus($status=null){
		if(is_null($status))return $this->currentStatus;
		if(!in_array($status, $this->model->statusType())){

			$this->currentStatus='out';
		}
		else{
			$this->currentStatus=$status===''?$status:intval($status);
		}
	}
}
