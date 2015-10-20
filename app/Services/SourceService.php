<?php namespace App\Services;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Storage;
use Validator;
trait SourceService{
	/**
	 * 提供文件上传功能 
	*/
	protected $disk = 'local';
	/**
	 *上传路径
	 * @var string
	*/
	protected $folder;
	/**
	 *上传类型,用于检查有效性及保存路径
	 * @var array
	*/
	protected $valid;

	/**
	 * 检查文件是否符合要求
	 * @param array $data
	 * @return Array
	*/
	public function validator($data){
		return Validator::make($data,$this->valid);
	}

	/**
	 * 保存文件
	 * @param Symfony\Component\HttpFoundation\File\UploadedFile $file
	 * @return string|bool
	*/
	public function save(UploadedFile $file){
		$ext = $file->guessExtension()=='zip'?'docx':$file->guessExtension();
		$fileName=md5(time().$file->getClientOriginalName()).'.'.$ext;
		$file->move(storage_path().DIRECTORY_SEPARATOR.$this->folder,$fileName);

		return $fileName;

	}

	/**
	 * 检查文件是否存在
	 * @param string $fileName
	 * @return bool
	*/
	public function exists($fileName){
		$disk = Storage::disk($this->disk);
		return $disk->exists($this->folder.DIRECTORY_SEPARATOR.$fileName);

	}

	/**
	 * 获得文件下载信息
	 * @param array $data
	 * @return array 
	*/
	public function downloadFile($data){
		$fileName = $data['file_name'];
		$rtn = array();
		$disk = Storage::disk($this->disk);
		if(!$disk->exists($this->folder.DIRECTORY_SEPARATOR.$fileName)){
			return false;
		}
		$rtn['file_path']=storage_path().DIRECTORY_SEPARATOR.$this->folder.DIRECTORY_SEPARATOR.$fileName;
		$rtn['name']=date('YmdHis').'.'.explode('.', $fileName)[1];
		return $rtn;
	}
}