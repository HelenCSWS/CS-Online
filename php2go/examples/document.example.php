<?php

	// $header: /www/cvsroot/php2go/examples/document.example.php,v 1.1 2003/09/22 19:37:59 mpont Exp $
	// $Revision: 1.10 $
	// $Date: 2005/05/02 13:06:52 $
	// vim: set expandtab tabstop=4 shiftwidth=4:

	require_once('../p2gConfig.php');
	import('php2go.base.Document');
	import('php2go.datetime.TimeCounter');
	
	$tc =& new TimeCounter();
	
	/**
	 * HTML document creation
	 */
	$doc = new Document('resources/layout2.example.tpl');
	
	/**
	 * some examples of utility methods
	 */
	
	// title of the page
	$doc->setTitle('DOCUMENT EXAMPLE PAGE');
	// use or not use browser cache
	$doc->setCache(TRUE);
	// page charset
	$doc->setCharset('iso-8859-1');
	// set language
	$doc->setLanguage('en');
	// set HTTP compression
	$doc->setCompression(TRUE, 9);
	// add a javascript file
	$doc->addScript("resources/javascript.example.js");
	// add a javascript instruction inside the HEAD tag
	$doc->addScriptCode("window.scrollTo(0,0);", 'JavaScript', SCRIPT_START);
	// add a javascript instruction in the end of the BODY tag
	$doc->addScriptCode("document.write('this simple text was written using JavaScript');", 'JavaScript', SCRIPT_END);
	// add a CSS file
	$doc->addStyle("resources/css.example.css", 'screen');
	// the following line shows how to define a CSS file that will be used exclusively in print mode
	$doc->addStyle("resources/cssprint.example.css", 'print');
	// configure BODY tag attributes
	$doc->addBodyCfg(array('topmargin'=>0, 'leftmargin'=>0));
	// attach body events
	$doc->attachBodyEvent('onLoad', "testFunction()");
	// append HTML content in the end of the BODY tag
	$doc->appendBodyContent("<!-- extra body content -->");
	
	/**
	 * create a new DocumentElement instance to build the header slot
	 */
	$header =& new DocumentElement();
	/**
	 * put a template file in the element buffer
	 */
	$header->put('resources/template3.include.tpl', T_BYFILE);
	/**
	 * the include blocks must be resolved before the parse() call
	 */
	$header->includeAssign('include_block', 'resources/template4.include.tpl', T_BYFILE);
	/**
	 * parse the element content and assign a simple variable
	 */
	$header->parse();
	$header->assign('date_time', date("d/m/Y H:i:s"));
	/**
	 * attach the element (only accepts direct assign if the right side is a DocumentElement instance)
	 * if you want to perform operations in the template later in your code, don't forget the reference operator
	 */
	$doc->elements['header'] =& $header;
	/*	
	
	/**
	 * create another element to generate the "menu" slot
	 */
	$menu =& new DocumentElement();
	/**
	 * insert a template file and parse the content
	 */
	$menu->put('resources/template5.include.tpl', T_BYFILE);
	$menu->parse();
	/**
	 * assign a simple variable
	 */
	$menu->assign('some_var', 'The quick brown fox jumps over the lazy dog');
	/**
	 * insert the element content in the document, using the getContent() method, 
	 * that returns the template content in a string format
	 */
	$doc->elements['menu'] = $menu->getContent();
	
	/**
	 * create another instance of DocumentElement to build the "main" slot
	 */
	$main =& new DocumentElement();
	/**
	 * if you want to (or must) use echo and print statements, you can use output buffering
	 * however, we don't recommend that kind of freestanding code mixing php and html
	 */
	ob_start();
	echo "<table width='100%' cellpadding='2' cellspacing='0' border='0'>";
	for ($i=1; $i<=10; $i++) {
		echo "<tr><td>LINE $i</td></tr>";
	}
	echo "</table>";
	$main->put(ob_get_contents(), T_BYVAR);
	ob_end_clean();	
	/**
	 * put simple string vars in the template
	 */
	$main->put('Main Slot<br>', T_BYVAR);
	$main->put('This is a text with a {variable}<br>', T_BYVAR);
	$main->put('Generation Time: {generation_time} seconds', T_BYVAR);
	/**
	 * parse the template, assign variables and attach it to the document
	 */
	$main->parse();
	$main->assign('variable', "VARIABLE");
	$main->assign('generation_time', round($tc->getElapsedTime(), 3));
	$doc->elements['main'] =& $main;

	/**
	 * save the final HTML code in a file
	 */
	$doc->toFile('resources/document.html');
	
	/**
	 * output the content buffer
	 */
	$doc->display();	
	

?>