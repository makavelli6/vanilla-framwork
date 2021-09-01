<?php

class UserController extends CommandController{
    public function __construct() {
        $this->type = 'clear';
    }
    public function handle(){
        
        $this->getPrinter()->display_success("-> Successful: All Users Cleared");
 
    }
    
    
}