<?php

import('php2go.base.Document');
import('php2go.data.PagedDataSet');
import('php2go.util.Number');
import('Form60.bll.bllf60reports');
import('Form60.util.F60Date');
import('Form60.util.F60Common');

class F60OVerDueList extends PagedDataSet
{
    var $_Document;
    var $estate_id;
    var $overdue_type;
    var $orderBy = "";
    var $orderType = "d";
    var $current_page = 1;
    var $sqlCode;
    
    var $user_id ="";
    var $Template = NULL;
    var $templateFile = 'sp_bc_sales.tpl';
    var $sortSymbols; 

	 var $isQuarter=false;

	 var $store_type_id;
	 var $pageSize =27;
	 var $total_wh =0;
	 var $total_cases = 0;
	 var $total_rt=0;
	 var $total_profit=0;
	 var $is_international=0;
	 var $isLoad =false;
	 var $estate_id ="";
	 
    function F60OVerDueList($Document,$order_by,$order_type, $estate_id, $store_type_id, $user_id,$overdue_type,$page )
	 
//	 ($Document, $estate_id,$date1,$date2,$isQuarter,$store_type_id,$user_id,$isinternational=0,$pageSize$page =1,$isLoadDate=true)
    {     			
       if ($Document)
       {
            $Document->addScript('resources/js/javascript.reports.js');
            $this->_Document = $Document;
       }
       
       $this->estate_id = $estate_id;
       
	
		$this->templateFile = 'overdue_inv_list.tpl';	
       
       $this->store_type_id = $store_type_id;
       $this->user_id = $user_id;
       $this->estate_id = $estate_id;
       $this->overdue_type = $overdue_type;
       $this->orderBy =$order_by;
       $this->orderType =$order_type;


       $this->current_page = $page;
      
      
      
       
       $this->templateFile = TEMPLATE_PATH. $this->templateFile;
       $this->Template =& new Template($this->templateFile); 
       if ($this->getConfigVal('TEMPLATE_CACHE', FALSE))
            $this->Template->setCacheProperties($this->getConfigVal('FORM60_CACHE_PATH'));
        
       $this->Template->parse();
       
       $this->sortSymbols = & F60Common::sortSymbols();
		
                
    }
 
    
    function _buildContent() 
    {
		 $f60Invoices = new F60ReportsData();
//    	getOverDueInvoices($order_by, $order_type,$page_size,$page,$store_type_id=-1,$overday_type=0,$estate_id=0, $user_id =0)
		

      $reportInfo=$f60Invoices->getOverDueInvoices($this->orderBy, $this->orderType,$this->pageSize,$this->current_page,$this->store_type_id,$this->overdue_type,$this->estate_id, $this->user_id);
		 
		 
		
	//	$sales = $reportInfo["invoicData"];
		$totalRecords =$reportInfo["total_records"];
		$nRows = 0;
		$pageCount = ceil($totalRecords/$this->pageSize); //$totalRecords/$this->pageSize	
		//print count($sales);
        while($sale=$reportInfo["invoicData"]->fetch())
		{
				
			$this->Template->createBlock('loop_line');
			$this->Template->assign("row_style", ($nRows % 2)?"gridrowOdd":"gridrowEven");
			
			$address = ($sale["address"]!="")?F60Date::ucwords1($sale["address"]):"&nbsp;";
			if(substr($address,0,1)=="-")
			{
				$address=substr_replace($address,'',0,2);
			}
			
			
			 $this->Template->assign("order_date", date("m/d/Y",strtotime($sale["order_date"])));
			 $this->Template->assign("over_days", $sale["overdays"]);
	         $this->Template->assign("invoice_number", $sale["invoice_number"]);
	         $this->Template->assign("store_type", $sale["license_name"]);
	         $this->Template->assign("lic_no", $sale["licensee_number"]);
	         $this->Template->assign("customer", $this->getCut($sale["customer_name"],35));
	         $this->Template->assign("tit_customer", $sale["customer_name"]);
	         $this->Template->assign("tit_address", $sale["address"]);
	         $this->Template->assign("address_data", $this->getCut($sale["address"],35));
	        // $this->Template->assign("tit_estate", $sale["estate_name"]);
	         $this->Template->assign("estate",$this->getCut($sale["estate_name"],15));
	         $this->Template->assign("amount", Number::fromDecimalToCurrency($sale["total_amount"],"$", ".", ",", 2, "left")); 
	      	 $this->Template->assign("cases", $sale["cases_sold"]); 
	         $this->Template->assign("user", $sale["user_name"]); 
	         $this->Template->assign("order_id", $sale["order_id"]); 
		
			 $this->Template->globalAssign("showStType", "block");
			
			 $nRows++;
  		}

		if($nRows!=0)
		{
	     	if ($this->current_page>1)
			{
				$this->Template->createBlock('prev_page_link');
				$this->Template->createBlock('btm_prev_page_link');
				$this->Template->assign("prev_page", ($this->current_page-1));
			}
	
			if ($totalRecords>$this->pageSize &&  $this->current_page <$pageCount)
			{
				$this->Template->createBlock('next_page_link');
				$this->Template->createBlock('btm_next_page_link');
				$this->Template->assign("next_page", ($this->current_page-1));
			}
				
			$this->Template->globalAssign("page",  $this->current_page);
			$this->Template->globalAssign("total",  $totalRecords);
			$this->Template->globalAssign("total_page",  $pageCount);
			$this->Template->globalAssign("sort_by", $this->orderBy);
	      	$this->Template->globalAssign("sort_type", $this->orderType);
		    $this->Template->globalAssign("currentpage", $this->current_page);
		}
        else
		{
			$this->Template->globalAssign("total",  0);
			$this->Template->globalAssign("sort_by", $this->orderBy);
	      	$this->Template->globalAssign("sort_type", $this->orderType);
	        	        
	      
		    $this->Template->globalAssign("currentpage", 1);
			
			
			$this->Template->globalAssign("page",  1);
			$this->Template->globalAssign("total_page",  1);
			$this->Template->globalAssign("isDisplay", "none" );
			$this->Template->globalAssign("total",  $totalRecords);		
		}
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
		return htmlentities($retVal);

 	}
}


?>