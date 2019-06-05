	function $(id)
	{
		return document.getElementById(id);
	}
	
	function loadForm()
	{
		setUsersByProvince_id();
	
//		setCommissionTypeByUser();	
		
	}
	
	function setCommissionTypeByUser()
	{
	 
		var user_id = $("user_id").value;
		xajax_setCommissionType(user_id);
			
	}
	
	function setCommissionType($type_id)
	{
		$("org_lkup_sales_commission_type_id").value=$type_id;
	 	if($type_id ==0)
			$type_id=1;
			
		$("lkup_sales_commission_type_id").value=$type_id;	

	}
	
	function setUsersByProvince_id()
	{
		var province_id = $("province_id").value;
		xajax_getUsersByProvince(province_id);
	}
	
	function openPage(pageId)
	{
	 	var user_id =$("user_id").value;
	 	var type_id =$("lkup_sales_commission_type_id").value;
	 	var province_id =$("province_id").value;
		 	 	
		if(pageId ==0)
		{
			
		}
		else if(pageId==1)
		{
			link = "main.php?page_name=salesCommissionLevels&user_id="+user_id+"&lkup_sales_commission_type_id="+type_id+"&province_id="+province_id;
		}
		
		document.location = link;
	    stopEvt();
	}
	
	