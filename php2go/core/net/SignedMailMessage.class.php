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
// $Header: /www/cvsroot/php2go/core/net/SignedMailMessage.class.php,v 1.7 2005/01/21 17:20:02 mpont Exp $
// $Date: 2005/01/21 17:20:02 $

//------------------------------------------------------------------
import('php2go.net.MailMessage');
import('php2go.text.StringUtils');
//------------------------------------------------------------------

//!-----------------------------------------------------------------
// @class		SignedMailMessage
// @desc		Esta classe extende a funcionalidade da classe MailMessage
//				permitindo o envio de mensagens assinadas utilizando o software
//				GnuPG
// @package		php2go.net
// @uses		StringUtils
// @extends		MailMessage
// @author		Marcos Pont
// @version		$Revision: 1.7 $
// @note		Inicialmente, esta classe tem funcionamento restrito a ambientes Unix/Linux
//!-----------------------------------------------------------------
class SignedMailMessage extends MailMessage
{
	var $keyName;				// @var keyName string				Nome do propriet�rio da chave utilizada para a assinatura
	var $keyPath;				// @var keyPath string				Caminho absoluto para os pares de chaves no servidor
	var $gnuPgPath;				// @var gnuPgPath string			Caminho absoluto para o programa GnuPG no servidor
	
	// propriedades privadas
	var $commandString;			// @var commandString string		Linha de comando para assinatura da mensagem
	var $commandTemplate;		// @var commandTemplate string		Template inicial da linha de comando
	var $encryptedContent;		// @var encryptedContent string		Conte�do criptografado que retorna da opera��o de assinatura
	var $errorMsg;				// @var errorMsg string				Mensagens de erro capturadas na classe

	//!-----------------------------------------------------------------
	// @function	SignedMailMessage::SignedMailMessage
	// @desc		Construtor da classe
	// @access		public
	//!-----------------------------------------------------------------
	function SignedMailMessage() {
		// construtor da classe pai
		MailMessage::MailMessage();
		// o script deve estar rodando em ambiente Unix/Windows
		if (System::isWindows())
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CANT_RUN_ON_WINDOWS'), E_USER_ERROR, __FILE__, __LINE__);
		// caminho padr�o do GnuPG
		$this->gnuPgPath = '/usr/bin/gpg';
		// template do comando a ser executado no servidor
		$this->commandTemplate = "echo \"%s\" | \"%s\" 2>&1 --batch --no-secmem-warning --armor --sign -u \"%s\" --default-key \"%s\" ";
	}
	
	//!-----------------------------------------------------------------
	// @function	SignedMailMessage::setKeyName
	// @desc		Seta o nome do propriet�rio da chave a ser utilizada na assinatura da mensagem
	// @access		public
	// @param		keyName string	Nome do propriet�rio da chave
	// @return		void
	// @note		O par�metro deve ser fornecido no formato nome &lt;e-mail&gt;
	// @note		Se este m�todo n�o for executado, o remetente da mensagem ser� utilizado como padr�o
	//!-----------------------------------------------------------------	
	function setKeyName($keyName) {
		$this->keyName = $keyName;
	}
	
	//!-----------------------------------------------------------------
	// @function	SignedMailMessage::setKeyPath
	// @desc		Define o caminho onde os pares de chaves est�o armazenadas no servidor
	// @access		public
	// @param		keyPath string	Caminho das chaves no servidor
	// @return		void	
	//!-----------------------------------------------------------------
	function setKeyPath($keyPath) {
		$this->keyPath = $keyPath;
	}
	
	//!-----------------------------------------------------------------
	// @function	SignedMailMessage::setGnuPGPath
	// @desc		Define o caminho para o execut�vel do software GnuPG no servidor
	// @access		public
	// @param		path string		Caminho absoluto para o GnuPG
	// @return		void	
	// @note		A classe assume como padr�o o caminho "/usr/bin/gpg"
	//!-----------------------------------------------------------------
	function setGnuPGPath($path) {
		$this->gnuPgPath = $path;
	}
	
	//!-----------------------------------------------------------------
	// @function	SignedMailMessage::build
	// @desc		Sobrescreve o m�todo MailMessage::build para construir
	//				o conte�do assinado para a mensagem
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function build() {
		// constr�i a mensagem utilizando o m�todo da classe pai
		MailMessage::build();
		// verifica se uma chave foi adicionada, aproveitando o sender da mensagem como default
		if (!isset($this->keyName)) {
			$this->keyName = isset($this->fromName) ? $this->fromName . " <" . $this->from . ">" : $this->from;
		}
		// verifica se o caminho do arquivo que cont�m o par de chaves foi fornecido
		if (!isset($this->keyPath)) {
			$this->build = FALSE;
			return FALSE;
		} else {
			$this->_buildCommandString();
			if ($this->_signMessage())
				return TRUE;
			else {
				$this->built = FALSE;
				return FALSE;
			}
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	SignedMailMessage::_buildCommandString
	// @desc		Constr�i a linha de comando que assina a mensagem, inserindo
	//				todos os destinat�rios da mensagem
	// @access		private
	// @return		void	
	//!-----------------------------------------------------------------
	function _buildCommandString() {
		$this->commandString = sprintf($this->commandTemplate, str_replace("\n", "\r\n", $this->body), $this->gnuPgPath, $this->keyName, $this->keyName);
		if (MailMessage::hasRecipients(MAIL_RECIPIENT_TO))
			for ($i=0; $i<sizeOf($this->to); $i++)
				$this->commandString .= " -r \"" . MailMessage::formatAddress($this->to[$i]) . "\"";
		if (MailMessage::hasRecipients(MAIL_RECIPIENT_CC))
			for ($i=0; $i<sizeOf($this->cc); $i++)
				$this->commandString .= " -r \"" . MailMessage::formatAddress($this->cc[$i]) . "\"";
		if (MailMessage::hasRecipients(MAIL_RECIPIENT_BCC))
			for ($i=0; $i<sizeOf($this->bcc); $i++)
				$this->commandString .= " -r \"" . MailMessage::formatAddress($this->bcc[$i]) . "\"";	
	}
	
	//!-----------------------------------------------------------------
	// @function	SignedMailMessage::_signMessage
	// @desc		Assina o conte�do (corpo) da mensagem j� constru�do,
	//				baseado nos parametros j� fornecidos a classe (caminho
	//				para o GnuPG, caminho para as chaves)
	// @access		private
	// @return		bool
	//!-----------------------------------------------------------------
	function _signMessage() {
		$oldHome = @getenv('HOME');
		if (StringUtils::right($this->keyPath, 1) == '/') 
			$this->keyPath = substr($this->keyPath, 0, -1);
		echo htmlspecialchars($this->commandString);
		putenv("HOME=$this->keyPath");		
		exec($this->commandString, $this->encryptedContent, $errorCode);
		putenv("HOME=$oldHome");
		$message = implode("\r\n", $this->encryptedContent);
		if(ereg("-----BEGIN PGP MESSAGE-----.*-----END PGP MESSAGE-----",$message)) {
			$this->body = $this->lineEnd . $this->lineEnd . $message;
			return TRUE;
		} else {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_SIGNED_MESSAGE_SIGN', $message), E_USER_ERROR, __FILE__, __LINE__);
			return FALSE;
		}
	}
}
?>