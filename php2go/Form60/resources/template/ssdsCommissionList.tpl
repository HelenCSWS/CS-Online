
<!--div class="gridContainer" -->
{current_page}
    <INPUT TYPE="hidden" ID='ListOrderBy_{statusid}' NAME='ListOrderBy_{statusid}' VALUE="{order_by}"/>
    <INPUT TYPE="hidden" ID='ListOrderType_{statusid}' NAME='ListOrderType_{statusid}' VALUE="{order_type}"/>
    <INPUT TYPE="hidden" ID='ListPage_{statusid}' NAME='ListPage_{statusid}' VALUE="{page}"/>

	<table style="display:block" width="100%"  cellpadding="0" cellspacing="0" border="0">
<tr><td style="font-size:8pt"><B>{user_name}</B></td><tr>
<tr>
<!---commission-->
<td width="400">
	<table style="display:block" cellpadding="0" cellspacing="0" border="0">
        <tr bgcolor="#7F9DB9">
             <td nowrap class="mlcolheader" width="10%">Commission levels</td>
             <td nowrap class="mlcolheader_right" width="10%" align="right">{sales_title}</td>

             <td nowrap class="mlcolheader_right" width="10%" align="right">{rate_title}</td>
             <td nowrap class="mlcolheader_right" width="*" align="right">{comm_title}&nbsp;</td>
        </tr>
        <!-- START BLOCK : loop_line -->
        <tr class='mlcellA'>
            <td nowrap class="CPgridrowCell" valign="middle" >{comm_level_desc}</td>
            <td nowrap class="CPgridrowCell_Right" valign="middle">{level_total_cases}</td>
            <td nowrap class="CPgridrowCell_Right" valign="middle" >{comm_rate}</td>
            <td nowrap class="CPgridrowCell_Right" valign="middle"  >${comm_amount}</td>
         </tr>
<!-- END BLOCK : loop_line -->
<!-- sub total -->

		       <tr class='mlcellA' >
		            <td style="BORDER-Top:1px black solid ;">&nbsp;</td><td style="BORDER-Top:1px black solid ;">&nbsp;</td><td nowrap class="CPgridrowCell_Right" valign="middle" style="BORDER-Top:1px black solid ;"><b>Total:</td><td style="BORDER-Top:1px black solid ;" nowrap class="CPgridrowCell_Right" valign="middle" align="right">{sub_total}</td>
		            
		         </tr>
		                <tr class='mlcellA'>
		            <td></td><td></td><td nowrap class="CPgridrowCell_Right" style="display:none;" valign="middle"><b>{second_title}</td><td nowrap class="CPgridrowCell_Right" valign="middle" align="right" style="display:none;">{target_bonus}</td>
		            
		         </tr>
		
		       <tr class='mlcellA'>
		            <td></td><td></td><td nowrap class="CPgridrowCell_Right" valign="middle" style="display:none;"><b>{last_title}</td><td nowrap class="CPgridrowCell_Right" valign="middle" align="right" style="display:none;">{total_bonus}</td>
		            
		         </tr>
		
	    
	</table> 
</td>
<!--total cases sold-->
<td valign="top" width="200" style="padding-left:15">
<table  cellpadding="0" cellspacing="0" border="0">
        <tr bgcolor="#7F9DB9">
             <td nowrap class="mlcolheader" width="200" colspan="2">Total cases sold</td>
        </tr>


        <tr class='mlcellA'>
            <td nowrap class="CPgridrowCell" valign="middle"><b>Total Canadian:</td><td nowrap class="CPgridrowCell_Right" valign="middle" align="right">{total_ca_cases}</td>
            
         </tr>
        <tr class='mlcellA'>
            <td nowrap class="CPgridrowCell" valign="middle" style="BORDER-bottom:1px black solid ;padding-bottom:20px"><b>Total international:</td><td nowrap class="CPgridrowCell_Right" valign="middle" style="BORDER-bottom:1px black solid ;padding-bottom:20px">{total_in_cases}</td>
            
         </tr>
        <tr class='mlcellA'>
            <td nowrap class="CPgridrowCell" valign="middle" ><b>Total cases sold:</td><td nowrap class="CPgridrowCell_Right" valign="middle" >{total_cases}</td>
            
         </tr>

   </table>

</td>

<!---total profit-->
<td valign="top" style="padding-left:15" > 

<table  cellpadding="0" cellspacing="0" border="0">
        <tr bgcolor="#7F9DB9">
             <td nowrap class="mlcolheader" width="200" colspan="2">Total profit</td>
        </tr>


        <tr class='mlcellA'>
            <td nowrap class="CPgridrowCell" valign="middle"><b>Total profit:</td><td nowrap class="CPgridrowCell_Right" valign="middle">${total_profit}</td>
            
         </tr>
        <tr class='mlcellA'>
            <td nowrap class="CPgridrowCell" valign="middle"><b>Ave profit/case:</td><td nowrap class="CPgridrowCell_Right" valign="middle">${ave_profit_per_case}</td>
            
         </tr>
        <tr class='mlcellA'>
            <td nowrap class="CPgridrowCell" valign="middle" style="BORDER-bottom:1px black solid ;padding-bottom:2px"><b>Commission paid:</td><td nowrap class="CPgridrowCell_Right" valign="middle" style="BORDER-bottom:1px black solid ;padding-bottom:2px">{bonus}</td>
            
         </tr>
         
         <tr class='mlcellA'>
            <td nowrap class="CPgridrowCell" valign="middle" ><b>Net profit:</td><td nowrap class="CPgridrowCell_Right" valign="middle">${net_profit}</td>
            
         </tr>

    
</td></tr></table>  
</td>

</tr>

</table>  


	

  
   

   
 


 
