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
// $Header: /www/cvsroot/php2go/core/gui/Menu.class.php,v 1.14 2005/06/08 22:56:02 mpont Exp $
// $Date: 2005/06/08 22:56:02 $

//------------------------------------------------------------------
import('php2go.util.Statement');
import('php2go.xml.XmlDocument');
//------------------------------------------------------------------

//!-----------------------------------------------------------------
// @class 		Menu
// @desc 		A classe Menu constr�i estruturas de �rvore para a
//				montagem de menus a partir de consultas SQL a banco
//				de dados ou a partir de uma especifica��o XML
// @package		php2go.gui
// @extends 	PHP2Go
// @uses 		Db
// @uses		ADORecordSet
// @uses 		Statement
// @uses		XmlDocument
// @author 		Marcos Pont
// @version		$Revision: 1.14 $
//!-----------------------------------------------------------------
class Menu extends PHP2Go
{
	var $name; 				// @var name string						Nome do menu, gerado automaticamente
	var $tree; 				// @var tree array						Vetor de dados da �rvore de p�ginas/links do menu
	var $rootSql; 			// @var rootSql string					Consulta SQL que monta o n�vel zero do menu, ou raiz
	var $rootSize; 			// @var rootSize int					N�mero de op��es da raiz do menu
	var $childSql; 			// @var childSql string					Consulta SQL no formato statement - parametrizada - para buscar os demais n�veis do menu
	var $limit; 			// @var limit int						Limite de n�veis que pode ser estabelecido pelo usu�rio
	var $lastLevel = 0; 	// @var lastLevel int					"0" N�vel mais alto gerado para o menu	
	var $_Db = NULL; 		// @var _Db Db object					"NULL" Conex�o com o banco de dados
	var $_Document; 		// @var _Document Document object		Inst�ncia da classe Document onde o menu � inclu�do

	//!-----------------------------------------------------------------
	// @function 	Menu::Menu
	// @desc 		Construtor da classe
	// @access 		public
	// @param 		&Document Document object	Objeto Document onde o menu ser� inserido
	//!-----------------------------------------------------------------
	function Menu(&$Document) {
		PHP2Go::PHP2Go();
		if ($this->isA('menu', FALSE))
        	PHP2Go::raiseError(PHP2Go::getLangVal('ERR_ABSTRACT_CLASS', 'Menu'), E_USER_ERROR, __FILE__, __LINE__);
		if (!TypeUtils::isObject($Document) || !$Document->isA('document'))
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_INVALID_OBJECT', 'Document'), E_USER_ERROR, __FILE__, __LINE__);
		$this->name = PHP2Go::generateUniqueId('p2g_menu_');
		$this->_Document =& $Document;
	}

	//!-----------------------------------------------------------------
	// @function 	Menu::loadFromDatabase
	// @desc        Constr�i a �rvore do menu atrav�s de duas consultas SQL:
	//				uma para a raiz ou n�vel zero e outra para os n�veis subseq�entes
	// @access 		public
	// @param 		rootSql string		Consulta SQL que gera o primeiro n�vel no menu
	// @param 		childSql string		Consulta SQL para gera��o dos demais n�veis
	// @param		limit int			"0"	Limite de n�veis
	// @param		connectionId string	"NULL" ID da conex�o a banco de dados a ser utilizada
	// @return		void
	// @note 		A consulta da raiz deve retornar ao menos dois campos, onde o
	// 				primeiro ser� interpretado como �ndice do menu e o segundo
	// 				como o seu r�tulo. O terceiro, se existir, ser� usado como
	// 				o link acess�vel atrav�s de cada op��o deste n�vel
	// @note 		A consulta dos filhos deve trazer no m�nimo duas colunas, que ser�o
	// 				interpretadas respectivamente como o �ndice e o r�tulo da
	// 				op��o de menu em um determinado n�vel. A cl�usula de condi��o
	// 				deve fazer refer�ncia ao n�vel superior da �rvore de menus
	// 				em uma constru��o do tipo 'WHERE cod_menu = ~cod_menu~',
	// 				onde a vari�vel cod_menu do statement em quest�o ser� buscada
	// 				no n�vel imediatamente superior do menu, se este existir
	//!-----------------------------------------------------------------
	function loadFromDatabase($rootSql, $childSql, $limit=0, $connectionId=NULL) {
		$this->_Db =& Db::getInstance($connectionId);
		if ($this->_verifyRootSql($rootSql)) {
			$this->rootSql = $rootSql;
			if ($this->_verifyChildrenSql($childSql)) {
				$this->childSql = ereg_replace("~([^~])~", "~".strtoupper("\\1")."~", $childSql);
				$this->limit = TypeUtils::parseIntegerPositive($limit);
			} else {
				PHP2Go::raiseError(PHP2Go::getLangVal('ERR_MENU_WRONG_CHILDREN_STATEMENT'), E_USER_ERROR, __FILE__, __LINE__);
			}
		} else {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_MENU_WRONG_ROOT_SQL'), E_USER_ERROR, __FILE__, __LINE__);
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	Menu::loadFromXmlFile
	// @desc		Constr�i a �rvore do menu a partir de um arquivo XML
	// @access		public
	// @param		xmlFile string	Arquivo XML com a defini��o do menu
	// @return		void	
	// @note		Os itens do menu s�o constru�dos a partir dos filhos da raiz
	//				da �rvore. Qualquer tag pode ser utilizada nos n�veis da �rvore,
	//				desde que cada nodo que deve representar um item do menu
	//				contenha os atributos 'LINK' (vazio para itens de menu n�o 
	//				clic�veis) e 'CAPTION'. O atributo 'TARGET' tamb�m pode
	//				ser informado, tanto para o n�vel raiz como para os n�veis
	//				inferiores
	//!-----------------------------------------------------------------
	function loadFromXmlFile($xmlFile, $byFile=TRUE) {
		$XmlDoc =& new XmlDocument();
		$XmlDoc->parseXml($xmlFile, ($byFile === TRUE ? T_BYFILE : T_BYVAR));
		$this->xmlRoot = $XmlDoc->getRoot();
		if (!$this->xmlRoot->hasChildren()) {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_MENU_EMPTY_XML_ROOT'), E_USER_ERROR, __FILE__, __LINE__);
		}
	}

	//!-----------------------------------------------------------------
	// @function	Menu::buildMenu
	// @desc		Executa as opera��es e m�todos necess�rios � constru��o
	//				da estrutura de dados do menu, dependendo da forma de
	//				input dos dados: sql ou xml
	// @access		public
	// @return		void	
	//!-----------------------------------------------------------------
	function buildMenu() {
		// menu a partir de consultas SQL		
		if (isset($this->rootSql) && isset($this->childSql)) {
			$oldFetchMode = $this->_Db->setFetchMode(ADODB_FETCH_ASSOC);			
			$RootRs =& $this->_Db->query($this->rootSql);
			$this->_buildTreeFromDatabase($RootRs, 0, $this->tree);
			$this->rootSize = sizeOf($this->tree);
			$this->_Db->setFetchMode($oldFetchMode);
		// menu a partir de arquivo XML
		} else if (isset($this->xmlRoot)) {
			$this->_buildTreeFromXmlFile($this->xmlRoot, 0, $this->tree);			
			$this->rootSize = sizeOf($this->tree);
		// menu n�o inicializado
		} else {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_MENU_NOT_FOUND'), E_USER_ERROR, __FILE__, __LINE__);
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	Menu::_buildTreeFromXmlFile
	// @desc		Fun��o recursiva que constr�i a estrutura de dados do
	//				menu a partir dos nodos definidos no arquivo XML
	// @accesss		private
	// @param		Node XmlNode object	Objeto XMLNode cujos filhos devem ser inseridos no menu
	// @param		i int				�ndice do n�vel atual do menu
	// @param		&Tree array			Ponteiro onde devem ser inseridos os valores de novos nodos a cada itera��o
	// @return		void
	// @note		Este m�todo � executado em Menu::buildMenu caso a op��o
	//				de gera��o por XML tenha sido escolhida
	//!-----------------------------------------------------------------
	function _buildTreeFromXmlFile($Node, $i, &$Tree) {
		$cCount = 0;
		for ($i=0,$s=$Node->getChildrenCount(); $i<$s; $i++) {
			$Child = $Node->getChild($i);
			$Tree[$cCount] = array(
				'CAPTION' => $Child->getAttribute('CAPTION'),
				'LINK' => $Child->getAttribute('LINK'),
				'TARGET' => $Child->getAttribute('TARGET'),
				'CHILDREN' => array()
			);
			if ($i < $this->limit || $this->limit == 0) {
				if ($Child->hasChildren()) {
					$TreePtr =& $Tree[$cCount]['CHILDREN'];
					$this->_buildTreeFromXmlFile($Child, $i+1, $TreePtr);
				} elseif ($i >= $this->lastLevel) {
					$this->lastLevel = $i;
				}
			}
			$cCount++;
		}
	}

	//!-----------------------------------------------------------------
	// @function 	Menu::_buildTreeFromDatabase
	// @desc 		Fun��o recursiva que armazena em uma estrutura de dados
	// 				as defini��es do menu que resultam das consultas SQL
	// 				fornecidas
	// @access 		private
	// @param 		rs ADORecordSet object	Result Set de consulta ativo
	// @param 		i int					�ndice do n�vel atual do menu
	// @param 		&Tree array				Ponteiro onde devem ser inseridos os valores de novos nodos a cada itera��o
	// @return		void
	// @note		Este m�todo � executado em Menu::buildMenu caso tenha
	//				sido escolhida a constru��o do menu por consultas SQL
	//!-----------------------------------------------------------------
	function _buildTreeFromDatabase($rs, $i, &$Tree) {
		$cCount = 0;
		while ($cData = $rs->FetchRow()) {
			$cData = array_change_key_case($cData, CASE_UPPER);
			$Tree[$cCount] = array(
				'CAPTION' => $cData['CAPTION'],
				'LINK' => $cData['LINK'],
				'TARGET' => '',
				'CHILDREN' => array()
			);
			if ($i < $this->limit || $this->limit == 0) {
				$Child =& new Statement($this->childSql);
				if (sizeOf($Child->getDefinedVars()) > 0) {
					$bindError = FALSE;
					$definedVars = $Child->getDefinedVars();
					foreach ($definedVars as $var) {
						if (!isset($cData[strtoupper($var)]))
							$bindError = TRUE;
						else
							$Child->bindByName($var, $cData[strtoupper($var)], FALSE);
					}
					if (!$bindError) {
						$ChildRs =& $this->_Db->query($Child->getResult());
						if ($ChildRs->recordCount() > 0) {
							$TreePtr =& $Tree[$cCount]['CHILDREN'];
							$this->_buildTreeFromDatabase($ChildRs, $i+1, $TreePtr);
						} elseif ($i >= $this->lastLevel) {
							$this->lastLevel = $i;
						}
					}
				}
			}
			$cCount++;
		}
	}

	//!-----------------------------------------------------------------
	// @function 	Menu::_verifyRootSql
	// @desc 		Verifica se a consulta da raiz do menu retorna resultados
	// 				e se possui as colunas obrigat�rias caption e link
	// @access 		private
	// @param 		rootSql string	Consulta SQL da raiz do menu
	// @return		bool
	// @see 		Menu::loadFromDatabase
	// @see			Menu::_buildTreeFromDatabase
	// @see 		Menu::_verifyChildrenSql
	//!-----------------------------------------------------------------
	function _verifyRootSql($rootSql) {
		$RootRs =& $this->_Db->query($rootSql);
		if ($RootRs->recordCount() > 0) {
			$rootData = array_change_key_case($RootRs->fields, CASE_UPPER);
			if (!isset($rootData['CAPTION']) || !isset($rootData['LINK']))
				return FALSE;
			else 
				return TRUE;
		} else {
			return FALSE;
		}
	}

	//!-----------------------------------------------------------------
	// @function 	Menu::_verifyChildrenSql
	// @desc 		Verifica se a consulta que gera as op��es internas de menu
	// 				possui par�metros v�lidos para o n�vel superior ('WHERE codigo = ~codigo~') 
	//				e se possui as colunas obrigat�rias caption e link
	// @access 		private
	// @param 		childSql string	Statement/Consulta SQL das subop��es de menu
	// @return		bool
	// @see 		Menu::loadFromDatabase
	// @see			Menu::_buildTreeFromDatabase
	// @see 		Menu::_verifyRootSql
	//!-----------------------------------------------------------------
	function _verifyChildrenSql($childSql) {
		$Child =& new Statement($childSql);
		$vars = $Child->getDefinedVars();
		$fields = array();
		if (empty($vars)) {
			return FALSE;
		} else {
			foreach($vars as $var)
				$Child->bindByName($var, '-1', FALSE);
			$Rs =& $this->_Db->query($Child->getResult());
			for ($i=0,$s=$Rs->fieldCount(); $i<$s; $i++) {
				$Field = $Rs->fetchField($i);
				$fields[] = strtoupper($Field->name);
			}
			if (!in_array('CAPTION', $fields) || !in_array('LINK', $fields))
				return FALSE;
			else
				return TRUE;
		}
	}
}
?>