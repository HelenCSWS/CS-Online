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
// $Header: /www/cvsroot/php2go/core/net/FtpClient.class.php,v 1.13 2005/07/20 22:28:58 mpont Exp $
// $Date: 2005/07/20 22:28:58 $

//------------------------------------------------------------------
import('php2go.text.StringUtils');
//------------------------------------------------------------------

// @const FTP_DEFAULT_PORT "21"
// Porta padrão do cliente FTP
define('FTP_DEFAULT_PORT', 21);

//!-----------------------------------------------------------------
// @class 		FtpClient
// @desc 		Esta classe implementa um cliente de FTP, para realização
// 				de conexões e transferência de arquivos
// @package		php2go.net
// @extends 	PHP2Go
// @uses		StringUtils
// @uses		System
// @author 		Marcos Pont
// @version		$Revision: 1.13 $
//!-----------------------------------------------------------------
class FtpClient extends PHP2Go
{
	var $host; 						// @var host string				Nome ou IP do host
	var $port = FTP_DEFAULT_PORT;	// @var port int				"FTP_DEFAULT_PORT" Porta para a conexão
	var $user;						// @var user string				Usuário para conexão no sevidor FTP
	var $password;					// @var password string			Senha para conexão no servidor FTP
	var $connectionId; 				// @var connectionId resource	Identificador da conexão
	var $localPath = ''; 			// @var localPath string		"" Caminho local atual
	var $remotePath = ''; 			// @var remotePath string		"" Caminho remoto atual
	var $sysType = ''; 				// @var sysType string			"" Identificador do tipo de sistema do servidor FTP
	var $transferMode = FTP_BINARY;	// @var transferMode int		"FTP_BINARY" Modo de transferência: ASCII ou binário
	var $connected = FALSE; 		// @var connected bool			"FALSE" Indica se a conexão está ativa ou não
	var $defaultSettings = array();	// @var defaultSettings array	"array()" Vetor de propriedades default da classe para utilização na função Reset
	var $defaultMode = "0777";		// @var defaultMode string		"0777" Modo padrão para criação de arquivos

	//!-----------------------------------------------------------------
	// @function 	FtpClient::FtpClient
	// @desc 		Construtor da classe, instancia o objeto PHP2Go
	// @access 		public
	//!-----------------------------------------------------------------
	function FtpClient() {
		PHP2Go::PHP2Go();
		if (!System::loadExtension("ftp"))
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_UNSUPPORTED_EXTENSION', "ftp"), E_USER_ERROR, __FILE__, __LINE__);
		parent::registerDestructor($this, '_FtpClient');
	}

	//!-----------------------------------------------------------------
	// @function 	FtpClient::_FtpClient
	// @desc 		Destrutor do objeto cliente FTP
	// @access 		public
	// @return		void
	// @note 		Função chamada automaticamente pelo PHP2Go para cada
	// 				instância da classe FtpClient
	//!-----------------------------------------------------------------
	function _FtpClient() {
		$this->quit();
	}

	//!-----------------------------------------------------------------
	// @function 	FtpClient::setServer
	// @desc 		Configura o servidor e a porta a serem usadas na conexão
	// @access 		public
	// @param 		host string	Nome ou IP do servidor FTP
	// @param 		port int		Porta para conexão
	// @return		void	
	// @see 		FtpClient::setUserInfo
	// @see 		FtpClient::setTransferMode
	//!-----------------------------------------------------------------
	function setServer($host, $port) {
		if ($this->isConnected()) return FALSE;
		$this->host = $host;
		$this->port = TypeUtils::parseInteger($port);
	}

	//!-----------------------------------------------------------------
	// @function 	FtpClient::setUserInfo
	// @desc 		Configura o nome de usuário e senha para a conexão
	// @access 		public
	// @param 		user string		Nome de usuário ou login
	// @param 		password string	Senha do usuário
	// @return		void	
	// @see 		FtpClient::setServer
	// @see 		FtpClient::setTransferMode
	//!-----------------------------------------------------------------
	function setUserInfo($user, $password) {
		if ($this->isConnected()) return FALSE;
		$this->user = $user;
		$this->password = $password;
	}

	//!-----------------------------------------------------------------
	// @function 	FtpClient::setTransferMode
	// @desc 		Configura o modo de transferência dos arquivos via FTP
	// @access 		public
	// @param 		mode int			Aceita as constantes FTP_ASCII e
	// 									FTP_BINARY pré-definidas no PHP
	// @return		void	
	// @see 		FtpClient::setServer
	// @see 		FtpClient::setUserInfo
	//!-----------------------------------------------------------------
	function setTransferMode($mode) {
		if (in_array($mode, array(FTP_ASCII, FTP_BINARY))) {
			$this->transferMode = $mode;
		}
	}

	//!-----------------------------------------------------------------
	// @function 	FtpClient::getCurrentDir
	// @desc 		Busca o diretório remoto atual
	// @access 		public
	// @return 		string Diretório remoto atual
	// @note		Retorna FALSE se a conexão não estiver ativa
	//!-----------------------------------------------------------------
	function getCurrentDir() {
		if (!empty($this->remotePath)) {
			return $this->remotePath;
		} else if (!$this->isConnected()) {
			return FALSE;
		} else {
			$path = ftp_pwd($this->connectionId);
			if (!$path) {
				$this->remotePath = null;
				return FALSE;
			} else {
				$this->remotePath = $path;
				return $this->remotePath;
			}
		}
	}

	//!-----------------------------------------------------------------
	// @function 	FtpClient::getSysType
	// @desc 		Busca informações do tipo de sistema do servidor FTP
	// @access 		public
	// @return 		string Identificador do tipo de sistema do servidor FTP ou FALSE em caso de ocorrência de erros
	// @note		Retorna FALSE se a conexão não estiver ativa	
	//!-----------------------------------------------------------------
	function getSysType() {
		if (!empty($this->sysType)) {
			return $this->sysType;
		} else if (!$this->isConnected()) {
			return FALSE;
		} else {
			$sysType = ftp_systype($this->connectionId);
			if (!$sysType) {
				$this->sysType = null;
				return FALSE;
			} else {
				$this->sysType = $sysType;
				return $this->sysType;
			}
		}
	}

	//!-----------------------------------------------------------------
	// @function 	FtpClient::isConnected
	// @desc 		Verifica se a conexão com o servidor está ativa
	// @access 		public
	// @return		bool
	//!-----------------------------------------------------------------
	function isConnected() {
		return $this->connected;
	}

	//!-----------------------------------------------------------------
	// @function 	FtpClient::connect
	// @desc 		Abre uma conexão com o servidor FTP
	// @access 		public
	// @return 		bool
	//!-----------------------------------------------------------------
	function connect() {
		if ($this->isConnected()) {
			$this->quit();
		}
		if (!isset($this->host)) {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_FTP_MISSING_HOST'), E_USER_ERROR, __FILE__, __LINE__);
		} else {
			$this->connectionId = @ftp_connect($this->host, $this->port);
			if (!$this->connectionId) {
				return FALSE;
			} else {
				$this->connected = TRUE;
				return TRUE;
			}
		}
	}

	//!-----------------------------------------------------------------
	// @function 	FtpClient::login
	// @desc 		Executa autenticação no servidor FTP
	// @access 		public
	// @param 		anonymous bool    "FALSE" Indica se deve ser utilizado usuário anônimo
	// @return 		bool
	//!-----------------------------------------------------------------
	function login($anonymous = FALSE) {
		if (!$this->isConnected()) {
			$this->connect();
		}
		if ((!isset($this->user) || !isset($this->password)) && !$anonymous) {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_FTP_MISSING_USER_OR_PASS'), E_USER_ERROR, __FILE__, __LINE__);
		} else {
			$authUser = ($anonymous ? 'anonymous' : $this->user);
			$authPass = ($anonymous ? 'anonymous@ftpclient.php2go.org' : $this->password);
			return @ftp_login($this->connectionId, $authUser, $authPass);
		}
	}

	//!-----------------------------------------------------------------
	// @function	FtpClient::restart
	// @desc		Reinicializa o cliente para execução de uma nova conexão
	// @access		public
	// @return		void	
	//!-----------------------------------------------------------------
	function restart() {
		if ($this->isConnected()) {
			$this->quit();
		}
		foreach($this->defaultSettings as $property => $value) {
			$this->$property = $value;
		}
		unset($this->host);
		unset($this->user);
		unset($this->password);
		unset($this->connectionId);
	}

	//!-----------------------------------------------------------------
	// @function 	FtpClient::quit
	// @desc 		Encerra a conexão com o servidor FTP, se estiver ativa
	// @access 		public
	// @return		bool
	//!-----------------------------------------------------------------
	function quit() {
		if (!$this->isConnected()) {
			return FALSE;
		} else {
			$return = @ftp_quit($this->connectionId);
			unset($this->connectionId);
			return $return;
		}
	}
	//!-----------------------------------------------------------------
	// @function 	FtpClient::site
	// @desc 		Executa um comando no servidor FTP
	// @access 		public
	// @param 		command string    Comando a ser executado
	// @return		bool
	//!-----------------------------------------------------------------
	function site($command) {
		if ($this->isConnected()) {
			return @ftp_site($this->connectionId, $command);
		} else {
			return FALSE;
		}
	}

	//!-----------------------------------------------------------------
	// @function 	FtpClient::togglePassiveMode
	// @desc 		Liga ou desliga o modo passivo
	// @access 		public
	// @param 		mode bool		Modo a ser setado. TRUE liga e FALSE desliga
	// @return		bool
	//!-----------------------------------------------------------------
	function togglePassiveMode($mode) {
		if ($this->isConnected()) {
			return @ftp_pasv($this->connectionId, TypeUtils::toBoolean($mode));
		} else {
			return FALSE;
		}
	}

	//!-----------------------------------------------------------------
	// @function 	FtpClient::changeDir
	// @desc 		Muda o diretório remoto atual
	// @access 		public
	// @param 		directory string	Novo diretório
	// @return		bool
	//!-----------------------------------------------------------------
	function changeDir($directory) {
		if (!$this->isConnected()) {
			return FALSE;
		} else {
			$return = ftp_chdir($this->connectionId, $directory);
			if (!$return) {
				return FALSE;
			} else {
				$this->remotePath = ftp_pwd($this->connectionId);
				return TRUE;
			}
		}
	}

	//!-----------------------------------------------------------------
	// @function 	FtpClient::changeDirUp
	// @desc 		Sobe um nível na árvore de diretórios do servidor FTP
	// @access 		public
	// @return		bool
	//!-----------------------------------------------------------------
	function changeDirUp() {
		if (!$this->isConnected()) {
			return FALSE;
		} else {
			$return = ftp_cdup($this->connectionId);
			if (!$return) {
				return FALSE;
			} else {
				$this->remotePath = ftp_pwd($this->connectionId);
				return TRUE;
			}
		}
	}

	//!-----------------------------------------------------------------
	// @function 	FtpClient::makeDir
	// @desc 		Cria um novo diretório no servidor FTP
	// @access 		public
	// @param 		directory string	Nome do novo diretório
	// @param 		moveDir bool		Move o ponteiro para o diretório criado
	// @return		bool
	//!-----------------------------------------------------------------
	function makeDir($directory, $moveDir = FALSE) {
		if (!$this->isConnected()) {
			return FALSE;
		} else {
			$return = @ftp_mkdir($this->connectionId, $directory);
			if (!$return) {
				return FALSE;
			} else {
				if ($moveDir) {
					$this->changeDir($directory);
				}
				return TRUE;
			}
		}
	}

	//!-----------------------------------------------------------------
	// @function 	FtpClient::removeDir
	// @desc 		Remove um diretório no servidor FTP
	// @access 		public
	// @param 		directory string	Nome do diretório a ser removido
	// @return		bool
	//!-----------------------------------------------------------------
	function removeDir($directory) {
		if (!$this->isConnected()) {
			return FALSE;
		} else {
			return @ftp_rmdir($this->connectionId, $directory);
		}
	}

	//!-----------------------------------------------------------------
	// @function 	FtpClient::removeDirRecursive
	// @desc 		Remove arquivos e subdiretórios a partir do diretório
	// 				informado no parâmetro $directory
	// @access 		public
	// @param 		directory string	Nome do diretório a ser removido
	// @return		bool
	//!-----------------------------------------------------------------
	function removeDirRecursive($directory) {
		if ($directory != '') {
			if (!$this->changeDir($directory)) return FALSE;
			$files = $this->rawList();
			if (TypeUtils::isArray($files)) {
				for ($i = 0; $i < count($files); $i++) {
					$fileInfo = $files[$i];
					if ($fileInfo['type'] == 'dir') {
						$this->removeDirRecursive($fileInfo['name']);
					} else {
						$this->delete($fileInfo['name']);
					}
				}
			}
			$this->changeDirUp();
			$this->removeDir($directory);
		}
	}

	//!-----------------------------------------------------------------
	// @function 	FtpClient::&fileList
	// @desc 		Busca a lista de nomes de arquivos de um diretório remoto
	// @access 		public
	// @param 		directory string	"" Diretório a ser utilizado como base
	// @return		mixed Vetor com a lista de arquivos ou FALSE em caso de erros
	// @note 		Se for fornecido um diretório e uma máscara de arquivos como
	// 				parâmetro para o método (ex: folder/file*.txt), o diretório
	// 				atual será trocado para 'folder' e a máscara de arquivos será
	// 				aplicada sobre o novo diretório
	//!-----------------------------------------------------------------
	function &fileList($directory='') {
		if (!$this->isConnected()) {
			return FALSE;
		} else {
			if ($directory != '') {
				list($changeDir, $fileMask) = $this->_parseDir($directory);
				if (!empty($changeDir))
					$this->changeDir($changeDir);
			} else {
				$fileMask = '';
			}
			$return = ftp_nlist($this->connectionId, $fileMask);
			if (!$return) {
				return FALSE;
			} else {
				return $return;
			}
		}
	}

	//!-----------------------------------------------------------------
	// @function 	FtpClient::&rawList
	// @desc 		Busca as informações sobre os arquivos de um diretório remoto
	// @access 		public
	// @param 		directory string	"" Diretório a ser utilizado como base
	// @param 		parseInfo bool	"TRUE" Retornar as informações em um vetor bidimensional
	// @return		mixed Vetor com a lista de arquivos ou FALSE em caso de erros
	// @note 		Se for fornecido um diretório e uma máscara de arquivos como
	// 				parâmetro para o método (ex: folder/file*.txt), o diretório
	// 				atual será trocado para 'folder' e a máscara de arquivos será
	// 				aplicada sobre o novo diretório
	//!-----------------------------------------------------------------
	function &rawList($directory='', $parseInfo=TRUE) {
		if (!$this->isConnected()) {
			return FALSE;
		} else {
			if ($directory != '') {
				list($changeDir, $fileMask) = $this->_parseDir($directory);
				if (!empty($changeDir))
					$this->changeDir($changeDir);
			} else {
				$fileMask = '';
			}
			$return = ftp_rawlist($this->connectionId, $fileMask);
			if (!$return) {
				return FALSE;
			} else {
				if ($parseInfo)
					$return = $this->_parseRawList($return);
				return $return;
			}
		}
	}

	//!-----------------------------------------------------------------
	// @function 	FtpClient::get
	// @desc 		Realiza o download de um arquivo do servidor FTP remoto
	// @access 		public
	// @param 		localFile string	Nome local para o arquivo
	// @param 		remoteFile string	Nome do arquivo remoto ou caminho a partir do diretório atual
	// @param 		mode int			"NULL" Modo da transferência [FTP_BINARY | FTP_ASCII]
	// @param		resume int			"NULL" Posição a partir da qual o download deve ser recomeçado
	// @return		bool
	// @see 		FtpClient::fileGet
	// @see 		FtpClient::put
	// @see 		FtpClient::filePut
	// @note Nas funções get, fileGet, put, filePut, delete, rename, fileLastMod e
	// fileSize, se for fornecido um diretório e um nome de arquivo
	// para o parâmetro $remoteFile (ex: folder/file.txt), o diretório
	// atual será trocado para 'folder' e a variável $remoteFile passará
	// a ter o valor 'file.txt'
	//!-----------------------------------------------------------------
	function get($localFile, $remoteFile, $mode=NULL, $resume=NULL) {
		if (!$this->isConnected()) {
			return FALSE;
		} else {
			list($changeDir, $remoteFile) = $this->_parseDir($remoteFile);
			if (!empty($changeDir)) $this->changeDir($changeDir);
			if (is_null($mode) || !in_array($mode, array(FTP_ASCII, FTP_BINARY))) $mode = &$this->transferMode;
			$return = @ftp_get($this->connectionId, $localFile, $remoteFile, $mode, (TypeUtils::isInteger($resume) && $resume >= 0 ? $resume : 0));
			if ($return != FTP_FINISHED) {
				return FALSE;
			} else {
				return TRUE;
			}
		}
	}

	//!-----------------------------------------------------------------
	// @function 	FtpClient::fileGet
	// @desc 		Realiza o download de um arquivo do servidor FTP gravando
	// 				seu conteúdo no arquivo aberto referenciado pelo parâmetro $filePointer
	// @access 		public
	// @param 		filePointer resource	Arquivo aberto onde o conteúdo do arquivo remoto deve ser gravado
	// @param 		remoteFile string		Nome do arquivo remoto ou caminho a partir do diretório atual
	// @param 		mode int				"NULL" Modo da transferência [FTP_BINARY | FTP_ASCII]
	// @return		bool
	// @see 		FtpClient::get
	// @see 		FtpClient::put
	// @see 		FtpClient::filePut
	//!-----------------------------------------------------------------
	function fileGet($filePointer, $remoteFile, $mode = NULL) {
		if (!$this->isConnected()) {
			return FALSE;
		} else if (!TypeUtils::isResource($filePointer)) {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_INVALID_RESOURCE', array('$filePointer', '$FtpClient->fileGet')), E_USER_ERROR, __FILE__, __LINE__);
		} else {
			list($changeDir, $remoteFile) = $this->_parseDir($remoteFile);
			if (!empty($changeDir)) $this->changeDir($changeDir);
			if (TypeUtils::isNull($mode) || !in_array($mode, array(FTP_ASCII, FTP_BINARY))) $mode = &$this->transferMode;
			$return = @ftp_fget($this->connectionId, $filePointer, $remoteFile, $mode);
			if ($return != FTP_FINISHED) {
				return FALSE;
			} else {
				return TRUE;
			}
		}
	}

	//!-----------------------------------------------------------------
	// @function 	FtpClient::nGet
	// @desc 		Realiza o download de todos os arquivos e diretórios
	// 				a partir do ponto informado no parâmetro $directory
	// @access 		public
	// @param 		directory string	"" Diretório a ser utilizado como base
	// @param 		mode int			"NULL" Modo da transferência [FTP_BINARY | FTP_ASCII]
	// @return		bool
	//!-----------------------------------------------------------------
	function nGet($directory = '', $mode = NULL) {
		if (!$this->isConnected()) {
			return FALSE;
		} else {
			$list = $this->rawList($directory);
			if (!$list || !TypeUtils::isArray($list)) return FALSE;
			foreach($list as $file) {
				switch ($file['type']) {
					// diretórios, aplica recursividade
					case 'dir' : 
						if (!@mkdir($file['name'])) {
							continue;
						}
						chdir($file['name']);
						$this->changeDir($file['name']);
						$this->nGet('', $mode);
						chdir('..');
						$this->changeDirUp();
						break;
					// arquivos
					case 'file' : 
						$this->get($file['name'], $file['name'], $mode);
						break;
					default : continue;
				}
			}
		}
	}

	//!-----------------------------------------------------------------
	// @function 	FtpClient::put
	// @desc 		Copia um arquivo local para o servidor FTP remoto
	// @access 		public
	// @param 		localFile string	Nome local do arquivo
	// @param 		remoteFile string	Nome do arquivo remoto ou caminho a partir do diretório atual
	// @param 		mode int			"NULL" Modo da transferência [FTP_BINARY | FTP_ASCII]
	// @return		bool
	// @see 		FtpClient::get
	// @see 		FtpClient::fileGet
	// @see 		FtpClient::filePut
	//!-----------------------------------------------------------------
	function put($localFile, $remoteFile, $mode = null) {
		if (!$this->isConnected()) {
			return FALSE;
		} else {
			list($changeDir, $remoteFile) = $this->_parseDir($remoteFile);
			if (!empty($changeDir)) $this->changeDir($changeDir);
			if (TypeUtils::isNull($mode) || !in_array($mode, array(FTP_ASCII, FTP_BINARY))) $mode = &$this->transferMode;
			return @ftp_put($this->connectionId, $remoteFile, $localFile, $mode);
		}
	}

	//!-----------------------------------------------------------------
	// @function 	FtpClient::filePut
	// @desc 		Copia o conteúdo do arquivo aberto referenciado pelo
	// 				ponteiro $filePointer para o servidor FTP remoto
	// @access 		public
	// @param 		filePointer resource	Arquivo aberto que deverá ser copiado para o servidor FTP
	// @param 		remoteFile string		Nome do arquivo remoto ou caminho a partir do diretório atual
	// @param 		mode int				"NULL" Modo da transferência [FTP_BINARY | FTP_ASCII]
	// @return		bool
	// @see 		FtpClient::get
	// @see 		FtpClient::fileGet
	// @see 		FtpClient::put
	//!-----------------------------------------------------------------
	function filePut($filePointer, $remoteFile, $mode = null) {
		if (!$this->isConnected()) {
			return FALSE;
		} else if (!TypeUtils::isResource($filePointer)) {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_INVALID_RESOURCE', array('$filePointer', 'FtpClient::filePut')), E_USER_ERROR, __FILE__, __LINE__);
		} else {
			list($changeDir, $remoteFile) = $this->_parseDir($remoteFile);
			if (!empty($changeDir)) 
				$this->changeDir($changeDir);
			if (TypeUtils::isNull($mode) || !in_array($mode, array(FTP_ASCII, FTP_BINARY))) 
				$mode =& $this->transferMode;
			return @ftp_fput($this->connectionId, $remoteFile, $filePointer, $mode);
		}
	}

	//!-----------------------------------------------------------------
	// @function 	FtpClient::nPut
	// @desc 		Copia para o servidor FTP todos os arquivos e diretórios
	// 				a partir do ponto informado no parâmetro $directory
	// @access 		public
	// @param 		directory string	"" Diretório a ser utilizado como base
	// @param 		mode int			"NULL" Modo da transferência [FTP_BINARY | FTP_ASCII]
	// @return	 	bool
	//!-----------------------------------------------------------------
	function nPut($directory = '', $mode = null) {
		if (!$this->isConnected()) {
			return FALSE;
		} else {
			if ($directory != '') {
				if (!is_dir($directory)) {
					return FALSE;
				} else {
					chdir($directory);
				}
			}
			if ($handle = opendir(getcwd())) {
				while (FALSE !== ($fileName = readdir($handle))) {
					if ($fileName != "." && $fileName != "..") {
						if (is_dir($fileName)) {
							chdir($fileName);
							if (!$this->makeDir($fileName, TRUE)) return FALSE;
							$this->nPut('', $mode);
							chdir('..');
							$this->changeDirUp();
						} else if (is_file($fileName)) {
							$this->put($fileName, $fileName, $mode);
						} else continue;
					}
				}
				closedir($handle);
			}
		}
	}

	//!-----------------------------------------------------------------
	// @function 	FtpClient::delete
	// @desc 		Apaga um arquivo no servidor FTP
	// @access 		public
	// @param 		remoteFile string	Nome do arquivo remoto ou caminho a partir do diretório atual
	// @return		bool
	// @see 		FtpClient::rename
	//!-----------------------------------------------------------------
	function delete($remoteFile) {
		if (!$this->isConnected()) {
			return FALSE;
		} else {
			list($changeDir, $remoteFile) = $this->_parseDir($remoteFile);
			if (!empty($changeDir)) $this->changeDir($changeDir);
			return @ftp_delete($this->connectionId, $remoteFile);
		}
	}

	//!-----------------------------------------------------------------
	// @function 	FtpClient::rename
	// @desc 		Renomeia um arquivo no servidor FTP
	// @access 		public
	// @param 		remoteFile string	Nome do arquivo remoto ou caminho a partir do diretório atual
	// @param 		newName string	Novo nome para o arquivo remoto
	// @return		bool
	// @see 		FtpClient::delete
	//!-----------------------------------------------------------------
	function rename($remoteFile, $newName) {
		if (!$this->isConnected()) {
			return FALSE;
		} else {
			list($changeDir, $remoteFile) = $this->_parseDir($remoteFile);
			if (!empty($changeDir)) $this->changeDir($changeDir);
			return @ftp_rename($this->connectionId, $remoteFile, $newName);
		}
	}

	//!-----------------------------------------------------------------
	// @function 	FtpClient::fileLastMod
	// @desc 		Busca a data da última modificação de um arquivo no servidor FTP
	// @access 		public
	// @param 		remoteFile string	Nome do arquivo remoto ou caminho a partir do diretório atual
	// @param 		formatDate bool	"TRUE" Indica se o timestamp retornado deve ser formatado
	// @return 		mixed Timestamp ou data da última modificação feita no arquivo ou FALSE em caso de erros
	// @see 		FtpClient::fileSize
	// @note 		Nem todos os servidores suportam a função ftp_mdtm nativa do PHP.
	// 				Esta função também não pode ser aplicada a diretórios. Nestes casos,
	// 				fileLastMod retorna FALSE
	//!-----------------------------------------------------------------
	function fileLastMod($remoteFile, $formatDate = TRUE) {
		if (!$this->isConnected()) {
			return FALSE;
		} else {
			list($changeDir, $remoteFile) = $this->_parseDir($remoteFile);
			if (!empty($changeDir)) $this->changeDir($changeDir);
			$return = @ftp_mdtm($this->connectionId, $remoteFile);
			if (!$return || $return == -1) {
				return FALSE;
			} else {
				if ($formatDate) {
					$dateF = PHP2Go::getConfigVal('LOCAL_DATE_FORMAT');
					$return = ($dateF == 'Y/m/d') ? date("$dateF H:i:s") : date("d/m/Y H:i:s");
				}
				return $return;
			}
		}
	}

	//!-----------------------------------------------------------------
	// @function 	FtpClient::fileSize
	// @desc 		Busca o tamanho de um arquivo no servidor FTP
	// @access 		public
	// @param 		remoteFile string  Nome do arquivo remoto ou caminho a partir do diretório atual
	// @return 		mixed Tamanho do arquivo em bytes ou FALSE em caso de erros
	// @see 		FtpClient::fileLastMod
	// @note 		Nem todos os servidores suportam a função ftp_size
	// 				que é executa neste método
	//!-----------------------------------------------------------------
	function fileSize($remoteFile) {
		if (!$this->isConnected()) {
			return FALSE;
		} else {
			list($changeDir, $remoteFile) = $this->_parseDir($remoteFile);
			if (!empty($changeDir)) $this->changeDir($changeDir);
			$return = @ftp_size($this->connectionId, $remoteFile);
			if (!$return || $return == -1) {
				return FALSE;
			} else {
				return $return;
			}
		}
	}

	//!-----------------------------------------------------------------
	// @function 	FtpClient::_parseDir
	// @desc 		Busca o diretório e a máscara de arquivo a partir de
	// 				um parâmetro $directory passado às funções fileList ou rawList
	// @access 		private
	// @param 		str string	Parâmetro $directory
	// @return 		array Vetor contendo diretório e máscara de arquivos
	//!-----------------------------------------------------------------
	function _parseDir($str) {
		if (StringUtils::match($str, '/')) {
			$slashPos = strrpos($str, '/');
			return array(substr($str, 0, $slashPos + 1), substr($str, $slashPos + 1, strlen($str) - $slashPos));
		} else {
			return array('', $str);
		}
	}

	//!-----------------------------------------------------------------
	// @function 	FtpClient::_parseRawList
	// @desc 		Processa as informações retornadas da listagem de dados
	// 				de arquivos de um diretório no servidor FTP, armazenando-as
	// 				em um vetor
	// @access 		private
	// @param 		rawList array	Vetor retornado pela função ftp_rawlist
	// @return 		array Vetor com dados dos arquivos organizados em novos vetores ou FALSE em caso de erros
	//!-----------------------------------------------------------------
	function _parseRawList($rawList) {
		if (TypeUtils::isArray($rawList)) {
			$newList = array();
			while (list($k) = each($rawList)) {
				$element = split(' {1,}', $rawList[$k], 9);
				if (TypeUtils::isArray($element) && (sizeOf($element) == 9)) {
					unset($fileInfo);
					$dateF = PHP2Go::getConfigVal('LOCAL_DATE_FORMAT');
					$year = (FALSE !== StringUtils::match($element[7], ':')) ? $element[7] : date('Y');
					$month = $element[5];
					$day = (strlen($element[6]) == 2) ? $element[6] : '0' . $element[6];

					$fileInfo['name'] = $element[8];
					$fileInfo['size'] = TypeUtils::parseInteger($element[4]);
					$fileInfo['date'] = ($dateF == 'Y/m/d') ? $year . '/' . $month . '/' . $day : $day . '/' . $month . '/' . $year;
					$fileInfo['attr'] = $element[0];
					$fileInfo['type'] = ($element[0][0] == '-') ? 'file' : 'dir';
					$fileInfo['dirno'] = TypeUtils::parseInteger($element[1]);
					$fileInfo['user'] = $element[2];
					$fileInfo['group'] = $element[3];
					$newList[] = $fileInfo;
				}
			}
			return $newList;
		}
		return FALSE;
	}
}
?>