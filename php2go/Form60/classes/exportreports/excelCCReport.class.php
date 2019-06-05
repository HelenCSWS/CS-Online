<?php

import('Form60.bll.bllf60Reports');
import('Form60.util.excel.writer');
import('Form60.util.F60Common');

class excelCCReport
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
    
    var $isExpiry=false;
       
    function excelCCReport($isExpiry=false)
    {
		$this->estate_id =	$_REQUEST["estate_id"];		
		
		$this->isExpiry	=$isExpiry;
		$returnFile=true;
		
		
		if($_REQUEST["isExpiry"]=='1')
		{
	
			$this->isExpiry	=true;
			$returnFile=false;
		}
	
	
		
		if($this->isExpiry==false)
		{
			$this->columns = array("A"=>array("index"=>0, "width"=>20), "B"=>array("index"=>1, "width"=>13), 
		          					"C"=>array("index"=>2, "width"=>30), "D"=>array("index"=>3, "width"=>15), 
										 "E"=>array("index"=>4, "width"=>15));
	       	
			
			   	$this->generateCCSpreadsheet(false);
	       
		}
		else
		{
		 
			$this->columns = array("A"=>array("index"=>0, "width"=>20), "B"=>array("index"=>1, "width"=>13), 
		          					"C"=>array("index"=>2, "width"=>11), "D"=>array("index"=>3, "width"=>45), 
									"E"=>array("index"=>4, "width"=>40), "F"=>array("index"=>5, "width"=>10), 
		          					"G"=>array("index"=>6, "width"=>12), "H"=>array("index"=>7, "width"=>20),"I"=>array("index"=>8, "width"=>14),
									"J"=>array("index"=>9, "width"=>52));
			
			if(!$returnFile)
				$this->generateExpiryCCSpreadsheet($returnFile);
		}     

   }
    function generateExpiryCCSpreadsheet($returnFile=true)
    {	
     
	    $bllData = new F60ReportsData();
       
    	$this->titleText="Expired Credit Card Information";
        $fileName = $this->titleText. ".xls";
        $filePath = ROOT_PATH . "logs/" . $fileName;
	    	
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

        $users = $bllData->getForm60Users();
        	
    	for ($i=0;$i<count($users); $i++)//
		{			
			$user_name =$users[$i]['user_name'];
		
			$c_user_id = $users[$i]["user_id"];
		
			$worksheetName = $user_name;
			
        	$sp =& $workbook->addWorksheet($worksheetName); 
			
			
			$reportData = $bllData->getCCInfo(true, $c_user_id);
		  
       
	        foreach($this->columns as $column)
	        {
	         
			   $sp->setColumn($column["index"], $column["index"], $column["width"]);
	        
	         }
	        $row = 0;
	        
	        $this->_writeTitle($workbook, $sp, $row,$this->titleText); //$row++
	        
	        $row++; // blank row
	        
	    
			$this->_writeColumnHeaders($workbook, $sp, $row);
			
	        $this->_writeCCData($workbook, $sp, $row, $reportData);
      
      	}
        $workbook->close();
         
        if ($returnFile)
            return $filePath;
	    
     }
    
    function generateCCSpreadsheet($returnFile=true)
    {	

	    $bllData = new F60ReportsData();

        	$this->titleText="Credit Card Information";
        	
        	
        	$reportData = $bllData->getCCInfo(false,null, $this->estate_id);
	        
			 
	        $worksheetName =$this->titleText;	//. F60Date::getMonthTxt($this->report_month). " " . $this->report_year;
	        
	        $fileName = $this->titleText. ".xls";
	        $filePath = ROOT_PATH . "logs/" . $fileName;
	        
	        if ($returnFile)
	            $workbook = new Spreadsheet_Excel_Writer($filePath);
	        else
	        {
	            $workbook = new Spreadsheet_Excel_Writer();
	            $workbook->send($fileName);
	        }
	        
	        //$workbook->setTempDir(ROOT_PATH. "logs/");
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
        
      
			$this->_writeColumnHeaders($workbook, $sp, $row);
			$this->_writeCCData($workbook, $sp, $row, $reportData);
      
        	$result = $workbook->close();
         
	   		if ($returnFile)
            	return $filePath;
    }
    
    function _writeCCData(&$workbook, & $sp, & $row, $infoData)
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
		while ($ccData=$infoData->fetch())
	    {
         	$expiry_date = $ccData["expiry_month"]." / " .$ccData["expiry_year"];
	        $ccNumber=F60Common::deCodeString($ccData["card_number"]);
	         
			if(strlen($ccNumber)<=16)
			{
				$ccNumber_new = substr($ccNumber, 0, 4) . " ";
				if(strlen($ccNumber)==16)	//Visa, MC
					$ccNumber_new .= substr($ccNumber, 4, 4) . " " . substr($ccNumber, 8, 4) . " " . substr($ccNumber, 12);
				else	//Amex
					$ccNumber_new .= substr($ccNumber, 4, 6) . " " . substr($ccNumber, 10);
			} 
			else
				$ccNumber_new = $ccNumber;
				
	         
            $currentRow = $row+1;
            
            if($this->isExpiry)
            {
             	$bllData = new F60ReportsData();
             	$orderInfo = $bllData->getLatestOrderByExpiryCC($ccData["license_number"]);
             	
             	$infoDetail="";
             	if(count($orderInfo)!=0)
             	{
				 	$infoDetail =$orderInfo[0]["invoice_number"]." (".$orderInfo[0]["delivery_date"].") - ".$orderInfo[0]["estate_name"];
				}
				$values = array(
								array("data"=>$ccNumber_new), 
								array("data"=>$expiry_date),
								array("data"=>$ccData["card_type"]),
								array("data"=>$infoDetail),
								array("data"=>$ccData["customer_name"]), 
								array("data"=>$ccData["license_name"]), 
								array("data"=>$ccData["license_number"]), 
								array("data"=>$ccData["contact_name"]), 
								array("data"=>$ccData["contact_number"]), 
								array("data"=>$ccData["address"])
								); 						
			}
			else
			{
				$values = array(
								array("data"=>$ccData["customer_name"]), 
								array("data"=>$ccData["license_number"]), 
								array("data"=>$ccNumber_new, "format"=>$arialNormalBorderNumRight), 
								array("data"=>$ccData["card_type"]),
								array("data"=>$expiry_date)
								); 
			}
							             
            $this->_writeRow($sp, $values, $row, $arialNormalBorder); 
            $i++;
            $row++;
        }
        $endRow=$row;
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
        $sp->mergeCells($row, $this->columns["A"]["index"], $row, $this->columns["I"]["index"]);
        $sp->setRow($row, 30);
        $row++;        
    }
    
    function _writeColumnHeaders(& $workbook, & $sp, & $row)
    {
        $fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'fgcolor'=>'silver', 
                'bgcolor'=>'black', 'border'=>1, 'align'=>'center', 'valign'=>'bottom',
                'top'=>2, 'bottom'=>2));
        //$fm->setTextWrap();
        $columnHeader = $fm;
               
        $fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'fgcolor'=>'silver', 
                'bgcolor'=>'black', 'border'=>1, 'align'=>'left', 'valign'=>'bottom',
                'top'=>2, 'bottom'=>2));       
        $columnHeaderLeft = $fm;
        
        $fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'fgcolor'=>'silver', 
                'bgcolor'=>'black', 'border'=>1, 'align'=>'right', 'valign'=>'bottom',
                'top'=>2, 'bottom'=>2));       
        $columnHeaderRight = $fm;        
          
        $sp->setRow($row, 20); 
               
       	if($this->isExpiry)
       	{
			$values = array(
							array("data"=>"Card#", "format"=>$columnHeaderLeft), 
							array("data"=>"Expiry date", "format"=>$columnHeaderLeft),
							array("data"=>"Card Type", "format"=>$columnHeaderLeft), 							
							array("data"=>"Latest voice not paid", "format"=>$columnHeaderLeft), 
							array("data"=>"Customer", "format"=>$columnHeaderLeft), 
							array("data"=>"Store type", "format"=>$columnHeaderLeft), 
							array("data"=>"Store number", "format"=>$columnHeaderLeft), 							
							array("data"=>"Contact", "format"=>$columnHeaderLeft), 
							array("data"=>"Contact#", "format"=>$columnHeaderLeft), 
							array("data"=>"Address", "format"=>$columnHeaderLeft)
							
							);
		}
		else
		{
			$values = array(
							array("data"=>"Customer", "format"=>$columnHeaderLeft), 
							array("data"=>"Licensee#", "format"=>$columnHeaderRight), 							
							array("data"=>"Card#", "format"=>$columnHeaderRight), 
							array("data"=>"Card Type", "format"=>$columnHeaderLeft), 
							array("data"=>"Expiry date", "format"=>$columnHeaderLeft)							
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
     
     	$nEndCol = "F";
     	if($this->isExpiry)     
     		$nEndCol = "K";	
  
     
        for ($i = "A", $j=0; $i!=$nEndCol; $i++, $j++) 
        {
         
            $this->_writeCell(& $sp, $value[$j], $row, $i, $format);
        }
    }

}
?>