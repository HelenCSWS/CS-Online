<?php

	// $Header: /www/cvsroot/php2go/examples/ftpclient.example.php,v 1.2 2005/06/03 19:04:38 mpont Exp $
	// $Revision: 1.2 $
	// $Date: 2005/06/03 19:04:38 $
	// vim: set expandtab tabstop=4 shiftwidth=4:

	require_once('../p2gConfig.php');
	import('php2go.net.FtpClient');
	
	echo '<B>PHP2Go Examples</B> : php2go.net.FtpClient<BR><BR>';
	
	// create an instance of the FtpClient class
	$ftp =& new FtpClient();
	
	// enable passive mode
	$ftp->togglePassiveMode(TRUE);
	
	// set host name
	$ftp->setServer('ftp.debian.org', FTP_DEFAULT_PORT);
	
	// connect
	$ftp->connect();
	
	// set user info (will not be used in this example
	//$ftp->setUserInfo('username', 'password');
	
	// login operation (first parameter means anonymous login)
	$ftp->login(TRUE);
	
	// print current dir
	print 'Current Directory: ' . $ftp->getCurrentDir() . '<BR>';
	// print server system information
	print 'Server system type: ' . $ftp->getSysType() . '<BR>';
	
	// change directory
	$ftp->changeDir('debian');
	//echo $ftp->getCurrentDir();
	
	// retrieve the file list containing only the file names
	echo '<HR><BR>';
	echo '<B>Retrieve the file names of a remote directory</B><BR>';
	$list = $ftp->fileList();
	foreach ($list as $entry)
		print $entry . '<BR>';
		
	// retrieve the details of the files inside a remote directory
	echo '<HR><BR>';
	echo '<B>Retrieve the details of each file included in a remote directory</B><BR>';
	$list = $ftp->rawList('', TRUE);
	foreach ($list as $entry) {
		print "Name: {$entry['name']} - Size: {$entry['size']} bytes - Permissions: {$entry['attr']}<BR>";
	}
	
	// quit the connection
	$ftp->quit();
	
?>