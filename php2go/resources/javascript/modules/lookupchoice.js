//
// +----------------------------------------------------------------------+
// | PHP2Go Web Development Framework                                     |
// +----------------------------------------------------------------------+
// | Copyright (c) Anand Raman [anand_raman@poboxes.com]                  |
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
// $Header: /www/cvsroot/php2go/resources/javascript/modules/lookupchoice.js,v 1.9 2005/08/30 14:41:31 mpont Exp $
// $Date: 2005/08/30 14:41:31 $
// $Revision: 1.9 $

//!-------------------------------------------------------
// @function	LookupChoice
// @desc		Construtor do objeto LookupChoice com base
//				no nome do formulário e no nome dos campos envolvidos
// @param		formName String	Nome do formulário
// @param		selName String	Nome do campo SELECT
// @param		editName String	Nome do campo EDIT
// @return		LookupChoice object
//!-------------------------------------------------------
function LookupChoice(formName, selName, editName) {
	this.formName = formName;
	this.selName = selName;
	this.editName = editName;
	this.selectString = '';
	this.selectArray = new Array();
	this.getElement = function(fieldName) {
		return eval("document." + this.formName + ".elements['" + fieldName + "']");
	};
	this.select = this.getElement(this.selName);
	this.edit = this.getElement(this.editName);		
	this.initializeList();
}

//!-------------------------------------------------------
// @function	initializeList
// @desc		Cria uma representação string dos valores inseridos
//				na lista, para facilitar a busca, ou recupera da string
//				criada para um vetor os valores iniciais da lista
// @return		void
//!-------------------------------------------------------
LookupChoice.prototype.initializeList = function() {
	if (this.selectString == '') {
		for(var i=0; i<this.select.options.length; i++) {
			this.selectArray[i] = this.select.options[i];
			this.selectString += this.select.options[i].value + ":" + this.select.options[i].text;
			if (i < (this.select.options.length-1)) 
				this.selectString = this.selectString + ",";
		}
	} else {
		var tempArray = this.selectString.split(',');
		for(var i=0;i<tempArray.length;i++) {
			var prop = tempArray[i].split(':');
			this.selectArray[i] = new Option(prop[1], prop[0]);
		}
	}
	return;
};

//!-------------------------------------------------------
// @function	rebuildList
// @desc		Reconstrói a lista a partir de seus valores iniciais
// @return		void
// @note		Este evento é disparado quando o campo de edição torna a ser vazio
//!-------------------------------------------------------
LookupChoice.prototype.rebuildList = function() {
	this.initializeList();
	for(var i=0; i<this.selectArray.length; i++) {
		this.select.options[i] = this.selectArray[i];
	}
	this.select.options.length = this.selectArray.length;
	return;
};

//!-------------------------------------------------------
// @function	updateList
// @desc		Função disparada a cada caractere digitado no campo texto
//				buscando filtrar e reconstruir a lista de opções
// @return		void
// @note		Os espaços em branco à esquerda no campo EDIT são retirados
//!-------------------------------------------------------
LookupChoice.prototype.updateList = function() {
	var j = 0;
	var pattern = null;
	var str = this.edit.value.replace('^\\s*','');
	if (str == '') {
		this.rebuildList();
		return;
	}
	this.initializeList();	
	pattern = new RegExp("^"+str, "i");
	for (var i=0; i<this.selectArray.length; i++) {
		if (pattern.test(this.selectArray[i].text)) {
			this.select.options[j++] = this.selectArray[i];
		}
	}
	this.select.options.length = j;
	if (j==1) {
		this.select.options[0].selected = true;
	}
};