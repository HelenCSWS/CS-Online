<?php

import('php2go.base.Document');
import('php2go.data.PagedDataSet');
import('Form60.bll.bllSSDSData');
import('Form60.util.F60Date');



class ssdsSummaryList extends PagedDataSet
{
	var $_Document;
	var $page = 1;
	var $sqlCode;
	
	var $Template = NULL;
	var $templateFile;	
	
	var $user_id;
	var $sale_month;
	var $sale_year;
	var $store_type;
	var $current_page;
	var $pageSize;
	var $bonus_type;
	var $province_id;
	var $commission_type;	
	

	function ssdsSummaryList($Document,$user_id,$sale_month,$sale_year,$store_type,$bonus_type=-1,$currentpage=1)
	{
		$this->user_id = $user_id;
		$this->sale_month = $sale_month;
		$this->sale_year = $sale_year;
		$this->store_type = $store_type;
		$this->current_page = $currentpage;
		$this->bonus_type = $bonus_type; 

		
		$this->province_id = $_COOKIE["F60_PROVINCE_ID"];
      
		PagedDataSet::PagedDataSet('db');
		PagedDataSet::setPageSize(20);

		if ($Document)
		{
			$Document->addScript('resources/js/javascript.ssdsreports.js');
			$this->_Document = $Document;
		}
		
		$this->templateFile = 'ssdsSummaryList.tpl';
		
		$this->templateFile = TEMPLATE_PATH. $this->templateFile;
		$this->Template =& new Template($this->templateFile);
		if ($this->getConfigVal('TEMPLATE_CACHE', FALSE))
			$this->Template->setCacheProperties($this->getConfigVal('FORM60_CACHE_PATH'));
		
		$this->Template->parse();
   
    }


	function _loadDataset()
	{
		PagedDataSet::setCurrentPage($this->page);
	}

  
	function _buildContent()
	{
		$aRow = 0;
		$bllSSDS = new SSDSData();
				
		$this->pageSize = 20;
		
		if ($this->store_type!=-1)
		{
			$this->pageSize = 25;			
		}
		
		$report_store_type = $this->store_type;
		if($this->bonus_type==6)
			$report_store_type = 6;
		if($this->bonus_type==8)
			$report_store_type = 8;



		$reportInfo = $bllSSDS->GetSalesReport($this->sale_month,$this->sale_year,$this->user_id,$report_store_type,1,$this->pageSize,$this->current_page );		
		$totalRecords=$reportInfo["total_records"];
		$subTotal_case=0;
		$subTotal_profit=0;
		
		$sales = $reportInfo["sales_details"];
		$nRows = 0;
		
		while ($sale=$reportInfo["sales_details"]->fetch())
		{
		
			$this->Template->createBlock('loop_line');
			$this->Template->assign("row_style", ($nRows % 2)?"cellA":"cellB");
			$this->Template->assign("license_no", ($sale['licensee_no']!="")?$sale['licensee_no']:"&nbsp;");
			
			if($this->bonus_type!=6)
			{
				$this->Template->assign("store_type", ($sale['type']!="")?$sale['type']:"&nbsp;");
				$this->Template->globalAssign("store_type_title",  "Store type");	
			}
			else
			{
				$this->Template->globalAssign("isDisplay_title",  "none");	
				$this->Template->globalAssign("isDisplay_type",  "none");	
			}
			
			//cut value
			$this->Template->assign("customer_name",($sale['customer_name']!="")?$this->getCut($sale['customer_name'],40):"&nbsp;");
			$this->Template->assign("city",($sale["city"]!="")?$this->getCut($sale["city"],15):"&nbsp;");
			
			$address =$this->getCut($sale["address"],25);
			
			
			
			if(substr($address,0,1)=="-")
			{
				$address=substr_replace($address,'',0,2);
			}
   	
          $this->Template->assign("address",($sale["address"]!="")?F60Date::ucwords1($address):"&nbsp;");
            
			$this->Template->assign("total_cases", $sale["total_cases"]);
         $this->Template->assign("bts_sold", $sale["total_bts_sold"]);  
			
			$t_total_profit = number_format($sale["total_profit"], 2, '.', ','); 
			$total_sales = "$".number_format($sale["total_sales"], 2, '.', ','); 
			
			$rt_sales = "$".number_format($sale["rt_sales"], 2, '.', ','); 
			//	print $t_total_profit;
  			$this->Template->assign("total_profit", '$'.$t_total_profit);
  			
  		//	print $total_sales;
  			$this->Template->assign("total_sales", $total_sales);
  			$this->Template->assign("total_RT", $rt_sales);
  		
  			
  			$subTotal_case =$subTotal_case +$sale["total_cases"];
  			$subTotal_profit =$subTotal_profit +$sale["total_profit"];
  		
  			
  			 $nRows++;
  		
      }
         
         
		if($this->store_type!=-1)
		{
		 
			$summary_details = $reportInfo['summary_details'];

			$total_cases_sold= $summary_details[0]['total_cases'];
			$t_total_profit= $summary_details[0]['total_profit'];
			$t_total_units= $summary_details[0]['total_units'];
			
			$this->Template->globalAssign("sub_total_cases",  '<b>Total: '.$total_cases_sold);
			$this->Template->globalAssign("sub_tlt_bts_sold",  $t_total_units);
			
			$t_total_profit = number_format($t_total_profit, 2, '.', ',');
			$this->Template->globalAssign("sub_total_profit",  ('$'.$t_total_profit));
		
		}
		$pageCount = ceil($totalRecords/$this->pageSize); //$totalRecords/$this->pageSize	
				
		$this->Template->globalAssign("current_page",  $this->current_page);
		
		$this->Template->globalAssign("page",  $this->current_page);
		$this->Template->globalAssign("total_page",  $pageCount);
		$this->Template->globalAssign("isDisplay", "block" );

		if ($this->current_page>1)
		{
			$this->Template->createBlock('prev_page_link');
			$this->Template->assign("prev_page", ($this->current_page-1));
		}

		if ($totalRecords>$this->pageSize &&  $this->current_page <$pageCount)
		{
			$this->Template->createBlock('next_page_link');
			$this->Template->assign("next_page", ($this->current_page-1));
		}
	}

	function getContent()
	{
		$this->_buildContent();
		return $this->Template->getContent();	
	}
    


	function getCut($listVal,$l)
	{
		 return F60Date::ucwords1($listVal);
 	}
}
?>
