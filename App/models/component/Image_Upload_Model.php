<?php 

/**
 * 
 */
class Image_Upload_Model extends Model
{
	
	public function __construct()
	{
		parent::__construct();
	}

	public function Handler($value)
	{
		$result = array();
		$fileName= $file['name'];
		$fileTempName = $file['tmp_name'];
		$fileSize =$file['size'];
		$fileError = $file['error'];
		$fileType = $file['type'];
		$fileExt = explode('.',$fileName);
		$filActualExt = strtolower(end($fileExt));
		$allowed = array('png','jpg','jpeg');
		$coverdir = $dir;

		if(!in_array($filActualExt, $allowed)){
			$result['error'] = 'format  not supported';
			return $result;
		}
		if($fileError != 0){
			$result['error'] = 'there was  an error during  uploaded
			<br>please try  again  letter';
			return $result;
		}
		if ($fileSize >200000) {
			$result['error'] = 'the  file is  too big'.$fileSize;
			return $result;
		}

		if (!isset($result['error'])){
			$fileNameNew = uniqid('',true).'.'.$filActualExt;
			$fileDest = $coverdir.$fileNameNew; 
			move_uploaded_file($fileTempName, $fileDest);
			$result['coverdir'] = $fileDest;
			$result['success'] = 1;	
		}

		$hash =  md5_file($fileDest);
		$fileNameNew = $hash.'.'.$filActualExt;
		if(self::check_file($fileNameNew)!=0){
			File::delete_file($fileDest);
			$result['coverdir'] = $coverdir.$fileNameNew;
			return $result;
			
		}else{
			#new thumbnail($fileDest,250,250,"TS");
			$fileNameNew = $coverdir.$fileNameNew;
			File::rename_file($fileDest, $fileNameNew);
			$result['coverdir'] = $fileNameNew;
			return $result;
		}
	}

	public static function check_file($file)
	{
		if (file_exists($file)) {
			return 1;
		}else{
			return 0;
		}
	}

}




 ?>