<?php 
require_once __DIR__.'/Logger.php';
/**
 * 
 */
class Database extends PDO
{
	private $newMigrations = [];
	protected $_dbName = '';
	protected $_dbType = '';

	public function __construct($db_type, $db_host, $db_name, $db_user, $db_pass)
	{
		$this->_dbType = strtolower($db_type);
		$this->_dbName = $db_name;
		
		$dsn = $this->buildDSN($db_host, $db_name);
		
		try {
			parent::__construct($dsn, $db_user, $db_pass);
			$this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		} catch (PDOException $e) {
			throw new Exception("Connection failed: " . $e->getMessage());
		}
	}

	private function buildDSN($host, $name)
	{
		switch ($this->_dbType) {
			case 'sqlite':
				// If host is provided, treat it as a directory, otherwise use DataBase root
				$dir = !empty($host) ? rtrim($host, DIRECTORY_SEPARATOR) : ROOT . DIRECTORY_SEPARATOR . 'DataBase';
				if (!is_dir($dir)) {
					mkdir($dir, 0777, true);
				}
				return "sqlite:" . $dir . DIRECTORY_SEPARATOR . $name . ".sqlite";
			
			case 'mysql':
			case 'pgsql':
			default:
				return "{$this->_dbType}:host={$host};dbname={$name}";
		}
	}

	/**
	*select 
	*@param string $sql An SQL string
	*@param array $array Parameters to bind
	*@param int $fetchMode A PDO Fetch mode
	*@return mixed
	*/
	public function select($sql, $array = array(), $fetchMode = PDO::FETCH_ASSOC) {
		$sth = $this->prepare($sql);
		foreach ($array as $key => $value) {
			$sth->bindValue($key, $value);
		}
		$sth->execute();
		return $sth->fetchAll($fetchMode);
	}

	public function select_one($sql, $array = array(), $fetchMode = PDO::FETCH_ASSOC) {
		$sth = $this->prepare($sql);
		foreach ($array as $key => $value) {
			$sth->bindValue($key, $value);
		}
		$sth->execute();
		return $sth->fetch($fetchMode);
	}

	/**
	*insert
	*@param string $table name of table to insert into
	*@param array $data An associative array
	*/
	public function insert($table, $data) {
		ksort($data);
		$fieldnames = implode('`,`', array_keys($data));
		$fieldvalues = ':' . implode(', :', array_keys($data));
		
		$sth = $this->prepare("INSERT INTO `$table` (`$fieldnames`) VALUES ($fieldvalues)");

		foreach ($data as $key => $value) {
			$sth->bindValue(":$key", $value);
		}
		return $sth->execute();
	}

	public function selectOne($sql, $array = array(), $fetchMode = PDO::FETCH_ASSOC)
	{
		return $this->select_one($sql, $array, $fetchMode);
	}

	/**
	*update
	*@param string $table - name of table to update
	*@param array $data An associative array
	*@param mixed $whereConditions
	*/
	public function update($table, $data, $whereConditions) {
		ksort($data);
		$fieldDetails = null;

		foreach ($data as $key => $value) {
			$fieldDetails .= "`$key` = :$key,";
		}
		$fieldDetails = rtrim($fieldDetails, ',');

		$whereClause = "";
		$bindings = [];
		if (is_array($whereConditions) && !empty($whereConditions)) {
			$whereClause = "WHERE ";
			$conditions = [];
			foreach ($whereConditions as $key => $value) {
				$conditions[] = "`$key` = :w_$key";
				$bindings[":w_$key"] = $value;
			}
			$whereClause .= implode(" AND ", $conditions);
		} else if (is_string($whereConditions) && !empty($whereConditions)) {
			$whereClause = "WHERE " . $whereConditions;
		}

		$sth = $this->prepare("UPDATE `$table` SET $fieldDetails $whereClause");

		foreach ($data as $key => $value) {
			$sth->bindValue(":$key", $value);
		}
		
		foreach ($bindings as $key => $value) {
			$sth->bindValue($key, $value);
		}
		
		return $sth->execute();
	}

	/**
	*DELETE
	*@param string $table
	*@param mixed $whereConditions
	*@param int $limit
	*@return bool success
	*/
	public function delete($table, $whereConditions, $limit = 1) {
		$whereClause = "";
		$bindings = [];
		if (is_array($whereConditions) && !empty($whereConditions)) {
			$whereClause = "WHERE ";
			$conditions = [];
			foreach ($whereConditions as $key => $value) {
				$conditions[] = "`$key` = :w_$key";
				$bindings[":w_$key"] = $value;
			}
			$whereClause .= implode(" AND ", $conditions);
		} else if (is_string($whereConditions) && !empty($whereConditions)) {
			$whereClause = "WHERE " . $whereConditions;
		}

		// LIMIT clause is not natively supported in DELETE by standard PDO/SQL across all drivers 
		// but supported in MySQL and SQLite (if compiled with SQLITE_ENABLE_UPDATE_DELETE_LIMIT).
		// We'll keep it for now but be mindful.
		$sql = "DELETE FROM `$table` $whereClause";
		if ($limit !== null && $this->_dbType !== 'pgsql') {
			$sql .= " LIMIT $limit";
		}

		$sth = $this->prepare($sql);
		
		foreach ($bindings as $key => $value) {
			$sth->bindValue($key, $value);
		}
		
		return $sth->execute();
	}

	public function applyMigration($value)
	{
		if (php_sapi_name() !== 'cli') {
			throw new Exception("Migrations can only be run from the Command Line Interface.");
		}

		Logger::Info("Creating migration table...");
		$this->createMigrationTable();
		
		Logger::Info("Fetching applied migrations...");
		$appliedMig = $this->getAppliedMigrations();

		Logger::Info("Scanning for new migrations in: $value/Migrations");
		$migrationDir = $value . '/Migrations';
		
		if (!is_dir($migrationDir)) {
			throw new Exception("Migration directory does not exist: $migrationDir");
		}

		$files = scandir($migrationDir);
		$toApply = array_diff($files, $appliedMig);
		
		foreach ($toApply as $migration) {
			if (in_array($migration, ['.', '..', '...']) || pathinfo($migration, PATHINFO_EXTENSION) !== 'php') {
				continue;
			}
			
			require_once $migrationDir . '/' . $migration;
			$className = pathinfo($migration, PATHINFO_FILENAME);
			
			if (!class_exists($className)) {
				throw new Exception("Migration class $className not found in $migration");
			}

			$instance = new $className();
			Logger::Info("Applying migration: $migration");
			$instance->up();
			Logger::Success("Migration $migration applied successfully.");
			$this->newMigrations[] = $migration;
		}
		
		if (!empty($this->newMigrations)) {
			$this->saveMigrations();
			Logger::Success("All new migrations saved.");
		} else {
			Logger::Info("All migrations are already up to date.");
		}
	}

	public function createMigrationTable()
	{
		if ($this->_dbType === 'sqlite') {
			$this->exec("
				CREATE TABLE IF NOT EXISTS migrations(
				id INTEGER PRIMARY KEY AUTOINCREMENT,
				migration VARCHAR(255),
				created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP);");
		} else {
			$this->exec("
				CREATE TABLE IF NOT EXISTS migrations(
				id INT AUTO_INCREMENT PRIMARY KEY,
				migration VARCHAR(255),
				created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP )ENGINE=INNODB;");
		}
	}

	public function getAppliedMigrations()
	{
		$sth = $this->prepare("SELECT migration FROM migrations");
		$sth->execute();
		return $sth->fetchAll(PDO::FETCH_COLUMN);
	}

	public function saveMigrations()
	{
		$placeholders = implode(',', array_fill(0, count($this->newMigrations), '(?)'));
		$sth = $this->prepare("INSERT INTO migrations (migration) VALUES $placeholders");
		$sth->execute($this->newMigrations);
	}

	public function clearMigration()
	{
		if ($this->_dbType === 'sqlite') {
			$this->exec("DROP TABLE IF EXISTS migrations");
			$this->createMigrationTable();
		} else {
			$this->dropDB();
			$this->createDB();
		}
	}

	public function refreshMigration($base)
	{
		$this->clearMigration();
		$this->applyMigration($base);
	}

	private function dropDB()
	{
		if ($this->_dbType !== 'sqlite') {
			$this->exec("DROP DATABASE IF EXISTS " . $this->_dbName);
		}
	}

	private function createDB()
	{
		if ($this->_dbType !== 'sqlite') {
			$this->exec("CREATE DATABASE " . $this->_dbName);
		}
	}
}


 ?>