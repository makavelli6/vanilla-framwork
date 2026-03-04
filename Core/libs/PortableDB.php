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
	*}
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
	
}


 ?>