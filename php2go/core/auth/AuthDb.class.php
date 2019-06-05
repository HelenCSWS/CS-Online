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
// $Header: /www/cvsroot/php2go/core/auth/AuthDb.class.php,v 1.14 2005/07/18 15:03:10 mpont Exp $
// $Date: 2005/07/18 15:03:10 $

//------------------------------------------------------------------
import('php2go.auth.Auth');
import('php2go.db.QueryBuilder');
import('php2go.text.StringUtils');
//------------------------------------------------------------------

// @const AUTH_DB_DEFAULT_TABLE "auth"
// Nome padrão da tabela utilizada na consulta de autenticação
define('AUTH_DB_DEFAULT_TABLE', 'auth');

//!-----------------------------------------------------------------
// @class		AuthDb
// @desc		Classe de autenticação de usuários baseada em dados armazenados
//				em uma tabela de um banco de dados
// @package		php2go.auth
// @uses		Db
// @uses		QueryBuilder
// @uses		StringUtils
// @extends		Auth
// @author		Marcos Pont
// @version		$Revision: 1.14 $
//!-----------------------------------------------------------------
class AuthDb extends Auth
{
	var $connectionId = NULL;	// @var connectionId string			"NULL" ID da conexão de banco de dados a ser utilizada
	var $tableName;				// @var tableName string			Nome da tabela que armazena dados de usuários
	var $dbFields = '';			// @var dbFields string				"" String contendo outros dados do usuário que devem ser consultados e armazenados
	var $extraClause = '';		// @var extraClause string			Cláusula adicional a ser utilizada na consulta por usuários
	var $cryptFunction = '';	// @var cryptFunction string		"" Função de criptografia aplicada na senha do usuário
	var $Query = NULL;			// @var Query QueryBuilder object	"" Armazena a consulta de autenticação a ser enviada ao banco de dados
	
	//!-----------------------------------------------------------------
	// @function	AuthDb::AuthDb
	// @desc		Construtor da classe
	// @access		public
	//!-----------------------------------------------------------------
	function AuthDb() {
		parent::Auth();
		$this->tableName = AUTH_DB_DEFAULT_TABLE;
	}
	
	//!-----------------------------------------------------------------
	// @function	AuthDb::setConnectionId
	// @desc		Seta o ID da conexão a banco de dados a ser utilizado
	// @access		public
	// @param		id string	ID da conexão
	// @return		void
	//!-----------------------------------------------------------------
	function setConnectionId($id) {
		$this->connectionId = $id;
	}
	
	//!-----------------------------------------------------------------
	// @function	AuthDb::setTableName
	// @desc		Define o nome da tabela que contém dados de usuários
	// @access		public
	// @param		tableName string	Nome da tabela
	// @return		void
	// @see			AuthDb::setDbFields
	//!-----------------------------------------------------------------
	function setTableName($tableName) {
		if (trim($tableName) != '')
			$this->tableName = $tableName;
	}
	
	//!-----------------------------------------------------------------
	// @function	AuthDb::setDbFields
	// @desc		Define outros campos que devem ser inseridos na consulta ao banco de dados
	// @access		public
	// @param		dbFields mixed	Vetor de campos ou string com o nome de um ou mais campos
	// @return		void	
	// @note		Se forem adicionados campos à consulta, eles serão registrados
	//				individualmente como propriedades da sessão
	// @see			AuthDb::setTableName
	//!-----------------------------------------------------------------
	function setDbFields($dbFields) {
		if (TypeUtils::isArray($dbFields)) {
			$dbFields = array_unique($dbFields);
			$this->dbFields = implode(', ', $dbFields);
		} else {
			$dbFields = trim($dbFields);
			if (StringUtils::left($dbFields, 1) == ',')
				$dbFields = trim(substr($dbFields, 1));
			$this->dbFields = $dbFields;
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	AuthDb::setExtraClause
	// @desc		Define uma expressão a ser utilizada em conjunto com a pesquisa
	//				pelo login informado na consulta de autenticação de usuários
	// @access		public
	// @param		extraClause string	Cláusula extra
	// @note		Exemplo de uso:
	//				<PRE>
	//
	//				$auth->setTableName('users');
	//				$auth->setDbFields(array('cod_user', 'name'));
	//				$auth->setExtraClause('user_active = 1');
	//
	//				</PRE>
	//!-----------------------------------------------------------------
	function setExtraClause($extraClause) {
		$this->extraClause = $extraClause;
	}
	
	//!-----------------------------------------------------------------
	// @function	AuthDb::setCryptFunction
	// @desc		Define a função de criptografia da senha do usuário
	// @access		public
	// @param		cryptFunction string	Nome da função para criptografar a senha
	// @return		void	
	// @note		A função fornecida pode ser uma dentre as implementadas
	//				no PHP (md5, crypt) ou uma função definida pelo usuário
	//!-----------------------------------------------------------------
	function setCryptFunction($cryptFunction) {
		$cryptFunction = trim($cryptFunction);
		if ($cryptFunction != '' && function_exists($cryptFunction)) {
			$this->cryptFunction = $cryptFunction;
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	AuthDb::authenticate
	// @desc		Realiza a verificação de autenticação do usuário
	// @access		public
	// @return		array Dados do usuário ou FALSE em caso de falha na autenticação
	// @note		Este método é executado em Auth::login
	// @note		Se necessário, sobrescreva este método para alterar a forma como
	//				os dados são consultados no banco, por exemplo, utilizando uma
	//				stored procedure
	//!-----------------------------------------------------------------
	function authenticate() {
		$Db =& Db::getInstance($this->connectionId);
		$this->Query =& new QueryBuilder();
		$this->Query->addTable($this->tableName);
		if ($this->dbFields == '*') {
			$this->Query->setFields('*');
		} else {
			$this->Query->setFields($this->loginFieldName);
			if (!empty($this->dbFields))
				$this->Query->addFields($this->dbFields);
		}
		$this->Query->setClause($this->loginFieldName . " = " . $Db->quoteString($this->_login, get_magic_quotes_gpc()));
		if (!empty($this->cryptFunction)) {
			$crypt = $this->cryptFunction;
			$this->Query->addClause($this->passwordFieldName . " = " . $Db->quoteString($crypt($this->_password), get_magic_quotes_gpc()));
		} else {
			$this->Query->addClause($this->passwordFieldName . " = " . $Db->quoteString($this->_password, get_magic_quotes_gpc()));
		}
		if (!empty($this->extraClause))
			$this->Query->addClause($this->extraClause);
		// executa a consulta
		$oldMode = $Db->setFetchMode(ADODB_FETCH_ASSOC);
		$Rs =& $Db->query($this->Query->getQuery());
		$Db->setFetchMode($oldMode);
		if ($Rs->recordCount() == 0)
			return FALSE;
		return $Rs->fields;
	}
}
?>