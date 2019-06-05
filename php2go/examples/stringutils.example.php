<?php

	// $Header: /www/cvsroot/php2go/examples/stringutils.example.php,v 1.1 2005/05/16 18:18:30 mpont Exp $
	// $Revision: 1.1 $
	// $Date: 2005/05/16 18:18:30 $
	// vim: set expandtab tabstop=4 shiftwidth=4:

	require_once('../p2gConfig.php');
	import('php2go.text.StringUtils');
	
	println('<B>PHP2Go Example</B> : php2go.text.StringUtils<BR>');
	
	println('<B>Trim and remove unnecessary whitespaces and blank chars from a string:</B>');
	println(StringUtils::stripBlank('  the quick   brown fox		jumps over 	the 		lazy dog') . '<BR>');
	
	println('<B>Extract portions of a string (shortcuts to PHP function substr)</B>');
	println(StringUtils::left('PHP2Go Web Development Framework', 10));
	println(StringUtils::right('PHP2Go Web Development Framework', 10));
	println(StringUtils::mid('PHP2Go Web Development Framework', 10, 5));
	println(StringUtils::charAt('foo bar baz', 4) . '<BR>');

	println('<B>Insert a substring into an existent string</B>');
	println(StringUtils::insert('foo baz', 'bar ', 4) . '<BR>');
	
	println('<B>Match a substring against an existent string (using strpos)</B>');
	dumpVariable(StringUtils::match('foo bar baz', 'oo', TRUE)) . '<BR><BR>';
	
	println('<B>String transformation methods</B>');
	println('Encode (using base64): ' . StringUtils::encode('this is a test', 'base64'));
	println('Encode (using quoted-printable): ' . StringUtils::encode('Hi! How are you?', 'quoted-printable', array('charset' => 'utf8')));
	println('Decode (using base64): ' . StringUtils::decode(base64_encode('this is a test'), 'base64'));
	println('Filter (accept only numbers): ' . StringUtils::filter('11249dhahd93848', 'num', ''));
	println('Filter (remove htmlentities, using replace string): ' . StringUtils::filter("one&nbsp;two&nbsp;three", 'htmlentities', '*'));
	println('Escape (convert html special chars): ' . StringUtils::escape('this is a <tag>', 'html'));
	println('Escape (convert all html entities): ' . StringUtils::escape('this is a <tag> and this is a string with accents: βγκυτ', 'htmlall'));
	println('Capitalize: ' . StringUtils::capitalize('this is an example of a capitalized text. the first letter of each word is uppercased.'));
	println('Normalize (convert all accents): ' . StringUtils::normalize('remove all accents: βγκτυ') . '<BR>');
	
	println('<B>Generate a random string</B>');
	println(StringUtils::randomString(10, TRUE, TRUE) . '<BR>');
	
	println('<B>String formatting methods</B>');
	println('Indent text (using tab character, size 1):<BR><pre>' . StringUtils::indent("this is an indented text.\nit must be moved 1 \"tab\" from the left side of the page.", 1, chr(9)) . '</pre>');
	println('Truncate text: ' . StringUtils::truncate("this is an example of a long sentence, that must be truncate when it reaches 100 chars. The portion of the sentence after this number of chars must not be displayed.", 100, '...'));
	println('Spacify text: ' . StringUtils::insertChar("PHP2GO WEB DEVELOPMENT FRAMEWORK", ' ', FALSE));
	println('Wrap text (size 20):<BR><pre>' . StringUtils::wrap("this is a long text that must be wrapped to have 20 chars per line. the method also cares about the word breaks, to preserve its integrity in the displayed content.", 20) . '</pre>');
	$d = fread(fopen('resources/css.example.css', 'rb'), filesize('resources/css.example.css'));
	println('Add line numbers<BR>' . StringUtils::addLineNumbers($d, 1, 3, ')', '<BR>'));

?>