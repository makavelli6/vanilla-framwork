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

    public static function Cyan( $msg )
    {
        echo "\033[36m$msg \033[0m";
    }
    public static function Red( $msg )
    {
        echo "\033[31m$msg \033[0m";
    }
    public static function Green( $msg )
    {
        echo "\033[32m$msg \033[0m";
    }
    public static function Yellow( $msg )
    {
        echo "\033[33m$msg \033[0m";
    }
    public static function Purple( $msg )
    {
        echo "\033[35m$msg \033[0m";
    }
    public static function Blue( $msg )
    {
        echo "\033[34m$msg \033[0m";
    }

}