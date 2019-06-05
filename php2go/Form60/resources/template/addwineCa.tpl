	<!-- Form60 : template used in userAdd.class.php -->
	
	{estate_id}{wineid}{wine_delivery_date_id}{total_bottles}{editMode}{price_per_unit}{pageid}{delivery_total}{price_winery}{is_international}

<table width="100%" height="60%" border="0" ><tr><td align="middle" valign="center">
    	<table width="420px" cellpadding="3" cellspacing="0" border="0">
                <!-- used for error display -->
                
            <tr><td colspan="5" align=left >
                <div id="form_client_errors" class="error_style" style="display:none">{error}</div></td>
            </tr>
         <tr>
                
    			<td colspan="3" style="padding-top:35px;padding-left:0px" >{label_wine_name}<BR>{wine_name}</td>
    			<td style="padding-top:35px;padding-left:10px">
    			<table cellpadding="0" cellspacing="0" border="0"><tr><td style="padding-right:2px">
                        <table cellpadding="0" cellspacing="0" border="0"><tr><td>{label_lkup_bottle_size_id}</td><tr><tr><td style="padding-top:1px">{lkup_bottle_size_id}</td>
                </tr></table></td>

                <td >
                    <table cellpadding="0" cellspacing="0" border="0"><tr><td>{label_lkup_wine_color_type_id}</td><tr><tr><td style="padding-top:1px">{lkup_wine_color_type_id}</td>
                </tr></table>
                </td></tr></table></td>
    			<td style="padding-top:35px;padding-left:10px">{label_cspc_code}<BR>{cspc_code}</td>
    			<td style="padding-top:35px;padding-left:10px">{label_vintage}<BR>{vintage}</td>
    		</tr>

    		<tr>
    			<td style="padding-top:15px;padding-left:0px">{label_price}<BR>{price}</td>
    			<td style="padding-top:15px;padding-left:0px">{label_wholesale}<BR>{wholesale}</td>
                 <td style="padding-top:14px;padding-left:0px">{label_delivery_date}<BR>{delivery_date}</td>
	           <td style="padding-top:15px;padding-left:10px">{label_total_cases}<BR>{total_cases}</td>
    			<td nowrap style="padding-top:15px;padding-left:10px">{label_bottles_per_case}<BR>{bottles_per_case}</td>

	       <!--td nowrap style="padding-top:15px;padding-left:10px">{label_show_total_bottles}<BR>{show_total_bottles}</td-->

<td nowrap style="padding-top:11px;padding-left:10px">

<table  cellpadding="0" cellspacing="0"><tr><td id="not_sold" name="not_sold">
<LABEL FOR="show_total_bottles" ID="show_total_bottles_label" CLASS="label">Total bottles not sold</LABEL>
</td>
<td id="totalbtls" name="totalbtls" style="display:none">
<LABEL FOR="show_total_bottles" ID="show_total_bottles_label" CLASS="label">Total bottles</LABEL>
</td></tr></table>

<INPUT TYPE="text" ID="show_total_bottles" NAME="show_total_bottles" VALUE="" MAXLENGTH="12" SIZE="22" TITLE="Total bottles" TABINDEX="9" CLASS="input" READONLY></td>

<td nowrap style="padding-top:11px;padding-left:10px">


           	</tr>


    		<tr>
    			<td style="padding-top:20px;padding-left:10" colspan="6" align="right" >{btnAdd}&nbsp;{btnCancel}</td>
    		</tr>
        </table>
</td></tr></table> <!-- location table end here-->

<SCRIPT LANGUAGE="JavaScript" TYPE="text/javascript">
<!--
window.chkDATE =
        function (field, format) {
            var v, re, d, m, y;
            v = field.value;
            (format != null && (format == 'EURO' || format == 'US')) || (format =='EURO');
            if (v.length > 0) {
                //((format == 'EURO' || format == 'US') ? re = /^\d{1,2}(\-|\/|\.)\d{1,2}\1\d{4}$/ :  re = /^\d{4}(\-|\/|\.)\d{1,2}\1\d{1,2}$/);
                re = /^\d{1,2}(\-|\/|\.)\d{1,2}\1\d{4}$/ ;
                //if (!re.test(field.value))
                //    return false;
                /*if (format == 'EURO')
                {
                    d = parseInt(v.substr(0, 2), 10);
                    m = parseInt(v.substr(3, 2), 10);
                    y = parseInt(v.substr(6, 4), 10);
                }
                else if (format == 'US')*/
                {
                    m = parseInt(v.substr(0, 2), 10);
                    d = parseInt(v.substr(3, 2), 10);
                    y = parseInt(v.substr(6, 4), 10);
                }
                /*else
                {
                    d = parseInt(v.substr(8, 2), 10);
                    m = parseInt(v.substr(5, 2), 10);
                    y = parseInt(v.substr(0, 4), 10);
                }*/
                binM = (1 << (m-1));
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
