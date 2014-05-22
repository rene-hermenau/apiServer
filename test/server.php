<?php
include(dirname (dirname(__FILE__))."/class_lib.php");
	$server = new apiServer();
	$publicKey=$server->getPublicKey();
	//
	// here the code to retrieve PRIVATEKEY
	//
	$privateKey="PRIVATEKEY";
	if ($server->handle($privateKey)) {
		//the request can be processed
		$server->response(200,"OK");
	}else{
		//Error
		$server->response($server->errno);
	}
?>