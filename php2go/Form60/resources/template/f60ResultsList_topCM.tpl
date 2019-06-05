<script type="text/javascript">
$(function() {		

		$("#tbResult").tablesorter({sortList:[[6,1]], widgets: ['zebra']});
	
	});	
</script>
	
<!--div class="gridContainer" -->

    <INPUT TYPE="hidden" ID='ListOrderBy' NAME='ListOrderBy' VALUE="{order_by}"/>
    <INPUT TYPE="hidden" ID='ListOrderType' NAME='ListOrderType' VALUE="{order_type}"/>
	

   <INPUT TYPE="hidden" ID='ListPage' NAME='ListPage' VALUE="{page}"/>

   <INPUT TYPE="hidden" ID='total_recs' NAME='total_recs' VALUE="{totalRecs}"/>
   <!--INPUT TYPE="hidden" ID='edt_sales_period' NAME='edt_sales_period' VALUE="{e_sales_period}"/>
   <INPUT TYPE="hidden" ID='edt_sales_year' NAME='edt_sales_year' VALUE="{e_sales_year}"/>
   <INPUT TYPE="hidden" ID='edt_isQrt' NAME='edt_isQrt' VALUE="{e_isQrt}"/>
   <INPUT TYPE="hidden" ID='edt_store_type_id' NAME='edt_store_type_id' VALUE="{e_store_type_id}"/>
   <INPUT TYPE="hidden" ID='edt_user_id' NAME='edt_user_id' VALUE="{e_user_id}"/>
   <INPUT TYPE="hidden" ID='edt_search_adt1' NAME='edt_search_adt1' VALUE="{e_search_adt1}"/>
   <INPUT TYPE="hidden" ID='edt_search_adt2' NAME='edt_search_adt2' VALUE="{e_search_adt2}"/ -->

<!-- $search_id,$sales_period,$sales_year,$isQrt,$store_type_id,$user_id,$search_adt=""-->
	<table  width="99%"  cellpadding="0" cellspacing="0" border="0" >
	    <tr>
	        <td nowrap class="label"><b>{wine_info}</td>
	        <td width="99%">&nbsp;</td>
    	    <!-- START BLOCK : prev_page_link -->
			  <td width="1%"><div class="CPgridRightLink"><A href="javascript:getPrevPage();">&lt;&lt;Previous</A></div></td>
			<!-- END BLOCK : prev_page_link -->
			<!-- START BLOCK : next_page_link -->
	        <td width="*"><div class="CPgridRightLink"><A href="javascript:getNextPage();">Next&gt;&gt;</A></div></td>
<!-- END BLOCK : next_page_link -->
	    </tr>
	</table>
	<table  width="99%"  cellpadding="0" cellspacing="0" border="0" id="tbResult" class="tablesorter">
	<thead>
        <tr bgcolor="#7F9DB9">
              <th nowrap class="mlcolheaderH" width="200" align="left">Customer<span ID='arrow_0' class="sortSymbol-C">{customer_name_sort}</span></th>
				   <th nowrap class="mlcolheaderH" width="200" align="left">Address<span ID='arrow_1' class="sortSymbol-C">{address_sort}</span></th>
				  <th nowrap class="mlcolheaderH" width="0" align="left" ><A href="javascript:sortTopCM('licensee_no');">License</A><span ID='arrow_2' class="sortSymbol-C">{licensee_no_sort}</span></th>
            
             <th nowrap class="mlcolheaderH" width="100" align="left"><A href="javascript:sortTopCM('store_type');">Store type</A><span ID='arrow_3' class="sortSymbol-C">{store_type_sort}</span></th>
            
             <th nowrap class="mlcolheaderH" width="80" align="{title_case_style}"><A href="javascript:sortTopCM('total_cases');">{title_cases}</A><span ID='arrow_4' class="sortSymbol-C">{total_cases_sort}</span></th>
             <th nowrap class="mlcolheaderH" width="100" align="{title_wh_style}"><A href="javascript:sortTopCM('wh_sales');">{title_WH}</A><span ID='arrow_5' class="sortSymbol-C">{wh_sales_sort}</span></th>
             <th nowrap class="mlcolheaderH" width="100" align="right" style="display:{isShowRT_t}"><A href="javascript:sortTopCM('total_sales');">Retail sales</A><span ID='arrow_6' class="sortSymbol-C">{total_sales_sort}</span></th>
             
             <th nowrap class="mlcolheaderH" width="100" style="padding-left:5px"><A href="javascript:sortTopCM('user_name');">Assigned to</A><span ID='arrow_7' class="sortSymbol-C">{user_name_sort}</span></th>
             
        </tr>
    </thead>
    <tbody>

<!-- START BLOCK : loop_line -->
      <tr class='ml{row_style}'>
          <td nowrap class="CPgridrowCell" valign="middle"  title="{customer_name_t}">{customer_name}</td>
          <td nowrap class="CPgridrowCell" valign="middle" title="{address_t}">{address}</td>
          <td nowrap class="CPgridrowCell" valign="middle"  title="{license_number}">{license_number}</td>
          <td nowrap class="CPgridrowCell" valign="middle"  title="{store_type_t}">{store_type}</td>
          <td nowrap class="{case_align_style}" valign="middle"  title="{total_cases_t}" >{total_cases}</td>
          <td nowrap class="{case_align_style}" valign="middle"   title="{wh_sales_t}" >{wh_sales}</td>
          <td nowrap class="{case_align_style}" valign="middle"   title="{total_sales_t}" style="display:{isShowRT}">{total_sales}</td>
          <td nowrap class="CPgridrowCell" valign="middle"  title="{user_name_t}" style="padding-left:5px">{user_name}</td>
		</tr>
<!-- END BLOCK : loop_line -->
</tbody>
   </table>

   <div class="CPgridRightLink" style="display:{isDisplay}">
       Page {page} of {total_page}
   </div>

