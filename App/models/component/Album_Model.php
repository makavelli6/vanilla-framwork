<?php 

class Album_Model extends Model
{
	function __construct()
	{
		parent::__construct();
	}

	public function albumList(){
		return $this->db->select('SELECT * FROM album WHERE artist_id = :artist_id', array('artist_id' =>$_SESSION['artist']));
	}

	public function get_playlist($playlist_id){
		return $this->db->select('SELECT * FROM playlist WHERE playlist_id= :playlist_id',array('playlist_id'=>$playlist_id));
	}
	
	public function create_album($data){
		if(isset($data['cover'])){
			$dir = 'cover';
			$cover = Image_Upload::hundler($dir, $data['cover']);
		}else{
			$cover = 'data/uploads/images/cover/audioPlug.jpg';
		}
	$this->db->insert('album', array(
		'album_name'=>$data['album_name'],
		'album_track'=>0,
		'album_txt'=> $data['album_note'],
		'artist_id'=>$data['artist_id'],
		'price'=>$data['price'],
		'release_date'=>$data['release_date'],
		'privacy'=>$data['privacy'],
		'cover'=>$cover['coverdir'],
		'state'=>$data['state']

	));
	}
	public function delete_album($id){
		$data['id']=$id;
		$this->db->delete('album',"`album_id`= {$data['id']} AND artist_id = '{$_SESSION['artist']}'");
	}


	public function edit_album($data){
		$postData = array(
		'album_name'=>$data['album_name'],
		'album_track'=>0,
		'album_txt'=> $data['album_note'],
		'artist_id'=>$data['artist_id'],
		'price'=>$data['price'],
		'release_date'=>$data['release_date'],
		'privacy'=>$data['privacy'],
		'cover'=>$data['cover'],
		'state'=>$data['state']
	);

		$this->db->update('playlist',$postData,"`playlist_id`={$data['playlist_id']} AND userid = '{$_SESSION['userid']}'");
	}


}













 ?>