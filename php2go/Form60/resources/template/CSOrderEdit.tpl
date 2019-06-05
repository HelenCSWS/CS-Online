	<!-- Form60 : template used in orderedit.class.php -->
	
	
        <div style="text-align: left;">
		{pageid}
        {order_id}
        {GST_factor}
        {PST_factor}
        {estate_id}
        {customer_id}
      
        {isPST}
        {discType}
        {province_id}
        {is_other_delivery}
        

        
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
        </td><td width="33%" align="center" valign="top">
            <div class="invTop" style='width:250px;'>
                <fieldset style="padding:2px;">
                    <div class='invBold'>
                        <table>
                        <tr><td>{label_product_name}</td><td>{product_name}</td></tr>
                        <tr><td>{label_lkup_payment_status_id}</td><td>{lkup_payment_status_id}</td></tr>
                        <tr><td>{label_lkup_payment_type_id}</td><td>{lkup_payment_type_id}</td></tr>
                        <tr><td>{label_lkup_order_status_id}</td><td>{lkup_order_status_id}</td></tr>
                        
                        </table>
                    </div>
                </fieldset>
            </div>
        </td><td width = "33%" align="right" valign="top">
            <div class="invTop" style='width:400px;'>
                <fieldset>
                    <div class='invBold'>
                        <table>
                        <tr><td style="width:100px;">{label_invoice_number}</td><td style="width:180px;">{invoice_number}</td></tr>
                        <tr><td style="width:100px;">{label_when_entered}</td><td style="width:180px;">{when_entered}</td></tr>
                        <tr><td>{label_delivery_date}</td><td style="width:180px;">{delivery_date}</td></tr>
                        <tr><td valign=top style="width:80px;">{label_created_by_user_name}</td><td style="width:180px;">{created_by_user_name}</td></tr>
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
                              <td class="gridHeaderCell" style="width:60px;text-align:center;">ITEM NO.</td>
                          <td class="gridHeaderCell" style="width:300px;text-align:center;">PRODUCT NAME</td>
                            <td class="gridHeaderCell" style="width:100px;text-align:center;">SOLD UNITS</td>
                            <td class="gridHeaderCell" style="width:100px;text-align:center;">SOLD CASES</td>
                            <td class="gridHeaderCell" style="width:100px;text-align:center;">AVA. UNITS</td>
                            <td class="gridHeaderCell" style="width:80px;text-align:center;">QUANTITY IN UNITS</td>
                            <!--td class="gridHeaderCell" style="width:80px;text-align:center;">QUANTITY IN CASES</td -->
                            <td class="gridHeaderCell" style="width:60px;text-align:center;">UNIT SELLING PRICE</td>
                              <td class="gridHeaderCell" style="width:60px;text-align:center;">UNIT SPECIAL PRICE</td>
                            <td class="gridHeaderCell" style="width:60px;text-align:center;">VALUE</td>
                        </tr>
                </table>
            </div>
            <div id="CSorderItemsGrid" style="overflow:auto; height:86px">
                <table id="orderItemsTable" class="gridTable" cellspacing="0">
                   <!-- START BLOCK : loop_line -->
                    <tr class="{row_style}">
                       <td nowrap class="gridrowCell" style="border-right: 1px #a3b3c0 solid; border-bottom: 1px #a3b3c0 solid;width:60px;text-align:center;" valign="middle">{item_no}</td>
                        <td nowrap class="gridrowCell" style="border-right: 1px #a3b3c0 solid; border-bottom: 1px #a3b3c0 solid;width:300;text-align:left;" valign="middle">{product_name}</td>
                        <td nowrap class="gridrowCell" style="border-right: 1px #a3b3c0 solid; border-bottom: 1px #a3b3c0 solid;width:100px;text-align:right;" valign="middle">{sold_btls}</td>
                        <td nowrap class="gridrowCell" style="border-right: 1px #a3b3c0 solid; border-bottom: 1px #a3b3c0 solid;width:100px;text-align:right;" valign="middle">{sold_cs}</td >
                        <td nowrap class="gridrowCell" style="border-right: 1px #a3b3c0 solid; border-bottom: 1px #a3b3c0 solid;width:100px;text-align:right;" valign="middle">{total_units}</td >
                        <td nowrap class="gridrowCell" style="border-right: 1px #a3b3c0 solid; border-bottom: 1px #a3b3c0 solid;width:80px;text-align:right;" valign="middle">  
                        <!-- INPUT TYPE="text" CLASS="orderinput" ID="{item_no}_qtyBtls" NAME="qtyBtls" VALUE="{qty_btls}" oldvalue="{qty_btls}" 
                            
                            cs_product_id="{cs_product_id}" order_item_id="{order_item_id}" cs_product_name="{product_name}"
                            org_price="{price_per_unit}" promotion_price="{promotion_price}"  ava_units="{total_units}" 
                            MAXLENGTH="4" SIZE="8" TITLE="Type order quantity" style="text-align:right;" 
                            onKeyPress="return chkMaskINTEGER(this, event);"
                            onFocus="this.select();"
                            onBlur="changeCsOrdQty(this);"
                            / -->
                            
                            
                            <INPUT TYPE="text" CLASS="orderinput" ID="CSOrder[{cs_product_id}]" NAME="CSOrder[{cs_product_id}]" VALUE="{qty_btls}" oldvalue="{qty_btls}" 
                            
                            cs_product_id="{cs_product_id}" order_item_id="{order_item_id}" cs_product_name="{product_name}"
                            org_price="{price_per_unit}" promotion_price="{promotion_price}"  ava_units="{total_units}" 
                            MAXLENGTH="4" SIZE="8" TITLE="Type order quantity" style="text-align:right;" 
                            onKeyPress="return chkMaskINTEGER(this, event);"
                            onFocus="this.select();"
                            onBlur="changeCsOrdQty(this);"
                            />
                            
                            <INPUT TYPE="hidden"  ID="CSOrder_old[{cs_product_id}]" NAME="CSOrder_old[{cs_product_id}]" VALUE="{qty_btls}" />
                            
							</td>
                        <!--td nowrap class="gridrowCell" style="border-right: 1px #a3b3c0 solid; border-bottom: 1px #a3b3c0 solid;width:80px;text-align:right;" valign="middle">
                          <span id="wl_qty_cs">{qty_cs}</span>
                        </td -->
                        <td nowrap class="gridrowCell" style="border-right: 1px #a3b3c0 solid; border-bottom: 1px #a3b3c0 solid;width:60px;text-align:right;" valign="middle">{price_per_unit}</td>
                        <td nowrap class="gridrowCell" style="border-right: 1px #a3b3c0 solid; border-bottom: 1px #a3b3c0 solid;width:60px;text-align:right;" valign="middle">{promotion_price}</td>
                        <td nowrap class="gridrowCell" style="border-right: 1px #a3b3c0 solid; border-bottom: 1px #a3b3c0 solid;width:60px;text-align:right;" valign="middle"><span id = "{item_no}_itemSubTotal">{cs_product_subtotal}</span></td>
                    </tr>
                    <!-- END BLOCK : loop_line -->
                </table>
            </div>
                 
        </div>
        <div style="clear:both;width:262px;float:right;">
                <table align="right" cellspacing="0" cellpadding="2" class="orderTable" style="border:#a3b3c0 1px solid;">
                    <tr style="background: #f2f5fa;"><td style="border-right: 1px #a3b3c0 solid; border-bottom: 1px #a3b3c0 solid;width:auto;">PRODUCT SUBTOTAL</td><td style="border-right: 1px #a3b3c0 solid; border-bottom: 1px #a3b3c0 solid;width:100px;text-align:right;"><span id = "order_subtotal">{order_subtotal}</span></td></tr>
                 <tr ><td style="border-right: 1px #a3b3c0 solid; border-bottom: 1px #a3b3c0 solid;width:auto;">DISCOUNT PRICE</td><td style="border-right: 1px #a3b3c0 solid; border-bottom: 1px #a3b3c0 solid;width:100px;text-align:right;"><span id = "disc_total">{discount_amount}</span></td></tr>
                       <tr style="background: #f2f5fa;"><td style="border-right: 1px #a3b3c0 solid; border-bottom: 1px #a3b3c0 solid;width:auto;">PST</td><td style="border-right: 1px #a3b3c0 solid; border-bottom: 1px #a3b3c0 solid;width:100px;text-align:right;">&nbsp;<span id = "pst_total">{pst_total}</span></td></tr>
                    <tr><td style="border-right: 1px #a3b3c0 solid; border-bottom: 1px #a3b3c0 solid;width:auto;">GST</td><td style="border-right: 1px #a3b3c0 solid; border-bottom: 1px #a3b3c0 solid;width:100px;text-align:right;">&nbsp;<span id = "gst_total">{gst_total}</span></td></tr>
                    
                
                    <tr style="background: #f2f5fa;"><td style="border-right: 1px #a3b3c0 solid; border-bottom: 1px #a3b3c0 solid;width:auto;">DELVIERY COST</td><td style="border-right: 1px #a3b3c0 solid; border-bottom: 1px #a3b3c0 solid;width:100px;text-align:right;">{adjustment_1}</td></tr>
                    <tr ><td style="border-right: 1px #a3b3c0 solid; border-bottom: 1px #a3b3c0 solid;width:auto;"><B>TOTAL</B></td><td style="border-right: 1px #a3b3c0 solid; border-bottom: 1px #a3b3c0 solid;width:100px;text-align:right;"><B><span id = "total_value">{total_value}</span></B></td></tr>
                </table>
        </div>
         <div style="margin-top:2px;width:900px;float:left;">
       <div style="margin-top:2px;width:250px;float:left; padding-top:8px;">
       <table class="orderTable" border="0"><tr id="pst_row" ><td><input type="checkbox" id="chk_PST" name="chk_PST" checked /></td>
					<td ><span id = "pst_rate_title">PST Rate:<span id = "pst_rate"></td>
					<td align="right"><span id = "pst_rate_val">{pst_rate}</span>&nbsp;{pst_no}</td>
                    <!-- td align="right"><span id = "pst_rate">{pst_rate}</span></td -->
				</tr>
                <tr><td></td>
					<td >GST Rate:</td>
					<td><span id = "gst_rate">{gst_rate}</span></td>
				</tr>
                
                
                 <tr ><td style="padding-top:50px;"><input type="checkbox" id="chk_other_delivery" name="chk_other_delivery" onclick="setOtherDelivery();"/></td>
					<td style="padding-top:50px;" colspan="2"><span >Deliver by other delivery service</td>
				
				</tr>
           
            </table>
        </div>
        <div style="margin-top:8px;width:180px;float:left">
            <table class="orderTable" border="0">
                <tr ><td width="1%"><input type="checkbox" id="chk_Disc" name="chk_Disc" onclick="tglDiscountCtls(this.checked)" /></td>
					<td colspan="2" >Discount</td>
		
                    <!-- td align="right"><span id = "pst_rate">{pst_rate}</span></td -->
				</tr>
                <tr>
                 <td ></td>
					<td width="1%"><input id="discInfo" name="discInfo" onclick="changeDiscType(1)"  class="discRadio disctype1" type="radio" /></td>
					<td><span id="sp_disc_1">$ &nbsp;</span>{disc_1} </td>
				</tr>
               <tr>
                <td ></td>
					<td width="1%"><input id="discInfo" name="discInfo" onclick="changeDiscType(2)" class="discRadio disctype2"  type="radio"  /></td>
					<td><span id="sp_disc_2">% </span>{disc_2}</td>
				</tr>
             
            </table>
        </div>
          <div style="margin-top:8px;width:250px;float:left">
            <table class="orderTable" border="0">
                <tr >
                <td width="1%">Note</td>
				</tr ><tr >	<td colspan="2" >{other_info}</td>
		
                    <!-- td align="right"><span id = "pst_rate">{pst_rate}</span></td -->
				</tr>
               
             
            </table>
        </div>
        
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
function setCSOrderItemsHeight() //????
{
    var nContainer = document.getElementById('CSorderItemsGrid');
    var windowHeight= getWindowHeight();
    var gap = 171;
    if (windowHeight>0) 
    {
        var listHeight= windowHeight - gap - findPosition(nContainer,0);
        if (listHeight > 20)
            nContainer.style.height =listHeight + "px";
    }
}


addEvent(window, 'load', setCSOrderItemsHeight);
addEvent(window, 'resize', setCSOrderItemsHeight);

</script>