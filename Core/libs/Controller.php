<?php 

class Controller
{
	
	function __construct()
	{
		//echo 'MAIN contraller <br>';
		$this->view = new View();
		$this->template = new Template();

        if(Host_ != '' && User_Name_ != '' && Password_ != '' && Port_ ){
            $this->mail = new Mailer(Host_, SMTP_Auth_, User_Name_, Password_, Port_);
        }

        
		

		
		$this->template->cache_enabled = false;
		$this->template->cache_path = ROOT.'/Store/Cache/Pages/';
		$this->template->file_ext = '.html';
		
	}
	public function loadModel($name, $modelPath){

		$path = $modelPath.$name.'_model.php';

		if(file_exists($path)){
			require $modelPath.$name.'_model.php';
			$modelName = $name.'_Model';
			$this->model = new $modelName();
		}
	}

	protected function FetchGet( $filters)
    {
        $data  = array( );
        foreach ($filters as $filter) {
            if(key_exists($filter ,$_GET)){
                //add a methord to sanitise the input
                $data[$filter] = $this->filter($_GET[$filter]);
            }
        }
        return $data;
    }

    protected function FetchAllGet()
    {
        $data  = array( );
        foreach ($_GET as $key => $value) {
            $data[$key] = $this->filter($value);
       }
       return $data;
    }

	protected function filter($str)
	{
		return $str;
	}

	protected function FetchPost( $filters)
    {
        $data  = array( );
        foreach ($filters as $filter) {
            if(key_exists($filter ,$_POST)){
                //add a methord to sanitise the input
                $data[$filter] = $this->filter($_POST[$filter]);
            }
        }
        return $data;
    }

    protected function FetchAllPost( $filter)
    {
        $data  = array( );
        foreach ($_POST as $key => $value) {
            $data[$key] = $this->filter($value);
       }
       return $data;
    }

}

 ?>