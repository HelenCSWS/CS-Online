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
// $Header: /www/cvsroot/php2go/core/net/HttpResponse.class.php,v 1.18 2005/06/01 16:15:04 mpont Exp $
// $Date: 2005/06/01 16:15:04 $

//------------------------------------------------------------------
import('php2go.net.HttpRequest');
import('php2go.net.Url');
import('php2go.net.MimeType');
import('php2go.net.httpConstants', 'php');
//------------------------------------------------------------------

//!-----------------------------------------------------------------
// @class		HttpResponse
// @desc		Classe para manipula��o da resposta da requisi��o HTTP
// @package		php2go.net
// @extends		PHP2Go
// @uses		Environment
// @uses		HttpRequest
// @uses		MimeType
// @author		Marcos Pont
// @version		$Revision: 1.18 $
//!-----------------------------------------------------------------
class HttpResponse extends PHP2Go
{
	//!-----------------------------------------------------------------
	// @function	HttpResponse::&getInstance
	// @desc		Retorna uma inst�ncia �nica da classe
	// @access		public
	// @return		HttpResponse object Inst�ncia da classe
	// @static
	//!-----------------------------------------------------------------
	function &getInstance() {
		static $instance;
		if (!isset($instance)) {
			$instance = new HttpResponse;
		}
		return $instance;
	}
	
	//!-----------------------------------------------------------------
	// @function	HttpResponse::headersSent
	// @desc		Verifica se os headers da resposta � requisi��o HTTP j� foram enviados
	// @access		public
	// @return		bool
	// @static	
	//!-----------------------------------------------------------------
	function headersSent() {
		return headers_sent();
	}
	
	//!-----------------------------------------------------------------
	// @function	HttpResponse::addHeader
	// @desc		Adiciona um header ao conte�do retornado
	// @access		public
	// @param		name string			Nome do header
	// @param		value string		"" Valor do header
	// @param		replace bool		"TRUE" Substituir o valor atual do header, se existente
	// @return		void
	// @note		� conveniente testar se os headers j� foram enviados
	//				atrav�s de HttpResponse::headersSent antes de utilizar 
	//				este m�todo
	// @static	
	//!-----------------------------------------------------------------
	function addHeader($name, $value = '', $replace=TRUE) {
		if (!empty($value))
			@header("$name: $value", TypeUtils::toBoolean($replace));
		else
			@header("$name", TypeUtils::toBoolean($replace));
	}
	
	//!-----------------------------------------------------------------
	// @function	HttpResponse::addCookie
	// @desc		Adiciona um cookie � resposta HTTP do servidor
	// @access		public
	// @param		Cookie HttpCookie object	Cookie a ser adicionado
	// @return		void
	// @static
	//!-----------------------------------------------------------------
	function addCookie($Cookie) {
		if (TypeUtils::isInstanceOf($Cookie, 'httpcookie')) {		
			setcookie($Cookie->getName(), $Cookie->getValue(), 
					$Cookie->getExpiryDate(), $Cookie->getPath(),
					$Cookie->getDomain(), ($Cookie->isSecure() ? 1 : 0));
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	HttpResponse::redirect
	// @desc		Redireciona para outra URL utilizando header HTTP Location
	// @access		public
	// @param		location Url object		URL de redirecionamento
	// @return		void
	// @static
	//!-----------------------------------------------------------------
	function redirect($Location) {
		if (TypeUtils::isInstanceOf($Location, 'url')) {
			HttpResponse::setStatus(HTTP_STATUS_MOVED_PERMANENTLY);
			HttpResponse::addHeader('Location', $Location->getUrl());
			HttpResponse::addHeader('Connection', 'close');
		}		
	}
	
	//!-----------------------------------------------------------------
	// @function	HttpResponse::setStatus
	// @desc		Define o c�digo de status para a resposta HTTP do servidor.
	//				Este m�todo pode ser utilizado quando � necess�rio enviar
	//				um determinado c�digo de status para o agente que requisitou
	//				um determinado recurso do sistema via HTTP
	// @access		public
	// @param		code int			C�digo de status
	// @param		httpVersion string	"1.0" Vers�o do protocolo HTTP
	// @return		void
	// @static
	//!-----------------------------------------------------------------
	function setStatus($code, $httpVersion='1.0') {
		HttpResponse::addHeader("HTTP/$httpVersion $code");
	}
	
	//!-----------------------------------------------------------------
	// @function	HttpResponse::download
	// @desc		Envia os headers espec�ficos para download de um arquivo
	// @access		public
	// @param		fileName string		Nome do arquivo
	// @param		length int			"0" Tamanho do conte�do
	// @param		mimeType string		"" Permite definir um mime-type diferente do padr�o que � definido a partir do nome do arquivo
	// @param		contentDisp string	"" Permite definir um valor diferente do padr�o para o header Content-disposition
	// @return		void
	// @static	
	//!-----------------------------------------------------------------
	function download($fileName, $length=0, $mimeType='', $contentDisp='') {		
		$Response =& HttpResponse::getInstance();
		if (trim($mimeType) == '')
			$mimeType = MimeType::getFromFileName($fileName);
		if (trim($contentDisp) == '')
			$contentDisp = 'attachment';		
		$Response->addHeader('Content-Type', $mimeType);
		$Agent =& UserAgent::getInstance();
		if ($Agent->matchBrowser('ie')) {
			$Response->addHeader('Content-Disposition', "{$contentDisp}; filename=\"{$fileName}\"");
			if ($length)
				$Response->addHeader('Content-Length', $length);
			$Response->addHeader('Last-Modified', gmdate("D, d M Y H:i:s") . ' GMT');
			//$Response->addHeader('Expires', '0');  //Deleted By Helen Wang, solve the CGI failure when export an excel file by calling this fnct. Only happens on the new server. Feb 13th 2012
			$Response->addHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0');
			$Response->addHeader('Pragma', 'public');
		} else {
			$Response->addHeader('Content-Disposition', "{$contentDisp}; filename=\"{$fileName}\"");
			if ($length)
				$Response->addHeader('Content-Length', $length);
			$Response->addHeader('Last-Modified', gmdate("D, d M Y H:i:s") . ' GMT');				
			//$Response->addHeader('Expires', '0');
			$Response->addHeader('Pragma', 'no-cache');
		}
    }
}
?>