<?php
require_once __DIR__.'/libs/logger.php';


class ConfigHundler{
    private $_ROOT = '';
    public function __construct($root) {
        $this->_ROOT = $root;
    }

    public  function InitCred(){
        $this->DBCread();
        # code...
    }
    public  function DBCread(){
        $db = array("DB_TYPE"=>'mysql', "DB_HOST"=>'', 'DB_NAME'=>'', 'DB_USER'=>'','DB_PASS'=>'');
        echo PHP_EOL.'-------------CONFIGERING DB--------------'.PHP_EOL;
        echo PHP_EOL.'Enter DataBase Type (mysql): ';
        //$db['DB_TYPE'] = trim(fgets(STDIN, 1024));
        echo PHP_EOL.'DataBase Type is "'.$db['DB_TYPE'].'"'.PHP_EOL;
        define('', '');

        echo PHP_EOL.'Enter DataBase Host (localhost): ';
        $db['DB_HOST'] = trim(fgets(STDIN, 1024));
        echo PHP_EOL.'DataBase User is "'.$db['DB_HOST'].'"'.PHP_EOL;

        

        echo PHP_EOL.'Enter DataBase User (root): ';
        $db['DB_USER'] = trim(fgets(STDIN, 1024));
        echo PHP_EOL.'DataBase User is "'.$db['DB_USER'].'"'.PHP_EOL;

        echo PHP_EOL.'Enter DataBase Password (): ';
        $db['DB_PASS'] = trim(fgets(STDIN, 1024));
        echo PHP_EOL.'DataBase Type is "'.$db['DB_PASS'].'"'.PHP_EOL;

       
        //add validation using preg
        echo PHP_EOL.'Enter DataBase Name (myDB): ';
        $db['DB_NAME'] = trim(fgets(STDIN, 1024));
        echo PHP_EOL.'DataBase Type is "'.$db['DB_NAME'].'"'.PHP_EOL;

        require_once $this->_ROOT.'/Core/libs/Helper.php';
        Helper::SetConfig($this->_ROOT.'/App/config/db', $db);
        # code...
    }
    

}  
function initConfig($root,$count,$value){
    
    if($count == 2 || $count == 3){
        $cmd = explode(':', $value[1]);

        if($cmd[0] == 'config' || $cmd[0] == 'Config'){
            $Instance = new ConfigHundler($root);
            if($cmd[1] == 'init' || $cmd[1] == 'Init'){
                $Instance->InitCred();
             }elseif($cmd[1] == 'db' || $cmd[1] == 'Db'){
                 $Instance->DBCread();
             }else{
                 echo "Invalide Config Command";
             }
        }
    }
}
