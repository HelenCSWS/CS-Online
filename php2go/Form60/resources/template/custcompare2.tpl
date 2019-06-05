	<!-- Form60 : template used in userAdd.class.php -->
	{pageid}
<table width="100%" border="0" ><tr><td style="padding-top:40px;padding-left:160px;">
    	<table width="420px" cellpadding="0" cellspacing="0" border="0" style="margin-left:auto; margin-right:auto;">
                <!-- used for error display -->
            <tr><td colspan="4" align=left >
                <div id="form_client_errors" class="error_style" style="display:none">{error}</div></td>
            </tr>
            <tr>
                <input type="hidden" name="page_name" value="customerCompare" />
                <input type="hidden" name="step" value="2" />
                <input type="hidden" name="file_format" value="2" />
    			<td style="padding-left:0px;display:none" >{label_debug}<BR>{debug}</td>
                {cc_session_id}
    		</tr>
            <tr>
    			<td style="padding-top:25px;padding-left:0px" >{label_compare_type}: {compare_type}</td>
    		</tr>
            <tr>
    			<td style="padding-top:25px;padding-left:0px" >{label_file_name}: {file_name}</td>
    		</tr>
            <tr>
    			<td style="padding-top:25px;padding-left:0px" >{label_file_size}: {file_size}</td>
    		</tr>
            <!--tr>
    			<td style="padding-top:25px;padding-left:0px" >{label_file_records}: {file_records}</td>
    		</tr-->
            <tr>
    			<td style="padding-top:25px;padding-left:0px" >{label_valid_records}: {valid_records}</td>
    		</tr>
            <tr>
    			<td id="msg" class="label" style="padding-top:25px;padding-left:0px" >Click on the Start button to begin the compare process.</td>
    		</tr>
            <tr>
    			<td class="label" style="padding-top:25px;padding-left:0px;">Progress: 
    			<div name="pbar_cont" id="pbar_cont" style="width:418px;height:16px;border:1px solid;margin-top:5px;margin-bottom:5px;"><div name="pbar" id="pbar" align="center" style="background-color:blue;width:0px;height:16px;"></div></div><div>{percentage}</div></td>
    		</tr>

    		<tr>
    			<td colspan="4" align="right">{bttnBack}&nbsp;{bttnAction}&nbsp;&nbsp;{btnCancel}</td>
    		</tr>
        </table>
</td></tr></table> <!-- location table end here-->

