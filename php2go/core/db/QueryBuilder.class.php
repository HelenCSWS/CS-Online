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
// $Header: /www/cvsroot/php2go/core/db/QueryBuilder.class.php,v 1.11 2005/06/08 23:00:28 mpont Exp $
// $Date: 2005/06/08 23:00:28 $

//------------------------------------------------------------------
import('php2go.data.DataSet');
//------------------------------------------------------------------

// @const QUERY_BUILDER_AND 	"AND"	
// Constante referente ao operador AND
define('QUERY_BUILDER_AND', 'AND');

// @const QUERY_BUILDER_OR		"OR"
// Constante referente ao operador OR
define('QUERY_BUILDER_OR', 'OR');

// @const QUERY_BUILDER_OP_NONE	"0"
// Define que a cl�usula n�o deve ser parentizada com outro operador
define('QUERY_BUILDER_OP_NONE', 0);

// @const QUERY_BUILDER_OP_LAST "1"
// Operar com o �ltimo operando
define('QUERY_BUILDER_OP_LAST', 1);

// @const QUERY_BUILDER_OP_ALL "2"
// Operar com todos os operandos
define('QUERY_BUILDER_OP_ALL', 2);

//!-----------------------------------------------------------------
// @class 		QueryBuilder
// @desc 		Constr�i consultas SQL (apenas DQL) a partir de por��es
// 				do c�digo, permitindo a combina��o dos elementos e a manipula��o
// 				dos mesmos independentemente
// @package		php2go.db
// @extends 	PHP2Go
// @author 		Marcos Pont
// @version		$Revision: 1.11 $
//!-----------------------------------------------------------------
class QueryBuilder extends PHP2Go
{
	var $distinct;				// @var distinct bool		Indica se apenas linhas distintas devem ser buscadas na consulta
	var $fields;				// @var fields string		Campos envolvidos na consulta
	var $tables;				// @var tables string		Tabelas e suas jun��es envolvidas na consulta
	var $clause;				// @var clause string		Cl�usula de condi��o da consulta
	var $groupby;				// @var groupby string		Agrupamento para a consulta
	var $condition;				// @var condition string	Condi��o da cl�usula de agrupamento
	var $orderby;				// @var orderby string		Ordena��o da consulta
	var $queryCode = '';		// @var queryCode string	"" C�digo final montado para a query
	var $upCaseWords = TRUE;	// @var upCaseWords bool	"TRUE" Utilizar mai�sculas nas palavras reservadas da query

	//!-----------------------------------------------------------------
	// @function 	QueryBuilder::QueryBuilder
	// @desc 		Inicializa o construtor de queries de banco de dados
	// @access 		public
	// @param 		fields string		"" Campos
	// @param 		tables string		"" Tabelas
	// @param 		clause string		"" Cl�usula de Condi��o
	// @param 		groupby string		"" Agrupamento
	// @param 		orderby string		"" Ordena��o
	// @note 		Os elementos da consulta devem ser fornecidos sem as
	// 				palavras reservadas SELECT, FROM, WHERE, ...
	//!-----------------------------------------------------------------
	function QueryBuilder($fields = '', $tables = '', $clause = '', $groupby = '', $orderby = '') {
		PHP2Go::PHP2Go();
		$this->distinct = FALSE;
		$this->fields = $fields;
		$this->tables = $tables;
		$this->clause = $clause;
		$this->groupby = $groupby;
		$this->condition = '';
		$this->orderby = $orderby;
	}
	
	//!-----------------------------------------------------------------
	// @function	QueryBuilder::setDistinct
	// @desc		Define valor para o flag distinct
	// @access		public
	// @param		setting bool		"TRUE" Valor para o flag
	// @return		void
	//!-----------------------------------------------------------------
	function setDistinct($setting=TRUE) {
		$this->distinct = TypeUtils::toBoolean($setting);
	}

	//!-----------------------------------------------------------------
	// @function	QueryBuilder::addFields
	// @desc		Adiciona um ou mais campos na consulta SQL em constru��o
	// @access		public
	// @param		fields string		Campos a serem inseridos, um ou mais,
	//									separados por v�rgula
	// @return		void
	// @note		Se a propriedade fields estiver vazia, o valor fornecido
	//				como par�metro para este m�todo ser� atribu�do e n�o concatenado
	//!-----------------------------------------------------------------
	function addFields($fields) {
    	if (empty($this->fields))
        	$this->fields = $fields;
		else
        	$this->fields .= ', ' . $fields;
	}
	
	//!-----------------------------------------------------------------
	// @function	QueryBuilder::setFields
	// @desc		Configura a propriedade fields da classe
	// @param		fields string		"*" Valor para a propriedade fields
	// @return		string Valor antigo da propriedade
	//!-----------------------------------------------------------------
	function setFields($fields='*') {
		$oldValue = $this->fields;
		$this->fields = $fields;
		return $oldValue;
	}
	
	//!-----------------------------------------------------------------
	// @function	QueryBuilder::addTable
	// @desc		Adiciona uma tabela � lista de tabelas envolvidas na consulta
	// @access		public
	// @param		tableName string	Nome da tabela
	// @return		void
	//!-----------------------------------------------------------------
	function addTable($tableName) {
		if (empty($this->tables))
			$this->tables = $tableName;
		else
			$this->tables .= ', ' . $tableName;
	}
	
	//!-----------------------------------------------------------------
	// @function	QueryBuilder::joinTable
	// @desc		Adiciona uma tabela � consulta SQL utilizando uma opera��o de jun��o
	// @access		public
	// @param		tableName string		Nome da tabela
	// @param		joinType string			Opera��o de jun��o (INNER JOIN, LEFT OUTER JOIN)
	// @param		joinCondition string	Condi��o da opera��o de jun��o
	// @return		void
	//!-----------------------------------------------------------------
	function joinTable($tableName, $joinType, $joinCondition) {
		if (!empty($this->tables)) {
			$this->tables .= " $joinType $tableName ON ($joinCondition)";
		}			
	}

	//!-----------------------------------------------------------------
	// @function 	QueryBuilder::addClause
	// @desc 		Inclui um valor � cl�usula de condi��o da consulta
	// @access 		public
	// @param 		clause string		Valor a ser adicionado � cl�usula de condi��o,
	// 									sem a palavra reservada 'WHERE'
	// @param 		and bool			"TRUE" Utilizar ou n�o o operador AND. O valor FALSE
	// 									indica o uso do operador OR
	// @return		bool
	//!-----------------------------------------------------------------
	function addClause($clause, $operator = QUERY_BUILDER_AND, $action = QUERY_BUILDER_OP_NONE) {
		if (empty($clause)) {
			return FALSE;
		} elseif (empty($this->clause)) {
			$this->clause = $clause;
			return TRUE;
		} else if (!in_array($operator, array(QUERY_BUILDER_AND, QUERY_BUILDER_OR))) {
			return FALSE;
		} else {
			switch ($action) {
				case QUERY_BUILDER_NONE :
					$this->clause .= ' ' . $operator . ' ' . $clause;
					break;
				case QUERY_BUILDER_OP_LAST :
					$v = preg_split('/and|or/i', $this->clause, -1);
					if (sizeOf($v) == 1) {
						$this->clause = '( ' . $this->clause . ' ' . $operator . ' ' . $clause . ' )';
					} else {
						$last = $v[sizeOf($v)-1];
						if (preg_match("/([^\)]+)(\)[ ]?)+/i", $last, $matches)) {
							$this->clause = eregi_replace("$matches[1]", " (\\0$operator $clause ) ", $this->clause);
						} else {
							$this->clause = eregi_replace("$last", " (\\0 $operator $clause )", $this->clause);
						}
					}
					break;
				case QUERY_BUILDER_OP_ALL :
					$this->clause = '(' . $this->clause . ') ' . $operator . ' ' . $clause;
					break;
				default :
					$this->clause .= ' ' . $operator . ' ' . $clause;				
			}
			return TRUE;
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	QueryBuilder::setClause
	// @desc		Configura o valor da cl�usula de condi��o da query na classe
	// @param		clause string		"" Valor para a cl�usula
	// @return		string Valor antigo da propriedade
	//!-----------------------------------------------------------------
	function setClause($clause='') {
		$oldValue = $this->clause;
		$this->clause = $clause;
		return $oldValue;
	}
	
	//!-----------------------------------------------------------------
	// @function	QueryBuilder::clearClause
	// @desc		Reseta a cl�usula de condi��o da consulta SQL
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function clearClause() {
		$this->clause = '';
	}

	//!-----------------------------------------------------------------
	// @function 	QueryBuilder::setGroup
	// @desc 		Configura o agrupamento da consulta
	// @access 		public
	// @param 		groupby string		"" Trecho de agrupamento da consulta
	// @param		condition string	"" Condi��o HAVING para a cl�usula groupby
	// @return		void	
	// @see 		QueryBuilder::setOrder
	// @note		Utilize QueryBuilder::setGroup() para resetar o valor da propriedade
	//				groupby na classe
	//!-----------------------------------------------------------------
	function setGroup($groupby='', $condition='') {
		$this->groupby = $groupby;
		if (trim($groupby) == '')
			$this->condition = '';
		else if (trim($condition) != '')
			$this->condition = $condition;
	}
	
	//!-----------------------------------------------------------------
	// @function	QueryBuilder::prefixOrder
	// @desc		Prefixa a propriedade 'orderby' da consulta com o valor
	//				passado no par�metro $orderby
	// @param		orderby string	Campo(s) priorit�rio(s) de ordena��o para a consulta
	// @return		void	
	// @note		Se a consulta n�o possuir ordena��o, ela ser� criada
	//!-----------------------------------------------------------------
	function prefixOrder($orderby) {
    	if (empty($this->orderby))
        	$this->setOrder($orderby);
		else
        	$this->orderby = $orderby . ' , ' . $this->orderby;
	}
	
	//!-----------------------------------------------------------------
	// @function	QueryBuilder::addOrder
	// @desc		Concatena um novo valor � cl�usula de ordena��o da consulta
	// @access		public
	// @param		orderby string	Nova cl�usula de ordena��o
	// @return		void
	// @note		Se a consulta n�o possui ordena��o, ela ser� criada
	//!-----------------------------------------------------------------
	function addOrder($orderby) {
		if (empty($this->orderby))
			$this->setOrder($orderby);
		else
			$this->orderby = $this->orderby . ' , ' . $orderby;
	}

	//!-----------------------------------------------------------------
	// @function 	QueryBuilder::setOrder
	// @desc 		Configura a ordena��o da consulta
	// @access 		public
	// @param 		orderby string	Trecho de ordena��o de uma consulta,
	// 									sem a palavra reservada 'ORDER BY'
	// @return		void	
	// @see 		QueryBuilder::setGroup
	// @note		Utilize QueryBuilder::setOrder() para resetar o valor da propriedade
	//				orderby na classe	
	//!-----------------------------------------------------------------
	function setOrder($orderby='') {
		$this->orderby = $orderby;
	}
	
	//!-----------------------------------------------------------------
	// @function	QueryBuilder::reset
	// @desc		Reseta todos os par�metros da consulta SQL
	// @access		public
	// @return		void	
	//!-----------------------------------------------------------------
	function reset() {
		$this->distinct = FALSE;
		$this->fields = '';
		$this->tables = '';
		$this->clause = '';
		$this->groupby = '';
		$this->condition = '';
		$this->orderby = '';
	}

	//!-----------------------------------------------------------------
	// @function	QueryBuilder::displayQuery
	// @desc		Exibe o c�digo da query
	// @access		public
	// @param		preFormatted bool		"TRUE" Manter a formata��o existente no c�digo SQL
	// @return		void	
	// @see			QueryBuilder::getQuery
	//!-----------------------------------------------------------------
	function displayQuery($preFormatted=TRUE) {
		$this->_buildQuery(TRUE);
		$this->_formatReserved();
		if ($preFormatted)
			print '<PRE>' . $this->queryCode . '</PRE><BR>';
		else
        	print $this->queryCode . '<BR>';
	}

	//!-----------------------------------------------------------------
	// @function 	QueryBuilder::getQuery
	// @desc 		Constr�i e retorna o c�digo da consulta SQL a partir
	// 				dos valores atuais encontrados no objeto
	// @access 		public
	// @return		string C�digo da consulta SQL
	// @see			QueryBuilder::displayQuery
	//!-----------------------------------------------------------------
	function getQuery() {
		if (empty($this->fields) || empty($this->tables)) {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_MISSING_QUERY_ELEMENTS'), E_USER_ERROR, __FILE__, __LINE__);
		} else {
			$this->_buildQuery();
			$this->_formatReserved();
			return $this->queryCode;
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	QueryBuilder::executeQuery
	// @desc		Busca uma conex�o com o banco de dados e executa a consulta SQL
	// @access		public			
	// @param		bindVars array		"array()" Vari�veis de amarra��o para a consulta
	// @param		connectionId string	"NULL" ID da conex�o ao banco de dados a ser utilizada
	// @return		ADORecordSet object Conjunto de resultados da consulta
	//!-----------------------------------------------------------------
	function executeQuery($bindVars=array(), $connectionId=NULL) {
		$Db =& Db::getInstance($connectionId);
		return $Db->query($this->getQuery(), TRUE, $bindVars);
	}
	
	//!-----------------------------------------------------------------
	// @function	QueryBuilder::&createDataSet
	// @desc		Constr�i um dataset a partir da consulta SQL
	// @access		public
	// @param		params array	"array()" Par�metros para a constru��o do dataset
	// @return		DataSetDb object DataSet resultante
	//!-----------------------------------------------------------------
	function &createDataSet($params=array()) {
		$DataSet =& DataSet::getInstance('db', $params);
		$DataSet->load($this->getQuery());
		return $DataSet;
	}

	//!-----------------------------------------------------------------
	// @function	QueryBuilder::_buildQuery
	// @desc		Monta o c�digo da query a partir das partes armazenadas
	// @access		private
	// @param		idDisplay bool	"FALSE" Indica se a query est� sendo constru�da para exibi��o ou para execu��o
	// @return		void	
	// @see			QueryBuilder::displayQuery
	// @see			QueryBuilder::getQuery
	// @note		Este m�todo privado � executado em displayQuery() e getQuery()
	//!-----------------------------------------------------------------
	function _buildQuery($isDisplay=FALSE) {
		$char = ($isDisplay) ? "\r\n\t" : ' ';
		$this->queryCode = "SELECT " . $char . ($this->distinct ? "DISTINCT " : "") . $this->fields;
		$this->queryCode .= $char . "FROM " . $char . eregi_replace("JOIN[ ]", "JOIN$char", $this->tables);
		if (!empty($this->clause))
			$this->queryCode .= $char . "WHERE " . $char . $this->clause;
		if (!empty($this->groupby)) {
			$this->queryCode .= $char . "GROUP BY " . $char . $this->groupby;
			if (!empty($this->condition))
				$this->queryCode .= " HAVING " . $this->condition;
		}
		if (!empty($this->orderby))
			$this->queryCode .= $char . "ORDER BY " . $char . $this->orderby;
	}

	//!-----------------------------------------------------------------
	// @function	QueryBuilder::_formatReserved
	// @desc		Formata as palavras reservadas da query
	// @access		private
	// @return		void	
	// @note		� montado um c�digo SQL com as palavras reservadas formatadas para
	//				mai�sculas ou min�sculas de acordo com o valor da propriedade upCaseWords.
	//				O conjunto de palavras reservadas foi extra�do do padr�o ANSI SQL 1998
	//!-----------------------------------------------------------------
    function _formatReserved() {
		$reservedWordsUp = 	array("ALL","AND","AS","ASC","AVG","BETWEEN","BY","CASE","CAST","CHAR","COUNT","CURRENT","CURRENT_DATE","CURRENT_TIME","CURRENT_TIMESTAMP","CURRENT_USER","CURSOR","DATE","DEC","" .
							"DESC","DISTINCT","ELSE","END","EXISTS","FALSE","FROM","FLOAT","GROUP","HAVING","IN","INNER","INTERSECT","INTERVAL","IS","JOIN","LAST","LEFT","LIKE","MAX","MIN","MONTH","" .
							"NATURAL","NEXT","NOT","NULL","NULLIF","NUMERIC","OF","ON","ONLY","OR","ORDER","OUTER","RIGHT","SELECT","SMALLINT","SUBSTRING","SUM","THEN","TRIM","TRUE","UNION","UPPER","" .
							"USING","VARYING","WHEN","WHERE","WITH");
		$reservedWordsLow =	array("all","and","as","asc","avg","between","by","case","cast","char","count","current","current_date","current_time","current_timestamp","current_user","cursor","date","dec","" .
							"desc","distinct","else","end","exists","false","from","float","group","having","in","inner","intersect","interval","is","join","last","left","like","max","min","month","" .
							"natural","next","not","null","nullif","numeric","of","on","only","or","order","outer","right","select","smallint","substring","sum","then","trim","true","union","upper","" .
							"using","varying","when","where","with");
		if ($this->upCaseWords) {
			while (list(, $word) = each($reservedWordsUp)) {
				$this->queryCode = substr(eregi_replace('[[:space:]]' . $word . '[[:space:]]', ' ' . $word  . ' ', ' ' . $this->queryCode), 1);
			}
		} else {
			while (list(, $word) = each($reservedWordsLow)) {
				$this->queryCode = substr(eregi_replace('[[:space:]]' . $word . '[[:space:]]', ' ' . $word  . ' ', ' ' . $this->queryCode), 1);
			}
		}
	}
}
?>