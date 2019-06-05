<style>
	.links, A.links {
		font-size: 11px; 
		font-family: Verdana, Arial, Helvetica, sans-serif; 
		color: #000000;
		font-weight: bold;
		text-decoration: none	
	}
	A.links:hover {
		text-decoration: underline
	}
	.input {
		font-family: Verdana, Helvetica;
		font-weight: bold;
		font-size: 11px
	}	
	.title {
		font-family: Verdana, Helvetica;
		font-size: 12px;
		color: #000000
	}
	.cellA {
		font-family: Verdana, Helvetica;
		font-size: 12px;
		color: #333333;
		padding: 3px;
		background-color: #ffffff
	}
	.cellB {
		font-family: Verdana, Helvetica;
		font-size: 12px;
		color: #333333;
		padding: 3px;
		background-color: #e9e9e9		
	}	
</style>

<table width="625" cellpadding="4" cellspacing="0" border="0" align="center">  
  <tr><td class="title" height="50" align="center"><b>PHP2Go Examples - php2go.data.Report</b></td></tr>
  <tr><td class="title" height="50" align="center"><b>{title}</b></td></tr>
</table>

<table width="600" cellpadding="4" cellspacing="0" border="1" bordercolor="#999999" align="center">  
  <tr><td class="cellB">{simple_search}</td></tr>
</table>

<table width="625" cellpadding="4" cellspacing="4" border="0" align="center">
  <tr>
    <td width="40%" align="left" class="cellA">{row_count}</td>
    <td width="60%" align="right" class="cellA">{rows_per_page}</td>
  </tr>
  <tr>
    <td align="left" class="cellA">{go_to_page}</td>
    <td align="right" class="cellA">{page_links}</td>
  </tr>
</table>	

<table width="625" cellpadding="4" cellspacing="0" border="1" bordercolor="#999999" align="center">
  <tr><td bgcolor="#e9e9e9" align="center">
	<table width="600" cellpadding="3" cellspacing="0" border="0">
	
	  <!-- START BLOCK : loop_line -->	
	  <tr>
	  
		<!-- START BLOCK : loop_group -->	  
		<td width="100%" colspan="{group_span}" valign="top" class="title"><B>{group_display}</B></th>	  
		<!-- END BLOCK : loop_group -->	  
	  
		<!-- START BLOCK : loop_header_cell -->	  
		<th width="{col_wid}" valign="top">{col_name}{col_order}</th>	  
		<!-- END BLOCK : loop_header_cell -->	  
	  
		<!-- START BLOCK : loop_cell -->	  
		<td width="{col_wid}" valign="top" class="{alt_style}">{col_data}{NAME}</td>	  
		<!-- END BLOCK : loop_cell -->
	  
		<!-- START BLOCK : loop_cell_empty -->
		<td width="{col_wid}" class="{alt_style}">&nbsp;</td>	  
		<!-- END BLOCK : loop_cell_empty -->
		
	  </tr>	
	  <!-- END BLOCK : loop_line -->
	
	</table>
  </td></tr>
</table>

<table width="625" cellpadding="4" cellspacing="4" border="0" align="center">
  <tr>
    <td width="40%" align="left" class="cellA">{go_to_page}</td>
    <td width="60%" align="right" class="cellA">{page_links}</td>
  </tr>	  
  <tr>
    <td align="left" class="cellA">{this_page}</td>
    <td align="right" class="cellA">{row_interval}</td>
  </tr>
</table>