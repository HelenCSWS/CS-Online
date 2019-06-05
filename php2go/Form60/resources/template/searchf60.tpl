{search_id}{search_id_w}{isWine}{contact_key}{isQtr}{isStart}{province_id}
<table border="0" cellpadding="0" cellspacing="0" width="99%">
<tr id="showWine" name="showWine" cellpadding="0" cellspacing="0"><td style="padding-top:100px" align="center">
<fieldset style="padding:0px 0px 0px 0px;width:656px" >
<table border="0" cellpadding="0" cellspacing="0"  width="656px;" >
<tr cellpadding="0" cellspacing="0"><td style="padding-top:0px" >
<table width="100%" border="0" cellpadding="0" cellspacing="0" >
            <tr class="tab">
                <td style="width:auto;border-bottom: 1px solid #a3b3c0;" ><b>&nbsp;</b></td>
                <td id ="tab0" align= "center" class="tab" onclick="changesearchTab(0)" width="49%"><b>Customers</b></td>
                <td id ="tab1" align= "center" class="tab" onclick="changesearchTab(1)" width="49%"><b>Products</b></td>
               
                <td style="width:auto;border-bottom: 1px solid #a3b3c0;"><b>&nbsp;</b></td>
            </tr>
        </table>
</td></tr>

<tr id="trSrchCM" name="trSrchCM" style="display:none"> <td colspan=2 style="padding-top:10px;padding-left:15px">


<table cellpadding="0" cellspacing="0" border="0" width="630" height="289"><tr><td align="middle" valign="top"  >
    <tr><td>
                	<table cellpadding="0" cellspacing="0" border="0" class="label">
                      <!--store type & start with-->
                        <tr><td colspan="4">
                        
                        
                       <table cellpadding="0" cellspacing="0" border="0" class="label" height="30"><tr><td><input type="checkbox" id="chkstoretype" name="chkstoretype"  onclick=enableStoretype(0) ></td><td class="label"> Store type</td><td style="padding-left:11px" > {lkup_store_type_id}</td><td style="padding-left:87px"> <input type="checkbox" id="startwith" name="startwith" onclick=setFocus2Text(0)></td><td class="label"> Starts with</td>
								<td style="padding-left:17px"> <input type="checkbox" id="chkisOOB" name="chkisOOB" onclick=setFocus2Text()></td><td class="label"> OOB Customers</td></tr></table>
								
								
								
								</td></tr>
                        
                        <!--Assign to & search for-->
                        <tr><td style="padding-top:0px">	<table cellpadding="0" cellspacing="0" border="0" class="label"><tr >
                    <td style="padding-left:0px"> <input type="checkbox" id="chkAssign" name="chkAssign"  onclick=enableStoretype(1) ></td>
                    <td class="label" nowrap> Assigned to</td><td style="padding-left:5px" > {user_id}</td>
<td nowrap style="padding-left:25px">{label_search_field}&nbsp;</td><td id="edt">{search_field}</td>
  <td id="cmd"  style="display:none">{estate_id1}</td>
 <td sytle="paddingtop:0px">&nbsp;{btnSearch}</td>
                   </tr></table></td></tr>
                        
                        <!--customre-->
                    	<tr>

                        <td class="label" style="padding-top:3px"><input type="radio" id="searchKey" name="searchKey" value="" onclick=changeKey("0")>Customer</td></tr>
                        
                       <tr><td colspan=5 style="padding-top:5px;padding-bottom:3px">
                      <table cellpadding="0" cellspacing="0" border="0" class="label"><tr><td nowrap class="label" checked><input type="radio" id="searchkey" name="searchKey"  onclick=changeKey("1")>Contact</td><td  style="padding-left:8px;padding-top:1px" >{contact}</td><td style="padding-left:8px;padding-top:1px" class="label"> name</td>
                       <td><table cellpadding="0" cellspacing="0" border="0" class="label" height="30"></table></td>
                       </tr></table>

                       </td></tr>
                  
                       <tr><td nowrap style="display:none" class="label" checked><input type="radio" id="searchkey" name="searchKey"  onclick=changeKey("2")>Estate</td></tr>

                      	<tr><td nowrap class="label" style="padding-top:1px;padding-bottom:0px" id="td_bc_invoice"><input type="radio" id="searchKey" name="searchKey"  onclick=changeKey("3")  >Invoice number for {estate_id} </td></tr>
                    	<tr><td nowrap class="label" style="padding-top:5px;padding-bottom:0x" ><input type="radio" id="searchKey" name="searchKey"  onclick=changeKey("4") >Store number</td></tr>
                    	<tr><td nowrap class="label" style="padding-top:5px;padding-bottom:0px"><input type="radio" id="searchKey" name="searchKey"  onclick=changeKey("10") >Phone number</td></tr>
                    	
                    	<tr><td nowrap class="label" style="padding-top:5px;padding-bottom:0px"><input type="radio" id="searchKey" name="searchKey"  onclick=changeKey("11") >Street name</td></tr>
                    	
                    	<tr><td nowrap class="label" style="padding-top:5px;padding-bottom:5px"><input type="radio" id="searchKey" name="searchKey"  onclick=changeKey("12") >City</td></tr>
                    	<tr style="display:none"><td nowrap class="label" style="padding-top:0px;padding-bottom:5px"> <input type="radio" id="searchKey" name="searchKey" onclick=changeKey("5") >Search for OOB customers</td></tr>
                    
                	</table>
 </td>
    		</tr>
        </table>
        
      
      
      
      
      
   <!-- search product -->
   
   </td></tr>
   
   <tr id="trSrchWine" name="trSrchWine" style="display:none"> <td colspan=2 style="padding-top:17px;padding-left:15px">


<table cellpadding="0" cellspacing="0" border="0" width="610" height="282"><tr><td align="middle" valign="top"  >
<tr><td valign="top" align="left" >
<table cellpadding="0" cellspacing="0" border="0" >
<tr><td><table cellpadding="0" cellspacing="0" border="0" class="label"><tr>
<td class="label">{product_id}
<!-- input type="radio" id="chkProduct" name="chkProduct" checked onclick=setProductId("1")  />Wine</td><td style="padding-left:20px" class="label"><input type="radio" id="chkProduct" name="chkProduct"  onclick=setProductId("2")  />Beer -->

</td></tr>
</table>

</td></tr>
<tr><td valign="top" align="left" >
                	<table cellpadding="0" cellspacing="0" border="0" class="label" >
                	
                		 <!--period-->	
								<tr><td style="padding-top:10px">
								<table cellpadding="0" cellspacing="0" border="0" class="label">
								
								<tr>
	<td style="padding-left:0px;padding-top:opx" nowarp width="260px;" ><LABEL FOR="sales_month" ID="lbl_sales_month_label" CLASS="label" >Month</LABEL>&nbsp;{sales_month}</td><td width="260px;"><LABEL FOR="sales_year" ID="lbl_sales_year_label" CLASS="label" style="margin-left:8px;">Year</LABEL>&nbsp;{sales_year}	</td><td class="label" style="padding-left:10px;padding-top:1px" width="20px"><input type="checkbox" id="chkQut" checked name="chkQut" onclick=changeSearchPeriod() style="mergin-left:50px"></td>
	<td class="label" style="padding-left:1px;padding-top:1px" nowrap width="90" nowrap>List by Quarter </td><td style="padding-left:1px;padding-top:1px" width="1%">{sales_qut}</td><td style="padding-left:5px">{quarter_desc}</td> <td width="*" align="right" style="padding-right:8px;padding-top:1px; display:none" id="tdFlip" ></td></tr>
	<tr>
	<td colspan="6" class="label" style="padding-left:0px;padding-top:5px; display:none" width="120px"><input type="checkbox" id="chkNoDate" checked name="chkNoDate" onclick=setWithoutDate()>Without date </td>
	</tr></table>
	
	
	</td></tr>
								</table></td></tr>

                      <!--store type & start with-->
                        <tr><td colspan="10" style="padding-top:10px">
                       <table cellpadding="0" cellspacing="0" border="0" class="label" height="30"><tr><td></td><td class="label"> Store type</td><td style="padding-left:11px" > {lkup_store_type_id_w}</td><td style="padding-left:87px"> <input type="checkbox" id="startwith_w" name="startwith_w" onclick=setFocus2Text(1)></td><td class="label"> Starts with</td>
								<td style="padding-left:17px">&nbsp;</td><tr></table>
								</td></tr>								
								
							  <!--Assign to & search for-->
                        <tr><td style="padding-top:0px" colspan="10">	<table cellpadding="0" cellspacing="0" border="0" class="label"><tr >
                    <td style="padding-left:0px"> </td>
                    <td class="label" nowrap> Assigned to</td><td style="padding-left:5px" > {user_id_w}</td>
<td nowrap style="padding-left:25px">{label_search_field_w}&nbsp;</td><td id="edt">{search_field_w}</td>
  <td id="cmd"  style="display:none">{estate_id1}</td>
 <td sytle="paddingtop:1px">&nbsp;{btnSearch_w}</td>
                   </tr></table></td></tr>
                      
                        
                        <!--option buttons-->
                    	<tr>

                        <td class="label" style="padding-top:20px"><input type="radio" checked id="searchKey_w" name="searchKey_w" value="" onclick=changeKey_w("0")>Who has {is_purchased} in {city} (city)</td></tr>
                         
                         
                       <tr><td colspan=5 style="padding-top:8px;padding-bottom:3px">
                      <table cellpadding="0" cellspacing="0" border="0" class="label"><tr><td nowrap class="label" checked><input type="radio" id="searchKey_w" name="searchKey_w"  onclick=changeKey_w("1")>Top</td><td  style="padding-left:8px;padding-top:1px" >{cm_number}</td><td style="padding-left:8px;padding-top:1px" class="label"> customers</td>
                         </tr>
							  
							  
							  </table>

                       </td></tr>                      	<tr><td nowrap class="label" style="padding-top:8px;padding-bottom:0px"><input type="radio" id="searchKey_w" name="searchKey_w"  onclick=changeKey_w("2")  >Top &nbsp;{wine_number} &nbsp;selling &nbsp;{lkup_wine_color_type_id} &nbsp;products</td></tr>
                      	
                    	<tr><td nowrap class="label" style="padding-top:8px;padding-bottom:0x" ><input type="radio" id="searchKey_w" name="searchKey_w"  onclick=changeKey_w("3") >Total sales by &nbsp;{sku_name}</td></tr></table>
 </td>
    		</tr>
        </table>
      
   <!-- end -->
   
   </td></tr>

                 </table>
                </legend>
                </fieldset>
                </td></tr>     
    <tr>
    	<td align="middle" style="padding-top:18px;"><div style="width:626px; text-align:right">{btnClose}<div></td>
    </tr>

        </table> 
       
