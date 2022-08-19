<?php

class UserController extends CommandController{
    public function __construct() {
        $this->type = 'edit';
    }
    public function handle(){
        $uName = '';
        $uPass = '';

        if($this->hasParam('user')){
            $uName = $this->getParam('user');
        }else{
            echo PHP_EOL.'Enter UserName : ';
            $uName = trim(fgets(STDIN, 1024));   
        }
        if($uName != '' || $uName != null){
            throw new Exception("\033[33m Name is Empty | \033[0m".'(!_!)'); die(); 
        }
        
        if($this->hasParam('password')){
            $uPass = $this->getParam('password');
        }else{
            echo PHP_EOL.'Enter Password : ';
            $uPass = trim(fgets(STDIN, 1024));   
        }
        if($uPass != '' || $uPass != null){
            throw new Exception("\033[33m Password is Empty | \033[0m".'(!_!)'); die(); 
        }
        echo PHP_EOL.'Confirm Password : ';
        $confirm = trim(fgets(STDIN, 1024));
        if($uPass != $confirm){
            throw new Exception("\033[33m Password does not match | \033[0m".'(!_!)'); die(); 
        }
                
        CliAuth::Validate();
        CliAuth::Update($uName, $uPass);
        $this->getPrinter()->display_success("-> Successful: User Updated");    
    }
}