<?php

class apiServer{
	public $errno;
	public $errmsg;
	public $errverbose;
	
	private $method;
	private $uri;
	private $headerHttp;
	private $rawData;
	private $privateKey;
	
	
	function __construct($pKey) 
	{	
		$this->privateKey=$pKey;
		$this->method=$this->getMethod();
		$this->uri=$_SERVER['REQUEST_URI'];
		$this->headerHttp=array_change_key_case(getallheaders(),CASE_LOWER);
		$this->rawData=@file_get_contents('php://input');
	}
		
	function isValid()
	{
		if ($this->getSignature() != $this->buildSignature()){
			$this->errno="401";
			$this->errmsg="Unauthorized";
			$this->errverbose="signature doesn't match";
			return False;
		}elseif(strtotime("now")-strtotime($this->getDateTime()) > 60*60 ){
			$this->errno="403";
			$this->errmsg="Forbidden";
			$this->errverbose="The date given in the header is too old.";
			return False;			
		}else{
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
		if (isset($this->headerHttp['x-http-method-override'])){
			return $this->headerHttp['x-http-method-override'];
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
		return @file_get_contents('php://input'); 
	}
	
	function getHost()
	{
		if (isset($this->headerHttp['host'])){
			return $this->headerHttp['host'];
		}else{
			return "nope";
		}		
	}
	
	function getPublicKey()
	{
		if (isset($this->headerHttp['x-public'])){
			return $this->headerHttp['x-public'];
		}else{
			return "nope";
		}		
	}
	
	function getDateTime()
	{
		if (isset($this->headerHttp['x-date'])){
			return $this->headerHttp['x-date'];
		}else{
			return "nope";
		}		
	}
	
	function getSignature()
	{
		if (isset($this->headerHttp['authorization'])){
			$sTemp=explode(" ",$this->headerHttp['authorization']);
			return $sTemp[1];
		}else{
			return "nope";
		}		
	}
	
	function response() {
	}
	
	function echoAll(){
		echo "privateKey = ".$this->privateKey." <br>\n";
		echo "publicKey = ".$this->getPublicKey()." <br>\n";
		echo "signature = ".$this->getSignature()." <br>\n";
		echo "signatureBuilt = ".$this->buildSignature()." <br>\n";
		echo "dateTime = ".$this->getDateTime()." <br>\n";
		echo "valid = ".($this->isValid() ?"true":"false")." <br>\n";
		echo "method = ".$this->getMethod()." <br>\n";
		echo "uri = ".$this->getUri()." <br>\n";
		echo "rawData = ".$this->getRawData()." <br>\n";
		echo "host = ".$this->getHost()." <br>\n";
		echo "errno = ".$this->errno." <br>\n";
		echo "errmsg = ".$this->errmsg." <br>\n";
		echo "errverbose = ".$this->errverbose." <br>\n";
		echo "<pre>";
		print_r($this->headerHttp);
		echo "</pre>";
	}
	
}
?>