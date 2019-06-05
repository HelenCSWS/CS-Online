<?php

/**
 * Select availabe sales year and month for Sales Summary page
 */

import('Form60.base.F60FormBase');
import('Form60.util.F60Date');
import('Form60.bll.bllSSDSData');
//import('php2go.util.Spreadsheet');
//import('php2go.file.ZipFile');
//require_once('Excel/reader.php');

define('DEFAULT_NO_PERIOD', 'No data available for this fiscal year.');


class selectSSDSMonth extends F60FormBase
{	
    var $form;
    
    function selectSSDSMonth()
    {
    	$this->province_id = $_COOKIE["F60_PROVINCE_ID"];
    	if (F60FormBase::getCached()) exit(0);
    
    	F60FormBase::F60FormBase('selectSSDSMonth', 'Sales summary report', 'selectSSDSMonth.xml', 'selectSSDSMonth.tpl');
    	$this->addScript('resources/js/javascript.ssdsreports.js');
    	Registry::set('current_province_id', $this->province_id);
    	$this->form = & $this->getForm();
    	$this->form->setButtonStyle('btnOK');
    	$this->form->setInputStyle('input');
    	$this->form->setLabelStyle('label');
    
    
    	if($_REQUEST["is_recreate"]!="")
    	{
            $cntl = & $this->form->getField("is_recreate");
    		$cntl->setValue($_REQUEST["is_recreate"]);
    	}
    
    	$this->attachBodyEvent('onLoad', 'checkRecreate();');
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
        
        if($periods !=0 )
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
            }        
        }   
    }
   

    
    function getMonthsSelectHtml($fiscal_year)
  		{

			$periods=selectSSDSMonth::getMonthBySaleYear($fiscal_year);
			
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
