<?php
//
// +----------------------------------------------------------------------+
// | PHP2Go Web Development Framework                                     |
// +----------------------------------------------------------------------+
// | Copyright (c) 2002-2005 Marcos Pont                                  |
// +----------------------------------------------------------------------+
// | This library is free software; you can redistribute it and/or        |
// | modify it under the terms of the GNU Lesser General Public           |
// | License as published by the Free Software Foundation; either         |
// | version 2.1 of the License, or (at your option) any later version.   |
// | 																	  |
// | This library is distributed in the hope that it will be useful,      |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of       |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU    |
// | Lesser General Public License for more details.                      |
// | 																	  |
// | You should have received a copy of the GNU Lesser General Public     |
// | License along with this library; if not, write to the Free Software  |
// | Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA             |
// | 02111-1307  USA                                                      |
// +----------------------------------------------------------------------+
//
// $Header: /www/cvsroot/php2go/apps/calculator.php,v 1.12 2005/07/20 22:46:11 mpont Exp $
// $Date: 2005/07/20 22:46:11 $
// $Revision: 1.12 $

//------------------------------------
require_once("../p2gConfig.php");
import('php2go.base.Document');
import('php2go.util.HtmlUtils');
//------------------------------------

if (!isset($_GET['form']) || !isset($_GET['field']) || !isset($_GET['language']) || !isset($_GET['mask'])) {
	$Lang =& LanguageBase::getInstance();
	$errorMsg = $Lang->getLanguageValue('ERR_CALCULATOR_MISSING_PARAMETERS');
	HtmlUtils::alert($errorMsg);
	HtmlUtils::closeWindow();
} else {
	$Init =& Init::getInstance();
	$Init->setLocale($_GET['language']);
	$Doc = new Document(PHP2GO_TEMPLATE_PATH . "simplelayout.tpl");
	$Doc->setTitle($Doc->getLangVal('CALCULATOR_WINDOW_TITLE'));
	$Doc->setCache(FALSE);
	$Doc->addBodyCfg(array('topmargin' => 0, 'leftmargin' => 0, 'marginwidth' => 0, 'marginheight' => 0));
	$Doc->addScriptCode(
		"	var outerForm  = \"$_GET[form]\";\n" .
		"	var outerField = \"$_GET[field]\";\n" .
		"	var outerMask = \"$_GET[mask]\";
	");
	$Doc->addScript("../resources/javascript/modules/calculator.js");
	$Tpl =& new Template(PHP2GO_TEMPLATE_PATH . 'calculator.tpl', T_BYFILE);
	$Tpl->parse();
	$Tpl->assign('result_caption', PHP2Go::getLangVal('CALCULATOR_RESULT_CAPTION'));
	$Doc->elements['main'] =& $Tpl;
	$Doc->display();	
}
?>