<?php

/**
 * 
 */

class Get_Model extends Model{
	public function __construct(){
		parent::__construct();
	}
	#get audio
	public  function get_track_md5($track_MD5){
		$query = $this->db->select('SELECT * FROM track WHERE track_MD5 = :track_MD5', array('track_MD5' =>$track_MD5));
		return $query;
	}
	#get audio
	public  function get_track_by_id($track_id){
		$query = $this->db->select('SELECT * FROM track WHERE track_id = :track_id', array('track_id' =>$track_id));
		$track = mysqli_fetch_array($query);
		return $track;

	}
	#get this get tacks where all
	public  function get_tracks_artist($track_artist){
		$query = $db->select('SELECT * FROM track WHERE track_artist = :track_artist', array('track_artist' =>$track_artist));
		$track = mysqli_fetch_array($query);
		return $track;
	}

	#get all tracks where
	public  function get_album_tracks($album_id){
		$query = $db->select('SELECT * FROM track WHERE album_id = :album_id', array('album_id' =>$album_id));
		$track = mysqli_fetch_array($query);
		return $track;
	}
	#get all tracks where
	public function get_albums_by_artist($artist_id){
		$query = $db->select('SELECT * FROM album WHERE artist_id = :artist_id', array('artist_id' =>$artist_id));
		return $query[0];
	}

	#get playlist
	public  function get_state_album($state){
		$query = $this->db->select('SELECT * FROM album WHERE state = :state', array('state' =>$state));
		$track = mysqli_fetch_array($query);
		return $track;

	}

	#get artist
	public  function get_artist_detail($artist_hash){
		$query = $this->db->select('SELECT * FROM artist WHERE artist_hash = :artist_hash', array('artist_hash' =>$artist_hash));
		$track = mysqli_fetch_array($query);
		return $track;
	}
	#get all ablum by
	public  function get_user_playlist($user_id){
		$query = $this->db->select('SELECT * FROM playlist WHERE user_id = :user_id', array('user_id' =>$user_id));
		$track = mysqli_fetch_array($query);
		return $track;
	}
	public  function load_playlist($id){ 
		$query = $this->db->select('SELECT * FROM album WHERE artist_id = :artist_id', array('artist_id' =>$artist_id));
		$track = mysqli_fetch_array($query);
		return $track;
	}
	public function albumList($artist_id)
	{
		$query = $this->db->select('SELECT album_id , album_name FROM album WHERE artist_id = :artist_id', array('artist_id' =>$artist_id));
		return $query;
		
	}



}



 ?>