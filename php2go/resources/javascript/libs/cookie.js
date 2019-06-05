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
// $Header: /www/cvsroot/php2go/resources/javascript/libs/cookie.js,v 1.10 2005/01/21 17:20:02 mpont Exp $
// $Date: 2005/01/21 17:20:02 $
// $Revision: 1.10 $

//!--------------------------------------------------------------
// @function	setCookie
// @desc		Cria um cookie
// @param		name String				Nome do cookie
// @param		value String			Valor para o cookie
// @param		expires Object			Objeto Date para a expiração
// @param		path String				Caminho do cookie
// @param		domain String			Domínio do cookie
// @param		secure Boolean			Define se o cookie deve ser seguro
// @return		void
//!--------------------------------------------------------------
function setCookie(name, value) {
	var v,c,e,p,d,s;
	v = setCookie.arguments;
	c = setCookie.arguments.length;
	e = (2 < c) ? v[2] : null;
	p = (3 < c) ? v[3] : null;
	d = (4 < c) ? v[4] : null;
	s = (5 < c) ? v[5] : false;
	document.cookie = name + '=' + escape(value) +
		((e == null) ? '' : ('; expires=' + e.toGMTString())) +
		((p == null) ? '' : ('; path=' + p)) +
		((d == null) ? '' : ('; domain=' + d)) +
		((s == true) ? '; secure' : '');
}

//!--------------------------------------------------------------
// @function	getCookieVal
// @desc		Busca o valor de um cookie a partir da posição do
//				nome nos cookies do navegador
// @param		offset Integer	Posição
// @return		string Valor atribuído ao cookie
//!--------------------------------------------------------------
function getCookieVal(offset) {
	var endstr = document.cookie.indexOf(';', offset);
	if (endstr == -1) {
		endstr = document.cookie.length;
	}
	return unescape(document.cookie.substring(offset, endstr));
}

//!--------------------------------------------------------------
// @function	getCookie
// @desc		Busca o valor de um cookie através de seu nome
// @param		name String	Nome do cookie
// @return		string Valor do cookie se ele for encontrado ou null em caso contrário
//!--------------------------------------------------------------
function getCookie(name) {
	var a,c,l,i;
	a = name + '=';
	c = a.length;
	l = (typeof(document.cookie) != 'undefined') ? document.cookie.length : 0;
	i = 0;
	while (i < l) {
		var j = i + c;
		if (document.cookie.substring(i, j) == a)
			return getCookieVal(j);
		i = document.cookie.indexOf(' ', i) + 1;
		if (i == 0)
			break;
	}
	return null;
}

//!--------------------------------------------------------------
// @function	deleteCookie
// @desc		Remove um cookie
// @param		name String	Nome do cookie
// @return		void
// @note		Se o cookie não existir, a função criará um cookie
//				com data já expirada, o que não afetará os outros cookies
//				já existentes
//!--------------------------------------------------------------
function deleteCookie(name) {
	var d = new Date();
	d.setTime (d.getTime() - 1);
	var c = getCookie(name);
	document.cookie = name + "=" + c + "; expires=" + d.toGMTString();
}

//!--------------------------------------------------------------
// @function	buildExpireDate
// @desc		Constrói uma data futura de expiração a partir de
//				um valor para número de dias / horas / minutos ou
//				segundos de durabilidade do cookie
// @param		days Integer		Número de dias, padrão é 0
// @param		hours Integer		Número de Horas, padrão é 0
// @param		minutes Integer		Número de Minutos, padrão é 0
// @param		seconds Integer		Número de Segundos, padrão é 0
// @return		string Objeto Date que contém a data atual mais o deslocamento solicitado
//!--------------------------------------------------------------
function buildExpireDate(days, hours, minutes, seconds) {
	var e = new Date();
	var n = e.getTime();
	var tc = 0;	
	tc += (days != null) ? (days * 24 * 60 * 60 * 1000) : 0;
	tc += (hours != null) ? (hours * 60 * 60 * 1000) : 0;
	tc += (minutes != null) ? (minutes * 60 * 1000) : 0;
	tc += (seconds != null) ? (seconds * 1000) : 0;	
	e.setTime(n + tc);	
	return (e);
}
