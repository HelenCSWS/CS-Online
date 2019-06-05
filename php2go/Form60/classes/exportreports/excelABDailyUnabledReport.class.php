<?php

import('Form60.bll.bllf60Reports');
import('Form60.util.excel.writer');
import('Form60.bll.bllABReports');
import('Form60.util.F60Common');

import('Form60.base.F60DbUtil');


class excelABDailyUnabledRepor
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
       
    function excelABDailyUnabledRepor()
    {
     		
	
		$this->columns = array("A"=>array("index"=>0, "width"=>8), "B"=>array("index"=>1, "width"=>40), 
		        					"C"=>array("index"=>2, "width"=>6), "D"=>array("index"=>3, "width"=>6), 
										  "E"=>array("index"=>4, "width"=>13), "F"=>array("index"=>5, "width"=>40),
										  "G"=>array("index"=>6, "width"=>13),"H"=>array("index"=>7, "width"=>5),
										  "I"=>array("index"=>8, "width"=>9),"J"=>array("index"=>9, "width"=>15),
										  "K"=>array("index"=>10, "width"=>20)
										  ); // first static title names 6 cols	
										  
			
   }   
   
  
  
	
    function generateReportSheet($returnFile=true)
    {			    

		
			$reportData = new ABReportData();
			
	      
   			
   			
   		  	$fileName = "Daily unable to ship report";
   			 
			
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
  				
				$worksheetName ="Unable to ship";
			    
		        $sp =& $workbook->addWorksheet($worksheetName);
		        
		          //set column widths	        
		        foreach($this->columns as $column)
		        {
		            $sp->setColumn($column["index"], $column["index"], $column["width"]);
		        }
		        
		        
		        $row = 0;
		        
		        $this->_writeTitle($workbook, $sp, $row); //$row++
		        
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
	


   
	
	function _writeData(&$workbook, & $sp, & $row)
    {
       	$fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'border'=>1));
		$arialNormalBorder = $fm;
		
		
		$fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'border'=>1,'color'=>'red'));
		$arialNormalBorderRed = $fm;
		
		$fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'border'=>1,'color'=>'blue'));
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
		
		$reportData = new ABReportData();
		//$user_id,$province_id,$sale_month,$sale_year
		$basicInfos = $reportData->getDailyUnabledInfo();
	//	print_r($report_data);
	

		 $i=0;
		 
	
		 
		 $cellFormat = $arialNormalBorder;
		 $cellCurrencyFormat = $arialNormalCurrency;
		 
		foreach($basicInfos as $info_date)
		{
		 	//sku, product, size, units, licensee_no,store,city,qty,unit_price,unable_date,user_name
		 	
			$sku =$info_date["SKU"];
			$product= $info_date["product"];
			$size = $info_date["size"];
			$units = $info_date["units"];
			$city = $info_date["city"];
			$licensee_no = $info_date["licensee_no"];
			$store = $info_date["store"];
			$city = $info_date["city"];
			$qty = $info_date["qty"];
			$unit_price = $info_date["unit_price"];
			$unable_date = $info_date["unable_date"];
			$user_name = $info_date["user_name"];
	
			$user_nameformat =$cellFormat;
			if($user_name == "New Customer")
				$user_nameformat= $arialNormalBorderRed;
	
			$values = array(
							array("data"=>$sku), 
							array("data"=>$product), 
							array("data"=>$size), 
							array("data"=>$units), 
							array("data"=>$licensee_no), 
							array("data"=>$store), 
							array("data"=>$city), 
							array("data"=>$qty), 
							array("data"=>$unit_price,"format"=>$cellCurrencyFormat),
							array("data"=>$unable_date),
							array("data"=>$user_name,"format"=>$user_nameformat)
						
												
							); 
			             
        
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
        $row=1;
        $sp->setRow($row, 60); 
        $sp->insertBitmap($row, $this->columns["F"]["index"], "resources/images/cswslogo.bmp", 33, -8, 1,1);

$row=2;
//$row+=5;

//row 4
      /*  $this->_writeCell($sp, array("data"=>$this->titleText), $row, "A", $reportTitle); 
        $sp->mergeCells($row, $this->columns["A"]["index"], $row, $this->columns["K"]["index"]);
        $row++;
        */
     
    }
    function _writeColumnHeaders(& $workbook, & $sp, & $row)
    {
        //$fgColor="silver";
        $fgColor="green";
        $fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'fgcolor'=>$fgColor, 
                'bgcolor'=>'black', 'border'=>0, 'align'=>'center', 'valign'=>'bottom','bold'=>1,
                'top'=>1, 'bottom'=>1));
        //$fm->setTextWrap();
        $columnHeader = $fm;
        
      
        
        $fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'fgcolor'=>$fgColor, 
                'bgcolor'=>'black', 'border'=>0, 'align'=>'left', 'valign'=>'bottom','bold'=>1,
                'top'=>1, 'bottom'=>1));       
        $columnHeaderLeft = $fm;
        
        
        
        $fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'fgcolor'=>$fgColor, 
                'bgcolor'=>'black', 'border'=>0, 'align'=>'right', 'valign'=>'bottom','bold'=>1,
                'top'=>1, 'bottom'=>1));       
        $columnHeaderRight = $fm;
        
//        $sp->setRow($row, 16); 
        
       

		
			$values = array(
							array("data"=>"CSPC", "format"=>$columnHeaderRight), 
							array("data"=>"Product", "format"=>$columnHeaderLeft),
							array("data"=>"Size", "format"=>$columnHeaderRight), 
							array("data"=>"Unit", "format"=>$columnHeaderRight), 
							array("data"=>"Licence#", "format"=>$columnHeaderRight),
							array("data"=>"Customer", "format"=>$columnHeaderLeft),
							array("data"=>"City", "format"=>$columnHeaderLeft),
							array("data"=>"QTY", "format"=>$columnHeaderRight),
							array("data"=>"$/Unit", "format"=>$columnHeaderRight),
							array("data"=>"Date", "format"=>$columnHeaderLeft),
								array("data"=>"Sales Consultant", "format"=>$columnHeaderLeft)
	
							);	

		
                        
       
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