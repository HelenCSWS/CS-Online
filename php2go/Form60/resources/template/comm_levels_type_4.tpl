	<!-- Form60 : template used in estateadd.php -->

             <div id="form_client_errors" class="error_style" style="display:none">{error}</div></td>
		<!-- Form60 : template used in userAdd.class.php -->
     {pageid}{levels}{bonus}{is_save}{bcldb_levels}{is_bcldb}{pro2_levels}{user_id}{lkup_commission_sales_sum_type_id}{sales_commission_level_id}{lkup_sales_commission_type_id}{province_id}
    
<table border="0" cellpadding="0" cellspacing="0">
<tr id="showWine" name="showWine" cellpadding="0" cellspacing="0"><td style="padding-top:60px" align="center">
<fieldset style="padding:2px 0px;width:390px" >
<legend class="legend" ><b> Alberta Wine Constultant &nbsp;</b></legend>
<table border="0" cellpadding="0" cellspacing="0" width="100%" width="390">
<tr cellpadding="0" cellspacing="0"><td style="padding-top:0px" align="center">

</td></tr>

<tr cellpadding="0" cellspacing="0" style="display:block;"><td style=" padding-top:0px;padding-bottom:10px" align="center" >
       
            <table border="0" cellpadding="0" cellspacing="0" height="391"><tr><td align="middle" valign="top" style="padding-top:0px"><tr><td>

               	<table cellpadding="3" cellspacing="0" border="0">
               

                <tr><td style="padding-left:0px;padding-top:20px">
                  	<table cellpadding="0" cellspacing="0" border="0">
   	                <tr> <td nowrap class="label" style="padding-left:0px;padding-top:22px;"><input type="checkbox" TABINDEX="3" id="chklevel1" name="chklevel1"  onclick=pro_changeLevels("1",2) checked>
   	                </td><td class="label" style="padding-left:5px;padding-top:22px;">level 1 >= </td>
                    <td style="padding-left:10px;padding-top:10px;padding-right:2px">{label_sales_1}<SPAN id="spmin1" STYLE="color:#FF0000"><font size="1">*</SPAN><BR>{sales_1}</td>
                   <td style="padding-left:5px;padding-top:20px;padding-right:0px">=</td>
                    <td style="padding-left:10px;padding-top:10px">{label_bonus_1}<SPAN id="spcom1" STYLE="color:#FF0000"><font size="1">*</SPAN><BR>{bonus_1}</td></tr></table>

                </td></tr>
               <tr><td style="padding-left:0px;padding-top:20px">
                  	<table cellpadding="0" cellspacing="0" border="0">
   	                <tr> <td nowrap class="label" style="padding-left:0px;padding-top:7px;"><input type="checkbox" TABINDEX="3" id="chklevel2" name="chklevel2"  onclick=pro_changeLevels("2",2) >
   	                </td><td class="label" style="padding-left:5px;padding-top:7px;">level 2 >= </td>
                    <td style="padding-left:10px;padding-top:0px;padding-right:2px">{label_sales_2}<SPAN id="spmin2" STYLE="color:#FF0000"><font size="1">*</SPAN><BR>{sales_2}</td>
                   <td style="padding-left:5px;padding-top:0px;padding-right:0px">=</td>
                    <td style="padding-left:10px;padding-top:0px">{label_bonus_2}<SPAN id="spcom2" STYLE="color:#FF0000"><font size="1">*</SPAN><BR>{bonus_2}</td> </tr></table>               </td></tr>
                    
                    
                     <tr><td style="padding-left:0px;padding-top:20px">
                  	<table cellpadding="0" cellspacing="0" border="0">
   	                <tr> <td nowrap class="label" style="padding-left:0px;padding-top:7px;"><input type="checkbox" TABINDEX="3" id="chklevel3" name="chklevel3"  onclick=pro_changeLevels("3",2) >
   	                </td><td class="label" style="padding-left:5px;padding-top:7px;">level 3 >= </td>
                    <td style="padding-left:10px;padding-top:0px;padding-right:2px">{label_sales_3}<SPAN id="spmin3" STYLE="color:#FF0000"><font size="1">*</SPAN><BR>{sales_3}</td>
                   <td style="padding-left:5px;padding-top:0px;padding-right:0px">=</td>
                    <td style="padding-left:10px;padding-top:0px">{label_bonus_3}<SPAN id="spcom3" STYLE="color:#FF0000"><font size="1">*</SPAN><BR>{bonus_3}</td> </tr></table>               </td></tr>
                    
 <tr><td style="padding-left:0px;padding-top:20px">
                  	<table cellpadding="0" cellspacing="0" border="0">
   	                <tr> <td nowrap class="label" style="padding-left:0px;padding-top:7px;"><input type="checkbox" TABINDEX="3" id="chklevel4" name="chklevel4"  onclick=pro_changeLevels("4",2) >
   	                </td><td class="label" style="padding-left:5px;padding-top:7px;">level 4 >= </td>
                    <td style="padding-left:10px;padding-top:0px;padding-right:2px">{label_sales_4}<SPAN id="spmin4" STYLE="color:#FF0000"><font size="1">*</SPAN><BR>{sales_4}</td>
                   <td style="padding-left:5px;padding-top:0px;padding-right:0px">=</td>
                    <td style="padding-left:10px;padding-top:0px">{label_bonus_4}<SPAN id="spcom4" STYLE="color:#FF0000"><font size="1">*</SPAN><BR>{bonus_4}</td> </tr></table>               </td></tr>
                     
					 <tr><td style="padding-left:0px;padding-top:20px">
                  	<table cellpadding="0" cellspacing="0" border="0">
   	                <tr> <td nowrap class="label" style="padding-left:0px;padding-top:7px;"><input type="checkbox" TABINDEX="3" id="chklevel5" name="chklevel5"  onclick=pro_changeLevels("5",2) >
   	                </td><td class="label" style="padding-left:5px;padding-top:7px;">level 5 >= </td>
                    <td style="padding-left:10px;padding-top:0px;padding-right:2px">{label_sales_5}<SPAN id="spmin5" STYLE="color:#FF0000"><font size="1">*</SPAN><BR>{sales_5}</td>
                   <td style="padding-left:5px;padding-top:0px;padding-right:0px">=</td>
                    <td style="padding-left:10px;padding-top:0px">{label_bonus_5}<SPAN id="spcom5" STYLE="color:#FF0000"><font size="1">*</SPAN><BR>{bonus_5}</td> </tr></table>               </td></tr>
                    
                  	</table>

                </td></tr>

            
         </td></tr>


        </table>
</td></tr>
	</td></tr>

</table>
 
</td></tr>

</td></tr>
</table>

<tr><td>
 <table width="395" border="0">
             		    		<tr>
    			<td style="padding-top:20px;padding-right:0" colspan="6" align="right" >{btnBack}&nbsp;&nbsp;{btnAddNext}&nbsp;&nbsp;{btnAdd}&nbsp;&nbsp;{btnCancel}</td>
    		</tr>

                </table>

</td></tr></table>



<!-- location table end here-->


