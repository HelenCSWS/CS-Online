<?php
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
// $Header: /www/cvsroot/php2go/core/form/FormButton.class.php,v 1.18 2005/07/25 13:35:02 mpont Exp $
// $Date: 2005/07/25 13:35:02 $

//------------------------------------------------------------------
import('php2go.util.HtmlUtils');
//------------------------------------------------------------------

//!-----------------------------------------------------------------
// @class		FormButton
// @desc		Armazena informa��es sobre um bot�o inclu�do em um
// 				formul�rio. A partir das configura��es criadas para
// 				o bot�o, esta classe cria o c�digo HTML do mesmo
// @package		php2go.form
// @extends		PHP2Go
// @uses		FormEventListener
// @uses		HtmlUtils
// @uses		TypeUtils
// @author		Marcos Pont
// @version		$Revision: 1.18 $
//!-----------------------------------------------------------------
class FormButton extends PHP2Go
{
	var $name = '';				// @var name string			"" Nome do bot�o
	var $value = '';			// @var value string		"" Caption/texto do bot�o
	var $attributes = array();	// @var attrs array			"array()" Atributos do bot�o
	var $children = array();	// @var children array		"array()" Nodos filhos do bot�o na especifica��o XML
	var $htmlCode = '';			// @var htmlCode string		"" C�digo HTML do bot�o
	var $disabled = FALSE;		// @var disabled bool		"FALSE" Status do bot�o
	var $listeners = array();	// @var listeners array		"array()" Tratadores de eventos associados ao bot�o
	var $xmlAttrs = array();	// @var xmlAttrs array		"array()" Atributos do bot�o definidos na especifica��o XML
	var $_Form = NULL;			// @var _Form Form object	"NULL" Objeto Form no qual o bot�o ser� inclu�do

	//!-----------------------------------------------------------------
	// @function	FormButton::FormButton
	// @desc		Inicializa as propriedades do bot�o e executa a
	// 				fun��o de constru��o do c�digo HTML
	// @access		public
	// @param		&Node XmlNode object	Objeto XmlNode que cont�m as configura��es do bot�o
	// @param 		&Form Form object		Objeto Form onde o bot�o ser� inclu�do
	// @param		internal bool			"FALSE" Se o bot�o � interno, o valor deste par�metro dever� ser TRUE
	//!-----------------------------------------------------------------
	function FormButton(&$Node, &$Form) {
		PHP2Go::PHP2Go();
		$this->attributes = array();
		$this->children = $Node->getChildrenTagsArray();
		$this->htmlCode = '';
		$this->listeners = array();
		$this->xmlAttrs = $Node->getAttributes();
		$this->_Form =& $Form;
		$this->_parseButton();
		$this->_Form->verifyButtonName($this->_Form->formName, $this->name);
		parent::registerDestructor($this, '_FormButton');
	}
	
	//!-----------------------------------------------------------------
	// @function	FormButton::_FormButton
	// @desc		Destrutor da classe
	// @access		public
	//!-----------------------------------------------------------------
	function _FormButton() {
		unset($this);
	}

	//!-----------------------------------------------------------------
	// @function	FormButton::getName
	// @desc		Consulta o nome do bot�o
	// @access		public
	// @return		string Nome do bot�o
	//!-----------------------------------------------------------------
	function getName() {
		return $this->name;
	}
	
	//!-----------------------------------------------------------------
	// @function	FormButton::getValue
	// @desc		Consulta o valor (caption) do bot�o
	// @access		public
	// @return		string Caption do bot�o
	//!-----------------------------------------------------------------
	function getValue() {
		return $this->value;
	}

	//!-----------------------------------------------------------------
	// @function	FormButton::getAttribute
	// @desc		Busca o valor de um atributo do bot�o
	// @access		public
	// @param		attrName string	Nome do atributo
	// @return		mixed Valor do atributo ou FALSE se ele n�o for encontrado
	//!-----------------------------------------------------------------
	function getAttribute($attrName) {
		if (!empty($this->xmlAttrs) && isset($this->xmlAttrs[$attrName])) {
			return trim($this->xmlAttrs[$attrName]);
		}
		return FALSE;
	}
	
	//!-----------------------------------------------------------------
	// @function	FormButton::&getOwnerForm
	// @desc		Retorna o formul�rio no qual o bot�o est� inserido
	// @access		public
	// @return		Form object
	//!-----------------------------------------------------------------
	function &getOwnerForm() {
		return $this->_Form;
	}
	
	//!-----------------------------------------------------------------
	// @function	FormButton::getCode
	// @desc		Constr�i o conte�do HTML do bot�o
	// @access		public
	// @return		string C�digo HTML do bot�o
	//!-----------------------------------------------------------------	
	function getCode() {		
		$this->_buttonListeners();
		if ($this->attributes['IMG'] != '')
			// bot�o desabilitado - somente imagem
			if ($this->disabled)
				$this->htmlCode = HtmlUtils::image($this->attributes['IMG'], $this->attributes['ALT'], 0, 0, -1, -1, "", "", $this->attributes['SWPIMG']);
			// imagem com type RESET - link para fun��o JS
			else if ($this->attributes['TYPE'] == 'RESET')
				$this->htmlCode = sprintf("<A ID=\"%s\" HREF=\"javascript:void(0);\" onClick=\"document.%s.reset();\"%s %s>%s</A>", 
					$this->name, $this->_Form->formName, $this->_Form->getLabelStyle(), HtmlUtils::statusBar($this->attributes['ALT']), HtmlUtils::image($this->attributes['IMG'], "", 0, 0, -1, -1, "", "", $this->attributes['SWPIMG']));
			// imagem com type BUTTON - link com eventos tratados
			else if ($this->attributes['TYPE'] == 'BUTTON')
				$this->htmlCode = sprintf("<A ID=\"%s\" HREF=\"javascript:void(0);\"%s%s %s>%s</A>", 
					$this->name, $this->attributes['SCRIPT'], $this->_Form->getLabelStyle(), HtmlUtils::statusBar($this->attributes['ALT']), HtmlUtils::image($this->attributes['IMG'], "", 0, 0, -1, -1, "", "", $this->attributes['SWPIMG']));
			// imagem com type SUBMIT - input type IMAGE
			else {			
				$this->htmlCode = sprintf("<INPUT ID=\"%s\" NAME=\"%s\" TYPE=\"IMAGE\" SRC=\"%s\" BORDER=\"0\"%s%s%s%s%s>",
					$this->name, $this->name, $this->attributes['IMG'], $this->attributes['ALTHTML'], $this->attributes['SCRIPT'],
					$this->attributes['TABINDEX'], $this->attributes['DISABLED'], $this->attributes['STYLE']);
			}
		else {
			$this->htmlCode = sprintf("<INPUT ID=\"%s\" NAME=\"%s\" TYPE=\"%s\" VALUE=\"%s\"%s%s%s%s%s>", 
				$this->name, $this->name, $this->attributes['TYPE'], $this->value, $this->attributes['ALTHTML'], 
				$this->attributes['SCRIPT'], $this->attributes['STYLE'], $this->attributes['TABINDEX'], $this->attributes['DISABLED']);
		}
		return $this->htmlCode;
	}
	
	//!-----------------------------------------------------------------
	// @function	FormButton::setImage
	// @desc		Define a imagem associada ao bot�o
	// @param		img string		Caminho da imagem
	// @access		public	
	// @return		void
	//!-----------------------------------------------------------------
	function setImage($img, $swpImg='') {
		$this->attributes['IMG'] = trim(TypeUtils::parseString($img));
		if ($swpImg && trim($swpImg) != '')
			$this->attributes['SWPIMG'] = $swpImg;
		else
			$this->attributes['SWPIMG'] = '';			
	}
	
	//!-----------------------------------------------------------------
	// @function	FormButton::setStyle
	// @desc		Altera o valor do estilo do bot�o
	// @param		style string	Estilo para o bot�o
	// @access		public	
	// @return		void
	// @note		Este m�todo permite customizar o estilo de um determinado
	//				bot�o em rela��o � configura��o global definida para todo
	//				o formul�rio
	//!-----------------------------------------------------------------
	function setStyle($style) {
		if (trim($style) == 'empty')
			$this->attributes['STYLE'] = " CLASS=\"\"";
		elseif (trim($style) != '')
			$this->attributes['STYLE'] = " CLASS=\"" . trim($style) . "\"";
		else
			$this->attributes['STYLE'] = $this->_Form->getButtonStyle();
	}
	
	//!-----------------------------------------------------------------
	// @function	FormButton::setTabIndex
	// @desc		Define o �ndice de tab order do bot�o
	// @param		tabIndex int		�ndice para o bot�o
	// @access		public	
	// @return		void
	//!-----------------------------------------------------------------
	function setTabIndex($tabIndex) {
		if (TypeUtils::isInteger($tabIndex))
			$this->attributes['TABINDEX'] = " TABINDEX=\"$tabIndex\"";
		else
			$this->attributes['TABINDEX'] = '';
	}
	
	//!-----------------------------------------------------------------
	// @function	FormButton::setAltText
	// @desc		Define o texto alternativo do bot�o
	// @access		public
	// @param		altText string		Texto alternativo para o bot�o
	// @return		void
	// @note		Este atributo apenas tem efeito quando o bot�o utiliza uma imagem
	//!-----------------------------------------------------------------
	function setAltText($altText) {
		if (TypeUtils::isString($altText) && trim($altText) != '') {
			$this->attributes['ALTHTML'] = " ALT=\"$altText\"";
			$this->attributes['ALT'] = trim($altText);
		} else {
			$this->attributes['ALTHTML'] = "";
			$this->attributes['ALT'] = "";
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	FormButton::setDisabled
	// @desc		Define o estado do bot�o (habilitado ou desabilitado)
	// @access		public
	// @param		setting bool		Estado do bot�o (TRUE=desabilitado)
	//!-----------------------------------------------------------------
	function setDisabled($setting) {
		if (TypeUtils::isTrue($setting)) {
			$this->attributes['DISABLED'] = " DISABLED";
			$this->disabled = TRUE;
		} else {
			$this->attributes['DISABLED'] = "";
			$this->disabled = FALSE;
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	FormButton::addEventListener
	// @desc		Adiciona um novo tratador de eventos no bot�o
	// @access		public
	// @param		Listener FormEventListener object	Tratador de evento
	// @return		void
	//!-----------------------------------------------------------------
	function addEventListener($Listener) {
		$Listener->setOwner($this);
		if ($Listener->isValid()) {
			$this->listeners[] =& $Listener;
		}		
	}
	
	//!-----------------------------------------------------------------
	// @function	FormButton::_buttonListeners
	// @desc		A partir dos tratadores de eventos armazenados na classe,
	//				constr�i a string com as declara��es dos eventos para inclus�o
	//				no c�digo HTML final do bot�o
	// @access		private
	// @return		void
	//!-----------------------------------------------------------------
	function _buttonListeners() {
		$script = '';
		$events = array();
		foreach ($this->listeners as $listener) {
			$eventName = $listener->eventName;
			if (!isset($events[$eventName]))
				$events[$eventName] = array();
			$events[$eventName][] = $listener->getScriptCode();
		}
		foreach ($events as $event => $action) {
			$action = implode(';', $action);
			$script .= " {$event}=\"" . str_replace('\"', '\'', $action) . ";\"";
		}
		$this->attributes['SCRIPT'] = $script;
	}
	
	//!-----------------------------------------------------------------
	// @function	FormButton::_parseButton
	// @desc		Captura o valor dos principais atributos do bot�o
	// @access		private
	// @return		void
	//!-----------------------------------------------------------------	
	function _parseButton() {
		// NAME
		if ($name = $this->getAttribute('NAME'))
			$this->name = $name;
		else
			$this->name = PHP2Go::generateUniqueId($this->objectName);
		$buttonType = $this->getAttribute('TYPE');			
		// VALUE
		if ($value = $this->getAttribute('VALUE'))
			$this->value = Form::resolveI18nEntry($value);
		elseif ($buttonType)
			$this->value = ucfirst(strtolower($buttonType));
		else
			$this->value = $this->name;
		// TYPE
		$this->attributes['TYPE'] = ($buttonType && eregi("submit|reset|clear|button", $buttonType) ? strtoupper($buttonType) : 'BUTTON');
		if ($this->attributes['TYPE'] == 'CLEAR') {
			$this->attributes['TYPE'] = 'BUTTON';
			$this->addEventListener(new FormEventListener(FORM_EVENT_JS, 'onClick', sprintf("clearForm('%s', %s)", $this->_Form->formName, ($this->_Form->hasEditor ? "'{$this->_Form->editorName}'" : "null"))));
		}
		// IMG
		$this->setImage($this->getAttribute('IMG'), $this->getAttribute('SWPIMG'));
		if ($this->attributes['TYPE'] == 'SUBMIT' && $this->attributes['IMG'] != '' && $this->attributes['SWPIMG'] != '') {
			$this->addEventListener(new FormEventListener(FORM_EVENT_JS, 'onMouseOver', sprintf("this.src='%s'", $this->attributes['SWPIMG'])));
			$this->addEventListener(new FormEventListener(FORM_EVENT_JS, 'onMouseOut', sprintf("this.src='%s'", $this->attributes['IMG'])));
		}
		// FORMUL�RIO POSTADO
		if ($this->_Form->isPosted()) {
			if ($this->attributes['TYPE'] == 'SUBMIT' && $this->attributes['IMG'] != '') {
				$x = HttpRequest::getVar($this->name . '_x', $this->_Form->formMethod);
				$y = HttpRequest::getVar($this->name . '_y', $this->_Form->formMethod);
				if (!TypeUtils::isNull($x) && !TypeUtils::isNull($y))
					$this->_Form->submittedValues[$this->name] = array('x' => $x, 'y' => $y);
			} else {
				$submittedValue = HttpRequest::getVar($this->name, $this->_Form->formMethod);
				if (!TypeUtils::isNull($submittedValue))
					$this->_Form->submittedValues[$this->name] = $submittedValue;
			}
		}			
		// STYLE
		$this->setStyle($this->getAttribute('STYLE'));
		// TABINDEX
		$this->setTabIndex($this->getAttribute('TABINDEX'));
		// ALT
		$this->setAltText($this->getAttribute('ALT'));
		// DISABLED
		$this->setDisabled(Form::resolveBooleanChoice($this->getAttribute('DISABLED')) || $this->_Form->readonly);
		// LISTENERS
		if (isset($this->children['LISTENER'])) {
			$listeners = TypeUtils::toArray($this->children['LISTENER']);
			foreach ($listeners as $listenerNode)
				$this->addEventListener(FormEventListener::fromNode($listenerNode));
		}
	}
}
?>