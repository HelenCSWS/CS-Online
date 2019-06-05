<style>
<!--

table {
font-family:'Century Gothic'; 	font-size:8pt; margin-left:auto;margin-right:auto; border-spacing: 0;border-collapse: collapse;
}


table td{
	height:22px;
}

.table_info{
	width:450px;
	padding-top:80px;
 
}

.table_inv_info{
	width:650px;
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
.td_widthPrice{
    width:85px;
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
<div style=" width:650px; border:0px solid black;margin-left:auto;margin-right:auto; font-family:'Century Gothic'; font-size:9pt">
    <div style="height:50px;"></div>
    
    <table style="width:650px; padding-top:40px;font-family:'Century Gothic'; font-size:9pt">
    <tr><td><img id="IMG1" src="resources/images/CSLogo.png" style="height:80px;width:80px; "/></td>
    <td style="font-size:42px;width:100%; text-align:right;">BON DE COMMANDE</td></tr>   
    </table>
    
    <div style="height:20px;"></div>
    
    <table id="table_inv_basic_info" style="width:650px; padding-top:40px;font-family:'Century Gothic'; font-size:9pt">
    <tr ><td style="width:40%"> <div>Christopher Stewart Wine & Spirits</div>
        <div style="padding-top:2px; padding-bottom:2px;">197 Murphy Dive</div>
        <div>Delta, BC V4M 3P8</div>
        <div style="padding-top:2px;">604-274-8481</div>
        </td>
    <td>
        <div style="text-align:right;">Date: {delivery_date}</div>
        <div style="text-align:right;padding-top:2px; padding-bottom:2px;"><b>Invoice #{invoice_number}</b></div>
        <div style="text-align:right;">{customer_name}</div>
        
        <div style="text-align:right;padding-top:2px; padding-bottom:2px;">{customer_address}</div>
        <div style="text-align:right;">{customer_city}&nbsp;{province}, {postal_code}</div >
    </td></tr>
    </table>
       
    <div style="float:left; width:100%; margin-left:auto;margin-right:auto;text-align:center; padding-top:80px;">
        <table class="table_info" >
            <tr class="tr_title">
				<td>Consultant en vente</td>
				<td>Date de livraison</td>
				<td>Terme de paiement</td>
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
                <tr class="tr_title" ><td style="width:50px;">Qte</td><td>Description</td><td>Prix unitaire</td><td>Total</td></tr>
 

<!-- START BLOCK : loop_line -->
  <tr style="font-weight:normal;">
                    <td class="td_cell">{qty}</td>
                    <td class="td_cell">{product_name}</td>
                    
                    <td class="td_cell td_right_algin td_widthPrice">{unit_price}</td>
                    <td class="td_cell td_cell_right td_right_algin">{line_total}</td>
                </tr>
<!-- END BLOCK : loop_line -->
  <tr style="font-weight:normal;">
                    <td class="td_cell td_cell_bottom">&nbsp;</td>
                    <td class="td_cell td_cell_bottom"></td>
                    <td class="td_cell td_cell_bottom"></td>
                    <td class="td_cell td_cell_bottom"></td>
                    <td class="td_cell td_cell_right"></td>
                </tr>

              <tr style="font-weight:normal; display:{isDisc}">
                    <td ></td>
                     <td ></td>
                    <td class="td_right_algin">Prix réduit</td>
                    <td class="td_cell td_cell_right td_right_algin">{disc_amount}</td>
                </tr>
               <tr style="font-weight:normal;">
                    <td ></td>
                    <td ></td>
                    <td class="td_right_algin">TPS</td>
                    <td class="td_cell td_cell_right td_right_algin">{pst_total}</td>
                </tr>
                <tr style="font-weight:normal;">
                    <td ></td>
                    <td ></td>
                    <td class="td_right_algin">TVQ</td>
                    <td class="td_cell td_cell_right td_right_algin">{gst_total}</td>
                </tr>
                <tr style="font-weight:normal;">
                    <td ></td>
                    <td ></td>
                    <td class="td_right_algin">Sous Total </td>
                    <td class="td_cell td_cell_right td_right_algin">{sub_total}</td>
                </tr>

                <tr style="font-weight:normal;">
                    <td ></td>
                    <td ></td>
                    <td class="td_right_algin">Livraison</td>
                    <td class="td_cell td_cell_right td_right_algin">{adjustment_1}</td>
                </tr>

                <tr style="font-weight:normal;">
                    <td ></td>
                    <td ></td>
                    <td class="td_right_algin">Total</td>
                    <td class="td_cell td_cell_bottom td_cell_right td_right_algin">{total_amount}</td>
                </tr>


            </table>

        </div>

        <div style="float:left; padding-top:80px;width:100%; text-align:center;">{pst_exempt_no}</div>
        <div style="float:left; padding-top:3px;width:100%; text-align:center;">Chèque payable à l’ordre de:  Christopher Stewart Wine & Spirits</div>
        <div style="float:left; padding-top:3px;width:100%; text-align:center;">HST #861256535</div>
        <div style="float:left; padding-top:8px;width:100%; text-align:center;font-weight:bold;">Toute vente est finale</div>
        <div style="float:left; padding-top:3px;width:100%; text-align:center;font-weight:bold;">Merci!</div>
        <div style="float:left; padding-top:3px;width:100%; text-align:center;font-size:9pt;font-style:italic;">www.christopherstewart.com</div>

 
    </div>
 