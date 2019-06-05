<?php

	// $Header: /www/cvsroot/php2go/examples/crypt.example.php,v 1.7 2004/03/08 22:22:27 mpont Exp $
	// $Revision: 1.7 $
	// $Date: 2004/03/08 22:22:27 $
	// vim: set expandtab tabstop=4 shiftwidth=4:

	require_once('../p2gConfig.php');
	import('php2go.security.Crypt');
	
	echo 
		'<B>PHP2Go Examples</B> : php2go.security.Crypt<BR><BR>',
		'Crypt cipher : blowfish (MCRYPT_BLOWFISH)<BR>',
		'Crypt mode : CBC (MCRYPT_MODE_CBC)<BR>',
		'Crypt key : \'this is the encrypt key\'<BR>',
		'Crypt data : \'this is secret data that must be encrypted\'<BR>';	
	
	$crypt = new Crypt();
	$crypt->setCipher(MCRYPT_BLOWFISH);
	$crypt->setCipherMode(MCRYPT_MODE_CBC);
	$crypt->setKey('this is the encrypt key');
	$data = 'this is secret data that must be encrypted';	
	$encrypted = $crypt->engineEncrypt($data);
	echo 'Encrypted : ' . $encrypted . '<BR>';
	$decrypted = $crypt->engineDecrypt($encrypted);
	echo 'Decrypted : ' . $decrypted . '<BR>';

?>