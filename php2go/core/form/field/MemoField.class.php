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
// $Header: /www/cvsroot/php2go/core/form/field/MemoField.class.php,v 1.18 2005/06/27 20:54:05 mpont Exp $
// $Date: 2005/06/27 20:54:05 $

//------------------------------------------------------------------
import('php2go.form.field.EditableField');
import('php2go.template.Template');
//------------------------------------------------------------------

// @const	MEMOFIELD_DEFAULT_COLS		"40"
// N�mero de colunas padr�o na c7onstru��o de campos do tipo TEXTAREA
define('MEMOFIELD_DEFAULT_COLS', 40);
// @const	MEMOFIELD_DEFAULT_ROWS		"5"
// N�mero de linhas padr�o para campos do tipo TEXTAREA
define('MEMOFIELD_DEFAULT_ROWS', 5);

//!-----------------------------------------------------------------
// @class		MemoField
// @desc		Classe respons�vel por construir um INPUT HTML do 
//				tipo TEXTAREA, para edi��o de texto com v�rias linhas
// @package		php2go.form.field
// @extends		EditableField
// @uses		Template
// @uses		TypeUtils
// @author		Marcos Pont
// @version		$Revision: 1.18 $
//!-----------------------------------------------------------------
class MemoField extends EditableField
{
	var $charCountControl = FALSE; // @var charCountControl bool	"FALSE" Indica se deve ser inclu�do um contador de caracteres
	
	//!-----------------------------------------------------------------
	// @function	MemoField::MemoField
	// @desc		Construtor da classe MemoField, inicializa os atributos do campo
	// @access		public
	// @param		&Form Form object	Formul�rio no qual o campo � inserido
	// @param		child bool			"FALSE" Se for TRUE, indica que o campo � membro de um campo composto
	//!-----------------------------------------------------------------
	function MemoField(&$Form, $child=FALSE) {
		parent::EditableField($Form, $child);
		$this->htmlType = 'TEXTAREA';
	}
	
	//!-----------------------------------------------------------------
	// @function	MemoField::getCode
	// @desc		Monta o c�digo HTML do campo
	// @access		public
	// @return		string C�digo HTML do campo
	//!-----------------------------------------------------------------
	function getCode() {
		$this->onPreRender();
		if (isset($this->maxLength) && $this->charCountControl) {
			// registra o campo MEMOFIELD, para que a fun��o clearForm possa resetar o campo de controle de caracteres
			$this->_Form->Document->addScriptCode(sprintf("     registerMemoField(\"%s\", %d);", $this->name . '_count', $this->maxLength), 'JavaScript', SCRIPT_START);
			// constr�i o c�digo da caixa de texto e do campo de controle de caracteres
			$memoCode = sprintf("<TEXTAREA ID=\"%s\" NAME=\"%s\" COLS=\"%s\" ROWS=\"%s\" TITLE=\"%s\"%s%s%s%s%s%s%s>%s</TEXTAREA>",
					$this->name, $this->name, $this->attributes['COLS'], $this->attributes['ROWS'], $this->label, $this->attributes['SCRIPT'],
					$this->attributes['TABINDEX'], $this->attributes['STYLE'], $this->attributes['READONLY'], 
					$this->attributes['DISABLED'], $this->attributes['DATASRC'], $this->attributes['DATAFLD'], $this->value);
			$countCode = sprintf("<INPUT TYPE=\"text\" ID=\"%s\" NAME=\"%s\" SIZE=\"5\" VALUE=\"%s\" DISABLED%s>",
					$this->name . '_count', $this->name . '_count', (max(0, $this->maxLength-strlen($this->value))), $this->attributes['STYLE']);
			$countLabel = sprintf("<SPAN%s>%s</SPAN>", 
					$this->_Form->getLabelStyle(), PHP2Go::getLangVal('MEMO_COUNT_LABEL'));
			$Tpl =& new Template(PHP2GO_TEMPLATE_PATH . 'memofield.tpl');
			$Tpl->parse();
			$Tpl->assign(array(
				'memo' => $memoCode, 
				'count' => $countCode, 
				'label_count' => $countLabel)
			);
			$this->htmlCode = $Tpl->getContent();
		} else {			
			$this->htmlCode = sprintf("<TEXTAREA ID=\"%s\" NAME=\"%s\" COLS=\"%s\" ROWS=\"%s\" TITLE=\"%s\"%s%s%s%s%s%s%s>%s</TEXTAREA>",
				$this->name, $this->name, $this->attributes['COLS'], $this->attributes['ROWS'], $this->label, $this->attributes['SCRIPT'],
				$this->attributes['TABINDEX'], $this->attributes['STYLE'], $this->attributes['READONLY'], 
				$this->attributes['DISABLED'], $this->attributes['DATASRC'], $this->attributes['DATAFLD'], $this->value);
		}
		return $this->htmlCode;
	}
	
	//!-----------------------------------------------------------------
	// @function	MemoField::setCols
	// @desc		Define o n�mero de colunas (largura) do campo
	// @access		public
	// @param		cols int	N�mero de colunas
	// @return		void
	//!-----------------------------------------------------------------
	function setCols($cols) {
		$this->attributes['COLS'] = $cols;
	}
	
	//!-----------------------------------------------------------------
	// @function	MemoField::setRows
	// @desc		Define o n�mero de linhas (altura) do campo
	// @access		public
	// @param		rows int 	N�mero de linhas
	// @return		void	
	//!-----------------------------------------------------------------
	function setRows($rows) {
		$this->attributes['ROWS'] = $rows;
	}
	
	//!-----------------------------------------------------------------
	// @function	MemoField::setWidth
	// @desc		Define a largura em pixels (via CSS) do campo memo
	// @access		public
	// @param		width int	Largura, em pixels
	// @return		void
	//!-----------------------------------------------------------------
	function setWidth($width) {
		if (TypeUtils::isInteger($width))
			$this->attributes['WIDTH'] = $width;
	}
	
	//!-----------------------------------------------------------------
	// @function	MemoField::charCount
	// @desc		Habilita ou desabilita o controle de m�ximo de caracteres no campo
	//				com exibi��o no n�mero de caracteres restantes em um campo de edi��o
	// @access		public
	// @param		setting bool	Valor para a propriedade (habilitado ou desabilitado)
	// @param		maxLength int	"NULL" Permite setar o n�mero m�ximo de caracteres para o campo
	// @return		void
	// @note		O mesmo efeito � obtido na seguinte seq�encia de comandos:<BR>
	//				<PRE>
	//				
	//				$field->charCount(TRUE);
	//				$field->setMaxLength(20);
	//
	//				</PRE>
	//!-----------------------------------------------------------------
	function charCount($setting, $maxLength=NULL) {
		$this->charCountControl = TypeUtils::toBoolean($setting);
		if (TypeUtils::isInteger($maxLength)) {
			parent::setMaxLength($maxLength);
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	MemoField::onLoadNode
	// @desc		M�todo respons�vel por processar atributos e nodos filhos
	//				provenientes da especifica��o XML do campo
	// @access		protected
	// @param		attrs array		Atributos do nodo
	// @param		children array	Vetor de nodos filhos
	// @return		void
	//!-----------------------------------------------------------------
	function onLoadNode($attrs, $children) {
		parent::onLoadNode($attrs, $children);
		// n�mero de colunas
		if (isset($attrs['COLS']) && TypeUtils::isInteger($attrs['COLS']))
			$this->setCols($attrs['COLS']);
		else
			$this->setCols(MEMOFIELD_DEFAULT_COLS);
		// n�mero de linhas
		if (isset($attrs['ROWS']) && TypeUtils::isInteger($attrs['ROWS']))
			$this->setRows($attrs['ROWS']);
		else
			$this->setRows(MEMOFIELD_DEFAULT_ROWS);
		// largura em pixels
		$this->setWidth(@$attrs['WIDTH']);
		// contador de caracteres
		$this->charCount(Form::resolveBooleanChoice(@$attrs['CHARCOUNT']));
	}
	
	//!-----------------------------------------------------------------
	// @function	MemoField::onPreRender
	// @desc		Adiciona os tratadores para os eventos onKeyUp e onKeyDown
	//				se o controle de caracteres estiver habilitado
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function onPreRender() {
		if (isset($this->maxLength) && $this->charCountControl) {
			// adiciona tratadores para os eventos onKeyUp e onKeyDown
			parent::addEventListener(new FormEventListener(FORM_EVENT_JS, 'onKeyUp', sprintf("memoFieldCharControl(document.%s.elements['%s'], document.%s.elements['%s'], %s, event)", $this->_Form->formName, $this->name, $this->_Form->formName, $this->name . '_count', $this->maxLength)));
			parent::addEventListener(new FormEventListener(FORM_EVENT_JS, 'onKeyDown', sprintf("memoFieldCharControl(document.%s.elements['%s'], document.%s.elements['%s'], %s, event)", $this->_Form->formName, $this->name, $this->_Form->formName, $this->name . '_count', $this->maxLength)));
		}
		parent::onPreRender();
	}
}
?>