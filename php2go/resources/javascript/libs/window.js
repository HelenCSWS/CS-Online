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
// $Header: /www/cvsroot/php2go/resources/javascript/libs/window.js,v 1.13 2005/05/19 22:54:54 mpont Exp $
// $Date: 2005/05/19 22:54:54 $
// $Revision: 1.13 $

//!--------------------------------------------------------
// @function	createWindow
// @desc		Configura e abre uma janela no Browser
// @param		url String			Url a ser aberta na janela
// @param		wid Integer			Largura da nova janela
// @param		hei Integer			Altura da nova janela
// @param		posx Integer		Posição x de abertura da janela
// @param		posy Integer		Posição y de abertura da janela
// @param		tit	String			Nome da janela
// @param		type Integer		Tipo da janela
// @param		evt Event object	Evento que disparou a função
// @param		ret Boolean			Se o valor deste parâmetro for true, a janela criada é retornada
// @return		Window object		A janela criada
// @note		O parâmetro type é uma soma dos atributos toolbar,
//				location, directories, status, menubar, scrollbars,
//				resizable e copyhistory utilizando bitwise. Os atributos
//				representam, respectivamente, os valores 1, 2, 4, 8, 16,
//				32, 64 e 128
//!--------------------------------------------------------
function createWindow (url, wid, hei, posx, posy, tit, type, evt, ret) {
	var win, x, y, pos, str = '';
	// posição padrão (usando o evento ou centralizando)
	pos = (evt == null ? getCenterPosition(wid, hei) : getFromSrcElement(evt));
	// posições fixas solicitadas
	posx = (posx != null && posx >= 0 ? posx : pos.x);
	posy = (posy != null && posy >= 0 ? posy : pos.y);
	// monta a string com os atributos da janela
	str =	(type & 1 ? 'toolbar=yes,' : 'toolbar=no,') +
			(type & 2 ? 'location=yes,' : 'location=no,') +
			(type & 4 ? 'directories=yes,' : 'directories=no,') +
			(type & 8 ? 'status=yes,' : 'status=no,') +
			(type & 16 ? 'menubar=yes,' : 'menubar=no,') +
			(type & 32 ? 'scrollbars=yes,' : 'scrollbars=no,') +
			(type & 64 ? 'resizable=yes,' : 'resizable=no,') +
			(type & 128 ? 'copyhistory=yes,' : 'copyhistory=no,') +
			'width=' + wid + ',height=' + hei + ',left=' + posx + ',top=' + posy;
	if (ret == true)
		return window.open(url, tit, str);
	window.open(url, tit, str);
}

//!--------------------------------------------------------
// @function	getCenterPosition
// @desc		Retorna uma posição centralizada
// @param		w Integer	Largura do objeto
// @param		h Integer	Altura do objeto
// @return		Object Coordenadas x e y centralizadas
//!--------------------------------------------------------
function getCenterPosition(w, h) {
	var r = { 
		x: parseInt((screen.width-w)/2),
		y: parseInt((screen.height-h)/2) 
	};
	return r;
}

//!--------------------------------------------------------
// @function	getFromSrcElement
// @desc		Retorna a posição para a janela a partir do elemento
//				que disparou o evento de abertura da janela
// @param		evt Event object	Evento ocorrido na página
// @return		Object Coordenadas x e y calculadas
//!--------------------------------------------------------
function getFromSrcElement(evt) {
	var result = 0;
	var ie = document.all && navigator.userAgent.indexOf('MSIE') != -1;
	var ns = document.layers || document.getElementById;
	var el = evt.srcElement || evt.target;
	var elpos = getAbsolutePos(el);
	var r = {
		x : (evt.screenX ? evt.screenX - (evt.offsetX ? evt.offsetX : 0) : window.outerWidth-window.innerWidth+elpos.x),
		y : (evt.screenY ? evt.screenY - (evt.offsetY ? evt.offsetY : 0) : window.outerHeight-window.innerHeight+elpos.y)
	};
	if (ie)
		r.x = (el.width ? r.x+el.width : (el.offsetWidth ? r.x+el.offsetWidth : r.x+(el.innerText.length*10)+5));
	else if (ns)
		r.x = (el.width ? r.x+el.width : r.x+el.offsetWidth-5);
	if (ns)
		r.y -= 10;
	return r;
}
