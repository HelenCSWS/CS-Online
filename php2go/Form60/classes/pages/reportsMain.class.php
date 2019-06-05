<?php

/**
 * Perform the necessary imports
 */

import('Form60.base.F60FormBase');
import('Form60.base.F60DbUtil');
import('Form60.base.F60DALBase');
import('Form60.bll.bllStorePenetrationData');
import('Form60.bll.bllAbVenderReports');
import('Form60.bll.bllSalesAnalysisData');


class reportsMain extends F60FormBase
{
	var $search_id ;
	var $estate_name;
	var $sURL;
	var $province_id;

	var $report_id =0;
	function reportsMain()
	{
		if (F60FormBase::getCached()) exit(0);
		
		// $this->search_id = $_REQUEST['searchid'];
		// $funstring = 'initForm('.$this->search_id.');';
		// print $funstring;
		$title = "Reports";
		
		$province_id=$_COOKIE["F60_PROVINCE_ID"];
		
		if($_REQUEST["reportId"]!="")
		{
			$this->report_id =$_REQUEST["reportId"];
		}
		
		if($province_id==2)//Alberta
		{
			F60FormBase::F60FormBase('reportsMain', $title, 'reportsMainAB.xml', 'reportsMainAB.tpl');			
		}
		else
		{
			F60FormBase::F60FormBase('reportsMain', $title, 'reportsMain.xml', 'reportsMain.tpl');			
		}

		
		//$this->addToPageStack();
		$this->province_id = $province_id;
		
		
		$form = & $this->getForm();
		$URL ='main.php?page_name=reportsMain.php';
		$form->setFormAction($URL);
		
		$this->form->setButtonStyle('btnOK');
		$this->form->setInputStyle('input');
		$this->form->setLabelStyle('label');
		
		Registry::set('current_user_province_id', $province_id);	
		Registry::set('current_store_province_id', $province_id);	
				
		$login_user_id = & F60DALBase::get_current_user_id();
		Registry::set('login_user_id', $login_user_id);
		
		
		$login_user_level = & F60DALBase::get_current_user_level();
	
	
		
		Registry::set('login_user_level', $login_user_level);
		
		$cmbUser = & $form->getField("user_id");
		$cmbUser ->setValue($login_user_id);
		
		$cmbLoginUser = & $form->getField("login_user_id");
		$cmbLoginUser ->setValue($login_user_id);
		
		$cmbLoginUserLevel = & $form->getField("login_user_level");
		$cmbLoginUserLevel ->setValue($login_user_level);
		
		$cmbLoginProvince = & $form->getField("login_pro");
		$cmbLoginProvince ->setValue($province_id);
		
		
		
		if($province_id==2)//Alberta
		{
			$this->attachBodyEvent('onLoad', 'initForm4AB();');
		}
		else //BC
		{
			$cmbSPType = & $form->getField("current_sp_type");
		
			if($login_user_id ==23 || $login_user_id ==44||$login_user_id ==51||$login_user_id ==15||$login_user_id ==54||$login_user_id ==43)
			{
				$cmbSPType->setValue(0);
			
			}
			else
			{
				$cmbSPType->setValue(1);	
			}
			$this->attachBodyEvent('onLoad', 'hidelast();');		
			$this->attachBodyEvent('onLoad', 'setCalendar();');
			
			
			$this->attachBodyEvent('onLoad', "initReport($this->report_id);");		
			
			$cntl = & $this->form->getField("sp_location_name");
			$cntl->setStyle("text");
		}
        
        		$this->addScript(PHP2GO_JAVASCRIPT_PATH . "libs/cookie.js");
		$this->addScript('resources/js/javascript.reports.js');

    }

	function display()
	{
		if (!$this->handlePost())
		{
		 	if($this->province_id ==2)//Albera
				$this->displayForm_AB();

		 	else
				$this->displayForm();
		}		
		
				$this->setValueToSalesAnaYearsCtnl($this->province_id);
		F60FormBase::display();
    }

	function displayForm_AB()
    {
        $form = & $this->getForm();
        
        //$this->setSalesYearsToControl(1);        
        
    }
    function displayForm()
    {
        $form = & $this->getForm();


        $month = date(m);
        $year = date(y);
        $last_day_of_month = date("d", mktime(0, 0, 0, $month+1, 0, $year ));

        $year = date(Y);
        if (strlen($month)==1)
        {
            $month = '0'.$month;
        }
        $last_date_of_month = $month.'/'.$last_day_of_month.'/'.$year;
       	$first_date_of_month = $month.'/01'.'/'.$year;


		$edtDate =& $form->getField("to_1");
		$edtDate->setValue($last_date_of_month);
		
		$edtDate =& $form->getField("to_2");
		$edtDate->setValue($last_date_of_month);
		
		$edtDate =& $form->getField("to_3");
		$edtDate->setValue($last_date_of_month);
		
		$edtDate =& $form->getField("to_6");
		$edtDate->setValue($last_date_of_month);
        
        $today=date("m/d/Y");
        $edtDate =& $form->getField("to_cs");
		$edtDate->setValue($today);
		
		$edtDate =& $form->getField("to_city");
		$edtDate->setValue($last_date_of_month);
		
		$edtDate =& $form->getField("from_1");
		$edtDate->setValue($first_date_of_month);
		
		$edtDate =& $form->getField("from_2");
		$edtDate->setValue($first_date_of_month);
		
		$edtDate =& $form->getField("from_3");
		$edtDate->setValue($first_date_of_month);
		
		$edtDate =& $form->getField("from_6");
		$edtDate->setValue($first_date_of_month);
		
		$edtDate =& $form->getField("from_city");
		$edtDate->setValue($first_date_of_month);
        
        	$edtDate =& $form->getField("from_cs");
		$edtDate->setValue($first_date_of_month);

        $edtEstate =& $form->getField("estate_id_5");
		$estateid = $this->getEstateid();

        if ($estateid!="")
        {
            $edtEstate->setFirstOption($estateid);
            $result =reportsMain::getWines($estateid);


    		$comWineid5=& $form->getField("wine_id_5");
    		$comWineid6=& $form->getField("wine_id_6");
    		$i=0;
            while(!$result->EOF)
            {
    		 			$row=& $result->FetchRow();

                  $comWineid5->addOption($row['wine_id'],$row['wine_name'],$i);
                  $comWineid6->addOption($row['wine_id'],$row['wine_name'],$i);

                  $i++;
    			}
        }
		  
		  //store penetration current data
		  
	/*	$this->SPData = new bllStorePenetrationData();
        		
        $lastSPDate = $this->SPData->getLastStorePenDate();
        		
        $isCurrentAva = 0;	// not available
      
       
        if($lastSPDate[0]["current_spdate"]!=Null)
        {
			$lastDate = substr($lastSPDate[0]["current_spdate"],0,10);
				
			if($lastDate == date("Y-m-d"))
			{
				$isCurrentAva=1; // available
			}
		}
		else
		{
			$isCurrentAva=0;
		}
			
		 $edtAva =& $form->getField("is_sp_current_ava");
		 $edtAva->setValue($isCurrentAva) ;
	
		*/
	
	}
	
	
	

	function getEstateid()
	{
		$sql="select e.estate_id from estates e, wines w where w.deleted=0 and w.price_per_unit!=0 and e.estate_id=w.estate_id and e.deleted =0 and e.is_international=0 order by estate_name";
		$result = & F60DbUtil::runSQL($sql);
		$row = & $result->FetchRow();
		$estateid=$row['estate_id'];
		return $estateid;
	
	}

		function getWines($estateid)//for ajax called
		{
			$sql="select w.wine_id ,concat(w.wine_name,' ', w.vintage) wine_name from wines w where w.estate_id = ".$estateid." and w.deleted=0 and w.price_per_unit!=0 order by wine_name";
			$result = & F60DbUtil::runSQL($sql);
		//	$row = & $result->FetchRow();
	
			return $result;
		}

		function setSalesYearsToControl($reportTypeId =1,$estate_id ="")
		{
		    $VenderData = new bllABVenderData();		 	
			$results=$VenderData->getVenderSalesYears($reportTypeId,$estate_id);
			$i=0;         
 			 
			$strSelect = "var c = document.getElementById(\"sales_year\");";
			$strSelect .= "c.options.length=0;";
		
			$current_year="";

			if(count($results)!=0)
			{			
				for ($i=0;$i<count($results); $i++)
				{	 	
			 		$sale_year = $results[$i]["sales_year"];
			 		if($i==0)
				 		$current_year =$sale_year;
			 	
					$strSelect .= 'c.options['.$i.']=new Option("'.$sale_year.'", "'.$sale_year.'", false, false);';				
				}
			}
	
			$strMonthCombo=$this->setSalesMonthsToControl($current_year,$reportTypeId,$estate_id);
						
			$strSelect =$strSelect.$strMonthCombo;
						
			return $strSelect;
		}
		
		
		
		function setSalesMonthsToControl($sales_year, $reportTypeId =1,$estate_id="")
		{
		 	$VenderData = new bllABVenderData();		 
			 
			$results=$VenderData->getVenderSalesMonths($sales_year,$reportTypeId,$estate_id);
			
			$i=0;         
 			
			$strSelect = "var c = document.getElementById(\"sales_month\");";
			$strSelect .= "c.options.length=0;";		
	
			if(count($results)!=0)
			{
				
				for ($i=0;$i<count($results); $i++)
				{
				
			 		$sale_month = $results[$i]["sales_month"];			
					$strSelect .= 'c.options['.$i.']=new Option("'.F60Date::getMonthTxt($sale_month).'", "'.$sale_month.'", false, false);';				
				}
			}		
	
			return $strSelect;
		}

  		function getWines4SelectHtml($estateid)
  		{
			$result=reportsMain::getWines($estateid);
			$i=0;
			$strSelect="";
			while(!$result->EOF)
	        {
			 	$row=& $result->FetchRow();
			 	if ($i==0)
					$strSelect = '<option selected value="'.$row['wine_id'].'">'.$row['wine_name'].'</option>';
				else
				    $strSelect .= '<option value="'.$row['wine_id'].'">'.$row['wine_name'].'</option>';
				$i++;
			}
			return $strSelect;
		}


  		function getWines4SelectScript($controlID, $estateid)
  		{
			$result=reportsMain::getWines($estateid);
			$i=0;
            $strSelect = "var c = document.getElementById(\"".$controlID."\");";
			$strSelect .= "c.options.length=0;";
			while(!$result->EOF)
	        {
			 	$row=& $result->FetchRow();
			 	if ($i==0)
			 	{
				    $strSelect .= 'c.options['.$i.']=new Option("'.$row['wine_name'].'", "'.$row['wine_id'].'", true, false);';
	    		}
				else
				    $strSelect .= 'c.options['.$i.']=new Option("'.$row['wine_name'].'", "'.$row['wine_id'].'", false, false);';
				$i++;
			}
			
		
			return $strSelect;
		}


		function setSalesAnaMonth($province_id,$sales_year)
		{
		 
		    $AnaData = new salesAnalysisData();		 	
			$results=$AnaData->getTotalMonth($province_id,$sales_year);
			$i=0;         
			$strSelect = "var c = document.getElementById(\"bi_sale_month\");";
			$strSelect .= "c.options.length=0;";		
	
			if(count($results)!=0)
			{
				
				for ($i=0;$i<count($results); $i++)
				{				
			 		$sale_month = $results[$i]["sales_month"];			
					$strSelect .= 'c.options['.$i.']=new Option("'.F60Date::getMonthTxt($sale_month).'", "'.$sale_month.'", false, false);';				
				}
			}		
			

  			$strUsers = reportsMain::setSalesAnaUsers($province_id,$sales_year,$sale_month);
  			
  			$strSelect.=$strUsers;
  			
  		
			return $strSelect;
	
		//	$strMonthCombo=$this->setSalesMonthsToControl($current_year,$reportTypeId,$estate_id);
						
		//	$strSelect =$strSelect.$strMonthCombo;
						
		
		}
		
		function setSalesAnaUsers($province_id,$year,$month)
		{
		 
		
		    $AnaData = new salesAnalysisData();		 	
			$results=$AnaData->getUsers($year,$month,$province_id,-1);
			$i=0;         
			$strSelect = "var c = document.getElementById(\"bi_user_id\");";
			$strSelect .= "c.options.length=0;";		
	
				$strSelect .= "c.options[0]=new Option('All', -1, false, false);";	
			if(count($results)!=0)
			{
				
				for ($i=0;$i<count($results); $i++)
				{				
			 		$user_name = $results[$i]["user_name"];	
					$user_id = $results[$i]["user_id"];			
			 		
			 		$j=$i+1;
					$strSelect .= "c.options[$j]=new Option('$user_name', $user_id, false, false);";				
				}
			}		
			
			
  			
			
			return $strSelect;
	
		
		}
		
		
		function setValueToSalesAnaYearsCtnl($province_id)
		{
		 

		    $AnaData = new salesAnalysisData();		 	
			$yearDatas=$AnaData->getTotalYears($province_id);
			$i=0;         
 			
 			
	
			$current_year="";

			$cmdCtl = & $this->form->getField("bi_sale_year");
				
			
			if(count($yearDatas)!=0)
			{			
				foreach($yearDatas as $yearData)
				{	 	
			 		$indexKey=$yearData["sales_year"];
			 		$index=$yearData["sales_year"];
		
					$cmdCtl ->addOption($indexKey,$index,$i);
				
				}			
				
			}
	
		//	$strMonthCombo=$this->setSalesMonthsToControl($current_year,$reportTypeId,$estate_id);
						
		//	$strSelect =$strSelect.$strMonthCombo;
						
		
		}
		
        function processForm()
        {
              return true;
        }
}


?>