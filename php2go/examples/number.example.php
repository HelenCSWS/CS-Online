<?php

	// $Header: /www/cvsroot/php2go/examples/number.example.php,v 1.1 2005/05/10 21:25:49 mpont Exp $
	// $Revision: 1.1 $
	// $Date: 2005/05/10 21:25:49 $
	// vim: set expandtab tabstop=4 shiftwidth=4:

	require_once('../p2gConfig.php');
	import('php2go.util.Number');
	
	echo '<B>PHP2Go Example</B> : php2go.util.Number<BR><BR>';
	
	// base conversion
	echo '<B>Convert a number from base M to base N:</B><BR>';
	echo '2 from base 10 to base 2 => ' . Number::numberConversion(2, 10, 2) . '<BR>';
	echo '65534 from base 10 to base 16 => ' . Number::numberConversion(65534, 10, 16) . '<BR>';
	echo '1111111111111111 from base 2 to base 10 => ' . Number::numberConversion('1111111111111111', 2, 10) . '<BR>';
	echo 'FFFFFF from base 16 to base 10 => ' . Number::numberConversion('FFFFFF', 16, 10) . '<BR><BR>';
	
	// hexbin conversion
	echo '<B>Convert an hexadecimal number to binary representation:</B><BR>';
	echo 'FEFE => ' . Number::fromHexToBin('FEFE') . '<BR><BR>';
	
	// decimal to currency conversion
	echo '<B>Show string representation of a decimal number (using fraction notation)</B><BR>';
	echo '1.4 => ' . Number::fromDecimalToFraction(1.4) . '<BR>';
	echo '6.25 => ' . Number::fromDecimalToFraction(6.25) . '<BR>';
	echo '18.875 => ' . Number::fromDecimalToFraction(18.875) . '<BR>';
	echo '9.99 => ' . Number::fromDecimalToFraction(9.99) . '<BR><BR>';
	
	// decimal to fraction conversion
	echo '<B>Convert decimal number into currency value:</B><BR>';
	echo '.01 => ' . Number::fromDecimalToCurrency('.01') . '<BR>';
	echo '1000,25 => ' . Number::fromDecimalToCurrency('1000,25') . '<BR>';	
	echo '2188.76 => ' . Number::fromDecimalToCurrency('2188.76') . '<BR>';	
	echo '-455.33 => ' . Number::fromDecimalToCurrency('-455.33') . '<BR>';	
	echo '1000.00, forcing locale settings => ' . Number::fromDecimalToCurrency(1000.00, 'USD', '.', ',', 2, 'left') . '<BR><BR>';
	
	// arabic-roman and roman-arabic conversions
	echo '<B>Conversion ARABIC=>ROMAN and ROMAN=>ARABIC:</B><BR>';
	echo '100 in ROMAN => ' . Number::fromArabicToRoman('100') . '<BR>';
	echo 'CMXCVIII in ARABIC => ' . Number::fromRomanToArabic('CMXCVIII') . '<BR>';
	echo '1999 in ROMAN => ' . Number::fromArabicToRoman('1999') . '<BR>';
	echo '555 in ROMAN => ' . Number::fromArabicToRoman('555') . '<BR>';
	echo 'DCCCXXXIII IN ARABIC => ' . Number::fromRomanToArabic('DCCCXXXIII') . '<BR><BR>';
	
	// human readable byte amount
	echo '<B>Convert a byte amount to human readable representation:</B><BR>';
	echo '1024 => ' . Number::formatByteAmount(1024, 'K', 0) . '<BR>';
	echo '39292839 => ' . Number::formatByteAmount(39292839, 'M', 2) . '<BR>';
	echo '40 * 1024 * 1024 * 1024 => ' . Number::formatByteAmount(40 * 1024 * 1024 * 1024, 'G', 0) . '<BR><BR>';
	
	// random number
	echo '<B>Generate a random number:</B><BR>';
	for ($i=0; $i<10; $i++) {
		echo 'Random number ' . $i . ': ' . Number::randomize(1, 100) . '<BR>';
	}
	
?>