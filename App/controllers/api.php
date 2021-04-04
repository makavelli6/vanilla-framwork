<?php

class  Api extends Controller {
    private $api;
	
	function __construct()
	{
		parent::__construct();        
	}
    function index(){
        $data = array('jeff' => 'name', 'fish' => 'Getto');
        $this->view->Json($data);
    }
    function john($data = '',$data2 = '')
    {
    	echo "My name is Lucky";
    	echo "<br>".$data;
    	echo "<br>".$data2;
    }
}

 ?>