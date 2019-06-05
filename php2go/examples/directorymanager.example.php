<?php

	// $Header: /www/cvsroot/php2go/examples/directorymanager.example.php,v 1.1 2005/05/02 13:29:10 mpont Exp $
	// $Revision: 1.1 $
	// $Date: 2005/05/02 13:29:10 $
	// vim: set expandtab tabstop=4 shiftwidth=4:

	require_once('../p2gConfig.php');
	import('php2go.datetime.Date');
	import('php2go.file.DirectoryManager');
	
	echo '<B>PHP2Go Example</B> : php2go.file.DirectoryManager<BR><BR>';
	
	// create an instance of DirectoryManager
	$dir =& new DirectoryManager();
	// open the current directory
	$dir->open(getcwd());
	
	echo '<B>List current directory content</B><BR>';
	echo '<B><PRE>while ($entry = $dir->read()) { ... print entry info ... } </PRE></B>';
	
	// while loop that reads the directory content
	while ($entry = $dir->read()) {
		print 'Name: ' . $entry->getName() . ' - Size: ' . $entry->getSize() . ' bytes - Last modified: ' . Date::localDate($entry->getAttribute('mTime')) . '<BR>';
	}
	// close the dir handle
	$dir->close();
	
	// open the current directory
	$dir->open('resources/');
	
	echo '<HR><BR>';	
	echo '<B>List relative path content using getFiles, filename mask (.tpl) and name sorting</B><BR>';
	echo '<B><PRE>$dir->getFiles("\.tpl", TRUE);</PRE></B>';
	
	// while loop that reads the directory content
	$entries = $dir->getFiles("\.tpl");
	foreach ($entries as $entry) {
		print 'Name: ' . $entry->getName() . ' - Size: ' . $entry->getSize() . ' bytes - Last modified: ' . Date::localDate($entry->getAttribute('mTime')) . '<BR>';
	}
	
	echo '<HR><BR>';	
	echo '<B>Get total size of a directory, using recursion</B><BR>';
	echo '<B><PRE>$dir->getSize(\'K\', 2, TRUE);</PRE></B>';
	
	// gets the parent directory (another DirectoryManager instance)
	$parent =& $dir->getParentDirectory();
	// gets the total size of a directory, using recursion
	$size = $parent->getSize('K', 2, TRUE);
	echo 'Total size: ' . $size;
	
	// close opened directories
	$parent->close();
	$dir->close();
	
?>