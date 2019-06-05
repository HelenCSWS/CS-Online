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
// @desc 		Armazena informações sobre uma seção de formulário, que
// 				consiste em um grupo de campos agrupados em termos de
// 				disposição de interface
// @package		php2go.form
// @extends 	PHP2Go
// @author 		Marcos Pont
// @version		$Revision: 1.17 $ 
//!-----------------------------------------------------------------
class FormSection extends PHP2Go
{
	var $name;					// @var name string							Nome da seção
	var $id;					// @var id string							ID da seção
	var $attrs;					// @var attrs array							Atributos da seção
	var $condition;				// @var condition bool						Indica a geração da seção no formulário é condicional
	var $evaluateFunc;			// @var evaluateFunc string					Nome da função de avaliação da geração ou não da seção no formulário
	var $show = TRUE;			// @var show bool							"TRUE" Indica se a seção deve ou não ser exibida
	var $children;				// @var children array						Elementos subordinados à seção (subseções, campos, botões)	
	var $childMap;				// @var childMap array						Armazena os filhos de diferentes tipos em áreas separadas, para facilitar a busca de um campo ou botão para alteração
	var $_Form = NULL;			// @var _Form Form object					"NULL" Formulário no qual a seção está incluída	
	var $_Parent = NULL;		// @var _Parent XmlNode object				"NULL" Elemento ao qual a seção é subordinada no formulário (o FORM em si ou uma outra SECTION)
	 
	//!-----------------------------------------------------------------
	// @function 	FormSection::FormSection
	// @desc 		Inicializa a seção com os atributos lidos a partir
	// 				da especificação XML do formulário
	// @access 		public 
	// @param 		xmlNode XmlNode object	Objeto XmlNode que contém os dados da seção
	// @param 		&Form Form object		Objeto Form ao qual a seção pertence
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
	// @desc 		Consulta o nome da seção atual
	// @access 		public 
	// @return 		string Nome da seção
	//!-----------------------------------------------------------------
	function getName() {
		return $this->name;
	}
	
	//!-----------------------------------------------------------------
	// @function	FormSection::getId
	// @desc		Busca o ID definido para a seção
	//!-----------------------------------------------------------------
	function getId() {
		return $this->id;
	}
	
	//!-----------------------------------------------------------------
	// @function 	FormSection::getAttribute
	// @desc 		Busca o valor de um atributo da seção
	// @access 		public 
	// @param 		attrName string	Nome do atributo
	// @return	 	mixed Valor do atributo ou NULL se ele não for encontrado
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
	// @desc		Verifica se a seção possui exibição condicional
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function isConditional() {
		return $this->condition;
	}
	
	//!-----------------------------------------------------------------
	// @function	FormSection::hasChildren
	// @desc		Verifica se a seção possui descendentes
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function hasChildren() {
		return (!empty($this->children));
	}
	
	//!-----------------------------------------------------------------
	// @function	FormSection::getChildren
	// @desc		Busca o vetor de elementos subordinados à seção atual
	// @access		public
	// @return		array
	//!-----------------------------------------------------------------
	function getChildren() {
		return $this->children;
	}
	
	//!-----------------------------------------------------------------
	// @function	FormSection::&getChild
	// @desc		Busca um elemento interno da seção, a partir de seu índice
	// @access		public
	// @param		index int	Índice do elemento
	// @return		mixed	O objeto alocado no índice fornecido ou NULL se o índice for inválido
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
	// @desc		Consulta o tipo de um determinado elemento da seção
	// @access		public
	// @param		index int	Índice do elemento
	// @return		string	Tipo do elemento
	// @note		Retorna NULL se o índice for inválido
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
	// @desc		Busca um campo a partir de seu nome dentre os campos filhos da seção
	// @access		public
	// @param		fieldName string	Nome do campo
	// @return		mixed	Objeto correspondente ao campo ou NULL se não encontrada
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
	// @desc		Busca uma subseção a partir de seu ID dentre as subseções filhas da seção
	// @access		public
	// @param		sectionId string	ID da seção
	// @return		mixed	Objeto correspondente à subseção ou NULL se não encontrada
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
	// @desc		Adiciona uma subseção, campo, botão ou grupo de botões à seção atual
	// @access		public
	// @param		&object mixed	Elemento interno à seção a ser inserido
	// @return		void
	//!-----------------------------------------------------------------
	function addChild(&$object) {
		$currentIndex = sizeOf($this->children);
		// subseção
		if (TypeUtils::isObject($object) && TypeUtils::isInstanceOf($object, 'formsection')) {
			$newChild['object'] =& $object;
			$newChild['type'] = 'SECTION';
			$this->childMap['SECTION'][$currentIndex] = $object->getId();
		}
		// botão
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
		// grupo de botões
		else if (TypeUtils::isArray($object) && TypeUtils::isObject($object[0]) && TypeUtils::isInstanceOf($object[0], 'formbutton')) {
			$newChild['object'] =& $object;
			$newChild['type'] = 'BUTTONGROUP';
			for ($i=0; $i<sizeOf($object); $i++) {
				$this->childMap['BUTTON'][$currentIndex] = $object[$i]->getName();
			}
		}
		// tipo inválido
		else {
			return FALSE;
		}
		// adiciona o elemento
		$this->children[] =& $newChild;
		return TRUE;
	}
	
	//!-----------------------------------------------------------------
	// @function	FormSection::_parseSection
	// @desc		Captura os atributos possíveis para uma seção de formulário
	// @access		private
	// @return		void
	//!-----------------------------------------------------------------
	function _parseSection() {
		// nome da seção
		if ($name = $this->getAttribute('NAME'))
			$this->name = Form::resolveI18nEntry($name);
		else
			$this->name = $this->_Form->formName . ' - Section ' . sizeOf($this->_Form->sections);
		// id da seção
		if ($id = $this->getAttribute('ID'))
			$this->id = $id;			
		else
			$this->id = $this->_Form->formName . '_section' . sizeOf($this->_Form->sections);
		// seção condicional e função de avaliação
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
		// define as configurações da marca de campos obrigatórios
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