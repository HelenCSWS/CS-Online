<?php

import('Form60.base.F60DALBase');

class dalorders extends F60DALBase
{
	function dalorders()
	{
		$this->table_name = "orders";
		$this->field_metadata = array(
				"order_id" => array("int unsigned", true, false, false, false, true),
				"lkup_payment_type_id" => array("tinyint unsigned", false, false, false, false, true),
				"estate_id" => array("int unsigned", false, false, false, false, true),
				"customer_id" => array("int unsigned", false, false, false, false, true),
				"lkup_order_status_id" => array("tinyint unsigned", false, false, false, false, true),
				"invoice_number" => array("varchar", false, false, false, false, false),
				"licensee_number" => array("varchar", false, false, false, false, false),
				"estate_name" => array("varchar", false, false, false, false, false),
				"customer_name" => array("varchar", false, false, false, false, false),
				"customer_address" => array("text", false, false, false, false, false),
				"when_entered" => array("datetime", false, false, false, false, true),
				"when_modified" => array("timestamp", false, true, false, false, false),
				"delivery_date" => array("date", false, false, false, false, true),
				"sst_number" => array("varchar", false, false, false, false, false),
				"GST_factor" => array("float", false, false, false, false, true),
				"agency_LRS_factor" => array("float", false, false, false, false, true),
				"sst" => array("decimal", false, true, false, false, false),
				"adjustment_1" => array("decimal", false, true, false, false, false),
				"adjustment_2" => array("decimal", false, true, false, false, false),
				"created_user_id" => array("mediumint unsigned", false, false, false, false, true),
				"created_by_user_name" => array("varchar", false, false, false, false, false),
				"deposit" => array("decimal", false, true, false, false, false),
				"LDB_payment_type" => array("char", false, true, false, false, false),
				"deleted" => array("tinyint", false, false, false, false, true),
				"lkup_payment_status_id" => array("tinyint", false, false, false, false, true),
                                "modified_user_id" => array("mediumint unsigned", false, false, false, false, true),
                                "estate_number" => array("varchar", false, false, false, false, false),
                                "lkup_store_type_id" => array("tinyint unsigned", false, false, false, false, true),
                                "license_name" => array("varchar", false, false, false, false, false)
			);

		parent::F60DALBase();
	}
        
        function fill_ids()
    	{
            $this->data["order_id"] = $this->db->lastInsertId();

    	}


}
class dalordersCollection extends F60DALCollectionBase
{
	function dalordersCollection()
	{
		parent::F60DALCollectionBase();
	}
	
	function create_singular($row) 
	{ 
		$obj = new dalorders();
		$obj->load_from_list($row);
		
		return $obj;
	}
}

?>