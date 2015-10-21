<?php namespace App\Services;

use App\User;
use Validator;
use App\UserField;
class UserService extends BasicService{
	protected $class='App\User';

	public function __construct(UserField $fieldService){
		parent::__construct();
		$this->fieldService = $fieldService;
	}
}