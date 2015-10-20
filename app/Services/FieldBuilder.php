<?php namespace App\Services;
use Illuminate\Support\Collection;
class FieldBuilder{

	protected $name,$method,$type,$role,$status;

	protected $requireAttr = ['name','method','type'];


	/**
	 * 设置字段name值
	 * @param string $name
	 * @return App\Services\FieldBuilder
	 */
	public function setName($name){
		$this->name=$name;
		return $this;
	}

	/**
	 * 设置请求method
	 * @param string $name
	 * @return App\Services\FieldBuilder
	 */
	public function setMethod($name){
		$this->method=$name;
		return $this;
	}

	/**
	 * 设置字段类型
	 * @param array $arr
	 * @return App\Services\FieldBuilder
	 */
	public function setType($arr){
		$this->type = $arr;

		return $this;
	}

	/**
	 * 设置角色
	 * @param int $int
	 * @return App\Services\FieldBuilder
	 */
	public function setRole($int){
		$this->role=$int;
		return $this;
	}


	/**
	 * 设置状态
	 * @param int $int
	 * @return App\Services\FieldBuilder
	 */
	public function setStatus($int){
		$this->status=$int;
		return $this;
	}

	/**
	 * 有效性检查
	 *
	 * @return bool
	 */
	public function checkValid(){
		foreach ($this->requireAttr as $attr) {
			if(!isset($this->$attr)||empty($this->$attr)){
				return false;
			}
		}
		return true;

	}

	/**
	 * 生成collection
	 *
	 * @return Collection
	 */
	public function makeArray(){
		$arr = [
			'name'=>$this->name,
			'method'=>$this->method,
			'type'=>$this->type,
			'role'=>$this->role,
			'status'=>$this->status,
		];

		return $arr;
	}
}