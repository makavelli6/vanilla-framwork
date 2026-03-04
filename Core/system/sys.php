<?php 

if(file_exists(ROOT.'vendor/autoload.php')){
	require ROOT.'vendor/autoload.php';	
}

// Global HTTP Response Headers
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');

// Handle CORS Preflight requests early
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit();
}

spl_autoload_register('api_auto_load');
function api_auto_load($className){
	$fullpath  = ROOT.CORE_LIB.$className.".php";

	if(!file_exists($fullpath  !== null ?  $fullpath:'')){
		$fullpath = ROOT.LIB.$className.'.php';
	}

	if(!file_exists($fullpath !== null ?  $fullpath:'')){
		$fullpath = ROOT.MIDDLEWARE.$className.'.php';
	}
	
	if(!file_exists($fullpath !== null ?  $fullpath:'')){
		$fullpath =  ROOT.MODEL.$className.'.php';
	}

    if(!file_exists($fullpath !== null ?  $fullpath:'')){
		$fullpath = ROOT . 'App/services/' . $className . '.php';
	}
	// if(!file_exists($fullpath)){
	// 	$path =LIB.'others/';
	// 	$fullpath = ''.$path.$className.'.php';
	// }
	if(!file_exists($fullpath !== null ?  $fullpath:'')){
		$fullpath = ROOT.UTIL.$className.'.php';
	}
	if(!file_exists($fullpath !== null ?  $fullpath:'')){
		echo $fullpath;
		return false;
	}
	require $fullpath;

}

// Initialize the Global ORM Database Connection
try {
    $globalDb = new Database(
        Config::get('DB_TYPE'),
        Config::get('DB_HOST'),
        Config::get('DB_NAME'),
        Config::get('DB_USER'),
        Config::get('DB_PASS')
    );
    Model::setConnection($globalDb);
} catch (Exception $e) {
    Logger::Error("Failed to initialize ORM Connection: " . $e->getMessage());
}

$bootstrap = new Bootstrap();
$bootstrap->init();

 ?>