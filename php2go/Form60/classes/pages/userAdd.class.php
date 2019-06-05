<?php

/**
 * Perform the necessary imports
 */

import('Form60.base.F60FormBase');
import('Form60.bll.bllusers');
import('Form60.base.F60DbUtil');

class userAdd extends F60FormBase
{
	   var $user_id ;
	   
	   var $bllData;
	   

    	function userAdd()
    	{
            if (F60FormBase::getCached()) exit(0);
            
            $this->user_id = $this->getRecordID();

            if ($this->editMode())
            {
                $title = "  Change user";
            }
            else
                $title = "  Add user";

            F60FormBase::F60FormBase('userAdd', $title, 'addnewuser.xml', 'addnewuser.tpl', 'btnAdd');
          
            $this->addScript('resources/js/javascript.users.js');
          	$this->attachBodyEvent('onLoad', 'initUserPage("userAdd");');

            $form = & $this->getForm();
            $form->setFormAction($_SERVER["REQUEST_URI"]);
            import('Form60.base.F60PageStack');
            F60PageStack::addtoPageStack();

            $this->registerActionhandler(array("btnAdd", array($this, processForm), "LASTPAGE", NULL));
            $this->registerActionhandler(array("btnAddAnother", array($this, processForm), "URL", "main.php?page_name=userAdd"));
            $this->registerActionhandler(array("delete", array($this, deleteData), "LASTPAGE", null));
           	$this->form->setButtonStyle('btnOK');
            $this->form->setInputStyle('input');
            $this->form->setLabelStyle('label');    
            
            
          
            
            //$usernameCtl =& $form->getField("username");

          //  $usernameCtl ->setValue("");       
       
       	//	$this->runtest();
       
	     }
       
       function runtest()
       {
			$blltest= new bllABVenderData();
		}
    	function display()
    	{
    	 	$this->setDisplayAction(false);
            if (!$this->handlePost())
                $this->displayForm();
        }

        function displayForm()
        {
            $form = & $this->getForm();

           if ($this->editMode())
            {
               $action = array(
                   "Add user" => "javascript:callSubmit('userAdd','btnAddAnother');",
                   "Delete user" => "javascript:runDelete(12);"
                );
                $this->loadData(&$form, $this->user_id);
            }
            else
            {
                $action = array(
                   "Add user" => "javascript:callSubmit('userAdd','btnAddAnother');",
                 // "Change user" => "main.php?page=userSearch",
                );
            }

            $this->setActions($action);
            $this->setFocus('userAdd', 'first_name');

            F60FormBase::display();
        }

        function validateInput(&$form, $user_id)
        {
            $username = $_POST["cs_username"];

            if (bllUsers::usernameExists($username, $user_id))
            {
                $form->addErrors("There is already a user with this name.");
                return FALSE;
            }
            else
                return TRUE;
        }

        function loadData(&$form, $user_id)
        {

            $users = & new bllusers();
            $user = $users->getByPrimaryKey($user_id);
            $user->loadDataToForm($form);

            //set extra fields
            $field = & $form->getField("repeatuserpass");
            $field->setValue($user->get_data(userpass));
        }

        function processForm()
        {
        
            if ($_POST["action_name"] == "btnAddAnother")
                F60PageStack::addtoPageStack(true); //force to stack
                
           $user_id = $_POST['user_id'];
             if (strlen($user_id)>0)
                $edit = TRUE;
            else  
            {
                $edit = FALSE;
                $user_id = null;
            }

			
            $form = & $this->getForm();
            
            $comUser_level=& $form->getField("user_level_id");
            
            $user_level_id = $comUser_level->getValue();
            
            
            if ($this->validateInput(&$form, $user_id))
            {
                $users = & new  bllusers();
                if ($edit)
                    $user = $users->getByPrimaryKey($user_id); //bllusers::getUser($user_id);
                else
                    $user = $users->add_new(); //& new bllusers();

                $user->getDataFromForm($form);
                
                if($user_level_id !=5) // if it is not the supplier, then estate_id should be 0
	                $user->set_data("estate_id", "0");
                
                $user->save();

                return true;
            }
            else
            {
                //allow to display the error
                return false;
            }
        }

		
		function processForm_all()
        {
        		$order= & new bllorder();
        		$order -> importALData(6);		
          
                return false;
          
        }


        function deleteData()
        {
           $user_id = $_POST['user_id'];
            $form = & $this->getForm();
            $users = & new  bllusers();
            $user = $users->getByPrimaryKey($user_id); //bllusers::getUser($user_id);
            $user->getDataFromForm($form);
            $user->is_deleted = true;
            $user->is_new = false;

        //delete the assignment if exist
          //  $sql = "update users_customers set deleted=1 where user_id = ".$user_id;

		//	$result = & F60DbUtil::runSQL($sql);



            return $user->save();
        }
}

?>