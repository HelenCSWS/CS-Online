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
// $Header: /www/cvsroot/php2go/resources/javascript/masks/time.js,v 1.2 2005/08/30 14:39:30 mpont Exp $
// $Date: 2005/08/30 14:39:30 $
// $Revision: 1.2 $

//!-------------------------------------------------------
// @function	chkMaskTIME
// @desc		Aplica máscara de tempo automaticamente
// @param		field Field object		Campo de um formulário
// @param		event Event object		Evento do teclado
// @return		Boolean
// @note		Aplica máscara de tempo segundo o padrão (HH:mm:ss)
//!-------------------------------------------------------
function chkMaskTIME(field, event) {
    var keyCode = (_dom == 1 || _dom == 2 ? window.event.keyCode : event.which);
    var key = String.fromCharCode(keyCode);
    var isKey = (String('0123456789:').indexOf(key) != -1);
    var isAction = (String('0,8,13').indexOf(keyCode) != -1 || event.ctrlKey);
    var ss = getEditCaretPos(field);
	var se = getSelectionEnd(field);
	var len = field.value.length;
    if (!isKey && !isAction)
    	return false;
	if (len == 8 && !isAction && ss == se) {
		return false;
	} else if (key == ':') {
		if ((len == 1 || len == 4) && field.value.charAt(len-1) != '0') {
			field.value = field.value.substr(0,field.value.length-1) + '0' + field.value.substr(field.value.length-1,1);
			return true;
		} else if (len != 2 && len != 5) {
			return false;
		}
	} else if (isAction) {
		return true;
	} else {
		if (len == 2 || len == 5) {
			field.value = field.value + ':';
			return true;
		}
	}
}

//!------------------------------------------------------------------
// @function	chkTIME
// @desc		Verifica se o campo possui um valor de tempo correto
// @param		field Field object	Campo de hora a ser verificada
// @return		Boolean
// @note		Valida valores de tempo no formato HH:mm:ss, sendo que os segundos não são obrigatórios
//!------------------------------------------------------------------
function chkTIME(field) {
	if (field.value.length == 0)
		return true;
	var h, m, s;
	var re = /^\d{1,2}\:\d{1,2}(\:\d{1,2})?$/;
	if (!re.test(field.value))
		return false;
	h = parseInt(field.value.substr(0, 2), 10);
	m = parseInt(field.value.substr(3, 2), 10);	
	if (length == 8) {
		s = parseInt(field.value.substr(6, 2), 10);
		if (s < 0 || s > 59)
			return false;
	}
	if	(h < 0 || h > 23 || m < 0 || m > 59)
		return false;
	return true;
}