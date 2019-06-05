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
// $Header: /www/cvsroot/php2go/core/form/field/LookupSelectionField.class.php,v 1.21 2005/06/28 13:09:05 mpont Exp $
// $Date: 2005/06/28 13:09:05 $

//------------------------------------------------------------------
import('php2go.form.field.FormField');
import('php2go.form.field.LookupField');
import('php2go.template.Template');
import('php2go.util.HtmlUtils');
//------------------------------------------------------------------

//!-----------------------------------------------------------------
// @class		LookupSelectionField
// @desc		Esta classe monta uma estrutura de campos contendo um LOOKUPFIELD
//				origem, contendo valores disponiveis para seleção e outro para
//				armazenamento dos valores selecionados. Dois campos escondidos são
//				definidos para armazenar os valores inseridos e removidos (INSFIELD
//				e REMFIELD)
// @package		php2go.form.field
// @uses		HtmlUtils
// @uses		LookupField
// @uses		Template
// @extends		FormField
// @author		Marcos Pont
// @version		$Revision: 1.21 $
//!-----------------------------------------------------------------
class LookupSelectionField extends FormField
{
	var $buttonImages = array();	// @var buttonImages array					"array()" Conjunto de imagens para os botões de ação
	var $jsObjectName;				// @var jsObjectName string					Nome criado para o objeto Javascript
	var $_SourceLookup;				// @var _SourceLookup LookupField object	Cria e monta o código do campo dos valores disponiveis
	var $_TargetLookup;				// @var _TargetLookup LookupField object	Cria e monta o código do campo dos valores inseridos
	
	//!-----------------------------------------------------------------
	// @function	LookupSelectionField::LookupSelectionField
	// @desc		Construtor da classe
	// @access		public
	// @param		&Form Form object	Formulário no qual o campo é inserido	
	//!-----------------------------------------------------------------
	function LookupSelectionField(&$Form) {
		parent::FormField($Form);
		$this->composite = TRUE;
		$this->jsObjectName = PHP2Go::generateUniqueId('p2g_ls_');
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
		$this->_Form->Document->addScript(PHP2GO_JAVASCRIPT_PATH . 'modules/lookupselection.js');			
		$this->_Form->resetCode[] = sprintf("%s.onReset();return true;", $this->jsObjectName);		
		// label para exibição do total de itens disponíveis
		$availableField = sprintf("<LABEL ID=\"%s\" STYLE=\"font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 11px; font-weight: bold; color: #000000\">%s</LABEL>", $this->jsObjectName . '_available', $this->_SourceLookup->getOptionCount());
		// label para exibição do total de itens inseridos
		$countField = sprintf("<LABEL ID=\"%s\" STYLE=\"font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 11px; font-weight: bold; color: #000000\">", $this->jsObjectName . '_cnt');
		// campos escondidos (valores inseridos, valores removidos)
		$hiddenFields = sprintf("<INPUT TYPE=\"hidden\" NAME=\"%s\"><INPUT TYPE=\"hidden\" NAME=\"%s\">", $this->attributes['INSFIELD'], $this->attributes['REMFIELD']);		
		// configuração do objeto JavaScript		
		$jsCode = sprintf("<SCRIPT TYPE=\"text/javascript\">%s = new LookupSelection(\"%s\",\"%s\",\"%s\",\"%s\",\"%s\",\"%s\");</SCRIPT>",
				$this->jsObjectName, $this->_Form->formName, $this->_SourceLookup->name, $this->_TargetLookup->name,
				$this->attributes['INSFIELD'], $this->attributes['REMFIELD'], $this->jsObjectName . '_cnt');
		$Tpl =& new Template(PHP2GO_TEMPLATE_PATH . 'lookupselectionfield.tpl');
		$Tpl->parse();		
		$Tpl->assign('table_width', $this->attributes['TABLEWIDTH']);
		$Tpl->assign('source_label', "<SPAN " . $this->_Form->getLabelStyle() . ">" . $this->_SourceLookup->label . "</SPAN>");
		$Tpl->assign('source', $this->_SourceLookup->getCode());
		$Tpl->assign('target_label', "<SPAN " . $this->_Form->getLabelStyle() . ">" . $this->_TargetLookup->label . "</SPAN>");
		$Tpl->assign('target', $this->_TargetLookup->getCode());
		foreach ($this->attributes['BUTTONS'] as $index => $button)
			$Tpl->assign('button_' . $index, $button);	
		$Tpl->assign('style', $this->_Form->getLabelStyle());
		$Tpl->assign('available_label', PHP2Go::getLangVal('SEL_AVAILABLE_VALUES_LABEL'));
		$Tpl->assign('available', $availableField);
		$Tpl->assign('count_label', PHP2Go::getLangVal('SEL_INSERTED_VALUES_LABEL'));
		$Tpl->assign('count', $countField);
		$Tpl->assign('hidden', $hiddenFields);
		$Tpl->assign('js_code', $jsCode);
		$this->htmlCode = $Tpl->getContent();
		return $this->htmlCode;	
	}
	
	//!-----------------------------------------------------------------
	// @function	LookupSelectionField::&getSourceLookup
	// @desc		Busca o objeto LookupField que representa a lista de
	//				itens disponíveis
	// @access		public
	// @return		LookupField object	Lista de itens disponíveis
	// @note		Retorna NULL caso a lista não tenha sido construída 
	//!-----------------------------------------------------------------
	function &getSourceLookup() {
		if (TypeUtils::isInstanceOf($this->_SourceLookup, 'lookupfield'))
			return $this->_SourceLookup;
		return NULL;
	}
	
	//!-----------------------------------------------------------------
	// @function	LookupSelectionField::&getTargetLookup
	// @desc		Busca o objeto LookupField que representa a lista de
	//				itens selecionados
	// @acces		public
	// @return		LookupField object	Lista de itens selecionados/inseridos
	// @note		Retorna NULL se o objeto ainda não foi definido
	//!-----------------------------------------------------------------
	function &getTargetLookup() {
		if (TypeUtils::isInstanceOf($this->_TargetLookup, 'lookupfield'))
			return $this->_TargetLookup;
		return NULL;
	}
	
	//!-----------------------------------------------------------------
	// @function	LookupSelectionField::setInsertedValuesFieldName
	// @desc		Define o nome do campo escondido que irá armazenar os valores inseridos na caixa de seleção
	// @access		public
	// @param		insField string		Nome para o campo
	// @return		void
	//!-----------------------------------------------------------------
	function setInsertedValuesFieldName($insField) {
		if (trim($insField) != '' && $insField != $this->_SourceLookup->name && $insField != $this->_TargetLookup->name)
			$this->attributes['INSFIELD'] = $insField;
		else
			$this->attributes['INSFIELD'] = $this->name . '_inserted';
		$this->_Form->verifyFieldName($this->_Form->formName, $this->attributes['INSFIELD']);
	}
	
	//!-----------------------------------------------------------------
	// @function	LookupSelectionField::setRemovedValuesFieldName
	// @desc		Define o nome do campo escondido que irá armazenar os valores removidos da caixa de seleção
	// @access		public
	// @param		remField string		Nome para o campo
	// @return		void
	//!-----------------------------------------------------------------
	function setRemovedValuesFieldName($remField) {
		if (trim($remField) != '' && $remField != $this->_SourceLookup->name && $remField != $this->_TargetLookup->name)
			$this->attributes['REMFIELD'] = $remField;
		else
			$this->attributes['REMFIELD'] = $this->name . '_removed';
		$this->_Form->verifyFieldName($this->_Form->formName, $this->attributes['REMFIELD']);
	}	
	
	//!-----------------------------------------------------------------
	// @function	LookupSelectionField::setTableWidth
	// @desc		Seta o tamanho (valor para o atributo WIDTH) da tabela
	//				construída para os campos e botões do objeto LookupSelectionField
	// @access		public
	// @param		tableWidth mixed	Tamanho da tabela
	// @return		void
	//!-----------------------------------------------------------------
	function setTableWidth($tableWidth) {
		if ($tableWidth)
			$this->attributes['TABLEWIDTH'] = " WIDTH=\"" . $tableWidth . "\"";
		else			
			$this->attributes['TABLEWIDTH'] = "";
	}
	
	//!-----------------------------------------------------------------
	// @function	LookupSelectionField::setButtonImages
	// @desc		Define imagens para os botões de ação
	// @access		public
	// @param		addAll string	Imagem para o botão "adicionar todos"
	// @param		add string		Imagem para o botão "adicionar"
	// @param		rem string		Imagem para o botão "remover"
	// @param		remAll string	Imagem para o botão "remover todos"
	// @return		void
	//!-----------------------------------------------------------------
	function setButtonImages($addAll, $add, $rem, $remAll) {
		(trim($addAll) != '') && ($this->buttonImages['ADDALL'] = $addAll);
		(trim($add) != '') && ($this->buttonImages['ADD'] = $add);		
		(trim($rem) != '') && ($this->buttonImages['REM'] = $rem);
		(trim($remAll) != '') && ($this->buttonImages['REMALL'] = $remAll);
	}
	
	//!-----------------------------------------------------------------
	// @function	LookupSelectionField::onLoadNode
	// @desc		Método responsável por processar atributos e nodos filhos
	//				provenientes da especificação XML do campo
	// @access		protected
	// @param		attrs array		Atributos do nodo
	// @param		children array	Vetor de nodos filhos
	// @return		void
	//!-----------------------------------------------------------------
	function onLoadNode($attrs, $children) {
		parent::onLoadNode($attrs, $children);
		if (isset($children['LOOKUPFIELD']) && TypeUtils::isArray($children['LOOKUPFIELD']) &&
			TypeUtils::isInstanceOf($children['LOOKUPFIELD'][0], 'xmlnode') &&
			TypeUtils::isInstanceOf($children['LOOKUPFIELD'][1], 'xmlnode')) {
			$srcLookupChildren = $children['LOOKUPFIELD'][0]->getChildrenTagsArray();
			if (!isset($srcLookupChildren['DATASOURCE']))
				PHP2Go::raiseError(PHP2Go::getLangVal('ERR_MISSING_SOURCELOOKUP_DATASOURCE', $this->name), E_USER_ERROR, __FILE__, __LINE__);
			$this->_SourceLookup =& new LookupField($this->_Form, TRUE);
			$this->_SourceLookup->onLoadNode($children['LOOKUPFIELD'][0]->getAttributes(), $children['LOOKUPFIELD'][0]->getChildrenTagsArray());
			$this->_TargetLookup =& new LookupField($this->_Form, TRUE);
			$this->_TargetLookup->onLoadNode($children['LOOKUPFIELD'][1]->getAttributes(), $children['LOOKUPFIELD'][1]->getChildrenTagsArray());
			// atributo disabled é propagado para os filhos
			$this->_SourceLookup->attributes['DISABLED'] = $this->attributes['DISABLED'];
			$this->_TargetLookup->attributes['DISABLED'] = $this->attributes['DISABLED'];
			// campo para valores inseridos
			$this->setInsertedValuesFieldName(@$attrs['INSFIELD']);
			// campo para valores removidos
			$this->setRemovedValuesFieldName(@$attrs['REMFIELD']);
			// largura da tabela
			$this->setTableWidth(@$attrs['TABLEWIDTH']);
			// imagens para os botões de ação
			$this->setButtonImages(@$attrs['ADDALLIMG'], @$attrs['ADDIMG'], @$attrs['REMIMG'], @$attrs['REMALLIMG']);
			// define valores submetidos
			if ($this->_Form->isPosted()) {
				$inserted = HttpRequest::getVar($this->attributes['INSFIELD'], $this->_Form->formMethod);
				$removed = HttpRequest::getVar($this->attributes['REMFIELD'], $this->_Form->formMethod);
				parent::setSubmittedValue(array(
					$this->attributes['INSFIELD'] => (!empty($inserted) ? explode('#', $inserted) : array()),
					$this->attributes['REMFIELD'] => (!empty($removed) ? explode('#', $removed) : array())
				));
			}			
		} else {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_MISSING_LOOKUPSELECTION_CHILDREN', $this->name), E_USER_ERROR, __FILE__, __LINE__);
		}		
	}
	
	//!-----------------------------------------------------------------
	// @function	LookupSelectionField::onPreRender
	// @desc		Configura os botões de ação do componente e configura
	//				os atributos dos campos de seleção de origem e destino
	//				que possuem restrições
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function onPreRender() {
		// configuração dos botões de ação
		$this->attributes['BUTTONS'] = array();
		$buttonMessages = PHP2Go::getLangVal('LOOKUP_SELECTION_BUTTON_TITLES');
		$imgActCode = "<BUTTON ID=\"%s\" NAME=\"%s\" TYPE=\"BUTTON\" TITLE=\"%s\" onClick=\"%s.%s();\" STYLE=\"cursor:pointer;background-color:transparent;border:none\"%s%s><IMG SRC=\"%s\" ALT=\"\" BORDER=\"0\"></BUTTON>";
		$btnActCode = "<INPUT ID=\"%s\" NAME=\"%s\" TYPE=\"BUTTON\" VALUE=\" %s \" STYLE=\"width:25px\" TITLE=\"%s\" onClick=\"%s.%s();\"%s%s%s>";
		$actHash = array(
			array('ADDALL', 'addall', 'addAll', '>>'),
			array('ADD', 'add', 'add', '>'),
			array('REM', 'rem', 'remove', '<'),
			array('REMALL', 'remall', 'removeAll', '<<')
		);
		for ($i=0; $i<sizeof($actHash); $i++) {
			if (isset($this->buttonImages[$actHash[$i][0]]))
				$this->attributes['BUTTONS'][] = sprintf($imgActCode, 
					$this->name . '_' . $actHash[$i][1], $actHash[$i][0],
					$buttonMessages[$actHash[$i][1]],
					$this->jsObjectName, $actHash[$i][2],
					$this->attributes['DISABLED'], 
					$this->_SourceLookup->attributes['TABINDEX'],
					$this->buttonImages[$actHash[$i][0]]
				);
			else
				$this->attributes['BUTTONS'][] = sprintf($btnActCode,
					$this->name . '_' . $actHash[$i][1], $actHash[$i][0],
					$actHash[$i][3], $buttonMessages[$actHash[$i][1]],
					$this->jsObjectName, $actHash[$i][2], $this->_Form->getButtonStyle(), 
					$this->attributes['DISABLED'], $this->_SourceLookup->attributes['TABINDEX']
				);
		}
		// define nome de foco
		$this->focusName = $this->_SourceLookup->getName();
		// origem não pode ter obrigatoriedade
		$this->_SourceLookup->setRequired(FALSE);
		// caixa de origem não possui primeira opção
		$this->_SourceLookup->disableFirstOption(TRUE);
		// caixa de origem deve ser múltipla
		$this->_SourceLookup->setMultiple();
		// tamanho da caixa de origem deve ser >= 2
		eregi(" SIZE=\"([0-9]+)\"", $this->_SourceLookup->getAttribute('SIZE'), $matches);
		if (!isset($matches[1]) || TypeUtils::parseInteger($matches[1]) < 2)
			$this->_SourceLookup->setSize(8);
		// destino não pode ter obrigatoriedade simples
		$this->_TargetLookup->setRequired(FALSE);
		// caixa de destino deve ter primeira opção não vazia		
		if (trim($this->_TargetLookup->getAttribute('FIRST')) == "")
			$this->_TargetLookup->setFirstOption(PHP2Go::getLangVal('LOOKUP_SELECTION_DEFAULT_SELFIRST'));
		$this->_TargetLookup->disableFirstOption(FALSE);
		// caixa de destino deve ser de seleção múltipla
		$this->_TargetLookup->setMultiple();
		// tamanho da caixa de destino deve ser >= 2
		eregi(" SIZE=\"([0-9]+)\"", $this->_TargetLookup->getAttribute('SIZE'), $matches);
		if (!isset($matches[1]) || TypeUtils::parseInteger($matches[1]) < 2)
			$this->_TargetLookup->setSize(8);
		// datasource da caixa destino não pode ter agrupamento
		$this->_TargetLookup->isGrouping = FALSE;
		// controle de obrigatoriedade
		if ($this->required)
			$this->_Form->appendScript(sprintf("          validator.addLookupCheck(\"%s\", \"%s\", %d);\n", $this->_TargetLookup->name, $this->_TargetLookup->label, 2));
	}
}
?>