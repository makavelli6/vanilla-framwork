<?php

require_once __DIR__.'/bin/AsyncExecutor/AsyncExecutor.php';
require_once __DIR__.'/bin/AsyncExecutor/AsyncMultiProcess.php';
require_once __DIR__.'/bin/AsyncExecutor/AsyncProcess.php';
class AsyncTask{
    public static function BackGround($script , $params)
    {
        $async = new AsyncExecutor('/usr/bin/php');
        $pid = $async->runProcess($script , $params);
    }
    public static function Run($script , $params)
    {
        $async = new AsyncExecutor('/usr/bin/php');
        $pid = $async->runProcess($script , $params);
    }
    public static function RunMulti( $tasks , $forever = false)
    {
        $async = new AsyncExecutor('/usr/bin/php');
        $multiAsync	= new AsyncMultiProcess($async);

        foreach ($tasks as $task ) {
            $multiAsync->addProcess(new AsyncProcess($task['script'], $task['params']));
        }
        if($forever == true){
            $multiAsync->keepRunningProcesses(); 
        }
        

    }

}