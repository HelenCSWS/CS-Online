	<!-- Form60 : template used in cutomercpreport.php -->
    
<table border="0" width="100%"><tr><td style="padding-top:5px">

<tr><td style="padding-top:1px" align="left" width="100%">{pageid}{search_key}{isWine}{isStart}{isOneRec}{city}{product_id}
{search_id}{sales_year}{sales_period}{isQtr}{store_type_id}{user_id}{search_adt1}{search_adt2}<td></tr>


    <tr><td style="padding-top:1px" align="left" width="100%" id="display_results1">
     <!--fieldset -->
         <div id = "customersList">
            {list_results}
         </div>
    <!--/fieldset-->
<tr id="display_results2"><td align="middle">	<table border="0" width="100%" id="tbButtons" name="tbButtons"><tr><td  id="tdBtns">{btnBack}&nbsp;{btnCancel} </td></tr></table>
    </td></tr>
</table>
 <table cellpadding="0" cellspacing="0" border="0" width="100%" height="100%">
 
<TR><TD id="noresults" style="display:none;  text-align: center;padding-top:300px;">
      Nothing was found matching your search criteria, please try again.<BR><BR>
    {btnBack}&nbsp;{btnCancel}</TD>
 </TR></table>


