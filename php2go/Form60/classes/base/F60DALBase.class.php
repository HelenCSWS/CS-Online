<?php

import('php2go.base.Php2Go');
import('php2go.util.TypeUtils');
import('php2go.datetime.Date');
import('Form60.util.F60Date');

// Defined constants for the field_metadata property of 
define('IDX_DATATYPE', 0);
define('IDX_IN_KEY', 1);
define('IDX_IS_NULLABLE', 2);
define('IDX_IS_AUTOKEY', 3);
define('IDX_IS_COMPUTED', 4);
define('IDX_HAS_DEFAULT', 5);


function sql_escape_fieldname($field)
{
    return "" . $field . "";
}

function sql_escape_tablename($table)
{
    return "" . $table . "";
}

function unhtmlspecialchars( $string )
{
    $string = str_replace ( "&amp;", '&', $string );
    $string = str_replace ( "&#039;", "\'", $string );
    $string = str_replace ( "&quot;", '\"', $string );
    $string = str_replace ( "&lt;", '<', $string );
    $string = str_replace ( "&gt;", '>', $string );
    
    return $string;
}

//function sql_escape_value($datatype, $value, $print=false)
function sql_escape_value($datatype, $value)
{
    $datatype = strtolower($datatype);

//	if($print) 	{echo "before:".$value;	}

   /* if (is_null($value) || (null == $value))
    {
	        $value = "NULL";
    }*/  //removed for mysql4 to mysql5 upgrade 
    
    
  // if($print){ echo "after:".$value;}
    $tempValue =$value;
    if (is_null($value) || (null == $value))
    {
     	if($tempValue!==0)
     	{
	        $value = "NULL";
	    }
    }
    else if ($datatype == 'bit')
    {
            $value = ($value ? "1" : "0");
    }
    else if ($datatype == 'decimal')
    {
        $value = TypeUtils::parseFloat($value);
    }
    else if (($datatype == 'datetime') ||
            ($datatype == 'smalldatetime') ||
            ($datatype == 'date'))
    {
        $value = "'" . Date::fromUsToSqlDate($value) . "'"; 
    }
    else if (($datatype == 'year') ||
            ($datatype == 'ntext') ||
            ($datatype == 'nvarchar') ||
            ($datatype == 'nchar') ||
            ($datatype == 'text') ||
            ($datatype == 'varchar') ||
            ($datatype == 'char') )
    {

           $value = htmlentities($value); //prevent script attacks
            if(get_magic_quotes_gpc())
                $value = stripslashes($value);
           $value = "'" . str_replace("'", "''", unhtmlspecialchars($value)) . "'";
    }
    
    return $value;
}

class F60DALBase extends Php2Go
{
	var $table_name = null;
	var $is_new = true;
	var $is_deleted = false;
	var $data = null;
	var $original_data = null;
	
	var $field_metadata = null;
	var $primary_keys = array();
	var $required_for_insert = array();
	
	var $filter = array();
	var $field_name=null;
        
        var $db;
        
	function F60DALBase()
	{
                $this->db = & Db::getInstance();
                $this->db->setFetchMode(ADODB_FETCH_ASSOC);
                
		foreach ($this->field_metadata as $field => $metadata) 
		{
			if ($metadata[IDX_IN_KEY]) 
			{
				array_push($this->primary_keys, $field);
			}
			if (!$metadata[IDX_IS_NULLABLE] && 
				!$metadata[IDX_IS_AUTOKEY] && 
				!$metadata[IDX_IS_COMPUTED] && 
				!$metadata[IDX_HAS_DEFAULT]) 
			{
				array_push($this->required_for_insert, $field);
			}
		}
	}
	
        function get_current_user_id()
        {
            import('php2go.auth.User');
            $currentUser = & User::getInstance();
            return $currentUser->getPropertyValue('user_id');
        }
        
        function get_current_user_level()
        {
            import('php2go.auth.User');
            $currentUser = & User::getInstance();
            return $currentUser->getPropertyValue('user_level_id');
        }
        
        function get_current_province_id()
        {
            import('php2go.auth.User');
            $currentUser = & User::getInstance();
            return $currentUser->getPropertyValue('province_id');
        }
        
        function get_current_user_full_name()
        {
            import('php2go.auth.User');
            $currentUser = & User::getInstance();
            return $currentUser->getPropertyValue('first_name') . ' ' .$currentUser->getPropertyValue('last_name');
        }
        
	function get_data($field)
	{
        if ($this->data!=null)
        {
		  if (array_key_exists($field, $this->data))
		  {
     		return $this->data[$field];
		  }
    		else
       	{
		  	return null;
		  }
        }
        else
            return null;
	}
	
	function set_data($field, $value)
	{
		$this->data[$field] = $value;
	}
	
	function load_from_list($row) 
	{
		if ($row == null) 
		{
			$this->is_new = true;
			$row = array();
		}
		else 
		{
			$this->data = array();
			$this->original_data = array();
	
			foreach ($row as $field => $value)
			{
				$this->data[$field] = $value;
				$this->original_data[$field] = $value;
			}
			$this->is_new = false;
		}
	}
	
	function is_dirty() 
	{
		$returnVal = false;
		foreach ($this->data as $field => $value) 
		
		{
		 	$old_value = $this->original_data[$field];
			if ( $old_value != $value) $returnVal = true;
		}
		
		return $returnVal;
	}
	
	function fill_ids()
	{
		// Needs to be overridden
	}
	
	function clear_filter()
	{
		$this->filter = array();
	}
	
	function add_filter()
	{
		$arg_count = func_num_args();		
		if ($arg_count == 5) 
		{
			// This could be between 2 other fields as opposed to values.
			$field = func_get_arg(0);
			$op1 = strtoupper(func_get_arg(1));
			$value1 = func_get_arg(2);
			$op2 = strtoupper(func_get_arg(3));
			$value2 = func_get_arg(4);
			
			$value1 = $this->sql_escape($field, $value1);
			$value2 = $this->sql_escape($field, $value2);
			$field = sql_escape_fieldname($field);
			
			array_push($this->filter, $field);
			array_push($this->filter, $op1);
			array_push($this->filter, $value1);
			array_push($this->filter, $op2);
			array_push($this->filter, $value2);
		}
		if ($arg_count == 3) 
		{
			//TODO: need to validate fieldname, operator, and escape the value
			$field = func_get_arg(0);
			$op = strtoupper(func_get_arg(1));
			$value = func_get_arg(2);
			
			$value = $this->sql_escape($field, $value);
			$field = sql_escape_fieldname($field);
			
			array_push($this->filter, $field);
			array_push($this->filter, $op);
			array_push($this->filter, $value);
		}
		else if ($arg_count == 1) 
		{
			//TODO: add code to check if it's a supported logical operator
			$op = func_get_arg(0);			
			array_push($this->filter, $op);
		}
		else 
		{
			return false;
		}
	}
	
	function build_where_clause() 
	{
		$sql = "";
		foreach ($this->filter as $token) 
		{
			$sql .= " " . $token;
		}		
		return $sql;
	}
        
    function setPrimaryKeyFilter()
    {
        if (count($this->filter)>0)
            $first = FALSE;
        else
            $first = TRUE;
        foreach ($this->primary_keys as $field)
        {
            if ($first) 
                $first = FALSE;
            else
                $this->add_filter("AND");
            $this->add_filter($field, "=", $this->data[$field]);
        }
    }
	
        function loadByPrimaryKey($keyValues)
        {
                if (is_array($keyValues))
                {
                    foreach ($this->primary_keys as $field)
                    {
                        $this->set_data($field, $keyValues[$field]);
                    }
                }
                else
                {
                    foreach ($this->primary_keys as $field)
                    {
                        $this->set_data($field, $keyValues);
                        break;
                    }
                }
                
                $this->setPrimaryKeyFilter();
                return $this->load();
        }
        
        function loadDataToForm(&$form)
        {
            $fields = $form->getFieldNames();
            foreach($fields as $fieldName)
            {
                if (array_key_exists($fieldName, $this->field_metadata))
                {
                    $field = & $form->getField($fieldName);
                    
                    $fieldValue =$this->get_data($fieldName);
                    if ($field->isA('editablefield') && ($field->getMask()=="DATE"))
                    {
                         $fieldValue = F60Date::get2goDate($fieldValue);
                    }
                    $field->setValue($fieldValue);
                }
            }
        }
              
        function getDataFromForm(&$form)
        {
            $fields = $form->getFieldNames();

            foreach($fields as $fieldName)
            {
                if (array_key_exists($fieldName, $this->field_metadata))
                {
                    $field = & $form->getField($fieldName);
                    $fieldValue =$field->getValue();
                    if ($field->isA('editablefield') && ($field->getMask()=="DATE"))
                    {
                         $fieldValue = F60Date::getsqlDate($fieldValue);
                    }
                    if (array_key_exists($fieldName, $_POST)) 
                        $this->set_data($fieldName, $fieldValue);
                }
            }
        }
        
        
	function load() 
	{
		// If we are loading data, this object is not new.
		$this->is_new = false;

        $sql = "SELECT * FROM ";
        if ($this->field_name != null)
            $sql = "SELECT ".$this->field_name." FROM ";
        
		
		$sql = $sql . sql_escape_tablename($this->table_name);

		// add where clause if any filters where set.
		$where_sql = $this->build_where_clause();
		if ($where_sql != "") 
		{
			$sql .= " WHERE" . $where_sql;
		}
		$sql .= ";";
		

		
		$result = $this->db->query($sql);
		
		
		if (!$result) 
		{
                        PHP2Go::raiseError('SQL error:' . $sql, E_USER_ERROR, __FILE__, __LINE__);
		}
		else
		{
			$row = $result->FetchRow();
			
			if (!$row) 
			{
				// Nothing was returned here, so return false
				return false;
			}
			else
			{
				$this->original_data = $row;
				$this->data = $row;
				
				// Add for database upgrade (mysql4 to mysql5), by Helen Oct 23th,2011
				/*
					timestamp format has changed from yyyymmddhhmmss in mysql4 to yyyy-mm-dd hh:mm:ss in mysql 5.0 
				*/
				if (array_key_exists("when_modified", $this->field_metadata))
                {
                 	
				
                 	$when_modified="'".$this->get_data("when_modified")."'";
                 	
                 	str_replace("''","'",$when_modified); // in some case "'" is already in. update for database upgrade
                    $this->set_data ("when_modified",$when_modified );
                }
			}
		}
		
		return true;
	}

	

	function mark_deleted()
	{
		$this->is_deleted = true;
	}

	function save()
	{
		if (!$this->is_deleted)
		{
			if ($this->is_new)
			{

				return $this->_insert();
			}
			else if ($this->is_dirty())
			{

				return $this->_update();
			}
            else
            {
               return true;
               }
		}
		else if ($this->is_deleted && !$this->is_new)
		{
        
        	return $this->_delete();
		}
	}
	
	function excutiveSQL($sql)
	{
		$first = true;
		$sql_set = "";

		$result = $this->db->query($sql);
		if (!$result)
		{
			PHP2Go::raiseError('SQL error:' . $sql, E_USER_ERROR, __FILE__, __LINE__);
			exit;
		}

		return $result;
		


	}

    function _update()
	{
                $this->save_history();
		$first = true;
		$sql_set = "";
		
                if (count($this->filter)<=0)
                    $this->setPrimaryKeyFilter();
                if (array_key_exists("modified_user_id", $this->field_metadata))
                {
                    $this->set_data ("modified_user_id", $this->get_current_user_id());
                }
				// Add for database upgrade (mysql4 to mysql5), by Helen Oct 23th,2011
		        if (array_key_exists("when_modified", $this->field_metadata))
		        {
		        
		         	
		         		$when_modified=$this->get_data("when_modified");
						str_replace("''","'",$when_modified); // in some case "'" is already in. update for database upgrade
		            
		                $this->set_data ("when_modified",$when_modified );
				}
				
				
				
		
		
		foreach ($this->data as $field => $value) 
		{
			if (array_key_exists($field, $this->field_metadata))
			{			 				
				$org_value = $this->original_data[$field];
				
				if ($org_value != $value)
				{
					$value = $this->sql_escape($field, $value);
					$field = sql_escape_fieldname($field);
					
					if($field=="when_modified")
					{
					 	
						$value = str_replace("''","'",$value);
						
						
					}
					
					if ($first) $first = false;
					else 
					{
						$sql_set .= ", ";
					}
						
					$sql_set .= $field . " = " . $value;
				}
			}
		       	
		
        	
		}
		
			
	
		if ($sql_set != "") 
		{
		// if($this->table_name =="estates_commissions")
		 //	$this->table_name ="estates_commissions0";
		 	
			$sql = "UPDATE " . sql_escape_tablename($this->table_name) . " SET " . $sql_set;
			
			// add where clause if any filters where set.
			$where_sql = $this->build_where_clause();
			if ($where_sql != "") 
			{
				$sql .= " WHERE" . $where_sql;
			}
			$sql .= ";";
			//traceLog("Update SQL: " . $sql);
			$result = $this->db->query($sql);
			if (!$result) 
			{
				PHP2Go::raiseError('SQL error:' . $sql, E_USER_ERROR, __FILE__, __LINE__);
				exit;
			}
			
			return true;
		}
		else
		{
           // print "wrong";
			return false;
		}
	}
	
	function _insert()
	{
		$first = true;
		$sql_fields = "";
		$sql_values = "";
                
                if (array_key_exists("created_user_id", $this->field_metadata))
                {
                    $this->set_data ("created_user_id", $this->get_current_user_id());
                }
                
                if (array_key_exists("modified_user_id", $this->field_metadata))
                {
                    $this->set_data ("modified_user_id", $this->get_current_user_id());
                }
                
                if (array_key_exists("when_entered", $this->field_metadata))
                {
                    $this->set_data ("when_entered", F60Date::sqlDateTime());
                }
                
                if (array_key_exists("when_created", $this->field_metadata))
                {
                    $this->set_data ("when_created", F60Date::sqlDateTime());
                }
		
		foreach ($this->data as $field => $value) 
		{
			if (array_key_exists($field, $this->field_metadata))
			{
                $value = $this->sql_escape($field, $value);
				$field = sql_escape_fieldname($field);
				
				if ($first) $first = false;
				else 
				{
					$sql_fields .= ", ";
					$sql_values .= ", ";
				}
					
				$sql_fields .= $field;
				$sql_values .= $value;
			}
		}
		
		if ($sql_fields != "")
		{
			$sql = "INSERT INTO " . sql_escape_tablename($this->table_name) . " (" . $sql_fields . ") VALUES (" . $sql_values . ");";
			
			$result = $this->db->query($sql);
			if (!$result) 
			{
				PHP2Go::raiseError('SQL error:' . $sql, E_USER_ERROR, __FILE__, __LINE__);
				exit;
			}

			$this->fill_ids();
			return true;
		}
		else
		{
			return false;
		}
	}

	function _delete()
	{
		$first = true;
		$sql_set = "";
		if (count($this->filter)<=0)
                    $this->setPrimaryKeyFilter();
                    
                //if there is definelted field theren do logical delete
                if (array_key_exists("deleted", $this->field_metadata))
                {
                    $this->data["deleted"] = 1;
                    return $this->_update();
                }
                else
                {
                  
                    foreach ($this->data as $field => $value) 
                    {
                           if (array_key_exists($field, $this->field_metadata))
                            {
                                    
                                    $org_value = $this->original_data[$field];
                                    
                                    if ($org_value != $value)
                                    {
                                            $value = $this->sql_escape($field, $value);
                                            $field = sql_escape_fieldname($field);
                                            
                                            if ($first) $first = false;
                                            else 
                                            {
                                                    $sql_set .= ", ";
                                            }
                                                    
                                            $sql_set .= $field . " = " . $value;
                                    }
                            }
                    }
                    
                    $sql = "DELETE FROM " . sql_escape_tablename($this->table_name);
                            
                    // add where clause if any filters where set.
                    $where_sql = $this->build_where_clause();
                    if ($where_sql != "") 
                    {
                            $sql .= " WHERE" . $where_sql;
                    }
                    
                    $sql .= ";";
                    
                    $result = $this->db->query($sql);
                    if (!$result) 
                    {
                            PHP2Go::raiseError('SQL error:' . $sql, E_USER_ERROR, __FILE__, __LINE__);
                            exit;
                    }
                            
                    return true;
                }
	}

	function get_datatype($field)
	{
		return $this->field_metadata[$field][IDX_DATATYPE];
	}
	
	function sql_escape($field, $value)
	{
	 	
		if (array_key_exists($field, $this->field_metadata))
		{
//		if($field=="sold"){ echo "valu: $value";}
	 	

			$datatype = $this->get_datatype($field);
			
//$p = false;
	//		if($field=="sold") {	echo "datatype: $datatype"; 	$p = true;}
	 				
		//	$value = sql_escape_value($datatype, $value, $p);
			$value = sql_escape_value($datatype, $value);
			
		//		if($field=="sold")	 				echo "after convert value=: $value";
	 	

		}
		
		return $value;
	}
        
        function save_history()
        {
            $tables = $this->db->getTables();
            $history_table_name = sql_escape_tablename($this->table_name);
            $history_table_name = substr($history_table_name, 0, -1) . "_history";  
            if (!in_array($history_table_name, $tables))
                return;
            $sql_fields = "";
            $sql_values = "";
            $first = true;
            if (is_array($this->original_data))
            {
                foreach ($this->original_data as $field => $value) 
                {
                    if (array_key_exists($field, $this->field_metadata))
                    {
                        $value = $this->sql_escape($field, $value);
                        $field = sql_escape_fieldname($field);
                        
                        if ($first) $first = false;
                        else 
                        {
                                $sql_fields .= ", ";
                                $sql_values .= ", ";
                        }
                                
                        $sql_fields .= $field;
                        if ($field == "modified_user_id")
                        {
                            $sql_values .= $this->get_current_user_id();
                        }
                        elseif ($field == "when_modified")
                        {
                            $sql_values .= $this->db->date(null, true);
                        }
                        else
                            $sql_values .= $value;
                    }
                }
            }
            if ($sql_fields != "")
            {
                $sql = "INSERT INTO " . sql_escape_tablename($history_table_name) . " (" . $sql_fields . ") VALUES (" . $sql_values . ");";
                
                $result = $this->db->query($sql);
                if (!$result) 
                {
                        PHP2Go::raiseError('SQL error:' . $sql, E_USER_ERROR, __FILE__, __LINE__);
                        exit;
                }
            }
        }
}



class F60DALCollectionBase extends Php2Go
{
	var $table_name = null;
	var $field_name = null;
	var $field_metadata = null;
	
	var $is_loaded = false;
	var $filter = array();
	var $sort = array();
	var $items = array();
        var $db;	
	function F60DALCollectionBase() 
	{
		$this->db = & Db::getInstance();
                $this->db->setFetchMode(ADODB_FETCH_ASSOC);
                
                $singular = $this->create_singular(null);
		
		$this->table_name = $singular->table_name;
		$this->field_metadata = $singular->field_metadata;
	}
	
	// MUST overload this in inheriting class
	function create_singular ($row) { return null; }
	
	function get_count() 
	{
		return count($this->items);
	}
	
	function is_dirty() 
	{
		foreach ($this->items as $obj) 
		{
			if ($obj.is_dirty()) return true;
		}
		return false;
	}
	
	function clear_sort()
	{
		$this->sort = array();
	}
	
	function add_sort()
	{
		$arg_count = func_num_args();
		
		if (($arg_count == 2) || ($arg_count == 1))
		{
			$sortAsc = true;
			if ($arg_count == 2) $sortAsc = func_get_arg(1);

			//TODO: need to validate fieldname
			$field = func_get_arg(0);
			
			$field = sql_escape_fieldname($field);
			
			$this->sort[$field] = ($sortAsc ? "ASC" : "DESC");
		}
		else 
		{
			return false;
		}
	}
	
	function build_sort_clause() 
	{
		$sql = "";
		foreach ($this->sort as $field => $direction) 
		{
			if ($sql != "") $sql .= ",";
			
			$sql .= " " . $field . " " . $direction;
		}
		
		return $sql;
	}
	
	function clear_filter()
	{
		$this->filter = array();
	}
	
	function add_filter()
	{
		$arg_count = func_num_args();
		
		if ($arg_count == 5) 
		{
			// This could be between 2 other fields as opposed to values.
			$field = func_get_arg(0);
			$op1 = strtoupper(func_get_arg(1));
			$value1 = func_get_arg(2);
			$op2 = strtoupper(func_get_arg(3));
			$value2 = func_get_arg(4);
			
			$value1 = $this->sql_escape($field, $value1);
			$value2 = $this->sql_escape($field, $value2);
			$field = sql_escape_fieldname($field);
			
			array_push($this->filter, $field);
			array_push($this->filter, $op1);
			array_push($this->filter, $value1);
			array_push($this->filter, $op2);
			array_push($this->filter, $value2);
		}
		if ($arg_count == 3) 
		{
			//TODO: need to validate fieldname, operator, and escape the value
			$field = func_get_arg(0);
			$op = func_get_arg(1);
			$value = func_get_arg(2);
			
			$value = $this->sql_escape($field, $value);
			$field = sql_escape_fieldname($field);
			
			array_push($this->filter, $field);
			array_push($this->filter, $op);
			array_push($this->filter, $value);
		}
		else if ($arg_count == 1) 
		{
			//TODO: add code to check if it's a supported logical operator
			$op = func_get_arg(0);
			
			array_push($this->filter, $op);
		}
		else 
		{
			return false;
		}
	}
	
	function build_where_clause() 
	{
		$sql = "";
		foreach ($this->filter as $token) 
		{
			$sql .= " " . $token;
		}
		
		return $sql;
	}
	
        function &getByPrimaryKey($keyValues)
        {
                $singular = $this->create_singular(null);
                if ($singular->loadByPrimaryKey($keyValues))
                {
                    $this->items = array(); //clear the collection
                    array_push($this->items, $singular);
                    return $singular;
                }
                return null;
        }
        
	function load() 
	{
            // If we are loading data, this object is not new.
            $this->is_new = false;
		
            $sql = "SELECT * FROM ";
            if ($this->field_name != null)
                $sql = "SELECT ".$this->field_name." FROM ";
    
            $sql = $sql . sql_escape_tablename($this->table_name);

            // add where clause if any filters where set.
            $where_sql = $this->build_where_clause();
            if ($where_sql != "") 
            {
                $sql .= " WHERE" . $where_sql;
            }
            $sort_sql = $this->build_sort_clause();
            if ($sort_sql != "") 
            {
                $sql .= " ORDER BY" . $sort_sql;
            }
            $sql .= ";";
            
            $result = $this->db->query($sql);
            if (!$result) 
            {
                PHP2Go::raiseError('SQL error:' . $sql, E_USER_ERROR, __FILE__, __LINE__);
                exit;
            }
            else
            {
                $rows = $result->GetRows();;
                
                if (!$rows) 
                {
                    // Nothing was returned here, so return false
                    return false;
                }
                else
                {
                        $this->is_loaded = true;
                        foreach ($rows as $row) 
                        {
                            array_push($this->items, $this->create_singular($row));
                        }
                }
            }
		
            return true;
	}
	
	function add_new()
	{
		$obj = $this->create_singular(null);
		array_push($this->items, $obj);
		
		return $obj;
	}
	
	function mark_deleted()
	{
		foreach ($this->items as $obj) 
		{
			$obj->mark_deleted();
		}
	}

	function save()
	{
		foreach ($this->items as $obj)
		{
			$obj->save();
		}
	}

	function get_datatype($field)
	{
		return $this->field_metadata[$field][IDX_DATATYPE];
	}
	
	function sql_escape($field, $value)
	{
		if (array_key_exists($field, $this->field_metadata))
		{
			$datatype = $this->get_datatype($field);
			$value = sql_escape_value($datatype, $value);
		}
		
		return $value;
	}
        
}
?>
