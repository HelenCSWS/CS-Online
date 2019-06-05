<?php

/**
 * Perform the necessary imports
 */

import('Form60.base.F60FormBase');
import('Form60.base.F60NotesList');
import('Form60.bll.bllestates');
import('Form60.bll.bllcontacts');

define('DEFAULT_PAYMENT_INFO_1', 'Please call with credit card number, or make cheque payable to ');

define('DEFAULT_PAYMENT_INFO_2', '<<Add Estate name>>');

define('DEFAULT_PAYMENT_INFO_3', ' Unit #2139 -11871 Horseshoe Way Richmond, B.C. V7A 5H5 Telephone 604.274.8481');

class estateAdd extends F60FormBase
{
	var $estate_id ;
	var $is_international =false;
    
	function estateAdd()
	{
		if (F60FormBase::getCached()) exit(0);
		
		$this->estate_id = $this->getRecordID();
		
		if ($_REQUEST["is_international"]==1)
		{
		    $this->is_international=true;
		}
		
		if ($this->editMode())
		{
		    $title = "  Edit estate";
		
		}
		else
		    $title = "  Add estate";
		
		
		if($this->is_international)
		    F60FormBase::F60FormBase('estateAdd', $title, 'addestate_in.xml', 'addestate_in.tpl', 'btnAdd');
		else
		    F60FormBase::F60FormBase('estateAdd', $title, 'addestate.xml', 'addestate.tpl', 'btnAdd');
		    
		$this->addScript('resources/js/javascript.pageAction.js');
		$this->addScript('resources/js/javascript.notes.js');
		
		$form = & $this->getForm();
		$form->setFormAction($_SERVER["REQUEST_URI"]);
            
      import('Form60.base.F60PageStack');
      F60PageStack::addtoPageStack();

      $sUrl ='main.php';
      $this->registerActionhandler(array("btnAdd", array($this, processForm), "LASTPAGE", null));
      
      if($this->is_international)
          $this->registerActionhandler(array("btnAddAnother", array($this, processForm), "URL", "main.php?page_name=estateAdd&is_international=1"));
      else
          $this->registerActionhandler(array("btnAddAnother", array($this, processForm), "URL", "main.php?page_name=estateAdd"));
          
      $this->registerActionhandler(array("delete", array($this, deleteData), "LASTPAGE", null));

		$sUrl ="main.php?page_name=estateSelect&pageid=24"; //add a new wine
		$this->registerActionhandler(array("btnAddWine", array($this, processForm), "URL", $sUrl));
		
		$this->form->setButtonStyle('btnOK');
		$this->form->setInputStyle('input');
		$this->form->setLabelStyle('label');
		
		if($this->is_international)
		{
			$edtIs_inter=& $form->getField("is_international");
			$edtIs_inter->setValue("1");		
		}
		
		$this->attachBodyEvent('onLoad', 'setForm("estateAdd");');
		
		if(!$this->is_international)
		{
			$this->attachBodyEvent('onLoad', 'setPaymentSize();');
			$this->attachBodyEvent('onResize', 'setPaymentSize();');
		}
            
	}
	function cancelForm()
	{
	  return false;
	}
	
	function display()
	{
	   if (!$this->handlePost())
	       $this->displayForm();
	}


	function displayForm()
	{
	   $form = & $this->getForm();
	
	   if ($this->editMode())
	   {
	      $action = array(
	          "Add estate" => "javascript:callSubmit('estateAdd','btnAddAnother');",
	         "Add wine" => "javascript:callSubmit('estateAdd','btnAddWine');",
	         "Delete estate"=>"javascript:runDelete(1);",
	       );
	       $this->loadData(&$form, $this->estate_id);
	       
	     if($this->is_international)
	     {
	       $edtCountry=& $form->getField("billing_address_country");
	       $edtCountry_2=& $form->getField("billing_address_country_2");
	
	
	       $edtCountry_2 ->setValue($edtCountry->getValue());
	     }
	
	   }
	   else
	   {
	       $action = array(
	          "Add estate" => "javascript:callSubmit('estateAdd','btnAddAnother');",
	          "Add wine" => "javascript:callSubmit('estateAdd','btnAddWine');",
	       );
	   }

      if (!$this->is_international)
      {
          $this->displayForms_ca();
      }


       $this->setActions($action);

      $notesControl = & new F60NotesList(&$this, "estate", ($this->editMode())?$this->estate_id:0);
      $form->Template->assign("note_contents", $notesControl->getContent());
      $this->setFocus('estateAdd','name');
   
      F60FormBase::display();
	}
        
        
	function displayForms_ca()
	{
	   $form = & $this->getForm();

	   if ($this->editMode())
	   {
	      
	   }
	   else
	   {
	  //     $cmdDelivery = & $form->getField("wine_delivery_date");
//	       $cmdDelivery->setDisabled(true);
	   }
	  
	   $edtPaymentInfo=& $form->getField('estateAdd.payment_info');
	   if (!$this->editMode())
	   {
			$DEFAULT_PAYMENT_INFO =DEFAULT_PAYMENT_INFO_1.DEFAULT_PAYMENT_INFO_2.DEFAULT_PAYMENT_INFO_3;
			$edtPaymentInfo ->setValue($DEFAULT_PAYMENT_INFO);
			
			if($_COOKIE["F60_PROVINCE_ID"]==2)
			{
				$edtProvince=& $form->getField("billing_address_state");
				$edtProvince->setValue("AB");
			}
	   }
	  
	}

	function validateInput(&$form, $estate_id)
	{
	   $estatename = $_POST["estate_name"];
	   if (bllestates::estatenameExists($estatename, $estate_id))
	   {
	       $form->addErrors("There is already an estate with this name.");
	       return FALSE;
	   }
	   
	   if(!$this->is_international)
	   {
	       $estatenumber = $_POST["estate_number"];
	       if (bllestates::estatenumberExists($estatenumber, $estate_id))
	       {
	           $form->addErrors("There is already an estate with this number.");
	           return FALSE;
	       }
	       else
	           return TRUE;
	   }
	   else
	       return true;
	}

	function loadData(&$form, $estate_id)
	{
		$estates = & new bllestates();
		$estate = $estates->getByPrimaryKey($estate_id);
		
		$estate->loadDataToForm($form, $this->is_international);
	
		if (!$this->is_international)
		{
		 	if(!$this->editMode())
		 	{
				$edtPaymentInfo=& $form->getField('estateAdd.payment_info');
				
				$DEFAULT_PAYMENT_INFO =DEFAULT_PAYMENT_INFO_1.$estate->get_data("estate_name").DEFAULT_PAYMENT_INFO_3;
				$edtPaymentInfo ->setValue($DEFAULT_PAYMENT_INFO);
			}
		}
	
	}
    
	function processForm()
	{
        
		if ($_POST["action_name"] == "btnAddAnother")
		    F60PageStack::addtoPageStack(true); //force to stack
		    
		$form = & $this->getForm();
		$estate_id = $_REQUEST['estate_id'];
		
		
		if($this->is_international)
		{
		    $edtCountry=& $form->getField("billing_address_country");
		
		    $isCountry = $_REQUEST['isCountry'];
		    if ($isCountry==1)
		    {
		        $edtCountry ->setValue($_REQUEST['billing_address_country_1']);
		    }
		    else
		        $edtCountry ->setValue($_REQUEST['billing_address_country_2']);
		}
                   
             
		if (strlen($estate_id)>0)
			$edit = true;
		else
		{
		 	$edit = false;
		 	$estate_id = null;
		}
		
		if ($this->validateInput(&$form, $estate_id))
		{
			$estates = & new  bllestates();
		 	if ($edit)
		   	$estate = $estates->getByPrimaryKey($estate_id);
		 	else
		   	$estate = $estates->add_new(); //& new estates();
		
		 $estate->setInternational($this->is_international);
		 $estate->getDataFromForm($form,$edit);
		 $estate->set_data("deleted", "0"); //This will save the temp. record added by note
		     
			return  $estate->save($form,$edit);
		}
		else
		{
			return false;
		}

   }

	function deleteData()
	{
		$form = & $this->getForm();
		
		$estateid =  & $form->getField("estate_id");
		$estate_id = $estateid->getValue();
		$estates = & new  bllestates();
		
		$estate = $estates->getByPrimaryKey($estate_id);
		
		return $estate->delete($this->is_international);
	}

}

?>
