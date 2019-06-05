{search_id}{search_id_w}
<INPUT TYPE="hidden" ID='is_international' NAME='is_international' VALUE="{isInt}"/>
<table border="0" cellpadding="0" cellspacing="0" width="100%">
<tr id="showWine" name="showWine" cellpadding="0" cellspacing="0"><td style="padding-top:60px" align="center">
		<!--fieldset style="padding:2px 0px;width:390px" -->
				
		
				<table cellpadding="0" cellspacing="0" border="0" height="249" id="tbframe" name="tbframe"><tr><td align="middle" valign="top"  >
				
					<table cellpadding="0" cellspacing="2" border="0" width="500" height="249">
			    		<tr><td valign="top" colspan="4" height="20px" style="padding-top:20px" nowrap><b>{wine_info}</td></tr>
			    		<tr><td valign="top" colspan="4" height="30px">{sales_period}</td></tr>
			    		
			    		<tr><td valign="top" ">
						 <table cellpadding="0" cellspacing="0" border="0" width="100%">
						 <tr bgcolor="#7F9DB9">
						 <td  class="mlcolheader" width="15%" height="25px" nowrap>Store type </td>
						 <td width="20%" class="mlcolheader">Total WH Sales </td>
						 <td width="20%" class="mlcolheader">Total RT sales</td>
						 <td width="20%" class="mlcolheader">Total cases cs</td>
						 <td width="25%" class="mlcolheader" nowrap>Sales % </td> </tr>
	
						 <!--agency-->						 
 						<tr class="mlcellB" style="display:{isDisplay_1}">
						  <td  class="CPgridrowCell"  width="15%" height="25px" nowrap>Agency</td>
						 <td width="20%" class="CPgridrowCell">{agency_sales_wh} </td>
						 <td width="20%" class="CPgridrowCell">{agency_sales}</td>
						 <td width="20%" class="CPgridrowCell">{agency_cases} cs</td>
						 <td width="25%" class="CPgridrowCell">{agency_percentage}%</td></tr>
						 
						  <!--bcldb-->	
						 <tr class="mlcellA" style="display:{isDisplay_2}">
						  <td  class="CPgridrowCell" width="15%" height="25px" nowrap>BCLDB</td>
						 <td width="20%" class="CPgridrowCell">{bcldb_sales_wh} </td>
						 <td width="20%" class="CPgridrowCell">{bcldb_sales}</td>
						 <td width="20%" class="CPgridrowCell">{bcldb_cases} cs</td>
						 <td width="25%" class="CPgridrowCell">{bcldb_percentage}%</td></tr>
						 
						  <!--Licensee-->	
						 <tr class="mlcellB" style="display:{isDisplay_3}">
						  <td  class="CPgridrowCell" width="15%" height="25px" nowrap>Licensee</td>
						 <td width="20%" class="CPgridrowCell">{lic_sales_wh} </td>
						 <td width="20%" class="CPgridrowCell">{lic_sales}</td>
						 <td width="20%" class="CPgridrowCell">{lic_cases} cs</td>
						 <td width="25%" class="CPgridrowCell">{lic_percentage}%</td></tr>
					
						 <!--LRS-->	
						 <tr class="mlcellA" style="display:{isDisplay_4}">
						  <td  class="CPgridrowCell" width="15%" height="25px" nowrap>LRS</td>
						 <td width="20%" class="CPgridrowCell">{lrs_sales_wh} </td>
						 <td width="20%" class="CPgridrowCell">{lrs_sales}</td>
						 <td width="20%" class="CPgridrowCell">{lrs_cases} cs</td>
						 <td width="25%" class="CPgridrowCell">{lrs_percentage}%</td></tr>
					 
					 	 <!--VQA-->	
					 	<tr class={vqa_class} style="display:{isDisplay_5}">
						  <td  class="CPgridrowCell" width="15%" height="25px" nowrap>{vaq_name}</td>
						 <td width="20%" class="CPgridrowCell">{vqa_sales_wh} </td>
						 <td width="20%" class="CPgridrowCell">{vqa_sales}</td>
						 <td width="20%" class="CPgridrowCell">{vqa_cases} cs</td>
						 <td width="25%" class="CPgridrowCell">{vqa_percentage}%</td></tr>
						
						 <!--sample.private-->	
						 <tr class={other_class} >
						  <td  class="CPgridrowCell" width="15%" height="25px" nowrap >{other_name}</td>
						 <td width="20%" class="CPgridrowCell">{other_sales_wh}</td>
						 <td width="20%" class="CPgridrowCell">{other_sales}</td>
						 <td width="20%" class="CPgridrowCell">{other_cases} cs</td>
						 <td width="25%" class="CPgridrowCell">{other_percentage}%</td></tr>
						 
						 <tr >
						  
						 <td width="100%" colspan="4" hight="*">&nbsp;</td></tr>

						</table>

					</td></tr>
					</table>
			</td></tr>
		<!--/fieldset-->
   </td></tr>   
        <tr><td>
     
        </td></tr>
   </table>       
