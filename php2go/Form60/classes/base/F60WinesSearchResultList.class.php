<?php

import('php2go.base.Document');
import('php2go.data.PagedDataSet');
import('php2go.util.Number');
import('Form60.util.F60Date');
import('Form60.base.F60DbUtil');
import('Form60.util.F60Common');


class F60WinesSearchResultList extends PagedDataSet
{
	var $_Document;
	var $orderBy = "total_sales";
	var $orderType = "d";

	var $templateFile = 'f60ResultsList.tpl';
	var $sortSymbols; 
	
	var $isQuarter=false;
	var $sales_period;
	var $sales_year;
	
	var $store_type;
	var $pageSize = 50;
	var $total_sales =0;
	var $total_cases = 0;
	
	var $isLoad =false;
	
	var $user_id ="";
	var $search_id ="";
	
	var $search_adt1 ="";
	var $search_adt2 ="";
	var $pages=1;
	var $search_key="";
	var $wine_type="";
	var $sales_date_txt="";
	var $city="";
	var $totalRecs=0;
	var $cspc_code="";
	var $product_id=1;

	var $product_type_id=1;
	
	/*
	
	searchid = 0 : option 1, who had purchased product in city
	*/
	
   function F60WinesSearchResultList($Document, $product_id,$search_id,$sales_period,$sales_year,$isQuarter,$store_type_id,$user_id,$search_adt1="",$search_adt2="",$city="", $page =1,$isPrint=0)
	{
	 


		PagedDataSet::PagedDataSet('db');
        
		if ($Document)
		{
			$Document->addScript('resources/js/javascript.f60search.js');
			$Document->addScript('resources/js/jquery-latest.js');
			$Document->addScript('resources/js/jquery.tablesorter.js');
			$this->_Document = $Document;
		}
		if($search_id ==0)
		{
			$this->cspc_code = $search_adt1;
			$this->city = $city;
		}
		
		if($search_id ==0 && $search_adt2==2)
		{
			$this->orderBy="customer_name";
			$this->orderType="a";				
		}
			
		
				
		$this->sales_year = $sales_year;
		$this->sales_period = $sales_period;
		$this->store_type = $store_type_id;
		$this->search_id = $search_id;
		$this->user_id = $user_id;
		$this->search_adt1=$search_adt1;
		$this->search_adt2=$search_adt2;
		
		$this->product_id=$product_id;
		
		$this->product_type_id=$product_id;  // separate sake, spirits from beer, because they are all in beer table
		
		if($this->product_id >2)
			$this->product_id =2;
    
		if($search_id==1) // top customers
		{
			$this->pageSize = intval($search_adt2);		
			$this->cspc_code = $search_adt1;
			$this->templateFile = 'f60ResultsList_topCM.tpl';  // implement sort in current page feature
		}
		else if($search_id==2) // top wines
		{
			if($search_adt1==-1)
			{
				$this->pageSize = 50;		   	 	
				$this->templateFile = 'f60winesResultsList_all.tpl';  	 		
			}
			else
			{
				$this->pageSize = intval($search_adt1);		   	 	
				$this->templateFile = 'f60winesResultsList.tpl';  	 	
			}
			
			$this->wine_type = intval($search_adt2);	
			$this->templateFile = 'f60winesResultsList.tpl';  	 		
		}
		else if($search_id==3)
		{
			$this->templateFile = 'sf60_wines_sales_total.tpl';  	 
		}
		
		if($search_id==0 or ($search_id ==2&&$search_adt1==-1))
		{
			if($isPrint)
			$this->pageSize =700;
		}
	

		
		
		PagedDataSet::setPageSize($this->pageSize);
		
		$this->templateFile = TEMPLATE_PATH. $this->templateFile;
		
		$this->Template =& new Template($this->templateFile); 
		
		if ($this->getConfigVal('TEMPLATE_CACHE', FALSE))
		   $this->Template->setCacheProperties($this->getConfigVal('FORM60_CACHE_PATH'));
		
		$this->Template->parse();
		
		$this->province_id = $_COOKIE["F60_PROVINCE_ID"];
		
		$this->sortSymbols = & F60Common::sortSymbols();
		
		if ( $isQuarter==1)
			$this->isQuarter=true;
		
		$this->page =$page;		    
		
	
		
    }
    
	function _loadDataset()
	{
		$this->LoadSales();			
	}
	
	function LoadSales()
	{
	 
		$sortBy = ($this->orderType == "a")?" ASC":" DESC";		
		$store_type_filter = "";
		$user_filter="";
		$user_tables="";
		
		
		if($this->search_id==0&&$this->search_adt2==2)
		{	
			if ($this->store_type!=-1)
			{
				if($this->store_type==-2)
					$store_type_filter = " and c.lkup_store_type_id <>6 ";
				else
					$store_type_filter = " and c.lkup_store_type_id = $this->store_type ";
			}
				
			if ($this->user_id!=-1)
			{
			 	if ( intval($this->user_id) ==0)				
					$user_filter = " and u.user_id IS NULL ";				
				else
					$user_filter = " and u.user_id = $this->user_id";	
			}
		}
		else
		{		
			if ($this->store_type!=-1)
			{
				if($this->store_type==-2) // all except BCLDB
					$store_type_filter = " and c.lkup_store_type_id <> 6 "	;
				else
					$store_type_filter = " and c.lkup_store_type_id = $this->store_type "	;
			}
			
			if ($this->user_id!=-1)
			{		
			 	if($_COOKIE["F60_PROVINCE_ID"] ==1)
			 	{
			 	
					if($this->search_id==3 or $this->search_id==2)
						$user_filter = " and s.user_id = $this->user_id "	;
					else 
						$user_filter = " and u.user_id = $this->user_id "	;
				}
				else
					$user_filter = " and s.user_id = $this->user_id and u.user_id =$this->user_id  "	;
			}
		}
		
		$searchSquery="";
		$group_by ="";
		$orderby="";
		$city_filter ="";
		$isBCEstate =1; // bc estate
		
		if($this->search_id == 0 ) //purchased or not
		{
		 	if($this->search_adt1!="")
		 	{		 		
		 		$f60dbutil= new F60DbUtil();
			
				$isBCEstate = $f60dbutil->isBCEstate($this->search_adt1);//by cspc
			
				if($isBCEstate ==1|| $this->province_id ==2)
				{
						$searchSquery = " and s.skua= $this->search_adt1 ";
				}
				else
				{
					$searchSquery = " and odit.cspc_code= $this->search_adt1 ";
				}
		 	}
			
		 	if($this->city!="")
		 		$city_filter = " and c.billing_address_city like '%$this->city%' ";
		 		
			$group_by=" group by c.customer_id ";
		}
		
		if($this->search_id == 1 ) // top customers
		{
		 	if($this->search_adt1!="")
		 		$searchSquery = " and s.skua= $this->search_adt1 ";
			
			$group_by=" group by c.customer_id ";
		}
		
		if($this->search_id == 2) // top wines
		{
			$group_by=" group by s.skua ";
			$wine_type_filter="";
			if($this->wine_type!=-1)
			{
				if($this->product_id==1)
					$wine_type_filter =" and lkcl.lkup_wine_color_type_id =$this->wine_type";
				else
	
					$wine_type_filter =" and lkcl.lkup_beer_type_id =$this->wine_type";  //beer update
			
			}
		
		}
	  	if($this->search_id == 3 )// wine total sales
		{
			if($this->search_adt1!="")
			$searchSquery = " and s.skua= $this->search_adt1 ";
		}
		
	
		
		if($this->sales_year!=-1)
		{
		 	
			$current_month = date(n);
			if($this->sales_year<date("Y"))
			{
				$current_month =12;
			}
			$byPeriod="";
			
		
			$this->sales_date_txt = "";
			$year_txt=$this->sales_year;
			$period_txt="";
			
			
			if($isBCEstate==0 && $this->province_id==1)
			{
				
				if($this->isQuarter)
				{
					 if($this->sales_period ==1)
					 {
						$byPeriod = " and 1<= month(od.delivery_date) and month(od.delivery_date)<=3 ";
						$period_txt = "first quarter: January - March";
					 }
					 else if($this->sales_period ==2)
					 {
						$byPeriod = " and 4<= month(od.delivery_date) and month(od.delivery_date)<=6 ";
						$period_txt = "second quarter: April - June";
					 }
					 else if($this->sales_period ==3)
					 {
				 		$byPeriod = " and 7<= month(od.delivery_date) and month(od.delivery_date)<=9 ";
				 		$period_txt = "third quarter: July - September";
					 }
					 else if($this->sales_period ==4)
					 {
				 		$byPeriod = " and 10<= month(od.delivery_date) and month(od.delivery_date)<=12 ";
				 		$period_txt = "forth quarter: October - December";
					 }
					else if($this->sales_period ==-1)
					{
					 	$period_txt = " YTD ";
					}
				}
				else
				{
					$byPeriod = " and month(od.delivery_date)=$this->sales_period";
					$dateInfo  = getDate(mktime(0, 0, 0, $this->sales_period ,1, date("Y")));
					$period_txt = $dateInfo["month"];
				}
				$this->sales_date_txt= $year_txt.' '.$period_txt.' total sales: ';
				
				$byPeriod = $byPeriod." and year(od.delivery_date)=$this->sales_year ";
			}
			else
			{
			 
				if($this->isQuarter)
				{
				 
					 if($this->sales_period ==1)
					 {
				  		$byPeriod = " and 1<= month(s.sale_date) and month(s.sale_date)<=3 ";
				  		$period_txt = "first quarter: January - March";
					 }
					 else if($this->sales_period ==2)
					 {
				 		$byPeriod = " and 4<= month(s.sale_date) and month(s.sale_date)<=6 ";
				 		$period_txt = "second quarter: April - June";
					 }
					 else if($this->sales_period ==3)
					 {
				 		$byPeriod = " and 7<= month(s.sale_date) and month(s.sale_date)<=9 ";
				 		$period_txt = "third quarter: July - September";
					 }
					 else if($this->sales_period ==4)
					 {
				 		$byPeriod = " and 10<= month(s.sale_date) and month(s.sale_date)<=12 ";
				 		$period_txt = "forth quarter: October - December";
					 }
					else if($this->sales_period ==-1)
					{
					 	$period_txt = " YTD ";
					}
				}
				else
				{
				 	
					$byPeriod = " and month(s.sale_date)=$this->sales_period";
					$dateInfo  = getDate(mktime(0, 0, 0, $this->sales_period ,1, date("Y")));
					$period_txt = $dateInfo["month"];
				}
				$this->sales_date_txt= $year_txt.' '.$period_txt.' total sales: ';
				
				$byPeriod = $byPeriod." and year(s.sale_date)=$this->sales_year ";
			}
			
		
		}
		else
		{
		
			$this->sales_date_txt= "All vintages";
				
				$byPeriod ="";
		}			
			
		if($this->search_id<2)
		{
			if($this->search_id==0&&$this->search_adt2==2)
			{
				$phone_column = 'CASE WHEN c.lkup_phone_type_id =1 THEN c.phone_office1
								WHEN c.lkup_phone_type_id =2 THEN c.phone_other1
								ELSE c.phone_fax
					 			END';
								$contact_number ="CONCAT_WS('.', SUBSTRING((" . $phone_column . "), 1, 3), SUBSTRING( (" . $phone_column . ") , 4, 3), SUBSTRING( (" . $phone_column . ") , 7))";
								$contact_number7 ="CONCAT_WS('.', SUBSTRING((" . $phone_column . "), 1, 3), SUBSTRING( (" . $phone_column . ") , 4))";
								$fax_number ="IFNULL(CONCAT_WS('.', SUBSTRING(c.phone_fax,1,3),SUBSTRING(c.phone_fax,4,3),SUBSTRING(c.phone_fax,7)),'')";
									$fax_number7 ="IFNULL(CONCAT_WS('.', SUBSTRING(c.phone_fax,1,3),SUBSTRING(c.phone_fax,4)),'')";

				$sqlTemplate="SELECT s.customer_id, c.customer_name customer_name, 
								concat_ws('', concat_ws('-', c.billing_address_unit, c.billing_address_street_number),
								c.billing_address_street,' ',c.billing_address_city) as address, 
								
								c.licensee_number licensee_no, 
								l.license_name store_type,
								concat(IFNULL(u.first_name,'Not'),' ',IFNULL(u.last_name,'Assgined')) user_name,
								case length($phone_column)
										when 0 then ''
										when 7 then $contact_number7
										else $contact_number end  contact_number,
								
								concat(IFNULL(ct.first_name,''),' ',IFNULL(ct.last_name,'')) contact_name
								
								FROM lkup_store_types l,
								customers  c left outer join  ssds_sales s on  s.customer_id = c.customer_id 
								$searchSquery
								$byPeriod
								left join customers_contacts cmc on c.customer_id = cmc.customer_id and cmc.is_primary=1
								left join contacts ct on ct.contact_id = cmc.contact_id 	and ct.deleted=0
								
								left outer join users_customers uc on uc.customer_id = c.customer_id and uc.deleted =0
								left outer join users u on u.user_id = uc.user_id										
								WHERE s.customer_id IS NULL								
								$store_type_filter 
								$user_filter
								$city_filter
								and l.province_id = $this->province_id
								and l.lkup_store_type_id=c.lkup_store_type_id
								and c.deleted=0";
							//	and c.status!=2 may be it is ok for just display the sales but not calculate the commission
			}
			else
			{
			 	$productfileter="";
			 	
			 	if($this->search_id==1)
			 	{
			    	if($this->product_type_id==2)
			   		{
							$productfileter = " and w.lkup_beer_type_id < 200"; // sake
					}
					if($this->product_type_id==3)
			   		{
							$productfileter = " and w.lkup_beer_type_id = 200"; // sake
					}
					else if($this->product_type_id==4) // gin and vodka
					{
							$productfileter = " and w.lkup_beer_type_id > 200"; // sake
					}
				}
			
			   		
			  //check estate //beer update
			  if($isBCEstate==1 || $this->province_id ==2)
			  {
			   	if($this->product_id==1)
			   	{
			   		$product_table="wines";
			   		$vintageFeild="w.vintage,";
			   		$product_name="wine_name";
			   		$prd_id="wine_id";
			   	}
			   	else
			   	{
			   	   
			   		$product_table="beers";
			   		$vintageFeild="0 vintage,";
			   		$product_name="beer_name";
			   		$prd_id="beer_id";
			   		
			   	
			   	}
			   	

			   		//	and c.status!=2 may be it is ok to remove this condition since it's only dispaly the sales but not calculate the sales'
				$sqlTemplate="SELECT s.customer_id, c.customer_name customer_name, 
								concat_ws('', concat_ws('-', c.billing_address_unit, c.billing_address_street_number),
								c.billing_address_street,' ',c.billing_address_city) as address, 
					
								s.licensee_no licensee_no, 
								l.license_name store_type,
				       
								round(sum(unit_sales/w.bottles_per_case),2) as total_cases,
								sum(unit_sales * s.price_per_unit) as total_sales,
								sum(unit_sales * s.price_winery) as wh_sales,
								concat(u.first_name, ' ', u.last_name) user_name,
								s.skua,
								$vintageFeild w.$product_name
								from ssds_sales s
								inner join customers c on c.customer_id = s.customer_id
								inner join lkup_store_types l on c.lkup_store_type_id = l.lkup_store_type_id
								left outer join users_customers uc on uc.customer_id = c.customer_id and uc.deleted=0
								left outer join users u on u.user_id = uc.user_id
								inner join $product_table w on w.$prd_id =s.wine_id and s.product_id =$this->product_id
								where s.customer_id <>0 and s.wine_id<>0 
								and c.deleted=0
						
								and s.province_id = $this->province_id	
								$productfileter
									                
								$byPeriod
								$store_type_filter 
								$user_filter
								$city_filter
								$searchSquery
								$group_by ";
				}
				else if($isBCEstate==0 && $this->province_id ==1) // updated by Helen for mysql4 to mysql5
			  	{
					$sqlTemplate= "SELECT  od.customer_id,od.customer_name, 
								   concat_ws('', concat_ws('-', c.billing_address_unit, c.billing_address_street_number),
								   c.billing_address_street,' ',c.billing_address_city) as address, 
								   c.licensee_number licensee_no,
								   lkst.license_name  store_type,
									
								   sum(odit.ordered_quantity/w.bottles_per_case) total_cases, 
								   sum(ordered_quantity * odit.price_per_unit) as total_sales,
								   sum(ordered_quantity * odit.price_winery) as wh_sales,
									
								   concat(u.first_name, ' ', u.last_name) user_name, 
								   w.cspc_code,
								   w.wine_name, w.vintage
									
								   FROM `order_items`  odit,  wines w,estates e,lkup_bottle_sizes lkbt,customers c,lkup_store_types lkst, users u,orders od 
								   left join users_customers uc on uc.customer_id = od.customer_id and uc.deleted=0
								   Where 
								   odit.order_id=od.order_id 
								   and u.user_id = uc.user_id
								   and w.wine_id=odit.wine_id
								   and w.estate_id = e.estate_id
								   and w.lkup_bottle_size_id = lkbt.lkup_bottle_size_id
								   and c.customer_id = od.customer_id and c.lkup_store_type_id = lkst.lkup_store_type_id
								   and od.deleted=0 and (odit.deleted =0 and odit.ordered_quantity<>0)									
								   $byPeriod
								   $store_type_filter 
								   $user_filter
								   $city_filter
								   $searchSquery
								   $group_by";       
			}
		}
	}//if search_id<2
	else if($this->search_id ==2)
	{
	 

	 
	 
	 	if($store_type_filter!="")
	 	{
			if($this->store_type==-2)
				$store_type_filter = " and s.lkup_store_type_id <>6 ";
			else
				$store_type_filter = " and s.lkup_store_type_id = $this->store_type ";
		}
					
		$productFilter ="";
		if($this->product_id==1)
	   	{
	   		$product_table="wines";
	   		$vintageFeild="w.vintage,";
	   		$product_type_table="lkup_wine_color_types";
	   		
	   		$product_type_id_field="lkup_wine_color_type_id";
	   		
	   		$product_size_table="lkup_bottle_sizes";
	   		
	   		$product_size_id_field="lkup_bottle_size_id";
	   		
	   		$product_name="wine_name";
	   		$product_id="wine_id";
	   	}
	   	else
	   	{
	   		$product_table="beers";
	   		$vintageFeild="";
	   		$product_type_table="lkup_beer_types";
	   		
	   		$product_type_id_field="lkup_beer_type_id";
	   		
			$product_size_table="lkup_beer_sizes";
			
			$product_size_id_field="lkup_beer_size_id";
			
			$product_name="beer_name";
			$product_id="beer_id";
			
			$productFilter=" and lkcl.lkup_beer_type_id<200";
			
			
			if($this->product_type_id==3)
			{
				$productFilter=" and lkcl.lkup_beer_type_id=200";
				$wine_type_filter="";
			}
			else if($this->product_type_id==4)
			{
				$productFilter=" and lkcl.lkup_beer_type_id>200";
				$wine_type_filter="";
			}
	   	}
			   		
		
		$sqlTemplate="SELECT e.estate_name,w.$product_name wine_name, s.skua sku, sum(cases_sold) total_cases, sum(unit_sales*s.price_per_unit) total_sales,
							sum(unit_sales*s.price_winery) wh_sales,
							lkcl.display_name color,
							lkbs.display_name btl_size
							
							FROM `ssds_sales` s, $product_table w,
							$product_type_table lkcl,
							$product_size_table lkbs, estates e
																	
							where w.$product_id=s.wine_id and s.product_id =$this->product_id
							and w.estate_id =e.estate_id
							and w.$product_type_id_field = lkcl.$product_type_id_field
							AND w.$product_size_id_field =lkbs.$product_size_id_field
							
							and s.province_id = $this->province_id
		    				$productFilter
		    				
							$byPeriod
							$wine_type_filter 
							$store_type_filter 
							$user_filter
			
							group by s.skua									
						";
	}
	else if($this->search_id ==3) //total sales
	{
	 
	 	if($this->province_id ==1)
	 	{
			$sample_license=100; // BC store licensee number where we purchased our sample
		}
		else
		{
			$sample_license=30028400;	// AB store licensee number where we purchased our sample
		}
		
	 	if($this->product_id==1)
	   	{
	   		$product_table="wines";
	   		$vintageFeild="w.vintage,";
	   		$product_type_table="lkup_wine_color_types";
	   		
	   		$product_type_id_field="lkup_wine_color_type_id";
	   		
	   		$product_size_table="lkup_bottle_sizes";
	   		
	   		$product_size_id_field="lkup_bottle_size_id";
	   		
	   		$is_inernational_field ="w.is_international,";
	   		
	   		$product_name="wine_name";
	   		$product_id="wine_id";
	   	}
	   	else
	   	{
	   		$product_table="beers";
	   		$vintageFeild="0000 vintage";
	   		$product_type_table="lkup_beer_types";
	   		
	   		$product_type_id_field="lkup_beer_type_id";
	   		
			$product_size_table="lkup_beer_sizes";
			
			$product_size_id_field="lkup_beer_size_id";
			$is_inernational_field ="0 is_international,";
			
			$product_name="beer_name";
			$product_id="beer_id";
	   	}
	   	
		$sqlTemplate="select e.estate_name,w.$product_name wine_name, lkbs.display_name btl_size, sum(unit_sales)/w.bottles_per_case total_cases, 
						sum(unit_sales*s.price_per_unit) total_sales, sum(unit_sales*s.price_winery) total_sales_wh,
						lkcl.display_name color, lkbs.display_name btl_size, s.lkup_store_type_id store_type, $is_inernational_field s.licensee_no								
						FROM `ssds_sales` s, $product_table w, $product_type_table lkcl, $product_size_table lkbs, estates e 
						where w.$product_id=s.wine_id and s.product_id =$this->product_id
						and w.estate_id =e.estate_id
						and w.$product_type_id_field = lkcl.$product_type_id_field
						AND w.$product_size_id_field =lkbs.$product_size_id_field
						and s.licensee_No<>$sample_license										
						and s.province_id = $this->province_id
						and s.licensee_No<>0
						
						$byPeriod
						$searchSquery
						$user_filter					
							
						group by s.lkup_store_type_id														
						union
						select e.estate_name,w.$product_name, lkbs.display_name btl_size, sum(unit_sales)/w.bottles_per_case total_cases, 
						sum(unit_sales*s.price_per_unit) total_sales, sum(unit_sales*s.price_winery) total_sales_wh,
						lkcl.display_name color, lkbs.display_name btl_size, s.lkup_store_type_id store_type, $is_inernational_field s.licensee_no										
						FROM `ssds_sales` s, $product_table w, $product_type_table lkcl, $product_size_table lkbs, estates e 
						where w.$product_id=s.wine_id and s.product_id =$this->product_id
						and w.estate_id =e.estate_id
						and w.$product_type_id_field = lkcl.$product_type_id_field
						AND w.$product_size_id_field =lkbs.$product_size_id_field
						and s.licensee_No=$sample_license										
						and s.licensee_No<>0
						and s.province_id = $this->province_id
						
						$byPeriod
						$searchSquery						
						$user_filter					
							
						group by s.lkup_store_type_id";
	}//end if search_id==3
	
	//print $sqlTemplate;				
		if($this->search_id !=3)
		{
		 	if($this->search_id ==1)
		 	{
				$sortType = ($this->orderType == "d")?" DESC":" ASC";		    
				$sqlCode= $sqlTemplate." order by total_sales DESC, ".$this->orderBy.$sortType;
			}
			else
			{	$sortType = ($this->orderType == "d")?" DESC":" ASC";		    
				$sqlCode= $sqlTemplate." order by ".$this->orderBy.$sortType;
			}
		}
		else
			$sqlCode = $sqlTemplate;
	

      	PagedDataSet::setCurrentPage($this->page);
      	PagedDataSet::load($sqlCode);
    
	  	$this->pages = ceil(PagedDataSet::getTotalRecordCount()/$this->pageSize);
	  	$this->totalRecs = PagedDataSet::getTotalRecordCount();   
}//function end here
    
	function _buildContent() 
	{
		$this->_loadDataset();
		

		if($this->search_id<2)		
			$this->getCmsList();
		else if($this->search_id ==2)
			$this->getWineList();		
		else if($this->search_id ==3)
			$this->getTotalList();		
			
		$this->Template->globalAssign("totalRecs", $this->totalRecs);
	}
	
	function getTotalList() 
	{ 
		$aRow = 0;	
		
		$total_sales =0;
		$total_cases =0;
		$total_sales_wh =0;
		$wine_name="";
		
		if($this->province_id ==1)
		{
			$this->Template->globalAssign("bcldb_sales", '$0.00');
			$this->Template->globalAssign("bcldb_sales_wh", '$0.00');
			$this->Template->globalAssign("bcldb_cases", '0');
			$this->Template->globalAssign("lic_sales", '$0.00');
			$this->Template->globalAssign("lic_sales_wh", '$0.00');
			$this->Template->globalAssign("lic_cases", '0');
			$this->Template->globalAssign("agency_sales", '$0.00');
			$this->Template->globalAssign("agency_sales_wh", '$0.00');
			$this->Template->globalAssign("agency_cases", '0');
			$this->Template->globalAssign("lrs_sales", '$0.00');
			$this->Template->globalAssign("lrs_sales_wh", '$0.00');
			$this->Template->globalAssign("lrs_cases", '0');
			$this->Template->globalAssign("vaq_name", 'VQA');
			$this->Template->globalAssign("vqa_sales", '$0.00');
			$this->Template->globalAssign("vqa_sales_wh", '$0.00');
			$this->Template->globalAssign("vqa_cases", '0');
			$this->Template->globalAssign("vqa_class", 'mlcellB');
			$this->Template->globalAssign("other_name", 'Private');
			$this->Template->globalAssign("other_class", 'mlcellA');
		}
		else
		{
		 	$i=1;

			for( $i=1; $i<5; $i++ )
			{
		 		$styleName="isDisplay_".$i;
		 		$this->Template->globalAssign($styleName, 'none');
			}
			$this->Template->globalAssign("vaq_name", 'Licensee'); //put alberta sales in VQA td
				
		}
		$this->Template->globalAssign("other_sales", '$0.00');
		$this->Template->globalAssign("other_sales_wh", '$0.00');
		$this->Template->globalAssign("other_cases", '0');

	   
		$nCnt =0;	
		
		$totals =0;
		
		$lrs_cs =0;
		$lic_cs =0;
		$agency_cs=0;
		$bcldb_cs=0;
		$vqa_cs=0;
		$sample_cs=0;
		$private_cs=0;
		$ab_lic_cs=0;
		$is_international=false;
		while ($lineData = PagedDataSet::fetch()) 
		{
		 	$nCnt++;
		 			 	
		 	if($nCnt==1)
		 	{
		 	  	$wine_info=$lineData["estate_name"].', '.$lineData["wine_name"].' '.$lineData["color"].' '.$lineData["btl_size"];
		 
				$this->Template->globalAssign("wine_info", $this->getCut($wine_info,5));
				$this->Template->globalAssign("sales_period", $this->sales_date_txt);
				
				if($lineData["is_international"]==1||$this->province_id==2)
				{
					$is_international=true;
				
					$this->Template->globalAssign("other_name", 'Sample');
				
					if($this->province_id ==1)
					{
						$this->Template->globalAssign("other_class", 'mlcellB');
						$this->Template->globalAssign("isDisplay_5", 'none');		
					}
				}
				
				$this->Template->globalAssign("isInt", $lineData["is_international"]);	
				
			}
			$samples_cs =0;
			if($lineData["total_sales"]!=NULL)
			{
				$total_sales= Number::fromDecimalToCurrency($lineData["total_sales"],"$", ".", ",", 2, "left");
				$total_sales_wh= Number::fromDecimalToCurrency($lineData["total_sales_wh"],"$", ".", ",", 2, "left");
			}	
	
			if( $lineData["store_type"]==1)//LRS
			{
				$this->Template->globalAssign("lrs_sales_wh", $total_sales_wh);
				$this->Template->globalAssign("lrs_sales", $total_sales);
				$this->Template->globalAssign("lrs_cases", $lineData["total_cases"]);
				$lrs_cs= $lineData["total_cases"];
				
				$totals = $totals+$lineData["total_cases"];		

			
			}
			else if( $lineData["store_type"]==2)//agency
			{
			   $this->Template->globalAssign("agency_sales", $total_sales);
			   $this->Template->globalAssign("agency_sales_wh", $total_sales_wh);
				$this->Template->globalAssign("agency_cases", $lineData["total_cases"]);
				$agency_cs= $lineData["total_cases"];
				$totals = $totals+$lineData["total_cases"];
			
			}
			else if( $lineData["store_type"]==3)//licensee
			{
 	
				$this->Template->globalAssign("lic_sales_wh", $total_sales_wh);
				$this->Template->globalAssign("lic_sales", $total_sales);
				$this->Template->globalAssign("lic_cases", $lineData["total_cases"]);
				$lic_cs= $lineData["total_cases"];
				
				$totals = $totals+$lineData["total_cases"];			
			
			}
			else if( $lineData["store_type"]==5)//vqa
			{
				$this->Template->globalAssign("vqa_sales_wh", $total_sales_wh);
				$this->Template->globalAssign("vqa_sales", $total_sales);
				$this->Template->globalAssign("vqa_cases", $lineData["total_cases"]);
				$vqa_cs= $lineData["total_cases"];
				$totals = $totals+$lineData["total_cases"];
			
			}
			else if( $lineData["store_type"]==6)//bcldb
			{
			
				if($lineData["licensee_no"]==100&&$this->province_id==1) //sample
			 	{
					$this->Template->globalAssign("other_sales_wh", $total_sales_wh);
					$this->Template->globalAssign("other_sales", $total_sales);
					$this->Template->globalAssign("other_cases", $lineData["total_cases"]);
					$samples_cs= $lineData["total_cases"];
					
					$totals = $totals+$lineData["total_cases"];
				}
				else
				{
					$this->Template->globalAssign("bcldb_sales", $total_sales);
					$this->Template->globalAssign("bcldb_sales_wh", $total_sales_wh);
					$this->Template->globalAssign("bcldb_cases", $lineData["total_cases"]);
					$bcldb_cs= $lineData["total_cases"];
					
					$totals = $totals+$lineData["total_cases"];
				}			
			}
			else if( $lineData["store_type"]==8)//alberta
			{
				$this->Template->globalAssign("other_class", 'mlcellB');
				$this->Template->globalAssign("vqa_class", 'mlcellA');
				if($lineData["licensee_no"]==30028400&&$this->province_id==2) //albera sample
			   	{
					$this->Template->globalAssign("other_sales_wh", $total_sales_wh);
					$this->Template->globalAssign("other_sales", $total_sales);
					$this->Template->globalAssign("other_cases", $lineData["total_cases"]);
					
					
					$samples_cs= $lineData["total_cases"];
					$totals = $totals+$lineData["total_cases"];
				}
				else
				{
					$this->Template->globalAssign("vqa_sales_wh", $total_sales_wh);
					$this->Template->globalAssign("vqa_sales", $total_sales);
					$this->Template->globalAssign("vqa_cases", $lineData["total_cases"]);
					
					$ab_lic_cs= $lineData["total_cases"];
					$totals = $totals+$lineData["total_cases"];	
				}
			}
			else//bc bulk
			{
			 	if($lineData["store_type"]!=0) 
			 	{
					$this->Template->globalAssign("other_sales_wh", $total_sales_wh);
					$this->Template->globalAssign("other_sales", $total_sales);
					$this->Template->globalAssign("other_cases", $lineData["total_cases"]);
					$private_cs= $lineData["total_cases"];
				
					$totals = $totals+$lineData["total_cases"];	
				}
				
			}
		}
		if($this->province_id ==1)
		{
			 if( $totals!=0)
			 {
				$lic_percent=$this->getRound($lic_cs,$totals);
				$lrs_percent=$this->getRound($lrs_cs,$totals);
			
			
				$agency_percent=$this->getRound($agency_cs,$totals);
				$bcldb_percent=$this->getRound($bcldb_cs,$totals);
				
				if($is_international)
				{
				 	//sample
					$sample_percent=$this->getRound($samples_cs,$totals);
					$this->Template->globalAssign("other_percentage", $sample_percent);
				}
				else
				{
				 	$vqa_percent=$this->getRound($vqa_cs,$totals);
				 	$private_percent=$this->getRound($private_cs,$totals);
					
					$this->Template->globalAssign("vqa_percentage", $lic_percent);
					$this->Template->globalAssign("other_percentage", $private_percent);
				}
				
				$this->Template->globalAssign("lic_percentage", $lic_percent);
				$this->Template->globalAssign("lrs_percentage", $lrs_percent);
				$this->Template->globalAssign("agency_percentage", $agency_percent);
				$this->Template->globalAssign("bcldb_percentage", $bcldb_percent);
				
			}
		}
		else
		{
			$ab_lic_percent=($ab_lic_cs/$totals)*100;
			$sample_percent=($sample_cs/$totals)*100;
			
		}
			
			
		
	}
	
	function getRound($num1, $den)
	{ 
	 	if($num1 ==0)
	 	{
			$retVal=0;
		}
		else
		{
		 	$val = $num1/$den;
			$retVal =  substr($val,0,6) ;			
			if($retVal==0&&$val>0)
			{
				$retVal = 0.0001;	
			}
			$retVal = $retVal*100;
		}
		return $retVal;
	}


	function getWineList() 
	{    		
		$aRow = 0;	
		
		$total_sales =0;
		$total_cases =0;
		$wine_name="";
		while ($lineData = PagedDataSet::fetch()) 
		{
			$aRow++;
			$this->Template->createBlock('loop_line');
			$this->Template->assign("row_style", ($aRow % 2)?"cellA":"cellB");
			$this->Template->assign("estate_name", $this->getCut($lineData["estate_name"],15));

			if($this->product_id==1)
			{	
				$wine_name = $lineData["wine_name"];
				$wine_name=str_replace('- okan','',$wine_name);
				$wine_name=str_replace('- Okan','',$wine_name);
				$wine_name=str_replace('-okan','',$wine_name);
				$wine_name=str_replace('- Vic','',$wine_name);
				$wine_name=str_replace('- vic','',$wine_name);
				$wine_name=str_replace('-vic','',$wine_name);
			}
			else
			{
			 	
				$wine_name = $lineData["beer_name"];
			}	
			$this->Template->assign("wine_name", $this->getCut($wine_name,15)); //$lineData["wine_name"]);
			$this->Template->assign("color", $lineData["color"]);
			$this->Template->assign("btl_size", $lineData["btl_size"]);
			
			
			$this->Template->assign("sku", str_pad($lineData["sku"],6,'0',STR_PAD_LEFT));
			$this->Template->assign("total_cases", $lineData["total_cases"]);
			$this->Template->assign("wh_sales", Number::fromDecimalToCurrency($lineData["wh_sales"],"$", ".", ",", 2, "left"));
			$this->Template->assign("total_sales", Number::fromDecimalToCurrency($lineData["total_sales"],"$", ".", ",", 2, "left"));
		}

		
		$wine_name=str_replace('- okan','',$wine_name);
		$wine_name=str_replace('- Okan','',$wine_name);
		$wine_name=str_replace('-okan','',$wine_name);
		$wine_name=str_replace('- Vic','',$wine_name);
		$wine_name=str_replace('- vic','',$wine_name);
		$wine_name=str_replace('-vic','',$wine_name);
			
		
	
		$this->Template->globalAssign("wine_name", $wine_name);
			
		$this->Template->globalAssign("total_page", $this->pages);
		
		if($this->search_id ==2)
		{
		 //($user_id==-1?"s.user_id":$user_id)
		 
		 
		 	$product_names =($this->product_id==1?"wines":"beers");
		 	
		 	if($this->product_id ==1)
		 		$product_names ="wines";
		 	else if($this->product_id ==2)
		 		$product_names ="beers";
		 	else if($this->product_id ==3)
		 		$product_names ="sakes";
			else if($this->product_id ==4)
		 		$product_names ="spirits";
		 	
			if($this->pageSize==-1&&$this->wine_type==-1)
				$wine_info ="Products sales:";
			else
			{
			 	$wingtype = F60DbUtil::getWineTyepByIds($this->wine_type, $this->product_id);
				if($this->pageSize==-1)
					$wine_info = "$wingtype wines sales:";
				if($this->wine_type==-1)
					$wine_info = "Top $this->pageSize selling wines:";
					
				if($this->pageSize!=-1&&$this->wine_type!=-1)
				{
				 	$type = strtolower($wingtype);
					$wine_info = "Top $this->pageSize selling $type $product_names:";
				}
			}
			
			$this->Template->globalAssign("wine_info", $wine_info);
		}
		
		
		if($this->search_adt1==-1)
		{
		 	$this->setPageFlip(0);
			$this->setPageFlip(1);
		}
		else
		{
			$this->Template->globalAssign("isDisplay", "none");
		}
			
		$this->setPageFlip(2);
	}
	
    function getCmsList() 
    {
		$aRow = 0;	        
		$total_sales =0;
		$total_cases =0;
		$wine_info="";
		  
	  	if($this->search_id==0 and $this->search_adt2==2)
		{
			//not print table	
			$this->Template->globalAssign("title_cases", "Contact name");
			$this->Template->globalAssign("title_WH", "Contact number");
			$this->Template->globalAssign("isShowRT_t", "none");
			$this->Template->globalAssign("title_case_style", "left");
			$this->Template->globalAssign("title_wh_style", "left");
			$this->Template->globalAssign("title_salse_style", "left");
		}
		else
		{			
			$this->Template->globalAssign("title_cases", "Total cases");
			$this->Template->globalAssign("title_WH", "WH sales");
			$this->Template->globalAssign("isShowRT_t", "block");
			$this->Template->globalAssign("title_case_style", "right");
			$this->Template->globalAssign("title_wh_style", "right");
			$this->Template->globalAssign("title_salse_style", "right");
		}
				
        while ($lineData = PagedDataSet::fetch()) 
        {       
            $aRow++;            
            if($this->search_id==0 and $this->search_adt2!=2)
            {
	            if(intval($lineData["total_sales"])!=0)
				{					 	
		            $this->Template->createBlock('loop_line');
		            $this->Template->assign("row_style", ($aRow % 2)?"cellA":"cellB");
		            $this->Template->assign("customer_name", htmlentities($lineData["customer_name"]));
		            
		            $address=$this->getcut($lineData["address"],500);
		            if(substr($address,0,1)=="-")
					{
						$address=substr_replace($this->getcut($lineData["address"],500),'',0,1);
					}
		            $this->Template->assign("address", $address);
		            
		            
		            $this->Template->assign("license_number", str_pad($lineData["licensee_no"],6,'0',STR_PAD_LEFT));
		            $this->Template->assign("store_type", $lineData["store_type"]);
		         }
			}
			else
			{
				$this->Template->createBlock('loop_line');
            	$this->Template->assign("row_style", ($aRow % 2)?"cellA":"cellB");
	            $this->Template->assign("customer_name", htmlentities($lineData["customer_name"]));
	            
	            $address=$this->getcut($lineData["address"],500);
	            if(substr($address,0,1)=="-")
				{
					$address=substr_replace($this->getcut($lineData["address"],500),'',0,1);
				}
	            $this->Template->assign("address", $address);
	            $this->Template->assign("license_number", $lineData["licensee_no"]);
	            $this->Template->assign("store_type", $lineData["store_type"]);
			}
            
         
            if($this->search_id==0 and $this->search_adt2==2)
            {
				//display contact name here				
				$this->Template->assign("total_cases", $lineData["contact_name"]);
				
				//display contact number here
				$this->Template->assign("wh_sales",$lineData["contact_number"]);
				$this->Template->assign("isShowRT", "none");
				$this->Template->assign("isShowRT_t", "none");
				$this->Template->assign("case_align_style", "CPgridrowCell");
				$this->Template->assign("sales_align_style", "CPgridrowCell");
				$this->Template->assign("wh_align_style", "CPgridrowCell");
			}
			else
			{				 		
				if($this->search_id<2)
				{
				 
	         		if($aRow==1)   
	         		{	
	         		 	if($this->product_id ==1)
		         		 	$product_name =$lineData["wine_name"];
		         		else
		         			$product_name =$lineData["beer_name"];
		         			
		         			
		         
		         	
		         	
	         		 	$wine_name = $product_name;
	         		 	
	         		 	if($this->product_id ==1)
	         		 	{
		         		   	 if( substr($product_name,-5)=="-okan")
		         		   	 {
								$wine_name = substr($product_name,0, (strlen($product_name)-5));
							 }
							 else if(  substr($product_name,-4)=="-vic")
							 {
								$wine_name = substr($product_name,0,(strlen($product_name)-4));
							 }
							 $wine_info =$wine_name.' '.$lineData["vintage"];
						}
						else
							$wine_info = $wine_name;
         			}
     			}
				
				if(intval($lineData["total_sales"])!=0)
				{
					$this->Template->assign("total_sales", Number::fromDecimalToCurrency($lineData["total_sales"],"$", ".", ",", 2, "left"));
	            	$this->Template->assign("wh_sales", Number::fromDecimalToCurrency($lineData["wh_sales"],"$", ".", ",", 2, "left"));
            		$this->Template->assign("total_cases", $lineData["total_cases"]);	
					$this->Template->assign("isShowRT", "block");
					$this->Template->assign("isShowRT_t", "block");
					$this->Template->assign("case_align_style", "CPgridrowCell_Right");
					$this->Template->assign("sales_align_style", "CPgridrowCell_Right");
					$this->Template->assign("wh_align_style", "CPgridrowCell_Right");
				}					
			}

            $this->Template->assign("user_name", $lineData["user_name"]);
	 		
        }
 
		if($this->search_id ==0)
		{
		
			if($this->cspc_code=="")
			{
				$wine_info ="Customers sales:";
			}
			else
			{
				$wine_info="Customers who purchased $wine_info:";
			}
		}
		else if($this->search_id ==1)
		{
		
			if($this->cspc_code=="")
			{
				$wine_info ="Top $this->pageSize customers:";
			}
			else
			{
				$wine_info="Top $this->pageSize customers who purchased $wine_info:";
			}
		}		
							
		$this->Template->globalAssign("wine_info", $wine_info);
		
		if($this->search_id!=1)
		{		
			$this->setPageFlip(0);
	    	$this->setPageFlip(1);
		}
		else
		{
			$this->Template->globalAssign("isDisplay", "none");
		}
		$this->setPageFlip(2);	
                    
    }
    
   function setPageFlip($step_id)
	{
	 	if($step_id==0)
	 	{
	 	 
			$this->Template->globalAssign("page",  PagedDataSet::getCurrentPage());
				
			if (PagedDataSet::getPreviousPage())
			{
				$this->Template->createBlock('prev_page_link');
				$this->Template->assign("prev_page", PagedDataSet::getPreviousPage());
			}
			if (PagedDataSet::getPageCount()>1 &&  PagedDataSet::getCurrentPage() <PagedDataSet::getPageCount())
			{
				$this->Template->createBlock('next_page_link');
				$this->Template->assign("next_page", PagedDataSet::getNextPage());
			}
		}
		else if($step_id ==1)
		{
			$this->Template->globalAssign("total_page", $this->pages);
			$this->Template->globalAssign("page",  PagedDataSet::getCurrentPage());				
		}
		else if($step_id ==2)
		{
			$this->Template->globalAssign($this->orderBy . "_sort", $this->sortSymbols[$this->orderType]);
			$this->Template->globalAssign("order_by", $this->orderBy);
			$this->Template->globalAssign("order_type", $this->orderType);    
			$this->Template->globalAssign("page",  PagedDataSet::getCurrentPage());		              			
		}		
	}
        
    function getContent() 
    {		
        $this->_buildContent();
        return $this->Template->getContent();
        
    }
    
	function getCut($listVal,$l)
	{
		return F60Date::ucwords1($listVal);
	
	}
	function getWineType($id,$product_id)
	{
	 
	}
}


?>