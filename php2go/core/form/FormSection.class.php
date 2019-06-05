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
// $Header: /www/cvsroot/php2go/core/form/FormSection.class.php,v 1.17 2005/07/25 13:35:02 mpont Exp $
// $Date: 2005/07/25 13:35:02 $

//------------------------------------------------------------------
import('php2go.util.Callback');
//------------------------------------------------------------------

//!-----------------------------------------------------------------
// @class 		FormSection
// @desc 		Armazena informa��es sobre uma se��o de formul�rio, que
// 				consiste em um grupo de campos agrupados em termos de
// 				disposi��o de interface
// @package		php2go.form
// @extends 	PHP2Go
// @author 		Marcos Pont
// @version		$Revision: 1.17 $ 
//!-----------------------------------------------------------------
class FormSection extends PHP2Go
{
	var $name;					// @var name string							Nome da se��o
	var $id;					// @var id string							ID da se��o
	var $attrs;					// @var attrs array							Atributos da se��o
	var $condition;				// @var condition bool						Indica a gera��o da se��o no formul�rio � condicional
	var $evaluateFunc;			// @var evaluateFunc string					Nome da fun��o de avalia��o da gera��o ou n�o da se��o no formul�rio
	var $show = TRUE;			// @var show bool							"TRUE" Indica se a se��o deve ou n�o ser exibida
	var $children;				// @var children array						Elementos subordinados � se��o (subse��es, campos, bot�es)	
	var $childMap;				// @var childMap array						Armazena os filhos de diferentes tipos em �reas separadas, para facilitar a busca de um campo ou bot�o para altera��o
	var $_Form = NULL;			// @var _Form Form object					"NULL" Formul�rio no qual a se��o est� inclu�da	
	var $_Parent = NULL;		// @var _Parent XmlNode object				"NULL" Elemento ao qual a se��o � subordinada no formul�rio (o FORM em si ou uma outra SECTION)
	 
	//!-----------------------------------------------------------------
	// @function 	FormSection::FormSection
	// @desc 		Inicializa a se��o com os atributos lidos a partir
	// 				da especifica��o XML do formul�rio
	// @access 		public 
	// @param 		xmlNode XmlNode object	Objeto XmlNode que cont�m os dados da se��o
	// @param 		&Form Form object		Objeto Form ao qual a se��o pertence
	//!-----------------------------------------------------------------
	function FormSection($xmlNode, &$Form) {
		PHP2Go::PHP2Go();
		$this->attrs = $xmlNode->getAttributes();
		$this->children = array();
		$this->condition = FALSE;
		$this->_Form =& $Form;
		$this->_Parent =& $xmlNode->getParentNode();
		$this->_parseSection();
		$Form->verifySectionId($this->_Form->formName, $this->id);
	} 
	
	//!-----------------------------------------------------------------
	// @function 	FormSection::getName
	// @desc 		Consulta o nome da se��o atual
	// @access 		public 
	// @return 		string Nome da se��o
	//!-----------------------------------------------------------------
	function getName() {
		return $this->name;
	}
	
	//!-----------------------------------------------------------------
	// @function	FormSection::getId
	// @desc		Busca o ID definido para a se��o
	//!-----------------------------------------------------------------
	function getId() {
		return $this->id;
	}
	
	//!-----------------------------------------------------------------
	// @function 	FormSection::getAttribute
	// @desc 		Busca o valor de um atributo da se��o
	// @access 		public 
	// @param 		attrName string	Nome do atributo
	// @return	 	mixed Valor do atributo ou NULL se ele n�o for encontrado
	//!-----------------------------------------------------------------
	function getAttribute($attrName) {
		if (!isset($this->attrs[$attrName])) {
			return NULL;
		} else {
			return trim($this->attrs[$attrName]);
		} 
	}
	
	//!-----------------------------------------------------------------
	// @function	FormSection::isConditional
	// @desc		Verifica se a se��o possui exibi��o condicional
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function isConditional() {
		return $this->condition;
	}
	
	//!-----------------------------------------------------------------
	// @function	FormSection::hasChildren
	// @desc		Verifica se a se��o possui descendentes
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function hasChildren() {
		return (!empty($this->children));
	}
	
	//!-----------------------------------------------------------------
	// @function	FormSection::getChildren
	// @desc		Busca o vetor de elementos subordinados � se��o atual
	// @access		public
	// @return		array
	//!-----------------------------------------------------------------
	function getChildren() {
		return $this->children;
	}
	
	//!-----------------------------------------------------------------
	// @function	FormSection::&getChild
	// @desc		Busca um elemento interno da se��o, a partir de seu �ndice
	// @access		public
	// @param		index int	�ndice do elemento
	// @return		mixed	O objeto alocado no �ndice fornecido ou NULL se o �ndice for inv�lido
	//!-----------------------------------------------------------------
	function &getChild($index) {
		if (isset($this->children[$index])) {
			return $this->children[$index]['object'];
		} else {
			return NULL;
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	FormSection::getChildType
	// @desc		Consulta o tipo de um determinado elemento da se��o
	// @access		public
	// @param		index int	�ndice do elemento
	// @return		string	Tipo do elemento
	// @note		Retorna NULL se o �ndice for inv�lido
	//!-----------------------------------------------------------------
	function getChildType($index) {
		if (isset($this->children[$index])) {
			return $this->children[$index]['type'];
		} else {
			return NULL;
		}		
	}
	
	//!-----------------------------------------------------------------
	// @function	FormSection::&getField
	// @desc		Busca um campo a partir de seu nome dentre os campos filhos da se��o
	// @access		public
	// @param		fieldName string	Nome do campo
	// @return		mixed	Objeto correspondente ao campo ou NULL se n�o encontrada
	//!-----------------------------------------------------------------
	function &getField($fieldName) {
		$index = (TypeUtils::isArray($this->childMap['FIELD']) ? array_search($fieldName, $this->childMap['FIELD']) : FALSE);
		if (!TypeUtils::isFalse($index)) {
			return $this->getChild($index);
		}
		return NULL;
	}
	
	//!-----------------------------------------------------------------
	// @function	FormSection::&getSubSection
	// @desc		Busca uma subse��o a partir de seu ID dentre as subse��es filhas da se��o
	// @access		public
	// @param		sectionId string	ID da se��o
	// @return		mixed	Objeto correspondente � subse��o ou NULL se n�o encontrada
	//!-----------------------------------------------------------------
	function &getSubSection($sectionId) {
		$index = (TypeUtils::isArray($this->childMap['SECTION']) ? array_search($sectionId, $this->childMap['SECTION']) : FALSE);
		if (!TypeUtils::isFalse($index)) {
			return $this->getChild($index);
		}
		return NULL;
	}	
	
	//!-----------------------------------------------------------------
	// @function	FormSection::addChild
	// @desc		Adiciona uma subse��o, campo, bot�o ou grupo de bot�es � se��o atual
	// @access		public
	// @param		&object mixed	Elemento interno � se��o a ser inserido
	// @return		void
	//!-----------------------------------------------------------------
	function addChild(&$object) {
		$currentIndex = sizeOf($this->children);
		// subse��o
		if (TypeUtils::isObject($object) && TypeUtils::isInstanceOf($object, 'formsection')) {
			$newChild['object'] =& $object;
			$newChild['type'] = 'SECTION';
			$this->childMap['SECTION'][$currentIndex] = $object->getId();
		}
		// bot�o
		else if (TypeUtils::isObject($object) && TypeUtils::isInstanceOf($object, 'formbutton')) {
			$newChild['object'] =& $object;
			$newChild['type'] = 'BUTTON';
			$this->childMap['BUTTON'][$currentIndex] = $object->getName();
		}
		// campo
		else if (TypeUtils::isObject($object) && TypeUtils::isInstanceOf($object, 'formfield')) {
			$newChild['object'] =& $object;
			$newChild['type'] = 'FIELD';			
			$this->childMap['FIELD'][$currentIndex] = $object->getName();
		}
		// grupo de bot�es
		else if (TypeUtils::isArray($object) && TypeUtils::isObject($object[0]) && TypeUtils::isInstanceOf($object[0], 'formbutton')) {
			$newChild['object'] =& $object;
			$newChild['type'] = 'BUTTONGROUP';
			for ($i=0; $i<sizeOf($object); $i++) {
				$this->childMap['BUTTON'][$currentIndex] = $object[$i]->getName();
			}
		}
		// tipo inv�lido
		else {
			return FALSE;
		}
		// adiciona o elemento
		$this->children[] =& $newChild;
		return TRUE;
	}
	
	//!-----------------------------------------------------------------
	// @function	FormSection::_parseSection
	// @desc		Captura os atributos poss�veis para uma se��o de formul�rio
	// @access		private
	// @return		void
	//!-----------------------------------------------------------------
	function _parseSection() {
		// nome da se��o
		if ($name = $this->getAttribute('NAME'))
			$this->name = Form::resolveI18nEntry($name);
		else
			$this->name = $this->_Form->formName . ' - Section ' . sizeOf($this->_Form->sections);
		// id da se��o
		if ($id = $this->getAttribute('ID'))
			$this->id = $id;			
		else
			$this->id = $this->_Form->formName . '_section' . sizeOf($this->_Form->sections);
		// se��o condicional e fun��o de avalia��o
		if ($this->getAttribute('CONDITION') == 'T') {
			$this->condition = TRUE;
			$negate = FALSE;
			$evalFunc = (isset($this->attrs['EVALFUNCTION']) ? $this->attrs['EVALFUNCTION'] : $this->id . '_evaluate');
			if (StringUtils::startsWith($evalFunc, '!', TRUE, TRUE)) {
				$evalFunc = substr($evalFunc, 1);
				$negate = TRUE;
			}
			$Callback =& Callback::getInstance();
			$Callback->setThrowErrors(FALSE);
			$Callback->setFunction($evalFunc);
			if (!$Callback->isValid())
				PHP2Go::raiseError(PHP2Go::getLangVal('ERR_FORM_SECTION_EVALFUNC', array($this->id, $evalFunc)), E_USER_ERROR, __FILE__, __LINE__);				
			else
				$this->show = ($negate ? !$Callback->invoke($this) : $Callback->invoke($this));
		} else {
			$this->attrs['CONDITION'] = 'F';
			$this->show = TRUE;
		}
		// define as configura��es da marca de campos obrigat�rios
		$parentAttrs = (TypeUtils::isInstanceOf($this->_Parent, 'xmlnode') && $this->_Parent->getTag() == 'SECTION' ? $this->_Parent->getAttributes() : array());		
//		if (isset($this->attrs['REQUIRED_FLAG']))
			$flag = Form::resolveBooleanChoice($this->attrs['REQUIRED_FLAG']);
//		else
//			$flag = NULL;
		if (TypeUtils::isNull($flag, TRUE)) {
			$flag = Form::resolveBooleanChoice($parentAttrs['REQUIRED_FLAG']);
			if (TypeUtils::isNull($flag, TRUE)) {
				$flag = $this->_Form->requiredMark;
			}
		}				
		$this->attrs['REQUIRED_FLAG'] = $flag;
		$this->attrs['REQUIRED_COLOR'] = (isset($this->attrs['REQUIRED_COLOR']) ? $this->attrs['REQUIRED_COLOR'] : (!empty($parentAttrs) && isset($parentAttrs['REQUIRED_COLOR']) ? $parentAttrs['REQUIRED_COLOR'] : $this->_Form->requiredColor));
		$this->attrs['REQUIRED_TEXT'] = (isset($this->attrs['REQUIRED_TEXT']) ? $this->attrs['REQUIRED_TEXT'] : (!empty($parentAttrs) && isset($parentAttrs['REQUIRED_TEXT']) ? $parentAttrs['REQUIRED_TEXT'] : $this->_Form->requiredText));
	}
} 
?>