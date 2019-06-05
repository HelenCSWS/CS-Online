<?php

/**
 * Perform the necessary imports
 */

import('Form60.base.F60FormBase');
import('Form60.base.F60DbUtil');
import('Form60.base.F60DALBase');

//import('Form60.bll.bllusers');


class estateSelect extends F60FormBase
{
	var $wine_id ;
	var $estate_name;
	var $pageid;
	
	function estateSelect()
	{
        
		$this->pageid = $_REQUEST['pageid']; //check to which page id=19 -> addwine page
		

	
		
		$title = "  Select estate";
          
            
        if ($this->pageid==56) // Wine accessory
              $title = "  Select product";
            
		
		F60FormBase::F60FormBase('estateSelect', $title, 'selectestate.xml', 'selectestate.tpl');
		$this->addScript('resources/js/javascript.pageAction.js');
		
		$form = & $this->getForm();
		
		$URL ='main.php?page_name=estateSelect&pageid='.$this->pageid;
		$form->setFormAction($URL);
		
		import('Form60.base.F60PageStack');
		F60PageStack::addtoPageStack();

		if ($this->pageid==1) //goto change estate page
		{
		    $URL ="main.php?page_name=estateAdd&is_international=1&id=".$_REQUEST['estate_id'];
		}
		elseif ($this->pageid==24) //go to add wine page
		{
		    $URL ="main.php?page_name=wineAdd&estate_id=".$_REQUEST['estate_id']."&pageid=".pageid;
		}
		elseif ($this->pageid==25)  //update wine->go to select wine and delivery date time page to update a wine
		{
		
		    $URL ="main.php?page_name=beerAdd&estate_id=".$_REQUEST['estate_id']."&pageid=".pageid;
		}
	
		$this->form->setButtonStyle('btnOK');
		$this->form->setInputStyle('input');
		$this->form->setLabelStyle('label');
		
		$edt_pageid = & $this->form->getField("pageid");
		$edt_pageid->setValue($this->pageid);
		
		$edt_searchid = & $this->form->getField("search_id");
		$edt_searchid->setValue($_REQUEST["search_id"]);
		
		$edt_searchKey = & $this->form->getField("search_key");
		$edt_searchKey->setValue($_REQUEST["search_key"]);
		
		$this->attachBodyEvent('onLoad', 'checkEstates();');

    }

	function display()
	{
		if (!$this->handlePost())
		$this->displayForm();
	}

	function displayForm()
	{
	   	$form = & $this->getForm();
		
		$countries_num=$this->getCountryNums();
		
		$edtCountry =& $form->getField("country");
	
		if($this->pageid==56)
        {
            $country ="CSWS Products";
        }
        else
        {
       	    $country = $this->getCountry();
        }

	    if($countries_num==1)
	    {
	           $estate_id =$this->getEstateByCountry($country);
	           
	           if($country=="Canada")
	           {
	                if($this->pageid==1)
	                    $sURL="main.php?page_name=estateAdd&is_international=0&id=".$estate_id;
	                if($this->pageid==24)//add wine
	                    $sURL="main.php?page_name=wineAdd&estate_id=".$estate_id;
	                if($this->pageid==25)//add wine
	                    $sURL="main.php?page_name=wineSelect&is_international=0&estate_id=".$estate_id;
	
	           }
	           else
	           {
	                if($this->pageid==1)
	                    $sURL="main.php?page_name=estateAdd&is_international=1&id=".$estate_id;
	                 if($this->pageid==24)//add wine
	                    $sURL="main.php?page_name=wineAdd&is_international=1&estate_id=".$estate_id;
	                 if($this->pageid==25)//add wine
	                    $sURL="main.php?page_name=wineSelect&is_international=1&estate_id=".$estate_id;
	           }
	            
	           HtmlUtils::redirect($sURL);
	
	    }
	      
		if ($country!="")
		{
			$edtCountry->setFirstOption($country);
		
			$result =estateSelect::getEstatsByCountry($country);
						
			$edtEstate=& $form->getField("estate_id");
			
			
			
			$i=0;
			while(!$result->EOF)
			{
				$row=& $result->FetchRow();				
				$edtEstate->addOption($row['estate_id'],$row['estate_name'],"Select estate");
				$i++;
			}
		}
        
		$edtCountry->setValue($country);
		F60FormBase::display();


	}

	function getCountry()
	{
	 	$bee_estate="";
	 	
		$sql="select distinct billing_address_country from estates e where e.deleted =0 and e.billing_address_country<>'' $bee_estate order by billing_address_country";
		
		$result = & F60DbUtil::runSQL($sql);
		$row = & $result->FetchRow();
		$country=$row['billing_address_country'];
		return $country;
	}
	
	function getCountryNums()
	{
		$sql="select count(*) countrys from estates e where e.deleted =0 and e.billing_address_country<>'' order by billing_address_country";
		$result = & F60DbUtil::runSQL($sql);
		$row = & $result->FetchRow();
		$numbers=$row['countrys'];
		return $numbers;
	}

	function getEstatsByCountry($country)//for ajax called
	{
		if($country=="Canada")
			$sql="select estate_id, estate_name from estates e where e.billing_address_country='".$country."' and  e.deleted=0 order by estate_name asc";
		else
			$sql="select estate_id, concat(e.estate_name,' ',IFNULL(e.billing_address_state,'')) estate_name from estates e where e.billing_address_country='".$country."' and  e.deleted=0 and estate_id!=0 order by estate_name asc";
		$result = & F60DbUtil::runSQL($sql);
		
		return $result;
	}
	
	function getEstateByCountry($country)//for ajax called
	{
      if($country=="Canada")
          $sql="select estate_id, estate_name from estates e where e.billing_address_country='".$country."' and  e.deleted=0";
      else
 		  $sql="select estate_id, concat(e.estate_name,' ',IFNULL(e.billing_address_state,'')) estate_name from estates e where e.billing_address_country='".$country."' and  e.deleted=0";
    			
		$result = & F60DbUtil::runSQL($sql);
		$row = & $result->FetchRow();
		$estate_id=$row['estate_id'];

		return $estate_id;
	}

	function getInEstate4SelectHtml($controlID,$country)
	{

		$result=estateSelect::getEstatsByCountry($country);
		$i=0;
		
		$strSelect = "var c = document.getElementById(\"".$controlID."\");";
		$strSelect .= "c.options.length=0;";
		while(!$result->EOF)
		{
			$row=& $result->FetchRow();
			if ($i==0)
			{
				$strSelect .= 'c.options['.$i.']=new Option("'.$row['estate_name'].'", "'.$row['estate_id'].'", false, true);';
			}
			else
				$strSelect .= 'c.options['.$i.']=new Option("'.$row['estate_name'].'", "'.$row['estate_id'].'", false, false);';
				
			$i++;
		}
		
		return $strSelect;
	}

	function processForm()
	{
	   return true; //add by wenling
	}

}

?>
