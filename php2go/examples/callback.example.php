<?php

	// $Header: /www/cvsroot/php2go/examples/callback.example.php,v 1.3 2005/06/08 23:01:04 mpont Exp $
	// $Revision: 1.3 $
	// $Date: 2005/06/08 23:01:04 $
	// vim: set expandtab tabstop=4 shiftwidth=4:

	require_once("../p2gConfig.php");
	import('php2go.util.Callback');
	import('php2go.net.*');
	
	$url =& new Url();
	
	println('<B>PHP2Go Examples</B> : php2go.util.Callback<BR>');
		
	function myDummyFunction($arg1, $arg2) {
		return $arg1 + $arg2;
	}
	
	$callback =& new Callback();
		
	// validate and invoke a callback using object and method
	println('<B>1) Callback using object instance and method : php2go.net.Url instance + encode method</B>');
	$callback->setFunction(array($url, 'encode'));
	$return = $callback->invoke(array(HttpRequest::basePath() . '?arg=test', 'q'), TRUE);
	println('Return: ' . $return . '<BR>');
	
	// validate and invoke a callback using a static method call
	println('<B>2) Callback using a static method call : php2go.net.HttpRequest + basePath method</B>');
	$callback->setFunction('HttpRequest::basePath');
	$return = $callback->invoke();
	println('Return: ' . $return . '<BR>');
	
	// validate and invoke a callback using a simple function
	println('<B>3) Callback using a simple function call</B>');
	$callback->setFunction('myDummyFunction');
	$return = $callback->invoke(array(1,1), TRUE);
	println('Return: ' . $return . '<BR>');
	
	// force an error without disabling the internal error handling of the class
	println('<B>4) Force an error without disabling the internal error of the class</B>');
	$callback->setFunction('i am not a funcion');
	$return = $callback->invoke();
	println('Return: ' . $return . '<BR>');		
	
?>