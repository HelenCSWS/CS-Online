<?php

/**
 * Perform the necessary imports
 */

import('Form60.base.F60FormBase');
import('Form60.base.F60DALBase');
import('Form60.base.F60DbUtil');

/*
    search_id = 1 : search Estate

*/
class searchf60 extends F60FormBase
{
	var $search_id ;
	var $estate_name;
	var $sURL;

	var $province_id;
	
	var $isWine=false;
	
	var $product_id;
	function searchf60()
	{
           // if (F60FormBase::getCached()) exit(0);
            
        $this->search_id = $_REQUEST['searchid'];
        $funstring = 'initForm('.$this->search_id.');';
        $title = "Search";
        
        $this->province_id=$_COOKIE["F60_PROVINCE_ID"];
        
        F60FormBase::F60FormBase('searchf60', $title, 'searchf60.xml', 'searchf60.tpl');
        $this->addScript(PHP2GO_JAVASCRIPT_PATH . "libs/cookie.js");
        $this->addScript('resources/js/javascript.pageAction.js');
        $this->addScript('resources/js/javascript.f60search.js');
		Registry::set('current_province_id', $this->province_id);	
		Registry::set('current_user_province_id', $this->province_id);	

        $bc_estates_filter="";
        
        if($this->province_id==1)
            $bc_estates_filter="billing_address_country = 'Canada' or ";
            
            
  		Registry::set('BC_ESTATES', $bc_estates_filter);
          	
        $form = & $this->getForm();
        $form->setFormAction($_SERVER["REQUEST_URI"]);
      
        $this->registerActionhandler(array("btnClose", array($this, processForm), "LASTPAGE",  null));
        $this->form->setButtonStyle('btnOK');
        $this->form->setInputStyle('input');
        $this->form->setLabelStyle('label');

        $login_user_id = & F60DALBase::get_current_user_id();
        
        $cmbUser = & $form->getField("user_id");
        $cmbUser ->setValue($login_user_id);
 
		$cntl = & $this->form->getField("quarter_desc");
		$cntl->setStyle("text");
		
			
		$cntl = & $form->getField("province_id");
		$cntl->setValue($this->province_id);
		
		if($_REQUEST["isWine"]==1)
		{
			 $cntl = & $this->form->getField("isWine");
			 $cntl->setValue("1");
			 $this->isWine=true;
		}
	
       $this->attachBodyEvent('onLoad', $funstring);
        
       $this->setProductTypes(1);
       
      /**
      *        printf("uniqid(): %s\r\n", uniqid(true));
      *        printf("uniqid('php_'): %s\r\n", uniqid('php_'));
      *        printf("uniqid('', true): %s\r\n", uniqid('', true));
      */     
   
    }

    function display()
    {
        if (!$this->handlePost())
        {             	
            $this->displayForm();
        }
    }

     function displayForm()
     {
		$form = & $this->getForm();

		$cmdStoreType = & $form->getField("lkup_store_type_id_w");
		$cmdStoreType->setFirstOption('All',-1);

		$cmdCtl = & $form->getField("lkup_store_type_id_w");
		if($this->province_id == 1)
		{		
			$cmdCtl ->addOption("-1","All","0");
			$cmdCtl ->addOption("-2","All except BCLDB","1");
			$cmdCtl ->addOption("1","L.R.S","2");
			$cmdCtl ->addOption("2","Agency","3");
			$cmdCtl ->addOption("3","Licensee","4");
			$cmdCtl ->addOption("5","VQA","5");
			$cmdCtl ->addOption("6","BCLDB","6");
		}
		else
			$cmdCtl ->addOption("-1","Alberta Licensee",0);
		
		$cmdCtl = & $form->getField("lkup_wine_color_type_id");
		$cmdCtl->setFirstOption('All',-1);			
		
		
		$cmdUser = & $form->getField("user_id_w");
		$cmdUser->setFirstOption('All',-1);	
			
		if($this->isWine) //set value to search wine page from back button
		{
			
			$cmdCtl = & $form->getField("isQtr");
			$cmdCtl->setValue($_REQUEST["isQtr"]);
			
			$cmdCtl = & $form->getField("isStart");
			
		//	echo $_REQUEST["isStart"];
			$cmdCtl->setValue($_REQUEST["isStart"]);
			
			$cmdCtl = & $form->getField("search_id_w");
			$cmdCtl->setValue($_REQUEST["search_id"]);
			
			$cmdCtl = & $form->getField("lkup_store_type_id_w");
		       	
			if($_REQUEST["store_type"]!=-1)
			{
				$cmdCtl->setValue($_REQUEST["store_type"]);	
			}
			
			$cmdCtl = & $form->getField("user_id_w");
			if($_REQUEST["user_id"]!=-1)
			{
				$cmdCtl->setValue($_REQUEST["user_id"]);	
			}
					
					
			if($_REQUEST["search_id"]!=2)
			{
				$cmdCtl = & $form->getField("search_field_w");
				$cmdCtl->setValue($_REQUEST["search_key"]);
				
			}
			
			$ctlProduct_id = & $form->getField("product_id");
			$ctlProduct_id->setValue($_REQUEST["product_id"]);
			
			$this->product_id = $ctlProduct_id->getValue();
			
			$cmdYear = & $form->getField("sales_year");
			$cmdYear->setValue($_REQUEST["year"]);
			if($_REQUEST["isQtr"]==1)
			{
			 	$cmdQtr = & $form->getField("sales_qut");
				$cmdQtr -> setValue($_REQUEST["sale_period"]);
			}
			else
			{
				$cmdMth = & $form->getField("sales_month");
				$cmdMth->setValue($_REQUEST["sale_period"]);
			}
			
			if($_REQUEST["search_id"]==0)
			{
				$cmdCtl = & $form->getField("is_purchased");
		
				$cmdCtl -> setValue($_REQUEST["search_adt"]);

				$cmdCtl = & $form->getField("city");
				$cmdCtl -> setValue($_REQUEST["city"]);
			}
			else if($_REQUEST["search_id"]==1)
			{
				$cmdCtl = & $form->getField("cm_number");
				$cmdCtl -> setValue($_REQUEST["search_adt"]);		
			}
			else if($_REQUEST["search_id"]==2)
			{
				$cmdCtl = & $form->getField("wine_number");
				$cmdCtl -> setValue($_REQUEST["search_adt1"]);
				
				$cmdCtl = & $form->getField("lkup_wine_color_type_id");						
				if( $_REQUEST["search_adt2"]==-1)
					$cmdCtl->setFirstOption('All',-1);	
				else						
					$cmdCtl->setValue($_REQUEST["search_adt2"]);				
			}
			else if($_REQUEST["search_id"]==3)
			{
				$cmdCtl = & $form->getField("sku_name");
				$cmdCtl -> setValue($_REQUEST["search_adt"]);				
			}
			
		}
			
         F60FormBase::display();
     }

	function setProductTypes($product_id)
	{
	
		$bllUntil = new F60DbUtil();
		$results= F60DbUtil::getProductTypsByProductId($product_id);
		$i=0;
      			
		$strSelect = "var c = document.getElementById(\"lkup_wine_color_type_id\");";
		
		if($product_id == 3)//sake
			$strSelect .= "c.options.length=0;c.options['0']=new Option('Sake', '3', false, false);";
		else
			$strSelect .= "c.options.length=0;c.options['0']=new Option('All', '-1', false, false);";
		
		
		
		
		$rows = count($results);
		
		if(count($results)!=0)
		{	
		 
			for ($i=0;$i<count($results); $i++)
			{
			 	$j=$i+1;
		 		$type = $results[$i]["caption"];
		 		$id = $results[$i]["product_type_id"];
				$strSelect .= 'c.options['.$j.']=new Option("'.$type.'", "'.$id.'", false, false);';
			}
		}
//	 $strSelect ="alert('test');";
		return $strSelect;
	}
     
    function processForm()
    {
        //  $this->sURL = "main.php?page_name=estateAdd";
          return true;
          
          //tnor makskie liser lmain p ?klbane 
    }

}

?>
