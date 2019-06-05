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
// $Header: /www/cvsroot/php2go/core/cache/CacheManager.class.php,v 1.5 2005/08/30 14:09:10 mpont Exp $
// $Date: 2005/08/30 14:09:10 $

//!-----------------------------------------------------------------
import('php2go.file.DirectoryManager');
import('php2go.text.StringUtils');
//!-----------------------------------------------------------------

// @const CACHE_MANAGER_LIFETIME "3600"
// Tempo padrão de expiração dos arquivos de cache criados pela classe
define('CACHE_MANAGER_LIFETIME', 3600);
// @const CACHE_MANAGER_GROUP "php2goCache"
// Nome padrão para grupo de arquivos de cache
define('CACHE_MANAGER_GROUP', 'php2goCache');
// @const CACHE_HIT "1"
// Status que indica que a última operação get retornou os dados da cache com sucesso
define('CACHE_HIT', 1);
// @const CACHE_STALE "2"
// Status que indica que a última operação get detectou dados expirados em cache
define('CACHE_STALE', 2);
// @const CACHE_MISS "3"
// Status que indica que a última operação get não encontrou os dados em cache
define('CACHE_MISS', 3);
// @const CACHE_MEMORY_HIT "4"
// Status que indica que a última operação get retornou os dados da cache em memória
define('CACHE_MEMORY_HIT', 4);

//!-----------------------------------------------------------------
// @class		CacheManager
// @desc		Esta classe implementa um mecanismo simples de cache para qualquer informação serializável. 
//				A operação de consulta ou escrita na cache deve conter, além da informação a ser avaliada, 
//				um ID único e um identificador opcional de grupo (categoria, divisão). 
//				Para ganho de  velocidade, também pode ser utilizada cache em memória. Para aumento da 
//				segurança, pode ser aplicada verificação de checksum nas operações de leitura de dados da cache
// @package		php2go.cache
// @extends		PHP2Go
// @uses		DirectoryManager
// @uses		FileManager
// @uses		StringUtils
// @uses		TypeUtils
// @author		Marcos Pont
// @version		$Revision: 1.5 $
//!-----------------------------------------------------------------
class CacheManager extends PHP2Go
{
	var $baseDir;								// @var baseDir string				Diretório base onde os arquivos devem ser gravados
	var $lifeTime = CACHE_MANAGER_LIFETIME;		// @var lifeTime int				Tempo de expiração dos dados em cache
	var $lastValidTime;							// @var lastValidTime int			Último timestamp válido para dados em cache
	var $obfuscateFileName = TRUE;				// @var obfuscateFileName bool		"TRUE" Flag que permite ofuscar IDs e nomes de grupos na montagem dos nomes de arquivos de cache
	var $readChecksum = TRUE;					// @var readChecksum bool			"TRUE" Indica se a validação de checksum está habilitada
	var $checksumType = 'crc32';				// @var checksumType string			"crc32" Função de checksum a ser utilizada
	var $autoCleanFrequency = 0;				// @var autoCleanFrequency int		"0" Se for maior que zero, indica a cada quantas operações de escrita deve ser executada uma limpeza na cache
	var $autoSerialize = TRUE;					// @var autoSerialize bool			"TRUE" Indica se as operações serialize/unserialize devem ser feitas automaticamente pela classe
	var $memoryCache = TRUE;					// @var memoryCache bool			"TRUE" Indica se deve ser usada cache em memória
	var $memoryLimit = 1000;					// @var memoryLimit int				"1000" Número máximo de entradas da cache em memória
	var $memoryTable = array();					// @var memoryTable array			"array()" Tabela de cache em memória
	var $debug = FALSE;							// @var debug bool					"FALSE" Debug habilitado/desabilitado
	var $currentId;								// @var currentId string			ID de cache atual
	var $currentGroup;							// @var currentGroup string			Grupo de cache atual
	var $currentFile;							// @var currentFile string			Nome do arquivo atual
	var $currentStatus = CACHE_MISS;			// @var currentStatus int			"CACHE_MISS" Último status de consulta à cache (vide constantes da classe)
	var $writeCount = 0;						// @var writeCount int				"0" Contador das operações de escrita
	var $Dir = NULL;							// @var Dir DirectoryManager object	"NULL" Utilizado na operação de limpeza da cache
	
	//!-----------------------------------------------------------------
	// @function	CacheManager::CacheManager
	// @desc		Construtor da classe
	// @access		public
	//!-----------------------------------------------------------------
	function CacheManager() {
		parent::PHP2Go();
		$this->lastValidTime = (time() - $this->lifeTime);
		$this->Dir = new DirectoryManager();
		$this->Dir->throwErrors = FALSE;
		$this->setBaseDir(System::getTempDir());
	}
	
	//!-----------------------------------------------------------------
	// @function	CacheManager::&getInstance
	// @desc		Retorna uma instância única da classe CacheManager
	// @access		public
	// @return		CacheManager object	Instância única
	//!-----------------------------------------------------------------
	function &getInstance() {
		static $instance;
		if (!isset($instance))
			$instance = new CacheManager();
		return $instance;
	}
	
	//!-----------------------------------------------------------------
	// @function	CacheManager::getBaseDir
	// @desc		Retorna o diretório base configurado na classe
	// @access		public
	// @return		string Diretório base
	//!-----------------------------------------------------------------
	function getBaseDir() {
		return $this->baseDir;
	}
	
	//!-----------------------------------------------------------------
	// @function	CacheManager::setBaseDir
	// @desc		Determina o diretório base para a cache
	// @access		public
	// @param		dir string	Caminho do diretório
	// @return		void
	//!-----------------------------------------------------------------
	function setBaseDir($dir) {
		$dir = str_replace("\\", "/", $dir);
		$this->baseDir = (!ereg("\/$", $dir) ? $dir . '/' : $dir);
	}
	
	//!-----------------------------------------------------------------
	// @function	CacheManager::setLifeTime
	// @desc		Define o tempo de expiração para os arquivos em cache
	// @access		public
	// @param		lifeTime int	Tempo de expiração
	// @return		void
	//!-----------------------------------------------------------------
	function setLifeTime($lifeTime) {
		$oldLifeTime = $this->lifeTime;
		$lifeTime = TypeUtils::parseIntegerPositive($lifeTime);
		if ($lifeTime > 0) {
			$this->lifeTime = $lifeTime;
			$this->lastValidTime = (time() - $lifeTime);
		} else {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_MUST_BE_POSITIVE', array("\$lifeTime", 'setLifeTime')), E_USER_ERROR, __FILE__, __LINE__);
		}
		return $oldLifeTime;
	}

	//!-----------------------------------------------------------------
	// @function	CacheManager::setObfuscateFileNames
	// @desc		Habilita/desabilita a codificação dos nomes de arquivos em cache
	// @param		setting bool 	Valor para o flag
	// @return		void
	//!-----------------------------------------------------------------
	function setObfuscateFileNames($setting) {
		$this->obfuscateFileName = TypeUtils::toBoolean($setting);
	}
	
	//!-----------------------------------------------------------------
	// @function	CacheManager::setReadChecksum
	// @desc		Habilita/desabilita validação de checksum na leitura de arquivos da cache
	// @access		public
	// @param		setting bool	Valor para o flag
	// @param		type string		Função de checksum a ser utilizada
	// @return		void
	//!-----------------------------------------------------------------
	function setReadChecksum($setting, $type) {
		$this->readChecksum = TypeUtils::toBoolean($setting);
		if ($this->readChecksum)
			$this->checksumType = $type;
	}
	
	//!-----------------------------------------------------------------
	// @function	CacheManager::setAutoClean
	// @desc		Determina a frequência de limpeza da cache
	// @access		public
	// @param		frequency int	Número de operações de escrita que deverão ser executadas
	//								até que uma limpeza automática seja feita na cache
	// @return		void
	//!-----------------------------------------------------------------
	function setAutoClean($frequency) {
		$frequency = TypeUtils::parseIntegerPositive($frequency);
		if ($frequency >= 0)
			$this->autoCleanFrequency = $frequency;			
	}
	
	//!-----------------------------------------------------------------
	// @function	CacheManager::setAutoSerialize
	// @desc		Habilita/desabilita serialização/deserialização automática
	//				de valores na leitura/escrita da cache
	// @access		public
	// @param		setting bool	Valor para o flag
	// @return		void
	//!-----------------------------------------------------------------
	function setAutoSerialize($setting) {
		$this->autoSerialize = TypeUtils::toBoolean($setting);
	}
	
	//!-----------------------------------------------------------------
	// @function	CacheManager::setMemoryCache
	// @desc		Habilita/desabilita cache em memória
	// @access		public
	// @param		enable bool		Habilitar ou desabilitar
	// @param		limit int		Limite da memória (número de entradas em cache)
	// @return		void
	//!-----------------------------------------------------------------
	function setMemoryCache($enable, $limit=1000) {
		$this->memoryCache = TypeUtils::toBoolean($enable);
		if ($this->memoryCache) {
			$limit = TypeUtils::parseIntegerPositive($limit);
			if ($limit > 0)
				$this->memoryLimit = $limit;
			else
				PHP2Go::raiseError(PHP2Go::getLangVal('ERR_MUST_BE_POSITIVE', array("\$limit", 'setMemoryCache')), E_USER_ERROR, __FILE__, __LINE__);
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	CacheManager::getLastStatus
	// @desc		Retorna o último status de operação de consulta
	// @access		public
	// @return		int Status da última consulta (vide constantes da classe)
	//!-----------------------------------------------------------------
	function getLastStatus() {
		return $this->currentStatus;
	}
	
	//!-----------------------------------------------------------------
	// @function	CacheManager::load
	// @desc		Verifica a existência de um determinado objeto na cache,
	//				a partir de seu ID e grupo
	// @access		public
	// @param		id string 		ID do objeto
	// @param		group string	"CACHE_MANAGER_GROUP" Grupo de cache do objeto
	// @param		force bool		"FALSE" Se TRUE, ignora expiração da cache
	// @return		mixed Dados da cache ou FALSE em caso de falha
	//!-----------------------------------------------------------------
	function load($id, $group=CACHE_MANAGER_GROUP, $force=FALSE) {
		$this->currentId = $id;
		$this->currentGroup = $group;
		$this->_setCurrentFile();
		$result = FALSE;
		// tenta busca da cache em memória
		if ($this->memoryCache)
			$result = $this->_readMemory();
		// cache em memória desabilitada ou memory cache miss
		if ($result === FALSE)
			$result = $this->_validateFile($force);
		// resultado final
		if ($result !== FALSE) {
			if ($this->autoSerialize && TypeUtils::isString($result))
				$result = unserialize($result);
			return $result;			
		}
		return FALSE;
	}
	
	//!-----------------------------------------------------------------
	// @function	CacheManager::save
	// @desc		Insere um objeto na cache, não existente ou por expiração
	// @access		public
	// @param		data mixed		Valor do objeto/informação
	// @param		id string		ID de cache
	// @param		group string	"CACHE_MANAGER_GROUP" Grupo de cache	
	// @return		bool
	//!-----------------------------------------------------------------
	function save($data, $id=NULL, $group=CACHE_MANAGER_GROUP) {
		if ($this->autoSerialize)
			$data = serialize($data);
		if (!empty($id)) {
			$this->currentId = $id;
			$this->currentGroup = $group;
			$this->_setCurrentFile();
		}
		if ($this->memoryCache)
			$this->_writeMemory($data);
		if ($this->autoCleanFrequency > 0) {
			if ($this->autoCleanFrequency == $this->writeCount) {
				$this->_clearCache(NULL);
				$this->writeCount = 1;
			} else {
				$this->writeCount++;
			}			
		}
		return $this->_writeFile($data);
	}
	
	//!-----------------------------------------------------------------
	// @function	CacheManager::loadMemoryState
	// @desc		Recupera do filesystem o estado anterior da cache em memória
	// @access		public
	// @param		id string		ID de cache
	// @param		group string	"CACHE_MANAGER_GROUP" Grupo de cache
	// @param		force bool		"FALSE" Se TRUE, ignora expiração da cache
	// @return		void
	//!-----------------------------------------------------------------
	function loadMemoryState($id, $group=CACHE_MANAGER_GROUP, $force=FALSE) {
		if ($this->memoryCache) {
			if ($data = $this->load($id, $group, $force)) {			
				if (!$this->autoSerialize)
					$data = unserialize($data);
				$this->_debug('Memory state loaded - ' . sizeof($data) . ' entries');
				$this->memoryTable = $data;
			}
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	CacheManager::saveMemoryState
	// @desc		Salva em filesystem o estado atual da cache em memória,
	//				permitindo uso em futuras requisições
	// @access		public
	// @param		id string		ID de cache
	// @param		group string	"CACHE_MANAGER_GROUP" Grupo de cache	
	// @return		void
	//!-----------------------------------------------------------------
	function saveMemoryState($id, $group=CACHE_MANAGER_GROUP) {
		if ($this->memoryCache) {
			$data = ($this->autoSerialize ? $this->memoryTable : serialize($this->memoryTable));
			$this->save($data, $id, $group);
		}
	}	

	//!-----------------------------------------------------------------
	// @function	CacheManager::clear
	// @desc		Limpa dados em cache
	// @access		public
	// @param		group string	"NULL" Grupo de cache
	// @return		bool
	// @note		Se for fornecido um grupo de cache, todos os dados deste grupo
	//				serão removidos da cache. Se for omitido, todos os arquivos
	//				de todos os grupos que possuírem validade expirada serão removidos
	//!-----------------------------------------------------------------
	function clear($group=NULL) {
		return $this->_clearCache($group);
	}
	
	//!-----------------------------------------------------------------
	// @function	CacheManager::remove
	// @desc		Remove um objeto da cache
	// @access		public
	// @param		id string		ID de cache
	// @param		group string	"CACHE_MANAGER_GROUP" Grupo de cache
	// @return		bool
	//!-----------------------------------------------------------------
	function remove($id, $group=CACHE_MANAGER_GROUP) {
		$this->currentId = $id;
		$this->currentGroup = $group;
		$this->_setCurrentFile();
		return (@unlink($this->currentFile));
	}
	
	//!-----------------------------------------------------------------
	// @function	CacheManager::_setCurrentFile
	// @desc		Define o nome do arquivo, baseado no ID e no grupo de cache
	// @access		private
	// @return		void
	//!-----------------------------------------------------------------
	function _setCurrentFile() {
		$id = ereg_replace("[^0-9a-zA-Z_\.\-\:]+", "", $this->currentId);
		$group = ereg_replace("[^0-9a-zA-Z_\.\-\:]+", "", $this->currentGroup);
		if ($this->obfuscateFileName)
			$this->currentFile = 'cache_' . md5($group) . '_' . md5($id);
		else
			$this->currentFile = 'cache_' . $group . '_' . $id;
	}
	
	//!-----------------------------------------------------------------
	// @function	CacheManager::_validateFile
	// @desc		Método responsável por verificar se um arquivo existe
	//				na cache em filesystem. Valida existência do arquivo,
	//				tempo de expiração e chama o método de leitura
	// @access		private
	// @param		force bool		"FALSE" Se TRUE, ignora expiração de cache
	// @return		mixed Dados em cache ou FALSE em caso de falha
	//!-----------------------------------------------------------------
	function _validateFile($force=FALSE) {
		$exists = file_exists($this->baseDir . $this->currentFile);
		if ($force) {
			if ($exists && $data = $this->_readFile()) {
				$this->_debug('File cache hit');
				$this->currentStatus = CACHE_HIT;
				return $data;
			}
		} else {
			if ($exists) {
				if (FileSystem::lastModified($this->baseDir . $this->currentFile) <= $this->lastValidTime) {
					$this->_debug('File cache is stale');
					$this->currentStatus = CACHE_STALE;
					return FALSE;
				}
				if ($data = $this->_readFile()) {
					$this->_debug('File cache hit');
					$this->currentStatus = CACHE_HIT;
					return $data;
				}
			}
		}
		if (!$exists)
			$this->_debug('File doesn\'t exists');
		$this->currentStatus = CACHE_MISS;
		return FALSE;		
	}
	
	//!-----------------------------------------------------------------
	// @function	CacheManager::_readFile
	// @desc		Executa a operação de leitura dos dados no arquivo em cache
	// @access		private
	// @return		mixed Dados do arquivo ou FALSE em caso de erros
	//!-----------------------------------------------------------------
	function _readFile() {
		clearstatcache();
		$file = @fopen($this->baseDir . $this->currentFile, 'r');
		if ($file !== FALSE) {
			$size = filesize($this->baseDir . $this->currentFile);
			if ($this->readChecksum) {				
				$savedChecksum = fread($file, 32);
				$savedData = fread($file, $size-32);
				$checksum = $this->_getChecksum($savedData);
				fclose($file);
				if ($savedChecksum != $checksum) {
					$this->_debug('Checksum error');
					touch($this->baseDir . $this->currentFile, time()-($this->lifeTime+3600));
					return FALSE;
				}
			} else {
				$savedData = fread($file, $size);
				fclose($file);
			}
			if ($this->memoryCache)
				$this->_writeMemory($savedData);
			return $savedData;
		}		
		$this->_debug('Read error');
		return FALSE;
	}
	
	//!-----------------------------------------------------------------
	// @function	CacheManager::_writeFile
	// @desc		Grava um objeto em um arquivo de cache
	// @access		private
	// @param		data mixed		Dados do objeto
	// @return		bool
	//!-----------------------------------------------------------------
	function _writeFile($data) {
		$file = @fopen($this->baseDir . $this->currentFile, 'w');
		if ($file !== FALSE) {
			if ($this->readChecksum) {
				$checksum = $this->_getChecksum($data);
				fwrite($file, $checksum, strlen($checksum));
			}
			fwrite($file, $data);
			fclose($file);
			return TRUE;
		}
		$this->_debug('Write error');
		return FALSE;
	}
	
	//!-----------------------------------------------------------------
	// @function	CacheManager::_readMemory
	// @desc		Verifica se o ID/grupo atual existe na cache em memória
	// @access		private
	// @return		mixed Valor do objeto ou FALSE se não for encontrado
	//!-----------------------------------------------------------------
	function _readMemory() {
		$bool = (isset($this->memoryTable[$this->currentFile]) ? $this->memoryTable[$this->currentFile] : FALSE);
		if ($bool !== FALSE) {
			$this->_debug('Memory cache hit');
			$this->currentStatus = CACHE_MEMORY_HIT;
		} else {
			$this->_debug('Memory cache miss');
		}
		return $bool;
	}
	
	//!-----------------------------------------------------------------
	// @function	CacheManager::_writeMemory
	// @desc		Grava um objeto na cache em memória
	// @access		private
	// @param		data mixed		Valor do objeto
	// @return		void
	// @note		Se o limite de memória for excedido, a entrada mais antiga
	//				da tabela em memória será removida
	//!-----------------------------------------------------------------
	function _writeMemory($data) {
		$this->memoryTable[$this->currentFile] = $data;
		if (sizeof($this->memoryTable) > $this->memoryLimit) {
			$this->_debug('Memory limit exceeded');
			list($key, $value) = each($this->memoryTable);
			unset($this->memoryTable[$key]);
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	CacheManager::_getChecksum
	// @desc		Monta o checksum do valor de um objeto
	// @access		private
	// @param		data mixed		Valor do objeto
	// @return		int Checksum
	//!-----------------------------------------------------------------
	function _getChecksum($data) {
		$func = $this->checksumType;
		switch($func) {
			case 'crc32' :
				return sprintf('% 32d', crc32($data));
			case 'md5' :
				return sprintf('% 32d', md5($data));
			case 'strlen' :
				return sprintf('% 32d', strlen($data));
			default :
				return sprintf('% 32d', crc32($data));
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	CacheManager::_clearCache
	// @desc		Método interno de limpeza de cache
	// @access		private
	// @param		group string	"NULL" Grupo de cache
	// @return		bool
	//!-----------------------------------------------------------------
	function _clearCache($group=NULL) {
		if (!empty($group)) {
			$pattern = ($this->obfuscateFileName ? 'cache_' . md5($group) . '_' : 'cache_' . $group . '_');
			$pattern = preg_quote($pattern, '/');
		} else {
			$pattern = 'cache_';
		}
		if ($this->memoryCache) {
			foreach ($this->memoryTable as $file => $data) {
				if (StringUtils::match($file, $pattern))
					unset($this->memoryTable[$file]);
			}
		}
		clearstatcache();
		if ($this->Dir->open($this->baseDir)) {
			$result = TRUE;
			$files = $this->Dir->getFileNames($pattern, FALSE);
			$i=0;
			foreach ($files as $file) {
				if (!empty($group)) {
					if (!@unkink($this->baseDir . $file)) {
						$result = FALSE;
						break;
					} else $i++;
				} elseif (FileSystem::lastModified($this->baseDir . $file) <= $this->lastValidTime) {
					if (!@unlink($this->baseDir . $file)) {
						$result = FALSE;
						break;
					} else $i++;						
				}
			}
			$this->Dir->close();
			return $result;
		}
		return FALSE;
	}
	
	//!-----------------------------------------------------------------
	// @function	CacheManager::_debug
	// @desc		Método utilitário de debug
	// @access		private
	// @param		msg string		Mensagem de debug
	// @return		void
	//!-----------------------------------------------------------------
	function _debug($msg) {
		if ($this->debug)
			println("CACHE DEBUG --- {$msg}");
	}
}
?>