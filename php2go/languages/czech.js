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
//	Translated by:  Jiri Vasina  <poutnik@users.sourceforge.net>
//
// $Header: /www/cvsroot/php2go/languages/czech.js,v 1.7 2005/09/01 15:15:48 mpont Exp $
// $Revision: 1.7 $
// $Date: 2005/09/01 15:15:48 $

var p2gInvalidVal   = "Neplatná hodnota!";
var advEditName     = "Uprav v Roz&#353;í&#345;eném módu";
var advEdValModeMsg = "Pro u&#382;ití pokro&#269;il&#253;ch nástroj&#367; editoru, ozna&#269;te polí&#269;ko 'Roz&#353;íné &#219;pravy'";
var advEdAddLinkMsg = "Zadejte adresu odkazu (P&#345;íklad: http://www.domain.com):";
var advEdAddImgMsg  = "Zadejte cestu obrázku:";
var colorSelTitle	= "Vyberte barvu kterou chcete";
var colorSelChoose	= "Vybrat tuto barvu";
var debugUndefMsg   = "Objekt není definován!";
var insValueMsg     = "Tato hodnota byla ji&#382; vlo&#382;ena!";
var selInsAllMsg    = "Zvolil jste vybrání z mo&#382;n&#253;ch záznam&#367; ve zdrojovém seznamu.\nTato operace m&#367;&#382;e trvat n&#283;kolik sekund.";
var selRemAllMsg    = "Zvolil jste odstran&#283;ní v&#353;ech záznamu v cílovém seznamu.\nTato operace m&#367;&#382;e trvat n&#283;kolik sekund.";
var csvDbInsMsg     = "Data byla &#219;sp&#283;&#353;n&#283; vlo&#382;ena!";
var csvDbAltMsg     = "Data byla &#219;sp&#283;&#353;n&#283; aktualizována!";
var csvDbDelMsg     = "Data byla &#219;sp&#283;&#353;n&#283; smazána!";
var csvDbDelConf    = "Opravdu chcete smazat tento záznam?";
var csvDbErrFilt    = "Musíte vybrat pole a zadat hodnotu pro pou&#382;ití filtru!";
var csvDbEmptyFilt  = "Vyhledávání vrátilo prázdn&#253; seznam záznam&#367;!";
var csvDbEmptyAlt   = "Nejsou &#382;ádné záznamy pro &#219;pravu!";
var csvDbEmptyDel   = "Nejsou &#382;ádné záznamy pro smazání!";
var csvDbErrSort    = "Musíte vybrat pole pro t&#345;íd&#283;ní!";
var csvDbEmptyGoto  = "Musíte zadat &#269;íslo záznamu pro p&#345;echod na n&#283;j!";
var csvDbErrGoto    = "Po&#382;adovan&#253; záznam je neplatn&#253;!";
var srchErrFilt     = "Musíte vybrat filtr a zadat &#345;et&#283;zec pro vyhledávání!"; 
var srchEmptyFilt   = "Vyhledávání vrátilo prázdnou sadu!";
var srchBtnVal      = "Vyhledávám...";
var formFieldsReq	= "Následující pole formulá&#345;e jsou po&#382;adovaná:";
var formFieldReq 	= "Pole %1 je povinné.";
var formFieldsInv	= "Formulá&#345; obsahuje následující chyby:";
var formComplFields = "Prosím, vlo&#382;te pole a zkuste znovu.";
var formFixFields	= "Prosím, opravte pole a zkuste znovu.";
var formFieldsRegex = "Pole %1 má neplatnou hodnotu!";
var formFieldsEq	= "Pole %1 se musí rovnat poli %2!"; 
var formFieldsNeq	= "Pole %1 se nesmí rovnat poli %2!";
var formFieldsGt	= "Pole %1 musí být v&#283;t&#353;í ne&#382; pole %2!";
var formFieldsLt	= "Pole %1 musí být men&#353;í ne&#382; pole %2!"; 
var formFieldsGoet	= "Pole %1 musí být v&#283;t&#353;í nebo rovno poli %2!";
var formFieldsLoet	= "Pole %1 musí být men&#353;í nebo rovno poli %2!";
var formFieldValueEq = "Pole %1 musí být rovno %2!";
var formFieldValueNeq = "Pole %1 nesmí být rovno %2!";
var formFieldValueGt = "Pole %1 musí být v&#283;t&#353;í ne&#382; %2!";
var formFieldValueLt = "Pole %1 musí být men&#353;í ne&#382; %2!";
var formFieldValueGoet = "Pole %1 musí být v&#283;t&#353;í nebo rovno %2!";
var formFieldValueLoet = "Pole %1 musí být men&#353;í nebo rovno %2!";
var reportGoToError = "Neplatné &#269;íslo stránky!";
var reportFilterOk  = "Filtr byl p&#345;idán!";
var reportFilEmpty  = "Nebyly p&#345;idány &#382;ádné filtry";
var reportFilRemove = "Odstra&#328;";
var reportFilClose  = "Zav&#345;i";
var reportFilResend = "Opravdu chcete odeslat vyhledání se zadan&#253;mi poli?";
var reportOpsA      = Array("=","Rovno",
                            "!=","Není rovno",
                            ">","V&#283;t&#353;í",
                            "<","Men&#353;í",
                            ">=","V&#283;t&#353;í nebo rovno",
                            "<=","Men&#353;í nebo rovno");
var reportOpsB      = Array("=","Rovno",
                            "!=","Není rovno",
                            "LIKE","Obsahuje",
                            "NOT LIKE","Neobsahuje",
                            "LIKEI","Za&#269;íná s",
                            "LIKEF","Kon&#269;í s");