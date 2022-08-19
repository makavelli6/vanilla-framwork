<?php
require_once __DIR__.'/Migration.php';

/**
 * 
 */
class BaseMigration  
{
	public $db;
	public $up_smt;
	public $down_smt;
	
	function __construct($base)
	{
		require_once $base.'/App/config/app.php';
		$this->db = new Migration(DB_TYPE,DB_HOST,DB_NAME,DB_USER,DB_PASS);
	}
	public function up()
	{
		if($this->up_smt != ''){
			$this->db->exec($this->up_smt);
		}
		
	}
	public function down(){
		if($this->up_smt != ''){
			if($this->down_smt != ''){
				$this->db->exec($this->down_smt);
			}
			
		}
	}
}
  






?>