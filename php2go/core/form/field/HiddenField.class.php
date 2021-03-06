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
// $Header: /www/cvsroot/php2go/core/form/field/HiddenField.class.php,v 1.9 2005/06/27 20:53:14 mpont Exp $
// $Date: 2005/06/27 20:53:14 $

//------------------------------------------------------------------
import('php2go.form.field.FormField');
//------------------------------------------------------------------

//!-----------------------------------------------------------------
// @class		HiddenField
// @desc		Classe que constr�i um grupo de campos do tipo RADIO BUTTON
// @package		php2go.form.field
// @extends		FormField
// @author		Marcos Pont
// @version		$Revision: 1.9 $
//!-----------------------------------------------------------------
class HiddenField extends FormField
{
	//!-----------------------------------------------------------------
	// @function	HiddenField::HiddenField
	// @desc		Construtor da classe, inicializa os atributos b�sicos do campo
	// @access		public
	// @param		&Form Form object	Formul�rio no qual o campo � inserido
	//!-----------------------------------------------------------------
	function HiddenField(&$Form) {
		parent::FormField($Form);
		$this->htmlType = 'HIDDEN';
		$this->searchDefaults['OPERATOR'] = 'EQ';
	}
	
	//!-----------------------------------------------------------------
	// @function	HiddenField::getCode
	// @desc		Monta o c�digo HTML do campo
	// @access		public
	// @return		string C�digo HTML do campo
	//!-----------------------------------------------------------------
	function getCode() {
		$this->htmlCode = sprintf("<INPUT TYPE=\"hidden\" ID=\"%s\" NAME=\"%s\" VALUE=\"%s\"%s%s>", 
				$this->name, $this->name, $this->value, 
				$this->attributes['DATASRC'], $this->attributes['DATAFLD']);
		return $this->htmlCode;
	}
}
?>