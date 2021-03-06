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
// Translated and maintained by: Herman Sandoval Manrique - Colombia <hfsandovalm@unal.edu.co>
//
// $Header: /www/cvsroot/php2go/languages/spanish.inc,v 1.25 2005/08/30 14:36:38 mpont Exp $
// $Revision: 1.25 $
// $Date: 2005/08/30 14:36:38 $

$P2G_LANGUAGE['ERR_OLD_PHP_VERSION'] = "PHP2Go no se puede ejecutar con la configuraci�n actual del servidor.<BR>Su versi�n actual de PHP es <I>%s</I>. El requerimiento m�nimo del framework es <I>4.1.0</I>.<BR>Instale una versi�n igual o superior para ejecutarlo normalmente.";
$P2G_LANGUAGE['ERR_UNSUPPORTED_EXTENSION'] = "La extensi�n <I>%s</I> no es soportada por la instalaci�n actual de PHP.";
$P2G_LANGUAGE['ERR_OBJ_MESSAGE'] = "Error en la clase %s:<BR>%s";
$P2G_LANGUAGE['ERR_SCRIPT_MESSAGE'] = "Error en el script:<BR>%s";
$P2G_LANGUAGE['ERR_DATABASE'] = "Error de Base de Datos";
$P2G_LANGUAGE['ERR_MISSING_DATABASE_PARAMETERS'] = "No fue posible cargar los par�metros de conexi�n de base de datos! [ID: %s]";
$P2G_LANGUAGE['ERR_INVALID_DATABASE_PARAMETERS'] = "Los par�metros de conexi�n de base de datos son inv�lidos! Las entradas USER, BASE y TYPE son obligatorias. [ID: %s]";
$P2G_LANGUAGE['ERR_DATABASE_CONNECTION_FAILED'] = "No fu� posible conectarse a la base de datos con la configuraci�n de acceso actual. Esta es correcta?";
$P2G_LANGUAGE['ERR_TYPES'] = array(
	1 => "Error Fatal",
	2 => "Aviso",
	4 => "Error de Sintaxis",
	8 => "Nota",
	16 => "Error Fatal del N�cleo",
	32 => "Aviso del N�cleo",
	64 => "Error Fatal de Compilador",
	128 => "Aviso del Compilador",
	256 => "Error de Usuario",
	512 => "Aviso de Usuario",
	1024 => "Nota de Usuario"
);
$P2G_LANGUAGE['ERR_ABSOLUTE_URI_NOT_FOUND'] = "La entrada 'ABSOLUTE_URI' no ha sido establecida en el arreglo de configuraci�n global!";
$P2G_LANGUAGE['ERR_CONFIG_ENTRY_NOT_FOUND'] = "The <I>%s</I> entry wasn't set in the global configuration array!";
$P2G_LANGUAGE['ERR_INVALID_LANGDOMAIN_FILE'] = "The messages file for the %s domain, language %s, is invalid or doesn't exist.";
$P2G_LANGUAGE['ERR_CANT_FIND_CFG_VAL'] = "No fu� posible encontrar la variable <I>%s</I> en el arreglo de configuraci�n global. Fu� configurada correctamente en la instalaci�n?";
$P2G_LANGUAGE['ERR_CANT_LOAD_MODULE'] = "No fue posible cargar el m�dulo <I>%s</I>";
$P2G_LANGUAGE['ERR_CANT_LOAD_DIR'] = "No fue posible cargar m�dulos desde una carpeta v�lida en  <I>%s</I>";
$P2G_LANGUAGE['ERR_CANT_LOAD_DIR_MODULE'] = "No fue posible cargar el archivo <I>%s</I> en <I>%s</I>";
$P2G_LANGUAGE['ERR_CANT_CONVERT_DATE'] = "No fue posible convertir la fecha al formato %s.";
$P2G_LANGUAGE['ERR_CANT_RUN_ON_WINDOWS'] = "La clase %s no se puede utilizar bajo Windows!";
$P2G_LANGUAGE['ERR_UNDEFINED_FUNCTION'] = "La funci�n %s no est� definida!";
$P2G_LANGUAGE['ERR_UNDEFINED_METHOD'] = "El m�todo %s no est� definido en la clase %s!";
$P2G_LANGUAGE['ERR_ABSTRACT_CLASS'] = "La clase <I>%s</I> es abstracta y no puede ser instanciada directamente!";
$P2G_LANGUAGE['ERR_DATE_FORMAT'] = "La fecha %s no est� en el formato requerido %s.";
$P2G_LANGUAGE['ERR_INVALID_RESOURCE'] = "La variable %s pasada a la funci�n o m�todo %s no es un recurso v�lido.";
$P2G_LANGUAGE['ERR_INVALID_AUTHENTICATOR'] = "El autenticador %s no es una subclase de php2go.net.Auth!";
$P2G_LANGUAGE['ERR_INVALID_AUTHENTICATOR_PATH'] = "No fue posible importar y/o instanciar el autenticador %s!";
$P2G_LANGUAGE['ERR_INVALID_USERCONTAINER'] = "El contenedor de usuario %s no es una subclase de php2go.auth.User!";
$P2G_LANGUAGE['ERR_INVALID_USERCONTAINER_PATH'] = "No fue posible importar y/o instanciar el contenedor de usuario %s!"; 
$P2G_LANGUAGE['ERR_XML_PARSE'] = "Ocurri� un error en el parsing del archivo XML:<BR>Error: %s<BR>L�nea: %s, Columna: %s";
$P2G_LANGUAGE['ERR_STATEMENT_EVAL'] = "Ocurri� un error en em parsing del c�digo PHP <I>%s</I>";
$P2G_LANGUAGE['ERR_CANT_FIND_FILE'] = "No fue posible encontrar el archivo o carpeta %s. Este existe?";
$P2G_LANGUAGE['ERR_CANT_OPEN_FILE'] = "No fue posible abrir el archivo %s con el modo %s. Verifique los permisos del archivo y el modo usado.";
$P2G_LANGUAGE['ERR_CANT_CREATE_FILE'] = "No fue posible crear el archivo o carpeta %s. Verifique los permisos de la carpeta padre.";
$P2G_LANGUAGE['ERR_CANT_READ_FILE'] = "No fue posible abrir el archivo %s para lectura. Este existe? La ruta es correcta?";
$P2G_LANGUAGE['ERR_CANT_WRITE_FILE'] = "No fue posible abrir el archivo o carpeta %s para escritura. Tiene permisos para sobreescribirlo?";
$P2G_LANGUAGE['ERR_CANT_DELETE_FILE'] = "No fue posible borrar el archivo %s.<BR>Tiene usted permisos de escritura en esta carpeta? El archivo tiene permiso de borrado?";
$P2G_LANGUAGE['ERR_CANT_INCLUDE_FILE'] = "No fue posible abrir el archivo %s para su inclusi�n. Este existe? La ruta es correcta?";
$P2G_LANGUAGE['ERR_CANT_CHANGE_MODE'] = "No fue posible aplicar el modo %s al archivo o carpeta %s.";
$P2G_LANGUAGE['ERR_CANT_READ_DIR'] = "No fue posible encontrar el directorio %s. Este existe?";
$P2G_LANGUAGE['ERR_CANT_OPEN_DIR'] = "No fue posible abrir la carpeta %s para lectura. Verifique sus permisos.";
$P2G_LANGUAGE['ERR_CANT_FIND_SERIALIZATION_FILE'] = "No fue posible abrir para lectura el archivo que contiene el objeto serializado. La ruta es correcta? Se cre� el archivo?";
$P2G_LANGUAGE['ERR_EMPTY_INSERT_QUERY'] = "La consulta de inserci�n en la tabla '%s' retorn� un conjunto de resultados vac�o utilizando los valores suministrados.";
$P2G_LANGUAGE['ERR_EMPTY_UPDATE_QUERY'] = "La consulta de actualizaci�n en la tabla '%s' con la condici�n %s retorn� un conjunto de resultados vac�o utilizando los valores suministrados.";
$P2G_LANGUAGE['ERR_EMPTY_TOGLOBALS_QUERY'] = "La consulta %s, utilizada para publicar variables en el ambiente retorn� un conjunto de resultados vac�o.";
$P2G_LANGUAGE['ERR_TOGLOBALS_WRONG_USAGE'] = "La consulta pasada al m�todo Db::toGlobals() deber�a comenzar con 'SELECT' (DQL).";
$P2G_LANGUAGE['ERR_EMPTY_TEMPLATE_FILE'] = "No fue posible inicializar la plantilla con un nombre de archivo vac�o!";
$P2G_LANGUAGE['ERR_EMPTY_TEMPLATE_CONTENT'] = "La plantilla no fue preparada ya que su contenido est� vac�o. Verifique el contenido pasado al constructor.";
$P2G_LANGUAGE['ERR_CANT_FIND_VARIABLE'] = "No fue posible encontrar la variable %s en la plantilla <I>%s</I>. <BR>Para obtener resultados correctos de esta herramienta, inserte {%s} en el c�digo de la plantilla.";
$P2G_LANGUAGE['ERR_CANT_FIND_BLOCK'] = "No fue posible encontrar el bloque <I>%s</I> para su creaci�n. Se declar� en la plantilla?";
$P2G_LANGUAGE['ERR_CANT_FIND_BLOCK_REUSE'] = "No fue posible encontrar el bloque %s para reusarlo como %s.";
$P2G_LANGUAGE['ERR_CANT_PUT_ON_PREPARED_TEMPLATE'] = "El elemento %s ya fue configurado con el m�todo DocumentElement::parse(). No es posible agregar elementos con el m�todo put().";
$P2G_LANGUAGE['ERR_TEMPLATE_NOT_PREPARED'] = "La plantilla %s no fue preparada. Ejecute el m�todo %s::parse(); antes de ejecutar operaciones con el objeto.";
$P2G_LANGUAGE['ERR_TEMPLATE_ALREADY_PREPARED'] = "Esta plantilla ya ha sido preparada. Usted deber�a llamar solamente una vez el m�todo parse().";
$P2G_LANGUAGE['ERR_UNBALANCED_BLOCKDEF'] = "La definici�n del bloque <I>%s</I> no est� balanceada.";
$P2G_LANGUAGE['ERR_INVALID_MODIFIER'] = "El modificador %s no es v�lido o no existe!";
$P2G_LANGUAGE['ERR_EMPTY_DOC_LAYOUT'] = "No hay elementos definidos en la capa de documento suministrada.";
$P2G_LANGUAGE['ERR_CANT_REPLICATE_ROOT_BLOCK'] = "El bloque base de una plantilla no puede ser sobreescrito.";
$P2G_LANGUAGE['ERR_MUST_BE_POSITIVE'] = "El valor de %s deber�a se mayor que cero en la funci�n %s.";
$P2G_LANGUAGE['ERR_MUST_BE_AN_ARRAY'] = "El valor de %s deber�a ser un arreglo en la funci�n %s";
$P2G_LANGUAGE['ERR_VALUE_OUT_OF_BOUNDS'] = "El valor de '%s' se encuentra fuera de los l�mites permitidos: %s y %s";
$P2G_LANGUAGE['ERR_URL_MALFORMED'] = "El valor de <I>%s</I> no es un URL v�lido!";
$P2G_LANGUAGE['ERR_HOST_INVALID'] = "<I>%s</I> no es una conexi�n a servidor v�lida!";
$P2G_LANGUAGE['ERR_DNS_NOT_FOUND'] = "No fue posible encontrar un DNS v�lido para el dominio %s.";
$P2G_LANGUAGE['ERR_SESSION_PROPERTY_NOT_FOUND'] = "No se encontr� la propiedad %s de la variable de sesi�n %s. Ejecute la funci�n createProperty()";
$P2G_LANGUAGE['ERR_SESSION_TIMECOUNTER_NOT_FOUND'] = "No se encontr� el contador de tiempo %s de la variable de sesi�n %s. Ejecute la funci�n createTimeCounter()";
$P2G_LANGUAGE['ERR_INVALID_OBJECT'] = "El objeto pasado como par�metro no es una instancia v�lida de la clase <I>%s</I>!";
$P2G_LANGUAGE['ERR_CALCULATOR_MISSING_PARAMETERS'] = "Par�metros insuficientes para construir la calculadora!";
$P2G_LANGUAGE['ERR_COLORSEL_MISSING_PARAMETERS'] = "Par�metros insuficientes para construir el selector de color!";
$P2G_LANGUAGE['ERR_INVALID_FORM_METHOD'] = "El m�todo %s no es v�lido para enviar el formulario '%s'";
$P2G_LANGUAGE['ERR_INVALID_VALIDATE_FUNC'] = "<I>%s</I> no es un nombre v�lido de funci�n de validaci�n!";
$P2G_LANGUAGE['ERR_FORM_DUPLICATED_FIELD'] = "El campo <I>%s</I> ya fue definido en el formulario <I>%s</I>!";
$P2G_LANGUAGE['ERR_FORM_DUPLICATED_BUTTON'] = "El bot�n <I>%s</I> ya ha sido definido en el formulario <I>%s</I>!";
$P2G_LANGUAGE['ERR_FORM_DUPLICATED_SECTION'] = "El ID de secci�n <I>%s</I> ya fue definido en el formulario <I>%s</I>!";
$P2G_LANGUAGE['ERR_FORM_SECTION_EVALFUNC'] = "No fue posible definir la visibilidad de la secci�n <I>%s</I>, ya que la funci�n <I>%s</I> no ha sido definida!";
$P2G_LANGUAGE['ERR_FORM_SECTION_TPLBLOCK'] = "La secci�n <I>%s</I> es condicional. Usted deber�a declarar un bloque en la plantilla que contiene los campos de secci�n llamado <I>%s</I>!";
$P2G_LANGUAGE['ERR_FORM_FIELD_TITLE'] = "El formulario contiene los siguientes errores:";
$P2G_LANGUAGE['ERR_FORM_FIELD_INVALID_INPUT'] = "El campo %s contiene un valor %s no v�lido.";
$P2G_LANGUAGE['ERR_FORM_FIELD_INVALID_FLOAT'] = "La precisi�n num�rica del campo %s no es v�lida.\\nLos tama�os m�ximos son %d para la parte entera y %d para la parte decimal.";
$P2G_LANGUAGE['ERR_FORM_FIELD_ALPHANUM'] = "El campo %s deber�a tener �nicamente caracteres alfanum�ricos.";
$P2G_LANGUAGE['ERR_FORM_FIELD_CHOICE'] = "El campo %s deber�a tener uno de los siguientes valores: <I>%s</I>.";
$P2G_LANGUAGE['ERR_FORM_FIELD_CREDITCARD'] = "El campo '%s' contiene un n�mero de tarjeta de cr�dito %s inv�lido.";
$P2G_LANGUAGE['ERR_FORM_FIELD_MIN_LENGTH'] = "El campo %s deber�a tener al menos %s caracteres.";
$P2G_LANGUAGE['ERR_FORM_FIELD_MAX_LENGTH'] = "El campo %s deber�a tener m�ximo %s caracteres.";
$P2G_LANGUAGE['ERR_FORM_FIELD_REQUIRED'] = "El campo %s es obligatorio.";
$P2G_LANGUAGE['ERR_FORM_FIELD_REGEX'] = "El campo %s tiene un valor incorrecto!";
$P2G_LANGUAGE['ERR_FORM_FIELD_EQ'] = "El campo %s debe ser igual al campo %s!";
$P2G_LANGUAGE['ERR_FORM_FIELD_NEQ'] = "El campo %s debe ser diferente al campo %s!";
$P2G_LANGUAGE['ERR_FORM_FIELD_GT'] = "El campo %s debe ser mayor que el campo %s!";
$P2G_LANGUAGE['ERR_FORM_FIELD_LT'] = "El campo %s debe ser menor que el campo %s!";
$P2G_LANGUAGE['ERR_FORM_FIELD_GOET'] = "El campo %s debe ser mayor o igual que el campo %s!";
$P2G_LANGUAGE['ERR_FORM_FIELD_LOET'] = "El campo %s debe ser menor o igual que el campo %s!";
$P2G_LANGUAGE['ERR_FORM_FIELD_VALUE_EQ'] = "El campo %s debe tener un valor igual a %s!";
$P2G_LANGUAGE['ERR_FORM_FIELD_VALUE_NEQ'] = "El campo %s debe tener un valor diferente a %s!";
$P2G_LANGUAGE['ERR_FORM_FIELD_VALUE_GT'] = "El campo %s debe tener un valor mayor a %s!";
$P2G_LANGUAGE['ERR_FORM_FIELD_VALUE_LT'] = "El campo %s debe tener un valor menor a %s!";
$P2G_LANGUAGE['ERR_FORM_FIELD_VALUE_GOET'] = "El campo %s debe tener un valor mayor o igual a %s!";
$P2G_LANGUAGE['ERR_FORM_FIELD_VALUE_LOET'] = "El campo %s debe tener un valor menor o igual a %s!";
$P2G_LANGUAGE['ERR_FORM_INVALID_MASK'] = "La m�scara <I>%s</I>, en el campo<I>%s</I>, no es v�lida!";
$P2G_LANGUAGE['ERR_FORM_INVALID_FIELDTYPE'] = "El tipo de campo <I>%s</I> no es v�lido!";
$P2G_LANGUAGE['ERR_FORM_CAPTCHA'] = "El c�digo indicado en el campo %s no coincide con el contenido de la imagen!";
$P2G_LANGUAGE['ERR_DATAGRID_INVALID_FIELDTYPE'] = "El tipo de campo <I>%s</I> no se puede utilizar dentro de una <I>rejilla</I>!";
$P2G_LANGUAGE['ERR_DATAGRID_INVALID_CELLSIZES'] = "El valor del atributo <I>CELLSIZES</I> en el campo <I>%s</I> no es v�lido. Usted debe suministrar N+1 tama�os (N=tama�o del FIELDSET) y la suma de los tama�os deber�a dar 100!";
$P2G_LANGUAGE['ERR_DATAGRID_INVALID_FIELDCOUNT'] = "La consulta definida en el <I>DATASOURCE</I> del campo <I>%s</I> debe retornar N+2 columnas (N=tama�o del FIELDSET)";
$P2G_LANGUAGE['ERR_FORM_WRONG_LISTENER'] = "Los atributos del manipulador de eventos (event handler) <I>%s</I> no son v�lidos o son incompletos!";
$P2G_LANGUAGE['ERR_FORM_WRONG_RULE'] = "Los atributos de la regla <I>%s</I> no son v�lidos o son incompletos!";
$P2G_LANGUAGE['ERR_INVALID_DATASOURCE_SYNTAX'] = "La especificaci�n de la etiqueta DATASOURCE en el campo <I>%s</I> no es v�lida.<BR>Los nodos hijos KEYFIELD y LOOKUPTABLE son obligatorios!";
$P2G_LANGUAGE['ERR_MISSING_GROUPFIELD_CHILDREN'] = "La especificaci�n del campo <I>%s</I> est� incompleta. Usted deber� suministrar al menos un nodo hijo &lt;OPTION&gt;!";
$P2G_LANGUAGE['ERR_MISSING_OPTION_VALUE'] = "Falta el atributo VALUE de la opci�n en el �ndice <I>%d</I> del campo <I>%s</I>!";
$P2G_LANGUAGE['ERR_MISSING_DBRADIOFIELD_DATASOURCE'] = "No se defini� una etiqueta DATASOURCE en el DBRADIOFIELD <I>%s</I>!";
$P2G_LANGUAGE['ERR_EMPTY_DBRADIOFIELD_RESULTS'] = "La consulta sumistrada en el elemento DATASOURCE del DBRADIOFIELD <I>%s</I> retorn� un conjunto de datos vac�o!";
$P2G_LANGUAGE['ERR_MISSING_COMBOOPTION_VALUE'] = "Falta el atribugo VALUE de la opci�n en el �ndice %d en el COMBOFIELD <I>%s</I>!";
$P2G_LANGUAGE['ERR_MISSING_EDITSELECTION_CHILDREN'] = "La especificaci�n del EDITSELECTIONFIELD <I>%s</I> est� incompleta. Los nodos hijos EDITFIELD y LOOKUPFIELD son obligatorios!";
$P2G_LANGUAGE['ERR_MISSING_LOOKUPSELECTION_CHILDREN'] = "La especificaci�n del LOOKUPSELECTIONFIELD <I>%s</I> es err�nea. El nodo deber�a tener dos hijos LOOKUPFIELD!";
$P2G_LANGUAGE['ERR_MISSING_SOURCELOOKUP_DATASOURCE'] = "No se defini� DATASOURCE en el primer hijo LOOKUPFIELD del LOOKUPSELECTIONFIELD <I>%s</I>!";
$P2G_LANGUAGE['ERR_MISSING_QUERY_ELEMENTS'] = "Los elementos \$fields y \$tables son obligatorios en la construcci�n de la consulta SQL!";
$P2G_LANGUAGE['ERR_INVALID_DATAGRID_STRUCTURE'] = "La estructura de la rejilla <I>%s</I> no es v�lida. Usted deber�a declarar un <I>DATASOURCE</I> y un <I>FIELDSET</I> que contengan al menos un campo!";
$P2G_LANGUAGE['ERR_MISSING_RANGEFIELD_CHILDREN'] = "La especificaci�n del RANGEFIELD <I>%s</I> es err�nea. El nodo deber�a tener dos hijos EDITFIELD!";
$P2G_LANGUAGE['ERR_EDITSEARCH_INVALID_DATAFILTER'] = "La especificaci�n del DATAFILTER <I>%s</I> no es v�lida.";
$P2G_LANGUAGE['ERR_EDITSEARCH_INVALID_DATAFILTER_MASK'] = "La m�scara asignada al DATAFILTER <I>%s</I> no es v�lida.";
$P2G_LANGUAGE['ERR_EDITSEARCH_DUPLICATED_DATAFILTER'] = "Ya existe otro DATAFILTER con el ID <I>%s</I>.";
$P2G_LANGUAGE['ERR_MISSING_EDITSEARCH_CHILDREN'] = "La especificaci�n del EDITSEARCHFIELD <I>%s</I> est� incompleta. Usted deber�a proveer al menos un DATAFILTER, un DATASOURCE y un LOOKUPFIELD!";
$P2G_LANGUAGE['ERR_CSV_DB_JSRS'] = "No fue posible completar la operaci�n!";
$P2G_LANGUAGE['ERR_SEARCHFORM_EMPTY'] = "La b�squeda no puede ser realizada debido a que todos los campos de b�squeda est�n vac�os.";
$P2G_LANGUAGE['ERR_SEARCHFORM_INVALID'] = "La b�squeda no puede ser realizada debido a que los valores de filtrado est�n vac�os o tienen menos de %d caracteres de longitud."; 
$P2G_LANGUAGE['ERR_REPORT_MAX_GROUP_COLS'] = "La cantidad de columnas de agrupaci�n deber�a ser menor que el n�mero de columnas de la consulta!";
$P2G_LANGUAGE['ERR_REPORT_UNKNOWN_GROUP_COL'] = "La columna de agrupaci�n <I>%s</I> no est� definida en el conjunto de resultados de la consulta!";
$P2G_LANGUAGE['ERR_REPORT_MISSING_BLOCK_VARIABLE'] = "No se declar� en la plantilla la variable <I>%s</I> del bloque <I>%s</I>!";
$P2G_LANGUAGE['ERR_REPORT_INVALID_COLSIZES'] = "El valor <I>%s</I> no es una definici�n v�lida para los tama�os de coluna!";
$P2G_LANGUAGE['ERR_REPORT_COL_SIZES_SUM'] = "La suma de los tama�os de columna suministrados a la funci�n setColumnSizes deber�a ser igual a 100!";
$P2G_LANGUAGE['ERR_REPORT_COL_COUNT_MISMATCH'] = "La cantidad de tama�os de columna suministrados (%s) no es igual al n�mero de columnas del reporte (%s, agrupaci�n %s )";
$P2G_LANGUAGE['ERR_REPORT_MISSING_SEARCH_VARIABLE'] = "No fue posible construir el formulario de b�squeda ya que no se encontr� la variable <I>%s</I>.<BR>Para proceder declare {%s} en la plantilla de reporte.";
$P2G_LANGUAGE['ERR_REPORT_SEARCH_PARS_MALFORMED'] = "Usted deber�a suministrar la Etiqueta, el Nombre de Campo y la M�scara en la construcci�n de los par�metros de b�squeda!";
$P2G_LANGUAGE['ERR_REPORT_SEARCH_INVALID_MASK'] = "La m�scara <I>%s</I> del par�metro de b�squeda <I>#%d</I> no es v�lida!";
$P2G_LANGUAGE['ERR_REPORT_MIN_ALT_STYLE'] = "La funci�n setAlternateStyle deber�a recibir al menos dos par�metros (nombres CSS)!";
$P2G_LANGUAGE['ERR_REPORT_HIDDEN_GROUP'] = "No se puede incluir una columna oculta (%s) en los encabezados del grupo!";
$P2G_LANGUAGE['ERR_REPORT_GROUP_HIDDEN'] = "No se puede definir como oculta una columna que se muestra en los encabezados de grupo (%s)!";
$P2G_LANGUAGE['ERR_REPORT_MAX_HIDDEN_COLS'] = "La cantidad de columnas ocultas debe ser menor que la cantidad de columnas de la consulta!";
$P2G_LANGUAGE['ERR_REPORT_UNKNOWN_HIDDEN_COL'] = "La columna <I>%s</I>, definida como oculta, no existe en el conjunto de resultados de la consulta!";
$P2G_LANGUAGE['ERR_DATASET_INVALID_TYPE'] = "El tipo <I>%s</I> no es un tipo DataSet v�lido!";
$P2G_LANGUAGE['ERR_MENU_WRONG_ROOT_SQL'] = "La consulta almacenada en el par�metro \$rootSql del m�todo Menu::loadFromDatabase() retorn� un conjunto vac�o de datos o no tiene el m�nimo n�mero de campos requerido (Indice y Etiqueta)!";
$P2G_LANGUAGE['ERR_MENU_WRONG_CHILDREN_STATEMENT'] = "La consulta pasada en el par�metro \$childSql del m�todo Menu::loadFromDatabase() no es v�lida.<BR>Suministre una sentencia que contenga '~var~' (Columna Relaci�n) mas los dos campos requeridos (Indice y Etiqueta)";
$P2G_LANGUAGE['ERR_MENU_NOT_FOUND'] = "No se cre� el men�. Construya los elementos de men� utilizando Menu::loadFromDatabase() o Menu::loadFromXmlFile().";
$P2G_LANGUAGE['ERR_MENU_EMPTY_XML_ROOT'] = "El nodo ra�z del archivo XML no tiene nodos hijos";
$P2G_LANGUAGE['ERR_MENU_INVALID_IMAGE'] = "La imagen <I>%s</I> no es v�lida. Los posibles valores son: <I>%s</I>.";
$P2G_LANGUAGE['ERR_FTP_MISSING_HOST'] = "No fue posible abrir la conexi�n. Usted primero deber�a configurar la conexi�n usando los m�todos FtpClient::setServer() y FtpClient::setUserInfo()";
$P2G_LANGUAGE['ERR_FTP_MISSING_USER_OR_PASS'] = "No fue posible autenticarse. No se suministr� uno de los valores de usuario (nombre de usuario o contrase�a)";
$P2G_LANGUAGE['ERR_SPRSH_OUT_OF_BOUNDS'] = "Se excedieron los l�mites de la hoja de c�lculo: [%d;%d]";
$P2G_LANGUAGE['ERR_SPRSH_MAX_NOTE_CHARS'] = "Se excedi� el tama�o m�ximo para una nota de celda<BR>Valor: %s";
$P2G_LANGUAGE['ERR_SPRSH_MAX_FONTS'] = "Se excedi� el l�mite de m�ximo 4 fuentes por cada hoja de c�lculo!";
$P2G_LANGUAGE['ERR_UPLOAD_NOT_FOUND'] = "No fue posible completar la subida (upload). El archivo no se transfiri� correctamente.";
$P2G_LANGUAGE['ERR_UPLOAD_TOO_BIG'] = "No fue posible completar la subida (upload). El tama�o del archivo es mayor que el m�ximo permitido.";
$P2G_LANGUAGE['ERR_UPLOAD_INVALID_NAME'] = "No fue posible completar la subida (upload). El nombre de archivo %s no es v�lido.";
$P2G_LANGUAGE['ERR_UPLOAD_INVALID_TYPE'] = "No fue posible completar la subida (upload). La extensi�n %s no es permitida.";
$P2G_LANGUAGE['ERR_UPLOAD_FILE_EXISTS'] = "No fue posible completar la subida (upload). El archivo <I>%s</I> ya existe y no se sobreescribi�.";
$P2G_LANGUAGE['ERR_UPLOAD_CANT_MOVE'] = "No fue posible mover el archivo temporal a subir (upload) a la carpeta de destino.";
$P2G_LANGUAGE['ERR_CANT_OPEN_SOCKET'] = "No fue posible abrir el socket usando el puerto %s con el servidor %s!<BR>Error %d - %s";
$P2G_LANGUAGE['ERR_SOCKET_NOT_CONNECTED'] = "No est� activa la conexi�n con el socket!";
$P2G_LANGUAGE['ERR_SOCKET_EOF'] = "Se alcanz� el fin del archivo, no se pudo completar la operaci�n!";
$P2G_LANGUAGE['ERR_SOCKET_TIMEOUT'] = "Se super� el tiempo m�ximo de ejecuci�n del socket. No se puede completar la operaci�n!";
$P2G_LANGUAGE['ERR_MAIL_MESSAGE_NOT_BUILT'] = "El mensaje no se construy� correctamente y no pudo ser enviado!<BR>Ejecute el m�todo build().";
$P2G_LANGUAGE['ERR_MAIL_EMPTY_RCPT'] = "No hay receptores para el mensaje!";
$P2G_LANGUAGE['ERR_SIGNED_MESSAGE_SIGN'] = "No fue posible firmar el contenido del mensaje.<BR>%s";
$P2G_LANGUAGE['ERR_SMTP_CONNECTION'] = "Error al conectarse al servidor SMTP %s!<BR>Errno: [%d]<BR>Errmsg: %s";
$P2G_LANGUAGE['ERR_SMTP_COMMAND'] = "Error al ejecutar el comando %s en el servidor SMTP!<BR>Errno: [%d]<BR>Errmsg: %s";
$P2G_LANGUAGE['ERR_SMTP_AUTHENTICATE'] = "No fue posible autenticar con el servidor SMTP!";
$P2G_LANGUAGE['ERR_CRYPT_MISSING_KEY'] = "Para encriptar o desencriptar datos, usted deber�a suministrar una llave de encripci�n a trav�s del m�todo Crypt::setKey()!";
$P2G_LANGUAGE['ERR_CRYPT_OPEN_MODULE'] = "No fue posible inicializar el cifrado <I>%s</I> utilizando el modo %s!";
$P2G_LANGUAGE['ERR_OPENSSL_PUBKEY_ENCRYPT'] = "No fue posible extraer una llave p�blica para encriptar los datos suministrados. Provea al m�todo OpenSSLCrypt::setCertificatePath() el archivo que contiene un certificado v�lido!";
$P2G_LANGUAGE['ERR_OPENSSL_PUBKEY_VERIFY'] = "No fue posible extraer una llave p�blica para verificar la firma incluida en estos datos. Provea al m�todo OpenSSLCrypt::setCertificatePath() el archivo que contiene un certificado v�lido.";
$P2G_LANGUAGE['ERR_OPENSSL_PRIVKEY_DECRYPT'] = "No fue posible extraer una llave privada para desencriptar los datos suministrados. Provea el archivo que contiene la llave privada y la frase clave, si la llave requiere de una.";
$P2G_LANGUAGE['ERR_OPENSSL_PRIVKEY_SIGN'] = "No fue posible extraer una llave privada para generar la firma digital. Provea el archivo que contiene la llave privada y la frase clave, si la llave creada requiere de una.";
$P2G_LANGUAGE['ERR_OPENSSL_CERT_PATH'] = "La ruta del certificado no es v�lida!";
$P2G_LANGUAGE['ERR_OPENSSL_READ_CERT'] = "No fue posible leer el contenido del certificado!%s";
$P2G_LANGUAGE['ERR_POP3_CONNECTION'] = "Error al conectarse al servidor POP3 %s!<BR>Errno: [%d]<BR>- Errmsg: %s";
$P2G_LANGUAGE['ERR_POP3_COMMAND'] = "Error al ejecutar el comando %s en el servidor POP!<BR>Errmsg: %s";
$P2G_LANGUAGE['ERR_POP3_AUTHENTICATE'] = "No fue posible autenticar con el servidor POP3!";
$P2G_LANGUAGE['ERR_POP3_APOP'] = "No fue posible ejecutar el comando APOP : no hay un mensaje de autenticaci�n almacenado!";
$P2G_LANGUAGE['ERR_HTTP_CONNECT'] = "No fue posible establecer una conexi�n HTTP con el servidor <I>%s</I>!";
$P2G_LANGUAGE['ERR_HTTP_MISSING_HOST'] = "No se estableci� el servidor HTTP. Ejecute el m�todo HttpClient::setHost().";
$P2G_LANGUAGE['ERR_CANT_INSTANTIATE_VALIDATOR'] = "No fue posible instanciar la clase de validaci�n <B>%s</B>!";
$P2G_LANGUAGE['ERR_INVALID_VALIDATOR'] = "La clase de validaci�n <I>%s</I> no es v�lida! No se puede encontrar el m�todo %s::execute().";
$P2G_LANGUAGE['ERR_ASSERTION_MESSAGE'] = "La expresi�n evaluada no alcanz� los resultados esperados.";
$P2G_LANGUAGE['ERR_INVALID_CALLBACK'] = "La funci�n o m�todo <I>%s</I> no es una retrollamada (callback) v�lida!";
$P2G_LANGUAGE['ERR_INVALID_PROPERTIES_FILE'] = "The file <I>%s</I> doesn't contain valid configuration data!";
$P2G_LANGUAGE['ERR_INVALID_COLOR_SPEC'] = "<I>%s</I> no es una especificaci�n de color v�lida.";
$P2G_LANGUAGE['ERR_ALLOCATE_COLOR'] = "No fue posible asignar un nuevo color a esta imagen!";
$P2G_LANGUAGE['ERR_JSRS_MISSING_HANDLER'] = "El nombre o alias del manipulador (handler) JSRS es err�neo o falta!";
$P2G_LANGUAGE['ERR_JSRS_INVALID_HANDLER'] = "El manipulador (handler) JSRS %s no es v�lido!";
$P2G_LANGUAGE['DEFAULT_BTN_VALUE'] = "Enviar";
$P2G_LANGUAGE['CALENDAR_LINK_TITLE'] = "Abrir el calendario";
$P2G_LANGUAGE['CALCULATOR_LINK_TITLE'] = "Abrir la calculadora";
$P2G_LANGUAGE['CALCULATOR_WINDOW_TITLE'] = "Calculadora";
$P2G_LANGUAGE['CALCULATOR_RESULT_CAPTION'] = "Resultado";
$P2G_LANGUAGE['DAYS_OF_WEEK'] = array("Domingo", "Lunes", "Martes", "Mi�rcoles", "Jueves", "Viernes", "S�bado");
$P2G_LANGUAGE['DAYS_OF_WEEK_ABBR'] = array("Dom", "Lun", "Mar", "Mi�", "Jue", "Vie", "Sab");
$P2G_LANGUAGE['MONTHS_OF_YEAR'] = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
$P2G_LANGUAGE['MONTHS_OF_YEAR_ABBR'] = array("Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic");
$P2G_LANGUAGE['COMPRESS_USE_MSG'] = "<!-- Utiliza Compresi�n %s -->";
$P2G_LANGUAGE['COMPRESS_DEBUG_MSG_ORIG'] = "Tama�o Original: %d";
$P2G_LANGUAGE['COMPRESS_DEBUG_MSG_COMP'] = "Tama�o Comprimido: %d";
$P2G_LANGUAGE['FORM_MASKS_DATA_LABEL'] = array(
	'CPFCNPJ' => 'CPF/CNPJ',
	'CURRENCY' => 'Moneda',
	'DATE' => 'Fecha',
	'TIME' => 'Hora',
	'EMAIL' => 'Correo-e',
	'FLOAT' => 'N�mero Flotante',
	'INTEGER' => 'N�mero Entero',
	'IP' => 'Direcci�n IP',
	'LOGIN' => 'Nombre de Usuario (Login)',
	'URL' => 'Direcci�n URL/Internet',
	'ZIP' => 'C�digo Postal'
);
$P2G_LANGUAGE['FORM_EDITOR_VARS'] = array(
	'paragraph' => 'P�rrafo',
	'pnorm' => 'Normal',
	'ph1' => 'Encabezado 1 &lt;H1&gt;',
	'ph2' => 'Encabezado 2 &lt;H2&gt;',
	'ph3' => 'Encabezado 3 &lt;H3&gt;',
	'ph4' => 'Encabezado 4 &lt;H4&gt;',
	'ph5' => 'Encabezado 5 &lt;H5&gt;',
	'ph6' => 'Encabezado 6 &lt;H6&gt;',
	'paddr' => 'Direcci�n &lt;ADDR&gt;',
	'ppre' => 'Formateado &lt;PRE&gt;',
	'prem' => 'Eliminar Formato',
	'bold' => 'Formato Negrita',
	'italic' => 'Formato Cursiva',
	'under' => 'Formato Subrayado',
	'cut' => 'Cortar',
	'copy' => 'Copiar',
	'paste' => 'Pegar',
	'fcolor' => 'Cambiar el color de Frente',
	'bcolor' => 'Cambiar el color de Fondo',
	'emoticon' => 'Insertar un Smiley',
	'font' => 'Fuente',
	'fontsize' => 'Tama�o',
	'left' => 'Alineaci�n izquierda',
	'center' => 'Centrar el texto',
	'right' => 'Alineaci�n derecha',
	'olist' => 'Insertar/Eliminar Lista Numerada',
	'ulist' => 'Insertar/Eliminar Lista Simple',
	'indent+' => 'Aumentar la Identaci�n',
	'indent-' => 'Reducir la Identaci�n',
	'link' => 'Insertar un V�nculo',
	'image' => 'Insertar una Imagen',
	'editmode' => 'Editar en Modo Avanzado',
	'choosesmil' => 'Seleccionar un Smiley'
);
$P2G_LANGUAGE['LOOKUP_CHOICE_FILTER_TIP'] = "--- Filtrar la lista ---";
$P2G_LANGUAGE['LOOKUP_SELECTION_DEFAULT_SELFIRST'] = "--- Insertar Valores ---";
$P2G_LANGUAGE['LOOKUP_SELECTION_BUTTON_TITLES'] = array(
	'add' => 'Agregar los Elementos seleccionados',
	'addall' => 'Agregar todos los Elementos',
	'rem' => 'Eliminar los Elementos seleccionados',
	'remall' => 'Eliminar todos los Elementos'
);
$P2G_LANGUAGE['EDIT_SELECTION_BUTTON_TITLES'] = array(
	'add' => 'Agregar',
	'rem' => 'Eliminar los Elementos seleccionados',
	'remall' => 'Eliminar todos los Elementos'
);
$P2G_LANGUAGE['MEMO_COUNT_LABEL'] = "Caracteres";
$P2G_LANGUAGE['SEL_AVAILABLE_VALUES_LABEL'] = "Disponibles:";
$P2G_LANGUAGE['SEL_INSERTED_VALUES_LABEL'] = "Insertados:";
$P2G_LANGUAGE['FORM_DATA_BIND_TOOLBAR_VALUES'] = array(
	'navFirstTip' => 'Ir al primer registro',
	'navPrevTip' => 'Ir al registro anterior',
	'navNextTip' => 'Ir al siguiente registro',
	'navLastTip' => 'Ir al �ltimo registro',
	'actNew' => 'Nuevo',
	'actNewTip' => 'Crear un nuevo registro',
	'actEdit' => 'Editar',
	'actEditTip' => 'Editar el registro actual',
	'actDel' => 'Borrar',
	'actDelTip' => 'Borrar el registro actual',
	'actSave' => 'Guardar',
	'actSaveTip' => 'Guardar el registro actual',
	'actCancel' => 'Cancelar',
	'actCancelTip' => 'Cancelar la acci�n actual',
	'sortAsc' => 'Orden Ascendente',
	'sortDesc' => 'Orden Descendente',
	'sortChoose' => 'Seleccione un campo por el cual ordenar',
	'sortTit' => 'Orden',
	'sortFirst' => 'Campos de Ordenamiento',
	'gotoTip' => 'Indique el n�mero de registro y presione Ir',
	'goto' => 'Ir',
	'gotoBtnTip' => 'Presione para ir al registro seleccionado',
	'gotoTit' => 'Ir a',
	'filter' => 'Filtrar',
	'filterTip' => 'Seleccione el campo al cual se le aplicar� el filtro',
	'filterVTip' => 'Seleccione el valor del filtro',
	'filterBtnTip' => 'Presione aqu� para filtrar',
	'filterTit' => 'Filtrar',
	'filterFirst' => 'Campos de Filtro'
);
$P2G_LANGUAGE['REPORT_EMPTY_VALUES'] = array(
	'emptyMsg' => 'La consulta o los filtros de b�squeda utilizados retornaron un juego de registros vac�o',
	'backLink' => 'Volver al Reporte'
);
$P2G_LANGUAGE['REPORT_ORDER_TIP'] = "Ordenar por %s";
$P2G_LANGUAGE['REPORT_FUNCTION_MESSAGES'] = array(
	'firstTit' => 'Primera',
	'firstTip' => 'Ir a la primera p&aacute;gina',
	'prevTit' => 'Anterior',
	'prevTip' => 'Ir a la p&aacute;gina anterior en el reporte',
	'prevScrTit' => '%d Atr&aacute;s',
	'prevScrTip' => '%d P&aacute;ginas atr&aacute;s en el reporte',
	'pageTip' => 'Ir a la p&aacute;gina #%d de %d',
	'nextTit' => 'Siguiente',
	'nextTip' => 'Ir a la p&aacute;gina siguiente en el reporte',
	'nextScrTit' => '%d Adelante',
	'nextScrTip' => '%d P&aacute;ginas adelante en el reporte',
	'lastTit' => '&Uacute;ltima',
	'lastTip' => 'Ir a la &uacute;ltima p&aacute;gina',
	'rowCount' => '%d registro(s) encontrado(s)',
	'rowsPerPage' => '%d resultado(s) por p&aacute;gina',
	'thisPage' => 'P&aacute;gina %d de %d',
	'rowInterval' => 'Mostrando los resultados %d-%d de %d ',
	'goTo' => 'Ir a la p&aacute;gina'
);
$P2G_LANGUAGE['REPORT_SEARCH_VALUES'] = array(
	'searchTit' => 'Buscar',
	'fieldFirst' => 'Seleccione un Campo',
	'btnAdd' => 'Agregar',
	'radioAny' => 'Cualquier Filtro',
	'radioAll' => 'Todos los Filtros',
	'btnClear' => 'Limpiar',
	'btnView' => 'Ver',
	'btnSend' => 'Buscar'
);
$P2G_LANGUAGE['REPORT_SEARCH_INI_OP'] = array(
	'=' => 'Igual a',
	'!=' => 'Diferente de',
	'LIKE' => 'Conteniendo',
	'NOT LIKE' => 'que no contenga',
	'LIKEI' => 'Iniciando con',
	'LIKEF' => 'Terminando en'
);
return $P2G_LANGUAGE;
?>