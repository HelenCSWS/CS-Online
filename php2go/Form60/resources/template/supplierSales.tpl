<script language="JavaScript1.2" TYPE="TEXT/JAVASCRIPT">


	function MouseOver(oImg,strLink,imgID)
	{
			oImg.src = "resources/images/apply-on_update.gif";
	}
	function MouseLeave(oImg,strLink,imgID)
	{
			oImg.src = "resources/images/apply_update.gif";
	}
	
	function MouseDown(oImg,strLink,imgID)
	{
	 	//	alert("herer");
			oImg.src = "resources/images/apply-down_update.gif";
	}
	
	function MouseUp(oImg,strLink,imgID)
	{
			oImg.src = "resources/images/apply-on_update.gif";
	}

</script>

</style>

<table border="0" cellpadding="0" cellspacing="0" width="100%"><!--first row-->
<tr><td style="padding-left: 20px; padding-top: 5px">
<table border="0" cellpadding="0" cellspacing="0" ><tr><td  class="label" id="tdEstate" style="padding-right:10px">Estate<br>{estate_id} </td> <td class="label">Province<br>{province_id}</td>

<td class="label" style="padding-left:10px">Store type<br>{lkup_store_type_id}</td><td class="label" style="padding-left:10px; display:none" >Wine Consaultant<br>{user_id}</td><td class="label" style="padding-left:10px">Products<br>{wine_id}</td><td id="tdVintage" class="label" style="padding-left:10px">Vintage<br>{vintage}</td><td class="label" style="padding-left:20px;padding-top:11px; display:block">
<img style="CURSOR: hand;" onclick="refreshSpSalesList(true)" style="border:0" src="resources/images/apply_update.gif" onmouseover="MouseOver(this)" onmouseout="MouseLeave(this)" onmousedown="MouseDown(this)" onmouseup="MouseUp(this)"></td>
</td></tr>
</table>
<td></tr>

{estate}{isBCEstate}


<!--second row-->
<tr><td style="padding-left: 12px; padding-top: 5px">
<table border="0" cellpadding="0" cellspacing="0" >
<tr id="trDateRange1" style="display:block; " height="35px;">
<td  nowrap class="label" style="padding-left:5px;padding-top:1px" width="10px"><input type="radio" id="chkdate" checked name="chkdate" onclick=changeDate() ></td> 
<td  nowrap class="label"  style="padding-left:2px;padding-top:0px" width="35px">From </td>
<td  nowrap class="label" style="padding-left:5px;padding-top:1px"  width="115px">{from_1}</td>
<td  nowrap class="label" style="padding-left:0px;padding-top:0px" width="30px">To </td>
<td colspan="5" nowrap class="label" style="padding-left:0px;padding-top:0px"  align="left">{to_1}</td>

</tr>

<tr id="trDateRange2" style="display:block;">
<td  nowrap class="label" style="padding-left:5px;padding-top:1px" width="10px"><input type="radio" id="chkdate"  name="chkdate" onclick=changeDate() ></td> 
<td  nowrap class="label"  style="padding-left:2px;padding-top:0px" width="35px">Month </td>
	<td style="padding-left:5px;padding-top:0px;padding-right:0px" width="115px" nowrap>{sales_month}</td>
	<td  nowrap class="label"  style="padding-left:0px;padding-top:0px" width="30px">Year</td>

	<td  style="padding-left:0px;padding-top:0px" width="110px">{sales_year}</td>
		<td><input type="checkbox" id="chkQut" name="chkQut" onclick=changeSearchPeriod() style="mergin-left:50px"></td>
			<td class="label" style="padding-left:1px;padding-top:0px" nowrap nowrap>List by Quarter </td>
				<td style="padding-left:6px;padding-top:1px" width="10px">{sales_qut}</td><td style="padding-left:5px">{quarter_desc}</td> <td  align="right" style="padding-right:8px;padding-top:1px; display:none" id="tdFlip" ></td></tr>
	</table>

</td>
</tr>
<tr>
<td colspan="5" style="padding-left:12px;padding-right:12px;padding-top:0px" id="spList">
<div id = "supplierSalesList">
{info_list}
</div>

	</td></tr>

</table>


<SCRIPT LANGUAGE="JavaScript" TYPE="text/javascript">
<!--
window.chkDATE =
        function (field, format) 
		{
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

//-->
</SCRIPT>

