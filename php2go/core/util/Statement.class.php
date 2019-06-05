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
// $Header: /www/cvsroot/php2go/core/util/Statement.class.php,v 1.17 2005/09/01 15:17:31 mpont Exp $
// $Date: 2005/09/01 15:17:31 $

//!-----------------------------------------------------------------
// @class 		Statement
// @desc 		Esta classe implementa o processamento de um 'statement',
// 				que pode ser utilizado para pré-definir uma consulta SQL,
// 				(comandos DQL/DML/DDL), um comando PHP a ser executado
// 				ou qualquer outro texto de uso geral com funcionalidade
// 				de definição de variáveis para futura amarração de valor
// @package		php2go.util
// @extends 	PHP2Go
// @author 		Marcos Pont
// @version		$Revision: 1.17 $
// @note 		As variáveis devem ser declaradas no statement no formato
// 				~variavel~. Os tipos de dado aceitos até a presente versão para
// 				uma variável são valores escalares e elementos de vetores
// 				indexados ou associativos
// @note		Também é possível utilizar código PHP *dentro do valor das variáveis*,
//				utilizando o padrão ~#trecho de código#~
//!-----------------------------------------------------------------
class Statement extends PHP2Go
{
	var $statement; 				// @var statement string			Conteúdo do statement fornecido no construtor do objeto
	var $prefix;					// @var prefix string				Prefixo do padrão das variáveis dentro do statement
	var $sufix;						// @var sufix string				Sufixo do padrão das variáveis dentro do statement
	var $result; 					// @var result string				Resultado final do statement
	var $defVars = array();			// @var defVars array				"array()" Lista de variáveis do statement capturada na criação da instância
	var $bindVars = array();		// @var bindVars array				"array()" Vetor associativo de variáveis definidas e seus valores
	var $showUnassigned = FALSE; 	// @var showUnassigned bool			"FALSE" Flag para exibição no resultado das variáveis não setadas
	var $prepared = FALSE;			// @var prepared bool				"FALSE" Flag que indica se o statement foi preparado

	//!-----------------------------------------------------------------
	// @function	Statement::Statement
	// @desc 		Construtor da classe. Inicializa a lista de variáveis definidas
	//				e o vetor de amarração. Inicializa o prefixo e o sufixo com o
	//				padrão da classe: '~'
	// @access 		public
	// @param 		stCode string		"" Código do Statement a ser inicializado, opcional para inicialização
	//!-----------------------------------------------------------------
	function Statement($stCode = '') {
		PHP2Go::PHP2Go();
		$this->statement = $stCode;
		$this->result = $stCode;
		$this->prefix = '~';
		$this->sufix = '~';
		if (!empty($this->statement))
			$this->_prepareStatement();
	}
	
	//!-----------------------------------------------------------------
	// @function	Statement::_Statement
	// @desc		Destrutor da classe
	// @access		public
	// @return		void	
	//!-----------------------------------------------------------------
	function _Statement() {
		unset($this);
	}
	
	//!-----------------------------------------------------------------
	// @function	Statement::evaluate
	// @desc		Método estático de avaliação de um statement utilizando
	//				apenas variáveis do escopo global, dos objetos de sessão
	//				e do Registry
	// @access		public
	// @param		value string		Código do statement
	// @param		prefix string		"~" Prefixo para variáveis
	// @param		suffix string		"~" Sufixo para variáveis
	// @param		showUnassigned bool	"TRUE" Exibir ou não variáveis não atribuídas
	// @return		string Código resultante
	// @static
	//!-----------------------------------------------------------------
	function evaluate($value, $prefix='~', $suffix='~', $showUnassigned=TRUE) {
		static $Stmt;
		if (!isset($Stmt))
			$Stmt =& new Statement();
		$Stmt->setVariablePattern($prefix, $suffix);
		$Stmt->setStatement($value);		
		$Stmt->setShowUnassigned($showUnassigned);
		$Stmt->bindVariables(FALSE);
		return $Stmt->getResult();		
	}

	//!-----------------------------------------------------------------
	// @function	Statement::setStatement
	// @desc		Atribui um valor ao statement
	// @access		public
	// @param		stCode string		Código do Statement
	// @return		void	
	//!-----------------------------------------------------------------
	function setStatement($stCode) {
    	$this->statement = $stCode;
		$this->result = $stCode;
		$this->_prepareStatement();
	}
	
	//!-----------------------------------------------------------------
	// @function	Statement::setVariablePattern
	// @desc		Configura o padrão de reconhecimento de variáveis dentro de um statement
	// @access		public
	// @param		prefix string		"" Prefixo
	// @param		sufix string		"" Sufixo
	// @return		void	
	// @note		Ao menos um dos dois parâmetros deve ser não vazio
	// @note		O padrão da classe para prefixo e sufixo é o caractere '~' em ambos
	//!-----------------------------------------------------------------
	function setVariablePattern($prefix='', $sufix='') {
		if (TypeUtils::isString($prefix) && TypeUtils::isString($sufix) && ($prefix != '' || $sufix != '')) {
			$this->prefix = $prefix;
			$this->sufix = $sufix;
		}
	}

	//!-----------------------------------------------------------------
	// @function	Statement::loadFromFile
	// @desc		Carrega o código do statement a partir de um arquivo
	// @access		public
	// @param		fileName string	Nome do arquivo
	// @return		void	
	//!-----------------------------------------------------------------
	function loadFromFile($fileName) {
		$fp = @fopen($fileName, 'r');
		if ($fp !== FALSE) {
			$this->setStatement(fread($fp, filesize($fileName)));
			fclose($fp);
		} else {
        	PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CANT_READ_FILE', $fileName), E_USER_ERROR, __FILE__, __LINE__);
		}
	}

	//!-----------------------------------------------------------------
	// @function	Statement::setShowUnassigned
	// @desc 		Habilita ou desabilita a exibição de variáveis sem valor
	// 				atribuído no statement
	// @access 		public
	// @param 		flag bool	"TRUE" Indica habilitação ou desabilitação
	// @return		void	
	//!-----------------------------------------------------------------
	function setShowUnassigned($flag=TRUE) {
		$this->showUnassigned = TypeUtils::toBoolean($flag);
	}

	//!-----------------------------------------------------------------
	// @function 	Statement::getDefinedVars
	// @desc 		Busca as variáveis definidas no statement
	// @access 		public
	// @return 		array Vetor contendo as variáveis definidas
	//!-----------------------------------------------------------------
	function getDefinedVars() {
		return $this->defVars;
	}
	
	//!-----------------------------------------------------------------
	// @function	Statement::isEmpty
	// @desc		Verifica se o statement possui variáveis de substituição definidas
	// @access		public
	// @return		bool
	// @note		Retorna FALSE se o statement não foi preparado
	//!-----------------------------------------------------------------
	function isEmpty() {
		return ($this->prepared && empty($this->defVars));
	}

	//!-----------------------------------------------------------------
	// @function	Statement::isDefinedVariable
	// @desc		Verifica se uma variável está definida no statement
	// @access		public
	// @param		variable string	Nome da variável
	// @return		bool
	//!-----------------------------------------------------------------
	function isDefined($variable) {
		return ($this->_findVariable($variable) !== FALSE);
	}
	
	//!-----------------------------------------------------------------
	// @function	Statement::isBound
	// @desc		Verifica se uma determinada variável já possui valor atribuído
	// @access		public
	// @param		variable string	Nome da variável
	// @return		bool
	//!-----------------------------------------------------------------
	function isBound($variable) {
		return isset($this->bindVars[$variable]);
	}
	
	//!-----------------------------------------------------------------
	// @function	Statement::isAllBound
	// @desc		Verifica se todas as variáveis declaradas possuem valor atribuído
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function isAllBound() {
		if ($this->isEmpty()) {		
			return TRUE;
		} else {
			for ($i=0, $size=sizeof($this->defVars); $i<$size; $i++) {
				$value = $this->defVars[$i];
				$searchValue = (TypeUtils::isArray($value) ? $value[0] : $value);
				if (!array_key_exists($searchValue, $this->bindVars))
					return FALSE;
			}
			return TRUE;
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	Statement::bindByName
	// @desc 		Atribui valor a uma variável do statement
	// @param 		variable string	Nome da variável
	// @param 		value mixed		Valor a ser atribuído à variável
	// @note		O nome da variável deve ser referenciado exatamente como foi definido entre os delimitadores	
	// @access 		public	
	// @return 		bool
	// @see			Statement::appendByName
	// @see 		Statement::bindAllFromGlobals
	// @see 		Statement::bindList
	//!-----------------------------------------------------------------
	function bindByName($variable, $value, $quoteStrings=TRUE) {
		$affected = FALSE;
		if (empty($this->defVars))
        	return $affected;
        $valueToBind = ($quoteStrings && TypeUtils::isString($value) ? "\"" . $value . "\"" : $value);
		if ($this->isDefined($variable)) {
			$this->bindVars[$variable] = $valueToBind;
			$affected = TRUE;			
		}
	}

	//!-----------------------------------------------------------------
	// @function	Statement::appendByName
	// @desc 		Concatena um valor em uma variável do statement
	// @param 		variable string	Nome da variável
	// @param 		value mixed		Valor a ser concatenado
	// @access 		public	
	// @return 		bool
	// @see			Statement::bindByName
	// @see 		Statement::bindAllFromGlobals
	// @see 		Statement::bindList
	//!-----------------------------------------------------------------
	function appendByName($variable, $value, $quoteStrings=TRUE) {
		$affected = FALSE;
		if (empty($this->defVars))
        	return $affected;
		$valueToAppend = ($quoteStrings && TypeUtils::isString($value) ? "\"" . $value . "\"" : $value);
		if ($this->isDefined($variable)) {
        	if (isset($this->bindVars[$variable]))
				$this->bindVars[$variable] .= $valueToAppend;
			else
				$this->bindVars[$variable] = $valueToAppend;
			$affected = TRUE;			
		}
		return $affected;        	
	}

	//!-----------------------------------------------------------------
	// @function	Statement::bindVariables
	// @desc 		Este método busca atribuir valores a todas as variáveis
	// 				declaradas utilizando as variáveis registradas nos repositórios
	//				da requisição: reg, obj, env, get, post, cookie e session
	// @access 		public
	// @param		quoteStrings bool	"TRUE" Indica que as strings devem ser quotadas
	// @param		searchOrder string	"ROEGPCS" Ordem de busca das variáveis nos repositórios	
	// @return 		int Número de variáveis atribuídas com sucesso
	// @see 		Statement::bindByName
	// @see 		Statement::bindList
	//!-----------------------------------------------------------------
	function bindVariables($quoteStrings=TRUE) {
		if ($this->isEmpty()) {
			return 0;
		} else {
			$affected = 0;
			$size = sizeof($this->defVars);
			for ($i=0; $i<$size; $i++) {
				$value = $this->defVars[$i];
				$bindVal = NULL;
				if (TypeUtils::isArray($value)) {
					$bindKey = $value[0];
					$temp = HttpRequest::getVar($value[1]);
					if (TypeUtils::isArray($temp) && array_key_exists($value[2], $temp))
						$bindVal = $temp[$value[2]];						
				} else {
					$bindKey = $value;
					if ($bindKey{0} == '#' && $bindKey{strlen($bindKey)-1} == '#') {
						ob_start();
						eval("\$bindVal = " . substr($value, 1, -1) . ";");
						$err = ob_get_clean();
						if ($err != '')
							PHP2Go::raiseError(PHP2Go::getLangVal('ERR_STATEMENT_EVAL', substr($bindKey, 1, -1)), E_USER_ERROR, __FILE__, __LINE__);
					} else {
						$bindVal = HttpRequest::getVar($value);
					}
				}
				if (!TypeUtils::isNull($bindVal, TRUE)) {
					$affected++;
					$this->bindVars[$bindKey] = ($quoteStrings && TypeUtils::isString($bindVal)) ? "\"" . $bindVal . "\"" : $bindVal;
				}
			}
			return $affected;
		}
	}

	//!-----------------------------------------------------------------
	// @function 	Statement::bindList
	// @desc 		Atribui uma lista de valores às variáveis definidas
	// 				levando em consideração a ordem em que estão declaradas
	// @access 		public
	// @return 		int O número de valores atribuídos com sucesso
	// @see 		Statement::bindByName
	// @see 		Statement::bindAllFromGlobals
	// @note 		Recebe n valores como parâmetro e tenta atribui-los a n
	// 				variáveis declaradas da esquerda para a direita no statement
	//!-----------------------------------------------------------------
	function bindList() {
		$arguments = func_get_args();
		$argn = func_num_args();
		$affected = 0;
		if ($this->isEmpty())
        	return $affected;
		$size = sizeof($this->defVars);
		for ($i = 0; $i < $argn; $i++) {
			if ($i < $size) {
				$this->defVars[$i] = $arguments[$i];
				$affected++;
			}
		}
		return $affected;
	}

	//!-----------------------------------------------------------------
	// @function	Statement::getResult
	// @desc 		Retorna o resultado do statement, com atribuição de
	// 				valores às variáveis
	// @access 		public
	// @return		string Resultado do statement, com variáveis de substituição atribuídas
	// @see 		Statement::setShowUnassigned
	// @note 		De acordo com o valor da propriedade showUnassigned,
	// 				variáveis sem valor atribuído serão exibidas ou não
	//!-----------------------------------------------------------------
	function getResult() {
		reset($this->defVars);
		$this->result = $this->statement;
		for ($i=0,$size=sizeof($this->defVars); $i<$size; $i++) {
			$value = $this->defVars[$i];
			if (!TypeUtils::isNull($value)) {
				if (TypeUtils::isArray($value)) {
					if (isset($this->bindVars[$value[0]])) {
						$source = $this->prefix . $value[1] . "\[\'?" . preg_quote($value[2], '/') . "\'?\]" . $this->sufix;
						$this->result = preg_replace("/{$source}/", $this->bindVars[$value[0]], $this->result, -1);
						continue;
					}
				} else {
					if (isset($this->bindVars[$value])) {
						$source = preg_quote("{$this->prefix}{$value}{$this->sufix}", '/');
						$this->result = preg_replace("/{$source}/", $this->bindVars[$value], $this->result, -1);
						continue;
					}
				}
			} 
			if (!$this->showUnassigned) {
				if (TypeUtils::isArray($value))
					$this->result = preg_replace("/" . $this->prefix . $value[1] . "\[" . addslashes($value[2]) . "\]" . $this->sufix . "/", '', $this->result, -1);
				else
					$this->result = preg_replace("/" . $this->prefix . $value . $this->sufix . "/", '', $this->result, -1);			
			}
		}
		return $this->result;
	}

	//!-----------------------------------------------------------------
	// @function	Statement::displayStatement
	// @desc		Imprime o valor original do statement
	// @access		public
	// @param		preFormatted bool	"TRUE" Inserir tags PRE para preservar a formatação
	// @return		void	
	//!-----------------------------------------------------------------
	function displayStatement($preFormatted=TRUE) {
		if ($preFormatted)
        	print '<PRE>' . $this->statement . '</PRE><BR>';
		else
        	print $this->statement . '<BR>';
	}

	//!-----------------------------------------------------------------
	// @function	Statement::displayResult
	// @desc		Imprime o resultado do processamento do statement
	// @access		public
	// @param		preFormatted bool	"TRUE" Inserir tags PRE para preservar a formatação
	// @return		void	
	//!-----------------------------------------------------------------
	function displayResult($preFormatted=TRUE) {
		if ($preFormatted)
        	print '<PRE>' . $this->getResult() . '</PRE><BR>';
		else
        	print $this->getResult() . '<BR>';
	}

	//!-----------------------------------------------------------------
	// @function	Statement::debugVariables
	// @desc		Monta e exibe o debug das variáveis definidas/amarradas
	//				no statement atual. Pode ser utilizado após a função isAllBound,
	//				para verificar variáveis não amarradas
	// @access		public
	// @param		preFormatted bool	"FALSE" Inserir tags de pré-formatação
	// @return		string Depuração das variáveis do statement
	//!-----------------------------------------------------------------
	function debugVariables($preFormatted=FALSE) {
		$debugStr = '';
		for ($i=0, $size=sizeof($this->defVars); $i<$size; $i++) {
			$value = $this->defVars[$i];
			$searchValue = (TypeUtils::isArray($value) ? $value[0] : $value);
			if (!array_key_exists($searchValue, $this->bindVars))
				$debugStr .= "<b>Variable:</b> " . $searchValue . " => <b>*NOT BOUND*</b><br>";
			else
				$debugStr .= "<b>Variable:</b> " . $searchValue . " => <b>" . $this->bindVars[$searchValue] . "</b><br>";
		}
		return ($preFormatted ? '<PRE>' . $debugStr . '</PRE>' : $debugStr);
	}

	//!-----------------------------------------------------------------
	// @function 	Statement::_prepareStatement
	// @desc 		Parseia o código do statement armazenando na propriedade
	// 				defVars do objeto as variáveis declaradas
	// @access 		private
	// @return		void	
	//!-----------------------------------------------------------------
	function _prepareStatement() {
		$this->prepared = TRUE;
		$temp = array();
		$expression = "/{$this->prefix}(#[^#]+#|[[:alnum:]_\:\[\'\]]+){$this->sufix}/";		
		preg_match_all($expression, $this->statement, $matches, PREG_PATTERN_ORDER);
		if (!empty($matches[1])) {
			$this->empty = FALSE;
			for ($i=0; $i<sizeOf($matches[1]); $i++) {
				if (preg_match("/([[:alnum:]_]+)\[\'?([[:alnum:]_]+)\'?\]/", $matches[1][$i], $arrParts)) {
					$valueToAdd = $arrParts;
				} else {
					$valueToAdd = $matches[1][$i];
				}
				if (!in_array($matches[1][$i], $temp)) {
					$temp[] = $matches[1][$i];
					$this->defVars[] = $valueToAdd;
				}
			}
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	Statement::_findVariable
	// @desc		Busca por uma variável na tabela de variáveis do statement
	// @param		variable string		Nome da variável
	// @return		mixed Índice da variável, se existente, ou FALSE se não for encontrada
	// @access		private	
	//!-----------------------------------------------------------------
	function _findVariable($variable) {
		for ($i=0,$size=sizeof($this->defVars); $i<$size; $i++) {
			$key = $this->defVars[$i];
			if ($key == $variable || (TypeUtils::isArray($key) && $key[0] == $variable))
				return $i;
		}
		return FALSE;
	}
}
?>