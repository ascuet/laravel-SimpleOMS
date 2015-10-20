<?php namespace App\Services;
use App\User;
use Illuminate\Support\Collection;
use Carbon\Carbon;
class FieldService{

	protected $fields=array();
	protected $builder;
	protected $status,$role,$currentRole,$currentStatus;
	protected $methodFields=array();
	public function __construct(){
		$this->fields = collect([]);
		$user = new User;
		$this->role = $user->roleType();
		$this->newBuilder();
	}

	/**
	 * 创建新的FieldBuilder实例
	 * 
	 * @return void
	 *
	 */
	protected function newBuilder(){
		$this->builder=new FieldBuilder;
	}


	/**
	 * 设置字段name值
	 * @param string $name
	 * @return App\Services\FieldBuilder
	 */
	protected function name($name){
		$this->builder->setName($name);
		return $this;
	}

	/**
	 * 设置请求method
	 * @param string $name
	 * @return App\Services\FieldBuilder
	 */
	protected function method($name){
		$this->builder->setMethod($name);
		return $this;
	}

	/**
	 * 设置字段类型
	 * @param array $arr
	 * @return App\Services\FieldBuilder
	 */
	protected function type($arr){
		$this->builder->setType($arr);
		return $this;
	}

	/**
	 * 设置角色
	 * @param int $int
	 * @return App\Services\FieldBuilder
	 */
	protected function role($int){
		$this->builder->setRole($int);
		return $this;
	}


	/**
	 * 设置状态
	 * @param int $int
	 * @return App\Services\FieldBuilder
	 */
	protected function status($int){
		$this->builder->setStatus($int);
		return $this;
	}



	/*
	 * 增加字段设置
	 * 
	 *
	 *
	*/

	protected function add(){
		if($this->builder->checkValid()){
			$col = $this->builder->makeArray();

			$this->fields[]=$col;
			$this->newBuilder();
		}

	}

	/**
	 * 获取当前fields
	 *
	 * return Collection
	 */
	public function get(){
		return collect($this->fields);
	}

	/**
	 * 设置/获取当前角色
	 * @param int $role
	 * @return int
	 */
	public function currentRole($role=null){
		if(is_null($role))return $this->currentRole;
		$this->currentRole=$role;
	}

	/**
	 * 设置/获取当前状态
	 * @param int $status
	 * @return int
	 */
	public function currentStatus($status=null){
		if(is_null($status))return $this->currentStatus;
		$this->currentStatus=$status===''?$status:intval($status);
	}
	/**
	 * 根据action获得field
	 * @param string $name
	 * @param int $role
	 * @param int $status
	 * @return array
	 */
	public function getFieldsByMethod($name,$role,$status=''){
		if(isset($this->methodFields[$name]))return $this->methodFields[$name];

		if($this->get()->where('method',$name)->isEmpty()){
			$this->setFields($name);			
		}
		$status=$status===''?$status:intval($status);
		$arrFields = $this->get()->where('method',$name)->where('role',$role)->where('status',$status)->toArray();
		$rtn=[];
		foreach ($arrFields as $arrField) {
			$rtn[$arrField['name']]=['type'=>$arrField['type']];
		}
		$this->methodFields[$name]=$rtn;

		return $rtn;
	}

	/**
	 * 批量设置Fields
	 * @param string $method
	 * @param string $type
	 * @param array $arrName
	 * @param array $arrStatus
	 * @param array $arrRole
	 * @return void
	 */
	protected function setFieldsBatch($method,$type,$arrName,$arrStatus=array(),$arrRole=array()){
		empty($arrStatus)&&$arrStatus = $this->status;
		empty($arrRole)&&$arrRole = $this->role;
		foreach ($arrName as $name) {
			foreach ($arrStatus as $status) {

				foreach ($arrRole as $role) {
					$this->method($method)->type($type)->name($name)->status($status)->role($role)->add();
				}

			}
		}
	}

	/**
	 * 创建验证规则
	 * @param string $method
	 * @return array
	 */
	public function parseValidator($method){

	}

	/**
	 * 按field渲染条件查询的html
	 * @param string $name
	 * @param string $label
	 * @return string
	 */
	public function selectFieldHTML($name,$label,$value=''){
		$methodFields = $this->getFieldsByMethod('select',$this->currentRole,$this->currentStatus);
		isset($methodFields[$name])&&$field = $methodFields[$name];
		if(!isset($field))return '';

		$html='';
		switch (key($field['type'])) {
			case 'date':
				$options = explode('|',current($field['type']));
				$dateFormat = 'yyyy-mm-dd';
				in_array('full', $options)&&$dateFormat='yyyy-mm-dd hh:ii';
				$html='<div class="input-daterange input-group datetimepicker">
	    		<input type="text" class="input-sm form-control" name="'.$name.'_start" data-date-format="'.$dateFormat.'" value="'.$value.'" />
	    		<span class="input-group-addon">到</span>
	    		<input type="text" class="input-sm form-control" name="'.$name.'_end" data-date-format="'.$dateFormat.'" value="'.$value.'" />
				</div>';
				$html = '<div class="col-sm-9">'.$html.'</div>';
				$html='<label class=" col-sm-2 col-sm-offset-1" for="'.$name.'">'.$label.'</label>'.$html;
				$html='<div class="form-group form-group-sm col-md-6">'.$html.'</div>';
				break;
			case 'checkbox':
				$array = $this->model->arrayField($name);
				foreach ($array as $key => $value) {
					$checked = $key===$value?'checked':'';
					$html.='<div class="checkbox-inline">
					  <label><input type="checkbox" name="'.$name.'" value="'.$key.'" '.$checked.'>' .$value.'</label>
					</div>';
				}
				$html = '<div class="col-sm-9">'.$html.'</div>';
				$html='<label class=" col-sm-2 col-sm-offset-1" for="'.$name.'">'.$label.'</label>'.$html;
				$html='<div class="form-group form-group-sm col-md-6">'.$html.'</div>';
				break;
			case 'select':
				$array = $this->model->arrayField($name);
				$html.='<option value="">未选择</option>';
				foreach ($array as $key => $value) {
					$selected = $key===$value?'selected':'';
					$html.='<option value="'.$key.'" '.$selected.'>'.$value.'</option>';
				}
				$html='<select class="form-control">'.$html.'</select>';
				$html = '<div class="col-sm-6">'.$html.'</div>';
				$html='<label class=" col-sm-2 col-sm-offset-1" for="'.$name.'">'.$label.'</label>'.$html;
				$html='<div class="form-group form-group-sm col-md-6">'.$html.'</div>';
				break;
			default:
				$html='	<input type="text" name="'.$name.'" class="form-control" value="'.$value.'">';
				$html = '<div class="col-sm-6">'.$html.'</div>';
				$html='<label class=" col-sm-2 col-sm-offset-1" for="'.$name.'">'.$label.'</label>'.$html;
				$html='<div class="form-group form-group-sm col-md-6">'.$html.'</div>';
				break;
		}
		
		echo $html;
	}

	/**
	 * 根据字段名渲染表头
	 * @param string $name
	 * @param string $head
	 * @return string
	 */
	public function tableHead($name,$head){
		$methodFields=$this->getFieldsByMethod('data',$this->currentRole,$this->currentStatus);
		//dd($methodFields);
		isset($methodFields[$name])&&$field = $methodFields[$name];
		if(!isset($field))return '';
		echo '<th>'.$head.'</th>';
	}

	/**
	 * 根据字段渲染表单元格
	 * @param string $name
	 * @param mixed $value
	 * @return string
	 */
	public function tableCell($name,$value){
		$methodFields=$this->getFieldsByMethod('data',$this->currentRole,$this->currentStatus);
		//dd($methodFields);
		isset($methodFields[$name])&&$field = $methodFields[$name];
		if(!isset($field))return '';
		switch (key($field['type'])) {
			case 'date':
				$options = explode('|',current($field['type']));
				!in_array('full', $options)&&$rtn=$value->$name->toDateString();
				break;
			case 'array':
				$array = $this->model->arrayField($name);
				$rtn = $array[$value->$name];
				break;
			default:
				$rtn = $value->$name;
				$method = explode('_', $name,2)[0];
				$fieldName = explode('_', $name,2)[1];
				if(method_exists($value, $method)){
					if(!empty($rtn)){
						$rtn = $value->{$method}()->first()->$fieldName;
					}else{
						$rtn = '未选择';
					}
				}
				break;
		}
		echo '<td>'.$rtn.'</td>';
	}

	/**
	 * 渲染detail字段
	 * @param string $method
	 * @param string $name
	 * @param string $label
	 * @param object $obj
	 * @return string
	 */
	protected function detailFieldHTML($method,$name,$label,$obj=''){
		$value = $obj===''?'':$obj->$name;

		$methodFields = $this->getFieldsByMethod($method,$this->currentRole,$this->currentStatus);
		//dd($this);
		isset($methodFields[$name])&&$field = $methodFields[$name];
		if(!isset($field))return '';

		$html='';
		$options = explode('|',current($field['type']));
		$readonly = in_array('readonly', $options)?'readonly':'';
		$required = in_array('required', $options)?'required':'';
		switch (key($field['type'])) {
			case 'date':
				$value = $value===''&&$required==='required'?Carbon::now():$value;
				$value = in_array('full', $options)&&$value!==''?$value->toDateTimeString():$value->toDateString();
				$dateFormat = in_array('full', $options)?'yyyy-mm-dd hh:ii':'yyyy-mm-dd';
				$html='<input type="text" class="input-sm form-control" name="'.$name.'" data-date-format="'.$dateFormat.'" value="'.$value.'" '.$readonly.' '.$required.' />';
				$html = '<div class="col-sm-6">'.$html.'</div>';
				$html='<label class=" col-sm-2 col-sm-offset-1" for="'.$name.'">'.$label.'</label>'.$html;
				$html='<div class="form-group form-group-sm col-md-6">'.$html.'</div>';
				break;
			case 'number':
				$html='<input type="number" class="input-sm form-control" name="'.$name.'" step="1"  value="'.$value.'" '.$readonly.' '.$required.'/>';
				$html = '<div class="col-sm-3">'.$html.'</div>';
				$html='<label class=" col-sm-2 col-sm-offset-1" for="'.$name.'">'.$label.'</label>'.$html;
				$html='<div class="form-group form-group-sm col-md-6">'.$html.'</div>';
				break;
			case 'decimal':
				$html='<input type="number" class="input-sm form-control" name="'.$name.'" step="0.01"  value="'.$value.'" '.$readonly.' '.$required.'/>';
				$html = '<div class="col-sm-3">'.$html.'</div>';
				$html='<label class=" col-sm-2 col-sm-offset-1" for="'.$name.'">'.$label.'</label>'.$html;
				$html='<div class="form-group form-group-sm col-md-6">'.$html.'</div>';
				break;
			case 'select':
				$array = $this->model->arrayField($name);
				if($required!==''){
					$html.='<option value="">未选择</option>';
				}
				foreach ($array as $key => $value) {
					$selected = $key===$value?'selected':'';
					$html.='<option value="'.$key.'" '.$selected.'>'.$value.'</option>';
				}
				$html='<select class="form-control" '.$readonly.'>'.$html.'</select>';
				$html = '<div class="col-sm-6">'.$html.'</div>';
				$html='<label class=" col-sm-2 col-sm-offset-1" for="'.$name.'">'.$label.'</label>'.$html;
				$html='<div class="form-group form-group-sm col-md-6">'.$html.'</div>';
				break;
			case 'radio':
				$array=$this->model->arrayField($name);
				foreach ($array as $key => $value) {
					$selected = $key===$value?'selected':'';
					$html.='<div class="radio-inline">
					  <label><input type="radio" name="'.$name.'" value="'.$key.'" '.$selected.'> '.$value.'</label>
					</div>';
				}
				$html = '<div class="col-sm-9">'.$html.'</div>';
				$html='<label class=" col-sm-2 col-sm-offset-1" for="'.$name.'">'.$label.'</label>'.$html;
				$html='<div class="form-group form-group-sm col-md-6">'.$html.'</div>';
				break;
			case 'textarea':
				$html='<textarea class="form-control" rows="3" '.$required.' '.$readonly.' >'.$value.'</textarea>';
				$html = '<div class="col-sm-9">'.$html.'</div>';
				$html='<label class=" col-sm-2 col-sm-offset-1" for="'.$name.'">'.$label.'</label>'.$html;
				$html='<div class="form-group col-md-6">'.$html.'</div>';
				break;
			case 'selecttable':
				$params = [];
				foreach ($options as $option ) {
					if (strpos($option, ':') !== false)
					{
						list($option, $set) = explode(':', $option, 2);

						$param[$option]=$set;
					}
				}
				$static='未选择';
				if(!empty($value)){
					list($relation,$attr)=explode('_', $param['related']);
					$static = $obj->{$relation}()->first()->$attr;
				}
				$html='<input type="text" class="form-control" value="'.$static.'" readonly >
				<span class="input-group-btn">
				<button type="button" class="btn btn-success" data-toggle="modal" data-table="'.$param['table'].'" data-field="'.$param['field'].'" data-target="#selecttableModal">选择</button>
				</span>
				<input type="hidden" name="'.$name.'" value="'.$value.'" '.$required.' >';
				$html = '<div class=" col-sm-5"><div class="input-group input-group-sm">'.$html.'</div></div>';
				$html='<label class=" col-sm-2 col-sm-offset-1" for="'.$name.'">'.$label.'</label>'.$html;
				$html='<div class="form-group form-group-sm col-md-6">'.$html.'</div>';
				break;
			case 'password':
				$html='	<input type="password" name="'.$name.'" class="form-control" value="'.$value.'" '.$required.' '.$readonly.'>';
				$html = '<div class="col-sm-6">'.$html.'</div>';
				$html='<label class=" col-sm-2 col-sm-offset-1" for="'.$name.'">'.$label.'</label>'.$html;
				$html='<div class="form-group form-group-sm col-md-6">'.$html.'</div>';
				break;
			default:
				$method = explode('_', $name,2)[0];
				if(method_exists($obj, $method)){
					$fieldName = explode('_', $name,2)[1];
					if(!empty($value)){
						$value = $obj->{$method}()->first()->$fieldName;
					}else{
						$value = '未选择';
					}
				}
				$html='	<input type="text" name="'.$name.'" class="form-control" value="'.$value.'" '.$required.' '.$readonly.'>';
				$html = '<div class="col-sm-6">'.$html.'</div>';
				$html='<label class=" col-sm-2 col-sm-offset-1" for="'.$name.'">'.$label.'</label>'.$html;
				$html='<div class="form-group form-group-sm col-md-6">'.$html.'</div>';
				break;
		}
		
		echo $html;
	}

	/** 
	 * 渲染add表单
	 * @param string $name
	 * @param string $label
	 * @return string
	 */
	public function addFieldHTML($name,$label){
		$this->detailFieldHTML('add',$name,$label);
	}

	/** 
	 * 渲染edit表单
	 * @param string $name
	 * @param string $label
	 * @param object $obj
	 * @return string
	 */
	public function editFieldHTML($name,$label,$obj){
		$this->detailFieldHTML('edit',$name,$label,$obj);
	}
}