<?php 
require_once __DIR__.'/../server.php';
require_once ROOT.'/Core/libs/BaseMigration.php';

/**
 * 
 */

class m0002_Test extends BaseMigration
{
	
	function __construct()
	{
		parent::__construct(ROOT);
	}
	
	public function up()
	{
		$this->up_smt = "CREATE TABLE tablename (

		)ENGINE=INNODB;";
		parent::up();
	}

	public function down()
	{
		$this->down_smt = "DROP TABLE tablename;";
		parent::down();
	}
}


 ?>