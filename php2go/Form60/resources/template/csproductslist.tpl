<div class="gridContainerWineList">
    <INPUT TYPE="hidden" ID="csproductlistOrderBy" NAME="csproductlistOrderBy" VALUE="{order_by}"/>    
    <INPUT TYPE="hidden" ID="csproductlistOrderType" NAME="csproductlistOrderType" VALUE="{order_type}"/>  
    <INPUT TYPE="hidden" ID="csproductCount" NAME="csproductCount" VALUE="{total}"/>      
    
    <div class="gridHeader">
        <table class="gridTable" cellspacing="0" width="100%" >
                <tr>
                    <td class="gridHeaderCell" style="width:305px;"><A href="javascript:sortCSProduts('product_name');">Product</A><span ID="arrow_product_name" class="sortSymbol">{product_name_sort}</span></td>
                    <td class="gridHeaderCell" style="width:100px;text-align:right;"><A href="javascript:sortCSProduts('total_units');">Available Units.</A><span ID="arrow_total_units" class="sortSymbol">{total_units_sort}</span></td>
                    <td class="gridHeaderCell" style="width:100px;text-align:right;"><A href="javascript:sortCSProduts('total_cs');">Available Cases</A><span ID="arrow_total_cs" class="sortSymbol">{total_cs_sort}</span></td>
                     <td class="gridHeaderCell" style="width:*;text-align:left;border:0px;padding-left:3px;">New order (Units)</td>
                </tr>
        </table>
    </div>
    <div id="csproductslistGrid" style="overflow:auto; height:20px">
    <!--div id="loadingMsgwines"></div-->
        <table id="csproductslistTable" class="gridTable" cellspacing="0">
           <!-- START BLOCK : loop_line -->
            <tr class="{row_style}">
                <td nowrap class="gridrowCell" style="width:305px;" valign="middle" title="{product_name}">{product_name}</td>
                
                 <td nowrap class="gridrowCell" style="width:100px;text-align:right;" valign="middle"><LABEL ID="Avl[{cs_product_id}]">{total_units}</LABEL></td>
                 
                <td nowrap class="gridrowCell" style="width:100px;text-align:right;" valign="middle">{total_cs}</td>
                
                <td nowrap class="gridrowCell" style="width:auto;text-align:left;padding-left:3px;" valign="middle">
                    <INPUT TYPE="text" CLASS="csorderinput" ID="CSOrder[{cs_product_id}]" NAME="CSOrder[{cs_product_id}]" CS_ID_INFO="{cs_product_id}" VALUE="" 
                    MAXLENGTH="4" SIZE="4" TITLE="Type order quantity" style="text-align:right;" 
                    onKeyPress="return csOrderKeyPress(this, event);"
                    onBlur="tglCSOrderButton(canCreateCSOrder());"/>
                </td>
            </tr>
            <!-- END BLOCK : loop_line -->
        </table>
    </div>
    <div class="gridLeftLink">
       Total: {total} product(s)
    </div>
</div>

<script language="javascript">

function setCSProductsListHeight()
{

    var nContainer=document.getElementById('csproductslistGrid');
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
