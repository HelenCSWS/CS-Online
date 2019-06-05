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
// $Header: /www/cvsroot/php2go/core/validation/RuleValidator.class.php,v 1.8 2005/06/17 20:06:20 mpont Exp $
// $Date: 2005/06/17 20:06:20 $

//------------------------------------------------------------------
import('php2go.datetime.Date');
import('php2go.util.Statement');
import('php2go.validation.Validator');
//------------------------------------------------------------------

//!-----------------------------------------------------------------
// @class		RuleValidator
// @desc		Classe que valida o valor de um campo de formulário de acordo
//				com uma determinada regra: comparação com outros campos, comparação
//				com valores estáticos ou obrigatoriedade condicional
// @package		php2go.validation
// @uses		TypeUtils
// @extends		Validator
// @uses		Date
// @uses		TypeUtils
// @author		Marcos Pont
// @version		$Revision: 1.8 $
// @note		Exemplo de uso:<BR>
//				<PRE>
//
//				$Password = $Form->getField('password');
//				$Rule =& new FormRule('EQ', 'confirm_password', NULL, NULL, 'Passwords must be equal!');
//				$Rule->setOwnerField($Password);
//				$params = array(
//				&nbsp;&nbsp;&nbsp;'rule' => $Rule
//				);
//				if (Validator::validate('php2go.validation.RuleValidator', 'password', $params)) {
//				&nbsp;&nbsp;&nbsp;print 'ok';
//				}
//
//				</PRE>
//!-----------------------------------------------------------------
class RuleValidator extends Validator
{
	var $Rule = NULL;	// @var FormRule object			Regra de formulário a ser validada
	var $errorMessage;	// @var errorMessage string		Mensagem de erro
	
	//!-----------------------------------------------------------------
	// @function	RuleValidator::RuleValidator
	// @desc		Construtor da classe
	// @access		public
	// @param		params array	"NULL" Parâmetros para o validador
	// @note		Conjunto de parâmetros:
	//				type => Tipo da regra
	//				form => Instância válida de um formulário
	//				target => Campo de comparação (opcional)
	//				value => Valor de comparação (opcional)
	//!-----------------------------------------------------------------
	function RuleValidator($params = NULL) {
		Validator::Validator();
		if (TypeUtils::isArray($params)) {
			if (isset($params['rule']))
				$this->Rule =& $params['rule'];
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	RuleValidator::execute
	// @desc		Verifica se o valor é válido para a regra estabelecida
	// @access		public
	// @param		srcName mixed	Nome do campo a ser validado
	// @return		bool
	//!-----------------------------------------------------------------
	function execute($srcName) {
		// validação da regra
		if (!isset($this->Rule))
			return FALSE;
		// validação do campo origem
		$Form =& $this->Rule->getOwnerForm();
		if (TypeUtils::isNull($Form))
			return FALSE;
		$Src =& $this->Rule->getOwnerField();
		if (TypeUtils::isNull($Src)) {
			return FALSE;
		} else {
			$srcValue = $Src->getValue();
			if ($Src->isA('checkfield') && $srcValue == 'F')
				$srcValue = '';
		}
		// validação do campo alvo
		$trgName = $this->Rule->getTargetField();
		if (!empty($trgName)) {
			$Trg =& $Form->getField($trgName);
			if (TypeUtils::isNull($Trg)) {
				return FALSE;
			} else {
				$trgValue = $Trg->getValue();
				if ($Trg->isA('checkfield') && $trgValue == 'F')
					$trgValue = '';
			}
		}
		$matches = array();
		if ($this->Rule->getType() == 'REQIF') {
			// obrigatoriedade condicional (se outro campo for não vazio)
			if (empty($srcValue) && !empty($trgValue)) {
				$this->errorMessage = PHP2Go::getLangVal('ERR_FORM_FIELD_REQUIRED', $Src->getLabel());
				return FALSE;
			} else {
				return TRUE;
			}
		} elseif ($this->Rule->getType() == 'REGEX') {
			// valor do campo deve respeitar um padrão			
			$pattern = $this->Rule->getValueArgument();
			if (!empty($srcValue) && !preg_match("{$pattern}", $srcValue)) {
				$this->errorMessage = PHP2Go::getLangVal('ERR_FORM_FIELD_REGEX', $Src->getLabel());
				return FALSE;
			} else {
				return TRUE;
			}
		} elseif (ereg("^(REQIF)?(EQ|NEQ|GT|LT|GOET|LOET)$", $this->Rule->getType(), $matches)) {
			$mask = $this->Rule->getCompareType();
			$value = $this->Rule->getValueArgument();
			if ($matches[1] != NULL) {
				// obrigatoriedade condicional (se comparação retornar verdadeiro)
				if (empty($srcValue) && !empty($trgValue) && $this->_compareValues($trgValue, $value, $matches[2], $mask, 'value')) {
					$result = FALSE;
					$this->errorMessage = PHP2Go::getLangVal('ERR_FORM_FIELD_REQUIRED', $Trg->getLabel());
				} else {
					$result = TRUE;
				}
			} else {
				// comparação campo X valor
				if (!empty($value)) {
					$result = $this->_compareValues($srcValue, $value, $matches[2], $mask, 'value');
					if (!$result)
						$this->errorMessage = PHP2Go::getLangVal('ERR_FORM_FIELD_VALUE_' . $matches[2], array($Src->getLabel(), $value));
				// comparação campo X campo
				} else {
					$result = $this->_compareValues($srcValue, $trgValue, $matches[2], $mask, 'field');
					if (!$result)
						$this->errorMessage = PHP2Go::getLangVal('ERR_FORM_FIELD_' . $matches[2], array($Src->getLabel(), $Trg->getLabel()));
				}
			}
			return $result;
		}
		$this->errorMessage = PHP2Go::getLangVal('ERR_FORM_FIELD_REGEX', $Src->getLabel());
		return FALSE;
	}
	
	//!-----------------------------------------------------------------
	// @function	RuleValidator::getError
	// @desc		Retorna a mensagem de erro resultante da validação
	// @access		public
	// @return		string Mensagem de erro
	//!-----------------------------------------------------------------
	function getError() {
		return $this->errorMessage;
	}
	
	//!-----------------------------------------------------------------
	// @function	RuleValidator::_compareValues
	// @desc		Método utilitário de comparação entre valores
	// @access		private
	// @param		source mixed	Valor origem
	// @param		target mixed	Valor alvo
	// @param		operator string	Operador de comparação
	// @param		dataType string	Tipo de dado a ser utilizado na comparação
	// @param		targetType int	Tipo de comparação: field (campo x campo) ou value (campo x valor)
	// @return		bool
	//!-----------------------------------------------------------------
	function _compareValues($source, $target, $operator, $dataType, $targetType) {
		if ($targetType == 'field') {
			if (empty($source) || empty($target))
				return TRUE;
		} else {
			if (empty($source))
				return TRUE;
		}
		if ($dataType != '') {
			switch ($dataType) {
				case 'DATE' :						
					$src = Date::dateToDays($source);
					if ($targetType == 'value')
						$target = Date::parseFieldExpression($target);
					$trg = Date::dateToDays($target);
					break;
				case 'INTEGER' :
					$src = TypeUtils::parseInteger($source);
					$trg = TypeUtils::parseInteger($target);
					break;
				case 'FLOAT' :
					$src = TypeUtils::parseFloat($source);
					$trg = TypeUtils::parseFloat($target);
					break;
				default :
					$src = $source;
					$trg = $target;
					break;
			}
		} else {
			$src = $source;
			$trg = $target;
		}
		$result = TRUE;
		switch ($operator) {
			case 'EQ' : $result = ($src == $trg); break;
			case 'NEQ' : $result = ($src != $trg); break;
			case 'GT' : $result = ($src > $trg); break;
			case 'LT' : $result = ($src < $trg); break;
			case 'GOET' : $result = ($src >= $trg); break;
			case 'LOET' : $result = ($src <= $trg); break;					
		}
		return $result;
	}
}
?>