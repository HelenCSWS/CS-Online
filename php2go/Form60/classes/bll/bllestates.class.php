<?php

import('Form60.dal.dalestates');
import('Form60.bll.bllcontacts');
import('Form60.dal.dalestates_commissions');
import('Form60.dal.dalestates_contacts');
import('Form60.dal.dalwine_delivery_dates');
import('Form60.dal.dalwines');
import('php2go.util.TypeUtils');
import('Form60.base.F60DbUtil');

define(COMMISSION_TYPE_COUNT, 5);

class bllestate extends dalestates
{
    var $primary_contact = null;
    var $secondary_contact = null;
    var $commissions;
    var $delivery_dates;
    var $estates_contacts_1 = null;
    var $estates_contacts_2 = null;
    var $estates_contacts = null;
    var $is_international =false;
    
    function bllestate()
    {
        parent::dalestates();
    }
    
    function setInternational($is_international)
    {
        $this->is_international =$is_international;
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
        if (parent::loadByPrimaryKey($keyValues))
        {
            //get the contacts
            $this->estates_contacts = & new dalestates_contactsCollection();
            $this->estates_contacts ->add_filter("estate_id", "=", $this->get_data("estate_id"));
            $this->estates_contacts ->add_filter("order by is_primary");
            if ($this->estates_contacts ->load())
            {
                foreach($this->estates_contacts ->items as $estate_contact)
                {
                    $contacts = & new bllcontacts();
                    if ($estate_contact->get_data("is_primary") == 1)
                    {
                        $this->primary_contact = $contacts->getByPrimaryKey($estate_contact->get_data("contact_id"));
                    }
                    else        //assuming only 2 contacts per estate
                    {
                        $this->secondary_contact = $contacts->getByPrimaryKey($estate_contact->get_data("contact_id"));
                    }
                }
            }

            //get the commissions
          
            if($_REQUEST["is_international"]==0)
            {
                $this->commissions = & new dalestates_commissionsCollection();
                $this->commissions->add_filter("estate_id", "=", $this->get_data("estate_id"));
                $this->commissions->add_filter(" order by lkup_commission_types_id");
                $this->commissions->load();

                //get wine_delivery_dates
                $this->delivery_dates = & new dalwine_delivery_datesCollection();

                $this->delivery_dates->table_name="wine_delivery_dates,estates e,wines w";

                $this->delivery_dates->add_filter("e.estate_id", "=", $this->get_data("estate_id"));
                $this->delivery_dates->add_filter("and e.estate_id = w.estate_id and w.wine_id= wine_delivery_dates.wine_id");
                $this->delivery_dates->add_filter("and w.deleted=0 and wine_delivery_dates.deleted = 0 ");
                $this->delivery_dates->add_filter(" order by wine_name");
                $this->delivery_dates->load();
            }

            return true;
        }

        return false;
    }


    function getFromDAL($dal)
    {
        if (TypeUtils::isObject($dal) && TypeUtils::isInstanceOf($dal, "dalestates"))
            return $this->loadByPrimaryKey($dal->get_data("estate_id")); //extra DB trip here
        else
            return false;
    }

    function getNextInvoiceNumber()
    {
        $invoiceNo = $this->get_data("invoice_prefix") .
                        $this->get_data("next_invoice_no");
        $this->set_data('next_invoice_no', $this->get_data('next_invoice_no') + 1);
        $this->save();
        return $invoiceNo;
    }

    function save($form = Null,$edit=Null)
    {
       
        $phone = preg_replace('/\D/', '', $this->get_data("phone_office1"));
        $this->set_data("phone_office1", $phone);
        
        $phone = preg_replace('/\D/', '', $this->get_data("phone_other1"));
        $this->set_data("phone_other1", $phone);

        $phone = preg_replace('/\D/', '', $this->get_data("phone_fax"));
        $this->set_data("phone_fax", $phone);


        if (parent::save())
        {
            $retVal = true;
            if ($form)
            {
                $field= & $form->getField("estate_id");
                $field->setValue($this->get_data("estate_id"));
            }
            $estate_id =$this->get_data("estate_id");

            if (TypeUtils::isObject($this->primary_contact))
            {
                $retVal = $this->primary_contact->save();

                    if ($retVal )
                    {

                        $estate_contact_id = & $_REQUEST["estates_contacts_id_1"];
                        
                        $estate_contact = & new dalestates_contacts();
                       if ($estate_contact_id!="0")
                       {
                            $estate_contact->is_new = false;
                            $estate_contact->set_data("estates_contacts_id",  $estate_contact_id);
                       }
                            
                        $estate_contact->set_data("estate_id",  $this->get_data("estate_id"));
                        $estate_contact->set_data("contact_id",  $this->primary_contact->get_data("contact_id"));
                        $estate_contact->set_data("is_primary",  1);                 
								$retVal = $estate_contact->save();
                    }
            }

            if ($retVal && TypeUtils::isObject($this->secondary_contact))
            {
            
                $retVal = $this->secondary_contact->save();

                if ($retVal  )
                {
                    $estate_contact = & new dalestates_contacts();

                     $estate_contact_id = & $_REQUEST["estates_contacts_id_2"];

                        $estate_contact = & new dalestates_contacts();
                     if ($estate_contact_id!="0")
                     {
                            $estate_contact->is_new = false;

                            $estate_contact->set_data("estates_contacts_id",  $estate_contact_id);
                    }

                    $estate_contact->set_data("estate_id",  $this->get_data("estate_id"));
                    $estate_contact->set_data("contact_id",  $this->secondary_contact->get_data("contact_id"));
                    $estate_contact->set_data("is_primary", 3);
                    $retVal = $estate_contact->save();
                }
            }

			
            if(!$this->is_international)
            {
                  if ($retVal && TypeUtils::isObject($this->commissions) && $form)
                    {
                        $est_comm_id =  & $form->getField("estate_commission_id_1");
                        $is_new = true;
                        if($est_comm_id->getValue()!=0)
                            $is_new=false;

                        foreach($this->commissions->items as $commission)
                        {
                            $commission->set_data("estate_id", $this->get_data("estate_id"));
                            $commission -> is_new =$is_new;
                            $retVal = $commission->save();
                        }
                    }
            }

            if ($form)
            {
                $edt_is_addwine= & $form->getField("is_addwine");
                $isAdd = $edt_is_addwine->getValue();
                if ($isAdd ==1)
                {
                    $sURL ="main.php?page_name=wineAdd&estate_id=".$estate_id."&pageid=24";
                    if($this->is_international)
                        $sURL ="main.php?page_name=wineAdd&editMode=0&is_international=1&estate_id=".$estate_id;
                    HtmlUtils::redirect($sURL);
                }
            }

            return $retVal;
        }
        
    }

/*delete estate
delete wines
delete allocations
delete orders -pending

*/
    function delete($is_international)
    {
        $this->is_deleted = true;
        $retVal = true;
        $estate_id=$this->get_data("estate_id");

       //delete wine
      //get wine_id
      
        $wines = new dalwinesCollection();
        //$wines ->add_filter()
        $wines->add_filter("estate_id", "=", $estate_id);
         if ($wines->load())
        {
            foreach($wines->items as $wine)
            {
              if(!$is_international)
              {
                    $delivery_dates = & new dalwine_delivery_datesCollection();
                    $delivery_dates->add_filter("wine_id", "=", $wine->get_data("wine_id"));
                    if ($delivery_dates->load())
                    {
                        foreach($delivery_dates->items as $wine_delivery)
                        {
                            $wine_delivery->is_deleted=true;
                            $wine_delivery->is_new=false;
                            $retVal = $wine_delivery->save();
                        }
                    }

                    if ($retVal)
                    {
                        //delete wine allocation and order_itmes
                      $retVal = & F60DbUtil::deleteAllocations($wine->get_data("wine_id"));
                      $retVal = & F60DbUtil::deleterOrderItemsbyWineid($wine->get_data("wine_id"));

                       $wine->is_deleted=true;
                        $retVal = $wine->save();

                    }
                }
                else
                {
                       $wine->is_deleted=true;
                       $retVal = $wine->save();
                       
                       if($is_international )
                       {
                            $retVal=true;
                       }
                }
                
                
              
            }
        }


         if ($retVal &&TypeUtils::isObject($this->estates_contacts_1))
         {

            $this->estates_contacts_1->is_deleted=true;
            $this->estates_contacts_1->is_new=false;
            $retVal = $this->estates_contacts_1->save();

         }
        //delete first contact
        if ($retVal &&TypeUtils::isObject($this->primary_contact))
        {
            //delete  estate_contact 1
            $this->primary_contact->is_deleted=true;
            $this->primary_contact->is_new=false;
            $retVal = $this->primary_contact->save();
        }

         if ($retVal &&TypeUtils::isObject($this->estates_contacts_2))
         {
            $this->estates_contacts_2->is_deleted=true;
            $this->estates_contacts_2->is_new=false;
            $retVal = $this->estates_contacts_2->save();

         }
                //delete secondary contact
        if ($retVal && TypeUtils::isObject($this->secondary_contact))
        {
            $this->secondary_contact->is_deleted=true;
            $this->secondary_contact->is_new=false;
            $retVal = $this->secondary_contact->save();
        }
        
        if(!is_international)
        {

            $i=1;
            if ($retVal && TypeUtils::isObject($this->commissions))
            {
                foreach ($this->commissions->items as $commission)
                {
                   $commission->is_deleted=true;
                    $commission->is_new=false;
                    $retVal = $commission->save();
                    //$i++;
                }
            }//if ($retVal && TypeUtils::isObject($this->commissions))
        }


        //delete estate
        if ($retVal)
            $retVal =parent::save();
        return $retVal;
    }

	 function getRatesByStoreType($estate_id,$store_type_id)
	 {
		 $sql="select * from estates_commissions where estate_id = $estate_id and lkup_commission_types_id = $store_type_id ";
		 $result = &F60DbUtil :: runSQL($sql);

      if(!$result->EOF)
      {
           $row = & $result->FetchRow();
           return $row;
     }
     else
     		return false;
		 
	}
	
	function getRatesByStoreTypeForAL($estate_id,$store_type_id) // for import old arrow leaf data, same as hillside's rates'
	 {
		 $sql="select * from estates_commissions_al where lkup_commission_types_id = $store_type_id ";
		 $result = &F60DbUtil :: runSQL($sql);

      if(!$result->EOF)
      {
           $row = & $result->FetchRow();
           return $row;
     }
     else
     		return false;
		 
	}
	
	
    function getDataFromForm(&$form,$edit)
    {

        parent::getDataFromForm($form);
        
        
        if (!TypeUtils::isObject($this->primary_contact))
            $this->primary_contact = & new dalcontacts();
        $this->primary_contact->getDataFromForm(&$form);

        $edt_cms_cns_id_1=& $form->getField("estates_contacts_id_1");
        if ($edt_cms_cns_id_1->getValue()!="0")
        {

            $this->estates_contacts_1 = & new dalestates_contacts();
            $this->estates_contacts_1 ->set_data("estates_contacts_id",$edt_cms_cns_id_1->getValue());
        }


        $field = & $form->getField("secondary_first_name");

        if ((strlen($field->getValue()) > 0 && $edit == false) ||$edit == true)
        {

            if (!TypeUtils::isObject($this->secondary_contact))
                $this->secondary_contact = & new dalcontacts();
            $this->secondary_contact->set_data("first_name", $field->getValue());
           
            $field = & $form->getField("secondary_last_name");
            $this->secondary_contact->set_data("last_name", $field->getValue());

            $edt_cms_cns_id_2=& $form->getField("estates_contacts_id_2");
            if ($edt_cms_cns_id_2->getValue()!="0")
            {
              
                $this->estates_contacts_2 = & new dalestates_contacts();
                $this->estates_contacts_2 ->set_data("estates_contacts_id",$edt_cms_cns_id_2->getValue());
            }

        }

        if(!$this->is_international)
        {
            if (!TypeUtils::isObject($this->commissions))
            {
                $this->commissions = & new dalestates_commissionsCollection();
            }

            for ($i = 0; $i <= COMMISSION_TYPE_COUNT - 1; $i++)
            {

                //get the commission fields
                $fieldname ="ctype_". ($i + 1);
                $field =& $form->getField($fieldname);
                $commissionVal =str_replace("$","",$field->getValue());

                $edtCntName ="cntype_". ($i + 1);
                $edtCnt=& $form->getField($edtCntName);

                $edt_estete_comm_id_name = "estate_commission_id_".($i + 1);
                $edt_estete_comm_id=& $form->getField($edt_estete_comm_id_name);

                if (array_key_exists(i, $this->commissions->items))
                    $commission = & $this->commissions->items[i];
                else
                    $commission = & $this->commissions->add_new();
                $commission->set_data("commission", $commissionVal);
                $commission->set_data("lkup_commission_types_id", $this->getCommissionID($i,true));
                $commission->set_data("commission_number_type", $edtCnt->getValue());

                if ($edt_estete_comm_id->getValue()!=0)
                {
                  $commission->set_data("estates_commissions_id", $edt_estete_comm_id->getValue());
                }
            }//for ($i = 0; $i <= COMMISSION_TYPE_COUNT - 1; $i++)
            
        }//if(!$this->is_international)
   }

    function getCommissionID($i, $isStoreType)
    {
      if ($isStoreType)
      {
            switch($i)
    		{
        	   case 0: //licensee 
                    return  3;
        		case 1://agency
                    return 2;
        		case 2: //LRS
                    return 1;
        		case 3: //bulk
                    return 4;
        		case 4: //VQA
                    return 5;
    		}
        }
        else
        {
            switch($i)
		    {
        		case 3: //licensee
                    return 1;
        		case 2://agency
                    return 2;
        		case 1: //LRS
                    return 3;
        		case 4: //bulk
                   return 4;
        		case 5: //VQA
                   return 5;
		   }
        }
        
    }

    function loadDataToForm(&$form,$is_international)
    {
        parent::loadDataToForm($form);
        
         $field = & $form->getField("phone_office1");
         $field->setValue($this->format_phone($this->get_data("phone_office1")));
         
         $field = & $form->getField("phone_other1");
         $field->setValue($this->format_phone($this->get_data("phone_other1")));

         $field = & $form->getField("phone_fax");
         $field->setValue($this->format_phone($this->get_data("phone_fax")));


         $field = & $form->getField("lkup_phone_type_id");
         $field->setValue($this->get_data("lkup_phone_type_id"));
         
        if (TypeUtils::isObject($this->primary_contact))
        {
            $this->primary_contact->loadDataToForm($form);
           
        }
        if (TypeUtils::isObject($this->secondary_contact))
        {
          
            $fieldName = "secondary_first_name";
            $field = & $form->getField($fieldName);
            $field->setValue($this->secondary_contact->get_data("first_name"));
            $fieldName = "secondary_last_name";
            $field = & $form->getField($fieldName);
            $field->setValue($this->secondary_contact->get_data("last_name"));
        }

       $i=1;
        foreach($this->estates_contacts->items as $estate_contact)
        {
            $edtName ="estates_contacts_id_".$i;
            $edt_cms_cns_id = & $form->getField($edtName);
            if (is_object($edt_cms_cns_id)) 
                $edt_cms_cns_id->setValue($estate_contact->get_data("estates_contacts_id"));
            $i++;
        }
       //get commission data here
       
       if (!$is_international)
       {
                $i=0;
                foreach ($this->commissions->items as $commission)
                {
                    $fieldName = "ctype_".$this->getCommissionID($commission->get_data("lkup_commission_types_id"),false);
                     $field = & $form->getField($fieldName);
                    $field->setValue($commission->get_data("commission"));


                     $edtCntName ="cntype_". $this->getCommissionID($commission->get_data("lkup_commission_types_id"),false);

                    $edtCnt=& $form->getField($edtCntName);
                    $edtCnt->setValue($commission->get_data("commission_number_type"));
                   $edt_estete_comm_id_name = "estate_commission_id_".$this->getCommissionID($commission->get_data("lkup_commission_types_id"),false);
                    $edt_estete_comm_id=& $form->getField($edt_estete_comm_id_name);
                    $edt_estete_comm_id->setValue($commission->get_data("estates_commissions_id"));
                   $i++;
              }

                //get delivery_dates
              /*  $i =0;
                foreach ($this->delivery_dates->items as $delivery_date)
                {
                    $fieldName ="wine_delivery_date";
                    $field = & $form->getField($fieldName);
                    $val =$delivery_date->get_data("wine_id");
                    $caption=$delivery_date->get_data("wine_name")."  ".$delivery_date->get_data("vintage")."  ".$delivery_date->get_data("delivery_date");
                    if ($i==0 )
                    {
                        $firstVal = $val;
                    }
                    $field->addOption($val,$caption,$i);

                    $i++;


               }
               if ($i>0)
               {  $i =0;
                  $field->removeOption($i);
                  $field->setValue($firstVal);
               }*/
        }

   }//end fucntion

}

class bllestates extends dalestatesCollection
{
    var $estates_commisstions = null;
    var $wine_delivery_dates = null;
	function bllestates()
	{
		parent::dalestatesCollection();
	   $this->estates_commisstions = & new dalestates_commissionsCollection();
	   $this->wine_delivery_dates =& new dalwine_delivery_dates();
	}

    function estatenameExists($name, $estate_id = NULL)
    {
        $estates = & new dalestatesCollection();
        $estates->add_filter("estate_name", "=", $name);


        if (isset($estate_id))
        {
            $estates->add_filter("AND");
            $estates->add_filter("estate_id", "<>", $estate_id);
            $estates->add_filter("AND");
            $estates->add_filter("deleted", "=", "0");

         }
        $estates->load();

         return ($estates->get_count() != 0);
    }

    function estatenumberExists($number, $estate_id = NULL)
    {
        $estates = & new dalestatesCollection();
        $estates->add_filter("estate_number", "=", $number);


        if (isset($estate_id))
        {
         	if($estate_id ==1 ||$estate_id==126) //Parardise Ranch and Soaring Eagle are Merge to one estate and using the same estate number  Aug 16th, 2012
         		return false;
         		
            $estates->add_filter("AND");
            $estates->add_filter(" estate_id", "<>", $estate_id);

         }
            $estates->add_filter(" AND");
         $estates->add_filter(" deleted", "=", "0");
        $estates->load();

         return ($estates->get_count() != 0);
    }

    function &getByPrimaryKey($keyValues)
    {
        $dal = parent::getByPrimaryKey($keyValues);
        if ($dal)
        {
            $bll = & new bllestate();
            if ($bll->getFromDAL($dal))
                return $bll;
        }
        return nulll;
    }

    function add_new()
    {
        //override collection add_new, return bll class
        $bll = & new bllestate();
        return $bll;
    }
}

?>
