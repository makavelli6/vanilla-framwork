<?php 
/**
 * 
 */
class Xarray 
{
	private $_arg = array();
	private $_value = array();

	function __construct($value = null){
		$this->_value = $value;
		$this->_arg = new Lite_Array();
	}


	public function __call($name, $aguments)
	{
		if($name === 'add' || $name === 'Add' || $name === 'Push' || $name === 'push'){
			for($i = 1; $i = count($aguments)-1; $i++){
				$this->value = $this->_arg->add($this->value, $aguments[$i]);
			}
		}elseif($name === 'remove_end' || $name === 'Pop' || $name === 'pop' ){

			$this->value = $this->_arg->remove_end($this->value);

		}elseif($name === 'empty' || $name === 'Empty'){

			$this->value = $this->_arg->empty($this->value);

		}elseif($name === 'length' || $name === 'Length' || $name === 'Count'){

			$this->value = $this->_arg->Length($this->value);

		}elseif($name === 'isValid' || $name === 'Valid'){

			$this->value = $this->_arg->isValid($this->value);

		}elseif($name === 'keys_to_Lower' || $name === 'LowerCaseKeys'){

			$this->value = $this->_arg->array_keys_lower($this->value);

		}elseif($name === 'keys_to_Upper' || $name === 'UpperCaseKeys'){

			$this->value = $this->_arg->array_keys_upper($this->value);

		}elseif($name === 'Reverse' || $name === 'reverse'){

			$this->value = $this->_arg->reverse($this->value);

		}elseif($name === 'top' || $name === 'Top'){

			return $this->_arg->top($this->value);

		}elseif($name === 'bottom' || $name === 'end'){

			return $this->_arg->bottom($this->value);

		}elseif($name === 'Value' || $name === 'value'){

			return $this->value;
		}elseif($name === 'add_to_top' || $name === 'addTop'){

			$a = $this->value;
			array_reverse($a);
			for($i = 1; $i = count($aguments)-1; $i++){
				$a = $this->_arg->add($a, $aguments[$i]);
			}
			array_reverse($a);
			$this->value = $a;

		}elseif($name === 'Shift' || $name === 'shift'){
			/*Remove the first element and from an array, and return the value of the removed element*/

			return array_shift($this->value);

		}elseif($name === 'Dubles' || $name === 'unque'){

			array_unique($this->value);
			
		}elseif($name === 'combine' || $name === 'marge_with'){
			$a = $this->value; 
			for($i = 1; $i = count($aguments)-1; $i++){
				$a = array_merge($a,$aguments[$i]);
			}
			$this->Value = $a;
		}elseif($name === 'keyExists' || $name === 'key_exists'){

			return array_key_exists($aguments,$this->value);

		}elseif($name === 'key' || $name === 'keys'){
			
			return array_keys($a);

		}elseif($name === 'shuffle' || $name === 'Shuffle'){

			shuffle($this->value);

		} 

	}
	
	

}



?>