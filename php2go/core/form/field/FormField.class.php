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
// $Header: /www/cvsroot/php2go/core/form/field/FormField.class.php,v 1.39 2005/08/30 14:52:47 mpont Exp $
// $Date: 2005/08/30 14:52:47 $

//------------------------------------------------------------------
import('php2go.net.HttpRequest');
import('php2go.datetime.Date');
import('php2go.util.Hashmap');
import('php2go.util.HtmlUtils');
import('php2go.util.Statement');
import('php2go.validation.Validator');
//------------------------------------------------------------------

//!-----------------------------------------------------------------
// @class		FormField
// @desc		Classe abstrata que atua como base para a constru��o 
//				de campos de formul�rio a partir dos atributos definidos 
//				na especifica��o XML
// @package		php2go.form.field
// @uses		Form
// @uses		FormEventListener
// @uses		FormRule
// @uses		HtmlUtils
// @uses		HttpRequest
// @uses		Statement
// @extends		PHP2Go
// @author		Marcos Pont
// @version		$Revision: 1.39 $
//!-----------------------------------------------------------------
class FormField extends PHP2Go
{
	var $name;							// @var name string				Nome do campo
	var $focusName;						// @var focusName string		Utilizado para indicar para onde deve apontar o foco quando o r�tulo do campo � clicado. Assume o pr�prio nome do campo, a n�o ser em campos compostos ou que representam grupos
	var $label;							// @var label string			R�tulo do campo
	var $value = '';					// @var value mixed				"" Valor do campo
	var $fieldTag;						// @var fieldTag string			Nome da tag no arquivo XML
	var $htmlType;						// @var htmlType string			Tipo da tag INPUT constru�da
	var $htmlCode = '';					// @var htmlCode string			"" C�digo HTML do campo	
	var $attributes = array();			// @var attributes array		"array()" Vetor de atributos validados do campo
	var $rules = array();				// @var rules array				"array()" Regras de igualdade, desigualdade e obrigatoriedade condicional para o campo	
	var $listeners = array();			// @var listeners array			"array()" Conjunto de tratadores de eventos associados a este campo
	var $search = array();				// @var search array			"array()" Configura��es customizadas de pesquisa para este campo
	var $searchDefaults = array();		// @var searchDefaults array	"array()" Configura��es padr�o de pesquisa para este campo
	var $required = FALSE;				// @var required bool			"FALSE" Indica se o campo � obrigat�rio ou n�o
	var $disabled = FALSE;				// @var disabled bool			"FALSE" Indica se o campo est� desabilitado	
	var $child = FALSE;					// @var child bool				"FALSE" Indica que o campo � um membro de um campo composto (DataGrid, RangeField, ...)
	var $composite = FALSE;				// @var composite bool			"FALSE" Indica que � um campo composto
	var $searchable = TRUE;				// @var searchable bool			"TRUE" Se esta propriedade for FALSE, indica que o campo n�o � v�lido para um formul�rio de pesquisa
	var $processed = FALSE;				// @var processed bool			"FALSE" Indica que os listeners e as regras do campo j� foram processadas
	var $_Form = NULL;					// @var _Form Form object		Objeto Form no qual o campo ser� inclu�do
	
	//!-----------------------------------------------------------------
	// @function	FormField::FormField
	// @desc		Construtor da classe, inicializa os atributos b�sicos do campo
	// @access		public
	// @param		&Form Form object		Formul�rio onde o campo ser� inserido
	// @param		child bool				"FALSE" Se for TRUE, indica que o campo � membro de um campo composto
	//!-----------------------------------------------------------------
	function FormField(&$Form, $child=FALSE) {
		parent::PHP2Go();
		if ($this->isA('formfield', FALSE))
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_ABSTRACT_CLASS', 'FormField'), E_USER_ERROR, __FILE__, __LINE__);		
		$this->_Form =& $Form;
		$this->fieldTag = strtoupper($this->objectName);
		$this->searchDefaults = array(
			'FIELDTYPE' => $this->fieldTag,
			'OPERATOR' => 'CONTAINING',
			'DATATYPE' => 'STRING'
		);
		$this->child = $child;
		parent::registerDestructor($this, '_FormField');
	}
	
	//!-----------------------------------------------------------------
	// @function	FormField::_FormField
	// @desc		Destrutor do objeto
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function _FormField() {
		unset($this);
	}
	
	//!-----------------------------------------------------------------
	// @function	FormField::getName
	// @desc		Busca o nome do campo
	// @access		public
	// @return		string Nome do campo
	//!-----------------------------------------------------------------
	function getName() {
		return $this->name;
	}
	
	//!-----------------------------------------------------------------
	// @function	FormField::setName
	// @desc		Altera ou define o nome do campo
	// @access		public
	// @param		newName string		Novo nome para o campo
	// @return		void
	//!-----------------------------------------------------------------	
	function setName($newName) {
		$oldName = $this->name;
		$name = trim(TypeUtils::parseString($newName));
		if ($newName != '')
			$this->name = $newName;
		else
			$this->name = PHP2Go::generateUniqueId($this->objectName);
		$this->searchDefaults['ALIAS'] = $this->name;
		$this->_Form->verifyFieldName($this->_Form->formName, $this->name);
		if (!empty($oldName) && isset($this->_Form->fields[$oldName])) {
			unset($this->_Form->fields[$oldName]);
			$this->_Form->fields[$this->name] =& $this;
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	FormField::getLabel
	// @desc		Busca o r�tulo do campo
	// @access		public
	// @return		string R�tulo do campo
	//!-----------------------------------------------------------------	
	function getLabel() {
		return $this->label;
	}
	
	//!-----------------------------------------------------------------
	// @function	FormField::getLabelCode
	// @desc		M�todo respons�vel pela constru��o do c�digo HTML
	//				do r�tulo do campo, incluindo indicativo de obrigatoriedade
	// @access		public
	// @param		reqFlag bool	Exibir ou n�o indicativo de obrigatoriedade
	// @param		reqColor string	Cor do indicativo
	// @param		reqText string	Texto do indicativo
	// @return		string C�digo da tag LABEL - r�tulo do campo
	//!-----------------------------------------------------------------
	function getLabelCode($reqFlag, $reqColor, $reqText) {
		$label = $this->label;
		if ($label != 'empty') {
			$required = ($this->required && TypeUtils::toBoolean($reqFlag) ? "<SPAN STYLE=\"color:{$reqColor}\">{$reqText}</SPAN>" : '');
			return sprintf("<LABEL FOR=\"%s\" ID=\"%s\"%s>%s%s</LABEL>", 
					$this->focusName, $this->getName() . '_label',
					$this->_Form->getLabelStyle(), $label, $required
			);
		} else {
			return '';
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	FormField::setLabel
	// @desc		Altera ou define o r�tulo do campo
	// @param		label string		Novo r�tulo para o campo
	// @access		public	
	// @return		void
	//!-----------------------------------------------------------------	
	function setLabel($label) {
		$label = trim(TypeUtils::parseString($label));
		if ($label != '')
			$this->label = Form::resolveI18nEntry($label);
		else
			$this->label = $this->name;
	}
	
	//!-----------------------------------------------------------------
	// @function	FormField::getValue
	// @desc		Busca o valor atribu�do ao campo
	// @access		public
	// @return		mixed Valor do campo
	//!-----------------------------------------------------------------	
	function getValue() {
		return $this->value;
	}
	
	//!-----------------------------------------------------------------
	// @function	FormField::getSearchData
	// @desc		Retorna o conjunto de informa��es espec�ficas de busca
	//				para este campo: o valor submetido e os par�metros de configura��o
	//				(operador, tipo de dado, alias)
	// @access		public
	// @return		array Dados de busca deste campo
	//!-----------------------------------------------------------------
	function getSearchData() {
		if ($this->_Form->isPosted())
			$this->search['VALUE'] = @$this->_Form->submittedValues[$this->name];
		return array_merge($this->searchDefaults, $this->search);
	}
	
	//!-----------------------------------------------------------------
	// @function	FormField::setValue
	// @desc		Altera ou define valor para o campo
	// @access		public
	// @param		value mixed		Valor para o campo
	// @return		void
	//!-----------------------------------------------------------------
	function setValue($value) {
	
		if (!TypeUtils::isArray($value)) {

		
			(System::getIni('magic_quotes_gpc') == 1) && ($value = stripslashes($value));
		
			if ($value == 'empty')
			{
				$value = '';
			}
			elseif (ereg("~[^~]+~", $value))
			{
			 $value = Statement::evaluate($value);
			}
			
			
		}
		$this->value = $value;
	}
	
	//!-----------------------------------------------------------------
	// @function	FormField::setSubmittedValue
	// @desc		Adiciona um valor no conjunto de valores submetidos do formul�rio
	// @access		protected
	// @return		void
	//!-----------------------------------------------------------------
	function setSubmittedValue($value=NULL) {
		$sv =& $this->_Form->submittedValues;
		$value = TypeUtils::ifNull($value, $this->getValue());
		if (preg_match("/([^\[]+)\[([^\]]+)\]/", $this->name, $matches)) {			
			if (!isset($sv[$matches[1]]))
				$sv[$matches[1]] = array();
			$sv[$matches[1]][$matches[2]] = $value;
		} else {
			$filteredName = ereg_replace("\[\]$", "", $this->name);
			$sv[$filteredName] = $value;
		}
	}	
	
	//!-----------------------------------------------------------------
	// @function	FormField::getAttribute
	// @desc		Retorna o valor de um atributo do campo
	// @access		public
	// @param		name string	Nome do atributo buscado
	// @return		mixed Valor do atributo ou FALSE se ele n�o existir
	//!-----------------------------------------------------------------	
	function getAttribute($name) {
		return isset($this->attributes[$name]) ? $this->attributes[$name] : FALSE;
	}
	
	//!-----------------------------------------------------------------
	// @function	FormField::getFieldTag
	// @desc		Retorna o nome da tag do campo no arquivo XML
	// @access		public
	// @return		string Nome da tag
	//!-----------------------------------------------------------------	
	function getFieldTag() {
		return $this->fieldTag;
	}
	
	//!-----------------------------------------------------------------
	// @function	FormField::&getOwnerForm
	// @desc		Retorna o formul�rio no qual o campo est� inserido
	// @access		public
	// @return		Form object
	//!-----------------------------------------------------------------
	function &getOwnerForm() {
		return $this->_Form;
	}
	
	//!-----------------------------------------------------------------
	// @function	FormField::getHtmlType
	// @desc		Busca o tipo da tag INPUT que este campo representa
	// @access		public
	// @return		string Tipo da tag INPUT (TEXT, PASSWORD, ...)
	//!-----------------------------------------------------------------	
	function getHtmlType() {
		return $this->htmlType;
	}
	
	//!-----------------------------------------------------------------
	// @function	FormField::getCode
	// @desc		Implementa��o base do m�todo getCode. A responsabilidade de 
	//				constru��o do c�digo HTML � de cada uma das classes filhas
	// @access		public
	// @return		string
	//!-----------------------------------------------------------------
	function getCode() {
		return $this->htmlCode;
	}
	
	//!-----------------------------------------------------------------
	// @function	FormField::getHelpCode
	// @desc		M�todo respons�vel pela constru��o do c�digo HTML de apresenta��o
	//				do texto de ajuda atrelado ao campo, proveniente do atributo HELP
	//				da especifica��o XML
	// @access		public
	// @return		string C�digo HTML da ajuda
	//!-----------------------------------------------------------------
	function getHelpCode() {
		if ($this->attributes['HELP'] != '') {
			if ($this->_Form->helpOptions['mode'] == FORM_HELP_INLINE) {
				$style = (isset($this->_Form->helpOptions['text_style']) ? " CLASS=\"{$this->_Form->helpOptions['text_style']}\"" : $this->_Form->getLabelStyle());
				return sprintf("<DIV ID=\"%s\"%s>%s</DIV>", 
					$this->getName() . '_help',
					$style, $this->attributes['HELP']);
			} else {
				return sprintf("<IMG ID=\"%s\" SRC=\"%s\" ALT=\"\" BORDER=\"0\"%s/>",
					$this->getName() . '_help', $this->_Form->helpOptions['popup_icon'],
					' ' . HtmlUtils::overPopup($this->_Form->Document, $this->attributes['HELP'], $this->_Form->helpOptions['popup_attrs']));
			}
		}
		return '';
	}
	
	//!-----------------------------------------------------------------
	// @function	FormField::setHelp
	// @desc		Atribui um texto de ajuda ao campo
	// @param		help string		Texto de ajuda para o campo
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function setHelp($help) {
		$help = trim($help);
		if ($help != '')
			$this->attributes['HELP'] = Form::resolveI18nEntry($help);
		else
			$this->attributes['HELP'] = '';
	}
	
	//!-----------------------------------------------------------------
	// @function	FormField::setStyle
	// @desc		Altera o valor do estilo do campo
	// @param		style string	Estilo para o campo
	// @access		public	
	// @return		void
	// @note		Este m�todo permite customizar o estilo de um determinado
	//				campo em rela��o � configura��o global definida para todo
	//				o formul�rio
	//!-----------------------------------------------------------------
	function setStyle($style) {
		if (trim($style) == 'empty')
			$this->attributes['STYLE'] = " CLASS=\"\"";
		elseif (trim($style) != '')
			$this->attributes['STYLE'] = " CLASS=\"" . trim($style) . "\"";
		else
			$this->attributes['STYLE'] = $this->_Form->getInputStyle();
	}
	
	//!-----------------------------------------------------------------
	// @function	FormField::setTabIndex
	// @desc		Define o �ndice de tab order do campo
	// @access		public
	// @param		tabIndex int		�ndice para o campo
	// @return		void
	//!-----------------------------------------------------------------
	function setTabIndex($tabIndex) {
		if (TypeUtils::isInteger($tabIndex))
			$this->attributes['TABINDEX'] = " TABINDEX=\"$tabIndex\"";
		else
			$this->attributes['TABINDEX'] = '';
	}
	
	//!-----------------------------------------------------------------
	// @function	FormField::setDisabled
	// @desc		Altera o valor do atributo que desabilita o campo
	// @access		public
	// @param		setting bool	"TRUE" Indica desabilita��o ou habilita��o do campo
	// @return		void
	//!-----------------------------------------------------------------
	function setDisabled($setting=TRUE) {
		if (TypeUtils::isTrue($setting)) {
			$this->attributes['DISABLED'] = " DISABLED";
			$this->disabled = TRUE;
		} else {
			$this->attributes['DISABLED'] = "";
			$this->disabled = FALSE;
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	FormField::isRequired
	// @desc		Consulta se o campo � ou n�o obrigat�rio
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------	
	function isRequired() {
		return $this->required;
	}
	
	//!-----------------------------------------------------------------
	// @function	FormField::setRequired
	// @desc		Altera a obrigatoriedade do campo
	// @access		public
	// @param		setting bool		"TRUE" Valor para a obrigatoriedade
	// @return		void	
	//!-----------------------------------------------------------------	
	function setRequired($setting=TRUE) {
		$this->required = TypeUtils::toBoolean($setting);
		if ($this->required && !$this->_Form->hasRequired)
			$this->_Form->hasRequired = TRUE;		
	}
	
	//!-----------------------------------------------------------------
	// @function	FormField::addEventListener
	// @desc		Adiciona um novo tratador de eventos no campo
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
	// @function	FormField::addRule
	// @desc		Adiciona uma regra de valida��o para o campo
	// @access		public
	// @param		Rule FormRule object		Regra de valida��o
	// @return		void
	//!-----------------------------------------------------------------
	function addRule($Rule) {
		$Rule->setOwnerField($this);
		if ($Rule->isValid()) {
			$this->rules[] =& $Rule;
		}		
	}
	
	//!-----------------------------------------------------------------
	// @function	FormField::isValid
	// @desc		Aplica as valida��es de obrigatoriedade e de regras no campo
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function isValid() {
		$validators = array();
		if ($this->required && !$this->composite)
			$validators[] = array('php2go.validation.RequiredValidator', NULL, NULL);
		for ($i=0, $s=sizeof($this->rules); $i<$s; $i++) {
			$params = array();
			$params['rule'] =& $this->rules[$i];
			$validators[] = array('php2go.validation.RuleValidator', $params, $this->rules[$i]->getMessage());
		}
		$result = TRUE;
		foreach ($validators as $validator)
			$result &= Validator::validateField($this, $validator[0], $validator[1], $validator[2]);
		return TypeUtils::toBoolean($result);
	}
	
	//!-----------------------------------------------------------------
	// @function	FormField::onLoadNode
	// @desc		M�todo respons�vel por processar atributos e nodos filhos
	//				provenientes da especifica��o XML do campo
	// @access		protected
	// @param		attrs array		Atributos do nodo
	// @param		children array	Vetor de nodos filhos
	// @return		void
	//!-----------------------------------------------------------------
	function onLoadNode($attrs, $children) {


		// nome
		$this->setName(@$attrs['NAME']);
		$this->focusName = $this->name;
		// r�tulo
		$this->setLabel(@$attrs['LABEL']);
		// valor
		if (!$this->composite || $this->isA('datagrid')) {

			if ($this->_Form->isPosted()) {

		
				// 1) valor submetido
				$submittedValue = HttpRequest::getVar($this->name, $this->_Form->formMethod);
				if (!TypeUtils::isNull($submittedValue)) {
					$this->setValue($submittedValue);
					$this->setSubmittedValue();					
				}
				// 2) atributo VALUE - valor est�tico
				elseif (isset($attrs['VALUE'])) {
					$this->setValue($attrs['VALUE']);
				} 
				// 3) atributo DEFAULT
				elseif (isset($attrs['DEFAULT'])) {				
					$this->setValue($attrs['DEFAULT']);
				}
			} else {
			
				// 1) atributo VALUE - valor est�tico
				if (isset($attrs['VALUE'])) {
					$this->setValue($attrs['VALUE']);
				} else {
				
					// 2) valor da requisi��o
					$requestValue = HttpRequest::getVar($this->name);
                 
                 	if (!TypeUtils::isNull($requestValue))
						$this->setValue($requestValue);
					// 3) atributo DEFAULT
					elseif (isset($attrs['DEFAULT']))
						$this->setValue($attrs['DEFAULT']);
				}
			}
		}
		// texto de ajuda
		$this->setHelp(@$attrs['HELP']);
		// classe CSS
		$this->setStyle(@$attrs['STYLE']);
		// tab index
		$this->setTabIndex(@$attrs['TABINDEX']);
		// status
		$this->setDisabled(Form::resolveBooleanChoice(@$attrs['DISABLED']) || $this->_Form->isA('formdatabind') || $this->_Form->readonly);
		// obrigatoriedade
		$this->setRequired(Form::resolveBooleanChoice(@$attrs['REQUIRED']));
		// tratadores de eventos
		if (isset($children['LISTENER']) && !$this->composite) {
			$listeners = TypeUtils::toArray($children['LISTENER']);
			foreach ($listeners as $listenerNode)
				$this->addEventListener(FormEventListener::fromNode($listenerNode));	
		}
		// regras de valida��o
		if (isset($children['RULE']) && !$this->composite) {
			$rules = TypeUtils::toArray($children['RULE']);
			foreach ($rules as $ruleNode)
				$this->addRule(FormRule::fromNode($ruleNode));	
		}
		// configura��es de busca, utilizadas pela classe php2go.form.SearchForm
		if (!$this->child && isset($children['SEARCH'])) {
			$this->search = TypeUtils::toArray(@$children['SEARCH']->getAttributes());
		}
		// atributos de data bind
		if ($this->_Form->isA('formdatabind') && !$this->composite) {
			$this->attributes['DATASRC'] = " DATASRC=\"#" . $this->_Form->csvDbName . "\"";
			$this->attributes['DATAFLD'] = " DATAFLD=\"" . $this->name . "\"";
		} else {
			$this->attributes['DATASRC'] = '';
			$this->attributes['DATAFLD'] = '';
		}		
	}
	
	//!-----------------------------------------------------------------
	// @function	FormField::onPreRender
	// @desc		Constr�i e adiciona no formul�rio o c�digo JavaScript de
	//				controle de obrigratoriedade e o c�digo gerado pelas regras
	//				de valida��o associadas ao campo
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function onPreRender() {
		// adiciona script para controle de obrigatoriedade
		if ($this->required)
			$this->_Form->appendScript(sprintf("          validator.addRequiredField(\"%s\", \"%s\");\n", $this->name, $this->getLabel()));
		if (!$this->processed) {
			// constr�i e adiciona o c�digo das regras
			if (!empty($this->rules)) {
				foreach ($this->rules as $Rule) {
					if (TypeUtils::isInstanceOf($Rule, 'formrule'))
						$this->_Form->appendScript($Rule->getScriptCode());
				}				
			}
			// executa a fun��o de constru��o do atributo SCRIPT (tratadores de eventos)
			$this->renderListeners();
			$this->processed = TRUE;
		}		
	}
	
	//!-----------------------------------------------------------------
	// @function	FormField::renderListeners
	// @desc		Constr�i o c�digo JavaScript para as chamadas definidas
	//				nos tratadores de eventos do campo
	// @access		protected
	// @return		void	
	//!-----------------------------------------------------------------
	function renderListeners() {
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
}
?>