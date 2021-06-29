<?php 
require_once __DIR__.'/Logger.php';
require_once __DIR__.'/../../server.php';
/**
 * 
 */
class PortableDB extends PDO
{
	private $newMigrations = [];
	public function __construct($name)
	{
		parent::__construct('sqlite:'.ROOT.'/DataBase/'.$name);
		$this->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
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

		$fieldDetails = null;
		foreach ($data as $key => $value) {
			$fieldDetails .= "`$key` = :$key," ;
		}
		$fieldDetails = rtrim($fieldDetails, ',');
		$sth = $this->prepare("UPDATE `$table` SET $fieldDetails WHERE $where ");

		foreach ($data as $key => $value) {
			$sth->bindValue("$key","$value");
		}
		$sth->execute();
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
	}
	
}


 ?>