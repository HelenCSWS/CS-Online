<?php

import('php2go.util.TypeUtils');
import('Form60.base.F60DbUtil');

import('php2go.base.Php2Go');
import('Form60.util.F60Date');


class bllBeers extends  Php2Go 
{
   	var $db;
   
    function bllBeers()
    {
        
        $this->db = & Db::getInstance();
        $this->db->setFetchMode(ADODB_FETCH_ASSOC);
    }

    function getBeerBasicInfoByBeerId($beer_id)
    {
     	$sql="Select * from beers where beer_id = $beer_id and deleted=0";
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
    
    function getBeerInfoByProvince($beer_id,$province_id="")
    {
     	$provinceFilter="";
     	if($province_id!="")
     		$provinceFilter=" and province_id=$province_id";
     		
     	$sql="Select * from beers_info where beer_id = $beer_id $provinceFilter and deleted=0 order by province_id asc";
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

	function checkDuplicatBasicBeers($aryBeer)
	{
	 	$cnt 			=0;
	 	$estate_id 		= $aryBeer["estate_id"];
	 	$beer_name 		= $aryBeer["beer_name"];
        $beer_name = str_replace("'","\'",$beer_name );	
        
	 	$bottle_size_id = $aryBeer["size_id"];
	 	$type_id 		= $aryBeer["type_id"];
	 	$btl_per_case 	= $aryBeer["bottles_per_case"];
	 	$btl_per_pack 	= $aryBeer["bottles_per_pack"];
	 	$beer_id 		= $aryBeer["beer_id"];
	 	
		if($beer_id!="")	
		{
			$sql="Select * from beers where beer_name='$beer_name' 
				  and lkup_beer_size_id=$bottle_size_id 
				  and lkup_beer_type_id = $type_id
				  and bottles_per_case = $btl_per_case
				  and bottles_per_pack = $btl_per_pack
				  
				  and beer_id != $beer_id
				  and estate_id = $estate_id
				  and deleted =0 ";
			$rows = $this->db->getAll($sql);
			
			$cnt=count($rows);
		}
		else
		{
			$sql="Select * from beers where beer_name='$beer_name'
											  and lkup_beer_size_id=$bottle_size_id 
											  and lkup_beer_type_id = $type_id
											  and bottles_per_case = $btl_per_case
											  and bottles_per_pack = $btl_per_pack											  
											  and estate_id = $estate_id
											  and deleted =0
											  ";
			$rows = $this->db->getAll($sql);
			
			$cnt=count($rows);
		}
		
	
		if($cnt==0)
		{
		 
			return false;
		}
		else
		{
			return true;
		}
	}
	
	function checkDuplicateProvinceBeers($aryBeer,$province_id)
	{
	 	$cnt 			=0;
	 	$beer_id 		= $aryBeer["beer_id"];
	 	
	 	$cspc_code = $aryBeer[$province_id]["cspc_code"];
	 
	 	if($beer_id!="")
	 	{
	 		$sql="select * from beers_info where beer_id = $beer_id";
		 	$rows = $this->db->getAll($sql);
			$isAdded=count($rows);
		}
		else
			$isAdded =0;
	 	
		if($isAdded>0)	// added, check if the beer is duplicat with other
		{
			$sql="Select * from beers_info where cspc_code='$cspc_code' 
				 
				  and beer_id != $beer_id
				  and province_id =$province_id
				  and deleted =0";
				  
			$rows = $this->db->getAll($sql);
			
			$cnt=count($rows);
		}
		else // not added yet, 
		{
			$sql="Select * from beers_info where cspc_code='$cspc_code' 
							and deleted =0
							and province_id =$province_id";
							
			$rows = $this->db->getAll($sql);
			
			$cnt=count($rows);
		}
			
		if($cnt==0)
		{
			return false;
		}
		else
		{
			return true;
		}
	}
	
	
	/*
		Add, update, delete the beer functions
		
		modifyId: integer, 0:insert; 1: update; 2: delete 
	
	*/
	function saveBeer($aryBeer,$modifyId,$province_id="",$oldBeerData=null)
	{

	 //	echo "modif: ".$modifyId;
	// echo $aryBeer["beer_id"];
	 
	 
	 	$retValue=true;
		switch ($modifyId)	
		{
			case 0: // add beer basic information			
				$retValue=$this->saveBeerBasicInfo($aryBeer,0);
					
				break;
			
			case 1: // add new for province
			
				if($aryBeer["beer_id"]!="")			
					$retValue = $this->saveBeerProvinceInfo($aryBeer,$province_id,0);
				else
					$retValue=false;
				break;
			
			case 2: //update
			 //update beer
				$retVale=true;
				if($province_id=="")//basice information
				{
				 	
						$this->isBeerUpdated($aryBeer,$oldBeerData);
							$retValue = $this->saveBeerBasicInfo($aryBeer,1);	

				}						
				else
				{
						if($this->isBeerUpdated($aryBeer,$oldBeerData,$province_id))
							$retValue = $this->saveBeerProvinceInfo($aryBeer,1,1);
						
						
				}
				break;
				
		
			
		}
			
		

		return $retValue;
	}	
	
	function assignEstate2Beer($estate_id)
	{
		$sql= "select * from estates_products where estate_id=$estate_id";
		
		$rows = $this->db->getAll($sql);
		
		if(count($rows)==0)
		{
			$sql="insert into estates_products(estate_id, lkup_product_id) 
				  values($estate_id,2)";
			return $this->db->execute($sql);
		}
		else
		{
			return true;
		}
		
		
		
	}
	
	//isDelete beer
	function deleteBeerProvinceInfo($beer_id,$province_id)
	{
			$sql="Update beers_info
				  Set deleted =1	 
			      Where beer_id = $beer_id and province_id =$province_id";
			      
			return $this->db->execute($sql);
	}		
	
	
	function deleteBeer($beer_id,$province_id)
	{
	 	if($province_id==0)// delete all beers infor
	 	{
		 	//delete province infor 
		 	$sql="Update beers_info
					  Set deleted =1	 
				      Where beer_id = $beer_id";
				      
			if($this->db->execute($sql))
			{
				$sql="Update beers 
					  Set deleted =1	 
					  Where beer_id = $beer_id";
					      
				return $this->db->execute($sql);
			}
			else
				return false;		
		}
		else
		{
			return $this->deleteBeerProvinceInfo($beer_id,$province_id);
		}
		
	}

	/*
		Add, update, delete the basic information in Beers table
		
		modifyId: integer, 0:insert; 1: update; 2: delete 
	
	*/
	function saveBeerBasicInfo($aryBeer)
    {
     	
     	$beer_name =$aryBeer['beer_name'];
     	$beer_name = str_replace("'","\'",$beer_name );	
     	
     	$user_id= F60DALBase::get_current_user_id();
      	$current_time=F60Date::sqlDateTime();
      	$bottle_per_case = $aryBeer['bottles_per_case'];
      	$bottle_per_pack = $aryBeer['bottles_per_pack'];
      	$type_id = $aryBeer['type_id'];
      	$size_id = $aryBeer['size_id'];
      	$estate_id = $aryBeer['estate_id'];
      	$beer_id = $aryBeer['beer_id'];      	
     	 
 	 	if($beer_id=="")
		{
	     	$sql="Insert into beers (estate_id,beer_name,bottles_per_case,bottles_per_pack,lkup_beer_type_id,
			      lkup_beer_size_id,when_entered,created_user_id)
				  Values ($estate_id, '$beer_name', $bottle_per_case,$bottle_per_pack,$type_id,
				  $size_id,'$current_time',$user_id) ";
		
		}	
		else//update
		{
			$sql="Update beers 
				 Set beer_name='$beer_name',bottles_per_case=$bottle_per_case,
				 bottles_per_pack=$bottle_per_pack,
				 lkup_beer_type_id=$type_id,lkup_beer_size_id=$size_id,
				 when_modified='$current_time',modified_user_id=$user_id
				 Where beer_id = $beer_id";
		}
	
		if($this->db->execute($sql))
		{
		 	if($beer_id=="")
		 	{
		 	 	
		 	 	$id =$this->db->lastInsertId();
		 	 	
				return $id;
			}
			
			else
			{
			
				return true;
			}
		}
		else
			return false;
    }
    
    /*
		Add, update, delete the province information in Beers)info table
		
		modifyId: integer, 0:insert; 1: update; 2: delete 
	
	*/
    function saveBeerProvinceInfo($aryBeer,$province_id,$modifyId)
    {
     	
		$user_id= F60DALBase::get_current_user_id();
      	$current_time=F60Date::sqlDateTime();
      
	 	$cspc_code = $aryBeer[$province_id]["cspc_code"];
	 	$cost_per_unit = $aryBeer[$province_id]["cost"];
	 	$price_winery = $aryBeer[$province_id]["wholesale"]; 
	 	
	 	$price_per_unit = $aryBeer[$province_id]["display_price"];  
	 	$profit_per_unit =$aryBeer[$province_id]["profit"];  
	 	$case_value =$aryBeer[$province_id]["case_value"]; 
	 	$case_sold =$aryBeer[$province_id]["case_sold"]; 
	 	
	 	//$case_value = $case_value/$case_sold;
	 	
	 	$beer_id =$aryBeer['beer_id']; 
	 

					
	
	
		switch($modifyId)
     	{ 
     	 	case 0: // add 
  		     	$sql = "INSERT INTO beers_info 
				   		(beer_id, cspc_code, cost_per_unit, price_winery, 
						price_per_unit, profit_per_unit,case_value,case_sold,province_id,
						when_entered,created_user_id) 
						VALUES ($beer_id, '$cspc_code',$cost_per_unit,$price_winery,
						$price_per_unit,$profit_per_unit,$case_value,$case_sold,$province_id,
						'$current_time', $user_id)";	
				break;
				
			case 1:			//update
				$sql="Update beers_info 
					 Set cspc_code=$cspc_code,cost_per_unit=$cost_per_unit,price_winery=$price_winery,
					 price_per_unit=$price_per_unit,profit_per_unit=$profit_per_unit,case_value=$case_value,case_sold=$case_sold,
					 when_modified='$current_time',modified_user_id=$user_id	 
					 Where beer_id = $beer_id and province_id =$province_id";
				break;
				
			
		}
		
		$retVal = $this->db->execute($sql);            
                
		return $retVal;	
		
    }

}
?>
