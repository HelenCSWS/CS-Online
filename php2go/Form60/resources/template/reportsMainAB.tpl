	<!-- Form60 : template used in reportsMainAB.class.php -->
{current_sp_type}{is_sp_current_ava}
{login_user_id}{login_pro}{login_user_level}
<table height="80%" border="0" style="margin-left:auto; margin-right:auto;"><tr><td align="middle" style = "padding-top:90px;" height="220px;">
	     <fieldset style="padding:2px 0px;" >
  	<table cellpadding="0" cellspacing="0" border="0"   >
      		<tr ><td  >

    	<table cellpadding="0" cellspacing="0" border="0"  height="220px" width="350px" >
    	
      		<tr >
    			<td style="padding-top:15px;padding-left:10px;padding-right:10px" valign="top" >
                	<table cellpadding="2px" cellspacing="0" border="0" class="label" style="font-size:8pt">
                      	     <tr id ="trUser"><td style="padding-bottom:8px" valign="top">
					<table cellpadding="0" cellspacing="0" border="0" class="label"><tr><td class="label" > Month</td><td style="padding-left:5px; padding-right:8px;" > {sales_month}</td>
 <td class="label" > Year</td><td style="padding-left:5px" > {sales_year}</td>
                   </tr></table>
				   </td></tr>
                   	     <tr id="trBCE1"><td>
                                <table cellpadding="0" cellspacing="0" border="0" class="label">
                                   <tr>
                                    <td nowrap class="label" style="padding-left:0px;padding-top:1px"><input type="radio" id="searchkey" name="searchKey"  onclick=changeSalesReportType("1") checked></td>
                                    <td nowrap  class="label" style="padding-left:3px;padding-top:1px" >Monthly sales report.</td>
                                    
                                    </tr>
                                </table>
                            </td></tr>   

							 <tr id="trBCE10" ><td style="display:block" id="tdBISalesReport">
                                <table cellpadding="0" cellspacing="0" border="0" class="label">
                                    <tr>
                                    <td nowrap class="label" style="padding-left:0px;padding-top:3px"> <input type="radio" id="searchkey" name="searchKey"  onclick=changeSalesReportType("16") ></td>
                                    <td nowrap  class="label" style="padding-left:3px;padding-top:5px" >Monthly sales summary report on {bi_sale_month} in  {bi_sale_year} </td>
                                  
                                    <td  nowrap class="label" style="padding-left:5px;padding-top:5px" id="td_bi_user_id" >for {bi_user_id}   </td>
                                       </tr>
                                </table>
                            </td></tr>
                            
							 <tr ><td>
                                <table cellpadding="0" cellspacing="0" border="0" class="label">
                                   <tr>
                                    <td nowrap class="label" style="padding-left:0px;padding-top:1px"><input type="radio" id="searchkey" name="searchKey"  onclick=changeSalesReportType("2") ></td>
                                    <td nowrap  class="label" style="padding-left:3px;padding-top:1px" >Monthly sales breakdown reports. {break_type}</td>
                                    
                                    </tr>
                                </table>
                            </td></tr>                              
                            
                             <tr id="trBCE2"><td>
                                <table cellpadding="0" cellspacing="0" border="0" class="label" style="display:block">
                                    <tr>
                                    <td nowrap class="label" style="padding-left:0px;padding-top:5px"><input type="radio" id="searchkey" name="searchKey"  onclick=changeSalesReportType("3") ></td>
                                    <td nowrap  class="label" style="padding-left:3px;padding-top:8px" >{estate_id} sales comission report on {bc_sale_month} in {bc_sale_year}</td>
                                    </tr>
                                </table>
                            </td></tr>
                            
                            <tr id="trBCE3" style="display:none" ><td>
                                <table cellpadding="0" cellspacing="0" border="0" class="label" style="display:none" >
                                    <tr>
                                    <td nowrap class="label" style="padding-left:0px;padding-top:5px"><input type="radio" id="searchkey" name="searchKey"  onclick=changeSalesReportType("4") ></td>
                                    <td nowrap  class="label" style="padding-left:3px;padding-top:8px" >Monthly store penetration report.</td>
                                    </tr>
                                </table>
                            </td></tr>
                            
                            <tr id="trBCE5"><td>
                                <table cellpadding="0" cellspacing="0" border="0" class="label">
                                    <tr>
                                    <td nowrap class="label" style="padding-left:0px;padding-top:0px"><input type="radio" id="searchkey" name="searchkey"  onclick=changeSalesReportType("15")></td>
                                    <td nowrap  class="label" style="padding-left:3px;padding-top:8px" >Export case values.</td>
                                    </tr>
                                </table>
                            </td></tr>
                            
                            <tr id="trBCE4"><td style="display:none">
                                <table cellpadding="0" cellspacing="0" border="0" class="label">
                                    <tr>
                                    <td nowrap class="label" style="padding-left:0px;padding-top:5px"><input type="radio" id="searchkey" name="searchKey"  onclick=changeSalesReportType("5")></td>
                                    <td nowrap  class="label" style="padding-left:3px;padding-top:6px" >Montly allocation report.</td></table></td></tr></table>
                			</td></tr>
                			
                			
                            
                		
    		
   	</table></td></tr>
	   
         </table>
	</fieldset>
   
    </td></tr>
    <tr><td valign="top"> <table width="100%"  border="0">
    		<tr>
     			<td colspan="2" align="right" width="98%" ><input style="font-size:8pt;width=100;display:none" type="button" value="Export" name="btnExcel" id="btnExcel" title="Export to excel" onclick=createReports(3) /></td>
				  <td width="*" style="padding-bottom:5px; ">
				  
				  <input style="font-size:8pt;width=100" type="button" value="View" name="btnprint" id="btnprint" title="Preview / Print" onclick=createABSalesReports(0) /></td><td width="*" style="padding-bottom:5px;padding-right:2px;"><input type="button" value="Close" name="btnClose" style="font-size:8pt;width=100"  id="btnClose" title="Close" onclick=createABSalesReports(1) /></td>
   		</tr>
         </table>
     </td>
   		</tr>
 	{changekey}
</table> 