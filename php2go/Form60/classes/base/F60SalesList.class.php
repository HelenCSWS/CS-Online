<?php

import('php2go.base.Document');
import('php2go.data.PagedDataSet');
import('php2go.util.Number');
import('Form60.dal.dalSales');
import('Form60.base.F60DbUtil');
import('Form60.util.F60Common');

class F60SalesList extends PagedDataSet
{
	var $_Document;
	var $customerID;
	var $orderBy = "sale_date";
	var $orderType = "d";
	var $estate_id = null;
	var $page = 1;
	var $sqlCode;
	
	var $Template = NULL;
	var $templateFile = 'saleslist.tpl';
	var $sortSymbols; 
	
	var $isQuarter=false;
	var $sales_period;
	var $sales_year;
	
	var $store_type_id;
	var $pageSize = 1000;
	var $total_wh =0;
	var $total_cases = 0;
	var $total_rt=0;
	var $total_profit=0;
	
	var $isLoad =false;
	
	var $province_id =1;
	var $displaySales=false;
	
    function F60SalesList($Document, $customerID,$sales_period,$sales_year,$isQuarter,$store_type_id,$page =1,$isLoadDate=true,$province_id=1, $displaySales=true)
    {

     	PagedDataSet::PagedDataSet('db');
		PagedDataSet::setPageSize($this->pageSize);
		
		if ($Document)
		{
		    $Document->addScript('resources/js/javascript.orderlist.js');
		    $this->_Document = $Document;
		}
		$this->customerID = $customerID;
		$this->sales_year = $sales_year;
		$this->sales_period = $sales_period;
		$this->store_type_id = $store_type_id;
		$this->isLoad = $isLoadDate;
		
		$this->templateFile = TEMPLATE_PATH. $this->templateFile;
		$this->Template =& new Template($this->templateFile); 
		if ($this->getConfigVal('TEMPLATE_CACHE', FALSE))
		    $this->Template->setCacheProperties($this->getConfigVal('FORM60_CACHE_PATH'));
		
		$this->Template->parse();
		$this->displaySales=$displaySales;
	
		$this->province_id = $province_id;
	//	$this->sortSymbols = array('a' => 5,'d' => 6);
	
	$this->sortSymbols = & F60Common::sortSymbols();
		
		if ( $isQuarter==1)
		$this->isQuarter=true;
		
		$this->page =$page;           
    }
    
	function _loadDataset()
	{
	 	
		if($this->isLoad)
		{
		 
			$dalSalesData = new dalSalesData();
	 		
	 		$retSales = $dalSalesData->getCustomerSales($this->orderType,$this->orderBy, $this->sales_year,$this->sales_period,$this->isQuarter,$this->store_type_id,$this->customerID,$this->province_id,$this->page,20);
	 		
	 		return $retSales;
		}
	
	}
    
    function _buildContent() 
    {
       if($this->displaySales)
       {
	        $salesData = $this->_loadDataset();      
	        
	        $totalData=$salesData["total_sales"];
	  		
	    	$this->total_sales = 0;
	    	$this->total_cases = 0;
	    	
	    	$this->total_wh=0;
	    	$this->total_rt=0;
	    	$lineData ="";
		
	 		while ($lineData = $totalData->fetch()) 
			{
				$this->total_wh = $this->total_wh+$lineData["total_amount"];
				$this->total_rt = $this->total_rt+$lineData["total_sales"];
					
				$this->total_cases = $this->total_cases+$lineData["cases_sold"];
				$this->total_profit = $this->total_profit+$lineData["profit"];
			}   
	                         
	        $recCnts=$this->getTotalRecordCount();
	        
	 	 	$pages = ceil(intval($recCnts)/$this->pageSize);
	
	        $aRow = 0;	
	        
	        $total_sales =0;
			$total_cases =0;
			
			$salesDetails = $salesData["sales_details"];
			$salesDetails->moveFirst();
	       	
		
			while ($lineData = $salesDetails->fetch()) 
	        {
	
	            $aRow++;
	            $this->Template->createBlock('loop_line');
	            $this->Template->assign("row_style", ($aRow % 2)?"gridrowOdd":"gridrowEven");
	            $this->Template->assign("sale_date", $lineData["sale_date"]);
	            $this->Template->assign("wine_country_t", $lineData["country"]);
	            $this->Template->assign("wine_country",  $this->getCut($lineData["country"],6));
	            $this->Template->assign("estate_name", $this->getCut($lineData["estate_name"],25));
	            $this->Template->assign("wine_name", $this->getCut($lineData["wine_name"],20));
	            $this->Template->assign("estate_name_t", $lineData["estate_name"]);
	            $this->Template->assign("wine_name_t", $lineData["wine_name"]);
		 		$this->Template->assign("wine_type_t", $lineData["wine_type"]);
	            $this->Template->assign("cspc_code", $lineData["cspc_code"]);
	            $this->Template->assign("wine_type", $this->getCut($lineData["wine_type"],5));
	            $this->Template->assign("bottles_per_case", $lineData["bottles_per_case"]);
					
	            $this->Template->assign("btl_sold", $lineData["units_sale"]);
	            $this->Template->assign("case_sold", round($lineData["cases_sold"],2));
	            $this->Template->assign("total_price",  Number::fromDecimalToCurrency($lineData["total_amount"],"$", ".", ",", 2, "left"));
	            $this->Template->assign("profit",  Number::fromDecimalToCurrency($lineData["profit"],"$", ".", ",", 2, "left"));
	            $this->Template->assign("total_RT",  Number::fromDecimalToCurrency($lineData["total_sales"],"$", ".", ",", 2, "left"));
	            
	            $total_sales =$total_sales+$lineData["total_amount"];
	            $total_cases =$total_cases+$lineData["cases_sold"];
	           
	        }
	
	
	
			$this->Template->globalAssign("total_whsales",  Number::fromDecimalToCurrency($this->total_wh,"$", ".", ",", 2, "left"));
			$this->Template->globalAssign("total_retail",  Number::fromDecimalToCurrency($this->total_rt,"$", ".", ",", 2, "left"));
			$this->Template->globalAssign("total_profit",  Number::fromDecimalToCurrency($this->total_profit,"$", ".", ",", 2, "left"));
			$this->Template->globalAssign("total_cases", round($this->total_cases,2));
	     	$this->Template->globalAssign("t_pages", $pages);
  		}
		
        
        $this->Template->globalAssign($this->orderBy . "_sort", $this->sortSymbols[$this->orderType]);
        $this->Template->globalAssign("customer_id", $this->customerID);
        $this->Template->globalAssign("sort_by", $this->orderBy);
        $this->Template->globalAssign("sort_type", $this->orderType);
 
        
    }
	function checkIsInternantionalAva()
	{
	
		if($this->isQuarter)
		{
			if($this->sales_period ==1)
				$byPeriod = " 1<= sale_month and sale_month<=3 ";
			else if($this->sales_period ==2)
				$byPeriod = " 4<= sale_month and sale_month<=6 ";
			else if($this->sales_period ==3)
				$byPeriod = " 7<= sale_month and sale_month<=9 ";
			else if($this->sales_period ==4)
				$byPeriod = " 10<= sale_month and sale_month<=12 ";
		}
		else
			$byPeriod = "sale_month=$this->sales_period and sale_year = $this->sales_year";
			
		$sql="Select sale_month from user_sales_summary where $byPeriod";
		
		
		PagedDataSet::load($sql);
		
		return PagedDataSet::getTotalRecordCount();
	
	}
	 
    function getContent() 
    {		
     	
	        $this->_buildContent();
        return $this->Template->getContent();
        
    }
    
   function getCut($listVal,$l)
	{
		$retVal = "";
		if ($listVal != Null && trim($listVal)!="")
		{
            $retVal =$listVal;
           // print strlen($listVal).'   ';
			if (strlen($listVal)>$l)
			{
				//print herer;
				$retVal = substr($listVal,0,$l).'...';
  			}
		}
		return $retVal;

 	}
}


?>