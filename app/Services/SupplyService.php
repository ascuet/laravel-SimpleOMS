<?php namespace App\Services;

use App\Supply;
use Validator;
use App\SupplyField;
class SupplyService extends BasicService{
	protected $class='App\Supply';

	public function __construct(SupplyField $fieldService){
		parent::__construct();
		$this->fieldService = $fieldService;
	}
}