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
// $Header: /www/cvsroot/php2go/resources/javascript/libs/form.js,v 1.32 2005/05/19 22:35:37 mpont Exp $
// $Date: 2005/05/19 22:35:37 $
// $Revision: 1.32 $

//!--------------------------------------------------
// @function	getFormObj
// @desc		Retorna um formulário do documento a partir de seu nome
// @param		frm String		Nome do formulário
// @return		Form object O formulário encontrado ou null se o formulário formName não estiver definido
//!--------------------------------------------------
function getFormObj(frm) {
	var f = eval("document." + frm);
	return (typeof(f) != 'undefined' ? f : null);
}

//!--------------------------------------------------
// @function	getFormFieldObj
// @desc		Retorna um objeto a partir do nome
//				do formulário e do nome do campo
// @param		frm String		Nome do formulário
// @param		fld String		Nome do campo
// @return		Field object Elemento encontrado
//!--------------------------------------------------
function getFormFieldObj(frm, fld) {	
	var o, f;
	if (typeof(frm) == 'string')
		f = getFormObj(frm);
	else if (typeof(frm) == 'object')
		f = frm;
	else
		return null;
	if (f == null || !f.elements) {
		return null;
	} else {
		o = f.elements[fld];
		return (typeof(o) != 'undefined') ? o : null;
	}	
}

//!--------------------------------------------------
// @function	getFormFieldAttribute
// @desc		Busca o valor de um atributo de um campo de formulário
// @param		frm String		Nome do formulário
// @param		fld String		Nome do campo
// @param		attr String		Nome do atributo
// @param		fallback String	"null" Valor de retorno em caso de erro
// @return		String Valor do atributo ou valor do parâmetro fallback em caso de erros
//!--------------------------------------------------
function getFormFieldAttribute(frm, fld, attr, fallback) {	
	var o = getFormFieldObj(frm, fld);
	if (o == null) {
		return fallback;
	} else {
		if (typeof(o.type) == 'undefined' && o.length) {
			if (typeof(o[0]) != 'undefined')
				return getElementAttribute(o[0], attr);
			return fallback;
		} else {
			return getElementAttribute(o, attr);
		}
	}
}

//!--------------------------------------------------
// @function	getFormFieldValue
// @desc		Busca o valor atual de um determinado campo de um formulário
// @param		frm String		Nome do formulário
// @param		fld String		Nome do campo
// @return		mixed Valor atual do campo
//!--------------------------------------------------
function getFormFieldValue(frm, fld) {
	var o = getFormFieldObj(frm, fld);
	if (o == null) {
		return null;
	} else {
		if (typeof(o.type) == 'undefined' && o.length) {
			if (typeof(o[0]) != 'undefined') {
				if (o[0].type == "radio")
					return getSelectedRadioOption(frm, fld);
				else if (o[0].type == "checkbox")
					return getCheckboxValue(frm, fld);
			}
			return null;
		} else {
			switch(o.type) {
				case "text" :
				case "hidden" :
				case "password" :
				case "file" :
				case "textarea" : 
					return o.value; 
					break;
				case "select-one" :
				case "select-multiple" : 
					return o.options[o.selectedIndex].value;
					break;
				case "radio" : 
					return getSelectedRadioOption(frm, fld);
					break;
				case "checkbox" : 
					return getCheckboxValue(frm, fld);
					break;
			}
			return null;
		}
	}	
}

//!--------------------------------------------------
// @function	getEditCaretPos
// @desc		Retorna a posição atual do cursor dentro de um campo TEXT
// @param		edit FormField object	Campo TEXT de um formulário
// @return		Integer Posição atual do cursor  (-1 se não suportado pelo browser)
//!--------------------------------------------------
function getEditCaretPos(edit) {
	var r,t,p;
	if (typeof(edit.value) == 'undefined') return -1;
	if (typeof(edit.selectionStart) != 'undefined') {
		return edit.selectionStart;
	} else if (typeof(document.selection) != 'undefined') {
		r = document.selection.createRange();
		t = edit.createTextRange();
		t.setEndPoint('EndToStart', r);
		return t.text.length;
	} else {
		return -1;
	}
}

//!--------------------------------------------------
// @function	getSelectionEnd
// @desc		Busca a posição do final da seleção dentro de um campo EDIT
// @param		edit FormField object	Campo TEXT de um formulário
// @return		Integer Posição final da seleção (-1 se não suportado pelo browser)
//!--------------------------------------------------
function getSelectionEnd(edit) {
	var r;
	if (typeof(edit.selectionEnd) != 'undefined') {
		return edit.selectionEnd;
	} else if (typeof(document.selection) != 'undefined') {
		r = document.selection.createRange().duplicate();
		r.moveStart("character", -edit.value.length);
		return r.text.length;
	} else {
		return -1;
	}
}

//!--------------------------------------------------
// @function	setEditCaretPos
// @desc		Move o cursor dentro de um campo EDIT para uma determinada posição
// @param		edit FormField object	Campo TEXT de um formulário
// @param		pos int					Nova posição para o cursor
// @return		Boolean
//!--------------------------------------------------
function setEditCaretPos(edit, pos) {
	var t;
	if (typeof(edit.value) == 'undefined' || isNaN(pos) || pos >= edit.value.length) return false;
	if (typeof(edit.selectionStart) != 'undefined') {
		edit.selectionStart = edit.selectionEnd = pos;
		return true;
	} else if (typeof(edit.createTextRange) != 'undefined') {
		t = edit.createTextRange();
		t.move('character', pos);
		t.select();
		return true;
	}
	return false;	
}


//!--------------------------------------------------
// @function	getSelectedRadioOption
// @desc		Retorna a opção selecionada em um grupo de botões RADIO
// @param		frm String		Nome do formulário
// @param		fld String		Nome do campo
// @return		mixed Valor da opção selecionada ou null se nenhuma está selecionada
//!--------------------------------------------------
function getSelectedRadioOption(frm, fld) {
	var f = getFormObj(frm);
	if (f != null) {
		for (var i=0; i<f.elements.length; i++) {
			if (f.elements[i].type == "radio" && f.elements[i].name == fld && f.elements[i].checked == true) {
				return f.elements[i].value;
			}
		}
	}
	return null;
}

//!-----------------------------------------------------------
// @function	getCheckboxValue
// @desc		Busca o valor de um campo checkbox simples ou múltiplo
// @param		frm String		Nome do formulário
// @param		fld String		Nome do campo
// @return		mixed Para checkbox simples, true ou false; 
//				para checkbox múltiplos, o conjunto de valores marcados
//!-----------------------------------------------------------
function getCheckboxValue(frm, fld) {	
	var f = getFormObj(frm);
	if (f != null) {
		if (fld.indexOf("[]") == -1) {
			return (f.elements[fld].checked);
		} else {
			var v = new Array();
			for (var i=0; i<f.elements.length; i++) {
				if (f.elements[i].type == "checkbox" && f.elements[i].name == fld && f.elements[i].checked == true)
					v[v.length] = f.elements[i].value;
			}
			return (v.length > 0 ? v : null);
		}
	}
}

//!-----------------------------------------------------------
// @function	setDisableField
// @desc		Habilita ou desabilita um campo de formulário a partir do nome
//				do formulário e do nome do campo
// @param		frm String		Nome do formulário
// @param		fld String		Nome do campo
// @param		setting Boolean	Valor para o atributo disabled do campo
// @return		void
//!-----------------------------------------------------------
function setDisableField(frm, fld, setting) {
	var o;
	if (setting == null) setting = true;
	o = getFormFieldObj(frm, fld);
	if (o != null) {
		try {
			if (typeof(o.type) == "undefined" && o.length) {
				for (var i=0; i<o.length; i++) {
					go = document.getElementById(fld.replace("[]", "") + "_" + i);
					if (go != null)
						go.disabled = setting;
				}
			} else {
				o.disabled = setting;
			}
		} catch (e) {
		}
	}
}

//!-----------------------------------------------------------
// @function	enableField
// @desc		Habilita um campo de um formulário
// @param		frm String		Nome do formulário
// @param		fld String		Nome do campo
// @return		void
//!-----------------------------------------------------------
function enableField(frm, fld) {
	setDisableField(frm, fld, false);
}

//!-----------------------------------------------------------
// @function	enableFieldList
// @desc		Habilita um conjunto de campos de um formulário
// @param		frm String		Nome do formulário
// @param		list Array		Conjunto de campos
// @return		void
//!-----------------------------------------------------------
function enableFieldList(frm, list) {
	if (typeof(list.length) != "undefined") {
		for (var i=0; i<list.length; i++) {
			enableField(frm, list[i]);
		}
	}
}

//!-----------------------------------------------------------
// @function	disableField
// @desc		Desabilita um campo de um formulário
// @param		frm String		Nome do formulário
// @param		fld String		Nome do campo
// @return		void
//!-----------------------------------------------------------
function disableField(frm, fld) {
	setDisableField(frm, fld, true);
}

//!-----------------------------------------------------------
// @function	disableFieldList
// @desc		Desabilita um conjunto de campos de um formulário
// @param		frm String		Nome do formulário
// @param		list Array		Conjunto de campos
// @return		void
//!-----------------------------------------------------------
function disableFieldList(frm, list) {
	if (typeof(list.length) != "undefined") {
		for (var i=0; i<list.length; i++) {
			disableField(frm, list[i]);
		}
	}
}

//!-----------------------------------------------------------
// @function	requestFocus
// @desc		Requisita foco para um campo de um formulário
// @param		frm String		Nome do formulário
// @param		fld String	Nome do campo requisitando foco
// @return		Boolean
// @note		Se o campo for do tipo RadioButton ou o campo estiver
//				desabilitado, o foco não será direcionado para o campo
//!-----------------------------------------------------------
function requestFocus(frm, fld) {
	if (window._php2go_editorFld == fld) {
		if ((typeof(frm) == 'object' && frm.name == window._php2go_editorFrm) ||
			(typeof(frm) == 'string' && frm == window._php2go_editorFrm)) {
			eval(window._php2go_editorFld + "_instance.focus();");
			return;
		}
	}
	var o = getFormFieldObj(frm, fld);
	if (o != null) {
		try {
			if (typeof(o.type) == "undefined" && o.length) {			
				for (var i=0; i<o.length; i++) {
					go = document.getElementById(fld.replace("[]", "") + "_" + i);
					if (go != null && !go.disabled) {
						go.focus();
						return true;
					}
				}
			} else if (!o.disabled && !o.readOnly) {	
				switch(o.type) {
					case "checkbox" :
					case "text" : 
					case "password" : 
					case "textarea" : 
					case "select-one" : 
					case "select-multiple" : 
						o.focus();
						break;
					case "radio" :
						go = document.getElementById(fld + "_0"); 
						if (go != null && !go.disabled)
							go.focus();
						break;
				}
			}
		} catch(e) {
			return false;
		}
		return true;
	} else {
		return false;
	}
}

//!--------------------------------------------------------------------
// @function	isEmpty
// @desc		Verifica se um campo está vazio ou não selecionado em um determinado formulário
// @param		frm String		Nome do formulário
// @param		fld String		Nome do campo
// @return		Boolean
//!--------------------------------------------------------------------
function isEmpty(frm, fld) {
	if (window._php2go_editorFld == fld) {
		if ((typeof(frm) == 'object' && frm.name == window._php2go_editorFrm) ||
			(typeof(frm) == 'string' && frm == window._php2go_editorFrm)) {
			return eval(window._php2go_editorFld + "_instance.isEmpty()");
		}
	}
	var o = getFormFieldObj(frm, fld);
	if (o == null) {
		return true;
	} else {
		if (typeof(o.type) == "undefined" && o.length) {
			return !isGroupSelected(frm, fld);
		} else {
			switch(o.type) {
				case "text" : 
				case "hidden" : 
				case "password" : 
				case "file" : 
				case "textarea": 
					return (trim(o.value) == ""); 
					break;
				case "select-one" : 
				case "select-multiple" : 
					return !isListSelected(frm, fld); 
					break;
				case "radio" : 
				case "checkbox" :
					return !isGroupSelected(frm, fld); 
					break;
			}
		}
	}
}

//!-----------------------------------------------------------------
// @function	isListSelected
// @desc		Verifica se foi selecionado um valor em uma lista
// @param		frm String	Nome do formulário
// @param		fld String	Nome do campo listbox
// @return		Boolean
//!-----------------------------------------------------------------
function isListSelected(frm, fld) {
	var list = getFormFieldObj(frm, fld);
	if (list == null || typeof(list.options) == 'undefined')
		return false;
	for (var i=0; i<list.options.length; i++) {
		if (list.options[i].selected == true && list.options[i].value != "")
			return true;
	}
	return false;
}

//!-------------------------------------------------------------------
// @function	isGroupSelected
// @desc		Verifica se foi selecionado um valor de um grupo de opções (radio button ou check group)
// @param		frm String		Nome do formulário
// @param		fld String		Nome do campo que representa o grupo de opções
// @return		Boolean
//!-------------------------------------------------------------------
function isGroupSelected(frm, fld) {
	var isSelected = false;
	var form = (typeof(frm) == 'string' ? getFormObj(frm) : (typeof(frm) == 'object' ? frm : null));
	if (typeof(form) != "undefined") {
		for (var i = 0; i < form.elements.length; i++) {
			if (form.elements[i].name == fld && form.elements[i].checked == true) 
				isSelected = true;
		}
	}
	return isSelected;
}

//!----------------------------------------------------------------
// @function	checkBoxChange
// @desc		Propaga as alterações feitas em um campo CHECKBOX de
//				um formulário para um campo auxiliar do tipo HIDDEN,
//				a fim de facilitar a verificação do valor escolhido
// @param		frm String			Nome do formulário
// @param		chkBox Field object	Campo do tipo CHECKBOX
// @return		void
//!----------------------------------------------------------------
function checkBoxChange(frm, chkBox) {
	var chkBoxName = chkBox.name;
	var hiddenField = getFormFieldObj(frm, "V_" + chkBoxName);
	if (hiddenField != null) {
		if (chkBox.checked == true) {
			hiddenField.value = "T";
		} else {
			hiddenField.value = "F";
		}
	}
}

//!-----------------------------------------------------------
// @function	editCalculatorClick
// @desc		Trata o evento onClick do botão de abertura da calculadora
// @param		frm String			Nome do formulário
// @param		fld String			Nome do campo associado à calculadora
// @param		url String			Caminho do script da calculadora
// @param		language String		Linguagem ativa
// @param		mask String			Máscara do campo
// @param		event Event object	Evento
// @return		void
//!-----------------------------------------------------------
function editCalculatorClick(frm, fld, url, language, mask, event) {
	var o = getFormFieldObj(frm, fld);
	if (o.disabled == false && o.readOnly == false) {
		url = url + "?form=" + frm + "&field=" + fld + "&language=" + language + mask;
		createWindow(url, 282, 117, null, null, 'calculator', 0, event);
	}
}

//!-----------------------------------------------------------
// @function	getRadioOptions
// @desc		Monta um vetor contendo as opções de um grupo
//				de campos do tipo RADIO
// @param		formName String		Nome do formulário
// @param		fieldName String	Nome do grupo de campos RADIO
// @return		Array Vetor de opções RADIO
// @note		Retorna false em caso de erros
//!-----------------------------------------------------------
function getRadioOptions(formName, fieldName) {
	var form = (typeof(formName) == 'string' ? getFormObj(formName) : (typeof(formName) == 'object' ? formName : null));
	var result = new Array();
	if (form == null)	
		return false;	
	for (var i=0; i<form.elements.length; i++) {
		if (form.elements[i].type == 'radio' && form.elements[i].name == fieldName)
			result[result.length] = form.elements[i];
	}
	return (result.length > 0) ? result : false;
}

//!-----------------------------------------------------------
// @function	clearForm
// @desc		Limpa os valores dos campos de um formulário
// @param		formName String			Nome do formulário
// @param		hasEditor Boolean		Flag do editor avançado de formulários
// @param		clearReadOnly Boolean	Flag para limpar campos readOnly
// @param		subset String			Lista de campos (subconjunto), separada por vírgulas
// @return		void
//!-----------------------------------------------------------
function clearForm(formName, editor, clearReadOnly, subset) {
	var i,ss,mc,pmc;
	var form = getFormObj(formName);
	var rOnly = (clearReadOnly == null) ? false : clearReadOnly;
	// subconjunto de caracteres que devem ter o conteúdo apagado
	ss = (subset != null ? "," + String(subset) + "," : "");
	// memo fields com controle de caracteres
	mc = (typeof(window._php2go_memoFields) != 'undefined' ? "," + window._php2go_memoFields : "");
	for (var i=0; i<form.elements.length; i++) {
		if (!rOnly && form.elements[i].readOnly == true) 
			continue;
		if (ss != "" && ss.indexOf("," + form.elements[i].name + ",") == -1) 
			continue;
		if (/_count$/.test(form.elements[i].name)) {
			eval("pmc = /(" + form.elements[i].name + ")\\$\\$(\\d+)/.exec(mc);");
			if (pmc) {
				form.elements[i].value = pmc[2];
				continue;
			}
		}
		switch(form.elements[i].type) {
			case 'text' :
			case 'password' :
			case 'textarea' :	
				form.elements[i].value = "";
				break;
			case 'select-one':	
				if (form.elements[i].options.length > 0)
					form.elements[i].options[0].selected = !form.elements[i].options[0].selected;
				break;
			case 'select-multiple': 
				for (var j=0; j<form.elements[i].options.length; j++)
					form.elements[i].options[j].selected = false;
				break;
			case 'checkbox' :
			case 'radio' :
				form.elements[i].checked = false;
		}
	}
	if (editor) {
		eval("if ("+editor+"_instance) {"+editor+"_instance.setHtml('');}");
	}
}

//!-----------------------------------------------------------
// @function	createOptionsFromString
// @desc		Cria um conjunto de OPTIONS em um campo do tipo
//				SELECT a partir de uma string com delimitadores
//				entre opções e itens da mesma opção
// @param		str String				String contendo as opções
// @param		formName mixed			Nome do formulário ou objeto HTMLForm
// @param		fieldName String		Nome do campo
// @param		lineSep String			Separador entre as opções (padrão é ~)
// @param		colSep String			Separador entre itens da mesma opção (padrão é |)
// @param		initialIndex Integer	Índice inicial para inserir a primeira opção
// @return 		Integer Número de opções inseridas
// @note		Retorna false em caso de erros
//!-----------------------------------------------------------
function createOptionsFromString(str, formName, fieldName, lineSep, colSep, initialIndex) {
	var i, j, optIndex, fld, lines, cols, inserted;
	var fld = (typeof(formName) == 'string' ? getFormFieldObj(formName, fieldName) : (typeof(formName) == 'object' ? formName.elements[fieldName] : null));	
	if (fld == null || !fld.type || fld.type.indexOf('select') == -1 || typeof(str) != 'string') {
		return false;
	}
	if (lineSep == null) lineSep = '~';
	if (colSep == null) colSep = '|';
	if (initialIndex == null) initialIndex = 0;
	if (initialIndex > fld.options.length) return false;
	inserted = 0;
	optIndex = initialIndex;
	lines = str.split(lineSep);
	if (lines.length > 0) {
		for (var i=0; i<lines.length; i++) {
			cols = lines[i].split(colSep);
			if (cols.length >= 2) {
				fld.options[optIndex] = new Option(cols[1], cols[0]);
				inserted++;
			}
			optIndex++;
		}
		return inserted;
	}
	return false;
}

//!-----------------------------------------------------------
// @function	clearOptions
// @desc		Remove todas as OPTIONS de um campo do tipo SELECT
// @param		formName mixed		Nome do formulário ou objeto HTMLForm
// @param		fieldName String	Nome do campo
// @return		Boolean
//!-----------------------------------------------------------
function clearOptions(formName, fieldName) {
	var fld = (typeof(formName) == 'string' ? getFormFieldObj(formName, fieldName) : (typeof(formName) == 'object' ? formName.elements[fieldName] : null));
	if (fld == null || !fld.type || fld.type.indexOf('select') == -1)
		return false;
	fld.options.length = 0;
	return true;
}

//!-----------------------------------------------------------
// @function	addOption
// @desc		Adiciona uma OPTION a um campo do tipo SELECT
// @param		formName mixed		Nome do formulário ou objeto HTMLForm
// @param		fieldName String	Nome do campo
// @param		value String		Valor da OPTION
// @param		caption String		Texto da OPTION
// @param		optIndex Integer	Índice onde deve ser inserida
// @return		void
//!-----------------------------------------------------------
function addOption(formName, fieldName, value, caption, optIndex) {
	var index;
	var fld = (typeof(formName) == 'string' ? getFormFieldObj(formName, fieldName) : (typeof(formName) == 'object' ? formName.elements[fieldName] : null));	
	if (fld == null || !fld.type || fld.type.indexOf('select') == -1)
		return false;
	index = (optIndex == null) ? 0 : parseInt(optIndex);
	if (index < 0 || index > fld.options.length)
		return false;
	fld.options[index] = new Option(String(caption), String(value));
}

//!-----------------------------------------------------------
// @function	selectOptionByCaption
// @desc		Busca uma determinada opção em um campo de seleção
//				a partir do valor da CAPTION (atributo text). Se ela
//				for encontrada, marca esta opção como selecionada
// @param		formName mixed		Nome do formulário ou objeto HTMLForm
// @param		fieldName String	Nome do campo
// @param		caption String		Caption da opção a ser selecionada
// @return		void
//!-----------------------------------------------------------
function selectOptionByCaption(formName, fieldName, caption) {
	var fld = (typeof(formName) == 'string' ? getFormFieldObj(formName, fieldName) : (typeof(formName) == 'object' ? formName.elements[fieldName] : null));
	if (fld == null || !fld.type || fld.type.indexOf('select') == -1)
		return false;
	for (var i=0; i<fld.options.length; i++) {
		if (fld.options[i].text == caption) {
			fld.options[i].selected = true;
			return true;
		}
	}
	return false;
}

//!-----------------------------------------------------------
// @function	memoFieldCharControl
// @desc		Trata a inserção de um caractere em um campo MemoField
//				com controle de máximo de caracteres habilitado
// @param		memoFldObj Object	Campo MemoField
// @param		cntFldObj Object	Campo texto que armazena a contagem regressiva
// @param		maxLength Integer	Tamanho máximo do campo
// @param		event Event Object	Evento do teclado capturado
// @return		void
//!-----------------------------------------------------------
function memoFieldCharControl(memoFldObj, cntFldObj, maxLength, event) {
	var memoLength, charsLeft, backCount;
	if (typeof(memoFldObj) == 'undefined' || typeof(cntFldObj) == 'undefined' || maxLength == null)
		return false;
	memoLength = memoFldObj.value.length;
	if (memoLength > maxLength) {
		memoFldObj.value = memoFldObj.value.substring(0, maxLength);
		charsLeft = 0;
	} else {
		charsLeft = maxLength - memoLength;
	}
	cntFldObj.value = charsLeft;
}

//!-----------------------------------------------------------
// @function	registerMemoField
// @desc		Registra em uma variável global a existência de um
//				campo MEMOFIELD com controle de caracteres. Esta informação
//				será utilizada na função clearForm
// @param		id String			ID do campo de controle de caracteres
// @param		maxLength Integer	Número máximo de caracteres
// @return		void
//!-----------------------------------------------------------
function registerMemoField(id, maxLength) {
	if (typeof(window._php2go_memoFields) == 'undefined')
		window._php2go_memoFields = id + '$$' + maxLength + ',';
	else
		window._php2go_memoFields += id + '$$' + maxLength + ',';
}