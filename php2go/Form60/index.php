<?php
	require_once('config/config.php');

	import('Form60.pages.F60DesktopDoc');
        
       import('Form60.base.F60PageStack');
        F60PageStack::Clear();
      
	$doc =& new F60DesktopDoc();
	$doc->display();

?>