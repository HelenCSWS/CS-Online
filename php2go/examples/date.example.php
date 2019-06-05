<?php

	// $Header: /www/cvsroot/php2go/examples/date.example.php,v 1.3 2005/05/23 16:40:07 mpont Exp $
	// $Revision: 1.3 $
	// $Date: 2005/05/23 16:40:07 $
	// vim: set expandtab tabstop=4 shiftwidth=4:

	require_once("../p2gConfig.php");
	import('php2go.datetime.Date');
	
	println('<B>PHP2Go Examples</B> : php2go.datetime.Date<BR>');
	
	// day of week of a given date
	$dow = Date::dayOfWeek(date('d/m/Y'), true, false);
	println('Day of week ('.date('d/m/Y').') : ' . $dow);
	
	// days of a given month
	$dim = Date::daysInMonth(2, 1940);
	println('Days in month (02/1940) : ' . $dim);
	
	// future date
	$fd = Date::futureDate(date('d/m/Y'), 40);
	println('Future date (today + 40 days) : ' . $fd);
	
	// difference between days
	$diff = Date::getDiff('03/05/1980', date('d/m/Y'));
	println('Diff (today - 03/05/1980) : ' . $diff);
	
	// date validation
	print('Date Validation');
	$arr = array('29/02/2003', '31/05/2002', '31/11/2000', '29/02/2000');
	for ($i=0; $i<sizeOf($arr); $i++) {
		if (Date::isValid($arr[$i]))
			print(' - valid : ' . $arr[$i]);
		else
			print(' - not valid : ' . $arr[$i]);
	}
	println('');
	
	// local current date
	println('Local date : ' . Date::localDate());
	
	// month name of a timestamp
	println('Month name (current date) : ' . Date::monthName(time()));
	
	// past date
	$pd = Date::pastDate(date('d/m/Y'), 100);
	println('Past date (today - 100 days) : ' . $pd);

	// previous and next day
	$pd = Date::prevDay('01/03/2004');
	$nd = Date::nextDay('28/02/2004');
	println('Next and Prev day - prev 01/03/2004 : ' . $pd . '  - next 28/02/2004 : ' . $nd);
	
	// print local date
	println('Print date : ' . Date::printDate());
	
	// tomorrow and yesterday
	$t = Date::tomorrow();
	$y = Date::yesterday();
	println('Tomorrow and yesterday : ' . $t . ' ' . $y);	
	
?>