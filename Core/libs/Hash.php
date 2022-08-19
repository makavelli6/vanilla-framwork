<?php 
/**
* @param string $algo the algorithm(md5,Sha1 , whrlpool,etc)
* @param string $data the data encode
* @param string $salt the  salt(shoild be same through out)
* @param string the harshe  daata
*/

class Hash{
	public static function create($algo,$data,$salt){
		$context=hash_init($algo,HASH_HMAC,$salt);
		hash_update($context, $data);
		return hash_final($context);
	}
	
}


 ?>