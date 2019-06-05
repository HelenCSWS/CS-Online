<?php

import('Form60.base.F60DALBase');

class dalwine_delivery_dates extends F60DALBase
{
	function dalwine_delivery_dates()
	{
		$this->table_name = "wine_delivery_dates";
		$this->field_metadata = array(
				"wine_delivery_date_id" => array("int unsigned", true, false, true, false, false),
				"lkup_delivery_statuse_id" => array("tinyint unsigned", false, false, false, false, true),
				"wine_id" => array("int unsigned", false, false, false, false, true),
				"delivery_date" => array("date", false, false, false, false, true),
				"when_entered" => array("datetime", false, false, false, false, true),
				"when_modified" => array("timestamp", false, true, false, false, false),
				"modified_user_id" => array("mediumint unsigned", false, false, false, false, true),
				"deleted" => array("tinyint", false, false, false, false, true),
				"total_cases" => array("mediumint unsigned", false, false, false, false, true)
			);

		parent::F60DALBase();
	}

	function fill_ids()
	{
		$this->data["wine_delivery_date_id"] = $this->db->lastInsertId();
		
	}
	function add_tables($tablenames)
	{
        $this->table_name = $this->table_name.",".$tablenames;
    }

   
}


class dalwine_delivery_datesCollection extends F60DALCollectionBase
{
	function dalwine_delivery_datesCollection()
	{
		parent::F60DALCollectionBase();
	}
	
	function create_singular($row) 
	{ 
		$obj = new dalwine_delivery_dates();
		$obj->load_from_list($row);
		
		return $obj;
	}
	
   
}

?>
