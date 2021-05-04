<?php 

require_once 'path.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/Core/libs/Helper.php';

//PATHS
//DO NT fORGET BACKSLASH (/) after path
//define('URL','/AudioPlug/');
function LoadConfig($file){
	if(!file_exists($file)){
        Log::Error("config file does not exist".PHP_EOL.'<br>');
        Log::Info ($file);
        Log::Success( "-------SOLUTION------");
        Log::Success("Run: php builder cofig:init ");
        Log::Success("---------------------");
        die();

	}
	return Json::decode_file($file);
}

$db = LoadConfig(__DIR__.'/db.conf');

define('SITE','/');
define('URL','/'); 

define('DB_TYPE',$db['DB_TYPE']);
define('DB_HOST',$db['DB_HOST']);
define('DB_NAME',$db['DB_NAME']);
define('DB_USER',$db['DB_USER']);
define('DB_PASS',$db['DB_PASS']);



//if local host use
//define('SITE','http://localhost/simpleMVC/');
//define('URL','http://localhost/simpleMVC/'); 

define('ENC_KEY','CKXH2U9RPY3EFD70TLS1ZG4N8WQBOVI6AMJ5');




//constants
//cite wide  hash key
define('HASH_GENKEY', 'mamasaymamasiadmamagusa');
define('HASH_PASSKEY', 'mamasaymamasiadmamagusa');

//cite wide  hash key
define('YEAR',25920000 );//60*60*24*30*12;
define('MONTH',2592000 );//60*60*24*30;
define('WEEK', 604800);//60*60*24*7;
define('DAY',  86400);//60*60*24;
define('HOUR', 3600);//60*60;
define('MINUTE',60);//60;





 ?>