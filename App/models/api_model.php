<?php
/**
 * 
 */
class Api_Model extends Model
{

	
	public function genreList(){
		return $this->db->select('SELECT genre_id , genre_name , created_on, popularity FROM genre');
	}

	public function singleGenre($genre_id){

		return $this->db->select('SELECT genre_id , genre_name , created_on, popularity FROM genre WHERE genre_id =:genre_id',array(':genre_id'=>$genre_id));
	}

	public function create_genre($data){

	$this->db->insert('genre', array(
		'genre_name'=>$data['genre_name'],
		'created_on'=>date('Y-m-d H:i:s'),
		'popularity'=>$data['popularity'],
		'image'=>$data['image']
	));
	}
	public function delete_genre($genre_id){
		$this->db->delete('genre',"genre_id = $genre_id");
	}


	public function edit_genre($data){
		$postData =array(
		'genre_name'=>$data['genre_name']
	);
		$this->db->update('genre',$postData,"`genre_id`={$data['genre_id']}");
	}
	/*
	**ADMIN FUCTIONS
	**
	*/
	public  function get_all_users(){
		return $this->db->select('SELECT u.user_id , u.user_email, u.user_fname, u.user_lname, r.role_name FROM users AS u JOIN role AS r ON u.role = r.role_value');
	}
	public  function search_users(){
		return $this->db->select('SELECT user_id , user_email, user_fname, user_lname, role FROM users');
	}
	
	
}

 ?>