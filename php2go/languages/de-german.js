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
// Translated and maintained by: Stefan Riedel <stefanrhro@web.de>
//
// $Header: /www/cvsroot/php2go/languages/de-german.js,v 1.3 2005/09/01 13:20:41 mpont Exp $
// $Revision: 1.3 $
// $Date: 2005/09/01 13:20:41 $

var p2gInvalidVal   = "Kein korrekter Inhalt!";
var advEditName     = "Im erweiterten Modus bearbeiten";
var advEdValModeMsg = "Um den erweiterten Modus zu benutzen deaktivieren Sie die 'Advanced Edit' Checkbox";
var advEdAddLinkMsg = "Geben Sie die Adresse des Links an (Beispiel: http://www.domain.com):";
var advEdAddImgMsg  = "Geben Sie den Pfad zum Bild an (Beispiel: /images/image1.gif oder http://www.domain.com/images/image1.gif):";
var colorSelTitle	= "Klicken Sie auf die Farbe die Sie haben m&ouml;chten";
var colorSelChoose	= "W&auml;hle diese Farbe";
var debugUndefMsg   = "Das Objekt ist nicht definiert!";
var insValueMsg     = "Dieser Wert wurde bereits eingef&uuml;gt!";
var selInsAllMsg    = "Sie haben alle verf&uuml;gbaren Datens&auml;tze gew&auml;hlt.\nDas Ergebniss kann einige Sekunden dauern.";
var selRemAllMsg    = "Sie haben alle Datens&aauml;tze zum l&ouml;schen ausgew&auml;hlt.\nDiese Operation kann einige Sekunden dauern.";
var csvDbInsMsg     = "Daten erfolgreich eingetragen!";
var csvDbAltMsg     = "Daten erfolgreich erneuert!";
var csvDbDelMsg     = "Daten erfolgreich gel&ouml;scht!";
var csvDbDelConf    = "Sind Sie sicher, dass Sie diesen Datensatzt l&ouml;schen m&ouml;chten?";
var csvDbErrFilt    = "Sie m&uuml;ssen ein Feld w&auml;hlen und den Inhalt ausw&auml;hlen um den Filter zu nutzen!";
var csvDbEmptyFilt  = "Ihre Sucher ergab ein leeres Datensatzfeld!";
var csvDbEmptyAlt   = "Es sind keine Daten zum editieren vorhanden!";
var csvDbEmptyDel   = "Es sind keine Daten zum l&ouml;schen vorhanden!";
var csvDbErrSort    = "Sie m&uuml;ssen ein Feld zum sortieren w&auml;hlen!";
var csvDbEmptyGoto  = "Sie m&uuml;ssen die Datensatznummer angebeb, zu der gesprungen werden soll!";
var csvDbErrGoto    = "Der angeforderte Datensatz ist nicht korrekt!";
var srchErrFilt     = "Du musst einen Filter wählen und ein Suchkriterium auswählen!";
var srchEmptyFilt   = "Die Suche erbrachte ein leeres Resultat!";
var srchBtnVal      = "Suche...";
var formFieldsReq	= "Die folgenden Felder des Formulars sind Pflichtfelder:";
var formFieldReq	= "Das Feld %1 ein Pflichtfeld.";
var formFieldsInv	= "Im Formular sind folgende Fehler:";
var formComplFields = "F&uuml;llen Sie bitte die Felder und versuchen Sie es erneut!";
var formFixFields	= "Bitte korrigieren Sie die Felder und versuchen Sie es erneut";
var formFieldsRegex = "Das Feld %1 hat einen nicht korrekten Inhalt!";
var formFieldsEq	= "Das Feld %1 den gleichen Inhalt haben wie das Feld %2!"; 
var formFieldsNeq	= "Das Feld %1 darf nicht den gleichen Inhalt haben wie das Feld %2!";
var formFieldsGt	= "Der Inhalt in Feld %1 muss gr&ouml;&szlig;er sein als der Inhalt von Feld %2!";
var formFieldsLt	= "Der Inhalt von Feld %1 muss kleiner sein als der von Feld %2!"; 
var formFieldsGoet	= "Der Inhalt von Feld %1 muss gr&ouml;&szlig;er oder gleich Feld %2 sein!";
var formFieldsLoet	= "Der Inhalt von Feld %1 muss kleiner oder gleich Feld %2 sein!";
var formFieldValueEq = "Der Inhalt von Feld %1 muss der gleiche sein wie in Feld %2!";
var formFieldValueNeq = "Der Inhalt von Feld %1 darf nicht der gleiche sein wie von Feld %2!";
var formFieldValueGt = "Der Inhalt von Feld %1 muss gr&ouml;&szlig;er sein als der von Feld %2!";
var formFieldValueLt = "Der Inhalt von Feld %1 muss kleiner sein als der von Feld %2!";
var formFieldValueGoet = "Der Inhalt von Feld %1 muss gr&ouml;&szlig;er oder gleich sein als der von Feld %2!";
var formFieldValueLoet = "Der Inhalt von Feld %1 muss kleiner oder sein als der von Feld %2!";
var reportGoToError = "Keine korrekte Seitenzahl!";
var reportFilterOk  = "Filter hinzugef&uuml;gt!";
var reportFilEmpty  = "Es sind keine Filter vorhanden";
var reportFilRemove = "L&ouml;schen";
var reportFilClose  = "Schlie&szlig;en";
var reportFilResend = "M&ouml;chten Sie die Suche erneut starten mit den gleichen Suchkriterien?";
var reportOpsA      = Array("=","Gleich",
                            "!=","Nicht gleich",
                            ">","Gr&ouml;&szlig;er",
                            "<","Kleiner",
                            ">=","Gr&ouml;&szlig;er oder gleich",
                            "<=","Kleiner oder gleich");
var reportOpsB      = Array("=","Gleich",
                            "!=","Nicht gleich",
                            "LIKE","Beinhaltet",
                            "NOT LIKE","Beinhaltet nicht",
                            "LIKEI","Beginnt mit",
                            "LIKEF","Endet mit");