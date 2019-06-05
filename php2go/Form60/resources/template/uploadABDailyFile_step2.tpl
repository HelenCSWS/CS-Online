<!-- Form60 : template used in uploadSSDS.class.php -->
{pageid}
{upload_step}
{uploaded_file}
<style>


</style>


<input type="hidden" name="page_name" value="uploadABDailyFile" />
<input type="hidden" name="MAX_FILE_SIZE" value="8000000" />

<table cellpadding="0" cellspacing="0" border="0" style="width:100%">
<tr><td align="center" valign="middle" style="padding-top:100" >
    <!-- used for error display -->
    <table class="" border="0" style="maring:auto;">
    <tr><td align=left>
        <div id="form_client_errors" class="error_style" style="display:none">{error}</div></td>
        <div id="file_format_errors" class="error_style" style="font-weight:bold;">{file_format_error}</div></td>
    </tr>
    <tr valign="middle" ><td align="left" style="padding-top:25px;padding-bottom:2px;padding-left:0px">Click Upload button to create report.
        </td>
    </tr>
  
   
    <tr id="tr_msg_ab" style="display:none">
        <td align="Left" style="padding-top:2px;padding-bottom:2px;padding-left:0px" >Uploading file</b>:{ab_file_name}
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
        <td colspan=2 style="padding-top:2px;padding-bottom:0px;padding-left:0px;padding-right:0px;" align="left" >{btnBack}&nbsp;{bttnUpload}&nbsp;{btnCancel}</td>
    </tr>
    </table>
    </td></tr>
</table> <!-- location table end here-->

