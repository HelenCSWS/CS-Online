<?php

import('Form60.bll.bllf60Reports');
import('Form60.util.excel.writer');
import('Form60.base.F60DbUtil');

class excelOverdueReport
{
  
    var $reportData;
    var $columns;
    
    var $estate_id;
    var $store_type_id;
    var $overdue_type;
    var $user_id;
    
    var $titleText="";
    var $estate_name;
    
    
    function excelOverdueReport($sendfile=false)
    {
        
        $this->estate_id =$_REQUEST["estate_id"];
        $this->overdue_type =$_REQUEST["overdue_type"];       
        $this->user_id =$_REQUEST["user_id"];
        $store_type_id=$_REQUEST["store_type_id"];
        
		$this->columns = array("A"=>array("index"=>0, "width"=>12.57), "B"=>array("index"=>1, "width"=>11.86), 
	                "C"=>array("index"=>2, "width"=>8), "D"=>array("index"=>3, "width"=>9.30), "E"=>array("index"=>4, "width"=>8.71),
	                "F"=>array("index"=>5, "width"=>53.86), "G"=>array("index"=>6, "width"=>42.30), 
	                "H"=>array("index"=>7, "width"=>5.71),"I"=>array("index"=>8, "width"=>10.43)						 
						 );	
		
	    
        if(!$sendfile)
        $this->generateSpreadsheet($sendfile,$this->estate_id,$this->user_id,$this->overdue_type,$this->store_type_id);
        
    }
    
    function initReport($estate_id=2,$overdue_type=0,$user_id =0,$store_type_id =0)
    {
		$this->estate_id =$estate_id;
		$this->overdue_type=$overdue_type;
		$this->user_id =0;
		$this->store_type_id =0;
	}
    
    function getTitle($estate_id)
    { 
      	$f60dbutil= new F60DbUtil();
    	$this->estate_name = $f60dbutil->getEstateName($estate_id);
	
		
		switch ($this->overdue_type)
		{
			case 0:
				$overdueText =" - Over 31 days ";
				break;
			case 1:
				$overdueText =" - 31 to 60 days ";
				break;
			case 2:
				$overdueText =" - 60 to 90 days ";
				break;
			case 3:
				$overdueText =" - Over 90 days ";
				break;
		}
		
		$titleTxt = "Accounts receivable summary for $this->estate_name $overdueText ";
			
		return $titleTxt;
		
		
	 }
    function getReportFile($month, $year)
    {
        $this->report_month =$month;
        $this->report_year = $year;
        return $this->generateSpreadsheet(true);
    }
          
    function generateSpreadsheet($returnFile=true,$estate_id,$user_id,$overdue_type,$store_type_id)
    {
     


		$this->titleText = $this->getTitle($estate_id);

		
        $worksheetName =$this->titleText;
        
        $fileName = $this->estate_name. ".xls";
        $filePath = ROOT_PATH . "logs/" . $fileName;
              
        if ($returnFile)
        {        
		    $workbook = new Spreadsheet_Excel_Writer($filePath); //send email
        }
        else
        {
            $workbook = new Spreadsheet_Excel_Writer();  //open in browser
            $workbook->send($fileName);
        }
             
        $workbook->setVersion(8);
     
        $bllReport= new F60ReportsData();
               
        if($estate_id >=1)
        	$this->estate_id=$estate_id;
        	
        $users = $bllReport->getOverdueUsers($user_id,$overdue_type, $estate_id, $store_type_id);
	
      	for ($i=0;$i<count($users); $i++)//
		{			
			$user_name =$users[$i]['user_name'];
		
			$c_user_id = $users[$i]["user_id"];
		
			$worksheetName = $user_name;
        	$sp =& $workbook->addWorksheet($worksheetName); 
			$reportData = $bllReport->getOverDueInvoices("overdays", "a","1000",1,$this->store_type_id,$this->overdue_type,$this->estate_id, $c_user_id);
		  
	        foreach($this->columns as $column)
	        {
			   $sp->setColumn($column["index"], $column["index"], $column["width"]);
	        }
	        
	        $row = 0;
	        
	        $this->_writeTitle($workbook, $sp, $row,$this->titleText); //$row++
	        
	        $row++; // blank row
	        
			$this->_writeColumnHeaders($workbook, $sp, $row);
			
 	        $sales = $reportData["invoicData"];

	        $this->_writeData($workbook, $sp, $row, $sales);  
	    }
	      
	    $workbook->close();
        
        if ($returnFile)
        {
            return $filePath;      
        }
    }
    
    function _writeData(&$workbook, & $sp, & $row, $salesData )
    {
        $fm = & $workbook->addFormat(array('fontfamily'=>'Calibri', 'size'=>10, 'border'=>1));
        $CalibriNormalBorder = $fm;
 
        $fm = & $workbook->addFormat(array('fontfamily'=>'Calibri', 'size'=>10, 'border'=>1, 'fgcolor'=>'yellow'));
        $CalibriNormalBorderYellowFg = $fm;
        
        $fm = & $workbook->addFormat(array('fontfamily'=>'Calibri', 'size'=>10, 'border'=>1,'align'=>'right', 'fgcolor'=>'yellow'));
        $CalibriNormalBorderRightYellowFg = $fm;
 
        $fm = & $workbook->addFormat(array('fontfamily'=>'Calibri', 'size'=>10, 'align'=>'right','border'=>1));
        $CalibriNormalBorderRight = $fm;
        
        $fm = & $workbook->addFormat(array('fontfamily'=>'Calibri', 'size'=>10, 'align'=>'right','border'=>1,'bold'=>1, 'fgcolor'=>'yellow'));
        $CalibriNormalBorderBoldRight = $fm;
        
        $fm = & $workbook->addFormat(array('fontfamily'=>'Calibri', 'size'=>10, 'border'=>1, 'numformat'=>'$0.00'));
        $CalibriNormalBorderCurrency = $fm;
        
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
        
       // print $startRow;
        $nIndex=0;
        $totalsales=0;
        $total_btls=0;
		
//		foreach($salesData as $sales)
		while ($sales=$salesData->fetch())
        {
         	
			$address = ($sales["address"]!="")?F60Date::ucwords1($sales["address"]):"N/A";
			if(substr($address,0,1)=="-")
			{
				$address=substr_replace($address,'',0,2);
			}
			
            $currentRow = $row+1;
            
            
			$values = array(array("data"=>($sales["delivery_date"])), 
							array("data"=>($sales["overdays"])), 
							array("data"=>$sales["invoice_number"]), 
							array("data"=>$sales["license_name"]), 
							array("data"=>$sales["licensee_number"]), 
							array("data"=>$sales["customer_name"]), 
							array("data"=>trim($address)), 
						//	array("data"=>$sales["estate_name"]), 
							array("data"=>$sales["cases_sold"]), 
							// array("data"=>$sales["btl_sold"]), 
							array("data"=>Number::fromDecimalToCurrency($sales["total_amount"],"$", ".", ",", 2, "left"),"format"=>$CalibriNormalBorderRight)
			         //array("data"=>$sales["payment_status"]), 
			         //array("data"=>$sales["order_status"])
							
							); 
			
			$this->_writeRow($sp, $values, $row, $CalibriNormalBorder); 
            $row++;
        }
        $endRow=$row;
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
        $sp->mergeCells($row, $this->columns["A"]["index"], $row, $this->columns["H"]["index"]);
        $sp->setRow($row, 30);
        $row++;
        
 
    }
    
    function _writeColumnHeaders(& $workbook, & $sp, & $row)
    {
        $fm = & $workbook->addFormat(array('fontfamily'=>'Calibri', 'size'=>10, 'fgcolor'=>'silver', 
                'bgcolor'=>'black', 'border'=>1, 'align'=>'center', 'valign'=>'bottom',
                'top'=>2, 'bottom'=>2));
     
        $columnHeader = $fm;
        
     
        
        
        $fm = & $workbook->addFormat(array('fontfamily'=>'Calibri', 'size'=>10, 'fgcolor'=>'silver', 
                'bgcolor'=>'black', 'border'=>1, 'align'=>'left', 'valign'=>'bottom',
                'top'=>2, 'bottom'=>2));       
        $columnHeaderLeft = $fm;
        
        $fm = & $workbook->addFormat(array('fontfamily'=>'Calibri', 'size'=>10, 'fgcolor'=>'silver', 
                'bgcolor'=>'black', 'border'=>1, 'align'=>'right', 'valign'=>'bottom',
                'top'=>2, 'bottom'=>2));       
        $columnHeaderRight = $fm;
        
        
          
        $sp->setRow($row, 20); 
        
       
	        $values = array(array("data"=>"Ordered", "format"=>$columnHeaderLeft), 
					        array("data"=>"Overdue days", "format"=>$columnHeaderRight), 
	                        array("data"=>"Invoice#", "format"=>$columnHeaderRight), 
	                        array("data"=>"Store type", "format"=>$columnHeaderLeft), 
	                        array("data"=>"Licensee#", "format"=>$columnHeaderRight), 
	                        array("data"=>"Customer", "format"=>$columnHeaderLeft), 
	                        array("data"=>"Address", "format"=>$columnHeaderLeft), 
	                        array("data"=>"Cases", "format"=>$columnHeaderRight), 
	                        array("data"=>"Total", "format"=>$columnHeaderRight)
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
      $endColum="J";
      

        for ($i = "A", $j=0; $i!=$endColum; $i++, $j++)
        {
         
            $this->_writeCell(& $sp, $value[$j], $row, $i, $format);
        }
    }

}
?>