<?php

	// $Header: /www/cvsroot/php2go/examples/httpresponse.example.php,v 1.1 2004/09/27 22:41:43 mpont Exp $
	// $Revision: 1.1 $
	// $Date: 2004/09/27 22:41:43 $
	// vim: set expandtab tabstop=4 shiftwidth=4:

	require_once('../p2gConfig.php');
	import('php2go.net.HttpCookie');
	import('php2go.net.HttpRequest');
	import('php2go.net.HttpResponse');

	$Cookie = new HttpCookie();
	if (HttpRequest::cookie('test') == NULL) {
		if (HttpRequest::get('op') == 'set') {
			$Cookie->set('test', 'mycookie', HttpRequest::serverName(), '/', 20);
			HttpResponse::addCookie($Cookie);
			$op = 'The cookie was added. Reload the page to verify.<BR><BR>';
		}		
	} else {
		if (HttpRequest::get('op') == 'unset') {			
			$Cookie->set('test', 'mycookie', HttpRequest::serverName(), '/', -20);
			HttpResponse::addCookie($Cookie);
			$op = 'The cookie was removed. Reload the page to verify.<BR><BR>';
		}		
	}
		
	echo '<B>PHP2Go Examples</B> : php2go.net.HttpResponse<BR><BR>';
	if (isset($op))
		print $op;

	echo 'Request Cookies Dump:<br>';
	dumpVariable($_COOKIE);
	
	echo '<A HREF=\'' . HttpRequest::basePath() . '?op=set\'>Add Cookie</A><BR>';
	echo '<A HREF=\'' . HttpRequest::basePath() . '?op=unset\'>Remove Cookie</A><BR>';
	echo '<A HREF=\'' . HttpRequest::basePath() . '\'>Reload Page</A><BR>';
		
?>