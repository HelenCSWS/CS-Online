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
// Nome padrão para a variável POST do nome de usuário
define('AUTH_DEFAULT_LOGIN_FIELD', 'username');
// @const AUTH_DEFAULT_PASSWORD_FIELD "password"
// Nome padrão para a variável POST que carrega a senha do usuário
define('AUTH_DEFAULT_PASSWORD_FIELD', 'password');
// @const AUTH_DEFAULT_EXPIRY_TIME
// Tempo padrão, em segundos, para a expiração da sessão
define('AUTH_DEFAULT_EXPIRY_TIME', 600);
// @const AUTH_DEFAULT_IDLE_TIME
// Tempo padrão, em segundos, de ociosidade da sessão
define('AUTH_DEFAULT_IDLE_TIME', 60);

//!-----------------------------------------------------------------
// @class		Auth
// @desc		Classe base para implementação de operações de autenticação
//				de usuários e criação de sessão com persistência de dados, controle
//				de expiração e ociosidade
// @package		php2go.auth
// @uses		HttpRequest
// @uses		SessionObject
// @uses		StringUtils
// @uses		TypeUtils
// @extends		PHP2Go
// @author		Marcos Pont
// @version		$Revision: 1.18 $
// @note		A classe Auth é abstrata e não deve ser instanciada diretamente.
//				Devem ser criadas instâncias das classes que definem diferentes 
//				tipos de autenticação
//!-----------------------------------------------------------------
class Auth extends PHP2Go
{
	var $loginFunction;				// @var loginFunction Callback object			Função ou método responsável por exibir a tela de login ou redirecionar para o script que constroi o mesmo
	var $loginCallback;				// @var loginCallback Callback object			Função ou método a ser executado quando o login é efetuado com sucesso
	var $errorCallback;				// @var errorCallback Callback object			Função ou método a ser executado quando o login falha
	var $logoutCallback;			// @var logoutCallback Callback object			Função ou método a ser executado quando o logout é efetuado
	var $expiryCallback;			// @var expiryCallback Callback object			Função ou método para tratar expiração da sessão
	var $idlenessCallback;			// @var idlenessCallback Callback object		Função ou método que trata uma sessão que excede o tempo de ociosidade
	var $validSessionCallback;		// @var validSessionCallback Callback object	Função ou método executado quando existe uma sessão válida, já persistida na sessão
	var $loginFieldName;			// @var loginFieldName string					Nome da variável que contém o nome de usuário
	var $passwordFieldName;			// @var passwordFieldName string				Nome da variável que contém a senha
	var $sessionKeyName;			// @var sessionKeyName string					Nome para a variável de sessão que deve ser criada
	var $expiryTime;				// @var expiryTime int							Tempo de expiração da sessão, em segundos
	var $idleTime;					// @var idleTime int							Tempo que a sessão pode permanecer ociosa, em segundos
	var $User = NULL;				// @var User User object						Instância da classe User (ou de uma subclasse) utilizada para persistir dados do usuário na sessão
	var $_login;					// @var login string							Nome de usuário para autenticação
	var $_password;					// @var password string							Senha para autenticação	

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
	// @desc		Constrói/retorna o singleton da classe de autenticação, 
	//				utilizando a classe de autenticação definida na configuração, 
	//				ou php2go.auth.AuthDb por padrão
	// @access		public
	// @return		Auth object
	//!-----------------------------------------------------------------
	function &getInstance() {
		static $instance;
		if (!isset($instance)) {
			// busca o autenticador customizado definido na configuração
			if ($authClassPath = PHP2Go::getConfigVal('AUTH.AUTHENTICATOR_PATH', FALSE, FALSE)) {
			
				if ($authClass = classForPath($authClassPath)) {
				    
               
					$instance = new $authClass();
                   
					if (!TypeUtils::isInstanceOf($instance, 'auth'))
						PHP2Go::raiseError(PHP2Go::getLangVal('ERR_INVALID_AUTHENTICATOR', $authClass), E_USER_ERROR, __FILE__, __LINE__);
				} else {
					PHP2Go::raiseError(PHP2Go::getLangVal('ERR_INVALID_AUTHENTICATOR_PATH', $authClassPath), E_USER_ERROR, __FILE__, __LINE__);
				}
			} 
			// usa o autenticador padrão - php2go.auth.AuthDb
			else {				
				$instance = new AuthDb();
			}
		}
		return $instance;
	}
	
	//!-----------------------------------------------------------------
	// @function	Auth::&getSession
	// @desc		Retorna uma instância da classe User representando o usuário do sistema
	// @access		public
	// @return		User object Usuário do sistema
	// @deprecated	Utilize o método Auth::getCurrentUser
	//!-----------------------------------------------------------------
	function &getSession() {
		return $this->User;
	}
	
	//!-----------------------------------------------------------------
	// @function	Auth::&getCurrentUser
	// @desc		Retorna uma instância da classe User representando o usuário do sistema
	// @access		public
	// @return		User object Usuário do sistema
	//!-----------------------------------------------------------------
	function &getCurrentUser() {
		return $this->User;
	}
	
	//!-----------------------------------------------------------------
	// @function	Auth::getActiveUser
	// @desc		Retorna o nome do usuário ativo
	// @access		public
	// @return		string Nome do usuário
	// @deprecated	Utilize Auth::getCurrentUsername
	//!-----------------------------------------------------------------
	function getActiveUser() {

		return $this->User->getUsername();
	}
	
	//!-----------------------------------------------------------------
	// @function	Auth::getCurrentUsername
	// @desc		Retorna o nome do usuário ativo
	// @access		public
	// @return		string Nome do usuário
	//!-----------------------------------------------------------------
	function getCurrentUsername() {
	  
		return $this->User->getUsername();
	}
	
	//!-----------------------------------------------------------------
	// @function	Auth::getElapsedTime
	// @desc		Busca o número de segundos desde o início da sessão
	// @access		public
	// @return		int Tempo da sessão, em segundos
	//!-----------------------------------------------------------------
	function getElapsedTime() {
		return $this->User->getElapsedTime();
	}
	
	//!-----------------------------------------------------------------
	// @function	Auth::isValid
	// @desc		Verifica se existe um usuário autenticado no presente momento
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function isValid() {
		return $this->User->isAuthenticated();
	}
	
	//!-----------------------------------------------------------------
	// @function	Auth::getExpiryTime
	// @desc		Retorna o tempo de expiração configurado na classe
	// @access		public
	// @return		int Tempo de expiração, em segundos
	//!-----------------------------------------------------------------
	function getExpiryTime() {
		return $this->expiryTime;
	}
	
	//!-----------------------------------------------------------------
	// @function	Auth::setExpiryTime
	// @desc		Configura o tempo de expiração da sessão
	// @access		public
	// @param		seconds int	Tempo de expiração, em segundos
	// @note		Utilize $seconds == 0 para desabilitar o controle de expiração
	// @return		void	
	//!-----------------------------------------------------------------
	function setExpiryTime($seconds) {
		$this->expiryTime = TypeUtils::parseIntegerPositive($seconds);
	}
	
	//!-----------------------------------------------------------------
	// @function	Auth::isExpired
	// @desc		Verifica se o tempo máximo de persistência da sessão foi excedido
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
	// @desc		Retorna o tempo máximo de ociosidade permitido
	// @access		public
	// @return		int Tempo de ociosidade
	//!-----------------------------------------------------------------
	function getIdleTime() {
		return $this->idleTime;
	}
	
	//!-----------------------------------------------------------------
	// @function	Auth::setIdleTime
	// @desc		Define o tempo máximo de ociosidade da sessão
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
	// @desc		Verifica se o tempo máximo de ociosidade foi excedido
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
	// @desc		Define o nome da variável que contém o nome de usuário
	// @access		public
	// @param		loginFieldName string		Nome da variável
	// @return		void	
	// @note		A classe define como padrão a variável 'username', que é buscada
	//				no vetor $_POST para a execução do login
	//!-----------------------------------------------------------------	
	function setLoginFieldName($loginFieldName) {
		if (trim($loginFieldName) != '')
			$this->loginFieldName = trim($loginFieldName);
	}
	
	//!-----------------------------------------------------------------
	// @function	Auth::setPasswordFieldName
	// @desc		Define o nome da variável que contém o senha do usuário
	// @access		public
	// @param		passwordFieldName string	Nome da variável
	// @return		void	
	// @note		O nome padrão para esta variável é 'password'. Ela é buscada
	//				no vetor $_POST para a execução do login
	//!-----------------------------------------------------------------	
	function setPasswordFieldName($passwordFieldName) {
		if (trim($passwordFieldName) != '')
			$this->passwordFieldName = $passwordFieldName;
	}
	
	//!-----------------------------------------------------------------
	// @function	Auth::setSessionKeyName
	// @desc		Seta o nome da variável de sessão que é criada
	// @access		public
	// @param		keyName string	Nome para a variável
	// @return		void	
	// @deprecated	Utilize a variável de configuração USER[SESSION_NAME]
	//!-----------------------------------------------------------------
	function setSessionKeyName($keyName) {
		if (trim($keyName) != '')
			$this->sessionKeyName = trim($keyName);
	}
	
	//!-----------------------------------------------------------------
	// @function	Auth::setLoginFunction
	// @desc		Define a função ou método que será responsável por gerar o formulário
	//				de autenticação de usuários
	// @access		public
	// @param		loginFunction mixed		Nome da função ou vetor contendo objeto/método
	// @return		void
	// @note		Se este tratador de evento for executado após o encerramento de uma sessão
	//				por expiração ou inativação, ele receberá como parâmetro o usuário que estava logado.
	//				Em caso contrário (sessão inexistente), não será enviado nenhum parâmetro
	//!-----------------------------------------------------------------
	function setLoginFunction($loginFunction) {
		$this->loginFunction =& new Callback($loginFunction);
	}
	
	//!-----------------------------------------------------------------
	// @function	Auth::setLoginCallback
	// @desc		Define a função ou método que será chamada após o login ter sido efetuado com sucesso
	// @access		public
	// @param		loginCallback mixed		Nome da função ou vetor contendo objeto/método
	// @note		Esta função receberá como parâmetro uma instância da classe User 
	//				representando o usuário que acaba de autenticar-se
	// @return		void	
	//!-----------------------------------------------------------------
	function setLoginCallback($loginCallback) {
		$this->loginCallback =& new Callback($loginCallback);
	}
	
	//!-----------------------------------------------------------------
	// @function	Auth::setErrorCallback
	// @desc		Define a função ou método que irá tratar a falha no login
	// @access		public
	// @param		errorCallback mixed		Nome da função ou vetor contendo objeto/método
	// @note		Esta função receberá como parâmetro uma instância da classe User 
	//				representando o usuário que realizou a tentativa de login
	// @return		void	
	//!-----------------------------------------------------------------
	function setErrorCallback($errorCallback) {
		$this->errorCallback =& new Callback($errorCallback);
	}
	
	//!-----------------------------------------------------------------
	// @function	Auth::setLogoutCallback
	// @desc		Define a função ou método que será chamado após o logout
	// @access		public
	// @param		logoutCallback mixed	Nome da função ou vetor contendo objeto/método
	// @note		Esta função receberá como parâmetro uma instância da classe User 
	//				representando a sessão de usuário que foi destruída
	// @return		void	
	//!-----------------------------------------------------------------
	function setLogoutCallback($logoutCallback) {
		$this->logoutCallback =& new Callback($logoutCallback);
	}
	
	//!-----------------------------------------------------------------
	// @function	Auth::setLogoutCallback
	// @desc		Define uma função ou método para tratar a expiração da sessão
	// @access		public
	// @param		expiryCallback mixed	Nome da função ou vetor contendo objeto/método
	// @note		Esta função receberá como parâmetro uma instância da classe User 
	//				representando a sessão de usuário expirada
	// @return		void	
	//!-----------------------------------------------------------------
	function setExpiryCallback($expiryCallback) {
		$this->expiryCallback =& new Callback($expiryCallback);
	}
	
	//!-----------------------------------------------------------------
	// @function	Auth::setIdlenessCallback
	// @desc		Define uma função ou método para tratar tempo ocioso excedido
	// @access		public
	// @param		idlenessCallback mixed	Nome da função ou vetor contendo objeto/método
	// @note		Esta função receberá como parâmetro uma instância da classe User 
	//				representando a sessão de usuário inativa
	// @return		void	
	//!-----------------------------------------------------------------
	function setIdlenessCallback($idlenessCallback) {
		$this->idlenessCallback =& new Callback($idlenessCallback);
	}
	
	//!-----------------------------------------------------------------
	// @function	Auth::setValidSessionCallback
	// @desc		Define uma função para tratar a existência de uma sessão válida. Pode ser utilizada
	//				para atualizar as propriedades do usuário logado a cada requisição realizada com sessão válida
	// @access		public
	// @param		validSessionCallback mixed	Nome da função ou vetor contendo objeto/método
	// @note		Esta função receberá como parâmetro uma instância da classe User representando o usuário logado
	// @return		void	
	//!-----------------------------------------------------------------
	function setValidSessionCallback($validSessionCallback) {
		$this->validSessionCallback =& new Callback($validSessionCallback);
	}
	
	//!-----------------------------------------------------------------
	// @function	Auth::init
	// @desc		Inicializa as operações de autenticação
	// @access		public
	// @return		void
	// @note		Este método pode executar as seguintes operações:<BR><BR>
	//				- se a sessão não for válida, irá executar a função do usuário para construção do login<BR>
	//				- se existirem parâmetros de autenticação, irá tentar a autenticação, chamando as callbacks de sucesso ou falha conforme o resultado<BR>
	//				- se existir uma sessão válida porém com tempo expirado, irá executar a callback de expiração ou reconstruir o login (loginFunction)<BR>
	//				- se existir uma sessão válida porém ociosa, irá executar a callback de ociosidade ou reconstruir o login (loginFunction)<BR>
	//				- do contrário, a sessão é válida e o tempo de ociosidade é zerado
	//!-----------------------------------------------------------------
	function init() {
		$this->_fetchAuthVars();
		// sessão inválida
		if (!$this->isValid()) {
			// se existem as variáveis de autenticação
			if (isset($this->_login) && isset($this->_password))
				$this->login();
			// chamada da função do usuário para construir o form de autenticação
			elseif (isset($this->loginFunction))
				$this->loginFunction->invoke();
		}
		// sessão válida, porém expirada
		elseif ($this->isExpired()) {
			$this->_handleExpiredSession();
		}
		// sessão válida, porém inativa por muito tempo entre 2 requisições
		elseif ($this->isIdled()) {
			$this->_handleIdleSession();
		}
		// sessão válida, não inativa e não expirada
		elseif (isset($this->validSessionCallback)) {
			$this->validSessionCallback->invokeByRef($this->User);
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	Auth::login
	// @desc		Método de autenticação. Verifica a autenticação do usuário
	//				se os parâmetros login e password forem encontrados
	// @access		protected
	// @note		Se a sessão estiver ativa, será encerrada
	// @return		void	
	//!-----------------------------------------------------------------	
	function login() {
		// executa o método de autenticação, implementado nas classes filhas
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
	// @desc		Encerra a sessão atual, se ela existir
	// @access		protected
	// @param		rebuildLogin bool	"FALSE" Indica se a opção de novo login deve ser oferecida
	// @return		void
	//!-----------------------------------------------------------------
	function logout($rebuildLogin=FALSE) {
		// efetua logout somente se existe uma sessão válida
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
	// @desc		Este método deve ser implementado nas classes filhas
	// @access		protected
	// @return		void
	//!-----------------------------------------------------------------
	function authenticate() {
		return TRUE;
	}
	
	//!-----------------------------------------------------------------
	// @function	Auth::_fetchAuthVars
	// @desc		Busca do vetor de variáveis POST os dados de autenticação
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
	// @desc		Trata uma sessão cujo tempo de persistência expirou
	// @access		private
	// @return		void
	// @note		Encerra e chama a callback de expiração, se existir. 
	//				Do contrário, encerra e reconstrói o login	
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
	// @desc		Trata uma sessão cujo tempo de ociosidade foi excedido
	// @access		private
	// @return		void
	// @note		Encerra e chama a callback de ociosidade, se existir. 
	//				Do contrário, encerra e reconstrói o login
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