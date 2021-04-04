<?php
require_once __DIR__.'/Database.php';
require_once __DIR__.'/Helper.php';

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
		$conf = Helper::LoadConfig($base.'/App/config/db');
		$db = new Database($config['DB_TYPE'],$config['DB_HOST'],$config['DB_NAME'],$config['DB_USER'],$config['DB_PASS']);
	}
	public function up()
	{
		if($this->up_smt != ''){
			$db->exec($this->up_smt);
		}
		
	}
	public function down(){
		if($this->up_smt != ''){
			if($this->down_smt != ''){
				$db->exec($this->down_smt);
			}
			
		}
	}
}
  






?>