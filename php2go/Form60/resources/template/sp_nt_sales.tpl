 <table  width="100%"  cellpadding="0" cellspacing="0" border="0" >
	    <tr>
	        <td>&nbsp;</td>
<td align=right style="padding-right:8px" class="label">Page {page} of {total_page}</td>

<!-- START BLOCK : prev_page_link -->
    	    <td width="60"><div class="CPgridRightLink"><A href="javascript:getSpPrevPage({current_page});">&lt;&lt;Previous</A></div></td>
<!-- END BLOCK : prev_page_link -->
<!-- START BLOCK : next_page_link -->
	        <td width="60"><div class="CPgridRightLink"><A href="javascript:getSpNextPage({current_page});">Next&gt;&gt;</A></div></td>
<!-- END BLOCK : next_page_link -->
	    </tr>
	</table>
	<div class="gridContainer" style="margin-top:2px;">
    <INPUT TYPE="hidden" ID="orderlistSortBy" NAME="orderlistSortBy" VALUE="{sort_by}"/>    
    <INPUT TYPE="hidden" ID="orderlistSortType" NAME="orderlistSortType" VALUE="{sort_type}"/>  
    <INPUT TYPE="hidden" ID="totalCount" NAME="totalCount" VALUE="{total}"/>  	 
	 <INPUT TYPE="hidden" ID="currentPage" NAME="currentPage" VALUE="{currentpage}"/>     
  
    <div class="gridHeader" >
    
    
	
     <table class="gridTable" cellspacing="0" border ="0">
      <tr>
                   
                   <td class="gridHeaderCell" style="width:220px;"><A href="javascript:sortSpList('customer_name');">Store name</A><span ID="arrow_customer_name" class="sortSymbol">{customer_name_sort}</span></td>
                    <td class="gridHeaderCell" style="width:80px;display:{showStoreType}"><A href="javascript:sortSpList('license_name');">Store type</A><span ID="arrow_license_name" class="sortSymbol">{license_name_sort}</span></td>
                    <td class="gridHeaderCell" style="width:70px;"><A href="javascript:sortSpList('licensee_number');">License#</A><span ID="arrow_licensee_number" class="sortSymbol">{licensee_number_sort}</span></td>
                    
                    <td class="gridHeaderCell" style="width:250;text-align:left;padding-right:0px"><A href="javascript:sortSpList('address');">Address</A><span ID="arrow_address" class="sortSymbol">{address_sort}</span></td>
                    
                    
                    <td class="gridHeaderCell" style="width:200;text-align:left;padding-right:0px"><A href="javascript:sortSpList('wine_name');">Product</A><span ID="arrow_wine_name" class="sortSymbol" >{wine_name_sort}</span></td>
                    
                     <td class="gridHeaderCell" style="width:70px;text-align:right;padding-right:0px"><A href="javascript:sortSpList('cspc');">CSPC</A><span ID="arrow_cspc" class="sortSymbol">{cspc_sort}</span></td>
                     
                    <td class="gridHeaderCell" style="width:60px;text-align:right;padding-right:0px"><A href="javascript:sortSpList('cases_sold');">Cases</A><span ID="arrow_cases_sold" class="sortSymbol">{cases_sold_sort}</span></td>
						  
						  <td class="gridHeaderCell" style="width:80px;text-align:right;padding-right:0px;display:none"><A href="javascript:sortSpList('btl_sold');">Total blts</A><span ID="arrow_btl_sold" class="sortSymbol">{btl_sold_sort}</span></td>
						  
						  <td class="gridHeaderCell" style="width:auto;text-align:right;padding-right:15px"><A href="javascript:sortSpList('total_amount');">Total</A><span ID="arrow_total_amount" class="sortSymbol">{total_amount_sort}</span></td>
						  
						
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
        <table id="orderlistTable" class="gridTable" cellspacing="0" x>
           <!-- START BLOCK : loop_line -->
            <tr class="{row_style}" height="20px">
              <td nowrap class="gridrowCell" width="220px" valign="middle" title="{tit_customer}">{customer}</td>
                  <td nowrap class="gridrowCell" width="80px" valign="middle" style="display:{showStType}">{store_type}</td>
               <td nowrap class="gridrowCell" width="70px" valign="middle">{lic_no}</td>
            <td nowrap class="gridrowCell" width="250" valign="middle" title="{tit_address}">{address_data}</td>
               <td nowrap class="gridrowCell" style="width:200px;" valign="middle" title="{tit_wine}">{wine_info}</td>
              <td nowrap class="gridrowCell" width="70px" valign="middle" style="text-align:right;padding-right:3px;">{cspc}</td>
              <td nowrap class="gridrowCell" width="60px" valign="middle" style="text-align:right;padding-right:3px;" >{cases}</td>
               <td nowrap class="gridrowCell" width="80px" valign="middle" style="text-align:right;padding-right:3px;display:none">{bottles}</td>
               <td nowrap class="gridrowCell" width="auto" valign="middle" style="text-align:right;padding-right:3px;">{amount}</td>
              

            </tr>
            <!-- END BLOCK : loop_line -->
        </table>
    </div>
    
</div>

<div class="CPgridRightLink" style="display:{isDisplay};margin-top:1px;">

<span style="margin-right:5px" class="label"><B>  Total cases: {tol_cases}</span>   
<span style="margin-right:0px;display:none" class="label"> Total bottles:  {tol_btl}</span>
<span style="margin-right:15px;margin-left:25px" class="label">  Total amount: {tol_amount}</b></span>
   
  <span style="margin-right:8px">Page {page} of {total_page}</span>
  
  <!-- START BLOCK : btm_prev_page_link -->
    	    <span style="margin-right:8px" class="label"><A href="javascript:getSpPrevPage({current_page});" class="label">&lt;&lt;Previous</A></span>
<!-- END BLOCK : btm_prev_page_link -->
<!-- START BLOCK : btm_next_page_link -->
	        <span style="margin-right:0px" class="label"><A href="javascript:getSpNextPage({current_page});" class="label">Next&gt;&gt;</A></span>
<!-- END BLOCK : btm_next_page_link -->

</div>

