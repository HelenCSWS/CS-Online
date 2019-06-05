<?php

/**
 * Perform the necessary imports
 */

import('Form60.base.F60FormBase');
import('Form60.base.F60NotesList');
import('Form60.base.F60WineList');
import('Form60.base.F60OrderList');
import('Form60.base.F60SalesList');
import('Form60.bll.bllcustomers');
import('Form60.bll.bllcontacts');
import('Form60.util.F60Date');
import('php2go.datetime.TimeCounter');
import('Form60.base.F60DbUtil');
import('Form60.base.F60DALBase');
import('Form60.util.F60Common');
import('php2go.net.MailMessage'); 

class customerAdd extends F60FormBase
{
	var $customer_id ;
	var $isCompared=true;
	var $province_id;
	var $cfg;
	var $login_user_id;
	
	var $ccInfo = array('type'=>'', 'number'=>'','exp_month'=>'','exp_year'=>'');

	function customeradd()
	{
	 	// for send emai feature  --- July 09,2010
	 	include('config/emailoutconfig.php');
        $this->cfg = $EMAIL_CFG;
        
    	//$this->login_user_id = & F60DALBase::get_current_user_id();

		$isCompared = & F60DbUtil::isCustomerUpdateDone();
		if ($isCompared)
		{
			if (F60FormBase::getCached()) exit(0);
			
			$this->customer_id = $this->getRecordID();
			
			if ($_REQUEST["customer_id"]!="")
				$this->customer_id =$_REQUEST["customer_id"];
		
		
			$this->province_id = $_COOKIE["F60_PROVINCE_ID"];
			if ($this->editMode())
			{
				$customer_name =$this->getCustomer();
				$title = "  Change customer - $customer_name";
				F60FormBase::F60FormBase('customerAdd', $title, 'addcustomer.xml', 'editcustomer.tpl', 'btnAdd');
			}
			else
			{
				$title = "  Add customer";
				F60FormBase::F60FormBase('customerAdd', $title, 'addcustomer.xml', 'addcustomer.tpl', 'btnAdd');
			}

			$this->addScript('resources/js/javascript.pageAction.js');
			$this->addScript('resources/js/javascript.notes.js');
			$this->addScript('resources/js/javascript.winelist.js');
			$this->addScript('resources/js/javascript.orderlist.js');
			
			$form = & $this->getForm();
			$form->setFormAction($_SERVER["REQUEST_URI"]);
			    
			import('Form60.base.F60PageStack');
			F60PageStack::addtoPageStack();
		
			$sUrl ='main.php';
			$this->registerActionhandler(array("btnAdd", array($this, processForm), "URL", "main.php"));
			$this->registerActionhandler(array("btnAddAnother", array($this, processForm), "URL", "main.php?page_name=customerAdd"));
			$this->registerActionhandler(array("delete", array($this, deleteData), "URL", "main.php"));
			$this->registerActionhandler(array("allocate", array($this, saveCustomer),  "URL", "main.php"));
			
			$this->form->setButtonStyle('btnOK');
			$this->form->setInputStyle('input');
			$this->form->setLabelStyle('label');
			
			$this->attachBodyEvent('onLoad', 'setForm("customerAdd");');
		
			if ($this->editMode())
				Registry::set('current_customer_id', $this->customer_id);	

			else
				Registry::set('current_customer_id', 0);
				
			Registry::set('current_province_id', $this->province_id);
			Registry::set('current_user_province_id', $this->province_id);
		}
		else
		{
			$this->isCompared = false;
			F60FormBase::F60FormBase('customerAdd', "Infomation", 'customerInfo.xml', 'customerInfo.tpl', 'btnAdd');
		}
	}
 	function getCustomer()
	{
		$sql="select customer_name from customers where customer_id= $this->customer_id and deleted=0";
		$result = & F60DbUtil::runSQL($sql);
		$row = & $result->FetchRow();
		$customer_name=$row['customer_name'];
		return $customer_name;
	
	}
	function display()
	{
     if (!$this->handlePost())
         $this->displayForm();
   }

    function displayForm()
    {
        $form = & $this->getForm();
        if($this->isCompared )
        {
            $cmbExpYear = & $form->getField("cc_exp_year");
            if ($cmbExpYear)
            {
                $expYear=date("Y");

			//	$cmbExpYear ->addOption($expYear,$expYear,0);
				$j=3;
                for ($i=0; $i<=8; $i++)
                {
                 	if($i<=3)
                 	{
                 	 	$j--;
						$cc_expYear = $expYear -$j;	
					}
					else
					{
	                 	$j=$i-2;
					    $cc_expYear =  $expYear +$j;
	                }
                  
                    $cmbExpYear ->addOption($cc_expYear,$cc_expYear,$i);
                }
               // $cmbExpYear ->removeOption(0);
            }

            if ($this->editMode())
            {
					if($this->province_id==1)
					{
						$action = array(
						"Add customer" =>"javascript:callSubmit('customerAdd','btnAddAnother');", // "main.php?page=customerAdd",
						"Delete customer"=>"javascript:runDelete(4);",
						"Allocate wine" => "javascript:callSubmit('customerAdd','allocate');"
						);
					}
					else
					{
					
						$action = array(
						"Add customer" =>"javascript:callSubmit('customerAdd','btnAddAnother');", // "main.php?page=customerAdd",
						"Delete customer"=>"javascript:runDelete(4);"
						);
					}
            		$this->loadData(&$form, $this->customer_id);
                
					if ($this->customer_id)
					{
						$cmdMonth = & $form->getField("sales_month");
						$cmdMonth->setValue(date("n"));
						
						
						$cmdYear = & $form->getField("sales_year");
						$cmdYear->setValue(date("Y"));
					
						$current_month = date("n");
						$current_year = date("Y");
	
						$current_quarter = intval(date("n")/3)+1;
						
						
						$winelistControl = & new F60WineList($this, $this->customer_id);
						$winelistControl->estateID = -1; //will draw an empty list
						$form->Template->assign("wine_list", $winelistControl->getContent());
						
						$cmdOdMonth = & $form->getField("order_month");
						$cmdOdMonth->setValue(date("n"));
						
						$cmdOdYear = & $form->getField("order_year");
						$cmdOdYear->setValue(date("Y"));

						$cmdOdQut = & $form->getField("order_qut");
						$cmdOdQut->setValue(-1);
							
						$orderlistControl = & new F60OrderList($this, $this->customer_id,-1,$current_year,1);
						$form->Template->assign("orders_list", $orderlistControl->getContent());
						
						$cmdQut = & $form->getField("sales_qut");
						$cmdQut->setValue(-1);
						
						$cmdStore_type = & $form->getField("lkup_store_type_id");
						$salesListControl = & new F60SalesList($this, $this->customer_id,-1,$current_year,1,$cmdStore_type->getValue() , false);
						
						$form->Template->assign("sales_list", $salesListControl->getContent());
						
						$cntl = & $this->form->getField("quarter_desc");
						$cntl->setStyle("text");
						$cntl = & $this->form->getField("order_quarter_desc");
						$cntl->setStyle("text");
	            }
	            
	            //get credit card information
	            
	            $this->setValue2Ctl("old_lkup_payment_type_id",$this->getCtlValue("lkup_payment_type_id")); 
	            if($this->getCtlValue("lkup_payment_type_id")>2)
	            {
		            $this->setValue2Ctl("old_cc_number",$this->getCtlValue("cc_number")); 
		            $this->setValue2Ctl("old_cc_exp_month",$this->getCtlValue("cc_exp_month")); 
		            $this->setValue2Ctl("old_cc_exp_year",$this->getCtlValue("cc_exp_year")); 
		        }
	        }
            else
            {
             	if($this->province_id ==1 )
             	{
	                $action = array(
	                   				"Add customer" =>"javascript:callSubmit('customerAdd','btnAddAnother');",// "main.php?page=customerAdd&id=4",
	                    			"Allocate wine" => "javascript:callSubmit('customerAdd','allocate');"
	                				);
	            }
	            else
	            {
					$action = array("Add customer" =>"javascript:callSubmit('customerAdd','btnAddAnother');");
				}
            }

            $this->setActions($action);

            $notesControl = & new F60NotesList($this, "customer", ($this->editMode())?$this->customer_id:0,($this->editMode()?true:false));
            $form->Template->assign("note_contents", $notesControl->getContent());
            
            $this->setFocus('customerAdd', 'name');

            $customerid =  & $form->getField("customer_id");
            $customerid ->setValue($this->customer_id);

            $ctlProvince =  & $form->getField("province_id");
            $ctlProvince ->setValue($this->province_id);
            if ($_REQUEST['estate_id_order']!="")
            {
                $estate_id_order =  & $form->getField("estate_id_order");
                $estate_id_order->setValue($_REQUEST['estate_id_order']);
                $estate_id =  & $form->getField("estate_id");
                $estate_id->setValue($_REQUEST['estate_id_order']);
            }
            
            if ($_REQUEST['isorder']!="")
            {
                $isorder =  & $form->getField("isorder");
                $isorder ->setValue($_REQUEST['isorder']);
            }
            
            $cmdUser=& $form->getField("user_id");
            
         	if(!$this->editMode())
         	{
				$edtProvince=& $form->getField("billing_address_state");
				
				if($_COOKIE["F60_PROVINCE_ID"]==2)
					$edtProvince->setValue("AB");					
				else
					$edtProvince->setValue("BC");
			}

       }
       F60FormBase::display();
    }

	function getCtlValue($cntlName)
	{
	 	$ctl = & $this->form->getField($cntlName);
	 	
	 	return $ctl->getValue();
		
	}
	
	function setValue2Ctl($cntlName, $val)
	{
	 
	 	$ctl = & $this->form->getField($cntlName);
	 	
	 	return $ctl->setValue($val);
		
	}
	function getConfigVal($config)
    {
     	
        return $this->cfg[$config];
    }
    function validateInput(&$form, $customer_id)
    {
		$form = & $this->getForm();
		$cmbStorePriority = & $form->getField('lkup_store_priority_id');
		
		if ($cmbStoretype=& $form->getField('lkup_store_type_id'))
		{
			if ($cmbStoretype->getValue()=="6")
			{
				if (strlen($cmbStorePriority->getValue())==0 )
				{
					$form->addErrors("Please select priority.");
					return FALSE;
				}
				$cmbUser = & $form->getField("user_id");
				if (strlen($cmbUser->getValue())==0)
				{
					$form->addErrors("Please select user.");
					return FALSE;
				}
			}
		}

		$cmbPaymentType = & $form->getField('lkup_payment_type_id');

		if ($cmbMonth=& $form->getField('cc_exp_month'))
		{
			if ($cmbPaymentType->getValue()>2)
			{
				if (strlen($cmbMonth->getValue())==0 )
				{
					$form->addErrors("Please select month for expiry date.");
					return FALSE;
				}
			}
		}
		if ($cmbYear=& $form->getField('cc_exp_year'))
		{
			if ($cmbPaymentType->getValue()>2)
			{
				if (strlen($cmbYear->getValue())==0 )
				{
					$form->addErrors("Please select year for expiry date.");
					return FALSE;
				}
			}
		}
        $customername = $_POST["customer_name"];
        if (bllcustomers::customernameExists($customername, $customer_id))
        {
            $form->addErrors("There is already a customer with this name, please try again.");
            return FALSE;
        }
        else
            return true;
    }

    function loadData(&$form, $customer_id)
    {
        $customers = & new bllcustomers();
        $customer = $customers->getByPrimaryKey($customer_id);
        $customer->loadDataToForm($form);
    }

	function processForm()
	{
	
		if ($_POST["action_name"] == "btnAddAnother" || $_POST["action_name"] == "allocate")
			F60PageStack::addtoPageStack(true); //force to stack
		
		$form = & $this->getForm();
		
		$edtCmid =  & $form->getField("customer_id");
		$customer_id = $edtCmid ->getValue();
		
		$phoneOffice = & $form->getField("phone_work");
		$phoneCell = & $form->getField("phone_cell");
		
		$phoneOffice->setValue($_REQUEST["phone_office1"]);
		$phoneCell->setValue($_REQUEST["phone_other1"]);

		if (strlen($customer_id)>0)
		{
			$edit = true;
		}
		else
		{
			$edit =false;
			$customer_id = null;
		}
		$customers = & new  bllcustomers();
		
		if ($edit)
			$customer = $customers->getByPrimaryKey($customer_id);
		else
			$customer = $customers->add_new(); //& new customers();
		
		$customer->getDataFromForm($form);
		$customer->set_data("deleted", "0"); //This will save the temp. record added by note
		
		if( $customer->save($edit))
		{
			$this->customer_id =$customer->get_data("customer_id");
			
			if($edit)
				$this->sendCCUpdateEmail($this->customer_id);
			
			return true; 
		}
		else
			return true;
	}
	
	function sendCCUpdateEmail($customer_id)
	{
		if($this->getCtlValue("lkup_payment_type_id")>2)
		{
			$this->sentEmail($customer_id);
		}
		else if($this->getCtlValue("old_lkup_payment_type_id")>2)
		{
			$this->sentEmail($customer_id);	
		}
	}
	
	function sentEmail($customer_id)
	{
	 	$cmBll = new bllcustomer();
	 	
	    $cc_number_old = str_replace(' ','',$this->getCtlValue("cc_number"));
	    $cc_number_new = str_replace(' ','',$this->getCtlValue("old_cc_number"));
	    
		if($this->getCtlValue("lkup_payment_type_id")!=$this->getCtlValue("old_lkup_payment_type_id")
			||$cc_number_old!=$cc_number_new||
			$this->getCtlValue("cc_exp_month")!=$this->getCtlValue("old_cc_exp_month")||
			$this->getCtlValue("cc_exp_year")!=	$this->getCtlValue("old_cc_exp_year"))
		{
			// account owner id
			$owner_id = $this->getCtlValue("user_id");
			
			//get estate manager list
			
			$arrayAddress = F60DbUtil::getAllEstatesManagerEmails();
			
			$toAddress ="";
			$to_id = "";
			if(count($arrayAddress)!=0)
			{
				foreach ( $arrayAddress as $key=>$emailAddress)
				{

					$toAddress =$toAddress.$emailAddress['email1'].";";
					
					//get estate manager user_id 
					$to_id =$to_id.$emailAddress['user_id']."|";
				}
			}
					
			//check if the account owner is the estate manager
			if($owner_id >0)
			{
				if(!strstr($to_id,$owner_id)) // $account owner is not the estate manager
				{
					//get accoun onwer email address
					$ownerAddress = $cmBll->getUserEmailAddress($owner_id);
					$toAddress .= ";".$ownerAddress;
				}
			}
			else
			{
				$owner_id ="0";
			}
						
			// office BCC list and Estate BCC list
			$bccAddress = $this->getConfigVal("BCC_EMAIL_RECEPIENTS").$this->getConfigVal("BCC_ESTATE_EMAIL_RECEPIENTS");
								
			// Email from address
			$fromAddress=$this->getConfigVal("EMAIL_FROM_ADDRESS");
			
			//Email subject
			$emailSubject = $this->getConfigVal("EMAIL_CC_UPDATE_SUBJECT");
			
			$currentDate = date("F d, Y");

			$address=$this->getCtlValue("billing_address_street_number")." ".$this->getCtlValue("billing_address_street")." ".$this->getCtlValue("billing_address_city");
			
			
			//update type
			$update_type="updated";
			$refer_text="Please refer to CSOnline for the updated credit card.";
									
			if($this->getCtlValue("lkup_payment_type_id")>2 && $this->getCtlValue("old_lkup_payment_type_id")<=2) // add new credit card
			{
				$update_type="added";
			}
			else if($this->getCtlValue("lkup_payment_type_id")<=2&&$this->getCtlValue("old_lkup_payment_type_id")>2) // delete credit card
			{
				$update_type="deleted";
				$refer_text ="";
			}
			
			$emailContent = $this->getConfigVal("CC_UPDATE_EMAIL_CONTENT");		
			$emailContent = $this->replaceToken("update_type",$update_type,$emailContent);
			$emailContent = $this->replaceToken("refer_text",$refer_text,$emailContent);
			$emailContent = $this->replaceToken("customer_name",$this->getCtlValue("customer_name"),$emailContent);
			$emailContent = $this->replaceToken("current_date",$currentDate,$emailContent);
			$emailContent = $this->replaceToken("store_type",$cmBll->getStoreType($this->getCtlValue("lkup_store_type_id")),$emailContent);
			$emailContent = $this->replaceToken("licensee_number",$this->getCtlValue("licensee_number"),$emailContent);
			$emailContent = $this->replaceToken("address",$address,$emailContent);
						
			// save to note			
			$today =Date("Y-m-d");
			$note_text="The credit card has been updated";
			$login_user_id = F60DALBase::get_current_user_id();
 
			$retVal = $cmBll->insertCCInfoToNote($today,$login_user_id,$note_text,$this->customer_id,$update_type);

			if($retVal) //send email
			{
				$from_name = $this->getConfigVal("EMAIL_FROM_CSWS_TITLE");
			
			//	echo "to: ".$toAddress." bcc: ".$bccAddress." from: ".$fromAddress;
		
		       F60Common::_sendEmail($toAddress,$bccAddress,$fromAddress,$emailSubject,$emailContent,$from_name);	
			}
		}	
	}

	function replaceToken( $token, $replaceVal,$regStr)
	{
	 	$sToken ="[$token]";
		return str_replace($sToken, $replaceVal,$regStr);
	}
	
	function _sendEmail($to, $bcc, $from, $subject, $body)
    {    
        $mail = new MailMessage(); 

        $mail->setSubject($subject); 
        $mail->setFrom($from, "Christopher Stewart Wine & Spirits"); 
        
		// to	
     	$arrayTo = split(";",$to);     
     	for($i=0; $i<count($arrayTo);$i++)
     	{
    	 	$emailTo = $arrayTo[$i];
    	 	if($emailTo!="")
			    $mail->addTo($emailTo);
    	}
       
	   // $bcc
	    $arrayBcc = split(";",$bcc);
     	
     	for($i=0; $i<count($arrayBcc);$i++)
     	{
    	 	$emailBcc = $arrayBcc[$i];
    	 	if($emailBcc!="")
			    $mail->addBcc($emailBcc);
    	}    	
        $mail->setHtmlBody($body);
        $mail->build(); 
        
        $transport =& $mail->getTransport(); 
        $transport->setType(MAIL_TRANSPORT_MAIL);

		$transport->send();				
    }
    
    function saveCustomer()
	{
		$this->processForm();
		
		$sURL ='main.php?page_name=F60SearchResult&search_id=1&pageid=18&customer_id='.$this->customer_id;
		
		HtmlUtils::redirect($sURL);
		
		return true;
   	}
   
	function deleteData()
	{
		$form = & $this->getForm();
		
		$customerid =  & $form->getField("customer_id");
		$customer_id = $customerid->getValue();
		$customers = & new  bllcustomers();
		$customer = $customers->getByPrimaryKey($customer_id);		
		$customer->getDataFromForm($form);
		
		return $customer->delete($customer_id);
	}
        
}

?>
