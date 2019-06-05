<?php

	// $Header: /www/cvsroot/php2go/examples/htmlutils.example.php,v 1.8 2005/01/24 12:18:49 mpont Exp $
	// $Revision: 1.8 $
	// $Date: 2005/01/24 12:18:49 $
	// vim: set expandtab tabstop=4 shiftwidth=4:

	require_once("../p2gConfig.php");
	import('php2go.util.HtmlUtils');
	
	echo '<B>PHP2Go Examples</B> : php2go.util.HtmlUtils<BR><BR>';
	
	/**
	 * Utility method to build HTML anchors
	 */
	$anchor = HtmlUtils::anchor('javascript:void(0);', 'Click me!', 'Text hint in the status bar', '', array('onClick'=>"alert(\"onClick\")"));
	print "Anchor : $anchor<BR><BR>";
	
	/**
	 * Method that builds mailto: anchors with obfuscation, protecting the e-mail address against robots
	 */
	$mailto = HtmlUtils::mailtoAnchor('mpont@users.sourceforge.net', 'Obfuscated mailto anchor', 'Obfuscated mailto anchor');
	print "Mailto anchor : $mailto<BR><BR>";	
	
	/**
	 * Utility method to buil IMG tags
	 */
	$image = HtmlUtils::image("http://php2go.sourceforge.net/resources/images/p2g_powered.gif");
	print "Image :<BR>$image<BR><BR>";
	
	/**
	 * Another way of using HtmlUtils::anchor, this time with an event handler that opens a popup window
	 * IMPORTANT NOTE : The HtmlUtils::window method will not work in a script that doesn't have an instance of the Document class. In these cases,
	 * you must include PHP2GO_JAVASCRIPT_PATH . 'libs/window.js' to make it work
	 */
	$script = "<script language='JavaScript' type='text/javascript' src='" . PHP2GO_JAVASCRIPT_PATH . 'libs/window.js' . "'></script>";	
	$anchor = HtmlUtils::anchor('javascript:void(0)', 'Open a new window', 'Open a new window', '', array('onClick' => HtmlUtils::window('http://php2go.sourceforge.net', 32, 800, 600)));
	print "Popup window anchor : $script$anchor<BR><BR>";
	
	/**
	 * Utility method to repeat the same tag $n times
	 */
	$repeat = HtmlUtils::tagRepeat('big', 'PHP2Go', 3);
	print "Tag repeat : $repeat<BR><BR>";
	
	/**
	 * The following method can build buttons, with JS events and CSS support
	 */
	$button = HtmlUtils::button('BUTTON', 'btnTest', 'Click me!', "onClick=\"alert('onClick');\"", 'button alt');
	print "Button : $button<BR><BR>";
	
	/**
	 * You can parse the links included in a text using the following method
	 */
	$parsedText = HtmlUtils::parseLinks("Visit http://php2go.sourceforge.net, and download PHP2Go! You can also visit the SF project page : http://sourceforge.net/projects/php2go/");
	print "Parse links in text : $parsedText<BR><BR>";
	
	/**
	 * The next 3 lines are method calls that build the proper HTML code to load a movie from Real Inc., Media Player and Quicktime.
	 * The 4th parameter for each call is an array of options to the HTML widget. For more information about these options, please consult
	 * the source code of the class (PHP2GO_ROOT/core/util/HtmlUtils)
	 */
	$real = HtmlUtils::realPlayerMovie("sample.rm", 240, 180, array('CONTROLS'=>1, 'AUTO_START'=>1, 'CLIP_STATUS'=>1));
	print "Real Player movie :<BR><BR>$real<BR><BR>";
	$mplayer = HtmlUtils::mediaPlayerMovie("sample.asx", 240, 180, array('CONTROLS'=>1, 'AUTO_START'=>1));
	print "Media Player movie :<BR><BR>$mplayer<BR><BR>";
	$qtime = HtmlUtils::quickTimeMovie("sample.mov", 360, 270, array('AUTO_START'=>1, 'CONTROLS'=>1));
	print "Quick Time movie :<BR><BR>$qtime<BR><BR>";
						
?>