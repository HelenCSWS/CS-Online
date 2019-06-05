<?php

/**
 * Perform the necessary imports
 */

import('Form60.base.F60FormBase');
import('Form60.base.F60MarketList');
//import('Form60.bll.bllestates');
import('Form60.bll.bllcontacts');


class customercpReport extends F60FormBase
{
	//var $estate_id ;
    var $isPrint=true;


	function customercpReport()
	{

           // if (F60FormBase::getCached()) exit(0);


            F60FormBase::F60FormBase('customercpreport', "Market update report", 'customercpreport.xml', 'customercpreport.tpl', 'btnAdd');
            $this->addScript('resources/js/javascript.pageAction.js');
            $this->addScript('resources/js/javascript.marketlist.js');

            $form = & $this->getForm();
            $form->setFormAction('main.php?page_name=customerCompare&step=3&cc_session_id=' . $_REQUEST['cc_session_id']);

            $sUrl ='main.php?page_name=';
            $this->registerActionhandler(array("btnAdd", array($this, processForm), "URL", $sUrl));

            $this->form->setButtonStyle('btnOK');

    }

    	function display()
    	{
            if (!$this->handlePost())
                $this->displayForm();
        }

        function displayForm()
        {
            $form = & $this->getForm();

            //    $this->loadData(&$form, $this->estate_id);

           // $this->setActions($action);

            $listControl = & new F60MarketList(&$this, 0, 10);
            $form->Template->assign("list_news", $listControl->getContent());


            $listControl2 = & new F60MarketList(&$this, 1, 10);
            $form->Template->assign("list_updates", $listControl2->getContent());

            $listControl3 = & new F60MarketList(&$this, 2, 10);
            $form->Template->assign("list_oobs", $listControl3->getContent());


            if ($this->isPrint)
            {
                $action = array(
    	            "Printable version" => "javascript:printReports();",
        	        "Export" => "javascript:exprotCPToExcel(" . $_REQUEST['cc_session_id'] . ");"
            	);
	            $this->setActions($action);
            }
            F60FormBase::display();
        }

        function processForm()
        {
         /*   $form = & $this->getForm();
            $estate_id = $_POST['estate_id'];
            if (strlen($estate_id)>0)
                $edit = TRUE;
            else
            {
                $edit = False;
                $estate_id = null;
            }

            if ($this->validateInput(&$form, $estate_id))
            {
                $estates = & new  bllestates();

                // print $estates;
                if ($edit)
                    $estate = $estates->getByPrimaryKey($estate_id);
                else
                    $estate = $estates->add_new(); //& new estates();

                $estate->getDataFromForm($form);
                $estate->set_data("deleted", "0"); //This will save the temp. record added by note

                return $estate->save($form,$edit);

            }
            else
            {
            //allow to display the error
             return false;
            }*/return true;

        }


}

?>