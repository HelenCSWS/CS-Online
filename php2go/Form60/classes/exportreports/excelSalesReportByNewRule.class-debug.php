<?php

import('Form60.bll.bllSSDSData');
import('Form60.util.excel.writer');
import('php2go.util.Number');


class excelSalesReportByNewRule
{
    var $report_month;
    var $report_year;
    var $SPData;
    var $reportData;
    var $columns;
    
    var $estate_id;
    var $titleText="";
    var $estate_name="";
    
    var $report_type=1;  //
    var $user_id;
    var $storeType="";
    var $commissionTypeID="";
    
    var $targetInfo;
    
    var $province_id=1;
    
    function excelSalesReportByNewRule()
    {       
        $this->columns = array("A"=>array("index"=>0, "width"=>20.14), "B"=>array("index"=>1, "width"=>33.71), 
                "C"=>array("index"=>2, "width"=>13), "D"=>array("index"=>3, "width"=>13), "E"=>array("index"=>4, "width"=>13),
                "F"=>array("index"=>5, "width"=>13), "G"=>array("index"=>6, "width"=>13), "H"=>array("index"=>7, "width"=>13),
                "I"=>array("index"=>8, "width"=>13),"J"=>array("index"=>9, "width"=>13),"K"=>array("index"=>10, "width"=>13),
				"L"=>array("index"=>11, "width"=>13));

        $this->sale_month =$_REQUEST["sale_month"];
        
        $this->sale_year = $_REQUEST["sale_year"];
        $this->user_id = $_REQUEST["user_id"];
       
        $this->commissionTypeID = $_REQUEST["commission_type_id"];
        
      	$this->province_id =$_REQUEST["province_id"];

  
        $this->generateSpreadsheet();
        
        F60DocBase::display();        
    }
        
    function generateSpreadsheet($returnFile=false)
    {
  		
  	 	if ($this->commissionTypeID == 1)
            $this->storeType = "All Stores";
        else if($this->commissionTypeID == 2)
        {
           $this->storeType = "BCLDB";
        }
        else if($this->commissionTypeID == 3)
        {
           $this->storeType = "BCLDB and All Stores";
        }
        else if($this->commissionTypeID == 4)
        {
           $this->storeType = "Alberta Stores";
        }
        
        $fileName ="All consultants - $this->storeType - " . F60Date::getMonthTxt($this->sale_month) . "-" . $this->sale_year;
        if($this->commissionTypeID == 2)
        {
            $fileName ="All sales through $this->storeType stores for " . F60Date::getMonthTxt($this->sale_month) . "-" . $this->sale_year;
        }
        
        
        $fileName = $fileName. ".xls";
        $filePath = ROOT_PATH . "salesreports/" . $fileName;
        
        if ($returnFile)
    	{
            $workbook = new Spreadsheet_Excel_Writer($filePath);
           
        }
        else
        {
           $workbook = new Spreadsheet_Excel_Writer();
           $workbook->send($fileName);
        }
        $workbook->setVersion(8);
        
        $SSDSData = new SSDSData();
        
   		$users = $SSDSData->getUsersByCommissionType($this->sale_month,$this->sale_year,$this->commissionTypeID);     

   	
      
   	//	for ($i=0;$i<=1; $i++)//// debug
	  //	for ($i=0;$i<count($users); $i++)
	//	{			
	
		 
	//	 echo $b;
	$i=1;
		 
			$user_name =$users[$i]['user_name'];
		
			
			$c_user_id = $users[$i]["user_id"];
			
		$c_user_id =149;
	
			$reportData = $SSDSData->GetSalesReport($this->sale_month, $this->sale_year, $c_user_id, -1, 2);		

			if(!$reportData)
				continue;
			
				$worksheetName = $user_name;  
			
        	$sp =& $workbook->addWorksheet($worksheetName); 
			
			$c_uer_id ="";			
			
			$this->targetInfo = $SSDSData->getTargetCommissionLeveInfo($this->sale_month,$this->sale_year,$c_user_id);
		  
       		//set column widths
	        foreach($this->columns as $column)
	        {
	         
			   $sp->setColumn($column["index"], $column["index"], $column["width"]);
	        
	        }
        	$row = 0;
        
        	$titleText = $user_name. ", " . F60Date::getMonthTxt($this->sale_month) . "-" . $this->sale_year . " ";
          	$titleText .= " Sales Summary Report ($this->storeType)";
          	
          	
          	//title
        	$this->_writeTitle($workbook, $sp, $row,$titleText); //$row++
        
        	$row++; // blank row
        
			$salesDetails = $reportData["sales_details"];
			
		
       	//	$this->_writeSalesData($workbook, $sp, $row,$salesDetails);
        
			
       		$this->_writeCommissionData($workbook, $sp, $row,$reportData);
			
	//	}
			//not assgined user
		
	        
	       
		    /*  for($i=1; $i<=5; $i++)
		        {
					$reportData = $SSDSData->GetSpecialSalesReport($this->sale_month, $this->sale_year, $i,$this->province_id, 2);
				
					if(count($reportData["summary_details"])!=0)
					{
					 	 
						if($i==1 )
						{
							if($this->commissionTypeID!=2 and $this->commissionTypeID!=3)
							{
								$this->generateSpecialSheet($i, $sp, & $workbook,$reportData );
							}
							
							if($this->province_id ==1)
								break;
								
						}
						else
						{
							 
							if($this->province_id ==2)
							{
						        //Sample
						    	$this->generateSpecialSheet($i, $sp, & $workbook,$reportData );    
							
						    }
						}
					}
				}*/
	
      
       
	   $workbook->close();
         
        if ($returnFile)
            return $filePath;
        
    }
    
    function generateSpecialSheet($sheetType, $sp, & $workbook, $reportData )
    {
     
     		if($sheetType==1)
				$worksheetName = "Not Assigned";
			if($sheetType==2)
				$worksheetName = "Samples";
			if($sheetType==3)
				$worksheetName = "NWT Liquor Commission";
			if($sheetType==4)
				$worksheetName = "Saskatchewan Liquor Board";
			if($sheetType==5)
				$worksheetName = "Yukon Liquor Corp";
				
			$sp =& $workbook->addWorksheet($worksheetName);
        	
 	        
	
			//set column widths
	        foreach($this->columns as $column)
	        {
	            $sp->setColumn($column["index"], $column["index"], $column["width"]);
	        }
	        
	        $row = 1;
	        
	       	$titleText = "$worksheetName". ", " . F60Date::getMonthTxt($this->sale_month) . "-" . $this->sale_year . " ";
          	$titleText .= " Sales Summary Report";
          	
          	
          	//title
        	$this->_writeTitle($workbook, $sp, $row,$titleText); //$row++
	        
	        
	        $this->_writeSalesData($workbook, $sp, $row,$reportData["sales_details"]);
	        
	        $row++;
	        
			$this->_writeCommissionColumnHeaders(& $workbook, & $sp, & $row, 4);
			
			//total
	      
          
        $fm = & $workbook->addFormat(array('fontfamily'=>'Verdana', 'size'=>9,'align'=>'left','bold'=>1 ));
        $arialNormalBoldLeft = $fm;
    
	    $fm = & $workbook->addFormat(array('fontfamily'=>'Verdana','size'=>9, 'align'=>'right','border'=>0));
        $fm ->setNumFormat("$#,##0.00;[Red]-$#,##0.00");
        $arialNormalCurrency = $fm;
         
     	$fm = & $workbook->addFormat(array('fontfamily'=>'Verdana', 'size'=>9, 'align'=>'right','border'=>0,'numformat'=>'0'));
     //	$fm ->setNumFormat("#,##0.00");
        $arialNormalNumber = $fm;
        
       if(count($reportData["summary_details"])!=0)			
		{
			$summary_details=$reportData["summary_details"][0];
			
			$summary_real_cases = $reportData["summary_real_cases"][0];
	
		
			$totalIntProfit = $summary_details["total_profit"];
			$totalIntCases = $summary_details["total_cases"];
			$rowWH = $summary_details["total_wholesale"];
			$rowRT = $summary_details["total_retail"];
			
			$real_totalIntCases = $summary_details['total_international_real_cases'];
	
			$values = array(array("data"=>"Total Inter profit:","format"=>$arialNormalBoldLeft),
							array("data"=>$totalIntProfit,"format"=>$arialNormalCurrency), 					
							array("data"=>"Total Inter sold:","format"=>$arialNormalBoldLeft), 
							array("data"=>$totalIntCases,"format"=>$arialNormalNumber), 
							array("data"=>$real_totalIntCases,"format"=>$arialNormalNumber), 
							array("data"=>$rowWH,"format"=>$arialNormalCurrency), 
							array("data"=>$rowRT,"format"=>$arialNormalCurrency)						
						       	);
			
			$this->_writeRow($sp, $values, $row, null,"F", "M");
			$row++;	    
		}
	}
	
    function currencyNumber($price)
    {
		return Number::fromDecimalToCurrency($price,"$", ".", ",", 2, "left");
	}
    
      		  
    function _writeSalesData(&$workbook, & $sp, & $row, $salesDetails)
    {
     //  $picEmpty = $sp->addPictureString("0");
       // $picNumber = $sp->addPictureString("#,##0.00");
       // $picCurrency = "$#,##0.00;[Red]-$#,##0.00";
        
       // $currency_format->setNumFormat($picCurrency);
       // $picPercent = $sp->addPictureString("0%");
        
        
        $fm = & $workbook->addFormat(array('fontfamily'=>'Verdana', 'size'=>10, 'border'=>1));
        $arialNormalBorder = $fm;
      
            
       	$fm = & $workbook->addFormat(array('fontfamily'=>'Verdana', 'size'=>9, 'align'=>'right','top'=>1, 'numformat'=>'0','bold'=>1,color=>'red'));
        $arialBoldNumberBorderTop = $fm;
   
      	$fm = & $workbook->addFormat(array('fontfamily'=>'Verdana','size'=>9, 'align'=>'right','top'=>1,'bold'=>1, color=>'red'));
        $fm ->setNumFormat("$#,##0.00;[Red]-$#,##0.00");
        $arialBoldCurrencyTop = $fm;
        
        $fm = & $workbook->addFormat(array('fontfamily'=>'Verdana', 'size'=>9, 'align'=>'right','border'=>0));
        $arialNormalRight = $fm;
        
        $fm = & $workbook->addFormat(array('fontfamily'=>'Verdana','size'=>9, 'align'=>'left','border'=>0));
        $arialNormalLeft = $fm;
        
        $fm = & $workbook->addFormat(array('fontfamily'=>'Verdana','size'=>9, 'align'=>'right','border'=>0));
        $fm ->setNumFormat("$#,##0.00;[Red]-$#,##0.00");
        $arialNormalCurrency = $fm;
        
        $fm = & $workbook->addFormat(array('fontfamily'=>'Verdana','size'=>9, 'align'=>'right','border'=>0,'bold'=>1));
        $fm ->setNumFormat("$#,##0.00;[Red]-$#,##0.00");
        $arialBoldCurrency = $fm;
      
     	$fm = & $workbook->addFormat(array('fontfamily'=>'Verdana', 'size'=>9, 'align'=>'right','border'=>0,'numformat'=>'0'));
     //	$fm ->setNumFormat("#,##0.00");
        $arialNormalNumber = $fm;
        
   		$startRow= $row + 1; //internally rows are 0 based, in formulas rows are 1 based
		
	
        
		// print $startRow;
		$nIndex=0;
		
		$customer_id = 0;
        $firstRow = true;
        $totalBottles = 0;
        $totalProfit = 0;
        $totlaCases = 0;
        $totlaRLCases = 0;
        $totlaSales = 0;
        $totalRTSales = 0;
        
        $emptyCell = array("data"=>"");
        $emptyCellTopLine = array("data"=>"","format"=>$arialBoldNumberBorderTop);
		
		$i=0;
		$salesDetails->moveFirst();            
		
		$sameCM=false;
		
		
							
        while ($sale = $salesDetails->fetch())    
		{
	
                if ($customer_id != $sale["customer_id"])
                {
                 
                 	$sameCM =false;
                    $customer_id = $sale["customer_id"];
                    
                    //if not first customer print totals
                    
                    if (!$firstRow && !$sameCM)
                    {
                    
                        $totalValues = array($emptyCellTopLine, $emptyCellTopLine,
						 $emptyCellTopLine, $emptyCellTopLine, 
                        array("data"=>"Total:","format"=>$arialBoldNumberBorderTop), 
						array("data"=>$totalBottles,"format"=>$arialBoldNumberBorderTop), $emptyCellTopLine, 
                        array("data"=>$totalProfit,"format"=>$arialBoldCurrencyTop), 
                        array("data"=>$totlaCases,"format"=>$arialBoldNumberBorderTop),
                        array("data"=>$totlaRLCases,"format"=>$arialBoldNumberBorderTop),
                        array("data"=>$totlaSales,"format"=>$arialBoldCurrencyTop),
						array("data"=>$totalRTSales, "format"=>$arialBoldCurrencyTop)); 
								
								
                        $this->_writeRow(& $sp, $totalValues, & $row, $format=null);
                        
                        $row++;
					
                       // $firstRow = true;
                        $totalBottles = 0;
                        $totalProfit = 0;
                        $totlaCases = 0;
                        $totlaRLCases = 0;
                        $totlaSales = 0;
                        $totalRTSales = 0;
                        //$firstRow =true;
                        $sameCM =true;
                    }
                    
                    $row++;
                    $address =F60Date::ucwords1($sale["address"]);
                    if(substr($address,0,1)=="-")
					{
						$address=substr_replace($address,'',0,2);
					}
                    $this->_writeColumnHeaders($workbook, $sp, $row,1);
                   
                 	$values = array(array("data"=>$sale["licensee_no"],"format"=>$arialNormalLeft ),
									array("data"=>F60Date::ucwords1($sale["customer_name"]),"format"=>$arialNormalLeft ),
									
									array("data"=>$sale["license_name"],"format"=>$arialNormalLeft ),
									array("data"=>F60Date::ucwords1($sale["city"]),"format"=>$arialNormalLeft ),
									array("data"=>$address,"format"=>$arialNormalLeft ),
									$emptyCell, $emptyCell, $emptyCell, $emptyCell,$emptyCell,$emptyCell,$emptyCell
								
									); 
								
								
                    $this->_writeRow($sp, $values, $row, null); 
                   
                    //write wine header
					 $row++;
                    
                    $this->_writeColumnHeaders($workbook, $sp, $row,2);
                    
                    $firstRow = false;
                }
                
                $totalBottles += $sale["bottles_sold"];
                $totalProfit += $sale["total_profit"];
                $totlaCases += $sale["cases_sold"];
                $totlaRLCases += $sale["cases"];
                $totlaSales += $sale["total_sales"];
                $totalRTSales += $sale["rt_sales"];
                
                $values = array(array("data"=>$sale["SKUA"],"format"=>$arialNormalLeft ),
									array("data"=>F60Date::ucwords1($sale["wine_name"]),"format"=>$arialNormalLeft ),
									array("data"=>$sale["liters"],"format"=>$arialNormalLeft ),
									array("data"=>$sale["type"],"format"=>$arialNormalLeft ),
									array("data"=>F60Date::ucwords1($sale["country"]),"format"=>$arialNormalLeft ),
									array("data"=>$sale["bottles_sold"],"format"=>$arialNormalNumber ),
									array("data"=>$sale["profit_per_bottle"],"format"=>$arialNormalCurrency ),
									array("data"=>$sale["total_profit"],"format"=>$arialNormalCurrency ),
									array("data"=>$sale["cases_sold"],"format"=>$arialNormalNumber ),
									array("data"=>$sale["cases"],"format"=>$arialNormalNumber ),
									array("data"=>$sale["total_sales"],"format"=>$arialNormalCurrency ),
									array("data"=>$sale["rt_sales"],"format"=>$arialNormalCurrency )
								); 

          	$this->_writeRow($sp, $values, $row, $arialNormalBorder); 
            //print total for last one
           
            $row++;

		}
	
		if (!$firstRow)
        {                         	
               $totalValues = array($emptyCellTopLine, $emptyCellTopLine, $emptyCellTopLine,$emptyCellTopLine,
                        array("data"=>"Total:","format"=>$arialBoldNumberBorderTop), 
						array("data"=>$totalBottles,"format"=>$arialBoldNumberBorderTop), $emptyCellTopLine,
                        array("data"=>$totalProfit,"format"=>$arialBoldCurrencyTop), 
                        array("data"=>$totlaCases,"format"=>$arialBoldNumberBorderTop),
                        array("data"=>$totlaRLCases,"format"=>$arialBoldNumberBorderTop),
                        array("data"=>$totlaSales,"format"=>$arialBoldCurrencyTop),
						array("data"=>$totalRTSales, "format"=>$arialBoldCurrencyTop)); 
                   
                $this->_writeRow($sp, $totalValues, $row, null); 
            
         }
	
		$row++;
    }
    function _writeCommissionData(&$workbook, & $sp, & $row, $reportData)
    {
        
        $fm = & $workbook->addFormat(array('fontfamily'=>'Verdana', 'size'=>9, 'bold'=>1));
        $arialNormalBorder = $fm;
        
        $fm = & $workbook->addFormat(array('fontfamily'=>'Verdana', 'size'=>9, 'border'=>1));
        $arialNormalBold = $fm;
        
        $fm = & $workbook->addFormat(array('fontfamily'=>'Verdana', 'size'=>9,'align'=>'left','bold'=>1 ));
        $arialNormalBoldLeft = $fm;
                  
       	$fm = & $workbook->addFormat(array('fontfamily'=>'Verdana', 'size'=>9, 'align'=>'right','top'=>1, 'numformat'=>'0','bold'=>1));
        $arialBoldNumberBorderTop = $fm;
        
        $fm = & $workbook->addFormat(array('fontfamily'=>'Verdana', 'size'=>9, 'align'=>'left','top'=>1, 'numformat'=>'0','bold'=>1));
        $arialBoldNumberBorderTopLeft = $fm;
        
        $fm = & $workbook->addFormat(array('fontfamily'=>'Verdana', 'size'=>9, 'align'=>'right','border'=>0));
        $arialNormalRight = $fm;
        
        $fm = & $workbook->addFormat(array('fontfamily'=>'Verdana','size'=>9, 'align'=>'left','border'=>0));
        $arialNormalLeft = $fm;
        
        $fm = & $workbook->addFormat(array('fontfamily'=>'Verdana','size'=>9, 'align'=>'right','border'=>0));
        $fm ->setNumFormat("$#,##0.00;[Red]-$#,##0.00");
        $arialNormalCurrency = $fm;
        
        $fm = & $workbook->addFormat(array('fontfamily'=>'Verdana','size'=>9, 'align'=>'right','top'=>1,'bold'=>1));
        $fm ->setNumFormat("$#,##0.00;[Red]-$#,##0.00");
        $arialNormalCurrencyTopBold = $fm;
      
     	$fm = & $workbook->addFormat(array('fontfamily'=>'Verdana', 'size'=>9, 'align'=>'right','border'=>0,'numformat'=>'0'));
     //	$fm ->setNumFormat("#,##0.00");
        $arialNormalNumber = $fm;
        
        $fm = & $workbook->addFormat(array('fontfamily'=>'Verdana','size'=>9, 'align'=>'right','top'=>1,'bold'=>1));
        $arialNormalTopBorderBold = $fm;
        
        $partternNumber =17;
     	$bgColor="white";
     	$fgColor="38";
        $fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'fgcolor'=>$fgColor, 'pattern'=>$partternNumber,
                'bgcolor'=>$bgColor, 'color'=>'black' ,'border'=>0, 'align'=>'left', 'valign'=>'bottom','bold'=>1,
                'top'=>0, 'bottom'=>0));
        //$fm->setTextWrap();
        $columnHeaderLeft = $fm;
              
	
	  	$emptyCell = array("data"=>"");
      
	    $row++;
        $this->_writeCommissionColumnHeaders($workbook, $sp, $row,$this->commissionTypeID);
      
        
        
        $startRow= $row; //internally rows are 0 based, in formulas rows are 1 based
		
		// print $startRow;
		$nIndex=0;
		
		
		$commissions = $reportData['commission_details'];	
		

		$summarys = $reportData["summary_details"];;
		$summary_real_cases = $reportData["summary_real_cases"];
		/*Array ( [0] => Array ( [user_id] => 6 [level_id] => 0 [caption] => Level 0 [min_cases] => 0.00 [max_cases] => 0.00 [commission_rate] => 0.00 [total_cases] => 0.00 [commission_amount] => 0.0000 [bonus] => 0.0 ) [1] => Array ( [user_id] => 6 [level_id] => 1 [caption] => Level 1 [min_cases] => 181.00 [max_cases] => 205.00 [commission_rate] => 15.00 [total_cases] => 0.00 [commission_amount] => 0.0000 [bonus] => 0.0 ) [2] => Array ( [user_id] => 6 [level_id] => 2 [caption] => Level 2 [min_cases] => 206.00 [max_cases] => 225.00 [commission_rate] => 20.00 [total_cases] => 0.00 [commission_amount] => 0.0000 [bonus] => 0.0 ) [3] => Array ( [user_id] => 6 [level_id] => 3 [caption] => Level 3 [min_cases] => 226.00 [max_cases] => 1000.00 [commission_rate] => 25.00 [total_cases] => 327.52 [commission_amount] => 800.7864 [bonus] => 0.0 ) )
		
		
		 Array ( [0] => Array ( [user_id] => 6 [total_cases] => 532.52 [total_units] => 6677 [total_canadian_profit] => 1172.76 [total_international_profit] => 4034.84 [total_profit] => 5207.60 [avg_profit_per_case] => 9.78 [total_canadian_cases] => 154.50 [total_international_cases] => 378.02 [total_sales] => 41950.63 [total_retail] => 113318.65 ) )	
*/
	
			/*
			Array ( [0] => Array ( [user_id] => 44 [level_number] => 1 [caption] => Level 1 [target_price] => 50000.00 [total_sales] => 59964.33 [commission_amount] => [bonus] => 250.00 ) [1] => Array ( [user_id] => 44 [level_number] => 2 [caption] => Level 2 [target_price] => 60000.00 [total_sales] => 0 [commission_amount] => [bonus] => 0 ) [2] => Array ( [user_id] => 44 [level_number] => 3 [caption] => Level 3 [target_price] => 70000.00 [total_sales] => 0 [commission_amount] => [bonus] => 0 ) [3] => Array ( [user_id] => 44 [level_number] => 4 [caption] => Level 4 [target_price] => 80000.00 [total_sales] => 0 [commission_amount] => [bonus] => 0 ) [4] => Array ( [user_id] => 44 [level_number] => 5 [caption] => Level 5 [target_price] => 90000.00 [total_sales] => 0 [commission_amount] => [bonus] => 0 ) )			

    [0] => Array		
        (		
            [user_id] => 40		
            [level_number] => 1		
            [caption] => Level 1		
            [target_price] => 500.00		
            [total_cases] => 526.83		
            [level_end_cases] => 500.00		
            [commission_amount] => 		
            [bonus] => 500.00		
        )		

			*/
		
			//Commission 
			$totalBonus=0;
			$totalCases=0;
			$bonus=0;
			
  		//Commissions
			foreach($commissions as $commission)
	        {	
		         if($this->commissionTypeID>1)
		         {
		         	$bonus =$commission["bonus"];
		         	
		         	if($this->commissionTypeID==4)
		         	{
						$values = array(array("data"=>$commission["target_price"],"format"=>$arialNormalCurrency), 
										array("data"=>$commission["total_cases"],"format"=>$arialNormalNumber), 
										$emptyCell, 
										array("data"=>$bonus,"format"=>$arialNormalCurrency), 
										$emptyCell);
					}
					else
					{
			        	$values = array(array("data"=>$commission["target_price"],"format"=>$arialNormalCurrency), 
										array("data"=>$commission["total_sales"],"format"=>$arialNormalCurrency), 
										$emptyCell, 
										array("data"=>$bonus,"format"=>$arialNormalCurrency), 
										$emptyCell);
					}
					   	
					$this->_writeRow($sp, $values, $row, null,"A", "F");
					$row++;
				}
				else if($this->commissionTypeID==1 && $nIndex>0)
				{
				 /*	/*Array ( [0] => Array ( [user_id] => 6 [level_id] => 0 [caption] => Level 0 [min_cases] => 0.00 [max_cases] => 0.00 [commission_rate] => 0.00 [total_cases] => 0.00 [commission_amount] => 0.0000 [bonus] => 0.0 ) 
				 [1] => Array ( [user_id] => 6 [level_id] => 1 [caption] => Level 1 [min_cases] => 181.00 [max_cases] => 205.00 [commission_rate] => 15.00 [total_cases] => 0.00 [commission_amount] => 0.0000 [bonus] => 0.0 ) 
				 [2] => Array ( [user_id] => 6 [level_id] => 2 [caption] => Level 2 [min_cases] => 206.00 [max_cases] => 225.00 [commission_rate] => 20.00 [total_cases] => 0.00 [commission_amount] => 0.0000 [bonus] => 0.0 ) 
				 [3] => Array ( [user_id] => 6 [level_id] => 3 [caption] => Level 3 [min_cases] => 226.00 [max_cases] => 1000.00 [commission_rate] => 25.00 [total_cases] => 327.52 [commission_amount] => 800.7864 [bonus] => 0.0 ) )
		*/

				 	$bonus =$commission["commission_amount"];
				 	$levelInfo = $commission["caption"].": ".$commission["min_cases"];
				 	$level_cases=$commission["total_cases"];
				 	$commission_rate = $commission["commission_rate"]."%";
		         	
		        	$values = array(array("data"=>$levelInfo,"format"=>$arialNormalLeft), 
									array("data"=>$level_cases,"format"=>$arialNormalNumber), 
									array("data"=>$commission_rate,"format"=>$arialNormalRight), 
									array("data"=>$bonus,"format"=>$arialNormalCurrency), 
									$emptyCell);
							       	
					   	
					$this->_writeRow($sp, $values, $row, null,"A", "F");
					$row++;
					
					$totalCases=$totalCases+$commission["total_cases"];
				}
				$nIndex++;
				$totalBonus=$totalBonus+$bonus;
			}
			
			$values = array($emptyCell,
							$emptyCell, 
					
							array("data"=>"Sub Total"), 
							array("data"=>$totalBonus,"format"=>$arialNormalCurrencyTopBold), 
							array("data"=>"","format"=>$arialNormalRight)						
						       	);
						       	
			$this->_writeRow($sp, $values, $row, $arialNormalTopBorderBold,"A", "F");
			
			
			/* type 2 :   Array ( [0] => Array ( [user_id] => 44 [total_cases] => 935.37 [total_units] => 11284 [total_canadian_profit] => 0.00 [total_international_profit] => 9684.00 [total_profit] => 9684.00 [avg_profit_per_case] => 10.35 [total_canadian_cases] => 0.00 [total_international_cases] => 935.37 [total_sales] => 59964.33 [total_retail] => 167277.36 ) )	
*/

/*type 3:    Array ( [0] => Array ( [user_id] => 43 [total_cases] => 383.54 [total_units] => 4627 [total_canadian_profit] => 0.00 [total_international_profit] => 3878.20 [total_profit] => 3878.20 [avg_profit_per_case] => 10.11 [total_canadian_cases] => 0.00 [total_international_cases] => 383.54 [total_sales] => 23777.56 [total_retail] => 66936.73 ) 
[1] => Array ( [user_id] => 43 [total_cases] => 331.09 [total_units] => 3746 [total_canadian_profit] => 764.28 [total_international_profit] => 2598.20 [total_profit] => 3362.48 [avg_profit_per_case] => 10.16 [total_canadian_cases] => 67.33 [total_international_cases] => 263.76 [total_sales] => 22900.77 [total_retail] => 61151.46 ) )	
*/

	/*type1:   Array ( [0] => Array ( [user_id] => 6 [total_cases] => 532.52 [total_units] => 6677 [total_canadian_profit] => 1172.76 [total_international_profit] => 4034.84 [total_profit] => 5207.60 [avg_profit_per_case] => 9.78 [total_canadian_cases] => 154.50 [total_international_cases] => 378.02 [total_sales] => 41950.63 [total_retail] => 113318.65 ) )	
*/			
			//Summary
			$row=$startRow;
			
			$totalIntProfit = $summarys[0]['total_international_profit'];
			
			$totalIntCases = $summarys[0]['total_international_cases'];
			$real_totalIntCases = $summary_real_cases[0]['total_international_real_cases'];
			
			//		$summary_real_cases = $reportData["summary_real_cases"];

	
			
			$totalWH = $summarys[0]['total_sales'];
			$totalRT = $summarys[0]['total_retail'];
			$avgProfit = $summarys[0]['avg_profit_per_case'];
			$totalCACases =$summarys[0]['total_canadian_cases'];
				
			$totalCAProfit=$summarys[0]['total_canadian_profit'];
			$totalProfit =0;
			$totalCases =0;
			$totalRealCases=0;
			$rowWH="";
			$rowRT="";
	
			
	
			
			//Summary information
			
			if($this->commissionTypeID==4||$this->commissionTypeID==2||$this->commissionTypeID==1)
			{
				//$totalCases = $totalIntCases;

				
				
				
				if($this->commissionTypeID==1)
				{
					$totalWH =$summarys[0]['total_sales'];
					$totalRT =$summarys[0]['total_retail'];
				}
				else
				{
					$rowWH= $totalWH;
					$rowRT=$totalRT;
				}
			}
			else if($this->commissionTypeID==3)
			{
				$totalIntProfit=$totalIntProfit+ $summarys[1]['total_international_profit'];
				$totalIntCases=$totalIntCases+ $summarys[1]['total_international_cases'];
			//	$real_totalIntCases=$real_totalIntCases+ $summarys[1]['total_international_real_cases'];
			//	$totalCACases=$totalCACases+$summarys[0]['total_canadian_cases'];
			//	$totalCAProfit=$totalCAProfit+$summarys[0]['total_canadian_profit'];
				
				
				
				$avgProfit =($avgProfit+$summarys[1]['avg_profit_per_case'])/2;
				
				$totalCases = $totalCACases+$totalIntCases;
				
				$totalRealCases = $totalCACases+$real_totalIntCases;
				
				$totalWH =$totalWH +$summarys[1]['total_sales'];
				$totalRT =$totalRT +$summarys[1]['total_retail'];

			}
		
			if($this->commissionTypeID==1||$this->commissionTypeID==3)// BC regular
			{
					$values = array(array("data"=>"Total CND profit:","format"=>$arialNormalBoldLeft),
							array("data"=>$totalCAProfit,"format"=>$arialNormalCurrency), 					
							array("data"=>"Total CND sold:","format"=>$arialNormalBoldLeft), 
							array("data"=>$totalCACases,"format"=>$arialNormalNumber), 
							array("data"=>$totalCACases,"format"=>$arialNormalNumber),
							array("data"=>$totalWH,"format"=>$arialNormalCurrency), 							
							array("data"=>$totalRT,"format"=>$arialNormalCurrency)						
						       	);
						       	
						    
				/*	$values = array(array("data"=>"Total CND profit:","format"=>$arialNormalBoldLeft),
							array("data"=>$totalCAProfit,"format"=>$arialNormalCurrency), 					
							array("data"=>"Total CND sold:","format"=>$arialNormalBoldLeft), 
							array("data"=>$totalCACases,"format"=>$arialNormalNumber), 
							array("data"=>$totalCACases,"format"=>$arialNormalNumber),
							array("data"=>$totalWH,"format"=>$arialNormalCurrency), 
							
							array("data"=>$totalRT,"format"=>$arialNormalCurrency)						
						       	);*/
			
					$this->_writeRow($sp, $values, $row, null,"F", "M");
					$row++;
			}
			 
				
			$totalCases = $totalCACases+$totalIntCases;
			$totalRealCases = $totalCACases+$real_totalIntCases;
				
				
			
		

			$totalProfit=$totalCAProfit+$totalIntProfit;
			
			//international
			$values = array(array("data"=>"Total Inter profit:","format"=>$arialNormalBoldLeft),
							array("data"=>$totalIntProfit,"format"=>$arialNormalCurrency), 					
							array("data"=>"Total Inter sold:","format"=>$arialNormalBoldLeft), 
						
							array("data"=>$totalIntCases,"format"=>$arialNormalNumber), 
							array("data"=>$real_totalIntCases,"format"=>$arialNormalNumber), // actual cases gose here
							array("data"=>$rowWH,"format"=>$arialNormalCurrency), 
							array("data"=>$rowRT,"format"=>$arialNormalCurrency)						
						       	);
			
			$this->_writeRow($sp, $values, $row, null,"F", "M");
			$row++;
			
			if($this->commissionTypeID==2||$this->commissionTypeID==4)
			$row++;
			
			else if($this->commissionTypeID==3||$this->commissionTypeID==1)// bc regular stores 
			{
				$values = array(array("data"=>"Total profit:","format"=>$arialBoldNumberBorderTopLeft),
							array("data"=>$totalProfit,"format"=>$arialNormalCurrencyTopBold), 					
							array("data"=>"Total cs sold:","format"=>$arialBoldNumberBorderTopLeft), 
							array("data"=>$totalCases,"format"=>$arialBoldNumberBorderTop),
							array("data"=>$totalRealCases,"format"=>$arialBoldNumberBorderTop)					
						       	);	
				$this->_writeRow($sp, $values, $row, $arialNormalTopBorderBold,"F", "K");
				$row++;
				$row++;
			}
			
			$values = array(array("data"=>"Commission:","format"=>$arialNormalBoldLeft),
							array("data"=>$totalBonus,"format"=>$arialNormalCurrency), 					
							array("data"=>"Avg profit:","format"=>$arialNormalBoldLeft), 
							array("data"=>$avgProfit,"format"=>$arialNormalCurrency)
						       	);
			$this->_writeRow($sp, $values, $row, null,"F", "J");
			
			
			$row++;
			$netProfit = $totalProfit-$totalBonus;
			
			$values = array(array("data"=>"Net profit:","format"=>$arialBoldNumberBorderTopLeft),
							array("data"=>$netProfit,"format"=>$arialNormalCurrencyTopBold)
						
						       	);
			$this->_writeRow($sp, $values, $row, $arialNormalTopBorderBold,"F", "H");
			
				
			//target information
			if($this->commissionTypeID==1 || $this->commissionTypeID==3)
			{
				$row+2;
				
				
				$minInTarget= $this->targetInfo[0]["target_cases_intl"]." cases International wine";
                $minCaTarget= $this->targetInfo[0]["target_cases_ca"]." cases Canadian wine";
                //(& $sp, $value, $row, $col, $format=null)
                
              
						       	
				$this->_writeCell($sp, array("data"=>"Minimum Target"),$row, "A", $columnHeaderLeft);
				$row++;
				if($this->commissionTypeID==1 )
				{
					$this->_writeCell($sp, array("data"=>$minInTarget),$row, "A", $arialNormalLeft);
					$row++;
				}
				$this->_writeCell($sp, array("data"=>$minCaTarget),$row, "A", $arialNormalLeft);
	                

                
			}
        
	}
    function _writeSpecialCommissionData(&$workbook, & $sp, & $row, $reportData,$sheetType)
    {
        
        $fm = & $workbook->addFormat(array('fontfamily'=>'Verdana', 'size'=>9, 'bold'=>1));
        $arialNormalBorder = $fm;
        
        $fm = & $workbook->addFormat(array('fontfamily'=>'Verdana', 'size'=>9, 'border'=>1));
        $arialNormalBold = $fm;
        
        $fm = & $workbook->addFormat(array('fontfamily'=>'Verdana', 'size'=>9,'align'=>'left','bold'=>1 ));
        $arialNormalBoldLeft = $fm;
                  
       	$fm = & $workbook->addFormat(array('fontfamily'=>'Verdana', 'size'=>9, 'align'=>'right','top'=>1, 'numformat'=>'0','bold'=>1));
        $arialBoldNumberBorderTop = $fm;
        
        $fm = & $workbook->addFormat(array('fontfamily'=>'Verdana', 'size'=>9, 'align'=>'left','top'=>1, 'numformat'=>'0','bold'=>1));
        $arialBoldNumberBorderTopLeft = $fm;
        
        $fm = & $workbook->addFormat(array('fontfamily'=>'Verdana', 'size'=>9, 'align'=>'right','border'=>0));
        $arialNormalRight = $fm;
        
        $fm = & $workbook->addFormat(array('fontfamily'=>'Verdana','size'=>9, 'align'=>'left','border'=>0));
        $arialNormalLeft = $fm;
        
        $fm = & $workbook->addFormat(array('fontfamily'=>'Verdana','size'=>9, 'align'=>'right','border'=>0));
        $fm ->setNumFormat("$#,##0.00;[Red]-$#,##0.00");
        $arialNormalCurrency = $fm;
        
        $fm = & $workbook->addFormat(array('fontfamily'=>'Verdana','size'=>9, 'align'=>'right','top'=>1,'bold'=>1));
        $fm ->setNumFormat("$#,##0.00;[Red]-$#,##0.00");
        $arialNormalCurrencyTopBold = $fm;
      
     	$fm = & $workbook->addFormat(array('fontfamily'=>'Verdana', 'size'=>9, 'align'=>'right','border'=>0,'numformat'=>'0'));
     //	$fm ->setNumFormat("#,##0.00");
        $arialNormalNumber = $fm;
        
        $fm = & $workbook->addFormat(array('fontfamily'=>'Verdana','size'=>9, 'align'=>'right','top'=>1,'bold'=>1));
        $arialNormalTopBorderBold = $fm;
        
        $partternNumber =17;
     	$bgColor="white";
     	$fgColor="38";
        $fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'fgcolor'=>$fgColor, 'pattern'=>$partternNumber,
                'bgcolor'=>$bgColor, 'color'=>'black' ,'border'=>0, 'align'=>'left', 'valign'=>'bottom','bold'=>1,
                'top'=>0, 'bottom'=>0));
        //$fm->setTextWrap();
        $columnHeaderLeft = $fm;
        
        
	
	  	$emptyCell = array("data"=>"");
      
	    $row++;
        $this->_writeCommissionColumnHeaders($workbook, $sp, $row,$this->commissionTypeID);
      
        
        
        $startRow= $row; //internally rows are 0 based, in formulas rows are 1 based
		
		// print $startRow;
		$nIndex=0;
		
		
		
		
			//Summary
			$row=$startRow;
			
			
			$totalIntProfit = $summarys[0]['total_international_profit'];
			
			$totalIntCases = $summarys[0]['total_international_cases'];
			$totalWH = $summarys[0]['total_sales'];
			$totalRT = $summarys[0]['total_retail'];
			$avgProfit = $summarys[0]['avg_profit_per_case'];
			$totalCACases =$summarys[0]['total_canadian_cases'];
			$totalCAProfit=$summarys[0]['total_canadian_profit'];
			$totalProfit =0;
			$totalCases =0;
			$rowWH="";
			$rowRT="";
			
			if($this->commissionTypeID==4||$this->commissionTypeID==2||$this->commissionTypeID==1)
			{
				//$totalCases = $totalIntCases;

				
				
				
				if($this->commissionTypeID==1)
				{
					$totalWH =$summarys[0]['total_sales'];
					$totalRT =$summarys[0]['total_retail'];
				}
				else
				{
					$rowWH= $totalWH;
					$rowRT=$totalRT;
				}
			}
			else if($this->commissionTypeID==3)
			{
				$totalIntProfit=$totalIntProfit+ $summarys[1]['total_international_profit'];
				$totalIntCases=$totalIntCases+ $summarys[1]['total_international_cases'];
				$totalCACases=$totalCACases+$summarys[1]['total_canadian_cases'];
				$totalCAProfit=$totalCAProfit+$summarys[1]['total_canadian_profit'];
				
				
				
				$avgProfit =($avgProfit+$summarys[1]['avg_profit_per_case'])/2;
				
				$totalCases = $totalCACases+$totalIntCases;
				
				$totalWH =$totalWH +$summarys[1]['total_sales'];
				$totalRT =$totalRT +$summarys[1]['total_retail'];
				
			
				
			

			}
		
			if($this->commissionTypeID==1||$this->commissionTypeID==3)
			{
					$values = array(array("data"=>"Total CND profit:","format"=>$arialNormalBoldLeft),
							array("data"=>$totalCAProfit,"format"=>$arialNormalCurrency), 					
							array("data"=>"Total CND sold:","format"=>$arialNormalBoldLeft), 
							array("data"=>$totalCACases,"format"=>$arialNormalNumber), 
							array("data"=>$totalWH,"format"=>$arialNormalCurrency), 
							array("data"=>$totalRT,"format"=>$arialNormalCurrency)						
						       	);
			
					$this->_writeRow($sp, $values, $row, null,"F", "L");
					$row++;
			}
			 
				
			$totalCases = $totalCACases+$totalIntCases;
				
				
			
		

			$totalProfit=$totalCAProfit+$totalIntProfit;
			
			//international
			$values = array(array("data"=>"Total Inter profit:","format"=>$arialNormalBoldLeft),
							array("data"=>$totalIntProfit,"format"=>$arialNormalCurrency), 					
							array("data"=>"Total Inter sold:","format"=>$arialNormalBoldLeft), 
							array("data"=>$totalIntCases,"format"=>$arialNormalNumber), 
							array("data"=>$rowWH,"format"=>$arialNormalCurrency), 
							array("data"=>$rowRT,"format"=>$arialNormalCurrency)						
						       	);
			
			$this->_writeRow($sp, $values, $row, null,"F", "L");
			$row++;
			
			if($this->commissionTypeID==2||$this->commissionTypeID==4)
			$row++;
			
			else if($this->commissionTypeID==3||$this->commissionTypeID==1)// total 
			{
				$values = array(array("data"=>"Total profit:","format"=>$arialBoldNumberBorderTopLeft),
							array("data"=>$totalProfit,"format"=>$arialNormalCurrencyTopBold), 					
							array("data"=>"Total cs sold:","format"=>$arialBoldNumberBorderTopLeft), 
							array("data"=>$totalCases,"format"=>$arialBoldNumberBorderTop)
												
						       	);	
				$this->_writeRow($sp, $values, $row, $arialNormalTopBorderBold,"F", "J");
				$row++;
				$row++;
			}
			
			$values = array(array("data"=>"Commission:","format"=>$arialNormalBoldLeft),
							array("data"=>$totalBonus,"format"=>$arialNormalCurrency), 					
							array("data"=>"Avg profit:","format"=>$arialNormalBoldLeft), 
							array("data"=>$avgProfit,"format"=>$arialNormalCurrency)
						       	);
			$this->_writeRow($sp, $values, $row, null,"F", "J");
			
			
			$row++;
			$netProfit = $totalProfit-$totalBonus;
			
			$values = array(array("data"=>"Net profit:","format"=>$arialBoldNumberBorderTopLeft),
							array("data"=>$netProfit,"format"=>$arialNormalCurrencyTopBold)
						
						       	);
			$this->_writeRow($sp, $values, $row, $arialNormalTopBorderBold,"F", "H");
			
				
			//target information
			if($this->commissionTypeID==1 || $this->commissionTypeID==3)
			{
				$row+2;
				
				
				$minInTarget= $this->targetInfo[0]["target_cases_intl"]." cases International wine";
                $minCaTarget= $this->targetInfo[0]["target_cases_ca"]." cases Canadian wine";
                //(& $sp, $value, $row, $col, $format=null)
                
              
						       	
				$this->_writeCell($sp, array("data"=>"Minimum Target"),$row, "A", $columnHeaderLeft);
				$row++;
				if($this->commissionTypeID==1 )
				{
					$this->_writeCell($sp, array("data"=>$minInTarget),$row, "A", $arialNormalLeft);
					$row++;
				}
				$this->_writeCell($sp, array("data"=>$minCaTarget),$row, "A", $arialNormalLeft);
	                

                
			}
        
	}
  
    function _writeTitle(& $workbook, & $sp, & $row, $titleText)
    {
        $fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'bold'=>1, 
                'fgcolor'=>'white', 'bgcolor'=>'black', 'align'=>'left', 'valign'=>'center'));
        $fmReportTitle = $fm;

        $fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'bold'=>1, 'underline'=>1));
        $arialBoldUnderlined  = $fm;
        
        $fm = & $workbook->addFormat(array('right'=>2));
        $thickRight = $fm;   
        
   
        
        $this->_writeCell($sp, array("data"=>$titleText), $row, "A", $fmReportTitle); 
        $sp->mergeCells($row, $this->columns["A"]["index"], $row, $this->columns["C"]["index"]);
		$row++;

      //  $sp->mergeCells($row, $this->columns["A"]["index"], $row, $this->columns["C"]["index"]);
      //  $this->_writeCell($sp, array("data"=>"Generated on: " . date("M, Y")), $row, "A", $arialBoldUnderlined); 
      //  $row++;
		
    }
   
    function _writeColumnHeaders(& $workbook, & $sp, & $row, $headerType,$commissionType="")
    {
     	$partternNumber =1;
     	$bgColor="23";
     	$fgColor="26";
        $fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'fgcolor'=>$fgColor, 'pattern'=>$partternNumber,
                'bgcolor'=>$bgColor, 'color'=>'black' ,'border'=>1, 'align'=>'center', 'valign'=>'bottom','bold'=>1, 'left'=>0,'right'=>0,
                'top'=>0, 'bottom'=>0));
        //$fm->setTextWrap();
        $columnHeader = $fm;
          
        $fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'fgcolor'=>$fgColor, 'pattern'=>$partternNumber,
                'bgcolor'=>$bgColor,'color'=>'black' ,'border'=>0, 'align'=>'left', 'valign'=>'bottom','bold'=>1,
                'left'=>0,'right'=>0,
                'top'=>1, 'bottom'=>1));       
        $columnHeaderLeft = $fm;
        
        $fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'fgcolor'=>$fgColor, 'pattern'=>$partternNumber,
                'bgcolor'=>$bgColor,'color'=>'black' ,'border'=>0, 'align'=>'left', 'valign'=>'bottom','bold'=>1,
                'left'=>1,'right'=>0,
                'top'=>1, 'bottom'=>1));       
        $columnHeaderLeftBorderLeft = $fm;
        
        $fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'fgcolor'=>$fgColor, 'pattern'=>$partternNumber,
                'bgcolor'=>$bgColor,'color'=>'black' ,'border'=>0, 'align'=>'left', 'valign'=>'bottom','bold'=>1,
                'left'=>0,'right'=>1,
                'top'=>1, 'bottom'=>1));       
        $columnHeaderRightBorderLeft = $fm;
        
        $fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'fgcolor'=>$fgColor, 'pattern'=>$partternNumber,
                'bgcolor'=>$bgColor,'color'=>'black' , 'border'=>0, 'align'=>'right', 'valign'=>'bottom','bold'=>1,
               'left'=>1,'right'=>1,
                'top'=>1, 'bottom'=>1));       
        $columnHeaderRight = $fm;
        
//        $sp->setRow($row, 20); 
        
//       Licensee#	Customer		Store type	City	Address

		if($headerType ==1) // customer :  Licensee#	Customer		Store type	City	Address
		{
			$values = array(
			                array("data"=>"Licensee#", "format"=>$columnHeaderLeftBorderLeft), 
			                array("data"=>"Customer", "format"=>$columnHeaderLeft), 
			                array("data"=>"Store type", "format"=>$columnHeaderLeft), 
			              	array("data"=>"City", "format"=>$columnHeaderLeft), 
			              	array("data"=>"Address", "format"=>$columnHeaderLeft),
			              	array("data"=>"", "format"=>$columnHeaderLeft), 
			              	array("data"=>"", "format"=>$columnHeaderLeft), 
			              	array("data"=>"", "format"=>$columnHeaderLeft), 
			              	array("data"=>"", "format"=>$columnHeaderLeft), 
			              	array("data"=>"", "format"=>$columnHeaderLeft), 
			              	array("data"=>"", "format"=>$columnHeaderLeft), 
			              	array("data"=>"", "format"=>$columnHeaderRightBorderLeft)
				);
		
		}
		else if($headerType ==2) //Wine:   SKU#	Product	Liters	Type	Country	Bts sold	Profit/btl	Total profit	Cases sold	WH sale	Retail sales
		{
			$values = array(
		                array("data"=>"SKU#", "format"=>$columnHeaderLeftBorderLeft), 
		                array("data"=>"Product", "format"=>$columnHeaderLeft), 
		                array("data"=>"Liters", "format"=>$columnHeaderLeft), 
		              	array("data"=>"Type", "format"=>$columnHeaderLeft), 
		              	array("data"=>"Country", "format"=>$columnHeaderLeft), 
		              	array("data"=>"Bts sold", "format"=>$columnHeaderLeft), 
		              	array("data"=>"Profit/btl", "format"=>$columnHeaderLeft), 
		              	array("data"=>"Total profit", "format"=>$columnHeaderLeft), 
		              	array("data"=>"Case value", "format"=>$columnHeaderLeft), 
		              	array("data"=>"Cases sold", "format"=>$columnHeaderLeft), 
		              	array("data"=>"Whole sale", "format"=>$columnHeaderLeft), 
		              	array("data"=>"Retail sales", "format"=>$columnHeaderRightBorderLeft)
					);
		
		}
	
        $this->_writeRow($sp, $values, $row, $columnHeader);  
        $row++;        
    }
    
    function _writeCommissionColumnHeaders(& $workbook, & $sp, & $row, $commissionType="")
    {
     	$partternNumber =1;
     	$bgColor="44";
     	$fgColor="56";
        $fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'fgcolor'=>$fgColor, 'pattern'=>$partternNumber,
                'bgcolor'=>$bgColor, 'color'=>'black' ,'border'=>0, 'align'=>'center', 'valign'=>'bottom','bold'=>1,
                'top'=>0, 'bottom'=>0));
        //$fm->setTextWrap();
        $columnHeader = $fm;
          
        $fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'fgcolor'=>$fgColor, 'pattern'=>$partternNumber,
                'bgcolor'=>$bgColor,'color'=>'white' ,'border'=>0, 'align'=>'left', 'valign'=>'bottom','bold'=>1,
                'top'=>0, 'bottom'=>0));       
        $columnHeaderLeft = $fm;
        
        $fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'fgcolor'=>$fgColor, 'pattern'=>$partternNumber,
                'bgcolor'=>$bgColor,'color'=>'black' , 'border'=>0, 'align'=>'right', 'valign'=>'bottom','bold'=>10,
                'top'=>0, 'bottom'=>0));       
        $columnHeaderRight = $fm;
        
//        $sp->setRow($row, 20); 
        
//       Licensee#	Customer		Store type	City	Address

	 	if($commissionType==1)// BC regular stores
		{
		 	
			$cellValue_2 ="Total cases";
		 	$cellValue_3 ="Comm. rate";
		}
		if($commissionType==2 ||$commissionType==3)// BCLDB
		{
		 	$cellValue_2 ="Total sales";
		 	$cellValue_3 ="";
		}
		if($commissionType==4)// BCLDB
		{
		 	$cellValue_2 ="Total cases";
		 	$cellValue_3 ="";
		}
		$values = array(
            array("data"=>"Commission levels", "format"=>$columnHeaderLeft), 
            array("data"=>$cellValue_2, "format"=>$columnHeaderLeft), 
            array("data"=>$cellValue_3, "format"=>$columnHeaderLeft), 
          	array("data"=>"Comm. amount", "format"=>$columnHeaderLeft), 
          	array("data"=>"", "format"=>$columnHeaderLeft), 
          	array("data"=>"Total profit", "format"=>$columnHeaderLeft), 
          	array("data"=>"", "format"=>$columnHeaderLeft), 
          	array("data"=>"", "format"=>$columnHeaderLeft),
           	array("data"=>"Case value sold", "format"=>$columnHeaderLeft),              	
          	array("data"=>"Actual cases sold", "format"=>$columnHeaderLeft),            
          	array("data"=>"Total WH sale", "format"=>$columnHeaderLeft), 
          	array("data"=>"Total RT sale", "format"=>$columnHeaderLeft)
          	
		);
                        
        $this->_writeRow($sp, $values, $row, null);  
        $row++;
        
    }
    
   
    function _writeCell(& $sp, $value, $row, $col, $format=null)
    {
        return $sp->write($row, $this->columns[$col]["index"], $value["data"], array_key_exists("format",$value)?$value["format"]:$format);
    }
    
  
    
    function _writeRow(& $sp, $value, & $row, $format=null, $startCell="",$endCell="")
    {
     	$startIndexKey = "A";
     	$endIndexKey = "M";
     	
     
	 	if($startCell!="")	
     	{
			$startIndexKey = $startCell;
	     	$endIndexKey = $endCell;		
		}	
     
     //	print_r($value);
        for ($i = $startIndexKey, $j=0; $i!=$endIndexKey; $i++, $j++) 
        {   
            $this->_writeCell(& $sp, $value[$j], $row, $i, $format);
        }
    }

	function getCut($listVal,$l)
	{
		 return F60Date::ucwords1($listVal);
 	}
}
?>