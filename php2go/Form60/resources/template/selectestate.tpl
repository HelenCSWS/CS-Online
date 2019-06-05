	<!-- Form60 : template used in userAdd.class.php -->
	{pageid}{search_id}{search_key}
	
<table height="10%" border="0" style="margin-left:auto; margin-right:auto;" ><tr id="trEstate" name="trEstate"><td style="padding-top:100px"id="trEstate" name="trEstate" align="middle" valign="center">
     <fieldset >
     <legend class="legend" ><b> Select <span id="legen_title">estate </span></b></legend>
    	<table cellpadding="3" cellspacing="0" border="0">
                <!-- used for error display -->
     		<tr>
    			<td style="padding:10px" id="td_country">{label_country}<br>{country}</td>
        	
    		</tr>
      		<tr>
    			<td style="padding:10px" name="estates" id="estates">{label_estate_id}<br>{estate_id}</td>
        		<td style="padding:10px; display:none" name="noestates" id="noestates" width="380" height="80" >NO estate!Please add an estate first.</td></tr>
         </table>
        </legend>
        </fieldset>

        </td></tr>
        

        <tr id="estate_btns" name="estate_btns"><td>
      <table width="100%" border="0" height="*" valgin="top">
     		<tr>
    			<td align="right" style="padding-top:18px;padding-right:0px" id="estate_btns" >{btnNext}&nbsp;{btnCancel}</td>
    		</tr>
        </table>
    </td></tr>


<TR id="trNoEstate" name="trNoEstate" style="display:none;">
      <TD width="100%" height="100%" style="padding-top:250px">
      <table cellpadding="0" cellspacing="0" border="0" width="100%" height="100%">
<TR >
      <TD ALIGN='center' valign="middle" >

      Nothing was found matching your search criteria, please try again.<BR><BR>
    <INPUT TYPE='button' NAME='btn_back'  VALUE='  Back  ' TITLE='Back' onClick="history.go(-1)">  </TD>
 </TR>

</table>



 <!-- location table end here-->
