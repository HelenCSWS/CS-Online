	<!-- Form60 : template used in userAdd.class.php -->
	{pageid}
<table width="100%" border="0" ><tr><td style="padding-top:120px;" align="center">
    	<table cellpadding="0" cellspacing="0" border="0" width="380px;">
                <!-- used for error display -->
            <tr><td align=left >
                <div id="form_client_errors" class="error_style" style="display:none">{error}</div></td>
            </tr>
            <tr>
                <input type="hidden" name="page_name" value="customerCompare" />
                <input type="hidden" name="step" value="1" />
                <input type="hidden" name="MAX_FILE_SIZE" value="8000000" />
    			<td style="padding-left:0px;color:red;" >{errmsg}</td>
    		</tr>
            <tr>
    			<td style="padding-top:25px;padding-left:0px" >{label_file_format} {file_format} <a class="label" href="cc_help.html" target="_blank"><img src="resources/images/help.jpg" alt="Help" width="15" height="15" border="0" align="absmiddle"></a></td>
    		</tr>
            <tr>
    			<td style="padding-top:25px;padding-bottom:5px;padding-left:0px" >{label_file_name} {file_name}</td>
    		</tr>
           <tr><td id="tdmessage" style="display:none" class="label" style="padding-top:0px;padding-bottom:0px;padding-left:0px;">
           <table cellpadding="0" cellspacing="0" width="565px;">
                 <tr>
                    			<td class="label">Please wait while the file is being uploaded...</td><td style="padding-right:48px;" align="right"><img src="resources/images/wait.gif" width="80"></td>
            		</tr>
        </table>
        </td>
        </tr>
    		<tr>
    			<td id="nomessage" height="20" style="padding-top:5px;padding-bottom:0px;padding-left:0px; display:block" class="label">&nbsp;</td>
    		</tr>
    		<tr>
    			<td style="padding-top:5px;padding-bottom:0px;padding-left:0px;padding-right:104px;" align="right" >{bttnStart}&nbsp;{btnCancel}</td>
    		</tr>
        </table>
</td></tr></table> <!-- location table end here-->

<script>
document.getElementById("file_name").style.width="400px";

</script>