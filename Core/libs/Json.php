<?php 
require_once  __DIR__.'/Text.php';

/**
 * 
 */
class Json 
{
	public static function encode($value)
	{
		return json_encode($value);
	}

	public static function decode($value)
	{
		return json_decode($value);
	}
	public static function encode_file($file,$data)
	{
		if(!file_exists($file)){
			Text::create_file_plus($file);
		}
		Text::write_file_plus($file,json_encode($data));
	}
	public static function decode_file($file)
	{
		return json_decode(Text::read_file_plus($file), true);
	}


}




 ?>