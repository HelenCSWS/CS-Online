<?php

	// $Header: /www/cvsroot/php2go/examples/mailmessage.example.php,v 1.8 2004/08/17 16:06:30 mpont Exp $
	// $Revision: 1.8 $
	// $Date: 2004/08/17 16:06:30 $
	// vim: set expandtab tabstop=4 shiftwidth=4:

	require_once('../p2gConfig.php');
	import('php2go.net.MailMessage');
	
	echo '<B>PHP2Go Example</B> : php2go.net.MailMessage<BR><BR>';

	/**
	 * Class instance
	 */
	$mail = new MailMessage();
	
	/**
	 * define subject and recipients
	 */
	$mail->setSubject("Foo Bar Baz");
	$mail->setFrom("john@foo.net", "John");
	$mail->addTo("paul@foo.com", "Paul");
	$mail->addCC("mary@baz.org", "Mary");
	
	/**
	 * define message HTML body -> this will set the type of the message to "multipart"
	 */
	$mail->setHtmlBody("
	<html>
		<body>
			<table><tr><td>
				<img src='cid:image'/><br/>
				This is HTML Mail!
			</td></tr></table>
		<body>
	</html>
	");
	
	/**
	 * add an embedded image, that must be included in the code using something like <IMG SRC='cid:image'/>
	 * the 4th parameter of this method is the file mime type. if omitted, it will be retrieved using the file extension
	 */
	$mail->addEmbeddedFile('resources/p2g_logo1.jpg', 'image', 'base64');
	
	/**
	 * build the message headers and body
	 */
	$mail->build();
	
	/*
	 * fetch an instance of the mail transporter
	 */
	$transport =& $mail->getTransport();
	
	/**
	 * configure transport using an SMTP server
	 * >>> attention : allowed options : server, port, user, password, debug
	 */
	//$transport->setType(MAIL_TRANSPORT_SMTP, array('server'=>'foo.bar.baz.com', 'username'=>'user', 'password'=>'pass'));
	
	/** 
	 * configure transport using PHP mail() function
	 */
	$transport->setType(MAIL_TRANSPORT_MAIL);
	
	/**
	 * configure transport using UNIX sendmail
	 * >>> attention : allowed options : sendmail (sendmail executable path)
	 */
	//$transport->setType(MAIL_TRANSPORT_SENDMAIL, array('sendmail' => '/usr/sbin/sendmail'));
	
	/**
	 * send the message
	 */
	if ($transport->send())
		print "sent ok";
	else
		print "delivery error";

?>