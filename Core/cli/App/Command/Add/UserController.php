<?php

class UserController extends CommandController{
    public function __construct() {
        $this->type = 'add';
    }
    public function handle(){
        CliAuth::Validate();
        $this->getPrinter()->display_info("-> Info: Creating new User");
        $uName = $this->hasParam('user') ? $this->getParam('user') :  CliUtil::RegularInput('Enter UserName: ');
        CliExeption::TextIsEmpty($uName ,"\033[33m Name is Empty | \033[0m".'(!_!)'.PHP_EOL);

        $uPass  = $this->hasParam('password') ? $this->getParam('password') :  CliUtil::HiddenInput('Enter Password: ');
        CliExeption::TextIsEmpty($uPass ,"\033[33m Password is Empty | \033[0m".'(!_!)'.PHP_EOL);

        $confirm = CliUtil::HiddenInput('Confirm Password : ');
        
        CliExeption::TextMatch($uPass, $confirm, "\033[33m Password does not match | \033[0m".'(!_!)'.PHP_EOL);
                
        
        CliAuth::Register($uName, $uPass);
        $this->getPrinter()->display_success("-> Successful: User Created");
 
    }
    
    
}