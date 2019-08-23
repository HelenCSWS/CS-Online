<?php
import('php2go.base.Php2Go');
import('php2go.data.PagedDataSet');
import('Form60.base.F60DbUtil');
require_once('Excel/reader.php');


import('php2go.base.Php2Go');
import('php2go.util.TypeUtils');
import('Form60.util.F60Date');

import('php2go.data.PagedDataSet');
import('Form60.base.F60DALBase');
import('Form60.util.F60Common');


class ABReportData extends Php2Go
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
    
    var $cfg;
 
    
    function ABReportData()
    {
     	include('config/emailoutconfig.php');
	 	
     	include('config/dataconfig.php');
     	
     	$this->cfg = $EMAIL_CFG;
        
		$this->data_cfg = $DATA_CFG;
     	
        $this->uploadfile = "";
        $this->bcldb_uploadfile = "";
        $this->uploaddir = "";
        $this->db = & Db::getInstance();
        $this->db->setFetchMode(ADODB_FETCH_ASSOC);
    }
    
    function setDaily_UploadedFile($file_name)
    {
     	 
        $this->uploadfile = $file_name;
        $this->dataFile = $file_name;
		 
        $this->uploaddir = dirname($this->uploadfile);
    }
        
    
	function isValidFile()
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
                $bRet = $this->_getDateInfo();
        }
    
        return $bRet;
    }
    
    function _getDateInfo()
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
            {               //SKU	SKUDescription	Size	UnitsxCase	QtyUnable	UnableDate1	LicNumber	LicName	SKUnitPrice

               	  $title = $data[0];
               	  
               
               
                    if($title=="")
                    {
                  
                        $this->file_format_error .= "Error: Unable to read the data from the upload file.";
                        $bRet=false;
                    }
	                
            }                             
        }
        
        return $bRet;
    }
    
    
	function import_Data($file_name)
    {
     
       $this->dataFile = $file_name;      
       $this->uploaddir = dirname($this->dataFile);
        
       $bRet = true;
        
        //clear temp table at first time when run bcldb
    //    if($isBCLDB)
    //    	$this->db->execute("DELETE FROM ssds_sales_temp");
        
	//	if($isBCLDB)
	//		$handle = fopen($this->bcldb_dataFile, "r");
	//	else
	    $handle = fopen($this->dataFile, "r");
    
        $SQL = "INSERT INTO temp_ab_daily_unabled (sku, product, size, units, licensee_no,store,city,qty,unit_price,unable_date,user_name) VALUES ";
       
        if(!$handle)
        {
            $this->file_format_error .= "Error: unable to open the data file.";
            
            $bRet = false;
        }
        else
        {
            $data = fgetcsv($handle, 1000, ",");
            
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
                {//SKU	SKUDescription	Size	UnitsxCase	QtyUnable	UnableDate1	LicNumber	LicName	SKUnitPrice
                   	//	$index=7;
                   
				 	
				 		$sku =intval($data[0]);
				 		$desc =$data[1];
				 		
				 		$desc=str_replace("'","\'",$desc)  ;
				 		$size =$data[2];
				 		$units =$data[3];
				 		
				 		$qty =intval($data[4]);
				 		$date =$data[5];
				 		$licno =$data[6];
				 		$lic_name =$data[7];
				 		$lic_name=str_replace("'","\'",$lic_name)  ;
				 		
				 	
				 		
				 		$price = floatval($data[8]);
				 	
				 		$rec = $this->getCustomerInfo($licno);
				 		$city ="";
						$user_name = "New Customer";
						
				 		//sku, product, size, unit, licensee_no,store,city,qty,unit_price,unable_date,user_name
				 		
				 		if($rec!=0)
				 		{
							$city =$rec[0]["city"];
							$user_name = $rec[0]["user_name"];
							
						
							$lic_name=$rec[0]["customer_name"];
							$lic_name=str_replace("'","\'",$lic_name);
						}
					
				 	
				 	$row++;
				 	if ($row == 1)
                    {	
                  		$SQL .= "('$sku', '$desc',$size, $units,'$licno','$lic_name','$city',$qty,$price,'$date','$user_name')";
                  	}
                  	else
                  	{
                  		$SQL .= ",('$sku', '$desc', $size,$units,'$licno','$lic_name','$city',$qty,$price,'$date','$user_name')";
					}
                }
            }
           
            $bRet = true;
            
            $delSql = "delete from temp_ab_daily_unabled";
            $bRet = $this->db->execute($delSql);
            
            
           
            $bRet = $this->db->execute($SQL);
           fclose ($handle);    
        
          
        }       
        
        $this->emailDailyUnableReport();
        	
        return $bRet ;
    }
    

    function getCustomerInfo($licno)
    {
     	$SQL = "select count(*) cnt from customers where licensee_number =$licno
				
				and lkup_store_type_id =8 and deleted=0";
				
		 $bRet=$this->db->getAll($SQL);
		 	
		 	
		 if($bRet[0]['cnt']==0)
		 {
			return 0;
		}
		$SQL = "select c.customer_name,c.billing_address_city city, ifnull(concat(u.first_name, ' ',u.last_name), 'Not Assigned') user_name

				from  customers c left outer join users_customers uc on uc.customer_id = c.customer_id 
				
				left outer join users u on uc.user_id =u.user_id
				
				where c.licensee_number =$licno
				
				and c.lkup_store_type_id =8 
				and c.deleted =0";
				
		$bRet=$this->db->getAll($SQL);
		return $bRet;
	}
	
	function getDailyUnabledInfo()
	{
		$SQL = "select * from temp_ab_daily_unabled order by user_name ";
				
		 $bRet=$this->db->getAll($SQL);
		 
		 return $bRet;
		
	
	}
   	function getConfigVal($config)
    {     	
        return $this->cfg[$config];
    }

	function test_emailDailyUnableReport()
	{
		return true;	
	}
	function emailDailyUnableReport()
    {

     		// Email from address
		$fromAddress=$this->getConfigVal("EMAIL_FROM_ADDRESS");
			  
      	
        $subject = "Daily unable to ship report";
        $emailContent = "<div style='font-family :verdana; font-size:9pt'>
		<P>Please find attached your Daily unable to ship report for today.</P>
	
		<P>Thanks,</P>

		<P>Christopher Stewart Online
		</p></div>";
	
	
	   import('Form60.exportreports.excelABDailyUnabledReport');
       $excelReport = new excelABDailyUnabledRepor(true);
       
     	$fileName = $excelReport->generateReportSheet();
       	
     
       	
       $to="";
       $user_name="";
      
		$bcc ="";
		
     	$bcc="garry@christopherstewart.com;helen@christopherstewart.com;gemma@christopherstewart.com";
//		$bcc="helen@christopherstewart.com;";
		  
		$i=0;
		
	//	$to="alex@christopherstewart.com;luc@christopherstewart.com;lindsay@christopherstewart.com;staci@christopherstewart.com;chris@christopherstewart.com;tyler@christopherstewart.com;angie@christopherstewart.com;";
//    	$to="jamie@christopherstewart.com;luc@christopherstewart.com;tyler@christopherstewart.com;angie@christopherstewart.com;";
		
        //automaticlly get Alberta sales email address and Sales manager (reports_to_id =1);
        $userEmailAddress =$this->getSCEmailAddresses();
	
        $to = $userEmailAddress;
	
	//	$to="helen@christopherstewart.com;";
				
    	F60Common::_sendEmail($to , $bcc, $fromAddress, $subject, $emailContent, $fromAddress,$fileName);
    }

	function getSCEmailAddresses()
    {
        $sql="select email1 from users where province_id =2 and deleted=0 and lkup_user_type_id =1  
              or reports_to_id =1 order by email1"; // reports_to_id =1 select the sales manager  
        $emails = $this->db->getAll($sql);
        
        $allEmaiAddress="";
 
        foreach($emails as $email)
        {        
            if($email["email1"]!="helen@christopherstewart.com")// ignore other setting
                $allEmaiAddress =$allEmaiAddress.$email["email1"].";";
        }        
        
		$allEmaiAddress = $allEmaiAddress.";anne@christopherstewart.com;chris@christopherstewart.com;tyler@christopherstewart.com;"; // Add Alberta inventory person to email lists
        
        return $allEmaiAddress;            			      	
	}
        

   
    
  
    
  
    
   
	

   
}
?>