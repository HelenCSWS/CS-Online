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
// $Header: /www/cvsroot/php2go/core/form/field/DbRadioField.class.php,v 1.18 2005/08/30 14:01:04 mpont Exp $
// $Date: 2005/08/30 14:01:04 $

//------------------------------------------------------------------
import('php2go.form.field.DbField');
import('php2go.template.Template');
//------------------------------------------------------------------

//!-----------------------------------------------------------------
// @class		DbRadioField
// @desc		Classe que constrói um grupo de campos do tipo RADIO BUTTON
// @package		php2go.form.field
// @uses		Template
// @uses		TypeUtils
// @extends		DbField
// @author		Marcos Pont
// @version		$Revision: 1.18 $
//!-----------------------------------------------------------------
class DbRadioField extends DbField
{
	var $optionCount;	// @var optionCount int	Total de opções do grupo radio

	//!-----------------------------------------------------------------
	// @function	DbRadioField::DbRadioField
	// @desc		Construtor da classe, inicializa os atributos básicos do campo
	// @access		public
	// @param		&Form Form object		Formulário no qual o campo é inserido
	// @param		child bool				"FALSE" Se for TRUE, indica que o campo é membro de um campo composto
	//!-----------------------------------------------------------------
	function DbRadioField(&$Form, $child=FALSE) {
		parent::DbField($Form, $child);
		$this->htmlType = 'RADIO';
		$this->searchDefaults['OPERATOR'] = 'EQ';		
	}
	
	//!-----------------------------------------------------------------
	// @function	DbRadioField::getCode
	// @desc		Monta o código HTML do campo
	// @access		public
	// @return		string Código HTML do campo
	//!-----------------------------------------------------------------
	function getCode() {
		$count = 1;		
		$this->onPreRender();
		$Tpl = new Template(PHP2GO_TEMPLATE_PATH . 'radiofield.tpl');
		$Tpl->parse();
		$Tpl->assign('width', $this->attributes['TABLEWIDTH']);
		$Tpl->createBlock('new_row');
		while (list($value, $caption) = $this->_Rs->fetchRow()) {
			$i = ($this->_Rs->absolutePosition() - 1);
			$optionSelected = ($value == $this->value ? ' CHECKED' : '');
			$Tpl->createBlock('radio_button');
			$Tpl->assign('input', sprintf("<INPUT TYPE=\"radio\" ID=\"%s\" NAME=\"%s\" VALUE=\"%s\" TITLE=\"%s\"%s%s%s%s%s%s%s>",
				"{$this->name}_{$i}", $this->name, $value, $this->label, $this->attributes['TABINDEX'], $this->attributes['SCRIPT'], $this->attributes['STYLE'], 
				$this->attributes['DISABLED'], $this->attributes['DATASRC'], $this->attributes['DATAFLD'], $optionSelected));
			$Tpl->assign('name', "{$this->name}_{$i}");
			$Tpl->assign('style', $this->_Form->getLabelStyle());
			$Tpl->assign('caption', $caption);
			if ($count == $this->attributes['COLS'] && $count < $this->optionCount) {
				$Tpl->createBlock('new_row');
				$count = 0;
			}
			$count++;
		}
		$this->htmlCode = $Tpl->getContent();
		return $this->htmlCode;
	}
	
	//!-----------------------------------------------------------------
	// @function	DbRadioField::setCols
	// @desc		Seta o número de colunas da tabela que contém os campos RADIO,
	//				definindo assim quantos elementos devem ser exibidos por linha
	// @access		public
	// @param		cols int	Número de colunas ou campos por linha
	// @return		void
	//!-----------------------------------------------------------------
	function setCols($cols) {
		$this->attributes['COLS'] = max(1, $cols);
	}
	
	//!-----------------------------------------------------------------
	// @function	DbRadioField::setTableWidth
	// @desc		Seta o tamanho (valor para o atributo WIDTH) da tabela
	//				construída para o grupo de campos RADIO
	// @access		public
	// @param		tableWidth mixed	Tamanho da tabela
	// @return		void
	//!-----------------------------------------------------------------
	function setTableWidth($tableWidth) {
		if ($tableWidth)
			$this->attributes['TABLEWIDTH'] = " WIDTH=\"" . $tableWidth . "\"";
		else			
			$this->attributes['TABLEWIDTH'] = "";
	}
	
	//!-----------------------------------------------------------------
	// @function	DbRadioField::onLoadNode
	// @desc		Método responsável por processar atributos e nodos filhos
	//				provenientes da especificação XML do campo
	// @access		protected
	// @param		attrs array		Atributos do nodo
	// @param		children array	Vetor de nodos filhos
	// @return		void
	//!-----------------------------------------------------------------
	function onLoadNode($attrs, $children) {
		parent::onLoadNode($attrs, $children);
		// número de colunas
		$this->setCols(@$attrs['COLS']);
		// largura da tabela
		$this->setTableWidth(@$attrs['TABLEWIDTH']);
		// datasource obrigatório
		if (empty($this->dataSource))
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_MISSING_DBRADIOFIELD_DATASOURCE', $this->name), E_USER_ERROR, __FILE__, __LINE__);		
	}
	
	//!-----------------------------------------------------------------
	// @function	DbRadioField::onPreRender
	// @desc		Monta o conjunto de opções a partir do banco de dados
	//				e realiza validação de existência de ao menos uma opção resultante
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function onPreRender() {
		parent::onPreRender();
		parent::processDbQuery(ADODB_FETCH_NUM);
		$this->focusName = "{$this->name}_0";
		$this->optionCount = $this->_Rs->recordCount();
		if ($this->optionCount == 0)
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_EMPTY_DBRADIOFIELD_RESULTS', $this->name), E_USER_ERROR, __FILE__, __LINE__);		
	}
}
?>