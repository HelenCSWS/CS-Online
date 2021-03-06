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
// Translated and maintained by: James Williams <james.l.williams@gmail.com>
//
// $Header: /www/cvsroot/php2go/languages/french.inc,v 1.8 2005/08/31 18:05:43 mpont Exp $
// $Revision: 1.8 $
// $Date: 2005/08/31 18:05:43 $

$P2G_LANGUAGE['ERR_OLD_PHP_VERSION'] = "PHP2Go ne marche pas avec la configuration actuelle de la serveur.<BR>Votre version PHP est <I>%s</I>. Les besoins minimals du framework est <I>4.1.0</I>.<BR>Installer une version �gale ou sup�rieure de marcher normalement.";
$P2G_LANGUAGE['ERR_UNSUPPORTED_EXTENSION'] = "L'extension <I>%s</I> n'est pas support� par l'installation PHP actuelle.";
$P2G_LANGUAGE['ERR_OBJ_MESSAGE'] = "Erreur dans la classe %s:<BR>%s";
$P2G_LANGUAGE['ERR_SCRIPT_MESSAGE'] = "Erreur dans le script:<BR>%s";
$P2G_LANGUAGE['ERR_DATABASE'] = "Error dans la base de donn�es";
$P2G_LANGUAGE['ERR_MISSING_DATABASE_PARAMETERS'] = "Ce n'�tait pas possible de charger les param�tres de la connexion de base de donn�es! [ID: %s]";
$P2G_LANGUAGE['ERR_INVALID_DATABASE_PARAMETERS'] = "La connexion des param�tres de base de donn�es sont invalide! Les entr�es USER, BASE et TYPE sont obligatoire. [ID: %s]"; 
$P2G_LANGUAGE['ERR_DATABASE_CONNECTION_FAILED'] = "Ce n'�tait pas possible � connecter � la base de donn�es avec la configuration d'acc�s actuelle. Sont-ils correctes?";
$P2G_LANGUAGE['ERR_TYPES'] = array(
	1 => "Erreur fatale",
	2 => "Alerte",
	4 => "Erreur syntaxe",
	8 => "Avis",
	16 => "Erreur fatale du noyau",
	32 => "Alerte du noyau",
	64 => "Compiler Fatal Error",
	128 => "Alerte de compilateur",
	256 => "Erreur d'usager",
	512 => "Alerte d'usager",
	1024 => "Avis d'usager"
);
$P2G_LANGUAGE['ERR_ABSOLUTE_URI_NOT_FOUND'] = "L'entr�e 'ABSOLUTE_URI' n'�tait pas fixer dans la tableau globale de configurations!";
$P2G_LANGUAGE['ERR_CONFIG_ENTRY_NOT_FOUND'] = "L'entr�e <I>%s</I> n'�tait pas fix� dans le tableau globale de configurations!"; 
$P2G_LANGUAGE['ERR_INVALID_LANGDOMAIN_FILE'] = "La fichier de messages pour la domaine %s, langage %s, est invalide ou n'exist pas.";
$P2G_LANGUAGE['ERR_CANT_FIND_CFG_VAL'] = "Ce n'�tait pas possible de trouver l'entr�e <I>%s</I> dans la tableau globale de configurations. Etait-il fix� correctement dans l'installation?";
$P2G_LANGUAGE['ERR_CANT_LOAD_MODULE'] = "Ce n'�tait pas possible de charger le module <I>%s</I>";
$P2G_LANGUAGE['ERR_CANT_LOAD_DIR'] = "Ce n'�tait pas possible de charger les modules d'un dossier valide � <I>%s</I>";
$P2G_LANGUAGE['ERR_CANT_LOAD_DIR_MODULE'] = "Ce n'�tait pas possible de charger la fichier <I>%s</I> � <I>%s</I>";
$P2G_LANGUAGE['ERR_CANT_CONVERT_DATE'] = "Ce n'�tait pas possible de convertir le date � la forme %s.";
$P2G_LANGUAGE['ERR_CANT_RUN_ON_WINDOWS'] = "La classe %s ne marche pas sur Windows!";
$P2G_LANGUAGE['ERR_UNDEFINED_FUNCTION'] = "La fonction %s n'existe pas!";
$P2G_LANGUAGE['ERR_UNDEFINED_METHOD'] = "La m�thode %s n'est pas d�fini dans la classe %s!";
$P2G_LANGUAGE['ERR_ABSTRACT_CLASS'] = "La classe <I>%s</I> est abstraite et vous ne pouvez pas cr�e une objet de cette classe!";
$P2G_LANGUAGE['ERR_DATE_FORMAT'] = "La date %s n'est pas sous la forme requis %s.";
$P2G_LANGUAGE['ERR_INVALID_RESOURCE'] = "La variable %s envoy� � la fonction ou m�thode %s n'est pas une ressource valide.";
$P2G_LANGUAGE['ERR_INVALID_AUTHENTICATOR'] = "L'authenticator %s n'est pas une sousclasse de php2go.net.Auth!";
$P2G_LANGUAGE['ERR_INVALID_AUTHENTICATOR_PATH'] = "Ce n'�tait pas possible d'importer et/ou cr�er un instance du authenticator %s!";
$P2G_LANGUAGE['ERR_INVALID_USERCONTAINER'] = "Le conteneur d'utilisateur %s n'est pas une sousclass de  php2go.auth.User!";
$P2G_LANGUAGE['ERR_INVALID_USERCONTAINER_PATH' ] = "Ce n'�tait pas possible d'importer et/ou cr�er un instance du conteneur d'utilisateur %s!";
$P2G_LANGUAGE['ERR_XML_PARSE'] = "Une erreur s'est pass� en analysant la fichier XML:<BR>Erreur: %s<BR>Ligne: %s, Colonne: %s";
$P2G_LANGUAGE['ERR_STATEMENT_EVAL'] = "Un erreur s'est pass� en analysant le code PHP <I>%s</I>";
$P2G_LANGUAGE['ERR_CANT_FIND_FILE'] = "Ce n'�tait pas possible de trouver la fichier ou dossier %s. Existe-t-il?";
$P2G_LANGUAGE['ERR_CANT_OPEN_FILE'] = "Ce n'�tait pas possible d'ouvrir la fichier %s avec la mode %s. V�rifier les permissions entr�es/sorties et the used mode.";
$P2G_LANGUAGE['ERR_CANT_CREATE_FILE'] = "Ce n'�tait pas possible de cr�er la fichier ou dossier %s. V�rifier les permissions entr�es/sorties de la dossier parent.";
$P2G_LANGUAGE['ERR_CANT_READ_FILE'] = "Ce n'�tait pas possible d'ouvrir la fichier %s pour lire. Existe-t-il? Est-ce que le chemin correcte?";
$P2G_LANGUAGE['ERR_CANT_WRITE_FILE'] = "Ce n'�tait pas possible d'ouvrir la fichier ou dossier %s pour �crire. Avez-vous la permission de l'�craser?";
$P2G_LANGUAGE['ERR_CANT_DELETE_FILE'] = "Ce n'�tait pas possible de detruire la fichier %s.<BR>Avez-vous la permission d'�crire � cette dossier? Est-ce que la fichier a la permission d'�tre d�truire?";
$P2G_LANGUAGE['ERR_CANT_INCLUDE_FILE'] = "Ce n'�tait pas possible d'ouvrir la fichier %s pour inclure. Existe-t-il? Est-ce que le chemin correcte?";
$P2G_LANGUAGE['ERR_CANT_CHANGE_MODE'] = "Ce n'�tait pas possible d'appliquer la mode %s � la fichier ou dossier %s.";
$P2G_LANGUAGE['ERR_CANT_READ_DIR'] = "Ce n'�tait pas possible de trouver la dossier %s. Existe-t-il?";
$P2G_LANGUAGE['ERR_CANT_OPEN_DIR'] = "Ce n'�tait pas possible d'ouvrir la dossier %s pour lire. V�rifier ses permissions.";
$P2G_LANGUAGE['ERR_CANT_FIND_SERIALIZATION_FILE'] = "Ce n'�tait pas possible d'ouvrir la fichier ce qui contenant l'objet s�rialis� pour lire. Est-ce que le chemin correcte? Etait la fichier cr�e?";
$P2G_LANGUAGE['ERR_EMPTY_INSERT_QUERY'] = "La requ�te ins�r� dans la table '%s' retourn� un set vide d'utiliser les valeurs fournies.";
$P2G_LANGUAGE['ERR_EMPTY_UPDATE_QUERY'] = "La requ�te actualis� dans la table '%s' avec la clause %s retourn� un set vide d'utiliser les valeurs fournies.";
$P2G_LANGUAGE['ERR_EMPTY_TOGLOBALS_QUERY'] = "La requ�te %s, utilis� de publier les variables dans une in the global scope, retourn� un set vide.";
$P2G_LANGUAGE['ERR_TOGLOBALS_WRONG_USAGE'] = "La requ�te pass� � la m�thode Db::toGlobals() doit commencer avec 'SELECT' (DQL).";
$P2G_LANGUAGE['ERR_EMPTY_TEMPLATE_FILE'] = "Ce n'�tait pas possible d'initialiser le gabarit avec un nom de fichier vide!";
$P2G_LANGUAGE['ERR_EMPTY_TEMPLATE_CONTENT'] = "Le gabarit n'�tait pas pr�par�, parce que son contenu est vide. V�rifier le contenu pass� au constructeur.";
$P2G_LANGUAGE['ERR_CANT_FIND_VARIABLE'] = "Ce n'�tait pas possible de trouver la variable %s dans le gabarit <I>%s</I>. <BR>Recevoir des r�sultats correctes de cet outil, ins�rez {%s} dans la code du gabarit.";
$P2G_LANGUAGE['ERR_CANT_FIND_BLOCK'] = "Ce n'�tait pas possible de trouver le bloc <I>%s</I> pour cr�ation. Est-il declar� dans le gabarit?";
$P2G_LANGUAGE['ERR_CANT_FIND_BLOCK_REUSE'] = "Ce n'�tait pas possible de trouver le bloc %s de r�utiliser comme %s.";
$P2G_LANGUAGE['ERR_CANT_PUT_ON_PREPARED_TEMPLATE'] = "L'el�ment %s �tait d�j� configur� with the m�thode DocumentElement::parse(). Ce n'�tait pas possible d'ajouter des el�ments avec la m�thode put().";
$P2G_LANGUAGE['ERR_TEMPLATE_NOT_PREPARED'] = "Le gabarit %s n'�tait pas pr�par�. G�rez la m�thode %s::parse(); avant ex�cutez les op�rations with the object.";
$P2G_LANGUAGE['ERR_TEMPLATE_ALREADY_PREPARED'] = "Ce gabarit avait d�j� pr�par�. Vous devez appeler le m�thode parse() seulement une fois."; 
$P2G_LANGUAGE['ERR_UNBALANCED_BLOCKDEF'] = "La d�finition du bloc <I>%s</I> est d�sax�e.";
$P2G_LANGUAGE['ERR_INVALID_MODIFIER'] = "Le modificateur <I>%s</I> est invalide ou n'existe pas!";
$P2G_LANGUAGE['ERR_EMPTY_DOC_LAYOUT'] = "Il n'y a pas de el�ments d�fini dans la plan de document fourni.";
$P2G_LANGUAGE['ERR_CANT_REPLICATE_ROOT_BLOCK'] = "Le bloc de base d'un gabarit ne peut pas �tre �cras�.";
$P2G_LANGUAGE['ERR_MUST_BE_POSITIVE'] = "La valeur de %s doit �tre sup�rieur � z�ro dans la fonction %s.";
$P2G_LANGUAGE['ERR_MUST_BE_AN_ARRAY'] = "La valeur de %s doit �tre un tableau en la fonction %s";
$P2G_LANGUAGE['ERR_VALUE_OUT_OF_BOUNDS'] = "La valeur de '%s' is out of the allowed bounds: %s and %s";
$P2G_LANGUAGE['ERR_URL_MALFORMED'] = "La valeur de <I>%s</I> n'est pas une URL valide!";
$P2G_LANGUAGE['ERR_HOST_INVALID'] = "<I>%s</I> n'est pas une valide h�te de connexion!";
$P2G_LANGUAGE['ERR_DNS_NOT_FOUND'] = "Ce n'�tait pas possible de trouver une DNS valide � la domaine %s.";
$P2G_LANGUAGE['ERR_SESSION_PROPERTY_NOT_FOUND'] = "La propri�t� %s de la variable de la session %s n'�tait pas trouv�. Ex�cutez la fonction createProperty() ";
$P2G_LANGUAGE['ERR_SESSION_TIMECOUNTER_NOT_FOUND'] = "Le minuteur %s de la variable de la session %s n'�tait pas trouv�. Ex�cutez la fonction createTimeCounter() ";
$P2G_LANGUAGE['ERR_INVALID_OBJECT'] = "L'object pass� comme param�tre n'est pas un instance valide de la classe <I>%s</I>!";
$P2G_LANGUAGE['ERR_CALCULATOR_MISSING_PARAMETERS'] = "Param�tres insuffisantes de construire la calcultrice!";
$P2G_LANGUAGE['ERR_COLORSEL_MISSING_PARAMETERS'] = "Param�tres insuffisantes de construire la s�lection de la couleur!";
$P2G_LANGUAGE['ERR_INVALID_FORM_METHOD'] = "La m�thode %s n'est pas valide de sousmettre la forme '%s'";
$P2G_LANGUAGE['ERR_INVALID_VALIDATE_FUNC'] = "<I>%s</I> n'est pas un nom valide pour un fonction de validation !";
$P2G_LANGUAGE['ERR_FORM_DUPLICATED_FIELD'] = "Le champ <I>%s</I> est d�j� d�fini dans la forme <I>%s</I>!";
$P2G_LANGUAGE['ERR_FORM_DUPLICATED_BUTTON'] = "Le bouton <I>%s</I> d�ja existe dans la forme <I>%s</I>!";
$P2G_LANGUAGE['ERR_FORM_DUPLICATED_SECTION'] = "The section ID <I>%s</I> �tait d�j� d�fini dans la forme <I>%s</I>!";
$P2G_LANGUAGE['ERR_FORM_SECTION_EVALFUNC'] = "Ce n'�tait possible de la visibilit� de la section <I>%s</I>, parce que la fonction <I>%s</I> n'est pas d�fini!";
$P2G_LANGUAGE['ERR_FORM_SECTION_TPLBLOCK'] = "La section <I>%s</I> est conditionel. Vouz devez d�clarer un bloc nomm� <I>%s</I> dans le gabarit contenu the section champs!";
$P2G_LANGUAGE['ERR_FORM_FIELD_TITLE'] = "Cette forme contient les erreurs suivantes:";
$P2G_LANGUAGE['ERR_FORM_FIELD_INVALID_INPUT'] = "Le champ %s contient une valuer invalide %s.";
$P2G_LANGUAGE['ERR_FORM_FIELD_INVALID_FLOAT'] = "La pr�cision numerique du champ %s est invalide.\\nLes tailles maximum sont %d pour la partie entier et %d pour la partie d�cimale.";
$P2G_LANGUAGE['ERR_FORM_FIELD_ALPHANUM'] = "Le champ %s doit contenir seulement les caract�res alphanumeriques.";
$P2G_LANGUAGE['ERR_FORM_FIELD_CHOICE'] = "Le champ %s doit contenir une des valeurs suivantes: <I>%s</I>";
$P2G_LANGUAGE['ERR_FORM_FIELD_CREDITCARD'] = "Le champ %s contient un nombre de carte de cr�dit invalide %s.";
$P2G_LANGUAGE['ERR_FORM_FIELD_MIN_LENGTH'] = "Le champ %s doit avoir au moins %s caract�res.";
$P2G_LANGUAGE['ERR_FORM_FIELD_MAX_LENGTH'] = "Le champ %s must have until %s caract�res.";
$P2G_LANGUAGE['ERR_FORM_FIELD_REQUIRED'] = "Le champ %s est obligatoire.";
$P2G_LANGUAGE['ERR_FORM_FIELD_REGEX'] = "Le champ %s a une valeur incorrecte!";
$P2G_LANGUAGE['ERR_FORM_FIELD_EQ'] = "Le champ %s doit �tre �gal au champ %s!";
$P2G_LANGUAGE['ERR_FORM_FIELD_NEQ'] = "Le champ %s ne peut pas �gal au champ %s!";
$P2G_LANGUAGE['ERR_FORM_FIELD_GT'] = "Le champ %s doit �tre sup�rieur au champ %s!";
$P2G_LANGUAGE['ERR_FORM_FIELD_LT'] = "Le champ %s doit �tre inf�rieur que champ %s!";
$P2G_LANGUAGE['ERR_FORM_FIELD_GOET'] = "Le champ %s doit �tre sup�rieur ou �gal au champ %s!";
$P2G_LANGUAGE['ERR_FORM_FIELD_LOET'] = "Le champ %s doit �tre inf�rieur ou �gal au champ %s!";
$P2G_LANGUAGE['ERR_FORM_FIELD_VALUE_EQ'] = "Le champ %s doit �tre �gal � %s!";
$P2G_LANGUAGE['ERR_FORM_FIELD_VALUE_NEQ'] = "Le champ %s can't be ou �gal � %s!";
$P2G_LANGUAGE['ERR_FORM_FIELD_VALUE_GT'] = "Le champ %s doit �tre sup�rieur � %s!";
$P2G_LANGUAGE['ERR_FORM_FIELD_VALUE_LT'] = "Le champ %s doit �tre inf�rieur � %s!";
$P2G_LANGUAGE['ERR_FORM_FIELD_VALUE_GOET'] = "Le champ %s doit �tre sup�rieur ou �gal � %s!";
$P2G_LANGUAGE['ERR_FORM_FIELD_VALUE_LOET'] = "Le champ %s doit �tre inf�rieur ou �gal � %s!";
$P2G_LANGUAGE['ERR_FORM_INVALID_MASK'] = "The mask <I>%s</I>, sur le champ <I>%s</I> n'est pas valide!";
$P2G_LANGUAGE['ERR_FORM_INVALID_FIELDTYPE'] = "Le champ type <I>%s</I> n'est pas valide!";
$P2G_LANGUAGE['ERR_FORM_CAPTCHA'] = "Le code �ntr� dans le champ %s ne coordone pas le contenu de l'image!";
$P2G_LANGUAGE['ERR_DATAGRID_INVALID_FIELDTYPE'] = "Le champ type <I>%s</I> ne peut pas utiliser dans un <I>grid</I>!";
$P2G_LANGUAGE['ERR_DATAGRID_INVALID_CELLSIZES'] = "La valeur du attribut <I>CELLSIZES</I> dans le champ <I>%s</I> est invalide. Vous devez fournir N+1 tailles (N=FIELDSET size) et la somme des tailles doit �tre �gal � 100!";
$P2G_LANGUAGE['ERR_DATAGRID_INVALID_FIELDCOUNT'] = "The query defined in the <I>DATASOURCE</I> du champ <I>%s</I> doit retourn� N+2 colonnes (N=FIELDSET size)";
$P2G_LANGUAGE['ERR_FORM_WRONG_LISTENER'] = "Les attributs de la contr�leur d'�v�nements <I>%s</I> sont invalides ou incomplets!";
$P2G_LANGUAGE['ERR_FORM_WRONG_RULE'] = "Les attributs de la r�gle <I>%s</I> sont invalides ou incomplets!";
$P2G_LANGUAGE['ERR_INVALID_DATASOURCE_SYNTAX'] = "La sp�cification de la marqueur DATASOURCE dans le champ <I>%s</I> est invalide.<BR>Les noeuds enfants KEYFIELD et LOOKUPTABLE sont obligatoiresy!";
$P2G_LANGUAGE['ERR_MISSING_GROUPFIELD_CHILDREN'] = "La sp�cification du champ <I>%s</I> est incomplet. Vous devez fournir au moins noeud enfant &lt;OPTION&gt; !";
$P2G_LANGUAGE['ERR_MISSING_OPTION_VALUE'] = "Le attribut VALUE de l'option au indice <I>%d</I> dans le champ <I>%s</I> est manquant!";
$P2G_LANGUAGE['ERR_MISSING_DBRADIOFIELD_DATASOURCE'] = "Il n'�tait pas d�fini un marquer DATASOURCE tag au DBRADIOFIELD <I>%s</I>!";
$P2G_LANGUAGE['ERR_EMPTY_DBRADIOFIELD_RESULTS'] = "La requ�te fournie dans l'�l�ment DATASOURCE element of the DBRADIOFIELD <I>%s</I> returned an empty set!";
$P2G_LANGUAGE['ERR_MISSING_COMBOOPTION_VALUE'] = "L'attribut VALUE de l'option � indice %d dans le COMBOFIELD <I>%s</I> est manquant!";
$P2G_LANGUAGE['ERR_MISSING_EDITSELECTION_CHILDREN'] = "The sp�cification du EDITSELECTIONFIELD <I>%s</I> est incomplet. Les noeuds enfants EDITFIELD et LOOKUPFIELD sont obligatoires!";
$P2G_LANGUAGE['ERR_MISSING_LOOKUPSELECTION_CHILDREN'] = "The sp�cification du LOOKUPSELECTIONFIELD <I>%s</I> a tort. Le noeud doit avoir deux enfants LOOKUPFIELD!";
$P2G_LANGUAGE['ERR_MISSING_SOURCELOOKUP_DATASOURCE'] = "Il n'y a aucun DATASOURCE d�fini dans le premier enfant LOOKUPFIELD du LOOKUPSELECTIONFIELD <I>%s</I>!";
$P2G_LANGUAGE['ERR_MISSING_QUERY_ELEMENTS'] = "Les items \$fields et \$tables sont obligatoires dans la construction de la r�quete SQL!";
$P2G_LANGUAGE['ERR_INVALID_DATAGRID_STRUCTURE'] = "La structure de la grille <I>%s</I> est invalide. Vouz devez d�clarer un <I>DATASOURCE</I> et un <I>FIELDSET</I> contenir au moins un champ!";
$P2G_LANGUAGE['ERR_MISSING_RANGEFIELD_CHILDREN'] = "La sp�cification du RANGEFIELD <I>%s</I> a tort. Le noeud doit avoir deux enfants EDITFIELD!";
$P2G_LANGUAGE['ERR_EDITSEARCH_INVALID_DATAFILTER'] = "La sp�cification du DATAFILTER <I>%s</I> est invalide.";
$P2G_LANGUAGE['ERR_EDITSEARCH_INVALID_DATAFILTER_MASK'] = "Le masque assign� au DATAFILTER <I>%s</I> est invalide.";
$P2G_LANGUAGE['ERR_EDITSEARCH_DUPLICATED_DATAFILTER'] = "D�j� existe un autre DATAFILTER avec ID <I>%s</I>.";
$P2G_LANGUAGE['ERR_MISSING_EDITSEARCH_CHILDREN'] = "La sp�cification du  EDITSEARCHFIELD <I>%s</I> est incomplet. Vous devez fournir au moins un DATAFILTER, un DATASOURCE et un LOOKUPFIELD!"; 
$P2G_LANGUAGE['ERR_CSV_DB_JSRS'] = "Ce n'�tait pas possible de compl�ter l'op�ration!";
$P2G_LANGUAGE['ERR_SEARCHFORM_EMPTY'] = "La recherche ne pouvait pas effectu� parce que tous les champs de recherche sont vides.";
$P2G_LANGUAGE['ERR_SEARCHFORM_INVALID'] = "La recherche ne pouvait pas effectu� car les valeurs de filtres sont vides ou plus courtes que %d carat�res.";
$P2G_LANGUAGE['ERR_REPORT_MAX_GROUP_COLS'] = "The grouping columns count doit �tre inf�rieur au compte de colonnes!";
$P2G_LANGUAGE['ERR_REPORT_UNKNOWN_GROUP_COL'] = "La colonne <I>%s</I> n'est pas d�fini dans la r�sultat de la requ�te!";
$P2G_LANGUAGE['ERR_REPORT_MISSING_BLOCK_VARIABLE'] = "La variable <I>%s</I> de la bloc <I>%s</I> n'�tait pas declar� dans le gabarit!";
$P2G_LANGUAGE['ERR_REPORT_INVALID_COLSIZES'] = "La valeur <I>%s</I> n'est pas une d�finition valide aux tailles de colonnes!";
$P2G_LANGUAGE['ERR_REPORT_COL_SIZES_SUM'] = "La somme des tailles de colonnes pass� � la fonction setColumnSizes doit �tre �gal � 100!";
$P2G_LANGUAGE['ERR_REPORT_COL_COUNT_MISMATCH'] = "Le compte de tailles colonnes fourni (%s) n'est pas �gal au compte de colonnes of the report (%s, %s grouping)";
$P2G_LANGUAGE['ERR_REPORT_MISSING_SEARCH_VARIABLE'] = "Ce n'�tait pas possible de consruire la forme de recherche, parce que la variable <I>%s</I> n'�tait pas trouv�. <BR>D�clarez {%s} dans le gabarit du document � continuer.";
$P2G_LANGUAGE['ERR_REPORT_SEARCH_PARS_MALFORMED'] = "Vous devez fournir le Label, le Field Name et la Mask dans la construction des param�tres de recherche!";
$P2G_LANGUAGE['ERR_REPORT_SEARCH_INVALID_MASK'] = "La masque <I>%s</I> du param�tre de recherche  <I>#%d</I> est invalide!";
$P2G_LANGUAGE['ERR_REPORT_MIN_ALT_STYLE'] = "La fonction setAlternateStyle doit recevoir au moins deux param�tres (CSS names)!";
$P2G_LANGUAGE['ERR_REPORT_HIDDEN_GROUP'] = "Une colonne cach�e (%s) ne peut pas �tre inclut des titres de groupe!";
$P2G_LANGUAGE['ERR_REPORT_GROUP_HIDDEN'] = "Une colonne displayed in the group headers (%s) ne peut pas �tre d�fini comme cach�e!";
$P2G_LANGUAGE['ERR_REPORT_MAX_HIDDEN_COLS'] = "La nombre des colonnes cach�es doit �tre smaller than the requ�te  columns count!";
$P2G_LANGUAGE['ERR_REPORT_UNKNOWN_HIDDEN_COL'] = "La colonne <I>%s</I> qui est d�fini comme cach�e n'existe pas dans XXXX the requ�te  result set!";
$P2G_LANGUAGE['ERR_DATASET_INVALID_TYPE'] = "Le type <I>%s</I> n'est pas un type valide de DataSet!";
$P2G_LANGUAGE['ERR_MENU_WRONG_ROOT_SQL'] = "The requ�te gard� dans le param�tre \$rootSql de la m�thode Menu::loadFromDatabase() retourn� un set vide ou elle n'a pas les champs requis et minumums (Index and Label)!";
$P2G_LANGUAGE['ERR_MENU_WRONG_CHILDREN_STATEMENT'] = "The requ�te pass� dans le param�tre \$childSql de la m�thode Menu::loadFromDatabase() est invalide.<BR>Fournissez une expression qui contenu le '~var~' (Relation Column) plus les deux champs requis (Index and Label)";
$P2G_LANGUAGE['ERR_MENU_NOT_FOUND'] = "Le menu n�tait pas cr�e. Construisez les items du menu utilisant Menu::loadFromDatabase() ou Menu::loadFromXmlFile().";
$P2G_LANGUAGE['ERR_MENU_EMPTY_XML_ROOT'] = "Le noeud racine de la fichier XML n'a aucun enfants!";
$P2G_LANGUAGE['ERR_MENU_INVALID_IMAGE'] = "L'image <I>%s</I> n'est pas valide. Les valeurs possibles sont: <I>%s</I>.";
$P2G_LANGUAGE['ERR_FTP_MISSING_HOST'] = "Ce n'�tait pas possible d'ouvrir la connexion. Vous devez �tablir la connexion, utilisant les m�thode FtpClient::setServer() et FtpClient::setUserInfo()";
$P2G_LANGUAGE['ERR_FTP_MISSING_USER_OR_PASS'] = "Ce n'�tait pas possible d'authentifier. Une des valuers d'usager (nom d'usager ou mot de passe) n'�tait pas fournie.";
$P2G_LANGUAGE['ERR_SPRSH_OUT_OF_BOUNDS'] = "Le limites de spreadsheet �tait surpass�: [%d;%d]";
$P2G_LANGUAGE['ERR_SPRSH_MAX_NOTE_CHARS'] = "La taille maximum pour un commentaire de la cellule �tait surpass�<BR>Valeur: %s";
$P2G_LANGUAGE['ERR_SPRSH_MAX_FONTS'] = "La maximum de quatre polices pour chaque spreadsheet �tait surpass�!";
$P2G_LANGUAGE['ERR_UPLOAD_NOT_FOUND'] = "Ce n'�tait pas possible de compl�ter the upload. The file wasn't transferred correctly.";
$P2G_LANGUAGE['ERR_UPLOAD_TOO_BIG'] = "Ce n'�tait pas possible de compl�ter the upload. La taille de la fichier est sup�rieur � la taille maximum permis.";
$P2G_LANGUAGE['ERR_UPLOAD_INVALID_NAME'] = "Ce n'�tait pas possible de compl�ter la transmission de la fichier. Le nom de fichier %s est invalide.";
$P2G_LANGUAGE['ERR_UPLOAD_INVALID_TYPE'] = "Ce n'�tait pas possible de compl�ter la transmission de la fichier. L'extension %s n'est pas permis.";
$P2G_LANGUAGE['ERR_UPLOAD_FILE_EXISTS'] = "Ce n'�tait pas possible de compl�ter la transmission de la fichier. La fichier <I>%s</I> d�j� existe et n'�tait pas �cras�.";
$P2G_LANGUAGE['ERR_UPLOAD_CANT_MOVE'] = "Ce n'�tait pas possible de d�placer la fichier temporaire � la dossier destinaire.";
$P2G_LANGUAGE['ERR_CANT_OPEN_SOCKET'] = "Ce n'�tait pas possible d'ouvrir le socket utilisant le port %s avec la h�te %s!<BR>Erreur %d - %s";
$P2G_LANGUAGE['ERR_SOCKET_NOT_CONNECTED'] = "La connection avec le socket n'est pas active!";
$P2G_LANGUAGE['ERR_SOCKET_EOF'] = "Fin de fichier attendu, l'op�ration ne peut pas �tre accompli!";
$P2G_LANGUAGE['ERR_SOCKET_TIMEOUT'] = "La temporisation socket �tait surpass�. L'op�ration ne peut pas �tre accompli!";
$P2G_LANGUAGE['ERR_MAIL_MESSAGE_NOT_BUILT'] = "Le message n�tait pas contruit correctement et ne peut pas �tre envoy�!<BR>G�rez la m�thode build().";
$P2G_LANGUAGE['ERR_MAIL_EMPTY_RCPT'] = "Il n'y a aucun destinataires pour le message!";
$P2G_LANGUAGE['ERR_SIGNED_MESSAGE_SIGN'] = "Ce n'�tait pas possible de signer le contenu de le message.<BR>%s";
$P2G_LANGUAGE['ERR_SMTP_CONNECTION'] = "Erreur en connectant � la serveur SMTP %s!<BR>Errno: [%d]<BR>Errmsg: %s";
$P2G_LANGUAGE['ERR_SMTP_COMMAND'] = "Erreur en ex�cutant la commande %s sur la serveur SMTP!<BR>Errno: [%d]<BR>Errmsg: %s";
$P2G_LANGUAGE['ERR_SMTP_AUTHENTICATE'] = "Ce n'�tait pas possible d'authentifier avec la serveur SMTP!";
$P2G_LANGUAGE['ERR_CRYPT_MISSING_KEY'] = "Crypter ou d�crypter donn�es, vous devez fournir un cl� avec m�thode Crypt::setKey()!";
$P2G_LANGUAGE['ERR_CRYPT_OPEN_MODULE'] = "Ce n'�tait pas possible to initialiser le cryptogramme <I>%s</I> utilisant la mode %s!";
$P2G_LANGUAGE['ERR_OPENSSL_PUBKEY_ENCRYPT'] = "Ce n'�tait pas possible d'extraire une cl� publique de crypter les donn�es. Fournissez un fichier contenu un certificat valide � la m�thode OpenSSLCrypt::setCertificatePath()!";
$P2G_LANGUAGE['ERR_OPENSSL_PUBKEY_VERIFY'] = "Ce n'�tait pas possible d'extraire une cl� publique de v�rifier la signature inclu dans cette donn�es. Fournissez un fichier contenu un certificat valide � la m�thode OpenSSLCrypt::setCertificatePath()!"; 
$P2G_LANGUAGE['ERR_OPENSSL_PRIVKEY_DECRYPT'] = "Ce n'�tait pas possible d'extraire une cl� priv� de d�crypter les donn�es. Fournissez un fichier qui contient la cl� priv� et la phrase de passe, si la cl� l'a besoin.";
$P2G_LANGUAGE['ERR_OPENSSL_PRIVKEY_SIGN'] = "Ce n'�tait pas possible d'extraire une cl� priv� to generate the digital signature. Fournissez un fichier qui contient une cl� priv� et la phrase de passe, si la cl� cr�e l'a besoin.";
$P2G_LANGUAGE['ERR_OPENSSL_CERT_PATH'] = "Le chemin d'acc�s du certificat est invalide!";
$P2G_LANGUAGE['ERR_OPENSSL_READ_CERT'] = "Ce n'�tait pas possible de lire le contenu du certificat %s!";
$P2G_LANGUAGE['ERR_POP3_CONNECTION'] = "Erreur en connectant � la serveur POP3 %s!<BR>Errno: [%d]<BR>- Errmsg: %s";
$P2G_LANGUAGE['ERR_POP3_COMMAND'] = "Erreur en ex�cutant la commande %s sur la serveur POP!<BR>Errmsg: %s";
$P2G_LANGUAGE['ERR_POP3_AUTHENTICATE'] = "Ce n'�tait pas possible d'authentifier avec la serveur POP3!";
$P2G_LANGUAGE['ERR_POP3_APOP'] = "Ce n'�tait pas possible g�rer la commande APOP : il n'y a pas de banni�re d'authentification !";
$P2G_LANGUAGE['ERR_HTTP_CONNECT'] = "Ce n'�tait pas possible d'�tablir un connexion HTTP avec la h�te <I>%s</I>!";
$P2G_LANGUAGE['ERR_HTTP_MISSING_HOST'] = "La h�te HTTP n'�tait pas fix�. G�rez la m�thode HttpClient::setHost().";
$P2G_LANGUAGE['ERR_CANT_INSTANTIATE_VALIDATOR'] = "Ce n'�tait pas possible cr�er une instance de la classe de validation <B>%s</B>!";
$P2G_LANGUAGE['ERR_INVALID_VALIDATOR'] = "La classe de validation <I>%s</I> est invalide! La m�thode %s::execute() ne peut pas �tre trouv�.";
$P2G_LANGUAGE['ERR_ASSERTION_MESSAGE'] = "L'expression �valu� n'a pas donn� le r�sultat pr�vu.";
$P2G_LANGUAGE['ERR_INVALID_CALLBACK'] = "La fonction ou m�thode <I>%s</I> n'est pas une callback valide!";
$P2G_LANGUAGE['ERR_INVALID_PROPERTIES_FILE'] = "La fichier <I>%s</I> ne contient aucune donn�es de configuration valide!";
$P2G_LANGUAGE['ERR_INVALID_COLOR_SPEC'] = "<I>%s</I> n'est past une sp�cification valide de coleur.";
$P2G_LANGUAGE['ERR_ALLOCATE_COLOR'] = "Ce n'�tait pas possible d'attribuer une nouvelle coleur pour cette image!";
$P2G_LANGUAGE['ERR_JSRS_MISSING_HANDLER'] = "Il y a un nom de handler JSRS ou alias manquant!";
$P2G_LANGUAGE['ERR_JSRS_INVALID_HANDLER'] = "Le handler JSRS handler %s est invalide!";
$P2G_LANGUAGE['DEFAULT_BTN_VALUE'] = "Soumettre";
$P2G_LANGUAGE['CALENDAR_LINK_TITLE'] = "Ouvrir la calendrier";
$P2G_LANGUAGE['CALCULATOR_LINK_TITLE'] = "Ouvrir la calculatrice";
$P2G_LANGUAGE['CALCULATOR_WINDOW_TITLE'] = "La Calculatrice";
$P2G_LANGUAGE['CALCULATOR_RESULT_CAPTION'] = "R�sultat";
$P2G_LANGUAGE['DAYS_OF_WEEK'] = array("Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi");
$P2G_LANGUAGE['DAYS_OF_WEEK_ABBR'] = array("Dim", "Lun", "Mar", "Mer", "Jeu", "Ven", "Sam");
$P2G_LANGUAGE['MONTHS_OF_YEAR'] = array("Janvier", "F�vrier", "Mars", "Avril", "Mai", "Juin", "Juillet", "Ao�t", "Septembre", "Octobre", "Novembre", "D�cembre");
$P2G_LANGUAGE['MONTHS_OF_YEAR_ABBR'] = array("Jan", "F�v", "Mar", "Avr", "Mai", "Juin", "Juil", "Ao�t", "Sep", "Oct", "Nov", "D�c");
$P2G_LANGUAGE['COMPRESS_USE_MSG'] = "<!-- Uses %s Compression -->";
$P2G_LANGUAGE['COMPRESS_DEBUG_MSG_ORIG'] = "Original Size: %d";
$P2G_LANGUAGE['COMPRESS_DEBUG_MSG_COMP'] = "Packed Size: %d";
$P2G_LANGUAGE['FORM_MASKS_DATA_LABEL'] = array(
	'CPFCNPJ' => 'CPF/CNPJ',
	'CURRENCY' => 'Devise',
	'DATE' => 'Date',
	'TIME' => 'L\'heure',
	'EMAIL' => 'Couriel',
	'FLOAT' => 'Nombre Flottant',
	'INTEGER' => 'Nombre Entier',
	'IP' => 'Addresse IP',
	'LOGIN' => 'Login ou Nom d\'usager',
	'URL' => 'URL/Addresse internet',
	'ZIP' => 'Code Postale'
);
$P2G_LANGUAGE['FORM_EDITOR_VARS'] = array(
	'paragraph' => 'Paragraphe',
	'pnorm' => 'Normale',
	'ph1' => 'Titre 1 &lt;H1&gt;',
	'ph2' => 'Titre 2 &lt;H2&gt;',
	'ph3' => 'Titre 3 &lt;H3&gt;',
	'ph4' => 'Titre 4 &lt;H4&gt;',
	'ph5' => 'Titre 5 &lt;H5&gt;',
	'ph6' => 'Titre 6 &lt;H6&gt;',
	'paddr' => 'Addresse &lt;ADDR&gt;',
	'ppre' => 'Formatted &lt;PRE&gt;',
	'prem' => 'Enlever Format',
	'bold' => 'Format en gras',
	'italic' => 'Format Italique',
	'under' => 'Format Souligne',
	'cut' => 'Couper',
	'copy' => 'Copier',
	'paste' => 'Paste',
	'fcolor' => 'Changer la couleur avant-plan',
	'bcolor' => 'Changer la couleur arri�re-plan',
	'emoticon' => 'Ins�rer un smiley',
	'font' => 'Police',
	'fontsize' => 'Taille',
	'left' => 'Aligner � gauche',
	'center' => 'Centrer le texte',
	'right' => 'Aligner � droite',
	'olist' => 'Ins�rer/Enlever Numbered List',
	'ulist' => 'Ins�rer/Enlever Simple List',
	'indent+' => 'Augmenter Indentation',
	'indent-' => 'R�duire Indentation',
	'link' => 'Ins�rer une Lien',
	'image' => 'Ins�rer une Image',
	'editmode' => 'Editer dans la Mode Avanc�e',
	'choosesmil' => 'Choisir un Smiley'
);
$P2G_LANGUAGE['LOOKUP_CHOICE_FILTER_TIP'] = "--- Filtrez la liste ---";
$P2G_LANGUAGE['LOOKUP_SELECTION_DEFAULT_SELFIRST'] = "--- Ins�rez les Valeurs ---";
$P2G_LANGUAGE['LOOKUP_SELECTION_BUTTON_TITLES'] = array(
	'add' => 'Ajoutez les items choisis',
	'addall' => 'Ajoutez tous les items',
	'rem' => 'Enlevez les items choisis',
	'remall' => 'Enlevez tous'
);
$P2G_LANGUAGE['EDIT_SELECTION_BUTTON_TITLES'] = array(
	'add' => 'Ajoutez',
	'rem' => 'Enlevez les items choisis',
	'remall' => 'Enlevez tous'
);
$P2G_LANGUAGE['MEMO_COUNT_LABEL'] = "Chars";
$P2G_LANGUAGE['SEL_AVAILABLE_VALUES_LABEL'] = "Disponible:";
$P2G_LANGUAGE['SEL_INSERTED_VALUES_LABEL'] = "Ins�r�:";
$P2G_LANGUAGE['FORM_DATA_BIND_TOOLBAR_VALUES'] = array(
	'navFirstTip' => 'Aller au premier record pr�c�dente',
	'navPrevTip' => 'Aller au record',
	'navNextTip' => 'Aller au prochain record',
	'navLastTip' => 'Aller au dernier record',
	'actNew' => 'New',
	'actNewTip' => 'Cr�ez une record nouvelle',
	'actEdit' => 'Editer',
	'actEditTip' => 'Editez la record actuelle',
	'actDel' => 'D�truire',
	'actDelTip' => 'D�truire l\'action actuelle',
	'actSave' => 'Sauvegarder',
	'actSaveTip' => 'Sauvegarder l\'action actuelle',
	'actCancel' => 'Annuler',
	'actCancelTip' => 'Annuler l\'action actuelle',
	'sortAsc' => 'L\'ordre ascendant',
	'sortDesc' => 'L\'ordre descendant',
	'sortChoose' => 'Choisissez un champ d\'ordonner',
	'sortTit' => 'Ordonner',
	'sortFirst' => 'Ordonner les champs',
	'gotoTip' => 'Donnez la nombre de la record et cliquez Allez',
	'goto' => 'Aller',
	'gotoBtnTip' => 'Cliquez � aller � la record choisi',
	'gotoTit' => 'Aller �',
	'filter' => 'Filtrer',
	'filterTip' => 'Choisissez the filter champ d\'appliquer',
	'filterVTip' => 'Choisissez la valeur du filtre',
	'filterBtnTip' => 'Cliquez ici de filtrer',
	'filterTit' => 'Filtrer',
	'filterFirst' => 'Filtrer les champs'
);
$P2G_LANGUAGE['REPORT_EMPTY_VALUES'] = array(
	'emptyMsg' => 'The requ�te  ou les filtres de recherche a r�tourn� un set vide',
	'backLink' => 'Retourner au document'
);
$P2G_LANGUAGE['REPORT_ORDER_TIP'] = "Ordonner par %s";
$P2G_LANGUAGE['REPORT_FUNCTION_MESSAGES'] = array(
	'firstTit' => 'Premi�re',
	'firstTip' => 'Aller � la premi�re page',
	'prevTit' => 'Pr�c�dente',
	'prevTip' => 'Aller � la page pr�c�dente',
	'prevScrTit' => '%d � l\'envers',
	'prevScrTip' => '%d Pages � l\'envers dans le document',
	'pageTip' => 'Aller � la %de page de %d',
	'nextTit' => 'Suivante',
	'nextTip' => 'Aller � la page suivante',
	'nextScrTit' => '%d en avant',
	'nextScrTip' => '%d Pages en avant dans le document',
	'lastTit' => 'Derni�re',
	'lastTip' => 'Aller � la derni�re page',
	'rowCount' => '%d record(s) trouv�(s)',
	'rowsPerPage' => '%d r�sultat(s) sur chaque page',
	'thisPage' => 'Page %d de %d',
	'rowInterval' => 'Enumerer %d-%d de %d r�sultats',
	'goTo' => 'Aller � la page'
);
$P2G_LANGUAGE['REPORT_SEARCH_VALUES'] = array(
	'searchTit' => 'Recherche',
	'fieldFirst' => 'Choisir un champ',
	'btnAdd' => 'Ajouter',
	'radioAny' => 'N\'importe quel filter',
	'radioAll' => 'Tous les filtres',
	'btnClear' => 'Supprimer',
	'btnView' => 'Vue',
	'btnSend' => 'Recherche'
);
$P2G_LANGUAGE['REPORT_SEARCH_INI_OP'] = array(
	'=' => 'Egal �',
	'!=' => 'Pas �gal �',
	'LIKE' => 'Contenir',
	'NOT LIKE' => 'Ne pas contenir',
	'LIKEI' => 'Commencer avec',
	'LIKEF' => 'Finir avec'
);
return $P2G_LANGUAGE;
?>