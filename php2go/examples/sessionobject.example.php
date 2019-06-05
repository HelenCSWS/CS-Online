<?php

	// $Header: /www/cvsroot/php2go/examples/sessionobject.example.php,v 1.10 2005/06/08 23:01:04 mpont Exp $
	// $Revision: 1.10 $
	// $Date: 2005/06/08 23:01:04 $
	// vim: set expandtab tabstop=4 shiftwidth=4:

	require_once('../p2gConfig.php');
	import('php2go.net.HttpRequest');
	import('php2go.session.SessionObject');
	session_save_path(PHP2GO_ROOT . 'examples/resources/');
	
	$sess = new SessionObject('MY_SESSION');
	echo '<B>PHP2Go Example</B> : php2go.session.SessionObject<BR><BR>';	
	if (!$sess->isRegistered()) {
		echo 'START...<br><a href=\'' . HttpRequest::basePath() . '\'>reload page</a>';
		$sess->createProperty('PAGE_VIEWS', 1);
		$sess->createTimeCounter('PAGE_TIME');	
		$timeCounter = $sess->getTimeCounter('PAGE_TIME');
		$sess->createProperty('URLS', array(array(HttpRequest::basePath(), NULL)));
		$sess->register();
	} else {
		$sess->setPropertyValue('PAGE_VIEWS', $sess->getPropertyValue('PAGE_VIEWS')+1);
		echo 'Page Views : ' . $sess->getPropertyValue('PAGE_VIEWS') . '<BR><a href=\'' . HttpRequest::basePath() . '\'>reload page</a><BR><BR>';
		$timeCounter =& $sess->getTimeCounter('PAGE_TIME');
		$u = $sess->getPropertyValue('URLS');
		$timeCounter->stop();
		$u[sizeOf($u)-1][1] = $timeCounter->getMinutes();
		echo 'Visited URLs :<PRE>';
		var_dump($u);
		echo '</PRE>';	
		$timeCounter->restart();
		$u[] = array(HttpRequest::basePath(), NULL);
		$sess->setPropertyValue('URLS', $u);
		$sess->update();
	}		

?>