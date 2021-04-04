<?php 

/**
 * 
 */
class Verify 
{
	
	function __construct()
	{
		# code...
	}

	public static  function authLogin($url = null){
		@session_start();
		if(isset($_SESSION['loggedIn'])){
			if($_SESSION['loggedIn'] == false ){
				session_destroy();
				return;
			}elseif (!is_null($url)) {
				header($url);
			}
		}
	}

	public static  function handleLogin($url = null){
		@session_start();
		$logged = $_SESSION['loggedIn'];
		if($logged == false ){
			session_destroy();
			header('location: '.URL.'auth');
			exit;
			return;
		}elseif (!is_null($url)) {
			header($url);
		}

	}
	public static  function isLogged(){
		@session_start();
		if (isset($_SESSION['loggedIn'])) {
			return true;
		}
		session_destroy();
		return false;
	}
	public static function isOwner(){
		Session::init();
		$logged =Session::get('loggedIn');
		$role =Session::get('role');

		if($logged == false  || $role != 'owner'){
			Session::destroy();
			header('location: '.URL.'login');
			exit;
		}
	}
	public static function isAdmin(){
		Session::init();
		$logged =Session::get('loggedIn');
		$role =Session::get('role');

		if($logged == false  || $role != 'owner'|| $role != 'admin' || $role != 'mod'){
			Session::destroy();
			header('location: '.URL.'login');
			exit;
		}
	}
	

}




?>