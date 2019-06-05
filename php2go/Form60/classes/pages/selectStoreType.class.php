<?php

/**
 * Perform the necessary imports
 */

import('Form60.base.F60FormBase');
import('Form60.util.F60Date');
import('Form60.base.F60DALBase');
import('Form60.bll.bllSSDSData');



class selectStoreType extends F60FormBase
{
	
		var $period_id;

		



		function selectStoreType()
		{
			if (F60FormBase::getCached()) exit(0);

			F60FormBase::F60FormBase('selectStoreType', 'Sales summary report', 'selectStoreType.xml', 'selectStoreType.tpl');
			$this->addScript('resources/js/javascript.ssdsreports.js');
			$form = & $this->getForm();
			$this->form->setButtonStyle('btnOK');
			$this->form->setInputStyle('input');
			$this->form->setLabelStyle('label');


			$this->registerActionhandler(array("btnCancel", array($this, processForm), "URL", "main.php"));

			$action = array("Print help" => "javascript:printHelp();",);

           $this->setActions($action);

			//$this->attachBodyEvent('onLoad', 'setFristFocus();');

            $this->period_id =$_REQUEST["period_id"];
            $edtPeriodId=& $this->form->getField("period_id");
            $edtPeriodId->setValue($_REQUEST["period_id"]);

            $edtIsRecreate=& $this->form->getField("is_recreate");
            $edtIsRecreate->setValue($_REQUEST["is_recreate"]);

            $edtfsYear=& $this->form->getField("fiscal_year");
            $edtfsYear->setValue($_REQUEST["fiscal_year"]);
            
             $edtCurrentUserId=& $this->form->getField("current_user_id");
            $edtCurrentUserId->setValue($_REQUEST["user_id"]);
            
         
           if($_REQUEST["store_type"]!="")
            {
             	$cntl = & $this->form->getField("store_type");
	        		$cntl->setValue($_REQUEST["store_type"]);
	        		
				$this->attachBodyEvent('onLoad', 'setForm(1);');
			}
			
			$this->attachBodyEvent('onLoad', 'setUser();');
		}

    

	function display()
	{
		if (!$this->handlePost())
			$this->displayForm();
	}

	function displayForm()
	{
       $cmbUser=& $this->form->getField("user_id");
       $bllSSDS = new SSDSData();
       $users = $bllSSDS->getUsersByPeriodId($this->period_id);
       $nUsers = 0;
        if($users!=0)
        {
            $nUsers = count($users);
              $cmbUser->addOption("-1","All wine consultants",0);

             for($i=0;$i<=$nUsers-1;$i++)
             {
               $user_name =$users[$i]['user_name'];
               $user_id =$users[$i]['user_id'];

                $nIndex=$i+1;
                $cmbUser->addOption($user_id,$user_name,$nIndex);

             }
        }
        if($_REQUEST["user_id"]!="")
        {
			$cmbUser->setValue($_REQUEST["user_id"]);
		}
		else
        	$cmbUser->setValue("-1");
        	
        	
      //  $cmbUser->setValue(6);
        	
        	
        
        $edtUsers=& $this->form->getField("users");
        $edtUsers->setValue($nUsers);
        

       F60FormBase::display();
	}
	


	function processForm()
	{
		return true;
	}




}

?>
