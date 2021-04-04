<?php 

class Errors extends Controller
{
	
	function __construct()
	{
		parent::__construct();
		//echo "this is  an error :( <br>";
	}
	function index(){
		$this->view->title = '404 Error';
		$this->view->msg = 'Sorry! the page you are looking for does not exist.';
		$this->view->render('error');	
	}
	function init($error_code = '404', $error_msg = 'the file you are looking for does not exist.'){
		$this->view->title = $error_code.' Error';
		$this->template->assign('error_msg','Sorry! '.$error_msg);
		$this->template->assign('error_code',$error_code);
		$this->template->render('error/index');	
	}

	

}


 ?>