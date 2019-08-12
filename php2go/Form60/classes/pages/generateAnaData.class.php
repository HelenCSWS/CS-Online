<?php

/**
 * Perform the necessary imports
 */

import('Form60.base.F60FormBase');
import('Form60.util.F60Date');
import('Form60.bll.bllSSDSData');
import('Form60.bll.bllSalesAnalysisData');
//import('php2go.util.Spreadsheet');
//import('php2go.file.ZipFile');
//require_once('Excel/reader.php');

define('DEFAULT_NO_PERIOD', 'No data available for this fiscal year.');


class generateAnaData extends F60FormBase
{	
		var $form;

		function generateAnaData()
		{
	 		$this->province_id = $_COOKIE["F60_PROVINCE_ID"];
			if (F60FormBase::getCached()) exit(0);

			F60FormBase::F60FormBase('selectSSDSMonth', 'Sales summary report', 'generateAnaData.xml', 'generateAnaData.tpl');
			$this->addScript('resources/js/javascript.generateAnaData.js');
				Registry::set('current_province_id', $this->province_id);
			$this->form = & $this->getForm();
			$this->form->setButtonStyle('btnOK');
			$this->form->setInputStyle('input');
			$this->form->setLabelStyle('label');


			if($_REQUEST["is_recreate"]!="")
			{
			// print $_REQUEST["is_recreate"];
                $cntl = & $this->form->getField("is_recreate");
        		$cntl->setValue($_REQUEST["is_recreate"]);
			}
			$this->attachBodyEvent('onLoad', 'initpage();');
			
		}
    
	function display()
	{
		if (!$this->handlePost())
			$this->displayForm();
	}

	function displayForm()
	{
    	

		$cmbYear=& $this->form->getField("sale_year");
		
		if($_REQUEST["sale_year"]!="")
		{
			$cmbYear->setValue($_REQUEST["sale_year"]);	
		}
		else
		{
		
	
			$bllSSDS = new SSDSData();
    	
    		$currentYear = $bllSSDS->getMaxSaleMonth($this->province_id);
    
    		$cmbYear->setValue($currentYear);	
		}
		
		$currentFsYear=$cmbYear->getValue();
        
      
    
		if($currentFsYear!="")
	       $this->getMonths($currentFsYear);
        
    	 F60FormBase::display();
	}
	
    function getMonths($currentFsYear)
    {
 	 	$periods = $this->getMonthBySaleYear($currentFsYear);
         $cmbPeriods=& $this->form->getField("sale_month");
       
       $current_period_id=intval($_REQUEST["sale_month"]);

       
     
       
       $current_period="";

      if ($periods ==0 )
      {
          // $txtInfo->setValue("No data available for this year.");
       
      }
      else
      {
          $nPeriods = count($periods);

         for($i=0;$i<=$nPeriods-1;$i++)
         {
           $period =$periods[$i]['sale_month'];
         
           

           $period_id =$periods[$i]['sale_month'];
            if($current_period_id=="" && $i==$nPeriods-1)
            {
					if($current_period_id =$periods[$i]['sale_month'] )
					{
						$current_period_id = $period;
					}
				}
            $period_name =F60Date::getMonthTxt($periods[$i]['sale_month']);
           
            $cmbPeriods->addOption($period_id,$period_name,$i);
         }
         if($current_period_id!="")
         {
          
				$cmbPeriods->setValue($current_period_id);
				//$cmbPeriods->setValue(4);
			}
         
      }

    }
   

    function checkAnaSalesDataAva($sales_year,$sales_month,$province_id)
    {
		 $bllAnaData = new salesAnalysisData();
        $rects = $bllAnaData->checkIfDataAVA($sales_year,$sales_month,$province_id);
        
        $isAva =false;
        if($rects>0)
        	$isAva=true;
        	
        $strScript ="setAvaMsg($isAva);";
        	
        
        return $strScript;
	}
	
  function generateASAnaData($sales_year,$sales_month,$province_id)
    {
		   $anaData = & new salesAnalysisData();
        
   
       $users = $anaData-> getUsers($sales_year,$sales_month,$province_id);

        $strScript="";
        foreach ($users as $user)
        {
			$user_id =$user["user_id"];
			
			$anaData-> generateCustomersSales($user_id,$sales_year,$sales_month,$province_id);
			       	
		}
		
		// update Alberta rank
		if($province_id==2)
			$anaData->ETL_add_rank($sales_year,$sales_month,$province_id);
		
		$strScript ="setAvaMsg(true);";
		return $strScript;
	}   
	
	function getMonthsSelectHtml($fiscal_year)
  		{

			$periods=generateAnaData::getMonthBySaleYear($fiscal_year);
			
			if($periods==0)
			{
	            $strSelect = "var c = document.getElementById(\"sale_month\");";
				$strSelect .= "c.options.length=0;";
				$strSelect .= 'c.options[0]=new Option("", "-100", false, false);';				
			}
			else
			{
				$i=0;
	
	            $strSelect = "var c = document.getElementById(\"sale_month\");";
				$strSelect .= "c.options.length=0;";
				
				$nPeriods = count($periods);
    
		         for($i=0;$i<=$nPeriods-1;$i++)
		         {
		           $period =F60Date::getMonthTxt($periods[$i]['sale_month']);
 
		           $period_id =$periods[$i]['sale_month'];
		            if($i==$nPeriods-1)
		            {
		              $strSelect .= 'c.options['.$i.']=new Option("'.$period.'", "'.$period_id.'", false, true);';
		              
					}
					else
					{
		              $strSelect .= 'c.options['.$i.']=new Option("'.$period.'", "'.$period_id.'", false, false);';
	
					}
		            
		         }
		         
		       //  $strSelect .=$periodText;
				
			}

        		return $strSelect;
		}
		
 	
	function getMonthBySaleYear($sale_year)
	{
      //  print $fiscal_year;
        $bllSSDS = new SSDSData();
        $periods = $bllSSDS->getAvialableMonthByYear($sale_year,$this->province_id);
        
        return $periods;
    }
    
    

	function processForm()
	{
		return true;
	}




//common function






}

?>
