<?php

/**
 * Perform the necessary imports
 */

import('Form60.base.F60FormBase');
import('Form60.bll.bllwines');
import('Form60.dal.dalwines');
import('Form60.base.F60DbUtil');
import('php2go.util.HtmlUtils');

class f60PreSearch extends F60FormBase
{
	var $estate_id ;
	var $pageid;
   var $is_international=false;
  // link = "main.php?page_name=f60PreSearch&search_id=".search_id."&search_key=".search_key."&year=".year."&month=".month."&quarter=".quarter."&isQtr=".isQtr."&store_type=".store_type_id."&user_id=".user_id."&isStart=".start_with."&search_adt=".search_adt."&wine_type=".wine_type;
   
	var $search_id ="";
	var $search_key ="";
	var $year ="";
	var $period ="";
	var $isQtr ="";
	var $store_type_id ="";
	var $user_id ="";
	var $start_with ="";
	var $search_adt ="";
	var $search_adt2 ="";
	var $wine_type ="";
	var $city="";
	
	var $province_id;
	var $product_id;
   
   
	function f60PreSearch()
	{
       if (F60FormBase::getCached()) exit(0);
       
       $title = "Select product ";

       F60FormBase::F60FormBase('f60Select', $title, 'f60preselect.xml', 'f60preselect.tpl');
       $this->addScript('resources/js/javascript.f60search.js');

	
       //$this->addToPageStack();

      // $form->setFormAction('main.php?page_name=wineSelect');

     // $this->registerActionhandler(array("btnNext", array($this, processForm), "URL", "main.php?page_name=allocatewine"));
       $this->registerActionhandler(array("btnClose", array($this, processForm), "URL", "main.php"));
       $this->form->setButtonStyle('btnOK');
       $this->form->setInputStyle('input');
       $this->form->setLabelStyle('label');

		$this->province_id = $_COOKIE["F60_PROVINCE_ID"];

		$this->search_id =$_REQUEST["search_id"];
		$this->search_key =$_REQUEST["search_key"];
		
		$this->search_key = str_replace("'","\'",$this->search_key);
		$this->search_adt =$_REQUEST["search_adt"];
		$this->year =$_REQUEST["year"];
		$this->period =$_REQUEST["sale_period"];
		$this->isQtr =$_REQUEST["isQtr"];
		$this->store_type_id =$_REQUEST["store_type"];

		$this->user_id =$_REQUEST["user_id"];
		$this->start_with =$_REQUEST["isStart"];
		
			
		
		$this->city =$_REQUEST["city"];
		$this->product_id =$_REQUEST["product_id"];
		
	//	echo $this->product_id;
	
      	$form = & $this->getForm();
       
		$this->setValue2Ctl($this->search_id,"search_id",$form);
	
		$this->setValue2Ctl($this->search_key,"search_key",$form);
		$this->setValue2Ctl($this->year,"year",$form);
		$this->setValue2Ctl($this->period,"period",$form);
		$this->setValue2Ctl($this->isQtr,"isQtr",$form);
		$this->setValue2Ctl($this->store_type_id,"store_type_id",$form);
		$this->setValue2Ctl($this->user_id,"user_id",$form);
		$this->setValue2Ctl($this->search_adt,"search_adt",$this->form);
		$this->setValue2Ctl($this->start_with,"start_with",$this->form);
		$this->setValue2Ctl($this->city,"city",$this->form);
		$this->setValue2Ctl($this->product_id,"product_id",$this->form);
				
		
		$this->search_adt =$_REQUEST["search_adt"];
		
		if($this->search_id==3)
			$this->wine_type =$_REQUEST["search_adt"];
        
      $this->attachBodyEvent('onLoad', 'forminit();');
    }

	function display()
	{
      if (!$this->handlePost())
            $this->displayForm();
      
   }
   function setValue2Ctl($val,$ctlName)
   {

		$ctl = & $this->form->getField($ctlName);
		$ctl->setValue($val);
	}
   function displayForm()
   {
		if($this->product_id==1)
		 	$this->displayForm4Wines();
		else
			$this->displayForm4Beers();
	
	}

	function getQuery4Wine()
	{

		
		$sql="select w.wine_name wine_name, max(vintage) vintage, wf.cspc_code, lkclr.display_name type,lkblt.display_name bottle_size,w.bottles_per_case 
				from wines w, wines_info wf,lkup_wine_color_types lkclr, lkup_bottle_sizes lkblt
				where w.wine_id=wf.wine_id 
				and w.lkup_wine_color_type_id = lkclr.lkup_wine_color_type_id
				and w.lkup_bottle_size_id =lkblt.lkup_bottle_size_id
				and wf.province_id=$this->province_id
				and w.deleted=0
				and wf.deleted=0
				"	;
		
		
		$wine_fileter="";
		$beginChac="%";
		$ca_wine_filter="";
		
		if($this->start_with==1)
		{
			$beginChac="";
		}
		
		$adt_search_field=" w.wine_name ";
		$adt_ca_search_field=" w.wine_name ";
		
		if(is_numeric($this->search_key))
		{
			$adt_search_field = " wf.cspc_code ";
			$adt_ca_search_field=" w.cspc_code ";
		}
		
		if( (intval($this->search_id) <2))
		{
				
			
				$wine_fileter = " and $adt_search_field like '$beginChac$this->search_key%'";
				$ca_wine_fileter = " and $adt_ca_search_field like '$beginChac$this->search_key%'";
				
		}
		else
		{
		 				
				
				 	if($this->wine_type ==1)
				 	{
						
						$wine_fileter = " and wf.cspc_code like '$beginChac$this->search_key%'";
						$ca_wine_fileter = " and w.cspc_code like '$beginChac$this->search_key%'";
						
					}
					else
					{
						$wine_fileter = " and w.wine_name like '%$beginChac$this->search_key%'";
						$ca_wine_fileter = " and w.wine_name like '%$beginChac$this->search_key%'";
					}
				
			
		}		
		$grpBy= " group by wf.cspc_code";		
			
		$sql=$sql.$wine_fileter.$grpBy;
		
		if($this->province_id==1)
		{
		 	$sql_ca="select w.wine_name wine_name,vintage, w.cspc_code, lkclr.display_name  type,lkblt.display_name bottle_size,w.bottles_per_case 
				from wines w, lkup_wine_color_types lkclr, lkup_bottle_sizes lkblt, estates e
				where w.estate_id =e.estate_id
				and w.lkup_wine_color_type_id = lkclr.lkup_wine_color_type_id
				and w.lkup_bottle_size_id =lkblt.lkup_bottle_size_id
				and e.billing_address_country='Canada'
				and e.estate_id=e.estate_id
				and w.deleted=0
				and e.deleted=0
				and w.cspc_code<>''
				"	;
			$grpBy= " group by w.cspc_code ";
			$sql=$sql." union $sql_ca
						$ca_wine_fileter
						$grpBy
						";
		}
		
		
		$orderBy=" order by wine_name";
		
		
		$sql=$sql.$orderBy;
		
		return $sql;
	}
	function getQuery4Beer()
	{
		
		$productfilter="";
		if($this->product_id ==2)
		{
			$productfilter =" and w.lkup_beer_type_id<200"; // beer
		}
		else if($this->product_id ==3) //sake
		{
			$productfilter =" and w.lkup_beer_type_id>=200 and w.lkup_beer_type_id<300"; // 
		}
		else if($this->product_id ==4)//spirits
		{
			$productfilter =" and w.lkup_beer_type_id>=300"; // 
		}
		$sql="select w.beer_name product_name, wf.cspc_code, lktype.caption type,lkblt.display_name bottle_size,w.bottles_per_case 
				from beers w, beers_info wf,lkup_beer_types lktype, lkup_beer_sizes lkblt
				where w.beer_id=wf.beer_id 
				and w.lkup_beer_type_id = lktype.lkup_beer_type_id
				and w.lkup_beer_size_id =lkblt.lkup_beer_size_id
				$productfilter
				and wf.province_id=$this->province_id
				and w.deleted=0
				and wf.deleted=0
				"	;
		
		
		$wine_fileter="";
		$beginChac="%";
		
		if($this->start_with==1)
		{
			$beginChac="";
		}
		
		$adt_search_field=" w.beer_name ";
		
		if(is_numeric($this->search_key))
		{
			$adt_search_field = " wf.cspc_code ";
		}
		
		if( (intval($this->search_id) <2))
		{
							
			$wine_fileter = " and $adt_search_field like '$beginChac$this->search_key%'";
				
		}
		else
		{				
		 	if($this->wine_type ==1)
		 	{						
				$wine_fileter = " and wf.cspc_code like '$beginChac$this->search_key%'";		
			}
			else
			{
				$wine_fileter = " and w.beer_name like '%$beginChac$this->search_key%'";
			}
				
			
		}		
		$grpBy= " group by wf.cspc_code";		
			
		$sql=$sql.$wine_fileter.$grpBy;
		

		
		$orderBy=" order by beer_name";
		
		
		$sql=$sql.$orderBy;
		
		return $sql;
	}
	
	function displayForm4Wines()
	{
	 
	   $form = & $this->getForm();
		
	
		$edtWineid = & $form->getField("wine_id");
		
		$sql =$this->getQuery4Wine();
		
	
		$result = & F60DbUtil::returnSQL($sql);
		
		
		$i=0;
		$ids=0;
		
		
		$rows= count($result);
	
		if($rows!=0)
		{
		
		
			for($row=0;$row<$rows;$row++)
		   {
		    
				if($row==0)
					$first_id =$result[$row]["cspc_code"];

				$cmbWines = & $form->getField("wine_id");
				$wine_name = $result[$row]["wine_name"];
				$wine_name=str_replace('- okan','',$wine_name);
				$wine_name=str_replace('- Okan','',$wine_name);
				$wine_name=str_replace('-okan','',$wine_name);
				$wine_name=str_replace('- Vic','',$wine_name);
				$wine_name=str_replace('- vic','',$wine_name);
				$wine_name=str_replace('-vic','',$wine_name);
				$cspc_code = $result[$row]["cspc_code"];
				
				$bottle_size = $result[$row]["bottle_size"];
				
				$bottle_per_case = $result[$row]["bottles_per_case"];
				
				
				$wine_color= $result[$row]["type"];
				$vintage  = $result[$row]["vintage"];
				if(Intval($vintage)==0)
					$vintage ="";
				$wine_id =$result[$row]["wine_id"];
				$wine_name = str_pad($cspc_code,6,'0',STR_PAD_LEFT)." - ".$wine_name . " " . $vintage . "  (" . $wine_color.") - ".$bottle_size." (".$bottle_per_case.")";
				$cmbWines ->addOption($cspc_code,$wine_name);
			//	$ids=$ids.$wine_id."|";
				
				$i++;		
		      
			}
			
			if($row==1)
			{
			 			
				$sURL="main.php?page_name=f60SearchWinesReports&search_id=".$this->search_id."&search_key=".$this->search_key."&year=".$this->year."&period=".$this->period."&isQtr=".$this->isQtr."&store_type=".$this->store_type_id."&user_id=".$this->user_id."&search_adt1=".$cspc_code."&city=".$this->city."&search_adt2=".$this->search_adt."&isOneRec=1&product_id=$this->product_id";

			//echo $sURL;
				HtmlUtils::redirect($sURL);
				//re direct
			}
			$cmbWines ->removeOption(0);
			$edtIndexs = & $form->getField("indexs");
			$edtIndexs ->setValue ($i);
	
		
			$cmbWines->setFirstOption($first_id);
			
			
			
			Registry::set('first_id', $first_id);
	}
	else
	{
	//display no wines info here.	
		$edtCtl = & $form->getField("isNoWine");
		$edtCtl ->setValue ("1");
	}

		F60FormBase::display();
	 
	}
	
	function displayForm4Beers()
	{
	 
	   $form = & $this->getForm();
		
	
		$edtWineid = & $form->getField("beer_id");
		
		$sql =$this->getQuery4Beer();
		
	
		$result = & F60DbUtil::returnSQL($sql);
		
		
		$i=0;
		$ids=0;
		
		
		$rows= count($result);
	
		if($rows!=0)
		{
		
		
			for($row=0;$row<$rows;$row++)
		   {
		    
				if($row==0)
					$first_id =$result[$row]["cspc_code"];

				$cmbWines = & $form->getField("wine_id");
				$wine_name = $result[$row]["product_name"];
				
				$cspc_code = $result[$row]["cspc_code"];
				
				$bottle_size = $result[$row]["bottle_size"];
				
				$bottle_per_case = $result[$row]["bottles_per_case"];
				
				
				$wine_color= $result[$row]["type"];
			
				$vintage ="";
				$wine_id =$result[$row]["beer_id"];
				$wine_name = str_pad($cspc_code,6,'0',STR_PAD_LEFT)." - ".$wine_name. " " . $vintage . "  (" . $wine_color.") - ".$bottle_size." (".$bottle_per_case.")";
				$cmbWines ->addOption($cspc_code,$wine_name);
			//	$ids=$ids.$wine_id."|";
				
				$i++;		
		      
			}
			
			if($row==1)
			{
			 			
				$sURL="main.php?page_name=f60SearchWinesReports&search_id=".$this->search_id."&search_key=".$this->search_key."&year=".$this->year."&period=".$this->period."&isQtr=".$this->isQtr."&store_type=".$this->store_type_id."&user_id=".$this->user_id."&search_adt1=".$cspc_code."&city=".$this->city."&search_adt2=".$this->search_adt."&isOneRec=1&product_id=$this->product_id";

			//echo $sURL;
				HtmlUtils::redirect($sURL);
				//re direct
			}
			$cmbWines ->removeOption(0);
			$edtIndexs = & $form->getField("indexs");
			$edtIndexs ->setValue ($i);
	
		
			$cmbWines->setFirstOption($first_id);
			
			
			
			Registry::set('first_id', $first_id);
	}
	else
	{
	//display no wines info here.	
		$edtCtl = & $form->getField("isNoWine");
		$edtCtl ->setValue ("1");
	}

		F60FormBase::display();
	 
	}

       
	function processForm()
	{
	   return true; 
	}

}

?>
