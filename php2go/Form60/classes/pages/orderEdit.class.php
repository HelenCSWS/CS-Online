<?php

/**
 * Perform the necessary imports
 */

import('Form60.base.F60FormBase');
import('Form60.util.F60Date');
import('Form60.util.F60Common');
import('Form60.bll.bllorders');
import('php2go.data.PagedDataSet');
import('php2go.util.Number');

class orderEdit extends F60FormBase
{
    var $order_id ;
    var $estate_id; // add for paradis ranch, set default delivery on Wednsday
    var $estate_name;
	var $invoice_number; // August 2010, for Arrowleaf's invoice number
	var $isNewRule =0;


	var $store_type_id =1;
	
    function orderEdit()
    {
        $order = & new bllorder();

        if (isset($_GET['inv']))
        {
            $order->loadByInvoiceNo($_GET['inv']);
            $this->order_id = $order->get_data("order_id");
        }
        else 
        {
            $this->order_id = (isset($_POST['order_id'])?$_POST['order_id']:$this->getRecordID());
            $order->loadByPrimaryKey($this->order_id);
        }
//echo $order->get_data("estate_name");
        $title = "  Edit order";

        F60FormBase::F60FormBase('orderEdit', $title, 'orderedit.xml', 'orderEdit.tpl', 'btnOK');
        $this->addScript('resources/js/javascript.pageAction.js');
        $this->addScript('resources/js/javascript.orderlist.js');
        $this->attachBodyEvent('onLoad', 'setForm("orderEdit");');

        $form = & $this->getForm();
        $form->setFormAction($_SERVER["REQUEST_URI"]);
        
        //go back to customer edit page with order tab selected
        import('Form60.base.F60PageStack');
        F60PageStack::addPagetoStack("main.php?page_name=customerAdd&id=" . $order->get_data("customer_id"));
        F60PageStack::addPagetoStack($_SERVER["REQUEST_URI"]);
        setcookie("CustomerTab", "Order", time()+3600, "/");

        $this->registerActionhandler(array("btnOK", array($this, processForm), "LASTPAGE", NULL));
        $this->registerActionhandler(array("delete", array($this, deleteData), "LASTPAGE", NULL));       
         
        $this->form->setButtonStyle('btnOK');
        $this->form->setInputStyle('input');
        $this->form->setLabelStyle('label');
        
        $edtEstate=& $form->getField("estateName");
        $edtEstate->setValue($order->get_data("estate_name"));
        $this->estate_name = $order->get_data("estate_name");
        
		$New_Rule_date = "2015-04-01"; //new calculateion start from this date
        
         $delivery_date = $order->get_delivery_date();
  		if(strtotime($delivery_date)>=strtotime($New_Rule_date))
		{
			$this->isNewRule =1;
		}
        
    }

    function display()
    {
        if (!$this->handlePost())
            $this->displayForm();
    }

    function displayForm()
    {
       $form = & $this->getForm();

       $action = array(
           "View Form 60" => "javascript:saveOrderViewForm60(" . $this->order_id . ");",
           "Delete order" => "javascript:runDelete(9);"
        );
        $this->loadData(&$form, $this->order_id);

        $this->setActions($action);

        F60FormBase::display();
    }


    function loadData(&$form, $order_id)
    {

        $order = & new bllorder();
        
		$order->loadByPrimaryKey($order_id);
        

        if($this->estate_name=="Arrowleaf Cellars")
        {
         
         	$HST_date ="2010-07-01";
         
         	if($order->get_data("delivery_date")<$HST_date)
         	{
			  	$this->_loadOrderItemsForAL($form, &$order);
			  
			}
			else
			{
			 
				$this->_loadOrderItems($form, &$order);
			}
  		}
        else
			$this->_loadOrderItems($form, &$order);
        	
        $order->set_data("when_entered", F60Date::sql2USDate($order->get_data("when_entered"), false));
        $when_entered =$order->get_data("when_entered");
        
      


        $HST_time = "07/01/2010"; // From July 1st 2010, stupid BC government will merge the PST with GST as HST
        $GST_date = "2013-04-01"; //Back to GST stupid BC government will back to the PST and GST 
        
        
        $New_rule_date =  "2015-04-01"; 
        
         
        $tax_name ="GST";
      //  $tax_name = strtotime($when_entered)>=strtotime($HST_time)?"HST":"GST";
        
     	 $delivery_date = $order->get_delivery_date();
        

        
         if(strtotime($when_entered)>=strtotime($HST_time)&& strtotime($when_entered)<strtotime($GST_date))
				$tax_name ="HST";

		$isNewRule =0;
		
		
		if(strtotime($delivery_date)>=strtotime($New_rule_date))
		{
			$isNewRule =1;
		}
		
	
		
		if($isNewRule ==0)
		
		{		
	        $order->set_data("adjustment_1", Number::fromDecimalToCurrency($order->get_data("adjustment_1"),"$", ".", ",", 2, "left"));
	        $order->set_data("adjustment_2", Number::fromDecimalToCurrency($order->get_data("adjustment_2"),"$", ".", ",", 2, "left"));
	    }
	    
        $order->set_data("deposit", Number::fromDecimalToCurrency($order->get_data("deposit"),"$", ".", ",", 2, "left"));
        $order->loadDataToForm($form);
               
        $form->Template->globalAssign("tax_name", $tax_name);

		$edtEstateId=& $form->getField("estate_id");
        $edtEstateId->setValue($order->get_data("estate_id"));
        
        
        

        
		$store_type_factor=$order->get_data("agency_LRS_factor");
		if($store_type_factor==null)
		{
			$store_type_factor="0";
		}
        $edt_agency_LRS_factor=& $form->getField("agency_LRS_factor");
       
      
      /* 	if($isNewRule ==1)
       		$store_type_factor = 0;
       	*/
		   	
	    $edt_agency_LRS_factor->setValue($store_type_factor);
	    

        
  		if($this->estate_name=="Arrowleaf Cellars")
  		{ 		 
			$edtALIn=& $form->getField("AL_invoice_no");
			$this->invoice_number=$order->get_data("invoice_number");
		 	if($this->invoice_number>=10000000)
		 	{
				$edtALIn->setValue("");
			}
			else
			{
				$edtALIn->setValue($this->invoice_number);	
			}
		}
    }



	function isValidInput($order_id)
	{
		$retVal=true;
		$form = & $this->getForm();
		$edtEstate=& $form->getField("estateName");
		$estate=$edtEstate->getValue();
		
		if($estate=="Arrowleaf Cellars")
		{
			$edtALNo=& $form->getField("AL_invoice_no");
			$AL_No=$edtALNo->getValue();	
			
			$edtEstateId=& $form->getField("estate_id");
			$estate_id=$edtEstateId->getValue();
			
			$retVal = bllorders::isUniqInvoice($estate_id,$order_id,$AL_No);
		}
		
		if(!$retVal)
		{
			$form->addErrors("There is already an invoice with this number.");
           return FALSE;
		}
		
		return $retVal;
		
	}
    function processForm()
    {
     	$form = & $this->getForm();

        $order_id = $_POST['order_id'];

		if($this->isValidInput($order_id))
		{
	        $form = & $this->getForm();
	        $order = & new bllorder();
	        $order->loadByPrimaryKey($order_id);
	        $order->getDataFromForm($form);
	        $order->set_data("adjustment_1", F60Common::currency2decimal($order->get_data("adjustment_1")));
	        $order->set_data("adjustment_2", F60Common::currency2decimal($order->get_data("adjustment_2")));
	        $order->set_data("deposit", F60Common::currency2decimal($order->get_data("deposit")));
	      
	      
	      	$AL_No="";
	      	if($order->get_data("estate_name")=="Arrowleaf Cellars")
	      	{
	      		$edtALNo=& $form->getField("AL_invoice_no");
				$AL_No=$edtALNo->getValue();
	      	}
	      	
	        $order->save($AL_No);
	        $order->AddUpdateOrderItems($_POST["Order"]);
			
	        return true; // should be true here, set for false for debug
	    }
	    else
	    {
			return false;
		}
    }
    
    function deleteData()
    {
        $order_id = $_POST['order_id'];
        $form = & $this->getForm();
        $order = & new bllorder();
        $order->loadByPrimaryKey($order_id);
        $order->mark_deleted();
        $order->save();

        return true;
    }
    
     function _loadOrderItemsForAL($form, &$order)
    {
     	$estate = $order->get_data('estate_name');     	
     	
        $sqlTemplate = "
                        SELECT oi.order_item_id, oi.cspc_code, oi.wine_id, oi.wine_name, oi.wine_vintage as vintage, bs.caption as size,oi.ordered_quantity, oi.price_per_unit, 
                        oi.ordered_quantity * oi.price_per_unit as product_subtotal, oi.litter_deposit
                        FROM order_items oi
                        inner join wines w on oi.wine_id = w.wine_id
                        inner join lkup_bottle_sizes bs on w.lkup_bottle_size_id = bs.lkup_bottle_size_id
                        where oi.order_id = %s 
                        and oi.deleted = 0

						order by wine_name
						";
                        /*)
                        Union all
                        (
                        SELECT 0 as order_item_id, w.cspc_code, w.wine_id, w.wine_name, w.vintage as vintage, bs.caption as size, c.allocated, c.sold, 
                        c.allocated-c.sold as available, 0 as ordered_quantity, w.price_per_unit, 0 as product_subtotal,
                        bs.litter_deposit
                        FROM customer_wine_allocations c
                        inner join wines w  on c.wine_id = w.wine_id
                        inner join estates e on w.estate_id = e.estate_id
                        inner join lkup_bottle_sizes bs on w.lkup_bottle_size_id = bs.lkup_bottle_size_id
                        left outer join order_items oi on w.wine_id = oi.wine_id and oi.order_id = %s and oi.deleted = 0
                        left outer join orders o on oi.order_id = o.order_id
                        where w.deleted = 0
                        and (c.allocated-c.sold) > 0
                        and c.customer_id = %s
                        and e.estate_id = %s
                        and (oi.order_item_id is null)
                        )
                        order by wine_name";*/
                        
        $order_id = $order->get_data('order_id');
        $customer_id = $order->get_data('customer_id');
        $estate_id = $order->get_data('estate_id');
        
        
        
        $sqlCode = sprintf($sqlTemplate, $order_id, 
        $customer_id, $order_id, $customer_id, $estate_id);
        $ds = & new PagedDataSet('db');
        $ds->setPageSize(500);
        $ds->setCurrentPage(1);
        $ds->load($sqlCode);
                        
        $aRow = 0;	
        while ($lineData = $ds->fetch()) 
        {
            $aRow++;
            $form->Template->createBlock('loop_line');
            $form->Template->assign("row_style", ($aRow % 2)?"gridrowOdd":"gridrowEven");
            $form->Template->assign("item_no", str_repeat('0', (2 - strlen($aRow . ""))) . $aRow);
            $form->Template->assign("cspc_code", $lineData["cspc_code"]);
            $form->Template->assign("wine_name", $lineData["wine_name"] . " - " . $lineData["vintage"]);
            $form->Template->assign("size", $lineData["size"]);
            $form->Template->assign("allocated", "0");
            $form->Template->assign("sold", "0");
            $form->Template->assign("available","0");
            $form->Template->assign("wine_id", $lineData["wine_id"]);
            $form->Template->assign("ordered_quantity", $lineData["ordered_quantity"]);
            $form->Template->assign("price_per_unit", "$" . $lineData["price_per_unit"]);
            $form->Template->assign("litter_deposit", $lineData["litter_deposit"]);
            $form->Template->assign("product_subtotal", "$" . $lineData["product_subtotal"]);
        }
        
        $order_subtotal = $order->orderSubTotal;
      
        
        
		$licensee_factor = $order->licenseeFactor;
        	
        $GST = $order->GST;
        $total_value = $order->orderGrandTotal;  
        $litter_deposit_total = $order->litterDepositTotal;
        
        $form->Template->globalAssign("order_subtotal", Number::fromDecimalToCurrency($order_subtotal,"$", ".", ",", 2, "left"));
        
        if ($order->get_data('license_name') == 'Licensee')
            $form->Template->globalAssign("licensee_factor", Number::fromDecimalToCurrency($licensee_factor,"$", ".", ",", 2, "left"));
        else if ($order->get_data('license_name') == 'Agency' || $order->get_data('license_name') == 'L.R.S.')
            $form->Template->globalAssign("Agency_LRS_factor", Number::fromDecimalToCurrency($licensee_factor,"$", ".", ",", 2, "left"));
            
            
        $form->Template->globalAssign("GST", Number::fromDecimalToCurrency($GST,"$", ".", ",", 2, "left"));
        
        $form->Template->globalAssign("litter_deposit_total", Number::fromDecimalToCurrency($litter_deposit_total,"$", ".", ",", 2, "left"));
        $form->Template->globalAssign("total_value", Number::fromDecimalToCurrency($total_value,"$", ".", ",", 2, "left"));
    }
    
    function _loadOrderItems($form, &$order)
    {
     	$estate = $order->get_data('estate_name');     	
     	
        $sqlTemplate = "(
                        SELECT oi.order_item_id, oi.cspc_code, oi.wine_id, oi.wine_name, oi.wine_vintage as vintage, bs.caption as size, c.allocated, c.sold, 
                        c.allocated-c.sold as available, oi.ordered_quantity, oi.price_per_unit, oi.price_winery, 
                        oi.ordered_quantity * oi.price_per_unit as product_subtotal,oi.ordered_quantity * oi.price_winery as lic_product_subtotal, 
						oi.litter_deposit
                        FROM order_items oi
                        inner join customer_wine_allocations c on oi.wine_id = c.wine_id
                        inner join wines w on oi.wine_id = w.wine_id
                        inner join lkup_bottle_sizes bs on w.lkup_bottle_size_id = bs.lkup_bottle_size_id
                        where oi.order_id = %s 
                        and oi.deleted = 0
                        and c.customer_id = %s
                        )
                        Union all
                        (
                        SELECT 0 as order_item_id, w.cspc_code, w.wine_id, w.wine_name, w.vintage as vintage, bs.caption as size, c.allocated, c.sold, 
                        c.allocated-c.sold as available, 0 as ordered_quantity,  w.price_per_unit,w.price_winery, 0 as product_subtotal,0 as lic_product_subtotal,
                        bs.litter_deposit
                        FROM customer_wine_allocations c
                        inner join wines w  on c.wine_id = w.wine_id
                        inner join estates e on w.estate_id = e.estate_id
                        inner join lkup_bottle_sizes bs on w.lkup_bottle_size_id = bs.lkup_bottle_size_id
                        left outer join order_items oi on w.wine_id = oi.wine_id and oi.order_id = %s and oi.deleted = 0
                        left outer join orders o on oi.order_id = o.order_id
                        where w.deleted = 0
                        and (c.allocated-c.sold) > 0
                        and c.customer_id = %s
                        and e.estate_id = %s
                        and (oi.order_item_id is null)
                        )
                        order by wine_name";
                        
        $order_id = $order->get_data('order_id');
        $customer_id = $order->get_data('customer_id');
        $estate_id = $order->get_data('estate_id');
        
        
        
        $sqlCode = sprintf($sqlTemplate, $order_id, 
                        $customer_id, $order_id, $customer_id, $estate_id);
        $ds = & new PagedDataSet('db');
        $ds->setPageSize(500);
        $ds->setCurrentPage(1);
        $ds->load($sqlCode);

       
        $aRow = 0;	
        
 
		$this->store_type_id=$order->get_data("lkup_store_type_id");


        while ($lineData = $ds->fetch()) 
        {
        	if( $this->isNewRule==1 )
			{
			   if($this->isNewRule==1&&$this->store_type_id==3)
			        {
			         		$sub_total = $lineData["product_subtotal"];  // retail price for Licensee
			        	$price = $lineData["price_per_unit"];
			         	 
			         	
					}
					else
					{
							$sub_total = $lineData["lic_product_subtotal"]; // whole sale price for LRS AGency
			         		 $price = $lineData["price_winery"];	
			        }
			    
			}
			else
			{
					$sub_total = $lineData["product_subtotal"];  // retail price for Licensee
		        	$price = $lineData["price_per_unit"];
			}

            $aRow++;
            $form->Template->createBlock('loop_line');
            $form->Template->assign("row_style", ($aRow % 2)?"gridrowOdd":"gridrowEven");
            $form->Template->assign("item_no", str_repeat('0', (2 - strlen($aRow . ""))) . $aRow);
            $form->Template->assign("cspc_code", $lineData["cspc_code"]);
            $form->Template->assign("wine_name", $lineData["wine_name"] . " - " . $lineData["vintage"]);
            $form->Template->assign("size", $lineData["size"]);
            $form->Template->assign("allocated", $lineData["allocated"]);
            $form->Template->assign("sold", $lineData["sold"]);
            $form->Template->assign("available", $lineData["available"]);
            $form->Template->assign("wine_id", $lineData["wine_id"]);
            $form->Template->assign("ordered_quantity", $lineData["ordered_quantity"]);

		 if( $this->isNewRule==1 )
		 {
			if($this->isNewRule==1&&$this->store_type_id==3)	            
	        	$form->Template->assign("price_per_unit", "$" . $lineData["price_per_unit"]);	            
	        else
	        	$form->Template->assign("price_per_unit", "$" . $lineData["price_winery"]);
		}
		else
			$form->Template->assign("price_per_unit", "$" . $lineData["price_per_unit"]);	
	        	
            $form->Template->assign("litter_deposit", $lineData["litter_deposit"]);
            $form->Template->assign("product_subtotal", "$" . $sub_total);
        }
        
        $GST_rate =0.05;
        
        $order_subtotal = $order->orderSubTotal;
        $licensee_factor = $order->licenseeFactor;
        
        
      
        $GST = $order->GST;
        $total_value = $order->orderGrandTotal;  
        $litter_deposit_total = $order->litterDepositTotal;
        
        $form->Template->globalAssign("order_subtotal", Number::fromDecimalToCurrency($order_subtotal,"$", ".", ",", 2, "left"));
        if ($order->get_data('license_name') == 'Licensee')
            $form->Template->globalAssign("licensee_factor", Number::fromDecimalToCurrency($licensee_factor,"$", ".", ",", 2, "left"));
        else if ($order->get_data('license_name') == 'Agency' || $order->get_data('license_name') == 'L.R.S.')
            $form->Template->globalAssign("Agency_LRS_factor", Number::fromDecimalToCurrency($licensee_factor,"$", ".", ",", 2, "left"));
        $form->Template->globalAssign("GST", Number::fromDecimalToCurrency($GST,"$", ".", ",", 2, "left"));
        $form->Template->globalAssign("litter_deposit_total", Number::fromDecimalToCurrency($litter_deposit_total,"$", ".", ",", 2, "left"));
        $form->Template->globalAssign("total_value", Number::fromDecimalToCurrency($total_value,"$", ".", ",", 2, "left"));
    }
    

    
}

?>
