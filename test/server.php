<?php
include(dirname (dirname(__FILE__))."/class_lib.php");
	$server = new apiServer();
	$publicKey=$server->getPublicKey();
	echo "PublickKey: $publicKey<br>\n";
	//
	// here the code to retrieve PRIVATEKEY
	//
	$privateKey="PRIVATEKEY";
	$server->setPrivateKey($privateKey);
	echo ($server->isValid() ? "TRUE": "FALSE");
	echo "\n<br>\n";
	echo $server->signature;
	$server->echoAll();
?>