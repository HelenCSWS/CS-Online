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
// $Header: /www/cvsroot/php2go/core/auth/User.class.php,v 1.5 2005/07/25 13:17:34 mpont Exp $
// $Date: 2005/07/25 13:17:34 $

//------------------------------------------------------------------
import('php2go.session.SessionObject');
//------------------------------------------------------------------

//!-----------------------------------------------------------------
// @class		User
// @desc		A classe User � a base de armazenamento dos dados do usu�rio logado
//				em uma aplica��o. � utilizada pela classe Auth (ou uma de suas classes
//				filhas) nas fun��es de cria��o, atualiza��o e controle de uma sess�o
//				de usu�rio. Uma sess�o v�lida de usu�rio significa que uma inst�ncia da
//				classe User est� gravada no escopo de sess�o do PHP
// @package		php2go.auth
// @extends		SessionObject
// @uses		System
// @uses		TypeUtils
// @author		Marcos Pont
// @version		$Revision: 1.5 $
// @note		Exemplo de utiliza��o:<BR>
//				<PRE>
//
//				$User =& User::getInstance();
//				if ($User->isAuthenticated()) {
//				&nbsp;&nbsp;&nbsp;print $User->getUsername();
//				&nbsp;&nbsp;&nbsp;print $User->getLastAccess('d/m/Y H:i:s');
//				}
//
//				</PRE>
//!-----------------------------------------------------------------
class User extends SessionObject
{
	var $username = NULL;		// @var username string		"NULL" Nome do usu�rio
	var $loginTime = NULL;		// @var loginTime int		"NULL" Timestamp da cria��o da sess�o
	var $lastAccess = NULL;		// @var lastAccess int		"NULL" Timestamp do �ltimo acesso do usu�rio
	
	//!-----------------------------------------------------------------
	// @function	User::User
	// @desc		Construtor da classe
	// @access		public
	// @note		**SEMPRE** utilize o m�todo User::getInstance para criar/alterar
	//				inst�ncias da classe User. Desta forma, os dados do usu�rio ser�o
	//				automaticamente atualizados na sess�o a cada encerramento de execu��o
	//!-----------------------------------------------------------------
	function User() {

	
    	$default = PHP2Go::getConfigVal('USER.SESSION_NAME', FALSE);

		

		parent::SessionObject(!empty($default) ? $default : 'php2goSession');
		PHP2Go::registerShutdownFunc(array('User', 'shutdown'));
	}
	
	//!-----------------------------------------------------------------
	// @function	User::&getInstance
	// @desc		Constr�i/retorna o singleton da classe User, ou da classe
	//				filha definida no vetor de configura��es do sistema
	// @access		public
	// @return		User object
	//!-----------------------------------------------------------------
	function &getInstance() {

		static $instance;
		if (!isset($instance)) {
			// busca o container definido na configura��o
			if ($userClassPath = PHP2Go::getConfigVal('USER.CONTAINER_PATH', FALSE, FALSE)) {
				if ($userClass = classForPath($userClassPath)) {					
					$instance = new $userClass();
					if (!TypeUtils::isInstanceOf($instance, 'user'))
						PHP2Go::raiseError(PHP2Go::getLangVal('ERR_INVALID_USERCONTAINER', $userClass), E_USER_ERROR, __FILE__, __LINE__);
				} else {
					PHP2Go::raiseError(PHP2Go::getLangVal('ERR_INVALID_USERCONTAINER_PATH', $userClassPath), E_USER_ERROR, __FILE__, __LINE__);
				}
			} 
			// usa o container padr�o (php2go.auth.User)
			else {	
		
				$instance = new User();
			}
		}
		return $instance;
	}
	
	//!-----------------------------------------------------------------
	// @function	User::shutdown
	// @desc		M�todo est�tico que � chamado no encerramento de cada execu��o
	//				para atualizar o timestamp do �ltimo acesso e publicar os dados
	//				do usu�rio na sess�o
	// @access		public
	// @return		void
	// @static
	//!-----------------------------------------------------------------
	function shutdown() {
	
		$User =& User::getInstance();
		if ($User->isAuthenticated()) {
			$User->lastAccess = System::getMicrotime();
			$User->update();
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	User::authenticate
	// @desc		M�todo que autentica e torna v�lido o objeto User, definindo o
	//				nome e inicializando as propriedades do usu�rio
	// @access		public
	// @param		username string		Nome do usu�rio
	// @param		properties array	"array()" Propriedades do usu�rio a serem gravadas na sess�o
	// @return		void
	//!-----------------------------------------------------------------
	function authenticate($username, $properties=array()) {


		$this->username = $username;
		$this->loginTime = System::getMicrotime();
		$this->lastAccess = System::getMicrotime();
		foreach ((array)$properties as $name => $value)
			parent::createProperty($name, $value);
		parent::createTimeCounter('userTimeStamp');
		parent::register();
	}
	
	//!-----------------------------------------------------------------
	// @function	User::logout
	// @desc		Encerra a sess�o do usu�rio, resetando os dados de autentica��o
	// @access		public
	// @return		bool
	// @note		Se este m�todo retornar FALSE, significa que n�o 
	//				foi poss�vel remover a sess�o do usu�rio
	//!-----------------------------------------------------------------
	function logout() {
		$result = parent::unregister();
		if ($result) {
			$this->username = NULL;
			$this->loginTime = NULL;
			$this->lastAccess = NULL;
			$this->timeCounters = array();		
		}
		return $result;
	}
	
	//!-----------------------------------------------------------------
	// @function	User::isAuthenticated
	// @desc		Verifica se o usu�rio est� autenticado
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function isAuthenticated() {
		return $this->registered;
	}
	
	//!-----------------------------------------------------------------
	// @function	User::getUsername
	// @desc		Retorna o nome do usu�rio
	// @access		public
	// @return		string Nome do usu�rio
	// @note		Se o usu�rio n�o est� autenticado, este m�todo retorna NULL
	//!-----------------------------------------------------------------
	function getUsername() {
		return $this->username;
	}
	
	//!-----------------------------------------------------------------
	// @function	User::setUsername
	// @desc		Define/altera o nome do usu�rio na sess�o
	// @access		public
	// @param		username string		Nome para o usu�rio
	// @return		void
	//!-----------------------------------------------------------------
	function setUsername($username) {

		$this->username = $username;
	}	
	
	//!-----------------------------------------------------------------
	// @function	User::getLoginTime
	// @desc		Retorna o timestamp de cria��o da sess�o
	// @access		public
	// @param		fmt string	"NULL" Formato, opcional
	// @return		mixed Timestamp, ou data/hora formatada se for fornecido um formato
	//!-----------------------------------------------------------------
	function getLoginTime($fmt=NULL) {
		if ($this->registered)
			return (empty($fmt) ? $this->loginTime : date($fmt, $this->loginTime));
		return NULL;
	}
	
	//!-----------------------------------------------------------------
	// @function	User::getLastAccess
	// @desc		Retorna o timestamp do �ltimo acesso do usu�rio
	// @access		public
	// @param		fmt string	"NULL" Formato, opcional
	// @return		mixed Timestamp, ou data/hora formatada se for fornecido um formato
	//!-----------------------------------------------------------------
	function getLastAccess($fmt=NULL) {
		if ($this->registered)
			return (empty($fmt) ? $this->lastAccess : date($fmt, $this->lastAccess));
		return NULL;
	}
	
	//!-----------------------------------------------------------------
	// @function	User::getElapsedTime
	// @desc		Retorna o n�mero de segundos desde a cria��o da sess�o do usu�rio
	// @access		public
	// @return		int N�mero de segundos
	//!-----------------------------------------------------------------
	function getElapsedTime() {
		if ($this->registered) {
			$Counter =& parent::getTimeCounter('userTimeStamp');
			return $Counter->getElapsedTime();
		}
		return 0;
	}
	
	//!-----------------------------------------------------------------
	// @function	User::getLastIdleTime
	// @desc		Retorna o tempo decorrido desde a �ltima requisi��o do usu�rio
	// @access		public
	// @return		int Tempo decorrido, em segundos
	// @note		O valor de retorno deste m�todo � utilizado em testes de tempo m�ximo de inatividade
	//!-----------------------------------------------------------------
	function getLastIdleTime() {
		return (System::getMicrotime() - $this->lastAccess);
	}
	
	//!-----------------------------------------------------------------
	// @function	User::getPropertyValue
	// @desc		Sobrescreve a implementa��o do m�todo getPropertyValue da
	//				classe SessionObject para que consultas por propriedades
	//				n�o existentes retornem NULL
	// @access		public
	// @param		name string		Nome da propriedade
	// @return		mixed Valor da propriedade ou NULL se ela n�o existir
	//!-----------------------------------------------------------------
	function getPropertyValue($name) {
		if ($this->registered) {
			$property = parent::getPropertyValue($name, FALSE);
			if ($property !== FALSE)
				//print $property;
				return $property;
				
				
		}
		return NULL;
	}
	
	//!-----------------------------------------------------------------
	// @function	User::toString
	// @desc		Constr�i a representa��o string do usu�rio
	// @access		public
	// @return		string
	//!-----------------------------------------------------------------
	function toString() {
       
	/*	return sprintf("User object {\nUsername: %s\nAuthenticated: %d\nProperties: %s}", 
			$this->username, ($this->registered ? 1 : 0), dumpArray($this->properties)
		);
        */
        return sprintf("User object {\nUsername: %s\nAuthenticated: %d\nProperties: %s}", 
			$this->username, ($this->registered ? 1 : 0), dumpArray($this->properties)
		);
        
  	}	
}
?>