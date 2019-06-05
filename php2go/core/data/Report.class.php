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
// $Header: /www/cvsroot/php2go/core/data/Report.class.php,v 1.45 2005/08/31 22:19:24 mpont Exp $
// $Date: 2005/08/31 22:19:24 $

//------------------------------------------------------------------
import('php2go.data.PagedDataSet');
import('php2go.data.ReportSimpleSearch');
import('php2go.db.QueryBuilder');
import('php2go.net.Url');
import('php2go.net.UserAgent');
import('php2go.xml.XmlDocument');
import('php2go.util.Callback');
import('php2go.util.Statement');
//------------------------------------------------------------------

// @const REPORT_DEFAULT_VISIBLE_PAGES "10"
// Define o n�mero padr�o de links para outras p�ginas vis�veis 
define('REPORT_DEFAULT_VISIBLE_PAGES', 10);
// @const REPORT_COLUMN_SIZES_CUSTOM "1"
// C�lulas cuja largura deve ser definida pelo programador
define('REPORT_COLUMN_SIZES_CUSTOM', 1);
// @const REPORT_COLUMN_SIZES_FIXED "2"
// C�lulas s�o geradas com o mesmo tamanho
define('REPORT_COLUMN_SIZES_FIXED', 2);
// @const REPORT_COLUMN_SIZES_FREE "3"
// C�lulas s�o geradas sem atributo de tamanho - a largura � definida pelo browser
define('REPORT_COLUMN_SIZES_FREE', 3);
// @const REPORT_PAGING_DEFAULT "1"
// Sistema de pagina��o padr�o, com links para N pr�ximas p�ginas, primeira e �ltima
define('REPORT_PAGING_DEFAULT', 1);
// @const REPORT_PREVNEXT "2"
// Sistema de pagina��o com apenas dois links, para a p�gina anterior e a pr�xima
define('REPORT_PREVNEXT', 2);
// @const REPORT_FIRSTPREVNEXTLAST "3"
// Sistema de pagina��o com links para a primeira, anterior, pr�xima e �ltima p�ginas
define('REPORT_FIRSTPREVNEXTLAST', 3);

//!-----------------------------------------------------------------
// @class		Report
// @desc 		A classe Report constr�i relat�rios a partir de consultas
// 				SQL, permitindo a exibi��o por colunas ou por c�lulas, o
// 				agrupamento do relat�rio por uma ou mais colunas, pagina��o
// 				autom�tica, filtros simples ou compostos e ordena��o on-the-fly
// @package		php2go.data
// @extends 	PagedDataSet
// @uses		Db
// @uses		HtmlUtils
// @uses		HttpRequest
// @uses		QueryBuilder
// @uses		ReportSimpleSearch
// @uses		Statement
// @uses		StringUtils
// @uses		Template
// @uses		XmlDocument
// @author 		Marcos Pont
// @version		$Revision: 1.45 $
// @note		Confira exemplo de uso em examples/report.example.php
// @note		Se estiver utilizando PHP5, n�o esque�a de incluir a declara��o XML na primeira linha do arquivo de especifica��o
//!-----------------------------------------------------------------
class Report extends PagedDataSet 
{
	var $title;						// @var title string				T�tulo do relat�rio
	var $hasHeader = FALSE;			// @var hasHeader bool				"FALSE" Indica que o relat�rio � montado em colunas com os cabe�alhos na primeira linha
	var $numCols = 1;				// @var numCols int					"1" N�mero de registros por linha (quando hasHeader = FALSE), modelo de relat�rio em "c�lulas"
	var $colSizes;					// @var colSizes array				Vetor que permite definir tamanhos customizados para as colunas (hasHeader = TRUE)
	var $colSizesMode;				// @var colSizesMode int			Modo utilizado para definir os tamanhos das colunas
	var $group;						// @var group array					Vetor de colunas pelas quais os dados devem ser agrupados
	var $groupDisplay = array();	// @var groupDisplay array			"array()" Vetor de colunas a serem exibidas a cada novo agrupamento
	var $hidden = array();			// @var hidden array				"array()" Vetor de colunas a serem escondidas na exibi��o do relat�rio
	var $lineHandler;				// @var lineHandler Callback object	Fun��o ou m�todo para tratamento e formata��o de cada uma das linhas do relat�rio
	var $columnAliases = array();	// @var columnAliases array			"array()" Armazena os aliases das colunas (nomes customizados)
	var $columnHandler = array();	// @var columnHandler mixed			"array()" Fun��es ou m�todos para tratamento das colunas do relat�rio
	var $emptyBlock;				// @var emptyBlock string			Permite a defini��o de um bloco alternativo a ser criado para relat�rios por c�lula quando o n�mero de c�lulas n�o completa a linha
	var $emptyTemplate;				// @var emptyTemplate array			Armazena o arquivo e as vari�veis de substitui��o do template customizado para tratamento de relat�rio vazio
	var $searchTemplate;			// @var searchTemplate array		Armazena o arquivo e as vari�veis de substitui��o do template do formul�rio de filtros de pesquisa
	var $extraVars;					// @var extraVars string			Par�metros extra que devem ser enviados junto com requisi��es de pagina��o e reordenamento
	var $pagingStyle;				// @var pagingStyle array			Configura��es dos links de pagina��o do relat�rio
	var $styleSheet;				// @var styleSheet array			Vetor que armazena os estilos utilizados no formul�rio de busca, nos cabe�alhos e links do relat�rio
	var $altStyle;					// @var altStyle array				Vetor que armazena estilos de altern�ncia para as linhas do relat�rio
	var $hlFormat;					// @var hlFormat string				Formato de destaque de valores de pesquisa encontrados
	var $icons; 					// @var icons array					Vetor de �cones da classe (orderasc = ordena��o ascendente, orderdesc = ordena��o descendente)
	var $isPrintable = FALSE;		// @var isPrintable bool			"FALSE" Indica se o relat�rio est� sendo gerado para impress�o
	var $pageBreak;					// @var pageBreak int				Quebra de p�gina (para a vers�o de impress�o)
	var $rootAttrs = array();		// @var rootAttrs array				"array()" Vetor de atributos da raiz do XML do relat�rio
	var $debug = FALSE;				// @var debug bool					"FALSE" Habilita ou desabilita debug na execu��o da consulta do relat�rio
	var $Template = NULL;			// @var Template Template object	Template para gera��o do conte�do do relat�rio
	var $_loaded = FALSE;			// @var _loaded bool				"FALSE" Indica se o relat�rio j� foi constru�do com o m�todo build
	var $_visiblePages;				// @var _visiblePages int			N�mero m�ximo de links vis�veis para outras p�ginas
	var $_firstPage;				// @var _firstPage int				Primeiro link para p�gina vis�vel no relat�rio
	var $_lastPage;					// @var _lastPage int				�ltimo link para p�gina vis�vel no relat�rio
	var $_navigation;				// @var _navigation array			Armazena dados para implementa��o da navega��o para outras p�ginas
	var $_dataSource = array();		// @var _dataSource array			"array()" Dados da consulta SQL, extra�dos do arquivo XML
	var $_bindVars = array();		// @var _bindVars array				"array()" Vari�veis de amarra��o da consulta SQL
	var $_sqlCode = '';				// @var _sqlCode string				"" C�digo SQL final do relat�rio
	var $_order;					// @var _order string				Coluna atual da ordena��o customizada pelo usu�rio
	var $_orderType;				// @var _orderType string			Tipo atual da ordena��o customizada pelo usu�rio
	var $_orderLinks = TRUE;		// @var _orderLinks bool			"TRUE" Se verdadeiro, habilita a gera��o de links nos cabe�alhos das colunas para reordena��o do relat�rio
	var $_Document = NULL;			// @var _Document Document object	Documento onde o relat�rio ser� inserido
	var $_SimpleSearch = NULL;		// @var _SimpleSearch ReportSimpleSearch object		Controla o formul�rio de busca/filtro
	
	//!-----------------------------------------------------------------
	// @function	Report::Report
	// @desc		Construtor da classe de relat�rios do PHP2Go. Cria uma
	// 				conex�o com o banco e realiza as configura��es iniciais
	// @access 		public
	// @param		xmlFile string			Arquivo XML com as defini��es de gera��o do relat�rio
	// @param		templateFile string		Arquivo template da interface do relat�rio
	// @param		&Document Document object	Objeto Document onde o relat�rio ser� inserido
	//!-----------------------------------------------------------------
	function Report($xmlFile, $templateFile, &$Document) {
		parent::PagedDataSet('db');
		if (!TypeUtils::isObject($Document) || !TypeUtils::isInstanceOf($Document, 'document')) {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_INVALID_OBJECT', 'Document'), E_USER_ERROR, __FILE__, __LINE__);
		}
		$this->icons = array('orderasc' => PHP2GO_ICON_PATH . "report_order_asc.gif", 'orderdesc' => PHP2GO_ICON_PATH . "report_order_desc.gif");
		$this->colSizesMode = REPORT_COLUMN_SIZES_FREE;
		$this->pagingStyle = array(REPORT_PAGING_DEFAULT, NULL);		
		$this->Template =& new Template($templateFile);
		$this->Template->parse();
		$this->_Document =& $Document;
		$this->_SimpleSearch =& new ReportSimpleSearch();
		$this->_visiblePages = REPORT_DEFAULT_VISIBLE_PAGES;
		$this->_order = HttpRequest::get('order');		
		$this->_orderType = TypeUtils::ifNull(HttpRequest::get('ordertype'), 'a');
		$this->_processXml($xmlFile);
		parent::registerDestructor($this, '_Report');
	}
	
	//!-----------------------------------------------------------------
	// @function	Report::_Report
	// @desc		Destrutor do objeto Report
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function _Report() {
		unset($this);
	}
	
	//!-----------------------------------------------------------------
	// @function	Report::getSql
	// @desc		Retorna o c�digo final da consulta SQL ou do statement
	//				utilizado para gerar o relat�rio
	// @access		public
	// @return		string C�digo SQL do relat�rio
	//!-----------------------------------------------------------------
	function getSql() {
		return $this->_sqlCode;
	}
	
	//!-----------------------------------------------------------------
	// @function	Report::getFirstPageUrl
	// @desc		Retorna a URL que aponta para a primeira p�gina do relat�rio
	// @access		public
	// @return		string	URL apontando para a primeira p�gina
	//!-----------------------------------------------------------------
	function getFirstPageUrl() {
		return $this->_generatePageLink(1);
	}
	
	//!-----------------------------------------------------------------
	// @function	Report::getLastPageUrl
	// @desc		Retorna a URL que aponta para a �ltima p�gina do relat�rio
	// @access		public
	// @return		string	URL apontando para a �ltima p�gina
	//!-----------------------------------------------------------------
	function getLastPageUrl() {
		return $this->_generatePageLink(parent::getPageCount());
	}
	
	//!-----------------------------------------------------------------
	// @function	Report::getPreviousPageUrl
	// @desc		Retorna a URL que aponta para a p�gina anterior do relat�rio,
	//				ou FALSE se a p�gina atual � a primeira
	// @access		public
	// @return		mixed	URL da p�gina anterior ou FALSE
	// @note		Para que este m�todo possa ser utilizado, deve ser executado
	//				ap�s a chamada do m�todo Report::build()
	//!-----------------------------------------------------------------
	function getPreviousPageUrl() {
		if ($previousPage = parent::getPreviousPage())
			return $this->_generatePageLink($previousPage);
		return FALSE;
	}
	
	//!-----------------------------------------------------------------
	// @function	Report::getNextPageUrl
	// @desc		Retorna a URL que aponta para a pr�xima p�gina do relat�rio,
	//				ou FALSE se a p�gina atual � a �ltima
	// @access		public
	// @return		mixed	URL da pr�xima p�gina ou FALSE
	// @note		Para que este m�todo possa ser utilizado, deve ser executado
	//				ap�s a chamada do m�todo Report::build()
	//!-----------------------------------------------------------------
	function getNextPageUrl() {
		if ($nextPage = parent::getNextPage())
			return $this->_generatePageLink($nextPage);
		return FALSE;
	}	
	
	//!-----------------------------------------------------------------
	// @function	Report::setTitle
	// @desc 		Configura o t�tulo do relat�rio
	// @access 		public
	// @param 		title string	T�tulo para o relat�rio
	// @param 		docTitle bool	"FALSE" Concatenar este t�tulo ao documento
	// @return		void	
	//!-----------------------------------------------------------------
	function setTitle($title, $docTitle=FALSE) {
		$this->title = $title;
		if ($docTitle)
			$this->_Document->appendTitle($this->title, TRUE);
	}	
	
	//!-----------------------------------------------------------------
	// @function 	Report::setVisiblePages
	// @desc 		Configura o n�mero m�ximo de p�ginas vis�veis na tela
	// @access 		public
	// @param 		pages int		N�mero de p�ginas vis�veis na tela
	// @return		void	
	//!-----------------------------------------------------------------
	function setVisiblePages($pages) {
		$this->_visiblePages = max(TypeUtils::parseInteger($pages), 1);
	}
	
	//!-----------------------------------------------------------------
	// @function 	Report::setColumns
	// @desc 		Informa quantos registros por linha o relat�rio deve exibir
	// @access 		public
	// @param 		numCols int		N�mero de registros por linha
	// @return		void
	// @see 		Report::useHeader	
	// @note 		Esta fun��o s� ter� efeito sobre o relat�rio se o uso
	// 				de cabe�alhos n�o estiver ativo	
	//!-----------------------------------------------------------------
	function setColumns($numCols) {
		$this->numCols = max(TypeUtils::parseInteger($numCols), 1);
	}

	//!-----------------------------------------------------------------
	// @function 	Report::useHeader
	// @desc 		Configura o relat�rio para exibir 1 registro apenas por
	// 				linha, criando no topo da p�gina cabe�alhos para todas
	// 				as colunas
	// @access 		public
	// @return		void	
	//!-----------------------------------------------------------------
	function useHeader() {
		$this->hasHeader = TRUE;
	}
	
	//!-----------------------------------------------------------------
	// @function	Report::isPrintable
	// @desc		Indica que o relat�rio est� sendo gerado para impress�o. Desta forma, �
	//				necess�rio informar o valor de quebra de p�gina. A pagina��o e a ordena��o
	//				din�mica ser�o desabilitadas
	// @access		public
	// @param		pageBreak int	Quebra de p�gina
	// @return		void
	//!-----------------------------------------------------------------
	function isPrintable($pageBreak) {
		@set_time_limit(0);
		$this->isPrintable = TRUE;
		$this->pageBreak = max($pageBreak, 1);
		$this->_SimpleSearch->clear();
	}
	
	//!-----------------------------------------------------------------
	// @function 	Report::setColumnSizes
	// @desc 		Define os tamanhos das colunas em cada linha do relat�rio
	// @access 		public
	// @param 		sizes mixed		"NULL" Modo de constru��o (vide constantes da classe) ou vetor de tamanhos customizados para as colunas (deve somar 100)
	// @return		void
	// @see			Report::useHeader
	// @note 		Esta fun��o s� ter� efeito se os cabe�alhos forem habilitados com a fun��o useHeader
	//!-----------------------------------------------------------------
	function setColumnSizes($param = NULL) {
		if (TypeUtils::isArray($param)) {
			if (array_sum($param) != 100) {
				PHP2Go::raiseError(PHP2Go::getLangVal('ERR_REPORT_COL_SIZES_SUM'), E_USER_ERROR, __FILE__, __LINE__);
			} else {
				$this->colSizesMode = REPORT_COLUMN_SIZES_CUSTOM;
				$this->colSizes = $param;
			}				
		} elseif ($param == REPORT_COLUMN_SIZES_FIXED || $param == REPORT_COLUMN_SIZES_FREE) {
			$this->colSizesMode = $param;
		} else {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_REPORT_INVALID_COLSIZES', $param), E_USER_ERROR, __FILE__, __LINE__);
		}
	}
	
	//!-----------------------------------------------------------------
	// @function 	Report::setGroup
	// @desc 		Define o agrupamento que deve ser realizado no relat�rio
	// @access 		public
	// @param 		groupBy mixed	Coluna ou vetor de colunas que devem ser usadas para agrupamento
	// @param 		display mixed	"" Coluna ou vetor de colunas que devem ser exibidas
	// 								a cada troca de grupo. Se n�o for informado, exibir�
	// 								as mesmas colunas indicadas no par�metro $groupBy
	// @return		void	
	// @note 		Todas as colunas devem ser nomes/alias v�lidos para a consulta
	//!-----------------------------------------------------------------
	function setGroup($groupBy, $display = '') {
		$this->group = (!TypeUtils::isArray($groupBy) ? array($groupBy) : $groupBy);		
		if ($display == '')
			$this->groupDisplay = (!TypeUtils::isArray($groupBy) ? array($groupBy) : $groupBy);
		elseif (is_scalar($display))
			$this->groupDisplay = (!TypeUtils::isArray($display) ? array($display) : $display);
		else
			$this->groupDisplay = (!TypeUtils::isArray($groupBy) ? array($groupBy) : $groupBy);
		foreach ($this->groupDisplay as $field)
			if (in_array($field, $this->hidden))
				PHP2Go::raiseError(PHP2Go::getLangVal('ERR_REPORT_HIDDEN_GROUP', $field), E_USER_ERROR, __FILE__, __LINE__);
	}
	
	//!-----------------------------------------------------------------
	// @function	Report::setHidden
	// @desc		Define como escondido um ou mais campos do relat�rio
	// @access		public
	// @param		fieldName mixed	Nome de campo ou vetor de campos
	// @return		void
	//!-----------------------------------------------------------------
	function setHidden($fieldName) {
		if (TypeUtils::isArray($fieldName)) {
			// as colunas n�o podem pertencer ao conjunto de colunas de cabe�alho de grupo (groupDisplay)
			foreach ($fieldName as $field)
				if (in_array($field, $this->groupDisplay))
					PHP2Go::raiseError(PHP2Go::getLangVal('ERR_REPORT_GROUP_HIDDEN', $field), E_USER_ERROR, __FILE__, __LINE__);
			if (!isset($this->hidden))
				$this->hidden = $fieldName;
			else
				$this->hidden = array_merge($this->hidden, $fieldName);
		} else {
			// a coluna n�o pode pertencer ao conjunto de colunas de cabe�alho de grupo (groupDisplay)
			if (in_array($fieldName, $this->groupDisplay))
				PHP2Go::raiseError(PHP2Go::getLangVal('ERR_REPORT_GROUP_HIDDEN', $fieldName), E_USER_ERROR, __FILE__, __LINE__);			
			if (!isset($this->hidden))
				$this->hidden = array($fieldName);
			else
				$this->hidden[] = $fieldName;
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	Report::bind
	// @desc		Adiciona uma vari�vel ou um conjunto de vari�veis para
	//				relat�rios que utilizam stored procedures
	// @param		variable mixed	Nome da vari�vel ou array associativo de vari�veis e valores
	// @param		value mixed		"" Valor da vari�vel	
	// @access		public	
	// @return		void
	// @note		Este m�todo n�o dever� ser utilizado para a atribui��o de valores
	//				para vari�veis no padr�o ~var~ utilizado pelo PHP2Go
	//!-----------------------------------------------------------------
	function bind($variable, $value = '') {
		if (TypeUtils::isHashArray($variable)) {
			foreach ($variable as $key => $value)
				$this->_bindVars[$key] = $value;
		} elseif (TypeUtils::isString($variable)) {
			$this->_bindVars[$variable] = $value;
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	Report::setLineHandler
	// @desc		Define uma fun��o ou m�todo que deve tratar cada linha
	//				do relat�rio, para fins de altera��o ou formata��o de valores
	// @access		public
	// @param		callback mixed		Nome de fun��o ou vetor contendo objeto e m�todo
	// @return		void
	//!-----------------------------------------------------------------
	function setLineHandler($callback) {
		$this->lineHandler = new Callback($callback);
	}

	//!-----------------------------------------------------------------
	// @function	Report::setColumnHandler
	// @desc		Define uma fun��o ou m�todo que deve tratar uma coluna espec�fica do relat�rio
	// @access		public
	// @param		columnName string	Nome da coluna
	// @param		callback mixed		Nome de fun��o ou vetor contendo objeto e m�todo
	// @return		void
	//!-----------------------------------------------------------------
	function setColumnHandler($columnName, $callback) {
		$this->columnHandler[$columnName] = new Callback($callback);
	}
	
	//!-----------------------------------------------------------------
	// @function	Report::setColumnAlias
	// @desc		Define um alias para uma determinada coluna do relat�rio
	// @access		public
	// @param		columnName mixed	Nome da coluna ou hash array com colunas=>aliases
	// @param		alias string		"" Alias para a coluna
	// @return		void
	//!-----------------------------------------------------------------
	function setColumnAlias($columnName, $alias = '') {
		if (TypeUtils::isHashArray($columnName)) {
			foreach ($columnName as $key => $value) {
				$this->columnAliases[$key] = $value;
			}
		} else {
			$this->columnAliases[$columnName] = $alias;
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	Report::disableOrderByLinks
	// @desc		Desabilita a funcionalidade de ordena��o de colunas a partir dos cabe�alhos
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function disableOrderByLinks() {
		$this->_orderLinks = FALSE;
	}
	
	//!-----------------------------------------------------------------
	// @function 	Report::setSearchMaskFunction
	// @desc 		Associa uma fun��o ou m�todo a uma m�scara de dados, para realizar
	// 				convers�o de valor nos par�metros de busca utilizados
	// @access 		public
	// @param 		mask string			Nome da m�scara
	// @param 		callback string		Nome da fun��o ou vetor objeto+m�todo a ser executada
	// @return		void	
	//!-----------------------------------------------------------------
	function setSearchMaskFunction($mask, $callback) {
		$this->_SimpleSearch->addMaskFunction($mask, $callback);
	}
	
	//!-----------------------------------------------------------------
	// @function	Report::setSearchTemplate
	// @desc		Define um template alternativo para o formul�rio de busca simples
	// @access		public
	// @param		searchTemplate string	Caminho completo do arquivo template
	// @param		templateVars array		"array()" Conjunto de vari�veis para atribui��o
	// @note		O template criado deve possuir as mesmas vari�veis declaradas no template original,
	//				localizado em PHP2GO_ROOT/resources/templates/simplesearch.tpl
	// @return		void
	//!-----------------------------------------------------------------
	function setSearchTemplate($searchTemplate, $templateVars = array()) {
		$this->searchTemplate = array(
			'file' => $searchTemplate,
			'vars' => (TypeUtils::isHashArray($templateVars)) ? $templateVars : array()
		);
	}
	
	//!-----------------------------------------------------------------
	// @function 	Report::setEmptyTemplate
	// @desc 		Configura o template a ser utilizado quando o relat�rio
	// 				ou o filtro de pesquisa n�o retornarem resultados
	// @access 		public
	// @param 		templateFile string	Nome do arquivo template
	// @param 		templateVars array	"array()" Vetor associativo com as vari�veis a serem substitu�das no template
	// @return		void	
	//!-----------------------------------------------------------------
	function setEmptyTemplate($templateFile, $templateVars = array()) {
		$this->emptyTemplate = array(
			'file' => $templateFile,
			'vars' => (TypeUtils::isHashArray($templateVars)) ? $templateVars : array()
		);
	}
	
	//!-----------------------------------------------------------------
	// @function	Report::setEmptyBlock
	// @desc		Permite definir um bloco a ser utilizado em relat�rios do tipo
	//				CELL (N c�lulas por linha com M placeholders em cada c�lula) para
	//				o caso da �ltima linha de dados tiver de ser completada com c�lulas
	//				em branco ou com um c�digo diferente do bloco loop_cell
	// @access		public
	// @param		blockName string	Nome do bloco
	// @return		void
	//!-----------------------------------------------------------------
	function setEmptyBlock($blockName) {
		$this->emptyBlock = $blockName;
	}
	
	//!-----------------------------------------------------------------
	// @function 	Report::setExtraVars
	// @desc 		Concatena o texto indicado pelo par�metro $extraVars a todos os links 
	//				para outras p�ginas, submiss�o de busca e reordena��o dos dados
	// @access 		public
	// @param 		extraVars string		Texto em formato 'urlencode'
	// @return		void	
	//!-----------------------------------------------------------------
	function setExtraVars($extraVars) {
		if (ereg("[^=]+=[^=]+", $extraVars)) {
			$this->extraVars = ltrim($extraVars);
			if ($this->extraVars[0] == '&') {
				$this->extraVars = substr($this->extraVars, 1);
			}
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	Report::setPagingStyle
	// @desc		Define o estilo dos links de pagina��o que ser�o gerados
	//				para o relat�rio. Os tipos est�o definidos em constantes
	//				da pr�pria classe
	// @access		public
	// @param		style int			Estilo de links de pagina��o
	// @param		params array		"NULL" Par�metros para montagem da pagina��o
	// @return		void
	//!-----------------------------------------------------------------
	function setPagingStyle($style, $params = NULL) {
		$this->pagingStyle = array($style, $params);
	}
	
	//!-----------------------------------------------------------------
	// @function 	Report::setStyleMapping
	// @desc 		Configura os estilos CSS do relat�rio
	// @access 		public
	// @param 		link string			"" Estilo CSS para os links e outros textos
	// @param 		filter string		"" Estilo CSS para os campos do formul�rio de busca
	// @param 		button string		"" Estilo CSS para os bot�es do formul�rio de busca
	// @param 		title string		"" Estilo CSS para o t�tulo do relat�rio
	// @param		header string		"" Estilo CSS para os cabe�alhos do relat�rio, se habilitados
	// @return		void	
	//!-----------------------------------------------------------------
	function setStyleMapping($link='', $filter='', $button='', $title='', $header='') {
		if (trim($header) == '')
			$header = $link;
		$this->styleSheet = array(
			'link' => $link,
			'filter' => $filter,
			'button' => $button,
			'title' => $title,
			'header' => $header			
		);
	}
	
	//!-----------------------------------------------------------------
	// @function 	Report::setAlternateStyle
	// @desc 		Configura estilos CSS para serem alternados nas linhas do relat�rio
	// @access 		public
	// @return		void
	// @note 		A fun��o recebe N par�metros de entrada, que ser�o os
	// 				estilos exibidos alternadamente no relat�rio. A vari�vel
	// 				{alt_style} deve ser declarada no bloco 'loop_cell'
	//!-----------------------------------------------------------------
	function setAlternateStyle() {
		if (func_num_args() < 2)
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_REPORT_MIN_ALT_STYLE'), E_USER_ERROR, __FILE__, __LINE__);
		else
			$this->altStyle = func_get_args();
	}

	//!-----------------------------------------------------------------
	// @function 	Report::enableHighlight
	// @desc 		Configura cores para destacar valores de busca nos resultados de uma consulta
	// @access 		public
	// @param 		fgColor string	Cor em formato RGB para o texto
	// @param 		bgColor string	"" Cor em formato RGB para o fundo
	// @return		void	
	//!-----------------------------------------------------------------
	function enableHighlight($fgColor, $bgColor = "") {
		$this->hlFormat = "color:$fgColor";
		if ($bgColor != "")
			$this->hlFormat .= ";background-color:$bgColor";
	}
	
	//!-----------------------------------------------------------------
	// @function	Report::build
	// @desc		Constr�i o conjunto de dados da p�gina atual do relat�rio
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function build() {
		if (!$this->_loaded) {
			$this->_buildDataSet();
			$this->_calculateLimits();
			$this->_loaded = TRUE;
		}
	}
	
	//!-----------------------------------------------------------------
	// @function 	Report::display
	// @desc 		Constr�i e imprime o relat�rio
	// @access 		public
	// @return		void	
	//!-----------------------------------------------------------------
	function display() {
		$content = $this->getContent();
		print $content;
	}	
	
	//!-----------------------------------------------------------------
	// @function 	Report::getContent
	// @desc 		Constr�i e retorna o conte�do do relat�rio
	// @access 		public
	// @return		string	Conte�do final do relat�rio
	//!-----------------------------------------------------------------
	function getContent() {		
		if (!$this->_loaded)
			$this->build();
		if (parent::getTotalRecordCount() > 0) {			
			$this->_buildContent();
			return $this->Template->getContent();
		} else {
			if (!isset($this->emptyTemplate['file'])) {
				$EmptyTpl = new Template(PHP2GO_TEMPLATE_PATH . 'emptyreport.tpl');
				$EmptyTpl->parse();
				$EmptyTpl->assign('url', HttpRequest::basePath());
				$EmptyTpl->assign($this->styleSheet);
				$EmptyTpl->assign(TypeUtils::toArray(PHP2Go::getLangVal('REPORT_EMPTY_VALUES')));				
			} else {
				$EmptyTpl = new Template($this->emptyTemplate['file']);
				$EmptyTpl->parse();
				if (!empty($this->emptyTemplate['vars']))
					$EmptyTpl->assign(TypeUtils::toArray($this->emptyTemplate['vars']));
			}
			return $EmptyTpl->getContent();
		}
	}

	//!-----------------------------------------------------------------
	// @function 	Report::_processXml
	// @desc 		Processa o arquivo XML que cont�m a especifica��o da
	// 				consulta SQL do relat�rio e os par�metros de filtragem
	// 				de dados
	// @access 		private
	// @param		xmlFile string	Caminho completo para o arquivo XML
	// @return		void	
	//!-----------------------------------------------------------------
	function _processXml($xmlFile) {
		$XmlDocument = new XmlDocument();
		$XmlDocument->parseXml($xmlFile);
		$XmlRoot =& $XmlDocument->getRoot();
		$this->rootAttrs = $XmlRoot->getAttributes();
		if (isset($this->rootAttrs['TITLE']))
			$this->setTitle($this->rootAttrs['TITLE'], TRUE);
		if ($children = $XmlRoot->getChildrenTagsArray()) {
			foreach ($children as $tag => $Node) {				
				if ($tag == 'DATASOURCE' && $dsChildren = $Node->getChildrenTagsArray()) {
					// ID de conex�o
					$this->adapter->setParameter('connectionId', TypeUtils::ifFalse($Node->getAttribute('CONNECTION'), NULL));
					// Par�metros da consulta SQL
					$this->_dataSource = array(
						'PROCEDURE' => isset($dsChildren['PROCEDURE']) ? $dsChildren['PROCEDURE']->value : '',
						'FIELDS' => isset($dsChildren['FIELDS']) ? $dsChildren['FIELDS']->value : '',
						'TABLES' => isset($dsChildren['TABLES']) ? $dsChildren['TABLES']->value : '',
						'CLAUSE' => isset($dsChildren['CLAUSE']) ? $dsChildren['CLAUSE']->value : '',
						'GROUPBY' => isset($dsChildren['GROUPBY']) ? $dsChildren['GROUPBY']->value : '',
						'ORDERBY' => isset($dsChildren['ORDERBY']) ? $dsChildren['ORDERBY']->value : ''
					);
					// Cursor
					if (isset($dsChildren['PROCEDURE']))
						$this->_dataSource['CURSORNAME'] = $dsChildren['PROCEDURE']->getAttribute('CURSORNAME');
				}
				else if ($tag == 'DATAFILTERS' && $Node->hasChildren()) {
					for ($i=0; $i < $Node->getChildrenCount(); $i++) {
						$Child =& $Node->getChild($i);
						$this->_SimpleSearch->addFilter($Child->getAttributes());
					}
				}
			}
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	Report::_buildDataSet
	// @desc		M�todo que constr�i o conjunto de dados da p�gina atual do relat�rio, 
	//				incluindo filtros de pesquisa se existentes e cl�usulas de ordena��o customizadas
	// @access		private
	// @return		void
	//!-----------------------------------------------------------------	
	function _buildDataSet() {
		// processa a substitui��o de poss�veis vari�veis nos membros da consulta SQL
		foreach ($this->_dataSource as $element => $value) {
			if (ereg("~[^~]+~", $value))
				$this->_dataSource[$element] = Statement::evaluate($value);
		}
		// debug do datasource montado
		if ($this->debug) {
			print('REPORT DEBUG --- DATASOURCE ELEMENTS :');
			dumpVariable($this->_dataSource);
			$this->adapter->setParameter('debug', TRUE);
		} else {
			$this->adapter->setParameter('debug', FALSE);
		}
		// verifica se a consulta ir� executar uma procedure no banco de dados
		if ($this->_dataSource['PROCEDURE'] != '') {
			$isProcedure = TRUE;
			$cursorName = @$this->_dataSource['CURSORNAME'];
			$this->_sqlCode = trim($this->_dataSource['PROCEDURE']);
			if (ereg(':CLAUSE', $this->_dataSource['PROCEDURE']))
				$this->_bindVars['CLAUSE'] = $this->_SimpleSearch->getSearchClause();
			$this->_bindVars['ORDER'] = $this->_orderByClause();
		// do contr�rio, constr�i a SQL a partir dos elementos declarados no arquivo XML
		} else {
			$isProcedure = FALSE;
			$cursorName = NULL;
			$Query = new QueryBuilder($this->_dataSource['FIELDS'], $this->_dataSource['TABLES'], $this->_dataSource['CLAUSE'], $this->_dataSource['GROUPBY']);		
			$Query->addClause($this->_SimpleSearch->getSearchClause());
			$Query->setOrder($this->_orderByClause());
			$this->_sqlCode = $Query->getQuery();			
		}
		// constru��o da p�gina de resultados
		if (!$this->isPrintable)
			PagedDataSet::load($this->_sqlCode, $this->_bindVars, $isProcedure, $cursorName);
		else {
			DataSet::load($this->_sqlCode, $this->_bindVars, $isProcedure, $cursorName);
		}
	}
	
	//!-----------------------------------------------------------------
	// @function 	Report::_calculateLimits
	// @desc 		Calcula os dados de pagina��o: p�gina atual, primeira p�gina exibida 
	//				na tela, �ltima p�gina exibida na tela. Verifica se � poss�vel navegar 
	//				para a primeira, N anteriores, N pr�ximas e �ltima p�ginas
	// @access 		private
	// @return		void	
	//!-----------------------------------------------------------------
	function _calculateLimits() {
		// valida��o para evitar p�ginas fora do escopo do relat�rio
		$basePage = (parent::getCurrentPage() > parent::getPageCount() ? parent::getPageCount() : parent::getCurrentPage());
		// c�lculo do primeiro link de p�gina vis�vel na tela
		if (($basePage % $this->_visiblePages) == 0) {
			$this->_firstPage = ((TypeUtils::parseInteger($basePage / $this->_visiblePages) - 1) * $this->_visiblePages) + 1;
		} else {
			$this->_firstPage = (TypeUtils::parseInteger($basePage / $this->_visiblePages) * $this->_visiblePages) + 1;
		}
		// c�lculo do �ltimo link de p�gina vis�vel na tela
		if (($this->_firstPage + $this->_visiblePages - 1) <= parent::getPageCount()) {
			$this->_lastPage = $this->_firstPage + $this->_visiblePages - 1;
		} else {
			$this->_lastPage = parent::getPageCount();
		}	
		// � poss�vel navegar para a primeira p�gina ?
		if (parent::getCurrentPage() > 1) {
			$this->_navigation['firstPage'] = 1;
		}
		// existe uma p�gina anterior ?
		if (parent::getCurrentPage() > 1) {
			if (parent::getCurrentPage() > $this->_visiblePages && parent::getCurrentPage() == $this->_firstPage)
				$this->_navigation['previousPage'] = $this->_firstPage - 1;
			else
				$this->_navigation['previousPage'] = parent::getCurrentPage() - 1;
		}
		// existe uma tela anterior ?
		if (parent::getCurrentPage() > $this->_visiblePages) {
			$this->_navigation['previousScreen'] = $this->_firstPage - 1;
		}		
		// existe uma pr�xima p�gina ?
		if (parent::getCurrentPage() < parent::getPageCount()) {
			if ($this->_lastPage < parent::getPageCount() && parent::getCurrentPage() == $this->_lastPage)
				$this->_navigation['nextPage'] = $this->_lastPage + 1;
			else
				$this->_navigation['nextPage'] = parent::getCurrentPage() + 1;			
		}
		// existe uma pr�xima tela ?
		if ($this->_lastPage < parent::getPageCount()) {
			$this->_navigation['nextScreen'] = $this->_lastPage + 1;
		}
		// � poss�vel navegar para a �ltima p�gina
		if (parent::getCurrentPage() < parent::getPageCount()) {
			$this->_navigation['lastPage'] = parent::getPageCount();
		}
	}

	//!-----------------------------------------------------------------
	// @function 	Report::_buildContent
	// @desc 		Esta fun��o constr�i o conte�do da p�gina. Exibe registros de acordo com 
	//				as op��es do usu�rio (uso de header, m�ltiplas colunas, uso de agrupamento, 
	//				fun��es de dados, etc...). Gera tamb�m o formul�rio de busca, se estiver habilitado
	// @access 		private
	// @return		void	
	//!-----------------------------------------------------------------
	function _buildContent() {
		$aRow = 0;	// contador de registros utilizados no result set
		$aLine = 0;	// contador de linhas de relat�rio geradas (somente usado quando hasHeader==FALSE)
		$aCell = 1;	// contador da c�lula atual (somente usado quando hasHeader==FALSE)
		$errorMsg = NULL;
		if (!$this->_checkHidden($errorMsg))
			PHP2Go::raiseError($errorMsg, E_USER_ERROR, __FILE__, __LINE__);
		if (isset($this->group)) {			
			if (!$this->_checkGroup($errorMsg)) {
				PHP2Go::raiseError($errorMsg, E_USER_ERROR, __FILE__, __LINE__);
			} else {
				if ($this->hasHeader) {
					while ($lineData = parent::fetch()) {
						$this->Template->createBlock('loop_line');
						// houve troca de agrupamento? gera grupo, cabe�alho e gera nova linha
						if ($this->_matchGroup($lineData)) {
							$this->_dataGroup($lineData);
							$this->Template->createBlock('loop_line');
							$this->_dataHeader();
							$this->Template->createBlock('loop_line');							
						}
						// gera as n colunas
						$this->_dataColumns($lineData, NULL);
						$aRow++;
						if ($this->isPrintable && (($aRow % $this->pageBreak) == 0))
							$this->_buildPageBreak();						
					}
				} else {
					$this->Template->createBlock('loop_line');
					while ($lineData = parent::fetch()) {
						// houve troca de agrupamento ?
						if ($this->_matchGroup($lineData)) {
							// � a primeira linha ? gera grupo e uma c�lula
							if ($aRow == 0) {
								$this->_dataGroup($lineData);
								$this->Template->createBlock('loop_line');
								$this->_dataColumns($lineData, $aCell);
								$aCell++;
								if (($aCell > $this->numCols) && ($aRow < (parent::getRecordCount()-1))) {
									$aLine++;
									if ($this->isPrintable && (($aLine % $this->pageBreak) == 0))
										$this->_buildPageBreak();
									$this->Template->createBlock('loop_line');
									$aCell = 1;
								}
							// n�o � a primeira linha
							} else {
								// verifica se � necess�rio completar a linha com c�lulas vazias
								if (($aCell > 1) && ($aCell <= $this->numCols)) {
									for ($i = $aCell; $i <= $this->numCols; $i++)
										$this->_dataColumns(NULL, $i);
									$aLine++;
									if ($this->isPrintable && (($aLine % $this->pageBreak) == 0))
										$this->_buildPageBreak();
									$this->Template->createBlock('loop_line');
									$aCell = 1;
								}
								// gera grupo, c�lula e quebra linha se necess�rio
								$this->_dataGroup($lineData);
								$this->Template->createBlock('loop_line');
								$this->_dataColumns($lineData, $aCell);
								$aCell++;
								if (($aCell > $this->numCols) && ($aRow < (parent::getRecordCount()-1))) {
									$aLine++;
									if ($this->isPrintable && (($aLine % $this->pageBreak) == 0))
										$this->_buildPageBreak();
									$this->Template->createBlock('loop_line');
									$aCell = 1;
								}
							}
						// n�o houve troca de agrupamento. Gera c�lula e quebra linha se necess�rio
						} else {
							$this->_dataColumns($lineData, $aCell);
							$aCell++;
							if (($aCell > $this->numCols) && ($aRow < (parent::getRecordCount()-1))) {
								$aLine++;
								if ($this->isPrintable && (($aLine % $this->pageBreak) == 0))
									$this->_buildPageBreak();
								$this->Template->createBlock('loop_line');
								$aCell = 1;
							}
						}
						$aRow++;
					}
					// completa com c�lulas vazias se necess�rio ao final da p�gina
					if ($aCell <= $this->numCols)
						for ($i = $aCell; $i <= $this->numCols; $i++)
							$this->_dataColumns(NULL, $i);
				}
			}
		} else {
			if ($this->hasHeader) {
				// gera o cabe�alho
				$this->Template->createBlock('loop_line');
				$this->_dataHeader();
				// gera as n linhas com as n colunas
				while ($lineData = parent::fetch()) {
					$this->Template->createBlock('loop_line');
					$this->_dataColumns($lineData, NULL);
					$aRow++;
					if ($this->isPrintable && (($aRow % $this->pageBreak) == 0))
						$this->_buildPageBreak();
				}
			} else {
				// cria a primeira linha
				$this->Template->createBlock('loop_line');
				$aLine++;
				// gera os N registros da p�gina
				while ($lineData = parent::fetch()) {
					// gera uma c�lula e quebra linha se necess�rio
					$this->_dataColumns($lineData, $aCell);
					$aCell++;
					if (($aCell > $this->numCols) && ($aRow < (parent::getRecordCount()-1))) {
						$this->Template->createBlock('loop_line');
						$aCell = 1;
						$aLine++;
						if ($this->isPrintable && (($aLine % $this->pageBreak) == 0))
							$this->_buildPageBreak();
					}
					$aRow++;
				}
				// completa com c�lulas vazias se necess�rio no final da p�gina
				if ($aCell <= $this->numCols)
					for ($i = $aCell; $i <= $this->numCols; $i++) {
						$this->_dataColumns(NULL, $i);
					}
			}
		}
		// executa a constu��o do formul�rio de busca se existem filtros definidos
		$this->Template->assign("_ROOT.title", sprintf("<SPAN CLASS=\"%s\">%s</SPAN>", $this->styleSheet['title'], $this->title));
		if (!$this->isPrintable) {
			$this->_buildSearchForm();			
			// executa as fun��es de gera��o de dados sobre a p�gina e links para outras p�ginas
			$functionMessages = PHP2Go::getLangVal('REPORT_FUNCTION_MESSAGES');
			$this->Template->assign("_ROOT.page_links", $this->_pageLinks($functionMessages));
			$this->Template->assign("_ROOT.row_count", $this->_rowCount($functionMessages));
			$this->Template->assign("_ROOT.rows_per_page", $this->_rowsPerPage($functionMessages));
			$this->Template->assign("_ROOT.this_page", $this->_thisPage($functionMessages));
			$this->Template->assign("_ROOT.row_interval", $this->_rowInterval($functionMessages));
			$this->Template->assign("_ROOT.go_to_page", $this->_goToPage($functionMessages));
		}
	}

	//!-----------------------------------------------------------------
	// @function 	Report::_dataHeader
	// @desc 		Exibe o cabe�alho de dados com os nomes das colunas do relat�rio
	// @access 		private
	// @return		void	
	// @note 		Esta fun��o s� � executada no modo em que os cabe�alhos
	// 				s�o exibidos no topo das p�ginas ou nos in�cios de grupo
	//!-----------------------------------------------------------------
	function _dataHeader() {
		// verifica os tamanhos fornecidos e o n�mero de colunas
		$finalSize = (parent::getFieldCount() - count($this->groupDisplay) - count($this->hidden));
		if (isset($this->colSizes) && sizeOf($this->colSizes) != $finalSize) 
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_REPORT_COL_COUNT_MISMATCH', array(sizeOf($this->colSizes), $finalSize, sizeOf($this->groupDisplay))), E_USER_ERROR, __FILE__, __LINE__);
		$varsOk = FALSE;
		for ($i = 0, $c = 0; $i < parent::getFieldCount(); $i++) {
			$key = parent::getFieldName($i);
			$keyAlias = (array_key_exists($key, $this->columnAliases) ? $this->columnAliases[$key] : $key);
			// verifica se a coluna pertence �s colunas de agrupamento ou �s colunas escondidas
			if (!in_array($key, $this->groupDisplay) && !in_array($key, $this->hidden)) {
				// define o tamanho da coluna
				$colWidth = ($this->colSizesMode == REPORT_COLUMN_SIZES_CUSTOM && isset($this->colSizes) ? $this->colSizes[$c] . '%' : ($this->colSizesMode == REPORT_COLUMN_SIZES_FIXED ? TypeUtils::parseInteger(100 / $finalSize) . '%' : ''));
				$this->Template->createBlock('loop_header_cell');
				// verifica se as vari�veis obrigat�rias foram declaradas
				if (!$varsOk) {
					if (!$this->Template->isVariableDefined('loop_header_cell.col_name'))
						PHP2Go::raiseError(PHP2Go::getLangVal('ERR_REPORT_MISSING_BLOCK_VARIABLE', array('col_name', 'loop_header_cell')), E_USER_ERROR, __FILE__, __LINE__);
					else if ($this->colSizesMode != REPORT_COLUMN_SIZES_FREE && !$this->Template->isVariableDefined('loop_header_cell.col_wid'))
						PHP2Go::raiseError(PHP2Go::getLangVal('ERR_REPORT_MISSING_BLOCK_VARIABLE', array('col_wid', 'loop_header_cell')), E_USER_ERROR, __FILE__, __LINE__);
					else $varsOk = TRUE;
				}
				// substitui valor, tamanho e ordena��o na coluna
				$this->Template->assign('col_wid', $colWidth);
				if (!$this->isPrintable && $this->_orderLinks === TRUE) {
					$this->Template->assign('col_id', $key);
					$this->Template->assign('col_name', HtmlUtils::anchor($this->_generatePageLink(parent::getCurrentPage(), $key), $keyAlias, PHP2Go::getLangVal('REPORT_ORDER_TIP', $keyAlias), $this->styleSheet['header'], array(), '', "head$c"));
					$this->Template->assign('col_order', (urldecode(HttpRequest::get('order')) == $key ? '&nbsp;' . HtmlUtils::image($this->_orderTypeIcon()) : '&nbsp;'));
				} else {
					$this->Template->assign('col_id', $key);
					$this->Template->assign("col_name", "<SPAN CLASS='{$this->styleSheet['header']}'>{$keyAlias}</SPAN>");
				}
				$c++;
			}
		}
	}
	
	//!-----------------------------------------------------------------
	// @function 	Report::_dataColumns
	// @desc 		Exibe um registro da consulta no relat�rio. Formata de acordo com a 
	//				op��o, utilizando colunas ou inserindo os dados em uma c�lula
	// @access 		private
	// @param 		data array		"NULL" Vetor com os dados do registro
	// @param		aCell int		"NULL" N�mero da c�lula, quando se trata de um relat�rio por c�lulas
	// @return		void	
	//!-----------------------------------------------------------------
	function _dataColumns($data=NULL, $aCell=NULL) {
		// verifica se deve aplicar estilo de altern�ncia
		if (!empty($this->altStyle)) {
			$altStyle = current($this->altStyle);
			if (TypeUtils::isNull($aCell) || $aCell == $this->numCols) {
				if (!next($this->altStyle)) 
					reset($this->altStyle);
			}
		}
		// dados nulos, deve gerar uma c�lula vazia
		if (TypeUtils::isNull($data)) {
			$blockName = (isset($this->emptyBlock) ? $this->emptyBlock : 'loop_cell');
			$this->Template->createBlock($blockName);
			$this->Template->assign(parent::getFieldName(0), '&nbsp;');
			if (!empty($this->altStyle) && $this->Template->isVariableDefined($blockName . '.alt_style')) {
				$this->Template->assign('alt_style', $altStyle);
				$this->Template->assign('loop_line.alt_style', $altStyle);
			}
		} else {
			// executa o tratador de linha
			if (isset($this->lineHandler))
				$data = $this->lineHandler->invoke($data);
			// executa a formata��o da linha
			if (!empty($this->hlFormat))
				$data = $this->_highlightSearch($data);
			// relat�rio com cabe�alhos
			if ($this->hasHeader) {
				$varsOk = FALSE;
				// verifica os tamanhos fornecidos e o n�mero de colunas			
				$finalSize = (parent::getFieldCount() - count($this->groupDisplay) - count($this->hidden));
				if (isset($this->colSizes) && sizeOf($this->colSizes) != $finalSize) 
					PHP2Go::raiseError(PHP2Go::getLangVal('ERR_REPORT_COL_COUNT_MISMATCH', array(sizeOf($this->colSizes), $finalSize, sizeOf($this->groupDisplay))), E_USER_ERROR, __FILE__, __LINE__);
				for ($i = 0, $c = 0; $i < parent::getFieldCount(); $i++) {
					$key = parent::getFieldName($i);
					// verifica se a coluna pertence �s colunas de agrupamento e �s escondidas
					if (!in_array($key, $this->groupDisplay) && !in_array($key, $this->hidden)) {
						// define o tamanho da coluna, a partir do modo definido na classe
						$colWidth = ($this->colSizesMode == REPORT_COLUMN_SIZES_CUSTOM && isset($this->colSizes) ? $this->colSizes[$c] . '%' : ($this->colSizesMode == REPORT_COLUMN_SIZES_FIXED ? TypeUtils::parseInteger(100 / $finalSize) . '%' : ''));
						// verifica se as vari�veis obrigat�rias foram declaradas
						$this->Template->createBlock('loop_cell');
						if (!$varsOk) {
							if (!$this->Template->isVariableDefined('loop_cell.col_data'))
								PHP2Go::raiseError(PHP2Go::getLangVal('ERR_REPORT_MISSING_BLOCK_VARIABLE', array("col_data", "loop_cell")), E_USER_ERROR, __FILE__, __LINE__);
							else if ($this->colSizesMode != REPORT_COLUMN_SIZES_FREE && !$this->Template->isVariableDefined('loop_cell.col_wid'))
								PHP2Go::raiseError(PHP2Go::getLangVal('ERR_REPORT_MISSING_BLOCK_VARIABLE', array("col_wid", "loop_cell")), E_USER_ERROR, __FILE__, __LINE__);
							else
								$varsOk = TRUE;
						}
						// verifica se existe tratador para a coluna
						if (isset($this->columnHandler[$key]))
							$data[$key] = $this->columnHandler[$key]->invoke($data[$key]);
						// substitui ID, valor e tamanho na coluna
						$this->Template->assign('col_wid', $colWidth);
						$this->Template->assign('col_data', $data[$key] . '&nbsp;');						
						if (!empty($this->altStyle) && $this->Template->isVariableDefined('loop_cell.alt_style')) {
							$this->Template->assign('alt_style', $altStyle);
							$this->Template->assign('loop_line.alt_style', $altStyle);
						}
						$c++;
					}
				}
			// relat�rio com c�lulas
			} else {
				$colWidth = TypeUtils::parseInteger(100 / $this->numCols) . '%';
				$this->Template->createBlock('loop_cell');
				$this->Template->assign('col_wid', $colWidth);
				if (!empty($this->altStyle) && $this->Template->isVariableDefined('loop_cell.alt_style'))
					$this->Template->assign('alt_style', $altStyle);
				
				// Substitui o valor de todas as colunas na c�lula
				for ($i=0; $i<parent::getFieldCount(); $i++) {
					$key = parent::getFieldName($i);
					// Verifica se existe tratador para a coluna
					if (isset($this->columnHandler[$key]))
						$data[$key] = $this->columnHandler[$key]->invoke($data[$key]);
					if (!isset($this->group) || !in_array($key, $this->groupDisplay)) 
						$this->Template->assign("$key", $data[$key]);
				}
			}
		}
	}
	
	//!-----------------------------------------------------------------
	// @function 	Report::_dataGroup
	// @desc 		Exibe as colunas referentes ao agrupamento de registros
	// @access 		private
	// @param 		data array		Vetor com os dados do registro
	// @return		void	
	//!-----------------------------------------------------------------
	function _dataGroup($data) {
		$display = "";
		$this->Template->createBlock('loop_group');
		$finalSize = parent::getFieldCount() - count($this->groupDisplay);
		foreach($this->groupDisplay as $key)
			$display .= empty($display) ? $data[$key] : ' - ' . $data[$key];
		// Verifica se as vari�veis obrigat�rias 'group_display' e 'group_span' foram definidas
		if (!$this->Template->isVariableDefined('loop_group.group_display'))
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_REPORT_MISSING_BLOCK_VARIABLE', array('group_display', 'loop_group')));
		if (!$this->Template->isVariableDefined('loop_group.group_span'))
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_REPORT_MISSING_BLOCK_VARIABLE', array('group_span', 'loop_group')));
		$this->Template->assign('group_display', $display);
		$this->Template->assign('group_span', ($this->hasHeader) ? $finalSize : $this->numCols);
	}
	
	//!-----------------------------------------------------------------
	// @function 	Report::_checkGroup
	// @desc 		Verifica se todas as colunas de agrupamento s�o v�lidas
	// @access 		private
	// @param 		&errorMsg string	Vari�vel para onde retornam os erros, se ocorrerem
	// @return		bool
	//!-----------------------------------------------------------------
	function _checkGroup(&$errorMsg) {
		$gCheck = TRUE;
		$fieldNames = parent::getFieldNames();
		if (sizeOf($this->group) >= parent::getFieldCount()) {
			$errorMsg = PHP2Go::getLangVal('ERR_REPORT_MAX_GROUP_COLS');
			$gCheck = FALSE;
		} else {		
			for ($i = 0; $i < sizeOf($this->group); $i++) {
				if (!in_array($this->group[$i], $fieldNames)) {
					$errorMsg = PHP2Go::getLangVal('ERR_REPORT_UNKNOWN_GROUP_COL', $this->group[$i]);
					$gCheck = FALSE;
				}
			}
		}
		if (sizeOf($this->groupDisplay) >= parent::getFieldCount()) {
			$errorMsg = PHP2Go::getLangVal('ERR_REPORT_MAX_GROUP_COLS');
			$gCheck = FALSE;
		} else {
			for ($i = 0; $i < sizeOf($this->groupDisplay); $i++) {
				if (!in_array($this->groupDisplay[$i], $fieldNames)) {
					$errorMsg = PHP2Go::getLangVal('ERR_REPORT_UNKNOWN_GROUP_COL', $this->groupDisplay[$i]);
					$gCheck = FALSE;
				}
			}
		}
		return $gCheck;
	}
	
	//!-----------------------------------------------------------------
	// @function	Report::_checkHidden
	// @desc		Realiza valida��o nas colunas definidas como escondidas, verificando
	//				se o m�ximo foi excedido e se todas as colunas existem no result set
	// @access		private
	// @param		&errorMsg string	Vari�vel por onde retornam os erros, se ocorrerem
	// @return		bool
	//!-----------------------------------------------------------------
	function _checkHidden(&$errorMsg) {
		$hCheck = TRUE;
		$fieldNames = parent::getFieldNames();
		if (sizeOf($this->hidden) >= parent::getFieldCount()) {
			$errorMsg = PHP2Go::getLangVal('ERR_REPORT_MAX_HIDDEN_COLS');;
			$hCheck = FALSE;
		} else {
			for ($i = 0; $i < sizeOf($this->hidden); $i++) {
				if (!in_array($this->hidden[$i], $fieldNames)) {
					$errorMsg = PHP2Go::getLangVal('ERR_REPORT_UNKNOWN_HIDDEN_COL', $this->hidden[$i]);
					$hCheck = FALSE;
				}
			}
		}
		return $hCheck;
	}
	
	//!-----------------------------------------------------------------
	// @function 	Report::_matchGroup
	// @desc 		Verifica se houve troca de agrupamento em uma linha de resultados
	// @access 		private
	// @param 		data array		Vetor com dados de uma linha de resultados
	// @return		bool
	//!-----------------------------------------------------------------
	function _matchGroup($data) {
		if (!isset($this->currentGroup)) {
			foreach ($this->group as $value) 
				$this->currentGroup[$value] = $data[$value];
			return TRUE;
		} else {
			$sizeOf = sizeOf($this->group);
			for ($i = 0; $i < $sizeOf; $i++) {
				if ($this->currentGroup[$this->group[$i]] != $data[$this->group[$i]]) {
					foreach ($this->group as $value) 
						$this->currentGroup[$value] = $data[$value];
					return TRUE;
				}
			}
			return FALSE;
		}
	}
	
	//!-----------------------------------------------------------------
	// @function 	Report::_generatePageLink
	// @desc 		Gera um link gen�rico para pagina��o ou reordena��o
	// @access 		private
	// @param 		page int		P�gina alvo
	// @param 		order int		"" �ndice para ordena��o
	// @return		string Link para uma p�gina constru�do
	//!-----------------------------------------------------------------
	function _generatePageLink($page, $order = '') {
		if (isset($this->_order) && $order == $this->_order)
			$ot = ($this->_orderType == 'a' ? 'd' : 'a');
		else 
			$ot = $this->_orderType;
		return sprintf("%s?page=%s%s%s%s%s", 
					HttpRequest::basePath(), $page, 
					($order != '' ? '&order=' . urlencode($order) : (isset($this->_order) ? '&order=' . $this->_order : '')), 
					'&ordertype=' . $ot, 
					($this->_SimpleSearch->searchSent ? $this->_SimpleSearch->getUrlString() : ''), 
					(isset($this->extraVars) ? '&' . $this->extraVars : '')
		);
	}	
	
	//!-----------------------------------------------------------------
	// @function 	Report::_highlightSearch
	// @desc 		Aplica destaque nos valores de busca em uma linha de resultados,
	// 				de acordo com os padr�es setados atrav�s da fun��o enableHighlight
	// @access 		private
	// @param 		data array		Vetor com dados de uma linha de resultados
	// @return 		array Vetor com as colunas modificadas destacando os valores de pesquisa encontrados
	//!-----------------------------------------------------------------
	function _highlightSearch($data) {
		$newData = $data;
		if ($this->_SimpleSearch->searchSent) {
			$fields = explode('|', $this->_SimpleSearch->getFields());
			$operators = explode('|', $this->_SimpleSearch->getOperators());
			$values = explode('|', $this->_SimpleSearch->getValues());
			$size = sizeOf($fields);
			for($i = 0; $i < $size; $i++) {
				$filters = $this->_SimpleSearch->iterator();
				while ($filter = $filters->next()) {
					if ($filter['field'] == $fields[$i]) {
						$patt = ($operators[$i] == 'LIKEI' ? '^' . $values[$i] : ($operators[$i] == 'LIKEF' ? $values[$i] . '$' : $values[$i]));
						$repl = "<span style=\"{$this->hlFormat}\">\\1</span>";
						foreach ($data as $key => $value) {
							if ($key == $filter['field'])
								$newData[$key] = preg_replace("'(?!<.*?)($patt)(?![^<>]*?>)'si", $repl, $newData[$key]);
							elseif (StringUtils::normalize(trim(strtolower($key))) == StringUtils::normalize(trim(strtolower($filter['label']))))
								$newData[$key] = preg_replace("'(?!<.*?)($patt)(?![^<>]*?>)'si", $repl, $newData[$key]);
						}
					}
				}
			}
		}
		return $newData;
	}
	
	//!-----------------------------------------------------------------
	// @function 	Report::_buildSearchForm
	// @desc 		Constr�i o formul�rio de busca simples
	// @access 		private
	// @return 		void
	//!-----------------------------------------------------------------
	function _buildSearchForm() {
		// n�o existem filtros definidos
		if ($this->_SimpleSearch->isEmpty())
			return FALSE;
		// busca simples n�o foi definida no template
		if (!$this->Template->isVariableDefined("simple_search"))
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_REPORT_MISSING_SEARCH_VARIABLE', array('simple_search', 'simple_search')));
		// constru��o do formul�rio de busca
		$this->_Document->addScript(PHP2GO_JAVASCRIPT_PATH . "libs/cookie.js");
		$this->_Document->addScript(PHP2GO_JAVASCRIPT_PATH . "libs/form.js");
		$this->_Document->addScript(PHP2GO_JAVASCRIPT_PATH . "libs/string.js");
		$this->_Document->addScript(PHP2GO_JAVASCRIPT_PATH . "modules/simplesearch.js");
		$this->_Document->attachBodyEvent("onLoad", "searchGetCookie();");
		// carrega o template de busca customizado ou o padr�o da classe
		if (isset($this->searchTemplate)) {
			$SearchTpl = new Template($this->searchTemplate['file']);
			$SearchTpl->parse();
			if (!empty($this->searchTemplate['vars']))
				$SearchTpl->assign($this->searchTemplate['vars']);
		} else {
			$SearchTpl = new Template(PHP2GO_TEMPLATE_PATH . "simplesearch.tpl");
			$SearchTpl->parse();
		}		
		// vari�veis simples do template de busca
		$SearchTpl->assign("url", HttpRequest::basePath() . (isset($this->extraVars) && $this->extraVars != "" ? '?' . $this->extraVars : ''));
		if (!empty($this->styleSheet['link']))
			$SearchTpl->assign("lstyle", " CLASS=\"{$this->styleSheet['link']}\"");
		$Agent =& UserAgent::getInstance();
		if ($Agent->matchBrowserList(array('ie5+', 'ns6+', 'opera5+'))) {
			if (!empty($this->styleSheet['filter']))
				$SearchTpl->assign("fstyle", " CLASS=\"{$this->styleSheet['filter']}\"");
			if (!empty($this->styleSheet['button']))
				$SearchTpl->assign("bstyle", " CLASS=\"{$this->styleSheet['button']}\"");
		}
		$SearchTpl->assign(PHP2Go::getLangVal('REPORT_SEARCH_VALUES'));		
		// op��es de operadores
		$opOptions = "";
		$opValues = PHP2Go::getLangVal('REPORT_SEARCH_INI_OP');
		foreach($opValues as $key => $value) 
			$opOptions .= "<OPTION VALUE='" . $key . "'>" . $value . "</OPTION>\n";
		$SearchTpl->assign("opOptions", $opOptions);		
		// op��es de campos de filtragem e m�scaras
		$masks = array();
		$fieldOptions = "";		
		$filters = $this->_SimpleSearch->iterator();		
		while ($filter = $filters->next()) {
			$fieldOptions .= "<OPTION VALUE=\"" . $filter['field'] . "\">" . $filter['label'] . "</OPTION>\n";
			if (substr($filter['mask'], 0, 3) == 'ZIP')
				$this->_Document->addScript(PHP2GO_JAVASCRIPT_PATH . 'masks/zip.js');
			elseif (substr($filter['mask'], 0, 5) == 'FLOAT')
				$this->_Document->addScript(PHP2GO_JAVASCRIPT_PATH . 'masks/float.js');
			elseif ($filter['mask'] != 'STRING')
				$this->_Document->addScript(PHP2GO_JAVASCRIPT_PATH . 'masks/' . strtolower($filter['mask']) . '.js');
			$masks[] = $filter['mask'];
		}
		$SearchTpl->assign("fieldOptions", $fieldOptions);
		$SearchTpl->assign("searchMasks", implode(',', $masks));
		$this->Template->assign(
			"_ROOT.simple_search", 
			"<SCRIPT TYPE=\"text/javascript\">var filterDateFormat = '" . PHP2Go::getConfigVal('LOCAL_DATE_TYPE') . "';</SCRIPT>\n" .
			$SearchTpl->getContent()
		);
	}

	//!-----------------------------------------------------------------
	// @function 	Report::_buildPageBreak
	// @desc 		Insere uma quebra de p�gina na vers�o de impress�o do relat�rio
	// @access 		private
	// @return 		void	
	//!-----------------------------------------------------------------
	function _buildPageBreak() {
		if ($this->Template->isVariableDefined("loop_line.page_break")) {
			$this->Template->assign("loop_line.page_break", "<TR STYLE=\"page-break-after: always\"></TR>");
			if ($this->hasHeader) {
				$this->Template->createBlock('loop_line');
				$this->_dataHeader();
			}		
		}
	}
	
	//!-----------------------------------------------------------------
	// @function 	Report::_pageLinks
	// @desc 		Constr�i os links para outras p�ginas do relat�rio
	// @access 		private
	// @param		fMessages array	Vetor de mensagens buscado no arquivo de linguagem ativo
	// @return		string Links para outras p�ginas a partir da p�gina atual
	//!-----------------------------------------------------------------
	function _pageLinks($fMessages) {
		$retorno = '';		
		$limiter = sprintf("<SPAN CLASS=\"%s\"> | </SPAN>", $this->styleSheet['link']);
		$style = $this->pagingStyle[0];
		$params = $this->pagingStyle[1];
		if ($style == REPORT_PAGING_DEFAULT) {
			for ($i = $this->_firstPage; $i <= $this->_lastPage; $i++) {
				if ($i == parent::getCurrentPage()) 
					$retorno .= sprintf("<SPAN CLASS=\"%s\">%d</SPAN>\n", $this->styleSheet['link'], $i); 
				else 
					$retorno .= HtmlUtils::anchor($this->_generatePageLink($i), "<U>$i</U>", sprintf($fMessages['pageTip'], $i, parent::getPageCount()), $this->styleSheet['link']);
				if ($i < $this->_lastPage) $retorno .= $limiter; else $retorno .= '<BR>';
			}
			if (isset($this->_navigation['firstPage'])) {
				$retorno .= HtmlUtils::anchor($this->_generatePageLink($this->_navigation['firstPage']), $fMessages['firstTit'], $fMessages['firstTip'], $this->styleSheet['link']);
			}
			if (isset($this->_navigation['previousScreen'])) {
				if (isset($this->_navigation['firstPage'])) $retorno .= $limiter;
				$retorno .= HtmlUtils::anchor($this->_generatePageLink($this->_navigation['previousScreen']), sprintf($fMessages['prevScrTit'], $this->_visiblePages), sprintf($fMessages['prevScrTip'], $this->_visiblePages), $this->styleSheet['link']);
			}
			if (isset($this->_navigation['nextScreen'])) {
				if (isset($this->_navigation['firstPage']) || isset($this->_navigation['previousScreen'])) $retorno .= $limiter;
				$retorno .= HtmlUtils::anchor($this->_generatePageLink($this->_navigation['nextScreen']), sprintf($fMessages['nextScrTit'], $this->_visiblePages), sprintf($fMessages['nextScrTip'], $this->_visiblePages), $this->styleSheet['link']);
			}
			if (isset($this->_navigation['lastPage'])) {
				if (isset($this->_navigation['firstPage']) || isset($this->_navigation['previousScreen']) || isset($this->_navigation['nextScreen'])) $retorno .= $limiter;
				$retorno .= HtmlUtils::anchor($this->_generatePageLink($this->_navigation['lastPage']), $fMessages['lastTit'], $fMessages['lastTip'], $this->styleSheet['link']);
			}
		} elseif ($style == REPORT_PREVNEXT) {
			$symbols = TypeUtils::toBoolean($params['useSymbols']);
			$buttons = TypeUtils::toBoolean($params['useButtons']);
			if (isset($this->_navigation['previousPage'])) {
				if ($buttons)
					$retorno .= sprintf("<BUTTON ID=\"report_prev_page\" NAME=\"prev\" TYPE=\"button\" CLASS=\"%s\" onClick=\"location.href='%s'\">%s</BUTTON>", $this->styleSheet['button'], $this->_generatePageLink($this->_navigation['previousPage']), ($symbols ? '<' : $fMessages['prevTit']));
				else
					$retorno .= HtmlUtils::anchor($this->_generatePageLink($this->_navigation['previousPage']), ($symbols ? '<' : $fMessages['prevTit']), $fMessages['prevTip'], $this->styleSheet['link']);
			}
			if (isset($this->_navigation['nextPage'])) {
				if (isset($this->_navigation['previousPage'])) $retorno .= ($symbols || $buttons ? HtmlUtils::noBreakSpace(2) : $limiter);			
				if ($buttons)
					$retorno .= sprintf("<BUTTON ID=\"report_next_page\" NAME=\"next\" TYPE=\"button\" CLASS=\"%s\" onClick=\"location.href='%s'\">%s</BUTTON>", $this->styleSheet['button'], $this->_generatePageLink($this->_navigation['nextPage']), ($symbols ? '>' : $fMessages['nextTit']));
				else
					$retorno .= HtmlUtils::anchor($this->_generatePageLink($this->_navigation['nextPage']), ($symbols ? '>' : $fMessages['nextTit']), $fMessages['nextTip'], $this->styleSheet['link']);
			}
		} elseif ($style == REPORT_FIRSTPREVNEXTLAST) {
			$symbols = TypeUtils::toBoolean($params['useSymbols']);
			$buttons = TypeUtils::toBoolean($params['useButtons']);
			if (isset($this->_navigation['firstPage'])) {
				if ($buttons)
					$retorno .= sprintf("<BUTTON ID=\"report_first_page\" NAME=\"first\" TYPE=\"button\" CLASS=\"%s\" onClick=\"location.href='%s'\">%s</BUTTON>", $this->styleSheet['button'], $this->_generatePageLink($this->_navigation['firstPage']), ($symbols ? '<<' : $fMessages['firstTit']));
				else
					$retorno .= HtmlUtils::anchor($this->_generatePageLink($this->_navigation['firstPage']), ($symbols ? '<<' : $fMessages['firstTit']), $fMessages['firstTip'], $this->styleSheet['link']);
			}
			if (isset($this->_navigation['previousPage'])) {
				if (isset($this->_navigation['firstPage'])) $retorno .= ($symbols || $buttons ? HtmlUtils::noBreakSpace(2) : $limiter);							
				if ($buttons)
					$retorno .= sprintf("<BUTTON ID=\"report_prev_page\" NAME=\"prev\" TYPE=\"button\" CLASS=\"%s\" onClick=\"location.href='%s'\">%s</BUTTON>", $this->styleSheet['button'], $this->_generatePageLink($this->_navigation['previousPage']), ($symbols ? '<' : $fMessages['prevTit']));
				else
					$retorno .= HtmlUtils::anchor($this->_generatePageLink($this->_navigation['previousPage']), ($symbols ? '<' : $fMessages['prevTit']), $fMessages['prevTip'], $this->styleSheet['link']);
			}
			if (isset($this->_navigation['nextPage'])) {
				if (isset($this->_navigation['firstPage']) || isset($this->_navigation['previousPage'])) $retorno .= ($symbols || $buttons ? HtmlUtils::noBreakSpace(2) : $limiter);			
				if ($buttons)
					$retorno .= sprintf("<BUTTON ID=\"report_next_page\" NAME=\"next\" TYPE=\"button\" CLASS=\"%s\" onClick=\"location.href='%s'\">%s</BUTTON>", $this->styleSheet['button'], $this->_generatePageLink($this->_navigation['nextPage']), ($symbols ? '>' : $fMessages['nextTit']));
				else
					$retorno .= HtmlUtils::anchor($this->_generatePageLink($this->_navigation['nextPage']), ($symbols ? '>' : $fMessages['nextTit']), $fMessages['nextTip'], $this->styleSheet['link']);
			}
			if (isset($this->_navigation['lastPage'])) {
				if (isset($this->_navigation['firstPage']) || isset($this->_navigation['previousPage']) || isset($this->_navigation['nextPage'])) $retorno .= ($symbols || $buttons ? HtmlUtils::noBreakSpace(2) : $limiter);			
				if ($buttons)
					$retorno .= sprintf("<BUTTON ID=\"report_last_page\" NAME=\"last\" TYPE=\"button\" CLASS=\"%s\" onClick=\"location.href='%s'\">%s</BUTTON>", $this->styleSheet['button'], $this->_generatePageLink($this->_navigation['lastPage']), ($symbols ? '>>' : $fMessages['lastTit']));
				else
					$retorno .= HtmlUtils::anchor($this->_generatePageLink($this->_navigation['lastPage']), ($symbols ? '>>' : $fMessages['lastTit']), $fMessages['lastTip'], $this->styleSheet['link']);
			}			
		}
		return $retorno;
	}
	
	//!-----------------------------------------------------------------
	// @function 	Report::_rowCount
	// @desc 		Constr�i a mensagem de n�mero total de registros
	// @access 		private
	// @param		fMessages array	Vetor de mensagens buscado no arquivo de linguagem ativo
	// @return		string Mensagem de n�mero total de registros
	//!-----------------------------------------------------------------
	function _rowCount($fMessages) {
		if (parent::getTotalRecordCount() > 0)
			return sprintf($fMessages['rowCount'], parent::getTotalRecordCount());
	}
	
	//!-----------------------------------------------------------------
	// @function 	Report::_rowsPerPage
	// @desc 		Constr�i a mensagem de n�mero de registros por p�gina
	// @access 		private
	// @param		fMessages array	Vetor de mensagens buscado no arquivo de linguagem ativo
	// @return		string Mensagem de n�mero de linhas por p�gina
	//!-----------------------------------------------------------------
	function _rowsPerPage($fMessages) {
		if (parent::getTotalRecordCount() > 0)
			return sprintf($fMessages['rowsPerPage'], parent::getPageSize());
	}
	
	//!-----------------------------------------------------------------
	// @function 	Report::_thisPage
	// @desc 		Constr�i a mensagem que indica a p�gina atual
	// @access 		private
	// @param		fMessages array	Vetor de mensagens buscado no arquivo de linguagem ativo
	// @return		string Mensagem contendo a p�gina corrente
	//!-----------------------------------------------------------------
	function _thisPage($fMessages) {
		if (parent::getTotalRecordCount() > 0)
			return sprintf($fMessages['thisPage'], parent::getCurrentPage(), parent::getPageCount());
	}
	
	//!-----------------------------------------------------------------
	// @function	Report::_rowInterval
	// @desc		Constr�i a mensagem do intervalo de registros que est� sendo exibido
	// @access		private
	// @param		fMessages array	Vetor de mensagens buscado no arquivo de linguagem ativo
	// @return		string Mensagem do intervalo atual de linhas
	//!-----------------------------------------------------------------
	function _rowInterval($fMessages) {
		if (isset($this->_offset)) {
			$upperBound = (($this->_offset + parent::getPageSize()) > parent::getTotalRecordCount()) ? parent::getTotalRecordCount() : ($this->_offset + parent::getPageSize());
			return sprintf($fMessages['rowInterval'], ($this->_offset + 1), $upperBound, parent::getTotalRecordCount());
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	Report::_goToPage
	// @desc		Gera o formul�rio e o campo que permite o salto para uma determinada p�gina
	//				do relat�rio atual
	// @access		private
	// @param		fMessages array Vetor de mensagens buscado no arquivo de linguagem ativo
	// @return		string C�digo do formul�rio/campo de salto para p�gina
	//!-----------------------------------------------------------------
	function _goToPage($fMessages) {
		$this->_Document->addScript(PHP2GO_JAVASCRIPT_PATH . 'masks/integer.js');
		$this->_Document->addScript(PHP2GO_JAVASCRIPT_PATH . 'libs/report.js');
		$goToUrl = ereg_replace("(\?|&)(page=[0-9]+)(&?)", "\\1", $this->_generatePageLink(parent::getCurrentPage()));
		$goToLabel = sprintf("<LABEL FOR=\"reportGoTo_page\" ID=\"reportGoTo_page_label\" CLASS=\"%s\">%s</LABEL>", $this->styleSheet['filter'], $fMessages['goTo']);
		$goToField = sprintf("<INPUT TYPE=\"text\" ID=\"reportGoTo_page\" NAME=\"page\" SIZE=\"5\" MAXLENGTH=\"10\" CLASS=\"%s\" onKeyPress=\"return chkMaskINTEGER(this, event)\"/>", $this->styleSheet['filter']);
		return sprintf("<FORM ID=\"reportGoTo\" NAME=\"reportGoTo\" METHOD=\"POST\" ACTION=\"%s\" STYLE=\"display:inline\" onSubmit=\"return goToPage(getDocumentObject('reportGoTo_page'), %d);\">\n%s\n&nbsp;%s\n</FORM>\n", $goToUrl, parent::getPageCount(), $goToLabel, $goToField);
	}
	
	//!-----------------------------------------------------------------
	// @function	Report::_orderByClause
	// @desc		M�todo privado de constru��o da cl�usula de ordena��o,
	//				baseado nas configura��es de grupo, na ordena��o definida pelo
	//				usu�rio (cabe�alhos) e na ordena��o padr�o do DATASOURCE
	// @access		private
	// @return		string Cl�usula de ordena��o
	//!-----------------------------------------------------------------
	function _orderByClause() {
		$orderMembers = array();
		if (isset($this->group))
			foreach ($this->group as $field)
				array_push($orderMembers, "\"{$field}\"");
		if (isset($this->_order))
		{
//			array_push($orderMembers, "\"{$this->_order}\"" . ($this->_orderType == 'd' ? " DESC" : " ASC"));
			array_push($orderMembers, "`{$this->_order}`" . ($this->_orderType == 'd' ? " DESC" : " ASC"));  //update by helen for mysql4 to mysql5; March 28th, 2012
		}
		if (!empty($this->_dataSource['ORDERBY']))
			array_push($orderMembers, trim($this->_dataSource['ORDERBY']));
		return (!empty($orderMembers) ? implode(',', $orderMembers) : NULL);		
	}

	//!-----------------------------------------------------------------
	// @function	Report::_orderTypeIcon
	// @desc		Retorna o nome da imagem de acordo com a orienta��o da ordena��o
	// @access		private
	// @return		string Nome do �cone de ordena��o
	//!-----------------------------------------------------------------
	function _orderTypeIcon() {
		switch ($this->_orderType) {
			case 'a' : return $this->icons['orderasc'];
			case 'd' : return $this->icons['orderdesc'];
			default : return $this->icons['orderasc'];
		}
	}	
}
?>