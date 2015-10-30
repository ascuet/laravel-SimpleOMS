<?php namespace App;

use App\Services\FieldService;
use App\Order;
class OrderField extends FieldService {
	protected $model;
	//
	public function __construct(Order $order){
		parent::__construct();
		$this->model = $order;
		$this->status = $order->statusType();
	}

	/**
	 * set Fields
	 *
	 *
	 */
	protected function setFields($method){
		switch ($method) {
			case 'select':
				//select
				$this->setFieldsBatch('select',['text'=>'fuzzy'],['oid','gid','gname','gmobile','country','belongsToSupply_name']);
				$this->setFieldsBatch('select',['date'=>'fuzzy'],['go_date','back_date']);
				$this->setFieldsBatch('select',['date'=>'fuzzy'],['send_date'],[0,1,-1,'']);
				$this->setFieldsBatch('select',['date'=>'fuzzy|full'],['order_date','modified_at']);
				$this->setFieldsBatch('select',['checkbox'=>''],['is_deliver','source']);
				$this->setFieldsBatch('select',['checkbox'=>''],['status'],['']);
				$this->setFieldsBatch('select',['hidden'=>''],['status'],[0,1,2,3,-1]);
				$this->setFieldsBatch('select',['select'=>''],['belongsToSupply_supply']);

				break;
			case 'data':
				$this->setFieldsBatch('data',['string'=>''],['gid','gname','country','amount','sum','days','belongsToSupply_supply','belongsToSupply_name']);
				$this->setFieldsBatch('data',['date'=>'full'],['order_date','modified_at']);
				$this->setFieldsBatch('data',['date'=>''],['go_date'],[0,1,2,3,'']);
				$this->setFieldsBatch('data',['date'=>''],['back_date'],[2,3]);
				$this->setFieldsBatch('data',['date'=>''],['send_date'],[0,1,'']);
				$this->setFieldsBatch('data',['array'=>''],['is_deliver','source']);
				$this->setFieldsBatch('data',['star'=>''],['is_important']);
				$this->setFieldsBatch('data',['array'=>''],['status'],['']);
				break;
			case 'add':
				$this->setFieldsBatch('add',['text'=>'required|unique:orders,oid,NULL,id,deleted_at,NULL'],['oid'],[''],[0,1,2]);
				$this->setFieldsBatch('add',['text'=>'required'],['country','gid','gname','gmobile','address'],[''],[0,1,2]);
				$this->setFieldsBatch('add',['textarea'=>'required'],['address'],[''],[0,1,2]);
				$this->setFieldsBatch('add',['date'=>'required|full'],['order_date'],[''],[0,1,2]);
				$this->setFieldsBatch('add',['number'=>'required'],['amount'],[''],[0,1,2]);
				$this->setFieldsBatch('add',['decimal'=>'required'],['sum'],[''],[0,1,2]);
				$this->setFieldsBatch('add',['date'=>'required|event:calculate_days'],['go_date','back_date'],[''],[0,1,2]);
				$this->setFieldsBatch('add',['date'=>'required|noDefault'],['send_date'],[''],[0,1,2]);
				$this->setFieldsBatch('add',['number'=>'required|event:calculate_days'],['days'],[''],[0,1,2]);
				$this->setFieldsBatch('add',['select'=>'readonly'],['belongsToSupply_supply'],[''],[0,1,2]);
				$this->setFieldsBatch('add',['selecttable'=>'table:supply|field:name|related:belongsToSupply_name|filter:belongsToSupply_supply'],['house'],[''],[0,1,2]);
				$this->setFieldsBatch('add',['radio'=>'required|event:change_deliver'],['is_deliver'],[''],[0,1,2]);
				$this->setFieldsBatch('add',['textarea'=>''],['memo','message'],[''],[0,1,2]);
				$this->setFieldsBatch('add',['select'=>'required'],['source'],[''],[0,1,2]);
				break;
			case 'edit':
				$this->setFieldsBatch('edit',['text'=>'required'],['oid','country','gid','gname','gmobile','address'],[0,1],[0,1,2]);
				$this->setFieldsBatch('edit',['textarea'=>'required'],['address'],[0,1],[0,1,2]);
				$this->setFieldsBatch('edit',['text'=>'required|readonly'],['oid','country','gid','gname','gmobile','address'],[2,3,-1],[0,1,2]);
				$this->setFieldsBatch('edit',['textarea'=>'required|readonly'],['address'],[2,3,-1],[0,1,2]);
				$this->setFieldsBatch('edit',['text'=>'required|readonly'],['oid','country','gid','gname','gmobile','address'],[0,1,2,3,-1],[3,4,5]);
				$this->setFieldsBatch('edit',['textarea'=>'required|readonly'],['address'],[0,1,2,3,-1],[3,4,5]);
				$this->setFieldsBatch('edit',['text'=>'form'],['delivery_no','delivery_company'],[1],[0,1,3]);
				$this->setFieldsBatch('edit',['text'=>'readonly'],['delivery_no','delivery_company'],[2,3],[0,1,3]);
				$this->setFieldsBatch('edit',['text'=>'readonly'],['delivery_no','delivery_company'],[1,2,3],[2,4,5]);
				$this->setFieldsBatch('edit',['date'=>'required|full|readonly'],['order_date'],[0,1,2,3,-1]);
				$this->setFieldsBatch('edit',['number'=>'required'],['amount'],[0,1],[0,1,2]);
				$this->setFieldsBatch('edit',['number'=>'required|readonly'],['amount'],[2,3,-1],[0,1,2]);
				$this->setFieldsBatch('edit',['number'=>'required|readonly'],['amount'],[0,1,2,3,-1],[3,4,5]);
				$this->setFieldsBatch('edit',['decimal'=>'required'],['sum'],[0,1],[0,1,2]);
				$this->setFieldsBatch('edit',['decimal'=>'required|readonly'],['sum'],[2,3,-1],[0,1,2]);
				$this->setFieldsBatch('edit',['decimal'=>'required|readonly'],['sum'],[0,1,2,3,-1],[3,4,5]);
				$this->setFieldsBatch('edit',['date'=>'required|event:calculate_days'],['go_date','back_date'],[0,1],[0,1,2]);
				$this->setFieldsBatch('edit',['date'=>'required|readonly'],['go_date','back_date'],[2,3,-1],[0,1,2]);
				$this->setFieldsBatch('edit',['date'=>'required|readonly'],['go_date','back_date'],[0,1,2,3,-1],[3,4,5]);
				$this->setFieldsBatch('edit',['date'=>'required|noDefault'],['send_date'],[0,1],[0,1,2]);
				$this->setFieldsBatch('edit',['date'=>'required|readonly'],['send_date'],[2,3,-1],[0,1,2]);
				$this->setFieldsBatch('edit',['date'=>'required|readonly'],['send_date'],[0,1,2,3,-1],[3,4,5]);
				$this->setFieldsBatch('edit',['number'=>'required|event:calculate_days'],['days'],[0,1],[0,1,2]);
				$this->setFieldsBatch('edit',['number'=>'required|readonly'],['days'],[2,3,-1],[0,1,2]);
				$this->setFieldsBatch('edit',['number'=>'required|readonly'],['days'],[0,1,2,3,-1],[3,4,5]);
				$this->setFieldsBatch('edit',['select'=>'readonly'],['belongsToSupply_supply'],[0,1],[0,1,2]);
				$this->setFieldsBatch('edit',['select'=>'readonly|disabled'],['belongsToSupply_supply'],[2,3,-1],[0,1,2]);
				$this->setFieldsBatch('edit',['select'=>'readonly|disabled'],['belongsToSupply_supply'],[0,1,2,3,-1],[3,4,5]);		
				$this->setFieldsBatch('edit',['selecttable'=>'table:supply|field:name|related:belongsToSupply_name|filter:belongsToSupply_supply'],['house'],[0,1],[0,1,2]);
				$this->setFieldsBatch('edit',['text'=>'readonly'],['belongsToSupply_name'],[2,3,-1],[0,1,2]);
				$this->setFieldsBatch('edit',['text'=>'readonly'],['belongsToSupply_name'],[0,1,2,3,-1],[3,4,5]);
				$this->setFieldsBatch('edit',['radio'=>'required|event:change_deliver'],['is_deliver'],[0,1],[0,1,2]);
				$this->setFieldsBatch('edit',['radio'=>'required|readonly'],['is_deliver'],[2,3,-1],[0,1,2]);
				$this->setFieldsBatch('edit',['radio'=>'required|readonly'],['is_deliver'],[0,1,2,3,-1],[3,4,5]);
				$this->setFieldsBatch('edit',['textarea'=>''],['memo','message'],[0,1,2,3,-1],[0,1,2,5]);
				$this->setFieldsBatch('edit',['textarea'=>'readonly'],['memo','message'],[0,1,2,3,-1],[3,4]);
				$this->setFieldsBatch('edit',['select'=>'required'],['source'],[0,1],[0,1,2]);
				$this->setFieldsBatch('edit',['select'=>'disabled'],['source'],[2,3,-1],[0,1,2]);
				$this->setFieldsBatch('edit',['select'=>'disabled'],['source'],[0,1,2,3,-1],[3,4,5]);
				$this->setFieldsBatch('edit',['hidden'=>''],['status']);
				break;
			case 'import':
				$this->setFieldsBatch('import',['text'=>'required|unique:orders,oid,NULL,id,deleted_at,NULL'],['oid'],[''],[0,1,2]);
				$this->setFieldsBatch('import',['string'=>'required'],['country','amount'],[''],[0,1,2]);
				$this->setFieldsBatch('import',['date'=>'required'],['order_date'],[''],[0,1,2]);
				$this->setFieldsBatch('import',['string'=>''],['gid','gname','sum','message','gmobile','address','memo','source'],[''],[0,1,2]);
				break;
			case 'export':
				$this->setFieldsBatch('export',['string'=>''],['oid','gid','gname','gmobile','country','amount','sum','days','belongsToSupply_name','belongsToSupply_supply','address','memo','message']);
				$this->setFieldsBatch('export',['date'=>'full'],['order_date']);
				$this->setFieldsBatch('export',['date'=>''],['go_date','back_date','send_date']);
				$this->setFieldsBatch('export',['array'=>''],['is_deliver','source','status']);
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
