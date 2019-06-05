<?php

/**
 * Perform the necessary imports
 */

import('Form60.base.F60FormBase');
import('Form60.base.F60DALBase');
import('Form60.base.F60OverDueList');
import('Form60.base.F60DbUtil');
//import('Form60.bll.bllf60Reports');

/*
    search_id = 1 : search Estate

*/
class f60OverDueReports extends F60FormBase
{

	var $sURL;

	var $province_id;
	
	var $isWine=false;
	
	var $spSales;
	var $estate_id=0;
	
	var $form;
	var $is_international=0;
	
	var $user_id=0;
	var $overdays=3;
	

	function f60OverDueReports()
	{
	 
		$login_user_id = & F60DALBase::get_current_user_id();
		
		F60FormBase::F60FormBase('f60OverDueReports', "Accounts receivable summary", 'f60overduereports.xml', 'f60overduereports.tpl');
	//	$this->addScript(PHP2GO_JAVASCRIPT_PATH . "libs/cookie.js");
		$this->addScript('resources/js/javascript.suppliersales.js');
		$this->addScript('resources/js/javascript.reports.js');
		
		$form = & $this->getForm();
		$form->setFormAction($_SERVER["REQUEST_URI"]);
		
		$this->registerActionhandler(array("btnClose", array($this, processForm), "LASTPAGE",  null));
		$this->form->setButtonStyle('btnOK');
		$this->form->setInputStyle('input');
		$this->form->setLabelStyle('label');
		
		$this->initControls();
		
		$this->estate_id = $_REQUEST["estate_id"];
		$this->user_id = $_REQUEST["user_id"];
		
		$cmbEstate = & $form->getField("estate_id");
		$cmbEstate->setValue($this->estate_id);
		
		$cmbOverDue = & $form->getField("overdue_type");
		$cmbOverDue->setValue($this->overdays);
		
		$cmbUser = & $form->getField("user_id");
		$cmbUser->setValue($this->user_id);

		 $this->attachBodyEvent('onLoad', "checkIfNoRecords();");

    }


	
	function display()
	{
	   if (!$this->handlePost())
	   {             	
	        $this->displayForm();
			$action = array("Export "=>"javascript:exportOverduReport();");
			$this->setActions($action);
		    F60FormBase::display();
	   }
	}

     function displayForm()
     {
		$orderBy="overdays"; // default order
		$orderType="a";
		$page =1; //first page
		$store_type_id=0; //all store type
				
		$invoiceList = & new F60OVerDueList(&$this,$orderBy,$orderType,$this->estate_id,  $store_type_id, $this->user_id,$this->overdays,$page);
		
		$this->form->Template->assign("inovice_list", $invoiceList->getContent()); 
 		
     }

	 function initControls()
	 {
	  	$form = & $this->getForm();
		$cmdUser = & $form->getField("user_id");
		$cmdUser->setFirstOption('All',0);	
		
		$cmdUser = & $form->getField("lkup_store_type_id");
		$cmdUser->setFirstOption('All',0);		
	 }
     
        function processForm()
        {
            //  $this->sURL = "main.php?page_name=estateAdd";
              return true;
           
        }

}

?>
