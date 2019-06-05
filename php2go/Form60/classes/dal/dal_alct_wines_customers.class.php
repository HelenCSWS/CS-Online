<?php

import('Form60.base.F60DALBase');

class dal_alct_wines_customers extends F60DALBase
{
	function dal_alct_wines_customers()
	{
		$this->table_name = "customer_wine_allocations";
		$this->field_metadata = array(
				"customer_wine_allocation_id" => array("int unsigned", true, false, false, false, true),
				"wine_id" => array("int unsigned", true, false, false, false, true),
				"customer_id" => array("int unsigned", true, false, false, false, true),
				"entered_time" => array("datetime", false, false, false, false, true),
				"when_entered" => array("datetime", false, false, false, false, true),
				"when_modified" => array("datetime", false, false, false, false, true),
				"created_user_id" => array("mediumint unsigned", false, false, false, false, true),
				"modified_user_id" => array("mediumint unsigned", false, false, false, false, true),
				"user_id" => array("mediumint unsigned", false, false, false, false, true),
				"allocated" => array("mediumint unsigned", false, false, false, false, true),
				"sold" => array("mediumint unsigned", false, false, false, false, true),
			);

		parent::F60DALBase();
	}

	function fill_ids()
	{
		$this->data["customer_wine_allocation_id"] = $this->db->lastInsertId();

	}
}

class dal_alct_wines_customersCollection extends F60DALCollectionBase
{
	function dal_alct_wines_customersCollection()
	{
		parent::F60DALCollectionBase();
	}

	function create_singular($row)
	{
		$obj = new dal_alct_wines_customers();
		$obj->load_from_list($row);

		return $obj;
	}
}


?>
