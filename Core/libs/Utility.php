<?php 
/**
 * 
 */
class Utility {

    public static function redirect($location = null){
        if($location){
            echo "<script>window.location='{$location}'</script>";
            exit();
        }
    }
    public static function cache_redirect($location = null){
        if($location){
            echo "<script>window.location='{$location}'</script>";
            exit();
        }
    }
    public static function client_redirect($location = null){
        if($location){
            
            //$(location).attr('href', $location);
            echo '<script>$(location)'.".attr('href','$location');</script>";
            exit();
        }
    }
    public static function server_redirect($location = null){
        if($location){
            header('location: '.$location);
            exit();
        }
    }
    public static function _error($code, $massage)
    {
       self::redirect(URL.'errors/init/'.$code.'/'.$massage);
    }

    public static function formatBytes($size){
        $mod = 1024;
        $units = explode(' ', 'B KB MB GB TB PB');
        for ($i = 0; $size > $mod; $i++) {
            $size /= $mod;
        }
        return round($size, 2) . ' ' . $units[$i];
    }

    // ********** Format time ********** //
    public static function formatTime($milliseconds)
    {
        $seconds = floor($milliseconds / 1000);
        $minutes = floor($seconds / 60);
        $hours = floor($minutes / 60);
        $milliseconds = $milliseconds % 1000;
        $seconds = $seconds % 60;
        $minutes = $minutes % 60;

        $format = '%u:%02u:%02u';
        $time = sprintf($format, $hours, $minutes, $seconds);
        return rtrim($time, '0');
    }





	public static function encrypt($string) {
    	$output = false;
    	$encrypt_method = "AES-256-CBC";
    	$secret_key = ENC_KEY;
    	$secret_iv = ENC_KEY;
    	// hash
    	$key = hash('sha256', $secret_key);

    	// iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
	    $iv = substr(hash('sha256', $secret_iv), 0, 16);
	    $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
	    $output = base64_encode($output);
	    return $output;
    }

    public static function decrypt($string) {
        $output = false;
        $encrypt_method = "AES-256-CBC";
        $secret_key = ENC_KEY;
        $secret_iv = ENC_KEY;
        // hash
        $key = hash('sha256', $secret_key);

        // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
        $iv = substr(hash('sha256', $secret_iv), 0, 16);
        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
        return $output;
        }

    public static function xss_filter($value)
    {
       $antiXss = new AntiXSS();
       return $antiXss->xss_clean($value);

    }

}
    







 ?>