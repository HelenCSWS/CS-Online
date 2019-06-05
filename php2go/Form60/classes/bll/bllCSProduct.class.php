<?php

import('Form60.dal.dalCSOrder');
import('php2go.util.TypeUtils');
import('Form60.base.F60DbUtil');

import('Form60.util.F60Date');
//import('Form60.base.F60DbUtil');
import('php2go.data.PagedDataSet');
import('Form60.base.F60DALBase');
import('Form60.util.F60Common');


class bllCSProduct 
{  
    function bllCSProduct()
    {
       // parent::dalCSOrder();
        $this->db = & Db::getInstance();
        $this->db->setFetchMode(ADODB_FETCH_ASSOC);
    }

  	function getCSProductBasicInfoById($cs_product_id)
    {
     	$sql="Select * from cs_products where cs_product_id = $cs_product_id and deleted=0";
     	$rows = $this->db->getAll($sql);
		if (count($rows) <= 0)
		{
		     return 0;
		}
		else
		{
		    return $rows;
		}
    }    
    
    function checkDuplicatProductName($product_name)
	{
	 
		$sql="Select * from cs_products where product_name='$product_name' 									
										  and deleted =0
										  ";
		$rows = $this->db->getAll($sql);
		
		$cnt=count($rows);
	
		if($cnt==0)
		{
		 
			return false;
		}
		else
		{
			return true;
		}
	}
    
    function saveProductBasicInfo($estate_id,$product_name,$model,$unitPerCS,$type_id,$user_id)
    {
        $created_user_id= F60DALBase::get_current_user_id();
        
       $product_name = str_replace("'","\'",$product_name );	
        

        $sql="insert into cs_products (estate_id,product_name,product_code,units_per_case,lkup_product_type_id,when_entered,created_user_id)
              value($estate_id,'$product_name','$model',$unitPerCS,$type_id,now(),$user_id)";
              
         
        $retVal = $this->db->execute($sql);
		
		$cs_product_id=0;
		if($retVal)
		{
			$cs_product_id = $this->db->lastInsertId();
		}
		
		return $cs_product_id;      
    }
  
    
    function updateProductBasicInfo($product_id,$product_name,$model,$unitPerCS,$type_id,$user_id)
    {
             $product_name = str_replace("'","\'",$product_name );	
             
        $sql="update cs_products set product_name='$product_name', product_code='$model',
              units_per_case=$unitPerCS,lkup_product_type_id=$type_id,modified_user_id=$user_id
              where cs_product_id =$product_id";
              
        $retVal = $this->db->execute($sql);
		
		return $retVal;      
    }
                                  //  ($product_id,0, 0, 0,  0,0,                                 $this->login_user_id,$db_province_id);
    function saveProductProvinceInfo($product_id,$display_price,$special_price,$commission,$cost,$user_id,$province_id)
    {
        $sql="insert into cs_products_info(cs_product_id,price_per_unit,promotion_price,cost_per_unit,commission,created_user_id,province_id,when_entered)
              value($product_id,$display_price,$special_price,$cost,$commission,$user_id,$province_id,now())";
              
            // insert into cs_products_info(cs_product_id,price_per_unit,promotion_price,cost_per_unit,commission,province_id,user_id,when_entered) 
            //value(1,0,0,0,0,124,0,now())
        $retVal = $this->db->execute($sql);		
		
		return $retVal;      
    }
    
    function updateProductProvinceInfo($product_id,$display_price,$special_price,$commission,$cost,$user_id,$province_id)
    {
        $sql="update cs_products_info set price_per_unit=$display_price,promotion_price=$special_price,
              cost_per_unit=$cost,commission=$commission,modified_user_id =$user_id
              where province_id=$province_id and cs_product_id=$product_id ";
              
        $retVal = $this->db->execute($sql);		
		
		return $retVal;      
    }
    
    function deleteProduct($product_id,$user_id)
    {
        $sql="update cs_products set deleted =1, modified_user_id =$user_id where cs_product_id = $product_id";
        $retVal = $this->db->execute($sql);
        
        if($retVal)
        {
           	$sql="update cs_products_info set deleted =1, modified_user_id =$user_id where cs_product_id = $product_ids";
            $retVal = $this->db->execute($sql);            
        }
        return $retVal;
    }
    
    function getProductBasicInfo($cs_product_id)
	{
	     $sql= "select * from cs_products where cs_product_id=$cs_product_id and deleted=0";//" and cs_product_id=$cs_product_id";
         $result = & F60DbUtil::runSQL($sql);
         $row = & $result->FetchRow();	                
	     return $row;
	}
    
    function getProductInfo($cs_product_id,$province_id)
	{
	     $sql= "select * from cs_products_info where cs_product_id=$cs_product_id and province_id=$province_id and deleted=0";//" and cs_product_id=$cs_product_id";
         $result = & F60DbUtil::runSQL($sql);
         $row = & $result->FetchRow();	 	                
	     return $row;
	}   
 
    function getProductInventory($cs_product_id,$province_id)
    {
 	     $sql= "select * from cs_product_inventory where cs_product_id=$cs_product_id and province_id=$province_id";//" and cs_product_id=$cs_product_id";
        	$rows = $this->db->getAll($sql);
		
    		$cnt=count($rows);
    	 
    		if($cnt==0)
    		{
    		 
    			return -1;
    		}
    		else
    		{
    	
    			return $rows[0]["total_units"];
    		}	  
 
    }
    
    function addUnitsToInventory($cs_product_id, $province_id,$units)
    {
        $user_id =F60DALBase::get_current_user_id();
        
        $org_units = $units;
        $inventoryUnits = $this->getProductInventory($cs_product_id,$province_id);
        if($units!=0 &&$units!="")
        {
            
            if($inventoryUnits==-1) // add new
            {
               $sql="insert cs_product_inventory (total_units,cs_product_id, province_id,when_entered,created_user_id) values($units,$cs_product_id,$province_id,now(),$user_id)";
               
            }
            else //update
            {
                $units = $inventoryUnits+$units;
                $sql="update cs_product_inventory  set total_units = $units,modified_user_id = $user_id where cs_product_id =$cs_product_id and province_id=$province_id";
            }
                        
            $retVal = $this->db->execute($sql);
            
            if($inventoryUnits==-1) 
                $cs_product_id = $this->db->lastInsertId();
                
          //  $this->updateInventoryHistoty($cs_product_id, $province_id,$units,$user_id);
            
            return $units;
            
        }
        
        return 0;
        
    }
    
   /* function updateInventoryHistoty($cs_product_id,$province_id,$units,$user_id)
    {
        $sql="insert cs_product_inventory_history (total_units,cs_product_id, province_id,when_entered,created_user_id) values($units,$cs_product_id,$province_id,now(),$user_id)";
    
         $retVal = $this->db->execute($sql);		
        
        return $retVal;      
    }*/
  
	
}
  

?>
