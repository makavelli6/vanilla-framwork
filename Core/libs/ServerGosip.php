<?php 
class ServerGosip{
    private $name;
	private $key;
	private $state;
	private $port;
	function __construct(string $name, int $port,?int $peerPort){
		$this->name = $name;
		$this->port = $port;
		$this->state = $state;
		$this->key = new key($name) ;
		$peers = [$port => true];

		$this->state =new State($name, $blockchain,$peers);
    }


    public function withPeer($port){
        $peerState = $this->gossip($port);
        if(!$peerState){
            unset($this->state->peer[$port]);
            $this->state->save();
        }else{
            $this->state->update($peerState);
        }

    } 

    public function reaload(){
        $this->state = file_exists($this->file) ? json_decode(file_get_contents($this->file),true):[];
    }
    private function gossip($port): ?State
    {
        $data = base64_encode(serialize($this->state));
        $peerState = @file_get_contents('http://localhost:'.$port.'/gosip',null);
    }


    public function  updateMine(){
        $session = $this->randomSession();
        $version = $this->invrementVersion();
        $this->state[$this->port] = ['user'=>$this->user,'session' =>$session, 'version' => $version];
        $this->save();
    }
public function update($state){
	if(!$state){ return; }

    foreach ($state as $kport => $data) {
        if ($port == $this->port) {
            continue;
        }
        if(!isset($this->state[$port]) || !isset($data['version'] ) || !isset($data['session'])){
            continue;
        }
        if(!isset($this->state[$port]) || $data['version']>$this->state[$port]['version']){
            $this->state[$port] = $data;
        }
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