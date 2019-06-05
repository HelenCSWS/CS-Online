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
// $Header: /www/cvsroot/php2go/core/form/field/EditSelectionField.class.php,v 1.20 2005/08/30 14:12:47 mpont Exp $
// $Date: 2005/08/30 14:12:47 $

//------------------------------------------------------------------
import('php2go.form.field.EditField');
import('php2go.form.field.FormField');
import('php2go.form.field.LookupField');
import('php2go.template.Template');
import('php2go.util.HtmlUtils');
//------------------------------------------------------------------

//!-----------------------------------------------------------------
// @class		EditSelectionField
// @desc		Esta classe monta uma estrutura de campos contendo um EDITFIELD para digitação
//				de valores que são inseridos sem repetição em um LOOKUPFIELD. Este último pode
//				ser definido com ou sem DATASOURCE. São definidos, também, dois campos escondidos
//				(INSFIELD e REMFIELD), que armazenam os valores inseridos e removidos
// @package		php2go.form.field
// @uses		EditField
// @uses		HtmlUtils
// @uses		LookupField
// @uses		Template
// @extends		FormField
// @author		Marcos Pont
// @version		$Revision: 1.20 $
//!-----------------------------------------------------------------
class EditSelectionField extends FormField
{
	var $buttonImages = array();	// @var buttonImages array				"array()" Vetor armazenando as imagens para os botões de ação
	var $jsObjectName;				// @var jsObjectName string				Nome criado para o objeto Javascript
	var $_EditField;				// @var _EditField EditField object		Objeto EditField que cria e monta o código do campo de edição
	var $_LookupField;				// @var _LookupField LookupField object	Objeto LookupField que cria e monta o código do campo que armazena os valores inseridos
	
	//!-----------------------------------------------------------------
	// @function	EditSelectionField::EditSelectionField
	// @desc		Construtor da classe
	// @access		public
	// @param		&Form Form object	Formulário no qual o campo é inserido	
	//!-----------------------------------------------------------------
	function EditSelectionField(&$Form) {
		parent::FormField($Form);
		$this->composite = TRUE;		
		$this->jsObjectName = PHP2Go::generateUniqueId($this->objectName);
		$this->searchable = FALSE;
	}
	
	//!-----------------------------------------------------------------
	// @function	EditSelectionField::getCode
	// @desc		Monta e retorna o código HTML da estrutura de campos
	// @access		public
	// @return		string Código HTML dos campos e botões, reunidos em uma tabela
	//				definida em um template
	//!-----------------------------------------------------------------
	function getCode() {		
		$this->onPreRender();
		$this->_Form->Document->addScript(PHP2GO_JAVASCRIPT_PATH . 'modules/editselection.js');
		$this->_Form->resetCode[] = sprintf("%s.onReset();return true;", $this->jsObjectName);
		// label para exibição do total de itens inseridos
		$countField = sprintf("<LABEL ID=\"%s\" STYLE=\"font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 11px; font-weight: bold; color: #000000\">", $this->jsObjectName . '_cnt');
		// campos escondidos (valores inseridos, valores removidos)
		$hiddenFields = sprintf("<INPUT TYPE=\"hidden\" ID=\"%s\" NAME=\"%s\"><INPUT TYPE=\"hidden\" ID=\"%s\" NAME=\"%s\">", $this->attributes['INSFIELD'], $this->attributes['INSFIELD'], $this->attributes['REMFIELD'], $this->attributes['REMFIELD']);
		// inicialização do componente JS
		$jsCode = sprintf("<SCRIPT TYPE=\"text/javascript\">%s = new EditSelection(\"%s\",\"%s\",\"%s\",\"%s\",\"%s\",\"%s\");</SCRIPT>",
				$this->jsObjectName, $this->_Form->formName, $this->_EditField->name, $this->_LookupField->name,
				$this->attributes['INSFIELD'], $this->attributes['REMFIELD'], $this->jsObjectName . '_cnt');
		$Tpl = new Template(PHP2GO_TEMPLATE_PATH . 'editselectionfield.tpl');
		$Tpl->parse();
		$Tpl->assign('table_width', $this->attributes['TABLEWIDTH']);
		$Tpl->assign('edit_label', "<LABEL FOR=\"" . $this->_EditField->name . "\" " . $this->_Form->getLabelStyle() . ">" . $this->_EditField->label . "</LABEL>");
		$Tpl->assign('edit', $this->_EditField->getCode());
		$Tpl->assign('lookup_label', "<LABEL FOR=\"" . $this->_LookupField->name . "\" " . $this->_Form->getLabelStyle() . ">" . $this->_LookupField->label . "</SPAN>");
		$Tpl->assign('lookup', $this->_LookupField->getCode());
		foreach ($this->attributes['BUTTONS'] as $index => $button)
			$Tpl->assign('button_' . $index, $button);
		$Tpl->assign('style', $this->_Form->getLabelStyle());
		$Tpl->assign('count_label', PHP2Go::getLangVal('SEL_INSERTED_VALUES_LABEL'));
		$Tpl->assign('count', $countField);
		$Tpl->assign('hidden', $hiddenFields);
		$Tpl->assign('js_code', $jsCode);
		$this->htmlCode = $Tpl->getContent();
		return $this->htmlCode;	
	}
	
	//!-----------------------------------------------------------------
	// @function	EditSelectionField::&getEditField
	// @desc		Busca o objeto EditField que representa a caixa de edição
	// @access		public
	// @return		EditField object	Caixa de edição
	// @note		Retorna NULL se o objeto ainda não foi definido
	//!-----------------------------------------------------------------
	function &getEditField() {
		if (TypeUtils::isInstanceOf($this->_EditField, 'editfield'))
			return $this->_EditField;
		return NULL;
	}
	
	//!-----------------------------------------------------------------
	// @function	EditSelectionField::&getLookupField
	// @desc		Retorna o objecto LookupField que representa a lista de
	//				itens inseridos na estrutura do campo
	// @access		public
	// @return		LookupField object	Lista de itens inseridos
	// @note		Retorna NULL se o objeto não foi definido
	//!-----------------------------------------------------------------
	function &getLookupField() {
		if (TypeUtils::isInstanceOf($this->_LookupField, 'lookupfield'))
			return $this->_LookupField;
		return NULL;
	}
	
	//!-----------------------------------------------------------------
	// @function	EditSelectionField::setInsertedValuesFieldName
	// @desc		Define o nome do campo escondido que irá armazenar os valores inseridos na caixa de seleção
	// @access		public
	// @param		insField string		Nome para o campo
	// @return		void
	//!-----------------------------------------------------------------
	function setInsertedValuesFieldName($insField) {
		if (trim($insField) != '' && $insField != $this->_EditField->name && $insField != $this->_LookupField->name)
			$this->attributes['INSFIELD'] = $insField;
		else
			$this->attributes['INSFIELD'] = $this->name . '_inserted';
		$this->_Form->verifyFieldName($this->_Form->formName, $this->attributes['INSFIELD']);
	}
	
	//!-----------------------------------------------------------------
	// @function	EditSelectionField::setRemovedValuesFieldName
	// @desc		Define o nome do campo escondido que irá armazenar os valores removidos da caixa de seleção
	// @access		public
	// @param		remField string		Nome para o campo
	// @return		void
	//!-----------------------------------------------------------------
	function setRemovedValuesFieldName($remField) {
		if (trim($remField) != '' && $remField != $this->_EditField->name && $remField != $this->_LookupField->name)
			$this->attributes['REMFIELD'] = $remField;
		else
			$this->attributes['REMFIELD'] = $this->name . '_removed';
		$this->_Form->verifyFieldName($this->_Form->formName, $this->attributes['REMFIELD']);
	}
	
	//!-----------------------------------------------------------------
	// @function	EditSelectionField::setTableWidth
	// @desc		Os campos do tipo EditSelection são gerados em um template
	//				pré-definido no framework. Este método permite customizar
	//				o tamanho da tabela principal deste template
	// @access		public
	// @param		tableWidth string	Tamanho para a tabela, a ser utilizado no atributo WIDTH da tabela
	// @return		void
	//!-----------------------------------------------------------------
	function setTableWidth($tableWidth) {
		if ($tableWidth)
			$this->attributes['TABLEWIDTH'] = " WIDTH='" . $tableWidth . "'";
		else
			$this->attributes['TABLEWIDTH'] = "";		
	}
	
	//!-----------------------------------------------------------------
	// @function	EditSelectionField::setButtonImages
	// @desc		Define imagens para os botões de ação
	// @access		public
	// @param		add string		Imagem para o botão "adicionar"
	// @param		rem string		Imagem para o botão "remover"
	// @param		remAll string	Imagem para o botão "remover todos"
	// @return		void
	//!-----------------------------------------------------------------
	function setButtonImages($add, $rem, $remAll) {
		(trim($add) != '') && ($this->buttonImages['ADD'] = $add);
		(trim($rem) != '') && ($this->buttonImages['REM'] = $rem);
		(trim($remAll) != '') && ($this->buttonImages['REMALL'] = $remAll);
	}
	
	//!-----------------------------------------------------------------
	// @function	EditSelectionField::onLoadNode
	// @desc		Método responsável por processar atributos e nodos filhos
	//				provenientes da especificação XML do campo
	// @access		protected
	// @param		attrs array		Atributos do nodo
	// @param		children array	Vetor de nodos filhos
	// @return		void
	//!-----------------------------------------------------------------
	function onLoadNode($attrs, $children) {
		parent::onLoadNode($attrs, $children);
		// verifica se a estrutura de nodos filhos está correta
		if (isset($children['EDITFIELD']) && isset($children['LOOKUPFIELD']) &&
			TypeUtils::isInstanceOf($children['EDITFIELD'], 'xmlnode') && 
			TypeUtils::isInstanceOf($children['LOOKUPFIELD'], 'xmlnode')) {
			// instancia os campos filhos			
			$this->_EditField = new EditField($this->_Form, TRUE);
			$this->_EditField->onLoadNode($children['EDITFIELD']->getAttributes(), $children['EDITFIELD']->getChildrenTagsArray());
			$this->_LookupField = new LookupField($this->_Form, TRUE);
			$this->_LookupField->onLoadNode($children['LOOKUPFIELD']->getAttributes(), $children['LOOKUPFIELD']->getChildrenTagsArray());
			// copia o atributo disabled para os filhos
			$this->_EditField->attributes['DISABLED'] = $this->attributes['DISABLED'];
			$this->_LookupField->attributes['DISABLED'] = $this->attributes['DISABLED'];	
			// define o nome de foco
			$this->focusName = $this->_EditField->getName();
			// campo para valores inseridos
			$this->setInsertedValuesFieldName(@$attrs['INSFIELD']);
			// campo para valores removidos
			$this->setRemovedValuesFieldName(@$attrs['REMFIELD']);
			// largura da tabela
			$this->setTableWidth(@$attrs['TABLEWIDTH']);
			// imagens dos botões
			$this->setButtonImages(@$attrs['ADDIMG'], @$attrs['REMIMG'], @$attrs['REMALLIMG']);
			// registra valores submetidos
			if ($this->_Form->isPosted()) {
				$inserted = HttpRequest::getVar($this->attributes['INSFIELD'], $this->_Form->formMethod);
				$removed = HttpRequest::getVar($this->attributes['REMFIELD'], $this->_Form->formMethod);
				parent::setSubmittedValue(array(
					$this->attributes['INSFIELD'] => (!empty($inserted) ? explode('#', $inserted) : array()),
					$this->attributes['REMFIELD'] => (!empty($removed) ? explode('#', $removed) : array())
				));
			}	
		} else {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_MISSING_EDITSELECTION_CHILDREN', $this->name), E_USER_ERROR, __FILE__, __LINE__);
		}		
	}
	
	//!-----------------------------------------------------------------
	// @function	EditSelectionField::onPreRender
	// @desc		Configura os botões de ação e algumas propriedades dos
	//				campos de valor e seleção que possuem restrições em seus
	//				valores quando usadas dentro desta classe
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function onPreRender() {
		// configuração dos botões de ação
		$this->attributes['BUTTONS'] = array();
		$buttonMessages = PHP2Go::getLangVal('EDIT_SELECTION_BUTTON_TITLES');
		$imgActCode = "<BUTTON ID=\"%s\" NAME=\"%s\" TYPE=\"BUTTON\" TITLE=\"%s\" onClick=\"%s.%s();\" STYLE=\"cursor:pointer;background-color:transparent;border:none\"%s%s><IMG SRC=\"%s\" ALT=\"\" BORDER=\"0\"></BUTTON>";
		$btnActCode = "<INPUT ID=\"%s\" NAME=\"%s\" TYPE=\"BUTTON\" VALUE=\" %s \" STYLE=\"width:25px\" TITLE=\"%s\" onClick=\"%s.%s();\"%s%s%s>";
		$actHash = array(
			array('ADD', 'add', 'add', '+'),
			array('REM', 'rem', 'remove', '-'),
			array('REMALL', 'remall', 'removeAll', 'X')
		);
		for ($i=0; $i<sizeof($actHash); $i++) {
			if (isset($this->buttonImages[$actHash[$i][0]]))
				$this->attributes['BUTTONS'][] = sprintf($imgActCode, 
					$this->name . '_' . $actHash[$i][1], $actHash[$i][0],
					$buttonMessages[$actHash[$i][1]],
					$this->jsObjectName, $actHash[$i][2],
					$this->attributes['DISABLED'], 
					$this->_EditField->attributes['TABINDEX'],
					$this->buttonImages[$actHash[$i][0]]
				);
			else
				$this->attributes['BUTTONS'][] = sprintf($btnActCode,
					$this->name . '_' . $actHash[$i][1], $actHash[$i][0],
					$actHash[$i][3], $buttonMessages[$actHash[$i][1]],
					$this->jsObjectName, $actHash[$i][2], $this->_Form->getButtonStyle(), 
					$this->attributes['DISABLED'], $this->_EditField->attributes['TABINDEX']
				);
		}
		// caixa de edição não pode ter obrigatoriedade
		$this->_EditField->setRequired(FALSE);
		// lista de itens inseridos deve ter primeira opção não vazia
		if (trim($this->_LookupField->getAttribute('FIRST')) == "")
			$this->_LookupField->setFirstOption(PHP2Go::getLangVal('LOOKUP_SELECTION_DEFAULT_SELFIRST'));
		$this->_LookupField->disableFirstOption(FALSE);
		// lista de itens inseridos deve ter seleção múltipla
		$this->_LookupField->setMultiple();
		// tamanho da lista de itens inseridos deve ser >= 2
		$matches = array();
		eregi(" SIZE=\"([0-9]+)\"", $this->_LookupField->getAttribute('SIZE'), $matches);
		if (!isset($matches[1]) || TypeUtils::parseInteger($matches[1]) < 2)
			$this->_LookupField->setSize(8);
		// inclusão de script para controle de obrigatoriedade
		if ($this->required)
			$this->_Form->appendScript(sprintf("          validator.addLookupCheck(\"%s\", \"%s\", %d);\n", $this->_LookupField->name, $this->_LookupField->label, 2));			
	}
}
?>