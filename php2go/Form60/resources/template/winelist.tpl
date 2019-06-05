<div class="gridContainerWineList">
    <INPUT TYPE="hidden" ID="winelistCustomerID" NAME="winelistCustomerID" VALUE="{customer_id}"/>    
    <INPUT TYPE="hidden" ID="winelistOrderBy" NAME="winelistOrderBy" VALUE="{order_by}"/>    
    <INPUT TYPE="hidden" ID="winelistOrderType" NAME="winelistOrderType" VALUE="{order_type}"/>  
    <INPUT TYPE="hidden" ID="wineCount" NAME="wineCount" VALUE="{total}"/>      
    
    <div class="gridHeader">
        <table class="gridTable" cellspacing="0" width="100%" >
                <tr>
                    <td class="gridHeaderCell" style="width:305px;">Estate</td>
                    <td class="gridHeaderCell" style="width:305px;"><A href="javascript:sortWines('wine_name');">Wine</A><span ID="arrow_wine_name" class="sortSymbol">{wine_name_sort}</span></td>
                    <td class="gridHeaderCell" style="width:50px;text-align:right;"><A href="javascript:sortWines('allocated');">Alloc.</A><span ID="arrow_allocated" class="sortSymbol">{allocated_sort}</span></td>
                    <td class="gridHeaderCell" style="width:50px;text-align:right;"><A href="javascript:sortWines('sold');">Sold</A><span ID="arrow_sold" class="sortSymbol">{sold_sort}</span></td>
                    <td class="gridHeaderCell" style="width:50px;text-align:right;"><A href="javascript:sortWines('available');">Avl.</A><span ID="arrow_available" class="sortSymbol">{available_sort}</span></td>
                    <td class="gridHeaderCell" style="width:*;text-align:left;border:0px;padding-left:3px;">New order</td>
                </tr>
        </table>
    </div>
    <div id="winelistGrid" style="overflow:auto; height:20px">
    <!--div id="loadingMsgwines"></div-->
        <table id="winelistTable" class="gridTable" cellspacing="0">
           <!-- START BLOCK : loop_line -->
            <tr class="{row_style}">
                <td nowrap class="gridrowCell" style="width:305px;" valign="middle" title="{estate_name}">{estate_name}</td>
                <td nowrap class="gridrowCell" style="width:305px;" valign="middle" title="{wine_name}">{wine_name}</td>
                <td nowrap class="gridrowCell" style="width:50px;text-align:right;" valign="middle">{allocated}</td>
                <td nowrap class="gridrowCell" style="width:50px;text-align:right;" valign="middle">{sold}</td>
                <td nowrap class="gridrowCell" style="width:50px;text-align:right;" valign="middle"><LABEL ID="Avl[{wine_id}]">{available}</LABEL></td>
                <td nowrap class="gridrowCell" style="width:auto;text-align:left;padding-left:3px;" valign="middle">
                    <INPUT TYPE="text" CLASS="orderinput" ID="Order[{wine_id}]" NAME="Order[{wine_id}]" VALUE="" 
                    MAXLENGTH="4" SIZE="4" TITLE="Type order quantity" CLASS="orderinput" style="text-align:right;" 
                    onKeyPress="return orderKeyPress(this,event);"
                    onBlur="tglOrderButton(canCreateOrder());"
                    />
                </td>
            </tr>
            <!-- END BLOCK : loop_line -->
        </table>
    </div>
    <div class="gridLeftLink">
       Total: {total} wines
    </div>
</div>

<script language="javascript">

function setWineListHeight() 
{
    var nContainer=document.getElementById('winelistGrid');
    var windowHeight= getWindowHeight();
    if (windowHeight>0) 
    {
        var noteHeight= windowHeight - 60 - findPosition(nContainer,0);
        if (noteHeight>20)
        nContainer.style.height=noteHeight+"px";
    }
    var lkup = document.getElementById('estate_id');
    if (lkup && parent.removeEvent) parent.removeEvent(lkup, 'change', formOnChange);
}
addEvent(window, 'load', setWineListHeight);
addEvent(window, 'resize', setWineListHeight);
</script>
