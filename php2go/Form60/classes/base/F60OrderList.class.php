<?php

import('php2go.base.Document');
import('php2go.data.PagedDataSet');
import('php2go.util.Number');
import('Form60.base.F60DbUtil');
import('Form60.util.F60Common');

class F60OrderList extends PagedDataSet
{
    var $_Document;
    var $customerID;
    var $orderBy = "order_date";
    var $orderType = "d";
    var $estate_id = null;
    var $order_year;
    var $page = 1;
    var $sqlCode;
    
    var $Template = NULL;
    var $templateFile = 'orderlist.tpl';
    var $sortSymbols; 

    var $period;
    var $isQuater;

    function F60OrderList($Document, $customerID,$period,$order_year, $isQuater)
    {
       PagedDataSet::PagedDataSet('db');
       PagedDataSet::setPageSize(3000);
        
       if ($Document)
       {
            $Document->addScript('resources/js/javascript.orderlist.js');
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
				
				if (!is_numeric($this->estate_id)) 
						$this->estate_id = null;
				$estate_id = (is_null($this->estate_id)?"o.estate_id": $this->estate_id);
				
				$order_type =($this->orderType == "d")?"DESC":"ASC";
				
				$amount= & F60DbUtil::getAmountOwned();
            $sqlCode = "SELECT o.order_id, o.lkup_order_status_id, o.delivery_date as order_date, invoice_number, estate_name, l.caption as payment_status,
			sum(oi.price_per_unit * oi.ordered_quantity)+sum(oi.ordered_quantity*oi.litter_deposit)+sum(oi.price_per_unit * oi.ordered_quantity)*0.05 total_prices,
						sum(oi.price_winery * oi.ordered_quantity)+sum(oi.ordered_quantity*oi.litter_deposit)+sum(oi.price_winery * oi.ordered_quantity)*0.05 total_winery,
                            os.caption as order_status,
							$amount   as total_amount , o.lkup_store_type_id 
							FROM orders o
                            inner join lkup_order_payment_status l on 
                            o.lkup_payment_status_id= l.lkup_payment_status_id
                            inner join lkup_order_statuses os on 
                            o.lkup_order_status_id= os.lkup_order_status_id
                            inner join order_items oi on o.order_id = oi.order_id
                            where o.deleted = 0 and o.customer_id=$this->customerID 
									 and o.estate_id=$estate_id
                            and year(o.delivery_date)=$this->order_year
									 and $byPeriod
                            group by o.order_id
                            order by $this->orderBy $order_type";
                        
        
       /* $this->sqlCode = sprintf($sqlTemplate, $this->customerID, 
                        (is_null($this->estate_id)?"o.estate_id": $this->estate_id),
                        $this->order_year, $this->orderBy,
                        ($this->orderType == "d")?"DESC":"ASC");*/
        PagedDataSet::setCurrentPage($this->page);
        PagedDataSet::load($sqlCode);
                        
    }
    
    function _buildContent() 
    {
        $this->_loadDataset();
        
        
        
        $aRow = 0;	
		  $total_sales =0;
		  $isPaid = 0;
        while ($lineData = PagedDataSet::fetch()) 
        {
            $aRow++;
            $this->Template->createBlock('loop_line');
            $this->Template->assign("row_style", ($aRow % 2)?"gridrowOdd":"gridrowEven");
            $this->Template->assign("order_date", date("m/d/Y",strtotime($lineData["order_date"])));
            
            $New_Rule_date = "2015-04-01";
         
		     $isNewRule =0;
			 if(strtotime($lineData["order_date"])>=strtotime($New_Rule_date))
			{
				$isNewRule =1;
			}
	
		
		    $this->Template->assign("invoice_number", $lineData["invoice_number"]);
            $this->Template->assign("estate_name", $lineData["estate_name"]);
            $this->Template->assign("payment_status", $lineData["payment_status"]);
            $this->Template->assign("order_status", $lineData["order_status"]);
            
     
	       if( $isNewRule ==1)
	        {	     
				
				$total_amount=0;
				if($lineData["lkup_store_type_id"]==3)
				{
					$this->Template->assign("total_amount",  Number::fromDecimalToCurrency($lineData["total_prices"],"$", ".", ",", 2, "left"));
					$total_sales =$total_sales+$lineData["total_prices"];
				}	
				else
				{
					$this->Template->assign("total_amount",  Number::fromDecimalToCurrency($lineData["total_winery"],"$", ".", ",", 2, "left"));
						$total_sales =$total_sales+$lineData["total_winery"];
				}
			}
            else
            {
            	$total_sales =$total_sales+$lineData["total_amount"];
            	$this->Template->assign("total_amount",  Number::fromDecimalToCurrency($lineData["total_amount"],"$", ".", ",", 2, "left"));
            }
                  	
            $this->Template->assign("order_id", $lineData["order_id"]);
            $this->Template->assign("lkup_order_status_id", $lineData["lkup_order_status_id"]);
            
			if($lineData["payment_status"]=="Not paid" && $lineData["order_status"]=="Delivered")
			{
			 	$isPaid =1;						
			}
        }
        
        $this->Template->globalAssign("total",  PagedDataSet::getTotalRecordCount());
        $this->Template->globalAssign("total_sales",  Number::fromDecimalToCurrency($total_sales,"$", ".", ",", 2, "left"));

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