<style>
<!--


table {
	font-size:10pt; margin-left:auto;margin-right:auto; border-spacing: 0;border-collapse: collapse;
}

table td{
	height:22px;
}

.table_info{
	width:450px;
	padding-top:80px;
}

.table_inv_info{
	width:550px;
	padding-top:15px;
}

.td_cell {
            border-top:1px solid silver;
         
            border-left:1px solid silver;
          
          
            padding-left:5px;
        }
        
.td_cell_bottom {
           
          border-bottom:1px solid silver;
          
        }
        
.td_cell_right {
     
          border-right:1px solid silver;
          
      
        }

.td_right_algin{
	text-align:right;
	padding-right:5px;
}

.td_center_algin{
	text-align:center;

}

.tr_title td{
	font-weight:bold;
	text-align:center;
}
        
     
-->
</style>


   <div style="height:1000px; width:650px; border:0px solid black;margin-left:auto;margin-right:auto; font-family:'Century Gothic'; font-size:10pt">
  
            <div style="width:100%; padding-top:80px;">
                <div style="float:left; width:50%;"><img id="IMG1" src="resources/images/CSLogo.png" border="0" style="height:85px;width:85px;"></div>

                <div style="float:right;font-size:45px;width:30%; text-align:right;">INVOICE</div>

            </div>

        <div style=" padding-top:80px;">
            <div style="float:left; width:60%;font-style:italic;padding-top:30px;">
                <div>Christopher Stewart Wine & Spirits</div>
                <div style="padding-top:2px; padding-bottom:2px;">197 Murphy Dive</div>
                <div>Delta, BC V4M 3P8</div>
                <div style="padding-top:2px;">604-274-8481</div>
            </div>


            <div style="float:right;width:40%;padding-top:30px;">
                <div style="text-align:right;">Date: {delivery_date}</div>
                <div style="text-align:right;padding-top:2px; padding-bottom:2px;">Invoice #{invoice_number}</div>
                <div style="text-align:right;">{customer_name}</div>
               
                <div style="text-align:right;padding-top:2px; padding-bottom:2px;">{customer_address}</div>
               <div style="text-align:right;">{customer_city}&nbsp;{province}, {postal_code}</div >

            </div>

        </div>  
       
        <div style="float:left; width:100%; margin-left:auto;margin-right:auto;text-align:center; padding-top:80px;">
            <table class="table_info" >
                <tr class="tr_title">
					<td >Sales Consultant</td>
					<td>Delivery Date</td>
					<td>Payment Terms</td>
				</tr>
                <tr >
					<td class="td_cell td_cell_bottom td_center_algin">{user_name}</td>
					<td class="td_cell td_cell_bottom td_center_algin">{delivery_date}</td>
					<td class="td_cell td_cell_bottom td_cell_right td_center_algin">{payment_method}</td>
				</tr>           
            </table>

        </div>
        <div style="float:left; width:100%; margin-left:auto;margin-right:auto;text-align:center;padding-top:15px; ">
            <table  class="table_inv_info">
                <tr class="tr_title" ><td>Qty</td><td>Description</td><td>Order#</td><td>Unit Price</td><td>Line Total</td></tr>
                <tr style="font-weight:normal;">
                    <td class="td_cell">{qty}</td>
                    <td class="td_cell">{product_name}</td>
                    <td class="td_cell">{invoice_number}</td>
                    
                    <td class="td_cell td_right_algin">{unit_price}</td>
                    <td class="td_cell td_cell_right td_right_algin">{line_total}</td>
                </tr>

                <tr style="font-weight:normal;">
                    <td class="td_cell">&nbsp;</td>
                    <td class="td_cell"></td>
                    <td class="td_cell"></td>
                    <td class="td_cell"></td>
                    <td class="td_cell td_cell_right"></td>
                </tr>
                <tr style="font-weight:normal;">
                    <td class="td_cell">&nbsp;</td>
                    <td class="td_cell"></td>
                    <td class="td_cell"></td>
                    <td class="td_cell"></td>
                    <td class="td_cell td_cell_right"></td>
                </tr>
                <tr style="font-weight:normal;">
                    <td class="td_cell">&nbsp;</td>
                    <td class="td_cell"></td>
                    <td class="td_cell"></td>
                    <td class="td_cell"></td>
                    <td class="td_cell td_cell_right"></td>
                </tr>
                <tr style="font-weight:normal;">
                    <td class="td_cell">&nbsp;</td>
                    <td class="td_cell"></td>
                    <td class="td_cell"></td>
                    <td class="td_cell"></td>
                    <td class="td_cell td_cell_right"></td>
                </tr>
                <tr style="font-weight:normal;">
                    <td class="td_cell td_cell_bottom">&nbsp;</td>
                    <td class="td_cell td_cell_bottom"></td>
                    <td class="td_cell td_cell_bottom"></td>
                    <td class="td_cell td_cell_bottom"></td>
                    <td class="td_cell td_cell_right"></td>
                </tr>

                <tr style="font-weight:normal;">
                    <td ></td>
                    <td ></td>
                    <td ></td>
                    <td class="td_right_algin">PST</td>
                    <td class="td_cell td_cell_right td_right_algin">{pst_total}</td>
                </tr>
                <tr style="font-weight:normal;">
                    <td ></td>
                    <td ></td>
                    <td ></td>
                    <td class="td_right_algin">GST</td>
                    <td class="td_cell td_cell_right td_right_algin">{gst_total}</td>
                </tr>
                <tr style="font-weight:normal;">
                    <td ></td>
                    <td ></td>
                    <td ></td>
                    <td class="td_right_algin">Subtotal</td>
                    <td class="td_cell td_cell_right td_right_algin">{sub_total}</td>
                </tr>

                <tr style="font-weight:normal;">
                    <td ></td>
                    <td ></td>
                    <td ></td>
                    <td class="td_right_algin">Delivery</td>
                    <td class="td_cell td_cell_right td_right_algin">{adjustment_1}</td>
                </tr>

                <tr style="font-weight:normal;">
                    <td ></td>
                    <td ></td>
                    <td ></td>
                    <td class="td_right_algin">Total</td>
                    <td class="td_cell td_cell_bottom td_cell_right td_right_algin">{total_amount}</td>
                </tr>


            </table>

        </div>

        <div style="float:left; padding-top:100px;width:100%; text-align:center;">Make all cheques payable to Christopher Stewart Wine & Spirits</div>
        <div style="float:left; padding-top:3px;width:100%; text-align:center;">HST #861256535</div>
        <div style="float:left; padding-top:8px;width:100%; text-align:center;font-weight:bold;">All Sales Are Final</div>
        <div style="float:left; padding-top:3px;width:100%; text-align:center;font-weight:bold;">Thank you for your business!</div>
        <div style="float:left; padding-top:3px;width:100%; text-align:center;font-size:9pt;font-style:italic;">www.christopherstewart.com</div>

 
    </div>
 