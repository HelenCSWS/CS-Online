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

{current_estate_id}{user_id}{estate}
<table border="0" cellpadding="0" cellspacing="0" width="100%"><!--first row-->

<tr><td style="padding-left: 12px; padding-top: 5px">
<table border="0" cellpadding="0" cellspacing="0" >
	<tr valign="bottom" style="padding-top:10px;">
		<td class="label" style="padding-bottom:0px; display:block; padding-right:10px;" id="tdEstate" valign="bottom">Estate</td> 
		<td class="label" style="padding-left:0px">Search by</td>
		<td style="padding-left:6px" class="label" ><input type="checkbox" id="startwith" name="startwith" 		
			onclick=changeSearchFeild()>Starts with</td>
		<td class="label" style="padding-left:10px;padding-top:0px; display:block">&nbsp;
			</td>
		
	</tr>
	<tr valign="middle" style="padding-top:0px;">
		<td class="label" style="display:block; padding-bottom:0px; padding-right:10px;" id="tdEstateId" >{estate_id} </td> 
		<td class="label" style="padding-left:0px">{search_type}</td>
		<td class="label" style="padding-left:10px;">{search_field}</td>
		<td class="label" style="padding-left:10px;padding-top:0px; display:block">
			<img style="CURSOR: hand;" onclick="getInvoiceList(true)" style="border:0" src="resources/images/apply_update.gif" 
			onmouseover="MouseOver(this)" onmouseout="MouseLeave(this)" onmousedown="MouseDown(this)" onmouseup="MouseUp(this)"></td>
		
	</tr>
</table>
<td></tr>
<tr>
<td colspan="5" style="padding-left:12px;padding-right:12px;padding-top:{topSpace}" id="spList">
<div id = "supplierSalesList">
{info_list}
</div>

	</td></tr>

</table>

