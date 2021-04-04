<?php

class Log{
    public static function Error($str)
    {
        echo "\033[31m$str \033[0m\n";
    }
    public static function Success($str)
    {
        echo "\033[32m$str \033[0m\n";
    }
    public static function Warning($str)
    {
        echo "\033[33m$str \033[0m\n";
    }
    
    public static function Info($str)
    {
        echo "\033[36m$str \033[0m\n";
    }
}
?>
