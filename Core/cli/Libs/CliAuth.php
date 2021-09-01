<?php
class CliAuth{
    public static function Validate(){
        $userDB = self::LoadDB();
        if(!$userDB->IsEmpty()){
            echo "\033[33m ->Authenticate User \033[0m";


            $uName = CliUtil::RegularInput('Enter User Name: ');
            CliExeption::TextIsEmpty($uName ,"\033[31m ->Error : UserName Name was not set \033[0m".'(!_!)'.PHP_EOL);

            $uPass = CliUtil::HiddenInput('Enter Password : ');
            CliExeption::TextIsEmpty($uPass ,"\033[31m ->Error : Password is Empty \033[0m".'(!_!)'.PHP_EOL);
    
            $userInfo = json_decode($userDB->Get(self::getKey($uName)), true) ;
            CliExeption::TextIsEmpty($userInfo,"\033[33m Invalide User (User Does not exist | \033[0m".'(!_!)'.PHP_EOL);
            CliExeption::TextMatch($userInfo['Password'],  sha1($uPass), "\033[33m Password does not match | \033[0m".'(!_!)'.PHP_EOL);
            
            echo "\033[32m ->LogIn Successful:\033[0m Welcome Back". ucfirst( strtolower($uName)).PHP_EOL;
        }
    }
    public static function Register($uName , $uPass){
        $userDB = self::LoadDB();
        if($userDB->Exist(self::getKey($uName))){ CliExeption::Hundle("\033[33m Warning : User already exist | \033[0m".'(!_!)'.PHP_EOL); }
        $userDB->Put(self::getKey($uName), json_encode( array('UserName' => self::getKey($uName),'Password' => sha1($uPass)) ));
    }
    public function Update($uName , $uPass){
        $userDB = self::LoadDB();
        if(!$userDB->Exist(self::getKey($key))){ CliExeption::Hundle("\033[33m Warning : User does not exist | \033[0m".'(!_!)'.PHP_EOL);
        }
        $userDB->Put(self::getKey($key), json_encode( array('UserName' => self::getKey($key),'Password' => sha1($uPass)) ));
    }
    public function Delete($key){
        $userDB = self::LoadDB();
        if(!$userDB->Exist(self::getKey($key))){ CliExeption::Hundle("\033[33m Warning : User does not exist | \033[0m".'(!_!)'.PHP_EOL);}
        $userDB->Delete(self::getKey($key));
    }
    public function ListUsers(){}

    private static function getKey($key){
        return ucfirst( strtolower($key));

    }
    private static function LoadDB(){
        $db = Xdb::InitDB('Users');
        //echo( "\033[36m-------------USER LODDED DB--------------\033[0m\n\n");
        return $db;
    }
}