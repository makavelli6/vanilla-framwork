<?php
class CliLogger{
    public static function info( $msg )
    {
        echo "\033[36m $msg \033[0m";
    }
    public static function error( $msg )
    {
        echo "\033[31m $msg \033[0m";
    }
    public static function success( $msg )
    {
        echo "\033[32m $msg \033[0m";
    }
    public static function warning( $msg )
    {
        echo "\033[33m$msg \033[0m";
    }
}