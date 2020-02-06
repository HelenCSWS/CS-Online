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
    
     var $report_month_text ="";
    
     
    function excelBCInABVenderReport($displayAsWebPage=true,$displayVender=false)
    {
     	if($_REQUEST["estate_id"]!="")
     	{
			$this->estate_id = $_REQUEST["estate_id"];
			$this->report_month = $_REQUEST["sale_month"];
			$this->report_year = $_REQUEST["sale_year"];
		}
	
		$this->displayVender = $displayVender;

 
        if($this->estate_id == 150 )//SpearHead
		{		$this->columns= array("A"=>array("index"=>0, "width"=>29), "B"=>array("index"=>1, "width"=>14), 
  		          				"C"=>array("index"=>2, "width"=>14), "D"=>array("index"=>3, "width"=>14), 
  								"E"=>array("index"=>4, "width"=>14), "F"=>array("index"=>5, "width"=>14),
  								"G"=>array("index"=>6, "width"=>16), "H"=>array("index"=>7, "width"=>45),
                                "I"=>array("index"=>8, "width"=>16));
		};
        
 
        $this->VenderData = new bllABVenderData();
       
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
        
        $this->report_month_text = F60Date::getMonthTxt($report_month);	
     
		$sale_year = $report_year;
		
	//	$sale_month_invent = intval($report_month)+1; // for current month as sales data are always from last month
		$sale_year_invent =$sale_year;
		
		if($sale_year_invent==13)
		{
			$sale_month_invent = 1;
			$sale_year_invent = intval($sale_year)+1;
		}
		
        $sale_month_invent = date('F');
		$sale_month_invent =F60Date::getMonthTxt($sale_month_invent);

        $this->fileName = "SpearHead Winery Alberta Sales Commission and Inventory Report - ".$sale_month." ".$sale_year;
        $this->titleText = "SpearHead Winery Alberta Licensee Sales - ".$sale_month." ".$sale_year;
    
	    $this->reportData = $this->VenderData->getVenderSalesFromDB($report_month, $report_year,$this->estate_id);     
       		
		$worksheetName = "SpearHead AB Sales";
        
        $fileName = $this->fileName. ".xls";
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
        
        
  
	
		$this->_writeColumnHeaders($workbook, $sp, $row,$this->estate_id,1);   //sales header
       	$this->_writeSalesData($workbook, $sp, $row, $this->reportData,$this->estate_id); //sales data
        $row++;
        $row++;  //blank row
        
        $this->titleText = "SpearHead Winery Current Inventory as of ".$sale_month_invent." ".Date(d). " ".$sale_year_invent;
        $this->_writeTitle($workbook, $sp, $row,$this->titleText); 
        	
       	$this->_writeColumnHeaders($workbook, $sp, $row,$this->estate_id,2); //inventory header
  		
 		$this->_writeInventoryData($workbook, $sp, $row,$this->reportData); //inventory header
            
        $row++;
        $row++;
               
        $this->_writeColumnHeaders($workbook, $sp, $row,$this->estate_id,3);//commission header
        
        $this->reportData = $this->VenderData->getBCWineryABSalesSummary($report_month,$report_year,$this->estate_id);
		$this->_writeSalesCommissionData($workbook, $sp, $row,$this->reportData);
          
        $workbook->close();
         
        if ($returnFile)
            return $filePath;
        
    }
    
    function _writeSalesData(&$workbook, & $sp, & $row, $infoData,$estate_id)
    {
        $fm = & $workbook->addFormat(array('fontfamily'=>'Calibri', 'size'=>11, 'border'=>1));
        $CalibriNormalBorder = $fm;
 
        $fm = & $workbook->addFormat(array('fontfamily'=>'Calibri', 'size'=>11, 'align'=>'right','border'=>1));
        $CalibriNormalBorderRight = $fm;
        
        $fm = & $workbook->addFormat(array('fontfamily'=>'Calibri',  'bold'=>1,'color'=>'red','size'=>11, 'align'=>'right','border'=>1));
        $CalibriNormalBorderRedFontRight = $fm;
        
        $fm = & $workbook->addFormat(array('fontfamily'=>'Calibri','bold'=>1,'color'=>'red','fgcolor'=>'yellow', 'size'=>11, 'align'=>'right','border'=>1));
        $CalibriNormalBorderRedYellowBKFontRight = $fm;
        
        $fm = & $workbook->addFormat(array('fontfamily'=>'Calibri', 'size'=>11, 'align'=>'right','border'=>1,'numformat'=>'0'));
        $CalibriNormalBorderNumRight = $fm;
        
        $fm = & $workbook->addFormat(array('fontfamily'=>'Calibri','size'=>11, 'align'=>'left','border'=>1));
        $CalibriNormalBorderLeft = $fm;
        
        $fm = & $workbook->addFormat(array('fontfamily'=>'Calibri','size'=>11, 'bold'=>1,'color'=>'red','border'=>1));
        $CalibriNormalBorderRedFontLeft = $fm;
              
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
			
	
					$values = array(
                    	array("data"=>ucwords(strtolower($salesData["product_name"]))), 
								array("data"=>$salesData["SKUA"],"format"=>$CalibriNormalBorderNumRight ), 
							
								array("data"=>$salesData["size"], "format"=>$CalibriNormalBorderNumRight), 
								array("data"=>$salesData["unit_sales"], "format"=>$CalibriNormalBorderNumRight), 
								array("data"=>$salesData["btl_per_cs"], "format"=>$CalibriNormalBorderNumRight), 
								array("data"=>$salesData["total_cs"], "format"=>$CalibriNormalBorderNumRight), 
								array("data"=>$salesData["licensee_no"],"format"=>$CalibriNormalBorderLeft ), 
								array("data"=>$salesData["store_name"]),
                                array("data"=>$salesData["city"])); 
		//	}
         
             
            $this->_writeRow($sp, $values, $row, $CalibriNormalBorder,true); 
            $i++;
            $row++;
        }
        
		//Bottles
		$this->_writeCell($sp, array("data"=>"Bottles"), $row, "C" , $CalibriNormalBorderRedFontLeft);
		
		$this->_writeCell($sp, array("data"=>$total_btls), $row, "D" , $CalibriNormalBorderRedFontRight);
		
		$this->_writeCell($sp, array("data"=>""), $row, "E" , $CalibriNormalBorderRedFontRight);
		
		$this->_writeCell($sp, array("data"=>""), $row, "F" , $CalibriNormalBorderRedFontRight);
        
        $row++;
        $rowBots=$row;

       //Cases
		$this->_writeCell($sp, array("data"=>"Cases"), $row, "C", $CalibriNormalBorderRedFontLeft);
		$this->_writeCell($sp, array("data"=>""), $row, "D" , $CalibriNormalBorderRedFontRight);
		
		$this->_writeCell($sp, array("data"=>""), $row, "E" , $CalibriNormalBorderRedFontRight);
		
		$this->_writeCell($sp, array("data"=>$total_cases), $row, "F" , $CalibriNormalBorderRedFontRight);
	
    }
    function _writeInventoryData(&$workbook, & $sp, & $row, $infoData)
    {
        $fm = & $workbook->addFormat(array('fontfamily'=>'Calibri', 'size'=>11, 'border'=>1));
        $CalibriNormalBorder = $fm;
 
        $fm = & $workbook->addFormat(array('fontfamily'=>'Calibri', 'size'=>11, 'align'=>'right','border'=>1));
        $CalibriNormalBorderRight = $fm;
        
        $fm = & $workbook->addFormat(array('fontfamily'=>'Calibri',  'bold'=>1,'color'=>'red','size'=>11, 'align'=>'right','border'=>1));
        $CalibriNormalBorderRedFontRight = $fm;
        
        $fm = & $workbook->addFormat(array('fontfamily'=>'Calibri','bold'=>1,'color'=>'red','fgcolor'=>'yellow', 'size'=>11, 'align'=>'right','border'=>1));
        $CalibriNormalBorderRedYellowBKFontRight = $fm;
        
        $fm = & $workbook->addFormat(array('fontfamily'=>'Calibri', 'size'=>11, 'align'=>'right','border'=>1,'numformat'=>'0'));
        $CalibriNormalBorderNumRight = $fm;
        
        $fm = & $workbook->addFormat(array('fontfamily'=>'Calibri','size'=>11, 'align'=>'left','border'=>1));
        $CalibriNormalBorderLeft = $fm;
        
        $fm = & $workbook->addFormat(array('fontfamily'=>'Calibri','size'=>11, 'bold'=>1,'color'=>'red','border'=>1));
        $CalibriNormalBorderRedFontLeft = $fm;
        
      
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
            	           array("data"=>ucwords(strtolower($salesData["wine_name"]))), 
							array("data"=>$salesData["sku"],"format"=>$CalibriNormalBorderNumRight ), 
						
							array("data"=>$salesData["size"], "format"=>$CalibriNormalBorderNumRight), 
							array("data"=>$salesData["units"], "format"=>$CalibriNormalBorderNumRight), 
							array("data"=>$this->currencyNumber($salesData["unit_price"]), "format"=>$CalibriNormalBorderNumRight), 
							array("data"=>$salesData["alloc"], "format"=>$CalibriNormalBorderNumRight), 
							array("data"=>$salesData["av_cs"],"format"=>$CalibriNormalBorderNumRight )); 
		
			
             
            $this->_writeRow($sp, $values, $row, $CalibriNormalBorder,2); 
            $i++;
            $row++;
        }
        
		//Totals
		$this->_writeCell($sp, array("data"=>"Totals"), $row, "A" , $CalibriNormalBorderRedFontRight);
		$this->_writeCell($sp, array("data"=>""), $row, "B" , $CalibriNormalBorderRedFontRight);
		$this->_writeCell($sp, array("data"=>""), $row, "C" , $CalibriNormalBorderRedFontRight);
		$this->_writeCell($sp, array("data"=>""), $row, "D" , $CalibriNormalBorderRedFontRight);
		$this->_writeCell($sp, array("data"=>""), $row, "E" , $CalibriNormalBorderRedFontRight);
        $sp->mergeCells($row, $this->columns["A"]["index"], $row, $this->columns["E"]["index"]);

		$this->_writeCell($sp, array("data"=>$total_allo), $row, "F" , $CalibriNormalBorderRedFontRight);
		
		$this->_writeCell($sp, array("data"=>$total_avcs), $row, "G" , $CalibriNormalBorderRedFontRight);
		
    }
   
   function _writeSalesCommissionData(&$workbook, & $sp, & $row, $infoData)
    {
        $fm = & $workbook->addFormat(array('fontfamily'=>'Calibri', 'size'=>11, 'border'=>1));
        $CalibriNormalBorder = $fm;
 
        $fm = & $workbook->addFormat(array('fontfamily'=>'Calibri', 'size'=>11, 'align'=>'right','border'=>1));
        $CalibriNormalBorderRight = $fm;
        
         
        $fm = & $workbook->addFormat(array('fontfamily'=>'Calibri','size'=>11, 'align'=>'left','bold'=>1,'border'=>1));
        $CalibriBoldlBorderLeft = $fm;
  
          
        $fm = & $workbook->addFormat(array('fontfamily'=>'Calibri','size'=>11, 'align'=>'right','border'=>1));
        $fm ->setNumFormat("$#,##0.00;[Red]-$#,##0.00");
        $CalibriNormalCurrency = $fm;
        
        
         $fgColor='44';
        $fm = & $workbook->addFormat(array('fontfamily'=>'Calibri', 'size'=>11, 'fgcolor'=>$fgColor, 
                'bgcolor'=>'black', 'border'=>1, 'align'=>'left', 'valign'=>'bottom','bold'=>1,
                'top'=>1, 'bottom'=>1));
        //$fm->setTextWrap();
        $columnHeaderBoldBlueLeft = $fm;
        
        $fm = & $workbook->addFormat(array('fontfamily'=>'Calibri', 'size'=>11, 'fgcolor'=>$fgColor, 
                'bgcolor'=>'black', 'border'=>1, 'align'=>'right', 'valign'=>'bottom','bold'=>1,
                'top'=>1, 'bottom'=>1));
        //$fm->setTextWrap();
        
        $columnHeaderBoldBlueRight = $fm;
        
        $fmCurrency=& $workbook->addFormat(array('fontfamily'=>'Calibri', 'size'=>11, 'fgcolor'=>$fgColor, 
                'bgcolor'=>'black', 'border'=>1, 'align'=>'right', 'valign'=>'bottom','bold'=>1,
                'top'=>1, 'bottom'=>1));
                
         $fmCurrency ->setNumFormat("$#,##0.00;[Red]-$#,##0.00");
         $columnHeaderBoldBlueCurrency = $fmCurrency;
         
              
         $fgColor='26';
        $fm = & $workbook->addFormat(array('fontfamily'=>'Calibri', 'size'=>11, 'fgcolor'=>$fgColor, 
                'bgcolor'=>'black', 'border'=>1, 'align'=>'left', 'valign'=>'bottom','bold'=>1,
                'top'=>1, 'bottom'=>1));
        //$fm->setTextWrap();
        $columnHeaderBoldYellowLeft = $fm;
        
        
         $fgColor='1';
        $fm = & $workbook->addFormat(array('fontfamily'=>'Calibri', 'size'=>11, 'fgcolor'=>$fgColor, 
                'bgcolor'=>'black', 'border'=>1, 'align'=>'left', 'valign'=>'bottom','bold'=>1,
                'top'=>1, 'bottom'=>1));
        //$fm->setTextWrap();
        $columnHeaderBoldLeft = $fm;
        
      
        
        
		$i=1;
		$startRow= $row + 1; //internally rows are 0 based, in formulas rows are 1 based
		
		// print $startRow;
		$nIndex=0;
	
		$total_btls =0;
        $total_wholesales =0;
        $total_comm=0;
		
       // print_r($infoData);
		foreach ($infoData as $salesData)
		{
//            $currentRow = $row+1;
			$total_btls =$total_btls+floatval($salesData["unit_sales"]);
            $total_wholesales =$total_wholesales+floatval($salesData["total_wholesale"]);
            $total_comm =$total_comm+floatval($salesData["commission"]);
			
	
			$values = array(
                        array("data"=>ucwords(strtolower($salesData["product"]))), 
						array("data"=>$salesData["cspc_code"],"format"=>$CalibriNormalBorderRight ), 
					
						array("data"=>$salesData["unit_sales"], "format"=>$CalibriNormalBorderRight), 
						array("data"=>$salesData["wholesale"], "format"=>$CalibriNormalCurrency), 
						array("data"=>$salesData["total_wholesale"], "format"=>$CalibriNormalCurrency), 
						array("data"=>$salesData["commission"],"format"=>$CalibriNormalCurrency ) 
					); 
             
            $this->_writeRow($sp, $values, $row, $CalibriNormalBorder,3); 
            $i++;
            $row++;
        }
        
        // Total Summary
        	$values = array(
                            array("data"=>"Total","format"=>$columnHeaderBoldBlueLeft ), 
                            array("data"=>"","format"=>$columnHeaderBoldBlueRight ), 
                            
                            array("data"=>$total_btls, "format"=>$columnHeaderBoldBlueRight), 
                            array("data"=>"", "format"=>$columnHeaderBoldBlueRight), 
                            array("data"=>$total_wholesales, "format"=>$columnHeaderBoldBlueCurrency), 
                            array("data"=>$total_comm, "format"=>$columnHeaderBoldBlueCurrency) 
							 
							); 
               $this->_writeRow($sp, $values, $row, $CalibriNormalBorder,3);     
               $row++;
               $row++;
                        
		//Bottles
        $arrayCommCaptions = array("Commission Rate","Total Commission Amount","GST - 86125 6535 RT00001(0.05)","DEDUCTION","Final Commission" );
        $gst = $total_comm*0.05; $totalFinal = $total_comm + $gst;
        
        $arrayCommSummary =array("15%",$total_comm,$gst,"",$totalFinal);
        
        $values = array(
                            array("data"=>"Commission","format"=>$columnHeaderBoldYellowLeft ), 
                            array("data"=>"","format"=>$columnHeaderBoldYellowLeft ), 
                            
                            array("data"=>"", "format"=>$columnHeaderBoldYellowLeft), 
                            array("data"=>"", "format"=>$columnHeaderBoldYellowLeft), 
                            array("data"=>"", "format"=>$columnHeaderBoldYellowLeft), 
                            array("data"=>"", "format"=>$columnHeaderBoldYellowLeft) 
							 
							); 
               $this->_writeRow($sp, $values, $row, $CalibriNormalBorder,3);  
               $row++;   
               
       // Total Summary
       for($i=0; $i<5; $i++)
       {
             $firstColFormat =  array("data"=>$arrayCommCaptions[$i], "format"=>$CalibriBoldlBorderLeft);
             if($i==3)
             {
               // $firstColFormat =  array("data"=>$arrayCommCaptions[$i], "format"=>$columnHeaderBoldLeftBlueTxt);
             }
             elseif($i==4)
             {
                $firstColFormat =  array("data"=>$arrayCommCaptions[$i], "format"=>$columnHeaderBoldBlueLeft);
             }
             
            
            $secondColFormat =  array("data"=>$arrayCommSummary[$i],"format"=>$CalibriNormalCurrency );
            if($i==0)
                $secondColFormat =  array("data"=>$arrayCommSummary[$i],"format"=>$CalibriNormalBorderRight );
            elseif($i==4)
                $secondColFormat =  array("data"=>$arrayCommSummary[$i],"format"=>$columnHeaderBoldBlueCurrency );
             
            
            
           
        	$values = array(
                            $firstColFormat, 
                            $secondColFormat, 
                            
                            array("data"=>"", "format"=>$CalibriBoldlBorderLeft), 
                            array("data"=>"", "format"=>$CalibriBoldlBorderLeft), 
                            array("data"=>"", "format"=>$CalibriBoldlBorderLeft), 
                            array("data"=>"", "format"=>$CalibriBoldlBorderLeft) 
							 
							); 
               $this->_writeRow($sp, $values, $row, $CalibriNormalBorder,3);  
               $row++;   
        }       
	
        $row++;
    }
    
    function _writeTitle(& $workbook, & $sp, & $row, $titleText)
    {
        $fm = & $workbook->addFormat(array('fontfamily'=>'Calibri', 'size'=>11, 'bold'=>1, 
                'fgcolor'=>'white', 'bgcolor'=>'black', 'align'=>'left', 'valign'=>'center'));
        $reportTitle = $fm;

        $fm = & $workbook->addFormat(array('fontfamily'=>'Calibri', 'size'=>11, 'bold'=>1, 'underline'=>1));
        $CalibriBoldUnderlined  = $fm;
        
        $fm = & $workbook->addFormat(array('right'=>2));
        $thickRight = $fm;
        
        $titleText1 = $titleText;
       
        
        $this->_writeCell($sp, array("data"=>$titleText1), $row, "A", $reportTitle); 
        $sp->mergeCells($row, $this->columns["A"]["index"], $row, $this->columns["E"]["index"]);
		$row++;
        
        
  
    }   
     function _writeColorChart(& $workbook, & $sp, & $row,$estate_id,$chart_type)
    {
        $fgColor='44';
        $fm = & $workbook->addFormat(array('fontfamily'=>'Calibri', 'size'=>11, 'fgcolor'=>$fgColor, 
                'bgcolor'=>'black', 'border'=>1, 'align'=>'center', 'valign'=>'bottom','bold'=>1,
                'top'=>1, 'bottom'=>1));
        //$fm->setTextWrap();
        $columnHeader = $fm;
        
      
        
    	$values = array();
        for($i=1; $i<=100; $i++)
        {
            $fgColor="$i";
            $fm = & $workbook->addFormat(array('fontfamily'=>'Calibri', 'size'=>11, 'fgcolor'=>$fgColor, 
                    'bgcolor'=>'black', 'border'=>1, 'align'=>'center', 'valign'=>'bottom','bold'=>1,
                    'top'=>1, 'bottom'=>1));
            //$fm->setTextWrap();
            $columnHeaderColor = $fm;
            $colorArray = array("data"=>"$i", "format"=>$columnHeaderColor);
            
            array_push($values, $colorArray);
            
            
            if($i%8==0)
            {
              //  print_r($values);
                $this->_writeRow($sp, $values, $row, $columnHeader,1);
                $row++;
                $values = array();
            }
        
      }
      

    //  $this->_writeRow($sp, $values, $row, $columnHeader,1);  
                $row++;
    }
   
    function _writeColumnHeaders(& $workbook, & $sp, & $row,$estate_id,$chart_type)
    {
        $fgColor='26';
        $fm = & $workbook->addFormat(array('fontfamily'=>'Calibri', 'size'=>11, 'fgcolor'=>$fgColor, 
                'bgcolor'=>'black', 'border'=>1, 'align'=>'center', 'valign'=>'bottom','bold'=>1,
                'top'=>1, 'bottom'=>1));
        //$fm->setTextWrap();
        $columnHeader = $fm;
        
        
         
        $fm = & $workbook->addFormat(array('fontfamily'=>'Calibri', 'size'=>11, 'fgcolor'=>"white", 
                'bgcolor'=>'black', 'border'=>1, 'align'=>'left', 'valign'=>'bottom','bold'=>1,
                'top'=>1, 'bottom'=>1));
        //$fm->setTextWrap();
        $columnHeaderWhite = $fm;
        
      
        
        $fm = & $workbook->addFormat(array('fontfamily'=>'Calibri', 'size'=>11, 'fgcolor'=>$fgColor, 
                'bgcolor'=>'black', 'border'=>1, 'align'=>'left', 'valign'=>'bottom','bold'=>1,
                'top'=>1, 'bottom'=>1));       
        $columnHeaderLeft = $fm;
        
        $fm = & $workbook->addFormat(array('fontfamily'=>'Calibri', 'size'=>11, 'fgcolor'=>$fgColor, 
                'bgcolor'=>'black', 'border'=>1, 'align'=>'right', 'valign'=>'bottom','bold'=>1,
                'top'=>1, 'bottom'=>1));       
        $columnHeaderRight = $fm;
        
        $txtColor ='16';
        $fm = & $workbook->addFormat(array('fontfamily'=>'Calibri', 'size'=>11, 'fgcolor'=>$fgColor, 
                'bgcolor'=>'black', 'border'=>1, 'align'=>'right', 'color'=>$txtColor, 'valign'=>'bottom','bold'=>1,
                'top'=>1, 'bottom'=>1));       
        $columnHeaderRightRedTxt = $fm;
        
        
        
        
       if($chart_type==1) // sales
       {
        
				$values = array(
                                array("data"=>"Product", "format"=>$columnHeaderLeft), 
								array("data"=>"CSPC #", "format"=>$columnHeaderRight), 
								
								array("data"=>"Size", "format"=>$columnHeaderRight), 
								array("data"=>"Bottles Sold", "format"=>$columnHeaderRight), 
								array("data"=>"Btls/cs", "format"=>$columnHeaderRight),
								array("data"=>"Cases Sold", "format"=>$columnHeaderRight),
								array("data"=>"Store #", "format"=>$columnHeaderLeft),
								array("data"=>"Store Name", "format"=>$columnHeaderLeft),
                                array("data"=>"City", "format"=>$columnHeaderLeft)
								);
                                //array("data"=>"Price /case", "format"=>$columnHeaderRight)
		
		} 
		elseif($chart_type==2) // inventory
		{
			$values = array(
                            array("data"=>"Product", "format"=>$columnHeaderLeft), 
                            array("data"=>"CSPC #", "format"=>$columnHeaderRight), 
                            
                            array("data"=>"Size", "format"=>$columnHeaderRight), 
                            array("data"=>"Units", "format"=>$columnHeaderRight), 
                            array("data"=>"$/Unit", "format"=>$columnHeaderRight),
                            array("data"=>"Allocated cs", "format"=>$columnHeaderRight),
                            array("data"=>"Available cs", "format"=>$columnHeaderRight));
		}
                 
                 
 	elseif($chart_type==3) // Commission
		{
		  $values = array(
                            array("data"=>"Commission - $this->report_month_text $this->report_year", "format"=>$columnHeader), 
                            array("data"=>"", "format"=>$columnHeader), 
                            
                            array("data"=>"", "format"=>$columnHeader), 
                            array("data"=>"", "format"=>$columnHeader),
                            array("data"=>"", "format"=>$columnHeader),
                            array("data"=>"", "format"=>$columnHeader) );
                            
          //  $commissoinTitle ="Commission - $this->report_month_text $this->report_year";
          //  $this->_writeCell($sp, array("data"=>$commissoinTitle), $row, "A", $columnHeader); 
          
           $this->_writeRow($sp, $values, $row, $columnHeader,$chart_type); 
           
            $sp->mergeCells($row, $this->columns["A"]["index"], $row, $this->columns["F"]["index"]);
            $row++;	
        
            $values = array(
                            array("data"=>"Sales Summary", "format"=>$columnHeaderWhite), 
                            array("data"=>"", "format"=>$columnHeaderWhite), 
                            
                            array("data"=>"", "format"=>$columnHeaderWhite), 
                            array("data"=>"", "format"=>$columnHeaderWhite),
                            array("data"=>"", "format"=>$columnHeaderWhite),
                            array("data"=>"", "format"=>$columnHeaderWhite) );
                            
          //  $commissoinTitle ="Commission - $this->report_month_text $this->report_year";
          //  $this->_writeCell($sp, array("data"=>$commissoinTitle), $row, "A", $columnHeader); 
          
           $this->_writeRow($sp, $values, $row, $columnHeader,$chart_type);
             $sp->mergeCells($row, $this->columns["A"]["index"], $row, $this->columns["F"]["index"]);
           $row++;	
           
        $values = array(
                            array("data"=>"Product", "format"=>$columnHeaderLeft), 
                            array("data"=>"CSPC #", "format"=>$columnHeaderRight), 
                            
                            array("data"=>"Bottles Sold", "format"=>$columnHeaderRight), 
                            array("data"=>"Wholesale $", "format"=>$columnHeaderRight),
                            array("data"=>"Total Amount", "format"=>$columnHeaderRight),
                            array("data"=>"Commission", "format"=>$columnHeaderRight) );                            
		}       
        
     
       
        $this->_writeRow($sp, $values, $row, $columnHeader,$chart_type);  
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
    function _writeRow(& $sp, $value, & $row, $format=null,$chart_type )
    {
     	$endIndex = "J";
  
        if($chart_type==1) // sales
            $endIndex = "J";
        elseif ($chart_type==2)// inventory
            $endIndex = "H";
        elseif ($chart_type==3)// commission
            $endIndex = "G";
           
            
            
        for ($i = "A", $j=0; $i!=$endIndex; $i++, $j++) 
        {    
            $this->_writeCell(& $sp, $value[$j], $row, $i, $format);
        }
    }

}
?>