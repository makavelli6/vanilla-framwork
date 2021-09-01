<?php

class CliPrinter
{
    public function out($message)
    {
        echo $message;
    }

    public function newline()
    {
        $this->out(PHP_EOL);
    }

    public function display($message)
    {
        $this->newline();
        $this->out($message);
        $this->newline();
        $this->newline();
    }
    public function display_info( $msg )
    {
        $this->newline();
        echo "\033[36m $msg \033[0m";
        $this->newline();
    }

    public function display_error( $msg )
    {
        $this->newline();
        echo "\033[31m $msg \033[0m";
        $this->newline();
    }

    public function display_success( $msg )
    {
        $this->newline();
        echo "\033[32m $msg \033[0m\n";
        $this->newline();
    }

    public function display_warning( $msg )
    {
        $this->newline();
        echo "\033[33m$msg \033[0m";
        $this->newline();
    }
    public function display_rw($msg){
        echo ("$msg \r");
    }



}