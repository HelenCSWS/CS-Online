<style type="text/css">
<!--
.advEdBtn { 
	background-color: ButtonFace; 
	border: 1px solid; 
	border-color: ButtonHighlight ButtonShadow ButtonShadow ButtonHighlight 
}
IMG.advEdBtn:hover { 
	border-color: ButtonShadow ButtonHighlight ButtonHighlight ButtonShadow 
}
.advEdEmoticon {
	cursor: pointer;
	width: 19px;
	height: 19px
}
//-->
</style>
<table style="width:{editorWidth}px" width="100%" cellpadding="1" cellspacing="0" border="0">
  <tr>
	<td align="left">
	  <select id="{editorName}_formatblock" class="{inputStyle}" onChange="{editorName}_instance.format('formatblock', this.options[this.selectedIndex].value);this.options[0].selected = true;"{globalDisabled}>
		<option value="" selected>{paragraph}</option><option value="h1">{ph1}</option><option value="h2">{ph2}</option><option value="h3">{ph3}</option>
		<option value="h4">{ph4}</option><option value="h5">{ph5}</option><option value="h6">{ph6}</option><option value="address">{paddr}</option>
		<option value="pre">{ppre}</option><option value="removeformat">{prem}</option>
	  </select>
	</td>
	<td height="30"><a href="javascript:void(0);" title="{bold}" onClick="{editorName}_instance.format('bold');"><img class="advEdBtn" src="{iconPath}adved_bold.gif" alt="" border="0"></a></td>
	<td><a href="javascript:void(0);" title="{italic}" onClick="{editorName}_instance.format('italic');"><img class="advEdBtn" src="{iconPath}adved_italic.gif" alt="" border="0"></a></td>
	<td><a href="javascript:void(0);" title="{under}" onClick="{editorName}_instance.format('underline');"><img class="advEdBtn" src="{iconPath}adved_under.gif" alt="" border="0"></a></td>
	<td><a href="javascript:void(0);" title="{cut}" onClick="{editorName}_instance.format('cut');"><img class="advEdBtn" src="{iconPath}adved_cut.gif" alt="" border="0"></a></td>
	<td><a href="javascript:void(0);" title="{copy}" onClick="{editorName}_instance.format('copy');"><img class="advEdBtn" src="{iconPath}adved_copy.gif" alt="" border="0"></a></td>
	<td><a href="javascript:void(0);" title="{paste}" onClick="{editorName}_instance.format('paste');"><img class="advEdBtn" src="{iconPath}adved_paste.gif" alt="" border="0"></a></td>
	<td><a href="javascript:void(0);" title="{fcolor}" onClick="{editorName}_instance.showColorSel(this, 'forecolor');"><img class="advEdBtn" src="{iconPath}adved_fcolor.gif" alt="" border="0"></a></td>
	<td><a href="javascript:void(0);" title="{bcolor}" onClick="{editorName}_instance.showColorSel(this, 'backcolor')"><img class="advEdBtn" src="{iconPath}adved_bcolor.gif" alt="" border="0"></a></td>
	<td><a href="javascript:void(0);" title="{emoticon}" onClick="{editorName}_instance.showHideEmoticons(this)"><img class="advEdBtn" src="{iconPath}adved_emoticon.gif" alt="" border="0"></a></td>
  </tr>
  <tr>
	<td width="42%" align="left" nowrap>
	  <select id="{editorName}_fontname" class="{inputStyle}" onChange="{editorName}_instance.format('fontname', this[this.selectedIndex].value);this.options[0].selected = true;"{globalDisabled}>
		<option value="" selected>{font}</option><option value="arial,helvetica,sans-serif">Arial</option>
		<option value="courier new,courier,monospace">Courier New</option><option value="georgia,times new roman,times,serif">Georgia</option>
		<option value="impact">Impact</option><option value="lucida console">Lucida Console</option>
		<option value="tahoma,arial,helvetica,sans-serif">Tahoma</option><option value="times new roman,times,serif">Times</option>
		<option value="verdana,arial,helvetica,sans-serif">Verdana</option><option value="wingdings">Wingdings</option>
	  </select>
	  <select id="{editorName}_fontsize" class="{inputStyle}" onChange="{editorName}_instance.format('fontsize', this[this.selectedIndex].value);this.options[0].selected = true;"{globalDisabled}>
		<option value="" selected>{fontsize}</option><option value="1">1 (8 pt)</option><option value="2">2 (10 pt)</option><option value="3">3 (12 pt)</option>
		<option value="4">4 (14 pt)</option><option value="5">5 (18 pt)</option><option value="6">6 (24 pt)</option><option value="7">7 (36 pt)</option>
	  </select>
	</td>
	<td height="30"><a href="javascript:void(0);" title="{left}" onClick="{editorName}_instance.format('justifyleft');"><img class="advEdBtn" src="{iconPath}adved_left.gif" alt="" border="0"></a></td>
	<td><a href="javascript:void(0);" title="{center}" onClick="{editorName}_instance.format('justifycenter');"><img class="advEdBtn" src="{iconPath}adved_center.gif" alt="" border="0"></a></td>
	<td><a href="javascript:void(0);" title="{right}" onClick="{editorName}_instance.format('justifyright');"><img class="advEdBtn" src="{iconPath}adved_right.gif" alt="" border="0"></a></td>
	<td><a href="javascript:void(0);" title="{olist}" onClick="{editorName}_instance.format('insertorderedlist');"><img class="advEdBtn" src="{iconPath}adved_olist.gif" alt="" border="0"></a></td>
	<td><a href="javascript:void(0);" title="{ulist}" onClick="{editorName}_instance.format('insertunorderedlist');"><img class="advEdBtn" src="{iconPath}adved_ulist.gif" alt="" border="0"></a></td>
	<td><a href="javascript:void(0);" title="{indent+}" onClick="{editorName}_instance.format('indent');"><img class="advEdBtn" src="{iconPath}adved_iright.gif" alt="" border="0"></a></td>
	<td><a href="javascript:void(0);" title="{indent-}" onClick="{editorName}_instance.format('outdent');"><img class="advEdBtn" src="{iconPath}adved_ileft.gif" alt="" border="0"></a></td>
	<td><a href="javascript:void(0);" title="{link}" onClick="{editorName}_instance.createAnchor();"><img class="advEdBtn" src="{iconPath}adved_link.gif" alt="" border="0"></a></td>
	<td><a href="javascript:void(0);" title="{image}" onClick="{editorName}_instance.insertImage();"><img class="advEdBtn" src="{iconPath}adved_image.gif" alt="" border="0"></a></td>
  </tr>
</table>
<table style="width:{editorWidth}px" cellpadding="1" cellspacing="0" border="0">
  <tr><td>
	  {hiddenContent}
	  <iframe id="{editorName}_composition" style="width:{editorWidth}px;height:190px;position:block;display:block"></iframe>
	  <textarea id="{editorName}_textarea" style="width:{editorWidth}px;height:190px;position:block;display:none" rows="50" cols="8"></textarea>
  </td></tr>
  <tr><td align="left"><label for="{editorName}_switch" id="label_{editorName}_switch" class="{labelStyle}" style="align:left;width:250">&nbsp;&nbsp;<input id="{editorName}_switch" name="{editorName}_switch" type="checkbox" onClick="{editorName}_instance.setMode()"{globalDisabled}>&nbsp;{editmode}</label></td></tr>
</table>
<div id="{editorName}_divcolorsel" style="position:absolute;visibility:hidden;z-index:50" class="{labelStyle}"></div>
<div id="{editorName}_divemoticons" style="position:absolute;visibility:hidden;z-index:50;top:84px;left:150;width:180px">
	<table width="100%" cellpadding="1" cellspacing="1" style="border:none;background-color:#000000">
	  <tr><td align="center" style="font-family:tahoma,verdana,sans-serif;font-weight:bold;font-size:11px;background-color:ActiveCaption;color:CaptionText">{choosesmil}</td></tr>
	  <tr>
		<td align="center" style="padding:2px;background-color:Window">
		  <!-- START BLOCK : emoticon -->
		  <img class="advEdEmoticon" style="cursor:hand" onClick="{editorName}_instance.addEmoticon('{iconPath}adved_emotions/{imgName}.gif')" src="{iconPath}adved_emotions/{imgName}.gif" alt="">
		  <!-- END BLOCK : emoticon -->
		</td>
	  </tr>
	</table>
</div>
<script language="JavaScript" type="text/javascript">
<!--
{editorName}_instance = new AdvancedEditor('{formName}', '{editorName}', {readonlyForm});
{editorName}_instance.init();
setTimeout(function() {ldelim}
	{editorName}_instance.setHtml(document.{formName}.elements['{editorName}'].value); 
{rdelim}, 50);
//-->
</script>