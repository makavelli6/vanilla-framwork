<?php



class ClearController extends CommandController
{
    public function handle()
    {
        $this->getPrinter()->display_info("Clearing Migration ... \n Please Wait");
        require_once $this->root_app.'App/config/app.php';
        require_once $this->root_core.'Libs/Migration.php';

        $config = LoadConfig($this->root_app.'/App/config/db.conf');
        $db = new Migration(DB_TYPE,DB_HOST,DB_NAME, DB_USER ,DB_PASS);
        $db->clearMigration($this->root_app);
        $this->getPrinter()->display_success("-->Migrations Cleared Succesfully");
        
    }

    
}