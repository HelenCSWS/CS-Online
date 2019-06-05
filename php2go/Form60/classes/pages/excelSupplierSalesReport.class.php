<?php

import('Form60.bll.bllsupplierData');
import('Form60.util.excel.writer');

class excelSupplierSalesReport
{
    var $report_month;
    var $report_year;
    var $SPData;
    var $reportData;
    var $columns;
    
    var $estate_id;
    var $titleText="";
    var $estate_name="";
    
    var $fileType=1;  //
    
    var $province_id = 1;
   
    
    function excelSupplierSalesReport()
    {
        
        $date1 =$_REQUEST["date1"];
        $date2 =$_REQUEST["date2"];
        $dateType=$_REQUEST["dateType"];
        
		  $this->province_id=$_REQUEST["province_id"];
        
        $wine_id=$_REQUEST["wine_id"];
        $store_type_id=$_REQUEST["store_type_id"];
        
        $this->estate_id =	$_REQUEST["estate_id"];
        

	      if(F60DbUtil::checkIsBCByEstate($this->estate_id)&& $this->province_id ==1)
	      {
				$this->fileType =0; //bc supplier in bc
			$this->columns = array("A"=>array("index"=>0, "width"=>13.57), "B"=>array("index"=>1, "width"=>8), 
	                "C"=>array("index"=>2, "width"=>11), "D"=>array("index"=>3, "width"=>9.30), "E"=>array("index"=>4, "width"=>35),
	                "F"=>array("index"=>5, "width"=>35), "G"=>array("index"=>6, "width"=>18), "H"=>array("index"=>7, "width"=>6),
	                "I"=>array("index"=>8, "width"=>7.29),"J"=>array("index"=>9, "width"=>11),
						 "K"=>array("index"=>10, "width"=>8),"L"=>array("index"=>11, "width"=>13.71)
						 
						 );	
				
			}
			else 
			{
			 	if($this->province_id ==2)
			 	{
						$this->columns = array("A"=>array("index"=>0, "width"=>10), "B"=>array("index"=>1, "width"=>35), 
	                "C"=>array("index"=>2, "width"=>43), "D"=>array("index"=>3, "width"=>13), "E"=>array("index"=>4, "width"=>25),
	                "F"=>array("index"=>5, "width"=>6.40), "G"=>array("index"=>6, "width"=>7), "H"=>array("index"=>7, "width"=>7),
	                "I"=>array("index"=>8, "width"=>11.57)
						 );	
				}
				else
				{
					$this->columns = array("A"=>array("index"=>0, "width"=>20), "B"=>array("index"=>1, "width"=>13), 
	                "C"=>array("index"=>2, "width"=>35), "D"=>array("index"=>3, "width"=>35), "E"=>array("index"=>4, "width"=>25),
	                "F"=>array("index"=>5, "width"=>35), "G"=>array("index"=>6, "width"=>7), "H"=>array("index"=>7, "width"=>6.45),
	                "I"=>array("index"=>8, "width"=>11.57),"J"=>array("index"=>9, "width"=>10.29)
						 );
				}
				
			}
		
		
        
        
        if(F60DbUtil::checkIsBCByEstate($this->estate_id)&&$this->province_id==1)
        		$order_by="delivery_date";
		  else
		 		$order_by="c.customer_name";
				 
		  $order_type="desc";
		  
        
        
        $this->SPData = new suppliersData();

        $this->reportData = $this->SPData->getSales($this->estate_id, $date1, $date2, $order_by,$order_type,$dateType, $store_type_id,-1, $this->province_id,$wine_id,0,1,100,0);
    
       
        $this->titleText=$this->getTitle($this->estate_id,$dateType,$date1,$date2,$this->province_id);
        $this->generateSpreadsheet($this->reportData);
        
    }
    
    function getDateTxt($dateValue) //YYYYMMDD
    {
    
		$month1 = F60Date::getMonthTxt(substr($dateValue,4,2));
				 	$yeat1= substr($dateValue,0,4);
				 	$day1 =substr($dateValue,-2);
				 	
		$dateTxt=$month1." ".$day1." ".$yeat1;
		
		return $dateTxt;
		
	}
	
    function getTitle($estate_id, $dateType, $date1, $date2, $province_id)
    {
     	$titleTxt = "sales report";
     	if($this->estate_id ==-1)
		 {
			$this->estate_name ="Enotecca winery";
		 }
		 else
		 {
			$this->estate_name = $this->SPData->getEstateName($this->estate_id);
		 }
		
		
		if(F60DbUtil::checkIsBCByEstate($this->estate_id)&&$province_id ==1)
			{		 
			 	$listTyle=0;
			 	$fromTxt= $this->getDateTxt($date1);
			 	$toTxt=$this->getDateTxt($date2);
			 	if($dateType==0)
				{
				 	$titleTxt ="$this->estate_name from $fromTxt to $toTxt";
				}
				else if($dateType==1)
			 	{
			 	 		 $sale_year =$date1;
							
						
						
				 	    if($date2 ==1)
				 	  	  		$byPeriod =" First quarter";// " 1<= month(o.delivery_date) and month(o.delivery_date)<=3 ";
				 	  	 else if($date2 ==2)
				 	  	 		$byPeriod = " Second quarter";//" 4<= month(o.delivery_date) and month(o.delivery_date)<=6 ";
				 	  	 else if($date2 ==3)
				 	  	 		$byPeriod = " Third quarter";//" 7<= month(o.delivery_date) and month(o.delivery_date)<=9 ";
				 	  	 else if($date2 ==4)
				 	  	 		$byPeriod = " Forth quarter";//" 10<= month(o.delivery_date) and month(o.delivery_date)<=12 ";
						else if($date2 ==-1)
						{
						 		$byPeriod = " YTD";
						}
					$titleTxt="$this->estate_name $sale_year $byPeriod";
			 	}
			 	else if($dateType==2)//
			 	{
				 		$sale_year =$date1;
				 		$byPeriod = F60Date::getMonthTxt($date2);
						$titleTxt="$this->estate_name $byPeriod $sale_year";
				}
				
				$titleTxt =$titleTxt." sales report: ";
			}
			else
			{	
			 
			 	$sale_year =$date1;
				if($dateType==1)
				{				
					 $sale_year =$date1;
			 	    if($date2 ==1)
			 	  	  		$byPeriod =" First quarter";// " 1<= month(o.delivery_date) and month(o.delivery_date)<=3 ";
			 	  	 else if($date2 ==2)
			 	  	 		$byPeriod = " Second quarter";//" 4<= month(o.delivery_date) and month(o.delivery_date)<=6 ";
			 	  	 else if($date2 ==3)
			 	  	 		$byPeriod = " Third quarter";//" 7<= month(o.delivery_date) and month(o.delivery_date)<=9 ";
			 	  	 else if($date2 ==4)
			 	  	 		$byPeriod = " Forth quarter";//" 10<= month(o.delivery_date) and month(o.delivery_date)<=12 ";
					else if($date2 ==-1)
					{
					 		$byPeriod = " YTD";
					}
					$titleTxt="$this->estate_name $sale_year $byPeriod";
				  		
				}
				else if($dateType==2)
				{
					$sale_year =$date1;
				 		$byPeriod = F60Date::getMonthTxt($date2);
						$titleTxt="$this->estate_name $byPeriod $sale_year sales report:";
					
				}
				
				if($province_id ==1)
				{
					$titleTxt =$titleTxt." sales report: ";
				}
				else
				{
					$titleTxt =$titleTxt." sales report: ";
				}
				
		}
		
		return $titleTxt;
		
		
	 }
    function getReportFile($month, $year)
    {
        $this->report_month =$month;
        $this->report_year = $year;
        return $this->generateSpreadsheet(true);
    }
    
    function generateSpreadsheet($reportData,$returnFile=true)
    {
        

		 
        $worksheetName =$this->titleText;	//. F60Date::getMonthTxt($this->report_month). " " . $this->report_year;
        
        $fileName = $this->estate_name. ".xls";
        $filePath = ROOT_PATH . "logs/" . $fileName;
        
        if ($returnFile)
            $workbook = new Spreadsheet_Excel_Writer($filePath);
        else
        {
            $workbook = new Spreadsheet_Excel_Writer();
            $workbook->send($fileName);
        }
        
         $workbook = new Spreadsheet_Excel_Writer();
         $workbook->send($fileName);
            
        $workbook->setVersion(8);
        $sp =& $workbook->addWorksheet($fileName);
        
        //set column widths
        foreach($this->columns as $column)
        {
            $sp->setColumn($column["index"], $column["index"], $column["width"]);
        }
        
        $row = 0;
        
        $this->_writeTitle($workbook, $sp, $row,$this->titleText); //$row++
        
        $row++; // blank row
        
       $sales = $reportData["sales_details"];
     //  print_r($sales);
		$totalRecords =$reportData["total_records"];
      
		 $this->_writeColumnHeaders($workbook, $sp, $row);
       $this->_writeSalesData($workbook, $sp, $row, $sales,$totalRecords);
      
        
        $workbook->close();
         
        if ($returnFile)
            return $filePath;
        
    }
    
    function _writeSalesData(&$workbook, & $sp, & $row, $salesData, $totalSales)
    {
        $fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'border'=>1));
        $arialNormalBorder = $fm;
 
        $fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'border'=>1, 'fgcolor'=>'yellow'));
        $arialNormalBorderYellowFg = $fm;
        
        $fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'border'=>1,'align'=>'right', 'fgcolor'=>'yellow'));
        $arialNormalBorderRightYellowFg = $fm;
 
        $fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'align'=>'right','border'=>1));
        $arialNormalBorderRight = $fm;
        
        $fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'align'=>'right','border'=>1,'bold'=>1, 'fgcolor'=>'yellow'));
        $arialNormalBorderBoldRight = $fm;
        
        $fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'border'=>1, 'numformat'=>'$0.00'));
        $arialNormalBorderCurrency = $fm;
        
        $fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'border'=>1, 'bottom'=>2));
        $arialNormalThickBottom = $fm;
        
        $fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'border'=>1, 'bottom'=>2, 
                'numformat'=>'0.00%'));
        $arialNormalThickBottomPercent = $fm;
        
        $fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'border'=>1, 'right'=>2,
                'numformat'=>'0.00%'));
        $arialNormalThickRightPercent = $fm;
        
        $fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'border'=>1, 'right'=>2,
                'BOTToM'=>2, 'numformat'=>'0.00%'));
        $arialNormalThickRightThickBottomPercent = $fm;
        
        $fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'numformat'=>'0.00%'));
        $arialNormalNoBorderPercent = $fm;
        
        $fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'bold'=>1));
        $arialBold = $fm;
        
     
        
        
		  $i=1;
        $startRow= $row + 1; //internally rows are 0 based, in formulas rows are 1 based
        
       // print $startRow;
        $nIndex=0;
        $totalsales=0;
        $total_btls=0;
		  
		 while ($sales=$salesData->fetch())
        {
         	
         	$address = ($sales["address"]!="")?F60Date::ucwords1($sales["address"]):"N/A";
         	if(substr($address,0,1)=="-")
				{
					$address=substr_replace($address,'',0,2);
				}
         	
				$nIndex=1;
      
	         
            $currentRow = $row+1;
            
            if($this->fileType==0)
            {
	            $values = array(array("data"=>($sales["delivery_date"])), 
	                    array("data"=>$sales["invoice_number"]), 
	                    array("data"=>$sales["license_name"]), 
	                     array("data"=>$sales["licensee_number"]), 
	                     array("data"=>$sales["customer_name"]), 
	                     array("data"=>$address), 
	                     array("data"=>$sales["city"]), 
	                     array("data"=>$sales["cases_sold"]), 
	                     array("data"=>$sales["btl_sold"]), 
	                     array("data"=>Number::fromDecimalToCurrency($sales["new_amount"],"$", ".", ",", 2, "left"),"format"=>$arialNormalBorderRight), 
	                     array("data"=>$sales["payment_status"]), 
	                     array("data"=>$sales["order_status"])); 
			 }
			 else
			 {
			   if($this->province_id ==2)
			   {
					$values = array(
	                     array("data"=>$sales["licensee_number"]), 
	                     array("data"=>$sales["customer_name"]), 
	                     array("data"=>$address), 
	                     array("data"=>$sales["city"]), 
	                     array("data"=>$sales["wine_name"]),
	                     array("data"=>$sales["cspc"]),
	                     array("data"=>$sales["cases_sold"]), 
	                     array("data"=>$sales["btl_sold"]), 
	                     array("data"=>Number::fromDecimalToCurrency($sales["total_amount"],"$", ".", ",", 2, "left"),"format"=>$arialNormalBorderRight), 
	                     array("data"=>$sales["payment_status"]), 
	                     array("data"=>$sales["order_status"])
								
								); 
				}
				else
				{
					$values = array(
	                    array("data"=>$sales["license_name"]), 
	                     array("data"=>$sales["licensee_number"]), 
	                     array("data"=>$sales["customer_name"]), 
	                     array("data"=>$address), 
	                     array("data"=>$sales["city"]), 
	                     array("data"=>$sales["wine_name"]),
	                     array("data"=>$sales["cspc"]),
	                     array("data"=>$sales["cases_sold"]), 
	                     array("data"=>$sales["btl_sold"]), 
	                     array("data"=>Number::fromDecimalToCurrency($sales["total_amount"],"$", ".", ",", 2, "left"),"format"=>$arialNormalBorderRight), 
	                     array("data"=>$sales["payment_status"]), 
	                     array("data"=>$sales["order_status"])); 
				}
			}
					
			 if($this->fileType==0)
			     $totalsales =$totalsales+$sales["new_amount"];
 			 else
                $totalsales =$totalsales+$sales["total_amount"];
             $total_btls = $total_btls+$sales["btl_sold"];
             
            $this->_writeRow($sp, $values, $row, $arialNormalBorder); 
            $i++;
            $row++;
        }
        $endRow=$row;
        
       /* $total_btls = $totalSales[0]["btl_sold"];
		  $total_amount = $totalSales[0]["total_amount"];
		  $totalcase = $totalSales[0]["cases_sold"];
		
			if(count($totalSales["totalSales"])>1)
			{
			 	
				$totalcase = $totalcase+$totalSales[1]["cases_sold"];
				$total_btls = $totalcase+$totalSales[1]["btl_sold"];
				$totalsales = $totalsales+$totalSales[1]["total_amount"];
			}*/
			
			 $total_btls =  Number::fromDecimalToCurrency($total_btls ,"", ".", ",", 2, "left");
			 $total_btls = substr($total_btls, 0, (strlen($total_btls)-3));
			 
			 $totalsales =  Number::fromDecimalToCurrency($totalsales ,"$", ".", ",", 2, "left");
			
		
		if($this->province_id ==2)
		{
				$this->_writeCell($sp, array("data"=>"Total:","format"=>$arialNormalBorderBoldRight), $row, "F", $arialBold);
      
      
	      $this->_writeCell($sp, array("data"=>"=SUM(G$startRow:G$endRow)"), $row, "G"
	                , $arialNormalBorderYellowFg);
	                
	      $this->_writeCell($sp, array("data"=>$total_btls), $row, "H"
	                , $arialNormalBorderRightYellowFg);
	                
	      //$totalsales ="$".$totalsales;
	      
	      $this->_writeCell($sp, array("data"=>$totalsales),$row, "I"
	                , $arialNormalBorderRightYellowFg);
		}
	   else
	   {
        $this->_writeCell($sp, array("data"=>"Total:","format"=>$arialNormalBorderBoldRight), $row, "G", $arialBold);
      
      
	      $this->_writeCell($sp, array("data"=>"=SUM(H$startRow:H$endRow)"), $row, "H"
	                , $arialNormalBorderYellowFg);
	                
	      $this->_writeCell($sp, array("data"=>$total_btls), $row, "I"
	                , $arialNormalBorderRightYellowFg);
	                
	      //$totalsales ="$".$totalsales;
	      
	      $this->_writeCell($sp, array("data"=>$totalsales),$row, "J"
	                , $arialNormalBorderRightYellowFg);
	   }
                
     
    }
    
    function _writeTitle(& $workbook, & $sp, & $row, $titleText)
    {
        $fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'bold'=>1, 
                'fgcolor'=>'white', 'bgcolor'=>'black', 'align'=>'left', 'valign'=>'center'));
        $reportTitle = $fm;

        $fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'bold'=>1, 'underline'=>1));
        $arialBoldUnderlined  = $fm;
        
        $fm = & $workbook->addFormat(array('right'=>2));
        $thickRight = $fm;
        
        $titleText1 = $titleText;
       
        
        $this->_writeCell($sp, array("data"=>$titleText1), $row, "A", $reportTitle); 
        $sp->mergeCells($row, $this->columns["A"]["index"], $row, $this->columns["I"]["index"]);
        $sp->setRow($row, 30);
        $row++;
        
 
    }
    
    function _writeColumnHeaders(& $workbook, & $sp, & $row)
    {
        $fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'fgcolor'=>'silver', 
                'bgcolor'=>'black', 'border'=>1, 'align'=>'center', 'valign'=>'bottom',
                'top'=>2, 'bottom'=>2));
     
        $columnHeader = $fm;
        
     
        
        
        $fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'fgcolor'=>'silver', 
                'bgcolor'=>'black', 'border'=>1, 'align'=>'left', 'valign'=>'bottom',
                'top'=>2, 'bottom'=>2));       
        $columnHeaderLeft = $fm;
        
        $fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'fgcolor'=>'silver', 
                'bgcolor'=>'black', 'border'=>1, 'align'=>'right', 'valign'=>'bottom',
                'top'=>2, 'bottom'=>2));       
        $columnHeaderRight = $fm;
        
        
          
        $sp->setRow($row, 20); 
        
        if($this->fileType==0)
        {
	        $values = array(array("data"=>"Ordered", "format"=>$columnHeaderLeft), 
	                        array("data"=>"Invoice#", "format"=>$columnHeaderRight), 
	                        array("data"=>"Store type", "format"=>$columnHeaderLeft), 
	                        array("data"=>"Licensee#", "format"=>$columnHeaderRight), 
	                        array("data"=>"Customer", "format"=>$columnHeaderLeft), 
	                        array("data"=>"Address", "format"=>$columnHeaderLeft), 
	                        array("data"=>"City", "format"=>$columnHeaderLeft), 
	                        array("data"=>"Cases", "format"=>$columnHeaderRight), 
	                        array("data"=>"Bottles", "format"=>$columnHeaderRight), 
	                        array("data"=>"Total", "format"=>$columnHeaderRight), 
	                        array("data"=>"Is Paid", "format"=>$columnHeaderLeft), 
	                        array("data"=>"Delivery Status", "format"=>$columnHeaderLeft), 							
									);
			}
			else
			{
			 	if($this->province_id ==2)
			 	{
					$values = array(
	                       
	                        array("data"=>"Licensee#", "format"=>$columnHeaderRight), 
	                        array("data"=>"Customer", "format"=>$columnHeaderLeft), 
	                        array("data"=>"Address", "format"=>$columnHeaderLeft), 
	                        array("data"=>"City", "format"=>$columnHeaderLeft), 
	                        array("data"=>"Wine", "format"=>$columnHeaderLeft), 
	                        array("data"=>"CSPC", "format"=>$columnHeaderRight),
	                        array("data"=>"Cases", "format"=>$columnHeaderRight), 
	                        array("data"=>"Bottles", "format"=>$columnHeaderRight), 
	                        array("data"=>"Total", "format"=>$columnHeaderRight) 
	                        					
									);
				}
				else
				{
					$values = array(
	                        array("data"=>"Store type", "format"=>$columnHeaderLeft), 
	                        array("data"=>"Licensee#", "format"=>$columnHeaderRight), 
	                        array("data"=>"Customer", "format"=>$columnHeaderLeft), 
	                        array("data"=>"Address", "format"=>$columnHeaderLeft), 
	                        array("data"=>"City", "format"=>$columnHeaderLeft), 
	                        array("data"=>"Wine", "format"=>$columnHeaderLeft), 
	                        array("data"=>"CSPC", "format"=>$columnHeaderRight),
	                        array("data"=>"Cases", "format"=>$columnHeaderRight), 
	                        array("data"=>"Bottles", "format"=>$columnHeaderRight), 
	                        array("data"=>"Total", "format"=>$columnHeaderRight) 
	                        					
									);
				}
			}
                        
       
        $this->_writeRow($sp, $values, $row, $columnHeader);  
        $row++;
        
    }
    
    function _writeCell(& $sp, $value, $row, $col, $format=null)
    {
        return $sp->write($row, $this->columns[$col]["index"], $value["data"], 
            array_key_exists("format",$value)?$value["format"]:$format);
    }
    
    function _writeRow(& $sp, $value, & $row, $format=null)
    {
      $endColum="M";
      if($this->fileType==1)
      {
			$endColum="K";
		}
		if($this->province_id ==2)
		{
			$endColum="J";
		}
	


        for ($i = "A", $j=0; $i!=$endColum; $i++, $j++)
        {
         
            $this->_writeCell(& $sp, $value[$j], $row, $i, $format);
        }
    }

}
?>