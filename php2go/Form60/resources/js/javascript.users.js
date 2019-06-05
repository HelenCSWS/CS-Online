	function $(id)
	{
		return document.getElementById(id);
	}

	function setUserType()
	{
	 	
		if($("is_wc").checked==true)
			$("lkup_user_type_id").value=1;
		else
			$("lkup_user_type_id").value=0;
			
	}
	
	function initUserPage()
	{
	 	if($("user_level_id").value ==5)
	 		$("div_user_type").style.display="none";
	 	else
	 	{
			if($("lkup_user_type_id").value==1)
				$("is_wc").checked=true;
			else
				$("is_wc").checked=false;
		}

        $("first_name").focus();
        changesectlevel();

	}
	
	//------------------------------------------------------------------------------
// Add User page: when change userlevel the description changes
//------------------------------------------------------------------------------
function changesectlevel()
{
    var level_id;
    var blockId;
	level_id = $("user_level_id").value;
	blockId= $("blockid").value;
	$("level"+blockId).style.display="none";
	$("blockid").value=level_id;
	$("level"+level_id).style.display="block";

    if(level_id==5)
    {
		$("tdEmail").style.display="none";	
		$("tdEstate").style.display="block";	
		$("province_id").value=1;
	
		$("div_user_type").style.display="none";
	 }
	else
	{
		$("tdEmail").style.display="table-cell";	
		$("tdEstate").style.display="none";
		$("div_user_type").style.display="block";
	
		
	}
	 if(level_id==1)
    	$("province_id").value=0;
    	
   
}