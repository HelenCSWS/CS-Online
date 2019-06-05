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
// $Header: /www/cvsroot/php2go/core/db/Db.class.php,v 1.38 2005/08/31 22:07:53 mpont Exp $
// $Date: 2005/08/31 22:07:53 $

//------------------------------------------------------------------
require_once(PHP2GO_ROOT . "external/adodb/adodb.inc.php");
import('php2go.datetime.Date');
import('php2go.util.Callback');
//------------------------------------------------------------------

//!-----------------------------------------------------------------
// @class		Db
// @desc		Responsável por criar conexões a banco através da
//				biblioteca ADODb, e por conter funções que facilitam
//				a execução de instruções DML e operações sobre os
//				result sets que retornam do banco. Sobrecarrega as
//				funções mais importantes implementas na classe ADOConnection
// @package		php2go.db
// @extends		PHP2Go
// @uses		ADOConnection
// @uses		Callback
// @uses		Date
// @uses		TypeUtils
// @note		Esta classe utiliza as funcionalidades da biblioteca
//				ADODb. Para maiores informações sobre o projeto ADODb,
//				manuais e documentação, acesse http://adodb.sourceforge.net
// @author		Marcos Pont
// @version		$Revision: 1.38 $
//!-----------------------------------------------------------------
class Db extends PHP2Go
{
	var $connected;			// @var connected bool				Flag de controle do status da conexão com o banco de dados
	var $connParameters;	// @var connParameters array		Vetor com as propriedades da conexão ativa
	var $connFunc;			// @var connFunc string				Função utilizada para conexão, entre Connect e PConnect			
	var $AdoDb;				// @var AdoDb ADOConnection object	Objeto da conexão ao banco. Através dele, podem ser executados outros métodos implementados pela classe AdoConnection
	var $affectedRows;		// @var affectedRows int			Linhas afetadas ou resultantes da consulta
	var $makeCache;			// @var makeCache bool				Flag para utilização de cache nos comandos de query e busca por resultados
	var $cacheSecs;			// @var cacheSecs int				Número de segundos para cache de um comando/consulta
	var $aConnect = NULL;	// @var aConnect Callback object	Callback, definida nas configurações do usuário (DATABASE_BEFORECONNECT), que é executada após a criação da conexão com o banco de dados
	var $bClose = NULL;		// @var bClose Callback object		Callback definida na configuração (DATABASE_BEFORECLOSE) que é executada antes do encerramento da conexão com o banco de dados

	//!-----------------------------------------------------------------
	// @function	Db::Db
	// @desc		Construtor da classe Db. Verifica as variáveis
	//				da configuração do PHP2Go necessárias e cria a
	//				conexão com o banco através do ADODb
	// @access		public
	// @param		id string	"NULL" ID da conexão desejada
	//!-----------------------------------------------------------------
	function Db($id=NULL) {
		parent::PHP2Go();
		// busca dos parâmetros de conexão
		$this->connParameters = Conf::getConnectionParameters($id);
		$this->connFunc = ($this->connParameters['PERSISTENT'] ? 'PConnect' : 'Connect');
		// inicialização da biblioteca ADODB
		$this->AdoDb =& AdoNewConnection($this->connParameters['TYPE']);
		// callback de conexão
		if (@$this->connParameters['AFTERCONNECT'])
			$this->aConnect =& new Callback($this->connParameters['AFTERCONNECT']);
		// callback de encerramento
		if (@$this->connParameters['BEFORECLOSE'])
			$this->bClose =& new Callback($this->connParameters['BEFORECLOSE']);
		// criação da conexão
		$connFunc = $this->connFunc;		
		if (!$this->AdoDb || !$this->AdoDb->$connFunc(@$this->connParameters['HOST'], $this->connParameters['USER'], @$this->connParameters['PASS'], $this->connParameters['BASE']))
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_DATABASE_CONNECTION_FAILED'), E_USER_ERROR, __FILE__, __LINE__);
		$this->AdoDb->raiseErrorFn = 'dbErrorHandler';
		// inicialização das outras propriedades da classe
		$this->affectedRows = 0;		
		$this->connected = ($this->AdoDb->_connectionID !== FALSE);
		$this->makeCache = FALSE;
		// executa o callback after connect
		if (isset($this->aConnect))
			$this->aConnect->invoke($this);
		// registra o destrutor do objeto
		parent::registerDestructor($this, '_Db');
	}

	//!-----------------------------------------------------------------
	// @function	Db::_Db
	// @desc		Destrutor do objeto Db
	// @access		public
	// @return		void
	// @note		Este método será executado automaticamente pelo PHP2Go
	//				ao término do script que contém a instância do objeto
	//!-----------------------------------------------------------------
  	function _Db() {  		
    	$this->close();
  	}
  	
  	//!-----------------------------------------------------------------
  	// @function	Db::&getInstance
  	// @desc		Método estático que armazena instâncias únicas de diferentes conexões a banco de dados
  	// @access		public
  	// @param		id string	"NULL" ID da conexão desejada
  	// @return		Db object Instância da classe Db
	// @note		É recomendável que todo e qualquer acesso a uma conexão a banco de dados 
	//				se inicie por uma chamada a este método, garantindo economia de recursos 
	//				tanto na aplicação quanto no SGBD
	// @static  	
  	//!-----------------------------------------------------------------
  	function &getInstance($id=NULL) {
  		static $instances;
  		if (!TypeUtils::isNull($id)) {
  			$key = $id;
  		} else {
  			$Conf =& Conf::getInstance();
  			$default = $Conf->getConfig('DATABASE.DEFAULT_CONNECTION');
  			if (!empty($default)) {
  				$key = $default;
  			} else {
  				$connections = $Conf->getConfig('DATABASE.CONNECTIONS');
  				if (TypeUtils::isArray($connections)) {
					reset($connections);
  					list($key, $value) = each($connections);  					
  				} else {
  					$key = 'DEFAULT';
  				}		
  			}
  		}
  		if (!isset($instances))
  			$instances = array();
  		if (!isset($instances[$key]))
  			$instances[$key] = new Db($id);
  		return $instances[$key];
  	}
  	
	//!-----------------------------------------------------------------
	// @function	Db::setCache
	// @desc		Configura o objeto de banco de dados para utilizar
	//				ou não cache em consultas, comandos DML e métodos
	//				que buscam resultados
	// @access		public
	// @param		flag bool		Novo valor para configuração de uso de cache
	// @param		seconds int		Número de segundos de durabilidade da cache realizada
	// @return		void	
	//!-----------------------------------------------------------------
	function setCache($flag, $seconds=0) {
		$flag = TypeUtils::toBoolean($flag);
		$seconds = TypeUtils::parseIntegerPositive($seconds);
		if ($flag && $seconds > 0) {
			$this->makeCache = TRUE;
			$this->cacheSecs = $seconds;
		} else {
			$this->makeCache = FALSE;
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	Db::setDebug
	// @desc		Habilita ou desabilita debug na conexão com o banco de dados
	// @access		public
	// @param		setting bool	Valor para o flag de debug
	// @return		void
	//!-----------------------------------------------------------------
	function setDebug($setting=TRUE) {
		$this->AdoDb->debug = ($setting ? 1 : 0);
	}
	
	//!-----------------------------------------------------------------
	// @function	Db::setErrorHandler
	// @desc		Configura a função de tratamento de erros no banco de dados
	// @access		public
	// @param		errorHandler mixed	Nome da função
	// @return		string Nome do tratador de erros configurado antes da execução deste método
	// @note		Utilize o valor FALSE para desabilitar o tratamento de erros de 
	//				banco de dados do PHP2Go
	//!-----------------------------------------------------------------
	function setErrorHandler($errorHandler) {
		$oldErrorHandler = $this->AdoDb->raiseErrorFn;
		$this->AdoDb->raiseErrorFn = $errorHandler;
		return $oldErrorHandler;
	}

	//!-----------------------------------------------------------------
	// @function	Db::setFetchMode
	// @desc		Configura o modo de construção dos resultados de uma consulta
	// @param		mode int	Modo a ser utilizado
	// @return		int Valor antigo da propriedade $fetchMode da conexão
	// @note		Os possíveis valores são 0: padrão definido na biblioteca AdoDb, 
	//				1: numérico, 2: associativo e 3: ambos
	//!-----------------------------------------------------------------
	function setFetchMode($mode) {
		return $this->AdoDb->SetFetchMode($mode);
	}
	
	//!-----------------------------------------------------------------
	// @function	Db::affectedRows
	// @desc		Retorna o número de linhas retornadas da consulta
	//				ou o número de linhas afetadas pelo comando DML
	// @access		public
	// @return		int Número de linhas da consulta ou afetadas por DML
	//!-----------------------------------------------------------------
	function affectedRows() {
		return $this->affectedRows;
	}

	//!-----------------------------------------------------------------
	// @function	Db::lastInsertId
	// @desc		Retorna o último código AUTONUMBER gerado pelo banco de dados
	// @access		public
	// @return		int O último código gerado ou FALSE se não suportado pelo tipo de banco utilizado
	//!-----------------------------------------------------------------
	function lastInsertId() {
		return ($this->AdoDb->hasInsertID ? $this->AdoDb->Insert_ID() : 0);
	}

  	//!-----------------------------------------------------------------
  	// @function	Db::getConnectionId
  	// @desc		Retorna o handle da conexão ativa
  	// @access		public
  	// @return		resource Handle da conexão ativa ou NULL se não existir
  	//!-----------------------------------------------------------------
  	function getConnectionId() {
  		return ($this->connected ? $this->AdoDb->_connectionID : NULL);
  	}
  	
  	//!-----------------------------------------------------------------
  	// @function	Db::getDatabaseType
  	// @desc		Retorna o nome do driver associado a esta conexão
  	// @access		public
  	// @return		string Nome do driver
  	//!-----------------------------------------------------------------
  	function getDatabaseType() {
  		return $this->AdoDb->databaseType;
  	}
  	
  	//!-----------------------------------------------------------------
  	// @function	Db::getServerInfo
  	// @desc		Busca as informações sobre o servidor de banco de
  	//				dados da conexão ativa
  	// @access		public
  	// @return		array Vetor de informações
  	// @note		Para uma documentação mais detalhada das informações
  	//				retornadas, consulte a documentação da biblioteca ADODb
  	//!-----------------------------------------------------------------
  	function getServerInfo() {
  		return ($this->connected ? $this->AdoDb->ServerInfo() : NULL);
  	}
  	
	//!-----------------------------------------------------------------
	// @function	Db::getError
	// @desc		Verifica se existe uma mensagem de erro armazenada
	// @access		public
	// @return		string A última mensagem de erro armazenada ou FALSE se não existir
	//!-----------------------------------------------------------------
	function getError() {
		if (!TypeUtils::isNull($this->AdoDb->ErrorNo())) {
			return $this->AdoDb->ErrorMsg();
		} else {
			return FALSE;
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	Db::getErrorCode
	// @desc		Busca o código de erro do banco de dados
	// @access		public
	// @return		int Código de erro do banco de dados, ou NULL se não existir
	//!-----------------------------------------------------------------
	function getErrorCode() {
		return $this->AdoDb->ErrorNo();
	}

	//!-----------------------------------------------------------------
	// @function	Db::getDatabases
	// @desc		Retorna as bases de dados existentes no banco de dados
	// @access		public
	// @return		array Vetor contendo as bases de dados encontradas
	// @see			Db::getTables
	//!-----------------------------------------------------------------
	function getDatabases() {
		return $this->AdoDb->MetaDatabases();
	}

	//!-----------------------------------------------------------------
	// @function	Db::getTables
	// @desc		Retorna as tabelas existentes na base de dados atual
	// @access		public
	// @param		tableType string	'TABLE' lista apenas tabelas, 'VIEW' lista apenas views
	// @return		array Vetor contendo as tabelas encontradas
	// @see			Db::getDatabases
	//!-----------------------------------------------------------------
	function getTables($tableType=FALSE) {
		return $this->AdoDb->MetaTables($tableType);
	}

	//!-----------------------------------------------------------------
	// @function	Db::getColumns
	// @desc		Retorna a lista de colunas de uma tabela ou view, onde
	//				cada elemento da lista é uma instância de ADOFieldObject
	// @access		public
	// @param		table string		Nome da tabela ou view
	// @return		array Vetor contendo os objetos das colunas da tabela
	// @see			Db::getColumnNames
	//!-----------------------------------------------------------------
	function getColumns($table) {
		return $this->AdoDb->MetaColumns($table);
	}

	//!-----------------------------------------------------------------
	// @function	Db::getColumnNames
	// @desc		Busca os nomes das colunas de uma tabela ou view
	// @access		public
	// @param		table string		Nome da tabela ou view
	// @return		array Vetor contendo os nomes das colunas da tabela
	// @see			Db::getColumns
	//!-----------------------------------------------------------------
	function getColumnNames($table) {
		return $this->AdoDb->MetaColumnNames($table);
	}

	//!-----------------------------------------------------------------
	// @function	Db::getPrimaryKeys
	// @desc		Busca os nomes das chaves primárias da tabela $table
	// @access		public
	// @param		table string		Nome da tabela
	// @return		array Vetor contendo os nomes das chaves primárias
	//!-----------------------------------------------------------------
	function getPrimaryKeys($table) {
		return $this->AdoDb->MetaPrimaryKeys($table);
	}
	
	//!-----------------------------------------------------------------
	// @function	Db::getIndexes
	// @desc		Busca os nomes dos índices definidos para uma tabela
	// @access		public
	// @param		table string		Nome da tabela
	// @return		array Vetor contendo os nomes dos índices da tabela	
	//!-----------------------------------------------------------------
	function getIndexes($table) {
		return $this->AdoDb->MetaIndexes($table);
	}
	
	//!-----------------------------------------------------------------
	// @function	Db::getProcedureSQL
	// @desc		Monta o SQL para a execução de uma procedure no banco de dados.
	//				Possui implementações diferentes dependendo do banco utilizado
	// @access		public
	// @param		stmt string		SQL da procedure
	// @param		prepare bool	"FALSE" Retornar um statement preparado ou somente a string SQL
	// @return		mixed String SQL ou o array do statement preparado
	//!-----------------------------------------------------------------
	function getProcedureSQL($stmt, $prepare=FALSE) {
		switch ($this->AdoDb->dataProvider) {
			// oci8, oci805, ocipo
			case 'oci8' :
				$stmt = "begin {$stmt}; end;";
				break;
			// db2
			case 'db2' :
			// mysqli
			case 'mysqli' :
				$stmt = "call {$stmt};";
				break;
			// sybase, sybase_ase
			case 'sybase' :
				$stmt = "exec {$stmt}";
				break;
			// @todo suportar outros formatos de execução de procedure
			default :
				break;
		}
		if ($prepare)
			return $this->prepare($stmt, TRUE);
		else
			return $stmt;
	}

  	//!-----------------------------------------------------------------
  	// @function	Db::getNextId
  	// @desc		Busca o próximo valor de uma seqüência, para preenchimento de
  	//				chaves primárias nas inserções de dados
  	// @access		public
  	// @param		seqName string		"p2gseq" Nome da seqüência
  	// @param		startId int			"1" ID inicial, caso a seqüência não exista
  	// @return		int Próximo valor da seqüência indicada
  	//!-----------------------------------------------------------------
  	function getNextId($seqName='p2gseq', $startId=1) {
  		return ($this->connected ? $this->AdoDb->GenID($seqName, $startId) : 0);
  	}

	//!-----------------------------------------------------------------
	// @function	Db::getFirstCell
	// @desc		Executa o comando SQL indicado pela variável $sql,
	//				buscando apenas a primeira célula do result set resultante
	// @access		public
	// @param		stmt mixed			Comando SQL ou statement preparado
	// @param		bindVars mixed		"FALSE" Variáveis de bind a serem utilizadas
	// @return		string Valor da primeira célula do result set ou FALSE se ocorrer algum erro
	// @note		Se a cache estiver habilitada no objeto, busca o result set correspondente
	//				à consulta SQL na cache
	// @see			Db::getFirstRow
	// @see			Db::getFirstCol
	// @see			Db::getAll
	//!-----------------------------------------------------------------
	function getFirstCell($stmt, $bindVars=FALSE) {
		if ($this->makeCache)
			return $this->AdoDb->CacheGetOne($this->cacheSecs, $stmt, $bindVars);
		else
			return $this->AdoDb->GetOne($stmt, $bindVars);
	}

	//!-----------------------------------------------------------------
	// @function	Db::getFirstRow
	// @desc		Executa o comando SQL indicado pelo parâmetro $sql,
	//				buscando a primeira linha do result set e ignorando o restante
	// @access		public
	// @param		stmt mixed			Comando SQL ou statement preparado
	// @param		bindVars mixed		"FALSE" Variáveis de bind a serem utilizadas
	// @return		string Vetor unidimensional da primeira linha do result set ou FALSE se ocorrerem erros
	// @note		Se a cache estiver habilitada no objeto, busca o result set correspondente
	//				à consulta SQL na cache
	// @see			Db::getFirstCell
	// @see			Db::getFirstCol
	// @see			Db::getAll
	//!-----------------------------------------------------------------
	function getFirstRow($stmt, $bindVars=FALSE) {
		if ($this->makeCache)
			return $this->AdoDb->CacheGetRow($this->cacheSecs, $stmt, $bindVars);
		else
			return $this->AdoDb->GetRow($stmt, $bindVars);
	}

	//!-----------------------------------------------------------------
	// @function	Db::getFirstCol
	// @desc		Executa o comando SQL indicado pela variável $sql,
	//				buscando a primeira coluna do result set e ignorando o restante
	// @access		public
	// @param		stmt mixed			Comando SQL ou statement preparado
	// @param		bindVars mixed		"FALSE" Variáveis de bind a serem utilizadas
	// @return		array Vetor unidimensional da primeira coluna do result set ou FALSE se ocorrerem erros
	// @note		Se a cache estiver habilitada no objeto, busca o result set correspondente
	//				à consulta SQL na cache
	// @see			Db::getFirstCell
	// @see			Db::getFirstRow
	// @see			Db::getAll
	//!-----------------------------------------------------------------
	function getFirstCol($stmt, $bindVars=FALSE) {
		if ($this->makeCache)
			return $this->AdoDb->CacheGetCol($this->cacheSecs, $stmt, $bindVars);
		else
			return $this->AdoDb->GetCol($stmt, $bindVars);
	}

	//!-----------------------------------------------------------------
	// @function	Db::getAll
	// @desc		Executa o comando SQL indicado pela variável $sql,
	//				retornando todo o conteúdo do result set
	// @access		public
	// @param		stmt mixed			Comando SQL ou statement preparado
	// @param		bindVars mixed		"FALSE" Variáveis de bind a serem utilizadas
	// @return		array Vetor bidimensional do result set ou FALSE se ocorrerem erros
	// @note		Se a cache estiver habilitada no objeto, busca o result set correspondente
	//				à consulta SQL na cache
	// @see			Db::getFirstCell
	// @see			Db::getFirstRow
	// @see			Db::getFirstCol
	//!-----------------------------------------------------------------
	function getAll($stmt, $bindVars=FALSE) {
		if ($this->makeCache)
			return $this->AdoDb->CacheGetAll($this->cacheSecs, $stmt, $bindVars);
		else
			return $this->AdoDb->GetAll($stmt, $bindVars);
	}
	
	//!-----------------------------------------------------------------
	// @function	Db::getCount
	// @desc		Executa o comando SQL a fim de buscar o total de linhas resultante da consulta
	// @access		public
	// @param		stmt mixed			Comando SQL ou statement preparado
	// @param		bindVars mixed		"FALSE" Variáveis de bind a serem utilizadas
	// @return		int Total de linhas resultantes da consulta fornecida
	//!-----------------------------------------------------------------
	function getCount($stmt, $bindVars=FALSE) {
		$count = 0;
		$matches = array();
		$sql = (TypeUtils::isArray($stmt) ? $stmt[0] : $stmt);
		// consultas com DISTINCT ou GROUP BY
		if (preg_match("/^\s*SELECT\s+DISTINCT/is", $sql) || preg_match('/\s+GROUP\s+BY\s+/is',$sql)) {
			if ($this->AdoDb->dataProvider == 'oci8') {
				$rewriteSql = preg_replace('/(\sORDER\s+BY\s.*)/is', '', $sql);
				if (preg_match('#/\\*+.*?\\*\\/#', $sql, $matches))
					$rewriteSql = "SELECT {$matches[0]} COUNT(*) FROM ({$rewriteSql})"; 
				else
					$rewriteSql = "SELECT COUNT(*) FROM ({$rewriteSql})";
			} else if (strncmp($this->AdoDb->databaseType, 'postgres', 8) == 0) {
				$info = $this->getServerInfo();
				if (substr($info['version'],0,3) >= 7.1) {
					$rewriteSql = preg_replace('/(\sORDER\s+BY\s[^)]*)/is','',$sql);
					$rewriteSql = "select COUNT(*) from ($rewriteSql) _ADODB_ALIAS_";
				}
			}
		// outros tipos de consultas: substituir o select (.+) from mais significativo por select count(*) from
		} else {
			$stack = 1;
			$index = -1;
			$sql = eregi_replace("^select", "", trim($sql));
			$words = preg_split("/\s+/", $sql);
			for ($i=0, $size=sizeOf($words); $i<$size; $i++) {
				if (eregi("select", $words[$i]))
					$stack++;
				elseif (eregi("from", $words[$i]))
					$stack--;
				if ($stack == 0) {
					$index = $i;
					break;
				}
			}
			if ($index > -1) {
				$result = array_slice($words, $index);				
				$rewriteSql = "select COUNT(*) " . implode(" ", $result);
				$rewriteSql = preg_replace('/(\sORDER\s+BY\s[^)]*)/is', '', $rewriteSql);
			}			
		}
		// executa a consulta de count se ela for válida
		if (isset($rewriteSql) && $rewriteSql != $sql) {
			if ($this->makeCache)
				$count = $this->AdoDb->CacheGetOne($this->cacheSecs, $rewriteSql, $bindVars);
			else
				$count = $this->AdoDb->GetOne($rewriteSql, $bindVars);
			if ($count !== FALSE)
				return $count;
		}	
		// reescrita da query falhou, a consulta original será utilizada
		if (preg_match('/\s*UNION\s*/is', $sql)) 
			$rewriteSql = $sql;
		else 
			$rewriteSql = preg_replace('/(\sORDER\s+BY\s.*)/is', '', $sql);
		$rs =& $this->AdoDb->Execute($rewriteSql, $bindVars);
		if ($rs) {
			$count = $rs->RecordCount();
			if ($count == -1) {
				while (!$rs->EOF)
					$rs->MoveNext();
				$count = $rs->_currentRow;
			}
			$rs->Close();
			if ($count > -1)
				return $count;
		}
		return 0;
	}
	
	//!-----------------------------------------------------------------
	// @function	Db::startTransaction
	// @desc		Cria uma nova transação no banco de dados
	// @access		public
	// @return		bool
	// @note		Se for executada em um tipo de banco de dados que não
	//				suporta transações, retorna FALSE
	//!-----------------------------------------------------------------
	function startTransaction() {
		return $this->AdoDb->StartTrans();
	}
	
	//!-----------------------------------------------------------------
	// @function	Db::failTransaction
	// @desc		Reporta um erro na execução de um comando de uma transação
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function failTransaction() {
		return $this->AdoDb->FailTrans();
	}
	
	//!-----------------------------------------------------------------
	// @function	Db::hasFailedTransaction
	// @desc		Verifica se a transação ativa falhou
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function hasFailedTransaction() {
		return $this->AdoDb->HasFailedTrans();
	}
	
	//!-----------------------------------------------------------------
	// @function	Db::completeTransaction
	// @desc		Finaliza a transação, verificando os erros e executando
	//				automaticamente a efetivação com commit ou a recuperação
	//				com rollback
	// @access		public
	// @param		forceRollback bool	"FALSE" Forçar a execução de rollback mesmo que não existam erros
	// @return		bool TRUE se a transação foi comitada, ou FALSE em caso contrário
	// @note		Se for executada em um driver que não suporta transações, retorna FALSE
	//!-----------------------------------------------------------------
	function completeTransaction($forceRollback=FALSE) {
		return $this->AdoDb->CompleteTrans(!TypeUtils::toBoolean($forceRollback));
	}

	//!-----------------------------------------------------------------
	// @function	Db::commit
	// @desc		Encerra uma transação com sucesso. Se o parâmetro
	//				$flag for FALSE, executa um rollback na transação
	// @access		public
	// @param		flag bool		"TRUE" Indica se a transação deve ser encerrada com sucesso (TRUE) ou não (FALSE)
	// @return		bool Indica o status da operação realizada
	// @note		Se for executada em um tipo de banco de dados que não
	//				suporta transações, retorna TRUE
	//!-----------------------------------------------------------------
	function commit($flag=TRUE) {
		return $this->AdoDb->CommitTrans(TypeUtils::toBoolean($flag));
	}

	//!-----------------------------------------------------------------
	// @function	Db::rollback
	// @desc		Encerra uma transação desfazendo todas as suas
	//				alterações no estado do banco de dados
	// @access		public
	// @return		bool Indica o status da operação realizada
	// @note		Se for executada em um tipo de banco de dados que não
	//				suporta transações, retorna FALSE
	//!-----------------------------------------------------------------
	function rollback() {
		return $this->AdoDb->RollbackTrans();
	}

	//!-----------------------------------------------------------------
	// @function	Db::prepare
	// @desc		Prepara uma instrução SQL para execução
	// @access		public
	// @param		statement string	Código da instrução a ser preparada
	// @param		cursor bool			"FALSE" Indica se haverá retorno de cursor na instrução executada
	// @return		string Vetor contendo instrução SQL e parâmetros ou a instrução
	//				SQL original se o driver utilizado não suportar a função
	//				Prepare()
	//!-----------------------------------------------------------------
	function prepare($statement, $cursor=FALSE) {
		return $this->AdoDb->Prepare($statement, TypeUtils::toBoolean($cursor));
	}
	
	//!-----------------------------------------------------------------
	// @function	Db::bind
	// @desc		Atribui um valor a uma variável de substituição em um statement criado
	// @access		public
	// @param		statement array		Statement previamente criado com $Db->prepare()
	// @param		&value mixed		Valor para o parâmetro
	// @param		varName string		Nome da variável no statement
	// @param		type mixed			"FALSE" Tipo da variável, depende dos tipos pré-definidos pelo BD
	// @param		maxLen int			"4000" Tamanho máximo para a variável bind
	// @param		isOutput bool		"FALSE" Indica se o parâmetro é IN (FALSE) ou OUT (TRUE)	
	// @return		bool
	//!-----------------------------------------------------------------
	function bind($statement, &$value, $varName, $type=FALSE, $maxLen=4000, $isOutput=FALSE) {
		return $this->AdoDb->Parameter($statement, $value, $varName, $isOutput, $maxLen, $type);
	}
	
	//!-----------------------------------------------------------------
	// @function	Db::quoteString
	// @desc		Insere corretamente haspas em uma string levando em conta
	//				os caracteres de escape
	// @access		public
	// @param		str string			String a ser processada
	// @param		magicQuotes bool	"FALSE" Forneça o retorno da função get_magic_quotes_gpc() 
	//									ou get_magic_quotes_runtime() para levar em conta estes casos
	//									no tratamento de caracteres de escape
	// @return		string String processada
	//!-----------------------------------------------------------------
	function quoteString($str, $magicQuotes=FALSE) {		
		return $this->AdoDb->qstr($str, $magicQuotes);
	}
	
	//!-----------------------------------------------------------------
	// @function	Db::date
	// @desc		Permite transformar uma data ou timestamp para o formato
	//				de data do banco de dados, incluindo os quotes
	// @access		public
	// @param		date mixed Data em formato string (EURO, SQL ou US) ou unix timestamp
	// @param		time bool	"FALSE" Se for TRUE, monta uma data, do contrário monta um datetime (timestamp)
	// @return		string Data formatada de acordo com os padrões da conexão ativa	
	//!-----------------------------------------------------------------
	function date($date=NULL, $time=FALSE) {
		if (empty($date)) {
			return ($time ? $this->AdoDb->sysTimeStamp : $this->AdoDb->sysDate);
		} else {
			if (!TypeUtils::isInteger($date))
				$date = Date::fromEuroToSqlDate(Date::fromUsToEuroDate($date, $time), $time);
			if ($time)
				return $this->AdoDb->DBTimeStamp($date);
			else
				return $this->AdoDb->DBDate($date);			
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	Db::&execute
	// @desc		Executa um statement na conexão com o banco de dados
	// @access		public
	// @param		statement mixed		Vetor com dados do statement ou instrução SQL a ser executada	
	// @param		bindVars mixed		"FALSE" Variáveis de bind a serem utilizadas
	// @param		cursorName string	"NULL" Nome do cursor dentro do código do statement (apenas para oci8)
	// @return		ADORecordset object Result Set ou FALSE em caso de erros ou resultado vazio
	// @note		Se a cache estiver habilitada no objeto, busca o result set correspondente
	//				na cache	
	//!-----------------------------------------------------------------
	function &execute($statement, $bindVars=FALSE, $cursorName=NULL) {
		if (!TypeUtils::isNull($cursorName) && $this->AdoDb->dataProvider == 'oci8')
			$rs =& $this->AdoDb->ExecuteCursor($statement, $cursorName, $bindVars);
		elseif ($this->makeCache)
			$rs =& $this->AdoDb->CacheExecute($this->cacheSecs, $statement, $bindVars);
		else
			$rs =& $this->AdoDb->Execute($statement, $bindVars);
		if ($rs) {
			$this->affectedRows = ($rs->EOF ? 0 : $rs->RecordCount());
			return $rs;
		} else {
			$this->affectedRows = 0;
			return FALSE;
		}
	}

	//!-----------------------------------------------------------------
	// @function	Db::&query
	// @desc		Executa uma query na conexão com o banco de dados
	// @access		public
	// @param		sqlCode string	Código SQL a ser executado
	// @param		execute	bool		"TRUE" Flag para executar ou exibir o código
	// @param		bindVars  mixed		"FALSE" Variáveis de bind a serem aplicadas no código SQL
	// @return		ADORecordset object Result set se a consulta puder ser executada ou FALSE em caso de erros
	// @note		Se a cache estiver habilitada no objeto, busca o result set correspondente
	//				à consulta SQL na cache
	// @see			Db::limitQuery
	//!-----------------------------------------------------------------
	function &query($sqlCode, $execute=TRUE, $bindVars=FALSE) {
		if ($execute) {
			if ($this->makeCache)
				$rs =& $this->AdoDb->CacheExecute($this->cacheSecs, $sqlCode, $bindVars);
			else
				$rs =& $this->AdoDb->Execute($sqlCode, $bindVars);
			if ($rs) {
				$this->affectedRows = $rs->RecordCount();
				return $rs;
			} else {
				$this->affectedRows = 0;
				return $this->emptyRecordSet();
			}
		} else {
			if (TypeUtils::isArray($sqlCode))
				echo $sqlCode[0], "<BR>";
			else
				echo $sqlCode, "<BR>";
			return TRUE;
		}
	}

	//!-----------------------------------------------------------------
	// @function	Db::&limitQuery
	// @desc		Executa uma query com limite no banco de dados
	// @access		public
	// @param		sqlCode string	Código da consulta SQL
	// @param		offset int			"-1" Número de linhas requerido, omita para buscar todas a partir de $lowerBound
	// @param		lowerBound int		"0" Limite inferior requerido, omita para buscar todas até $offset
	// @param		execute bool		"TRUE" Flag para executar ou exibir o código
	// @param		bindVars array		"FALSE" Vetor opcional de variáveis bind a serem aplicadas no código SQL
	// @return		ADORecordset object Result set se a consulta puder ser executada
	// @note		Se a cache estiver habilitada no objeto, busca o result set correspondente
	//				à consulta SQL na cache
	// @see			Db::query
	//!-----------------------------------------------------------------
	function &limitQuery($sqlCode, $offset=-1, $lowerBound=0, $execute=TRUE, $bindVars=FALSE) {
		if ($lowerBound < 0) {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_MUST_BE_POSITIVE', array("\$lowerBound", "limitQuery")), E_USER_WARNING, __FILE__, __LINE__);
			$lowerBound = 0;
		}
		if ($execute) {
			if ($this->makeCache)
				$rs =& $this->AdoDb->CacheSelectLimit($this->cacheSecs, $sqlCode, $offset, $lowerBound, $bindVars);
			else
				$rs =& $this->AdoDb->SelectLimit($sqlCode, $offset, $lowerBound, $bindVars);
			if ($rs) {
				$this->affectedRows = $rs->RecordCount();
				return $rs;
			} else {
				$this->affectedRows = 0;
				return $this->emptyRecordSet();
			}
		} else {
			if (TypeUtils::isArray($sqlCode))
				echo $sqlCode[0], "<BR>";
			else
				echo $sqlCode, "<BR>";
			return TRUE;
		}
	}

	//!-----------------------------------------------------------------
	// @function	Db::replace
	// @desc		Busca por registros na tabela $table que satisfaçam
	//				o(s) valor(s) da(s) chave(s) indicadas em $keyFields
	//				cujos valores devem estar em $arrFields. Atualiza
	//				o(s) registro(s) se forem encontrados ou insere um
	//				registro novo em caso contrário
	// @access		public
	// @param		table string		Nome da tabela a ser atualizada/incrementada
	// @param		arrFields array		Array associativo de valores para o novo registro ou
	//									atualização dos registros existentes
	// @param		keyFields mixed		Chave simples em um string ou chave composta em um array
	// @param		quoteVals bool		"FALSE" Quotar os valores não numéricos automaticamente
	//									nos comandos DML executados
	// @return		int 0 em caso de falha, 1 se a atualização foi efetuada e
	//				2 se a inserção foi efetuada
	//!-----------------------------------------------------------------
	function replace($table, $arrFields, $keyFields, $quoteVals=FALSE) {
		return $this->AdoDb->Replace($table, $arrFields, $keyFields, $quoteVals);
	}

	//!-----------------------------------------------------------------
	// @function	Db::insert
	// @desc		Constrói e executa um comando DML 'INSERT'
	// @access		public
	// @param		table string		Nome da tabela ou view
	// @param		arrData array		Array associativo com os dados
	// @return		mixed Se o banco suportar, retorna o último ID inserido. Do
	//				contrário, retorna um valor booleano representando o sucesso
	//				ou a falha da operação de inserção
	// @see			Db::update
	// @see			Db::delete
	//!-----------------------------------------------------------------
	function insert($table, $arrData) {
		if (empty($table))
			return FALSE;
		$rs =& $this->AdoDb->Execute(sprintf("SELECT * FROM %s WHERE 1=0", $table));
		if ($rs && TypeUtils::isHashArray($arrData)) {
			$insertSQL = $this->AdoDb->GetInsertSQL($rs, $arrData);
			if (!empty($insertSQL)) {
        		$result = $this->AdoDb->Execute($insertSQL);
				if ($result) {
					// apenas para manter populada esta propriedade
					$this->affectedRows = $this->AdoDb->Affected_Rows();
					// retorna o último ID inserido apenas se ele for não-zero
					$insertId = $this->lastInsertId();
					return ($insertId ? $insertId : TRUE);
				} else {
					$this->affectedRows = 0;
					return FALSE;
				}
			}
		}
		return FALSE;
	}

	//!-----------------------------------------------------------------
	// @function	Db::update
	// @desc		Constrói e executa um comando DML 'UPDATE'
	// @access		public
	// @param		table string		Nome da tabela ou view
	// @param		arrData array		Array associativo de valores a serem alterados
	// @param		clause string		Cláusula de condição
	// @param		force bool			"FALSE" Forçar a execução do update mesmo quando não existirem campos a serem atualizados
	// @return		bool
	// @note		Se não for informada uma cláusula de condição para o 
	//				comando UPDATE, este método retornará FALSE
	// @see			Db::insert
	// @see			Db::delete
	//!-----------------------------------------------------------------
	function update($table, $arrData, $clause, $force=FALSE) {
		if (empty($table) || empty($clause))
			return FALSE;
		$rs =& $this->AdoDb->Execute(sprintf("SELECT * FROM %s WHERE %s", $table, $clause));
		if ($rs && TypeUtils::isHashArray($arrData)) {
			$updateSQL = $this->AdoDb->GetUpdateSQL($rs, $arrData, $force);
			if (!empty($updateSQL)) {
				$result = $this->AdoDb->Execute($updateSQL);
				$this->affectedRows = $this->AdoDb->Affected_Rows();
				return ($result ? TRUE : FALSE);
			}				
			return TRUE;
		}
		return FALSE;
	}

	//!-----------------------------------------------------------------
	// @function	Db::delete
	// @desc		Constrói e executa um comando DML 'DELETE'
	// @access		public
	// @param		table string		Nome da tabela ou view
	// @param		clause string		Cláusula de condição
	// @return		bool
	// @note		Se não for informada uma cláusula de condição para o 
	//				comando DELETE, este método retornará FALSE	
	// @see			Db::insert
	// @see			Db::update
	//!-----------------------------------------------------------------
	function delete($table, $clause) {
		if (empty($table) || empty($clause))
			return FALSE;
		$result = $this->AdoDb->Execute(sprintf("DELETE FROM %s WHERE %s", $table, $clause));
		$this->affectedRows = $this->AdoDb->Affected_Rows();
		return ($result ? TRUE : FALSE);
	}
	
	//!-----------------------------------------------------------------
	// @function	Db::&emptyRecordSet
	// @desc		Retorna um result set vazio em consultas e operações
	//				que retornam erros ou não retornam resultados no BD
	// @access		public
	// @return		ADORecordSet_empty object
	// @note		Ao executar uma query ou uma operação no BD, tanto o
	//				teste $Db->affectedRows() quanto o $Rs->RecordCount()
	//				deverão retornar zero. Porém, possíveis erros poderão
	//				ser encontrados em $Db->getError()
	//!-----------------------------------------------------------------
	function &emptyRecordSet() {
		return new ADORecordSet_empty();
	}	

	//!-----------------------------------------------------------------
	// @function	Db::isDbDesign
	// @desc		Verifica se uma query possui palavras reservadas
	//				indicativas de um comando DML ou DDL
	// @access		public
	// @param		sql string	Código SQL
	// @return		bool
	//!-----------------------------------------------------------------
	function isDbDesign($sql) {
		if (TypeUtils::isArray($sql)) 
			$sql = $sql[0];
		$resWords = 'INSERT|UPDATE|DELETE|' . 'REPLACE|CREATE|DROP|' .
					'ALTER|GRANT|REVOKE|' . 'LOCK|UNLOCK';
		if (preg_match('/^\s*"?(' . $resWords . ')\s+/i', $sql)) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	//!-----------------------------------------------------------------
	// @function	Db::isDbQuery
	// @desc		Verifica se uma query possui a palavra SELECT,
	//				indicativa de um comando DQL
	// @access		public
	// @param		sql string	Código SQL
	// @return		bool
	//!-----------------------------------------------------------------
	function isDbQuery($sql) {
		if (TypeUtils::isArray($sql)) 
			$sql = $sql[0];
		$resWord = 'SELECT';
		if (preg_match('/^\s*"?(' . $resWord . ')\s+/i', $sql)) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	//!-----------------------------------------------------------------
	// @function	Db::checkIntegrity
	// @desc		Verifica a integridade referencial de uma tabela
	//				em uma determinada coluna para as referências do
	//				parâmetro 'reference'
	// @access		public
	// @param		table string		Tabela a ser testada
	// @param		column string		Coluna da tabela acima
	// @param		value mixed		Valor de 'column' sendo testado
	// @param		reference mixed	Vetor 'tabela'=>'coluna' ou tabela simples a ser testada a integridade
	// @return		bool
	//!-----------------------------------------------------------------
	function checkIntegrity($table, $column, $value, $reference) {
		$ok = TRUE;
		if (TypeUtils::isArray($reference)) {
			foreach($reference as $tb => $col) {
				$fields = $table . "." . $column;
				$tables = $table . "," . $tb;
				$clause = $table . "." . $column . " = " . $value . " AND " . $table . "." . $column . " = " . $tb . "." . $col;
				$this->query("SELECT " . $fields . " FROM " . $tables . " WHERE " . $clause);
				$ok = ($this->affectedRows) ? FALSE : $ok;
			}
		} else {
			$fields = $table . "." . $column;
			$tables = $table . "," . $reference;
			$clause = $table . "." . $column . " = " . $value . " AND " . $table . "." . $column . " = " . $reference . "." . $column;
			$this->query("SELECT " . $fields . " FROM " . $tables . " WHERE " . $clause);
			$ok = ($this->affectedRows) ? FALSE : $ok;
		}
		return $ok;
     }

	//!-----------------------------------------------------------------
	// @function	Db::toGlobals
	// @desc		Publica como variáveis globais os valores das
	//				colunas da primeira linha do resultado de 'sqlCode'
	// @access		public
	// @param		sqlCode string			Consulta SQL para publicação das variáveis
	// @param		bindVars array			"FALSE" Variáveis de amarração para a consulta
	// @param		ignoreEmptyResults bool	"TRUE" Não gerar erro para uma consulta SQL que não retorna resultados
	// @return		bool
	//!-----------------------------------------------------------------
	function toGlobals($sqlCode, $bindVars=FALSE, $ignoreEmptyResults=FALSE) {
		// testa a natureza do comando passado por parâmetro
		if (!$this->isDbQuery($sqlCode) || $this->isDbDesign($sqlCode)) {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_TOGLOBALS_WRONG_USAGE'), E_USER_WARNING, __FILE__, __LINE__);
			return FALSE;
		}
		// altera para fetch mode associativo
		if ($this->AdoDb->fetchMode != ADODB_FETCH_ASSOC) {
			$oldFetchMode = $this->AdoDb->fetchMode;
			$this->setFetchMode(ADODB_FETCH_ASSOC);
		}
		// executa a consulta e publica a primeira linha no escopo global
		$rs =& $this->AdoDb->Execute($sqlCode, $bindVars);
		if ($rs->RecordCount() > 0) {
			// retorna ao fetch mode anterior, se existente
			if (isset($oldFetchMode))
				$this->setFetchMode($oldFetchMode);
            $data = $rs->FetchRow();
			foreach ($data as $key => $value) {
				Registry::set($key, $value);
			}
			return TRUE;
		} else if (!$ignoreEmptyResults) {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_EMPTY_TOGLOBALS_QUERY', $sqlCode), E_USER_NOTICE, __FILE__, __LINE__);
			return FALSE;
		}
		return FALSE;
	}

	//!-----------------------------------------------------------------
	// @function	Db::close
	// @desc		Fecha a conexão atual com o banco de dados
	// @access		public
	// @return		bool	Indica o status da operação realizada
	//!-----------------------------------------------------------------
	function close() {
		if (isset($this->AdoDb->_connectionID) && TypeUtils::isResource($this->AdoDb->_connectionID)) {
			if (TypeUtils::isInstanceOf($this->bClose, 'callback'))
				$this->bClose->invoke($this);
			$this->connected = $this->AdoDb->Close();
		} else {
			$this->connected = FALSE;
		}
		return ($this->connected === FALSE);
	}
}
?>