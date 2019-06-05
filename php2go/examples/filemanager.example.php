<?php

	// $Header: /www/cvsroot/php2go/examples/filemanager.example.php,v 1.1 2005/05/02 13:29:10 mpont Exp $
	// $Revision: 1.1 $
	// $Date: 2005/05/02 13:29:10 $
	// vim: set expandtab tabstop=4 shiftwidth=4:

	require_once('../p2gConfig.php');
	import('php2go.datetime.Date');
	import('php2go.file.FileManager');
	import('php2go.util.Number');
	
	echo '<B>PHP2Go Example</B> : php2go.file.FileManager<BR><BR>';
	
	/**
	 * create an instance of the FileManager class
	 */
	$mgr =& new FileManager();
	
	echo '<B>Display the contents of a file, line by line</B><BR>';	
	/**
	 * open using READ_BINARY mode ("rb")
	 */
	$mgr->open('resources/menu.sql', FILE_MANAGER_READ_BINARY);
	while ($line = $mgr->readLine()) {
		print $line . '<BR>';
	}
	
	echo '<HR><BR>';
	echo '<B>Pointer operations: tell, rewind, seek</B><BR>';
	print 'Current position: ' . $mgr->getCurrentPosition() . '<BR>';
	$mgr->rewind();
	$mgr->seek(100);
	print $mgr->readChar() . '<BR>';
	$mgr->seek(0);
	print $mgr->readChar() . '<BR>';
	
	echo '<HR><BR>';
	echo '<B>Display the attributes of the current opened file</B><BR>';
	echo '<B><PRE>$attrs = $mgr->getAttributes();</PRE></B>';
	/**
	 * get all attributes
	 */
	$attrs = $mgr->getAttributes();
	foreach ($attrs as $name => $value) {
		echo "{$name}: $value<BR>";
	}
	
	echo '<HR><BR>';
	echo '<B>Transform the value of an attribute</B><BR>';
	echo 'Last modified: ' . Date::localDate($mgr->getAttribute('mTime')) . '<BR>';
	echo 'Total size in KB: ' . Number::formatByteAmount($mgr->getAttribute('size'), 'K', 2) . '<BR>';
	echo 'Is writeable?: ' . ($mgr->getAttribute('isWriteable') ? 'yes' : 'no') . '<BR>';
	$mgr->close();
	
	echo '<HR><BR>';
	echo '<B>Read a file as an array</B><BR>';
	echo '<B><PRE>$file = $mgr->readArray(\'file_path\');</PRE></B>';
	/**
	 * using readArray, it's not necessary to open or close the file, 
	 * because the class uses the file() function internally
	 */	 
	$file = $mgr->readArray('resources/javascript.example.js');
	foreach ($file as $line)
		print $line . '<BR>';
		
	echo '<HR><BR>';
	echo '<B>Create a new file and write some data</B><BR>';
	echo "<B><PRE>\$mgr->open('file_path', FILE_MANAGER_WRITE_BINARY);\n\$mgr->writeLine('content');</PRE></B><BR>";
	$mgr->open('resources/filemanager.example.txt', FILE_MANAGER_WRITE_BINARY);
	$mgr->writeLine('the quick brown fox jumps over the lazy dog', "\r\n");
	$mgr->writeLine('PHP2Go Web Development Framework', "\r\n");
	
	/**
	 * if this line is missing, the class destructor will close all the active 
	 * and valid pointers and release all the valid file locks
	 */	
	$mgr->close();
	
?>