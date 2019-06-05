 <?php

/**
 * Perform the necessary imports
 */

import('Form60.base.F60FormBase');
//import('Form60.bll.bllusers');
import('Form60.base.F60AllocateGroup');
import('Form60.base.F60FristContent');
import('Form60.bll.bllnewallocates');


class allocatewine extends F60FormBase
{
		var $wine_ids;
		var $ctlContent;
		var $wineNos;
		var $pageid;

    	function allocatewine()
    	{
			$title = "Allocate wine";

			F60FormBase::F60FormBase('allocatewine', $title, 'allocateNewWine.xml', 'allocateNewWine.tpl', 'btnAdd');
			$this->addScript('resources/js/javascript.wineAllocate.js');
						
			$form = & $this->getForm();
			$form->setFormAction('main.php?page_name=allocatewine');
			$this->form->setButtonStyle('btnOK');
			$this->form->setLabelStyle('label');
			
			$wine_ids = $_REQUEST['wine_ids'];
			$this->pageid =$_REQUEST['pageid'];
			
			$edtids = & $this->form->getField("all_wine_ids");
			$edtids->setValue($wine_ids);
			
			if ($_REQUEST['unwine_ids']!="")
			    $wine_ids = $_REQUEST['unwine_ids'];
			
			$edtids = & $this->form->getField("wine_ids");
			$edtids->setValue($wine_ids);
			
			$edtid = & $this->form->getField("estate_id");
			$edtid->setValue($_REQUEST["estate_id"]);
			
			$edtpageid = & $this->form->getField("pageid");
			$edtpageid->setValue($_REQUEST["pageid"]);
			
			$this->wine_ids =split("[|]",$wine_ids);
			
			$this->ctlContent = new F60FristContent($this->wine_ids, $form,true);			
			
			if ($_REQUEST["pageid"]==18)
			    $sUrl ='main.php?page_name=allocatewine2customer&wine_ids='.$_REQUEST["all_wine_ids"].'&estate_id='.$_REQUEST["estate_id"];
			
			$this->registerActionhandler(array("btnSave", array($this, processForm), "URL", $sUrl));
			$this->registerActionhandler(array("btnAllocate", array($this, processForm), "URL", $sUrl));
			$this->attachBodyEvent('onLoad', 'initPage(0);');
       }
       
		function display()
		{
			if (!$this->handlePost())
				$this->displayForm();
		}

		function displayForm()
		{
			$form = & $this->getForm();
			
			$edtNumbers = & $form->getField("wine_numbers");
			$edtNumbers ->setValue(sizeof($this->wine_ids));
			$form->Template->assign("content_wine", $this->ctlContent->getContent());
			F60FormBase::display();
		}
		
		function checkAllocate(&$form)
		{		
			if (bllnewallocates::allcateExsit($_REQUEST["estate_id"]))
			{
				$this->form->addErrors("There is no allocated wine.");
				return FALSE;
			}		
		}
		
		function processForm()
		{
			$form = & $this->getForm();
			$allocate_wines = & new bllnewallocates();
			$edit = false;			
			$allocate_wines->getDataFromForm($form);
			
			return $allocate_wines->saveNew2DB($edit);
		
		}
}

?>