<?php

import('php2go.base.Php2Go');
import('php2go.util.TypeUtils');
import('Form60.util.F60Date');
import('Form60.util.tableExtractor');
import('Form60.util.Services_JSON');
import('Form60.base.F60DALBase');
import('php2go.net.MailMessage'); 
import('php2go.data.PagedDataSet');


function toArray($data) 
{
	if (is_object($data)) 
		$data = get_object_vars($data);
	return is_array($data) ? array_map(__FUNCTION__, $data) : $data;
}	
class bllStorePenetrationData extends Php2Go 
{
    var $db;
    var $cfg;
    var $logFile;
    var $errorMessage;
    var $isPrint=false;
   
    function bllStorePenetrationData()
    {
        $this->_init();;
        
        $this->db = & Db::getInstance();
        $this->db->setFetchMode(ADODB_FETCH_ASSOC);
        

    }
    
    
    function collectData($isCurrentDate = false)
    {		$cYear = Date(Y);
			$cMonth=Date(m);
			$cDay=Date(d);
   
     	if(!$isCurrentDate)
        {
			$sql ="delete from store_penetration_data where when_entered >='$cYear-$cMonth-$cDay'";
			$this->db->execute($sql);
			$this->errorMessage = "";
			$this->logMessage("---- Starting data collection ---- ", false);
        }
        else
        {
			//delete today's data if exist		
	        $sql ="delete from store_penetration_data_current where when_entered >='$cYear-$cMonth-$cDay'";
	        $this->db->execute($sql);
			
		}
		
        $sql = "SELECT cspc_code from include_in_store_penetration_report;";
        $rows = $this->db->getAll($sql);
        

      
      foreach ($rows as $row)
        {
            if ($row["cspc_code"] <> '')
			{
				$cspc_code = $row["cspc_code"];
				$datas = $this->getData($cspc_code);
				
				if($datas!=0) // get wine name;
					{
						foreach ($datas as $data)
						{
	
							if ($data["error_code"] == 0)
							{
								//print $cspc_code;
								 if (!$this->writeDatatoDatabase($cspc_code, $data["wine_name"], $data["no_of_stores"], $data["available_quantity"],$data["location_name"],$data["user_id"], $isCurrentDate))
									$this->setError(9, "Error in saving data for CSPC code: $cspc_code", null);
							}
							
						}
				}
            }
        }
        
        //test comment, should be reversed
        
      	if(!$isCurrentDate)
        {
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
	    
	    
    }
    function getLastStorePenDate()
    {
			$sql="select max(when_entered) current_spdate from store_penetration_data_current";
			$rows = $this->db->getAll($sql);
			return $rows;
	 }
	 
	 function getStoreInfo($cspc_code,$wine_name)
	 {
  
		$url = str_replace("[cspc_code]", $cspc_code, $this->getConfigVal("INFO_URL"));
		
		$this->logMessage("Getting data for CSPC code: $cspc_code from $url", true);
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_USERAGENT, $this->getConfigVal("USER_AGENT"));
		curl_setopt($ch, CURLOPT_FAILONERROR, TRUE);
		
		$htmltext = curl_exec($ch);
		$cerr = curl_errno($ch);
		
		if ($cerr<>0)
		{
		   if ($cerr == 22)
		       $this->setError($cerr, curl_error($ch) . " WINE_NAME_URL: $url", $data, false); //got error 500 means the wine is not in the database, don't log this as error
		   else
		       $this->setError($cerr, curl_error($ch) . " WINE_NAME_URL: $url", $data, true);
		   curl_close($ch);
		   return $data;
		}
		curl_close($ch);
		
		
		if ($htmltext == "")
		{
		   $this->setError(100, "No data returned from $url", $data);
		   return $data;
		}
		
		$objJson = new Services_JSON;
		
		
		$objArrayText= $objJson->decode( $htmltext );

		$arrayInfoTexts = toArray($objArrayText);
		
	
		
		$total_stores =0;
		$total_quantities =0;	
		$location_name ="";
		$total_store_tag =$this->getConfigVal("TOTAL_STORE_TAG_JSON");
		$quantity_tag =$this->getConfigVal("QUANTITY_TAG_JSON");
		$lacation_name_tag =$this->getConfigVal("LOCATION_NAME_TAG_JSON");
		
		/*$STORE_PEN_DATA_CFG['TOTAL_STORE_TAG_JSON'] = "total";
    $STORE_PEN_DATA_CFG['QUANTITY_TAG_JSON'] = "quantity";
    $STORE_PEN_DATA_CFG['LOCATION_NAME_TAG_JSON'] = "name";
	*/
		
		$wine_datas = array();
		
		$i=0;
		$j=0;
		
		$user_id =0;
		
		$users =$this->getBreakDownUsers();
		
		$user_store_numbers=array(); // array to save how many store for per user, index key is user_id
		$user_quantities=array();
		
		$user_licensee_nos=array(); // array to save the user_id or store, index key is store licensee number
		
		foreach($users as $data=>$user)
		{
		 	//create the user store numbers array
		 	$current_user_id =$user['user_id'];
			$user_id_store=array("$current_user_id"=>0);
		//	array_push($user_store_numbers,$user_id_store);
		
			$user_store_numbers=$user_store_numbers + $user_id_store;
			$user_quantities=$user_quantities + $user_id_store;
		}
		
		$store_lic_nos =$this->getBCLDBLicenseeNosByUser($users);
		
		foreach($store_lic_nos as $data=>$array_store_lic_no)
		{
		 	//create the user store numbers array
		 	$current_user_id =$array_store_lic_no['user_id'];
		 	$store_lic_no =$array_store_lic_no['licensee_number'];
			$array_user_licensee_no=array("$store_lic_no"=>$current_user_id);
			
			$user_licensee_nos=$user_licensee_nos+$array_user_licensee_no;
			
		}
	//	print_r($user_licensee_nos);
	

	
		/*foreach($users as $data=>$user)
		{
		
		 	$current_user_id =$user['user_id'];
		 	
		 //	echo $current_user_id;
			$user_date=array("user_id"=>$current_user_id,"store_no"=>0);
			
			array_push($users_data, $user_date);
		}*/

		
		foreach( $arrayInfoTexts as $group=>$arrayStoreInfo) 
		{
	
			if (array_key_exists($quantity_tag, $arrayStoreInfo)) 
			{
				$total_quantities=$arrayStoreInfo[$quantity_tag];
			}
			else
			{
				$this->setError(101, "No quantity flag in Json", $data);
				exit;
			}
			
			if (array_key_exists($total_store_tag, $arrayStoreInfo)) 
				$total_stores =$arrayStoreInfo[$total_store_tag];
			else
			{
				$this->setError(101, "No total flag in Json", $data);
				exit;
			}
			
			$ava_stores=0;
			if (array_key_exists($lacation_name_tag, $arrayStoreInfo)) 
			{
				$location_name =$arrayStoreInfo[$lacation_name_tag];
				
				
			
				$groupId = $this->getGroupTypeID($location_name);
				
				if($groupId ==2)//lower mainland area need breakdown by wine consultants
				{
				 	//init arrays;
				 	foreach($users as $data=>$user)
					{
					 	$current_user_id =$user['user_id'];
						$user_store_numbers["$current_user_id"]=0;
						$user_quantities["$current_user_id"]=0;
					}
		
					
					//print_r(array_keys($arrayStoreInfo["cities"]));
					
					$arrayCites = $arrayStoreInfo["cities"];
					
					$a=0;
					$store_number =0;
					foreach( $arrayCites as $group1=>$city) 
					{
					 	$array_licensee_numbers =array_keys($city["stores"]);
						
				//		if($i==1)
				//	print_r($user_store_numbers);
						
							for($j=0;$j<sizeof($array_licensee_numbers);$j++)
							{							 						   
								//if the licensee number = array's licensee number the, user's  store add 1'
								$current_lic_no = $array_licensee_numbers[$j];
								
								$index_user_id = $user_licensee_nos["$current_lic_no"];
								
							
								$user_store_numbers["$index_user_id"]=intval($user_store_numbers["$index_user_id"])+1;
								$user_quantities["$index_user_id"]=intval($user_quantities["$index_user_id"])+$city["stores"]["$current_lic_no"]['quantity'];;
								
							}//for($j=0;$j<=sizeof($arrayStore_nos);$j++)
						
						
						$a++;						
					}
						
					foreach($users as $data=>$user)
					{
					 	$current_user_id =$user['user_id'];
					 	
					 	//$str_user_id_index="$current_user_id";
					 	
						$wine_data = array("cspc_code" => $cspc_code,"wine_name" => $wine_name,"no_of_stores" => $user_store_numbers["$current_user_id"], "available_quantity" => $user_quantities["$current_user_id"], "location_name" => $location_name,"user_id"=>$user["user_id"], "error_code" => 0,"error_message" => "");
			
						array_push($wine_datas, $wine_data);
					}
					
				// print_r($wine_datas);
				}// other location
				else
				{
				 	$user_id = $this->getUserIdByGroupId($groupId);
					$wine_data = array("cspc_code" => $cspc_code,"wine_name" => $wine_name,"no_of_stores" => $total_stores, "available_quantity" => $total_quantities, "location_name" => $location_name,"user_id"=>$user_id, "error_code" => 0,"error_message" => "");
			
					array_push($wine_datas, $wine_data);
				}					
			}
			else
			{
				$this->setError(101, "No location name flag in Json", $data);
				exit;
			}
				

			$i++;

		}
	  
		
		$this->logMessage("Retrieved data for CSPC code: $cspc_code from $url", true);
		
	//	print_r($wine_data) ;
		return $wine_datas;
            
	}
	
	function getBCLDBLicenseeNosByUser($users)
	{
	 	$i=0;
	 	
	 	
		foreach($users as $data=>$user)
		{
		 	$user_id = $user['user_id'];
		 	
		 	//create the user store numbers array
		 	 if($i==0)
				 $user_filter =" and (user_id = $user_id";
			 else
				$user_filter ="$user_filter or user_id = $user_id";
		
			$i++;
		}
		$user_filter = "$user_filter )"	;
		
		$sql="Select c.licensee_number, uc.user_id 
			  From customers c, users_customers uc
			  Where c.customer_id = uc.customer_id
			  and c.lkup_store_type_id=6			  
			  $user_filter ";
		
		$rows = $this->db->getAll($sql);
		
		return $rows;
		
	}
	
	function isStoreBelong2User($user_id, $store_number)
	{
		$sql = "Select c.customer_id From customers c, users_customers uc 
				Where c.licensee_number =$store_number
				And c.lkup_store_type_id =6
				And c.customer_id = uc.customer_id
				and uc.user_id =$user_id";
				
		$rows = $this->db->getAll($sql);
		

        if (count($rows) > 0)		
        {
			return true;
		}
		else
		{
			return false;
		}
	}
	
    function getData($cspc_code)
    {
	  	
        $url = str_replace("[cspc_code]", $cspc_code, $this->getConfigVal("WINE_NAME_URL"));
        
        $this->logMessage("Getting Wine name for CSPC code: $cspc_code from $url", true);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, $this->getConfigVal("USER_AGENT"));
        curl_setopt($ch, CURLOPT_FAILONERROR, TRUE);


        $htmltext = curl_exec($ch);
        $cerr = curl_errno($ch);
        
        if ($cerr<>0)
        {
            if ($cerr == 22)
                $this->setError($cerr, curl_error($ch) . " WINE_NAME_URL: $url", $data, false); //got error 500 means the wine is not in the database, don't log this as error
            else
            {
                $this->setError($cerr, curl_error($ch) . " WINE_NAME_URL: $url", $data, true);
            	curl_close($ch);
            	return $data;
            }
        }
        curl_close($ch);
        
        if ($htmltext == "")
        {
            $this->setError(1, "No data returned from $url", $data);
            return 0;
        }
    
		   $data["wine_name"] = $this->extractString($htmltext, $this->getConfigVal("WINE_NAME_START"), 
            $this->getConfigVal("WINE_NAME_END"));
            
       if ($data["wine_name"] == ""||$data["wine_name"]==null)
        {

            $this->setError(2, "Wine name not found in data returned from $url", $data);
            return 0;
        }
        
        //store infor
        $storeInfo=array();
        $storeInfo = $this->getStoreInfo($cspc_code,$data["wine_name"]);
         
   	/*  	$data["no_of_stores"] = $storeInfo['no_of_stores'];
		$data["available_quantity"] = $storeInfo['available_quantity'];
		$data["location_name"] = $storeInfo['location_name'];
		$data["error_code"] = $storeInfo['error_code'];
		$data["error_message"] = $storeInfo['error_message'];
*/
	//	print_r($storeInfo);

         return $storeInfo;
        
    }
    function getUserIdByGroupId($groupId)
    {
		if($groupId!=2)
		{
			$sql="Select user_id from store_penetration_users_group where group_type_id =$groupId";
			
			$rows = $this->db->getAll($sql);
	        return $rows[0]["user_id"];
		}
		else
		 	return 0;
	}
    function getGroupTypeID($location_name)
    {
		$sql="Select group_type_id from lkup_storepen_location_type where location = '$location_name'";
		
		$rows = $this->db->getAll($sql);
        if (count($rows) <= 0)
        {
              $this->setError(2, "Error: Location name not in the database. Location name:", $location_name);
              return 0;
        }
        else
        {

             return $rows[0]["group_type_id"];
        }
		
	}
	
	function getBreakDownUsers($group_type_id =2)
    {
     	if($group_type_id ==2)
			$sql="Select user_id from store_penetration_users_group where group_type_id =2 order by user_id";
		else
		{
			$sql="Select user_id, group_type_id from store_penetration_users_group order by group_type_id asc";
		}
		
		$rows = $this->db->getAll($sql);
        return $rows;
	}

    function writeDatatoDatabase($cspc_code, $wine_name, $no_of_stores, $available_quantity,$location_name, $user_id, $isCurrentDate=false)
    {
        $when_entered = sql_escape_value('datetime', F60Date::sqlDateTime());
        $cspc_code =  sql_escape_value('varchar', $cspc_code); 
        $wine_name =  sql_escape_value('varchar', $wine_name);
		$wine_name =str_replace("''","'",$wine_name) ;
		
		$location_group_type_id = $this->getGroupTypeID($location_name);
          
        $table_name = "store_penetration_data";
        if($isCurrentDate)
        {
			
			$table_name = "store_penetration_data_current";	
		}
		
		  
        $sql = "INSERT INTO $table_name (cspc_code, wine_name, no_of_stores, available_quantity,group_type_id,location,user_id, when_entered)
           VALUES ($cspc_code, $wine_name, $no_of_stores, $available_quantity,$location_group_type_id,'$location_name',$user_id, $when_entered);";
       return $this->db->execute($sql);
    }

    function getConfigVal($config)
    {
        return $this->cfg[$config];
    }
    
    function extractString($str, $start, $end) 
    {
        $str_low = strtolower($str);
        if (strpos($str_low, $start) !== false && strpos($str_low, $end) !== false) 
        {
      
            $pos1 = strpos($str_low, $start) + strlen($start);
            
      
            $pos2 = strpos($str_low, $end, $pos1) - $pos1;
            return substr($str, $pos1, $pos2);
        }
        
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
        $mail->setFrom($this->getConfigVal("EMAIL_FROM_ADDRESS")); 
        $mail->addTo($to);
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
        $to = $this->getConfigVal("REPORT_EMAIL_RECEPIENTS");
        $subject = $this->replaceTokens($this->getConfigVal("REPORT_EMAIL_SUBJECT"));
        $body = " ";
        
        import('Form60.pages.excelStorePenetrationReport');
        
        $excelReport = new excelStorePenetrationReport(false);
        $today = getdate();
        $fileName = $excelReport->getReportFile($today["mon"], $today["year"]);
        if (file_exists($fileName))
        {
            $this->_sendEmail($to , $subject, $body, $fileName);
            unlink($fileName);
        }
    }
    
    function replaceTokens($text)
    {
        $text = str_replace("[date]",strftime("%B %d, %Y"), $text);
        $text = str_replace("[month]",strftime("%B"), $text);
        $text = str_replace("[year]",strftime("%Y"), $text);
        return $text;
    }
    
    function getDataForReport($month, $year, $group_id =0,$user_id=0)
    {   
        /*
            returns the following array
            [WINES]
                [cspc_code] [wine_name][week1_2_start][week1_2_end][week3_4_start][week3_4_end]
            [EXTERNAL_AGENCY1]
                [cspc_code] [wine_name][week1_2_start][week1_2_end][week3_4_start][week3_4_end]
            [EXTERNAL_AGENCY2]
                [cspc_code] [wine_name][week1_2_start][week1_2_end][week3_4_start][week3_4_end]
            ...
        */
        
        $report_data= array();
        
        //get regular wine names
        $regular_wine_subquery = "FROM store_penetration_data s
                left outer join external_wines e on s.cspc_code = e.cspc_code
                where month(s.when_entered) = $month and year(s.when_entered)=$year 
                and e.external_wine_id is null";
                
   											

      	$report_data["WINES"]= $this->_getDataForWines($month, $year,$regular_wine_subquery,$group_id,$user_id );
         
		 
		 //removed Empson, Dec 07, 2012 Helen 
         
         //get external agency names  
    /*    $sql = "select e.external_agency_id, e.agency_name from external_agencies e
                inner join external_wines w on e.external_agency_id = w.external_agency_id
                inner join store_penetration_data spd on w.cspc_code = spd.cspc_code
                where month(spd.when_entered)=$month and year(spd.when_entered)=$year
                group by e.external_agency_id order by e.agency_name asc;";
        $external_agencies = $this->db->getAll($sql);
        
        foreach($external_agencies as $external_agency)
        {
            $external_agency_id = $external_agency["external_agency_id"];
            $agency_name = strtoupper($external_agency["agency_name"]);
            
            $external_wine_subquery = "FROM store_penetration_data s
						               inner join external_wines e on s.cspc_code = e.cspc_code
						               where month(s.when_entered) = $month and year(s.when_entered)=$year 
						               and e.external_agency_id = $external_agency_id";
                
            $report_data[$agency_name]= $this->_getDataForWines($month, $year, $external_wine_subquery,$group_id,$user_id );
            

      	}*/
        return $report_data;
    }
    
    function getCurrentDataForReport($month, $year, $day="",$report_type=0,$location_group_id=0,$user_id=0)
    {

        /*
            returns the following array
            [WINES]
                [cspc_code] [wine_name][week1_2_start][week1_2_end][week3_4_start][week3_4_end]
            [EXTERNAL_AGENCY1]
                [cspc_code] [wine_name][week1_2_start][week1_2_end][week3_4_start][week3_4_end]
            [EXTERNAL_AGENCY2]
                [cspc_code] [wine_name][week1_2_start][week1_2_end][week3_4_start][week3_4_end]
            ...
        */
        
        if($day!="")
        {
			$table = "store_penetration_data_current";
			$querayDay=" and dayofmonth(s.when_entered) = $day";
				
		}
		else
		{
			$table = "store_penetration_data";
			$querayDay="";
		}
		
        $report_data= array();
        
        //get regular wine names
        $regular_wine_subquery = "FROM $table s
                left outer join external_wines e on s.cspc_code = e.cspc_code
                where month(s.when_entered) = $month and year(s.when_entered)=$year 
                $querayDay
                and e.external_wine_id is null";
                
        $store_pen_wine_query = "FROM store_penetration_data s
                left outer join external_wines e on s.cspc_code = e.cspc_code
                where month(s.when_entered) = $month and year(s.when_entered)=$year 
                 and e.external_wine_id is null";


        $report_data["WINES"]= $this->_getDataForWines($month, $year, $regular_wine_subquery,$location_group_id,$user_id, $day, $report_type,$store_pen_wine_query);
        
        //get external agency names
    /*    if($day!="")
        {
			
				$querayExDay=" and dayofmonth(spd.when_entered) = $day";
				
		  }
        $sql = "select e.external_agency_id, e.agency_name from external_agencies e
                inner join external_wines w on e.external_agency_id = w.external_agency_id
                inner join $table spd on w.cspc_code = spd.cspc_code
                where month(spd.when_entered)=$month and year(spd.when_entered)=$year
                $querayExDay
                group by e.external_agency_id order by e.agency_name asc;";
        $external_agencies = $this->db->getAll($sql);
        
        foreach($external_agencies as $external_agency)
        {
         	
            $external_agency_id = $external_agency["external_agency_id"];
            $agency_name = strtoupper($external_agency["agency_name"]);
            
            $external_wine_subquery = "FROM $table s
										inner join external_wines e on s.cspc_code = e.cspc_code
										where month(s.when_entered) = $month and year(s.when_entered)=$year 
										$querayDay
										and e.external_agency_id = $external_agency_id";
                
            $store_pen_wine_query = "FROM store_penetration_data s
									inner join external_wines e on s.cspc_code = e.cspc_code
									where month(s.when_entered) = $month and year(s.when_entered)=$year 
									and e.external_agency_id = $external_agency_id";
             
            $report_data[$agency_name]= $this->_getDataForWines($month, $year, $external_wine_subquery,$location_group_id,$user_id, $day, $report_type,$store_pen_wine_query);
            
        }*/
         return $report_data;
    }

 	function _getDataForWines($month, $year, $wine_subquery,$location_group_id=0 ,$user_id=0, $day="", $report_type=1, $store_pen_wine_query="")
    {
    
    	$lastDay4Month = F60Date::getLastDay4Month($month,$year);
     	  
     	  
        $week1_max_date = "'$year-$month-7 11:59:59'";
        $week2_max_date = "'$year-$month-15 23:59:59'";
        $week4_max_date = "'$year-$month-$lastDay4Month 11:59:59'";
		

        $sql = "select distinct s.cspc_code,s.wine_name,max(s.when_entered) $wine_subquery group by s.cspc_code order by s.wine_name, s.cspc_code asc;";
        $wine_names = $this->db->getAll($sql);
        
        $current_year = Date(Y);
        $current_month =Date(m);
        
        if($current_year == 2009 && $current_month<12) // before 2009 December
        {
	        $week_data_columns = "SELECT s.cspc_code, s.no_of_stores";
	        $week_data_grouping_week1 = "order by wine_name, s.cspc_code asc, s.when_entered asc;";
	        $week_data_grouping_rest = "order by wine_name, s.cspc_code asc, s.when_entered desc;";
	        
	        
	        //get current day's data
	        $sql = "$week_data_columns $wine_subquery order by wine_name, s.cspc_code asc, s.when_entered asc;";
        	$current_data = $this->db->getAll($sql);
        }
        else //After 2009 December
        {
	        $week_data_columns = "SELECT s.cspc_code, sum(s.no_of_stores) no_of_stores";


         	if($location_group_id ==0) // all locations
         	{
         	 
				$week_data_grouping_week1 = "group by s.cspc_code order by wine_name, s.cspc_code asc, s.when_entered asc;";
	        	$week_data_grouping_rest = "group by s.cspc_code order by wine_name, s.cspc_code asc, s.when_entered desc;";
	        	
	        	//current data
		    	$sql = "$week_data_columns $wine_subquery group by s.cspc_code order by wine_name, s.cspc_code asc, s.when_entered asc;";
 
			}
			else if($location_group_id !=2)
			{
			 
			 
				$week_data_grouping_week1 = "and s.group_type_id=$location_group_id group by s.group_type_id, s.cspc_code order by wine_name, s.cspc_code asc, s.when_entered asc;";
	        	$week_data_grouping_rest = "and s.group_type_id=$location_group_id  group by s.group_type_id, s.cspc_code order by wine_name, s.cspc_code asc, s.when_entered desc;";
	        	
	        	//current data
		    	$sql = "$week_data_columns $wine_subquery and s.group_type_id=$location_group_id group by s.group_type_id, s.cspc_code order by wine_name, s.cspc_code asc, s.when_entered asc;";
 	
			}
			else if($location_group_id ==2)
			{
				$week_data_grouping_week1 = "and s.group_type_id=$location_group_id and s.user_id =$user_id group by s.group_type_id, s.cspc_code order by wine_name, s.cspc_code asc, s.when_entered asc;";
	        	$week_data_grouping_rest = "and s.group_type_id=$location_group_id and s.user_id =$user_id group by s.group_type_id, s.cspc_code order by wine_name, s.cspc_code asc, s.when_entered desc;";
	        	
	        	//current data
		    	$sql = "$week_data_columns $wine_subquery and s.group_type_id=$location_group_id and s.user_id =$user_id group by s.group_type_id, s.cspc_code order by wine_name, s.cspc_code asc, s.when_entered asc;";
			}
         
         
        	$current_data = $this->db->getAll($sql);
	
			
		}

		  if($day==""||$report_type==0)
		  {
	        //get week_1_2_start
	       // $store_pen_query = $wine_subquery;
	        
	        if($report_type==0)
	        {
					$wine_subquery = $store_pen_wine_query;
			}
				
	        $sql = "$week_data_columns $wine_subquery
	                and s.when_entered<=$week1_max_date $week_data_grouping_week1";
	        $week1_2_starts = $this->db->getAll($sql);
	     
	        
	        //get week_1_2_end
	        $sql = "$week_data_columns $wine_subquery
	                and s.when_entered>$week1_max_date and s.when_entered<=$week2_max_date $week_data_grouping_rest";
	        $week1_2_ends = $this->db->getAll($sql);
	        
	        //get week_3_4 end
	        $sql = "$week_data_columns $wine_subquery
	                and s.when_entered>$week2_max_date and s.when_entered<=$week4_max_date $week_data_grouping_rest";
	        $week3_4_ends = $this->db->getAll($sql);
        }
        //create the array
        $wines_data = array();
        $nRow=0;
      //  print $report_type;
        foreach($wine_names as $wine)
        {
        
            $name = $wine["wine_name"];
            $cspc_code = $wine["cspc_code"];
       
            
     //       $store_no = $wine["no_of_stores"];
           // print $cspc_code;
            
            if($day==""||$report_type==0)
            {
	            $week1_2_start = $this->_getNoOfStoresForWine($cspc_code,$week1_2_starts);
	            
	            
	            $week1_2_end = $this->_getNoOfStoresForWine($cspc_code,$week1_2_ends);
	            $week3_4_start = $week1_2_end;
	            $week3_4_end = $this->_getNoOfStoresForWine($cspc_code,$week3_4_ends);
	            if($report_type ==0)
	            {
						$current_day = $this->_getNoOfStoresForWine($cspc_code,$current_data);
						$wine_data = array("cspc_code" => $cspc_code, "wine_name" => $name, 
											"week1_2_start" => $week1_2_start,
	                              "week1_2_end" => $week1_2_end, "week3_4_start" => $week3_4_start,
	                              "week3_4_end" => $week3_4_end, "current_data"=> $current_day);
					}
	            else
	            {
	             
	            	$wine_data = array("cspc_code" => $cspc_code, "wine_name" => $name, 
											"week1_2_start" => $week1_2_start,
	                              "week1_2_end" => $week1_2_end, "week3_4_start" => $week3_4_start,
	                              "week3_4_end" => $week3_4_end);
	                              
	                  
	            }
	         }
	         else
	         {
	             $current_day = $this->_getNoOfStoresForWine($cspc_code,$current_data);

				 $wine_data = array("cspc_code" => $cspc_code, "wine_name" => $name, 
											"current_data" => $current_day);
											
					
			}
             array_push($wines_data, $wine_data);
            
         }

   
   	if($day==""||$report_type==0)
   	{
   	 	  if($report_type ==0)
   	 	  {
   	 	   //	print here;
					$wines_data = $this->sortWineData($wines_data, "current_data", $report_type);
			  }
			  else
			  {
			   
			 
		        if( $wines_data[0]["week3_4_end"]!=0)
				  {
				   	//sort by week 3's number
				   	$wines_data = $this->sortWineData($wines_data, "week3_4_end");
				  }
				  else if($wines_data[0]["week1_2_end"]!=0)
				  {
				   	// sort by week4's start
						$wines_data = $this->sortWineData($wines_data, "week3_4_start");
					}
					else if($wines_data[0]["week1_2_start"]!=0)
					{
						$wines_data = $this->sortWineData($wines_data, "week1_2_start");
					}
				}
		}
		else
		{
			$wines_data = $this->sortWineData($wines_data, "current_data");
		}
	 	
	 	
       return $wines_data;
        
    }
    

    


	
// sort from highest to lowest
    function sortWineData($wine_data, $keyField, $report_type=1)
    {
    
	  		$cntRecs= count($wine_data);  		
	  
    
	 		if($keyField =="current_data" ) 		
	 		{
	 		 	if($report_type==1)//current for no bcldb
	 		 	{
					$tempData = array("cspc_code" => $wine_data[0]["cspc_code"], "wine_name" => $wine_data[0]["wine_name"], "current_data" => $wine_data[0]["current_data"]);
				}
				else
				{
			//	 print step1;
					$tempData = array("cspc_code" => $wine_data[0]["cspc_code"], "wine_name" => $wine_data[0]["wine_name"], "week1_2_start" => $wine_data[0]["week1_2_start"],
		                            "week1_2_end" => $wine_data[0]["week1_2_end"], "week3_4_start" => $wine_data[0]["week3_4_start"],
		                            "week3_4_end" => $wine_data[0]["week3_4_end"], "current_data" => $wine_data[0]["current_data"]);
				}
			}
			else
			{
		     		$tempData = array("cspc_code" => $wine_data[0]["cspc_code"], "wine_name" => $wine_data[0]["wine_name"], "week1_2_start" => $wine_data[0]["week1_2_start"],
		                            "week1_2_end" => $wine_data[0]["week1_2_end"], "week3_4_start" => $wine_data[0]["week3_4_start"],
		                            "week3_4_end" => $wine_data[0]["week3_4_end"]);
		      
         }                  
			$keepChecking = true;
			
			$j=0;
			$i =0;
			
			for($i=0;$i<($cntRecs);$i++)
			{
			  
				for($j=$i+1;$j<($cntRecs);$j++)
				{
					
					 	if($keyField =="current_data") 		
	 					{		
		 					if($report_type ==1)
		 					{
								
								 if($wine_data[$i][$keyField]<$wine_data[$j][$keyField])
								 {
									//swap($wine_data[$i], $wine_data[$j]);
									$tempData1 = array("cspc_code" => $wine_data[$i]["cspc_code"], "wine_name" => $wine_data[$i]["wine_name"], "current_data" => $wine_data[$i]["current_data"]);
			                            
			                  $tempData2 = array("cspc_code" => $wine_data[$i+1]["cspc_code"], "wine_name" => $wine_data[$j]["wine_name"], "current_data" => $wine_data[$j]["current_data"]);
			                            
			                    $wine_data[$i]["cspc_code"] = $tempData2["cspc_code"];
			                    $wine_data[$i]["wine_name"] = $tempData2["wine_name"];
			                    $wine_data[$i]["current_data"] = $tempData2["current_data"];
			                  
			                    $wine_data[$j]["cspc_code"] = $tempData1["cspc_code"];
			                    $wine_data[$j]["wine_name"] = $tempData1["wine_name"];
			                    $wine_data[$j]["current_data"] = $tempData1["current_data"];
			                    
								}
							}
							else
							{
					
								 if($wine_data[$i][$keyField]<$wine_data[$j][$keyField])
									{
										$tempData1 = array("cspc_code" => $wine_data[$i]["cspc_code"], "wine_name" => $wine_data[$i]["wine_name"], "week1_2_start" => $wine_data[$i]["week1_2_start"],
				                            "week1_2_end" => $wine_data[$i]["week1_2_end"], "week3_4_start" => $wine_data[$i]["week3_4_start"],
				                            "week3_4_end" => $wine_data[$i]["week3_4_end"],"current_data" => $wine_data[$i]["current_data"]);
				                            
				                  $tempData2 = array("cspc_code" => $wine_data[$i+1]["cspc_code"], "wine_name" => $wine_data[$j]["wine_name"], "week1_2_start" => $wine_data[$j]["week1_2_start"],
				                            "week1_2_end" => $wine_data[$j]["week1_2_end"], "week3_4_start" => $wine_data[$j]["week3_4_start"],
				                            "week3_4_end" => $wine_data[$j]["week3_4_end"],"current_data" => $wine_data[$j]["current_data"]);
				                            
				                    $wine_data[$i]["cspc_code"] = $tempData2["cspc_code"];
				                    $wine_data[$i]["wine_name"] = $tempData2["wine_name"];
				                    $wine_data[$i]["week1_2_start"] = $tempData2["week1_2_start"];
				                    $wine_data[$i]["week1_2_end"] = $tempData2["week1_2_end"];
				                    $wine_data[$i]["week3_4_start"] = $tempData2["week3_4_start"];
				                    $wine_data[$i]["week3_4_end"] = $tempData2["week3_4_end"];
				                    $wine_data[$i]["current_data"] = $tempData2["current_data"];
				                  
				                    
				                    $wine_data[$j]["cspc_code"] = $tempData1["cspc_code"];
				                    $wine_data[$j]["wine_name"] = $tempData1["wine_name"];
				                    $wine_data[$j]["week1_2_start"] = $tempData1["week1_2_start"];
				                    $wine_data[$j]["week1_2_end"] = $tempData1["week1_2_end"];
				                    $wine_data[$j]["week3_4_start"] = $tempData1["week3_4_start"];
				                    $wine_data[$j]["week3_4_end"] = $tempData1["week3_4_end"];
				                    $wine_data[$j]["current_data"] = $tempData1["current_data"];
				               }
							}
					}
					else
					{
					 if($wine_data[$i][$keyField]<$wine_data[$j][$keyField])
					 {
						$tempData1 = array("cspc_code" => $wine_data[$i]["cspc_code"], "wine_name" => $wine_data[$i]["wine_name"], "week1_2_start" => $wine_data[$i]["week1_2_start"],
	                            "week1_2_end" => $wine_data[$i]["week1_2_end"], "week3_4_start" => $wine_data[$i]["week3_4_start"],
	                            "week3_4_end" => $wine_data[$i]["week3_4_end"]);
	                            
	                  $tempData2 = array("cspc_code" => $wine_data[$i+1]["cspc_code"], "wine_name" => $wine_data[$j]["wine_name"], "week1_2_start" => $wine_data[$j]["week1_2_start"],
	                            "week1_2_end" => $wine_data[$j]["week1_2_end"], "week3_4_start" => $wine_data[$j]["week3_4_start"],
	                            "week3_4_end" => $wine_data[$j]["week3_4_end"]);
	                            
	                    $wine_data[$i]["cspc_code"] = $tempData2["cspc_code"];
	                    $wine_data[$i]["wine_name"] = $tempData2["wine_name"];
	                    $wine_data[$i]["week1_2_start"] = $tempData2["week1_2_start"];
	                    $wine_data[$i]["week1_2_end"] = $tempData2["week1_2_end"];
	                    $wine_data[$i]["week3_4_start"] = $tempData2["week3_4_start"];
	                    $wine_data[$i]["week3_4_end"] = $tempData2["week3_4_end"];
	                    
	                    $wine_data[$j]["cspc_code"] = $tempData1["cspc_code"];
	                    $wine_data[$j]["wine_name"] = $tempData1["wine_name"];
	                    $wine_data[$j]["week1_2_start"] = $tempData1["week1_2_start"];
	                    $wine_data[$j]["week1_2_end"] = $tempData1["week1_2_end"];
	                    $wine_data[$j]["week3_4_start"] = $tempData1["week3_4_start"];
	                    $wine_data[$j]["week3_4_end"] = $tempData1["week3_4_end"];
	               }
					}
				}//end for
			}
			
		
			return $wine_data;
	}
    function _getNoOfStoresForWine($cspc_code, $weekly_data_array)
    {
     
        $noOfStores = 0;
        
        foreach($weekly_data_array as $data)
        {
      
            if ($cspc_code == $data["cspc_code"])
            {
             
                $noOfStores = $data["no_of_stores"];
            
               break;
            }
        }
        return $noOfStores;
    }
    
    //called by ajax to get report months for a report year
    function getMonthsSelectHtml($controlID, $reportYear)
    {

            $sql="Select distinct monthname(when_entered) as monthName, month(when_entered) as reportMonth from store_penetration_data
                 where year(when_entered) = $reportYear order by reportMonth desc; ";
            $results = $this->db->getAll($sql);
            
            $strSelect = "var c = document.getElementById(\"".$controlID."\");";
            $strSelect .= "c.options.length=0;";
            $i = 0;
            foreach($results  as $result)
            {
                if ($i==0)
                {
                        $strSelect .= 'c.options['.$i.']=new Option("'.$result['monthName'].'", "'.$result['reportMonth'].'", false, true);';
                }
                else
                        $strSelect .= 'c.options['.$i.']=new Option("'.$result['monthName'].'", "'.$result['reportMonth'].'", false, false);';
                $i++;
            }
            
            
            return $strSelect;
    }
}
?>
