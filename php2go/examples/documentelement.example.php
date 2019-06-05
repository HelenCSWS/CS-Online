<?php

	// $Header: /www/cvsroot/php2go/examples/documentelement.example.php,v 1.7 2005/07/18 15:00:55 mpont Exp $
	// $Revision: 1.7 $
	// $Date: 2005/07/18 15:00:55 $
	// vim: set expandtab tabstop=4 shiftwidth=4:

	require_once('../p2gConfig.php');
	import('php2go.base.Document');
	import('php2go.data.DataSet');	
	import('php2go.datetime.*');
	import('php2go.file.FileManager');
	import('php2go.template.DocumentElement');
	
	$tc =& new TimeCounter();
	
	$db =& Db::getInstance();
	$tables = $db->getTables();
	if (!in_array('client', $tables)) {
		PHP2Go::raiseError("The <i>client</i> table was not found! Please run <i>clients.sql</i>, located at the <i>ROOT/examples/resources</i> folder.<br>P.S.: The creation script was designed for mySQL databases.", E_USER_ERROR, __FILE__, __LINE__);
	}	
	
	/**
	 * document creation, using a template layout containing three slots: header, menu and main
	 */
	$document =& new Document('resources/layout2.example.tpl');
	$document->setTitle('PHP2Go Example - php2go.template.DocumentElement');
	
	/**
	 * simple element : a simple string
	 */
	$document->elements['header'] = "<span style='color:red'>This is an example : PAGE HEADER</span>";
	
	/**
	 * file element : include the content of a file
	 */
	$document->elements['menu'] = FileSystem::getContents('resources/menu.include.html');
	
	/**
	 * new instance of DocumentElement, an extended template that can be used
	 * to build complex page slots with all template operations: assign, block replication
	 * and file inclusion
	 */
	$element =& new DocumentElement();
	
	/** 
	 * content add
	 * 1) put a template file
	 * 2) put another template file, below first
	 * 3) add a string variable
	 * 4) parse/compile the template
	 */
	$element->put('resources/template1.include.tpl', T_BYFILE);
	$element->put('resources/template2.include.tpl', T_BYFILE);
	$element->put('<table border="0"><tr><td>End of the page slot!</td></tr></table>', T_BYVAR);
	$element->parse();
	
	/**
	 * defines a global value for a variable
	 * each time that this variable is found in the template, it will be assigned to this value
	 */
	$element->globalAssign('date', Date::localDate());
	
	/**
	 * block replication, assigning for each instance the values returned in a database record
	 */
	$element->generateFromQuery('master_client', 'select NAME, ADDRESS from client where CATEGORY = \'Master\' order by NAME limit 5');
	
	/**
	 * shows a data set, with automatic creation of the container (external) block and N instances of a loop block "or" an instance
	 * of an "empty results" block. the DataSet class can load data from a database, from a XML file or from a CSV file.
	 * the "common_clients" block is the container block. it's here that we declare the table heading
	 * the "common_client_loop" block must contain all the query aliases declared as block variables
	 * the "common_clients_empty" is created when the dataset contains no data
	 */
	$dataset =& DataSet::getInstance('db');
	$dataset->load('select NAME, ADDRESS from client where name like \'%ma%\'');
	$element->generateFromDataSet($dataset, 'common_clients', 'common_clients_empty', 'common_client_loop');
	
	/**
	 * after all the performed operations, the DocumentElement object can be attached to the document
	 * 1) direct object assign (use this approach only if using DocumentElement or Template instances)
	 * 2) string assign (using a method that returns the element/template/file content
	 * use one OR another
	 */
	/* 1) */ 
	$document->elements['element'] =& $element;
	/* 2 */
	$document->elements['main'] = $element->getContent();
	
	/**
	 * displays the final content of the HTML document
	 */
	$document->appendBodyContent("Generation Time: " . (round($tc->getElapsedTime(), 3)) . " seconds");
	$document->display();

?>