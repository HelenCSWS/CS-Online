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
// $Header: /www/cvsroot/php2go/resources/javascript/modules/editselection.js,v 1.12 2005/08/30 14:41:31 mpont Exp $
// $Date: 2005/08/30 14:41:31 $
// $Revision: 1.12 $

//!-------------------------------------------------------
// @function	EditSelection
// @desc		Construtor do objeto EditSelection, partindo
//				dos nomes do formulário e dos campos envolvidos
// @param		formName String		Nome do formulário
// @param		srcField String		Nome do campo edit de origem
// @param		trgField String		Nome da listbox de destino
// @param		addedfield String		Nome do campo que armazena os adicionados
// @param		removedfield String	Nome do campo que armazena os removidos
// @param		countfield String		Nome do campo que armazena a contagem de itens inseridos
// @return		EditSelection object
//!-------------------------------------------------------
function EditSelection(formName, srcField, trgField, addedfield, removedfield, countfield) {
	this.formName = formName;
	this.sourceFieldName = srcField;
	this.targetFieldName = trgField;
	this.addedFieldName = addedfield;
	this.removedFieldName = removedfield;
	this.countFieldName = countfield;
	this.source = null;
	this.target = null;	
	this.added = null;
	this.addedStr = "";
	this.removed = null;
	this.removedStr = "";
	this.hiddenFieldsReload = false;	
	this.count = null;
	this.pre = "";
	this.preCount = 0;
	this.getElement = function(fieldName) {
		return eval("document." + this.formName + ".elements['" + fieldName + "']");	
	};
	this.onReset = function() {
		this.addedStr = this.added.value;
		this.removedStr = this.removed.value;
	};
	this.inString = function(needle, haystack) {
		haystack = '#' + haystack + '#';
		return (haystack.indexOf('#'+needle+'#'));
	}	
	this.initialize();
}

//!-------------------------------------------------------
// @function	initialize
// @desc		Função de inicialização do componente de seleção
// @return		void
//!-------------------------------------------------------
EditSelection.prototype.initialize = function() {
	this.source = this.getElement(this.sourceFieldName);
	this.target = this.getElement(this.targetFieldName);
	this.count = document.getElementById(this.countFieldName);
	this.added = this.getElement(this.addedFieldName);
	this.removed = this.getElement(this.removedFieldName);
	this.count.innerHTML = this.target.options.length - 1;			
	for (var i=1; i<this.target.options.length; i++) {
		if (this.pre.length > 0) {
			this.pre = this.pre + '#' + this.target.options[i].value;
		} else {
			this.pre = this.target.options[i].value;
		}
		this.preCount++;
	}
};

//!------------------------------------------------------
// @function	add
// @desc		Copia o elemento fornecido no editfield
//				de origem para a listbox de destino
// @return		Boolean
//!------------------------------------------------------
EditSelection.prototype.add = function() {
	if (!this.hiddenFieldsReload) {
		this.added.value = "";
		this.removed.value = "";
		this.hiddenFieldsReload = true;
	}
	if (this.added.value == "" && this.addedStr != "") {
		this.added.value = this.addedStr;
		this.addedStr = "";
	}
	if (!this.source || !this.target) {
		return false;
	} else {
		if (!isEmpty(this.formName, this.sourceFieldName)) {
			if ( ( this.inString(this.source.value, this.added.value) == -1 ) && ( this.inString(this.source.value, this.pre) == -1 || this.inString(this.source.value, this.removed.value) != -1 ) ) {
				this.target.options[this.target.options.length] = new Option(this.source.value, this.source.value);
				this.addMark(this.source.value);
				this.count.innerHTML = this.target.length - 1;
			} else {
				alert(insValueMsg);
				this.source.focus();
			}
		}
		return true;
	}
};

//!----------------------------------------------------------------
// @function	remove
// @desc		Remove os elementos selecionados da listbox destino
// @return		Boolean
//!----------------------------------------------------------------
EditSelection.prototype.remove = function() {
	if (!this.hiddenFieldsReload) {
		this.added.value = "";
		this.removed.value = "";
		this.hiddenFieldsReload = true;
	}
	if (this.removed.value == "" && this.removedStr != "") {
		this.removed.value = this.removedStr;
		this.removedStr = "";
	}
	var del = 0;
	if (!this.target) {
		return false;
	} else {
		var cont = 0;
		for (var z=0; z<this.target.length;z++) {
			if ((this.target.options[z].selected == true) && (z != 0)) {
				if (this.target.length > 1) {
					var i = z;
					var j = z;
					while ((i<=this.target.length-2)&&(this.target.options[i].selected == true)) {
						this.removeMark(this.target.options[i].value);
						del++;
						cont++;
						i++;
					}
					if (cont > 0) {
						for (i=j;i<this.target.length-cont;i++) {
							this.target.options[i].value = this.target.options[i+cont].value;
							this.target.options[i].text = this.target.options[i+cont].text;
							this.target.options[i].selected = this.target.options[i+cont].selected;
						}
						this.target.length = this.target.length - cont;
					}
					cont = 0;
				}
			}
		}
		if ( this.target.length > 1 && this.target.options[this.target.length-1].selected == true )
		{
			del++;
			this.removeMark(this.target.options[this.target.length-1].value);
			this.target.options[this.target.length-1].value = -1;
			this.target.options[this.target.length-1].text = "                          ";
			if ((this.target.length) > 1) {
				this.target.length--;
			}
		}
		this.count.innerHTML = this.target.length - 1;
	}
};

//!-----------------------------------------------------------
// @function	removeAll
// @desc		Remove todos os elementos da listbox destino
// @return		Boolean
//!-----------------------------------------------------------
EditSelection.prototype.removeAll = function() {
	if (!this.hiddenFieldsReload) {
		this.added.value = "";
		this.removed.value = "";
		this.hiddenFieldsReload = true;
	}
	if (this.removed.value == "" && this.removedStr != "") {
		this.removed.value = this.removedStr;
		this.removedStr = "";
	}
	var del = 0;
	if (!this.target) {
		return false;
	} else {
		if (this.target.options.length > 100) {
			alert(selRemAllMsg);
		}
		for (var i=1; i<this.target.options.length; i++) {
			this.removeMark(this.target.options[i].value);
			del++;
		}
		this.target.options.length = 1;
		this.count.innerHTML = this.target.length - 1;
		return true;
	}
};

//!---------------------------------------------------------------
// @function	addMark
// @desc		Registra a inserção de um elemento se necessário
// @param		optValue String	Valor inserido
// @return		void
//!---------------------------------------------------------------
EditSelection.prototype.addMark = function(optValue) {
	var pos = this.inString(optValue, this.removed.value);
	if ( pos != -1 ) {
		this.removed.value = this.removed.value.substr(0, pos-1) + this.removed.value.substr(pos + optValue.length + 1);
	} else {
		if ( this.added.value.length > 0 ) {
			this.added.value = this.added.value + '#' + optValue;
		} else {
			this.added.value = optValue;
		}
	}
};

//!--------------------------------------------------------------
// @function	removeMark
// @desc		registra a remoção de um elemento se necessário
// @param		optValue String	Valor removido
// @return		void
//!--------------------------------------------------------------
EditSelection.prototype.removeMark = function(optValue) {
	var in_add = this.inString(optValue, this.added.value);
	var in_pre = this.inString(optValue, this.pre);
	if (in_add != -1 && in_pre == -1) {
		this.added.value = this.added.value.substr(0, in_add-1) + this.added.value.substr(in_add + optValue.length + 1);
	} else {
		if (this.removed.value.length > 0) {
			this.removed.value = this.removed.value + "#" + optValue;
		} else {
			this.removed.value = optValue;
		}
	}
};