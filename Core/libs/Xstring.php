<?php 
/**
 * 
 */
class Xstring
{
	public static function remove_tail($value ,$delimeter='',$wrap = false){
		$b = explode($delimeter, $value);
		array_pop($b);
		$value = implode($delimeter,$b);
		if($wrap = true){
			return  $value.$delimeter;
		}else{
			return $value.$delimeterl;
		}
	}
	





}


 ?>