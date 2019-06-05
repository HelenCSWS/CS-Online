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
// $Header: /www/cvsroot/php2go/core/form/field/LookupField.class.php,v 1.21 2005/07/25 13:35:02 mpont Exp $
// $Date: 2005/07/25 13:35:02 $

//------------------------------------------------------------------
import('php2go.form.field.DbField');
//------------------------------------------------------------------

//!-----------------------------------------------------------------
// @class		LookupField
// @desc		A classe LookupField monta campos de seleção de valores
//				provenientes de uma base de dados. A especificação do
//				elemento DATASOURCE define os elementos da consulta SQL
// @package		php2go.form.field
// @uses		TypeUtils
// @extends		DbField
// @author		Marcos Pont
// @version		$Revision: 1.21 $
//!-----------------------------------------------------------------
class LookupField extends DbField
{
	var $optionCount = 0;	// @var optionCount integer		Total de opções do campo
	
	//!-----------------------------------------------------------------
	// @function	LookupField::LookupField
	// @desc		Construtor da classe LookupField
	// @access		public
	// @param		&Form Form object	Formulário no qual o campo é inserido
	// @param		child bool			"FALSE" Se for TRUE, indica que o campo é membro de um campo composto
	//!-----------------------------------------------------------------
	function LookupField(&$Form, $child=FALSE) {
		parent::DbField($Form, $child);
		$this->htmlType = 'SELECT';
		$this->searchDefaults['OPERATOR'] = 'EQ';
	}
	
	//!-----------------------------------------------------------------
	// @function	LookupField::getCode
	// @desc		Monta o código HTML do campo
	// @access		public
	// @return		string Código HTML do campo
	//!-----------------------------------------------------------------
	function getCode() {
		$this->onPreRender();
		// código do campo SELECT
		$this->htmlCode = sprintf("<SELECT ID=\"%s\" NAME=\"%s%s\" TITLE=\"%s\"%s%s%s%s%s%s%s%s%s>\n",
			$this->name, $this->name, $this->attributes['CLASPS'], $this->label, $this->attributes['SCRIPT'], 
			$this->attributes['TABINDEX'], $this->attributes['STYLE'], $this->attributes['MULTIPLE'], 
			$this->attributes['SIZE'], $this->attributes['WIDTH'], $this->attributes['DISABLED'], 
			$this->attributes['DATASRC'], $this->attributes['DATAFLD']);
		// primeira opção
		if ($this->attributes['NOFIRST'] != 'T')
			$this->htmlCode .= sprintf("<OPTION VALUE=\"\">%s</OPTION>\n", $this->attributes['FIRST']);
		// código das opções da lista de seleção			
		if ($this->_Rs->recordCount() > 0) {
			$this->optionCount = $this->_Rs->RecordCount();
			$hasValue = (!empty($this->value));
			$arrayValue = (TypeUtils::isArray($this->value));
			if ($this->isGrouping) {
				$groupVal = '';
				while (list($key, $display, $group, $groupDisplay) = $this->_Rs->fetchRow()) {
					if (strcasecmp($group, $groupVal)) {
						if ($groupVal != '')
							$this->htmlCode .= "</OPTGROUP>\n";
						$this->htmlCode .= sprintf("<OPTGROUP LABEL=\"%s\">\n", $groupDisplay);
					}
					if ($hasValue) {
						if ($arrayValue)
							$optionSelected = in_array($key, $this->value) ? ' SELECTED' : '';
						else
							$optionSelected = !strcasecmp($key, $this->value) ? ' SELECTED' : '';
					} else {
						$optionSelected = '';
					}
					$this->htmlCode .= sprintf("<OPTION VALUE=\"%s\"%s>%s</OPTION>\n", 
						$key, $optionSelected, $display);
					$groupVal = $group;
				}
				$this->htmlCode .= "</OPTGROUP>\n";
			} else {
				while (list($key, $display) = $this->_Rs->fetchRow()) {
					if ($hasValue) {
						if ($arrayValue)
							$optionSelected = in_array($key, $this->value) ? ' SELECTED' : '';
						else
							$optionSelected = !strcasecmp($key, $this->value) ? ' SELECTED' : '';
					} else {
						$optionSelected = '';
					}
					$this->htmlCode .= sprintf("<OPTION VALUE=\"%s\"%s>%s</OPTION>\n", 
						$key, $optionSelected, $display);
				}
			}
		}
		$this->htmlCode .= "</SELECT>\n";
		return $this->htmlCode;
	}
	
	//!-----------------------------------------------------------------
	// @function	LookupField::getOptionCount
	// @desc		Retorna o total de opções do campo, baseado no total de registros
	//				retornados da consulta realizada
	// @access		public
	// @return		int Total de opções disponíveis
	//!-----------------------------------------------------------------
	function getOptionCount() {
		return $this->optionCount;
	}
	
	//!-----------------------------------------------------------------
	// @function	LookupField::setFirstOption
	// @desc		Os campos do tipo LookupField possuem por padrão
	//				uma primeira opção em branco não selecionável na lista
	//				de opções. Este método permite definir um texto para este item
	// @access		public
	// @param		first string	Texto para a primeira opção
	// @return		void
	//!-----------------------------------------------------------------
	function setFirstOption($first) {
		if ($first)
			$this->attributes['FIRST'] = Form::resolveI18nEntry($first);
		else
			$this->attributes['FIRST'] = "";
	}
	
	//!-----------------------------------------------------------------
	// @function	LookupField::disableFirstOption
	// @desc		Desabilita ou habilita a inserção de uma primeira opção
	//				em branco na lista de opções
	// @access		public
	// @param		setting bool	"TRUE" Valor para o atributo
	// @return		void
	//!-----------------------------------------------------------------
	function disableFirstOption($setting=TRUE) {
		if (TypeUtils::isTrue($setting))
			$this->attributes['NOFIRST'] = 'T';
		else
			$this->attributes['NOFIRST'] = 'F';
	}
	
	//!-----------------------------------------------------------------
	// @function	LookupField::setMultiple
	// @desc		Habilita ou desabilita a possibilidade de seleção de múltiplas opções na lista
	// @access		public
	// @param		setting bool	"TRUE" Valor para o atributo
	// @return		void
	//!-----------------------------------------------------------------
	function setMultiple($setting=TRUE) {
		if (TypeUtils::isTrue($setting)) {
			$this->attributes['MULTIPLE'] = " MULTIPLE";
			$this->attributes['CLASPS'] = "[]";
			$this->searchDefaults['OPERATOR'] = 'IN';
		} else {
			$this->attributes['MULTIPLE'] = "";
			$this->attributes['CLASPS'] = "";
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	LookupField::setSize
	// @desc		O atributo SIZE de um campo do tipo SELECT define o número
	//				de opções exibidas na construção do campo, ou seja, a altura
	//				do campo em número de linhas
	// @access		public
	// @param		size int	Tamanho (número de linhas exibidas) para o campo
	// @return		void
	//!-----------------------------------------------------------------
	function setSize($size) {
		if (TypeUtils::isInteger($size))
			$this->attributes['SIZE'] = " SIZE=\"{$size}\"";
		else
			$this->attributes['SIZE'] = "";
	}
	
	//!-----------------------------------------------------------------
	// @function	LookupField::setWidth
	// @desc		Define a largura da lista de opções, em pixels
	// @access		public
	// @param		width int	Largura para o campo
	// @return		void
	//!-----------------------------------------------------------------
	function setWidth($width) {
		if (TypeUtils::isInteger($width))
			$this->attributes['WIDTH'] = " STYLE=\"width:{$width}px\"";
		else
			$this->attributes['WIDTH'] = "";
	}
	
	//!-----------------------------------------------------------------
	// @function	LookupField::onLoadNode
	// @desc		Método responsável por processar atributos e nodos filhos
	//				provenientes da especificação XML do campo
	// @access		protected
	// @param		attrs array		Atributos do nodo
	// @param		children array	Vetor de nodos filhos
	// @return		void
	//!-----------------------------------------------------------------
	function onLoadNode($attrs, $children) {
		parent::onLoadNode($attrs, $children);
		// texto da primeira opção
		$this->setFirstOption(@$attrs['FIRST']);
		// primeira opção (vazia ou não) desabilitada
		$this->disableFirstOption(Form::resolveBooleanChoice(@$attrs['NOFIRST']));
		// escolha múltipla
		$size = @$attrs['SIZE'];
		$this->setMultiple(Form::resolveBooleanChoice(@$attrs['MULTIPLE']) && TypeUtils::isInteger($size));
		// tamanho
		$this->setSize($size);
		// largura em pixels
		$this->setWidth(@$attrs['WIDTH']);
	}
	
	//!-----------------------------------------------------------------
	// @function	LookupField::onPreRender
	// @desc		Executa a consulta ao banco para montar o 
	//				conjunto de opções para a lista de seleção
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function onPreRender() {
		parent::processDbQuery(ADODB_FETCH_NUM);
		parent::onPreRender();
	}	
}
?>