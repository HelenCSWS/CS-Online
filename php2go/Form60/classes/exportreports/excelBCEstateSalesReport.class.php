<?php

import('Form60.bll.bllf60Reports');
import('Form60.util.excel.writer');

class excelBCEstateSalesReport
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
    
    var $nEndCol ="I";  // the end col of spread sheet
    
    var $bllData=null;
    
    var $colIndexs=array(); // 0,25
    

    var $totalCols=6; // total columns
    
    var $grpWines;
    
    var $ab_grpWines;
    
    var $sizeWines;
    
    var $ab_sizeWines;
    
    var $columns;
    var $rowDataArray;
    
    var $ab_columns;
    var $ab_rowDataArray;

    var $data_cfg;
       
    function excelBCEstateSalesReport()
    {
     	include('config/dataconfig.php');
     	$this->data_cfg = $DATA_CFG;
     		
		$this->estate_id =	$_REQUEST["estate_id"];
		$this->report_month =	$_REQUEST["report_month"];
		$this->report_year =	$_REQUEST["report_year"];
		
		$columns = array("A"=>array("index"=>0, "width"=>13), "B"=>array("index"=>1, "width"=>8.43), 
		        					"C"=>array("index"=>2, "width"=>13.41), "D"=>array("index"=>3, "width"=>10), 
										  "E"=>array("index"=>4, "width"=>35), "F"=>array("index"=>5, "width"=>16)); // first static title names 6 cols
										  
		$rowDataArray = array("date"=>"", "form60"=>"", 
		        					"account_type"=>"", "lic_no"=>"", 
										  "account"=>"", "city"=>""); // set a array for re-group the invoices
	
		$this->bllData = new F60ReportsData();
		
		$this->grpWines = $this->getWines($this->estate_id);
				
		$this->sizeWines = sizeof($this->grpWines);
		
		
		$indexKey ="A";
		
		$cols=6+$this->sizeWines+2;
		
		$initData=$this->getInitData($this->grpWines,$columns,$rowDataArray);
		$this->nEndCol = $initData[0];
		$this->columns = $initData[1];
		$this->rowDataArray = $initData[2];
		
		//Alberta 		
		$isSMCurrentMonth=true;
		if($this->report_year==date(Y)&&$this->report_month>=date(m))
			$isSMCurrentMonth=false;
			
		
		//if($this->report_month<date(m)) 
	//	$isSMCurrentMonth=false;
		if($isSMCurrentMonth)
		{
			if($this->estate_id!=1)	 // only for hillside and enotecca
			{
				$this->ab_grpWines = $this->getWines($this->estate_id,2);
				
				$this->ab_sizeWines = sizeof($this->ab_grpWines);
		
				$indexKey ="A";
				
				$cols=6+$this->sizeWines+2;
				
				$initData=$this->getInitData($this->ab_grpWines,$columns,$rowDataArray,2);
				$this->ab_nEndCol = $initData[0];
				$this->ab_columns = $initData[1];
				$this->ab_rowDataArray = $initData[2];
			}
		}
		$this->generateSpreadsheet(false);		        
   }   
   
   function getInitData($arrayWines,$columns, $rowDataArray, $province_id =1)   
   {
    	$sizeWines = sizeof($arrayWines);
		
		$this->colIndexs =range("A","Z");
		
		$i=0;
		
		$indexKey ="A";
		
		$cols=6+$sizeWines+2;
		
		$k=0;		
		
		$j=0;
			

		
		for($i=6;$i<$cols; $i++) // start from G, stop at wines and 2 cols
		{ 			 
			/* 	$intLength = Intval(($i-1)/26); 	 // 26 letters for Excel cell name
		 	if($intLength<1)
		 	{
				$indexKey=$this->colIndexs[$i-1];
			}
			else
			{
				$j =$i%26;
				$index = $intLength-2;
				
				if($j==0)$j=26;
				
				$indexKey=$this->colIndexs[$index].$this->colIndexs[$j-1]; //"AA,AB, AC ... BA,BB,BC ..."
			}
			
	*/		 	
		 
			
		 	
		 	if($i>25)
		 	{
		 	 
		 	    if($i>=52) 
		 		{
		 		
		 			if($i>=78)
		 			{	
		 				$j=$i-78;
				 
		 				$indexKey="B".$this->colIndexs[$j]; 
					 }
					 else
					 {
				 		$j=$i-52;
				 
		 				$indexKey="B".$this->colIndexs[$j]; 
		 			}
		 		}
		 		else
		 		{
					$j =$i-26;
					
					$indexKey="A".$this->colIndexs[$j]; // if over z, the start AA, to AZ
				}
			}
			else
			{
				$indexKey=$this->colIndexs[$i];
			}
			
			
			if($i>=$cols-1)
			{
			 
				if($i==$cols-1)
					$rowDataArray["total_due"]="";
				else
					$rowDataArray["warehouse"]="";
			}
					
			if($i>=$cols-2)
			{
			
				$columns["$indexKey"]=array("index"=>$i,"width"=>'12');	
				
				if($province_id ==2 && $j<2)	
					$this->columns["$indexKey"]=array("index"=>$i,"width"=>'12');		
					
					$j++;
			}		
			else
			{
					$columns["$indexKey"]=array("index"=>$i,"width"=>'4.30');
			 	if($k<=$sizeWines-1)
			 	{
					$indexExcelWines=$arrayWines[$k]['cspc_code'];
				}
				$k++;
			}
			
		}	
		

		$nEndCol = $indexKey;
		
		$retArray= array();
		$retArray[0]=$nEndCol;
		$retArray[1]=$columns;
		$retArray[2]=$rowDataArray;
		
		return $retArray;
		

}
   
    function getWines($estate_id, $province_id =1)
	{	
	 
	 	if($province_id ==2)
	 	{	
	 	
	 		$wineSkus=$this->bllData->getSkusById($estate_id, $province_id, $this->report_month,$this->report_year);
	 	}
	 	else
	 	
			$wineSkus=$this->bllData->getSkusById($estate_id);
		
		$wines =array(); //array( sku,wine_name, bottle per case)
		

		$i=0;
		foreach($wineSkus as $group=>$wine)
		{
				
			$wineInfo=$this->bllData->getSingleWineInfoBySku($wine['cspc_code'],$province_id,$this->report_month,$this->report_year);
			 	
			 		
			 		
			if($wineInfo!="")
			{
				//replace -vic and -okan			
				$wineInfo=str_replace('- okan','',$wineInfo);
				$wineInfo=str_replace('-okan','',$wineInfo);
				$wineInfo=str_replace('- vic','',$wineInfo);
				$wineInfo=str_replace('-vic','',$wineInfo);
				$wineInfo=str_replace('- G','',$wineInfo);
				$wineInfo=str_replace('- W','',$wineInfo);
				
				$wines[$i]=array('cspc_code'=>trim($wine['cspc_code']),'wine'=>trim($wineInfo),'bottles_per_case'=>$wine['bottles_per_case']);
			}
			$i++;
		}
		return $wines;
	}
	
	
    function generateSpreadsheet($returnFile=true)
    {			    

			$estate_name = $this->bllData->getEstate($this->estate_id);
			
        	$this->titleText="Monthly sales report - $estate_name";
        	
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
	        
	        $row++; // blank row
     
			$this->_writeColumnHeaders($workbook, $sp, $row);
	 		$this->_writeData($workbook, $sp, $row);
      
        	$result = $workbook->close();
         
	   		if ($returnFile)
            	return $filePath;
       
    }
    
    function debugText($text)
    {
	    $fp = fopen("logs/test.log","a");
		fputs($fp, $text);
		fclose($fp);
		
	}
	


	function _regroupForm60s($f60Datas, $org_rowDataArray,$grpWines, $province_id =1)
	{
	 	$rowsData=array();

	 	$rowDataArray = $org_rowDataArray; // copy an empty array
	 	
	 	$rowTotalBottless_DataArray = $org_rowDataArray;// copy an empty array to save total value;

		$i=0;
		
		$prev_separate_number =0;
		
		$total_due=0;
		$user_id=0;
		
		$totalRows = count($f60Datas);
		
		$prev_store_type_name="";
		
		$storeTypeTotalDue=0;
		
		$total_sales=0;
		
		$total_bottles=0;
	

		foreach($f60Datas as $grpData=>$f60Data)
		{
			if($province_id==2)
			{				
				$current_separate_number=$f60Data["licensee_number"];
				$display_month=sprintf("%02u",$this->report_month);
			}
			else
			{
				$current_separate_number=$f60Data["invoice_number"];
			}
			
			$current_store_type_name=$f60Data["license_name"];					
			if($i==0)
			{	

			 	$rowDataArray["form60"]=$f60Data["invoice_number"];
				
				if($province_id==1)	
					$rowDataArray["date"]=$f60Data["order_date"];
				else
						$rowDataArray["date"]="$display_month-01-".$this->report_year;
				//	$rowDataArray["date"]="$display_month-01-".date(Y);
					
				$rowDataArray["account_type"]=$f60Data["license_name"];
				$rowDataArray["lic_no"]=$f60Data["licensee_number"];
				$rowDataArray["account"]=$f60Data["customer_name"];
				$rowDataArray["city"]=$f60Data["city"];
				$user_id =$f60Data["user_id"];
				$cspc_code=trim($f60Data["cspc_code"]);				
				
				$total_due=$f60Data["total_amount"];
				
		
				$prev_store_type_name=$f60Data["license_name"];		
						
				if($province_id ==1)			
					$prev_separate_number=$f60Data["invoice_number"];	
				else
					$prev_separate_number=$f60Data["licensee_number"];	
					
				$rowDataArray["$cspc_code"]=$f60Data["btl_sold"]; //quantities
				
				$rowTotalBottles_DataArray["$cspc_code"]=$rowDataArray["$cspc_code"];
				
				if($i==$totalRows-1)
				{
					$storeTypeTotalDue=$f60Data["total_amount"];	
				}
			}
			else
			{	
			 	if($current_separate_number!=$prev_separate_number) // add prev array to parent array(rowsdataarray), start a new inovice,
				{
						$rowDataArray["total_due"]=Number::fromDecimalToCurrency($total_due,"$", ".", ",", 2, "left");	
						
						$storeTypeTotalDue=$storeTypeTotalDue+round($total_due,2); // add store total together;

						$rowDataArray["warehouse"]=$this->getWarehouse($user_id,$province_id);
						
						array_push($rowsData,$rowDataArray); // new array
						
						$total_due=$f60Data["total_amount"];
						
						
						//check if the same store type, if not, create new array
						if($current_store_type_name!=$prev_store_type_name) // add another row
						{
							$rowDataArray =null;
							$rowDataArray = $this->rowDataArray;
							
							$rowDataArray["city"]="Total $prev_store_type_name";
							$rowDataArray["total_due"]=Number::fromDecimalToCurrency($storeTypeTotalDue,"$", ".", ",", 2, "left");		
						
							$total_sales=$total_sales+$storeTypeTotalDue;
							array_push($rowsData,$rowDataArray);
							
							$storeTypeTotalDue=0;
						
							$prev_store_type_name = $current_store_type_name;
						}
					
						//clean the $rowDataArray, main point is to clean the quantities, so the extra loop doesn't need to run
						$rowDataArray =null;
						$rowDataArray = $this->rowDataArray;
						
						// new row;				
						$total_due=$f60Data["total_amount"];
					
						$rowDataArray["form60"]=$f60Data["invoice_number"];
						
						if($province_id==1)
							$rowDataArray["date"]=$f60Data["order_date"];
						else
							$rowDataArray["date"]="$display_month-01-".$this->report_year;
						//	$rowDataArray["date"]="$display_month-01-".date(Y);
							
						$rowDataArray["account_type"]=$f60Data["license_name"];
						$rowDataArray["lic_no"]=$f60Data["licensee_number"];
						$rowDataArray["account"]=$f60Data["customer_name"];
						$rowDataArray["city"]=$f60Data["city"];
						$cspc_code=trim($f60Data["cspc_code"]);
												
						$user_id =$f60Data["user_id"];
						
						if($province_id ==1)
							$prev_separate_number =$f60Data["invoice_number"];
						else
							$prev_separate_number =$f60Data["licensee_number"];
							
						$rowDataArray["$cspc_code"]=$f60Data["btl_sold"]; //quantities
						
						if($rowTotalBottles_DataArray["$cspc_code"]=="" || $rowTotalBottles_DataArray["$cspc_code"]==null )
							$rowTotalBottles_DataArray["$cspc_code"]=0;
					
						$rowTotalBottles_DataArray["$cspc_code"]=intval($rowTotalBottles_DataArray["$cspc_code"])+$rowDataArray["$cspc_code"];
						
				}
				else // if same inovice, add total due together, save the quantities
				{						 	
				
						
					$total_due=$total_due+$f60Data["total_amount"];
				
					$rowDataArray["total_due"]=Number::fromDecimalToCurrency($total_due,"$", ".", ",", 2, "left");	
					
					//save the quantities
					$cspc_code=trim($f60Data["cspc_code"]);
					$rowDataArray["$cspc_code"]=$f60Data["btl_sold"]; //quantities				
					$rowTotalBottles_DataArray["$cspc_code"]=intval($rowTotalBottles_DataArray["$cspc_code"])+$rowDataArray["$cspc_code"];		
				}
				
				// wines loop
			}
			if($i==$totalRows-1)
			{			 		
			 	
							
				$rowDataArray["total_due"]=Number::fromDecimalToCurrency($total_due,"$", ".", ",", 2, "left");				
				$rowDataArray["warehouse"]=$this->getWarehouse($user_id,$province_id);
					
				array_push($rowsData,$rowDataArray);
				
				$rowDataArray =null;
				$rowDataArray = $this->rowDataArray;
						
				$rowDataArray["city"]="Total $prev_store_type_name";	
				
				if($totalRows>1)
					$storeTypeTotalDue=$storeTypeTotalDue+$total_due;

				
				$rowDataArray["total_due"]=Number::fromDecimalToCurrency($storeTypeTotalDue,"$", ".", ",", 2, "left");			
				array_push($rowsData,$rowDataArray);			
			}
			$i++;		
		}
		
	
		$rowDataArray =null;
		$rowDataArray = $this->rowDataArray;
		array_push($rowsData,$rowDataArray);
		
		$rowDataArray =null;
		$rowDataArray = $this->rowDataArray;

	
	
		$total_sales=$total_sales+$storeTypeTotalDue; // add last store type
	
		$rowDataArray["total_due"]=Number::fromDecimalToCurrency($total_sales,"$", ".", ",", 2, "left");	

		$rowDataArray["city"]="Total Sales";			
		
		array_push($rowsData,$rowDataArray);
	
		$rowTotalBottles_DataArray["city"]="Total Bottles";	
		
		$col_total_bottles=0;
		$col_total_cases=0;
		
		$rowTotalCases_DataArray = $this->rowDataArray;
		
		foreach($grpWines as $arrayData=>$wine)
		{
			$cspc_code=trim($wine["cspc_code"]);
			$bottle_per_case=$wine["bottles_per_case"];
			
			$col_total_bottles = $col_total_bottles+$rowTotalBottles_DataArray["$cspc_code"];
			
			//cases
			$cases =0;
			if(intval($rowTotalBottles_DataArray["$cspc_code"])!==0)
			{
				$cases=$rowTotalBottles_DataArray["$cspc_code"]/$bottle_per_case;
			}	
			$rowTotalCases_DataArray["$cspc_code"]=$cases;
						
			$col_total_cases=$col_total_cases+$cases;
		}
		
		$rowTotalBottles_DataArray["total_due"]=$col_total_bottles;		
	
		array_push($rowsData,$rowTotalBottles_DataArray);
		
		$rowTotalCases_DataArray["city"]="Total Cases";
		$rowTotalCases_DataArray["total_due"]=round($col_total_cases,2);
			
		array_push($rowsData,$rowTotalCases_DataArray);
		
		return $rowsData;		
	}
	
	
	
	function getWarehouse($user_id, $province_id=1)
	{
	 	if($province_id ==2)
	 		return $this->data_cfg["NAME_ALBERTA"];
	 		
	 		
	 	$name=$this->data_cfg["NAME_VANCOUVER"];
	 	
	 	$okanagan_user_id=$this->data_cfg["OKANAGAN_USER"];
		$victoria_User_id=$this->data_cfg["VAN_ISLAND_USER"];
		
		switch ($user_id)
		{
			case $okanagan_user_id: // okangan
				$name=$this->data_cfg["WARE_HOUSE_SIGNATURE"];
				break;
			case $victoria_User_id: //vancouver island
				$name=$this->data_cfg["WARE_HOUSE_IBD"];
			 break;
		}
		
		return $name;
	}
    function _writeData(&$workbook, & $sp, & $row)
    {
     	// BC     	
   		for($i=0;$i<=3; $i++) // location
     	{		
	       		$reportData = $this->bllData->getForm60Details($this->estate_id, $this->report_year,$this->report_month,$i);
	       		
	       		if(count($reportData)>0)
	       		{
 					$rowsData = $this->_regroupForm60s($reportData,$this->rowDataArray,$this->grpWines,1); 				
					$this->_writeSheetData(&$workbook, & $sp,& $row,$rowsData, $this->grpWines,1,$i);
				}
 		
 		}
 		//ALBerta 	
 		if($this->estate_id!=1 ) //&& $this->report_month<date(m)
 		{
	 	/*	$this->nEndCol = $this->ab_nEndCol;
	 		
		 	$reportData = $this->bllData->getForm60Details($this->estate_id, $this->report_year,$this->report_month,0,2);
		 	if(count($reportData)>0)
	       	{
		 		$rowsData = $this->_regroupForm60s($reportData,$this->ab_rowDataArray,$this->ab_grpWines, 2);
		 	
	
				$this->_writeSheetData(&$workbook, & $sp,& $row,$rowsData,$this->ab_grpWines,2);
			}*/
		}
	}
    
    function _writeSheetData(&$workbook, & $sp, & $row, $rowsData,$grpWines, $province_id=1,$location_id=0)
    {
      	
 		$fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'border'=>1));
		$arialNormalBorder = $fm;
		
		$fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'fgcolor'=>'yellow','size'=>10, 'border'=>1));
		$arialNormalBorderYellow = $fm;
		
		$fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'fgcolor'=>'42','size'=>10, 'border'=>1));
		$arialNormalBorderGreen = $fm;
				

		$fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'fgcolor'=>'yellow', 'size'=>10, 'color'=>'red','align'=>'left','border'=>1,'bold'=>1,));
		$arialNormalBorderLefttRedBold = $fm;
		
		$fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'fgcolor'=>'42', 'size'=>10, 'color'=>'37','align'=>'left','border'=>1,'bold'=>1,));
		$arialNormalBorderLefttRedGreenBold = $fm;
		
		$fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'align'=>'right','border'=>1,'numformat'=>'0'));
		$arialNormalBorderNumRight = $fm;
		
		$fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'fgcolor'=>'42','size'=>10, 'align'=>'right','border'=>1,'numformat'=>'0','color'=>'37','bold'=>1,));							
		$arialNormalBorderNumRightRedGreenBold = $fm;
		
		$fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'fgcolor'=>'yellow','size'=>10, 'align'=>'right','border'=>1,'numformat'=>'0','color'=>'red','bold'=>1,));							
		$arialNormalBorderNumRightRedBold = $fm;
		
		$fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'fgcolor'=>'yellow','size'=>10, 'align'=>'left','border'=>1,'numformat'=>'0','color'=>'red','bold'=>1,));							
		$arialNormalBorderNumLeftRedBold = $fm;
		
        $fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>11, 'bold'=>1));
        $arialBoldUnderlined  = $fm;

		
		$i=0;
		$startRow= $row + 1; //internally rows are 0 based, in formulas rows are 1 based
		$nIndex=0;
		$totalsales=0;
		
		$f60_btls=0;
		
		$inovice_number="";
		
		$monthTxt=F60Date::getMonthTxt($this->report_month);
		
		$location_name="";
		if($province_id ==1)
		{
			switch ($location_id)
			{
			 	case 1:
			 		$location_name=$this->data_cfg["NAME_VANCOUVER_ISLAND"];
					break;		        
			    case 2:
			    	$location_name=$this->data_cfg["NAME_OKANAGAN"];
					break;
			    case 3:
			    	$location_name=$this->data_cfg["NAME_WHISTLER"];
					break;		        
			}
			if($location_id>0)
			{
				$row=$row+2;
		       // $this->_writeCell($sp, array("data"=>"$location_name: " ." $monthTxt ". date(Y)), $row, "A", $arialBoldUnderlined); 
		        $this->_writeCell($sp, array("data"=>"$location_name: " ." $monthTxt ".$this->report_year), $row, "A", $arialBoldUnderlined); 
				$row++;
		        $this->_writeColumnHeaders($workbook, $sp, $row);
			}	
		}
		else
		{
		 	$location_name=$this->data_cfg["NAME_ALBERTA"];
			$row=$row+2;
	       // $this->_writeCell($sp, array("data"=>"$location_name: " ." $monthTxt ". date(Y)), $row, "A", $arialBoldUnderlined); 
	        $this->_writeCell($sp, array("data"=>"$location_name: " ." $monthTxt ".$this->report_year), $row, "A", $arialBoldUnderlined); 
	        $row++;
	        $this->_writeColumnHeaders($workbook, $sp, $row, 2);
		}	        

	

		foreach($rowsData as $grpData =>$rowData)
	    {	     					     
		 	
			if(strstr($rowData["city"],"Total"))
			{
				if(strstr($rowData["city"],"Total Sales")||strstr($rowData["city"],"Total Bottles")||strstr($rowData["city"],"Total Cases"))
				{
						$colFormat=$arialNormalBorderLefttRedBold;
						$colNumFormat=$arialNormalBorderNumRightRedBold;
				}
				else
				{
						$colFormat=$arialNormalBorderLefttRedGreenBold;
						$colNumFormat=$arialNormalBorderNumRightRedGreenBold;
				}
			}
			else
			{
				$colFormat=$arialNormalBorder;
				$colNumFormat=$arialNormalBorderNumRight;
			}
			$values = array(
								array("data"=>$rowData["date"]), 
								array("data"=>$rowData["form60"]), 
								array("data"=>$rowData["account_type"]), 
								array("data"=>$rowData["lic_no"]),
								array("data"=>$rowData["account"]),
								array("data"=>$rowData["city"],"format"=>$colFormat)
							); 
			
			$j=0;
			
			if($province_id==1)
				$totalNum=Intval($this->sizeWines);
			else
				$totalNum=Intval($this->ab_sizeWines);
			
			for($j=0;$j<$totalNum; $j++) // start from G, stop at wines and 2 cols
			{					
			 	$cspc_code = trim($grpWines[$j]['cspc_code']);
			 
		 		$quantities=$rowData["$cspc_code"];
			 		
			
					
				$colData=array("data"=>"$quantities", "format"=>$arialNormalBorder);	
			
				if(strstr($rowData["city"],"Total"))
				{
				 	if(strstr($rowData["city"],"Total Sales")||strstr($rowData["city"],"Total Bottles")||strstr($rowData["city"],"Total Cases"))
						$colData=array("data"=>"$quantities", "format"=>$arialNormalBorderYellow);
					else
						$colData=array("data"=>"$quantities", "format"=>$arialNormalBorderGreen);
				}
				array_push($values,$colData);
			}
		
			
			$colData = array("data"=>$rowData["total_due"],"format"=>$colNumFormat);
			array_push($values,$colData);
			$colData =array("data"=>$rowData["warehouse"]); 
			array_push($values,$colData);
				 
            $this->_writeRow($sp, $values, $row, $arialNormalBorder); 
            $i++;
            $row++;
    
        }        
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
        
        $titleText1 = "Christopher Stewart Wine & Spirits Inc.";
        $sub_estate_name = $this->bllData->getEstate($this->estate_id);
        
        $estate_name=$this->data_cfg["ESTATE_ENOTECCA"];
		if($this->estate_id ==96 or $this->estate_id==97)
        {
	        $estate_name=$this->data_cfg["ESTATE_ENOTECCA"];
			
			$sub_estate_name ="- $sub_estate_name";
		}
		else
		{
			$estate_name = $sub_estate_name;	
			$sub_estate_name ="";
		}
        
        $monthTxt=F60Date::getMonthTxt($this->report_month);
        
        $titleText2="$estate_name Sales Report $sub_estate_name";
        
        $this->_writeCell($sp, array("data"=>""), $row , "A", $reportTitle); 
		$this->_writeCell($sp, array("data"=>""), $row+1 , "A", $reportTitle);  // SET BACK GROUD TO DARK GREY
        $sp->setRow($row, 18);
        $sp->setRow($row+1, 18);
        $sp->mergeCells($row, $this->columns["A"]["index"], $row, $this->columns["A"]["index"]);
        $sp->insertBitmap($row, $this->columns["A"]["index"], "resources/images/cswslogo.bmp", 33, -8, 1,1);

        $this->_writeCell($sp, array("data"=>$titleText1), $row, "B", $reportTitle); 
        $sp->mergeCells($row, $this->columns["B"]["index"], $row, $this->columns["E"]["index"]);
        $row++;
        
        $this->_writeCell($sp, array("data"=>$titleText2), $row, "B", $reportTitle); 
        $sp->mergeCells($row, $this->columns["B"]["index"], $row, $this->columns["E"]["index"]);
        
        $row+=3;
         
        $location_name=$this->data_cfg["NAME_VANCOUVER"];
    //    $this->_writeCell($sp, array("data"=>"$location_name: " ." $monthTxt ". date(Y)), $row, "A", $arialBoldUnderlined); 
        $this->_writeCell($sp, array("data"=>"$location_name: " ." $monthTxt ". $this->report_year), $row, "A", $arialBoldUnderlined); 
    }
    
    function _writeColumnHeaders(& $workbook, & $sp, & $row, $province_id=1)
    {
        $fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'fgcolor'=>'26', 
                'bgcolor'=>'black', 'border'=>1, 'align'=>'center', 'valign'=>'bottom','bold'=>'1',
                'top'=>2, 'bottom'=>2));
        //$fm->setTextWrap();
        $columnHeader = $fm;
               
        $fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'fgcolor'=>'26', 
                'bgcolor'=>'black', 'border'=>1, 'align'=>'left', 'valign'=>'bottom','bold'=>'1',
                'top'=>1, 'bottom'=>1));       
        $columnHeaderLeft = $fm;
        
        $fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'fgcolor'=>'26', 
                'bgcolor'=>'black', 'border'=>1, 'align'=>'right', 'valign'=>'bottom','bold'=>'1',
                'top'=>1, 'bottom'=>1));       
        $columnHeaderRight = $fm;  
		
		$fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'fgcolor'=>'27', 'color'=>'black',
                'bgcolor'=>'black', 'border'=>1, 'align'=>'right', 'valign'=>'bottom','TextRotation'=>'270', 'bold'=>'1',
                'top'=>1, 'bottom'=>1));       
        $columnHeaderVertical = $fm;       
          
        $sp->setRow($row, 200); 
             
       
       	if($province_id==1)
       	{
        	$colFormat = $columnHeaderRight;
	
		}
		else
		{
		 	$colFormat = $columnHeaderLeft;
		}
		$values = array(
						array("data"=>"Date", "format"=>$columnHeaderLeft), 
						array("data"=>"Invoice", "format"=>$colFormat),
						array("data"=>"Account Type", "format"=>$columnHeaderLeft), 
						array("data"=>"Licensee #", "format"=>$columnHeaderLeft), 
						array("data"=>"Account", "format"=>$columnHeaderLeft),
						array("data"=>"Territory", "format"=>$columnHeaderLeft)
						);
								
		$indexKey ="A";
		
		if($province_id ==1)
			$sizeWines =$this->sizeWines;
		else
			$sizeWines =$this->ab_sizeWines;
			
		$totalNum=Intval($sizeWines)+2;
		
		for($i=0;$i<$totalNum; $i++) // start from G, stop at wines and 2 cols
		{	
			if($i>=$sizeWines)
			{	
			 	if($i==$sizeWines)
					$wineInfo=array("data"=>"Total Due", "format"=>$columnHeaderRight);
				else
					$wineInfo=array("data"=>"Warehouse", "format"=>$columnHeaderLeft);
			}
			else
			{
			 	if($province_id==1)
				 	$wineName = $this->grpWines[$i]['wine'];
				else
					$wineName = $this->ab_grpWines[$i]['wine'];
					
				$wineInfo=array("data"=>"$wineName", "format"=>$columnHeaderVertical);				
			}
			
			array_push($values,$wineInfo);
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
        for ($i = "A", $j=0; $i!=$this->nEndCol; $i++, $j++) 
        {         
            $this->_writeCell(& $sp, $value[$j], $row, $i, $format);
        }
    }

}
?>