<?php

import('Form60.base.F60DALBase');

class dallkup_store_types extends F60DALBase
{
	function dallkup_store_types()
	{
		$this->table_name = "lkup_store_types";
		$this->field_metadata = array(
				"lkup_store_type_id" => array("tinyint unsigned", true, false, true, false, false),
				"display_name" => array("varchar", false, true, false, false, false),
				"caption" => array("varchar", false, false, false, false, false),
				"license_name" => array("varchar", false, true, false, false, false),
				"agency_lrs_factor" => array("float", false, true, false, false, false),
				"gst_factor" => array("float", false, true, false, false, false)
			);

		parent::F60DALBase();
	}

	function fill_ids()
	{
		$this->data["lkup_store_type_id"] = $this->db->lastInsertId();
		
	}
}


class dallkup_store_typesCollection extends F60DALCollectionBase
{
	function dallkup_store_typesCollection()
	{
		parent::F60DALCollection();
	}
	
	function create_singular($row) 
	{ 
		$obj = new dallkup_store_types();
		$obj->load_from_list($row);
		
		return $obj;
	}
}

?>