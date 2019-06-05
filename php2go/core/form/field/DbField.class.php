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
// $Header: /www/cvsroot/php2go/core/form/field/DbField.class.php,v 1.18 2005/08/30 20:43:06 mpont Exp $
// $Date: 2005/08/30 20:43:06 $

//------------------------------------------------------------------
import('php2go.db.QueryBuilder');
import('php2go.form.field.FormField');
//------------------------------------------------------------------

//!-----------------------------------------------------------------
// @class		DbField
// @desc		Os campos que utilizam dados provenientes de uma base
//				de dados utilizam esta classe como base para montagem
//				da fonte de dados para gerar os valores do campo
// @package		php2go.form.field
// @uses		ADORecordSet
// @uses		Db
// @uses		QueryBuilder
// @uses		TypeUtils
// @extends		FormField
// @author		Marcos Pont
// @version		$Revision: 1.18 $
//!-----------------------------------------------------------------
class DbField extends FormField
{
	var $dataSource = array();	// @var dataSource array					"array()" Vetor de nodos XML que representam elementos de uma consulta SQL
	var $isGrouping = FALSE;	// @var isGrouping bool						"FALSE" Indica se o datasource possui coluna de agrupamento
	var $isProcedure = FALSE;	// @var isProcedure bool					"FALSE" Indica se o datasource contщm uma chamada de stored procedure
	var $queryDone = FALSE;		// @var queryDone bool						"FALSE" Armazena o valor TRUE depois que a consulta for executada, evitando a repetiчуo da operaчуo
	var $_Db;					// @var _Db Db object						Objeto Db da conexуo com o banco de dados
	var $_Rs;					// @var _Rs ADORecordSet					Objeto ADORecordSet para manipulaчуo dos resultados da consulta

	//!-----------------------------------------------------------------
	// @function	DbField::DbField
	// @desc		Construtor da classe DbField
	// @access		public
	// @param		&Form FormObject	Objeto Form onde o campo serс inserido
	// @param		child bool			"FALSE" Se for TRUE, indica que o campo щ membro de um campo composto
	//!-----------------------------------------------------------------
	function DbField(&$Form, $child=FALSE) {
		parent::FormField($Form, $child);
		if ($this->isA("dbfield", FALSE))
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_ABSTRACT_CLASS', 'DbField'), E_USER_ERROR, __FILE__, __LINE__);
	}
	
	//!-----------------------------------------------------------------
	// @function	DbField::onLoadNode
	// @desc		Mщtodo responsсvel por processar atributos e nodos filhos
	//				provenientes da especificaчуo XML do campo
	// @access		protected
	// @param		attrs array		Atributos do nodo
	// @param		children array	Vetor de nodos filhos
	// @return		void
	//!-----------------------------------------------------------------
	function onLoadNode($attrs, $children) {
		parent::onLoadNode($attrs, $children);
		if (!isset($children['DATASOURCE'])) {
			$this->dataSource = array();			
		} else {
			$dataSource = $children['DATASOURCE'];
			$dataSourceElements = $dataSource->getChildrenTagsArray();
			// ID de conexуo a ser utilizado
			$connectionId = trim($dataSource->getAttribute('CONNECTION'));
			if (empty($connectionId))
				$connectionId = NULL;
			$this->dataSource['CONNECTIONID'] = $connectionId;			
			// nodo DATASOURCE sem nodos filhos
			if (empty($dataSourceElements))
				PHP2Go::raiseError(PHP2Go::getLangVal('ERR_INVALID_DATASOURCE_SYNTAX', $this->name), E_USER_ERROR, __FILE__, __LINE__);
			foreach($dataSourceElements as $name => $node) {
				if ($name == 'PROCEDURE')
					$this->dataSource['CURSORNAME'] = ($node->hasAttribute('CURSORNAME') ? $node->getAttribute('CURSORNAME') : NULL);
				$this->dataSource[$name] = (ereg("~[^~]+~", $node->value) ? Statement::evaluate($node->value) : $node->value);
			}
			// elementos KEYFIELD e LOOKUPTABLE do DATASOURCE sуo obrigatѓrios
			if (!isset($this->dataSource['PROCEDURE']) && (!isset($this->dataSource['KEYFIELD']) || !isset($this->dataSource['LOOKUPTABLE'])))
				PHP2Go::raiseError(PHP2Go::getLangVal('ERR_INVALID_DATASOURCE_SYNTAX', $this->name), E_USER_ERROR, __FILE__, __LINE__);
			else {
				if (!isset($this->dataSource['PROCEDURE'])) {
					if (!isset($this->dataSource['DISPLAYFIELD']))
						$this->dataSource['DISPLAYFIELD'] = $this->dataSource['KEYFIELD'];
					if (!isset($this->dataSource['CLAUSE']))
						$this->dataSource['CLAUSE'] = '';
					if (!isset($this->dataSource['GROUPBY']))
						$this->dataSource['GROUPBY'] = '';
					if (!isset($this->dataSource['ORDERBY']))
						$this->dataSource['ORDERBY'] = '';
					if (!isset($this->dataSource['GROUPFIELD']))
						$this->dataSource['GROUPFIELD'] = '';
					if (!isset($this->dataSource['GROUPDISPLAY']))
						$this->dataSource['GROUPDISPLAY'] = $this->dataSource['GROUPFIELD'];
					if (!empty($this->dataSource['GROUPFIELD']))
						$this->isGrouping = TRUE;
				} else {
					$this->isProcedure = TRUE;
				}
			}			
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	DbField::processDbQuery
	// @desc		Reњne as informaчѕes definidas no datasource da especificaчуo
	//				XML e constrѓi o conjunto de resultados
	// @param		fetchMode int	Fetch mode a ser utilizado
	// @param		debug bool		"FALSE" Habilitar ou nуo debug da consulta
	// @access		protected	
	// @return		void
	//!-----------------------------------------------------------------	
	function processDbQuery($fetchMode=ADODB_FETCH_DEFAULT, $debug=FALSE) {
		// cria a conexуo com o banco de dados
		$this->_Db = Db::getInstance($this->dataSource['CONNECTIONID']);		
		($debug) && $this->_Db->setDebug(TRUE);
		// evita que a consulta seja executada novamente em objetos como DataGrid		
		if ($this->queryDone) {
			$this->_Rs->moveFirst();
			return $this->_Rs;
		}
		// nуo existe fonte de dados configurada
		if (empty($this->dataSource)) {
			$this->queryDone = TRUE;			
			$this->_Rs = $this->_Db->emptyRecordSet();			
		} else {
			$this->queryDone = TRUE;
			$oldMode = $this->_Db->setFetchMode($fetchMode);
			if ($this->isProcedure) {
				$stmt = $this->_Db->getProcedureSql($this->dataSource['PROCEDURE']);
				$this->_Rs =& $this->_Db->execute($stmt, FALSE, @$this->dataSource['CURSORNAME']);
			} else {
				$Query = new QueryBuilder(
						$this->dataSource['KEYFIELD'] . ',' . $this->dataSource['DISPLAYFIELD'], 
						$this->dataSource['LOOKUPTABLE'], $this->dataSource['CLAUSE'], 
						$this->dataSource['GROUPBY'], $this->dataSource['ORDERBY']
				);
				if ($this->isGrouping) {
					$Query->addFields($this->dataSource['GROUPFIELD']);
					$Query->addFields($this->dataSource['GROUPDISPLAY']);
					$Query->prefixOrder($this->dataSource['GROUPDISPLAY']);
				}
				$stmt = $Query->getQuery();
				// execuчуo da consulta
				if (isset($this->dataSource['LIMIT']) && ereg("([0-9]{1,}),?([0-9]{1,})?", ereg_replace('[[:blank:]]', '', $this->dataSource['LIMIT']), $matches))
					$this->_Rs =& $this->_Db->limitQuery($stmt, TypeUtils::parseInteger($matches[1]), TypeUtils::parseInteger($matches[2]));
				else
					$this->_Rs =& $this->_Db->query($stmt);
			}
			$this->_Db->setFetchMode($oldMode);
		}
	}	
}
?>