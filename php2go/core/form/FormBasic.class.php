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
// 				especifica��o XML do formul�rio e armazena a informa��o
//				extra�da em uma estrutura de dados, gerando uma interface
//				pr�-definida para as se��es, campos e bot�es encontrados
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
	var $formAlign = 'left';			// @var formAlign string			"left" Define o alinhamento do c�digo do formul�rio
	var $formWidth;						// @var formWidth string			Largura do formul�rio, expressa em n�mero de pixels
	var $labelW = 0.2; 					// @var labelW float				"0.2" Largura da coluna dos r�tulos, entre 0 e 1, em rela��o ao tamanho total da tabela do formul�rio
	var $labelAlign = 'right'; 			// @var labelAlign string			"right" Alinhamento dos r�tulos: left, right, ...
	var $fieldSetStyle;					// @var fieldSetStyle string		Estilo para os fieldsets criados para representar as sections
	var $sectionTitleStyle;				// @var sectionTitleStyle string	Estilo para os t�tulos das se��es
	var $tblCPadding = 3; 				// @var tblCPadding int				"3" Espa�amento interno dos campos em rela��o � coluna onde eles est�o inseridos
	var $tblCSpacing = 2; 				// @var tblCSpacing int				"2" Espa�amento entre as c�lulas da tabela do formul�rio
	var $formCode = ''; 				// @var formCode string				"" C�digo HTML do formul�rio	
	var $_Template; 					// @var _Template Template object	Objeto Template para constru��o da interface do formul�rio

	//!-----------------------------------------------------------------
	// @function	FormBasic::FormBasic
	// @desc		Construtor da classe FormBasic. Executa o construtor
	// 				da classe pai, que gera a estrutura de dados do formul�rio,
	// 				e instancia o template de interface pr�-definida que ser�
	// 				utilizado
	// @access 		public
	// @param 		xmlFile string	Arquivo XML da especifica��o do formul�rio
	// @param 		formName string	Nome do formul�rio
	// @param 		&Document Document object	Objeto Document onde o formul�rio ser� inserido
	//!-----------------------------------------------------------------
	function FormBasic($xmlFile, $formName, &$Document) {
		parent::Form($xmlFile, $formName, $Document);
		$this->_Template = new Template(PHP2GO_TEMPLATE_PATH . "basicform.tpl");
		$this->_Template->parse();
	}
	
	//!-----------------------------------------------------------------
	// @function	FormBasic::getFieldsetStyle
	// @desc		Monta a defini��o CSS configurada para a tag FIELDSET das se��es
	// @access		public
	// @return		string Defini��o do estilo
	//!-----------------------------------------------------------------
	function getFieldsetStyle() {
		if (!empty($this->fieldSetStyle))
			return " CLASS=\"{$this->fieldSetStyle}\"";
		return '';
	}
	
	//!-----------------------------------------------------------------
	// @function	FormBasic::setFieldsetStyle
	// @desc		Define o estilo CSS interno dos fieldsets criados para representar as sections do formul�rio
	// @access		public
	// @param		style string		Nome do estilo CSS
	// @return		void
	//!-----------------------------------------------------------------
	function setFieldsetStyle($style) {
		$this->fieldSetStyle = $style;
	}
	
	//!-----------------------------------------------------------------
	// @function	FormBasic::getSectionTitleStyle
	// @desc		Monta a defini��o CSS configurada para os t�tulos das se��es
	// @access		public
	// @return		string Defini��o do estilo
	//!-----------------------------------------------------------------
	function getSectionTitleStyle() {
		if (!empty($this->sectionTitleStyle))
			return " CLASS=\"{$this->sectionTitleStyle}\"";
		return '';
	}

	//!-----------------------------------------------------------------
	// @function	FormBasic::setSectionTitleStyle
	// @desc		Configura o estilo CSS para os t�tulos das sections
	// @access		public
	// @param		style string		Nome do estilo CSS
	// @return		void
	//!-----------------------------------------------------------------
	function setSectionTitleStyle($style) {
		$this->sectionTitleStyle = $style;
	}

	//!-----------------------------------------------------------------
	// @function	FormBasic::setFormAlign
	// @desc		Configura o alinhamento do formul�rio em rela��o ao elemento
	//				onde ele ser� inserido dentro do documento HTML
	// @access		public
	// @param		align string		Alinhamento para o formul�rio
	// @return		void	
	//!-----------------------------------------------------------------
	function setFormAlign($align) {
		$this->formAlign = $align;
	}
	
	//!-----------------------------------------------------------------
	// @function	FormBasic::setFormWidth
	// @desc		Define a largura da tabela externa ao formul�rio
	// @access		public
	// @param		width int			Largura, expressa em n�mero de pixels
	// @return		void
	// @note		Podem ser utilizados valores do tipo "100%", "95%", "500px", "600"
	//!-----------------------------------------------------------------
	function setFormWidth($width) {
		$this->formWidth = $width;
	}

	//!-----------------------------------------------------------------
	// @function	FormBasic::setLabelWidth
	// @desc		Configura o tamanho que os r�tulos do formul�rio ter�o
	// 				em propor��o ao tamanho total da tabela. Aceita valores
	// 				decimais de 0 a 1 (Exemplos: 0.2, 0.25, 0.3)
	// @access		public
	// @param 		width float		Tamanho dos r�tulos, de 0 a 1
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
	// @desc		Configura o alinhamento dos r�tulos do formul�rio
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
	// @desc		Configura as propriedades da tabela que conter� os
	// 				campos e bot�es do formul�rio quanto ao espa�amento
	// 				entre as c�lulas e interno �s c�lulas
	// @access 		public
	// @param 		cellpadding int	Espa�amento interno das c�lulas
	// @param 		cellspacing int	Espa�amento entre as c�lulas
	// @return		void	
	//!-----------------------------------------------------------------
	function setFormTableProperties($cellpadding, $cellspacing) {
		$this->tblCPadding = TypeUtils::parseIntegerPositive($cellpadding);
		$this->tblCSpacing = TypeUtils::parseIntegerPositive($cellspacing);
	}
	
	//!-----------------------------------------------------------------
	// @function	FormBasic::setErrorDisplayOptions
	// @desc		Define o modo de exibi��o dos erros client-side
	// @param		mode int	Modo de exibi��o
	// @access		public	
	// @return		void
	// @note		Os valores poss�veis para o par�metro $mode s�o 
	//				FORM_CLIENT_ERROR_ALERT e FORM_CLIENT_ERROR_DHTML
	//!-----------------------------------------------------------------
	function setErrorDisplayOptions($mode) {
		if (in_array($mode, array(FORM_CLIENT_ERROR_ALERT, FORM_CLIENT_ERROR_DHTML))) {
			$this->clientErrorOptions['mode'] = $mode;
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	FormBasic::loadGlobalSettings
	// @desc		Define op��es de apresenta��o do formul�rio a partir das
	//				configura��es globais, se existentes
	// @param		settings array	Conjunto de configura��es globais
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
	// @desc		Constr�i e imprime o c�digo HTML do formul�rio
	// @access		public
	// @return		void	
	// @see			FormBasic::getContent
	//!-----------------------------------------------------------------
	function display() {
		print $this->getContent();
	}
	
	//!-----------------------------------------------------------------
	// @function	FormBasic::getContent
	// @desc		Constr�i e retorna o c�digo HTML do formul�rio
	// @access		public
	// @return		string C�digo HTML do formul�rio
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
	// @desc		Esta fun��o constr�i a interface do formul�rio a partir
	// 				da estrutura gerada pela classe Form e de um template
	// 				pr�-definido de interface
	// @access		public
	// @return		void	
	//!-----------------------------------------------------------------
	function _buildFormInterface() {
		$this->_Template->assign('_ROOT.errorStyle', parent::getErrorStyle());
		$this->_Template->assign('_ROOT.errorTitle', @$this->errorStyle['header_text']);
		// exibe erros da valida��o server-side
		if ($errors = parent::getFormErrors()) {
			$mode = @$this->errorStyle['list_mode'];
			$errors = ($mode == FORM_ERROR_BULLET_LIST ? "<UL><LI>" . implode("</LI><LI>", $errors) . "</LI></UL>" : implode("<BR>", $errors));
			$this->_Template->assign('_ROOT.errorDisplay', " STYLE=\"display:block\"");
			$this->_Template->assign('_ROOT.errorMessages', $errors);
		} else {
			$this->_Template->assign('_ROOT.errorDisplay', " STYLE=\"display:none\"");
		}
		// configura exibi��o de erros client-side
		if ($this->clientErrorOptions['mode'] == FORM_CLIENT_ERROR_DHTML)
			$this->clientErrorOptions['placeholder'] = 'form_client_errors';
		// verifica se a classe deve trabalhar em modo de compatibilidade (browsers antigos)
		$Agent =& UserAgent::getInstance();
		$compatMode = ($Agent->matchBrowserList(array('ie5+', 'ns6+', 'opera5+')) === FALSE);		
		foreach($this->sections as $sectionId => $section) {
			if ($section->show && $section->hasChildren()) {
				// cria��o do bloco
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
					// compatibilidade com vers�es anteriores
					$cssStyle = (!empty($this->sectionTitleStyle) ? $this->getSectionTitleStyle() : parent::getLabelStyle());
					$this->_Template->assign('name', sprintf('<SPAN%s>%s</SPAN>', $cssStyle, $section->name));
				}
				// alinhamento da tabela
				if (isset($this->formAlign) && $this->formAlign != "")
					$this->_Template->assign("loop_section.tblAlign", $this->formAlign);				
				// configura��o da tabela
				$this->_Template->assign("loop_section.tblCPadding", $this->tblCPadding);
				$this->_Template->assign("loop_section.tblCSpacing", $this->tblCSpacing);
				// gera as subse��es e os campos da se��o
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
	// @desc		M�todo recursivo que constr�i as subse��es condicionais
	//				definidas na especifica��o XML do formul�rio
	// @access		private
	// @param		subSection FormSection object	Subse��o do formul�rio
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
	// @desc		Constr�i um grupo de bot�es (tag BUTTONS declarada no arquivo XML)
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
	// @desc		Gera a camada externa de c�digo do formul�rio e insere
	// 				o nela o conte�do extra�do do template. Armazena o
	// 				c�digo HTML gerado na propriedade $formCode da classe
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