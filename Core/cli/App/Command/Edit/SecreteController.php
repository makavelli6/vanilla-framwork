<?php

class SecreteController extends CommandController{
    public function __construct() {
        $this->type = 'edit';
    }

    public function handle()
    {
        CliAuth::Validate();
        $name = $this->hasParam('name') ? $this->getParam('name') :  CliUtil::RegularInput('Enter DataBase Name: ');
        CliExeption::TextIsEmpty($name ,"\033[31m ->Error Found : DataBase Name was not set 
        \n\033[36m -> Run the cmd below insted... \n\033[0m vanilla new Database name=yourDataBaseName ".'(!_!)'.PHP_EOL);
        
        
        $this->getPrinter()->display_success("->Success:\n Server Secrete was set Successfully");
        
    }

  

    
}