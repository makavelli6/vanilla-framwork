<?php



class CreateController extends CommandController
{
    public function __construct() {
        $this->type = 'migration';
    }
    public function handle()
    {
        CliAuth::Validate();
        $name = $this->hasParam('name') ? $this->getParam('name') :  CliUtil::RegularInput('Enter Migration Name: ');
        CliExeption::TextIsEmpty($name ,"\033[31m ->Error Found : Migration Name was not set 
        \n\033[36m -> Run the cmd below insted... \n\033[0m vanilla migration create name=yourMigrationName ".'(!_!)'.PHP_EOL);

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