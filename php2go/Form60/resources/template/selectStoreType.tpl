	<!-- Form60 : template for allocate new wine allocatewine -->

{period_id}{is_recreate}{store_type}{users}{fiscal_year}{current_user_id}
<table cellpadding="0" cellspacing="0" border="0" width="100%" >

<tr><td align="middle" style="padding-top:100px">

        <table cellpadding="0" cellspacing="0" border="0" width="370" >

        <tr><td nowrap width="1%" align="left" valign="center" class="label" style="padding-right:0px;padding-top:0px">{label_user_id}<br>{user_id}</td></tr>
        
        <tr><td  nowrap width="1%" align="left" valign="center" class="label" style="padding-top:15px;padding-bottom:0px">
        <fieldset style="width:250">
       
             <legend class="legend" ><b> Customer type</b></legend>
            	<table cellpadding="3" cellspacing="0" border="0">
                <tr><td class="label"> <input  type="radio" name="rdoType" id="rdoType" checked onclick=setStoreType(-1)>All store types</td></tr>
                <tr><td class="label"> <input type="radio" name="rdoType" id="rdoType"  onclick=setStoreType(3)> Licensee</td></tr>
                <tr><td class="label"> <input type="radio" name="rdoType" id="rdoType"  onclick=setStoreType(1)> LRS</td></tr>
                <tr><td class="label"> <input  type="radio" name="rdoType" id="rdoType" onclick=setStoreType(2)> Agency</td></tr>

                 </table>
                </legend>
                </fieldset>

                </td></tr>
     

<tr>
    			<td  align="left" style="padding-top:15px">{btnBack}&nbsp;{btnView}&nbsp;{btnCancel}</td>
    		</tr>
        </table>
</td></tr>

</table>

<!-- form is end here-->

