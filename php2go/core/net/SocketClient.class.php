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
// $Header: /www/cvsroot/php2go/core/net/SocketClient.class.php,v 1.13 2005/06/08 22:16:15 mpont Exp $
// $Date: 2005/06/08 22:16:15 $

//------------------------------------------------------------------
import('php2go.text.StringUtils');
//------------------------------------------------------------------

//!-----------------------------------------------------------------
// @class		SocketClient
// @desc		Esta classe implementa sockets TCP, permitindo a
//				conexão dos mesmos a máquinas remotas através de diversos
//				protocolos. Um socket é um ponto de comunicação entre
//				duas máquinas
// @package		php2go.net
// @uses		StringUtils
// @extends		PHP2Go
// @author		Marcos Pont
// @version		$Revision: 1.13 $
//!-----------------------------------------------------------------
class SocketClient extends PHP2Go
{
	var $stream;			// @var stream resource		Ponteiro de arquivo para o socket
	var $host;				// @var host string			Endereço do host remoto
	var $port;				// @var port int			Porta da conexão via socket
	var $blocking;			// @var blocking bool		Indica se a conexão através do socket é bloqueante
	var $timeout;			// @var timeout int			Timeout do socket
	var $persistent;		// @var persistent bool		Indica se a conexão deve ser persistente
	var $bufferSize;		// @var bufferSize int		Tamanho do buffer de leitura
	var $lineEnd;			// @var lineEnd string		Caractere de final de linha utilizado no socket para escrita
	var $errorMsg;			// @var errorMsg string		Armazena mensagens de erro retornadas pelo socket
	
	//!-----------------------------------------------------------------
	// @function	SocketClient::SocketClient
	// @desc		Construtor da classe, inicializa os parâmetros de configuração
	// @access		public
	//!-----------------------------------------------------------------
	function SocketClient() {
		PHP2Go::PHP2Go();
		$this->host = '';
		$this->port = 0;
		$this->blocking = TRUE;
		$this->timeout = FALSE;
		$this->persistent = FALSE;
		$this->bufferSize = 2048;
		$this->lineEnd = "\r\n";
		$this->errorNo = 0;
		$this->errorMsg = '';
		parent::registerDestructor($this, '_SocketClient');
	}
	
	//!-----------------------------------------------------------------
	// @function	SocketClient::_SocketClient
	// @desc		Destrutor da classe. Fecha o socket se estiver aberto
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function _SocketClient() {
		$this->close();
	}
	
	//!-----------------------------------------------------------------
	// @function	SocketClient::getRemoteHost
	// @desc		Busca o endereço do host remoto
	// @access		public
	// @return		string Endereço ou IP do host
	// @note		Se não houver conexão ativa, retorna uma string vazia
	//!-----------------------------------------------------------------
	function getRemoteHost() {
		return $this->host;
	}
	
	//!-----------------------------------------------------------------
	// @function	SocketClient::getRemotePort
	// @desc		Busca a porta remota da conexão atual
	// @access		public
	// @return		int Número da porta
	//!-----------------------------------------------------------------
	function getRemotePort() {
		return $this->port;
	}
	
	//!-----------------------------------------------------------------
	// @function	SocketClient::isBlocking
	// @desc		Verifica se a classe está configurada para criar conexões bloqueantes
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function isBlocking() {
		return $this->blocking;
	}
	
	//!-----------------------------------------------------------------
	// @function	SocketClient::getTimeout
	// @desc		Consulta o timeout definido na classe
	// @access		public
	// @return		int Timeout, em segundos
	//!-----------------------------------------------------------------
	function getTimeout() {
		return $this->timeout;
	}
	
	//!-----------------------------------------------------------------
	// @function	SocketClient::isPersistent
	// @desc		Verifica se as conexões para este objeto são persistentes
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function isPersistent() {
		return $this->persistent;
	}
	
	//!-----------------------------------------------------------------
	// @function	SocketClient::getLastError
	// @desc		Busca a última mensagem de erro gerada
	// @access		public
	// @return		array Vetor contendo código e mensagem de erro ou FALSE se nenhum erro foi capturado
	//!-----------------------------------------------------------------
	function getLastError() {
		return (!empty($this->errorMsg)) ? $this->errorMsg : FALSE;
	}
	
	//!-----------------------------------------------------------------
	// @function	SocketClient::getStatus
	// @desc		Consulta o status do socket ativo
	// @access		public
	// @return		array Vetor com dados de status ou FALSE se a conexão não
	//				estiver ativa ou não for possível buscar o status
	// @note		O vetor retornado contendo o status do socket é constituído
	//				de quatro posições: timed_out bool, blocked bool,
	//				eof bool e unread_bytes int. 
	// @note		A partir da versão 4.3.0 do PHP, inclui quatro novas 
	//				informações: stream_type, wrapper_type, wrapper_data e
	//				filters array
	//!-----------------------------------------------------------------
	function getStatus() {
		if ($this->isConnected()) {
			$status = @socket_get_status($this->stream);
			if (TypeUtils::isArray($status)) {
				return $status;
			}
		}
		return FALSE;
	}
	
	//!-----------------------------------------------------------------
	// @function	SocketClient::isConnected
	// @desc		Verifica se a conexão através do socket está ativa
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function isConnected() {
		if (!isset($this->stream) || !TypeUtils::isResource($this->stream)) {
			return FALSE;
		} else {
			return TRUE;
		}
	}

	//!-----------------------------------------------------------------
	// @function	SocketClient::isTimedOut
	// @desc		Verifica se houve timeout na conexão
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function isTimedOut() {
		if ($this->isConnected()) {
			if ($status = $this->getStatus()) {
				return $status['timed_out'];
			}
		}
		return FALSE;
	}
	
	//!-----------------------------------------------------------------
	// @function	SocketClient::setBlocking
	// @desc		Modifica o socket aberto para realizar operações
	//				bloqueantes ou não bloqueantes, dependendo do valor
	//				de $setting
	// @access		public
	// @param		setting bool	Se TRUE, as operações do socket serão bloqueantes
	// @return		bool
	//!-----------------------------------------------------------------
	function setBlocking($setting) {
		if ($this->isConnected()) {
			return @socket_set_blocking($this->stream, TypeUtils::toBoolean($setting));
		} else {
			return FALSE;
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	SocketClient::setTimeout
	// @desc		Configura o timeout da conexão através do socket
	// @access		public
	// @param		timeout float		Timeout a ser aplicado no socket
	// @return		bool
	//!-----------------------------------------------------------------
	function setTimeout($timeout) {
		if ($this->isConnected()) {
			$seconds = TypeUtils::parseInteger($timeout);
			$microseconds = $timeout % $seconds;
			return @socket_set_timeout($this->stream, $seconds, $microseconds);
		} else {
			return FALSE;
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	SocketClient::setBufferSize
	// @desc		Seta um novo valor para o tamanho do buffer de leitura
	// @access		public
	// @param		bufferSize int	Novo tamanho para o buffer
	// @return		void	
	//!-----------------------------------------------------------------
	function setBufferSize($bufferSize) {
		$this->bufferSize = TypeUtils::parseIntegerPositive($bufferSize);
	}
	
	//!-----------------------------------------------------------------
	// @function	SocketClient::setLineEnd
	// @desc		Configura o padrão de final de linha do socket atual
	// @access		public
	// @param		lineEnd string	Padrão para o final de linha: \n, \r\n, \r
	// @return		void	
	//!-----------------------------------------------------------------
	function setLineEnd($lineEnd) {
		$this->lineEnd = $lineEnd;
	}
	
	//!-----------------------------------------------------------------
	// @function	SocketClient::connect
	// @desc		Cria um socket para o host $host, na porta $port
	// @access		public
	// @param		host string		"" Endereço do host remoto
	// @param		port int			"0" Porta para conexão
	// @param		persistent bool	"FALSE" Indica se a conexão deve ser persistente
	// @param		timeout float		"NULL" Timeout da conexão, em segundos
	// @return		bool
	// @note		Os parâmetros $persistent e $timeout permitem a criação de
	//				sockets persistentes e definir o timeout da conexão
	//!-----------------------------------------------------------------
	function connect($host = '', $port = 0, $persistent = FALSE, $timeout = NULL) {
		if (!$this->host = $this->_checkHostAddress($host)) {	
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_HOST_INVALID', $host), E_USER_ERROR, __FILE__, __LINE__);
			return FALSE;
		}
		$this->port = TypeUtils::parseIntegerPositive($port % 65536);
		$this->persistent = TypeUtils::toBoolean($persistent);
		$this->timeout = TypeUtils::ifNull($timeout, FALSE);
		if ($this->isConnected()) {
			$this->close();
		}		
		$openFunc = $this->persistent ? 'pfsockopen' : 'fsockopen';
		if (is_numeric($this->timeout)) {
			$this->stream = @$openFunc($this->host, $this->port, $errNo, $errMsg, $this->timeout);
		} else {
			$this->stream = @$openFunc($this->host, $this->port, $errNo, $errMsg);
		}
		if (!$this->stream) {
			$this->errorMsg = PHP2Go::getLangVal('ERR_CANT_OPEN_SOCKET', array($this->port, $this->host, $errNo, $errMsg));
			PHP2Go::raiseError($this->errorMsg, E_USER_ERROR, __FILE__, __LINE__);
			return FALSE;
		} else {
			@socket_set_blocking($this->stream, $this->blocking);
			return TRUE;
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	SocketClient::read
	// @desc		Lê uma quantidade $size de bytes do socket ativo
	// @access		public
	// @param		size int		"1" Número de bytes a serem lidos
	// @return		string Conteúdo lido ou FALSE em caso de erros
	//!-----------------------------------------------------------------
	function read($size=1) {
		if ($this->isConnected()) {			
			if ($content = @fread($this->stream, $size)) {
				return $content;
			} else if ($this->isTimedOut()) {
				$this->errorMsg = PHP2Go::getLangVal('ERR_SOCKET_TIMEOUT');	
			}
			return FALSE;
		} else {			
			$this->errorMsg = PHP2Go::getLangVal('ERR_SOCKET_NOT_CONNECTED');
			return FALSE;
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	SocketClient::readChar
	// @desc		Lê um caractere através do socket
	// @access		public
	// @return		string Valor do byte lido em ASCII
	// @note		Retorna FALSE se o socket não estiver conectado	
	//!-----------------------------------------------------------------
	function readChar() {
		if ($buffer = $this->read()) {
			return ord($buffer);
		} else {
			$this->errorMsg = PHP2Go::getLangVal('ERR_SOCKET_NOT_CONNECTED');
			return FALSE;
		}
	}

	//!-----------------------------------------------------------------
	// @function	SocketClient::readWord
	// @desc		Lê uma palavra através do socket
	// @access		public
	// @return		string Valor da palavra em ASCII
	// @note		Retorna FALSE se o socket não estiver conectado	
	//!-----------------------------------------------------------------
	function readWord() {
		if ($buffer = $this->read(2)) {
			return (ord($buffer[0]) + (ord($buffer[1]) << 8));
		} else {
			$this->errorMsg = PHP2Go::getLangVal('ERR_SOCKET_NOT_CONNECTED');
			return FALSE;
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	SocketClient::readInteger
	// @desc		Lê um número inteiro através do socket
	// @access		public
	// @return		int Valor do número lido
	// @note		Retorna FALSE se o socket não estiver conectado
	//!-----------------------------------------------------------------
	function readInteger() {
		if ($buffer = $this->read(4)) {
			return (ord($buffer[0]) + (ord($buffer[1]) << 8) + (ord($buffer[2]) << 16) + (ord($buffer[3]) << 24));
		} else {
			$this->errorMsg = PHP2Go::getLangVal('ERR_SOCKET_NOT_CONNECTED');
			return FALSE;
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	SocketClient::readString
	// @desc		Lê uma cadeia de caracteres através do socket
	// @access		public
	// @return		string String lida ou FALSE em caso de erros
	//!-----------------------------------------------------------------
	function readString() {
		if ($this->isConnected()) {
			$string = '';
			while (!$this->eof() && ($char = $this->read()) != "\x00") {
				$string .= $char;
			}
			return $string;		
		} else {
			$this->errorMsg = PHP2Go::getLangVal('ERR_SOCKET_NOT_CONNECTED');
			return FALSE;
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	SocketClient::readLine
	// @desc		Lê uma linha de dados no socket
	// @access		public
	// @return		string Linha lida ou FALSE em caso de erros
	// @note		Este método efetua leituras ao socket, de tamanho
	//				$bufferSize (propriedade da classe), até que seja encontrado
	//				uma marca de fim de linha. Retorna a linha sem a marca de
	//				final
	// @note		Retorna FALSE se encontrar final de arquivo ou o socket
	//				não estiver ativo
	//!-----------------------------------------------------------------
	function readLine() {
		if ($this->isConnected()) {
			$line = '';
			$timeout = time() + $this->timeout;
			while (!$this->eof() && (!$this->timeout || time() < $timeout)) {
				$line .= @fgets($this->stream, $this->bufferSize);
				if (strlen($line) >= 2 && (StringUtils::right($line, 2) == "\r\n" || StringUtils::right($line, 1) == "\n")) {
					return $line;
				}
			}
			return $line;
		} else {
			$this->errorMsg = PHP2Go::getLangVal('ERR_SOCKET_NOT_CONNECTED');
			return FALSE;
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	SocketClient::readAllContents
	// @desc		Lê todo o conteúdo disponível no socket para um buffer dentro
	//				do método
	// @access		public
	// @return		string Conteúdo lido no buffer ou FALSE em caso de erros
	// @note		A unidade de leitura é medida pela propriedade $bufferSize
	//				da classe SocketClient. O padrão é 2048 bytes, valor que
	//				pode ser alterado pelo método setBufferSize
	//!-----------------------------------------------------------------
	function readAllContents() {
		if ($this->isConnected()) {
			// monta um buffer com todo o conteúdo disponível no socket
			$buffer = '';
			while (!$this->eof()) {
				$buffer .= $this->read($this->bufferSize);
			}
			return $buffer;
		} else {
			$this->errorMsg = PHP2Go::getLangVal('ERR_SOCKET_NOT_CONNECTED');
			return FALSE;
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	SocketClient::write
	// @desc		Escreve uma string no socket ativo
	// @access		public
	// @param		str string		Conteúdo a ser escrito
	// @return		bool
	//!-----------------------------------------------------------------
	function write($str) {
		if ($this->isConnected()) {			
			if (@fwrite($this->stream, $str, strlen($str))) {
				return TRUE;
			} else if ($this->isTimedOut()) {
				$this->errorMsg = PHP2Go::getLangVal('ERR_SOCKET_TIMEOUT');				
			}
			return FALSE;
		} else {
			$this->errorMsg = PHP2Go::getLangVal('ERR_SOCKET_NOT_CONNECTED');
			return FALSE;
		}	
	}
	
	//!-----------------------------------------------------------------
	// @function	SocketClient::writeLine
	// @desc		Escreve uma linha, incluindo caractere de final, no socket ativo
	// @access		public
	// @param		line string		Conteúdo da linha
	// @return		bool
	// @note		A linha deve ser passada ao método sem o caractere que determina seu final
	//!-----------------------------------------------------------------
	function writeLine($line) {
		return $this->write($line . $this->lineEnd);
	}
	
	//!-----------------------------------------------------------------
	// @function	SocketClient::eof
	// @desc		Testa se o fim de arquivo foi encontrado em um descritor de socket
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function eof() {
		return ($this->isConnected() && @feof($this->stream));
	}
	
	//!-----------------------------------------------------------------
	// @function	SocketClient::close
	// @desc		Fecha a conexão através do socket
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function close() {
		if ($this->isConnected()) {
			@fclose($this->stream);
			unset($this->stream);
			return TRUE;
		} else {
			return FALSE;
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	SocketClient::resetError
	// @desc		Limpa as informações de erro da classe
	// @access		public
	// @return		void	
	//!-----------------------------------------------------------------
	function resetError() {
		unset($this->errorMsg);
	}
	
	//!-----------------------------------------------------------------
	// @function	SocketClient::_checkHostAddress
	// @desc		Verifica a validade do endereço fornecido como host da conexão
	// @access		private
	// @param		host string	Host fornecido como parâmetro para a conexão
	// @return		mixed Valor válido para o host ou FALSE se ele for inválido
	//!-----------------------------------------------------------------
	function _checkHostAddress($host) {
		if (ereg("[a-zA-Z]+", $host)) {
			return gethostbyname($host);
		} else if (strspn($host, '0123456789.') == strlen($host)) {
			return $host;
		} else {
			return FALSE;
		}		
	}
}
?>