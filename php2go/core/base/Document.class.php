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
// $Header: /www/cvsroot/php2go/core/base/Document.class.php,v 1.33 2005/07/20 22:33:48 mpont Exp $
// $Date: 2005/07/20 22:33:48 $

//------------------------------------------------------------------
import('php2go.datetime.Date');
import('php2go.datetime.TimeCounter');
import('php2go.file.FileManager');
import('php2go.net.HttpRequest');
import('php2go.net.HttpResponse');
import('php2go.template.DocumentElement');
import('php2go.text.StringBuffer');
import('php2go.text.StringUtils');
import('php2go.util.HtmlUtils');
//------------------------------------------------------------------

// @const SCRIPT_START "1"
// Valor de posicionamento de scripts gerados dentro da tag HEAD
define('SCRIPT_START', 1);
// @const SCRIPT_END "2"
// Valor de posicionamento de scripts gerados no final da tag BODY
define('SCRIPT_END', 2);

//!-----------------------------------------------------------------
// @class		Document
// @desc		Responsável por gerenciar e gerar os documentos HTML
//				do sistema. Gerencia o esqueleto HTML fornecido ao
//				documento (Layout) e os elementos declarados no mesmo
//				(Elementos de documento). Controla a geração do cabeçalho
//				do documento, configurações de interface, cache, entre
//				outras funcionalidades.
// @package		php2go.base
// @extends		PHP2Go
// @uses		Db
// @uses		DocumentElement
// @uses		FileManager
// @uses		HtmlUtils
// @uses		HttpRequest
// @uses		HttpResponse
// @uses		StringBuffer
// @uses		StringUtils
// @uses		System
// @uses		TimeCounter
// @author		Marcos Pont
// @version		$Revision: 1.33 $
// @note		Exemplo de uso:
//				<PRE>
//
//				$doc = new Document('page_layout.tpl');
//				$doc->setTitle('Page Title');
//				$doc->setCache(FALSE);
//				$doc->setCompression(TRUE, 9);
//				$doc->addBodyCfg(array('bgcolor'=>'FFFFFF'));
//				$doc->addScript('functions.js');
//				$doc->addStyle('style.css');
//				$header =& new DocumentElement();
//				$header->put('header.tpl', T_BYFILE);
//				$header->parse();
//				$doc->elements['header'] =& $header;
//				$menu =& new DocumentElement();
//				$menu->put('menu.tpl', T_BYFILE);
//				$menu->parse();
//				$doc->elements['menu'] =& $menu;
//				... other elements ...
//				$doc->display();
//
//				</PRE>
// @note		Os scripts JS libs/div.js, libs/object.js e libs/window.js
//				já são adicionados automaticamente a todo documento instanciado
//!-----------------------------------------------------------------
class Document extends PHP2Go
{
	var $docLayout;						// @var	docLayout string				Nome do Template base que serve como 'esqueleto' para o documento	
	var $docHeader = NULL;				// @var	docHeader StringBuffer object	"NULL" Conteúdo do header do documento HTML
	var $docCharset;					// @var	docCharset string				Charset do conteúdo do documento
	var $docLanguage;					// @var	docLanguage string				Linguagem do documento
	var $docTitle;						// @var	docTitle string					Título do documento
	var $docBody = NULL;				// @var	docBody StringBuffer object		"NULL" Conteúdo do corpo do documento HTML
	var $metaTagsName = array();		// @var	metaTagsName array				"array()" Vetor que contém tags META do tipo NAME e seus valores
	var $metaTagsHttp = array();		// @var	metaTagsHttp array				"array()" Vetor que armazena as tags META do tipo HTTP-EQUIV
	var $scriptCode = '';				// @var	scriptCode string				"" Código de funções de script gerado para o documento
	var $scriptExtCode = array();		// @var	scriptExtCode string			"array()" Código de script inserido direto pelo usuário
	var $scriptFiles = array();			// @var	scriptFiles array				"array()" Vetor contendo os arquivos script incluídos no documento
	var $onLoadCode = array();			// @var onLoadCode array				"array()" Conjunto de instruções (JavaScript) a serem executadas no evento onLoad da página	
	var $styles = array();				// @var	styles string					"array()" Links para arquivos de estilo CSS incluídos no documento
	var $styleCode = '';				// @var	styleCode array					"" Código de links CSS gerado para o documento
	var $styleExtCode = '';				// @var styleExtCode string				"" Código de estilo inserido diretamente pelo usuário
	var $extraHeaderCode = '';			// @var	extraHeaderCode string			"" Código extra a ser incluído no header do documento
	var $bodyEvents = array();			// @var	bodyEvents array				"array()" Vetor associativo contendo eventos e respectivas ações tratadas na tag BODY
	var $bodyCfg = array();				// @var	bodyCfg array					"array()" Vetor associativo contendo as configuração da tag BODY do documento
	var $extraBodyContent = '';			// @var extraBodyContent array			"" Código extra que será incluído no corpo do documento
	var $allowRobots = TRUE;			// @var allowRobots bool				"TRUE" Se for FALSE, inclui a tag META que previne contra a ação de robôs de pesquisa
	var $makeCache = FALSE;				// @var	makeCache bool					"FALSE" Indica a utilização ou não de headers HTTP para habilitação de cache
	var $makeCompression = FALSE;		// @var	makeCompression bool			"FALSE" Indica que o conteúdo HTML gerado deve ser compactado ao enviar para o cliente
	var $compressionLevel;				// @var	compressionLevel int			Nível de compressão aplicado ao conteúdo do documento
	var $Template;						// @var	Template Template object		Template de manipulação do layout do documento
	var $TimeCounter;					// @var TimeCounter TimeCounter object	Utilizado para calcular o tempo de geração da página
	var $elements;						// @var	elements array					Vetor de objetos DocumentElement para os elementos declarados no layout do documento

	//!-----------------------------------------------------------------
	// @function	Document::Document
	// @desc		Construtor da classe Document. Cria uma instância
	//				da classe Template para manipulação do layout de
	//				documento e parseia seu conteúdo
	// @access		public
	// @param		docLayout string	Nome do arquivo template base do documento
	// @param		docIncludes array	"array()" Vetor de templates de inclusão
	//!-----------------------------------------------------------------
	function Document($docLayout, $docIncludes=array()) {
		parent::PHP2Go();		
		$this->docLayout = $docLayout;
		$this->docCharset = PHP2Go::getConfigVal('CHARSET', FALSE);
		$this->docLanguage = PHP2Go::getConfigVal('LOCALE', FALSE);
		$this->docTitle = PHP2Go::getConfigVal('TITLE', FALSE);
		$this->Template =& new Template($docLayout);
		if (!empty($docIncludes) && TypeUtils::isHashArray($docIncludes)) {
			foreach ($docIncludes as $blockName => $blockValue)
				$this->Template->includeAssign($blockName, $blockValue, T_BYFILE);
		}
		$this->Template->parse();
		$this->TimeCounter =& new TimeCounter();
		$this->_initMetaTags();
		$this->_initDeclaredElements();
		$this->_addSystemElements();
		parent::registerDestructor($this, '_Document');
	}
  
	
	//!-----------------------------------------------------------------
	// @function	Document::_Document
	// @desc		Destrutor da classe
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function _Document() {
		unset($this);
	}
	
	//!-----------------------------------------------------------------
	// @function	Document::getCharset
	// @desc		Retorna o conjunto de caracteres setado para o documento
	// @access		public
	// @return		string Conjunto de caracteres. Ex: iso-8859-1, UTF-8, etc...	
	//!-----------------------------------------------------------------
	function getCharset() {
		return $this->docCharset;
	}
	
	//!-----------------------------------------------------------------
	// @function	Document::getTitle
	// @desc		Busca o título do documento
	// @access		public
	// @return		string Título do Documento
	//!-----------------------------------------------------------------
	function getTitle() {
		return $this->docTitle;
	}

	//!-----------------------------------------------------------------
	// @function	Document::setCache
	// @desc		Seta o flag de utilização de cache no documento
	// @access		public
	// @param		flag bool		"TRUE"	Valor para o parâmetro de utilização de cache
	// @return		void
	//!-----------------------------------------------------------------
	function setCache($flag=TRUE) {
		$this->makeCache = TypeUtils::toBoolean($flag);
	}
	
	//!-----------------------------------------------------------------
	// @function	Document::preventRobots
	// @desc		Indica que a página deve incluir um cabaçalho de prevenção contra robôs
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function preventRobots() {
		$this->allowRobots = FALSE;
	}

	//!-----------------------------------------------------------------
	// @function	Document::setCharset
	// @desc		Configura o conjunto de caracteres do documento
	// @param		charset string		Conjunto de caracteres. Ex: iso-8859-1, UTF-8, etc...
	// @access		public	
	// @return		void	
	//!-----------------------------------------------------------------
	function setCharset($charset) {
		$this->docCharset = $charset;
	}
	
	//!-----------------------------------------------------------------
	// @function	Document::setCompression
	// @desc		Configura o objeto para realizar compressão no conteúdo HTML do documento
	// @param		flag bool			"TRUE"	Habilitação ou desabilitação da compressão de documento
	// @param		level int			"9"		Nível de compressão, de 1 a 9. Será ignorado se $flag for TRUE
	// @note		Atualmente, a funcionalidade de compressão de documento não funciona em versões do PHP para Windows
	// @access		public	
	// @return		void	
	//!-----------------------------------------------------------------
	function setCompression($flag=TRUE, $level=9) {
		$this->makeCompression = TypeUtils::toBoolean($flag);
		if ($this->makeCompression)
			$this->compressionLevel = ($level >= 1 ? min($level, 9) : 9);
	}
	
	//!-----------------------------------------------------------------
	// @function	Document::setFocus
	// @desc		Configura o campo de formulário que deve receber foco após a geração do documento HTML
	// @param		formName string		Nome do formulário
	// @param		formField string	Nome do campo do formulário	
	// @access		public	
	// @return		void	
	//!-----------------------------------------------------------------
	function setFocus($formName, $formField) {
		$this->addScript(PHP2GO_JAVASCRIPT_PATH . 'libs/form.js');
		$this->addScriptCode("     requestFocus(\"{$formName}\", \"$formField\");", 'JavaScript', SCRIPT_END);
	}

	//!-----------------------------------------------------------------
	// @function	Document::setLanguage
	// @desc		Configura a linguagem do documento HTML
	// @param		lang string			Linguagem a ser utilizada
	// @access		public	
	// @return		void	
	//!-----------------------------------------------------------------
	function setLanguage($lang) {
		$this->docLanguage = $lang;
	}
	
	//!-----------------------------------------------------------------
	// @function	Document::setTitle
	// @desc		Configura o título do documento a partir da variável $title
	// @param		title string		Título para o documento
	// @param		ignoreSpaces bool	"FALSE"	Ignorar espaços à esquerda e à direita do título
	// @access		public
	// @return		void	
	// @see			Document::setTitleFromDb
	// @see			Document::appendTitle
	// @see			Document::appendTitleFromDb
	//!-----------------------------------------------------------------
	function setTitle($title, $ignoreSpaces=FALSE) {
		if ($ignoreSpaces)
			$this->docTitle = $title;
		else
			$this->docTitle = trim($title);
	}

	//!-----------------------------------------------------------------
	// @function	Document::setTitleFromDb
	// @desc		Configura o título a partir de uma consulta SQL
	// @param		sql string				Consulta SQL para o título
	// @param		connectionId string		"NULL" ID da conexão ao BD
	// @access		public	
	// @return		void
	// @see			Document::setTitle
	// @see			Document::appendTitle
	// @see			Document::appendTitleFromDb
	//!-----------------------------------------------------------------
	function setTitleFromDb($sql, $connectionId=NULL) {
		$Db =& Db::getInstance($connectionId);
		$dbTitle = $Db->getFirstCell($sql);
		if ($dbTitle)
			$this->docTitle = $dbTitle;
	}

	//!-----------------------------------------------------------------
	// @function	Document::appendTitle
	// @desc		Concatena um valor ao título do documento
	// @param		aTitle string		Valor a ser concatenado ao título
	// @param		useSeparator bool	"TRUE"	Utilizar ou não separador com relação ao título existente
	// @param		separator string	"-"		Separador com relação ao título existente
	// @access		public	
	// @return		void	
	// @see			Document::setTitle
	// @see			Document::setTitleFromDb
	// @see			Document::appendTitleFromDb
	//!-----------------------------------------------------------------
	function appendTitle($aTitle, $useSeparator=TRUE, $separator='-') {
		if ($this->docTitle == "") {
			$this->setTitle($aTitle);
		} else {
			if ($useSeparator)
				$this->docTitle .= ' ' . $separator;
			$this->docTitle .= ' ' . ltrim($aTitle);
		}
	}

	//!-----------------------------------------------------------------
	// @function	Document::appendTitleFromDb
	// @desc		Concatena um valor ao título do documento a partir de uma consulta SQL
	// @param		sql string			Consulta SQL para concatenação no título do documento
	// @param		useSeparator bool	"TRUE" Utilizar ou não separador com relação ao título existente, padrão é TRUE
	// @param		separator string	"-" Separador com relação ao título existente, padrão é '-'
	// @param		connectionId string	"NULL" ID da conexão ao BD
	// @access		public	
	// @return		void	
    // @see			Document::setTitle
	// @see			Document::setTitleFromDb
	// @see			Document::appendTitle
	//!-----------------------------------------------------------------
	function appendTitleFromDb($sql, $useSeparator=TRUE, $separator='-', $connectionId=NULL) {
		$Db =& Db::getInstance($connectionId);
		$dbTitle = $Db->getFirstCell($sql);
		if ($dbTitle)
			$this->appendTitle($dbTitle, $useSeparator, $separator);
	}
	
	//!-----------------------------------------------------------------
	// @function	Document::addScript
	// @desc		Adiciona um arquivo de script ao cabeçalho do documento
	// @param		scriptFile string	Nome do arquivo de script
	// @param		language string		"JavaScript"	Linguagem do script
	// @access		public	
	// @return		bool TRUE se o script for incluído ou FALSE se ele já existir
	// @see			Document::addScriptCode
	// @see			Document::addStyle
	//!-----------------------------------------------------------------
	function addScript($scriptFile, $language="JavaScript") {
		$scriptType = ereg_replace("[^a-zA-Z]", "", $language);
		if (!in_array($scriptFile, $this->scriptFiles)) {
			$this->scriptFiles[] = $scriptFile;
			$this->scriptCode .= sprintf("<SCRIPT LANGUAGE=\"%s\" SRC=\"%s\" TYPE=\"text/" . strtolower($scriptType) . "\"></SCRIPT>\n", $language, $scriptFile);
			return TRUE;
		} else {
			return FALSE;
		}
	}

	//!-----------------------------------------------------------------
	// @function	Document::addScriptCode
	// @desc		Adiciona um código de script ao cabeçalho do documento
	// @param		scriptCode string	Código de script
	// @param		language string		"JavaScript" Linguagem do script
	// @param		position int		"SCRIPT_START" Posição onde o código deve ficar
	// @access		public	
	// @return		void	
	// @note		A classe agrupa as funções de linguagens diferentes
	//				em estruturas separadas, para geração uma tag SCRIPT
	//				para cada linguagem no momento da construção do documento
	// @note		Além da linguagem, é possível definir a posição onde o código
	//				será exibido: a constante SCRIPT_START posiciona o código dentro
	//				da tag HEAD do documento, enquanto a constante SCRIPT_END posiciona
	//				o código no fim da tag BODY
	// @see			Document::addScript
	// @see			Document::addStyle
	//!-----------------------------------------------------------------
	function addScriptCode($scriptCode, $language="JavaScript", $position=SCRIPT_START) {
		if ($position != SCRIPT_START && $position != SCRIPT_END)
			$position = SCRIPT_START;
		$this->scriptExtCode[$position][$language] = isset($this->scriptExtCode[$position][$language]) ? $this->scriptExtCode[$position][$language] . $scriptCode . "\n" : $scriptCode . "\n";
	}
	
	//!-----------------------------------------------------------------
	// @function	Document::addOnloadCode
	// @desc		Adiciona código que deve ser executado no carregamento da página
	// @access		public
	// @param		instruction string	Instrução JavaScript (uma ou mais linhas, será transformado em uma só linha no código fonte)
	// @return		void
	//!-----------------------------------------------------------------
	function addOnloadCode($instruction) {
		$instruction = ltrim(ereg_replace("[[:blank:]|\n|\r]{1,}", " ", $instruction));
		$this->onLoadCode[] = $instruction;
	}
	
	//!-----------------------------------------------------------------
	// @function	Document::setShortcutIcon
	// @desc		Define o ícone do sistema, utilizado para criação de shortcuts
	//				ou identificação nos bookmarks nos navegadores
	// @param		iconUrl string		URL do ícone
	// @access		public	
	// @return		void
	//!-----------------------------------------------------------------
	function setShortcutIcon($iconUrl) {
		$this->appendHeaderContent("<LINK REL=\"shortcut icon\" HREF=\"{$iconUrl}\">");
	}

	//!-----------------------------------------------------------------
	// @function	Document::addStyle
	// @desc		Adiciona um arquivo do tipo stylesheet ao documento
	// @param		styleFile string	Arquivo de estilos CSS
	// @param		media string		Mídia para a qual o arquivo de estilos será utilizado
	// @access		public	
	// @return		void	
	// @see			Document::addScript
	// @see			Document::addScriptCode
	//!-----------------------------------------------------------------
	function addStyle($styleFile, $media="") {
		if (!in_array($styleFile, $this->styles)) {
			$this->styles[] = $styleFile;			
			$this->styleCode .= sprintf("<LINK REL=\"stylesheet\" TYPE=\"text/css\" HREF=\"%s\"%s>\n", $styleFile, ($media != '' ? " MEDIA=\"" . trim($media) . "\"" : ''));
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	Document::importStyle
	// @desc		Importa um estilo CSS a partir de uma fonte externa para o documento HTML
	// @param		styleUrl string		URL onde se encontra o arquivo CSS
	// @access		public	
	// @return		void
	//!-----------------------------------------------------------------
	function importStyle($styleUrl) {
		if (!in_array($styleUrl, $this->styles)) {
			$this->styles[] = $styleUrl;
			$this->styleExtCode .= sprintf("@import url(%s);\n", trim($styleUrl));
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	Document::addStyleCode
	// @desc		Adiciona declarações de estilo explícitas,
	//				a serem inseridas dentro do cabeçalho do documento
	// @param		styleCode string	Código de definição de estilos
	// @access		public	
	// @return		void
	//!-----------------------------------------------------------------
	function addStyleCode($styleCode) {
		$this->styleExtCode .= ltrim($styleCode) . "\n";
	}

	//!-----------------------------------------------------------------
	// @function	Document::addMetaData
	// @desc		Adiciona uma nova tag META ao documento
	// @param		name string			Nome da meta informação
	// @param		value mixed			Valor
	// @param		httpEquiv bool		Se TRUE, indica equivalência com um header HTTP (http-equiv)
	// @access		public	
	// @return		void
	//!-----------------------------------------------------------------
	function addMetaData($name, $value, $httpEquiv=FALSE) {
		if ($httpEquiv) {
			$this->metaTagsHttp[$name] = $value;
		} else {
			$name = strtoupper($name);
			$this->metaTagsName[$name] = $value;
		}
	}

	//!-----------------------------------------------------------------
	// @function	Document::appendHeaderContent
	// @desc		Insere um valor extra ao cabeçalho do documento
	// @access		public
	// @return		void	
	//!-----------------------------------------------------------------
	function appendHeaderContent($value) {
		$this->extraHeaderCode .= $value . "\n";
	}

	//!-----------------------------------------------------------------
	// @function	Document::addBodyCfg
	// @desc		Configura uma propriedade da tag BODY do documento,
	//				sobrescrevendo valores anteriormente setados
	// @param		attr mixed		Nome do atributo ou vetor associativo de atributos
	// @param		value string	""	Valor para o atributo em caso de atributo único
	// @access		public	
	// @return		void	
	// @see			Document::attachBodyEvent
	//!-----------------------------------------------------------------
	function addBodyCfg($attr, $value="") {
		if (TypeUtils::isArray($attr)) {
			foreach($attr as $key => $value)
				$this->bodyCfg[strtoupper($key)] = $value;
		} else {
			$this->bodyCfg[strtoupper($attr)] = $value;
		}
	}

	//!-----------------------------------------------------------------
	// @function	Document::attachBodyEvent
	// @desc		Adiciona um ação a um determinado evento tratado
	//				na tag BODY do documento
	// @param		event string	Nome de evento, como onLoad ou onUnload
	// @param		action string	Ação para o evento
	// @access		public	
	// @return		void	
	// @note		Use aspas simples na definição das ações dos eventos. Exemplo: "onLoad","funcao('parametro')"
	// @see			Document::addBodyCfg
	//!-----------------------------------------------------------------
	function attachBodyEvent($event, $action) {
		$action = str_replace("\"", "'", $action);
		$this->bodyEvents[$event] = (isset($this->bodyEvents[$event])) ? $this->bodyEvents[$event] . $action : $action;
	}
	
	//!-----------------------------------------------------------------
	// @function	Document::appendBodyContent
	// @desc		Insere um conteúdo HTML extra que será incluído no documento dentro da tag BODY
	// @param		value string		Valor a ser incluído
	// @access		public	
	// @return		void	
	//!-----------------------------------------------------------------
	function appendBodyContent($value) {
		$this->extraBodyContent .= $value . "\n";
	}

	//!-----------------------------------------------------------------
	// @function	Document::display
	// @desc		Envia para a tela o conteúdo do documento HTML
	// @access		public
	// @return		void	
	// @note		Se a compressão tiver sido habilitada através da
	//				função setCompression(), o conteúdo compactado
	//				será enviado à tela e as configurações de cache
	//!-----------------------------------------------------------------
	function display() {
		$this->_preRenderHeader(TRUE);
		$this->_buildDocumentHeader();
		$this->_buildDocumentBody();
		// retorna o conteúdo compactado ou não ao cliente
		$Agent =& UserAgent::getInstance();
		if ($this->makeCompression && !HttpResponse::headersSent() && !connection_aborted() && extension_loaded('zlib') && ($encoding = $Agent->matchAcceptList(array('x-gzip', 'gzip'), 'encoding'))) {
			// configura o nível de compressão
			System::setIni('zlip.output_compression', $this->compressionLevel);
			// inicializa o buffer de output (configurando para utilizar compressão)
			ob_start('ob_gzhandler');		
			// imprime o conteúdo da página e informação de debug
			echo $this->docHeader->toString(), $this->docBody->toString(), "\n", PHP2Go::getLangVal('COMPRESS_USE_MSG', $encoding), "\n";
			// envia o conteúdo compactado
			ob_end_flush();
		} else {
			// imprime o conteúdo da página
			echo $this->docHeader->toString(), $this->docBody->toString();
		}
	}

	//!-----------------------------------------------------------------
	// @function	Document::getContent
	// @desc		Constrói o cabeçalho e o corpo do documento e retorna o código gerado
	// @access		public
	// @return		string Código completo gerado para o documento
	//!-----------------------------------------------------------------
	function getContent() {
		$this->_preRenderHeader(FALSE);
		$this->_buildDocumentHeader();
		$this->_buildDocumentBody();
		return $this->docHeader->toString() . $this->docBody->toString();
	}

	//!-----------------------------------------------------------------
	// @function	Document::toFile
	// @desc		Permite gerar o conteúdo do documento e salvá-lo em um arquivo
	// @access		public
	// @param		fileName string		""	Nome do arquivo
	// @return		bool
	//!-----------------------------------------------------------------
	function toFile($fileName='') {
		$Mgr =& new FileManager();
		$Mgr->throwErrors = FALSE;
		if ($fileName == '') {
			return FALSE;
		} elseif (!$Mgr->open($fileName, FILE_MANAGER_WRITE_BINARY)) {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CANT_CREATE_FILE', $fileName), E_USER_ERROR, __FILE__, __LINE__);
			return FALSE;
		} else {
			$Mgr->write($this->getContent());
			$Mgr->close();
			return TRUE;
		}
	}

	//!-----------------------------------------------------------------
	// @function	Document::_initMetaTags
	// @desc		Inicializa as tags META do documento
	// @access		private
	// @return		void	
	//!-----------------------------------------------------------------
	function _initMetaTags() {
		$this->metaTagsName['TITLE']					=& $this->docTitle;
		$this->metaTagsName['AUTHOR']					=  PHP2Go::getConfigVal('AUTHOR', FALSE);
		$this->metaTagsName['DESCRIPTION']				=  PHP2Go::getConfigVal('DESCRIPTION', FALSE);
		$this->metaTagsName['KEYWORDS']					=  PHP2Go::getConfigVal('KEYWORDS', FALSE);
		$this->metaTagsName['CATEGORY']					=  PHP2Go::getConfigVal('CATEGORY', FALSE);
		$this->metaTagsName['CODE_LANGUAGE']			=  'PHP';		
		$this->metaTagsName['GENERATOR']				=  'PHP2Go Web Development Framework ' . PHP2GO_VERSION;
		$this->metaTagsName['DATE_CREATION']			=  PHP2Go::getConfigVal('DATE_CREATION', FALSE);
		$this->metaTagsName['DATE_REVISION']			=  Date::localDate();
		$this->metaTagsHttp['Content-Language']			=  $this->docLanguage;
	}

	//!-----------------------------------------------------------------
	// @function	Document::_initDeclaredElements
	// @desc		Busca todos os elementos declarados no layout
	//				do documento, inicializando seus valores
	// @access		private
	// @return		void	
	//!-----------------------------------------------------------------
	function _initDeclaredElements() {
		$foundElements = $this->Template->getDefinedVariables();
		$fElSize = sizeOf($foundElements);
		// não foram declarados elementos no layout de apresentação do documento
		if (!$fElSize) {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_EMPTY_DOC_LAYOUT'), E_USER_ERROR, __FILE__, __LINE__);
		} else {
			for ($i=0; $i<$fElSize; $i++)
				$this->elements[$foundElements[$i]] = "";
		}
	}

	//!-----------------------------------------------------------------
	// @function	Document::_addSystemElements
	// @desc		Adiciona ao documento elementos indispensáveis ao
	//				funcionamento geral
	// @access		private
	// @return		void	
	//!-----------------------------------------------------------------
	function _addSystemElements() {
		$Conf =& Conf::getInstance();
		$this->addScript(PHP2GO_ABSOLUTE_PATH . 'languages/' . $Conf->getConfig('LANGUAGE_NAME') . '.js');
		$this->addScript(PHP2GO_JAVASCRIPT_PATH . 'libs/div.js');
		$this->addScript(PHP2GO_JAVASCRIPT_PATH . 'libs/object.js');
		$this->addScript(PHP2GO_JAVASCRIPT_PATH . 'libs/window.js');
	}

	//!-----------------------------------------------------------------
	// @function	Document::_preRenderHeader
	// @desc		Executa operações antes da construção do cabeçalho da página
	// @access		private
	// @param		display bool	"TRUE" A página será exibida ou armazenada em um buffer
	// @return		void
	//!-----------------------------------------------------------------
	function _preRenderHeader($display=TRUE) {
		if (!$this->makeCache) {
			if ($display && !HttpResponse::headersSent() && !$this->makeCompression) {
				HttpResponse::addHeader('Expires', 'Tue, 1 Jan 1980 12:00:00 GMT');
				HttpResponse::addHeader('Last-Modified', gmdate('D, d M Y H:i:s') . ' GMT');
				HttpResponse::addHeader('Cache-Control', 'no-cache');
				HttpResponse::addHeader('Pragma', 'no-cache');
			}
			$this->metaTagsHttp['Expires']			= 'Tue, 1 Jan 1980 12:00:00 GMT';
			$this->metaTagsHttp['Last-Modified'] 	= gmdate('D, d M Y H:i:s') . ' GMT';
			$this->metaTagsHttp['Cache-Control'] 	= 'no-cache';
			$this->metaTagsHttp['Pragma']			= 'no-cache';
		}
		if (!$this->allowRobots) {
			$this->metaTagsName['ROBOTS'] = 'NOINDEX,NOFOLLOW,NOARCHIVE';
		}
	}

	//!-----------------------------------------------------------------
	// @function	Document::_buildDocumentHeader
	// @desc		Constrói o cabeçalho do documento a partir das
	//				tags META configuradas, e dos scripts e estilos
	//				inseridos no objeto
	// @access		private
	// @return		void	
	//!-----------------------------------------------------------------
	function _buildDocumentHeader() {
		$this->docHeader =& new StringBuffer();
		$this->docHeader->append("<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">\n");
		//$this->docHeader .= "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">\n";
		$this->docHeader->append("<HTML>\n");
		$this->docHeader->append("<HEAD>\n");
		$this->docHeader->append(sprintf("<META HTTP-EQUIV=\"Content-Type\" CONTENT=\"text/html; charset=%s\">\n", $this->docCharset));
		foreach($this->metaTagsHttp as $name => $content)
			$this->docHeader->append(sprintf("<META HTTP-EQUIV=\"%s\" CONTENT=\"%s\">\n", $name, htmlspecialchars($content)));
		foreach($this->metaTagsName as $name => $content)
			$this->docHeader->append(sprintf("<META NAME=\"%s\" CONTENT=\"%s\">\n", $name, htmlspecialchars($content)));
		$this->docHeader->append("<TITLE>{$this->docTitle}</TITLE>\n");
		if ($this->styleExtCode != '') {
			$this->docHeader->append(sprintf("<STYLE TYPE=\"text/css\">\n<!--\n%s//-->\n</STYLE>\n", $this->styleExtCode));
		}
		$this->docHeader->append($this->styleCode);		
		$this->docHeader->append($this->scriptCode);
		if (!empty($this->onLoadCode)) {
			$onLoad = "     function p2g_page_onLoad() {\n";
			foreach ($this->onLoadCode as $instruction)
				$onLoad .= "          $instruction\n";
			$onLoad .= "     }";
			$this->addScriptCode($onLoad, 'JavaScript');
			$this->attachBodyEvent('onLoad', 'p2g_page_onLoad();');
		}
		// scripts direcionados para a tag HEAD
		if (isset($this->scriptExtCode[SCRIPT_START])) {
			foreach($this->scriptExtCode[SCRIPT_START] as $language => $scripts) {
				if (StringUtils::right($scripts, 1) != "\n")
					$scripts .= "\n";
				$this->docHeader->append(sprintf("<SCRIPT LANGUAGE=\"%s\" TYPE=\"text/%s\">\n<!--\n%s//-->\n</SCRIPT>\n", $language, strtolower($language), $scripts));
			}
		}
		$this->docHeader->append($this->extraHeaderCode);
		$this->docHeader->append("</HEAD>\n");
	}

	//!-----------------------------------------------------------------
	// @function	Document::_buildDocumentBody
	// @desc		Constrói o corpo do documento a partir das configurações
	//				e eventos associados à tag BODY, do conteúdo retornado
	//				do template de layout de documento e seus respectivos 
	//				elementos (slots)
	// @access		private
	// @return		void	
	//!-----------------------------------------------------------------
	function _buildDocumentBody() {
		$this->docBody =& new StringBuffer();
		$this->docBody->append('<BODY');
		foreach($this->bodyCfg as $attr => $value)
			$this->docBody->append(sprintf(" %s=\"%s\"", $attr, str_replace('\'', '\"', $value)));
		foreach($this->bodyEvents as $event => $actions) {
			$this->docBody->append(sprintf(" %s=\"%s\"", $event, $actions));
		}
		$this->docBody->append(">\n<A NAME=\"php2go_top\"></A>\n");
		$this->docBody->append($this->_buildBodyContent());
		if (!empty($this->extraBodyContent)) {
			$this->docBody->append("\n" . $this->extraBodyContent);
		}
		// scripts direcionados para o fim da tag BODY
		if (isset($this->scriptExtCode[SCRIPT_END])) {
			foreach($this->scriptExtCode[SCRIPT_END] as $language => $scripts) {
				if (StringUtils::right($scripts, 1) != "\n")
					$scripts .= "\n";
				$this->docBody->append(sprintf("\n<SCRIPT LANGUAGE=\"%s\" TYPE=\"text/%s\">\n<!--\n%s//-->\n</SCRIPT>", $language, strtolower($language), $scripts));
			}
		}		
		$this->docBody->append("\n</BODY>");
		$this->docBody->append("\n</HTML>");
		$this->docBody->append("\n<!-- This content is powered by PHP2Go v. " . PHP2GO_VERSION . " (http://php2go.sourceforge.net) -->");
		$this->TimeCounter->stop();
		$this->docHeader->insert($this->docHeader->indexOf("<TITLE"), "<META NAME=\"TIMESPENT\" CONTENT=\"" . $this->TimeCounter->getElapsedTime() . "\">\n");
	}

	//!-----------------------------------------------------------------
	// @function	Document::_buildBodyContent
	// @desc		Constrói o conteúdo do corpo do documento a partir
	//				do conteúdo de cada elemento armazenado no objeto
	//				através do atributo elements
	// @access		private
	// @return		string Conteúdo do corpo do documento
	//!-----------------------------------------------------------------
	function _buildBodyContent() {
		foreach ($this->elements as $elementName => $elementValue) {
			if (TypeUtils::isInstanceOf($elementValue, 'Template') && $elementValue->isPrepared())
				$this->Template->assign($elementName, $elementValue->getContent());
			elseif ((is_scalar($elementValue)) && (!is_bool($elementValue)))
				$this->Template->assign($elementName, $elementValue);
		}
		return $this->Template->getContent();
	}
}
?>
