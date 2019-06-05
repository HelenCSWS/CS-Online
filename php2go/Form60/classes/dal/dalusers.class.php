<?php

import('Form60.base.F60DALBase');

class dalusers extends F60DALBase
{
	function dalusers()
	{
		$this->table_name = "users";
		$this->field_metadata = array(
				"user_id" => array("mediumint unsigned", true, false, true, false, false),
				"user_level_id" => array("tinyint unsigned", false, false, false, false, true),
				"province_id" => array("tinyint unsigned", false, false, false, false, true),
				"deleted" => array("tinyint", false, false, false, false, true),
				"when_entered" => array("datetime", false, false, false, false, true),
				"when_modified" => array("timestamp", false, true, false, false, false),
				"first_name" => array("varchar", false, false, false, false, false),
				"last_name" => array("varchar", false, true, false, false, false),
				"title" => array("varchar", false, true, false, false, false),
				"department" => array("varchar", false, true, false, false, false),
				"reports_to_id" => array("int unsigned", false, true, false, false, false),
				"birthdate" => array("date", false, true, false, false, false),
				"phone_home" => array("varchar", false, true, false, false, false),
				"phone_cell" => array("varchar", false, true, false, false, false),
				"phone_work" => array("varchar", false, true, false, false, false),
				"phone_other" => array("varchar", false, true, false, false, false),
				"phone_fax" => array("varchar", false, true, false, false, false),
				"phone_pager" => array("varchar", false, true, false, false, false),
				"phone_other1" => array("varchar", false, true, false, false, false),
				"phone_other2" => array("varchar", false, true, false, false, false),
				"email1" => array("varchar", false, false, false, false, false),
				"email2" => array("varchar", false, true, false, false, false),
				"primary_address_unit" => array("varchar", false, true, false, false, false),
				"primary_address_street" => array("varchar", false, true, false, false, false),
				"primary_address_city" => array("varchar", false, true, false, false, false),
				"primary_address_state" => array("varchar", false, true, false, false, false),
				"primary_address_postalcode" => array("varchar", false, true, false, false, false),
				"primary_address_country" => array("varchar", false, true, false, false, false),
				"website1" => array("varchar", false, true, false, false, false),
				"website2" => array("varchar", false, true, false, false, false),
				"username" => array("varchar", false, false, false, false, false),
				"userpass" => array("varchar", false, false, false, false, false),
				"lkup_user_type_id" => array("tinyint", false, false, false, false, false),
				"estate_id" => array("mediumint", false, false, false, false, false)			
			);

		parent::F60DALBase();
	}

	function fill_ids()
	{
		$this->data["user_id"] = $this->db->lastInsertId();

	}
}


class dalusersCollection extends F60DALCollectionBase
{
	function dalusersCollection()
	{
		parent::F60DALCollectionBase();
	}

	function create_singular($row)
	{
		$obj = new dalusers();
		$obj->load_from_list($row);

		return $obj;
	}
}

?>
