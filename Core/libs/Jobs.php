<?php

class Jobs {
    public static function isWin (){
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            return true;
        }
        return false;
    }
    public static function BackGround(){}
}