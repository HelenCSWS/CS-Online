<?php

	// $Header: /www/cvsroot/php2go/examples/tarfile.example.php,v 1.2 2004/09/08 17:15:03 mpont Exp $
	// $Revision: 1.2 $
	// $Date: 2004/09/08 17:15:03 $
	// vim: set expandtab tabstop=4 shiftwidth=4:

	require_once('../p2gConfig.php');
	import('php2go.file.TarFile');
	
	echo
		'<B>PHP2Go Examples</B> : php2go.file.TarFile<BR><BR>';
		
	/**
	 * create the instance using the factory method getInstance
	 */
	$tar =& FileCompress::getInstance('tar');
	$tar->debug = TRUE;
	
	/**
	 * add some files in the tarball
	 */
	$tar->addFile('forms.example.xml');
	$tar->addFile('reports.example.xml');
	
	/**
	 * save the file with and without gzip compression
	 */
	$tar->saveFile('test.tar', 0777);
	$tar->saveGzip('test.tar.gz', 0777);
		
	/**
	 * extract the archived and compressed data
	 */
	$tar->saveExtractedFiles($tar->extractGzip('test.tar.gz'), 0777, 'resources');

?>