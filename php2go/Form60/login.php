<?php

	require_once('config/config.php');	
	import('Form60.base.F60Auth');	
	
        $auth =& Auth::getInstance();
        
    
         
	if ($auth->isActiveSession() && isset($_GET['logoff'])) 
        {
            $auth->logout();
        } 
        else 
        {
            //clean up current session
            //if (isset($_GET['logoff']))
            {
                $currentUser = & $auth->getCurrentUser();
                $currentUser->logout();
            }
            
            $auth->init(); 
            
            if ($auth->isActiveSession())
            {
             
                import('php2go.net.HttpResponse');
                HttpResponse::redirect(new Url('index.php'));
                exit;
            }
           
            
        }
	

?>