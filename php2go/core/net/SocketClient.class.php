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
//				conex�o dos mesmos a m�quinas remotas atrav�s de diversos
//				protocolos. Um socket � um ponto de comunica��o entre
//				duas m�quinas
// @package		php2go.net
// @uses		StringUtils
// @extends		PHP2Go
// @author		Marcos Pont
// @version		$Revision: 1.13 $
//!-----------------------------------------------------------------
class SocketClient extends PHP2Go
{
	var $stream;			// @var stream resource		Ponteiro de arquivo para o socket
	var $host;				// @var host string			Endere�o do host remoto
	var $port;				// @var port int			Porta da conex�o via socket
	var $blocking;			// @var blocking bool		Indica se a conex�o atrav�s do socket � bloqueante
	var $timeout;			// @var timeout int			Timeout do socket
	var $persistent;		// @var persistent bool		Indica se a conex�o deve ser persistente
	var $bufferSize;		// @var bufferSize int		Tamanho do buffer de leitura
	var $lineEnd;			// @var lineEnd string		Caractere de final de linha utilizado no socket para escrita
	var $errorMsg;			// @var errorMsg string		Armazena mensagens de erro retornadas pelo socket
	
	//!-----------------------------------------------------------------
	// @function	SocketClient::SocketClient
	// @desc		Construtor da classe, inicializa os par�metros de configura��o
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
	// @desc		Busca o endere�o do host remoto
	// @access		public
	// @return		string Endere�o ou IP do host
	// @note		Se n�o houver conex�o ativa, retorna uma string vazia
	//!-----------------------------------------------------------------
	function getRemoteHost() {
		return $this->host;
	}
	
	//!-----------------------------------------------------------------
	// @function	SocketClient::getRemotePort
	// @desc		Busca a porta remota da conex�o atual
	// @access		public
	// @return		int N�mero da porta
	//!-----------------------------------------------------------------
	function getRemotePort() {
		return $this->port;
	}
	
	//!-----------------------------------------------------------------
	// @function	SocketClient::isBlocking
	// @desc		Verifica se a classe est� configurada para criar conex�es bloqueantes
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
	// @desc		Verifica se as conex�es para este objeto s�o persistentes
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function isPersistent() {
		return $this->persistent;
	}
	
	//!-----------------------------------------------------------------
	// @function	SocketClient::getLastError
	// @desc		Busca a �ltima mensagem de erro gerada
	// @access		public
	// @return		array Vetor contendo c�digo e mensagem de erro ou FALSE se nenhum erro foi capturado
	//!-----------------------------------------------------------------
	function getLastError() {
		return (!empty($this->errorMsg)) ? $this->errorMsg : FALSE;
	}
	
	//!-----------------------------------------------------------------
	// @function	SocketClient::getStatus
	// @desc		Consulta o status do socket ativo
	// @access		public
	// @return		array Vetor com dados de status ou FALSE se a conex�o n�o
	//				estiver ativa ou n�o for poss�vel buscar o status
	// @note		O vetor retornado contendo o status do socket � constitu�do
	//				de quatro posi��es: timed_out bool, blocked bool,
	//				eof bool e unread_bytes int. 
	// @note		A partir da vers�o 4.3.0 do PHP, inclui quatro novas 
	//				informa��es: stream_type, wrapper_type, wrapper_data e
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
	// @desc		Verifica se a conex�o atrav�s do socket est� ativa
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
	// @desc		Verifica se houve timeout na conex�o
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
	// @desc		Modifica o socket aberto para realizar opera��es
	//				bloqueantes ou n�o bloqueantes, dependendo do valor
	//				de $setting
	// @access		public
	// @param		setting bool	Se TRUE, as opera��es do socket ser�o bloqueantes
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
	// @desc		Configura o timeout da conex�o atrav�s do socket
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
	// @desc		Configura o padr�o de final de linha do socket atual
	// @access		public
	// @param		lineEnd string	Padr�o para o final de linha: \n, \r\n, \r
	// @return		void	
	//!-----------------------------------------------------------------
	function setLineEnd($lineEnd) {
		$this->lineEnd = $lineEnd;
	}
	
	//!-----------------------------------------------------------------
	// @function	SocketClient::connect
	// @desc		Cria um socket para o host $host, na porta $port
	// @access		public
	// @param		host string		"" Endere�o do host remoto
	// @param		port int			"0" Porta para conex�o
	// @param		persistent bool	"FALSE" Indica se a conex�o deve ser persistente
	// @param		timeout float		"NULL" Timeout da conex�o, em segundos
	// @return		bool
	// @note		Os par�metros $persistent e $timeout permitem a cria��o de
	//				sockets persistentes e definir o timeout da conex�o
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
	// @desc		L� uma quantidade $size de bytes do socket ativo
	// @access		public
	// @param		size int		"1" N�mero de bytes a serem lidos
	// @return		string Conte�do lido ou FALSE em caso de erros
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
	// @desc		L� um caractere atrav�s do socket
	// @access		public
	// @return		string Valor do byte lido em ASCII
	// @note		Retorna FALSE se o socket n�o estiver conectado	
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
	// @desc		L� uma palavra atrav�s do socket
	// @access		public
	// @return		string Valor da palavra em ASCII
	// @note		Retorna FALSE se o socket n�o estiver conectado	
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
	// @desc		L� um n�mero inteiro atrav�s do socket
	// @access		public
	// @return		int Valor do n�mero lido
	// @note		Retorna FALSE se o socket n�o estiver conectado
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
	// @desc		L� uma cadeia de caracteres atrav�s do socket
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
	// @desc		L� uma linha de dados no socket
	// @access		public
	// @return		string Linha lida ou FALSE em caso de erros
	// @note		Este m�todo efetua leituras ao socket, de tamanho
	//				$bufferSize (propriedade da classe), at� que seja encontrado
	//				uma marca de fim de linha. Retorna a linha sem a marca de
	//				final
	// @note		Retorna FALSE se encontrar final de arquivo ou o socket
	//				n�o estiver ativo
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
	// @desc		L� todo o conte�do dispon�vel no socket para um buffer dentro
	//				do m�todo
	// @access		public
	// @return		string Conte�do lido no buffer ou FALSE em caso de erros
	// @note		A unidade de leitura � medida pela propriedade $bufferSize
	//				da classe SocketClient. O padr�o � 2048 bytes, valor que
	//				pode ser alterado pelo m�todo setBufferSize
	//!-----------------------------------------------------------------
	function readAllContents() {
		if ($this->isConnected()) {
			// monta um buffer com todo o conte�do dispon�vel no socket
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
	// @param		str string		Conte�do a ser escrito
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
	// @param		line string		Conte�do da linha
	// @return		bool
	// @note		A linha deve ser passada ao m�todo sem o caractere que determina seu final
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
	// @desc		Fecha a conex�o atrav�s do socket
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
	// @desc		Limpa as informa��es de erro da classe
	// @access		public
	// @return		void	
	//!-----------------------------------------------------------------
	function resetError() {
		unset($this->errorMsg);
	}
	
	//!-----------------------------------------------------------------
	// @function	SocketClient::_checkHostAddress
	// @desc		Verifica a validade do endere�o fornecido como host da conex�o
	// @access		private
	// @param		host string	Host fornecido como par�metro para a conex�o
	// @return		mixed Valor v�lido para o host ou FALSE se ele for inv�lido
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