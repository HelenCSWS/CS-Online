	<!-- Form60 : template used in customeradd.php -->
<table border="0" width="100%"><tr><td align=middle style="padding-top:5px">
     <fieldset >
     <legend class="legend" ><b> Store  information&nbsp;</b></legend>
     {customer_id}{is_primary}{contact_id}{lkup_phone_type_id}{customers_contacts_id_1}{customers_contacts_id_2}{pageid}{estate_id_order}{isorder}{province_id}
     {billing_address_state} 
     {old_lkup_payment_type_id}{old_cc_number}{old_cc_exp_month}{old_cc_exp_year}{cs_order_qty}
    
    	<table cellpadding="0" cellspacing="0" border="0" width="100%" >
                <!-- used for error display -->
                <tr><td colspan="4" align=left >
                    <div id="form_client_errors" class="error_style" style="display:none">{error}</div></td>
                </tr>
        	<tr>
    			<td  width="33%" style="padding-left:3px">{label_customer_name}</b><BR>{customer_name}</td>
    			<td width="*" align="middle"><table><tr><td nowrap>{label_licensee_number}<BR>{licensee_number}</td></tr></table></td>
   				<td width="33%" nowrap align="right" style="padding-right:3px"><table><tr><td>{label_sst_number}<BR>{sst_number}</td></tr></table></td>
    		</tr>
    		<tr>
                  <td  width="33%" style="padding-left:3px">
                    <table cellpadding="0" cellspacing="0" >
                    <tr>
                        <td style="padding-right:3px"><table cellpadding="0" cellspacing="0"><tr><td>{label_lkup_store_type_id}</td></tr><tr><td style="padding-top:1px">{lkup_store_type_id}</td></tr></table></td>
                        <td>{label_billing_address_street_number}<BR>{billing_address_street_number}</td>
                     </tr>
                     </table>
                 </td>
  			<td width="*" align="middle"><table><tr><td>{label_billing_address_street}<BR>{billing_address_street}</td>
              <td>{label_po_box}<BR>{po_box}</td></tr></table></td>
   			<td  width="33%" nowrap align="right" style="padding-right:3px"><table><tr><td>{label_billing_address_city}<BR>{billing_address_city}</td></tr></table></td>
    		</tr>

    		<tr>
                <td  width="33%" style="padding-left:3px">
                    <table cellpadding="0" cellspacing="0" >
                    <tr>
                        <td style="padding-right:3px">{label_cm_province_id}<BR>{cm_province_id}</td>
                        <td>{label_billing_address_postalcode}<BR>{billing_address_postalcode}</td>
                     </tr>
                     </table>
                 </td>
                 <td width="*" align="middle" >
                 <table cellpadding="0" cellspacing="0" border="0" >
                        <tr>
                         <td style="padding-right:3px"><table cellpadding="0" cellspacing="0"><tr><td>{label_lkup_payment_type_id}</td></tr><tr><td style="padding-top:1px">{lkup_payment_type_id}</td></tr></table></td>
<td ><table cellpadding="0" cellspacing="0"><tr>
<td style="display:none" id="showCCno"><LABEL FOR="cc_number" ID="lbl_cc_number" CLASS="label">{label_cc_number}<SPAN STYLE="color:#FF0000">*</SPAN></LABEL></td>
<td style="diplay:block" id="noCCno"><LABEL FOR="cc_number" ID="lbl_cc_number" CLASS="label">{label_cc_number}</LABEL></td>

</tr>

<tr><td style="padding-top:1px">{cc_number}</td>
</tr></table></td>
                     </tr>
                   </table>
                </td>

                <td  width="33%" nowrap align="right" style="padding-right:6px">

  <table cellpadding="0" cellspacing="0" border="0" >
                        <tr>
                         <td style="padding-right:3px">

                         <table cellpadding="0" cellspacing="0" border="0">
                         <tr><td colspan="2"><table cellpadding="0" cellspacing="0" ><tr>
                         <td id="showCCexp"style="display:none;padding-right:0px"><LABEL FOR="cc_exp_month" ID="lbl_cc_exp_month" CLASS="label">{label_cc_exp_month}</LABEL><SPAN STYLE="color:#FF0000">*</SPAN></td>
                         <td id="noCCexp" style="display:block; padding-right:0px;padding-top:3px"><LABEL FOR="cc_exp_month" ID="lbl_cc_exp_month" CLASS="label">{label_cc_exp_month}</LABEL></td>
                         </tr></table>
                        </td>
                         </tr>

                         <tr><td style="padding-top:1px;padding-right:3px">{cc_exp_month}</td>
                         <td style="padding-top:1px">{cc_exp_year}</td></tr></table></td>


                        
<td  style="padding-top:1px;"><table cellpadding="0" cellspacing="0"><tr><td>{label_cc_digit_code}</td></tr><tr><td style="padding-top:1px">{cc_digit_code}</td></tr></table></td>
                     </tr>
                   </table>
                </td>



                </td>
     		</tr>

        	<tr>
    			<td width="33%" style="padding-left:3px" id="tddelivery" >{label_best_time_to_deliver}<BR>{best_time_to_deliver}</td>
    			<td width="33%" style="padding-left:3px" id ="tdrank">{label_rank}<BR>{rank}</td>
    				
    				
    			<td width="*" align="middle"><table ><tr><td id="tdShowMark" style="display:none"><LABEL FOR="lkup_store_priority_id" ID="lbl_lkup_store_priority_id_label" CLASS="label"  >Store priority<SPAN STYLE="color:#FF0000">*</SPAN></LABEL></td>
<td id="tdNoMark" style="diplay:block"><LABEL FOR="lkup_store_priority_id" ID="lbl_lkup_store_priority_id_label" CLASS="label"  >Store priority</LABEL></td>

                </tr><tr><td style="padding-top:0px">{lkup_store_priority_id}</td></tr></table></td>
                
	 			<td  width="33%" nowrap align="right" style="padding-right:6px"><table cellpadding="0" cellspacing="0"><tr>
                 <td id="userBCLDB" style="display:none"><LABEL FOR="user_id" ID="label_user_id" CLASS="label"  >Assign to<SPAN STYLE="color:#FF0000">*</SPAN></LABEL></td>
<td id="userOther" style="diplay:block"><LABEL FOR="user_id" ID="label_user_id" CLASS="label"  >Assign to</LABEL></td>
                 </tr><tr><td style="padding-top:1px">{user_id}</td></tr></table></td>
    		</tr>

    </table>
    </fieldset>
    </td></tr>

    <tr><td style="padding-top:5px" >
    <fieldset >
     <legend class="legend"><b> Contact  information&nbsp;</b></legend>
    	<table cellpadding="0" cellspacing="0" border="0" width="100%" >
        	<tr>
     			<td width="33%" style="padding-left:3px" >{label_first_name}<BR>{first_name}</td>
    			<td width="*" align="middle"><table><tr><td>{label_last_name}<BR>{last_name}</td></tr></table></td>
  				<td width="33%" nowrap align="right" style="padding-right:3px"><table><tr><td>{label_title}<BR>{title}</td></tr></table></td>
     		</tr>
     		

    		<tr>
    			<td width="33%" style="padding-left:3px;" nowrap class="label" >
<table cellpadding="0" cellspacing="0"><tr>
  		<td  style="padding-left:2px;" nowrap class="label" valign="top">{label_phone_office1}<input  type="radio" name="best1" id="best1" checked onclick=selectBest(1)>Best # to contact <BR>{phone_office1}</td><td style="padding-top:7px;padding-left:4px">{label_ext_no}<BR>{ext_no}</td></tr></table >

</td>{phone_work}{phone_cell}
     			<td nowrap width="*" align="middle" ><table><tr><td class="label">{label_phone_other1}&nbsp;<input onclick=selectBest(2) type="radio" name="best1" id="best1"  >Best # to contact <BR>{phone_other1}</td></tr></table></td>

     			<td width="33%" nowrap align="right" style="padding-right:3px" nowrap ><table><tr><td class="label">{label_phone_fax}&nbsp;<input  onclick=selectBest(3) type="radio" name="best1" id="best1"  >Best # to contact <BR>{phone_fax}</td></tr></table></td>
     		</tr>

    		<tr>
    			<td width="33%" style="padding-left:3px" >{label_email1}<BR>{email1}</td>
    			<td width="*" align="middle"><table><tr><td>{label_second_first_name}<BR>{second_first_name}</td></tr></table></td>
   			<td width="33%" nowrap align="right" style="padding-right:3px"><table><tr><td>{label_second_last_name}<BR>{second_last_name}</td></tr></table></td>
    		</tr>

    </table>
    </fieldset></td></tr>

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
     			<td colspan="2" align="right" width="98%">{btnAdd}</td><td width="*" style="padding-right:0px">{btnCancel}</td>
   		</tr>
   	</table>
</td></tr>
</table>


