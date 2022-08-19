<?php



class ServerController extends CommandController{

    public function __construct() {
        $this->type = 'init';
    }
    public function handle()
    {
        // $this->getPrinter()->display_info("Clearing Migration ... \n Please Wait");
        // require_once $this->root_app.'App/config/app.php';
        // require_once $this->root_core.'Libs/Migration.php';

        // $config = LoadConfig($this->root_app.'/App/config/db.conf');
        // $db = new Migration(DB_TYPE,DB_HOST,DB_NAME, DB_USER ,DB_PASS);
        // $db->clearMigration($this->root_app);
        // $this->getPrinter()->display_success("-->Migrations Cleared Succesfully");
        $this->getPrinter()->display_info("-> Info: Starting Sever ...");
        $port = $this->hasParam('port') ? $this->getParam('port') :  CliUtil::RegularInput('Enter Port: ');
        CliExeption::TextIsEmpty($port ,"\033[33m Name is Empty | \033[0m".'(!_!)'.PHP_EOL);
        
        echo($this->root_app);

        shell_exec('php -S localhost:8000  '.getcwd().'/Public/system.php');
        $this->getPrinter()->display_success("-> Successful: User Created");
        
    }

    
}

