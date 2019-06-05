<?php

import('Form60.bll.bllf60Reports');
import('Form60.util.excel.writer');
import('Form60.base.F60DbUtil');

import('php2go.util.Number');


class excelCaseValueReport
{
   
    var $SPData;
   
    var $columns;
    
    var $province_id;
    
    var $maxIndexColumn="I";
    
    var $login_user_id =0;
    
    
    function excelCaseValueReport()
    {
  	
		$this->columns = array("A"=>array("index"=>0, "width"=>12), "B"=>array("index"=>1, "width"=>55), 
		          				"C"=>array("index"=>2, "width"=>7), "D"=>array("index"=>3, "width"=>8.45), 
								"E"=>array("index"=>4, "width"=>7.15), "F"=>array("index"=>5, "width"=>10),
								"G"=>array("index"=>6, "width"=>10),"H"=>array("index"=>7, "width"=>10),
								"I"=>array("index"=>8, "width"=>10));
        
        $this->login_user_id = $_REQUEST["login_user_id"];
      
        
		$this->generateSpreadsheet();
		
           
    }
        
    function generateSpreadsheet($returnFile=false)
    {  	
			
        $this->titleText = "Case value list";	
        
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
        
        $login_province_id = & F60DbUtil::getProvinceId4LoginUser($this->login_user_id);
        
     
		$bllReport = new F60ReportsData; 
	
		if($login_province_id==0)
		{
			$provinces =2;
			$province_id =1;
		}
		else if(($login_province_id==1))
		{
			$provinces =1;
			$province_id =1;		
		}
		else if(($login_province_id==2))
		{
			$provinces =2;
			$province_id =2;		
		}
		
		for($i=$province_id;$i<=$provinces;$i++)
        {
        	 $province_name=($i==1?"BC":"Alberta");
        	 
        	 
         	 $worksheetName ="Case value for $province_name";
        
		     $sp =& $workbook->addWorksheet($worksheetName);
        
        //set column widths
	        foreach($this->columns as $column)
	        {
	            $sp->setColumn($column["index"], $column["index"], $column["width"]);
	        }
        
	        $row = 0;
	        
	       $this->_writeTitle($workbook, $sp, $row,$this->titleText); //$row++
	        
	        $row++; // blank row
	        
		
		   
      		$report_data=$bllReport->getCaseValueList($i);
      		$this->_writeColumnHeaders($workbook, $sp, $row);
	        $this->_writeData($workbook, $sp, $row,$report_data);
      	}
      	

				

      
        $workbook->close();
         
        if ($returnFile)
            return $filePath;   
    }
    
    function currencyNumber($price)
    {
		return Number::fromDecimalToCurrency($price,"$", ".", ",", 2, "left");
	}
    
    function _writeData(&$workbook, & $sp, & $row, $report_data)
    {
       	$fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'border'=>1));
		$arialNormalBorder = $fm;
		
		$fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'align'=>'right','border'=>1));
		$arialNormalBorderRight = $fm;
		
		$fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'align'=>'right','border'=>1,'numformat'=>'0'));
		$arialNormalBorderNumRight = $fm;
		
		
		$i=1;
		$startRow= $row + 1; //internally rows are 0 based, in formulas rows are 1 based
		$nIndex=0;
		$totalsales=0;
		
	//	print_r($report_data);
		foreach($report_data as $info_date)
	    {
			$product = ($info_date["product"]==1?"Wine":"Beer");
			$product_name =$info_date["product_name"].' ('.$info_date["type"].') '.$info_date["size"];
			$vintage = ($info_date["vintage"]=='0000'?"":$info_date["vintage"]);
			$sku =$info_date["cspc_code"];
			$bottles_per_case =$info_date["bottles_per_case"];
			$i=0;
			
			if($info_date["case_sold"]==0) // Wine case value
			{
			 	$case_value = floatval($info_date["case_value"]);
			 	
			 	$case_sold =1;
			 	
			 	if($case_value < 1)
			 	{
			 	 	if($case_value == 0)
				 	{
						 $case_sold = 1; 
						 $case_value =0;
						  
					}
					else
					{
					 	$case_sold = intval(1/$info_date["case_value"]); 
					 	$case_value =1;
					}
					  
				}
			}
			else
			{
				$case_sold =$info_date["case_sold"];
				$case_value = $info_date["case_value"];
				
			}
	
		
			$value=round(($case_value/$case_sold),2);
			$values = array(
							array("data"=>$product), 
							array("data"=>$product_name),
							array("data"=>$vintage),
							array("data"=>$sku),
							array("data"=>$bottles_per_case),
							
							array("data"=>$case_sold),	
							array("data"=>$case_value),	
							array("data"=>$value)
												
							); 
										             
            $this->_writeRow($sp, $values, $row, $arialNormalBorder); 
            $i++;
            $row++;
            
        }
        $endRow=$row;
	                
        

    }
    
    function _writeTitle(& $workbook, & $sp, & $row, $worksheetName)
    {
		$fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>12, 'bold'=>1,
									'fgcolor'=>'silver', 'bgcolor'=>'black', 'align'=>'center', 'valign'=>'center'));
		$reportTitle = $fm;
		
		$fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'bold'=>1, 'underline'=>1));
		$arialBoldUnderlined  = $fm;
		
		$fm = & $workbook->addFormat(array('right'=>2));
		$thickRight = $fm;
		
		$cswsName = "Christopher Stewart Wine & Spirits Inc.";
		$titleText2 = $this->titleText;
        
        
			$colBeginIndex = "B";
			$colEndIndex = "F";
			$colMergeIndex="P";
			$iconIndex="A";
		
		// add graphice
		$row=1;
  	//    $sp->insertBitmap($row, $this->columns[$iconIndex]["index"], "resources/images/cswslogo.bmp", 30, -8, 1,1);
 	    $sp->insertBitmap($row, $this->columns[$iconIndex]["index"], "resources/images/cswslogo.bmp", 1, 1, 0.8,0.7);
 		
 		$row++;
        $this->_writeCell($sp, array("data"=>$cswsName), $row, $colBeginIndex, $reportTitle); 
        $sp->mergeCells($row, $this->columns[$colBeginIndex]["index"], $row, $this->columns[$colEndIndex]["index"]);
        
        $row++;
        
        $this->_writeCell($sp, array("data"=>$titleText2), $row, $colBeginIndex, $reportTitle); 
        $sp->mergeCells($row, $this->columns[$colBeginIndex]["index"], $row, $this->columns[$colEndIndex]["index"]);
		        
        $sp->setRow($row-1, 18);
        $sp->setRow($row, 18);
        
        $row++;
        $row++;
          
        $this->_writeCell($sp, array("data"=>"$worksheetName - Generated on: " . date("M d, Y")), $row, "A", $arialBoldUnderlined); 
        $row++;
        $row++;
        
        //set right border
      /*  for ($i=0; $i<=$row; $i++)
        {
            $this->_writeCell($sp, array("data"=>""), $i, $colMergeIndex, $thickRight); 
        }*/
    }
    
    function _writeColumnHeaders(& $workbook, & $sp, & $row)
    {
        $fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'fgcolor'=>'silver', 
                'bgcolor'=>'black', 'border'=>1, 'align'=>'center', 'valign'=>'bottom','bold'=>0,
                'top'=>2, 'bottom'=>2));
        //$fm->setTextWrap();
        $columnHeader = $fm;
        
      
        
        $fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'fgcolor'=>'silver', 
                'bgcolor'=>'black', 'border'=>1, 'align'=>'left', 'valign'=>'bottom','bold'=>0,
                'top'=>2, 'bottom'=>2));       
        $columnHeaderLeft = $fm;
        
        $fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'fgcolor'=>'silver', 
                'bgcolor'=>'black', 'border'=>1, 'align'=>'right', 'valign'=>'bottom','bold'=>0,
                'top'=>2, 'bottom'=>2));       
        $columnHeaderRight = $fm;
        
//        $sp->setRow($row, 20); 
        
       
		$values = array(
		                array("data"=>"Product", "format"=>$columnHeaderLeft), 
		                array("data"=>"Product type", "format"=>$columnHeaderLeft), 
		              
		                array("data"=>"Vintage", "format"=>$columnHeaderRight), 
		                array("data"=>"SKU", "format"=>$columnHeaderRight), 
		                array("data"=>"Blts/cs", "format"=>$columnHeaderRight),
						array("data"=>"Case sold", "format"=>$columnHeaderRight),
						array("data"=>"Case value", "format"=>$columnHeaderRight),
						array("data"=>"Value", "format"=>$columnHeaderRight)
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
							array("data"=>"", "format"=>$format)
						);
							
		$this->_writeRow($sp, $values, $row, $format);  
	}
    
    function _writeCell(& $sp, $value, $row, $col, $format=null)
    {
        return $sp->write($row, $this->columns[$col]["index"], $value["data"], 
            array_key_exists("format",$value)?$value["format"]:$format);
    }
    
    function _writeRow(& $sp, $value, & $row, $format=null)
    {
     
        for ($i = "A", $j=0; $i!=$this->maxIndexColumn; $i++, $j++) 
        {    
            $this->_writeCell(& $sp, $value[$j], $row, $i, $format);
        }
    }

}
?>