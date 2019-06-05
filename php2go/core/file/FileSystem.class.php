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
// $Header: /www/cvsroot/php2go/core/file/FileSystem.class.php,v 1.12 2005/07/20 22:29:45 mpont Exp $
// $Date: 2005/07/20 22:29:45 $

//!-----------------------------------------------------------------
import('php2go.util.Number');
import('php2go.text.StringUtils');
//!-----------------------------------------------------------------

//!-----------------------------------------------------------------
// @class		FileSystem
// @desc		Através desta classe, é possível obter informações
//				sobre arquivos localizados no servidor, como nome,
//				extensão, caminho, modo, usuário e grupo, etc
// @package		php2go.file
// @extends 	PHP2Go
// @uses		Number
// @uses		StringUtils
// @uses		System
// @author		Marcos Pont
// @version		$Revision: 1.12 $
//!-----------------------------------------------------------------
class FileSystem extends PHP2Go 
{
	//!-----------------------------------------------------------------
	// @function	FileSystem::FileSystem
	// @desc		Cria uma instância do objeto de gerenciamento do sistema de arquivos
	// @access		public
	//!-----------------------------------------------------------------
	function FileSystem() {
		PHP2Go::PHP2Go();
	}
	
	//!-----------------------------------------------------------------
	// @function	FileSystem::&getInstance
	// @desc		Retorna uma instância única da classe FileSystem
	// @access		public
	// @return		FileSystem object
	// @static	
	//!-----------------------------------------------------------------
	function &getInstance() {
		static $instance;
		if (!isset($instance))
			$instance = new FileSystem();
		return $instance;			
	}

	//!-----------------------------------------------------------------
	// @function	FileSystem::exists
	// @desc		Verifica se um arquivo existe a partir de seu caminho
	// @access		public
	// @param		path string		Caminho completo do arquivo
	// @return		bool
	// @static	
	//!-----------------------------------------------------------------
	function exists($path) {
		return (@file_exists($path));
	}
	
	//!-----------------------------------------------------------------
	// @function	FileSystem::getContents
	// @desc		Método utilitário de leitura do conteúdo total de um arquivo
	// @access		public
	// @param		path string		Caminho completo do arquivo
	// @return		string Conteúdo do arquivo
	// @static
	//!-----------------------------------------------------------------
	function getContents($path) {
		if (function_exists('file_get_contents')) {
			$contents = @file_get_contents($path);
			if ($contents === FALSE) {
				PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CANT_READ_FILE', $path), E_USER_ERROR, __FILE__, __LINE__);
				return FALSE;
			}
			return $contents;
		} else {
			$fp = @fopen($path, 'rb');
			if ($fp === FALSE) {
				PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CANT_READ_FILE', $path), E_USER_ERROR, __FILE__, __LINE__);
				return FALSE;
			}
			$contents = fread($fp, filesize($path));
			fclose($fp);
			return $contents;
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	FileSystem::lastModified
	// @desc		Busca a data da última modificação de um arquivo ou diretório
	// @access		public
	// @param		path string		Caminho do arquivo ou diretório
	// @param		noCache bool	"TRUE" Se TRUE, limpa a cache do PHP (stat cache)
	// @return		int Timestamp de modificação
	// @static
	//!-----------------------------------------------------------------
	function lastModified($path, $noCache=TRUE) {
		if ($noCache)
			clearstatcache();
		return (FileSystem::exists($path) ? @filemtime($path) : FALSE);
	}

	//!-----------------------------------------------------------------
	// @function	FileSystem::getFileAttributes
	// @desc		Busca os atributos de um arquivo a partir do caminho
	// @access		public
	// @param		path string		Caminho para o arquivo
	// @return		array Um vetor contendo os atributos do arquivo ou FALSE em
	//				caso de erros
	// @note		Este método pode ser executado estaticamente, para buscar
	//				atributos de um arquivo sempre que for necessário
	// @note		Os atributos retornados no vetor são: name, extension,
	//				path, lastDir, type, mode, perms, size, aTime, mTime,
	//				userId, groupId, isFile, isDir, isLink, isReadable,
	//				isWriteable e isExecutable
	// @static	
	//!-----------------------------------------------------------------
	function getFileAttributes($path) {
		$FileSystem = FileSystem::getInstance();
		if (!$FileSystem->exists($path)) {
			return FALSE;
		} else {
			$pathInfo = $FileSystem->getPathInfo($path);
			$fileAttr = array();
			$fileAttr['name']			= '';
			$fileAttr['extension']		= '';
			$fileAttr['path']			= $pathInfo['fullPath'];
			$fileAttr['lastDir']		= $pathInfo['lastDir'];
			$fileAttr['type']			= filetype($path);
			$fileAttr['mode']			= fileperms($path);
			$fileAttr['perms']			= $FileSystem->getFilePermissions($path);
			$fileAttr['size']			= filesize($path);
			$fileAttr['aTime']			= fileatime($path);
			$fileAttr['mTime']			= filemtime($path);
			$fileAttr['cTime']			= filectime($path);
			$fileAttr['userId']			= fileowner($path);
			$fileAttr['groupId']		= filegroup($path);
			$fileAttr['isFile']			= TypeUtils::toBoolean(is_file($path));
			$fileAttr['isDir']			= TypeUtils::toBoolean(is_dir($path));
			$fileAttr['isLink']			= TypeUtils::toBoolean(is_link($path)) || (System::isWindows() && StringUtils::left($path, -4) == '.lnk');
			$fileAttr['isReadable']		= TypeUtils::toBoolean(is_readable($path));
			$fileAttr['isWriteable']	= TypeUtils::toBoolean(is_writeable($path));
			$fileAttr['isExecutable']	= !System::isWindows() && TypeUtils::toBoolean(is_executable($path));
			if ($fileAttr['isFile']) {
				$fileAttr['name']		= $pathInfo['file'];
				$fileAttr['extension']	= $FileSystem->getFileExtension($path);
			}
			if ($fileAttr['isLink'])
				$fileAttr['linkTarget'] = readlink($path);
			else
				$fileAttr['linkTarget'] = '';
			clearstatcache();
			return $fileAttr;
		}
	}

	//!-----------------------------------------------------------------
	// @function	FileSystem::getFullPath
	// @desc		Busca o caminho completo reorganizado do arquivo
	// @access		public
	// @param		path string		Caminho completo do arquivo
	// @return		string Caminho do arquivo reorganizado ou FALSE em caso de erros
	// @see			FileSystem::getPathInfo
	//!-----------------------------------------------------------------
	function getFullPath($path) {
		if (!$pathInfo = $this->getPathInfo($path)) {
			return FALSE;
		} else {
			return $pathInfo['fullPath'];
		}
	}

	//!-----------------------------------------------------------------
	// @function	FileSystem::getPathInfo
	// @desc		Detalha as informações sobre um caminho passado como parâmetro
	// @access		public
	// @param		path string 	Caminho a ser analisado
	// @return		array Vetor de informações sobre o caminho ou FALSE em caso de erros
	// @see			FileSystem::getFullPath
	//!-----------------------------------------------------------------
	function getPathInfo($path) {
		$pathInfo = $this->_buildPathInfo($path);
		if (@is_dir($pathInfo['fullPath']) && !empty($pathInfo['file'])) {			
			$pathInfo['dirs']    .= $pathInfo['file'] . '/';
			$pathInfo['lastDir']  = $pathInfo['file'];
			$pathInfo['realPath'] = $pathInfo['fullPath'];
			$pathInfo['file']     = '';
			if (StringUtils::right($pathInfo['fullPath'], 1) != '/')
				$pathInfo['fullPath'] .= '/';
		}
		return $pathInfo;
	}
	
	//!-----------------------------------------------------------------
	// @function	FileSystem::getAbsolutePath
	// @desc		Transforma um caminho em caminho absoluto
	// @access		public
	// @param		path string		Caminho para um diretório/arquivo
	// @return		string Caminho absoluto	
	// @static
	//!-----------------------------------------------------------------
	function getAbsolutePath($path) {
		return realpath($path);
	}

	//!-----------------------------------------------------------------
	// @function	FileSystem::getStandardPath
	// @desc		Converte todas as barras invertidas para barras convencionais
	//				para padronizar a formatação de um caminho
	// @access		public
	// @param		path string		Caminho para um diretório ou arquivo
	// @return		string Caminho com as barras padronizadas
	// @static	
	//!-----------------------------------------------------------------
	function getStandardPath($path) {
		return str_replace("\\", "/", $path);
	}

	//!-----------------------------------------------------------------
	// @function	FileSystem::getFileName
	// @desc		Busca o nome do arquivo em um caminho
	// @access		public
	// @param		path string		Caminho para um arquivo ou diretório
	// @return		string Nome de arquivo para o caminho fornecido
	// @see			FileSystem::getFileExtension
	// @see			FileSystem::getFilePermissions
	// @static	
	//!-----------------------------------------------------------------
	function getFileName($path) {
		return basename($path);
	}

	//!-----------------------------------------------------------------
	// @function	FileSystem::getFileExtension
	// @desc		Busca a extensão de um arquivo a partir de seu caminho
	// @access		public
	// @param		path string 	Caminho para um arquivo
	// @return		string Extensão do arquivo ou vazio se ele não possuir
	// @see			FileSystem::getFileName
	// @see			FileSystem::getFilePermissions
	//!-----------------------------------------------------------------
	function getFileExtension($path) {
		$fileName = basename($path);
		if (FALSE !== ($pos = strpos($fileName, '.'))) {
			$extension = substr($fileName, $pos+1);
			if ($extension == 'lnk' && System::isWindows()) {
       			$extension = $this->getFileExtension(StringUtils::left($fileName, -4));
			}
		} else {
			$extension = '';
		}
		return $extension;
	}

	//!-----------------------------------------------------------------
	// @function	FileSystem::getFilePermissions
	// @desc		Constrói a definição das permissões de um arquivo,
	//				no formato 'rwxrwxrwx'
	// @access		public
	// @param		path string		Caminho do arquivo
	// @return		string Permissões do arquivo ou FALSE em caso de erros
	// @see			FileSystem::getFileName
	// @see			FileSystem::getFileExtension
	//!-----------------------------------------------------------------
	function getFilePermissions($path) {
		if (!$this->exists($path)) {
			return FALSE;
		} else {
			$mode = fileperms($path);
			$fperms["uread"] = ($mode & 00400) ? 'r' : '-';
			$fperms["uwrite"] = ($mode & 00200) ? 'w' : '-';
			$fperms["uexecute"] = ($mode & 00100) ? 'x' : '-';
			$fperms["gread"] = ($mode & 00040) ? 'r' : '-';
			$fperms["gwrite"] = ($mode & 00020) ? 'w' : '-';
			$fperms["gexecute"] = ($mode & 00010) ? 'x' : '-';
			$fperms["aread"] = ($mode & 00004) ? 'r' : '-';
			$fperms["awrite"] = ($mode & 00002) ? 'w' : '-';
			$fperms["aexecute"] = ($mode & 00001) ? 'x' : '-';
			return implode("",$fperms);
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	FileSystem::createPath
	// @desc		Método utilitário para verificar se um determinado caminho
	//				existe, criando todas as pastas necessárias
	// @access		public
	// @param		path string		Caminho para um diretório
	// @param		mode int		"0777" Modo de criação do(s) diretório(s)
	// @return		bool
	// @note		Este método não deve receber como parâmetro um caminho 
	//				para um arquivo regular, e sim um caminho para um diretório
	//!-----------------------------------------------------------------
	function createPath($path, $mode=0777) {
		$cwd = getcwd();
		$path = trim($path);
		if (StringUtils::startsWith($path, '/'))
			$path = substr($path, 1);
		if (StringUtils::endsWith($path, '/'))
			$path = substr($path, 0, -1);
		$path = preg_replace('/^(\.{1,2}(\/|\\\))+/', '', $path);
		$path = preg_replace('/\\{1,2}/', '/', $path);
		$parts = explode('/', $path);
		for ($i=0, $size=sizeOf($parts); $i<$size; $i++) {
			if (!FileSystem::exists($parts[$i])) {
				if (!@mkdir($parts[$i], $mode)) {
					chdir($cwd);
					PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CANT_CREATE_FILE', $path), E_USER_WARNING, __FILE__, __LINE__);
					return FALSE;
				}
			}
			chdir($parts[$i]);
		}
		chdir($cwd);
		return TRUE;
	}
	
	//!-----------------------------------------------------------------
	// @function	FileSystem::getDiskTotalSpace
	// @desc		Retorna o tamanho total do espaço de armazenamento do disco no servidor
	// @access		public
	// @param		mode string		"" Modo como o resultado deve ser apresentado
	// @param		precision int	"2" Número de casas decimais no resultado
	// @return		string Espaço total no disco
	// @see			FileSystem::getDiskFreeSpace
	// @note		Os valores de abreviação possíveis são K, M, G e T, respectivamente
	//				KBytes, MBytes, GBytes e TBytes
	// @note		Este método pode ser executado estaticamente
	// @see			Number::formatByteAmount
	// @static	
	//!-----------------------------------------------------------------
	function getDiskTotalSpace($mode='', $precision=2) {
		$size = disk_total_space('/');
		return Number::formatByteAmount($size, $precision);
	}
	
	//!-----------------------------------------------------------------
	// @function	FileSystem::getDiskFreeSpace
	// @desc		Retorna o espaço livre em disco no servidor
	// @param		mode string		"" Modo como o resultado deve ser apresentado
	// @param		precision int	"2" Número de casas decimais no resultado
	// @return		string Espaço livre no disco
	// @see			FileSystem::getDiskTotalSpace
	// @note		Este método pode ser executado estaticamente
	// @see			Number::formatByteAmount	
	// @static	
	//!-----------------------------------------------------------------
	function getDiskFreeSpace($mode='', $precision=2) {
		$size = disk_free_space('/');
		return Number::formatByteAmount($size, $precision);
	}
	
	//!-----------------------------------------------------------------
	// @function	FileSystem::isNewer
	// @desc		Verifica, através dos atributos de um arquivo, se o
	//				mesmo foi modificado após um determinado instante de tempo
	// @access		public
	// @param		attrs array		Vetor de atributos de um arquivo criado com a função getFileAttributes()
	// @param		timestamp int	Timestamp para comparação
	// @return		bool
	// @see			FileSystem::isOlder
	//!-----------------------------------------------------------------
	function isNewer($attrs, $timestamp) {
		if (isset($attrs['mTime']) && TypeUtils::isInteger($timestamp)) {
			return ($attrs['mTime'] > $timestamp);
		} else {
			return FALSE;
		}
	}

	//!-----------------------------------------------------------------
	// @function	FileSystem::isOlder
	// @desc		Verifica, através dos atributos de um arquivo, se o
	//				mesmo foi modificado antes de um determinado instante de tempo
	// @access		public
	// @param		attrs array		Vetor de atributos de um arquivo criado com a função getFileAttributes()
	// @param		timestamp int	Timestamp para comparação
	// @return		bool
	// @see			FileSystem::isNewer
	//!-----------------------------------------------------------------
	function isOlder($attrs, $timestamp) {
		if (isset($attrs['mTime']) && TypeUtils::isInteger($timestamp)) {
			return ($attrs['mTime'] < $timestamp);
		} else {
			return FALSE;
		}
	}	
	
	//!-----------------------------------------------------------------
	// @function	FileSystem::touch
	// @desc		Altera as datas de modificação e acesso de um arquivo
	// @access		public
	// @param		fileName string	Nome do arquivo
	// @param		time int		"0" Timestamp a ser aplicado às datas do arquivo
	// @return		bool
	//!-----------------------------------------------------------------
	function touch($fileName, $time=0) {
		if (FileSystem::exists($fileName)) {
			return @touch($fileName, (!$time ? time() : $time));
		} else {
			return FALSE;
		}
	}	

	//!-----------------------------------------------------------------
	// @function	FileSystem::_buildPathInfo
	// @desc		Constrói as informações sobre um caminho de arquivo,
	//				utilizadas pelo método getPathInfo()
	// @access		private
	// @param		path string	Caminho para um arquivo ou diretório
	// @return		array Vetor de informações sobre o caminho
	//!-----------------------------------------------------------------
	function _buildPathInfo($path) {
		$ret = array(
			'realPath'=>'',
			'fullPath'=>'',
			'root'=>'',
			'dirs'=>'',
			'lastDir'=>'',
			'file'=>''
		);
		$path = FileSystem::getAbsolutePath(FileSystem::getStandardPath(StringUtils::allTrim($path)));
		if (ereg("^((.+\:)?(/{1,2})?).+", $path, $matches)) {
			$ret['root'] = $matches[1];
			$path = substr($path, strlen($matches[1]));
		}
		$path  = preg_replace(array(';/\./;', ';[/\\\\]+;', ';^(?:\.)/;', ';/\.$;'), array('/','/','','/'), $path);
		$pathParts = explode('/', $path);
		$pathPartsSize = sizeOf($pathParts);
		$ret['file'] = $pathParts[$pathPartsSize-1];
		if ($pathPartsSize > 1)
			$ret['lastDir'] = $pathParts[$pathPartsSize-2];
		array_pop($pathParts);
		$ret['dirs'] = implode(PHP2GO_DIRECTORY_SEPARATOR, $pathParts) . PHP2GO_DIRECTORY_SEPARATOR;
		$ret['realPath'] = $ret['root'] . $ret['dirs'];
		$ret['fullPath'] = $ret['realPath'] . $ret['file'];
		return $ret;
	}
}
?>