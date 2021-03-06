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
// $Header: /www/cvsroot/php2go/core/xml/feed/Feed.class.php,v 1.4 2005/06/08 22:22:50 mpont Exp $
// $Date: 2005/06/08 22:22:50 $

//------------------------------------------------------------------
import('php2go.xml.feed.FeedChannel');
//------------------------------------------------------------------

// @const FEED_RSS "RSS"
// Constante para feeds do tipo RSS
define('FEED_RSS', 'RSS');
// @const FEED_ATOM "ATOM"
// Constante para feeds do tipo ATOM
define('FEED_ATOM', 'ATOM');

//!-----------------------------------------------------------------
// @class		Feed
// @desc		Esta classe funciona como base para um feed (conjunto de informa��es),
//				constitu�do por um canal (FeedChannel) e um ou mais itens (FeedItem).
//				Al�m disso, uma inst�ncia da classe Feed possui um c�digo de tipo 
//				(FEED_RSS ou FEED_ATOM), a propriedade etag (hash do feed) e a data 
//				da �ltima modifica��o das informa��es
// @package		php2go.xml.feed
// @extends		FeedNode
// @uses		TypeUtils
// @author		Marcos Pont
// @version		$Revision: 1.4 $
//!-----------------------------------------------------------------
class Feed extends FeedNode
{
	var $type;				// @var type int					Tipo de feed (FEED_RSS ou FEED_ATOM)
	var $version;			// @var version string				Vers�o do formato (utilizado em feeds RSS)
	var $contentType;		// @var contentType string			Content-type do feed
	var $etag;				// @var etag string					Hash do conte�do XML do feed
	var $lastModified;		// @var lastModified int			Timestamp da �ltima modifica��o do feed
	var $syndicationURL;	// @var syndicationURL string		URL de origem do feed
	var $Channel = NULL;	// @var Channel FeedChannel	object	Canal associado ao feed

	//!-----------------------------------------------------------------
	// @function	Feed::Feed
	// @desc		Construtor da classe
	// @access		public
	// @param		type int		Tipo do feed
	// @param		version string	"NULL" Vers�o
	//!-----------------------------------------------------------------	
	function Feed($type, $version=NULL) {
		parent::FeedNode();
		switch (strtoupper($type)) {
			case 'RDF' :
				$this->type = FEED_RSS;
				$this->version = '1.0';
				$this->contentType = 'application/xml';
				break;
			case 'RSS' :
				$this->type = FEED_RSS;
				$this->version = TypeUtils::ifNull($version, '2.0');
				$this->contentType = 'application/rss+xml';
				break;
			case 'ATOM' :
			case 'FEED' :
				$this->type = FEED_ATOM;
				$this->version = TypeUtils::ifNull($version, '0.3');
				$this->contentType = 'application/atom+xml';
				break;
			default :
				$this->type = FEED_RSS;
				$this->version = '2.0';
				$this->contentType = 'application/rss+xml';
				break;
		}		
	}
	
	//!-----------------------------------------------------------------
	// @function	Feed::isATOM
	// @desc		Verifica se o feed � do tipo ATOM
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------	
	function isATOM() {
		return ($this->type == FEED_ATOM);
	}
	
	//!-----------------------------------------------------------------
	// @function	Feed::isRSS
	// @desc		Verifica se o feed � do tipo RSS
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------	
	function isRSS() {
		return ($this->type == FEED_RSS);
	}
	
	//!-----------------------------------------------------------------
	// @function	Feed::getLastModified
	// @desc		Busca a data da �ltima atualiza��o do feed
	// @access		public
	// @param		fmt string	"r" Formato de apresenta��o da data/hora
	// @return		string Data formatada
	// @note		Se a data armazenada n�o for do tipo unix timestamp, o formato desejado n�o ser� aplicado
	//!-----------------------------------------------------------------
	function getLastModified($fmt='r') {
		return (TypeUtils::isInteger($this->lastModified) ? date($fmt, $this->lastModified) : $this->lastModified);
	}
	
	//!-----------------------------------------------------------------
	// @function	Feed::&getChannel
	// @desc		Busca o canal associado a este feed
	// @access		public
	// @return		FeedChannel object
	//!-----------------------------------------------------------------
	function &getChannel() {
		return $this->Channel;
	}
	
	//!-----------------------------------------------------------------
	// @function	Feed::getChannelElementNames
	// @desc		Retorna o conjunto de propriedades v�lidas para o canal,
	//				de acordo com o tipo e vers�o do feed
	// @access		public
	// @return		array Vetor de propriedades
	//!-----------------------------------------------------------------
	function getChannelElementNames() {
		if ($this->isRSS()) {
			switch ($this->version) {
				// RSS 0.9 e 0.91
				case '0.9' :
				case '0.91' :
					return array(
						'title', 'description', 'link', 'image', 'textinput'
					);
				// RSS 0.92, 0.93 e 0.94
				case '0.92' :
				case '0.93' :
				case '0.94' :
					return array(
						'title', 'description', 'link', 'category', 'image', 'textinput', 
						'cloud', 'language', 'copyright', 'docs', 'lastBuildDate', 
						'managingEditor', 'pubDate', 'rating', 'skipDays', 'skipHours'
					);
				// RSS 1.0
				case '1.0' :
					return array(
						'title', 'description', 'link', 'image', 'textinput', 'language',
						'copyright', 'docs', 'lastBuildDate', 'managingEditor', 'pubDate',
						'rating', 'skipDays', 'skipHours'
					);
				// RSS 2.0
				default :
					return array(
						'title', 'description', 'link', 'category', 'image', 'textinput', 
						'cloud', 'language', 'copyright', 'docs', 'lastBuildDate', 
						'managingEditor', 'webMaster', 'pubDate', 'rating', 'skipDays', 
						'skipHours', 'generator', 'ttl'
					);				
			}
		} else {
			// ATOM 0.x
			return array(
				'title', 'tagline', 'link', 'author', 'contributor', 'id', 'generator',
				'copyright', 'info', 'created', 'issued', 'published', 'updated', 'modified'
			);
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	Feed::getItemElementNames
	// @desc		Retorna o conjunto de propriedades v�lidas para um item do canal,
	//				de acordo com o tipo e vers�o do feed
	// @access		public
	// @return		array Vetor de propriedades v�lidas para um item
	//!-----------------------------------------------------------------
	function getItemElementNames() {
		if ($this->isRSS()) {
			switch ($this->version) {
				// RSS 0.9
				case '0.9' :
					return array(
						'title', 'link'
					);
				// RSS 0.91
				case '0.91 ':
					return array(
						'title', 'description', 'link'
					);
				// RSS 1.0
				case '1.0' :
					return array(
						'title', 'description', 'link', 'dc:date', 'dc:creator', 'dc:source', 'dc:format'
					);				
				// RSS 0.92, 0.93 e 0.94
				case '0.92' :
				case '0.93' :
				case '0.94' :
					return array(
						'title', 'description', 'link', 'category', 'enclosure', 'source'
					);
				// RSS 2.0 
				default :
					return array(
						'title', 'description', 'link', 'guid', 'author', 'pubDate', 'category', 'enclosure', 'source', 'comments'
					);					
			}
		} else {
			// ATOM 0.x
			return array(
				'title', 'link', 'author', 'contributor', 'id', 'created', 
				'issued', 'published', 'modified', 'updated', 'content', 'summary'
			);
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	Feed::setETag
	// @desc		Define o hash (ETag) do feed
	// @access		public
	// @param		hash string	Valor do hash
	// @return		void
	//!-----------------------------------------------------------------
	function setETag($hash) {
		$this->etag = $hash;
	}
	
	//!-----------------------------------------------------------------
	// @function	Feed::setLastModified
	// @desc		Seta o timestamp da �ltima modifica��o do feed
	// @access		public
	// @param		lastModified int	Timestamp
	// @return		void
	//!-----------------------------------------------------------------
	function setLastModified($lastModified) {
		$this->lastModified = parent::parseDate($lastModified);
	}
	
	//!-----------------------------------------------------------------
	// @function	Feed::setSyndicationURL
	// @desc		Define a URL de origem do feed
	// @access		public
	// @param		url string		URL de origem
	// @return		void
	//!-----------------------------------------------------------------
	function setSyndicationURL($url) {
		$this->syndicationURL = $url;
	}
	
	//!-----------------------------------------------------------------
	// @function	Feed::setChannel
	// @desc		Define o canal associado ao feed
	// @access		public
	// @param		Channel FeedChannel object
	// @return		void
	//!-----------------------------------------------------------------
	function setChannel($Channel) {
		if (TypeUtils::isInstanceOf($Channel, 'feedchannel'))
			$this->Channel = $Channel;
	}
	
	//!-----------------------------------------------------------------
	// @function	Feed::renderRootProperties
	// @desc		Monta uma estrutura com nome e atributos do nodo raiz
	//				da �rvore XML do feed para fins de renderiza��o
	// @access		public
	// @return		array Vetor contendo nome e atributos do nodo raiz
	//!-----------------------------------------------------------------
	function renderRootProperties() {
		if ($this->isRSS()) {
			if ($this->version == '1.0') {
				// RSS 1.0
				return array(
					'name' => 'rdf:RDF',
					'attrs' => array('xmlns' => 'http://purl.org/rss/1.0', 'xmlns:rdf' => 'http://www.w3.org/1999/02/22-rdf-syntax-ns#', 'xmlns:slash' => 'http://purl.org/rss/1.0/modules/slash/', 'xmlns:dc' => 'http://purl.org/dc/elements/1.1/')
				);
			} else {
				// RSS 0.9x e 2.0
				return array(
					'name' => 'rss',
					'attrs' => array('version' => $this->version)
				);
			}
		} else {
			// ATOM 0.x
			return array(
				'name' => 'feed',
				'attrs' => array('version' => $this->version, 'xmlns' => 'http://purl.org/atom/ns#')
			);
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	Feed::renderChannelElements
	// @desc		Este m�todo monta um vetor com os nomes e valores das 
	//				propriedades formatados para fins de gera��o do arquivo
	//				XML do feed (renderiza��o)
	// @access		public
	// @return		array Vetor de propriedades com valores formatados para exibi��o	
	//!-----------------------------------------------------------------
	function renderChannelElements() {
		if (TypeUtils::isInstanceOf($this->Channel, 'feedchannel')) {
			$result = array();
			$elements = $this->getChannelElementNames();
			foreach ($elements as $element) {
				// busca o valor da propriedade
				$value = $this->Channel->getElement($element);
				if (!$value)
					continue;
				// elementos de data/timestamp
				if (in_array($element, array('lastBuildDate', 'pubDate', 'modified', 'updated'))) {
					$result[$element] = htmlspecialchars(parent::buildDate($value, $this->type, $this->version));
				}
				// elementos onde os atributos s�o atributos de nodo, e n�o nodos filhos
				elseif ($element == 'cloud' || ($element == 'link' && $this->isATOM())) {
					$result[$element] = array('_attrs' => $this->_formatElementValue($value));
				} 
				// outros elementos
				else {
					$result[$element] = $this->_formatElementValue($value);
				}
			}
			// atributos e elementos especiais
			if ($this->isRSS() && $this->version == '1.0') {
				$result['_attrs'] = array('rdf:about' => htmlspecialchars($this->syndicationURL));
				$result['dc:date'] = htmlspecialchars(Date::formatTime(time(), DATE_FORMAT_ISO8601));
				if (isset($result['image']) && isset($result['image']['link']))
					$result['image']['_attrs'] = array('rdf:about' => htmlspecialchars($result['image']['link']));
				$items = array();
				foreach ($this->Channel->getChildren() as $item)
					$items[] = array('_attrs' => array('rdf:resource' => htmlspecialchars($item->getElement('link', ''))));
				$result['items'] = array(
					'rdf:Seq' => array(
						'rdf:li' => $items
					)
				);
			}			
			return $result;
		}
		return array();
	}
	
	//!-----------------------------------------------------------------
	// @function	Feed::renderItems
	// @desc		Este m�todo monta um vetor com todos os itens do canal,
	//				com suas propriedades e elementos formatados para fins de 
	//				gera��o do arquivo XML do feed (renderiza��o)
	// @access		public
	// @return		array Vetor com os dados dos itens do canal formatados
	//!-----------------------------------------------------------------
	function renderItems() {
		if (TypeUtils::isInstanceOf($this->Channel, 'feedchannel')) {
			$itemList = array();
			$itemElements = $this->getItemElementNames();
			$items = $this->Channel->getChildren();
			foreach ($items as $item) {
				$itemData = array();
				reset($itemElements);				
				foreach ($itemElements as $element) {
					// busca o valor da propriedade
					$value = $item->getElement($element);
					if (!$value)
						continue;
					// elementos de data/timestamp
					if (in_array($element, array('pubDate', 'created', 'issued', 'published', 'modified', 'updated'))) {
						$itemData[$element] = htmlspecialchars(parent::buildDate($value, $this->type, $this->version));
					} 
					// enclosure: elemento com atributos e sem nodos filhos
					elseif ($element == 'enclosure') {
						$itemData[$element] = array('_attrs' => $this->_formatElementValue($value));
					}
					// link no formato ATOM: elemento com atributos, pode ser m�ltiplo
					elseif ($element == 'link' && $this->isATOM()) {
						if (TypeUtils::isArray($value)) {
							// conjunto de links
							if (!TypeUtils::isHashArray($value) && !empty($value)) {
								foreach ($value as $key=>$link) {
									if (TypeUtils::isArray($link))
										$value[$key] = array('_attrs' => $link);
									else
										$value[$key] = array('_attrs' => array('href' => htmlspecialchars($link)));
								}
								$itemData[$element] = $value;
							} 
							// link �nico com atributos
							else {
								$value = $this->_formatElementValue($value);
								$itemData[$element] = array('_attrs' => $value);
							}
						} 
						// link simples formato string: converter em elemento com atributo href
						else {
							$value = array('href' => htmlspecialchars($value));
							$itemData[$element] = array('_attrs' => $value);
						}						
					} 
					// outros elementos
					else {
						$itemData[$element] = $this->_formatElementValue($value);
					}
				}
				// atributos e elementos especiais
				if ($this->isRSS() && $this->version == '1.0')
					$itemData['_attrs'] = array('rdf:about' => htmlspecialchars($item->getElement('link', '')));
				if ($this->isRSS() && !in_array($this->version, array('0.9', '0.91')))
					$itemData['source'] = array('_attrs' => array('url' => htmlspecialchars($this->syndicationURL)), '_cdata' => htmlspecialchars($this->Channel->getElement('title', '')));
				if ($this->isRSS() && $this->version == '2.0') {
					if (isset($itemData['guid']))
						$itemData['guid'] = array('_attrs' => array('isPermaLink' => 'true'), '_cdata' => $itemData['guid']);
				}
				$itemList[] = $itemData;
			}
			if ($this->isRSS())
				return array('item' => $itemList);
			else
				return array('entry' => $itemList);
		}
		return array();
	}
	
	//!-----------------------------------------------------------------
	// @function	Feed::_formatElementValue
	// @desc		Formata o valor de um elemento, sendo ele simples, composto,
	//				m�ltiplo ou composto e m�ltiplo
	// @access		private
	// @param		value mixed		Valor do elemento
	// @return		mixed Valor(es) do elemento formatados para inclus�o no 
	//				arquivo XML (usando a fun��o htmlspecialchars)
	//!-----------------------------------------------------------------
	function _formatElementValue($value) {
		// elementos compostos ou m�ltiplos (ex: image, textinput, author, contributor)
		if (TypeUtils::isArray($value)) {
			foreach ($value as $k=>$v) {
				// elementos m�ltiplos e compostos (ex: contributor)
				if (TypeUtils::isArray($value[$k])) {
					foreach ($value[$k] as $_k => $_v)
						$value[$k][$_k] = htmlspecialchars($_v);
				} else {
					$value[$k] = htmlspecialchars($v);
				}
			}
		} else {
			$value = htmlspecialchars($value);
		}
		return $value;	
	}
}
?>