<?php

	// $Header: /www/cvsroot/php2go/examples/stringbuffer.example.php,v 1.4 2005/05/16 18:04:59 mpont Exp $
	// $Revision: 1.4 $
	// $Date: 2005/05/16 18:04:59 $
	// vim: set expandtab tabstop=4 shiftwidth=4:

	require_once('../p2gConfig.php');
	import('php2go.text.StringBuffer');
	
	echo '<B>PHP2Go Example</B> : php2go.text.StringBuffer<BR><BR>';
	
	$sb =& new StringBuffer("the quick");
	$sb->append(" brown fox");
	$sb->append(" jumps over the lazy dog");
	echo $sb->charAt(1) . '<BR>';
	echo $sb->indexOf("over") . '<BR>';
	echo $sb->indexOf("o") . '<BR>';
	echo $sb->indexOf("o", 20) . '<BR>';	
	echo $sb->lastIndexOf("o") . '<BR>';
	echo $sb->lastIndexOf("foo") . '<BR>';
	echo $sb->lastIndexOf("x") . '<BR>';	
	echo $sb->lastIndexOf("x", 20) . '<BR>';
	$sb->getChars(0, 10, $dst);
	echo $dst . '<BR>';
	dumpVariable($sb);
	$sb->setLength(15);
	dumpVariable($sb);
	$sb->insert(15, " fox jumps over the lazy dog");
	$sb->ensureCapacity(40);
	dumpVariable($sb);
	$sb->setCharAt(0, "T");
	echo $sb->toString() . '<BR>';
		
?>