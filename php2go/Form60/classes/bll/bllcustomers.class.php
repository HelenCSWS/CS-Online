<?php

import('Form60.dal.dalcustomers');
import('Form60.dal.dalcontacts');
import('Form60.dal.dalcustomers_contacts');
import('Form60.dal.dalcustomers_users');
import('php2go.util.TypeUtils');
import('Form60.base.F60DbUtil');
/*import('Form60.util.F60common');*/

define(COMMISSION_TYPE_COUNT, 5);

class bllcustomer extends dalcustomers
{
    var $primary_contact = null;
    var $secondary_contact = null;
    var $customers_users=null;
    var $customers_contacts = null;
    var $customers_contacts_1 = null;
    var $customers_contacts_2 = null;
    var $isContact = array(false,false);

    var $customer_id = "";
    var $db;
    
    function bllcustomer()
    {
        parent::dalcustomers();
        
          $this->db = &Db::getInstance();
        $this->db->setFetchMode(ADODB_FETCH_ASSOC);
    }
    
    function format_phone($phone)
    {
        if (strlen($phone)==10)
            return preg_replace("/([0-9]{3})([0-9]{3})([0-9]{4})/", "$1.$2.$3", $phone);
        else if((strlen($phone)==11))
            return preg_replace("/([0-9]{1})([0-9]{3})([0-9]{3})([0-9]{4})/", "$1.$2.$3.$4", $phone);
       else if((strlen($phone)==7))
            return preg_replace("/([0-7]{3})([0-7]{4})/", "$1.$2", $phone);
       else
            return $phone;

    }

    function loadByPrimaryKey($keyValues)
    {
       // $this->add_filter("order by is_pri");
        if (parent::loadByPrimaryKey($keyValues))
        {
            //get the contacts
            $this->customers_contacts = & new dalcustomers_contactsCollection();
            $this->customers_contacts->add_filter("customer_id", "=", $this->get_data("customer_id"));
            $this->customers_contacts->add_filter("order by is_primary");
            
            if ($this->customers_contacts->load())
            {
                foreach($this->customers_contacts->items as $customer_contact)
                {
                    $contacts = & new bllcontacts();
                    if ($customer_contact->get_data("is_primary") == 1)
                    {
                         $isContact[1] = true;
                         $this->primary_contact = $contacts->getByPrimaryKey($customer_contact->get_data("contact_id"));
                    }
                    else        //assuming only 2 contacts per customer
                    {
                        $isContact[2] = true;
                        $this->secondary_contact = $contacts->getByPrimaryKey($customer_contact->get_data("contact_id"));
                    }
                }
            }

            $this->customers_users = & new dalcustomers_users();
            $this->customers_users->add_filter("customer_id", "=", $this->get_data("customer_id"));
            
            $this->customers_users->load();
            return true;
        }

        return false;
    }

    function getFromDAL($dal)
    {
        if (TypeUtils::isObject($dal) && TypeUtils::isInstanceOf($dal, "dalcustomers"))
            return $this->loadByPrimaryKey($dal->get_data("customer_id")); //extra DB trip here
        else
            return false;
    }
    function getCustomerId()
    {
        return $this->get_data("customer_id");
    }
    function getAddress()
    {
	    $address = '';
        $address .= (TypeUtils::isNull($this->get_data("po_box")))?'':'PO Box: '. $this->get_data("po_box") . ', ';
        $address .= (TypeUtils::isNull($this->get_data("billing_address_unit")))?'':$this->get_data("billing_address_unit") . '-';
        $address .= TypeUtils::ifNull($this->get_data("billing_address_street_number"), '') . ' ';
        $address .= TypeUtils::ifNull($this->get_data("billing_address_street"), '') . ', ';
        $address .= TypeUtils::ifNull($this->get_data("billing_address_city"), '') . ' ';
        $address .=  TypeUtils::ifNull($this->get_data("billing_address_state"), '');
        $address .= TypeUtils::ifNull($this->get_data("billing_address_postalcode"), '');
        
        return $address;
    }
    
    function getUserEmailAddress($user_id)
    {
		$db = & Db::getInstance();
        $db->setFetchMode(ADODB_FETCH_ASSOC);
        
        $sql= "Select email1 from users where user_id =$user_id";
        $rows = $this->db->getAll($sql);
        
		return $rows[0]["email1"];	           
	}
	
	function getStoreType($type_id)
    {
		$db = & Db::getInstance();
        $db->setFetchMode(ADODB_FETCH_ASSOC);
        
        $sql= "Select license_name from lkup_store_types where lkup_store_type_id =$type_id";
        $rows = $this->db->getAll($sql);
		return $rows[0]["license_name"];	    
        
	}
    
    function save($edit)
    {
        $objFomm = new F60Common();

        $phone = preg_replace('/\D/', '', $this->get_data("phone_office1"));
        $this->set_data("phone_office1", $phone);

        $phone = preg_replace('/\D/', '', $this->get_data("phone_other1"));
        $this->set_data("phone_other1", $phone);

        $phone = preg_replace('/\D/', '', $this->get_data("phone_fax"));
        $this->set_data("phone_fax", $phone);

        $province_id = $_COOKIE["F60_PROVINCE_ID"];
        $this->set_data("province_id", $province_id);
        
        
        $cc_card_number = $this->get_data("cc_number");
        $dig_code = $this->get_data("cc_digit_code");
        if($cc_card_number!="")
        {
            $cc_card_number = $objFomm->enCodeString(($cc_card_number));
            $dig_code = $objFomm->enCodeString(($dig_code));
        }
        $this->set_data("cc_number",$cc_card_number);
        $this->set_data("cc_digit_code",$dig_code);
                    
        if (parent::save())
        {
                $retVal = true;
                if (TypeUtils::isObject($this->primary_contact))
                {
                    $retVal = $this->primary_contact->save();
                    if ($retVal )
                    {
                        $customer_contact_id = & $_REQUEST["customers_contacts_id_1"];

                        $customer_contact = & new dalcustomers_contacts();
                        if ($customer_contact_id!="0")
                        {
                            $customer_contact->is_new = false;
                            $customer_contact->set_data("customers_contacts_id",  $customer_contact_id);
                        }
                        
                        $customer_contact->set_data("customer_id",  $this->get_data("customer_id"));
                        $customer_contact->set_data("contact_id",  $this->primary_contact->get_data("contact_id"));
                        $customer_contact->set_data("is_primary",  1);
                        $retVal = $customer_contact->save();
                    }
                }
                
                if ($retVal && TypeUtils::isObject($this->secondary_contact))
                {
                    $retVal = $this->secondary_contact->save();
                    if ($retVal)
                    {


                        $customer_contact_id = & $_REQUEST["customers_contacts_id_2"];

                        $customer_contact = & new dalcustomers_contacts();
                       if ($customer_contact_id!="0")
                       {
                            $customer_contact->is_new = false;
                            $customer_contact->set_data("customers_contacts_id",  $customer_contact_id);
                       }

                        $customer_contact->set_data("customer_id",  $this->get_data("customer_id"));
                        $customer_contact->set_data("contact_id",  $this->secondary_contact->get_data("contact_id"));
                        $customer_contact->set_data("is_primary",  3);
                        $retVal = $customer_contact->save();
                        
                    
                    }
                }
            if ($retVal && TypeUtils::isObject($this->customers_users))
            { 
                if($_REQUEST["assign_user_id"]=="" ||$_REQUEST["assign_user_id"]==0 )
                {
                	
                		$this->customers_users->is_deleted=true;
                		$this->customers_users->is_new =false;
                	}
                else
                {
                
                		 $this->customers_users->set_data("customer_id", $this->get_data("customer_id"));
                    
                   if (!$this->customers_users->get_data('users_customers_id'))
                         $this->customers_users->is_new=true;
                         
                }
                $retVal = $this->customers_users->save();
                }
                 return retVal;//$retVal;
            }
    }

    function delete($customer_id)
    {
        $this->is_deleted = true;
        $retVal = true;

        //delete customers_users
        if ( TypeUtils::isObject($this->customers_users))
        {
            if ($this->customers_users!=NULL)
            {
                $this->customers_users->is_deleted=true;
                $this->customers_users->is_new=false;
                $retVal = $this->customers_users->save();
            }
        }

         if ($retVal &&TypeUtils::isObject($this->customers_contacts_1))
         {
            $this->customers_contacts_1->is_deleted=true;
            $this->customers_contacts_1->is_new=false;
            $retVal = $this->customers_contacts_1->save();
         }
        //delete first contact
        if ($retVal &&TypeUtils::isObject($this->primary_contact))
        {
            //delete  customer_contact 1
            $this->primary_contact->is_deleted=true;
            $this->primary_contact->is_new=false;
            $retVal = $this->primary_contact->save();
        }

         if ($retVal &&TypeUtils::isObject($this->customers_contacts_2))
         {
            $this->customers_contacts_2->is_deleted=true;
            $this->customers_contacts_2->is_new=false;
            $retVal = $this->customers_contacts_2->save();

         }
                //delete secondary contact
        if ($retVal && TypeUtils::isObject($this->secondary_contact))
        {
            $this->secondary_contact->is_deleted=true;
            $this->secondary_contact->is_new=false;
            $retVal = $this->secondary_contact->save();
        }

        //delete customer
        if ($retVal)
        {
          
         $retVal =& F60DbUtil::caltWine4DelCm($customer_id);
            
        }
        if ($retVal)
        {
         

            $retVal =parent::save();
        }

//      return false;
        return $retVal;
    }
    
    function getDataFromForm(&$form)
    {
        parent::getDataFromForm($form);


        if (!TypeUtils::isObject($this->primary_contact))
        {
            $this->primary_contact = & new dalcontacts();

        }
        $edt_cms_cns_id_1=& $form->getField("customers_contacts_id_1");
        if ($edt_cms_cns_id_1->getValue()!="0")
        {

            $this->customers_contacts_1 = & new dalcustomers_contacts();
            $this->customers_contacts_1 ->set_data("customers_contacts_id",$edt_cms_cns_id_1->getValue());
        }

        $this->primary_contact->getDataFromForm($form);
        
        $field = & $form->getField("second_first_name");
        if (!TypeUtils::isObject($this->secondary_contact))
        {
            $this->secondary_contact = & new dalcontacts();
        
          
        }
        $edt_cms_cns_id_2=& $form->getField("customers_contacts_id_2");
        if ($edt_cms_cns_id_2->getValue()!="0")
        {
            $this->customers_contacts_2 = & new dalcustomers_contacts();
            $this->customers_contacts_2 ->set_data("customers_contacts_id",$edt_cms_cns_id_2->getValue());
        }
        $this->secondary_contact->set_data("first_name", $field->getValue());
        $field = & $form->getField("second_last_name");
        $this->secondary_contact->set_data("last_name", $field->getValue());
        
        
        $cmbUser = & $form->getField('user_id');
        
         if (!TypeUtils::isObject($this->customers_users))
                $this->customers_users = & new dalcustomers_users();
         $this->customers_users->getDataFromForm($form);

   }

	function insertCCInfoToNote($today,$login_user_id,$note_text,$customer_id)
	{
	 	$db = & Db::getInstance();
        $db->setFetchMode(ADODB_FETCH_ASSOC);

		$sqlInsertNote="Insert notes (when_created,when_modified,created_user_id,modified_user_id,note_text)
				values('$today','$today',$login_user_id,$login_user_id,'$note_text')";

		$retVal=$db->execute($sqlInsertNote);
		
		if($retVal)
		{
			$insertNoteId = $db->lastInsertId();

			$sqlInsertCustomerNote="insert customers_notes (note_id, customer_id) values($insertNoteId,$customer_id)";
			
			$retVal =$this->db->execute($sqlInsertCustomerNote);
		}
		
		
		return $retVal;

	}
    function loadDataToForm(&$form)
    {
        parent::loadDataToForm($form);
        
          $field = & $form->getField("phone_office1");
         $field->setValue($this->format_phone($this->get_data("phone_office1")));

         $field = & $form->getField("phone_other1");
         $field->setValue($this->format_phone($this->get_data("phone_other1")));

       $field = & $form->getField("phone_fax");
         $field->setValue($this->format_phone($this->get_data("phone_fax")));

        
      //  print & parent::get_date("phone_fax");
              $field = & $form->getField("lkup_phone_type_id");
            $field->setValue($this->get_data("lkup_phone_type_id"));

      	if (TypeUtils::isObject($this->primary_contact))
        {
            $this->primary_contact->loadDataToForm($form);
            //set the best numberber to call
 
            $cmbAssignto = & $form->getField("user_id");
            $cmbAssignto ->setValue(parent::get_data("user_id"));

            $phoneOf =& $form->getField("phone_office1");
            
            if ($phoneOf->getValue()=="" or $phoneOf->getValue()==null)
            {

                    $phoneOf ->setValue($this->primary_contact->get_data("phone_work"));
            }

            $phoneCell =& $form->getField("phone_other1");
            if ($phoneCell->getValue()=="" or $phoneCell->getValue()==null)
                    $phoneCell ->setValue($this->primary_contact->get_data("phone_cell"));
                    
      	}

       $i=1;
        foreach($this->customers_contacts->items as $customer_contact)
        {
            if ($i<=2)
            {
                $edtName ="customers_contacts_id_".$i;
             //   print $edtName;
                $edt_cms_cns_id = & $form->getField($edtName);
                $edt_cms_cns_id ->setValue($customer_contact->get_data("customers_contacts_id"));
                $i++;
            }
        }

      if (TypeUtils::isObject($this->secondary_contact))
        {
            $fieldName = "second_first_name";
            $field = & $form->getField($fieldName);
            $field->setValue($this->secondary_contact->get_data("first_name"));
            $fieldName = "second_last_name";
            $field = & $form->getField($fieldName);
            $field->setValue($this->secondary_contact->get_data("last_name"));
        }
        //user & priority
                  

        if (TypeUtils::isObject($this->customers_users))
        {
       
            if( $this->customers_users->get_data("user_id")!=null)
            {
                
                $cmbAssignedUser = & $form->getField("assign_user_id");
            	$cmbAssignedUser->setValue($this->customers_users->get_data("user_id"));
             
                $this->customers_users->loadDataToForm($form);
            }
            else
            {
            
					$cmbUser = & $form->getField("user_id");
            	$cmbUser->setValue("0");
            }
        }

   }
   
   //HK ranks
   function saveHKRanks($customer_id, $subTypeIDs)
   {
        //clear previous ranls
    $SQL="delete from hk_customers_ranks where customer_id =$customer_id";
	$bRet = $this->db->execute($SQL);
      
      if($subTypeIDs!=0)
      {
        foreach ($subTypeID as $subTypeIDs) 
        {
            $year=2019;
            if($subTypeID==3) //tatler 2018
            {
                $year=2018;
            }
            $SQL="insert hk_customers_ranks(customer_id, hk_rank_type_id, rank_year)                       
                                    value($customer_id,$subTypeID,$year)";
            $bRet = $this->db->execute($SQL);
        }
      }
  }
  
    function getHKRanks($customer_id)
    {
        $SQL="select * from hk_customers_ranks where customer_id =$customer_id";
        
        $rows = $this->db->getAll($SQL);
        
		return $rows;	 
    }

}
//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
class bllcustomers extends dalcustomersCollection
{
  	function bllcustomers()
	{
		parent::dalcustomersCollection();
	}

    function customernameExists($name, $customer_id = NULL)
    {
        $customers = & new dalcustomersCollection();
        $customers->add_filter("customer_name", "=", $name);

        if (isset($customer_id))
        {
            $customers->add_filter("AND");
            $customers->add_filter("customer_id", "<>", $customer_id);
   
         }
        $customers->add_filter("AND");
        $customers->add_filter("deleted", "=", "0");
        $customers->load();

        return ($customers->get_count() != 0);
    }


    function &getByPrimaryKey($keyValues)
    {
        $dal = parent::getByPrimaryKey($keyValues);
        if ($dal)
        {
            $bll = & new bllcustomer();
            if ($bll->getFromDAL($dal))
                return $bll;
        }
        return nulll;
    }

    function add_new()
    {
        //override collection add_new, return bll class
        $bll = & new bllcustomer();
        return $bll;
    }
}

?>
