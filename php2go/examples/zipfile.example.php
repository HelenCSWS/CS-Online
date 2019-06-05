<?php

	// $Header: /www/cvsroot/php2go/examples/zipfile.example.php,v 1.2 2004/09/08 17:22:55 mpont Exp $
	// $Revision: 1.2 $
	// $Date: 2004/09/08 17:22:55 $
	// vim: set expandtab tabstop=4 shiftwidth=4:

	require_once('../p2gConfig.php');
	import('php2go.file.ZipFile');
	
	echo
		'<B>PHP2Go Examples</B> : php2go.file.ZipFile<BR><BR>';
		
	/**
	 * creates the instance using the factory method getInstance
	 */
	$zip =& FileCompress::getInstance('zip');
	$zip->debug = TRUE;
	
	/**
	 * add a file in the ZIP archive using the filename
	 */
	$zip->addFile('forms.example.xml');
	/**
	 * add the contents of a file (string parameter)
	 */
	$zip->addData(readfile('reports.example.xml'), 'reports.example.xml', array('time' => filemtime('reports.example.xml')));
	
	/**
	 * save the file
	 */
	$zip->saveFile('test.zip', 0777);
		
	/**
	 * extract the archived & compressed data
	 */
	$zip->saveExtractedFiles($zip->extractFile('test.zip'), 0777, 'resources');

?>