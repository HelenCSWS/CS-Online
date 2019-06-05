<?php

import('Form60.base.F60DALBase');

class dalwine_allocates extends F60DALBase
{
	function dalwine_allocates()
	{
		$this->table_name = "wine_allocations";
		$this->field_metadata = array(
				"wine_id" => array("int unsigned", true, false, false, false, true),
				"entered_time" => array("datetime", false, false, false, false, true),
				"when_entered" => array("datetime", false, false, false, false, true),
				"when_modified" => array("datetime", false, false, false, false, true),
				"created_user_id" => array("mediumint unsigned", false, false, false, false, true),
				"modified_user_id" => array("mediumint unsigned", false, false, false, false, true),
				"user_id" => array("mediumint unsigned", false, false, false, false, true),
				"unallocated" => array("mediumint unsigned", false, false, false, false, true),
				"sample" => array("mediumint unsigned", false, false, false, false, true),
				"buffer" => array("mediumint unsigned", false, false, false, false, true),
				"breakage_corked" => array("mediumint unsigned", false, false, false, false, true),
			);

		parent::F60DALBase();
	}

	function fill_ids()
	{
		$this->data["wine_id"] = $this->db->lastInsertId();

	}
}

class dalwine_allocatesCollection extends F60DALCollectionBase
{
	function dalwine_allocatesCollection()
	{
		parent::F60DALCollectionBase();
	}

	function create_singular($row)
	{
		$obj = new dalwine_allocates();
		$obj->load_from_list($row);

		return $obj;
	}
}


?>
