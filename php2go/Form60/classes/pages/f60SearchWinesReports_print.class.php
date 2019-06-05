<?php

/**
 * Perform the necessary imports
 */

import('Form60.base.F60FormBase');
import('Form60.base.F60WinesSearchResultList');
//import('Form60.bll.bllestates');
import('Form60.bll.bllcontacts');


class f60SearchWinesReports_print extends Document
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
	function f60SearchWinesReports_print()
	{

			
			 
        Document::Document('resources/template/f60SearchWinesReports.tpl');
        Document::addStyle(CSS_PATH . 'report.css');
        Document::addScript('resources/js/javascript.f60searchresultlist.js');

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

			if($_REQUEST["isOneRec"]==1)
				$this->isOneRec=1;
			
         
     }
	
    	function display()
    	{
         
            
            $listControl = & new F60WinesSearchResultList(&$this, $this->search_id,
																			 $this->sales_period,
																			 $this->sales_year,
																			 $this->isQuarter,
																			 $this->store_type_id,
																			 $this->user_id,
																			 $this->search_adt1,
																			 $this->search_adt2,
																			 $this->city,1,1);
																			 
				$this->elements['list_results'] = $listControl->getContent();
            Document::display();
        }

        function processForm()
        {
        		return true;
        }


}

?>