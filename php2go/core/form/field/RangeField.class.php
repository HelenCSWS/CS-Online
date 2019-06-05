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
// $Header: /www/cvsroot/php2go/core/form/field/RangeField.class.php,v 1.6 2005/07/25 13:35:02 mpont Exp $
// $Date: 2005/07/25 13:35:02 $

//------------------------------------------------------------------
import('php2go.form.field.EditField');
//------------------------------------------------------------------

//!-----------------------------------------------------------------
// @class		RangeField
// @desc		Classe responsável pela construção de um par de elementos
//				INPUT do tipo TEXT, formando um intervalo de valores. A classe
//				também oferece facilidades para inserção de texto envolvendo
//				o código dos campos e inserção de regra de comparação
// @package		php2go.form.field
// @uses		EditField
// @uses		TypeUtils
// @extends		FormField
// @author		Marcos Pont
// @version		$Revision: 1.6 $	
//!-----------------------------------------------------------------
class RangeField extends FormField
{
	var $_StartEdit = NULL;		// @var _StartEdit EditField object		"NULL" Representa o campo TEXT com o valor inicial do intervalo
	var $_EndEdit = NULL;		// @var _EndEdit EditField object		"NULL" Representa o campo TEXT com o valor final do intervalo
	
	//!-----------------------------------------------------------------
	// @function	RangeField::RangeField
	// @desc		Construtor da classe
	// @access		public
	// @param		&Form Form object	Formulário no qual o campo é inserido	
	//!-----------------------------------------------------------------
	function RangeField(&$Form) {
		parent::FormField($Form);
		$this->composite = TRUE;
		$this->searchDefaults['OPERATOR'] = 'BETWEEN';
	}
	
	//!-----------------------------------------------------------------
	// @function	RangeField::getCode
	// @desc		Monta o código HTML do campo
	// @access		public
	// @return		string Código HTML do campo	
	//!-----------------------------------------------------------------	
	function getCode() {
		$this->onPreRender();
		if (isset($this->attributes['SURROUNDTEXT'])) {
			$this->htmlCode = sprintf("<SPAN ID=\"%s\"%s%s>%s</SPAN>",
				$this->name, $this->attributes['STYLE'], $this->attributes['TABINDEX'], 
				sprintf($this->attributes['SURROUNDTEXT'], $this->_StartEdit->getCode(), $this->_EndEdit->getCode())
			);
		} else {
			$this->htmlCode = sprintf("<SPAN ID=\"%s\"%s>%s&nbsp;%s</SPAN>",
				$this->name, $this->attributes['TABINDEX'], $this->_StartEdit->getCode(), $this->_EndEdit->getCode()
			);
		}
		return $this->htmlCode;
	}
	
	//!-----------------------------------------------------------------	
	// @function	RangeField::&getStartEdit
	// @desc		Retorna uma referência para o EDITFIELD do valor inicial do intervalo
	// @access		public
	// @return		EditField object
	//!-----------------------------------------------------------------
	function &getStartEdit() {
		if (isset($this->_StartEdit) && TypeUtils::isInstanceOf($this->_StartEdit, 'editfield'))
			return $this->_StartEdit;
		return NULL;
	}
	
	//!-----------------------------------------------------------------
	// @function	RangeField::&getEndEdit
	// @desc		Retorna uma referência para o EDITFIELD do valor final do intervalo
	// @access		public
	// @return		EditField object
	//!-----------------------------------------------------------------
	function &getEndEdit() {
		if (isset($this->_EndEdit) && TypeUtils::isInstanceOf($this->_EndEdit, 'editfield'))
			return $this->_EndEdit;
		return NULL;
	}
	
	//!-----------------------------------------------------------------
	// @function	RangeField::getSearchData
	// @desc		Sobrescreve a implementação da classe superior para que as
	//				máscaras definidas nos campos filhos sejam utilizadas como
	//				DATATYPE de pesquisa
	// @access		public
	// @return		array Dados específicos de busca para este campo
	//!-----------------------------------------------------------------
	function getSearchData() {
		$searchData = parent::getSearchData();
		$bottomMask = $this->_StartEdit->getMask();
		$topMask = $this->_StartEdit->getMask();
		if ($bottomMask == $topMask) {
			if ($bottomMask != 'DATE' || $searchData['DATATYPE'] != 'DATETIME')
				$searchData['DATATYPE'] = $bottomMask;
		}
		return $searchData;
	}
	
	//!-----------------------------------------------------------------
	// @function	RangeField::setSurroundText
	// @desc		Permite definir o texto que circunda os dois campos do intervalo. Um exemplo
	//				deste texto em português poderia ser: "Entre %s e %s". Os dois pontos de
	//				substituição são obrigatórios
	// @access		public
	// @param		text string		Texto a ser utilizado
	// @return		void
	//!-----------------------------------------------------------------
	function setSurroundText($text) {
		if (!empty($text))
			$this->attributes['SURROUNDTEXT'] = Form::resolveI18nEntry($text);
	}
	
	//!-----------------------------------------------------------------
	// @function	RangeField::onLoadNode
	// @desc		Método responsável por processar atributos e nodos filhos
	//				provenientes da especificação XML do campo
	// @access		protected
	// @param		attrs array		Atributos do nodo
	// @param		children array	Vetor de nodos filhos
	// @return		void
	//!-----------------------------------------------------------------
	function onLoadNode($attrs, $children) {
		parent::onLoadNode($attrs, $children);
		// construção dos 2 campos EDITFIELD
		if (!empty($children)) {
			foreach ($children as $key => $value) {
				if ($key == 'EDITFIELD') {
					if (!TypeUtils::isArray($value) || sizeof($value) != 2) {
						PHP2Go::raiseError(PHP2Go::getLangVal('ERR_MISSING_RANGEFIELD_CHILDREN', $this->name), E_USER_ERROR, __FILE__, __LINE__);
					} else {
						$start = @$attrs['STARTNAME'];
						$end = @$attrs['ENDNAME'];
						if (!$start || !$end || $start == $end) {
							$start = 'start';
							$end = 'end';
						}							
						$value[0]->setAttribute('NAME', "{$this->name}[{$start}]");
						if (!$value[0]->hasAttribute('LABEL'))
							$value[0]->setAttribute('LABEL', $this->label . ' (' . ucfirst(strtolower($start)) . ')');
						$value[1]->setAttribute('NAME', "{$this->name}[{$end}]");
						if (!$value[1]->hasAttribute('LABEL'))
							$value[1]->setAttribute('LABEL', $this->label . ' (' . ucfirst(strtolower($end)) . ')');
						$this->_StartEdit =& new EditField($this->_Form, TRUE);
						$this->_StartEdit->onLoadNode($value[0]->getAttributes(), $value[0]->getChildrenTagsArray());
						$this->_StartEdit->setRequired($this->required);
						$this->_Form->fields[$this->_StartEdit->getName()] =& $this->_StartEdit;
						$this->_EndEdit =& new EditField($this->_Form, TRUE);
						$this->_EndEdit->onLoadNode($value[1]->getAttributes(), $value[1]->getChildrenTagsArray());
						$this->_EndEdit->setRequired($this->required);
						$this->_Form->fields[$this->_EndEdit->getName()] =& $this->_EndEdit;
					}
				}
			}			
		} else {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_MISSING_RANGEFIELD_CHILDREN', $this->name), E_USER_ERROR, __FILE__, __LINE__);
		}
		// texto envolvendo os 2 campos EDITFIELD
		$this->setSurroundText(@$attrs['SURROUNDTEXT']);
		// inclusão da regra de validação
		$type = Form::resolveBooleanChoice(@$attrs['RULEEQUAL']);
		if (isset($attrs['RULEMESSAGE']))
			$attrs['RULEMESSAGE'] = Form::resolveI18nEntry($attrs['RULEMESSAGE']);
		$this->_EndEdit->addRule(new FormRule(
			($type ? 'GOET' : 'GT'), $this->_StartEdit->getName(),
			NULL, $this->_StartEdit->getMask(), @$attrs['RULEMESSAGE']
		));		
	}
	
	//!-----------------------------------------------------------------
	// @function	RangeField::onPreRender
	// @desc		Executa configurações necessárias antes da construção
	//				do código HTML final do campo
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function onPreRender() {
		// nome de foco
		$this->focusName = $this->_StartEdit->getName();
		// propagação dos atributos DISABLED e READONLY
		if ($this->disabled) {
			$this->_StartEdit->setDisabled();
			$this->_EndEdit->setDisabled();
		}
		$readOnly = Form::resolveBooleanChoice(@$attrs['READONLY']);
		$this->_StartEdit->setReadonly($readOnly);
		$this->_EndEdit->setReadonly($readOnly);
		// se não foi definido um estilo, utiliza estilo de rótulos do formulário
		if (!isset($this->attributes['STYLE']))
			$this->attributes['STYLE'] = $this->_Form->getLabelStyle();		
	}
}
?>