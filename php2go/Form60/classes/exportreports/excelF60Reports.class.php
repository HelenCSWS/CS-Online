<?php

import('Form60.bll.bllf60Reports');
import('Form60.util.excel.writer');

class excelF60Reports
{
    var $report_month;
    var $report_year;
    var $invData;
    var $reportData;
    var $columns;
    
    var $search_id ="";
    var $user_id=-1;
    var $estate_id = "";
    var $from="";
    var $to="";
    var $store_type_id="";
    var $wine_id="";
  
    
   var $searchAdt="";
   
   var $arryCity="";
   
    function excelF60Reports()
    {
	   $this->search_id = $_REQUEST['searchType'];
       $this->user_id = $_REQUEST['user_id'];
       $this->estate_id = $_REQUEST['estateid'];
       $this->from = $_REQUEST['from'];
       $this->to = $_REQUEST['to'];
       $this->wine_id =$_REQUEST["wine_id"];
       $this->store_type_id =$_REQUEST["store_type_id"];
       $this->searchAdt =$_REQUEST["searchAdt"];
       

       
		if($this->search_id <=4||$this->search_id ==6||$this->search_id==14)
		{
	        if($this->search_id == 1||$this->search_id==14)
	        {
				$this->columns = array("A"=>array("index"=>0, "width"=>11), "B"=>array("index"=>1, "width"=>9), 
                "C"=>array("index"=>2, "width"=>11), "D"=>array("index"=>3, "width"=>9.15), "E"=>array("index"=>4, "width"=>30),
                "F"=>array("index"=>5, "width"=>40),
				"G"=>array("index"=>6, "width"=>26), "H"=>array("index"=>7, "width"=>8),
				"I"=>array("index"=>8, "width"=>6), "J"=>array("index"=>9, "width"=>7),
				"K"=>array("index"=>10, "width"=>12), "L"=>array("index"=>11, "width"=>12),
                "M"=>array("index"=>12, "width"=>13),"N"=>array("index"=>13, "width"=>15),"O"=>array("index"=>14, "width"=>17.86));
			}
			else
			{
	      	$this->columns = array("A"=>array("index"=>0, "width"=>11), "B"=>array("index"=>1, "width"=>9), 
                "C"=>array("index"=>2, "width"=>11), "D"=>array("index"=>3, "width"=>9.15), "E"=>array("index"=>4, "width"=>30),
                "F"=>array("index"=>5, "width"=>40), "G"=>array("index"=>6, "width"=>6), "H"=>array("index"=>7, "width"=>8),
                "I"=>array("index"=>8, "width"=>11.57),"J"=>array("index"=>9, "width"=>10.29),"K"=>array("index"=>10, "width"=>17.86));
         	}
		}
		else if($this->search_id ==5)
		{
	      $this->columns = array("A"=>array("index"=>0, "width"=>44), "B"=>array("index"=>1, "width"=>9), 
                "C"=>array("index"=>2, "width"=>25), "D"=>array("index"=>3, "width"=>20), "E"=>array("index"=>4, "width"=>40),
                "F"=>array("index"=>5, "width"=>9), "G"=>array("index"=>6, "width"=>6));
		}
                
      
        $this->invData = new F60ReportsData();
 
//($search_id, $user_id, $estate_id, $from, $to, $store_type_id="", $wine_id ="")
        $this->reportData = $this->invData->getInvoicesData($this->search_id,$this->user_id, $this->estate_id, $this->from, $this->to, $this->store_type_id,$this->wine_id,$this->searchAdt);
        
        $invoiceDatas = $this->reportData["invoicData"];
        
//        print_r($invoiceDatas);
        
       //($search_id,$estateid,$from,$to)
        $this->titleText=$this->invData->getF60RepTitle($this->search_id,$this->estate_id,$this->from,$this->to);
        
      
        if($this->search_id!=14)
	        $this->generateSpreadsheet($invoiceDatas);
	    else
	    {
   	     //  $arryCity=$this->getCities($invoiceDatas);
   		   $this->generateSpreadsheetCity($invoiceDatas);   
     	}
    }
    
    function getCities($recData)
    {    
     	$city ="";
     	$oldCity="";
     	$i=0;
     	$j=0;
     	$arraCities ="";
		foreach($recData as $sales)
		{
		 	$city=$sales["city"];
			if($i==0)	
			{	
				$arraCities[$j]=$city;
				$oldCity=$city;
			}
			else
			{
				if($city!=$oldCity)	
				{
					$j++;
					$arraCities[$j]=$city;
					$oldCity=$city;
				}
			}
			
			$i++;
		}
		return $arraCities;
	}
    
    function getReportFile($month, $year)
    {
        $this->report_month =$month;
        $this->report_year = $year;
        return $this->generateSpreadsheet(true);
    }
    function generateSpreadsheetCity($invoiceDatas)
    {	
     
	    $returnFile = false;
     		
        $worksheetName =$this->titleText;	//. F60Date::getMonthTxt($this->report_month). " " . $this->report_year;
        
        $fileName = "Invoices". ".xls";
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

        $cities = $this->getCities($invoiceDatas);
    
    	for ($i=0;$i<count($cities); $i++)//
		{			
			$city_name =$cities[$i];
		
		
			$worksheetName = $city_name;
			
        	$sp =& $workbook->addWorksheet($worksheetName); 		  
       
	        foreach($this->columns as $column)
	        {
	         
			   $sp->setColumn($column["index"], $column["index"], $column["width"]);
	        
	         }
	        $row = 0;
	        
	        $this->_writeTitle($workbook, $sp, $row,$this->titleText); //$row++
	        
	        $row++; // blank row
	        
	    
			$this->_writeColumnHeaders($workbook, $sp, $row);
			
	
	        $this->_writeData($workbook, $sp, $row, $invoiceDatas,$city_name);
      
      	}
        $workbook->close();
         
        if ($returnFile)
            return $filePath;
	    
     }
    function generateSpreadsheet( $invoiceDatas )
    {
     	$returnFile = false;
     		
        $worksheetName =$this->titleText;	//. F60Date::getMonthTxt($this->report_month). " " . $this->report_year;
        
        $fileName = "Invoices". ".xls";
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
                
        $this->_writeTitle($workbook, $sp, $row,$this->titleText); //$row++
        
        $row++; // blank row
        
       
		$this->_writeColumnHeaders($workbook, $sp, $row);
		 
		 
     	$this->_writeData($workbook, $sp, $row, $invoiceDatas);
      
        
        $workbook->close();
         
        if ($returnFile)
            return $filePath;
        
    }
    
    function _writeData(&$workbook, & $sp, & $row,  $invoiceDatas,$city_name="" )
    { 
			$this->_writeInvoiceData(&$workbook, & $sp, & $row, $invoiceDatas,$city_name);
			
	}
	
    function _writeInvoiceData(&$workbook, & $sp, & $row, $invoiceDatas,$city_name="")
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
        
	
	
		foreach($invoiceDatas as $sales)
        {
         	
         	if($this->search_id==4)
         	{
         	 	$nIndex=1;
	      
	
	            $currentRow = $row+1;
            
	            $values = array(array("data"=>($sales["wine"])), 
	                      array("data"=>$sales["cspc_code"]), 
	                      array("data"=>$sales["color"]), 
	                      array("data"=>$sales["size"],"format"=>$arialNormalBorderRight), 
	                      array("data"=>$sales["price"],"format"=>$arialNormalBorderRight), 
	                      array("data"=>$sales["unallocated"],"format"=>$arialNormalBorderRight), 
	                      array("data"=>$sales["sample"],"format"=>$arialNormalBorderRight),
							    array("data"=>$sales["breakage_corked"],"format"=>$arialNormalBorderRight), 
	                      array("data"=>$sales["cases"],"format"=>$arialNormalBorderRight), 
	                      array("data"=>$sales["btls"],"format"=>$arialNormalBorderRight),
	                      array("data"=>$sales["bottles"],"format"=>$arialNormalBorderRight)								
								); 
         	}
         	else if($this->search_id==5)
         	{
					$address = ($sales["address"]!="")?F60Date::ucwords1($sales["address"]):"N/A";
	         	if(substr($address,0,1)=="-")
					{
						$address=substr_replace($address,'',0,2);
					}
	         	
					$nIndex=1;
	     
	
	            $currentRow = $row+1;
            
	            $values = array(array("data"=>($sales["customer_name"])), 
	                      array("data"=>$sales["store_type"]), 
	                      array("data"=>$sales["contact_name"]), 
	                      array("data"=>$sales["contact_number"]), 
	                       array("data"=>$address), 
	                      array("data"=>$sales["allocated"],"format"=>$arialNormalBorderRight),
							    array("data"=>$sales["sold"],"format"=>$arialNormalBorderRight)
								); 
			}
			else if($this->search_id==1)//1
			{
					
				$address = ($sales["address"]!="")?F60Date::ucwords1($sales["address"]):"N/A";
				if(substr($address,0,1)=="-")
				{
					$address=substr_replace($address,'',0,2);
				}
	         	
				$nIndex=1;
	      
	
	            $currentRow = $row+1;
            
	            $values = array(array("data"=>($sales["delivery_date"])), 
	                      array("data"=>$sales["invoice_number"]), 
	                      array("data"=>$sales["store_type"]), 
	                      array("data"=>$sales["licensee_number"]), 
	                      array("data"=>$sales["customer_name"]), 
	                      array("data"=>$address), 	                      
	                      array("data"=>$sales["wine_name"]),
	                      array("data"=>$sales["sku"]),
							    array("data"=>$sales["orqt"],"format"=>$arialNormalBorderRight), 
							    array("data"=>$sales["total_cs"],"format"=>$arialNormalBorderRight), 
							    array("data"=>("$".$sales["csws_price"]),"format"=>$arialNormalBorderRight), 
							    array("data"=>("$".$sales["market_price"]),"format"=>$arialNormalBorderRight), 
	                      array("data"=>$sales["isPaid"]), 
	                      array("data"=>$sales["isRecieved"]),
	                      array("data"=>$sales["user_name"])								
								); 
			}
			else if($this->search_id==14)//1
			{
			 
			 	
			 	if(strtolower($city_name)==strtolower($sales["city"]))
			 	{
					$isWrite=true;
					$address = ($sales["address"]!="")?F60Date::ucwords1($sales["address"]):"N/A";
					if(substr($address,0,1)=="-")
					{
						$address=substr_replace($address,'',0,2);
					}
		         	
					$nIndex=1;
		      
		
		            $currentRow = $row+1;
	            
	            	
		            $values = array(array("data"=>($sales["delivery_date"])), 
		                      array("data"=>$sales["invoice_number"]), 
		                      array("data"=>$sales["store_type"]), 
		                      array("data"=>$sales["licensee_number"]), 
		                      array("data"=>$sales["customer_name"]), 
		                      array("data"=>$address), 	                      
		                      array("data"=>$sales["wine_name"]),
		                      array("data"=>$sales["sku"]),
								    array("data"=>$sales["orqt"],"format"=>$arialNormalBorderRight), 
								    array("data"=>$sales["total_cs"],"format"=>$arialNormalBorderRight), 
								    array("data"=>("$".$sales["csws_price"]),"format"=>$arialNormalBorderRight), 
								    array("data"=>("$".$sales["market_price"]),"format"=>$arialNormalBorderRight), 
		                      array("data"=>$sales["isPaid"]), 
		                      array("data"=>$sales["isRecieved"]),
		                      array("data"=>$sales["user_name"])								
									); 
				}
				else
				{
					$isWrite=false;
				}
			}
	        else //23,6,7
			{
					
					$address = ($sales["address"]!="")?F60Date::ucwords1($sales["address"]):"N/A";
	         		if(substr($address,0,1)=="-")
					{
						$address=substr_replace($address,'',0,2);
					}
	         	
					$nIndex=1;
	      
	
	            $currentRow = $row+1;
            
	            $values = array(array("data"=>($sales["delivery_date"])), 
	                      array("data"=>$sales["invoice_number"]), 
	                      array("data"=>$sales["store_type"]), 
	                      array("data"=>$sales["licensee_number"]), 
	                      array("data"=>$sales["customer_name"]), 
	                      array("data"=>$address), 
	                      array("data"=>$sales["total_cs"]),
							    array("data"=>$sales["amount_owned"],"format"=>$arialNormalBorderRight), 
	                      array("data"=>$sales["isPaid"]), 
	                      array("data"=>$sales["isRecieved"]),
	                      array("data"=>$sales["user_name"])								
								); 
			}
			
					
            // $totalsales =$totalsales+$sales["total_amount"];
             //$total_btls = $total_btls+$sales["btl_sold"];
            
			$isWrite=true;
			 
            if($this->search_id==14)
			{ 
			 	if(strtolower($city_name)!=strtolower($sales["city"]))
					$isWrite=false;
			}
             
            if($isWrite)
            {
            	$sp->setRow($row, 15); 
	            $this->_writeRow($sp, $values, $row, $arialNormalBorder); 
    	        $i++;
        	    $row++;
        	}
        }
        if($isWrite)
	        $endRow=$row;
        
      
      	if($this->search_id==1||$this->search_id==14)
      	{
	     	if($isWrite)
				  $this->_writeCell($sp, array("data"=>"=SUM(J$startRow:J$endRow)"), $row, "J", $arialNormalBorderYellowFg);

		}
	    else if($this->search_id==2 || $this->search_id==3 || $this->search_id==6)
      	{
		      $this->_writeCell($sp, array("data"=>"=SUM(G$startRow:G$endRow)"), $row, "G"
	                , $arialNormalBorderYellowFg);

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
        $fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'fgcolor'=>'silver', 
                'bgcolor'=>'black', 'border'=>1, 'align'=>'center', 'valign'=>'bottom',
                'top'=>2, 'bottom'=>2));
     
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
        
        if($this->search_id<=3||$this->search_id==6||$this->search_id==14)
        {
         	if($this->search_id==1||$this->search_id==14)
         	{
					$values = array(array("data"=>"Ordered", "format"=>$columnHeaderLeft), 
		                        array("data"=>"Invoice#", "format"=>$columnHeaderRight), 
		                        array("data"=>"Store type", "format"=>$columnHeaderLeft), 
		                        array("data"=>"Licensee#", "format"=>$columnHeaderRight), 
		                        array("data"=>"Customer", "format"=>$columnHeaderLeft), 
		                        array("data"=>"Address", "format"=>$columnHeaderLeft), 
		                        
		                        array("data"=>"Wine", "format"=>$columnHeaderLeft), 
		                        array("data"=>"SKU", "format"=>$columnHeaderLeft), 
		                        array("data"=>"Bottles", "format"=>$columnHeaderRight), 
		                        array("data"=>"Cases", "format"=>$columnHeaderRight),
										array("data"=>"CSWS price", "format"=>$columnHeaderRight),
										array("data"=>"Market price", "format"=>$columnHeaderRight), 
		                        
		                        array("data"=>"Is Paid", "format"=>$columnHeaderLeft), 
		                        array("data"=>"Delivery Status", "format"=>$columnHeaderLeft), 							
		                        array("data"=>"Assinged to", "format"=>$columnHeaderLeft)							
										);
				}
				else
				{
		        $values = array(array("data"=>"Ordered", "format"=>$columnHeaderLeft), 
		                        array("data"=>"Invoice#", "format"=>$columnHeaderRight), 
		                        array("data"=>"Store type", "format"=>$columnHeaderLeft), 
		                        array("data"=>"Licensee#", "format"=>$columnHeaderRight), 
		                        array("data"=>"Customer", "format"=>$columnHeaderLeft), 
		                        array("data"=>"Address", "format"=>$columnHeaderLeft), 
		                        array("data"=>"Cases", "format"=>$columnHeaderRight),
		                        array("data"=>"Total", "format"=>$columnHeaderRight), 
		                        array("data"=>"Is Paid", "format"=>$columnHeaderLeft), 
		                        array("data"=>"Delivery Status", "format"=>$columnHeaderLeft), 							
		                        array("data"=>"Assinged to", "format"=>$columnHeaderLeft)							
										);
					}
			}
			else if($this->search_id==4)
			{
				$values = array(
                        array("data"=>"Wine", "format"=>$columnHeaderLeft), 
                        array("data"=>"SKU", "format"=>$columnHeaderLeft), 
                        array("data"=>"Color", "format"=>$columnHeaderLeft), 
                        array("data"=>"Size", "format"=>$columnHeaderRight), 
                        array("data"=>"Price", "format"=>$columnHeaderRight), 
                        array("data"=>"Allocated", "format"=>$columnHeaderRight), 
                        array("data"=>"Samples", "format"=>$columnHeaderRight),
                          array("data"=>"Brk/Corked", "format"=>$columnHeaderRight), 
                        array("data"=>"Cases", "format"=>$columnHeaderRight), 
                        array("data"=>"Bottles", "format"=>$columnHeaderRight),
                        array("data"=>"Avaliable bottles", "format"=>$columnHeaderRight)
                        					
								);
			}
			else if($this->search_id==5)
			{
				$values = array(
                        array("data"=>"Customer", "format"=>$columnHeaderLeft), 
                        array("data"=>"Store type", "format"=>$columnHeaderLeft), 
                        array("data"=>"Contact", "format"=>$columnHeaderLeft), 
                        array("data"=>"Phone number", "format"=>$columnHeaderLeft), 
                        array("data"=>"Address", "format"=>$columnHeaderLeft), 
                        array("data"=>"Allocated", "format"=>$columnHeaderRight),
                        array("data"=>"Sold", "format"=>$columnHeaderRight)
                        					
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
     		$maxColumLeter = "A";
     		
     		if($this->search_id<=4||$this->search_id ==6||$this->search_id==14)
     		{
     		 	if($this->search_id==1||$this->search_id==14)
     		 	{
						$maxColumLeter = "P";
				}
				else
				{
					$maxColumLeter = "L";
				}
			}
			else if($this->search_id ==5)
			{
					$maxColumLeter = "H";	
			}
        for ($i = "A", $j=0; $i!=$maxColumLeter; $i++, $j++)
        {
            $this->_writeCell(& $sp, $value[$j], $row, $i, $format);
        }
    }

}
?>