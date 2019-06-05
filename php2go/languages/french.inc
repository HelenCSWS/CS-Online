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

$P2G_LANGUAGE['ERR_OLD_PHP_VERSION'] = "PHP2Go ne marche pas avec la configuration actuelle de la serveur.<BR>Votre version PHP est <I>%s</I>. Les besoins minimals du framework est <I>4.1.0</I>.<BR>Installer une version égale ou supérieure de marcher normalement.";
$P2G_LANGUAGE['ERR_UNSUPPORTED_EXTENSION'] = "L'extension <I>%s</I> n'est pas supporté par l'installation PHP actuelle.";
$P2G_LANGUAGE['ERR_OBJ_MESSAGE'] = "Erreur dans la classe %s:<BR>%s";
$P2G_LANGUAGE['ERR_SCRIPT_MESSAGE'] = "Erreur dans le script:<BR>%s";
$P2G_LANGUAGE['ERR_DATABASE'] = "Error dans la base de données";
$P2G_LANGUAGE['ERR_MISSING_DATABASE_PARAMETERS'] = "Ce n'était pas possible de charger les paramètres de la connexion de base de données! [ID: %s]";
$P2G_LANGUAGE['ERR_INVALID_DATABASE_PARAMETERS'] = "La connexion des paramètres de base de données sont invalide! Les entrées USER, BASE et TYPE sont obligatoire. [ID: %s]"; 
$P2G_LANGUAGE['ERR_DATABASE_CONNECTION_FAILED'] = "Ce n'était pas possible à connecter à la base de données avec la configuration d'accès actuelle. Sont-ils correctes?";
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
$P2G_LANGUAGE['ERR_ABSOLUTE_URI_NOT_FOUND'] = "L'entrée 'ABSOLUTE_URI' n'était pas fixer dans la tableau globale de configurations!";
$P2G_LANGUAGE['ERR_CONFIG_ENTRY_NOT_FOUND'] = "L'entrée <I>%s</I> n'était pas fixé dans le tableau globale de configurations!"; 
$P2G_LANGUAGE['ERR_INVALID_LANGDOMAIN_FILE'] = "La fichier de messages pour la domaine %s, langage %s, est invalide ou n'exist pas.";
$P2G_LANGUAGE['ERR_CANT_FIND_CFG_VAL'] = "Ce n'était pas possible de trouver l'entrée <I>%s</I> dans la tableau globale de configurations. Etait-il fixé correctement dans l'installation?";
$P2G_LANGUAGE['ERR_CANT_LOAD_MODULE'] = "Ce n'était pas possible de charger le module <I>%s</I>";
$P2G_LANGUAGE['ERR_CANT_LOAD_DIR'] = "Ce n'était pas possible de charger les modules d'un dossier valide à <I>%s</I>";
$P2G_LANGUAGE['ERR_CANT_LOAD_DIR_MODULE'] = "Ce n'était pas possible de charger la fichier <I>%s</I> à <I>%s</I>";
$P2G_LANGUAGE['ERR_CANT_CONVERT_DATE'] = "Ce n'était pas possible de convertir le date à la forme %s.";
$P2G_LANGUAGE['ERR_CANT_RUN_ON_WINDOWS'] = "La classe %s ne marche pas sur Windows!";
$P2G_LANGUAGE['ERR_UNDEFINED_FUNCTION'] = "La fonction %s n'existe pas!";
$P2G_LANGUAGE['ERR_UNDEFINED_METHOD'] = "La méthode %s n'est pas défini dans la classe %s!";
$P2G_LANGUAGE['ERR_ABSTRACT_CLASS'] = "La classe <I>%s</I> est abstraite et vous ne pouvez pas crée une objet de cette classe!";
$P2G_LANGUAGE['ERR_DATE_FORMAT'] = "La date %s n'est pas sous la forme requis %s.";
$P2G_LANGUAGE['ERR_INVALID_RESOURCE'] = "La variable %s envoyé à la fonction ou méthode %s n'est pas une ressource valide.";
$P2G_LANGUAGE['ERR_INVALID_AUTHENTICATOR'] = "L'authenticator %s n'est pas une sousclasse de php2go.net.Auth!";
$P2G_LANGUAGE['ERR_INVALID_AUTHENTICATOR_PATH'] = "Ce n'était pas possible d'importer et/ou créer un instance du authenticator %s!";
$P2G_LANGUAGE['ERR_INVALID_USERCONTAINER'] = "Le conteneur d'utilisateur %s n'est pas une sousclass de  php2go.auth.User!";
$P2G_LANGUAGE['ERR_INVALID_USERCONTAINER_PATH' ] = "Ce n'était pas possible d'importer et/ou créer un instance du conteneur d'utilisateur %s!";
$P2G_LANGUAGE['ERR_XML_PARSE'] = "Une erreur s'est passé en analysant la fichier XML:<BR>Erreur: %s<BR>Ligne: %s, Colonne: %s";
$P2G_LANGUAGE['ERR_STATEMENT_EVAL'] = "Un erreur s'est passé en analysant le code PHP <I>%s</I>";
$P2G_LANGUAGE['ERR_CANT_FIND_FILE'] = "Ce n'était pas possible de trouver la fichier ou dossier %s. Existe-t-il?";
$P2G_LANGUAGE['ERR_CANT_OPEN_FILE'] = "Ce n'était pas possible d'ouvrir la fichier %s avec la mode %s. Vérifier les permissions entrées/sorties et the used mode.";
$P2G_LANGUAGE['ERR_CANT_CREATE_FILE'] = "Ce n'était pas possible de créer la fichier ou dossier %s. Vérifier les permissions entrées/sorties de la dossier parent.";
$P2G_LANGUAGE['ERR_CANT_READ_FILE'] = "Ce n'était pas possible d'ouvrir la fichier %s pour lire. Existe-t-il? Est-ce que le chemin correcte?";
$P2G_LANGUAGE['ERR_CANT_WRITE_FILE'] = "Ce n'était pas possible d'ouvrir la fichier ou dossier %s pour écrire. Avez-vous la permission de l'écraser?";
$P2G_LANGUAGE['ERR_CANT_DELETE_FILE'] = "Ce n'était pas possible de detruire la fichier %s.<BR>Avez-vous la permission d'écrire à cette dossier? Est-ce que la fichier a la permission d'être détruire?";
$P2G_LANGUAGE['ERR_CANT_INCLUDE_FILE'] = "Ce n'était pas possible d'ouvrir la fichier %s pour inclure. Existe-t-il? Est-ce que le chemin correcte?";
$P2G_LANGUAGE['ERR_CANT_CHANGE_MODE'] = "Ce n'était pas possible d'appliquer la mode %s à la fichier ou dossier %s.";
$P2G_LANGUAGE['ERR_CANT_READ_DIR'] = "Ce n'était pas possible de trouver la dossier %s. Existe-t-il?";
$P2G_LANGUAGE['ERR_CANT_OPEN_DIR'] = "Ce n'était pas possible d'ouvrir la dossier %s pour lire. Vérifier ses permissions.";
$P2G_LANGUAGE['ERR_CANT_FIND_SERIALIZATION_FILE'] = "Ce n'était pas possible d'ouvrir la fichier ce qui contenant l'objet sérialisé pour lire. Est-ce que le chemin correcte? Etait la fichier crée?";
$P2G_LANGUAGE['ERR_EMPTY_INSERT_QUERY'] = "La requête inséré dans la table '%s' retourné un set vide d'utiliser les valeurs fournies.";
$P2G_LANGUAGE['ERR_EMPTY_UPDATE_QUERY'] = "La requête actualisé dans la table '%s' avec la clause %s retourné un set vide d'utiliser les valeurs fournies.";
$P2G_LANGUAGE['ERR_EMPTY_TOGLOBALS_QUERY'] = "La requête %s, utilisé de publier les variables dans une in the global scope, retourné un set vide.";
$P2G_LANGUAGE['ERR_TOGLOBALS_WRONG_USAGE'] = "La requête passé à la méthode Db::toGlobals() doit commencer avec 'SELECT' (DQL).";
$P2G_LANGUAGE['ERR_EMPTY_TEMPLATE_FILE'] = "Ce n'était pas possible d'initialiser le gabarit avec un nom de fichier vide!";
$P2G_LANGUAGE['ERR_EMPTY_TEMPLATE_CONTENT'] = "Le gabarit n'était pas préparé, parce que son contenu est vide. Vérifier le contenu passé au constructeur.";
$P2G_LANGUAGE['ERR_CANT_FIND_VARIABLE'] = "Ce n'était pas possible de trouver la variable %s dans le gabarit <I>%s</I>. <BR>Recevoir des résultats correctes de cet outil, insérez {%s} dans la code du gabarit.";
$P2G_LANGUAGE['ERR_CANT_FIND_BLOCK'] = "Ce n'était pas possible de trouver le bloc <I>%s</I> pour création. Est-il declaré dans le gabarit?";
$P2G_LANGUAGE['ERR_CANT_FIND_BLOCK_REUSE'] = "Ce n'était pas possible de trouver le bloc %s de réutiliser comme %s.";
$P2G_LANGUAGE['ERR_CANT_PUT_ON_PREPARED_TEMPLATE'] = "L'elément %s était déjà configuré with the méthode DocumentElement::parse(). Ce n'était pas possible d'ajouter des eléments avec la méthode put().";
$P2G_LANGUAGE['ERR_TEMPLATE_NOT_PREPARED'] = "Le gabarit %s n'était pas préparé. Gèrez la méthode %s::parse(); avant exécutez les opérations with the object.";
$P2G_LANGUAGE['ERR_TEMPLATE_ALREADY_PREPARED'] = "Ce gabarit avait déjà préparé. Vous devez appeler le méthode parse() seulement une fois."; 
$P2G_LANGUAGE['ERR_UNBALANCED_BLOCKDEF'] = "La définition du bloc <I>%s</I> est désaxée.";
$P2G_LANGUAGE['ERR_INVALID_MODIFIER'] = "Le modificateur <I>%s</I> est invalide ou n'existe pas!";
$P2G_LANGUAGE['ERR_EMPTY_DOC_LAYOUT'] = "Il n'y a pas de eléments défini dans la plan de document fourni.";
$P2G_LANGUAGE['ERR_CANT_REPLICATE_ROOT_BLOCK'] = "Le bloc de base d'un gabarit ne peut pas être écrasé.";
$P2G_LANGUAGE['ERR_MUST_BE_POSITIVE'] = "La valeur de %s doit être supérieur à zéro dans la fonction %s.";
$P2G_LANGUAGE['ERR_MUST_BE_AN_ARRAY'] = "La valeur de %s doit être un tableau en la fonction %s";
$P2G_LANGUAGE['ERR_VALUE_OUT_OF_BOUNDS'] = "La valeur de '%s' is out of the allowed bounds: %s and %s";
$P2G_LANGUAGE['ERR_URL_MALFORMED'] = "La valeur de <I>%s</I> n'est pas une URL valide!";
$P2G_LANGUAGE['ERR_HOST_INVALID'] = "<I>%s</I> n'est pas une valide hôte de connexion!";
$P2G_LANGUAGE['ERR_DNS_NOT_FOUND'] = "Ce n'était pas possible de trouver une DNS valide à la domaine %s.";
$P2G_LANGUAGE['ERR_SESSION_PROPERTY_NOT_FOUND'] = "La propriété %s de la variable de la session %s n'était pas trouvé. Exécutez la fonction createProperty() ";
$P2G_LANGUAGE['ERR_SESSION_TIMECOUNTER_NOT_FOUND'] = "Le minuteur %s de la variable de la session %s n'était pas trouvé. Exécutez la fonction createTimeCounter() ";
$P2G_LANGUAGE['ERR_INVALID_OBJECT'] = "L'object passé comme paramètre n'est pas un instance valide de la classe <I>%s</I>!";
$P2G_LANGUAGE['ERR_CALCULATOR_MISSING_PARAMETERS'] = "Paramètres insuffisantes de construire la calcultrice!";
$P2G_LANGUAGE['ERR_COLORSEL_MISSING_PARAMETERS'] = "Paramètres insuffisantes de construire la sélection de la couleur!";
$P2G_LANGUAGE['ERR_INVALID_FORM_METHOD'] = "La mèthode %s n'est pas valide de sousmettre la forme '%s'";
$P2G_LANGUAGE['ERR_INVALID_VALIDATE_FUNC'] = "<I>%s</I> n'est pas un nom valide pour un fonction de validation !";
$P2G_LANGUAGE['ERR_FORM_DUPLICATED_FIELD'] = "Le champ <I>%s</I> est déjà défini dans la forme <I>%s</I>!";
$P2G_LANGUAGE['ERR_FORM_DUPLICATED_BUTTON'] = "Le bouton <I>%s</I> déja existe dans la forme <I>%s</I>!";
$P2G_LANGUAGE['ERR_FORM_DUPLICATED_SECTION'] = "The section ID <I>%s</I> était déjà défini dans la forme <I>%s</I>!";
$P2G_LANGUAGE['ERR_FORM_SECTION_EVALFUNC'] = "Ce n'était possible de la visibilité de la section <I>%s</I>, parce que la fonction <I>%s</I> n'est pas défini!";
$P2G_LANGUAGE['ERR_FORM_SECTION_TPLBLOCK'] = "La section <I>%s</I> est conditionel. Vouz devez déclarer un bloc nommé <I>%s</I> dans le gabarit contenu the section champs!";
$P2G_LANGUAGE['ERR_FORM_FIELD_TITLE'] = "Cette forme contient les erreurs suivantes:";
$P2G_LANGUAGE['ERR_FORM_FIELD_INVALID_INPUT'] = "Le champ %s contient une valuer invalide %s.";
$P2G_LANGUAGE['ERR_FORM_FIELD_INVALID_FLOAT'] = "La précision numerique du champ %s est invalide.\\nLes tailles maximum sont %d pour la partie entier et %d pour la partie décimale.";
$P2G_LANGUAGE['ERR_FORM_FIELD_ALPHANUM'] = "Le champ %s doit contenir seulement les caractères alphanumeriques.";
$P2G_LANGUAGE['ERR_FORM_FIELD_CHOICE'] = "Le champ %s doit contenir une des valeurs suivantes: <I>%s</I>";
$P2G_LANGUAGE['ERR_FORM_FIELD_CREDITCARD'] = "Le champ %s contient un nombre de carte de crédit invalide %s.";
$P2G_LANGUAGE['ERR_FORM_FIELD_MIN_LENGTH'] = "Le champ %s doit avoir au moins %s caractères.";
$P2G_LANGUAGE['ERR_FORM_FIELD_MAX_LENGTH'] = "Le champ %s must have until %s caractères.";
$P2G_LANGUAGE['ERR_FORM_FIELD_REQUIRED'] = "Le champ %s est obligatoire.";
$P2G_LANGUAGE['ERR_FORM_FIELD_REGEX'] = "Le champ %s a une valeur incorrecte!";
$P2G_LANGUAGE['ERR_FORM_FIELD_EQ'] = "Le champ %s doit être égal au champ %s!";
$P2G_LANGUAGE['ERR_FORM_FIELD_NEQ'] = "Le champ %s ne peut pas égal au champ %s!";
$P2G_LANGUAGE['ERR_FORM_FIELD_GT'] = "Le champ %s doit être supérieur au champ %s!";
$P2G_LANGUAGE['ERR_FORM_FIELD_LT'] = "Le champ %s doit être inférieur que champ %s!";
$P2G_LANGUAGE['ERR_FORM_FIELD_GOET'] = "Le champ %s doit être supérieur ou égal au champ %s!";
$P2G_LANGUAGE['ERR_FORM_FIELD_LOET'] = "Le champ %s doit être inférieur ou égal au champ %s!";
$P2G_LANGUAGE['ERR_FORM_FIELD_VALUE_EQ'] = "Le champ %s doit être égal à %s!";
$P2G_LANGUAGE['ERR_FORM_FIELD_VALUE_NEQ'] = "Le champ %s can't be ou égal à %s!";
$P2G_LANGUAGE['ERR_FORM_FIELD_VALUE_GT'] = "Le champ %s doit être supérieur à %s!";
$P2G_LANGUAGE['ERR_FORM_FIELD_VALUE_LT'] = "Le champ %s doit être inférieur à %s!";
$P2G_LANGUAGE['ERR_FORM_FIELD_VALUE_GOET'] = "Le champ %s doit être supérieur ou égal à %s!";
$P2G_LANGUAGE['ERR_FORM_FIELD_VALUE_LOET'] = "Le champ %s doit être inférieur ou égal à %s!";
$P2G_LANGUAGE['ERR_FORM_INVALID_MASK'] = "The mask <I>%s</I>, sur le champ <I>%s</I> n'est pas valide!";
$P2G_LANGUAGE['ERR_FORM_INVALID_FIELDTYPE'] = "Le champ type <I>%s</I> n'est pas valide!";
$P2G_LANGUAGE['ERR_FORM_CAPTCHA'] = "Le code éntré dans le champ %s ne coordone pas le contenu de l'image!";
$P2G_LANGUAGE['ERR_DATAGRID_INVALID_FIELDTYPE'] = "Le champ type <I>%s</I> ne peut pas utiliser dans un <I>grid</I>!";
$P2G_LANGUAGE['ERR_DATAGRID_INVALID_CELLSIZES'] = "La valeur du attribut <I>CELLSIZES</I> dans le champ <I>%s</I> est invalide. Vous devez fournir N+1 tailles (N=FIELDSET size) et la somme des tailles doit être égal à 100!";
$P2G_LANGUAGE['ERR_DATAGRID_INVALID_FIELDCOUNT'] = "The query defined in the <I>DATASOURCE</I> du champ <I>%s</I> doit retourné N+2 colonnes (N=FIELDSET size)";
$P2G_LANGUAGE['ERR_FORM_WRONG_LISTENER'] = "Les attributs de la contrôleur d'événements <I>%s</I> sont invalides ou incomplets!";
$P2G_LANGUAGE['ERR_FORM_WRONG_RULE'] = "Les attributs de la régle <I>%s</I> sont invalides ou incomplets!";
$P2G_LANGUAGE['ERR_INVALID_DATASOURCE_SYNTAX'] = "La spécification de la marqueur DATASOURCE dans le champ <I>%s</I> est invalide.<BR>Les noeuds enfants KEYFIELD et LOOKUPTABLE sont obligatoiresy!";
$P2G_LANGUAGE['ERR_MISSING_GROUPFIELD_CHILDREN'] = "La spécification du champ <I>%s</I> est incomplet. Vous devez fournir au moins noeud enfant &lt;OPTION&gt; !";
$P2G_LANGUAGE['ERR_MISSING_OPTION_VALUE'] = "Le attribut VALUE de l'option au indice <I>%d</I> dans le champ <I>%s</I> est manquant!";
$P2G_LANGUAGE['ERR_MISSING_DBRADIOFIELD_DATASOURCE'] = "Il n'était pas défini un marquer DATASOURCE tag au DBRADIOFIELD <I>%s</I>!";
$P2G_LANGUAGE['ERR_EMPTY_DBRADIOFIELD_RESULTS'] = "La requête fournie dans l'élément DATASOURCE element of the DBRADIOFIELD <I>%s</I> returned an empty set!";
$P2G_LANGUAGE['ERR_MISSING_COMBOOPTION_VALUE'] = "L'attribut VALUE de l'option à indice %d dans le COMBOFIELD <I>%s</I> est manquant!";
$P2G_LANGUAGE['ERR_MISSING_EDITSELECTION_CHILDREN'] = "The spécification du EDITSELECTIONFIELD <I>%s</I> est incomplet. Les noeuds enfants EDITFIELD et LOOKUPFIELD sont obligatoires!";
$P2G_LANGUAGE['ERR_MISSING_LOOKUPSELECTION_CHILDREN'] = "The spécification du LOOKUPSELECTIONFIELD <I>%s</I> a tort. Le noeud doit avoir deux enfants LOOKUPFIELD!";
$P2G_LANGUAGE['ERR_MISSING_SOURCELOOKUP_DATASOURCE'] = "Il n'y a aucun DATASOURCE défini dans le premier enfant LOOKUPFIELD du LOOKUPSELECTIONFIELD <I>%s</I>!";
$P2G_LANGUAGE['ERR_MISSING_QUERY_ELEMENTS'] = "Les items \$fields et \$tables sont obligatoires dans la construction de la rêquete SQL!";
$P2G_LANGUAGE['ERR_INVALID_DATAGRID_STRUCTURE'] = "La structure de la grille <I>%s</I> est invalide. Vouz devez déclarer un <I>DATASOURCE</I> et un <I>FIELDSET</I> contenir au moins un champ!";
$P2G_LANGUAGE['ERR_MISSING_RANGEFIELD_CHILDREN'] = "La spécification du RANGEFIELD <I>%s</I> a tort. Le noeud doit avoir deux enfants EDITFIELD!";
$P2G_LANGUAGE['ERR_EDITSEARCH_INVALID_DATAFILTER'] = "La spécification du DATAFILTER <I>%s</I> est invalide.";
$P2G_LANGUAGE['ERR_EDITSEARCH_INVALID_DATAFILTER_MASK'] = "Le masque assigné au DATAFILTER <I>%s</I> est invalide.";
$P2G_LANGUAGE['ERR_EDITSEARCH_DUPLICATED_DATAFILTER'] = "Déjà existe un autre DATAFILTER avec ID <I>%s</I>.";
$P2G_LANGUAGE['ERR_MISSING_EDITSEARCH_CHILDREN'] = "La spécification du  EDITSEARCHFIELD <I>%s</I> est incomplet. Vous devez fournir au moins un DATAFILTER, un DATASOURCE et un LOOKUPFIELD!"; 
$P2G_LANGUAGE['ERR_CSV_DB_JSRS'] = "Ce n'était pas possible de compléter l'opération!";
$P2G_LANGUAGE['ERR_SEARCHFORM_EMPTY'] = "La recherche ne pouvait pas effectué parce que tous les champs de recherche sont vides.";
$P2G_LANGUAGE['ERR_SEARCHFORM_INVALID'] = "La recherche ne pouvait pas effectué car les valeurs de filtres sont vides ou plus courtes que %d caratères.";
$P2G_LANGUAGE['ERR_REPORT_MAX_GROUP_COLS'] = "The grouping columns count doit être inférieur au compte de colonnes!";
$P2G_LANGUAGE['ERR_REPORT_UNKNOWN_GROUP_COL'] = "La colonne <I>%s</I> n'est pas défini dans la résultat de la requête!";
$P2G_LANGUAGE['ERR_REPORT_MISSING_BLOCK_VARIABLE'] = "La variable <I>%s</I> de la bloc <I>%s</I> n'était pas declaré dans le gabarit!";
$P2G_LANGUAGE['ERR_REPORT_INVALID_COLSIZES'] = "La valeur <I>%s</I> n'est pas une définition valide aux tailles de colonnes!";
$P2G_LANGUAGE['ERR_REPORT_COL_SIZES_SUM'] = "La somme des tailles de colonnes passé à la fonction setColumnSizes doit être égal à 100!";
$P2G_LANGUAGE['ERR_REPORT_COL_COUNT_MISMATCH'] = "Le compte de tailles colonnes fourni (%s) n'est pas égal au compte de colonnes of the report (%s, %s grouping)";
$P2G_LANGUAGE['ERR_REPORT_MISSING_SEARCH_VARIABLE'] = "Ce n'était pas possible de consruire la forme de recherche, parce que la variable <I>%s</I> n'était pas trouvé. <BR>Déclarez {%s} dans le gabarit du document à continuer.";
$P2G_LANGUAGE['ERR_REPORT_SEARCH_PARS_MALFORMED'] = "Vous devez fournir le Label, le Field Name et la Mask dans la construction des paramètres de recherche!";
$P2G_LANGUAGE['ERR_REPORT_SEARCH_INVALID_MASK'] = "La masque <I>%s</I> du paramètre de recherche  <I>#%d</I> est invalide!";
$P2G_LANGUAGE['ERR_REPORT_MIN_ALT_STYLE'] = "La fonction setAlternateStyle doit recevoir au moins deux paramètres (CSS names)!";
$P2G_LANGUAGE['ERR_REPORT_HIDDEN_GROUP'] = "Une colonne cachée (%s) ne peut pas être inclut des titres de groupe!";
$P2G_LANGUAGE['ERR_REPORT_GROUP_HIDDEN'] = "Une colonne displayed in the group headers (%s) ne peut pas être défini comme cachée!";
$P2G_LANGUAGE['ERR_REPORT_MAX_HIDDEN_COLS'] = "La nombre des colonnes cachées doit être smaller than the requête  columns count!";
$P2G_LANGUAGE['ERR_REPORT_UNKNOWN_HIDDEN_COL'] = "La colonne <I>%s</I> qui est défini comme cachée n'existe pas dans XXXX the requête  result set!";
$P2G_LANGUAGE['ERR_DATASET_INVALID_TYPE'] = "Le type <I>%s</I> n'est pas un type valide de DataSet!";
$P2G_LANGUAGE['ERR_MENU_WRONG_ROOT_SQL'] = "The requête gardé dans le paramètre \$rootSql de la méthode Menu::loadFromDatabase() retourné un set vide ou elle n'a pas les champs requis et minumums (Index and Label)!";
$P2G_LANGUAGE['ERR_MENU_WRONG_CHILDREN_STATEMENT'] = "The requête passé dans le paramètre \$childSql de la méthode Menu::loadFromDatabase() est invalide.<BR>Fournissez une expression qui contenu le '~var~' (Relation Column) plus les deux champs requis (Index and Label)";
$P2G_LANGUAGE['ERR_MENU_NOT_FOUND'] = "Le menu nétait pas crée. Construisez les items du menu utilisant Menu::loadFromDatabase() ou Menu::loadFromXmlFile().";
$P2G_LANGUAGE['ERR_MENU_EMPTY_XML_ROOT'] = "Le noeud racine de la fichier XML n'a aucun enfants!";
$P2G_LANGUAGE['ERR_MENU_INVALID_IMAGE'] = "L'image <I>%s</I> n'est pas valide. Les valeurs possibles sont: <I>%s</I>.";
$P2G_LANGUAGE['ERR_FTP_MISSING_HOST'] = "Ce n'était pas possible d'ouvrir la connexion. Vous devez établir la connexion, utilisant les méthode FtpClient::setServer() et FtpClient::setUserInfo()";
$P2G_LANGUAGE['ERR_FTP_MISSING_USER_OR_PASS'] = "Ce n'était pas possible d'authentifier. Une des valuers d'usager (nom d'usager ou mot de passe) n'était pas fournie.";
$P2G_LANGUAGE['ERR_SPRSH_OUT_OF_BOUNDS'] = "Le limites de spreadsheet était surpassé: [%d;%d]";
$P2G_LANGUAGE['ERR_SPRSH_MAX_NOTE_CHARS'] = "La taille maximum pour un commentaire de la cellule était surpassé<BR>Valeur: %s";
$P2G_LANGUAGE['ERR_SPRSH_MAX_FONTS'] = "La maximum de quatre polices pour chaque spreadsheet était surpassé!";
$P2G_LANGUAGE['ERR_UPLOAD_NOT_FOUND'] = "Ce n'était pas possible de compléter the upload. The file wasn't transferred correctly.";
$P2G_LANGUAGE['ERR_UPLOAD_TOO_BIG'] = "Ce n'était pas possible de compléter the upload. La taille de la fichier est supérieur à la taille maximum permis.";
$P2G_LANGUAGE['ERR_UPLOAD_INVALID_NAME'] = "Ce n'était pas possible de compléter la transmission de la fichier. Le nom de fichier %s est invalide.";
$P2G_LANGUAGE['ERR_UPLOAD_INVALID_TYPE'] = "Ce n'était pas possible de compléter la transmission de la fichier. L'extension %s n'est pas permis.";
$P2G_LANGUAGE['ERR_UPLOAD_FILE_EXISTS'] = "Ce n'était pas possible de compléter la transmission de la fichier. La fichier <I>%s</I> déjà existe et n'était pas écrasé.";
$P2G_LANGUAGE['ERR_UPLOAD_CANT_MOVE'] = "Ce n'était pas possible de déplacer la fichier temporaire à la dossier destinaire.";
$P2G_LANGUAGE['ERR_CANT_OPEN_SOCKET'] = "Ce n'était pas possible d'ouvrir le socket utilisant le port %s avec la hôte %s!<BR>Erreur %d - %s";
$P2G_LANGUAGE['ERR_SOCKET_NOT_CONNECTED'] = "La connection avec le socket n'est pas active!";
$P2G_LANGUAGE['ERR_SOCKET_EOF'] = "Fin de fichier attendu, l'opération ne peut pas être accompli!";
$P2G_LANGUAGE['ERR_SOCKET_TIMEOUT'] = "La temporisation socket était surpassé. L'opération ne peut pas être accompli!";
$P2G_LANGUAGE['ERR_MAIL_MESSAGE_NOT_BUILT'] = "Le message nétait pas contruit correctement et ne peut pas être envoyé!<BR>Gérez la méthode build().";
$P2G_LANGUAGE['ERR_MAIL_EMPTY_RCPT'] = "Il n'y a aucun destinataires pour le message!";
$P2G_LANGUAGE['ERR_SIGNED_MESSAGE_SIGN'] = "Ce n'était pas possible de signer le contenu de le message.<BR>%s";
$P2G_LANGUAGE['ERR_SMTP_CONNECTION'] = "Erreur en connectant à la serveur SMTP %s!<BR>Errno: [%d]<BR>Errmsg: %s";
$P2G_LANGUAGE['ERR_SMTP_COMMAND'] = "Erreur en exécutant la commande %s sur la serveur SMTP!<BR>Errno: [%d]<BR>Errmsg: %s";
$P2G_LANGUAGE['ERR_SMTP_AUTHENTICATE'] = "Ce n'était pas possible d'authentifier avec la serveur SMTP!";
$P2G_LANGUAGE['ERR_CRYPT_MISSING_KEY'] = "Crypter ou décrypter données, vous devez fournir un clé avec méthode Crypt::setKey()!";
$P2G_LANGUAGE['ERR_CRYPT_OPEN_MODULE'] = "Ce n'était pas possible to initialiser le cryptogramme <I>%s</I> utilisant la mode %s!";
$P2G_LANGUAGE['ERR_OPENSSL_PUBKEY_ENCRYPT'] = "Ce n'était pas possible d'extraire une clé publique de crypter les données. Fournissez un fichier contenu un certificat valide à la méthode OpenSSLCrypt::setCertificatePath()!";
$P2G_LANGUAGE['ERR_OPENSSL_PUBKEY_VERIFY'] = "Ce n'était pas possible d'extraire une clé publique de vérifier la signature inclu dans cette données. Fournissez un fichier contenu un certificat valide à la méthode OpenSSLCrypt::setCertificatePath()!"; 
$P2G_LANGUAGE['ERR_OPENSSL_PRIVKEY_DECRYPT'] = "Ce n'était pas possible d'extraire une clé privé de décrypter les données. Fournissez un fichier qui contient la clé privé et la phrase de passe, si la clé l'a besoin.";
$P2G_LANGUAGE['ERR_OPENSSL_PRIVKEY_SIGN'] = "Ce n'était pas possible d'extraire une clé privé to generate the digital signature. Fournissez un fichier qui contient une clé privé et la phrase de passe, si la clé crée l'a besoin.";
$P2G_LANGUAGE['ERR_OPENSSL_CERT_PATH'] = "Le chemin d'accès du certificat est invalide!";
$P2G_LANGUAGE['ERR_OPENSSL_READ_CERT'] = "Ce n'était pas possible de lire le contenu du certificat %s!";
$P2G_LANGUAGE['ERR_POP3_CONNECTION'] = "Erreur en connectant à la serveur POP3 %s!<BR>Errno: [%d]<BR>- Errmsg: %s";
$P2G_LANGUAGE['ERR_POP3_COMMAND'] = "Erreur en exécutant la commande %s sur la serveur POP!<BR>Errmsg: %s";
$P2G_LANGUAGE['ERR_POP3_AUTHENTICATE'] = "Ce n'était pas possible d'authentifier avec la serveur POP3!";
$P2G_LANGUAGE['ERR_POP3_APOP'] = "Ce n'était pas possible gérer la commande APOP : il n'y a pas de bannière d'authentification !";
$P2G_LANGUAGE['ERR_HTTP_CONNECT'] = "Ce n'était pas possible d'établir un connexion HTTP avec la hôte <I>%s</I>!";
$P2G_LANGUAGE['ERR_HTTP_MISSING_HOST'] = "La hôte HTTP n'était pas fixé. Gérez la méthode HttpClient::setHost().";
$P2G_LANGUAGE['ERR_CANT_INSTANTIATE_VALIDATOR'] = "Ce n'était pas possible créer une instance de la classe de validation <B>%s</B>!";
$P2G_LANGUAGE['ERR_INVALID_VALIDATOR'] = "La classe de validation <I>%s</I> est invalide! La méthode %s::execute() ne peut pas être trouvé.";
$P2G_LANGUAGE['ERR_ASSERTION_MESSAGE'] = "L'expression évalué n'a pas donné le résultat prévu.";
$P2G_LANGUAGE['ERR_INVALID_CALLBACK'] = "La fonction ou mèthode <I>%s</I> n'est pas une callback valide!";
$P2G_LANGUAGE['ERR_INVALID_PROPERTIES_FILE'] = "La fichier <I>%s</I> ne contient aucune données de configuration valide!";
$P2G_LANGUAGE['ERR_INVALID_COLOR_SPEC'] = "<I>%s</I> n'est past une spécification valide de coleur.";
$P2G_LANGUAGE['ERR_ALLOCATE_COLOR'] = "Ce n'était pas possible d'attribuer une nouvelle coleur pour cette image!";
$P2G_LANGUAGE['ERR_JSRS_MISSING_HANDLER'] = "Il y a un nom de handler JSRS ou alias manquant!";
$P2G_LANGUAGE['ERR_JSRS_INVALID_HANDLER'] = "Le handler JSRS handler %s est invalide!";
$P2G_LANGUAGE['DEFAULT_BTN_VALUE'] = "Soumettre";
$P2G_LANGUAGE['CALENDAR_LINK_TITLE'] = "Ouvrir la calendrier";
$P2G_LANGUAGE['CALCULATOR_LINK_TITLE'] = "Ouvrir la calculatrice";
$P2G_LANGUAGE['CALCULATOR_WINDOW_TITLE'] = "La Calculatrice";
$P2G_LANGUAGE['CALCULATOR_RESULT_CAPTION'] = "Résultat";
$P2G_LANGUAGE['DAYS_OF_WEEK'] = array("Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi");
$P2G_LANGUAGE['DAYS_OF_WEEK_ABBR'] = array("Dim", "Lun", "Mar", "Mer", "Jeu", "Ven", "Sam");
$P2G_LANGUAGE['MONTHS_OF_YEAR'] = array("Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre");
$P2G_LANGUAGE['MONTHS_OF_YEAR_ABBR'] = array("Jan", "Fév", "Mar", "Avr", "Mai", "Juin", "Juil", "Août", "Sep", "Oct", "Nov", "Déc");
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
	'bcolor' => 'Changer la couleur arrière-plan',
	'emoticon' => 'Insérer un smiley',
	'font' => 'Police',
	'fontsize' => 'Taille',
	'left' => 'Aligner à gauche',
	'center' => 'Centrer le texte',
	'right' => 'Aligner à droite',
	'olist' => 'Insérer/Enlever Numbered List',
	'ulist' => 'Insérer/Enlever Simple List',
	'indent+' => 'Augmenter Indentation',
	'indent-' => 'Réduire Indentation',
	'link' => 'Insérer une Lien',
	'image' => 'Insérer une Image',
	'editmode' => 'Editer dans la Mode Avancée',
	'choosesmil' => 'Choisir un Smiley'
);
$P2G_LANGUAGE['LOOKUP_CHOICE_FILTER_TIP'] = "--- Filtrez la liste ---";
$P2G_LANGUAGE['LOOKUP_SELECTION_DEFAULT_SELFIRST'] = "--- Insérez les Valeurs ---";
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
$P2G_LANGUAGE['SEL_INSERTED_VALUES_LABEL'] = "Inséré:";
$P2G_LANGUAGE['FORM_DATA_BIND_TOOLBAR_VALUES'] = array(
	'navFirstTip' => 'Aller au premier record précédente',
	'navPrevTip' => 'Aller au record',
	'navNextTip' => 'Aller au prochain record',
	'navLastTip' => 'Aller au dernier record',
	'actNew' => 'New',
	'actNewTip' => 'Créez une record nouvelle',
	'actEdit' => 'Editer',
	'actEditTip' => 'Editez la record actuelle',
	'actDel' => 'Détruire',
	'actDelTip' => 'Détruire l\'action actuelle',
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
	'gotoBtnTip' => 'Cliquez à aller à la record choisi',
	'gotoTit' => 'Aller à',
	'filter' => 'Filtrer',
	'filterTip' => 'Choisissez the filter champ d\'appliquer',
	'filterVTip' => 'Choisissez la valeur du filtre',
	'filterBtnTip' => 'Cliquez ici de filtrer',
	'filterTit' => 'Filtrer',
	'filterFirst' => 'Filtrer les champs'
);
$P2G_LANGUAGE['REPORT_EMPTY_VALUES'] = array(
	'emptyMsg' => 'The requête  ou les filtres de recherche a rétourné un set vide',
	'backLink' => 'Retourner au document'
);
$P2G_LANGUAGE['REPORT_ORDER_TIP'] = "Ordonner par %s";
$P2G_LANGUAGE['REPORT_FUNCTION_MESSAGES'] = array(
	'firstTit' => 'Première',
	'firstTip' => 'Aller à la première page',
	'prevTit' => 'Précédente',
	'prevTip' => 'Aller à la page précédente',
	'prevScrTit' => '%d à l\'envers',
	'prevScrTip' => '%d Pages à l\'envers dans le document',
	'pageTip' => 'Aller à la %de page de %d',
	'nextTit' => 'Suivante',
	'nextTip' => 'Aller à la page suivante',
	'nextScrTit' => '%d en avant',
	'nextScrTip' => '%d Pages en avant dans le document',
	'lastTit' => 'Dernière',
	'lastTip' => 'Aller à la dernière page',
	'rowCount' => '%d record(s) trouvé(s)',
	'rowsPerPage' => '%d résultat(s) sur chaque page',
	'thisPage' => 'Page %d de %d',
	'rowInterval' => 'Enumerer %d-%d de %d résultats',
	'goTo' => 'Aller à la page'
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
	'=' => 'Egal à',
	'!=' => 'Pas égal à',
	'LIKE' => 'Contenir',
	'NOT LIKE' => 'Ne pas contenir',
	'LIKEI' => 'Commencer avec',
	'LIKEF' => 'Finir avec'
);
return $P2G_LANGUAGE;
?>