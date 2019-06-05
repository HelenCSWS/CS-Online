<table cellpadding="0" cellspacing="0" border="0" width="550">	
	<tr><td>
	<fieldset>
	<legend class="label_style">Search Results</legend>	
	<div><pre>Query debug:<br>{filter}</pre></div>
	<table cellpadding="2" cellspacing="2" style="border:1px solid #000000" width="100%" align="center">
		<!-- START BLOCK : result -->
		<tr>
			<th class="blue_style">Code</th>
			<th class="blue_style">Description</th>
			<th class="blue_style">Price</th>
			<th class="blue_style">Amount</th>
		</tr>
		<!-- START BLOCK : loop_result -->
		<tr>
			<td class="input_style">{code}</td>
			<td class="input_style">{short_desc}</td>
			<td class="input_style">{price}</td>
			<td class="input_style">{amount}</td>
		</tr>
		<!-- END BLOCK : loop_result -->
		<!-- END BLOCK : result -->		
		<!-- START BLOCK : empty -->
		<tr>
			<td colspan="3" class="error_style" style="padding:10px">The submitted search returned an empty result set!</td>
		</tr>
		<!-- END BLOCK : empty -->		
	</table>
	<div><br><a href="searchform.example.php" class="label_style">« Search again</a></div>
	</fieldset><br>
	</td></tr>
</table>