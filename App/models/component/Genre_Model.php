<?php 	

class Genre_Model extends Model
{
	
	public function __construct()
	{
		parent::__construct();
	}
	public function genreList(){
		return $this->db->select('SELECT genre_id , genre_name , created_on, popularity FROM genre');
	}


	public function singleGenre($genre_id){
		return $this->db->select('SELECT genre_id , genre_name , created_on, popularity FROM genre WHERE genre_id =:genre_id',array(':genre_id'=>$genre_id));
	}

	public function create($data){
		$this->db->insert('genre', array(
			'genre_name'=>$data['genre_name'],
			'created_on'=>date('Y-m-d H:i:s'),
			'popularity'=>$data['popularity'],
			'image'=>$data['image']
		));
	}
	public function delete($genre_id){
		$this->db->delete('genre',"genre_id = $genre_id");
	}


	public function editSave($data){
		$postData =array('genre_name'=>$data['genre_name']);
		$this->db->update('genre',$postData,"`genre_id`={$data['genre_id']}");
	}


}