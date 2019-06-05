<?php

import('Form60.bll.bllAbVenderReports');
import('Form60.util.excel.writer');
import('php2go.util.Number');


class excelABStorePenReport 
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
	var	$display_year;
	var	$display_month;
	
	var $isGetHistoryDate=false;
    
    function excelABStorePenReport($displayAsWebPage=true)
    {  		
     	if($_REQUEST["sale_year"]!="")
     	{	
		  	$this->display_year =$_REQUEST["sale_year"];
			$this->display_month = $_REQUEST["sale_month"];
		}
     	else
     	{
     		$this->display_year = Date(Y);
			$this->display_month = Date(m);
		}
		$this->columns = array("A"=>array("index"=>0, "width"=>10.43), "B"=>array("index"=>1, "width"=>44.71), 
		          			   "C"=>array("index"=>2, "width"=>17.86));
		
           
       if ($displayAsWebPage)
        {
          $this->generateSpreadsheet();
        }  			        
    }
    
    function getReportFile()
    {
     
        return $this->generateSpreadsheet(true);
    }
    
    function generateSpreadsheet($returnFile = false)
   	{
		$filePrefix="Store Penetration report: ". F60Date::getMonthTxt($this->display_month). " " . $this->display_year;
        
        $titletext ="Alberta: ". F60Date::getMonthTxt($this->display_month). " " . $this->display_year;
		$fileName = $filePrefix . ".xls";
        $filePath = ROOT_PATH . "logs/" . $fileName;

		if ($returnFile)
            $workbook = new Spreadsheet_Excel_Writer($filePath);
        else
        {
            $workbook = new Spreadsheet_Excel_Writer();
            $workbook->send($fileName);
        }
        $workbook->setVersion(8);
  
   		$worksheetName = "Alberta store penetration report";
	    $this->SPData = new bllABVenderData();
	    
	    
         
    	$sp =& $workbook->addWorksheet($worksheetName);
    	
    //	$this->setDataToSheet($workbook,$sp,$location_group_id);
    	        
        $this->reportData = $this->SPData->getABStorePenReport($this->display_year, $this->display_month) ;

		//set column widths
        foreach($this->columns as $column)
        {
            $sp->setColumn($column["index"], $column["index"], $column["width"]);
        }
        
        $row = 1;
        
        $this->_writeTitle($workbook, $sp, $row,$titletext);
        
        $this->_writeColumnHeaders($workbook, $sp, $row);
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
        $fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'border'=>1));
        $arialNormalBorder = $fm;
 
        $fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'align'=>'right','border'=>1));
        $arialNormalBorderRight = $fm;
        
        $fm = & $workbook->addFormat(array('fontfamily'=>'Arial',  'bold'=>1,'color'=>'red','size'=>10, 'align'=>'right','border'=>1));
        $arialNormalBorderRedFontRight = $fm;
        
        $fm = & $workbook->addFormat(array('fontfamily'=>'Arial','bold'=>1,'color'=>'red','fgcolor'=>'yellow', 'size'=>10, 'align'=>'right','border'=>1));
        $arialNormalBorderRedYellowBKFontRight = $fm;
        
        $fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'align'=>'right','border'=>1,'numformat'=>'0'));
        $arialNormalBorderNumRight = $fm;
        
        $fm = & $workbook->addFormat(array('fontfamily'=>'Arial','size'=>10, 'align'=>'left','border'=>1));
        $arialNormalBorderLeft = $fm;
        
        $fm = & $workbook->addFormat(array('fontfamily'=>'Arial','size'=>10, 'bold'=>1,'color'=>'red','border'=>1));
        $arialNormalBorderRedFontLeft = $fm;
        
         $fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'border'=>1, 'fgcolor'=>'yellow','color'=>'red'));
        $arialNormalBorderYellowFg = $fm;
 
        
      
		$startRow= $row; //first row
		
		$lastStoreNo="";
		$store_sales =0;
		$total_store_sales =0;
	

		// print $startRow;
		$nIndex=0;
		$total_sales = 0;
		$total_cases =0;

		$i=1;
		while ($salesData=$infoData["sales_data"]->fetch())
		{

          

			$values = array(
							array("data"=>$salesData["skua"],"format"=>$arialNormalBorderLeft ), 
							array("data"=>$salesData["product_name"],"format"=>$arialNormalBorderLeft ), 
							array("data"=>$salesData["store_number"], "format"=>$arialNormalBorderNumRight)																																									); 
			
				
	            $this->_writeRow($sp, $values, $row, $arialNormalBorder); 
	            $row++;
	        
        }//end while
        $endRow = $row; //last row
         
		$this->_writeCell($sp, array("data"=>"=SUM(C$startRow:C$endRow)"), $row, "C"
	                , $arialNormalBorderYellowFg);	
		$row++;
    }
    
    function _writeTitle(& $workbook, & $sp, & $row, $titleText)
    {
     	$fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>12, 'bold'=>1,
									'fgcolor'=>'silver', 'bgcolor'=>'black', 'align'=>'center', 'valign'=>'center'));
		$reportTitle = $fm;
		
		$fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'bold'=>1, 'underline'=>1));
		$arialBoldUnderlined  = $fm;
		
		$fm = & $workbook->addFormat(array('right'=>2));
		$thickRight = $fm;
		
		$titleText1 = "Christopher Stewart Wine & Spirits Inc.";
		$titleText2 = "Store Penetration";
        
        
	
			$colBeginIndex = "A";
			$colEndIndex = "C";
			$colMergeIndex="C";
			$iconIndex="C";
	
		
        $this->_writeCell($sp, array("data"=>$titleText1), $row, $colBeginIndex, $reportTitle); 
        $sp->mergeCells($row, $this->columns[$colBeginIndex]["index"], $row, $this->columns[$colEndIndex]["index"]);
        
        $row++;
        
        $this->_writeCell($sp, array("data"=>$titleText2), $row, $colBeginIndex, $reportTitle); 
        $sp->mergeCells($row, $this->columns[$colBeginIndex]["index"], $row, $this->columns[$colEndIndex]["index"]);
		        
        $sp->setRow($row-1, 18);
        $sp->setRow($row, 18);
        
        $sp->insertBitmap($row-1, $this->columns[$iconIndex]["index"], "resources/images/cswslogo.bmp", 33, -8, 1,1);
        $row++;
        $row++;
        
        $this->_writeCell($sp, array("data"=>$titleText), $row, "A", $arialBoldUnderlined); 
        $row++;
        $row++;
        
        //set right border
        for ($i=0; $i<=$row; $i++)
        {
            $this->_writeCell($sp, array("data"=>""), $i, $colMergeIndex, $thickRight); 
        }
        
        
     /*   $fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'bold'=>1, 
                'fgcolor'=>'white', 'bgcolor'=>'black', 'align'=>'left', 'valign'=>'center'));
        $reportTitle = $fm;

        $fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'bold'=>1, 'underline'=>1));
        $arialBoldUnderlined  = $fm;
        
        $fm = & $workbook->addFormat(array('right'=>2));
        $thickRight = $fm;
        
        $titleText1 = $titleText;
       
        
        $this->_writeCell($sp, array("data"=>$titleText1), $row, "A", $reportTitle); 
        $sp->mergeCells($row, $this->columns["A"]["index"], $row, $this->columns["C"]["index"]);
		$row++;*/
        
        
  
    }
    
    function _writeColumnHeaders(& $workbook, & $sp, & $row)
    {
        $fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'fgcolor'=>'silver', 
                'bgcolor'=>'black', 'color'=>'black' ,'border'=>1, 'align'=>'center', 'valign'=>'bottom','bold'=>0,
                'top'=>2, 'bottom'=>2));
        //$fm->setTextWrap();
        $columnHeader = $fm;
        
        $fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'fgcolor'=>'silver', 
                'bgcolor'=>'black','color'=>'black' ,'border'=>1, 'align'=>'center', 'valign'=>'bottom','bold'=>0,
                'top'=>2, 'bottom'=>2));       
        $columnHeaderLeft = $fm;
        
        $fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'fgcolor'=>'silver', 
                'bgcolor'=>'black','color'=>'black' , 'border'=>1, 'align'=>'center', 'valign'=>'bottom','bold'=>0,
                'top'=>2, 'bottom'=>2));       
        $columnHeaderRight = $fm;
        
       $sp->setRow($row, 40); 
        
    	$values = array(
		             	array("data"=>"CSPC", "format"=>$columnHeaderLeft), 
		                array("data"=>"Wines", "format"=>$columnHeaderLeft), 
		               	array("data"=>"Total Stores", "format"=>$columnHeaderRight) 		                
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
        for ($i = "A", $j=0; $i!="D"; $i++, $j++) 
        {    
            $this->_writeCell(& $sp, $value[$j], $row, $i, $format);
        }
    }

}
?>