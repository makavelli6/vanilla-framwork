<?php

class AuthController extends CommandController{
    public function __construct() {
        $this->type = 'load';
    }
    public function handle(){
        $uName = $this->hasParam('user') ? $this->getParam('user') :  CliUtil::RegularInput('Enter UserName: ');
        
        $this->getPrinter()->display_success("-> Successful: User Created");
 
    }
    
    
}