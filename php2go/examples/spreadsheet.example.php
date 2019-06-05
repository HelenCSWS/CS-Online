<?php

	// $Header: /www/cvsroot/php2go/examples/spreadsheet.example.php,v 1.5 2004/09/08 17:15:03 mpont Exp $
	// $Revision: 1.5 $
	// $Date: 2004/09/08 17:15:03 $
	// vim: set expandtab tabstop=4 shiftwidth=4:

	require_once('../p2gConfig.php');
	import('php2go.util.Spreadsheet');
	
	echo '<B>PHP2Go Example</B> : php2go.util.Spreadsheet<BR><BR>';

	$sp =& new Spreadsheet();
	
	/**
	 * create a font format. the method returns an index that can be used later
	 */	
	$arialBold = $sp->addFont(array('bold'=>true, 'italic'=>true, 'name'=>'Arial'));
	/**
	 * create a cell format
	 */
	$borders = $sp->addCellFormat(array('left_border'=>true, 'right_border'=>true, 'shaded'=>true));
	
	/**
	 * write some data in the spreadsheet cells
	 */
	$sp->writeString(0, 0, 'Test', 0, 0, $arialBold, $borders);
	$sp->writeNumber(2, 2, 1, 30);
	$sp->addCellNote(0, 0, 'Test');
	
	/**
	 * create a frozen pane
	 */
	$sp->freezePanes(1, 10);
	
	/**
	 * save the final content
	 */
	$location = 'resources/spreadsheet.example.xls';
	$sp->toFile($location);
	
	print 'Spreadsheet generated and saved at <a href=\'' . $location . '\'>' . $location . '</a>';

?>