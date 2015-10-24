<?php namespace App\Services;

class ExcelService{
	use SourceService;

	public function __construct(){
		$this->folder= 'uploads/excel';
		$this->valid = [
			'file'=>'required|mimes:xls,xlsx'
		];

	}

}