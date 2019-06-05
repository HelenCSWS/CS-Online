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
// $Header: /www/cvsroot/php2go/core/data/DataSet.class.php,v 1.8 2005/08/31 22:08:19 mpont Exp $
// $Date: 2005/08/31 22:08:19 $

//!-----------------------------------------------------------------
// @class		DataSet
// @desc		A classe DataSet é uma interface para a construção de conjuntos
//				de dados através dos quais é possível navegar utilizando um ponteiro, 
//				permitindo a criação de iterações sobre estes dados.<BR>
//				Para tal, existe um conjunto de <B>adaptadores de dados</B> capazes
//				de montar e manipular um DataSet partindo de diversas fontes: banco de dados,
//				arquivo CSV, arquivo XML ou arrays.
// @package		php2go.data
// @extends		PHP2Go
// @uses		TypeUtils
// @author		Marcos Pont
// @version		$Revision: 1.8 $
//!-----------------------------------------------------------------
class DataSet extends PHP2Go
{
	var $adapter = NULL;	// @var adapter mixed			"NULL" Objeto que representa o adaptador de dados da classe
	var $adapterType;		// @var adapterType string		Tipo do adaptador de dados (db, csv, xml, array)
	
	//!-----------------------------------------------------------------
	// @function	DataSet::DataSet
	// @desc		Construtor da classe
	// @access		public
	// @param		type string		Tipo de adaptador (db, csv, xml, array)
	// @param		params array	"array()" Parâmetros de inicialização do adaptador
	//!-----------------------------------------------------------------
	function DataSet($type, $params=array()) {
		parent::PHP2Go();
		$this->_factory($type, $params);
	}

	//!-----------------------------------------------------------------
	// @function	DataSet::&getInstance
	// @desc		Retorna uma instância única de um determinado tipo de DataSet
	// @access		public
	// @param		type string		Tipo de adaptador (db, csv, xml, array)
	// @param		params array	"array()" Parâmetros de inicialização do adaptador
	// @note		Parâmetros do tipo "db": debug (bool), connectionId (string)<BR>
	//				Parâmetros do tipo "xml": nenhum<BR>
	//				Parâmetros do tipo "csv": nenhum<BR>
	//				Parâmetros do tipo "array": nenhum	
	// @return		DataSet object	
	// @static	
	//!-----------------------------------------------------------------
	function &getInstance($type, $params=array()) {
		static $instances;
		$type = (trim($type) != '' ? strtolower(trim($type)) : 'custom');
		$hash = $type . serialize($params);
		if (!isset($instances[$hash]))
			$instances[$hash] = new DataSet($type, $params);
		return $instances[$hash];
	}
	
	//!-----------------------------------------------------------------
	// @function	DataSet::load
	// @desc		Este método recebe uma quantidade variável de parâmetros
	//				dependendo do adaptador de dados utilizado. A partir dos parâmetros
	//				recebidos, o método load() interno ao adaptador é executado
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function load() {
		$args = func_get_args();
		@call_user_func_array(array(&$this->adapter, 'load'), $args);
	}
	
	//!-----------------------------------------------------------------
	// @function	DataSet::loadSubSet
	// @desc		Este método recebe uma quantidade variável de parâmetros
	//				dependendo do adaptador de dados utilizado. A partir dos parâmetros
	//				fornecidos, o método loadSubSet() interno ao adaptador é executado
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function loadSubSet() {
		$args = func_get_args();
		@call_user_func_array(array(&$this->adapter, 'loadSubSet'), $args);		
	}
	
	//!-----------------------------------------------------------------
	// @function	DataSet::getFieldCount
	// @desc		Busca o número de colunas/campos do DataSet criado
	// @return		int Número de campos
	// @see			DataSet::getRecordCount
	// @access		public	
	//!-----------------------------------------------------------------
	function getFieldCount() {
		return $this->adapter->getFieldCount();
	}
	
	//!-----------------------------------------------------------------
	// @function	DataSet::getFieldNames
	// @desc		Monta um vetor contendo os nomes dos campos do DataSet
	// @return		array Vetor de campos do conjunto de dados
	// @see			DataSet::getFieldNames
	// @access		public	
	//!-----------------------------------------------------------------
	function getFieldNames() {
		return $this->adapter->getFieldNames();
	}
	
	//!-----------------------------------------------------------------
	// @function	DataSet::getFieldName
	// @desc		Busca o nome de um determinado campo do conjunto de dados,
	//				a partir de seu índice
	// @param		i int	Índice do campo
	// @return		string Nome do campo buscado
	// @access		public	
	//!-----------------------------------------------------------------
	function getFieldName($i) {
		$fieldNames = $this->adapter->getFieldNames();
		return (isset($fieldNames[$i]) ? $fieldNames[$i] : NULL);
	}
	
	//!-----------------------------------------------------------------
	// @function	DataSet::getField
	// @desc		Busca o valor de um campo na posição atual do cursor
	//				através de seu índice ou de seu nome
	// @param		fieldId mixed	Índice ou nome do campo buscado
	// @return		mixed	Valor do campo no registro atual
	// @access		public	
	//!-----------------------------------------------------------------
	function getField($fieldId) {
		return $this->adapter->getField($fieldId);
	}	
	
	//!-----------------------------------------------------------------
	// @function	DataSet::getRecordCount
	// @desc		Retorna o número de registros do DataSet
	// @access		public
	// @return		int Número de registros
	// @see			DataSet::getFieldCount
	//!-----------------------------------------------------------------
	function getRecordCount() {
		return $this->adapter->getRecordCount();
	}
	
	//!-----------------------------------------------------------------
	// @function	DataSet::getAbsolutePosition
	// @desc		Retorna a posição atual do cursor
	// @return		int Posição atual do cursor
	// @access		public	
	//!-----------------------------------------------------------------
	function getAbsolutePosition() {
		return $this->adapter->getAbsolutePosition();
	}
	
	//!-----------------------------------------------------------------
	// @function	DataSet::current
	// @desc		Busca o registro apontado pela posição atual do cursor
	// @return		array Vetor contendo dados do registro atual
	// @access		public	
	//!-----------------------------------------------------------------
	function current() {
		return $this->adapter->current();
	}	
	
	//!-----------------------------------------------------------------
	// @function	DataSet::fetch
	// @desc		Retorna um vetor contendo a linha atual, ou FALSE se
	//				o final do DataSet for atingido
	// @return		mixed Vetor contendo o registro atual ou FALSE
	// @see			DataSet::fetchInto
	// @access		public	
	//!-----------------------------------------------------------------
	function fetch() {
		return $this->adapter->fetch();
	}
	
	//!-----------------------------------------------------------------
	// @function	DataSet::fetchInto
	// @desc		Armazena o conteúdo do registro atual no vetor passado
	//				através do parâmetro $dataArray. Retorna FALSE se o final
	//				do conjunto de resultados foi atingido
	// @param		&dataArray array	Vetor para armazenamento do registro
	// @see			DataSet::fetch	
	// @access		public	
	// @return		bool
	//!-----------------------------------------------------------------
	function fetchInto(&$dataArray) {
		return $this->adapter->fetchInto($dataArray);
	}
	
	//!-----------------------------------------------------------------
	// @function	DataSet::eof
	// @desc		Verifica se o final do conjunto de resultados foi atingido
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function eof() {
		return $this->adapter->eof();
	}
	
	//!-----------------------------------------------------------------
	// @function	DataSet::move
	// @desc		Move o ponteiro para um determinado número de registro
	// @param		recordNumber int	Número do registro
	// @access		public	
	// @return		bool
	// @see			DataSet::movePrevious
	// @see			DataSet::moveNext
	// @see			DataSet::moveFirst
	// @see			DataSet::moveLast	
	//!-----------------------------------------------------------------
	function move($recordNumber) {
		return $this->adapter->move($recordNumber);
	}
	
	//!-----------------------------------------------------------------
	// @function	DataSet::moveNext
	// @desc		Move o ponteiro para o próximo registro
	// @access		public
	// @return		bool
	// @note		Retorna FALSE se o final do DataSet foi atingido
	// @see			DataSet::move
	// @see			DataSet::movePrevious
	// @see			DataSet::moveFirst
	// @see			DataSet::moveLast	
	//!-----------------------------------------------------------------
	function moveNext() {
		return $this->adapter->moveNext();
	}
	
	//!-----------------------------------------------------------------
	// @function	DataSet::movePrevious
	// @desc		Move o ponteiro para o registro anterior
	// @access		public
	// @return		bool
	// @note		Retorna FALSE se o início do DataSet foi alcançado
	// @see			DataSet::move
	// @see			DataSet::moveNext
	// @see			DataSet::moveFirst
	// @see			DataSet::moveLast
	//!-----------------------------------------------------------------
	function movePrevious() {
		return $this->adapter->movePrevious();
	}	
	
	//!-----------------------------------------------------------------
	// @function	DataSet::moveFirst
	// @desc		Move o ponteiro para o primeiro registro
	// @access		public
	// @return		bool
	// @see			DataSet::move
	// @see			DataSet::moveNext
	// @see			DataSet::movePrevious
	// @see			DataSet::moveLast
	//!-----------------------------------------------------------------
	function moveFirst() {
		return $this->adapter->moveFirst();
	}
	
	//!-----------------------------------------------------------------
	// @function	DataSet::moveLast
	// @desc		Move o ponteiro para o último registro
	// @access		public
	// @return		bool
	// @see			DataSet::move
	// @see			DataSet::moveNext
	// @see			DataSet::movePrevious
	// @see			DataSet::moveFirst
	//!-----------------------------------------------------------------
	function moveLast() {
		return $this->adapter->moveLast();
	}
	
	//!-----------------------------------------------------------------
	// @function	DataSet::_factory
	// @desc		Constrói um objeto DataSet de um determinado tipo
	// @access		private
	// @param		type string		"NULL" Tipo de adaptador
	// @param		params array	"array()" Parâmetros de inicialização do adaptador
	// @return		void
	//!-----------------------------------------------------------------
	function _factory($type, $params=array()) {
		$type = ucfirst(strtolower(trim($type)));
		$className = 'DataSet' . $type;
		if (import("php2go.data.adapter.{$className}")) {
			$this->adapter = new $className($params);
			$this->adapterType = $type;
		} else {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_DATASET_INVALID_TYPE', $type), E_USER_ERROR, __FILE__, __LINE__);
		}
	}	
}
?>