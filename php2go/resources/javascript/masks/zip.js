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
// $Header: /www/cvsroot/php2go/resources/javascript/masks/zip.js,v 1.11 2005/08/30 14:39:30 mpont Exp $
// $Date: 2005/08/30 14:39:30 $
// $Revision: 1.11 $

//!----------------------------------------------------------------
// @function	chkMaskZIP
// @desc		Aplica máscara de Código Postal automaticamente
// @param		field Field object	Campo de um formulário
// @param		event Event object	Evento do teclado
// @param		llen Integer		Número de caracteres antes do separador '-'
// @param		rlen Integer		Número de caracteres depois do separador '-'
// @return		Boolean
//!----------------------------------------------------------------
function chkMaskZIP(field, event, llen, rlen) {
	(llen != null) || (llen = 5);
	(rlen != null) || (rlen = 3);
    var keyCode = (_dom == 1 || _dom == 2 ? window.event.keyCode : event.which);
    var key = String.fromCharCode(keyCode);
    var isKey = (String('0123456789').indexOf(key) != -1);
    var isAction = (String('0,8,13').indexOf(keyCode) != -1 || event.ctrlKey);
    var ss = getEditCaretPos(field);
    var se = getSelectionEnd(field);        
    var len = (llen + rlen + 1);
	var pos = field.value.indexOf('-');
	if (!isKey && !isAction)
		return false;
	if (!isAction && field.value.length == len && ss == se)
		return false;
	if (field.value.length == len && isAction)
		return true;
	if (!isAction && pos != -1 && ss > pos && field.value.substring(pos+1).length == rlen)
		return false;
	if (field.value.length == llen && pos == -1 && ss == se) {
		field.value = field.value + '-';
		return true;
	}
	if (!isAction && ss == llen && pos == -1 && field.value.substring(ss).length < rlen) {
		field.value = field.value.substring(0,ss) + '-' + key + field.value.substring(ss);
		setEditCaretPos(field, ss+2);
		stopEvent(event);
		return false;
	}
	return true;
}

//!------------------------------------------------------------
// @function	chkZIP
// @desc		Verifica se o campo possui um Código Postal válido
// @param		field Field object	Campo de Código Postal a ser verificado
// @param		llen Integer			Número de caracteres antes do separador '-'
// @param		rlen Integer			Número de caracteres depois do separador '-'
// @return		Boolean
//!------------------------------------------------------------
function chkZIP(field, llen, rlen) {
	var re = new RegExp("^\\d{"+llen+"}-\\d{"+rlen+"}$");
	var re2 = new RegExp("^\\d{" + (llen+rlen) + "}$");
	if (field.value.length == 0)
		return true;
	if (re2.test(field.value))
		field.value = field.value.substr(0,llen) + '-' + field.value.substr(llen,rlen);
	if (!re.test(field.value)) {
		return false;
	} else {
		return true;
	}
}