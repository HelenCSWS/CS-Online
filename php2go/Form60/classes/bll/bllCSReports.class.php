<?php

import('php2go.base.Php2Go');
import('php2go.util.TypeUtils');
import('Form60.util.F60Date');
import('php2go.data.PagedDataSet');
import('Form60.base.F60DALBase');
import('Form60.base.F60DbUtil');
import('Form60.util.F60Common');

class F60CSReportsData extends Php2Go 
{
    var $db;
    var $cfg;
    var $data_cfg;
    var $logFile;
    var $errorMessage;
   
    function F60CSReportsData()
    {
     	include('config/emailoutconfig.php');
     	include('config/dataconfig.php');
        $this->db = & Db::getInstance();
        $this->db->setFetchMode(ADODB_FETCH_ASSOC);
        
        $this->cfg = $EMAIL_CFG;
        $this->data_cfg = $DATA_CFG;
    }
    
    function getFormatDate($sDate) //yyyymmdd    return: mm/dd/yyyy YYYYKL SUB STING ($ISDate,))
    {
        $syear=substr($sDate,0,4);
        $smonth=substr($sDate,4,2);
        $sday=substr($sDate,6,2);

        return $smonth.'/'.$sday.'/'.$syear;
    }
    function getEstate($estate_id)
	{
        $sql = "select estate_name from estates where estate_id = ".$estate_id;
        $result = & F60DbUtil::runSQL($sql);
        $row = & $result->FetchRow();
        return  $row['estate_name'];
    }
    
    function getBCEstate($bc_estate_id)
	{
        $sql = "select estate_name from bc_estates where bc_estate_id = ".$bc_estate_id;
        $result = & F60DbUtil::runSQL($sql);
        $row = & $result->FetchRow();
        return  $row['estate_name'];
    }
    
    
    function getTotalStoppers($invoice_no)
   {
         $SQL="SELECT 
             ifnull(sum(ot.ordered_quantity),0) units_sold
            
            from cs_product_orders co,cs_product_order_items ot, 
            lkup_product_types pType, cs_products cp
            where
            co.order_id=ot.order_id
            and cp.lkup_product_type_id = pType.lkup_product_type_id 
            and ot.product_id = cp.cs_product_id
                     
            and cp.estate_id =188
            and co.invoice_number ='$invoice_no'
            and cp.lkup_product_type_id =7
            ";
            
            
        
      $result = & F60DbUtil::runSQL($SQL);
      $row = & $result->FetchRow();
      return $row['units_sold'];
		 
	  
   }
   
    
   function getCSSalesTotal($estate_id,$from, $to)
   {
        $estate_filter="";
        if($estate_id !=0)
        {
            $estate_filter =" and co.estate_id=$estate_id";
        }
        
        $datePeriod = " and (delivery_date>= '$from' and delivery_date<='$to')";
         
        $SQL="SELECT delivery_date, p.province_id, p.province_name,p.short_name, ifnull( user_id,0) user_id,
             ifnull(user_name,'Un Assigned') user_name, customer_name, licensee_number, customer_Address,invoice_number,
             sum(ot.ordered_quantity) units_sold, sum(ot.ordered_quantity*ot.price_per_unit) price, 
             sum(ot.ordered_quantity*ot.promotion_price) prom_price, sum(ot.ordered_quantity*ot.price_winery) wholesale,
             payt.caption payType, co.estate_id, co.estate_name, sum(ot.ordered_quantity*ot.commission) bonus,
             sum(ot.cost_per_unit*ot.ordered_quantity) cost, sum(round((ot.ordered_quantity*(ot.price_per_unit-ot.cost_per_unit-ot.commission))*0.35,2)) profit,
            
             sum(round((ot.ordered_quantity*(ot.promotion_price-ot.cost_per_unit))*0.35-ot.commission,2)) prom_profit 
            
            from cs_product_orders co,cs_product_order_items ot, 
            lkup_payment_types payt, lkup_provinces p , cs_products cp
            where co.deleted=0 and ot.deleted=0 and co.order_id=ot.order_id
            and co.lkup_payment_type_id = payt.lkup_payment_type_id 
            and co.province_id =p.province_id and ot.product_id = cp.cs_product_id         
            $estate_filter
            $datePeriod
            group by co.order_id
            
            order by province_id, user_name, estate_id, delivery_date";
        
      $bRet=$this->db->getAll($SQL);
		 
	  return $bRet;
   }
       
   function getCSSalesTotal_new($estate_id,$from, $to)
   {
        $estate_filter="";
        if($estate_id !=0)
        {
            $estate_filter =" and co.estate_id=$estate_id";
        }
        
        $datePeriod = " and (delivery_date>= '$from' and delivery_date<='$to')";
        
        $SQL_Stoper ="";
        $SQL_No_Stoper ="";
        
       
            $SQL_Stoper="SELECT 7 p_type,delivery_date, p.province_id, p.province_name,p.short_name, ifnull( user_id,0) user_id,
                 ifnull(user_name,'Un Assigned') user_name, customer_name, licensee_number, customer_Address,invoice_number,
                 sum(ot.ordered_quantity) units_sold, sum(ot.ordered_quantity*ot.price_per_unit) price, 
                 sum(ot.ordered_quantity*ot.promotion_price) prom_price, sum(ot.ordered_quantity*ot.price_winery) wholesale,
                 payt.caption payType, co.estate_id, co.estate_name, sum(ot.ordered_quantity*ot.commission) bonus,
                 sum(ot.cost_per_unit*ot.ordered_quantity) cost, sum(round((ot.ordered_quantity*(ot.price_per_unit-ot.cost_per_unit-ot.commission))*0.35,2)) profit,
                
                 sum(round((ot.ordered_quantity*(ot.promotion_price-ot.cost_per_unit))*0.35-ot.commission,2)) prom_profit 
                            
                from cs_product_orders co,cs_product_order_items ot, 
                lkup_payment_types payt, lkup_provinces p , cs_products cp,
                lkup_product_types p_type
                
                where co.deleted=0 and ot.deleted=0 and co.order_id=ot.order_id
                and co.lkup_payment_type_id = payt.lkup_payment_type_id 
                and co.province_id =p.province_id and ot.product_id = cp.cs_product_id         
                and cp.estate_id=188
                
                and cp.lkup_product_type_id=p_type .lkup_product_type_id
        		and p_type.lkup_product_type_id =7
        
                $datePeriod
                group by co.order_id
                
               ";
               
               $SQL_No_Stoper="SELECT 1 p_type,delivery_date, p.province_id, p.province_name,p.short_name, ifnull( user_id,0) user_id,
                 ifnull(user_name,'Un Assigned') user_name, customer_name, licensee_number, customer_Address,invoice_number,
                 sum(ot.ordered_quantity) units_sold, sum(ot.ordered_quantity*ot.price_per_unit) price, 
                 sum(ot.ordered_quantity*ot.promotion_price) prom_price, sum(ot.ordered_quantity*ot.price_winery) wholesale,
                 payt.caption payType, co.estate_id, co.estate_name, sum(ot.ordered_quantity*ot.commission) bonus,
                 sum(ot.cost_per_unit*ot.ordered_quantity) cost, sum(round((ot.ordered_quantity*(ot.price_per_unit-ot.cost_per_unit-ot.commission))*0.35,2)) profit,
                
                 sum(round((ot.ordered_quantity*(ot.promotion_price-ot.cost_per_unit))*0.35-ot.commission,2)) prom_profit 
                            
                from cs_product_orders co,cs_product_order_items ot, 
                lkup_payment_types payt, lkup_provinces p , cs_products cp,
                lkup_product_types p_type
                
                where co.deleted=0 and ot.deleted=0 and co.order_id=ot.order_id
                and co.lkup_payment_type_id = payt.lkup_payment_type_id 
                and co.province_id =p.province_id and ot.product_id = cp.cs_product_id         
                and cp.estate_id=188
                
                and cp.lkup_product_type_id=p_type .lkup_product_type_id
        		and p_type.lkup_product_type_id !=7
        
                $datePeriod
                group by co.order_id
                
               ";
       
         
             $SQL_Product="SELECT  0 p_type, delivery_date, p.province_id, p.province_name,p.short_name, ifnull( user_id,0) user_id,
                     ifnull(user_name,'Un Assigned') user_name, customer_name, licensee_number, customer_Address,invoice_number,
                     sum(ot.ordered_quantity) units_sold, sum(ot.ordered_quantity*ot.price_per_unit) price, 
                     sum(ot.ordered_quantity*ot.promotion_price) prom_price, sum(ot.ordered_quantity*ot.price_winery) wholesale,
                     payt.caption payType, co.estate_id, co.estate_name, sum(ot.ordered_quantity*ot.commission) bonus,
                     sum(ot.cost_per_unit*ot.ordered_quantity) cost, sum(round((ot.ordered_quantity*(ot.price_per_unit-ot.cost_per_unit-ot.commission))*0.35,2)) profit,
                    
                     sum(round((ot.ordered_quantity*(ot.promotion_price-ot.cost_per_unit))*0.35-ot.commission,2)) prom_profit 
                                
                    from cs_product_orders co,cs_product_order_items ot, 
                    lkup_payment_types payt, lkup_provinces p , cs_products cp
                    where co.deleted=0 and ot.deleted=0 and co.order_id=ot.order_id
                    and co.lkup_payment_type_id = payt.lkup_payment_type_id 
                    and co.province_id =p.province_id and ot.product_id = cp.cs_product_id         
                    $estate_filter
                    $datePeriod
                    group by co.order_id
                    ";
                    
             $OrderBy =" order by province_id, user_name, estate_id, delivery_date";
                  
             $SQL ="";
             if($estate_id==0)
             {
                $SQL=$SQL_No_Stoper . " union " . $SQL_Stoper . " union " . $SQL_Product . $OrderBy;
                
             }
             else if($estate_id==188)
             {
                $SQL=$SQL_No_Stoper . " union " .$SQL_Stoper . $OrderBy;
             }
             else
             {
                $SQL=$SQL_Product  . $OrderBy;
             }
        
        
      $bRet=$this->db->getAll($SQL);
		 
	  return $bRet;
   }


    function getCSSalesDetails($estate_id,$from, $to)
   {
        $estate_filter="";
        if($estate_id !=0)
        {
            $estate_filter =" and co.estate_id=$estate_id";
        }
        
        $datePeriod = " and (delivery_date>= '$from' and delivery_date<='$to')";
        $SQL="
            SELECT delivery_date, p.province_id, p.province_name,p.short_name, ifnull( user_id,0) user_id,
            ifnull(user_name,'Un Assigned') user_name, customer_name, licensee_number, customer_Address,invoice_number, 
            ot.product_name, ot.ordered_quantity, ot.price_per_unit, ot.price_winery,cf.commission,
            (ot.price_per_unit*cf.commission) bonus, co.estate_name
            
            from cs_product_orders co,cs_product_order_items ot,  lkup_payment_types payt, lkup_provinces p
            , cs_products cp, cs_products_info cf
            
            where co.deleted=0
            and ot.deleted=0         
            and co.order_id=ot.order_id
            and co.lkup_payment_type_id = payt.lkup_payment_type_id
            
            and co.province_id =p.province_id
            and ot.product_id = cp.cs_product_id
            
            and cp.cs_product_id=cf.cs_product_id
            and cf.province_id= co.province_id 
        
            $estate_filter
            
            $datePeriod
            group by co.order_id
            
            order by province_id, user_name, estate_id, delivery_date
        ";
        
        	 $bRet=$this->db->getAll($SQL);
		 
		 return $bRet;
   }
}
?>