<?php

require_once('config/config.php');       
//process ajax calls
require_once('ajax/ajaxhandler.php');
   

if (isset($_REQUEST['page_name']))
{
	$pageClass = $_REQUEST['page_name'];
	
	 //import only what is needed
	import('Form60.pages.' . $pageClass); 
	$doc = & new $pageClass;
	
	$doc->appendHeaderContent($xajax->getJavascript("ajax/"));
	$doc->display();

}
if (isset($_REQUEST['report_page_name']))
{
	$pageClass = $_REQUEST['report_page_name'];
	
	 //import only what is needed
	import('Form60.exportreports.' . $pageClass); 
	$doc = & new $pageClass;
	
	$doc->appendHeaderContent($xajax->getJavascript("ajax/"));
	$doc->display();

}

?>