//
// +----------------------------------------------------------------------+
// | PHP2Go Web Development Framework                                     |
// +----------------------------------------------------------------------+
// | Copyright (c) 2002-2005 Marcos Pont                                  |
// +----------------------------------------------------------------------+
// | This library is free software; you can redistribute it and/or        |
// | modify it under the terms of the GNU Lesser General Public           |
// | License as published by the Free Software Foundation; either         |
// | version 2.1 of the License, or (at your option) any later version.   |
// | 																	  |
// | This library is distributed in the hope that it will be useful,      |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of       |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU    |
// | Lesser General Public License for more details.                      |
// | 																	  |
// | You should have received a copy of the GNU Lesser General Public     |
// | License along with this library; if not, write to the Free Software  |
// | Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA             |
// | 02111-1307  USA                                                      |
// +----------------------------------------------------------------------+
//
// $Header: /www/cvsroot/php2go/resources/javascript/modules/colorselection.js,v 1.12 2005/08/30 14:41:31 mpont Exp $
// $Date: 2005/08/30 14:41:31 $
// $Revision: 1.12 $

//!--------------------------------------------------------------
// @function	ColorSelection
// @desc		Construtor do objeto ColorSelection
// @param		layerId String		ID da layer a ser utilizada para exibir a seleção de cor (palheta)
// @param		callback String		Nome da função que tratará a escolha de cor
// @return		ColorSelection object
//!--------------------------------------------------------------
function ColorSelection(layerId, callback) {
	this.div = getDivFromName(layerId);
	this.divId = layerId;
	this.callback = callback;
	this.ready = (this.callback != '' && this.div != null);
	this.hlColor = layerId + "_color";
	this.hlValue = layerId + "_value";
	this.curr = "#FFFFFF";
	this.highlight = (document.getElementById || document.all);
	this.colors = new Array(
		'#000000','#000000','#003300','#006600','#009900','#00cc00','#00ff00','#330000','#333300','#336600','#339900','#33cc00',
		'#33ff00','#660000','#663300','#666600','#669900','#66cc00','#66ff00','#333333','#000033','#003333','#006633','#009933',
		'#00cc33','#00ff33','#330033','#333333','#336633','#339933','#33cc33','#33ff33','#660033','#663333','#666633','#669933',
		'#66cc33','#66ff33','#666666','#000066','#003366','#006666','#009966','#00cc66','#00ff66','#330066','#333366','#336666',
		'#339966','#33cc66','#33ff66','#660066','#663366','#666666','#669966','#66cc66','#66ff66','#999999','#000099','#003399',
		'#006699','#009999','#00cc99','#00ff99','#330099','#333399','#336699','#339999','#33cc99','#33ff99','#660099','#663399',
		'#666699','#669999','#66cc99','#66ff99','#cccccc','#0000cc','#0033cc','#0066cc','#0099cc','#00cccc','#00ffcc','#3300cc',
		'#3333cc','#3366cc','#3399cc','#33cccc','#33ffcc','#6600cc','#6633cc','#6666cc','#6699cc','#66cccc','#66ffcc','#ffffff',
		'#0000ff','#0033ff','#0066ff','#0099ff','#00ccff','#00ffff','#3300ff','#3333ff','#3366ff','#3399ff','#33ccff','#33ffff',
		'#6600ff','#6633ff','#6666ff','#6699ff','#66ccff','#66ffff','#ff0000','#990000','#993300','#996600','#999900','#99cc00',
		'#99ff00','#cc0000','#cc3300','#cc6600','#cc9900','#cccc00','#ccff00','#ff0000','#ff3300','#ff6600','#ff9900','#ffcc00',
		'#ffff00','#00ff00','#990033','#993333','#996633','#999933','#99cc33','#99ff33','#cc0033','#cc3333','#cc6633','#cc9933',
		'#cccc33','#CCFF33','#ff0033','#ff3333','#ff6633','#ff9933','#ffcc33','#ffff33','#0000ff','#990066','#993366','#996666',
		'#999966','#99cc66','#99ff66','#cc0066','#cc3366','#cc6666','#cc9966','#cccc66','#ccff66','#ff0066','#ff3366','#ff6666',
		'#ff9966','#ffcc66','#ffff66','#ffff00','#990099','#993399','#996699','#999999','#99cc99','#99ff99','#cc0099','#cc3399',
		'#cc6699','#cc9999','#cccc99','#ccff99','#ff0099','#ff3399','#ff6699','#ff9999','#ffcc99','#ffff99','#00ffff','#9900cc',
		'#9933cc','#9966cc','#9999cc','#99cccc','#99ffcc','#cc00cc','#cc33cc','#cc66cc','#cc99cc','#cccccc','#ccffcc','#ff00cc',
		'#ff33cc','#ff66cc','#ff99cc','#ffcccc','#ffffcc','#ff00ff','#9900ff','#9933ff','#9966ff','#9999ff','#99ccff','#99ffff',
		'#cc00ff','#cc33ff','#cc66ff','#cc99ff','#ccccff','#ccffff','#ff00ff','#ff33ff','#ff66ff','#ff99ff','#ffccff','#ffffff'	
	);
	this.total = this.colors.length;
	this.width = 19;
	this.hideCovered = true;
	this.init = colorSelInit;
	this.show = colorSelShow;
	this.hide = colorSelHide;
}
ColorSelection.affected = new Array();

//!--------------------------------------------------------------
// @function	colorSelInit
// @desc		Função de inicialização do objeto
// @return		void
//!--------------------------------------------------------------
function colorSelInit() {
	var c, o, p;
	if (this.ready) {
		var d = "<table style=\"font-family:tahoma,verdana,sans-serif;background-color:#000000\" cellpadding=\"0\" cellspacing=\"1\" width=\""+(this.width*10)+"\">";
		d += "<tr><td colspan=\""+this.width+"\" align=\"center\" style=\"font-size:11px;font-weight:bold;padding:1px;background:ActiveCaption;color:CaptionText;text-align:center\">"+colorSelTitle+"</td></tr>";
		for (var i=0; i<this.total; i++) {
			if ((i%this.width) == 0)
				d += "<tr>";
			o = (this.highlight ? " onMouseOver=\"colorSelHighlight('"+this.colors[i]+"','"+this.hlColor+"','"+this.hlValue+"');\" onMouseOut=\"colorSelOut('"+this.hlColor+"','"+this.hlValue+"');\"" : "");
			c = (this.callback != '' ? " onClick=colorSelHide('"+this.divId+"');"+this.callback+"('"+this.colors[i]+"')" : "colorSelHide('"+this.divId+"')");
			d += "<td style=\"background-color:"+this.colors[i]+";cursor:pointer;width:9px;height:9px\""+c+o+"></td>";
			if ((i+1)>this.total || ((i+1)%this.width) == 0)
				d += "</tr>";			
		}
		d += "<tr><td colspan=\""+this.width+"\"><table width=\"100%\" style=\"font-family:tahoma,verdana,sans-serif;font-size:11px;text-align:center;background-color:Window\"><tr>";
		d += "<td id=\""+this.hlColor+"\" width=\"50%\" style=\"background-color:"+this.curr+"\">&nbsp;</td><td id=\""+this.hlValue+"\" width=\"50%\" align=\"center\">#ffffff</td>";
		d += "</tr></table></td></tr>";
		d += "</table>";
		writeToDiv(this.div, true, true, d);
	}
}

//!--------------------------------------------------------------
// @function	colorSelHighlight
// @desc		Exibe a cor que está sendo apontada pelo mouse
// @param		color String		Valor da cor
// @param		clayer String		Nome do elemento onde deve ser mostrada a cor
// @param		vlayer String		Nome do elemento onde deve ser mostrado o valor RGB
// @return		void
//!--------------------------------------------------------------
function colorSelHighlight(color, clayer, vlayer) {
	window.status = colorSelChoose;
	var c = getDocumentObject(clayer);
	var v = getDocumentObject(vlayer);	
	if (c != null)
		c.style.backgroundColor = color;
	if (v != null)
		v.innerHTML = color.toUpperCase();
}

//!--------------------------------------------------------------
// @function	colorSelOut
// @desc		Retorna para a cor inicial (evento onMouseOut de uma célula de cor)
// @param		clayer String	Nome do elemento onde é mostrada a cor
// @param		vlayer String	Nome do elemento onde é mostrado o valor RGB
// @return		void
//!--------------------------------------------------------------
function colorSelOut(clayer, vlayer) {
	window.status = '';
	var c = getDocumentObject(clayer);
	var v = getDocumentObject(vlayer);	
	if (c != null)
		c.style.backgroundColor = '#ffffff';
	if (v != null)
		v.innerHTML = '#FFFFFF';
}

//!--------------------------------------------------------------
// @function	colorSelShow
// @desc		Mostra a palheta de cores, tomando como base a posição do parâmetro el
// @param		el HTMLElement object	Elemento abaixo do qual deve ser exibida a palheta
// @return		void
//!--------------------------------------------------------------
function colorSelShow(el) {
	if (this.ready) {
		var p = getAbsolutePos(el);
		var px = p.x-165;
		var py = p.y+el.offsetHeight;
		moveDivTo(this.div, px, py);
		setDivVisibility(this.div, true);
		(this.hideCovered && this.div.offsetWidth && this.div.offsetHeight) && (ColorSelection.affected = hideCoveredElements(px, px+this.div.offsetWidth, py, py+this.div.offsetHeight));
	}
}

//!--------------------------------------------------------------
// @function	colorSelHide
// @desc		Esconde a palheta de cores
// @param		layerId String		ID da div utilizada para exibir a palheta
// @return		void
//!--------------------------------------------------------------
function colorSelHide(layerId) {
	setDivVisibility(getDocumentObject(layerId), false);
	for (i=0; i<ColorSelection.affected.length; i++)
		setDivVisibility(ColorSelection.affected[i], true);
	ColorSelection.affected = new Array();
}