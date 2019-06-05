<?php

import('Form60.base.F60DALBase');

class dalcontacts extends F60DALBase
{
	function dalcontacts()
	{
		$this->table_name = "contacts";
		$this->field_metadata = array(
				"contact_id" => array("int unsigned", true, false, false, false, true),
				"deleted" => array("tinyint", false, false, false, false, true),
				"when_entered" => array("datetime", false, false, false, false, true),
				"when_modified" => array("timestamp", false, true, false, false, false),
				"created_user_id" => array("int unsigned", false, false, false, false, true),
				"modified_user_id" => array("int unsigned", false, false, false, false, true),
				"salutation" => array("varchar", false, true, false, false, true),
				"first_name" => array("varchar", false, false, false, false, false),
				"last_name" => array("varchar", false, true, false, false, false),
				"lead_source" => array("varchar", false, true, false, false, false),
				"title" => array("varchar", false, true, false, false, false),
				"department" => array("varchar", false, true, false, false, false),
				"reports_to_id" => array("int unsigned", false, true, false, false, false),
				"birthdate" => array("date", false, true, false, false, false),
				"email1" => array("varchar", false, true, false, false, false),
				"email2" => array("varchar", false, true, false, false, false),
				"assistant" => array("varchar", false, true, false, false, false),
				"primary_address_unit" => array("varchar", false, true, false, false, false),
				"primary_address_street_number" => array("varchar", false, true, false, false, false),
				"primary_address_street" => array("varchar", false, true, false, false, false),
				"primary_address_city" => array("varchar", false, true, false, false, true),
				"primary_address_state" => array("varchar", false, true, false, false, true),
				"primary_address_postalcode" => array("varchar", false, true, false, false, false),
				"primary_address_country" => array("varchar", false, true, false, false, false),
				"alt_address_unit" => array("varchar", false, true, false, false, false),
				"alt_address_street_number" => array("varchar", false, true, false, false, false),
				"alt_address_street" => array("varchar", false, true, false, false, false),
				"alt_address_city" => array("varchar", false, true, false, false, false),
				"alt_address_state" => array("varchar", false, true, false, false, false),
				"alt_address_postalcode" => array("varchar", false, true, false, false, false),
				"alt_address_country" => array("varchar", false, true, false, false, false),
				"website1" => array("varchar", false, true, false, false, false),
				"website2" => array("varchar", false, true, false, false, false)
			);
			
		parent::F60DALBase();
	}
     function fill_ids()
	{

		$this->data["contact_id"] =  $this->db->lastInsertId();

	}
}

class dalcontactsCollection extends F60DALCollectionBase
{
	function dalcontactsCollection()
	{
		parent::F60DALCollectionBase();
	}
	
	function create_singular($row) 
	{ 
		$obj = new dalcontacts();
		$obj->load_from_list($row);
		
		return $obj;
	}
}

?>
