	<!-- Form60 : template used in userAdd.class.php -->
	
{wine_ids}{unwine_ids}{pageid}{customer_id}{isWine}{is_international}{search_key}{search_id}
<table height="100%" border="0" style="margin-left:auto; margin-right:auto;" >
<tr id="showWine" name="showWine" style="display:none"><td style="padding-top:60px">
        <table height="100%" border="0" ><tr><td align="middle" valign="top" style="padding-top:20px">

             <fieldset >
             <legend class="legend" ><b> Select  {product_name}&nbsp;</b></legend>
            	<table cellpadding="3" cellspacing="0" border="0">
                        <!-- used for error display -->{indexs}{estate_id}
              		<tr>
            			<td style="padding:10px">{wine_id}</td>

            		</tr>
            		

                 </table>
                </legend>
                </fieldset>
              <table width="100%" border="0">
             		<tr>
            			<td width="*" id="tdAllocate" name="tdAllocate" align="right" style="padding-top:18px;display:none">{btnAlctAll}&nbsp;{btnAllocate}</td>
            			<td width="*" id="tdWine" name="tdWine" align="right" style="padding-top:18px;display:none"><input style="font-size:8pt;width=100" type="button" value="Add new" name="btnNew" id="btnNew" title="Add new" onclick=openWine(0) />&nbsp;<input type="button" value="Update" name="btnUpdate" style="font-size:8pt;width=100"  id="btnUpdate" title="Update wine" onclick=openWine(1) />
							</td>
            			<td  align="right" style="width:50px;padding-top:18px;padding-right:0px">{btnClose}</td>
            		</tr>
                </table>
         </td></tr>


        </table>
</tr></td>
<TR id="trNowine" name="trNowine" style="display:none">
      <TD width="100%" height="100%">
      <table cellpadding="0" cellspacing="0" border="0" width="100%" height="100%">
<TR >
      <TD ALIGN='center' valign="middle" >

      Nothing was found matching your search criteria, please try again.<BR><BR>
    <INPUT TYPE='button' NAME='btn_back'  VALUE='  Back  ' TITLE='Back' onClick="history.go(-1)">  </TD>
 </TR>

</table></td>
 </TR></table>
<!-- location table end here-->
