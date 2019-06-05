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
// $Header: /www/cvsroot/php2go/core/data/PagedDataSet.class.php,v 1.7 2005/09/01 15:18:13 mpont Exp $
// $Date: 2005/09/01 15:18:13 $

//-----------------------------------------
import('php2go.data.DataSet');
import('php2go.net.HttpRequest');
//-----------------------------------------

// @const PDS_DEFAULT_PAGE_SIZE	"30"
// Define o tamanho padrão de uma página de resultados
define('PDS_DEFAULT_PAGE_SIZE', 30);

//!-----------------------------------------------------------------
// @class		PagedDataSet
// @desc		A classe PagedDataSet implementa um mecanismo de paginação
//				sobre os conjuntos de dados criados com a classe DataSet.
//				Os adaptadores de dados montam um subconjunto de registros
//				baseado no número da página atual, habilitando a navegação
//				sobre os mesmos e armazenando na classe o total de registros
//				do conjunto (todas as páginas de resultados somadas)
// @package		php2go.data
// @extends		DataSet
// @uses		HttpRequest
// @uses		TypeUtils
// @author		Marcos Pont
// @version		$Revision: 1.7 $
// @note		Exemplo de uso:<PRE>
//
//				# exemplo de dataset paginado utilizando XML
//				$dataset =& PagedDataSet::getInstance('xml');
//				$dataset->setPageSize(5);
//				$dataset->load('dataset.xml', DS_XML_CDATA);
//
//				# monta os links de paginação
//				if ($previous = $dataset->getPreviousPage()) {
//				&nbsp;&nbsp;&nbsp;&nbsp;print HtmlUtils::anchor(HttpRequest::basePath() . '?page=' . $previous, 'Previous');
//				}
//				if ($next = $dataset->getNextPage()) {
//				&nbsp;&nbsp;&nbsp;&nbsp;print HtmlUtils::anchor(HttpRequest::basePath() . '?page=' . $next, 'Next');
//				}
//
//				# navega nos registros
//				while (!$dataset->eof()) {
//				&nbsp;&nbsp;&nbsp;&nbsp;print $dataset->getField('fieldname');
//				}
//				
//
//				</PRE>
//!-----------------------------------------------------------------
class PagedDataSet extends DataSet
{
	var $_currentPage;		// @var _currentPage int	Número da página atual
	var $_pageCount = 0;	// @var _pageCount int		"0" Total de páginas do conjunto de dados
	var $_offset;			// @var _offset int			Deslocamento atual no conjunto (início da página atual)
	var $_pageSize;			// @var _pageSize int		Tamanho das páginas de resultados
	
	//!-----------------------------------------------------------------
	// @function	PagedDataSet::PagedDataSet
	// @desc		Construtor da classe
	// @access		public
	// @param		type string		Tipo do adaptador de dados a ser utilizado	
	//!-----------------------------------------------------------------
	function PagedDataSet($type) {
		parent::DataSet($type);
		$this->_pageSize = PDS_DEFAULT_PAGE_SIZE;
		$this->_currentPage = TypeUtils::ifNull(HttpRequest::get('page'), 1);
		if (!TypeUtils::isInteger($this->_currentPage) || $this->_currentPage < 1)
			$this->_currentPage = 1;
		$this->_offset = (($this->_currentPage - 1) * $this->_pageSize);
	}
	
	//!-----------------------------------------------------------------
	// @function	PagedDataSet::&getInstance
	// @desc		Retorna uma instância única da classe PagedDataSet,
	//				para um determinado tipo de adaptador de dados
	// @access		public
	// @param		type string		Tipo do adaptador de dados
	// @return		PagedDataSet object
	// @static
	//!-----------------------------------------------------------------
	function &getInstance($type) {
		static $instances;
		$type = (trim($type) != '') ? strtolower(trim($type)) : 'custom';
		if (!isset($instances[$type]))
			$instances[$type] =& new PagedDataSet($type);
		return $instances[$type];		
	}
	
	//!-----------------------------------------------------------------
	// @function	PagedDataSet::getPageSize
	// @desc		Retorna o tamanho de página atual
	// @access		public
	// @return		int
	//!-----------------------------------------------------------------
	function getPageSize() {
		return $this->_pageSize;
	}
	
	//!-----------------------------------------------------------------
	// @function	PagedDataSet::getCurrentPage
	// @desc		Retorna o número da página atual
	// @access		public
	// @return		int
	//!-----------------------------------------------------------------
	function getCurrentPage() {
		return $this->_currentPage;
	}
	
	//!-----------------------------------------------------------------
	// @function	PagedDataSet::getPreviousPage
	// @desc		Retorna o número da página anterior do relatório, se existente
	// @access		public
	// @return		int Número da página anterior ou FALSE se a atual é a primeira
	//!-----------------------------------------------------------------
	function getPreviousPage() {
		return ($this->atFirstPage() ? FALSE : $this->_currentPage - 1);
	}
	
	//!-----------------------------------------------------------------
	// @function	PagedDataSet::getNextPage
	// @desc		Retorna o número da próxima página do relatório, se existente
	// @access		public
	// @return		int Número da próxima página ou FALSE se a atual é a última
	//!-----------------------------------------------------------------
	function getNextPage() {
		return ($this->atLastPage() ? FALSE : $this->_currentPage + 1);
	}
	
	//!-----------------------------------------------------------------
	// @function	PagedDataSet::getPageCount
	// @desc		Retorna o total de páginas do conjunto de resultados
	// @access		public
	// @return		int Total de páginas
	//!-----------------------------------------------------------------
	function getPageCount() {
		return $this->_pageCount;
	}
	
	//!-----------------------------------------------------------------
	// @function	PagedDataSet::getTotalRecordCount
	// @desc		Retorna o total de registros do conjunto de dados, somando
	//				todas as páginas existentes
	// @access		public
	// @return		int
	//!-----------------------------------------------------------------
	function getTotalRecordCount() {
		return $this->adapter->totalRecordCount;
	}
	
	//!-----------------------------------------------------------------
	// @function	PagedDataSet::atFirstPage
	// @desc		Verifica se a página atual é a primeira
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function atFirstPage() {
		return $this->_currentPage == 1;
	}
	
	//!-----------------------------------------------------------------
	// @function	PagedDataSet::atLastPage
	// @desc		Verifica se a página atual é a última
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function atLastPage() {
		return $this->_currentPage == $this->_pageCount;
	}
	
	//!-----------------------------------------------------------------
	// @function	PagedDataSet::setCurrentPage
	// @desc		Define a página do dataset que deverá ser carregada
	// @access		public
	// @param		page int	Número da página
	// @return		
	//!-----------------------------------------------------------------
	function setCurrentPage($page) {
		if (TypeUtils::isInteger($page) && $page > 0) {
			$this->_currentPage = $page;
			$this->_offset = (($this->_currentPage - 1) * $this->_pageSize);
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	PagedDataSet::setPageSize
	// @desc		Define o tamanho de página a ser utilizado
	// @access		public
	// @param		pageSize int	Novo tamanho de página
	// @return		void
	//!-----------------------------------------------------------------	
	function setPageSize($pageSize) {
		$this->_pageSize = max(1, $pageSize);
		$this->_offset = (($this->_currentPage - 1) * $this->_pageSize);
	}
	
	//!-----------------------------------------------------------------	
	// @function	PagedDataSet::load
	// @desc		Este método recebe uma quantidade variável de parâmetros
	//				dependendo do adaptador de dados utilizado. A partir dos parâmetros
	//				recebidos, o método load() interno ao adaptador é executado
	// @access		public
	// @return		bool
	// @see			DataSetDb::loadSubSet
	// @see			DataSetCsv::loadSubSet
	// @see			DataSetXml::loadSubSet
	//!-----------------------------------------------------------------	
	function load() {
		$args = func_get_args();
		$args = array_merge(array($this->_offset, $this->_pageSize), $args);
		if (call_user_func_array(array(&$this->adapter, 'loadSubSet'), $args)) {
			$this->_calculatePages();
			return TRUE;
		}
		return FALSE;
	}
	
	//!-----------------------------------------------------------------
	// @function	PagedDataSet::loadSubSet
	// @desc		Sobrescreve o método loadSubSet da classe pai, anulando
	//				a sua funcionalidade
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function loadSubSet() {
		return FALSE;
	}
	
	//!-----------------------------------------------------------------
	// @function	PagedDataSet::_calculatePages
	// @desc		Calcula o número total de páginas no conjunto de dados,
	//				baseado no número total de registros e no tamanho da página
	// @access		private
	// @return		void
	//!-----------------------------------------------------------------
	function _calculatePages() {
		if (($this->adapter->totalRecordCount % $this->_pageSize) == 0)
			$this->_pageCount = ($this->adapter->totalRecordCount / $this->_pageSize);
		else
			$this->_pageCount = TypeUtils::parseInteger(($this->adapter->totalRecordCount / $this->_pageSize) + 1);
	}	
}
?>