<?php

	// $Header: /www/cvsroot/php2go/examples/timecounter.example.php,v 1.7 2004/03/08 22:22:27 mpont Exp $
	// $Revision: 1.7 $
	// $Date: 2004/03/08 22:22:27 $
	// vim: set expandtab tabstop=4 shiftwidth=4:

	require_once('../p2gConfig.php');
	import('php2go.datetime.TimeCounter');
	
	echo '<B>PHP2Go Examples</B> : php2go.datetime.TimeCounter<BR><BR>';
	
	$timeCounter = new TimeCounter();
	echo 'Start<BR>', 'Sleep 2 seconds...<BR>';
	flush();
	sleep(2);	
	echo 'Elapsed time : ' . $timeCounter->getElapsedTime() . '<BR>', 'Sleep 2 seconds...<BR>';	
	flush();
	sleep(2);
	echo 'Elapsed time : ' . $timeCounter->getElapsedTime() . '<BR>', 'Sleep 2 seconds...<BR>';	
	flush();
	sleep(2);	
	$timeCounter->stop();
	echo 'Stop<BR>', 'Final interval : ' . $timeCounter->getInterval() . '<BR>';
	

?>