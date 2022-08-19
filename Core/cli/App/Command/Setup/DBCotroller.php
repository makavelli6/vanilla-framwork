<?php

class SytemCotroller extends CommandController{
    public function __construct() {
        $this->type = 'init';
    }
    public function handle(){
        $this->getPrinter()->display_info("-> Info: Starting Sever ...");
        $port = $this->hasParam('port') ? $this->getParam('port') :  CliUtil::RegularInput('Enter Port: ');
        CliExeption::TextIsEmpty($port ,"\033[33m Name is Empty | \033[0m".'(!_!)'.PHP_EOL);

        shell_exec('php -S localhost:8000');
 
    }
    
    
}