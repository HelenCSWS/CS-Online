	<!-- Form60 : template used in userAdd.class.php -->
	{estate_id}{wine_ids}{pageid}
	{contact_key}
<table height="80%" border="0" ><tr><td align="middle" valign="center">
     <fieldset >
     <legend class="legend" ><b> Search by&nbsp;</b></legend>
    	<table cellpadding="3" cellspacing="0" border="0">
      		<tr>
    			<td style="padding:10px">
                	<table cellpadding="0" cellspacing="0" border="0" class="label">
                        <tr>
                        <td>
                        <table cellpadding="0" cellspacing="0" border="0">
                        <tr><td nowrap class="label"><input type="checkbox" name="s_type" id="s_type" >Store type</td>
                            <td nowrap style="padding-left:5px"class="label">{adt5}</td>
<td style="padding-left:88px" class="label"> <input type="checkbox" id="startwith" name="startwith" onclick=setFocus2Control("search_field")>Starts with</td>
                            </tr>
</table>
                        </td>
                       
                        </tr>

                        <tr><td style="padding-top:5px">
<table  cellpadding="0" cellspacing="0">
<tr>
                      <td style="padding-left:0px" nowrap class="label"><input type="checkbox" name="s_user" id="s_user" onclick=enablecontrols('s_user','adt6')>Assign to</td>
                            <td nowrap style="padding-left:13px;padding-top:0px"class="label">{adt6}</td>
                            <td nowrap style="padding-left:20">{label_search_field}&nbsp;</td><td style="padding-left:7px" id="edt">{search_field}</td>
 </tr>
</table>
                        </td>
                        
                    	<tr><td style="padding-top:5px;padding-bottom:3px">
<table  cellpadding="0" cellspacing="0">
<tr>
                       <td class="label" ><input type="radio" id="searchKey" name="searchKey" value="" onclick="changeSelect('0')">Customer</td>
                        
 </tr>
</table>
                        </td>

                        </tr>
                       <tr><td ><table cellpadding="0" cellspacing="0" border="0" class="label"><tr><td nowrap class="label" checked><input type="radio" id="searchkey" name="searchKey"  onclick="changeSelect('1')">Contact</td><td  style="padding-left:8px;padding-top:1px" >{contact}</td><td style="padding-left:8px;padding-top:1px"> name</td> 
                       </tr></table></td></tr>
                       
                    	<tr><td nowrap class="label" style="padding-top:5px;padding-bottom:5px"><input type="radio" id="searchKey" name="searchKey"  onclick="changeSelect('2')" >License / Agency / LRS number</td></tr>
                    	<tr><td nowrap class="label"><input type="radio" id="searchKey" name="searchKey" onclick="changeSelect('3')"  >Address</td></tr>

                    	<tr><td nowrap class="label" style="padding-left:12px">
                            <table>
                                <tr>
                                         <td nowrap class="label"><input disabled type="checkbox" checked="checked" name="searchAdt0" id="searchAdt0" onclick="checkAdtKey(0)"/>Province</td>
                                         <td nowrap class="label">{adt0}</td>
                                </tr>
                                <tr>
                                         <td nowrap class="label"><input disabled type="checkbox" name="searchAdt1" id="searchAdt1" onclick="checkAdtKey(1)">City</td>
                                         <td class="label" nowrap >{adt1} Please use"|" to split city if have more than one city</td>
                                </tr>
                                <tr>
                                         <td nowrap class="label"><input disabled type="checkbox" name="searchAdt2" id="searchAdt2" onclick="checkAdtKey(2)">Street name</td>
                                         <td nowrap class="label">{adt2}</td>
                                </tr>
                                <tr>
                                         <td nowrap class="label"><input disabled type="checkbox" name="searchAdt3" id="searchAdt3" onclick="checkAdtKey(3)">Post code</td>
                                         <td nowrap class="label">{adt3}</td>
                                </tr>
                                <tr>
                                         <td nowrap class="label"><input disabled type="checkbox" name="searchAdt4" id="searchAdt4" onclick="checkAdtKey(4)">Po Box</td>
                                         <td nowrap class="label">{adt4}</td>
                                </tr>
                                
                            </table>
                        </td></tr>


  
                	</table>

                </td>

    		</tr>

         </table>
         {search_id}
        </legend>
        </fieldset>
      <table width="100%" border="0">
     		<tr>
    			<td align="right" style="padding-top:18px;padding-right:11px">{btnSearch}&nbsp;{btnClose}</td>
    		</tr>
        </talbe>
    </td></tr>

</table> <!-- location table end here-->
