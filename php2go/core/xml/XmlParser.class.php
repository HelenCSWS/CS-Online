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
// $Header: /www/cvsroot/php2go/core/xml/XmlParser.class.php,v 1.16 2005/08/09 17:49:23 mpont Exp $
// $Date: 2005/08/09 17:49:23 $

//------------------------------------------------------------------
import('php2go.file.FileSystem');
import('php2go.xml.XmlNode');
//------------------------------------------------------------------

//!-----------------------------------------------------------------
// @class		XmlParser
// @desc		Esta classe � uma camada de abstra��o sobre o parser
//				EXPAT nativo do PHP, que constr�i uma �rvore de objetos 
//				XmlNode representando o conte�do do arquivo e insere esta
//				em um objeco XmlDocument
// @package		php2go.xml
// @extends 	PHP2Go
// @uses 		XmlNode
// @uses		FileSystem
// @author 		Marcos Pont
// @version		$Revision: 1.16 $
// @note		Exemplo de uso:
//				<PRE>
//
//				$Doc =& new XmlDocument();
//				$Doc->parseXml('file.xml');
//				$Root =& $Doc->getRoot();
//				if ($Root->hasChildren()) {
//				&nbsp;&nbsp;&nbsp;$count = $Root->getChildrenCount();
//				&nbsp;&nbsp;&nbsp;for ($i=0; $i<$count; $i++) {
//				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$Child = $Root->getChild($i);
//				&nbsp;&nbsp;&nbsp;}
//				}
//
//				</PRE>
//!-----------------------------------------------------------------
class XmlParser extends PHP2Go
{
	var $parser; 					// @var parser resource						Objeto xml_parser criado
	var $data; 						// @var data string							Dados XML a serem interpretados pelo parser
	var $vals; 						// @var vals array							Vetor de valores capturados do arquivo XML
	var $includedFiles = array();	// @var includedFiles array					"array()" Vetor de arquivos j� inclu�dos - entidades externas	
	var $Document = NULL;			// @var Document XmlDocument object			"NULL" Documento onde o conte�do parseado � inserido
	
	//!-----------------------------------------------------------------
	// @function	XmlParser::XmlParser
	// @desc		Construtor da classe
	// @access		public
	// @param		&Document XmlDocument object	Documento onde os nodos criados s�o inseridos
	//!-----------------------------------------------------------------
	function XmlParser(&$Document) {
		PHP2Go::PHP2Go();
		// verifica a exist�ncia da xml extension
		if (!function_exists('xml_parser_create'))
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_UNSUPPORTED_EXTENSION', 'xml'), E_USER_ERROR, __FILE__, __LINE__);
		$this->Document =& $Document;
	}
	
	//!-----------------------------------------------------------------
	// @function	XmlParser::createParser
	// @desc		M�todo est�tico para a cria��o de um parser XML utilizando
	//				um determinado tipo de codifica��o para o c�digo XML
	// @access		public
	// @param		srcEncoding string 	"NULL" Codifica��o a ser utilizada pelo parser
	// @param		&xmlSource string	C�digo XML
	// @param		optionFlags array	Conjunto de op��es para o parser
	// @return		resource Parser criado
	// @static
	//!-----------------------------------------------------------------
	function createParser($srcEncoding=NULL, &$xmlSource, $optionFlags=array()) {
		$validEncodings = array('iso-8859-1', 'us-ascii', 'utf-8');		
		if (!TypeUtils::isNull($srcEncoding)) {
			// codifica��o definida explicitamente
			$parser = xml_parser_create($srcEncoding);		
		} elseif (System::isPHP5()) {
			// php5 - possui auto detec��o de source encoding
			$parser = xml_parser_create('');		
		} else {
			// php4 - buscar codifica��o dos atributos do root XML
			if (preg_match('/<?xml.*encoding=[\'"](.*?)[\'"].*?>/m', $xmlSource, $matches))
				$srcEncoding = strtolower($matches[1]);
			else
				$srcEncoding = 'UTF-8';
			// se for uma das codifica��es v�lidas para a xml entension
			if (in_array(strtolower($srcEncoding), $validEncodings)) {
				$parser = xml_parser_create($srcEncoding);
			}
			// convers�o usando iconv
			elseif (function_exists('iconv')) {
				$xmlSource = iconv($srcEncoding, 'UTF-8', $xmlSource);
				$parser = xml_parser_create('UTF-8');
			}
			// convers�o usando mbstring
			elseif (function_exists('mb_convert_encoding')) {
				$xmlSource = mb_convert_encoding($xmlSource, 'UTF-8', $srcEncoding);
				$parser = xml_parser_create('UTF-8');
			} 
			// n�o foi poss�vel converter a partir do srcEncoding fornecido
			else {
				$parser = xml_parser_create();
			}
		}
		// seta as op��es
		foreach ($optionFlags as $code => $value)
			xml_parser_set_option($parser, $code, $value);
		return $parser;
	}

	//!-----------------------------------------------------------------
	// @function	XmlParser::parse
	// @desc		Interpreta o conte�do de um arquivo ou string XML
	// @access		public
	// @param		xmlContent string		Caminho completo do arquivo ou string XML
	// @param		srcType int				"T_BYFILE" T_BYFILE para arquivos e T_BYVAR para vari�veis
	// @param		srcEncoding string		"NULL" Codifica��o a ser utilizada na interpreta��o dos dados
	// @param		trgEncoding string		"NULL" Codifica��o a ser utilizada na montagem da �rvore XML
	// @return		bool
	//!-----------------------------------------------------------------
	function parse($xmlContent, $srcType=T_BYFILE, $srcEncoding=NULL, $trgEncoding=NULL) {
		if (TypeUtils::isNull($srcEncoding))
			$srcEncoding = PHP2Go::getConfigVal('CHARSET');
		if (TypeUtils::isNull($trgEncoding))
			$trgEncoding = PHP2Go::getConfigVal('CHARSET');
		if ($srcType == T_BYFILE) {
			$this->includedFiles[] = $xmlContent;
			$this->data = FileSystem::getContents($xmlContent);
		} else {
			$this->data = $xmlContent;
		}
		$this->data = eregi_replace(">[[:space:]]+<", "><", $this->data);
		return ($this->_parseExternalEntities($this->data) && $this->_parseXmlString($srcEncoding, $trgEncoding));
	}
	
	//!-----------------------------------------------------------------
	// @function	XmlParser::_parseExternalEntities
	// @desc		Procura por defini��es de entidades externas no cabe�alho
	//				do documento, inserindo o conte�do das mesmas no arquivo
	//				principal
	// @access		private
	// @param		&data string		Dados a serem processados
	// @return		void
	// @note		Este m�todo � recursivo para defini��es de novas entidades
	//				nos arquivos inclu�dos. O encontro de uma refer�ncia circular
	//				(arquivo j� utilizado) ir� parar o processamento
	//!-----------------------------------------------------------------
	function _parseExternalEntities(&$data) {
		if (preg_match_all('/<!ENTITY[ ](%[ ])?([a-zA-Z0-9_]+)[ ]SYSTEM[ ]\"([^\"]+)\"/', $data, $matches, PREG_SET_ORDER)) {
			$sizeMatches = sizeOf($matches);
			for ($i=0; $i<$sizeMatches; $i++) {
				if (!in_array($matches[$i][3], $this->includedFiles)) {
					if ($fileData = FileSystem::getContents($matches[$i][3])) {
						$this->_parseExternalEntities($fileData);
						$data = ereg_replace("(&|%){$matches[$i][2]};", $fileData, $data);
					} else {
						return FALSE;
					}
				} else
					$data = eregi_replace("(&|%){$matches[$i][2]};", "", $data);
			}			
		}
		return TRUE;
	}	

	//!-----------------------------------------------------------------
	// @function	XmlParser::_parseXmlString
	// @desc 		Inicializa o parser XML, setando suas op��es de
	// 				configura��o e executa a fun��o de interpreta��o
	// 				do parser, armazenando os resultados em uma estrutura
	// 				de �rvore
	// @access 		private
	// @param		srcEncoding string		Codifica��o para o parser
	// @param		trgEncoding string		Codifica��o para a fun��o de montagem da �rvore (xml_parse_into_struct)
	// @return		void	
	//!-----------------------------------------------------------------
	function _parseXmlString($srcEncoding, $trgEncoding) {
		$i = 0;		
		$this->parser = XmlParser::createParser(
			$srcEncoding, $this->data,
			array(
				XML_OPTION_TARGET_ENCODING => $trgEncoding,
				XML_OPTION_SKIP_WHITE => 1
			)
		);
		if (!@xml_parse_into_struct($this->parser, $this->data, &$this->vals, &$this->index)) {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_XML_PARSE', array(xml_error_string(xml_get_error_code($this->parser)), xml_get_current_line_number($this->parser), xml_get_current_column_number($this->parser))), E_USER_ERROR, __FILE__, __LINE__);
			return FALSE;
		}
		xml_parser_free($this->parser);
		$this->Document->DocumentElement =& new XmlNode($this->vals[$i]['tag'],
			(isset($this->vals[$i]['attributes'])) ? $this->vals[$i]['attributes'] : NULL,
				$this->_getChildren($this->vals, $i),
					(isset($this->vals[$i]['value'])) ? $this->vals[$i]['value'] : NULL);
		return TRUE;
	}

	//!-----------------------------------------------------------------
	// @function	XmlParser::_getChildren
	// @desc 		Fun��o recursiva para a montagem da �rvore XML
	// @access 		private
	// @param 		vals array	Vetor de valores do arquivo
	// @param 		&i int		�ndice atual do vetor de valores
	// @return		array Vetor de filhos de um determinado nodo
	// @see 		XmlParser::_getRoot
	//!-----------------------------------------------------------------
	function _getChildren($vals, &$i) {
		$children = array();
		while (++$i < sizeof($vals)) {
			switch ($vals[$i]['type']) {
				case 'cdata': 
					array_push($children, new XmlNode('#cdata_section', NULL, NULL, $vals[$i]['value']));
					break;
				case 'complete': 
					array_push($children, new XmlNode($vals[$i]['tag'], (isset($vals[$i]['attributes']) ? $vals[$i]['attributes'] : NULL), NULL, (isset($vals[$i]['value']) ? $vals[$i]['value'] : NULL)));
					break;
				case 'open': 
					array_push($children, new XmlNode($vals[$i]['tag'], (isset($vals[$i]['attributes']) ? $vals[$i]['attributes'] : NULL), $this->_getChildren($vals, $i), (isset($vals[$i]['value']) ? $vals[$i]['value'] : NULL)));
					break;
				case 'close': 
					return $children;
			}
		}
	}	
}
?>