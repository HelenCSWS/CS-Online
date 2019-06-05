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
// $Header: /www/cvsroot/php2go/resources/javascript/libs/string.js,v 1.12 2005/04/25 13:29:44 mpont Exp $
// $Date: 2005/04/25 13:29:44 $
// $Revision: 1.12 $

String.prototype.trim = function() {
	var ltrim = this.replace(/^\s+/, '');
	return ltrim.replace(/\s+$/, '');
};

//!----------------------------------------------------------
// @function	trim
// @desc		Elimina espaços em branco à esquerda e à direita do campo
// @param		str String		Valor a ser processado
// @return		String Valor sem os espaços em branco desnecessários
//!----------------------------------------------------------
function trim(fieldValue) {
	if (fieldValue == null)
		return '';
	return fieldValue.trim();
}

//!----------------------------------------------------------
// @function	stringReplace
// @desc		Simulação do funcionamento da função sprintf recebendo
//				apenas parâmetros do tipo string e nomeando as variáveis
//				com índices para substituição: %1, %2, %3, ...
// @note		Recebe um número variável de argumentos: o texto original e N variáveis de substuição
// @return		String Valor com as substituições feitas
//!----------------------------------------------------------
function stringReplace() {
	var argv = stringReplace.arguments;
	var argc = argv.length;
	if (argc == 0) {
		return '';
	} else if (argc == 1) {
		return argv[0];
	} else {
		for (var i=1; i<argc; i++) {			
			argv[0] = argv[0].replace("%" + i, String(argv[i]));
		}
		return argv[0];
	}
}

//!----------------------------------------------------------
// @function	capitalizeWords
// @desc		Implementa a capitalização de todas as palavras de uma string
// @param		s String	Texto a ser processado
// @return		String Valor da string transformado
// @note		Esta função é executada quando o atributo CAPITALIZE é 
//				inserido em um campo do tipo EditField
//!----------------------------------------------------------
function capitalizeWords(s) {
	var f, r;
	var w = s.split(/\s+/g);
	for (var i=0; i<w.length; i++) {
		f = w[i].substring(0,1).toUpperCase();
		r = w[i].substring(1, w[i].length).toLowerCase();
		w[i] = f + r;
	}
	s = w.join(' ');
	return s;
}

var base64EncodeChars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/"; 
var base64DecodeChars = new Array(
	-1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
	-1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
	-1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 62, -1, -1, -1, 63,
	52, 53, 54, 55, 56, 57, 58, 59, 60, 61, -1, -1, -1, -1, -1, -1,
	-1, 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14,
	15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, -1, -1, -1, -1, -1,
	-1, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40,
	41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, -1, -1, -1, -1, -1
);

//!----------------------------------------------------------
// @function	base64Encode
// @desc		Codifica uma string no formato base64
// @param		str String	Texto a ser codificado
// @return		String Texto codificado
//!----------------------------------------------------------
function base64Encode(str) {
	var out, i, len, c1, c2, c3;
	var len = str.length; 
	var i = 0; 
	out = ""; 
	while(i < len) { 
		c1 = str.charCodeAt(i++) & 0xff; 
		if(i == len) { 
			out += base64EncodeChars.charAt(c1 >> 2); 
			out += base64EncodeChars.charAt((c1 & 0x3) << 4); 
			out += "=="; 
			break; 
		} 
		c2 = str.charCodeAt(i++); 
		if(i == len) { 
			out += base64EncodeChars.charAt(c1 >> 2); 
			out += base64EncodeChars.charAt(((c1 & 0x3)<< 4) | ((c2 & 0xF0) >> 4)); 
			out += base64EncodeChars.charAt((c2 & 0xF) << 2); 
			out += "="; 
			break; 
		} 
		c3 = str.charCodeAt(i++); 
		out += base64EncodeChars.charAt(c1 >> 2); 
		out += base64EncodeChars.charAt(((c1 & 0x3)<< 4) | ((c2 & 0xF0) >> 4)); 
		out += base64EncodeChars.charAt(((c2 & 0xF) << 2) | ((c3 & 0xC0) >>6)); 
		out += base64EncodeChars.charAt(c3 & 0x3F); 
	} 
	return out; 
}

//!----------------------------------------------------------
// @function	base64Decode
// @desc		Decodifica uma string no formato base64 para o formato ASCII
// @param		str String Texto a ser decodificado
// @return		String Valor original da string
//!----------------------------------------------------------
function base64Decode(str) {
	var out, i, len, c1, c2, c3, c4;
	len = str.length;
	i = 0;
	out = "";
	while(i < len) {
		do {
			c1 = base64DecodeChars[str.charCodeAt(i++) & 0xff];
		} while(i < len && c1 == -1);
		if(c1 == -1) break;
		do {
			c2 = base64DecodeChars[str.charCodeAt(i++) & 0xff];
		} while(i < len && c2 == -1);
		if(c2 == -1) break;
		out += String.fromCharCode((c1 << 2) | ((c2 & 0x30) >> 4));
		do {
			c3 = str.charCodeAt(i++) & 0xff;
			if(c3 == 61) return out;
			c3 = base64DecodeChars[c3];
		} while(i < len && c3 == -1);
		if(c3 == -1) break;
		out += String.fromCharCode(((c2 & 0XF) << 4) | ((c3 & 0x3C) >> 2));
		do {
			c4 = str.charCodeAt(i++) & 0xff;
			if(c4 == 61) return out;
			c4 = base64DecodeChars[c4];
		} while(i < len && c4 == -1);
		if(c4 == -1) break;
		out += String.fromCharCode(((c3 & 0x03) << 6) | c4);
	}
	return out;
}
