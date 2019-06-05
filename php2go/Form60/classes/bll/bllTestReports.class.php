<?php

import('php2go.base.Php2Go');
import('php2go.util.TypeUtils');
import('Form60.util.F60Date');
import('Form60.util.tableExtractor');
import('Form60.util.Services_JSON');
import('Form60.base.F60DALBase');
import('php2go.net.MailMessage'); 
import('php2go.data.PagedDataSet');


class bllABVenderData extends Php2Go 
{
    var $db;
    var $cfg;
    var $logFile;
    var $errorMessage;
    var $isPrint=false;
   
    function bllABVenderData()
    {
        $this->_init();;
        
        $this->db = & Db::getInstance();
        $this->db->setFetchMode(ADODB_FETCH_ASSOC);
    }
    
    function collectData()
    {
		$this->logMessage("---- Start data collection ---- ", false);	 
		
		$this->getVenderSiteSalesData();
		$this->getVenderSiteInvertoryData();
		
		$this->logMessage("---- Finished data collection ---- ", false);
		
		if ($this->errorMessage <> "")
		{
		    if ($this->getConfigVal("EMAIL_ON_ERROR"))
		        $this->emailError();
		    return false;
		}
		
		if ($this->getConfigVal("EMAIL_ON_SUCCESS"))
		    $this->emailSuccess();
		
		if ($this->getConfigVal("EMAIL_REPORT_ON_SUCCESS"))
		  $this->emailReport();
    }
    
    function collectABMonthlyAlloData()
    {		
     
  
		$this->getABMonthlyAlloHtmlData();
		$this->compareSalesWithAlloc();
		
		$this->logMessage("---- Finished data collection ---- ", false);
		
		echo herer;
		if ($this->errorMessage <> "")
		{
		    if ($this->getConfigVal("EMAIL_ON_ERROR"))
		        $this->emailError();
		    return false;
		}
		
		if ($this->getConfigVal("EMAIL_ON_SUCCESS"))
		    $this->emailSuccess();
		
		if ($this->getConfigVal("EMAIL_REPORT_ON_SUCCESS"))
		{
		 echo email;
		    $this->emailReport();
		    echo sent;
		    
		}

    }
    
    
    
    function getDataArrayFromFile($filename)
    {
		$myFile = ROOT_PATH."reports/$filename";
		$htmltext = file_get_contents($myFile);
		
		$tx = new tableExtractor;
		$tx->source = $htmltext;
		$tx->anchor ="<body bgcolor=#ffffff  onLoad=\"focus()\">";
		$tx->anchorWithin = "TURE";
		$tx->stripTags = true;
		$tableArray = $tx->extractTable();
		
		return $tableArray;
	}
	
//Array ( [1] => 8742 [2] => 30028400 [3] => CHRISTOPHER STE [4] => 5108 [5] => EXPATRIE RESERV [6] => 750 [7] => $22.36 [8] => $268.32 [9] => 8 [10] => 0 [11] => CS )  /*	  1. order id		2.licensee_no -- Required field;	3.Customer_name -- Required field 	  4. cspc code - Required field;	5. wine name - Required field 	  6. Bottle size 	7. display price 	  8 caseper price	9 allocation cases	  10. B/O		11. unit	  
	  //first is not valid row	  	  */	
	  
	function getABMonthlyAlloHtmlData()
    {
	
		$fileName ="ab_monthly_allo.html";


		$tableArray = $this->getDataArrayFromFile($fileName);
		$nSize = sizeof($tableArray);
		
		$current_year = date(Y);
		$current_month = date(m);
			
		$sql = "Delete from  temp_ab_monthly_allo ";//"where month(when_entered) =$current_month and year(when_entered)=$current_year";
	//	$sql = "Delete from  history_ab_monthly_allo where month(when_entered) =$current_month and year(when_entered)=$current_year";
		
    	$retVal = $this->db->execute($sql);
		
		if($nSize!=0)
		{
			$i=0;
			
			foreach ($tableArray as $row)  
			{
			 
				if(($i>0)&&($i<($nSize-1)) )// first row is empty 
				{
				 
				//importMothlyAlloToDB($license_no, $cm_name, $cspc_code, $wine_name,$bottle_size, $unit_price, $case_price, $allo_cs)
				  //if ($i==1)
				  
					$retVal = $this->importMonthlyAlloToDB($row[2],$row[3],$row[4],$row[5],$row[6],$row[7],$row[8],$row[9]);
				}
			
            $i++;
			}
				
		}
		else
		{
			// display error 
		}
	
	}
	
	function getVenderSiteSalesData()
	{
		$this->clearTable(false);
		
		$this->getVenderHtmlSalesData(); //all
		
		$this->clearTable(false,2);
		$this->getVenderHtmlSalesData(96); //enotecca
		$this->getVenderHtmlSalesData(118); //rustico
	}
	
   //Array ( [1] => 31096 [2] => DEAD LETTER OFF [3] => 750 [4] => 12 [5] => 1 [6] => CS [7] => 71950800 [8] => KENSINGTON WINE MARKET [9] => $273.00 [10] => $22.75 )
    /*
	  1. cspc code - Required field ;				  2. wine name - Required field
	  3. size - Required field ;					  4. btl_per_case --Required field
	  5. cases -- Required field;					  6. OUM (unit)
	  7.licensee_no -- Required field;				  8.Customer_name -- Required field;
	  9. total_sales/cs -- Required field;			  10.price_unit -- Required field

	  
	  //first and last row are not valid row
	  */	
	function getVenderHtmlSalesData($estate_id=null)
    {

		if($estate_id == null)
			$fileName ="ab_sales_report.html";
		else if ($estate_id ==2)
			$fileName ="ab_sales_hill.html";
		else if ($estate_id ==96)
			$fileName ="ab_sales_enot.html";
		else if ($estate_id ==118) //rustico
			$fileName ="ab_sales_rustico.html";


		$tableArray = $this->getDataArrayFromFile($fileName);
		$nSize = sizeof($tableArray);
		
		if($nSize!=0)
		{
			$i=0;
			$cs =0;
			foreach ($tableArray as $row)  
			{
				if(($i>0)&&($i<($nSize-1)) )// first row is empty and last row
				{
				 //	importVerderSalesToDB($cspc_code, $wine_name, $size, $btl_per_case,$total_cases,$lic_no,$store_name,$price_cs,$unit_price)
	//Array ( [1] => 11114 [2] => CHOOK SHED [3] => 750 [4] => 12 [5] => 1 [6] => CS [7] => 30028400 [8] => CHRISTOPHER STEWART WINE & SPIRITS INC. [9] => $104.64 [10] => $8.72 )
	
					$cases = $row[5];
					if($row[6]='BT')//by bottles
						$cases = $cases/$row[4];
						
					$retVal = $this->importVenderSalesToDB($row[1],$row[2],$row[3],$row[4],$row[5],$row[7],$row[8],$row[9],$row[10],$estate_id);
					$cs = $cs+$row[5];
				}
			    $i++;
			}
			
		
		}
		else
		{
			// display error 
		}
		
	}
     
	//Array ( [1] => 188003 [2] => GEWURZTRAMINER [3] => 750 [4] => 12 [5] => $15.61 [6] => 32 [7] => 0 [8] => 0 [9] => 0 [10] => 32 [11] => 0 [12] => 5 ) 

    /*
	  1. cspc code - Required field ;				  2. wine name - Required field
	  3. size - Required field ;					  4. units --Required field
	  5. $units -- Required field;					  6. OH_CS
	  7.Held;					  8.Com
	  9.Alloc --Required field;					  10.AV_CS -- Required field
	  11.ON_ORD;					  12.Sold_TD
	  
	  //first and last row are not valid row
	  */
	function getVenderSalesFromDB($report_month="",$report_year="",$estate_id="")
	{
	 
	 	$sale_year = $report_year;
	 	$sale_month= $report_month;
	 	
	 	$estate_filter ="where";
	 	$table_name="ab_ssds_sales";
	 	
		$pagedRS = & PagedDataSet::getInstance("db");
        
        $pagedRS->setPageSize(100000);
        $pagedRS->setCurrentPage(1);
	 	
	 	if($estate_id !="")
	 	{
	 		$estate_filter = " where estate_id = $estate_id and ";
	 		$table_name ="ab_sales_bc";
	 	}	 	
	 		 		
	 	if($estate_id==96||$estate_id==97||$estate_id==118)
			$sql = "Select SKUA, product_name, size, unit_sales,btl_per_cs,total_cs,licensee_no,store_name,price_case,price_unit from $table_name $estate_filter  year(sale_date)= $sale_year and month(sale_date)=$sale_month order by licensee_no, store_name ";
		else
			$sql="Select SKUA, product_name, size, unit_sales,btl_per_cs,total_cs,st.licensee_no,store_name,price_case,price_unit, c.billing_address_city city
			from $table_name st 
			left outer join customers c on st.licensee_no = c.licensee_number 
			where year(sale_date)= $sale_year and month(sale_date)=$sale_month
			order by st.licensee_no, store_name";
			
	
        if (!$pagedRS->load($sql))
        {
            $this->file_format_error .= "Error: unable to get sales data.";
            $bRet = false;
            return false;
        }
        $rs["total_record"]=$pagedRS->getTotalRecordCount();
        
        $rs["sales_details"] = $pagedRS;
		
		if($estate_id!=null)
		{
		 	$sale_month=$sale_month+1;
		 	
		 	if($sale_month==13)
		 	{
		 		$sale_month=1;
			 	$sale_year++;
		 	}
		 	
			$sql="Select * from government_inventory where estate_id = $estate_id and year(when_entered)= $sale_year and month(when_entered)=$sale_month";
			
		//	$sql="Select * from government_inventory where estate_id = $estate_id and year(when_entered)= 2012 and month(when_entered)=1";
	        
	        $rs["inventory_data"] =$this->db->getAll($sql);

		}       
			
        return $rs;
	}
	
	
	function getABSalesReportFromDB($report_month="",$report_year="",$estate_id="")
	{
	 
	 	$sale_year = $report_year;
	 	$sale_month= $report_month;
	 	
	 	$estate_filter ="where";
	 	$table_name="ssds_sales";
	 	
		$pagedRS = & PagedDataSet::getInstance("db");
        
        $pagedRS->setPageSize(100000);
        $pagedRS->setCurrentPage(1);
	 	
	 	/*if($estate_id !="")
	 	{
	 		$estate_filter = " where estate_id = $estate_id and ";
	 		$table_name ="ab_sales_bc";
	 	}*/
	 	
	 	$estate_filter = "";
	 	if($estate_id !="")
	 	{
	 	 	if($estate_id ==118)
	 	 	{
	 	 	 	$estate_filter =" and w.estate_id = 118 ";
	 	 	}
	 	 	else
	 	 	{
				$estate_filter =" and (w.estate_id =96 or w.estate_id =97)";
			}
		}
		
		$sql = "Select SKUA, product_name, lksize.display_name size, unit_sales,w.bottles_per_case btl_per_cs, cases_sold total_cs, licensee_no,
		c.customer_name store_name, s.price_per_unit*w.bottles_per_case price_case, c.billing_address_city city
			 	
		From ssds_sales s, wines w,lkup_bottle_sizes lksize, customers c
				  
		Where s.province_id =2
		
		and  s.wine_id =w.wine_id
		
		and w.lkup_bottle_size_id = lksize.lkup_bottle_size_id
		
		and s.customer_id =c.customer_id
		$estate_filter
		
		
		and year(sale_date)= $sale_year and month(sale_date)=$sale_month order by licensee_no, store_name ";
			
	 		
	 /*	if($estate_id!=2)
			$sql = "Select SKUA, product_name, size, unit_sales,btl_per_cs,total_cs,licensee_no,store_name,price_case,price_unit from $table_name $estate_filter  year(sale_date)= $sale_year and month(sale_date)=$sale_month order by licensee_no, store_name ";
		else
			$sql="Select SKUA, product_name, size, unit_sales,btl_per_cs,total_cs,st.licensee_no,store_name,price_case,price_unit, c.billing_address_city city
			from ab_sales_bc st 
			left outer join customers c on st.licensee_no = c.licensee_number 
			where st.estate_id=2
			and year(sale_date)= $sale_year and month(sale_date)=$sale_month
			order by st.licensee_no, store_name";
			
	*/
        if (!$pagedRS->load($sql))
        {
            $this->file_format_error .= "Error: unable to get sales data.";
            $bRet = false;
            return false;
        }
        $rs["total_record"]=$pagedRS->getTotalRecordCount();
        
        $rs["sales_details"] = $pagedRS;
		
		if($estate_id!=null)
		{
		 	$sale_month=$sale_month+1;
			$sql="Select * from government_inventory where estate_id = $estate_id and year(when_entered)= $sale_year and month(when_entered)=$sale_month";
	        
	        $rs["inventory_data"] =$this->db->getAll($sql);

		}       
			
        return $rs;
	}
	
	
	 function getVenderSiteInvertoryData()
	 {
	  	$this->clearTable(true);
		$this->getInventoryHtmlData(96); //enotecca
		$this->getInventoryHtmlData(118); //rustico
		
	
		
	}
	
    function getInventoryHtmlData($estate_id)
    {
		$fileName ="ab_inventory_hillside.html";
		
		if($estate_id ==2)
			$fileName ="ab_inventory_hillside.html";
		if($estate_id == 118)//enotecca
			$fileName ="ab_inventory_rustico.html";	
		else
		{
			$fileName ="ab_inventory_enotecca.html";	
		}
		
		
		$tableArray = $this->getDataArrayFromFile($fileName);
		
		
		$nSize = sizeof($tableArray);
		
	//	print_r($tableArray);
		
		if($nSize!=0)
		{
			     

			$i=0;
			//CSPC1 DESCRIPTION2 SIZE3 UNITS4 $/UNIT5 OH_CS OH_UN HELD COM ALLOC10 AV_CS11 ON_ORD SOLD_TD 
 
			foreach ($tableArray as $row)  // index = 0 index = 34 ( first and last)
			{

				if(($i>0)&&($i<($nSize-1)) )// first row is empty
				{

					$retVal = $this->importInverntoryToDB($row[1],$row[2],$row[3],$row[4],$row[5],$row[9],$row[10],$estate_id);
				}
				$isFirstRow = false;
            $i++;
			}
				
		}
		else
		{
			// display error 	
			
		}
	}


	function getABStorePenReport($sale_year, $sale_month)
	{
		$sql="SELECT count(*) store_number, s.skua, w.wine_name product_name
				FROM `ssds_sales` s, wines w
				where year(sale_date)=$sale_year
				and month(sale_date)=$sale_month
				and province_id=2
				and w.wine_id =s.wine_id
				group by skua
				order by store_number desc";
	
		$pagedRS = & PagedDataSet::getInstance("db");
        
        $pagedRS->setPageSize(100000);
        $pagedRS->setCurrentPage(1);
		if (!$pagedRS->load($sql))
        {
            $this->file_format_error .= "Error: unable to get sales data.";
            return false;
        }        
        $rs["sales_data"]=$pagedRS;		
        
        return $rs;
	}

	// report type : 1: by store
	//				 2: by wine
	function getBreakDownReportData($user_id, $sale_year, $sale_month,$report_type=1) 
	{
		if($report_type ==1)
		{
			$sql ="SELECT  c.customer_name store_name,s.licensee_no,sum(cases_sold) cases FROM ssds_sales s, customers c
					where s.user_id =$user_id
					and	c.licensee_number = s.licensee_no
					and month(s.sale_date)=$sale_month 
					and year(s.sale_date)=$sale_year 
					and s.province_id =2
					group by licensee_no
					order by cases desc";	
	}
		else
		{
	
					
			$sql ="SELECT product_id, e.estate_name,concat(w.wine_name,' ',lkcl.display_name) wine_name,lkbs.display_name btl_size,sum(cases_sold) cases
					FROM `ssds_sales` s,wines w, lkup_wine_color_types lkcl, lkup_bottle_sizes lkbs, estates e 
					where w.wine_id=s.wine_id 
					And w.estate_id = e.estate_id
					And w.lkup_wine_color_type_id = lkcl.lkup_wine_color_type_id 
					AND w.lkup_bottle_size_id =lkbs.lkup_bottle_size_id 
					And s.province_id = 2 
					And month(s.sale_date)=$sale_month 
					And year(s.sale_date)=$sale_year 
					And s.user_id = $user_id
					and s.product_id =1
					group by s.wine_id
				    
				    
				    union
				    
				    SELECT product_id, e.estate_name,concat(w.beer_name,' ',lkcl.display_name) wine_name,lkbs.display_name btl_size,sum(cases_sold) cases
					FROM `ssds_sales` s,beers w, lkup_beer_types lkcl, lkup_beer_sizes lkbs, estates e 
					where w.beer_id=s.wine_id 
					And w.estate_id = e.estate_id
					And w.lkup_beer_type_id = lkcl.lkup_beer_type_id 
					AND w.lkup_beer_size_id =lkbs.lkup_beer_size_id 
					And s.province_id = 2 
					And month(s.sale_date)=$sale_month 
					And year(s.sale_date)=$sale_year 
					And s.user_id = $user_id
					and product_id =2
					group by s.wine_id
				    order by product_id, cases desc
				    
					";
		}
		
				
		$pagedRS = & PagedDataSet::getInstance("db");
        
        $pagedRS->setPageSize(1000);
        $pagedRS->setCurrentPage(1);
		if (!$pagedRS->load($sql))
        {
            $this->file_format_error .= "Error: unable to get sales data.";
            return false;
        }
        
        $rs["sales_data"]=$pagedRS;		
        
        return $rs;
	}

	function getABUsers($sale_year, $sale_month)
	{
		$sql="Select distinct s.user_id, concat(u.first_name,' ', u.last_name) user_name 
			  From users u, ssds_sales s
			  Where s.province_id=2 
			  And s.user_id = u.user_id
			  And year(sale_date)=$sale_year
			  And month(sale_date)=$sale_month
			  And s.user_id <>13			  
			  Order by s.user_id
			  ";
		
		return $this->db->getAll($sql);
	}
	
	function getHisotryAlloReportData($report_year, $report_month, $user_id = 0) 
	{	
		
		if($user_id ==0)
		{
			$user_filter = " user_id is null";
		}
		else
		{
			$user_filter = " user_id=$user_id";	
		}
		$sql ="SELECT am.*, date_format(am.allo_date,'%m-%Y-%d') format_date
				from history_ab_monthly_allo am left outer join customers cm on am.license_no=cm.licensee_number 
				left outer join users_customers uc on cm.customer_id=uc.customer_id   
				where am.license_no<>30028400 and year(am.when_entered)=$report_year and month(am.when_entered)=$report_month and $user_filter order by am.license_no asc, cspc_code asc ";
		
		$pagedRS = & PagedDataSet::getInstance("db");
        
        $pagedRS->setPageSize(100000);
        $pagedRS->setCurrentPage(1);
		if (!$pagedRS->load($sql))
        {
            $this->file_format_error .= "Error: unable to get sales data.";
            return false;
        }        
        $rs["allo_data"]=$pagedRS;		
        
        return $rs;
	}
	
	
	function getAlloReportData($user_id = 0) 
	{	
		
		if($user_id ==0)
		{
			$user_filter = " user_id is null";
		}
		else
		{
			$user_filter = " user_id=$user_id";	
		}
		$sql ="SELECT am.*, date_format(am.allo_date,'%m-%Y-%d') format_date
				from ab_monthly_allo am left outer join customers cm on am.license_no=cm.licensee_number 
				left outer join users_customers uc on cm.customer_id=uc.customer_id   
				where am.license_no<>30028400 and $user_filter order by am.license_no asc, cspc_code asc ";
		
		$pagedRS = & PagedDataSet::getInstance("db");
        
        $pagedRS->setPageSize(100000);
        $pagedRS->setCurrentPage(1);
		if (!$pagedRS->load($sql))
        {
            $this->file_format_error .= "Error: unable to get sales data.";
            return false;
        }        
        $rs["allo_data"]=$pagedRS;		
        
        return $rs;
	}
	
	function getVenderSalesYears($reportTypeId, $estate_id="")
	{
		if($reportTypeId!=3)//monthly sales report from AB vender site
		{
			$sql="Select distinct year(sale_date) sales_year from ssds_sales where province_id = 2 order by sales_year  desc";
		}
		else if($reportTypeId ==3)
		{
		 	$estate_filter = " and estate_id = 2 ";
		 	if($estate_id !=2)
		 	{
				$estate_filter = " and (w.estate_id =96 or w.estate_id = 97)";
			}
			
			$sql= "Select distinct year(sale_date) sales_year from ssds_sales s , wines w 
					where province_id = 2
					and s.wine_id =w.wine_id
					$estate_filter
					order by sales_year  desc
				 ";
		}
		else if($reportTypeId==5)//monthly allocation report from AB vender site
		{
			$sql="Select distinct year(when_entered) sales_year from history_ab_monthly_allo";	
		}

		return  $this->db->getAll($sql);
	}
	
	function getVenderSalesMonths($sale_year,$reportTypeId,$estate_id="")
	{
	 
		if($reportTypeId!=3)//monthly sales reprot from AB vender site
		{
			$sql="Select distinct month(sale_date) sales_month from ssds_sales where year(sale_date)=$sale_year and province_id = 2 order by sales_month";	
		}
		else if($reportTypeId ==3)
		{
		 	$estate_filter = " and estate_id = 2 ";
		 	
		 	if($estate_id !=2)
		 	{
				$estate_filter = " and (w.estate_id =96 or w.estate_id = 97)";
			}
			$sql="Select distinct month(sale_date) sales_month from ssds_sales s , wines w 
					where year(sale_date)=$sale_year
					and province_id = 2
					and s.wine_id =w.wine_id
					
					$estate_filter
					order by sales_month  asc
				 ";
		}
	
		else if($reportTypeId==5)//monthly sales reprot from AB vender site
		{
			$sql="Select distinct month(when_entered) sales_month from history_ab_monthly_allo where year(when_entered)=$sale_year";
		}				
		return  $this->db->getAll($sql);
	}

	function clearABAlloTable($isHistory=false)
	{
		if($isHistory)	
		{
			$sql= "Delete from history_ab_monthly_allo where month(when_entered) =$current_month and year(when_entered)=$current_year";
		}
		else
		{
			$sql= "Delete from temp_ab_monthly_allo";	
		}
		
    	$retVal = $this->db->execute($sql);
	}
	
	function clearTable($isAbInventory = false,$estate_id=null)
	{
	  $current_month = Date(m);
	  $current_year = Date(Y);

		if($isAbInventory)	
		{
			$sql = "Delete from  government_inventory where month(when_entered) =$current_month and year(when_entered)=$current_year";
		}
		else
		{
		 	if($estate_id!=null)
		 	{
		 	 	$sql = "Delete from ab_sales_bc where month(when_entered) =$current_month and year(when_entered)=$current_year";   			 
			}
			else
				$sql = "Delete from ab_ssds_sales where month(when_entered) =$current_month and year(when_entered)=$current_year";
    	
		}
    	$retVal = $this->db->execute($sql);
	}
		
	/*
	  1. cspc code - Required field ;				  2. wine name - Required field
	  3. size - Required field ;					  4. btl_per_case --Required field
	  5. cases -- Required field;					  6. OUM (unit)
	  7.licensee_no -- Required field;				  8.Customer_name -- Required field;
	  9. total_sales/cs -- Required field;			  10.price_unit -- Required field
	  //first and last row are not valid row
	  */
		
		function importVenderSalesToDB($cspc_code, $wine_name, $size, $btl_per_case,$total_cases,$lic_no,$store_name,$price_cs,$unit_price,$estate_id)
		{		 
		 	$table_name = "ab_ssds_sales";
		 	
		 	if($estate_id ==118 or $estate_id ==96)
		 		$table_name = "ab_sales_bc";		 		
		 		
			$when_entered = sql_escape_value('datetime', F60Date::sqlDateTime());
			
			//because thie application runs on every first day of the month, it's acturally last month's data
			
			$sale_month = intval(date(m))-1;
			
			if($sale_month ==0)
			{
				$sale_month =12;
				$sale_year=intval(date(Y))-1;
			}
			else
				$sale_year=date(Y);

			$sale_day="01";
			
			$sale_date = "$sale_year-$sale_month-$sale_day";
		 
			$cspc_code =  sql_escape_value('varchar', $cspc_code); 
		//	$wine_name =  sql_escape_value('varchar', $wine_name);
			$wine_name =str_replace("''","'",$wine_name) ;
			$wine_name =str_replace("'","\'",$wine_name) ;
		//	$store_name =  sql_escape_value('varchar', $store_name);
			$store_name =str_replace("''","'",$store_name) ;
			$store_name =str_replace("'","\'",$store_name) ;
			
			$unit_price = str_replace("$","",$unit_price) ;
			$price_cs = str_replace("$","",$price_cs) ;
			
			$unit_sales = $btl_per_case*$total_cases;
		
			if($estate_id ==null)
			{
			
				$sql = "INSERT INTO ab_ssds_sales (unit_sales, Licensee_No,SKUA,product_name,sale_date,size,total_cs,store_name,price_case,price_unit,btl_per_cs,when_entered)VALUES ($unit_sales, '$lic_no', $cspc_code, '$wine_name', '$sale_date', $size, $total_cases, '$store_name',$price_cs,$unit_price,$btl_per_case,$when_entered);";
					return $this->db->execute($sql);
					
			//	$sql = "INSERT INTO history_ab_ssds_sales (unit_sales, Licensee_No,SKUA,product_name,sale_date,size,total_cs,store_name,price_case,price_unit,btl_per_cs,when_entered)VALUES ($unit_sales, '$lic_no', $cspc_code, '$wine_name', '$sale_date', $size, $total_cases, '$store_name',$price_cs,$unit_price,$btl_per_case,$when_entered);";
				//	return $this->db->execute($sql);
			}
			else
			{
				$sql = "INSERT INTO ab_sales_bc(unit_sales, Licensee_No,SKUA,product_name,sale_date,size,total_cs,store_name,price_case,price_unit,btl_per_cs,when_entered,estate_id)VALUES ($unit_sales, '$lic_no', $cspc_code, '$wine_name', '$sale_date', $size, $total_cases, '$store_name',$price_cs,$unit_price,$btl_per_case,$when_entered,$estate_id);";
				return $this->db->execute($sql);
				
			//	$sql = "INSERT INTO hisotry_ab_sales_bc(unit_sales, Licensee_No,SKUA,product_name,sale_date,size,total_cs,store_name,price_case,price_unit,btl_per_cs,when_entered,estate_id)VALUES ($unit_sales, '$lic_no', $cspc_code, '$wine_name', '$sale_date', $size, $total_cases, '$store_name',$price_cs,$unit_price,$btl_per_case,$when_entered,$estate_id);";
			//	return $this->db->execute($sql);
			}
		
		}
		 
		function importInverntoryToDB($cspc_code, $wine_name, $size, $units,$unit_price, $alloc,$av_cs,$estate_id)
		{
			$when_entered = sql_escape_value('datetime', F60Date::sqlDateTime());
			$cspc_code =  sql_escape_value('varchar', $cspc_code); 
			$wine_name =  sql_escape_value('varchar', $wine_name);
			$wine_name =str_replace("'","",$wine_name) ;
			$wine_name =str_replace("'","\'",$wine_name) ;
			
			$unit_price = str_replace("$","",$unit_price) ;
			$province_id =2;
			
			$sql = "INSERT INTO government_inventory (province_id, estate_id,sku, wine_name, size, units,unit_price,alloc,av_cs, when_entered)VALUES ($province_id, $estate_id, $cspc_code, '$wine_name', '$size', $units, $unit_price, $alloc,$av_cs,$when_entered);";
			
			return $this->db->execute($sql);		
		}
		
		function importMonthlyAlloToDB($license_no, $cm_name, $cspc_code, $wine_name,$bottle_size, $unit_price, $case_price, $allo_cs, $isHistory=false)
		{
			$when_entered = sql_escape_value('datetime', F60Date::sqlDateTime());
			$allo_date = sql_escape_value('datetime', F60Date::sqlDateTime());
			
			$cspc_code =  sql_escape_value('varchar', $cspc_code); 
			$wine_name =str_replace("''","'",$wine_name) ;
			$wine_name =  sql_escape_value('varchar', $wine_name);
			$cm_name =  sql_escape_value('varchar', $cm_name);
			
			$unit_price = str_replace("$","",$unit_price) ;
			$case_price = str_replace("$","",$case_price) ;			
			
			//template			
			$sql = "INSERT INTO temp_ab_monthly_allo ( license_no,customer_name, cspc_code, wine_name, size, price_per_unit,price_per_case, allo_cases, allo_date, when_entered)VALUES ($license_no, $cm_name, $cspc_code, $wine_name, '$bottle_size', $unit_price, $case_price, $allo_cs,$allo_date,$when_entered);";
			
			$this->db->execute($sql);
			
			//history
			$sql = "INSERT INTO history_ab_monthly_allo ( license_no,customer_name, cspc_code, wine_name, size, price_per_unit,price_per_case, allo_cases, allo_date, when_entered)VALUES ($license_no, $cm_name, $cspc_code, $wine_name, '$bottle_size', $unit_price, $case_price, $allo_cs,$allo_date,$when_entered);";			
		//	$this->db->execute($sql);			
		}
		
		function compareSalesWithAlloc($current_month)
		{
		 	$last_month = $current_month -1;
		 	$lastMonth_year= Date(Y);
		 	
		 	$current_year = Date(Y);
		 	if($last_month==0)
		 	{
				$last_month =12;
				$lastMonth_year= Date(Y)-1;
			}
		 	
			$when_entered = sql_escape_value('datetime', F60Date::sqlDateTime());
		 	
			//delete same sales & alloc
			$sql_delete = "Delete ab_monthly_allo from ab_monthly_allo a,ab_ssds_sales s
					where year(s.sale_date) = $lastMonth_year
					and month(s.sale_date)=$last_month					
					and s.licensee_no=a.license_no
					and s.skua=a.cspc_code
					and s.total_cs >=a.allo_cases				
					";
			// update old allocation by new allocation with same licensee and cspc code, keep aold llocation date
			$sql_update = "Update ab_monthly_allo,temp_ab_monthly_allo
						   set ab_monthly_allo.allo_cases = temp_ab_monthly_allo.allo_cases, ab_monthly_allo.when_entered =$when_entered 		  				   
						   where (temp_ab_monthly_allo.license_no=ab_monthly_allo.license_no
						   and temp_ab_monthly_allo.cspc_code=ab_monthly_allo.cspc_code)";
						   
			// insert new allocation	  
			$sql_insert = " Insert into ab_monthly_allo
							( customer_name,license_no,wine_name, 
							cspc_code,allo_date, size, price_per_case, allo_cases, price_per_unit, when_entered)  
							Select tal.customer_name,tal.license_no,tal.wine_name, tal.cspc_code,tal.allo_date, 
							tal.size, tal.price_per_case, tal.allo_cases, tal.price_per_unit,$when_entered  
							from ab_monthly_allo al right outer join  temp_ab_monthly_allo tal  
							on  (tal.license_no =al.license_no and tal.cspc_code = al.cspc_code)
							where al.customer_name is null";
			
			$sql_insert_history ="";
	
			$this->db->execute($sql_delete);
			$this->db->execute($sql_update);
			$this->db->execute($sql_insert);		
		}
		
		function insertABMonAllo2History()
		{
			$sql_insert = " Insert into history_ab_monthly_allo
							( customer_name,license_no,wine_name, 
							cspc_code,allo_date, size, price_per_case, allo_cases, price_per_unit, when_entered)  
							Select tal.customer_name,tal.license_no,tal.wine_name, tal.cspc_code,tal.allo_date, 
							tal.size, tal.price_per_case, tal.allo_cases, tal.price_per_unit,when_entered 
							from ab_monthly_allo tal";
			$this->db->execute($sql_insert);
		}
		
		function insertABMonAlloTemp2History()
		{
			$sql_insert = " Insert into history_temp_ab_monthly_allo
							( customer_name,license_no,wine_name, 
							cspc_code,allo_date, size, price_per_case, allo_cases, price_per_unit, when_entered)  
							Select tal.customer_name,tal.license_no,tal.wine_name, tal.cspc_code,tal.allo_date, 
							tal.size, tal.price_per_case, tal.allo_cases, tal.price_per_unit,when_entered 
							from temp_ab_monthly_allo tal";
			$this->db->execute($sql_insert);
		}
 	
	    function getConfigVal($config)
	    {
	        return $this->cfg[$config];
	    }  
    
	    function setError($error, $message, & $data, $logAsError=true)
	    {
	        $data["error_code"] = $error;
	        $data["error_message"] = $message;
	        $this->logMessage("Error: $error $message", false, $logAsError);
	    }
    
	    function _init()
	    {
	        include('config/storepenetrationdataconfig.php');
	        $this->cfg = $STORE_PEN_DATA_CFG;
	        
	        $this->logFile = $this->getConfigVal("LOG_FILE_PATH") . $this->getConfigVal("LOG_FILE_PREFIX") . strftime("%Y_%m_%d") . ".txt";
	        if (!file_exists($this->logFile))
	            @touch($this->logFile);
	            
	        return true;
	    }
    
	    function logMessage($message, $debug=false, $errorMessage = false)
	    {
	        $logFormat = "[%s] %s\r\n";
	        $logString = sprintf($logFormat, strftime("%Y-%m-%d %H:%M:%S"), $message);
	        if ($debug)
	        {
	            if ($this->getConfigVal("DEBUG_TRACE"))
	                @error_log($logString, 3, $this->logFile);
	        }
	        else
	            @error_log($logString, 3, $this->logFile);
	        if ($errorMessage)
	            $this->errorMessage.= $logString;
	    }
    
	    function _sendEmail($to, $subject, $body, $attachments=null)
	    {
	        $mail = new MailMessage(); 
	        $mail->setSubject($subject); 
	        $mail->setFrom("helen@christopherstewart.com;"); 
	        
	        $arrayTo = split(";",$to);     
	     	for($i=0; $i<count($arrayTo);$i++)
	     	{
	    	 	$emailTo = $arrayTo[$i];
	    	 	if($emailTo!="")
				    $mail->addTo($emailTo);
	    	}
	       // $mail->addTo($to);
	        if (TypeUtils::isArray($attachments))
	        {
	            foreach ($attachments as $attachment)
	            {
	                if ($attachment<>"")
	                    $mail->addAttachment($attachment);
	            }
	        }
	        else if (isset($attachments) && $attachments<>"")
	            $mail->addAttachment($attachments);
	           
	        $mail->setHtmlBody($body);
	        $mail->build(); 
	        
	        $transport =& $mail->getTransport(); 
	        $transport->setType(MAIL_TRANSPORT_MAIL);
	        if (!$transport->send())
	            $this->logMessage("Error sending email: " . $transport->getErrorMessage(), false, true);
	    }
    
	    function emailSuccess()
	    {
	        $to = $this->getConfigVal("SUCCESS_EMAIL_RECEPIENTS");
	        $subject = $this->replaceTokens($this->getConfigVal("SUCCESS_EMAIL_SUBJECT"));
	        $body = "<H3>Detailed logs:</H3>\r\n";
	        $body .= nl2br(file_get_contents($this->logFile));
	        $this->_sendEmail($to , $subject, $body);
	    }
	    
	    function emailError()
	    {
	        $to = $this->getConfigVal("ERROR_EMAIL_RECEPIENTS");
	        $subject = $this->replaceTokens($this->getConfigVal("ERROR_EMAIL_SUBJECT"));
	        $body = "<H3>Errors:</H3>\r\n";
	        $body .= nl2br($this->errorMessage);
	        $body .= "<BR>\r\n<H3>Detailed logs:</H3>\r\n";
	        $body .= str_replace("\r\n", "<BR>", file_get_contents($this->logFile));
	        $this->_sendEmail($to , $subject, $body);
	    }
    
    function emailReport()
    {
      //  $to = "lisa@christopherstewart.com;helen@christopherstewart.com";
      
        $to = "helen@christopherstewart.com";
     
        $subject = "Sales analysis reports";
        $body = " ";


        
        $arrayAttachFileNames =array();
        //total alberta sales report

	   import('Form60.exportreports.excelABVenderSalesReport');
       $excelReport = new excelABVenderSalesReport(false,true);
       $fileName = 'C:/phpdev/www/php2go/form60/salesreports/BC Sales Analysis Report - Taylor - May 2013.xlsx';
       
       
       array_push($arrayAttachFileNames,$fileName);
        
	  /* import('Form60.exportreports.excelBCInABVenderReport');

	
       $excelReport = new excelBCInABVenderReport(false,true);
       $fileName = $excelReport->getReportFile($sale_month,$sale_year,96);
       array_push($arrayAttachFileNames,$fileName);
       
       
       $excelReport = new excelBCInABVenderReport(false,true);
       $fileName = $excelReport->getReportFile($sale_month,$sale_year,118);
       array_push($arrayAttachFileNames,$fileName);
   */
	
   /*  //removed by Helen, Hillside is not available since May 01,2011
   	
     $excelReport = new excelBCInABVenderReport(false,true);
       $fileName = $excelReport->getReportFile($sale_month,$sale_year,2);
       array_push($arrayAttachFileNames,$fileName);
  	 */ 
       if (file_exists($fileName))
       {
             $this->_sendEmail($to , $subject, $body, $arrayAttachFileNames);
            //unlink($fileName);
           
		   /* foreach ($arrayAttachFileNames as $filename)
            {
                if ($filename<>"")
                    unlink($filename);
            }*/            
    	}        
    }
    
    
}
?>