<?php

import('Form60.bll.bllStorePenetrationData');
import('Form60.util.excel.writer');

class excelStorePenetrationReport
{
    var $report_month;
    var $report_year;
    var $SPData;
    var $reportData;
    var $columns;
    
    function excelStorePenetrationReport($displayAsWebPage=true,$location_group_id=0)
    {
    	 $location_group_id = $_REQUEST["location_group_id"];
		
	
		if($location_group_id==""|$location_group_id==null)
		{
			$location_group_id = $location_group_id;
		}
		
        $this->columns = array("A"=>array("index"=>0, "width"=>25.86), "B"=>array("index"=>1, "width"=>7.29), 
                "C"=>array("index"=>2, "width"=>6), "D"=>array("index"=>3, "width"=>6), "E"=>array("index"=>4, "width"=>11.57),
                "F"=>array("index"=>5, "width"=>6), "G"=>array("index"=>6, "width"=>6), "H"=>array("index"=>7, "width"=>6),
                "I"=>array("index"=>8, "width"=>11.57),"J"=>array("index"=>9, "width"=>10.29),"K"=>array("index"=>10, "width"=>17.86));
        if ($displayAsWebPage)
        {
            $this->report_month =$_REQUEST["report_month"];
            $this->report_year = $_REQUEST["report_year"];
        
            $this->generateSpreadsheet(false,$location_group_id);
        }
    }
    
    function getReportFile($month, $year)
    {
        $this->report_month =$month;
        $this->report_year = $year;
        return $this->generateSpreadsheet(true);
    }
       
 

	 function generateSpreadsheet($returnFile = false)
   	{
        $filePrefix = "Store Penetration ". F60Date::getMonthTxt($this->report_month).  " " . $this->report_year;
        
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
  
   		$worksheetName = "All regions";
	    $this->SPData = new bllStorePenetrationData();
	    
       	$number_types = 3;
	    if($this->report_year<=2009&&$this->report_month<=11)//before Dec 20009
	    {
			$number_types = 0;
		}
		
		$users =$this->SPData->getBreakDownUsers(0);

      //	for($i=0; $i<=$number_types; $i++)
		$worksheetName = "All region";
      
     		$location_group_id =0;
      		$user_id =0;
      		//	echo $worksheetName;
        	$sp =& $workbook->addWorksheet($worksheetName);
        	
 	      
	        $this->reportData = $this->SPData->getDataForReport($this->report_month, $this->report_year,$location_group_id,$user_id) ;
	        
	
			//set column widths
	        foreach($this->columns as $column)
	        {
	            $sp->setColumn($column["index"], $column["index"], $column["width"]);
	        }
	        
	        $row = 1;
	        
	        $this->_writeTitle($workbook, $sp, $row,$user_id,$worksheetName);
	        
	        foreach($this->reportData as $storeData => $data)
	        {
	            $this->_writeColumnHeaders($workbook, $sp, $row, $storeData);
	           $this->_writeWineData($workbook, $sp, $row, $data);
	        }     
	        
	        
      	foreach($users as $data=>$user)
       	{
         	$location_group_id =$user["group_type_id"];
         	$user_id =$user["user_id"];
         	
			if($location_group_id==1)
			 	$worksheetName = "Okanagan";
			else if($location_group_id==2)
			{
			 	if($user_id==54)
				 	$worksheetName = "Lower Mainland - Sarah Barathan";
				else
				 	$worksheetName = "Lower Mainland - Jillian";
			}
			else if($location_group_id==3)
				$worksheetName = "Island";
      
      
      		//	echo $worksheetName;
        	$sp =& $workbook->addWorksheet($worksheetName);
        	
 	      //    function getDataForReport($month, $year, $group_id =0)

	        $this->reportData = $this->SPData->getDataForReport($this->report_month, $this->report_year,$location_group_id,$user_id) ;
	        
	
			//set column widths
	        foreach($this->columns as $column)
	        {
	            $sp->setColumn($column["index"], $column["index"], $column["width"]);
	        }
	        
	        $row = 1;
	        
	        $this->_writeTitle($workbook, $sp, $row,$user_id,$worksheetName);
	        
	        foreach($this->reportData as $storeData => $data)
	        {
	            $this->_writeColumnHeaders($workbook, $sp, $row, $storeData);
	           $this->_writeWineData($workbook, $sp, $row, $data);
	        }     
	        
	      
	        
	   	
        }

		$workbook->close();
        
        if ($returnFile)
            return $filePath;
	}
    
    function _writeWineData(&$workbook, & $sp, & $row, $wineData)
    {
        $fm = & $workbook->addFormat(array('fontfamily'=>'Calibri', 'size'=>10, 'border'=>1));
        $CalibriNormalBorder = $fm;
        
        $fm = & $workbook->addFormat(array('fontfamily'=>'Calibri', 'size'=>10, 'border'=>1, 'numformat'=>'0.00%'));
        $CalibriNormalBorderPercent = $fm;
        
        $fm = & $workbook->addFormat(array('fontfamily'=>'Calibri', 'size'=>10, 'border'=>1, 'bottom'=>2));
        $CalibriNormalThickBottom = $fm;
        
        $fm = & $workbook->addFormat(array('fontfamily'=>'Calibri', 'size'=>10, 'border'=>1, 'bottom'=>2, 
                'numformat'=>'0.00%'));
        $CalibriNormalThickBottomPercent = $fm;
        
        $fm = & $workbook->addFormat(array('fontfamily'=>'Calibri', 'size'=>10, 'border'=>1, 'right'=>2,
                'numformat'=>'0.00%'));
        $CalibriNormalThickRightPercent = $fm;
        
        $fm = & $workbook->addFormat(array('fontfamily'=>'Calibri', 'size'=>10, 'border'=>1, 'right'=>2,
                'BOTTOM'=>2, 'numformat'=>'0.00%'));
        $CalibriNormalThickRightThickBottomPercent = $fm;
        
        $fm = & $workbook->addFormat(array('fontfamily'=>'Calibri', 'size'=>10, 'numformat'=>'0.00%'));
        $CalibriNormalNoBorderPercent = $fm;
        
        $fm = & $workbook->addFormat(array('fontfamily'=>'Calibri', 'size'=>10, 'bold'=>1));
        $CalibriBold = $fm;
        
        $i=1;
        $startRow= $row + 1; //internally rows are 0 based, in formulas rows are 1 based
        foreach($wineData as $wine)
        {
            $currentRow = $row+1;
            $values = array(array("data"=>ucwords(strtolower(($wine["wine_name"])))), 
                    array("data"=>intval($wine["week1_2_start"])), 
                    array("data"=>intval($wine["week1_2_end"])), 
                    array("data"=>"=SUM(C$currentRow-B$currentRow)"), 
                    array("data"=>"=IF(B$currentRow>0,D$currentRow/B$currentRow,0)", 
                        "format"=>($i==count($wineData))?$CalibriNormalThickBottomPercent:$CalibriNormalBorderPercent), 
                    array("data"=>intval($wine["week3_4_start"])), 
                    array("data"=>intval($wine["week3_4_end"])), 
                    array("data"=>"=SUM(G$currentRow - F$currentRow)"), 
                    array("data"=>"=IF(F$currentRow>0,H$currentRow/F$currentRow, 0)", 
                        "format"=>($i==count($wineData))?$CalibriNormalThickBottomPercent:$CalibriNormalBorderPercent), 
                    array("data"=>"=SUM(H$currentRow+D$currentRow)"), 
                    array("data"=>"=IF(B$currentRow>0,SUM(J$currentRow/B$currentRow), 0)",
                        "format"=>($i==count($wineData))?$CalibriNormalThickRightThickBottomPercent
                        :$CalibriNormalThickRightPercent)); 
                        
            $this->_writeRow($sp, $values, $row, ($i==count($wineData))?$CalibriNormalThickBottom:$CalibriNormalBorder); 
            $i++;
            $row++;
        }
        $endRow=$row;
          $this->_writeCell($sp, array("data"=>"=SUM(B$startRow:B$endRow)"), $row, "B"
                , $CalibriNormalBorder);
                
	        $this->_writeCell($sp, array("data"=>"=SUM(C$startRow:C$endRow)"), $row, "C"
	                , $CalibriNormalBorder);
	                
	        $this->_writeCell($sp, array("data"=>"=SUM(F$startRow:F$endRow)"), $row, "F"
	                , $CalibriNormalBorder);
	                
	        $this->_writeCell($sp, array("data"=>"=SUM(G$startRow:G$endRow)"), $row, "G"
	                , $CalibriNormalBorder);
	        $row++;
	        
        $this->_writeCell($sp, array("data"=>"% Increase in Store Distribution"), $row, "H", $CalibriBold);
        $sp->mergeCells($row, $this->columns["H"]["index"], $row, $this->columns["J"]["index"]);
        $this->_writeCell($sp, array("data"=>"=SUM(J$startRow:J$endRow)/SUM(B$startRow:B$endRow)"), $row, "K"
                , $CalibriNormalNoBorderPercent);
        $row++;
        $row++;
    }
    
    function _writeTitle(& $workbook, & $sp, & $row,$user_id =0,$sheetName)
    {
        $fm = & $workbook->addFormat(array('fontfamily'=>'Calibri', 'size'=>12, 'bold'=>1, 
                'fgcolor'=>'silver', 'bgcolor'=>'black', 'align'=>'center', 'valign'=>'center'));
        $reportTitle = $fm;

        $fm = & $workbook->addFormat(array('fontfamily'=>'Calibri', 'size'=>10, 'bold'=>1, 'underline'=>1));
        $CalibriBoldUnderlined  = $fm;
        
        $fm = & $workbook->addFormat(array('right'=>2));
        $thickRight = $fm;
        
        $titleText1 = "Christopher Stewart Wine & Spirits Inc.";
        $titleText2 = "Store Penetration";
        
        $this->_writeCell($sp, array("data"=>$titleText1), $row, "C", $reportTitle); 
        $sp->mergeCells($row, $this->columns["C"]["index"], $row, $this->columns["I"]["index"]);
        $row++;
        
        $this->_writeCell($sp, array("data"=>$titleText2), $row, "C", $reportTitle); 
        $sp->mergeCells($row, $this->columns["C"]["index"], $row, $this->columns["I"]["index"]);
        
        $sp->setRow($row-1, 18);
        $sp->setRow($row, 18);
        $sp->mergeCells($row-1, $this->columns["K"]["index"], $row, $this->columns["K"]["index"]);
        $sp->insertBitmap($row-1, $this->columns["K"]["index"], "resources/images/cswslogo.bmp", 33, -8, 1,1);
        $row++;
        $row++;
        
         $total = "196";
        
        switch ($user_id)
        {
			case 51: //hunter
				$total ="46" ;
				break;
			case 54: //keri
				$total ="58" ;
				break;
			case 43: //jill
				$total="38";
				break;
			case 44: //jillian
				$total="54";
				break;
				
		}
        
        
        $this->_writeCell($sp, array("data"=>"$sheetName - Generated on: " . date("M d, Y").'  Total stores:'. $total), $row, "A", $CalibriBoldUnderlined); 
        $row++;
        $row++;
        
        //set right border
        for ($i=0; $i<=$row; $i++)
        {
            $this->_writeCell($sp, array("data"=>""), $i, "K", $thickRight); 
        }
    }
    
    function _writeColumnHeaders(& $workbook, & $sp, & $row, $agency)
    {
        $fm = & $workbook->addFormat(array('fontfamily'=>'Calibri', 'size'=>10, 'fgcolor'=>'silver', 
                'bgcolor'=>'black', 'border'=>1, 'align'=>'center', 'valign'=>'bottom',
                'top'=>2, 'bottom'=>2));
        $fm->setTextWrap();
        $columnHeader = $fm;
        
        $fm = & $workbook->addFormat(array('fontfamily'=>'Calibri', 'size'=>10, 'fgcolor'=>'silver', 
                'bgcolor'=>'black', 'border'=>1, 'align'=>'center', 'valign'=>'vjustify',
                'top'=>2, 'bottom'=>2));
        $fm->setTextWrap();
        $columnHeadervJustify = $fm;
        
        $fm = & $workbook->addFormat(array('fontfamily'=>'Calibri', 'size'=>10, 'fgcolor'=>'silver', 
                'bgcolor'=>'black', 'border'=>1, 'align'=>'center', 'valign'=>'bottom',
                'top'=>2, 'bottom'=>2, 'right'=>2));
        $fm->setTextWrap();
        $columnHeadervThickRight = $fm;
        
        $fm = & $workbook->addFormat(array('fontfamily'=>'Calibri', 'size'=>10, 'border'=>1, 'top'=>2, 'bottom'=>2));
        $fm->setTextWrap();
        $columnHeader2ndLine = $fm;
        
        $fm = & $workbook->addFormat(array('fontfamily'=>'Calibri', 'size'=>10, 'border'=>1, 
                'top'=>2, 'bottom'=>2, 'right'=>2));
        $fm->setTextWrap();
        $columnHeader2ndLineRight = $fm;
        
        $emptyCell = array("data"=>"");
        
        $sp->setRow($row, 40); 
        $values = array(array("data"=>$agency), 
                        array("data"=>"Week 1 & 2"), $emptyCell, $emptyCell, 
                        array("data"=>"% Growth/Loss", "format"=>$columnHeadervJustify), 
                        array("data"=>"Week 3 & 4"), $emptyCell, $emptyCell, 
                        array("data"=>"% Growth/Loss", "format"=>$columnHeadervJustify), 
                        array("data"=>"Total Stores"), 
                        array("data"=>"Total % Growth/Loss for the Month", "format"=>$columnHeadervThickRight));
        $this->_writeRow($sp, $values, $row, $columnHeader);  
        $sp->mergeCells($row, $this->columns["B"]["index"], $row, $this->columns["D"]["index"]);
        $sp->mergeCells($row, $this->columns["F"]["index"], $row, $this->columns["H"]["index"]);
        $row++;
        
        $sp->setRow($row, 40); 
        $values = array(array("data"=>""), 
                        array("data"=>"Start"), array("data"=>"End"), array("data"=>"Total New Stores"), 
                        $emptyCell, 
                        array("data"=>"Start"), array("data"=>"End"), array("data"=>"Total New Stores"),
                        $emptyCell, $emptyCell, 
                        array("data"=>"", "format"=>$columnHeader2ndLineRight));
        $this->_writeRow($sp, $values, $row, $columnHeader2ndLine); 
        $row++;
        
    }
    
    function _writeCell(& $sp, $value, $row, $col, $format=null)
    {
        return $sp->write($row, $this->columns[$col]["index"], $value["data"], 
            array_key_exists("format",$value)?$value["format"]:$format);
    }
    
    function _writeRow(& $sp, $value, & $row, $format=null)
    {
        for ($i = "A", $j=0; $i!="L"; $i++, $j++)
        {
            $this->_writeCell(& $sp, $value[$j], $row, $i, $format);
        }
    }

}
?>