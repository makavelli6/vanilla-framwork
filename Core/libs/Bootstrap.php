<?php 

class Bootstrap
{
	
	private $_url = null;
	private $_controller =null;

	private $_controllerPath = ROOT.CONTROLLER;
	private $_managerPath = ROOT.'/Core/managers/';
	private $_errorFile = 'errors.php';
	private $_defaultFile = 'index.php';

	private $_modelPath = ROOT.MODEL;

	function __construct()
	{


	}

	public function init(){
		//Sets the URL
		$this->_getURL();

		//load the default contraller if no url is set
		if(empty($this->_url[0])){
			$this->_loadDefaultCOntraller();
			return false;
		}

		//
		$this->_loadExistingController();

		//calling methords  area
		$this->_callControllerMethord();
	}
	//this fetches the url
	//set the controller path
	public function setContrallerpath($path){
		$this->_controllerPath = trim($path, '/').'/';

	}
	//set the error file
	public function errorPath($path){
		$this->_errorFile = trim($path, '/').'/';

	}
	//set the model path
	public function setModelpath($path){
		$this->_modelPath = trim($path, '/');

	}

	//set the default file  eg index.php
	public function setDefaultFile($path){
		$this->_defaultFile = trim($path, '/');

	}

	private function _getURL(){
		$url =isset($_GET['url'])? $_GET['url']: null;
		$url = rtrim($url, '/');
		$url =filter_var($url, FILTER_SANITIZE_URL);
		//removes exess back slash
		$this->_url = explode('/', $url);
	}

	//loads  if the isno  URL passes
	private function _loadDefaultCOntraller(){
		require $this->_controllerPath.$this->_defaultFile;
		$this->_controller = new Index();
		$this->_controller->index();

	}

	//loads existing controller if it is sest in the UrL
	private function _loadExistingController(){
		$file =$this->_controllerPath.$this->_url[0].'.php';
		if(file_exists($file)){
			require  $file;
			$this->_controller =  new $this->_url[0];
			$this->_controller->loadModel($this->_url[0], $this->_modelPath);
		}elseif(file_exists(ROOT.'/Core/system/controllers/'.$this->_url[0].'php')){
			require  ROOT.'/Core/system/controllers/'.$this->_url[0].'php';
			$this->_controller =  new $this->_url[0];
			$this->_controller->loadModel($this->_url[0], ROOT.'/Core/system/models/');

		}else{
			$this->_error();
			return false;

		}
	}

	private function _callControllerMethord(){

		$length = count($this->_url);


//makes sure the methord we are calling exists
		if($length >  1){
			if(!method_exists($this->_controller, $this->_url[1])){
				$this->_error();
			}
		}
		switch ($length) {
			case 5:
			$this->_controller->{$this->_url[1]}($this->_url[2],$this->_url[3],$this->_url[4]);
			break;
			case 4:
			$this->_controller->{$this->_url[1]}($this->_url[2],$this->_url[3]);

			break;
			case 3:
			$this->_controller->{$this->_url[1]}($this->_url[2]);
			break;
			case 2:
			$this->_controller->{$this->_url[1]}();
			break;
			default:
			//this  should lead to an error
			//print_r($this->_url);
			$this->_controller->index();
			break;
		} 



	}
	private function _loadManager(){

	}




	private function _error(){
		require $this->_controllerPath.$this->_errorFile;
		$this->_controller = new Errors();
		$this->_controller->index();
		exit();
	}

}


?>