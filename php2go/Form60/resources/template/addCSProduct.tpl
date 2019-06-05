	<!-- Form60 : template used in userAdd.class.php -->
	{estate_id}{product_id}{product_ids}{delete_id}{is_next}{editMode}

	{isAddNew}{isAdmin}{basicChanged}{infoChanged}{current_province_id}{total_units}{isAddAnother}

    <table width="100%" height="60%" border="0" cellpadding="0" cellspacing="0">
    <!--tb out-->
            
        <tr>
        <td align="center">
        <div style="text-align:left;width:625px;padding-top:15px; padding-bottom:15px; padding-left:5px;">   
          {province_id} 
        </div>
        <fieldset style="width:600px;" >
        <legend class="legend" ><b> Product  information&nbsp;</b></legend>
            <table border="0" cellpadding="0" cellspacing="0">
                <!--tb1-->
                <!-- used for error display -->
                <tr>
                    <td colspan="5" align=left>
                        <div id="form_client_errors" class="error_style" style="display:none">{error}</div>
                    </td>
                </tr>
                    <td style="padding-top:0px;padding-left:0px" height="50pt">

                        <table cellpadding="3" cellspacing="0" border="0" id="table_basic">
                            <!--tb2-->
                            <!--tb2-->
                            <tr>
                                <td style="padding-top:0px;padding-left:0px">{label_product_name}<SPAN class="label" STYLE="color:#FF0000">*</SPAN></td>
                                <td style="padding-top:0px;padding-left:7px">{label_product_number}</td>
                                <td style="padding-top:0px;padding-left:7px">{label_bottles_per_case}</td>
                                <td style="padding-top:0px;padding-left:10px">{label_lkup_product_type_id}</td>
                            </tr>
                            <tr id="tr_basic">
                                <td style="padding-top:0px;padding-left:0px">{product_name}</td>
                                <td style="padding-top:0px;padding-left:7px">{product_number}</td>
                                <td style="padding-top:0px;padding-left:7px">{bottles_per_case}</td>
                                <td style="padding-top:0px;padding-left:7px">{lkup_product_type_id}</td>
                            </tr>

                        </table><!--tb2-->

                    </td>
                </tr>
                <tr>
                    <td style="padding-top:10px;padding-left:0px" valign="top">
                        <table id="table_1" name="table_1" cellpadding="0" cellspacing="0" border="0">
                            <!--tb3-->
                            <tr>
                                <td style="padding-top:0px;padding-left:0px">
                                    <table cellpadding="0" cellspacing="0" border="0">
                                        <!--tb5-->
                                        <tr id="tr_info_1">
                                            <td style="padding-top:0px;padding-left:0px">{label_display_price}<BR>{display_price}</td>
                                            <td style="padding-top:0px;padding-left:10px">{label_special_price}<BR>{special_price}</td>
                                            <td style="padding-top:0px;padding-left:10px" id="tdCost">{label_cost}<BR>{cost}</td>
                                            <td style="padding-top:0px;padding-left:10px" id="tdProfit">{label_commission}<BR>{commission}</td>

                                        </tr>
                                    </table><!--tb4-->

                                </td>
                             </tr>
                        </table>	<!--tb3-->

                </tr>


                <tr>
                    <td nowrap style="padding-top:18px;padding-left:0;padding-right:5px;padding-bottom:5px;" colspan="20" align="right">
                        <table cellpadding="0" cellspacing="3" border="0">
                            <tr>

                                <td style="padding-top:0px;padding-left:0" align="right">{btnAdd}</td>
                                 <td style="padding-top:0px;padding-left:0" align="right">{btnAddAnother}</td>
                               <td style="padding-top:0px;padding-left:0" align="right">{btnCancel}</td>

                            </tr>
                        </table>
                    </td>
                </tr>
            </table><!--tb1-->
    </fieldset>
              
    <div id="div_inventory" style="text-align:left;width:625px;padding-top:15px; display:none;">   
                <fieldset style="width:300px;" >
            <legend class="legend" ><b> Inventory information&nbsp;</b></legend>
            <div class="label" style="padding-top:8px;padding-left:2px;">Available inventories: <span id="sp_total_units"></span> units; <span id="sp_total_cases"></span> cases</div>
            <div style="padding-top:12px;padding-bottom:8px;padding-left:2px;">{inventory} &nbsp;&nbsp;
            <!-- input id="btnAddInventory" name="btnAddInventory" value="Add" class="btnOK" type="BUTTON" / -->
            <input id="butnInventory" name="butnInventory" type="BUTTON" value="Add" class="btnOK" style="width:120px;"  onclick="addProductInventory();"/>
             </div>
            </fieldset>
    </div>
</td>
    </tr>
 
</table><!--tb out-->



<SCRIPT LANGUAGE="JavaScript" TYPE="text/javascript">
                $(document).ready(function () {

                    initCSProductPage();

                    document.getElementById("display_price").onblur = function () { setPrice(this,1) };
                    document.getElementById("display_price").onfocus = function () { setPrice(this,0) };
                    
                    document.getElementById("special_price").onblur = function () { setPrice(this,1) };
                    document.getElementById("special_price").onfocus = function () { setPrice(this,0) };

                    document.getElementById("cost").onblur = function () { setPrice(this,1) };
                    document.getElementById("cost").onfocus = function () { setPrice(this,0) };
                    
                    document.getElementById("commission").onblur = function () { setPrice(this,1) };
                    document.getElementById("commission").onfocus = function () { setPrice(this,0) };

                
                 })



</SCRIPT>
