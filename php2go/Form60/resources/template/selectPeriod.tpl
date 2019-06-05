	<!-- Form60 : template for allocate new wine allocatewine -->

{is_recreate}
<table cellpadding="0" cellspacing="0" border="0" width="100%" >

<tr><td align="middle" style="padding-top:100px;padding-bottom:10px">
<fieldset style="width:460">
     <legend class="legend" ><b> View report&nbsp;</b></legend>
        <table cellpadding="0" cellspacing="0" border="0" width="470" >

        <tr><td nowrap width="1%" align="left" valign="center" class="label" style="padding-left:15px;padding-right:8px;padding-top:25px">SSDS fiscal year</td><td width="*" align="left" colspan="2" style="padding-right:0px;padding-top:25px">{fiscal_year}</td></tr>
        
        <tr><td  nowrap width="1%" align="left" valign="center" class="label" style="padding-left:15px;padding-top:15px;padding-bottom:15px">Available period</td><td width="*" align="left" style="padding-left:0px;padding-top:15px;padding-bottom:15px" valign="center">{period}</td><td nowrap width="250" valign="center" style="padding-left:8px;padding-top:15px;padding-bottom:15px">{period_desc}</td></tr>
        
        <tr><td colspan="3" align="left" valign="center" class="label" style="padding-left:13px;padding-top:0px;padding-bottom:25px"><input type="checkbox" id="recreate_report" >Recreate the reports</td></tr>

        </table>
        </legend></fieldset>
</td></tr>

</table>


 <table width="480"  border="0">
    		<tr>
     			<td colspan="2" align="right" width="98%"><input style="font-size:8pt;width=100" type="button" value="View reports" name="btnprint" id="btnprint" title="View reports" onclick=viewReports(1) /></td><td width="*" style="padding-right:0px"><input type="button" value="Cancel" name="btnClose" style="font-size:8pt;width=100"  id="btnClose" title="Cancel" onclick=closePage() /></td>
   		</tr>
   	</table>

<!-- form is end here-->

