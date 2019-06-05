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
// $Header: /www/cvsroot/php2go/core/base/Registry.class.php,v 1.4 2005/01/21 17:20:01 mpont Exp $
// $Date: 2005/01/21 17:20:01 $

//!-----------------------------------------------------------------
// @class		Registry
// @desc		Classe utilitária que permite o armazenamento de variáveis
//				em um registro, na forma de um vetor associativo par => valor.
//				O repositório é inicializado com o valor da variável global
//				$GLOBALS
// @package		php2go.base
// @extends		PHP2Go
// @author		Marcos Pont
// @version		$Revision: 1.4 $
//!-----------------------------------------------------------------
class Registry extends PHP2Go
{
	var $entries;	// @var entries array	Vetor associativo de entradas do registro
	
	//!-----------------------------------------------------------------
	// @function	Registry::Registry
	// @desc		Construtor da classe
	// @access		public
	//!-----------------------------------------------------------------
	function Registry() {
		PHP2Go::PHP2Go();
		$this->entries =& $GLOBALS;
	}
	
	//!-----------------------------------------------------------------
	// @function	Registry::&getInstance
	// @desc		Retorna uma instância única da classe
	// @access		public
	// @return		Registry object		Instância da classe	
	// @static
	//!-----------------------------------------------------------------
	function &getInstance() {
		static $instance;
		if (!isset($instance))
			$instance =& new Registry();
		return $instance;
	}
	
	//!-----------------------------------------------------------------
	// @function	Registry::get
	// @desc		Método que busca no registro uma entrada, retornando
	//				o seu valor caso seja encontrada
	// @access		public
	// @param		variable string		Nome da entrada buscada
	// @return		mixed Valor da entrada ou NULL se não encontrada
	// @static
	//!-----------------------------------------------------------------
	function get($variable) {
		$Registry =& Registry::getInstance();
		if (array_key_exists($variable, $Registry->entries))
			return $Registry->entries[$variable];
		else
			return NULL;
	}
	
	//!-----------------------------------------------------------------
	// @function	Registry::set
	// @desc		Insere uma nova entrada no registro
	// @access		public
	// @param		variable string		Nome da nova entrada
	// @param		value mixed			Valor definido para a entrada
	// @return		void
	// @static
	//!-----------------------------------------------------------------
	function set($variable, $value) {
		$Registry =& Registry::getInstance();
		$Registry->entries[$variable] = $value;
	}
	
	//!-----------------------------------------------------------------
	// @function	Registry::remove
	// @desc		Remove uma entrada do registro
	// @access		public
	// @param		variable string		Nome da entrada
	// @return		bool
	//!-----------------------------------------------------------------
	function remove($variable) {
		$Registry =& Registry::getInstance();
		if (array_key_exists($variable, $Registry->entries)) {
			unset($Registry->entries[$variable]);
			return TRUE;
		}
		return FALSE;
	}
}
?>