<?php

import('php2go.base.Php2Go');
import('php2go.util.TypeUtils');
import('Form60.util.F60Date');
import('Form60.base.F60DbUtil');
import('php2go.data.PagedDataSet');
import('Form60.base.F60DALBase');
import('Form60.util.F60Common');


class dalSalesData extends Php2Go 
{
    var $db;
    var $cfg;
    var $logFile;
    var $errorMessage;
    
    
    var $product_table="";
    var $product_id_field="";
   
    function dalSalesData()
    {
	 	include('config/emailoutconfig.php');

        $this->db = & Db::getInstance();
        $this->db->setFetchMode(ADODB_FETCH_ASSOC);
        
        $this->cfg = $EMAIL_CFG;
    }

	
	function getDateQuery($sales_year,$sales_period,$isQuarter,$isBC=false)
	{
		$current_month = date(n);
		if($sales_year<date("Y"))
		{
			$current_month =12;
		}
		
		$sqlDate = "s.sale_date";
		if($isBC)
		{
			$sqlDate="o.delivery_date";
		}
		
	
		
		if($isQuarter)
		{
			 if($sales_period ==1)
			  		$byPeriod = " 1<= month($sqlDate) and month($sqlDate)<=3 ";
			 else if($sales_period ==2)
			 		$byPeriod = " 4<= month($sqlDate) and month($sqlDate)<=6 ";
			 else if($sales_period ==3)
			 		$byPeriod = " 7<= month($sqlDate) and month($sqlDate)<=9 ";
			 else if($sales_period ==4)
			 		$byPeriod = " 10<= month($sqlDate) and month($sqlDate)<=12 ";
			else if($sales_period ==-1)
			{
			 		$byPeriod = " 1<= month($sqlDate) and month($sqlDate)<=$current_month ";
			}
		  		
		}
		else
			$byPeriod = "month($sqlDate)=$sales_period";
			
		
		return $byPeriod;		
	}
	
	function debugText($msg)
	{
		$fp = fopen("logs/sales.log","a");
		fputs($fp,  $msg."\n");
		fclose($fp);
	}
	
	function getCustomerSales($orderType,$orderBy, $sales_year,$sales_period,$isQuarter,$store_type_id,$customerID,$province_id,$page,$pageSize)
	{
	 // $province_id;
	 	
     	$sortBy = ($orderType == "a")?" ASC":" DESC";
	
		$current_month = date(n);
		if($sales_year<date("Y"))
		{
			$current_month =12;
		}
	
		$byPeriod =dalSalesData::getDateQuery($sales_year,$sales_period,$isQuarter);
		$byPeriod_ca =dalSalesData::getDateQuery($sales_year,$sales_period,$isQuarter,true);
	
		$sqlCode_Ca ="";
		if($store_type_id<8)
		{
			$sqlCode_Ca=" Union Select 
							sum(ot.ordered_quantity* ot.price_winery) total_amount,
							sum(ot.ordered_quantity* ot.price_per_unit) total_sales,
							
							sum(ot.ordered_quantity/w.bottles_per_case) cases_sold,
							sum(ot.profit) profit
							
							FROM orders o, order_items ot, estates e, wines w, lkup_bottle_sizes lkbt,lkup_wine_color_types lk_color
							
							
							where $byPeriod_ca	and year(o.delivery_date)= $sales_year
							and o.customer_id =$customerID 							
							and ot.wine_id = w.wine_id
							and w.estate_id = e.estate_id
							and w.lkup_wine_color_type_id = lk_color.lkup_wine_color_type_id
							and w.lkup_bottle_size_id = lkbt.lkup_bottle_size_id
							and o.order_id=ot.order_id
							and o.deleted =0
							and ot.deleted=0
							group by o.customer_id
							
						  ";
						
			$sqlCode="SELECT 								
							sum(s.price_winery* s.unit_sales) total_amount,
							sum(s.price_per_unit* s.unit_sales) total_sales,
							sum(s.unit_sales/w.bottles_per_case) cases_sold,
							sum(s.profit_per_unit*s.unit_sales) profit
							
							FROM ssds_sales s,
							estates e, wines w, lkup_wine_color_types lk_color,wines_info wf
							
							where $byPeriod 
							and year(s.sale_date)= $sales_year
							and s.customer_id =$customerID
							
							and s.wine_id = w.wine_id
							and w.estate_id = e.estate_id
							and w.lkup_wine_color_type_id = lk_color.lkup_wine_color_type_id
						   	and s.is_international=1
						   	and w.wine_id = wf.wine_id
						   	and wf.province_id=$province_id
							group by s.customer_id
								
							UNION
							
							SELECT 								
							sum(s.price_winery* s.unit_sales) total_amount,
							sum(s.price_per_unit* s.unit_sales) total_sales,
							sum(s.unit_sales/w.bottles_per_case) cases_sold,
							sum(s.profit_per_unit*s.unit_sales) profit
							
							FROM ssds_sales s,
							estates e, beers w, lkup_beer_types lk_color,beers_info wf
							
							where $byPeriod 
							and year(s.sale_date)= $sales_year
							and s.customer_id =$customerID
							
							and s.wine_id = w.beer_id
							and w.estate_id = e.estate_id
							and w.lkup_beer_type_id = lk_color.lkup_beer_type_id
							and s.is_international=1
							and w.beer_id = wf.beer_id
							and wf.province_id=$province_id
							group by s.customer_id
							
							$sqlCode_Ca 
							";
							
							
		}
		else
		{
				 
			$sqlCode="SELECT sum(winfo.price_winery* s.unit_sales) total_amount,
							sum(s.price_per_unit* s.unit_sales) total_sales,
							sum(s.unit_sales/w.bottles_per_case) cases_sold,
							sum(s.profit_per_unit*s.unit_sales) profit
					
							FROM ssds_sales s,
							estates e, wines w, lkup_wine_color_types lk_color, wines_info winfo
							
							WHERE $byPeriod 
							and year(s.sale_date)= $sales_year
							and s.customer_id =$customerID
							
							and s.wine_id = w.wine_id
							and w.estate_id = e.estate_id
							and w.lkup_wine_color_type_id = lk_color.lkup_wine_color_type_id
							and s.is_international=1
							and w.wine_id = winfo.wine_id
							and winfo.province_id =$province_id
							group by s.customer_id";
		}
  		$pagedRS = & PagedDataSet::getInstance("db");
   		$pagedRS->setPageSize($pageSize);
        $pagedRS->setCurrentPage($page);
        if (!$pagedRS->load($sqlCode))
        {
            $this->file_format_error .= "Error: unable to get report data.";
            $bRet = false;
            return false;
        }
        
        $rs["total_sales"]=$pagedRS;
    
       	$sqlCode_Ca ="";
       
	
		if($store_type_id<8)//BC 
		{
	
			$sqlCode_Ca=" union  SELECT date_format(o.delivery_date ,'%m/%d/%Y') sale_date,
								e.billing_address_country country, 
								e.estate_name, concat(w.wine_name,' ',w.vintage)  wine_name,
								lk_color.caption wine_type,
								sum(ot.ordered_quantity) units_sale, 
								sum(ot.ordered_quantity/w.bottles_per_case) cases_sold,
								
								
								sum(ot.price_winery * ot.ordered_quantity)  total_amount,
								sum(ot.price_per_unit * ot.ordered_quantity)  total_sales,	
								sum(ot.profit) profit,					
								w.cspc_code cspc_code,
								w.bottles_per_case
							
									
								FROM orders o, order_items ot, estates e, wines w, lkup_wine_color_types lk_color
							
								where $byPeriod_ca	and year(o.delivery_date)= $sales_year
								and o.customer_id =$customerID 							
								and ot.wine_id = w.wine_id
								and w.estate_id = e.estate_id
								and w.lkup_wine_color_type_id = lk_color.lkup_wine_color_type_id
								and o.order_id=ot.order_id
								and o.deleted =0
								and ot.deleted=0
							
								group by o.delivery_date ,w.wine_id order by ".$orderBy.$sortBy;
						
							
						
			$sqlCode="SELECT date_format(s.sale_date ,'%m/%d/%Y') sale_date,
							e.billing_address_country country, e.estate_name, concat(w.wine_name,' ',w.vintage)  wine_name,
							lk_color.caption wine_type,
							sum(s.unit_sales) units_sale, 
							sum(s.unit_sales)/w.bottles_per_case cases_sold,
							sum(s.price_winery* s.unit_sales) total_amount,
							sum(s.price_per_unit* s.unit_sales) total_sales,
							sum(s.profit_per_unit* s.unit_sales) profit,
							
							s.skua cspc_code,
							w.bottles_per_case
							
							FROM ssds_sales s,
							estates e, wines w, lkup_wine_color_types lk_color
							
							where $byPeriod 
							and year(s.sale_date)= $sales_year
							and s.customer_id =$customerID
							
							and s.wine_id = w.wine_id
							and w.estate_id = e.estate_id
							and w.lkup_wine_color_type_id = lk_color.lkup_wine_color_type_id
							and s.is_international=1
							
							and s.province_id=$province_id
							and s.product_id =1
							
							group by s.sale_date , s.wine_id 
								
							UNION
							
							SELECT date_format(s.sale_date ,'%m/%d/%Y') sale_date,
							e.billing_address_country country, e.estate_name, w.beer_name  wine_name,
							lk_color.caption wine_type,
							sum(s.unit_sales) units_sale, 
							sum(s.unit_sales)/w.bottles_per_case cases_sold,
							sum(s.price_winery* s.unit_sales) total_amount,
							sum(s.price_per_unit* s.unit_sales) total_sales,
							sum(s.profit_per_unit* s.unit_sales) profit,
							
							s.skua cspc_code,
							w.bottles_per_case
							
							FROM ssds_sales s,
							estates e, beers w, lkup_beer_types lk_color
							
							where $byPeriod 
							and year(s.sale_date)= $sales_year
							and s.customer_id =$customerID
							
							and s.wine_id = w.beer_id
							and s.product_id =2
							and w.estate_id = e.estate_id
							and w.lkup_beer_type_id = lk_color.lkup_beer_type_id
							and s.is_international=1
							
							and s.province_id=$province_id
							
							group by s.sale_date , s.wine_id
							
							
							$sqlCode_Ca";
		}
		else
		{
				 
			$sqlCode="SELECT date_format(s.sale_date ,'%m/%d/%Y') sale_date,
						e.billing_address_country country, e.estate_name, concat(w.wine_name,' ',w.vintage)  wine_name,
						lk_color.caption wine_type,
						sum(s.unit_sales) units_sale, 
						sum(s.cases_sold) cases_sold,
						sum(s.price_winery* s.unit_sales) total_amount,
						sum(s.price_per_unit* s.unit_sales) total_sales,
						sum(s.profit_per_unit* s.unit_sales) profit,
						
						
						s.skua cspc_code,
						w.bottles_per_case
						FROM ssds_sales s,
						estates e, wines w, lkup_wine_color_types lk_color
						
						where $byPeriod 
						and year(s.sale_date)= $sales_year
						and s.customer_id =$customerID
						
						and s.wine_id = w.wine_id
						and w.estate_id = e.estate_id
						and w.lkup_wine_color_type_id = lk_color.lkup_wine_color_type_id
						and s.is_international=1
						and s.province_id=$province_id
						AND s.product_id =1
						group by s.sale_date , s.wine_id
						UNION
						
						SELECT date_format(s.sale_date ,'%m/%d/%Y') sale_date,
						e.billing_address_country country, e.estate_name, w.beer_name  wine_name,
						lk_color.caption wine_type,
						sum(s.unit_sales) units_sale, 
						sum(s.cases_sold) cases_sold,
						sum(s.price_winery* s.unit_sales) total_amount,
						sum(s.price_per_unit* s.unit_sales) total_sales,
						sum(s.profit_per_unit* s.unit_sales) profit,
						
						
						s.skua cspc_code,
						w.bottles_per_case
						FROM ssds_sales s,
						estates e, beers w, lkup_beer_types lk_color
						
						where $byPeriod 
						and year(s.sale_date)= $sales_year
						and s.customer_id =$customerID
						and s.product_id =2
						and s.wine_id = w.beer_id
						and w.estate_id = e.estate_id
						and w.lkup_beer_type_id = lk_color.lkup_beer_type_id
						and s.is_international=1
						and s.province_id=$province_id
						group by s.sale_date , s.wine_id
						order by ".$orderBy.$sortBy;
		}
  
  		
		$pagedRS1 = & PagedDataSet::getInstance("db");
		$pageSize =1000;;
		$pagedRS1->setPageSize($pageSize);
		$pagedRS1->setCurrentPage($page);     
	  

	  	if (!$pagedRS1->load($sqlCode))
        {
            $this->file_format_error .= "Error: unable to get sales data.";
            $bRet = false;
            return false;
        }
         
		$rs["sales_details"]=$pagedRS1;
		  
        return $rs;
    }
}

?>
