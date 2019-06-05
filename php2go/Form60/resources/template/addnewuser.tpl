	<!-- Form60 : template used in userAdd.class.php -->
	{pageid}{lkup_user_type_id}
<table width="100%" height="60%" border="0" ><tr><td align="middle" valign="center">
    	<table width="420px" cellpadding="0" cellspacing="0" border="0">
                <!-- used for error display -->
            <tr><td colspan="4" align=left >
                <div id="form_client_errors" class="error_style" style="display:none">{error}</div></td>
            </tr>
            
            <tr><td colspan="4" align=left  id="td_user_type" height="30px;"><div class="label" id="div_user_type"><input type="checkbox" id="is_wc" name="is_wc"onclick=setUserType()>Wine consultant</td>
            
												           
            </tr>
         <tr>
    			<td style="padding-top:10px;padding-left:0px" >{label_first_name}<BR>{first_name}</td>
    			<td style="padding-top:10px;padding-left:10px">{label_last_name}<BR>{last_name}</td>
    			
    			<td style="padding-top:10px;padding-left:10px">{label_phone_cell}<BR>{phone_cell}</td>
    			<td id="tdEmail" colspan="2" style="padding-top:10px;padding-left:10px">{label_email1}<BR>{email1}</td>
    			<td id="tdEstate" style="display:none" colspan="2" style="padding-top:10px;padding-left:10px">{label_estate_id}<BR>{estate_id}</td>
		 	</tr>

    		<tr>
            <td style="padding-top:15px;padding-left:0px">{label_username}<BR>{username}</td>
    			<td style="padding-top:15px;padding-left:10px">{label_userpass}<BR>{userpass}</td>
    			<td nowrap style="padding-top:15px;padding-left:10px">{label_repeatuserpass}<BR>{repeatuserpass}</td>
    			
				 <td style="padding-top:15px;padding-left:10px">{label_user_level_id}<BR>{user_level_id}</td>
				 <td style="padding-top:15px;padding-left:5px">{label_province_id}<BR>{province_id}</td>

    		</tr>

        	<tr ><td colspan="5" style="padding-top:15px; font-size:8pt"><B>User level description</b></td></tr>
    		<tr><td height="30px" width="100%" colspan="5" valign="top">
                <table  width="100%" style="font-size:19pt" cellpadding="0" cellspacing="0" border="0" height="30px"><!--height table-->
            		<tr id="level5" name="level6" style="display:none;">
                        <td colspan="5" valign="top" style="font-size:8pt">This person can only see supplier page.</td>
                    </tr>
                    
						  <tr id="level4" name="level5" style="display:block;" >
                        <td colspan="5" valign="top" style="font-size:8pt">This person can do Data entry and the Assignment of stores to Wine Consultants.</td>
                    </tr>
            		<tr id="level3" name="level3" style="display:none">
                        <td style="font-size:8pt" colspan="5" valign="top">This person can do, Data entry, Allocations and the Assignment of stores to Wine Consultants.</td>
                    </tr>
            		<tr id="level2" name="level2" style="display:none">
                        <td style="font-size:8pt" colspan="5" valign="top">This person can do Data entry, Allocations, Discount overrides and the Assignment of stores to Wine Consultants.</td>
                    </tr>
                        <tr id="level1" name="level1" style="display:none">
                            <td style="font-size:8pt" colspan="5" valign="top">This person can do everything, Data entry, Allocations, Discount overrides, the Assignment of stores to Wine Consultants and create new users in the system.
</td>
                    </tr>
                     <tr><td colspan="5">
                            <input type="hidden" value="4" id="blockid" name="blockid">
                            {user_id}
                            </td>
                     </tr>
                </table><!--height table end here-->

            </td></tr>
    		<tr>
    			<td colspan="5" align="right">{btnAdd}&nbsp;{btnCancel}</td>
    		</tr>
        </table>
</td></tr></table> <!-- location table end here-->
