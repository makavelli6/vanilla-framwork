<?php
#require_once __DIR__.'/../../../server.php';
require_once __DIR__.'/../../server.php';



class Xdb extends dbEngine{
    private  $dbIndex = 'index.xdb';
    private  $dbName;
    private  $instance;

    public function __construct($dbName, $dbSize = 1024 * 0.5){

        $this->dbSize = $dbSize;
        $this->dbName = $dbName;
        $this->instance = OpperatorDB::Get($this->dbName, $this->dbIndex);

	}
    public function Put( $key,$value){
        $hashedKey = md5($key);
        if(!array_key_exists($hashedKey,$this->instance)){
            
            $currentDB = $this->loadDB();
            //print($currentDB);
            OpperatorDB::Put($this->dbName , $currentDB, array($hashedKey => $value ));
            OpperatorDB::Put($this->dbName , $this->dbIndex, array( $hashedKey => $currentDB));
        }else {
            OpperatorDB::Put($this->dbName , $this->instance[$hashedKey], array($hashedKey => $value ));
        }
    }
    public function Get( $key){
        $hashedKey = md5($key);
        if(!array_key_exists( $hashedKey,$this->instance)){ return null; }
        $temp = OpperatorDB::Get($this->dbName , $this->instance[$hashedKey]);
        return $temp[$hashedKey];
        
    }
    public function Delete( $key){
        $hashedKey = md5($key);
        if(!array_key_exists($this->instance, $data)){ return null; }
        OpperatorDB::Delete($this->dbName , $this->instance[$hashedKey] , $hashedKey);
        OpperatorDB::Delete($this->dbName , $this->dbIndex , $hashedKey);

    }
    Public static function InitDB( $name){
        if(!self::DB_Exists($name)){
            self::Create($name);
        }
        return new Xdb($name);
    }
    Public function IsEmpty(){
        $size = count($this->instance);
        if($size < 4){ return true; }
        return false;
    }
    Public function Exist($key){
        $hashedKey = md5($key);
        return array_key_exists($hashedKey, $this->instance);  
    }

    private function shardDB($files){
        $size = count($files)-3;
        if($size < 10 ){
            $size =$size + 1;
            $fileName = '/db000'.$size.'.xdb';
        }else if($size > 10 && $size < 100 ){
            $size =$size + 1;
            $fileName = '/db00'.$size.'.xdb';
        }else if($size > 100 && $size < 1000 ){
            $size =$size + 1;
            $fileName = '/db0'.$size.'.xdb'; 
        }

        $handle = fopen(self::GetDir($this->dbName).$fileName, "x+");
		fclose($handle);
    }
    private function loadDB(){
        $files = scandir(self::GetDir($this->dbName));
        if(!in_array('db0001.xdb', $files)){
            $handle = fopen(self::GetDir($this->dbName).'db0001.xdb', "x+");
            fclose($handle);
        }else{
            $files = scandir(self::GetDir($this->dbName));
            if($this->IsLarg($files[count($files)-2])){
                $this->shardDB($files);
            }
        }
        $files = scandir(self::GetDir($this->dbName));
        return $files[count($files)-2];

    }
    private function IsLarg( $fileName){
        if(filesize(self::GetDir($this->dbName).$fileName) > $this->dbSize){
            return true;
        }
        return false;
    }
    

}


class OpperatorDB{
    public static function Put(string $dbName,string $file,array $data){
        $dbData = self::loadDB($dbName,$file);
        $dbData = array_merge($dbData , $data);
        $data = serialize($dbData);
        file_put_contents(dbEngine::GetDir($dbName).$file ,$data);
    }
    public static function Get(string $dbName,string $file){
        $data = self::loadDB($dbName, $file);
        return $data;
    }
    public static function Delete(string $dbName,string $file,string $key){
        $dbData = self::loadDB($dbName,$file);
        unset($dbData[$key]);
        $data = serialize($dbData);
        file_put_contents(dbEngine::GetDir($dbName).$file,$data);
    }
    private static function loadDB(string $dbName,string $file):array{
        self::checkDB($dbName , $file);
        $data = file_get_contents(dbEngine::GetDir($dbName).$file);
        $dbData = unserialize($data);
        if(is_array($dbData)){ return $dbData;}
        return array();
    }
    private static function checkDB(string $dbName,string $file){
        
        if(!file_exists(dbEngine::GetDir($dbName).'index.xdb')){
            throw new Exception('Database has not instaciated');
            die();
        }
    }



}

class dbEngine {
    public static function Create(string $dbName){
        mkdir(self::GetDir($dbName));
        $handle = fopen(self::GetDir($dbName).'index.xdb', "x+");
        $details = array('Name' => $dbName, 'Shard' => 0, 'Entries' =>0 );
        fwrite($handle, serialize($details));
    }
    public static function DB_Exists(string $dbName){
        return file_exists(self::GetDir($dbName).'index.xdb');
    }
    public static function GetDir(string $dbName){
        return ROOT.DIRECTORY_SEPARATOR.'DataBase'.DIRECTORY_SEPARATOR.'LiteDB'.DIRECTORY_SEPARATOR.$dbName.'_db'.DIRECTORY_SEPARATOR;
    } 
}