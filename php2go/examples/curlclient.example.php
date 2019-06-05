<?php

	// $Header: /www/cvsroot/php2go/examples/curlclient.example.php,v 1.3 2005/05/23 16:30:57 mpont Exp $
	// $Revision: 1.3 $
	// $Date: 2005/05/23 16:30:57 $
	// vim: set expandtab tabstop=4 shiftwidth=4:
	
	require_once('../p2gConfig.php');
	import('php2go.net.CurlClient');
	
	echo '<B>PHP2Go Examples</B> : php2go.net.CurlClient<BR><BR>';

	// create an instance of the CurlClient class
	$curl =& new CurlClient();
	
	// initialize the CURL handle
	$curl->init();
	
	// define the URL to be used in this example
	// this method accepts string URLs or php2go.net.Url instances
	$curl->setUrl(PHP2GO_ABSOLUTE_PATH . 'examples/resources/curlclient.request.php');
	
	println('<B>Perform a simple GET request</B>');
	// sinalize that we want the response headers
	$curl->setOption(CURLOPT_HEADER, 1);
	// perform the request
	$response = $curl->doGet();
	// parse the response, extracting the response code, headers and body
	print 'Response contents: ';
	dumpVariable($curl->parseResponse($response));
	
	// retrieve information about the last transfer
	println('<HR>');
	println('<B>Retrieve information about the last transfer</B>'); 
	$info = $curl->getTransferInfo();
	foreach ($info as $name => $value) {
		println("{$name} = {$value}");
	}
	
	// reset the configuration settings (CURL options)
	// this method must be called when a new transfer will be executed
	$curl->reset();
	
	println('<HR>');
	println('<B>Perform a POST request, saving the response body in a text file</B>');
	// set the file where the response must be written
	$ok = $curl->returnToFile('resources/curlclient.response.txt');
	// set the array of POST fields
	$curl->setPostData(array('foo' => 1, 'bar' => 2, 'baz' => 3));
	$return = $curl->doPost();
	if ($ok) {
		println('Response body contents:');
		readfile('resources/curlclient.response.txt');
	} else {
		println('It wasn\'t possible to write response to a file. Response body contents:');
		println($return);
	}

?>