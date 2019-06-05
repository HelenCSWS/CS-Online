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
// $Header: /www/cvsroot/php2go/core/auth/Auth.class.php,v 1.18 2005/07/25 13:16:46 mpont Exp $
// $Date: 2005/07/25 13:16:46 $

//------------------------------------------------------------------
import('php2go.auth.User');
import('php2go.net.HttpRequest');
import('php2go.text.StringUtils');
import('php2go.util.Callback');
//------------------------------------------------------------------

// @const AUTH_DEFAULT_LOGIN_FIELD "username"
// Nome padr�o para a vari�vel POST do nome de usu�rio
define('AUTH_DEFAULT_LOGIN_FIELD', 'username');
// @const AUTH_DEFAULT_PASSWORD_FIELD "password"
// Nome padr�o para a vari�vel POST que carrega a senha do usu�rio
define('AUTH_DEFAULT_PASSWORD_FIELD', 'password');
// @const AUTH_DEFAULT_EXPIRY_TIME
// Tempo padr�o, em segundos, para a expira��o da sess�o
define('AUTH_DEFAULT_EXPIRY_TIME', 600);
// @const AUTH_DEFAULT_IDLE_TIME
// Tempo padr�o, em segundos, de ociosidade da sess�o
define('AUTH_DEFAULT_IDLE_TIME', 60);

//!-----------------------------------------------------------------
// @class		Auth
// @desc		Classe base para implementa��o de opera��es de autentica��o
//				de usu�rios e cria��o de sess�o com persist�ncia de dados, controle
//				de expira��o e ociosidade
// @package		php2go.auth
// @uses		HttpRequest
// @uses		SessionObject
// @uses		StringUtils
// @uses		TypeUtils
// @extends		PHP2Go
// @author		Marcos Pont
// @version		$Revision: 1.18 $
// @note		A classe Auth � abstrata e n�o deve ser instanciada diretamente.
//				Devem ser criadas inst�ncias das classes que definem diferentes 
//				tipos de autentica��o
//!-----------------------------------------------------------------
class Auth extends PHP2Go
{
	var $loginFunction;				// @var loginFunction Callback object			Fun��o ou m�todo respons�vel por exibir a tela de login ou redirecionar para o script que constroi o mesmo
	var $loginCallback;				// @var loginCallback Callback object			Fun��o ou m�todo a ser executado quando o login � efetuado com sucesso
	var $errorCallback;				// @var errorCallback Callback object			Fun��o ou m�todo a ser executado quando o login falha
	var $logoutCallback;			// @var logoutCallback Callback object			Fun��o ou m�todo a ser executado quando o logout � efetuado
	var $expiryCallback;			// @var expiryCallback Callback object			Fun��o ou m�todo para tratar expira��o da sess�o
	var $idlenessCallback;			// @var idlenessCallback Callback object		Fun��o ou m�todo que trata uma sess�o que excede o tempo de ociosidade
	var $validSessionCallback;		// @var validSessionCallback Callback object	Fun��o ou m�todo executado quando existe uma sess�o v�lida, j� persistida na sess�o
	var $loginFieldName;			// @var loginFieldName string					Nome da vari�vel que cont�m o nome de usu�rio
	var $passwordFieldName;			// @var passwordFieldName string				Nome da vari�vel que cont�m a senha
	var $sessionKeyName;			// @var sessionKeyName string					Nome para a vari�vel de sess�o que deve ser criada
	var $expiryTime;				// @var expiryTime int							Tempo de expira��o da sess�o, em segundos
	var $idleTime;					// @var idleTime int							Tempo que a sess�o pode permanecer ociosa, em segundos
	var $User = NULL;				// @var User User object						Inst�ncia da classe User (ou de uma subclasse) utilizada para persistir dados do usu�rio na sess�o
	var $_login;					// @var login string							Nome de usu�rio para autentica��o
	var $_password;					// @var password string							Senha para autentica��o	

	//!-----------------------------------------------------------------
	// @function	Auth::Auth
	// @desc		Construtor da classe
	// @access		public
	//!-----------------------------------------------------------------
	function Auth() {
		PHP2Go::PHP2Go();
		if ($this->isA('auth', FALSE))
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_ABSTRACT_CLASS', 'Auth'), E_USER_ERROR, __FILE__, __LINE__);
		$this->loginFieldName = AUTH_DEFAULT_LOGIN_FIELD;
		$this->passwordFieldName = AUTH_DEFAULT_PASSWORD_FIELD;
		$this->expiryTime = TypeUtils::ifFalse(PHP2Go::getConfigVal('AUTH.EXPIRY_TIME', FALSE), AUTH_DEFAULT_EXPIRY_TIME);
		$this->idleTime = TypeUtils::ifFalse(PHP2Go::getConfigVal('AUTH.IDLE_TIME', FALSE), AUTH_DEFAULT_IDLE_TIME);
		$this->User =& User::getInstance();
        
      
	}
	
	//!-----------------------------------------------------------------
	// @function	Auth::&getInstance
	// @desc		Constr�i/retorna o singleton da classe de autentica��o, 
	//				utilizando a classe de autentica��o definida na configura��o, 
	//				ou php2go.auth.AuthDb por padr�o
	// @access		public
	// @return		Auth object
	//!-----------------------------------------------------------------
	function &getInstance() {
		static $instance;
		if (!isset($instance)) {
			// busca o autenticador customizado definido na configura��o
			if ($authClassPath = PHP2Go::getConfigVal('AUTH.AUTHENTICATOR_PATH', FALSE, FALSE)) {
			
				if ($authClass = classForPath($authClassPath)) {
				    
               
					$instance = new $authClass();
                   
					if (!TypeUtils::isInstanceOf($instance, 'auth'))
						PHP2Go::raiseError(PHP2Go::getLangVal('ERR_INVALID_AUTHENTICATOR', $authClass), E_USER_ERROR, __FILE__, __LINE__);
				} else {
					PHP2Go::raiseError(PHP2Go::getLangVal('ERR_INVALID_AUTHENTICATOR_PATH', $authClassPath), E_USER_ERROR, __FILE__, __LINE__);
				}
			} 
			// usa o autenticador padr�o - php2go.auth.AuthDb
			else {				
				$instance = new AuthDb();
			}
		}
		return $instance;
	}
	
	//!-----------------------------------------------------------------
	// @function	Auth::&getSession
	// @desc		Retorna uma inst�ncia da classe User representando o usu�rio do sistema
	// @access		public
	// @return		User object Usu�rio do sistema
	// @deprecated	Utilize o m�todo Auth::getCurrentUser
	//!-----------------------------------------------------------------
	function &getSession() {
		return $this->User;
	}
	
	//!-----------------------------------------------------------------
	// @function	Auth::&getCurrentUser
	// @desc		Retorna uma inst�ncia da classe User representando o usu�rio do sistema
	// @access		public
	// @return		User object Usu�rio do sistema
	//!-----------------------------------------------------------------
	function &getCurrentUser() {
		return $this->User;
	}
	
	//!-----------------------------------------------------------------
	// @function	Auth::getActiveUser
	// @desc		Retorna o nome do usu�rio ativo
	// @access		public
	// @return		string Nome do usu�rio
	// @deprecated	Utilize Auth::getCurrentUsername
	//!-----------------------------------------------------------------
	function getActiveUser() {

		return $this->User->getUsername();
	}
	
	//!-----------------------------------------------------------------
	// @function	Auth::getCurrentUsername
	// @desc		Retorna o nome do usu�rio ativo
	// @access		public
	// @return		string Nome do usu�rio
	//!-----------------------------------------------------------------
	function getCurrentUsername() {
	  
		return $this->User->getUsername();
	}
	
	//!-----------------------------------------------------------------
	// @function	Auth::getElapsedTime
	// @desc		Busca o n�mero de segundos desde o in�cio da sess�o
	// @access		public
	// @return		int Tempo da sess�o, em segundos
	//!-----------------------------------------------------------------
	function getElapsedTime() {
		return $this->User->getElapsedTime();
	}
	
	//!-----------------------------------------------------------------
	// @function	Auth::isValid
	// @desc		Verifica se existe um usu�rio autenticado no presente momento
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function isValid() {
		return $this->User->isAuthenticated();
	}
	
	//!-----------------------------------------------------------------
	// @function	Auth::getExpiryTime
	// @desc		Retorna o tempo de expira��o configurado na classe
	// @access		public
	// @return		int Tempo de expira��o, em segundos
	//!-----------------------------------------------------------------
	function getExpiryTime() {
		return $this->expiryTime;
	}
	
	//!-----------------------------------------------------------------
	// @function	Auth::setExpiryTime
	// @desc		Configura o tempo de expira��o da sess�o
	// @access		public
	// @param		seconds int	Tempo de expira��o, em segundos
	// @note		Utilize $seconds == 0 para desabilitar o controle de expira��o
	// @return		void	
	//!-----------------------------------------------------------------
	function setExpiryTime($seconds) {
		$this->expiryTime = TypeUtils::parseIntegerPositive($seconds);
	}
	
	//!-----------------------------------------------------------------
	// @function	Auth::isExpired
	// @desc		Verifica se o tempo m�ximo de persist�ncia da sess�o foi excedido
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function isExpired() {
		if ($this->expiryTime > 0 && $this->User->isAuthenticated()) {
			$elapsedTime = $this->User->getElapsedTime();
			if ($elapsedTime >= $this->expiryTime)
				return TRUE;			
		}
		return FALSE;
	}
	
	//!-----------------------------------------------------------------
	// @function	Auth::getIdleTime
	// @desc		Retorna o tempo m�ximo de ociosidade permitido
	// @access		public
	// @return		int Tempo de ociosidade
	//!-----------------------------------------------------------------
	function getIdleTime() {
		return $this->idleTime;
	}
	
	//!-----------------------------------------------------------------
	// @function	Auth::setIdleTime
	// @desc		Define o tempo m�ximo de ociosidade da sess�o
	// @access		public
	// @param		seconds int	Tempo de ociosidade, em segundos
	// @return		void	
	// @note		Utilize $seconds == 0 para desabilitar o controle de ociosidade
	//!-----------------------------------------------------------------
	function setIdleTime($seconds) {
		$this->idleTime = TypeUtils::parseIntegerPositive($seconds);
	}
	
	//!-----------------------------------------------------------------
	// @function	Auth::isIdled
	// @desc		Verifica se o tempo m�ximo de ociosidade foi excedido
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function isIdled() {
		if ($this->idleTime > 0 && $this->User->isAuthenticated()) {
			$lastIdleTime = $this->User->getLastIdleTime();
			if ($lastIdleTime >= $this->idleTime)
				return TRUE;
		}
		return FALSE;
	}
	
	//!-----------------------------------------------------------------
	// @function	Auth::setLoginFieldName
	// @desc		Define o nome da vari�vel que cont�m o nome de usu�rio
	// @access		public
	// @param		loginFieldName string		Nome da vari�vel
	// @return		void	
	// @note		A classe define como padr�o a vari�vel 'username', que � buscada
	//				no vetor $_POST para a execu��o do login
	//!-----------------------------------------------------------------	
	function setLoginFieldName($loginFieldName) {
		if (trim($loginFieldName) != '')
			$this->loginFieldName = trim($loginFieldName);
	}
	
	//!-----------------------------------------------------------------
	// @function	Auth::setPasswordFieldName
	// @desc		Define o nome da vari�vel que cont�m o senha do usu�rio
	// @access		public
	// @param		passwordFieldName string	Nome da vari�vel
	// @return		void	
	// @note		O nome padr�o para esta vari�vel � 'password'. Ela � buscada
	//				no vetor $_POST para a execu��o do login
	//!-----------------------------------------------------------------	
	function setPasswordFieldName($passwordFieldName) {
		if (trim($passwordFieldName) != '')
			$this->passwordFieldName = $passwordFieldName;
	}
	
	//!-----------------------------------------------------------------
	// @function	Auth::setSessionKeyName
	// @desc		Seta o nome da vari�vel de sess�o que � criada
	// @access		public
	// @param		keyName string	Nome para a vari�vel
	// @return		void	
	// @deprecated	Utilize a vari�vel de configura��o USER[SESSION_NAME]
	//!-----------------------------------------------------------------
	function setSessionKeyName($keyName) {
		if (trim($keyName) != '')
			$this->sessionKeyName = trim($keyName);
	}
	
	//!-----------------------------------------------------------------
	// @function	Auth::setLoginFunction
	// @desc		Define a fun��o ou m�todo que ser� respons�vel por gerar o formul�rio
	//				de autentica��o de usu�rios
	// @access		public
	// @param		loginFunction mixed		Nome da fun��o ou vetor contendo objeto/m�todo
	// @return		void
	// @note		Se este tratador de evento for executado ap�s o encerramento de uma sess�o
	//				por expira��o ou inativa��o, ele receber� como par�metro o usu�rio que estava logado.
	//				Em caso contr�rio (sess�o inexistente), n�o ser� enviado nenhum par�metro
	//!-----------------------------------------------------------------
	function setLoginFunction($loginFunction) {
		$this->loginFunction =& new Callback($loginFunction);
	}
	
	//!-----------------------------------------------------------------
	// @function	Auth::setLoginCallback
	// @desc		Define a fun��o ou m�todo que ser� chamada ap�s o login ter sido efetuado com sucesso
	// @access		public
	// @param		loginCallback mixed		Nome da fun��o ou vetor contendo objeto/m�todo
	// @note		Esta fun��o receber� como par�metro uma inst�ncia da classe User 
	//				representando o usu�rio que acaba de autenticar-se
	// @return		void	
	//!-----------------------------------------------------------------
	function setLoginCallback($loginCallback) {
		$this->loginCallback =& new Callback($loginCallback);
	}
	
	//!-----------------------------------------------------------------
	// @function	Auth::setErrorCallback
	// @desc		Define a fun��o ou m�todo que ir� tratar a falha no login
	// @access		public
	// @param		errorCallback mixed		Nome da fun��o ou vetor contendo objeto/m�todo
	// @note		Esta fun��o receber� como par�metro uma inst�ncia da classe User 
	//				representando o usu�rio que realizou a tentativa de login
	// @return		void	
	//!-----------------------------------------------------------------
	function setErrorCallback($errorCallback) {
		$this->errorCallback =& new Callback($errorCallback);
	}
	
	//!-----------------------------------------------------------------
	// @function	Auth::setLogoutCallback
	// @desc		Define a fun��o ou m�todo que ser� chamado ap�s o logout
	// @access		public
	// @param		logoutCallback mixed	Nome da fun��o ou vetor contendo objeto/m�todo
	// @note		Esta fun��o receber� como par�metro uma inst�ncia da classe User 
	//				representando a sess�o de usu�rio que foi destru�da
	// @return		void	
	//!-----------------------------------------------------------------
	function setLogoutCallback($logoutCallback) {
		$this->logoutCallback =& new Callback($logoutCallback);
	}
	
	//!-----------------------------------------------------------------
	// @function	Auth::setLogoutCallback
	// @desc		Define uma fun��o ou m�todo para tratar a expira��o da sess�o
	// @access		public
	// @param		expiryCallback mixed	Nome da fun��o ou vetor contendo objeto/m�todo
	// @note		Esta fun��o receber� como par�metro uma inst�ncia da classe User 
	//				representando a sess�o de usu�rio expirada
	// @return		void	
	//!-----------------------------------------------------------------
	function setExpiryCallback($expiryCallback) {
		$this->expiryCallback =& new Callback($expiryCallback);
	}
	
	//!-----------------------------------------------------------------
	// @function	Auth::setIdlenessCallback
	// @desc		Define uma fun��o ou m�todo para tratar tempo ocioso excedido
	// @access		public
	// @param		idlenessCallback mixed	Nome da fun��o ou vetor contendo objeto/m�todo
	// @note		Esta fun��o receber� como par�metro uma inst�ncia da classe User 
	//				representando a sess�o de usu�rio inativa
	// @return		void	
	//!-----------------------------------------------------------------
	function setIdlenessCallback($idlenessCallback) {
		$this->idlenessCallback =& new Callback($idlenessCallback);
	}
	
	//!-----------------------------------------------------------------
	// @function	Auth::setValidSessionCallback
	// @desc		Define uma fun��o para tratar a exist�ncia de uma sess�o v�lida. Pode ser utilizada
	//				para atualizar as propriedades do usu�rio logado a cada requisi��o realizada com sess�o v�lida
	// @access		public
	// @param		validSessionCallback mixed	Nome da fun��o ou vetor contendo objeto/m�todo
	// @note		Esta fun��o receber� como par�metro uma inst�ncia da classe User representando o usu�rio logado
	// @return		void	
	//!-----------------------------------------------------------------
	function setValidSessionCallback($validSessionCallback) {
		$this->validSessionCallback =& new Callback($validSessionCallback);
	}
	
	//!-----------------------------------------------------------------
	// @function	Auth::init
	// @desc		Inicializa as opera��es de autentica��o
	// @access		public
	// @return		void
	// @note		Este m�todo pode executar as seguintes opera��es:<BR><BR>
	//				- se a sess�o n�o for v�lida, ir� executar a fun��o do usu�rio para constru��o do login<BR>
	//				- se existirem par�metros de autentica��o, ir� tentar a autentica��o, chamando as callbacks de sucesso ou falha conforme o resultado<BR>
	//				- se existir uma sess�o v�lida por�m com tempo expirado, ir� executar a callback de expira��o ou reconstruir o login (loginFunction)<BR>
	//				- se existir uma sess�o v�lida por�m ociosa, ir� executar a callback de ociosidade ou reconstruir o login (loginFunction)<BR>
	//				- do contr�rio, a sess�o � v�lida e o tempo de ociosidade � zerado
	//!-----------------------------------------------------------------
	function init() {
		$this->_fetchAuthVars();
		// sess�o inv�lida
		if (!$this->isValid()) {
			// se existem as vari�veis de autentica��o
			if (isset($this->_login) && isset($this->_password))
				$this->login();
			// chamada da fun��o do usu�rio para construir o form de autentica��o
			elseif (isset($this->loginFunction))
				$this->loginFunction->invoke();
		}
		// sess�o v�lida, por�m expirada
		elseif ($this->isExpired()) {
			$this->_handleExpiredSession();
		}
		// sess�o v�lida, por�m inativa por muito tempo entre 2 requisi��es
		elseif ($this->isIdled()) {
			$this->_handleIdleSession();
		}
		// sess�o v�lida, n�o inativa e n�o expirada
		elseif (isset($this->validSessionCallback)) {
			$this->validSessionCallback->invokeByRef($this->User);
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	Auth::login
	// @desc		M�todo de autentica��o. Verifica a autentica��o do usu�rio
	//				se os par�metros login e password forem encontrados
	// @access		protected
	// @note		Se a sess�o estiver ativa, ser� encerrada
	// @return		void	
	//!-----------------------------------------------------------------	
	function login() {
		// executa o m�todo de autentica��o, implementado nas classes filhas
		$result = $this->authenticate();
		// o login falhou
		if ($result === FALSE) {
			if (isset($this->errorCallback)) {
				$user = $this->User;
				$user->logout();
				$user->setUsername($this->_login);
				$this->errorCallback->invoke($user);
			}
		}
		// o login teve sucesso
		else {
			$this->User->authenticate($this->_login, (TypeUtils::isHashArray($result) ? $result : array()));
			if (isset($this->loginCallback))
				$this->loginCallback->invokeByRef($this->User);
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	Auth::logout
	// @desc		Encerra a sess�o atual, se ela existir
	// @access		protected
	// @param		rebuildLogin bool	"FALSE" Indica se a op��o de novo login deve ser oferecida
	// @return		void
	//!-----------------------------------------------------------------
	function logout($rebuildLogin=FALSE) {
		// efetua logout somente se existe uma sess�o v�lida
		$lastUser = $this->getCurrentUser();
		$lastUser->unregister();
		if ($this->User->isAuthenticated())
			$this->User->logout();
		if (isset($this->logoutCallback))
			$this->logoutCallback->invoke($lastUser);
		if ($rebuildLogin && isset($this->loginFunction))
			$this->loginFunction->invoke($lastUser);
	}

	//!-----------------------------------------------------------------
	// @function	Auth::authenticate
	// @desc		Este m�todo deve ser implementado nas classes filhas
	// @access		protected
	// @return		void
	//!-----------------------------------------------------------------
	function authenticate() {
		return TRUE;
	}
	
	//!-----------------------------------------------------------------
	// @function	Auth::_fetchAuthVars
	// @desc		Busca do vetor de vari�veis POST os dados de autentica��o
	// @access		private
	// @return		void	
	//!-----------------------------------------------------------------
	function _fetchAuthVars() {
		$login = HttpRequest::post($this->loginFieldName);
		if ($login)
			$this->_login = $login;
		$password = HttpRequest::post($this->passwordFieldName);
		if ($password)
			$this->_password = $password;
	}
	
	//!-----------------------------------------------------------------
	// @function	Auth::_handleExpiredSession
	// @desc		Trata uma sess�o cujo tempo de persist�ncia expirou
	// @access		private
	// @return		void
	// @note		Encerra e chama a callback de expira��o, se existir. 
	//				Do contr�rio, encerra e reconstr�i o login	
	//!-----------------------------------------------------------------
	function _handleExpiredSession() {
		if (isset($this->expiryCallback)) {
			$lastUser = $this->getCurrentUser();
			$lastUser->registered = FALSE;
			$this->User->logout();
			$this->expiryCallback->invoke($lastUser);
		} else {
			$this->logout(TRUE);
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	Auth::_handleIdleSession
	// @desc		Trata uma sess�o cujo tempo de ociosidade foi excedido
	// @access		private
	// @return		void
	// @note		Encerra e chama a callback de ociosidade, se existir. 
	//				Do contr�rio, encerra e reconstr�i o login
	//!-----------------------------------------------------------------
	function _handleIdleSession() {
		if (isset($this->idlenessCallback)) {
			$lastUser = $this->getCurrentUser();
			$lastUser->registered = FALSE;
			$this->User->logout();
			$this->idlenessCallback->invoke($lastUser);
		} else {
			$this->logout(TRUE);
		}
	}
}
?>