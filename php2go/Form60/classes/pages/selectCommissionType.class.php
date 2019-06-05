<?php

/**
 * Perform the necessary imports
 */

import('Form60.base.F60FormBase');
import('Form60.base.F60DbUtil');
import('Form60.base.F60DALBase');
import('Form60.bll.bllSalesCommission');

class selectCommissionType extends F60FormBase
{

	var $bllCommissionData;
	
	function selectCommissionType()
	{
		if (F60FormBase::getCached()) exit(0);
		
		$title = "Commission levels";
		
		
		F60FormBase::F60FormBase('commLevels', $title, 'selectSalesCommissionType.xml', 'selectSalesCommissionType.tpl','btnAdd');
		
		$this->addScript('resources/js/javascript.salesCommission.js');
		
		$form = & $this->getForm();
		$form->setFormAction($_SERVER["REQUEST_URI"]);	
		
		
		import('Form60.base.F60PageStack');
		F60PageStack::addtoPageStack();
		
		$this->registerActionhandler(array("btnAdd", array($this, processForm), "LASTPAGE", null));		
		
		$form->setButtonStyle('btnOK');
		$form->setInputStyle('input');
		$form->setLabelStyle('label');
		
		$this->bllCommissionData=new salesCommissionData();
		
		$this->attachBodyEvent('onLoad', 'loadForm();');    
    }

	function display()
	{
		if (!$this->handlePost())
			$this->displayForm();
	}
	
	function displayForm()
	{
		//$this->loadData();
		F60FormBase::display();
	}
	
	function getUsersHtmlByProvince($province_id)
	{

		$result=salesCommissionData::getUsersByProvince($province_id);
		$i=0;
		
		$strSelect = "var c = document.getElementById(\"user_id\");";
		$strSelect .= "c.options.length=0;";
		while(!$result->EOF)
		{
			$row=& $result->FetchRow();
		
			
			if ($i==0)
			{
				$strSelect .= 'c.options['.$i.']=new Option("'.$row['user_name'].'", "'.$row['user_id'].'", false, true);';
			}
			else
				$strSelect .= 'c.options['.$i.']=new Option("'.$row['user_name'].'", "'.$row['user_id'].'", false, false);';
				
			$i++;
		}
		$strSelect .='setCommissionTypeByUser();';
		return $strSelect;
	}
	
	function loadData()
	{	
		 $form=& $this->getForm();
		
		if($_REQUEST["province_id"]!="")
		{
			$userCtl=& $form->getField("province_id");
			
			$userCtl->setValue($_REQUEST["province_id"]);
		}
	}
       
    function processForm()
    {
     	return true;
    }

}


?>
