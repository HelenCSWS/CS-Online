<?php

import('php2go.base.Php2Go');
import('php2go.util.TypeUtils');
import('Form60.util.F60Date');
import('Form60.base.F60DbUtil');
import('php2go.data.PagedDataSet');
import('Form60.base.F60DALBase');
import('Form60.util.F60Common');


class salesAnalysisData extends Php2Go 
{
    var $db;
    var $cfg;
    var $logFile;
    var $errorMessage;
    
    
    var $product_table="";
    var $product_id_field="";
   
    function salesAnalysisData()
    {
	 	include('config/emailoutconfig.php');
	 	
     	include('config/dataconfig.php');

        $this->db = & Db::getInstance();
        $this->db->setFetchMode(ADODB_FETCH_ASSOC);
        
        $this->cfg = $EMAIL_CFG;
        
		$this->data_cfg = $DATA_CFG;
        
        
    }
    
    function getUsers($year,$month,$province_id,$user_id=-1)
    {
     	
     	$userFilter="";
     	
     	if($user_id!=-1)
     	{
			$userFilter =" and su.user_id =$user_id";
		}
     	
		$orderDec ="ASC";
		if($province_id==2)
		{
			$orderDec ="desc";
		}
     		 $sql=" SELECT distinct su.user_id, concat(first_name,' ',last_name) user_name 
			  		from user_sales_summary su, users u
					where u.user_id =su.user_id
					and sale_year=$year and sale_month=$month
					and su.province_id=$province_id 
					and su.user_id <>0
					$userFilter					
					order by su.user_id $orderDec					
					";  
			$rows = $this->db->getAll($sql);			
			return $rows;		
	}
	
	function getAnaUsers($year,$month,$province_id,$user_id=-1)
    {
     	
     	$userFilter="";
     	
     	if($user_id!=-1)
     	{
			$userFilter =" and su.user_id =$user_id";
		}
     	
		$orderDec ="ASC";
		if($province_id==2)
		{
			$orderDec ="desc";
		}
     		 $sql=" SELECT distinct su.user_id, concat(first_name,' ',last_name) user_name, 
			  		u.email1 from bi_monthly_sales_basic_info su, users u
					where u.user_id =su.user_id
					and sales_year=$year and sales_month=$month
					and su.province_id=$province_id 
					and su.user_id <>0
					and u.deleted =0
					$userFilter					
					order by su.user_id $orderDec					
					";  
			$rows = $this->db->getAll($sql);			
			return $rows;		
	}
   
   function checkIfDataAVA($sales_year,$sales_month,$province_id)
   {
		$sql="select distinct sales_month from bi_monthly_sales_basic_info
			 where
			 sales_year=$sales_year and sales_month=$sales_month
			 and province_id=$province_id 
			 ";
			 
			 
			 $rows = $this->db->getAll($sql);	
			 
			 		
			return count($rows);	
   }
   
    function generateCustomersSales($user_id,$year,$month,$province_id)
    {
     		$this->ETL_deleteSalesSummaryData($user_id,$year,$month,$province_id);
     		
			$sql ="select distinct customer_id 
					from ssds_sales 
					where province_id =$province_id and user_id = $user_id 
				   and month(sale_date)=$month and year(sale_date)=$year";				   					
			$rows = $this->db->getAll($sql);
			
			if(count($rows)!=0)
			{
				for($i=0;$i<count($rows);$i++)
				{
					$customer_id = $rows[$i]["customer_id"];			
					$this->ETL_saleAnalysisData_addBasicInfo($customer_id,$user_id,$year,$month,$province_id);
				}
			}
			return true;
	 }
	 
	 function ETL_cleanSalesSummaryData($sales_id)
	 {
			$sql ="delete from bi_monthly_sales_summary 
				   where (total_cases =0 
				   and monthly_sales_basic_id =$sales_id )
				  ";
			
			if($this->db->execute($sql))
				return true;	 
			else
				return false;	   
				
	 }
	 
	function ETL_deleteSalesSummaryData($user_id,$year,$month,$province_id)
	 {		
	  		//delete details
	  		
	  		$sql ="delete sd.* from bi_monthly_sales_summary  sd, bi_monthly_sales_basic_info sb
				   where 
				   sb.user_id = $user_id
					and sb.sales_month= $month
					and sb.sales_year= $year
					and sb.province_id =$province_id
					
					and sd.monthly_sales_basic_id =sb.monthly_sales_basic_id
				  ";
			
			$this->db->execute($sql);
			
	  
			$sql ="delete from bi_monthly_sales_basic_info 
				   where 
				   user_id = $user_id
				   and sales_month= $month
				   and sales_year= $year
				   and province_id =$province_id
				  ";
			
			if($this->db->execute($sql))
				return true;	 
			else
				return false;	   
				
	 }
	 
	 function ETL_saleAnalysisData_addBasicInfo($customer_id,$user_id,$year,$month,$province_id)
	 {
			$current_user_id= F60DALBase::get_current_user_id();
			$current_user_id =15;
	      	$current_time=F60Date::sqlDateTime();
	  
	  	
	  		
	  		$sql="insert bi_monthly_sales_basic_info (customer_id, customer_name,lkup_store_type_id,billing_address_city,address,licensee_number,
			  											user_id,sales_year, sales_month,province_id,when_entered,created_user_id) 
				  select customer_id,customer_name,lkup_store_type_id,billing_address_city,concat_ws(\" \", concat_ws(\"-\", billing_address_unit, billing_address_street_number), billing_address_street) as address,licensee_number,
				  $user_id,$year,$month,$province_id ,'$current_time',$current_user_id
				  from customers
				  where customer_id = $customer_id";
	  		$this->db->execute($sql);
	  		
			$sales_id =$this->db->lastInsertId();
			
			for($i=0; $i<=3;$i++)
			{
			 
			 	$product_type_id = $i;
				$this->ETL_saleAnalysisData_addSalesSummary($sales_id,$customer_id,$user_id,$year,$month,$province_id,$product_type_id);
			
			}
			
		
	  		
	  		
			$this->ETL_cleanSalesSummaryData($sales_id); // clear summary details
		
	 }
	 
	 function ETL_add_rank($year,$month,$province_id)
	 {
			//update rank
				$sql="update bi_monthly_sales_basic_info basic, ab_customer_rank rank
				  set basic.rank = rank.rank
				  where basic.licensee_number = rank.licensee_no
				  and sales_year = $year
				  and sales_month =$month
				  and province_id =2
				  ";
	  		$this->db->execute($sql);
	}
	 
	 function ETL_saleAnalysisData_addSalesSummary($sale_id,$customer_id,$user_id,$year,$month,$province_id,$product_type_id)
	 {

	  		$current_user_id= F60DALBase::get_current_user_id();
			$current_user_id =15;
	      	$current_time=F60Date::sqlDateTime();
	  
			//BC Wine 
			if($product_type_id ==0)
			{
				$product_filter=" ssds_sales 
								where customer_id =$customer_id  and user_id =$user_id 
								and month(sale_date)=$month and year(sale_Date)=$year and province_id =$province_id
								and is_international=0
								";
			}
			else if ($product_type_id ==1) //International
			{
				$product_filter=" ssds_sales 
								 where customer_id =$customer_id  and user_id =$user_id 
								 and month(sale_date)=$month and year(sale_Date)=$year and province_id =$province_id						
								 and is_international=1 and product_id =1";	
			}
			else if ($product_type_id ==2) //beer
			{
				$product_filter=" ssds_sales s, beers b 
								where customer_id =$customer_id  and user_id =$user_id 
								and month(sale_date)=$month and year(sale_Date)=$year and province_id =$province_id
								and is_international=1 and product_id =2
								and b.beer_id =s.wine_id
								and b.lkup_beer_type_id <200
								";	
			}
			
			else if ($product_type_id ==3) //spirits
			{
				$product_filter=" ssds_sales s, beers b where customer_id =$customer_id  and user_id =$user_id 
								and month(sale_date)=$month and year(sale_Date)=$year and province_id =$province_id
								and is_international=1 and product_id =2
								and b.beer_id =s.wine_id
								and b.lkup_beer_type_id >=200
								";	
			}
			
	  		$sql="insert bi_monthly_sales_summary(monthly_sales_basic_id,lkup_product_type_id,
			  									  total_cases, total_case_value, total_retail,total_whole_sale,total_profit, when_entered,created_user_id) 
				  				select $sale_id, $product_type_id, ifnull(sum(cases_sold), 0) total_cases,ifnull(sum(cases_sold*case_value), 0) total_case_value,
				  									ifnull(sum(price_per_unit*unit_sales),0) total_retail,
				  									ifnull(sum(price_winery*unit_sales),0) total_whole_sale,
				  									ifnull(sum(profit_per_unit*unit_sales),0) total_profit,
				  									'$current_time',$current_user_id
				  									 from $product_filter";
				  									
			if($this->db->execute($sql))
				return true;	 
			else
				return false;
				  
				  
				  
	 }
	 
	 function getMonthlySaleInfo($user_id,$sale_month,$sale_year)
	 {
			$sql ="select sa.customer_id,sa.monthly_sales_basic_id,sa.customer_name,lktype.license_name store_type, sa.billing_address_city city,sa.address address,sa.licensee_number,lkp.product_name,
			  										
			  	sd.total_cases, sd.total_case_value, sd.total_whole_sale,total_retail,sd.total_profit, rank
														  
				from bi_monthly_sales_basic_info sa, bi_monthly_sales_summary sd, lkup_store_types lktype,lkup_products lkp
				
				where sa.monthly_sales_basic_id = sd.monthly_sales_basic_id
            
				and sa.lkup_store_type_id = lktype.lkup_store_type_id
				
        		and lkp.lkup_product_id = sd.lkup_product_type_id
        
		
				and sa.user_id = $user_id
				and sales_month= $sale_month
				and sales_year= $sale_year
                
        		order by customer_name,sa.licensee_number, product_name";	
				
							   					
			$rows = $this->db->getAll($sql);
			
			return $rows;
	 }
	 
	 function getMonthlySalesDetails($sales_id)
	 {
			$sql ="select * from bi_monthly_sales_summary where monthly_sales_basic_id = $sales_id";				   					
			$rows = $this->db->getAll($sql);
			
			return $rows;
	 }
	 
	 function getMonthlySummary($user_id,$sale_month,$sale_year)
	 {
			$sql ="select license_name, sum(total_cases) total_cases, sum(total_case_value) total_case_value, sum(total_whole_sale) total_whole_sale, 
					sum(total_retail) total_retail,sum(total_profit) total_profit
					
					from bi_monthly_sales_summary ss, bi_monthly_sales_basic_info sb,lkup_store_types lkt
					
					where ss.monthly_sales_basic_id =sb.monthly_sales_basic_id
					
					and sb.lkup_store_type_id = lkt.lkup_store_type_id
					
					and sb.user_id = $user_id
					and sales_month= $sale_month
					and sales_year= $sale_year
					
					group by sb.lkup_store_type_id
					
					order by sb.lkup_store_type_id";
					
						$rows = $this->db->getAll($sql);
			
			return $rows;
	 } 
	 
	 function getMonthlyGrandTotal($user_id,$sale_month,$sale_year)
	 {
			$sql ="select  sum(total_cases) total_cases, sum(total_case_value) total_case_value, sum(total_whole_sale) total_whole_sale, 
					sum(total_retail) total_retail,sum(total_profit) total_profit
					
					from bi_monthly_sales_summary ss, bi_monthly_sales_basic_info sb,lkup_store_types lkt
					
					where ss.monthly_sales_basic_id =sb.monthly_sales_basic_id
					
					and sb.lkup_store_type_id = lkt.lkup_store_type_id
					
					and sb.user_id = $user_id
					and sales_month= $sale_month
					and sales_year= $sale_year
					 ";
					
					$rows = $this->db->getAll($sql);
			
			return $rows;
	 }
	
	function getLastMthCMTotalInCurrentMth($user_id,$sale_month,$sale_year,$province_id=1)
	 {
	  
	  		if($sale_month ==1)
	  		{
				$last_month =12;
				$last_month_sale_year = $sale_year-1;
			}
			else
			{
				$last_month =$sale_month-1;
				$last_month_sale_year = $sale_year;
			}
			

			$sql ="select c1.customer_id   from (select a1.customer_id customer_id, ifnull(b1.customer_id,0) as cs_id from 

					(select  customer_id ,user_id from bi_monthly_sales_basic_info where sales_month =$sale_month and user_id =$user_id and sales_year= $sale_year) as a1
					
					left join (select  customer_id ,user_id from bi_monthly_sales_basic_info where sales_month =$last_month and sales_year= $last_month_sale_year 
								and user_id =$user_id) as b1
					
					on a1.customer_id=b1.customer_id ) as c1
					
					where c1.cs_id=0			
					
							
					
					 ";
					
					$rows = $this->db->getAll($sql);
			
			return $rows;
	 }	 
	 function getMonthlyTotalCMS($user_id,$sale_month,$sale_year,$totalTypes=0)
	 {
	  
	  		if($totalTypes == 0)//grand total
	  		{
					$sql ="select  count(*) totalRecs 
						from bi_monthly_sales_basic_info 
						where user_id = $user_id
						and sales_month= $sale_month
						and sales_year= $sale_year";
			}
			else
			{
			$sql ="select  sb.lkup_store_type_id,lt.license_name, count(*) totalRecs from bi_monthly_sales_basic_info sb,lkup_store_types lt
				where sb.lkup_store_type_id = lt.lkup_store_type_id
				
				and sb.user_id = $user_id
					and sales_month= $sale_month
					and sales_year= $sale_year
				
				group by lkup_store_type_id
				
				order by lkup_store_type_id";
			}			
					$rows = $this->db->getAll($sql);
			
			return $rows;
	 }
	 
	  function getMonthlyTotalBaseCMS($user_id,$province_id,$totalTypes=0)
	 {
	  
	  		if($totalTypes == 0)//grand total
	  		{
	  		 		if($province_id ==1)
	  		 		{
                        $sql =  "select  count(*) totalRecs 
                                from customers c, users_customers uc
                                
                                where c.customer_id = uc.customer_id
                                and c.lkup_store_type_id!=4
                                and c.lkup_store_type_id!=5	        				
                                and c.lkup_store_type_id<8
                                and uc.user_id =$user_id
                                and c.deleted =0
                                 
                                ";
					}
					else
					{
                        $sql =  "select  count(*) totalRecs 
                                from customers c, users_customers uc
                                
                                where c.customer_id = uc.customer_id						
                                and c.lkup_store_type_id=8
                                and uc.user_id =$user_id
                                and c.deleted =0 
                                ";
					}
			}
			else
			{
                $sql =  "select  c.lkup_store_type_id,lt.license_name, count(*) totalRecs from customers c,users_customers uc,lkup_store_types lt
                
                        where  c.customer_id = uc.customer_id
                        
                        and c.lkup_store_type_id = lt.lkup_store_type_id
                        
                        and uc.user_id = $user_id	
                        and c.lkup_store_type_id!=4
                        and c.lkup_store_type_id!=5
                        and c.lkup_store_type_id<8
                        and c.deleted =0
                        
                        group by lkup_store_type_id
                        
                        order by lkup_store_type_id";
			}			
					$rows = $this->db->getAll($sql);
			
			return $rows;
	 }
	 
 	function getTotalYears($province_id)
 	{
				$sql ="select  distinct sales_year 
                        from bi_monthly_sales_basic_info 
                        where province_id = $province_id 
                        and sales_year>2013
						order by sales_year desc";
					
					$rows = $this->db->getAll($sql);
					
				return $rows;
	}
	
	function getTotalMonth($province_id,$sales_year)
 	{
				$sql ="select  distinct sales_month 
                        from bi_monthly_sales_basic_info 
                        where province_id = $province_id
						and sales_year =$sales_year
				
						order by sales_month asc";
					
					$rows = $this->db->getAll($sql);
					
				return $rows;
	}
	
	function getConfigVal($config)
    {     	
        return $this->cfg[$config];
    }
    
	function emailAnaReport($report_year,$report_month,$province_id,$isAll=0)
    {
       
      		// Email from address
		$fromAddress=$this->getConfigVal("EMAIL_FROM_ADDRESS");
			  
     	$province=($province_id==1?"BC":"Alberta");
     	
        $subject = "$province Sales Summary Report";
        $emailContent = "<div style='font-family :verdana; font-size:9pt'>
		<P>Please find attached your Sales Summary Report for the previous month.</P>
	
		<P>Thanks,</P>

		<P>Christopher Stewart Online
		</p></div>";

	
	
	   import('Form60.exportreports.BI_excelMonthlySalesAnalysisReport');
       $excelReport = new BI_excelMonthlySalesAnalysisReport(true);
       
       $to="";
       $user_name="";
       $fileName ="";
       
     $bcc="helen@christopherstewart.com;garry@christopherstewart.com;";
      // $bcc="helen@christopherstewart.com;";
       
       	$users =$this->getAnaUsers($report_year,$report_month,$province_id,-1);
    
        if(count($users)==0) // if running report month's data is available
        {
            $displayMonth =str_pad($report_month,2,"0",STR_PAD_LEFT);
            echo "No sales data for $displayMonth - $report_year yet.";
            return false;
        }
        else
        {
            
            $i=0;
    		if($isAll==0)//individal user
    		{		 	
    		
    			
    			foreach($users as $user)
    			{
    				$user_id = $user["user_id"];				
    				
    				if($user_id!=13)
    				{
    					$fileName = $excelReport->generateReportSheet($report_month,$report_year,$province_id,$user_id);
    					$to = $user["email1"];
    					$user_name = $user["user_name"];
    					
    				//	print $user_name.' email:'.$to.' | ';
    			     /* $to="helen@christopherstewart.com;";
    			    $bcc ="";*/
    					if($to!=="")
                           	F60Common::_sendEmail($to , $bcc, $fromAddress, $subject, $emailContent, $fromAddress,$fileName);
                            
    			    }
    			}
                return true;
    		}
    		else
    		{	
    		
    			$fileName = $excelReport->generateReportSheet($report_month,$report_year,$province_id,-1);
    			
    			$to="chris@christopherstewart.com;tyler@christopherstewart.com;";		
				
				/*	   $to="helen@christopherstewart.com;";
    			    $bcc ="";
    */
    			if (file_exists($fileName))
    		    {		
    		         F60Common::_sendEmail($to , $bcc, $fromAddress, $subject, $emailContent, $fromAddress,$fileName);
                   
    		   	}
                   
                echo "Reports have been sent out, please check your email.";  
    		}
        }
        
    }
}


?>
