<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>client</title>
</head>

<body>
<?php
$date=gmdate("D, j M Y H:i:s \G\M\T");
$privateKey="PRIVATEKEY";
$publickKey="PUBLICKEY";
$host=$_SERVER['HTTP_HOST'];
$method="PATCH";
$uri=str_replace("client","server",$_SERVER['REQUEST_URI']);

$signature="";
$signature.="date=".urlencode($date);
$signature.="&host=".urlencode($host);
$signature.="&method=".urlencode($method);
$signature.="&public=".urlencode($publickKey);
$signature.="&uri=".urlencode($uri);
$signature=base64_encode(hash_hmac('sha256',$signature,$privateKey));

$link="http://$host$uri";
$opts = array(
  'http'=>array(
	'method'=>$method,
	'header'=>"Authorization: signature $signature\r\n" .
			  "x-public: $publickKey\r\n" .
			  "date: $date\r\n" .	  
			  "content-type: application/json\r\n" .
			  "X-HTTP-Method-Override: $method\r\n"
  )
);
$context = stream_context_create($opts);
$response=@file_get_contents($link,false,$context);
echo "<pre>";
print_r($http_response_header);
echo "</pre>";
echo $response;
?>
</body>
</html>