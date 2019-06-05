<!-- Form60 : template used in uploadSSDS.class.php -->
{pageid}
{SSDS_step}{province_id}{pro_id}
{uploaded_file}{bcldb_uploaded_file}
<input type="hidden" name="page_name" value="uploadSSDS" />
<input type="hidden" name="MAX_FILE_SIZE" value="8000000" />

<table cellpadding="0" cellspacing="0" border="0" style="margin-left:auto; margin-right:auto;">
<tr><td align="center" valign="middle" style="padding-top:100px" style="margin-left:auto; margin-right:auto;">
    <!-- used for error display -->
    <table border="0">
    <tr><td align=left>
        <div id="form_client_errors" class="error_style" style="display:none">{error}</div></td>
        <div id="file_format_errors" class="error_style" style="font-weight:bold;">{file_format_error}</div></td>
    </tr>
    <tr valign="middle" ><td align="left" style="padding-top:25px;padding-bottom:2px;padding-left:0px">The data you are going to upload is for <b>{month} {year}
        </td>
    </tr>
  
    <tr id="tr_msg_license">
        <td align="Left" style="padding-top:12px;padding-bottom:2px;padding-left:0px" >Customer sales</b>: {SSDS_file_name}
        </td>
    </tr>
    <tr id="tr_msg_bcldb">
        <td align="Left" style="padding-top:2px;padding-bottom:2px;padding-left:0px" >BCLDB sales</b>: {bcldb_SSDS_file_name}
        </td>
    </tr>
    
    <tr id="tr_msg_ab" style="display:none">
        <td align="Left" style="padding-top:2px;padding-bottom:2px;padding-left:0px" >Alberta sales</b>: {ab_SSDS_file_name}
        </td>
    </tr>

   <tr>
        <td id="tdmessage" style="visibility:hidden" class="label" style="padding-top:2px;padding-bottom:0px;padding-left:0px;">
            <table cellpadding="0" cellspacing="0" width="100%">
                <tr>
                    <td align="left" class="label">Importing data... generating summary... <img align="top" style="margin-left:10px;" src="resources/images/wait.gif" width="80"></td>
                </tr>
             </table>
        </td>
    </tr>
    <tr>
        <td id="nomessage" height="20" style="padding-top:2px;padding-bottom:0px;padding-left:0px; display:block" class="label">&nbsp;</td>
    </tr>
    <tr>
        <td colspan=2 style="padding-top:2px;padding-bottom:0px;padding-left:0px;padding-right:0px;" align="right" >{btnBack}&nbsp;{bttnUpload}&nbsp;{btnCancel}</td>
    </tr>
    </table>
    </td></tr>
</table> <!-- location table end here-->

