<?php

import('php2go.base.Php2Go');
import('php2go.util.TypeUtils');
import('Form60.util.F60Date');
import('Form60.base.F60DbUtil');
import('php2go.data.PagedDataSet');
import('Form60.base.F60DALBase');
import('Form60.util.F60Common');


class dalCSOrder extends Php2Go
{
    var $db;
    var $cfg;
    var $logFile;
    var $errorMessage;


    var $product_table = "";
    var $product_id_field = "";

    function dalCSOrder()
    {
        include ('config/emailoutconfig.php');

        $this->db = &Db::getInstance();
        $this->db->setFetchMode(ADODB_FETCH_ASSOC);
        
        

        $this->cfg = $EMAIL_CFG;
    }

    function getInventorys($cs_product_id, $province_id)
    {

        $sql = "select round(total_units,2) total_units from cs_product_inventory where cs_product_id =$cs_product_id and province_id=$province_id";

        $result = &F60DbUtil::runSQL($sql);

        return $result;
    }


    function getInvoicBase($estate_id, $province_id)
    {

        $sql = "select invoice_base from cs_product_invoice_base where province_id=$province_id  and estate_id=$estate_id";


        $result = &F60DbUtil::runSQL($sql);

        $row = &$result->FetchRow();

        return $row;
    }

    function getTaxRate($province_id, $cs_product_id = WINE_LIFE_ID)
    {

        $sql = "select * from lkup_province_tax where province_id=$province_id "; //"and cs_product_id=$cs_product_id";


        $result = &F60DbUtil::runSQL($sql);

  
      
        return $result;
    }
    
    function savePstExemptNo($customer_id,$pst_no)
    {
        if($pst_no==0||$pst_no=="")
        {
            $sql="update customers_pst_exemptions set pst_exempt_number =0 where customer_id =$customer_id";
            $retVal = $this->db->execute($sql);
        }
        else
        {  
            $sql="delete from customers_pst_exemptions  where customer_id =$customer_id";
            $retVal = $this->db->execute($sql);
            
            $sql="insert into customers_pst_exemptions(pst_exempt_number,customer_id)
                    value('$pst_no', $customer_id)";
                    
             $retVal = $this->db->execute($sql);       
            
        
        }
        return true;
        
    }
    
    function getPstExemptNo($customer_id)
    {

    //    $sql = "select * from customers_pst_exemptions where customer_id =22";//$customer_id "; //"and cs_product_id=$cs_product_id";
      $sql = "select * from customers_pst_exemptions where customer_id =$customer_id "; //"and cs_product_id=$cs_product_id";


        $result = &F60DbUtil::runSQL($sql);
        


        return $result;
    }

    function updateInvoicBase($estate_id, $province_id, $invoiceNo)
    {
        $sql = "UPDATE  cs_product_invoice_base SET invoice_base=$invoiceNo where province_id=$province_id and estate_id=$estate_id";
        $retVal = $this->db->execute($sql);

        return $retVal;
    }


    //create cs order and return the order_id
    //$invoiceNumber,$customer_id,$estateID,$province_id);
    function createCSOrder($invoice_number, $customer_id, $estate_id, $province_id,$other_info)
    {
        //get invoice_number
        $created_user_id = F60DALBase::get_current_user_id();
        $created_user_name = F60DALBase::get_current_user_full_name();
        $proShort = F60DbUtil::getProvinceInit($province_id);

        $invoice_number = $invoice_number.$proShort;

        $pst_rate ="pst_rate";
        
        if($estate_id ==196)
            $pst_rate ="0";
        
        $sql = "insert INTO cs_product_orders
		(customer_id, customer_name,customer_address,customer_city, postal_code,

			licensee_number,lkup_store_type_id,
			invoice_number,delivery_date,lkup_delivery_status_id,lkup_payment_status_id,lkup_payment_type_id,
			estate_id,estate_name,
			when_entered,created_user_id,created_by_user_name,
			user_id,user_name,
			GST_factor,PST_factor,province_id,
            other_info)
		
		select c.customer_id,customer_name,c.billing_address_street,c.billing_address_city,c.billing_address_postalcode, licensee_number, lkup_store_type_id,
		'$invoice_number',date_format(now(), '%Y-%m-%d'),2,2,6,
		$estate_id,e.estate_name,
		now(),$created_user_id,'$created_user_name',
		 u.user_id,concat(us.first_name,' ',us.last_name),
		gst_Rate,$pst_rate,$province_id, '$other_info'
		
		from estates e,lkup_province_tax pt, customers c left join  users_customers u
		
		on  c.customer_id=u.customer_id
		left join users us
		 on us.user_id = u.user_id
		
		where c.customer_id=$customer_id
	
		and pt.province_id=$province_id
        and e.estate_id =$estate_id ";

        $retVal = $this->db->execute($sql);

        $order_id = 0;
        if ($retVal)
        {
            $order_id = $this->db->lastInsertId();
        }

        return $order_id;

    }
    //($order_id,$order_qty,$cs_product_id,$province_id);
    function createCSOrderItems($order_id, $order_qty, $cs_product_id, $province_id)
    {

        $created_user_id = F60DALBase::get_current_user_id();
        //get invoice_number
        $sql = "insert into cs_product_order_items 
				(order_id, ordered_quantity, 
				product_id,product_name,product_code,
                
				price_per_unit,promotion_price,
                cost_per_unit, commission,
				when_entered, created_user_id)			
				
				select $order_id,$order_qty,
				cp.cs_product_id,product_name,product_code,
				price_per_unit,promotion_price,
                cost_per_unit, commission,
				now(),$created_user_id
				
				from cs_products_info cpinfo, cs_products cp 
                where cp.cs_product_id=$cs_product_id
                and cpinfo.province_id = $province_id
                and cpinfo.cs_product_id = cp.cs_product_id";

        $retVal = $this->db->execute($sql);

        return $retVal;

    }

    function updateCSProductInventory($cs_product_id, $province_id, $order_qty)
    {

        $sql = "update cs_product_inventory set total_units =total_units- $order_qty where cs_product_id = $cs_product_id and province_id=$province_id";

        $retVal = $this->db->execute($sql);

        return $retVal;

    }
    /*
    (customer_id, customer_name,customer_address,customer_city, postal_code,

    licensee_number,lkup_store_type_id,
    invoice_number,delivery_date,lkup_delivery_status_id,lkup_payment_status_id,lkup_payment_type_id,
    estate_id,estate_name,
    when_entered,created_user_id,user_id,
    
    */
    function upDateCSOrder($order_id, $paymentStatus, $paymentType, $orderStatus, $deliveryDate,
        $adjustment1, $PST_No, $discType, $discVal,$is_other_delivery,$other_info)
    {
        //get invoice_number
        $modify_user_id = F60DALBase::get_current_user_id();
        $deliveryDate = F60Date::getSqlDate($deliveryDate);

        $isPst = 1;

        if ($discType != 0 && $discVal == "")
            $discVal = 0;

        if ($PST_No != "")
            $isPst = 0;


        $sql = "UPDATE cs_product_orders set lkup_payment_status_id = $paymentStatus 
        		, lkup_payment_type_id =$paymentType
        		, lkup_delivery_status_id = $orderStatus
        		, delivery_Date ='$deliveryDate'
        		, adjustment_1 = $adjustment1
        		, discount = $discVal
                , discount_type = $discType
        		, PST_included=$isPst
                , PST_NO = '$PST_No'
                ,other_info='$other_info'
                ,is_other_delivery =$is_other_delivery
        		, modified_user_id = $modify_user_id
        		 where order_id=$order_id";

        $retVal = $this->db->execute($sql);

        return $retVal;

    }

    function upDateCSOrderITem($order_id, $orderQty, $cs_product_id)
    {
        //get invoice_number
        $modify_user_id = F60DALBase::get_current_user_id();

        $sql = "UPDATE cs_product_order_items set ordered_quantity = $orderQty 
				, modified_user_id = $modify_user_id
				where product_id=$cs_product_id
				and order_id =$order_id";

        $retVal = $this->db->execute($sql);

        return $retVal;

    }


    function getOrderInfo($order_id, $isViewInvioice = false)
    {
        $sql = "select st.license_name, date_format(po.when_entered,'%m/%d/%Y') crt_on,concat(u.first_name,' ',u.last_name) created_user,
                    concat(customer_address,', ',customer_city) customer_whole_address,
                    po.* 
                    
                    from cs_product_orders po, lkup_store_types st, users u
                    where order_id =$order_id
                    and po.lkup_store_type_id=st.lkup_store_type_id
                    
                    and u.user_id=po.created_user_id";

        if ($isViewInvioice)
        {

            $sql = "select  st.caption payment_type, date_format(po.delivery_date,'%M %d, %Y') frm_delivery_date,p.short_name,
                po.* 	
                from cs_product_orders po, lkup_payment_types st, lkup_provinces p
                where order_id =$order_id
                and po.lkup_payment_type_id=st.lkup_payment_type_id
                
                and po.province_id=p.province_id";
        }

        $result = &F60DbUtil::runSQL($sql);

        return $result;
    }

    function deleteOrder($order_id)
    {
        $user_id = F60DALBase::get_current_user_id();

        $sql = "update cs_product_orders set deleted =1 , modified_user_id=$user_id where order_id =$order_id";

        $retVal = $this->db->execute($sql);

        $sql = "update cs_product_order_items set deleted =1 , modified_user_id=$user_id where order_id =$order_id";

        $retVal = $this->db->execute($sql);

        return $retVal;
    }

    function deleteOrderItems($order_id)
    {
        $user_id = F60DALBase::get_current_user_id();

        $sql = "update cs_product_order_items set deleted =1 , modified_user_id=$user_id where order_id =$order_id";

        $retVal = $this->db->execute($sql);

        return $retVal;
    }

    function deleteOrderItem($order_id, $cs_product_id)
    {
        $user_id = F60DALBase::get_current_user_id();

        $sql = "update cs_product_order_items set deleted =1 , modified_user_id=$user_id where order_id =$order_id and product_id=$cs_product_id";

        $retVal = $this->db->execute($sql);

        return $retVal;
    }

    function getOrderItems($order_id)
    {
        $sql = "SELECT *
            FROM cs_product_order_items ot
            
            where order_id=$order_id
            and ot.deleted = 0
            
            order by product_name asc";

        $rows = $this->db->getAll($sql);
        return $rows;
    }

    function getOrderItems4Edit($order_id, $estate_id, $province_id)
    {
       /* $sql = "00select cp.product_name,cp.product_code, cp.cs_product_id, cp.units_per_case, cf.price_per_unit,
                 cf.promotion_price, cn.total_units 
                 
                 , co.price_per_unit order_price , co.promotion_price order_special_price, 
                 
                  ifnull(co.ordered_quantity,0) ordered_quantity, co.product_name order_product_name, co.product_code order_product_code
                 
                 from cs_products cp, cs_product_inventory cn, cs_products_info cf
                 
                left join cs_product_order_items co on co.product_id= cf.cs_product_id and co.order_id= $order_id
                 
                 where cp.cs_product_id = cf.cs_product_id 
                 and cp.estate_id = $estate_id 
                 and cf.province_id=$province_id 
                 and cn.cs_product_id = cf.cs_product_id 
                 and cn.province_id = $province_id
                 and co.deleted =0
                 
                 order by ordered_quantity desc, product_name";*/
                 
         $sql=" select cp.product_name,cp.product_code, cp.cs_product_id, cp.units_per_case, 
                cf.price_per_unit, cf.promotion_price, cn.total_units , co.price_per_unit order_price ,
                co.promotion_price order_special_price, ifnull(co.ordered_quantity,0) ordered_quantity,
                co.product_name order_product_name, co.product_code order_product_code
                
                from cs_product_inventory cn, cs_products_info cf,cs_products cp
                left join cs_product_order_items co on co.product_id= cp.cs_product_id 
                and co.order_id=$order_id and co.deleted=0 
                
                where cp.cs_product_id = cf.cs_product_id and cp.estate_id = $estate_id                 
                and cf.province_id=$province_id  
                and cn.cs_product_id = cf.cs_product_id 
                and cn.province_id = $province_id 
                order by ordered_quantity desc, product_name";

        $rows = $this->db->getAll($sql);
        return $rows;
    }

    function getOrderItems4OrderView($order_id)
    {
        $sql = "SELECT ot.ordered_quantity,
                ot.price_per_unit,
                ot.promotion_price,
                cp.product_name,
                cp.product_code,
                type.display_name product_type
                
                FROM cs_product_order_items ot, cs_products cp, lkup_product_types type
                
                where order_id=$order_id
                and cp.cs_product_id = ot.product_id
                and type.lkup_product_type_id = cp.lkup_product_type_id
                and ot.deleted = 0";

                $rows = $this->db->getAll($sql);
                return $rows;
    }

    function getTotalSoldInfo($customer_id, $cs_product_id)
    {
        $sql = "select sum(ordered_quantity) sold, round(sum(ordered_quantity/units_per_case),2) sold_cs  from cs_product_orders po, cs_product_order_items pot, cs_products w 
 
			 where  po.customer_id=$customer_id
			 
             and pot.product_id = $cs_product_id
			 and pot.order_id=po.order_id
			 
			 and w.cs_product_id=pot.product_id
			 
			 and po.deleted=0
			 and pot.deleted=0 
			group by customer_id
			";
        $result = &F60DbUtil::runSQL($sql);
        return $result;
    }


    function debugText($msg)
    {
        $fp = fopen("logs/sales.log", "a");
        fputs($fp, $msg . "\n");
        fclose($fp);
    }

}

?>
