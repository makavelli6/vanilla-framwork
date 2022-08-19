<?php 

/**
 * 
 */
class Session
{
	public static function exists($key)
    {
        return (isset($_SESSION[$key])) ? true : false;
    }

	public static function init()
	{
		@session_start();
		@session_regenerate_id();
	}
	public static function set($key,$value)
	{

		$_SESSION[$key] = $value;
	}
	public static function get($key)
	{
		@session_start();
		if(isset($_SESSION[$key]))
		return $_SESSION[$key];
	}
	public static function del($key)
	{
		if (self::exists($key)) {
            unset($_SESSION[$key]);
        }
	}
	public static function destroy()
	{
		unset($_SESSION['loggedIn']);
		@session_destroy();
	}
	public static function flash($key, $val = '')
    {
        if (self::exists($key)) {
            $session = self::get($key);
            self::del($key);
            return $session;
        } else {
            self::set($key, $val);
        }
    }
}

 ?>