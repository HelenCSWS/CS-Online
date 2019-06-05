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
// $Header: /www/cvsroot/php2go/jsrs/csvdbpersist.php,v 1.9 2005/01/21 17:20:02 mpont Exp $
// $Date: 2005/01/21 17:20:02 $
// $Revision: 1.9 $

//------------------------------------------------------------------
require_once("../p2gConfig.php");
//------------------------------------------------------------------

jsrsDispatch("processPost deleteReg");

//!-----------------------------------------------------------------
// @function	processPost
// @desc		Realiza a alteração ou a inclusão de um registro de
// 				uma tabela através da classe FormDataBind
// @param 		values string		Valores da inserção/alteração
// 									no formato coluna1#valor1|...|colunaN#valorN
// @param 		table string		Nome da tabela
// @param 		pk string			Chave primária da tabela
// @return		mixed Retorna o último id inserido em caso de inserção com sucesso, 1 em
//				caso de alteração com sucesso ou uma mensagem de erro em caso contrário
//!-----------------------------------------------------------------
function processPost($values, $table, $pk) {
	$Lang =& LanguageBase::getInstance();		
	$errorMsg = $Lang->getLanguageValue('ERR_DB_CSV_JSRS');		
	$_Db =& Db::getInstance();
	$arrFields = getFields($values);
	if ($arrFields[$pk] == '') {
		$_Db->insert($table, $arrFields, TRUE);
		if ($_Db->affectedRows() > 0) return $_Db->lastInsertId();
	} else {
		$_Db->update($table, $arrFields, $pk . ' = ' . $arrFields[$pk]);
		if ($_Db->affectedRows() > 0) return 1;
	}
	$_Db->close();
	return $errorMsg;
}
//!-----------------------------------------------------------------
// @function 	deleteReg
// @desc 		Realiza uma exclusão de registro solicitada através
// 				da barra de ferramentas gerada pela classe FormDataBind
// @param 		table string		Nome da tabela
// @param 		pk string			Chave primária da tabela
// @param 		value string		Valor de $pk a ser excluído
// @return		mixed Retorna 1 se a operação foi realizada com sucesso ou uma mensagem de erro
//!-----------------------------------------------------------------
function deleteReg($table, $pk, $value) {
	$Lang =& LanguageBase::getInstance();		
	$errorMsg = $Lang->getLanguageValue('ERR_DB_CSV_JSRS');
	$_Db =& Db::getInstance();
	$_Db->delete($table, $pk . " = " . $value);
	if ($_Db->affectedRows() > 0) {
		return 1;
	}
	$_Db->close();
	return $errorMsg;
}

//!-----------------------------------------------------------------
// @function	getFields
// @desc		Monta um vetor com os campos a serem utilizados no
//				comando DML a partir da string enviada no parâmetro
//				values da função processPost()
// @param		values string		String com campos e valores a serem utilizados
// @return		array Vetor associativo dos campos e valores
//!-----------------------------------------------------------------
function getFields($values) {
	$arrFields = array();
	$values = explode("#", $values);
	for($i = 0; $i < count($values); $i++) {
		$fields = array();
		$fields = explode("|", $values[$i]);
		if (count($fields) == 2) {
			$arrFields[$fields[0]] = $fields[1];
		}
	}
	return $arrFields;
}
?>