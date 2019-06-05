<?php

import('Form60.bll.bllAbVenderReports');
import('Form60.util.excel.writer');

class excelBCInABVenderReport
{
    var $report_month;
    var $report_year;
    var $SPData;
    var $reportData;
    var $columns_sales;
    
    var $columns_inventory;
    
    var $estate_id;
    var $titleText="";
    var $estate_name="";
    
    var $fileType=1;  //
    
    var $isSales=true;
    
    var $displayVender = false;
    
    
     
    function excelBCInABVenderReport($displayAsWebPage=true,$displayVender=false)
    {
     	if($_REQUEST["estate_id"]!="")
     	{
			$this->estate_id = $_REQUEST["estate_id"];
			$this->report_month = $_REQUEST["sale_month"];
			$this->report_year = $_REQUEST["sale_year"];
		}
	
		$this->displayVender = $displayVender;
	/*	if($this->estate_id == 2 )//Hillside
		{
			$this->columns= array("A"=>array("index"=>0, "width"=>7.72), "B"=>array("index"=>1, "width"=>22), 
		          				"C"=>array("index"=>2, "width"=>8.50), "D"=>array("index"=>3, "width"=>8.45), 
								"E"=>array("index"=>4, "width"=>7.15), "F"=>array("index"=>5, "width"=>6),
								"G"=>array("index"=>6, "width"=>11.30), "H"=>array("index"=>7, "width"=>47.30),
								"I"=>array("index"=>8, "width"=>18.45));
		}*/
		if($this->estate_id == 175 )//C.C Jentsch
		{
			$this->columns= array("A"=>array("index"=>0, "width"=>7.72), "B"=>array("index"=>1, "width"=>40), 
		          				"C"=>array("index"=>2, "width"=>8.50), "D"=>array("index"=>3, "width"=>8.45), 
								"E"=>array("index"=>4, "width"=>7.15), "F"=>array("index"=>5, "width"=>6),
								"G"=>array("index"=>6, "width"=>11.30), "H"=>array("index"=>7, "width"=>47.30),
								"I"=>array("index"=>8, "width"=>18.45));
		}
		else //enotecca use estate_id =96
		{
			$this->estate_id =96;
			$this->columns = array("A"=>array("index"=>0, "width"=>7.72), "B"=>array("index"=>1, "width"=>42), 
		          				"C"=>array("index"=>2, "width"=>8.50), "D"=>array("index"=>3, "width"=>8.45), 
								"E"=>array("index"=>4, "width"=>7.15), "F"=>array("index"=>5, "width"=>6),
								"G"=>array("index"=>6, "width"=>11.30), "H"=>array("index"=>7, "width"=>47.30),
								"I"=>array("index"=>8, "width"=>18.45));
		
		
		}  
 
        $this->VerderData = new bllABVenderData();
	
      
        
       if ($displayAsWebPage)
        {
        	$this->generateSpreadsheet($this->report_month, $this->report_year);
     
        }        
    }
    
    function getReportFile($report_month, $report_year,$estate_id)
    { 
     	$this->estate_id = $estate_id;
        return $this->generateSpreadsheet($report_month, $report_year,true);
    }
    
    function generateSpreadsheet($report_month, $report_year,$returnFile=false)
    {		
       
		$sale_month =F60Date::getMonthTxt($report_month);
		
		$sale_year = $report_year;
		
		$sale_month_invent = intval($report_month)+1; // for current month
		$sale_year_invent =$sale_year;
		
		if($sale_month_invent==13)
		{
			$sale_month_invent = 1;
			$sale_year_invent = intval($sale_year)+1;
		}
	
			
		$sale_month_invent =F60Date::getMonthTxt($sale_month_invent);
	
	/*	if($this->estate_id==2)
		
        	$this->titleText = "Hillside Estate Alberta sales - ".$sale_month." ".$sale_year;
        if($this->estate_id==118)
		
        	$this->titleText = "Rustico Farm and Cellars Alberta sales - ".$sale_month." ".$sale_year;
        if($this->estate_id==126)
		
        	$this->titleText = "Bench 1775 Alberta sales - ".$sale_month." ".$sale_year;*/
        	
        if($this->estate_id==175)
		
        	$this->titleText = "C.C. Jentsch Cellars Alberta sales - ".$sale_month." ".$sale_year;
        	
        else
        	$this->titleText = "Enotecca Winery Alberta sales - ".$sale_month." ".$sale_year;
        	
        if($this->displayVender)
	       	$this->reportData = $this->VerderData->getVenderSalesFromDB($report_month, $report_year,$this->estate_id);     
	    else
	    	$this->reportData = $this->VerderData->getABSalesReportFromDB($report_month, $report_year,$this->estate_id);     
        		
		$worksheetName = $this->titleText;
        
        $fileName = $this->titleText. ".xls";
        $filePath = ROOT_PATH . "salesreports/" . $fileName;
        
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
        
    
		// $totalRecords =$reportData["total_records"];
      
		$this->_writeColumnHeaders($workbook, $sp, $row,$this->estate_id,true);//sales header
       	$this->_writeSalesData($workbook, $sp, $row, $this->reportData,$this->estate_id);//sales data
       	
        $row++;
        $row++;
        
        if($this->displayVender) // only display inventory report when automatically gernate the numbers from liquor connection at the beginning of the month
        {
 
	       //Inverntory report      		
	       	$this->titleText = "Current Inventory as of ".$sale_month_invent." ".Date(d). " ".$sale_year_invent;
	        $this->_writeTitle($workbook, $sp, $row,$this->titleText); //$row++	
			$row++;
			
			$this->_writeColumnHeaders($workbook, $sp, $row,$this->estate_id,false);//inventory header
			$this->_writeInventoryData($workbook, $sp, $row,$this->reportData);//inventory header
		}
	         
        $workbook->close();
         
        if ($returnFile)
            return $filePath;
        
    }
    
    function _writeSalesData(&$workbook, & $sp, & $row, $infoData,$estate_id)
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
		
		while ($salesData=$infoData["sales_details"]->fetch())
		{
//            $currentRow = $row+1;
			$total_btls =$total_btls+intval($salesData["unit_sales"]);
			$total_cases =$total_cases+intval($salesData["total_cs"]);
			
			
  			if($estate_id ==2||$this->displayVender==false)			 	
  			{
				$values = array(
								array("data"=>$salesData["SKUA"],"format"=>$arialNormalBorderLeft ), 
								array("data"=>$salesData["product_name"]), 
								array("data"=>$salesData["size"], "format"=>$arialNormalBorderNumRight), 
								array("data"=>$salesData["unit_sales"], "format"=>$arialNormalBorderNumRight), 
								array("data"=>$salesData["btl_per_cs"], "format"=>$arialNormalBorderNumRight), 
								array("data"=>$salesData["total_cs"], "format"=>$arialNormalBorderNumRight), 
								array("data"=>$salesData["licensee_no"],"format"=>$arialNormalBorderLeft ), 
								array("data"=>$salesData["store_name"]),
								array("data"=>$salesData["city"], "format"=>$arialNormalBorderLeft)); 
			}
			else
			{
					$values = array(
								array("data"=>$salesData["SKUA"],"format"=>$arialNormalBorderLeft ), 
								array("data"=>$salesData["product_name"]), 
								array("data"=>$salesData["size"], "format"=>$arialNormalBorderNumRight), 
								array("data"=>$salesData["unit_sales"], "format"=>$arialNormalBorderNumRight), 
								array("data"=>$salesData["btl_per_cs"], "format"=>$arialNormalBorderNumRight), 
								array("data"=>$salesData["total_cs"], "format"=>$arialNormalBorderNumRight), 
								array("data"=>$salesData["licensee_no"],"format"=>$arialNormalBorderLeft ), 
								array("data"=>$salesData["store_name"]),
								array("data"=>$this->currencyNumber($salesData["price_case"]), "format"=>$arialNormalBorderNumRight)); 
			}
         
             
            $this->_writeRow($sp, $values, $row, $arialNormalBorder,true); 
            $i++;
            $row++;
        }
        
		//Bottles
		$this->_writeCell($sp, array("data"=>"Bottles"), $row, "C" , $arialNormalBorderRedFontLeft);
		
		$this->_writeCell($sp, array("data"=>$total_btls), $row, "D" , $arialNormalBorderRedFontRight);
		
		$this->_writeCell($sp, array("data"=>""), $row, "E" , $arialNormalBorderRedFontRight);
		
		$this->_writeCell($sp, array("data"=>""), $row, "F" , $arialNormalBorderRedFontRight);
        
        $row++;
        $rowBots=$row;

       //Cases
		$this->_writeCell($sp, array("data"=>"Cases"), $row, "C", $arialNormalBorderRedFontLeft);
		$this->_writeCell($sp, array("data"=>""), $row, "D" , $arialNormalBorderRedFontRight);
		
		$this->_writeCell($sp, array("data"=>""), $row, "E" , $arialNormalBorderRedFontRight);
		
		$this->_writeCell($sp, array("data"=>$total_cases), $row, "F" , $arialNormalBorderRedFontRight);
	
    }
    function _writeInventoryData(&$workbook, & $sp, & $row, $infoData)
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
        
      
		$i=1;
		$startRow= $row + 1; //internally rows are 0 based, in formulas rows are 1 based
		
		// print $startRow;
		$nIndex=0;
		$total_allo =0;
		$total_avcs =0;
		
		foreach ($infoData["inventory_data"] as $salesData)
		{
		 	//province_id, estate_id,sku, wine_name, size, units,unit_price,alloc,av_cs	
			$total_allo =$total_allo+intval($salesData["alloc"]);
			$total_avcs =$total_avcs+intval($salesData["av_cs"]);
			
			
  			
			$values = array(
							array("data"=>$salesData["sku"],"format"=>$arialNormalBorderLeft ), 
							array("data"=>$salesData["wine_name"]), 
							array("data"=>$salesData["size"], "format"=>$arialNormalBorderNumRight), 
							array("data"=>$salesData["units"], "format"=>$arialNormalBorderNumRight), 
							array("data"=>$this->currencyNumber($salesData["unit_price"]), "format"=>$arialNormalBorderNumRight), 
							array("data"=>$salesData["alloc"], "format"=>$arialNormalBorderNumRight), 
							array("data"=>$salesData["av_cs"],"format"=>$arialNormalBorderNumRight )); 
		
			
             
            $this->_writeRow($sp, $values, $row, $arialNormalBorder,false); 
            $i++;
            $row++;
        }
        
		//Totals
		$this->_writeCell($sp, array("data"=>"Totals"), $row, "A" , $arialNormalBorderRedFontRight);
		$this->_writeCell($sp, array("data"=>""), $row, "B" , $arialNormalBorderRedFontRight);
		$this->_writeCell($sp, array("data"=>""), $row, "C" , $arialNormalBorderRedFontRight);
		$this->_writeCell($sp, array("data"=>""), $row, "D" , $arialNormalBorderRedFontRight);
		$this->_writeCell($sp, array("data"=>""), $row, "E" , $arialNormalBorderRedFontRight);
        $sp->mergeCells($row, $this->columns["A"]["index"], $row, $this->columns["E"]["index"]);

		$this->_writeCell($sp, array("data"=>$total_allo), $row, "F" , $arialNormalBorderRedFontRight);
		
		$this->_writeCell($sp, array("data"=>$total_avcs), $row, "G" , $arialNormalBorderRedFontRight);
		
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
        $sp->mergeCells($row, $this->columns["A"]["index"], $row, $this->columns["E"]["index"]);
		$row++;
        
        
  
    }   
   
    function _writeColumnHeaders(& $workbook, & $sp, & $row,$estate_id,$isSales=true)
    {
        $fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'fgcolor'=>'yellow', 
                'bgcolor'=>'black', 'border'=>1, 'align'=>'center', 'valign'=>'bottom','bold'=>1,
                'top'=>2, 'bottom'=>2));
        //$fm->setTextWrap();
        $columnHeader = $fm;
        
      
        
        $fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'fgcolor'=>'yellow', 
                'bgcolor'=>'black', 'border'=>1, 'align'=>'left', 'valign'=>'bottom','bold'=>1,
                'top'=>2, 'bottom'=>2));       
        $columnHeaderLeft = $fm;
        
        $fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'fgcolor'=>'yellow', 
                'bgcolor'=>'black', 'border'=>1, 'align'=>'right', 'valign'=>'bottom','bold'=>1,
                'top'=>2, 'bottom'=>2));       
        $columnHeaderRight = $fm;
        
        
       if($isSales)
       {
        	if($estate_id ==2||$this->displayVender==false)
        	{
				$values = array(
								array("data"=>"CSPC #", "format"=>$columnHeaderLeft), 
								array("data"=>"Wine Purchased", "format"=>$columnHeaderLeft), 
								array("data"=>"Size", "format"=>$columnHeaderRight), 
								array("data"=>"Total Bot", "format"=>$columnHeaderRight), 
								array("data"=>"Bot Per", "format"=>$columnHeaderRight),
								array("data"=>"Cases", "format"=>$columnHeaderRight),
								array("data"=>"Store #", "format"=>$columnHeaderLeft),
								array("data"=>"Store Name", "format"=>$columnHeaderLeft),
								array("data"=>"City", "format"=>$columnHeaderLeft));
			}
			else
			{
				$values = array(
								array("data"=>"CSPC #", "format"=>$columnHeaderLeft), 
								array("data"=>"Wine Purchased", "format"=>$columnHeaderLeft), 
								array("data"=>"Size", "format"=>$columnHeaderRight), 
								array("data"=>"Total Bot", "format"=>$columnHeaderRight), 
								array("data"=>"Bot Per", "format"=>$columnHeaderRight),
								array("data"=>"Cases", "format"=>$columnHeaderRight),
								array("data"=>"Store #", "format"=>$columnHeaderLeft),
								array("data"=>"Store Name", "format"=>$columnHeaderLeft),
								array("data"=>"Price /case", "format"=>$columnHeaderRight));
			}
		}
		else
		{
			$values = array(
								array("data"=>"CSPC #", "format"=>$columnHeaderLeft), 
								array("data"=>"DESCRIPTION", "format"=>$columnHeaderLeft), 
								array("data"=>"SIZE", "format"=>$columnHeaderRight), 
								array("data"=>"UNITS", "format"=>$columnHeaderRight), 
								array("data"=>"$/UNIT", "format"=>$columnHeaderRight),
								array("data"=>"ALLO", "format"=>$columnHeaderRight),
								array("data"=>"AV_CS", "format"=>$columnHeaderRight));
		}
                        
       
        $this->_writeRow($sp, $values, $row, $columnHeader,$isSales);  
        $row++;
        
    }
    function currencyNumber($price)
    {
		return Number::fromDecimalToCurrency($price,"$", ".", ",", 2, "left");
	}
	
    function _writeCell(& $sp, $value, $row, $col, $format=null)
    {
        return $sp->write($row, $this->columns[$col]["index"], $value["data"], 
            array_key_exists("format",$value)?$value["format"]:$format);
    }
    
    /*
	  Hillside and Enotecca has same column for sales and inventory report, so only need on tag for knowing if it's for sales or inventory
	*/
    function _writeRow(& $sp, $value, & $row, $format=null,$isSales )
    {
     	$endIndex = "J";
     	if(!$isSales)
     		$endIndex = "H";
     
        for ($i = "A", $j=0; $i!=$endIndex; $i++, $j++) 
        {    
            $this->_writeCell(& $sp, $value[$j], $row, $i, $format);
        }
    }

}
?>