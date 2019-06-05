<?php

import('Form60.bll.bllCSReports');
import('Form60.util.excel.writer');

class excelCSSalesSummary 
{
   
    var $invData;
    var $reportData;
    var $columns;
    
  
    var $estate_id = "";
    var $year="";
    var $month="";
  
  	var $maxColumLeter ="A";
    
    var $start_date="";
    var $end_date="";
   
	function excelCSSalesSummary ()
	{
	      
        $this->estate_id = $_REQUEST['estate_id'];
        $this->start_date = $_REQUEST['start_date'];
        $this->end_date = $_REQUEST['end_date'];
        
        
        $this->columns = array("A"=>array("index"=>0, "width"=>10), 
                                "B"=>array("index"=>1, "width"=>19), 
                                "C"=>array("index"=>2, "width"=>11), 
                                "D"=>array("index"=>3, "width"=>35), 
                                "E"=>array("index"=>4, "width"=>10), 
                                "F"=>array("index"=>5, "width"=>15),
                                "G"=>array("index"=>6, "width"=>9),  
                                "H"=>array("index"=>7, "width"=>11),
                                "I"=>array("index"=>8, "width"=>13),
                                "J"=>array("index"=>9, "width"=>13),
                                "K"=>array("index"=>10, "width"=>10),
                                "L"=>array("index"=>11, "width"=>10),
                                "M"=>array("index"=>12, "width"=>13),
                                "N"=>array("index"=>13, "width"=>13),
                                "O"=>array("index"=>14, "width"=>13)
						  ); // first static title names 14 cols	
      			
		$this->maxColumLeter = "P";        
        $this->titleText=$this->getReportTitle($this->start_date, $this->end_date);                
        $this->generateSpreadsheet();
	  
    }
    function ordinal($num)
    {
    // Special case "teenth"
    if ( ($num / 10) % 10 != 1 )
    {
        // Handle 1st, 2nd, 3rd
        switch( $num % 10 )
        {
            case 1: return $num . 'st';
            case 2: return $num . 'nd';
            case 3: return $num . 'rd';  
        }
    }
    // Everything else is "nth"
    return $num . 'th';
    }    
    function getReportTitle($start_date,$end_date)
    { //yy-mm-d
    
        $startDates=explode("-",$start_date);
        
        $monthName = date("F", mktime(0, 0, 0, $startDates[1], 10));
    
       // $dayName =date("S", mktime(0, 0, 0, 0, (int)$startDates[2], 0));
        
        $dayName=$this->ordinal((int)$startDates[2]);
        $sDate = "$monthName $dayName $startDates[0]";
        
  		$endDates=explode("-",$end_date);
        
        $monthName = date("F", mktime(0, 0, 0, $endDates[1], 10));
         $dayName=$this->ordinal((int)$endDates[2]);

        $endDate = "$monthName $dayName $endDates[0]";
//		'C.C. Jentsch Cellars Sales Summary report for March 2016								'
		$title= " Sales Summary report from $sDate to $endDate";
		return $title;
	}
      
    function generateSpreadsheet()
    {
     	$returnFile = false;
     		
        $worksheetName ="Sales Summary";	//. F60Date::getMonthTxt($this->report_month). " " . $this->report_year;
        
        $fileName = "CS Sales Summary Report". ".xls";
        $filePath = ROOT_PATH . "logs/" . $fileName;
        
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
      //  $sp->freezePanes(1);
        
        $row++; // blank row
		$this->_writeColumnHeaders($workbook, $sp, $row);
        $sp->freezePanes(2);
       
        $rpData = new F60CSReportsData();
        
        $invoiceDatas = $rpData->getCSSalesTotal($this->estate_id,$this->start_date,$this->end_date);
        
     	$this->_writeData($workbook, $sp, $row, $invoiceDatas);        
        $workbook->close();
         
        if ($returnFile)
            return $filePath;
        
    }
    
    function _writeData(&$workbook, & $sp, & $row,  $invoiceDatas )
    { 
        
	     $isWrite=true;
         	$fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'border'=>1));
		$arialNormalBorder = $fm;
        
        $isWrite=true;
         	$fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'border'=>0));
		$arialNormalNoBorder = $fm;
        
		
		$fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'border'=>1,'color'=>'red'));
		$arialNormalBorderRed = $fm;
		$fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'border'=>1, 'bold'=>1, 'color'=>'red'));
        
		$arialNormalBorderRedBold = $fm;
        
        $fgcolor="26"; //yellow
        $fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'border'=>1, 'bold'=>1, 'color'=>'red','fgcolor'=>$fgcolor));
        
		$arialNormalBorderRedBoldYellowBG = $fm;
        
		
		$fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'bold'=>1,'border'=>1,'color'=>'blue'));
		$arialNormalBorderBlue = $fm;
        
        $fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'border'=>1,'bold'=>1,'color'=>'blue'));
		$arialNormalBorderBlueBold = $fm;
		
		$fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'align'=>'right','border'=>1));
		$arialNormalBorderRight = $fm;
		
		$fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'align'=>'right','border'=>1,'numformat'=>'0'));
		$arialNormalBorderNumRight = $fm;
		
		$fm = & $workbook->addFormat(array('fontfamily'=>'Arial','size'=>9, 'align'=>'right','border'=>1));
        $fm ->setNumFormat("$#,##0.00;[Red]-$#,##0.00");
        $arialNormalCurrency = $fm;
        
        $fm = & $workbook->addFormat(array('fontfamily'=>'Arial','size'=>9, 'align'=>'right','bold'=>1,'border'=>1,'color'=>'red'));
        $fm ->setNumFormat("$#,##0.00;[Red]-$#,##0.00");
        $arialNormalCurrencyRed = $fm;
        
        $fm = & $workbook->addFormat(array('fontfamily'=>'Arial','size'=>9, 'align'=>'right','bold'=>1,'border'=>1,'color'=>'red','fgcolor'=>$fgcolor));
        $fm ->setNumFormat("$#,##0.00;[Red]-$#,##0.00");
        $arialNormalCurrencyRedYellowBG = $fm;
        
        
        $fm = & $workbook->addFormat(array('fontfamily'=>'Arial','size'=>9, 'align'=>'right','bold'=>1,'border'=>1,'color'=>'blue'));
        $fm ->setNumFormat("$#,##0.00;[Red]-$#,##0.00");
        $arialNormalCurrencyBlue = $fm;
 //   print_r($invoiceDatas);
        $lastPro ="";
        
        $proTotalW=0;
        $proTotalBM=0;
        $proTotalBM_Stopper=0;
        $proTotalBT=0;
        $proTotalSB=0;
        $proTotal=0;
        $proTotalCM=0;
        $proTotalPF=0;
        $proCost =0 ;
        
        $gTotalW=0;
        $gTotalBM=0;
        $gTotalBM_Stopper=0;       
        $gTotalBT=0;
        $gTotalSB=0;
        $gProTotal=0;
        $gProCost=0;
        $gProCM=0;
        $gProTotalPF=0;
           
         $last_user = "";
        $i=0;
       
        $rpData = new F60CSReportsData();
        
		foreach($invoiceDatas as $info_date)
        {
   		   	$province      =$info_date["short_name"];
			$user_name   = $info_date["user_name"];
			$invoiceNo   = $info_date["invoice_number"];
			$store       = $info_date["customer_name"];
			$storeNo     = $info_date["licensee_number"];
            $payType     = $info_date["payType"];
			$qty         = $info_date["units_sold"];
			
            $commission  = $info_date["bonus"];
            $cost        = $info_date["cost"];
            $profit      = $info_date["profit"];
            
            $estate_id   = $info_date["estate_id"];
            
			$total       = $info_date["price"];
            
            $pro_id      = $info_date["province_id"];
            
            $user_id     = $info_date["user_id"];
 	
            $isSum       = false;
            
            $qty_stopper =0;
            
            if($estate_id ==187) //winelife 
            {
                if($qty>=24)
                {
                    $total  = $info_date["prom_price"];
                    $profit = $info_date["prom_profit"];
                }
            }

            if($estate_id ==188) //winelife 
            {
                $qty_stopper =$rpData ->getTotalStoppers($invoiceNo);
                $qty = $qty - $qty_stopper;
            }
                        
           if($i==0)
           {
                $lastPro = $pro_id;
                if($estate_id ==187)
                    $proTotalW=$qty;
                elseif($estate_id ==188)
                {
                    $proTotalBM=$qty;
                    $proTotalBM_Stopper= $qty_stopper;
                }
                elseif($estate_id ==196)
                    $proTotalBT=$qty;
                elseif($estate_id ==215)
                    $proTotalSB=$qty;
                    
                $proTotal=$total;
                $proTotalCM=$commission;
                $proTotalPF=$profit;
                $proCost =$cost;
              
                $startRow = $row;
                
                $last_user = $user_name;
                $startUserRow =$row;
           }
           else
           {
                if($last_user==$user_name)
                {
                    $user_name="";
                  
                }
                else
                {
                    
                    $endUserRow=$row;
                    $values = array(
        							array("data"=>""), 
        							array("data"=>""), 
        							array("data"=>""), 
        							array("data"=>""), 
        							array("data"=>""), 
                                    array("data"=>"Consultant Total","format"=>$arialNormalBorderRedBold),
        							array("data"=>"=SUM(G$startUserRow:G$endUserRow)","format"=>$arialNormalBorderRedBold),
        							array("data"=>"=SUM(H$startUserRow:H$endUserRow)","format"=>$arialNormalBorderRedBold),
        							array("data"=>"=SUM(I$startUserRow:I$endUserRow)","format"=>$arialNormalBorderRedBold),
     						
                                   	array("data"=>"=SUM(J$startUserRow:J$endUserRow)","format"=>$arialNormalBorderRedBold),	
                                        
                                	array("data"=>"=SUM(K$startUserRow:K$endUserRow)","format"=>$arialNormalBorderRedBold),	
                                    
                                    array("data"=>"=SUM(L$startUserRow:L$endUserRow)","format"=>$arialNormalCurrencyRed),												
                                    array("data"=>"=SUM(M$startUserRow:M$endUserRow)","format"=>$arialNormalCurrencyRed),	
                                    array("data"=>"=SUM(N$startUserRow:N$endUserRow)","format"=>$arialNormalCurrencyRed),
                                    array("data"=>"=SUM(O$startUserRow:O$endUserRow)","format"=>$arialNormalCurrencyRed),												
    							); 
                               
    						 	$this->_writeRow($sp, $values, $row, $arialNormalBorder); 
                     $last_user = $user_name;
                    
                   
                    
                    $row++;
                    $startUserRow = $row+1;
                }
               
            
                
               if($lastPro==$pro_id)
                {
                    $province ="";
                    $proTotal = $proTotal+$qty;
                    
                    if($estate_id ==187)
                        $proTotalW=$proTotalW+$qty;
                    elseif($estate_id ==188)
                    {
                        $proTotalBM=$proTotalBM+$qty;
                        $proTotalBM_Stopper=$proTotalBM_Stopper+$qty_stopper;
                    }
                    elseif($estate_id ==196)
                        $proTotalBT=$proTotalBT+$qty;
                    elseif($estate_id ==215)
                        $proTotalSB=$proTotalSB+$qty;
                        
                    $proTotal=$proTotal+$total;
                    $proTotalCM=$proTotalCM+$commission;
                    $proTotalPF=$proTotalPF+$profit;
                    $proCost =$proCost+$cost;
                
                }
                else
                {
                    $endRow = $row;
                    $values = array(
    							array("data"=>""), 
    							array("data"=>""), 
    							array("data"=>""), 
    							array("data"=>""), 
    							array("data"=>""), 
                                array("data"=>"Province Total","format"=>$arialNormalBorderBlueBold),
    							array("data"=>$proTotalW,"format"=>$arialNormalBorderBlue),
                                array("data"=>$proTotalBM_Stopper,"format"=>$arialNormalBorderBlue),
    							array("data"=>$proTotalBM,"format"=>$arialNormalBorderBlue),
    							array("data"=>$proTotalBT,"format"=>$arialNormalBorderBlue),
                                array("data"=>$proTotalSB,"format"=>$arialNormalBorderBlue),
    						
                               	array("data"=>$proTotal,"format"=>$arialNormalCurrencyBlue),	
                                    
                                array("data"=>$proTotalCM,"format"=>$arialNormalCurrencyBlue),
                                array("data"=>$proCost,"format"=>$arialNormalCurrencyBlue),
                                array("data"=>$proTotalPF,"format"=>$arialNormalCurrencyBlue),												
    							); 
                                
    						 	$this->_writeRow($sp, $values, $row, $arialNormalBorder); 
            
                    $gTotalBT = $gTotalBT+$proTotalBT;
                    $gTotalBM = $gTotalBM+$proTotalBM;
                    $gTotalBM_Stopper = $gTotalBM_Stopper+$proTotalBM_Stopper;
                    
                    $gTotalW=$gTotalW+$proTotalW;
                    $gTotalSB=$gTotalSB+$proTotalSB;
                    
                    $gProTotal=$gProTotal+$proTotal;
                    $gProCost=$gProCost+$proCost;
                    $gProCM=$gProCM+$proTotalCM;
                    $gProTotalPF=$gProTotalPF+$proTotalPF;
            
                    $proTotalW=0;
                    $proTotalBM=0;
                    $gTotalBM_Stopper=0;
                    $proTotalBT=0;
                    $proTotalSB=0;
                    if($estate_id ==187)
                        $proTotalW=$qty;
                    elseif($estate_id ==188)
                    {
                        $proTotalBM=$qty;
                        $proTotalBM_Stopper=$qty_stopper;
                        
                    }
                    elseif($estate_id ==196)
                        $proTotalBT=$qty;
                    elseif($estate_id ==215)
                        $proTotalSB=$qty;
                    
                    $proTotal=$total;
                    $proTotalCM=$commission;
                    $proTotalPF=$profit;
                    $proCost =$cost;
                    
                    $lastPro = $pro_id;
                        
                 	$row++;
                    $startRow = $row+1;
                    $startUserRow = $row+1;
                }
           }
            
                if($estate_id ==187) //winelife
                {
    		      	$values = array(
    							array("data"=>$province), 
    							array("data"=>$user_name), 
    							array("data"=>$invoiceNo), 
    							array("data"=>$store), 
    							array("data"=>$storeNo), 
                                array("data"=>$payType), 
    							array("data"=>$qty), 
    							array("data"=>""), 
                                array("data"=>""), 
    							array("data"=>""), 
                                array("data"=>""), 
    							array("data"=>$total,"format"=>$arialNormalCurrency),	
                                array("data"=>$commission,"format"=>$arialNormalCurrency),
                                array("data"=>$cost,"format"=>$arialNormalCurrency),	
                                 array("data"=>$profit,"format"=>$arialNormalCurrency),												
    							); 
    						 	$this->_writeRow($sp, $values, $row, $arialNormalBorder); 
    							$row++;
            
                }
                elseif($estate_id ==188) // barmar
                {
                    $bm_qty="";
                    $bm_stopper ="";
                    
                    if($qty_stopper!=0)
                        $bm_stopper = $qty_stopper;
                    if($qty!=0)
                        $bm_qty = $qty;
                        
                    $values = array(
    							array("data"=>$province), 
    							array("data"=>$user_name), 
    							array("data"=>$invoiceNo), 
    							array("data"=>$store), 
    							array("data"=>$storeNo), 
                                array("data"=>$payType), 
    							array("data"=>""), 
                                array("data"=>$bm_stopper), 
    							array("data"=>$bm_qty), 
    							array("data"=>""), 
                                array("data"=>""), 
    							array("data"=>$total,"format"=>$arialNormalCurrency),
    							array("data"=>$commission,"format"=>$arialNormalCurrency),
                                array("data"=>$cost,"format"=>$arialNormalCurrency),
                                array("data"=>$profit,"format"=>$arialNormalCurrency),					
    							); 
    
    
    						 	$this->_writeRow($sp, $values, $row, $arialNormalBorder); 
    							$row++;
    				
                }
                elseif($estate_id==196) // bitter
                {
                    $values = array(
    							array("data"=>$province), 
    							array("data"=>$user_name), 
    							array("data"=>$invoiceNo), 
    							array("data"=>$store), 
    							array("data"=>$storeNo), 
                                array("data"=>$payType), 
    							array("data"=>""), 
    							array("data"=>""), 
                                array("data"=>""),
                                array("data"=>$qty),
                                array("data"=>""),
    							array("data"=>$total,"format"=>$arialNormalCurrency),
    							array("data"=>$commission,"format"=>$arialNormalCurrency),
                                array("data"=>$cost,"format"=>$arialNormalCurrency),
                                	array("data"=>$profit,"format"=>$arialNormalCurrency),					
    							); 
    
    
    						 	$this->_writeRow($sp, $values, $row, $arialNormalBorder); 
    							$row++;
    				
                }
                elseif($estate_id==215) // sorbos
                {
                    $values = array(
    							array("data"=>$province), 
    							array("data"=>$user_name), 
    							array("data"=>$invoiceNo), 
    							array("data"=>$store), 
    							array("data"=>$storeNo), 
                                array("data"=>$payType), 
    							array("data"=>""), 
    							array("data"=>""), 
                                array("data"=>""),
                                array("data"=>""),
    							array("data"=>$qty), 
    							array("data"=>$total,"format"=>$arialNormalCurrency),
    							array("data"=>$commission,"format"=>$arialNormalCurrency),
                                array("data"=>$cost,"format"=>$arialNormalCurrency),
                                	array("data"=>$profit,"format"=>$arialNormalCurrency),					
    							); 
    
    
    						 	$this->_writeRow($sp, $values, $row, $arialNormalBorder); 
    							$row++;
    				
                }
                
                $i++;
            
        }			
        
        $endUserRow =$row;
        	//last user total commission
         /*	$fp = fopen("logs/exceltext.log","a");
    fputs($fp, "$startUserRow end $endUserRow"."\n");
    fclose($fp);*/
    
              $values = array(
    							array("data"=>""), 
    							array("data"=>""), 
    							array("data"=>""), 
    							array("data"=>""), 
    							array("data"=>""), 
                                array("data"=>"Consultant Total","format"=>$arialNormalBorderRedBold),
    							array("data"=>"=SUM(G$startUserRow:G$endUserRow)","format"=>$arialNormalBorderRedBold),
    							array("data"=>"=SUM(H$startUserRow:H$endUserRow)","format"=>$arialNormalBorderRedBold),
    							array("data"=>"=SUM(I$startUserRow:I$endUserRow)","format"=>$arialNormalBorderRedBold),
    						   	array("data"=>"=SUM(J$startUserRow:J$endUserRow)","format"=>$arialNormalBorderRedBold),	
                                array("data"=>"=SUM(K$startUserRow:K$endUserRow)","format"=>$arialNormalBorderRedBold),	
                                array("data"=>"=SUM(L$startUserRow:L$endUserRow)","format"=>$arialNormalCurrencyRed),
                                array("data"=>"=SUM(M$startUserRow:M$endUserRow)","format"=>$arialNormalCurrencyRed),
                                array("data"=>"=SUM(N$startUserRow:N$endUserRow)","format"=>$arialNormalCurrencyRed),
                                array("data"=>"=SUM(O$startUserRow:O$endUserRow)","format"=>$arialNormalCurrencyRed),													
							); 
                            $this->_writeRow($sp, $values, $row, $arialNormalBorder); 
                            $row++;
                                
        //last province total
		 $values = array(
    							array("data"=>""), 
    							array("data"=>""), 
    							array("data"=>""), 
    							array("data"=>""), 
    							array("data"=>""), 
                                array("data"=>"Province Total","format"=>$arialNormalBorderBlueBold),
    							array("data"=>$proTotalW,"format"=>$arialNormalBorderBlue),
                                array("data"=>$proTotalBM_Stopper,"format"=>$arialNormalBorderBlue),
    							array("data"=>$proTotalBM,"format"=>$arialNormalBorderBlue),
    							array("data"=>$proTotalBT,"format"=>$arialNormalBorderBlue),
                                array("data"=>$proTotalSB,"format"=>$arialNormalBorderBlue),
    						
                               	array("data"=>$proTotal,"format"=>$arialNormalCurrencyBlue),	
                                    
                                array("data"=>$proTotalCM,"format"=>$arialNormalCurrencyBlue),	
                                array("data"=>$proCost,"format"=>$arialNormalCurrencyBlue),	
                                array("data"=>$proTotalPF,"format"=>$arialNormalCurrencyBlue),												
    							);                         
    						 	$this->_writeRow($sp, $values, $row, $arialNormalBorder); 
                                
            $gTotalBT = $gTotalBT+$proTotalBT;
            $gTotalBM = $gTotalBM+$proTotalBM;
             $gTotalBM_Stopper = $gTotalBM_Stopper+$proTotalBM_Stopper;
            $gTotalW=$gTotalW+$proTotalW;
            $gTotalSB=$gTotalSB+$proTotalSB; 
            
            $gProTotal=$gProTotal+$proTotal;
            $gProCost=$gProCost+$proCost;
            $gProCM=$gProCM+$proTotalCM;
            $gProTotalPF=$gProTotalPF+$proTotalPF;
            
            
            $fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'fgcolor'=>'silver', 'bold'=>1, 
                'bgcolor'=>'black', 'border'=>1, 'align'=>'center', 'valign'=>'bottom',
                'top'=>1, 'bottom'=>1));
     
            $columnHeader = $fm;
            
            $fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'fgcolor'=>'silver', 'bold'=>1, 
                    'bgcolor'=>'black', 'border'=>1, 'align'=>'right', 'valign'=>'bottom',
                    'top'=>1, 'bottom'=>1));       
            $columnHeaderRight = $fm;
        
            // country total
            	$values = array(array("data"=>"", "format"=>$arialNormalNoBorder),  
		                        array("data"=>"", "format"=>$arialNormalNoBorder),   
		                        array("data"=>"", "format"=>$arialNormalNoBorder),  
                                array("data"=>"", "format"=>$arialNormalNoBorder),   
		                        array("data"=>"", "format"=>$arialNormalNoBorder),   
		                        
		                        
		                        array("data"=>""),  
		                        array("data"=>"Winelife", "format"=>$columnHeaderRight), 
		                       
                               array("data"=>"B Stoppers", "format"=>$columnHeaderRight),
		                        array("data"=>"Bermar units", "format"=>$columnHeaderRight),
                                array("data"=>"Bitters", "format"=>$columnHeaderRight), 
                                array("data"=>"Sorbos", "format"=>$columnHeaderRight), 
                                 
            					array("data"=>"Total", "format"=>$columnHeaderRight),
                                array("data"=>"Commissions", "format"=>$columnHeaderRight),
                                array("data"=>"Cost", "format"=>$columnHeaderRight),
                                array("data"=>"Profit", "format"=>$columnHeaderRight)
                    
                    );	
                    $row++;
                   	$this->_writeRow($sp, $values, $row, $arialNormalBorder); 
                   	 $values = array(
    							array("data"=>"", "format"=>$arialNormalNoBorder),  
		                        array("data"=>"", "format"=>$arialNormalNoBorder),   
		                        array("data"=>"", "format"=>$arialNormalNoBorder),  
                                array("data"=>"", "format"=>$arialNormalNoBorder),   
		                        array("data"=>"", "format"=>$arialNormalNoBorder),    
                                array("data"=>"Grand Total","format"=>$arialNormalBorderRedBoldYellowBG),
    							array("data"=>$gTotalW,"format"=>$arialNormalBorderRedBoldYellowBG),
    							array("data"=>$gTotalBM_Stopper,"format"=>$arialNormalBorderRedBoldYellowBG),
                                array("data"=>$gTotalBM,"format"=>$arialNormalBorderRedBoldYellowBG),
    							array("data"=>$gTotalBT,"format"=>$arialNormalBorderRedBoldYellowBG),
                                array("data"=>$gTotalSB,"format"=>$arialNormalBorderRedBoldYellowBG),
    						
                               	array("data"=>$gProTotal,"format"=>$arialNormalCurrencyRedYellowBG),	
                                    
                                array("data"=>$gProCM,"format"=>$arialNormalCurrencyRedYellowBG),	
                                array("data"=>$gProCost,"format"=>$arialNormalCurrencyRedYellowBG),	
                                array("data"=>$gProTotalPF,"format"=>$arialNormalCurrencyRedYellowBG),												
    							);                         
    				$row++;
                    		 	$this->_writeRow($sp, $values, $row, $arialNormalBorder); 			
	
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
          $this->_writeCell($sp, array("data"=>$this->titleText), $row, "A", $reportTitle); 
 
        $sp->mergeCells($row, $this->columns["A"]["index"], $row, $this->columns["K"]["index"]);
    }
    
    function _writeColumnHeaders(& $workbook, & $sp, & $row)
    {
        $fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'fgcolor'=>'silver', 'bold'=>1, 
                'bgcolor'=>'black', 'border'=>1, 'align'=>'center', 'valign'=>'bottom',
                'top'=>1, 'bottom'=>1));
     
        $columnHeader = $fm;        
        
        $fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'fgcolor'=>'silver', 'bold'=>1, 
                'bgcolor'=>'black', 'border'=>1, 'align'=>'left', 'valign'=>'bottom',
                'top'=>1, 'bottom'=>1));       
        $columnHeaderLeft = $fm;
        
        $fm = & $workbook->addFormat(array('fontfamily'=>'Arial', 'size'=>10, 'fgcolor'=>'silver', 'bold'=>1, 
                'bgcolor'=>'black', 'border'=>1, 'align'=>'right', 'valign'=>'bottom',
                'top'=>1, 'bottom'=>1));       
        $columnHeaderRight = $fm;
        
       
		$values = array(array("data"=>"Province", "format"=>$columnHeaderLeft), 
                    array("data"=>"Consultant", "format"=>$columnHeaderLeft), 
                    array("data"=>"Invoice#", "format"=>$columnHeaderLeft), 
                    array("data"=>"Customer", "format"=>$columnHeaderLeft), 
                    array("data"=>"Store#", "format"=>$columnHeaderRight), 
                    
                    
                    array("data"=>"Payment type", "format"=>$columnHeaderLeft), 
                    array("data"=>"Winelife", "format"=>$columnHeaderRight), 
                    array("data"=>"B Stoppers", "format"=>$columnHeaderRight),
                    array("data"=>"Bermar units", "format"=>$columnHeaderRight),
                    array("data"=>"Bitters", "format"=>$columnHeaderRight),
                    
                    array("data"=>"Sorbos", "format"=>$columnHeaderRight), 
                     
					array("data"=>"Total", "format"=>$columnHeaderRight),
                    array("data"=>"Commissions", "format"=>$columnHeaderRight),
                    array("data"=>"Cost", "format"=>$columnHeaderRight),
                    array("data"=>"Profit", "format"=>$columnHeaderRight)
        
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
     	
        for ($i = "A", $j=0; $i!=$this->maxColumLeter; $i++, $j++)
        {
            
            $this->_writeCell(& $sp, $value[$j], $row, $i, $format);
        }
    }

}
?>