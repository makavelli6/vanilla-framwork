<?php
require_once __DIR__.'/libs/logger.php';
require_once __DIR__.'/util/create_mig.php';

class HundleMigration{
    private $Base = '';
    public function __construct($base) {
        $this->Base = $base;
    }

    public function Create($name = '')
    {
        if($name =='' || $name == null){
            Logger::Error("Error:\n Invalid Migration Name \n Use Variable Naming Standards");
        }else{
            create_new_mig($this->Base,$name);
            Logger::Success("Success:\n this ".$name." Migration was created Successfully");
        }
    }
    public function Run(){

        Logger::Info("Running Migration ... \n Please Wait\n");
        require_once $this->Base.'/Core/libs/Database.php';
        require_once $this->Base.'/Core/libs/Helper.php';
        $config = Helper::LoadConfig($this->Base.'/App/config/db');
       
        $db = new Database($config['DB_TYPE'],$config['DB_HOST'],$config['DB_NAME'],$config['DB_USER'],$config['DB_PASS']);
        $db->applyMigration($this->Base);
        Logger::Success("Migration  Run Succesfully");
    }
    public function Refressh()
    {
        Logger::Info("Refresh Migration ... \n Please Wait");
        $db = new Database($config['DB_TYPE'],$config['DB_HOST'],$config['DB_NAME'],$config['DB_USER'],$config['DB_PASS']);
        $db->refreshMigration($this->Base);
        Logger::Success("Migration Refreshed Succesfully");
    }
    public function Clear(){
        Logger::Info("Clearing Migration ... \n Please Wait");
        $db = new Database($config['DB_TYPE'],$config['DB_HOST'],$config['DB_NAME'],$config['DB_USER'],$config['DB_PASS']);
        $db->clearMigration($this->Base);
        Logger::Success("Migration Cleared Succesfully");
    }

}



function initMigration($base,$count,$value)
{
    
    if($count == 2 || $count == 3){
        $cmd = explode(':', $value[1]);

        if($cmd[0] == 'migration' || $cmd[0] == 'migration'){
            $Instance = new HundleMigration($base);
            if($cmd[1] == 'Run' || $cmd[1] == 'run'){
                $Instance->Run();
             }elseif($cmd[1] == 'Refress' || $cmd[1] == 'refress'){
                 $Instance->Refressh();
             }elseif($cmd[1] == 'Create' || $cmd[1] == 'create'){
                if(isset($value[2])){
                    $name = $value[2];
                }else{
                    $name = '';
                }
                $Instance->Create($name);
             }elseif($cmd[1] == 'Clear' || $cmd[1] == 'clear'){
                 $Instance->Clear();  
             }else{
                 echo "Invalide Migration";
             }
        }
    }
}

?>