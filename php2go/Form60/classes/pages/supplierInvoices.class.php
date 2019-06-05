<?php

/**
 * Perform the necessary imports
 */

import('Form60.base.F60FormBase');
import('Form60.base.F60DALBase');
import('Form60.base.SupplierSalesList');
import('Form60.base.F60DbUtil');
import('Form60.bll.bllsupplierData');

/*
    search_id = 1 : search Estate

*/
class supplierInvoices extends F60FormBase
{
	var $search_id ;
	var $estate_name;
	var $sURL;

	var $province_id;
	
	var $isWine=false;
	
	var $spSales;
	var $estate_id;
	
	var $form;
	var $is_international=0;
	var $isListEmpty=true;
	

	function supplierInvoices()
	{
	 
        if($_REQUEST["estate_id"]!="")
        {
        
			$this->estate_id=$_REQUEST["estate_id"];
		}
		else
		{
			$this->spSales = new suppliersData();
			
			$estates= $this->spSales->getEstate($login_user_id);
			
			$this->estate_id =$estates[0]["estate_id"];
			
		}
		$login_user_id = & F60DALBase::get_current_user_id();
		
	/*	if($this->estate_id == 96)
		{
			$title ="Enotecca winery";
			
		//	$this->estate_id =-1;
		}
		else
			$title = $estates[0]["estate_name"];
	   */         
            
	
		
		$this->province_id=1;// only BC sales has the search inovices page
        
        $title = "Un paid invoices";

        F60FormBase::F60FormBase('supplierSales', $title, 'searchinvoice.xml', 'searchinvoice.tpl');
       
       $this->addScript(PHP2GO_JAVASCRIPT_PATH . "libs/cookie.js");
        $this->addScript('resources/js/javascript.suppliersales.js');
         

        $form = & $this->getForm();
        $form->setFormAction($_SERVER["REQUEST_URI"]);
          
        $this->registerActionhandler(array("btnClose", array($this, processForm), "LASTPAGE",  null));
        $this->form->setButtonStyle('btnOK');
        $this->form->setInputStyle('input');
        $this->form->setLabelStyle('label');

		$edtEstate = & $form->getField("current_estate_id");
		
        $edtEstate ->setValue($this->estate_id);
        
        $edtEstate = & $form->getField("estate");
		if($this->estate_id == 96)
		{
			$this->estate_id =-1;            	
		}
		$edtEstate ->setValue($this->estate_id);
		
		
        $edtUser = & $form->getField("user_id");
		
        $edtUser ->setValue($login_user_id);
           					
		
		$this->form = & $this->getForm();
                   

		$this->attachBodyEvent('onLoad', "initSearchPage($this->estate_id);");
             
  }
		function setEmptyListPara($isTrue)
		{
			$this->isListEmpty = $isTrue;
		}

    	function display()
    	{
    	
           if (!$this->handlePost())
            {           
			 	$action = array(
		 				//"Export "=>"javascript:exportReport();",
						 "Export CC List"=>"javascript:exportCCReport();",
						 //"Sales "=>"javascript:supplierSales($this->estate_id);"
						 );
 	
                
                
				$this->setActions($action);
					  	       
                $this->displayForm();
            }
    
          
 		}

	
     function displayForm()
     {
   
         $form = & $this->getForm();

		 $isSearch = true;
	
		 $date1 = ""; // $searchVale: default : all
		 $date2 = 0; // $isStart: def: not start with
		 $dateType = 1; //$searchType: def: by customer name
		 
		 //function SupplierSalesList($Document,$estate_id, $date1, $date2, $order_by,$order_type, $dateType=2, $store_type_id=-1, $user_id=-1,$province_id=1,$wine_id="", $reportType=1, $page = 1, $isSearch=false,$isFirst=false;)
	 
		 $store_type_id=-1;
		 $user_id=-1;
		 $province_id=1;
		 $wine_id ="";
		 $reportType=1;
		 
		 $orderBy="delivery_date";
		 $order_type="d";
	 
	 	$this->isListEmpty = true;
	 	
 		 $salesList = & new SupplierSalesList(&$this,$this->estate_id, $date1,$date2 , $orderBy,$order_type,$dateType, $store_type_id, $user_id,1,$province_id, $wine_id, $reportType,$isSearch,$this->isListEmpty);
	    
     	
		$this->form->Template->assign("info_list", $salesList->getContent()); 
	
	
		
		F60FormBase::display();
 		
     }

	


     
        function processForm()
        {
            //  $this->sURL = "main.php?page_name=estateAdd";
              return true;
           
        }

}

?>
