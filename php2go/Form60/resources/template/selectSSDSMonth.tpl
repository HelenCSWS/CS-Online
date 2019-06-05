	<!-- Form60 : template for allocate new wine allocatewine -->

{is_recreate}
<table cellpadding="0" cellspacing="0" border="0" width="280" style="margin:auto;" >

<tr ><td align="middle" style="padding-top:100px;padding-bottom:10px"  colspan="2">
<fieldset style="width:255">
     <legend class="legend" ><b> View report&nbsp;</b></legend>
        <table cellpadding="0" cellspacing="0" border="0" width="255" >

        <tr>
		<td width="*" align="left" style="padding-left:12px;padding-top:10px;padding-bottom:20px;" valign="center" class="label">Month<br>{sale_month}</td>
        
       <td width="10%" align="left" style="padding-left:30px;padding-right:10px;padding-top:10px; padding-bottom:20px;" class="label">Year<br>{sale_year}</td></tr>
        <tr><td colspan="2" align="left" valign="middle" class="label" style="padding-left:10px;padding-top:10px;padding-bottom:10px; display:none;"><input type="checkbox" id="recreate_report" >Recreate reports</td></tr>

        </table>
        </legend></fieldset>
</td></tr>

<tr>
     			<td align="right" width="200px" style="padding-left:0px;"><input style="font-size:8pt;width:70px" type="button" value="OK" name="btnprint" id="btnprint" title="OK" onclick=viewReports(1) /></td>
                <td align="right" width="80px" style="padding-left:0px;"><input type="button" value="Cancel" name="btnClose" style="font-size:8pt;width:70px;"  id="btnClose" title="Cancel" onclick=closePage() /></td>
   		</tr>

</table>


<!-- form is end here-->

