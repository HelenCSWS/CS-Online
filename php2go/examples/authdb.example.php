<?php

	// $Header: /www/cvsroot/php2go/examples/authdb.example.php,v 1.11 2005/07/20 22:31:29 mpont Exp $
	// $Revision: 1.11 $
	// $Date: 2005/07/20 22:31:29 $
	// vim: set expandtab tabstop=4 shiftwidth=4:

	require_once('../p2gConfig.php');
	
	import('php2go.auth.AuthDb');
	import('php2go.net.HttpRequest');	
	import('php2go.net.HttpResponse');
	import('php2go.net.Url');
	import('php2go.text.StringUtils');
	
 	/**
	 * This example class was created to demonstrate how to encapsulate Auth configuration and 
	 * Auth callbacks (event handlers) inside a child class. Using this approach, you could use
	 * this child class in all application, without the need of duplicating code.
	 * In this example, we extend php2go.auth.AuthDb because we want to authenticate the user
	 * against a database table (e.g. users)
	 */
	class MyAuth extends AuthDb
	{	
		function MyAuth() {
			parent::AuthDb();
			// define the callbacks (event handlers)
			parent::setLoginFunction(array($this, 'printLogin'));
			parent::setLoginCallback(array($this, 'onLogin'));
			parent::setLogoutCallback(array($this, 'onLogout'));
			parent::setErrorCallback(array($this, 'onError'));
			parent::setExpiryCallback(array($this, 'onExpire'));
			parent::setIdlenessCallback(array($this, 'onIdle'));
			parent::setValidSessionCallback(array($this, 'onValidSession'));
			// set the table columns that must be persisted in the session
			// this method accepts comma separated values or an array
			parent::setDbFields('*');
			// set the name of the table that contains information about the users
			parent::setTableName('users');
			// define an optional extra clause (the initial "AND" must be omitted)
			parent::setExtraClause('active = 1');
			// set the name of the request parameters that will be sent by the login form
			parent::setLoginFieldName('username');
			parent::setPasswordFieldName('password');
			// set the crypt function to be used to verify the user password
			parent::setCryptFunction('md5');
		}
		
		function authenticate() {
			/**
			 * uncomment the following line if you want to 
			 * verify login and password against the database
			 */
			//return AuthDb::authenticate();
			/**
			 * using the next 2 lines, you can perform a 
			 * user/password check against static values
			 */
			return ($this->_login == 'admin' && $this->_password == 'admin');
		}
		
		/**
		 * VALID SESSION CALLBACK
		 * This callback is called when the authenticator detects that there's a valid user stored in the session scope.
		 * When this event is triggered, we're sure that the user is not idled or expired.
		 * IMPORTANT: the User object is passed by reference
		 */
		function onValidSession($currentUser) {
			// set the last visited URIs with parameters
			$currentUser->setPropertyValue('last_visited_uri', HttpRequest::uri());
			// set the number of visited URIs
			$currentUser->setPropertyValue('hit_count', $currentUser->getPropertyValue('hit_count', FALSE)+1);
		}
		
		/**
		 * LOGIN CALLBACK
		 * The login callback is called when the authentication method succeeds. Receives as parameter a reference to
		 * the User object that has just logged in. The most common behaviour of this callback is redirect to a secure page.
		 * However, other behaviours are accepted, such as printing a message or drawing the page without performing any
		 * redirection
		 * IMPORTANT: the User object is passed by reference
		 */
		function onLogin($newUser) {
			// set the last visited URIs with parameters
			$newUser->setPropertyValue('last_visited_uri', HttpRequest::uri());
			// set the number of visited URIs
			$newUser->setPropertyValue('hit_count', $newUser->getPropertyValue('hit_count', FALSE)+1);
			println("the user " . $newUser->getUsername() . " has logged successfully");
		}
		
		/**
		 * ERROR CALLBACK
		 * This callback is called when the authentication method fails. This callback receives as parameter an instance of the 
		 * class User containing the failed username. Normally, this callback should send the user back to the login page or 
		 * redraw the login form displaying some error message
		 */
		function onError($errorUser) {
			// call the login function with an error message
			$this->printLogin('The username ' . $errorUser->getUsername() . ' is invalid or the password don\'t match!');
		}
		
		/**
		 * LOGOUT CALLBACK
		 * Here we define the callback function that will be called when the method logout() in Auth class is called.
		 * This callback receives as parameter a reference to the User object that was logged in.
		 * Normally, this kind of callback function must send the user back to the login page or to the home page
		 */
		function onLogout($lastUser) {
			// here we get an instance of the SessionManager to destroy all the session content
			$Session =& SessionManager::getInstance();
			$Session->destroy();
			// redirect to the login page
			HttpResponse::redirect(new Url(HttpRequest::basePath()));
		}	

		/**
		 * EXPIRENESS CALLBACK
		 * When the session is expired, the Auth class automatically destroys the user session, and calls the expireness
		 * callback if it's defined. This callback receives as parameter a reference to the User object that was logged in.
		 * Some possible behaviours of this callback are redirect to the login page or call the login function providing the
		 * error message to be displayed
		 */
		function onExpire($lastUser) {
			$this->printLogin("The session of the user ".$lastUser->getUsername()." has expired!");
		}
		
		/**
		 * IDLENESS CALLBACK
		 * If the time between one request and another is greater than the defined idle time, the user session will be destroyed
		 * and the idleness callback will be called, if it's defined. This callback receives as parameter a reference to the User
		 * object that was logged in. The suggested behaviours for this callback are the same suggested to the expireness callback
		 */
		function onIdle($lastUser) {
			$this->printLogin("The session of the user ".$lastUser->getUsername()." has been idle for a long time!");
		}
			
		/**
		 * LOGIN FUNCTION
		 * This callback must handle the "non-logged" state. When this auth state is found, the application must show a login form
		 * to the user. In this special case, a login form is printed directly inside the callback. However, the most common behaviour
		 * is to send the user to a special login page (sometimes using a secure channel)
		 */
		function printLogin($msg=NULL) {
			echo "<B>PHP2Go Examples</B> : php2go.auth.AuthDb<br><br>\n";
			if (!TypeUtils::isNull($msg))
				echo $msg . "\n";
			echo "<fieldset style=\"width:230px\"><legend>Login</legend>\n";
            echo "<form method=\"post\" action=\"" . $_SERVER['PHP_SELF'] . "\" style=\"display:inline\">\n";
            echo "<table border=\"0\" cellpadding=\"3\" cellspacing=\"0\">\n";
            echo "<tr>\n";
            echo "    <td>Username:</td>\n";
            echo "    <td><input type=\"text\" id=\"username\" name=\"username\" value=\"\"></td>\n";
            echo "</tr>\n";
            echo "<tr>\n";
            echo "    <td>Password:</td>\n";
            echo "    <td><input type=\"password\" id=\"password\" name=\"password\"></td>\n";
            echo "</tr>\n";
            echo "<tr>\n";
            echo "    <td colspan=\"2\"><input type=\"submit\" value=\"Login\"></td>\n";
            echo "</tr>\n";
            echo "</table>\n";
            echo "</form>\n";
            echo "</fieldset>\n";
		}		
	}
	
	/**
	 * create an instance of the Auth class
	 * don't forget to call the initialization method - init()
	 */
	$myAuth =& new MyAuth();	
	$myAuth->init();
	
	/**
	 * handles the logout parameter
	 * you can define how the logout operation will be done, the use of a GET parameter is simply demonstrative
	 */
	if (isset($_GET['logout'])) {
		$myAuth->logout();
	}	
	
	/**
	 * at this point, we test if there's a valid user in the session. if so, we show some of his properties.
	 * VERY IMPORTANT: everything that requires authentication must be inside a test like this.
	 */
	if ($myAuth->isValid()) {
		$user =& $myAuth->getCurrentUser();
		echo "<B>PHP2Go Examples</B> : php2go.auth.AuthDb<br><br>\n";
		echo "<fieldset style=\"width:340px\"><legend>User is logged</legend>\n";
		echo "<a href=" . $_SERVER['PHP_SELF'] . ">reload this page</a><br>\n";
		echo "<a href=" . $_SERVER['PHP_SELF'] . "?logout>sign out</a><br>\n";
		echo "the logged user is  <B>" . $user->getUsername() . "</B><br>\n";
		echo "user logged in <B>" . $user->getLoginTime('d/m/Y H:i:s') . "</B><br>\n";
		echo "user logged in <B>" . $user->getElapsedTime() . "</B> seconds ago<br>\n";
		if ($myAuth->getExpiryTime() > 0) echo "the user session will expire in <B>" . ($myAuth->getExpiryTime() - $user->getElapsedTime()) . "</B> seconds<br>\n";
		echo "user's last idle time was <B>" . $user->getLastIdleTime() . "</B> seconds<br>\n";
		echo "dump user properties:<br>\n";
		dumpVariable($user->getProperties());		
		echo "</fieldset>\n";
	}

?>