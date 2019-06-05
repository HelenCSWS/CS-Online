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
// $Header: /www/cvsroot/php2go/core/form/FormBasic.class.php,v 1.28 2005/08/30 18:50:00 mpont Exp $
// $Date: 2005/08/30 18:50:00 $

//------------------------------------------------------------------
import('php2go.form.Form');
//------------------------------------------------------------------

//!-----------------------------------------------------------------
// @class		FormBasic
// @desc		Esta classe extende a classe Form, que interpreta a
// 				especificação XML do formulário e armazena a informação
//				extraída em uma estrutura de dados, gerando uma interface
//				pré-definida para as seções, campos e botões encontrados
// @package		php2go.form
// @extends		Form
// @uses		HttpRequest
// @uses		Template
// @uses		TypeUtils
// @uses		UserAgent
// @author		Marcos Pont
// @version		$Revision: 1.28 $
// @note		Exemplo de uso:
//				<PRE>
//
//				$Form = new FormBasic("file.xml", "myForm", $Doc);
//				$Form->setFormMethod("POST");
//				$Form->setFormAlign("center");
//				$Form->setFormWidth(500);
//				$Form->setFormAction("anotherpage.php");
//				$Form->setFormActionTarget("_blank");
//				$Form->setInputStyle("input_style");
//				$Form->setLabelWidth(0.35);
//				$Form->setLabelAlign("right");
//				$content = $Form->getContent();
//
//				</PRE>
//!-----------------------------------------------------------------
class FormBasic extends Form 
{
	var $formAlign = 'left';			// @var formAlign string			"left" Define o alinhamento do código do formulário
	var $formWidth;						// @var formWidth string			Largura do formulário, expressa em número de pixels
	var $labelW = 0.2; 					// @var labelW float				"0.2" Largura da coluna dos rótulos, entre 0 e 1, em relação ao tamanho total da tabela do formulário
	var $labelAlign = 'right'; 			// @var labelAlign string			"right" Alinhamento dos rótulos: left, right, ...
	var $fieldSetStyle;					// @var fieldSetStyle string		Estilo para os fieldsets criados para representar as sections
	var $sectionTitleStyle;				// @var sectionTitleStyle string	Estilo para os títulos das seções
	var $tblCPadding = 3; 				// @var tblCPadding int				"3" Espaçamento interno dos campos em relação à coluna onde eles estão inseridos
	var $tblCSpacing = 2; 				// @var tblCSpacing int				"2" Espaçamento entre as células da tabela do formulário
	var $formCode = ''; 				// @var formCode string				"" Código HTML do formulário	
	var $_Template; 					// @var _Template Template object	Objeto Template para construção da interface do formulário

	//!-----------------------------------------------------------------
	// @function	FormBasic::FormBasic
	// @desc		Construtor da classe FormBasic. Executa o construtor
	// 				da classe pai, que gera a estrutura de dados do formulário,
	// 				e instancia o template de interface pré-definida que será
	// 				utilizado
	// @access 		public
	// @param 		xmlFile string	Arquivo XML da especificação do formulário
	// @param 		formName string	Nome do formulário
	// @param 		&Document Document object	Objeto Document onde o formulário será inserido
	//!-----------------------------------------------------------------
	function FormBasic($xmlFile, $formName, &$Document) {
		parent::Form($xmlFile, $formName, $Document);
		$this->_Template = new Template(PHP2GO_TEMPLATE_PATH . "basicform.tpl");
		$this->_Template->parse();
	}
	
	//!-----------------------------------------------------------------
	// @function	FormBasic::getFieldsetStyle
	// @desc		Monta a definição CSS configurada para a tag FIELDSET das seções
	// @access		public
	// @return		string Definição do estilo
	//!-----------------------------------------------------------------
	function getFieldsetStyle() {
		if (!empty($this->fieldSetStyle))
			return " CLASS=\"{$this->fieldSetStyle}\"";
		return '';
	}
	
	//!-----------------------------------------------------------------
	// @function	FormBasic::setFieldsetStyle
	// @desc		Define o estilo CSS interno dos fieldsets criados para representar as sections do formulário
	// @access		public
	// @param		style string		Nome do estilo CSS
	// @return		void
	//!-----------------------------------------------------------------
	function setFieldsetStyle($style) {
		$this->fieldSetStyle = $style;
	}
	
	//!-----------------------------------------------------------------
	// @function	FormBasic::getSectionTitleStyle
	// @desc		Monta a definição CSS configurada para os títulos das seções
	// @access		public
	// @return		string Definição do estilo
	//!-----------------------------------------------------------------
	function getSectionTitleStyle() {
		if (!empty($this->sectionTitleStyle))
			return " CLASS=\"{$this->sectionTitleStyle}\"";
		return '';
	}

	//!-----------------------------------------------------------------
	// @function	FormBasic::setSectionTitleStyle
	// @desc		Configura o estilo CSS para os títulos das sections
	// @access		public
	// @param		style string		Nome do estilo CSS
	// @return		void
	//!-----------------------------------------------------------------
	function setSectionTitleStyle($style) {
		$this->sectionTitleStyle = $style;
	}

	//!-----------------------------------------------------------------
	// @function	FormBasic::setFormAlign
	// @desc		Configura o alinhamento do formulário em relação ao elemento
	//				onde ele será inserido dentro do documento HTML
	// @access		public
	// @param		align string		Alinhamento para o formulário
	// @return		void	
	//!-----------------------------------------------------------------
	function setFormAlign($align) {
		$this->formAlign = $align;
	}
	
	//!-----------------------------------------------------------------
	// @function	FormBasic::setFormWidth
	// @desc		Define a largura da tabela externa ao formulário
	// @access		public
	// @param		width int			Largura, expressa em número de pixels
	// @return		void
	// @note		Podem ser utilizados valores do tipo "100%", "95%", "500px", "600"
	//!-----------------------------------------------------------------
	function setFormWidth($width) {
		$this->formWidth = $width;
	}

	//!-----------------------------------------------------------------
	// @function	FormBasic::setLabelWidth
	// @desc		Configura o tamanho que os rótulos do formulário terão
	// 				em proporção ao tamanho total da tabela. Aceita valores
	// 				decimais de 0 a 1 (Exemplos: 0.2, 0.25, 0.3)
	// @access		public
	// @param 		width float		Tamanho dos rótulos, de 0 a 1
	// @return		void
	// @see 		FormBasic::setLabelAlign
	//!-----------------------------------------------------------------
	function setLabelWidth($width) 	{
		if (TypeUtils::parseFloatPositive($width) > 1 || TypeUtils::parseFloatPositive($width) <= 0) {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_VALUE_OUT_OF_BOUNDS', array("width (FormBasic::setLabelWidth)", 0, 1)), E_USER_ERROR, __FILE__, __LINE__);
		} else {
			$this->labelW = $width;
		}
	}

	//!-----------------------------------------------------------------
	// @function	FormBasic::setLabelAlign
	// @desc		Configura o alinhamento dos rótulos do formulário
	// @access		public
	// @param		align string		Alinhamento: left, center, right
	// @return		void	
	// @see 		FormBasic::setLabelWidth
	//!-----------------------------------------------------------------
	function setLabelAlign($align) {
		$this->labelAlign = $align;
	}
	
	//!-----------------------------------------------------------------
	// @function	FormBasic::setFormTableProperties
	// @desc		Configura as propriedades da tabela que conterá os
	// 				campos e botões do formulário quanto ao espaçamento
	// 				entre as células e interno às células
	// @access 		public
	// @param 		cellpadding int	Espaçamento interno das células
	// @param 		cellspacing int	Espaçamento entre as células
	// @return		void	
	//!-----------------------------------------------------------------
	function setFormTableProperties($cellpadding, $cellspacing) {
		$this->tblCPadding = TypeUtils::parseIntegerPositive($cellpadding);
		$this->tblCSpacing = TypeUtils::parseIntegerPositive($cellspacing);
	}
	
	//!-----------------------------------------------------------------
	// @function	FormBasic::setErrorDisplayOptions
	// @desc		Define o modo de exibição dos erros client-side
	// @param		mode int	Modo de exibição
	// @access		public	
	// @return		void
	// @note		Os valores possíveis para o parâmetro $mode são 
	//				FORM_CLIENT_ERROR_ALERT e FORM_CLIENT_ERROR_DHTML
	//!-----------------------------------------------------------------
	function setErrorDisplayOptions($mode) {
		if (in_array($mode, array(FORM_CLIENT_ERROR_ALERT, FORM_CLIENT_ERROR_DHTML))) {
			$this->clientErrorOptions['mode'] = $mode;
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	FormBasic::loadGlobalSettings
	// @desc		Define opções de apresentação do formulário a partir das
	//				configurações globais, se existentes
	// @param		settings array	Conjunto de configurações globais
	// @access		protected
	// @return		void
	//!-----------------------------------------------------------------
	function loadGlobalSettings($settings) {
		parent::loadGlobalSettings($settings);
		if (isset($settings['ERRORS']['CLIENT_MODE'])) {
			$mode = @constant($settings['ERRORS']['CLIENT_MODE']);
			if (!TypeUtils::isNull($mode, TRUE))
				$this->setErrorDisplayOptions($mode);
		}
		if (isset($settings['BASIC'])) {
			$basic = $settings['BASIC'];
			(isset($basic['FIELDSET_STYLE'])) && $this->setFieldsetStyle($basic['FIELDSET_STYLE']);
			(isset($basic['SECTION_TITLE_STYLE'])) && $this->setSectionTitleStyle($basic['SECTION_TITLE_STYLE']);
			(isset($basic['ALIGN'])) && $this->setFormAlign($basic['ALIGN']);
			(isset($basic['WIDTH'])) && $this->setFormWidth($basic['WIDTH']);
			(isset($basic['LABEL_ALIGN'])) && $this->setLabelAlign($basic['LABEL_ALIGN']);
			(isset($basic['LABEL_WIDTH'])) && $this->setLabelWidth($basic['LABEL_WIDTH']);
			(isset($basic['TABLE_PADDING']) && isset($basic['TABLE_SPACING'])) && $this->setFormTableProperties($basic['TABLE_PADDING'], $basic['TABLE_SPACING']);
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	FormBasic::display
	// @desc		Constrói e imprime o código HTML do formulário
	// @access		public
	// @return		void	
	// @see			FormBasic::getContent
	//!-----------------------------------------------------------------
	function display() {
		print $this->getContent();
	}
	
	//!-----------------------------------------------------------------
	// @function	FormBasic::getContent
	// @desc		Constrói e retorna o código HTML do formulário
	// @access		public
	// @return		string Código HTML do formulário
	// @see			FormBasic::display
	//!-----------------------------------------------------------------
	function getContent() {
		if (!$this->formConstruct) 
			parent::processXml();
		$this->_buildFormInterface();
		parent::buildScriptCode();
		$this->_buildFormCode();		
		return $this->formCode;
	}

	//!-----------------------------------------------------------------
	// @function	FormBasic::_buildFormInterface
	// @desc		Esta função constrói a interface do formulário a partir
	// 				da estrutura gerada pela classe Form e de um template
	// 				pré-definido de interface
	// @access		public
	// @return		void	
	//!-----------------------------------------------------------------
	function _buildFormInterface() {
		$this->_Template->assign('_ROOT.errorStyle', parent::getErrorStyle());
		$this->_Template->assign('_ROOT.errorTitle', @$this->errorStyle['header_text']);
		// exibe erros da validação server-side
		if ($errors = parent::getFormErrors()) {
			$mode = @$this->errorStyle['list_mode'];
			$errors = ($mode == FORM_ERROR_BULLET_LIST ? "<UL><LI>" . implode("</LI><LI>", $errors) . "</LI></UL>" : implode("<BR>", $errors));
			$this->_Template->assign('_ROOT.errorDisplay', " STYLE=\"display:block\"");
			$this->_Template->assign('_ROOT.errorMessages', $errors);
		} else {
			$this->_Template->assign('_ROOT.errorDisplay', " STYLE=\"display:none\"");
		}
		// configura exibição de erros client-side
		if ($this->clientErrorOptions['mode'] == FORM_CLIENT_ERROR_DHTML)
			$this->clientErrorOptions['placeholder'] = 'form_client_errors';
		// verifica se a classe deve trabalhar em modo de compatibilidade (browsers antigos)
		$Agent =& UserAgent::getInstance();
		$compatMode = ($Agent->matchBrowserList(array('ie5+', 'ns6+', 'opera5+')) === FALSE);		
		foreach($this->sections as $sectionId => $section) {
			if ($section->show && $section->hasChildren()) {
				// criação do bloco
				$this->_Template->createBlock('loop_section');
				// legenda do fielset, dependente do browser
				if ($section->name != '') {
					if (!$compatMode) {
						$this->_Template->createBlock('section_name');
						$this->_Template->assign('loop_section.fieldset_style', $this->getFieldsetStyle());
					} else {
						$this->_Template->createBlock('section_name_browser_compat');
						$this->_Template->assign('_ROOT.sectable_style', $this->getFieldsetStyle());
					}
					// compatibilidade com versões anteriores
					$cssStyle = (!empty($this->sectionTitleStyle) ? $this->getSectionTitleStyle() : parent::getLabelStyle());
					$this->_Template->assign('name', sprintf('<SPAN%s>%s</SPAN>', $cssStyle, $section->name));
				}
				// alinhamento da tabela
				if (isset($this->formAlign) && $this->formAlign != "")
					$this->_Template->assign("loop_section.tblAlign", $this->formAlign);				
				// configuração da tabela
				$this->_Template->assign("loop_section.tblCPadding", $this->tblCPadding);
				$this->_Template->assign("loop_section.tblCSpacing", $this->tblCSpacing);
				// gera as subseções e os campos da seção
				$buttons = array();
				for ($i = 0; $i < sizeOf($section->getChildren()); $i++) {
					$object =& $section->getChild($i);
					if ($section->getChildType($i) == 'SECTION') {
						$this->_buildSubSection($object);
					} elseif ($section->getChildType($i) == 'BUTTON') {
						$this->_Template->createAndAssign('button', 'button', $object->getCode());
					} elseif ($section->getChildType($i) == 'BUTTONGROUP') {
						$this->_buildButtonGroup($object);
					} elseif ($section->getChildType($i) == 'FIELD') {
						if ($object->getFieldTag() == 'HIDDENFIELD') {
							$this->_Template->createAndAssign('hidden_field', 'field', $object->getCode());
						} else {
							$this->_Template->createBlock("loop_field");
							$this->_Template->assign("labelW", ($this->labelW * 100) . '%');
							$this->_Template->assign("labelAlign", $this->labelAlign);
							$this->_Template->assign("label", $object->getLabelCode($section->attrs['REQUIRED_FLAG'], $section->attrs['REQUIRED_COLOR'], $section->attrs['REQUIRED_TEXT']));
							$this->_Template->assign("fieldW", (100 - ($this->labelW * 100)) . '%');
							$this->_Template->assign("field", $object->getCode(&$this));
							if ($this->helpOptions['mode'] == FORM_HELP_POPUP)
								$this->_Template->assign('popup_help', "&nbsp;" . $object->getHelpCode());
							else
								$this->_Template->assign('inline_help', "<BR>" . $object->getHelpCode());
						}						
					}
				}
			}
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	FormBasic::_buildSubSection
	// @desc		Método recursivo que constrói as subseções condicionais
	//				definidas na especificação XML do formulário
	// @access		private
	// @param		subSection FormSection object	Subseção do formulário
	// @return		void
	//!-----------------------------------------------------------------
	function _buildSubSection($subSection) {
		if ($subSection->show) {
			for ($i = 0; $i < sizeOf($subSection->getChildren()); $i++) {
				$object =& $subSection->getChild($i);
				if ($subSection->getChildType($i) == 'SECTION') {
					$this->_buildSubSection($object);
				} elseif ($subSection->getChildType($i) == 'BUTTON') {
					$this->_Template->createAndAssign('button', 'button', $object->getCode());
				} elseif ($subSection->getChildType($i) == 'BUTTONGROUP') {
					$this->_buildButtonGroup($object);
				} else {
					if ($object->getFieldTag() == 'HIDDENFIELD') {
						$this->_Template->createAndAssign('hidden_field', 'field', $object->getCode());
					} else {
						$this->_Template->createBlock("loop_field");
						$this->_Template->assign("labelW", ($this->labelW * 100) . '%');
						$this->_Template->assign("labelAlign", $this->labelAlign);
						$this->_Template->assign("label", $object->getLabelCode($subSection->attrs['REQUIRED_FLAG'], $subSection->attrs['REQUIRED_COLOR'], $subSection->attrs['REQUIRED_TEXT']));
						$this->_Template->assign("fieldW", (100 - ($this->labelW * 100)) . '%');
						$this->_Template->assign("field", $object->getCode(&$this));
						if ($this->helpOptions['mode'] == FORM_HELP_POPUP)
							$this->_Template->assign('popup_help', "&nbsp;" . $object->getHelpCode());
						else
							$this->_Template->assign('inline_help', "<BR>" . $object->getHelpCode());							
					}
				}
			}
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	FormBasic::_buildButtonGroup
	// @desc		Constrói um grupo de botões (tag BUTTONS declarada no arquivo XML)
	// @access		private
	// @param		buttonGroup array	Vetor de objetos do tipo FormButton
	// @return		void
	//!-----------------------------------------------------------------
	function _buildButtonGroup($buttonGroup) {
		if (sizeOf($buttonGroup) > 0) {
			$this->_Template->createBlock("button_group");
			for ($j = 0; $j < sizeOf($buttonGroup); $j++) {
				$this->_Template->createBlock("loop_button_group");
				$this->_Template->assign("btnW", round(100 / sizeOf($buttonGroup)) . '%');
				$this->_Template->assign("button", $buttonGroup[$j]->getCode());
			}
		}		
	}

	//!-----------------------------------------------------------------
	// @function	FormBasic::_buildFormCode
	// @desc		Gera a camada externa de código do formulário e insere
	// 				o nela o conteúdo extraído do template. Armazena o
	// 				código HTML gerado na propriedade $formCode da classe
	// @access 		private
	// @return		void	
	//!-----------------------------------------------------------------
	function _buildFormCode() {		
		$WIDTH = (isset($this->formWidth) && $this->formWidth != "" ? " WIDTH=\"" . $this->formWidth . "\"" : "");
		$this->_Template->assign("_ROOT.formWidth", $WIDTH);	
		$TARGET = (isset($this->actionTarget) ? " TARGET=\"" . $this->actionTarget . "\"" : '');
		$ENCTYPE = ($this->hasUpload ? " ENCTYPE=\"multipart/form-data\"" : '');		
		$RESET = (!empty($this->resetCode) ? " onReset=\"return " . $this->formName . "_reset()\"" : "");
		$SIGNATURE = sprintf("<INPUT TYPE=\"hidden\" ID=\"%s\" NAME=\"__form_signature\" VALUE=\"%s\"/>", FORM_SIGNATURE, parent::getSignature());
		$this->formCode .= sprintf("<FORM ID=\"%s\" NAME=\"%s\" ACTION=\"%s\" METHOD=\"%s\" STYLE=\"display:inline\"%s%s onSubmit=\"return %s_submit();\"%s>%s\n", 
			$this->formName, $this->formName, $this->formAction, $this->formMethod, 
			$TARGET, $ENCTYPE, $this->formName, $RESET, $SIGNATURE);
		$this->formCode .= $this->_Template->getContent();
		$this->formCode .= "</FORM>\n";
	}
}
?>