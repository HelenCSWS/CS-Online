<?php

	// $Header: /www/cvsroot/php2go/examples/formservervalidation.example.php,v 1.5 2005/06/27 20:45:48 mpont Exp $
	// $Revision: 1.5 $
	// $Date: 2005/06/27 20:45:48 $
	// vim: set expandtab tabstop=4 shiftwidth=4:

	require_once('../p2gConfig.php');
	import('php2go.base.Document');
	import('php2go.form.FormBasic');
	import('php2go.form.FormTemplate');
	import('php2go.net.HttpResponse');
	
	/**
	 * create and configure an instance of the class document, where the form will be included
	 */
	$doc =& new Document('resources/layout.example.tpl');
	$doc->addStyle('resources/css.example.css');
	$doc->setTitle('PHP2Go - Form validated on server');
	
	/**
	 * create and configure an instance of the form class	 
	 */
	if ($_GET['frm'] == 'FormTemplate') {
		$form =& new FormTemplate('resources/formservervalidation.example.xml', 'resources/formservervalidation.example.tpl', 'serverform', $doc);
		$form->setFormAction(HttpRequest::uri());
		/**
		 * set the display options of error messages
		 * the first param is the template variable where the server validation errors must be shown
		 * the second param is the client-side errors display mode: FORM_CLIENT_ERROR_ALERT (default) or FORM_CLIENT_ERROR_DHTML
		 * the third param is the container HTML element where the client-side errors must be displayed
		 */
		$form->setErrorDisplayOptions('error', FORM_CLIENT_ERROR_DHTML, 'form_client_errors');
		/**
		 * define the css style and list mode of the server error messages
		 * these settings will be used to client-side errors if the display mode
		 * is set to FORM_CLIENT_ERROR_DHTML
		 */
		$form->setErrorStyle('error_style', FORM_ERROR_BULLET_LIST, NULL, 'error_header');
		$form->setInputStyle('input_style');	
		$form->setLabelStyle('label_style');		
	} else {
		$form =& new FormBasic('resources/formservervalidation.example.xml', 'serverform', $doc);
		$form->setFormAction(HttpRequest::uri());
		/**
		 * configure label width (a value between 0 and 1)
		 */
		$form->setLabelWidth(0.15);
		/**
		 * set the total form width
		 */
		$form->setFormWidth(580);
		/**
		 * set input and label
		 */
		$form->setInputStyle('input_style');	
		$form->setLabelStyle('label_style');		
		/**
		 * this line will make the client-side errors visible inside 
		 * a DIV container and using the same server style settings
		 */
		$form->setErrorDisplayOptions(FORM_CLIENT_ERROR_DHTML);
		/**
		 * define the error messages css style, list mode and header text
		 */
		$form->setErrorStyle('error_style', FORM_ERROR_BULLET_LIST, 'The form contains the following error(s):', 'error_header');
	}
	
	/**
	 * the isPosted() method verifies if the request contains a variable called __form_signature which
	 * value is the same signature of the FormBasic instance
	 */
	if ($form->isPosted()) {
		/**
		 * the isValid() method executes the chain of validation of the form;
		 * each field has a set of validators according to its configuration;
		 */
		if ($form->isValid()) {
			HttpResponse::redirect(new Url(HttpRequest::uri()));
		}
	}
	
	/**
	 * create a DocumentElement instance to render the main content of the page
	 */
	$main =& new DocumentElement();
	$frm = ($_GET['frm'] == 'FormTemplate' ? 'FormBasic' : 'FormTemplate');
	$main->put("
		<div class=\"sample_simple_text\">
		<B>PHP2Go Examples</B> : php2go.form.FormBasic with server validation<br/>
		<A HREF=\"?frm={$frm}\" CLASS=\"sample_simple_text\">See the same example with php2go.form.{$frm}</A><br/><br/>
		<b>IMPORTANT:</b><br/>Disable JavaScript in your browser to see the server validation in action.<br/><br/>
		</div>
	");
	$main->put($form->getContent());
	$main->parse();
	$doc->elements['main'] =& $main;
	
	/**
	 * display the HTML document
	 */
	$doc->display();
	
	/**
	 * customized function used to execute the copy/move operation on the uploaded file
	 * >> this function is defined in the form XML file (attribute SAVEFUNCTION in the FILEFIELD entity)
	 * >> the FileUpload class calls this function after checking the file integrity
	 * >> the function receives as parameter an array containing information about the upload handler
	 */
	function uploadHandler($fileData) {
		$name = $fileData['save_name'];
		// apply some transformations in the file name
		$name = trim(strtolower($name));
		$name = StringUtils::filter($name, 'blank', '_');
		$name = date('Ymd') . '_' . $name;
		$name = StringUtils::truncate($name, 32, '');
		// move the file manually from the temp directory
		if (@move_uploaded_file($fileData['tmp_name'], $fileData['save_path'] . $name)) {
			$fileData['save_name'] = $name;
			@chmod($fileData['save_path'] . $fileData['save_name'], $fileData['save_mode']);
		} else {
			$fileData['error'] = "It wasn't possible to move the uploaded file {$fileData['save_name']}";
		}
		// FileUpload class expects this function to *always* return back the modified array
		return $fileData;
	}

?>