<?php
include(dirname (dirname(__FILE__))."/class_lib.php")
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Headers</title>
</head>
<body>

<?php
	$test = new apiServer("PRIVATEKEY");
	$test->handle(getallheaders(),@file_get_contents('php://input'));
	echo ($test->isValid() ? "TRUE": "FALSE");
	echo "\n<br>\n";
	echo $test->signature;
	$test->echoAll();
?>
</body>
</html>