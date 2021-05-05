<?php

spl_autoload_register('api_auto_load');
function api_auto_load($temp){
    $temp = strtolower($temp);
    $className = ucfirst($temp);
	echo 'checking';

	$fullpath  = __DIR__.'/App/Command/'.$className.".php";

	if(!file_exists($fullpath)){
		echo 'checking '.$fullpath.PHP_EOL;
		$fullpath = __DIR__.'/Libs/'.$className.'.php';
	}


	if(!file_exists($fullpath)){
		echo 'checking '.$fullpath.PHP_EOL;
		$fullpath = __DIR__.'/Util'.$className.'.php';
	}
	
	
	if(!file_exists($fullpath)){
		echo 'checking '.$fullpath.PHP_EOL;
		$fullpath = __DIR__.'/Temp'.$className.'.php';
	}
	if(!file_exists($fullpath)){
		echo $fullpath;
		return false;
	}
	require $fullpath;
	

}