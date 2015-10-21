<?php namespace App\Services;

use App\UserLog;
use Validator;
use App\UserLogField;
class LogService extends BasicService{
	protected $class='App\UserLog';

	public function __construct(UserLogField $fieldService){
		parent::__construct();
		$this->fieldService = $fieldService;
	}
}