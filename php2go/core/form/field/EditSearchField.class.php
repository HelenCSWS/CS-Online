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
// $Header: /www/cvsroot/php2go/core/form/field/EditSearchField.class.php,v 1.3 2005/09/01 13:55:52 mpont Exp $
// $Date: 2005/09/01 13:55:52 $

//------------------------------------------------------------------
import('php2go.form.field.DbField');
import('php2go.form.field.LookupField');
import('php2go.util.service.ServiceJSRS');
//------------------------------------------------------------------

// @const EDITSEARCH_DEFAULT_SIZE "10"
// Tamanho padrão do campo que contém o termo de pesquisa
define('EDITSEARCH_DEFAULT_SIZE', 10);

//!-----------------------------------------------------------------
// @class		EditSearchField
// @desc		A classe EditSearchField implementa um pequeno e eficiente componente
//				de pesquisa, baseado em um conjunto de filtros e um campo de digitação
//				do termo de pesquisa. Partindo de uma consulta SQL base, o componente
//				inclui a cláusula definida para o filtro escolhido, e popula um campo
//				SELECT com os resultados da pesquisa. A requisição da pesquisa é
//				realizada utilizando JSRS
// @package		php2go.form.field
// @extends		DbField
// @author		Marcos Pont
// @version		$Revision: 1.3 $
//!-----------------------------------------------------------------
class EditSearchField extends DbField
{
	var $filters = array();		// @var filters array					"array()" Conjunto de filtros
	var $jsObjectName;			// @var jsObjectName string				Nome da instância da classe JavaScript EditSearch a ser utilizada
	var $_LookupField;			// @var _LookupField LookupField object	Objeto LookupField usado para exibir os resultados da pesquisa
	
	//!-----------------------------------------------------------------
	// @function	EditSearchField::EditSearchField
	// @desc		Construtor da classe
	// @access		public
	// @param		&Form Form object	Formulário no qual o campo é inserido		
	//!-----------------------------------------------------------------
	function EditSearchField(&$Form) {
		parent::DbField($Form, FALSE);
		$this->composite = TRUE;
		$this->searchable = FALSE;
		$this->jsObjectName = PHP2Go::generateUniqueId($this->objectName);
	}
	
	//!-----------------------------------------------------------------
	// @function	EditSearchField::getCode
	// @desc		Monta e retorna o código HTML da estrutura de campos
	// @return		string Código HTML dos campos e do botão de pesquisa,
	//				dispostos de acordo com um template interno pré-definido
	// @access		public
	//!-----------------------------------------------------------------
	function getCode() {
		$this->onPreRender();
		$this->_Form->Document->addScript(PHP2GO_JAVASCRIPT_PATH . 'jsrsclient.js');
		$this->_Form->Document->addScript(PHP2GO_JAVASCRIPT_PATH . 'modules/editsearch.js');
		// instancia o template de exibição do campo
		$Tpl = new Template(PHP2GO_TEMPLATE_PATH . 'editsearchfield.tpl');
		$Tpl->parse();
		$filters = sprintf("<SELECT ID=\"%s\" NAME=\"%s\" TITLE=\"%s\"%s%s%s%s>",
				$this->name . '_filters', $this->name . '_filters', $this->label, '', 
				$this->attributes['TABINDEX'], $this->attributes['STYLE'], $this->attributes['DISABLED']
		);
		$masks = array();
		foreach ($this->filters as $value => $data) {
			$filters .= sprintf("<OPTION VALUE=\"%s\">%s</OPTION>", $value, $data[0]);
			$masks[] = $data[2];
			if (substr($data[2], 0, 3) == 'ZIP')
				$this->_Form->Document->addScript(PHP2GO_JAVASCRIPT_PATH . 'masks/zip.js');
			elseif (substr($data[2], 0, 5) == 'FLOAT')
				$this->_Form->Document->addScript(PHP2GO_JAVASCRIPT_PATH . 'masks/float.js');
			elseif ($data[2] != 'STRING')
				$this->_Form->Document->addScript(PHP2GO_JAVASCRIPT_PATH . 'masks/' . strtolower($data[2]) . '.js');
		}
		$filters .= "</SELECT><INPUT TYPE=\"hidden\" ID=\"{$this->name}_masks\" NAME=\"{$this->name}_masks\" VALUE=\"" . implode(',', $masks) . "\"/>";
		$Tpl->assign('filters', $filters);
		$Tpl->assign('search', sprintf("<INPUT TYPE=\"text\" ID=\"%s\" NAME=\"%s\" VALUE=\"\" MAXLENGTH=\"%s\" SIZE=\"%s\" TITLE=\"%s\"%s%s%s%s%s>&nbsp;",
			$this->name, $this->name, $this->attributes['LENGTH'], $this->attributes['SIZE'], $this->label, $this->attributes['SCRIPT'], 
			$this->attributes['TABINDEX'], $this->attributes['STYLE'], $this->attributes['DISABLED'], $this->attributes['AUTOCOMPLETE']
		));
		if (!empty($this->attributes['BTNIMG']))
			$Tpl->assign('button', sprintf("<A ID=\"%s\" HREF=\"javascript:void(0);\"%s%s %s>%s</A>", 
				$this->name . '_button', " onClick=\"{$this->jsObjectName}.submit(this)\"", 
				$this->_Form->getLabelStyle(), HtmlUtils::image($this->attributes['BTNIMG'])
			));
		else
			$Tpl->assign('button', sprintf("<INPUT ID=\"%s\" NAME=\"%s\" TYPE=\"BUTTON\" VALUE=\"%s\"%s%s%s%s><BR>", 
				$this->name . '_button', $this->name . '_button', $this->attributes['BTNVALUE'], 
				" onClick=\"{$this->jsObjectName}.submit(this)\"", $this->_Form->getButtonStyle(), 
				$this->attributes['TABINDEX'], $this->attributes['DISABLED']
			));
		$Tpl->assign('lookup', $this->_LookupField->getCode());
		$Tpl->assign('js_code', sprintf("<SCRIPT TYPE=\"text/javascript\">%s = new EditSearch(\"%s\",\"%s\",\"%s\",\"%s\",%s,%s,%s,'%s');</SCRIPT>",
				$this->jsObjectName, HttpRequest::uri(), $this->_Form->formName, $this->name, 
				$this->_LookupField->name, ($this->_LookupField->attributes['NOFIRST'] == 'T' ? '0' : '1'), 
				($this->attributes['DEBUG'] ? 'true' : 'false'), ($this->attributes['AUTOTRIM'] ? 'true' : 'false'),
				PHP2Go::getConfigVal('LOCAL_DATE_TYPE')
		));
		$this->htmlCode = $Tpl->getContent();
		return $this->htmlCode;		
	}
	
	//!-----------------------------------------------------------------
	// @function	EditSearchField::&getLookupField
	// @desc		Retorna o objecto LookupField que representa a lista de
	//				de resultados da pesquisa
	// @access		public
	// @return		LookupField object	Campo LookupField associado à este campo
	// @note		Retorna NULL se o objeto não foi definido
	//!-----------------------------------------------------------------
	function &getLookupField() {
		if (TypeUtils::isInstanceOf($this->_LookupField, 'lookupfield'))
			return $this->_LookupField;
		return NULL;
	}
	
	//!-----------------------------------------------------------------
	// @function	EditSearchField::setSize
	// @desc		Altera ou define o tamanho do campo
	// @param		size int	Tamanho para o campo de pesquisa
	// @access		public	
	// @return		void
	//!-----------------------------------------------------------------
	function setSize($size) {
		if (TypeUtils::isInteger($size))
			$this->attributes['SIZE'] = $size;
	}

	//!-----------------------------------------------------------------
	// @function	EditSearchField::setLength
	// @desc		Define número máximo de caracteres do campo
	// @param		length int	Máximo de caracteres para o campo de pesquisa
	// @access		public	
	// @return		void
	//!-----------------------------------------------------------------
	function setLength($length) {
		if (TypeUtils::isInteger($length))
			$this->attributes['LENGTH'] = $length;
	}

	//!-----------------------------------------------------------------
	// @function	EditSearchField::setAutoComplete
	// @desc		Define valor para o recurso autocompletar no campo
	// @param		setting mixed	Valor para o atributo AUTOCOMPLETE
	// @access		public	
	// @return		void
	//!-----------------------------------------------------------------
	function setAutoComplete($setting) {
		if (TypeUtils::isTrue($setting))
			$this->attributes['AUTOCOMPLETE'] = " AUTOCOMPLETE=\"ON\"";
		else if (TypeUtils::isFalse($setting))
			$this->attributes['AUTOCOMPLETE'] = " AUTOCOMPLETE=\"OFF\"";
		else
			$this->attributes['AUTOCOMPLETE'] = "";
	}
	
	//!-----------------------------------------------------------------
	// @function	EditSearchField::setAutoTrim
	// @desc		Habilita ou desabilita a remoção automática dos caracteres
	//				brancos no início e no fim do termo de pesquisa no momento
	//				da submissão
	// @access		public
	// @param		setting bool	"TRUE" Valor para o atributo
	// @return		void
	//!-----------------------------------------------------------------
	function setAutoTrim($setting=TRUE) {
		if (TypeUtils::isTrue($setting))
			$this->attributes['AUTOTRIM'] = "T";
		else
			$this->attributes['AUTOTRIM'] = "F";
	}
	
	//!-----------------------------------------------------------------
	// @function	EditSearchField::setButtonValue
	// @desc		Define o valor do botão de pesquisa usado no componente	
	// @param		value string	Valor para o botão
	// @access		public	
	// @return		void
	//!-----------------------------------------------------------------
	function setButtonValue($value) {
		if ($value)
			$this->attributes['BTNVALUE'] = Form::resolveI18nEntry($value);
		else
			$this->attributes['BTNVALUE'] = PHP2Go::getLangVal('DEFAULT_BTN_VALUE');
	}
	
	//!-----------------------------------------------------------------
	// @function	EditSearchField::setButtonImage
	// @desc		Define uma imagem a ser utilizada no botão de pesquisa
	// @param		img string	Caminho da imagem
	// @access		public	
	// @return		void
	//!-----------------------------------------------------------------
	function setButtonImage($img) {
		if ($img)
			$this->attributes['BTNIMG'] = trim($img);
		else
			$this->attribites['BTNIMG'] = '';
	}
	
	//!-----------------------------------------------------------------
	// @function	EditSearchField::setDebug
	// @desc		Habilita ou desabilita debug no mecanismo de pesquisa JSRS
	// @param		setting bool	Valor para o flag
	// @access		public	
	// @return		void
	//!-----------------------------------------------------------------
	function setDebug($setting) {
		$this->attributes['DEBUG'] = TypeUtils::toBoolean($setting);
	}

	//!-----------------------------------------------------------------
	// @function	EditSearchField::onLoadNode
	// @desc		Método responsável por processar atributos e nodos filhos
	//				provenientes da especificação XML do campo
	// @access		public
	// @param		attrs array		Atributos do nodo
	// @param		children array	Vetor de nodos filhos
	// @return		void
	//!-----------------------------------------------------------------
	function onLoadNode($attrs, $children) {
		parent::onLoadNode($attrs, $children);
		if (!empty($this->dataSource) &&  isset($children['DATAFILTER']) && isset($children['LOOKUPFIELD']) &&
			TypeUtils::isInstanceOf($children['LOOKUPFIELD'], 'xmlnode')
		) {
			// armazenamento dos filtros
			$filters = TypeUtils::toArray($children['DATAFILTER']);
			foreach ($filters as $filterNode) {
				$id = $filterNode->getAttribute('ID');
				$label = $filterNode->getAttribute('LABEL');
				$expression = $filterNode->getAttribute('EXPRESSION');
				$mask = TypeUtils::ifFalse($filterNode->getAttribute('MASK'), 'STRING');
				if (empty($id) || empty($label) || empty($expression) || substr_count($expression, '%s') != 1)
					PHP2Go::raiseError(PHP2Go::getLangVal('ERR_EDITSEARCH_INVALID_DATAFILTER', (empty($id) ? '?' : $id)), E_USER_ERROR, __FILE__, __LINE__);
				if ($mask != 'STRING' && !preg_match(PHP2GO_MASK_PATTERN, $mask))
					PHP2Go::raiseError(PHP2Go::getLangVal('ERR_EDITSEARCH_INVALID_DATAFILTER_MASK', $id), E_USER_ERROR, __FILE__, __LINE__);
				if (isset($this->filters[$id]))
					PHP2Go::raiseError(PHP2Go::getLangVal('ERR_EDITSEARCH_DUPLICATED_DATAFILTER', $id), E_USER_ERROR, __FILE__, __LINE__);
				$this->filters[$id] = array($label, $expression, $mask);
			}
			// inicializa o serviço JSRS e trata o evento
			$Service = new ServiceJSRS();
			$Service->registerHandler(array($this, 'performSearch'), 'performSearch');
			$Service->handleRequest();			
			// tamanho do campo de pesquisa
			if (isset($attrs['SIZE']))
				$this->setSize($attrs['SIZE']);
			elseif (isset($attrs['LENGTH']))
				$this->setSize($attrs['LENGTH']);
			else
				$this->setSize(EDITSEARCH_DEFAULT_SIZE);
			// número máximo de caracteres do termo de pesquisa
			if ($attrs['LENGTH'])
				$this->setLength($attrs['LENGTH']);
			else
				$this->setLength($this->attributes['SIZE']);
			// autocomplete
			$this->setAutoComplete(Form::resolveBooleanChoice(@$attrs['AUTOCOMPLETE']));
			// autotrim
			$this->setAutoTrim(Form::resolveBooleanChoice(@$attrs['AUTOTRIM']));
			// valor e imagem do botão
			$this->setButtonValue(@$attrs['BTNVALUE']);
			$this->setButtonImage(@$attrs['BTNIMG']);
			// debug da requisição JSRS
			$this->setDebug(Form::resolveBooleanChoice(@$attrs['DEBUG']));
			// cria o campo lookupfield
			$this->_LookupField = new LookupField($this->_Form, TRUE);
			$this->_LookupField->onLoadNode($children['LOOKUPFIELD']->getAttributes(), $children['LOOKUPFIELD']->getChildrenTagsArray());
			$this->_LookupField->setRequired($this->required);
			$this->_Form->fields[$this->_LookupField->getName()] =& $this->_LookupField;
		} else {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_MISSING_EDITSEARCH_CHILDREN', $this->name), E_USER_ERROR, __FILE__, __LINE__);
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	EditSearchField::onPreRender
	// @desc		Executa as configurações necessárias antes da construção
	//				do código HTML final do componente
	// @access		protected
	// @return		void
	//!-----------------------------------------------------------------
	function onPreRender() {
		parent::onPreRender();
		// desabilita o filho se estiver desabilitado
		$this->_LookupField->setDisabled($this->disabled);
	}
	
	//!-----------------------------------------------------------------
	// @function	EditSearchField::performSearch
	// @desc		Método responsável por executar a pesquisa, utilizando
	//				o tipo de filtro e o termo de pesquisa escolhidos no
	//				formulário. O retorno produzido é uma string contendo
	//				separadores padrão para linhas "|" e colunas "~"
	// @access		protected
	// @param		id int		ID do Filtro
	// @param		term mixed	Termo de pesquisa
	// @return		string Resultados
	//!-----------------------------------------------------------------
	function performSearch($id, $term) {
		if (isset($this->filters[$id])) {
			// constrói a nova cláusula
			$clause = sprintf($this->filters[$id][1], $term);
			if (empty($this->dataSource['CLAUSE']))
				$this->dataSource['CLAUSE'] = $clause;
			else
				$this->dataSource['CLAUSE'] = "({$this->dataSource['CLAUSE']}) AND {$clause}";
			// executa a consulta			
			@parent::processDbQuery(ADODB_FETCH_NUM, ServiceJSRS::debugEnabled());
			// monta a string de resultados
			if ($this->_Rs->RecordCount() > 0) {
				$lines = array();
				while (!$this->_Rs->EOF) {
					$lines[] = @$this->_Rs->fields[0] . '~' . @$this->_Rs->fields[1];
					$this->_Rs->MoveNext();
				}
				return implode('|', $lines);
			} else {
				return '';
			}
		}
	}
}
?>