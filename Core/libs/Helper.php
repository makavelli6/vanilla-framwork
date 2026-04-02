<?php 


/**
 * 
 */
class Helper 
{
	public static function LoadConfig($file)
	{
		if(!file_exists($file)){
			Logger::Error("Configuration file does not exist: $file");
			Logger::Info("Please run: php vanilla config:init to generate the default configuration.");
			die();
		}
		return Json::decode_file($file);
	}

	public static function SetConfig($file, $data)
	{
		Json::encode_file($file.'.conf', $data);
		if(file_exists($file.'.conf')){
			$name = explode('/', $file);
			Logger::Success($name[count($name) - 1].".config file created successfully");
		}
	}
	public static function isWin (){
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            return true;
        }
        return false;
    }
}






 ?>