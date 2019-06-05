<?php

/**
 * Perform the necessary imports
 */

import('Form60.base.F60MarketList');
define('MAX_PAGE_SIZE', 15000);

class mkprintreport extends Document
{
	function mkprintreport()
	{
        Document::Document('resources/template/customercpreport.tpl');
        Document::addStyle(CSS_PATH . 'report.css');
    }

	function display()
	{
        $listControl = & new F60MarketList(&$this, 0, MAX_PAGE_SIZE, true);
		$this->elements['list_news'] = $listControl->getContent();

        $listControl2 = & new F60MarketList(&$this, 1, MAX_PAGE_SIZE, true);
		$this->elements['list_updates'] = $listControl2->getContent();

        $listControl3 = & new F60MarketList(&$this, 2, MAX_PAGE_SIZE, true);
		$this->elements['list_oobs'] = $listControl3->getContent();

        Document::display();
    }
}

?>