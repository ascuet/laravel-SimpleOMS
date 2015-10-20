<?php namespace App\Services;

class ExcelService{
	use SourceService;

	public function __construct(){
		$this->folder= 'uploads/excel';
		$this->valid = [
			'files'=>'required|mimes:xls,xlsx'
		];

	}

}