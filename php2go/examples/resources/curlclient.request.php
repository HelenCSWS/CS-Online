<?php

	// this file is used to answer to curlclient.example.php requests	
	require_once('../../p2gConfig.php');	
	if (!empty($_POST)) {
		print "POST superglobal dump:<BR>";
		echo '<L>';
		foreach ($_POST as $key=>$value)
			print "<LI>{$key}=>{$value}</LI>";
		echo '</L>';
	} else {
		print "this is the remote page response body";		
	}
		

?>