<?php

class CliUtil{
    public static function HiddenInput( $prompt = "Enter Password: "){
        echo PHP_EOL.$prompt;
        echo "\033[30;40m";
        $pass = trim(fgets(STDIN));
        echo "\033[0m";
        return $pass;
    }

    public static function RegularInput( $prompt = "Enter Text: ")
    {
        echo PHP_EOL.$prompt;
        $text  = trim(fgets(STDIN));
        return $text;
    }
    public static function GetKey( $prompt = "Enter Text: "){
        echo PHP_EOL.$prompt;
        $text  = trim(fgets(STDIN));
        return $text;
    }
    public static function IsWin()
    {
        if(strtoupper(substr(PHP_OS, 0 , 3))){ return true; }
        return false;
    }

    public static function IsUnix()
    {
        if(PHP_OS_FAMILY === "L"){}
    }
    public static function ClearLine()
    {
        echo "\033[2k\r";
    }

    public static function ClearLines(int $var)
    {
        for ($i=0; $i < $var ; $i++) { 
            echo "\r"; 
        }
    }
    public static function Clear()
    {
        if(Utility::GetOS()== Utility::OS_WIN){ system('clear'); }
        elseif(Utility::GetOS()== Utility::OS_LINUX){ system('cls'); }
    }

}