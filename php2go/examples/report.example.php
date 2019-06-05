<?php

	// $Header: /www/cvsroot/php2go/examples/report.example.php,v 1.15 2005/08/30 14:33:28 mpont Exp $
	// $Revision: 1.15 $
	// $Date: 2005/08/30 14:33:28 $
	// vim: set expandtab tabstop=4 shiftwidth=4:

	require_once('../p2gConfig.php');
	import('php2go.base.Document');
	import('php2go.data.Report');
	import('php2go.datetime.TimeCounter');
	
	$db =& Db::getInstance();
	$tables = $db->getTables();
	if (!in_array('client', $tables)) {
		PHP2Go::raiseError("The <i>client</i> table was not found! Please run <i>clients.sql</i>, located at the <i>ROOT/examples/resources</i> folder.<br>P.S.: The creation script was designed for mySQL databases.", E_USER_ERROR, __FILE__, __LINE__);
	}	
	
	/**
	 * create an instance of the Document class using a simple page layout, with one single slot : "main"
	 */
	$doc =& new Document('resources/layout.example.tpl');
	
	/** 
	 * example 1 : header report
	 * >>> attention : edit the XML specification file (resources/report.example.xml) to test the Report class with your database	 
	 */
	$report =& new Report(
		'resources/report.example.xml', 
		'resources/report.example.tpl', 
		$doc);
	$report->hasHeader = TRUE; 										// enable column headers
	$report->enableHighlight('#ffffff', '#ff0000'); 				// highlight search values
	$report->setStyleMapping('links', 'input', 'input', 'title');	// set link, input, button and title styles
	$report->setAlternateStyle('cellA', 'cellB');					// set alternate style (at least 2)
	$report->setColumnAlias('NAME', 'Client Name');					// set column aliases
	$report->setColumnAlias('ADDRESS', 'Client Address');
	$report->setColumnAlias('CLIENT_ID', 'Actions');
	$report->setGroup('CATEGORY');									// group by "CATEGORY" column
	$report->setLineHandler('lineHandler');							// define a function to transform/format the data inside the records of the list
	
	/**
	 * switch the next three lines to see the different modes of column sizes
	 */
	$report->setColumnSizes(REPORT_COLUMN_SIZES_FREE);				// let the browser define the width of each column
	//$report->setColumnSizes(REPORT_COLUMN_SIZES_FIXED);			// all the columns must have the same width
	//$report->setColumnSizes(array(40,60));						// provide an array of sizes to the columns - the sum of the array values must be equal to 100
	
	/**
	 * now, the following three lines show three different ways of building the links to the other pages in the report
	 * this is called "paging style"
	 */
	// only previous and next links, using buttons and localized text
	//$report->setPagingStyle(REPORT_PREVNEXT, array('useButtons' => TRUE, 'useSymbols' => TRUE));
	// first, previous, next and last page links, using symbols instead of text
	//$report->setPagingStyle(REPORT_FIRSTPREVNEXTLAST, array('useButtons' => FALSE, 'useSymbols' => TRUE));
	// first, previous, next and last page links, using buttons
	$report->setPagingStyle(REPORT_FIRSTPREVNEXTLAST, array('useButtons' => TRUE, 'useSymbols' => FALSE));
	
	$report->build();												// build the report
	
	/**
	 * example 2 : cell report (one or more database result per line)
	 * in the template file, inside the loop_cell block, each column must be represented by a variable with the same name
	 * of the alias. in this particular case, we've included only the {NAME} variable in the loop_cell block
	 */
	$report2 =& new Report(
		'resources/report.example.xml', 
		'resources/report.example.tpl', 
		$doc);
	$report2->setColumns(3);										// number of records per line
	$report2->enableHighlight('#ffffff', '#ff0000');				// highlight search values
	$report2->setStyleMapping('links', 'input', 'input', 'title');	// set link, input, button and title styles
	$report2->setAlternateStyle('cellA', 'cellB');					// set alternate style (at least 2)
	$report2->setEmptyBlock('loop_cell_empty');						// set the block that must be used to fill an incomplete line
	$report2->setGroup('CATEGORY');									// group by "CATEGORY" column	
	$report2->build();												// build the report
	
	/**
	 * switch the next two lines to see the difference between the two kinds of reports
	 */
	$doc->elements['main'] = $report->getContent();
	//$doc->elements['main'] = $report2->getContent();
	
	$doc->display();

	/**
	 * this is the line handler, used to transform some of the values of a record of the paged list
	 * the handlers in php2go.data.Report can be any kind of callback: functions, static or dynamic methods
	 * a line handler must receive the hash array of a record as parameter and return it with the necessary transformations
	 */
	function lineHandler($data) {
		/**
		 * inside a line handler, we can transform a data column in a list of anchors pointing to actions
		 * in this simple case, we will simulate anchors to edit and delete the record in the database
		 */
		$actions = array(
			HtmlUtils::anchor(HttpRequest::basePath() . '?edit=' . $data['CLIENT_ID'], 'Edit', 'Edit this record'),
			HtmlUtils::anchor(HttpRequest::basePath() . '?delete=' . $data['CLIENT_ID'], 'Delete', 'Delete this record')
		);
		$data['CLIENT_ID'] = join('&nbsp;', $actions);			
		return $data;
	}

?>
