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
// $Header: /www/cvsroot/php2go/core/form/field/EditorField.class.php,v 1.16 2005/06/17 19:46:16 mpont Exp $
// $Date: 2005/06/17 19:46:16 $

//------------------------------------------------------------------
import('php2go.form.field.MemoField');
import('php2go.net.HttpRequest');
import('php2go.template.Template');
//------------------------------------------------------------------

//!-----------------------------------------------------------------
// @class		EditorField
// @desc		Classe responsável por construir um editor HTML WYSIWYG
//				com formatação de fonte, cor, inclusão de imagens e links,
//				marcação e indentação
// @package		php2go.form.field
// @uses		HttpRequest
// @uses		Template
// @extends		MemoField
// @author		Marcos Pont
// @version		$Revision: 1.16 $
// @note		O código gerado para o campo EditorField só irá construir
//				o editor WYSIWYG se o navegador do usuário for o Internet 
//				Explorer. Para outros navegadores, será gerado um campo
//				do tipo MemoField
//!-----------------------------------------------------------------
class EditorField extends MemoField
{
	var $useMemo = FALSE;	// @var useMemo bool		"FALSE" Indica que um MEMO FIELD deve ser gerado, ou por incompatibilidade do browser ou por o formulário atual já possui um editor
	
	//!-----------------------------------------------------------------
	// @function	EditorField::EditorField
	// @desc		Construtor da classe EditorField, inicializa os atributos do campo
	// @access		public
	// @param		&Form Form object	Formulário no qual o campo é inserido
	//!-----------------------------------------------------------------
	function EditorField(&$Form) {
		parent::MemoField($Form);
		$this->htmlType = 'IFRAME';
		// registra a existência do campo
		if ($this->_Form->hasEditor)
			$this->useMemo = TRUE;
		else
			$this->_Form->hasEditor = TRUE;
	}
	
	//!-----------------------------------------------------------------
	// @function	EditorField::setName
	// @desc		Sobrescreve a implementação do método setName na classe
	//				superior para manter sincronia entre o nome do campo e o
	//				nome do editor associado ao formulário
	// @access		public
	// @param		name string		Novo nome para o campo
	// @return		void
	//!-----------------------------------------------------------------
	function setName($name) {
		parent::setName($name);	
		$this->_Form->editorName = $this->name;		
	}
	
	//!-----------------------------------------------------------------
	// @function	EditorField::getCode
	// @desc		Monta o código HTML do campo
	// @access		public
	// @return		string Código HTML do campo
	//!-----------------------------------------------------------------
	function getCode() {
		if ($this->useMemo) {
			return parent::getCode();
		} else {
			$this->onPreRender();
			$this->_Form->Document->addScript(PHP2GO_JAVASCRIPT_PATH . "modules/advancededitor.js");
			$this->_Form->Document->addScript(PHP2GO_JAVASCRIPT_PATH . "modules/colorselection.js");
			$Tpl =& new Template(PHP2GO_TEMPLATE_PATH . "advancededitor.tpl");
			$Tpl->parse();
			$Tpl->globalAssign('iconPath', PHP2GO_ICON_PATH);			
			$Tpl->globalAssign('editorName', $this->name);
			$Tpl->assign('editorWidth', (isset($this->attributes['WIDTH']) ? max($this->attributes['WIDTH'], 450) : 450));
			$Tpl->assign('formName', $this->_Form->formName);
			$Tpl->assign('hiddenContent', sprintf("<INPUT TYPE=\"hidden\" ID=\"%s\" NAME=\"%s\" VALUE=\"%s\" TITLE=\"%s\"%s%s>", 
					$this->name, $this->name, htmlspecialchars($this->value), $this->label, $this->attributes['DATASRC'], $this->attributes['DATAFLD']));
			$Tpl->assign('readonlyForm', ($this->_Form->readonly ? 'true' : 'false'));
			$Tpl->assign('globalDisabled', ($this->_Form->readonly ? ' DISABLED' : ''));
			$Tpl->assign('buttonStyle', (isset($this->_Form->buttonStyle) ? $this->_Form->buttonStyle : ''));
			$Tpl->assign('inputStyle', (isset($this->_Form->inputStyle) ? $this->_Form->inputStyle : ''));
			$Tpl->assign('labelStyle', (isset($this->_Form->labelStyle) ? $this->_Form->labelStyle : ''));
			$Tpl->assign(PHP2Go::getLangVal('FORM_EDITOR_VARS'));
			// 49 (7x7) emoticons disponíveis no editor HTML			
			$customEmoticons = array(
				'smiley', 'lol', 'surprise', 'blink', 'sad', 'confused', 'disappointed', 
				'cry', 'shame', 'glasses', 'angry', 'angel', 'devil', 'creekingteeth', 
				'nerd', 'sarcastic', 'secret', 'party', 'thumbup', 'thumbdown', 'boy', 
				'girl', 'hug', 'heart', 'brokenheart', 'kiss', 'gift', 'flower', 
				'bulb', 'coffee', 'beer', 'cake', 'gift', 'camera', 'phone', 
				'moon', 'star', 'email', 'clock',  'plate', 'pizza', 'ball', 
				'computer', 'car', 'plane', 'umbrella', 'island', 'storm', 'money'
			);
			foreach ($customEmoticons as $emoticon)
				$Tpl->createAndAssign('emoticon', array('imgName' => $emoticon));
			$this->htmlCode = $Tpl->getContent();
			return $this->htmlCode;
		}	
	}
	
	//!-----------------------------------------------------------------
	// @function	EditorField::onPreRender
	// @desc		Realiza configurações necessárias antes da construção
	//				do código HTML do campo
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------	
	function onPreRender() {
		if (!$this->useMemo) {
			$this->listeners = array();
			$this->focusName = "{$this->name}_composition";
		}
		parent::onPreRender();
	}
}
?>