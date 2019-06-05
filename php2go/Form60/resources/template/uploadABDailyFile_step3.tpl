<!-- Form60 : template used in uploadSSDS.class.php -->
{pageid}{upload_step}

{uploaded_file}{bcldb_uploaded_file}
<input type="hidden" name="page_name" value="uploadSSDS" />
<input type="hidden" name="MAX_FILE_SIZE" value="8000000" />

<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr><td align="center" valign="middle" style="padding-top:100" >
    <!-- used for error display -->
    <table border="0" width="470px" style="margin:auto;">
    <tr><td align=middle >
        <div id="form_client_errors" class="error_style" style="display:none">{error}</div></td>
        <div id="file_format_errors" class="error_style" style="font-weight:bold;">{file_format_error}</div></td>
    </tr>
    <tr >
        <td align="center" colspan=2 style="padding-top:25px;padding-bottom:2px;padding-left:0px" >
            <b>Upload succeeded!</b>
        </td>
    </tr>
    <tr>
        <td align="center" style="padding-top:2px;padding-bottom:2px;padding-left:0px" class="label">
       
        </td>
    </tr>
    <tr styule="display:block">
        <td align="Left" class="error_style" style="padding-top:20px;padding-bottom:2px;padding-left:0px;font-weight:bold;font-color:red" >{miss_data}</td>
    </tr>
    <tr>
        <td id="nomessage" height="20" style="padding-top:2px;padding-bottom:0px;padding-left:0px; display:block" class="label">&nbsp;</td>
    </tr>
    <tr><td style="padding-top:52px;padding-bottom:0px;padding-left:0px;padding-right:0px;" align ="center">
    <table cellpadding="0" cellspacing="0" border="0">
  
        <td style="padding-top:0px;padding-bottom:0px;padding-left:10px;padding-right:0px;"  align ="center">{btnClose}</td>
        </table>
        </td>
    </tr>
    </table>
    </td></tr>
</table> <!-- location table end here-->

