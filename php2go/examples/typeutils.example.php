<?php

	// $Header: /www/cvsroot/php2go/examples/typeutils.example.php,v 1.2 2004/08/17 16:06:30 mpont Exp $
	// $Revision: 1.2 $
	// $Date: 2004/08/17 16:06:30 $
	// vim: set expandtab tabstop=4 shiftwidth=4:

	require_once('../p2gConfig.php');
	
	echo '<B>PHP2Go Example</B> : php2go.util.TypeUtils<BR><BR>';
	
	echo 'The class TypeUtils is automatically imported when you require p2gConfig.php. Besides, it\'s a static class.<BR>';
	
	$number = 4;
	echo 'Is integer ('.$number.') ? ' . TypeUtils::parseInteger(TypeUtils::isInteger($number)) . '<BR>';
	
	$float = 'abc';
	echo 'Is float ('.$float.') ? ' . TypeUtils::parseInteger(TypeUtils::isFloat($float)) . '<BR>';
	
	$toParse = "test00001";
	echo 'Parse integer ('.$toParse.') = ' . TypeUtils::parseInteger($toParse) . '<BR>';
	
	$toParse = "9,1";
	echo 'Parse float ('.$toParse.') = ' . TypeUtils::parseFloat($toParse) . '<BR>';
	
	$isNull = '';
	echo 'Is null (empty string) ? ' . TypeUtils::parseInteger(TypeUtils::isNull($isNull)) . '<BR>';
	
	$false = strpos('teste', 'a');
	echo 'Is false (strpos of a in string teste) ? ' . TypeUtils::parseInteger(TypeUtils::isFalse($false)) . '<BR>';
	
	class type_utils_example {
		function type_utils_example() {
		}
	}
	$t = new type_utils_example();
	echo 'Is instance of type_utils_example (' . exportVariable($t) . ') ? ' . TypeUtils::parseInteger(TypeUtils::isInstanceOf($t, 'type_utils_example'));
	
?>