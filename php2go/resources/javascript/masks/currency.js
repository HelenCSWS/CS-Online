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
// $Header: /www/cvsroot/php2go/resources/javascript/masks/currency.js,v 1.12 2005/08/30 14:39:30 mpont Exp $
// $Date: 2005/08/30 14:39:30 $
// $Revision: 1.12 $

//!----------------------------------------------------------------
// @function	chkMaskCURRENCY
// @desc		Aplica máscara de moeda automaticamente
// @param		field Field object		Campo de um formulário
// @param		event Event object		Evento do teclado
// @return		Boolean
//!----------------------------------------------------------------
function chkMaskCURRENCY(field,event) {
    var keyCode = (_dom == 1 || _dom == 2 ? window.event.keyCode : event.which);
    var key = String.fromCharCode(keyCode);
    var isKey = (String('-0123456789').indexOf(key) != -1);
    var isAction = (String('0,8,13').indexOf(keyCode) != -1 || event.ctrlKey);
	var negative = false;
	var fieldValue = field.value;
	var newValue = "";
	if ((parseFloat(fieldValue) < 0) || (field.value.charAt(0) == "-"))
		negative = true;
	if (!isKey && !isAction)
		return false;
	else if (isAction)
		return true;
	else if (field.value.length == field.maxLength)
		return false;
	else if ((key == "-") && (field.value.length > 0))
		return false;
	else {
		fieldValue = field.value.replace(",","");
		fieldValue = fieldValue.replace("-","");
		while (fieldValue.indexOf('.') != -1) 
			fieldValue = fieldValue.replace(".","");
		var inicio   = fieldValue.substr(0,((fieldValue.length-1)%3));
		var centavos = fieldValue.substr(fieldValue.length-1,1);
		var resto    = fieldValue.substr(((fieldValue.length-1)%3),fieldValue.length-1-((fieldValue.length-1)%3));
		if (negative)
			newValue = newValue + "-";
		if (inicio != "")
			newValue = newValue + inicio;
		for (i=0; i<resto.length; i++) {
			if (((i>0) && ((i%3)==0)) || ((i==0) && (inicio != "")))
				newValue = newValue + '.';
			newValue = newValue + resto.charAt(i);
		}
		if (fieldValue.length >= 2)
			newValue = newValue + ',';
		newValue = newValue + centavos;
		field.value = newValue;
	}
}

//!----------------------------------------------------------------
// @function	chkCURRENCY
// @desc		Verifica se o campo possui um valor válido de moeda
// @param		field Field object	Campo moeda a ser verificado
// @return		Boolean
//!----------------------------------------------------------------
function chkCURRENCY(field) {
	if (field.value.length == 1) return false;
	if (field.value.length == 2) {
		if (field.value.charAt(0) == "-")
			field.value = "-0,0" + field.value.charAt(1);
		else
			field.value = "0," + field.value;
	}
	if ((field.value.length == 3) && (field.value.charAt(0) == "-"))
		field.value = "-0," + field.value.substring(1,3);
	var strRegExp = "";
	var size = field.value.length;
	if (field.value.charAt(0) == '-') size--;
	var start = (size - 3) % 4;
	var rest = size - start - 3;
	if (size == 0) {
		return true;
	} else {
		strRegExp = "/^\-?\\d{"+start+"}";
		for (var i=0; i<rest; i=i+4)
			strRegExp = strRegExp + "(\\.)\\d{3}";
		strRegExp = strRegExp + "(\\,)\\d{2}$/";
		eval("var objRegExp = "+strRegExp);
		if (objRegExp.test(field.value))
			return true;
		else
			return false;
	}
}
