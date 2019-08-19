<style>

		.links, A.links {
		font-size: 8pt;
		font-family: Verdana, Arial, Helvetica, sans-serif;
		color: #000000;
		font-weight: bold;
		text-decoration: none
	}
	A.links:hover {
		text-decoration: underline
	}
	.input {
		font-family: Verdana, Arial;
		font-weight: bold;
		font-size: 7pt
	}
	.title {
		font-family: Verdana, Arial;
		font-size: 8pt;
		color: #000000;
		padding: 3px;
	}
	.cellA {
		font-family: Verdana;
        font-size: 8pt;
		color: #333333;
		padding: 3px;
		background-color: #ffffff;
	}
	.cellB {
		font-family: Verdana;
        font-size: 8pt;
		color: #333333;
		padding: 3px;
		background-color: #F2F5FA;
	}
	.colheader
	{
        padding: 2px;
        font-size: 8pt;
    }
    
    #report_first_page,#report_prev_page,#report_next_page, #report_last_page{
        width:70px;
        height:20px;
        font-size:10px;
		border-radius：10%;
    
    }
	
	#btnClose{
        width:100px;
        height:30px;
        font-size:11px;
		border-radius：30%;
    
    }
</style>

<table  width="100%" height="100%"  cellpadding="0" cellspacing="0" border="0"  >
<tr><td style="padding-top:20px" valign="top">

    <table  width="95%"  cellpadding="0" cellspacing="0" border="0" bordercolor="#7F9DB9"  align="center" >
          <tr><td bgcolor="#7F9DB9" align="center">
        	<table  width="100%"  cellpadding="0" cellspacing="0" border="0" >

        	  <!-- START BLOCK : loop_line -->
        	  <tr>

        		<!-- START BLOCK : loop_header_cell -->
        		<td nowrap width="{col_wid}" valign="top" class="colheader" >{col_name}{col_order}</td>
        		<!-- END BLOCK : loop_header_cell -->

        		<!-- START BLOCK : loop_cell -->
        		<td nowrap width="{col_wid}" valign="top" class="{alt_style}" >{col_data}{NAME}</td>
        		<!-- END BLOCK : loop_cell -->

        		<!-- START BLOCK : loop_cell_empty -->
        		<td nowrap width="{col_wid}" class="{alt_style}">&nbsp;</td>
        		<!-- END BLOCK : loop_cell_empty -->

        	  </tr>
        	  <!-- END BLOCK : loop_line -->

        	</table>
          </td></tr>
    </table>
    <table width="95%" cellpadding="0" cellspacing="0" border="0" align="center">
    
      <tr style="">
        <td style="padding-top:20px;" colspan="2" width="100%" align="center" class="cellA"><span id="spTotalCS"></span>&nbsp;{page_links}</td>
    </tr>
      <tr style="">
        <td width="100%" align="center" class="cellA" >{this_page}</td>
        <td style="display:none" width="50%" align="center" class="cellA">{row_interval}</td >
      </tr>
    </table>
    <table border="0" width ="100%">
    <tr><td align="center" style="padding-right:25px;padding-top:20px"><span id="spTotalCS">&nbsp;</span> <INPUT onClick="closePage()" ID="btnClose" NAME="btnClose" TYPE="BUTTON" VALUE="Close" CLASS="btnOK" ></td></tr>
    </table>
</td></tr>


</table>
