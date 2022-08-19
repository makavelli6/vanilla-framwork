<?php 

//namespace Model\Componet;
/**
 * 
 */
class Audio_Upload_Model extends Model
{
	
	public function __construct()
	{
		parent::__construct();
	}

	public function Hundle(){

		if(!isset($_FILES['audio'])){
			return 'no audio to  upload';
		}
		if(!(isset($_POST['title']) || isset($_POST['artist']) || isset($_POST['date']) || isset($_POST['year']))){ return 'invalid upload attempt';}
		$data=array(
			'artist_id' => Session::get('artist'),
			'title' => $_POST['title'],
			'artist' => $_POST['artist'],
			'track_prod' => $_POST['prod'],
			'desc' => $_POST['desc'],
			'year' => $_POST['year'],
			'tags' => $_POST['tags'],
			'genre' => $_POST['genre'],
			'cover' => $_FILES['cover'],
			'audio' => $_FILES['audio'],
			'privacy'=>$_POST['privacy'],
			'album_id'=>$_POST['album'],
			'track_release_date'=>$_POST['date'],
			'override' => $_POST['override'],
			'songtype'=>$_POST['songtype'],
			'music_lisence_id'=>$_POST['access']);

		//songtype {cover, original , remix, }
		if(!isset($data['override'])){
			$data['override'] = 0;
		}

		if(isset($data['price'])){
			$data['track_type'] = $_POST['privacy'];
			$data['price']= $_POST['price'];
		}else{
			$data['track_type'] = 'FREE';
			$data['price']= 0;
			}

			//echo json_encode($data);
			//die();

		$result=$this->upload($data);
	
	}

	private function upload($data){
		
		$imgdir = 'data/uploads/images/cover/';

		$uploader = new Audio_Upload;

		$result=$uploader->Handler($data['audio'],$data['songtype']);
		print_r($result);
		die();
		$result2 = $this->upload_image( $imgdir,$data['cover']);

		if (isset($result['error'])) {
			return $result['error'];
		}elseif (isset($result['success'])) {

			$file=$result['directory'];
			if($this->check_file($file)==0){
				return 'unknown error please try later';
			}
			if($this->check_duble($file) == 0){
				File::delete_file($file);
				return 'Song is a duplicate.Audio had already  been uploaded .Please Contact Us  if  there is  any  problem';
			}
			$data['directory'] = $result['directory'];
			$data['md5'] = md5_file($data['directory']);
			$data['coverimg'] = $result2['coverdir'];

			$this->insertTrack($data);
			$result['md5'] = $data['md5'];
			return $result;
		}

	}

	private function insertTrack($data){
		$date = date("F j, Y");
		print_r($data);
		die();

		//id,title,description, keyword,user,privacy,date, $date ,md5,views,vid_id,file_md, filename,image,no,channelname
		$this->db->insert('track', array(
			'track_title'=>$data['title'] ,
			'artist_id'=> $data['artist_id'] ,//artist_id
			'track_artist'=>$data['artist'] ,
			'track_prod'=>$data['track_prod'] ,
			'track_ft'=>'-',//track_ft
			'track_upload_date'=> date('Y-m-d'),//track_upload_date
			'track_release_date'=> $data['date'] ,//track_release_date
			'track_desc'=>$data['desc'],//track_desc
			'tags'=>$data['tags'],//tags
			'track_type'=>$data['privacy'] ,//track_type
			'price'=> $data['price'] ,//price
			'track_directory'=> $data['directory'],//track_directory
			'song_type'=> $data['songtype'],
			'track_MD5'=> $data['md5'],//track_MD5	
			'privacy'=> $data['privacy'] ,//privacy
			'album_id'=> $data['album_id'] ,//album_id
			'genre_id'=> $data['genre'] ,//genre_id
			'music_lisence_id'=> $_POST['music_lisence_id'],//music_lisence_id
			'views'=>0,	
			'likes'=>0,
			'cover'=> $data['coverimg'] ,//uses gmt time
			'state'=> 0
		));
	}

	private function check_duble($file){
		#$md5list = $this->db->select('SELECT track_MD5  FROM track WHERE track_MD5 = :track_MD5', array('track_MD5' => $track_MD5));
		$track_MD5 = md5_file($file);
		$sth=$this->db->prepare("SELECT track_MD5  FROM track WHERE track_MD5 = :track_MD5");
		$sth->execute(array('track_MD5' => $track_MD5));
		$data = $sth->fetch();
		$count = $sth->rowCount();
	
		if($count > 0){
			File::delete_file($file);
			return 0 ;	
		}else{
			return 1;
		}
	}

	private function check_file($file){
		if (file_exists($file)) {
			return 1;
		}else{
			return 0;
		}
	}




}


 ?>