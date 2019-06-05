	<!-- Form60 : template for allocate new wine allocatewine -->
        {wine_numbers}{wine_ids}{all_wine_ids}{estate_id_order}
<table   height="100%" cellpadding="0" cellspacing="0" border="0"  style="margin-left:auto; margin-right:auto;">
    <tr id="trAlct" name="trAlct"><td align="center" valign="center">
        <table   cellpadding="0" cellspacing="0" border="0"  >
        <tr id="lgdAltc" name="lgdAltc">
        <td align="left"> <div name="divCm" id="divCm" style="overflow:auto; width:831px;">
             <fieldset >
     <legend class="legend" > Allocate wines to {customer_numbers} customers &nbsp;</legend>
<table    height="100%" cellpadding="0" cellspacing="0" border="0">
        <tr id="lgdchl" name="lgdchl"><td style="padding:5px">{wines_customers}</td></tr></table>    </fieldset>

        </div>

        </td>
        </tr>


        <tr id="trbtn">
        <td colspan="5" algin="right"><table  height="100%" cellpadding="0" cellspacing="0" border="0" ><tr>
            <td width="99%">&nbsp;</td>
        	<td style="padding-top:0px;padding-right:0px;display:none" name="tdfirst" id="tdfirst" align="right" width="1%" style="padding-left:5px"><a href="javascript:getCustomers(0)" style="font-size:8pt">First  </a> </td>
        	<td style="padding-top:0px;padding-right:0px" name="tdpre" id="tdpre"  align="right" width="1%" style="padding-left:5px;display=none"><a href="javascript:getCustomers(2)" style="font-size:8pt"> &lt;&lt;Previous </a> </td>
        	<td style="padding-top:0px;padding-right:0px;display:none" name="tdnext" id="tdnext"  align="right" width="1%" style="font-size:8pt;padding-left:5px"><a href="javascript:getCustomers(1)" style="font-size:8pt">Next>></a></td>
        	<td style="padding-top:0px;padding-right:7px" name="tdlast" id="tdlast"  align="right" width="1%" style="font-size:8pt;padding-left:5px;display=none"><a href="javascript:getCustomers(3);" style="font-size:8pt">Last</a></td>
        	</tr></table>
        </td>
        </tr>
       <tr><td align="left" style="padding-top:10px;padding-right:5px" valign="top">
         <div name="divWine" id="divWine" style="overflow:auto; ">
        <fieldset >
     <legend class="legend" > Allocations and Inventory  &nbsp;</legend>
<table    height="100%" cellpadding="0" cellspacing="0" border="0">
        <tr><td style="padding:5px">{content_wine}</td></tr></table>    </fieldset></div >


        </td></tr>
        
        <tr id="trbtn">
        	<td style="padding-top:10px;padding-right:5px" colspan="5" align="right" >{btnSave}&nbsp;{btnCancel}</td>
        </tr>
        </table>
        </td>
    </tr>

    <TR id="trNoCm" name="trNoCm" style="display:none">
      <TD width="100%" height="100%">
      <table cellpadding="0" cellspacing="0" border="0" width="100%" height="100%">
<TR >
      <TD ALIGN='center' valign="middle" >

      Nothing was found matching your search criteria, please try again.<BR><BR>
    <INPUT TYPE='button' NAME='btn_back'  VALUE='  Back  ' TITLE='Back' onClick="history.go(-1)">  </TD>
 </TR>

</table></td>
 </TR>
</table>
	{estate_id}{wine_ids}{customers}{pageid}{isNoCm}{sql_select}{sql_where}{current_page}{total_pages}{cm_name}{status}{record_counts}{isCurrentSave}{customer_id}{isNoCm}

<!-- form is end here-->

