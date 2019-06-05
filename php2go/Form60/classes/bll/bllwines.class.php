<?php

import('Form60.dal.dalwines');
import('Form60.dal.dalwines_info');
import('Form60.dal.dalwine_delivery_dates');
import('php2go.util.TypeUtils');
import('Form60.base.F60DbUtil');

class bllwine extends dalwines
{
    var $deliverydate = null;
     var $estate_wines= null;
    var $wine_deliveries= null;

    function bllwine()
    {
        parent::dalwines();
    }

    function loadByPrimaryKey($keyValues)
    {
        if (parent::loadByPrimaryKey($keyValues))
        {
            if($_REQUEST["is_international"]==0)
            {
                $this->deliverydate = & new dalwine_delivery_dates();
                $this->deliverydate->add_filter("wine_id", "=", $this->get_data("wine_id"));
                if ($this->deliverydate->load())
                {
                               return true;
                }
            }
            return true;
        }
        else
            return false;
    }

    function loadByPrimaryKey4Delivery($keyValues,$form)
    {
         $this->deliverydate = & new dalwine_delivery_dates();
          if ($keyValues!="")
             $this->deliverydate->add_filter("wine_delivery_date_id", "=", $keyValues);
         $this->deliverydate->load();
         return true;
    }

    function getFromDAL($dal)
    {
    
        if (TypeUtils::isObject($dal) && TypeUtils::isInstanceOf($dal, "dalwines"))
        {
            
            return $this->loadByPrimaryKey($dal->get_data("wine_id")); //extra DB trip here
        }
        else
        {
         
            return false;
        }
    }

    function deleteInternationalWine()
    {
       $this->is_deleted=true;
       $this->is_new=false;
//       $retVal =parent::save();
       return $retVal =parent::save();
       
       
    }

    function delete($is_del_delivery, $bts=NULL)
    {
        $retVal = false;
        
             if($is_del_delivery=="1")//delete delivery
            {
               $this->deliverydate->is_deleted=true;
               $this->deliverydate->is_new=false;
               $retVal = $this->deliverydate->save();

               if($retVal)
               {

	               $this->deliverydate->is_deleted=true;
	               $this->deliverydate->is_new=false;
	               $retVal = $this->deliverydate->save();
                   if($retVal)
                   {

                        $wine = new dalwines();
                        $wine ->add_filter("wine_id = ", $this->get_data("wine_id"));
                        $wine->load();
                        $modified_user_id =$wine->get_current_user_id();

                        $sql = "update wines set total_bottles = ";
                        $sql = $sql . $bts;
                        $sql = $sql . ",modified_user_id = " .$modified_user_id;
                        $sql = $sql . " where";
                        $sql = $sql . " wine_id = " .$this->get_data("wine_id");
                        $wine->excutiveSQL($sql);
                   }
                }
          }
            else//delete wine
            {
                 $delivery_dates = & new dalwine_delivery_datesCollection();
                $delivery_dates->add_filter("wine_id", "=", $this->get_data("wine_id"));
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
	                  //delete allcations
	                  $retVal = & F60DbUtil::deleteAllocations($this->get_data("wine_id"));
	                  $retVal = & F60DbUtil::deleterOrderItemsbyWineid($this->get_data("wine_id"));
                  
                    $this->is_deleted =true;
                    $retVal =parent::save();
                }
            }


            return $retVal;

    }
	function getValueFromCtl($ctlName,$form)
	{
		//print $ctlName;
		$ctl = & $form->getField($ctlName);
		return $ctl->getValue();
	}
		
    function save4otherProvince($form)
    {		
			parent::set_data("estate_id", $this->getValueFromCtl('estate_id',$form));
			
			parent::set_data("wine_name", $this->getValueFromCtl('wine_name',$form));
			parent::set_data("lkup_bottle_size_id", $this->getValueFromCtl('lkup_bottle_size_id',$form));
			parent::set_data("lkup_wine_color_type_id", $this->getValueFromCtl('lkup_wine_color_type_id',$form));
			parent::set_data("vintage", $this->getValueFromCtl('vintage',$form));
			parent::set_data("bottles_per_case", $this->getValueFromCtl('bottles_per_case',$form));
		
			if($this->getValueFromCtl('wineid',$form)!="")
			{
				$this->is_new =false;
				parent::set_data("wineid", $this->getValueFromCtl('wineid',$form));
			}
			else
				$this->is_new =true;
				
			parent::save();
			
			
			$wine_id = $this->get_data("wine_id");
			
			
			$wineInfos = & new dalwines_infoCollection();
			$wineInfo=$wineInfos->add_new();
			
			if($this->getValueFromCtl('wine_info_id',$form)!="")
			{
				$wineInfo->is_new =false;
				$wineInfo->set_data("wine_info_id", $this->getValueFromCtl('wine_info_id',$form));				
			}
			else
			{
				$wineInfo->is_new =true;
			}
			$wineInfo->is_new =true;  // should be command after all code are done!!!!!
			
			$wineInfo->set_data("wine_id", $wine_id);
			$wineInfo->set_data("province_id", $this->getValueFromCtl('province_id',$form));		
			$wineInfo->set_data("cspc_code", $this->getValueFromCtl('cspc_code',$form));
			$wineInfo->set_data("cost_per_unit", $this->getValueFromCtl('cost_per_unit',$form));
			$wineInfo->set_data("price_per_unit", $this->getValueFromCtl('price_per_unit',$form));
			$wineInfo->set_data("profit_per_unit", $this->getValueFromCtl('profit_per_unit',$form));
			$wineInfo->set_data("price_winery", $this->getValueFromCtl('price_winery',$form));
			$wineInfo->set_data("bottles_per_case", $this->getValueFromCtl('bottles_per_case',$form));
			$wineInfo->set_data("province_id", $this->getValueFromCtl('province_id',$form));
			
			return $wineInfo ->save();
		
		 	
	}
    function save($editMode,$is_international=0)
    {
    
                $retVal =false;
                
                if ($is_international)
                    $is_inter_value="1";
                else
                    $is_inter_value="0";
               // $is_inter_value=1; //delete
                switch ($editMode)
                {
                    case 0: //add wine
                    
                        parent::set_data("is_international", $is_inter_value);
                        $retVal = parent::save();
                       if ($retVal&&(!$is_international))
                        {
                              $this->deliverydate->set_data("wine_id",  $this->get_data("wine_id"));
                              $this->deliverydate->set_data("wine_delivery_date_id", "");
                                $this->deliverydate->is_new =true;
                                    $retVal = $this->deliverydate->save();

                        }
                        break;
                    case 1: //update wiine
                        if($is_international)
                            $this->is_new =false;
                        $retVal = parent::save();
                        break;
                    case 2: // add new delivery
                            parent::save();
                           if (TypeUtils::isObject($this->deliverydate))
                                {
                                   $wine_id =$_REQUEST["wineid"];
                                   if ($wine_id=="")
                                        $wine_id=$_REQUEST["wine_id"];
                                  
                                    $this->deliverydate->set_data("wine_id",  $_REQUEST["wineid"]);
                                    $this->deliverydate->set_data("wine_delivery_date_id", "");
                                    $this->deliverydate->is_new =true;
                                    $retVal = $this->deliverydate->save();
                                }


                            break;
                    case 3: //update delivery
                            parent::save();
                             if (TypeUtils::isObject($this->deliverydate))
                                {
                                    $this->deliverydate->set_data("wine_id",  $this->get_data("wine_id"));
                                    $this->deliverydate->is_new =false;
                                    $retVal = $this->deliverydate->save();
                                }
                           break;
                }
                       return $retVal;
    }

    function getDataFromForm(&$form ,$is_international)
    {
        parent::getDataFromForm($form);

        if(!$is_international)
        {
            if (!TypeUtils::isObject($this->deliverydate))
                 $this->deliverydate = & new dalwine_delivery_dates();
            $this->deliverydate->getDataFromForm(&$form);
        }
   }

    function loadDataToForm4Update(&$form,$wine_deliver_date_id)
    {
        $this->deliverydate = & new dalwine_delivery_dates();
        $this->deliverydate->add_filter("wine_delivery_date_id", "=", $wine_deliver_date_id);

       if ($this->deliverydate->load())
       {
            $this->deliverydate->loadDataToForm($form);
       }
 
    }
    
  
    function loadDataToForm(&$form,$editMode,$is_international)
    {



        parent::loadDataToForm($form);

        if(!$is_international)
        {
               if (TypeUtils::isObject($this->deliverydate))
                {
                    if ($editMode!=2)
                    {

                           $this->deliverydate->loadDataToForm($form);


                     $sql= "select total_bottles from wines where wine_id = " . $this->get_data("wine_id");
                       $result = & F60DbUtil::runSQL($sql);
                  	    $row = & $result->FetchRow();
                  	    $bottles = $row["total_bottles"];
                       $edtbottles = & $form->getField("show_total_bottles");
                       $edtbottles->setValue($bottles);
                     }
                      else
                    {

                           $edtWinename = & $form->getField("wine_name");
                            $edtWinename->setReadonly(true);
                            $edtCspc = & $form->getField("cspc_code");
                            $edtCspc->setReadonly(true);
                            $edtVintage = & $form->getField("vintage");
                            $edtVintage->setReadonly(true);
                            $edtPrice = & $form->getField("price");
                            $edtPrice ->setValue ($this->get_data("price_per_unit"));
                            $edtPrice->setReadonly(true);

                            $edtPrice = & $form->getField("wholesale");
                          //  print $this->get_data("price_winery");
                            $edtPrice ->setValue ($this->get_data("price_winery"));
                            $edtPrice->setReadonly(true);

                            $cmbSize = & $form->getField("lkup_bottle_size_id");
                            $cmbSize->setDisabled(true);
                            $cmbColor = & $form->getField("lkup_wine_color_type_id");
                            $cmbColor->setDisabled(true);

                            $edtbottles = & $form->getField("bottles_per_case");
                            $edtbottles->setReadonly(true);


                   }
              }
        }//if(!$is_international)
   }

}

class bllwines extends dalwinesCollection
{
    var $deliverydates = null;
	function bllwines()
	{
		parent::dalwinesCollection();
	   $this->deliverydates = & new dalwine_delivery_datesCollection();
	}

    function wineExists($name, $vintage, $estate_id,$wine_id = NULL)
    {
        $wines = & new dalwinesCollection();
        $wines->add_filter("wine_name", "=", $name);

        $wines->add_filter("AND");
        $wines->add_filter("vintage", "=", $vintage);
        $wines->add_filter("AND");
        $wines->add_filter("estate_id", "=", $estate_id);
          $wines->add_filter("AND");
         $wines->add_filter("deleted", "=", "0");

        if (isset($wine_id))
        {
            $wines->add_filter("AND");
            $wines->add_filter("wine_id", "<>", $wine_id);

         }
        $wines->load();

         return ($wines->get_count() != 0);
    }
    function cspcExists($cspc_code,$wine_id = NULL,$vintage,$wine_name)
    {
			$wines = & new dalwinesCollection();
			$wines->add_filter("cspc_code", "=", $cspc_code);
			
			$wines->add_filter("AND");
			$wines->add_filter("deleted", "=", "0");
			$wines->add_filter("and vintage", "=", $vintage);
			$wines->add_filter("and wine_name", "='", $wine_name."'");
			
        if (isset($wine_id))
        {

            $wines->add_filter("AND");
            $wines->add_filter("wine_id", "<>", $wine_id);

         }
        $wines->load();

         return ($wines->get_count() != 0);
    }

    function priceNotSame($wine_id = NULL,$price)
    {

        $wines=  & new dalwinesCollection();
        $wines->add_filter("price_per_unit", "=", $price);

        $wines->add_filter(" AND");
        $wines->add_filter("wine_id", "=", $wine_id);
        $wines->add_filter("AND");
        $wines->add_filter("deleted", "=", "0");
        $wines->load();
        return ($wines->get_count() == 0);
     
        
    }

   function deliverySame($name, $vintage, $estate_id,$wine_id ,$price,$delivery_date)
    {
        $wines = & new dalwinesCollection();
        $wines ->table_name = "wines w,wine_delivery_dates wd";
        $wines ->field_name = "w.wine_name";
        $wines->add_filter("w.wine_name", "='", $name);

        $wines->add_filter("' AND");
        $wines->add_filter("w.vintage", "=", $vintage);
        $wines->add_filter("AND");
        $wines->add_filter("w.estate_id", "=", $estate_id);
        $wines->add_filter("AND w.wine_id = wd.wine_id and ");
        $wines->add_filter("w.price_per_unit", "=", $price);
        $wines->add_filter("AND");
        $wines->add_filter("wd.delivery_date", "=", $delivery_date);
        $wines->add_filter("AND");
         $wines->add_filter("wd.deleted", "=", "0");

        if (isset($wine_id))
        {
            $wines->add_filter("AND");
            $wines->add_filter("w.wine_id", "=", $wine_id);

         }
        $wines->load();

         return ($wines->get_count() != 0);
    }

    function &getByPrimaryKey($keyValues)
    {
        $dal = parent::getByPrimaryKey($keyValues);
        if ($dal)
        {
         
            $bll = & new bllwine();
            if ($bll->getFromDAL($dal))
            {
                return $bll;
            }

        }
        return nulll;
    }

 

    function add_new()
    {
        //override collection add_new, return bll class
        $bll = & new bllwine();
        return $bll;
    }
}

function logMessage2($msg)
{
    $logFile = PHP2Go::getConfigVal('ERROR_LOG_FILE', FALSE);
    PHP2Go::logError($logFile, $msg, E_USER_NOTICE);
}
?>
