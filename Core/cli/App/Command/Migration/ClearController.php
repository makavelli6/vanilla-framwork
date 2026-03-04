<?php



class ClearController extends CommandController
{
    public function __construct() {
        $this->type = 'migration';
    }
    public function handle()
    {
        $this->getPrinter()->display_info("Clearing Migration ... \n Please Wait");
        require_once $this->root_app.'App/config/app.php';
        require_once $this->root_core.'Libs/Migration.php';

        $db = new Migration(
            Config::get('DB_TYPE'),
            Config::get('DB_HOST'),
            Config::get('DB_NAME'),
            Config::get('DB_USER'),
            Config::get('DB_PASS')
        );
        $db->clearMigration($this->root_app);
        $this->getPrinter()->display_success("-->Migrations Cleared Succesfully");
        
    }

    
}