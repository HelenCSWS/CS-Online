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
// $Header: /www/cvsroot/php2go/core/form/SearchForm.class.php,v 1.3 2005/09/01 20:02:46 mpont Exp $
// $Date: 2005/09/01 20:02:46 $

//------------------------------------------------------------------
import('php2go.db.QueryBuilder');
import('php2go.form.FormBasic');
import('php2go.form.FormTemplate');
import('php2go.net.Url');
import('php2go.session.SessionManager');
//------------------------------------------------------------------

//!-----------------------------------------------------------------
// @class		SearchForm
// @desc		Esta classe implementa um formulário específico para sistemas
//				de pesquisa. Seu objetivo é construir, a partir da parametrização
//				de pesquisa definida para cada campo do formulário, uma cláusula de
//				condição que será utilizada em uma consulta de banco de dados. Após
//				montada esta consulta, a classe pode retornar o código SQL produzido
//				ou armazená-lo na sessão, para que este possa ser utilizado como filtro
//				de um DataSet ou de um Report em uma outra requisição
// @package		php2go.form
// @extends		PHP2Go
// @uses		Callback
// @uses		Db
// @uses		FormBasic
// @uses		FormTemplate
// @uses		SessionManager
// @uses		TypeUtils
// @uses		Url
// @author		Marcos Pont
// @version		$Revision: 1.3 $
//!-----------------------------------------------------------------
class SearchForm extends PHP2Go
{
	var $valid;							// @var valid bool					Armazena o status de validação da busca
	var $searchRawData = array();		// @var searchRawData array			"array()" Vetor que armazena os dados crus de retorno da pesquisa (cada linha contém valor e configurações de cada campo)
	var $searchString = '';				// @var searchString string			"" Contém a string de pesquisa já montada (cláusula SQL de condição)
	var $mainOperator = 'AND';			// @var mainOperator string			"AND" Operador principal a ser utilizado (AND ou OR)
	var $prefixOperator = '';			// @var prefixOperator string		"" Operador a ser incluído no início da cláusula (AND ou OR)
	var $acceptEmptySearch = FALSE;		// @var acceptEmptySearch bool		"FALSE" Indica se a pesquisa poderá ser submetida sem filtros
	var $emptySearchMessage;			// @var emptySearchMessage string	Mensagem de erro exibida para uma busca sem filtros, quando não permitida
	var $stringMinLength;				// @var stringMinLength int			Restrição de mínimo de caracteres para um campo que usa operadores string (CONTAINING, STARTING, ENDING)
	var $autoRedirect = FALSE;			// @var autoRedirect bool			"FALSE" Indica se uma busca válida será redirecionada para outra URL
	var $redirectUrl;					// @var redirectUrl string			URL para onde os resultados da busca devem ser enviados
	var $paramName = 'p2g_search';		// @var paramName string			"p2g_search" Variável de requisição ou de sessão para a cláusula de busca
	var $useSession = FALSE;			// @var useSession bool				"FALSE" Utilizar sessão para a persistência da cláusula
	var $useEncode = FALSE;				// @var useEncode bool				"FALSE" Utilizar codificação base64 no envio da cláusula por GET
	var $connectionId = NULL;			// @var connectionId string			"NULL" ID da conexão ao banco de dados (uma conexão é utilizada para formatar strings e datas)	
	var $checkboxMapping = array(		// @var checkboxMapping array		Mapa de conversão para um campo do tipo checkbox
		'T' => 1,
		'F' => 0
	);
	var $validators = array();			// @var validators array			"array()" Vetor de validadores da pesquisa
	var $sqlCallbacks = array();		// @var sqlCallbacks array			"array()" Armazena callbacks que constróem o código SQL para um campo de pesquisa independente dos outros parâmetros de configuração
	var $valueCallbacks = array();		// @var valueCallbacks array		"array()" Armazena callbacks de transformação de valor já utilizadas
	var $Db = NULL;						// @var Db Db object				Utilizado para formatação de strings e datas de acordo com o banco utilizado
	var $Mgr = NULL;					// @var Mgr SessionManager object	Utilizado para persistir cláusulas de busca se assim for configurado
	var $Form = NULL;					// @var Form Form object			Formulário construído pela classe para exibição dos filtros
	
	//!-----------------------------------------------------------------
	// @function	SearchForm::
	// @desc		Construtor da classe
	// @access		public
	// @param		xmlFile string	Arquivo XML de especificação do formulário
	// @param		templateFile string "NULL" Template para o formulário. Se for NULL, a classe FormBasic será utilizada para montar o formulário
	// @param		formName string	Nome para o formulário
	// @param		&Doc Document object Documento ao qual o formulário está associado
	//!-----------------------------------------------------------------
	function SearchForm($xmlFile, $templateFile=NULL, $formName, &$Doc) {
		parent::PHP2GO();
		$this->Mgr =& new SessionManager();
		if (TypeUtils::isNull($templateFile))
			$this->Form = new FormBasic($xmlFile, $formName, $Doc);
		else
			$this->Form = new FormTemplate($xmlFile, $templateFile, $formName, $Doc);
	}
	
	//!-----------------------------------------------------------------
	// @function	SearchForm::isValid
	// @desc		Verifica se o formulário de busca foi postado e é válido,
	//				seguindo apenas as regras de validação básicas do formulário
	// @note		A validação segundo os validadores de busca só é executada dentro
	//				do método run() desta mesma classe
	// @see			SearchForm::run
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function isValid() {
		if (!isset($this->valid))
			$this->valid = ($this->Form->isPosted() && $this->Form->isValid());
		if (!$this->valid && $this->useSession)
			$this->Mgr->unregister($this->paramName);
		return $this->valid;
	}
	
	//!-----------------------------------------------------------------
	// @function	SearchForm::getSearchRawData
	// @desc		Retorna um vetor com os valores e configurações dos campos de pesquisa
	// @access		public
	// @return		array
	//!-----------------------------------------------------------------
	function getSearchRawData() {
		return $this->searchRawData;
	}
	
	//!-----------------------------------------------------------------
	// @function	SearchForm::getSearchString
	// @desc		Retorna a cláusula de condição montada a partir dos valores submetidos na pesquisa
	// @access		public
	// @return		string
	//!-----------------------------------------------------------------
	function getSearchString() {
		return $this->searchString;
	}
	
	//!-----------------------------------------------------------------
	// @function	SearchForm::getContent
	// @desc		Retorna o conteúdo do formulário de pesquisa
	// @access		public
	// @return		string Código HTML completo do formulário
	//!-----------------------------------------------------------------
	function getContent() {
		return $this->Form->getContent();
	}
	
	//!-----------------------------------------------------------------
	// @function	SearchForm::getMainOperator
	// @desc		Retorna o operador principal configurado na classe
	// @access		public
	// @return		string
	//!-----------------------------------------------------------------
	function getMainOperator() {
		return $this->mainOperator;
	}
	
	//!-----------------------------------------------------------------
	// @function	SearchForm::setMainOperator
	// @desc		Define o operador principal a ser utilizado entre as	
	//				cláusulas de cada campo de pesquisa. Aceita os valores AND e OR
	// @param		operator string	Operador principal da busca
	// @access		public	
	// @return		void
	//!-----------------------------------------------------------------
	function setMainOperator($operator) {
		$operator = strtoupper($operator);
		if ($operator == 'OR' || $operator == 'AND')
			$this->mainOperator = $operator;
	}
	
	//!-----------------------------------------------------------------
	// @function	SearchForm::getPrefixOperator
	// @desc		Retorna o operador configurado para prefixar a cláusula de pesquisa
	// @access		public
	// @return		string
	//!-----------------------------------------------------------------
	function getPrefixOperator() {
		return $this->prefixOperator;
	}
	
	//!-----------------------------------------------------------------
	// @function	SearchForm::setPrefixOperator
	// @desc		Define o operador que deve prefixar a cláusula de pesquisa.
	//				Aceita os valores AND e OR
	// @note		Esta configuração deve ser utilizada quando a consulta base onde a
	//				cláusula de pesquisa será utilizada já possui uma cláusula de condição.
	//				Desta forma, a cláusula vinda do formulário seria combinada com a existente
	//				utilizando o operador AND ou o operador OR
	// @param		operator string	Operador que deve prefixar a cláusula montada
	// @access		public	
	// @return		void
	//!-----------------------------------------------------------------
	function setPrefixOperator($operator) {
		$operator = strtoupper($operator);
		if ($operator == 'OR' || $operator == 'AND')
			$this->prefixOperator = $operator;		
	}
	
	//!-----------------------------------------------------------------
	// @function	SearchForm::setAcceptEmptySearch
	// @desc		Utilizando este método com o parâmetro $setting == TRUE,
	//				as buscas sem filtros estarão habilitadas, ou seja, não
	//				gerarão erro e reapresentação do formulário
	// @param		setting bool "TRUE" Valor para o flag
	// @access		public	
	// @return		void
	//!-----------------------------------------------------------------
	function setAcceptEmptySearch($setting=TRUE) {
		$this->acceptEmptySearch = TypeUtils::toBoolean($setting);
	}
	
	//!-----------------------------------------------------------------
	// @function	SearchForm::setEmptySearchMessage
	// @desc		Define a mensagem de erro para uma busca enviada sem filtros
	// @param		message string	Mensagem de erro
	// @access		public	
	// @return		void
	//!-----------------------------------------------------------------
	function setEmptySearchMessage($message) {
		$this->emptySearchMessage = $message;
	}
	
	//!-----------------------------------------------------------------
	// @function	SearcForm::setStringMinLength
	// @desc		Define o tamanho mínimo de caracteres para campos que
	//				utilizam operadores string (STARTING, ENDING, CONTAINING)
	// @note		Por padrão, não é feita validação nesse sentido, ou seja,
	//				um filtro onde apenas um caractere é fornecido poderia comprometer
	//				a performance da consulta
	// @param		minlength int	Tamanho mínimo
	// @access		public	
	// @return		void
	//!-----------------------------------------------------------------
	function setStringMinLength($minlength) {
		if (TypeUtils::isInteger($minlength) && $minlength > 0) {
			$this->stringMinLength = $minlength;
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	SearchForm::setAutoRedirect
	// @desc		Permite definir uma URL de redirecionamento para a pesquisa
	// @param		setting bool		"TRUE" Utilizar ou não redirecionamento automático
	// @param		redirectUrl string	URL de redirecionamento
	// @param		paramName string	Parâmetro de requisição ou de sessão para armazenamento da cláusula montada
	// @param		useSession bool		"FALSE" Utilizar sessão na persistência da cláusula
	// @param		useEncode bool		"FALSE" Utilizar codificação base64 no envio da cláusula na requisição GET
	// @access		public	
	// @return		void
	//!-----------------------------------------------------------------
	function setAutoRedirect($setting=TRUE, $url, $paramName='p2g_search', $useSession=FALSE, $useEncode=FALSE) {
		$this->autoRedirect = TypeUtils::toBoolean($setting);
		if ($this->autoRedirect) {
			$this->redirectUrl = $url;
			$this->paramName = $paramName;
			$this->useSession = TypeUtils::toBoolean($useSession);
			$this->useEncode = TypeUtils::toBoolean($useEncode);
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	SearchForm::setCheckboxMapping
	// @desc		Define o mapeamento de valores para campos do tipo checkbox
	// @param		trueValue mixed		Valor de subsituição para T (marcado)
	// @param		falseValue mixed	Valor de substituição para F (não marcado)
	// @access		public	
	// @return		void
	//!-----------------------------------------------------------------
	function setCheckboxMapping($trueValue, $falseValue) {
		$this->checkboxMapping = array(
			'T' => $trueValue,
			'F' => $falseValue
		);
	}
	
	//!-----------------------------------------------------------------
	// @function	SearchForm::setConnectionId
	// @desc		Define o ID da conexão a banco de dados a ser utilizada na classe
	// @param		id string	ID da conexão
	// @access		public	
	// @return		void
	//!-----------------------------------------------------------------
	function setConnectionId($id) {
		$this->connectionId = $id;
	}
	
	//!-----------------------------------------------------------------
	// @function	SearchForm::addValidator
	// @desc		Adiciona um validador para os valores de busca submetidos
	// @param		validator string	Caminho para o validador (usando notação de pontos: dir.dir2.MyClass)
	// @param		arguments array		"array()" Argumentos para o validador
	// @access		public	
	// @return		void
	//!-----------------------------------------------------------------
	function addValidator($validator, $arguments=array()) {
		$this->validators[] = array($validator, TypeUtils::toArray($arguments));
	}
	
	//!-----------------------------------------------------------------
	// @function	SearchForm::run
	// @desc		Método principal da classe. Deve ser chamado após a configuração
	//				do formulário para que a verificação de busca postada e montagem
	//				da cláusula de condição seja executada
	// @note		O retorno deste método indica se o formulário não foi postado ou
	//				foi postado com erros (FALSE) ou foi postado com sucesso (TRUE)
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function run() {
		if ($this->isValid()) {
			$this->Db =& Db::getInstance($this->connectionId);
			if ($this->_buildSearchString()) {
				foreach ($this->validators as $validator) {
					if (!Validator::validate($validator[0], $this->searchRawData, $validator[1]))
						$this->valid = FALSE;
				}
				if (!$this->valid) {
					$this->Form->addErrors(Validator::getErrors());
					return FALSE;
				} elseif ($this->autoRedirect) {
					$Url = new Url($this->redirectUrl);
					if ($this->useSession) {
						$this->Mgr->setValue($this->paramName, $this->searchString);
					} else {
						if ($this->useEncode)
							$Url->addParameter($this->paramName, base64_encode($this->searchString));
						else
							$Url->addParameter($this->paramName, urlencode($this->searchString));
					}
					HttpResponse::redirect($Url);
				}
				return TRUE;
			} else {
				$errorMessage = (isset($this->emptySearchMessage) ? $this->emptySearchMessage : (isset($this->stringMinLength) ? PHP2Go::getLangVal('ERR_SEARCHFORM_INVALID', $this->stringMinLength) : PHP2Go::getLangVal('ERR_SEARCHFORM_EMPTY')));
				$this->Form->addErrors($errorMessage);
				$this->valid = FALSE;
				return FALSE;
			}
		}		
	}
	
	//!-----------------------------------------------------------------
	// @function	SearchForm::_buildSearchString
	// @desc		Método interno de construção da cláusula de condição a partir
	//				dos valores submetidos para cada campo do formulário
	// @access		private
	// @return		bool
	//!-----------------------------------------------------------------
	function _buildSearchString() {
		$result = array();
		foreach ($this->Form->fields as $name => $field) {
			$sd = $field->getSearchData();
			if ($field->child || !$field->searchable || !$this->_validadeSearchField($sd))
				continue;			
			$this->searchRawData[$name] = $sd;				
		}		
		foreach ($this->searchRawData as $field => $args) {
			// parâmetros de pesquisa cuja SQL é construída por uma callback
			if (isset($args['SQLFUNC'])) {
				$clause = $this->_resolveSqlCallback($args['SQLFUNC'], $args['VALUE']);
			}
			// operador BETWEEN
			elseif ($args['FIELDTYPE'] == 'RANGEFIELD') {
				$clause = (isset($args['FIELDFUNC']) ? sprintf($args['FIELDFUNC'], $args['ALIAS']) : $args['ALIAS']);
				list($tmp, $bottom) = each($args['VALUE']);
				list($tmp, $top) = each($args['VALUE']);
				$bottom = $this->_resolveValueCallback(@$args['VALUEFUNC'], $bottom);
				$top = $this->_resolveValueCallback(@$args['VALUEFUNC'], $top);
				if ($args['DATATYPE'] == 'STRING') {
					$bottom = $this->Db->quoteString($bottom);
					$top = $this->Db->quoteString($top);
				} elseif ($args['DATATYPE'] == 'DATE') {
					$bottom = $this->Db->date($bottom);
					$top = $this->Db->date($top);
				} elseif ($args['DATATYPE'] == 'DATETIME') {
					// completa o intervalo com hora, minuto e segundo
					$bottom .= " 00:00:00";
					$top .= " 23:59:59";
					$bottom = $this->Db->date($bottom, TRUE);
					$top = $this->Db->date($top, TRUE);
				}
				$clause .= $this->_resolveOperator($args['OPERATOR']) . $bottom . ' and ' . $top;
			}
			// operadores IN e NOTIN (array como valor)
			elseif ($args['OPERATOR'] == 'IN' || $args['OPERATOR'] == 'NOTIN') {
				$clause = (isset($args['FIELDFUNC']) ? sprintf($args['FIELDFUNC'], $args['ALIAS']) : $args['ALIAS']);
				$value = $this->_resolveValueCallback(@$args['VALUEFUNC'], TypeUtils::toArray($args['VALUE']));
				if ($args['DATATYPE'] == 'STRING') {
					foreach ($value as $key => $entry)
						$value[$key] = $this->Db->quoteString($entry);
				} elseif ($args['DATATYPE'] == 'DATE' || $args['DATATYPE'] == 'DATETIME') {
					foreach ($value as $key => $entry)
						$value[$key] = $this->Db->date($entry, ($args['DATATYPE'] == 'DATETIME'));
				}
				$clause .= $this->_resolveOperator($args['OPERATOR']) . '(' . implode(',', $value) . ')';	
			}
			// operadores string
			elseif ($args['OPERATOR'] == 'STARTING' || $args['OPERATOR'] == 'ENDING' || $args['OPERATOR'] == 'CONTAINING') {
				$clause = (isset($args['FIELDFUNC']) ? sprintf($args['FIELDFUNC'], $args['ALIAS']) : $args['ALIAS']);				
				$value = $this->_resolveValueCallback(@$args['VALUEFUNC'], TypeUtils::parseString($args['VALUE']));				
				if ($args['OPERATOR'] == 'ENDING' || $args['OPERATOR'] == 'CONTAINING')
					$value = '%' . $value;					
				if ($args['OPERATOR'] == 'STARTING' || $args['OPERATOR'] == 'CONTAINING')
					$value .= '%';					
				$value = $this->Db->quoteString($value);
				$clause .= $this->_resolveOperator($args['OPERATOR']) . $value;
			} 
			// outros operadores
			else {
				$clause = (isset($args['FIELDFUNC']) ? sprintf($args['FIELDFUNC'], $args['ALIAS']) : $args['ALIAS']);
				$value = $this->_resolveValueCallback(@$args['VALUEFUNC'], @$args['VALUE']);
				if ($args['FIELDTYPE'] == 'CHECKFIELD')
					$value = $this->checkboxMapping[$value];
				elseif ($args['DATATYPE'] == 'STRING')
					$value = $this->Db->quoteString($value);
				elseif ($args['DATATYPE'] == 'DATE')
					$value = $this->Db->date($value);
				elseif ($value['DATATYPE'] == 'DATETIME')
					$value = $this->Db->date($value, TRUE);
				$clause .= $this->_resolveOperator($args['OPERATOR']) . $value;
			}
			if (!empty($clause))
				$result[] = $clause;
		}
		$this->searchString = implode(" {$this->mainOperator} ", $result);
		if (!empty($this->prefixOperator) && !empty($this->searchString))
			$this->searchString = ($this->prefixOperator == 'OR' ? " OR ({$this->searchString})" : " AND {$this->searchString}");
		return ($this->acceptEmptySearch || !empty($this->searchString));
	}
	
	//!-----------------------------------------------------------------
	// @function	SearchForm::_validateSearchField
	// @desc		Aplica validação básica de valor vazio em um campo de pesquisa
	// @access		private
	// @param		args array	Valor e configurações de um campo de pesquisa
	// @return		bool
	//!-----------------------------------------------------------------
	function _validadeSearchField($args) {
		if ($args['FIELDTYPE'] == 'RANGEFIELD') {
			$sv = TypeUtils::toArray($args['VALUE']);
			if (sizeof($sv) != 2)
				return FALSE;
			list($tmp, $bottom) = each($sv);
			list($tmp, $top) = each($sv);
			return (!empty($bottom) && !empty($top));			
		} else {
			// validação de tamanho mínimo quando é um operador de string
			if (in_array($args['OPERATOR'], array('STARTING', 'CONTAINING', 'ENDING')) && isset($this->stringMinLength))
				return (!TypeUtils::isNull($args['VALUE']) && strlen($args['VALUE']) >= $this->stringMinLength);
			return (!TypeUtils::isNull($args['VALUE']));
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	SearchForm::_resolveOperator
	// @desc		Transforma um valor de operador definido no XML para um
	//				nome de operador válido para a especificação SQL-ANSI
	// @access		private
	// @param		op string 	Operador
	// @return		string Código SQL correspondente ao operador
	//!-----------------------------------------------------------------
	function _resolveOperator($op) {
		switch ($op) {
			case 'EQ' : return ' = ';
			case 'NEQ' : return ' <> ';
			case 'LT' : return ' < ';
			case 'LOET' : return ' <= ';
			case 'GT' : return ' > ';
			case 'GOET' : return ' >= ';
			case 'STARTING' : 
			case 'ENDING' :
			case 'CONTAINING' :
				return ' LIKE ';
			case 'IN' :
				return ' IN ';
			case 'NOTIN' :
				return ' NOT IN ';
			case 'BETWEEN' :
				return ' BETWEEN ';
			default :
				return ' = ';
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	SearchForm::_resolveSqlCallback
	// @desc		Verifica se um determinado campo possui uma função de
	//				construção da cláusula SQL, ignorando todos os outros parâmetros
	//				de configuração
	// @access		private
	// @param		callback string	Callback de construção de cláusula SQL
	// @param		value mixed		Valor do campo de pesquisa
	// @return		mixed Cláusula SQL montada para o campo
	//!-----------------------------------------------------------------
	function _resolveSqlCallback($callback, $value) {
		if (empty($callback))
			return FALSE;
		if (!isset($this->sqlCallbacks[$callback]))
			$this->sqlCallbacks[$callback] =& new Callback($callback);
		return $this->sqlCallbacks[$callback]->invoke($value);
	}
	
	//!-----------------------------------------------------------------
	// @function	SearchForm::_resolveValueCallback
	// @desc		Verifica se um determinado campo possui uma função de
	//				transformação de valor e executa a mesma se existir
	// @access		private
	// @param		callback string	Callback de transformação de valor
	// @param		value mixed		Valor do campo de pesquisa
	// @return		mixed Valor de pesquisa (transformado, se a função existir)
	//!-----------------------------------------------------------------
	function _resolveValueCallback($callback, $value) {
		if (empty($callback))
			return $value;
		if (!isset($this->valueCallbacks[$callback]))
			$this->valueCallbacks[$callback] =& new Callback($callback);
		return $this->valueCallbacks[$callback]->invoke($value);
	}
}
?>