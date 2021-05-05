<?php


class CommandCall
{
    public $command;

    public $subcommand;

    public $args = [];

    public $params = [];

    public function __construct(array $argv)
    {
        $this->args = $argv;
        $this->command = isset($argv[1]) ? $argv[1] : null;
        $this->subcommand = isset($argv[2]) ? $argv[2] : 'default';

        $this->loadParams($argv);
    }

    protected function loadParams(array $args)
    {
        foreach ($args as $arg) {
            $pair = explode('=', $arg);
            if (count($pair) == 2) {
                $this->params[$pair[0]] = $pair[1];
            }
        }
    }

    public function hasParam($param)
    {
        return isset($this->params[$param]);
    }


    public function getParam($param)
    {
        return $this->hasParam($param) ? $this->params[$param] : null;
    }
}