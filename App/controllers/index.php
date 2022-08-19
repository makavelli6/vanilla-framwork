<?php 

class Index extends Controller
{

	
	function __construct()
	{
		parent::__construct();
		 
	}
	function index(){
		echo "INSIDE INDEX ";
	}
	function home(){
		echo "INSIDE Home ";
		//$this->view->title = 'Home';
		//$this->view->render_no_player('user/home');
	}
	

	function top_tracks($rigion , $size){
		$this->view->render('index');
	}

	function pricing(){
		//$this->view->css = $this->dashboad;
		//$this->view->render('App/Head');
	}





}

 ?>