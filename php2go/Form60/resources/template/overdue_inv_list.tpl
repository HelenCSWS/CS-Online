 <table  width="100%"  cellpadding="0" cellspacing="0" border="0" >
	    <tr >
	        <td>&nbsp;</td>
<td align=right style="padding-right:8px" class="label">Page {page} of {total_page}</td>

<!-- START BLOCK : prev_page_link -->
    	    <td width="60"><div ><A href="javascript:getOverduePrevPage({current_page});" class="label">&lt;&lt;Previous</A></div></td>
<!-- END BLOCK : prev_page_link -->
<!-- START BLOCK : next_page_link -->
	        <td width="60" style="padding-left:8px"><div ><A href="javascript:getOverdueNextPage({current_page});" class="label">Next&gt;&gt;</A></div></td>
<!-- END BLOCK : next_page_link -->
	    </tr>
	</table>
	 <div class="gridContainer" style="margin-top:2px;">
    <INPUT TYPE="hidden" ID="orderlistSortBy"  NAME="orderlistSortBy" VALUE="{sort_by}"/>    
    <INPUT TYPE="hidden" ID="orderlistSortType" NAME="orderlistSortType" VALUE="{sort_type}"/>  
    <INPUT TYPE="hidden" ID="totalCount" NAME="totalCount" VALUE="{total}"/>  	 
	<INPUT TYPE="hidden" ID="currentPage" NAME="currentPage" VALUE="{currentpage}"/>     
  
    <div class="gridHeader" >    
	
     <table class="gridTable" cellspacing="0" border="0">
      <tr>
            <td class="gridHeaderCell" style="width:83px;"><A href="javascript:sortOverdueList('delivery_date');">Ordered</A><span ID="arrow_delivery_date" class="sortSymbol">{delivery_date_sort}</span></td>
             <td class="gridHeaderCell" style="width:45px;"><A href="javascript:sortOverdueList('overdays');">Days</A><span ID="arrow_overdays" class="sortSymbol">{overdays_sort}</span></td>
			 <td class="gridHeaderCell" style="width:70px;"><A href="javascript:sortOverdueList('invoice_number');">Invoice#</A><span ID="arrow_invoice_number" class="sortSymbol">{invoice_number_sort}</span></td>
            
            <td class="gridHeaderCell" style="width:75px;"><A href="javascript:sortOverdueList('license_name');">Store type</A><span ID="arrow_license_name" class="sortSymbol">{license_name_sort}</span></td>
            <td class="gridHeaderCell" style="width:60px;"><A href="javascript:sortOverdueList('licensee_number');">License#</A><span ID="arrow_licensee_number" class="sortSymbol">{licensee_number_sort}</span></td>
            <td class="gridHeaderCell" style="width:250px;"><A href="javascript:sortOverdueList('customer_name');">Store name</A><span ID="arrow_customer_name" class="sortSymbol">{customer_name_sort}</span></td>
            <td class="gridHeaderCell" style="width:250;text-align:left;padding-right:0px"><A href="javascript:sortOverdueList('address');">Address</A><span ID="arrow_address" class="sortSymbol">{address_sort}</span></td>
				  
		    <!--td class="gridHeaderCell" style="width:120px;text-align:left;padding-left:5px"><A href="javascript:sortOverdueList('estate_name');">Estate</A><span ID="arrow_estate_name" class="sortSymbol">{estate_name_sort}</span></td-->
			  
			<td class="gridHeaderCell" style="width:60px;text-align:right;padding-right:0px"><A href="javascript:sortOverdueList('cases_sold');">Cases</A><span ID="arrow_cases_sold" class="sortSymbol">{cases_sold_sort}</span></td>
				  
			<td class="gridHeaderCell" style="width:70px;text-align:right;padding-right:0px"><A href="javascript:sortOverdueList('total_amount');">Total</A><span ID="arrow_total_amount" class="sortSymbol">{total_amount_sort}</span></td>
				  
				  <td class="gridHeaderCellRight" style="width:*;text-align:left;padding-right:0px" nowrap><A href="javascript:sortOverdueList('user_name');">Wine consultant</A><span ID="arrow_user_name" class="sortSymbol">{user_name_sort}</span></td>
        </tr>
    </table>
    </div>
    <div id="orderlistGrid" style="overflow:auto;height:540px;">
   <table  width="100%"  cellpadding="0" cellspacing="0" border="0" >
	   <TR id="noresults"><TD ALIGN='center' valign="middle" style="display:block" style="padding-top:250px">
	   	   Nothing was found matching your select criteria, please try again.<BR><BR>
	     </TD>
		</TR>
 	</table>
    <!--div id="loadingMsgorders"></div-->
        <table id="orderlistTable" class="gridTable" cellspacing="0" border="0">
           <!-- START BLOCK : loop_line -->
            <tr class="{row_style}" height="20px">
                <td nowrap class="gridrowCell" style="width:83px;" valign="middle"><A href="javascript:viewForm60('{order_id}')">{order_date}</A></td>
                <td nowrap class="gridrowCell" width="45px" valign="middle" style="text-align:right;padding-right:10px;"><A id="{order_id}" href="javascript:viewForm60('{order_id}');">{over_days}</A></td>
               <td nowrap class="gridrowCell" width="70px" valign="middle"><A id="{order_id}" href="javascript:viewForm60('{order_id}');">{invoice_number}</A></td>
              <td nowrap class="gridrowCell" width="75px" valign="middle"><A id="{order_id}" href="javascript:viewForm60('{order_id}');">{store_type}</A></td>
               <td nowrap class="gridrowCell" width="60px" valign="middle"><A id="{order_id}" href="javascript:viewForm60('{order_id}');">{lic_no}</A></td>
               <td nowrap class="gridrowCell" width="250px" valign="middle" title="{tit_customer}"><A id="{order_id}" href="javascript:viewForm60('{order_id}');" >{customer}</A></td>              
               <td nowrap class="gridrowCell" width="250px" valign="middle" title="{tit_address}"><A id="{order_id}" href="javascript:viewForm60('{order_id}');" >{address_data}</A></td>              
               <!--td nowrap class="gridrowCell" width="120px" valign="middle" title="{tit_estate}" style="padding-left:5px;"><A id="{order_id}" href="javascript:viewForm60('{order_id}');">{estate}</A></td-->                
                <td nowrap class="gridrowCell" width="60px" valign="middle" style="text-align:right;padding-right:3px;"><A id="{order_id}" href="javascript:viewForm60('{order_id}');">{cases}</A></td>
               <td nowrap class="gridrowCell" width="70px" valign="middle" style="text-align:right;padding-right:3px;"><A id="{order_id}" href="javascript:viewForm60('{order_id}');">{amount}</A></td>               
               <td nowrap class="gridrowCell" width="*" valign="middle"><A id="{order_id}" href="javascript:viewForm60('{order_id}');">{user}</A></td>
            </tr>
            <!-- END BLOCK : loop_line -->
        </table>
    </div>
   


<div class="CPgridRightLink" style="display:{isDisplay};margin-top:1px;">

<!--span style="margin-right:5px" class="label"><B>  Total cases: {tol_cases}</span>   
<span style="margin-right:0px;display:none" class="label"> Total bottles:  {tol_btl}</span>
<span style="margin-right:15px;margin-left:25px" class="label">  Total amount: {tol_amount}</b></span-->
   
  <span style="margin-right:8px">Page {page} of {total_page}</span>
  
  <!-- START BLOCK : btm_prev_page_link -->
    	    <span style="margin-right:8px" class="label"><A href="javascript:getOverduePrevPage({current_page});" class="label">&lt;&lt;Previous</A></span>
<!-- END BLOCK : btm_prev_page_link -->
<!-- START BLOCK : btm_next_page_link -->
	        <span style="margin-right:0px" class="label"><A href="javascript:getOverdueNextPage({current_page});" class="label">Next&gt;&gt;</A></span>
<!-- END BLOCK : btm_next_page_link -->

</div>
