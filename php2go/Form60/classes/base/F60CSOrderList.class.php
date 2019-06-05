<?php

import('php2go.base.Document');
import('php2go.data.PagedDataSet');
import('php2go.util.Number');
import('Form60.base.F60DbUtil');
import('Form60.util.F60Common');

class F60CSOrderList extends PagedDataSet
{
    var $_Document;
    var $customerID;
    var $orderBy = "order_date";
    var $orderType = "d";

    var $order_year;
    var $page = 1;
    var $sqlCode;
    
    var $Template = NULL;
    var $templateFile = 'CSOrderlist.tpl';
    var $sortSymbols; 

    var $period;
    var $isQuater;
    
    var $estate_id;

    function F60CSOrderList($Document, $customerID,$period,$order_year, $isQuater)
    {
       PagedDataSet::PagedDataSet('db');
       PagedDataSet::setPageSize(3000);
        
       if ($Document)
       {
            $Document->addScript('resources/js/javascript.csproduct.js');
             $this->_Document = $Document;
       }
       $this->customerID = $customerID;
       $this->order_year = $order_year;
       $this->isQuater = $isQuater;
       $this->period = $period;
       
      // print $this->$period;
       
       $this->templateFile = TEMPLATE_PATH. $this->templateFile;
       $this->Template =& new Template($this->templateFile);
       if ($this->getConfigVal('TEMPLATE_CACHE', FALSE))
            $this->Template->setCacheProperties($this->getConfigVal('FORM60_CACHE_PATH'));
        
       $this->Template->parse();
       
       $this->sortSymbols = & F60Common::sortSymbols();
	   
	   /*array(
			'a' => '&#9650;',
			'd' => '&#9660;'
		);*/
                
    }

    function _loadDataset()
    {
     		$current_month = date(n);
     		
			if($this->order_year<date("Y"))
			{
				$current_month =12;
			}
			if($this->isQuater)
			{
				 if($this->period ==1)
				  		$byPeriod = " 1<= month(o.delivery_date) and month(o.delivery_date)<=3 ";
				 else if($this->period ==2)
				 		$byPeriod = " 4<= month(o.delivery_date) and month(o.delivery_date)<=6 ";
				 else if($this->period ==3)
				 		$byPeriod = " 7<= month(o.delivery_date) and month(o.delivery_date)<=9 ";
				 else if($this->period ==4)
				 		$byPeriod = " 10<= month(o.delivery_date) and month(o.delivery_date)<=12 ";
				else if($this->period ==-1)
				{
				 		$byPeriod = " 1<= month(o.delivery_date) and month(o.delivery_date)<=$current_month ";
				}
			  		
			}
			else
			{
				$byPeriod = "month(o.delivery_date)=$this->period";
				
			}
				
			
				
				$order_type =($this->orderType == "d")?"DESC":"ASC";
				
				$amount= & F60DbUtil::getAmountOwned();
            	$sqlCode = "SELECT o.order_id, oi.ordered_quantity, 
o.lkup_delivery_status_id, 
o.delivery_date as order_date, invoice_number, e.estate_name,
 l.caption as payment_status, 
 
  round((sum((oi.price_per_unit * oi.ordered_quantity)- ifnull(discount,0))*(o.pst_factor*o.PST_included+o.gst_factor+1))
 +ifnull(o.adjustment_1,0)- ifnull(discount,0),2 ) total_prices,
 
 round((sum((oi.promotion_price * oi.ordered_quantity)- ifnull(discount,0))*(o.pst_factor*o.PST_included+o.gst_factor+1))
 +ifnull(o.adjustment_1,0),2 ) total_winery,
 
 
 os.caption as order_status,
 o.lkup_store_type_id 
							 
							FROM estates e, cs_product_orders o
                            inner join lkup_order_payment_status l on 
                            o.lkup_payment_status_id= l.lkup_payment_status_id
                            inner join lkup_order_statuses os on 
                            
                            o.lkup_delivery_status_id= os.lkup_order_status_id
                            inner join cs_product_order_items oi on o.order_id = oi.order_id
                            
	                        where o.deleted = 0 
                            and e.estate_id=o.estate_id
                            and o.customer_id=$this->customerID 
								
	                        and year(o.delivery_date)=$this->order_year
									 and $byPeriod
	                        group by o.order_id
	                        order by $this->orderBy $order_type";
      
		        PagedDataSet::setCurrentPage($this->page);
		        PagedDataSet::load($sqlCode);                      
    }
    
    function _buildContent() 
    {
        $this->_loadDataset();
		$aRow = 0;	
		$total_sales =0;
		$isPaid = 0;
		$total_amount=0;
        while ($lineData = PagedDataSet::fetch()) 
        {
            $aRow++;
            $this->Template->createBlock('loop_line');
            $this->Template->assign("row_style", ($aRow % 2)?"gridrowOdd":"gridrowEven");
            $this->Template->assign("order_date", date("m/d/Y",strtotime($lineData["order_date"])));
		
		    $this->Template->assign("invoice_number", $lineData["invoice_number"]);
            $this->Template->assign("product_name", $lineData["estate_name"]);
            $this->Template->assign("payment_status", $lineData["payment_status"]);
            $this->Template->assign("order_status", $lineData["order_status"]);
            
 			
			if($lineData["ordered_quantity"]<24)
			{
				$this->Template->assign("total_amount",  Number::fromDecimalToCurrency($lineData["total_prices"],"$", ".", ",", 2, "left"));
				$total_sales =$lineData["total_prices"];
			}	
			else
			{
			
				$this->Template->assign("total_amount",  Number::fromDecimalToCurrency($lineData["total_winery"],"$", ".", ",", 2, "left"));
					$total_sales =$lineData["total_winery"];
			}
		
            $total_amount = $total_amount+$total_sales;
                  	
            $this->Template->assign("order_id", $lineData["order_id"]);
            $this->Template->assign("lkup_order_status_id", $lineData["lkup_delivery_status_id"]);
            
			if($lineData["payment_status"]=="Not paid" && $lineData["order_status"]=="Delivered")
			{
			 	$isPaid =1;						
			}
        }
        
        $this->Template->globalAssign("total",  PagedDataSet::getTotalRecordCount());
        $this->Template->globalAssign("total_sales",  Number::fromDecimalToCurrency($total_amount,"$", ".", ",", 2, "left"));

	/*	$fp = fopen("logs/symbo.log","a");
		fputs($fp,  $this->sortSymbols[$this->orderType]."\n");
		fclose($fp);*/
		
        $this->Template->globalAssign($this->orderBy . "_sort", $this->sortSymbols[$this->orderType]);
        $this->Template->globalAssign("customer_id", $this->customerID);
        $this->Template->globalAssign("sort_by", $this->orderBy);
        $this->Template->globalAssign("sort_type", $this->orderType);
        $this->Template->globalAssign("isPaid", $isPaid);
    }
        
    function getContent() 
    {		
        $this->_buildContent();
        return $this->Template->getContent();
    }
}


?>