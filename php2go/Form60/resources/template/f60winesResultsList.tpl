
<script type="text/javascript">
$(function() {		

		$("#tbResult").tablesorter({sortList:[[7,1]], widgets: ['zebra']});
	
	});	
</script>

<!--div class="gridContainer" -->

    <INPUT TYPE="hidden" ID='ListOrderBy' NAME='ListOrderBy' VALUE="{order_by}"/>
    <INPUT TYPE="hidden" ID='ListOrderType' NAME='ListOrderType' VALUE="{order_type}"/>
    <INPUT TYPE="hidden" ID='ListPage' NAME='ListPage' VALUE="{page}"/>
    <INPUT TYPE="hidden" ID='total_recs' NAME='total_recs' VALUE="{totalRecs}"/>
   
    <!--INPUT TYPE="hidden" ID='edt_search_id' NAME='edt_search_id' VALUE="{e_search_id}"/>
    <INPUT TYPE="hidden" ID='edt_sales_period' NAME='edt_sales_period' VALUE="{e_sales_period}"/>
    <INPUT TYPE="hidden" ID='edt_sales_year' NAME='edt_sales_year' VALUE="{e_sales_year}"/>
    <INPUT TYPE="hidden" ID='edt_isQrt' NAME='edt_isQrt' VALUE="{e_isQrt}"/>
    <INPUT TYPE="hidden" ID='edt_store_type_id' NAME='edt_store_type_id' VALUE="{e_store_type_id}"/>
    <INPUT TYPE="hidden" ID='edt_user_id' NAME='edt_user_id' VALUE="{e_user_id}"/>
    <INPUT TYPE="hidden" ID='edt_search_adt1' NAME='edt_search_adt1' VALUE="{e_search_adt1}"/>
    <INPUT TYPE="hidden" ID='edt_search_adt2' NAME='edt_search_adt2' VALUE="{e_search_adt2}"/>
    <INPUT TYPE="hidden" ID='edt_isStart' NAME='edt_isStart' VALUE="{e_edt_isStart}"/-->


<!-- $search_id,$sales_period,$sales_year,$isQrt,$store_type_id,$user_id,$search_adt="" #7F9DB9-->
	<table  width="99%"  cellpadding="0" cellspacing="0" border="0" >
	    <tr>
	        <td nowrap><b>{wine_info}</td>
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
		<tr bgcolor="white">
		  <th nowrap class="mlcolheaderH" width="200"><A href="javascript:sortf60resaultlist('estate_name');">Estate</A><span ID='arrow_0' class="sortSymbol-C">{estate_name_sort}</span></th>
			   <th nowrap class="mlcolheaderH" width="200"><A href="javascript:sortf60resaultlist('wine_name');">Product name</A><span ID='arrow_1' class="sortSymbol-C">{wine_name_sort}</span></th>
			  <th nowrap class="mlcolheaderH" width="40"><A href="javascript:sortf60resaultlist('color');">Type</A><span ID='arrow_2' class="sortSymbol-C">{color_sort}</span></th>
		
		 <th nowrap class="mlcolheaderH" width="100"><A href="javascript:sortf60resaultlist('bottle_size');">Btl size</A><span ID='arrow_3' class="sortSymbol-C">{bottle_size_sort}</span></th>
		<th nowrap class="mlcolheaderH" width="100"><A href="javascript:sortf60resaultlist('sku');">SKU</A><span ID='arrow_4' class="sortSymbol-C">{sku_sort}</span></th>    
		
		 
			 <th nowrap class="mlcolheaderH" width="100" id="showCase" name="showCase" align="right"><A href="javascript:sortf60resaultlist('total_cases');">Total cases</A><span ID='arrow_5' class="sortSymbol-C">{total_cases_sort}</span></th>
		 <th nowrap class="mlcolheaderH" width="100" id="showWH" name="showWH" align="right"><A href="javascript:sortf60resaultlist('wh_sales');">WH sales</A><span ID='arrow_6' class="sortSymbol-C">{wh_sales_sort}</span></th>
		 <th nowrap class="mlcolheaderH" width="100" id="showRT" name="showRT" align="right"><A href="javascript:sortf60resaultlist('total_sales');">Retail sales</A><span ID='arrow_7' class="sortSymbol-C">{total_sales_sort}</span></th>
		 
		
		 
		</tr>
	</thead>
        <tbody>

<!-- START BLOCK : loop_line -->
		<tr class='ml{row_style}'>
			<td nowrap class="CPgridrowCell" valign="middle"  title="{estate_name_t}">{estate_name}</td>
			<td nowrap class="CPgridrowCell" valign="middle" title="{wine_name_t}">{wine_name}</td>
			<td nowrap class="CPgridrowCell" valign="middle"  title="{color_t}">{color}</td>
			<td nowrap class="CPgridrowCell" valign="middle"  title="{bottle_size_t}">{btl_size}</td>
			<td nowrap class="CPgridrowCell" valign="middle"  title="{sku_t}">{sku}</td>
			<td nowrap class="CPgridrowCell_Right" valign="middle"  title="{total_cases_t}" style="display:{isShowCase}">{total_cases}</td>
			<td nowrap class="CPgridrowCell_Right" valign="middle"  title="{wh_sales_t}" style="display:{isShowRT}">{wh_sales}</td>
			<td nowrap class="CPgridrowCell_Right" valign="middle"  title="{total_sales_t}" style="display:{isShowWH}">{total_sales}</td>
		</tr>
<!-- END BLOCK : loop_line -->
	</tbody>
   </table>

   <div class="CPgridRightLink" style="display:{isDisplay}">
       Page {page} of {total_page}
   </div>

