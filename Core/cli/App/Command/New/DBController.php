<?php

class DBController extends CommandController
{
    public function __construct() {
        $this->type = 'new';
    }
    public function handle()
    {
        CliAuth::Validate();
        $name = $this->hasParam('name') ? $this->getParam('name') :  CliUtil::RegularInput('Enter DataBase Name: ');
        CliExeption::TextIsEmpty($name ,"\033[31m ->Error Found : DataBase Name was not set 
        \n\033[36m -> Run the cmd below insted... \n\033[0m vanilla new Database name=yourDataBaseName ".'(!_!)'.PHP_EOL);
        
        
        try{
            $config = LoadConfig($this->root_app.'/App/config/db.conf');
            $db = new Migration(DB_TYPE,DB_HOST,DB_NAME, DB_USER ,DB_PASS);
            $db->exec("CREATE DATABASE $name;") or $db->errorInfo();
        }catch(PDOExcption $e){
            die("\033[31m ->DB Error : $e->getMessage() \033[0m ");
        }
        $this->getPrinter()->display_success("->Success:\n ".$name." DataBase was created Successfully");
        
    }

  

    
}