<?php 

/**
 * 
 */
class View
{
	public $path = ROOT.VIEWS;
	
	function __construct()
	{
		//echo "We have a view <br>";
	}

	public function render_mutli($pages = array(), $noInclude = false)
	{
		if($noInclude == true){
			$this->render_array($pages);
		}else{
			require_once $this->path.'./App/Head.php';
			$this->render_array($pages);
			require_once $this->path.'./App/Tail.php';

		}
		
	}


	public function render($name, $noInclude = false){
		if($noInclude == true){
			require_once $this->path.$name.'.php';
		}else{
			require_once $this->path.'./App/Head.php';
			require_once $this->path.'./'.$name.'.php';
			require_once $this->path.'./App/Tail.php';
			
		}
	}
	

	public function Json($value)
	{
		header('Content-type: application/json');
		echo json_encode($value);
	}
}

?>