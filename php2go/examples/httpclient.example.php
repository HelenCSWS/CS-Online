<?php

	// $Header: /www/cvsroot/php2go/examples/httpclient.example.php,v 1.5 2005/06/03 19:32:23 mpont Exp $
	// $Revision: 1.5 $
	// $Date: 2005/06/03 19:32:23 $
	// vim: set expandtab tabstop=4 shiftwidth=4:

	require_once('../p2gConfig.php');
	import('php2go.net.HttpClient');	
	
	/**
	 * routine used to generate the response for the POST and multipart POST requests
	 */
	if (isset($_GET['process'])) {
		println('POST VARS');
		dumpVariable($_POST);
		println('FILES');
		dumpVariable($_FILES);
		println('GET VARS');
		dumpVariable($_GET);
		exit;
	}
	
	$testCase = 1;
	
	/**
	 * create an instance of HttpClient class;
	 * turn on followRedirects and debug flags
	 * define a custom user agent
	 */
	$http = new HttpClient();
	$http->setFollowRedirects(TRUE);
	$http->setUserAgent('MyUserAgent (compatible; MyBrowser; Linux)');
	$http->debug = TRUE;
	
	if ($testCase == 1) {
	
		/**
		 * define the host and perform a simple GET request;
		 * test for the HTTP 502 code (HTTP_STATUS_OK) to print the response body
		 */
		$http->setHost('www.php.net');
		if ($http->doGet('/') == HTTP_STATUS_OK) {
			echo $http->responseBody;
		}
	
	} elseif ($testCase == 2) {	
		
		/**
		 * set the target host and perform a multipart POST request,
		 * sending a file as parameter (upload file)
		 */
		$http->setHost('php2go.sourceforge.net');
		$cont = fread(fopen('resources/httpclient.example.txt', 'rb'), filesize('resources/httpclient.example.txt'));
		if ($http->doMultipartPost(
			'/php2go/examples/httpclient.example.php?process=1', 
			array(
				'name'=>'John Doe',
				'e_mail'=>'john@foo.org',
				'phone'=>'6666666',
				'message'=>'The quick brown fox jumps over the lazy dog',
				'contact'=>'bar@baz.foo.org'
			),
			array(
				array('name'=>'arquivo','file'=>'resources/httpclient.example.txt','data'=>$cont)
			)) == HTTP_STATUS_OK) 
		{
			echo '<PRE>' . $http->getResponseBody() . '</PRE>';
		}
		
	} elseif ($testCase == 3) {
			
		/**
		 * set the target host and perform a POST request, 
		 * specifying the hash array containing the POST data
		 */
		$http->setHost('php2go.sourceforge.net');
		$formData = array(
			'name'=>'John Doe',
			'e_mail'=>'john@foo.org',
			'phone'=>'6666666',
			'message'=>'The quick brown fox jumps over the lazy dog',
			'contact'=>'bar@baz.foo.org'	
		);
		if ($http->doPost(
			'/php2go/examples/httpclient.example.php?process=1', 
			$formData
		) == HTTP_STATUS_OK) {
			echo '<PRE>' . $http->getResponseBody() . '</PRE>';
		}
		
	} elseif ($testCase == 4) {
	
		/**
		 * perform a GET request using a proxy host
		 */		
		/*
		echo '<PRE>';		
		$http->setHost('www.yahoo.com');
		$http->setProxy('200.168.74.40', 80);
		if ($http->doGet('/') == HTTP_STATUS_OK) {
			var_dump($http->getResponseHeaders());
		} else {
			echo 'Return status: ' . $http->getStatus();		
		}
		echo '</PRE>';
		*/
		
	}
	
?>