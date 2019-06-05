	<form name="simple_search" method="post" action="{url}" onSubmit="return checkSimpleSearch();" style="display:inline">
	<table border="0" width="100%" cellpadding="3" cellspacing="0">
		<tr>			
			<td align="left" valign="top" nowrap><label for="p2g_sfield" {lstyle}>{searchTit}</label><br>
				<input type="hidden" name="search_fields" value="">
				<input type="hidden" name="search_operators" value="">
				<input type="hidden" name="search_values" value="">
				<input type="hidden" name="p2g_masks" value="{searchMasks}">
				<select id="p2g_sfield" name="p2g_sfield" onChange="filterOperators()"{fstyle}>
					<option value="-1">{fieldFirst}</otion>
					{fieldOptions}
				</select>&nbsp;
				<select name="p2g_soperator" style="width:120px"{fstyle}>
					{opOptions}
				</select>&nbsp;
				<input type="text" name="p2g_svalue" size="20" maxlength="100" onKeyPress="return checkSearchMask(this, event);"{fstyle}>&nbsp;
				<input type="button" name="ok_search" value="{btnSend}" onClick="checkSimpleSearch();"{bstyle}>
			</td>
		</tr>
		<tr>
			<td align="left" nowrap>
				<input type="radio" id="search_main_op_and" name="search_main_op" value="AND" checked><label for="search_main_op_and" {lstyle}>&nbsp;{radioAll}&nbsp;&nbsp;</label>
				<input type="radio" id="search_main_op_or" name="search_main_op" value="OR"><label for="search_main_op_or" {lstyle}>&nbsp;{radioAny}&nbsp;&nbsp;</label>
				<input type="button" name="ok_add" value="{btnAdd}" onClick="addFilter(true);"{bstyle}>&nbsp;
				<input type="button" name="ok_view" value="{btnView}" onClick="viewFilters();"{bstyle}>&nbsp;
				<input type="button" name="ok_clear" value="{btnClear}" onClick="location.replace('{url}')"{bstyle}>&nbsp;
			</td>			
		</tr>
		<tr>
			<td><div id="filter_view" style="position:block;display:none"></div></td> 
		</tr>
	</table>
	</form>