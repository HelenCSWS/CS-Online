	<!-- Form60 : template used in userAdd.class.php -->
{current_sp_type}{is_sp_current_ava}{login_user_id}{login_pro}{login_user_level}
  <div id="no_ava" class="not-ava" style="padding-top:300px;">Coming soon!</div>
  
  <table height="80%" border="0" style="margin-left:auto; margin-right:auto;"><tr><td align="middle" valign="center"  id="westen_table">
	
     <fieldset style="padding:0px 0px 0px 0px;">

      
    	<table cellpadding="0" cellspacing="0" border="0"  height="295px" width="1050px">
    	<tr cellpadding="0" cellspacing="0" height="10px"><td style="padding-top:0px" align="center" valign="top">
			<table width="100%" border="0" cellpadding="0" cellspacing="0" width="1000px">
            <tr class="tab">
                <td style="width:auto;border-bottom: 1px solid #a3b3c0;"><b>&nbsp;</b></td>
                <td id ="tab0" align= "center" class="tab" onclick="changeRepTab(0)"><b>BC Estates</b></td>
                <td id ="tab1" align= "center" class="tab" onclick="changeRepTab(1)"><b>BCLDB</b></td>
                <td id ="tab2" align= "center" class="tab" onclick="changeRepTab(2)"><b>Miscellaneous</b></td>              
                <td style="width:auto;border-bottom: 1px solid #a3b3c0;"><b>&nbsp;</b></td>
            </tr>
        </table>
        </td></tr>
      		<tr >
    			<td style="padding:10px" valign="top" >
                	<table cellpadding="2px" cellspacing="0" border="0" class="label" style="font-size:8pt">
                      	     <tr id ="trUser"><td style="padding-bottom:8px" valign="top">
										
										<table cellpadding="0" cellspacing="0" border="0" class="label"><tr >
                    <td style="padding-left:0px"> <input type="checkbox" id="chkAssign" name="chkAssign" ></td>
                    <td class="label" > Assigned to</td><td style="padding-left:5px" > {user_id}</td>
 
                   </tr></table></td><tr>
                   
                   
                   	     <tr id="trBCE1"><td>
                                <table cellpadding="0" cellspacing="0" border="0" class="label">
                                   <tr>
                                    <td nowrap class="label" style="padding-left:0px;padding-top:1px"><input type="radio" id="searchkey" name="searchKey"  onclick=changeReportType("1") checked></td>
                                    <td nowrap  class="label" style="padding-left:3px;padding-top:1px" >Find all invoices</td>
                                    <td nowrap class="label" style="padding-left:5px;padding-top:1px" > for </td>
                                    <td nowrap class="label" style="padding-left:5px;padding-top:1px" >{estate_id_1}</td>
                                    <td nowrap class="label" style="padding-left:5px;padding-top:1px" >from </td>
                                    <td nowrap class="label" style="padding-left:5px;padding-top:1px" >{from_1}</td>
                                    <td nowrap class="label" style="padding-left:2px;padding-top:1px" >to </td>
                                    <td nowrap class="label" style="padding-left:5px;padding-top:1px" > {to_1} </td>
                                    </tr>
                                </table>
                            </td></tr>                            
                            
                             <tr id="trBCE2"><td>
                                <table cellpadding="0" cellspacing="0" border="0" class="label">
                                    <tr>
                                    <td nowrap class="label" style="padding-left:0px;padding-top:5px"><input type="radio" id="searchkey" name="searchKey"  onclick=changeReportType("2") ></td>
                                    <td nowrap  class="label" style="padding-left:3px;padding-top:8px" >Who has</td>
                                    <td  nowrap class="label" style="padding-left:4px;padding-top:8px" >{searchType_2}</td>
                                    <td  nowrap class="label" style="padding-left:5px;padding-top:8px" > for </td>
                                    <td  nowrap class="label" style="padding-left:5px;padding-top:8px" >{estate_id_2}</td>
                                    <td  nowrap class="label" style="padding-left:5px;padding-top:8px" >from </td>
                                    <td  nowrap class="label" style="padding-left:5px;padding-top:8px" >{from_2}</td>
                                    <td  nowrap class="label" style="padding-left:2px;padding-top:8px" >to </td>
                                    <td  nowrap class="label" style="padding-left:5px;padding-top:8px" > {to_2} </td>
                                    </tr>
                                </table>
                            </td></tr>
                            
                            <tr id="trBCE3"><td>
                                <table cellpadding="0" cellspacing="0" border="0" class="label">
                                    <tr>
                                    <td nowrap class="label" style="padding-left:0px;padding-top:5px"><input type="radio" id="searchkey" name="searchKey"  onclick=changeReportType("3")></td>
                                    <td nowrap  class="label" style="padding-left:3px;padding-top:6px" >Who has</td>
                                    <td  nowrap class="label" style="padding-left:4px;padding-top:8px" >{searchType_3}</td>
                                    <td  nowrap class="label" style="padding-left:5px;padding-top:8px" > their allocations for </td>
                                    <td  nowrap class="label" style="padding-left:5px;padding-top:8px" >{estate_id_3}</td>
                                    <td  nowrap class="label" style="padding-left:5px;padding-top:8px" >from </td>
                                    <td  nowrap class="label" style="padding-left:5px;padding-top:8px" >{from_3}</td>
                                    <td  nowrap class="label" style="padding-left:2px;padding-top:8px" >to </td>
                                    <td  nowrap class="label" style="padding-left:5px;padding-top:8px" > {to_3} </td>
                                    </tr>
                                </table>
                            </td></tr>
                            
                            <tr id="trBCE4" style="display:none"><td>
                                <table cellpadding="0" cellspacing="0" border="0" class="label">
                                    <tr>
                                    <td nowrap class="label" style="padding-left:0px;padding-top:5px"><input type="radio" id="searchkey" name="searchKey"  onclick=changeReportType("4")></td>
                                    <td nowrap  class="label" style="padding-left:3px;padding-top:6px" >Print complete inventory for </td>
                                    <td  nowrap class="label" style="padding-left:5px;padding-top:8px" >{estate_id_4}</td>
                                    </tr>
                                </table>
                            </td></tr>
                            
                            <tr id="trBCE5" style="display:none"><td>
                                <table cellpadding="0" cellspacing="0" border="0" class="label">
                                    <tr>
                                    <td nowrap class="label" style="padding-left:0px;padding-top:5px"><input type="radio" id="searchkey" name="searchKey"  onclick=changeReportType("5")></td>
                                    <td nowrap  class="label" style="padding-left:3px;padding-top:6px" >Allocation summary for </td>
                                    <td  nowrap class="label" style="padding-left:5px;padding-top:8px" >{estate_id_5}</td>
                                 <td  nowrap class="label" style="padding-left:5px;padding-top:8px" >by </td>
                                    <td  nowrap class="label" style="padding-left:5px;padding-top:8px" >{wine_id_5} </td>
                                       </tr>
                                </table>
                            </td></tr>
                            
                            
                            
                            <tr id="trBCE6"><td>
                                <table cellpadding="0" cellspacing="0" border="0" class="label">
                                    <tr>
                                    <td nowrap class="label" style="padding-left:0px;padding-top:5px"><input type="radio" id="searchkey" name="searchKey"  onclick=changeReportType("6")></td>
                                    <td nowrap  class="label" style="padding-left:3px;padding-top:6px" >Print report by </td>
                                    <td  nowrap class="label" style="padding-left:5px;padding-top:8px" >{store_type_id}</td>
                                    <td  nowrap class="label" style="padding-left:5px;padding-top:8px" > for </td>
                                    <td  nowrap class="label" style="padding-left:5px;padding-top:8px" >{estate_id_6}</td>
                                    <td  nowrap class="label" style="padding-left:5px;padding-top:8px" >by </td>
                                    <td  nowrap class="label" style="padding-left:5px;padding-top:8px" >{wine_id_6} </td>
                                    <td  nowrap class="label" style="padding-left:5px;padding-top:8px" >from </td>
                                    <td  nowrap class="label" style="padding-left:5px;padding-top:8px" >{from_6}</td>
                                    <td  nowrap class="label" style="padding-left:2px;padding-top:8px" >to </td>
                                    <td  nowrap class="label" style="padding-left:5px;padding-top:8px" > {to_6} </td>
                                    </tr>
                                </table>
                            </td></tr>
                            
                            <tr id="trBCE9"><td>
                                <table cellpadding="0" cellspacing="0" border="0" class="label">
                                    <tr>
                                    <td nowrap class="label" style="padding-left:0px;padding-top:5px"><input type="radio" id="searchkey" name="searchKey"  onclick=changeReportType("14")></td>
                                    <td nowrap  class="label" style="padding-left:3px;padding-top:6px" >Print report by </td>
                                    <td  nowrap class="label" style="padding-left:5px;padding-top:8px" >{store_type_id_city}</td>
                                    <td  nowrap class="label" style="padding-left:5px;padding-top:8px" > for </td>
                                    <td  nowrap class="label" style="padding-left:5px;padding-top:8px" >{estate_id_city}</td>
                                    
                                    <td  nowrap class="label" style="padding-left:5px;padding-top:8px" >from </td>
                                    <td  nowrap class="label" style="padding-left:5px;padding-top:8px" >{from_city}</td>
                                    <td  nowrap class="label" style="padding-left:5px;padding-top:8px" >to </td>
                                    <td  nowrap class="label" style="padding-left:5px;padding-top:8px" > {to_city} </td>
                                    <td  nowrap class="label" style="padding-left:5px;padding-top:8px" >in</td>
                                    <td  nowrap class="label" style="padding-left:3px;padding-top:8px" >{city} </td>
                                    </tr>
                                </table>
                            </td></tr>
                            
                            
                            <tr id="trBCE8" ><td style="display:none" id="tdSalesReport">
                                <table cellpadding="0" cellspacing="0" border="0" class="label">
                                    <tr>
                                    <td nowrap class="label" style="padding-left:0px;padding-top:5px"> <input checked type="radio" id="searchkeyM" name="searchKeyM"  onclick=changeReportType("13") ></td>
                                    <td nowrap  class="label" style="padding-left:3px;padding-top:6px" >Monthly Sales report for  </td>
                                    <!--td  nowrap class="label" style="padding-left:5px;padding-top:8px" >{estate_id_sales} on {bc_sale_month} in <span id="current_year">2010</span></td -->
                                    <td  nowrap class="label" style="padding-left:5px;padding-top:8px" >{estate_id_sales} on {bc_sale_month} in  {bc_sale_year}  </td>
                                       </tr>
                                </table>
                            </td></tr>
                            
                            
                                <tr id="trBCE11"><td style="display:none; padding-top:10px;padding-bottom:5px;"  id="tdCSSalesReport">
                                <table cellpadding="0" cellspacing="0" border="0" class="label">
                                   <tr>
                                    <td nowrap class="label" style="padding-left:0px;padding-top:1px"><input type="radio" id="searchKeyM" name="searchKeyM"  onclick=changeReportType("18")></td>
                                    <td nowrap  class="label" style="padding-left:3px;padding-top:1px" >Find all invoices</td>
                                    <td nowrap class="label" style="padding-left:5px;padding-top:1px" > for </td>
                                    <td nowrap class="label" style="padding-left:5px;padding-top:1px" >{cs_estate_id_sales}</td>
                                    <td nowrap class="label" style="padding-left:5px;padding-top:1px" >from </td>
                                    <td nowrap class="label" style="padding-left:5px;padding-top:1px" >{from_cs}</td>
                                    <td nowrap class="label" style="padding-left:2px;padding-top:1px" >to </td>
                                    <td nowrap class="label" style="padding-left:5px;padding-top:1px" > {to_cs} </td>
                                    </tr>
                                </table>
                            </td></tr>            
                            
                               
                             <!-- tr id="trBCE7" ><td style="display:none" id="tdOverdue">
                                <table cellpadding="0" cellspacing="0" border="0" class="label">
                                    <tr>
                                    <td nowrap class="label" style="padding-left:0px;padding-top:5px"><input type="radio" id="searchkeyM" name="searchKeyM"  onclick=changeReportType("7")></td>
                                    <td nowrap  class="label" style="padding-left:3px;padding-top:6px" >Accounts receivable summary for  </td>
                                    <td  nowrap class="label" style="padding-left:5px;padding-top:8px" >{estate_id_overdue} of {overdue_type}</td>
                                       </tr>
                                </table>
                            </td></tr -->
                            
                             <tr id="trBCE7" ><td style="display:none" id="tdOverdue">
                                <table cellpadding="0" cellspacing="0" border="0" class="label">
                                    <tr>
                                    <td nowrap class="label" style="padding-left:0px;padding-top:5px"><input type="radio" id="searchkeyM" name="searchKeyM"  onclick=changeReportType("17")></td>
                                    <td nowrap  class="label" style="padding-left:3px;padding-top:6px" >Custom Sales Summary report for </td>
                                    <td  nowrap class="label" style="padding-left:5px;padding-top:8px" >{estate_id_sm} on {bc_sale_month_sm} in  {bc_sale_year_sm} </td>
                                       </tr>
                                </table>
                            </td></tr >
                            
                            
                               <tr id="trBCE10" ><td style="display:block" id="tdBISalesReport">
                                <table cellpadding="0" cellspacing="0" border="0" class="label">
                                    <tr>
                                    <td nowrap class="label" style="padding-left:0px;padding-top:5px"> <input type="radio" id="searchkeyM" name="searchKeyM"  onclick=changeReportType("16") ></td>
                                    <td nowrap  class="label" style="padding-left:3px;padding-top:6px" >Monthly sales summary report on {bi_sale_month} in  {bi_sale_year} </td>
                                  
                                    <td  nowrap class="label" style="padding-left:5px;padding-top:8px" id="td_bi_user_id" >for {bi_user_id}   </td>
                                       </tr>
                                </table>
                            </td></tr>
                            
                                                     
                            <tr id="trBC1"  ><td valign="top">
                                <table cellpadding="0" cellspacing="0" border="0" class="label" width="">  
                                <tr style="display:none;"><td class="label" colspan="5" style="padding-left:0px;padding-top:5px;padding-bottom:3px;" ><input type="checkbox" id="chkSPLocation" name="chkSPLocation" onclick="checkSPLocation();"> Locaiton&nbsp;&nbsp;{sp_location_type}{sp_location_name}</td></tr>
										   <tr>
                                    <td nowrap class="label" style="padding-left:0px;padding-top:5px" width="1%"><input type="radio" id="searchkeyBC" name="searchKeyBC"  onclick=changeReportType("9") checked></td>
                                    <td nowrap  class="label" style="padding-left:3px;padding-top:6px" width="1%">Store penetration report for the month of</td>
                                    <td  nowrap class="label" style="padding-left:5px;padding-top:8px" width="1%">{sp_report_month}</td>
                                    <td  nowrap class="label" style="padding-left:5px;padding-top:8px" width="1%"> year </td>
                                    <td  nowrap class="label" style="padding-left:5px;padding-top:8px" width="*">{sp_report_year}</td>
                                    
                                    </tr>
                                </table>
                            </td></tr>
                            
                            <tr id="trBC2"><td>
                                <table cellpadding="0" cellspacing="0" border="0" class="label">                                    <tr>
                                    <td nowrap class="label" style="padding-left:0px;padding-top:5px"><input type="radio" id="searchkeyBC" name="searchKeyBC"  onclick=changeReportType("11")></td>
                                    <td nowrap  class="label" style="padding-left:3px;padding-top:6px" >Store penetration report for today</td>                               </tr>
                                </table>
                            </td></tr>
                            
                            
                            <tr id="trCCInfo" style="display:block;"><td>
                                <table cellpadding="0" cellspacing="0" border="0" class="label">
                                    <tr>
                                    <td nowrap class="label" style="padding-left:0px;padding-top:5px"><input type="radio" id="searchkeyM" name="searchKeyM"  onclick=changeReportType("10")></td>
                                    <td nowrap  class="label" style="padding-left:3px;padding-top:6px" >Export customer credit cards list for {estate_id_cc}</td>
                                    </tr>
                                </table>
                            </td></tr>
                            
                            <tr id="trExpCCInfo" style="display:block;"><td>
                                <table cellpadding="0" cellspacing="0" border="0" class="label">
                                    <tr>
                                    <td nowrap class="label" style="padding-left:0px;padding-top:5px"><input type="radio" id="searchkeyM" name="searchKeyM"  onclick=changeReportType("12")></td>
                                    <td nowrap  class="label" style="padding-left:3px;padding-top:6px" >Export expired credit cards</td>
                                    </tr>
                                </table>
                            </td></tr>
                            
                            <tr id="trCaseValue" style="display:block;"><td>
                                <table cellpadding="0" cellspacing="0" border="0" class="label">
                                    <tr>
                                    <td nowrap class="label" style="padding-left:0px;padding-top:5px"><input type="radio" id="searchkeyM" name="searchKeyM"  onclick=changeReportType("15")></td>
                                    <td nowrap  class="label" style="padding-left:3px;padding-top:6px" >Export case values</td>
                                    </tr>
                                </table>
                            </td></tr>
                            
                              <tr id="trM1"><td>
                                <table cellpadding="0" cellspacing="0" border="0" class="label">
                                    <tr>
                                    <td nowrap class="label" style="padding-left:0px;padding-top:5px"><input type="radio" id="searchkeyM" name="searchKeyM"  onclick=changeReportType("8") ></td>
                                    <td nowrap  class="label" style="padding-left:3px;padding-top:6px" >Print report for </td>
                                    <td  nowrap class="label" style="padding-left:5px;padding-top:8px" >{estate_id_8}</td>
                                    </tr>
                                </table>
                            </td></tr>
                            
                          
                	</table>
                </td>
    		</tr>
         </table>
       
        </fieldset>
   <table width="100%"  border="0">
    		<tr>
     			<td colspan="2" align="right" width="98%" ><input style="font-size:8pt;width=100;display:none" type="button" value="Export" name="btnExcel" id="btnExcel" title="Export to excel" onclick=createReports(3) /></td>
				  <td width="*" style="padding-right:0px">
				  
				  <input style="font-size:8pt;width=100" type="button" value="View" name="btnprint" id="btnprint" title="Preview / Print" onclick=createReports(1) /></td><td width="*" style="padding-right:0px"><input type="button" value="Close" name="btnClose" style="font-size:8pt;width=100"  id="btnClose" title="Close" onclick=createReports(2) /></td>
   		</tr>
   	</table>
    </td></tr>
 	{changekey}
</table> <!-- location table end here-->
<SCRIPT LANGUAGE="JavaScript" TYPE="text/javascript">
<!--
window.chkDATE =
        function (field, format) {
            var v, re, d, m, y;
            v = field.value;
            (format != null && (format == 'EURO' || format == 'US')) || (format =='EURO');
            if (v.length > 0) {
                //((format == 'EURO' || format == 'US') ? re = /^\d{1,2}(\-|\/|\.)\d{1,2}\1\d{4}$/ :  re = /^\d{4}(\-|\/|\.)\d{1,2}\1\d{1,2}$/);
                re = /^\d{1,2}(\-|\/|\.)\d{1,2}\1\d{4}$/ ;
                //if (!re.test(field.value))
                //    return false;
                /*if (format == 'EURO')
                {
                    d = parseInt(v.substr(0, 2), 10);
                    m = parseInt(v.substr(3, 2), 10);
                    y = parseInt(v.substr(6, 4), 10);
                }
                else if (format == 'US')*/
                {
                    m = parseInt(v.substr(0, 2), 10);
                    d = parseInt(v.substr(3, 2), 10);
                    y = parseInt(v.substr(6, 4), 10);
                }
                /*else
                {
                    d = parseInt(v.substr(8, 2), 10);
                    m = parseInt(v.substr(5, 2), 10);
                    y = parseInt(v.substr(0, 4), 10);
                }*/
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
        
        
getSPReportMonths('sp_report_month', document.getElementById('sp_report_year').value);
//-->

$("city").onfocus=function clearText()
{
 	var cities = $("city").value;
 	if(cities.search("Input")>=0)
		$("city").value="";
}
</SCRIPT>
