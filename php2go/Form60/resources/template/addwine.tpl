	<!-- Form60 : template used in userAdd.class.php -->
	{estate_id}{wine_id}{wine_delivery_date_id}{s_total_bottles}{editMode}{pageid}{delivery_total}{is_international}{cost_per_unit_bc}{profit_per_unit_bc}{price_per_unit_bc}{price_winery_bc}

	{cost_per_unit_ab}{profit_per_unit_ab}{price_per_unit_ab}{price_winery_ab}

	{new_bc}{new_ab}{new_mb}{login_user_id}
<table width="100%" height="60%" border="0" cellpadding="0" cellspacing="0">  <!--tb out-->
  
 <tr><td align="center">
 
    <table  border="0" cellpadding="0" cellspacing="0" ><!--tb1-->
         <tr><td colspan="15" align=left ><div id="form_client_errors" class="error_style" style="display:none">{error}</div></td></tr>  <!-- used for error display -->
          <!-- Basice Wine info-->
          <tr><td style="padding-top:0px;padding-left:45px" height="50pt">
             <table width="420px" cellpadding="3" cellspacing="0" border="0"><!--tb2-->               
                <tr>
                    <td  style="padding-top:0px;padding-left:0px" >{label_wine_name}</td>
                    <td style="padding-top:0px;padding-left:8px">{label_vintage}</td>
                    <td style="padding-top:0px;padding-left:10px">{label_bottles_per_case}</td>
                    <td style="padding-top:0px;padding-left:10px" >{label_lkup_wine_color_type_id}</td>
                    <td style="padding-top:0px;padding-left:10px" >{label_lkup_bottle_size_id}</td>
                </tr>
    			<tr>
                    <td  style="padding-top:0px;padding-left:0px" >{wine_name}</td>
                    <td style="padding-top:0px;padding-left:7px">{vintage}</td>
                    <td style="padding-top:0px;padding-left:7px">{bottles_per_case}</td>
                    <td style="padding-top:0px;padding-left:7px" >{lkup_wine_color_type_id}</td>
                    <td style="padding-top:0px;padding-left:7px" >{lkup_bottle_size_id}</td>
                    
                 </tr>
             </table><!--tb2 end-->
          </td></tr>
          
          <!--BC international Wine info -->
          <tr><td>
                <table cellpadding="0" cellspacing="0" border="0" >
                    <tr>
                        <td style="padding-top:10px;padding-left:0px"><input type="checkbox" id="chk_bc" name="chk_bc"  onclick="checkPro(1)" ></td>
                        <td class="label" style="padding-top:10px;padding-left:0px" >BC</td>
                        <td style="padding-top:0px;padding-left:8px">{label_cspc_code_bc}<SPAN class="label"STYLE="color:#FF0000" id="sp_cspc_code_bc">*</SPAN><BR>{cspc_code_bc}</td>
                        <td style="padding-top:0px;padding-left:10px">{label_display_price_bc}<SPAN class="label"STYLE="color:#FF0000" id="sp_display_price_bc">*</SPAN><BR>{display_price_bc}</td>
                        <td style="padding-top:0px;padding-left:10px">{label_wholesale_bc}<SPAN class="label"STYLE="color:#FF0000" id="sp_wholesale_bc">*</SPAN><BR>{wholesale_bc}</td>
                         <td style="padding-top:0px;padding-left:10px" id="tdCost">{label_cost_bc}<SPAN class="label"STYLE="color:#FF0000" id="sp_cost_bc">*</SPAN><BR>{cost_bc}</td>
                         <!-- display BC (supplier) Wine, hide when international -->
                         <td id="tdCaWine" width="380px">
                           <table  cellpadding="0" cellspacing="0" border="0" width="100%">
                                <tr>
                                    <td width="1%" nowrap style="padding-top:10px;padding-left:10px;padding-bottom:10px">{label_total_cases}<SPAN class="label"STYLE="color:#FF0000" id="sp_total_cases">*</SPAN><BR>{total_cases}</td>
                                    <td width="*" style="padding-top:10px;padding-left:10px;padding-bottom:10px" class="label">{label_total_bottles}<SPAN class="label"STYLE="color:#FF0000" id="sp_total_bottles">*</SPAN><BR>{total_bottles}</td>
                                    <td width="*" style="padding-top:8px;padding-left:10px;padding-bottom:10px" class="label" id="tdDelivery">{label_delivery_date}<SPAN class="label"STYLE="color:#FF0000" id="sp_delivery_date">*</SPAN><BR>{delivery_date}</td>   
                                </tr>
                            </table><!--tb4-->
                       </td>
                       <!-- display BC Wine, hide when international -->
                        <td style="padding-top:0px;padding-left:10px" id="tdProfit">{label_profit_bc}<SPAN class="label"STYLE="color:#FF0000" id="sp_profit_bc">*</SPAN><BR>{profit_bc}</td>
                        <td style="padding-top:8px;padding-left:10px;padding-bottom:10px">{label_case_sold_bc}<BR>{case_sold_bc}</td>
                        <td style="padding-top:10px;padding-left:3px;padding-bottom:10px" class="label">=</td>
                        <td style="padding-top:8px;padding-left:3px;padding-bottom:10px;padding-right:8px;">{label_case_value_bc}<BR>{case_value_bc}</td>
                        <td style="padding-top:8px;padding-left:3px;padding-bottom:10px;padding-right:8px;"></td>     
                        <td style="padding-top:18px;padding-left:0px;padding-bottom:10px;padding-right:10px;" id="tdDel_bc"><input style="font-size:8pt;width=100" type="button" value="Clear content" name="btnDel_bc" id="btnDel_bc" title="Clear content" onclick=delWine(1) /></td>
                  </tr>
                
                
                </table>
          
          </td></tr>
            <!--BC international Wine info End -->
            
            <!--AB international Wine info -->
          <tr>
          <td style="padding-top:0px;padding-left:0px">
                <table cellpadding="0" cellspacing="0" border="0">
                   <tr>
                        <td style="padding-top:10px;padding-left:0px"><input type="checkbox" id="chk_ab" name="chk_ab" onclick="checkPro(2)"></td>
                        <td class="label" style="padding-top:10px;padding-left:0px">AB</td>
                        <td style="padding-top:0px;padding-left:8px">{label_cspc_code_ab}<SPAN class="label" STYLE="color:#FF0000" id="sp_cspc_code_ab">*</SPAN><BR>{cspc_code_ab}</td>
                        <td style="padding-top:0px;padding-left:10px">{label_display_price_ab}<SPAN class="label" STYLE="color:#FF0000" id="sp_display_price_ab">*</SPAN><BR>{display_price_ab}</td>
                        <td style="padding-top:0px;padding-left:10px">{label_wholesale_ab}<SPAN class="label" STYLE="color:#FF0000" id="sp_wholesale_ab">*</SPAN><BR>{wholesale_ab}</td>
                        <td style="padding-top:0px;padding-left:10px">{label_cost_ab}<SPAN class="label" STYLE="color:#FF0000" id="sp_cost_ab">*</SPAN><BR>{cost_ab}</td>
                        <td style="padding-top:0px;padding-left:10px">{label_profit_ab}<SPAN class="label" STYLE="color:#FF0000" id="sp_profit_ab">*</SPAN><BR>{profit_ab}</td>
                        <td>
                            <table cellpadding="0" cellspacing="0" border="0" width="10px">
                                <!--tb4-->
                                <tr>
                                    <td nowrap style="padding-top:8px;padding-left:10px;padding-bottom:10px">{label_case_sold_ab}<BR>{case_sold_ab}</td>
                                    <td style="padding-top:10px;padding-left:3px;padding-bottom:10px" class="label">=</td>
                                    <td style="padding-top:8px;padding-left:3px;padding-bottom:10px;padding-right:8px;">{label_case_value_ab}<BR>{case_value_ab}</td>
                                </tr>
                            </table><!--tb4-->
                        </td>

                        <td style="padding-top:18px;padding-left:0px;padding-bottom:10px;padding-right:10px;" id="tdDel_ab">
                            <input style="font-size:8pt;width=100" type="button" value="Clear content" name="btnDel_ab" id="btnDel_ab" title="Clear content" onclick=delWine(2) />
                        </td>

                    </tr>
                </table>
          </td></tr>
          
           <!--AB international Wine info end-->
           <tr>
    <td nowrap style="padding-top:20px;padding-left:0;padding-right:5px" colspan="20" align="right">
        <table cellpadding="0" cellspacing="3" border="0">
            <tr>
                <!-- td width="620px; display:none;"><a href="" onclick=product4OtherProvince(1) class="cs-link">Other provinces</a></td -->
                <td style="padding-top:0px;padding-left:0;display:none" align="right">{btnAddAnother}</td>
                <td style="padding-top:0px;padding-left:0" align="right">{btnAdd}</td>
                <td style="padding-top:0px;padding-left:0" align="right">{btnCancel}</td>
                <td style="padding-top:0px;padding-left:0" align="right">{btnDeleteWine}</td>

            </tr>
        </table>
    </td>
</tr>
           
    </table>
 
 
 </td></tr>

</table><!--tb out-->



<SCRIPT LANGUAGE="JavaScript" TYPE="text/javascript">
<!--
    window.chkDATE =
        function (field, format) {

            var v, re, d, m, y;
            v = field.value;
            (format != null && (format == 'EURO' || format == 'US')) || (format == 'EURO');
            if (v.length > 0) {
                //((format == 'EURO' || format == 'US') ? re = /^\d{1,2}(\-|\/|\.)\d{1,2}\1\d{4}$/ :  re = /^\d{4}(\-|\/|\.)\d{1,2}\1\d{1,2}$/);
                re = /^\d{1,2}(\-|\/|\.)\d{1,2}\1\d{4}$/;
                //if (!re.test(field.value))
                //    return false;

                {
                    m = parseInt(v.substr(0, 2), 10);
                    d = parseInt(v.substr(3, 2), 10);
                    y = parseInt(v.substr(6, 4), 10);
                }

                binM = (1 << (m - 1));
                m31 = 0xAD5;
                if ((y < 1000) || (m < 1) || (m > 12) || (d < 1) || (d > 31) ||
                    ((d == 31 && ((binM & m31) == 0))) ||
                    ((d == 30 && m == 2)) || ((d == 29 && m == 2 && !isLeap(y)))) {
                    return false;
                }
            }
            return true;
        }
//-->
</SCRIPT>
