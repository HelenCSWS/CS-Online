<?php

import('Form60.base.F60DocBase');
import('Form60.base.F60DALBase');
import('Form60.base.F60DbUtil');

import('Form60.util.F60Date');
import('Form60.bll.bllorders');
import('php2go.data.PagedDataSet');
import('php2go.util.Number');

define ('WRONG_ESTATE','The inovice you are opening is not belong to your current estate.');

class Form60View extends F60DocBase 
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
    
    var $isNewRule=0;
    
    var $store_type_id =1;
    
    function Form60View() 
    {	    	
        $this->pageTitle = "Form 60";    
        $this->order_id = isset($_GET["id"])? $_GET["id"] : null;
        
        $this->isDisplay=$this->isSameLoginEstate();
        
        if($this->isDisplay)
        {         
	        F60DocBase::F60DocBase($this->pageTitle, "Form60View.tpl", "");
	    }
	    else
	    {
		    F60DocBase::F60DocBase($this->pageTitle, "message.tpl", "");
	        $this->elements["message"] = WRONG_ESTATE;
	    }
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
        $when_entered =F60Date::sql2USDate($order->get_data("when_entered"), False);
         $when_entered_tax =F60Date::sql2PHPDate($order->get_data("when_entered"), False);
        $this->elements["when_entered"] = $when_entered;
        $this->elements["delivery_date"] = F60Date::sql2USDate($order->get_data("delivery_date"), False);
        //$this->elements["delivery_date"] = F60Date::sql2USDate($order->get_data("when_created"), False);
        $this->elements["licensee_number"] = $order->get_data("licensee_number");
        
        $estate_name = $order->get_data("estate_name");
        
        $this->store_type_id = $order->get_data("lkup_store_type_id");
       /* if(strpos($estate_name,"1775")==false)
        {
			
		}	
		else
			$estate_name = "Bench 1775";
		*/
		//if(strlen(strstr($estate_name,"Bench"))>0)
		
        
        $this->elements["estate_name"] = $estate_name;
       
         $estate_id = $order->get_data("estate_id");
        $payment_info=F60DbUtil::getEstatePayMentInfo($estate_id);
      //  $contact_number=F60DbUtil::getEstateContactNumber($estate_id);
        
        
        $this->elements["payment_info"] = $payment_info;
      //  $this->elements["estate_number"] = $contact_number;
        $this->elements["store_no"] = $order->get_data("estate_number");
        $this->elements["customer_name"] = $order->get_data("customer_name");
        $this->elements["customer_address"] = $order->get_data("customer_address");

        $this->elements["delivery_instruction"] = $order->delivery_info;
        
      
         
             //   print 
        //get the best number to phone
        $customer = & new bllcustomer();
        $customerPhone = "";
        $phoneType = "";
        if ($customer->loadByPrimaryKey($order->get_data("customer_id")))
        {
            switch ($customer->get_data("lkup_phone_type_id"))
            {
                case 1:
                        $customerPhone = $customer->get_data("phone_office1") ;
                        $phoneType = " (Business)";
                        break;
                case 2:
                        $customerPhone = $customer->get_data("phone_other1");
                        $phoneType = " (Cell)";
                        break;
                case 3:
                        $customerPhone = $customer->get_data("phone_fax");
                        $phoneType = " (Fax)";
                        break;
                    
            }
            $this->elements["customer_phone"] = $customer->format_phone($customerPhone) . $phoneType;
              
        }
   
   		$ownername = & F60DbUtil::getAccountUserNameByCustomer($order->get_data("customer_id"));
   		
   	
   		if($ownername=="0")
   		{
			echo "<b><p style=\"font-size:15pt; color:red\">This account doesn't have a wine consultant, you must assgin a consultant to this customer.</p>";
		}
        
        $this->elements["created_by_user_name"] = $ownername;
        if ($order->get_data("adjustment_1")>0)
            $this->elements["adjustment_1"] = Number::fromDecimalToCurrency($order->get_data("adjustment_1"),"$", ".", ",", 2, "left");
        if ($order->get_data("adjustment_2")>0)
            $this->elements["adjustment_2"] = Number::fromDecimalToCurrency($order->get_data("adjustment_2"),"$", ".", ",", 2, "left");
        

        
        $sqlCode = "SELECT license_name FROM lkup_store_types where lkup_store_type_id<8 order by license_name";
        $ds = & new PagedDataSet('db');
        $ds->setPageSize(500);
        $ds->setCurrentPage(1);
        $ds->load($sqlCode);
        while ($lineData = $ds->fetch()) 
        {
            $tpl = & $this->Template;
            $tpl->createBlock('loop_licensee');
            $tpl->assign("license_name", $lineData["license_name"]);
            if ($lineData["license_name"] == $order->get_data("license_name"))
                $tpl->assign("img_check", "check");
            else
                $tpl->assign("img_check", "uncheck");
        }
        
        $HST_date = "2010-07-01"; // From July 1st 2010, idot BC government will merge the PST with GST together as HST
        
        $GST_date = "2013-04-01"; //Back to GST
        
        $NewRule_date = "2015-04-01"; //GST =0.05 and new calculation
	
		
	//	$enter_date = strtotime($when_entered_tax);
	//	$HST_date = strtotime($HST_date);
		
		$tax_name ="GST";
		
		$delivery_date = $order->get_delivery_date();
		if(strtotime($when_entered_tax)>=strtotime($HST_date)&& strtotime($when_entered_tax)<strtotime($GST_date))
				$tax_name ="HST";
				
		if(strtotime($delivery_date)>=strtotime($NewRule_date))
		{
			$this->isNewRule=1;
		}
        
        $this->elements["tax_name_title"]=$tax_name;
        $this->elements["tax_name_included"]=$tax_name;
 


        $tpl->setCurrentBlock(TP_ROOTBLOCK);
        
        $this->_loadOrderItems(&$order);        
    }
    
    function _loadOrderItems(&$order)
    {
        $sqlTemplate = "(
                        SELECT oi.order_item_id, oi.cspc_code, oi.wine_id, oi.wine_name, oi.wine_vintage as vintage, bs.caption as size, 
                        oi.ordered_quantity, oi.price_per_unit, oi.price_winery, oi.ordered_quantity * oi.price_per_unit as item_value,
                        oi.ordered_quantity * oi.price_winery  as wholesale_item_value, oi.litter_deposit
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
        $ds->setPageSize(500);
        $ds->setCurrentPage(1);
        $ds->load($sqlCode);
                        
        $aRow = 0;	
	
	$total_value =0;
        while ($lineData = $ds->fetch()) 
        {
         	
         	if($this->isNewRule==1)
         	{
         	 	if($this->store_type_id ==3)
         	 	{
	         		$unit_price = $lineData["price_per_unit"];
	         		$item_value = $lineData["item_value"];
	         	}
	         	else
	         	{
					$unit_price = $lineData["price_winery"];
         			$item_value = $lineData["wholesale_item_value"];
				}
			}			
			elseif($this->isNewRule==0)
			{
				$unit_price = $lineData["price_per_unit"];
				$item_value = $lineData["item_value"];
			
			}
         		
            $aRow++;
            $tpl = & $this->Template;
            $tpl->createBlock('loop_line');
            $tpl->assign("rowcolor", ($aRow % 2)?"white":"silver");
            $tpl->assign("item_no", str_repeat('0', (2 - strlen($aRow . ""))) . $aRow);
            $tpl->assign("cspc_code", $lineData["cspc_code"]);
            $tpl->assign("wine_name", $lineData["wine_name"] . " - " . $lineData["vintage"]);
            $tpl->assign("size", $lineData["size"]);
            $tpl->assign("ordered_quantity", $lineData["ordered_quantity"]);
            $tpl->assign("price_per_unit", Number::fromDecimalToCurrency($unit_price,"$", ".", ",", 2, "left"));
            $tpl->assign("item_value", Number::fromDecimalToCurrency($item_value,"$", ".", ",", 2, "left"));
            
            $total_value  = $total_value +$item_value;
        }
        while ($aRow<13) 
        {
            $aRow++;
            $tpl = & $this->Template;
            $tpl->createBlock('loop_line');
            $tpl->assign("rowcolor", ($aRow % 2)?"white":"silver");
            $tpl->assign("item_no", str_repeat('0', (2 - strlen($aRow . ""))) . $aRow);
            $tpl->assign("cspc_code", "");
            $tpl->assign("wine_name", "");
            $tpl->assign("size", "");
            $tpl->assign("ordered_quantity", "");
            $tpl->assign("price_per_unit", "");
            $tpl->assign("item_value", "");
        }
        $tpl->setCurrentBlock(TP_ROOTBLOCK);
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
        
    }
    
}

?>