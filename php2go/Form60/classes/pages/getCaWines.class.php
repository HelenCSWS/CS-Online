<?php

/**
 * Perform the necessary imports
 */

import('Form60.base.F60FormBase');
import('Form60.util.F60Date');
import('Form60.base.F60DALBase');
import('Form60.bll.bllSSDSData');


class getCaWines extends F60FormBase
{
		//steps:
		// 1 - upload file
		// 2 - received file
		var $sale_month;

		var $user_id;
	
		var $sale_year;
		//columns of uploaded_customers table


		function getCaWines()
		{
			if (F60FormBase::getCached()) exit(0);

			F60FormBase::F60FormBase('getCaWines', 'Sales summary report', 'getCaWines.xml', 'getCaWines.tpl','btnAdd');
			
			$this->addScript('resources/js/javascript.ssdsreports.js');
			$form = & $this->getForm();
			$this->form->setButtonStyle('btnOK');
			$this->form->setInputStyle('input');
			$this->form->setLabelStyle('label');

 
         for($i=1;$i<=5;$i++)
         {
              $ctlName="user$i";
			     $cntl = & $form->getField("getCaWines.$ctlName");
			     $cntl->setStyle("text");
			}
		//	$cntl->setValue();


            $this->sale_month =$_REQUEST["sale_month"];
            $edtPeriodId=& $this->form->getField("sale_month");
            $edtPeriodId->setValue($_REQUEST["sale_month"]);

            $edtIsRecreate=& $this->form->getField("is_recreate");
            $edtIsRecreate->setValue($_REQUEST["is_recreate"]);

            $edtUsers=& $this->form->getField("users");
            $edtUsers->setValue($_REQUEST["users"]);
            $this->users=$_REQUEST["users"];
            
          //  print $this->users;

            $edtUser_id=& $this->form->getField("user_id");
            $edtUser_id->setValue($_REQUEST["user_id"]);
            $this->user_id=$_REQUEST["user_id"];

            $edtStoreType=& $this->form->getField("store_type");
            $edtStoreType->setValue($_REQUEST["store_type"]);
            
            $store_type=$_REQUEST["store_type"];

           
             $edtFiscalYear=& $this->form->getField("sale_year");
            $edtFiscalYear->setValue($_REQUEST["sale_year"]);
            
            $this->sale_year = $_REQUEST["sale_year"];

          //  import('Form60.base.F60PageStack');
          //  F60PageStack::addtoPageStack();
           // print $this->users;
          $sURL="main.php?page_name=summaryreport&user_id=$this->user_id&sale_month=$this->sale_month&sale_year=$this->sale_year&store_type=$store_type&users=$this->users";
          
       
            $this->registerActionhandler(array("btnAdd", array($this, processForm), "URL", $sURL));
      
	//		$this->registerActionhandler(array("btnCancel", array($this, processForm), "URL", "main.php"));

           $form = & $this->getForm();
            $form->setFormAction($_SERVER["REQUEST_URI"]);

			$action = array("Print help" => "javascript:printHelp();",);


            $this->setActions($action);
          
            $this->attachBodyEvent('onLoad', 'setForm(2);');
		}

  


	function display()
	{
		if (!$this->handlePost())
			$this->displayForm();
	}

	function displayForm()
	{

        $bllSSDS= new SSDSData();
        
  		$cntl = & $this->form->getField("fiscal_year");
		$cntl->setStyle("text");
			$txtMonth =F60Date:: getMonthTxt($this->sale_month);
		$periodRangeTxt = $txtMonth." ".$this->sale_year;

  		$cntl = & $this->form->getField("period_desc");
		$cntl->setStyle("text");
		$cntl->setValue($periodRangeTxt);
  
      $nUsers=$this->users;
      $usersInfo=$bllSSDS->getCaUsersByMonth($this->sale_month,$this->sale_year);
  
      $nUsers = count($usersInfo);
	//	print $usersInfo[0]["user_name"];
	$cntl = & $this->form->getField("users");
	$cntl->setValue($nUsers);
		
       for($i=1;$i<$nUsers+1;$i++)
        {
      
      		$ctlName ="user$i";
      		$ctlUserName=& $this->form->getField($ctlName);
      		$nindex=$i-1;
      		$user_name =$usersInfo[$nindex]["user_name"];
      		

    		$ctlUserName->setStyle("text");
    		$ctlUserName->setValue($user_name);
//casese
            $user_id =$usersInfo[$nindex]["user_id"];
            
            $totalSales = $bllSSDS->getTotalCandianCasesById($user_id,$this->sale_month,$this->sale_year);
            $caCases =$totalSales[0]["total_canadian_cases"];

      		$ctlName ="total_cases$i";
      		$ctlTotalCasesName=& $this->form->getField($ctlName);
      		$ctlTotalCasesName->setValue($caCases);
        }




    	F60FormBase::display();
	}

	function processForm()
	{
       $bllSSDS= new SSDSData();

 	 	$is_recreate = $this->form->getField("is_recreate");
 	 	
 	 	$retVal=true;
		if($is_recreate ==1)
		{
			$retVal = $bllSSDS->regenerateSummaryData($this->sale_month);
		}

		if($retVal)
		{

		
	        $edtIsUpdate = $this->form->getField("is_update_CaCases");
	
	        $is_update =$edtIsUpdate->getValue();
	        
	        if($is_update==1)
	        {
	         
	            $nUsers=1;
	            if($this->user_id==-1)
	            {
	                $nUsers=$this->users;
	                 $usersInfo=$bllSSDS->getCaUsersByMonth($this->sale_month,$this->sale_year);
	           }
	           else
	           {
	                $usersInfo=$bllSSDS->getUserInfoByUser_id($this->user_id);
	           }
	
	            for($i=1;$i<=$nUsers;$i++)
	            {
	                $nindex=$i-1;
	                $user_id =$usersInfo[$nindex]["user_id"];
	                $ctlName ="total_cases$i";
	          		$ctlTotalCasesName=& $this->form->getField($ctlName);
	          		$total_cacases=$ctlTotalCasesName->getValue();
	          		
	          	//	print  $total_cacases;
	          		$retVal = $bllSSDS->updateCanadianTotalCases($this->sale_year,$this->sale_month,$user_id,$total_cacases);
	          		if(!$retVal)
	          			break;
	            }
	
	        }
        }
        
        
        
		return $retVal;
	}









}

?>
