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
// $Header: /www/cvsroot/php2go/resources/javascript/modules/advancededitor.js,v 1.19 2005/08/30 14:41:31 mpont Exp $
// $Date: 2005/08/30 14:41:31 $
// $Revision: 1.19 $

//!--------------------------------------------------------------
// @function	AdvancedEditor
// @desc		Construtor do objeto AdvancedEditor, cria os métodos
//				que poderão ser executados sobre o elemento
// @param		frmName String		Nome do formulário onde o editor está inserido
// @param		fldName String		Nome do editor na especificação do formulário
// @param		readOnly Boolean	Indica se o formulário é somente leitura
// @return		AdvancedEditor object
//!--------------------------------------------------------------
function AdvancedEditor(frmName, fldName, readOnly) {
	// propriedades
	window._php2go_editorFrm = frmName;
	window._php2go_editorFld = fldName;
	this.form = frmName;
	this.name = fldName;
	this.composition = getDocumentObject(this.name + "_composition");
	this.textarea = getDocumentObject(fldName + '_textarea');
	this.csobj = null;
	this.lastColorCmd = null;
	this.lastSelection = null;
	this.emoticons = getDocumentObject(fldName + '_divemoticons');	
	this.readOnly = readOnly;
	this.textMode = false;
	this.initHtml = "<html><head></head><body style='font-size:12px;font-family:arial,sans-serif;background-color:#ffffff'></body></html>";
}
AdvancedEditor.agent = navigator.userAgent.toLowerCase();
AdvancedEditor.ie = (AdvancedEditor.agent.indexOf("msie") != -1 && AdvancedEditor.agent.indexOf("opera") == -1);
AdvancedEditor.mozilla = (AdvancedEditor.agent.indexOf("mozilla") != -1 && AdvancedEditor.agent.indexOf("msie") == -1);
AdvancedEditor.affected = new Array();

//!--------------------------------------------------------------
// @function	init
// @desc		Função de inicialização do objeto
// @return		void
//!--------------------------------------------------------------
AdvancedEditor.prototype.init = function() {
	var editor = this;
	var doc = this.getDocument();
	// inicializa o documento
	doc.open();
	doc.write(this.initHtml);
	doc.close();
	if (!this.readOnly) {
		try { doc.designMode="On"; } catch(e) { }
	}
	// inicializa os tratadores de eventos
	addEvent(editor.getDocument(), "keydown", 
		function (event) { 
			return editor.keyHandler(event);
		}
	);
	addEvent(editor.getDocument(), "keypress", 
		function (event) {
			return editor.keyHandler(event);
		}
	);
	addEvent(editor.getDocument(), "click",
		function (event) {
			return editor.clickHandler();
		}
	);
	// inicializa o controle de seleção de cor
	this.csobj = new ColorSelection(this.name+'_divcolorsel', this.name+'_instance.pickColor');
	this.csobj.hideCovered = (AdvancedEditor.ie && /MSIE 5/i.test(navigator.userAgent));
	this.csobj.init();
};

//!--------------------------------------------------------------
// @function	isEmpty
// @desc		Verifica se o conteúdo do editor é vazio, levando em consideração
//				apenas o conteúdo das tags e as tags gráficas
//!--------------------------------------------------------------
AdvancedEditor.prototype.isEmpty = function() {
	var re = /<(img|input|hr)/i;
	var h = this.getHtml();
	oldValue = RegExp.multiline;
	RegExp.multiline = true;
	var t = trim(this.getInnerText().replace("&nbsp;", ""));
	RegExp.multiline = oldValue;
	return (t == "" && !re.test(h));
};

//!--------------------------------------------------------------
// @function	getDocument
// @desc		Função de captura do documento incluído no IFRAME do editor
// @return		Document object Documento contido no IFRAME de edição
//!--------------------------------------------------------------
AdvancedEditor.prototype.getDocument = function() {
	(AdvancedEditor.ie ? eval("d = " + this.name + "_composition.document;") : d = this.composition.contentWindow.document);
	return d;
};

//!--------------------------------------------------------------
// @function	getInnerText
// @desc		Remove as tags HTML do conteúdo do editor, mantendo apenas as listadas no parâmetro allowedTags
// @return		String Conteúdo de texto do editor
//!--------------------------------------------------------------
AdvancedEditor.prototype.getInnerText = function() {
	var h = this.getHtml();
	return h.replace(/<[^>]+>/g, "", h);
};

//!--------------------------------------------------------------
// @function	getHtml
// @desc		Busca o conteúdo HTML atual do editor
// @return		String Conteúdo HTML
//!--------------------------------------------------------------
AdvancedEditor.prototype.getHtml = function() {
	return (this.textMode ? this.textarea.value : this.getDocument().body.innerHTML);
};

//!--------------------------------------------------------------
// @function	setHtml
// @desc		Define o conteúdo HTML do editor
// @param		html String			Novo conteúdo HTML para o editor
// @return		void
//!--------------------------------------------------------------
AdvancedEditor.prototype.setHtml = function(html) {
	(this.textMode ? this.textarea.value = html : this.getDocument().body.innerHTML = html);
};

//!--------------------------------------------------------------
// @function	addHtml
// @desc		Adiciona um conteúdo HTML ao editor
// @param		html String			HTML a ser adicionado
// @return		void
//!--------------------------------------------------------------
AdvancedEditor.prototype.addHtml = function(html) {
	if (this.textMode) {
		this.textarea.value += html;
	} else {
		var sel, range, frag, div, node;
		sel = this.getSelection();
		range = this.getRange(sel);
		if (AdvancedEditor.ie) {
			range.pasteHTML(html);
		} else {
			frag = this.getDocument().createDocumentFragment();
			div = this.getDocument().createElement('div');
			div.innerHTML = html;
			while (div.firstChild)
				frag.appendChild(div.firstChild);
			this.insertAtSelection(frag);
		}
	}
};

//!--------------------------------------------------------------
// @function	setMode
// @desc		Alterna entre o modo wysiwyg e o modo texto
// @return		void
//!--------------------------------------------------------------
AdvancedEditor.prototype.setMode = function() {
	this.textMode = getDocumentObject(this.name + "_switch").checked;
	if (this.textMode) { // alternar para modo texto
		this.textarea.value = this.getDocument().body.innerHTML;
		this.composition.style.display = "none";
		this.textarea.style.display = "block";
		this.textarea.focus();
	} else { // alternar para modo wysiwyg
		this.getDocument().body.innerHTML = this.textarea.value;
		this.textarea.style.display = "none";
		this.composition.style.display = "block";
		if (AdvancedEditor.mozilla)
			this.getDocument().designMode = "on";
		this.focus();
	}	
};

//!--------------------------------------------------------------
// @function	validateMode
// @desc		Valida se o editor está em modo wysiwyg, do contrário
//				as ações de formatação serão barradas
// @return		Boolean
//!--------------------------------------------------------------
AdvancedEditor.prototype.validateMode = function() {
	if (this.textMode) {
		alert(advEdValModeMsg);
		this.focus();
		return false;
	}
	return true;
};

//!--------------------------------------------------------------
// @function	focus
// @desc		Requisita foco para o IFRAME do editor (modo wysiwyg) ou para
//				a textarea do conteúdo (modo texto)
// @return		void
//!--------------------------------------------------------------
AdvancedEditor.prototype.focus = function() {
	if (!this.readOnly) {
		if (this.textMode)
			this.textarea.focus();
		else if (AdvancedEditor.ie)
			eval(this.name + "_composition.focus();");
		else
			this.composition.contentWindow.focus();
	}
};

//!--------------------------------------------------------------
// @function	format
// @desc		Executa um comando de formatação na seleção ativa do editor
// @param		cmdId String		Identificador do comando
// @param		param String		Parâmetro para execução do comando
// @return		void
//!--------------------------------------------------------------
AdvancedEditor.prototype.format = function(cmdId, param) {
	try {
		var doc = this.getDocument();
		if (this.validateMode() && !this.readOnly) {
			this.focus();
			switch (cmdId.toLowerCase()) {
				// backcolor para IE, hilitecolor para Mozilla/Gecko
				case "backcolor" :
					(AdvancedEditor.ie ? doc.execCommand(cmdId, false, param) : doc.execCommand('hilitecolor', false, param));
					break;
				// fontname e fontsize não podem possuir parâmetro vazio
				case "fontname" :
				case "fontsize" :
					if (param != "")
						doc.execCommand(cmdId, false, param);
					break;
				// formatblock no IE deve possuir < e > no parâmetro
				case "formatblock" :
					if (param == "removeformat") {
						doc.execCommand(param, false, null);
					} else {
						(AdvancedEditor.ie ? doc.execCommand(cmdId, false, '<'+param+'>') : doc.execCommand(cmdId, false, param));
					}
					break;
				default :
					doc.execCommand(cmdId, false, param);
			}			
		}
	} catch (e) {
	}
};

//!--------------------------------------------------------------
// @function	createAnchor
// @desc		Função que implementa a inclusão/alteração de um link no conteúdo do editor
// @return		void
//!--------------------------------------------------------------
AdvancedEditor.prototype.createAnchor = function() {
	var sel, range, ancestor, isA, href;
	try {
		if (this.validateMode() && !this.readOnly) {
			sel = this.getSelection();
			range = this.getRange(sel);
			ancestor = (AdvancedEditor.ie ? (sel.type == "Control" ? range(0).parentElement : range.parentElement()) : range.startContainer);
			isA = findParentElementByTag(ancestor, "A");
			href = prompt(advEdAddLinkMsg, isA ? isA.href : "http:\/\/");
			if (href != null && href != "" && href != "http://") {
				// alteração de um link existente
				if (isA)
					isA.href = href;
				// inclusão de link
				else if (sel.type == "None" || sel == "")
					this.addHtml("<a href='"+href+"' target='_blank'>"+href+"</a>");
				// aplicação de link para texto (apenas IE)
				else if (sel.type == "Text")
					this.addHtml("<a href='"+href+"' target='_blank'>"+range.text+"</a>");
				// Mozilla/Gecko
				else
					this.format("createlink", href);
			}
		}
	} catch(e) { 
	}
	this.focus();
};

//!--------------------------------------------------------------
// @function	insertImage
// @desc		Função que implementa a inclusão/alteração de uma imagem no conteúdo do editor
// @return		void
//!--------------------------------------------------------------
AdvancedEditor.prototype.insertImage = function() {
	var sel, range, ancestor, isImg, src;
	try {
		if (this.validateMode() && !this.readOnly) {
			sel = this.getSelection();
			range = this.getRange(sel);			
			ancestor = (AdvancedEditor.ie ? (sel.type == "Control" ? range(0) : range.parentElement()) : (range.commonAncestorContainer ? range.commonAncestorContainer.firstChild : range.startContainer));
			isImg = findParentElementByTag(ancestor, "IMG");
			src = prompt(advEdAddImgMsg, isImg ? isImg.src : "http://");
			if (src != null && src != "" && src != "http://") {
				// alteração de imagem existente
				if (isImg)
					isImg.src = src;
				// inclusão de imagem
				else if (sel.type == "None" || sel.type == "Text" || sel == "")
					this.addHtml("<img src='"+src+"' border='0' alt=''/>");
				// alteração de imagem (apenas IE)
				else if (sel.type == "Control")
					range(0).src = src;
				// Mozilla/Gecko
				else
					this.format('createimage', src);
			}
		}
	} catch(e) {
	}
	this.focus();
};

//!--------------------------------------------------------------
// @function	showHideEmoticons
// @desc		Mostra/esconde a layer de emoticons
// @param		el HTMLElement object	Elemento a partir do qual o evento é disparado
// @param		forceState Boolean		Permite forçar um determinado estado de visibilidade
// @return		void
//!--------------------------------------------------------------
AdvancedEditor.prototype.showHideEmoticons = function(el, forceState) {
	function doShowHide(op, o, x, y) {
		setDivVisibility(o, op);
		if (op) {
			if (x != null && y != null) {
				moveDivTo(o, x, y);
				(o.offsetWidth && o.offsetHeight) && (AdvancedEditor.affected = hideCoveredElements(x, x+o.offsetWidth, y, y+o.offsetHeight));
			}
		} else {
			for (i=0; i<AdvancedEditor.affected.length; i++)
				setDivVisibility(AdvancedEditor.affected[i], true);
			AdvancedEditor.affected = new Array();
		}
	}
	var div, pos, px, py;
	if (this.validateMode() && !this.readOnly) {
		this.csobj.hide(this.name+"_divcolorsel");
		this.lastColorCmd = null;
		div = getDivFromName(this.name + "_divemoticons");
		if (forceState == true || forceState == false) {
			doShowHide(forceState, div);
		} else if (div.style.visibility == 'hidden' || div.style.visibility == 'hide') {
			pos = getAbsolutePos(el);
			doShowHide(true, div, pos.x-155, pos.y+el.offsetHeight);
		} else {
			doShowHide(false, div);
			this.focus();
		}
	}	
};

//!--------------------------------------------------------------
// @function	addEmoticon
// @desc		Adiciona um emoticon ao conteúdo do IFRAME
// @param		emoticon String		Caminho da imagem
// @return		void
//!--------------------------------------------------------------
AdvancedEditor.prototype.addEmoticon = function(emoticon) {
	if (AdvancedEditor.ie) {
		var a = AdvancedEditor.affected;
		// se estiver escondido, o iframe deve ser exibido/escondido
		if (a.length > 0 && a[0].id == this.name + '_composition') {
			setDivVisibility(a[0], true);
			this.focus();
			this.addHtml("<img src='"+emoticon+"' border='0' alt=''/>");
			setDivVisibility(a[0], false);			
		} else {
			this.focus();
			this.addHtml("<img src='"+emoticon+"' border='0' alt=''/>");
		}
	} else {
		this.focus();
		this.format('insertimage', emoticon);
	}
};

//!--------------------------------------------------------------
// @function	showColorSel
// @desc		Exibe a caixa de seleção de cores para a cor da letra ou do fundo
// @param		src HTMLElement object	Elemento a partir do qual o evento é disparado
// @param		cmd String				Comando (forecolor ou backcolor)
// @return		void
//!--------------------------------------------------------------
AdvancedEditor.prototype.showColorSel = function(src, cmd) {
	if (this.validateMode() && !this.readOnly) {
		if (cmd == this.lastColorCmd) {
			this.lastColorCmd = null;
			this.csobj.hide(this.name+'_divcolorsel');			
		} else {
			this.saveSelection();
			this.showHideEmoticons(null, false);
			this.lastColorCmd = cmd;
			this.csobj.show(src);
		}
	}
};

//!--------------------------------------------------------------
// @function	pickColor
// @desc		Função callback chamada no momento em que uma cor é escolhida na palheta de cores
// @param		color String			Valor RGB da cor
// @return		void
//!--------------------------------------------------------------
AdvancedEditor.prototype.pickColor = function(color) {
	if (this.lastColorCmd != null) {
		this.restoreSelection();
		this.format(this.lastColorCmd, color);
		this.lastColorCmd = null;
	}
};

//!--------------------------------------------------------------
// @function	keyHandler
// @desc		Tratador dos eventos keydown e keypress para o conteúdo do editor
// @param		event Event object		Evento a ser tratado
// @return		void
//!--------------------------------------------------------------
AdvancedEditor.prototype.keyHandler = function(event) {
	var editor = this;
	var key = keyCode = sel = range = cmd = value = null;
	var keyEvent = (AdvancedEditor.ie && event.type == 'keydown') || (event.type == 'keypress');
	if (event.keyCode == 9 && !event.shiftKey && !AdvancedEditor.ie) {
		getDocumentObject(editor.name + "_switch").focus();
		stopEvent(event);
	}
	if (keyEvent && event.ctrlKey) {		
		key = (AdvancedEditor.ie ? String.fromCharCode(event.keyCode) : String.fromCharCode(event.charCode));
		switch (key.toLowerCase()) {
			case 'a' :
				if (event.shiftKey) {
					editor.createAnchor();
					stopEvent(event);
				} else if (!AdvancedEditor.ie) {
					sel = editor.getSelection();
					sel.removeAllRanges();
					range = editor.getRange();
					range.selectNodeContents(editor.getDocument().body);
					sel.addRange(range);
					stopEvent(event);
				}
				break;
			case 'b' : 
				cmd = 'bold'; 
				break;
			case 'f' :
				if (event.shiftKey) {
					document.getElementById(editor.name + '_fontname').focus();
					stopEvent(event);
				}
				break;
			case 'i' : 
				(event.shiftKey ? editor.insertImage() : cmd = 'italic');
				stopEvent(event);
				break;
			case 'u' : 
				cmd = 'underline'; 
				break;
			case 'x' : 
				!AdvancedEditor.ie || (cmd = 'cut');
				break;
			case 'c' : 
				!AdvancedEditor.ie || (cmd = 'copy');
				break;
			case 'v' : 
				!AdvancedEditor.ie || (cmd = 'paste');
				break;
			case 'l' : 
				cmd = 'justifyleft'; 
				break;
			case 'e' : 
				cmd = 'justifycenter'; 
				break;
			case 'r' : 
				cmd = 'justifyright'; 
				break;
		}
		if (cmd) {
			editor.format(cmd, value);
			stopEvent(event);
		}
	}
};

//!--------------------------------------------------------------
// @function	clickHandler
// @desc		Tratador do evento onclick do iframe utilizado no editor HTML
// @return		void
//!--------------------------------------------------------------
AdvancedEditor.prototype.clickHandler = function() {
	var doc = this.getDocument();
	if (doc.designMode != "on")
		doc.designMode = "on";
	this.focus();
};

//!--------------------------------------------------------------
// @function	getSelection
// @desc		Retorna a seleção atual do documento ativo
// @return		Selection object Seleção atual do documento
//!--------------------------------------------------------------
AdvancedEditor.prototype.getSelection = function() {
	if (AdvancedEditor.ie)
		return this.getDocument().selection;
	else
		return this.composition.contentWindow.getSelection();
};

//!--------------------------------------------------------------
// @function	saveSelection
// @desc		Salva a seleção atual do documento, se existir
// @return		void
//!--------------------------------------------------------------
AdvancedEditor.prototype.saveSelection = function() {
	var sel, range;
	if (AdvancedEditor.ie) {
		sel = this.getSelection();
		this.lastSelection = sel.createRange().getBookmark();
	} else if (AdvancedEditor.mozilla) {
		sel = this.getSelection();
		if (sel.rangeCount > 0) {
			this.lastSelection = sel.getRangeAt(0).cloneRange();			
		} else {
			this.lastSelection = null;
		}
	} else {
		this.lastSelection = null;
	}
};

//!--------------------------------------------------------------
// @function	restoreSelection
// @desc		Restaura uma seleção anteriormente salva para o documento
// @return		void
//!--------------------------------------------------------------
AdvancedEditor.prototype.restoreSelection = function() {
	var sel, range;
	if (this.lastSelection != null) {
		if (AdvancedEditor.ie) {
			range = this.getDocument().body.createTextRange();
			range.moveToBookmark(this.lastSelection);
			range.select();
		} else {
			sel = this.getSelection();
			sel.removeAllRanges();
			sel.addRange(this.lastSelection);
		}
	}
};

//!--------------------------------------------------------------
// @function	getRange
// @desc		Cria um range a partir da seleção atual do documento
// @param		sel Selection object	Seleção atual do documento
// @return		object Range criado
//!--------------------------------------------------------------
AdvancedEditor.prototype.getRange = function(sel) {
	if (AdvancedEditor.ie) {
		return sel.createRange();
	} else {
		this.focus();
		return (typeof(sel) != 'undefined' ? sel.getRangeAt(0) : this.getDocument().createRange());
	}
};

//!--------------------------------------------------------------
// @function	insertAtSelection
// @desc		Insere um elemento tendo como base a seleção atual
// @param		n HTMLElement object	Elemento a ser inserido
// @author		Mihai Bazon <http://dynarch.com/mishoo/home.epl>
// @return		void
//!--------------------------------------------------------------
AdvancedEditor.prototype.insertAtSelection = function(n) {
	var sel, range, node, pos;
	if (!AdvancedEditor.ie) {
		sel = this.getSelection();
		range = this.getRange(sel);
		sel.removeAllRanges();
		range.deleteContents();
		node = range.startContainer;
		pos = range.startOffset;
		range = this.getRange();
		switch (node.nodeType) {
			case 3 :
				if (n.nodeType == 3) {
					node.insertData(pos, n.data);
					range.setEnd(node, pos + n.length);
					range.setStart(node, pos + n.length);
				} else {
					node = node.splitText(pos);
					node.parentNode.insertBefore(n, node);
					range.setStart(node, 0);
					range.setEnd(node, 0);
				}
				break;
			case 1 :
				node = node.childNodes[pos];
				node.parentNode.insertBefore(n, node);
				range.setStart(node, 0);
				range.setEnd(node, 0);
				break;				
		}
		sel.addRange(range);
	}
};
// propriedades que guardam o form e o field do tipo editor avançado
window._php2go_editorFrm = null;
window._php2go_editorFld = null;