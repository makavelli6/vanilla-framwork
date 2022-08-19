<?php

class RefreshController extends CommandController
{
    public function __construct() {
        $this->type = 'migration';
    }
    public function handle()
    {
        $this->getPrinter()->display_info("Refreshing Migration ... \n Please Wait");
        require_once $this->root_app.'App/config/app.php';
        require_once $this->root_core.'Libs/Migration.php';

        $db = new Migration(DB_TYPE,DB_HOST,DB_NAME,DB_USER,DB_PASS);
        $db->refreshMigration($this->root_app);
        $this->getPrinter()->display_success("-->Migration Refreshed Succesfully");        
    }
}