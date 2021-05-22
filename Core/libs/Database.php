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
	public function select_fetch_1($sql, $array = array(),$fetchMode =PDO::FETCH_ASSOC){
		$sth = $this->prepare($sql);
		foreach ($array as $key => $value) {
			$sth->bindValue("$key","$value");
				}
				$sth->execute();
				return $sth->fetch($fetchMode);
	}


	/**
	*insert{
	*@param type $table -name  oftabe to insert into
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
	public function update($table,$data,$where){

		ksort($data);
		//print_r($data);

		$fieldDetails = null;

		foreach ($data as $key => $value) {
			$fieldDetails .= "`$key` = :$key," ;
			}
			$fieldDetails = rtrim($fieldDetails, ',');
		//UPDATE table  SET data1= a, data2=b , data3= c WHERE something(id)=1
		$sth = $this->prepare("UPDATE `$table` SET $fieldDetails WHERE $where ");

		foreach ($data as $key => $value) {
			$sth->bindValue("$key","$value");
				}
		$sth->execute();
		//die();
	}

/**
*DELETE
*@param type string $table
*@param type string $where
*@param type  int $limit
*@return int affected rows

*/
	public  function  delete($table,$where, $limit =1){
		return $this->exec("DELETE FROM `$table`  WHERE $where LIMIT $limit ");
		//print_r($where);
		

	}
	public function applyMigration($value)
	{
		echo "creating  ";
		$this->createMigrationTable();
		echo "getting  ";
		$appliedMig = $this->getAppliedMigrations();

		echo "scaning \n  ";
		$files = scandir($value.'/Migrations');
		$toApply = array_diff($files, $appliedMig);
		echo "looping \n  ";
		foreach ($toApply as $migration) {
			if($migration == '.' || $migration =='..' || $migration =='...'){
				continue;
			}
			echo "scaning 1\n  ";
			require_once $value.'/Migrations/'.$migration;
			echo "scaning 2\n  ";
			$className = pathinfo($migration,PATHINFO_FILENAME);
			echo "scaning 3\n  ";
			$instance = new $className();
			echo "scaning 4\n  ";
			echo 'apping migration '.$migration.PHP_EOL;
			$instance->up();
			echo $migration.' applied'.PHP_EOL;
			$this->newMigrations[]= $migration;

		}
		echo "endloop \n  ";
		if(!empty($newMigrations)){
			$this->saveMigrations();

		}else{
			echo "All migrations have been Applied";
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