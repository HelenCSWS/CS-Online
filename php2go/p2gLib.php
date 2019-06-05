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
// $Header: /www/cvsroot/php2go/p2gLib.php,v 1.27 2005/08/30 15:09:22 mpont Exp $
// $Date: 2005/08/30 15:09:22 $
// $Revision: 1.27 $

//!------------------------------------------------------------------
// @function	import
// @desc		Inclui um m�dulo do PHP2Go atrav�s de seu caminho na �rvore de m�dulos
// @param		modulePath string		Caminho do m�dulo na �rvore
// @param		extension string		"class.php" Extens�o do m�dulo
// @param		return bool			"FALSE" Indica que o conte�do gerado pela inclus�o deve ser retornado
// @return		bool
// @note		Esta fun��o recebe nomes de m�dulos do tipo pasta1.pasta2.Modulo, 
//				transformando a string em um caminho na �rvore de diret�rios do servidor
// @note		Para utilizar classes que n�o pertencem ao PHP2Go, crie a posi��o INCLUDE_PATH 
//				no vetor de configura��es P2G_USER_CFG, contendo um vetor associativo chave => 
//				caminho. Ex: para incluir /www/project/classes/my.class.php, inclua project => 
//				'/www/project/' em INCLUDE_PATH e execute import('project.classes.my');
//!------------------------------------------------------------------
function import($modulePath, $extension='class.php', $return=FALSE) {

	if (isset($cache[$modulePath])) 
		return TRUE;
	$Lang =& LanguageBase::getInstance();
	// busca o caminho completo do m�dulo ou do diret�rio
	$path = getRealPath($modulePath, $extension);
	if ($path) {
		// importa��o de diret�rio
		if (is_dir($path)) {
			if (!importDirectory($path)) {
				trigger_error(sprintf($Lang->getLanguageValue('ERR_CANT_LOAD_DIR'), $modulePath), E_USER_ERROR);
				return FALSE;
			} else {
				$cache[$modulePath] = TRUE;
				return TRUE;
			}
		}
		// importa��o de m�dulo simples
		else if (is_file($path)) {
			if (!importFile($path, $extension, $return)) {
				trigger_error(sprintf($Lang->getLanguageValue('ERR_CANT_LOAD_MODULE'), $modulePath), E_USER_ERROR);
				return FALSE;
			} else {
				$cache[$modulePath] = TRUE;
				return TRUE;
			}
		}
		// falha na importa��o
		else {
			trigger_error(sprintf($Lang->getLanguageValue('ERR_CANT_LOAD_MODULE'), $modulePath), E_USER_ERROR);
			return FALSE;
		}
	} else {
		trigger_error(sprintf($Lang->getLanguageValue('ERR_CANT_LOAD_MODULE'), $modulePath), E_USER_ERROR);
		return FALSE;		
	}
}

//!------------------------------------------------------------------
// @function	getRealPath
// @desc		Resolve o caminho completo na �rvore de diret�rios para
//				um m�dulo ou package a partir de seu caminho especificado
//				com separa��o por pontos (foo.bar.baz.ModuleName)
// @param		modulePath string		Caminho do m�dulo ou package
// @param		extension string		"class.php" Extens�o do m�dulo a ser inclu�do
// @return		string	Caminho completo para o m�dulo
// @note		Retorna FALSE se o caminho n�o puder ser encontrado utilizando os INCLUDE
//				PATHs registrados na configura��o
//!------------------------------------------------------------------
function getRealPath($modulePath, $extension = 'class.php') {	
	if (preg_match("/^([^\.]+)\.(.*\.)?([^\.]+)/i", $modulePath, $matches)) {
		if ($matches[1] == PHP2GO_INCLUDE_KEY) {
			$basePath = PHP2GO_ROOT . 'core/' . strtr($matches[2], '.', PHP2GO_DIRECTORY_SEPARATOR);
		} else {
			$Conf =& Conf::getInstance();
			$includePaths = $Conf->getConfig('INCLUDE_PATH');
			if ($includePaths && array_key_exists($matches[1], $includePaths)) {
				$basePath = $includePaths[$matches[1]] . strtr($matches[2], '.', PHP2GO_DIRECTORY_SEPARATOR);
			} else {
				$basePath = FALSE;
			}			
		}
		if ($basePath) {			
			if ($matches[3] == '*')
				return $basePath;
			else
				return $basePath . $matches[3] . '.' . $extension;
		} else {
			return $basePath;
		}		
	}
}

//!------------------------------------------------------------------
// @function	importFile
// @desc		Importa um arquivo simples, a partir de seu caminho completo
// @param		filePath string	Caminho do arquivo
// @param		return bool		"FALSE" Indica se o conte�do da inclus�o deve ser retornado
// @return		bool
//!------------------------------------------------------------------
function importFile($filePath, $return=FALSE) {	
	if ($return === TRUE) {
		return (include_once($filePath));
	} else {
		if (!include_once($filePath))
			return FALSE;
		else
			return TRUE;
	}
}

//!------------------------------------------------------------------
// @function	includeFile
// @desc		Inclui um arquivo a partir de seu caminho completo
// @param		filePath string	Caminho do arquivo
// @param		return bool		"FALSE" Indica se o conte�do da inclus�o deve ser retornado
// @return		bool
// @note		Ao contr�rio da fun��o importFile, a fun��o includeFile replica
//				arquivos j� carregados (utiliza a fun��o include do PHP)
//!------------------------------------------------------------------
function includeFile($filePath, $return=FALSE) {
	if ($return === TRUE) {
		return (include($filePath));
	} else {
		if (!@include($filePath)) {
			return FALSE;
		} else {		
			return TRUE;
		}
	}
}

//!------------------------------------------------------------------
// @function	importDirectory
// @desc		Importa o conte�do inteiro de um diret�rio
// @param		directoryPath string	Caminho do diret�rio
// @return		bool
//!------------------------------------------------------------------
function importDirectory($directoryPath) {
	$Lang =& LanguageBase::getInstance();
	$directory = @dir($directoryPath);
	if (FALSE === $directory)
		return FALSE;
	while ($file = $directory->read()) {
		if ($file == '.' || $file == '..' || is_dir($directoryPath . '/' . $file)) {
			continue;
		}
		if (!include_once($directoryPath . PHP2GO_DIRECTORY_SEPARATOR . $file)) {
			trigger_error(sprintf($Lang->getLanguageValue('ERR_CANT_LOAD_DIR_MODULE'), $file, $directoryPath), E_USER_ERROR);
			return FALSE;
		}
	}
	return TRUE;
}

//!--------------------------------------------------------------
// @function	classForPath
// @desc		Importa e retorna o nome de uma classe a partir de seu caminho
//				no padr�o PHP2Go (pontos como separadores)
// @param		path string		Caminho da classe
// @return		string Nome da classe ou FALSE se n�o for poss�vel import�-la
// @note		Exemplo: para o caminho myproject.person.Person, deve retornar Person
//!--------------------------------------------------------------
function classForPath($path) {
	import($path);
	$className = basename(str_replace('.', '/', $path));
	if (class_exists($className))
		return $className;
	return FALSE;
}

//!--------------------------------------------------------------
// @function	getPhp2GoOffset
// @desc		Calcula o caminho que deve ser seguido para chegar � raiz 
//				do PHP2Go a partir da pasta atual
// @return		string Caminho calculado
//!--------------------------------------------------------------
function getPhp2GoOffset() {
	$matches = NULL;
	ereg("(https?://)([^/]+)/?(.*)?", substr(PHP2GO_ABSOLUTE_PATH, 0, -1), $matches);
	if (!empty($matches[2]) && $matches[2] != @$_SERVER['SERVER_NAME'])
		return FALSE;
	$path1 = (string)$matches[3];
	$path2 = substr($_SERVER['PHP_SELF'], 1);
	$equal = TRUE;
	$back = '';
	$forward = '';
	while ($path1 != '' || $path2 != '') {
		unset($matches1);
		unset($matches2);
		ereg("([^/]+)(/)?(.*)?", $path1, $matches1);
		ereg("([^/]+)(/)?(.*)?", $path2, $matches2);
		if ((string)$matches1[1] != (string)$matches2[1] || !$equal) {
			if ($matches2[2] == '/')
				$back .= '../';
			$forward .= $matches1[1] . $matches1[2];
			$equal = FALSE;
		}
		$path1 = $matches1[3];
		$path2 = $matches2[3];
	}
	$finalPath = $back . $forward;
	return $finalPath;
}

//!-----------------------------------------------
// @function	jsrsDispatch
// @desc		Configura tratadores de eventos JSRS, utilizando
//				a classe ServiceJSRS. Fun��o criada para manter compatiblidade
//				com a bibiliteca Jsrs.lib.php, que foi removida do PHP2Go
// @param		handlersList string	Lista de tratadores, separada por espa�os simples
// @return		void
//!-----------------------------------------------
function jsrsDispatch($handlersList) {
	import('php2go.util.service.ServiceJSRS');
	$Service = new ServiceJSRS();
	$handlersList = trim((string)$handlersList);
	$handlers = explode(' ', $handlersList);
	foreach ($handlers as $handler)
		$Service->registerHandler($handler);
	$Service->handleRequest();
}

//!-----------------------------------------------
// @function	destroyPHP2GoObjects
// @desc		Destr�i os objetos criados cujos destrutores foram 
//				registrados no escopo global e executa as fun��es
//				de shutdown registradas pelo usu�rio
// @return		void
// @note		Esta fun��o � registrada como shutdown_function
//				na inicializa��o global definida em p2gConfig.php
//!-----------------------------------------------
function destroyPHP2GoObjects() {
	global $PHP2Go_destructor_list, $PHP2Go_shutdown_funcs;
	import('php2go.util.TypeUtils');	
	if (is_array($PHP2Go_destructor_list) && !empty($PHP2Go_destructor_list)) {
		foreach($PHP2Go_destructor_list as $destructor) {
			$object =& $destructor[0];
			$method = $destructor[1];
			$object->$method();
			unset($object);
		}
	}
	if (is_array($PHP2Go_shutdown_funcs) && !empty($PHP2Go_shutdown_funcs)) {
		foreach($PHP2Go_shutdown_funcs as $function) {
			if (sizeOf($function) == 3) {
				$object =& $function[0];
				$method = $function[1];
				$args = implode(',', $function[2]);
				eval("\$object->$method($args);");
			} else {
				call_user_func_array($function[0], $function[1]);
			}
		}
	}
}

//!-----------------------------------------------
// @function	println
// @desc		Fun��o utilit�ria para exibi��o de uma string
//				seguida de uma quebra de linha
// @param		str string	String a ser exibida
// @param		nl string	"&lt;BR&gt;" Quebra de linha
// @return		void
//!-----------------------------------------------
function println($str, $nl='<BR>') {
	echo $str . $nl;
}

//!-----------------------------------------------
// @function	dumpVariable
// @desc		Imprime a descri��o de uma vari�vel utilizando
//				pr�-formata��o, para melhor visualiza��o
// @param		var mixed	Vari�vel
// @return		void
//!-----------------------------------------------
function dumpVariable($var) {
	print '<PRE>';
	var_dump($var);
	print '</PRE>';	
}

//!-----------------------------------------------
// @function	dumpArray
// @desc		Retorna ou imprime a forma apresent�vel
//				da primeira dimens�o de um array. �til
//				para a valida��o de arrays associativos 
//				(hash tables) ou unidimensionais utilizados
//				nas aplica��es
// @param		arr array	Vari�vel a ser exibida
// @param		return bool	"TRUE" Retornar ou imprimir o conte�do do array
// @param		stringLimit int "200" Limite de caracteres para entradas do tipo string
// @return		string Retorna o conte�do do array, se o par�metro $return for TRUE
//!-----------------------------------------------
function dumpArray($arr, $return=TRUE, $stringLimit=200) {
	$r = array();
	foreach ($arr as $k => $v) {
		if (is_string($v)) {
			$r[] = $k . "=>'" . (strlen($v) > $stringLimit ? substr($v, 0, $stringLimit) . "...(" . strlen($v) . ")" : $v) . "'";
		} else {
			$r[] = $k . '=>' . $v;
		}
	}
	if ($return)
		return "[" . implode(", ", $r) . "]";
	else
		print "[" . implode(", ", $r) . "]";
}

//!-----------------------------------------------
// @function	exportVariable
// @desc		Retorna a descri��o de uma vari�vel, utilizando
//				ou n�o pr�-formata��o
// @param		var mixed		Vari�vel
// @param		formatted bool	"FALSE" Utilizar pr�-formata��o
// @return		string Conte�do exportado da vari�vel
//!-----------------------------------------------
function exportVariable($var, $formatted=FALSE) {
	// objetos com m�todo toString
	if (is_object($var) && method_exists($var, 'tostring'))
		return $var->toString();
	if (function_exists('var_export')) {
		$export = var_export($var, TRUE);
	} else {
		ob_start();
		var_dump($var);
		$export = ob_get_contents();
		ob_end_clean();
	}
	if ($formatted)
		return '<PRE>' . $export . '</PRE>';
	else
		return $export;
}

//!-----------------------------------------------
// @function	findArrayPath
// @desc		Procura por um valor em um array multidimensional
//				utilizando um "caminho" de chaves, utilizando um separador
// @param		arr array			Array multidimensional
// @param		path string			Caminho de pesquisa
// @param		separator string	"." Separador de n�veis no caminho fornecido
// @param		fallback mixed		"NULL" Valor retornado caso o caminho n�o seja encontrado
// @return		mixed Valor encontrado ou valor do par�metro $fallback
//!-----------------------------------------------
function findArrayPath($arr, $path, $separator='.', $fallback=NULL) {
	if (!is_array($arr))
		return $fallback;
	$parts = explode($separator, $path);
	if (sizeof($parts) == 1) {
		return (isset($arr[$path]) ? $arr[$path] : $fallback);
	} else {
		$i = 0;
		$base = $arr;
		$size = sizeof($parts);
		while ($i < $size) {
			if (!isset($base[$parts[$i]]))
				return $fallback;
			else
				$base = $base[$parts[$i]];
			if ($i < ($size-1) && !is_array($base))
				return $fallback;
			$i++;
		}
		return $base;
	}	
}
?>