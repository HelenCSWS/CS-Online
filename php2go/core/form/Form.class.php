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
// $Header: /www/cvsroot/php2go/core/form/Form.class.php,v 1.39 2005/08/30 18:50:00 mpont Exp $
// $Date: 2005/08/30 18:50:00 $

//------------------------------------------------------------------
import('php2go.form.FormButton');
import('php2go.form.FormEventListener');
import('php2go.form.FormRule');
import('php2go.form.FormSection');
import('php2go.net.HttpRequest');
import('php2go.template.Template');
import('php2go.util.HtmlUtils');
import('php2go.xml.XmlDocument');
//------------------------------------------------------------------

// @const FORM_SIGNATURE "__form_signature"
// Nome do campo escondido que é incluído no formulário contendo a assinatura do mesmo
define('FORM_SIGNATURE', '__form_signature');
// @const FORM_ERROR_FLOW	"1"
// Indica que os erros serão exibidos um abaixo do outro, apenas com quebra de linha como separador
define('FORM_ERROR_FLOW', 1);
// @const FORM_ERROR_BULLET_LIST	"2"
// Indica que os erros serão exibidos em uma lista com marcadores
define('FORM_ERROR_BULLET_LIST', 2);
// @const FORM_CLIENT_ERROR_ALERT	"1"
// Tipo de exibição de erros de valição JavaScript que utiliza um diálogo do tipo "alert"
define('FORM_CLIENT_ERROR_ALERT', 1);
// @const FORM_CLIENT_ERROR_DHTML	"2"
// Tipo de exibição de erros da validação JavaScript que utiliza DHTML
define('FORM_CLIENT_ERROR_DHTML', 2);
// @const FORM_HELP_INLINE "1"
// Tipo de exibição de ajuda de campos que exibe diretamente o conteúdo em um elemento do tipo LABEL
define('FORM_HELP_INLINE', 1);
// @const FORM_HELP_POPUP "2"
// Tipo de exibição de ajuda de campos que exibe um ícone, e uma popup flutuante quando se passa o mouse sobre o ícone
define('FORM_HELP_POPUP', 2);

//!-----------------------------------------------------------------
// @class		Form
// @desc		A classe Form funciona como base para a construção de formulários
//				a partir de uma especificação XML dos campos, seções e botões. O conteúdo
//				XML é interpretado e mapeado para uma estrutura de dados, e utilizada
//				pelas classes filhas (FormBasic, FormTemplate e FormDataBind) para renderizar
//				o conteúdo HTML final do formulário. Através da classe Form, também são
//				reunidas e organizadas as rotinas de validação e tratamento de
//				eventos nos campos e botões
// @package		php2go.form
// @extends		PHP2Go
// @uses		ADORecordSet
// @uses		Db
// @uses		FormButton
// @uses		FormField
// @uses		FormSection
// @uses		HttpRequest
// @uses		XmlDocument
// @author		Marcos Pont
// @version		$Revision: 1.39 $
// @note		Os formulários no PHP2Go aplicam validação sobre as informações fornecidas tanto
//				no cliente (JavaScript) quanto no servidor, desde que o método isValid seja executado
//				(o que faz com que a cadeia de validações seja processada).<br><br>
// @note		Para conhecer mais sobre o formato da especificação XML, um arquivo DTD com as definições
//				é incluído junto com o framework (docs/dtd/). Para conhecer mais sobre a aplicabilidade de 
//				cada campo, o diretório examples/ que acompanha o framework possui alguns exemplos de 
//				utilização desta classe: formbasic.example.php, formtemplate.example.php, formdatabind.example.php 
//				e formservervalidation.example.php
// @note		Se estiver utilizando PHP5, não esqueça de incluir a declaração XML na primeira linha do arquivo de especificação
//!-----------------------------------------------------------------
class Form extends PHP2Go
{
	var $formName;						// @var formName string				Nome do formulário
	var $formAction;					// @var formAction string			Action ou URL destino da submissão do formulário, padrão é o script atual
	var $actionTarget;					// @var actionTarget string			Alvo da resposta da requisição		
	var $formConstruct = FALSE;			// @var formConstruct bool			"FALSE" Indica que o formulário já foi construído (seções, campos, botões)
	var $formMethod = 'POST';			// @var formMethod string			"POST" Método da submissão da requisição, padrão é POST
	var $formErrors = array();			// @var formErrors string			Mensagens de erro resultantes da validação do formulário
	var $readonly = FALSE;				// @var readonly bool				"FALSE" Indica se o formulário é somente para visualização	
	var $buttonStyle;					// @var buttonStyle string			Estilo para os botões do formulário
	var $inputStyle;					// @var inputStyle string			Estilo para os campos do formulário
	var $labelStyle;					// @var labelStyle string			Estilo para os rótulos dos campos do formulário
	var $errorStyle = array();			// @var errorStyle string			Configurações de estilo para exibição dos erros ocorridos no formulário
	var $clientErrorOptions = array();	// @var clientErrorOptions array	"array()" Configurações para erros na validação executada no cliente com JavaScript
	var $helpOptions = array();			// @var helpOptions array			"array()" Configurações para exibição de textos de ajuda de campos do formulário
	var $sections = array();			// @var sections array				"array()" Vetor de seções do formulário
	var $fields = array();				// @var fields array				"array()" Vetor de campos do formulário
	var $submittedValues = array();		// @var submittedValues array		"array()" Vetor associativo contendo os dados submetidos, se o formulário foi postado
	var $isPosted;						// @var isPosted bool				Armazena o estado do formulário: postado ou não postado
	var $scriptCode = '';				// @var scriptCode string			"" Código JavaScript gerado para o formulário
	var $resetCode = array();			// @var resetCode array				"array()" Conjunto de instruções a serem executadas quando o formulário for resetado
	var $hasEditor = FALSE;				// @var hasEditor bool				"FALSE" Indica se um dos campos do formulário é do tipo EDITORFIELD - Editor Avançado
	var $editorName;					// @var editorName string			Nome do campo que submete o conteúdo do editor avançado	
	var $hasUpload = FALSE;				// @var hasUpload bool				"FALSE" Indica se há um campo do tipo FILE no formulário
	var $hasRequired = FALSE;			// @var hasRequired bool			"FALSE" Indica se pelo menos um campo do formulário é obrigatório
	var $requiredText = "*";			// @var requiredText string			"*" Texto utilizado para indicar um campo obrigatório, inserido automaticamente ao lado dos rótulos dos campos
	var $requiredMark = TRUE;			// @var requiredMark bool			"TRUE" Habilita ou desabilita a exibição de marcas ao lado do rótulo dos campos obrigatórios
	var $requiredColor = '#ff0000';		// @var requiredColor string		"#ff0000" Padrão de cor textual ou código RGB para a marca de campo obrigatório
	var $rootAttrs = array();			// @var rootAttrs array				"array()" Vetor de atributos da tag raiz do XML, pode ser utilizado pelo usuário para alguma customização	
	var $icons = array();				// @var icons array					"array()" Vetor de ícones e imagens utilizados pela classe	
	var $Document = NULL;				// @var Document Document object		"NULL" Documento HTML ao qual o formulário está subordinado
	var $XmlDocument = NULL;			// @var XmlDocument XmlDocument object	"NULL" Documento XML que contém os dados da especificação do formulário
	
	//!-----------------------------------------------------------------
	// @function	Form::Form
	// @desc		Construtor da classe de gerência de formulários
	// @access		public
	// @param		xmlFile string				Nome do arquivo XML que especifica o formulário
	// @param		formName string				Nome do formulário
	// @param		&Document Document object	Objeto Document onde o formulário será inserido
	//!-----------------------------------------------------------------
	function Form($xmlFile, $formName, &$Document) {
		parent::PHP2Go();
		// a classe Form é abstrata e não pode ser instanciada
		if ($this->isA('form', FALSE))
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_ABSTRACT_CLASS', 'Form'), E_USER_ERROR, __FILE__, __LINE__);
		// o parâmetro Document deve ser uma instância válida de documento
		elseif (!TypeUtils::isObject($Document) || !TypeUtils::isInstanceOf($Document, 'document'))
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_INVALID_OBJECT', 'Document'), E_USER_ERROR, __FILE__, __LINE__);
		else {
			// referência para o documento
			$this->Document =& $Document;
			// inicializa as propriedades
			$this->formName = $formName;
			$this->formAction = HttpRequest::basePath();
			$this->formMethod = "POST";			
			$this->clientErrorOptions = array(
				'mode' => FORM_CLIENT_ERROR_ALERT
			);
			$this->helpOptions = array(
				'mode' => FORM_HELP_POPUP, 
				'popup_icon' => PHP2GO_ICON_PATH . 'help.gif', 
				'popup_attrs' => 'BGCOLOR,"#000000",FGCOLOR,"#ffffff"'
			);
			$this->icons = array(
				'calendar' => PHP2GO_ICON_PATH . 'calendar.gif', 
				'calculator' => PHP2GO_ICON_PATH . 'calculator.gif'
			);			
			// interpreta o arquivo de especificação XML
			$this->XmlDocument = new XmlDocument();
			$this->XmlDocument->parseXml($xmlFile);
			$xmlRoot =& $this->XmlDocument->getRoot();		
			$this->rootAttrs = $xmlRoot->getAttributes();
			// inicializa configurações de apresentação a partir da configuração global do PHP2Go
			$globalConf = PHP2Go::getConfigVal('FORMS', FALSE);
			if ($globalConf)
				$this->loadGlobalSettings($globalConf);
		}
		parent::registerDestructor($this, '_Form');
	}
	
	//!-----------------------------------------------------------------
	// @function	FormField::_Form
	// @desc		Destrutor do objeto
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function _Form() {
		unset($this);
	}	
	
	//!-----------------------------------------------------------------
	// @function	Form::getButtonStyle
	// @desc		Monta a definição CSS configurada para os botões
	//				em formato atributo-valor (CLASS='estilo')
	// @access		public
	// @return		string Definição do estilo
	// @note		A definição de estilos para botões só é válida para
	//				alguns browsers. Se o browser do cliente não suportar
	//				esta funcionalidade, não será gerada a definição
	//!-----------------------------------------------------------------
	function getButtonStyle() {
		$Agent =& UserAgent::getInstance();
		if (!empty($this->buttonStyle) && $Agent->matchBrowserList(array('ie5+', 'ns6+', 'opera5+')))
			return " CLASS=\"{$this->buttonStyle}\"";
		return '';
	}

	//!-----------------------------------------------------------------
	// @function	Form::setButtonStyle
	// @desc		Configura o estilo CSS dos botões
	// @access		public
	// @param		style string		Nome do estilo CSS
	// @return		void	
	// @note		O parâmetro $style deve ser um estilo tal que possa
	//				ser referenciado em uma tag CLASS='estilo'
	// @see			Form::setInputStyle
	// @see			Form::setLabelStyle
	//!-----------------------------------------------------------------
	function setButtonStyle($style) {
		$this->buttonStyle = $style;
	}

	//!-----------------------------------------------------------------
	// @function	Form::getInputStyle
	// @desc		Monta a definição CSS configurada para os campos
	//				em formato atributo-valor (CLASS='estilo')
	// @access		public
	// @return		string Definição do estilo
	// @note		A definição de estilos para campos só é válida para
	//				alguns browsers. Se o browser do cliente não suportar
	//				esta funcionalidade, não será gerada a definição
	//!-----------------------------------------------------------------
	function getInputStyle() {
		$Agent =& UserAgent::getInstance();
		if (!empty($this->inputStyle) && $Agent->matchBrowserList(array('ie5+', 'ns6+', 'opera5+')))
			return " CLASS=\"{$this->inputStyle}\"";
		return '';
	}

	//!-----------------------------------------------------------------
	// @function	Form::setInputStyle
	// @desc		Configura o estilo CSS dos campos do formulário
	// @access		public
	// @param		style string		Nome do estilo CSS
	// @return		void	
	// @note		O parâmetro $style deve ser um estilo tal que possa
	//				ser referenciado em uma tag CLASS='estilo'
	// @see			Form::setButtonStyle
	// @see			Form::setLabelStyle
	//!-----------------------------------------------------------------
	function setInputStyle($style) {
		$this->inputStyle = $style;
	}

	//!-----------------------------------------------------------------
	// @function	Form::getLabelStyle
	// @desc		Monta a definição CSS configurada para os rótulos
	//				em formato atributo-valor (CLASS='estilo')
	// @access		public
	// @return		string Definição do estilo
	//!-----------------------------------------------------------------
	function getLabelStyle() {
		if (isset($this->labelStyle)) {
			return " CLASS=\"{$this->labelStyle}\"";
		} else {
			return '';
		}
	}

	//!-----------------------------------------------------------------
	// @function	Form::setLabelStyle
	// @desc		Configura o estilo CSS dos rótulos de campos do formulário
	// @access		public
	// @param		style string		Nome do estilo CSS
	// @return		void	
	// @note		O parâmetro $style deve ser um estilo tal que possa
	//				ser referenciado em uma tag CLASS='estilo'
	// @see			Form::setButtonStyle
	// @see			Form::setInputStyle
	//!-----------------------------------------------------------------
	function setLabelStyle($style) {
		$this->labelStyle = $style;
	}
	
	//!-----------------------------------------------------------------
	// @function	Form::getErrorStyle
	// @desc		Monta a definição CSS configurada para as mensagens
	//				de erro geradas pela validação do formulário
	//				em formato atributo-valor (CLASS='estilo')
	// @access		public
	// @return		string Definição do estilo
	//!-----------------------------------------------------------------
	function getErrorStyle() {
		if (isset($this->errorStyle['class'])) {
			return " CLASS=\"{$this->errorStyle['class']}\"";
		} else {
			return '';
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	Form::setErrorStyle
	// @desc		Define o estilo de apresentação dos erros encontrados
	//				na validação do formulário
	// @access		public
	// @param		class string		Nome do estilo CSS para as mensagens de erro
	// @param		listMode int		"FORM_ERROR_FLOW" Modo de exibição (ver constantes da classe)
	// @param		headerText string	"NULL" Permite customizar o  texto do cabeçalho do sumário de erros
	// @param		headerStyle string	"NULL" Nome do estilo CSS para o cabeçalho do sumário de erros
	// @return		void
	//!-----------------------------------------------------------------
	function setErrorStyle($class, $listMode=FORM_ERROR_FLOW, $headerText=NULL, $headerStyle=NULL) {
		// validação do tipo de listagem de erros
		if ($listMode != FORM_ERROR_FLOW && $listMode != FORM_ERROR_BULLET_LIST)
			$listMode = FORM_ERROR_FLOW;
		// mensagem customizada
		if (!TypeUtils::isNull($headerText, TRUE)) {
			if (!empty($headerText))
				$headerText = (!empty($headerStyle) ? sprintf("<DIV CLASS='%s'>%s</DIV>", $headerStyle, $headerText) : $headerText . '<BR>');
		} 
		// mensagem padrão
		else {
			$headerText = PHP2Go::getLangVal('ERR_FORM_FIELD_TITLE');
			$headerText = (!empty($headerStyle) ? sprintf("<DIV CLASS='%s'>%s</DIV>", $headerStyle, $headerText) : $headerText . '<BR>');
		}
		// armazena as configurações
		$this->errorStyle = array('class' => $class, 'list_mode' => $listMode, 'header_text' => $headerText);
	}
	
	//!-----------------------------------------------------------------
	// @function	Form::setHelpDisplayOptions
	// @desc		Define as opções de apresentação dos textos de ajuda dos campos do formulário
	// @access		public
	// @param		mode int		Modo de aprensentação (ver constantes da classe)
	// @param		options array	"array()" Vetor de opções adicionais
	// @return		void
	// @note		Conjunto de opções disponíveis:<br>
	//				popup_attrs - atributos para a popup flutuante (http://www.bosrup.com/web/overlib/?Command_Reference),<br>
	//				popup_icon - ícone de ajuda,<br>
	//				text_style - estilo para o texto de ajuda
	//!-----------------------------------------------------------------
	function setHelpDisplayOptions($mode, $options=array()) {
		if ($mode == FORM_HELP_INLINE || $mode == FORM_HELP_POPUP)
			$this->helpOptions = array_merge((array)$options, array('mode' => $mode));
	}
	
	//!-----------------------------------------------------------------
	// @function	Form::loadGlobalSettings
	// @desc		Define opções de apresentação, configurações de erros e ajuda
	//				a partir das configurações globais, se existentes
	// @param		settings array	Conjunto de configurações globais
	// @access		protected
	// @return		void
	//!-----------------------------------------------------------------
	function loadGlobalSettings($settings) {
		(isset($settings['INPUT_STYLE'])) && $this->setInputStyle($settings['INPUT_STYLE']);
		(isset($settings['BUTTON_STYLE'])) && $this->setButtonStyle($settings['BUTTON_STYLE']);
		(isset($settings['LABEL_STYLE'])) && $this->setLabelStyle($settings['LABEL_STYLE']);
		if (isset($settings['HELP_MODE'])) {
			$mode = @constant($settings['HELP_MODE']);
			if (!TypeUtils::isNull($mode))
				$this->setHelpDisplayOptions($mode, TypeUtils::toArray(@$settings['HELP_OPTIONS']));
		}
		if (isset($settings['ERRORS']['STYLE'])) {
			$mode = @constant($settings['ERRORS']['LIST_MODE']);
			$headerText = (isset($settings['ERRORS']['HEADER_TEXT']) ? Form::resolveI18nEntry($settings['ERRORS']['HEADER_TEXT']) : NULL);
			$this->setErrorStyle($settings['ERRORS']['STYLE'], $mode, $headerText, @$settings['ERRORS']['HEADER_STYLE']);			
		}
	}

	//!-----------------------------------------------------------------
	// @function	Form::&getField
	// @desc		Busca o objeto correspondente a um determinado campo,
	//				a fim de aplicar modificações/customizações ao mesmo
	// @access		public
	// @param		fieldPath string	Caminho do campo na ávore de elementos do formulário
	// @return		mixed	Objeto que representa o campo (package php2go.form.field) ou NULL se não existir no formulário
	//!-----------------------------------------------------------------
	function &getField($fieldPath) {
		if (!$this->formConstruct)
			$this->processXml();	
		$fieldSplitted = explode('.', $fieldPath);
		// o caminho completo para o caminho foi fornecido (secao.campo ou secao.subsecao.campo)
		if (sizeOf($fieldSplitted) > 1) {
			// busca a primeira seção
			$sectionId = $fieldSplitted[0];
			if (!isset($this->sections[$sectionId])) {
				return NULL;
			}
			$section = $this->sections[$sectionId];
			// busca subseções se fazem parte do caminho
			for ($i=1; $i<(sizeOf($fieldSplitted)-1); $i++) {
				$section = $section->getSubSection($fieldSplitted[$i]);
				if (TypeUtils::isNull($section)) {
					return NULL;
				}
			}
			// busca o campo
			return $section->getField($fieldSplitted[sizeOf($fieldSplitted)-1]);
		// apenas o nome do campo foi fornecido
		} else {
			if (array_key_exists($fieldPath, $this->fields))
				return $this->fields[$fieldPath];
		}
		return NULL;
	}
	
	//!-----------------------------------------------------------------
	// @function	Form::&getFields
	// @desc		Retorna um vetor contendo todos os campos do formulário
	// @access		public
	// @return		array Vetor de campos
	//!-----------------------------------------------------------------
	function &getFields() {
		if (!$this->formConstruct)
			$this->processXml();
		return $this->fields;
	}
	
	//!-----------------------------------------------------------------
	// @function	Form::getFieldNames
	// @desc		Retorna um vetor contendo todos os nomes de campos do formulário
	// @access		public
	// @return		array Vetor de nomes de campos
	//!-----------------------------------------------------------------
	function getFieldNames() {
		if (!$this->formConstruct)
			$this->processXml();
		return array_keys($this->fields);
	}
	
	//!-----------------------------------------------------------------
	// @function	Form::getSubmittedValues
	// @desc		Retorna o array associativo campo=>valor contendo os
	//				dados enviados na última submissão deste formulário
	// @access		public
	// @return		array Valores submetidos
	//!-----------------------------------------------------------------
	function getSubmittedValues() {
		return ($this->isPosted() ? $this->submittedValues : array());
	}

	//!-----------------------------------------------------------------
	// @function	Form::getFormErrors
	// @desc		Busca o conjunto de erros ocorridos na submissão do formulário
	// @access		public
	// @param		glue string			"NULL" String a ser utilizada para separar os erros na string resultante
	// @return		mixed Erros em forma de texto, se for fornecido um separador. Do contrário, retorna um array
	//!-----------------------------------------------------------------
	function getFormErrors($glue=NULL) {
  
 
		if (empty($this->formErrors))
    {
			return FALSE;
    }
		if (!TypeUtils::isNull($glue))
			return implode($glue, $this->formErrors);
      
   
		return $this->formErrors;
	}

	//!-----------------------------------------------------------------
	// @function	Form::addErrors
	// @desc		Adiciona uma ou mais mensagens de erro resultantes
	//				de validações sobre os dados submetidos
	// @access		public
	// @param		errors mixed	Mensagem ou vetor de mensagens
	// @return		void	
	//!-----------------------------------------------------------------
	function addErrors($errors) {
		if (TypeUtils::isArray($errors))
			$this->formErrors = array_merge($this->formErrors, $errors);
		else
			$this->formErrors[] = $errors;

	}	
	
	//!-----------------------------------------------------------------
	// @function	Form::getSignature
	// @desc		Monta a assinatura do formulário, que é enviada na submissão
	//				como um campo escondido
	// @access		protected
	// @return		string Assinatura do formulário
	//!-----------------------------------------------------------------
	function getSignature() {
		return md5($this->formName);
	}
	
	//!-----------------------------------------------------------------
	// @function	Form::setFormAction
	// @desc		Configura a URL a ser buscada na submissão do formulário
	// @access		public
	// @param		action    string  URL alvo da submissão do formulário
	// @return		void
	// @see			Form::setFormMethod
	// @see			Form::setFormActionTarget
	// @see			Form::setFormAlign
	//!-----------------------------------------------------------------
	function setFormAction($action) {
		$this->formAction = $action;
	}

	//!-----------------------------------------------------------------
	// @function	Form::setFormMethod
	// @desc		Configura o método de submissão do formulário
	// @access		public
	// @param		method    string  Método de submissão do formulário
	// @return		void	
	// @see			Form::setFormAction
	// @see			Form::setFormActionTarget
	// @see			Form::setFormAlign
	//!-----------------------------------------------------------------
	function setFormMethod($method) {
		$method = trim($method);
		if (in_array(strtoupper($method), array('GET','POST'))) {
			$this->formMethod = $method;
		} else {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_INVALID_FORM_METHOD', array($method, $this->formName)), E_USER_ERROR, __FILE__, __LINE__);
		}
	}

	//!-----------------------------------------------------------------
	// @function	Form::setFormActionTarget
	// @desc		Configura onde a URL definida na propriedade $formAction
	//				da classe deve ser aberta. Aceita valores como '_blank',
	//				'_self', '_parent', '_top', ...
	// @access		public
	// @param		target    string  Local onde a URL alvo do formulário será aberta
	// @return		void	
	// @see			Form::setFormAction
	// @see			Form::setFormMethod
	// @see			Form::setFormAlign
	//!-----------------------------------------------------------------
	function setFormActionTarget($target) {
		$this->actionTarget = $target;
	}
	
	//!-----------------------------------------------------------------
	// @function	Form::isPosted
	// @desc		Indica se o formulário foi postado, verificando o método
	//				da requisição e a presenção de uma variável com a mesma
	//				assinatura do formulário
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function isPosted() {		
		if (!isset($this->isPosted)) {
			if (HttpRequest::method() == $this->formMethod) {
				$signature = HttpRequest::getVar(FORM_SIGNATURE);
				if (!TypeUtils::isNull($signature) && $signature == $this->getSignature())
					$this->isPosted = TRUE;
				else
					$this->isPosted = FALSE;
			} else {
				$this->isPosted = FALSE;
			}
		}
		return $this->isPosted;
	}
	
	//!-----------------------------------------------------------------
	// @function	Form::isValid
	// @desc		Executa a validação em todos os campos do formulário,
	//				somente se o mesmo foi postado
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function isValid() {
		if ($this->isPosted()) {
			if (!$this->formConstruct)
				$this->processXml();		
			$result = TRUE;
			$keys = array_keys($this->fields);
			foreach ($keys as $name) {
				$Field =& $this->fields[$name];
				$result &= $Field->isValid();
			}
			$result = TypeUtils::toBoolean($result);			
			if ($result === FALSE) {
				$this->addErrors(Validator::getErrors());
				Validator::clearErrors();
			}
			return ($result);
		}
		return FALSE;
	}

	//!-----------------------------------------------------------------
	// @function	Form::isReadonly
	// @desc		Indica para a classe que o formulário construído é
	//				somente para visualização
	// @return		void	
	// @note		Com a utilização deste método, todos os campos e botões 
	//				do formulário serão desabilitados
	//!-----------------------------------------------------------------
	function isReadonly() {
    	$this->readonly = TRUE;
	}

	//!-----------------------------------------------------------------
	// @function	Form::appendScript
	// @desc		Concatena código JavaScript à propriedade scriptCode do objeto
	// @access		public
	// @param		toAppend string	Código JavaScript a ser inserido
	// @return		void	
	//!-----------------------------------------------------------------
	function appendScript($toAppend) {
		$this->scriptCode .= $toAppend;
	}
	
	//!-----------------------------------------------------------------
	// @function	Form::verifySectionId
	// @desc		Verifica a declaração duplicada de um ID de seção no formulário
	// @access		public
	// @param		formName string		Nome do formulário
	// @param		sectionId string	ID de uma seção
	// @return		void
	// @static	
	//!-----------------------------------------------------------------
	function verifySectionId($formName, $sectionId) {
		static $sections;
		if (!isset($sections) || !isset($sections[$formName])) {
			$sections = array($formName => array($sectionId));
		} else {
			if (in_array($sectionId, $sections[$formName])) {
				PHP2Go::raiseError(PHP2Go::getLangVal('ERR_FORM_DUPLICATED_SECTION', array($sectionId, $formName)), E_USER_ERROR, __FILE__, __LINE__);
			} else {
				$sections[$formName][] = $sectionId;
			}
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	Form::verifyFieldName
	// @desc		Verifica a declaração duplicada de um nome de campo no formulário
	// @access		public
	// @param		formName string	Nome do formulário
	// @param		fieldName string	Nome do campo a ser verificado
	// @return		void	
	// @static
	//!-----------------------------------------------------------------
	function verifyFieldName($formName, $fieldName) {
		static $fields;
		if (!isset($fields) || !isset($fields[$formName])) {
			$fields = array($formName => array($fieldName));
		} else {			
			if (in_array($fieldName, $fields[$formName])) {
				PHP2Go::raiseError(PHP2Go::getLangVal('ERR_FORM_DUPLICATED_FIELD', array($fieldName, $formName)), E_USER_ERROR, __FILE__, __LINE__);
			} else {
				$fields[$formName][] = $fieldName;
			}
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	Form::verifyButtonName
	// @desc		Verifica a declaração duplicada de um nome de botão no formulário
	// @access		public
	// @param		formName string	Nome do formulário
	// @param		btnName string	Nome do botão a ser verificado
	// @return		void	
	// @static
	//!-----------------------------------------------------------------
	function verifyButtonName($formName, $btnName) {
		static $buttons;
		if (!isset($buttons) || !isset($buttons[$formName])) {
			$buttons = array($formName => array($btnName));
		} else {			
			if (in_array($btnName, $buttons[$formName])) {
				PHP2Go::raiseError(PHP2Go::getLangVal('ERR_FORM_DUPLICATED_BUTTON', array($btnName, $formName)), E_USER_ERROR, __FILE__, __LINE__);
			} else {
				$buttons[$formName][] = $btnName;
			}
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	Form::resolveBooleanChoice
	// @desc		Define o valor das escolhas booleanas utilizadas nos atributos
	//				dos campos de formulário, onde o valor TRUE é mapeado para T e
	//				o valor FALSE é mapeado para F
	// @access		public
	// @param		value mixed			"NULL" Valor do atributo
	// @return		mixed TRUE ou FALSE se o valor vor T ou F, e NULL em outros casos
	// @static
	//!-----------------------------------------------------------------
	function resolveBooleanChoice($value=NULL) {
		if (TypeUtils::isNull($value))
			return NULL;
		elseif (trim($value) == "T")
			return TRUE;
		elseif (trim($value) == "F")
			return FALSE;
		return NULL;
	}
	
	//!-----------------------------------------------------------------
	// @function	Form::resolveI18nEntry
	// @desc		Resolve referências a tabelas de linguagem utilizadas
	//				em atributos de elementos de formulários
	// @access		public
	// @param		value string	Valor do atributo
	// @return		string Valor traduzido
	//!-----------------------------------------------------------------
	function resolveI18nEntry($value) {
		if (!empty($value) && preg_match(PHP2GO_I18N_PATTERN, $value, $matches))
			return PHP2Go::getLangVal($matches[1]);
		return $value;
	}

	//!-----------------------------------------------------------------
	// @function	Form::buildScriptCode
	// @desc		Constrói a função de validação da submissão do
	//				formulário a partir validações necessárias aos
	//				campos requeridos e campos com checagem de máscara
	// @access		protected
	// @return		void	
	//!-----------------------------------------------------------------
	function buildScriptCode() {
		$this->Document->addScript(PHP2GO_JAVASCRIPT_PATH . 'libs/string.js');
		$this->Document->addScript(PHP2GO_JAVASCRIPT_PATH . 'libs/form.js');
		$this->Document->addScript(PHP2GO_JAVASCRIPT_PATH . 'modules/formvalidator.js');
		$code  = "     function " . $this->formName . "_submit() {\n";
		$code .= "          validator = new FormValidator(\"{$this->formName}\");\n";
		if ($this->hasEditor) {		
			$code .= "          validator.editor = \"{$this->editorName}\";\n";
		}
		$headerText = (isset($this->errorStyle['header_text']) ? $this->errorStyle['header_text'] : PHP2Go::getLangVal('ERR_FORM_FIELD_TITLE'));
		$listMode = (isset($this->errorStyle['list_mode']) ? $this->errorStyle['list_mode'] : FORM_ERROR_FLOW);
		$code .= "          validator.setErrorOptions(" . $this->clientErrorOptions['mode'] . ", \"" . @$this->clientErrorOptions['placeholder'] . "\", {$listMode}, \"{$headerText}\");\n";
		$code .= $this->scriptCode;
		// função customizada de validação, resultado é combinado com o do validador padrão
		if (!empty($this->rootAttrs) && array_key_exists('VALIDATEFUNC', $this->rootAttrs)) {		
			$validateFunc = trim($this->rootAttrs['VALIDATEFUNC']);
			if (!ereg("[[:alnum:]]+\(.*\)", $validateFunc) || $validateFunc == $this->formName . "_submit()")
				PHP2Go::raiseError(PHP2Go::getLangVal('ERR_INVALID_VALIDATE_FUNC', $validateFunc), E_USER_ERROR, __FILE__, __LINE__);
			else
				$code .= "          return (validator.isValid() && $validateFunc);\n";		
		} 
		// validação padrão
		else {
			$code .= "          return validator.isValid();\n";
		}
		$code .= "     }";
		// instruções de reset
		if (!empty($this->resetCode)) {
			$code .= "\n     function " . $this->formName . "_reset() {\n";
			foreach ($this->resetCode as $instruction) {
				$code .= "          " . $instruction . "\n";
			}
			$code .= "     }\n";
		}
		$this->Document->addScriptCode($code);
	}
	
	//!-----------------------------------------------------------------
	// @function	Form::processXml
	// @desc		Inicia o processamento da árvore XML a partir de
	//				sua raiz, processando seções de formulário e seus
	//				botões
	// @access		protected
	// @return		void	
	//!-----------------------------------------------------------------	
	function processXml() {
		$xmlRoot =& $this->XmlDocument->getRoot();
		if ($xmlRoot->hasChildren()) {
			$childrenCount = $xmlRoot->getChildrenCount();
			for ($i=0; $i<$childrenCount; $i++) {
				$node = $xmlRoot->getChild($i);
				if ($node->getTag() == 'SECTION') {
					if ($FormSection =& $this->_createSection($node)) {
						$this->sections[$FormSection->getId()] =& $FormSection;
					}						
				} 
			}
		}
		$this->formConstruct = TRUE;
	}
	
	//!-----------------------------------------------------------------
	// @function	Form::&_createSection
	// @desc		Processa uma seção do formulário (conjunto de campos)
	// @access		private
	// @param		xmlNode XmlNode object	Nodo que representa a seção
	// @return		FormSection object Seção criada
	// @note		O método retorna NULL se a seção não possuir campos
	//!-----------------------------------------------------------------
	function &_createSection($xmlNode) {
		$FormSection = new FormSection($xmlNode, $this);
		if ($FormSection->show) {
			if ($xmlNode->hasChildren()) {
				$childrenCount = $xmlNode->getChildrenCount();
				for ($i=0; $i<$childrenCount; $i++) {
					$child = $xmlNode->getChild($i);		
					// seção condicional
					if ($child->getTag() == 'CONDSECTION') {
						$child->setAttribute('CONDITION', 'T');
						$this->_createSubSection($child, $FormSection);
					// grupo de botões
					} else if ($child->getTag() == 'BUTTONS') {
						$this->_createButtonGroup($child, $FormSection);
					// botão
					} else if ($child->getTag() == 'BUTTON') {
						$this->_createButton($child, $FormSection);
					// campo
					} else {
						$this->_createField($child, $FormSection);
					}
				}
				return $FormSection;
			}
		}
		return NULL;
	}
	
	//!-----------------------------------------------------------------
	// @function	Form::_createSubSection
	// @desc		Processa uma subseção de formulário, que depende de uma
	//				condição para ser incluída no formulário
	// @access		private
	// @param		xmlNode XmlNode object				Representa a subseção na árvore XML
	// @param		&parentSection FormSection object	Referência para a seção ou subseção superior
	// @return		void
	//!-----------------------------------------------------------------
	function _createSubSection($xmlNode, &$parentSection) {
		$FormSection = new FormSection($xmlNode, $this);
		if ($FormSection->show) {			
			if ($xmlNode->hasChildren()) {
				$parentSection->addChild($FormSection);
				$childrenCount = $xmlNode->getChildrenCount();
				for ($i=0; $i<$childrenCount; $i++) {
					$child = $xmlNode->getChild($i);
					// seção condicional
					if ($child->getTag() == 'CONDSECTION') {
						$child->setAttribute('CONDITION', 'T');
						$this->_createSubSection($child, $FormSection);
					// grupo de botões
					} else if ($child->getTag() == 'BUTTONS') {
						$this->_createButtonGroup($child, $FormSection);
					// botão
					} else if ($child->getTag() == 'BUTTON') {
						$this->_createButton($child, $FormSection);
					// campo
					} else {													
						$this->_createField($child, $FormSection);
					}
				}				
			}
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	Form::_createButtonGroup
	// @desc		Processa um grupo de botões, inserindo-os em uma seção ou subseção
	// @access		private
	// @param		buttons XmlNode object			Grupo de botões de uma seção
	// @param		&FormSection FormSection object Seção à qual o grupo de botões pertence
	// @return		void	
	// @see			Form::_processSection
	// @see			Form::_processField
	//!-----------------------------------------------------------------
	function _createButtonGroup($buttons, &$FormSection) {
		if (TypeUtils::isInstanceOf($FormSection, 'formsection') && $FormSection->show) {						
			for ($i=0; $i<$buttons->getChildrenCount(); $i++) {
				$button = $buttons->getChild($i);
				if ($button->getTag() == 'BUTTON') {
					$buttonGroup[] = new FormButton($button, $this);
				}
			}
			if (!empty($buttonGroup)) {
				$FormSection->addChild($buttonGroup);
			}
		}
	}

	//!-----------------------------------------------------------------
	// @function	Form::_createButton
	// @desc		Adiciona um botão a uma seção ou subseção
	// @access		private
	// @param		button FormButton object		Botão a ser inserido
	// @param		&FormSection FormSection object	Seção à qual o botão pertence
	// @return		void
	//!-----------------------------------------------------------------
	function _createButton($button, &$FormSection) {
		 if (TypeUtils::isInstanceOf($FormSection, 'formsection') && $FormSection->show) {
		 	$FormSection->addChild(new FormButton($button, $this));
		 }
	}

	//!-----------------------------------------------------------------
	// @function	Form::_createField
	// @desc		Cria um objeto FormField, construindo o código HTML
	//				do campo, e gera o código JavaScript para as validações
	//				e checagens configuradas na especificação XML
	// @access		private
	// @param		field XmlNode object			Objecto XmlNode referente a um campo de formulário
	// @param		&FormSection FormSection object	Seção ou subseção onde o campo está incluído
	// @return		void	
	//!-----------------------------------------------------------------
	function _createField($field, &$FormSection) {
		$fieldClassName = NULL;
 		if (TypeUtils::isInstanceOf($FormSection, 'formsection') && $FormSection->show) {
			switch($field->getTag()) {
				case 'EDITFIELD' : $fieldClassName = 'EditField'; break;
				case 'RANGEFIELD' : $fieldClassName = 'RangeField'; break;
				case 'PASSWDFIELD' : $fieldClassName = 'PasswdField'; break;
				case 'MEMOFIELD' : $fieldClassName = 'MemoField'; break;
				case 'EDITORFIELD' : $fieldClassName = 'EditorField'; break;
				case 'CHECKFIELD' : $fieldClassName = 'CheckField'; break;
				case 'CHECKGROUP' : $fieldClassName = 'CheckGroup'; break;
				case 'FILEFIELD' : $fieldClassName = 'FileField'; break;
				case 'LOOKUPFIELD' : $fieldClassName = 'LookupField'; break;
				case 'LOOKUPCHOICEFIELD' : $fieldClassName = 'LookupChoiceField'; break;
				case 'COMBOFIELD' : $fieldClassName = 'ComboField'; break;
				case 'RADIOFIELD' : $fieldClassName = 'RadioField'; break;
				case 'DBRADIOFIELD' : $fieldClassName = 'DbRadioField'; break;
				case 'HIDDENFIELD' : $fieldClassName = 'HiddenField'; break;
				case 'TEXTFIELD' : $fieldClassName = 'TextField'; break;
				case 'EDITSEARCHFIELD' : $fieldClassName = 'EditSearchField'; break;
				case 'EDITSELECTIONFIELD' : $fieldClassName = 'EditSelectionField'; break;
				case 'LOOKUPSELECTIONFIELD' : $fieldClassName = 'LookupSelectionField'; break;
				case 'DATAGRID' : $fieldClassName = 'DataGrid'; break;
				case 'CAPTCHAFIELD' : $fieldClassName = 'CaptchaField'; break;
				default : PHP2Go::raiseError(PHP2Go::getLangVal('ERR_FORM_INVALID_FIELDTYPE', $field->getTag()), E_USER_ERROR, __FILE__, __LINE__); break;
			}
			if (!TypeUtils::isNull($fieldClassName)) {
				// instancia e inicializa o campo
				import("php2go.form.field.{$fieldClassName}");
				$obj = new $fieldClassName($this);
				$obj->onLoadNode($field->getAttributes(), $field->getChildrenTagsArray());
				// adiciona o campo na seção
				$FormSection->addChild($obj);
				// adiciona o campo neste formulário
				$this->fields[$obj->getName()] =& $obj;
			}
		}
	}	
}
?>