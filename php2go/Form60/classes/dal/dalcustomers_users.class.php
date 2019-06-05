<?php

import('Form60.base.F60DALBase');

class dalcustomers_users extends F60DALBase
{
	function dalcustomers_users()
	{
		$this->table_name = "users_customers";
		$this->field_metadata = array(
				"users_customers_id" => array("int unsigned", true, false, true, false, false),
				"customer_id" => array("int unsigned", false, false, false, false, true),
				"user_id" => array("int unsigned", false, false, false, false, true),
				"when_entered" => array("datetime", false, false, false, false, true),
				"when_modified" => array("timestamp", false, true, false, false, false),
				"created_user_id" => array("int unsigned", false, false, false, false, true),
				"modified_user_id" => array("int unsigned", false, false, false, false, true),
				"lkup_store_priority_id" => array("tinyint unsigned", false, true, false, false, false)
			);

		parent::F60DALBase();
	}

	function fill_ids()
	{
		global $data_objects;

		$this->data["users_customers_id"] =  $this->db->lastInsertId();


	}
}

class dalcustomers_usersCollection extends F60DALCollectionBase
{
	function dalcustomers_usersCollection()
	{
		parent::F60DALCollectionBase();
	}

	function create_singular($row)
	{
		$obj = new dalcustomers_users();
		$obj->load_from_list($row);

		return $obj;
	}
}

?>