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
// $Header: /www/cvsroot/php2go/core/form/field/PasswdField.class.php,v 1.11 2005/06/27 20:54:36 mpont Exp $
// $Date: 2005/06/27 20:54:36 $

//------------------------------------------------------------------
import('php2go.form.field.EditableField');
//------------------------------------------------------------------

//!-----------------------------------------------------------------
// @class		PasswdField
// @desc		Classe responsável por construir um INPUT HTML do 
//				tipo PASSWORD
// @package		php2go.form.field
// @extends		EditableField
// @author		Marcos Pont
// @version		$Revision: 1.11 $
//!-----------------------------------------------------------------
class PasswdField extends EditableField
{
	//!-----------------------------------------------------------------
	// @function	PasswdField::PasswdField
	// @desc		Construtor da classe PasswdField, inicializa os atributos do campo
	// @access		public
	// @param		&Form Form object	Formulário no qual o campo é inserido
	// @param		child bool			"FALSE" Se for TRUE, indica que o campo é membro de um campo composto
	//!-----------------------------------------------------------------
	function PasswdField(&$Form, $child=FALSE) {
		parent::EditableField($Form, $child);
		$this->htmlType = 'PASSWORD';
	}
	
	//!-----------------------------------------------------------------
	// @function	PasswdField::getCode
	// @desc		Monta o código HTML do campo
	// @access		public
	// @return		string Código HTML do campo
	//!-----------------------------------------------------------------
	function getCode() {
		parent::onPreRender();
		$this->htmlCode = sprintf("<INPUT TYPE=\"password\" ID=\"%s\" NAME=\"%s\" VALUE=\"%s\" MAXLENGTH=\"%s\" SIZE=\"%s\" TITLE=\"%s\"%s%s%s%s%s%s%s%s%s>",
			$this->name, $this->name, $this->value, $this->attributes['LENGTH'], $this->attributes['SIZE'], $this->label, $this->attributes['SCRIPT'],
			$this->attributes['TABINDEX'], $this->attributes['ALIGN'], $this->attributes['STYLE'], $this->attributes['READONLY'], 
			$this->attributes['DISABLED'], $this->attributes['DATASRC'], $this->attributes['DATAFLD'], $this->attributes['AUTOCOMPLETE']);
		return $this->htmlCode;
	}	
}
?>