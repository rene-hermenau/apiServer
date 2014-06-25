<?php

class apiServer{
	const REQUESTLIFETIME = 180; 	//The request mustn't be more than REQUESTLIFETIME seconds old
	const RATELIMIT = 5;			// Rate limit
	const RATELIMITWINDOW = 10; 	// Rate limit window in seconds
	public $errno;
	public $errmsg;
	
	private $method;
	private $uri;
	private $rawData;
	private $privateKey;
	private $userKey;
	private $rateUsed;
	private $httpCodes = array(
			'200' => 'OK',
			'201' => 'Created',
			'202' => 'Accepted',
			'204' => 'No Content',
			'301' => 'Moved Permanently',
			'302' => 'Found',
			'304' => 'Not Modified',
			'400' => 'Bad Request',
			'401' => 'Unauthorized',
			'403' => 'Forbidden',
			'404' => 'Not Found',
			'405' => 'Method Not Allowed',
			'406' => 'Not Acceptable',
			'409' => 'Conflict',
			'410' => 'Gone',
			'429' => 'Too Many Request',
			'500' => 'Internal Server Error',
			'501' => 'Not Implemented',
			'503' => 'Service Unavailable'
		);	
		
	function __construct() 
	{	
		$this->uri=$_SERVER['REQUEST_URI'];
		$this->rawData=@file_get_contents('php://input');
		$this->method=$this->getMethod();
	}

	function handle($pKey, $userKey) 
	{	
		$this->privateKey=$pKey;
		$this->userKey=$userKey;
		$memcache = new Memcached;
		if ($this->rateUsed = $memcache->get($this->memcacheKey())) {
			$memcache->increment($this->memcacheKey(),1);
			$this->rateUsed+=1;
		}else{
			$memcache->add($this->memcacheKey() , 1 , $this->getNextResetTime());
			$this->rateUsed=1;
		}
		return $this->isValid();
	}

	function handleError($code,$msg) 
	{	
		$this->privateKey=$pKey;
		return $this->isValid();
	}
		
		
	function isValid()
	{
		// check the validity of the request
		if ($this->getSignature() != $this->buildSignature()){
			$this->errno="401";
			$this->errmsg="The signature doesn't match";
			return false;
		}elseif (!$this->rateUsed || $this->rateUsed > apiServer::RATELIMIT) {
			$this->errno="429";
			$this->errmsg="Too Many request.";
			return false;			
		}elseif(abs(strtotime("now")-strtotime($this->getDateTime())) > apiserver::REQUESTLIFETIME ){
			$this->errno="403";
			$this->errmsg="The date given in the header is in the past or the future. Check your clock.";
			return false;
		}else{
			$this->errno="0";
			$this->errmsg="";
			return true;
		}
	}
	
	private function buildSignature()
	{
		$signatureBuilt="";
		$signatureBuilt.="date=".urlencode($this->getDateTime());
		$signatureBuilt.="&host=".urlencode($this->getHost());
		$signatureBuilt.="&method=".urlencode($this->getMethod());
		$signatureBuilt.="&public=".urlencode($this->getPublicKey());
		$signatureBuilt.="&uri=".urlencode($this->getUri());
		return base64_encode(hash_hmac('sha256',$signatureBuilt,$this->privateKey));
	}
	
	function getMethod()
	{ 
		if (isset($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'])){
			return $_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'];
		}else{
			return $_SERVER['REQUEST_METHOD'];
		}
	}
	
	function getUri()
	{ 
		return $_SERVER['REQUEST_URI']; 
	}
	
	function getRawData()
	{ 
		return $this->rawData; 
	}
	
	function getHost()
	{
		if (isset($_SERVER['HTTP_HOST'])){
			return $_SERVER['HTTP_HOST'];
		}else{
			return "nope";
		}		
	}
	
	function getPublicKey()
	{
		if (isset($_SERVER['HTTP_X_PUBLIC'])){
			return $_SERVER['HTTP_X_PUBLIC'];
		}else{
			return "nope";
		}		
	}
	
	function getDateTime()
	{
		if (isset($_SERVER['HTTP_DATE'])){
			return $_SERVER['HTTP_DATE'];
		}else{
			return "nope";
		}		
	}
	
	function getSignature()
	{
		if (isset($_SERVER['HTTP_AUTHORIZATION'])){
			$sTemp=explode(" ",$_SERVER['HTTP_AUTHORIZATION']);
			return $sTemp[1];
		}else{
			return "nope";
		}		
	}
	
	function getNextResetTime()
	{
		return ((int) (time() / apiServer::RATELIMITWINDOW) + 1 ) * apiServer::RATELIMITWINDOW;
	}
	
	function response($httpCode, $content="") {
		$date=gmdate("D, j M Y H:i:s \G\M\T");
		header("HTTP/1.1 $httpCode ".$this->httpCodes[$httpCode]);
		header("Date: $date");
		header("Content-Type: application/json");
		header("X-Rate-Limit-Limit: ".apiServer::RATELIMIT);
		header("X-Rate-Limit-Remaining: ".max (0,(apiServer::RATELIMIT - $this->rateUsed)));
		header("X-Rate-Limit-Reset: ".$this->getNextResetTime());
		echo $content;
	}
	
	private function memcacheKey()
	{
		return "RATEUSED/".$this->privateKey."/".$this->userKey;
	}
	
	
}
?>