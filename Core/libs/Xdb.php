<?php
#require_once __DIR__.'/../../../server.php';
require_once __DIR__.'/../../server.php';



class Xdb extends dbEngine {
    private $dbIndex = 'index.xdb';
    private $dbName;
    private $instance; // This acts like a cache mapping keys to the specific file they live in 
    private $dbSize;

    public function __construct($dbName, $dbSize = 1024 * 0.5) {
        $this->dbSize = $dbSize;
        $this->dbName = $dbName;
        $this->instance = OpperatorDB::Get($this->dbName, $this->dbIndex);
        
        // Ensure index structure matches new JSON format if loading old db
        if (!isset($this->instance['Shard'])) {
            $this->instance['Shard'] = 1;
        }
    }

    public function Put($key, $value) {
        $hashedKey = md5($key);
        
        // Key doesn't exist yet, add it
        if (!array_key_exists($hashedKey, $this->instance)) {
            $currentDB = $this->loadDB();
            
            OpperatorDB::Put($this->dbName, $currentDB, array($hashedKey => $value));
            
            // Update local instance cache and save it to the index
            $this->instance[$hashedKey] = $currentDB;
            OpperatorDB::Put($this->dbName, $this->dbIndex, array($hashedKey => $currentDB));
        } else {
            // Key exists, update it in its designated shard
            OpperatorDB::Put($this->dbName, $this->instance[$hashedKey], array($hashedKey => $value));
        }
    }

    public function Get($key) {
        $hashedKey = md5($key);
        if (!array_key_exists($hashedKey, $this->instance)) { 
            return null; 
        }
        $shardFile = $this->instance[$hashedKey];
        $temp = OpperatorDB::Get($this->dbName, $shardFile);
        return $temp[$hashedKey] ?? null;
    }

    public function Delete($key) {
        $hashedKey = md5($key);
        // Ensure the key exists in our instance map
        if (!array_key_exists($hashedKey, $this->instance)) { 
            return; 
        }
        
        $shardFile = $this->instance[$hashedKey];
        
        // Delete from the shard
        OpperatorDB::Delete($this->dbName, $shardFile, $hashedKey);
        
        // Delete from the index map mapping
        unset($this->instance[$hashedKey]);
        OpperatorDB::Delete($this->dbName, $this->dbIndex, $hashedKey);
    }

    public static function InitDB($name) {
        if (!self::DB_Exists($name)) {
            self::Create($name);
        }
        return new Xdb($name);
    }

    public function IsEmpty() {
        // Shard, Name, Entries, etc. take up keys. Meaning real data is length - metadata length
        // We can just rely on Get() returning null instead of strictly trusting IsEmpty
        $size = count($this->instance);
        return $size <= 3; 
    }

    public function Exist($key) {
        $hashedKey = md5($key);
        return array_key_exists($hashedKey, $this->instance);  
    }

    private function getShardName($shardNum) {
        return sprintf('db%04d.xdb', $shardNum);
    }

    private function loadDB() {
        $shardNum = $this->instance['Shard'] ?? 1;
        $currentFile = $this->getShardName($shardNum);
        
        if ($this->IsLarg($currentFile)) {
            $shardNum++;
            $currentFile = $this->getShardName($shardNum);
            
            // Create the new empty file
            file_put_contents(self::GetDir($this->dbName).$currentFile, json_encode([], JSON_PRETTY_PRINT));
            
            // Update the index to point to the new shard
            $this->instance['Shard'] = $shardNum;
            OpperatorDB::Put($this->dbName, $this->dbIndex, array('Shard' => $shardNum));
        }
        
        return $currentFile;
    }

    private function IsLarg($fileName) {
        $path = self::GetDir($this->dbName).$fileName;
        if (!file_exists($path)) return false;
        
        // If file size exceeds the max DB size limit in bytes (dbSize is given in KB)
        return filesize($path) > ($this->dbSize * 1024);
    }
}


class OpperatorDB{
    public static function Put(string $dbName,string $file,array $data){
        self::checkDB($dbName, $file);
        $path = dbEngine::GetDir($dbName).$file;
        $fp = fopen($path, 'c+');
        if (flock($fp, LOCK_EX)) {
            $currentData = self::readJson($fp);
            $newData = array_merge($currentData, $data);
            self::writeJson($fp, $newData);
            flock($fp, LOCK_UN);
        }
        fclose($fp);
    }
    
    public static function Get(string $dbName,string $file){
        self::checkDB($dbName, $file);
        $path = dbEngine::GetDir($dbName).$file;
        $fp = fopen($path, 'r');
        $data = [];
        if (flock($fp, LOCK_SH)) {
            $data = self::readJson($fp);
            flock($fp, LOCK_UN);
        }
        fclose($fp);
        return $data;
    }
    
    public static function Delete(string $dbName,string $file,string $key){
        self::checkDB($dbName, $file);
        $path = dbEngine::GetDir($dbName).$file;
        $fp = fopen($path, 'c+');
        if (flock($fp, LOCK_EX)) {
            $dbData = self::readJson($fp);
            unset($dbData[$key]);
            self::writeJson($fp, $dbData);
            flock($fp, LOCK_UN);
        }
        fclose($fp);
    }

    private static function readJson($fp): array {
        fseek($fp, 0);
        $contents = stream_get_contents($fp);
        if (empty($contents)) return [];
        $data = json_decode($contents, true);
        return is_array($data) ? $data : [];
    }

    private static function writeJson($fp, array $data) {
        ftruncate($fp, 0);
        fseek($fp, 0);
        fwrite($fp, json_encode($data, JSON_PRETTY_PRINT));
    }

    private static function checkDB(string $dbName,string $file){
        if(!file_exists(dbEngine::GetDir($dbName).'index.xdb')){
            throw new Exception("Database '$dbName' has not been instantiated.");
        }
    }
}

class dbEngine {
    public static function Create(string $dbName){
        $dir = self::GetDir($dbName);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        $path = $dir.'index.xdb';
        if (!file_exists($path)) {
            $details = array('Name' => $dbName, 'Shard' => 1, 'Entries' => 0);
            file_put_contents($path, json_encode($details, JSON_PRETTY_PRINT));
        }
        $shardPath = $dir.'db0001.xdb';
        if (!file_exists($shardPath)) {
            file_put_contents($shardPath, json_encode([], JSON_PRETTY_PRINT));
        }
    }
    public static function DB_Exists(string $dbName){
        return file_exists(self::GetDir($dbName).'index.xdb');
    }
    public static function GetDir(string $dbName){
        return ROOT.DIRECTORY_SEPARATOR.'DataBase'.DIRECTORY_SEPARATOR.'LiteDB'.DIRECTORY_SEPARATOR.$dbName.'_db'.DIRECTORY_SEPARATOR;
    } 
}