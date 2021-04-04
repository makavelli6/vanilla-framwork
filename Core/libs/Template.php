<?php 

/**
 * 
 */
class Template{
	private $var = array();
	public $path = ROOT.TEMPLATES;

	public function __call($name, $aguments){
		if($name === 'register'){
			if(is_array($argument[0])){
				foreach ($argument[0] as $key => $value) {
					$this->assign($key , $value);
					}
			}
			
		}
	}
	
	public function assign($key,$value){
		$this->var[$key] = $value;

	}
	public function render($name, $noInclude = false){
		if($noInclude == true){
			$this->GenTemplate($name);
		}else{
			require_once ROOT.VIEWS.'./App/Head.php';
			$this->GenTemplate($name);
			require_once ROOT.VIEWS.'./App/Tail.php';
		}
		
	}

	private function GenTemplate($templateName){
		$path = $this->path.$templateName.'.html';
		if(file_exists($path)){
			$content = file_get_contents($path);
			foreach ($this->var as $key => $value) {
				$content  = preg_replace('/\['.$key.'\]/', $value, $content);
			}
			$content = preg_replace('/\<\!\-\- if(.*) \-\->/', '<php if ($1):', $content);
			$content = preg_replace('/\<\!\-\- else \-\->/', '<php else :', $content);
			$content =  preg_replace('/\<\!\-\- endif \-\->/', '<php endif :', $content);
			eval(' ?>'.$content.'<?php');
		}else{
			die('<h1>Template Error</>');
		}
	}


}

 ?>