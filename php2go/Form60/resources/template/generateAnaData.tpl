	<!-- Form60 : template for allocate new wine allocatewine -->

{is_recreate}{province_id}
<table cellpadding="0" cellspacing="0" border="0" width="100%" >

<tr ><td align="middle" style="padding-top:100px;padding-bottom:10px"  >
<fieldset style="width:255">
     <legend class="legend" ><b> Report date&nbsp;</b></legend>
        <table cellpadding="0" cellspacing="0" border="0" width="255" >

        <tr>
		<td width="*" align="left" style="padding-left:12px;padding-top:10px;padding-bottom:10px" valign="center" class="label">Month<br>{sale_month}</td>
        
       <td width="10%" align="left" style="padding-left:30px;padding-right:10px;padding-top:10px;padding-bottom:10px;" class="label">Year<br>{sale_year}</td></tr>
        <tr><td id="td_msg" colspan="2" align="left" valign="middle" class="label" style="padding-left:12px;padding-top:0px;padding-bottom:15px; color:black"></td></tr>

        </table>
        </legend></fieldset>
</td></tr>

<tr>
     			<td align="middle" style="padding-left:110px;"><input style="font-size:8pt;width=80" type="button" value="Generate" name="btnprint" id="btnprint" title="Generate" onclick=generateASAnaData() /><!-- input type="button" value="Cancel" name="btnClose" style="font-size:8pt;width=80"  id="btnClose" title="Cancel" onclick=closePage() / --></td>
   		</tr>

</table>


<!-- form is end here-->

