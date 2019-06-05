	<!-- Form60 : template for allocate new wine allocatewine -->

{sale_month}{is_recreate}{users}{store_type}{user_id}{is_update_CaCases}{sale_year}

<table cellpadding="0" cellspacing="0" border="0" width="100%" >

<tr><td align="middle" style="padding-top:100px">
<fieldset style="width:410">
     <legend class="legend" ><b> View report&nbsp;</b></legend>

        <table cellpadding="0" cellspacing="0" border="0" width="370" >

        
        
        <tr><td  nowrap colspan="5" align="left" valign="center" class="label" style="padding-top:25px;padding-bottom:25px">Canadian wines sales in {period_desc}</td></tr>
        
        <tr id="trUser1" style="display:none"><td align="left" width="1%" nowrap class="label" style="padding-top:0px;padding-bottom:25px;padding-right:8px">{user1}</td><td style="padding-top:0px;padding-bottom:25px" width="*" class="label"> {total_cases1} cases</td></tr>
        <tr id="trUser2" style="display:none"><td align="left" width="1%" nowrap class="label" style="padding-top:0px;padding-bottom:25px;padding-right:8px">{user2}</td><td style="padding-top:0px;padding-bottom:25px" width="*" class="label"> {total_cases2} cases</td></tr>
        <tr id="trUser3" style="display:none"><td align="left" width="1%" nowrap class="label" style="padding-top:0px;padding-bottom:25px;padding-right:8px">{user3}</td><td style="padding-top:0px;padding-bottom:25px" width="*" class="label"> {total_cases3} cases</td></tr>
        <tr id="trUser4" style="display:none"><td align="left" width="1%" nowrap class="label" style="padding-top:0px;padding-bottom:25px;padding-right:8px">{user4}</td><td style="padding-top:0px;padding-bottom:25px" width="*" class="label"> {total_cases4} cases</td></tr>
        <tr id="trUser5" style="display:none"><td align="left" width="1%" nowrap class="label" style="padding-top:0px;padding-bottom:25px;padding-right:8px">{user5}</td><td style="padding-top:0px;padding-bottom:25px" width="*" class="label"> {total_cases5} cases</td></tr>

        
        
<tr style="display:none"><td align="left" colspan="5" valign="center" class="label" style="padding-top:0px;padding-bottom:15px"><input type="checkbox" id="chkChange" onclick=setCases() > Change cases</td></tr>

        </table></legend></fieldset></td></tr>
        
        <tr><td align="middle" >
    	<table border="0" width="410"><tr>		<td colspan="5" align="right" style="padding-top:10" >{btnBack}&nbsp;{btnAdd}&nbsp;{btnCancel}</td>
    		</tr></table></td></tr>
</table>

<!-- form is end here-->

