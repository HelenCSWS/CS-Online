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
// $Header: /www/cvsroot/php2go/resources/javascript/masks/integer.js,v 1.11 2005/08/30 14:39:30 mpont Exp $
// $Date: 2005/08/30 14:39:30 $
// $Revision: 1.11 $

//!----------------------------------------------------------------
// @function	chkMaskINTEGER
// @desc		Aplica máscara de número inteiro automaticamente
// @param		field Field object		Campo de um formulário
// @param		event Event object		Evento do teclado
// @return		Boolean
//!----------------------------------------------------------------
function chkMaskINTEGER(field,event) {
    var keyCode = (_dom == 1 || _dom == 2 ? window.event.keyCode : event.which);
    var key = String.fromCharCode(keyCode);
    var isKey = (String('0123456789/').indexOf(key) != -1);
    var isAction = (String('0,8,13').indexOf(keyCode) != -1 || event.ctrlKey);
    return (isKey || isAction);
}

//!------------------------------------------------------------
// @function	chkINTEGER
// @desc		Verifica se o campo possui um número inteiro
// @param		field Field object	Campo inteiro a ser verificado
// @return		Boolean
//!------------------------------------------------------------
function chkINTEGER(field) {
	var regExp = /^(\+|\-)?[0-9]+$/;
	var integerValue = parseInt(field.value);
	if (!regExp.test(field.value)) {
		return false;
	} else if (field.value.length == 0 || integerValue == 0) {
		return true;
	} else if (integerValue) {
		return true;
	} else {
		return false;
	}
}