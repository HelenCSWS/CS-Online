<?php

/**
 * Perform the necessary imports
 */
import('php2go.auth.AuthDb');
import('php2go.net.HttpCookie');

class F60Auth extends AuthDb {
	
	/**
	 * This property will be used to build the login form view
	 */
	var $doc = NULL;
	
	function F60Auth($validateOnly = FALSE) {
		
		/**
		 * Call the parent ctor
		 */
		parent::AuthDb();		
		
		/**
		 * Define the table where the user data is stored,
		 * the login and password fields that will be sent in the request
		 */
		parent::setTableName('users u INNER JOIN user_levels ul ON u.user_level_id = ul.user_level_id');
		parent::setLoginFieldName('username');
		parent::setPasswordFieldName('userpass');
        parent::setExtraClause('u.deleted = 0');
		
		/**
		 * Define that all the table fields must be copied to the session object
		 * Add province_id ,Feb 25,2008 --- Helen
		 */
		parent::setDbFields('user_id, u.user_level_id, ul.name as user_level_name, first_name, last_name, email1,province_id');
		
		/**
		 * Define the crypt function that is used to compare the passwords
		 * By default, the framework compares plain text strings
		 */
		parent::setCryptFunction('');
		
		/**
		 * Define the name of your session object
		 * By default, the session key name is php2goSession
		 */
	//	parent::setSessionKeyName($this->getConfigVal('SESSION_KEY_NAME'));
	//	parent::setExpiryTime(0);
                //parent::setIdleTime(1800);
                
                /**
                 * For validation we don't need to display login form
                 */
                if (!$validateOnly)
                {
                    
                   parent::setLoginFunction(array($this, 'loginForm'));
                    
                    /**
                     * Define the methods that will handle success, logout and error in the login execution chain
                     */
                    parent::setLoginCallback(array($this, 'onLogin'));
                    parent::setLogoutCallback(array($this, 'onLogout'));
                    parent::setErrorCallback(array($this, 'onError'));
                    parent::setIdlenessCallback(array($this, 'onIdle'));
		}
		/**
		 * Initialize the authentication process
		 */
		//AuthDb::init();	
                
	}

	function loginForm($error=NULL) {
        
                import('php2go.base.Document');
                import('php2go.form.FormTemplate');
                
		/**
		 * This method will generate a view to the user containing the login form
		 * So, we must create an instance of Document class, to generate an HTML document
		 */
		 
	
		$this->doc =& new Document(TEMPLATE_PATH . 'login_layout.tpl'); 
        $icons="<LINK REL=\"icon\" type=\"image/png\" href=\"Address-bar-icon.png\" />";
	   $this->doc->appendHeaderContent($icons);
		$this->doc->setCache(FALSE);
		$this->doc->addStyle(CSS_PATH . 'Form60app.css');
		$this->doc->addScript('resources/js/javascript.common.js');
		$this->doc->addScript(PHP2GO_JAVASCRIPT_PATH . "libs/cookie.js");
		
		        
		$this->doc->appendBodyContent("<script language=\"JavaScript1.2\" TYPE=\"TEXT/JAVASCRIPT\">gotoTop();</script>");
		
           
    
		/**
		 * Here we assign the login error, if it's passed to this method
		 */
		if ($error)
          $this->doc->elements['error'] = $error;
			
		/**
		* Create a form containing username and password fields
		*/
       $form =& new FormTemplate(XML_PATH . 'login.xml', TEMPLATE_PATH .'login_form.tpl', 'F60LoginForm', $this->doc);

       $this->doc->elements['main'] = $form->getContent();
       
       $this->doc->elements['year'] =date(Y);
          //   $this->doc->elements['current_year'] ='2008';
		
		/**
		 * Request focus on the "username" field, in the "F60LoginForm" form
		 */
		$this->doc->setFocus('F60LoginForm', 'username');
		
		$this->doc->attachBodyEvent('onLoad', 'loadCookie();');
	
		/**
		 * Display the view (HTML document)
		 */
		$this->doc->display();
	}
	
	function onLogin($newUser) {
                $this->_initSession(&$newUser);
                import('php2go.net.HttpResponse');
                import('php2go.net.Url');
                HttpResponse::redirect(new Url('index.php'));
		exit;
	}
	
	function onError($errorUser) {
		$this->loginForm('<font color=red>Username or password invalid</font><br>');
	}
	
	function onLogout($lastUser) {
            if (isset($lastUser))
                $this->_endSession(&$lastUser);
            $msg = '<font color=red>Logged out</font><br>';
            $this->loginForm($msg);
	}	
        
        function onIdle($lastUser) {
            $msg = "<font color=red>The session of the user ".$lastUser->getUsername()." has been idle for a long time!</font><br>";
            $this->loginForm($msg);
        }
        
        function _initSession($newUser)
        {
            import('php2go.util.TypeUtils');
            import('Form60.util.F60Date');
            //record login
            $user_id = $newUser->getPropertyValue('user_id');
            $province_id = $newUser->getPropertyValue('province_id');
    
            
            $Db =& Db::getInstance();
            $login_history_id = $Db->insert('login_history', array("user_id"=>$user_id, 
                "when_logged_in"=>F60Date::sqlDateTime()));
            $newUser->createProperty('login_history_id', $login_history_id);
            //get restricted page list
            $user_level_id = $newUser->getPropertyValue('user_level_id');
          
          	setcookie("F60_USER_LEVEL_ID",$user_level_id,0);
			setcookie("F60_USER_PROVINCE_ID",$province_id,0);
            
    
            
				if($province_id<1)
            {
					$province_id=1;
					setcookie("F60_USER_PROVINCE_ID",0,0);
				}
				else
				{
						setcookie("F60_USER_PROVINCE_ID",1,0);	
				}
				
           
			  	 setcookie("F60_PROVINCE_ID",$province_id);
           
          
			if($province_id==1)
            {
	            $clause = "user_level_id = " . $newUser->getPropertyValue('user_level_id') ;
	            $QueryBuilder =& new QueryBuilder("p.name", "user_levels_page_restrictions ur", $clause);
	         }
	         elseif($province_id==2)
	         {
	            $clause = "province_id =$province_id and user_level_id = " . $newUser->getPropertyValue('user_level_id') ;
	            $QueryBuilder =& new QueryBuilder("p.name,p.page_id", "user_levels_page_restrictions ur", $clause);
					
			}
            else // ON, MB and other provinces are available , since their level are same  so we use ON province ID as on condition ; March 28th 2018
            {
               
            $clause = "province_id =3 and user_level_id = " . $newUser->getPropertyValue('user_level_id') ;
            $QueryBuilder =& new QueryBuilder("p.name,p.page_id", "user_levels_page_restrictions ur", $clause);
				
		
            }
            $QueryBuilder->joinTable('pages p', 'INNER JOIN', 'ur.page_id = p.page_id');
            $oldMode = $Db->setFetchMode(ADODB_FETCH_ASSOC);
            $restricted = $Db->getAll($QueryBuilder->getQuery());
            $restrictedPages = "";
            $sub_pageIDs="";
            foreach ($restricted as $row)
            {
                if (TypeUtils::isArray($row))
                {
                     $restrictedPages = $restrictedPages . "," . $row["name"];        
						
                     $sub_pageIDs = $sub_pageIDs . "," . $row["sub_page_id"];
                }
                
            }
  			
            $newUser->createProperty('restricted_pages', substr($restrictedPages,1));
            
            $newUser->createProperty('restricted_sub_pages', substr($sub_pageIDs,1));
          
			$pageStack = array();
          
			$newUser->createProperty('page_stack', $pageStack);
			
            $newUser->update();
        }
        
        function _endSession($lastUser)
        {
            import('Form60.util.F60Date');
            //record logout
            $login_history_id = $lastUser->properties["login_history_id"];
            $Db =& Db::getInstance();
            $Db->update('login_history', array("when_logged_out"=>F60Date::sqlDateTime()),
                "login_history_id = " . $login_history_id);
        }
        
        function getRestrictedPages()
        {
            if ($this->isValid())
            {
                $currentUser = $this->getCurrentUser();
                return $currentUser->getPropertyValue('restricted_pages');
            }
            else return "";
        }
        
        function getCurrentUserID()
        {
            $currentUser = $this->getCurrentUser();
            return $currentUser->getPropertyValue('user_id');
        }
        
        function isActiveSession()
        {
            return (!$this->isIdled() && !$this->isExpired() && $this->isValid());
        }
}
?>
