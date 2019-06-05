	<!-- Form60 : template used in orderedit.class.php -->
	
	
        <div style="text-align: left;">
	{pageid}{estateName}{estate_id}
        {order_id}
        {GST_factor}
        {agency_LRS_factor}
        <table width="100%" cellspacing="0" cellpadding="0">
        <tr><td width = "33%" align="left" valign="top">
            <div class="invTop" style='width:300px;'>
                <fieldset>
                    <div class='invBold'>
                        Customer: </BR>
                        {customer_name}</BR>
                        {customer_address}</BR></BR>
                        {label_license_name} {license_name} </BR>
                        {label_licensee_number} {licensee_number}</BR>  
                    </div>
                </fieldset>
            </div>
        </td><td width = "33%" align="center" valign="top">
            <div class="invTop" style='width:250px;'>
                <fieldset style="padding:2px;">
                    <div class='invBold'>
                        <table>
                        <tr><td>{label_estate_name}</td><td>{estate_name}</td></tr>
                        <tr><td>{label_estate_number}</td><td>{estate_number}</td></tr>
                        <tr><td>{label_lkup_order_status_id}</td><td>{lkup_order_status_id}</td></tr>
                        <tr><td>{label_lkup_payment_status_id}</td><td>{lkup_payment_status_id}</td></tr>
                        </table>
                    </div>
                </fieldset>
            </div>
        </td><td width = "33%" align="right" valign="top">
            <div class="invTop" style='width:300px;'>
                <fieldset>
                    <div class='invBold'>
                        <table>
                        <tr><td style="width:100px;">{label_invoice_number}</td><td style="width:110px;">{invoice_number}</td></tr>
                        <tr><td style="width:100px;">{label_when_entered}</td><td style="width:110px;">{when_entered}</td></tr>
                        <tr><td>{label_delivery_date}</td><td>{delivery_date}
                        <tr><td valign=top style="width:80px;">{label_created_by_user_name}</td><td style="width:110px;">{created_by_user_name}</td></tr>
                        </table>
                    </div>
                </fieldset>
            </div>
        </td></tr></table>
        <div style="margin-bottom:3px;clear:both;">Order items:</div>
        <div class="gridContainer">
            <div class="gridHeader">
                <table class="gridTable" cellspacing="0">
                        <tr>
                            <td class="gridHeaderCell" style="width:40px;text-align:center;">ITEM NO.</td>
                            <td class="gridHeaderCell" style="width:70px;text-align:center;">STOCK NUMBER</td>
                            <td class="gridHeaderCell" style="width:300px;text-align:center;">WINE</td>
                            <td class="gridHeaderCell" style="width:60px;text-align:center;">SIZE</td>
                            <td class="gridHeaderCell" style="width:60px;text-align:center;">ALLOC.</td>
                            <td class="gridHeaderCell" style="width:60px;text-align:center;">SOLD</td>
                            <td class="gridHeaderCell" style="width:60px;text-align:center;">AVL.</td>
                            <td class="gridHeaderCell" style="width:80px;text-align:center;">QUANTITY IN UNITS</td>
                            <td class="gridHeaderCell" style="width:70px;text-align:center;">UNIT SELLING PRICE</td>
                            <td class="gridHeaderCell" style="width:70px;text-align:center;">VALUE</td>
                        </tr>
                </table>
            </div>
            <div id="orderItemsGrid" style="overflow:auto; height:86px">
                <table id="orderItemsTable" class="gridTable" cellspacing="0">
                   <!-- START BLOCK : loop_line -->
                    <tr class="{row_style}">
                        <td nowrap class="gridrowCell" style="border-right: 1px #a3b3c0 solid; border-bottom: 1px #a3b3c0 solid;width:40px;text-align:right;" valign="middle">{item_no}</td>
                        <td nowrap class="gridrowCell" style="border-right: 1px #a3b3c0 solid; border-bottom: 1px #a3b3c0 solid;width:70px;text-align:left;" valign="middle">{cspc_code}</td>
                        <td nowrap class="gridrowCell" style="border-right: 1px #a3b3c0 solid; border-bottom: 1px #a3b3c0 solid;width:300px;text-align:left;" valign="middle">{wine_name}</td>
                        <td nowrap class="gridrowCell" style="border-right: 1px #a3b3c0 solid; border-bottom: 1px #a3b3c0 solid;width:60px;text-align:left;" valign="middle">{size}</td>
                        <td nowrap class="gridrowCell" style="border-right: 1px #a3b3c0 solid; border-bottom: 1px #a3b3c0 solid;width:60px;text-align:right;" valign="middle">{allocated}</td>
                        <td nowrap class="gridrowCell" style="border-right: 1px #a3b3c0 solid; border-bottom: 1px #a3b3c0 solid;width:60px;text-align:right;" valign="middle"><span id = "sold[{wine_id}]">{sold}</span></td>
                        <td nowrap class="gridrowCell" style="border-right: 1px #a3b3c0 solid; border-bottom: 1px #a3b3c0 solid;width:60px;text-align:right;" valign="middle"><span id = "available[{wine_id}]">{available}</span></td>
                        <td nowrap class="gridrowCell" style="border-right: 1px #a3b3c0 solid; border-bottom: 1px #a3b3c0 solid;width:80px;text-align:right;" valign="middle">
                            <INPUT TYPE="text" CLASS="orderinput" ID="Order[{wine_id}]" NAME="Order[{wine_id}]" VALUE="{ordered_quantity}" oldvalue="{ordered_quantity}" 
                            price = "{price_per_unit}" litter = "{litter_deposit}"
                            MAXLENGTH="4" SIZE="4" TITLE="Type order quantity" style="text-align:right;" 
                            onKeyPress="return chkMaskINTEGER(this, event);"
                            onFocus="this.select();"
                            onBlur="qtyChanged(this);"
                            />
                        </td>
                        <td nowrap class="gridrowCell" style="border-right: 1px #a3b3c0 solid; border-bottom: 1px #a3b3c0 solid;width:70px;text-align:right;" valign="middle">{price_per_unit}</td>
                        <td nowrap class="gridrowCell" style="border-right: 1px #a3b3c0 solid; border-bottom: 1px #a3b3c0 solid;width:70px;text-align:right;" valign="middle"><span id = "product_subtotal[{wine_id}]">{product_subtotal}</span></td>
                    </tr>
                    <!-- END BLOCK : loop_line -->
                </table>
            </div>
            
        </div>
        <div style="clear:both;width:262px;float:right;">
                <table align="right" cellspacing="0" cellpadding="2" class="orderTable" style="border:#a3b3c0 1px solid;">
                    <tr style="background: #f2f5fa;"><td style="border-right: 1px #a3b3c0 solid; border-bottom: 1px #a3b3c0 solid;width:auto;">PRODUCT SUBTOTAL</td><td style="border-right: 1px #a3b3c0 solid; border-bottom: 1px #a3b3c0 solid;width:100px;text-align:right;"><span id = "order_subtotal">{order_subtotal}</span></td></tr>
                    <tr><td style="border-right: 1px #a3b3c0 solid; border-bottom: 1px #a3b3c0 solid;width:auto;">LICENSEE FACTOR</td><td style="border-right: 1px #a3b3c0 solid; border-bottom: 1px #a3b3c0 solid;width:100px;text-align:right;">&nbsp;<span id = "licensee_factor_total">{licensee_factor}</span></td></tr>
                    <tr><td style="border-right: 1px #a3b3c0 solid; border-bottom: 1px #a3b3c0 solid;width:auto;">AGENCY/LRS FACTOR</td><td style="border-right: 1px #a3b3c0 solid; border-bottom: 1px #a3b3c0 solid;width:100px;text-align:right;">&nbsp;<span id = "Agency_LRS_factor_total">{Agency_LRS_factor}</span></td></tr>
                    <tr style="background: #f2f5fa;"><td style="border-right: 1px #a3b3c0 solid; border-bottom: 1px #a3b3c0 solid;width:auto;">+ S. S. TAX</td><td style="border-right: 1px #a3b3c0 solid; border-bottom: 1px #a3b3c0 solid;width:100px;text-align:right;">&nbsp;</td></tr>
                    <tr><td style="border-right: 1px #a3b3c0 solid; border-bottom: 1px #a3b3c0 solid;width:auto;">+ LITTER DEPOSIT</td><td style="border-right: 1px #a3b3c0 solid; border-bottom: 1px #a3b3c0 solid;width:100px;text-align:right;"><span id = "litter_deposit_total">{litter_deposit_total}</span></td></tr>
                    <tr style="background: #f2f5fa;"><td style="border-right: 1px #a3b3c0 solid; border-bottom: 1px #a3b3c0 solid;width:auto;">- ADJUSTMENT 1</td><td style="border-right: 1px #a3b3c0 solid; border-bottom: 1px #a3b3c0 solid;width:100px;text-align:right;">{adjustment_1}</td></tr>
                    <tr><td style="border-right: 1px #a3b3c0 solid; border-bottom: 1px #a3b3c0 solid;width:auto;">+ ADJUSTMENT 2</td><td style="border-right: 1px #a3b3c0 solid; border-bottom: 1px #a3b3c0 solid;width:100px;text-align:right;">{adjustment_2}</td></tr>
                    <tr style="background: #f2f5fa;"><td style="border-right: 1px #a3b3c0 solid; border-bottom: 1px #a3b3c0 solid;width:auto;"><B>TOTAL</B></td><td style="border-right: 1px #a3b3c0 solid; border-bottom: 1px #a3b3c0 solid;width:100px;text-align:right;"><B><span id = "total_value">{total_value}</span></B></td></tr>
                </table>
        </div>
        <div style="margin-top:2px;width:300px;float:left;">
            <table class="orderTable" border="0">
                <tr><td style="font-family: Verdana;
                    font-size: 8pt;
                    font-weight: light;
                    color: #333333;">{tax_name} included in this order:</td><td align="right" style="font-family: Verdana;
                    font-size: 8pt;
                    font-weight: light;
                    color: #333333;"><span id = "GST">{GST}</span></td></tr>
                <tr><td>{label_deposit}</td><td>{deposit}</td></tr>
                <tr id="trALInoviceNo"><td>{label_AL_invoice_no}</td><td>{AL_invoice_no}</td></tr>
            </table>
        </div>
        <table align="right" style="margin:10px;">
            <tr><td align="right">{btnOK}&nbsp;{btnCancel}</td> </tr>
        </table>
    </div>
    
<script language="javascript">
window.chkDATE =
        function (field, format) {
            var v, re, d, m, y;
            v = field.value;
            if (v.length > 0) {
                //re = /^\d{1,2}(\/|\-|\.)\d{1,2}(\/|\-|\.)\d{4}$/ ;
                //if (!re.test(v))
                //{
                //    return false;
                //}
                m = parseInt(v.substr(0, 2), 10);
                d = parseInt(v.substr(3, 2), 10);
                y = parseInt(v.substr(6, 4), 10);
                binM = (1 << (m-1));
                m31 = 0xAD5;
                if ((y < 1000) || (m < 1) || (m > 12) || (d < 1) || (d > 31) ||
                    ((d == 31 && ((binM & m31) == 0))) ||
                    ((d == 30 && m == 2)) || ((d == 29 && m == 2 && !isLeap(y)))) {
                    return false;
                }
            }
            return true;
        }
function setOrderItemsHeight() 
{
    var nContainer = document.getElementById('orderItemsGrid');
    var windowHeight= getWindowHeight();
    var gap = 171;
    if (windowHeight>0) 
    {
        var listHeight= windowHeight - gap - findPosition(nContainer,0);
        if (listHeight > 20)
            nContainer.style.height =listHeight + "px";
    }
}


addEvent(window, 'load', setOrderItemsHeight);
addEvent(window, 'resize', setOrderItemsHeight);

</script>