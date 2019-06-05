<script language="JavaScript1.2" TYPE="TEXT/JAVASCRIPT">


	function MouseOver(oImg,strLink,imgID)
	{
			oImg.src = "resources/images/apply-on_update.gif";
	}
	function MouseLeave(oImg,strLink,imgID)
	{
			oImg.src = "resources/images/apply_update.gif";
	}
	
	function MouseDown(oImg,strLink,imgID)
	{
	 	//	alert("herer");
			oImg.src = "resources/images/apply-down_update.gif";
	}
	
	function MouseUp(oImg,strLink,imgID)
	{
			oImg.src = "resources/images/apply-on_update.gif";
	}

</script>

</style>

<table border="0" cellpadding="0" cellspacing="0" ><!--first row-->
<tr><td style="padding-left: 0px; padding-top: 5px" class='label'>

	<table border="0" cellpadding="0" cellspacing="0" ><!--first row-->
	<tr><td style="padding-left: 10px; padding-top: 5px" class='label'>Overdue days</td>
	<td style="padding-left: 10px; padding-top: 5px" class='label'>{overdue_type}</td>
	<td style="padding-left: 10px; padding-top: 5px" class='label'>	Store type </td>
	<td style="padding-left: 10px; padding-top: 5px" class='label'>	{lkup_store_type_id}</td>
	<td class="label" style="padding-left:20px;padding-top:11px; display:block">
	
	<img style="CURSOR: hand;" onclick="refreshOverdueList(true)" style="border:0" src="resources/images/apply_update.gif" onmouseover="MouseOver(this)" onmouseout="MouseLeave(this)" onmousedown="MouseDown(this)" onmouseup="MouseUp(this)">
	
	
	</td>
	
	</tr>
	
	
	<!--second row-->
	<tr><td style="padding-left: 10px; padding-top: 5px" class='label'>Estate </td>
	<td style="padding-left: 10px; padding-top: 5px" class='label'>{estate_id}</td>
	<td style="padding-left: 10px; padding-top: 5px" class='label'>Assigned to</td>
	
	<td style="padding-left: 10px; padding-top: 5px" class='label'>{user_id}</td>
	<td>&nbsp;</td>
	</tr>
	
	</table >

</td>
</tr><tr><td style="padding: 10px;" class='label'>
<div id = "f60OverdueList">

{inovice_list}
</div>
</td></tr>


<tr><td style="padding:5px;" class='label' align="right">
<input type="button" value="Back" name="back2Overdue" id="back2Overdue" title="Go back to overdue" onclick="back2Report(7)" class="label" style="width:100px">
</td></tr>

</table >  
<!-- 
 -->

