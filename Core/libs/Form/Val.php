<?php  
class Val
{
	private $regexes = Array(
            'date' => "/^[0-9]{1,2}[-/][0-9]{1,2}[-/][0-9]{4}\$/",
            'amount' => "/^[-]?[0-9]+\$/",
            'number' => "/^[-]?[0-9,]+\$/",
            'alfanum' => "/^[0-9a-zA-Z ,.-_\\s\?\!]+\$/",
            'not_empty' => "/[a-z0-9A-Z]+/",
            'words' => "/^[A-Za-z]+[A-Za-z \\s]*\$/",
            'phone' => "/^[0-9]{10,11}\$/",
            'zipcode' => "/^[1-9][0-9]{3}[a-zA-Z]{2}\$/",
            'plate' => "/^([0-9a-zA-Z]{2}[-]){2}[0-9a-zA-Z]{2}\$/",
            'price' => "/^[0-9.,]*(([.,][-])|([.,][0-9]{2}))?\$/",
            '2digitopt' => "/^\d+(\,\d{2})?\$/",
            '2digitforce' => "/^\d+\,\d\d\$/",
            'anything' => "/^[\d\D]{1,}\$/"
    );
	
	function __construct()
	{
		# code...
	}
    public function isEmail($value)
    {
       if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
           return $value.'Invalid email';
       }
    }
	public function minlength( $data, $arg){
		if(strlen($data)<$arg){
			return "Your string  can only be $arg  long";
		}
	}
    public function isPhone($data)
    {
        if(!preg_match($this->regexes['phone'], $data)){
            return "Invalid  PhoneNumber - $data -";
       }
    }
    public function isZipcode($data)
    {
        if(!preg_match($this->regexes['zipcode'], $data)){
            return "Invalid  zipcode - $data -";
       }
    }
    public function isPrice($data)
    {
        if(!preg_match($this->regexes['price'], $data)){
            return "Invalid  Price - $data -";
       }
    }
    public function isWord($data)
    {
        if(!preg_match($this->regexes['words'], $data)){
            return "Invalid  Word or Name - $data -";
       }
    }

    public function isAlpaNum($data)
    {
        if(!preg_match($this->regexes['alfanum'], $data)){
            return "Invalid AlphaNumeric Charactors - $data -";
       }
    }
    public function isDate($data)
    {
       if(!preg_match($this->regexes['date'], $data)){
            return "Invalid Date - $data -";
       }
    }
	public function maxlength( $data, $arg){
		if(strlen($data)>$arg){
			return "Your string  can only be $arg  long";
		}

}
	public function digit( $data){
		if(ctype_digit($data) == false){
			return "Your string   must be a digit ";
		}
    }
	public function empty($data){
		if ($data = '' || $data = " ") {
			return 'is empty';
		}

	}
    public function __call($name,$argument){
        throw new Exception("$name does  not exist inside  of  ".__CLASS__ );
    }

}

?>