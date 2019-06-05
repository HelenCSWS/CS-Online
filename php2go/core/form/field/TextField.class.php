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
// $Header: /www/cvsroot/php2go/core/form/field/TextField.class.php,v 1.12 2005/06/27 20:54:15 mpont Exp $
// $Date: 2005/06/27 20:54:15 $

//------------------------------------------------------------------
import('php2go.form.field.FormField');
//------------------------------------------------------------------

//!-----------------------------------------------------------------
// @class		TextField
// @desc		Classe que permite a inclusão de porções de texto dinâmicas
//				dentro de um formulário, utilizando tags &lt;SPAN&gt;. A utilização
//				de um TEXTFIELD pode ser substituída pelo uso de templates e variáveis
//				de substituição
// @package		php2go.form.field
// @extends		FormField
// @author		Marcos Pont
// @version		$Revision: 1.12 $
//!-----------------------------------------------------------------
class TextField extends FormField
{
	//!-----------------------------------------------------------------
	// @function	TextField::TextField
	// @desc		Construtor da classe, inicializa os atributos básicos do campo
	// @access		public
	// @param		&Form Form object	Formulário no qual o campo é inserido
	// @param		child bool			"FALSE" Se for TRUE, indica que o campo é membro de um campo composto
	//!-----------------------------------------------------------------
	function TextField(&$Form, $child=FALSE) {
		parent::FormField($Form, $child);
		$this->htmlType = 'SPAN';
		$this->searchable = FALSE;
	}
	
	//!-----------------------------------------------------------------
	// @function	TextField::getCode
	// @desc		Monta o código HTML do campo
	// @access		public
	// @return		string Código HTML do campo
	//!-----------------------------------------------------------------
	function getCode() {
		$this->htmlCode = sprintf("<SPAN ID=\"%s\" TITLE=\"%s\"%s>%s</SPAN>", 
				$this->name, $this->label, $this->attributes['STYLE'], $this->value);
		return $this->htmlCode;
	}
}
?>