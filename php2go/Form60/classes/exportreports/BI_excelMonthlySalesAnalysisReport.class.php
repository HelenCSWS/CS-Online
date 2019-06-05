<?php

import('Form60.bll.bllf60Reports');
import('Form60.util.excel.writer');
import('Form60.bll.bllSalesAnalysisData');
import('Form60.util.F60Common');

import('Form60.base.F60DbUtil');


class BI_excelMonthlySalesAnalysisReport
{
    var $report_month;
    var $report_year;
    var $SPData;
    var $reportData;
    
    var $estate_id;
    var $titleText="";
    var $estate_name="";
    
    var $fileType=1;  //
    
    var $isExpiry=false;
    
    var $nEndCol ="L";  // the end col of spread sheet
    var $nStartCol="A";
    
    var $bllData=null;
    
    var $colIndexs=array(); // 0,25
    

    var $totalCols=6; // total columns
    
     
    var $columns;
    var $rowDataArray;
    
    var $ab_columns;
    var $ab_rowDataArray;

    var $data_cfg;
    
    
    var $user_id;
    var $user_name;
    
    var $province_id=1;
    
    var $lastMthCMs =array();
    
    var $fileName="";
       
    function BI_excelMonthlySalesAnalysisReport($isSendingEmail=false)
    {
     		
	
		$this->columns = array("A"=>array("index"=>0, "width"=>43), "B"=>array("index"=>1, "width"=>10), 
		        					"C"=>array("index"=>2, "width"=>19), "D"=>array("index"=>3, "width"=>33), 
										  "E"=>array("index"=>4, "width"=>13), "F"=>array("index"=>5, "width"=>10),
										  "G"=>array("index"=>6, "width"=>10),"H"=>array("index"=>7, "width"=>10),
										  "I"=>array("index"=>8, "width"=>10),"J"=>array("index"=>9, "width"=>10),
										  "K"=>array("index"=>10, "width"=>10)
										  ); // first static title names 6 cols	
										  
		if(!$isSendingEmail)
		{
		
			$this->user_id =	$_REQUEST["user_id"];		
	
        
			$this->province_id = $_COOKIE["F60_PROVINCE_ID"];
		
			$this->report_month =	$_REQUEST["report_month"];
			$this->report_year =	$_REQUEST["report_year"];
		
			$this->generateSpreadsheet(false);		        
		}
		
   }   
   
  
  function generateReportSheet($report_month,$report_year,$province_id,$user_id)
  {
		$this->province_id = $province_id;
	
		$this->report_month =$report_month;
		$this->report_year  =$report_year;

		$this->user_id = $user_id;
		return $this->generateSpreadsheet(true);	
	}
  
	
	function getReportBasiceInfo($user_name)
    {
	
		
		$month_name = date("F", mktime(0, 0, 0, $this->report_month, 10));

			
		 $province=($this->province_id==1?"BC":"Alberta");
		 $title = "$province Sales Summary $user_name: $month_name $this->report_year";
		
		
		
		return $title;
		
		
	}
	
    function generateSpreadsheet($returnFile=true)
    {			    

		
			$salesAnaylsisiData = new salesAnalysisData();
			
	      
   			$users = $salesAnaylsisiData->getUsers($this->report_year,$this->report_month,$this->province_id,$this->user_id);
   			
   			
   			$province=($this->province_id==1?"BC":"Alberta");
			$month_name = date("F", mktime(0, 0, 0, $this->report_month, 10));
   		  	$fileName = "$province Sales Summary $month_name $this->report_year";
   			 
			
			$fileName = $fileName. ".xls";
	        $filePath = ROOT_PATH . "salesreports/" . $fileName;
	        
	        if ($returnFile)
            	$workbook = new Spreadsheet_Excel_Writer($filePath);
        	else
	        {
	            $workbook = new Spreadsheet_Excel_Writer();
	            $workbook->send($fileName);
	        }
	        
	        $workbook->setVersion(8);
   			
   			foreach($users as $user)
   			{
   				 
				$user_name =$user["user_name"];
				
				$user_name =str_replace("Garry Preiswerck","Nwt Liquor Commission",$user_name);
				
			//	$user_name ="lycy";
				//	$this->user_id =91;
				
				$this->user_id =$user["user_id"];
			

				
				$this->titleText=$this->getReportBasiceInfo($user_name);
				
					$worksheetName =$user_name;
			    
		        $sp =& $workbook->addWorksheet($worksheetName);
		        
		          //set column widths	        
		        foreach($this->columns as $column)
		        {
		            $sp->setColumn($column["index"], $column["index"], $column["width"]);
		        }
		        
		        
		        $row = 0;
		        
		        $this->_writeTitle($workbook, $sp, $row); //$row++
		        
		        //Sales summary
		        $row++; // blank row
	     		$this->nEndCol ="H";
	     		
	     		if($this->province_id==2)
	     				$this->nEndCol ="F";
	     				
	     		$this->_writeSalesSummary($workbook, $sp, $row);
	     		
			   
			   //Sales summary
		       $row+=2; // blank row
	     	   $this->nEndCol ="D";
	     	   $this->_writeCMSummary($workbook, $sp, $row);
	     		
	     		
			    $row+=1;
			    $this->nStartCol ="A";
	     		$this->nEndCol ="L";
				$this->_writeColumnHeaders($workbook, $sp, $row);
				
				
		 	
				 $this->_writeData($workbook, $sp, $row);
		        
				
			}
   			$result = $workbook->close();
         
	   		if ($returnFile)
            	return $filePath;
			
       /* 	$this->titleText=$this->getReportBasiceInfo();
        	
        	$this->report_month;	        
			 
       		$worksheetName =$this->titleText;
	        
	        $fileName = $this->titleText. ".xls";
	        $filePath = ROOT_PATH . "logs/" . $fileName;
	        
	        if ($returnFile)
	            $workbook = new Spreadsheet_Excel_Writer($filePath);
	        else
	        {
	            $workbook = new Spreadsheet_Excel_Writer();
	            $workbook->send($fileName);
	        }
	        
	        $workbook->setVersion(8);
	        
	        
	        $sp =& $workbook->addWorksheet($worksheetName);
	        
	        //set column widths	        
	        foreach($this->columns as $column)
	        {
	            $sp->setColumn($column["index"], $column["index"], $column["width"]);
	        }
	        
	        
	        $row = 0;
	        
	      $this->_writeTitle($workbook, $sp, $row); //$row++
	        
	        //Sales summary
	        $row++; // blank row
     		$this->nEndCol ="H";
     		
     		if($this->province_id==2)
     				$this->nEndCol ="F";
     				
     		$this->_writeSalesSummary($workbook, $sp, $row);
     		
		   
		   //Sales summary
	       $row+=2; // blank row
     	   $this->nEndCol ="D";
     	   $this->_writeCMSummary($workbook, $sp, $row);
     		
     		
		    $row+=1;
		    $this->nStartCol ="A";
     		$this->nEndCol ="L";
			$this->_writeColumnHeaders($workbook, $sp, $row);
			
			
	 	
			 $this->_writeData($workbook, $sp, $row);
      
        	$result = $workbook->close();
         
	   		if ($returnFile)
            	return $filePath;*/
       
    }
    
    function debugText($text)
    {
	    $fp = fopen("logs/test.log","a");
		fputs($fp, $text);
		fclose($fp);
		
	}
	


    function _writeSalesSummary(&$workbook, & $sp, & $row)
    {
	
	//header
		   $fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'fgcolor'=>'silver', 
                'bgcolor'=>'black', 'border'=>1, 'align'=>'center', 'valign'=>'bottom','bold'=>1,
                'top'=>1, 'bottom'=>1));
        //$fm->setTextWrap();
        $columnHeader = $fm;
        
      
        
        $fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'fgcolor'=>'silver', 
                'bgcolor'=>'black', 'border'=>1, 'align'=>'left', 'valign'=>'bottom','bold'=>1,
                'top'=>1, 'bottom'=>1));       
        $columnHeaderLeft = $fm;
        
        
        
        $fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'fgcolor'=>'silver', 
                'bgcolor'=>'black', 'border'=>1, 'align'=>'right', 'valign'=>'bottom','bold'=>1,
                'top'=>1, 'bottom'=>1));       
        $columnHeaderRight = $fm;
	
	// content
	
		$fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'border'=>1));
		$arialNormalBorder = $fm;
		
		$fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'align'=>'right','border'=>1));
		$arialNormalBorderRight = $fm;
		
		$fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'align'=>'left','border'=>1));
		$arialNormalBorderLeft = $fm;

		$fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'align'=>'right','border'=>1,'numformat'=>'0'));
		$arialNormalBorderNumRight = $fm;
		
		$fm = & $workbook->addFormat(array('fontfamily'=>'Arial','size'=>10, 'align'=>'right','border'=>1));
        $fm ->setNumFormat("$#,##0.00;[Black]-$#,##0.00");
        $arialNormalCurrency = $fm;
		
		$fm = & $workbook->addFormat(array('fontfamily'=>'Arial','size'=>10, 'fgcolor'=>'silver', 'bold'=>1,
                'bgcolor'=>'black','align'=>'right','border'=>1));
        $fm ->setNumFormat("$#,##0.00;[Black]-$#,##0.00");
        $arialNormalHeaderCurrency = $fm;

		$i=1;
		$startRow= $row; //remember the start row
		$nIndex=0;
	
		
		$salesAnaylsisiData = new salesAnalysisData();
		//$user_id,$province_id,$sale_month,$sale_year
		$summaryInfos = $salesAnaylsisiData->getMonthlyGrandTotal($this->user_id,$this->report_month,$this->report_year);
		$summaryInfosDetails = $salesAnaylsisiData->getMonthlySummary($this->user_id,$this->report_month,$this->report_year);
	//	print_r($summaryInfos);
	
	
		
		
	
			$gTotalCS = $summaryInfos[0]["total_cases"];
			$gTotalVL = $summaryInfos[0]["total_case_value"];
			$gTotalWS = $summaryInfos[0]["total_whole_sale"];
			$gTotalRT = $summaryInfos[0]["total_retail"];
			$gTotalPT = $summaryInfos[0]["total_profit"];
			
		
		 $i=0;
		foreach($summaryInfosDetails as $info_date)
	    {
	//sa.customer_name,lktype.caption store_type, sa.billing_address_city city,sa.billing_address_street address,sa.licensee_number,lkp.product_name,
			  										
	//sd.total_cases, sd.total_case_value, sd.total_whole_sale,total_retail,sd.total_profit
		
		
		
	
			if($i==0) //write title first
			{
			 	if($this->province_id ==1)
			 	{
					$values = array(
						array("data"=>"Summary", "format"=>$columnHeaderLeft), 
						array("data"=>"Sold cases", "format"=>$columnHeaderRight),
						array("data"=>"Case value", "format"=>$columnHeaderRight), 
						array("data"=>"Wholesale", "format"=>$columnHeaderRight), 
						array("data"=>"Retail", "format"=>$columnHeaderRight),
						array("data"=>"Gross profit", "format"=>$columnHeaderRight),
						array("data"=>"Percent", "format"=>$columnHeaderRight)

						);	
                }
                else
                {
						$values = array(
						array("data"=>"Summary", "format"=>$columnHeaderLeft), 
						array("data"=>"Sold cases", "format"=>$columnHeaderRight),
						array("data"=>"Case value", "format"=>$columnHeaderRight), 
						array("data"=>"Wholesale", "format"=>$columnHeaderRight), 
						array("data"=>"Gross profit", "format"=>$columnHeaderRight)
					

						);	
				}
       			
        		$this->_writeRow($sp, $values, $row, $columnHeader);  
        		$row++;
			}
			//license_name, sum(total_cases), sum(total_case_value), sum(total_whole_sale), sum(total_retail),sum(total_profit)
			$license= $info_date["license_name"];
			$total_case = $info_date["total_cases"];
	
			$total_case_value = $info_date["total_case_value"];
			$total_whole_sale = $info_date["total_whole_sale"];
			$total_retail = $info_date["total_retail"];
			$total_profit = $info_date["total_profit"];
			
		//	$precentage = $total_case;///$gTotalCS;
			$precentage = F60Common::percentage($total_case,$gTotalCS,2);
		
		
		//	$value=round(($case_value/$case_sold),2);
			if($this->province_id ==1)
			{
				$values = array(
								array("data"=>$license,"format"=>$arialNormalBorderLeft), 
								array("data"=>$total_case,"format"=>$arialNormalBorderNumRight), 
								array("data"=>$total_case_value,"format"=>$arialNormalBorderNumRight), 
								array("data"=>$total_whole_sale,"format"=>$arialNormalCurrency), 
								array("data"=>$total_retail,"format"=>$arialNormalCurrency), 
								array("data"=>$total_profit,"format"=>$arialNormalCurrency), 
								array("data"=>$precentage,"format"=>$arialNormalBorderNumRight)				
													
								); 
			}
			else
			{
					$values = array(
								array("data"=>$license,"format"=>$arialNormalBorderLeft), 
								array("data"=>$total_case,"format"=>$arialNormalBorderNumRight), 
								array("data"=>$total_case_value,"format"=>$arialNormalBorderNumRight), 
								array("data"=>$total_whole_sale,"format"=>$arialNormalCurrency), 
								array("data"=>$total_profit,"format"=>$arialNormalCurrency)		
													
								); 
			}
			
						             
            $this->_writeRow($sp, $values, $row, $arialNormalBorderNumRight); 
           
            $row++;
            
            
            
            $i++;
            
        }
        
        
        if($this->province_id==1)
        {
    	 //write grand total
			
			$values = array(
				array("data"=>"Total", "format"=>$columnHeaderRight), 
				array("data"=>$gTotalCS, "format"=>$columnHeaderRight),
				array("data"=>$gTotalVL, "format"=>$columnHeaderRight), 
				array("data"=>$gTotalWS, "format"=>$arialNormalHeaderCurrency), 
				array("data"=>$gTotalRT, "format"=>$arialNormalHeaderCurrency),
				array("data"=>$gTotalPT, "format"=>$arialNormalHeaderCurrency),
				array("data"=>"", "format"=>$columnHeaderRight)
	
				);	
	                
			
			$this->_writeRow($sp, $values, $row, $columnHeader);  
		}
	    	
		
        
	                
	}
	function _writeCMSummary(&$workbook, & $sp, & $row)
    {
	
	    //header
		$fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'fgcolor'=>'silver', 
                'bgcolor'=>'black', 'border'=>1, 'align'=>'center', 'valign'=>'bottom','bold'=>1,
                'top'=>1, 'bottom'=>1));
        //$fm->setTextWrap();
        $columnHeader = $fm;
        
      
         $fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'fgcolor'=>'silver', 
                'bgcolor'=>'black', 'border'=>1, 'align'=>'right', 'valign'=>'bottom','bold'=>1,
                'top'=>1, 'bottom'=>1));       
        $columnHeaderRight = $fm;
        
        $fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'fgcolor'=>'silver', 
                'bgcolor'=>'black', 'border'=>1, 'align'=>'left', 'valign'=>'bottom','bold'=>1,
                'top'=>1, 'bottom'=>1));       
        $columnHeaderLeft = $fm;
        
        
        
        $fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'fgcolor'=>'silver', 'color'=>'blue',
                'bgcolor'=>'black', 'border'=>1, 'align'=>'right', 'valign'=>'bottom','bold'=>0,
                'top'=>1, 'bottom'=>1));       
        $columnHeaderRightRed = $fm;
        
        
         $fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'fgcolor'=>'silver', 'color'=>'blue',
                'bgcolor'=>'black', 'border'=>1, 'align'=>'left', 'valign'=>'bottom','bold'=>0,
                'top'=>1, 'bottom'=>1));       
        $columnHeaderLeftRed = $fm;
        
        
        
       
	
	// content
	
		$fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'border'=>1));
		$arialNormalBorder = $fm;
		
		$fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'align'=>'right','border'=>1));
		$arialNormalBorderRight = $fm;
		
		$fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'align'=>'left','border'=>1));
		$arialNormalBorderLeft = $fm;

		$fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'align'=>'right','border'=>1,'numformat'=>'0'));
		$arialNormalBorderNumRight = $fm;
		
		$fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'align'=>'right','border'=>1,'color'=>'blue','numformat'=>'0'));
		$arialNormalBorderNumRightRed = $fm;
		
		$fm = & $workbook->addFormat(array('fontfamily'=>'Arial','size'=>10, 'align'=>'right','border'=>1));
        $fm ->setNumFormat("$#,##0.00;[Black]-$#,##0.00");
        $arialNormalCurrency = $fm;
		
		$fm = & $workbook->addFormat(array('fontfamily'=>'Arial','size'=>10, 'fgcolor'=>'silver', 
                'bgcolor'=>'black','align'=>'right','border'=>1));
        $fm ->setNumFormat("$#,##0.00;[Black]-$#,##0.00");
        $arialNormalHeaderCurrency = $fm;

		$i=1;
		$startRow= $row; //remember the start row
		$nIndex=0;
	
		
		$salesAnaylsisiData = new salesAnalysisData();
		//$user_id,$province_id,$sale_month,$sale_year
		$summaryInfos = $salesAnaylsisiData->getMonthlyTotalCMS($this->user_id,$this->report_month,$this->report_year,0);
		$summaryInfosDetails = $salesAnaylsisiData->getMonthlyTotalCMS($this->user_id,$this->report_month,$this->report_year,1);
	//	print_r($summaryInfos);
	
	
		$gTotalPurchasedCMS = $summaryInfos[0]["totalRecs"];
			
			
		$arPurchasedCMs = array();
		$arPurchasedCM = array();
	 	$i=0;
	 	
	 	

	 	//get customers who didn't 
	 	$lastMthCMs = $salesAnaylsisiData->getLastMthCMTotalInCurrentMth($this->user_id,$this->report_month,$this->report_year,1);
	 	$notPCHCMs = count($lastMthCMs);
	 	
	 
	 	$i=0;
	 	foreach( $lastMthCMs as $lastMthCM)
	 	{
			$this->lastMthCMs[$i]=$lastMthCM["customer_id"];
			$i++;
		}
		

			$i=0; 	
	 	if($this->province_id==2)//Ab start
	 	{
	 	 
			// header 	 
	 	 	//TOTAL CUSTOMER
			$summaryBSInfos = $salesAnaylsisiData->getMonthlyTotalBaseCMS($this->user_id,2,0);
			
			$gTotalCMS = $summaryBSInfos[0]["totalRecs"];
			
			$precentage = F60Common::percentage($gTotalPurchasedCMS,$gTotalCMS,2);
				
			$values = array(
					array("data"=>"Customers who purchased this month", "format"=>$columnHeaderLeft), 
					array("data"=>$gTotalPurchasedCMS, "format"=>$arialNormalBorderNumRight),
					array("data"=>$precentage, "format"=>$arialNormalBorderNumRight),
					array("data"=>"Customer base", "format"=>$columnHeaderLeft),
					array("data"=>$gTotalCMS, "format"=>$arialNormalBorderNumRight),
				
					);	
					
					$this->nEndCol="F";
					$this->_writeRow($sp, $values, $row, $columnHeader);  
	        		$row++;
	        		
	        // didn't purchased in last month
	        
	      
	        		$precentage = F60Common::percentage($notPCHCMs,$gTotalPurchasedCMS,2);
	        	  
	        	  if(count($lastMthCMs)>100)
	        	  {
					$notPCHCMs ="N/A";
					$precentage ="N/A";
					
					}
				  $values = array(
					array("data"=>"Customers who didn't purchased last month", "format"=>$columnHeaderLeftRed), 
					array("data"=>$notPCHCMs, "format"=>$arialNormalBorderNumRightRed),
					array("data"=>$precentage, "format"=>$arialNormalBorderNumRightRed)

				
					);	
					
					$this->nEndCol="D";
					$this->_writeRow($sp, $values, $row, $columnHeader);  
	        		$row++;
	        		
	        	//didn't purchased in current month
	        	
	        	$nMSCMs= $gTotalCMS - $gTotalPurchasedCMS;
	        	
	        	$precentage = F60Common::percentage($nMSCMs,$gTotalCMS,2);
	        	$values = array(
					array("data"=>"Customers who didn't purchased current month", "format"=>$columnHeaderLeft), 
					array("data"=>$nMSCMs, "format"=>$arialNormalBorderNumRight),
					array("data"=>$precentage, "format"=>$arialNormalBorderNumRight)				
					);	
					
				
					$this->_writeRow($sp, $values, $row, $columnHeader);  
	        		$row++;		
			
		}
	 	else//BC Start
	 	{
			foreach($summaryInfosDetails as $info_date)
		    {
			
		
				if($i==0) //write title first
				{
				 	
						$values = array(
							array("data"=>"Customers who purchased this month", "format"=>$columnHeaderLeft), 
							array("data"=>$gTotalPurchasedCMS, "format"=>$columnHeaderRight),
							array("data"=>"Percent", "format"=>$columnHeaderRight)
						
							);	
	                        
	       			
	        		$this->_writeRow($sp, $values, $row, $columnHeader);  
	        		$row++;
				}
				//license_name, sum(total_cases), sum(total_case_value), sum(total_whole_sale), sum(total_retail),sum(total_profit)
				$license= $info_date["license_name"];
				$total_recs = $info_date["totalRecs"];
				
			//	$precentage = $total_case;///$gTotalCS;
				$precentage = F60Common::percentage($total_recs,$gTotalPurchasedCMS,2);
			
			
			//	$value=round(($case_value/$case_sold),2);
				$values = array(
								array("data"=>$license,"format"=>$arialNormalBorderLeft), 
								array("data"=>$total_recs,"format"=>$arialNormalBorderNumRight), 
								array("data"=>$precentage,"format"=>$arialNormalBorderNumRight)				
													
								); 			
	            $this->_writeRow($sp, $values, $row, $arialNormalBorderNumRight); 
	            
	           
	            
	            $arPurchasedCMs[$license]=$total_recs;
	           
	          
	           
			    $row++;
	            
	            $i++;
	            
	        }
	      
	    
	        $precentage = F60Common::percentage($notPCHCMs,$gTotalPurchasedCMS,2);
	        
	          if(count($lastMthCMs)>100)
	          {
					$notPCHCMs ="N/A";
					$precentage ="N/A";
					
				}
	        $values = array(
				array("data"=>"Customers who didn't purchased last month", "format"=>$columnHeaderLeftRed), 
				array("data"=>$notPCHCMs, "format"=>$columnHeaderRightRed),
				array("data"=>$precentage, "format"=>$columnHeaderRightRed)
	
				);	
	     
			$this->_writeRow($sp, $values, $row, $columnHeader);  
	        $endRow =$row;
	        
	      	
			// Total customers
			
			$arCMs = array();
			$arCM = array();
			
			$summaryInfos = $salesAnaylsisiData->getMonthlyTotalBaseCMS($this->user_id,1,0);
			$summaryInfosDetails = $salesAnaylsisiData->getMonthlyTotalBaseCMS($this->user_id,1,1);
	
	       	$gTotalCMS = $summaryInfos[0]["totalRecs"];
	       	
	       
		    $i=0;
		    $this->nStartCol = "E";
		    $this->nEndCol = "H";
		    
		    
	        
			$row=$startRow;
	        		
	        		
			foreach($summaryInfosDetails as $info_date)
		    {
			
		
				if($i==0) //write title first
				{
						$values = array(
							array("data"=>"Customer base", "format"=>$columnHeaderLeft), 
							array("data"=>$gTotalCMS, "format"=>$columnHeaderRight),
							array("data"=>"Percent", "format"=>$columnHeaderRight)
						
							);	
	                        
	       			
	        		$this->_writeRow($sp, $values, $row, $columnHeader);  
	        		$row++;
				}
				//license_name, sum(total_cases), sum(total_case_value), sum(total_whole_sale), sum(total_retail),sum(total_profit)
				$license= $info_date["license_name"];
				$total_recs = $info_date["totalRecs"];
				
			//	$precentage = $total_case;///$gTotalCS;
				$precentage = F60Common::percentage($total_recs,$gTotalCMS,2);
			
			
			//	$value=round(($case_value/$case_sold),2);
				$values = array(
								array("data"=>$license,"format"=>$arialNormalBorderLeft), 
								array("data"=>$total_recs,"format"=>$arialNormalBorderNumRight), 
								array("data"=>$precentage,"format"=>$arialNormalBorderNumRight)				
													
								); 
				
				
				$arCMs[$license]=$total_recs;
	           
	          
							             
	            $this->_writeRow($sp, $values, $row, $arialNormalBorderNumRight); 
	           
	            $row++;
	            
	            
	            
	            $i++;
	            
	        }       
	        
	        
	         	 //write customer who didn't purchased in current month
			
			 $this->nStartCol = "A";
		    $this->nEndCol = "D";
		    
		    $notCMs = $gTotalCMS - $gTotalPurchasedCMS;
		    
		    
			$values = array(
				array("data"=>"Customers who didn't purchased current month", "format"=>$columnHeaderLeft), 
				array("data"=>$notCMs, "format"=>$columnHeaderRight),
				array("data"=>"Percent", "format"=>$columnHeaderRight)
	
				);	
				
		
	                
			$row=$endRow;		
			$row+=2;
			$this->_writeRow($sp, $values, $row, $columnHeader);  
	 
	 		$row++;
	 		
	 		$i=0;
	 		$licenses =array_keys($arPurchasedCMs);
			for($i=0;$i<=count($licenses)-1;$i++)
			{
			 	
				$license = $licenses[$i];
			
				$total_recs = $arCMs[$license]- $arPurchasedCMs[$license];
				$precentage =F60Common::percentage($total_recs,$notCMs,2);
				
				$values = array(
								array("data"=>$license,"format"=>$arialNormalBorderLeft), 
								array("data"=>$total_recs,"format"=>$arialNormalBorderNumRight), 
								array("data"=>$precentage,"format"=>$arialNormalBorderNumRight)				
													
								); 
				
				$this->_writeRow($sp, $values, $row, $columnHeader);  				
				$row++;
			}
		}//BC end
	}
	
	
	function _writeData(&$workbook, & $sp, & $row)
    {
       	$fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'border'=>1));
		$arialNormalBorder = $fm;
		
		
		$fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'border'=>1,'color'=>'blue'));
		$arialNormalBorderRed = $fm;
		
		$fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'border'=>1,'color'=>'red'));
		$arialNormalBorderBlue = $fm;
		
		$fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'align'=>'right','border'=>1));
		$arialNormalBorderRight = $fm;
		
		$fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'align'=>'right','border'=>1,'numformat'=>'0'));
		$arialNormalBorderNumRight = $fm;
		
		$fm = & $workbook->addFormat(array('fontfamily'=>'Arial','size'=>9, 'align'=>'right','border'=>1));
        $fm ->setNumFormat("$#,##0.00;[Red]-$#,##0.00");
        $arialNormalCurrency = $fm;
        
        
        $fm = & $workbook->addFormat(array('fontfamily'=>'Arial','size'=>9, 'align'=>'right','border'=>1,'color'=>'blue'));
        $fm ->setNumFormat("$#,##0.00;[blue]-$#,##0.00");
        $arialNormalCurrencyRed = $fm;
		
		$i=1;
		$startRow= $row + 1; //internally rows are 0 based, in formulas rows are 1 based
		$nIndex=0;
		$totalsales=0;
		
		$salesAnaylsisiData = new salesAnalysisData();
		//$user_id,$province_id,$sale_month,$sale_year
		$basicInfos = $salesAnaylsisiData->getMonthlySaleInfo($this->user_id,$this->report_month,$this->report_year);
	//	print_r($report_data);
	
	
		 $i=0;
		 
		 $lastID="";
		 $currentId="";
		 
		 $cellFormat = $arialNormalBorder;
		 $cellCurrencyFormat = $arialNormalCurrency;
		 
		foreach($basicInfos as $info_date)
	    {
	//sa.customer_name,lktype.caption store_type, sa.billing_address_city city,sa.billing_address_street address,sa.licensee_number,lkp.product_name,
			  										
	//sd.total_cases, sd.total_case_value, sd.total_whole_sale,total_retail,sd.total_profit
			$customer_id =$info_date["customer_id"];
			$currentId= $info_date["monthly_sales_basic_id"];
			$customer = $info_date["customer_name"];
			$storetype = $info_date["store_type"];
			$city = $info_date["city"];
			$address = $info_date["address"];
			$storeno = $info_date["licensee_number"];
			$product = $info_date["product_name"];
			$totalcases = $info_date["total_cases"];
			$totalvalues = $info_date["total_case_value"];
			$totalwhosale = $info_date["total_whole_sale"];
			$totalretail = $info_date["total_retail"];
			$totalprofit = $info_date["total_profit"];
			$rank = $info_date["rank"];
			
			
		
		
			if(in_array( $customer_id,$this->lastMthCMs))
			{
			 	if(count($this->lastMthCMs)<100)
			 	{
		 		if(floatval($totalcases)<0)
					{
					 //	$this->debugText($totalcases);
						$cellFormat = $arialNormalBorderBlue;
					}
					else
					{
						$cellFormat = $arialNormalBorderRed;
			 			$cellCurrencyFormat = $arialNormalCurrencyRed;
					}
				
			 	}
			}
			else
			{
				 $cellFormat = $arialNormalBorder;
				 
				 	if(floatval($totalcases)<0)
					{
					 //	$this->debugText($totalcases);
						$cellFormat = $arialNormalBorderBlue;
						$cellCurrencyFormat = $arialNormalCurrencyRed;
					}
					else
					{
						$cellFormat =$arialNormalBorder;
					}
			
		 		 $cellCurrencyFormat = $arialNormalCurrency;
			}
		
		
		
	
			if($i==0)
				$lastId = $currentId;
			else
			{
				if($currentId == $lastId)
				{
				 	$customer="";
				 	$storetype="";
				 	$city="";
				 	$address="";
				 	$storeno="";
				 	$rank ="";
				 	
					
				}
				$lastId = $currentId;
			}
				
		//	$value=round(($case_value/$case_sold),2);
		
		if($this->province_id ==1)
		{
			$values = array(
							array("data"=>$customer), 
							array("data"=>$storetype), 
							array("data"=>$city), 
							array("data"=>$address), 
							array("data"=>$storeno), 
							array("data"=>$product), 
							array("data"=>$totalcases), 
							array("data"=>$totalvalues), 
							array("data"=>$totalwhosale,"format"=>$cellCurrencyFormat),
							array("data"=>$totalretail,"format"=>$cellCurrencyFormat),
							array("data"=>$totalprofit,"format"=>$cellCurrencyFormat)
						
												
							); 
		}
		else
		{
				$values = array(
							array("data"=>$customer), 
							array("data"=>$rank), 
							array("data"=>$city), 
							array("data"=>$address), 
							array("data"=>$storeno), 
							array("data"=>$product), 
							array("data"=>$totalcases), 
							array("data"=>$totalvalues), 
							array("data"=>$totalwhosale,"format"=>$cellCurrencyFormat),
						
							array("data"=>$totalprofit,"format"=>$cellCurrencyFormat)
						
												
							); 
							
				$this->nEndCol="J";
		}								             
        
			$this->_writeRow($sp, $values, $row, $cellFormat); 
           
            $row++;
            $i++;
            
        }
        $endRow=$row;
	                
        

    }
    
   
    
   
    
    function _writeTitle(& $workbook, & $sp, & $row)
    {
        $fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>12, 'bold'=>1, 
                'fgcolor'=>'silver', 'bgcolor'=>'black', 'align'=>'center', 'valign'=>'center'));
        $reportTitle = $fm;

        $fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>11, 'bold'=>1));
        $arialBoldUnderlined  = $fm;
        
        $fm = & $workbook->addFormat(array('right'=>2));
        $thickRight = $fm;
        
    	 
        $sp->mergeCells($row, $this->columns["A"]["index"], $row, $this->columns["K"]["index"]);
        $sp->insertBitmap($row, $this->columns["D"]["index"], "resources/images/cswslogo.bmp", 33, -8, 1,1);

$row+=5;

//row 4
        $this->_writeCell($sp, array("data"=>$this->titleText), $row, "A", $reportTitle); 
        $sp->mergeCells($row, $this->columns["A"]["index"], $row, $this->columns["K"]["index"]);
        $row++;
        
     
    }
    function _writeColumnHeaders(& $workbook, & $sp, & $row)
    {
        $fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'fgcolor'=>'silver', 
                'bgcolor'=>'black', 'border'=>1, 'align'=>'center', 'valign'=>'bottom','bold'=>1,
                'top'=>1, 'bottom'=>1));
        //$fm->setTextWrap();
        $columnHeader = $fm;
        
      
        
        $fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'fgcolor'=>'silver', 
                'bgcolor'=>'black', 'border'=>1, 'align'=>'left', 'valign'=>'bottom','bold'=>1,
                'top'=>1, 'bottom'=>1));       
        $columnHeaderLeft = $fm;
        
        
        
        $fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'fgcolor'=>'silver', 
                'bgcolor'=>'black', 'border'=>1, 'align'=>'right', 'valign'=>'bottom','bold'=>1,
                'top'=>1, 'bottom'=>1));       
        $columnHeaderRight = $fm;
        
        $sp->setRow($row, 16); 
        
       

		if($this->province_id ==1)
		{
			$values = array(
							array("data"=>"Customer", "format"=>$columnHeaderLeft), 
							array("data"=>"Store type", "format"=>$columnHeaderLeft),
							array("data"=>"City", "format"=>$columnHeaderLeft), 
							array("data"=>"Address", "format"=>$columnHeaderLeft), 
							array("data"=>"Store number", "format"=>$columnHeaderRight),
							array("data"=>"Product", "format"=>$columnHeaderLeft),
							array("data"=>"Total cs", "format"=>$columnHeaderRight),
							array("data"=>"Case value", "format"=>$columnHeaderRight),
							array("data"=>"Wholesale", "format"=>$columnHeaderRight),
							array("data"=>"Retail", "format"=>$columnHeaderRight),
								array("data"=>"Profit", "format"=>$columnHeaderRight)
	
							);	
		}
		else
		{
				$values = array(
							array("data"=>"Customer", "format"=>$columnHeaderLeft), 
							array("data"=>"Rank", "format"=>$columnHeaderLeft), 
							array("data"=>"City", "format"=>$columnHeaderLeft), 
							array("data"=>"Address", "format"=>$columnHeaderLeft), 
							array("data"=>"Store number", "format"=>$columnHeaderRight),
							array("data"=>"Product", "format"=>$columnHeaderLeft),
							array("data"=>"Total cs", "format"=>$columnHeaderRight),
							array("data"=>"Case value", "format"=>$columnHeaderRight),
							array("data"=>"Wholesale", "format"=>$columnHeaderRight),
						
							array("data"=>"Profit", "format"=>$columnHeaderRight)
	
							);	
				$this->nEndCol ="J";
		}
                        
       
        $this->_writeRow($sp, $values, $row, $columnHeader);  
        $row++;
        
    }
    
    
    
    function _writeCell(& $sp, $value, $row, $col, $format=null)
    {
        return $sp->write($row, $this->columns[$col]["index"], $value["data"], array_key_exists("format",$value)?$value["format"]:$format);
    }
    
    function _writeRow(& $sp, $value, & $row, $format=null)
    {
        for ($i = $this->nStartCol, $j=0; $i!=$this->nEndCol; $i++, $j++) 
        {         
            $this->_writeCell(& $sp, $value[$j], $row, $i, $format);
        }
    }

}
?>