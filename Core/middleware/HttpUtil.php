<?php 

$error = new Error_Redirect();
/**
 * 
 */
class HttpUtil
{
	
	private static $errors = array(
		'400' =>'400 Bad Request',
		'401' =>'401 Unauthorized',
		'402' =>'402 Payment Required',
		'403' =>'403 Forbidden',
		'404' =>'404 Not Found',
		'405' =>'405 Method Not Allowed',
		'406' =>'406 Not Acceptable',
		'407' =>'407 Proxy Authentication Required',
		'408' =>'408 Request Timeout',
		'409' =>'409 Conflict',
		'410' =>'410 Gone',
		'411' =>'411 Length Required',
		'412' =>'412 Precondition Failed',
		'413' =>'413 Payload Too Large',
		'414' =>'414 Request-URI Too Long',
		'415' =>'415 Unsupported Media Type',
		'417' =>'416 Requested Range Not Satisfiable',
		'402' =>'417 Expectation Failed',
		'421' =>'421 Misdirected Request',
		'422' =>'422 Unprocessable Entity',
		'423' =>'423 Locked',
		'424' =>'424 Failed Dependency',
		'426' =>'426 Upgrade Required',
		'428' =>'428 Precondition Required',
		'429' =>'429 Too Many Requests',
		'431' =>'431 Request Header Fields Too Large',
		'444' =>'444 Connection Closed Without Response',
		'451' =>'451 Unavailable For Legal Reasons',
		'499' =>'499 Client Closed Request',
		'5xx' =>'5×× Server Error',
		'500' =>'500 Internal Server Error',

		'501' =>'501 Not Implemented',
		'502' =>'502 Bad Gateway',
		'503' =>'503 Service Unavailable',
		'504' =>'504 Gateway Timeout',
		'505' =>'505 HTTP Version Not Supported',
		'506' =>'506 Variant Also Negotiates',
		'507' =>'507 Insufficient Storage',
		'508' =>'508 Loop Detected',
		'510' =>'510 Not Extended',
		'511' =>'511 Network Authentication Required',
		'599' =>'599 Network Connect Timeout Error'
	);

	public static function handle($code,$msg){
		$error_code = ''.$code;
		header('HTTP/1.0  ' . $error_code . ' ' . self::$errors[$error_code]);
		echo json_encode(['result'=>'error', 'msg' =>msg]);
		exit();       

	}
	public static function ValidateToken(){
		$headers = get_headers();
		$token = $headers['Bearer'];
		$result = JWT::validateToken($token);
		if($result['result'] == 'succes'){
			return true;
		}
		self::handle(401,$result['msg']);
		die();
	}
	public static function ValidateClient(){

	}
    public static function setHeader($header = array()){
		$headers = $header;
	}
	public static function responce($data){
		header('HTTP/1.0  ' . 200 . ' ' . self::$errors[$code]);
		echo json_encode($data);
		# code...
	}
	

}




?>