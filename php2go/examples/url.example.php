<?php

	// $Header: /www/cvsroot/php2go/examples/url.example.php,v 1.7 2004/11/18 09:15:31 mpont Exp $
	// $Revision: 1.7 $
	// $Date: 2004/11/18 09:15:31 $
	// vim: set expandtab tabstop=4 shiftwidth=4:

	require_once('../p2gConfig.php');
	import('php2go.net.Url');
	
	echo '<B>PHP2Go Examples</B> : php2go.net.Url<BR><BR>';
	
	/**
	 * URL to be tested
	 */
	$testUrl = 'http://user:pass@www.domain.com:8080/path/internal/script.php?parameter=2#anchor';
	echo 'Original URL : ' . $testUrl . '<BR>';
	echo '<HR>';
	
	//$url =& new Url($testUrl);
	$url =& new Url();
	$url->setFromCurrent();
	/**
	 * URL host
	 */
	echo 'Host: ' . $url->getHost() . '<BR>';
	/**
	 * URL scheme (protocol)
	 */
	echo 'Scheme: ' . $url->getScheme() . '<BR>';
	/**
	 * URL connection port
	 */
	echo 'Port: ' . $url->getPort() . '<BR>';
	/**
	 * authentication data (username and password)
	 */
	echo 'Auth vars: ' . $url->getAuth() . '<BR>';
	/**
	 * path after the domain name
	 */
	echo 'Path: ' . $url->getPath() . '<BR>';
	/**
	 * file name
	 */
	echo 'File: ' . $url->getFile() . '<BR>';
	/**
	 * query string
	 */
	echo 'Parameters: ' . $url->getQueryString() . '<BR>';
	/**
	 * query string as array
	 */
	echo 'Parameters returned as array: ' . exportVariable($url->getQueryStringArray()) . '<BR>';
	/**
	 * fragment or anchor
	 */
	echo 'Fragment or anchor: ' . $url->getFragment() . '<BR>';
	/**
	 * build an anchor pointing to the URL
	 */
	echo 'Geração de âncora: ' . $url->getAnchor('Click me!') . '<BR>';
	echo '<HR>';
	/**
	 * get the normalized URL value
	 */
	echo 'Final URL: ' . $url->getUrl() . '<BR>';
	echo '<HR>';	
	/**
	 * encode the URL using base64
	 */
	$encode = $url->encode(NULL, 'q');
	echo 'Encoded URL: ' . $encode . '<BR>';
	/**
	 * catches the parameters from an encoded URL value
	 */
	$encoded =& new Url($encode);	
	$decode = $encoded->decode();
	echo 'Decoded URL: ' . $decode . '<BR>';
	echo 'Decoded parameters returned as array: ' . exportVariable($encoded->decode(NULL, TRUE)) . '<BR>';
	echo '<HR>';
	/**
	 * adding and removing new parameters to the URL
	 */
	$url->addParameter('param', 'value');
	$url->addParameter('action', 'edit');
	$url->addParameter('goto', 2);
	echo 'URL with new parameters: ' . $url->getUrl() . '<BR>';
	$url->removeParameter('action');
	echo 'URL parameters after removing one of them: ' . $url->getUrl();
	
?>