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
class customerSelect extends F60FormBase
{
	var $search_id ;
	var $estate_name;

	function customerSelect()
	{
		if (F60FormBase::getCached()) exit(0);
		
		$this->search_id = $_REQUEST['searchid'];
		$title = "Search";
		
		F60FormBase::F60FormBase('customerSelect', $title, 'customerSelect.xml', 'customerSelect.tpl');
		$this->addScript(PHP2GO_JAVASCRIPT_PATH . "libs/cookie.js");
		$this->addScript('resources/js/javascript.wineAllocate.js');
		
		$form = & $this->getForm();
		$form->setFormAction($_SERVER["REQUEST_URI"]);
		
		import('Form60.base.F60PageStack');
		F60PageStack::addtoPageStack();
		
		$this->registerActionhandler(array("btnClose", array($this, processForm), "LASTPAGE", NULL));
		$this->form->setButtonStyle('btnOK');
		$this->form->setInputStyle('input');
		$this->form->setLabelStyle('label');
		
		
		$wine_ids = $_REQUEST['wine_ids'];
		$edtallids = & $this->form->getField("wine_ids");
		$edtallids->setValue($wine_ids);
		
		$edtpageid =& $form->getField("pageid");
		$edtpageid ->setValue($_REQUEST["pageid"]);
		
		$this->attachBodyEvent('onLoad', 'set2Customer();');
	}

	function display()
	{
      if (!$this->handlePost())
          $this->displayForm();
	}

	function displayForm()
	{
	   $form = & $this->getForm();
	   F60FormBase::display();
	}


	function loadData(&$form, $customer_id)
	{
	   $fields = $form->getFieldNames();

	   foreach($fields as $fieldName)
	   {
	      $field = & $form->getField($fieldName);
	   }
	}

  function processForm()
  {
        return true;
     
  }

}

?>
