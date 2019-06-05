<?php

import('Form60.base.F60DALBase');

class dalestates extends F60DALBase
{
	function dalestates()
	{
		$this->table_name = "estates";
		$this->field_metadata = array(
				"estate_id" => array("int unsigned", true, false, true, false, false),
				"when_entered" => array("datetime", false, false, false, false, true),
				"when_modified" => array("timestamp", false, true, false, false, false),
				"created_user_id" => array("mediumint unsigned", false, false, false, false, true),
				"modified_user_id" => array("mediumint unsigned", false, false, false, false, true),
				"estate_name" => array("varchar", false, false, false, false, false),
				"estate_number" => array("varchar", false, false, false, false, false),
				"phone_office1" => array("varchar", false, false, false, false, false),
				"phone_office2" => array("varchar", false, true, false, false, false),
				"phone_fax" => array("varchar", false, true, false, false, false),
				"phone_other1" => array("varchar", false, true, false, false, false),
				"phone_other2" => array("varchar", false, true, false, false, false),
				"lkup_phone_type_id" => array("tinyint unsigned", false, false, false, false, true),
				"po_box" => array("varchar", false, true, false, false, false),
				"ext_no" => array("varchar", false, true, false, false, false),
				"billing_address_unit" => array("varchar", false, true, false, false, false),
				"billing_address_street_number" => array("varchar", false, false, false, false, false),
				"billing_address_street" => array("varchar", false, false, false, false, false),
				"billing_address_city" => array("varchar", false, false, false, false, true),
				"billing_address_state" => array("varchar", false, false, false, false, true),
				"billing_address_postalcode" => array("varchar", false, true, false, false, false),
				"billing_address_country" => array("varchar", false, true, false, false, false),
				"email1" => array("varchar", false, true, false, false, false),
				"email2" => array("varchar", false, true, false, false, false),
				"website1" => array("varchar", false, true, false, false, false),
				"website2" => array("varchar", false, true, false, false, false),
				"payment_info" => array("text", false, true, false, false, false),
				"deleted" => array("tinyint", false, false, false, false, true),
				"is_international" => array("tinyint", false, false, false, false, true),
				"is_fob" => array("tinyint", false, false, false, false, true),
				"invoice_prefix" => array("varchar", false, true, false, false, false),
				"next_invoice_no" => array("int unsigned", false, true, false, false, true),
				"user_id" => array("int unsigned", false, true, false, false, false)
			);

		parent::F60DALBase();
	}

	function fill_ids()
	{
		$this->data["estate_id"] = $this->db->lastInsertId();
		
	}
}


class dalestatesCollection extends F60DALCollectionBase
{
	function dalestatesCollection()
	{
		parent::F60DALCollectionBase();
	}
	
	function create_singular($row) 
	{ 
		$obj = new dalestates();
		$obj->load_from_list($row);
		
		return $obj;
	}
}

?>
