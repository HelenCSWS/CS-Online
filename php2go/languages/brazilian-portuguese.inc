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
// $Header: /www/cvsroot/php2go/languages/brazilian-portuguese.inc,v 1.44 2005/08/30 14:36:10 mpont Exp $
// $Revision: 1.44 $
// $Date: 2005/08/30 14:36:10 $

$P2G_LANGUAGE['ERR_OLD_PHP_VERSION'] = "As configura��es atuais do servidor n�o permitem a execu��o do PHP2Go.<BR>A vers�o do PHP instalada em seu servidor � a <I>%s</I>. O requerimento m�nimo do PHP2Go � a vers�o <I>4.1.0</I>.<BR>Instale uma vers�o igual ou superior para utiliz�-lo normalmente.";
$P2G_LANGUAGE['ERR_UNSUPPORTED_EXTENSION'] = "A extens�o <I>%s</I> n�o � suportada pela instala��o atual do PHP.";
$P2G_LANGUAGE['ERR_OBJ_MESSAGE'] = "Erro na Classe %s:<BR>%s";
$P2G_LANGUAGE['ERR_SCRIPT_MESSAGE'] = "Erro no script:<BR>%s";
$P2G_LANGUAGE['ERR_DATABASE'] = "Erro no Banco de Dados";
$P2G_LANGUAGE['ERR_MISSING_DATABASE_PARAMETERS'] = "N�o foi poss�vel carregar os par�metros de conex�o com o banco de dados! [ID: %s]";
$P2G_LANGUAGE['ERR_INVALID_DATABASE_PARAMETERS'] = "Os par�metros de conex�o ao banco de dados s�o inv�lidos! Os par�metros USER, BASE e TYPE s�o obrigat�rios. [ID: %s]";
$P2G_LANGUAGE['ERR_DATABASE_CONNECTION_FAILED'] = "N�o foi poss�vel conectar ao banco de dados com as configura��es de acesso atuais. Elas est�o corretas?";
$P2G_LANGUAGE['ERR_TYPES'] = array(
	1 => "Erro Fatal",
	2 => "Alerta",
	4 => "Erro de Sintaxe",
	8 => "Aviso",
	16 => "Erro Fatal do N�cleo",
	32 => "Alerta do N�cleo",
	64 => "Erro Fatal do Compilador",
	128 => "Alerta do Compilador",
	256 => "Erro de Usu�rio",
	512 => "Alerta de Usu�rio",
	1024 => "Aviso de Usu�rio"
);
$P2G_LANGUAGE['ERR_ABSOLUTE_URI_NOT_FOUND'] = "A vari�vel 'ABSOLUTE_URI' n�o foi informada na tabela geral de configura��es!";
$P2G_LANGUAGE['ERR_CONFIG_ENTRY_NOT_FOUND'] = "A vari�vel <I>%s</I> n�o foi informada na tabela geral de configura��es!";
$P2G_LANGUAGE['ERR_INVALID_LANGDOMAIN_FILE'] = "O arquivo de mensagens para o dom�nio %s, na linguagem %s, � inv�lido ou inexistente.";
$P2G_LANGUAGE['ERR_CANT_FIND_CFG_VAL'] = "N�o foi poss�vel encontrar o valor <I>%s</I> no tabela geral de configura��es. Ela foi corretamente configurada na instala��o?";
$P2G_LANGUAGE['ERR_CANT_LOAD_MODULE'] = "N�o foi poss�vel carregar o m�dulo <I>%s</I>";
$P2G_LANGUAGE['ERR_CANT_LOAD_DIR'] = "N�o foi poss�vel carregar m�dulos a partir de um diret�rio v�lido em <I>%s</I>";
$P2G_LANGUAGE['ERR_CANT_LOAD_DIR_MODULE'] = "N�o foi poss�vel carregar o arquivo <I>%s</I> em <I>%s</I>";
$P2G_LANGUAGE['ERR_CANT_CONVERT_DATE'] = "N�o foi poss�vel converter a data para o formato %s.";
$P2G_LANGUAGE['ERR_CANT_RUN_ON_WINDOWS'] = "A classe %s n�o pode ser utilizada em ambiente Windows!";
$P2G_LANGUAGE['ERR_UNDEFINED_FUNCTION'] = "A fun��o %s n�o est� definida!";
$P2G_LANGUAGE['ERR_UNDEFINED_METHOD'] = "O m�todo %s n�o est� definido na classe %s!";
$P2G_LANGUAGE['ERR_ABSTRACT_CLASS'] = "A classe <I>%s</I> � abstrata e n�o pode ser instanciada diretamente!";
$P2G_LANGUAGE['ERR_DATE_FORMAT'] = "A data %s n�o est� no formato %s exigido.";
$P2G_LANGUAGE['ERR_INVALID_RESOURCE'] = "A vari�vel %s fornecida � fun��o %s n�o � um tipo 'resource' v�lido.";
$P2G_LANGUAGE['ERR_INVALID_AUTHENTICATOR'] = "O autenticador %s n�o � uma subclasse de php2go.net.Auth!";
$P2G_LANGUAGE['ERR_INVALID_AUTHENTICATOR_PATH'] = "N�o foi poss�vel importar e/ou instanciar o autenticador %s!";
$P2G_LANGUAGE['ERR_INVALID_USERCONTAINER'] = "O container de usu�rio %s n�o � uma subclasse de php2go.auth.User!";
$P2G_LANGUAGE['ERR_INVALID_USERCONTAINER_PATH'] = "N�o foi poss�vel importar e/ou instanciar o container de usu�rio %s!";
$P2G_LANGUAGE['ERR_XML_PARSE'] = "Ocorreu um erro na interpreta��o do arquivo XML:<BR>Erro: %s<BR>Linha: %s, Coluna: %s";
$P2G_LANGUAGE['ERR_STATEMENT_EVAL'] = "Ocorreu um erro de sintaxe na interpreta��o do c�digo PHP <I>%s</I>";
$P2G_LANGUAGE['ERR_CANT_FIND_FILE'] = "N�o foi poss�vel encontrar o arquivo ou diret�rio %s. Ele existe?";
$P2G_LANGUAGE['ERR_CANT_OPEN_FILE'] = "N�o foi poss�vel abrir o arquivo %s com o modo %s. Verifique as permiss�es do arquivo e o modo utilizado.";
$P2G_LANGUAGE['ERR_CANT_CREATE_FILE'] = "N�o foi poss�vel criar o arquivo ou diret�rio %s. Verifique as permiss�es do diret�rio.";
$P2G_LANGUAGE['ERR_CANT_READ_FILE'] = "N�o foi poss�vel abrir o arquivo ou diret�rio %s para leitura. Ele existe? O caminho est� correto?";
$P2G_LANGUAGE['ERR_CANT_WRITE_FILE'] = "N�o foi poss�vel abrir o arquivo ou diret�rio %s para escrita. Voc� tem permiss�es para sobrescreve-lo?";
$P2G_LANGUAGE['ERR_CANT_DELETE_FILE'] = "N�o foi poss�vel excluir o arquivo %s.<BR>Voc� possui permiss�es de escrita neste diret�rio? Os arquivos possuem permiss�o para serem deletados?";
$P2G_LANGUAGE['ERR_CANT_INCLUDE_FILE'] = "N�o foi poss�vel abrir o arquivo %s para inclus�o. Ele existe? O caminho est� correto?";
$P2G_LANGUAGE['ERR_CANT_CHANGE_MODE'] = "N�o foi poss�vel aplicar o modo %s ao arquivo ou diret�rio %s.";
$P2G_LANGUAGE['ERR_CANT_READ_DIR'] = "N�o foi poss�vel encontrar o diret�rio %s. Ele existe?";
$P2G_LANGUAGE['ERR_CANT_OPEN_DIR'] = "N�o foi poss�vel abrir o diret�rio %s para leitura. Verifique suas permiss�es.";
$P2G_LANGUAGE['ERR_CANT_FIND_SERIALIZATION_FILE'] = "N�o foi poss�vel abrir o arquivo do objeto serializado para leitura. O caminho est� certo? O arquivo foi criado?";
$P2G_LANGUAGE['ERR_EMPTY_INSERT_QUERY'] = "A consulta de inser��o na tabela '%s' retornou vazia para o conjunto de valores fornecidos.";
$P2G_LANGUAGE['ERR_EMPTY_UPDATE_QUERY'] = "A consulta de altera��o da tabela '%s' para a sele��o %s retornou vazia para o conjunto de valores fornecidos.";
$P2G_LANGUAGE['ERR_EMPTY_TOGLOBALS_QUERY'] = "A consulta de publica��o de vari�veis globais %s n�o retornou resultados.";
$P2G_LANGUAGE['ERR_TOGLOBALS_WRONG_USAGE'] = "A consulta fornecida � fun��o Db::toGlobals() deve iniciar por 'SELECT' (DQL).";
$P2G_LANGUAGE['ERR_EMPTY_TEMPLATE_FILE'] = "N�o foi poss�vel inicializar o Template com um nome de arquivo vazio!";
$P2G_LANGUAGE['ERR_EMPTY_TEMPLATE_CONTENT'] = "O template n�o foi preparado, pois o seu conte�do � vazio. Verifique o conte�do fornecido ao construtor como par�metro.";
$P2G_LANGUAGE['ERR_CANT_FIND_VARIABLE'] = "N�o foi poss�vel encontrar a vari�vel %s no arquivo template <I>%s</I>. <BR>Para obter o funcionamento completo desta ferramenta, insira {%s} no c�digo do template.";
$P2G_LANGUAGE['ERR_CANT_FIND_BLOCK'] = "N�o foi poss�vel encontrar o bloco <I>%s</I> para cria��o. Ele foi declarado no template?";
$P2G_LANGUAGE['ERR_CANT_FIND_BLOCK_REUSE'] = "N�o foi poss�vel encontrar o bloco %s para reus�-lo como %s.";
$P2G_LANGUAGE['ERR_CANT_PUT_ON_PREPARED_TEMPLATE'] = "O elemento %s j� foi configurado com o m�todo DocumentElement::parse(). N�o � poss�vel adicionar elementos com a fun��o put().";
$P2G_LANGUAGE['ERR_TEMPLATE_NOT_PREPARED'] = "O template %s n�o foi preparado. Execute a fun��o %s::parse(); antes de realizar opera��es sobre o mesmo.";
$P2G_LANGUAGE['ERR_TEMPLATE_ALREADY_PREPARED'] = "Este template j� foi preparado anteriormente. Execute o m�todo parse() somente uma vez.";
$P2G_LANGUAGE['ERR_UNBALANCED_BLOCKDEF'] = "A defini��o do bloco <I>%s</I> est� desbalanceada.";
$P2G_LANGUAGE['ERR_INVALID_MODIFIER'] = "O modificador <I>%s</I> � inv�lido ou inexistente.";
$P2G_LANGUAGE['ERR_EMPTY_DOC_LAYOUT'] = "N�o foi poss�vel encontrar nenhum elemento no layout de documento fornecido.";
$P2G_LANGUAGE['ERR_CANT_REPLICATE_ROOT_BLOCK'] = "O bloco base de um template n�o pode ser replicado.";
$P2G_LANGUAGE['ERR_MUST_BE_POSITIVE'] = "O valor de %s deve ser maior do que zero na fun��o %s.";
$P2G_LANGUAGE['ERR_MUST_BE_AN_ARRAY'] = "O valor de %s deve ser um array na fun��o %s";
$P2G_LANGUAGE['ERR_VALUE_OUT_OF_BOUNDS'] = "O valor de '%s' est� fora dos limites permitidos: %s e %s";
$P2G_LANGUAGE['ERR_URL_MALFORMED'] = "O valor de <I>%s</I> n�o � um valor v�lido para URL!";
$P2G_LANGUAGE['ERR_HOST_INVALID'] = "<I>%s</I> n�o � um valor v�lido para host de conex�o!";
$P2G_LANGUAGE['ERR_DNS_NOT_FOUND'] = "N�o foi poss�vel encontrar um DNS v�lido para o dom�nio %s.";
$P2G_LANGUAGE['ERR_SESSION_PROPERTY_NOT_FOUND'] = "A propriedade %s da vari�vel de sess�o %s n�o foi encontrada. Execute a fun��o createProperty()";
$P2G_LANGUAGE['ERR_SESSION_TIMECOUNTER_NOT_FOUND'] = "O cron�metro %s da vari�vel de sess�o %s n�o foi encontrado. Execute a fun��o createTimeCounter()";
$P2G_LANGUAGE['ERR_INVALID_OBJECT'] = "O objeto fornecido como par�metro n�o � uma inst�ncia v�lida da classe <I>%s</I>!";
$P2G_LANGUAGE['ERR_CALCULATOR_MISSING_PARAMETERS'] = "Par�metros insuficientes para constru��o da calculadora!";
$P2G_LANGUAGE['ERR_COLORSEL_MISSING_PARAMETERS'] = "Par�metros insuficientes para constru��o da sele��o de cor!";
$P2G_LANGUAGE['ERR_INVALID_FORM_METHOD'] = "O m�todo %s � inv�lido para a submiss�o do formul�rio '%s'";
$P2G_LANGUAGE['ERR_INVALID_VALIDATE_FUNC'] = "<I>%s</I> n�o � um nome v�lido para fun��o de valida��o!";
$P2G_LANGUAGE['ERR_FORM_DUPLICATED_FIELD'] = "O campo <I>%s</I> j� foi definido para o formul�rio <I>%s</I>!";
$P2G_LANGUAGE['ERR_FORM_DUPLICATED_BUTTON'] = "O bot�o <I>%s</I> j� foi definido para o formul�rio <I>%s</I>!";
$P2G_LANGUAGE['ERR_FORM_DUPLICATED_SECTION'] = "O ID de se��o <I>%s</I> j� foi definido para o formul�rio <I>%s</I>!";
$P2G_LANGUAGE['ERR_FORM_SECTION_EVALFUNC'] = "N�o foi poss�vel definir a visibilidade da se��o <I>%s</I>, pois a fun��o <I>%s</I> n�o foi definida!";
$P2G_LANGUAGE['ERR_FORM_SECTION_TPLBLOCK'] = "A se��o <I>%s</I> � condicional. Deve ser declarado um bloco de nome <I>%s</I> no template contendo os campos da se��o!";
$P2G_LANGUAGE['ERR_FORM_FIELD_TITLE'] = "Os seguintes erros foram encontrados neste formul�rio:";
$P2G_LANGUAGE['ERR_FORM_FIELD_INVALID_INPUT'] = "O campo %s possui um valor de %s inv�lido.";
$P2G_LANGUAGE['ERR_FORM_FIELD_INVALID_FLOAT'] = "A precis�o num�rica do campo %s � inv�lida.\\nOs tamanhos m�ximos s�o %d para a parte inteira e %d para a parte decimal.";
$P2G_LANGUAGE['ERR_FORM_FIELD_ALPHANUM'] = "O campo %s deve conter apenas caracteres alfanum�ricos.";
$P2G_LANGUAGE['ERR_FORM_FIELD_CHOICE'] = "O campo %s deve conter um dos seguintes valores: <I>%s</I>";
$P2G_LANGUAGE['ERR_FORM_FIELD_CREDITCARD'] = "O campo %s cont�m um n�mero inv�lido de cart�o de cr�dito %s.";
$P2G_LANGUAGE['ERR_FORM_FIELD_MIN_LENGTH'] = "O campo %s deve possuir ao menos %s caracteres.";
$P2G_LANGUAGE['ERR_FORM_FIELD_MAX_LENGTH'] = "O campo %s deve possuir no m�ximo %s caracteres.";
$P2G_LANGUAGE['ERR_FORM_FIELD_REQUIRED'] = "O campo %s � obrigat�rio.";
$P2G_LANGUAGE['ERR_FORM_FIELD_REGEX'] = "O campo %s possui um valor incorreto!";
$P2G_LANGUAGE['ERR_FORM_FIELD_EQ'] = "O campo %s deve ser igual ao campo %s!"; 
$P2G_LANGUAGE['ERR_FORM_FIELD_NEQ'] = "O campo %s deve ser diferente do campo %s!"; 
$P2G_LANGUAGE['ERR_FORM_FIELD_GT'] = "O campo %s deve ser maior do que o campo %s!";
$P2G_LANGUAGE['ERR_FORM_FIELD_LT'] = "O campo %s deve ser menor do que o campo %s!";
$P2G_LANGUAGE['ERR_FORM_FIELD_GOET'] = "O campo %s deve ser maior ou igual ao campo %s!";
$P2G_LANGUAGE['ERR_FORM_FIELD_LOET'] = "O campo %s deve ser menor ou igual ao campo %s!";
$P2G_LANGUAGE['ERR_FORM_FIELD_VALUE_EQ'] = "O campo %s deve ter um valor igual a %s!";
$P2G_LANGUAGE['ERR_FORM_FIELD_VALUE_NEQ'] = "O campo %s deve ter um valor diferente de %s!";
$P2G_LANGUAGE['ERR_FORM_FIELD_VALUE_GT'] = "O campo %s deve ter um valor maior do que %s!";
$P2G_LANGUAGE['ERR_FORM_FIELD_VALUE_LT'] = "O campo %s deve ter um valor menor do que %s!";
$P2G_LANGUAGE['ERR_FORM_FIELD_VALUE_GOET'] = "O campo %s deve ter um valor maior ou igual a %s!";
$P2G_LANGUAGE['ERR_FORM_FIELD_VALUE_LOET'] = "O campo %s deve ter um valor menor ou igual a %s!";
$P2G_LANGUAGE['ERR_FORM_INVALID_MASK'] = "A m�scara <I>%s</I>, no campo <I>%s</I>, n�o � uma m�scara v�lida!";
$P2G_LANGUAGE['ERR_FORM_INVALID_FIELDTYPE'] = "O tipo de campo <I>%s</I> � inv�lido!";
$P2G_LANGUAGE['ERR_FORM_CAPTCHA'] = "O c�digo informado no campo %s n�o corresponde ao conte�do da imagem!";
$P2G_LANGUAGE['ERR_DATAGRID_INVALID_FIELDTYPE'] = "O tipo de campo <I>%s</I> n�o pode ser utilizado dentro de um <I>grid</I>!";
$P2G_LANGUAGE['ERR_DATAGRID_INVALID_CELLSIZES'] = "O valor do atributo <I>CELLSIZES</I> no campo <I>%s</I> � inv�lido. � necess�rio fornecer N+1 tamanhos (N=tamanho do FIELDSET) e a soma dos tamanhos deve ser 100!";
$P2G_LANGUAGE['ERR_DATAGRID_INVALID_FIELDCOUNT'] = "A consulta definida no <I>DATASOURCE</I> do campo <I>%s</I> deve retornar N+2 colunas (N=tamanho do FIELDSET)";
$P2G_LANGUAGE['ERR_FORM_WRONG_LISTENER'] = "Os atributos do tratador de eventos <I>%s</I> s�o inv�lidos ou incompletos!";
$P2G_LANGUAGE['ERR_FORM_WRONG_RULE'] = "Os atributos da regra <I>%s</I> s�o inv�lidos ou incompletos!";
$P2G_LANGUAGE['ERR_INVALID_DATASOURCE_SYNTAX'] = "A especifica��o da tag DATASOURCE do campo <I>%s</I> � inv�lida.<BR>Os nodos filhos KEYFIELD e LOOKUPTABLE s�o obrigat�rios!";
$P2G_LANGUAGE['ERR_MISSING_GROUPFIELD_CHILDREN'] = "A especifica��o do campo <I>%s</I> est� incompleta. Deve ser definido ao menos um filho do tipo &lt;OPTION&gt;!";
$P2G_LANGUAGE['ERR_MISSING_OPTION_VALUE'] = "O atributo VALUE da op��o �ndice <I>%d</I> do campo <I>%s</I> n�o foi especificado!";
$P2G_LANGUAGE['ERR_MISSING_DBRADIOFIELD_DATASOURCE'] = "N�o foi especificada uma tag DATASOURCE para o campo DBRADIOFIELD <I>%s</I>!";
$P2G_LANGUAGE['ERR_EMPTY_DBRADIOFIELD_RESULTS'] = "A consulta especificada no DATASOURCE do campo DBRADIOFIELD <I>%s</I> n�o retornou resultados!";
$P2G_LANGUAGE['ERR_MISSING_COMBOOPTION_VALUE'] = "O atributo VALUE da op��o �ndice %d do campo COMBOFIELD <I>%s</I> n�o foi especificado!";
$P2G_LANGUAGE['ERR_MISSING_EDITSELECTION_CHILDREN'] = "A especifica��o do campo EDITSELECTIONFIELD <I>%s</I> est� incompleta. Os nodos filhos EDITFIELD e LOOKUPFIELD s�o obrigat�rios!";
$P2G_LANGUAGE['ERR_MISSING_LOOKUPSELECTION_CHILDREN'] = "A especifica��o do campo LOOKUPSELECTIONFIELD <I>%s</I> est� errada. � obrigat�rio fornecer dois nodos filhos do tipo LOOKUPFIELD!";
$P2G_LANGUAGE['ERR_MISSING_SOURCELOOKUP_DATASOURCE'] = "N�o foi especificada uma tag DATASOURCE para o primeiro filho LOOKUPFIELD do campo LOOKUPSELECTIONFIELD <I>%s</I>!";
$P2G_LANGUAGE['ERR_MISSING_QUERY_ELEMENTS'] = "Os itens \$fields e \$tables s�o obrigat�rios na montagem de uma consulta SQL!";
$P2G_LANGUAGE['ERR_INVALID_DATAGRID_STRUCTURE'] = "A estrutura do grid <I>%s</I> � inv�lida. Deve ser declarado um <I>DATASOURCE</I> e um conjunto de campos (<I>FIELDSET</I>) com pelo menos um elemento!";
$P2G_LANGUAGE['ERR_MISSING_RANGEFIELD_CHILDREN'] = "A especifica��o do campo RANGEFIELD <I>%s</I> est� errada. � obrigat�rio fornecer dois nodos filhos do tipo EDITFIELD!";
$P2G_LANGUAGE['ERR_EDITSEARCH_INVALID_DATAFILTER'] = "A especifica��o do DATAFILTER <I>%s</I> � inv�lida.";
$P2G_LANGUAGE['ERR_EDITSEARCH_INVALID_DATAFILTER_MASK'] = "A m�scara informada para o DATAFILTER <I>%s</I> � inv�lida.";
$P2G_LANGUAGE['ERR_EDITSEARCH_DUPLICATED_DATAFILTER'] = "J� existe um DATAFILTER com o ID <I>%s</I> informado.";
$P2G_LANGUAGE['ERR_MISSING_EDITSEARCH_CHILDREN'] = "A especifica��o do campo EDITSEARCHFIELD <I>%s</I> est� incompleta. � obrigat�rio fornecer ao menos um DATAFILTER, um DATASOURCE e um LOOKUPFIELD!";
$P2G_LANGUAGE['ERR_CSV_DB_JSRS'] = "N�o foi poss�vel realizar a opera��o!";
$P2G_LANGUAGE['ERR_SEARCHFORM_EMPTY'] = "A pesquisa n�o p�de ser executada porque todos os campos de pesquisa est�o vazios.";
$P2G_LANGUAGE['ERR_SEARCHFORM_INVALID'] = "A pesquisa n�o p�de ser executada porque os filtros s�o vazios ou possuem menos do que %s caracteres.";
$P2G_LANGUAGE['ERR_REPORT_MAX_GROUP_COLS'] = "O n�mero de colunas de agrupamento deve ser menor do que o n�mero de colunas da consulta!";
$P2G_LANGUAGE['ERR_REPORT_UNKNOWN_GROUP_COL'] = "A coluna de agrupamento <I>%s</I> n�o existe no resultado da consulta!";
$P2G_LANGUAGE['ERR_REPORT_MISSING_BLOCK_VARIABLE'] = "A vari�vel <I>%s</I> do bloco <I>%s</I> n�o foi declarada no template!";
$P2G_LANGUAGE['ERR_REPORT_INVALID_COLSIZES'] = "O valor <I>%s</I> n�o � uma defini��o v�lida para os tamanhos das colunas!";
$P2G_LANGUAGE['ERR_REPORT_COL_SIZES_SUM'] = "A soma dos tamanhos das colunas fornecido � fun��o setColumnSizes deve ser igual a 100!";
$P2G_LANGUAGE['ERR_REPORT_COL_COUNT_MISMATCH'] = "O n�mero de tamanhos de colunas fornecido (%s) � diferente do n�mero de colunas do relat�rio (%s, %s agrupadas)";
$P2G_LANGUAGE['ERR_REPORT_MISSING_SEARCH_VARIABLE'] = "N�o foi poss�vel gerar o formul�rio de busca, pois a vari�vel <I>%s</I> n�o foi encontrada.<BR>Declare {%s} no template do relat�rio para prosseguir.";
$P2G_LANGUAGE['ERR_REPORT_SEARCH_PARS_MALFORMED'] = "Voc� deve informar R�tulo, Nome do Campo e M�scara obrigatoriamente na montagem dos par�metros de busca!";
$P2G_LANGUAGE['ERR_REPORT_SEARCH_INVALID_MASK'] = "A m�scara do <I>%s</I> do par�metro <I>%d</I> de busca � inv�lida!";
$P2G_LANGUAGE['ERR_REPORT_MIN_ALT_STYLE'] = "A fun��o setAlternateStyle deve ser chamada com pelo menos dois par�metros (nomes de estilos CSS)!";
$P2G_LANGUAGE['ERR_REPORT_HIDDEN_GROUP'] = "Uma coluna escondida (%s) n�o pode ser inclu�da nos cabe�alhos de agrupamento!";
$P2G_LANGUAGE['ERR_REPORT_GROUP_HIDDEN'] = "Uma coluna de cabe�alho de grupo (%s) n�o pode ser definida como escondida!";
$P2G_LANGUAGE['ERR_REPORT_MAX_HIDDEN_COLS'] = "O n�mero de colunas escondidas deve ser menor do que o total de colunas da consulta!";
$P2G_LANGUAGE['ERR_REPORT_UNKNOWN_HIDDEN_COL'] = "A coluna <I>%s</I> marcada como escondida n�o existe no resultado da consulta!";
$P2G_LANGUAGE['ERR_DATASET_INVALID_TYPE'] = "O tipo <I>%s</I> n�o � um tipo de DataSet v�lido!";
$P2G_LANGUAGE['ERR_MENU_WRONG_ROOT_SQL'] = "A consulta fornecida no par�metro \$rootSql do m�todo Menu::loadFromDatabase() n�o retornou resultados ou n�o possui o m�nimo de 2 colunas (�ndice e R�tulo)!";
$P2G_LANGUAGE['ERR_MENU_WRONG_CHILDREN_STATEMENT'] = "A consulta fornecida no par�metro \$childSql do m�todo Menu::loadFromDatabase() � inv�lida.<BR>Forne�a um statement com a vari�vel de liga��o '~vari�vel~' declarada e as 2 colunas obrigat�rias (�ndice e R�tulo)";
$P2G_LANGUAGE['ERR_MENU_NOT_FOUND'] = "O menu n�o foi criado. Construa os itens do menu utilizando Menu::loadFromDatabase() ou Menu::loadFromXmlFile().";
$P2G_LANGUAGE['ERR_MENU_EMPTY_XML_ROOT'] = "O nodo raiz do arquivo XML n�o possui nodos filhos!";
$P2G_LANGUAGE['ERR_MENU_INVALID_IMAGE'] = "A imagem <I>%s</I> � inv�lida. Os valores v�lidos s�o: <I>%s</I>.";
$P2G_LANGUAGE['ERR_FTP_MISSING_HOST'] = "N�o foi poss�vel realizar a conex�o. � preciso configurar a conex�o com os m�todos FtpClient::setServer() e FtpClient::setUserInfo()";
$P2G_LANGUAGE['ERR_FTP_MISSING_USER_OR_PASS'] = "N�o foi poss�vel realizar a autentica��o. Um dos dados do Usu�rio (nome ou senha) n�o foi informado";
$P2G_LANGUAGE['ERR_SPRSH_OUT_OF_BOUNDS'] = "Limites da planilha excedidos: [%d;%d]";
$P2G_LANGUAGE['ERR_SPRSH_MAX_NOTE_CHARS'] = "O tamanho m�ximo para uma nota de c�lula foi excedido<BR>Valor: %s";
$P2G_LANGUAGE['ERR_SPRSH_MAX_FONTS'] = "O n�mero m�ximo de 4 fontes para uma planilha foi excedido!";
$P2G_LANGUAGE['ERR_UPLOAD_NOT_FOUND'] = "N�o foi poss�vel realizar o upload. O arquivo n�o foi transferido corretamente.";
$P2G_LANGUAGE['ERR_UPLOAD_TOO_BIG'] = "N�o foi poss�vel realizar o upload. O arquivo transferido � maior do que o tamanho m�ximo permitido.";
$P2G_LANGUAGE['ERR_UPLOAD_INVALID_NAME'] = "N�o foi poss�vel realizar o upload. O nome de arquivo %s � inv�lido.";
$P2G_LANGUAGE['ERR_UPLOAD_INVALID_TYPE'] = "N�o foi poss�vel realizar o upload. A extens�o %s n�o � permitida.";
$P2G_LANGUAGE['ERR_UPLOAD_FILE_EXISTS'] = "N�o foi poss�vel realizar o upload. O arquivo <I>%s</I> j� existe e n�o foi sobrescrito.";
$P2G_LANGUAGE['ERR_UPLOAD_CANT_MOVE'] = "N�o foi poss�vel mover o arquivo tempor�rio de upload para a pasta destino.";
$P2G_LANGUAGE['ERR_CANT_OPEN_SOCKET'] = "N�o foi poss�vel abrir o socket na porta %s com o host %s!<BR>Erro %d - %s";
$P2G_LANGUAGE['ERR_SOCKET_NOT_CONNECTED'] = "A conex�o com o socket n�o est� ativa!";
$P2G_LANGUAGE['ERR_SOCKET_EOF'] = "Fim de arquivo atingido, n�o foi poss�vel completar a opera��o!";
$P2G_LANGUAGE['ERR_SOCKET_TIMEOUT'] = "O tempo de espera do socket foi excedido. A opera��o n�o pode ser conclu�da!";
$P2G_LANGUAGE['ERR_MAIL_MESSAGE_NOT_BUILT'] = "A mensagem n�o foi corretamente constru�da e n�o pode ser enviada!<BR>Execute o m�todo build().";
$P2G_LANGUAGE['ERR_MAIL_EMPTY_RCPT'] = "N�o foi fornecido nenhum destinat�rio � mensagem!";
$P2G_LANGUAGE['ERR_SIGNED_MESSAGE_SIGN'] = "N�o foi poss�vel assinar o conte�do da mensagem.<BR>%s";
$P2G_LANGUAGE['ERR_SMTP_CONNECTION'] = "Erro ao conectar ao servidor SMTP %s!<BR>Errno: [%d]<BR>Errmsg: %s";
$P2G_LANGUAGE['ERR_SMTP_COMMAND'] = "Erro ao executar o comando %s no servidor SMTP!<BR>Errno: [%d]<BR>Errmsg: %s";
$P2G_LANGUAGE['ERR_SMTP_AUTHENTICATE'] = "N�o foi poss�vel realizar a autentica��o com o servidor SMTP!";
$P2G_LANGUAGE['ERR_CRYPT_MISSING_KEY'] = "Para encriptar ou desencriptar dados, voc� deve fornecer uma chave de criptografia atrav�s do m�todo Crypt::setKey()!";
$P2G_LANGUAGE['ERR_CRYPT_OPEN_MODULE'] = "N�o foi poss�vel inicializar o cifrador <I>%s</I> utilizando o modo %s!";
$P2G_LANGUAGE['ERR_OPENSSL_PUBKEY_ENCRYPT'] = "N�o foi poss�vel extrair uma chave p�blica para encriptar os dados fornecidos. Forne�a um arquivo contendo um certificado correto ao m�todo OpenSSLCrypt::setCertificatePath()!";
$P2G_LANGUAGE['ERR_OPENSSL_PUBKEY_VERIFY'] = "N�o foi poss�vel extrair uma chave p�blica para verificar a assinatura dos dados. Forne�a um arquivo contendo um certificado correto ao m�todo OpenSSLCrypt::setCertificatePath()!";
$P2G_LANGUAGE['ERR_OPENSSL_PRIVKEY_DECRYPT'] = "N�o foi poss�vel extrair uma chave privada para decriptar os dados. Forne�a o nome do arquivo que cont�m a chave privada e a frase de passagem, se a chave criada exigir uma.";
$P2G_LANGUAGE['ERR_OPENSSL_PRIVKEY_SIGN'] = "N�o foi poss�vel extrair uma chave privada para gerar a assinatura digital. Forne�a o nome do arquivo que cont�m a chave privada e a frase de passagem, se a chave criada exigir uma.";
$P2G_LANGUAGE['ERR_OPENSSL_CERT_PATH'] = "O caminho fornecido para o certificado � inv�lido!";
$P2G_LANGUAGE['ERR_OPENSSL_READ_CERT'] = "N�o foi poss�vel ler o conte�do do certificado fornecido!%s";
$P2G_LANGUAGE['ERR_POP3_CONNECTION'] = "Erro ao conectar ao servidor POP3 %s!<BR>Errno: [%d]<BR>- Errmsg: %s";
$P2G_LANGUAGE['ERR_POP3_COMMAND'] = "Erro ao executar o comando %s no servidor POP!<BR>Errmsg: %s";
$P2G_LANGUAGE['ERR_POP3_AUTHENTICATE'] = "N�o foi poss�vel realizar a autentica��o com o servidor POP3!";
$P2G_LANGUAGE['ERR_POP3_APOP'] = "N�o foi poss�vel executar o comando APOP : n�o existe um banner de autentica��o armazenado!";
$P2G_LANGUAGE['ERR_HTTP_CONNECT'] = "N�o foi poss�vel estabelecer a conex�o HTTP com o host <I>%s</I>!";
$P2G_LANGUAGE['ERR_HTTP_MISSING_HOST'] = "O host da conex�o HTTP n�o foi setado. Execute o m�todo HttpClient::setHost().";
$P2G_LANGUAGE['ERR_CANT_INSTANTIATE_VALIDATOR'] = "N�o foi poss�vel instanciar a classe de valida��o <B>%s</B>!";
$P2G_LANGUAGE['ERR_INVALID_VALIDATOR'] = "A classe de valida��o <I>%s</I> � inv�lida! O m�todo %s::execute() n�o foi encontrado.";
$P2G_LANGUAGE['ERR_ASSERTION_MESSAGE'] = "A express�o avaliada n�o alcan�ou o resultado desejado.";
$P2G_LANGUAGE['ERR_INVALID_CALLBACK'] = "A fun��o ou m�todo <I>%s</I> n�o � um callback v�lido!";
$P2G_LANGUAGE['ERR_INVALID_PROPERTIES_FILE'] = "O arquivo <I>%s</I> n�o cont�m dados v�lidos de configura��o!";
$P2G_LANGUAGE['ERR_INVALID_COLOR_SPEC'] = "<I>%s</I> n�o � uma especifica��o v�lida de cor.";
$P2G_LANGUAGE['ERR_ALLOCATE_COLOR'] = "N�o foi poss�vel alocar uma nova cor para esta imagem!";
$P2G_LANGUAGE['ERR_JSRS_MISSING_HANDLER'] = "Nome ou alias do tratador de evento JSRS n�o fornecido!";
$P2G_LANGUAGE['ERR_JSRS_INVALID_HANDLER'] = "O tratador de evento JSRS %s � inv�lido!";
$P2G_LANGUAGE['DEFAULT_BTN_VALUE'] = "Submeter";
$P2G_LANGUAGE['CALENDAR_LINK_TITLE'] = "Ver Calend�rio";
$P2G_LANGUAGE['CALCULATOR_LINK_TITLE'] = "Abrir a Calculadora";
$P2G_LANGUAGE['CALCULATOR_WINDOW_TITLE'] = "Calculadora";
$P2G_LANGUAGE['CALCULATOR_RESULT_CAPTION'] = "Resultado";
$P2G_LANGUAGE['DAYS_OF_WEEK'] = array("Domingo","Segunda-Feira","Ter�a-Feira","Quarta-Feira","Quinta-Feira","Sexta-Feira","S�bado");
$P2G_LANGUAGE['DAYS_OF_WEEK_ABBR'] = array("Dom", "Seg", "Ter", "Qua", "Qui", "Sex", "S�b");
$P2G_LANGUAGE['MONTHS_OF_YEAR'] = array("Janeiro","Fevereiro","Mar�o","Abril","Maio","Junho","Julho","Agosto","Setembro","Outubro","Novembro","Dezembro");
$P2G_LANGUAGE['MONTHS_OF_YEAR_ABBR'] = array("Jan", "Fev", "Mar", "Abr", "Mai", "Jun", "Jul", "Ago", "Set", "Out", "Nov", "Dez");
$P2G_LANGUAGE['COMPRESS_USE_MSG'] = "<!-- Utiliza Compress�o %s -->";
$P2G_LANGUAGE['COMPRESS_DEBUG_MSG_ORIG'] = "Tamanho Original: %d";
$P2G_LANGUAGE['COMPRESS_DEBUG_MSG_COMP'] = "Tamanho Compactado: %d";
$P2G_LANGUAGE['FORM_MASKS_DATA_LABEL'] = array(
	'CPFCNPJ' =>'CPF/CNPJ',
	'CURRENCY' => 'Moeda',
	'DATE' => 'Data',
	'TIME' => 'Hora',
	'EMAIL' => 'E-Mail',
	'FLOAT' => 'N�mero Decimal',
	'INTEGER' => 'N�mero Inteiro',
	'IP' => 'Endere�o IP',
	'LOGIN' => 'Login/Nome de Usu�rio',
	'URL' => 'URL/Endere�o Internet',
	'ZIP' => 'CEP'
);
$P2G_LANGUAGE['FORM_EDITOR_VARS'] = array(
	'paragraph' => 'Par�grafo',
	'pnorm' => 'Normal',
	'ph1' => 'Cabe�alho 1 &lt;H1&gt;',
	'ph2' => 'Cabe�alho 2 &lt;H2&gt;',
	'ph3' => 'Cabe�alho 3 &lt;H3&gt;',
	'ph4' => 'Cabe�alho 4 &lt;H4&gt;',
	'ph5' => 'Cabe�alho 5 &lt;H5&gt;',
	'ph6' => 'Cabe�alho 6 &lt;H6&gt;',
	'paddr' => 'Endere�o &lt;ADDR&gt;',
	'ppre' => 'Formatado &lt;PRE&gt;',
	'prem' => 'Remover Formato',
	'bold' => 'Formatar Negrito',
	'italic' => 'Formatar It�lico',
	'under' => 'Formatar Sublinhado',
	'cut' => 'Cortar',
	'copy' => 'Copiar',
	'paste' => 'Colar',
	'fcolor' => 'Alterar a cor da letra',
	'bcolor' => 'Alterar a cor do fundo',
	'emoticon' => 'Inserir uma Caricatura',
	'font' => 'Fonte',
	'fontsize' => 'Tam',
	'left' => 'Alinhar o texto � Esquerda',
	'center' => 'Centralizar o texto',
	'right' => 'Alinhar o texto � Direita',
	'olist' => 'Inserir/Remover Lista Numerada',
	'ulist' => 'Inserir/Remover Lista n�o Numerada',
	'indent+' => 'Aumentar a Indenta��o',
	'indent-' => 'Diminuir a Indenta��o',
	'link' => 'Inserir um Link',
	'image' => 'Inserir uma Imagem',
	'editmode' => 'Editar em Modo Avan�ado',
	'choosesmil' => 'Escolha uma Caricatura'
);
$P2G_LANGUAGE['LOOKUP_CHOICE_FILTER_TIP'] = "--- Filtre a Lista ---";
$P2G_LANGUAGE['LOOKUP_SELECTION_DEFAULT_SELFIRST'] = "--- Insira Valores ---";
$P2G_LANGUAGE['LOOKUP_SELECTION_BUTTON_TITLES'] = array(
	'add' => 'Adicionar os Itens Selecionados',
	'addall' => 'Adicionar todos os Itens',
	'rem' => 'Remover os Itens Selecionados',
	'remall' => 'Remover todos os Itens'
);
$P2G_LANGUAGE['EDIT_SELECTION_BUTTON_TITLES'] = array(
	'add' => 'Adicionar',
	'rem' => 'Remover os Itens Selecionados',
	'remall' => 'Remover Todos os Itens'
);
$P2G_LANGUAGE['MEMO_COUNT_LABEL'] = "Caracteres";
$P2G_LANGUAGE['SEL_AVAILABLE_VALUES_LABEL'] = "Dispon�veis:";
$P2G_LANGUAGE['SEL_INSERTED_VALUES_LABEL'] = "Inseridos:";
$P2G_LANGUAGE['FORM_DATA_BIND_TOOLBAR_VALUES'] = array(
	'navFirstTip' => 'Ir para o primeiro registro',
	'navPrevTip' => 'Ir para o registro anterior',
	'navNextTip' => 'Ir para o pr�ximo registro',
	'navLastTip' => 'Ir para o �ltimo registro',
	'actNew' => 'Novo',
	'actNewTip' => 'Criar um novo registro',
	'actEdit' => 'Editar',
	'actEditTip' => 'Editar o registro atual',
	'actDel' => 'Excluir',
	'actDelTip' => 'Excluir o registro atual',
	'actSave' => 'Salvar',
	'actSaveTip' => 'Salvar o registro atual',
	'actCancel' => 'Cancelar',
	'actCancelTip' => 'Cancelar a a��o atual',
	'sortAsc' => 'Ordena��o Ascendente',
	'sortDesc' => 'Ordena��o Descendente',
	'sortChoose' => 'Escolha um valor para ordenar',
	'sortTit' => 'Ordenar',
	'sortFirst' => 'Campos de Ordena��o',
	'gotoTip' => 'Digite o registro para o qual deseja ir',
	'goto' => 'Ir',
	'gotoBtnTip' => 'Clique para ir para o registro escolhido',
	'gotoTit' => 'Ir Para',
	'filter' => 'Filtrar',
	'filterTip' => 'Escolha o campo pelo qual deseja filtrar',
	'filterVTip' => 'Escolha o valor a ser utilizado no filtro',
	'filterBtnTip' => 'Clique aqui para filtrar',
	'filterTit' => 'Filtrar',
	'filterFirst' => 'Campos de Filtragem'
);
$P2G_LANGUAGE['REPORT_EMPTY_VALUES'] = array(
	'emptyMsg' => 'A consulta ou os filtros de pesquisa utilizados n�o retornaram resultados',
	'backLink' => 'Voltar ao Relat�rio'
);
$P2G_LANGUAGE['REPORT_ORDER_TIP'] = "Ordernar por %s";
$P2G_LANGUAGE['REPORT_FUNCTION_MESSAGES'] = array(
	'firstTit' => 'Primeira',
	'firstTip' => 'Ir para a primeira p�gina do relat�rio',
	'prevTit' => 'Anterior',
	'prevTip' => 'Ir para a p�gina anterior',
	'prevScrTit' => 'Voltar %d',
	'prevScrTip' => 'Voltar %d P�ginas no relat�rio',
	'pageTip' => 'Ir para a p�gina %d de %d',
	'nextTit' => 'Pr�xima',
	'nextTip' => 'Ir para a pr�xima p�gina',
	'nextScrTit' => 'Avan�ar %d',
	'nextScrTip' => 'Avan�ar %d p�ginas no relat�rio',
	'lastTit' => '�ltima',
	'lastTip' => 'Ir para a �ltima p�gina do relat�rio',
	'rowCount' => '%d registro(s) encontrado(s)',
	'rowsPerPage' => '%d resultado(s) por p�gina',
	'thisPage' => 'P�gina %d de %d',
	'rowInterval' => 'Exibindo %d-%d de %d resultados',
	'goTo' => 'Ir para a p�gina'
);
$P2G_LANGUAGE['REPORT_SEARCH_VALUES'] = array(
	'searchTit' => 'Buscar',
	'fieldFirst' => 'Escolha um Campo',
	'btnAdd' => 'Adicionar',
	'radioAny' => 'Qualquer um dos filtros',
	'radioAll' => 'Todos os Filtros',
	'btnClear' => 'Limpar',
	'btnView' => 'Visualizar',
	'btnSend' => 'Buscar'
);
$P2G_LANGUAGE['REPORT_SEARCH_INI_OP'] = array(
	'=' => 'Igual a',
	'!=' => 'Diferente de',
	'LIKE' => 'Contendo',
	'NOT LIKE' => 'n�o Contendo',
	'LIKEI' => 'Iniciando com',
	'LIKEF' => 'Terminando com'
);
return $P2G_LANGUAGE;											  
?>