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
// $Header: /www/cvsroot/php2go/resources/javascript/masks/date.js,v 1.14 2005/08/30 14:37:35 mpont Exp $
// $Date: 2005/08/30 14:37:35 $
// $Revision: 1.14 $

//!-------------------------------------------------------
// @function	chkMaskDATE
// @desc		Aplica e valida máscara para um campo de data, nos formatos
//				DD/MM/YYYY ou YYYY/MM/DD
// @param		field HTMLInputElement object Campo de formulário
// @param		event Event object Evento do teclado
// @param		format String Formato de data ativo
// @note		O formato de data a ser utilizado é definido pela chave
//				LOCAL_DATE_FORMAT do array global de configuração do PHP2Go
// @return		Boolean
//!-------------------------------------------------------
function chkMaskDATE(field, event, format) {
    (format != null && (format == 'EURO' || format == 'US')) || (format = 'EURO');
    var p1 = (format == 'EURO' ? 2 : 4);
    var p2 = (format == 'EURO' ? 5 : 7);
    var keyCode = (_dom == 1 || _dom == 2 ? window.event.keyCode : event.which);
    var key = String.fromCharCode(keyCode);
    var isKey = (String('0123456789/').indexOf(key) != -1);
    var isAction = (String('0,8,13').indexOf(keyCode) != -1 || event.ctrlKey);
    var ss = getEditCaretPos(field);
    var se = getSelectionEnd(field);        
    var len = field.value.length;
    var previous = (ss > 0 ? field.value.charAt(ss-1) : null);
    var next = field.value.charAt(ss);
    if (!isKey && !isAction)
        return false;
    if (len == 10 && !isAction && ss == se)
        return false;
    if (key == '/') {
        return (len == p1 || len == p2 || ((ss == p1 || ss == p2) && next != '/' && previous != '/'));
    } else if (!isAction) {
        if (len == p1 || len == p2) {
            field.value += '/';
            return true;
        } else if ((ss == p1 || ss == p2) && !isAction) {
            if (next == '/' || len == 9)
                return false;
            field.value = field.value.substring(0, ss) + '/' + key + field.value.substring(ss);
            setEditCaretPos(field, ss+2);
            stopEvent(event);
            return false;
        } else {
            return true;
        }
    }
    return true;
}

//!------------------------------------------------------------------
// @function	chkDATE
// @desc		Verifica se a data informada no campo é válida, de acordo
//				com o formato de data ativo no sistema
// @param		field HTMLInputElement object Campo de formulário
// @param		format String Formato de data ativo
// @return		Boolean
//!------------------------------------------------------------------
function chkDATE(field, format) {
    var v, re, d, m, y;
    v = field.value;
    (format != null && (format == 'EURO' || format == 'US')) || (format = 'EURO');
    if (v.length > 0) {
        (format == 'EURO' ? re = /^\d{1,2}(\-|\/|\.)\d{1,2}\1\d{4}$/ :  re = /^\d{4}(\-|\/|\.)\d{1,2}\1\d{1,2}$/);
        if (!re.test(field.value))
            return false;
        d = (format == 'EURO' ? parseInt(v.substr(0, 2), 10) : parseInt(v.substr(8, 2), 10));
        m = (format == 'EURO' ? parseInt(v.substr(3, 2), 10) : parseInt(v.substr(5, 2), 10));
        y = (format == 'EURO' ? parseInt(v.substr(6, 4), 10) : parseInt(v.substr(0, 4), 10));
        binM = (1 << (m-1));
        m31 = 0xAD5;
        if ((y < 1000) || (m < 1) || (m > 12) || (d < 1) || (d > 31) ||
            ((d == 31 && ((binM & m31) == 0))) ||
            ((d == 30 && m == 2)) || ((d == 29 && m == 2 && !isLeap(y)))) {
            return false;
        }
    }
    return true;
}
    
//!----------------------------------------------------
// @function	isLeap
// @desc		Verifica se um ano é bissexto
// @param		year Integer	Ano a ser verificado
// @return		Boolean
//!----------------------------------------------------
function isLeap(year) {
	return (year % 4 == 0) && (year % 100 != 0 || year % 400 == 0);
}

//!----------------------------------------------------
// @function	dateToDays
// @desc		Converte uma data em número de dias
// @param		dateValue String	Valor da data
// @return		Integer Número de dias ou zero se a data não estiver no formato DD/MM/YYYY
//!----------------------------------------------------
function dateToDays(dateValue) {
	var day, month, year, century; 
	var reEuro = /^\d{1,2}(\-|\/|\.)\d{1,2}\1\d{4}$/;
	var reUS = /^\d{4}(\-|\/|\.)\d{1,2}\1\d{1,2}$/;
	if (reEuro.test(dateValue)) {
		day = parseInt(dateValue.substr(0, 2), 10);
		month = parseInt(dateValue.substr(3, 2), 10);
		year = parseInt(dateValue.substr(6, 4), 10);
	} else if (reUS.test(dateValue)) {
		day = parseInt(dateValue.substr(8, 2), 10);
		month = parseInt(dateValue.substr(5, 2), 10);
		year = parseInt(dateValue.substr(0, 4), 10);
	} else {
		return 0;
	}
	century = parseInt(String(year).substr(0, 2));
	year = String(year).substr(2, 2);
	if (month > 2) {
		month -= 3;
	} else {
		month += 9;
		if (year) {
			year--;
		} else {
			year = 99;
			century--;
		}
	}
	return (Math.floor((146097 * century) / 4) + Math.floor((1461 * year) / 4) + Math.floor((153 * month + 2) / 5) + day + 1721119);	
}

//!----------------------------------------------------
// @function	dateCalendarClose
// @desc		Tratador do evento close do calendário
// @param		cal Object	Instância do calendário
// @return		void
//!----------------------------------------------------
function dateCalendarClose(cal) {
	if (cal.params.inputField)
		cal.params.inputField.readOnly = false;
	cal.hide();
}