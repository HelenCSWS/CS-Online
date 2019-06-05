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
// $Header: /www/cvsroot/php2go/resources/javascript/masks/url.js,v 1.14 2005/08/30 14:39:30 mpont Exp $
// $Date: 2005/08/30 14:39:30 $
// $Revision: 1.14 $

//!--------------------------------------------------------
// @function	chkMaskURL
// @desc		Aplica máscara de URL automaticamente
// @param		field Field object		Campo de um formulário
// @param		event Event object		Evento do teclado
// @return		Boolean
//!--------------------------------------------------------
function chkMaskURL(field, event) {
    var keyCode = (_dom == 1 || _dom == 2 ? window.event.keyCode : event.which);
    var key = String.fromCharCode(keyCode);
    var isKey = (String('1234567890qwertyuiop[]asdfghjklzxcvbnm.:/@-+?&%$#_~QWERTYUIOPASDFGHJKLZXCVBNM').indexOf(key) != -1);
    var isAction = (String('0,8,13').indexOf(keyCode) != -1 || event.ctrlKey);
	return (isKey || isAction);
}

//!-------------------------------------------------------------------
// @function	chkURL
// @desc		Verifica se uma URL está no formato correto
// @param		field Field object	Campo de data a ser verificada
// @return		Boolean
//!-------------------------------------------------------------------
function chkURL(field) {
	if (field.value.length == 0)
		return true;
	var urlPattern = /^(ht|f)tps?\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z0-9\-\._]{2,3}(:[a-zA-Z0-9]*)?\/?([a-zA-Z0-9\-\._\?\,\'\/\\\+&%\$#\=~])*$/;
	return urlPattern.test(field.value.toLowerCase());
}

//!-------------------------------------------------------------------
// @function	encodeURL
// @desc		Codifica os parâmetros de uma URL no formato base64
// @param		url String Valor da URL
// @param		varName String Nome da variável para envio dos parâmetros codificados
// @see			Url::encode
//!-------------------------------------------------------------------
function encodeURL(url, varName) {
	var pattern, matches;
	pattern = new RegExp(/([^\?]+)\?(.+)/);
	matches = pattern.exec(url);
	if (matches && matches.length == 3) {
		if (varName == null) varName = 'p2gvar';
		return matches[1] + '?' + varName + '=' + base64Encode(matches[2]);
	} else {
		return url;
	}
}

//!-------------------------------------------------------------------
// @function	decodeURL
// @desc		Decodifica os parâmetros de uma URL, codificados no formato base64
// @param		url String Valor da URL
// @return		String URL original decodificada
//!-------------------------------------------------------------------
function decodeURL(url) {
	var pattern, matches;
	pattern = new RegExp(/([^\?]+)\?[^=]+=(.+)/);
	matches = pattern.exec(url);
	if (matches && matches.length == 3) {
		return matches[1] + '?' + base64Decode(matches[2]);
	} else {
		return url;
	}
}