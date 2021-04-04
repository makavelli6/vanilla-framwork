<?php 

class Controller
{
	
	function __construct()
	{
		//echo 'MAIN contraller <br>';
		$this->view = new View();
		$this->template = new Template();
		
	}
	public function loadModel($name, $modelPath){

		$path = $modelPath.$name.'_model.php';

		if(file_exists($path)){
			require $modelPath.$name.'_model.php';
			$modelName = $name.'_Model';
			$this->model = new $modelName();
		}
	}
}

 ?>