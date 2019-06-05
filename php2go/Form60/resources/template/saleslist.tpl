<div class="gridContainer">
    <INPUT TYPE="hidden" ID="saleslistCustomerID" NAME="saleslistCustomerID" VALUE="{customer_id}"/>    
    <INPUT TYPE="hidden" ID="saleslistSortBy" NAME="saleslistSortBy" VALUE="{sort_by}"/>    
    <INPUT TYPE="hidden" ID="saleslistSortType" NAME="saleslistSortType" VALUE="{sort_type}"/>  
    <INPUT TYPE="hidden" ID="salesCount" NAME="salesCount" VALUE="{total}"/>      
    
     <INPUT TYPE="hidden" ID='total_sales_pages' NAME='total_sales_pages' VALUE="{t_pages}"/>
    
    <div class="gridHeader">
        <table class="gridTable" cellspacing="0" width="100%">
                <tr>
                    <td class="gridHeaderCell" style="width:90px;text-align:left;"><A href="javascript:sortSalesList('sale_date');">Sales date</A><span ID="arrow_sale_date" class="sortSymbol">{sale_date_sort}</span></td>
						  
						  <td class="gridHeaderCell" style="width:65px;"><A href="javascript:sortSalesList('country');">Country</A><span ID="arrow_country" class="sortSymbol">{country_sort}</span></td>
                    <td class="gridHeaderCell" style="width:180px;">
						  
						  <A href="javascript:sortSalesList('estate_name');">Estate</A><span ID="arrow_estate_name" class="sortSymbol">{estate_name_sort}</span>
						  </td>
                    <td class="gridHeaderCell" style="width:200px;" nowrap><A href="javascript:sortSalesList('wine_name');">Product</A><span ID="arrow_wine_name" class="sortSymbol">{wine_name_sort}</span></td>
                    <td class="gridHeaderCell" style="width:60px;"><A href="javascript:sortSalesList('cspc_code');">CSPC</A><span ID="arrow_cspc_code" class="sortSymbol">{cspc_code_sort}</span></td>
                    <td class="gridHeaderCell" style="width:55px;"><A href="javascript:sortSalesList('wine_type');">Type</A><span ID="arrow_wine_type" class="sortSymbol">{wine_type_sort}</span></td>
                    <td class="gridHeaderCell" style="width:45px;text-align:right"><A href="javascript:sortSalesList('bottles_per_case');">Btls/cs</A><span ID="arrow_bottles_per_case" class="sortSymbol">{bottles_per_case_sort}</span></td>
						  <td class="gridHeaderCell" style="width:65px;text-align:right"><A href="javascript:sortSalesList('units_sale');">Btls sold</A><span ID="arrow_units_sale" class="sortSymbol">{units_sale_sort}</span></td>
                    <td class="gridHeaderCell" style="width:75px;text-align:right"><A href="javascript:sortSalesList('cases_sold');">Cs sold</A><span ID="arrow_cases_sold" class="sortSymbol">{cases_sold_sort}</span></td>
                    <td  class="gridHeaderCell" style="width:105px;text-align:right;padding-right:0px"><A href="javascript:sortSalesList('total_amount');">WH sales</A><span ID="arrow_total_amount" class="sortSymbol">{total_amount_sort}</span></td>
                    <td  class="gridHeaderCell" style="width:65px;text-align:right;padding-right:0px"><A href="javascript:sortSalesList('profit');">Profit</A><span ID="arrow_profit" class="sortSymbol">{profit_sort}</span></td>
                    <td  class="gridHeaderCell" style="width:auto;text-align:right;padding-right:0px"><A href="javascript:sortSalesList('total_sales');">Retail sales</A><span ID="arrow_total_sales" class="sortSymbol">{total_RT_sort}</span></td>
                    
                </tr>
        </table>
    </div>
    <div id="saleslistGrid" style="overflow:auto; height:20px">
    <!--div id="loadingMsgsales"></div-->
        <table id="saleslistTable" class="gridTable" cellspacing="0" border="0" width="100%">
           <!-- START BLOCK : loop_line -->
            <tr class="{row_style}" height="20px">
                <td nowrap class="gridrowCell" style="width:90px;text-align:left;"  valign="middle">{sale_date}</td>
					 <td nowrap class="gridrowCell" style="width:65px;" title="{wine_country_t}" valign="middle">{wine_country}</td>
                <td nowrap class="gridrowCell" style="width:180px;" title="{estate_name_t}" valign="middle">{estate_name}</td>
                <td nowrap class="gridrowCell" style="width:200px" title="{wine_name_t}" valign="middle" nowrap>{wine_name}</td>
                 <td nowrap class="gridrowCell" width="60px" valign="middle">{cspc_code}</td>
                <td nowrap class="gridrowCell" style="width:55px;" title="{wine_type_t}" valign="middle">{wine_type}</td>
                <td nowrap class="gridrowCell" style="width:45px;text-align:right" valign="middle">{bottles_per_case}</td>
                <td nowrap class="gridrowCell" style="width:65px;text-align:right" valign="middle">{btl_sold}</td>
                <td nowrap class="gridrowCell" style="width:75px;text-align:right" valign="middle">{case_sold}</td>
                <td nowrap class="gridrowCell" style="width:105px;text-align:right;padding-right:3px" valign="middle">{total_price}</td>
                <td nowrap class="gridrowCell" style="width:65px;text-align:right;padding-right:3px" valign="middle">{profit}</td>
                <td nowrap class="gridrowCell" style="width:auto;text-align:right;padding-right:3px" valign="middle">{total_RT}</td>
                
            </tr>
            <!-- END BLOCK : loop_line -->
           
        </table>
    </div>
    
    <div class="gridRightLink" style="margin-top:5px;margin-right:2px">
       Total cases: {total_cases}&nbsp;&nbsp;&nbsp;Total wholesale: {total_whsales}&nbsp;&nbsp;&nbsp;Total profit: {total_profit}&nbsp;&nbsp;&nbsp;Total retail: {total_retail}
    </div>
</div>

<script language="javascript">
function setSalesListHeight() 
{
    var nContainer = document.getElementById('saleslistGrid');
    if(nContainer.style.display == 'none') return;
    var refElement = document.getElementById('fldTab');
    //var refElHeight = findPosition(refElement,0);
    var gap = 295 ;
    
    var province_id = document.getElementById('province_id').value;
    
    if(province_id==1)
    {
	    /*if (refElement.offsetHeight>0) 
	    {
	        var listHeight = getWindowHeight()- gap - findPosition(nContainer,0);
	        if (listHeight > 20)
	            nContainer.style.height =listHeight + "px";
	    }
	    var lkup = document.getElementById('order_estate_id');
	    if (lkup && parent.removeEvent) parent.removeEvent(lkup, 'change', formOnChange);
	    lkup = document.getElementById('order_year');
	    if (lkup && parent.removeEvent) parent.removeEvent(lkup, 'change', formOnChange);*/
	    
	    if (refElement.offsetHeight>0) 
	    {
	        var listHeight = getWindowHeight()- 75 - findPosition(nContainer,0);
	        if (listHeight > 20)
	            nContainer.style.height =listHeight + "px";
	    }
	}
	else
	{
		 if (refElement.offsetHeight>0) 
	    {
	        var listHeight = getWindowHeight()- 75 - findPosition(nContainer,0);
	        if (listHeight > 20)
	            nContainer.style.height =listHeight + "px";
	    }
	}
}
addEvent(window, 'load', setSalesListHeight);
addEvent(window, 'resize', setSalesListHeight);
</script>
