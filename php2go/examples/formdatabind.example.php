<?php

	// $Header: /www/cvsroot/php2go/examples/formdatabind.example.php,v 1.3 2005/05/19 22:39:54 mpont Exp $
	// $Revision: 1.3 $
	// $Date: 2005/05/19 22:39:54 $
	// vim: set expandtab tabstop=4 shiftwidth=4:

	require_once('../p2gConfig.php');
	import('php2go.base.Document');
	import('php2go.form.FormDataBind');

	/**
	 * create and configure an instance of the class document, where the form will be included
	 */
	$doc = new Document('resources/layout.example.tpl');
	$doc->setCache(FALSE);
	$doc->setCompression();
	$doc->addScript('resources/javascript.example.js');
	$doc->addStyle('resources/css.example.css');
	$doc->addBodyCfg(array('bgcolor'=>'#ffffff', 'style'=>'margin:0em'));
	
	/**
	 * create an instance of FormDataBind. this form uses an external template to apply the fields HTML code
	 * besides, the fields are populated with the content of a database record, and the user can navigate
	 * through the table content using DataBind. this component works only under Internet Explorer
	 * the 6th parameter is the table name (CLIENT)
	 * the 7th parameter is the table primary key (CLIENT_ID)
	 */
	$form =& new FormDataBind('resources/formdatabind.example.xml', 'resources/formdatabind.example.tpl', 'myForm', $doc, array(), 'client', 'CLIENT_ID');
		
	/**
	 * here we define the SQL query that will be used to retrieve the table values and create the DBCSV file
	 */
	$form->queryFields = 'CLIENT_ID, NAME, ADDRESS, CATEGORY, ACTIVE';
	$form->queryTables = 'client'; // the class will only persist the records of one table, but you may want to use inner/left joins here
	$form->queryClause = '';
	$form->queryOrder = 'NAME';
	
	/**
	 * the databind tool can filter and sort data using user defined criteria
	 * the following method receives as parameter a formatted string: name#caption|name#caption|...
	 */
	$form->setFilterSortOptions('NAME#Name|ADDRESS#Address|CATEGORY#Category');
	
	/**
	 * using this method, you can force records to be inserted/updated/deleted using a default POST request,
	 * instead of sending data to a remote PHP script that generates and executes the SQL statement
	 */
	$form->disableJsrs();
	
	/**
	 * attach the form content to the document
	 */
	$doc->elements['main'] = $form->getContent();
	
	$doc->display();

?>