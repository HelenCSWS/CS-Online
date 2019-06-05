	<!-- Form60 : template used in estateadd.php -->

             <div id="form_client_errors" class="error_style" style="display:none">{error}</div></td>
		<!-- Form60 : template used in userAdd.class.php -->
     {pageid}{levels}{bonus}{is_save}{bcldb_levels}{is_bcldb}{pro2_levels}{user_id}{lkup_commission_sales_sum_type_id}{sales_commission_level_id}{lkup_sales_commission_type_id}{province_id}
    
<table border="0" cellpadding="0" cellspacing="0" style="margin-left:auto; margin-right:auto;">

<tr id="showWine" name="showWine" cellpadding="0" cellspacing="0"><td style="padding-top:60px" align="center">

<fieldset style="padding:2px 0px;width:390px" >
<table border="0" cellpadding="0" cellspacing="0" width="100%" width="390">

<tr id="trComm" name="trComm" cellpadding="0" cellspacing="0"><td style="display:block; padding-top:0px;padding-bottom:10px" align="center" >
       
       <table border="0" cellpadding="0" cellspacing="0"><tr><td align="middle" valign="top" style="padding-top:20px">

             <fieldset style="width:360" >
             <legend class="legend" ><b> Minimum target &nbsp;</b></legend>
            	<table cellpadding="3" cellspacing="0" border="0">
                <tr><td class="label">{label_min_intl_cases}</td> <td class="label" style="padding-top:0px;padding-left:80px">{min_intl_cases}</td></tr>
                <tr><td>{label_min_canadian_cases}</td> <td class="label" style="padding-top:5px;padding-left:80px">{min_canadian_cases}</td> </tr>
              	<!--tr><td style="padding-top:0px">{label_bonus_d}<SPAN STYLE="color:#FF0000"><font size="1">*</SPAN></td> <td class="label" colspan="8" style="padding-top:5px;padding-left:80px">{bonus_d}</td></tr-->

                 </table>
                </legend>
                </fieldset>
                </td</tr>

                <tr><td style="padding-left:0px;padding-top:20px">
                  	<table cellpadding="0" cellspacing="0" border="0">
   	                <tr> <td nowrap class="label" style="padding-left:0px;padding-top:22px;"><input type="checkbox" TABINDEX="3" id="chklevel1" name="chklevel1"  onclick=changeLevels("1") >
   	                </td><td class="label" style="padding-left:5px;padding-top:22px;">level 1</td>
                    <td style="padding-left:10px;padding-top:10px;padding-right:2px">{label_min_cases1}<SPAN id="spmin1" STYLE="color:#FF0000"><font size="1">*</SPAN><BR>{min_cases1}  </td>
                    <td style="padding-left:5px;padding-top:20px;padding-right:2px">-</td>
                    <td style="padding-left:5px;padding-top:10px">{label_max_cases1}<SPAN id="spmax1" STYLE="color:#FF0000"><font size="1">*</SPAN><BR>{max_cases1}</td>
                    <td style="padding-left:5px;padding-top:20px;padding-right:0px">=</td>
                    <td style="padding-left:10px;padding-top:10px">{label_comm1}<SPAN id="spcom1" STYLE="color:#FF0000"><font size="1">*</SPAN><BR>{comm1}</td>

                       </tr>

                  	</table>

                </td></tr>
                <tr><td style="padding-left:0px;padding-top:10px">
                  	<table cellpadding="0" cellspacing="0" border="0">
   	                <tr> <td nowrap class="label" style="padding-left:0px;padding-top:22px;"><input type="checkbox" TABINDEX="7" id="chklevel2" name="chklevel2"  onclick=changeLevels("2") >
   	                </td><td class="label" style="padding-left:5px;padding-top:22px;">level 2</td>
                    <td style="padding-left:10px;padding-top:10px;padding-right:2px">{label_min_cases2}<SPAN id="spmin2" STYLE="color:#FF0000"><font size="1">*</SPAN><BR>{min_cases2}  </td>
                    <td style="padding-left:5px;padding-top:20px;padding-right:2px">-</td>
                    <td style="padding-left:5px;padding-top:10px">{label_max_cases2}<SPAN id="spmax2" STYLE="color:#FF0000"><font size="1">*</SPAN><BR>{max_cases2}</td><td style="padding-left:5px;padding-top:20px;padding-right:0px">=</td>
                    <td style="padding-left:10px;padding-top:10px">{label_comm2}<SPAN id="spcom2" STYLE="color:#FF0000"><font size="1">*</SPAN><BR>{comm2}</td>

                       </tr>


                  	</table>

                </td></tr>

                <tr><td style="padding-left:0px;padding-top:10px">
                  	<table cellpadding="0" cellspacing="0" border="0">
   	                <tr> <td nowrap class="label" style="padding-left:0px;padding-top:22px;"><input type="checkbox" TABINDEX="10" id="chklevel3" name="chklevel3"  onclick=changeLevels("3") >
   	                </td><td class="label" style="padding-left:5px;padding-top:22px;">level 3</td>
                    <td style="padding-left:10px;padding-top:10px;padding-right:2px">{label_min_cases3}<SPAN id="spmin3" STYLE="color:#FF0000"><font size="1">*</SPAN><BR>{min_cases3}</td>
                    <td style="padding-left:5px;padding-top:20px;padding-right:2px">-</td>
                    <td style="padding-left:5px;padding-top:10px">{label_max_cases3}<SPAN id="spmax3" STYLE="color:#FF0000"><font size="1">*</SPAN><BR>{max_cases3}</td><td style="padding-left:5px;padding-top:20px;padding-right:0px">=</td>
                    <td style="padding-left:10px;padding-top:10px">{label_comm3}<SPAN id="spcom3" STYLE="color:#FF0000"><font size="1">*</SPAN><BR>{comm3}</td>

                       </tr>


                  	</table>

                </td></tr>

<tr><td style="padding-left:0px;padding-top:10px">
                  	<table cellpadding="0" cellspacing="0" border="0">
   	                <tr> <td nowrap class="label" style="padding-left:0px;padding-top:22px;"><input type="checkbox" TABINDEX="13" id="chklevel4" name="chklevel4"  onclick=changeLevels("4") >
   	                </td><td class="label" style="padding-left:5px;padding-top:22px;">level 4</td>
                    <td style="padding-left:10px;padding-top:10px;padding-right:2px">{label_min_cases4}<SPAN id="spmin4" STYLE="color:#FF0000"><font size="1">*</SPAN><BR>{min_cases4}</td>
                    <td style="padding-left:5px;padding-top:20px;padding-right:2px">-</td>
                    <td style="padding-left:5px;padding-top:10px">{label_max_cases4}<SPAN id="spmax4" STYLE="color:#FF0000"><font size="1">*</SPAN><BR>{max_cases4}</td><td style="padding-left:5px;padding-top:20px;padding-right:0px">=</td>
                    <td style="padding-left:10px;padding-top:10px">{label_comm4}<SPAN id="spcom4" STYLE="color:#FF0000"><font size="1">*</SPAN><BR>{comm4}</td>

                       </tr>


                  	</table>

                </td></tr>

                 </td></tr>

<tr><td style="padding-left:0px;padding-top:10px">
                  	<table cellpadding="0" cellspacing="0" border="0">
   	                <tr> <td nowrap class="label" style="padding-left:0px;padding-top:22px;"><input type="checkbox" TABINDEX="16" id="chklevel5" name="chklevel5"  onclick=changeLevels("5") >
   	                </td><td class="label" style="padding-left:5px;padding-top:22px;">level 5</td>
                    <td style="padding-left:10px;padding-top:10px;padding-right:2px">{label_min_cases5}<SPAN id="spmin5" STYLE="color:#FF0000"><font size="1">*</SPAN><BR>{min_cases5}</td>
                    <td style="padding-left:5px;padding-top:20px;padding-right:2px">-</td>
                    <td style="padding-left:5px;padding-top:10px">{label_max_cases5}<SPAN id="spmax5" STYLE="color:#FF0000"><font size="1">*</SPAN><BR>{max_cases5}</td><td style="padding-left:5px;padding-top:20px;padding-right:0px">=</td>
                    <td style="padding-left:10px;padding-top:10px">{label_comm5}<SPAN id="spcom5" STYLE="color:#FF0000"><font size="1">*</SPAN><BR>{comm5}</td>           </tr>
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
 <table width="395" border="0" style="margin-left:auto; margin-right:auto;">
             		    		<tr>
    			<td style="padding-top:20px;padding-right:20" colspan="6" align="right" >{btnBack}&nbsp;&nbsp;{btnAddNext}&nbsp;&nbsp;{btnAdd}&nbsp;&nbsp;{btnCancel}</td>
    		</tr>

                </table>

</td></tr></table>



<!-- location table end here-->


