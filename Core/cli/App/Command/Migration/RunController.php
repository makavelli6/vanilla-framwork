<?php



class RunController extends CommandController
{
    public function handle()
    {
        $this->getPrinter()->display_info("Running Migration ... \n Please Wait");
        require_once $this->root_app.'App/config/app.php';
        require_once $this->root_core.'Libs/Migration.php';

        $config = LoadConfig($this->root_app.'/App/config/db.conf');
        $db = new Migration($config['DB_TYPE'],$config['DB_HOST'],$config['DB_NAME'],$config['DB_USER'],$config['DB_PASS']);
        $db->applyMigration($this->root_app);
        $this->getPrinter()->display_success("---Migration Run Succesfully---");
        
    }
}