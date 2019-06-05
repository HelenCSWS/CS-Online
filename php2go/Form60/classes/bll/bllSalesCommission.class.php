<?php

import('php2go.base.Php2Go');
import('php2go.util.TypeUtils');
import('Form60.util.F60Date');
import('Form60.base.F60DbUtil');
import('php2go.data.PagedDataSet');
import('Form60.base.F60DALBase');
import('Form60.util.F60Common');


class salesCommissionData extends Php2Go 
{
    var $db;
    var $cfg;
    var $logFile;
    var $errorMessage;
    
    
 
   
    function salesCommissionData()
    {
	 	include('config/emailoutconfig.php');

        $this->db = & Db::getInstance();
        $this->db->setFetchMode(ADODB_FETCH_ASSOC);
        
    }
    function getCommissionType($user_id)
    {
			$sql ="Select lkup_sales_commission_type_id FROM user_sales_commission_types where user_id = $user_id";
			$rows = $this->db->getAll($sql);
			
			if(count($rows)!=0)
			{
				return $rows[0]["lkup_sales_commission_type_id"];
			}
			else
				return 0;			
	}
	
	function getCommissionLevelsDetail($user_id,$lkup_sales_commission_type_id)
	{	
		$sql="select * from sales_commission_levels where user_id =$user_id and lkup_sales_commission_type_id = $lkup_sales_commission_type_id order by level_number asc";
		$rows = $this->db->getAll($sql);
		
		if(count($rows)!=0)
			return $rows;
		else
			return 0;	
	}
	
	function getCommissionTypeByUser($user_id)
	{
		$sql="SELECT distinct lkup_sales_commission_type_id FROM `sales_commission_levels` where user_id =$user_id";
		$rows = $this->db->getAll($sql);
		
		if(count($rows)!=0)
			return $rows[0]["lkup_sales_commission_type_id"];
		else
			return 1;
		
	}
	
	function getUsersByProvince($province_id)
	{
		$sql="SELECT concat(first_name,' ',last_name) user_name, user_id from users where lkup_user_type_id=1 and deleted=0 and province_id=$province_id";
		$rows = & F60DbUtil::runSQL($sql);
		
		if(count($rows)!=0)
			return $rows;
		else
			return 0;	
	}
	
	/*
	
	
	*/
	function saveCommissionData($aryCommissionData)
	{     	
		if($this->deleteCommissionRecords("sales_commission_levels",$aryCommissionData[0]["user_id"]))
		{
		 	foreach($aryCommissionData as $levelCommissionData)
		 	{
				if(!$this->_exeInsert("sales_commission_levels","sales_commission_id",$levelCommissionData))			
				{
					$this->deleteCommissionRecords("sales_commission_levels",$aryCommissionData[0]["user_id"]);
					return false;			
				}
			}
			return true;
		}
		else
		{
			echo "cannot delete";
			return false;
		}
	}

	function _insertSQL($tableName, $primeKeyField, $arrayData)
	{
		$fieldsNamesArray = array_keys($arrayData);
	 	
	 //	print_r($fieldsNamesArray);
	 	
	 	$tableFields="";
	 	$fieldValues="";
	 	
	 	$i=0;
	 	
	 	
	 	$current_user_id= F60DALBase::get_current_user_id();
      	$current_time=F60Date::sqlDateTime();
      	$arrayData["when_entered"]="'$current_time'";
      	$arrayData["created_user_id"]="'$current_user_id'";
      
 	  	foreach($fieldsNamesArray as $keyField)		
	  	{
		   	if($keyField!=$primeKeyField && $keyField!="modified_user_id"&&$keyField!="when_modified")
		   	{			   	 	
		   	 	$tableFields = $tableFields.$keyField.",";
		   	 	$fieldValues=$fieldValues.$arrayData[$keyField].",";
			}
			
			$i++;
		}
		$tableFields= substr($tableFields,0,strlen($tableFields)-1);
		$fieldValues= substr($fieldValues,0,strlen($fieldValues)-1);
		
	
		$sql = "Insert into $tableName ($tableFields) values ($fieldValues)";
		
		return $sql;
	}
	

	function _exeInsert($tableName, $primeKeyField, $arrayData)
	{
			 	
	 	$fieldsNamesArray = array_keys($arrayData);
	 	
	 //	print_r($fieldsNamesArray);
	 	
	 	$tableFields="";
	 	$fieldValues="";
	 	
	 	$i=0;
	 	
	 	
	 	$current_user_id= F60DALBase::get_current_user_id();
      	$current_time=F60Date::sqlDateTime();
      	$arrayData["when_entered"]="'$current_time'";
      	$arrayData["created_user_id"]="'$current_user_id'";
      
 	  	foreach($fieldsNamesArray as $keyField)		
	  	{
		   	if($keyField!=$primeKeyField && $keyField!="modified_user_id"&&$keyField!="when_modified")
		   	{			   	 	
		   	 	$tableFields = $tableFields.$keyField.",";
		   	 	$fieldValues=$fieldValues.$arrayData[$keyField].",";
			}
			
			$i++;
		}
		$tableFields= substr($tableFields,0,strlen($tableFields)-1);
		$fieldValues= substr($fieldValues,0,strlen($fieldValues)-1);
		
	
		$sql = "Insert into $tableName ($tableFields) values ($fieldValues)";
		
		
		
		return $this->db->execute($sql);
			
	}
	
	function _exeUpdate($tableName, $primeKeyField,$primeValue, $arrayData)
	{
			 	
	 	$fieldsNamesArray = array_keys($arrayData);
	 	
	 //	print_r($fieldsNamesArray);
	 	
	 	$tableFields="";
	 	$fieldValues="";
	 	
	 	$i=0;
	 	
	 	
	 	$current_user_id= F60DALBase::get_current_user_id();
      	$current_time=F60Date::sqlDateTime();
      	$levelDetailData["when_modified"]="'$current_time'";
      	$levelDetailData["modified_user_id"]="'$current_user_id'";
      
      	$updateContent="";
 	  	foreach($fieldsNamesArray as $keyField)		
	  	{
		   	if($keyField!="created_user_id"&&$keyField!="when_entered")
		   	{			   	 	
		   	 	$tableField = $keyField;
		   	 	$fieldValue=$arrayData[$keyField];
		   	 	
		   	 	$updateContent=$updateContent."$tableField=$fieldValue,";
			}
			
			$i++;
		}
		$tableFields= substr($tableFields,0,strlen($tableFields)-1);
		$fieldValues= substr($fieldValues,0,strlen($fieldValues)-1);
		
		$sql = "Update $tableName set($updateContent) where $primeKeyField = $primeKeyValue";
		return $this->db->execute($sql);
			
	}
	
	function deleteCommissionRecords($tableName,$user_id)
	{
		$sql="delete from $tableName where user_id =$user_id";
		
		return $this->db->execute($sql);
	}
}

?>
