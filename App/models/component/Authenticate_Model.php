<?php 
/**
 * 
 */
class Authenticate_Model extends Model
{
	public function __construct()
	{
		parent::__construct();
	}
	public function signup_ext($data){
		$this->db->insert('users', array(
			'user_ext_id'=>$data['user_ext_id'],
			'user_name'=>$data['user_name'],
			'user_email'=>$data['user_email'],
			'user_fname'=>$data['user_fname'],
			'user_lname'=>$data['user_lname'],
			'password' =>Hash::create('sha1','-12345-',HASH_PASSKEY),
			'role_id' =>1,
			'state' =>1
		));	
	}

	public function signup(){
		$this->db->insert('users', array(
		'user_name'=>$_GET['fname'],
		'user_email'=>$_GET['email'],
		'user_fname'=>$_GET['fname'],
		'user_lname'=>$_GET['lname'],
		'password' =>Hash::create('sha1',$_GET['password'],HASH_PASSKEY),
		'role_id' =>1));

		$result = array('result' => 'success');
		return $result;	
	}

	public function signin( $email, $pass){
		$sth=$this->db->prepare("SELECT * FROM users WHERE user_email = :user_email AND password = :password");
		$sth->execute(array('user_email'=>$email,
							':password' =>Hash::create('sha1',$pass,HASH_PASSKEY)));
		$data = $sth->fetch();
		$count = $sth->rowCount();
		

		if($count > 0){
			//if password is  true
			return $this->signup_sessions($data);
			
		}else{
			$result = array('result' => 'fail');
			return $result;
		}
	}
	protected function signup_sessions($data){
		Session::init();	
		Session::set('loggedIn',true);
		Session::set('userid',$data['user_id']);
		$this->setRole($data['role_id']);
		$this->setLevel($data['level']);
		$result = array('result' => 'success');
		return $result;
	}
	protected function setLevel($data){
		if($data == 1){
			Session::set('access',random_int(1001, 1999));
			#free basic atist
		}
		elseif ($data == 2) {
			Session::set('access',random_int(2001, 2999));
			#lvel 1 atist 300
		}elseif ($data == 3) {
			Session::set('access',random_int(3001, 3999));
			#level 2 500
		}elseif ($data == 4) {
			Session::set('access',random_int(4001, 4999));
			#level 3  
		}elseif ($data == 5) {
			Session::set('access',random_int(5001, 5999));
			#test
		}elseif ($data == 6) {
			Session::set('access',random_int(6001, 6999));
			#admin
		}elseif ($data == 7) {
			Session::set('access',random_int(7001, 7999));
			#investors
		}elseif ($data == 8) {
			Session::set('access',random_int(8001, 8999));
			#member
		}elseif ($data == 9) {
			Session::set('access','owner');
			#isowner
		}elseif (($data == 10) || ($data == 'G')) {
			Session::set('access','G');
			#isowner
		}else{
			Session::set('access',random_int(100,999));
			#user
		}
	}
	protected function setRole($data){
		if($data == 1){
			Session::set('role',random_int(1001, 1999));
			$artist_id = $this->getArtist($data['creator_id']);
			Session::set('artist',$artist_id);
			#issartist
		}
		elseif ($data == 2) {
			Session::set('role',random_int(2001, 2999));
			#prod
		}elseif ($data == 3) {
			Session::set('role',random_int(3001, 3999));
			#isanadmin
		}elseif ($data == 4) {
			Session::set('role',random_int(4001, 4999));
			#isamod
		}elseif ($data == 5) {
			Session::set('role',random_int(5001, 6999));
			#isinvester
		}elseif ($data == 6) {
			Session::set('role','owner');
			#isowner
		}else{
			Session::set('role',random_int(100,999));
			#user
		} 
	}
	/*
	** GET USERS 
	**
	*/
	protected function user_ext($user_ext_id){
		return $this->db->select('SELECT user_ext_id ,  role, level FROM users WHERE user_ext_id =:user_ext_id',array(':user_ext_id'=>$user_ext_id));
	}
	protected function user($user_id){
		return $this->db->select('SELECT user_id ,  role, level FROM users WHERE user_id =:user_id',array(':user_id'=>$user_id));
	}

}

?>