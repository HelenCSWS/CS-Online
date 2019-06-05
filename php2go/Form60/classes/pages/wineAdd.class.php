<?php

/**
 * Perform the necessary imports
 */

import('Form60.base.F60FormBase');
import('Form60.bll.bllf60wines');
import('Form60.bll.bllestates');
import('Form60.dal.dalwine_delivery_dates');
import('Form60.base.F60DbUtil');


define('DEFAULT_SAME_WINE', 'There is already a wine with this cspc code , please try again.');
define('DEFAULT_DIFF_PRICE', 'The price for this wine is different, you must create a unique name for this wine because of the price change.');
define('DEFAULT_SAME_DELIVERY', 'There already is a delivery for this wine on this date.');
define('DEFAULT_CANNOT_DEL_DELIVERY', 'There is not enough wine available to delete this delivery.Please review your existing allocations and orders for this wine.');

class wineAdd extends F60FormBase
{
		var $wine_id ;
		var $estate_id;
		var $wine_delivery_date_id;
		var $editMode=0; //0: add new wine 1: update wine info 2: add new delivery 3: edit delivery
		var $is_international = 0;
		var $province_id ="";
		
		
		var $bllWines;
		var $wineBsInfo;
		
		var $wineInfos;
		var $pros =2;
		

	    var $wineCaInfo;
    	function wineAdd()
    	{
            if (F60FormBase::getCached()) exit(0);

		
            $this->wine_id = $_REQUEST["wine_id"];    
			$this->is_international = $_REQUEST["is_international"];     
			
		
            
           
				$this->province_id = $_COOKIE["F60_PROVINCE_ID"];
				        
            if ($_REQUEST["wine_id"]=="" ||$_REQUEST["wine_id"]==null)
            {
              
                $this->editMode = "0";
            }
            else
            {
                $this->editMode ="1";               
            }



  			  $this->bllWines = new bllf60wines();
				 
              $this->estate_id = $_REQUEST["estate_id"];


              $estate_name = F60DbUtil::getEstateName($this->estate_id);
              
              if($this->wine_id!="")
              {
               	//$this->setValue2Ctl($this->wine_id,"wine_id",$this->form);
						
						
						$this->wineBsInfo = $this->bllWines-> getWinesBasicInfo($this->wine_id);
						$this->wineInfos = $this->bllWines-> getWinesByWineID($this->wine_id);
						
						if($this->is_international==0)
						{
							$this->wineCaInfo = $this->bllWines-> getWinesBasicInfo($this->wine_id,true);
						}
				  }
				
			
            switch ($this->editMode)
            {
                case 0:
                    $title = "Add wine for ";
                    break;
                case 1:
                    $title ="Update wine for ";
                    break;
                case 2:
                    $title = "Add new delivery for ";
                    break;
                case 3:
                    $pageid = 19;
                    $title ="Update delivery for ";

                    break;

            }

                
            $title = $title . $estate_name;



            F60FormBase::F60FormBase('wineAdd', $title, 'addwine.xml', 'addwine.tpl', 'btnAdd');
                
            $this->addScript('resources/js/javascript.pageAction.js');
            $this->addScript('resources/js/javascript.wines.js');

          
            $form = & $this->getForm();
         

            $form->setFormAction($_SERVER["REQUEST_URI"]);

            import('Form60.base.F60PageStack');
            F60PageStack::addtoPageStack();

            $sUrl ='main.php';
            $this->registerActionhandler(array("btnAdd", array($this, processForm), "LASTPAGE", $sUrl));

            $URL ='main.php?page_name=wineAdd&editMode=0&estate_id='.$this->estate_id;
           
            $this->registerActionhandler(array("btnAddAnother", array($this, processForm), "URL", $URL));


				$URL ="main.php";
            //$this->registerActionhandler(array("btnDel", array($this, deleteWine), "URL", $URL));
            
            $this->registerActionhandler(array("btnDeleteWine", array($this, deleteWine), "URL", $URL));

            $this->form->setButtonStyle('btnOK');
            $this->form->setInputStyle('input');
            $this->form->setLabelStyle('label');

            $estate =& $form->getField("estate_id");

            $estate ->setValue($this->estate_id);

            $edt_editMode =& $form->getField("editMode");
            
            $edt_editMode->setValue($this->editMode);
            
            if($this->wine_id!="")
              {
               	$this->setValue2Ctl($this->wine_id,"wine_id",$this->form);
               }
            
            $this->setValue2Ctl($this->is_international,"is_international",$this->form);
             $this->attachBodyEvent('onLoad', 'loadWine();');
            

			$edtInter = & $this->form->getfield("is_international");
				
				if($this->is_international==0)
				{
					$edtInter->setValue("0");
				}
				
				if($this->editMode==0 && $this->is_international==0)
				{
					$edtCs =& $form->getField("total_cases");
					$edtBts =& $form->getField("bottles_per_case");
					$edtTbs =& $form->getField("total_bottles");
					
					$cs=$edtCs->getValue();
					$bpc=$edtBts->getValue();
					
					
					$btls = intval($cs)*intval($bpc);
					
			
					$edtTbs->setValue($btls);

            
				}
                
             
        }

      
        

    	function display()
    	{
          if (!$this->handlePost())
              $this->displayForm();
      }

		function displayForm()
		{
			$form = & $this->getForm();
		

			
		//	$action = array("Add wine" => "javascript:callSubmit('wineAdd','btnAddAnother');",);
			
		
			if($this->editMode==1)
			{
	
			 	$action = array(
                   "Delete wine"=>"javascript:runDelete(25);",
                );
                
            $this->setActions($action);
				$this->loadWineBsInfoById($this->form, $this->wineBsInfo);
		
				if($this->is_international==1 ) //internatinal
				{
				 
					$this->loadData4Provinces($this->form,$this->wine_id,$this->wineInfos)	;			
				}
				else
				{
				
				 		//if($this->wineCaInfo!=0)
								$this->loadData4CaWines($this->form,$this->wine_id,$this->wineBsInfo, $this->wineInfos)	;				
				}
                                
                                //get CSPC code
                                $cspc_code = $this->getValueFromCtl("cspc_code_bc",$form);
                                //set up store pen report check box here
                              /*  if (($cspc_code<>"") && $this->bllWines->isCSPCcodeinPenetrationReport($cspc_code))
                                {
                                    $chkIncludeinPenReport =& $form->getField("chkIncludeInStorePenReport");
                                    $chkIncludeinPenReport->setChecked();
                                }*/
			}
			$this->setFocus('addwine','wine_name');
			
			F60FormBase::display();
		
		}
      
      function loadWineBsInfoById ( $form, $row )
      {
 			$this->setValue2Ctl($row[0]['wine_name'],"wine_name",$form);
			$this->setValue2Ctl($row[0]['lkup_bottle_size_id'],"lkup_bottle_size_id",$form);
			$this->setValue2Ctl($row[0]['lkup_wine_color_type_id'],"lkup_wine_color_type_id",$form);
			$this->setValue2Ctl($row[0]['vintage'],"vintage",$form);
			$this->setValue2Ctl($row[0]['bottles_per_case'],"bottles_per_case",$form);

		}
        

		  function setValue2Ctl($val,$ctlName,$form)
		  {
		
				$ctl = & $form->getField($ctlName);
				$ctl->setValue($val);
			}
			
		  function getValueFromCtl($ctlName,$form)
		  {
				$ctl = & $form->getField($ctlName);
				return $ctl->getValue();
			}
		
		  function getSuffix($province_id)
		  {
				$suffix="_bc";

				if($province_id ==1 )
					{
						$suffix="_bc";
					}
					else if($province_id ==2 ) 
					{
						$suffix="_ab";
					}
					else if($province_id ==3 ) 
					{
						$suffix="_mb";
					}
				return $suffix;
				
				
		  }
		function loadData4CaWines(&$form, $wine_id,$rowBwine,$rowWineInfo)
		  {
	   		//bc wine for bc
	   
	   		if($this->wineCaInfo!=0)
	   		{
	   	
					$province_id =1;
					
					$suffix = $this->getSuffix($province_id);
					
					$newCtlName="new".$suffix;
					
				
					
					$i=0;
					
					$total_cases = $this->bllWines->getTotalCases($wine_id);
				
								
					$this->setValue2Ctl($rowBwine[$i]['cspc_code'],"cspc_code".$suffix,$form);
					
					$isNew ="2";
					if($rowBwine[$i]['cspc_code']==null or $rowBwine[$i]['cspc_code']=="")
					{
						$isNew ="0";
					}
				   $this->setValue2Ctl($isNew,$newCtlName,$form);
				   
					$this->setValue2Ctl($rowBwine[$i]['price_per_unit'],"display_price".$suffix,$form);
				   $this->setValue2Ctl($rowBwine[$i]['price_per_unit'],"price_per_unit".$suffix,$form);
	
		
					$this->setValue2Ctl($rowBwine[$i]['price_winery'],"wholesale".$suffix,$form);
				   $this->setValue2Ctl($rowBwine[$i]['price_winery'],"price_winery".$suffix,$form);
				   
					$this->setValue2Ctl($total_cases,"total_cases",$form);
				   $this->setValue2Ctl($this->wineCaInfo[$i]['total_bottles'],"total_bottles",$form);
	
				   $this->setValue2Ctl($rowBwine[$i]['case_value'],"case_value".$suffix,$form);
				}
				   
				   
				//bc wine for other province
				$nRows = count($rowWineInfo);
			
		//		print $nRows;
				if($rowWineInfo!=0  )
				{
				
					for($i=0;$i<=$nRows-1;$i++)
					{
					
						$province_id =$rowWineInfo[$i]['province_id'];
						if($province_id!=1)
						{
							
							$suffix = $this->getSuffix($province_id);
							
							$newCtlName="new".$suffix;
							
							$this->setValue2Ctl(2,$newCtlName,$form);
							
							$this->setValue2Ctl($rowWineInfo[$i]['cspc_code'],"cspc_code".$suffix,$form);
						  // $this->setValue2Ctl($row['lkup_bottle_size_id'],"lkup_bottle_size_id",$form);
							   
						   $this->setValue2Ctl($rowWineInfo[$i]['cost_per_unit'],"cost".$suffix,$form);
						   $this->setValue2Ctl($rowWineInfo[$i]['cost_per_unit'],"cost_per_unit".$suffix,$form);
						   
							$this->setValue2Ctl($rowWineInfo[$i]['profit_per_unit'],"profit".$suffix,$form);
						   $this->setValue2Ctl($rowWineInfo[$i]['profit_per_unit'],"profit_per_unit".$suffix,$form);
												   
							$this->setValue2Ctl($rowWineInfo[$i]['price_per_unit'],"display_price".$suffix,$form);
						   $this->setValue2Ctl($rowWineInfo[$i]['price_per_unit'],"price_per_unit".$suffix,$form);
		
						
							$this->setValue2Ctl($rowWineInfo[$i]['price_winery'],"wholesale".$suffix,$form);
						   $this->setValue2Ctl($rowWineInfo[$i]['price_winery'],"price_winery".$suffix,$form);					   
						   $this->setValue2Ctl($rowWineInfo[$i]['case_value'],"case_value".$suffix,$form);	
						}				   
					}
				}
			
		  }
		
	  function loadData4Provinces(&$form, $wine_id,$row)
	  {
		   	
		   	if($row!=0)
		   	{
				$nRows = count($row);
				
				
				for($i=0;$i<=$nRows-1;$i++)
				{
					$province_id =$row[$i]['province_id'];
					
					$suffix = $this->getSuffix($province_id);
					
					$newCtlName="new".$suffix;
					
					$this->setValue2Ctl(2,$newCtlName,$form);
					
					$this->setValue2Ctl($row[$i]['cspc_code'],"cspc_code".$suffix,$form);
					// $this->setValue2Ctl($row['lkup_bottle_size_id'],"lkup_bottle_size_id",$form);
					
					$this->setValue2Ctl($row[$i]['cost_per_unit'],"cost".$suffix,$form);
					$this->setValue2Ctl($row[$i]['cost_per_unit'],"cost_per_unit".$suffix,$form);
					
					$this->setValue2Ctl($row[$i]['profit_per_unit'],"profit".$suffix,$form);
					$this->setValue2Ctl($row[$i]['profit_per_unit'],"profit_per_unit".$suffix,$form);
					
					$this->setValue2Ctl($row[$i]['price_per_unit'],"display_price".$suffix,$form);
					$this->setValue2Ctl($row[$i]['price_per_unit'],"price_per_unit".$suffix,$form);
					
					$this->setValue2Ctl($row[$i]['price_winery'],"wholesale".$suffix,$form);
					$this->setValue2Ctl($row[$i]['price_winery'],"price_winery".$suffix,$form);
					
					$this->setValue2Ctl($row[$i]['case_value'],"case_value".$suffix,$form);
					
					$case_val=floatval($row[$i]['case_value']);
					  
				
					
					
					if($case_val<1)
					{
					
						
					
						
						if($case_val==1) //2:1
						{						
							$this->setValue2Ctl(1,"case_sold".$suffix,$form);	
						}
						else if($case_val==0.5) //2:1
						{						
							$this->setValue2Ctl(2,"case_sold".$suffix,$form);	
						}
						else if($case_val==0.25) //4:1
						{		
							
							$this->setValue2Ctl(4,"case_sold".$suffix,$form);	
						}
						else if($case_val==0.2) //5:1
						{						
							$this->setValue2Ctl(5,"case_sold".$suffix,$form);	
						}
						else if($case_val==0.125) //8:1 0.125 round up
						{						
							$this->setValue2Ctl(8,"case_sold".$suffix,$form);	
						}
						else
							$this->setValue2Ctl(3,"case_sold".$suffix,$form);

												   	
					}
					else
					{	
						$case_value = Intval($row[$i]['case_value']);
						$this->setValue2Ctl($case_value,"case_value".$suffix,$form);
					}
						   
						   
				}
			}
			
		}
               

        //check validate input , check if there is a same wine name ,vintage, esate_id alread in db
		function validateInput()
		{
			if (!$this->bllWines->checkDuplicatWines ($this->form, $this->wine_id))
			{
				$this->form->addErrors(DEFAULT_SAME_WINE);
				return FALSE;
			}
			
		
			for ($proid =1; $proid<=$this->pros; $proid++)
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
        function isEnoughWine(&$form)
        {
            $deliveries =$_POST["show_total_bottles"];
            $dbWines =$_POST["total_bottles"];

            if( $dbWines < $deliveries )
            {

                $form->addErrors(DEFAULT_CANNOT_DEL_DELIVERY);
                return FALSE;
            }
            else
                return true;
        }
        
        function isWineryPriceNull(& $form)
        {

            $edt_wholesale =& $form->getField("wholesale");
            $edt_winery_price =& $form->getField("price_winery");


            if($edt_wholesale->getValue()=="" ||$edt_winery_price->getValue()==0 )
            {
                $edt_price =& $form->getField("price_per_unit");
                $edt_price_winery =& $form->getField("price_winery");

                $edt_price_winery->setValue($edt_price->getValue());
            }
            return true;
        }
        
        function checkEmpty($proid)
        {
         
				$form=$this->form;
			 	$province="BC";
			 	
				 if($proid==2)
			 		$province="Alberta";
				
				$cspc_code = $this->bllWines->getFieldValue($form,"cspc_code",$proid);
				if($cspc_code=="")
				{
				 
					$this->form->addErrors("Please fill the cspc_code for $province first.");
                return FALSE;
				}
				
				
			 	$price_per_unit = $this->bllWines->getFieldValue($form,"price_per_unit",$proid);
			 		
			 	if($price_per_unit==""||$price_per_unit==0)
				{
				 
					$this->form->addErrors("Please fill the display price for $province first.");
                return FALSE;
				}
				
			 	$price_winery = $this->bllWines->getFieldValue($form,"price_winery",$proid);
			 	if($price_winery==""||$price_winery==0)
				{
					$this->form->addErrors("Please fill the csws price for $province first.");
                return FALSE;
				}
				
			
				if($proid==1 && $this->is_international==0)
				{
				 	if($this->wine_id=="")
				 	{
						$total_cases = $this->bllWines->getFieldValue($form,"total_cases");
						$btl_per_case = $this->bllWines->getFieldValue($form,"bottles_per_case");
						
					if(($total_cases==""||$total_cases==0)||($btl_per_case==""||$btl_per_case==0))
						{
							$this->form->addErrors("Please fill the total cases for $province first.");
		                return FALSE;
						}
					}
					
				}
				else
				{
				
				 /*	$cost_per_unit = $this->bllWines->getFieldValue($form,"cost_per_unit",$proid);
			 	
				 	if($cost_per_unit=="")
					{
						$this->form->addErrors("Please fill the cost for $province first.");
	                return FALSE;
					}*/
				}
				
				return true;

			}
			function processForm_test()
        {
         	
				$form=& $this->form;
				
				$edtTest= & $form->getField("total_bottles");
				print " btls".$edtTest->getValue();
         	
         	return false;
			}
         
        function processForm()
        {
         	$form=$this->form;
         	
         	$j=0;
         	for ($proid =1; $proid<=$this->pros; $proid++)
				{
					$suffix=$this->getSuffix($proid);
					//check update or add
					$newName ="new".$suffix;
					$saveMode=$this->getValueFromCtl($newName,$this->form);
					
					if($saveMode==0)
					{
						
						$j++;
						
					}
				}
				
			
				 
	         	$retVal=false;
	         	$wine_id =0;
	         	$isBc=false;
	         	
	         	if($this->validateInput())
	         	{
		         	
						if($this->is_international==0)
	         		{
	         		 	$edtCspc= & $form->getField("cspc_code_bc");
	         		 	
	         		 	if( $edtCspc->getValue()!="")
								$isBc=true;	
						}
					if($this->wine_id==""||$this->wine_id==null)//insert
						{
						
							$wine_id = $this->bllWines->insertWineBasicInfo($this->form,$isBc);
							if($isBc && $wine_id!=0)
							{
								$retVal = true;
							}
					
						}
						else
						{
						 	$wine_id = $this->wine_id;
							$retVal = $this->bllWines->updateWineBasicInfo($this->form,$this->wine_id,$isBc);	
						}
						for ($proid =1; $proid<=$this->pros; $proid++)
						{
						
							if($proid==1&&$this->is_international==0)
							{}
							else
							{
							
								$suffix=$this->getSuffix($proid);
								//check update or add
								$newName ="new".$suffix;
								$saveMode=$this->getValueFromCtl($newName,$this->form);
							
						
								if($saveMode==1)
								{
								 
								 	if($wine_id!==0)
								 		$retVal= $this->bllWines->insertWineInfoByProId($form,$proid,$wine_id);
								
														
								}
								else if($saveMode==2)
								{
						
									$retVal= $this->bllWines->updateWineInfo($form,$this->wine_id, $proid);
								}
							}//if($proid!=1&&$this->estate>2)
						}
					}					
				
			//	$this->validateInput();
			
					return $retVal;
			
				
				

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
		  
		  
}

?>
