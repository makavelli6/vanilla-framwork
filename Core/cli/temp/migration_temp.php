<?php 
require __DIR__.'/../server.php';
require_once ROOT.'/Core/libs/BaseMigration.php';

/**
 * 
 */

class tempName extends BaseMigration
{
	
	function __construct()
	{
		parent::__construct(ROOT);
	}
	
	public function up()
	{
		$this->up_smt = "ALTER TABLE tablename;";
		parent::up();
	}

	public function down()
	{
		$this->down_smt = "ALTER TABLE tablename;";
		parent::down();
	}
}


 ?>