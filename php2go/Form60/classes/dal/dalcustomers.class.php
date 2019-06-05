<?php

import('Form60.base.F60DALBase');

class dalcustomers extends F60DALBase
{
	function dalcustomers()
	{
		$this->table_name = "customers";
		$this->field_metadata = array(
				"customer_id" => array("int unsigned", true, false, false, false, true),
				"lkup_store_type_id" => array("tinyint unsigned", false, false, false, false, true),
				"lkup_payment_type_id" => array("tinyint unsigned", false, false, false, false, true),
				"lkup_credit_card_type_id" => array("tinyint unsigned", false, false, false, false, true),
				"lkup_phone_type_id" => array("tinyint unsigned", false, false, false, false, true),
                "lkup_territory_id" => array("int unsigned", false, false, false, false, true),
				"when_entered" => array("datetime", false, false, false, false, true),
				"when_modified" => array("timestamp", false, true, false, false, false),
				"created_user_id" => array("mediumint unsigned", false, false, false, false, true),
				"modified_user_id" => array("mediumint unsigned", false, false, false, false, true),
				"customer_name" => array("varchar", false, false, false, false, false),
				"licensee_number" => array("varchar", false, true, false, false, false),
				"sst_number" => array("varchar", false, true, false, false, false),
				"phone_office1" => array("varchar", false, false, false, false, false),
				"phone_office2" => array("varchar", false, true, false, false, false),
				"phone_fax" => array("varchar", false, true, false, false, false),
				"phone_other1" => array("varchar", false, true, false, false, false),
				"phone_other2" => array("varchar", false, true, false, false, false),
				"billing_address_unit" => array("varchar", false, true, false, false, false),
				"billing_address_street_number" => array("varchar", false, false, false, false, false),
				"billing_address_street" => array("varchar", false, false, false, false, false),
				"billing_address_city" => array("varchar", false, false, false, false, true),
				"billing_address_state" => array("varchar", false, false, false, false, true),
				"billing_address_postalcode" => array("varchar", false, true, false, false, false),
				"billing_address_country" => array("varchar", false, true, false, false, false),
				"po_box" => array("varchar", false, false, false, false, false),
				"ext_no" => array("varchar", false, false, false, false, false),
				"email1" => array("varchar", false, true, false, false, false),
				"email2" => array("varchar", false, true, false, false, false),
				"website1" => array("varchar", false, true, false, false, false),
				"website2" => array("varchar", false, true, false, false, false),
				"shipping_address_unit" => array("varchar", false, true, false, false, false),
				"shipping_address_street_number" => array("varchar", false, true, false, false, false),
				"shipping_address_street" => array("varchar", false, true, false, false, false),
				"shipping_address_city" => array("varchar", false, true, false, false, true),
				"shipping_address_state" => array("varchar", false, true, false, false, true),
				"shipping_address_postalcode" => array("varchar", false, true, false, false, false),
				"shipping_address_country" => array("varchar", false, true, false, false, false),
				"best_time_to_deliver" => array("varchar", false, true, false, false, false),
				"cc_number" => array("varchar", false, true, false, false, false),
				"cc_digit_code" => array("varchar", false, true, false, false, false),
				"cc_exp_month" => array("varchar", false, true, false, false, false),
				"cc_exp_year" => array("varchar", false, true, false, false, false),
				"area_no" => array("varchar", false, true, false, false, false),
				"deleted" => array("tinyint", false, false, false, false, true),
   	            "province_id" => array("int", false, true, false, false, true),
				"salt" => array("varchar", false, false, false, false, false)
			);

		parent::F60DALBase();
	}
		function fill_ids()
    	{
    		$this->data["customer_id"] = $this->db->lastInsertId();

    	}
	
}

class dalcustomersCollection extends F60DALCollectionBase
{
	function dalcustomersCollection()
	{
		parent::F60DALCollectionBase();
	}
	
	function create_singular($row) 
	{ 
		$obj = new dalcustomers();
		$obj->load_from_list($row);
		
		return $obj;
	}
}

?>
