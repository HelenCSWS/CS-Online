<?php

import('Form60.bll.bllsupplierData');
import('Form60.util.excel.writer');

class excelDSWRReport
{
   
    var $invData;
   
   
   
    var $report_month;
    var $report_year;
    var $SPData;
    var $reportData;
    var $columns;
    
    var $estate_id;
    var $titleText="";
    var $estate_name="";
    
    var $fileType=1;  //
    
  	var $date1;
	var	$date2;
	var	$dateType;
    
   
    function excelDSWRReport()
    {
	  
		$this->estate_id = $_REQUEST['estate_id'];
		
		$this->date1 =$_REQUEST["date1"];
		$this->date2 =$_REQUEST["date2"];
		$this->dateType=$_REQUEST["dateType"];
         
  
        //						var sURL = "main.php?page_name=excelDSWRReport&estate_id=" + estate_id + "&date1=" + date1 + "&date2=" + date2 + "&dateType=" + dateType;

				$this->columns = array("A"=>array("index"=>0, "width"=>13), "B"=>array("index"=>1, "width"=>15), 
                "C"=>array("index"=>2, "width"=>15), "D"=>array("index"=>3, "width"=>23), "E"=>array("index"=>4, "width"=>21),
                "F"=>array("index"=>5, "width"=>16),
				"G"=>array("index"=>6, "width"=>14), "H"=>array("index"=>7, "width"=>15),
				"I"=>array("index"=>8, "width"=>10), "J"=>array("index"=>9, "width"=>8),
				"K"=>array("index"=>10, "width"=>8), "L"=>array("index"=>11, "width"=>16),
                "M"=>array("index"=>12, "width"=>17),"N"=>array("index"=>13, "width"=>19));
      
     //   $this->invData = new F60ReportsData();
        
//($search_id, $user_id, $estate_id, $from, $to, $store_type_id="", $wine_id ="")
       // $this->reportData = $this->invData->getInvoicesData($this->search_id,$this->user_id, $this->estate_id, $this->from, $this->to, $this->store_type_id,$this->wine_id,$this->searchAdt);
        
      //  $invoiceDatas = $this->reportData["invoicData"];
        
//        print_r($invoiceDatas);
        
       //($search_id,$estateid,$from,$to)
        
      
         
        
        
       
       // $this->titleText=$this->getTitle($this->estate_id,$dateType,$date1,$date2,$this->province_id);
        $this->generateSpreadsheet();
    }
    
 
    
    function getReportFile($month, $year)
    {
        $this->report_month =$month;
        $this->report_year = $year;
        return $this->generateSpreadsheet(true);
    }
 
    function generateSpreadsheet()
    {
     	$returnFile = false;
     		
        $worksheetName ="estate_name";	//. F60Date::getMonthTxt($this->report_month). " " . $this->report_year;
        
        $fileName = "DSWR". ".xls";
        $filePath = ROOT_PATH . "logs/" . $fileName;
        
        if ($returnFile)
            $workbook = new Spreadsheet_Excel_Writer($filePath);
        else
        {
            $workbook = new Spreadsheet_Excel_Writer();
            $workbook->send($fileName);
        }
                    
        $workbook->setVersion(8);
        $sp =& $workbook->addWorksheet($fileName);
        
        //set column widths
        foreach($this->columns as $column)
        {
            $sp->setColumn($column["index"], $column["index"], $column["width"]);
        }
        
        $row = 0;
                 
      
		$this->_writeColumnHeaders($workbook, $sp, $row);
		 
		 
     	$this->_writeData($workbook, $sp, $row, $this->estate_id);
      
        
        $workbook->close();
         
        if ($returnFile)
            return $filePath;
        
    }
    
   
	
    function _writeData(&$workbook, & $sp, & $row,$estate_id)
    {
	     $isWrite=true;
        $fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'border'=>1));
        $arialNormalBorder = $fm;
 
    
        $fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'align'=>'right','border'=>1));
        $arialNormalBorderRight = $fm;
        
        $fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'align'=>'right','border'=>1,'bold'=>1, 'fgcolor'=>'yellow'));
        $arialNormalBorderBoldRight = $fm;
        
        $fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'border'=>1, 'fgcolor'=>'yellow'));
        $arialNormalBorderYellowFg = $fm;
 
 
        
		  $i=1;
        $startRow= $row + 1; //internally rows are 0 based, in formulas rows are 1 based
        
       // print $startRow;
        $nIndex=0;
        $totalsales=0;
        $total_btls=0;
        
	
		$SPData = new suppliersData();

        $reportData = $SPData->getDSWRData($estate_id, $this->date1, $this->date2,$this->dateType);
        
       // print_r($reportData);
       $nindex =0;
	//	foreach($reportData as $sales)
		while ($sales=$reportData->fetch())
        {
      
				$store_type = $sales["store_type"];
				
				if($sales["lkup_store_type_id"] ==2)
				{
					$store_type = $sales["sub_type"];
				}
				
				$delivery_date = str_replace("-","/",$sales["delivery_date"]);
	      
	            $values = array(array("data"=>($sales["estate_number"])), 
	                      array("data"=>"SALE"), 
	                      array("data"=>$delivery_date), 
	                      array("data"=>$sales["invoice_number"],"format"=>$arialNormalBorderRight), 
	                      array("data"=>"","format"=>$arialNormalBorderRight), 
	                      array("data"=>$sales["licensee_number"],"format"=>$arialNormalBorderRight), 
	                      array("data"=>$store_type),
							    array("data"=>"","format"=>$arialNormalBorderRight), 
	                      array("data"=>$sales["sku"],"format"=>$arialNormalBorderRight), 
	                      array("data"=>$sales["orqt"],"format"=>$arialNormalBorderRight),
	                      array("data"=>$sales["price"],"format"=>$arialNormalBorderRight),							
	                      array("data"=>$sales["deposit"],"format"=>$arialNormalBorderRight),
	                       array("data"=>$sales["total_amount"],"format"=>$arialNormalBorderRight),
	                        array("data"=>"","format"=>$arialNormalBorderRight)
								); 
								
				     $this->_writeRow($sp, $values, $row, $arialNormalBorder); 
    	      
        	  $row++;
        	
        	    
         	}
         
            
      
 
    }
    
    function _writeTitle(& $workbook, & $sp, & $row)
    {
         $fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'bold'=>1, 
                'fgcolor'=>'white', 'bgcolor'=>'black', 'align'=>'left', 'valign'=>'center'));
        $reportTitle = $fm;

        $fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'bold'=>1, 'underline'=>1));
        $arialBoldUnderlined  = $fm;
        
        $fm = & $workbook->addFormat(array('right'=>2));
        $thickRight = $fm;
        
        $titleText1 = $this->titleText;
       
        
        $this->_writeCell($sp, array("data"=>$titleText1), $row, "A", $reportTitle); 
        $sp->mergeCells($row, $this->columns["A"]["index"], $row, $this->columns["I"]["index"]);
        $sp->setRow($row, 30);
        $row++;
    }
    
    function _writeColumnHeaders(& $workbook, & $sp, & $row)
    {
        $fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'fgcolor'=>'white', 
                'bgcolor'=>'black', 'border'=>1, 'align'=>'center', 'valign'=>'bottom',
                'top'=>1, 'bottom'=>1));
     
        $columnHeader = $fm;
        
     
        
        
        $fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'fgcolor'=>'white', 
                'bgcolor'=>'black', 'border'=>1, 'align'=>'left', 'valign'=>'bottom',
                'top'=>1, 'bottom'=>1));       
        $columnHeaderLeft = $fm;
        
        $fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'fgcolor'=>'white', 
                'bgcolor'=>'black', 'border'=>1, 'align'=>'right', 'valign'=>'bottom',
                'top'=>1, 'bottom'=>1));       
        $columnHeaderRight = $fm;
        
        
          
        $sp->setRow($row, 20); 
        
        
					$values = array(array("data"=>"Store_Number", "format"=>$columnHeaderLeft), 
		                        array("data"=>"Transaction_Type", "format"=>$columnHeaderRight), 
		                        array("data"=>"Transaction_Date", "format"=>$columnHeaderLeft), 
		                        array("data"=>"Invoice_Reference_Number", "format"=>$columnHeaderRight), 
		                        array("data"=>"Original_Invoice_Number", "format"=>$columnHeaderLeft), 
		                        array("data"=>"Customer_Number", "format"=>$columnHeaderLeft), 
		                        
		                        array("data"=>"Customer_Type", "format"=>$columnHeaderLeft), 
		                        array("data"=>"Payment_Method", "format"=>$columnHeaderLeft), 
		                        array("data"=>"SKU", "format"=>$columnHeaderRight), 
		                        array("data"=>"Quantity", "format"=>$columnHeaderRight),
										array("data"=>"Price", "format"=>$columnHeaderRight),
										array("data"=>"Container_Deposit", "format"=>$columnHeaderRight), 
		                        
		                        array("data"=>"Total_Doc_Amount", "format"=>$columnHeaderLeft), 
		                        array("data"=>"Return_Reason_Code", "format"=>$columnHeaderLeft)					
										);
			                               
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
     		$maxColumLeter = "O";
     		
     	
        for ($i = "A", $j=0; $i!=$maxColumLeter; $i++, $j++)
        {
            $this->_writeCell(& $sp, $value[$j], $row, $i, $format);
        }
    }

}
?>