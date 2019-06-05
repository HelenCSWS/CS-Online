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

var p2gInvalidVal   = "Neplatn� hodnota!";
var advEditName     = "Uprav v Roz&#353;�&#345;en�m m�du";
var advEdValModeMsg = "Pro u&#382;it� pokro&#269;il&#253;ch n�stroj&#367; editoru, ozna&#269;te pol�&#269;ko 'Roz&#353;�n� &#219;pravy'";
var advEdAddLinkMsg = "Zadejte adresu odkazu (P&#345;�klad: http://www.domain.com):";
var advEdAddImgMsg  = "Zadejte cestu obr�zku:";
var colorSelTitle	= "Vyberte barvu kterou chcete";
var colorSelChoose	= "Vybrat tuto barvu";
var debugUndefMsg   = "Objekt nen� definov�n!";
var insValueMsg     = "Tato hodnota byla ji&#382; vlo&#382;ena!";
var selInsAllMsg    = "Zvolil jste vybr�n� z mo&#382;n&#253;ch z�znam&#367; ve zdrojov�m seznamu.\nTato operace m&#367;&#382;e trvat n&#283;kolik sekund.";
var selRemAllMsg    = "Zvolil jste odstran&#283;n� v&#353;ech z�znamu v c�lov�m seznamu.\nTato operace m&#367;&#382;e trvat n&#283;kolik sekund.";
var csvDbInsMsg     = "Data byla &#219;sp&#283;&#353;n&#283; vlo&#382;ena!";
var csvDbAltMsg     = "Data byla &#219;sp&#283;&#353;n&#283; aktualizov�na!";
var csvDbDelMsg     = "Data byla &#219;sp&#283;&#353;n&#283; smaz�na!";
var csvDbDelConf    = "Opravdu chcete smazat tento z�znam?";
var csvDbErrFilt    = "Mus�te vybrat pole a zadat hodnotu pro pou&#382;it� filtru!";
var csvDbEmptyFilt  = "Vyhled�v�n� vr�tilo pr�zdn&#253; seznam z�znam&#367;!";
var csvDbEmptyAlt   = "Nejsou &#382;�dn� z�znamy pro &#219;pravu!";
var csvDbEmptyDel   = "Nejsou &#382;�dn� z�znamy pro smaz�n�!";
var csvDbErrSort    = "Mus�te vybrat pole pro t&#345;�d&#283;n�!";
var csvDbEmptyGoto  = "Mus�te zadat &#269;�slo z�znamu pro p&#345;echod na n&#283;j!";
var csvDbErrGoto    = "Po&#382;adovan&#253; z�znam je neplatn&#253;!";
var srchErrFilt     = "Mus�te vybrat filtr a zadat &#345;et&#283;zec pro vyhled�v�n�!"; 
var srchEmptyFilt   = "Vyhled�v�n� vr�tilo pr�zdnou sadu!";
var srchBtnVal      = "Vyhled�v�m...";
var formFieldsReq	= "N�sleduj�c� pole formul�&#345;e jsou po&#382;adovan�:";
var formFieldReq 	= "Pole %1 je povinn�.";
var formFieldsInv	= "Formul�&#345; obsahuje n�sleduj�c� chyby:";
var formComplFields = "Pros�m, vlo&#382;te pole a zkuste znovu.";
var formFixFields	= "Pros�m, opravte pole a zkuste znovu.";
var formFieldsRegex = "Pole %1 m� neplatnou hodnotu!";
var formFieldsEq	= "Pole %1 se mus� rovnat poli %2!"; 
var formFieldsNeq	= "Pole %1 se nesm� rovnat poli %2!";
var formFieldsGt	= "Pole %1 mus� b�t v&#283;t&#353;� ne&#382; pole %2!";
var formFieldsLt	= "Pole %1 mus� b�t men&#353;� ne&#382; pole %2!"; 
var formFieldsGoet	= "Pole %1 mus� b�t v&#283;t&#353;� nebo rovno poli %2!";
var formFieldsLoet	= "Pole %1 mus� b�t men&#353;� nebo rovno poli %2!";
var formFieldValueEq = "Pole %1 mus� b�t rovno %2!";
var formFieldValueNeq = "Pole %1 nesm� b�t rovno %2!";
var formFieldValueGt = "Pole %1 mus� b�t v&#283;t&#353;� ne&#382; %2!";
var formFieldValueLt = "Pole %1 mus� b�t men&#353;� ne&#382; %2!";
var formFieldValueGoet = "Pole %1 mus� b�t v&#283;t&#353;� nebo rovno %2!";
var formFieldValueLoet = "Pole %1 mus� b�t men&#353;� nebo rovno %2!";
var reportGoToError = "Neplatn� &#269;�slo str�nky!";
var reportFilterOk  = "Filtr byl p&#345;id�n!";
var reportFilEmpty  = "Nebyly p&#345;id�ny &#382;�dn� filtry";
var reportFilRemove = "Odstra&#328;";
var reportFilClose  = "Zav&#345;i";
var reportFilResend = "Opravdu chcete odeslat vyhled�n� se zadan&#253;mi poli?";
var reportOpsA      = Array("=","Rovno",
                            "!=","Nen� rovno",
                            ">","V&#283;t&#353;�",
                            "<","Men&#353;�",
                            ">=","V&#283;t&#353;� nebo rovno",
                            "<=","Men&#353;� nebo rovno");
var reportOpsB      = Array("=","Rovno",
                            "!=","Nen� rovno",
                            "LIKE","Obsahuje",
                            "NOT LIKE","Neobsahuje",
                            "LIKEI","Za&#269;�n� s",
                            "LIKEF","Kon&#269;� s");