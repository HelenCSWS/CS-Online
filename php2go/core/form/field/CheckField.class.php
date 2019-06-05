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
// $Header: /www/cvsroot/php2go/core/form/field/CheckField.class.php,v 1.21 2005/07/25 13:35:02 mpont Exp $
// $Date: 2005/07/25 13:35:02 $

//------------------------------------------------------------------
import('php2go.form.field.FormField');
//------------------------------------------------------------------

//!-----------------------------------------------------------------
// @class		CheckField
// @desc		Esta classe constrói um campo de formulário do tipo CHECKBOX
// @package		php2go.form.field
// @extends		FormField
// @author		Marcos Pont
// @version		$Revision: 1.21 $
//!-----------------------------------------------------------------
class CheckField extends FormField
{
	var $captionString;		// @var captionString string	String contendo a caption do campo
	
	//!-----------------------------------------------------------------
	// @function	CheckField::CheckField
	// @desc		Construtor da classe CheckField, inicializa os atributos do campo
	// @access		public
	// @param		&Form Form object		Formulário onde o campo será inserido
	// @param		child bool				"FALSE" Se for TRUE, indica que o campo é membro de um campo composto
	//!-----------------------------------------------------------------
	function CheckField(&$Form, $child=FALSE) {
		parent::FormField($Form, $child);
		$this->htmlType = 'CHECKBOX';
		$this->searchDefaults['OPERATOR'] = 'EQ';
	}
	
	//!-----------------------------------------------------------------
	// @function	CheckField::getCode
	// @desc		Monta o código HTML do campo
	// @access		public
	// @return		string Código HTML do campo
	//!-----------------------------------------------------------------
	function getCode() {
		parent::onPreRender();
		// código do checkbox
		$this->htmlCode = sprintf("<INPUT TYPE=\"checkbox\" ID=\"%s\" NAME=\"%s\" TITLE=\"%s\"%s%s%s%s%s%s%s>%s",
			$this->name, $this->name, $this->attributes['CAPTION'], $this->attributes['TABINDEX'], $this->attributes['STYLE'], 
			$this->attributes['DISABLED'], $this->attributes['CHECKED'], $this->attributes['DATASRC'], 
			$this->attributes['DATAFLD'], $this->attributes['SCRIPT'], $this->captionString);
		// código do hidden field associado
		$this->htmlCode .= sprintf("<INPUT TYPE=\"hidden\" ID=\"%s\" NAME=\"%s\" VALUE=\"%s\">", 
				"V_{$this->name}", "V_{$this->name}",
				(empty($this->attributes['DISABLED']) ? $this->value : '')
		);
		return $this->htmlCode;
	}
	
	//!-----------------------------------------------------------------
	// @function	CheckField::setName
	// @desc		Sobrescreve o método de atualização do nome do campo para
	//				atualizar o código HTML da caption, se este já foi construído
	// @access		public
	// @param		newName string	Novo nome para o campo
	// @return		void
	//!-----------------------------------------------------------------
	function setName($newName) {
		if (!empty($this->captionString)) {
			$oldName = $this->name;
			if ($this->attributes['CAPTION'] == $oldName)
				$this->attributes['CAPTION'] = $newName;
			$this->captionString = str_replace($oldName, $newName, $this->captionString);
		}		
		parent::setName($newName);
	}
	
	//!-----------------------------------------------------------------
	// @function	CheckField::setLabel
	// @desc		Como o rótulo (atributo LABEL) não é obrigatório para um campo 
	//				CHECKFIELD, este método retornará a CAPTION se o label for vazio
	// @access		public
	// @return		string Rótulo do checkbox
	//!-----------------------------------------------------------------
	function getLabel() {
		if (empty($this->label) || $this->label == 'empty')
			return $this->attributes['CAPTION'];
		return $this->label;
	}
	
	//!-----------------------------------------------------------------
	// @function	CheckField::setValue
	// @desc		Sobrescreve o método setValue em FormField para converter
	//				o valor do campo para T ou F (a requisição envia "on" ou vazio)
	// @access		public
	// @param		aValue string	Valor para o checkbox
	// @return		void
	//!-----------------------------------------------------------------
	function setValue($aValue) {
		// traduz o valor do campo
		$aValue = TypeUtils::parseString($aValue);
		switch ($aValue) {
			case 'T' :
			case 'on' :
			case '1' :
				$value = 'T';
				break;
			case 'F' :
			case '0' :
				$value = 'F';
				break;
			default :
				$value = 'F';
				break;
		}
		// armazena o novo valor nos arrays globais
		$_REQUEST["V_{$this->name}"] = $value;
		$method = HttpRequest::method();
		if ($method == 'GET')
			$_GET["V_{$this->name}"] = $value;
		else
			$_POST["V_{$this->name}"] = $value;
		// define o valor do atributo CHECKED
		$this->attributes['CHECKED'] = ($value == 'T' ? ' CHECKED' : '');
		parent::setValue($value);
	}
	
	//!-----------------------------------------------------------------
	// @function	CheckField::setCaption
	// @desc		Altera ou define uma caption para o campo checkbox
	// @access		public
	// @param		caption string	Texto da caption
	// @return		void 
	//!-----------------------------------------------------------------
	function setCaption($caption) {
		$caption = trim($caption);
		if ($caption == 'empty')
			$this->attributes['CAPTION'] = '';
		elseif ($caption != '')
			$this->attributes['CAPTION'] = Form::resolveI18nEntry($caption);
		else
			$this->attributes['CAPTION'] = $this->name;
		$this->captionString = sprintf("&nbsp;<LABEL FOR=\"%s\" ID=\"%s\" NAME=\"%s\"%s>%s</LABEL>", 
				$this->name, $this->name . "_label", $this->name . "_label", 
				$this->_Form->getLabelStyle(), $this->attributes['CAPTION']
		);
	}
	
	//!-----------------------------------------------------------------
	// @function	CheckField::setChecked
	// @desc		Define se o campo deverá ser marcado
	// @access		public
	// @param		setting bool	"TRUE" Marcar ou não o campo
	// @return		void
	//!-----------------------------------------------------------------
	function setChecked($setting=TRUE) {
		if (TypeUtils::isTrue($setting)) {
			$this->attributes['CHECKED'] = " CHECKED";
			$this->value = 'T';
		} else {
			$this->attributes['CHECKED'] = "";
			$this->value = 'F';
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	CheckField::onLoadNode
	// @desc		Método responsável por processar atributos e nodos filhos
	//				provenientes da especificação XML do campo
	// @access		protected
	// @param		attrs array		Atributos do nodo
	// @param		children array	Vetor de nodos filhos
	// @return		void
	//!-----------------------------------------------------------------
	function onLoadNode($attrs, $children) {
		parent::onLoadNode($attrs, $children);
		// evento onClick da troca de valor
		parent::addEventListener(new FormEventListener(FORM_EVENT_JS, 'onClick', sprintf("checkBoxChange('%s', this)", $this->_Form->formName)));
		// caption
		$this->setCaption(@$attrs['CAPTION']);
		// label vazio se não fornecido
		if (!isset($attrs['LABEL']))
			$this->setLabel('empty');
	}
}
?>