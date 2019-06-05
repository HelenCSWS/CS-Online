<?php

import('Form60.base.F60DALBase');

class dalcustomers_notes extends F60DALBase
{
	function dalcustomers_notes()
	{
		$this->table_name = "customers_notes";
		$this->field_metadata = array(
				"customer_id" => array("int unsigned", true, false, false, false, true),
				"note_id" => array("int unsigned", true, false, false, false, true)
			);

		parent::F60DALBase();
	}

}
class dalcustomers_notesCollection extends F60DALCollectionBase
{
	function dalcustomers_notesCollection()
	{
		parent::F60DALCollection();
	}
	
	function create_singular($row) 
	{ 
		$obj = new dalcustomers_notes();
		$obj->load_from_list($row);
		
		return $obj;
	}
}

?>