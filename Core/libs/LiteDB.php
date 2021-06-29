<?php
require_once __DIR__.'/bin/LiteDB/dbEngine.php';
require_once __DIR__.'/../../server.php';


class LiteDB extends dbEngine{

    private string $dbDir ;
    private string $dbIndex = 'index.xdb';
    private string $dbName;
    private string $instance;
    private int $dbSize;

    public function __construct($dbName, $dbSize = 1024 * 0.5){
        $this->dbSize = $dbSize;
        $this->dbName = $dbName;
        $this->dbDir = ROOT.'/DataBase/LiteDB/'.$dbName.'_db';
		parent::__construct(ROOT);
        $this->instance = $this->Opperator->Get($this->dbName, $this->dbIndex);
	}
    public function Put(string $key,$value){
        $hashedKey = md5($key);
        if(!array_key_exists($this->instance,$hashedKey)){
            $currentDB = $this->loadDB();
            $this->Opperator->Put($this->dbName , $currentDB, array($hashedKey => $value ));
            $this->Opperator->Put($this->dbName , $this->dbIndex, array( $hashedKey => $currentDB));
        }else {
            $this->Opperator->Put($this->dbName , $this->instance[$hashedKey], array($hashedKey => $value ));
        }
    }
    public function Get(string $key){
        $hashedKey = md5($key);
        if(!array_key_exists($this->instance, $data)){
            return null;
        }
        $temp = $this->Opperator->Get($this->dbName , $this->instance[$hashedKey]);
        return $temp[$hashedKey];
        
    }
    public function Delete(string $key){
        $hashedKey = md5($key);
        if(!array_key_exists($this->instance, $data)){ return null; }

        $this->Opperator->Delete($this->dbName , $this->instance[$hashedKey] , $hashedKey);
        $this->Opperator->Delete($this->dbName , $this->dbIndex , $hashedKey);

    }
    Public static function InitDB(string $name){
        self::Create($name);
        return new LiteDB($name);
    }
    private function shardDB($files){
        $size = count($files)-3;
        if($size < 10 ){
            $size =$size + 1;
            $fileName = '/db000'.$size;
        }else if($size > 10 && $size < 100 ){
            $size =$size + 1;
            $fileName = '/db00'.$size;
        }else if($size > 100 && $size < 1000 ){
            $size =$size + 1;
            $fileName = '/db0'.$size; 
        }

        $handle = fopen($this->dbDir, "x+");
		fclose($handle);
    }
    private function loadDB(){
        $files = array();
        if($this->instance['Shard'] == 0){
            $handle = fopen($this->dbDir.'/db0001.xdb', "x+");
            fclose($handle);
        }else{
            $files = scandir($this->dbDir);
            if($this->checkLoad($files[count($files)-1])){
                $this->shardDB($files);
            }
        }
        $files = scandir($this->dbDir);
        return $files[count($files)-1];

    }
    private function checkLoad(string $fileName){
        if(filesize($this->dbDir.'/'.$fileName) > $this->dbSize){
            return false;
        }
        return true;
    }
    

}