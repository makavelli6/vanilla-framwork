<?php
/**
 * Cookies management
 */
class Cookie{
    // Default configuration
    /**
     * Life time of the cookie in seconds
     * @var int
     */
    const DURATION	= 25920000;//60*60*24*30*12;
    const PATH		= '/';
    const DOMAIN	= null;
    const SECURE	= false;
    const HTTPONLY	= true;

    
    public static function get($name){
        if(isset($_COOKIE[$name]))
            return $_COOKIE[$name];
        return null;
    }
    public static function setCookie($name, $value=null, $duration=null, $domain=null, $path=null, $secure=null, $httponly=null){
        if(!isset($value))
            return self::removeCookie($name);
        if(!isset($duration))
            $duration = self::DURATION;
        if(!isset($path))
            $path = self::PATH;
        if(!isset($domain))
            $domain = self::DOMAIN;
        if(!isset($secure))
            $secure = self::SECURE;
        if(!isset($httponly))
            $httponly = self::HTTPONLY;

        // Expiration date from the life time in seconds
        if($duration==0)
            $expire = 0;
        else
            $expire = time()+((int) $duration);

        // The value must be a string
        $value = (string) $value;

        // Writes the cookie
        setcookie($name, $value, $expire, $path, $domain, $secure, $httponly);
        $_COOKIE[$name] = $value;
    }
    


    public static function delete($name){
        setcookie($name, null, time()-3600*30);
        unset($_COOKIE[$name]);
    }
    public static function exist($name){
        return (isset($_COOKIE[$name]));
    }

}