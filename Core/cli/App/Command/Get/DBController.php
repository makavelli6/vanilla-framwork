<?php



class DBController extends CommandController{

    public function __construct() {
        $this->type = 'get';
    }
    public function handle()
    {

        $this->getPrinter()->display_info('------------- DB Configaration --------------');
        require_once $this->root_app.'App/config/app.php';
        $config = LoadConfig($this->root_app.'/App/config/db.conf');

        
        echo PHP_EOL.'DataBase Type is "'.DB_TYPE.'"'.PHP_EOL;

        
        echo PHP_EOL.'DataBase User is "'.DB_HOST.'"'.PHP_EOL;

        
        echo PHP_EOL.'DataBase Type is "'.DB_NAME.'"'.PHP_EOL;

        
        echo PHP_EOL.'DataBase User is "'.DB_USER.'"'.PHP_EOL;



        require_once $this->root_core.'Libs/Helper.php';
        Helper::SetConfig($this->root_app.'App/config/db', $db);
        
        $this->getPrinter()->display_success("-->DataBase Configured Succesfully");
    }

    
}