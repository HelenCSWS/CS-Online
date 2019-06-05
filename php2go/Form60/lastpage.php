<?php

	require_once('config/config.php');
	import('Form60.pages.F60DesktopDoc');
        
        import('Form60.base.F60PageStack');
        $popPage = "";
        if (isset($_REQUEST['pop']))
        {
            $popPage = $_REQUEST['pop'];
        }
        $lastPage = F60PageStack::getLastPage();
        F60PageStack::popLastPage();
        F60PageStack::gotoLastPage();        

?>