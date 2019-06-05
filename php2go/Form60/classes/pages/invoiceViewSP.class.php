<?php

import('Form60.base.F60DocBase');
import('Form60.base.F60DALBase');

import('Form60.util.F60Date');
import('Form60.bll.bllorders');
import('php2go.data.PagedDataSet');
import('php2go.util.Number');

define ('WRONG_ESTATE','The inovice you are opening is not belong to your current estate.');

class invoiceViewSP extends F60DocBase 
{
    var $actions;
    var $templateName;
    var $pageTitle;
    var $contents;
    var $showPrint = false;
    var $showPDF = false;
    var $showTimer = true;
    var $order_id;
    var $user_id;
    
    var $isDisplay=true;
    
    var $isInnerView =false;  // opened by CSWS team
    
    function invoiceViewSP() 
    {	    	
        $this->pageTitle = "Form 60";    
        $this->order_id = isset($_GET["id"])? $_GET["id"] : null;
        
        if($_REQUEST["isInner"]==1)
        {
			$this->isInnerView =true;
		}
        
        
       
        
        if(!$this->isInnerView)
        {
         	$hide_payment=0;
	        $this->isDisplay=$this->isSameLoginEstate();
	               
		       // F60DocBase::F60DocBase($this->pageTitle, "invoiceViewSP.tpl");
		        
		        
		    if($this->isSameLoginEstate())
	        {         
		        F60DocBase::F60DocBase($this->pageTitle, "invoiceViewSP.tpl");
		    }
		    else
		    {
			    F60DocBase::F60DocBase($this->pageTitle, "message.tpl", "");
		        $this->elements["message"] = WRONG_ESTATE;
		    }
		    
		    $this->user_id=$_GET["user_id"];
		}
		else
		{
		 	$hide_payment=1;
			F60DocBase::F60DocBase($this->pageTitle, "invoiceViewSP.tpl");
		}
		
		$functionName = "initInvoiceView($hide_payment);";
		$this->attachBodyEvent('onLoad', $functionName);
		Document::addScript('resources/js/javascript.suppliersales.js');
 	}
     
	function isSameLoginEstate()      
	{
	 
	 	$order = & new bllorder();
        
	    $login_user_id = & F60DALBase::get_current_user_id();

        $loginUserEstateId = $order->getEstateIdByLoginUser($login_user_id);
  		
 	    if($loginUserEstateId!=0)
        {
        	$orderEstateId = $order->getEstateIdByOrderId($this->order_id);
 	        	    
	        if($loginUserEstateId==97)$loginUserEstateId=96; // set LST and LVP as the same estae        
	
	        if(Intval($orderEstateId)==97)
	        {
				$orderEstateId=96; // set LST and LVP as the same estae
			}
	        
	        if($loginUserEstateId!=$orderEstateId)
	        {
	          return false;
			}			
		}

		return true;
	}
	
    function display() 
    {
     	if($this->isDisplay)
	        $this->_loadData();
	   
	    	 
        F60DocBase::display(); 
    }
    
    function _loadData()
    {	
        $order = & new bllorder();
        $order->loadByPrimaryKey($this->order_id);
        
        $this->elements["invoice_number"] = $order->get_data("invoice_number");
        // $this->elements["when_entered"] = F60Date::sql2USDate($order->get_data("when_entered"), False);
          
        $this->elements["delivery_date"]  = str_replace("/","-",F60Date::sql2USDate($order->get_data("delivery_date"), False)); // mm/dd/yyyy
               
        $this->elements["licensee_number"] = $order->get_data("licensee_number");
        $this->elements["license_name"] = $order->get_data("license_name");
        $this->elements["estate_name"] = $order->get_data("estate_name");
        $this->elements["store_no"] = $order->get_data("estate_number");
        $this->elements["store_no"] = $order->get_data("estate_number");
        $this->elements["customer_name"] = $order->get_data("customer_name");
        $this->elements["customer_address"] = $order->get_data("customer_address");
        
        $this->elements["customer_id"]=$order->get_data("customer_id");

//var link="main.php?page_name=invoiceViewSP&id="+order_id+"&estate_id"+estate_id+"&searchValue="+searchValue+"&isStart"+isStart+"&order_by"+order_by+"&order_type"+order_type+"&searchType="+searchType+"&currentpage="+currentpage;

        $this->elements["estate_id"] = $order->get_data("estate_id");
        $this->elements["user_id"] = $_GET["user_id"];
        $this->elements["inovice_number"] = $order->get_data("invoice_number");
        $this->elements["order_id"] = $this->order_id;
        $this->elements["search_value"] = $_GET["searchValue"];
        $this->elements["search_type"] = $_GET["searchType"];
        $this->elements["isStart"] = $_GET["isStart"];
        $this->elements["payment_type"] = 3;
        
        if($this->isInnerView)
        {
       		 $this->elements["isInnerView"] = 1;	
		}
		else
			 $this->elements["isInnerView"] = 0;	
        
        $this->_loadOrderItems(&$order);    
    }
    
    function _loadOrderItems(&$order)
    {
        $sqlTemplate = "(
	                        SELECT oi.order_item_id, oi.cspc_code, oi.wine_id, oi.wine_name, oi.wine_vintage as vintage, bs.caption as size, 
	                        oi.ordered_quantity, oi.price_per_unit, 
	                        oi.ordered_quantity * oi.price_per_unit as item_value, oi.litter_deposit
	                        FROM order_items oi
	                        inner join customer_wine_allocations c on oi.wine_id = c.wine_id
	                        inner join wines w on oi.wine_id = w.wine_id
	                        inner join lkup_bottle_sizes bs on w.lkup_bottle_size_id = bs.lkup_bottle_size_id
	                        where oi.order_id = %s 
	                        and oi.deleted = 0
	                        and c.customer_id = %s
                        )
                        order by wine_name";
                        
        $order_id = $order->get_data('order_id');
        $customer_id = $order->get_data('customer_id');
        
        $sqlCode = sprintf($sqlTemplate, $order_id, 
                        $customer_id, $order_id, $customer_id);
        $ds = & new PagedDataSet('db');
        $ds->setPageSize(20);
        $ds->setCurrentPage(1);
        $ds->load($sqlCode);
                        
        $aRow = 0;	
	 
        while ($lineData = $ds->fetch()) 
        {
            $aRow++;
            $tpl = & $this->Template;
            $tpl->createBlock('loop_line');
            $tpl->assign("row_style", ($aRow % 2)?"gridrowEven":"gridrowOdd");
            $tpl->assign("item_no", str_repeat('0', (2 - strlen($aRow . ""))) . $aRow);
            $tpl->assign("cspc_code", $lineData["cspc_code"]);
            $tpl->assign("wine_name", $lineData["wine_name"] . " - " . $lineData["vintage"]);
            $tpl->assign("size", $lineData["size"]);
            $tpl->assign("ordered_quantity", $lineData["ordered_quantity"]);
            $tpl->assign("price_per_unit", Number::fromDecimalToCurrency($lineData["price_per_unit"],"$", ".", ",", 2, "left"));
            $tpl->assign("item_value", Number::fromDecimalToCurrency($lineData["item_value"],"$", ".", ",", 2, "left"));
            
        }
        $tpl->setCurrentBlock(TP_ROOTBLOCK);
        $order_subtotal = $order->orderSubTotal;
    }
    
}

?>