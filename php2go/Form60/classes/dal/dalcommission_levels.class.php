<?php

import('Form60.base.F60DALBase');

class dalcommission_levels extends F60DALBase
{
	function dalcommission_levels()
	{
		$this->table_name = "commission_levels";
		$this->field_metadata = array(
				"level_id" => array("smallint unsigned", true, false, true, false, false),
				"min_cases" => array("mediumint unsigned", false, false, false, false, true),
				"max_cases" => array("mediumint unsigned", false, false, false, false, true),
				"commission_rate" => array("float unsigned", false, false, false, false, true),
			
				"caption" => array("varchar", false, false, false, false, false),
				"bonus" => array("decimal unsigned", false, false, false, false, true),
				"min_intl_cases" => array("smallint unsigned", false, false, false, false, true),
				"min_canadian_cases" => array("smallint unsigned", false, false, false, false, true),
				
				"is_float" => array("tinyint unsigned", false, false, true, false, false),
				"lkup_store_type_id" => array("smallint unsigned", false, false, true, false, false),
				"target_price" => array("mediumint unsigned", false, false, true, false, false),
				
				"when_entered" => array("datetime", false, false, false, false, true),
				"when_modified" => array("timestamp", false, true, false, false, false),
				"created_user_id" => array("mediumint unsigned", false, false, false, false, true),
				"modified_user_id" => array("mediumint unsigned", false, false, false, false, true),
			);

		parent::F60DALBase();
	}

	function fill_ids()
	{
		$this->data["level_id"] = $this->db->lastInsertId();
		
	}
}


class dalcommission_levelsCollection extends F60DALCollectionBase
{
	function dalcommission_levelsCollection()
	{
		parent::F60DALCollectionBase();
	}
	
	function create_singular($row) 
	{ 
		$obj = new dalcommission_levels();
		$obj->load_from_list($row);
		
		return $obj;
	}
}

?>
