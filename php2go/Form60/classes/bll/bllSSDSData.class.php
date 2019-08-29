<?php
import('php2go.base.Php2Go');
import('php2go.data.PagedDataSet');
import('Form60.base.F60DbUtil');
require_once('Excel/reader.php');


class SSDSData extends Php2Go
{
    var $uploaddir;
    var $uploadfile;
    var $bcldb_uploadfile;
    var $dataFile;
    var $bcldb_dataFile;
    var $baseFileName;
    var $file_format_error = "";
    var $user_id;
    var $db;
    var $missingCustomers;
    var $missingWines;
    var $sale_year;
    var $sale_month;
    var $sale_date;
    
    var $isNewRule=false;
 
    
    function SSDSData()
    {
        $this->uploadfile = "";
        $this->bcldb_uploadfile = "";
        $this->uploaddir = "";
        $this->db = & Db::getInstance();
        $this->db->setFetchMode(ADODB_FETCH_ASSOC);
    }
    
    function setSales_UploadedFile($file_name,$bcldb_filename)
    {
     	$this->bcldb_uploadfile = $bcldb_filename;
        $this->bcldb_dataFile = $bcldb_filename;
 		 
        $this->uploadfile = $file_name;
        $this->dataFile = $file_name;
		 
        $this->uploaddir = dirname($this->uploadfile);
    }
        
    function setSales_DataFile($file_name,$bcldb_filename="")
    {
        $this->dataFile = $file_name;      
        $this->uploaddir = dirname($this->dataFile);
        $this->bcldb_dataFile = $bcldb_filename;     
    }
    
    function setUserID($user_id)
    {
        $this->user_id = $user_id;
    }
    
    function deleteUploadDir($province_id=1)
    {       
        if ($this->dataFile != "" && file_exists($this->dataFile)) unlink($this->dataFile);
        if ($this->uploadfile != "" && file_exists($this->uploadfile)) unlink($this->uploadfile);
        
		if($province_id!=2)
		{
			if ($this->bcldb_dataFile != "" && file_exists($this->bcldb_dataFile)) unlink($this->bcldb_dataFile);
		    if ($this->bcldb_uploadfile != "" && file_exists($this->bcldb_uploadfile)) unlink($this->bcldb_uploadfile);
		}
        
        if ($this->uploaddir != "" && file_exists($this->uploaddir)) rmdir($this->uploaddir);
    }
    
    function isValidAbFile()
    {
        $bRet = true;
      
        if(strtolower(substr($this->uploadfile, -3)) != "csv")
        {
            $this->file_format_error .= "Error: $this->baseFileName  Not a valid upload data file.";
            $bRet = false;
        }
        else
        {
            if ($bRet) 
                $bRet = $this->_getAbDateInfo();
        }
    
        return $bRet;
    }
    
    function _getAbDateInfo()
    {
     	$bRet = true;
    
        $handle = fopen($this->dataFile, "r");
        
        if(!$handle)
        {
            $this->file_format_error .= "Error: Unable to open the data file.";
            $bRet = false;
        }
        else
        {
            $data = fgetcsv($handle, 1000, ",");
            
            if(!$data) 
            {
                fclose ($handle);
                $this->file_format_error .= "Error: Unable to read the data file.";
                $bRet = false;
            }
            else
            {                
               	  $title = $data[0];
               
                    if(substr($title,"Sales for the month of")<0)
                    {
                        $this->file_format_error .= "Error: Unable to read the data from Channel sales file.";
                        $bRet=false;
                    }
	                else
	                {
	                    $title = $data[3];
	                 
	                   
	                    if($title=="")
	                    {
	                     
	                        $this->file_format_error .= "Error: Unable to read the data from Alberta licensee sales file.";
	                        $bRet=false;
	                    }
	                    else
	                    {    
	                        $n_date=explode("/",$data[3]);
	
							$this->sale_year = intval($n_date[1]);
							$this->sale_month = intval($n_date[0]);								
	                            
	                        fclose ($handle);	   	                       
	                    }
	                }
               }                             
        }
        
        return $bRet;
    }
    
    function isValidFile($isBCLDB)
    {
        $bRet = true;
      
        if(strtolower(substr($this->uploadfile, -3)) != "csv")
        {
            $this->file_format_error .= "Error: $this->baseFileName  Not a valid upload data file.";
            $bRet = false;
        }
        else
        {
            if ($bRet) 
                $bRet = $this->_getDateInfo($isBCLDB);
        }
    
        return $bRet;
    }
    
    function _getDateInfo($isBCLDB)
    {
     	$bRet = true;
        if(!$isBCLDB)
        {
            $handle = fopen($this->dataFile, "r");
        }
        else
        {
            $handle = fopen($this->bcldb_dataFile, "r");
        }
        if(!$handle)
        {
            $this->file_format_error .= "Error: Unable to open the data file.";
            $bRet = false;
        }
        else
        {
            $data = fgetcsv($handle, 1000, ",");
            
            if(!$data) 
            {
                fclose ($handle);
                $this->file_format_error .= "Error: Unable to read the data file.";
                $bRet = false;
            }
            else
            {
                if(!$isBCLDB) //licensee 
                {
                 	
                    $title = $data[0];
                 
                    if($title!="CUSTOMER_TYPE_CODE")
                    {
                        $this->file_format_error .= "Error: Unable to read the data from licensee sales file.";
                        $bRet=false;
                    }
                    else
                    {
	                    $title = $data[7];
	                    if($title!="CALENDAR_DATE")
	                    {
	                        $this->file_format_error .= "Error: Unable to read the data from licensee sales file.";
	                        $bRet=false;
	                    }
	                    else
	                    {
	                        $data = fgetcsv($handle, 1000, ",");
					
							$n_date=explode("/",$data[7]);
							$this->sale_year = intval($n_date[2]);
							$this->sale_month = intval($n_date[0]);									
	                        $this->sale_date="$this->sale_year"."$this->sale_month"."01";
	                        fclose($handle);
	                    }
	                  }
                }
                else //bcldb
                {
                 	  $title = $data[0];
                    if($title!="CHANNEL_NO")
                    {
                        $this->file_format_error .= "Error: Unable to read the data from Channel sales file.";
                        $bRet=false;
                    }
	               else
	                {
	                    $title = $data[5];
	                    if($title!="CALENDAR_DATE")
	                    {
	                        $this->file_format_error .= "Error: Unable to read the data from Channel sales file.";
	                        $bRet=false;
	                    }
	                    else
	                    {
	                        $data = fgetcsv($handle, 1000, ",");
	                        
	                        $n_date=explode("/",$data[5]);
							$year = intval($n_date[2]);
							$month = intval($n_date[0]);
									
	                       // $year = intval(substr($data[5],-4,4));
	                        //$month =intval(substr($data[5],0,2));
	                            
	                        fclose ($handle);
	   
	                        if($year==$this->sale_year && $month==$this->sale_month)
	                        {
	                            $bRet=true;
	                        }
	                        else
	                        {
	                            $this->file_format_error .= "Error: The BCLDB month is diffrent than the Customer sales file month.";
	                            $bRet = false;
	                        }
	                    }
	                  }
                }
            }                
        }
        
        return $bRet;
    }

    function generateCanadianWines()
    {
     
        $last_day_of_month = date("d", mktime(0, 0, 0, $this->sale_month+1, 0, $this->sale_year ));
        $last_date_of_month = $this->sale_year.$this->sale_month.$last_day_of_month; 
        $first_date_of_month = $this->sale_year.$this->sale_month.'01';
        
        $newGSTrule =0;
        
        if($this->sale_year>2015)	
        	$newGSTrule = 1;
        else 
		{
			if($this->sale_year==2015 & $this->sale_month>=4)
					$newGSTrule = 1;
		}	
        
 
        $sql= "SELECT
                        lkbt.size_value,
                        e.estate_id,e.estate_name,
                        odit.wine_id, odit.cspc_code,
                        lkst.lkup_store_type_id,
                        od.customer_id, od.customer_name, 
                        od.licensee_number, 
                        od.delivery_date,
                        ordered_quantity unit_sales, 
                        ordered_quantity/w.bottles_per_case cases, 
                        w.vintage,w.wine_name,w.case_value,
                       
                        odit.price_winery,odit.price_per_unit,ifnull(uc.user_id,0) user_id
                FROM `order_items`  odit,  wines w,estates e,lkup_bottle_sizes lkbt,customers c,lkup_store_types lkst,
                orders od
                left join users_customers uc on uc.customer_id = od.customer_id
                Where 
                month(od.delivery_date)='$this->sale_month' 
                and year(od.delivery_date) ='$this->sale_year'
                and odit.order_id=od.order_id and od.lkup_order_status_id=2 
                and w.wine_id=odit.wine_id
                and w.estate_id = e.estate_id
                and w.lkup_bottle_size_id = lkbt.lkup_bottle_size_id
                and c.customer_id = od.customer_id and c.lkup_store_type_id = lkst.lkup_store_type_id
                and od.deleted=0 and (odit.deleted =0 and odit.ordered_quantity<>0)                            
                order by e.estate_id desc";                 
                    
        $rows = $this->db->getAll($sql);
        $nRows = count($rows);
        
        if ($nRows <= 0)
        {
            $this->file_format_error .= "Error: Couldn't find Canadian wines sales for $this->sale_year $this->sale_month'.";
            $bRet = false;
        }
        else
        {
            for ($i=0;$i<$nRows; $i++)
            { 	 
             	$f60dbutil= new F60DbUtil();
               // $profit_per_unit= $f60dbutil->getProfits($rows[$i]['estate_id'],$rows[$i]['lkup_store_type_id'],$rows[$i]['price_winery'],$rows[$i]['size_value'],$rows[$i]['wine_id']);     
			    $profit_per_unit= $f60dbutil->getBCWineProfits4BCSales($rows[$i]['estate_id'],$rows[$i]['lkup_store_type_id'],$rows[$i]['wine_id']);              					               									
                $customer_id=$rows[$i]['customer_id'];
                $user_id=$rows[$i]['user_id'];
                $wine_id=$rows[$i]['wine_id'];
                $vintage=$rows[$i]['vintage'];
                $delivery_date="'".$rows[$i]['delivery_date']."'";
                $unit_sales=$rows[$i]['unit_sales'];
                $licensee_number=$rows[$i]['licensee_number'];
                $cspc_code=$rows[$i]['cspc_code'];
                $cases=$rows[$i]['cases'];
                $case_value=$rows[$i]['case_value'];
                $price_per_unit=$rows[$i]['price_per_unit'];
                $price_winery=$rows[$i]['price_winery'];
                $lkup_store_type_id=$rows[$i]['lkup_store_type_id'];
                $product_name=$rows[$i]['wine_name']." ".$rows[$i]['vintage'];    
				$product_name=str_replace("'","\'",$product_name)                ;
			
                $sql="insert into ssds_sales (product_name,
                                sale_date,customer_id,wine_id,user_id,
                                unit_sales,Licensee_No,SKUA,
                                cases_sold,profit_per_unit, case_value,
                                price_per_unit,price_winery,is_international,lkup_store_type_id,
                                when_entered,created_user_id
                                ) values ('$product_name',
                                $delivery_date,$customer_id,$wine_id,$user_id,
                                $unit_sales,'$licensee_number','$cspc_code',
                                $cases,$profit_per_unit,$case_value,
                                $price_per_unit,$price_winery,0,$lkup_store_type_id,
                                NOW(),$this->user_id)";
                
                $this->db->execute($sql);	
            }
        }
                    
    }

   
    function getUsersByDate($sale_month,$sale_year,$province_id=1)
    {
        $sql="SELECT distinct u.user_id, concat(u.first_name,' ',u.last_name) user_name from users u, user_sales_summary us
              where u.user_id =us.user_id and us.sale_year=$sale_year and us.sale_month=$sale_month
				  and us.province_id =$province_id and u.lkup_user_type_id=1 order by us.lkup_store_type_id desc, u.first_name ";

       $rows = $this->db->getAll($sql);
        if (count($rows) <= 0)
        {
              $this->file_format_error .= "Error: No sales for this month.";
              return 0;
        }
        else
        {
             return $rows;
        }
    }
    
    function getUsersByCommissionType($sale_month,$sale_year,$commission_type_id)
    {
        $sql="SELECT distinct u.user_id, concat(u.first_name,' ',u.last_name) user_name from users u, monthly_sales_commission_levels us
              where u.user_id =us.user_id and year(us.sale_date)=$sale_year and month(us.sale_date)=$sale_month
				  and us.lkup_sales_commission_type_id=$commission_type_id and level_number=0  order by u.first_name ";
				  
	 /* $sql="SELECT  distinct u.user_id, concat(u.first_name,' ',u.last_name) user_name 
			from users u, monthly_sales_commission_levels us , user_sales_summary s
			where u.user_id =us.user_id
			and s.sale_year=$sale_year and s.sale_month=$sale_month 
			and us.lkup_sales_commission_type_id=$commission_type_id
			and u.user_id=113
			and s.user_id= us.user_id
			
			and level_number=0 order by u.first_name";*/

       $rows = $this->db->getAll($sql);
        if (count($rows) <= 0)
        {
              $this->file_format_error .= "Error: No sales for this month.";
              return 0;
        }
        else
        {
             return $rows;
        }
    }
    
    function getCaUsersByMonth($sale_month,$sale_year)
    {
        $sql="SELECT distinct u.user_id, concat(u.first_name,' ',u.last_name) user_name from users u, user_sales_summary us
              where u.user_id =us.user_id and u.deleted=0 and  us.sale_year=$sale_year and us.sale_month=$sale_month  and us.lkup_store_type_id !=6 and us.lkup_store_type_id !=8 and us.user_id!=0 order by u.first_name";

       $rows = $this->db->getAll($sql);
        if (count($rows) <= 0)
        {
              $this->file_format_error .= "Error: No sales for this month!";
              return 0;
        }
        else
        {
             return $rows;
        }

    }
        
    function getAvialableMonthByYear($sale_year,$province_id=1)
    {    
        //$this->getFicscalYearRange($fiscal_year);
    	$sql="SELECT distinct sale_month from user_sales_summary where sale_year=$sale_year and province_id=$province_id order by sale_month ";       

	    $rows = $this->db->getAll($sql);
        if (count($rows) <= 0)
        {
              return 0;
        }
        else
        {
             return $rows;
        }     
    }
    
    function getUserInfoByUser_id($user_id)
    {
        $sql="SELECT user_id, concat(u.first_name,' ',u.last_name) user_name from users u  where u.user_id =$user_id and u.deleted=0";

       $rows = $this->db->getAll($sql);
        if (count($rows) <= 0)
        {
              $this->file_format_error .= "Error: No user.";
              return 0;
        }
        else
        {

             return $rows;
        }

    }
    
    function getTotalCasesById($user_id,$sale_month,$sale_year,$bonus_type,$commission_type_id ="")
    {
     	if($commission_type_id!="")
     	{
     	 	
			$sql="Select * from user_sales_summary where user_id=$user_id and sale_month=$sale_month and sale_year=$sale_year order by lkup_store_type_id asc";
		}
		else
	       // $sql="select * from user_sales_summary where user_id=$user_id and sale_month=$sale_month and sale_year=$sale_year and lkup_store_type_id=8";
	        $sql="select * from user_sales_summary where user_id=$user_id and sale_month=$sale_month and sale_year=$sale_year and lkup_store_type_id=$bonus_type";
        $rows = $this->db->getAll($sql);
        if (count($rows) <= 0)
        {
              $this->file_format_error .= "Error: No Sales.";
              return 0;
        }
        else
        {

             return $rows;
        }
    }
    
    function getTotalCandianCasesById($user_id,$sale_month,$sale_year)
    {
        $sql="Select * from user_sales_summary where user_id=$user_id and sale_month=$sale_month and sale_year=$sale_year and lkup_store_type_id!=6";
        $rows = $this->db->getAll($sql);
        if (count($rows) <= 0)
        {
            $this->file_format_error .= "Error: No Sales.";
            return 0;
        }
        else
        {
            return $rows;
        }
    }
    
    function getTargetCommissionLeveInfo($sale_month,$sale_year, $user_id="")
    {
     	if($this->isNewRule($sale_year,$sale_month))
			$SQL= "SELECT * FROM monthly_sales_commission_levels where level_number=0 and user_id=$user_id and month(sale_date) = $sale_month and year(sale_date)=$sale_year";
     	else
			$SQL= "SELECT * FROM month_commission_levels where level_id=1 and commission_rate=0 and lkup_store_type_id=-1 and sale_month = $sale_month and sale_year=$sale_year";
		
		$rows = $this->db->getAll($SQL);
        if (count($rows) <= 0)
        {
            $this->file_format_error .= "Error: Can't find target cases.";
            return 0;
        }
        else
        {
             return $rows;
        }
	}
	
    function import_salesData($province_id=1)
    {
      if($province_id==2)
      {
			$this->import_ab_monthy_Data();
		}
      else
      {
     		$this->import_monthy_Data(true);//bcldb
     		$this->import_monthy_Data(false);//licensee. lrs . agency  this must called after bcldb because it will save all data to ssds_sales table and other tables , then temp table will be cleared
     	}
        return true;
    }
	function import_ab_monthy_Data()
    {
       $bRet = true;
        
        //clear temp table at first time when run bcldb
    //    if($isBCLDB)
    //    	$this->db->execute("DELETE FROM ssds_sales_temp");
        
	//	if($isBCLDB)
	//		$handle = fopen($this->bcldb_dataFile, "r");
	//	else
	        $handle = fopen($this->dataFile, "r");
    
        $SQL = "INSERT INTO ssds_sales_temp (Licensee_No, SKUA, unit_sales, product_name, sale_date) VALUES ";
        
        if(!$handle)
        {
            $this->file_format_error .= "Error: unable to open the data file.";
            
            $bRet = false;
        }
        else
        {
         
            $data = fgetcsv($handle, 1000, ",");
            
            
            
            $n_date=explode("/",$data[3]);
            $this->sale_year =intval($n_date[1]);// substr($data[$index],-4,4);
            $this->sale_month =  intval($n_date[0]);//substr($data[$index],0,2);
                     
                
            $data = fgetcsv($handle, 1000, ","); 
          
            $data = fgetcsv($handle, 1000, ",");
         
           if($data[0]!="CSPC #" || $data[6]!="Store #")
           {
					fclose ($handle);
               $this->file_format_error .= "Error: Error reading data file.";
              
               return false;			
			  }
           
           // $title = $data[0];
          
            $row = 0;
            while(($data = fgetcsv($handle, 1000, ","))!==False)
            {
             
                if(!$data) 
                {
                    fclose ($handle);
                    $this->file_format_error .= "Error: Error reading data file.";
                   
                    $bRet = false;
                }   
                else
                {//Licensee_No, SKUA, unit_sales,product_name,sale_date
 				 		$sku =intval($data[0]);
				 		if(is_numeric($sku)&&$sku!="")
				 		{
				 		
				 	
	                        $row++;
                            if ($row == 1)
                            {
                             
        					         $sale_date = $this->sale_year."-".$this->sale_month."-01";
        					         
                             
                                    $product_name = str_replace("'","\'",$data[1]);
                                    $license_no = intval($data[6]);
                                    //$sku = intval($data[0]);
        									
        									$SQL .= "('$license_no', '$sku', $data[3],'$product_name','$sale_date')";
                                  //  $SQL .= "($data[0], $data[2], $data[4],'$product_name','$this->sale_date')";
                             
                            } 
                            //Licensee_No, SKUA, unit_sales,product_name,sale_date
                            else
                            {
                             	if($data[0]!=null) // some sales file were saved by unix sys which will save a null value at the end of rows
                             	{
        							   	//$n_date=explode("/",$data[$index]);
        									$sale_date = $this->sale_year."-".$this->sale_month."-01";
        								
        	                            $product_name = str_replace("'","\'",$data[1]);
        	                            //$product_name = str_replace("'","\'",$data[1]);
        	                            $license_no = intval($data[6]);
        	                            $sku = intval($data[0]);
        										
        										$SQL .= ",('$license_no', '$sku', $data[3],'$product_name','$sale_date')";
        	                           // $SQL .= ",($data[0], $data[2], $data[4],'$product_name','$this->sale_date')";
        	                       
        	                     }
                            }
						}
                }
            }
           
            $bRet = true;
            $bRet = $this->db->execute($SQL);
            fclose ($handle);    
        
            if ($bRet)
            {
                    $bRet = $this->createSummaryData(2);
                    if ($bRet)
                    {
                    
                        $this->getMissingCustomersWines(2);
                     }
            }
            else
            {
                $this->file_format_error .= "Error: importing row data.";
            }
        }       
        return $bRet;
    }
    
    
    
    function import_monthy_Data($isBCLDB)
    {
   
       $bRet = true;
        
        //clear temp table at first time when run bcldb
        if($isBCLDB)
        	$this->db->execute("DELETE FROM ssds_sales_temp");
        
		if($isBCLDB)
			$handle = fopen($this->bcldb_dataFile, "r");
		else
	        $handle = fopen($this->dataFile, "r");
    
        $SQL = " INSERT INTO ssds_sales_temp (Licensee_No, SKUA, unit_sales, product_name, sale_date) VALUES ";
        
        if(!$handle)
        {
            $this->file_format_error .= "Error: unable to open the data file.";
            $bRet = false;
        }
        else
        {
            $data = fgetcsv($handle, 1000, ",");
           
           
            $title = $data[0];
            $row = 0;
            
            while(($data = fgetcsv($handle, 1000, ","))!==False)
            {
                if(!$data) 
                {
                    fclose ($handle);
                    $this->file_format_error .= "Error: Error reading data file.";
                    $bRet = false;
                }
                else
                {//Licensee_No, SKUA, unit_sales,product_name,sale_date
                		if($isBCLDB)
                     		$index = 5;
                     	else
                     		$index=7;
                     		
				 		
                    $row++;
                    if ($row == 1)
                    {
                     	
  				 		$n_date=explode("/",$data[$index]);
                     	$this->sale_year =intval($n_date[2]);// substr($data[$index],-4,4);
                        $this->sale_month =  intval($n_date[0]);//substr($data[$index],0,2);
                    		
								//	$year = intval($n_date[2]);
								//	$month = intval($n_date[0]);
									
									
                        //$this->sale_date = $data[$index];
					    $sale_date = $this->sale_year."-".$this->sale_month."-".substr($data[$index],3,2);
					         
                        if(!$isBCLDB)
                        {
                            $product_name = str_replace("'","\'",$data[5]);
                            $license_no = intval($data[1]);
                            $sku = intval($data[4]);
                            $units= str_replace(",","",$data[6]);
									
									$SQL .= "('$license_no', '$sku', $units,'$product_name','$sale_date')";
									
									
                          //  $SQL .= "($data[1], $data[4], $data[6],'$product_name','$this->sale_date'				 )";
                        }
						else
						{
                            $product_name = str_replace("'","\'",$data[3]);
                            $license_no = intval($data[0]);
                            $sku = intval($data[2]);
                            $units= str_replace(",","",$data[4]);
									
							$SQL .= "('$license_no', '$sku', $units,'$product_name','$sale_date')";
                          //  $SQL .= "($data[0], $data[2], $data[4],'$product_name','$this->sale_date')";
                        }
                      
                    } 
                    //Licensee_No, SKUA, unit_sales,product_name,sale_date
                    else
                    {
                     	if($data[0]!=null) // some sales file were saved by unix sys which will save a null value at the end of rows
                     	{
						   	$n_date=explode("/",$data[$index]);
								$sale_date = $this->sale_year."-".$this->sale_month."-".substr($data[$index],3,2);
								if(!$isBCLDB)
								{
									 	
									 
		                            $product_name = str_replace("'","\'",$data[5]);
		                            $product_name = str_replace("'","\'",$data[5]);
		                            $license_no = intval($data[1]);
		                            $sku = intval($data[4]);
		                            $units= str_replace(",","",$data[6]);
										
									$SQL .= ",('$license_no', '$sku', $units,'$product_name','$sale_date')";
										
	                           // $SQL .= ",($data[1], $data[4], $data[6],'$product_name','$this->sale_date')";
	                        }
	                        else
	                        {
	                            $product_name = str_replace("'","\'",$data[3]);
	                            $product_name = str_replace("'","\'",$data[3]);
	                            $license_no = intval($data[0]);
	                            $sku = intval($data[2]);
	                            $units= str_replace(",","",$data[4]);
										
										$SQL .= ",('$license_no', '$sku', $units,'$product_name','$sale_date')";
	                           // $SQL .= ",($data[0], $data[2], $data[4],'$product_name','$this->sale_date')";
	                        }
	                     }
	                     if($row>10000)
	                     {
							$bRet = $this->db->execute($SQL);
							$row=0;
							$SQL=" INSERT INTO ssds_sales_temp (Licensee_No, SKUA, unit_sales, product_name, sale_date) VALUES ";
						 }
                    }
                }
            }
        
            $bRet = true;
           
           if($row!=10000)
	            $bRet = $this->db->execute($SQL);
	            
             fclose ($handle);
            
            //traceLog ($SQL); 
            if ($bRet)
            {
             	if(!$isBCLDB)
             	{
                    $bRet = $this->createSummaryData(1);
                    if ($bRet)
                    {
                        $this->getMissingCustomersWines(1);
                    }
                }
            }
            else
            {
                $this->file_format_error .= "Error: importing row data.";
            }
        }
        $bRet=true;
        return $bRet;
    }
    
   
	
	function renew_latest_vintage_wines($province_id=1)
	{
	 	 $bRet = true;
		 $SQL="delete from temp_lastest_wine_vintage";
		 
		 $bRet = $this->db->execute($SQL);
		 
		/* if($province_id ==1)
		 {
			 $SQL = "INSERT temp_lastest_wine_vintage(cspc_code, vintage)
						SELECT cspc_code, Max(vintage) FROM wines  
						WHERE wines.is_international=1 group by cspc_code";
		 }	
		 else
		 {*/
			$SQL = "INSERT temp_lastest_wine_vintage(cspc_code, vintage,wine_info_id)
					  SELECT winfo.cspc_code, Max(vintage),wine_info_id FROM wines_info winfo, wines w
					  where winfo.wine_id=w.wine_id
					  and winfo.province_id =$province_id
					  and winfo.deleted =0
					  and w.deleted =0
					  group by cspc_code";
	//	}

		$bRet = $this->db->execute($SQL);
		
		return $bRet;
	}
    
	function createSummaryData($province_id=1)
	{
	 
		$bRet = true;

        //clear old data
		$this->clearMonthData($province_id);
		
		if($province_id ==1)
			$this->generateCanadianWines();
			
		//if(w.bottles_per_case=6, wi.case_value/2,wi.case_value)
		
		$this->renew_latest_vintage_wines($province_id);	
			 
			 //$notAssignedId=1000;
			 //if(province_id ==1)
			 //	$notAssignedId=1000;
			
				$SQL = " insert ssds_sales (unit_sales, customer_id, user_id, wine_id, when_entered, created_user_id, modified_user_id,
			    		Licensee_No, SKUA, cases_sold, case_value, profit_per_unit, price_per_unit,is_international,product_name, sale_date,lkup_store_type_id,price_winery,province_id)
			        
					    select t.unit_sales, IFNULL(c.customer_id, 0), IFNULL(u.user_id, 0), IFNULL(w.wine_id, 0),
					    NOW(), $this->user_id, $this->user_id, t.Licensee_No, t.SKUA, t.unit_sales/w.bottles_per_case, wi.case_value, IFNULL(wi.profit_per_unit, 0),
					    IFNULL(wi.price_per_unit,0), 1,t.product_name,t.sale_date,IFNULL(c.lkup_store_type_id,0),IFNULL(wi.price_winery,0) ,$province_id
					    
					    From ssds_sales_temp t
					    
					    left outer join customers c on t.Licensee_NO = c.licensee_number and c.deleted=0  and c.licensee_number<>0 
                             and c.province_id = $province_id
					    left outer join users_customers u on u.customer_id = c.customer_id
			
					    left outer join temp_lastest_wine_vintage wv on wv.CSPC_code = t.SKUA 
	                	left outer join wines_info wi on wv.wine_info_id= wi.wine_info_id and wi.province_id=$province_id and wi.deleted=0
					 	left outer join wines w on wi.wine_id = w.wine_id and w.deleted =0
	                 
					    where Month(t.sale_date)=$this->sale_month 
					    and Year(t.sale_date) =$this->sale_year
				
				
					 ";
		//}
//				    NOW(), $this->user_id, $this->user_id, t.Licensee_No, t.SKUA, t.unit_sales/w.bottles_per_case, if(w.bottles_per_case=6, wi.case_value/2,wi.case_value), wi.profit_per_unit,

		$bRet = $this->db->execute($SQL);
		
		//udpate 3 wines that sold in costco || Are you crazy:1364 ; Bastide Miragflors: 1379 ; Riondo Spagp: 1159 ealier
        
        //udpate all wines that sold in costco || Bastide Miragflors: 1379 ; Riondo Spagp: 1159 //2018 June
		
       if($province_id==2)
        {
		$SQL="update customers c, ssds_sales s 

				set s.case_value=0
				
				where c.customer_id =s.customer_id
				
				and s.province_id = 2
				
				and month(s.sale_date)=$this->sale_month 
				and year(s.sale_date)=$this->sale_year
				and c.customer_name like 'costco%'

			";
            
			$bRet = $this->db->execute($SQL);
		}
        
		//update beer information
		$SQL ="update ssds_sales s, beers b, beers_info bf
				set 				
				s.wine_id = b.beer_id,
				s.unit_sales =(s.unit_sales*b.bottles_per_pack),
				s.cases_sold = (s.unit_sales*b.bottles_per_pack/b.bottles_per_case),
				s.case_value = (bf.case_value/bf.case_sold),
				s.profit_per_unit =bf.profit_per_unit,
				s.price_winery =bf.price_winery,
				s.price_per_unit =bf.price_per_unit,
				s.product_id=2
				
				where s.skua =bf.cspc_code
				
				and bf.beer_id = b.beer_id
				
				and  year(sale_date)=$this->sale_year
				and month(sale_date)=$this->sale_month
				and bf.province_id =$province_id
				and s.province_id =$province_id
			
			  ";
	
		$bRet = $this->db->execute($SQL);
		
		if($province_id ==1)
		{
			$sql_store_type ="s.lkup_store_type_id < 6 and s.lkup_store_type_id <> 8";
		}
		else if($province_id ==2)
		{
			$sql_store_type ="s.lkup_store_type_id = 8";
		}
		
		
		if ($bRet)
		{
		 	$store_type=-1;
		 	if($province_id==2)
		 	{
				$store_type=8;
			}
//* case_value
			$SQL = "insert user_sales_summary (user_id, total_units, total_canadian_cases, total_international_cases, total_cases, 
			       total_canadian_profit, total_international_profit, total_profit, avg_profit_per_case,sale_month,sale_year, 
			       total_sales,total_retail, lkup_store_type_id,province_id)
			       select user_id,
			       sum(unit_sales) total_units,
			       sum(if(is_international=0, cases_sold * case_value, 0)) as total_canadian_cases,
			       sum(if(is_international=1, cases_sold * case_value, 0)) as total_international_cases,
			       sum(cases_sold * case_value) as total_cases,
			       sum(if(is_international=0, unit_sales * profit_per_unit, 0)) as total_canadian_profit,
			       sum(if(is_international=1, unit_sales * profit_per_unit, 0)) as total_international_profit,
			       sum(unit_sales * profit_per_unit* case_value) as total_profit,
			       sum(unit_sales * profit_per_unit* case_value)/sum(cases_sold* case_value ) as avg_profit_per_case,
			       $this->sale_month,$this->sale_year, 
			       sum(unit_sales * price_winery) as total_sales, 
				   sum(unit_sales * price_per_unit) as total_retail,
					  
				   $store_type,s.province_id
		           from ssds_sales s
		           where s.customer_id <>0 and s.wine_id<>0 and Month(s.sale_date) = $this->sale_month and Year(s.sale_date) = $this->sale_year
		           and s.province_id=$province_id
		           and $sql_store_type
		           group by user_id";
		   $bRet = $this->db->execute($SQL);
		   
		   if($province_id ==1)
		   {
			   $SQL = "insert user_sales_summary (user_id, total_units, total_canadian_cases, total_international_cases, total_cases, 
				        total_canadian_profit, total_international_profit, total_profit, avg_profit_per_case,sale_month,sale_year, 
			           total_sales, total_retail,lkup_store_type_id)
			           select user_id,
			           sum(unit_sales) total_units,
			           sum(if(is_international=0, cases_sold * case_value, 0)) as total_canadian_cases,
			           sum(if(is_international=1, cases_sold * case_value, 0)) as total_international_cases,
			           sum(cases_sold * case_value) as total_cases,
			           sum(if(is_international=0, unit_sales * profit_per_unit, 0)) as total_canadian_profit,
			           sum(if(is_international=1, unit_sales * profit_per_unit, 0)) as total_international_profit,
			           sum(unit_sales * profit_per_unit* case_value) as total_profit,
			           sum(unit_sales * profit_per_unit* case_value)/sum(cases_sold* case_value) as avg_profit_per_case,
			           $this->sale_month,$this->sale_year, 
			           sum(unit_sales * price_winery) as total_sales,  sum(unit_sales * price_per_unit) as total_retail,
			           6
			           from ssds_sales s
			           where s.customer_id <>0 and s.wine_id<>0 and Month(s.sale_date) = $this->sale_month and Year(s.sale_date) = $this->sale_year
			           and lkup_store_type_id = 6
			           group by user_id";
			   $bRet = $this->db->execute($SQL);
			}
		   
		   if (!$bRet)
		   {
		       $this->file_format_error .= "Error: importing sales summary data.";
		       $this->clearMonthData($province_id);
		   }
		   else
		   {
		    	 $sqlWhere=" where user";
		    	 
				if($this->isNewRule($this->sale_year,$this->sale_month))
				{
				 	$sale_date="'$this->sale_year-$this->sale_month-01'";
				 	
					$SQL = "insert monthly_sales_commission_levels (sale_date,user_id,lkup_sales_commission_type_id,target_cases_ca, target_cases_intl,level_start_cases,level_end_cases,level_commission_rate,level_target_sales,level_commission_bonus,lkup_commission_sales_sum_type_id,level_caption,level_number,created_user_id, province_id)
			           select $sale_date,sc.user_id,lkup_sales_commission_type_id,target_cases_ca, target_cases_intl,level_start_cases,level_end_cases,level_commission_rate,level_target_sales,level_commission_bonus,lkup_commission_sales_sum_type_id,level_caption,level_number,$this->user_id,$province_id
			           from sales_commission_levels sc, users u
					   where sc.user_id=u.user_id and u.province_id=$province_id
					   ";
				}
				else
				{
				
			    	if($province_id==2)
			    	{
						 $sqlWhere = " where commission_levels.lkup_store_type_id=8 ";
					}
					else
					{
						$sqlWhere = " where commission_levels.lkup_store_type_id!=8 ";
					}
				
			       $SQL = "insert month_commission_levels (sale_year,sale_month,target_price,level_id, min_cases, max_cases, commission_rate, caption,
			           bonus, min_intl_cases, min_canadian_cases, when_entered, created_user_id, modified_user_id, lkup_store_type_id)
			           select $this->sale_year,$this->sale_month,target_price,level_id, min_cases, max_cases, commission_rate, caption,
			           bonus, min_intl_cases, min_canadian_cases, NOW(), $this->user_id, $this->user_id, lkup_store_type_id
			           from commission_levels
			           $sqlWhere
			           ";
		        }
		       $bRet = $this->db->execute($SQL);
		       if (!$bRet)
		       {
		           $this->file_format_error .= "Error: importing sales commission data.";
		           $this->clearMonthData($province_id);
		       }
		   }
		}
		else
		{
		   $this->file_format_error .= "Error: importing sales data.";
		   $this->clearMonthData($province_id);
		}
		
		//clear temp table
		$this->db->execute("DELETE FROM ssds_sales_temp");
		
		return $bRet;
	}
	
	function isNewRule($saleY, $saleM)
	{
	 	if(strlen($saleM)==1)
	 	{
			$saleM="0$saleM";
		}
		
		
		$saleDate ="$saleY-$saleM-01";
		$newRuleDate="2011-07-31";
		
	
	//	return true;   // for debug, to be deleted after debug
		
		if($saleDate>$newRuleDate)
		{
			
			return true;
		}
		else
		{
		 
			return false;
		}
	}
    
    function getMissingCustomersWines($province_id=1)
    {
     		if($province_id==1)
     		{
        		$SQL = "select distinct licensee_no from ssds_sales where customer_id = 0 and YEAR(sale_date)=$this->sale_year and MONTH(sale_date)=$this->sale_month and province_id=1";
        	}else if($province_id==2)
        	{
				$SQL = "select distinct licensee_no from ssds_sales where customer_id = 0 and YEAR(sale_date)=$this->sale_year and MONTH(sale_date)=$this->sale_month and province_id =2 ";	
			}
        
        $customers = $this->db->getAll($SQL);
        
        if($province_id==1)
     		{
        		$SQL = "select distinct SKUA from ssds_sales where wine_id = 0 and YEAR(sale_date)=$this->sale_year and MONTH(sale_date)=$this->sale_month and province_id=1";
        		
        	}
        	else
        	{
				$SQL = "select distinct SKUA from ssds_sales where wine_id = 0 and YEAR(sale_date)=$this->sale_year and MONTH(sale_date)=$this->sale_month and province_id =2";
			}
        $wines = $this->db->getAll($SQL);
        
        $this->missingCustomers = $this->implode_assoc_r("", ", ", $customers);
        $this->missingWines = $this->implode_assoc_r("", ", ", $wines);
        
    }
    
    function implode_assoc_r($inner_glue = "=", $outer_glue = "\n", $array = null, $keepOuterKey = false)
    {
        $output = array();
        foreach( $array as $key => $item )
        if ( is_array ($item) )
        {
        if ( $keepOuterKey )
        $output[] = $key;
        // This is value is an array, go and do it again!
        $output[] = $this->implode_assoc_r ($inner_glue, $outer_glue, $item, $keepOuterKey);
        }
        else
        $output[] = $inner_glue . $item;
        return implode($outer_glue, $output);
    }

    
    function regenerateSummaryData($sale_month,$sale_year,$province_id)
    {
     		$this->sale_month = $sale_month;
     		$this->sale_year = $sale_year;
     		
        $bRet = true;
        
        $this->db->execute("DELETE FROM ssds_sales_temp");
        
        if($province_id==1)
        {
        $SQL = "INSERT ssds_sales_temp (Licensee_No, SKUA, product_name, sale_date, unit_sales)
                SELECT s.Licensee_No, s.SKUA,  s.product_name,s.sale_date, s.unit_sales FROM ssds_sales s

                where is_international=1 and MONTH(s.sale_date) = $sale_month and YEAR(s.sale_date) = $sale_year
					 and lkup_store_type_id!=8 ";
			}
        $bRet = $this->db->execute($SQL);

        if (!$bRet)
        {
            $this->file_format_error .= "Error: Recalculating sales data.";
        }
        else
            $bRet = $this->createSummaryData($province_id);
            
        return $bRet;
    }
    
    function clearMonthData($province_id=1)
    {
     
     	if(!$this->isNewRule($this->sale_year,$this->sale_month))
     	{
     	
	    	  if($province_id==2)
	    	  {
					$this->db->execute("DELETE FROM ssds_sales where YEAR(sale_date) = $this->sale_year and MONTH(sale_date)=$this->sale_month and (lkup_store_type_id=8 or province_id =2)");
			        $this->db->execute("DELETE FROM user_sales_summary where sale_year = $this->sale_year and sale_month=$this->sale_month and (lkup_store_type_id=8 or province_id =2)");
			        
			        $this->db->execute("DELETE FROM month_commission_levels where sale_year = $this->sale_year and sale_month=$this->sale_month and lkup_store_type_id=8  ");
				}
				else
				{
			        $this->db->execute("DELETE FROM ssds_sales where YEAR(sale_date) = $this->sale_year and MONTH(sale_date)=$this->sale_month and  province_id =$province_id");
			        $this->db->execute("DELETE FROM user_sales_summary where sale_year = $this->sale_year and sale_month=$this->sale_month and province_id =$province_id");
			        
			        
			        $this->db->execute("DELETE FROM month_commission_levels where sale_year = $this->sale_year and sale_month=$this->sale_month and lkup_store_type_id!=8  ");
		        }
        
        }
    	else
    	{
        
           $this->db->execute("DELETE FROM ssds_sales where YEAR(sale_date) = $this->sale_year and MONTH(sale_date)=$this->sale_month and  province_id =$province_id");
	        $this->db->execute("DELETE FROM user_sales_summary where sale_year = $this->sale_year and sale_month=$this->sale_month and province_id =$province_id");
	        $this->db->execute("DELETE FROM monthly_sales_commission_levels where YEAR(sale_date) = $this->sale_year and MONTH(sale_date)=$this->sale_month and province_id=$province_id");
	        
	    }
    }
    
    function clearData4Recreate($province_id=1)
    {
     	if($this->isNewRule($this->sale_year,$this->sale_month))
     	{
		     if($province_id ==1)
		     {
		        $this->db->execute("DELETE FROM user_sales_summary where sale_year = $this->sale_year and sale_month=$this->sale_month");
		        $this->db->execute("DELETE FROM month_commission_levels where sale_year = $this->sale_year and sale_month=$this->sale_month");
		      }
		      else
		      {
					$this->db->execute("DELETE FROM user_sales_summary where sale_year = $this->sale_year and sale_month=$this->sale_month and lkup_store_type_id =8");
		        $this->db->execute("DELETE FROM month_commission_levels where sale_year = $this->sale_year and sale_month=$this->sale_month and lkup_store_type_id =8");
			}
		}
		else
		{
			$this->db->execute("DELETE FROM user_sales_summary where sale_year = $this->sale_year and sale_month=$this->sale_month and province_id =$province_id");
		        $this->db->execute("DELETE FROM monthly_sales_commission_levels where sale_year = $this->sale_year and sale_month=$this->sale_month and province_id =$province_id");
		}
    }
	function GetSpecialSalesReport($sale_month,$sale_year, $sheetType,$province_id =1, $reportType=1, $pageSize = 99999, $page = 1)
    {  	
        //print hh."000".$store_type_id;
//        $user_id_filter = ($user_id==-1?"s.user_id":$user_id); //-1 means select all

		$user_id_filter="";
		
		$licensee_filter="";
		
	 	if($sheetType==1) //not Assgined
			$user_id_filter ="and s.user_id = 0 and s.licensee_no != 30028400";
		if($sheetType==2) //sample
			$licensee_filter ="and s.licensee_no = 30028400";
		if($sheetType==3) //NWT
			$licensee_filter ="and s.licensee_no = 40080400";
		if($sheetType==4) //saska
			$licensee_filter ="and (s.licensee_no = 40088000 or s.licensee_no = 40081000) ";
        if($sheetType==5) //yukon
			$licensee_filter ="and (s.licensee_no = 40081100) ";
        $province_filter="and s.province_id=$province_id";

        if ($reportType == 1) //summary report -- for HTML
        {
            $SQL = "SELECT s.user_id, s.customer_id, s.licensee_no, l.license_name type, l.license_name, c.customer_name, c.billing_address_city as city,
					concat_ws(\" \", concat_ws(\"-\", c.billing_address_unit, c.billing_address_street_number), c.billing_address_street) as address,
					sum(cases_sold * s.case_value) as total_cases,
					sum(cases_sold) as cases,
					sum(unit_sales) total_bts_sold, sum(unit_sales * profit_per_unit* s.case_value) as total_profit,
					sum(unit_sales * price_winery) as total_sales, 
					sum(unit_sales * price_per_unit) as rt_sales, 
					s.is_international 
					from ssds_sales s
					inner join customers c on c.customer_id = s.customer_id and c.deleted =0
					inner join lkup_store_types l on c.lkup_store_type_id = l.lkup_store_type_id
					where s.customer_id <>0 and s.wine_id<>0 and MONTH(sale_date)=$sale_month and YEAR(sale_date)=$sale_year
					$user_id_filter
					
					$province_filter
					
					group by customer_id, user_id
					order by user_id, total_sales desc,  customer_name,c.licensee_number asc
                 	";
        }
      
        else if ($reportType == 2) //detailed report -- for Excel
        {// wine ; then beer
            $SQL = "SELECT  s.user_id, s.customer_id, s.licensee_no, l.license_name, c.customer_name, c.billing_address_city as city,
					concat_ws(\" \", concat_ws(\"-\", c.billing_address_unit, c.billing_address_street_number), c.billing_address_street) as address,
					s.SKUA, s.wine_id, w.wine_name, b.size_value as liters, cl.caption as type, s.is_international,
					e.billing_address_country as country, sum(unit_sales) as bottles_sold,
					round(sum(cases_sold * s.case_value),2) as cases_sold,
					sum(cases_sold ) as cases,
					sum(unit_sales * s.profit_per_unit* s.case_value) as total_profit,
					sum(unit_sales * s.price_winery) as total_sales,
					sum(unit_sales * s.price_per_unit) as rt_sales,
					s.profit_per_unit as profit_per_bottle
					from ssds_sales s
					inner join customers c on c.customer_id = s.customer_id
					inner join lkup_store_types l on c.lkup_store_type_id = l.lkup_store_type_id
					inner join wines w on w.wine_id = s.wine_id and s.product_id=1
					inner join lkup_bottle_sizes b on w.lkup_bottle_size_id = b.lkup_bottle_size_id
					inner join estates e on w.estate_id=e.estate_id
					inner join lkup_wine_color_types cl on w.lkup_wine_color_type_id = cl.lkup_wine_color_type_id
					where s.customer_id <>0 and s.wine_id<>0 and MONTH(sale_date)=$sale_month and YEAR(sale_date)=$sale_year
					$user_id_filter
					$licensee_filter
					$province_filter	
						
					and s.product_id=1			
					group by customer_id, user_id, wine_id
					
						
					union
					
					SELECT  s.user_id, s.customer_id, s.licensee_no, l.license_name, c.customer_name, c.billing_address_city as city,
					concat_ws(\" \", concat_ws(\"-\", c.billing_address_unit, c.billing_address_street_number), c.billing_address_street) as address,
					s.SKUA, s.wine_id, w.beer_name wine_name, b.size_value as liters, cl.caption as type, s.is_international,
					e.billing_address_country as country, sum(unit_sales) as bottles_sold,
					round(sum(cases_sold * s.case_value),2) as cases_sold,
					sum(cases_sold) as cases,
					 sum(unit_sales * s.profit_per_unit* s.case_value) as total_profit,
					sum(unit_sales * s.price_winery) as total_sales,
					sum(unit_sales * s.price_per_unit) as rt_sales,
					s.profit_per_unit as profit_per_bottle
					from ssds_sales s
					inner join customers c on c.customer_id = s.customer_id
					inner join lkup_store_types l on c.lkup_store_type_id = l.lkup_store_type_id
					inner join beers w on w.beer_id = s.wine_id and s.product_id=2
					inner join lkup_beer_sizes b on w.lkup_beer_size_id = b.lkup_beer_size_id
					inner join estates e on w.estate_id=e.estate_id
					inner join lkup_beer_types cl on w.lkup_beer_type_id = cl.lkup_beer_type_id
					where s.customer_id <>0 and s.wine_id<>0 and MONTH(sale_date)=$sale_month and YEAR(sale_date)=$sale_year
					$user_id_filter
					$licensee_filter
					$province_filter
					and s.product_id =2					
					group by customer_id, user_id, wine_id
					
					order by user_id asc,  customer_name asc, licensee_no asc, wine_name asc,country asc, is_international desc
					";
					
        }
        
        $pagedRS = & PagedDataSet::getInstance("db");
        
        
      // $pageSize = 13; //delete when finish the debug
        
        $pagedRS->setPageSize($pageSize);
        $pagedRS->setCurrentPage($page);
        if (!$pagedRS->load($SQL))
        {
            $this->file_format_error .= "Error: unable to get report data.";
            $bRet = false;
            return false;
        }
        $rs["total_records"]=$pagedRS->getTotalRecordCount();
        
        $rs["sales_details"] = $pagedRS;
        //* case_value
      $SQL = "select sum(if(is_international=0, cases_sold * case_value, 0)) as total_canadian_cases,
	                    sum(if(is_international=1, cases_sold * case_value, 0)) as total_international_cases,
	                    sum(if(is_international=1, cases_sold, 0)) as total_international_real_cases,
	                    sum(cases_sold * case_value) as total_cases,
	                    sum(cases_sold) as total_real_cases,
	                    sum(unit_sales) as total_units,
	                    sum(unit_sales * profit_per_unit* case_value) as total_profit,
	                    (sum(unit_sales * profit_per_unit* case_value)/sum(cases_sold * case_value)) as avg_profit_per_case,
	                    sum(unit_sales *price_per_unit) total_retail,
	                    sum(unit_sales *price_winery) total_wholesale
	                    from ssds_sales s
	                    where s.customer_id <>0 and s.wine_id<>0 and MONTH(s.sale_date)=$sale_month and YEAR(s.sale_date)=$sale_year
	                    $user_id_filter
	                    $licensee_filter
	  					$province_filter          
	                    group by user_id
	                    order by user_id
	                ";
	      
	    
      
        $rs["summary_details"] = $this->db->getAll($SQL);
        
       
        
        return $rs;
        
    }
	function GetSpecialSalesSummery($sale_month,$sale_year, $sheetType,$province_id =1, $reportType=1, $pageSize = 99999, $page = 1)
    {  	
        //print hh."000".$store_type_id;
//        $user_id_filter = ($user_id==-1?"s.user_id":$user_id); //-1 means select all

		$user_id_filter="";
		
		$licensee_filter="";
		
	 	if($sheetType==1) //not Assgined
			$user_id_filter ="and s.user_id = 0";
		if($sheetType==2) //sample
			$licensee_filter ="and s.licensee_no = 30028400";
		if($sheetType==3) //NWT
			$licensee_filter ="and s.licensee_no = 40080400";
		if($sheetType==4) //saska
			$licensee_filter ="and (s.licensee_no = 40088000 or s.licensee_no = 40081000) ";
        if($sheetType==5) //yukon
			$licensee_filter ="and (s.licensee_no = 40081100) ";
			
        $province_filter="and s.province_id=$province_id";
      
        //* case_value
        $SQL = "select sum(if(is_international=0, cases_sold * case_value, 0)) as total_canadian_cases,
	                    sum(if(is_international=1, cases_sold * case_value, 0)) as total_international_cases,
	                    sum(cases_sold * case_value) as total_cases,
	                    sum(unit_sales) as total_units,
	                    sum(unit_sales * profit_per_unit* case_value) as total_profit,
	                    (sum(unit_sales * profit_per_unit* case_value)/sum(cases_sold* case_value )) as avg_profit_per_case,
	                    sum(unit_sales *price_per_unit) total_retail,
	                    sum(unit_sales *price_winery) total_wholesale
	                    from ssds_sales s
	                    where s.customer_id <>0 and s.wine_id<>0 and MONTH(s.sale_date)=$sale_month and YEAR(s.sale_date)=$sale_year
	                    $user_id_filter
	                    $licensee_filter
	  					$province_filter          
	                    group by user_id
	                    order by user_id
	                ";	    
        $rs = $this->db->getAll($SQL);
        
        return $rs;
        
    }
 
    function GetSalesReport($sale_month,$sale_year, $user_id=-1, $store_type_id=-1, $reportType=1, $pageSize = 99999, $page = 1)
    {  	

		
		$user_id_filter = ($user_id==-1?"s.user_id":$user_id); //-1 means select all
     


	 	$store_type_filter ="";

	 	if(!$this->isNewRule($sale_year,$sale_month))
	 	{
	        $store_type_filter = "and s.lkup_store_type_id ".($store_type_id==-1?" <> 6 ":" =$store_type_id"); //-1 means select all
			
	         if($store_type_id == 8)
			{
				 $province_filter =" and s.province_id=2 ";
			}
			else
			{
				 $province_filter =" and s.province_id=1 ";
			}
	    }
	    else
	    {
		 	$commissionType = $this->getUserBonusType($user_id,-1,$sale_year,$sale_month);
			
			$province_filter =" and s.province_id=1 ";
			if($commissionType==4)
			{
				$province_filter =" and s.province_id=2 ";
			}		   
		}
       
                
        $SQL = "select distinct u.user_id, concat_ws(\" \", u.first_name, u.last_name) as name
	            from user_sales_summary s
	            inner join users u on s.user_id = u.user_id
	            where s.user_id = $user_id_filter and s.sale_month = $sale_month and s.sale_year=$sale_year
	            $store_type_filter 
	            order by name
            ";
            
        $rs["user_details"] = $this->db->getAll($SQL);
        
          
        if (count($rs["user_details"]) <= 0)
        {
            $this->file_format_error .= "Error: data doesn't exist for the specified month.";
            $bRet = false;
            return false;
        }
        
        $SQL = "select lkup_store_type_id, caption, license_name
            from lkup_store_types
            ";
        $rs["store_type_details"] = $this->db->getAll($SQL);
        
    
     
        if ($reportType == 1) //summary report -- for HTML
        {
            $SQL = "SELECT s.user_id, s.customer_id, s.licensee_no, l.license_name type, l.license_name, c.customer_name, c.billing_address_city as city,
					concat_ws(\" \", concat_ws(\"-\", c.billing_address_unit, c.billing_address_street_number), c.billing_address_street) as address,
					sum(cases_sold * case_value) as total_cases,sum(unit_sales) total_bts_sold, sum(unit_sales * profit_per_unit) as total_profit,
					sum(unit_sales * price_winery) as total_sales, 
					sum(unit_sales * price_per_unit) as rt_sales, 
					s.is_international 
					from ssds_sales s
					inner join customers c on c.customer_id = s.customer_id and c.deleted =0
					inner join lkup_store_types l on c.lkup_store_type_id = l.lkup_store_type_id
					where s.customer_id <>0 and s.wine_id<>0 and MONTH(sale_date)=$sale_month and YEAR(sale_date)=$sale_year
					and s.user_id = $user_id_filter
					$store_type_filter
					$province_filter
					
					group by customer_id, user_id
					order by user_id, total_sales desc,  customer_name,c.licensee_number asc
                 	";
        }
      
        else if ($reportType == 2) //detailed report -- for Excel
        {// wine ; then beer
            $SQL = "SELECT  s.user_id, s.customer_id, s.licensee_no, l.license_name, c.customer_name, c.billing_address_city as city,
					concat_ws(\" \", concat_ws(\"-\", c.billing_address_unit, c.billing_address_street_number), c.billing_address_street) as address,
					s.SKUA, s.wine_id, w.wine_name, b.size_value as liters, cl.caption as type, s.is_international,
					e.billing_address_country as country, sum(unit_sales) as bottles_sold,
					round(sum((unit_sales/w.bottles_per_case) * s.case_value),2) as cases_sold,
					round(sum((unit_sales/w.bottles_per_case)),2) as cases,
					sum(unit_sales * s.profit_per_unit) as total_profit,
					sum(unit_sales * s.price_winery) as total_sales,
					sum(unit_sales * s.price_per_unit) as rt_sales,
					s.profit_per_unit as profit_per_bottle
					from ssds_sales s
					inner join customers c on c.customer_id = s.customer_id and c.deleted =0
					inner join lkup_store_types l on c.lkup_store_type_id = l.lkup_store_type_id
					inner join wines w on w.wine_id = s.wine_id and s.product_id=1
					inner join lkup_bottle_sizes b on w.lkup_bottle_size_id = b.lkup_bottle_size_id
					inner join estates e on w.estate_id=e.estate_id
					inner join lkup_wine_color_types cl on w.lkup_wine_color_type_id = cl.lkup_wine_color_type_id
					where s.customer_id <>0 and s.wine_id<>0 and MONTH(sale_date)=$sale_month and YEAR(sale_date)=$sale_year
					and	s.user_id = $user_id_filter
					$store_type_filter
					$province_filter	
						
					and s.product_id=1			
					group by customer_id, user_id, wine_id
					
					
					union
					
					SELECT  s.user_id, s.customer_id, s.licensee_no, l.license_name, c.customer_name, c.billing_address_city as city,
					concat_ws(\" \", concat_ws(\"-\", c.billing_address_unit, c.billing_address_street_number), c.billing_address_street) as address,
					s.SKUA, s.wine_id, w.beer_name wine_name, b.size_value as liters, cl.caption as type, s.is_international,
					e.billing_address_country as country, sum(unit_sales) as bottles_sold,
					round(sum(cases_sold * s.case_value),2) as cases_sold, 
					round(sum(cases_sold),2) as cases, 
					sum(unit_sales * s.profit_per_unit) as total_profit,
					sum(unit_sales * s.price_winery) as total_sales,
					sum(unit_sales * s.price_per_unit) as rt_sales,
					s.profit_per_unit as profit_per_bottle
					from ssds_sales s
					inner join customers c on c.customer_id = s.customer_id and c.deleted =0
					inner join lkup_store_types l on c.lkup_store_type_id = l.lkup_store_type_id
					inner join beers w on w.beer_id = s.wine_id and s.product_id=2
					inner join lkup_beer_sizes b on w.lkup_beer_size_id = b.lkup_beer_size_id
					inner join estates e on w.estate_id=e.estate_id
					inner join lkup_beer_types cl on w.lkup_beer_type_id = cl.lkup_beer_type_id
					where s.customer_id <>0 and s.wine_id<>0 and MONTH(sale_date)=$sale_month and YEAR(sale_date)=$sale_year
					and	s.user_id = $user_id_filter
					$store_type_filter
					$province_filter
					and s.product_id =2					
					group by customer_id, user_id, wine_id
					
					order by user_id asc,  customer_name asc, licensee_no asc, wine_name asc,country asc, is_international desc
					
					
					";
					
        }
        
        $pagedRS = & PagedDataSet::getInstance("db");
        
        
      // $pageSize = 13; //delete when finish the debug
        
        $pagedRS->setPageSize($pageSize);
        $pagedRS->setCurrentPage($page);
        if (!$pagedRS->load($SQL))
        {
            $this->file_format_error .= "Error: unable to get report data.";
            $bRet = false;
            return false;
        }
        
        $rs["total_records"]=$pagedRS->getTotalRecordCount();
        
        $rs["sales_details"] = $pagedRS;
        
        //get totals
        if($this->isNewRule($sale_year,$sale_month))
        {
         	  
	          $SQL = "select s.user_id, s.total_cases, s.total_units,  s.total_canadian_profit, s.total_international_profit,
	                    s.total_profit, avg_profit_per_case,
	                    s.total_canadian_cases, s.total_international_cases, s.total_sales, s.total_retail
	                    from user_sales_summary s
	                    where s.user_id = $user_id_filter	  
			
	                    and s.sale_month=$sale_month and s.sale_year=$sale_year
	                    order by user_id,s.total_canadian_cases desc
	                ";
	        
	    /*    else
	        {
	            $SQL = "select user_id, sum(if(is_international=0, cases_sold * case_value, 0)) as total_canadian_cases,
	                    sum(if(is_international=1, cases_sold * case_value, 0)) as total_international_cases,
	                    sum(cases_sold * case_value) as total_cases,
	                    sum(unit_sales) as total_units,
	                    sum(unit_sales * profit_per_unit) as total_profit,
	                    (sum(unit_sales * profit_per_unit)/sum(cases_sold * case_value)) as avg_profit_per_case
	                    from ssds_sales s
	                    inner join customers c on c.customer_id = s.customer_id
	                    where s.customer_id <>0 and s.wine_id<>0 and MONTH(s.sale_date)=$sale_month and YEAR(s.sale_date)=$sale_year
	                    $store_type_filter and s.user_id = $user_id_filter         
	  					$province_filter          
	                    group by user_id
	                    order by user_id
	                ";
	        }*/
	    }
	    else // old rule
	    {
	     //     and s.lkup_store_type_id!=6
			 if ($store_type_id == -1 ||$store_type_id == 6 ||$store_type_id == 8)//all stores add commission details
	        {
	            $SQL = "select s.user_id, s.total_cases, s.total_units,  s.total_canadian_profit, s.total_international_profit,
	                    s.total_profit, s.avg_profit_per_case,
	                    s.total_canadian_cases, s.total_international_cases, s.total_sales, s.total_retail
	                    from user_sales_summary s
	                    and lkup_store_type_id <>6
	                    where s.user_id = $user_id_filter
	                  
	                    $store_type_filter
	               
	                    and s.sale_month=$sale_month and s.sale_year=$sale_year
	                    $province_filter 
	                    group by user_id
	                    order by user_id
	                ";
	        }
	        else  // old rule
	        {
	            $SQL = "select user_id, sum(if(is_international=0, cases_sold * case_value, 0)) as total_canadian_cases,
	                    sum(if(is_international=1, cases_sold * case_value, 0)) as total_international_cases,
	                    sum(cases_sold * case_value) as total_cases,
	                    sum(unit_sales) as total_units,
	                    sum(unit_sales * profit_per_unit) as total_profit,
	                    (sum(unit_sales * profit_per_unit)/sum(cases_sold * case_value)) as avg_profit_per_case
	                    from ssds_sales s
	                    inner join customers c on c.customer_id = s.customer_id
	                    where s.customer_id <>0 and s.wine_id<>0 and MONTH(s.sale_date)=$sale_month and YEAR(s.sale_date)=$sale_year
	                    $store_type_filter and s.user_id = $user_id_filter         
	  					$province_filter          
	                    group by user_id
	                    order by user_id
	                ";
	        }
		}
	    //   and s.lkup_store_type_id !=6
      
        $rs["summary_details"] = $this->db->getAll($SQL);
        

        
        if($commissionType==2)// BCLDB
        {
             $SQL ="select   sum(if(is_international=1, cases_sold, 0)) as total_international_real_cases,
	                    sum(cases_sold) as total_real_cases
	                    from ssds_sales s
	                    where s.customer_id <>0 and s.wine_id<>0 and MONTH(s.sale_date)=$sale_month and YEAR(s.sale_date)=$sale_year
	                    and s.user_id = $user_id_filter
	            and lkup_store_type_id = 6
	  					$province_filter          
	                    group by user_id
	                    order by user_id";
        }      
        else if($commissionType==3)// BCLDB & licensee
        {
            // updat pn Dec 07, for BCLDB and Licensee type, we display total case value and case value total for No bcldb store types
             /*$SQL ="select   sum(if(is_international=1, cases_sold, 0)) as total_international_real_cases,
	                    sum(cases_sold) as total_real_cases
	                    from ssds_sales s
	                    where s.customer_id <>0 and s.wine_id<>0 and MONTH(s.sale_date)=$sale_month and YEAR(s.sale_date)=$sale_year
	                    and s.user_id = $user_id_filter
	            -- and lkup_store_type_id = 6
	  					$province_filter          
	                    group by user_id
	                    order by user_id";
                        */
                        
                        // we called it total_Real_cases just match the Excel report code part so we don't need to chante the code there, it is the case value not real cases
                $SQL ="select   total_international_cases as total_international_real_cases
                from user_sales_summary s
                where  s.sale_month=$sale_month and s.sale_year=$sale_year
                and s.user_id = $user_id_filter
              
                and lkup_store_type_id <>6 
				$province_filter          
                group by user_id
                order by user_id";
        }      
        else
        {
  		    $SQL ="select   sum(if(is_international=1, cases_sold, 0)) as total_international_real_cases,
	                    sum(cases_sold) as total_real_cases
	                    from ssds_sales s
	                    where s.customer_id <>0 and s.wine_id<>0 and MONTH(s.sale_date)=$sale_month and YEAR(s.sale_date)=$sale_year
	                    and s.user_id = $user_id_filter
	            and lkup_store_type_id <> 6
	  					$province_filter          
	                    group by user_id
	                    order by user_id";
        }
        $rs["summary_real_cases"] = $this->db->getAll($SQL);
       // if (($store_type_id == -1) || ($store_type_id == 6))//all stores add commission details
      //  {
        if($this->isNewRule($sale_year,$sale_month))
        {
	        $rs["commission_details"] = $this->GetSalesCommissionsReport($sale_month,$sale_year, $user_id);	
		}
		else
		{
	        $rs["commission_details"] = $this->GetCommissionsReport($sale_month,$sale_year, $user_id, $store_type_id);
        }
        
        return $rs;
        
    }
   
   
   	function getMonthlySalesCommissionTypeDetails($user_id, $sale_year, $sale_month)
   	{
		$sql="select * from monthly_sale_commission_levels where user_id =$user_id and level_number=0 and Year(sale_date)=$sale_year and Month(sale_date)=$sale_month";
		$rows = $this->db->getAll($SQL);
        if (count($rows) <= 0)
        {
            $this->file_format_error .= "Error: Can't find commission type by this user.";
            return 0;
        }
        else
        {
             return $rows;
        }
		
	}
   
    function getUserBonusType($user_id,$province_id=1,$sale_year="",$sale_month="")
    {
     	if($sale_year=="" || !$this->isNewRule($sale_year,$sale_month))
     	{
     	 
	        $SQL = "select lkup_store_type_id lkup_store_type_id from user_sales_summary where user_id=$user_id";
	        $rows = $this->db->getAll($SQL);
	        if (count($rows) <= 0)
	        {
	            $this->file_format_error .= "Error: Can't find store_type by this user.";
	            return 0;
	        }
	        else
	        {
	             return $rows;
	        }
	    }
	    else
	    {
	     	
			$SQL = "Select lkup_sales_commission_type_id type_id FROM `monthly_sales_commission_levels` where user_id = $user_id and level_number=0 and year(sale_date)=$sale_year 
					and month(sale_date)=$sale_month";

	        $rows = $this->db->getAll($SQL);
	        if (count($rows) <= 0)
	        {
	            $this->file_format_error .= "Error: Can't find store_type by this user.";
	            return 0;
	        }
	        else
	        {
	             return $rows[0]["type_id"];;
	        }
		}
    }
   
	function GetSalesCommissionsReport($sale_month,$sale_year, $user_id, $commission_type_id=1, $lkup_commission_sales_sum_type_id=1)
	{
	
	 	
			$commissionType = $this->getUserBonusType($user_id,-1,$sale_year,$sale_month); // use a dummy province id here, need to get ride of the province_id
			
		//	
			
			if($commissionType==1)//regular store in BC
			{
			 	
				$sql= "SELECT * FROM monthly_sales_commission_levels where month(sale_date) = $sale_month 
                and year(sale_date) = $sale_year
                and user_id = $user_id
                and level_number=1";
	            $min_sale = $this->db->getAll($sql);
	           
	            $min_cases = 0.0;
	            if (count($min_sale) > 0)
                	$min_cases = floatval($min_sale[0]["level_start_cases"])-1;
                	
               
                	
                /*
				 "select us.user_id, cm.level_id, cm.caption,
                   cm.min_cases, cm.max_cases, cm.commission_rate,
                   case
                     when Truncate(us.total_cases, 0) < cm.min_cases then 0.0
                     when Truncate(us.total_cases, 0) > cm.max_cases and IFNULL(cm.min_canadian_cases, 0)<=us.total_canadian_cases
                          and IFNULL(cm.min_intl_cases, 0)<= us.total_international_cases then 0.0
                     when Truncate(us.total_cases, 0)<=cm.max_cases and IFNULL(cm.min_canadian_cases, 0)<= us.total_canadian_cases
                          and IFNULL(cm.min_intl_cases, 0)<= us.total_international_cases then (us.total_cases - $min_cases)
                     else 0.0
                   End as total_cases,
                   case
                     when Truncate(us.total_cases, 0) < cm.min_cases then 0.0
                     when Truncate(us.total_cases, 0) > cm.max_cases and IFNULL(cm.min_canadian_cases, 0)<=us.total_canadian_cases
                          and IFNULL(cm.min_intl_cases, 0)<=us.total_international_cases then 0.0
                     when Truncate(us.total_cases, 0) <= cm.max_cases and IFNULL(cm.min_canadian_cases, 0)<=us.total_canadian_cases
                          and IFNULL(cm.min_intl_cases, 0)<=us.total_international_cases then (us.total_cases - $min_cases)
                     else 0.0
                   End * us.avg_profit_per_case * (cm.commission_rate /100.00) as commission_amount,
                   if(us.total_cases >= cm.max_cases and IFNULL(cm.min_canadian_cases, 0) <=us.total_canadian_cases
                          and IFNULL(cm.min_intl_cases, 0) <= us.total_international_cases,cm.bonus, 0.0) as bonus
                   from user_sales_summary us
                   inner join month_commission_levels cm on us.sale_month = cm.sale_month and us.sale_year=cm.sale_year
                   where us.sale_month = $sale_month and us.sale_year=$sale_year
                   and us.lkup_store_type_id = -1
                   and cm.lkup_store_type_id = -1
                   and us.user_id = $user_id_filter
                   order by user_id, level_id, cm.min_cases
                   ";
                   
				*/
                /*
				
				Array ( [0] => Array ( [user_id] => 6 [level_id] => 0 [caption] => Level 0 [min_cases] => 

0.00 [max_cases] => 0.00 [commission_rate] => 0.00 [total_cases] => 0.00 [commission_amount] 

=> 0.0000 [bonus] => 0.0 ) 
[1] => Array ( [user_id] => 6 [level_id] => 1 [caption] => Level 

1 [min_cases] => 181.00 [max_cases] => 205.00 [commission_rate] => 15.00 [total_cases] => 

0.00 [commission_amount] => 0.0000 [bonus] => 0.0 ) 
[2] => Array ( [user_id] => 6 [level_id] 

=> 2 [caption] => Level 2 [min_cases] => 206.00 [max_cases] => 225.00 [commission_rate] => 

20.00 [total_cases] => 0.00 [commission_amount] => 0.0000 [bonus] => 0.0 ) 

[3] => Array ( [user_id] => 6 [level_id] => 3 [caption] => Level 3 [min_cases] => 226.00 [max_cases] => 1000.00
	 [commission_rate] => 25.00 [total_cases] => 353.11 [commission_amount] => 1147.6075 

[bonus] => 0.0 ) ) 
				*/
	            $SQL = "SELECT us.user_id, mc.level_number level_id, mc.level_caption caption,
	                   mc.level_start_cases min_cases, mc.level_end_cases max_cases, mc.level_commission_rate commission_rate,
	                   case
	                     when Truncate(us.total_cases, 0) < mc.level_start_cases then 0.0
	                     when Truncate(us.total_cases, 0) > mc.level_end_cases and IFNULL(mc.target_cases_ca, 0)<=us.total_canadian_cases
	                          and IFNULL(mc.target_cases_intl, 0)<= us.total_international_cases then 0.0
	                     when Truncate(us.total_cases, 0)<=mc.level_end_cases and IFNULL(mc.target_cases_ca, 0)<= us.total_canadian_cases
	                          and IFNULL(mc.target_cases_intl, 0)<= us.total_international_cases then (us.total_cases - $min_cases)
	                     else 0.0
	                   End as total_cases,
	                   case
	                     when Truncate(us.total_cases, 0) < mc.level_start_cases then 0.0
	                     when Truncate(us.total_cases, 0) > mc.level_end_cases and IFNULL(mc.target_cases_ca, 0)<=us.total_canadian_cases
	                          and IFNULL(mc.target_cases_intl, 0)<=us.total_international_cases then 0.0
	                     when Truncate(us.total_cases, 0) <= mc.level_end_cases and IFNULL(mc.target_cases_ca, 0)<=us.total_canadian_cases
	                          and IFNULL(mc.target_cases_intl, 0)<=us.total_international_cases then (us.total_cases - $min_cases)
	                     else 0.0
	                   End * us.avg_profit_per_case * (mc.level_commission_rate /100.00) as commission_amount,
	                   if(us.total_cases >= mc.level_end_cases and IFNULL(mc.target_cases_ca, 0) <=us.total_canadian_cases
	                          and IFNULL(mc.target_cases_intl, 0) <= us.total_international_cases,0, 0.0) as bonus
	                   from user_sales_summary us
	                   inner join monthly_sales_commission_levels mc on us.sale_month = month(mc.sale_date) and us.sale_year=year(mc.sale_date)
	                   where us.sale_month = $sale_month and us.sale_year=$sale_year
	                   and us.lkup_store_type_id = -1
	                   and mc.user_id=$user_id

	                   and us.user_id = $user_id
	                   order by user_id, level_number, mc.level_start_cases
	                   ";
	                   
	            $rows = $this->db->getAll($SQL);
	            
	           
	            
	        
			}
			else if ($commissionType>1 && $commissionType<4)//type2: BCLDB and Type3: BCLDB and regular
	        {	         
	           $SQL = "select us.user_id, cm.level_number, cm.level_caption caption,
	                   cm.level_target_sales target_price, us.total_sales,   null as commission_amount,
	                   cm.level_commission_bonus bonus
	                   from user_sales_summary us
	                   inner join monthly_sales_commission_levels cm on us.sale_month = month(cm.sale_date) and us.sale_year=year(cm.sale_date)
	                   where us.sale_month = $sale_month and us.sale_year=$sale_year
	                   and us.user_id = $user_id
	                   and us.lkup_store_type_id = 6
	                   and cm.user_id = us.user_id
	                   and cm.level_number<>0
	                   order by user_id, cm.level_target_sales
	                   ";
	         
	            $rows = $this->db->getAll($SQL);
	            
	            $target_cases =0;
	            if($commissionType==3)
	            {
	             
	             	$SQL="select 
						if(us.total_canadian_cases>mc.target_cases_ca, us.total_canadian_cases,0) cases
						 from user_sales_summary us, monthly_sales_commission_levels mc where us.user_id =$user_id and us.lkup_store_type_id!=6
						and us.sale_month = $sale_month and us.sale_year=$sale_year
						and mc.level_number=0
						and mc.user_id =us.user_id
						and month(mc.sale_date)=us.sale_month
						and year(mc.sale_date)=us.sale_year";
						
					$records = $this->db->getAll($SQL);
					$target_cases=$records[0]["cases"];
					
					
					
	     		}
	     		
	     
	            //every row has bonus and total sales filled in
	
	            for ($i=1; $i<count($rows); $i++)
	            {            
	                if (floatval($rows[$i-1]["total_sales"])< floatval($rows[$i-1]["target_price"]))
	                {
	                 	  if($i!=1&&$i!=11&&$i!=21) // add by Helen, display the sales if not catch the first level $i!=10 : next user
		                    $rows[$i-1]["total_sales"] = 0.0;
		                    
	                    $rows[$i-1]["bonus"] = 0.0;
	               
	                    
	                }
	                  	
	                if ($rows[$i]["user_id"] <> $rows[$i-1]["user_id"])
	                {
	                    continue;
	                }
	                    
	                //the actual bonus is at a higher commission level
	                if ((floatval($rows[$i-1]["total_sales"])> floatval($rows[$i-1]["target_price"])) and (floatval($rows[$i-1]["total_sales"]) >= floatval($rows[$i]["target_price"])))
	                {
	                    $rows[$i-1]["total_sales"] = floatval($rows[$i-1]["target_price"]);
	                    //$rows[$i-1]["bonus"] = 0.0; //change by HL, if bonus is for second level, add first level too
	                }
	                
	                if($commissionType==3)
	                {
	                 	if($target_cases==0)
	                 	{
							$rows[$i-1]["bonus"]=0.0;
						}
					}
	                
	            }            
	            //sanity check the last row 

		//	echo $i;

	            if (floatval($rows[$i-1]["total_sales"])<floatval($rows[$i-1]["target_price"]))
	            {
	                $rows[$i-1]["total_sales"] = 0.0;
	                $rows[$i-1]["bonus"] = 0.0;
	            }
	            
	             
	        } //BCLDB	        
			else if ($commissionType == 4)//alberta
	        {
	        
	        //update by Helen, mysql4 to mysql5
	        
	           $SQL = "select us.user_id, cm.level_number, cm.level_caption caption,
	                   cm.level_end_cases target_price,   us.total_cases,   cm.level_end_cases,  null as commission_amount,
	                   cm.level_commission_bonus bonus
	                   from user_sales_summary us
	                   inner join monthly_sales_commission_levels cm on us.sale_month = month(cm.sale_date) and us.sale_year=year(cm.sale_date)
	                   where us.sale_month = $sale_month and us.sale_year=$sale_year
	                   and us.lkup_store_type_id = 8
	                   and cm.user_id = us.user_id
	                   and cm.level_number!=0
	                   and us.user_id = $user_id
	                   order by user_id, cm.level_end_cases
	                   ";
	                   
	        /* "select us.user_id, cm.level_number, cm.level_caption,
	                   cm.level_target_sales target_price, cm.max_cases, cm.commission_rate, cm.target_price,
	                   us.total_cases,
	                   null as commission_amount,
	                   cm.bonus
	                   from user_sales_summary us
	                   inner join month_commission_levels cm on us.sale_month = cm.sale_month and us.sale_year=cm.sale_year
	                   where us.sale_month = $sale_month and us.sale_year=$sale_year
	                   and us.lkup_store_type_id = 8
	                   and cm.lkup_store_type_id = 8
	                   and us.user_id = $user_id_filter
	                   order by user_id, cm.target_price
	                   ";*/
	            $rows = $this->db->getAll($SQL);
	            
	            //every row has bonus and total sales filled in
	            for ($i=1; $i<count($rows); $i++)
	            {
	                if (floatval($rows[$i-1]["total_cases"])< floatval($rows[$i-1]["target_price"]))
	                {
	                 	  if($i!=1&&$i!=6) // add by HL, still display the sales if not catch the first level $i!=4 : next user
		                    $rows[$i-1]["total_cases"] = 0.0;
	                    $rows[$i-1]["bonus"] = 0.0;                  
	                    
	                }
	                
	                if ($rows[$i]["user_id"] <> $rows[$i-1]["user_id"])
	                {
	                    continue;
	                }                    
	                
	                //the actual bonus is at a higher commission level
	                if ((floatval($rows[$i-1]["total_cases"])> floatval($rows[$i-1]["target_price"])) and (floatval($rows[$i-1]["total_cases"]) >= floatval($rows[$i]["target_price"])))
	                {
	                    $rows[$i-1]["total_cases"] = floatval($rows[$i-1]["target_price"]);
	                    //$rows[$i-1]["bonus"] = 0.0; //change by HL, if bonus is for second level, add first level too
	                }
	            }
	            
	            //sanity check the last row 
	            if (floatval($rows[$i-1]["total_cases"])<floatval($rows[$i-1]["target_price"]))
	            {
	                $rows[$i-1]["total_cases"] = 0.0;
	                $rows[$i-1]["bonus"] = 0.0;
	            }		
	        } //Alberta
				//print_r($rows);
				
				//
				
		return $rows;
		
	}
	
    function GetCommissionsReport($sale_month,$sale_year, $user_id=-1, $store_type_id = -1,$province_id=1)
    {
        $user_id_filter = ($user_id==-1?"us.user_id":$user_id); //-1 means select all


        if ($store_type_id == -1 && $province_id == 1) //all other stores
        {
            //get minimum case sales needed to qaulify for commission
            $sql= "SELECT max_cases FROM month_commission_levels where commission_rate=0 and sale_month = $sale_month 
                and sale_year = $sale_year
                and lkup_store_type_id = -1
                order by max_cases desc Limit 1";
            $min_sale = $this->db->getAll($sql);
           
            $min_cases = 0.0;
                     
            if (count($min_sale) > 0)
                $min_cases = floatval($min_sale[0]["max_cases"]);
                
            $SQL = "select us.user_id, cm.level_id, cm.caption,
                   cm.min_cases, cm.max_cases, cm.commission_rate,
                   case
                     when Truncate(us.total_cases, 0) < cm.min_cases then 0.0
                     when Truncate(us.total_cases, 0) > cm.max_cases and IFNULL(cm.min_canadian_cases, 0)<=us.total_canadian_cases
                          and IFNULL(cm.min_intl_cases, 0)<= us.total_international_cases then 0.0
                     when Truncate(us.total_cases, 0)<=cm.max_cases and IFNULL(cm.min_canadian_cases, 0)<= us.total_canadian_cases
                          and IFNULL(cm.min_intl_cases, 0)<= us.total_international_cases then (us.total_cases - $min_cases)
                     else 0.0
                   End as total_cases,
                   case
                     when Truncate(us.total_cases, 0) < cm.min_cases then 0.0
                     when Truncate(us.total_cases, 0) > cm.max_cases and IFNULL(cm.min_canadian_cases, 0)<=us.total_canadian_cases
                          and IFNULL(cm.min_intl_cases, 0)<=us.total_international_cases then 0.0
                     when Truncate(us.total_cases, 0) <= cm.max_cases and IFNULL(cm.min_canadian_cases, 0)<=us.total_canadian_cases
                          and IFNULL(cm.min_intl_cases, 0)<=us.total_international_cases then (us.total_cases - $min_cases)
                     else 0.0
                   End * us.avg_profit_per_case * (cm.commission_rate /100.00) as commission_amount,
                   if(us.total_cases >= cm.max_cases and IFNULL(cm.min_canadian_cases, 0) <=us.total_canadian_cases
                          and IFNULL(cm.min_intl_cases, 0) <= us.total_international_cases,cm.bonus, 0.0) as bonus
                   from user_sales_summary us
                   inner join month_commission_levels cm on us.sale_month = cm.sale_month and us.sale_year=cm.sale_year
                   where us.sale_month = $sale_month and us.sale_year=$sale_year
                   and us.lkup_store_type_id = -1
                   and cm.lkup_store_type_id = -1
                   and us.user_id = $user_id_filter
                   order by user_id, level_id, cm.min_cases
                   ";
                   
            $rows = $this->db->getAll($SQL);
        }
        else if ($store_type_id == 6)//BCLDB
        {
           $SQL = "select us.user_id, cm.level_id, cm.caption,
                   cm.min_cases, cm.max_cases, cm.commission_rate, cm.target_price,
                   us.total_sales,
                   null as commission_amount,
                   cm.bonus
                   from user_sales_summary us
                   inner join month_commission_levels cm on us.sale_month = cm.sale_month and us.sale_year=cm.sale_year
                   where us.sale_month = $sale_month and us.sale_year=$sale_year
                   and us.lkup_store_type_id = 6
                   and cm.lkup_store_type_id = 6
                   and us.user_id = $user_id_filter
                   order by user_id, cm.target_price
                   ";
         
            $rows = $this->db->getAll($SQL);
     
            //every row has bonus and total sales filled in

            for ($i=1; $i<count($rows); $i++)
            {            
                if (floatval($rows[$i-1]["total_sales"])< floatval($rows[$i-1]["target_price"]))
                {
                 	  if($i!=1&&$i!=11&&$i!=21) // add by HL, display the sales if not catch the first level $i!=10 : next user
	                    $rows[$i-1]["total_sales"] = 0.0;
	                    
                    $rows[$i-1]["bonus"] = 0.0;                 
                }
                  	
                if ($rows[$i]["user_id"] <> $rows[$i-1]["user_id"])
                {
                    continue;
                }
                    
                //the actual bonus is at a higher commission level
                if ((floatval($rows[$i-1]["total_sales"])> floatval($rows[$i-1]["target_price"])) and (floatval($rows[$i-1]["total_sales"]) >= floatval($rows[$i]["target_price"])))
                {
                    $rows[$i-1]["total_sales"] = floatval($rows[$i-1]["target_price"]);
                    //$rows[$i-1]["bonus"] = 0.0; //change by HL, if bonus is for second level, add first level too
                }
            }            
            //sanity check the last row 
            if (floatval($rows[$i-1]["total_sales"])<floatval($rows[$i-1]["target_price"]))
            {
                $rows[$i-1]["total_sales"] = 0.0;
                $rows[$i-1]["bonus"] = 0.0;
            }
        		
        } //BCLDB
        else if ($store_type_id == 8)//alberta
        {
           $SQL = "select us.user_id, cm.level_id, cm.caption,
                   cm.min_cases, cm.max_cases, cm.commission_rate, cm.target_price,
                   us.total_cases,
                   null as commission_amount,
                   cm.bonus
                   from user_sales_summary us
                   inner join month_commission_levels cm on us.sale_month = cm.sale_month and us.sale_year=cm.sale_year
                   where us.sale_month = $sale_month and us.sale_year=$sale_year
                   and us.lkup_store_type_id = 8
                   and cm.lkup_store_type_id = 8
                   and us.user_id = $user_id_filter
                   order by user_id, cm.target_price
                   ";
         
            $rows = $this->db->getAll($SQL);
            
            //every row has bonus and total sales filled in
            for ($i=1; $i<count($rows); $i++)
            {
                if (floatval($rows[$i-1]["total_cases"])< floatval($rows[$i-1]["target_price"]))
                {
                 	  if($i!=1&&$i!=6) // add by HL, still display the sales if not catch the first level $i!=4 : next user
	                    $rows[$i-1]["total_cases"] = 0.0;
                    $rows[$i-1]["bonus"] = 0.0;                                      
                }
                
                if ($rows[$i]["user_id"] <> $rows[$i-1]["user_id"])
                {
                    continue;
                }                    
                
                //the actual bonus is at a higher commission level
                if ((floatval($rows[$i-1]["total_cases"])> floatval($rows[$i-1]["target_price"])) and (floatval($rows[$i-1]["total_cases"]) >= floatval($rows[$i]["target_price"])))
                {
                    $rows[$i-1]["total_cases"] = floatval($rows[$i-1]["target_price"]);
                    //$rows[$i-1]["bonus"] = 0.0; //change by HL, if bonus is for second level, add first level too
                }
            }
            
            //sanity check the last row 
            if (floatval($rows[$i-1]["total_cases"])<floatval($rows[$i-1]["target_price"]))
            {
                $rows[$i-1]["total_cases"] = 0.0;
                $rows[$i-1]["bonus"] = 0.0;
            }		
        } //BCLDB
        return $rows;
    }
    
    function getMaxSaleMonth($province_id =1)
    {
		$SQL = "select max(sale_year) sale_year from user_sales_summary where province_id= $province_id";
        $rows = $this->db->getAll($SQL);
        if (count($rows) <= 0)
        {
            $this->file_format_error .= "Error: Database error.";
            return 0;
        }
        else
        {        
             return $rows[0]["sale_year"];
        }
	}
    
    function updateCanadianTotalCases($sale_year,$sale_month, $user_id, $new_total)
    {
        $SQL = "Update user_sales_summary set total_canadian_cases = $new_total
                where user_id = $user_id and sale_month = $sale_month and sale_year=$sale_year
                ";
        return $this->db->execute($SQL);
    }
}
?>