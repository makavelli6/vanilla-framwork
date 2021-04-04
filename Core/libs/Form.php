 <?php 

/**
 * 
 *-FIll out form
 *-POST to Php
 *-Sanitize 
 *-Validate
 *Return Data
 *Write to DataBase
  */
require 'Form/Val.php';


class Form 
{
	// @var array $_currentItem The immediately posted data  
	private $_currentItem  = null;

	// @var array $_postdata stores  the posted data  
	private $_postData = array();

	// @var array $_val validator object  
	private $_val = array();
	// 
	// @var array $_error hold s the errors from the form  
	private $_error = array();
	// 
	// instanciate th validator
	public function __construct()
	{
		$this->_val = new Val();
	}
	/**
	*post- this is run $_POST
	@param string $field - the HTML fieldname to post
	*/
	public function post($field){
		$this->_postData[$field]=$_POST[$field];
		$this->_currentItem =$field;
		return $this;

	}
	// fetch returns the posted data;
	// @param mixed $filedName;
	// @return mix String or array
	// 
	// 
	public function fetch($fieldName = false){
		if($fieldName){
			if (isset($this->_postData[$fieldName])) 
				return $this->_postData[$fieldName];
			else
				return false;
		}else{
			return $this->_postData;
	
		}

	}
	/**
	*val - This is to validate
	*@param string $typeOfValidator  a method from  the Form/Val.php 
	*@param string $arg  a property to bbe validated
	*/
	public function val($typeOFValidator,$arg = null){
		if($arg == null){
			 $error =$this->_val->{$typeOFValidator}($this->_postData[$this->_currentItem]);

		}else{
			 $error =$this->_val->{$typeOFValidator}($this->_postData[$this->_currentItem],$arg);
		}

		 if($error){
		 	$this->_error[$this->_currentItem] = $error;
		 }
		 //$val->minlength(,$arg)



		return $this;

	}
	/**
	*
	*submit - Handles the form  and  throws  the exeption upon an error
	*@throws exeption
	*@return bool
	*/
	public function submit(){
		if(empty($this->_error)){
			return true;
		}else{
			$str = '';
			foreach ($this->_error as $key => $value) {
				$str .=$key.'=>'.$value."\n";
			}
			throw new Exception($str);
			
 		}
	}
}






 ?>