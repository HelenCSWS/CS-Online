<?php

import('Form60.base.F60DALBase');

class dalestates_notes extends F60DALBase
{
	function dalestates_notes()
	{
		$this->table_name = "estates_notes";
		$this->field_metadata = array(
				"estate_id" => array("int unsigned", true, false, false, false, true),
				"note_id" => array("int unsigned", true, false, false, false, true)
			);

		parent::F60DALBase();
	}

}
class dalestates_notesCollection extends F60DALCollectionBase
{
	function dalestates_notesCollection()
	{
		parent::F60DALCollection();
	}
	
	function create_singular($row) 
	{ 
		$obj = new dalestates_notes();
		$obj->load_from_list($row);
		
		return $obj;
	}
}

?>