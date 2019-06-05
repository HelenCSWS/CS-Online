<?php

import('Form60.dal.dalCSOrder');
import('php2go.util.TypeUtils');
import('Form60.base.F60DbUtil');

import('Form60.util.F60Date');
//import('Form60.base.F60DbUtil');
import('php2go.data.PagedDataSet');
import('Form60.base.F60DALBase');
import('Form60.util.F60Common');


class bllCsOrder
{
    var $deliverydate = null;
    var $estate_wines = null;
    var $wine_deliveries = null;


    function bllCsOrder()
    {
        // parent::dalCSOrder();
        $this->db = &Db::getInstance();
        $this->db->setFetchMode(ADODB_FETCH_ASSOC);
    }


    function getProOrderInfo($order_id, $isForInvoiceView)
    {
        $dalCSOrd = &new dalCSOrder();
        $result = $dalCSOrd->getOrderInfo($order_id, $isForInvoiceView);

        $row = &$result->FetchRow();

        return $row;

    }

    function getOrderForInvoiceView($order_id)
    {
        $dalCSOrd = &new dalCSOrder();
        $result = $dalCSOrd->getOrderForInvoiceView($order_id);

        return $result;

    }
    function getOrderItems($order_id, $estate_id = "", $province_id = "")
    {
        $dalCSOrd = &new dalCSOrder();


        if ($estate_id != "")
        {
            //$result = $dalCSOrd->getOrderItems($order_id);
       
            $result = $dalCSOrd->getOrderItems4Edit($order_id, $estate_id, $province_id);
         }else
            $result = $dalCSOrd->getOrderItems4OrderView($order_id);

        return $result;

    }
    //cs_producr_id=1 winelife; 2 Le vera devin
    function getCsInventory($cs_product_id, $province_id)
    {

        $dalCSOrd = &new dalCSOrder();
        $result = $dalCSOrd->getInventorys($cs_product_id, $province_id);
        $row = &$result->FetchRow();
        $units = $row["total_units"];

        //	$bottles= 10;
        return $units;
    }

//=> [_inited] => 1 [_obj] => [_names] => [_currentPage] => -1 [_atFirstPage] => [_atLastPage] => [_lastPageNo] => -1 [_maxRecordCount] => 0 [datetime] => [adodbFetchMode] => 2 ) 
//=> [fnExecute] => [fnCacheExecute] => [blobEncodeType] => [rsPrefix] => ADORecordSet_ [autoCommit] => 1 [transOff] => 0 [transCnt] => 0 [fetchMode] => 2 [_oldRaiseFn] => [_transOK] => [_connectionID] => Resource id #97 [_errorMsg] => [_errorCode] => [_queryID] => Resource id #133 [_isPersistentConnection] => [_bindInputArray] => [_evalAll] => [_affected] => [_logsql] => ) [_numOfRows] => 1 [_numOfFields] => 7 [_queryID] => Resource id #133 [_currentRow] => 0 [_closed] => [_inited] => 1 [_obj] => [_names] => [_currentPage] => -1 [_atFirstPage] => [_atLastPage] => [_lastPageNo] => -1 [_maxRecordCount] => 0 [datetime] => [adodbFetchMode] => 2 ) Array ( [customer_pst_exemption_id] => 1 [customer_id] => 9450 [pst_exempt_number] => 10018155 [created_user_id] => 15 [when_entered] => 2017-02-24 09:53:53 [modified_user_id] => 15 [when_modified] => 2017-02-24 09:54:02 ) 



    function getPstExemptNo($customer_id)
    {
        $dalCSOrd = &new dalCSOrder();
        $result = $dalCSOrd->getPstExemptNo($customer_id);
      
        $PST_EXMP_No = 0;  
        $row = &$result->FetchRow();
      
        if($row!=null)
            $PST_EXMP_No = $row["pst_exempt_number"];
                
          
        return $PST_EXMP_No;
    }
    
    
    function updateInventory($cs_product_id, $province_id, $order_qty)
    {
        $dalCSOrd = &new dalCSOrder();
        $dalCSOrd->updateCSProductInventory($cs_product_id, $province_id, $order_qty);
    }


    function updateCSOrderTable($order_id, $paymentStatus, $paymentType, $orderStatus,
        $deliveryDate, $adjustment1, $PST_No, $discType, $discVal,$is_other_delivery,$other_info)
    {
        
        $dalCSOrd = &new dalCSOrder();
        $dalCSOrd->upDateCSOrder($order_id, $paymentStatus, $paymentType, $orderStatus,
            $deliveryDate, $adjustment1, $PST_No, $discType, $discVal,$is_other_delivery,$other_info);
    }

    function upDateCSOrderITems($order_id, $productsInfos, $productsInfos_old, $province_id)
    {

        //delete order items and add new
        $dalCSOrd = &new dalCSOrder();

        $qtyDiff = 0;

        foreach ($productsInfos as $cs_product_id => $quantity)
        {
            $quantity_old = $productsInfos_old[$cs_product_id];

            $qtyDiff = $quantity - $quantity_old;
            if ($quantity_old == 0 || $quantity_old == "") //add
            {
                if ($quantity != "" && $quantity != 0) // add with quantity
                {
                    $result = $dalCSOrd->createCSOrderItems($order_id, $quantity, $cs_product_id, $province_id);
                    // update inventory
                    $this->updateInventory($cs_product_id, $province_id, $qtyDiff);
                }
            } else // update if($quantity_old!=0&&$quantity_old!="")//delete
            {
             
                if ($quantity == 0 || $quantity == "")
                {
               
                    //delete
                    $result = $dalCSOrd->deleteOrderItem($order_id, $cs_product_id);

                    // update inventory
                    $this->updateInventory($cs_product_id, $province_id, $qtyDiff);
                } else //update
                {
                    //update
                    if ($quantity != $quantity_old)
                    {
                        $result = $dalCSOrd->upDateCSOrderITem($order_id, $quantity, $cs_product_id);
                        // update inventory
                        $this->updateInventory($cs_product_id, $province_id, $qtyDiff);
                    }
                }

            }

        }
    }


    function updateCSOrder($order_id, $cs_product_id, $province_id, $qtyDifferece, $order_qty,
        $adjustment1, $adjustment2, $paymentStatus, $paymentType, $orderStatus, $deliveryDate,
        $isPST)
    {
        $dalCSOrd = &new dalCSOrder();

        $dalCSOrd->updateCSProductInventory($cs_product_id, $province_id, $qtyDifferece);

        $dalCSOrd->upDateCSOrderITems($order_id, $order_qty, $cs_product_id);

        $dalCSOrd->upDateCSOrder($order_id, $adjustment1, $adjustment2, $paymentStatus,
            $paymentType, $orderStatus, $deliveryDate, $isPST);

    }

    function deleteCSOrder($cs_product_id, $province_id, $order_id, $order_qty)
    {
        $dalCSOrd = &new dalCSOrder();
        $order_qty = 0 - $order_qty;

        $dalCSOrd->updateCSProductInventory($cs_product_id, $province_id, $order_qty);
        $dalCSOrd->deleteOrder($order_id);

    }

    function deleteCSOrderByID($order_id, $province_id)
    {
        $dalCSOrd = &new dalCSOrder();
        //get $order_qty

        $orderItemsInfo = $dalCSOrd->getOrderItems($order_id);


        foreach ($orderItemsInfo as $orderItem)
        {
            $order_qty = $orderItem["ordered_quantity"];
            $cs_product_id = $orderItem["product_id"];

            $dalCSOrd->updateCSProductInventory($cs_product_id, $province_id, (0 - $order_qty));

        }


        $dalCSOrd->deleteOrder($order_id);

    }

    //   function create($customerID, $estateID, $wines, $isImportAL=false,$orders=null) // should remove isAl and invoice_number
    //($customerID,$estate_id,$province_id,$cs_product_list );
    function createCSOrder($customer_id, $estateID, $province_id,$pst_no,$other_info, $csproducts)
    {

        $dalCSOrd = &new dalCSOrder();

        $savePST = $dalCSOrd->savePstExemptNo($customer_id,$pst_no);
        //get invoice base and invoice numenr
        $invoiceBase = $dalCSOrd->getInvoicBase($estateID, $province_id);
        $invoiceNumber = (int)$invoiceBase["invoice_base"] + 1;

        //update invoice base number
        $dalCSOrd->updateInvoicBase($estateID, $province_id, $invoiceNumber);
        //create order
        $order_id = $dalCSOrd->createCSOrder($invoiceNumber, $customer_id, $estateID, $province_id, $other_info);
        $content = count($csproducts);


        foreach ($csproducts as $csproductInfo)
        {
            //create order item
            $cs_product_id = $csproductInfo["cs_product_id"];
            $order_qty = $csproductInfo["quantity"];

            $result = $dalCSOrd->createCSOrderItems($order_id, $order_qty, $cs_product_id, $province_id);

            //deduct inventory first
            $result = $dalCSOrd->updateCSProductInventory($cs_product_id, $province_id, $order_qty);

        }
        return $order_id;
    }

    function getTotalSoldInfo($customer_id, $cs_product_id)
    {
        $dalCSOrd = &new dalCSOrder();
        $result = $dalCSOrd->getTotalSoldInfo($customer_id, $cs_product_id);

        $row = &$result->FetchRow();

        return $row;
    }


    function getAllProductsInfo4Province($cs_product_id, $province_id, $estate_id)
    {
        $sql = "Select cp.product_name, cp.cs_product_id, cp.units_per_case,
                cpinfo.price_per_unit, cpinfo.promotion_price,
                
                cpinvn.total_units 
                from cs_products cp,cs_products_info cpinfo,cs_product_inventory cpinvn
                
                where cp.cs_product_id=cpinfo.cs_product_id
                
                and cpinfo.province_id =$province_id
                
                and cpinvn.cs_product_id=cpinfo.cs_product_id
                
                and cpinvn.total_units >0
                
                and cp.estate_id = $estate_id
                and cp.deleted=0";

        $rows = $this->db->getAll($sql);
        if (count($rows) <= 0)
        {
            return 0;
        } else
        {
            return $rows;
        }
    }
}

/*class objProOrder
{	
var customer_name="";

function objProOrder($customer_name)
{
$this->customer_name = $customer_name;
}

}*/

class objProOrder // extends dalorderscollection
{
    var $customer_name = "";
    function csProOrder($customer_name)
    {

        $this->customer_name = $customer_name;
        // parent::dalorderscollection();
    }


}

?>
