	<!-- Form60 : template used in userAdd.class.php -->
	
	{estate_id}{beer_id}{beer_ids}{delete_id}{is_next}
	
	{new_1}{new_2}{new_mb}
	
<table width="100%" height="60%" border="0" cellpadding="0" cellspacing="0"><!--tb out-->

          <tr><td align="center">
         
<table  border="0" cellpadding="0" cellspacing="0" ><!--tb1-->

          <tr><td colspan="5" align=left >
                <div id="form_client_errors" class="error_style" style="display:none">{error}</div></td>
            </tr>
  
  <tr><td style="padding-top:0px;padding-left:108px" height="50pt">
    	
		 <table width="420px" cellpadding="3" cellspacing="0" border="0"  id="table_basic"><!--tb2-->
                <!-- used for error display -->
                
            
            <tr ><td  style="padding-top:0px;padding-left:0px" >{label_beer_name}</td>
            <!--td style="padding-top:0px;padding-left:8px">{label_vintage}</td-->
            <td style="padding-top:0px;padding-left:7px">{label_bottles_per_case}</td>
            <td style="padding-top:0px;padding-left:7px">{label_bottles_per_pack}</td>
            <td style="padding-top:0px;padding-left:10px" >{label_lkup_beer_type_id}</td>
            <td style="padding-top:0px;padding-left:10px" >{label_lkup_beer_size_id}</td>
            </tr>
				<tr id="tr_basic"><td  style="padding-top:0px;padding-left:0px" >{beer_name}</td>
            <!--td style="padding-top:0px;padding-left:7px">{vintage}</td-->
            <td style="padding-top:0px;padding-left:7px">{bottles_per_case}</td>
            <td style="padding-top:0px;padding-left:7px">{bottles_per_pack}</td>
            <td style="padding-top:0px;padding-left:7px" >{lkup_beer_type_id}</td>
            <td style="padding-top:0px;padding-left:7px" >{lkup_beer_size_id}</td>
             </tr>
				
			</table><!--tb2-->
            
</td></tr>
  <tr><td style="padding-top:0px;padding-left:30px" valign="top">
  <table id="table_1" name="table_1" cellpadding="0" cellspacing="0" border="0"><!--tb3-->
  <tr><td style="padding-top:0px;padding-left:0px" >
  	<table cellpadding="0" cellspacing="0" border="0"><!--tb5-->
  <tr id="tr_info_1">

  <td style="padding-top:10px;padding-left:32px"><input type="checkbox" id="chk_1" name="chk_1" checked></td>
  <td class="label" style="padding-top:10px;padding-left:0px" >BC</td>
  <td style="padding-top:0px;padding-left:8px">{label_cspc_code_1}<SPAN class="label"STYLE="color:#FF0000" id="sp_cspc_code_1">*</SPAN><BR>{cspc_code_1}</td>
  
   <td style="padding-top:0px;padding-left:10px">{label_display_price_1}<SPAN class="label"STYLE="color:#FF0000" id="sp_display_price_1">*</SPAN><BR>{display_price_1}</td>
   <td style="padding-top:0px;padding-left:10px">{label_wholesale_1}<SPAN class="label"STYLE="color:#FF0000" id="sp_wholesale_1">*</SPAN><BR>{wholesale_1}</td>
   <td style="padding-top:0px;padding-left:10px" id="tdCost">{label_cost_1}<SPAN class="label"STYLE="color:#FF0000" id="sp_cost_1">*</SPAN><BR>{cost_1}</td>
   <td style="padding-top:0px;padding-left:10px" id="tdProfit">{label_profit_1}<SPAN class="label"STYLE="color:#FF0000" id="sp_profit_1">*</SPAN><BR>{profit_1}</td>
   
 </tr></table><!--tb4-->
	</td-->
		
   <td ><table cellpadding="0" cellspacing="0" border="0" ><!--tb4-->
			<tr >
    	    <td nowrap style="padding-top:8px;padding-left:10px;padding-bottom:10px">{label_case_sold_1}<BR>{case_sold_1}</td>
      <td style="padding-top:10px;padding-left:3px;padding-bottom:10px" class="label">=</td>
       <td style="padding-top:8px;padding-left:3px;padding-bottom:10px;padding-right:8px;">{label_case_value_1}<BR>{case_value_1}</td>
       <td style="padding-top:8px;padding-left:3px;padding-bottom:10px;padding-right:8px;">&nbsp;<BR>{chkIncludeInStorePenReport}</td>
       
      <td style="padding-top:18px;padding-left:0px;padding-bottom:10px;padding-right:10px;" id="tdDel_1"><input style="font-size:8pt;width=100" type="button" value="Delete province" name="btnDel_1" id="btnDel_1" title="Delete province" onclick=delBeer(1) /></td>
               	</tr></table><!--tb4-->
		</td>
		</tr></td></table>
  </tr>
  <!--alberta-->
  <tr><td style="padding-top:0px;padding-left:30px" >
  <table id="table_2" name="table_2" cellpadding="0" cellspacing="0" border="0"><!--tb3-->
  <tr><td style="padding-top:0px;padding-left:0px" >
  
  <!--Alberta-->
  	<table cellpadding="0" cellspacing="0" border="0"><!--tb5-->
  <tr id="tr_info_2">

  <td style="padding-top:10px;padding-left:32px"><input type="checkbox" id="chk_2" name="chk_2" checked></td>
  <td class="label" style="padding-top:10px;padding-left:0px" >AB</td>
  <td style="padding-top:0px;padding-left:8px">{label_cspc_code_2}<SPAN class="label"STYLE="color:#FF0000" id="sp_cspc_code_2">*</SPAN><BR>{cspc_code_2}</td>
  
   <td style="padding-top:0px;padding-left:10px">{label_display_price_2}<SPAN class="label"STYLE="color:#FF0000" id="sp_display_price_2">*</SPAN><BR>{display_price_2}</td>
   <td style="padding-top:0px;padding-left:10px">{label_wholesale_2}<SPAN class="label"STYLE="color:#FF0000" id="sp_wholesale_2">*</SPAN><BR>{wholesale_2}</td>
   <td style="padding-top:0px;padding-left:10px" id="tdCost">{label_cost_2}<SPAN class="label"STYLE="color:#FF0000" id="sp_cost_2">*</SPAN><BR>{cost_2}</td>
   <td style="padding-top:0px;padding-left:10px" id="tdProfit">{label_profit_2}<SPAN class="label"STYLE="color:#FF0000" id="sp_profit_2">*</SPAN><BR>{profit_2}</td>
   
 </tr></table><!--tb4-->
	</td-->
		
   <td ><table cellpadding="0" cellspacing="0" border="0" ><!--tb4-->
			<tr >
    	    <td nowrap style="padding-top:8px;padding-left:10px;padding-bottom:10px">{label_case_sold_2}<BR>{case_sold_2}</td>
      <td style="padding-top:10px;padding-left:3px;padding-bottom:10px" class="label">=</td>
       <td style="padding-top:8px;padding-left:3px;padding-bottom:10px;padding-right:8px;">{label_case_value_2}<BR>{case_value_2}</td>
       <td style="padding-top:8px;padding-left:3px;padding-bottom:10px;padding-right:8px;">&nbsp;<BR>{chkIncludeInStorePenReport}</td>
       
      <td style="padding-top:18px;padding-left:0px;padding-bottom:10px;padding-right:10px;" id="tdDel_2"><input style="font-size:8pt;width=100" type="button" value="Delete province" name="btnDel_2" id="btnDel_2" title="Delete province" onclick=delBeer(2) /></td></table>

  </tr>
</table>

  <tr >
  	<td nowrap style="padding-top:20px;padding-left:0;padding-right:5px" colspan="20" align="right">
	  <table cellpadding="0" cellspacing="3" border="0" >
  	<tr>
		
		<td  style="padding-top:0px;padding-left:0"  align="right">{btnAdd}</td>
		<td  style="padding-top:0px;padding-left:0" align="right" id="tdAddAnother">{btnAddAnother}</td>
		<td  style="padding-top:0px;padding-left:0"  align="right">{btnCancel}</td>
		
	</tr>
	<tr>
	
		<td   style="padding-top:0px;padding-left:0"  align="right"><div class="CPgridRightLink"><A id="hfPrev" href="javascript:confirmSave(0);">&lt;&lt;Previous</A></td>
		<td  style="padding-top:0px;padding-left:0" align="right"><div class="CPgridRightLink"><A id="hfNext" href="javascript:confirmSave(1);">Next&gt;&gt;</A></td>
		<td  style="padding-top:0px;padding-left:0"  align="right">&nbsp;</td>
		
	</tr>
	
	</table></td>
 </tr>
   </table><!--tb3-->
  </td></tr>
   

</table><!--tb1-->

</td></tr>
   

</table><!--tb out-->



<SCRIPT LANGUAGE="JavaScript" TYPE="text/javascript">
$(document).ready(function(){
 
initBeerPage();

document.getElementById("chk_1").onclick=function(){enableControls(1)};
document.getElementById("chk_2").onclick=function(){enableControls(2)};

document.getElementById("display_price_1").onblur=function(){setPrice(this,1)};
document.getElementById("display_price_2").onblur=function(){setPrice(this,2)};

document.getElementById("wholesale_1").onblur=function(){setPrice(this,1)};
document.getElementById("wholesale_2").onblur=function(){setPrice(this,2)};

document.getElementById("cost_1").onblur=function(){setPrice(this,1)};
document.getElementById("cost_2").onblur=function(){setPrice(this,2)};

document.getElementById("profit_1").onblur=function(){setPrice(this,1)};
document.getElementById("profit_2").onblur=function(){setPrice(this,2)};


document.getElementById("display_price_1").onfocus=function(){removeCurrency(this)};
document.getElementById("display_price_2").onfocus=function(){removeCurrency(this)};

document.getElementById("wholesale_1").onfocus=function(){removeCurrency(this)};
document.getElementById("wholesale_2").onfocus=function(){removeCurrency(this)};

document.getElementById("cost_1").onfocus=function(){removeCurrency(this)};
document.getElementById("cost_2").onfocus=function(){removeCurrency(this)};




})



</SCRIPT>
