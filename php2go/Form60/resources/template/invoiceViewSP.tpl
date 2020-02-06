

<input TYPE="HIDDEN" id="searchField" value="{search_type}">
<input TYPE="HIDDEN" id="searchValue" value="{search_value}">
<input TYPE="HIDDEN" id="isStart" value="{isStart}">

<input TYPE="HIDDEN" id="order_id" value="{order_id}">
<input TYPE="HIDDEN" id="customer_id" value="{customer_id}">
<input TYPE="HIDDEN" id="inovice_number" value="{inovice_number}">
<input TYPE="HIDDEN" id="user_id" value="{user_id}">
<input TYPE="HIDDEN" id="estate_id" value="{estate_id}">
<input TYPE="HIDDEN" id="payment_type" value="{payment_type}">

<input TYPE="HIDDEN" id="isInnerView" value="{isInnerView}">



<div class="Sub-FrameHeader" >Update {estate_name} invoice</div>
        <div  style="  font-family: verdana, arial, helvetica, sans-serif;
        background-color: white;
                       border-left: #a3b3c0 1px solid;
                    	border-right: #a3b3c0 1px solid;
                          border-bottom: #a3b3c0 1px solid;
                       overflow:auto;
                       backgroundColor:gray;
					 padding-left:30px;
					 padding-top:10px;
    margin-left:20px;
        margin-right:20px;
		height:400px;
		align:middle;"> 
		
		    <table width="600px;" border="0" cellpadding="0" cellspacing="0"  >
	<tr ><td height="25px" style="padding:10px;">
	<fieldset sytle="background-color:white" ><legend class="legend" ><B>Information&nbsp;</B></legend>
	<div style="padding:10px;">
	 <table width="600px;" border="0" cellpadding="0px;" cellspacing="1" >
	
	<tr style="padding:3px;" class="label"><td  style="padding-left:3px;background-color:white;" width="60px;" class="label">Invoice:</td><td  width="60px;" style="padding-left:0px;" class="label"><B>{invoice_number}</B></td>
	<td  style="padding-left:3px;background-color:white;" norwp width="87px;" class="label">Delivery date:&nbsp;</td>
	<td  style="padding-left:0px;" width="80px" class="label" align="left"><B>{delivery_date}</B></td>
	<td  style="padding-left:0px;padding-left:3px;background-color:white;" colspan="0"class="label" width="25px;">License#:&nbsp;</td>
	<td  style="padding-left:0px;" class="label" width="45px;" align="left"><B>{licensee_number}</B></td>
	<td  style="padding-left:0px;padding-left:5px;background-color:white;" width="70px;" colspan="0"class="label">Store type:&nbsp;</td>
	<td  style="padding-left:0px;" colspan="4" class="label" width="*"><B>{license_name}</B></td>
	</tr>    

	<tr style="padding:3px;"><td  width="60px;" style="padding-left:0px;padding-left:3px;background-color:white;" colspan="0" class="label">Customer:&nbsp;</td><td  style="padding-left:0px;" colspan="8" class="label"><B>{customer_name}</B>
	</td></tr>    
	
		<tr style="padding:3px;"><td  style="padding-left:0px;padding-left:3px;background-color:white;" colspan="0" class="label">Address:&nbsp;</td><td  style="padding-left:0px;" colspan="8" class="label"><B>{customer_address}</B>
	</td></tr>    
		</table></fieldset></div>
</td></tr>
<tr style="padding-bottom:10px;"><td align="middle">
<table border="0" cellpadding="0" cellspacing="0" width="630px;">
	<tr><td  style="padding-bottom:0px; " colspan="3">		
        <div style="margin-bottom:3px;clear:both;" class="label"><B>Order items:</B></div>
        <div class="gridContainer">
            <div class="gridHeader">
                <table class="gridTable" cellspacing="0">
                        <tr>
                            <td class="gridHeaderCell" style="width:70px;text-align:left;">SKU</td>
							<td class="gridHeaderCell" style="width:auto;text-align:left;">WINE</td>
                            <td class="gridHeaderCell" style="width:60px;text-align:left;">SIZE</td>                          
                            <td class="gridHeaderCell" style="width:80px;text-align:right;">QUANTITY</td>
                        </tr>
                </table>
            </div>
            <div id="orderItemsGrid" style="overflow:auto; height:86px">
                <table id="orderItemsTable" class="gridTable" cellspacing="0">
                   <!-- START BLOCK : loop_line -->
                    <tr class="{row_style}">
                            <td nowrap class="gridrowCell" style="border-right: 1px #a3b3c0 solid; border-bottom: 1px #a3b3c0 solid;width:70px;text-align:left;" valign="middle">{cspc_code}</td>
                        <td nowrap class="gridrowCell" style="border-right: 1px #a3b3c0 solid; border-bottom: 1px #a3b3c0 solid;width:auto;text-align:left;" valign="middle">{wine_name}</td>
                        <td nowrap class="gridrowCell" style="border-right: 1px #a3b3c0 solid; border-bottom: 1px #a3b3c0 solid;width:60px;text-align:left;" valign="middle">{size}</td>
                       
                        <td nowrap class="gridrowCell" style="border-right: 1px #a3b3c0 solid; border-bottom: 1px #a3b3c0 solid;width:80px;text-align:right;" valign="middle"><span id = "sold[{wine_id}]">{ordered_quantity}</span></td>
                       
                        
                    </tr>
                    <!-- END BLOCK : loop_line -->
                </table>
            </div>
            
        </div>
        
        </td></tr>
        <tr style="padding-top:15px;display:block;"  id="tr_payment_method"><td><fieldset sytle="background-color:white" >
		
		<legend class="legend" style="color:red;"><B>Payment method</B></legend><table border="0">

		<tr style="padding-top:15px;backgroudcolor=red" ><td  style="padding-left:0px; color:red" colspan="3" class="label"></td></tr>    
		<tr style="padding-top:3px;padding-bottom:10px;"><td  style="padding-left:0px;" colspan="3" class="label"><input  type="radio" name="rdoPayType" id="rdoPayType" checked onclick=setPaymentType(3)>Credit Card	<!--img src="resources/images/cd5.jpg" border="0" title = "Credit card" height="70px" width="70px"-->
		
		<input  type="radio" name="rdoPayType" id="rdoPayType"  onclick=setPaymentType(2)>Cheque <!--img src="resources/images/chq1.jpg" border="0" title = "Cheque" -->	
		
				<input  type="radio" name="rdoPayType" id="rdoPayType"  onclick=setPaymentType(1)>Cash
				<!--img src="resources/images/cash.jpg" border="0" title = "Cash" -->
				
					</td></tr>    

	</table></fieldset>
				</td></tr> 
		<!-Delivery status-->	
		<tr style="padding-top:15px;display:block;"  id="tr_delivery_status"><td><table border="0">

		<tr style="padding-top:15px;backgroudcolor=red" ><td  style="padding-left:0px; color:red" colspan="3" class="label"></td></tr>    
		<tr style="padding-top:3px;padding-bottom:10px;"><td  style="padding-left:0px;" colspan="3" class="label"> <B>Delivery Status: </B></td><td  style="padding-left:0px;" colspan="3" class="label"> <select height="1" class="label" id="order_status">
	<option value="2">Delivered</option>
	<option value="1" selected>Pending</option>
</select>
				
					</td></tr>    

	</table>
				</td></tr>  <!-- Delivery status end here -->	
				 </table>
	</td></tr>    
	
	
	<tr><td align="right" style="padding-right: 20px;"><input type="button"  CLASS="btnOK" id="btnSave" onclick="saveInvoice();" value="Save">&nbsp;&nbsp;&nbsp;<input type="button" CLASS="btnOK" id="btnClose" value="Close" onClick="javascript:window.close();">
</td></tr>
	</table>

</div>
<div style="background-image:url(resources/images/csws_graphic.png); background-repeat:no-repeat;background-position:bottom center; height:40px;">&nbsp;</div>
