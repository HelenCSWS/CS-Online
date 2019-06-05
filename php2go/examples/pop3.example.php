<?php

	// $Header: /www/cvsroot/php2go/examples/pop3.example.php,v 1.9 2005/05/02 13:06:52 mpont Exp $
	// $Revision: 1.9 $
	// $Date: 2005/05/02 13:06:52 $
	// vim: set expandtab tabstop=4 shiftwidth=4:

	require_once('../p2gConfig.php');
	import('php2go.net.Pop3');
	
	echo '<B>PHP2Go Examples</B> : php2go.net.Pop3<BR><BR>';
	
	// create an instance of the Pop3 class
	$pop =& new Pop3();
	
	// enable debug to see the client/server messages
	$pop->debug = TRUE;
	
	// connect to a POP3 host (default port = 110)
	$pop->connect('your.pop3.host');
	
	// send authentication data
	$pop->login('username', 'password');
	
	// get the message count
	$count = $pop->getMsgCount();
	print '<B>Total number os messages in the POP3 server:</B> ' . $count . '<BR>';
	
	// get the headers of each message
	for ($i=1; $i<=$count; $i++) {
		$h = $pop->getMessageHeaders($i, TRUE);
		echo 'Date: ' . $h['Date'] . '<BR>Subject: ' . $h['Subject'] . '<BR>From: ' . htmlspecialchars($h['From']) . '<BR>';
	}

	//close the connection
	$pop->close();	

?>