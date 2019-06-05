<?php

import('Form60.base.F60DALBase');

class dalorderitems extends F60DALBase
{
	function dalorderitems()
	{
		$this->table_name = "order_items";
		$this->field_metadata = array(
				"order_item_id" => array("int unsigned", true, false, false, false, true),
				"order_id" => array("int unsigned", false, false, false, false, true),
				"wine_id" => array("int unsigned", false, false, false, false, true),
				"ordered_quantity" => array("smallint unsigned", false, false, false, false, true),
				"wine_vintage" => array("year", false, false, false, false, true),
				"wine_name" => array("varchar", false, false, false, false, false),
				"supplied_quantity" => array("smallint unsigned", false, false, false, false, true),
				"cspc_code" => array("varchar", false, true, false, false, false),
				"price_per_unit" => array("decimal", false, false, false, false, true),
				"litter_deposit" => array("float", false, false, false, false, true),
				"when_entered" => array("datetime", false, false, false, false, true),
				"when_modified" => array("timestamp", false, true, false, false, false),
				"created_user_id" => array("mediumint unsigned", false, false, false, false, true),
				"modified_user_id" => array("mediumint unsigned", false, false, false, false, true),
				"deleted" => array("tinyint", false, false, false, false, true),
            "price_winery" => array("decimal", false, true, false, false, false),
            "profit" => array("float", false, true, false, false, false)
			);

		parent::F60DALBase();
	}


        function fill_ids()
    	{
            $this->data["order_item_id"] = $this->db->lastInsertId();

    	}
}

class dalorderitemsCollection extends F60DALCollectionBase
{
	function dalorderitemsCollection()
	{
		parent::F60DALCollectionBase();
	}
	
	function create_singular($row) 
	{ 
		$obj = new dalorderitems();
		$obj->load_from_list($row);
		
		return $obj;
	}
}

?>