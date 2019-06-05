<?php

import('Form60.base.F60DALBase');

class dalwines extends F60DALBase
{
	function dalwines()
	{
		$this->table_name = "wines";
		$this->field_metadata = array(
				"wine_id" => array("int unsigned", true, false, false, false, true),
				"lkup_bottle_size_id" => array("tinyint unsigned", false, false, false, false, true),
				"lkup_wine_color_type_id" => array("tinyint unsigned", false, false, false, false, true),
				"estate_id" => array("int unsigned", false, false, false, false, true),
				"deleted" => array("tinyint", false, false, false, false, true),
				"allocated" => array("tinyint", false, false, false, false, true),
				"when_entered" => array("datetime", false, false, false, false, true),
				"when_modified" => array("timestamp", false, true, false, false, false),
				"created_user_id" => array("mediumint unsigned", false, false, false, false, true),
				"modified_user_id" => array("mediumint unsigned", false, false, false, false, true),
				"wine_name" => array("varchar", false, false, false, false, false),
				"cspc_code" => array("varchar", false, false, false, false, false),
				"vintage" => array("year", false, false, false, false, true),
				"total_bottles" => array("int unsigned", false, false, false, false, true),
				"bottles_per_case" => array("tinyint unsigned", false, false, false, false, true),
				"price_per_unit" => array("decimal unsigned", false, false, false, false, true),
				"price_winery" => array("decimal unsigned", false, false, false, false, true),
				"profit_per_unit" => array("decimal unsigned", false, false, false, false, true),
				"cost_per_unit" => array("decimal unsigned", false, false, false, false, true),
				"is_international" => array("tinyint unsigned", false, false, false, false, true),
				"case_sold" => array("tinyint unsigned", false, false, false, false, true),
			"case_value" => array("tinyint unsigned", false, false, false, false, true)
			);

		parent::F60DALBase();
	}

	function fill_ids()
	{
		$this->data["wine_id"] = $this->db->lastInsertId();

	}
}

class dalwinesCollection extends F60DALCollectionBase
{
	function dalwinesCollection()
	{
		parent::F60DALCollectionBase();
	}

	function create_singular($row)
	{
		$obj = new dalwines();
		$obj->load_from_list($row);

		return $obj;
	}
}


?>
