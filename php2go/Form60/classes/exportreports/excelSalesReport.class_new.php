<?php

import('Form60.base.F60DocBase');
import('Form60.util.F60Date');
import('php2go.util.Spreadsheet');
import('Form60.bll.bllSSDSData');

class excelSalesReport extends F60DocBase 
{
    var $sale_month;
    var $sale_year;
    var $user_id;
    var $store_type_id;
    var $SSDSData;
    var $reportData;
    var $columns;
    
    function excelSalesReport()
    {
     
        $this->pageTitle = "Detailed sales report"; 
        F60DocBase::F60DocBase($this->pageTitle);
        $this->elements['main'] ="";
        $this->columns = array("A"=>array("index"=>0, "width"=>20.14), "B"=>array("index"=>1, "width"=>33.71), 
                "C"=>array("index"=>2, "width"=>13), "D"=>array("index"=>3, "width"=>13), "E"=>array("index"=>4, "width"=>13),
                "F"=>array("index"=>5, "width"=>13), "G"=>array("index"=>6, "width"=>13), "H"=>array("index"=>7, "width"=>13),
                "I"=>array("index"=>8, "width"=>13),"J"=>array("index"=>9, "width"=>13),"K"=>array("index"=>10, "width"=>13));

        $this->sale_month =$_REQUEST["sale_month"];
        
        $this->sale_year = $_REQUEST["sale_year"];
        $this->user_id = $_REQUEST["user_id"];
        $this->store_type_id = $_REQUEST["store_type_id"];
        $this->SSDSData = new SSDSData();
       
        $bRet = $this->SSDSData->GetSalesReport($this->sale_month, $this->sale_year, $this->user_id, $this->store_type_id, 2);
        
        if (!$bRet)
        {
            $this->elements['error'] = $this->SSDSData->file_format_error;
        }
        else
        {
            $this->reportData = $bRet;
            $this->generateSpreadsheet();
        }
              
        
        F60DocBase::display();
    }
    
  function generateSpreadsheet_test()
    {
        $sp =& new Spreadsheet();
        $sp->setPrintGridlines(false);
        
        $row = 0;
        
          
        //fonts
        $arialNormal = $sp->addFont(array('name'=>'Arial', 'size'=>10));
        $verdanaNormal = $sp->addFont(array('name'=>'Verdana', 'size'=>9));
        $verdanaBold = $sp->addFont(array('bold'=>true, 'italic'=>false, 'name'=>'Verdana', 'size'=>9));
        
        //cell formats
        $shaded = $sp->addCellFormat(array('shaded'=>true));
        $topBorder= $sp->addCellFormat(array('top_border'=>true));
        $topBottomBorder= $sp->addCellFormat(array('top_border'=>true, 'bottom_border'=>true));
        $right = $sp->addCellFormat(array('align'=>'right'));
        $topBorderRight = $sp->addCellFormat(array('top_border'=>true, 'align'=>'right'));
        $topBottomBorderRight = $sp->addCellFormat(array('top_border'=>true, 'bottom_border'=>true, 'align'=>'right'));
        
        //format pics
        $picEmpty = $sp->addPictureString("0");
        $picNumber = $sp->addPictureString("#,##0.00");
        $picCurrency = $sp->addPictureString("$#,##0.00;[Red]-$#,##0.00");
        $picPercent = $sp->addPictureString("0%");
        $emptyCell = array("data"=>"");

      
         $fileName = "excelTest.xls";
        
        $userCnt=0;
      
          $values = array($emptyCell, $emptyCell, $emptyCell, $emptyCell, 
                        array("data"=>"Total:"), array("data"=>"Total"), $emptyCell,
                        array("data"=>"profit", "picture"=>$picCurrency), 
                        array("data"=>"totalcase", "picture"=>$picNumber),
                        array("data"=>"totalsales", "picture"=>$picCurrency),
								array("data"=>"totalretail", "picture"=>$picCurrency)); 
                   
                $this->writeRow($sp, $values, $row, $verdanaBold, $topBorder);
    
    //	 $this->debugTxt(print_r($sp));
    
   // print_r($sp);
     //$location = "reports/$fileName";
		//$sp->toFile($location); 
		
		  $sp->download($fileName);
    
   
		
    }
    function generateSpreadsheet()
    {
        $sp =& new Spreadsheet();
        $sp->setPrintGridlines(false);
        
        $row = 0;
        
        $users = $this->reportData["user_details"];
        $sales = $this->reportData["sales_details"];
        $summaryData = $this->reportData["summary_details"];
        $storeTypes = $this->reportData["store_type_details"];
        
        //fonts
        $arialNormal = $sp->addFont(array('name'=>'Arial', 'size'=>10));
        $verdanaNormal = $sp->addFont(array('name'=>'Verdana', 'size'=>9));
        $verdanaBold = $sp->addFont(array('bold'=>true, 'italic'=>false, 'name'=>'Verdana', 'size'=>9));
        
        //cell formats
        $shaded = $sp->addCellFormat(array('shaded'=>true));
        $topBorder= $sp->addCellFormat(array('top_border'=>true));
        $topBottomBorder= $sp->addCellFormat(array('top_border'=>true, 'bottom_border'=>true));
        $right = $sp->addCellFormat(array('align'=>'right'));
        $topBorderRight = $sp->addCellFormat(array('top_border'=>true, 'align'=>'right'));
        $topBottomBorderRight = $sp->addCellFormat(array('top_border'=>true, 'bottom_border'=>true, 'align'=>'right'));
        
        //format pics
        $picEmpty = $sp->addPictureString("0");
        $picNumber = $sp->addPictureString("#,##0.00");
        $picCurrency = $sp->addPictureString("$#,##0.00;[Red]-$#,##0.00");
        $picPercent = $sp->addPictureString("0%");
        $emptyCell = array("data"=>"");

        if ($this->store_type_id == -1)
            $storeType = "All Stores";
        else
        {
            foreach($storeTypes as $storeType)
                if ($storeType["lkup_store_type_id"] == $this->store_type_id)
                    break;
            $storeType = $storeType["license_name"];
        }
        
        if ($this->user_id == -1)
            $fileName ="All consultants-$storeType-" . F60Date::getMonthTxt($this->sale_month) . "-" . substr($this->sale_year,-2) .".xls";
        else
            $fileName = $users[0]["name"] . "-$storeType-" . F60Date::getMonthTxt($this->sale_month). "-" . substr($this->sale_year,-2) .".xls";
        
        $userCnt=0;
        foreach($users as $user)
        {
         	if ($this->store_type_id == 6)
         	{
         		if($userCnt!=0)
         			$row = $row+10;
         	}
         		
         	if ($this->store_type_id == 8)
         	{
         		if($userCnt!=0)
         			$row = $row+4;
         	}   
         		
            $titleText = $user["name"]. ", " . F60Date::getMonthTxt($this->sale_month) . "-" . $this->sale_year . " ";
            $titleText .= " Sales Summary Report ($storeType)";

            $this->writeCell($sp, array("data"=>$titleText), $row, "A", $verdanaBold); 
       
            $row++;
            $this->writeCell($sp, array("data"=>"Generated on: " . date("M d, Y H:i:s")), $row, "A", $verdanaBold); 
           
            $row++;
            $userCnt++;
            
            $customer_id = 0;
            $firstRow = true;
            $totalBottles = 0;
            $totalProfit = 0;
            $totlaCases = 0;
            $totlaSales = 0;
            $totalRTSales = 0;
            
            $sales->moveFirst();            
           while ($sale = $sales->fetch())
            {
                if ($sale["user_id"] != $user["user_id"])
                    continue;
                    
                if ($customer_id != $sale["customer_id"])
                {
                    $customer_id = $sale["customer_id"];
                    
                    //if not first customer print totals
                    if (!$firstRow)
                    {
                        $values = array($emptyCell, $emptyCell, $emptyCell, $emptyCell, 
	                            array("data"=>"Total:"), array("data"=>$totalBottles), $emptyCell,
	                            array("data"=>$totalProfit, "picture"=>$picCurrency), 
	                            array("data"=>$totlaCases, "picture"=>$picNumber),
	                            array("data"=>$totlaSales, "picture"=>$picCurrency),
								array("data"=>$totalRTSales, "picture"=>$picCurrency)); 
                           
                        $this->writeRow($sp, $values, $row, $verdanaBold, $topBorder);
                        
                        $totalBottles = 0;
                        $totalProfit = 0;
                        $totlaCases = 0;
                        $totlaSales = 0;
                        $totalRTSales = 0;
                    }
                    
                    $row++;
                    $values = array(array("data"=>"Licensee#"), array("data"=>"Customer"), $emptyCell, array("data"=>"Store type"), 
                            array("data"=>"City"), array("data"=>"Address"), $emptyCell, $emptyCell, $emptyCell, $emptyCell,$emptyCell);
                    $this->writeRow($sp, $values, $row, $verdanaBold, $shaded);  
                    
                    $values = array(array("data"=>$sale["licensee_no"]), array("data"=>F60Date::ucwords1($sale["customer_name"])), $emptyCell, array("data"=>$sale["license_name"]), 
                        array("data"=>F60Date::ucwords1($sale["city"])), array("data"=>F60Date::ucwords1($sale["address"])), 
                        $emptyCell, $emptyCell, $emptyCell, $emptyCell,$emptyCell);
                    $this->writeRow($sp, $values, $row, $verdanaNormal);  
                    
                    //write wine header
                    $values = array(array("data"=>"SKU#"), array("data"=>"Product"), array("data"=>"Liters"), array("data"=>"Type"), 
                            array("data"=>"Country"), array("data"=>"Bts sold"), array("data"=>"Profit/btl"), 
                            array("data"=>"Total profit"), array("data"=>"Cases sold"),array("data"=>"WH sale"),array("data"=>"Retail sales"));

                    $this->writeRow($sp, $values, $row, $verdanaBold, $shaded); 
                    $firstRow = false;
                }
                
                $totalBottles += $sale["bottles_sold"];
                $totalProfit += $sale["total_profit"];
                $totlaCases += $sale["cases_sold"];
                $totlaSales += $sale["total_sales"];
                $totalRTSales += $sale["rt_sales"];
  
                $values = array(array("data"=>$sale["SKUA"]), array("data"=>$sale["wine_name"]), array("data"=>$sale["liters"]), 
		                        array("data"=>$sale["type"]),array("data"=>$sale["country"]), array("data"=>intval($sale["bottles_sold"])), 
		                        array("data"=>floatval($sale["profit_per_bottle"]), "picture"=>$picCurrency), 
		                        array("data"=>floatval($sale["total_profit"]), "picture"=>$picCurrency), array("data"=>floatval($sale["cases_sold"]), "picture"=>$picNumber),array("data"=>floatval($sale["total_sales"]), "picture"=>$picCurrency),array("data"=>floatval($sale["rt_sales"]), "picture"=>$picCurrency));
                    
                $this->writeRow($sp, $values, $row, $verdanaNormal);

            }
           
            //print total for last one
            if (!$firstRow)
            {
                $values = array($emptyCell, $emptyCell, $emptyCell, $emptyCell, 
                        array("data"=>"Total:"), array("data"=>$totalBottles), $emptyCell,
                        array("data"=>$totalProfit, "picture"=>$picCurrency), 
                        array("data"=>$totlaCases, "picture"=>$picNumber),
                        array("data"=>$totlaSales, "picture"=>$picCurrency),
								array("data"=>$totalRTSales, "picture"=>$picCurrency)); 
                   
                $this->writeRow($sp, $values, $row, $verdanaBold, $topBorder);
            
            }
            $row++;
            
           
              
            
            
           
            
            //print the summary part
          $summary = null;
            foreach($summaryData as $summary)
            {
                if ($summary["user_id"] == $user["user_id"])
                    break;
            }
                        
            if ($this->store_type_id == -1) //show commission details
            {
                $bonus = 0.00;
                $total_commission = 0.0;
                $grand_total_commission = 0.0;

                $commissions = $this->reportData["commission_details"];
                $commissionHeaderWritten = false;
                $userCommissionFound = false;
                $rowPos = $row;
                
                if (is_array($commissions))
                {
                    $level = 0;
                    foreach($commissions as $commission)
                    {
                        if ($commission["user_id"] != $user["user_id"])
                            continue;
                          
                        $userCommissionFound = true;                          
                        if (!$commissionHeaderWritten)
                        {
                            $values = array(array("data"=>"Commission levels"), array("data"=>"Total cases"), array("data"=>"Comm. rate"), array("data"=>"Comm. amount"), 
                            $emptyCell, array("data"=>"Total profit"), $emptyCell, array("data"=>"Total cases sold"), $emptyCell,array("data"=>"Total WH sale"), array("data"=>"Total RT sales"));
                            
                                $this->writeRow($sp, $values, $row, $verdanaBold, $shaded);
                                
                            $rowPos = $row;
                            $commissionHeaderWritten = true;
                        }

                        if ($commission["commission_rate"] == 0)
                            $bonus += $commission["bonus"]; //level is only for target bonus
                        else
                        {
                            $commRow = $rowPos + $level;
                            $lastLevel = (intval($commission["max_cases"])==9999999);
                            $levelCaption = $commission["caption"] . ": " . $commission["min_cases"];
                            $this->writeCell($sp, array("data"=>$levelCaption),$commRow, "A", $verdanaNormal);
                              
                            $this->writeCell($sp, array("data"=>floatval($commission["total_cases"]), "picture"=>$picNumber),
                                $commRow, "B", $verdanaNormal);
                            $this->writeCell($sp, array("data"=>floatval($commission["commission_rate"]/100), "picture"=>$picPercent),
                                $commRow, "C", $verdanaNormal);
                            $this->writeCell($sp, array("data"=>floatval($commission["commission_amount"]),"picture"=>$picCurrency),
                                $commRow, "D", $verdanaNormal);
                            $total_commission += $commission["commission_amount"];
                            $bonus += $commission["bonus"];
                            $level++;
                        }
                    }
                }
                
                if ($userCommissionFound) 
                {
                    //comm. totals
                    $commRow = $rowPos + $level;
                    $this->writeCell($sp, $emptyCell, $commRow, "A", $verdanaBold, $topBorder);
                    $this->writeCell($sp, $emptyCell, $commRow, "B", $verdanaBold, $topBorder);
                    $this->writeCell($sp, array("data"=>"Sub Total:"), $commRow, "C", $verdanaBold, $topBorderRight);
                    $this->writeCell($sp, array("data"=>floatval($total_commission),"picture"=>$picCurrency),
                        $commRow, "D", $verdanaBold, $topBorder);
                    $commRow++;
                    
                    $this->writeCell($sp, array("data"=>"Minimum Target"), $commRow, "A", $verdanaBold, $shaded);
                    $this->writeCell($sp, $emptyCell, $commRow, "B", $verdanaBold);
                    $this->writeCell($sp, array("data"=>"Target bonus:"), $commRow, "C", $verdanaBold, $right);
                    $this->writeCell($sp, array("data"=>floatval($bonus),"picture"=>$picCurrency),
                        $commRow, "D", $verdanaBold);
                    $commRow++;
                    
                    $grand_total_commission = $bonus + $total_commission;
                    
                    
                    $bllSSDS = new SSDSData();
			
							$targetInfo = $bllSSDS->getTargetCommissionLeveInfo($this->sale_month,$this->sale_year);
							
							//	$cntlInter->setValue($targetInfo[0]["min_intl_cases"]);
							//	$cntlCa = & $this->form->getField("target_ca_cases");
							//		$cntlCa->setValue($targetInfo[0]["min_canadian_cases"]);
		
			
			
                    $minInTarget= $targetInfo[0]["min_intl_cases"]." cases International wine";
                    $minCaTarget= $targetInfo[0]["min_canadian_cases"]." cases Canadian wine";
                    
                    $this->writeCell($sp, array("data"=>$minInTarget), $commRow, "A", $verdanaNormal);
                    $this->writeCell($sp, $emptyCell, $commRow, "B", $verdanaBold);
                    $this->writeCell($sp, array("data"=>"Total:"), $commRow, "C", $verdanaBold, $right);
                    $this->writeCell($sp, array("data"=>floatval($grand_total_commission),"picture"=>$picCurrency),
                        $commRow, "D", $verdanaBold);
                    $commRow++;
                    $this->writeCell($sp, array("data"=>$minCaTarget), $commRow, "A", $verdanaNormal);
                    
                    $row = $commRow; //max row for commissions
                }
                
                $commRow = $rowPos;
                $this->writeCell($sp, array("data"=>"Total CND Profit:", "font"=>$verdanaBold),
                    $commRow, "F", $verdanaNormal);
                $this->writeCell($sp, array("data"=>floatval($summary["total_canadian_profit"]),"picture"=>$picCurrency),
                    $commRow, "G", $verdanaNormal);
                $this->writeCell($sp, array("data"=>"Total CND:", "font"=>$verdanaBold),
                    $rowPos, "H", $verdanaNormal);
                $this->writeCell($sp, array("data"=>floatval($summary["total_canadian_cases"]), "picture"=>$picNumber),
                    $commRow, "I", $verdanaNormal);
                $this->writeCell($sp, array("data"=>floatval($summary["total_sales"]), "picture"=>$picCurrency),
                    $commRow, "J", $verdanaNormal);
                $this->writeCell($sp, array("data"=>floatval($summary["total_retail"]), "picture"=>$picCurrency),
                    $commRow, "K", $verdanaNormal);
                $commRow++;                
                $this->writeCell($sp, array("data"=>"Total Inter profit:", "font"=>$verdanaBold),
                    $commRow, "F", $verdanaNormal);
                $this->writeCell($sp, array("data"=>floatval($summary["total_international_profit"]),"picture"=>$picCurrency),
                    $commRow, "G", $verdanaNormal);
                $this->writeCell($sp, array("data"=>"Total Intern:", "font"=>$verdanaBold),
                    $commRow, "H", $verdanaNormal);
                $this->writeCell($sp, array("data"=>floatval($summary["total_international_cases"]), "picture"=>$picNumber),
                    $commRow, "I", $verdanaNormal);
                    
                $commRow++;                
                $this->writeCell($sp, array("data"=>"Total profit:", "font"=>$verdanaBold),
                    $commRow, "F", $verdanaNormal, $topBorder);
                $this->writeCell($sp, array("data"=>floatval($summary["total_profit"]),"picture"=>$picCurrency),
                    $commRow, "G", $verdanaNormal, $topBorder);
                $this->writeCell($sp, array("data"=>"Total cs sold:", "font"=>$verdanaBold),
                    $commRow, "H", $verdanaBold, $topBorder);
                $this->writeCell($sp, array("data"=>floatval($summary["total_cases"]), "picture"=>$picNumber),
                    $commRow, "I", $verdanaBold, $topBorder);
                    
                $commRow++;
                $this->writeCell($sp, array("data"=>"Commission:", "font"=>$verdanaBold),
                    $commRow, "F", $verdanaNormal);
                $this->writeCell($sp, array("data"=>floatval($grand_total_commission),"picture"=>$picCurrency),
                    $commRow, "G", $verdanaNormal);
                $this->writeCell($sp, array("data"=>"Ave profit:", "font"=>$verdanaBold),
                    $commRow, "H", $verdanaNormal);
                $this->writeCell($sp, array("data"=>floatval($summary["avg_profit_per_case"]), "picture"=>$picCurrency),
                    $commRow, "I", $verdanaNormal);
                    
                $commRow++;
                $netProfit = $summary["total_profit"] - $grand_total_commission;
                $this->writeCell($sp, array("data"=>"Net profit:", "font"=>$verdanaBold),
                    $commRow, "F", $verdanaNormal, $topBottomBorder);
                $this->writeCell($sp, array("data"=>floatval($netProfit),"picture"=>$picCurrency),
                    $commRow, "G", $verdanaBold, $topBottomBorder);
            }
            
            else if ($this->store_type_id == 6 ||$this->store_type_id == 8) //BCLDB
            {
                $bonus = 0.00;

                $commissions = $this->reportData["commission_details"];
                $commissionHeaderWritten = false;
                $userCommissionFound = false;
                $rowPos = $row;
                
                if (is_array($commissions))
                {
                    $level = 0;
                    foreach($commissions as $commission)
                    {
								   if ($commission["user_id"] != $user["user_id"])
								       continue;
								     
								   $userCommissionFound = true;                          
								   
								   if (!$commissionHeaderWritten)
								   {
								    	if($this->store_type_id == 6)
								    	{
								    	 	
								       	$values = array(array("data"=>"Commission levels"), array("data"=>"Total sales"), $emptyCell, array("data"=>"Comm. amount"), 
								       	$emptyCell, array("data"=>"Total profit"), $emptyCell, array("data"=>"Total cases sold"), $emptyCell,array("data"=>"Total WH sale"),array("data"=>"Total RT sales"));
								         	  $this->writeRow($sp, $values, $row, $verdanaBold, $shaded);
								      }
								      else
								      {
											$values = array(array("data"=>"Commission levels"), array("data"=>"Total cases"), $emptyCell, array("data"=>"Comm. amount"), 
								       	$emptyCell, array("data"=>"Total profit"), $emptyCell, array("data"=>"Total cases sold"), $emptyCell,array("data"=>"Total WH sale"),array("data"=>"Total RT sales"));
								         	  $this->writeRow($sp, $values, $row, $verdanaBold, $shaded);
										}
								       $rowPos = $row;
								       $commissionHeaderWritten = true;
								   }
								
								   $commRow = $rowPos + $level;
								  // print $commission["target_price"];
								   $this->writeCell($sp, array("data"=>floatval($commission["target_price"]),"picture"=>$picCurrency),
								           $commRow, "A", $verdanaNormal);
								           
								           
								   if($this->store_type_id == 6)
								   {
								   	$this->writeCell($sp, array("data"=>floatval($commission["total_sales"]), "picture"=>$picCurrency),
								      	 $commRow, "B", $verdanaNormal);
								   }
								   else
								   {
								   	$this->writeCell($sp, array("data"=>floatval($commission["total_cases"]), "picture"=>$picCurrency),
								      	 $commRow, "B", $verdanaNormal);
									}
								   $this->writeCell($sp, array("data"=>floatval($commission["bonus"]),"picture"=>$picCurrency),
								       $commRow, "D", $verdanaNormal);
								   $bonus += $commission["bonus"];
								   $level++;
                    }
                }
                
                if ($userCommissionFound) 
                {
                    $commRow = $rowPos + $level;
                    $this->writeCell($sp, $emptyCell, $commRow, "A", $verdanaBold, $topBorder);
                    $this->writeCell($sp, $emptyCell, $commRow, "B", $verdanaBold, $topBorder);
                    $this->writeCell($sp, array("data"=>"Sub Total:"), $commRow, "C", $verdanaBold, $topBorderRight);
                    $this->writeCell($sp, array("data"=>floatval($bonus),"picture"=>$picCurrency),
                        $commRow, "D", $verdanaBold, $topBorder);
                    $commRow++;
                }
                
                $commRow = $rowPos;
                $this->writeCell($sp, array("data"=>"Total Inter profit:", "font"=>$verdanaBold),
                    $commRow, "F", $verdanaNormal);
                $this->writeCell($sp, array("data"=>floatval($summary["total_international_profit"]),"picture"=>$picCurrency),
                    $commRow, "G", $verdanaNormal);
                $this->writeCell($sp, array("data"=>"Total cs sold:", "font"=>$verdanaBold),
                    $commRow, "H", $verdanaNormal);
                $this->writeCell($sp, array("data"=>floatval($summary["total_cases"]), "picture"=>$picNumber),
                    $commRow, "I", $verdanaNormal);
               
              
               $this->writeCell($sp, array("data"=>floatval($summary["total_sales"]), "picture"=>$picCurrency),
                    $commRow, "J", $verdanaNormal);
                    
                $this->writeCell($sp, array("data"=>floatval($summary["total_retail"]), "picture"=>$picCurrency),
                    $commRow, "K", $verdanaNormal);

               $commRow+=2;                
                $this->writeCell($sp, array("data"=>"Commission:", "font"=>$verdanaBold),
                    $commRow, "F", $verdanaNormal);
                $this->writeCell($sp, array("data"=>floatval($bonus),"picture"=>$picCurrency),
                    $commRow, "G", $verdanaNormal);
                    
                $commRow++;
                $netProfit = $summary["total_profit"] - $bonus;
                $this->writeCell($sp, array("data"=>"Net profit:", "font"=>$verdanaBold),
                    $commRow, "F", $verdanaNormal, $topBottomBorder);
                $this->writeCell($sp, array("data"=>floatval($netProfit),"picture"=>$picCurrency),
                    $commRow, "G", $verdanaBold, $topBottomBorder);
            }
            
            else //just show totals
            {
                $total_profit = 0.0;
                $total_cases = 0.0;
                if (is_array($summary))
                {
                    $total_profit = $summary["total_profit"];
                    $total_cases = $summary["total_cases"];
                }
                $this->writeCell($sp, array("data"=>"Total profit:"), $row, "A", $verdanaBold);
                $this->writeCell($sp, array("data"=>floatval($total_profit), "picture"=>$picCurrency),
                    $row, "B", $verdanaBold);
                    
                $row++;
                $this->writeCell($sp, array("data"=>"Total cases sold:"), $row, "A", $verdanaBold);
                $this->writeCell($sp, array("data"=>floatval($total_cases), "picture"=>$picNumber),
                    $row, "B", $verdanaBold);
            }
          
            
              $values = array($emptyCell, $emptyCell, $emptyCell, $emptyCell, 
                        array("data"=>"Total:"), array("data"=>$totalBottles), $emptyCell,
                        array("data"=>$totalProfit, "picture"=>$picCurrency), 
                        array("data"=>$totlaCases, "picture"=>$picNumber),
                        array("data"=>$totlaSales, "picture"=>$picCurrency),
								array("data"=>$totalRTSales, "picture"=>$picCurrency)); 
                   
                $this->writeRow($sp, $values, $row, $verdanaBold, $topBorder);
            
            $row +=2;
        }
        
    
    //	 $this->debugTxt(print_r($sp));
    
    //print_r($sp);
        $sp->download($fileName);
    }
    
      /* function debugTxt($text)
    {
		$fp = fopen("logs/excelDebug.log","a");
		fputs($fp,  $text."\n");
		fclose($fp);
	}*/
    
    function writeCell(& $sp, $value, $row, $col, $font =0, $format = 0)
    {
        if (is_string($value["data"]))
            return $sp->writeString($row, $this->columns[$col]["index"], $value["data"], 
                    $this->columns[$col]["width"], array_key_exists("picture",$value)?$value["picture"]:0, 
                    array_key_exists("font",$value)?$value["font"]:$font, 
                    array_key_exists("format",$value)?$value["format"]:$format);
        else
            return $sp->writeData($row, $this->columns[$col]["index"], $value["data"], 
                    $this->columns[$col]["width"], array_key_exists("picture",$value)?$value["picture"]:0, 
                    array_key_exists("font",$value)?$value["font"]:$font, 
                    array_key_exists("format",$value)?$value["format"]:$format);
    }
    
    function writeRow(& $sp, $value, & $row, $font = 0, $format=0)
    {
        for ($i = "A", $j=0; $i!="L"; $i++, $j++)
        {
            $this->writeCell(& $sp, $value[$j], $row, $i, $font, $format);
        }
        $row++;
    }

}
?>