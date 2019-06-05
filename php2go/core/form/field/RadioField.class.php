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
// $Header: /www/cvsroot/php2go/core/form/field/RadioField.class.php,v 1.16 2005/08/30 14:32:54 mpont Exp $
// $Date: 2005/08/30 14:32:54 $

//------------------------------------------------------------------
import('php2go.form.field.GroupField');
import('php2go.template.Template');
//------------------------------------------------------------------

//!-----------------------------------------------------------------
// @class		RadioField
// @desc		Classe que constrói um grupo de campos do tipo RADIO BUTTON
// @package		php2go.form.field
// @extends		GroupField
// @uses		TypeUtils
// @uses		Template
// @author		Marcos Pont
// @version		$Revision: 1.16 $
//!-----------------------------------------------------------------
class RadioField extends GroupField
{
	//!-----------------------------------------------------------------
	// @function	RadioField::RadioField
	// @desc		Construtor da classe, inicializa os atributos básicos do campo
	// @access		public
	// @param		&Form Form object	Formulário no qual o campo é inserido
	// @param		child bool			"FALSE" Se for TRUE, indica que o campo é membro de um campo composto
	//!-----------------------------------------------------------------
	function RadioField(&$Form, $child=FALSE) {
		parent::GroupField($Form, $child);
		$this->htmlType = 'RADIO';
		$this->searchDefaults['OPERATOR'] = 'EQ';
	}
	
	//!-----------------------------------------------------------------
	// @function	RadioField::getCode
	// @desc		Monta o código HTML do campo
	// @access		public
	// @return		string Código HTML do campo
	//!-----------------------------------------------------------------
	function getCode() {
		parent::onPreRender();		
		$Tpl =& new Template(PHP2GO_TEMPLATE_PATH . 'radiofield.tpl');		
		$Tpl->parse();
		$Tpl->assign('width', $this->attributes['TABLEWIDTH']);	
		$Tpl->createBlock('new_row');
		$count = 1;
		for ($i=0; $i<$this->getOptionCount(); $i++) {
			if ($this->optionAttributes[$i]['VALUE'] == parent::getValue())
				$this->optionAttributes[$i]['SELECTED'] = " CHECKED";
			else
				$this->optionAttributes[$i]['SELECTED'] = "";
			$Tpl->createBlock('radio_button');
			$Tpl->assign('input', sprintf("<INPUT TYPE=\"radio\" ID=\"%s\" NAME=\"%s\" VALUE=\"%s\" TITLE=\"%s\"%s%s%s%s%s%s%s>",
				$this->name . "_$i", $this->name, $this->optionAttributes[$i]['VALUE'], $this->label, $this->attributes['TABINDEX'], 
				$this->optionAttributes[$i]['SCRIPT'], $this->attributes['STYLE'], $this->optionAttributes[$i]['DISABLED'],
				$this->attributes['DATASRC'], $this->attributes['DATAFLD'], $this->optionAttributes[$i]['SELECTED']));
			$Tpl->assign('name', $this->name . "_$i");
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
}
?>