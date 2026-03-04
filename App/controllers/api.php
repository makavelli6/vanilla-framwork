<?php

class  Api extends Controller {
    private $api;
	
	function __construct()
	{
		parent::__construct();        
        $this->loadService('api');
	}
    function index(){
        // Using the newly implemented Service Architecture
        $data = $this->service->genreList();
        
        $this->view->Json([
            'status' => 'success',
            'data' => $data
        ]);
    }
    function john($data = '',$data2 = '')
    {
    	echo "My name is Lucky";
    	echo "<br>".$data;
    	echo "<br>".$data2;
    }
}

 ?>