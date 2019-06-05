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
// $Header: /www/cvsroot/php2go/core/Init.class.php,v 1.25 2005/08/30 14:08:12 mpont Exp $
// $Date: 2005/08/30 14:08:12 $

// @const PHP2GO_CONFIG_FILE_NAME	"userConfig.php"
// Nome do arquivo padrão de configuração do framework
define('PHP2GO_CONFIG_FILE_NAME', 'userConfig.php');
// @const PHP2GO_DEFAULT_LANGUAGE	"en-us"
// Representa a linguagem padrão a ser utilizada quando não for definido um valor fixo nem habilitada auto detecção
define('PHP2GO_DEFAULT_LANGUAGE', 'en-us');
// @const PHP2GO_DEFAULT_CHARSET	"iso-8859-1"
// Charset padrão a ser utilizado quando requisitado e não fornecido pelo usuário
define('PHP2GO_DEFAULT_CHARSET', 'iso-8859-1');

//!-----------------------------------------------------------------
// @class		Init
// @desc		A classe Init é instanciada dentro do arquivo de definições
//				do framework, a fim de carregar o arquivo de configurações do
//				usuário, o arquivo de entradas de linguagem e realizar validações
//				no ambiente atual
// @author		Marcos Pont
// @version		$Revision: 1.25 $
//!-----------------------------------------------------------------
class Init
{
	var $_Conf;			// @var _Conf Conf object					Contém as configurações do usuário para o framework
	var $_Lang;			// @var _Lang LanguageBase object			Contém a tabela de linguagem utilizada no framework
	var $_Negotiator;	// @var _Negotiator LocaleNegotiator object	Utilizada para detectar parâmetros de internacionalização a partir dos headers enviados pelo browser cliente
	
	/* definições de localização para cada uma das linguagens suportadas pelo PHP2Go */
	var $localeTable = array(
		'pt-br' => array(array('pt_BR', 'portuguese', 'pt_BR.iso-8859-1', 'pt_BR.utf-8'), 'brazilian-portuguese', 'calendar-br.js', 'pt-br'),
		'en-us' => array(array('en_US', 'en'), 'us-english', 'calendar-en.js', 'en'),
		'es' => array(array('es_ES', 'es'), 'spanish', 'calendar-es.js', 'es'),
		'cs' => array(array('cs_CZ', 'cz'), 'czech', 'calendar-cs-win.js', 'cz'),
		'it' => array(array('it_IT', 'it'), 'italian', 'calendar-it.js', 'it'),
		'de-de' => array(array('de_DE', 'de', 'ge'), 'de-german', 'calendar-de.js', 'de'),
		'fr-fr' => array(array('fr_FR', 'fr'), 'french', 'calendar-fr.js', 'fr')
	);

	//!-----------------------------------------------------------------
	// @function	Init::Init
	// @desc		Construtor da classe de inicialização do framework
	// @access		public
	//!-----------------------------------------------------------------
	function Init() {
		$this->_Conf =& Conf::getInstance();
		$this->_Lang =& LanguageBase::getInstance();
		$this->_Negotiator =& LocaleNegotiator::getInstance();
		$this->_initConfig();
		$this->_initSession();
		$this->_initLocale();
		$this->_checkPhpVersion();
		$this->_checkAbsoluteUri();
		$this->_checkDateFormat();
	}
	
	//!-----------------------------------------------------------------
	// @function	Init::&getInstance
	// @desc		Retorna uma instância única (singleton) da classe Init
	// @access		public
	// @return		Init object
	// @static
	//!-----------------------------------------------------------------
	function &getInstance() {
		static $instance;
		if (!isset($instance))
			$instance = new Init();
		return $instance;
	}
	
	//!-----------------------------------------------------------------
	// @function	Init::setLocale
	// @desc		Altera a tabela de linguagem em relação ao valor original da tabela de configuração
	// @param		language string		Código da linguagem/idioma
	// @access		public	
	// @return		void
	//!-----------------------------------------------------------------
	function setLocale($language) {
		$this->_applyLocale($language);
	}
	
	//!-----------------------------------------------------------------
	// @function	Init::resetLocale
	// @desc		Verifica se existe um código de linguagem definido pelo usuário 
	//				armazenado em um cookie ou na sessão. Se existir, remove as referências
	//				existentes e reinicializa as tabelas de linguagem baseado na configuração
	// @note		Este método pode ser útil para remover uma escolha de linguagem 
	//				feita pelo usuário quando ele encerra sua sessão
	// @return		void
	//!-----------------------------------------------------------------
	function resetLocale() {
		if (isset($_SESSION['PHP2GO_LANGUAGE']) || isset($_COOKIE['PHP2GO_LANGUAGE'])) {
			unset($_SESSION['PHP2GO_LANGUAGE']);
			setcookie('PHP2GO_LANGUAGE', @$_COOKIE['PHP2GO_LANGUAGE'], time()-1440);
			$this->initLocale();			
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	Init::_initConfig
	// @desc		Este método inicializa o conjunto de configurações setadas
	//				pelo usuário, criando uma instância da classe Conf
	// @access		private
	// @return		void
	//!-----------------------------------------------------------------
	function _initConfig() {
		// verifica se o vetor de configuração já foi definido pelo usuário
		global $P2G_USER_CFG;
		if (isset($P2G_USER_CFG) && is_array($P2G_USER_CFG)) {
			$this->_Conf->setConfig($P2G_USER_CFG);
			$P2G_USER_CFG = NULL;
			return TRUE;
		}
		// verifica se existe um arquivo de configuração na raiz do domínio
		if (@file_exists($_SERVER['DOCUMENT_ROOT'] . '/userConfig.php')) {
			$this->_Conf->loadConfig($_SERVER['DOCUMENT_ROOT'] . '/userConfig.php');
			return TRUE;
		}
		// inicializa a partir das configurações padrões do framework
		if (@file_exists(PHP2GO_ROOT . 'userConfig.php')) {
			$this->_Conf->loadConfig(PHP2GO_ROOT . 'userConfig.php');
		} else {
			setupError('The default configuration file <B>userConfig.php</B> was not found at <B>' . $php2goRoot . '</B>');
			return FALSE;
		}		
	}
	
	//!-----------------------------------------------------------------
	// @function	Init::_initSession
	// @desc		Inicializa a sessão de usuário, aplicando as configurações
	//				de nome, tempo de expiração e caminho de serialização
	// @access		private
	// @return		void
	//!-----------------------------------------------------------------
	function _initSession() {
		// nome do cookie de sessão
		$name = $this->_Conf->getConfig('SESSION_NAME');
		if (empty($name))
			$name = 'PHP2GO_SESSION';
		ini_set('session.name', $name);
		// tempo de expiração das sessões criadas
		$lifetime = $this->_Conf->getConfig('SESSION_LIFETIME');
		if ($lifetime)
			ini_set('session.gc_maxlifetime', $lifetime);
		// define o caminho onde a sessão deve ser serializada
		$path = $this->_Conf->getConfig('SESSION_PATH');		
		if (!empty($path) && is_dir($path))
			session_save_path($path);
		@session_start();
	}
	
	//!-----------------------------------------------------------------
	// @function	Init::_initLocale
	// @desc		Inicializa as configurações de linguagem e localização a partir
	//				das configurações definidas pelo usuário 
	// @note		Implementa alteração dinâmica da linguagem a partir de um parâmetro 
	//				da requisição e auto detecção a partir do cabeçalho Accept-Language
	// @note		Consulte o arquivo INSTALL.txt que acompanha o framework para saber mais
	//				sobre as possibilidades da entrada de configuração 'LANGUAGE'
	// @access		private
	// @return		void	
	//!-----------------------------------------------------------------
	function _initLocale() {
		// linguagem
		$conf = $this->_Conf->getConfig('LANGUAGE');
		$userDefined = FALSE;
		if (!empty($conf)) {
			if (is_array($conf)) {
				$default = (isset($conf['DEFAULT']) ? $conf['DEFAULT'] : PHP2GO_DEFAULT_LANGUAGE);
				$param = (!empty($conf['REQUEST_PARAM']) ? $conf['REQUEST_PARAM'] : NULL);
				$supported = (isset($conf['AVAILABLE']) ? (array)$conf['AVAILABLE'] : array_keys($this->localeTable));
				// alteração dinâmica de linguagem por GET ou POST				
				if (!empty($param) && !empty($_REQUEST[$param]) && in_array($_REQUEST[$param], $supported)) {
					$language = $_REQUEST[$param];
					$userDefined = TRUE;
				}
				// linguagem armazenada em um cookie
				elseif (isset($_COOKIE['PHP2GO_LANGUAGE']) && in_array($_COOKIE['PHP2GO_LANGUAGE'], $supported)) {
					$language = $_COOKIE['PHP2GO_LANGUAGE'];
					$userDefined = TRUE;
				}
				// linguagem definida anteriormente armazenada na sessão
				elseif (isset($_SESSION['PHP2GO_LANGUAGE']) && in_array($_SESSION['PHP2GO_LANGUAGE'], $supported)) {
					$language = $_SESSION['PHP2GO_LANGUAGE'];
				}
				// verifica se foi solicitada auto detecção
				elseif (@$conf['AUTO_DETECT'] == TRUE) {					
					$language = $this->_Negotiator->negotiateLanguage($supported, $default);
				}
				// utiliza a linguagem padrão definida pelo usuário
				else {
					$language = $default;
				}
			} else {
				$language = (string)$conf;
			}
			// aplica a linguagem definida
			$this->_applyLocale($language, $userDefined);
		} else {
			$this->_applyLocale(PHP2GO_DEFAULT_LANGUAGE);
		}
		// charset
		$userCharset = $this->_Conf->getConfig('CHARSET');
		if ($userCharset == 'auto') {
			$charset = $this->_Negotiator->negotiateCharset(array('iso-8859-1', 'utf-8'), PHP2GO_DEFAULT_CHARSET);
			$this->_Conf->setConfig('CHARSET', $charset);
			ini_set('default_charset', $charset);
		} elseif (empty($userCharset)) {
			$this->_Conf->setConfig('CHARSET', PHP2GO_DEFAULT_CHARSET);
			ini_set('default_charset', PHP2GO_DEFAULT_CHARSET);
		}		
	}
	
	//!-----------------------------------------------------------------
	// @function	Init::_checkPhpVersion
	// @desc		Verifica a versão do PHP instalada no sistema operacional
	//				do servidor
	// @access		private	
	// @return		void	
	// @note		A requisição mínima para utilização do PHP2Go é a versão 4.1.0
	//!-----------------------------------------------------------------
	function _checkPhpVersion() {
		if (!version_compare(PHP_VERSION, '4.1.0', '>=') == -1)
			setupError($this->_Lang->getLanguageValue('ERR_OLD_PHP_VERSION'), PHP_VERSION);
		if (version_compare(PHP_VERSION, '4.2.0') == -1)
			srand((double)microtime()*1000000);			
	}
	
	//!-----------------------------------------------------------------
	// @function	Init::_checkAbsoluteUri
	// @desc		Verifica se a chave ABSOLUTE_URI do arquivo de configuração
	//				foi definida e se a mesma possui um valor correto
	// @access		private
	// @return		void	
	//!-----------------------------------------------------------------
	function _checkAbsoluteUri() {
		if (!$uri = $this->_Conf->getConfig('ABSOLUTE_URI')) {
			setupError($this->_Lang->getLanguageValue('ERR_ABSOLUTE_URI_NOT_FOUND'));
		} else {
			$pattern = "/^https?\:\/\/[a-zA-Z0-9\-\.\/\:~]+$/";
			if (!preg_match($pattern, $uri)) {
				setupError(sprintf($this->_Lang->getLanguageValue('ERR_URL_MALFORMED'), "'ABSOLUTE_URI'"));
			} else {
				$uriArr = @parse_url($uri);
				if (empty($uriArr)) {
					setupError(sprintf($this->_Lang->getLanguageValue('ERR_URL_MALFORMED'), "'ABSOLUTE_URI'"));
				} else {
					if (substr($uri, strlen($uri)-1, 1) != '/') {
						$this->_Conf->setConfig('ABSOLUTE_URI', $uri . '/');
					}
				}
			}
		}	
	}
	
	//!-----------------------------------------------------------------
	// @function	Init::_checkDateFormat
	// @desc		Verifica o formato local de data definido no arquivo de configuração
	// @access		private
	// @return		void	
	//!-----------------------------------------------------------------
	function _checkDateFormat() {
		$dateFormat = $this->_Conf->getConfig('LOCAL_DATE_FORMAT');
		if ($dateFormat) {
			switch ($dateFormat) {
				case 'd/m/Y' :
					$this->_Conf->setConfig('LOCAL_DATE_TYPE', 'EURO');
					break;
				case 'Y/m/d' :
					$this->_Conf->setConfig('LOCAL_DATE_TYPE', 'US');
					break;
				default :
					$this->_Conf->setConfig('LOCAL_DATE_TYPE', 'EURO');
					break;				
			}
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	Init::_applyLocale
	// @desc		Aplica uma definição/alteração de linguagem nas tabelas
	//				de configuração e linguagem
	// @access		private
	// @param		language string		Código da linguagem
	// @param		userDefined bool	"FALSE" Definida pelo usuário?
	// @return		void
	//!-----------------------------------------------------------------
	function _applyLocale($language, $userDefined=FALSE) {
		global $ADODB_LANG;		
		if (isset($this->localeTable[$language])) {		
			// busca cada uma das definições para a linguagem
			$locale = $this->localeTable[$language][0];
			$langName = $this->localeTable[$language][1];
			$calendarLang = $this->localeTable[$language][2];
			$adodbLang = $this->localeTable[$language][3];
			// define a localização utilizando a função setlocale
			if (version_compare(PHP_VERSION, '4.3.0', '>=')) {
				$params = array_merge(array(LC_ALL), $locale);
				call_user_func_array('setlocale', $params);
			} else {
				setlocale(LC_ALL, $locale[0]);
			}
			// modifica a configuração
			$this->_Conf->setConfig('LOCALE', $locale[0]);
			$this->_Conf->setConfig('LANGUAGE_CODE', $language);		
			$this->_Conf->setConfig('LANGUAGE_NAME', $langName);
			$this->_Conf->setConfig('CALENDAR_LANGFILE', $calendarLang);
			// grava na sessão se foi definida pelo usuário
			if ($userDefined) {
				$_SESSION['PHP2GO_LANGUAGE'] = $language;
				setcookie('PHP2GO_LANGUAGE', $language, time()+1440);
			}				
			// carrega a tabela de linguagem do framework
			$this->_Lang->clearLanguageBase();
			$this->_Lang->loadLanguageTableByFile(PHP2GO_ROOT . 'languages/' . $langName . '.inc', 'PHP2GO');
			$ADODB_LANG = $adodbLang;
		} else {
			setupError("The language <B>\"{$language}\"</B> is not supported by PHP2Go.");
		}		
	}
}
?>
