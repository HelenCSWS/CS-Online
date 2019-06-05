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
// $Header: /www/cvsroot/php2go/resources/javascript/modules/calculator.js,v 1.13 2005/06/13 14:48:57 mpont Exp $
// $Date: 2005/06/13 14:48:57 $
// $Revision: 1.13 $

//----------------------------------------
// Captura do evento onKeyPress do teclado
//----------------------------------------
if (document.layers) 
	document.captureEvents(Event.KEYPRESS);
document.onkeypress = calcKeyHandler;

//----------------------------------------
// Inicialização de valores
//----------------------------------------
var Accum = 0;           // -> Operando anterior aguardando operação
var FlagNewNum = false;  // -> Indica se um novo número foi inserido
var PendingOp = "";      // -> Uma operação esperando pelo segundo operador

//!----------------------------------------------------------------
// @function	calcKeyHandler
// @desc		Executa os comandos da calculadora a partir de eventos do teclado
// @param		e Event object	Evento
// @return		void
//!----------------------------------------------------------------
function calcKeyHandler(e) {
	if (!e) e = window.event;
	var agent = navigator.userAgent.toLowerCase();
	var ie = (agent.indexOf("msie") != -1 && agent.indexOf("opera") == -1);
	var mozilla = (agent.indexOf("mozilla") != -1 && agent.indexOf("msie") == -1);
	var key = (ie ? e.keyCode : (mozilla ? e.which : null));	
	if (key == null)
		return false;
	if (key >= 48 && key <= 57) {
		numPressed(key-48);
	} else {
		switch (key) {
			case 43: // +
				Operation('+'); stopEvent(e); break;
			case 45: // -
				Operation('-'); stopEvent(e); break;
			case 42: // *
				Operation('*'); stopEvent(e); break;
			case 47: // /
				Operation('/'); stopEvent(e); break;
			case 67: // c
			case 99: // C
				Clear(); stopEvent(e); break;
			case 8: // backspace
			case 44: // del
			case 90: // del
			case 122:
				Backspace(); stopEvent(e); break;
			case 13: // enter
			case 61: // =
				Operation('='); stopEvent(e); break;
			case 46: // .
				Decimal(); stopEvent(e); break;
			case 82: // r
			case 114: // R
				if (!e.ctrlKey && !e.altKey) { GetResult(); stopEvent(e); } break;
			case 37: // %
				Percent(); stopEvent(e); break;
		}
	}
}

//!----------------------------------------------------------------
// @function	Backspace
// @desc		Implementa a remoção do caractere mais à direita do valor atual digitado
// @return		void
//!----------------------------------------------------------------
function Backspace() {
	if (document.Keypad.ReadOut.value.length == 1) {
		document.Keypad.ReadOut.value = "0";
	} else {
		document.Keypad.ReadOut.value = document.Keypad.ReadOut.value.substr(0,document.Keypad.ReadOut.value.length-1);
	}
}

//!----------------------------------------------------------------
// @function	numPressed
// @desc		Insere um número ao valor atual de um operando
// @param		Num String	Novo número
// @return		void
//!----------------------------------------------------------------
function numPressed(Num) {
	if (FlagNewNum) {
		document.Keypad.ReadOut.value  = Num;
		FlagNewNum = false;
	} else {
		if (document.Keypad.ReadOut.value == "0")
			document.Keypad.ReadOut.value = Num;
		else
			document.Keypad.ReadOut.value += Num;
	}
}

//!----------------------------------------------------------------
// @function	Operation
// @desc		Aplica na equação atual uma determinada operação
// @param		Op String		Operação: '+', '-', '*', '/', '%'
// @return		void
//!----------------------------------------------------------------
function Operation(Op) {
	var Readout = document.Keypad.ReadOut.value;
	if (FlagNewNum && PendingOp != "=");
	else {
		FlagNewNum = true;
		if ( '+' == PendingOp )
			Accum += parseFloat(Readout);
		else if ( '-' == PendingOp )
			Accum -= parseFloat(Readout);
		else if ( '/' == PendingOp )
			Accum /= parseFloat(Readout);
		else if ( '*' == PendingOp )
			Accum *= parseFloat(Readout);
		else
			Accum = parseFloat(Readout);
		document.Keypad.ReadOut.value = Accum;
		PendingOp = Op;
	}
}

//!----------------------------------------------------------------
// @function	Decimal
// @desc		Trata a inserção do ponto decimal no operando atual
// @return		void
//!----------------------------------------------------------------
function Decimal() {
	var curReadOut = document.Keypad.ReadOut.value;
	if (FlagNewNum) {
		curReadOut = "0.";
		FlagNewNum = false;
	} else {
		if (curReadOut.indexOf(".") == -1)
			curReadOut += ".";
	}
	document.Keypad.ReadOut.value = curReadOut;
}

//!----------------------------------------------------------------
// @function	clearEntry
// @desc		Reseta o operando atual
// @return		void
//!----------------------------------------------------------------
function clearEntry() {
	document.Keypad.ReadOut.value = "0";
	FlagNewNum = true;
}

//!----------------------------------------------------------------
// @function	Clear
// @desc		Reseta a calculadora (acumulador, operação)
// @return		void
//!----------------------------------------------------------------
function Clear() {
	Accum = 0;
	PendingOp = "";
	clearEntry();
}

//!----------------------------------------------------------------
// @function	Neg
// @desc		Inverte o sinal do valor atual
// @return		void
//!----------------------------------------------------------------
function Neg() {
	document.Keypad.ReadOut.value = parseFloat(document.Keypad.ReadOut.value) * -1;
}

//!----------------------------------------------------------------
// @function	Percent
// @desc		Aplica o operador '%' sobre o operando atual e o acumulador
// @return		void
//!----------------------------------------------------------------
function Percent() {
	document.Keypad.ReadOut.value = (parseFloat(document.Keypad.ReadOut.value) / 100) * parseFloat(Accum);
}

//!----------------------------------------------------------------
// @function	GetResult
// @desc		Retorna o resultado da operação para a janela principal
// @return		Boolean
// @note		Caso a máscara do campo no formulário inferior for CURRENCY (moeda),
//				o valor será formatado antes da alteração do campo
//!----------------------------------------------------------------
function GetResult() {
	var r,m;
	try {
		eval("target = parent.opener.document." + outerForm + ".elements['" + outerField + "'];");
	} catch (e) {
		return false;
	}
	if (outerMask.toUpperCase() == "CURRENCY") {
		formatCurrency(document.Keypad.ReadOut);
	} else {
		r = new RegExp("^FLOAT\-(\\d+):(\\d+)$");
		if (m = r.exec(outerMask.toUpperCase())) {
			if (!checkFloat(m[1], m[2])) {
				alert(p2gInvalidVal);
				return false;
			}
		}
	}
	target.value = document.Keypad.ReadOut.value;
	if (typeof(target.disable) == 'undefined' || target.disabled == false) {
		target.focus();
	}
	window.close();
	return true;
}

//!----------------------------------------------------------------
// @function	formatCurrency
// @desc		Formata o número gerado para valor de moeda
// @param		field Field object	Campo com o valor atual da calculadora
// @return		void
//!----------------------------------------------------------------
function formatCurrency(field) {
	var newValue = "";
	var negative = false;
	fieldValue = field.value;
	if (parseFloat(fieldValue) < 0) {
		negative = true;
		fieldValue = String(Math.abs(parseFloat(fieldValue)));
	}
	dotPos = fieldValue.indexOf(".");
	if (dotPos != -1) {
		if ((dotPos+1) == (fieldValue.length-1)) fieldValue = fieldValue + "0";
		fieldValue = fieldValue.substr(0, dotPos+3);
		fieldValue = fieldValue.replace(/\./g,"");
	} else {
		fieldValue = fieldValue + "00";
	}
	var inicio   = fieldValue.substr(0, (fieldValue.length-2)%3);
	var resto    = fieldValue.substr((fieldValue.length-2)%3, fieldValue.length-((fieldValue.length-2)%3)-2);
	var centavos = fieldValue.substr(fieldValue.length-2, 2);
	if (negative) {
		newValue = newValue + "-";
	}
	if (inicio != "") {
		newValue = newValue + inicio;
	}
	if (resto != "") {
		for (i=0; i<resto.length; i++) {
			if (((i>0) && ((i%3)==0)) || ((i==0) && (inicio != ""))) {
				newValue = newValue + '.';
			}
			newValue = newValue + resto.charAt(i);
		}
	}
	if (fieldValue.length >= 2) {
		newValue = newValue + ',';
	}
	newValue = newValue + centavos;
	field.value = newValue;
}

//!----------------------------------------------------------------
// @function	checkFloat
// @desc		Valida a precisão numérica do resultado da operação,
//				conferindo se os tamanhos das partes inteira e decimal
//				não excedem os tamanhos definidos no formulário
// @param		l int	Tamanho máximo da parte inteira
// @param		r int	Tamanho máximo da parte decimal
// @return		Boolean
//!----------------------------------------------------------------
function checkFloat(l, r) {
	var s = document.Keypad.ReadOut.value;
	var p = s.indexOf('.');
	var r = parseInt(r);
	if (p == -1) {
		if (s.length > l)
			return false;
		else
			return true;
	} else {
		if (s.substring(0,p).length > l) {
			return false;
		} else if ((s.substring(p).length-1) > r) {
			document.Keypad.ReadOut.value = s.substring(0,p+1) + s.substring(p+1,p+r+1);
			return true;
		} else {
			return true;
		}
	}
}