<?php 
require __DIR__."./Database.php";

/**
 * 
 */
class Transaction extends PDO
{
	public function __construct($db_type,$db_host,$db_name,$db_user,$db_pas)
	{
		parent::__construct($db_type.':host='.$db_host.';dbname='.$db_name,$db_user,$db_pas);
		parent::settAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTIONS);
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
		$this->beginTransaction();
		$sth = $this->prepare("INSERT INTO `$table` (`$fieldnames`) VALUES ($fieldvalues)");

		foreach ($data as $key => $value) {
			$sth->bindValue("$key","$value");
				}
		$sth->execute();


	}


	private function clean($value){
		$antiXss = new AntiXSS();
		$value = $antiXss->xss_clean($value);
		return $value;
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
		print_r($where);
		

	}
}

 ?>