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
// $Header: /www/cvsroot/php2go/core/validation/UploadValidator.class.php,v 1.4 2005/06/27 20:58:37 mpont Exp $
// $Date: 2005/06/27 20:58:37 $

//------------------------------------------------------------------
import('php2go.file.FileUpload');
import('php2go.validation.Validator');
//------------------------------------------------------------------

//!-----------------------------------------------------------------
// @class		UploadValidator
// @desc		Classe que valida o upload de arquivos
// @package		php2go.validation
// @uses		TypeUtils
// @extends		Validator
// @author		Marcos Pont
// @version		$Revision: 1.4 $
// @note		Exemplo de uso:<BR>
//				<PRE>
//
//				$value = 'MY_FILE'; // nome do campo
//				$params = array('maxfilesize'=>'2M', 'allowedtypes'=>array('image/gif,image/jpeg'), 'savepath'=>'images/', 'savemode'=>'0755', 'overwrite'=>TRUE);
//				if (Validator::validate('php2go.validation.UploadValidator', $value, $params)) {
//				&nbsp;&nbsp;&nbsp;print 'ok';
//				}
//
//				</PRE>
//!-----------------------------------------------------------------
class UploadValidator extends Validator
{
	var $Uploader = NULL;		// @var Uploader FileUpload object	"NULL" Inst�ncia da classe FileUpload utilizada para executar a opera��o
	var $errorMessage;			// @var errorMessage string			Mensagem de erro	
	
	//!-----------------------------------------------------------------
	// @function	UploadValidator::UploadValidator
	// @desc		Construtor da classe
	// @access		public
	// @param		params array	"NULL" Par�metros para o validador
	//!-----------------------------------------------------------------
	function UploadValidator($params = NULL) {	
		Validator::Validator();
		$this->Uploader =& FileUpload::getInstance();
	}
	
	//!-----------------------------------------------------------------
	// @function	UploadValidator::execute
	// @desc		Executa a valida��o do upload de um arquivo
	// @access		public
	// @param		upload array	Vetor contendo o nome do campo a ser validado e as configura��es de valida��o
	// @return		bool
	//!-----------------------------------------------------------------
	function execute($upload) {
		if (isset($upload['MAXFILESIZE']))
			$this->Uploader->setMaxFileSize($upload['MAXFILESIZE']);
		if (isset($upload['ALLOWEDTYPES']))
			call_user_func_array(array(&$this->Uploader, 'setAllowedTypes'), TypeUtils::toArray($upload['ALLOWEDTYPES']));
		if (isset($upload['OVERWRITE']))
			$this->Uploader->setOverwriteFiles($upload['OVERWRITE']);
		if (isset($upload['FIELDNAME'])) {
			$idx = $this->Uploader->addHandler($upload['FIELDNAME'], @$upload['SAVEPATH'], @$upload['SAVENAME'], @$upload['SAVEMODE'], @$upload['SAVEFUNCTION']);
			if ($this->Uploader->upload($idx)) {
				return TRUE;
			} else {				
				$this->errorMessage = $this->Uploader->getErrorAt($idx);
				return FALSE;
			}
		} else {
			return FALSE;
		}
	}	
	
	//!-----------------------------------------------------------------
	// @function	UploadValidator::getError
	// @desc		Retorna a mensagem de erro resultante da valida��o
	// @access		public
	// @return		string Mensagem de erro
	//!-----------------------------------------------------------------
	function getError() {
		return $this->errorMessage;
	}	
}
?>