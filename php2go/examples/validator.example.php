<?php
	
	// $Header: /www/cvsroot/php2go/examples/validator.example.php,v 1.5 2004/03/08 22:22:27 mpont Exp $
	// $Revision: 1.5 $
	// $Date: 2004/03/08 22:22:27 $
	// vim: set expandtab tabstop=4 shiftwidth=4:
	
	require_once('../p2gConfig.php');
	import('php2go.validation.Validator');
	
	echo '<B>PHP2Go Example</B> : package php2go.validation<BR><BR>';
	
	// AlphaCharsValidator
	//----------------------------------------------------------------------
	$wrongValues = array();	
	$values = array('version 5.0.1', 'black & white', 'wrong value', 'php 4.0', 'ããã');
	if (Validator::validateMultiple('php2go.validation.AlphaCharsValidator', $values, array(
		'space'=>TRUE, 'number'=>TRUE, 'punctuation'=>TRUE),
		$wrongValues)) {
		print 'AlphaCharsValidator => OK';
	} else {
		print 'AlphaCharsValidator => ERROR<br>Valores Errados:<BR>'; var_dump($wrongValues);
	}
	echo '<BR><BR><HR><BR>';
	
	
	// ChoiceValidator
	//----------------------------------------------------------------------
	$choice = 'male';
	$options = array('male', 'female');
	if (Validator::validate('php2go.validation.ChoiceValidator', $choice, array('options'=>$options))) {
		echo 'ChoiceValidator => OK';
	} else {
		echo 'ChoiceValidator => ERROR';
	}
	echo '<BR><BR><HR><BR>';
	
	// CreditCardValidator
	//----------------------------------------------------------------------	
	$number = '5555555555552232';	
	if (Validator::validate(
		'php2go.validation.CreditCardValidator', 
		$number,
		array(
			'name' => 'Cartão de Crédito',
			'type' => 'mastercard',
			'expiryMonth' => 9,
			'expiryYear' => 2003
		)
	)) {
		echo 'CreditCardValidator => OK';
	} else {
		echo 'CreditCardValidator => ERROR';
	}
	echo '<BR><BR><HR><BR>';
	
	// DateValidator
	//----------------------------------------------------------------------	
	$date = '10/12/2003 10:55';
	if (Validator::validate('php2go.validation.DateValidator', $date, array('type' => 'EURO'))) {
		echo 'DateValidator => OK';
	} else {
		echo 'DateValidator => ERROR';
	}
	echo '<BR><BR><HR><BR>';
	
	// EmailValidator
	//----------------------------------------------------------------------	
	$wrongValues = array();
	$emails = array(
		'foo@bar.baz',
		'xpto@xyz.com',
		'yclw@domain',
		'john@doe.org',
		'roald@dahl'
	);
	if (Validator::validateMultiple('php2go.validation.EmailValidator', $emails, NULL, $wrongValues)) {
		echo 'EmailValidator => OK';
	} else {
		echo 'EmailValidator => ERROR<br>Valores Errados:<br>';
		var_dump($wrongValues);
	}
	echo '<BR><BR><HR><BR>';
	
	// IntervalValidator
	//----------------------------------------------------------------------	
	$wrongValues = array();
	$values = array(
		2, 12, 29, 66, 55, 17, 20.12, 36.099, 70.01
	);
	if (Validator::validateMultiple('php2go.validation.IntervalValidator', $values, array('min' => 10, 'max' => 70), $wrongValues)) {
		echo 'IntervalValidator => OK';
	} else {
		echo 'IntervalValidator => ERROR<br>Valores Errados:<br>';
		var_dump($wrongValues);
	}
	echo '<BR><BR><HR><BR>';
	
	// IPAddressValidator
	//----------------------------------------------------------------------	
	$wrongValues = array();
	$ipAddresses = array(
		'192.168.1.1',
		'192.168.1.21',
		'200.148.300.1',
		'24.16.001.12',
		'200.176.99.35',
		'255.255.255.0'
	);
	if (Validator::validateMultiple('php2go.validation.IPAddressValidator', $ipAddresses, NULL, $wrongValues)) {
		echo 'IPAddressValidator => OK';
	} else {
		echo 'IPAddressValidator => ERROR<br>Valores Errados:<br>';
		var_dump($wrongValues);
	}
	echo '<BR><BR><HR><BR>';
	
	// MaxValidator
	//----------------------------------------------------------------------	
	$wrongValues = array();
	$values = array(
		1,2,3,4,5,6,7,8,9,10.01
	);
	if (Validator::validateMultiple('php2go.validation.MaxValidator', $values, array('max' => 10), $wrongValues)) {
		echo 'MaxValidator => OK';
	} else {
		echo 'MaxValidator => ERROR<br>Valores Errados:<br>';
		var_dump($wrongValues);
	}
	echo '<BR><BR><HR><BR>';
	
	// MinValidator
	//----------------------------------------------------------------------	
	$value = 10;
	if (Validator::validate('php2go.validation.MinValidator', $value, array('min' => 1))) {
		echo 'MinValidator => OK';
	} else {
		echo 'MinValidator => ERROR';
	}
	echo '<BR><BR><HR><BR>';
	
	// RegexValidator
	//----------------------------------------------------------------------	
	$pattern = "^[a-zA-Z]{3}-[1-9][0-9]{3}$";
	$type = 'POSIX';
	$value = 'FOO-2003';
	if (Validator::validate('php2go.validation.RegexValidator', $value, array('pattern' => $pattern, 'type' => $type))) {
		echo 'RegexValidator => OK';
	} else {
		echo 'RegexValidator => ERROR';		
	}
	echo '<BR><BR><HR><BR>';
	
	// UrlValidator
	//----------------------------------------------------------------------	
	$wrongValues = array();
	$urlArray = array(
		'http://www.domain.org/subdomain/url.html?get=value#fragment',
		'https://www.domain.com/secureurl?sessid=1AB39482910000B0203940FF02930401',
		'http://domain/subdomain/',
		'ftp://ftp.domain.org/pub/downloads/sw/xpto5/',
		'http://www.domain.edu.br//'
	);
	if (Validator::validateMultiple('php2go.validation.UrlValidator', $urlArray, NULL, $wrongValues)) {
		echo 'UrlValidator => OK';
	} else {
		echo 'UrlValidator => ERROR<br>Valores Errados:<br>';
		var_dump($wrongValues);
	}
	echo '<BR><BR><HR><BR>';
	
?>