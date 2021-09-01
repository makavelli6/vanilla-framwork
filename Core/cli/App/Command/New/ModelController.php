<?php

class ModelController extends CommandController{
    public function __construct() {
        $this->type = 'new';
    }
    public function handle()
    {
        CliAuth::Validate();
        $name = $this->hasParam('name') ? $this->getParam('name') :  CliUtil::RegularInput('Enter Model Name: ');
        
        CliExeption::TextIsEmpty($name ,"\033[31m ->Error Found : Model Name was not set 
        \n\033[36m -> Run the cmd below insted... \n\033[0m vanilla new Model name=yourModelName ".'(!_!)'.PHP_EOL);
        $name = ucfirst(strtolower($name));
        $file = $name.'_model.php';
        
        File::copy_file($this->root_core.'cli/App/Temp/model_temp.php',$this->root_app.'App/models/',$file);
        File::replace_string_in_file($this->root_app.'App/models/'.$file,'tempName',$name);
        $this->getPrinter()->display_success("->Success:\n ".$name." Model was created Successfully");
        
    }

  

    
}