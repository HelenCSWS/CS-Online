<?php

import('php2go.base.Document');
import('php2go.data.PagedDataSet');
import('Form60.util.F60Common');


class F60WineList extends PagedDataSet
{
    var $_Document;
    var $customerID;
    var $estateID;
    var $orderBy = "estate_name";
    var $orderType = "a";
    var $page = 1;
    var $sqlCode;
    
    var $Template = NULL;
    var $templateFile = 'winelist.tpl';
    var $sortSymbols; 


    function F60WineList($Document, $customerID)
    {
       PagedDataSet::PagedDataSet('db');
       PagedDataSet::setPageSize(500);
       
       if ($Document)
       {
            $Document->addScript('resources/js/javascript.winelist.js');
            $this->_Document = $Document;
       }
       $this->customerID = $customerID;
       
       $this->templateFile = TEMPLATE_PATH. $this->templateFile;
       $this->Template =& new Template($this->templateFile);
       if ($this->getConfigVal('TEMPLATE_CACHE', FALSE))
            $this->Template->setCacheProperties($this->getConfigVal('FORM60_CACHE_PATH'));
        
       $this->Template->parse();
       
       /*$this->sortSymbols = array(
			'a' => 5,
			'd' => 6
		);*/
		
		
	//	$this->sortSymbols =array('a' => '&#9650;','d' => '&#9660;');
		
		 $this->sortSymbols = & F60Common::sortSymbols();
                
    }

    function _loadDataset()
    {
        $sqlTemplate = "SELECT w.wine_id, e.estate_name, w.wine_name, w.vintage, c.allocated,wcolor.caption color, c.sold, c.allocated-c.sold as available 
                        FROM customer_wine_allocations c, wines w, estates e , lkup_wine_color_types wcolor where c.wine_id = w.wine_id and w.estate_id = e.estate_id
                        and w.deleted = 0
                        and w.lkup_wine_color_type_id=wcolor.lkup_wine_color_type_id
                        and c.customer_id = %s
                        and e.estate_id = %s
                        order by %s %s";
                        
                        
        $sqlCode = sprintf($sqlTemplate, $this->customerID, 
                        ($this->estateID)?$this->estateID:"w.estate_id",
                        $this->orderBy,
                        ($this->orderType == "d")?"DESC":"ASC");
        PagedDataSet::setCurrentPage($this->page);
         
        PagedDataSet::load($sqlCode);
        
      
                        
    }
    
    function debugTxt($msg)
    {
		
		$fp = fopen("logs/TEST.log","a");
		fputs($fp, $msg."\n");
		fclose($fp);
	}
    function _buildContent() 
    {
        $this->_loadDataset();
        
        $aRow = 0;	
					
       while ($lineData = PagedDataSet::fetch()) 
        {
            $aRow++;
            $this->Template->createBlock('loop_line');
            $this->Template->assign("row_style", ($aRow % 2)?"gridrowOdd":"gridrowEven");
            $this->Template->assign("estate_name", $lineData["estate_name"]);
            $this->Template->assign("wine_name", $lineData["wine_name"] );//. " -  " . $lineData["vintage"]." - ".$lineData["color"]);
            $this->Template->assign("allocated", $lineData["allocated"]);
            $this->Template->assign("sold", $lineData["sold"]);
            $this->Template->assign("available", $lineData["available"]);
            $this->Template->assign("wine_id", $lineData["wine_id"]);
        }
        
        
        $this->Template->globalAssign("total",  PagedDataSet::getTotalRecordCount());
        
        if($this->orderBy!=="estate_name" )
        $this->Template->globalAssign($this->orderBy . "_sort", $this->sortSymbols[$this->orderType]);
        $this->Template->globalAssign("customer_id", $this->customerID);
        $this->Template->globalAssign("order_by", $this->orderBy);
        $this->Template->globalAssign("order_type", $this->orderType);
    }
        
    function getContent() 
    {		
        $this->_buildContent();
        return $this->Template->getContent();
    }
}


?>