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
// $Header: /www/cvsroot/php2go/resources/javascript/masks/login.js,v 1.10 2005/08/30 14:39:30 mpont Exp $
// $Date: 2005/08/30 14:39:30 $
// $Revision: 1.10 $

//!----------------------------------------------------------------
// @function	chkMaskLOGIN
// @desc		Aplica máscara de login automaticamente
// @param		field Field object		Campo de um formulário
// @param		event Event object		Evento do teclado
// @return		Boolean
//!----------------------------------------------------------------
function chkMaskLOGIN(field, event) {
    var keyCode = (_dom == 1 || _dom == 2 ? window.event.keyCode : event.which);
    var key = String.fromCharCode(keyCode);
    var isKey = (String('1234567890qwertyuiopasdfghjklzxcvbnm.-_QWERTYUIOPASDFGHJKLZXCVBNM').indexOf(key) != -1);
    var isAction = (String('0,8,13').indexOf(keyCode) != -1 || event.ctrlKey);
	return (isKey || isAction);
}

//!------------------------------------------------------------
// @function	chkLOGIN
// @desc		Verifica se o campo possui um login válido
// @param		field Field object	Campo login a ser verificado
// @return		Boolean
//!------------------------------------------------------------
function chkLOGIN(field) {
	var loginRegExp = /^\w+((-\w+)|(\.\w+))*$/;
    return loginRegExp.test(field.value);
}
