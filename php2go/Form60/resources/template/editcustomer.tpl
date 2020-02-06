<!-- Form60 : template used in customeradd.php (for edit customer)-->
 {old_lkup_payment_type_id}{old_cc_number}{old_cc_exp_month}{old_cc_exp_year}

 {estate_id_order}{isorder}{province_id}{current_sales_page}{isAdmin}


  {customer_id}{is_primary}{contact_id}{lkup_phone_type_id}{customers_contacts_id_1}{customers_contacts_id_2}{pageid}{isload}{test}

{pst_no_org}{assign_user_id}{billing_address_state} {hk_rank_1}{hk_rank_2}{hk_rank_3}{hk_rank_4}{hk_rank_5}{hk_rank_dirty}{hk_rank_types}

<!--Main table-->
<table border="0" width="100%">
    <tr>
        <td valign="top" style="padding-top:0px">
            <fieldset id="fldTab" style="padding:2px 0px;">
                <!--Top field set-->
                <div>
                    <!-- First Row Menu Tab-->
                    <table width="100%" border="0" cellpadding="0" cellspacing="0">
                        <tr class="tab">
							<td style="width:auto;border-bottom: 1px solid #a3b3c0;"><b>&nbsp;</b></td>
							<td id="tab0" align="center" class="tab" onclick="changeTab(0)" width="18%"><b>Store information</b></td>
							<td id="tab1" align="center" class="tab" onclick="changeTab(1)" width="18%"><b>Contact information</b></td>
							<td id="tab3" align="center" class="tab" onclick="changeTab(3)" width="18%"><b>Form 60</b></td>
							 <td id="tab5" align="center" class="tab" onclick="changeTab(5)" width="18%"><b>Products</b></td>
							<td id="tab4" align="center" class="tab" onclick="changeTab(4)" width="18%"><b>Sales</b></td>
							 <td id="tab2" align="center" class="tabRight" onclick="changeTab(2)" width="10%"><b>Notes</b></td>
							<td style="width:auto;border-bottom: 1px solid #a3b3c0;"><b>&nbsp;</b></td>
                        </tr>
                    </table>
                </div><!-- First Row Menu Tab Ends here-->
                <!-- Second  Section Store Information-->
                <div style="display:block" valign=middle id="tab_store">

                    <table border="0" width="100%" cellpadding="0" cellspacing="0">
                        <tr>
                            <!-- Store info 1st row-->
                            <td width="33%" style="padding-left:3px">{label_customer_name}</b><BR>{customer_name}</td>
                            <td width="*" align="middle"><table><tr><td nowrap>{label_licensee_number}<BR>{licensee_number}</td></tr></table></td>
                            <td width="33%" nowrap align="right" style="padding-right:3px"><table><tr><td>{label_sst_number}<BR>{sst_number}</td></tr></table></td>
                        </tr> <!-- Store info 1st row end here-->

                        <tr>
                            <!-- Store info 2nd row-->
                            <td width="33%" style="padding-left:3px">
                                <table cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td style="padding-right:3px">
                                            <table cellpadding="0" cellspacing="0">
                                                <tr><td>{label_lkup_store_type_id}</td></tr>
                                                <tr><td style="padding-top:1px">{lkup_store_type_id}</td></tr>
                                            </table>
                                        </td>

                                        <td>{label_billing_address_street_number}<BR>{billing_address_street_number}</td>
                                    </tr>
                                </table>
                            </td>
                            <td width="*" align="middle">
                                <table>
                                    <tr>
                                        <td>{label_billing_address_street}<BR>{billing_address_street}</td>
                                        <td>{label_po_box}<BR>{po_box}</td>
                                    </tr>
                                </table>
                            </td>
                            <td width="33%" nowrap align="right" style="padding-right:3px"><table><tr><td>{label_billing_address_city}<BR>{billing_address_city}</td></tr></table></td>
                        </tr><!-- Store info 2nd row end here-->
                        <!-- Store info 3rd row-->
                        <tr>
                            <td width="33%" style="padding-left:3px">
                                <table cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td style="padding-right:3px">{label_cm_province_id}<BR>{cm_province_id}</td>
                                        <td>{label_billing_address_postalcode}<BR>{billing_address_postalcode}</td>
                                    </tr>
                                </table>
                            </td>
                            <td width="*" align="middle">
                                <table cellpadding="0" cellspacing="0" border="0">
                                    <tr>
                                        <td style="padding-right:3px">
                                            <table cellpadding="0" cellspacing="0">
                                                <tr><td>{label_lkup_payment_type_id}</td></tr>
                                                <tr><td style="padding-top:1px">{lkup_payment_type_id}</td></tr>
                                            </table>
                                        </td>
                                        <td>
                                            <table cellpadding="0" cellspacing="0">
                                                <tr>
                                                    <td style="display:none" id="showCCno">
                                                        <LABEL FOR="cc_number" ID="lbl_cc_number" CLASS="label">
                                                            {label_cc_number}
                                                            <SPAN STYLE="color:#FF0000">*</SPAN>
                                                        </LABEL>
                                                    </td>
                                                    <td style="display:block" id="noCCno"><LABEL FOR="cc_number" ID="lbl_cc_number" CLASS="label">{label_cc_number}</LABEL></td>
                                                </tr>
                                                <tr>
                                                    <td style="padding-top:1px">{cc_number}</td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </td>

                            <td width="33%" nowrap align="right" style="padding-right:6px">
                                <table cellpadding="0" cellspacing="0" border="0">
                                    <tr>
                                        <td style="padding-right:3px">
                                            <table cellpadding="0" cellspacing="0" border="0">
                                                <tr>
                                                    <td colspan="2">
                                                        <table cellpadding="0" cellspacing="0">
                                                            <tr>
                                                                <td id="showCCexp" style="display:none;padding-right:0px"><LABEL FOR="cc_exp_month" ID="lbl_cc_exp_month" CLASS="label">{label_cc_exp_month}</LABEL><SPAN STYLE="color:#FF0000">*</SPAN></td>
                                                                <td id="noCCexp" style="display:block; padding-right:0px;padding-top:3px"><LABEL FOR="cc_exp_month" ID="lbl_cc_exp_month" CLASS="label">{label_cc_exp_month}</LABEL></td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td style="padding-top:1px;padding-right:3px">{cc_exp_month}</td>
                                                    <td style="padding-top:1px">{cc_exp_year}</td>
                                                </tr>
                                            </table>
                                        </td>
                                        <td style="padding-top:1px;">
                                            <table cellpadding="0" cellspacing="0">
                                                <tr><td>{label_cc_digit_code}</td></tr>
                                                <tr><td style="padding-top:1px">{cc_digit_code}</td></tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <!-- Store info 3rd row end-->
                        <!-- Store info 4th row -->
                        <tr>
                            <td width="33%" style="padding-left:3px" id="tddelivery">{label_best_time_to_deliver}<BR>{best_time_to_deliver}</td>
                            <td width="33%" style="padding-left:3px; display:none" id="tdrank">{label_rank}<BR>{rank}</td>
							<td width="33%" style="padding-left:3px; display:none" id="tdsubtype">
                            
                            <!-- {label_sub_type}<BR>{sub_type} -->
                            
                            
                            <table cellpadding="0" cellspacing="0" ><tr>
                            <td style="display:block" id="showSubType"><LABEL FOR="sub_type" ID="label_sub_type" CLASS="label"><span style="cursor:pointer" onclick="selectHKSubType()"><U>Sub Type</U></span></LABEL></td>
                            
                            </tr>
                            
                            <tr><td style="padding-top:1px" id="sub_type" name="sub_type">{sub_type}</td></tr>
                            </table>

                            </td>
                            <td width="*" align="middle">
                                <table>
                                    <tr>
                                        <!-- td id="tdShowMark" style="display:none"><LABEL FOR="lkup_store_priority_id" ID="lbl_lkup_store_priority_id_label" CLASS="label">Store priority<SPAN STYLE="color:#FF0000">*</SPAN></LABEL></td>
                                        <td id="tdNoMark" style="display:block"><LABEL FOR="lkup_store_priority_id" ID="lbl_lkup_store_priority_id_label" CLASS="label">Store priority</LABEL></td -->
                                    <td id="tdNoMark" style="display:block"><LABEL FOR="lkup_territory_id" ID="lbl_lkup_territory_id" CLASS="label">Store territory</LABEL></td>

                                    </tr>
                                    <tr><td style="padding-top:0px">{lkup_territory_id}</td></tr>
                                </table>
                            </td>

                            <td width="33%" nowrap align="right" style="padding-right:6px">
                                <table cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td id="userBCLDB" style="display:none"><LABEL FOR="user_id" ID="label_user_id" CLASS="label">Assign to<SPAN STYLE="color:#FF0000">*</SPAN></LABEL></td>
                                        <td id="userOther" style="display:block"><LABEL FOR="user_id" ID="label_user_id" CLASS="label">Assign to</LABEL></td>
                                    </tr>
                                    <tr><td style="padding-top:1px">{user_id}</td></tr>
                                </table>
                            </td>
                        </tr>
                        <!-- Store info 4th row end-->
						
						<tr>
						<!-- Store info 5th row end HK Rank-->
						<td colspan="3" style="display:none;" >
						
						<section id="hk_ranks">
							<div style="padding-left:5px">Ranks</div>
							<table>
							<tr>
							<td><input class="chk-input" type="checkbox" name="rank_type_1" id="rank_type_1" border="0"></td><td>TimeOut Hong Kong 2019</td>
							<td style="padding-left:20px"><input class="chk-input" type="checkbox" name="rank_type_2" id="rank_type_2" border="0"></td><td>Top Bars Hong Kong</td>
							<td style="padding-left:20px"><input class="chk-input" type="checkbox" name="rank_type_3" id="rank_type_3" border="0"></td><td>Tatler 2018</td>
							<td style="padding-left:20px"><input class="chk-input" type="checkbox" name="rank_type_4" id="rank_type_4" border="0"></td><td>Tatler 2019</td>
							<td style="padding-left:20px"><input class="chk-input" type="checkbox" name="rank_type_5" id="rank_type_5" border="0"></td><td>Tatler Best Resautants</td>
							<td></td><td></td>
							</tr>
							</table>			
						</section>
						</td>
						</tr>
						<!-- Store info 5th row end-->
                    </table><!-- Store info table end here-->


                </div><!-- Second  Section Store info End here-->
                
                <!-- Second  Section Contact info End here-->
                <div style="padding-top:5px;display:none" valign="top" id="tab_contact" height="153px">
                    <table cellpadding="0" cellspacing="0" border="0" width="100%">
                        <tr>
                            <td width="33%" style="padding-left:3px">{label_first_name}<BR>{first_name}</td>
                            <td width="*" align="middle"><table><tr><td>{label_last_name}<BR>{last_name}</td></tr></table></td>
                            <td width="33%" nowrap align="right" style="padding-right:3px"><table><tr><td>{label_title}<BR>{title}</td></tr></table></td>
                        </tr>
                        <tr>
                            <td width="33%" style="padding-left:3px;" nowrap class="label">
                                <table cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td style="padding-left:2px;" nowrap class="label" valign="top">
                                            {label_phone_office1}
                                            <input type="radio" name="best1" id="best1" checked onclick=selectBest(1)>Best # to contact <BR>{phone_office1}
                                        </td>
                                        <td style="padding-top:7px;padding-left:4px">{label_ext_no}<BR>{ext_no}</td>
                                    </tr>
                                </table>

                            </td>{phone_work}{phone_cell}
                            <td nowrap width="*" align="middle">
                                <table>
                                    <tr>
                                        <td class="label">
                                            {label_phone_other1}&nbsp;<input onclick=selectBest(2) type="radio" name="best1" id="best1">Best # to contact <BR>{phone_other1}
                                        </td>
                                    </tr>
                                </table>
                            </td>
                            <td width="33%" nowrap align="right" style="padding-right:3px" nowrap>
                                <table>
                                    <tr>
                                        <td class="label">
                                            {label_phone_fax}&nbsp;<input onclick=selectBest(3) type="radio" name="best1" id="best1">Best # to contact <BR>{phone_fax}
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td width="33%" style="padding-left:3px">{label_email1}<BR>{email1}</td>
                            <td width="*" align="middle"><table><tr><td>{label_second_first_name}<BR>{second_first_name}</td></tr></table></td>
                            <td width="33%" nowrap align="right" style="padding-right:3px"><table><tr><td>{label_second_last_name}<BR>{second_last_name}</td></tr></table></td>
                        </tr>
                    </table>
                </div><!-- Second  Section Contact info End here-->
                
                <!-- Second  Section Order info Start here-->
                <div style="padding-top:2px;display:none;padding-bottom:1px" align="left" valign="top" width="100%" id="tab_orders" height="155px">
                    <table cellpadding="0" cellspacing="0" border="0" class="label" width="100%">
                        <tr>
                            <td style="padding-left:8px;padding-top:1px" nowarp width="135px;"><LABEL FOR="order_month" ID="lbl_order_month_label" CLASS="label">Month</LABEL>&nbsp;{order_month}</td>
                            <td width="107" nowrap calss="label"><LABEL FOR="order_year" ID="lbl_order_year_label" CLASS="label" style="margin-left:8px;">Year</LABEL>&nbsp;{order_year}	</td>
                            <td width="10" class="label" style="padding-left:10px;padding-top:1px" nowrap><input type="checkbox" id="chkOdQut" name="chkOdQut" onclick=changeOrderPeriod() style="mergin-left:50px"></td>
                            <td width="30" class="label" style="padding-left:1px;padding-top:1px" nowrap>List&nbsp;by&nbsp;Quarter </td>
                            <td width="10" style="padding-left:5px;padding-top:1px" align="left">{order_qut}</td>
                            <td width="1%" style="padding-left:5px" align="left">{order_quarter_desc}</td>
                            <td width="*" align="right" Class="label">Select estate {order_estate_id}</td>
                        </tr>
                    </table>

                    <div id="ordersList" style="padding:4px;">
                        {orders_list}
                    </div>
                </div><!-- Second  Section order info End here-->
                
                <!-- Second  Section Note info start here-->
                <div style="padding-top:2px;display:none;padding-bottom:0px" align="left" valign="top" width="100%" id="tab_note" height="155px">
                    <div id="notesList" style="padding:4px;">
                        {note_contents}
                    </div>
                </div><!-- Second  Section Note info End here-->
                
                <!-- Second  Section Sales info start here-->
                <div style="padding-top:2px;display:none;padding-bottom:0px" align="left" valign="top" width="100%" id="tab_sales" height="155px">

                    <table cellpadding="0" cellspacing="0" border="0" class="label" width="100%">
                        <tr>
                            <td style="padding-left:8px;padding-top:1px" nowarp width="136"><LABEL FOR="sales_month" ID="lbl_sales_month_label" CLASS="label">Month</LABEL>&nbsp;{sales_month}</td>
                            <td width="108"><LABEL FOR="sales_year" ID="lbl_sales_year_label" CLASS="label" style="margin-left:8px;">Year</LABEL>&nbsp;{sales_year}	</td>
                            <td class="label" style="padding-left:10px;padding-top:1px" width="20px"><input type="checkbox" id="chkQut" name="chkQut" onclick=changeSalesPeriod() style="mergin-left:50px"> </td>
                            <td class="label" style="padding-left:1px;padding-top:1px" nowrap width="90" nowrap>List by Quarter </td>
                            <td style="padding-left:1px;padding-top:1px" width="1%">{sales_qut}</td>
                            <td style="padding-left:5px">{quarter_desc}</td>
                            <td width="*" align="right" style="padding-right:8px;padding-top:1px; display:none" id="tdFlip">
                                <table cellpadding="0" cellspacing="0" border="0" class="label" width="150">
                                    <tr>
                                        <td align="right" style="padding-left:0px;padding-top:0px" nowarp class="label">
                                            <a href="javascript:getsales(0);" style="font-size:8pt"><< Previous</a>
                                        </td>
                                        <td align="right" style="padding-left:0px;padding-top:0px" nowarp class="label">
                                            <a href="javascript:getsales(1);" style="font-size:8pt"> Next >></a>
                                        </td>
                                    </tr>
                                </table>

                            </td>
                        </tr>
                    </table>
                    <div id="salesList" style="padding:4px;">
			            {sales_list}
			        </div>
                </div><!-- Second  Section Sales info End here-->
                
                <!-- Second  Section CS product order  info start here-->
                <div style="padding-top:2px;display:none;padding-bottom:0px" align="left" valign="top" width="100%" id="tab_pro_order" height="155px">
                     <table cellpadding="0" cellspacing="0" border="0" class="label" width="100%">
                        <tr>
                            <td style="padding-left:8px;padding-top:1px" nowarp width="135px;"><LABEL FOR="order_month" ID="lbl_order_month_label" CLASS="label">Month</LABEL>&nbsp;{cs_order_month}</td>
                            <td width="107" nowrap calss="label"><LABEL FOR="order_year" ID="lbl_order_year_label" CLASS="label" style="margin-left:8px;">Year</LABEL>&nbsp;{cs_order_year}	</td>
                            <td width="10" class="label" style="padding-left:10px;padding-top:1px" nowrap><input type="checkbox" id="chkCSOdQut" name="chkCSOdQut" onclick=changeCSOrderPeriod() style="mergin-left:50px"></td>
                            <td width="30" class="label" style="padding-left:1px;padding-top:1px" nowrap>List&nbsp;by&nbsp;Quarter </td>
                            <td width="10" style="padding-left:5px;padding-top:1px" align="left">{cs_order_qut}</td>
                            <td width="1%" style="padding-left:5px" align="left">{cs_order_quarter_desc}</td>
                            <td width="*" align="right" Class="label"><!-- Select product&nbsp;{display_products} -->
                           
                            </td>
                        </tr>
                    </table>

                    <div id="cs_ordersList" style="padding:4px;">
                        {cs_orders_list}
                    </div>
                </div><!-- Second  Section CS product orde info End here-->

            </fieldset>          <!--Top field set End here-->
        </td>
    </tr>
    
    
	<!-- 3rd  Section OK Cancel buttons panel start here-->
	<tr>
	    <td valign="bottom" style="padding-top:8px">
	        <table cellpadding="0" cellspacing="0" width="100%" border="0">
	            <tr>
	                <td colspan="2" align="right" width="99%" valign="bottome">{btnAdd}</td>
	                <td width="*" style="padding-right:5px;padding-left:3px">{btnCancel}</td>
	            </tr>
	        </table>
	    </td>
	</tr>
	<!-- 3rd  Section OK Cancel buttons panel end here-->
	
	<!-- 4th  Section Ca wines estate panel start here-->
	<tr id="trSelectEstate">
    <td width="100%">

        <fieldset style="margin-top:0px; padding:2px 0px; width:100%">
            <legend class="legend">Add new order </legend>
            <div style="margin-bottom:3px; width:100% ">
                <LABEL FOR="estate_id" ID="lbl_estate_id_label" CLASS="label">Select estate</LABEL>
                {estate_id}<LABEL CLASS="label">for ordering:</LABEL>
            </div>
            <div id="winesList">
                {wine_list}
            </div>
            <div class="gridRightLink" style="margin-top:10px;">
                <INPUT ID="buttonF60" NAME="buttonF60" TYPE="BUTTON" VALUE="Create Form 60" CLASS="btnOK" style="width:120px;" TABINDEX="29" onClick="createOrd();">
            </div>
        </fieldset>
    </td>
</tr>
<!-- 4th  Section Ca wines estate panel end here-->

	<!-- 4th  Section Ca wines estate panel start here-->
	<tr id="trCreateCSOrder">
       <td style="width:100%;padding-right:9px;" >

        <fieldset style="margin-top:0px; padding:2px 0px; width:100%">
            <legend class="legend">Add new order </legend>
          <div class="divOrd_Select_Product" >                
                <!--div style="float:left; padding-left:10px;padding-right:5px;padding-top:3px;">{label_cs_products_id}</div><div >{cs_products_id} </div --> 
                  <table class="orderTable" border="0">
                <tr ><td>{label_cs_products_id}</td><td>{cs_products_id} </td>
                <td><table id="PST_table"><tr >
                <td><input type="checkbox" id="chk_PST" name="chk_PST" /></td>
					<td ><span id = "pst_rate_title">PST Rate:<span id = "pst_rate"></td>
					<td align="right"><span id = "pst_rate_val">{pst_rate}</span>&nbsp;{pst_no}</td>
                    <!-- td align="right"><span id = "pst_rate">{pst_rate}</span></td -->
				</tr></table>
                </td>
            </table>
               
                
            </div>
            
            <div id="csproductList">
                {cs_product_list}
            </div>
            <div class="gridRightLink" style="margin-top:10px;">
                <INPUT ID="butCSOrd" NAME="butCSOrd" TYPE="BUTTON" VALUE="Create Order" CLASS="btnOK" style="width:120px;" TABINDEX="29" onClick="createCSOrder();">
            </div>
        </fieldset>
    </td>
</tr>
<!-- 4th  Section Ca wines estate panel end here-->

	<!-- 4th  Section CS product order start here-->
	<!-- tr id="trCreateCSOrder">
    <td style="width:100%;padding-right:9px;" >
        <fieldset style="margin-top:0px; padding:2px 0px; width:100%">
            <legend class="legend">Add new order </legend>
             
             <div class="divCsOrdQty">                
                <div style="float:left; padding-left:10px;padding-right:5px;padding-top:2px;">{label_cs_order_qty}</div><div class="Label" >{cs_order_qty} Cans</div>
                
            </div>
            
                <div class="divCsInventory">                
                   
                <div style="float:left; padding-left:10px;padding-right:5px;padding-top:0px;"><span CLASS="label">Current Inventory:{cs_inventory_winelife}</span></div><div ><span CLASS="label" id="wl_inventory_bottles" name="wl_inventory_bottles">0 </span><span CLASS="label">&nbsp;Cans;&nbsp;</span><span CLASS="label" id="wl_inventory_cs" name="wl_inventory_cs">; 0</span><span CLASS="label">&nbspCases</span></div>
                
            </div>
           
            <div class="gridRightLink" style="margin-top:10px;"> <
               <INPUT ID="butCSOrd" NAME="butCSOrd" TYPE="BUTTON" VALUE="Create Order" CLASS="btnOK" style="width:120px;" TABINDEX="29" onClick="createCSOrder(1530);">
            </div>
        </fieldset >
    </td>
</tr -->
<!-- 4th  Section Ca wines estate panel end here-->
	
</table><!--Main table ends-->


<script language="javascript">

if (window.setWineListHeight) setWineListHeight();
if (window.setNoteHeight) setNoteHeight();
if (window.setorderListHeight) setorderListHeight();
if (window.setcsListHeight) setcsListHeight();
if (window.tglOrderButton) tglOrderButton(false);
refreshWines();

$(document).ready(function(){

 
 });
 
 function HKSetting()
 {
 

	if($("#province_id").val()==13)
	{
			initHKRank();

			document.getElementById("rank_type_1").onclick = function () { setHKRank("rank_type_1", 1) };
			document.getElementById("rank_type_2").onclick = function () { setHKRank("rank_type_2", 2) };
			document.getElementById("rank_type_3").onclick = function () { setHKRank("rank_type_3", 3) };
			document.getElementById("rank_type_4").onclick = function () { setHKRank("rank_type_4", 4) };
			document.getElementById("rank_type_5").onclick = function () { setHKRank("rank_type_5", 5) };
			   
		   

	 }             
}
 function initHKRank()
 {
	var i=1;
	for(i=1;i<=5;i++)
	{
		var editRankTypeID="hk_rank_"+i;
		
		var chkRankTypeID="rank_type_"+i;
		
		if(document.getElementById(editRankTypeID).value==1)
		{
			document.getElementById(chkRankTypeID).checked=true;
		}
		
	
		if ($("#isAdmin").val()==0) // disable checkbox
		{
			document.getElementById(chkRankTypeID).disabled=true;
		}
	}
	//document.getElementById("user_id").disabled=true;
	$("#billing_address_city_label").html("District");
	$("#cm_province_id_label").html("Territory");
 }
 function setHKRank(chk_rank_type_id,rankType)
 {
	
	document.getElementById("hk_rank_dirty").value=1;
		
	var editRankTypeID="#hk_rank_"+rankType;
	if(document.getElementById(chk_rank_type_id).checked)
	{

		$(editRankTypeID).val(1)
	}
	else
	{

		$(editRankTypeID).val(0)
	}
	
 }
 
</script>

