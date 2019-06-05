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
// $Header: /www/cvsroot/php2go/core/form/FormTemplate.class.php,v 1.29 2005/08/30 18:50:00 mpont Exp $
// $Date: 2005/08/30 18:50:00 $

//------------------------------------------------------------------
import('php2go.form.Form');
import('php2go.template.Template');
//------------------------------------------------------------------

//!-----------------------------------------------------------------
// @class 		FormTemplate
// @desc 		Esta classe é uma das extensões da classe que constrói
// 				formulários que gera o código final integrando a estrutura
// 				de dados já montada pela classe pai com um template que
// 				define a disposição dos elementos
// @package		php2go.form
// @extends 	Form
// @uses 		Template
// @author 		Marcos Pont
// @version		$Revision: 1.29 $
// @note		Exemplo de uso:
//				<PRE>
//
//				$form = new FormTemplate('file.xml', 'file.tpl', 'formName', $Doc);
//				$form->setFormMethod('POST');
//				$form->setInputStyle('input_style');
//				$content = $form->getContent();
//
//				</PRE>
//!-----------------------------------------------------------------
class FormTemplate extends Form
{
	var $templateFile;				// @var templateFile string			Nome do arquivo template para construção do formulário
	var $Template; 					// @var Template Template object	Objeto Template para manipulação da interface do formulário
	var $formCode = ''; 			// @var formCode string				"" Código HTML do formulário
	var $errorPlaceHolder;			// @var errorPlaceHolder string		Nome da variável para exibição dos erros de validação

	//!-----------------------------------------------------------------
	// @function 	FormTemplate::FormTemplate
	// @desc 		Construtor da classe FormTemplate. Inicializa a configuração
	// 				do formulário controlada por este objeto e cria uma instância
	// 				da classe Template para integrar com a especificação XML definida
	// 				em $xmlFile
	// @access 		public
	// @param 		xmlFile string				Arquivo XML da especificação do formulário
	// @param 		templateFile string			Arquivo template para geração da interface do formulário
	// @param 		formName string				Nome do formulário
	// @param 		&Document Document object	Objeto Document onde o formulário será inserido
	// @param		tplIncludes array			"array()" Vetor de valores para blocos de inclusão no template
	//!-----------------------------------------------------------------
	function FormTemplate($xmlFile, $templateFile, $formName, &$Document, $tplIncludes=array()) {
		parent::Form($xmlFile, $formName, $Document);
		$this->Template = new Template($templateFile);
		if (TypeUtils::isHashArray($tplIncludes) && !empty($tplIncludes)) {			
			foreach ($tplIncludes as $blockName => $blockValue)
				$this->Template->includeAssign($blockName, $blockValue, T_BYFILE);
		}		
		$this->Template->parse();
	}
	
	//!-----------------------------------------------------------------
	// @function	FormTemplate::setErrorDisplayOptions
	// @desc		Define o modo de exibição dos erros na validação client-side
	// @access		public
	// @param		serverPlaceHolder string	Variável do template para exibição dos erros de validação do servidor
	// @param		clientMode int				Modo de exibição de erros client-side
	// @param		clientContainerId string	"" ID do container (elemento HTML) para exibição dos erros client-side
	// @return		void
	// @note		Os valores possíveis para $clientMode são FORM_CLIENT_ERROR_ALERT e FORM_CLIENT_ERROR_DHTML
	//!-----------------------------------------------------------------
	function setErrorDisplayOptions($serverPlaceHolder, $clientMode, $clientContainerId='') {
		$this->errorPlaceHolder = $serverPlaceHolder;
		if ($clientMode == FORM_CLIENT_ERROR_DHTML && !empty($clientContainerId)) {
			$this->clientErrorOptions = array(
				'mode' => FORM_CLIENT_ERROR_DHTML,
				'placeholder' => $clientContainerId
			);
		} else {
			$this->clientErrorOptions = array(
				'mode' => FORM_CLIENT_ERROR_ALERT
			);
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	FormTemplate::loadGlobalSettings
	// @desc		Define opções de apresentação a partir das configurações globais, se existentes
	// @param		settings array	Conjunto de configurações globais
	// @access		protected
	// @return		void
	//!-----------------------------------------------------------------
	function loadGlobalSettings($settings) {
		parent::loadGlobalSettings($settings);
		if (isset($settings['ERRORS']['CLIENT_MODE']) && isset($settings['ERRORS']['TEMPLATE_PLACEHOLDER'])) {
			$mode = @constant($settings['ERRORS']['CLIENT_MODE']);
			if (!TypeUtils::isNull($mode, TRUE))
				$this->setErrorDisplayOptions($settings['ERRORS']['TEMPLATE_PLACEHOLDER'], $mode, @$settings['ERRORS']['CLIENT_CONTAINER']);
		}
	}
	
	//!-----------------------------------------------------------------
	// @function 	FormTemplate::display
	// @desc 		Constrói e imprime o código HTML do formulário
	// @access 		public
	// @return		void	
	// @see 		FormTemplate::getContent
	//!-----------------------------------------------------------------
	function display() {
		print $this->getContent();
	}

	//!-----------------------------------------------------------------
	// @function 	FormTemplate::getContent
	// @desc 		Constrói e retorna o código HTML do formulário
	// @access 		public
	// @return 		string Código HTML do Formulário
	// @see 		FormTemplate::display
	//!-----------------------------------------------------------------
	function getContent() {
		if (!$this->formConstruct) 
			parent::processXml();
		$this->_buildErrors();
		foreach($this->sections as $section)
			$this->_buildSection($section);
		parent::buildScriptCode();
		$this->_buildFormCode();		
		return $this->formCode;
	}
	
	//!-----------------------------------------------------------------
	// @function	FormTemplate::_buildErrors
	// @desc		Exibe os erros resultantes de validações realizadas no formulário
	// @access		private
	// @return		void
	//!-----------------------------------------------------------------
	function _buildErrors() {
		$this->Template->setCurrentBlock(TP_ROOTBLOCK);		
		$this->Template->assign('errorStyle', parent::getErrorStyle());
		if (isset($this->errorPlaceHolder) && ($errors = parent::getFormErrors())) {
			$mode = @$this->errorStyle['list_mode'];
			$errors = ($mode == FORM_ERROR_BULLET_LIST ? "<UL><LI>" . implode("</LI><LI>", $errors) . "</LI></UL>" : implode("<BR>", $errors));
			$this->Template->assign('errorDisplay', " STYLE=\"display:block\"");			
			$this->Template->assign($this->errorPlaceHolder, @$this->errorStyle['header_text'] . $errors);
		} else {			
			$this->Template->assign('errorDisplay', " STYLE=\"display:none\"");
		}
	}

	//!-----------------------------------------------------------------
	// @function	FormTemplate::_buildSection
	// @desc		Aplica no template os rótulos e códigos dos campos e botões
	//				referente a suma seção do formulário
	// @access 		private
	// @param		section FormSection object	Seção do formulário
	// @return		void	
	//!-----------------------------------------------------------------
	function _buildSection($section) {
		if (TypeUtils::isInstanceOf($section, 'formsection')) {
			$sectionId = $section->getId();
			if ($section->isConditional()) {				
				if (!$this->Template->isBlockDefined($sectionId))
					PHP2Go::raiseError(PHP2Go::getLangVal('ERR_FORM_SECTION_TPLBLOCK', array($section->getId(), $section->getId())), E_USER_ERROR, __FILE__, __LINE__);
				if ($section->show) {
					$this->Template->createBlock($sectionId);
					$this->Template->assign("$sectionId.section_" . $sectionId, $section->name);
					for ($i = 0; $i < sizeOf($section->getChildren()); $i++) {
						$object =& $section->getChild($i);						
						if ($section->getChildType($i) == 'SECTION') {
							$this->_buildSection($object);
						} else if ($section->getChildType($i) == 'BUTTON') {
							$this->Template->assign("$sectionId." . $object->getName(), $object->getCode());
						} else if ($section->getChildType($i) == 'BUTTONGROUP') {
							for ($i=0; $i<sizeOf($object); $i++) {
								$this->Template->assign("{$sectionId}." . $object[$i]->getName(), $object[$i]->getCode());
							}
						} else if ($section->getChildType($i) == 'FIELD') {
							$this->Template->assign("{$sectionId}.label_" . $object->getName(), $object->getLabelCode($section->attrs['REQUIRED_FLAG'], $section->attrs['REQUIRED_COLOR'], $section->attrs['REQUIRED_TEXT']));
							$this->Template->assign("{$sectionId}.help_" . $object->getName(), $object->getHelpCode());
							$this->Template->assign("{$sectionId}." . $object->getName(), $object->getCode());
						}				
					}
				}
			// seção normal
			} else {
				$this->Template->assign("_ROOT.section_{$sectionId}", $section->name);
				for ($i = 0; $i < sizeOf($section->getChildren()); $i++) {
					$object =& $section->getChild($i);					
					if ($section->getChildType($i) == 'SECTION') {
						$this->_buildSection($object);
					} else if ($section->getChildType($i) == 'BUTTON') {
						$this->Template->assign("_ROOT." . $object->getName(), $object->getCode());						
					} else if ($section->getChildType($i) == 'BUTTONGROUP') {
						for ($j=0; $j<sizeOf($object); $j++) {
							$button = $object[$j];
							$this->Template->assign("_ROOT." . $button->getName(), $button->getCode());
						}
					} else if ($section->getChildType($i) == 'FIELD') {						
						$this->Template->assign("_ROOT.label_" . $object->getName(), $object->getLabelCode($section->attrs['REQUIRED_FLAG'], $section->attrs['REQUIRED_COLOR'], $section->attrs['REQUIRED_TEXT']));
						$this->Template->assign("_ROOT.help_" . $object->getName(), $object->getHelpCode());
						$this->Template->assign("_ROOT." . $object->getName(), $object->getCode());
					}				
				}
			}
		}
	}

	//!-----------------------------------------------------------------
	// @function 	FormTemplate::_buildFormCode
	// @desc 		Gera a camada externa de código do formulário e insere
	// 				o nela o conteúdo extraído do template. Armazena o
	// 				código HTML gerado na propriedade $formCode da classe
	// @access 		private
	// @return		void	
	//!-----------------------------------------------------------------
	function _buildFormCode() {

		$TARGET = (isset($this->actionTarget) ? " TARGET=\"" . $this->actionTarget . "\"" : '');
		$ENCTYPE = ($this->hasUpload ? " ENCTYPE=\"multipart/form-data\"" : '');
		$RESET = (!empty($this->resetCode) ? " onReset=\"return " . $this->formName . "_reset()\"" : "");
		$SIGNATURE = sprintf("<INPUT TYPE=\"hidden\" ID=\"%s\" NAME=\"__form_signature\" VALUE=\"%s\"/>", FORM_SIGNATURE, parent::getSignature());
    
		$this->formCode .= sprintf("<FORM ID=\"%s\" NAME=\"%s\" ACTION=\"%s\" METHOD=\"%s\" STYLE=\"display:inline\"%s%s onSubmit=\"return %s_submit();\"%s>%s\n",

                 
			$this->formName, $this->formName, $this->formAction, $this->formMethod, 
			$TARGET, $ENCTYPE, $this->formName, $RESET, $SIGNATURE);
            
  
		$this->formCode .= $this->Template->getContent();
		$this->formCode .= "</FORM>\n";
   
	}
}
?>