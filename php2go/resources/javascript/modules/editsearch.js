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
// $Header: /www/cvsroot/php2go/resources/javascript/modules/editsearch.js,v 1.1 2005/09/01 13:53:10 mpont Exp $
// $Date: 2005/09/01 13:53:10 $
// $Revision: 1.1 $

//!--------------------------------------------------------------
// @function	EditSearch
// @desc		Construtor do objeto EditSearch, utilizando na classe EditSearchField,
//				que implementa um mecanismo de busca para formulários
// @param		url String			URL a ser utilizada na pesquisa
// @param		frmName String		Nome do formulário
// @param		fldName String		Nome do campo que contém o termo de pesquisa
// @param		trgFld String		Nome do campo que será populado com os resultados da pesquisa
// @param		trgIdx String		Índice inicial de população do campo (0 ou 1)
// @param		debug Boolean		Debug habilitado ou não para a requisição JSRS
// @param		autoTrim Boolean	Eliminar caracteres brancos no início e no fim do termo de pesquisa ao enviar a consulta
// @param		dateFormat String	Formato de data ativo
// @return		void
//!--------------------------------------------------------------
function EditSearch(url, frmName, fldName, trgFld, trgIdx, debug, autoTrim, dateFormat) {
	this.url = url;
	this.frm = getFormObj(frmName);
	this.fld = fldName;
	this.trg = trgFld;
	this.idx = (isNaN(trgIdx) ? 0 : parseInt(trgIdx, 10));	
	this.debug = (debug == true ? true : false);
	this.autoTrim = (autoTrim == true ? true : false);
	this.dateFormat = dateFormat;
	this.masks = this.frm.elements[this.fld + '_masks'].value.split(',');
	this.setupEvents();
}

//!--------------------------------------------------------------
// @function    setupEvents
// @desc        Inicializa os eventos no campo de seleção de tipo de filtro
//              e no campo de digitação do termo de pesquisa, para controle de máscara
// @return      void
//!--------------------------------------------------------------
EditSearch.prototype.setupEvents = function() {
	var s,f,e;
	s = this;
	f = this.frm.elements[this.fld + '_filters'];    
	e = this.frm.elements[this.fld];
	addEvent(f, 'change', function(evt) {    
		e.value = '';
		e.focus();
	});
	addEvent(e, 'keypress', function(evt) {
		var keyCode = (_dom == 1 || _dom == 2 ? window.event.keyCode : evt.which);
		if (keyCode == 13) {
			var b = getDocumentObject(s.fld + '_button');
			if (b != null && typeof(b.click) == 'function') {
				b.click();
				stopEvent(evt);
			}
		} else {
			return s.validateInput(e, evt, f.selectedIndex);
		}
	});
};

//!--------------------------------------------------------------
// @function    submit
// @desc        Submete a pesquisa
// @param       btn Object  Botão que dispara o evento
// @return      void
//!--------------------------------------------------------------
EditSearch.prototype.submit = function(btn) {
	var btnVal, sOpt, sArgs;
	if (!isEmpty(this.frm, this.fld)) {
		if (this.autoTrim)
			this.frm.elements[this.fld].value = trim(this.frm.elements[this.fld].value);
		if (!this.validateSubmit()) {
			alert(p2gInvalidVal);
			try {
				this.frm.elements[this.fld].select();
			} catch (e) { }
			return;
		}
		search = this;
		btn.disabled = true;
		btnVal = btn.value;
		btn.value = srchBtnVal;
		args = Array(getFormFieldValue(this.frm, this.fld + '_filters'), getFormFieldValue(this.frm, this.fld));
		function processResponse(response) {
			btn.value = btnVal;
			btn.disabled = false;
			if (response != "") {
				search.frm.elements[search.trg].options.length = search.idx;
				createOptionsFromString(response, search.frm, search.trg, '|', '~', search.idx);
				search.frm.elements[search.trg].focus();
			} else {
				alert(srchEmptyFilt);
			}
		}
		jsrsExecute(this.url, processResponse, 'performSearch', args, this.debug);
	} else {
		alert(srchErrFilt);
		this.frm.elements[this.fld].focus();
	}
};

//!--------------------------------------------------------------
// @function	validateInput
// @desc		Valida a inserção de um caractere no campo do termo de pesquisa,
//				utilizando a máscara do filtro ativo
// @param		fld Object			Campo do termo de pesquisa
// @param		evt Event object	Evento onKeyPress
// @param		filterIdx int		ID do filtro ativo
// @return		Boolean
//!--------------------------------------------------------------
EditSearch.prototype.validateInput = function(fld, evt, filterIdx) {
	var mask = this.masks[filterIdx];
	var res = true;
	// máscara de código postal, possui delimitadores
	var rz = new RegExp("ZIP\-?([1-9])\:?([1-9])");
	var mz = rz.exec(mask);
	if (mz) {
		res = chkMaskZIP(fld, evt, mz[1], mz[2]);
		(res == false) && (stopEvent(evt));
		return res;
	}
	// máscara de número decimal (com ou sem delimitadores)
	var rf = new RegExp("FLOAT(\-([1-9][0-9]*)\:([1-9][0-9]*))?");
	var mf = rf.exec(mask);
	if (mf) {
		res = (mf[2] && mf[3] ? chkMaskFLOAT(fld, evt, mf[2], mf[3]) : chkMaskFLOAT(fld, evt));
		(res == false) && (stopEvent(evt));
	return res;                
	}
	// outras máscaras
	switch (mask) {
		case "INTEGER" : res = chkMaskINTEGER(fld, evt); break;
		case "DATE"    : res = chkMaskDATE(fld, evt, this.dateFormat); break;
		case "TIME"    : res = chkMaskTIME(fld, evt); break;
		case "EMAIL"   : res = chkMaskEMAIL(fld, evt); break;
		case "URL"     : res = chkMaskURL(fld, evt); break;
		case "CURRENCY": res = chkMaskCURRENCY(fld, evt); break;
		case "LOGIN"   : res = chkMaskLOGIN(fld, evt); break;
		default        : res = true;    
	}
	(res == false) && (stopEvent(evt));
	return res;
};

//!--------------------------------------------------------------
// @function	validateSubmit
// @desc		Valida o termo de pesquisa de acordo com a máscara do filtro escolhido
// @return		Boolean
//!--------------------------------------------------------------
EditSearch.prototype.validateSubmit = function() {
	var fld = this.frm.elements[this.fld];
	var filterIdx = this.frm.elements[this.fld + '_filters'].selectedIndex;
	var mask = this.masks[filterIdx];
	var res = true;
	// máscara de código postal, possui delimitadores
	var rz = new RegExp("ZIP\-?([1-9])\:?([1-9])");
	var mz = rz.exec(mask);
	if (mz)
		return chkZIP(fld, mz[1], mz[2]);
	// máscara de número decimal (com ou sem delimitadores)
	var rf = new RegExp("FLOAT(\-([1-9][0-9]*)\:([1-9][0-9]*))?");
	var mf = rf.exec(mask);
	if (mf)
		return (mf[2] && mf[3] ? chkFLOAT(fld, mf[2], mf[3]) : chkFLOAT(fld));
	// outras máscaras
	switch (mask) {
		case "INTEGER" : return chkINTEGER(fld); break;
		case "DATE"    : return chkDATE(fld, this.dateFormat); break;
		case "TIME"    : return chkTIME(fld); break;
		case "EMAIL"   : return chkEMAIL(fld); break;
		case "URL"     : return chkURL(fld); break;
		case "CURRENCY": return chkCURRENCY(fld); break;
		case "LOGIN"   : return chkLOGIN(fld); break;
		case "STRING"  : return true;
		default        : return false;    
	}
};