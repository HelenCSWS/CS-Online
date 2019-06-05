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
// @desc		Esta classe implementa um formul�rio espec�fico para sistemas
//				de pesquisa. Seu objetivo � construir, a partir da parametriza��o
//				de pesquisa definida para cada campo do formul�rio, uma cl�usula de
//				condi��o que ser� utilizada em uma consulta de banco de dados. Ap�s
//				montada esta consulta, a classe pode retornar o c�digo SQL produzido
//				ou armazen�-lo na sess�o, para que este possa ser utilizado como filtro
//				de um DataSet ou de um Report em uma outra requisi��o
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
	var $valid;							// @var valid bool					Armazena o status de valida��o da busca
	var $searchRawData = array();		// @var searchRawData array			"array()" Vetor que armazena os dados crus de retorno da pesquisa (cada linha cont�m valor e configura��es de cada campo)
	var $searchString = '';				// @var searchString string			"" Cont�m a string de pesquisa j� montada (cl�usula SQL de condi��o)
	var $mainOperator = 'AND';			// @var mainOperator string			"AND" Operador principal a ser utilizado (AND ou OR)
	var $prefixOperator = '';			// @var prefixOperator string		"" Operador a ser inclu�do no in�cio da cl�usula (AND ou OR)
	var $acceptEmptySearch = FALSE;		// @var acceptEmptySearch bool		"FALSE" Indica se a pesquisa poder� ser submetida sem filtros
	var $emptySearchMessage;			// @var emptySearchMessage string	Mensagem de erro exibida para uma busca sem filtros, quando n�o permitida
	var $stringMinLength;				// @var stringMinLength int			Restri��o de m�nimo de caracteres para um campo que usa operadores string (CONTAINING, STARTING, ENDING)
	var $autoRedirect = FALSE;			// @var autoRedirect bool			"FALSE" Indica se uma busca v�lida ser� redirecionada para outra URL
	var $redirectUrl;					// @var redirectUrl string			URL para onde os resultados da busca devem ser enviados
	var $paramName = 'p2g_search';		// @var paramName string			"p2g_search" Vari�vel de requisi��o ou de sess�o para a cl�usula de busca
	var $useSession = FALSE;			// @var useSession bool				"FALSE" Utilizar sess�o para a persist�ncia da cl�usula
	var $useEncode = FALSE;				// @var useEncode bool				"FALSE" Utilizar codifica��o base64 no envio da cl�usula por GET
	var $connectionId = NULL;			// @var connectionId string			"NULL" ID da conex�o ao banco de dados (uma conex�o � utilizada para formatar strings e datas)	
	var $checkboxMapping = array(		// @var checkboxMapping array		Mapa de convers�o para um campo do tipo checkbox
		'T' => 1,
		'F' => 0
	);
	var $validators = array();			// @var validators array			"array()" Vetor de validadores da pesquisa
	var $sqlCallbacks = array();		// @var sqlCallbacks array			"array()" Armazena callbacks que constr�em o c�digo SQL para um campo de pesquisa independente dos outros par�metros de configura��o
	var $valueCallbacks = array();		// @var valueCallbacks array		"array()" Armazena callbacks de transforma��o de valor j� utilizadas
	var $Db = NULL;						// @var Db Db object				Utilizado para formata��o de strings e datas de acordo com o banco utilizado
	var $Mgr = NULL;					// @var Mgr SessionManager object	Utilizado para persistir cl�usulas de busca se assim for configurado
	var $Form = NULL;					// @var Form Form object			Formul�rio constru�do pela classe para exibi��o dos filtros
	
	//!-----------------------------------------------------------------
	// @function	SearchForm::
	// @desc		Construtor da classe
	// @access		public
	// @param		xmlFile string	Arquivo XML de especifica��o do formul�rio
	// @param		templateFile string "NULL" Template para o formul�rio. Se for NULL, a classe FormBasic ser� utilizada para montar o formul�rio
	// @param		formName string	Nome para o formul�rio
	// @param		&Doc Document object Documento ao qual o formul�rio est� associado
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
	// @desc		Verifica se o formul�rio de busca foi postado e � v�lido,
	//				seguindo apenas as regras de valida��o b�sicas do formul�rio
	// @note		A valida��o segundo os validadores de busca s� � executada dentro
	//				do m�todo run() desta mesma classe
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
	// @desc		Retorna um vetor com os valores e configura��es dos campos de pesquisa
	// @access		public
	// @return		array
	//!-----------------------------------------------------------------
	function getSearchRawData() {
		return $this->searchRawData;
	}
	
	//!-----------------------------------------------------------------
	// @function	SearchForm::getSearchString
	// @desc		Retorna a cl�usula de condi��o montada a partir dos valores submetidos na pesquisa
	// @access		public
	// @return		string
	//!-----------------------------------------------------------------
	function getSearchString() {
		return $this->searchString;
	}
	
	//!-----------------------------------------------------------------
	// @function	SearchForm::getContent
	// @desc		Retorna o conte�do do formul�rio de pesquisa
	// @access		public
	// @return		string C�digo HTML completo do formul�rio
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
	//				cl�usulas de cada campo de pesquisa. Aceita os valores AND e OR
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
	// @desc		Retorna o operador configurado para prefixar a cl�usula de pesquisa
	// @access		public
	// @return		string
	//!-----------------------------------------------------------------
	function getPrefixOperator() {
		return $this->prefixOperator;
	}
	
	//!-----------------------------------------------------------------
	// @function	SearchForm::setPrefixOperator
	// @desc		Define o operador que deve prefixar a cl�usula de pesquisa.
	//				Aceita os valores AND e OR
	// @note		Esta configura��o deve ser utilizada quando a consulta base onde a
	//				cl�usula de pesquisa ser� utilizada j� possui uma cl�usula de condi��o.
	//				Desta forma, a cl�usula vinda do formul�rio seria combinada com a existente
	//				utilizando o operador AND ou o operador OR
	// @param		operator string	Operador que deve prefixar a cl�usula montada
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
	// @desc		Utilizando este m�todo com o par�metro $setting == TRUE,
	//				as buscas sem filtros estar�o habilitadas, ou seja, n�o
	//				gerar�o erro e reapresenta��o do formul�rio
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
	// @desc		Define o tamanho m�nimo de caracteres para campos que
	//				utilizam operadores string (STARTING, ENDING, CONTAINING)
	// @note		Por padr�o, n�o � feita valida��o nesse sentido, ou seja,
	//				um filtro onde apenas um caractere � fornecido poderia comprometer
	//				a performance da consulta
	// @param		minlength int	Tamanho m�nimo
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
	// @param		setting bool		"TRUE" Utilizar ou n�o redirecionamento autom�tico
	// @param		redirectUrl string	URL de redirecionamento
	// @param		paramName string	Par�metro de requisi��o ou de sess�o para armazenamento da cl�usula montada
	// @param		useSession bool		"FALSE" Utilizar sess�o na persist�ncia da cl�usula
	// @param		useEncode bool		"FALSE" Utilizar codifica��o base64 no envio da cl�usula na requisi��o GET
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
	// @param		trueValue mixed		Valor de subsitui��o para T (marcado)
	// @param		falseValue mixed	Valor de substitui��o para F (n�o marcado)
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
	// @desc		Define o ID da conex�o a banco de dados a ser utilizada na classe
	// @param		id string	ID da conex�o
	// @access		public	
	// @return		void
	//!-----------------------------------------------------------------
	function setConnectionId($id) {
		$this->connectionId = $id;
	}
	
	//!-----------------------------------------------------------------
	// @function	SearchForm::addValidator
	// @desc		Adiciona um validador para os valores de busca submetidos
	// @param		validator string	Caminho para o validador (usando nota��o de pontos: dir.dir2.MyClass)
	// @param		arguments array		"array()" Argumentos para o validador
	// @access		public	
	// @return		void
	//!-----------------------------------------------------------------
	function addValidator($validator, $arguments=array()) {
		$this->validators[] = array($validator, TypeUtils::toArray($arguments));
	}
	
	//!-----------------------------------------------------------------
	// @function	SearchForm::run
	// @desc		M�todo principal da classe. Deve ser chamado ap�s a configura��o
	//				do formul�rio para que a verifica��o de busca postada e montagem
	//				da cl�usula de condi��o seja executada
	// @note		O retorno deste m�todo indica se o formul�rio n�o foi postado ou
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
	// @desc		M�todo interno de constru��o da cl�usula de condi��o a partir
	//				dos valores submetidos para cada campo do formul�rio
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
			// par�metros de pesquisa cuja SQL � constru�da por uma callback
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
	// @desc		Aplica valida��o b�sica de valor vazio em um campo de pesquisa
	// @access		private
	// @param		args array	Valor e configura��es de um campo de pesquisa
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
			// valida��o de tamanho m�nimo quando � um operador de string
			if (in_array($args['OPERATOR'], array('STARTING', 'CONTAINING', 'ENDING')) && isset($this->stringMinLength))
				return (!TypeUtils::isNull($args['VALUE']) && strlen($args['VALUE']) >= $this->stringMinLength);
			return (!TypeUtils::isNull($args['VALUE']));
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	SearchForm::_resolveOperator
	// @desc		Transforma um valor de operador definido no XML para um
	//				nome de operador v�lido para a especifica��o SQL-ANSI
	// @access		private
	// @param		op string 	Operador
	// @return		string C�digo SQL correspondente ao operador
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
	// @desc		Verifica se um determinado campo possui uma fun��o de
	//				constru��o da cl�usula SQL, ignorando todos os outros par�metros
	//				de configura��o
	// @access		private
	// @param		callback string	Callback de constru��o de cl�usula SQL
	// @param		value mixed		Valor do campo de pesquisa
	// @return		mixed Cl�usula SQL montada para o campo
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
	// @desc		Verifica se um determinado campo possui uma fun��o de
	//				transforma��o de valor e executa a mesma se existir
	// @access		private
	// @param		callback string	Callback de transforma��o de valor
	// @param		value mixed		Valor do campo de pesquisa
	// @return		mixed Valor de pesquisa (transformado, se a fun��o existir)
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