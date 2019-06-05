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
// $Header: /www/cvsroot/php2go/core/form/FormDataBind.class.php,v 1.27 2005/08/30 18:50:13 mpont Exp $
// $Date: 2005/08/30 18:50:13 $

// ------------------------------------------------
import('php2go.data.DataSet');
import('php2go.db.QueryBuilder');
import('php2go.form.Form');
import('php2go.template.Template');
// ------------------------------------------------

//!-----------------------------------------------------------------
// @class		FormDataBind
// @desc		Esta classe, que possui funcionamento restrito no ambiente 
//				Microsoft/Internet Explorer, gera um formulário que utiliza 
//				Data Binding. 
//				O formulário gerado está associado a uma fonte de dados do tipo 
//				TDC (Tabular Data Control), que significa um arquivo texto (ou CSV) 
//				que contém os dados provenientes do banco de dados.
// @package		php2go.form
// @extends		Form
// @uses		DataSet
// @uses		QueryBuilder
// @uses		Template
// @author		Marcos Pont
// @version		$Revision: 1.27 $
// @note		O funcionamento, além de restrito ao Internet Explorer
// 				>= 5.0, é considerado como um módulo experimental do PHP2Go
// @note		Exemplo de uso:<BR>
//				<PRE>
//
//				$form = new FormDataBind('file.xml', 'file.tpl', 'formName', $doc, 'table', 'primary_key');
//				$form->setFormMethod('POST');
//				$form->setInputStyle('input_style');
//				$form->setButtonStyle('button_style');
//				$form->setFilterSortOptions('columnA#Column A|columnB#Column B');
//				$form->queryFields = 'columnA, columnB';
//				$form->queryTables = 'table';
//				$form->queryClause = 'columnA > 10';
//				$form->queryOrder = 'columnA DESC';
//				$content = $form->getContent();
//
//				</PRE>
//!-----------------------------------------------------------------
class FormDataBind extends Form
{
    var $templateFile;		  		// @var templateFile string 	Nome do arquivo template do formulário
    var $queryFields;				// @var queryFields string		Campos da consulta para geração da navegação
    var $queryTables;				// @var queryTables string		Tabelas da consulta para geração da navegação
    var $queryClause;				// @var queryClause string		Cláusula WHERE de condição
    var $queryOrder;				// @var queryOrder string		Coluna ou colunas de ordenação da consulta
    var $queryLimit;				// @var queryLimit int			Limite ou número de registros desejados na consulta
    var $tableName;					// @var tableName string 		Nome da tabela que está sendo manipulada
    var $primaryKey;				// @var primaryKey string 		Chave primária da tabela que está sendo manipulada
    var $csvFile;					// @var csvFile string 			Nome do arquivo CSV para armazenamento dos dados
    var $csvDbName;					// @var csvDbName string 		Nome do objeto de data bind utilizado
    var $csvDbToolbar = array();	// @var csvDbToolbar array 		"array()" Vetor contendo botões de navegação, ação e elementos de ordenação e filtragem
    var $dataBindCode;		  		// @var dataBindCode string 	Código Javascript da navegação e manipulação dos registros
    var $extraFunctions;			// @var extraFunctions array 	Vetor de funções a serem executadas nos botões de ação/navegação
    var $forcePost = FALSE;			// @var forcePost bool 			"FALSE" Salvar/Excluir com submissão do formulário e não com JSRS
    var $parsFilterSort;			// @var parsFilterSort string	Valores de campo/valor para filtragem/ordenação, no modelo campo1#valor1|campo2#valor2|...|campon#valorn    
    var $formCode;			  		// @var formCode string 		Código HTML gerado para o formulário
	var $Template;					// @var Template Template object	Objeto Template para manipulação do arquivo indicado em $templateFile

	//!-----------------------------------------------------------------
	// @function	FormDataBind::FormDataBind
	// @desc		Constrói a instância do objeto FormDataBind, inicializando
	// 				a conexão ao banco, o template do conteúdo do formulário
	// 				e as definições para criação da estrutura de Data Binding
	// @access		public
	// @param		xmlFile string				Arquivo XML da especificação do formulário
	// @param 		templateFile string			Arquivo template para geração da interface do formulário
	// @param 		formName string				Nome do formulário
	// @param 		&Document Document object	Objeto Document onde o formulário será inserido
	// @param		tplIncludes array			"array()" Vetor de valores para blocos de inclusão no template
	// @param 		tableName string			Nome da tabela envolvida nos dados que serão manipulados
	// @param 		primaryKey string			Nome da coluna que representa a chave primária da tabela indicada em $tableName
	//!-----------------------------------------------------------------
	function FormDataBind($xmlFile, $templateFile, $formName, &$Document, $tplIncludes=array(), $tableName, $primaryKey) {
		parent::Form($xmlFile, $formName, $Document);
		// inicializa e parseia o template principal
		$this->templateFile = $templateFile;
		$this->Template = new Template($templateFile);
		if (TypeUtils::isHashArray($tplIncludes) && !empty($tplIncludes)) {			
			foreach ($tplIncludes as $blockName => $blockValue)
				$this->Template->includeAssign($blockName, $blockValue, T_BYFILE);
		}		
		$this->Template->parse();
		// configurações principais da geração dos dados
		$this->csvDbName = "db_" . strtolower($tableName);
		$this->tableName = $tableName;
		$this->primaryKey = $primaryKey;
		// inicializa as imagens de ordem asc/desc com os valores pré-definidos
		$this->icons['sortasc'] = PHP2GO_ICON_PATH . "fdb_order_asc.gif";
		$this->icons['sortdesc'] = PHP2GO_ICON_PATH . "fdb_order_desc.gif";
	}

	//!-----------------------------------------------------------------
	// @function	FormDataBind::setImageSortAsc
	// @desc		Configura o ícone de ordenação ascendente
	// @access		public
	// @param		igmAsc string		Caminho da nova imagem
	// @return		void
	// @see			FormDataBind::setImageSortDesc
	//!-----------------------------------------------------------------
	function setImageSortAsc($imgAsc) {
		$this->icons['sortasc'] = $imgAsc;
	}

	//!-----------------------------------------------------------------
	// @function	FormDataBind::setImageSortDesc
	// @desc		Configura o ícone de ordenação descendente
	// @access		public
	// @param		igmDesc string	Caminho da nova imagem
	// @return		void	
	// @see			FormDataBind::setImageSortAsc
	//!-----------------------------------------------------------------
	function setImageSortDesc($imgDesc) {
		$this->icons['sortdesc'] = $imgDesc;
	}

	//!-----------------------------------------------------------------
	// @function	FormDataBind::setFilterParameters
	// @desc		Configura a lista de opções para filtragem e ordenação
	// 				dos dados. A lista deve respeitar o formato
	// 				campo1#rótulo1|campo2#rótulo2|...|campoN#rótuloN, onde
	// campoN		referencia nomes de campos no formulário
	// @access		public
	// @param		options string	Lista de opções de filtragem/ordenação
	// @return		void	
	//!-----------------------------------------------------------------
	function setFilterSortOptions($options) {
		$this->parsFilterSort = $options;
	}

	//!-----------------------------------------------------------------
	// @function	FormDataBind::setExtraButtonFunction
	// @desc		Permite associar a um dos botões de ação/navegação uma
	// 				função extra no evento 'onClick'
	// @access		public
	// @param		button string		Nome do botão: FIRST, PREVIOUS,
	// 									NEXT, LAST, NEW, EDIT, SAVE, DELETE ou CANCEL
	// @param		function string	Nome da função de script a ser executada
	// @return 		bool Retorna FALSE se a função não foi corretamente aplicada
	//!-----------------------------------------------------------------
	function setExtraButtonFunction($button, $function) {
		$button = strtoupper($button);
		if (in_array($button, array('FIRST', 'PREVIOUS', 'NEXT', 'LAST', 'NEW', 'EDIT', 'SAVE', 'DELETE', 'CANCEL'))) {
			$this->extraFunctions[$button] = "onClick=setTimeout(\"" . str_replace("\"", "'", $function) . "\", 100);";
			return true;
		} else {
			return false;
		}
	}

	//!-----------------------------------------------------------------
	// @function	FormDataBind::disableJsrs
	// @desc		Desabilita as operações de inserção/alteração/exclusão
	// 				utilizando JSRS. Neste caso, estas operações deverão
	// 				ser processadas fora da classe, através do tratamento
	// 				da submissão do formulário
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function disableJsrs() {
		$this->forcePost = TRUE;
	}
	
	//!-----------------------------------------------------------------
	// @function	FormDataBind::display
	// @desc		Constrói e imprime o código HTML do formulário
	// @access		public
	// @return		void	
	// @see			FormDataBind::getContent
	//!-----------------------------------------------------------------
	function display() {
		print $this->getContent();
	}

	//!-----------------------------------------------------------------
	// @function	FormDataBind::display
	// @desc	  	Constrói e retorna o código HTML do formulário
	// @access		public
	// @return		string Código HTML do Formulário
	// @see			FormDataBind::getContent
	//!-----------------------------------------------------------------
	function getContent() {
		if (!$this->formConstruct) 
			parent::processXml();
		$this->_createDbCsvFile();
		$this->_generateDbCsvScript();
		foreach($this->sections as $section)
			$this->_buildSection($section);
		$this->_buildCsvDbToolbar();
		parent::buildScriptCode();
		return $this->_buildFormCode();
	}
	
	//!-----------------------------------------------------------------
	// @function	FormDataBind::_buildFormCode
	// @desc		Gera a camada externa de código do formulário e insere
	// 				o nela o conteúdo extraído do template. Armazena o
	// 				código HTML gerado na propriedade $formCode da classe
	// @access 		private
	// @return		void	
	//!-----------------------------------------------------------------
	function _buildFormCode() {
		$TARGET = (isset($this->actionTarget)) ? " TARGET=\"" . $this->actionTarget . "\"" : '';
		$ENCTYPE = ($this->hasUpload) ? " ENCTYPE=\"multipart/form-data\"" : '';
		$RESET = (!empty($this->resetCode) ? " onReset=\"return " . $this->formName . "_reset()\"" : "");
		$SIGNATURE = sprintf("<INPUT TYPE=\"hidden\" ID=\"%s\" NAME=\"__form_signature\" VALUE=\"%s\"/>", FORM_SIGNATURE, parent::getSignature());
		$LASTPOSITION = HttpRequest::getVar('lastposition');
		$REMOVEID = HttpRequest::getVar('removeid');		
		$this->formCode .= $this->dataBindCode;
		$this->formCode .= sprintf("<FORM ID=\"%s\" NAME=\"%s\" ACTION=\"%s\" METHOD=\"%s\" STYLE=\"display:inline\"%s%s onSubmit=\"return %s_submit();\"%s>%s\n", 
			$this->formName, $this->formName, $this->formAction, $this->formMethod, 
			$TARGET, $ENCTYPE, $this->formName, $RESET, $SIGNATURE);
		if ($this->forcePost) {
			$this->formCode .= "<INPUT TYPE=\"hidden\" NAME=\"lastposition\" VALUE=\"$LASTPOSITION\">\n";
			$this->formCode .= "<INPUT TYPE=\"hidden\" NAME=\"removeid\" VALUE=\"$REMOVEID\">\n";
		}
		$this->formCode .= $this->Template->getContent();
		$this->formCode .= "</FORM>\n";
		return $this->formCode;
	}	

	//!-----------------------------------------------------------------
	// @function	FormDataBind::_buildOptionsList
	// @desc		Constrói a lista de opções para as caixas de seleção
	// 				dos campos de filtragem e ordenação
	// @access		private
	// @param		type string	Tipo: sort ou filter
	// @return		string Código das opções do campo 'SELECT'
	//!-----------------------------------------------------------------
	function _buildOptionsList($type, $toolbarValues) {
		switch ($type) {
			case 'sort' : 
				$text = $toolbarValues['sortFirst'];
				break;
			case 'filter' : 
				$text = $toolbarValues['filterFirst'];
				break;
			default: 
				$text = "";
				break;
		}
		$this->options = "";
		$this->options .= "<OPTION VALUE=\"\">" . $text . "</OPTION>\n";
		$this->filterPars = explode("|", $this->parsFilterSort);
		for($i = 0; $i < count($this->filterPars); $i++) {
			$this->valOptions = explode("#", $this->filterPars[$i]);
			if (!empty($this->valOptions[0]) && !empty($this->valOptions[1])) {
				$this->options .= "<OPTION VALUE=\"" . $this->valOptions[0] . "\">" . $this->valOptions[1] . "</OPTION>\n";
			}
		}
		return $this->options;
	}

	//!-----------------------------------------------------------------
	// @function	FormDataBind::_buildCsvDbToolbar
	// @desc		Constrói os botões e ferramentas de navegação, manipulação,
	// 				ordenação e filtragem de registros e aplica os valores obtidos
	//				no template do formulário
	// @access		private
	// @return		void	
	// @note 		Gera um erro caso a barra de navegação não tenha sido
	// 				definida no template principal	
	//!-----------------------------------------------------------------
	function _buildCsvDbToolbar() {
		$toolbarValues = PHP2Go::getLangVal('FORM_DATA_BIND_TOOLBAR_VALUES');
		$globalDisabled = $this->readonly ? ' DISABLED' : '';
		$this->csvDbToolbar = array();
		$this->csvDbToolbar[0]['NAME'] = "_first";
		$this->csvDbToolbar[0]['CONTENT'] = "<INPUT ID=\"fdb_navFirst\" NAME=\"navFirst\" TYPE=\"button\" DATASRC=\"#" . $this->csvDbName . "\" DATAFLD=\"navFirst\" VALUE=\"<<\" " . $this->extraFunctions['FIRST'] . parent::getButtonStyle() . " " . HtmlUtils::statusBar($toolbarValues['navFirstTip']) . ">\n";
		$this->csvDbToolbar[1]['NAME'] = "_previous";
		$this->csvDbToolbar[1]['CONTENT'] = "<INPUT ID=\"fdb_navPrevious\" NAME=\"navPrevious\" TYPE=\"button\" DATASRC=\"#" . $this->csvDbName . "\" DATAFLD=\"navPrevious\" VALUE=\"<\" " . $this->extraFunctions['PREVIOUS'] . parent::getButtonStyle() . " " . HtmlUtils::statusBar($toolbarValues['navPrevTip']) . ">\n";
		$this->csvDbToolbar[2]['NAME'] = "_next";
		$this->csvDbToolbar[2]['CONTENT'] = "<INPUT ID=\"fdb_navNext\" NAME=\"navNext\" TYPE=\"button\" DATASRC=\"#" . $this->csvDbName . "\" DATAFLD=\"navNext\" VALUE=\">\" " . $this->extraFunctions['NEXT'] . parent::getButtonStyle() . " " . HtmlUtils::statusBar($toolbarValues['navNextTip']) . ">\n";
		$this->csvDbToolbar[3]['NAME'] = "_last";
		$this->csvDbToolbar[3]['CONTENT'] = "<INPUT ID=\"fdb_navLast\" NAME=\"navLast\" TYPE=\"button\" DATASRC=\"#" . $this->csvDbName . "\" DATAFLD=\"navLast\" VALUE=\">>\" " . $this->extraFunctions['LAST'] . parent::getButtonStyle() . " " . HtmlUtils::statusBar($toolbarValues['navLastTip']) . ">\n";
		$this->csvDbToolbar[4]['NAME'] = "_new";
		$this->csvDbToolbar[4]['CONTENT'] = "<INPUT ID=\"fdb_actNew\" NAME=\"actNew\" TYPE=\"button\" DATASRC=\"#" . $this->csvDbName . "\" DATAFLD=\"actNew\" VALUE=\"" . $toolbarValues['actNew'] . "\" " . $this->extraFunctions['NEW'] . parent::getButtonStyle() . " " . HtmlUtils::statusBar($toolbarValues['actAddTip']) . $globalDisabled . ">\n";
		$this->csvDbToolbar[5]['NAME'] = "_edit";
		$this->csvDbToolbar[5]['CONTENT'] = "<INPUT ID=\"fdb_actEdit\" NAME=\"actEdit\" TYPE=\"button\" DATASRC=\"#" . $this->csvDbName . "\" DATAFLD=\"actEdit\" VALUE=\"" . $toolbarValues['actEdit'] . "\" " . $this->extraFunctions['EDIT'] . parent::getButtonStyle() . " " . HtmlUtils::statusBar($toolbarValues['actEditTip']) . $globalDisabled . ">\n";
		$this->csvDbToolbar[6]['NAME'] = "_save";
		$this->csvDbToolbar[6]['CONTENT'] = "<INPUT ID=\"fdb_actSave\" NAME=\"actSave\" TYPE=\"button\" DATASRC=\"#" . $this->csvDbName . "\" DATAFLD=\"actSave\" VALUE=\"" . $toolbarValues['actSave'] . "\" " . $this->extraFunctions['SAVE'] . parent::getButtonStyle() . " " . HtmlUtils::statusBar($toolbarValues['actSaveTip']) . $globalDisabled . ">\n";
		$this->csvDbToolbar[7]['NAME'] = "_delete";
		$this->csvDbToolbar[7]['CONTENT'] = "<INPUT ID=\"fdb_actDel\" NAME=\"actDel\" TYPE=\"button\" DATASRC=\"#" . $this->csvDbName . "\" DATAFLD=\"actDel\" VALUE=\"" . $toolbarValues['actDel'] . "\" " . $this->extraFunctions['DELETE'] . parent::getButtonStyle() . " " . HtmlUtils::statusBar($toolbarValues['actDelTip']) . $globalDisabled . ">\n";
		$this->csvDbToolbar[8]['NAME'] = "_cancel";
		$this->csvDbToolbar[8]['CONTENT'] = "<INPUT ID=\"fdb_actCancel\" NAME=\"actCancel\" TYPE=\"button\" DATASRC=\"#" . $this->csvDbName . "\" DATAFLD=\"actCancel\" VALUE=\"" . $toolbarValues['actCancel'] . "\" " . $this->extraFunctions['CANCEL'] . parent::getButtonStyle() . " " . HtmlUtils::statusBar($toolbarValues['actCancelTip']) . $globalDisabled . ">\n";
		$this->csvDbToolbar[9]['NAME'] = "_sortAsc";
		$this->csvDbToolbar[9]['CONTENT'] = "<A HREF=\"#\" NAME=\"ext_sortAsc\"><IMG SRC=\"" . $this->icons['sortasc'] . "\" NAME=\"img_sortAsc\" BORDER=\"0\" " . HtmlUtils::statusBar($toolbarValues['sortAsc']) . "></A>\n";
		$this->csvDbToolbar[10]['NAME'] = "_sortDesc";
		$this->csvDbToolbar[10]['CONTENT'] = "<A HREF=\"#\" NAME=\"ext_sortDesc\"><IMG SRC=\"" . $this->icons['sortdesc'] . "\" NAME=\"img_sortDesc\" BORDER=\"0\" " . HtmlUtils::statusBar($toolbarValues['sortDesc']) . "></A>\n";
		$this->csvDbToolbar[11]['NAME'] = "_sortSel";
		$this->csvDbToolbar[11]['CONTENT'] = "<SELECT ID=\"ext_sortSel\" NAME=\"ext_sortSel\"" . parent::getInputStyle() . " " . HtmlUtils::statusBar($toolbarValues['sortChoose']) . ">\n" . $this->_buildOptionsList('sort', $toolbarValues) . "</SELECT>\n";
		$this->csvDbToolbar[12]['NAME'] = "_nGoto";
		$this->csvDbToolbar[12]['CONTENT'] = "<INPUT ID=\"ext_nGoto\" NAME=\"ext_nGoto\" TYPE=\"text\" SIZE=\"5\" MAXLENGTH=\"5\" VALUE=\"\" onKeyPress='return chkMaskINTEGER(this, event);'" . parent::getInputStyle() . " " . HtmlUtils::statusBar($toolbarValues['gotoTip']) . ">\n";
		$this->csvDbToolbar[13]['NAME'] = "_bGoto";
		$this->csvDbToolbar[13]['CONTENT'] = "<INPUT ID=\"ext_btnGoto\" NAME=\"ext_btnGoto\" TYPE=\"button\" VALUE=\"" . $toolbarValues['goto'] . "\"" . parent::getButtonStyle() . " " . HtmlUtils::statusBar($toolbarValues['gotoBtnTip']) . ">\n";
		$this->csvDbToolbar[14]['NAME'] = "_filField";
		$this->csvDbToolbar[14]['CONTENT'] = "<SELECT ID=\"ext_sel_FilField\" NAME=\"ext_sel_FilField\" " . parent::getInputStyle() . " " . HtmlUtils::statusBar($toolbarValues['filterTip']) . ">\n" . $this->_buildOptionsList('filter', $toolbarValues) . "</SELECT>\n";
		$this->csvDbToolbar[15]['NAME'] = "_filValue";
		$this->csvDbToolbar[15]['CONTENT'] = "<INPUT ID=\"ext_txtFilValue\" NAME=\"ext_txtFilValue\" TYPE=\"text\" SIZE=\"15\" VALUE=\"\" " . parent::getInputStyle() . " " . HtmlUtils::statusBar($toolbarValues['filterVTip']) . ">\n";
		$this->csvDbToolbar[16]['NAME'] = "_filButton";
		$this->csvDbToolbar[16]['CONTENT'] = "<INPUT ID=\"ext_btnFilButton\" NAME=\"ext_btnFilButton\" TYPE=\"button\" VALUE=\"" . $toolbarValues['filter'] . "\" " . parent::getButtonStyle() . " " . HtmlUtils::statusBar($toolbarValues['filterBtnTip']) . ">\n";
		$this->csvDbToolbar[17]['NAME'] = "_count";
		$this->csvDbToolbar[17]['CONTENT'] = "<DIV ID=\"recCount\" NAME=\"recCount\"" . parent::getLabelStyle() . "></DIV>\n";
		$this->csvDbToolbar[18]['NAME'] = "_sortTitle";
		$this->csvDbToolbar[18]['CONTENT'] = "<LABEL FOR=\"ext_sortSel\"" . parent::getLabelStyle() . ">" . $toolbarValues['sortTit'] . "</LABEL>";
		$this->csvDbToolbar[19]['NAME'] = "_filTitle";
		$this->csvDbToolbar[19]['CONTENT'] = "<LABEL FOR=\"ext_sel_FilField\"" . parent::getLabelStyle() . ">" . $toolbarValues['filterTit'] . "</LABEL>";
		$this->csvDbToolbar[20]['NAME'] = "_gotoTitle";
		$this->csvDbToolbar[20]['CONTENT'] = "<LABEL FOR=\"ext_nGoto\"" . parent::getLabelStyle() . ">" . $toolbarValues['gotoTit'] . "</LABEL>";
		// constrói a barra de ferramentas da classe a partir de um template auxiliar pré-definido
		if ($this->Template->isVariableDefined("databind_toolbar")) {            
			$_ToolbarTemplate = new Template(PHP2GO_TEMPLATE_PATH . "formdatabind.tpl");
			$_ToolbarTemplate->parse();
			$sizeToolbar = sizeOf($this->csvDbToolbar);
			for ($i = 0; $i < $sizeToolbar; $i++) {
				$_ToolbarTemplate->assign("_ROOT." . $this->csvDbToolbar[$i]["NAME"], $this->csvDbToolbar[$i]["CONTENT"]);
			}
			$this->Template->assign("_ROOT.databind_toolbar", $_ToolbarTemplate->getContent());
 		} else {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CANT_FIND_VARIABLE', array('databind_toolbar', $this->templateFile, 'databind_toolbar')), E_USER_ERROR, __FILE__, __LINE__);
		}		
	}

	//!-----------------------------------------------------------------
	// @function	FormDataBind::_buildSection
	// @desc		Atribui no template os rótulos e códigos dos campos e
	//				botões de uma seção de formulário
	// @access 		private
	// @param		section FormSection object	Seção do formulário
	// @return		void	
	//!-----------------------------------------------------------------
	function _buildSection($section) {
		if (TypeUtils::isInstanceOf($section, 'formsection')) {
			$sectionId = $section->getId();
			if ($section->isConditional()) {
				if (!$this->Template->isBlockDefined($sectionId)) {
					PHP2Go::raiseError(PHP2Go::getLangVal('ERR_FORM_SECTION_TPLBLOCK', array($section->getId(), $section->getId())), E_USER_ERROR, __FILE__, __LINE__);
				}
				if (!$section->show)
					continue;
				$this->Template->createBlock($sectionId);
				$this->Template->assign("$sectionId.section_" . $sectionId, $section->name);
				for ($i = 0; $i < sizeOf($section->getChildren()); $i++) {
					$object =& $section->getChild($i);					
					if ($section->getChildType($i) == 'SECTION') {
						$this->_buildSection($object);
					} 
					else if ($section->getChildType($i) == 'BUTTON') {
						$this->Template->assign("$sectionId." . $object->getName(), $object->getCode());
					}
					else if ($section->getChildType($i) == 'BUTTONGROUP') {
						for ($j=0; $j<sizeOf($object); $j++) {
							$button = $object[$j];
							$this->Template->assign("$sectionId." . $button->getName(), $button->getCode());
						}						
					} 					
					else if ($section->getChildType($i) == 'FIELD') {
						$this->Template->assign("$sectionId.label_" . $object->getName(), $object->getLabelCode($section->attrs['REQUIRED_FLAG'], $section->attrs['REQUIRED_COLOR'], $section->attrs['REQUIRED_TEXT']));
						$this->Template->assign("$sectionId." . $object->getName(), $object->getCode());
					}				
				}
			// seção normal
			} else {
				$this->Template->assign("_ROOT.section_" . $sectionId, $section->name);
				for ($i = 0; $i < sizeOf($section->getChildren()); $i++) {
					$object =& $section->getChild($i);					
					if ($section->getChildType($i) == 'SECTION') {
						$this->_buildSection($object);
					} 
					else if ($section->getChildType($i) == 'BUTTON') {
						$this->Template->assign("_ROOT." . $object->getName(), $object->getCode());						
					}
					else if ($section->getChildType($i) == 'BUTTONGROUP') {
						for ($j=0; $j<sizeOf($object); $j++) {
							$button = $object[$j];
							$this->Template->assign("_ROOT." . $button->getName(), $button->getCode());
						}						
					}					
					else if ($section->getChildType($i) == 'FIELD') {
						$this->Template->assign("_ROOT.label_" . $object->getName(), $object->getLabelCode($section->attrs['REQUIRED_FLAG'], $section->attrs['REQUIRED_COLOR'], $section->attrs['REQUIRED_TEXT']));
						$this->Template->assign("_ROOT." . $object->getName(), $object->getCode());
					}				
				}
			}
		}		
	}

	//!-----------------------------------------------------------------
	// @function	FormDataBind::_generateDbCsvScript
	// @desc		Gera o código Javascript utilizado nas operações sobre
	//				os dados apresentados no formulário
	// @access		private
	// @return		void	
	//!-----------------------------------------------------------------
	function _generateDbCsvScript() {
		$lastPosition = HttpRequest::getVar('lastposition');
		$removeId = HttpRequest::getVar('removeid');
		$this->Document->addScript(PHP2GO_JAVASCRIPT_PATH."jsrsclient.js");
		$this->Document->addScript(PHP2GO_JAVASCRIPT_PATH."masks/integer.js");
		$this->Document->addScriptCode(
				"     var absoluteUri   = \"" . PHP2GO_OFFSET_PATH . "/jsrs/\";\n".
				"     var csvDbName     = \"" . $this->csvDbName . "\";\n".
				"     var csvDbForm     = document." . $this->formName . ";\n".
				"     var csvDbFormName = \"" . $this->formName . "\";\n".
				"     var forcePost     = " . (($this->forcePost == TRUE) ? "true" : "false") . ";\n".
				"     var tableName     = \"" . $this->tableName . "\";\n".
				"     var pk            = \"" . $this->primaryKey . "\";"
		);
		$databindSource = PHP2GO_OFFSET_PATH . '/cache/' . $this->csvFile;
		$this->dataBindCode .= "\n".
		"     <OBJECT NAME=\"{$this->csvDbName}\" CLASSID=\"clsid:333C7BC4-460F-11D0-BC04-0080C7055A83\" WIDTH=\"0\" HEIGHT=\"0\">\n".
		"       <PARAM NAME=\"DataURL\" VALUE=\"{$databindSource}\">\n".
		"       <PARAM NAME=\"UseHeader\" value=\"True\">\n".
		"       <PARAM NAME=\"TextQualifier\" VALUE=\"'\">\n".
		"       <PARAM NAME=\"CaseSensitive\" VALUE=\"false\">\n".
		"     </OBJECT>";
		$this->dataBindCode .= "\n".
		"     <SCRIPT LANGUAGE=\"JavaScript\" TYPE=\"text/javascript\">\n".
		"     var g_fFieldsChanged = 0;\n".
		"     var initPos = '';\n".
		"     var action = '';\n".
		"     var oldValues = '';\n".
		"     var csvDbObject = eval(\"document.all.\"+csvDbName);\n".
		"     var readonlyForm = " . ($this->readonly ? 'true' : 'false') . ";\n".
		"     function ADONavigate(oElement, oDSO) {\n".
		"          if (oDSO == null || oElement == null) {\n".
		"               return;\n".
		"          }\n".
		"          cNAME = oElement.name;\n".
		"          if (cNAME == 'navFirst' && oElement.disabled == false) {\n".
		"               oDSO.recordset.MoveFirst();\n".
		"          } else if (cNAME == 'navPrevious' && oElement.disabled == false) {\n".
		"               if (oDSO.recordset.BOF) {\n".
		"                    oDSO.recordset.MoveFirst();\n".
		"               } else {\n".
		"                    if(oDSO.recordset.AbsolutePosition != 1) {\n".
		"                         oDSO.recordset.MovePrevious();\n".
		"                    }\n".
		"               }\n".
		"          } else if (cNAME == 'navNext' && oElement.disabled == false) {\n".
		"               if (oDSO.recordset.EOF) {\n".
		"                    oDSO.recordset.MoveLast();\n".
		"               } else if(oDSO.recordset.AbsolutePosition != oDSO.recordset.RecordCount) {\n".
		"                    oDSO.recordset.MoveNext();\n".
		"               }\n".
		"          } else if (cNAME == 'navLast' && oElement.disabled == false) {\n".
		"               oDSO.recordset.MoveLast();\n".
		"          }\n".
		"     }\n".
		"     function pageActions(elem) {\n".
		"          if(elem.disabled == false) {\n".
		"               if(elem.name == 'actNew') {\n".
		"                    action = 'add';\n".
		"                    addReg();\n".
		"               } else if(elem.name == 'actEdit') {\n".
		"                    if(csvDbObject.recordset.RecordCount > 0) {\n".
		"                         action = 'edit';\n".
		"                         editReg();\n".
		"                    } else alert(csvDbEmptyAlt);\n".
		"               } else if(elem.name == 'actSave') {\n".
		"                    eval(\"if (\"+csvDbFormName+\"_submit()) { if (forcePost == true) saveRegPost(); else saveReg(); }\");\n".
		"               } else if(elem.name == 'actDel') {\n".
		"                    if(csvDbObject.recordset.RecordCount > 0) {\n".
		"                         if(confirm(csvDbDelConf)) {\n".
		"                              if (forcePost == true ) delRegPost(); else delReg();\n".
		"                         }\n".
		"                    } else alert(csvDbEmptyDel);\n".
		"               } else if(elem.name == 'actCancel') {\n".
		"                    cancelAction();\n".
		"               }\n".
		"          }\n".
		"     }\n".
		"     function pageNavigation(elem) {\n".
		"          if(elem.disabled == false) {\n".
		"               if(elem.name == 'navFirst') {\n".
		"                    setTimeout(\"RecCountFirst()\",100);\n".
		"               } else if(elem.name == 'navPrevious') {\n".
		"                    setTimeout(\"RecCountPrevious()\",100);\n".
		"               } else if(elem.name == 'navNext') {\n".
		"                    setTimeout(\"RecCountNext()\",100);\n".
		"               } else if(elem.name == 'navLast') {\n".
		"                    setTimeout(\"RecCountLast()\",100);\n".
		"               }\n".
		"          }\n".
		"     }\n".
		"     function cacheValues(frec,action) {\n".
		"          oldValues = '';\n".
		"          if(action == 'edit') {\n".
		"               var oldFields = csvDbObject.recordset.fields\n".
		"               for(i=0;i<oldFields.count;i++) {\n".
		"                    var bn = (i+1) == oldFields.count ? '' : '#'\n".
		"                    oldValues += oldFields(i).value + bn;\n".
		"               }\n".
		"          }\n".
		"          initPos = csvDbObject.recordset.AbsolutePosition;\n".
		"     }\n".
		"     function cacheAction(action) {\n".
		"          document.all.action.value = action;\n".
		"     }\n".
		"     function addReg() {\n".
		"          cacheValues(csvDbObject.recordset.fields);\n".
		"          csvDbObject.recordset.AddNew();\n".
		"          showRecCount(csvDbObject.recordset.AbsolutePosition,csvDbObject.recordset.RecordCount);\n".
		"          disabledForm(false);\n".
		"          thisForm = eval(\"document.\"+csvDbFormName);\n".
		"          for (var i=0; i<thisForm.length; i++) { if (thisForm[i].tabIndex == 1) thisForm[i].focus(); }\n".
		"          disabledButtons(true);\n".
		"          disabledNavigation(true);\n".
		"     }\n".
		"     function editReg() {\n".
		"          cacheValues(csvDbObject.recordset.fields,action);\n".
		"          disabledForm(false);\n".
		"          disabledButtons(true);\n".
		"          disabledNavigation(true);\n".
		"     }\n".
		"     function saveReg() {\n".
		"          var elementsPage = eval(\"document.\"+csvDbFormName);\n".
		"          var frecord = csvDbObject.recordset.fields;\n".
		"          var valores = '';\n".
		"          for (n = 0; n < elementsPage.length; n++) {\n".
		"               var bn = ((n+1) == elementsPage.length) ? '' : '#';\n".
		"               if(elementsPage[n].type == 'radio') {\n".
		"                    if(elementsPage[n].checked == true) {\n".
		"                         valores += elementsPage[n].name + '|' + elementsPage[n].value + bn;\n".
		"                    }\n".
		"               } else {\n".
		"                    valores += elementsPage[n].name + '|' + elementsPage[n].value + bn;\n".
		"               }\n".
		"          }\n".
		"          jsrsExecute(absoluteUri+'csvdbpersist.php', saveRegReturn, \"processPost\", Array(valores, tableName, pk));\n".
		"          window.status = \"\";\n".
		"     }\n".
		"     function saveRegReturn(retorno) {\n".
		"          if(!parseInt(retorno)) {\n".
		"               alert(retorno);\n".
		"               cancelAction();\n".
		"          } else {\n".
		"               if(action == 'add') {\n".
		"                    alert(csvDbInsMsg);\n".
		"                    eval(\"var pkField = document.\"+csvDbFormName+\"['\"+pk+\"'];\");\n".
		"                    pkField.value = retorno;\n".
		"               } else if(action == 'edit') {\n".
		"                    alert(csvDbAltMsg);\n".
		"               }\n".
		"               disabledForm(true);\n".
		"               disabledButtons(false);\n".
		"               disabledNavigation(false);\n".
		"               return;\n".
		"          }\n".
		"     }\n".
		"     function saveRegPost() {\n".
		"          var csvDbForm = eval(\"document.\"+csvDbFormName);\n".
		"          csvDbForm['lastposition'].value = csvDbObject.recordset.AbsolutePosition;\n".
		"          csvDbForm.submit();\n".
		"     }\n".
		"     function delReg() {\n".
		"          var valor = '';\n".
		"          var frecord = csvDbObject.recordset.fields;\n".
		"          for (n = 0; n < frecord.count; n++) {\n".
		"               if(pk == frecord(n).name) {\n".
		"                    valor = frecord(n).value;\n".
		"               }\n".
		"          }\n".
		"          jsrsExecute(absoluteUri+'csvdbpersist.php', delRegReturn, \"deleteReg\", Array(tableName, pk, valor));\n".
		"          csvDbObject.recordset.Delete();\n".
		"          window.status = \"\";\n".
		"          showRecCount(csvDbObject.recordset.AbsolutePosition,csvDbObject.recordset.RecordCount);\n".
		"     }\n".
		"     function delRegReturn(retorno) {\n".
		"          if(!parseInt(retorno)) {\n".
		"               alert(retorno);\n".
		"               cancelAction();\n".
		"          } else alert(csvDbDelMsg);\n".
		"          disabledNavigation(false);\n".
		"          return;\n".
		"     }\n".
		"     function delRegPost() {\n".
		"          var valor = '';\n".
		"          var frecord = csvDbObject.recordset.fields;\n".
		"          for (n = 0; n < frecord.count; n++) {\n".
		"               if(pk == frecord(n).name) {\n".
		"                    valor = frecord(n).value;\n".
		"               }\n".
		"          }\n".
		"          var csvDbForm = eval(\"document.\"+csvDbFormName);\n".
		"          csvDbForm['lastposition'].value = csvDbObject.recordset.AbsolutePosition;\n".
		"          csvDbForm['removeid'].value = valor;\n".
		"          csvDbForm.submit();\n".
		"     }\n".
		"     function disabledForm(acao) {\n".
		"          if (readonlyForm) return;\n".
		"          var thisForm = document.forms[csvDbFormName]\n".
		"          for(var i=0; i < thisForm.length; i++) {\n".
		"               if((thisForm[i].name.substring(0,3) != 'act') && (thisForm[i].name.substring(0,3) != 'ext')) {\n".
		"                    thisForm[i].disabled = acao;\n".
		"               }\n".
		"          }\n".
		"     }\n".
		"     function disabledButtons(acao) {\n".
		"          if (readonlyForm) return;\n".
		"          inputs = document.getElementsByTagName('INPUT');\n".
		"          for(i=0;i<inputs.length;i++) {\n".
		"               if (inputs[i].name == \"ext_btnFilButton\" || inputs[i].name == \"ext_btnGoto\") {\n".
		"                    inputs[i].disabled = acao;\n".
		"               }\n".
		"               if(inputs[i].name.substring(0,3) == 'act') {\n".
		"                    var inputName = inputs[i].name.substring(3,inputs[i].name.length);\n".
		"                    if(inputName == 'New' || inputName == 'Edit' || inputName == 'Del') {\n".
		"                         inputs[i].disabled = acao;\n".
		"                    } else if(inputName == 'Save' || inputName == 'Cancel') {\n".
		"                         inputs[i].disabled = !acao;\n".
		"                    }\n".
		"               }\n".
		"          }\n".
		"     }\n".
		"     function disabledNavigation(acao) {\n".
		"          if (readonlyForm) return;\n".
		"          inputs = document.getElementsByTagName('INPUT');\n".
		"          rset = csvDbObject.recordset;\n".
		"          for(i=0;i<inputs.length;i++) {\n".
		"               if(inputs[i].name.substring(0,3) == 'nav') {\n".
		"                    if(csvDbObject.recordset.RecordCount > 0) {\n".
		"                         inputs[i].disabled = acao;\n".
		"                    } else inputs[i].disabled = true;\n".
		"               }\n".
		"          }\n".
		"     }\n".
		"     function cancelAction() {\n".
		"          if(action == 'add') {\n".
		"               csvDbObject.recordset.Delete();\n".
		"          }\n".
		"          if(action == 'edit') {\n".
		"               fvalues = oldValues.split(\"#\");\n".
		"               for(i=0;i<csvDbObject.recordset.fields.count;i++) {\n".
		"                    fval = fvalues[i] == '' ? '' : fvalues[i];\n".
		"                    csvDbObject.recordset.fields(i).value = fval;\n".
		"               }\n".
		"          }\n".
		"          if(csvDbObject.recordset.AbsolutePosition > 0) {\n".
		"               csvDbObject.recordset.AbsolutePosition = parseInt(initPos) ? initPos : 1;\n".
		"          }\n".
		"          showRecCount(csvDbObject.recordset.AbsolutePosition,csvDbObject.recordset.RecordCount);\n".
		"          disabledForm(true);\n".
		"          disabledButtons(false);\n".
		"          disabledNavigation(false);\n".
		"     };\n".
		"     function showRecCount(rec,reccount) {\n".
		"          if(rec > 0) {\n".
		"               document.all.recCount.innerHTML = 'Registro&nbsp;&nbsp;'+ rec +'/'+ reccount;\n".
		"          } else if(rec <= 0) {\n".
		"               document.all.recCount.innerHTML = 'Registro&nbsp;&nbsp;0/0';\n".
		"          }\n".
		"     }\n".
		"     function RecCountFirst() {\n".
		"          setTimeout(\"showRecCount(csvDbObject.recordset.AbsolutePosition,csvDbObject.recordset.RecordCount)\",10);\n".
		"     }\n".
		"     function RecCountPrevious() {\n".
		"          setTimeout(\"showRecCount(csvDbObject.recordset.AbsolutePosition,csvDbObject.recordset.RecordCount)\",10);\n".
		"     }\n".
		"     function RecCountNext() {\n".
		"          setTimeout(\"showRecCount(csvDbObject.recordset.AbsolutePosition,csvDbObject.recordset.RecordCount)\",10);\n".
		"     }\n".
		"     function RecCountLast() {\n".
		"          setTimeout(\"showRecCount(csvDbObject.recordset.AbsolutePosition,csvDbObject.recordset.RecordCount)\",10);\n".
		"     }\n".
		"     function gotoField(ext) {\n".
		"          if (ext == null) cancelAction();\n".
		"          var gotoF = (ext == null) ? parseInt(document.all.ext_nGoto.value) : parseInt(ext)\n".
		"          var nFields =  csvDbObject.recordset.RecordCount\n".
		"          if(gotoF != '') {\n".
		"               if(gotoF <= nFields) {\n".
		"                    csvDbObject.recordset.AbsolutePosition = gotoF;\n".
		"                    setTimeout(\"showRecCount(csvDbObject.recordset.AbsolutePosition, csvDbObject.recordset.RecordCount)\", 100);\n".
		"               } else if (ext == null) {\n".
		"                    alert(csvDbErrGoto);\n".
		"                    document.all.ext_nGoto.value = \"\";\n".
		"                    document.all.ext_nGoto.focus();\n".
		"               } else if (ext != null) {\n".
		"                    csvDbObject.recordset.AbsolutePosition = nFields;\n".
		"                    setTimeout(\"showRecCount(csvDbObject.recordset.AbsolutePosition,csvDbObject.recordset.RecordCount)\", 100);\n".
		"               }\n".
		"          } else alert(csvDbEmptyGoto)\n".
		"     }\n".
		"     function restorePosition() {\n".
		"          var csvDbForm = eval(\"document.\"+csvDbFormName);\n".
		"          if (csvDbForm['lastposition'].value != '') {\n".
		"               gotoField(csvDbForm['lastposition'].value);\n".
		"               csvDbForm['lastposition'].value = '';\n".
		"          }\n".
		"     }\n".
		"     function applyFilter() {\n".
		"          cancelAction();\n".
		"          var ini = csvDbObject.recordset.AbsolutePosition\n".
		"          cField = document.all.ext_sel_FilField.options[document.all.ext_sel_FilField.selectedIndex].value;\n".
		"          cValue = document.all.ext_txtFilValue.value;\n".
		"          if(cField != '' && cValue != '') {\n".
		"               csvDbObject.Filter = cField + ' >= ' + cValue;\n".
		"               csvDbObject.Reset();\n".
		"               var rcount = csvDbObject.recordset.RecordCount;\n".
		"               if(rcount == 0) {\n".
		"                    alert(csvDbEmptyFilt);\n".
		"               }\n".
		"               setTimeout(\"showRecCount(csvDbObject.recordset.AbsolutePosition,csvDbObject.recordset.RecordCount)\",100);\n".
		"          } else {\n".
		"               csvDbObject.Filter = '';\n".
		"               csvDbObject.Reset();\n".
		"               alert(csvDbErrFilt);\n".
		"               document.all.ext_sel_FilField.focus();\n".
		"          }\n".
		"     }\n".
		"     function applySort(direct) {\n".
		"          cancelAction();\n".
		"          vSort = document.all.ext_sortSel.options[document.all.ext_sortSel.selectedIndex].value;\n".
		"          if(vSort != '') {\n".
		"               if(direct == 'img_sortAsc') {\n".
		"                    csvDbObject.SortColumn = vSort;\n".
		"                    csvDbObject.SortAscending = true;\n".
		"               } else if(direct == 'img_sortDesc') {\n".
		"                    csvDbObject.SortColumn = vSort;\n".
		"                    csvDbObject.SortAscending = false;\n".
		"               }\n".
		"               csvDbObject.Reset();\n".
		"          } else {\n".
		"               alert(csvDbErrSort);\n".
		"               document.all.ext_sortSel.focus();\n".
		"          }\n".
		"          setTimeout(\"showRecCount(csvDbObject.recordset.AbsolutePosition,csvDbObject.recordset.RecordCount)\",100);\n".
		"     }\n".
		"     function lastTabIndex() {\n".
		"          var thisForm = document.forms[csvDbFormName]\n".
		"          var max = 0;\n".
		"          for(i=0;i<thisForm.length;i++) {\n".
		"               var tabindex = thisForm[i].tabIndex;\n".
		"               if(tabindex > max) max = tabindex;\n".
		"          }\n".
		"          thisForm.actSave.tabIndex = (max+1);\n".
		"          thisForm.actCancel.tabIndex = (max+2);\n".
		"     }\n".
		"     </SCRIPT>\n".
		"     <SCRIPT FOR='document' EVENT='onclick'>\n".
		"          var oElement = window.event.srcElement;\n".
		"          if (oElement.name && !oElement.disabled)\n{".
		"               if (oElement.name.substring(0,3) == 'nav')\n{".
		"                    ADONavigate(oElement, csvDbObject);\n".
		"                    pageNavigation(oElement);\n".
		"               }\n".
		"          		if (oElement.name.substring(0,3) == 'act')		pageActions(oElement);\n".
		"               if (oElement.name == 'ext_btnGoto')             gotoField();\n".
		"               if (oElement.name == 'ext_btnFilButton')        applyFilter();\n".
		"               if (oElement.name.substring(0,8) == 'img_sort') applySort(oElement.name);\n".
		"          }\n".
		"     </SCRIPT>\n".
		"     <SCRIPT FOR='{$this->csvDbName}' EVENT=\"ondatasetcomplete\">\n".
		"       if (!readonlyForm) {\n".
		"            setTimeout(\"disabledButtons(false);\",100);\n".
		"            setTimeout(\"disabledNavigation(false);\",100);\n".
		"       }\n".
		"       setTimeout(\"showRecCount(csvDbObject.recordset.AbsolutePosition,csvDbObject.recordset.RecordCount);\",100);\n".
		"       setTimeout(\"lastTabIndex();\",100);\n".
		"       setTimeout(\"restorePosition()\", 100);\n".
		"     </SCRIPT>\n";
	}
	
	//!-----------------------------------------------------------------
	// @function	FormDataBind::_createDbCsvFile
	// @desc		Cria o arquivo .csv que será utilizado para navegação
	//				nos registros da tabela a partir dos resultados da
	//				consulta
	// @access		private
	// @return		void	
	//!-----------------------------------------------------------------
	function _createDbCsvFile() {
		// remove os arquivos anteriormente armazenados
		$dir = @opendir(PHP2GO_CACHE_PATH);
		while (false !== ($file = readdir($dir))) {
			if (eregi("db.*\.csv", $file)) {
				if (!@unlink(PHP2GO_CACHE_PATH . $file)) PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CANT_DELETE_FILE', $file), E_USER_ERROR, __FILE__, __LINE__);
			}
		}
		// monta a query
		$Query = new QueryBuilder($this->queryFields, $this->queryTables, $this->queryClause, '', $this->queryOrder);
		// instancia e popula o dataset
		$DataSet =& DataSet::getInstance('db');
		if (empty($this->queryLimit))
			$DataSet->load($Query->getQuery());
		else
			$DataSet->loadSubSet(0, $this->queryLimit, $Query->getQuery());
		// armazena dados do dataset
		$this->csvFile = $this->csvDbName . '_' . time() . '.csv';
		$Mgr = new FileManager();
		$Mgr->open(PHP2GO_CACHE_PATH . $this->csvFile, FILE_MANAGER_WRITE_BINARY);		
		// monta o conteúdo do arquivo
		$Mgr->writeLine(implode(',', $DataSet->getFieldNames()));
		while (!$DataSet->eof()) {
			$row = $DataSet->current();
			foreach ($row as $column => $value)
				$row[$column] = "'" . ereg_replace(",", ",", $value) . "'";
			$Mgr->writeLine(implode(',', $row));
			$DataSet->moveNext();
		}
		$Mgr->close();
	}
}
?>