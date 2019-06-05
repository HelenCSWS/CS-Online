	<!-- Form60 : template used in estateadd.php -->

             <div id="form_client_errors" class="error_style" style="display:none">{error}</div></td>
		<!-- Form60 : template used in userAdd.class.php -->
     {pageid}{levels}{bonus}{is_save}{bcldb_levels}{is_bcldb}{pro2_levels}{user_id}{lkup_commission_sales_sum_type_id}{sales_commission_level_id}{lkup_sales_commission_type_id}{province_id}
    
<table border="0" cellpadding="0" cellspacing="0">
<tr id="showWine" name="showWine" cellpadding="0" cellspacing="0"><td style="padding-top:60px" align="center">
<fieldset style="padding:2px 0px;width:390px" >
<legend class="legend" ><b> BCLDB and regular store Wine Constultant &nbsp;</b></legend>
<table border="0" cellpadding="0" cellspacing="0" width="100%" width="390">
<tr cellpadding="0" cellspacing="0"><td style="padding-top:0px" align="center">

</td></tr>

<tr id="trComm_bcldb" name="trComm_bcldb" cellpadding="0" cellspacing="0" style="display:block;"><td style=" padding-top:0px;padding-bottom:10px" align="center" >
       
       <table border="0" cellpadding="0" cellspacing="0" height="371"><tr><td align="middle" valign="top" style="padding-top:0px">
	   
	   <tr><td>
	    <table border="0" cellpadding="0" cellspacing="0"><tr><td align="middle" valign="top" style="padding-top:20px">

             <fieldset style="width:360" >
             <legend class="legend" ><b> Minimum target &nbsp;</b></legend>
            	<table cellpadding="3" cellspacing="0" border="0">
               
                <tr><td >{label_min_canadian_cases}</td> <td class="label" style="padding-top:5px;padding-left:60px">{min_canadian_cases}</td> </tr>
              	
                 </table>
                </legend>
                </fieldset>
                </td</tr>

              

                  	</table>
	   </td></tr>
	   <tr><td>

               	<table cellpadding="3" cellspacing="0" border="0">
              
 <tr><td colspan="2" style="padding-top:20px;padding-left:0px"><table cellpadding="0" cellspacing="0" border="0">
               
              <tr><td class="label"><input type="radio" id="rdoSumType" name="rdoSumType" onclick=setSumType("1") checked>Government stores</td> <td class="label" style="padding-top:0px;padding-left:15px"><input type="radio" id="rdoSumType" name="rdoSumType" onclick=setSumType("2")>Regular stores</td><td class="label" style="padding-top:0px;padding-left:15px"><input type="radio" id="rdoSumType" name="rdoSumType" onclick=setSumType("3")>Both</td></tr></table>
			   
			   </td></tr>
                <tr><td style="padding-left:0px;padding-top:10px">
                  	<table cellpadding="0" cellspacing="0" border="0">
   	                <tr> <td nowrap class="label" style="padding-left:0px;padding-top:22px;"><input type="checkbox" TABINDEX="3" id="bcldb_chklevel1" name="bcldb_chklevel1"  onclick=bcldb_changeLevels("1") checked>
   	                </td><td class="label" style="padding-left:5px;padding-top:22px;">level 1 >= </td>
                    <td style="padding-left:10px;padding-top:10px;padding-right:2px">{label_sales_1}<SPAN id="bcldb_spmin1" STYLE="color:#FF0000"><font size="1">*</SPAN><BR>{sales_1}</td>
                   <td style="padding-left:5px;padding-top:20px;padding-right:0px">=</td>
                    <td style="padding-left:10px;padding-top:10px">{label_bonus_1}<SPAN id="bcldb_spcom1" STYLE="color:#FF0000"><font size="1">*</SPAN><BR>{bonus_1}</td></tr></table>

                </td></tr>
               <tr><td style="padding-left:0px;padding-top:20px">
                  	<table cellpadding="0" cellspacing="0" border="0">
   	                <tr> <td nowrap class="label" style="padding-left:0px;padding-top:7px;"><input type="checkbox" TABINDEX="3" id="bcldb_chklevel2" name="bcldb_chklevel2"  onclick=bcldb_changeLevels("2") >
   	                </td><td class="label" style="padding-left:5px;padding-top:7px;">level 2 >= </td>
                    <td style="padding-left:10px;padding-top:0px;padding-right:2px">{label_sales_2}<SPAN id="bcldb_spmin2" STYLE="color:#FF0000"><font size="1">*</SPAN><BR>{sales_2}</td>
                   <td style="padding-left:5px;padding-top:0px;padding-right:0px">=</td>
                    <td style="padding-left:10px;padding-top:0px">{label_bonus_2}<SPAN id="bcldb_spcom2" STYLE="color:#FF0000"><font size="1">*</SPAN><BR>{bonus_2}</td> </tr></table>               </td></tr>
                    
                    
                     <tr><td style="padding-left:0px;padding-top:20px">
                  	<table cellpadding="0" cellspacing="0" border="0">
   	                <tr> <td nowrap class="label" style="padding-left:0px;padding-top:7px;"><input type="checkbox" TABINDEX="3" id="bcldb_chklevel3" name="bcldb_chklevel3"  onclick=bcldb_changeLevels("3") >
   	                </td><td class="label" style="padding-left:5px;padding-top:7px;">level 3 >= </td>
                    <td style="padding-left:10px;padding-top:0px;padding-right:2px">{label_sales_3}<SPAN id="bcldb_spmin3" STYLE="color:#FF0000"><font size="1">*</SPAN><BR>{sales_3}</td>
                   <td style="padding-left:5px;padding-top:0px;padding-right:0px">=</td>
                    <td style="padding-left:10px;padding-top:0px">{label_bonus_3}<SPAN id="bcldb_spcom3" STYLE="color:#FF0000"><font size="1">*</SPAN><BR>{bonus_3}</td> </tr></table>               </td></tr>
                    
 <tr><td style="padding-left:0px;padding-top:20px">
                  	<table cellpadding="0" cellspacing="0" border="0">
   	                <tr> <td nowrap class="label" style="padding-left:0px;padding-top:7px;"><input type="checkbox" TABINDEX="3" id="bcldb_chklevel4" name="bcldb_chklevel4"  onclick=bcldb_changeLevels("4") >
   	                </td><td class="label" style="padding-left:5px;padding-top:7px;">level 4 >= </td>
                    <td style="padding-left:10px;padding-top:0px;padding-right:2px">{label_sales_4}<SPAN id="bcldb_spmin4" STYLE="color:#FF0000"><font size="1">*</SPAN><BR>{sales_4}</td>
                   <td style="padding-left:5px;padding-top:0px;padding-right:0px">=</td>
                    <td style="padding-left:10px;padding-top:0px">{label_bonus_4}<SPAN id="bcldb_spcom4" STYLE="color:#FF0000"><font size="1">*</SPAN><BR>{bonus_4}</td> </tr></table>               </td></tr>
                     
					 <tr><td style="padding-left:0px;padding-top:20px">
                  	<table cellpadding="0" cellspacing="0" border="0">
   	                <tr> <td nowrap class="label" style="padding-left:0px;padding-top:7px;"><input type="checkbox" TABINDEX="3" id="bcldb_chklevel5" name="bcldb_chklevel5"  onclick=bcldb_changeLevels("5") >
   	                </td><td class="label" style="padding-left:5px;padding-top:7px;">level 5 >= </td>
                    <td style="padding-left:10px;padding-top:0px;padding-right:2px">{label_sales_5}<SPAN id="bcldb_spmin5" STYLE="color:#FF0000"><font size="1">*</SPAN><BR>{sales_5}</td>
                   <td style="padding-left:5px;padding-top:0px;padding-right:0px">=</td>
                    <td style="padding-left:10px;padding-top:0px">{label_bonus_5}<SPAN id="bcldb_spcom5" STYLE="color:#FF0000"><font size="1">*</SPAN><BR>{bonus_5}</td> </tr></table>               </td></tr>
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


