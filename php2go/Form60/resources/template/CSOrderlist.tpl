<div class="gridContainer">
    <INPUT TYPE="hidden" ID="csorderlistCustomerID" NAME="csorderlistCustomerID" VALUE="{customer_id}"/>    
    <INPUT TYPE="hidden"  ID="csorderlistSortBy" NAME="csorderlistSortBy" VALUE="{sort_by}"/>    
    <INPUT TYPE="hidden" ID="csorderlistSortType" NAME="csorderlistSortType" VALUE="{sort_type}"/>  
    <INPUT TYPE="hidden" ID="csorderCount" NAME="csorderCount" VALUE="{total}"/>  	 
  
    <div class="gridHeader">
        <table class="gridTable" cellspacing="0">
                <tr>
                    <td class="gridHeaderCell" style="width:65px;">&nbsp;</td>
                    <td class="gridHeaderCell" style="width:83px;"><A href="javascript:sortOrders('order_date');">Date</A><span ID="arrow_order_date" class="sortSymbol">{order_date_sort}</span></td>
                    <td class="gridHeaderCell" style="width:120px;"><A href="javascript:sortOrders('invoice_number');">Invoice#</A><span ID="arrow_invoice_number" class="sortSymbol">{invoice_number_sort}</span></td>
                    <td class="gridHeaderCell" style="width:215px;"><A href="javascript:sortOrders('product_name');">Product</A><span ID="arrow_product_name1" class="sortSymbol">{product_name_sort}</span></td>
                    <td class="gridHeaderCell" style="width:120px;"><A href="javascript:sortOrders('payment_status');">Payment status</A><span ID="arrow_payment_status" class="sortSymbol">{payment_status_sort}</span></td>
                    <td class="gridHeaderCell" style="width:120px;"><A href="javascript:sortOrders('order_status');">Order status</A><span ID="arrow_order_status" class="sortSymbol">{order_status_sort}</span></td>
                    <td class="gridHeaderCell" style="width:auto;text-align:right;padding-right:0px"><A href="javascript:sortOrders('total_amount');">Order total</A><span ID="arrow_total_amount" class="sortSymbol">{total_amount_sort}</span></td>
                    
                    
                </tr>
        </table>
    </div>
    <div id="CSOrderlistGrid" style="overflow:auto; height:20px">
    <!--div id="loadingMsgorders"></div-->
        <table id="csorderlistTable" class="gridTable" cellspacing="0">
           <!-- START BLOCK : loop_line -->
            <tr class="{row_style}">
                <td nowrap class="gridrowCell" style="width:65px;" valign="top">
                <A href="javascript:viewCSOrder('{order_id}')"><img src="resources/images/print.gif" border="0" title = "Print Invoice" ></A>
                <A href="javascript:showCsOrderForm('{order_id}')"><img src="resources/images/edit.gif" border="0" title = "Edit order" ></A>
                <A href="javascript:deleteCSOrder('{order_id}', {lkup_order_status_id})"><img src="resources/images/delete.gif" border="0" title = "Delete order"></A></td>
                <td nowrap class="gridrowCell" style="width:83px;" valign="middle">{order_date}</td>
                <td nowrap class="gridrowCell" width="120px" valign="middle"><A id="{order_id}" href="javascript:showCsOrderForm('{order_id}');">{invoice_number}</A></td>
                <td nowrap class="gridrowCell" style="width:215px;" valign="middle">{product_name}</td>
                <td nowrap class="gridrowCell" style="width:120px;" valign="middle">{payment_status}</td>
                <td nowrap class="gridrowCell" style="width:120px;" valign="middle">{order_status}</td>
                <td nowrap class="gridrowCell" style="width:auto;text-align:right;padding-right:8px" valign="middle">{total_amount}</td>
            </tr>
            <!-- END BLOCK : loop_line -->
        </table>
    </div>
    <div class="gridBottomRightLink" style="margin-top:8px">
       Total orders: {total}&nbsp;&nbsp;&nbsp;Total sales: {total_sales}
    </div>
</div>

<script language="javascript">
//function setCSOrderListHeight() 
function setCSOrderListHeight() 
{

    var nContainer = document.getElementById('CSOrderlistGrid');
    if(nContainer.style.display == 'none') return;
    var refElement = document.getElementById('fldTab');
    //var refElHeight = findPosition(refElement,0);
    var gap = 295 ;
    if (refElement.offsetHeight>0) 
    {
        var listHeight = getWindowHeight()- gap - findPosition(nContainer,0);
        if (listHeight > 20)
            nContainer.style.height =listHeight + "px";
    }
  //  var lkup = document.getElementById('order_estate_id');
  //  if (lkup && parent.removeEvent) parent.removeEvent(lkup, 'change', formOnChange);
    lkup = document.getElementById('cs_order_year');
    if (lkup && parent.removeEvent) parent.removeEvent(lkup, 'change', formOnChange);
}
addEvent(window, 'load', setCSOrderListHeight);
addEvent(window, 'resize', setCSOrderListHeight);
</script>
