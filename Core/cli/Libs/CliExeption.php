<?php

class CliExeption{
    public static function TextIsEmpty( $var, $msg)
    {
        if($var == '' || $var == null){
            die("$msg");
            throw new Exception();
        }
    }
    public static function TextMatch( $var,  $var2, $msg ){
        if($var != $var2){
            die("$msg");
            throw new Exception();
        }
    }
    public  static function Hundle($msg){
        die("$msg");
        throw new Exception();
    }
    public  static function InvalidInput($msg){
        die("$msg");
        throw new Exception();
    }
}