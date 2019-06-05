<?php

	// $Header: /www/cvsroot/php2go/examples/dataset.example.php,v 1.8 2005/09/01 15:48:20 mpont Exp $
	// $Revision: 1.8 $
	// $Date: 2005/09/01 15:48:20 $
	// vim: set expandtab tabstop=4 shiftwidth=4:

	require_once('../p2gConfig.php');
	import('php2go.data.DataSet');
	
	println('<B>PHP2Go Example</B> : php2go.data.DataSet');
	println('<B>Also using:</B> php2go.data.adapter.DataSetDb, php2go.data.adapter.DataSetXml, php2go.data.adapter.DataSetCsv, php2go.data.adapter.DataSetArray');
	
	/**
	 * Type "db" - dataset using a database as external source
	 * Available optional parameters:
	 *	- debug (bool): enable or disable debug in the DB connection
	 *	- connectionId (string): ID of the database connection to be used
	 * Example using eof and moveNext methods	 
	 */
	print('<BR><HR>');
	println('<B>DataSet from a database : select * from client where category = \'Master\' order by name</B>');
	$DataSet =& DataSet::getInstance('db', array(
		'debug' => TRUE,
		'connectionId' => 'DEFAULT'
	));
	$DataSet->load('select * from client where category = \'Master\' order by name');
	while (!$DataSet->eof()) {
		println($DataSet->getField('NAME'));
		$DataSet->moveNext();
	}
	
	/**
	 * Type "xml" - dataset using a XML file as external source
	 * Available optional parameters: none	 
	 * Example using fetchInto method
	 */
	print('<BR><HR>');
	println('<B>DataSet from XML file : resources/datasetxml.example.xml</B>');
	$DataSet =& DataSet::getInstance('xml');
	$DataSet->load('resources/datasetxml.example.xml', DS_XML_CDATA);
	while ($DataSet->fetchInto($fields)) {
		// the field names are always uppercased
		println($fields['FIELD']);
	}
	
	/**
	 * Type "csv" - create a dataset based on the contents of a CSV file (comma-separated values)
	 * Available optional parameters: none
	 * Example using eof, moveNext and fetch methods
	 */
	print('<BR><HR>');
	println('<B>DataSet from CSV file : resources/datasetcsv.example.csv</B>');
	$DataSet =& DataSet::getInstance('csv');	
	$DataSet->load('resources/datasetcsv.example.csv');
	println('10 last lines');
	$DataSet->move(16);
	while (!$DataSet->eof()) {
		println($DataSet->getField('letter'));
		$DataSet->moveNext();
	}
	println('10 first lines');
	$DataSet->moveFirst();
	while ($fields = $DataSet->fetch()) {
		println($fields['letter']);
		if ($DataSet->getAbsolutePosition() == 10) break;
	}
	
	/**
	 * Type "array" - create an array based dataset
	 * Available optional parameters: none
	 * Example using fetch method
	 */
	print('<BR><HR>');
	println('<B>DataSet from a bidimensional array</B>');
	$DataSet =& DataSet::getInstance('array');	
	$DataSet->load(array(
		array('KEY' => 1),
		array('KEY' => 2),
		array('KEY' => 3),
		array('KEY' => 4),
		array('KEY' => 5),
		array('KEY' => 6),
		array('KEY' => 7),
		array('KEY' => 8),
		array('KEY' => 9),
		array('KEY' => 10)
	));
	while ($fields = $DataSet->fetch()) {
		println($fields['KEY']);
	}

?>