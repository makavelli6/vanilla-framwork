<?php
require_once __DIR__.'/dbOpperator.php';

class dbEngine {

    protected static dbOpperator $opperator ;
    protected dbOpperator $Opperator;
    private static $baseDir;

    public function __construct( $root_dir) {
        self::$baseDir = $root_dir ;
        $this->Opperator = new dbOpperator($root_dir);
    }
    public static function Create(string $dbName){
        mkdir(self::$baseDir.'/DataBase/LiteDB/'.$name.'_db');
        $handle = fopen(self::$baseDir.'/DataBase/LiteDB/'.$name.'_db/index.xdb', "x+");
        $details = array('Name' => $name,'Name' => $name, 'Shard' => 0, 'Entries' =>0 );
        fwrite($handle, serialize($details));
    }
    public static function Exists(string $dbName){
        return file_exists(self::$baseDir.'/DataBase/LiteDB/'.$name.'_db/index.xdb');
    }
    

    
}