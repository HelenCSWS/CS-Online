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
// $Header: /www/cvsroot/php2go/core/validation/EmailValidator.class.php,v 1.6 2005/01/24 16:08:58 mpont Exp $
// $Date: 2005/01/24 16:08:58 $

//------------------------------------------------------------------
import('php2go.validation.Validator');
//------------------------------------------------------------------

//!-----------------------------------------------------------------
// @class		EmailValidator
// @desc		Classe que executa validação sintática de endereços
//				de correio eletrônico
// @package		php2go.validation
// @extends		Validator
// @author		Marcos Pont
// @version		$Revision: 1.6 $
//!-----------------------------------------------------------------
class EmailValidator extends Validator
{
	var $fieldLabel;	// @var fieldLabel string		Rótulo do campo que está sendo validado
	var $errorMessage;	// @var errorMessage string		Mensagem de erro
	
	//!-----------------------------------------------------------------
	// @function	EmailValidator::EmailValidator
	// @desc		Construtor da classe
	// @access		public
	// @param		params array	Parâmetros para o validador
	//!-----------------------------------------------------------------
	function EmailValidator($params = NULL) {	
		Validator::Validator();
		if (TypeUtils::isArray($params)) {
			if (isset($params['fieldLabel']))
				$this->fieldLabel = $params['fieldLabel'];			
		}		
	}
	
	//!-----------------------------------------------------------------
	// @function	EmailValidator::execute
	// @desc		Executa a validação de um endereço de e-mail
	// @access		public
	// @param		value string	Endereço de e-mail
	// @return		bool
	//!-----------------------------------------------------------------
	function execute($value) {
		$value = TypeUtils::parseString($value);
		if (preg_match("/^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/", $value)) {
            $result = TRUE;
        } else {
			$result = FALSE;
        }		
		if ($result === FALSE && isset($this->fieldLabel)) {
			$maskLabels = PHP2Go::getLangVal('FORM_MASKS_DATA_LABEL');			
			$this->errorMessage = PHP2Go::getLangVal('ERR_FORM_FIELD_INVALID_INPUT', array($this->fieldLabel, $maskLabels['EMAIL']));
		}
		return $result;
	}	
	
	//!-----------------------------------------------------------------
	// @function	EmailValidator::getError
	// @desc		Retorna a mensagem de erro resultante da validação
	// @access		public
	// @return		string Mensagem de erro
	//!-----------------------------------------------------------------
	function getError() {
		return $this->errorMessage;
	}	
}
?>