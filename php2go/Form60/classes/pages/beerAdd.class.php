<?php

/**
 * Perform the necessary imports
 */

import('Form60.base.F60FormBase');
import('Form60.bll.bllBeers');
import('Form60.bll.bllestates');

import('Form60.bll.bllf60wines');

import('Form60.base.F60DbUtil');


define('DEFAULT_SAME_WINE', 'There is already a wine with this cspc code , please try again.');
define('DEFAULT_DIFF_PRICE', 'The price for this wine is different, you must create a unique name for this wine because of the price change.');
define('DEFAULT_SAME_DELIVERY', 'There already is a delivery for this wine on this date.');
define('DEFAULT_CANNOT_DEL_DELIVERY', 'There is not enough wine available to delete this delivery.Please review your existing allocations and orders for this wine.');

class beerAdd extends F60FormBase
{
	
		var $estate_id;
	
		var $editMode=0; //0: add new wine 1: update wine info 2: add new delivery 3: edit delivery
		var $is_international = 0;
		var $province_id ="";
		
		var $pros =2;
		

		var $beer_id="";
		
		var $isNewBeer=true;
		
		var $arryBeerInfo=array();
		var $oldBeerData=array();
		
		var $pageid=0;
		
		
    	function beerAdd()
    	{
    	 
    	 	
            if (F60FormBase::getCached()) exit(0);


			$this->pageid=$_REQUEST["pageid"];

			
			$this->province_id = $_COOKIE["F60_PROVINCE_ID"];
				        
			$beer_ids ="";
			if($_REQUEST["beer_id"]!=null&&$_REQUEST["beer_id"]!="")
			{
				$this->beer_id=$_REQUEST["beer_id"];
				$beer_ids=$_REQUEST["beer_ids"];
				$this->isNewBeer=false;	
			}
			else
			{
				$this->isNewBeer=true;
			}
			
			if($this->pageid==46)//beer
			{
				$typefilter = " lkup_beer_type_id<100 ";
			}
			else if($this->pageid==45)//sake
			{
				$typefilter = " lkup_beer_type_id=200 ";
			}
			else if($this->pageid==50)//spirits
			{
				$typefilter = " lkup_beer_type_id>200 ";
			}
			
			Registry::set('typefilter', $typefilter);	
			
			
  		    $this->estate_id =$_REQUEST["estate_id"];


            $estate_name = F60DbUtil::getEstateName($this->estate_id);
              
            $title="Save product for $estate_name";           

	        F60FormBase::F60FormBase('beerAdd', $title, 'addbeer.xml', 'addbeer.tpl', 'btnAdd');
                
            $this->addScript('resources/js/javascript.jquery.js');
            $this->addScript('resources/js/javascript.beers.js');
            $this->addScript('resources/js/javascript.common.js');
  
            $form = & $this->getForm();
            $form->setFormAction($_SERVER["REQUEST_URI"]);

            import('Form60.base.F60PageStack');
            F60PageStack::addtoPageStack();

            $sUrl ='main.php';
            $this->registerActionhandler(array("btnAdd", array($this, processForm), "LASTPAGE", $sUrl));

            $URL ="main.php?page_name=beerAdd&estate_id=$this->estate_id&pageid=$this->pageid";         
            $this->registerActionhandler(array("btnAddAnother", array($this, processForm), "URL", $URL));

			$URL ="main.php";

            $this->form->setButtonStyle('btnOK');
            $this->form->setInputStyle('input');
            $this->form->setLabelStyle('label');

            $estate =& $form->getField("estate_id");
            $estate ->setValue($this->estate_id);
            
            $estate =& $form->getField("beer_ids");
            $estate ->setValue($beer_ids);
            
         }

    	function display()
    	{
          if (!$this->handlePost())
              $this->displayForm();
        }

		function displayForm()
		{
			$form = & $this->getForm();

			if(!$this->isNewBeer)
			{				
				$action = array(
						
						"Delete beer"=>"javascript:delBeer(0);",					
					);
				
				$this->setActions($action);	
				$this->loadBeersInfo();				
			}
			F60FormBase::display();
		}
      
      	function loadBeersInfo()
      	{
			$this->loadBasicInfo();
			$this->loadProvinceInfos();
		}
    
		function loadBasicInfo()
		{
			$bllBeer= new bllBeers();
			
			$rowBeer=$bllBeer->getBeerBasicInfoByBeerId($this->beer_id);
			
			if($rowBeer!=0)
			{
				$this->setValueToCtl("beer_name"		,$rowBeer[0]["beer_name"]);
				$this->setValueToCtl("beer_id"			,$rowBeer[0]["beer_id"]);
				$this->setValueToCtl("bottles_per_case"	,$rowBeer[0]["bottles_per_case"]);
				$this->setValueToCtl("bottles_per_pack"	,$rowBeer[0]["bottles_per_pack"]);
				$this->setValueToCtl("lkup_beer_type_id",$rowBeer[0]["lkup_beer_type_id"]);
				$this->setValueToCtl("lkup_beer_size_id",$rowBeer[0]["lkup_beer_size_id"]);

				$this->oldBeerDate["beer_name"]			=$rowBeer[0]["beer_name"];
				$this->oldBeerDate["beer_id"]			=$rowBeer[0]["beer_id"];
				$this->oldBeerDate["bottles_per_case"]	=$rowBeer[0]["bottles_per_case"];
				$this->oldBeerDate["type_id"]			=$rowBeer[0]["type_id"];
				$this->oldBeerDate["size_id"]			=$rowBeer[0]["size_id"];
			}
			else
				echo "wrong";
		}
		
		function loadProvinceInfos()
		{
			$bllBeer= new bllBeers();
			
			$rowBeerInfo=$bllBeer->getBeerInfoByProvince($this->beer_id);

			if($rowBeerInfo!=0)
			{
				for($i=1; $i<=count($rowBeerInfo);$i++)
				{					
				 	$index=$i-1;
					$proid =$rowBeerInfo[$index]["province_id"];					
					$this->setValueToCtl("new_$proid",2); // need to update
					$this->setValueToCtl("cspc_code_$proid"		,$rowBeerInfo[$index]["cspc_code"]);
					$this->setValueToCtl("display_price_$proid"	,$rowBeerInfo[$index]["price_per_unit"],true);
					$this->setValueToCtl("wholesale_$proid"		,$rowBeerInfo[$index]["price_winery"],true);
					$this->setValueToCtl("cost_$proid"			,$rowBeerInfo[$index]["cost_per_unit"],true);
					$this->setValueToCtl("profit_$proid"		,$rowBeerInfo[$index]["profit_per_unit"],true);
					
					$this->setValueToCtl("case_sold_$proid"		,$rowBeerInfo[$index]["case_sold"]);
					
					$this->setValueToCtl("case_value_$proid"	,$rowBeerInfo[$index]["case_value"]);
					
					$this->oldBeerDate[$proid]["cspc_code"]		=$rowBeerInfo[$index]["cspc_code"];
					$this->oldBeerDate[$proid]["display_price"]	=$rowBeerInfo[$index]["price_per_unit"];
					$this->oldBeerDate[$proid]["wholesale"]		=$rowBeerInfo[$index]["price_winery"];
					$this->oldBeerDate[$proid]["cost"]			=$rowBeerInfo[$index]["cost_per_unit"];
					$this->oldBeerDate[$proid]["profit"]		=$rowBeerInfo[$index]["profit_per_unit"];
					$this->oldBeerDate[$proid]["case_sold"]		=$rowBeerInfo[$index]["size_id"];
					$this->oldBeerDate[$proid]["case_value"]	=$rowBeerInfo[$index]["case_value"];
				}
			}
		}
		
		function setValue2Ctl($val,$ctlName,$form)
		{
			$ctl = & $form->getField($ctlName);
			$ctl->setValue($val);
		}
			
		function setValueToCtl($ctlName,$value,$isCurrency=false)
		{
			$form = $this->form;
			   	
			$ctl = & $form->getField($ctlName);
			
			if($isCurrency)	
				$value="$".$value;
				
			$ctl->setValue($value);
		}
		
	  function getValueFromCtl($ctlName,$form=null)
	  {	   
	   		$form = $this->form;
			   	
			$ctl = & $form->getField($ctlName);			
			$retValue=$ctl->getValue();
			
			$retValue=str_replace("$","",$retValue);
			
			return trim($retValue);
		}
		
		//check validate input , check if there is a same wine name ,vintage, esate_id alread in db
		function validateInput()
		{
			if (!$this->bllWines->checkDuplicatWines ($this->form, $this->wine_id))
			{
				$this->form->addErrors(DEFAULT_SAME_WINE);
				return FALSE;
			}
					
			for ($proid =1; $proid<=2; $proid++)
			{
				$suffix=$this->getSuffix($proid);			
				$newName ="new".$suffix;				
				$saveMode=$this->getValueFromCtl($newName,$this->form);
				
				if($saveMode>0)
				{
					//check empty
					if(!$this->checkEmpty($proid))
					{				
						return false;
						break;
					}
				}			
			}					
			return true;
		}
       
       
        function processForm()
        {        	
         	$isChecked=0;
         	
         	$form=$this->form;
         	$retVal = false;
         	if($this->isFormValid())// add new beer
         	{          	
				if($this->saveBeerBasic())
				{				 
					$retVal = $this->saveBeerProvinceInfo();
					
					if(!$retVal)
					 	return false;
					else
						return $this->assignEstate2Beer();
				}
				else
					return false;
			}
			else
				return false;

			return true;
  	     }
  	     
  	     function deleteBeerForFialed()
  	     {
			//$bllBeer = new bllBeers();	
			return true;	
			//	return -> $bllBeer->deleteBeerForFailed($this->arryBeerInfo)
		 }
		 
		function assignEstate2Beer()
		{
			$bllBeer = new bllBeers();
		
			return $bllBeer->assignEstate2Beer($this->estate_id);
			
		}
		
  	     function saveBeerBasic()
  	     {
  	      	$bllBeer = new bllBeers();
  	      	if($this->arryBeerInfo['beer_id']=="")//update, check if beer need to update
  	      	{
				if(!$this->isBeerUpdated($this->arryBeerInfo,$this->oldBeerData))
				{
						return true;  // information not change
				}
			}

  	      	$retVal = $bllBeer->saveBeerBasicInfo($this->arryBeerInfo);
  	      
			if($retVal!=false)
  	      	{				
				if($this->arryBeerInfo['beer_id']=="")		
				{				 
					$this->arryBeerInfo['beer_id']=$retVal;	
					return true;
				}
				return true;
			}
			else
				return false;
				
		 }
		 
		 function saveBeerProvinceInfo()
		 {
		  	$modify_id =0;
		  	$bllBeer=new bllBeers();

		  	for ($proid =1; $proid<=2; $proid++)
		  	{
				$newName ="new_$proid";
				$modify_id = $this->getValueFromCtl($newName)-1;	//new_proid value: 1 add new. 2: update' modityid: 0:add, 1; update'
							
				if($modify_id>=0)
				{
					if(!$bllBeer->saveBeerProvinceInfo($this->arryBeerInfo,$proid,$modify_id))
						return false;
				}	
			}
			return true;
		 }
  	     
		
		function getBeerBasicInfoFromForm()
		{
		
			$this->arryBeerInfo["beer_name"]=$this->getValueFromCtl("beer_name");
			$this->arryBeerInfo["bottles_per_case"]=$this->getValueFromCtl("bottles_per_case");
			$this->arryBeerInfo["bottles_per_pack"]=$this->getValueFromCtl("bottles_per_pack");
			$this->arryBeerInfo["type_id"]=$this->getValueFromCtl("lkup_beer_type_id");
			$this->arryBeerInfo["size_id"]=$this->getValueFromCtl("lkup_beer_size_id");
			$this->arryBeerInfo["beer_id"]=$this->getValueFromCtl("beer_id");
			$this->arryBeerInfo["estate_id"]=$this->getValueFromCtl("estate_id");
		}
		
		function getBeerInfoByProFromForm($proid)
		{
		
			$cspc="cspc_code_$proid";			
		
			$beerProInfo["cspc_code"]=$this->getValueFromCtl("cspc_code_$proid");
			$beerProInfo["display_price"]=$this->getValueFromCtl("display_price_$proid");
			$beerProInfo["wholesale"]=$this->getValueFromCtl("wholesale_$proid");
			$beerProInfo["cost"]=$this->getValueFromCtl("cost_$proid");
			$beerProInfo["profit"]=$this->getValueFromCtl("profit_$proid");
			$beerProInfo["case_sold"]=$this->getValueFromCtl("case_sold_$proid");
			$beerProInfo["case_value"]=$this->getValueFromCtl("case_value_$proid");
			
			
			$this->arryBeerInfo["$proid"]=$beerProInfo;
			
		}
		
		function isFormValid()
		{
		 	
			$bllBeer= new bllBeers();
			
			$isSavePro=false;
			
		 	//check empty form
		 	$this->getBeerBasicInfoFromForm();
		 	
			if($bllBeer->checkDuplicatBasicBeers($this->arryBeerInfo))
			{
				$this->form->addErrors("There is a same beer in CS Online.");
				return false;
			}

			for ($proid =1; $proid<=2; $proid++)
			{
				//check update or add
				$newName ="new_$proid";
			//	$saveMode=$this->getValueFromCtl($newName,$this->form);
				
				if($this->getValueFromCtl($newName,$this->form>=1)) // add new
				{		
				 	$isSavePro =true;	
					if(!$this->checkControlsEmpty($proid))
						return false;
					else
					{
						$this->getBeerInfoByProFromForm($proid);
						if($bllBeer->checkDuplicateProvinceBeers($this->arryBeerInfo,$proid))
						{
						 	$province =($proid == "1")?"BC":"Alberta";
						 	$this->form->addErrors("There is a beer with same cspc_code for $province in CS Online.");
						 	return false; 
						}
					}
				}
			}
			
			if(!$isSavePro)
			{
				$this->form->addErrors("Please add province information for the beer.");
			 	return false; 
			}
			
			// no same beer
			return true;
			
		}
		

		
		function checkControlsEmpty($proid)
		{
		 	$province =($proid == "1")?"BC":"Alberta";
		 //		$this->form->addErrors("Please input the value to following fields: <br> $unValidCtls");
		 
		
			if($this->isControlEmpty("cspc_code_$proid"))
			{
			 	
				$this->form->addErrors("Please fill the CSPC code for $province first.");
				return false;
			}
			
			if($this->isControlEmpty("display_price_$proid"))
			{
				$this->form->addErrors("Please fill the display price for $province first.");
				return false;
			}	
			
			
			if($this->isControlEmpty("wholesale_$proid"))
			{
				$this->form->addErrors("Please fill the wholesale price for $province first.");
				return false;
			}
			
			if($this->isControlEmpty("cost_$proid"))
			{
				$this->form->addErrors("Please fill the cost for $province first.");
				return false;
			}
	
			if($this->isControlEmpty("profit_$proid"))
			{
			 	$this->form->addErrors("Please fill the profit for $province first.");
				return false;
			}
			
			
		/*	if($this->getValueFromCtl("profit_$proid")<0)	
			{
				$this->form->addErrors("Please correct the profit for $province.");
				return false;
			}
		*/		
			
			return true;
		}
		
		function isControlEmpty($ctlName)
		{
		 	$form=$this->form;
		 	
			if($this->getValueFromCtl($ctlName, $form)!=""&&$this->getValueFromCtl($ctlName, $form)!=0)
			{
			 	
				return  "";
			}
			else
			{
				return $ctlName;
			}
		
		}
		
		
		  
		  function deleteWine()
		  {
		
  	   		$isBc=false;
         	if($this->is_international==0)
         	{
					$isBc=true;	
				}
		
			   return $this->bllWines->deleteWine($this->wine_id,$isBc);
		
			   
		  }
		  
		/*
	
	*/
	function isBeerUpdated($aryBeer,$oldBeerData,$province_id="")
	{
	 
	 	
	 	$retVal=false;
	 	$aryBeerKeys = array_keys($aryBeer);
	 	
	 	if($province_id=="")	
	 	{
	 		//check basice info
		 	foreach ($aryBeerKeys as $beerKey)
		 	{
				if($beerKey!=1||$beerKey!=2)
				{
					if($aryBeer[$beerKey]!=$oldBeerData[$beerKey])
					{
						$retVal =true;
						break;
					}
				}
			}
		}
		else
		{
		
			//check province info
			$beerProvinceInfo=$aryBeer[$province_id];
			$beerOldProvinceInfo=$oldBeerData[$province_id];
			
			$aryBeerInfoKeys = array_keys($beerProvinceInfo);
			
			foreach ($aryBeerInfoKeys as $beerInfoKey)
			{
				if($beerProvinceInfo[$beerInfoKey]!=$beerOldProvinceInfo[$beerInfoKey])
				{
					$retVal =true;
					break;
				}
			}
		}
				
		return $retVal;
		
	}
		  
}

?>
