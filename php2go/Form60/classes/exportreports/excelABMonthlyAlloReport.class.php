<?php

import('Form60.bll.bllAbVenderReports');
import('Form60.util.excel.writer');
import('php2go.util.Number');


class excelABMonthlyAlloReport 
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
    
    function excelABMonthlyAlloReport($displayAsWebPage=true)
    {  		
     	if($_REQUEST["sale_year"]!="")
     	{	
		  	$this->display_year =$_REQUEST["sale_year"];
			$this->display_month = $_REQUEST["sale_month"];
			$this->isGetHistoryDate = true;
		}
     	else
     	{
     		$this->display_year = Date(Y);
			$this->display_month = Date(m);
			$this->isGetHistoryDate =false;
		}
		$this->columns = array("A"=>array("index"=>0, "width"=>10.43), "B"=>array("index"=>1, "width"=>30), 
		          			   "C"=>array("index"=>2, "width"=>9), "D"=>array("index"=>3, "width"=>25),
		          			   "E"=>array("index"=>4, "width"=>6.5), "F"=>array("index"=>5, "width"=>12),
		          			   "G"=>array("index"=>6, "width"=>12), "H"=>array("index"=>7, "width"=>8),
		          			   "I"=>array("index"=>8, "width"=>12), "J"=>array("index"=>9, "width"=>12));
		
           
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
	//	if($display_month==12)
	//		$display_year--;
		
		$display_month_txt =F60Date::getMonthTxt($this->display_month);
		
		$this->titleText = "Alberta Allocation report - ".$display_month_txt." ".$this->display_year;
        
		$worksheetName = $this->titleText;
        
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
      //  $workbook->setVersion(8);
        
        $worksheetName = "Not Assgined";
		
 		$VenderData = new bllABVenderData();

        $users = $VenderData->getABUsers();
        	
    	for ($i=0;$i<count($users); $i++)//count($users)
		{			
			$user_name =$users[$i]['user_name'];
			$c_user_id = $users[$i]["user_id"];

		 	$worksheetName = $user_name;
      
        	$sp =& $workbook->addWorksheet($worksheetName);
        	        	 
         	
        	if($this->isGetHistoryDate)// get data from history table
        		$this->reportData = $VenderData->getHisotryAlloReportData($this->display_year, $this->display_month, $c_user_id);
        	else
		        $this->reportData = $VenderData->getAlloReportData($c_user_id);
	
			foreach($this->columns as $column)
	        {
	            $sp->setColumn($column["index"], $column["index"], $column["width"]);
	        }
	        
	        $row = 0;
	        
	        $this->_writeTitle($workbook, $sp, $row,$this->titleText); //$row++
	        
	        $row++; // blank row
	        
			$this->_writeColumnHeaders($workbook, $sp, $row);		
  	        $this->_writeData($workbook, $sp, $row, $this->reportData);	        	              
	//	}

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
 
        
      
		$startRow= $row; //internally rows are 0 based, in formulas rows are 1 based
		
		$lastStoreNo="";
		$store_sales =0;
		$total_store_sales =0;
	

		// print $startRow;
		$nIndex=0;
		$total_sales = 0;
		$total_cases =0;

		$i=1;
		while ($salesData=$infoData["allo_data"]->fetch())
		{

            if($i!=1)
			{			 	 	
				if($salesData["license_no"] !=$lastStoreNo ) // total  sales per store
				{
				 	$this->_writeBorderToRow($sp,$row,$arialNormalBorderRedFontRight);					

					$this->_writeCell($sp, array("data"=>"Total"), $row, "G" , $arialNormalBorderRedFontLeft);	                

	                $endRow = $row;
					$this->_writeCell($sp, array("data"=>"=SUM(H$startRow:H$endRow)"), $row, "H", $arialNormalBorderRedFontRight);	                				                

	                $this->_writeCell($sp, array("data"=>$this->currencyNumber($total_store_sales)), $row, "I", $arialNormalBorderRedFontRight);		                
	                
	        		$total_store_sales =0;
					
					$row =$row+1;
					
		        	$this->_writeBorderToRow($sp,$row,$arialNormalBorderRedFontRight);

					$row =$row+1;
					$startRow = $row;
					
				}					
			}
			$i++;
			//store total
			$lastStoreNo = $salesData["license_no"];
			$store_sales = floatval($salesData["price_per_case"])*floatval($salesData["allo_cases"]);
			$display_s_sales = $this->currencyNumber($store_sales);
			$total_store_sales =$total_store_sales+$store_sales;



			//total 
			$total_sales = $total_sales + floatval($salesData["price_per_case"])*floatval($salesData["allo_cases"]);
			$total_cases = $total_cases + floatval($salesData["allo_cases"]);

			$values = array(
							array("data"=>$salesData["license_no"],"format"=>$arialNormalBorderLeft ), 
							array("data"=>$salesData["customer_name"],"format"=>$arialNormalBorderLeft ), 
							array("data"=>$salesData["cspc_code"], "format"=>$arialNormalBorderNumRight), 
							array("data"=>$salesData["wine_name"], "format"=>$arialNormalBorderLeft),				
							array("data"=>$salesData["size"], "format"=>$arialNormalBorderNumRight),				
							array("data"=>$this->currencyNumber($salesData["price_per_unit"]), "format"=>$arialNormalBorderNumRight),	
							array("data"=>$this->currencyNumber($salesData["price_per_case"]), "format"=>$arialNormalBorderNumRight),										
										
							array("data"=>$salesData["allo_cases"], "format"=>$arialNormalBorderNumRight),				
							array("data"=>$display_s_sales, "format"=>$arialNormalBorderNumRight),	
							array("data"=>$salesData["format_date"],"format"=>$arialNormalBorderLeft ) 																																											); 
			
				
	            $this->_writeRow($sp, $values, $row, $arialNormalBorder); 
	            $row++;
	        
        }//end while
         
        //Add last store
        $endRow = $row;
        $this->_writeCell($sp, array("data"=>"=SUM(H$startRow:H$endRow)"), $row, "H"
	                , $arialNormalBorderRedFontRight);
	                
	    $this->_writeCell($sp, array("data"=>$this->currencyNumber($total_store_sales)), $row, "I"
	                , $arialNormalBorderRedFontRight);	
	                
		//FINAL TOTAL
        $row++;
         
        
		$this->_writeCell($sp, array("data"=>"Total"), $row, "G"
	                , $arialNormalBorderYellowFg);	
	                
	    //cases
  		$this->_writeCell($sp, array("data"=>$total_cases), $row, "H"
	                , $arialNormalBorderYellowFg);	
		
		//sales
   		$this->_writeCell($sp, array("data"=>$this->currencyNumber($total_sales)), $row, "I"
	                , $arialNormalBorderYellowFg);	

		
		$row++;
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
        $sp->mergeCells($row, $this->columns["A"]["index"], $row, $this->columns["C"]["index"]);
		$row++;
    }
    
    function _writeColumnHeaders(& $workbook, & $sp, & $row)
    {
        $fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'fgcolor'=>'yellow', 
                'bgcolor'=>'black', 'color'=>'red' ,'border'=>1, 'align'=>'center', 'valign'=>'bottom','bold'=>1,
                'top'=>2, 'bottom'=>2));
        //$fm->setTextWrap();
        $columnHeader = $fm;
        
        $fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'fgcolor'=>'yellow', 
                'bgcolor'=>'black','color'=>'red' ,'border'=>1, 'align'=>'left', 'valign'=>'bottom','bold'=>1,
                'top'=>2, 'bottom'=>2));       
        $columnHeaderLeft = $fm;
        
        $fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'fgcolor'=>'yellow', 
                'bgcolor'=>'black','color'=>'red' , 'border'=>1, 'align'=>'right', 'valign'=>'bottom','bold'=>1,
                'top'=>2, 'bottom'=>2));       
        $columnHeaderRight = $fm;
        
//        $sp->setRow($row, 20); 
        
    	$values = array(
		             	array("data"=>"Licensee#", "format"=>$columnHeaderLeft), 
		                array("data"=>"Store", "format"=>$columnHeaderLeft), 
		               	array("data"=>"CSPC", "format"=>$columnHeaderRight), 
		                array("data"=>"Description", "format"=>$columnHeaderLeft),
		                array("data"=>"Size", "format"=>$columnHeaderRight),
		                array("data"=>"$/Unit", "format"=>$columnHeaderRight),
		                array("data"=>"$/Case", "format"=>$columnHeaderRight),
		                array("data"=>"Alloc", "format"=>$columnHeaderRight),
		                array("data"=>"Total $$$", "format"=>$columnHeaderRight),
		                array("data"=>"Alloc date", "format"=>$columnHeaderLeft)
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