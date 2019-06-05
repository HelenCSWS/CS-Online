<?php

import('Form60.base.F60DALBase');

class dalnotes extends F60DALBase
{
	function dalnotes()
	{
		$this->table_name = "notes";
		$this->field_metadata = array(
				"note_id" => array("int unsigned", true, false, true, false, false),
				"deleted" => array("tinyint", false, false, false, false, true),
				"when_created" => array("datetime", false, false, false, false, true),
				"when_modified" => array("timestamp", false, true, false, false, false),
				"created_user_id" => array("mediumint unsigned", false, false, false, false, true),
				"modified_user_id" => array("mediumint unsigned", false, false, false, false, true),
				"note_text" => array("text", false, false, false, false, false)
			);

		parent::F60DALBase();
	}

	function fill_ids()
	{
		$this->data["note_id"] =  $this->db->lastInsertId();
		
	}
}


class dalnotesCollection extends F60DALCollectionBase
{
	function dalnotesCollection()
	{
		parent::F60DALCollectionBase();
	}
	
	function create_singular($row) 
	{ 
		$obj = new dalnotes();
		$obj->load_from_list($row);
		
		return $obj;
	}
}

?>