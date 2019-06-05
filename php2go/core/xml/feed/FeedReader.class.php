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
// $Header: /www/cvsroot/php2go/core/xml/feed/FeedReader.class.php,v 1.5 2005/07/18 22:50:25 mpont Exp $
// $Date: 2005/07/18 22:50:25 $

//------------------------------------------------------------------
import('php2go.cache.CacheManager');
import('php2go.net.HttpClient');
import('php2go.net.Url');
import('php2go.xml.XmlParser');
import('php2go.xml.feed.Feed');
//------------------------------------------------------------------

//!-----------------------------------------------------------------
// @class		FeedReader
// @desc		A classe FeedReader busca dados de canais de informa��o do
//				tipo RSS ou ATOM em URLs remotas, armazenando-as em uma estrutura
//				de objetos (FeedChannel, FeedItem). Possui suporte a cache dos
//				dados remotos, com tempo de expira��o, e � compat�vel com os padr�es
//				RSS 0.9x, 1.0, 2.0 e ATOM 0.x
// @package		php2go.xml.feed
// @extends		PHP2Go
// @uses		CacheManager
// @uses		FeedChannel
// @uses		FeedItem
// @uses		HttpClient
// @uses		TypeUtils
// @uses		XmlParser
// @author		Marcos Pont
// @version		$Revision: 1.5 $
//!-----------------------------------------------------------------
class FeedReader extends PHP2Go
{
	var $cacheEnabled = FALSE;				// @var cacheEnabled bool				"FALSE" Flag que indica se o mecanismo de cache est� habilitado
	var $cacheGroup = 'php2goFeedReader';	// @var cacheGroup string				"php2goFeedReader" Nome de grupo de cache a ser utilizado
	var $targetEncoding;					// @var targetEncoding string			Codifica��o a ser usada na fun��es de montagem da estrutura do feed
	var $userAgent;							// @var userAgent string				User agent a ser enviado na requisi��o HTTP
	var $Cache = NULL;						// @var Cache CacheManager object		Gerenciador de cache utilizado na classe
	var $Url = NULL;						// @var Url Url object					Utilizada na conex�o HTTP com a URL remota
	var $_lastResponse = NULL;				// @var _lastResponse array				"NULL" Resposta da �ltima requisi��o feita	
	var $_currentFeed;						// @var _currentFeed Feed object		Armazena o objeto Feed depois da execu��o do m�todo fetch
	var $_currentItem;						// @var _currentItem FeedItem object	Controle para itens de um feed
	var $_currentCompElement;				// @var _currentCompElement array		Controle para elementos compostos/m�ltiplos de canais/itens
	var $_currentElement;					// @var _currentElement array			Controle para elementos
	var $_currentAttrs;						// @var _currentAttrs array				Controle para atributos de elementos
	
	//!-----------------------------------------------------------------
	// @function	FeedReader::FeedReader
	// @desc		Construtor da classe
	// @access		public
	//!-----------------------------------------------------------------
	function FeedReader() {
		parent::PHP2Go();
		// verifica a exist�ncia da xml extension
		if (!function_exists('xml_parser_create'))
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_UNSUPPORTED_EXTENSION', 'xml'), E_USER_ERROR, __FILE__, __LINE__);
		$this->targetEncoding = PHP2Go::getConfigVal('CHARSET', FALSE);
		$this->userAgent = 'PHP2Go Feed Reader ' . PHP2GO_VERSION . ' (compatible; MSIE 6.0; Linux)';
		$this->Cache = CacheManager::getInstance();
		parent::registerDestructor($this, '_FeedReader');
	}
	
	//!-----------------------------------------------------------------
	// @function	FeedReader::_FeedReader
	// @desc		Destrutor da classe
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function _FeedReader() {
		if ($this->cacheEnabled)
			$this->Cache->saveMemoryState('__memCache', $this->cacheGroup);
	}
	
	//!-----------------------------------------------------------------
	// @function	FeedReader::getLastResponse
	// @desc		Retorna a resposta da �ltima requisi��o realizada
	// @access		public
	// @return		array Array com 2 elementos: headers (cabe�alhos de resposta) e body (corpo da resposta)
	// @note		Se um feed for carregado da cache em filesystem, os headers de resposta 
	//				correspondem � �ltima vez em que o feed foi lido de sua fonte original	
	//!-----------------------------------------------------------------
	function getLastResponse() {
		return $this->_lastResponse;
	}
	
	//!-----------------------------------------------------------------
	// @function	FeedReader::setCacheProperties
	// @desc		Habilita o uso de cache na classe, e configura algumas propriedades
	// @access		public
	// @param		dir string		Diret�rio base para a cache
	// @param		lifeTime int	"NULL" Tempo de expira��o em segundos
	// @return		void
	//!-----------------------------------------------------------------
	function setCacheProperties($dir, $lifeTime=NULL) {
		$this->Cache->setBaseDir($dir);
		if ($lifeTime)
			$this->Cache->setLifeTime($lifeTime);
		$this->Cache->loadMemoryState('__memCache', $this->cacheGroup);
		$this->cacheEnabled = TRUE;
	}
	
	//!-----------------------------------------------------------------
	// @function	FeedReader::setTargetEncoding
	// @desc		Define a codifica��o a ser usada pelo parser expat do PHP
	//				na interpreta��o dos nodos do conte�do XML
	// @access		public
	// @param		encoding string		Tipo de codifica��o
	// @return		void
	// @note		Os valores v�lidos s�o iso-8859-1, utf-8 e us-ascii
	//!-----------------------------------------------------------------
	function setTargetEncoding($encoding) {
		$this->targetEncoding = $encoding;
	}
	
	//!-----------------------------------------------------------------
	// @function	FeedReader::setUserAgent
	// @desc		Seta o user agent a ser enviado nas requisi��es HTTP
	// @access		public
	// @param		userAgent string	Valor para o user agent
	// @return		void
	//!-----------------------------------------------------------------
	function setUserAgent($userAgent) {
		$this->userAgent = $userAgent;
	}
	
	//!-----------------------------------------------------------------
	// @function	FeedReader::fetch
	// @desc		Busca os dados de um canal de informa��o (feed) a partir
	//				de um endere�o URL. Executa uma requisi��o HTTP para o endere�o
	//				fornecido caso o mecanismo de cache n�o encontrar uma vers�o
	//				j� armazenada ou estiver desabilitado
	// @access		public
	// @param		url mixed URL do feed
	// @return		FeedChannel object Em caso de falhas, este m�todo ir� retornar NULL
	//!-----------------------------------------------------------------
	function fetch($url) {
		$this->_reset();
		$this->Url = (TypeUtils::isInstanceOf($url, 'url') ? $url : new Url($url));
		// cache habilitada
		if ($this->cacheEnabled) {
			// cache hit
			$data = $this->Cache->load($this->Url->getUrl(), $this->cacheGroup);
			if ($data !== FALSE) {
				$this->_lastResponse = $data['response'];
				$this->_currentFeed = $data['feed'];
				return $this->_currentFeed;
			// cache miss
			} elseif ($this->_fetchFeed()) {
				$data = array(
					'response' => $this->_lastResponse,
					'feed' => $this->_currentFeed
				);
				$this->Cache->save($data, $this->Url->getUrl(), $this->cacheGroup);
				return $this->_currentFeed;
			}
		// fetch normal
		} elseif ($this->_fetchFeed()) {
			return $this->_currentFeed;			
		}
		return NULL;
	}
	
	//!-----------------------------------------------------------------
	// @function	FeedReader::_fetchFeed
	// @desc		M�todo interno que busca o conte�do do feed atrav�s
	//				de uma requisi��o HTTP
	// @access		private
	// @return		bool
	//!-----------------------------------------------------------------
	function _fetchFeed() {
		static $Http;
		if (!isset($Http)) {
			$Http =& new HttpClient();
			$Http->setFollowRedirects(TRUE);
			$Http->setUserAgent($this->userAgent);
		}
		$Http->setHost($this->Url->getHost());
		$uri = TypeUtils::ifNull($this->Url->getPath() . $this->Url->getQueryString(TRUE), '/');
		$status = $Http->doGet($uri);
		$this->_lastResponse = array(
			'headers' => $Http->responseHeaders,
			'body' => $Http->responseBody
		);
		if ($status == HTTP_STATUS_OK) {
			return $this->_parseFeed($this->_lastResponse['body']);
		}
		return FALSE;
	}
	
	//!-----------------------------------------------------------------
	// @function	FeedReader::_parseFeed
	// @desc		M�todo interno de interpreta��o do conte�do XML do feed
	// @access		private
	// @param		content string	Conte�do XML do feed
	// @return		bool
	//!-----------------------------------------------------------------
	function _parseFeed($content) {
		$parser = XmlParser::createParser(
			NULL, $content, 
			array(
				XML_OPTION_TARGET_ENCODING => $this->targetEncoding,
				XML_OPTION_SKIP_WHITE => 1,
				XML_OPTION_CASE_FOLDING => 0
			)
		);
		xml_set_object($parser, $this);
		xml_set_element_handler($parser, '_startElement', '_endElement');
		xml_set_character_data_handler($parser, '_characterData');		
		if (!xml_parse($parser, $content)) {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_XML_PARSE', array(xml_error_string(xml_get_error_code($parser)), xml_get_current_line_number($parser), xml_get_current_column_number($parser))), E_USER_ERROR, __FILE__, __LINE__);
			return FALSE;
		}
		xml_parser_free($parser);
		return TRUE;
	}
	
	//!-----------------------------------------------------------------
	// @function	FeedReader::_startElement
	// @desc		Tratador de in�cio de tag para o parser XML do feed
	// @access		private
	// @param		parser resource Refer�ncia para o parser associado a este tratador
	// @param		element string	Nome do elemento (nodo)
	// @param		attrs array		Atributos do elemento
	// @return		void
	//!-----------------------------------------------------------------
	function _startElement($parser, $element, $attrs) {
		$name = NULL; $ns = NULL;
		$this->_parseNodeName($element, $name, $ns);
		if (!isset($this->_currentFeed)) {
			$this->_currentFeed =& new Feed($name, @$attrs['version']);
			$this->_currentFeed->setEtag(@$this->_lastResponse['headers']['Etag']);
			$this->_currentFeed->setLastModified(@$this->_lastResponse['headers']['Last-Modified']);
			$this->_currentFeed->setSyndicationURL($this->Url->getUrl());
			$this->_currentFeed->setChannel(new FeedChannel());
		} else {
			switch (strtolower($name)) {
				// channel: apenas no formato FEED_RSS
				case 'channel' :
					break;
				// item e entry: especifica��o de um item no canal
				case 'entry' :
				case 'item' :
					$this->_currentItem =& new FeedItem();
					if (isset($attrs['rdf:about']))
						$this->_currentItem->setElement('rdf:about', $attrs['rdf:about']);
					break;
				// imagem e textinput: elementos com atributos internos
				case 'image' :				
				case 'textinput' :
					$this->_currentCompElement = array($name, (!empty($attrs) ? $attrs : array()), FALSE);
					break;
				// contributor: elemento com atributos internos e m�ltiplo
				case 'contributor' :
					$this->_currentCompElement = array($name, (!empty($attrs) ? $attrs : array()), TRUE);
					break;
				// author: com atributos internos no formato FEED_ATOM
				case 'author' :
					if ($this->_currentFeed->isATOM())
						$this->_currentCompElement = array($name, array(), FALSE);
					else
						$this->_currentElement = array($name, '', FALSE);
					break;
				// outras tags
				default :
					$multiple = (($this->_currentFeed->isATOM() && $name == 'LINK') || $name == 'CATEGORY' ? TRUE : FALSE);
					if ($this->_currentFeed->isATOM())
						$this->_currentElement = array($name, '', $multiple);
					else
						$this->_currentElement = array($element, '', $multiple);
					$this->_currentAttrs = $attrs;
					break;
			}
		}
	}
		
	//!-----------------------------------------------------------------
	// @function	FeedReader::_endElement
	// @desc		Tratador de final de tag para o parser XML do feed
	// @access		private
	// @param		parser resource 	Refer�ncia para o parser associado a este tratador
	// @param		element string		Nome do elemento
	// @return		void
	//!-----------------------------------------------------------------
	function _endElement($parser, $element) {
		$name = NULL; $ns = NULL;
		$this->_parseNodeName($element, $name, $ns);
		switch (strtolower($name)) {
			// tag inicial
			case 'rss' :
			case 'rdf' :
				break;
			// channel, feed: inser��o de canal
			case 'channel' :
			case 'feed' :
				break;
			// entry, item: inser��o de item/entrada no canal
			case 'entry' :
			case 'item' :
				$this->_currentFeed->Channel->addItem($this->_currentItem);
				$this->_currentItem = NULL;
				break;
			default :
				// inser��o de elemento composto e/ou m�ltiplo
				if (isset($this->_currentCompElement) && $name == $this->_currentCompElement[0]) {
					if ($this->_currentCompElement[2] === TRUE)
						$this->_currentFeed->Channel->addElement($this->_currentCompElement[0], $this->_currentCompElement[1]);
					else
						$this->_currentFeed->Channel->setElement($this->_currentCompElement[0], $this->_currentCompElement[1]);
					$this->_currentCompElement = NULL;
				// inser��o de elemento simples
				} else {
					if (empty($this->_currentElement[1]) && !empty($this->_currentAttrs))
						$this->_currentElement[1] = $this->_currentAttrs;
					if (isset($this->_currentCompElement)) {
						$this->_currentCompElement[1][$this->_currentElement[0]] = $this->_currentElement[1];
					} else {
						// elemento de item
						if (isset($this->_currentItem)) {
							if ($this->_currentElement[2] === TRUE)
								$this->_currentItem->addElement($this->_currentElement[0], $this->_currentElement[1]);
							else								
								$this->_currentItem->setElement($this->_currentElement[0], $this->_currentElement[1]);
						} 
						// elemento de feed
						else {
							if ($this->_currentElement[2] === TRUE)
								$this->_currentFeed->Channel->addElement($this->_currentElement[0], $this->_currentElement[1]);
							else
								$this->_currentFeed->Channel->setElement($this->_currentElement[0], $this->_currentElement[1]);
						}
					}
					$this->_currentElement = NULL;
					$this->_currentAttrs = NULL;
				}
		}		
	}
	
	//!-----------------------------------------------------------------
	// @function	FeedReader::_characterData
	// @desc		Tratador de conte�do de nodo (character data) para o parser XML do feed
	// @access		private
	// @param		parser resource	Refer�ncia para o parser XML do feed
	// @param		text string		Conte�do do nodo em forma de string
	// @return		void
	//!-----------------------------------------------------------------
	function _characterData($parser, $text) {
		if (isset($this->_currentElement))
			$this->_currentElement[1] .= $text;
	}
	
	//!-----------------------------------------------------------------
	// @function	FeedReader::_parseNodeName
	// @desc		M�todo utilit�rio para retornar o namespace e o nome
	//				a partir de um elemento XML
	// @access		private
	// @param		qualifiedName string	Nome completo do elemento
	// @param		&name string			Refer�ncia para retorno do nome
	// @param		&ns string				Refer�ncia para retorno do namespace
	// @return		void
	//!-----------------------------------------------------------------
	function _parseNodeName($qualifiedName, &$name, &$ns) {
		$matches = array();
		if (preg_match("/^(([^\:]+)\:)?(.*)$/", $qualifiedName, $matches)) {
			$name = $matches[3];
			$ns = TypeUtils::ifNull($matches[2], '');
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	FeedReader::_reset
	// @desc		Reseta as propriedades tempor�rias de controle
	// @access		private
	// @return		void
	//!-----------------------------------------------------------------
	function _reset() {
		$this->_lastResponse = NULL;
		$this->_currentFeed = NULL;
		$this->_currentItem = NULL;
		$this->_currentCompElement = NULL;
		$this->_currentElement = NULL;
		$this->_currentAttrs = array();
	}	
}
?>