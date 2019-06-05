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
// $Header: /www/cvsroot/php2go/resources/javascript/libs/object.js,v 1.14 2005/06/01 16:01:49 mpont Exp $
// $Date: 2005/06/01 16:01:49 $
// $Revision: 1.14 $

//!--------------------------------------------------------------
// @function	getDocumentObject
// @desc		Retorna um objeto do documento a partir do seu nome/id
// @param		n String		Nome do objeto
// @param		d Object		Documento onde ele se encontra, opcional
// @return		Object O objeto procurado ou null caso ele não exista
//!--------------------------------------------------------------
function getDocumentObject(n, d) {
	var p,i,x;
	if (!d) d=document;
	if ((p=n.indexOf("?"))>0 && parent.frames.length) {
		d=parent.frames[n.substring(p+1)].document;
		n=n.substring(0,p);
	}
	if (!(x=d[n])&&d.all) x=d.all[n];
	for (i=0; !x && i<d.forms.length; i++) x = d.forms[i][n];
	for (i=0;!x&&d.layers&&i<d.layers.length;i++) x = getDocumentObject(n,d.layers[i].document);
	if (!x && document.getElementById) x = document.getElementById(n);
	return x;
}

//!--------------------------------------------------------------
// @function	getElementAttribute
// @desc		Retorna o valor de um atributo de um objeto
// @param		obj Object		Objeto
// @param		attr String		Nome do atributo
// @return		mixed O valor do atributo
//!--------------------------------------------------------------
function getElementAttribute(obj, attr) {
	var val = null;
	if (document.getElementById && obj.getAttribute)
		val = obj.getAttribute(attr, false);
	else if (document.all)
		val = eval("document.all['" + obj.id + "." + attr + "']");
	return val;
}

//!--------------------------------------------------------------
// @function	getAbsolutePos
// @desc		Busca a posição absoluta de um elemento
// @param		el HTMLElement object	Elemento HTML
// @return		object Objeto contendo as coordenadas absolutas X e Y
//!--------------------------------------------------------------
function getAbsolutePos(el) {
	var sl=0,st=0,d,t,r;
	d = /^div$/i.test(el.tagName);
	if (d && el.scrollLeft)
		sl = el.scrollLeft;
	if (d && el.scrollTop)
		st = el.scrollTop;
	var r = { 
		x: el.offsetLeft - sl, 
		y: el.offsetTop - st 
	};
	if (el.offsetParent) {
		t = getAbsolutePos(el.offsetParent);
		r.x += t.x;
		r.y += t.y;
	}
	return r;	
}

//!--------------------------------------------------------------
// @function	findParentElementByTag
// @desc		Busca, a partir de um determinado elemento, um ascendente que seja do tipo do parâmetro tag
// @param		start HTMLElement object	Elemento inicial de pesquisa
// @param		tag String					Tag a ser buscada
// @return		HTMLElement object Elemento encontrado ou null se inexistente
//!--------------------------------------------------------------
function findParentElementByTag(start, tag) {
	try {
		while (start != null && start.nodeName != tag)
			start = start.parentNode;
		return start;
	} catch(e) {
		return start;
	}
}

//!--------------------------------------------------------------
// @function	addEvent
// @desc		Adiciona um tratador de um evento em um determinado elemento
// @param		el HTMLElement object		Elemento onde o evento será tratado
// @param		eventName String			Nome do evento (keypress, keydown, click, mouseover, mouseout, ...)
// @param		callback Function object	Função que irá tratar o evento
// @return		void
//!--------------------------------------------------------------
function addEvent(el, eventName, callback) {
	if (el.attachEvent) // IE
		el.attachEvent('on' + eventName, callback);
	else if (el.addEventListener) // Mozilla/Gecko
		el.addEventListener(eventName, callback, true);
	else // outros browsers
		el['on' + eventName] = callback;
}

//!--------------------------------------------------------------
// @function	stopEvent
// @desc		Cancela a propagação de um determinado evento
// @param		e Event object	Evento a ser cancelado
// @return		void
//!--------------------------------------------------------------
function stopEvent(e) {
	if (document.all && _dom != 4) { // IE
		e.cancelBubble = true;
		e.returnValue = false;
	} else { // Mozilla/Gecko
		e.preventDefault();
		e.stopPropagation();
	}
}

//!--------------------------------------------------------------
// @function	setBackgroundColor
// @desc		Configura a cor de fundo de um determinado objeto da página
// @param		object Object		Objeto da página
// @param		color String		String RGB a ser aplicada ao objeto
// @return		void
// @note		Se a cor não for fornecida, a função adotará o valor 'transparent'
//!--------------------------------------------------------------
function setBackgroundColor(object, color) {
	if (color == null)
		color = 'transparent';
    if (typeof(window.opera) == 'undefined' && typeof(object.getAttribute) != 'undefined')
        object.setAttribute('bgcolor', color, 0);
    else
		object.style.backgroundColor = color;
}

//!--------------------------------------------------------------
// @function	getStyleAttribute
// @desc		Retorna o valor de um atributo do estilo de um objeto
// @param		o Object	Objeto da página
// @param		a String	Nome do atributo de estilo
// @return		mixed Valor do atributo
//!--------------------------------------------------------------
function getStyleAttribute(o, a) {
	var v = null;
	if (typeof(window.opera) == 'undefined' && typeof(o.style.getAttribute) != 'undefined') // Mozilla/Gecko
		v = o.style.getAttribute(a);
	else if (typeof(o.style) != 'undefined') // IE
		eval("v = o.style."+a+";");
	return v;
}

//!--------------------------------------------------------------
// @function	setStyleAttribute
// @desc		Configura o valor de um atributo do estilo de um objeto
// @param		o Object	Objeto da página
// @param		a String	Nome do atributo de estilo
// @param		v String	Valor para o atributo
// @return		void
//!--------------------------------------------------------------
function setStyleAttribute(o, a, v) {
    if (typeof(window.opera) == 'undefined' && typeof(o.style.getAttribute) != 'undefined') // Mozilla/Gecko
        o.style.setAttribute(a, v, 0);
    else // IE
		eval("o.style."+a+"='"+v+"';");
}

//!----------------------------------------------
// @function	debugObject
// @desc		Exibe informações sobre um objeto
// @param		obj Object	Objeto a ser examinado
// @param		cnt Integer	Número de propriedades a serem exibidas a cada vez
// @return		void
//!----------------------------------------------
function debugObject(obj, cnt) {
	var desc = "", i = 0, cnt = (cnt != null ? cnt : 20);
	if (!obj) {
		alert(debugUndefMsg);
	} else {
		for(var property in obj) {
			desc = desc + obj + "." + property + " = " + obj[property];
			if (i == cnt) {
				alert(desc); 
				desc = ""; 
				i = 0;
			} else {
				desc = desc + "\n"; i++;
			}
		}
	}
}