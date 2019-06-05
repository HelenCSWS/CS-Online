<?php

import('Form60.base.F60DALBase');

class dalcustomers_contacts extends F60DALBase
{
	function dalcustomers_contacts()
	{
		$this->table_name = "customers_contacts";
		$this->field_metadata = array(
				"customers_contacts_id" => array("int unsigned", true, false, true, false, false),
				"customer_id" => array("int unsigned", false, false, false, false, true),
				"contact_id" => array("int unsigned", false, false, false, false, true),
				"is_primary" => array("tinyint", false, true, false, false, true)
			);

		parent::F60DALBase();
	}

	function fill_ids()
	{
		global $data_objects;

		$this->data["customers_contacts_id"] =  $this->db->lastInsertId();

		
	}
}

class dalcustomers_contactsCollection extends F60DALCollectionBase
{
	function dalcustomers_contactsCollection()
	{
		parent::F60DALCollectionBase();
	}
	
	function create_singular($row) 
	{ 
		$obj = new dalcustomers_contacts();
		$obj->load_from_list($row);
		
		return $obj;
	}
}

?>