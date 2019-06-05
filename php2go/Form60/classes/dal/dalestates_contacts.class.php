<?php

import('Form60.base.F60DALBase');

class dalestates_contacts extends F60DALBase
{
	function dalestates_contacts()
	{
		$this->table_name = "estates_contacts";
		$this->field_metadata = array(
				"estates_contacts_id" => array("int unsigned", true, false, true, false, false),
				"estate_id" => array("int unsigned", false, false, false, false, true),
				"contact_id" => array("int unsigned", false, false, false, false, true),
				"is_primary" => array("tinyint", false, false, false, false, true)
			);

		parent::F60DALBase();
	}

	function fill_ids()
	{
		$this->data["estates_contacts_id"] =  $this->db->lastInsertId();
	}
}


class dalestates_contactsCollection extends F60DALCollectionBase
{
	function dalestates_contactsCollection()
	{
		parent::F60DALCollectionBase();
	}
	
	function create_singular($row) 
	{ 
		$obj = new dalestates_contacts();
		$obj->load_from_list($row);
		
		return $obj;
	}
}

?>