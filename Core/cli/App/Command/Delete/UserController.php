<?php



class UserController extends CommandController{
    public function __construct() {
        $this->type = 'delete';
    }
    public function handle(){
        $uName = '';

        if($this->hasParam('user')){
            $uName = $this->getParam('user');
        }else{
            echo PHP_EOL.'Enter UserName : ';
            $uName = trim(fgets(STDIN, 1024));   
        }
        if($uName != '' || $uName != null){
            throw new Exception("\033[33m Name is Empty | \033[0m".'(!_!)'); die(); 
        }
            
        CliAuth::Validate();
        CliAuth::Delete($uName);
        $this->getPrinter()->display_success("-> Successful: $uName Deleted"); 
        
    }
}