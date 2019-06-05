<?php

import('php2go.util.TypeUtils');
import('Form60.base.F60DbUtil');
import('Form60.base.F60DALBase');
import('php2go.base.Php2Go');
import('Form60.util.F60Date');

class bllf60wines extends Php2Go 
{
 	var $db;
   
    function bllf60wines()
    {
        
        $this->db = & Db::getInstance();
        $this->db->setFetchMode(ADODB_FETCH_ASSOC);
    }



	function getWinesBasicInfo($wine_id,$isCa=false)
	{
	 	if($isCa)
			$sql="Select * from wines where wine_id =$wine_id and deleted =0 and is_available=0 ";
	 	else
			$sql="Select * from wines where wine_id =$wine_id and deleted =0";
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
	
	function getTotalCases($wine_id)
	{
	 
		$sql="SELECT sum(total_cases) total_cases FROM `wine_delivery_dates` where wine_id=$wine_id and deleted=0";
		$rows = $this->db->getAll($sql);
		
		if (count($rows) <= 0)
		{
		    // $this->file_format_error .= "Error: No wines.";
		     return 0;
		}
		else
		{
		
		    return $rows[0]["total_cases"];
		}
	}
	
	function getWinesByWineID($wine_id)
	{

		$sql="Select * from wines_info where wine_id = $wine_id and deleted =0";
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


	function getFieldValue($form,$fieldName,$pro_id=null)
	{
	 
		$suffix="";
		if($pro_id!=null)
		{
			if($pro_id==1)
			{
				$suffix="_bc";
			}
			else if ($pro_id==2)
			{
				$suffix="_ab";
			}
			else if ($pro_id==3)
			{
				$suffix="_mb";
			}
		
		}		
		$fieldName= $fieldName.$suffix;
			
		
		$ctl = & $form->getField($fieldName);
		
		$val = $ctl->getValue();
	

			
			
	
		if($fieldName=="wine_name")
		{
			$val = str_replace("'","\'",$val );	
		}

		return $val;
	}
	

	function setValue2Field($form,$fieldName,$val)
	{
		$ctl = & $form->getField($fieldName);
		
		$ctl->setValue($val);
		
	}
	
	function insertCaWine($form)
	{
	 	$pro_id=1;
	  	$created_user_id= F60DALBase::get_current_user_id();
      	$when_created=F60Date::sqlDateTime();
         
		
	 	$wine_name = $this->getFieldValue($form,"wine_name");	 	

	 	$bottle_size_id = $this->getFieldValue($form,"lkup_bottle_size_id");
	 	$type_id = $this->getFieldValue($form,"lkup_wine_color_type_id");
	 	$btl_per_case = $this->getFieldValue($form,"bottles_per_case");
	 	$vintage = $this->getFieldValue($form,"vintage");
	 	$estate_id = $this->getFieldValue($form,"estate_id");
	 	$cspc_code = $this->getFieldValue($form,"cspc_code",$pro_id);
	 	$delivery_date = F60Date::getSqlDate($this->getFieldValue($form,"delivery_date"));
	 	$price_winery = $this->getFieldValue($form,"price_winery",$pro_id);
	 	$price_per_unit = $this->getFieldValue($form,"price_per_unit",$pro_id);
	 	$total_bottles = $this->getFieldValue($form,"total_bottles");
	 	$total_cases = $this->getFieldValue($form,"total_cases");
	 	$case_value = $this->getFieldValue($form,"case_value",$pro_id);
	 	$case_sold = $this->getFieldValue($form,"case_sold",$pro_id);
	 	
	 	$case_value = $case_value/$case_sold;

		$SQL = "INSERT INTO wines (wine_name, estate_id, lkup_bottle_size_id, lkup_wine_color_type_id, vintage, bottles_per_case, when_entered, created_user_id, 
											cspc_code, price_winery, price_per_unit, is_international, case_value,total_bottles) VALUES ";
		$SQL = $SQL."('$wine_name',$estate_id, $bottle_size_id,$type_id,$vintage,$btl_per_case,'$when_created', $created_user_id,
							'$cspc_code', $price_winery, $price_per_unit, 0, $case_value,$total_bottles)";
		
		$retVal = $this->db->execute($SQL);
		
		$wine_id =0;
		if($retVal)
		{
			$wine_id = $this->db->lastInsertId();

			$SQL = "INSERT INTO wine_delivery_dates (wine_id, delivery_date, when_entered,created_user_id,total_cases) VALUES ";
			$SQL = $SQL."($wine_id,'$delivery_date', '$when_created', $created_user_id,$total_cases)";
							
			$retVal = $this->db->execute($SQL);
                        
		
		}
		return $wine_id;
		
	}
	function insertWineBasicInfo($form, $isBc=false)
	{
	 	
            if($isBc)
            {
             
                    $retval = $this->insertCaWine($form);
            }
            else
            {
            
                    $retval = $this->insertWine4In($form);
            }
            //insert BC CSPC code into include in store penetration report table
            $cspc_code = $this->getFieldValue($form,"cspc_code",1);
            if ($cspc_code<>"")
            {
                if ($this->getFieldValue($form,"chkIncludeInStorePenReport")== "T")
                {
                    $cspc_code = $this->getFieldValue($form,"cspc_code",1);
                    $this->insertCSPCcodeForPenetrationReport($cspc_code);
                }
            }
            
            return $retval;
	}

	function insertWine4In($form)
	{
      $created_user_id= F60DALBase::get_current_user_id();
      $when_created=F60Date::sqlDateTime();
             
		
	 	$wine_name = $this->getFieldValue($form,"wine_name");
	 	$bottle_size_id = $this->getFieldValue($form,"lkup_bottle_size_id");
	 	$type_id = $this->getFieldValue($form,"lkup_wine_color_type_id");
	 	$btl_per_case = $this->getFieldValue($form,"bottles_per_case");
	 	$vintage = $this->getFieldValue($form,"vintage");
	 	$estate_id = $this->getFieldValue($form,"estate_id");
	 	$is_international = $this->getFieldValue($form,"is_international");
	 	if($is_international ==0)
	 		$is_international =0;
	 	else
	 		$is_international =1;
	  
		$SQL = "INSERT INTO wines (wine_name, estate_id, lkup_bottle_size_id, lkup_wine_color_type_id, vintage, bottles_per_case, when_entered,created_user_id,is_international ) VALUES ";
		$SQL = $SQL."('$wine_name',$estate_id, $bottle_size_id,$type_id,$vintage,$btl_per_case,'$when_created', $created_user_id,$is_international)";
		
		$retVal = $this->db->execute($SQL);
		
		$wine_id =0;
		if($retVal)
		{
			$wine_id = $this->db->lastInsertId();
		
		}
	
		return $wine_id;
	}
	
	function insertWineInfoByProId($form,$pro_id,$wine_id)
	{

		$created_user_id= F60DALBase::get_current_user_id();
      $when_created=F60Date::sqlDateTime();
      
	 	$cspc_code = $this->getFieldValue($form,"cspc_code",$pro_id);
	 	$cost_per_unit = $this->getFieldValue($form,"cost_per_unit",$pro_id);
	 	if($cost_per_unit=="")
	 			$cost_per_unit =0;
	 	$price_winery = $this->getFieldValue($form,"price_winery",$pro_id);
	 	$price_per_unit = $this->getFieldValue($form,"price_per_unit",$pro_id);
	 	$profit_per_unit = $this->getFieldValue($form,"profit_per_unit",$pro_id);
	 	$case_value = $this->getFieldValue($form,"case_value",$pro_id);
	 	$case_sold = $this->getFieldValue($form,"case_sold",$pro_id);
	 	
	 	$case_value = $case_value/$case_sold;

		$SQL = "INSERT INTO wines_info (wine_id, cspc_code, cost_per_unit, price_winery, price_per_unit, profit_per_unit,case_value,province_id,when_entered,created_user_id) VALUES ";
		$SQL = $SQL."($wine_id, '$cspc_code',$cost_per_unit,$price_winery,$price_per_unit,$profit_per_unit,$case_value,$pro_id,'$when_created', $created_user_id)";
		
		
	
		$retVal = $this->db->execute($SQL);
                
                
		
		return $retVal;	
		
	}
	
	function updateWineBasicInfo($form,$wine_id, $isBc=false)
	{
	 	$user_id= F60DALBase::get_current_user_id();
      //$when_date=F60Date::sqlDateTime();
      
	 	$wine_name = $this->getFieldValue($form,"wine_name");
	 	$bottle_size_id = $this->getFieldValue($form,"lkup_bottle_size_id");
	 	$type_id = $this->getFieldValue($form,"lkup_wine_color_type_id");
	 	$btl_per_case = $this->getFieldValue($form,"bottles_per_case");
	 	$vintage = $this->getFieldValue($form,"vintage");
	 	
	 	$cspc_code = $this->getFieldValue($form,"cspc_code",1); //cn
	 	if($cspc_code=="")
	 	{
			$isBc=false;
		}

		if($isBc)
		{
		 	
		 	$pro_id=1;
		 	$cspc_code = $this->getFieldValue($form,"cspc_code",$pro_id);
		 	//$cost_per_unit = $this->getFieldValue($form,"cost_per_unit",$pro_id);
		 	$price_winery = $this->getFieldValue($form,"price_winery",$pro_id);
		 	$price_per_unit = $this->getFieldValue($form,"price_per_unit",$pro_id);
		 //	$profit_per_unit = $this->getFieldValue($form,"profit_per_unit",$pro_id);
		 	$case_value = $this->getFieldValue($form,"case_value",$pro_id);
		 	$case_sold = $this->getFieldValue($form,"case_sold",$pro_id);
		 	
		 	$case_value = $case_value/$case_sold;

			$SQL = "Update wines set wine_name='$wine_name',
										 lkup_bottle_size_id = $bottle_size_id,
										 lkup_wine_color_type_id=$type_id,
										 vintage=$vintage,
										 bottles_per_case=$btl_per_case,
										 modified_user_id = $user_id,
										 cspc_code ='$cspc_code',
										 price_winery =$price_winery,
										 price_per_unit =$price_per_unit,
										 case_value =$case_value,
										 is_available=0,
										 is_international=0
										 
					where wine_id = $wine_id ";
		}
		else
		{
			$SQL = "Update wines set wine_name='$wine_name',
										 lkup_bottle_size_id = $bottle_size_id,
										 lkup_wine_color_type_id=$type_id,
										 vintage=$vintage,
										 bottles_per_case=$btl_per_case,
										 modified_user_id = $user_id
										 
					where wine_id = $wine_id ";
		}									 
		
		$retVal = $this->db->execute($SQL);
                
                //insert BC CSPC code into include in store penetration report table
                $cspc_code = $this->getFieldValue($form,"cspc_code",1);
                if ($cspc_code<>"")
                {
                    if ($this->getFieldValue($form,"chkIncludeInStorePenReport")== "T")
                    {
                        $this->insertCSPCcodeForPenetrationReport($cspc_code);
                    }
                    else
                        $this->deleteCSPCcodeForPenetrationReport($cspc_code);
                }

		
		return $retVal;
	}

	function updateWineInfo($form,$wine_id, $pro_id)
	{
	 	$user_id= F60DALBase::get_current_user_id();
	 	 
	 	$cspc_code = $this->getFieldValue($form,"cspc_code",$pro_id);
	 	$cost_per_unit = $this->getFieldValue($form,"cost_per_unit",$pro_id);
	 	if($cost_per_unit=="")
	 		$cost_per_unit=0;
	 	$price_winery = $this->getFieldValue($form,"price_winery",$pro_id);
	 	$price_per_unit = $this->getFieldValue($form,"price_per_unit",$pro_id);
	 	$profit_per_unit = $this->getFieldValue($form,"profit_per_unit",$pro_id);
	 	$case_value = $this->getFieldValue($form,"case_value",$pro_id);
	 	$case_sold = $this->getFieldValue($form,"case_sold",$pro_id);
	 	
	 	$case_value = $case_value/$case_sold;
	 	
	 	
	 	

		$SQL = "Update wines_info set cspc_code = '$cspc_code',
		 										cost_per_unit =$cost_per_unit,
											   price_winery = $price_winery,
												price_per_unit=$price_per_unit,
												profit_per_unit=$profit_per_unit,
												case_value=$case_value,
												province_id=$pro_id,
												modified_user_id = $user_id
												where wine_id=$wine_id 
												and 	province_id= $pro_id";
	//	$SQL = $SQL."($cspc_code,$cost_per_unit,$price_winery,$price_per_unit,$profit_per_unit,$case_value,$pro_id)";
		
		$retVal = $this->db->execute($SQL);
		
		return $retVal;
	}


	function deleteWine4Province($is_international, $wine_id,$pro_id,$estate_id)
	{
	
                $cspc_sql="";
	 	if($is_international==0 && $pro_id==1)
	 	{
                    $sql ="Update wines set is_available=1 where wine_id =$wine_id";	
                    $cspc_sql = "SELECT cspc_code from wines where wine_id = $wine_id";
		}
		else
                {
                    $sql ="Update wines_info set deleted=1 where wine_id =$wine_id and province_id=$pro_id";
                    if ($pro_id == 1)
                    {
                        $cspc_sql = "SELECT cspc_code from wines_info where wine_id = $wine_id and province_id=1";
                    }
                }
		//$retVal = bllf60wines::db->execute($sql);
                
                //delete from include in store penetration report table
	        if ($cspc_sql<>"")
                {
                    $rows = $this->db->getAll($cspc_sql);
                    if (count($rows)>=1)
                    {
                        $cspc_code = $rows[0]["cspc_code"];
                        $this->deleteCSPCcodeForPenetrationReport($cspc_code);
                    }
                }
                
		$retVal =& F60DALBase::excutiveSQL($sql);
		
		return $retVal;
	}
	
	function deleteWine($wine_id,$isBC=false)
	{
            //delete from include in store penetration report table
            if ($isBC)
                $cspc_sql = "SELECT cspc_code from wines where wine_id = $wine_id";
            else
                $cspc_sql = "SELECT cspc_code from wines_info where wine_id = $wine_id and province_id=1";
            
            $rows = $this->db->getAll($cspc_sql);
            if (count($rows)>=1)
            {
                $cspc_code = $rows[0]["cspc_code"];
                $this->deleteCSPCcodeForPenetrationReport($cspc_code);
            }    
            
		$sql ="Update wines_info set deleted=1 where wine_id =$wine_id";
		$retVal = $this->db->execute($sql);
		
	//	if($isBC)
	//	{
			$sql ="Update wines set deleted=1 , is_available=1 where wine_id =$wine_id";
			$retVal = $this->db->execute($sql);	
	//	}
		return true;
	}
	
	function checkDuplicatWines($form,$wine_id = null)
	{
	
	 	$cnt =0;
	 	$estate_id = $this->getFieldValue($form,"estate_id");
	 	$wine_name = $this->getFieldValue($form,"wine_name");
	 	$bottle_size_id = $this->getFieldValue($form,"lkup_bottle_size_id");
	 	$type_id = $this->getFieldValue($form,"lkup_wine_color_type_id");
	 	$btl_per_case = $this->getFieldValue($form,"bottles_per_case");
	// 	$cspc = $this->getFieldValue($form,"cspc_code");
	 	$vintage = $this->getFieldValue($form,"vintage");
	 	
		if($wine_id!=null or $wine_id!="")	
		{
			$sql="Select * from wines where wine_name='$wine_name' 
				  and lkup_bottle_size_id=$bottle_size_id 
				  and lkup_wine_color_type_id = $type_id
				  and bottles_per_case = $btl_per_case
				  and vintage = $vintage
				  and wine_id != $wine_id
				  and estate_id = $estate_id
				  and deleted =0 ";
			$rows = $this->db->getAll($sql);
			
			$cnt=count($rows);
		}
		else
		{
			$sql="Select * from wines where wine_name='$wine_name '
											  and lkup_bottle_size_id=$bottle_size_id 
											  and lkup_wine_color_type_id = $type_id
											  and bottles_per_case = $btl_per_case
											  and vintage = $vintage
											  and estate_id = $estate_id
											  
											  and deleted =0
											  ";
			$rows = $this->db->getAll($sql);
			
			$cnt=count($rows);
		}
		
	
		if($cnt==0)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
		
		
    function insertCSPCcodeForPenetrationReport($cspc_code)
    {
        $this->deleteCSPCcodeForPenetrationReport($cspc_code);
        $sql = "INSERT INTO include_in_store_penetration_report (cspc_code) VALUES ('$cspc_code');";
        return $this->db->execute($sql);
    }
		
    function deleteCSPCcodeForPenetrationReport($cspc_code)
    {
        $sql = "DELETE FROM include_in_store_penetration_report WHERE cspc_code='$cspc_code';";
        return $this->db->execute($sql);
    }
	
    function isCSPCcodeinPenetrationReport($cspc_code)
    {
        $sql = "SELECT cspc_code FROM include_in_store_penetration_report WHERE cspc_code='$cspc_code' LIMIT 1;";
        $rs = $this->db->query($sql);
        return (($rs->RecordCount()>=1)?true:false);
    }
}
?>
