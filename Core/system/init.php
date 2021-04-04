<?php 

spl_autoload_register('api_auto_load');
function api_auto_load($className){
	$fullpath  = ROOT.CORE_LIB.$className.".php";

	if(!file_exists($fullpath)){
		$fullpath = ROOT.LIB.$className.'.php';
	}

	if(!file_exists($fullpath)){
		$fullpath = ROOT.MIDDLEWARE.$className.'.php';
	}
	
	if(!file_exists($fullpath)){
		$fullpath =  ROOT.MODEL.$className.'.php';
	}
	// if(!file_exists($fullpath)){
	// 	$path =LIB.'others/';
	// 	$fullpath = ''.$path.$className.'.php';
	// }
	if(!file_exists($fullpath)){
		$fullpath = ROOT.UTIL.$className.'.php';
	}
	if(!file_exists($fullpath)){
		echo $fullpath;
		return false;
	}
	require $fullpath;

}

$bootstrap = new Bootstrap();
$bootstrap->init();

 ?>