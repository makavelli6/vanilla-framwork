<?php 

require_once 'path.php';
require_once __DIR__.'/../../Core/libs/Helper.php';
require_once __DIR__.'/../../Core/libs/JSON.php';
require_once __DIR__.'/../../Core/libs/Text.php';



require_once __DIR__.'/../../Core/libs/Config.php';

//PATHS
function LoadConfig($file){
	if(!file_exists($file)){
        Log::Error("config file does not exist".PHP_EOL.'<br>');
        Log::Info ($file);
        Log::Success( "-------SOLUTION------");
        Log::Success("Run: php builder config:init ");
        Log::Success("---------------------");
        die();
	}
	return Json::decode_file($file);
}

$db = LoadConfig(__DIR__.'/db.conf');

// Load settings into Config registry and define globals for backward compatibility
define('SITE', $db['SITE'] ?? '/');
define('URL',  $db['URL'] ?? '/');

Config::load([
    // App Defaults
    'SITE' => '/',
    'URL'  => '/',
    
    // Database Settings
    'DB_TYPE' => $db['DB_TYPE'],
    'DB_HOST' => $db['DB_HOST'],
    'DB_NAME' => $db['DB_NAME'],
    'DB_USER' => $db['DB_USER'],
    'DB_PASS' => $db['DB_PASS'],
    
    // Mailer Settings
    'SMTP_Host' => '',
    'SMTP_Auth' => true,
    'SMTP_User' => '',
    'SMTP_Pass' => '',
    'SMTP_Port' => 100,
    
    // Security
    'ENC_KEY' => 'CKXH2U9RPY3EFD70TLS1ZG4N8WQBOVI6AMJ5',
    'HASH_GENKEY'  => 'mamasaymamasiadmamagusa',
    'HASH_PASSKEY' => 'mamasaymamasiadmamagusa'
]);

// Time Constants (Safe to keep global for general utility logic)
define('YEAR', 25920000); // 60*60*24*30*12
define('MONTH', 2592000); // 60*60*24*30
define('WEEK', 604800);   // 60*60*24*7
define('DAY',  86400);    // 60*60*24
define('HOUR', 3600);     // 60*60
define('MINUTE', 60);

?>