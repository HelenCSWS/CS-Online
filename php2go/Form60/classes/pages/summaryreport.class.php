<?php

/**
 * Perform the necessary imports
 */

import('Form60.base.F60FormBase');
import('Form60.base.F60MarketList');
import('Form60.util.F60Date');
import('Form60.bll.bllcontacts');
import('Form60.base.ssdsCommissionList');
import('Form60.base.ssdsSummaryList');


class summaryreport extends F60FormBase
{
	//var $estate_id ;
    var $isPrint=true;
		var $period_id;

		var $user_id;
		var $store_type;
		var $user_name;
		var $users = 1;
		var $bonus_type=-1;
		
		var $sale_month;
		var $sale_year;
		var $province_id =1;
		
		var $isNewRule=false;

		function summaryreport()
		{
	

            F60FormBase::F60FormBase('summaryreport', "Sales summary report", 'summaryreport.xml', 'summaryreport.tpl', 'btnAdd');
            $this->addScript('resources/js/javascript.ssdsreports.js');
            $this->addScript('resources/js/javascript.uploadSSDS.js');
          
				
            $form = & $this->getForm();
      
   			$this->form->setInputStyle('input');

			$this->sale_month =$_REQUEST["sale_month"];
			
			$this->sale_year =$_REQUEST["sale_year"];
			$this->period_id =$_REQUEST["sale_month"];
			
            $edtPeriodId=& $this->form->getField("sale_month");
            $edtPeriodId->setValue($_REQUEST["sale_month"]);

            $edtYear=& $this->form->getField("sale_year");
            $edtYear->setValue($_REQUEST["sale_year"]);
 
				 
           $edtRecreate=& $this->form->getField("is_recreate");
            $edtRecreate->setValue($_REQUEST["is_recreate"]);
 				 
            $this->user_id=$_REQUEST["user_id"];
           
            
            if($this->user_id==-1)
	            $this->store_type=-1;
            else
   	         $this->store_type=$_REQUEST["store_type"];

 
            $cmbStoreType=& $this->form->getField("store_type");
            $cmbStoreType->setValue($this->store_type);
            
            $cmbStoreType=& $this->form->getField("store_types");
            $cmbStoreType->setValue($this->store_type);
            
            
            
            $edtCurrentUserId=& $this->form->getField("current_user_id");
            $edtCurrentUserId->setValue($_REQUEST["user_id"]);
            
    			
		    $this->province_id = $_COOKIE["F60_PROVINCE_ID"];
    			
    			
    		Registry::set('current_province_id', $this->province_id);
    			
    			
            $cntl = & $this->form->getField("period_desc");
      		$cntl->setStyle("text");
            $cntl = & $this->form->getField("target_inter_cases");
      		$cntl->setStyle("text");
            $cntl = & $this->form->getField("target_ca_cases");
      		$cntl->setStyle("text");
      		
            $this->form->setButtonStyle('btnOK');
            
            $this->attachBodyEvent('onLoad', 'initReport();');
            
			$bllSSDS= new SSDSData();
			
			$this->isNewRule=$bllSSDS->isNewRule($this->sale_year,$this->sale_month);
			
		
    	}


    	function display()
    	{
            if (!$this->handlePost())
                $this->displayForm();
      }

		function displayForm()
		{
	
		 	if($this->isNewRule)
		 		$this->loadDataByNewRule();
		 	else
		 	{
	
				$this->loadData();
			}
	
			
			
			$display_store_type = $this->store_type;
			
			$edtUser_id = & $this->form->getField("user_id");
			$user_id = $edtUser_id->getValue();
			
	
			$user_name ="All consultant";

			if($this->isNewRule)				
			{
				if($this->province_id ==1)
				{
					$action = array(
						"Export BCLDB and Regular"=>"javascript:showExcelReportByNewRule($this->sale_month,$this->sale_year, -1, 3,$this->province_id);",
						"Export BCLDB"=>"javascript:showExcelReportByNewRule($this->sale_month,$this->sale_year, -1, 2,$this->province_id);",
						"Export Licensee"=>"javascript:showExcelReportByNewRule($this->sale_month,$this->sale_year, -1, 1,$this->province_id);",			
					);
				}
				else if($this->province_id ==2)
				{
				 
						$action = array(
							"Export Alberta"=>"javascript:showExcelReportByNewRule($this->sale_month,$this->sale_year, -1, 4,$this->province_id);",
							"By store"=>"javascript:showBreakdownReport($user_id,'$user_name',1,$this->sale_month,$this->sale_year,$this->province_id);",	
							"By wine"=>"javascript:showBreakdownReport($user_id,'$user_name', 2,$this->sale_month,$this->sale_year,$this->province_id);",
						);
				
					
				}
			}
			else
			{
				if($this->province_id ==1)
				{
					$action = array(
						"Export BCLDB"=>"javascript:showExcelReport($this->sale_month,$this->sale_year, -1, 6);",
						"Export Licensee"=>"javascript:showExcelReport($this->sale_month,$this->sale_year, -1, -1);",			
					);
				}
				else if($this->province_id ==2)
				{
				 	if($user_id!=-1)
				 	{
				 	
						$action = array(
							"Export Alberta"=>"javascript:showExcelReport($this->sale_month,$this->sale_year, -1, 38);",
							"By store"=>"javascript:showBreakdownReport($user_id,'$user_name',1,$this->sale_month,$this->sale_year);",	
							"By wine"=>"javascript:showBreakdownReport($user_id,'$user_name', 2,$this->sale_month,$this->sale_year);",
							);
					}
					else
					{
						$action = array(
							"Export Alberta"=>"javascript:showExcelReport($this->sale_month,$this->sale_year, -1, 8);",
						);
					}
					
				}
			}
			$this->setActions($action);
			F60FormBase::display();
        
		}
		
		function loadDataByNewRule()
		{
		   $form = & $this->getForm();
		
			$bllSSDS= new SSDSData();
		
			$bonusinfo= $bllSSDS->getTargetCommissionLeveInfo($this->sale_month,$this->sale_year,$this->user_id);


	 	 	$is_recreate = $_REQUEST["is_recreate"];

	        
			$this->bonus_type = $bonusinfo[0]["lkup_sales_commission_type_id"];
			
 			
 			$edtBonuType= & $this->form->getField("bonus_type");
 			$edtBonuType->setValue($this->bonus_type);
	 	 	
	 	 	
			if($is_recreate ==1)
			{
			
				$bllSSDS->setUserID($this->getCurrentUserID());
				$bllSSDS->regenerateSummaryData($this->sale_month,$this->sale_year,$this->province_id);
			}
		   
			$this->setUsers($this->period_id,$this->user_id);
			$this->getPeriodInfo($this->period_id);
			
			$this->loadReportByNewRule($this->user_id);
			
			if($this->bonus_type==1)
			{
				$cntlInter = & $this->form->getField("target_inter_cases");
				$cntlInter->setValue($bonusinfo[0]["min_intl_cases"]);
				$cntlCa = & $this->form->getField("target_ca_cases");
				$cntlCa->setValue($bonusinfo[0]["min_canadian_cases"]);
			}
			
		}
		

		function loadData()
		{
		 
		   $form = & $this->getForm();
		
			$bllSSDS= new SSDSData();
	
	 	 	$is_recreate = $_REQUEST["is_recreate"];

         	if($this->user_id!=-1)
         	{
         	 
				$bonusinfo= $bllSSDS->getUserBonusType($this->user_id,$this->province_id);
				$this->bonus_type = $bonusinfo[0]["lkup_store_type_id"];
			}
 			
 			$edtBonuType= & $this->form->getField("bonus_type");
 			$edtBonuType->setValue($this->bonus_type);
	 	 	
			if($is_recreate ==1)
			{
							
				$bllSSDS->setUserID($this->getCurrentUserID());
				$bllSSDS->regenerateSummaryData($this->sale_month,$this->sale_year,$this->province_id);
			}
		

      
			$this->setUsers($this->period_id,$this->user_id);
			$this->getPeriodInfo($this->period_id);
			

			
			$this->loadReport($this->user_id,$this->store_type);
			

			
			if($this->bonus_type!=6)
				$this->getTargetInfo();
		}
		

		function loadReportByNewRule($user_id)
		{
		 
		 
			if($this->user_id==-1)//mutiple user
			{
			 			
	   	 	   $bllSSDS = new SSDSData();
		       $users = $bllSSDS->getUsersByDate($this->sale_month,$this->sale_year,$this->province_id);
		       
	//	       print_r($users);
		       
		       $nRows = count($users);
		       
		   	
		       $edtUsers = & $this->form->getField("users");
		       $edtUsers->setValue($nRows);
		   
		   	//	$nRows = 1;
		       for($i=0;$i<$nRows;$i++)
		       {
		        	
		      
		        	$user_id   = $users[$i]["user_id"];
		        	$user_name = $users[$i]["user_name"];
		        //	print $store_type;
		        
		        //	$user_id = 6;
		       
					$listControl = & new ssdsCommissionList(&$this, $user_id, $this->sale_month,$this->sale_year,$user_name,$this->province_id);
				
					$comm_content = "commissions".$i;
    	        	$this->form->Template->assign($comm_content, $listControl->getContent()); 
    	        	
    	        
			   }
			   $totalContent=$i;
			   
				for($i=1; $i<=5; $i++)
			    {
			     	
					$indexCol=$i+$totalContent;
					
			
					
					$bllSSDS = new SSDSData();
					$bllTotalInfo = $bllSSDS->GetSpecialSalesSummery($this->sale_month, $this->sale_year, $i,$this->province_id, 2);
					if(count($bllTotalInfo)!=0)
					{
						if($i==1)
						{
							$listControl = & new ssdsCommissionList(&$this, 0, $this->sale_month,$this->sale_year,$user_name,$this->province_id,$i);
				
							$comm_content = "commissions".($indexCol);
		    	        	$this->form->Template->assign($comm_content, $listControl->getContent()); 
		    	        	if($this->province_id ==1)
		    	        		break;
						}
						else
						{
							if($this->province_id ==2)
							{
						        //Sample
						    	$listControl = & new ssdsCommissionList(&$this, 0, $this->sale_month,$this->sale_year,$user_name,$this->province_id,$i);
					
								$comm_content = "commissions".$indexCol;
	    	        			$this->form->Template->assign($comm_content, $listControl->getContent()); 
							
						    }
						}
					}
					
				}
			   
			  
			}	
			else //one user
			{
			 	$store_type =-1; // user a dummy type to pass to parameter
				$reportControl = & new ssdsSummaryList(&$this, $this->user_id, $this->sale_month,$this->sale_year,$store_type,$this->bonus_type);
    	        $this->form->Template->assign("summarylist", $reportControl->getContent()); 
    	        
			 	if($store_type==-1)
			 	{
					$listControl = & new ssdsCommissionList(&$this, $this->user_id,$this->sale_month,$this->sale_year,"",$store_type,$this->bonus_type);
    	        	$this->form->Template->assign("commissions0", $listControl->getContent()); 
				}
			}
			

		}

		function loadReport($user_id,$store_type)
		{
		 
			if($this->user_id==-1)//mutiple user
			{
			 			
	   	 	   $bllSSDS = new SSDSData();
		       $users = $bllSSDS->getUsersByDate($this->sale_month,$this->sale_year,$this->province_id);
		       $nRows = count($users);
		       
		   		       
		       $edtUsers = & $this->form->getField("users");
		       $edtUsers->setValue($nRows);
		   
		       for($i=0;$i<$nRows;$i++)
		       {
		      
		        	$user_id   = $users[$i]["user_id"];
		        	$user_name = $users[$i]["user_name"];
		        //	print $store_type;
		        
		        			
					$listControl = & new ssdsCommissionList(&$this, $user_id, $this->sale_month,$this->sale_year,$user_name,$store_type,$this->province_id);
			
				
					$comm_content = "commissions".$i;
    	        	$this->form->Template->assign($comm_content, $listControl->getContent()); 
			   }
			   
			   			
			}	
			else //one user
			{
				$reportControl = & new ssdsSummaryList(&$this, $this->user_id, $this->sale_month,$this->sale_year,$store_type,$this->bonus_type);
    	        $this->form->Template->assign("summarylist", $reportControl->getContent()); 
    	        
			 	if($store_type==-1)
			 	{
					$listControl = & new ssdsCommissionList(&$this, $this->user_id,$this->sale_month,$this->sale_year,"",$store_type,$this->bonus_type);
    	        	$this->form->Template->assign("commissions0", $listControl->getContent()); 
				}
			}
			

		}


		function getTargetInfo()
		{
			$bllSSDS = new SSDSData();
			
			$targetInfo = $bllSSDS->getTargetCommissionLeveInfo($this->sale_month,$this->sale_year);
			
			$cntlInter = & $this->form->getField("target_inter_cases");
			$cntlInter->setValue($targetInfo[0]["min_intl_cases"]);
			$cntlCa = & $this->form->getField("target_ca_cases");
			$cntlCa->setValue($targetInfo[0]["min_canadian_cases"]);
		}

		function getPeriodInfo($period_id)
		{
			$bllSSDS = new SSDSData();
		
			$period_desc = F60Date::getMonthTxt($this->period_id)." ".$this->sale_year;
			
			$desc = "$period_desc sales summary report for";
		
			$txtInfo=& $this->form->getField("period_desc");
			$txtInfo->setValue($desc);
			
		}
		function setUsers($period_id,$user_id)
		{
		 
			$cmbUser=& $this->form->getField("user_id");
			$bllSSDS = new SSDSData();
			$users = $bllSSDS->getUsersByDate($period_id,$this->sale_year,$this->province_id);
			$nUsers = count($users);
			
			$cmbUser->addOption("-1","All consultants",0);
			
			for($i=0;$i<=$nUsers-1;$i++)
			{
				$user_name =$users[$i]['user_name'];
				$user_id =$users[$i]['user_id'];
				
				$nIndex=$i+1;
				$cmbUser->addOption($user_id,$user_name,$user_name);		
			}
			
			$cmbUser->setValue($this->user_id);
		    
		}
		
		function processForm()
		{
		
		}


}

?>
