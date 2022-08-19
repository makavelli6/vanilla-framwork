<?php 
require_once __DIR__.'/Logger.php';
/**
 * 
 */
class Migration extends PDO
{
	private $newMigrations = [];
	private $_dbName = '';
	public function __construct($db_type,$db_host,$db_name,$db_user,$db_pas)
	{
		parent::__construct($db_type.':host='.$db_host.';dbname='.$db_name,$db_user,$db_pas);
		$this->_dbName = $db_name;
	}

	
	public function applyMigration($value)
	{
		$this->createMigrationTable();
		$appliedMig = $this->getAppliedMigrations();

		$files = scandir($value.'/Migrations');
		$toApply = array_diff($files, $appliedMig);
		foreach ($toApply as $migration) {
			if($migration == '.' || $migration =='..' || $migration =='...'){
				continue;
			}
			require_once $value.'/Migrations/'.$migration;
			$className = pathinfo($migration,PATHINFO_FILENAME);
			$instance = new $className();
			echo '-->apping migration - '.$migration.PHP_EOL;
			//$instance->up();
			echo ' - '.$migration.' applied'.PHP_EOL.PHP_EOL.PHP_EOL;
			$this->newMigrations[]= $migration;

		}
		if(!empty($newMigrations)){
			$this->saveMigrations();

		}else{
			echo "---* All migrations have been Applied *---".PHP_EOL.PHP_EOL;
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
		$this->exec("DROP DATABASE IF EXISTS ".$this->_dbName.";");
	}
	private function createDB()
	{
		$this->exec("CREATE DATABASE ".$this->_dbName);
	}
	public function create_user(){

	}
}


 ?>