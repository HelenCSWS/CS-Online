<?php

import('php2go.base.Document');
import('php2go.data.PagedDataSet');
import('php2go.util.Number');
import('Form60.bll.bllsupplierData');
import('Form60.util.F60Date');
import('Form60.base.F60DbUtil');
import('Form60.util.F60Common');

class SupplierSalesList extends PagedDataSet
{
    var $_Document;
    var $estate_id;
    var $orderBy = "";
    var $orderType = "d";
    var $current_page = 1;
    var $sqlCode;
    
    var $user_id ="";
    var $Template = NULL;
    var $templateFile = 'sp_bc_sales.tpl';
    var $sortSymbols; 

	 var $isQuarter=false;
	 var $date1;
	 var $date2;
	 var $dateType=0;
	 var $province_id;
	 
	 var $store_type_id;
	 var $pageSize =27;
	 var $total_wh =0;
	 var $total_cases = 0;
	 var $total_rt=0;
	 var $total_profit=0;
	 var $is_international=0;
	 var $isLoad =false;
	 var $wine_id ="";
	 
	 var $vintage ="";
	 
	 var $isSearch =false;
	 var $isFirst=false;
	 /*
		for display inovices list for search inovices page, user $dateType for transfer: $searchType, $date1 for $searchValue, $date2 for isstart
		
		for display sales list for supplier page, $date1 and $dates remain the orginale meaning
	 
	 */

	 
	 
	function SupplierSalesList($Document,$estate_id, $date1, $date2, $order_by,$order_type, $dateType=2, $store_type_id=-1, $user_id=-1,$province_id=1,$wine_id="",$vintage, $reportType=1, $page = 1, $isSearch=false,$isFirst=false)
    {
     	$this->isSearch = $isSearch;
     	
     
		if ($Document)
		{
			$Document->addScript('resources/js/javascript.suppliersales.js');
			$this->_Document = $Document;
		}
		
		$this->estate_id = $estate_id;
		
		
		if($province_id ==1 && (F60DbUtil::checkIsBCByEstate($this->estate_id)))
		{		
			$this->templateFile = 'sp_bc_sales.tpl';
		}
		else
		{
			$this->templateFile = 'sp_nt_sales.tpl';	
		}
	
		$this->isFirst=$isFirst;
		
		$this->isSearch = $isSearch;
		
		$this->date1 = $date1;
		
		$this->date2=$date2;
		if($this->isSearch)
			$this->date2 =($date2==0)?false:true;
		
	
		
		
		$this->dateType = $dateType;
		
		$this->store_type_id = $store_type_id;
		$this->user_id = $user_id;
		$this->orderBy =$order_by;
		$this->orderType =$order_type;
		$this->province_id = $province_id;
		$this->wine_id =$wine_id;
		$this->vintage =$vintage;
		$this->current_page = $page;
      
      
		$this->templateFile = TEMPLATE_PATH. $this->templateFile;
		$this->Template =& new Template($this->templateFile); 
		if ($this->getConfigVal('TEMPLATE_CACHE', FALSE))
		$this->Template->setCacheProperties($this->getConfigVal('FORM60_CACHE_PATH'));
		
		$this->Template->parse();
		
	/*	$this->sortSymbols = array(
		'a' => 5,
		'd' => 6
		);*/
		
		$this->sortSymbols = & F60Common::sortSymbols();
			
		if ( $dateType==0)
			$this->isQuarter=true;
    } 
 
    function _buildContent() 
    {
		$this->Template->globalAssign("isDisplayTopPage", "block");  

	
		
    	if($this->isSearch)
    	{
    	 
    		if(!$this->isFirst)
    		{
				$this->_buildInoviceContent();
			
			}
			else
			{
			 
			 	$this->Template->globalAssign("isDisplayTopPage", "none");
				$this->Template->globalAssign("isDisplay", "none");
			}
		 	$this->Template->globalAssign("isDisplayTCs", "none");
		 	$this->Template->globalAssign("isDisplayTAm", "none");
		}
		else
		{
			$this->_buildSalesContent();
		}
	}
	
	function _buildInoviceContent() 
    {
		$spSales = new suppliersData();
		//function getUnPaidInvoice($searchType,$searchValue, $estate_id, $orderBy,$order_type,$currentpage, $pageSize, $isStart=true)

		$searchType = $this->dateType;
		$searchValue = $this->date1;
		$isStart = $this->date2;
		
		
		$reportInfo=$spSales->getUnPaidInvoice($searchType, $searchValue, $this->estate_id, $this->orderBy,$this->orderType, $this->current_page,$this->pageSize, $isStart);
		
		$sales = $reportInfo["sales_details"];
		$totalRecords =$reportInfo["total_records"];
		$nRows = 0;
		
		
		$total_bc_sales=0;
		$total_bc_bts=0;
		$total_bc_cs=0;
	
		
		//print count($sales);
        while($sale=$reportInfo["sales_details"]->fetch())     
		{		
			$this->Template->createBlock('loop_line');
			$this->Template->assign("row_style", ($nRows % 2)?"gridrowOdd":"gridrowEven");
			
			$address = ($sale["address"]!="")?F60Date::ucwords1($sale["address"]):"&nbsp;";
			if(substr($address,0,1)=="-")
			{
				$address=substr_replace($address,'',0,2);
			}
			
			$New_Rule_date = "2015-04-01";
         
		     $isNewRule =0;
			 if(strtotime($sale["order_date"])>=strtotime($New_Rule_date))
			{
				$isNewRule =1;
			}
			
			$this->Template->assign("order_date", date("m/d/Y",strtotime($sale["order_date"])));
			$this->Template->assign("invoice_number", $sale["invoice_number"]);
			$this->Template->assign("store_type", $sale["license_name"]);
			$this->Template->assign("lic_no", $sale["licensee_number"]);
			$this->Template->assign("customer", htmlentities($this->getCut($sale["customer_name"],30)));
			$this->Template->assign("tit_customer", htmlentities($sale["customer_name"]));
			$this->Template->assign("tit_address", $sale["address"].' '.$sale["city"]);
			$this->Template->assign("address_data", $this->getCut($sale["address"].' '.$sale["city"],35));
			$this->Template->assign("cases", $sale["cases_sold"]);
			$this->Template->assign("bottles", $sale["btl_sold"]);
			
			if($isNewRule==1)
				{
			

										
					$this->Template->assign("amount", Number::fromDecimalToCurrency($sale["new_amount"],"$", ".", ",", 2, "left")); 
				}
				else
				{
				 	$this->Template->assign("amount", Number::fromDecimalToCurrency($sale["total_amount"],"$", ".", ",", 2, "left")); 
				}
				
			$this->Template->assign("isPaid", "Not Paid"); 
			$this->Template->assign("isPending", $sale["order_status"]); 
			$this->Template->assign("user", $sale["user_name"]); 
			$this->Template->assign("order_id", $sale["order_id"]); 
				
			$this->Template->globalAssign("showStType", "block");
		
			$nRows++;
  		
		}
   
		$this->Template->globalAssign("showStoreType", "block");
	

		//total
	
		if($nRows!=0)
		{
			
			$this->Template->globalAssign($this->orderBy . "_sort", $this->sortSymbols[$this->orderType]);
			
	        $this->Template->globalAssign("sort_by", $this->orderBy);
	        $this->Template->globalAssign("sort_type", $this->orderType);
	        
	        
			$pageCount = ceil($totalRecords/$this->pageSize); //$totalRecords/$this->pageSize	
			$this->Template->globalAssign("currentpage", $this->current_page);
			
			$this->Template->globalAssign("page",  $this->current_page);
			$this->Template->globalAssign("total_page",  $pageCount);
			$this->Template->globalAssign("isDisplay", "block" );
			$this->Template->globalAssign("total",  $totalRecords);
	
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
			
			$this->Template->globalAssign("total",  $totalRecords);
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
    
    function _buildSalesContent() 
    {
		$spSales = new suppliersData();
		//function getSales($estate_id, $date1, $date2, $order_by,$order_type, $is_internaional=0, $dateType=2, $store_type_id=-1, $user_id=-1,$province_id=1,$wine_id="", $reportType=1, $pageSize = 999, $page = 1)
		
	
		$reportInfo=$spSales->getSales($this->estate_id, $this->date1, $this->date2, $this->orderBy,$this->orderType,  $this->dateType, $this->store_type_id, $this->user_id,$this->province_id,$this->wine_id,$this->vintage,0,$this->pageSize, $this->current_page);
		
		$sales = $reportInfo["sales_details"];
		$totalRecords =$reportInfo["total_records"];
		$nRows = 0;
		
		
		$total_bc_sales=0;
		$total_bc_bts=0;
		$total_bc_cs=0;
		
		//print count($sales);
        while($sale=$reportInfo["sales_details"]->fetch())     
		{		
			$this->Template->createBlock('loop_line');
			$this->Template->assign("row_style", ($nRows % 2)?"gridrowOdd":"gridrowEven");
			
			$address = ($sale["address"]!="")?F60Date::ucwords1($sale["address"]):"&nbsp;";
			if(substr($address,0,1)=="-")
			{
				$address=substr_replace($address,'',0,2);
			}
			
			if($this->province_id==2 || !F60DbUtil::checkIsBCByEstate($this->estate_id))
		 	{
				$this->Template->assign("store_type", $sale["license_name"]);
				$this->Template->assign("lic_no", $sale["licensee_number"]);
				$this->Template->assign("customer", htmlentities($this->getCut($sale["customer_name"],30)));
				$this->Template->assign("tit_customer", $sale["customer_name"]);
				$this->Template->assign("tit_address", $address.' '.$sale["city"]);
				$this->Template->assign("address_data", $this->getCut($address.' '.$sale["city"],30));
				$this->Template->assign("cases", $sale["cases_sold"]);
				$this->Template->assign("bottles", $sale["btl_sold"]);
				$this->Template->assign("amount", Number::fromDecimalToCurrency($sale["total_amount"],"$", ".", ",", 2, "left")); 
				$this->Template->assign("wine_info", $this->getCut($sale["wine_name"],30));
				$this->Template->assign("tit_wine", $sale["wine_name"]); 
				$this->Template->assign("cspc", $sale["cspc"]); 
				$this->Template->assign("user", $sale["user_name"]); 
	       	
			}
			else
			{	
			 
			 	$New_Rule_date = "2015-04-01";
         
			     $isNewRule =0;
				 if(strtotime($sale["order_date"])>=strtotime($New_Rule_date))
				{
					$isNewRule =1;
				}
	
			
				$this->Template->assign("order_date", date("m/d/Y",strtotime($sale["order_date"])));
				$this->Template->assign("invoice_number", $sale["invoice_number"]);
				$this->Template->assign("store_type", $sale["license_name"]);
				$this->Template->assign("lic_no", $sale["licensee_number"]);
				$this->Template->assign("customer", $this->getCut($sale["customer_name"],30));
				$this->Template->assign("tit_customer", $sale["customer_name"]);
				$this->Template->assign("tit_address", $sale["address"].' '.$sale["city"]);
				$this->Template->assign("address_data", $this->getCut($sale["address"].' '.$sale["city"],35));
				$this->Template->assign("cases", $sale["cases_sold"]);
				$this->Template->assign("bottles", $sale["btl_sold"]);
				
				if($isNewRule==1)
				{
			
					$this->Template->assign("amount", Number::fromDecimalToCurrency($sale["new_amount"],"$", ".", ",", 2, "left")); 
										$total_bc_sales = $sale["new_amount"] +$total_bc_sales;
				}
				else
				{
					$this->Template->assign("amount", Number::fromDecimalToCurrency($sale["total_amount"],"$", ".", ",", 2, "left")); 
					$total_bc_sales = $sale["total_amount"] +$total_bc_sales;
				}
				
				$this->Template->assign("isPaid", $sale["payment_status"]); 
				$this->Template->assign("isPending", $sale["order_status"]); 
				$this->Template->assign("user", $sale["user_name"]); 
				$this->Template->assign("order_id", $sale["order_id"]); 
				
		//		echo Number::fromDecimalToCurrency($sale["total_amount"]);
			//$total_bc_sales =
			

			//	$total_bc_sales = $sale["total_amount"] +$total_bc_sales;
				
		
			
				$total_bc_bts=$total_bc_bts+$sale["btl_sold"];
				$total_bc_cs=$total_bc_cs+$sale["cases_sold"];
				
			
	      	}
		
			if($this->province_id ==2)
			{
				$this->Template->globalAssign("showStType", "none");
			}
			else
			{
				$this->Template->globalAssign("showStType", "block");
			}
			$nRows++;
  		
		}
   
		if($this->province_id ==2)
		{
			$this->Template->globalAssign("showStoreType", "none");
		}
		else
		{
			$this->Template->globalAssign("showStoreType", "block");
		}

		//total
	
		if($nRows!=0)
		{

			$total_btls = $reportInfo["totalSales"][0]["btl_sold"];
			$total_amount = $reportInfo["totalSales"][0]["total_amount"];
			$totalcase = $reportInfo["totalSales"][0]["cases_sold"];
			
			
			if(count($reportInfo["totalSales"])>1)
			{		
				$totalcase = $totalcase+$reportInfo["totalSales"][1]["cases_sold"];
				$total_btls = $totalcase+$reportInfo["totalSales"][1]["btl_sold"];
				$total_amount = $total_amount+$reportInfo["totalSales"][1]["total_amount"];
			}
			
			if($this->province_id==2 || !F60DbUtil::checkIsBCByEstate($this->estate_id) )
			{
				  
			    $this->Template->globalAssign("tol_cases",  Number::fromDecimalToCurrency(round($totalcase,2),"", ".", ",", 2, "left"));
			     
				$totalBts = Number::fromDecimalToCurrency($total_btls,"", ".", ",", 2, "left");
				$totalBts = substr($totalBts, 0, (strlen($totalBts)-3));
				$this->Template->globalAssign("tol_btl", $totalBts );
				$this->Template->globalAssign("tol_amount",  Number::fromDecimalToCurrency($total_amount,"$", ".", ",", 2, "left"));
			}
			else
			{
				
				
				$this->Template->globalAssign("tol_cases",  $total_bc_cs);
			     
				$totalBts = Number::fromDecimalToCurrency($total_bc_bts,"", ".", ",", 2, "left");
				$totalBts = substr($totalBts, 0, (strlen($totalBts)-3));
				$this->Template->globalAssign("tol_btl", $totalBts );
				
				$total_bc_sales = Number::fromDecimalToCurrency($total_bc_sales,"$", ".", ",", 2, "left"); 
				
				$this->Template->globalAssign("tol_amount", $total_bc_sales);
			}
			
			$this->Template->globalAssign($this->orderBy . "_sort", $this->sortSymbols[$this->orderType]);
			
	        $this->Template->globalAssign("sort_by", $this->orderBy);
	        $this->Template->globalAssign("sort_type", $this->orderType);
	        
	        
			$pageCount = ceil($totalRecords/$this->pageSize); //$totalRecords/$this->pageSize	
			$this->Template->globalAssign("currentpage", $this->current_page);
			
			$this->Template->globalAssign("page",  $this->current_page);
			$this->Template->globalAssign("total_page",  $pageCount);
			$this->Template->globalAssign("isDisplay", "block" );
			$this->Template->globalAssign("total",  $totalRecords);
	
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
			
			$this->Template->globalAssign("total",  $totalRecords);
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
			if (strlen($listVal)>$l)
			{
				$retVal = substr($listVal,0,$l).'...';
			}
		}
		$retVal = ucwords(strtolower($retVal));
		return $retVal;
 	}
}

?>