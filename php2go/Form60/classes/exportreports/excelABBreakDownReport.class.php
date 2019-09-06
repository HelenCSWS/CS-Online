<?php

import('Form60.bll.bllAbVenderReports');
import('Form60.util.excel.writer');
import('php2go.util.Number');


class excelABBreakDownReport
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
    
    
    function excelABBreakDownReport($report_type=1, $user_id=40,$user_name="",$displayAsWebPage=true)
    {
  		if($_REQUEST["user_id"]!=""&&$_REQUEST["user_id"]!=null)
  		{
 			$this->user_id =$_REQUEST["user_id"];
			$this->user_name =$_REQUEST["user_name"];
			$this->report_type =$_REQUEST["report_type"];
			$this->report_month =$_REQUEST["sale_month"];
			$this->report_year =$_REQUEST["sale_year"];
		}
		else if(($_REQUEST["break_type"]!=""&&$_REQUEST["break_type"]!=null))
		{
			$this->report_type = $_REQUEST["break_type"];
			$this->report_month =$_REQUEST["sale_month"];
			$this->report_year =$_REQUEST["sale_year"];
		}
	
		if($this->report_type ==1)
			$this->columns = array("A"=>array("index"=>0, "width"=>40), "B"=>array("index"=>1, "width"=>22), 
		          				"C"=>array("index"=>2, "width"=>12));
		else
			$this->columns = array("A"=>array("index"=>0, "width"=>40), "B"=>array("index"=>1, "width"=>35), 
		          				"C"=>array("index"=>2, "width"=>10), "D"=>array("index"=>3, "width"=>12));
		
        
       if ($displayAsWebPage)
        {
          $this->generateSpreadsheet();
     
        }  			        
    }
    
    function getReportFile()
    {
     
        return $this->generateSpreadsheet(true);
    }
    
    function generateSpreadsheet($returnFile=false)
    {
  		$sale_year = $this->report_year;
		$sale_month = intval($this->report_month);
		
		$sale_month =F60Date::getMonthTxt($sale_month);
		
		if($this->report_type==1)
	        $this->titleText = "Alberta sales break down - ".$sale_month." ".$sale_year." - by store";
	    else
	        $this->titleText = "Alberta sales break down - ".$sale_month." ".$sale_year." - by wine";
	        
	//	$base_worksheetName = $this->titleText;
        
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
        
        $VenderData = new bllABVenderData();         

        $users = $VenderData->getABUsers($this->report_year, $this->report_month);
        

        	
    	for ($i=0;$i<count($users); $i++)//
		{			
			$user_name =$users[$i]['user_name'];
		
			$c_user_id = $users[$i]["user_id"];
		
			$worksheetName = $user_name;
        	$sp =& $workbook->addWorksheet($worksheetName); 
			
			$this->reportData = $VenderData->getBreakDownReportData($c_user_id, $this->report_year, $this->report_month,$this->report_type);
		  
        //set column widths
	        foreach($this->columns as $column)
	        {
	         
			   $sp->setColumn($column["index"], $column["index"], $column["width"]);
	        
	         }
        $row = 0;
        
        $this->_writeTitle($workbook, $sp, $row,$this->titleText); //$row++
        
        $row++; // blank row
        
    
		$this->_writeColumnHeaders($workbook, $sp, $row);
		
		
        $this->_writeData($workbook, $sp, $row, $this->reportData);
      
      }
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
        
         $fm = & $workbook->addFormat(array('fontfamily'=>'Calibri', 'size'=>10, 'border'=>1, 'fgcolor'=>'yellow','color'=>'red'));
        $CalibriNormalBorderYellowFg = $fm;
 
        
      
		$startRow= $row + 1; //internally rows are 0 based, in formulas rows are 1 based
		
		// print $startRow;
		$nIndex=0;

		while ($salesData=$infoData["sales_data"]->fetch())
		{
//            $currentRow = $row+1;
            
				if($this->report_type == 1)
				{
					$values = array(
								array("data"=>$salesData["store_name"],"format"=>$CalibriNormalBorderLeft ), 
								array("data"=>$salesData["licensee_no"],"format"=>$CalibriNormalBorderLeft ), 
								array("data"=>$salesData["cases"], "format"=>$CalibriNormalBorderNumRight)
								); 
				}
				else
				{
					 		
					$values = array(
							array("data"=>$salesData["estate_name"],"format"=>$CalibriNormalBorderLeft ), 
							array("data"=>$salesData["wine_name"],"format"=>$CalibriNormalBorderLeft ), 
							array("data"=>$salesData["btl_size"], "format"=>$CalibriNormalBorderLeft), 
							array("data"=>$salesData["cases"], "format"=>$CalibriNormalBorderNumRight)								
							); 
				}
				
	            $this->_writeRow($sp, $values, $row, $CalibriNormalBorder); 
	            $row++;
	        
        }//end while
        $endRow = $row;
        
        if($this->report_type==1)
        {
			$this->_writeCell($sp, array("data"=>"=SUM(C$startRow:C$endRow)"), $row, "C"
	                , $CalibriNormalBorderYellowFg);	
		}
		else
		{
			$this->_writeCell($sp, array("data"=>"=SUM(D$startRow:D$endRow)"), $row, "D"
	                , $CalibriNormalBorderYellowFg);	
		}
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
                'bgcolor'=>'black', 'color'=>'red' ,'border'=>1, 'align'=>'center', 'valign'=>'bottom','bold'=>1,
                'top'=>2, 'bottom'=>2));
        //$fm->setTextWrap();
        $columnHeader = $fm;
        
      
      	
        
        $fm = & $workbook->addFormat(array('fontfamily'=>'Calibri', 'size'=>10, 'fgcolor'=>'yellow', 
                'bgcolor'=>'black','color'=>'red' ,'border'=>1, 'align'=>'left', 'valign'=>'bottom','bold'=>1,
                'top'=>2, 'bottom'=>2));       
        $columnHeaderLeft = $fm;
        
        $fm = & $workbook->addFormat(array('fontfamily'=>'Calibri', 'size'=>10, 'fgcolor'=>'yellow', 
                'bgcolor'=>'black','color'=>'red' , 'border'=>1, 'align'=>'right', 'valign'=>'bottom','bold'=>1,
                'top'=>2, 'bottom'=>2));       
        $columnHeaderRight = $fm;
        
//        $sp->setRow($row, 20); 
        
       if($this->report_type==1)
       {
		$values = array(
                array("data"=>"Store name", "format"=>$columnHeaderLeft), 
                array("data"=>"Licensee number", "format"=>$columnHeaderLeft), 
              
                array("data"=>"Total cases", "format"=>$columnHeaderRight)
                
				);
		}
		else
		{
			$values = array(
                array("data"=>"Estate", "format"=>$columnHeaderLeft), 
                array("data"=>"Wine", "format"=>$columnHeaderLeft), 
                array("data"=>"Bottle size", "format"=>$columnHeaderRight), 
                array("data"=>"Cases", "format"=>$columnHeaderRight)
				);
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
     	$indexKey = "E";
     	
     	if($this->report_type ==1)
     	{
     		$indexKey = "D";
     	}
     
        for ($i = "A", $j=0; $i!=$indexKey; $i++, $j++) 
        {    
            $this->_writeCell(& $sp, $value[$j], $row, $i, $format);
        }
    }

}
?>