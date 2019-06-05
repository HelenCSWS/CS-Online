	<!-- Form60 : template used in estateadd.php -->

<table border="0" width="100%"><tr><td align=middle style="padding-top:5px">
<tr><td >
     <fieldset >
     <legend class="legend" ><b> Estate  information&nbsp;</b></legend>
     {estate_id} {lkup_commission_types_id}    {commission} {is_primary}{contact_id}{is_save}{estates_contacts_id_1}{estates_contacts_id_2}
     {cntype_1}{cntype_2}{cntype_3}{cntype_4}{cntype_5}{is_addwine}{estate_commission_id_1}{estate_commission_id_2}{estate_commission_id_3}{estate_commission_id_4}{estate_commission_id_5}{pageid}{is_international}
    	<table cellpadding="3" cellspacing="0" border="0" width="100%" height="100" >
        <!-- used for error display -->
        <tr><td colspan="4" align=left >
            <div id="form_client_errors" class="error_style" style="display:none">{error}</div></td>
        </tr>
    	<tr>
                <td width="40%" valign="top">
                        <table cellpadding="0" cellspacing="0" border="0" width="100%"><tr>
                      			<td   valign="top" >{label_estate_name}</b><BR>{estate_name}</td>
              	     		</tr><tr>
                      			<td   valign="top" style="padding-top:3px">{label_billing_address_street_number}</b><BR>{billing_address_street_number}</td>
              	     		</tr>
               	     		<tr>
                                <td valign="top" s style="padding-top:2px"><table cellpadding="0" cellspacing="0" border="0" >
                                    <tr><td valign="bottom" style="padding-right:3px;" valign="top">{label_billing_address_state}<BR>{billing_address_state}</td><td valign="bottom" valign="top">{label_billing_address_postalcode}<BR>{billing_address_postalcode}</td>
                                     </tr>
                                     </table>
                         </td>
           	     		</tr>
                   </table>
                </td>
                
                <td width="*" valign="top"><table cellpadding="0" cellspacing="0" border="0" height="100%" width="100%"><tr>
                   			<td align="left" valign="top" width="50%" style="padding-top:0px;padding-right:3px">{label_estate_number}<BR>{estate_number}</td>
                   				<td nowrap width="*" valign="top" align="right" style="padding-top:1px"><table  cellpadding="0" cellspacing="0"><tr><td>{label_user_id}</td></tr><tr><td style="padding-top:1px">{user_id}</td></tr></table></td></tr>
                                   <tr><td align="left" width="50%" valign="top" style="padding-top:2px"><table cellpadding="0" cellspacing="0"><tr><td>{label_billing_address_street}<BR>{billing_address_street}</td><td style="padding-left:4px">{label_po_box}<BR>{po_box}</td></tr></table></td>
                   				<td nowrap style="padding-top:2px"width="*" valign="top" align="right"><table cellpadding="0" cellspacing="0"><tr><td >{label_billing_address_city}<BR>{billing_address_city}</td></tr></table></td></tr>
                        <tr> <td colspan="2" width="*" id="tdinfo" name="tdinfo" style="padding-top:2px">{label_payment_info}<BR>{payment_info}</td>
                        </tr>

                    </table>
                </td>
  		</tr>
    </table>
    </fieldset>
    </td></tr>

<!--contact information -->
    <tr><td style="padding-top:5px" colspan="2">
    <fieldset >
     <legend class="legend"><b> Contact  information&nbsp;</b></legend>
    	<table cellpadding="0" cellspacing="0" border="0" width="100%"  height="33%">
	<tr>
<td width="40%"><table cellpadding="0" cellspacing="0" border="0" height="100%" width="100%" ><tr>
                      			<td  style="padding-left:3px">{label_first_name}</b><BR>{first_name}</td></tr>
              	     		<tr><td><table cellpadding="0" cellspacing="0"><tr>
  		<td  style="padding-left:2px;" nowrap class="label" valign="top">{label_phone_office1}<input  type="radio" name="best1" id="best1" checked onclick=selectBest(1)>Best # to contact <BR>{phone_office1}</td><td style="padding-top:7px;padding-left:4px">{label_ext_no}<BR>{ext_no}</td></tr></table ></td></tr>
               	     		
                            <tr>
                     			<td  style="padding-left:3px" valign="top">{label_email1}<BR>{email1}</td>
                           	</tr>
                   </table>
                </td>


                <td width="*" valign="top"> {lkup_phone_type_id}
                        <table cellpadding="0" cellspacing="0" border="0" height="100%" width="100%">
                              <tr>
                                <td align="left" style="padding-right:3px" valign="top"><table cellpadding="0" cellspacing="0"><tr><td>{label_last_name}<BR>{last_name}</td></tr></table></td>
                              	<td nowrap align="right" style="padding-right:0px"><table cellpadding="0" cellspacing="0"><tr><td style="padding-right:2px">{label_title}<BR>{title}</td></tr></table></td>
                              </tr>
                             <tr>
                       			<td nowrap align="left" valign="top"><table cellpadding="0" cellspacing="0"><tr><td class="label">{label_phone_other1}&nbsp;<input type="radio" name="best1" id="best1"  onclick=selectBest(2) >Best # to contact <BR>{phone_other1}</td></tr></table></td>

                			<td  nowrap align="right" style="padding-right:0px" width="*" valign="top"><table cellpadding="0" cellspacing="0"><tr><td class="label" style="padding-right:2px">{label_phone_fax}&nbsp;<input  type="radio" name="best1" id="best1" onclick=selectBest(3) >Best # to contact <BR>{phone_fax}</td></tr></table></td>
                                    </tr>

                            <tr><td  align="left" valign="top"><table cellpadding="0" cellspacing="0"><tr><td >{label_secondary_first_name}<BR>{secondary_first_name}</td></tr></table></td>
           			<td width="*%" nowrap align="right" style="padding-right:0px" valign="top"><table cellpadding="0" cellspacing="0"><tr><td style="padding-right:2px">{label_secondary_last_name}<BR>{secondary_last_name}</td></tr></table></td></tr>
                            </table>
                </td>
  		</tr>
      </table>
    </fieldset></td></tr>
<!--commission amount-->
<tr><td width="*" algin="center">
    <fieldset >
     <legend class="legend"><b> Commission amount&nbsp;</b></legend>
    	<table cellpadding="0" cellspacing="0" border="0" width="100%" height="*">
	<tr> <td  width="40%" ><table cellpadding="0" cellspacing="0"  border="0"><tr>
                         <td  style="padding-left:3px;" nowrap class="label" valign="top">{label_ctype_1}<input  type="radio" name="cntype1" id="cntype1" checked onclick=changeNumberType(1,0)>%<input  type="radio" name="cntype1" id="cntype1"  onclick=changeNumberType(1,1)>$<br>{ctype_1}</td>
 <td  style="padding-left:3px;" nowrap class="label" valign="top">{label_ctype_2}<input  type="radio" name="cntype2" id="cntype2" checked onclick=changeNumberType(2,0)>%<input  type="radio" name="cntype2" id="cntype2" onclick=changeNumberType(2,1)>$<br>{ctype_2}</td>                                             </tr>
                     </table>
                 </td>
                 <td width="*"   valign="top">
                     <table border="0" cellpadding="0" cellspacing="0" width="100%">
                         <tr><td width="50%" valign="top" align="left">
                                            <table cellpadding="0" cellspacing="0" border="0" >
                                                <tr>
<td  style="padding-left:3px;" nowrap class="label" valign="top" align="left">{label_ctype_3}&nbsp;&nbsp;<input  type="radio" name="cntype3" id="cntype3" checked onclick=changeNumberType(3,0)>%<input  type="radio" name="cntype3" id="cntype3"  onclick=changeNumberType(3,1)>$<br>{ctype_3}</td>
 <td  style="padding-left:3px;" nowrap class="label" valign="top" align="left">{label_ctype_4}&nbsp;&nbsp;<input  type="radio" name="cntype4" id="cntype4" checked onclick=changeNumberType(4,0)>%<input  type="radio" name="cntype4" id="cntype4"  onclick=changeNumberType(4,1)>$<br>{ctype_4}</td>
                                                 </tr>
                                             </table>
                             </td>

                                <td  width="*" nowrap align="right" valign="top" style="padding-left:3px">
                                        <table cellpadding="0" cellspacing="0" ><tr>
<td  style="padding-right:2px;" nowrap class="label" valign="top">{label_ctype_5}&nbsp;&nbsp;<input  type="radio" name="cntype5" id="cntype5" checked onclick=changeNumberType(5,0)>%<input  type="radio" name="cntype5" id="cntype5"  onclick=changeNumberType(5,1)>$<br>{ctype_5}</td></table>
                                </td>
                        </tr>
                    </table>
                </td></tr>

    </table>
    </fieldset>


</td>
    </tr>
    
<!--note-->
    <tr><td style="padding-top:1px" align="left" width="100%">
     <!--fieldset -->
         <!--legend class="legend"><b> Notes&nbsp;</b></legend-->
         <div id = "notesList">
            {note_contents}
         </div>
    <!--/fieldset-->

    </td></tr>
    
 <tr><td>
    <table width="100%"  border="0">
    		<tr>
     			<td align="right" width="100%">{btnAdd}</td><td style="padding-right:0px">{btnCancel}</td>
   		</tr>
   	</table>
</td></tr>
</table>


