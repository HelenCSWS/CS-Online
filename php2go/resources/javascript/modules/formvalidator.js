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
// $Header: /www/cvsroot/php2go/resources/javascript/modules/formvalidator.js,v 1.24 2005/08/30 14:41:31 mpont Exp $
// $Date: 2005/08/30 14:41:31 $
// $Revision: 1.24 $

FormValidator.MODE_ALERT = 1;
FormValidator.MODE_DHTML = 2;
FormValidator.LIST_FLOW = 1;
FormValidator.LIST_BULLET = 2;
FormValidator.FIELD_FIELD = 1;
FormValidator.FIELD_VALUE = 2;

//!---------------------------------------------
// @function	FormValidator
// @desc		Construtor do objeto de validação de formulários
// @param		frm String		Nome do formulário
// @return		void
//!---------------------------------------------
function FormValidator(frm) {
	this.frm = frm;
	this.req = new Array();
	this.chk = new Array();
	this.len = new Array();
	this.rule = new Array();
	this.ept = new Array();
	this.fail = new Array();
	this.editor = null;
	this.errmsg = '';
	this.errmode = FormValidator.MODE_ALERT;	
	this.errlm = FormValidator.LIST_FLOW;
	this.errnl = "\n";
	this.errls = "---------------------------------------------------------------------------------------\n";
	this.errdiv = null;
	this.errhdr = formFieldsInv;
}

//!---------------------------------------------
// @function	setErrorOptions
// @desc		Define o modo de visualização dos erros
//				de validação gerados por este objeto
// @param		mode Integer	Modo de exibição (1=alert, 2=dhtml)
// @param		div String		Nome do elemento onde os erros devem ser exibidos (somente se mode=2)
// @param		lm Integer		Tipo de lista de erros (1=flow, 2=bullet list)
// @param		hdr String		Texto para o cabeçalho da exibição de erros
// @return		void
//!---------------------------------------------
FormValidator.prototype.setErrorOptions = function (mode, div, lm, hdr) {
	if (mode == FormValidator.MODE_DHTML && div != null && div != "") {
		this.errmode = mode;
		this.errnl = "<br/>";
		this.errls = "";
		this.errdiv = div;
		if (lm != null && (lm == FormValidator.LIST_FLOW || lm == FormValidator.LIST_BULLET))
			this.errlm = lm;		
	} else {
		this.errmode = FormValidator.MODE_ALERT;
	}
	if (hdr != null)
		this.errhdr = hdr;
};

//!---------------------------------------------
// @function	addRequiredField
// @desc		Adiciona um campo obrigatório à validação
// @param		fname String	Nome do campo
// @param		flabel String	Rótulo do campo
// @return		void
//!---------------------------------------------
FormValidator.prototype.addRequiredField = function(fname, flabel) {
	this.req[this.req.length] = { name: fname, label: flabel, minsize: 0 };
};

//!---------------------------------------------
// @function	addLookupCheck
// @desc		Adiciona uma validação de tamanho mínimo de itens em um lookup de inserção de valores
// @param		fname String	Nome do campo
// @param		flabel String	Rótulo do campo
// @param		fminzie Integer	Tamanho mínimo
// @return		void	
//!---------------------------------------------
FormValidator.prototype.addLookupCheck = function(fname, flabel, fminsize) {
	this.req[this.req.length] = { name: fname, label: flabel, minsize: fminsize };
};

//!---------------------------------------------
// @function	addCheckField
// @desc		Adiciona uma checagem de máscara à validação
// @param		fname String	Nome do campo
// @param		fmask String	Nome da máscara
// @param		emsg String		Mensagem de erro
// @param		eargs String	Argumentos extra para a função de validação
// @return		void
//!---------------------------------------------
FormValidator.prototype.addCheckField = function(fname, fmask, emsg, eargs) {
	this.chk[this.chk.length] = { name: fname, mask: fmask, msg: emsg, args: (eargs != null ? ',' + eargs : ''), exp: "r = (isEmpty('%1', '%2') || chk%3(document.%4.elements['%5']%6));" };
};

//!---------------------------------------------
// @function	addLengthCheck
// @desc		Adiciona uma checagem de tamanho de campo à validação
// @param		fname String	Nome do campo
// @param		frule String	Regra (maxlength ou minlength)
// @param		flimit String	Valor limite
// @param		emsg String		Mensagem de erro
// @return		void
//!---------------------------------------------
FormValidator.prototype.addLengthCheck = function(fname, frule, flimit, emsg) {
	this.len[this.len.length] = { name: fname, rule: frule, op: (frule == "maxlength" ? "<=" : ">="), limit: flimit, exp: "r = (isEmpty('%1', '%2') || document.%3.elements['%4'].value.length %5 %6)", msg: emsg };
};

//!---------------------------------------------
// @function	addRule
// @desc		Adiciona uma regra extra de validação para um campo do formulário,
//				como comparação de igualdade/desigualdade com outro campo ou
//				obrigatoriedade condicional
// @param		srcfld String	Nome do campo de origem
// @param		rtype String	Tipo da regra
// @param		datatype String	Define a máscara dos campos que serão comparados
// @param		trgfld String	Campo alvo para comparação
// @param		trgval String	"null" Usado para obrigatoriedade condicional por valor de outro campo
// @param		emsg String		"null" Permite definir uma mensagem de erro customizada em caso de falha da regra
// @return		void
//!---------------------------------------------
FormValidator.prototype.addRule = function(srcfld, rtype, datatype, trgfld, trgval, emsg) {
	var sl,tl;
	sl = getFormFieldAttribute(this.frm, srcfld, 'title', srcfld);
	tl = (trgfld != null ? getFormFieldAttribute(this.frm, trgfld, 'title', trgfld) : null);
	this.rule[this.rule.length] = { source: srcfld, sourcelbl: sl, type: rtype, datatype: datatype, comptype: (trgval != null ? FormValidator.FIELD_VALUE : FormValidator.FIELD_FIELD), target: (trgval != null ? trgval : trgfld), targetfld: trgfld, targetlbl: tl, msg: emsg };
};

//!---------------------------------------------
// @function	isValid
// @desc		Executa as validações adicionadas no formulário
// @return		Boolean
//!---------------------------------------------
FormValidator.prototype.isValid = function() {
	var i, o, v, r, f, fe, fw;
	if (this.errhdr != '' && this.errmode == FormValidator.MODE_ALERT)
		this.errhdr += this.errnl;
	// atualização do valor do editor avançado
	this.updateEditorValue();
	// campos obrigatórios
	for (o in this.req) {
		o = this.req[o];
		if (o.minsize != null && o.minsize > 0) {
			f = getDocumentObject(o.name);
			if (f != null && f.options.length < o.minsize) {
				this.ept[this.ept.length] = (this.errmode == FormValidator.MODE_ALERT ? o.label : stringReplace(formFieldReq, o.label));
				if (!fe) fe = o.name;
			}
		} else if (isEmpty(this.frm, o.name)) {
			this.ept[this.ept.length] = (this.errmode == FormValidator.MODE_ALERT ? o.label : stringReplace(formFieldReq, o.label));
			if (!fe)  fe = o.name;
		}
	}
	// checagem de tamanho de campo
	for (o in this.len) {
		o = this.len[o];
		eval(stringReplace(o.exp, this.frm, o.name, this.frm, o.name, o.op, o.limit));
		if (!r) {
			this.fail[this.fail.length] = o.msg;
			if (!fw) fw = o.name;
		}
	}
	// checagem de máscara de campo
	for (o in this.chk) {
		o = this.chk[o];
		eval(stringReplace(o.exp, this.frm, o.name, o.mask, this.frm, o.name, o.args));
		if (!r) {
			this.fail[this.fail.length] = o.msg;
			if (!fw) fw = o.name;
		}
	}
	// validação de regras
	for (o in this.rule) {		
		o = this.rule[o];
		// regras de comparação com o valor de outros campos ou com valores estáticos
		if (/^(EQ|NEQ|GT|LT|GOET|LOET)$/.test(o.type)) {
			if (!this.compareValues(o.source, o.target, o.type, o.datatype, o.comptype)) {
				this.fail[this.fail.length] = (o.msg != null ? o.msg : this.getErrorMessage(o.type, o.sourcelbl, (o.comptype == FormValidator.FIELD_VALUE ? o.target : o.targetlbl), o.comptype));
				if (!fw) fw = o.source;
			}
		// regras de validação contra um padrão de valor
		} else if (o.type == 'REGEX' && !isEmpty(this.frm, o.source)) {
			eval("r = " + o.target + ".test(\"" + getFormFieldValue(this.frm, o.source) + "\");");
			if (!r) {
				this.fail[this.fail.length] = (o.msg != null ? o.msg : stringReplace(formFieldsRegex, o.sourcelbl));
				if (!fw) fw = o.source;
			}
		// obrigatoriedade condicional se outro campo for não vazio
		} else if (o.type == 'REQIF' && isEmpty(this.frm, o.source) && !isEmpty(this.frm, o.target)) {
			if (o.msg != null) {
				if (this.errmode == FormValidator.MODE_ALERT) {
					this.fail[this.fail.length] = o.msg;
					if (!fw) fw = o.source;
				} else {
					this.ept[this.ept.length] = o.msg;
					if (!fe) fe = o.source;
				}
			} else {			
				this.ept[this.ept.length] = (this.errmode == FormValidator.MODE_ALERT ? o.sourcelbl : stringReplace(formFieldReq, o.sourcelbl));
				if (!fe) fe = o.source;
			}
		// obrigatoriedade condicional com comparação com outro campo ou com valor estático
		} else if (/^REQIF(EQ|NEQ|GT|LT|GOET|LOET)$/.test(o.type)) {
			if (isEmpty(this.frm, o.source) && !isEmpty(this.frm, o.targetfld) && this.compareValues(o.targetfld, o.target, o.type.replace('REQIF', ''), o.datatype, o.comptype)) {
				if (o.msg != null) {
					if (this.errmode == FormValidator.MODE_ALERT) {
						this.fail[this.fail.length] = o.msg;
						if (!fw) fw = o.source;
					} else {
						this.ept[this.ept.length] = o.msg;
						if (!fe) fe = o.source;
					}
				} else {
					this.ept[this.ept.length] = (this.errmode == FormValidator.MODE_ALERT ? o.sourcelbl : stringReplace(formFieldReq, o.sourcelbl));
					if (!fe) fe = o.source;
				}
			}
		}
	}
	// existem campos obrigatórios vazios?
	if (this.ept.length > 0) {
		if (this.errmode == FormValidator.MODE_ALERT) {
			this.errmsg = formFieldsReq + this.errnl + this.errls;
			for (i=0; i<this.ept.length; i++)
				this.errmsg += this.ept[i] + this.errnl;
			this.errmsg += this.errls + formComplFields;
		} else {
			this.errmsg = this.errhdr + this.errls;
			if (this.errlm == FormValidator.LIST_BULLET)
				this.errmsg += '<ul>';
			for (i=0; i<this.ept.length; i++)
				this.errmsg += (this.errlm == FormValidator.LIST_BULLET ? '<li>' + this.ept[i] + '</li>' : this.ept[i] + this.errnl);
			if (this.errlm == FormValidator.LIST_BULLET)
				this.errmsg += '</ul>';				
			this.errmsg += this.errls; 
		}
		this.showErrors();
		if (fe != null)
			requestFocus(this.frm, fe);
		return false;
	// existe campos inválidos?
	} else if (this.fail.length > 0) {		
		if (this.errmode == FormValidator.MODE_ALERT) {
			this.errmsg = this.errhdr.replace(/(<([^>]+)>)/ig, '') + this.errls;
			for (i=0; i<this.fail.length; i++)
				this.errmsg += this.fail[i] + this.errnl;
			this.errmsg += this.errls + formFixFields;
		} else {
			this.errmsg = this.errhdr + this.errls;
			if (this.errlm == FormValidator.LIST_BULLET)
				this.errmsg += '<ul>';
			for (i=0; i<this.fail.length; i++)
				this.errmsg += (this.errlm == FormValidator.LIST_BULLET ? '<li>' + this.fail[i] + '</li>' : this.fail[i] + this.errnl);
			if (this.errlm == FormValidator.LIST_BULLET)
				this.errmsg += '</ul>';
			this.errmsg += this.errls;
		}
		this.showErrors();
		if (fw != null)
			requestFocus(this.frm, fw);
		return false;
	// validação ok
	} else {
		// atualização dos campos escondidos associados a checkboxes desabilitados
		this.updateDisabledCheckboxes();
		// esconde a layer de erros, se ela estiver visível
		this.clearErrors();
		return true;
	}	
};

//!---------------------------------------------
// @function	showErrors
// @desc		Método para exibição dos erros resultantes
// 				da validação do formulário
// @return		void
//!---------------------------------------------
FormValidator.prototype.showErrors = function() {
	if (this.errmode == 1) {
		alert(this.errmsg); 
	} else {
		var d = document.getElementById(this.errdiv);
		writeToDiv(d, true, true, this.errmsg);
		if (d.style.display == "none")
			d.style.display = "block";
		var pos = getAbsolutePos(d);
		window.scrollTo(0, pos.y);
	}
};

//!---------------------------------------------
// @function	clearErrors
// @desc		Esconde a seção de erros de validação previamente exibida
// @return		void
//!---------------------------------------------
FormValidator.prototype.clearErrors = function() {
	if (this.errmode == 2) {
		var d = document.getElementById(this.errdiv);
		writeToDiv(d, true, true, "");
		if (d.style.display == "block")
			d.style.display = "none";
	}
};

//!-----------------------------------------------------------
// @function	compareValues
// @desc		Compara dois campos utilizando um determinado critério
// @param		src String		Nome do primeiro campo
// @param		trg String		Nome do segundo campo
// @param		op String		Operador de comparação
// @param		datatype String	Tipo de dados dos valores
// @param		comptype Integer	Tipo de comparação: campo/campo ou campo/valor	
// @return		Boolean
//!-----------------------------------------------------------
FormValidator.prototype.compareValues = function(src, trg, op, datatype, comptype) {
	var left, right, srcval, trgval, r;
	srcval = getFormFieldValue(this.frm, src);
	if (comptype == FormValidator.FIELD_FIELD) {
		trgval = getFormFieldValue(this.frm, trg);
		if (trim(String(srcval)) == "" || trim(String(trgval)) == "")
			return true;
	} else if (comptype == FormValidator.FIELD_VALUE) {
		trgval = trg;
		if (trim(String(srcval)) == "")
			return true;
	}
	// comparação entre inteiros
	if (datatype == "INTEGER") {
		left = "parseInt(srcval)";
		right = "parseInt(trgval)";
	// comparação entre números decimais
	} else if (datatype == "FLOAT") {
		left = "parseFloat(srcval)";
		right = "parseFloat(trgval)";
	// comparação entre datas
	} else if (datatype == "DATE") {
		left = "dateToDays(srcval)";
		right = "dateToDays(trgval)";
	// comparação entre strings
	} else {
		left = "srcval";
		right = "trgval";
	}
	switch (op) {
		case 'EQ' : op = ' == '; break;
		case 'NEQ' : op = ' != '; break;
		case 'GT' : op = ' > '; break;
		case 'LT' : op = ' < '; break;
		case 'GOET' : op = ' >= '; break;
		case 'LOET' : op = ' <= '; break;
		default   : op = ' == ';
	}
	eval("r = (" + left + op + right + ");");
	return r;	
};

//!-----------------------------------------------------------
// @function	getErrorMessage
// @desc		Retorna a mensagem de erro para a falha em uma regra de campo,
// @param		op String		Operador da comparação
// @param		source String	Rótulo do campo a ser validado
// @param		target String	Rótulo do campo ou valor com o qual foi feita a comparação
// @param		comptype String	"null" Tipo da comparação
// @return		String Valor da mensagem de erro, de acordo com o tipo e com o alvo da comparação
// @note		A comparação com um valor tem precedência sobre a comparação com outro campo
//!-----------------------------------------------------------
FormValidator.prototype.getErrorMessage = function(op, source, target, comptype) {
	switch (op) {
		case 'EQ' : 
			return (comptype == FormValidator.FIELD_VALUE ? stringReplace(formFieldValueEq, source, target) : stringReplace(formFieldsEq, source, target));
		case 'NEQ' :
			return (comptype == FormValidator.FIELD_VALUE ? stringReplace(formFieldValueNeq, source, target) : stringReplace(formFieldsNeq, source, target));
		case 'GT' :
			return (comptype == FormValidator.FIELD_VALUE ? stringReplace(formFieldValueGt, source, target) : stringReplace(formFieldsGt, source, target));
		case 'LT' :
			return (comptype == FormValidator.FIELD_VALUE ? stringReplace(formFieldValueLt, source, target) : stringReplace(formFieldsLt, source, target));
		case 'GOET' :
			return (comptype == FormValidator.FIELD_VALUE ? stringReplace(formFieldValueGoet, source, target) : stringReplace(formFieldsGoet, source, target));
		case 'LOET' :
			return (comptype == FormValidator.FIELD_VALUE ? stringReplace(formFieldValueLoet, source, target) : stringReplace(formFieldsLoet, source, target));
		default :
			return "";
	}	
};

//!-----------------------------------------------------------
// @function	updateEditorValue
// @desc		Atualiza o valor do campo associado ao editor HTML,
//				se este estiver incluído neste formulário
// @return		void
//!-----------------------------------------------------------
FormValidator.prototype.updateEditorValue = function() {
	if (this.editor != null)
		eval("document." + this.frm + ".elements['" + this.editor + "'].value = (" + this.editor + "_instance.isEmpty() ? '' : " + this.editor + "_instance.getHtml());");	
};

//!-----------------------------------------------------------
// @function	updateDisabledCheckboxes
// @desc		Atualiza o valor dos campos escondidos que são enviados juntamente
//				com campos do tipo checkbox, para que eles tenham um valor vazio nos
//				casos em que o checkbox é desabilitado
// @return		void
//!-----------------------------------------------------------
FormValidator.prototype.updateDisabledCheckboxes = function() {
	var f, h, e = null;
	f = getFormObj(this.frm);
	for (var i=0,s=f.elements.length; i<s; i++) {
		e = f.elements[i];
		if (e.type == 'checkbox' && e.disabled) {
			h = document.getElementById('V_' + e.name);
			if (h != null)
				h.disabled = true;
		}			
	}
};