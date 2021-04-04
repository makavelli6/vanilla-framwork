<?php 
require_once __DIR__.'Logger.php';

class Model
{
	
	function __construct()
	{
		try {
			$config = Helper::LoadConfig(ROOT.'/App/cofig/db.conf');
			$this->db =new Database($config['DB_TYPE'],$config['DB_HOST'],$config['DB_NAME'],$config['DB_USER'],$config['DB_PASS']);
		} catch (PDOException $e) {
			Logger::Error("ERROR INVALID DATABASE CREDENTIALS".PHP_EOL);
			echo "Please run the command in terminal to set up your Database:".PHP_EOL;
			Logger::Info("php builder config:db");

		}
		
		
		//$this->tx = new Database(DB_TYPE,DB_HOST,DB_NAME,DB_USER,DB_PASS);

	}
}

 ?>