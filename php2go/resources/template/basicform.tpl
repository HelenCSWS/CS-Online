<!-- START BLOCK : hidden_field -->
{field}
<!-- END BLOCK : hidden_field -->
<table cellpadding="0" cellspacing="0" border="0"{formWidth}>	
	<tr><td>
	<div{errorStyle} id="form_client_errors"{errorDisplay}>{errorTitle}{errorMessages}</div>
	<!-- START BLOCK : loop_section -->
	<fieldset {fieldset_style}>
	<!-- START BLOCK : section_name -->
	<legend>{name}</legend>
	<!-- END BLOCK : section_name -->
	<table cellpadding="{tblCPadding}" cellspacing="{tblCSpacing}" border="0" width="100%" align="{tblAlign}"{sectable_style}>
		<!-- START BLOCK : section_name_browser_compat -->
		<tr>
			<td colspan="2">
				&nbsp;&nbsp;{name}<br>
				<hr noshade/>
			</td>
		</tr>
		<!-- END BLOCK : section_name_browser_compat -->
		<!-- START BLOCK : loop_field -->
		<tr>
			<td align="{labelAlign}" width="{labelW}">&nbsp;{label}{popup_help}&nbsp;</td>
			<td align="left" width="{fieldW}">{field}{button}{inline_help}</td>
		</tr>
		<!-- START BLOCK : button -->
		<tr>
			<td>&nbsp;</td>
			<td align="left">{button}</td>
		</tr>
		<!-- END BLOCK : button -->
		<!-- START BLOCK : button_group -->
		<tr>
			<td>&nbsp;</td>
			<td>
				<table cellpadding="0" cellspacing="0"  border="0" width="100%">
					<tr>
						<!-- START BLOCK : loop_button_group -->
						<td width="{btnW}" align="center">{button}</td>
						<!-- END BLOCK : loop_button_group -->
					</tr>
				</table>
			</td>
		</tr>
		<!-- END BLOCK : button_group -->
		<!-- END BLOCK : loop_field -->
	</table>
	</fieldset><br>
	<!-- END BLOCK : loop_section -->
	</td></tr>
</table>