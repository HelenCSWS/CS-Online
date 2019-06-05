<?php

/**
 * Perform the necessary imports
 */

import('Form60.base.F60FormBase');
import('Form60.bll.bllwines');
import('Form60.bll.bllestates');
import('Form60.dal.dalwine_delivery_dates');
import('Form60.base.F60DbUtil');


define('DEFAULT_SAME_WINE', 'There is already a wine with this cspc code , please try again.');
define('DEFAULT_DIFF_PRICE', 'The price for this wine is different, you must create a unique name for this wine because of the price change.');
define('DEFAULT_SAME_DELIVERY', 'There already is a delivery for this wine on this date.');
define('DEFAULT_CANNOT_DEL_DELIVERY', 'There is not enough wine available to delete this delivery.Please review your existing allocations and orders for this wine.');

class wineAddCa extends F60FormBase
{
	var $wine_id ;
	var $estate_id;
    var $wine_delivery_date_id;
    var $editMode=0; //0: add new wine 1: update wine info 2: add new delivery 3: edit delivery
    var $is_international = false;


    	function wineAddCa()
    	{
            if (F60FormBase::getCached()) exit(0);

		
            $this->wine_id = $this->getRecordID();


            if($_REQUEST["is_international"]==1)
                $this->is_international=true;
                
            
            if($_REQUEST["province_id"]<>"")
            	$this->province_id =1;
            else
            	$this->province_id = $_REQUEST["province_id"];


        
                if ($_REQUEST["editMode"]=="")
                {
                    $this->editMode = 0;
                }
                else
                {
                    $this->editMode =$_REQUEST["editMode"];              


                    if($this->editMode ==3)
                        $this->wine_delivery_date_id = $_REQUEST["id"];

                }



               $this->estate_id = $_REQUEST["estate_id"];


               if ($this->editMode==0)
                {
                    $estate_name = &F60DbUtil::getEstateNamebyEstaetID($this->estate_id);
                }
                else
                {
                    if($this->is_international)//editmode=1 for international wine
                    {
                        $estate_name = &F60DbUtil::getEstateNamebyEstaetID($this->estate_id);
                        $this->wine_id =$_REQUEST["wineid"];
                    }
                    else
                    {
                        if ($this->editMode !=3 )
                        {

                            if($_REQUEST["id"]!="")
                            {
                                $this->wine_id =$_REQUEST["id"];
                            }

                            if ($this->wine_id =="")
                            {
                               $this->wine_id =$_REQUEST["wine_id_0"];
                            }
                            if ($this->wine_id =="")
                            {
                               $this->wine_id =$_REQUEST["wineid"];
                            }


                            $sql ="select estate_name, e.estate_id from estates e ,wines w where w.estate_id = e.estate_id and w.wine_id = ".$this->wine_id;
                            $result = & F60DbUtil::runSQL($sql);
                            if(!$result->EOF)
                            {
                               $row = & $result->FetchRow();
                               $estate_name =$row['estate_name'];
                               $this->estate_id=$row['estate_id'];

                            }
                        }
                        else
                        {
                            $wine_deliver_id =$this->wine_delivery_date_id;
                            $sql ="select estate_name, e.estate_id, w.wine_id from estates e ,wines w ,wine_delivery_dates wd where w.estate_id = e.estate_id and w.wine_id = wd.wine_id and wd.wine_delivery_date_id = ".$wine_deliver_id;
                            $result = & F60DbUtil::runSQL($sql);
                            if(!$result->EOF)
                            {
                               $row = & $result->FetchRow();
                               $estate_name =$row['estate_name'];
                               $this->estate_id=$row['estate_id'];
                               $this->wine_id =$row['wine_id'];
                               
                            }

                        }//if ($this->editMode !=3 )
                    }//if($this->is_internatnional)
                }

               



                $pageid = 25;
       
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

            if ($this->is_international)
                F60FormBase::F60FormBase('wineAdd', $title, 'addwine_in.xml', 'addwine_in.tpl', 'btnAdd');
            else
                F60FormBase::F60FormBase('wineAdd', $title, 'addwineCa.xml', 'addwineCa.tpl', 'btnAdd');
                
            $this->addScript('resources/js/javascript.pageAction.js');

            //$this->addToPageStack();
            $form = & $this->getForm();

            $form->setFormAction($_SERVER["REQUEST_URI"]);

            import('Form60.base.F60PageStack');
            F60PageStack::addtoPageStack();

            $sUrl ='main.php';
            $this->registerActionhandler(array("btnAdd", array($this, processForm), "LASTPAGE", $sUrl));


            if($this->is_international)
            {
                $URL ='main.php?page_name=wineAdd&is_international=1&estate_id='.$this->estate_id;
            }
            else
               $URL ='main.php?page_name=wineAdd&estate_id='.$this->estate_id;
 
            $this->registerActionhandler(array("btnAddAnother", array($this, processForm), "URL", $URL));


            $URL = "main.php?page_name=wineAdd&editMode=2&wine_id_0=".$this->wine_id."&test=1";

            $this->registerActionhandler(array("btnAddAnotherDelivery", array($this, processForm), "URL", $URL));

				$URL ="main.php";
            $this->registerActionhandler(array("delete", array($this, deleteWine), "URL", $URL));
            $this->registerActionhandler(array("delete_delivery", array($this, deleteDelivery), "URL", $URL));

            $this->form->setButtonStyle('btnOK');
            $this->form->setInputStyle('input');
            $this->form->setLabelStyle('label');

            $estate =& $form->getField("estate_id");

            $estate ->setValue($this->estate_id);

            $edt_pageid =& $form->getField("pageid");
            $edt_pageid->setValue($pageid);
            
            $edt_pageid =& $form->getField("is_international");
            $edt_pageid->setValue($_REQUEST["is_international"]);


            $wineid =  & $form->getField("wineid"); //herere
            $wineid ->setValue($this->wine_id);


            if(!$this->is_international)
                $this->setCtrlsDisable($this->editMode,$form);

            $this->attachBodyEvent('onLoad', 'setForm("wineAdd");');

            //The 2nd call is needed to set date format
           $this->attachBodyEvent('onFocus', 'setForm("wineAdd");');


            $edt_editMode =& $form->getField("editMode");
            $edt_editMode->setValue($this->editMode);

            if ($this->editMode =="3") //update delivery
            {
                   $edt_wine_delivery_id = & $form->getField("wine_delivery_date_id");

                    $edt_wine_delivery_id->setValue($this->wine_delivery_date_id);
             }


        }
        function checkAlct($wine_id)
        {
            $sql="select unallocated from wine_allocations where wine_id =".$wine_id;
            $result = & F60DbUtil::runSQL($sql);
            if(!$result->EOF)
            {
                return true;
            }
            else
                return false;
        }

        function setCtrlsDisable($editMode)
        {
           $form = & $this->getForm();
           switch ($editMode)
            {
                case 0:

                    break;
                case 1://edit wine

                    $edtDeliveryDate =& $form->getField("delivery_date");
                    $edtDeliveryDate ->setDisabled(true);

                    if ($this->checkAlct($this->wine_id))
                    {
                        $edtBtlsPerCase =& $form->getField("bottles_per_case");
                        $edtBtlsPerCase ->setDisabled(true);
                    }

                    $edt_show_totalbts =& $form->getField("show_total_bottles");

                    $edt_show_totalbts ->setDisabled(true);


                   $edt_show_totalbts =& $form->getField("total_cases");

                    $edt_show_totalbts ->setDisabled(true);

                    break;
                case 2: //add new delivery
                    $edtName =& $form->getField("wine_name");
                    $edtName ->setDisabled(true);

                    $edtName =& $form->getField("lkup_bottle_size_id");
                    $edtName ->setDisabled(true);

                    $edtName =& $form->getField("lkup_wine_color_type_id");
                    $edtName ->setDisabled(true);

                    $edtName =& $form->getField("cspc_code");
                    $edtName ->setDisabled(true);

                    $edtName =& $form->getField("vintage");
                    $edtName ->setDisabled(true);

                    $edtName =& $form->getField("price");
                    $edtName ->setDisabled(true);

                    $edtName =& $form->getField("wholesale");
                    $edtName ->setDisabled(true);

                    $edtName =& $form->getField("bottles_per_case");
                    $edtName ->setDisabled(true);
                  break;
                case 3: //edit  delivery
                    $edtName =& $form->getField("wine_name");
                    $edtName ->setDisabled(true);

                    $edtName =& $form->getField("lkup_bottle_size_id");
                    $edtName ->setDisabled(true);

                    $edtName =& $form->getField("lkup_wine_color_type_id");
                    $edtName ->setDisabled(true);

                    $edtName =& $form->getField("cspc_code");
                    $edtName ->setDisabled(true);

                    $edtName =& $form->getField("vintage");
                    $edtName ->setDisabled(true);

                    $edtName =& $form->getField("price");
                    $edtName ->setDisabled(true);

                   $edtName =& $form->getField("wholesale");
                    $edtName ->setDisabled(true);

                    if ($this->checkAlct($this->wine_id))
                    {
                      $edtName =& $form->getField("total_cases");
                      $edtName ->setDisabled(true);
                    }
                    $edtName =& $form->getField("bottles_per_case");
                    $edtName ->setDisabled(true);
                     break;

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

            if ($this->editMode==0)
            {
               $action = array(
                   "Add wine" => "javascript:callSubmit('wineAdd','btnAddAnother');",
                );
             //   $this->loadData(&$form, $this->wine_id,$this->wine_delivery_date_id);
            }
            if ($this->editMode==1)
            {
               $action = array(
                   "Add wine" => "javascript:callSubmit('wineAdd','btnAddAnother');",
                   "Delete wine"=>"javascript:runDelete(25);",
                );

                $this->loadData(&$form, $this->wine_id,$this->wine_delivery_date_id);
            }
            elseif($this->editMode ==2)
            {
               $action = array(
                   "Add delivery" => "javascript:callSubmit('wineAdd','btnAddAnotherDelivery');",
                 //  "Delete wine"=>"javascript:submitAction('wineAdd', 'delete');",
                );

             $this->loadData(&$form, $this->wine_id,$this->wine_delivery_date_id);
            }

             elseif($this->editMode ==3)
            {
               $action = array(
                   "Add delivery" => "javascript:callSubmit('wineAdd','btnAddAnotherDelivery');",
                   "Delete wine"=>"javascript:runDelete(25);",
                   "Delete delivery"=>"javascript:runDelete(19);",//"javascript:submitAction('wineAdd', 'delete_delivery');",
                );

                $this->loadData(&$form, $this->wine_id,$this->wine_delivery_date_id);

                $edtCase =& $form -> getField("total_cases");
                $edtB4Case =& $form -> getField("bottles_per_case");
                
                $deliveryT =($edtCase->getValue())*($edtB4Case->getValue());
                
                $edtDeliveryTotal = & $form -> getField("delivery_total");
                $edtDeliveryTotal ->setValue($deliveryT);
            }


            $this->setActions($action);

            if( $cmdSize=& $form->getField('wine.lkup_bottle_size_id'))
            {
               $cmdSize->setStyle('select');
            }

            if( $edtEstateID=& $form->getField('estate_id'))
            {
               $edtEstateID->setValue($this->estate_id);
            }

            if($this->is_international)
            {
               if( $wine_delivery_date_id=& $form->getField('wine_delivery_date_id'))
                {
                   $wine_delivery_date_id->setValue($this->wine_delivery_date_id);
                }
            }

         //   if( $deliver_date=& $form->getField('delivery_date'))
           // {
                if ($this->editMode !=0)
                {
                    if(!$this->is_international)
                    {
                        $price_per_unit=& $form->getField('price_per_unit');
                        $price=& $form->getField('price');
                        $price ->setValue($price_per_unit->getValue());
                    }
                    else
                    {
                        $profit_per_unit=& $form->getField('profit_per_unit');
                        $profit=& $form->getField('profit');
                        $profit ->setValue($profit_per_unit->getValue());

                       $cost_per_unit=& $form->getField('cost_per_unit');
                        $cost=& $form->getField('cost');
                        $cost ->setValue($cost_per_unit->getValue());
                    }
                    $price_winery=& $form->getField('price_winery');
                    $price=& $form->getField('wholesale');
                    $price ->setValue($price_winery->getValue());

                   
                }
                else
                {
                   // $deliver_date->setValue(F60Date::initDate());
                }
//            }

          $this->setFocus('addwine','wine_name');

          F60FormBase::display();


        }

        function loadData(&$form, $wine_id,$wine_delivery_date_id=NULL)
        {
            $wines = & new bllwines();

         
            $wine = $wines->getByPrimaryKey($wine_id);
            $wine->loadDataToForm($form,$this->editMode,loadDataToForm);

            if(!$this->is_international)
            {
                 if ($this->editMode == 1)
                {
                    $wine->loadDataToForm4Update($form,$this->wine_delivery_date_id);
                    $sql = "SELECT sum(wd.total_cases) cases
                            FROM `wine_delivery_dates` wd,wines w
                            where wd.deleted=0 and wd.wine_id = w.wine_id and w.wine_id = " .$wine_id;

                    $result = & F60DbUtil::runSQL($sql);
                        if(!$result->EOF)
                        {
                           $row = & $result->FetchRow();
                           $cases =$row['cases'];
                           $edtName = & $form->getField("total_cases");

                            $edtName ->setValue($cases);

                        }
                }
                else if ($this->editMode > 1)
                {
                    $wine->loadDataToForm4Update($form,$this->wine_delivery_date_id);
                    
                }
            }
				else
				{
				 	if($this->editMode>0)
				 	{
						$editPrice = &$form->getField("price_per_unit");
						$edtDisplayPrice = &$form->getField("display_price");
						$edtDisplayPrice ->setValue($editPrice->getValue());
					}
				}//if(!$this->is_international)

        }

        //check validate input , check if there is a same wine name ,vintage, esate_id alread in db
        function validateInput(&$form, $wine_id)
        {
            $winename = $_POST["wine_name"];
            $vintage = $_POST["vintage"];
            $estate_id = $_POST["estate_id"];
            $price_per_unit = $_POST["price_per_unit"];
            $delivery_date = $_POST["delivery_date"];
            $cspc_code = $_POST["cspc_code"];
            $vintage = $_POST["vintage"];

            $wine_delivery_date_id = $_POST["wine_delivery_date_id"];

            if ($this->editMode <=1 )
            {
                if (bllwines::cspcExists($cspc_code,$wine_id,$vintage,$winename))
                {
                    $form->addErrors(DEFAULT_SAME_WINE);
                    return FALSE;
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
        
        function processForm()
        {
            if ($this->is_international)
            {
             // print herere;
                $form = & $this->getForm();
                $wine_id = $_REQUEST["wineid"];
                
                 if ($this->validateInput(& $form, $wine_id))
                {
                    $wines = & new  bllwines();
                    switch ($this->editMode)
                    {
                        case 0: //add wine
                            $wine = $wines->add_new();
                           // $wine ->loadByPrimaryKey4Delivery("",$form);
                        break;
                         
                        case 1: //update wiine
                              $wine = $wines->getByPrimaryKey($wine_id);
                              $wine->is_new=false;
                             break;
                    }
               
                     $wine->getDataFromForm($form,$this->is_international);


                    return $wine->save($this->editMode,$this->is_international);
                     // false;
                }
            }
            else
            {
                 return $this->processForm_ca();
                // return false;
            }


        }
        function processForm_ca()
        {

            if ($_POST["action_name"] == "btnAddAnother")
                F60PageStack::addtoPageStack(true); //force to stack


            $form = & $this->getForm();

            $wineid =  & $form->getField("wineid");
            $wine_id = $wineid ->getValue();

            if ($wine_id=="")
                $wine_id =$_REQUEST["wine_id_0"];
        


            if (strlen($wine_id)>0)
                $edit = TRUE;
            else
            {
                $edit =False;
                $wine_id = null;
            }

            $edt_totalbts =& $form->getField("total_bottles");
            $old_total =$edt_totalbts->getValue();
            
            $edt_show_totalbts =& $form->getField("show_total_bottles");
            $new_bottles =$edt_show_totalbts->getValue();
            
            $edt_old_deliveries=& $form->getField("delivery_total");
            $old_deliveies =$edt_old_deliveries->getValue();
          

           switch ($this->editMode)
            {
                case 0: //add wine
                    $this->isWineryPriceNull(& $form);
                    $edt_totalbts->setValue( $edt_show_totalbts->getValue());
                    break;
                case 1: //update wiine
                    $this->isWineryPriceNull(& $form);
                     break;
                case 2:
                    
                     $tbottles =$edt_totalbts->getValue();
                        $edt_totalbts->setValue( ($tbottles + $edt_show_totalbts->getValue()));
                        break;
               case 3:
                          $tbottles =$edt_totalbts->getValue();
                        if ($new_bottles > $old_deliveies )
                        {
                           $newtotal=$new_bottles -$old_deliveies;
                           $old_total =$old_total+$newtotal;
                        }
                        else if ($new_bottles < $old_deliveies )
                        {
                            $newtotal=$old_deliveies-$new_bottles;
                           $old_total =$old_total-$newtotal;
                        }

                        $edt_totalbts->setValue( $old_total);
                        break;
  


            }

            if ($this->validateInput(&$form, $wine_id))
            {
                $wines = & new  bllwines();

                 switch ($this->editMode)
                {
                    case 0: //add wine
                        $wine = $wines->add_new();
                        $wine ->loadByPrimaryKey4Delivery("",$form);
                        break;
                    case 1: //update wiine
                        $wine = $wines->getByPrimaryKey($wine_id);
                        break;
                    case 2:

                            $wine = $wines->getByPrimaryKey($wine_id);
                            $wine ->loadByPrimaryKey4Delivery($this->wine_delivery_date_id,$form);

                             break;
                    case 3:

                            $wine = $wines->getByPrimaryKey($wine_id);
                            $wine ->loadByPrimaryKey4Delivery($this->wine_delivery_date_id,$form);
                            break;



                }

                $wine->getDataFromForm($form,$this->is_international);

             
              return $wine->save($this->editMode,$this->is_international);
//             return true;
            }
            else
            {
                //allow to display the error
                return false;
            }

        }



        function deleteData($is_del_delivery)
        {
            $form = & $this->getForm();

            $wineid =  & $form->getField("wineid");//hhhhh
            $wine_id = $wineid ->getValue();
            
          
            $wines = & new  bllwines();
            $wine = $wines->getByPrimaryKey($wine_id);

            if($this->is_international)
            {
               
                return $wine->deleteInternationalWine();
//                return false;
            }
            else
            {
                if ($is_del_delivery==1)
                {
                    $edt_allbts = & $form->getField("total_bottles");
                    $allBts =$edt_allbts->getValue();
                    $edt_bts =& $form->getField("show_total_bottles");
                    $bts =$edt_bts->getValue();


                    $allBts =$allBts -$bts;

                   $wine -> loadByPrimaryKey4Delivery($this->wine_delivery_date_id,$form);
                   return $wine ->delete(1,$allBts);
                }
                else
                {

                   $wine->getDataFromForm($form,$this->is_international);

                    return $wine->delete(0);
                }
                return true;
            }
        }


        function deleteDelivery()
        {

            $form = & $this->getForm();
            if($this->isEnoughWine(&$form))
                return $this->deleteData(1);
        }

        function deleteWine()
        {

           return $this->deleteData(0);
        }
}

?>
