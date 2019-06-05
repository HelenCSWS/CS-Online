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
// $Header: /www/cvsroot/php2go/core/form/field/EditField.class.php,v 1.28 2005/08/30 14:44:48 mpont Exp $
// $Date: 2005/08/30 14:44:48 $

//------------------------------------------------------------------
import('php2go.form.field.EditableField');
import('php2go.datetime.Date');
//------------------------------------------------------------------

//!-----------------------------------------------------------------
// @class		EditField
// @desc		Classe responsável por construir um INPUT HTML do 
//				tipo TEXT, para simples edição de texto
// @package		php2go.form.field
// @uses		Date
// @uses		HtmlUtils
// @extends		EditableField
// @author		Marcos Pont
// @version		$Revision: 1.28 $
//!-----------------------------------------------------------------
class EditField extends EditableField
{
	var $calendarJsCode = '';		// @var calendarJsCode string	"" 	Código JavaScript padrão para a instância de um calendário
	
	//!-----------------------------------------------------------------
	// @function	EditField::EditField
	// @desc		Construtor da classe EditField, inicializa os atributos do campo
	// @access		public
	// @param		&Form Form object	Formulário no qual o campo é inserido
	// @param		child bool			"FALSE" Se for TRUE, indica que o campo é membro de um campo composto	
	//!-----------------------------------------------------------------
	function EditField(&$Form, $child=FALSE) {	
		parent::EditableField($Form, $child);
		$this->htmlType = 'TEXT';
		$dateFormat = (PHP2Go::getConfigVal('LOCAL_DATE_TYPE') == 'EURO' ? "%%d/%%m/%%Y" : "%%Y/%%m/%%d");
		$this->calendarJsCode = "     Calendar.setup( {\n          inputField:\"%s\", ifFormat:\"{$dateFormat}\", button:\"%s\", singleClick:true, align:\"Bl\", cache:true, showOthers:true, onClose: dateCalendarClose\n     } );";
	}
	
	//!-----------------------------------------------------------------
	// @function	EditField::getCode
	// @desc		Monta o código HTML do campo
	// @access		public
	// @return		string Código HTML do campo
	//!-----------------------------------------------------------------
	function getCode() {
		$this->onPreRender();
		$btnDisabled = ($this->attributes['READONLY'] != '' || $this->attributes['DISABLED'] != '' || $this->_Form->readonly ? " DISABLED" : "");
		// botão de exibição do calendário (date picker)
		if ($this->mask == 'DATE') {
			$this->_Form->Document->importStyle(PHP2GO_JAVASCRIPT_PATH . "ext/jscalendar/calendar-system.css");
			$this->_Form->Document->addScript(PHP2GO_JAVASCRIPT_PATH . "ext/jscalendar/calendar_stripped.js");
			$this->_Form->Document->addScript(PHP2GO_JAVASCRIPT_PATH . "ext/jscalendar/lang/" . PHP2Go::getConfigVal('CALENDAR_LANGFILE'));
			$this->_Form->Document->addScript(PHP2GO_JAVASCRIPT_PATH . "ext/jscalendar/calendar-setup_stripped.js");
			$this->_Form->Document->addScriptCode(sprintf($this->calendarJsCode, $this->name, $this->name . '_calendar'), 'JavaScript', SCRIPT_END);
			$this->attributes['CALENDAR'] = sprintf("<BUTTON ID=\"%s\" TYPE=\"BUTTON\" %s STYLE=\"vertical-align:text-bottom;cursor:pointer;width:20px;background-color:transparent;border:none\"%s><IMG SRC=\"%s\" BORDER=\"0\" ALT=\"\"/></BUTTON>", 
					$this->name . '_calendar', 
					HtmlUtils::statusBar(PHP2Go::getLangVal('CALENDAR_LINK_TITLE')), 
					$this->attributes['TABINDEX'],
					$this->_Form->icons['calendar']
			);
		} else {
			$this->attributes['CALENDAR'] = '';
		}
		// botão de exibição da calculadora
		if ($this->attributes['CALCULATOR'] && ($this->mask == 'INTEGER' || $this->mask == 'FLOAT' || $this->mask == 'CURRENCY' || $this->mask == '') && PHP2GO_OFFSET_PATH != PHP2GO_ABSOLUTE_PATH) {
			$mask = '&mask=' . $this->mask . (TypeUtils::isArray($this->limiters) ? '-' . implode(':', $this->limiters) : '');
			$this->attributes['CALCULATOR'] = sprintf("<BUTTON ID=\"%s\" TYPE=\"BUTTON\" %s STYLE=\"vertical-align:text-bottom;cursor:pointer;width:23px;background-color:transparent;border:none\" onClick=\"editCalculatorClick('%s', '%s', '%s', '%s', '%s', event)\"%s><IMG NAME=\"php2go_calculator\" SRC=\"%s\" BORDER=\"0\" ALT=\"\"></BUTTON>", 
					$this->name . '_calculator', 
					HtmlUtils::statusBar(PHP2Go::getLangVal('CALCULATOR_LINK_TITLE')), 
					$this->_Form->formName, $this->name, 
					PHP2GO_OFFSET_PATH . '/apps/calculator.php', 
					PHP2Go::getConfigVal('LANGUAGE_CODE'), $mask, 
					$this->attributes['TABINDEX'],
					$this->_Form->icons['calculator']
			);
		} else {
			$this->attributes['CALCULATOR'] = '';
		}
		 // construção do código do campo
		$this->htmlCode = sprintf("<INPUT TYPE=\"text\" ID=\"%s\" NAME=\"%s\" VALUE=\"%s\" MAXLENGTH=\"%s\" SIZE=\"%s\" TITLE=\"%s\"%s%s%s%s%s%s%s%s%s%s>%s%s",
			$this->name, $this->name, $this->value, $this->attributes['LENGTH'], $this->attributes['SIZE'], $this->label, $this->attributes['SCRIPT'], 
			$this->maskFunction, $this->attributes['TABINDEX'], $this->attributes['ALIGN'], $this->attributes['STYLE'], $this->attributes['READONLY'], 
			$this->attributes['DISABLED'], $this->attributes['DATASRC'], $this->attributes['DATAFLD'], $this->attributes['AUTOCOMPLETE'], 
			$this->attributes['CALENDAR'], $this->attributes['CALCULATOR']
		);
		return $this->htmlCode;
	}
	
	//!-----------------------------------------------------------------
	// @function	EditField::setCapitalize
	// @desc		Habilita ou desabilita a transformação do valor do campo, no momento
	//				da submissão, para possuir o primeiro caractere de cada palavra em
	//				maiúscula e o resto em letras minúsculas (capitalização)
	// @access		public
	// @param		setting bool	"TRUE" Valor para o atributo
	// @return		void
	//!-----------------------------------------------------------------
	function setCapitalize($setting=TRUE) {
		if (TypeUtils::isTrue($setting))
			$this->attributes['CAPITALIZE'] = "T";
		else
			$this->attributes['CAPITALIZE'] = "F";
	}	
	
	//!-----------------------------------------------------------------
	// @function	EditField::setAutoTrim
	// @desc		Habilita ou desabilita a remoção automática dos caracteres
	//				brancos no início e no fim do valor informado no campo no
	//				momento da submissão do formulário
	// @access		public
	// @param		setting bool	"TRUE" Valor para o atributo
	// @return		void
	//!-----------------------------------------------------------------
	function setAutoTrim($setting=TRUE) {
		if (TypeUtils::isTrue($setting))
			$this->attributes['AUTOTRIM'] = "T";
		else
			$this->attributes['AUTOTRIM'] = "F";
	}
	
	//!-----------------------------------------------------------------
	// @function	EditField::isValid
	// @desc		Sobrecarrega o método EditableField::isValid a fim de executar 
	//				as conversões de valor necessárias no momento da validação do campo
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function isValid() {
		if ($this->attributes['CAPITALIZE'] == "T")
			$this->value = StringUtils::capitalize($this->value);
		if ($this->attributes['AUTOTRIM'] == "T")
			$this->value = trim($this->value);
		return parent::isValid();
	}
	
	//!-----------------------------------------------------------------
	// @function	EditField::onLoadNode
	// @desc		Método responsável por processar atributos e nodos filhos
	//				provenientes da especificação XML do campo
	// @access		protected
	// @param		attrs array		Atributos do nodo
	// @param		children array	Vetor de nodos filhos
	// @return		void
	//!-----------------------------------------------------------------
	function onLoadNode($attrs, $children) {
		parent::onLoadNode($attrs, $children);
		// expressões de data
		if ($this->mask == 'DATE')
			parent::setValue(Date::parseFieldExpression($this->value));
		// calculator
		$this->attributes['CALCULATOR'] = Form::resolveBooleanChoice(@$attrs['CALCULATOR']);
		// capitalize
		$this->setCapitalize(Form::resolveBooleanChoice(@$attrs['CAPITALIZE']));
		// autotrim
		$this->setAutoTrim(Form::resolveBooleanChoice(@$attrs['AUTOTRIM']));
	}
	
	//!-----------------------------------------------------------------
	// @function	EditField::onPreRender
	// @desc		Constrói o código JavaScript relacionado com os atributos
	//				CAPITALIZE e AUTOTRIM (transformação de valor antes da submissão)
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function onPreRender() {
		parent::onPreRender();
		if ($this->attributes['CAPITALIZE'] == 'T') {
			$this->_Form->appendScript(sprintf("          document.%s.elements['%s'].value = capitalizeWords(String(document.%s.elements['%s'].value));\n",
						$this->_Form->formName, $this->name, $this->_Form->formName, $this->name));
		}
		if ($this->attributes['AUTOTRIM'] == 'T') {
			$this->_Form->appendScript(sprintf("          document.%s.elements['%s'].value = trim(String(document.%s.elements['%s'].value));\n",
						$this->_Form->formName, $this->name, $this->_Form->formName, $this->name));
		}		
	}	
}
?>