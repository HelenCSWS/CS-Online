<?php

/**
 * Perform the necessary imports
 */

import('Form60.base.F60FormBase');
import('Form60.util.F60Date');
import('Form60.util.F60Common');
import('Form60.bll.bllcsOrder');
import('php2go.data.PagedDataSet');
import('php2go.util.Number');

// discount type: 0: no; 1: amount; 2:rate

class csOrderEdit extends F60FormBase
{
    var $order_id ;
    var $estate_id; // add for paradis ranch, set default delivery on Wednsday
    var $estate_name;
	var $invoice_number; // August 2010, for Arrowleaf's invoice number
	var $isNewRule =0;


	var $store_type_id =1;
	var $orderInfoBll=null;
	
	var $customer_id;
	
	var $orderInfo;
	
	var $oldQty=0;
    
    var $province_id;
	
    function csOrderEdit()
    {		
	
	
        $title = "  Edit order";
      
 		$this->order_id = (isset($_REQUEST['order_id'])?$_REQUEST['order_id']:$this->getRecordID());
		
		$this->orderInfoBll = & new bllcsorder();
		
		$this->orderInfo = $this->orderInfoBll->getProOrderInfo($this->order_id,false);	
		
        $this->province_id = $_COOKIE["F60_PROVINCE_ID"];
        
     //  
		$customer_id= $this->orderInfo["customer_id"];
        
        
        
        F60FormBase::F60FormBase('csOrderEdit', $title, 'csorderEdit.xml', 'csorderedit.tpl', 'btnOK');
      
        $this->addScript('resources/js/javascript.pageAction.js');
        $this->addScript('resources/js/javascript.csproduct.js');
 		 
 
       
        //go back to customer edit page with order tab selected
        import('Form60.base.F60PageStack');
        F60PageStack::addPagetoStack("main.php?page_name=customerAdd&id=" . $customer_id);
        F60PageStack::addPagetoStack($_SERVER["REQUEST_URI"]);
        setcookie("CustomerTab", "Product", time()+3600, "/");
        
        
      
        $form = & $this->getForm();
        $form->setFormAction($_SERVER["REQUEST_URI"]);
        
        $this->registerActionhandler(array("btnOK", array($this, processForm), "LASTPAGE", NULL));
        $this->registerActionhandler(array("delete", array($this, deleteData), "LASTPAGE", NULL));       
         
        $this->form->setButtonStyle('btnOK');
        $this->form->setInputStyle('input');
        $this->form->setLabelStyle('label');   
             
		$this->attachBodyEvent('onLoad', 'initCSOrder("");');
		
		
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
		   "View invoice" => "javascript:saveOrderViewCSInvoice(" . $this->order_id . ");",
		   "Delete order" => "javascript:runDelete(10);",
		);
        
        $this->_loadOrderData( $this->orderInfo);
		
		$this->setActions($action);
		
		F60FormBase::display();
    }

	function _loadOrderData( $orderInfo)
    {
        $this->estate_id = $orderInfo["estate_id"];
		
	
   		$this->setValue2Ctl("estate_id",$this->estate_id);
        
         $this->setValue2Ctl("province_id",$this->province_id);
       
		$this->customer_id = $orderInfo["customer_id"];
  		$this->setValue2Ctl("customer_id",$this->customer_id);


		// first section
		$cm_name = $orderInfo["customer_name"];
		$customer_address=$orderInfo["customer_whole_address"];
		$license_name=$orderInfo["license_name"];
		$licensee_number=$orderInfo["licensee_number"];
	
		$this->setValue2Ctl("customer_name",$cm_name);
		$this->setValue2Ctl("customer_address",$customer_address);
		$this->setValue2Ctl("license_name",$license_name);
		$this->setValue2Ctl("licensee_number",$licensee_number);
		
		
		//2nd section  
	
		$payment_status=$orderInfo["lkup_payment_status_id"];
		$payment_type_id=$orderInfo["lkup_payment_type_id"];
		$order_status=$orderInfo["lkup_delivery_status_id"];		
		
		$this->setValue2Ctl("product_name",$orderInfo["estate_name"]); 
		$this->setValue2Ctl("lkup_payment_status_id",$payment_status); 
		$this->setValue2Ctl("lkup_payment_type_id",$payment_type_id); +
		$this->setValue2Ctl("lkup_order_status_id",$order_status); 
		
		
		//3rd section
		$invoice_number=$orderInfo["invoice_number"];		
		$create_date=$orderInfo["crt_on"];	
		$create_by=$orderInfo["created_user"];	
		$delivery_date=F60Date::get2goDate($orderInfo["delivery_date"]);	
		
		$this->setValue2Ctl("invoice_number",$invoice_number); 
		$this->setValue2Ctl("when_entered",$create_date); 
		$this->setValue2Ctl("delivery_date",$delivery_date); 
		$this->setValue2Ctl("created_by_user_name",$create_by); 
        
        
        
		

        $this->_loadOrderItems($orderInfo);
        		
     }
     
    
     
    function _loadOrderItems ($orderInfo)
    {
      	
  		$form = & $this->getForm();
                
  		$orderItemInfo=$this->orderInfoBll->getOrderItems($this->order_id,$this->estate_id,$this->province_id);
        $aRow = 0;
        
 //       print_r($orderItemInfo);	
    
        $subTotal =0;
       
        foreach ( $orderItemInfo as $lineData) 
        {
        
            $product_name = $lineData["product_name"];
            $product_code = $lineData["product_code"];
            $aRow++;
            $form->Template->createBlock('loop_line');
            $form->Template->assign("row_style", ($aRow % 2)?"gridrowOdd":"gridrowEven");
            
            $cs_product_id =$lineData["cs_product_id"];
            
            $isOrderItem = false;
            $orderQty = 0;
            $price_per_unit=$lineData["price_per_unit"];
            
            $promotion_price=$lineData["promotion_price"];
        
          
            $orderQty = intval($lineData["ordered_quantity"]);
            
          
            if($orderQty!=0)
            {
                $product_name = $lineData["order_product_name"];
                $price_per_unit=$lineData["order_price"];
                $promotion_price=$lineData["order_special_price"];
                $product_code = $lineData["order_product_code"];
            }
            
            if($product_code!="")
            {
                $product_name = $product_name." (".$product_code.")";
            }
            $form->Template->assign("product_name", $product_name );
           
            $total_sold = 0;
       
            $saleInfo = $this->orderInfoBll->getTotalSoldInfo($orderInfo["customer_id"],$cs_product_id);
            
            $total_sold  =$saleInfo["sold"];
            $total_cs  =intval($saleInfo["sold_cs"]);
            
          
            $form->Template->assign("sold_btls", $total_sold);
            $form->Template->assign("sold_cs", $total_cs);
            
            $form->Template->assign("qty_btls",$orderQty );
       
            $price_per_unit =F60Common::formatLast2ZeroAsCurrency($price_per_unit);

            $form->Template->assign("price_per_unit", "$".$price_per_unit);
            
            $promotion_price =F60Common::formatLast2ZeroAsCurrency($promotion_price);
            $form->Template->assign("promotion_price", "$".$promotion_price);
            $form->Template->assign("cs_product_id", $cs_product_id);
            $totalInventory =$this->orderInfoBll->getCsInventory($cs_product_id,$this->province_id);
            
            $form->Template->assign("total_units", $totalInventory);
            $form->Template->assign("item_no", $aRow);
            
            $itemSubVal= $price_per_unit*$orderQty;
            
           // alert()
            if($product_name=="Winelife" && $orderQty>=24)
            {
                $itemSubVal= $promotion_price*$orderQty;
            }
            
            $form->Template->assign("cs_product_subtotal", "$".$itemSubVal);
            
            $subTotal = $subTotal+$itemSubVal;
         }                                          
               
        //discount
        $discountTotal =0;
        $discType = $orderInfo["discount_type"];
             
        
        $this->setValue2Ctl("discType",$discType);
        if($discType>0)
        {
            $discCtl = "disc_".$discType;
            
            $discVal = $orderInfo["discount"];
            
            $discountTotal = floatval($subTotal)-$discVal;
            
            $this->setValue2Ctl($discCtl,$orderInfo["discount"]);
            
            if($discType==2)
                $discountTotal =$subTotal -$subTotal*$orderInfo["discount"]/100;
        }
        
       
        if($discountTotal!=0)
            $form->Template->globalAssign("discount_amount",Number::fromDecimalToCurrencyNoSpace($discountTotal,"$", ".", ",", 2, "left"));
        
        
        //Tax 
        $form->Template->globalAssign("pst_rate",$orderInfo["PST_factor"]);
        $form->Template->globalAssign("gst_rate",$orderInfo["GST_factor"]);
        
        $pst_no = 0;
        
        if($product_name=="Le Verre de Vin")// not only winelife apply to all product
        {
            $pst_no=0;
        }
        else
            $pst_no = $this->orderInfoBll->getPstExemptNo($this->customer_id);
            
        
        
        if($pst_no==0)
        {
            $isPST =1;
              
        }
        else
        {
            $isPST =0; // pst exempt
            $this->setValue2Ctl("pst_no",$pst_no);
        }
        

        $this->setValue2Ctl("isPST",$isPST);
        
        if($this->estate_id==196)
            $isPST =0;
            
        $pst_total= $orderInfo["PST_factor"]*$subTotal*$isPST;
        
        $gst_total= $orderInfo["GST_factor"]*$subTotal;
        
        $is_other_delivery = $orderInfo["is_other_delivery"];
        
        $this->setValue2Ctl("is_other_delivery",$is_other_delivery);
		 
         
		$form->Template->globalAssign("order_subtotal", Number::fromDecimalToCurrencyNoSpace($subTotal,"$", ".", ",", 2, "left"));
		$form->Template->globalAssign("pst_total", Number::fromDecimalToCurrencyNoSpace($pst_total,"$", ".", ",", 2, "left"));
		$form->Template->globalAssign("gst_total", Number::fromDecimalToCurrencyNoSpace($gst_total,"$", ".", ",", 2, "left"));
			
        //delivery cost/adjustment_1
        $this->setValue2Ctl("adjustment_1",$orderInfo["adjustment_1"]);
        //other cost
		$totalAmount = $subTotal+$pst_total+$gst_total+$orderInfo["adjustment_1"];
		
		$form->Template->globalAssign("total_value", Number::fromDecimalToCurrencyNoSpace($totalAmount,"$", ".", ",", 2, "left"));
        
        //other infomation, like po number
        $this->setValue2Ctl("other_info",$orderInfo["other_info"]);
     
    }
    
    function getPstExemptNo()
    {
        
    }
    function processForm()
    {
              
     	$form = & $this->getForm();
 		
   		$paymentStatusID = $this->getCtlValue("lkup_payment_status_id");
   		$paymentTypeID = $this->getCtlValue("lkup_payment_type_id");
   		$orderStatusID = $this->getCtlValue("lkup_order_status_id");
   		
   		$deliveryDate = $this->getCtlValue("delivery_date");
   		$adjustment1 = $this->getCtlValue("adjustment_1");
        
   		$isPST= $this->getCtlValue("isPST");
        $other_info= $this->getCtlValue("other_info");
        
        //PST
        $PST_No=$this->getCtlValue("pst_no");
        if($isPST==0)
        {
            if($PST_No=="")
            {
               $this->form->addErrors("Please input the PST No. Or uncheck the PST checkbox.");
               return false;
            }
        }
        
        //Discount
        $discType = $this->getCtlValue("discType");
        
        $discVal=0;
        if($discType>0)
        {
          //  $discCtl ="disc_".$discType;
            if($discType==1)
            {
                $discVal = $this->getCtlValue("disc_1");
                
                if($discVal=="")
                {
                    $this->form->addErrors("Please input the discount amount. Or uncheck the discount checkbox.");
                    return false;
                }
             }
            
            if($discType==2)
            {
                $discVal = $this->getCtlValue("disc_2");
                if($discVal=="")
                {
                    $this->form->addErrors("Please input the discount rate. Or uncheck the discount checkbox.");
                    return false;
                }
             }
        }
     
     
        $is_other_delivery = $this->getCtlValue("is_other_delivery");
        
		$this->orderInfoBll->upDateCSOrderITems($this->order_id,$_POST["CSOrder"],$_POST["CSOrder_old"],$this->province_id);
        
		$this->orderInfoBll->updateCSOrderTable($this->order_id,$paymentStatusID,$paymentTypeID,$orderStatusID,$deliveryDate,$adjustment1,$PST_No, $discType,$discVal,$is_other_delivery,$other_info);
		
		return true;
		
      }
    
    function deleteData()
    {
		$order_id = $_POST['order_id'];
        
		
		$this->orderInfoBll->deleteCSOrderByID($order_id,$this->province_id);		
		
		return true;
    }
    

    
    
	function setValue2Ctl($cntlName, $val)
	{
	  
	 	$ctl = & $this->form->getField($cntlName);
	 	
	 	return $ctl->setValue($val);
		
	}
	
	function getCtlValue($cntlName)
	{
	 	$ctl = & $this->form->getField($cntlName);

	 	return str_replace("$","",$ctl->getValue());
		
	}

    
}

?>
