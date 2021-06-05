<?php



class CreateController extends CommandController
{
    public function handle()
    {
        $name = $this->hasParam('name') ? $this->getParam('name') : '--';

        if($name == '--'){
            $this->getPrinter()->display_error("->Error Found : Migration Name was not set");
            $this->getPrinter()->display_info("-> Run the cmd below ...");
            $this->getPrinter()->display(" vanilla migration create name=yourMigrationName");
            die();
            return;
        }
        require_once $this->root_core.'Libs/File.php';
        $fileName = '';
        $size = count(scandir($this->root_app.'Migrations'))-2;

        if($size < 10 ){
            $size =$size + 1;
            $fileName = 'm000'.$size.'_'.$name;
        }else if($size > 10 && $size < 100 ){
            $size =$size + 1;
            $fileName = 'm00'.$size.'_'.$name;
        }else if($size > 100 && $size < 1000 ){
            $size =$size + 1;
            $fileName = 'm0'.$size.'_'.$name; 
        }
        File::copy_file($this->root_core.'cli/App/Temp/migration_temp_alter.php',$this->root_app.'Migrations/',$fileName.'.php');
        File::replace_string_in_file($this->root_app.'Migrations/'.$fileName.'.php','tempName',$fileName);
        $this->getPrinter()->display_success("->Success:\n ".$name." Migration was created Successfully");
        
    }

    
}