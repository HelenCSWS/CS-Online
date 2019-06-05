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
// $Header: /www/cvsroot/php2go/core/form/field/LookupChoiceField.class.php,v 1.13 2005/06/17 19:54:58 mpont Exp $
// $Date: 2005/06/17 19:54:58 $

//------------------------------------------------------------------
import('php2go.form.field.LookupField');
//------------------------------------------------------------------

//!-----------------------------------------------------------------
// @class		LookupChoiceField
// @desc		Esta classe extende a funcionalidade implementada pela
//				classe LookupField incluindo um campo texto que permite
//				filtrar a lista de valores à medida que os caracteres
//				são digitados
// @package		php2go.form.field
// @extends		LookupField
// @author		Marcos Pont
// @version		$Revision: 1.13 $
//!-----------------------------------------------------------------
class LookupChoiceField extends LookupField
{
	//!-----------------------------------------------------------------
	// @function	LookupChoiceField::LookupChoiceField
	// @desc		Construtor da classe LookupChoiceField
	// @access		public
	// @param		&Form Form object	Formulário no qual o campo é inserido
	//!-----------------------------------------------------------------
	function LookupChoiceField(&$Form) {
		parent::LookupField($Form);
	}
	
	//!-----------------------------------------------------------------
	// @function	LookupChoiceField::getCode
	// @desc		Monta o código HTML do campo
	// @access		public
	// @return		string Código HTML do campo
	//!-----------------------------------------------------------------
	function getCode() {
		$this->_Form->Document->addScript(PHP2GO_JAVASCRIPT_PATH . "modules/lookupchoice.js");		
		parent::getCode();		
		$selectCode = $this->htmlCode;
		$jsObjectName = uniqid('p2g_lc_');
		$this->htmlCode = sprintf("<INPUT TYPE=\"text\" ID=\"%s\" NAME=\"%s\" VALUE=\"%s\" MAXLENGHT=\"60\" onFocus=\"this.value='';this.focus();\" onKeyUp=\"%s.updateList();\"%s%s%s%s><BR>%s", 
				$this->name . '_choose', $this->name . '_choose', PHP2Go::getLangVal('LOOKUP_CHOICE_FILTER_TIP'), 
				$jsObjectName, $this->attributes['TABINDEX'], $this->attributes['STYLE'], $this->attributes['DISABLED'], 
				(empty($this->attributes['WIDTH']) ? " SIZE=\"25\"" : $this->attributes['WIDTH']), $selectCode
		);
		$this->htmlCode .= sprintf("<SCRIPT TYPE=\"text/javascript\">%s = new LookupChoice(\"%s\",\"%s\",\"%s\");</SCRIPT>", 
				$jsObjectName, $this->_Form->formName, $this->name, $this->name . '_choose');
		return $this->htmlCode;
	}
	
	//!-----------------------------------------------------------------
	// @function	LookupChoiceField::onPreRender
	// @desc		Configura alguns atributos que possuem restrições
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function onPreRender() {
		// um campo do tipo lookupchoice não permite agrupamento
		$this->isGrouping = FALSE;
		// não pode existir primeira opção
		$this->disableFirstOption(TRUE);
		parent::onPreRender();
	}
}
?>