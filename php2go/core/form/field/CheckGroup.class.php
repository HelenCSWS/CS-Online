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
// $Header: /www/cvsroot/php2go/core/form/field/CheckGroup.class.php,v 1.8 2005/08/30 14:31:13 mpont Exp $
// $Date: 2005/08/30 14:31:13 $

//------------------------------------------------------------------
import('php2go.form.field.GroupField');
import('php2go.template.Template');
//------------------------------------------------------------------

//!-----------------------------------------------------------------
// @class		CheckGroup
// @desc		Classe que constrói um grupo de campos do tipo CHECKBOX, com controle
//				de obrigatoriedade de seleção de um dos itens, pelo menos
// @package		php2go.form.field
// @extends		GroupField
// @uses		TypeUtils
// @uses		Template
// @author		Marcos Pont
// @version		$Revision: 1.8 $
//!-----------------------------------------------------------------
class CheckGroup extends GroupField
{
	//!-----------------------------------------------------------------
	// @function	CheckGroup::CheckGroup
	// @desc		Construtor da classe, inicializa os atributos básicos do campo
	// @access		public
	// @param		&Form Form object	Formulário no qual o campo é inserido
	// @param		child bool			"FALSE" Se for TRUE, indica que o campo é membro de um campo composto
	//!-----------------------------------------------------------------
	function CheckGroup(&$Form, $child=FALSE) {
		parent::GroupField($Form, $child);
		$this->htmlType = 'CHECKBOX';
		$this->searchDefaults['OPERATOR'] = 'IN';
	}
	
	//!-----------------------------------------------------------------
	// @function	CheckGroup::getCode
	// @desc		Monta o código HTML do campo
	// @access		public
	// @return		string Código HTML do campo
	//!-----------------------------------------------------------------
	function getCode() {
		$this->onPreRender();
		$Tpl = new Template(PHP2GO_TEMPLATE_PATH . 'checkgroup.tpl');		
		$Tpl->parse();
		$Tpl->assign('width', $this->attributes['TABLEWIDTH']);	
		$Tpl->createBlock('new_row');		
		$hasValue = (!empty($this->value));
		$arrayValue = (TypeUtils::isArray($this->value));
		$count = 1;
		for ($i=0, $s=$this->optionCount; $i<$s; $i++) {
			if ($hasValue) {
				if ($arrayValue)
					$this->optionAttributes[$i]['SELECTED'] = (in_array($this->optionAttributes[$i]['VALUE'], $this->value) ? ' CHECKED' : '');
				else
					$this->optionAttributes[$i]['SELECTED'] = (!strcasecmp($this->optionAttributes[$i]['VALUE'], $this->value) ? ' CHECKED' : '');
			} else {
				$this->optionAttributes[$i]['SELECTED'] = '';
			}
			$Tpl->createBlock('checkbox');
			$Tpl->assign('input', sprintf("<INPUT TYPE=\"checkbox\" ID=\"%s\" NAME=\"%s\" TITLE=\"%s\" %s%s%s%s%s%s%s>",
				substr($this->name, 0, -2) . "_$i", $this->name, $this->label, $this->attributes['TABINDEX'], 
				$this->optionAttributes[$i]['SCRIPT'], $this->attributes['STYLE'], $this->optionAttributes[$i]['DISABLED'],
				$this->attributes['DATASRC'], $this->attributes['DATAFLD'], $this->optionAttributes[$i]['SELECTED']));
			$Tpl->assign('name', substr($this->name, 0, -2) . "_$i");
			$Tpl->assign('style', $this->_Form->getLabelStyle());
			$Tpl->assign('caption', $this->optionAttributes[$i]['CAPTION']);
			if (!empty($this->optionAttributes[$i]['ALT']))
				$Tpl->assign('alt', " title=\"{$this->optionAttributes[$i]['ALT']}\"");
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
	// @function	CheckGroup::onPreRender
	// @desc		Configura o nome do campo para incluir o sufixo "[]",
	//				que garante a submissão do valor do campo como um array
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function onPreRender() {
		$this->name .= '[]';
		parent::onPreRender();
	}
}
?>