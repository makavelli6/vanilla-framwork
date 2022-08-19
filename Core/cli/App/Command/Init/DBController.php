<?php



class DBController extends CommandController{

    public function __construct() {
        $this->type = 'init';
    }
    public function handle()
    {

        $this->getPrinter()->display_info('-------------CONFIGERING DB--------------');
        require_once $this->root_app.'App/config/app.php';
        $config = LoadConfig($this->root_app.'/App/config/db.conf');
        $db = array("DB_TYPE"=>DB_TYPE, "DB_HOST"=>DB_HOST, 'DB_NAME'=>DB_NAME, 'DB_USER'=>DB_USER,'DB_PASS'=>DB_PASS);

        $dbtype = $this->hasParam('type') ? $this->getParam('type') :  CliUtil::RegularInput('Enter DataBase Type ('.DB_TYPE.'): ');
        if($dbtype != '' || $dbtype != null){ $db['DB_TYPE'] = $dbtype; }
        echo PHP_EOL.'DataBase Type is "'.$db['DB_TYPE'].'"'.PHP_EOL;

        $dbhost = $this->hasParam('host') ? $this->getParam('host') :  CliUtil::RegularInput('Enter DataBase Host ('.DB_HOST.'): ');
        if( $dbhost != '' || $dbhost != null){ $db['DB_HOST'] = $dbhost; }
        echo PHP_EOL.'DataBase User is "'.$db['DB_HOST'].'"'.PHP_EOL;

        $dbname = $this->hasParam('name') ? $this->getParam('name') :  CliUtil::RegularInput('Enter DataBase Name ('.DB_NAME.'): ');
        if($dbname != '' || $dbname != null){ $db['DB_NAME'] = $dbname; }
        echo PHP_EOL.'DataBase Type is "'.$db['DB_NAME'].'"'.PHP_EOL;

        $dbuser = $this->hasParam('user') ? $this->getParam('user') :  CliUtil::RegularInput('Enter DataBase User ('.DB_USER.'): ');
        if($dbuser != '' || $dbuser != null){ $db['DB_USER'] = $data; }
        echo PHP_EOL.'DataBase User is "'.$db['DB_USER'].'"'.PHP_EOL;

        $dbpass = $this->hasParam('password') ? $this->getParam('password') :  CliUtil::RegularInput('Enter DataBase Password (): ');
        //CliExeption::TextIsEmpty($dbpass,"\033[33m Password is Empty | \033[0m".'(!_!)'.PHP_EOL);
        $db['DB_PASS'] = $dbpass;
        echo PHP_EOL.'DataBase Password is "'.$db['DB_PASS'].'"'.PHP_EOL;

        Helper::SetConfig($this->root_app.'App/config/db', $db);
        
        $this->getPrinter()->display_success("-->DataBase Configured Succesfully");
    }

    
}