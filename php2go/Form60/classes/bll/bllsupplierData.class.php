<?php

import('php2go.base.Php2Go');
import('php2go.util.TypeUtils');
import('Form60.util.F60Date');
import('Form60.base.F60DbUtil');
import('php2go.data.PagedDataSet');
import('Form60.base.F60DALBase');
import('Form60.util.F60Common');


class suppliersData extends Php2Go 
{
    var $db;
    var $cfg;
    var $logFile;
    var $errorMessage;
    
    
    var $product_table="";
    var $product_id_field="";
   
    function suppliersData()
    {
	 	include('config/emailoutconfig.php');

        $this->db = & Db::getInstance();
        $this->db->setFetchMode(ADODB_FETCH_ASSOC);
        
        $this->cfg = $EMAIL_CFG;
    }
    function getEstate($user_id)
    {
			$sql ="select estate_name, e.estate_id from users u, estates e where u.user_id =$user_id and u.estate_id=e.estate_id";
			$rows = $this->db->getAll($sql);
			
			return $rows;
	 }
	 
	function getSaleYears($province_id, $estate_id)
    {
     	
     		$productID =$this->getProductId($estate_id);
     			
     		if($estate_id ==-1)
     		{
				$estateQuery = "  (w.estate_id = 96 or w.estate_id = 97 ) ";
			}
			else
			{
				$estateQuery =" w.estate_id = $estate_id";
			}
			
			$sql ="select distinct year(sale_date) sale_year from ssds_sales s, $this->product_table w where s.wine_id = w.$this->product_id_field and s.product_id=$productID and $estateQuery and s.province_id = $province_id order by sale_year desc";
			
			if($estate_id==175)
					$sql = "select distinct year(delivery_date) sale_year from orders o where o.estate_id = 175 order by sale_year desc"; //temparary fixed sales not in ssds_saels table issue
			
			$rows = $this->db->getAll($sql);
			
			return $rows;
	 }
	
	function getLatestSaleYear($province_id, $estate_id)
    {
     
     		$producID =$this->getProductId($estate_id);
     		
     		if($estate_id ==-1)
     		{
				$estateQuery = "  (w.estate_id = 96 or w.estate_id = 97 ) ";
			}
			else
			{
				$estateQuery =" w.estate_id = $estate_id";
			}
			
			$sql ="select max(year(sale_date)) sale_year from ssds_sales s, $this->product_table w where s.wine_id = w.$this->product_id_field and $estateQuery and s.province_id = $province_id order by sale_year desc";
			$rows = $this->db->getAll($sql);
			
			return $rows[0]["sale_year"];
	 }
	
	
	function getSaleMonths($province_id, $estate_id,$sales_year)
    {
     		$productID =$this->getProductId($estate_id);
     		
     	
		
     		if($province_id == 1)
     		{
     		 	
				if(F60DbUtil::checkIsBCByEstate($estate_id))  // BC estates
				{
					if($estate_id ==-1 )
		     		{
						$estateQuery = "  (o.estate_id = 96 or o.estate_id = 97 ) ";
					}
					else
						$estateQuery =" o.estate_id = $estate_id";
					
					$sql ="select distinct month(delivery_date) sale_month from orders o where $estateQuery and year(delivery_date)= $sales_year order by sale_month asc";
				}
				else //international
				{
				 
					$estateQuery =" w.estate_id = $estate_id";
					if($estate_id ==141) // bro
						$sales_year =2016;
						
						
					$sql ="select distinct month(sale_date) sale_month from ssds_sales s, $this->product_table w where s.wine_id = w.$this->product_id_field and s.product_id=$productID and $estateQuery and s.province_id = $province_id and year(sale_date)= $sales_year order by sale_month asc";
				}
			}
			else
			{
				if($estate_id ==-1)
	     		{
					$estateQuery = "  (w.estate_id = 96 or w.estate_id = 97 ) ";
				}
				else
				{
					$estateQuery =" w.estate_id = $estate_id";
				}
				
			//	if($province_id ==2)
					$sales_year = 2016;
					
				$sql ="select distinct month(sale_date) sale_month from ssds_sales s, $this->product_table w where s.wine_id = w.$this->product_id_field and $estateQuery and s.province_id = $province_id and year(sale_date)= $sales_year and s.product_id=$productID order by sale_month asc";
			}
			
	     		
			//$sql="000$sql";
			$rows = $this->db->getAll($sql);
			
			return $rows;
	 }	 
	 
	

	function getEstateName($estate_id)
    {
			$sql ="select estate_name, e.estate_id from  estates e where e.estate_id=$estate_id";
			$rows = $this->db->getAll($sql);
			
			$estate_name = $rows[0]["estate_name"];
			return $estate_name;
	 }
	 
    function getWines($province_id,$estate_id)
    {
     	$productId =$this->getProductId($estate_id);
			
		if($productId==2)
		{
			$product_table="beers";
			$product_name ="beer_name";
			$bottle_size_table ="lkup_beer_sizes";
			$type_table ="lkup_beer_types";
			$bottle_size_id ="lkup_beer_size_id";
			$type_id ="lkup_beer_type_id";
			$product_id_field ="beer_id";
			
			$vintage ="0000 vintage";
			
			$product_info_table="beers_info";
		}
		else
		{
			$product_table="wines";
			$product_name ="wine_name";
			$product_id_field ="wine_id";
			$bottle_size_table ="lkup_bottle_sizes";
			$type_table ="lkup_wine_color_types";
			$bottle_size_id ="lkup_bottle_size_id";
			$type_id ="lkup_wine_color_type_id";
			$vintage ="max(vintage) vintage";
			$product_info_table="wines_info";
		}
		
		
     		if($province_id==1)
     		{
     		 	  if($estate_id == -1) //all enotecca wines
     		 	  {
     		 	  
						$sql="select w.wine_name wine_name, max(vintage) vintage, w.cspc_code, lkclr.display_name color,lkblt.display_name bottle_size,w.bottles_per_case 
						from wines w, lkup_wine_color_types lkclr, lkup_bottle_sizes lkblt, estates e
						where w.estate_id =e.estate_id
						and w.lkup_wine_color_type_id = lkclr.lkup_wine_color_type_id
						and w.lkup_bottle_size_id =lkblt.lkup_bottle_size_id
						and e.billing_address_country='Canada'
						and e.estate_id=e.estate_id
						and w.deleted=0
						and e.deleted=0
						and w.cspc_code<>''
						and (e.estate_id =97
						or e.estate_id =96)
						
						
						group by w.cspc_code
						order by w.cspc_code
						"	;
						
						//and (cspc_code = 162578 or cspc_code=109785) test code
						/*$sql="select w.wine_name wine_name,vintage, w.cspc_code, lkclr.display_name color,lkblt.display_name bottle_size,w.bottles_per_case 
						from wines w, lkup_wine_color_types lkclr, lkup_bottle_sizes lkblt, estates e
						where w.estate_id =e.estate_id
						and w.lkup_wine_color_type_id = lkclr.lkup_wine_color_type_id
						and w.lkup_bottle_size_id =lkblt.lkup_bottle_size_id
						and e.billing_address_country='Canada'
						and e.estate_id=e.estate_id
						and w.deleted=0
						and e.deleted=0
						and w.cspc_code<>''
						and (e.estate_id =97
						or e.estate_id =96)
						
						order by w.cspc_code
						"	;*/
				  }
				else
				{
		        
				  	if(!F60DbUtil::checkIsBCByEstate($estate_id)) //international wines
			        {			
				  		$sql="select w.$product_name wine_name, $vintage, wf.cspc_code, lkclr.display_name color,lkblt.display_name bottle_size,w.bottles_per_case 
						from $product_table w, $product_info_table wf,$type_table lkclr, $bottle_size_table lkblt
						where w.$product_id_field=wf.$product_id_field 
						and w.$type_id = lkclr.$type_id
						and w.$bottle_size_id =lkblt.$bottle_size_id
						and wf.province_id=$province_id
						and w.deleted=0
						and wf.deleted=0
						and w.estate_id =$estate_id
						group by wf.cspc_code
						order by wf.cspc_code
						";
					}
					else //Canada wines
					{
						$sql="select w.wine_name wine_name, max(vintage) vintage, w.cspc_code, lkclr.display_name color,lkblt.display_name bottle_size,w.bottles_per_case 
						from wines w, lkup_wine_color_types lkclr, lkup_bottle_sizes lkblt, estates e
						where w.estate_id =e.estate_id
						and w.lkup_wine_color_type_id = lkclr.lkup_wine_color_type_id
						and w.lkup_bottle_size_id =lkblt.lkup_bottle_size_id
						and e.billing_address_country='Canada'
						and e.estate_id=e.estate_id
						and w.deleted=0
						and e.deleted=0
						and w.cspc_code<>''
						and e.estate_id = $estate_id
						group by w.cspc_code
						order by w.cspc_code
						"	;
					}
				}
		}
		else if($province_id==2)
		{		
			
			if($estate_id == -1) 
			{
				$sql="select w.wine_name wine_name, max(vintage) vintage, wf.cspc_code, lkclr.display_name color,lkblt.display_name bottle_size,w.		bottles_per_case 
					from wines w, wines_info wf,lkup_wine_color_types lkclr, lkup_bottle_sizes lkblt
					where w.wine_id=wf.wine_id 
					and w.lkup_wine_color_type_id = lkclr.lkup_wine_color_type_id
					and w.lkup_bottle_size_id =lkblt.lkup_bottle_size_id
					and wf.province_id=$province_id
					and w.deleted=0
					and wf.deleted=0
					and (w.estate_id =96 or w.estate_id =97)
					group by wf.cspc_code
					order by wf.cspc_code
					";
			}
			else
			{
				$sql="select w.$product_name wine_name, $vintage, wf.cspc_code, lkclr.display_name color,lkblt.display_name bottle_size,w.		bottles_per_case 
						from $product_table w, $product_info_table wf,$type_table lkclr, $bottle_size_table lkblt
						where w.$product_id_field=wf.$product_id_field 
						and w.$type_id = lkclr.$type_id
						and w.$bottle_size_id =lkblt.$bottle_size_id
						and wf.province_id=$province_id
						and w.deleted=0
						and wf.deleted=0
						and w.estate_id =$estate_id
						group by wf.cspc_code
						order by wf.cspc_code
					 ";
			}
		}
				
        $rows = $this->db->getAll($sql);
        
        return $rows;
    }
    function getVintages($SKU)
    {
     	$sql ="select distinct vintage from wines where cspc_code = '$SKU' order by vintage desc";
		
		$rows = $this->db->getAll($sql);
		
		return $rows;
		
	}
	function getWineNamesBySKU($SKU)
	{
		$sql ="select wine_name from wines where cspc_code = $SKU order by wine_name asc limit 1";
		
		$rows = $this->db->getAll($sql);
		
		$wine_name = $rows[0]["wine_name"];
		
		$wine_name=str_replace('- okan','',$wine_name);
		$wine_name=str_replace('-okan','',$wine_name);
		$wine_name=str_replace('- vic','',$wine_name);
		$wine_name=str_replace('-vic','',$wine_name);
		
		return $wine_name;
	}
    function getStoreTypes($province_id,$estate_id)
    {
   
			if( !F60DbUtil::checkIsBCByEstate($estate_id))//international
     		{
				$sql ="SELECT lkup_store_type_id,license_name FROM lkup_store_types WHERE province_id =$province_id ORDER BY license_name";
			}
			else
			{
				if($province_id ==1)
					$sql ="SELECT lkup_store_type_id,license_name FROM lkup_store_types WHERE province_id =$province_id and lkup_store_type_id!=6 ORDER BY license_name";
				else
					$sql ="SELECT lkup_store_type_id,license_name FROM lkup_store_types WHERE province_id =$province_id ORDER BY license_name";
			}
			$rows = $this->db->getAll($sql);
			
			return $rows;
	 }
	 
	
	function getProvinces($estate_id)
    {
     		$productID =$this->getProductId($estate_id);
     		
     		if($productID==1)
     		{
				$product_info_table="wines_info";
			}
			else
			{
				$product_info_table="beers_info";
			}
     	
			if( !F60DbUtil::checkIsBCByEstate($estate_id))//international
     		{
				$sql ="SELECT distinct p.province_id, p.short_name FROM lkup_provinces p , $product_info_table wf, $this->product_table w 
						WHERE wf.province_id = p.province_id and wf.$this->product_id_field=w.$this->product_id_field and w.estate_id=$estate_id AND wf.deleted=0 and w.deleted=0
						ORDER BY	p.province_id";
			}
			else
			{
				$estateFilter = " w.estate_id=$estate_id ";
				if($estate_id == -1)
				{
					$estateFilter = " (w.estate_id=96 or w.estate_id=97) ";
				}
				
					$sql ="SELECT distinct p.province_id, p.short_name FROM lkup_provinces p WHERE p.province_id=1 
						UNION
						SELECT distinct p.province_id, p.short_name FROM lkup_provinces p , wines_info wf, wines w 
						WHERE wf.province_id = p.province_id and wf.wine_id=w.wine_id and $estateFilter and wf.province_id=2 and wf.deleted=0 and w.deleted=0";	
				
			}
			$rows = $this->db->getAll($sql);
			
			return $rows;
	 }
	 
	 function checkProvinces($estate_id,$year)
    {
     		$productID =$this->getProductId($estate_id);
     		
     		if($productID==1)
     		{
				$product_info_table="wines_info";
			}
			else
			{
				$product_info_table="beers_info";
			}
     	
			if( !F60DbUtil::checkIsBCByEstate($estate_id))//international
     		{
				$sql ="SELECT distinct s.province_id FROM ssds_sales s , $this->product_table w 
						WHERE w.estate_id=$estate_id 
						and s.wine_id =w.$this->product_id_field 
						and year(s.sale_date)=$year
						";
			}
			$rows = $this->db->getAll($sql);
			
			return $rows;
	 }
	 
	function getUsers($province_id,$estate_id)
    {
     		if( !F60DbUtil::checkIsBCByEstate($estate_id))//internationl
     		{
				$sql ="SELECT user_id,concat(first_name,' ',last_name) user_name FROM users WHERE deleted=0 AND user_id!=26 AND user_level_id!=5 AND (province_id=-1 OR province_id = $province_id ) ORDER BY first_name";
			}
			else
			{
			 	if($province_id==1)
					$sql ="SELECT user_id,concat(first_name,' ',last_name) user_name FROM users WHERE user_id=7 or user_id =6 or user_id =22 or user_id =51 ORDER BY first_name"; //temperary code by stupid disition	
				else
				{
					$sql ="SELECT user_id,concat(first_name,' ',last_name) user_name FROM users WHERE deleted=0 AND user_id!=26 AND user_level_id!=5 AND (province_id=-1 OR province_id = $province_id ) ORDER BY first_name";
				}
			}
			$rows = $this->db->getAll($sql);
			
			return $rows;
	 }	 
	 
	 function getFormatDate($sDate) //yyyymmdd    return: mm/dd/yyyy
    {
        $syear=substr($sDate,0,4);
        $smonth=substr($sDate,4,2);
        $sday=substr($sDate,6,2);

        return $smonth.'/'.$sday.'/'.$syear;


    }
    
    function getProductId($estate_id)
    {
		$sql="select * from estates_products where estate_id =$estate_id";
		$rows = $this->db->getAll($sql);
		

		
		if(count($rows)==0)	
		{
			$product_id = 1;  //wine
		}
		else
			$product_id=2;  //beer
			
		if($product_id==2)
		{
			$this->product_table="beers";
			$this->product_id_field ="beer_id";
		
		}
		else
		{
			$this->product_table="wines";
			$this->product_id_field ="wine_id";
		}
		
		return $product_id;
	}

	 /*--------------------------------------------------------------------------------------------------------*/
		//dateType =1 : list by quarter date1 = year, date2 = quarter
	   //dateType =2 : not list by quarter date1= year, date2 = month
	   //dateType =0 : from date1 to date2
	 /*--------------------------------------------------------------------------------------------------------*/
	 function getDSWRData($estate_id, $date1, $date2, $dateType=2)
	 {
	  
	 
	  if($estate_id ==-1)
	  		$estate_id =96;

	  	
		if($dateType==0)
			{
				$byPeriod ="and date_format(o.delivery_date,'%Y%m%d')>='$date1' and date_format(o.delivery_date,'%Y%m%d') <='$date2'";
				$dateQuery = $byPeriod;
			}
			else if($dateType==1)
		 	{
		 	 		 $sale_year =$date1;
						
						$current_month = date(n);
						if($date1<date("Y"))
						{
							$current_month =12;
						}
					
			 	    if($date2 ==1)
			 	  	  		$byPeriod = " 1<= month(o.delivery_date) and month(o.delivery_date)<=3 ";
			 	  	 else if($date2 ==2)
			 	  	 		$byPeriod = " 4<= month(o.delivery_date) and month(o.delivery_date)<=6 ";
			 	  	 else if($date2 ==3)
			 	  	 		$byPeriod = " 7<= month(o.delivery_date) and month(o.delivery_date)<=9 ";
			 	  	 else if($date2 ==4)
			 	  	 		$byPeriod = " 10<= month(o.delivery_date) and month(o.delivery_date)<=12 ";
					else if($date2 ==-1)
					{
					 		$byPeriod = " 1<= month(o.delivery_date) and month(o.delivery_date)<=$current_month ";
					}
					$dateQuery = " and year(o.delivery_date)=$sale_year and $byPeriod";
		 	}
		 	else if($dateType==2)
		 	{
			 		$sale_year =$date1;
			 		$byPeriod = "month(o.delivery_date)=$date2";
					$dateQuery = " and year(o.delivery_date) =$sale_year and $byPeriod ";
			}
			
			
			$sql="Select e.estate_number, cm.customer_id id, date_format(o.delivery_date ,'%m-%d-%Y') delivery_date, 
					o.invoice_number invoice_number, stype.init_name store_type, cm.sub_type,cm.lkup_store_type_id,
					cm.licensee_number,
					
					oi.cspc_code sku,
					concat(oi.wine_name,' ',oi.wine_vintage) wine_name,
					oi.ordered_quantity orqt, oi.ordered_quantity/w.bottles_per_case total_cs,
					oi.price_winery whole_sale,
					oi.price_per_unit retail,
					if(cm.lkup_store_type_id=3,oi.price_per_unit, oi.price_winery ) price ,
					
					round(oi.litter_deposit *oi.ordered_quantity,2 ) deposit,

					round(round(oi.litter_deposit *oi.ordered_quantity,2 )+if(cm.lkup_store_type_id=3,oi.price_per_unit, oi.price_winery )*oi.ordered_quantity +
					if(cm.lkup_store_type_id=3,oi.price_per_unit, oi.price_winery )*oi.ordered_quantity*0.05, 2) total_amount

					
					From customers cm 
					left join customers_contacts cmc on cm.customer_id = cmc.customer_id 
					and cmc.is_primary=1, 
					orders o, estates e,order_items oi, 
					wines w,lkup_store_types stype
					
					where cm.deleted=0 
					and stype.lkup_store_type_id=cm.lkup_store_type_id 
					and o.deleted=0 
					and oi.deleted=0 
					and o.customer_id = cm.customer_id 
					and o.estate_id = e.estate_id 
				
					
					and e.estate_id =$estate_id
					and oi.order_id = o.order_id
					and oi.wine_id = w.wine_id 
					and o.lkup_order_status_id =2
				   
				   $dateQuery order by o.delivery_date asc, invoice_number asc";
				   
				   $pagedRS = & PagedDataSet::getInstance("db");
				$pagedRS->setPageSize(50000);
        		$pagedRS->setCurrentPage(1);
        
        	if (!$pagedRS->load($sql))
	      	{
	            $this->file_format_error .= "Error: Unable to get Card information.";
	            $bRet = false;
	            return false;
	       	}
		
	
			
			return $pagedRS;
			
	}
	 
	 
	 
	 /*--------------------------------------------------------------------------------------------------------*/
		//dateType =1 : list by quarter date1 = year, date2 = quarter
	   //dateType =2 : not list by quarter date1= year, date2 = month
	   //dateType =0 : from date1 to date2
	 /*--------------------------------------------------------------------------------------------------------*/
	 function getSales($estate_id, $date1, $date2, $orderBy,$order_type, $dateType=2, $store_type_id=-1, $user_id=-1,$province_id=1,$wine_id="", $vintage, $reportType=1, $pageSize = 70, $page, $isTotal=0)
	 {
	 
/*
$strq="estate: $estate_id  date1: $date1 date2: $date1 orderBy: $orderBy order_type: $order_type dateType: $dateType store_type_id: $store_type_id user_id: $user_id province_id: $province_id wine_id: $wine_id reportType: $reportType pageSize: $pageSize page: $page isTotal: $isTotal";

$fp = fopen("logs/bll_logfile.log","a");
		fputs($fp,   $strq."\n");
		fclose($fp);
		*/
	/*	$fp = fopen("logs/profit.log","a");
				fputs($fp,  $orderBy);
				fclose($fp);
	*/	
		
		$strq=" date1: $date1 date2: $date2  dateType: $dateType ";
		$listTyle=0; // sp in bc
		
		$store_type_filter ="";
		if($store_type_id!=-1)
		{
			if($store_type_id==4)
			{
				if(!F60DbUtil::checkIsBCByEstate($estate_id) && $province_id ==1)//international in bc
				{
					$store_type_filter =" and c.licensee_number=100";	
				}
				else
				  	$store_type_filter = " and c.lkup_store_type_id=$store_type_id";
			}
			else
			  	$store_type_filter = " and c.lkup_store_type_id=$store_type_id";
		}

		$productId =$this->getProductId($estate_id);		

		if($productId==2)
		{
			$product_table="beers";
			$product_name ="beer_name";
			$bottle_size_table ="lkup_beer_sizes";
			$type_table ="lkup_beer_types";
			$bottle_size_id ="lkup_beer_size_id";
			$type_id ="lkup_beer_type_id";
			$product_id_field ="beer_id";
		}
		else
		{
			$product_table="wines";
			$product_name ="wine_name";
			$product_id_field ="wine_id";
			$bottle_size_table ="lkup_bottle_sizes";
			$type_table ="lkup_wine_color_types";
			$bottle_size_id ="lkup_bottle_size_id";
			$type_id ="lkup_wine_color_type_id";
		}
			  			
		$user_filter ="";
		if($user_id!=-1)
			$user_filter = " and u.user_id=$user_id";
	
		if(F60DbUtil::checkIsBCByEstate($estate_id)&&$province_id ==1)// BC in bc
		{		 		 	
		 	$listTyle=0;
		 	
		 	if($dateType==0)
			{
				$byPeriod ="and date_format(o.delivery_date,'%Y%m%d')>='$date1' and date_format(o.delivery_date,'%Y%m%d') <='$date2'";
				$dateQuery = $byPeriod;
			}
			else if($dateType==1)
		 	{
		 	 		 $sale_year =$date1;
						
						$current_month = date(n);
						if($date1<date("Y"))
						{
							$current_month =12;
						}
					
			 	    if($date2 ==1)
			 	  	  		$byPeriod = " 1<= month(o.delivery_date) and month(o.delivery_date)<=3 ";
			 	  	 else if($date2 ==2)
			 	  	 		$byPeriod = " 4<= month(o.delivery_date) and month(o.delivery_date)<=6 ";
			 	  	 else if($date2 ==3)
			 	  	 		$byPeriod = " 7<= month(o.delivery_date) and month(o.delivery_date)<=9 ";
			 	  	 else if($date2 ==4)
			 	  	 		$byPeriod = " 10<= month(o.delivery_date) and month(o.delivery_date)<=12 ";
					else if($date2 ==-1)
					{
					 		$byPeriod = " 1<= month(o.delivery_date) and month(o.delivery_date)<=$current_month ";
					}
					$dateQuery = " and year(o.delivery_date)=$sale_year and $byPeriod";
		 	}
		 	else if($dateType==2)
		 	{
			 		$sale_year =$date1;
			 		$byPeriod = "month(o.delivery_date)=$date2";
					$dateQuery = " and year(o.delivery_date) =$sale_year and $byPeriod ";
			}
		}
		else
		{	
			$listTyle=1;
	
		 	$sale_year =$date1;
		 	
		 		if($estate_id ==141)
		 			$sale_year =2016;
		 			
			if($dateType==1)
			{
			 	$current_month = date(n);
				if($date1<date("Y"))
				{
					$current_month =12;
				}
			
				 if($date2 ==1)
				  		$byPeriod = " 1<= month(s.sale_date) and month(s.sale_date)<=3 ";
				 else if($date2 ==2)
				 		$byPeriod = " 4<= month(s.sale_date) and month(s.sale_date)<=6 ";
				 else if($date2 ==3)
				 		$byPeriod = " 7<= month(s.sale_date) and month(s.sale_date)<=9 ";
				 else if($date2 ==4)
				 		$byPeriod = " 10<= month(s.sale_date) and month(s.sale_date)<=12 ";
				else if($date2==-1)
				{
				 		$byPeriod = " 1<= month(s.sale_date) and month(s.sale_date)<=$current_month ";
				}			  		
			}
			else if($dateType==2)
			{
				$byPeriod= "month(s.sale_date)=$date2";					
			}
			
			$dateQuery = " and year(s.sale_date) =$sale_year and $byPeriod ";
		}

				
		$order_type =($order_type == "d")?"DESC":"ASC";
		
		if($orderBy=="delivery_date")
		{
			$orderBy = "delivery_date ".$order_type.", invoice_number asc";
			
			$order_type ="";
		}
			
		if($listTyle==1) // sp in ab or International sp in bc
		{	
			$wine_filter ="";
			if($wine_id!=-1)
			{
					$wine_filter= " and s.skua = $wine_id"	;
			}
			
			if($estate_id ==-1)
			{
				$sql = "SELECT  s.licensee_no licensee_number, l.license_name,  c.customer_name, c.billing_address_city as city,
		                concat_ws(\" \", concat_ws(\"-\", c.billing_address_unit, c.billing_address_street_number), c.billing_address_street) as address, 
		                s.SKUA cspc, concat(w.wine_name) wine_name, b.size_value as liters, cl.caption as type,
	                    if(ISNULL(u.first_name), 'Not Assgined', concat(u.first_name,' ', u.last_name)) user_name,
						sum(unit_sales) as btl_sold, sum(s.cases_sold) as cases_sold, 
	                    sum(unit_sales * s.price_per_unit) as total_amount
                
	                    From ssds_sales s
	                    
	                    inner join customers c on c.customer_id = s.customer_id
	                    inner join lkup_store_types l on c.lkup_store_type_id = l.lkup_store_type_id
	                    inner join $product_table w on w.$product_id_field = s.wine_id and s.product_id =$productId
	                    inner join $bottle_size_table b on w.$bottle_size_id = b.$bottle_size_id
	
	                    inner join $type_table cl on w.$type_id = cl.$type_id
	                    left outer join users u on s.user_id=u.user_id
	                    where s.customer_id <>0 and s.wine_id<>0 
	                    and (w.estate_id =97 or w.estate_id =96)
	                    $wine_filter
	                    and s.licensee_no<>0
						$dateQuery
	                    $store_type_filter
	                    and s.province_id = $province_id
	                    and s.cases_sold>0
	                    
	                    
	                   
	                    group by s.customer_id, s.skua
	                    order by $orderBy $order_type
                 ";
                     
               $sqlTotal= "SELECT 
						  sum(unit_sales) as btl_sold,
		                    sum(cases_sold) as cases_sold, 
		                    sum(unit_sales * s.price_per_unit) as total_amount
		                    
		                    from ssds_sales s
		                    
		                    inner join customers c on c.customer_id = s.customer_id
		                    inner join $product_table w on w.$product_id_field = s.wine_id and s.product_id =$productId
		
		                    
		                    where s.customer_id <>0 and s.wine_id<>0 
		                    and s.licensee_no<>0
							and (w.estate_id =97 or w.estate_id =96)
							$wine_filter
							$dateQuery
		                    $store_type_filter
		                    and s.province_id = $province_id
		                   
		                    group by w.estate_id
		                    order by customer_name
						  
                    
                     ";
				}
				else
				{
				 
				 		
			
					$sql = "SELECT  s.licensee_no licensee_number, l.license_name,  c.customer_name, c.billing_address_city as city,
			                concat_ws(\" \", concat_ws(\"-\", c.billing_address_unit, c.billing_address_street_number), c.billing_address_street) as address, 
			                s.SKUA cspc, concat(w.$product_name) wine_name, b.size_value as liters, cl.caption as type,
		                    if(ISNULL(u.first_name), 'Not Assgined', concat(u.first_name,' ', u.last_name)) user_name,
								  sum(s.unit_sales) as btl_sold,
		                    sum(s.cases_sold) as cases_sold, 
		                    sum(s.unit_sales * s.price_per_unit) as total_amount
		                    
		                    from ssds_sales s
		                    
		                    inner join customers c on c.customer_id = s.customer_id
		                    inner join lkup_store_types l on c.lkup_store_type_id = l.lkup_store_type_id
		                    inner join $product_table w on w.$product_id_field = s.wine_id and s.product_id =$productId
		                    inner join $bottle_size_table b on w.$bottle_size_id = b.$bottle_size_id
		
		                    inner join $type_table cl on w.$type_id = cl.$type_id
		                    left outer join users u on s.user_id=u.user_id
		                    where s.customer_id <>0 and s.wine_id<>0 
		                    and w.estate_id =$estate_id
		                    $wine_filter
		                    and s.licensee_no<>0
								  $dateQuery
		                    $store_type_filter
		                    and s.province_id = $province_id
		                    and s.cases_sold>0
		                   
		                    group by s.customer_id, s.skua
		                    order by $orderBy $order_type";
                     
               $sqlTotal= "SELECT 
							sum(unit_sales) as btl_sold,
							sum(cases_sold) as cases_sold, 
							sum(unit_sales * s.price_per_unit) as total_amount
                    
		                    From ssds_sales s
		                    
		                    inner join customers c on c.customer_id = s.customer_id
		                    inner join $product_table w on w.$product_id_field = s.wine_id and s.product_id =$productId
		
		                    
		                    where s.customer_id <>0 and s.wine_id<>0 
		                    and s.licensee_no<>0
								  and w.estate_id=$estate_id
								  $wine_filter
								  $dateQuery
		                    $store_type_filter
		                    and s.province_id = $province_id
		                   
		                    group by w.estate_id
		                    order by customer_name";
            }
		}
		else 
		{
			 
		 	if( intval($estate_id) != -1)
		 	{
		 		$estateFilter =" and o.estate_id =$estate_id ";	
		 	}
		 	else
		 		$estateFilter = " and (o.estate_id =96 or o.estate_id =97)";
		 		
		 	$wine_filter ="";
			if($wine_id!=-1)
			{
			 	if($vintage==-1)
					$wine_filter= " and oi.cspc_code = $wine_id";
				else
						$wine_filter= " and oi.cspc_code = $wine_id and oi.wine_vintage = $vintage ";
			}
				
		 	if($orderBy=="")
		 	{
				$orderBy = "o.delivery_date";
			}
			
			// new_amount affect on 2015-4-1
			
			$sql="SELECT o.order_id,o.delivery_date, o.lkup_order_status_id, 
					
					lktp.license_name, c.licensee_number, c.customer_name, c.billing_address_city as city,
					concat_ws(\" \", concat_ws(\"-\", c.billing_address_unit, c.billing_address_street_number), c.billing_address_street) as address, 
					o.delivery_date as order_date, invoice_number, l.caption as payment_status,
					os.caption as order_status, 
					sum(oi.ordered_quantity) btl_sold,
					round(sum(oi.ordered_quantity/w.bottles_per_case),2) cases_sold,
					sum(oi.price_per_unit * oi.ordered_quantity)+ if(o.deposit>0, o.deposit, sum(oi.litter_deposit * oi.ordered_quantity))
					-(IFNULL(o.agency_LRS_factor,0) * sum(oi.price_per_unit * oi.ordered_quantity))- IFNULL(o.adjustment_1, 0.0)  - IFNULL(o.adjustment_2, 0.0)as total_amount,
					
					  if(o.lkup_store_type_id=3, sum(oi.price_per_unit * oi.ordered_quantity) +sum(oi.litter_deposit * oi.ordered_quantity) +sum(oi.price_per_unit * oi.ordered_quantity)*0.05,
      sum(oi.price_winery * oi.ordered_quantity) +sum(oi.litter_deposit * oi.ordered_quantity) +sum(oi.price_winery * oi.ordered_quantity)*0.05) new_amount,
      
					concat(u.first_name,' ', u.last_name) user_name
					FROM orders o
                    inner join lkup_order_payment_status l on 
                    o.lkup_payment_status_id= l.lkup_payment_status_id
                    inner join customers c on c.customer_id = o.customer_id
                    inner join users_customers uc on uc.customer_id = c.customer_id
                    inner join users u on u.user_id =uc.user_id
                    inner join lkup_store_types lktp on c.lkup_store_type_id = lktp.lkup_store_type_id
                    inner join lkup_order_statuses os on 
                    o.lkup_order_status_id= os.lkup_order_status_id
                    inner join order_items oi on o.order_id = oi.order_id
                    inner join wines w on w.wine_id = oi.wine_id
                    $wine_filter 

                    where o.deleted = 0 
							 $estateFilter
							 $store_type_filter
							 $user_filter
                    $dateQuery
                    group by o.order_id
                    order by $orderBy $order_type";
                            
               $sqlTotal="SELECT	 sum(oi.ordered_quantity) btl_sold,		 sum(oi.ordered_quantity)/w.bottles_per_case cases_sold,
									 sum(oi.price_per_unit * oi.ordered_quantity) + if(o.deposit>0, o.deposit, sum(oi.litter_deposit * oi.ordered_quantity))
                            -(o.agency_LRS_factor * sum(oi.price_per_unit * oi.ordered_quantity))
                            - IFNULL(o.adjustment_1, 0.0)  - IFNULL(o.adjustment_2, 0.0)as total_amount,
                            
                            if(o.lkup_store_type_id=3, sum(oi.price_per_unit * oi.ordered_quantity)*(1+1*oi.litter_deposit+0.05),
      sum(oi.price_winery * oi.ordered_quantity)*(1+1*oi.litter_deposit+0.05)) new_amount
      
									 FROM orders o
                            inner join lkup_order_payment_status l on 
                            o.lkup_payment_status_id= l.lkup_payment_status_id
                            inner join customers c on c.customer_id = o.customer_id
                            inner join users_customers uc on uc.customer_id = c.customer_id
                            inner join users u on u.user_id =uc.user_id
                            inner join lkup_store_types lktp on c.lkup_store_type_id = lktp.lkup_store_type_id
                            inner join lkup_order_statuses os on 
                            o.lkup_order_status_id= os.lkup_order_status_id
                            inner join order_items oi on o.order_id = oi.order_id
                            inner join wines w on w.wine_id = oi.wine_id
									 $wine_filter                     

                            where o.deleted = 0 
                            and oi.deleted=0
									 $estateFilter
									 $store_type_filter
                            		 $user_filter
									 $dateQuery
                            group by o.estate_id
                            ";
			}
		 
		 	$pagedRS = & PagedDataSet::getInstance("db");
		 
			$pagedRS->setPageSize($pageSize);
	        $pagedRS->setCurrentPage($page);
	   
		     /*	$fp = fopen("logs/spsales.log","a");
		fputs($fp, $sql."\n");
		fclose($fp);*/
		
	        if (!$pagedRS->load($sql))
	        {
	            $this->file_format_error .= "Error: unable to get report data.";
	            $bRet = false;
	            return false;
	        }
	        
	        $rs["total_records"]=$pagedRS->getTotalRecordCount();
	        
	        $rs["sales_details"] = $pagedRS;
	      // print_r( $rs["sales_details"] );
	        
	        $rs["totalSales"] = $this->db->getAll($sqlTotal);
	      
			
	 	  return $rs;
	 
	 }
	 
	 function getCCInfo($estate_id=0)
	 {
//	 print $estate_id;;
	  		$estateFilter=" ";
			if($estate_id!=0)	
			{
				if($estate_id==-1)//enotecca
				{
					$estateFilter = " and (o.estate_id=96 or o.estate_id=97) ";
				}
				else
				{
					$estateFilter = " and o.estate_id=$estate_id ";	
				}
			}
	
			$sql="SELECT distinct c.customer_name customer_name, 
					c.licensee_number license_number, cc_number card_number, cc_exp_month expiry_month, cc_exp_year expiry_year ,
					lkct.caption card_type
					
 					FROM customers c, order_items od, orders o, lkup_payment_types lkct

					Where o.customer_id =c.customer_id and o.order_id=od.order_id 
					$estateFilter
					and cc_number<>''
					and c.lkup_payment_type_id!=1
					and c.lkup_payment_type_id!=2 
					and c.lkup_payment_type_id=lkct.lkup_payment_type_id
					and c.deleted =0
					
					order by c.customer_name ";
					
			$pagedRS = & PagedDataSet::getInstance("db");
			$pagedRS->setPageSize(50000);
        	$pagedRS->setCurrentPage(1);
        
        	if (!$pagedRS->load($sql))
	      {
	            $this->file_format_error .= "Error: Unable to get Card information.";
	            $bRet = false;
	            return false;
	       }
		
	
			
			return $pagedRS;
	 }

//($estate_id, $date1, $date2, $orderBy,$order_type, $dateType=2, $store_type_id=-1, $user_id=-1,$province_id=1,$wine_id="", $reportType=1, $pageSize = 70, $page, $isTotal=0)
	function getUnPaidInvoice($searchType,$searchValue, $estate_id, $orderBy,$order_type,$currentpage, $pageSize, $isStart=true)
	{
	 
	 
	 	$estateFilter = " and o.estate_id = $estate_id";
	 	if($estate_id ==-1)
	 	{
			$estateFilter = " and (o.estate_id = 96 or o.estate_id = 97) ";
		}
		
		$searchQuery="";
		
		$order_type =($order_type == "d")?"DESC":"ASC";

	
		
		$searchValue =($isStart==true)?$searchValue:"%$searchValue";
		
		$searchValue=mysql_real_escape_string($searchValue);
		switch ($searchType)
		{
			case 1: //customer
				
				$searchQuery=" And c.customer_name like '$searchValue%'";					
				break;
			case 2: //invoice number
			
				$searchQuery=" And o.invoice_number like '$searchValue%'";
				break;
			case 3: //license number
				$searchQuery=" And c.licensee_number like '$searchValue%'";
				break;
			case 4: //street name
				$searchQuery=" And ( c.billing_address_unit like '$searchValue%'
							   or c.billing_address_street_number like '$searchValue%' 
							   or c.billing_address_street like '$searchValue%' ) ";
				break;
		}
		$query_address = $this->query_address();
		$query_total_amount = $this->query_total_amount();
		
		
		$sql=" SELECT o.order_id,o.delivery_date, o.lkup_order_status_id, 					
				lktp.license_name, c.licensee_number, c.customer_name, c.billing_address_city as city,
	        	 $query_address, 
				 o.delivery_date as order_date, invoice_number, l.caption as payment_status,
                os.caption as order_status, 
						 sum(oi.ordered_quantity) btl_sold,
						 sum(oi.ordered_quantity/w.bottles_per_case) cases_sold,
						 $query_total_amount,
						 
				 if(o.lkup_store_type_id=3, sum(oi.price_per_unit * oi.ordered_quantity) +sum(oi.litter_deposit * oi.ordered_quantity) +sum(oi.price_per_unit * oi.ordered_quantity)*0.05,sum(oi.price_winery * oi.ordered_quantity) +sum(oi.litter_deposit * oi.ordered_quantity) +sum(oi.price_winery * oi.ordered_quantity)*0.05 ) new_amount,
      
      
                concat(u.first_name,' ', u.last_name) user_name
				
				FROM orders o
                inner join lkup_order_payment_status l on 
                o.lkup_payment_status_id= l.lkup_payment_status_id
                inner join customers c on c.customer_id = o.customer_id
                inner join users_customers uc on uc.customer_id = c.customer_id
                inner join users u on u.user_id =uc.user_id
                inner join lkup_store_types lktp on c.lkup_store_type_id = lktp.lkup_store_type_id
                inner join lkup_order_statuses os on 
                o.lkup_order_status_id= os.lkup_order_status_id
                inner join order_items oi on o.order_id = oi.order_id
                inner join wines w on w.wine_id = oi.wine_id
        
                where o.deleted = 0 
                and o.lkup_payment_status_id=1 
                and c.deleted =0
                and c.licensee_number!=0
                
				$estateFilter
				$searchQuery
						 
				
                group by o.order_id
                order by $orderBy $order_type";
                            
        $pagedRS = & PagedDataSet::getInstance("db");
    
		$pagedRS->setPageSize($pageSize);
		$pagedRS->setCurrentPage($currentpage);
		
		if (!$pagedRS->load($sql))
		{
		    $this->file_format_error .= "Error: unable to get report data.";
		    $bRet = false;
		    return false;
		}
		
		$rs["total_records"]=$pagedRS->getTotalRecordCount();
		
		$rs["sales_details"] = $pagedRS;
		
			
	 	return $rs;
	}
	
	function query_address()
	{
		$strQuery ="concat_ws(\" \", concat_ws(\"-\", c.billing_address_unit, c.billing_address_street_number), c.billing_address_street) as address";
		
		return $strQuery;
	}
	
	function query_total_amount()
	{
		$strQuery ="sum(oi.price_per_unit * oi.ordered_quantity)+ if(o.deposit>0, o.deposit, sum(oi.litter_deposit * oi.ordered_quantity))
                            -(IFNULL(o.agency_LRS_factor,0) * sum(oi.price_per_unit * oi.ordered_quantity))- IFNULL(o.adjustment_1, 0.0)  - IFNULL(o.adjustment_2, 0.0)as total_amount";
		
		return $strQuery;
	}
	
	function updateInvoice($order_id,$customer_id,$invoice_number,$payment_type,$user_id,$estate_id )
	{
	 	$retVal = false;
	 	
	 	$today =date("Y-m-d H:i:s");
	 	//update invoice
		$sqlUpdateInvoice="update orders set lkup_payment_status_id=2, estate_payment_type = $payment_type,estate_update_date='$today'
			  Where order_id =$order_id";
			
		$payment="";
				
		switch ($payment_type)
		{
			case 1:
				$payment="cash";
				break;
			case 2:
				$payment="cheque";
				break;
			case 3:
				$payment="credit card";
				break;
		}
		//save the note
		$note_text="inv# $invoice_number paid by $payment";
		
		$sqlInsertNote="Insert notes (when_created,when_modified,created_user_id,modified_user_id,note_text)
				values('$today','$today',$user_id,$user_id,'$note_text')";
				
		$retVal=$this->db->execute($sqlUpdateInvoice);
		
		if($retVal)
		{
	
			$retVal =$this->db->execute($sqlInsertNote);
		}
	
		if($retVal)
		{
			$insertNoteId = $this->db->lastInsertId();

			$sqlInsertCustomerNote="insert customers_notes (note_id, customer_id) values($insertNoteId,$customer_id)";
			
			$retVal =$this->db->execute($sqlInsertCustomerNote);
		}
		
		$retVal =true;
		
		if($retVal)
		{
			$this->sendInvoiceUpdateEmail($order_id,$estate_id);
		}
		
		return $retVal;
		
	}
	
	
	
	function getConfigVal($config)
    {     	
        return $this->cfg[$config];
    }
    
	function sendInvoiceUpdateEmail($order_id,$estate_id)
	{   
        
	 		$sql="SELECT o.customer_id,o.estate_name, o.customer_name,o.license_name, o.invoice_number,o.licensee_number
		 	  FROM orders o
			  WHERE order_id =$order_id
			  ";
			  
		 	$invoiceInfoRows = $this->db->getAll($sql);
		 		
	
		 	$customer_id =$invoiceInfoRows[0]["customer_id"];
		 	$sql = "select user_id from users_customers where customer_id =$customer_id";
		 	$userIDRows = $this->db->getAll($sql);
	 	
			// account owner id
			$owner_id = $userIDRows[0]["user_id"];
			
			//get estate manager address
			$arrayAddress = F60DbUtil::getManagerEmailByEstate_id($estate_id);

			$toAddress="";
			$estate_manager_id ="";
			if(count($arrayAddress)!=0)
			{
				$toAddress=$arrayAddress[0]["email1"];
				$estate_manager_id =$arrayAddress[0]["user_id"];
			}
			
			/*if($estate_manager_id!=$owner_id)
			{
				$ownerAddress = F60DbUtil::getUserEmailAddress($owner_id);
				$toAddress .= ";".$ownerAddress.";";
			}*/
			
			
			$ownerAddress = F60DbUtil::getUserEmailAddress($owner_id);
			$toAddress = $ownerAddress.";";
			
			
						
			// office BCC list 
			$bccAddress = $this->getConfigVal("BCC_EMAIL_RECEPIENTS");
								
			// Email from address
			$fromAddress=$this->getConfigVal("EMAIL_FROM_ADDRESS");
						
			$currentDate = date("F d, Y");

			$estate_name 	= $invoiceInfoRows[0]["estate_name"];
			$customer_name 	= $invoiceInfoRows[0]["customer_name"];
			$invoice_number = $invoiceInfoRows[0]["invoice_number"];
			$store_type 	= $invoiceInfoRows[0]["license_name"];
			$licensee_number = $invoiceInfoRows[0]["licensee_number"];
			
			//Email subject
			$emailSubject = $this->getConfigVal("EMAIL_INOVICE_UPDATE_SUBJECT");
			$emailSubject = & F60Common::replaceToken("invoice_number", $invoice_number,$emailSubject);
					
			// Email content	
			$emailContent = $this->getConfigVal("INVOICE_UPDATE_EMAIL_CONTENT");		
			$emailContent = & F60Common::replaceToken("customer_name",$customer_name,$emailContent);
			$emailContent = & F60Common::replaceToken("current_date",$currentDate,$emailContent);
			$emailContent = & F60Common::replaceToken("store_type",$store_type,$emailContent);
			$emailContent = & F60Common::replaceToken("licensee_number",$licensee_number,$emailContent);
			$emailContent = & F60Common::replaceToken("invoice_number",$invoice_number,$emailContent);
			$emailContent = & F60Common::replaceToken("estate_name",$estate_name,$emailContent);
						
			$from_name = $this->getConfigVal("EMAIL_FROM_CSWS_TITLE");
					
	        F60Common::_sendEmail($toAddress,$bccAddress,$fromAddress,$emailSubject,$emailContent,$from_name);	//$toAddress ,$bccAddress,$fromAddress $emailSubject, $emailContent	           		
	}
	
	
	function updateInvoiceStatus($order_id, $order_status) // by inner csonline user, not for estate
	{
	 	$retVal = false;
	 	
	 	$today =Date("Y-m-d");
	 	
	 	//update invoice
		$sqlUpdateInvoice="update orders set lkup_order_status_id=$order_status Where order_id =$order_id";
			
		$retVal=$this->db->execute($sqlUpdateInvoice);
	
		return $retVal;
		
	}

}

?>
