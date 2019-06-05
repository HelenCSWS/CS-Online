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
// $Header: /www/cvsroot/php2go/resources/javascript/masks/cpfcnpj.js,v 1.9 2005/08/30 14:39:30 mpont Exp $
// $Date: 2005/08/30 14:39:30 $
// $Revision: 1.9 $

//!----------------------------------------------------------------
// @function	chkMaskCPFCNPJ
// @desc		Aplica máscara de CPF/CNPJ automaticamente
// @param		field Field object		Campo de um formulário
// @param		event Event object		Evento do teclado
// @return		Boolean
//!----------------------------------------------------------------
function chkMaskCPFCNPJ(field,event) {
    var keyCode = (_dom == 1 || _dom == 2 ? window.event.keyCode : event.which);
    var key = String.fromCharCode(keyCode);
    var isKey = (String('0123456789').indexOf(key) != -1);
    var isAction = (String('0,8,13').indexOf(keyCode) != -1 || event.ctrlKey);
    return (isKey || isAction);
}

//!--------------------------------------------------------------
// @function	chkCPFCNPJ
// @desc		Verifica se um CPF/CNPJ é válido
// @param		field Field object	Campo CPF/CNPJ a ser verificado
// @return		Boolean
//!--------------------------------------------------------------
function chkCPFCNPJ(field) {
	if (field.value.length == 0) {
		return true;
	} else if (field.value.length == 14) {
		soma1 = (field.value.charAt(0) * 5) +
			 (field.value.charAt(1) * 4) +
			 (field.value.charAt(2) * 3) +
			 (field.value.charAt(3) * 2) +
			 (field.value.charAt(4) * 9) +
			 (field.value.charAt(5) * 8) +
			 (field.value.charAt(6) * 7) +
			 (field.value.charAt(7) * 6) +
			 (field.value.charAt(8) * 5) +
			 (field.value.charAt(9) * 4) +
			 (field.value.charAt(10) * 3) +
			 (field.value.charAt(11) * 2);
		resto = soma1 % 11;
		digito1 = resto < 2 ? 0 : 11 - resto;
		soma2 = (field.value.charAt(0) * 6) +
			 (field.value.charAt(1) * 5) +
			 (field.value.charAt(2) * 4) +
			 (field.value.charAt(3) * 3) +
			 (field.value.charAt(4) * 2) +
			 (field.value.charAt(5) * 9) +
			 (field.value.charAt(6) * 8) +
			 (field.value.charAt(7) * 7) +
			 (field.value.charAt(8) * 6) +
			 (field.value.charAt(9) * 5) +
			 (field.value.charAt(10) * 4) +
			 (field.value.charAt(11) * 3) +
			 (field.value.charAt(12) * 2);
		resto = soma2 % 11;
		digito2 = resto < 2 ? 0 : 11 - resto;
		return ((field.value.charAt(12) == digito1) && (field.value.charAt(13) == digito2));
	} else if (field.value.length == 11) {
		soma1 = (field.value.charAt(0) * 10) +
			 (field.value.charAt(1) * 9) +
			 (field.value.charAt(2) * 8) +
			 (field.value.charAt(3) * 7) +
			 (field.value.charAt(4) * 6) +
			 (field.value.charAt(5) * 5) +
			 (field.value.charAt(6) * 4) +
			 (field.value.charAt(7) * 3) +
			 (field.value.charAt(8) * 2);
		resto = soma1 % 11;
		digito1 = resto < 2 ? 0 : 11 - resto;
		soma2 = (field.value.charAt(0) * 11) +
			 (field.value.charAt(1) * 10) +
			 (field.value.charAt(2) * 9) +
			 (field.value.charAt(3) * 8) +
			 (field.value.charAt(4) * 7) +
			 (field.value.charAt(5) * 6) +
			 (field.value.charAt(6) * 5) +
			 (field.value.charAt(7) * 4) +
			 (field.value.charAt(8) * 3) +
			 (field.value.charAt(9) * 2);
		resto = soma2 % 11;
		digito2 = resto < 2 ? 0 : 11 - resto;
		return ((field.value.charAt(9) == digito1) && (field.value.charAt(10) == digito2));
	} else {
		return false;
	}
}
