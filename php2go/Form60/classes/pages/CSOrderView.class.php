<?php

import('Form60.base.F60DocBase');
import('Form60.base.F60DALBase');
import('Form60.base.F60DbUtil');
import('Form60.util.F60Common');

import('Form60.util.F60Date');
import('Form60.bll.bllorders');
import('Form60.bll.bllcsorder');
import('php2go.data.PagedDataSet');
import('php2go.util.Number');

define('WRONG_ESTATE',
    'The inovice you are opening is not belong to your current estate.');

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

    var $isDisplay = true;

    var $paument_info = "";
    var $contact_number = "";

    var $isNewRule = 0;

    var $store_type_id = 1;
    var $customer_id="";
    var $estate_id="";
    
    var $other_info="";
    

    function CSOrderView()
    {
        $this->pageTitle = "Invoice";
        $this->order_id = isset($_REQUEST["order_id"]) ? $_REQUEST["order_id"] : null;

      $this->province_id = $_COOKIE["F60_PROVINCE_ID"];
      
      
 
        if ($this->isDisplay)
        {
            if( $this->province_id==6)
                F60DocBase::F60DocBase($this->pageTitle, "CSOrderView-fr.tpl", "");
            else
                F60DocBase::F60DocBase($this->pageTitle, "CSOrderView.tpl", "");
        } else
        {
            F60DocBase::F60DocBase($this->pageTitle, "message.tpl", "");
            $this->elements["message"] = WRONG_ESTATE;
        }

     }

    function display()
    {
        if ($this->isDisplay)
            $this->_loadData();

        F60DocBase::display();
    }

    function _loadData()
    {
        $this->orderInfoBll = &new bllcsorder();

        $order = $this->orderInfoBll->getProOrderInfo($this->order_id, true);

        //	$orderItem = $this->orderInfoBll->getOrderItems( $this->order_id );

        $this->elements["delivery_date"] = $order["frm_delivery_date"];

        $this->elements["invoice_number"] = $order["invoice_number"];

        $this->elements["customer_name"] = $order["customer_name"];
        $this->elements["customer_address"] = $order["customer_address"];
        $this->elements["customer_city"] = $order["customer_city"];
        $this->elements["province"] = $order["short_name"];
        $this->elements["postal_code"] = $order["postal_code"];
         $this->elements["other_info"] = $order["other_info"];
         
         if($order["other_info"]=="")
            $this->elements["is_display_other_info"] = "none";
         else
               $this->elements["is_display_other_info"] = "block";
            
       $this->customer_id = $order["customer_id"];
        $this->estate_id = $order["estate_id"];
        
        $is_other_delivery = $order["is_other_delivery"];

        $user_name = $order["user_name"];

        if ($user_name == "")
            $user_name = $order["created_by_user_name"];

        $this->elements["user_name"] = $user_name;

        $this->elements["payment_method"] = $order["payment_type"];
        
        if($is_other_delivery==1)
            $this->elements["is_other_delivery"] = "Other delviery service";
        else
            $this->elements["is_other_delivery"] = "Christopher Stewart";

        $lineTotal = 0;

        $orderItemInfo = $this->orderInfoBll->getOrderItems($this->order_id);

        $aRow = 0;

        $total_value = 0;
        $lineTotal = 0;

        $this->_loadOrderItems($order);

    }


    function _loadOrderItems($order)
    {
        $aRow = 0;

        $sub_total = 0;

        $orderInfoBll = new bllCsOrder();
        $orderItemInfo = $orderInfoBll->getOrderItems($this->order_id);
        
        $orderProName ="";

        foreach ($orderItemInfo as $lineData)
        {
            $aRow++;

            $tpl = &$this->Template;
            $tpl->createBlock('loop_line');
            $qty = $lineData["ordered_quantity"];
            $tpl->assign("qty", $lineData["ordered_quantity"]);
            //     $tpl->assign("qty", $lineData["ordered_quantity"]);
            
            $product_name = $lineData["product_name"];
            
            if($orderProName=="")
                $orderProName = $product_name;
                
            $product_code = $lineData["product_code"];
            
            if($product_code!="")
                $product_name = $product_name." (".$product_code.")";
                
           
           if($this->estate_id=="196")//bittered sling
           {
                $product_name = $product_name." - ".$lineData["product_type"];
           }
                
            $tpl->assign("product_name", $product_name);

          //  if ($aRow == 1)
            //    $tpl->assign("order_number", $order["invoice_number"]);

            $unit_price = $lineData["price_per_unit"];

            if ($lineData["product_name"] == "Winelife" && $lineData["ordered_quantity"] >
                23)
                $unit_price = $lineData["promotion_price"];

            $unit_price =F60Common::formatLast2ZeroAsCurrency($unit_price);

            
            $tpl->assign("unit_price", "$".$unit_price);

            $lineTotal = $unit_price * $qty;

            $tpl->assign("line_total", Number::fromDecimalToCurrencyNoSpace($lineTotal, "$",
                ".", ",", 2, "left"));


            $sub_total = $sub_total + $lineTotal;
        }
        while ($aRow < 6)
        {
            $aRow++;
            $tpl = &$this->Template;
            $tpl->createBlock('loop_line');

        }
        $tpl->setCurrentBlock(TP_ROOTBLOCK);


        $this->elements["sub_total"] = Number::fromDecimalToCurrencyNoSpace($sub_total,
            "$", ".", ",", 2, "left");
            
        $discType = $order["discount_type"];

        $discountTotal = 0;
        if ($discType > 0 && intval($order["discount"] != 0))
        {
            //            echo intval($order["discount"]);
            $this->elements["isDisc"] = "table-row";
            $discount = $order["discount"];
            if ($discType == 1)
                $discountTotal = $sub_total - $discount;
            else
                $discountTotal = $sub_total - $sub_total * $discount / 100;

            $this->elements["disc_amount"] = Number::fromDecimalToCurrencyNoSpace($discountTotal,
                "$", ".", ",", 2, "left");

            $sub_total = $discountTotal;
        } else
        {
            $this->elements["isDisc"] = "none";
        }


        $isPST = $order["PST_included"];
        
        $pst_no =0;
        if($orderProName!="Le Verre de Vin")
        {
            $pst_no = $this->orderInfoBll->getPstExemptNo($this->customer_id);
        }
        
        if($pst_no==0)
        {
            $isPST =1;
              
        }
        else
        {
            $isPST =0; // pst exempt
          //  $this->setValue2Ctl("pst_no",$pst_no);
        }
        

        if($this->estate_id==196)
            $isPST =0;
            
        $pst_total = $order["PST_factor"] * $sub_total * $isPST;
        $gst_total = $order["GST_factor"] * $sub_total;

        $this->elements["pst_total"] = Number::fromDecimalToCurrencyNoSpace($pst_total,
            "$", ".", ",", 2, "left");
        $this->elements["gst_total"] = Number::fromDecimalToCurrencyNoSpace($gst_total,
            "$", ".", ",", 2, "left");


       // $sub_total = $sub_total + $pst_total + $gst_total;

        if ($pst_no == 0||$pst_no=="0"||$pst_no==null||$pst_no==“”)
        {
            
        }
        else
        {
            $this->elements["pst_exempt_no"] = "<B>PST #</B> " . $pst_no . "";
        }
      
        $delivery_cost = $order["adjustment_1"];
        $this->elements["adjustment_1"] = Number::fromDecimalToCurrencyNoSpace($order["adjustment_1"],
            "$", ".", ",", 2, "left");

        $total_amount = $sub_total + $delivery_cost+$pst_total + $gst_total;;

        $this->elements["total_amount"] = Number::fromDecimalToCurrencyNoSpace($total_amount,
            "$", ".", ",", 2, "left");
    }

}

?>