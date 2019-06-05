<?php

import('Form60.base.F60DocBase');
import('Form60.base.F60DALBase');
import('Form60.base.F60DbUtil');

import('Form60.util.F60Date');
import('Form60.bll.bllCsOrder');
import('php2go.data.PagedDataSet');
import('php2go.util.Number');



class CSOrderView extends F60DocBase 
{
    var $actions;
    var $templateName;
    var $pageTitle;
    var $contents;
    var $showPrint = false;
    var $showPDF = false;
    var $showTimer = true;
    var $order_id;
    var $login_user_id;
    
    var $isDisplay=true;
    
    var $paument_info="";
    var $contact_number="";
    

    
    var $store_type_id =1;
    
    var $province_id;
    
    var $estate_id ="";
    
    function CSOrderView() 
    {	    	
        $this->pageTitle = "Invoice";    
        $this->order_id = isset($_REQUEST["order_id"])? $_REQUEST["order_id"] : null;
        
         
        if($this->isDisplay)
        {         
	        F60DocBase::F60DocBase($this->pageTitle, "CSOrderView.tpl", "");
	    }
	    else
	    {
		    F60DocBase::F60DocBase($this->pageTitle, "message.tpl", "");
	        $this->elements["message"] = WRONG_ESTATE;
	    }
        
        $this->province_id = $_COOKIE["F60_PROVINCE_ID"];
    }
     

	
    function display() 
    {
     	if($this->isDisplay)
	       $this->_loadData();
	   	    	 
        F60DocBase::display(); 
    }
    
    function _loadData()
    {	  
    	$this->orderInfoBll = & new bllcsorder();
       	 
       	$order = $this->orderInfoBll->getProOrderInfo( $this->order_id,true);
	
	//	$orderItem = $this->orderInfoBll->getOrderItems( $this->order_id );
		
        $this->elements["delivery_date"] = $order["frm_delivery_date"];     
        
        $this->elements["invoice_number"] = $order["invoice_number"];
        
        $this->elements["customer_name"] = $order["customer_name"];
        $this->elements["customer_address"] = $order["customer_address"];
        $this->elements["customer_city"] = $order["customer_city"];
        $this->elements["province"] = $order["short_name"];
        $this->elements["postal_code"] = $order["postal_code"];
        
        $user_name  = $order["user_name"];
        
        if($user_name=="")
       		 $user_name  = $order["created_by_user_name"];
       		 
        $this->elements["user_name"] = $user_name;
        	
        $this->elements["payment_method"] = $order["payment_type"];
        
 
        $lineTotal =0;
        
     
        $orderItemInfo=$this->orderInfoBll->getOrderItems($this->order_id,true);
                        
        $aRow = 0;	
	
		$total_value =0;
        $lineTotal=0;
      
      
	
    }
    
    function _loadOrderItems($order)
    {
        
        
        $orderItemInfo=$this->orderInfoBll->getOrderItems($this->order_id,true);
                        
        $aRow = 0;	
	
		$total_value =0;
        $lineTotal=0;
        foreach ( $orderItemInfo as $lineData) 
        {
            
         	      
           $aRow++;
           
     
            $tpl = & $this->Template;
            $tpl->createBlock('loop_line');
            
            $qty = $lineData["ordered_quantity"];
            $tpl->assign("qty", $lineData["ordered_quantity"]);         
            $tpl->assign("product_name", $lineData["product_name"]);      
            
            if($aRow==1)
                $tpl->assign("order_number", $order["invoice_number"]);        
            
            $unit_price =$lineData["price_per_unit"];
            
            if($lineData["product_name"]=="Winelife" &&  $lineData["ordered_quantity"]>23)
                $unit_price =$lineData["promotion_price"];
              
            
             $tpl->assign("unit_price",Number::fromDecimalToCurrencyNoSpace($unit_price,"$", ".", ",", 2, "left"));
             
             $lineTotal = $unit_price*$qty;
             
             $tpl->assign("line_total",Number::fromDecimalToCurrencyNoSpace($lineTotal,"$", ".", ",", 2, "left"));
             
             
             $total_value = $total_value+$lineTotal;
                                                
        }
        
       while ($aRow<4) 
        {
            $aRow++;
           /* $tpl = & $this->Template;
            $tpl->createBlock('loop_line');*/
        
        }
        
                $isPST= $order["PST_included"];

        	$pst_total= $order["PST_factor"]*$lineTotal*$isPST;
		$gst_total= $order["GST_factor"]*$lineTotal;
			 
		$this->elements["pst_total"] =Number::fromDecimalToCurrencyNoSpace($pst_total,"$", ".", ",", 2, "left");
		$this->elements["gst_total"] =Number::fromDecimalToCurrencyNoSpace($gst_total,"$", ".", ",", 2, "left");
		
		$sub_total = $lineTotal+ $pst_total+ $gst_total;
		
		$this->elements["sub_total"] =Number::fromDecimalToCurrencyNoSpace($sub_total,"$", ".", ",", 2, "left");
		$delivery_cost = $order["adjustment_1"];
		$this->elements["adjustment_1"] =Number::fromDecimalToCurrencyNoSpace($order["adjustment_1"],"$", ".", ",", 2, "left");
		
		$total_amount = $sub_total +$delivery_cost ;
		
		$this->elements["total_amount"] =Number::fromDecimalToCurrencyNoSpace($total_amount,"$", ".", ",", 2, "left");
        
     /*   $tpl->setCurrentBlock(TP_ROOTBLOCK);
        $order_subtotal = $order->orderSubTotal;
        $licensee_factor = $order->licenseeFactor;
        $GST = $order->GST;
        
        if($this->isNewRule==1)
        {
          
			$GST = $total_value*0.05;
		}
		
        $total_value = $order->orderGrandTotal;  
        $litter_deposit_total = $order->litterDepositTotal;
        
        $this->elements["order_subtotal"] = Number::fromDecimalToCurrency($order_subtotal,"$", ".", ",", 2, "left");
        if ($order->get_data('license_name') == 'Licensee')
            $this->elements["licensee_factor"] =Number::fromDecimalToCurrency($licensee_factor,"$", ".", ",", 2, "left");
        else if ($order->get_data('license_name') == 'Agency' || $order->get_data('license_name') == 'L.R.S.')
            $this->elements["agency_lrs_factor"] = Number::fromDecimalToCurrency($licensee_factor,"$", ".", ",", 2, "left");
        
        $this->elements["GST"] = Number::fromDecimalToCurrency($GST,"$", ".", ",", 2, "left");

        $this->elements["litter_deposit_total"] = Number::fromDecimalToCurrency($litter_deposit_total,"$", ".", ",", 2, "left");
        $this->elements["total_value"] = Number::fromDecimalToCurrency($total_value,"$", ".", ",", 2, "left");
        */
        
    }
    
}

?>