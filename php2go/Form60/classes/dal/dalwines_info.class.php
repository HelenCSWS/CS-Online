<?php

import('Form60.base.F60DALBase');

class dalwines_info extends F60DALBase
{
	function dalwines_info()
	{
		$this->table_name = "wines_info";
		$this->field_metadata = array(
				"wine_info_id" => array("int unsigned", true, false, false, false, true),
				"wine_id" => array("int unsigned", true, false, false, false, true),
				"province_id" => array("int unsigned", true, false, false, false, true),
				"deleted" => array("tinyint", false, false, false, false, true),
				"when_entered" => array("datetime", false, false, false, false, true),
				"when_modified" => array("timestamp", false, true, false, false, false),
				"created_user_id" => array("mediumint unsigned", false, false, false, false, true),
				"modified_user_id" => array("mediumint unsigned", false, false, false, false, true),
				"cspc_code" => array("varchar", false, false, false, false, false),
				"price_per_unit" => array("decimal unsigned", false, false, false, false, true),
				"price_winery" => array("decimal unsigned", false, false, false, false, true),
				"profit_per_unit" => array("decimal unsigned", false, false, false, false, true),
				"cost_per_unit" => array("decimal unsigned", false, false, false, false, true),
				"case_sold" => array("tinyint unsigned", false, false, false, false, true),
  				"case_value" => array("tinyint unsigned", false, false, false, false, true)
			);

		parent::F60DALBase();
	}

	function fill_ids()
	{
		$this->data["wine_info_id"] = $this->db->lastInsertId();

	}
}

class dalwines_infoCollection extends F60DALCollectionBase
{
	function dalwines_infoCollection()
	{
		parent::F60DALCollectionBase();
	}

	function create_singular($row)
	{
		$obj = new dalwines_info();
		$obj->load_from_list($row);

		return $obj;
	}
}


?>
