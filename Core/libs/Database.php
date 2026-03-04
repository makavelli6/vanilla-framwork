<?php 
require_once __DIR__.'/Logger.php';
/**
 * 
 */
class Database extends PDO
{
	private $newMigrations = [];
	private $_dbName = '';
	public function __construct($db_type,$db_host,$db_name,$db_user,$db_pas)
	{
		parent::__construct($db_type.':host='.$db_host.';dbname='.$db_name,$db_user,$db_pas);
		$this->_dbName = $db_name;
		//parent::setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTIONS);
	}

	/**
	*select 
	*@param string $sql AnSQL string
	*@param $array $array Parameters to bind
	*@param constant $fetchMode A PDO Fetch mode
	*@return mixed
	*/
	public function select($sql, $array = array(),$fetchMode =PDO::FETCH_ASSOC){
		$sth = $this->prepare($sql);
		foreach ($array as $key => $value) {
			$sth->bindValue("$key","$value");
				}
				$sth->execute();
				return $sth->fetchAll($fetchMode);
	}
	public function select_one($sql, $array = array(),$fetchMode =PDO::FETCH_ASSOC){
		$sth = $this->prepare($sql);
		foreach ($array as $key => $value) {
			$sth->bindValue("$key","$value");
				}
				$sth->execute();
				return $sth->fetch($fetchMode);
	}


	/**
	*insert{
	*@param type $table name of tabe to insert into
	*@param type $data An associate array
	}
	*/
	public function insert($table,$data){

		ksort($data);
		//print_r($data);
		$fieldnames =implode('`,`', array_keys($data));
		$fieldvalues=':'.implode(', :', array_keys($data));
		//echo "INSERT INTO `$table` (`$fieldnames`) VALUES ($fieldvalues)";
		$sth = $this->prepare("INSERT INTO `$table` (`$fieldnames`) VALUES ($fieldvalues)");

		foreach ($data as $key => $value) {
			$sth->bindValue("$key","$value");
				}
		$sth->execute();


	}

	public function selectOne($sql, $array = array(), $fetchMode = PDO::FETCH_ASSOC)
	{
		$sth = $this->prepare($sql);
		foreach ($array as $key => $value) {
			$sth->bindValue("$key", $value);
		}
		
		$sth->execute();
		return $sth->fetch($fetchMode);
	}


	
	/**
	*update{
	*@param type $table -name  oftabe to insert into
	*@param type $data An associate array
	}
	*/
	public function update($table, $data, $whereConditions){
		ksort($data);
		$fieldDetails = null;

		foreach ($data as $key => $value) {
			$fieldDetails .= "`$key` = :$key," ;
		}
		$fieldDetails = rtrim($fieldDetails, ',');

        $whereClause = "";
        if (is_array($whereConditions) && !empty($whereConditions)) {
            $whereClause = "WHERE ";
            $conditions = [];
            foreach ($whereConditions as $key => $value) {
                $conditions[] = "`$key` = :w_$key";
            }
            $whereClause .= implode(" AND ", $conditions);
        } else if (is_string($whereConditions) && !empty($whereConditions)) {
            $whereClause = "WHERE " . $whereConditions;
        }

		$sth = $this->prepare("UPDATE `$table` SET $fieldDetails $whereClause");

		foreach ($data as $key => $value) {
			$sth->bindValue(":$key", $value);
		}
        
        if (is_array($whereConditions)) {
            foreach ($whereConditions as $key => $value) {
                $sth->bindValue(":w_$key", $value);
            }
        }
        
		$sth->execute();
	}

/**
*DELETE
*@param type string $table
*@param type array|string $whereConditions
*@param type  int $limit
*@return bool success
*/
	public function delete($table, $whereConditions, $limit = 1){
        $whereClause = "";
        if (is_array($whereConditions) && !empty($whereConditions)) {
            $whereClause = "WHERE ";
            $conditions = [];
            foreach ($whereConditions as $key => $value) {
                $conditions[] = "`$key` = :w_$key";
            }
            $whereClause .= implode(" AND ", $conditions);
        } else if (is_string($whereConditions) && !empty($whereConditions)) {
            $whereClause = "WHERE " . $whereConditions;
        }

        $sth = $this->prepare("DELETE FROM `$table` $whereClause LIMIT $limit");
        
        if (is_array($whereConditions)) {
            foreach ($whereConditions as $key => $value) {
                $sth->bindValue(":w_$key", $value);
            }
        }
        
		return $sth->execute();
	}
	public function applyMigration($value)
	{
        if (php_sapi_name() !== 'cli') {
            throw new Exception("Migrations can only be run from the Command Line Interface.");
        }

		echo "Creating migration table... \n";
		$this->createMigrationTable();
		
        echo "Fetching applied migrations... \n";
		$appliedMig = $this->getAppliedMigrations();

		echo "Scanning for new migrations in: $value/Migrations \n";
        $migrationDir = $value.'/Migrations';
        
        if (!is_dir($migrationDir)) {
            throw new Exception("Migration directory does not exist: $migrationDir");
        }

		$files = scandir($migrationDir);
		$toApply = array_diff($files, $appliedMig);
		
		foreach ($toApply as $migration) {
			if(in_array($migration, ['.', '..', '...']) || pathinfo($migration, PATHINFO_EXTENSION) !== 'php'){
				continue;
			}
			
			require_once $migrationDir.'/'.$migration;
			$className = pathinfo($migration,PATHINFO_FILENAME);
            
            if (!class_exists($className)) {
                throw new Exception("Migration class $className not found in $migration");
            }

			$instance = new $className();
			echo "Applying migration: $migration \n";
			$instance->up();
			echo " -> $migration applied successfully.\n";
			$this->newMigrations[]= $migration;
		}
		
		if(!empty($this->newMigrations)){
			$this->saveMigrations();
            echo "All new migrations saved. \n";
		}else{
			echo "All migrations are already up to date. \n";
		}
	}
	public function createMigrationTable()
	{
		$this->exec("
			CREATE TABLE IF NOT EXISTS migrations(
			id INT AUTO_INCREMENT PRIMARY KEY,
			migration VARCHAR(255),
			created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP )ENGINE=INNODB;");

	}
	public function getAppliedMigrations()
	{
		$sth= $this->prepare("SELECT migrations FROM migration");
		$sth->execute();
		return $sth->fetchAll(PDO::FETCH_COLUMN);
	}

	public function saveMigrations()
	{
		$str = implode(',' ,array_map($this->mFormart($m),$this->newMigrations));
		$smt = $this->prepare("INSERT INTO migrations (migration) VALUES $str");
		$smt->execute();
		
	}
	private function mFormart($var ){ 	return "('$var')"; }
	public function clearMigration()
	{
		$this->dropDB();
		$this->createDB();
	}
	public function refreshMigration($base)
	{
		$this->clearMigration();
		$this->applyMigration($base);
	}
	private function dropDB()
	{
		$this->exec("DROP DATABASE IF EXISTS ".$this->_dbName);
	}
	private function createDB()
	{
		$this->exec("CREATE DATABASE ".$this->_dbName);
	}
	public function create_user(){

	}
}


 ?>