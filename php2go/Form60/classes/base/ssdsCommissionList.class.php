<?php

import('php2go.base.Document');
import('php2go.data.PagedDataSet');
import('Form60.bll.bllSSDSData');


class ssdsCommissionList extends PagedDataSet
{
    var $_Document;
    var $orderBy = "customer_name";
    var $orderType = "a";
    var $page = 1;
    var $sqlCode;

    var $Template = NULL;
    var $templateFile;
    var $sortSymbols;

    var $user_id;
    var $sale_month;
    var $sale_year;
    var $store_type;
    var $currentpage;
    var $user_name;
  
	var $store_type;
	var $province_id;
	
	var $userType=1;


    function ssdsCommissionList($Document,$user_id,$sale_month,$sale_year, $user_name="",$store_type=-1,$userType=0)
    {
       $this->user_id = $user_id;
       $this->sale_month = $sale_month;
       $this->sale_year = $sale_year;
       $this->user_name =$user_name;
       $this->store_type=$store_type;
       $this->province_id = $_COOKIE["F60_PROVINCE_ID"];
       $this->userType=$userType;
      
       PagedDataSet::PagedDataSet('db');
       PagedDataSet::setPageSize(10);

       if ($Document)
       {
			$Document->addScript('resources/js/javascript.ssdsreports.js');
            $this->_Document = $Document;
       }

	   $this->templateFile = 'ssdsCommissionList.tpl';
    
	   $this->templateFile = TEMPLATE_PATH. $this->templateFile;
       $this->Template =& new Template($this->templateFile);
       if ($this->getConfigVal('TEMPLATE_CACHE', FALSE))
            $this->Template->setCacheProperties($this->getConfigVal('FORM60_CACHE_PATH'));

       $this->Template->parse();
		
       
    }


    function _loadDataset()
    {
           
		PagedDataSet::setCurrentPage($this->page);
	   // PagedDataSet::load($this->sqlCode);
    }


	function _buildContent()
	{
		$bllSSDS = new SSDSData();
		
	
		if(!$bllSSDS->isNewRule($this->sale_year,$this->sale_month))
		{
		 
			$this->_buildContent_Old();
		}
		else
		{
			if($this->user_id ==0)
				$this->_buildContent_NewSpeical($this->userType);
			else
				$this->_buildContent_New();
			
		}
	}

	function _buildContent_New()
    {
 	
 		$this->Template->globalAssign("user_name",  $this->user_name);   
		
		$bllSSDS = new SSDSData();
		

		
		
		$bonusinfo= $bllSSDS->getTargetCommissionLeveInfo($this->sale_month,$this->sale_year,$this->user_id);
		
//	print_r($bonusinfo);
		$bonus_type = $bonusinfo[0]["lkup_sales_commission_type_id"];
		

       	 
		$lkup_commission_sales_sum_type_id = $bonusinfo[0]["lkup_commission_sales_sum_type_id"];
		

		       //	print $bonus_type."000".$this->province_id;
        $commInfo = $bllSSDS->GetSalesCommissionsReport($this->sale_month,$this->sale_year,$this->user_id,$bonus_type,$lkup_commission_sales_sum_type_id);


        $aRow = 0;
        
        $nRows= count($commInfo);       
		        
		$sub_total =0;
		        
			    //	$targetInfo = $bllSSDS->getTargetCommissionLeveInfo($this->sale_month,$this->sale_year);
			    	
			  //     	$bllSSDS = new SSDSData();
	
		//	$targetInfo = $bllSSDS->getTargetCommissionLeveInfo($this->sale_month,$this->sale_year,$this->user_id);
			

			
		    $bllTotalInfo =$bllSSDS->getTotalCasesById($this->user_id,$this->sale_month,$this->sale_year,"",$bonus_type);
	       // print_r($bllTotalInfo);
            // if( $bonus_type ==2||$bonus_type ==3||) //bcldb ; bcldb and regular store
            
            
             if( $bonus_type ==2||$bonus_type ==3) //bcldb ; bcldb and regular store
              {
		    
			/*
			 /*Array ( [0] => Array ( [user_id] => 64 [level_id] => 0 [caption] => Level 0 [min_cases] => 0.00 [max_cases] => 0.00 [commission_rate] => 0.00 [total_cases] => 0.00 [commission_amount] => 0.0000 [bonus] => 0.0 ) [1] => Array ( [user_id] => 64 [level_id] => 1 [caption] => Level 1 [min_cases] => 181.00 [max_cases] => 205.00 [commission_rate] => 15.00 [total_cases] => 0.00 [commission_amount] => 0.0000 [bonus] => 0.0 ) [2] => Array ( [user_id] => 64 [level_id] => 2 [caption] => Level 2 [min_cases] => 206.00 [max_cases] => 255.00 [commission_rate] => 20.00 [total_cases] => 0.00 [commission_amount] => 0.0000 [bonus] => 0.0 ) [3] => Array ( [user_id] => 64 [level_id] => 3 [caption] => Level 3 [min_cases] => 256.00 [max_cases] => 1000.00 [commission_rate] => 25.00 [total_cases] => 118.24 [commission_amount] => 253.9204 [bonus] => 0.0 ) )*/
	        
	        /*bonusinfo
	        
	        Array ( [0] => Array ( [sales_commission_level_id] => 424 [province_id] => 1 [sale_date] => 2011-09-01 [user_id] => 43 [lkup_sales_commission_type_id] => 3 [target_cases_ca] => 40.00 [target_cases_intl] => 0.00 [level_start_cases] => 0.00 [level_end_cases] => 0.00 [level_commission_rate] => 0.00 [level_target_sales] => 0.00 [level_commission_bonus] => 0.00 [lkup_commission_sales_sum_type_id] => 1 [level_caption] => Level 0 [level_number] => 0 [when_entered] => 0000-00-00 00:00:00 [when_modified] => 20111018144210 [created_user_id] => 15 [modified_user_id] => 0 ) )
	        
	        bllTotalInfo
			Array ( [0] => Array ( [sales_summary_id] => 3416 [user_id] => 43 [total_canadian_cases] => 24.50 [total_international_cases] => 320.63 [total_cases] => 345.13 [total_canadian_profit] => 243.78 [total_international_profit] => 3142.10 [total_profit] => 3385.88 [avg_profit_per_case] => 9.81 [sale_month] => 9 [sale_year] => 2011 [lkup_store_type_id] => -1 [total_sales] => 23565.47 [total_retail] => 60702.78 [total_units] => 4308 [province_id] => 1 ) [1] => Array ( [sales_summary_id] => 3419 [user_id] => 43 [total_canadian_cases] => 0.00 [total_international_cases] => 473.60 [total_cases] => 473.60 [total_canadian_profit] => 0.00 [total_international_profit] => 4808.74 [total_profit] => 4808.74 [avg_profit_per_case] => 10.15 [sale_month] => 9 [sale_year] => 2011 [lkup_store_type_id] => 6 [total_sales] => 29761.87 [total_retail] => 83338.89 [total_units] => 5711 [province_id] => 1
			
			*/

	        		$this->Template->globalAssign("sales_title",  "Total sales");
	        		$this->Template->globalAssign("rate_title",  "");		        	
	        		$this->Template->globalAssign("comm_title",  "Commissons");		
	        		$this->Template->globalAssign("second_title",  "");		
	        		$this->Template->globalAssign("Last_title",  "");		
		        		
			        for($i=0;$i<=$nRows-1;$i++)
		        	{
						$this->Template->createBlock('loop_line');
						$this->Template->assign("row_style", ($i % 2)?"cellA":"cellB");
						
						//$t_price = 
						$comm_desc=$commInfo[$i]['caption'].": $".number_format($commInfo[$i]['target_price'],2, '.', ',');
						$this->Template->assign("comm_level_desc", $comm_desc);
						
						//cut valu
						$com_total_sales = $commInfo[$i]['total_sales'];
						if( $commInfo[$i]['total_sales']!= 0)
						{
							$com_total_sales = "$".$this->formatNumer2Float($com_total_sales,true);
						}
						else 
							$com_total_sales ="$0.00";
						
						$this->Template->assign("level_total_cases",$com_total_sales);
						
						
						$this->Template->assign("comm_rate","");
						
						if($bllTotalInfo[0]["total_canadian_cases"]<$bonusinfo[0]["target_cases_ca"] ||$bllTotalInfo[0]["total_international_cases"]<$bonusinfo[0]["target_cases_intl"])
				    	{
							$com_bonus = 0;
							$sub_total = 0;
						}
						else
						{
						 	$com_bonus = $commInfo[$i]['bonus'];
						 	$sub_total = $sub_total+ $commInfo[$i]['bonus'];
						}	
					
						$dis_val =$this->formatNumer2Float($com_bonus);
						$this->Template->assign("comm_amount",$dis_val);
						
					
			        }
		         }
		         else if($bonus_type ==4 ) //Alberta
		         { 
						     	
							$this->Template->globalAssign("sales_title",  "Total cases");
			        		$this->Template->globalAssign("rate_title",  "");		        	
			        		$this->Template->globalAssign("comm_title",  "Commissons");		
			        		$this->Template->globalAssign("second_title",  "");		
			        		$this->Template->globalAssign("Last_title",  "");	
							

				         for($i=0;$i<=$nRows-1;$i++)
				        	{
						      $this->Template->createBlock('loop_line');
				            $this->Template->assign("row_style", ($i % 2)?"cellA":"cellB");
				            
				            //$t_price = 
				            $comm_desc=$commInfo[$i]['caption'].": ".$commInfo[$i]['target_price'].' cases';
				            $this->Template->assign("comm_level_desc", $comm_desc);
				 
				            //cut valu
				            $com_total_sales = $commInfo[$i]['total_cases'];
				            if( $commInfo[$i]['total_cases']!= 0)
				            {
				            	$com_total_sales = $com_total_sales;
				            }
				            else 
				            	$com_total_sales ="0.00";
				           
				            $this->Template->assign("level_total_cases",$com_total_sales);
				            
				            
				            $this->Template->assign("comm_rate","");
				            
				            $com_bonus = $commInfo[$i]['bonus'];
				    
				            //if( $commInfo[$i]['bonus']== 0)
				            //	$com_bonus = "$".$com_bonus;
				            $dis_val =$this->formatNumer2Float($com_bonus);
				            $this->Template->assign("comm_amount",$dis_val);
				            
				            $sub_total = $sub_total+ $commInfo[$i]['bonus'];
				         }
					}
			        else  //regular
			        {
			         /*Array ( [0] => Array ( [user_id] => 64 [level_id] => 0 [caption] => Level 0 [min_cases] => 0.00 [max_cases] => 0.00 [commission_rate] => 0.00 [total_cases] => 0.00 [commission_amount] => 0.0000 [bonus] => 0.0 ) [1] => Array ( [user_id] => 64 [level_id] => 1 [caption] => Level 1 [min_cases] => 181.00 [max_cases] => 205.00 [commission_rate] => 15.00 [total_cases] => 0.00 [commission_amount] => 0.0000 [bonus] => 0.0 ) [2] => Array ( [user_id] => 64 [level_id] => 2 [caption] => Level 2 [min_cases] => 206.00 [max_cases] => 255.00 [commission_rate] => 20.00 [total_cases] => 0.00 [commission_amount] => 0.0000 [bonus] => 0.0 ) [3] => Array ( [user_id] => 64 [level_id] => 3 [caption] => Level 3 [min_cases] => 256.00 [max_cases] => 1000.00 [commission_rate] => 25.00 [total_cases] => 118.24 [commission_amount] => 253.9204 [bonus] => 0.0 ) )*/
	        
	        /*bonusinfo
	        
	        Array ( [0] => Array ( [sales_commission_level_id] => 443 [province_id] => 1 [sale_date] => 2011-09-01 [user_id] => 64 [lkup_sales_commission_type_id] => 1 [target_cases_ca] => 80.00 [target_cases_intl] => 100.00 [level_start_cases] => 0.00 [level_end_cases] => 0.00 [level_commission_rate] => 0.00 [level_target_sales] => 0.00 [level_commission_bonus] => 0.00 [lkup_commission_sales_sum_type_id] => 0 [level_caption] => Level 0 [level_number] => 0 [when_entered] => 0000-00-00 00:00:00 [when_modified] => 20111018144210 [created_user_id] => 15 [modified_user_id] => 0 ) )
	        
	        bllTotalInfo
			Array ( [0] => Array ( [sales_summary_id] => 3418 [user_id] => 64 [total_canadian_cases] => 50.75 [total_international_cases] => 247.49 [total_cases] => 298.24 [total_canadian_profit] => 421.14 [total_international_profit] => 2140.48 [total_profit] => 2561.62 [avg_profit_per_case] => 8.59 [sale_month] => 9 [sale_year] => 2011 [lkup_store_type_id] => -1 [total_sales] => 24476.86 [total_retail] => 49215.59 [total_units] => 3773 [province_id] => 1 ) ) 
			
			*/
			
			          	//print_r($commInfo);
			          	
			        		$this->Template->globalAssign("second_title",  "Target bonus");		
					  		$this->Template->globalAssign("sales_title",  "Total cases");
					  		$this->Template->globalAssign("rate_title",  "Comm rate");
					  		$this->Template->globalAssign("comm_title",  "Comm amount");
					  		$this->Template->globalAssign("second_title",  "Target bonus:");	
					  		$this->Template->globalAssign("last_title",  "Total:");
							for($i=1;$i<=$nRows-1;$i++)
							{
								$this->Template->createBlock('loop_line');
								$this->Template->assign("row_style", ($i % 2)?"cellA":"cellB");
								
								if($i==$nRows-1)
									$max_cases = "up";
								else
									$max_cases = $commInfo[$i]['max_cases'];
									
								$comm_desc=$commInfo[$i]['caption'].": ".$commInfo[$i]['min_cases']." - ". $max_cases;
								$this->Template->assign("comm_level_desc", $comm_desc);
								
								//cut value
						//	print $targetInfo[0]["min_canadian_cases"];
						
						//echo $bllTotalInfo[0]["total_canadian_cases"];
						echo $bonusinfo[0]["min_canadian_cases"];
						
							
								if($bllTotalInfo[0]["total_canadian_cases"]<$bonusinfo[0]["target_cases_ca"] ||$bllTotalInfo[0]["total_international_cases"]<$bonusinfo[0]["target_cases_intl"])
								{
								 	
									$this->Template->assign("level_total_cases","0.00");
										$this->Template->assign("comm_amount","0.00");
								}
								else
								{
									$this->Template->assign("level_total_cases",$commInfo[$i]['total_cases']);
									
//									$commVal = round($commInfo[$i]['commission_amount'],2);
									$dis_Val = $this->formatNumer2Float($commInfo[$i]['commission_amount'],true);
										$this->Template->assign("comm_amount",$dis_Val);
								}
								
								$this->Template->assign("comm_rate",$commInfo[$i]['commission_rate']."%");
								
							
							if($bllTotalInfo[0]["total_canadian_cases"]<$bonusinfo[0]["target_cases_ca"] ||$bllTotalInfo[0]["total_international_cases"]<$bonusinfo[0]["target_cases_intl"])
								{
								 	$sub_total="0.00";
								}
								else
									$sub_total = $sub_total+ $commInfo[$i]['commission_amount'];
								
							}
						
						}
					
		       
			//total bonus
		
			   if( $bonus_type ==1)
			   {
			//    print $targetInfo[0]["min_canadian_cases"].$targetInfo[0]["min_intl_cases"];
					if(($bonusinfo[0]["total_canadian_cases"]>=$bonusinfo[0]["min_canadian_cases"])&&$bllTotalInfo[0]["total_international_cases"]>=$bonusinfo[0]["min_intl_cases"])
					{					 
						$target_bonus=$bonusinfo[0]["bonus"];
					 
					 	$total_bonus = $sub_total+$target_bonus;
					 //	print $total_bonus;
					}
					else
					{
						$target_bonus = 0;
						$total_bonus = 0;
					}
				}
				else
					$total_bonus = $sub_total;
			
				if( $bonus_type ==2||$bonus_type ==3)//BCLDB
				{					
				 	$total_bonus = number_format($total_bonus, 2, '.', ',');
				 	$total_bonus ="$".$total_bonus;
				 
					$this->Template->globalAssign("target_bonus",  "");//this ist the total_bonus for BCLDB
					$this->Template->globalAssign("total_bonus",  "");
			
					$this->Template->globalAssign("bonus",  $total_bonus);
					
				
					$dis_sub_total = $this->formatNumer2Float($sub_total,true)	;
					$this->Template->globalAssign("sub_total",  ("$".$dis_sub_total));
				}
				else if( $bonus_type ==4)
				{	
				
				 	$total_bonus = number_format($total_bonus, 2, '.', ',');
				 	$total_bonus ="$".$total_bonus;
				 
					$this->Template->globalAssign("target_bonus",  "");//this ist the total_bonus for BCLDB
					$this->Template->globalAssign("total_bonus",  "");
			
					$this->Template->globalAssign("bonus",  $total_bonus);
					
				
					$dis_sub_total = $this->formatNumer2Float($sub_total,true)	;
					$this->Template->globalAssign("sub_total",  ("$".$dis_sub_total));
				}
				else
				{
				 //	$total_bonus ="$".round($total_bonus,2);
				
				 	$dis_val = $this->formatNumer2Float($target_bonus);
					$this->Template->globalAssign("target_bonus",  "$".$dis_val);
				 	
					 $total_bonus ="$".$this->formatNumer2Float($total_bonus,true);
					$this->Template->globalAssign("total_bonus",  $total_bonus);
					$this->Template->globalAssign("bonus",  $total_bonus);
					$sub_total_s ="$".round($sub_total,2);
					
					if($bllTotalInfo[0]["total_canadian_cases"]<$bonusinfo[0]["min_canadian_cases"] or $bllTotalInfo[0]["total_international_cases"]<$bonusinfo[0]["min_intl_cases"])
					{
							$this->Template->globalAssign("sub_total",  "$"."0.00");		
					}
					else					
						$this->Template->globalAssign("sub_total",  $sub_total_s);
					
				}
	
				if($bonus_type==3)//BCLDB and regular
				{
				 	if($bllTotalInfo[0]["lkup_store_type_id"]==6)
				 		$total_ca_cases = $bllTotalInfo[1]["total_canadian_cases"];
				 	else
				 		$total_ca_cases = $bllTotalInfo[0]["total_canadian_cases"];
				 		
					$this->Template->globalAssign("total_ca_cases",  $total_ca_cases);	
					
					$international_cases = $bllTotalInfo[0]["total_international_cases"]+$bllTotalInfo[1]["total_international_cases"];
					
					$this->Template->globalAssign("total_in_cases",  $international_cases);
					
					$total_cases = $total_ca_cases+$international_cases;
				}
				else
				{
		  			$this->Template->globalAssign("total_ca_cases",  $bllTotalInfo[0]["total_canadian_cases"]);
					$this->Template->globalAssign("total_in_cases",  $bllTotalInfo[0]["total_international_cases"]);
				
					$total_cases = $bllTotalInfo[0]["total_canadian_cases"]+$bllTotalInfo[0]["total_international_cases"];
				}
			
				$total_cases =$this->formatNumer2Float($total_cases,true);
				$this->Template->globalAssign("total_cases",  $total_cases);
		    	
				if($bonus_type==3)//BCLDB and regular
				{
				 	
				 	$total_profit = number_format(($bllTotalInfo[0]["total_profit"]+$bllTotalInfo[1]["total_profit"]), 2, '.', ',');
				}	     
				else
		      		$total_profit = number_format($bllTotalInfo[0]["total_profit"], 2, '.', ',');
		      	
		      	if( $total_profit==0)
		      		$total_profit ="0.00";

				$this->Template->globalAssign("total_profit",  $total_profit);
			
				if($bonus_type==3)//BCLDB and regular
				{
				 	
				 	$avgProfit = number_format(($bllTotalInfo[0]["avg_profit_per_case"]+$bllTotalInfo[1]["avg_profit_per_case"])/2, 2, '.', ',');
				}	     
				else
		      		$avgProfit = number_format($bllTotalInfo[0]["avg_profit_per_case"], 2, '.', ',');
		      		
		      		
				$this->Template->globalAssign("ave_profit_per_case",  $avgProfit);
			
			 	$tltbounus= str_replace("$","",$total_bonus);
		      	$tltbounus= str_replace(",","",$tltbounus);
		      
		      	if($bonus_type==3)
					$net_profit = ($bllTotalInfo[0]["total_profit"]+$bllTotalInfo[1]["total_profit"])- floatval($tltbounus);
				else
					$net_profit = $bllTotalInfo[0]["total_profit"]- floatval($tltbounus);
				
				$net_profit = number_format($net_profit, 2, '.', ',');
			
				$this->Template->globalAssign("net_profit",  $net_profit);
	

    }

	function _buildContent_NewSpeical($userType)
    {//GetSpecialSalesSummery
 	
 		if($userType==1)
				$user_name = "Not Assigned";
			if($userType==2)
				$user_name = "Samples";
			if($userType==3)
				$user_name = "NWT Liquor Commission";
			if($userType==4)
				$user_name = "Saskatchewan Liquor Board";
			if($userType==5)
				$user_name = "Yukon Liquor Corp";
				
 		$this->Template->globalAssign("user_name",  $user_name);   
		
		$bllSSDS = new SSDSData();
	        $bllTotalInfo = $bllSSDS->GetSpecialSalesSummery($this->sale_month, $this->sale_year, $userType,$this->province_id, 2);
        
		        		     	
							$this->Template->globalAssign("sales_title",  "Total cases");
			        		$this->Template->globalAssign("rate_title",  "");		        	
			        		$this->Template->globalAssign("comm_title",  "Commissons");		
			        		$this->Template->globalAssign("second_title",  "");		
			        		$this->Template->globalAssign("Last_title",  "");	
							

				         for($i=1;$i<=3;$i++)
				        	{
						      $this->Template->createBlock('loop_line');
				            $this->Template->assign("row_style", ($i % 2)?"cellA":"cellB");
				            
				            //$t_price = 
				            $comm_desc="Level $i: 0 cases";
				            $this->Template->assign("comm_level_desc", $comm_desc);
				 
				            //cut valu
				            $com_total_sales = "0.00";
				           
				            $this->Template->assign("level_total_cases",$com_total_sales);
				            
				            
				            $this->Template->assign("comm_rate","");
				            
				            $com_bonus = "0.00";
				            $dis_val =$this->formatNumer2Float($com_bonus);
				            $this->Template->assign("comm_amount",$dis_val);
				            
				        
				         }
	
				 	$total_bonus ="0.00";
				 	
		  			$this->Template->globalAssign("total_ca_cases",  "0.00");
					$this->Template->globalAssign("total_in_cases",  $bllTotalInfo[0]["total_international_cases"]);

				 
					$this->Template->globalAssign("target_bonus",  "");//this ist the total_bonus for BCLDB
					$this->Template->globalAssign("total_bonus",  "");
			
					$this->Template->globalAssign("bonus",  ("$"."0.00"));
					
				
			
					$this->Template->globalAssign("sub_total",  ("$"."0.00"));
		
			
				$total_cases =$this->formatNumer2Float($bllTotalInfo[0]["total_cases"],true);
				$this->Template->globalAssign("total_cases",  $total_cases);
		    	
			
		      	$total_profit = number_format($bllTotalInfo[0]["total_profit"], 2, '.', ',');
		      	
		      	if( $total_profit==0)
		      		$total_profit ="0.00";

				$this->Template->globalAssign("total_profit",  $total_profit);
			
				$avgProfit = number_format($bllTotalInfo[0]["avg_profit_per_case"], 2, '.', ',');
		      		
		      		
				$this->Template->globalAssign("ave_profit_per_case",  $avgProfit);
			
			
				$this->Template->globalAssign("net_profit",  $total_profit);
	

    }

	function _buildContent_Old()
    {
 
        
      
	  $this->Template->globalAssign("user_name",  $this->user_name);   
        
      $bllSSDS = new SSDSData();
      
      $bonusinfo= $bllSSDS->getUserBonusType($this->user_id,$this->province_id);
      $bonus_type = $bonusinfo[0]["lkup_store_type_id"];
        	
        
       	if($this->store_type==-1)
       	{       	 
		       //	print $bonus_type."000".$this->province_id;
		       
		        $commInfo = $bllSSDS->GetCommissionsReport($this->sale_month,$this->sale_year,$this->user_id,$bonus_type,$this->province_id);
		        	

		        $aRow = 0;
		        
		        $nRows= count($commInfo);
		        
		       
		        
		        $sub_total =0;
		        
			    //	$targetInfo = $bllSSDS->getTargetCommissionLeveInfo($this->sale_month,$this->sale_year);
			    	
			  //     	$bllSSDS = new SSDSData();
			
			$targetInfo = $bllSSDS->getTargetCommissionLeveInfo($this->sale_month,$this->sale_year);
			
//			$cntlInter = & $this->form->getField("target_inter_cases");
	//	print "int1: ".$targetInfo[0]["min_canadian_cases"];
//			$cntlCa = & $this->form->getField("target_ca_cases");
//			$cntlCa->setValue($targetInfo[0]["min_canadian_cases"]);
          
		    //total cases
			   $bllTotalInfo =$bllSSDS->getTotalCasesById($this->user_id,$this->sale_month,$this->sale_year,$bonus_type);
	        
	              if( $bonus_type ==6)
	              {
			      		
			        		$this->Template->globalAssign("sales_title",  "Total sales");
			        		$this->Template->globalAssign("rate_title",  "");		        	
			        		$this->Template->globalAssign("comm_title",  "Commissons");		
			        		$this->Template->globalAssign("second_title",  "");		
			        		$this->Template->globalAssign("Last_title",  "");		
				         for($i=0;$i<=$nRows-1;$i++)
				        	{
						      $this->Template->createBlock('loop_line');
				            $this->Template->assign("row_style", ($i % 2)?"cellA":"cellB");
				            
				            //$t_price = 
				            $comm_desc=$commInfo[$i]['caption'].": $".number_format($commInfo[$i]['target_price'],2, '.', ',');
				            $this->Template->assign("comm_level_desc", $comm_desc);
				 
				            //cut valu
				            $com_total_sales = $commInfo[$i]['total_sales'];
				            if( $commInfo[$i]['total_sales']!= 0)
				            {
				            	$com_total_sales = "$".$this->formatNumer2Float($com_total_sales,true);
				            }
				            else 
				            	$com_total_sales ="$0.00";
				           
				            $this->Template->assign("level_total_cases",$com_total_sales);
				            
				            
				            $this->Template->assign("comm_rate","");
				            
				            $com_bonus = $commInfo[$i]['bonus'];
				            //if( $commInfo[$i]['bonus']== 0)
				            //	$com_bonus = "$".$com_bonus;
				            $dis_val =$this->formatNumer2Float($com_bonus);
				            $this->Template->assign("comm_amount",$dis_val);
				            
				            $sub_total = $sub_total+ $commInfo[$i]['bonus'];
				         }
			         }
			         else if($bonus_type ==8 )
			         { 
						
							$this->Template->globalAssign("sales_title",  "Total cases");
			        		$this->Template->globalAssign("rate_title",  "");		        	
			        		$this->Template->globalAssign("comm_title",  "Commissons");		
			        		$this->Template->globalAssign("second_title",  "");		
			        		$this->Template->globalAssign("Last_title",  "");		
				         for($i=0;$i<=$nRows-1;$i++)
				        	{
						      $this->Template->createBlock('loop_line');
				            $this->Template->assign("row_style", ($i % 2)?"cellA":"cellB");
				            
				            //$t_price = 
				            $comm_desc=$commInfo[$i]['caption'].": ".$commInfo[$i]['target_price'].' cases';
				            $this->Template->assign("comm_level_desc", $comm_desc);
				 
				            //cut valu
				            $com_total_sales = $commInfo[$i]['total_cases'];
				            if( $commInfo[$i]['total_cases']!= 0)
				            {
				            	$com_total_sales = $com_total_sales;
				            }
				            else 
				            	$com_total_sales ="0.00";
				           
				            $this->Template->assign("level_total_cases",$com_total_sales);
				            
				            
				            $this->Template->assign("comm_rate","");
				            
				            $com_bonus = $commInfo[$i]['bonus'];
				            //if( $commInfo[$i]['bonus']== 0)
				            //	$com_bonus = "$".$com_bonus;
				            $dis_val =$this->formatNumer2Float($com_bonus);
				            $this->Template->assign("comm_amount",$dis_val);
				            
				            $sub_total = $sub_total+ $commInfo[$i]['bonus'];
				         }
						}
			         else 
			         {
			        		$this->Template->globalAssign("second_title",  "Target bonus");		
					  		$this->Template->globalAssign("sales_title",  "Total cases");
					  		$this->Template->globalAssign("rate_title",  "Comm rate");
					  		$this->Template->globalAssign("comm_title",  "Comm amount");
					  		$this->Template->globalAssign("second_title",  "Target bonus:");	
					  		$this->Template->globalAssign("last_title",  "Total:");
							for($i=1;$i<=$nRows-1;$i++)
							{
								$this->Template->createBlock('loop_line');
								$this->Template->assign("row_style", ($i % 2)?"cellA":"cellB");
								
								if($i==$nRows-1)
									$max_cases = "up";
								else
									$max_cases = $commInfo[$i]['max_cases'];
									
								$comm_desc=$commInfo[$i]['caption'].": ".$commInfo[$i]['min_cases']." - ". $max_cases;
								$this->Template->assign("comm_level_desc", $comm_desc);
								
								//cut value
						//	print $targetInfo[0]["min_canadian_cases"];
						
								if($bllTotalInfo[0]["total_canadian_cases"]<$targetInfo[0]["min_canadian_cases"] or $bllTotalInfo[0]["total_international_cases"]<$targetInfo[0]["min_intl_cases"])
								{
									$this->Template->assign("level_total_cases","0.00");
										$this->Template->assign("comm_amount","0.00");
								}
								else
								{
									$this->Template->assign("level_total_cases",$commInfo[$i]['total_cases']);
									
//									$commVal = round($commInfo[$i]['commission_amount'],2);
									$dis_Val = $this->formatNumer2Float($commInfo[$i]['commission_amount'],true);
										$this->Template->assign("comm_amount",$dis_Val);
								}
								
								$this->Template->assign("comm_rate",$commInfo[$i]['commission_rate']."%");
								
							
							
								
								$sub_total = $sub_total+ $commInfo[$i]['commission_amount'];
							}
						
						}
					
		       
			//total bonus
			   
			   if( $bonus_type !=6 && $bonus_type !=8)
			   {
			//    print $targetInfo[0]["min_canadian_cases"].$targetInfo[0]["min_intl_cases"];
					if(($bllTotalInfo[0]["total_canadian_cases"]>=$targetInfo[0]["min_canadian_cases"])&&$bllTotalInfo[0]["total_international_cases"]>=$targetInfo[0]["min_intl_cases"])
					{
					 
					
						$target_bonus=$targetInfo[0]["bonus"];
					 
					 	$total_bonus = $sub_total+$target_bonus;
					 //	print $total_bonus;
					}
					else
					{
						$target_bonus = 0;
						$total_bonus = 0;
					}
				}
				else
					$total_bonus = $sub_total;
			
				if( $bonus_type ==6)
				{	
				
				 	$total_bonus = number_format($total_bonus, 2, '.', ',');
				 	$total_bonus ="$".$total_bonus;
				 
					$this->Template->globalAssign("target_bonus",  "");//this ist the total_bonus for BCLDB
					$this->Template->globalAssign("total_bonus",  "");
			
					$this->Template->globalAssign("bonus",  $total_bonus);
					
				
					$dis_sub_total = $this->formatNumer2Float($sub_total,true)	;
					$this->Template->globalAssign("sub_total",  ("$".$dis_sub_total));
				}
				else if( $bonus_type ==8)
				{	
				
				 	$total_bonus = number_format($total_bonus, 2, '.', ',');
				 	$total_bonus ="$".$total_bonus;
				 
					$this->Template->globalAssign("target_bonus",  "");//this ist the total_bonus for BCLDB
					$this->Template->globalAssign("total_bonus",  "");
			
					$this->Template->globalAssign("bonus",  $total_bonus);
					
				
					$dis_sub_total = $this->formatNumer2Float($sub_total,true)	;
					$this->Template->globalAssign("sub_total",  ("$".$dis_sub_total));
				}
				else
				{
				 //	$total_bonus ="$".round($total_bonus,2);
				
				 	$dis_val = $this->formatNumer2Float($target_bonus);
					$this->Template->globalAssign("target_bonus",  "$".$dis_val);
				 	
					 $total_bonus ="$".$this->formatNumer2Float($total_bonus,true);
					$this->Template->globalAssign("total_bonus",  $total_bonus);
					$this->Template->globalAssign("bonus",  $total_bonus);
					$sub_total_s ="$".round($sub_total,2);
					
					if($bllTotalInfo[0]["total_canadian_cases"]<$targetInfo[0]["min_canadian_cases"] or $bllTotalInfo[0]["total_international_cases"]<$targetInfo[0]["min_intl_cases"])
					{
							$this->Template->globalAssign("sub_total",  "$"."0.00");		
					}
				else
				
					$this->Template->globalAssign("sub_total",  $sub_total_s);
					
				}
				
		  		$this->Template->globalAssign("total_ca_cases",  $bllTotalInfo[0]["total_canadian_cases"]);
				$this->Template->globalAssign("total_in_cases",  $bllTotalInfo[0]["total_international_cases"]);
				
				$total_cases = $bllTotalInfo[0]["total_canadian_cases"]+$bllTotalInfo[0]["total_international_cases"];
			
				$total_cases =$this->formatNumer2Float($total_cases,true);
				$this->Template->globalAssign("total_cases",  $total_cases);
		    		     
	      	$total_profit = number_format($bllTotalInfo[0]["total_profit"], 2, '.', ',');
		      	
		      if( $total_profit==0)
		      	$total_profit ="0.00";

				$this->Template->globalAssign("total_profit",  $total_profit);
			
				$this->Template->globalAssign("ave_profit_per_case",  $bllTotalInfo[0]["avg_profit_per_case"]);
			
			 	$tltbounus= str_replace("$","",$total_bonus);
		      $tltbounus= str_replace(",","",$tltbounus);
		      
				$net_profit = $bllTotalInfo[0]["total_profit"]- floatval($tltbounus);
				
				$net_profit = number_format($net_profit, 2, '.', ',');
			
				$this->Template->globalAssign("net_profit",  $net_profit);
		}

    }

	function formatNumer2Float($numVal,$isRound=false)
	{
		if($isRound) 	
		{
			$numVal =round($numVal,2);
		}
		$retVal=number_format($numVal, 2, '.', ',');
		
	
		return $retVal;
		
	}
    function getContent()
    {
	
	        $this->_buildContent();
	        return $this->Template->getContent();
    
    }



	function getCut($listVal,$l)
	{
		$retVal = "";
		if ($listVal != Null && trim($listVal)!="")
		{
            $retVal =$listVal;
           // print strlen($listVal).'   ';
			if (strlen($listVal)>$l)
			{
				//print herer;
				$retVal = substr($listVal,0,$l).'...';
  			}
		}
		return $retVal;

 	}
}
?>
