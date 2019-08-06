<?php

import('Form60.bll.bllf60Reports');
import('Form60.util.excel.writer');

class excelBCSalesCustomReports 
{
   
    var $invData;
    var $reportData;
    var $columns;
    
  
    var $estate_id = "";
    var $year="";
    var $month="";
  
  	var $maxColumLeter ="A";
   
	function excelBCSalesCustomReports ()
	{
	      
		$this->estate_id = $_REQUEST['estate_id'];
		$this->month = $_REQUEST['report_month'];
		$this->year = $_REQUEST['report_year'];
      
		$this->columns = array("A"=>array("index"=>0, "width"=>11), "B"=>array("index"=>1, "width"=>9), 
		"C"=>array("index"=>2, "width"=>11), "D"=>array("index"=>3, "width"=>9.15), "E"=>array("index"=>4, "width"=>30),
		"F"=>array("index"=>5, "width"=>40),
		"G"=>array("index"=>6, "width"=>26), "H"=>array("index"=>7, "width"=>8),
		"I"=>array("index"=>8, "width"=>6), "J"=>array("index"=>9, "width"=>7),
		"K"=>array("index"=>10, "width"=>17), "L"=>array("index"=>11, "width"=>12),
		"M"=>array("index"=>12, "width"=>13),"N"=>array("index"=>13, "width"=>15));
		
		$this->maxColumLeter = "O";
	
        $this->invData = new F60ReportsData();
        
        $this->reportData = $this->invData->getCustomInvoicesData( $this->estate_id, $this->month, $this->year);
        
        $invoiceDatas = $this->reportData["invoicData"];
        
        $this->estate=$this->invData->getEstate($this->estate_id);
        
//        print_r($invoiceDatas);
        
       //($search_id,$estateid,$from,$to)
        
        $this->titleText=$this->getReportTitle($this->estate, $this->month, $this->year);
                
        $this->generateSpreadsheet($invoiceDatas);
	  
    }
    
    function getReportTitle($estate, $month,$year)
    {
		
//		'C.C. Jentsch Cellars Sales Summary report for March 2016								'
		$title= $estate." Sales Summary report for " .date('F', mktime(0, 0, 0, $month, 10)).' '.$year;
		return $title;
	}
      
    function generateSpreadsheet( $invoiceDatas )
    {
     	$returnFile = false;
     		
        $worksheetName =$this->estate;	//. F60Date::getMonthTxt($this->report_month). " " . $this->report_year;
        
        $fileName = $this->estate. ".xls";
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
        $this->_writeTitle($workbook, $sp, $row,$this->titleText); //$row++
        
        $row++; // blank row
        
                /* This freezes the first six rows of the worksheet: */
	//	$sp->freezePanes("A1");
		$this->_writeColumnHeaders($workbook, $sp, $row);
		 
     	$this->_writeData($workbook, $sp, $row, $invoiceDatas);        
        $workbook->close();
         
        if ($returnFile)
            return $filePath;
        
    }
    
    function _writeData(&$workbook, & $sp, & $row,  $invoiceDatas )
    { 
			$this->_writeInvoiceData(&$workbook, & $sp, & $row, $invoiceDatas);
			
	}
	
    function _writeInvoiceData(&$workbook, & $sp, & $row, $invoiceDatas)
    {
	     $isWrite=true;
        $fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'border'=>1));
        $arialNormalBorder = $fm;
 
         $fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'border'=>1,color=>'red'));
        $arialNormalBorderRed = $fm;
  
        $fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'align'=>'right','border'=>1));
        $arialNormalBorderRight = $fm;
        
        $fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'align'=>'right','border'=>1,color=>'red'));
        $arialNormalBorderRightRed = $fm;
        
        $fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10,'align'=>'right', 'border'=>1,'bold'=>1, 'fgcolor'=>'yellow'));
        $arialBoldBorderYellowRightFg = $fm;
        
        
        $fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'fgcolor'=>'27', color=>'red',
                'border'=>1, 'align'=>'right',  'bold'=>'1',  'top'=>1, 'bottom'=>1));       
        $columnBorderBoldGrantTotal = $fm;     
		
		  $fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'border'=>0));
        $arialNormal = $fm;  
        
      /*   $fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'align'=>'right','border'=>1));
        $fm ->setNumFormat("$#,##0.00;[Black]-$#,##0.00");
        $arialBorderRighCurrency = $fm;
        
         $fm = & $workbook->addFormat(array('fontfamily'=>'Verdana','size'=>9, 'align'=>'right','border'=>0));
        $fm ->setNumFormat("$#,##0.00;[Red]-$#,##0.00");
        $arialNormalCurrency = $fm;
        
         $fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'border'=>1, 'numformat'=>'$0.00'));
        $arialNormalBorderCurrency = $fm;
        
        
        $fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'align'=>'right','border'=>1,'bold'=>1, 'fgcolor'=>'yellow'));
        $arialNormalBorderBoldRight = $fm;
        
        $fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'border'=>1, 'fgcolor'=>'yellow'));
        $arialNormalBorderYellowFg = $fm;
 
 */
 	//	 $fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'border'=>1, 'fgcolor'=>'yellow'));
      //  $arialNormalBorderYellowBorFg = $fm;
        
		  $i=1;
       // $row= $row + 1; //internally rows are 0 based, in formulas rows are 1 based
        
       // print $startRow;
        $nIndex=0;
        $totalsales=0;
        $total_btls=0;
        
	    $last_invNo="";
	    $last_storeType="";
	
	    $totalRow=0;
	    $total_amount=0;
	    $lastStoreType="";
		foreach($invoiceDatas as $sales)
        {
   		
		      		$invoiceNo= $sales["invoice_number"];
		
		    	        $orderDate= $sales["order_date"];
		            //	echo $sales["order_date"];
		            	$displayInvNo=$invoiceNo;
		            	$storeType= $sales["store_type"];
		            	$storeNo= $sales["licensee_number"];
		            	$customer= $sales["customer_name"];
		            	$address = ($sales["address"]!="")?F60Date::ucwords1($sales["address"]):"N/A";
						if(substr($address,0,1)=="-")
						{
							$address=substr_replace($address,'',0,2);
						}
		
					
						$wine= $sales["wine_name"];
		            	$sku= $sales["sku"];
		            	$quantity= $sales["orqt"];
		            	$totalCS= $sales["total_cs"];
		            	$wholesale= $sales["csws_price"];
		            	$retail= $sales["market_price"];
		            	$status= $sales["isPaid"];
		            	$amount= $sales["amount"];
		            	$store_type_id= $sales["lkup_store_type_id"];
		            
		            	
		            
					if($invoiceNo==$last_invNo)
					{
						$orderDate="";
						$storeType="";
						$storeNo="";
						$customer="";
						$address="";
						$displayInvNo="";
						$displayAmount="";
						//	$total_amount=$total_amount+$amount;
					}
					else
					{
					//	$displayAmount= $invoiceNo;
					 	$displayAmount=$this->invData->getTotalAmountByInvoiceNo($this->estate_id,$invoiceNo);
					 	
					    $displayAmount = Number::fromDecimalToCurrency($displayAmount,"$", ".", ",", 2, "left");
					 	
						$totalRow =$row; // remember the first row for put total amount value ;
					}
				
						$last_invNo = $invoiceNo;
						
						if($store_type_id!=$lastStoreType&&$lastStoreType!="")
						{
						 	
							$totalInfo =$this->invData->getBCTotalInfoByStoreType($this->estate_id,$this->year,$this->month,$lastStoreType);
							$totalStoreTypequantity = $totalInfo[0]["orqt"];
							$totalStoreTypeCS = $totalInfo[0]["total_cs"];
							$displayStoreTypeAmount = Number::fromDecimalToCurrency($totalInfo[0]["amount"],"$", ".", ",", 2, "left");
								
							$values = array(array("data"=>""), 
							array("data"=>""), 
							array("data"=>""), 
							array("data"=>""), 
							array("data"=>""), 
							array("data"=>""), 	                      
							array("data"=>""),
							array("data"=>""),
							array("data"=>$totalStoreTypequantity,"format"=>$arialBoldBorderYellowRightFg), 
							array("data"=>$totalStoreTypeCS,"format"=>$arialBoldBorderYellowRightFg), 
							array("data"=>"","format"=>$arialBoldBorderYellowRightFg), 
							array("data"=>"","format"=>$arialBoldBorderYellowRightFg), 
							array("data"=>"","format"=>$arialBoldBorderYellowRightFg), 
							array("data"=>$displayStoreTypeAmount,"format"=>$arialBoldBorderYellowRightFg));
					
						 	$this->_writeRow($sp, $values, $row, $arialNormalBorder); 
							$row++;
						}
						
						$lastStoreType= $store_type_id;
						
						$RightFormat = $arialNormalBorderRight;
						$normalFormat=$arialNormalBorder;
						if($wholesale=="0")
						{
							$RightFormat= $arialNormalBorderRightRed;
							$normalFormat=$arialNormalBorderRed;
						}
			/*	
			
			Array		
(		
    [0] => Array		
        (		
            [orqt] => 180		
            [total_cs] => 15.00		
            [amount] => 2918.31		
        )		



			if($wholesale=="0")
				{
						$values = array(array("data"=>$orderDate), 
						array("data"=>$displayInvNo), 
						array("data"=>$storeType), 
						array("data"=>$storeNo), 
						array("data"=>$customer), 
						array("data"=>$address), 	                      
						array("data"=>$wine),
						array("data"=>$sku),
						array("data"=>$quantity,"format"=>$arialNormalBorderRightRed), 
						array("data"=>$totalCS,"format"=>$arialNormalBorderRightRed), 
						array("data"=>("$".$wholesale),"format"=>$arialNormalBorderRightRed), 
						array("data"=>("$".$retail),"format"=>$arialNormalBorderRightRed), 
						array("data"=>$status), 
						
						array("data"=>Number::fromDecimalToCurrency($amount,"$", ".", ",", 2, "left"),"format"=>$arialNormalBorderRight));
						 $this->_writeRow($sp, $values, $row, $arialNormalBorderRed); 
				}
				else
				{*/
					$values = array(array("data"=>$orderDate), 
						array("data"=>$displayInvNo), 
						array("data"=>$storeType), 
						array("data"=>$storeNo), 
						array("data"=>$customer), 
						array("data"=>$address), 	                      
						array("data"=>$wine),
						array("data"=>$sku),
						array("data"=>$quantity,"format"=>$RightFormat), 
						array("data"=>$totalCS,"format"=>$RightFormat), 
						array("data"=>("$".$wholesale),"format"=>$RightFormat), 
						array("data"=>("$".$retail),"format"=>$RightFormat), 
						array("data"=>$status), 
						array("data"=>$displayAmount,"format"=>$RightFormat));
					
						 $this->_writeRow($sp, $values, $row, $normalFormat); 
			
		//	if($invoiceNo!=$last_invNo)
		//	{
			//	$this->_writeCell($sp, array("data"=>$displayAmount), $totalRow, "N", $arialNormalBorderRight);
		//	}
			
			
		
			//	}
            // $totalsales =$totalsales+$sales["total_amount"];
             //$total_btls = $total_btls+$sales["btl_sold"];
            //	$sp->setRow($row, 15); 
	           
    	        $i++;
        	    $row++;
        
       }
	//			  $this->_writeCell($sp, array("data"=>"=SUM(J$startRow:J$endRow)"), $row, "J", $arialNormalBorderYellowFg);
	
		//Last Store Type total
			$totalInfo =$this->invData->getBCTotalInfoByStoreType($this->estate_id,$this->year,$this->month,$lastStoreType);
							$totalStoreTypequantity = $totalInfo[0]["orqt"];
							$totalStoreTypeCS = $totalInfo[0]["total_cs"];
						//	$displayStoreTypeAmount = "$".$totalInfo[0]["amount"];
							
						$displayStoreTypeAmount  = Number::fromDecimalToCurrency($totalInfo[0]["amount"],"$", ".", ",", 2, "left");
								
							$values = array(array("data"=>""), 
							array("data"=>""), 
							array("data"=>""), 
							array("data"=>""), 
							array("data"=>""), 
							array("data"=>""), 	                      
							array("data"=>""),
							array("data"=>""),
							array("data"=>$totalStoreTypequantity,"format"=>$arialBoldBorderYellowRightFg), 
							array("data"=>$totalStoreTypeCS,"format"=>$arialBoldBorderYellowRightFg), 
							array("data"=>"","format"=>$arialBoldBorderYellowRightFg), 
							array("data"=>"","format"=>$arialBoldBorderYellowRightFg), 
							array("data"=>"","format"=>$arialBoldBorderYellowRightFg), 
							array("data"=>$displayStoreTypeAmount,"format"=>$arialBoldBorderYellowRightFg));
					
						 	$this->_writeRow($sp, $values, $row, $arialNormalBorder); 
							$row++;
							
							//grand total
			$totalInfo =$this->invData->getBCTotalInfoByStoreType($this->estate_id,$this->year,$this->month,-1);
							$totalStoreTypequantity = $totalInfo[0]["orqt"];
							$totalStoreTypeCS = $totalInfo[0]["total_cs"];
						//	$displayStoreTypeAmount = "$".$totalInfo[0]["amount"];
							
							$displayStoreTypeAmount  = Number::fromDecimalToCurrency($totalInfo[0]["amount"],"$", ".", ",", 2, "left");
								
							$values = array(array("data"=>""), 
							array("data"=>""), 
							array("data"=>""), 
							array("data"=>""), 
							array("data"=>""), 
							array("data"=>""), 	                      
							array("data"=>""),
							array("data"=>"Total:","format"=>$columnBorderBoldGrantTotal), 
							array("data"=>$totalStoreTypequantity,"format"=>$columnBorderBoldGrantTotal), 
							array("data"=>$totalStoreTypeCS,"format"=>$columnBorderBoldGrantTotal), 
							array("data"=>"","format"=>$columnBorderBoldGrantTotal), 
							array("data"=>"","format"=>$columnBorderBoldGrantTotal), 
							array("data"=>"","format"=>$columnBorderBoldGrantTotal), 
							array("data"=>$displayStoreTypeAmount,"format"=>$columnBorderBoldGrantTotal));
					
						 	$this->_writeRow($sp, $values, $row, $arialNormal); 
							$row++;
	
    }
    
    function _writeTitle(& $workbook, & $sp, & $row)
    {
         $fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'bold'=>1, 
                'fgcolor'=>'white', 'bgcolor'=>'black', 'align'=>'center', 'valign'=>'center'));
        $reportTitle = $fm;

        $fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'bold'=>1, 'underline'=>1));
        $arialBoldUnderlined  = $fm;
        
        $fm = & $workbook->addFormat(array('right'=>2));
        $thickRight = $fm;
        
        $titleText1 = $this->titleText;
       
        
        $this->_writeCell($sp, array("data"=>$titleText1), $row, "A", $reportTitle); 
        $sp->mergeCells($row, $this->columns["A"]["index"], $row, $this->columns["N"]["index"]);
        $sp->setRow($row, 30);
        $row++;
    }
    
    function _writeColumnHeaders(& $workbook, & $sp, & $row)
    {
        $fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'fgcolor'=>'silver', 'bold'=>1, 
                'bgcolor'=>'black', 'border'=>1, 'align'=>'center', 'valign'=>'bottom',
                'top'=>1, 'bottom'=>1));
     
        $columnHeader = $fm;
        
     
        
        
        $fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'fgcolor'=>'silver', 'bold'=>1, 
                'bgcolor'=>'black', 'border'=>1, 'align'=>'left', 'valign'=>'bottom',
                'top'=>1, 'bottom'=>1));       
        $columnHeaderLeft = $fm;
        
        $fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'fgcolor'=>'silver', 'bold'=>1, 
                'bgcolor'=>'black', 'border'=>1, 'align'=>'right', 'valign'=>'bottom',
                'top'=>1, 'bottom'=>1));       
        $columnHeaderRight = $fm;
        
        
          
        $sp->setRow($row, 20); 
        
      
					$values = array(array("data"=>"Ordered", "format"=>$columnHeaderLeft), 
		                        array("data"=>"Invoice#", "format"=>$columnHeaderRight), 
		                        array("data"=>"Store type", "format"=>$columnHeaderLeft), 
		                        array("data"=>"Store#", "format"=>$columnHeaderRight), 
		                        array("data"=>"Customer", "format"=>$columnHeaderLeft), 
		                        array("data"=>"Address", "format"=>$columnHeaderLeft), 
		                        
		                        array("data"=>"Wine", "format"=>$columnHeaderLeft), 
		                        array("data"=>"SKU", "format"=>$columnHeaderLeft), 
		                        array("data"=>"Bottles", "format"=>$columnHeaderRight), 
		                        array("data"=>"Cases", "format"=>$columnHeaderRight),
										array("data"=>"Wholesale price", "format"=>$columnHeaderRight),
										array("data"=>"Retail price", "format"=>$columnHeaderRight), 
		                        
		                        array("data"=>"Status", "format"=>$columnHeaderLeft), 
		                        array("data"=>"Invoice Amount", "format"=>$columnHeaderLeft));						
			
		
                               
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
     	
        for ($i = "A", $j=0; $i!=$this->maxColumLeter; $i++, $j++)
        {
            $this->_writeCell(& $sp, $value[$j], $row, $i, $format);
        }
    }

}
?>