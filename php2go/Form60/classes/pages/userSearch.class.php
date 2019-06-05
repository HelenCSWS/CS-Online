<?php

/**
 * Perform the necessary imports
 */

import('Form60.base.F60PageBase');
import('php2go.data.Report');

class userSearch extends F60PageBase 
{
	
	function userSearch() 
	{		
	
            F60PageBase::F60PageBase('userSearch', 'Search users', 'userSearch.tpl');
            //$this->addtoPageStack();
	}
		
	
	function display() 
	{
                $report =& new Report(
		        'resources/xml/userSearch.xml', 
		        'resources/template/userSearchReport.tpl', 
		        $this);
                $report->hasHeader = TRUE; 										// enable column headers
                $report->enableHighlight('#ffffff', '#ff6800'); 				// highlight search values
                $report->setStyleMapping('links', 'input', 'input', 'title');	// set link, input, button and title styles
                $report->setAlternateStyle('cellA', 'cellB');					// set alternate style (at least 2)
                $report->setColumnAlias('first_name', 'First name');					// set column aliases
                $report->setColumnAlias('last_name', 'Last name');
                $report->setColumnAlias('username', 'Login');
                $report->setColumnAlias('email1', 'Email');
                $report->setColumnAlias('phone_cell', 'Cell phone');
                //$report->setColumnSizes(REPORT_COLUMN_SIZES_FIXED);			// all the columns must have the same width
	        $report->setColumnSizes(array(20,20,20,20,20));
                $report->setPagingStyle(REPORT_FIRSTPREVNEXTLAST, array('useButtons' => TRUE, 'useSymbols' => FALSE));
	        $report->build();
                
                $action = array(
                   "Add user" => "main.php?page_name=userAdd",
                   "Edit users" => "main.php?page_name=userAdd&id=4",
                   "Sample pop" =>"javascript:showPopWin('main.php?page_name=F60TestDoc', 500, 300);",
                   $this->getLastPage() => $this->getLastPage()
                );
                
                $this->setActions($action);
                $contents = & $this->getContents();
		$contents->assign('userSearch', $report->getContent());
               
		F60PageBase::display();
	}
}

?>