<?php

/**
 * Perform the necessary imports
 */

import('Form60.base.F60FormBase');
import('Form60.base.F60WinesSearchResultList');
//import('Form60.bll.bllestates');
import('Form60.bll.bllcontacts');


class f60SearchWinesReports extends F60FormBase
{
		var $search_id="";
		var $sales_period="";
		var $sales_year="";
		var $store_type_id="";
		var $user_id="";
		var $search_adt1="";
		var $search_adt2="";
		var $isQuarter="";
		var $isStart="";
		var $search_key="";
		var $isOneRec=0;
		var $city="";
		var $product_id=1;
	
		function f60SearchWinesReports()
		{
	
			F60FormBase::F60FormBase('f60SearchWinesReports', "Search result", 'f60SearchWinesReports.xml', 'f60SearchWinesReports.tpl', 'btnAdd');
			$this->addScript('resources/js/javascript.f60searchresultlist.js');
			$this->addStyle('resources/css/jqplug_style.css');
			
			$form = & $this->getForm();
			$form->setFormAction('main.php?page_name=customerCompare&step=3&cc_session_id=' . $_REQUEST['cc_session_id']);
			
			$sUrl ='main.php?page_name=';
			$this->registerActionhandler(array("btnAdd", array($this, processForm), "URL", $sUrl));
			
			$this->form->setButtonStyle('btnOK');
			
			$this->search_id = $_REQUEST["search_id"];
			$this->sales_year = $_REQUEST["year"];
			
			$this->isQuarter = $_REQUEST["isQtr"];
			
			$this->sales_period = $_REQUEST["period"]; 
			$this->isStart = $_REQUEST["start_with"];
			$this->search_key = $_REQUEST["search_key"];
			
			$this->store_type_id = $_REQUEST["store_type"];
			$this->user_id = $_REQUEST["user_id"];
			$this->search_adt1 = $_REQUEST["search_adt1"];
			$this->search_adt2 = $_REQUEST["search_adt2"];
			$this->search_adt2 = $_REQUEST["search_adt2"];
			
			$this->isOneRec = $_REQUEST["isOneRec"];
	
	        $this->product_id = $_REQUEST["product_id"];
			$this->setValue2Ctl($this->search_id,"search_id",$form);
			
			$this->setValue2Ctl($this->search_id,"search_id",$form);
			$this->setValue2Ctl($this->search_key,"search_key",$form);
			$this->setValue2Ctl($this->sales_year,"sales_year",$form);
			$this->setValue2Ctl($this->isQuarter,"isQtr",$form);
			$this->setValue2Ctl($this->isStart,"isStart",$form);
			$this->city = $_REQUEST["city"];
				
			if($_REQUEST["isOneRec"]==1)
				$this->isOneRec=1;
			 
			$this->setValue2Ctl($this->isOneRec,"isOneRec",$form);
			
			$this->setValue2Ctl($this->product_id,"product_id",$form);
			
			$this->setValue2Ctl($this->sales_period,"sales_period",$form);
			$this->setValue2Ctl($this->store_type_id,"store_type_id",$form);
			$this->setValue2Ctl($this->user_id,"user_id",$form);
			$this->setValue2Ctl($this->store_type_id,"store_type_id",$form);
			$this->setValue2Ctl($this->search_adt1,"search_adt1",$form);
			$this->setValue2Ctl($this->search_adt2,"search_adt2",$form);
			$this->setValue2Ctl($this->city,"city",$form);
			
			$this->attachBodyEvent('onLoad', 'initRestultForm();');
	     }
	
		function setValue2Ctl($val,$ctlName)
	   	{
			$ctl = & $this->form->getField($ctlName);
			$ctl->setValue($val);
		}
		
    	function display()
    	{
            if (!$this->handlePost())
                $this->displayForm();
        }

        function displayForm()
        {
         
			//$action = array( "Print report" => "javascript:printResults();");
          	//$this->setActions($action);
          
            $form = & $this->getForm();
     
            $listControl = & new F60WinesSearchResultList(&$this, 
																			$this->product_id,
																			$this->search_id,
																			 $this->sales_period,
																			 $this->sales_year,
																			 $this->isQuarter,
																			 $this->store_type_id,
																			 $this->user_id,
																			 $this->search_adt1,
																			 $this->search_adt2,
																			 $this->city);
            $form->Template->assign("list_results", $listControl->getContent());
            F60FormBase::display();
        }

        function processForm()
        {
        		return true;
        }


}

?>