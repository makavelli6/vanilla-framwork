<?php 

class ServerState
{
	public $state;
	public $priority;
	public $job;
	public $name;

	


	public	function __construct($name ,array $peer = []){
		$this->name= $name;
		$this->peers = $peers;
		$this->save();
	}

	public function save(){
		file_put_contents(self::file($this->name), serialize($this));
	}
	public static function load($name):self{
		return unserialize(file_get_contents(self::file($name)));
	} 

	public function reaload(){
		if($state = self::load($this->name)){
			$this->blockchain = $state->blockchain;
			$this->peers[$peer] = true;
		}
	}


	public function  updateMine(){
		$session = $this->randomSession();
		$version = $this->invrementVersion();
		$this->state[this->port] = ['user'=>$this->user,'session' =>$session, 'version' = $version];
		$this->save();
	}

	public function update(State $state){
		if($this->blockchain){
			$this->blockchain->update($state->blockchain);
		}
		else{
			$this->blockchain = $state->blockchain;
		}
		foreach (array_keys($state->peer) as $peer) {
			$this->peers[$peer] = true;
		}
		$this->save();
	}

public function __toString(){
	$data = [];
	foreach ($this->state as $port => $d) {
		$data[] = sprintf('%s/%s -- %d/%s', $port, $id['user'], $d['version'], substr($d['session'], 0, 40));
	}
	return implode("\n", $data);
}
}



 ?>