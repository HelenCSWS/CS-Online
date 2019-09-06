<?php

import('Form60.bll.bllAbVenderReports');
import('Form60.util.excel.writer');
import('php2go.util.Number');


class excelABVenderSalesReport
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
    
    var $sale_year;
    
    var $sale_month;
   
   	var $displayVender=false;
    
    function excelABVenderSalesReport($displayAsWebPage=true, $displayVender=false)
    {
  		
  		if($_REQUEST['sale_year']!="")
  		{
			$this->sale_year = $_REQUEST['sale_year'];
			$this->sale_month = $_REQUEST['sale_month'];
			
		}
	//	$this->estate_id =	$_REQUEST["estate_id"];
		$this->columns = array("A"=>array("index"=>0, "width"=>7.72), "B"=>array("index"=>1, "width"=>22), 
		          				"C"=>array("index"=>2, "width"=>5.30), "D"=>array("index"=>3, "width"=>8.45), 
								"E"=>array("index"=>4, "width"=>7.15), "F"=>array("index"=>5, "width"=>6),
								"G"=>array("index"=>6, "width"=>11.30), "H"=>array("index"=>7, "width"=>47.30),	"I"=>array("index"=>8, "width"=>9.00), "J"=>array("index"=>9, "width"=>9.00));
		
		  
        

         
       if ($displayAsWebPage)
        {
          $this->generateSpreadsheet();
     
        }
  			
  		$this->displayVender = $displayVender;
        
    }
    
    function getReportFile($sale_month,$sale_year)
    {
     	$this->sale_year = $sale_year;
     	$this->sale_month = $sale_month;
     	
     	
        return $this->generateSpreadsheet(true);
    }
    
    
    function generateSpreadsheet($returnFile=false)
    {
  	
		$sale_month =F60Date::getMonthTxt($this->sale_month);
		
        $this->titleText = "Alberta sales - ".$sale_month." ".$this->sale_year;
		$worksheetName = $this->titleText;
        
       $this->VerderData = new bllABVenderData();

		if($this->displayVender)
	        $this->reportData = $this->VerderData->getVenderSalesFromDB($this->sale_month,$this->sale_year);
	    else
    	    $this->reportData = $this->VerderData->getABSalesReportFromDB($this->sale_month,$this->sale_year);

        $fileName = $this->titleText. ".xls";
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
        $sp =& $workbook->addWorksheet($worksheetName);
        
        //set column widths
        foreach($this->columns as $column)
        {
            $sp->setColumn($column["index"], $column["index"], $column["width"]);
        }
        
        $row = 0;
        
        $this->_writeTitle($workbook, $sp, $row,$this->titleText); //$row++
        
        $row++; // blank row
        
    
		// $totalRecords =$reportData["total_records"];
      
		$this->_writeColumnHeaders($workbook, $sp, $row);
 		$sp->freezePanes(1);
		
		
       $this->_writeData($workbook, $sp, $row, $this->reportData);
      
       
        $workbook->close();
         
        if ($returnFile)
            return $filePath;
        
    }
    function currencyNumber($price)
    {
		return Number::fromDecimalToCurrency($price,"$", ".", ",", 2, "left");
	}
    
    function _writeData(&$workbook, & $sp, & $row, $infoData)
    {
        $fm = & $workbook->addFormat(array('fontfamily'=>'Calibri', 'size'=>10, 'border'=>1));
        $CalibriNormalBorder = $fm;
 
        $fm = & $workbook->addFormat(array('fontfamily'=>'Calibri', 'size'=>10, 'align'=>'right','border'=>1));
        $CalibriNormalBorderRight = $fm;
        
        $fm = & $workbook->addFormat(array('fontfamily'=>'Calibri',  'bold'=>1,'color'=>'red','size'=>10, 'align'=>'right','border'=>1));
        $CalibriNormalBorderRedFontRight = $fm;
        
        $fm = & $workbook->addFormat(array('fontfamily'=>'Calibri','bold'=>1,'color'=>'red','fgcolor'=>'yellow', 'size'=>10, 'align'=>'right','border'=>1));
        $CalibriNormalBorderRedYellowBKFontRight = $fm;
        
        $fm = & $workbook->addFormat(array('fontfamily'=>'Calibri', 'size'=>10, 'align'=>'right','border'=>1,'numformat'=>'0'));
        $CalibriNormalBorderNumRight = $fm;
        
        $fm = & $workbook->addFormat(array('fontfamily'=>'Calibri','size'=>10, 'align'=>'left','border'=>1));
        $CalibriNormalBorderLeft = $fm;
        
        $fm = & $workbook->addFormat(array('fontfamily'=>'Calibri','size'=>10, 'bold'=>1,'color'=>'red','border'=>1));
        $CalibriNormalBorderRedFontLeft = $fm;
        
      
		$i=1;
		$startRow= $row + 1; //internally rows are 0 based, in formulas rows are 1 based
		
		// print $startRow;
		$nIndex=0;
		$lastStoreNo="";
		$store_total_btls =0;
		$store_total_cases =0;
		$total_btls =0;
		$total_cases =0;
		
		$total_CSWS_btls=0;
		$total_CSWS_cs=0;
		$total_NWT_btls =0;
		$total_NWT_cs =0;
		
		$total_SASK_btls =0;
		$total_SASK_cs =0;

		while ($salesData=$infoData["sales_details"]->fetch())
		{
//            $currentRow = $row+1;
            
		//	if(intval($salesData["unit_sales"]>0))
 		//	{
			 	//$sql = "Select SKUA, product_name, size, unit_sales,btl_per_cs,total_cs, licensee_no,store_name,price_case,price_unit from ssds_sales_temp";		
			 	if($i!=1)
			 	{
			 	 	
					if($salesData["licensee_no"] !=$lastStoreNo ) // total  sales per store
					{
					 	$this->_writeBorderToRow($sp,$row,$CalibriNormalBorderRedFontRight);
						
						//Total
						$this->_writeCell($sp, array("data"=>"Total"), $row, "C" , $CalibriNormalBorderRedFontLeft);
		                
		                //total_bottles 
		                $endRow = $row;
						$this->_writeCell($sp, array("data"=>"=SUM(D$startRow:D$endRow)"), $row, "D"
		                , $CalibriNormalBorderRedFontRight);
		                
						//Empty cell
		                $this->_writeCell($sp, array("data"=>""), $row, "E"
		                , $CalibriNormalBorderNumRight);
		                
		                //total_cases 
		                $this->_writeCell($sp, array("data"=>"=SUM(F$startRow:F$endRow)"), $row, "F"
		                , $CalibriNormalBorderRedFontRight);
		                
		                $store_total_btls =0;
						$store_total_cases =0;
						
						$row =$row+1;
						
			        	$this->_writeBorderToRow($sp,$row,$CalibriNormalBorderRedFontRight);

						$row =$row+1;
						$startRow = $row;
						
					}
					
				}
				if($salesData["licensee_no"]=='30028400') //csws
			 	{
					$total_CSWS_btls=$total_CSWS_btls+intval($salesData["unit_sales"]);						
					$total_CSWS_cs=$total_CSWS_cs+intval($salesData["total_cs"]);						
	
				}
				else if($salesData["licensee_no"]=='40080400')
				{
					$total_NWT_btls=$total_NWT_btls+intval($salesData["unit_sales"]);
					$total_NWT_cs=$total_NWT_cs+intval($salesData["total_cs"]);
	
				}
				else if($salesData["licensee_no"]=='40081000')
				{
					$total_SASK_btls=$total_SASK_btls+intval($salesData["unit_sales"]);
					$total_SASK_cs=$total_SASK_cs+intval($salesData["total_cs"]);
	
				}
				
				$total_btls =$total_btls+intval($salesData["unit_sales"]);
				$total_cases =$total_cases+intval($salesData["total_cs"]);
				
				
	    		$lastStoreNo = $salesData["licensee_no"];
				 	
				$values = array(
								array("data"=>$salesData["SKUA"],"format"=>$CalibriNormalBorderLeft ), 
								array("data"=>$salesData["product_name"]), 
								array("data"=>$salesData["size"], "format"=>$CalibriNormalBorderNumRight), 
								array("data"=>$salesData["unit_sales"], "format"=>$CalibriNormalBorderNumRight), 
								array("data"=>$salesData["btl_per_cs"], "format"=>$CalibriNormalBorderNumRight), 
								array("data"=>$salesData["total_cs"], "format"=>$CalibriNormalBorderNumRight), 
								array("data"=>$salesData["licensee_no"],"format"=>$CalibriNormalBorderLeft ), 
								array("data"=>$salesData["store_name"]),
								array("data"=>$this->currencyNumber($salesData["price_case"]), "format"=>$CalibriNormalBorderNumRight), 
								array("data"=>$this->currencyNumber($salesData["price_unit"]), "format"=>$CalibriNormalBorderNumRight)); 
				
				
			
	         
	             
	            $this->_writeRow($sp, $values, $row, $CalibriNormalBorder); 
	            $i++;
	            $row++;
	      //  }//if unit sales >0
        }//end while
        
         $row=$row+2;
         
		//Bottles
		
		
		$this->_writeCell($sp, array("data"=>"Bottles"), $row, "D" , $CalibriNormalBorderRedFontLeft);
		
		$this->_writeCell($sp, array("data"=>$total_btls), $row, "E" , $CalibriNormalBorderRedFontRight);
		
		$this->_writeCell($sp, array("data"=>""), $row, "F" , $CalibriNormalBorderRedFontRight);
		
		$this->_writeCell($sp, array("data"=>""), $row, "G" , $CalibriNormalBorderRedFontRight);
        
        $row++;
        $rowBots=$row;

       //Cases
       // add border first
       
        
		$this->_writeCell($sp, array("data"=>"Cases"), $row, "D", $CalibriNormalBorderRedFontLeft);
		$this->_writeCell($sp, array("data"=>""), $row, "E" , $CalibriNormalBorderRedFontRight);
		
		$this->_writeCell($sp, array("data"=>""), $row, "F" , $CalibriNormalBorderRedFontRight);
		
		$this->_writeCell($sp, array("data"=>$total_cases), $row, "G" , $CalibriNormalBorderRedFontRight);
	
        $row++;
		$rowCS=$row;
	                
 		//Samples
 		
 		
		$this->_writeCell($sp, array("data"=>"Sample"), $row, "D", $CalibriNormalBorderRedFontLeft);
		$this->_writeCell($sp, array("data"=>$total_CSWS_btls==0?0:"-".$total_CSWS_btls), $row, "E" , $CalibriNormalBorderRedFontRight);
		
		$this->_writeCell($sp, array("data"=>""), $row, "F" , $CalibriNormalBorderRedFontRight);
		
		$this->_writeCell($sp, array("data"=>$total_CSWS_cs==0?0:"-".$total_CSWS_cs), $row, "G" , $CalibriNormalBorderRedFontRight);
		
		
		$row++;
		$rowCSWS=$row;
					
		//SASK
		$this->_writeCell($sp, array("data"=>"SASK"), $row, "D", $CalibriNormalBorderRedFontLeft);
		$this->_writeCell($sp, array("data"=>$total_SASK_btls==0?0:"-".$total_SASK_btls), $row, "E" , $CalibriNormalBorderRedFontRight);
		
		$this->_writeCell($sp, array("data"=>""), $row, "F" , $CalibriNormalBorderRedFontRight);
		
		$this->_writeCell($sp, array("data"=>$total_SASK_btls==0?0:"-".$total_SASK_btls), $row, "G" , $CalibriNormalBorderRedFontRight);
		
		
		$row++;
		$rowSASK=$row;
		
		//NWT
		$this->_writeCell($sp, array("data"=>"NWT"), $row, "D", $CalibriNormalBorderRedFontLeft);
		$this->_writeCell($sp, array("data"=>$total_NWT_btls==0?0:"-".$total_NWT_btls), $row, "E" , $CalibriNormalBorderRedFontRight);
		
		$this->_writeCell($sp, array("data"=>""), $row, "F" , $CalibriNormalBorderRedFontRight);
		
		$this->_writeCell($sp, array("data"=>$total_NWT_cs==0?0:"-".$total_NWT_cs), $row, "G" , $CalibriNormalBorderRedFontRight);
		
		
		$row++;
		$rowNWT=$row;
					
	                
		//Total
		
		$Total = 
		$this->_writeCell($sp, array("data"=>"Total"), $row, "D", $CalibriNormalBorderRedFontLeft);
		$this->_writeCell($sp, array("data"=>"=(E$rowBots+E$rowCSWS+E$rowSASK+E$rowNWT)"), $row, "E" , $CalibriNormalBorderRedFontRight);
		
		$this->_writeCell($sp, array("data"=>""), $row, "F" , $CalibriNormalBorderRedFontRight);
		
		$rowTotalCS =$rowBots+1;
		$this->_writeCell($sp, array("data"=>"=(G$rowTotalCS+G$rowCSWS+G$rowSASK+G$rowNWT)"), $row, "G" , $CalibriNormalBorderRedYellowBKFontRight);

		$row++;
	                
        

    }
    
    function _writeTitle(& $workbook, & $sp, & $row, $titleText)
    {
        $fm = & $workbook->addFormat(array('fontfamily'=>'Calibri', 'size'=>10, 'bold'=>1, 
                'fgcolor'=>'white', 'bgcolor'=>'black', 'align'=>'left', 'valign'=>'center'));
        $reportTitle = $fm;

        $fm = & $workbook->addFormat(array('fontfamily'=>'Calibri', 'size'=>10, 'bold'=>1, 'underline'=>1));
        $CalibriBoldUnderlined  = $fm;
        
        $fm = & $workbook->addFormat(array('right'=>2));
        $thickRight = $fm;
        
        $titleText1 = $titleText;
       
        
        $this->_writeCell($sp, array("data"=>$titleText1), $row, "A", $reportTitle); 
        $sp->mergeCells($row, $this->columns["A"]["index"], $row, $this->columns["C"]["index"]);
		$row++;
        
        
  
    }
    
    function _writeColumnHeaders(& $workbook, & $sp, & $row)
    {
        $fm = & $workbook->addFormat(array('fontfamily'=>'Calibri', 'size'=>10, 'fgcolor'=>'yellow', 
                'bgcolor'=>'black', 'border'=>1, 'align'=>'center', 'valign'=>'bottom','bold'=>1,
                'top'=>2, 'bottom'=>2));
        //$fm->setTextWrap();
        $columnHeader = $fm;
        
      
        
        $fm = & $workbook->addFormat(array('fontfamily'=>'Calibri', 'size'=>10, 'fgcolor'=>'yellow', 
                'bgcolor'=>'black', 'border'=>1, 'align'=>'left', 'valign'=>'bottom','bold'=>1,
                'top'=>2, 'bottom'=>2));       
        $columnHeaderLeft = $fm;
        
        $fm = & $workbook->addFormat(array('fontfamily'=>'Calibri', 'size'=>10, 'fgcolor'=>'yellow', 
                'bgcolor'=>'black', 'border'=>1, 'align'=>'right', 'valign'=>'bottom','bold'=>1,
                'top'=>2, 'bottom'=>2));       
        $columnHeaderRight = $fm;
        
//        $sp->setRow($row, 20); 
        
       
		$values = array(
                array("data"=>"CSPC #", "format"=>$columnHeaderLeft), 
                array("data"=>"Wine Purchased", "format"=>$columnHeaderLeft), 
              
                array("data"=>"Size", "format"=>$columnHeaderRight), 
                array("data"=>"Total Bot", "format"=>$columnHeaderRight), 
                array("data"=>"Bot Per", "format"=>$columnHeaderRight),
				array("data"=>"Cases", "format"=>$columnHeaderRight),
				array("data"=>"Store #", "format"=>$columnHeaderLeft),
				array("data"=>"Store Name", "format"=>$columnHeaderLeft),
				array("data"=>"$/case", "format"=>$columnHeaderRight),
				array("data"=>"$/unit", "format"=>$columnHeaderRight)
				
				);
		
                        
       
        $this->_writeRow($sp, $values, $row, $columnHeader);  
        $row++;
        
    }
    function _writeBorderToRow($sp,$row,$format)
    {
		$values = array(
			                array("data"=>"", "format"=>$format), 
			                array("data"=>"", "format"=>$format), 
			                array("data"=>"", "format"=>$format), 
			                array("data"=>"", "format"=>$format), 
			                array("data"=>"", "format"=>$format),
							array("data"=>"", "format"=>$format),
							array("data"=>"", "format"=>$format),
							array("data"=>"", "format"=>$format),
							array("data"=>"", "format"=>$format),
							array("data"=>"", "format"=>$format));
							
		$this->_writeRow($sp, $values, $row, $format);  
	}
    
    function _writeCell(& $sp, $value, $row, $col, $format=null)
    {
        return $sp->write($row, $this->columns[$col]["index"], $value["data"], 
            array_key_exists("format",$value)?$value["format"]:$format);
    }
    
    function _writeRow(& $sp, $value, & $row, $format=null)
    {
     
        for ($i = "A", $j=0; $i!="K"; $i++, $j++) 
        {    
            $this->_writeCell(& $sp, $value[$j], $row, $i, $format);
        }
    }

}
?>