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
// $Header: /www/cvsroot/php2go/p2gConfig.php,v 1.46 2005/09/01 20:03:27 mpont Exp $
// $Date: 2005/09/01 20:03:27 $
// $Revision: 1.46 $
	
	// {{{
	// }}} Define a raiz do framework

	define("PHP2GO_ROOT", str_replace("\\", "/", dirname(__FILE__)) . '/');
	
	// {{{
	// }}} Inclui e instancia os m�dulos de inicializa��o e configura��o do framework
	
	require_once(PHP2GO_ROOT . 'errorHandler.php');
	require_once(PHP2GO_ROOT . 'p2gLib.php');
	require_once(PHP2GO_ROOT . 'core/Conf.class.php');
	require_once(PHP2GO_ROOT . 'core/Init.class.php');
	require_once(PHP2GO_ROOT . 'core/LanguageBase.class.php');
	require_once(PHP2GO_ROOT . 'core/LocaleNegotiator.class.php');

	$Conf =& Conf::getInstance();
	$Init =& Init::getInstance();
	
	// }}}
	// {{{ Constantes para caminhos globais e absolutos dentro do framework
	
	// @const PHP2GO_ABSOLUTE_PATH "ABSOLUTE_URI"
	// Representa a URL absoluta do framework, informada no vetor de configura��es do usu�rio
	define("PHP2GO_ABSOLUTE_PATH", $Conf->getConfig('ABSOLUTE_URI'));	
	// @const PHP2GO_OFFSET_PATH "Retorno da fun��o getPhp2GoOffset(), em p2gLib.php"
	// Caminho relativo calculado entre o dom�nio atual e a URL informada para o PHP2Go
	$offset = getPhp2GoOffset();
	define("PHP2GO_OFFSET_PATH", ($offset !== FALSE ? $offset : PHP2GO_ABSOLUTE_PATH));		
	// @const PHP2GO_ICON_PATH "PHP2GO_ABSOLUTE_PATH . 'resources/icon'"
	// Define o caminho absoluto http para o diret�rio de �cones e imagens do PHP2Go
	define("PHP2GO_ICON_PATH", PHP2GO_ABSOLUTE_PATH . "resources/icon/");	
	// @const PHP2GO_JAVASCRIPT_PATH "PHP2GO_ABSOLUTE_PATH . 'resources/jsrun/'"
	// Constante a ser utilizada no momento de inserir scripts JavaScript que est�o inclu�dos no framework
	define("PHP2GO_JAVASCRIPT_PATH", PHP2GO_ABSOLUTE_PATH . "resources/jsrun/");
	// @const PHP2GO_CACHE_PATH "PHP2GO_ROOT . 'cache/'"
	// Constante que define o caminho para o diret�rio 'cache/' do PHP2Go, utilizado para o armazenamento de arquivos tempor�rios
	define("PHP2GO_CACHE_PATH", PHP2GO_ROOT . "cache/");	
	// @const PHP2GO_TEMPLATE_PATH "PHP2GO_ROOT . 'resources/template/'"
	// Constante que representa o caminho no servidor onde os templates HTML do PHP2Go est�o armazenados
	define("PHP2GO_TEMPLATE_PATH", PHP2GO_ROOT . "resources/template/");	
	
	// {{{
	// }}} Outras constantes
	
	// @const PHP2GO_VERSION "0.3.1"
	/// Vers�o do framework
	define("PHP2GO_VERSION", "0.3.1");	
	// @const PHP2GO_RELEASE_DATE "01/09/2005"
	// Data de lan�amento da �ltima vers�o
	define("PHP2GO_RELEASE_DATE", "01/09/2005");	
	// @const PHP2GO_INCLUDE_KEY "php2go"
	// Nome da chave de inclus�o de m�dulos padr�o a ser utilizada
	define("PHP2GO_INCLUDE_KEY", 'php2go');	
	// @const PHP2GO_DIRECTORY_SEPARATOR "/"
	// Separador padr�o de diret�rios do framework
	define("PHP2GO_DIRECTORY_SEPARATOR", '/');	
	// @const PHP2GO_PATH_SEPARATOR
	// Separador padr�o para PATH
	define("PHP2GO_PATH_SEPARATOR", (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN' ? ';' : ':'));
	// @const LONG_MAX
	// Valor m�ximo de n�mero inteiro com sinal
	define('LONG_MAX', is_int(2147483648) ? 9223372036854775807 : 2147483647);	
	// @const LONG_MIN
	// Valor m�nimo de n�mero inteiro com sinal
	define('LONG_MIN', -LONG_MAX - 1);	
	// @const PHP2GO_I18N_PATTERN "/#i18n.([^#]+)#/"
	// Padr�o para refer�ncia � mensagens internacionalizadas em templates e defini��es de formul�rio
	define('PHP2GO_I18N_PATTERN', '/#i18n:([^#]+)#/');
	// @const PHP2GO_MASK_PATTERN ""
	// Express�o regular de valida��o de m�scaras para campos editable ou filtros de busca
	define('PHP2GO_MASK_PATTERN', "/^(CPFCNPJ|CURRENCY|DATE|EMAIL|FLOAT|(FLOAT)(\-([1-9][0-9]*)\:([1-9][0-9]*))?|INTEGER|LOGIN|TIME|URL|(ZIP)(\-?([1-9])\:?([1-9])))$/");	
	
	// {{{
	// }}} Vari�veis de inicializa��o do PHP

	ini_set('magic_quotes_gpc', 'off');
	ini_set('register_argc_argv', 'off');
	ini_set('register_globals', 'off');	
	ini_set('short_open_tag', 'off');
	ini_set('variables_order', 'EPROSGC');
	
	// {{{
	// }}} M�dulos obrigat�rios

	import('php2go.base.Php2Go');
	import('php2go.base.Php2GoError');
	import('php2go.base.Registry');
	import('php2go.util.System');
	import('php2go.util.TypeUtils');
	import('php2go.db.Db');
	
	// }}}
	// {{{ Configura��o de tratamento de erros	
	
	error_reporting(E_ERROR | E_PARSE | E_COMPILE_ERROR);
	set_error_handler("php2GoErrorHandler");	
	
	// {{{
	// }}} Registra a fun��o de destrui��o dos objetos
	
	register_shutdown_function("destroyPHP2GoObjects");
	
?>