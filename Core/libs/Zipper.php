<?php 
/**
 * 
 */
class Zipper 
{
	private $_files = array(), $_zip;
	public function __construct()
	{
		$this->_zip = new ZipArchive();

	}
	public function add($input)
	{
		if (is_array($input)) {
			$this->_files = array_merge($this->_files,$input);
		}else{
			$this->_files[]= $input;

		}
	}
	public function store($location = null)
	{
		if(count($this->_files) && $location){
	foreach ($this->_files as $index => $file) {
		if(!file_exists($file)){
			unset($this->_files[index]);
		}

		# open the  zip...
		#loop add file to the zip
		#close 
	}
	if ($this->_zip->open($location,file_exists($location) ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE)) {
		#ECHO ALL OK
		foreach ($this->_files as $file) {
			$this->_zip->addFile($file,$file);
			# code...
		}
		$this->zip->close();
	}

		}
	}
}


 ?>