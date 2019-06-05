<!--div class="gridContainer" -->

    <INPUT TYPE="hidden" ID='ListOrderBy_{statusid}' NAME='ListOrderBy_{statusid}' VALUE="{order_by}"/>
    <INPUT TYPE="hidden" ID='ListOrderType_{statusid}' NAME='ListOrderType_{statusid}' VALUE="{order_type}"/>
    <INPUT TYPE="hidden" ID='ListPage_{statusid}' NAME='ListPage_{statusid}' VALUE="{page}"/>

	<table  width="99%"  cellpadding="0" cellspacing="0" border="0"   align="center">
	    <tr>
	        <td class="label" width="200" style="font:bold;">{type}: {total}</td>
	        <td class="label" width="160" style="font:bold;">YTD: {ytd_number}</td>
	        <td>&nbsp;</td>
<!-- START BLOCK : prev_page_link -->
    	    <td width="60"><div class="gridRightLink"><A href="javascript:getPrevPage({statusid});">&lt;&lt;Previous</A></div></td>
<!-- END BLOCK : prev_page_link -->
<!-- START BLOCK : next_page_link -->
	        <td width="60"><div class="gridRightLink"><A href="javascript:getNextPage({statusid});">Next&gt;&gt;</A></div></td>
<!-- END BLOCK : next_page_link -->
	    </tr>
	</table>
	<table  width="99%"  cellpadding="0" cellspacing="0" border="0"   align="center" >
        <tr bgcolor="#7F9DB9">
             <td nowrap class="mlcolheader" width="80">License#</td>
             <td nowrap class="mlcolheader" width="200">Customer</td>
             <td nowrap class="mlcolheader" width="100">PostalCode</td>
             <td nowrap class="mlcolheader" width="200">Address</td>
             <td nowrap class="mlcolheader" width="200">City</td>
             <td nowrap class="mlcolheader" width="30">Phone</td>
             <td nowrap class="mlcolheader" width="30">Fax</td>
        </tr>
   
<!-- START BLOCK : loop_line -->
        <tr class='ml{row_style}'>
            <td nowrap class="CPgridrowCell" {license_updcolor} valign="middle">{license_number}</td>
            <td nowrap class="CPgridrowCell" {customer_name_updcolor} valign="middle">{customer_name_t}</td>
            <td nowrap class="CPgridrowCell"  valign="middle">{postal_code_t}</td>
            <td nowrap class="CPgridrowCell" {address_updcolor} valign="middle">{address_t}</td>
            <td nowrap class="CPgridrowCell" {city_updcolor} valign="middle">{city_t}</td>
            <td nowrap class="CPgridrowCell" {phone_updcolor} valign="middle">{phone_t}</td>
            <td nowrap class="CPgridrowCell" {fax_updcolor} valign="middle">{fax_t}</td>
         </tr>
<!-- END BLOCK : loop_line -->
   </table>
 
   <div class="gridRightLink" style="display:{isDisplay}">
       Page {page} of {total_page}
   </div>

