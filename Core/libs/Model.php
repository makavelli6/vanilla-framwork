<?php 
require_once __DIR__.'/Logger.php';
require_once __DIR__.'/LevelDB.php';



class Model
{
	
	function __construct()
	{
		try {
			$this->portabeDB = new PortableDB('core.db');
		} catch (PDOException $e) {
			echo 'Unble to Connect :';
			echo $e->getMessage();
			exit;
		}

		try {
			$this->db =new Database(DB_TYPE,DB_HOST,DB_NAME,DB_USER,DB_PASS);
		} catch (PDOException $e) {
			echo 'Unble to Connect :';
			echo $e->getMessage();
			exit;

			Logger::Error("ERROR INVALID DATABASE CREDENTIALS".PHP_EOL);
			echo "Please run the command in terminal to set up your Database:".PHP_EOL;
			Logger::Info("php builder config:db");
		}
	}
	
	
}

 ?>