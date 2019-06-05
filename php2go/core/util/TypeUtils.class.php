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
// $Header: /www/cvsroot/php2go/core/util/TypeUtils.class.php,v 1.14 2005/07/18 15:04:26 mpont Exp $
// $Date: 2005/07/18 15:04:26 $

//!----------------------------------------------
// @class		TypeUtils
// @desc		Classe que contщm funчѕes utilitсrias para verificaчуo de tipagem
//				de dados e conversуo (cast) entre tipos primitivos de dado no PHP
// @package		php2go.util
// @extends		PHP2Go
// @author		Marcos Pont
// @version		$Revision: 1.14 $
// @static
//!----------------------------------------------
class TypeUtils extends PHP2Go
{	
	//!----------------------------------------------
	// @function	TypeUtils::getType
	// @desc		Retorna o tipo de uma variсvel
	// @access		public
	// @return		string Tipo da variсvel
	// @static
	//!----------------------------------------------
	function getType($value) {
		return gettype($value);
	}

	//!----------------------------------------------
	// @function	TypeUtils::isFloat
	// @desc		Determina se um valor щ do tipo float
	// @access		public
	// @param		&value mixed	Valor a ser testado
	// @param		strict bool	"FALSE" Se TRUE, realiza o teste de sintaxe e formato de variсvel
	// @return		bool
	// @note		Se o parтmetro $strict for mantido FALSE, um nњmero inteiro ou string, se respeitar
	//				a sintaxe de um decimal - 999[.999] - serс convertido para float
	// @static	
	//!----------------------------------------------
	function isFloat(&$value, $strict=FALSE) {
		$locale = localeconv();
		$dp = $locale['decimal_point'];
		$exp = "/^\-?[0-9]+(\\" . $dp . "[0-9]+)?$/";
		if (preg_match($exp, $value)) {
			if (!$strict && !is_float($value)) {
				$value = TypeUtils::parseFloat($value);
			}
			return TRUE;
		} else {
			return FALSE;
		}
	}
	
	//!----------------------------------------------
	// @function	TypeUtils::parseFloat
	// @desc		Cria a representaчуo de nњmero decimal para um valor
	// @access		public	
	// @param		value mixed	Valor a ser convertido
	// @return		float Valor convertido para nњmero decimal
	// @static	
	//!----------------------------------------------
	function parseFloat($value) {
		if (TypeUtils::isString($value)) {
			$locale = localeconv();
			if ($locale['decimal_point'] != '.') {
				$value = str_replace(',', '.', $value);
			}
		}
		return (function_exists('floatval') ? floatval($value) : doubleval($value));
	}
	
	//!----------------------------------------------
	// @function	TypeUtils::parseFloatPositive
	// @desc		Cria a representaчуo de nњmero decimal positivo para um valor
	// @access		public	
	// @param		value mixed	Valor a ser convertido
	// @return		float Valor convertido para nњmero decimal positivo
	// @static	
	//!----------------------------------------------
	function parseFloatPositive($value) {
		return abs(floatval($value));
	}
	
	//!----------------------------------------------
	// @function	TypeUtils::isInteger
	// @desc		Testa se um valor щ um nњmero inteiro
	// @access		public	
	// @param		&value int		Valor a ser testado
	// @param		strict bool	"FALSE" Se TRUE, realiza o teste de sintaxe e formato de variсvel
	// @return		bool
	// @note		Se o parтmetro $strict for mantido FALSE, uma string que respeite a sintaxe de
	//				nњmeros inteiros - 999 - serс convertida para integer
	// @static	
	//!----------------------------------------------
	function isInteger(&$value, $strict=FALSE) {
		$exp = "/^\-?[0-9]+$/";
		if (preg_match($exp, $value)) {
			if (!$strict && !is_int($value)) {
				$value = TypeUtils::parseInteger($value);
			}
			return TRUE;
		} else {
			return FALSE;
		}
	}
	
	//!----------------------------------------------
	// @function	TypeUtils::parseInteger
	// @desc		Cria a representaчуo de nњmero inteiro para um valor
	// @access		public	
	// @param		value mixed	Valor a ser convertido
	// @return		int Valor convertido
	// @static	
	//!----------------------------------------------
	function parseInteger($value) {
		return intval($value);
	}
	
	//!----------------------------------------------
	// @function	TypeUtils::parseIntegerPositive
	// @desc		Cria a representaчуo de nњmero inteiro positivo para um valor
	// @access		public	
	// @param		value mixed	Valor a ser convertido
	// @return		int Valor convertido
	// @static	
	//!----------------------------------------------
	function parseIntegerPositive($value) {
		return abs(intval($value));
	}
	
	//!----------------------------------------------
	// @function	TypeUtils::isString
	// @desc		Testa se um determinado valor щ string
	// @access		public	
	// @param		value mixed	Valor a ser testado
	// @return		bool
	// @static	
	//!----------------------------------------------
	function isString($value) {
		return is_string($value);
	}
	
	//!----------------------------------------------
	// @function	TypeUtils::parseString
	// @desc		Retorna a representaчуo string de um valor
	// @access		public	
	// @param		value mixed	Valor a ser convertido
	// @return		string Resultado da conversуo
	// @static	
	//!----------------------------------------------
	function parseString($value) {
		return strval($value);
	}
	
	//!----------------------------------------------
	// @function	TypeUtils::isArray
	// @desc		Valida se um valor щ do tipo array
	// @access		public	
	// @param		value mixed	Valor a ser testado
	// @return		bool
	// @static		
	//!----------------------------------------------
	function isArray($value) {
		return is_array($value);
	}
	
	//!----------------------------------------------
	// @function	TypeUtils::isHashArray
	// @desc		Valida se um array щ do tipo hash (associativo)
	// @access		public
	// @param		value array	Vetor a ser testado
	// @return		bool
	// @static		
	//!----------------------------------------------
	function isHashArray($value) {
		if (!TypeUtils::isArray($value)) {
			return FALSE;
		}
		foreach ($value as $key => $value) {
			$keyType = TypeUtils::getType($key);
			if ($keyType != 'integer')
				return TRUE;
		}
		return FALSE;
	}
	
	//!----------------------------------------------
	// @function	TypeUtils::toArray
	// @desc		Cria uma representaчуo de vetor para um valor qualquer
	// @access		public	
	// @param		value mixed	Valor a ser convertido
	// @return		array Valor convertido para um array
	// @note		O valor permanece inalterado caso jс seja do tipo array
	// @static		
	//!----------------------------------------------
	function toArray($value) {
		return TypeUtils::isArray($value) ? $value : array($value);
	}
	
	//!----------------------------------------------
	// @function	TypeUtils::isObject
	// @desc		Valida se um valor щ do tipo object
	// @access		public	
	// @param		value mixed	Valor a ser testado
	// @return		bool
	// @static	
	//!----------------------------------------------
	function isObject($value) {
		return is_object($value);
	}
	
	//!----------------------------------------------
	// @function	TypeUtils::isInstanceOf
	// @desc		Verifica se um determinado objeto щ
	//				uma instтncia da classe fornecida no parтmetro $className
	// @param		object object		Objeto a ser testado
	// @param		className string	Nome da classe
	// @param		recurse bool		Testar os ascendentes do objeto
	// @return		bool
	// @note		Utiliza recursividade para os nэveis 
	//				superiores se o parтmetro $recurse for TRUE
	// @static	
	//!----------------------------------------------
	function isInstanceOf($object, $className, $recurse=TRUE) {
		if (!is_object($object))
			return FALSE;
		$result = (strtolower(get_class($object)) == strtolower($className));
		if ($recurse)
			$result = ($result || is_subclass_of($object, $className));
		return $result;
	}
	
	//!----------------------------------------------
	// @function	TypeUtils::isResource
	// @desc		Valida se um valor щ do tipo resource
	// @access		public	
	// @param		value mixed	Valor a ser testado
	// @return		string Tipo de resource. Retorna FALSE se o valor nуo pertencer р classe resource
	// @static	
	//!----------------------------------------------
	function isResource($value) {
		if (is_resource($value)) {
			return get_resource_type($value);
		} else {
			return FALSE;
		}
	}
	
	//!----------------------------------------------
	// @function	TypeUtils::isNull
	// @desc		Verifica se um determinado valor щ NULL
	// @access		public	
	// @param		value mixed	Valor a ser testado
	// @param		strict bool	"FALSE" Se TRUE, leva em consideraчуo o tipo do dado
	// @return		bool
	// @static	
	//!----------------------------------------------
	function isNull($value, $strict = FALSE) {
		return ($strict) ? (NULL === $value) : (NULL == $value);
	}
	
	//!----------------------------------------------
	// @function	TypeUtils::ifNull
	// @desc		Realiza o teste se um valor щ NULL, retornando um
	//				valor padrуo determinado
	// @access		public	
	// @param		value mixed	Valor a ser testado
	// @param		default mixed	"NULL" Valor padrуo quando o teste de null for verdadeiro
	// @return		mixed $default se $value for null, em caso contrсrio $value
	// @static	
	//!----------------------------------------------
	function ifNull($value, $default = NULL) {
		if (TypeUtils::isNull($value, TRUE)) {		
			return $default;
		} else {
			return $value;
		}
	}
	
	//!----------------------------------------------
	// @function	TypeUtils::isBoolean
	// @desc		Verifica se um valor щ do tipo boolean
	// @access		public	
	// @param		value mixed	Valor a ser testado
	// @return		bool
	// @see			TypeUtils::isTrue
	// @see			TypeUtils::isFalse
	// @static	
	//!----------------------------------------------
	function isBoolean($value) {
		return TypeUtils::isTrue($value) || TypeUtils::isFalse($value);
	}
	
	//!----------------------------------------------
	// @function	TypeUtils::isTrue
	// @desc		Verifica se um valor щ TRUE utilizando comparaчуo por valor e tipagem
	// @access		public	
	// @param		value mixed	Valor a ser testado
	// @return		bool
	// @see			TypeUtils::isBoolean
	// @see			TypeUtils::isFalse
	// @static	
	//!----------------------------------------------
	function isTrue($value) {
		return (TRUE === $value);
	}
	
	//!----------------------------------------------
	// @function	TypeUtils::isFalse
	// @desc		Verifica se um valor щ FALSE utilizando comparaчуo por valor e tipagem
	// @access		public	
	// @param		value mixed	Valor a ser testado
	// @return		bool
	// @see			TypeUtils::isBoolean
	// @see			TypeUtils::isTrue
	// @static	
	//!----------------------------------------------
	function isFalse($value) {
		return (FALSE === $value);
	}
	
	//!----------------------------------------------
	// @function	TypeUtils::ifFalse
	// @desc		Realiza o teste se um valor щ FALSE, retornando um
	//				valor padrуo determinado
	// @access		public	
	// @param		value mixed	Valor a ser testado
	// @param		default mixed	"FALSE" Valor a ser retornado quando o valor testado for false
	// @return		mixed $default se $value for false, em caso contrсrio $value
	// @static	
	//!----------------------------------------------
	function ifFalse($value, $default = FALSE) {
		if (TypeUtils::isFalse($value)) {		
			return $default;
		} else {
			return $value;
		}
	}
	
	//!----------------------------------------------
	// @function	TypeUtils::toBoolean
	// @desc		Converte um valor qualquer para sua representaчуo booleana
	// @access		public	
	// @param		value mixed	Valor a ser convertido
	// @return		bool
	// @static	
	//!----------------------------------------------
	function toBoolean($value) {
		return (bool)$value;
	}
}
?>