<?
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
// | 																	                                    |
// | This library is distributed in the hope that it will be useful,      |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of       |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU    |
// | Lesser General Public License for more details.                      |
// | 																	                                    |
// | You should have received a copy of the GNU Lesser General Public     |
// | License along with this library; if not, write to the Free Software  |
// | Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA             |
// | 02111-1307  USA                                                      |
// +----------------------------------------------------------------------+
//
// Translated and maintained by: Stefan Riedel <stefanrhro@web.de>
//
// $Header: /www/cvsroot/php2go/languages/de-german.inc,v 1.9 2005/09/01 13:20:41 mpont Exp $
// $Revision: 1.9 $
// $Date: 2005/09/01 13:20:41 $

$P2G_LANGUAGE['ERR_OLD_PHP_VERSION'] = "PHP2Go kann mit den aktuellen Servereinstellungen nicht arbeiten!<BR>Ihre PHP- Version ist <I>%s</I>. Wir empfehlen jedoch mindestens <I>4.1.0</I>.<BR>Installieren Sie PHP ab der Version 4.1.0 um mit PHP2Go normal arbeiten zu k&ouml;nnen!";
$P2G_LANGUAGE['ERR_UNSUPPORTED_EXTENSION'] = "Die Erweiterung <I>%s</I> wird von der aktuellen PHP Version leider nicht unterst&uuml;tzt.";
$P2G_LANGUAGE['ERR_OBJ_MESSAGE'] = "Error in Klasse %s:<BR>%s";
$P2G_LANGUAGE['ERR_SCRIPT_MESSAGE'] = "Error in Skript:<BR>%s";
$P2G_LANGUAGE['ERR_DATABASE'] = "Datenbank Error";
$P2G_LANGUAGE['ERR_MISSING_DATABASE_PARAMETERS'] = "Es war leider nicht m�glich die Datenbank Parameter zu laden! [ID: %s]";
$P2G_LANGUAGE['ERR_INVALID_DATABASE_PARAMETERS'] = "Die Datenbank Parameter sind nicht korrekt. USER, BASE und TYPE sind Pflichtwerte!  [ID: %s]";
$P2G_LANGUAGE['ERR_DATABASE_CONNECTION_FAILED'] = "Es war leider nicht m&ouml;glich mit den aktuellen Datenbankeinstellungen sich zur Datenbank zu verbinden. Haben Sie die richtigen Daten angegeben?";
$P2G_LANGUAGE['ERR_TYPES'] = array(
	1 => "Schwerer Fehler",
	2 => "Warnung",
	4 => "Syntax Fehler",
	8 => "Nachricht",
	16 => "Schwerer Quellcode Fehler",
	32 => "Quellcode Warnung",
	64 => "Schwerer Kompiler Fehler",
	128 => "Kompiler Warnung",
	256 => "User Fehler",
	512 => "User Warnung",
	1024=> "User Nachricht"
);
$P2G_LANGUAGE['ERR_ABSOLUTE_URI_NOT_FOUND'] = "Der 'ABSOLUTE_URI' Eintrag ist nicht gesetzt im globalen Konfigurationsarray!";                                   
$P2G_LANGUAGE['ERR_CONFIG_ENTRY_NOT_FOUND'] = "Das Konfigurationselement <I>%s</I> wurde nicht gesetzt im globalen Konfigurationsarray!";
$P2G_LANGUAGE['ERR_INVALID_LANGDOMAIN_FILE'] = "Die Nachrichtendatei %s f&uuml;r die Domain %s, ist nicht korrekt oder existiert nicht.";
$P2G_LANGUAGE['ERR_CANT_FIND_CFG_VAL'] = "Es war leider nicht m&ouml;glich den <I>%s</I> Eintrag im globalen Konfigurationsarray zu finden. Ist er ordentlich gesetzt?";
$P2G_LANGUAGE['ERR_CANT_LOAD_MODULE'] = "Das Modul <I>%s</I> konnte leider nicht geladen werden.";
$P2G_LANGUAGE['ERR_CANT_LOAD_DIR'] = "Es war nicht m&ouml;glich Module in einem ordentlichen Verzeichnis (<I>%s</I>) zu laden";
$P2G_LANGUAGE['ERR_CANT_LOAD_DIR_MODULE'] = "Es war nicht m&ouml;glich die Datei <I>%s</I> in <I>%s</I> zu laden";
$P2G_LANGUAGE['ERR_CANT_CONVERT_DATE'] = "Das Datum konnte leider nicht ins Format %s umgewandelt werden.";
$P2G_LANGUAGE['ERR_CANT_RUN_ON_WINDOWS'] = "Die Klasse %s Kann unter Windows leider nicht genutzt werden!";
$P2G_LANGUAGE['ERR_UNDEFINED_FUNCTION'] = "Die Funtkion %s existiert nicht!";
$P2G_LANGUAGE['ERR_UNDEFINED_METHOD'] = "Die Methode %s existiert nicht in der Klasse %s!";
$P2G_LANGUAGE['ERR_ABSTRACT_CLASS'] = "Die Klasse <I>%s</I> ist als \"abstract\" deklariert und kann nicht direkt instanziert werden!";
$P2G_LANGUAGE['ERR_DATE_FORMAT'] = "Das Datum %s ist nicht im empfohlenen Format %s.";
$P2G_LANGUAGE['ERR_INVALID_RESOURCE'] = "Die Variable %s, die zur Funktion oder Methode %s geh&ouml;rt ist keine ordentlichen Quelle.";
$P2G_LANGUAGE['ERR_INVALID_AUTHENTICATOR'] = "Die Authentifizierung �ber %s ist leider keine Unterklasse von php2go.net.Auth!";
$P2G_LANGUAGE['ERR_INVALID_AUTHENTICATOR_PATH'] = "Es war leider nicht m&ouml;glich, den Authentifizierer %s zu importieren und/oder zu initialisieren!";
$P2G_LANGUAGE['ERR_INVALID_USERCONTAINER'] = "Der User Container %s ist keine Unterklasse von php2go.auth.User!";
$P2G_LANGUAGE['ERR_INVALID_USERCONTAINER_PATH'] = "Es war leider nicht m&ouml;glich den User Container %s zu importieren und/oder zu initialisieren!";
$P2G_LANGUAGE['ERR_XML_PARSE'] = "Error beim Parsen der XML Datei:<BR>Error: %s<BR>Line: %s, Column: %s";
$P2G_LANGUAGE['ERR_STATEMENT_EVAL'] = "Error beim Parsen des Php Codes <I>%s</I>";
$P2G_LANGUAGE['ERR_CANT_FIND_FILE'] = "Konnte die Datei oder das Verzeichniss %s nicht finden. Existiert sie/ es &uuml;berhaupt? Ist der Pfad korrekt gesetzt?";
$P2G_LANGUAGE['ERR_CANT_OPEN_FILE'] = "Es war nicht m&ouml;glich die datei %s mit der Methode %s zu &ouml;ffnen. &Uuml;berpr&uuml;fen Sie die Rechte und die Methode!";
$P2G_LANGUAGE['ERR_CANT_CREATE_FILE'] = "Es war nicht m&ouml;glich die Datei oder das Verzeichniss %s zu erstellen. Haben Sie Schreibrechte im \"Elternverzeichniss\"?";
$P2G_LANGUAGE['ERR_CANT_READ_FILE'] = "Konnte die datei %s nicht lesen. Existiert sie &uuml;berhaupt? Ist der Pfad korrekt gesetzt?";
$P2G_LANGUAGE['ERR_CANT_WRITE_FILE'] = "Es war leider nicht m&ouml;glich die Datei oder das Verzichniss %s zum Schreiben zu &ouml;ffnen. Haben Sie rechte zum &uuml;berschreiben?";
$P2G_LANGUAGE['ERR_CANT_DELETE_FILE'] = "Die Datei %s konnte leider nicht gel&oum;scht werden.<BR>Haben Sie Schreibrechte in diesem Verzeichniss? Darf die Datei gel&ouml;scht werden?";
$P2G_LANGUAGE['ERR_CANT_INCLUDE_FILE'] = "Konnte die Datei %s leider nicht einbinden. Existiert sie &uuml;berhaupt? Ist der Pfad korrekt gesetzt?";
$P2G_LANGUAGE['ERR_CANT_CHANGE_MODE'] = "Es war leider nicht m&ouml;glich, die Datei oder das Verzeichnis %s auf den Mode %s zu setzen.";
$P2G_LANGUAGE['ERR_CANT_READ_DIR'] = "Konnte das Verzeichniss %s leider nicht finden. Existiert es &uuml;berhaupt?";
$P2G_LANGUAGE['ERR_CANT_OPEN_DIR'] = "Konnte das Verzeichniss %s zum Lesen nicht &ouml;ffnen. Sind die Rechte korrekt gesetzt?";
$P2G_LANGUAGE['ERR_CANT_FIND_SERIALIZATION_FILE'] = "Es war leider nicht m&ouml;glich die Datei mit dem seriellen Objekt zum lesen zu &ouml;ffnen. Ist der Pfad korrekt? Wurde die Datei erstellt?";
$P2G_LANGUAGE['ERR_EMPTY_INSERT_QUERY'] = "Der \"INSERT- Query\" in die Tabelle '%s' lieferten mit den aktuellen Werten ein leeres Resultat.";
$P2G_LANGUAGE['ERR_EMPTY_UPDATE_QUERY'] = "Der \"UPDATE- Query\" in die Tabelle '%s' mit der Klausel %s lieferten mit den aktuellen Werten ein leeres Resultat.";
$P2G_LANGUAGE['ERR_EMPTY_TOGLOBALS_QUERY'] = "Der \"globale Query\" %s, mit den aktuellen Variablen lieferte ein leeres Resultat.";
$P2G_LANGUAGE['ERR_TOGLOBALS_WRONG_USAGE'] = "Der Query f&uuml;r die Methode Db::toGlobals() muss mit \"SELECT\" beginnen (DQL).";
$P2G_LANGUAGE['ERR_EMPTY_TEMPLATE_FILE'] = "Konnte das Template nicht laden da kein Dateiname angegeben wurde!";
$P2G_LANGUAGE['ERR_EMPTY_TEMPLATE_CONTENT'] = "Das Template wurde nicht pr&auml;pariert, es hat leider keinen Inhalt. &Uuml;berpr&uuml;fen Sie bitte ob der Inhalt zum Konstruktor passt.";
$P2G_LANGUAGE['ERR_CANT_FIND_VARIABLE'] = "Konnte die Variable %s im Template <I>%s</I> leider nicht finden. <BR>Um sie korrekt zu setzen, setzen Sie {%s} ins Template ein.";
$P2G_LANGUAGE['ERR_CANT_FIND_BLOCK'] = "Der Block <I>%s</I> konnte leider nicht erstellt werden. Ist er gesetzt im Template?";
$P2G_LANGUAGE['ERR_CANT_FIND_BLOCK_REUSE'] = "Es war nicht m&ouml;glich den Block %s zu finden und als %s zu nutzen.";
$P2G_LANGUAGE['ERR_CANT_PUT_ON_PREPARED_TEMPLATE'] = "Das Element %s wurde schon mit der Methode DocumentElement::parse() konfiguriert. Es ist nicht empfehlenswert Elemente danach Element mit put() hinzuzuf&uuml;gen.";
$P2G_LANGUAGE['ERR_TEMPLATE_NOT_PREPARED'] = "Das Template %s wurde nicht vorbereitet. Benutzen Sie die Methode %s::parse(); bevor Sie mit dem Objekt weitere Operationen durchf&uuml;hren.";
$P2G_LANGUAGE['ERR_TEMPLATE_ALREADY_PREPARED'] = "Dieses Template wurde bereits bearbeitet. Du musst vorher die Funktion parse() aufrufen.";
$P2G_LANGUAGE['ERR_UNBALANCED_BLOCKDEF'] = "Die Definition des Blockes <I>%s</I> ist nicht korrekt. Wom&ouml;glich ist er nicht ordentlich geschlossen";
$P2G_LANGUAGE['ERR_INVALID_MODIFIER'] = "The modifier %s is invalid or inexistent!";
$P2G_LANGUAGE['ERR_EMPTY_DOC_LAYOUT'] = "Es sind leider keine Elemente im aktuellen Dokumenten Layout  definiert.";
$P2G_LANGUAGE['ERR_CANT_REPLICATE_ROOT_BLOCK'] = "Der Hauptblock eines Templates darf leider nicht &uuml;berschrieben werden.";
$P2G_LANGUAGE['ERR_MUST_BE_POSITIVE'] = "Der Wert von %s muss gr&ouml;&szlig;er als null bei der benutzten Funktion %s sein.";
$P2G_LANGUAGE['ERR_MUST_BE_AN_ARRAY'] = "Der Wert von %s muss ein Array bei der benutzten Funtkion %s sein";
$P2G_LANGUAGE['ERR_VALUE_OUT_OF_BOUNDS'] = "Der Wert von '%s' ist au&szlig;erhalb der erlaubten Grenze: %s und %s";
$P2G_LANGUAGE['ERR_URL_MALFORMED'] = "Der Wert von <I>%s</I> ist keine URL!";
$P2G_LANGUAGE['ERR_HOST_INVALID'] = "<I>%s</I> ist kein Host der konnektiert werden kann!";
$P2G_LANGUAGE['ERR_DNS_NOT_FOUND'] = "Es wurde leider kein DNS- Server zu der Domain %s gefunden.";
$P2G_LANGUAGE['ERR_SESSION_PROPERTY_NOT_FOUND'] = "Die Eigenschaft %s der Session- Variable %s konnte leider nicht gefunden werden. Benutzen Sie bitte die createProperty() Methode!";
$P2G_LANGUAGE['ERR_SESSION_TIMECOUNTER_NOT_FOUND'] = "Der Timecounter %s f&uuml;r die Session- Variable %s konnte leider nicht gefunden werden. Benutzen Sie bitte die createTimeCounter() Methode!";
$P2G_LANGUAGE['ERR_INVALID_OBJECT'] = "Das Objekt welches als Parameter &uuml;bergeben wurde ist leider keine Instanz der Klasse <I>%s</I>!";
$P2G_LANGUAGE['ERR_CALCULATOR_MISSING_PARAMETERS'] = "Zu wenig Parameter f&uuml;r den Taschenrechner!";
$P2G_LANGUAGE['ERR_COLORSEL_MISSING_PARAMETERS'] = "Zu wenig Parameter f&uuml;r die Farbpalette!";
$P2G_LANGUAGE['ERR_INVALID_FORM_METHOD'] = "Die Methode %s ist nicht korrekt um das Formular '%s' abzuschicken";
$P2G_LANGUAGE['ERR_INVALID_VALIDATE_FUNC'] = "<I>%s</I> ist kein korrekter Name f&uuml;r eine Validations- Funktion!";
$P2G_LANGUAGE['ERR_FORM_DUPLICATED_FIELD'] = "Das Feld <I>%s</I> wurde im Formular <I>%s</I> schon definiert!";
$P2G_LANGUAGE['ERR_FORM_DUPLICATED_BUTTON'] = "Der Button <I>%s</I> wurde im Formular <I>%s</I> bereits definiert!";
$P2G_LANGUAGE['ERR_FORM_DUPLICATED_SECTION'] = "Die Sektion ID <I>%s</I> wurde im Formular <I>%s</I> schon definiert!";
$P2G_LANGUAGE['ERR_FORM_SECTION_EVALFUNC'] = "Es war leider nicht m&ouml;glich die Sichtbarkeit der Sektion <I>%s</I> zu definieren, weil die %s Funktion ist nicht definiert!";
$P2G_LANGUAGE['ERR_FORM_SECTION_TPLBLOCK'] = "Die Sektion <I>%s</I> ist bedingt. Sie m&uuml;ssen einen Block <I>%s</I> im Template definieren der die Sektion Felder beinhaltet!";
$P2G_LANGUAGE['ERR_FORM_FIELD_TITLE'] = "Dieses Formular beinhaltet folgende Errors:";
$P2G_LANGUAGE['ERR_FORM_FIELD_INVALID_INPUT'] = "Das Feld %s beinhaltet ein unakzeptablen Inhalt %s.";
$P2G_LANGUAGE['ERR_FORM_FIELD_INVALID_FLOAT'] = "Die nummerische Prezision des Feldes %s ist nicht korrekt.\\nDie maximale Gr&ouml;&szlig;e ist %d f&uuml;r den Ganzzahl Teil und %d f&uuml;r den Dezimalzahl Teil.";
$P2G_LANGUAGE['ERR_FORM_FIELD_ALPHANUM'] = "Das Feld %s darf nur Buchstaben beinhalten.";
$P2G_LANGUAGE['ERR_FORM_FIELD_CHOICE'] = "Das Feld %s muss folgende Inhalte haben: <I>%s</I>";
$P2G_LANGUAGE['ERR_FORM_FIELD_CREDITCARD'] = "Das Feld %s keine korrekte %s Kreditkartennummer.";
$P2G_LANGUAGE['ERR_FORM_FIELD_MIN_LENGTH'] = "Das Feld %s muss wenigstens %s Zeichen beinhalten.";
$P2G_LANGUAGE['ERR_FORM_FIELD_MAX_LENGTH'] = "Das Feld %s darf nur %s Zeichen beinhalten.";
$P2G_LANGUAGE['ERR_FORM_FIELD_REQUIRED'] = "Das Feld %s ist  ein Pflichtfeld.";
$P2G_LANGUAGE['ERR_FORM_FIELD_REGEX'] = "Der Inhalt des Feldes %s ist inkorrekt!";
$P2G_LANGUAGE['ERR_FORM_FIELD_EQ'] = "Das Feld %s muss den gleichen Inhalt haben wie das Feld %s!"; 
$P2G_LANGUAGE['ERR_FORM_FIELD_NEQ'] = "Das Feld %s darf nicht den gleich Inhalt haben wie das Feld %s!"; 
$P2G_LANGUAGE['ERR_FORM_FIELD_GT'] = "Das Feld %s muss gr&ouml;&szlig;er als Feld %s sein!";
$P2G_LANGUAGE['ERR_FORM_FIELD_LT'] = "Das Feld %s muss kleiner als Feld %s sein!";
$P2G_LANGUAGE['ERR_FORM_FIELD_GOET'] = "Das Feld %s muss gr&ouml;&szlig;er oder gleich Feld %s sein!";
$P2G_LANGUAGE['ERR_FORM_FIELD_LOET'] = "Das Feld %s kleiner oder gleich Feld %s sein!";
$P2G_LANGUAGE['ERR_FORM_FIELD_VALUE_EQ'] = "Das Feld %s muss gleich %s sein!";
$P2G_LANGUAGE['ERR_FORM_FIELD_VALUE_NEQ'] = "Das Feld %s darf nicht gleich %s sein!";
$P2G_LANGUAGE['ERR_FORM_FIELD_VALUE_GT'] = "Das Feld %s muss gr&ouml;&szlig;er als %s sein!";
$P2G_LANGUAGE['ERR_FORM_FIELD_VALUE_LT'] = "Das Feld %s muss kleiner als %s sein!";
$P2G_LANGUAGE['ERR_FORM_FIELD_VALUE_GOET'] = "Das Feld %s muss gr&ouml;&szlig;er oder gleich %s sein!";
$P2G_LANGUAGE['ERR_FORM_FIELD_VALUE_LOET'] = "Das Feld %s muss kleiner oder gleich %s sein!";
$P2G_LANGUAGE['ERR_FORM_INVALID_MASK'] = "Die Maskierung <I>%s</I>, im Feld <I>%s</I> ist nicht korrekt!";
$P2G_LANGUAGE['ERR_FORM_INVALID_FIELDTYPE'] = "Der Feldtype <I>%s</I> ist nicht korrekt!";
$P2G_LANGUAGE['ERR_FORM_CAPTCHA'] = "Der Code der in das Feld %s eingegeben wurde, stimmt nicht mit dem auf dem Bild &uuml;berein!";
$P2G_LANGUAGE['ERR_DATAGRID_INVALID_FIELDTYPE'] = "Der Feldtype <I>%s</I> kann in einem <I>Netz</I> nicht benutzt werden!";
$P2G_LANGUAGE['ERR_DATAGRID_INVALID_CELLSIZES'] = "Der Wert vom <I>CELLSIZES</I> Attribut im Feld <I>%s</I> ist nicht korrekt. Sie m&uuml;ssen N+1 Gr&ouml;&szlig;en unterst&uuml;tzen (N=FIELDSET Gr&ouml;&szlig;e) und die Summe der Gr&ouml;&szligen muss 100 ergeben!";
$P2G_LANGUAGE['ERR_DATAGRID_INVALID_FIELDCOUNT'] = "Der Query der im <I>DATASOURCE</I> des Feldes <I>%s</I> defineirt wurde muss N+2 Spalten ergeben (N=FIELDSET Gr&ouml;&szlige)";
$P2G_LANGUAGE['ERR_FORM_WRONG_LISTENER'] = "Die Attribute des Event- Handlers <I>%s</I> sind nicht korrekt oder nicht komplett!";
$P2G_LANGUAGE['ERR_FORM_WRONG_RULE'] = "Die Attribute der Regel <I>%s</I> sin nicht korrekt oder nicht komplett!";
$P2G_LANGUAGE['ERR_INVALID_DATASOURCE_SYNTAX'] = "Die Spezifikation des Tags DATASOURCE im Feld <I>%s</I> ist nicht korrekt.<BR>Die Kindknoten KEYFIELD und LOOKUPTABLE sind Pflicht!";
$P2G_LANGUAGE['ERR_MISSING_GROUPFIELD_CHILDREN'] = "Die Spezifikation des Feldes <I>%s</I> ist unkomplett. Sie m&uuml;ssen zum Schluss &lt;OPTION&gt; Kind Knoten unterst&uuml;tzen!";
$P2G_LANGUAGE['ERR_MISSING_OPTION_VALUE'] = "Das VALUE Attribut der Option im Index <I>%d</I> des Feldes <I>%s</I> ist nicht angegeben!";
$P2G_LANGUAGE['ERR_MISSING_DBRADIOFIELD_DATASOURCE'] = "Es wurde kein DATASOURCE Tag definiert im DBRADIOFIELD <I>%s</I>!";
$P2G_LANGUAGE['ERR_EMPTY_DBRADIOFIELD_RESULTS'] = "Der Query im DATASOURCE Element des DBRADIOFIELD <I>%s</I> gibt einen leeren Datensatz zur&uuml;ck!";
$P2G_LANGUAGE['ERR_MISSING_COMBOOPTION_VALUE'] = "Das VALUE Attribut der Option im Index %d des COMBOFIELD <I>%s</I> ist nicht angegeben!";
$P2G_LANGUAGE['ERR_MISSING_EDITSELECTION_CHILDREN'] = "Die Spezifikation des EDITSELECTIONFIELD <I>%s</I> ist nicht komplett. Die EDITFIELD und LOOKUPFIELD Kindknoten sind erforderlich!";
$P2G_LANGUAGE['ERR_MISSING_LOOKUPSELECTION_CHILDREN'] = "Die Spezifikation des LOOKUPSELECTIONFIELD <I>%s</I> ist falsch. Der Knoten muss zwei LOOKUPFIELD Kinder haben!";
$P2G_LANGUAGE['ERR_MISSING_SOURCELOOKUP_DATASOURCE'] = "Es ist kein DATASOURCE definiert im ersten LOOKUPFIELD Kind des LOOKUPSELECTIONFIELD <I>%s</I>!";
$P2G_LANGUAGE['ERR_MISSING_QUERY_ELEMENTS'] = "Die \$fields und \$tables sind erforderlich in der SQL- Query Konstruktion!";
$P2G_LANGUAGE['ERR_INVALID_DATAGRID_STRUCTURE'] = "Die Struktur des Netzes <I>%s</I> ist nicht korrekt. Sie m&uuml;ssen ein <I>DATASOURCE</I>- Tag und ein <I>FIELDSET</I>- Tag definieren das mindestens ein Feld enth&auml;lt!";
$P2G_LANGUAGE['ERR_MISSING_RANGEFIELD_CHILDREN'] = "Die Spezifikation vom RANGEFIELD <I>%s</I> ist falsch. Der Knoten muss zwei EDITFIELD Kindknoten haben!";
$P2G_LANGUAGE['ERR_EDITSEARCH_INVALID_DATAFILTER'] = "Die Spezifikation des DATAFILTER <I>%s</I> ist nicht korrekt.";
$P2G_LANGUAGE['ERR_EDITSEARCH_INVALID_DATAFILTER_MASK'] = "Die zugewiesene Maske f&uuml;r den DATAFILTER <I>%s</I> ist nicht korrekt.";
$P2G_LANGUAGE['ERR_EDITSEARCH_DUPLICATED_DATAFILTER'] = "Ein anderer DATAFILTER mit der ID <I>%s</I> existiert bereits.";
$P2G_LANGUAGE['ERR_MISSING_EDITSEARCH_CHILDREN'] = "Die Spezifikation des EDITSEARCHFIELD <I>%s</I> ist nicht vollst&auml;ndig. Sie m&uuml;ssen als letztes DATAFILTER, des Typs DATASOURCE und ein LOOKUPFILED zuteilen.";
$P2G_LANGUAGE['ERR_CSV_DB_JSRS'] = "Die Operation konnte leider nicht beendet werden!";
$P2G_LANGUAGE['ERR_SEARCHFORM_EMPTY'] = "Die Suche konnte nicht ausgef�hrt werden, weil leider alle Felder leer sind.";
$P2G_LANGUAGE['ERR_SEARCHFORM_INVALID'] = "Die Suche konnte nicht ausgef�hrt werden, weil die Filtereinstellungen leer sind oder weniger als %d Zeichen besitzt.";
$P2G_LANGUAGE['ERR_REPORT_MAX_GROUP_COLS'] = "Die gruppierten Spalten m&uuml;ssen weniger sein als die Query Spalten!";
$P2G_LANGUAGE['ERR_REPORT_UNKNOWN_GROUP_COL'] = "Die gruppierte Spalte <I>%s</I> ist nicht im Query definiert!";
$P2G_LANGUAGE['ERR_REPORT_MISSING_BLOCK_VARIABLE'] = "Die Variable <I>%s</I> im Block <I>%s</I> wurde nicht im Template deklariert!";
$P2G_LANGUAGE['ERR_REPORT_INVALID_COLSIZES'] = "Der Wert <I>%s</I> ist keine korrekte Definition der Spaltengr&ouml;&szlig;e!";
$P2G_LANGUAGE['ERR_REPORT_COL_SIZES_SUM'] = "Die Summe der Spaltengr&ouml;&szlig;en die zur Funktion setColumnSizes muss 100 ergeben!";
$P2G_LANGUAGE['ERR_REPORT_COL_COUNT_MISMATCH'] = "Die Anzahl an Spaltengr&ouml;&szlig die (%s) nutzen ist nicht die gleiche Anzahl wie die Spalten vom Report (%s, %s gruppiert)";
$P2G_LANGUAGE['ERR_REPORT_MISSING_SEARCH_VARIABLE'] = "Das Suchformular konnte leider nicht erstellt werden, weil die Variable <I>%s</I> nicht gefunden wurde.<BR>Deklarieren Sie bitte {%s} im Template.";
$P2G_LANGUAGE['ERR_REPORT_SEARCH_PARS_MALFORMED'] = "Du musst den Text, den Feldnamen und die Art der Suche als Parameter, vor dem Suchen angeben!";
$P2G_LANGUAGE['ERR_REPORT_SEARCH_INVALID_MASK'] = "Die Maskierung <I>%s</I> des Suchparameters <I>#%d</I> ist nicht korrekt!";
$P2G_LANGUAGE['ERR_REPORT_MIN_ALT_STYLE'] = "Die setAlternateStyle Funktion muss zum Schluss zwei Parameter bekommen (CSS names)!";
$P2G_LANGUAGE['ERR_REPORT_HIDDEN_GROUP'] = "Eine versteckte Spalte (%s) kann nicht in den Grupenkopf eingef&uuml;gt werden!";
$P2G_LANGUAGE['ERR_REPORT_GROUP_HIDDEN'] = "Eine Spalte die im Gruppenkopf (%s) gezeigt wird, darf nicht als hidden deklariert werden!";
$P2G_LANGUAGE['ERR_REPORT_MAX_HIDDEN_COLS'] = "Die versteckten Spalten m&uuml;ssen weniger sein als der Query holt!";
$P2G_LANGUAGE['ERR_REPORT_UNKNOWN_HIDDEN_COL'] = "Die Spalte <I>%s</I> die als hidden definiert wurde, existiert nicht im Datensatz des Querys!";
$P2G_LANGUAGE['ERR_DATASET_INVALID_TYPE'] = "Der Type <I>%s</I> ist kein korrekter DataSet Type!";
$P2G_LANGUAGE['ERR_MENU_WRONG_ROOT_SQL'] = "Der \$rootSql- Query der als Parameter an die Menu::loadFromDatabase() Methode &uuml;bergeben wurde liefert ein leeres Ergebnis oder hat nicht mindestens die Felder (Index und Label)!";
$P2G_LANGUAGE['ERR_MENU_WRONG_CHILDREN_STATEMENT'] = "Der \$childSql Query der als Parameter an die Menu::loadFromDatabase() Methode &uuml;bergeben wurde ist nicht korrekt.<BR>Der Query muss ein WHERE Statment beinhalten das auf '~var~' (Suchkriterium) &uum;berpr&uuml;ft und die mindestens zwei Zeilen (Index and Label) enthalten!";
$P2G_LANGUAGE['ERR_MENU_NOT_FOUND'] = "Das Menu wurde nicht gebildet. Das Menu wirde durch Menu::loadFromDatabase() oder Menu::loadFromXmlFile() gebildet.";
$P2G_LANGUAGE['ERR_MENU_EMPTY_XML_ROOT'] = "Der Root Knoten der XML Datei hat keine Kindknoten!";
$P2G_LANGUAGE['ERR_MENU_INVALID_IMAGE'] = "Das <I>%s</I> Bild ist nicht korrekt. Die richtigen Werte sind: <I>%s</I>.";
$P2G_LANGUAGE['ERR_FTP_MISSING_HOST'] = "Die Verbindung konnte nicht aufgebaut werden. Sie m&uuml;ssen die Verbindung erst mit FtpClient::setServer() und FtpClient::setUserInfo() konfigurieren";
$P2G_LANGUAGE['ERR_FTP_MISSING_USER_OR_PASS'] = "Die Authentifizierung ist leider fehlgeschlagen. Einer der Daten (username oder password) passt nicht zusammen!";
$P2G_LANGUAGE['ERR_SPRSH_OUT_OF_BOUNDS'] = "Das Spreadsheet Limit ist erreichtt: [%d;%d]";
$P2G_LANGUAGE['ERR_SPRSH_MAX_NOTE_CHARS'] = "Die Maximum Gr&ouml;&szlige einer Zelle ist erreicht!<BR>Valor: %s";
$P2G_LANGUAGE['ERR_SPRSH_MAX_FONTS'] = "Das Maximum on 4 Schriftarten f&uuml;r Spreedseats sind erreicht!";
$P2G_LANGUAGE['ERR_UPLOAD_NOT_FOUND'] = "Der Upload konnte nicht beendet werden. Die Datei wurde nicht korrekt verschoben.";
$P2G_LANGUAGE['ERR_UPLOAD_TOO_BIG'] = "Der Upload konnte nicht beendet werden. Die Dateigr&ouml;&szlig;e ist zu gro&szlig;.";
$P2G_LANGUAGE['ERR_UPLOAD_INVALID_NAME'] = "Der Upload konnte nicht beendet werden. Der Dateiname %s ist nicht korrekt.";
$P2G_LANGUAGE['ERR_UPLOAD_INVALID_TYPE'] = "Der Upload konnte nicht beendet werden. Die Endung %s ist nicht erlaubt.";
$P2G_LANGUAGE['ERR_UPLOAD_FILE_EXISTS'] = "Der Upload konnte nicht beendet werden. Die Datei <I>%s</I> existiert bereits und wurde nicht &uuml;berschrieben.";
$P2G_LANGUAGE['ERR_UPLOAD_CANT_MOVE'] = "Es war nicht m&ouml;glich die Tempor&auml;re Datei ins Zielverzeichnis zu verschieben.";
$P2G_LANGUAGE['ERR_CANT_OPEN_SOCKET'] = "Es war nicht m&ouml;glich den Socket mit dem Port %s und dem Host %s zu &ouml;ffnen!<BR>Error %d - %s";
$P2G_LANGUAGE['ERR_SOCKET_NOT_CONNECTED'] = "Die Socketverbindung ist nicht aktiv!";
$P2G_LANGUAGE['ERR_SOCKET_EOF'] = "Ende der Datei erreicht, die Operation konnte nicht beendet werden!";
$P2G_LANGUAGE['ERR_SOCKET_TIMEOUT'] = "Der Socket- Timeout ist erreicht. Die Operation konnte nicht beendet werden!";
$P2G_LANGUAGE['ERR_MAIL_MESSAGE_NOT_BUILT'] = "Die Nachricht wurde nicht korrekt gebildet und konnte nicht gesendet werden!<BR>Benutzen Sie die build() Methode.";
$P2G_LANGUAGE['ERR_MAIL_EMPTY_RCPT'] = "Es gibt keine Empf&auml;nger f&uuml;r die Nachricht!";
$P2G_LANGUAGE['ERR_SIGNED_MESSAGE_SIGN'] = "Den Nachrichteninhaltz konnte nicht signiert werden.<BR>%s";
$P2G_LANGUAGE['ERR_SMTP_CONNECTION'] = "Fehler bei der Verbindung zum SMTP- Server %s!<BR>Errno: [%d]<BR>Errmsg: %s";
$P2G_LANGUAGE['ERR_SMTP_COMMAND'] = "Fehler beim ausf&uuml;hren des Kommandos %s zum SMTP- Server!<BR>Errno: [%d]<BR>Errmsg: %s";
$P2G_LANGUAGE['ERR_SMTP_AUTHENTICATE'] = "Es war nicht m&ouml;glich sich beim SMTP- Server zu authentifizieren!!";
$P2G_LANGUAGE['ERR_CRYPT_MISSING_KEY'] = "Um Daten zu entschl&uuml;sseln oder verschl&uuml;sseln m&uuml;ssen Sie ein Verschl&uuml;sselungsschl&uuml;ssel mit der Crypt::setKey() Methode erstellen!";
$P2G_LANGUAGE['ERR_CRYPT_OPEN_MODULE'] = "Die Ziffer %s konnte mit dem mode %s nicht innitiallisiert werden!";
$P2G_LANGUAGE['ERR_OPENSSL_PUBKEY_ENCRYPT'] = "Es war leider nicht m�glich einen &ouml;ffentlichen Schl�ssel zu zu holen um die Daten zu entschl�sseln. Besorgen Sie sich eine Datei wo ein ordentliches Zertifikat f�r die OpenSSLCrypt::setCertificatePath() Methode ist!";
$P2G_LANGUAGE['ERR_OPENSSL_PUBKEY_VERIFY'] = "Es war leider nicht m�glich einen &ouml;ffentlichen Schl�ssel zu zu holen um die Signatur in diesem Teil zu best�tigen. Besorgen Sie sich eine Datei wo ein ordentliches Zertifikat f�r die OpenSSLCrypt::setCertificatePath() Methode ist!";
$P2G_LANGUAGE['ERR_OPENSSL_PRIVKEY_DECRYPT'] = "Es war leider nicht m�glich den einen Schl&uuml;ssel zu holen um die Daten zu entschl&uuml;sseln. Besorgen Sie sich eine Datei wo der Schl&uuml;ssel und das Passwort drinstehen.";
$P2G_LANGUAGE['ERR_OPENSSL_PRIVKEY_SIGN'] = "Es war leider nicht m�glich den einen Schl&uuml;ssel zu holen um die digitale Unterschrift zu erstellen. Besorgen Sie sich eine Datei wo der Schl&uuml;ssel und das Passwort drinstehen.";
$P2G_LANGUAGE['ERR_OPENSSL_CERT_PATH'] = "Der Pfad zum Zertifikat ist nicht korrekt!";
$P2G_LANGUAGE['ERR_OPENSSL_READ_CERT'] = "Der Inhalt vom Zertifikat %s konnte nicht gelesen werden %s!";
$P2G_LANGUAGE['ERR_POP3_CONNECTION'] = "Fehler bei der Verbindung zum POP3- Server %s!<BR>Errno: [%d]<BR>- Errmsg: %s";
$P2G_LANGUAGE['ERR_POP3_COMMAND'] = "Fehler beim ausf&uuml;hren des Kommandos %s beim POP3- Server!<BR>Errmsg: %s";
$P2G_LANGUAGE['ERR_POP3_AUTHENTICATE'] = "Es war nicht m&ouml;glich sich beim POP3- Server zu authentifizieren!";
$P2G_LANGUAGE['ERR_POP3_APOP'] = "Das APOP Kommando konnte nicht ausgef&uuml;hrt werden : es ist kein Authentifikationsbanner vorhanden!";
$P2G_LANGUAGE['ERR_HTTP_CONNECT'] = "Eine HTTP Verbindung mit dem Host <I>%s</I> konnte nicht ausgef&uuml;hrt werden!";
$P2G_LANGUAGE['ERR_HTTP_MISSING_HOST'] = "Der HTTP- Host wurde nicht gesetzt. Benutzen Sie die HttpClient::setHost() Methode.";
$P2G_LANGUAGE['ERR_CANT_INSTANTIATE_VALIDATOR'] = "Die Vallidierungsklasse <B>%s</B> konnte nicht innitiallisiert werden!";
$P2G_LANGUAGE['ERR_INVALID_VALIDATOR'] = "Die Vallidierungsklasse <I>%s</I> ist nicht korrekt! Die %s::execute() Methode konnte nicht gefunden werden.";
$P2G_LANGUAGE['ERR_ASSERTION_MESSAGE'] = "Der angenommene Ausdruck bringt nicht das gew&uuml;nschte Resultat.";
$P2G_LANGUAGE['ERR_INVALID_CALLBACK'] = "Die Funktion oder Methode <I>%s</I> ist kein ordentliches Callback!";
$P2G_LANGUAGE['ERR_INVALID_PROPERTIES_FILE'] = "Die Datei %s enth&auml;lt leieder keine korrekten Konfigurationsdaten!";
$P2G_LANGUAGE['ERR_INVALID_COLOR_SPEC'] = "<I>%s</I> ist keine korrekte Farbdefinition!";
$P2G_LANGUAGE['ERR_ALLOCATE_COLOR'] = "Es war leider nicht m&ouml;glich eine neue Farbe f&uuml;r das Bild auszuw&auml;hlen!";
$P2G_LANGUAGE['ERR_JSRS_MISSING_HANDLER'] = "JSRS Handler Name oder Alias fehlt!";
$P2G_LANGUAGE['ERR_JSRS_INVALID_HANDLER'] = "Der JSRS Handler %s ist nicht korrekt!";
$P2G_LANGUAGE['DEFAULT_BTN_VALUE'] = "Abschicken";
$P2G_LANGUAGE['CALENDAR_LINK_TITLE'] = "&Ouml;ffne den Kalender";
$P2G_LANGUAGE['CALCULATOR_LINK_TITLE'] = "&Ouml;ffne den Taschenrechner";
$P2G_LANGUAGE['CALCULATOR_WINDOW_TITLE'] = "Taschenrechner";
$P2G_LANGUAGE['CALCULATOR_RESULT_CAPTION'] = "Resultat";
$P2G_LANGUAGE['DAYS_OF_WEEK'] = array("Sonntag","Montag","Dienstag","Mittwoch","Donnerstag","Freitag","Samstag");
$P2G_LANGUAGE['DAYS_OF_WEEK_ABBR'] = array("Son", "Mon", "Die", "Mit", "Don", "Fre", "Sam");
$P2G_LANGUAGE['MONTHS_OF_YEAR'] = array("Januar","Februar","M&auml;rz","April","Mai","Juni","Juli","August","September","Oktober","November","Dezember");
$P2G_LANGUAGE['MONTHS_OF_YEAR_ABBR'] = array("Jan", "Feb", "Mar", "Apr", "Mai", "Jun", "Jul", "Aug", "Sep", "Okt", "Nov", "Dez");
$P2G_LANGUAGE['COMPRESS_USE_MSG'] = "<!-- Nutzt %s Kompression -->";
$P2G_LANGUAGE['COMPRESS_DEBUG_MSG_ORIG'] = "Original Gr&ouml;&szlig;e: %d";
$P2G_LANGUAGE['COMPRESS_DEBUG_MSG_COMP'] = "Gepackte Gr&ouml;&szlig;e: %d";
$P2G_LANGUAGE['FORM_MASKS_DATA_LABEL'] = array(
	'CPFCNPJ' => 'CPF/CNPJ',
	'CURRENCY' => 'W&auml;hrung',
	'DATE' => 'Datum',
	'TIME' => 'Zeit',
	'EMAIL' => 'E-Mail',
	'FLOAT' => 'Flie&szlig;kommazahl',
	'INTEGER' => 'Ziffer',
	'IP' => 'IP Addresse',
	'LOGIN'=> 'Login oder Username',
	'URL' => 'URL/Internet Addresse',
	'ZIP' => 'PLZ'
);
$P2G_LANGUAGE['FORM_EDITOR_VARS'] = array(
	'paragraph' => 'Paragraph',
	'pnorm' => 'Normal',
	'ph1' => '&Uuml;berschrift 1 &lt;H1&gt;',
	'ph2' => '&Uuml;berschrift 2 &lt;H2&gt;',
	'ph3' => '&Uuml;berschrift 3 &lt;H3&gt;',
	'ph4' => '&Uuml;berschrift 4 &lt;H4&gt;',
	'ph5' => '&Uuml;berschrift 5 &lt;H5&gt;',
	'ph6' => '&Uuml;berschrift 6 &lt;H6&gt;',
	'paddr' => 'Addresse &lt;ADDR&gt;',
	'ppre' => 'Formatiert &lt;PRE&gt;',
	'prem' => 'L&ouml;sche Format',
	'bold' => 'Fett',
	'italic' => 'Kursiv',
	'under' => 'Unterstrichen',
	'cut' => 'Ausschneiden',
	'copy' => 'Kopieren',
	'paste' => 'Einf&uumk;gen',
	'fcolor' => '&Auml;ndere die Vordergrundfarbe',
	'bcolor' => '&Auml;ndere die Hintergrundfarbe',
	'emoticon' => 'Einen Smiley einf&uuml;gen',
	'font' => 'Schriftart',
	'fontsize' => 'Gr&ouml;&szlig;e',
	'left' => 'Links ausrichteb',
	'center' => 'Mittig ausrichten',
	'right' => 'Rechts ausrichten',
	'olist' => 'Einf&uuml;gen/ L&ouml;schen einer nummerischen Liste',
	'ulist' => 'Einf&uuml;gen/ L&ouml;schen einer normalen Liste',
	'indent+' => 'Einr&uuml;cken',
	'indent-' => 'Ausr&uuml;cken',
	'link' => 'Einen Link einf&uuml;gen',
	'image' => 'Ein Bild einf&uuml;gen',
    'editmode' => 'Im fortgeschrittenen Modus arbeiten',
	'choosesmil' => 'W&auuml;hle einen Smiley'
);
$P2G_LANGUAGE['LOOKUP_CHOICE_FILTER_TIP'] = "--- Die Liste filtern ---";
$P2G_LANGUAGE['LOOKUP_SELECTION_DEFAULT_SELFIRST'] = "--- Inhalt einf&uuml;gen ---";
$P2G_LANGUAGE['LOOKUP_SELECTION_BUTTON_TITLES'] = array(
	'add' => 'Selektion einf&uuml;gen',
	'addall' => 'Alles einf&uuml;gen',
	'rem' => 'Selektion l&ouml;schen',
	'remall' => 'L&ouml;sche alles'
);
$P2G_LANGUAGE['EDIT_SELECTION_BUTTON_TITLES'] = array(
	'add' => 'Hinzuf&uuml;gen',
    'rem' => 'Selektion l&ouml;schen',
    'remall' => 'Alles l&ouml;schen'
);
$P2G_LANGUAGE['MEMO_COUNT_LABEL'] = "Zeichen";                                                      
$P2G_LANGUAGE['SEL_AVAILABLE_VALUES_LABEL'] = "Vorhanden:";
$P2G_LANGUAGE['SEL_INSERTED_VALUES_LABEL'] = "Eingef&uuml;gt:";
$P2G_LANGUAGE['FORM_DATA_BIND_TOOLBAR_VALUES'] = array(
	'navFirstTip' => 'Gehe zum ersten Datensatz',
    'navPrevTip' => 'Gehe zum vorrigen Datensatz',
	'navNextTip' => 'Gehe zum n&auml;chsten Datensatz',
	'navLastTip' => 'Gehe zum letzten Datensatz',
	'actNew' => 'Neu',
	'actNewTip' => 'Erstelle einen neuen Datensatz',
	'actEdit' => 'Bearbeiten',
	'actEditTip' => 'Bearbeiten den aktuellen Datensatz',
	'actDel' => 'L&ouml;schen',
	'actDelTip' => 'L&ouml;sche den aktuellen Datensatz',
	'actSave' => 'Speichern',
	'actSaveTip' => 'Speicher den aktuellen Datensatz',
	'actCancel' => 'Abbruch',
	'actCancelTip' => 'Die aktuelle Aktion abbrechen',
	'sortAsc' => 'Aufsteigend sortieren',
	'sortDesc' => 'Absteigend sortieren',
	'sortChoose' => 'W&auuml;hle ein Feld das nach dem sortiert werden soll',
	'sortTit' => 'Sortiere',
	'sortFirst' => 'Sortier Felder',
	'gotoTip' => 'Provide the number of the record and click Go',
	'goto' => 'Los',
	'gotoBtnTip' => 'Click to go to the chosen record',
	'gotoTit' => 'Gehe zu',
	'filter' => 'Filter',
	'filterTip' => 'W&auml;hlen Sie das Filterfeld um es anzuwenden',
	'filterVTip' => 'W&auml;hlen Sie den Inhalt des Filter',
	'filterBtnTip' => 'Klicke hier um zu filtern',
	'filterTit' => 'Filter',
	'filterFirst' => 'Filter Felder'
);
$P2G_LANGUAGE['REPORT_EMPTY_VALUES'] = array(
	'emptyMsg' => 'Der Query oder die Suchkriterien lieferten kein Ergebnis.',
	'backLink' => 'Zur&uuml;ck'
);
$P2G_LANGUAGE['REPORT_ORDER_TIP'] = "Sortiere nach %s";                                             
$P2G_LANGUAGE['REPORT_FUNCTION_MESSAGES'] = array(
	'firstTit' => 'Erste Seite',
	'firstTip' => 'Gehe zur ersten Seite',
	'prevTit' => 'Zur&uuml;ck',
	'prevTip' => 'Gehe zur vorrigen Seite',
    'prevScrTit' => '%d Seiten zur&uuml;ck',
	'prevScrTip' => '%d Seiten zur&uuml;ck',
	'pageTip' => 'Gehe zur Seite #%d von %d',
	'nextTit' => 'N&auuml;chste',
	'nextTip' => 'Gehe zur n&auml;chsten Seite',
    'nextScrTit' => '%d Seiten vorw&auml;rts',
	'nextScrTip' => '%d Seiten vorw&auuml;rts',
    'lastTit' => 'Letzte Seite',
	'lastTip' => 'Gehe zur letzten Seite',
	'rowCount' => '%d Resultat(e) gefunden',
	'rowsPerPage' => '%d Resultat(e) pro Seite',
	'thisPage' => 'Seite %d von %d',
	'rowInterval' => 'Datensatz/Datens&auml;tze %d-%d von %d ',
	'goTo' => 'Gehe zu Seite'
);
$P2G_LANGUAGE['REPORT_SEARCH_VALUES'] = array(
	'searchTit' => 'Suche',
	'fieldFirst' => 'W&auml;hle ein Feld',
	'btnAdd' => 'Hinzuf&uuml;gen',
	'radioAny' => 'Irgendein Filter',
	'radioAll' => 'Alle Filter',
	'btnClear' => 'L&ouml;sche',
	'btnView' => 'Zeige aktuelle Filter',
	'btnSend' => 'Suche'
);
$P2G_LANGUAGE['REPORT_SEARCH_INI_OP'] = array(
	'=' => 'Gleich',
	'!=' => 'Nicht gleich',
	'LIKE' => 'Beinhaltet',
	'NOT LIKE' => 'Beinhaltet nicht',
	'LIKEI' => 'Beginnt mit',
	'LIKEF' => 'Endet mit'
);
return $P2G_LANGUAGE;											  
?>