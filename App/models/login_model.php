<?php 	

class Login_Model extends Model
{
	
	public function __construct()
	{
		parent::__construct();
	}
	public  function getArtist($creator_id){
		$query = $this->db->select('SELECT artist_id FROM artist WHERE creator_id = :creator_id', array('creator_id' =>$creator_id));
		return $query['0']['artist_id'];
		
	}
	public function run(){

		$sth=$this->db->prepare("SELECT * FROM users WHERE user_email = :user_email AND password = :password");
		//':password' =>Hash::create('sha1',$_POST['password'],HASH_PASSKEY)
		$sth->execute(array(
			'user_email'=>$_POST['email'],
			':password' =>Hash::create('sha1',$_POST['password'],HASH_PASSKEY)
		));

		$data = $sth->fetch();
		//print_r($data);
		//die();
		
		//$data = $sth->fetchAll();
		$count = $sth->rowCount();
		if($count > 0){
			//login
			Session::init();	
			
			Session::set('loggedIn',true);
			Session::set('userid',$data['user_id']);
			
			if($data['role_id'] == 1){
				Session::set('role',random_int(1001, 1999));
				$artist_id = $this->getArtist($data['creator_id']);
				Session::set('artist',$artist_id);
				#issartist
			}
			elseif ($data['role_id'] == 2) {
				Session::set('role',random_int(2001, 2999));
				$artist_id = $this->getArtist($data['creator_id']);
				Session::set('artist',$artist_id);
				#prod
			}elseif ($data['role_id'] == 3) {
				Session::set('role',random_int(3001, 3999));
				#isanadmin
			}elseif ($data['role_id'] == 4) {
				Session::set('role',random_int(4001, 4999));
				#isamod
			}elseif ($data['role_id'] == 5) {
				Session::set('role',random_int(5001, 6999));
				#isinvester
			}elseif ($data['role_id'] == 6) {
				Session::set('role','owner');
				#isowner
			}else{
				Session::set('role',random_int(100,999));
				#user
			}

			if($data['level'] == 1){
				Session::set('access',random_int(1001, 1999));
				#free basic atist
			}
			elseif ($data['level'] == 2) {
				Session::set('access',random_int(2001, 2999));
				#lvel 1 atist 300
			}elseif ($data['level'] == 3) {
				Session::set('access',random_int(3001, 3999));
				#level 2 500
			}elseif ($data['level'] == 4) {
				Session::set('access',random_int(4001, 4999));
				#level 3  
			}elseif ($data['level'] == 5) {
				Session::set('access',random_int(5001, 5999));
				#test
			}elseif ($data['level'] == 6) {
				Session::set('access',random_int(6001, 6999));
				#admin
			}elseif ($data['level'] == 7) {
				Session::set('access',random_int(7001, 7999));
				#investors
			}elseif ($data['level'] == 8) {
				Session::set('access',random_int(8001, 8999));
				#member
			}elseif ($data['level'] == 9) {
				Session::set('access','owner');
				#isowner
			}elseif (($data['level'] == 10) || ($data['level'] == 'G')) {
				Session::set('access','G');
				#isowner
			}else{
				Session::set('access',random_int(100,999));
				#user
			}


			header('location:'.URL.'dashboad');
		}else{
			//show an error
			header('location:'.URL.'login');
			$error = '<div class="wrapper"><div class="alert alert-danger" role="alert">A simple danger alert—check it out!</div></div>';
			return $error;

		}
		
	}
}

 ?>