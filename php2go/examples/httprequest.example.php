<?php

	// $Header: /www/cvsroot/php2go/examples/httprequest.example.php,v 1.6 2005/06/01 16:15:31 mpont Exp $
	// $Revision: 1.6 $
	// $Date: 2005/06/01 16:15:31 $
	// vim: set expandtab tabstop=4 shiftwidth=4:

	require_once('../p2gConfig.php');
	import('php2go.net.HttpRequest');
	import('php2go.session.SessionObject');	
	
	$sess =& new SessionManager();
	
	println('<B>PHP2Go Examples</B> : php2go.net.HttpRequest');
	println('<B>Also using</B> : php2go.net.UserAgent<BR>');
	
	/**
	 * 1) current script (PHP_SELF)
	 * 2) URI of the last request (REQUEST_URI) - you can test this variable providing different GET parameters to this page
	 */
	println('<B>Current Script:</B> ' . HttpRequest::basePath());
	println('<B>Current URI:</B> ' . HttpRequest::uri());
	println('<B>Referer:</B> ' . HttpRequest::referer());
	println('<B>Method:</B> ' . HttpRequest::method());
	println('<B>User agent:</B> ' . HttpRequest::userAgent());
	println('<B>Request headers:</B> ' . exportVariable(HttpRequest::getHeaders(), TRUE));
	println('<B>Server name and script name:</B> ' . HttpRequest::scriptName() . ', running at ' . HttpRequest::serverName());
	println('<B>User IP and host:</B> ' . HttpRequest::remoteAddress() . ' - ' . HttpRequest::remoteHost());
	
	/**
	 * 3) session variables fetch using HttpRequest::session
	 * in the above lines, a new session variable is created (execution_count)
	 * after that, you can see how the value can be retrieved using HttpRequestt
	 */
	if ($sess->isRegistered('execution_count')) {
		$sess->setValue('execution_count', $sess->getValue('execution_count') + 1);
	} else {
		$sess->register('execution_count', 1);
	}
	println('<B>Execution count:</B> ' . HttpRequest::session('execution_count') . ' <A HREF=\'javascript:location.reload()\'>Reload</A>');
	
	/**
	 * 4) generic variable search, using the getVar method
	 */
	println('<B>User IP (using getVar):</B> ' . HttpRequest::getVar('REMOTE_ADDR', 'SERVER'));
	println('<B>Document Root (using getVar):</B> ' . HttpRequest::getVar('DOCUMENT_ROOT', 'all', 'EGP'));
	
	/**
	 * 5) user agent info, using php2go.net.UserAgent
	 */
	println('<B>Get user agent information</B>');
	$agent =& UserAgent::getInstance();
	println(nl2br($agent->toString()));
	println('<B>Is IE?</B> ' . TypeUtils::parseInteger($agent->matchBrowser('ie')));
	println('<B>Match against a browser list:</B> ' . $agent->matchBrowserList(array('ie5+', 'ns6+', 'opera5+')));
	println('<B>Accepts gzip encoding?</B> ' . TypeUtils::parseInteger($agent->matchAcceptList('gzip', 'encoding')));
	println('<B>What is the JavaScript version?</B> ' . $agent->getFeature('javascript'));
	println('<B>Print the browser full name</B> ' . $agent->getBrowserFullName());
	println('<B>Print the OS full name</B> ' . $agent->getOSFullName());
	
?>