<?php

spl_autoload_register('api_auto_load');

function api_auto_load($temp){
    $temp = strtolower($temp);
    $className = ucfirst($temp);
	//echo 'checking';

	$fullpath  = __DIR__.'/App/Command/'.$className.".php";


	
	if(!file_exists($fullpath !== null ?  $fullpath:'')){
		$fullpath = check_in_dir($className, __DIR__.'/App/Command/');
	}

	if(!file_exists($fullpath !== null ?  $fullpath:'')){
		$fullpath = __DIR__.'/Libs/'.$className.'.php';
	}


	if(!file_exists($fullpath !== null ?  $fullpath:'')){
		$fullpath = __DIR__.'/Util'.$className.'.php';
	}

	if(!file_exists($fullpath !== null ?  $fullpath:'')){
		$fullpath = __DIR__.'/../Libs/'.$className.'.php';
	}
	
	
	if(!file_exists($fullpath !== null ?  $fullpath:'')){
		$fullpath = __DIR__.'/Temp/'.$className.'.php';
	}
	if(!file_exists($fullpath !== null ?  $fullpath:'')){
		return false;
	}
	require_once $fullpath;
	

}
function check_in_dir($className,$path){
	$dir = new DirectoryIterator($path);
	foreach ($dir as $fileinfo) {
		if ($fileinfo->isDir() && !$fileinfo->isDot()) {
			$mypath  = $path.DIRECTORY_SEPARATOR.$fileinfo->getFilename().DIRECTORY_SEPARATOR.$className.".php";
			if(file_exists($mypath)){
				require_once($mypath);
				$instance = new $className();
				if(strtolower($fileinfo) == strtolower($instance->type) ){
					return $mypath;
					break;
				}	
			}
		}
	}

}