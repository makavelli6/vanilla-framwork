<?php
class Config {
    private static $settings = [];

    public static function set($key, $value) {
        self::$settings[$key] = $value;
    }

    public static function get($key, $default = null) {
        return isset(self::$settings[$key]) ? self::$settings[$key] : $default;
    }

    public static function has($key) {
        return isset(self::$settings[$key]);
    }
    
    public static function load(array $config) {
        self::$settings = array_merge(self::$settings, $config);
    }
}
?>
