	<!-- Form60 : template used in estateadd.php -->

             <div id="form_client_errors" class="error_style" style="display:none">{error}</div></td>
		<!-- Form60 : template used in userAdd.class.php -->
     {pageid}{levels}{bonus}{is_save}{bcldb_levels}{is_bcldb}{pro2_levels}
    
<table border="0" cellpadding="0" cellspacing="0">
<tr id="showWine" name="showWine" cellpadding="0" cellspacing="0"><td style="padding-top:60px" align="center">
<fieldset style="padding:2px 0px;width:390px" >
<table border="0" cellpadding="0" cellspacing="0" width="100%" width="390">
<tr cellpadding="0" cellspacing="0"><td style="padding-top:0px" align="center">
<table width="100%" border="0" cellpadding="0" cellspacing="0" >
            <tr class="tab">
                <td style="width:auto;border-bottom: 1px solid #a3b3c0;"><b>&nbsp;</b></td>
                <td id ="tab0" align= "center" class="tab" onclick="changeCommTab(0)"><b>BC consultants</b></td>
                <td id ="tab1" align= "center" class="tab" onclick="changeCommTab(1)"><b>BCLDB consultants</b></td>
                <td id ="tab2" align= "center" class="tab" onclick="changeCommTab(2)"><b>AB consultants</b></td>
               
                <td style="width:auto;border-bottom: 1px solid #a3b3c0;"><b>&nbsp;</b></td>
            </tr>
        </table>
</td></tr>

<tr id="trComm" name="trComm" cellpadding="0" cellspacing="0"><td style="display:block; padding-top:0px;padding-bottom:10px" align="center" >
       
       <table border="0" cellpadding="0" cellspacing="0"><tr><td align="middle" valign="top" style="padding-top:20px">

             <fieldset style="width:360" >
             <legend class="legend" ><b> Minimum target &nbsp;</b></legend>
            	<table cellpadding="3" cellspacing="0" border="0">
                <tr><td class="label">{label_min_intl_cases}</td> <td class="label" style="padding-top:0px;padding-left:80px">{min_intl_cases}</td></tr>
                <tr><td>{label_min_canadian_cases}</td> <td class="label" style="padding-top:5px;padding-left:80px">{min_canadian_cases}</td> </tr>
              	<tr><td style="padding-top:0px">{label_bonus_d}<SPAN STYLE="color:#FF0000"><font size="1">*</SPAN></td> <td class="label" colspan="8" style="padding-top:5px;padding-left:80px">{bonus_d}</td></tr>

                 </table>
                </legend>
                </fieldset>
                </td</tr>

                <tr><td style="padding-left:0px;padding-top:20px">
                  	<table cellpadding="0" cellspacing="0" border="0">
   	                <tr> <td nowrap class="label" style="padding-left:0px;padding-top:22px;"><input type="checkbox" TABINDEX="3" id="chklevel1" name="chklevel1"  onclick=changeLevels("1") checked>
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

<tr id="trComm_bcldb" name="trComm_bcldb" cellpadding="0" cellspacing="0" style="display:none;"><td style=" padding-top:0px;padding-bottom:10px" align="center" >
       
       <table border="0" cellpadding="0" cellspacing="0" height="371"><tr><td align="middle" valign="top" style="padding-top:0px"><tr><td>

               	<table cellpadding="3" cellspacing="0" border="0">
               

                <tr><td style="padding-left:0px;padding-top:20px">
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
                    
                    <tr><td style="padding-left:0px;padding-top:20px">
                  	<table cellpadding="0" cellspacing="0" border="0">
   	                <tr> <td nowrap class="label" style="padding-left:0px;padding-top:7px;"><input type="checkbox" TABINDEX="3" id="bcldb_chklevel6" name="bcldb_chklevel6"  onclick=bcldb_changeLevels("6") >
   	                </td><td class="label" style="padding-left:5px;padding-top:7px;">level 6 >= </td>
                    <td style="padding-left:10px;padding-top:0px;padding-right:2px">{label_sales_6}<SPAN id="bcldb_spmin6" STYLE="color:#FF0000"><font size="1">*</SPAN><BR>{sales_6}</td>
                   <td style="padding-left:5px;padding-top:0px;padding-right:0px">=</td>
                    <td style="padding-left:10px;padding-top:0px">{label_bonus_6}<SPAN id="bcldb_spcom6" STYLE="color:#FF0000"><font size="1">*</SPAN><BR>{bonus_6}</td> </tr></table>              </td></tr>
                    
                    
                    <tr><td style="padding-left:0px;padding-top:20px">
                  	<table cellpadding="0" cellspacing="0" border="0">
   	                <tr> <td nowrap class="label" style="padding-left:0px;padding-top:7px;"><input type="checkbox" TABINDEX="3" id="bcldb_chklevel7" name="bcldb_chklevel7"  onclick=bcldb_changeLevels("7") >
   	                </td><td class="label" style="padding-left:5px;padding-top:7px;">level 7 >= </td>
                    <td style="padding-left:10px;padding-top:0px;padding-right:2px">{label_sales_7}<SPAN id="bcldb_spmin7" STYLE="color:#FF0000"><font size="1">*</SPAN><BR>{sales_7}</td>
                   <td style="padding-left:5px;padding-top:0px;padding-right:0px">=</td>
                    <td style="padding-left:10px;padding-top:0px">{label_bonus_7}<SPAN id="bcldb_spcom7" STYLE="color:#FF0000"><font size="1">*</SPAN><BR>{bonus_7}</td> </tr></table>              </td></tr>
                    
                     <tr><td style="padding-left:0px;padding-top:20px">
                  	<table cellpadding="0" cellspacing="0" border="0">
   	                <tr> <td nowrap class="label" style="padding-left:0px;padding-top:7px;"><input type="checkbox" TABINDEX="3" id="bcldb_chklevel8" name="bcldb_chklevel8"  onclick=bcldb_changeLevels("8") >
   	                </td><td class="label" style="padding-left:5px;padding-top:7px;">level 8 >= </td>
                    <td style="padding-left:10px;padding-top:0px;padding-right:2px">{label_sales_8}<SPAN id="bcldb_spmin8" STYLE="color:#FF0000"><font size="1">*</SPAN><BR>{sales_8}</td>
                   <td style="padding-left:5px;padding-top:0px;padding-right:0px">=</td>
                    <td style="padding-left:10px;padding-top:0px">{label_bonus_8}<SPAN id="bcldb_spcom8" STYLE="color:#FF0000"><font size="1">*</SPAN><BR>{bonus_8}</td> </tr></table>              </td></tr>
                    
                     <tr><td style="padding-left:0px;padding-top:20px">
                  	<table cellpadding="0" cellspacing="0" border="0">
   	                <tr> <td nowrap class="label" style="padding-left:0px;padding-top:7px;"><input type="checkbox" TABINDEX="3" id="bcldb_chklevel9" name="bcldb_chklevel9"  onclick=bcldb_changeLevels("9") >
   	                </td><td class="label" style="padding-left:5px;padding-top:7px;">level 9 >= </td>
                    <td style="padding-left:10px;padding-top:0px;padding-right:2px">{label_sales_9}<SPAN id="bcldb_spmin9" STYLE="color:#FF0000"><font size="1">*</SPAN><BR>{sales_9}</td>
                   <td style="padding-left:5px;padding-top:0px;padding-right:0px">=</td>
                    <td style="padding-left:10px;padding-top:0px">{label_bonus_9}<SPAN id="bcldb_spcom9" STYLE="color:#FF0000"><font size="1">*</SPAN><BR>{bonus_9}</td> </tr></table>              </td></tr>
                    
                     <tr><td style="padding-left:0px;padding-top:20px">
                  	<table cellpadding="0" cellspacing="0" border="0">
   	                <tr> <td nowrap class="label" style="padding-left:0px;padding-top:7px;"><input type="checkbox" TABINDEX="3" id="bcldb_chklevel10" name="bcldb_chklevel10"  onclick=bcldb_changeLevels("10") >
   	                </td><td class="label" style="padding-left:5px;padding-top:7px;">level 10 >= </td>
                    <td style="padding-left:5px;padding-top:0px;padding-right:2px">{label_sales_10}<SPAN id="bcldb_spmin10" STYLE="color:#FF0000"><font size="1">*</SPAN><BR>{sales_10}</td>
                   <td style="padding-left:5px;padding-top:0px;padding-right:0px">=</td>
                    <td style="padding-left:10px;padding-top:0px">{label_bonus_10}<SPAN id="bcldb_spcom10" STYLE="color:#FF0000"><font size="1">*</SPAN><BR>{bonus_10}</td> </tr></table>              </td></tr>
                    
                    
                  	</table>
                

                </td></tr>

            
         </td></tr>


        </table>
	</td></tr>
	
	
<tr id="trComm_pro2" name="trComm_pro2" cellpadding="0" cellspacing="0" style="display:none;"><td style=" padding-top:0px;padding-bottom:10px" align="center" >
       
       <table border="0" cellpadding="0" cellspacing="0" height="391"><tr><td align="middle" valign="top" style="padding-top:0px"><tr><td>

               	<table cellpadding="3" cellspacing="0" border="0">
               

                <tr><td style="padding-left:0px;padding-top:20px">
                  	<table cellpadding="0" cellspacing="0" border="0">
   	                <tr> <td nowrap class="label" style="padding-left:0px;padding-top:22px;"><input type="checkbox" TABINDEX="3" id="pro2_chklevel1" name="pro2_chklevel1"  onclick=pro_changeLevels("1",2) checked>
   	                </td><td class="label" style="padding-left:5px;padding-top:22px;">level 1 >= </td>
                    <td style="padding-left:10px;padding-top:10px;padding-right:2px">{label_sales_1_pro_2}<SPAN id="pro2_spmin1" STYLE="color:#FF0000"><font size="1">*</SPAN><BR>{sales_1_pro_2}</td>
                   <td style="padding-left:5px;padding-top:20px;padding-right:0px">=</td>
                    <td style="padding-left:10px;padding-top:10px">{label_bonus_1_pro_2}<SPAN id="pro2_spcom1" STYLE="color:#FF0000"><font size="1">*</SPAN><BR>{bonus_1_pro_2}</td></tr></table>

                </td></tr>
               <tr><td style="padding-left:0px;padding-top:20px">
                  	<table cellpadding="0" cellspacing="0" border="0">
   	                <tr> <td nowrap class="label" style="padding-left:0px;padding-top:7px;"><input type="checkbox" TABINDEX="3" id="pro2_chklevel2" name="pro2_chklevel2"  onclick=pro_changeLevels("2",2) >
   	                </td><td class="label" style="padding-left:5px;padding-top:7px;">level 2 >= </td>
                    <td style="padding-left:10px;padding-top:0px;padding-right:2px">{label_sales_2_pro_2}<SPAN id="pro2_spmin2" STYLE="color:#FF0000"><font size="1">*</SPAN><BR>{sales_2_pro_2}</td>
                   <td style="padding-left:5px;padding-top:0px;padding-right:0px">=</td>
                    <td style="padding-left:10px;padding-top:0px">{label_bonus_2_pro_2}<SPAN id="pro2_spcom2" STYLE="color:#FF0000"><font size="1">*</SPAN><BR>{bonus_2_pro_2}</td> </tr></table>               </td></tr>
                    
                    
                     <tr><td style="padding-left:0px;padding-top:20px">
                  	<table cellpadding="0" cellspacing="0" border="0">
   	                <tr> <td nowrap class="label" style="padding-left:0px;padding-top:7px;"><input type="checkbox" TABINDEX="3" id="pro2_chklevel3" name="pro2_chklevel3"  onclick=pro_changeLevels("3",2) >
   	                </td><td class="label" style="padding-left:5px;padding-top:7px;">level 3 >= </td>
                    <td style="padding-left:10px;padding-top:0px;padding-right:2px">{label_sales_3_pro_2}<SPAN id="pro2_spmin3" STYLE="color:#FF0000"><font size="1">*</SPAN><BR>{sales_3_pro_2}</td>
                   <td style="padding-left:5px;padding-top:0px;padding-right:0px">=</td>
                    <td style="padding-left:10px;padding-top:0px">{label_bonus_3_pro_2}<SPAN id="pro2_spcom3" STYLE="color:#FF0000"><font size="1">*</SPAN><BR>{bonus_3_pro_2}</td> </tr></table>               </td></tr>
                    
 <tr><td style="padding-left:0px;padding-top:20px">
                  	<table cellpadding="0" cellspacing="0" border="0">
   	                <tr> <td nowrap class="label" style="padding-left:0px;padding-top:7px;"><input type="checkbox" TABINDEX="3" id="pro2_chklevel4" name="pro2_chklevel4"  onclick=pro_changeLevels("4",2) >
   	                </td><td class="label" style="padding-left:5px;padding-top:7px;">level 4 >= </td>
                    <td style="padding-left:10px;padding-top:0px;padding-right:2px">{label_sales_4_pro_2}<SPAN id="pro2_spmin4" STYLE="color:#FF0000"><font size="1">*</SPAN><BR>{sales_4_pro_2}</td>
                   <td style="padding-left:5px;padding-top:0px;padding-right:0px">=</td>
                    <td style="padding-left:10px;padding-top:0px">{label_bonus_4_pro_2}<SPAN id="pro2_spcom4" STYLE="color:#FF0000"><font size="1">*</SPAN><BR>{bonus_4_pro_2}</td> </tr></table>               </td></tr>
                     
					 <tr><td style="padding-left:0px;padding-top:20px">
                  	<table cellpadding="0" cellspacing="0" border="0">
   	                <tr> <td nowrap class="label" style="padding-left:0px;padding-top:7px;"><input type="checkbox" TABINDEX="3" id="pro2_chklevel5" name="pro2_chklevel5"  onclick=pro_changeLevels("5",2) >
   	                </td><td class="label" style="padding-left:5px;padding-top:7px;">level 5 >= </td>
                    <td style="padding-left:10px;padding-top:0px;padding-right:2px">{label_sales_5_pro_2}<SPAN id="pro2_spmin5" STYLE="color:#FF0000"><font size="1">*</SPAN><BR>{sales_5_pro_2}</td>
                   <td style="padding-left:5px;padding-top:0px;padding-right:0px">=</td>
                    <td style="padding-left:10px;padding-top:0px">{label_bonus_5_pro_2}<SPAN id="pro2_spcom5" STYLE="color:#FF0000"><font size="1">*</SPAN><BR>{bonus_5_pro_2}</td> </tr></table>               </td></tr>
                    
                  	</table>
                </td></tr>

            
         </td></tr>


        </table>
	</td></tr>

</table>
 
</td></tr>

</td></tr>
</table>

<tr><td>
 <table width="395" border="0">
             		    		<tr>
    			<td style="padding-top:20px;padding-right:0" colspan="6" align="right" >{btnAdd}&nbsp;{btnCancel}</td>
    		</tr>

                </table>

</td></tr></table>



<!-- location table end here-->


