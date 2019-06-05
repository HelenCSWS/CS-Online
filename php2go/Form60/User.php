<?php

	require_once('config/config.php');
	import('Form60.pages.F60AddUserForm');
        import('php2go.net.HttpRequest');
	
        $doc =& new F60AddUserForm();
        if (isset($_GET['action'])) {
		$doc->processForm();
		
	} 
        else {
	        $doc->display();
        }

?>