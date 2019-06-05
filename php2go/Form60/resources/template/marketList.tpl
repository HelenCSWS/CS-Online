
<!--div class="gridContainer" -->

    <INPUT TYPE="hidden" ID='ListOrderBy_{statusid}' NAME='ListOrderBy_{statusid}' VALUE="{order_by}"/>
    <INPUT TYPE="hidden" ID='ListOrderType_{statusid}' NAME='ListOrderType_{statusid}' VALUE="{order_type}"/>
    <INPUT TYPE="hidden" ID='ListPage_{statusid}' NAME='ListPage_{statusid}' VALUE="{page}"/>

	<table  width="99%"  cellpadding="0" cellspacing="0" border="0" >
	    <tr>
	        <td class="label" width="250" style="font:bold;">{type}: {total}</td>
	        <td class="label" width="160" style="font:bold;">YTD: {ytd_number}</td>
	        <td>&nbsp;</td>
<!-- START BLOCK : prev_page_link -->
    	    <td width="60"><div class="CPgridRightLink"><A href="javascript:getPrevPage({statusid});">&lt;&lt;Previous</A></div></td>
<!-- END BLOCK : prev_page_link -->
<!-- START BLOCK : next_page_link -->
	        <td width="60"><div class="CPgridRightLink"><A href="javascript:getNextPage({statusid});">Next&gt;&gt;</A></div></td>
<!-- END BLOCK : next_page_link -->
	    </tr>
	</table>
	<table  width="99%"  cellpadding="0" cellspacing="0" border="0">
        <tr bgcolor="#7F9DB9">
             <td nowrap class="mlcolheader" width="40"><A href="javascript:sortMarketList('license_number',{statusid});">License#</A><span ID='arrow_license_number_{statues_id}' class="sortSymbol-C">{license_number_sort}</span></td>
             <td nowrap class="mlcolheader" width="200"><A href="javascript:sortMarketList('customer_name',{statusid});">Customer</A><span ID='arrow_customer_name_{statues_id}' class="sortSymbol-C">{customer_name_sort}</span></td>
             <td nowrap class="mlcolheader" width="100"><A href="javascript:sortMarketList('postalcode','{statusid}');">PostalCode</A><span ID='arrow_postalcode_{statues_id}' class="sortSymbol-C">{contact_name_sort}</span></td>
             <td nowrap class="mlcolheader" width="200"><A href="javascript:sortMarketList('address','{statusid}');">Address</A><span ID='arrow_address_{statues_id}' class="sortSymbol-C">{address_sort}</span></td>
             <td nowrap class="mlcolheader" width="100"><A href="javascript:sortMarketList('city','{statusid}');">City</A><span ID='arrow_city_{statues_id}' class="sortSymbol-C">{city_sort}</span></td>
             <td nowrap class="mlcolheader" width="30"><A href="javascript:sortMarketList('phone','{statusid}');">Phone</A><span ID='arrow_phone_{statues_id}' class="sortSymbol-C">{phone_sort}</span></td>
             <td nowrap class="mlcolheader" width="30"><A href="javascript:sortMarketList('fax','{statusid}');">Fax</A><span ID='arrow_fax_{statues_id}' class="sortSymbol-C">{fax_sort}</span></td>
        </tr>

<!-- START BLOCK : loop_line -->
        <tr class='ml{row_style}'>
            <td nowrap class="CPgridrowCell" valign="middle" {license_updcolor} title="{license_number}">{license_number}</td>
            <td nowrap class="CPgridrowCell" valign="middle" {customer_name_updcolor} title="{customer_name_t}">{customer_name}</td>
            <td nowrap class="CPgridrowCell" valign="middle"  title="{postalcode_t}">{postalcode}</td>
            <td nowrap class="CPgridrowCell" valign="middle" {address_updcolor} title="{address_t}">{address}</td>
            <td nowrap class="CPgridrowCell" valign="middle" {city_updcolor} title="{city_t}">{city}</td>
            <td nowrap class="CPgridrowCell" valign="middle" {phone_updcolor} title="{phone_t}">{phone}</td>
            <td nowrap class="CPgridrowCell" valign="middle" {fax_updcolor} title="{fax_t}">{fax}</td>
         </tr>
<!-- END BLOCK : loop_line -->
   </table>

   <div class="CPgridRightLink" style="display:{isDisplay}">
       Page {page} of {total_page}
   </div>

