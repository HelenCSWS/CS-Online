<?php

	// $Header: /www/cvsroot/php2go/examples/statement.example.php,v 1.2 2004/07/06 12:21:08 mpont Exp $
	// $Revision: 1.2 $
	// $Date: 2004/07/06 12:21:08 $
	// vim: set expandtab tabstop=4 shiftwidth=4:

	require_once('../p2gConfig.php');
	import('php2go.net.HttpRequest');
	import('php2go.util.Statement');
	
	echo '<B>PHP2Go Example</B> : php2go.util.Statement<BR><BR>';
	
	$statement =& new Statement();
	
	$stCode = "You are running PHP ~version~, and the name of this script is ~script~";
	echo 'New Statement code : ' . $stCode . '<BR>';
	$statement->setStatement($stCode);
	
	echo 'Bind variable version = PHP_VERSION<BR>';
	$statement->bindByName('version', PHP_VERSION, FALSE);
	
	echo 'Bind variable script = HttpRequest::basePath()<BR>';
	$statement->bindByName('script', HttpRequest::basePath());
	
	echo 'Result : ' . $statement->getResult() . '<BR><BR>';
	
	$stCode = "Running PHP on ~_SERVER['SERVER_NAME']~, ~_SERVER['SERVER_ADDR']~";
	echo 'New Statement code : ' . $stCode . '<BR>';
	$statement->setStatement($stCode);
	
	echo 'Bind variables (method that tries to find all the variables in the DATA REPOSITORIES (request, session, registry)<BR>';
	$statement->bindVariables(FALSE, 'ROEGP');
	
	echo 'Result : ' . $statement->getResult() . '<BR>';	
	
?>