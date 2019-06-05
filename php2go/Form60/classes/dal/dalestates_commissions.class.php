<?php

import('Form60.base.F60DALBase');

class dalestates_commissions extends F60DALBase
{
	function dalestates_commissions()
	{
		$this->table_name = "estates_commissions";
		$this->field_metadata = array(
				"estates_commissions_id" => array("int unsigned", true, false, true, false, false),
				"estate_id" => array("int unsigned", false, false, false, false, true),
				"lkup_commission_types_id" => array("int unsigned", false, false, false, false, true),
				"commission" => array("int", false, false, false, false, true),
				"commission_number_type" => array("tinyint", false, true, false, false, true),
				"discount_rate" => array("float", false, false, false, false, true),
				"agency_lrs_factor" => array("float", false, false, false, false, true),
				"gst_factor" => array("float", false, false, false, false, true),
				"profit_formula" => array("varchar", false, false, false, false, true)
			
			);

		parent::F60DALBase();
	}

	function fill_ids()
	{
		$this->data["estates_commissions_id"] =  $this->db->lastInsertId();
	}
}

class dalestates_commissionsCollection extends F60DALCollectionBase
{
	function dalestates_commissionsCollection()
	{
		parent::F60DALCollectionBase();
	}

	function create_singular($row)
	{
		$obj = new dalestates_commissions();
		$obj->load_from_list($row);

		return $obj;
	}
}

?>