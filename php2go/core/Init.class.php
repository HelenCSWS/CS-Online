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
// Nome do arquivo padr�o de configura��o do framework
define('PHP2GO_CONFIG_FILE_NAME', 'userConfig.php');
// @const PHP2GO_DEFAULT_LANGUAGE	"en-us"
// Representa a linguagem padr�o a ser utilizada quando n�o for definido um valor fixo nem habilitada auto detec��o
define('PHP2GO_DEFAULT_LANGUAGE', 'en-us');
// @const PHP2GO_DEFAULT_CHARSET	"iso-8859-1"
// Charset padr�o a ser utilizado quando requisitado e n�o fornecido pelo usu�rio
define('PHP2GO_DEFAULT_CHARSET', 'iso-8859-1');

//!-----------------------------------------------------------------
// @class		Init
// @desc		A classe Init � instanciada dentro do arquivo de defini��es
//				do framework, a fim de carregar o arquivo de configura��es do
//				usu�rio, o arquivo de entradas de linguagem e realizar valida��es
//				no ambiente atual
// @author		Marcos Pont
// @version		$Revision: 1.25 $
//!-----------------------------------------------------------------
class Init
{
	var $_Conf;			// @var _Conf Conf object					Cont�m as configura��es do usu�rio para o framework
	var $_Lang;			// @var _Lang LanguageBase object			Cont�m a tabela de linguagem utilizada no framework
	var $_Negotiator;	// @var _Negotiator LocaleNegotiator object	Utilizada para detectar par�metros de internacionaliza��o a partir dos headers enviados pelo browser cliente
	
	/* defini��es de localiza��o para cada uma das linguagens suportadas pelo PHP2Go */
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
	// @desc		Construtor da classe de inicializa��o do framework
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
	// @desc		Retorna uma inst�ncia �nica (singleton) da classe Init
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
	// @desc		Altera a tabela de linguagem em rela��o ao valor original da tabela de configura��o
	// @param		language string		C�digo da linguagem/idioma
	// @access		public	
	// @return		void
	//!-----------------------------------------------------------------
	function setLocale($language) {
		$this->_applyLocale($language);
	}
	
	//!-----------------------------------------------------------------
	// @function	Init::resetLocale
	// @desc		Verifica se existe um c�digo de linguagem definido pelo usu�rio 
	//				armazenado em um cookie ou na sess�o. Se existir, remove as refer�ncias
	//				existentes e reinicializa as tabelas de linguagem baseado na configura��o
	// @note		Este m�todo pode ser �til para remover uma escolha de linguagem 
	//				feita pelo usu�rio quando ele encerra sua sess�o
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
	// @desc		Este m�todo inicializa o conjunto de configura��es setadas
	//				pelo usu�rio, criando uma inst�ncia da classe Conf
	// @access		private
	// @return		void
	//!-----------------------------------------------------------------
	function _initConfig() {
		// verifica se o vetor de configura��o j� foi definido pelo usu�rio
		global $P2G_USER_CFG;
		if (isset($P2G_USER_CFG) && is_array($P2G_USER_CFG)) {
			$this->_Conf->setConfig($P2G_USER_CFG);
			$P2G_USER_CFG = NULL;
			return TRUE;
		}
		// verifica se existe um arquivo de configura��o na raiz do dom�nio
		if (@file_exists($_SERVER['DOCUMENT_ROOT'] . '/userConfig.php')) {
			$this->_Conf->loadConfig($_SERVER['DOCUMENT_ROOT'] . '/userConfig.php');
			return TRUE;
		}
		// inicializa a partir das configura��es padr�es do framework
		if (@file_exists(PHP2GO_ROOT . 'userConfig.php')) {
			$this->_Conf->loadConfig(PHP2GO_ROOT . 'userConfig.php');
		} else {
			setupError('The default configuration file <B>userConfig.php</B> was not found at <B>' . $php2goRoot . '</B>');
			return FALSE;
		}		
	}
	
	//!-----------------------------------------------------------------
	// @function	Init::_initSession
	// @desc		Inicializa a sess�o de usu�rio, aplicando as configura��es
	//				de nome, tempo de expira��o e caminho de serializa��o
	// @access		private
	// @return		void
	//!-----------------------------------------------------------------
	function _initSession() {
		// nome do cookie de sess�o
		$name = $this->_Conf->getConfig('SESSION_NAME');
		if (empty($name))
			$name = 'PHP2GO_SESSION';
		ini_set('session.name', $name);
		// tempo de expira��o das sess�es criadas
		$lifetime = $this->_Conf->getConfig('SESSION_LIFETIME');
		if ($lifetime)
			ini_set('session.gc_maxlifetime', $lifetime);
		// define o caminho onde a sess�o deve ser serializada
		$path = $this->_Conf->getConfig('SESSION_PATH');		
		if (!empty($path) && is_dir($path))
			session_save_path($path);
		@session_start();
	}
	
	//!-----------------------------------------------------------------
	// @function	Init::_initLocale
	// @desc		Inicializa as configura��es de linguagem e localiza��o a partir
	//				das configura��es definidas pelo usu�rio 
	// @note		Implementa altera��o din�mica da linguagem a partir de um par�metro 
	//				da requisi��o e auto detec��o a partir do cabe�alho Accept-Language
	// @note		Consulte o arquivo INSTALL.txt que acompanha o framework para saber mais
	//				sobre as possibilidades da entrada de configura��o 'LANGUAGE'
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
				// altera��o din�mica de linguagem por GET ou POST				
				if (!empty($param) && !empty($_REQUEST[$param]) && in_array($_REQUEST[$param], $supported)) {
					$language = $_REQUEST[$param];
					$userDefined = TRUE;
				}
				// linguagem armazenada em um cookie
				elseif (isset($_COOKIE['PHP2GO_LANGUAGE']) && in_array($_COOKIE['PHP2GO_LANGUAGE'], $supported)) {
					$language = $_COOKIE['PHP2GO_LANGUAGE'];
					$userDefined = TRUE;
				}
				// linguagem definida anteriormente armazenada na sess�o
				elseif (isset($_SESSION['PHP2GO_LANGUAGE']) && in_array($_SESSION['PHP2GO_LANGUAGE'], $supported)) {
					$language = $_SESSION['PHP2GO_LANGUAGE'];
				}
				// verifica se foi solicitada auto detec��o
				elseif (@$conf['AUTO_DETECT'] == TRUE) {					
					$language = $this->_Negotiator->negotiateLanguage($supported, $default);
				}
				// utiliza a linguagem padr�o definida pelo usu�rio
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
	// @desc		Verifica a vers�o do PHP instalada no sistema operacional
	//				do servidor
	// @access		private	
	// @return		void	
	// @note		A requisi��o m�nima para utiliza��o do PHP2Go � a vers�o 4.1.0
	//!-----------------------------------------------------------------
	function _checkPhpVersion() {
		if (!version_compare(PHP_VERSION, '4.1.0', '>=') == -1)
			setupError($this->_Lang->getLanguageValue('ERR_OLD_PHP_VERSION'), PHP_VERSION);
		if (version_compare(PHP_VERSION, '4.2.0') == -1)
			srand((double)microtime()*1000000);			
	}
	
	//!-----------------------------------------------------------------
	// @function	Init::_checkAbsoluteUri
	// @desc		Verifica se a chave ABSOLUTE_URI do arquivo de configura��o
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
	// @desc		Verifica o formato local de data definido no arquivo de configura��o
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
	// @desc		Aplica uma defini��o/altera��o de linguagem nas tabelas
	//				de configura��o e linguagem
	// @access		private
	// @param		language string		C�digo da linguagem
	// @param		userDefined bool	"FALSE" Definida pelo usu�rio?
	// @return		void
	//!-----------------------------------------------------------------
	function _applyLocale($language, $userDefined=FALSE) {
		global $ADODB_LANG;		
		if (isset($this->localeTable[$language])) {		
			// busca cada uma das defini��es para a linguagem
			$locale = $this->localeTable[$language][0];
			$langName = $this->localeTable[$language][1];
			$calendarLang = $this->localeTable[$language][2];
			$adodbLang = $this->localeTable[$language][3];
			// define a localiza��o utilizando a fun��o setlocale
			if (version_compare(PHP_VERSION, '4.3.0', '>=')) {
				$params = array_merge(array(LC_ALL), $locale);
				call_user_func_array('setlocale', $params);
			} else {
				setlocale(LC_ALL, $locale[0]);
			}
			// modifica a configura��o
			$this->_Conf->setConfig('LOCALE', $locale[0]);
			$this->_Conf->setConfig('LANGUAGE_CODE', $language);		
			$this->_Conf->setConfig('LANGUAGE_NAME', $langName);
			$this->_Conf->setConfig('CALENDAR_LANGFILE', $calendarLang);
			// grava na sess�o se foi definida pelo usu�rio
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
