<?php

/**
 * Perform the necessary imports
 */

import('Form60.base.F60FormBase');
import('Form60.util.F60Date');
import('Form60.bll.bllSSDSData');
//import('php2go.util.Spreadsheet');
//import('php2go.file.ZipFile');
//require_once('Excel/reader.php');

define('DEFAULT_NO_PERIOD', 'No data available for this fiscal year.');


class selectPeriod extends F60FormBase
{
		//steps:
		// 1 - upload file
	
		var $form;

		//columns of uploaded_customers table
	

		function selectPeriod()
		{
			if (F60FormBase::getCached()) exit(0);

			F60FormBase::F60FormBase('selectPeriod', 'Sales summary report', 'selectPeriod.xml', 'selectPeriod.tpl');
			$this->addScript('resources/js/javascript.ssdsreports.js');
			$this->form = & $this->getForm();
			$this->form->setButtonStyle('btnOK');
			$this->form->setInputStyle('input');
			$this->form->setLabelStyle('label');

		/*	$cntl = & $form->getField('customercompare.errmsg');
			$cntl->setStyle("hiddenInput");
			$cntl->setValue('&nbsp;');*/


              $sURL = "main.php?page_name=selectStoreType";
         
   



                $cntl = & $this->form->getField("period_desc");
        		$cntl->setStyle("text");


				if($_REQUEST["is_recreate"]!="")
				{
	                $cntl = & $this->form->getField("is_recreate");
	        		$cntl->setValue($_REQUEST["is_recreate"]);

          
					$this->attachBodyEvent('onLoad', 'setForm(0);');
				}

            
		}

 
    
	function display()
	{
		if (!$this->handlePost())
			$this->displayForm();
	}

	function displayForm()
	{
        $action = array( "SSDS schedule"=>"javascript:openHelp(1);"
                );
                 $this->setActions($action);


        $cmbYear=& $this->form->getField("fiscal_year");
        
        if($_REQUEST["fiscal_year"]!="")
        {
			$cmbYear->setValue($_REQUEST["fiscal_year"]);	
		}
        $currentFsYear=$cmbYear->getValue();
        
      //  print $currentFsYear;


        $this->getPeriods($currentFsYear);
        
    	F60FormBase::display();
	}
	
    function getPeriods($currentFsYear)
    {
 
       $periods = $this->getPeriodsByFiscalYear($currentFsYear);
          $txtInfo=& $this->form->getField("period_desc");
       $cmbPeriods=& $this->form->getField("period");
       
       $current_period_id=$_REQUEST["period_id"];
       
       $current_period="";

      if ($periods ==0 )
      {
           $txtInfo->setValue("No data available for this fiscal year.");
       
      }
      else
      {
          $nPeriods = count($periods);

         for($i=0;$i<=$nPeriods-1;$i++)
         {
           $period =$periods[$i]['period'];
           

           $period_id =$periods[$i]['period_id']."|".$period;
            if($current_period_id!="")
            {
				if($current_period_id =$periods[$i]['period_id'] )
				{
					$current_period_id = $periods[$i]['period_id']."|".$period;
				}
			}
            $period_name ="period ".$periods[$i]['period'];
           
            $cmbPeriods->addOption($period_id,$period_name,$i);
            if($i==0)
            {
				$periodInfo = $this->getPeriodInfoTxt($period,$currentFsYear);
	           $txtInfo->setValue($periodInfo);
				
			} 
         }
         if($current_period_id!="")
         {
			$cmbPeriods->setValue($current_period_id);
		}
         
      }

    }
    
    function getPeriodInfoTxt($period,$f_year)
    {
		$bllSSDS = new SSDSData();
		
		$bllSSDS->SSDS_period =$period;
		$bllSSDS->SSDS_year =$f_year;
		$bllSSDS->getYearPeriodDateRange();
		
	
		$PeriodTxt =$bllSSDS->getPeriodInfoText($period);
		return $PeriodTxt;
	}
	

    
    function getPeriodsSelectHtml($fiscal_year)
  		{

			$periods=selectPeriod::getPeriodsByFiscalYear($fiscal_year);
			
			if($periods==0)
			{
	            $strSelect = "var c = document.getElementById(\"period\");";
				$strSelect .= "c.options.length=0;";
				$strSelect .= 'c.options[0]=new Option("", "-100", false, false);';
				
				$strSelect.= "var txtInfo=document.getElementById(\"period_desc\");";
            	//$strTxt .='alert(txtInfo);';//.$infoText;
            	$strSelect.='txtInfo.innerHTML="No data available for this fiscal year.";';
            	
            	//$test="";
				
			}
			else
			{
				$i=0;
	
	            $strSelect = "var c = document.getElementById(\"period\");";
				$strSelect .= "c.options.length=0;";
				
				$nPeriods = count($periods);
    
		         for($i=0;$i<=$nPeriods-1;$i++)
		         {
		           $period =$periods[$i]['period'];
		           $period_id =$periods[$i]['period_id']."|".$period;
		            $period_name ="period ".$periods[$i]['period'];
		           
		            //$cmbPeriods->addOption($period_id,$period_name,$i);
		            if($i==0)
		            {
		              $strSelect .= 'c.options['.$i.']=new Option("'.$period_name.'", "'.$period_id.'", false, true);';
		              $periodText = selectPeriod::getPeriodTextHtml($period,$fiscal_year);
		              
					}
					else
					{
		              $strSelect .= 'c.options['.$i.']=new Option("'.$period_name.'", "'.$period_id.'", false, false);';
	
					}
		            
		         }
		         
		         $strSelect .=$periodText;
				
			}

        		return $strSelect;
		}
		
  function getPeriodTextHtml($period,$fsyear)
  		{

			$infoText=selectPeriod::getPeriodInfoTxt($period,$fsyear);
			$i=0;

            $strTxt= "var txtInfo=document.getElementById(\"period_desc\");";
            $strTxt .='txtInfo.innerHTML="'.$infoText.'";';

        	return $strTxt;
		}
	
	function getPeriodsByFiscalYear($fiscal_year)
	{
       // print $fiscal_year;
        $bllSSDS = new SSDSData();
        $periods = $bllSSDS->getAvialablePeriodsByFiscalYear($fiscal_year);
        
        return $periods;
    }

	function processForm()
	{
		return true;
	}




//common function






}

?>
