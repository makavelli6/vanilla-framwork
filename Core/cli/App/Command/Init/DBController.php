<?php



class DBController extends CommandController
{
    public function handle()
    {

        $this->getPrinter()->display_info('-------------CONFIGERING DB--------------');
        require_once $this->root_app.'App/config/app.php';
        $config = LoadConfig($this->root_app.'/App/config/db.conf');
        $db = array("DB_TYPE"=>DB_TYPE, "DB_HOST"=>DB_HOST, 'DB_NAME'=>DB_NAME, 'DB_USER'=>DB_USER,'DB_PASS'=>DB_PASS);
        print_r($db);

        echo PHP_EOL.'Enter DataBase Type ('.DB_TYPE.'): ';
        $data = trim(fgets(STDIN, 1024));
        if($data != '' || $data != null){
           $db['DB_TYPE'] = $data;
        }
        echo PHP_EOL.'DataBase Type is "'.$db['DB_TYPE'].'"'.PHP_EOL;

        echo PHP_EOL.'Enter DataBase Host ('.DB_HOST.'): ';
        $data = trim(fgets(STDIN, 1024));
        if( $data != '' || $data != null){
           $db['DB_HOST'] = $data;
        }
        echo PHP_EOL.'DataBase User is "'.$db['DB_HOST'].'"'.PHP_EOL;

        echo PHP_EOL.'Enter DataBase Name ('.DB_NAME.'): ';
        if($data != '' || $data != null){
            $db['DB_NAME'] = $data;
         }
        echo PHP_EOL.'DataBase Type is "'.$db['DB_NAME'].'"'.PHP_EOL;

        echo PHP_EOL.'Enter DataBase User ('.DB_USER.'): ';
        $data = trim(fgets(STDIN, 1024));
        if($data != '' || $data != null){
           $db['DB_USER'] = $data;
        }
        echo PHP_EOL.'DataBase User is "'.$db['DB_USER'].'"'.PHP_EOL;

        echo PHP_EOL.'Enter DataBase Password (): ';
        $db['DB_PASS'] = trim(fgets(STDIN, 1024));
        echo PHP_EOL.'DataBase Password is "'.$db['DB_PASS'].'"'.PHP_EOL;

        require_once $this->root_core.'Libs/Helper.php';
        Helper::SetConfig($this->root_app.'App/config/db', $db);
        
        $this->getPrinter()->display_success("-->DataBase Configured Succesfully");
    }

    
}