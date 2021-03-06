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
// Translated and maintained by: Jiri Vasina <poutnik@users.sourceforge.net>
//
// $Header: /www/cvsroot/php2go/languages/czech.inc,v 1.23 2005/09/01 15:15:48 mpont Exp $
// $Revision: 1.23 $
// $Date: 2005/09/01 15:15:48 $

$P2G_LANGUAGE['ERR_OLD_PHP_VERSION'] = "PHP2Go nem&#367;&#382;e fungovat se sou&#269;asn&#253;m nastaven�m serveru.<BR>Va&#353;e verze PHP je: <I>%s</I>. Minim�ln� pot&#345;ebn� verze je <I>4.1.0</I>.<BR>Nainstalujte tuto nebo nov&#283;j&#353;� verzi PHP pro b&#283;&#382;nou pr�ci.";
$P2G_LANGUAGE['ERR_UNSUPPORTED_EXTENSION'] = "Roz&#353;�&#345;en� <I>%s</I> nen� podporov�no sou&#269;asnou instalac� PHP.";
$P2G_LANGUAGE['ERR_OBJ_MESSAGE'] = "Chyba ve t&#345;�d&#283; %s:<BR>%s";
$P2G_LANGUAGE['ERR_SCRIPT_MESSAGE'] = "Chyba ve skriptu:<BR>%s";
$P2G_LANGUAGE['ERR_DATABASE'] = "Chyba datab�ze";
$P2G_LANGUAGE['ERR_MISSING_DATABASE_PARAMETERS'] = "Nebylo mo&#383;n� nahr�t parametry pro spojen� s datab�z�! [ID: %s]";
$P2G_LANGUAGE['ERR_INVALID_DATABASE_PARAMETERS'] = "Parametry pro spojen� s datab�z� nejsou platn�! Polo&#383;ky USER, BASE a TYPE jsou povinn�. [ID: %s]";
$P2G_LANGUAGE['ERR_DATABASE_CONNECTION_FAILED'] = "Se sou&#269;asn&#253;m nastaven�m nebylo mo&#382;n� se p&#345;ipojit k datab�zi. Je toto nastaven� spr�vn�?";
$P2G_LANGUAGE['ERR_TYPES'] = array(
	1 => "Fat�ln� chyba",
	2 => "Upozorn&#283;n�",
	4 => "Chyba syntaxe",
	8 => "Pozn�mka",
	16 => "Fat�ln� chyba j�dra",
	32 => "Upozorn&#283;n� j�dra",
	64 => "Fat�ln� chyba p&#345;eklada&#269;e",
	128 => "Upozorn&#283;n� p&#345;eklada&#269;e",
	256 => "U&#382;ivatelsk� chyba",
	512 => "U&#382;ivatelsk� upozorn&#283;n�",
	1024 => "U&#382;ivatelsk� pozn�mka"
);
$P2G_LANGUAGE['ERR_ABSOLUTE_URI_NOT_FOUND'] = "Z�znam 'ABSOLUTE_URI' nebyl zad�n v konfigura&#269;n�m poli!";
$P2G_LANGUAGE['ERR_CONFIG_ENTRY_NOT_FOUND'] = "Z�znam <I>%s</I> nebyl v glob�ln�m konfigura&#269;n�m poli nastaven!";
$P2G_LANGUAGE['ERR_INVALID_LANGDOMAIN_FILE'] = "Soubor text&#367; pro dom�nu %s, jazyk %s, je neplatn� nebo neexistuje.";
$P2G_LANGUAGE['ERR_CANT_FIND_CFG_VAL'] = "Nem&#367;&#382;u naj�t z�znam <I>%s</I> v glob�ln� konfiguraci. Byl spr�vn&#283; nastaven p&#345;i instalaci?";
$P2G_LANGUAGE['ERR_CANT_LOAD_MODULE'] = "Nem&#367;&#382;u na&#269;�st modul <I>%s</I>";
$P2G_LANGUAGE['ERR_CANT_LOAD_DIR'] = "Nem&#367;&#382;u na&#269;�st moduly z adres�&#345;e <I>%s</I>";
$P2G_LANGUAGE['ERR_CANT_LOAD_DIR_MODULE'] = "Nem&#367;&#382;u na&#269;�st soubor <I>%s</I> na <I>%s</I>";
$P2G_LANGUAGE['ERR_CANT_CONVERT_DATE'] = "Nem&#367;&#382;u p&#345;ev�st datum na form�t %s.";
$P2G_LANGUAGE['ERR_CANT_RUN_ON_WINDOWS'] = "T&#345;�da  %s m&#367;&#382;e b&#253;t pou&#382;ita pod  Windows!";
$P2G_LANGUAGE['ERR_UNDEFINED_FUNCTION'] = "Funkce  %s nen� definov�na!";
$P2G_LANGUAGE['ERR_UNDEFINED_METHOD'] = "Metoda %s nen� definov�na ve t&#345;�d&#283; %s!";
$P2G_LANGUAGE['ERR_ABSTRACT_CLASS'] = "T&#345;�da  <I>%s</I> je abstraktn� a nem&#367;&#382;e b&#253;t vytvo&#345;ena jej� p&#345;�m� instance!";
$P2G_LANGUAGE['ERR_DATE_FORMAT'] = "Datum %s nen� v po&#382;adovan�m form�tu %s.";
$P2G_LANGUAGE['ERR_INVALID_RESOURCE'] = "Prom&#283;nn� %s p&#345;edan� funkci nebo metod&#283; %s nen� pou&#382;iteln&#253;m zdrojem.";
$P2G_LANGUAGE['ERR_INVALID_AUTHENTICATOR'] = "Autentik�tor %s nen� podt&#345;�dou php2go.net.Auth!";
$P2G_LANGUAGE['ERR_INVALID_AUTHENTICATOR_PATH'] = "Nebylo mo&#383;n� importovat nebo vytvo&#345;it autentik�tor %s!";
$P2G_LANGUAGE['ERR_INVALID_USERCONTAINER'] = "Spr�vce u&#383;ivatel&#367; %s nen� podt&#345;�dou php2go.auth.User!";
$P2G_LANGUAGE['ERR_INVALID_USERCONTAINER_PATH'] = "Nebylo mo&#383;n� importovat nebo vytvo&#345;it spr�vce u&#383;ivatel&#367; %s!";
$P2G_LANGUAGE['ERR_XML_PARSE'] = "P&#345;i proch�zen� souboru XML se vyskytla chyba:<BR>Chyba: %s<BR>&#345;�dek: %s, Sloupec: %s";
$P2G_LANGUAGE['ERR_STATEMENT_EVAL'] = "P&#345;i proch�zen� PHP k�du <I>%s</I> se vyskytla chyba";
$P2G_LANGUAGE['ERR_CANT_FIND_FILE'] = "Nebylo mo&#382;no nal�zt soubor nebo adres�&#345; %s. Existuje?";
$P2G_LANGUAGE['ERR_CANT_OPEN_FILE'] = "Nebylo mo&#382;no otev&#345;�t soubor %s v m�du %s. Zkontrolujte p&#345;�stupov� pr�va a pou&#382;it&#253; m�d.";
$P2G_LANGUAGE['ERR_CANT_CREATE_FILE'] = "Nebylo mo&#382;no vytvo&#345;it soubor nebo adres�&#345; %s. Zkontrolujte p&#345;�stupov� pr�va vy&#353;&#353;�ho adres�&#345;e.";
$P2G_LANGUAGE['ERR_CANT_READ_FILE'] = "Nebylo mo&#382;no otev&#345;�t soubor %s pro &#269;ten�. Existuje? Je spr�vn&#283; nastavena cesta?";
$P2G_LANGUAGE['ERR_CANT_WRITE_FILE'] = "Nebylo mo&#382;no otev&#345;�t soubor nebo adres�&#345; %s pro z�pis. M�te p&#345;�stupov� pr�va pro z�pis?";
$P2G_LANGUAGE['ERR_CANT_DELETE_FILE'] = "Nebylo mo&#382;no smazat soubor %s.<BR>M�te p&#345;�stupov� pr�va v tomto adres�&#345;i? M�te p&#345;�stupov� pr�va pro smaz�n� tohoto souboru?";
$P2G_LANGUAGE['ERR_CANT_INCLUDE_FILE'] = "Nebylo mo&#382;no otev&#345;�t soubor %s pro p&#345;ipojen� (\"inclusion\"). Existuje? Je spr�vn&#283; nastaven� cesta?";
$P2G_LANGUAGE['ERR_CANT_CHANGE_MODE'] = "Nebylo mo&#382;no nastavit m�d %s pro soubor nebo adres�&#345; %s.";
$P2G_LANGUAGE['ERR_CANT_READ_DIR'] = "Nebylo mo&#382;n� nal�zt adres�&#345; %s. Existuje?";
$P2G_LANGUAGE['ERR_CANT_OPEN_DIR'] = "Nebylo mo&#382;n� otev&#345;�t adres�&#345; %s pro &#269;ten�. Zkontrolujte nastaven� p&#345;�stupov&#253;ch pr�v.";
$P2G_LANGUAGE['ERR_CANT_FIND_SERIALIZATION_FILE'] = "Nebylo mo&#382;n� otev&#345;�t soubor obsahuj�c� serializovan&#253; objekt pro &#269;ten�. Je spr�vn&#283; nastaven� cesta? Byl soubor vytvo&#345;en?";
$P2G_LANGUAGE['ERR_EMPTY_INSERT_QUERY'] = "Dotaz vlo&#382;en� do datab�ze '%s' se zadan&#253;mi hodnotami vr�til pr�zdn&#253; z�znam.";
$P2G_LANGUAGE['ERR_EMPTY_UPDATE_QUERY'] = "Dotaz &#219;pravy z�znamu v datab�zi '%s' s p&#345;�kazem %s se zadan&#253;mi hodnotami vr�til pr�zdn&#253; z�znam.";
$P2G_LANGUAGE['ERR_EMPTY_TOGLOBALS_QUERY'] = "Dotaz %s, u&#382;it&#253; pro publikov�n� prom&#283;nn&#253;ch glob�ln&#283;, vr�til pr�zdn&#253; z�znam.";
$P2G_LANGUAGE['ERR_TOGLOBALS_WRONG_USAGE'] = "Dotaz p&#345;edn&#253; metod&#283; Db::toGlobals() mus� za&#269;�nat pomoc� 'SELECT' (DQL).";
$P2G_LANGUAGE['ERR_EMPTY_TEMPLATE_FILE'] = "Nebylo mo&#382;no inicializovat P&#345;edlohu s pr�zdn&#253;m jm�nem souboru!";
$P2G_LANGUAGE['ERR_EMPTY_TEMPLATE_CONTENT'] = "P&#345;edloha nebyla p&#345;ipravena, proto&#382;e je pr�zdn�. Zkontrolujte obsah p&#345;edan&#253; vytv�&#345;ec� metod&#283; (\"constructor\").";
$P2G_LANGUAGE['ERR_CANT_FIND_VARIABLE'] = "Nebylo mo&#382;n� naj�t prom&#283;nnou %s v p&#345;edloze <I>%s</I>. <BR>Pro spr�vn&#253; v&#253;stup, vlo&#382;te {%s} do k�du p&#345;edlohy.";
$P2G_LANGUAGE['ERR_CANT_FIND_BLOCK'] = "Nebylo mo&#382;n� naj�t blok <I>%s</I> pro vytvo&#345;en�. Byl deklarov�n v p&#345;edloze?";
$P2G_LANGUAGE['ERR_CANT_FIND_BLOCK_REUSE'] = "Nebylo mo&#382;n� naj�t blok %s pro op&#283;tovn&#283; pou&#382;it� jako %s.";
$P2G_LANGUAGE['ERR_CANT_PUT_ON_PREPARED_TEMPLATE'] = "Element %s byl ji&#382; konfigurov�n pomoc� metody DocumentElement::parse(). Nen� mo&#382;n� p&#345;id�vat elementy metodou put().";
$P2G_LANGUAGE['ERR_TEMPLATE_NOT_PREPARED'] = "P&#345;edloha %s nebyla p&#345;ipravena. Spus&#357;te metodu %s::parse(); p&#345;ed spou&#353;t&#283;n�m operac� na tomto objektu.";
$P2G_LANGUAGE['ERR_TEMPLATE_ALREADY_PREPARED'] = "Tato p&#345;edloha ji&#382; byla zpracov�na. Funkci parse() je mo&#382;n� volat pouze jednou.";
$P2G_LANGUAGE['ERR_UNBALANCED_BLOCKDEF'] = "Definice bloku <I>%s</I> nen� vyv�&#382;en�.";
$P2G_LANGUAGE['ERR_INVALID_MODIFIER'] = "Parametr %s nen� platn� nebo neexistuje!";
$P2G_LANGUAGE['ERR_EMPTY_DOC_LAYOUT'] = "V t�to p&#345;edloze nejsou definov�ny &#382;�dn� elementy.";
$P2G_LANGUAGE['ERR_CANT_REPLICATE_ROOT_BLOCK'] = "Z�kladn� blok v p&#345;edloze nem&#367;&#382;e b&#253;t p&#345;eps�n.";
$P2G_LANGUAGE['ERR_MUST_BE_POSITIVE'] = "Hodnota  %s ve funkci %s mus� b&#253;t v&#283;t&#353;� ne&#382; 0.";
$P2G_LANGUAGE['ERR_MUST_BE_AN_ARRAY'] = "Hodnota %s ve funkci %s mus� b&#253;t pole.";
$P2G_LANGUAGE['ERR_VALUE_OUT_OF_BOUNDS'] = "Hodnota '%s' je mimo povolen� hranice: %s a %s";
$P2G_LANGUAGE['ERR_URL_MALFORMED'] = "Hodnota <I>%s</I> nen� platnou URL!";
$P2G_LANGUAGE['ERR_HOST_INVALID'] = "<I>%s</I> nen� platn&#253;m hostitelem pro spojen�!";
$P2G_LANGUAGE['ERR_DNS_NOT_FOUND'] = "Nebylo mo&#382;n� naj�t platn� DNS pro dom�nu %s.";
$P2G_LANGUAGE['ERR_SESSION_PROPERTY_NOT_FOUND'] = "Vlastnost %s prom&#283;nn� sezen� %s nebyla nalezena. Spus&#357;te funkci createProperty()";
$P2G_LANGUAGE['ERR_SESSION_TIMECOUNTER_NOT_FOUND'] = "&#269;�ta&#269; &#269;asu %s prom&#283;nn� sezen� %s nebyl nalezen. Spus&#357;te funkci createTimeCounter()";
$P2G_LANGUAGE['ERR_INVALID_OBJECT'] = "Objekt p&#345;edan&#253; jako parametr nen� platnou instanc� t&#345;�dy <I>%s</I>!";
$P2G_LANGUAGE['ERR_CALCULATOR_MISSING_PARAMETERS'] = "Nedostatek parametr&#367; pro vytvo&#345;en� kalkula&#269;ky!";
$P2G_LANGUAGE['ERR_COLORSEL_MISSING_PARAMETERS'] = "Nedostatek parametr&#367; pro vytvo&#345;en� v&#253;b&#283;ru barev!";
$P2G_LANGUAGE['ERR_INVALID_FORM_METHOD'] = "Metoda %s nen� platn� pro odesl�n� formul�&#345;e '%s'";
$P2G_LANGUAGE['ERR_INVALID_VALIDATE_FUNC'] = "<I>%s</I> nen� platn&#253;m jm�nem pro valida&#269;n� funkci!";
$P2G_LANGUAGE['ERR_FORM_DUPLICATED_FIELD'] = "Pole <I>%s</I> bylo ve formul�&#345;i <I>%s</I> ji&#382; definov�no!";
$P2G_LANGUAGE['ERR_FORM_DUPLICATED_BUTTON'] = "The button <I>%s</I> is already defined in the form <I>%s</I>!";
$P2G_LANGUAGE['ERR_FORM_DUPLICATED_SECTION'] = "ID sekce <I>%s</I> bylo ve formul�&#345;i <I>%s</I> ji&#382; definov�no!";
$P2G_LANGUAGE['ERR_FORM_SECTION_EVALFUNC'] = "Nebylo mo&#382;n� definovat viditelnost sekce <I>%s</I>, proto&#382;e funkce <I>%s</I> nen� definov�na!";
$P2G_LANGUAGE['ERR_FORM_SECTION_TPLBLOCK'] = "Sekce <I>%s</I> je podm�n&#283;n�. Mus�te deklarovat blok jm�nem <I>%s</I> v p&#345;edloze obsahuj�c� pole sekce!";
$P2G_LANGUAGE['ERR_FORM_FIELD_TITLE'] = "Formul�&#345; obsahuje n�sleduj�c� chyby:";
$P2G_LANGUAGE['ERR_FORM_FIELD_INVALID_INPUT'] = "Pole %s obsahuje neplatnou hodnotu %s.";
$P2G_LANGUAGE['ERR_FORM_FIELD_INVALID_FLOAT'] = "Numerick� p&#345;esnost pole %s je neplatn�.\\nMaxim�ln� velikost je %d pro celou &#269;�st a %d pro desetinnou &#269;�st.";
$P2G_LANGUAGE['ERR_FORM_FIELD_ALPHANUM'] = "Pole %s mus� obsahovat pouze p�smena abecedy a &#269;�sla.";
$P2G_LANGUAGE['ERR_FORM_FIELD_CHOICE'] = "Pole %s mus� m�t jednu z n�sleduj�c�ch hodnot: <I>%s</I>";
$P2G_LANGUAGE['ERR_FORM_FIELD_CREDITCARD'] = "Pole %s obsahuje neplatn� &#269;�slo kreditn� karty %s.";
$P2G_LANGUAGE['ERR_FORM_FIELD_MIN_LENGTH'] = "Pole %s mus� m�t alespo&#328; %s znak&#367;.";
$P2G_LANGUAGE['ERR_FORM_FIELD_MAX_LENGTH'] = "Pole %s mus� m�t maxim�ln&#283; %s znak&#367;.";
$P2G_LANGUAGE['ERR_FORM_FIELD_REQUIRED'] = "Pole %s je povinn�.";
$P2G_LANGUAGE['ERR_FORM_FIELD_REGEX'] = "Pole %s m� neplatnou hodnotu!";
$P2G_LANGUAGE['ERR_FORM_FIELD_EQ'] = "Pole %s se mus� rovnat poli %s!";
$P2G_LANGUAGE['ERR_FORM_FIELD_NEQ'] = "Pole %s se nesm� rovnat poli %s!";
$P2G_LANGUAGE['ERR_FORM_FIELD_GT'] = "Pole %s mus� b�t v&#283;t&#353;� ne&#382; pole %s!";
$P2G_LANGUAGE['ERR_FORM_FIELD_LT'] = "Pole %s mus� b�t men&#353;� ne&#382; pole %s!";
$P2G_LANGUAGE['ERR_FORM_FIELD_GOET'] = "Pole %s mus� b�t v&#283;t&#353;� nebo rovno poli %s!";
$P2G_LANGUAGE['ERR_FORM_FIELD_LOET'] = "Pole %s mus� b�t men&#353;� nebo rovno poli %s!";
$P2G_LANGUAGE['ERR_FORM_FIELD_VALUE_EQ'] = "Pole %s mus� b�t rovno %s!";
$P2G_LANGUAGE['ERR_FORM_FIELD_VALUE_NEQ'] = "Pole %s nesm� b�t rovno %s!";
$P2G_LANGUAGE['ERR_FORM_FIELD_VALUE_GT'] = "Pole %s mus� b�t v&#283;t&#353;� ne&#382; %s!";
$P2G_LANGUAGE['ERR_FORM_FIELD_VALUE_LT'] = "Pole %s mus� b�t men&#353;� ne&#382; %s!";
$P2G_LANGUAGE['ERR_FORM_FIELD_VALUE_GOET'] = "Pole %s mus� b�t v&#283;t&#353;� nebo rovno %s!";
$P2G_LANGUAGE['ERR_FORM_FIELD_VALUE_LOET'] = "Pole %s mus� b�t men&#353;� nebo rovno %s!";
$P2G_LANGUAGE['ERR_FORM_INVALID_MASK'] = "Maska <I>%s</I>, pro pole <I>%s</I>, nen� platnou maskou!";
$P2G_LANGUAGE['ERR_FORM_INVALID_FIELDTYPE'] = "Pole typu <I>%s</I> nen� platn�!";
$P2G_LANGUAGE['ERR_FORM_CAPTCHA'] = "K�d zadan� v poli %s nesouhlas� s obsahem p&#345;edlohy!";
$P2G_LANGUAGE['ERR_DATAGRID_INVALID_FIELDTYPE'] = "Pole typu <I>%s</I> nem&#367;&#382;e b&#253;t v r�mci <I>tabulky (\"grid\")</I>!";
$P2G_LANGUAGE['ERR_DATAGRID_INVALID_CELLSIZES'] = "Hodnoty parametru <I>CELLSIZES</I> pole <I>%s</I> nen� platn�. Mus�te zadat N+1 velikost� (N=velikost FIELDSET) a sou&#269;et velikost� mus� b&#253;t 100!";
$P2G_LANGUAGE['ERR_DATAGRID_INVALID_FIELDCOUNT'] = "Dotaz definovan&#253; v <I>DATASOURCE</I> pole <I>%s</I> mus� vracet N+2 sloupc&#367; (N=velikost FIELDSET)";
$P2G_LANGUAGE['ERR_FORM_WRONG_LISTENER'] = "Atributy zpracovavatele ud�lost� <I>%s</I> jsou neplatn� nebo nekompletn�!";
$P2G_LANGUAGE['ERR_FORM_WRONG_RULE'] = "Atributy pravidla <I>%s</I> jsou neplatn� nebo nekompletn�!";
$P2G_LANGUAGE['ERR_INVALID_DATASOURCE_SYNTAX'] = "Specifikace DATASOURCE v poli <I>%s</I> nen� platn�.<BR>Dce&#345;inn� body KEYFIELD a LOOKUPTABLE jsou povinn�!";
$P2G_LANGUAGE['ERR_MISSING_GROUPFIELD_CHILDREN'] = "Specifikace pole <I>%s</I> nen� kompletn�. Mus�te zadat alespo&#328; jeden &lt;OPTION&gt; dce&#345;inn&#253; bod!";
$P2G_LANGUAGE['ERR_MISSING_OPTION_VALUE'] = "Atribut VALUE v&#253;b&#283;ru s indexem <I>%d</I> v poli <I>%s</I> chyb�!";
$P2G_LANGUAGE['ERR_MISSING_DBRADIOFIELD_DATASOURCE'] = "Nen� definov�n DATASOURCE pro DBRADIOFIELD <I>%s</I>!";
$P2G_LANGUAGE['ERR_EMPTY_DBRADIOFIELD_RESULTS'] = "Dotaz pro DATASOURCE element v DBRADIOFIELD <I>%s</I> vr�til pr�zdnou sadu!";
$P2G_LANGUAGE['ERR_MISSING_COMBOOPTION_VALUE'] = "Atribut VALUE v&#253;b&#283;ru s indexem %d v COMBOFIELD <I>%s</I> chyb�!";
$P2G_LANGUAGE['ERR_MISSING_EDITSELECTION_CHILDREN'] = "Specifikace EDITSELECTIONFIELD <I>%s</I> nen� kompletn�. EDITFIELD a LOOKUPFIELD dce&#345;inn� body jsou povinn�!";
$P2G_LANGUAGE['ERR_MISSING_LOOKUPSELECTION_CHILDREN'] = "Specifikace LOOKUPSELECTIONFIELD <I>%s</I> je &#353;patn�. Bod mus� obsahovat 2 potomky LOOKUPFIELD!";
$P2G_LANGUAGE['ERR_MISSING_SOURCELOOKUP_DATASOURCE'] = "DATASOURCE je definov�n v prvn�m LOOKUPFIELD potomku LOOKUPSELECTIONFIELD <I>%s</I>!";
$P2G_LANGUAGE['ERR_MISSING_QUERY_ELEMENTS'] = "\$fields a \$tables polo&#382;ky jsou povinn� pro vytvo&#345;en� SQL dotazu!";
$P2G_LANGUAGE['ERR_INVALID_DATAGRID_STRUCTURE'] = "Struktura m&#345;�&#382;ky <I>%s</I> nen� platn�. Mus�te deklarovat <I>DATASOURCE</I> a <I>FIELDSET</I> obsahuj�c� alespo&#328; jedno pole!";
$P2G_LANGUAGE['ERR_MISSING_RANGEFIELD_CHILDREN'] = "Specifikace RANGEFIELD <I>%s</I> je &#353;patn�. Bod mus� obsahovat 2 potomky EDITFIELD!";
$P2G_LANGUAGE['ERR_EDITSEARCH_INVALID_DATAFILTER'] = "Specifikace <I>%s</I> pro DATAFILTER je neplatn�.";
$P2G_LANGUAGE['ERR_EDITSEARCH_INVALID_DATAFILTER_MASK'] = "Maska <I>%s</I> p&#345;i&#345;azen� pro DATAFILTER je neplatn�.";
$P2G_LANGUAGE['ERR_EDITSEARCH_DUPLICATED_DATAFILTER'] = "Jin� DATAFILTER s ID <I>%s</I> ji&#383; existuje.";
$P2G_LANGUAGE['ERR_MISSING_EDITSEARCH_CHILDREN'] = "Specifikace <I>%s</I> pro EDITSEARCHFIELD nen� kompletn�. Mus�te zadat alespo&#328; jedno z pol� DATAFILTER, DATASOURCE nebo LOOKUPFIELD!";
$P2G_LANGUAGE['ERR_CSV_DB_JSRS'] = "Nebylo mo&#382;n� dokon&#269;it operaci!";
$P2G_LANGUAGE['ERR_SEARCHFORM_EMPTY'] = "Vyhled�v�n� nemohlo b�t provedeno, proto&#382;e pole pro vyhled�v�n� jsou pr�zdn�.";
$P2G_LANGUAGE['ERR_SEARCHFORM_INVALID'] = "Vyhled�v�n� nemohlo b�t provedeno, proto&#382;e hodnoty filtr&#367; jsou pr�zdn� nebo krat&#353;� ne&#382; %d znak&#367;.";
$P2G_LANGUAGE['ERR_REPORT_MAX_GROUP_COLS'] = "Po&#269;et sloupc&#367; skupiny mus� b&#253;t men&#353;� ne&#382; po&#269;et sloupc&#367; dotazu!";
$P2G_LANGUAGE['ERR_REPORT_UNKNOWN_GROUP_COL'] = "Skupina sloupc&#367; <I>%s</I> nen� definov�na ve v&#253;sledn�m z�znamu dotazu!";
$P2G_LANGUAGE['ERR_REPORT_MISSING_BLOCK_VARIABLE'] = "Prom&#283;nn� <I>%s</I> bloku <I>%s</I> nebyla deklarov�na v p&#345;edloze!";
$P2G_LANGUAGE['ERR_REPORT_INVALID_COLSIZES'] = "Hodnota <I>%s</I> nen� platn� pro definici &#353;�&#345;ky sloupce!";
$P2G_LANGUAGE['ERR_REPORT_COL_SIZES_SUM'] = "Sou&#269;et velikost� sloupc&#367; p&#345;edan&#253;ch funkci setColumnSizes mus� b&#253;t roven 100!";
$P2G_LANGUAGE['ERR_REPORT_COL_COUNT_MISMATCH'] = "Po&#269;et zadan&#253;ch velikost� sloupc&#367; (%s) nen� roven po&#269;tu sloupc&#367; v reportu (%s, %s skupina)";
$P2G_LANGUAGE['ERR_REPORT_MISSING_SEARCH_VARIABLE'] = "Nebylo mo&#382;n� vytvo&#345;it formul�&#345; vyhled�v�n�, proto&#382;e prom&#283;nn� <I>%s</I> nebyla nalezena.<BR>Deklarujte {%s} v p&#345;edloze reportupro pokra&#269;ov�n�.";
$P2G_LANGUAGE['ERR_REPORT_SEARCH_PARS_MALFORMED'] = "Mus�te poskytnout N�zev (\"Label\"), N�zev pole (\"Field Name\") a Masku (\"Mask\") p&#345;i vytv�&#345;en� parametr&#367; vyhled�v�n�!";
$P2G_LANGUAGE['ERR_REPORT_SEARCH_INVALID_MASK'] = "Maska <I>%s</I> pro parametry vyhled�v�n� <I>#%d</I> nen� platn�!";
$P2G_LANGUAGE['ERR_REPORT_MIN_ALT_STYLE'] = "Funkce setAlternateStyle mus� obdr&#382;et alespo&#328; 2 parametry (jm�na CSS)!";
$P2G_LANGUAGE['ERR_REPORT_HIDDEN_GROUP'] = "Skryt&#253; sloupec (%s) nem&#367;&#382;e b&#253;t pou&#382;it ve skupin&#283; hlavi&#269;ek!";
$P2G_LANGUAGE['ERR_REPORT_GROUP_HIDDEN'] = "Sloupec zobrazen&#253; ve skupin&#283; hlavi&#269;ek (%s) nem&#367;&#382;e b&#253;t definov�n jako skryt&#253;!";
$P2G_LANGUAGE['ERR_REPORT_MAX_HIDDEN_COLS'] = "Po&#269;et skryt&#253;ch sloupc&#367; mus� b&#253;t men&#353;� ne&#382; po&#269;et sloupc&#367; dotazu!";
$P2G_LANGUAGE['ERR_REPORT_UNKNOWN_HIDDEN_COL'] = "Sloupec <I>%s</I> definovan&#253; jako skryt&#253; neexistuje ve v&#253;sledkov�m z�znamu dotazu!";
$P2G_LANGUAGE['ERR_DATASET_INVALID_TYPE'] = "Typ <I>%s</I> nen� platn&#253;m typem DataSet!";
$P2G_LANGUAGE['ERR_MENU_WRONG_ROOT_SQL'] = "Dotaz ulo&#382;en&#253; parametru \$rootSql metody Menu::loadFromDatabase() vr�til pr�zdn&#253; z�znam nebo nem� minim�ln� po&#382;adovan&#253; po&#269;et pol� (Index a Label)!";
$P2G_LANGUAGE['ERR_MENU_WRONG_CHILDREN_STATEMENT'] = "Dotaz p&#345;edan&#253; parametru \$childSql metody Menu::loadFromDatabase() je neplatn&#253;.<BR>Zadejte dotaz obsahuj�c� '~var~' (Sloupec vztah&#367;) a dal&#353;� 2 po&#382;adovan� pole (Index and Label)";
$P2G_LANGUAGE['ERR_MENU_NOT_FOUND'] = "Menu nebylo vytvo&#345;eno. Vytvo&#345;te polo&#382;ky menu pomoc� Menu::loadFromDatabase() nebo Menu::loadFromXmlFile().";
$P2G_LANGUAGE['ERR_MENU_EMPTY_XML_ROOT'] = "The root node of the XML file has no children!";
$P2G_LANGUAGE['ERR_MENU_INVALID_IMAGE'] = "Obr�zek <I>%s</I> nen� platn&#253;. Mo&#382;n� hodnoty jsou: <I>%s</I>.";
$P2G_LANGUAGE['ERR_FTP_MISSING_HOST'] = "Nebylo mo&#382;n� otev&#345;�t spojen�. Mus�te nejd&#345;�v nastavit spojen�, pou&#382;ijte metody FtpClient::setServer() a FtpClient::setUserInfo()";
$P2G_LANGUAGE['ERR_FTP_MISSING_USER_OR_PASS'] = "Nebylo mo&#382;n� ov&#283;&#345;it u&#382;ivatele. Jeden ze zadan&#253;ch parametr&#367; (u&#382;ivatelsk� jm�no nebo heslo) nebylo poskytnuto";
$P2G_LANGUAGE['ERR_SPRSH_OUT_OF_BOUNDS'] = "Omezen� tabulky byly p&#345;ekro&#269;eny: [%d;%d]";
$P2G_LANGUAGE['ERR_SPRSH_MAX_NOTE_CHARS'] = "Maxim�ln� velikost pro pozn�mku bu&#328;ky byla p&#345;ekro&#269;ena.<BR>Hodnota: %s";
$P2G_LANGUAGE['ERR_SPRSH_MAX_FONTS'] = "Maximum 4 p�sem pro ka&#382;dou tabulku bylo p&#345;ekro&#269;eno!";
$P2G_LANGUAGE['ERR_UPLOAD_NOT_FOUND'] = "Nebylo mo&#382;n� dokon&#269;it nahr�n� souboru. Soubor nebyl spr�vn&#283; p&#345;enesen.";
$P2G_LANGUAGE['ERR_UPLOAD_TOO_BIG'] = "Nebylo mo&#382;n� dokon&#269;it nahr�n� souboru. Velikost souboru je v&#283;t&#353;� ne&#382; povolen� maximum.";
$P2G_LANGUAGE['ERR_UPLOAD_INVALID_NAME'] = "Nebylo mo&#382;n� dokon&#269;it nahr�n� souboru. Jm�no souboru %s nen� platn�.";
$P2G_LANGUAGE['ERR_UPLOAD_INVALID_TYPE'] = "Nebylo mo&#382;n� dokon&#269;it nahr�n� souboru. P&#345;�pona %s nen� povolena.";
$P2G_LANGUAGE['ERR_UPLOAD_FILE_EXISTS'] = "Nebylo mo&#382;n� dokon&#269;it nahr�n� souboru. Soubor <I>%s</I> ji&#382; existuje a nebyl p&#345;eps�n.";
$P2G_LANGUAGE['ERR_UPLOAD_CANT_MOVE'] = "Nebylo mo&#382;n� p&#345;esunout soubor z do&#269;asn�ho adres�&#345;e do ur&#269;en�ho adres�&#345;e.";
$P2G_LANGUAGE['ERR_CANT_OPEN_SOCKET'] = "Nebylo mo&#382;n� otev&#345;it socket za pou&#382;it� portu %s s hostitelem %s!<BR>Chyba %d - %s";
$P2G_LANGUAGE['ERR_SOCKET_NOT_CONNECTED'] = "Spojen� za pou&#382;it� socketu nen� aktivn�!";
$P2G_LANGUAGE['ERR_SOCKET_EOF'] = "Dosa&#382;en konec souboru, operace nemohla b&#253;t dokon&#269;ena!";
$P2G_LANGUAGE['ERR_SOCKET_TIMEOUT'] = "Vypr&#353;el &#269;as socketu. Operace nem&#367;&#382;e b&#253;t dokon&#269;ena!";
$P2G_LANGUAGE['ERR_MAIL_MESSAGE_NOT_BUILT'] = "Zpr�va nebyla spr�vn&#283; vytvo&#345;ena a nem&#367;&#382;e b&#253;t odesl�na!<BR>Spus&#357;te metodu build().";
$P2G_LANGUAGE['ERR_MAIL_EMPTY_RCPT'] = "Nejsou zad�ni &#382;�dn� adres�ti pro zpr�vu!";
$P2G_LANGUAGE['ERR_SIGNED_MESSAGE_SIGN'] = "Nebylo mo&#382;n� podepsat obsah zpr�vy.<BR>%s";
$P2G_LANGUAGE['ERR_SMTP_CONNECTION'] = "Chyba p&#345;i spojov�n� se serverem SMTP %s!<BR>&#269;�slo chyby: [%d]<BR>Text chyby: %s";
$P2G_LANGUAGE['ERR_SMTP_COMMAND'] = "Chyba p&#345;i vykon�v�n� p&#345;�kazu %s SMTP serverem!<BR>&#269;�slo chyby: [%d]<BR>Text chyby: %s";
$P2G_LANGUAGE['ERR_SMTP_AUTHENTICATE'] = "Nebylo mo&#382;n� ov&#283;&#345;it u&#382;ivatele se serverem SMTP!";
$P2G_LANGUAGE['ERR_CRYPT_MISSING_KEY'] = "Pro za&#353;ifrov�n� nebo od&#353;ifrov�n� dat mus�te poskytnout &#353;ifrovac� kl�&#269; metod&#283; Crypt::setKey()!";
$P2G_LANGUAGE['ERR_CRYPT_OPEN_MODULE'] = "Nebylo mo&#382;n� inicializovat &#353;ifrov�n� <I>%s</I> v modu %s!";
$P2G_LANGUAGE['ERR_OPENSSL_PUBKEY_ENCRYPT'] = "Nebylo mo&#382;n� pou&#382;�t ve&#345;ejnou &#269;�st poskytnut�ho kl�&#269;e pro za&#353;ifrov�n� dat. Poskytn&#283;te soubor obsahuj�c� platn&#253; certifik�t metod&#283; OpenSSLCrypt::setCertificatePath()!";
$P2G_LANGUAGE['ERR_OPENSSL_PUBKEY_VERIFY'] = "Nebylo mo&#382;n� pou&#382;�t ve&#345;ejnou &#269;�st poskytnut�ho kl�&#269;e k ov&#283;&#345;en� podpisu dat. Poskytn&#283;te soubor obsahuj�c� platn&#253; certifik�t metod&#283; OpenSSLCrypt::setCertificatePath()!";
$P2G_LANGUAGE['ERR_OPENSSL_PRIVKEY_DECRYPT'] = "Nebylo mo&#382;n� pou&#382;�t soukromou &#269;�st poskytnut�ho kl�&#269;e k de&#353;ifrov�n� dat. Poskytn&#283;te soubor obsahuj�c� platn&#253; soukrom&#253; kl�&#269; a ov&#283;&#345;ovac� fr�zi, je-li pot&#345;eba.";
$P2G_LANGUAGE['ERR_OPENSSL_PRIVKEY_SIGN'] = "Nebylo mo&#382;n� pou&#382;�t soukromou &#269;�st poskytnut�ho kl�&#269;e pro vytvo&#345;en� digit�ln�ho podpisu. Poskytn&#283;te soubor obsahuj�c� platn&#253; soukrom&#253; kl�&#269; a ov&#283;&#345;ovac� fr�zi, je-lipot&#345;eba.";
$P2G_LANGUAGE['ERR_OPENSSL_CERT_PATH'] = "Cesta k certifik�tu je neplatn�!";
$P2G_LANGUAGE['ERR_OPENSSL_READ_CERT'] = "Nebylo mo&#382;n� p&#345;e&#269;�st obsah certifik�tu!%s";
$P2G_LANGUAGE['ERR_POP3_CONNECTION'] = "Chyba p&#345;i spojov�n� se serverem POP3 %s!<BR>&#269;�slo chyby: [%d]<BR>- Text chyby: %s";
$P2G_LANGUAGE['ERR_POP3_COMMAND'] = "Chyba p&#345;i spou&#353;t&#283;n� p&#345;�kazu %s serverem POP3!<BR>Text chyby: %s";
$P2G_LANGUAGE['ERR_POP3_AUTHENTICATE'] = "Nebylo mo&#382;n� ov&#283;&#345;it u&#382;ivatele serverem POP3!";
$P2G_LANGUAGE['ERR_POP3_APOP'] = "Nebylo mo&#382;n� prov�st p&#345;�kaz APOP: nen� ulo&#382;eno &#382;�dn� ov&#283;&#345;en�!";
$P2G_LANGUAGE['ERR_HTTP_CONNECT'] = "Nebylo mo&#382;n� vytvo&#345;it HTTP spojen� s hostitelem <I>%s</I>!";
$P2G_LANGUAGE['ERR_HTTP_MISSING_HOST'] = "Hostitel HTTP nebyl nastaven. Spus&#357;te metodu HttpClient::setHost().";
$P2G_LANGUAGE['ERR_CANT_INSTANTIATE_VALIDATOR'] = "Nebylo mo&#382;n� vytvo&#345;it ov&#283;&#345;ovac� t&#345;�du <B>%s</B>!";
$P2G_LANGUAGE['ERR_INVALID_VALIDATOR'] = "Ov&#283;&#345;ovac� t&#345;�da <I>%s</I> nen� platn�! Metoda %s::execute() nebyla nalezena.";
$P2G_LANGUAGE['ERR_ASSERTION_MESSAGE'] = "Zadan&#253;m p&#345;�kazem nebyl z�sk�n o&#269;ek�van&#253; v&#253;sledek.";
$P2G_LANGUAGE['ERR_INVALID_CALLBACK'] = "Funkce nebo metoda <I>%s</I> nen� platn�m zp&#283;tn�m odkazem!";
$P2G_LANGUAGE['ERR_INVALID_PROPERTIES_FILE'] = "Soubor <I>%s</I> neobsahuje platn� konfigura&#269;n� data!";
$P2G_LANGUAGE['ERR_INVALID_COLOR_SPEC'] = "<I>%s</I> nen� platnou specifikac� barvy."; 
$P2G_LANGUAGE['ERR_ALLOCATE_COLOR'] = "Nebylo mo&#382;n� alokovat (vytvo&#345;it) novou barvu pro tento obr�zek!"; 
$P2G_LANGUAGE['ERR_JSRS_MISSING_HANDLER'] = "Chyb&#283;j�c� jm�no JSRS ovlada&#269;e nebo z�stupce!";
$P2G_LANGUAGE['ERR_JSRS_INVALID_HANDLER'] = "JSRS ovlada&#269; %s je neplatn�!";
$P2G_LANGUAGE['DEFAULT_BTN_VALUE'] = "Poslat";
$P2G_LANGUAGE['CALENDAR_LINK_TITLE'] = "Otev&#345;�t kalend�&#345;";
$P2G_LANGUAGE['CALCULATOR_LINK_TITLE'] = "Otev&#345;�t kalkula&#269;ku";
$P2G_LANGUAGE['CALCULATOR_WINDOW_TITLE'] = "Kalkula&#269;ka";
$P2G_LANGUAGE['CALCULATOR_RESULT_CAPTION'] = "V&#253;sledek";
$P2G_LANGUAGE['DAYS_OF_WEEK'] = array("Ned&#283;le", "Pond&#283;l�", "&#218;ter&#253;", "St&#345;eda", "&#269;tvrtek", "P�tek", "Sobota");
$P2G_LANGUAGE['DAYS_OF_WEEK_ABBR'] = array("Ne", "Po", "&#218;t", "St", "&#269;t", "P�", "So");
$P2G_LANGUAGE['MONTHS_OF_YEAR'] = array("Leden", "&#218;nor", "B&#345;ezen", "Duben", "Kv&#283;ten", "&#269;erven", "&#269;ervenec", "Srpen", "Z�&#345;�", "&#345;�jen", "Listopad", "Prosinec");
$P2G_LANGUAGE['MONTHS_OF_YEAR_ABBR'] = array("Led", "&#218;no", "B&#345;e", "Dub", "Kv&#283;", "&#269;en", "&#269;ec", "Srp", "Z�&#345;", "&#345;�j", "Lis", "Pro");
$P2G_LANGUAGE['COMPRESS_USE_MSG'] = "<!-- Uses %s Compression -->";
$P2G_LANGUAGE['COMPRESS_DEBUG_MSG_ORIG'] = "P&#367;vodn� velikost: %d";
$P2G_LANGUAGE['COMPRESS_DEBUG_MSG_COMP'] = "Pakovan� velikost: %d";
$P2G_LANGUAGE['FORM_MASKS_DATA_LABEL'] = array(
	'CPFCNPJ' => 'CPF/CNPJ',
	'CURRENCY' => 'M&#283;na',
	'DATE' => 'Datum',
	'TIME' => '&#268;as',
	'EMAIL' => 'E-Mail',
	'FLOAT' => 'Desetinn� &#269;�slo',
	'INTEGER' => 'Cel� &#269;�slo',
	'IP' => 'IP adresa',
	'LOGIN' => 'Login nebo U&#382;ivatelsk� jm�no',
	'URL' => 'URL/Internetov� adresa',
	'ZIP' => 'PS&#269;'
);
$P2G_LANGUAGE['FORM_EDITOR_VARS'] = array(
	'paragraph' => 'Odstavec',
	'pnorm' => 'Norm�ln�',
	'ph1' => 'Nadpis 1 &lt;H1&gt;',
	'ph2' => 'Nadpis 2 &lt;H2&gt;',
	'ph3' => 'Nadpis 3 &lt;H3&gt;',
	'ph4' => 'Nadpis 4 &lt;H4&gt;',
	'ph5' => 'Nadpis 5 &lt;H5&gt;',
	'ph6' => 'Nadpis 6 &lt;H6&gt;',
	'paddr' => 'Adresa &lt;ADDR&gt;',
	'ppre' => 'Form�tov�no &lt;PRE&gt;',
	'prem' => 'Odstra&#328; form�t',
	'bold' => 'Tu&#269;n&#283;',
	'italic' => 'Kurz�vou',
	'under' => 'Podtr&#382;en&#283;',
	'cut' => 'Vyst&#345;ihnout',
	'copy' => 'Kop�rovat',
	'paste' => 'Vlo&#382;it',
	'fcolor' => 'Zm&#283;nit barvu pop&#345;ed�',
	'bcolor' => 'Zm&#283;nit barvu pozad�',
	'emoticon' => 'Vlo&#382;it smajl�ka',
	'font' => 'P�smo',
	'fontsize' => 'Velikost',
	'left' => 'Zarovnat vlevo',
	'center' => 'Vyst&#345;edit text',
	'right' => 'Zarovnat vpravo',
	'olist' => 'Vlo&#382;/Odstra&#328; &#269;�slovan&#253; seznam',
	'ulist' => 'Vlo&#382;/Odstra&#328; jednoduch&#253; seznam',
	'indent+' => 'Zv&#253;&#353;it odsazen�',
	'indent-' => 'Sn�&#382;it odsazen�',
	'link' => 'Vlo&#382;it odkaz',
	'image' => 'Vlo&#382;it obr�zek',
	'editmode' => 'Upravit v Roz&#353;�&#345;en�m m�du',
	'choosesmil' => 'Vybrat smajl�ka'
);
$P2G_LANGUAGE['LOOKUP_CHOICE_FILTER_TIP'] = "--- Filtrovat seznam ---";
$P2G_LANGUAGE['LOOKUP_SELECTION_DEFAULT_SELFIRST'] = "--- Vlo&#382;it hodnoty ---";
$P2G_LANGUAGE['LOOKUP_SELECTION_BUTTON_TITLES'] = array(
	'add' => 'P&#345;idat vybran� polo&#382;ky',
	'addall' => 'P&#345;idat v&#353;echny polo&#382;ky',
	'rem' => 'Odstranit vybran� polo&#382;ky',
	'remall' => 'Odstranit v&#353;echny polo&#382;ky'
);
$P2G_LANGUAGE['EDIT_SELECTION_BUTTON_TITLES'] = array(
	'add' => 'P&#345;idat',
	'rem' => 'Odstranit vybran� polo&#382;ky',
	'remall' => 'Odstranit v&#353;echny polo&#382;ky'
);
$P2G_LANGUAGE['MEMO_COUNT_LABEL'] = "Znaky";
$P2G_LANGUAGE['SEL_AVAILABLE_VALUES_LABEL'] = "Mo&#382;n�:";
$P2G_LANGUAGE['SEL_INSERTED_VALUES_LABEL'] = "Vlo&#382;en�:";
$P2G_LANGUAGE['FORM_DATA_BIND_TOOLBAR_VALUES'] = array(
	'navFirstTip' => 'Jdi na prvn� z�znam',
	'navPrevTip' => 'Jdi na p&#345;edchoz� z�znam',
	'navNextTip' => 'Jdi na dal&#353;� z�znam',
	'navLastTip' => 'Jdi na posledn� z�znam',
	'actNew' => 'Nov&#253;',
	'actNewTip' => 'Vytvo&#345;it nov&#253; z�znam',
	'actEdit' => 'Edit',
	'actEditTip' => 'Uprav sou&#269;asn&#253; z�znam',
	'actDel' => 'Sma&#382;',
	'actDelTip' => 'Sma&#382; sou&#269;asn&#253; z�znam',
	'actSave' => 'Ulo&#382;',
	'actSaveTip' => 'Ulo&#382; sou&#269;asn&#253; z�znam',
	'actCancel' => 'Zru&#353;',
	'actCancelTip' => 'Zru&#353; prov�d&#283;nou akci',
	'sortAsc' => 'Vzestupn� po&#345;ad�',
	'sortDesc' => 'Sestupn� po&#345;ad�',
	'sortChoose' => 'Vyberte pole pro t&#345;�d&#283;n�',
	'sortTit' => 'T&#345;�d&#283;n�',
	'sortFirst' => 'Pole pro t&#345;�d&#283;n�',
	'gotoTip' => 'Zadejte &#269;�slo z�znamu a zm�&#269;kn&#283;te Jdi na',
	'goto' => 'Jdi na',
	'gotoBtnTip' => 'Zm�&#269;kn&#283;te pro p&#345;echod na zadan&#253; z�znam',
	'gotoTit' => 'Jdi na',
	'filter' => 'Filtr',
	'filterTip' => 'Vyberte filtr',
	'filterVTip' => 'Vyberte hodnotu pro filtr',
	'filterBtnTip' => 'Zm�&#269;kn&#283;te zde pro filtrov�n�',
	'filterTit' => 'Filtr',
	'filterFirst' => 'Filtrovan� pole'
);
$P2G_LANGUAGE['REPORT_EMPTY_VALUES'] = array(
	'emptyMsg' => 'Dotaz nebo zadan&#253; filtr pro vyhled�v�n� vr�til pr�zdn&#253; seznam',
	'backLink' => 'Zp&#283;t na Report'
);
$P2G_LANGUAGE['REPORT_ORDER_TIP'] = "T&#345;�dit podle %s";
$P2G_LANGUAGE['REPORT_FUNCTION_MESSAGES'] = array(
	'firstTit' => 'Prvn�',
	'firstTip' => 'Jdi na prvn� str�nku',
	'prevTit' => 'P&#345;edchoz�',
	'prevTip' => 'Jdi na p&#345;edchoz� str�nku',
	'prevScrTit' => '%d zp&#283;t',
	'prevScrTip' => '%d str�nek zp&#283;t ve v�stupu',
	'pageTip' => 'Jdi na st�nku &#269;.%d z %d',
	'nextTit' => 'Dal&#353;�',
	'nextTip' => 'Jdi na dal&#353;� str�nku',
	'nextScrTit' => '%d dop&#345;edu',
	'nextScrTip' => '%d str�nek dop&#345;edu ve v�stupu',
	'lastTit' => 'Posledn�',
	'lastTip' => 'Jdi na posledn� str�nku',
	'rowCount' => 'nalezeno %d z�znam&#367;',
	'rowsPerPage' => '%d z�znam&#367; na str�nku',
	'thisPage' => 'Str�nka %d z %d',
	'rowInterval' => 'Vypisuji %d-%d z %d z�znam&#367;',
	'goTo' => 'Jdi na str�nku'
);
$P2G_LANGUAGE['REPORT_SEARCH_VALUES'] = array(
	'searchTit' => 'Hledat',
	'fieldFirst' => 'Vybrat pole',
	'btnAdd' => 'P&#345;idat',
	'radioAny' => 'Jak&#253;koliv filtr',
	'radioAll' => 'V&#353;echny filtry',
	'btnClear' => 'Sma&#382;',
	'btnView' => 'Zobraz',
	'btnSend' => 'Hledej'
);
$P2G_LANGUAGE['REPORT_SEARCH_INI_OP'] = array(
	'=' => 'Rovno',
	'!=' => 'Nen� rovno',
	'LIKE' => 'Obsahuje',
	'NOT LIKE' => 'Neobsahuje',
	'LIKEI' => 'Za&#269;�n� s',
	'LIKEF' => 'Kon&#269;� s'
);
return $P2G_LANGUAGE;
?>