<?php

class dbOpperator{
    private $name;
    private $dir;
    private $baseDir;
    private $instance;

    public function __construct( $base) {
        $this->name = $name;
        $this->baseDir = $base;
    }
   
    public function Put(string $dbName,string $file,array $data){
        $dbData = $this->loadDB($dbName,$file);
        $dbData = array_merge($dbData , $data);
        $data = serialize($dbData);
        file_put_contents($this->baseDir.'/DataBase/LiteDB/'.$dbName.'_db/'.$file ,$data);
    }
    public function Get(string $dbName,string $file){
        $data = $this->loadDB($dbName, $file);
        return $data;
    }
    public function Delete(string $dbName,string $file,string $key){
        $dbData = $this->loadDB($dbName,$file);
        unset($dbData[$key]);
        $data = serialize($dbData);
        file_put_contents($this->baseDir.'/DataBase/LiteDB/'.$dbName.'_db/'.$file.'.xdb',$data);
    }
    private function loadDB(string $dbName,string $file):array{
        $this->checkDB($name , $file);
        $data = file_get_contents($this->baseDir.'/DataBase/LiteDB/'.$dbName.'_db/'.$file);
        $dbData = unserialize($data);
        return $dbData;
    }
    private function checkDB(string $dbName,string $file){
        if(!file_exists($this->baseDir.'/DataBase/LiteDB/'.$dbName.'_db/'.$file)){
            throw new Exception('Database has not instaciated');
            die();
        }

    }

    public function deleteDB(){
        Text::delete_file($this->dir);
        Text::create_file($this->dir);
    }


}

