<?php
include(dirname (dirname(__FILE__))."/class_lib.php");
	$server = new apiServer();
	$publicKey=$server->getPublicKey();
	//
	// here the code to retrieve PRIVATEKEY
	//
	$privateKey="PRIVATEKEY";
	if ($server->handle($privateKey,123)) {
		//the request can be processed
		
		$responseCode=array(200,201,202,304);
		$ind=array_rand($responseCode);
		$server->response($responseCode[$ind],"OK");
	}else{
		//Error
		$server->response($server->errno);
	}
?>