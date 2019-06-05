	<!-- Form60 : template used in userAdd.class.php -->
	
	{estate_id}{wineid}{editMode}{price_per_unit}{pageid}{price_winery}{is_international}{profit_per_unit}{cost_per_unit}{wine_info_id}

<table width="100%" height="60%" border="0" ><tr><td align="middle" valign="center">
     	<table width="420px" cellpadding="3" cellspacing="0" border="0">
                <!-- used for error display -->
    <tr><td width="300px" style="padding-bottom:8px">{label_province_id}<br>{province_id}</td></tr>
    <TR><TD align="middle" valign="center" style="padding-left:2px" colspan="2" ><fieldset >
     <legend class="legend" > <b> Wine&nbsp;</b></legend>
     <table cellpadding="0" cellspacing="0" border="0">
  
            <tr><td colspan="5" align=left >
                <div id="form_client_errors" class="error_style" style="display:none">{error}</div></td>
            </tr>
      <tr><td colspan="3" style="padding-top:10px;padding-left:9px;padding-bottom:0px" valign="bottom">{label_wine_name}<BR>{wine_name}</td>
    			<td style="padding-top:10px;padding-left:10px">
                <table cellpadding="0" cellspacing="0" border="0"><tr><td style="padding-right:2px">
                        <table cellpadding="0" cellspacing="0" border="0"><tr><td>{label_lkup_bottle_size_id}</td><tr><tr><td style="padding-top:1px">{lkup_bottle_size_id}</td>
                </tr></table>

                 </td></tr></table>

                </td>
    			<td style="padding-top:12px;padding-left:10px">{label_lkup_wine_color_type_id}<BR>{lkup_wine_color_type_id}</td>
    			<td style="padding-top:11px;padding-left:10px;padding-right:15px">{label_cspc_code}<BR>{cspc_code}</td>
    			
    			
    		</tr>

    		<tr>
    			<td style="padding-top:0px;padding-left:8px;padding-right:5px">{label_vintage}<BR>{vintage}</td>
    			<td style="padding-top:15px;padding-left:2px;padding-bottom:15px">{label_bottles_per_case}<BR>{bottles_per_case}</td>
  	           <td style="padding-top:15px;padding-left:8px;padding-bottom:15px">{label_cost}<BR>{cost}</td>
    			<td nowrap style="padding-top:15px;padding-left:10px;padding-bottom:15px">{label_wholesale}<BR>{wholesale}</td>
   			<td style="padding-top:15px;padding-left:12px;padding-bottom:15px">{label_display_price}<BR>{display_price}</td>
 
	       <td nowrap style="padding-top:15px;padding-left:10px;padding-right:15px;padding-bottom:15px">{label_profit}<BR>{profit}</td>

           	</tr></table>
           	  </fieldset >
      </TD></TR>
           	
           	<tr><td>
<fieldset style="width:200px">
     <legend class="legend" > <b> Case volume&nbsp;</b></legend>
     <table cellpadding="0" cellspacing="0" border="0" width="250px"><tr>
    	    <td style="padding-top:5px;padding-left:10px;padding-bottom:10px">{label_case_sold}<BR>{case_sold}</td>
      <td style="padding-top:10px;padding-left:10px;padding-bottom:10px" class="label">=</td>
       <td style="padding-top:5px;padding-left:10px;padding-bottom:10px;padding-right:10px;">{label_case_value}<BR>{case_value}</td>
     
</td>

           	</tr></table>
           	  </fieldset >
               </td></tr>


    		<tr>
    			<td style="padding-top:20px;padding-left:10" colspan="6" align="right" >{btnAdd}&nbsp;{btnCancel}</td> 
    		</tr>
        </table>
      
</td></tr></table> <!-- location table end here-->



