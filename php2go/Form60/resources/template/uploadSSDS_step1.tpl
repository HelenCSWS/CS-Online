<!-- Form60 : template used in uploadSSDS.class.php -->
{pageid}
{SSDS_step}{pro_id}
{uploaded_file}{bcldb_uploaded_file}
<input type="hidden" name="page_name" value="uploadSSDS" />
<input type="hidden" name="MAX_FILE_SIZE" value="8000000" />
<table cellpadding="0" cellspacing="0" border="0" style="margin-left:auto; margin-right:auto;">
<tr><td align="center" valign="middle" style="padding-top:120" >
    <!-- used for error display -->
    <table>
    <tr><td align=left >
        <div id="form_client_errors" class="error_style" style="display:none">{error}</div></td>
        <div id="file_format_errors" class="error_style" style="font-weight:bold;">{file_format_error}</div></td>
    </tr>
    <tr valign="middle"  i>
        <td class="label" align="" style="padding-top:25px;padding-bottom:5px;padding-left:0px" >
        	Select Province<br>{province_id}
        </td>
    </tr>  
	 <tr valign="middle"  id="tr_bcldb" name="tr_bcldb">
        <td class="label" align="" style="padding-top:25px;padding-bottom:5px;padding-left:0px" >
        	Please select the files for uploading: <br><br>
            {label_bcldb_file_name}<SPAN STYLE="color:#FF0000">*</SPAN><br>{bcldb_file_name}
        </td>
    </tr>
  <tr valign="middle" id="tr_license" name="tr_licensee">
        <td align="" style="padding-top:10px;padding-bottom:5px;padding-left:0px" >
            {label_file_name}<SPAN STYLE="color:#FF0000">*</SPAN><br>{file_name}
        </td>
    </tr>
   <tr>
   
    <tr valign="middle" id="tr_ab" name="tr_ab" >
    <td class="label" align="" style="padding-top:22px;padding-bottom:5px;padding-left:0px" >
        	Please select the files for uploading: <br><br>
            {label_ab_file_name}<SPAN STYLE="color:#FF0000">*</SPAN><br>{ab_file_name}
        </td>
        
    </tr>
   <tr>

        <td id="tdmessage" style="visibility:hidden" class="label" style="padding-top:5px;padding-bottom:0px;padding-left:0px;" align="right">
            <table cellpadding="0" cellspacing="0" width="100%" border="0">
                <tr>
                    <td align="right" class="label">Please wait while the files are uploaded... <img align="top" style="margin-left:10px;" src="resources/images/wait.gif" width="80"></td>
                </tr>
             </table>
        </td>
    </tr>
    <tr>
        <td id="nomessage" height="1" style="padding-top:5px;padding-bottom:0px;padding-left:0px; display:block" class="label">&nbsp;</td>
    </tr>
    <tr>
        <td style="padding-top:0px;padding-bottom:0px;padding-left:0px;padding-right:0px;" align="right" >{bttnStart}&nbsp;{btnCancel}</td>
    </tr>
    </table>
    </td></tr>
</table> <!-- location table end here-->
<script>
document.getElementById("file_name").style.width="400px";
document.getElementById("bcldb_file_name").style.width="400px";
document.getElementById("ab_file_name").style.width="400px";

</script>
