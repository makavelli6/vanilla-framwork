<?php 

require_once __DIR__.'/Json.php';
require_once __DIR__.'/Logger.php';
/**
 * 
 */
class Helper 
{
	public static function LoadConfig($file)
	{
		if(!file_exists($file)){

			echo('//////////////////////////////////////');
			Log::Error("config file does not exist".PHP_EOL);
			echo('//////////////////////////////////////');
			echo ($file.PHP_EOL);
			echo "-------SOLUTION------".PHP_EOL.PHP_EOL;
			Log::Info("Run: php builder cofig:init ".PHP_EOL.PHP_EOL);
			echo "---------------------".PHP_EOL;
			die();

		}
		return Json::decode_file($file);
	}

	public static function SetConfig($file, $data)
	{
		Json::encode_file($file.'.conf', $data);
		if(file_exists($file.'.conf')){
			$name = explode('/', $file);
			echo $name[count($name) - 1]."config file created successfully";
		}
	}
}






 ?>