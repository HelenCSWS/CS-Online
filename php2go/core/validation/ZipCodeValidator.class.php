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
// $Header: /www/cvsroot/php2go/core/validation/ZipCodeValidator.class.php,v 1.1 2005/01/24 17:36:55 mpont Exp $
// $Date: 2005/01/24 17:36:55 $

//------------------------------------------------------------------
import('php2go.validation.Validator');
//------------------------------------------------------------------

//!-----------------------------------------------------------------
// @class		ZipCodeValidator
// @desc		Classe que valida valores de c�digo postal, com N
//				digitos, um separador (-) e outros N d�gitos
// @package		php2go.validation
// @uses		TypeUtils
// @extends		Validator
// @author		Marcos Pont
// @version		$Revision: 1.1 $
// @note		Exemplo de uso:<BR>
//				<PRE>
//
//				$value = '90504-333';
//				if (Validator::validate('php2go.validation.ZipCodeValidator', $value, array('limiters' => array(5,3)))) {
//				&nbsp;&nbsp;&nbsp;print 'ok';
//				}
//
//				</PRE>
//!-----------------------------------------------------------------
class ZipCodeValidator extends Validator
{
	var $limiters;		// @var limiters array		N�mero de d�gitos antes e depois do espa�ador
	var $fieldLabel;	// @var fieldLabel string		R�tulo do campo que est� sendo validado
	var $errorMessage;	// @var errorMessage string		Mensagem de erro

	//!-----------------------------------------------------------------
	// @function	ZipCodeValidator::ZipCodeValidator
	// @desc		Construtor da classe
	// @access		public
	// @param		params array	"NULL" Par�metros para o validador
	// @note		Conjunto de par�metros:
	//				limiters => Vetor com o n�mero d�gitos antes e depois do separador
	//!-----------------------------------------------------------------
	function ZipCodeValidator($params = NULL) {
		Validator::Validator();
		if (TypeUtils::isArray($params)) {
			if (isset($params['limiters']) && TypeUtils::isArray($params['limiters']) && sizeof($params['limiters']) == 2) {
				$this->limiters = $params['limiters'];
				$this->limiters[0] = max(1, $this->limiters[0]);
				$this->limiters[1] = max(1, $this->limiters[1]);				
			}
			if (isset($params['fieldLabel']))
				$this->fieldLabel = $params['fieldLabel'];			
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	ZipCodeValidator::execute
	// @desc		Executa a valida��o de c�digo postal em um determinado valor
	// @access		public
	// @param		value mixed	Valor a ser validado
	// @return		bool
	//!-----------------------------------------------------------------
	function execute($value) {
		if (isset($this->limiters)) {
			$result = (ereg("/^[0-9]{" . $this->limiters[0] . "}\.[0-9]{" . $this->limiters[0] . "}$/", $value));
		} else {
			$result = FALSE;
		}
		if ($result === FALSE && isset($this->fieldLabel)) {
			$maskLabels = PHP2Go::getLangVal('FORM_MASKS_DATA_LABEL');			
			$this->errorMessage = PHP2Go::getLangVal('ERR_FORM_FIELD_INVALID_INPUT', array($this->fieldLabel, $maskLabels['ZIP']));
		}
		return $result;		
	}	
	
	//!-----------------------------------------------------------------
	// @function	ZipCodeValidator::getError
	// @desc		Retorna a mensagem de erro resultante da valida��o
	// @access		public
	// @return		string Mensagem de erro
	//!-----------------------------------------------------------------
	function getError() {
		return $this->errorMessage;
	}	
}
?>