
<!--div class="gridContainer" -->

    <INPUT TYPE="hidden" ID='ListOrderBy_{statusid}' NAME='ListOrderBy_{statusid}' VALUE="{order_by}"/>
    <INPUT TYPE="hidden" ID='ListOrderType_{statusid}' NAME='ListOrderType_{statusid}' VALUE="{order_type}"/>
    <INPUT TYPE="hidden" ID='ListPage_{statusid}' NAME='ListPage_{statusid}' VALUE="{page}"/>

	<table  width="99%"  cellpadding="0" cellspacing="0" border="0" >
	    <tr>
	        <td class="label" width="160" style="display:none">{{current_page}}</td>
	        <!-- td class="label" width="250" style="font:bold;">{type}: {total}</td>
	        <td class="label" width="160" style="font:bold;">YTD: {ytd_number}</td -->
	        <td>&nbsp;</td>
<!-- START BLOCK : prev_page_link -->
    	    <td width="60"><div class="CPgridRightLink"><A href="javascript:getPrevPage({current_page});">&lt;&lt;Previous</A></div></td>
<!-- END BLOCK : prev_page_link -->
<!-- START BLOCK : next_page_link -->
	        <td width="60"><div class="CPgridRightLink"><A href="javascript:getNextPage({current_page});">Next&gt;&gt;</A></div></td>
<!-- END BLOCK : next_page_link -->
	    </tr>
	</table>
	<table  width="99%"  cellpadding="0" cellspacing="0" border="0">
        <tr bgcolor="#7F9DB9">
             <td nowrap class="mlTitle" width="10%" style="padding-left:3px">License#</td>
             <td nowrap class="mlTitle" width="10%" style="padding-left:0px;display:{isDisplay_title}">{store_type_title}</td>
             <td nowrap class="mlTitle" width="20%" style="padding-left:1px">Customer</td>
             <td nowrap class="mlTitle" width="15%" style="padding-left:1px">City</td>
             <td nowrap class="mlTitle" width="*%" style="padding-left:1px">Address</td>
             <td nowrap class="mlTitle" width="5%" align="right">Cases sold</td>
             <td nowrap class="mlTitle" width="5%" align="right" style="padding-left:15px">Bts sold</td>
             <td nowrap class="mlTitle" width="100" align="right" style="padding-left:10px;padding-right:3px">Total profit</td>
             <td nowrap class="mlTitle" width="100" align="right" style="padding-left:10px;padding-right:3px">WH sales</td>
             <td nowrap class="mlTitle" width="100" align="right" style="padding-left:10px;padding-right:3px">Retail sales</td>
        </tr>

<!-- START BLOCK : loop_line -->
        <tr class='ml{row_style}'>
            <td nowrap class="CPgridrowCell" valign="middle" title="{license_no_t}">{license_no}</td>
            <td nowrap class="CPgridrowCell" valign="middle" title="{store_type_t}" style="display:{isDisplay_type}">{store_type}</td>
            <td nowrap class="CPgridrowCell" valign="middle" title="{customer_name_t}">{customer_name}</td>
            <td nowrap class="CPgridrowCell" valign="middle" title="{city_t}">{city}</td>
            <td nowrap class="CPgridrowCell" valign="middle" title="{address_t}">{address}</td>
            <td nowrap class="CPgridrowCell_Right" valign="middle" align="right" title="{total_cases_t}" >{total_cases}</td>
				<td nowrap class="CPgridrowCell_Right" valign="middle" align="right" title="(bts_sold}" >{bts_sold}</td>
            <td nowrap class="CPgridrowCell_Right" valign="middle" align="right" title="{total_profit_t}" >{total_profit}</td>
            <td nowrap class="CPgridrowCell_Right" width="100" align="right" style="padding-left:10px;padding-right:3px">{total_sales}</td>
    
            <td nowrap class="CPgridrowCell_Right" width="100" align="right" style="padding-left:10px;padding-right:3px">{total_RT}</td>
         </tr>
<!-- END BLOCK : loop_line -->

	<tr id="trStoreTypeTotal">
            <td nowrap class="CPgridrowCell" valign="middle" ></td>
            <td nowrap class="CPgridrowCell" valign="middle" ></td>
            <td nowrap class="CPgridrowCell" valign="middle" ></td>
            <td nowrap class="CPgridrowCell" valign="middle" ></td>
            <td nowrap class="CPgridrowCell" valign="middle" ></td>
            <td nowrap class="CPgridrowCell_Right" valign="middle" >{sub_total_cases}</td>
            <td nowrap class="CPgridrowCell_Right" valign="middle" ><b>{sub_tlt_bts_sold}</b></td>
             <td nowrap class="CPgridrowCell_Right" valign="middle" ><b>{sub_total_profit}</b></td>
         </tr>
   </table>

   <div class="CPgridRightLink" style="display:{isDisplay}">
       Page {page} of {total_page}
   </div>

