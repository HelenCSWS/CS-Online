<?php

import('php2go.base.Document');
import('php2go.data.PagedDataSet');
import('Form60.util.F60Common');


class CSProductsList extends PagedDataSet
{
    var $_Document;
     var $estate_id;
    var $orderBy = "cp.product_name";
    var $orderType = "a";
    var $page = 1;
    var $sqlCode;
    
    var $Template = NULL;
    var $templateFile = 'csproductslist.tpl';
    var $sortSymbols; 

    var $province_id;

    function CSProductsList($Document, $estate_id,$province_id)
    {

        
       PagedDataSet::PagedDataSet('db');
       PagedDataSet::setPageSize(500);
       
       if ($Document)
       {
            $Document->addScript('resources/js/javascript.csproduct.js');
            $this->_Document = $Document;
       }
     //  $this->customerID = $customerID;
       $this->estate_id =$estate_id;
       $this->province_id =$province_id;
       
 
       
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

        $sqlTemplate = "SELECT cp.estate_id, cp.cs_product_id,cp.product_name, cn.total_units, round(cn.total_units/cp.units_per_case,2) total_cs

                        FROM cs_products cp, cs_product_inventory cn
                        
                        where cp.cs_product_id = cn.cs_product_id
                        
                        and cn.province_id = %s
                        and cp.estate_id = %s
                        
                        order by %s %s";
                  
      /*    $sqlTemplate = "SELECT cp.estate_id, cp.cs_product_id,cp.product_name, cn.total_units, round(cn.total_units/cp.units_per_case,2) total_cs

                        FROM cs_products cp, cs_product_inventory cn
                        
                        where cp.cs_product_id = cn.cs_product_id
                        
                        and cn.province_id = %s
                        and cp.estate_id = %s
                        order by %s %s";*/
                
                        
        $sqlCode = sprintf($sqlTemplate, $this->province_id, 
                        $this->estate_id,
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
             $this->Template->assign("product_name", $lineData["product_name"] );//. " -  " . $lineData["vintage"]." - ".$lineData["color"]);
            $this->Template->assign("total_units", intval($lineData["total_units"]));
            $this->Template->assign("total_cs", intval($lineData["total_cs"]));
            $this->Template->assign("cs_product_id", $lineData["cs_product_id"]);
        }
        
        
        $this->Template->globalAssign("total",  PagedDataSet::getTotalRecordCount());
        
        $this->Template->globalAssign($this->orderBy . "_sort", $this->sortSymbols[$this->orderType]);
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