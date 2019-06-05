<?php
import('php2go.base.Php2Go');
import('Form60.dal.dalorderitems');
import('Form60.bll.bllwines');
import('Form60.dal.dal_alct_wines_customers');
import('Form60.base.F60DbUtil');


class bllorderitem extends dalorderitems
{  
    var $customer_id;
    var $estate_id;
    var $store_type_id;
    
    function bllorderitem()
    {
        $this->customer_id = NULL;
        parent::dalorderitems();
    }
        
    function getFromDAL($dal)
    {
        if (TypeUtils::isObject($dal) && TypeUtils::isInstanceOf($dal, "dalorderitems"))
            return $this->loadByPrimaryKey($dal->get_data("order_item_id")); //extra DB trip here
        else
            return false;
    }
function showtrace($content)
{
   	$fp = fopen("logs/ajax.log","a");
		fputs($fp, $content);
		fclose($fp);
}
	function setEstate($estate_id)
	{
		$this->estate_id = $estate_id;
	}
	
	
    function save()
    {
		
        $bNew = $this->is_new;
        $old_quantity =Intval($this->original_data["ordered_quantity"]);
      
        $old_quantity = (($bNew)?0:$old_quantity);
      
      
      	
      	
      	$bDirty = $this->is_dirty();
        $wine_id = $this->get_data("wine_id");
        
        
        $quantity = Intval($this->get_data("ordered_quantity")) - $old_quantity;
        
        $order_id = $this->get_data("order_id");
        $customerID = $this->customer_id;
        
        $estate=$this->get_data("estate");
        $wine = & new bllwine();
        if ($order_id && $customerID && $wine->loadByPrimaryKey($wine_id))
        {
            
            $this->set_data("wine_name", $wine->get_data("wine_name"));
            $this->set_data("cspc_code", $wine->get_data("cspc_code")); 
            if ($bNew) //get price for only new order items. For old order items keep the price fixed
            {
                $this->set_data("price_per_unit", $wine->get_data("price_per_unit"));
                $this->set_data("price_winery", $wine->get_data("price_winery"));
         		
				
				//$profit= $quantity*(F60DbUtil::getProfits($this->estate_id,$this->store_type_id,$wine->get_data("price_winery"),$wine->get_data("lkup_bottle_size_id"),$wine_id));
				$profit= $quantity*(F60DbUtil::getBCWineProfits4BCSales($this->estate_id,$this->store_type_id,$wine_id)); //April 01, 2015
		
				//	$this->showtrace($profit);
			/*	$fp = fopen("logs/profit.log","a");
				fputs($fp,  $profit);
				fclose($fp);
			*/		 	  
                $this->set_data("profit", $profit);
            }
            $this->set_data("wine_vintage", $wine->get_data("vintage"));

            
            if($bNew)
            {
	            $SQL = "select litter_deposit from lkup_bottle_sizes where lkup_bottle_size_id = " . $wine->get_data("lkup_bottle_size_id") ;
	            $Db =& Db::getInstance();
	            $oldMode = $Db->setFetchMode(ADODB_FETCH_ASSOC);
	            $result = $Db->getAll($SQL);
	            $this->set_data("litter_deposit", $result[0]["litter_deposit"]);
	        }
            //update invoice number by Arrowleaf's invoice number
            
            if (parent::save())
            {
                //update inventory
                
                $this->removeFromInventory($quantity, $wine);
               
                //if total order = 0 then delete the order item
                if ($this->get_data("ordered_quantity") <= 0)
                {
                    $this->mark_deleted();
                    parent::save();
                }               

                return true;
            }
        }    
        return false;
        
    }
    
    function importOrderITemForAL($wineInfo)
    {
         
     /*   $wine_id = $wineInfo["wine_id"];
        $wine_name = $wineInfo["wine_name"];
        $SKU = $wineInfo["sku"];
        $vintage = $wineInfo["vintage"];
      	$lkup_bottle_size_id = $wineInfo["lkup_bottle_size_id"];
      	
        $quantity = $wineInfo["quantity"];
        $store_type_id = $wineInfo["store_type_id"];
        $order_id = $wineInfo["order_id"];
        $customerID = $wineInfo["customer_id"];
        $estate_id = $wineInfo["estate_id"];
        $price_per_unit = $wineInfo["price_per_unit"];
       
         $wine = & new bllwine();
            
        //    $this->set_data("wine_name", $wine->get_data("wine_name"));
        $this->set_data("order_id", $order_id); 
        $this->set_data("wine_id", $wine_id); 
        $this->set_data("ordered_quantity", $quantity); 
        $this->set_data("wine_name", $wine_name); 
        $this->set_data("cspc_code", $SKU); 
           
			
		$this->set_data("price_per_unit", $price_per_unit);
        
		$this->set_data("price_winery", $price_per_unit);
     		 
     	$profit =0;
     	
     	if($store_type_id!=5)
			$profit= $quantity*(F60DbUtil::getProfits($estate_id,$store_type_id,$price_per_unit,$lkup_bottle_size_id,$wine_id));
				 
        $this->set_data("profit", $profit);
        
     
       
       
        $this->set_data("wine_vintage", $vintage);

            
        $SQL = "select litter_deposit from lkup_bottle_sizes where lkup_bottle_size_id = " . $lkup_bottle_size_id;
        $Db =& Db::getInstance();
        $oldMode = $Db->setFetchMode(ADODB_FETCH_ASSOC);
        $result = $Db->getAll($SQL);
        $this->set_data("litter_deposit", $result[0]["litter_deposit"]);
        //update invoice number by Arrowleaf's invoice number
        
        if (parent::save())
        {
        	return true;
		}
            */
        return false;
    }
    
    function updateInvoiceNoByAL($order_id,$invoice_number)
    {
		
	}
    function removeFromInventory($quantity, $wine = NULL)
    {
        //sanity checks
        if ($quantity == 0) return true;
        $customerID = $this->customer_id;
        if (!$customerID) return false;
        $wine_id = $this->get_data("wine_id");
        if (!$wine)
        {
            $wine = & new bllwine();
            if (!$wine->loadByPrimaryKey($wine_id)) return false;
        }
        
        $wine->set_data("total_bottles", $wine->get_data("total_bottles") - $quantity);
        $wine->save(1);
        
        $customerAllocation = & new dal_alct_wines_customers();
        $customerAllocation->add_filter("wine_id", "=", $wine_id);
        $customerAllocation->add_filter("AND");
        $customerAllocation->add_filter("customer_id", "=", $customerID);
     //   echo here;
        if ($customerAllocation->load())
        {
            $customerAllocation->set_data("sold", $customerAllocation->get_data("sold") + $quantity);        
           $customerAllocation->save();
            
            $sql = "INSERT INTO customer_wine_allocation_history
                    (user_id,
                    customer_wine_allocation_id,
                    when_entered,
                    allocated,
                    sold
                    ) ";
            $sql = $sql . " Select " . $this->get_current_user_id() . ",
                           customer_wine_allocations.customer_wine_allocation_id,
                           customer_wine_allocations.entered_time,
                           customer_wine_allocations.allocated,
                           customer_wine_allocations.sold";
            $sql = $sql . " From customer_wine_allocations where wine_id = " . $wine_id;
            $sql = $sql . " And customer_id = " . $customerID;
            $result = &F60DbUtil :: runSQL($sql);
        }
    }
    
    function returnToInventory($quantity, $wine = NULL)
    {
        //sanity checks
        if ($quantity == 0) return true;
        $customerID = $this->customer_id;
        if (!$customerID) return false;
        $wine_id = $this->get_data("wine_id");
        if (!$wine)
        {
            $wine = & new bllwine();
            if (!$wine->loadByPrimaryKey($wine_id)) return false;
        }
            
        $wine->set_data("total_bottles", $wine->get_data("total_bottles") + $quantity);
        $wine->save(1);
        
        $customerAllocation = & new dal_alct_wines_customers();
        $customerAllocation->add_filter("wine_id", "=", $wine_id);
        $customerAllocation->add_filter("AND");
        $customerAllocation->add_filter("customer_id", "=", $customerID);
        
       // $customerAllocation->add_filter("00000customer_id", "=", $customerID);
        if ($customerAllocation->load())
        {
         
		//  echo "sold".$customerAllocation->get_data("sold");
        //    echo "sold2-".$quantity;   

//echo "after deduct".
			
			$new_quantity= ($customerAllocation->get_data("sold") - $quantity);

            $customerAllocation->set_data("sold", $new_quantity);
                     
            $customerAllocation->save();
            
            $sql = "INSERT INTO customer_wine_allocation_history
                    (user_id,
                    customer_wine_allocation_id,
                    when_entered,
                    allocated,                 
                    sold
                    ) ";
            $sql = $sql . " Select " . $this->get_current_user_id() . ",
                           customer_wine_allocations.customer_wine_allocation_id,
                           customer_wine_allocations.entered_time,
                           customer_wine_allocations.allocated,
                           customer_wine_allocations.sold";
            $sql = $sql . " From customer_wine_allocations where wine_id = " . $wine_id;
            $sql = $sql . " And customer_id = " . $customerID;
            $this->mark_deleted();
            parent::save();
        }
    }

}

class bllorderitems extends dalorderitemscollection
{
    var $order_id;
    
    function bllorderitems($order_id)
    {
        $this->order_id = $order_id;
        parent::dalorderitemscollection();
        $this->add_filter("order_id", "=", $order_id);
        $this->add_filter("AND");
        $this->add_filter("deleted", "=", "0");
        $this->add_sort("wine_name");
    }
	
    function add_new()
    {
        //override collection add_new, return bll class
        $bll = & new bllorderitem();
        return $bll;
    }
	
    function &getByPrimaryKey($keyValues)
    {
        $bll = & new bllorderitem();
        if ($bll->loadByPrimaryKey($keyValues))
            return $bll;
        return nulll;
    }
}
?>