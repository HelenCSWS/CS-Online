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
// $Header: /www/cvsroot/php2go/core/base/AbstractNode.class.php,v 1.17 2005/08/30 14:06:15 mpont Exp $
// $Date: 2005/08/30 14:06:15 $

//!-----------------------------------------------------------------
// @class		AbstractNode
// @desc		Classe que implementa métodos de construção e manipulação
//				de nodos, criando, destruindo e gerenciando seus atributos 
//				e nodos filhos. Baseia-se nos métodos implementados pelo
//				modelo DOM Level 2
// @package		php2go.base
// @extends		PHP2Go
// @version		$Revision: 1.17 $
// @author		Marcos Pont
//!-----------------------------------------------------------------
class AbstractNode extends PHP2Go
{
	var $id;						// @var id string							ID único do nodo
	var $name;						// @var name string							Nome do nodo, com semântica dependente da utilização nas classes extendidas
	var $attrs;						// @var attrs array							Vetor de atributos do nodo
	var $children;					// @var children array						Vetor de filhos do nodo
	var $hashIndex;					// @var hashIndex array						Indexa os IDs de nodos para facilitar a busca
	var $childrenCount = 0;			// @var childrenCount int					"0" Número de filhos do nodo
	var $parentNode = NULL;			// @var parentNode AbstractNode object		"NULL" Nodo pai
	var $firstChild = NULL;			// @var firstChild AbstractNode object		"NULL" Primeiro filho do nodo
	var $lastChild = NULL;			// @var lastChild AbstractNode object		"NULL" Último filho do nodo
	var $previousSibling = NULL;	// @var previousSibling AbstractNode object	"NULL" Nodo anterior na cadeia de nodos do mesmo nível
	var $nextSibling = NULL;		// @var nextSibling AbstractNode object		"NULL" Próximo nodo na cadeia de nodos do mesmo nível
	
	//!-----------------------------------------------------------------
	// @function	AbstractNode::AbstractNode
	// @desc		Construtor do objeto AbstractNode
	// @access		public
	// @param		nodeName string		Nome para o nodo
	// @param		nodeAttrs array		"array()" Atributos do nodo
	// @param		nodeChildren array	"NULL" Vetor de filhos do nodo
	//!-----------------------------------------------------------------
	function AbstractNode($nodeName, $nodeAttrs = array(), $nodeChildren = NULL) {
		parent::PHP2Go();
		$this->id = PHP2Go::generateUniqueId('Node');
		$this->name = $nodeName;
		$this->attrs = $nodeAttrs;
		if ($nodeChildren) {
			foreach ($nodeChildren as $Child)
				$this->addChild($Child);
		} else {
			$this->children = array();
			$this->hashIndex = array();
		}
		$this->childrenCount = TypeUtils::isArray($nodeChildren) ? sizeOf($nodeChildren) : 0;
	}
	
	//!-----------------------------------------------------------------
	// @function	AbstractNode::getId
	// @desc		Busca o ID do nodo
	// @access		public
	// @return		string ID do nodo
	//!-----------------------------------------------------------------
	function getId() {
		return $this->id;
	}
	
	//!-----------------------------------------------------------------
	// @function	AbstractNode::getName
	// @desc		Busca o nome do nodo
	// @access		public
	// @return		string Nome do nodo
	//!-----------------------------------------------------------------
	function getName() {
		return $this->name;
	}
	
	//!-----------------------------------------------------------------
	// @function	AbstractNode::setName
	// @desc		Atribui um novo valor ao nome do nodo atual
	// @access		public
	// @param		newName string	Nome nome para o nodo
	// @return		void
	//!-----------------------------------------------------------------
	function setName($newName) {
		$this->name = $newName;
	}
	
	//!-----------------------------------------------------------------
	// @function	AbstractNode::hasAttributes
	// @desc		Verifica se o nodo possui atributos
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function hasAttributes() {
		return (TypeUtils::isArray($this->attrs) && !empty($this->attrs));
	}
	
	//!-----------------------------------------------------------------
	// @function	AbstractNode::hasAttribute
	// @desc		Verifica se o nodo possui um determinado atributo	
	// @access		public
	// @param		name string		Nome do atributo
	// @return		bool
	//!-----------------------------------------------------------------
	function hasAttribute($name) {
		return (TypeUtils::isArray($this->attrs) && array_key_exists($name, $this->attrs));
	}
	
	//!-----------------------------------------------------------------
	// @function	AbstractNode::getAttributes
	// @desc		Retorna o vetor de atributos do nodo XML
	// @access		public
	// @return		array Vetor de atributos do nodo
	//!-----------------------------------------------------------------
	function getAttributes() {
		return $this->attrs;
	}
	
	//!-----------------------------------------------------------------
	// @function	AbstractNode::getAttribute
	// @desc		Busca o valor de um atributo do nodo XML
	// @access		public
	// @param		attribute string	Nome do atributo
	// @return		string Valor do atributo ou FALSE se ele não existir
	//!-----------------------------------------------------------------
	function getAttribute($attribute) {
		if ($this->hasAttribute($attribute))
			return $this->attrs[$attribute];
		else
			return FALSE;
	}
	
	//!-----------------------------------------------------------------
	// @function	AbstractNode::addAttributes
	// @desc		Adiciona um conjunto de atributos ao nodo
	// @access		public
	// @param		attributes array	Vetor de atributos
	// @return		void
	//!-----------------------------------------------------------------
	function addAttributes($attributes) {
		if (TypeUtils::isHashArray($attributes))
			$this->attrs = array_merge($this->attrs, $attributes);
	}
	
	//!-----------------------------------------------------------------
	// @function	AbstractNode::setAttribute
	// @desc		Configura o valor de um atributo do nodo
	// @access		public
	// @param		attribute string	Nome do atributo
	// @param		value mixed			Valor para o atributo
	// @return		void	
	//!-----------------------------------------------------------------
	function setAttribute($attribute, $value) {
		$this->attrs[$attribute] = $value;
	}
	
	//!-----------------------------------------------------------------
	// @function	AbstractNode::removeAttribute
	// @desc		Remove um atributo do nodo
	// @access		public
	// @param		attribute string	Nome do atributo
	// @return		void
	//!-----------------------------------------------------------------
	function removeAttribute($attribute) {		
		unset($this->attrs[$attribute]);
	}

	//!-----------------------------------------------------------------
	// @function	AbstractNode::&getParentNode
	// @desc		Retorna o nodo pai do nodo atual
	// @access		public
	// @return		AbstractNode object
	//!-----------------------------------------------------------------
	function &getParentNode() {
		return $this->parentNode;
	}
	
	//!-----------------------------------------------------------------
	// @function	AbstractNode::setParentNode
	// @desc		Define o nodo superior ao nodo atual
	// @access		public
	// @param		&Node AbstractNode object	Nodo superior
	// @return		void
	//!-----------------------------------------------------------------
	function setParentNode(&$Node) {
		if (TypeUtils::isInstanceOf($Node, 'abstractnode'))
			$this->parentNode =& $Node;
	}
	
	//!-----------------------------------------------------------------
	// @function	AbstractNode::hasChildren
	// @desc		Verifica se o nodo XML possui filhos
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function hasChildren() {
		return ($this->childrenCount > 0);
	}

	//!-----------------------------------------------------------------
	// @function	AbstractNode::getChildrenCount
	// @desc 		Retorna o número de filhos do nodo XML
	// @access 		public
	// @return		int Número de filhos do nodo
	//!-----------------------------------------------------------------
	function getChildrenCount() {
		return $this->childrenCount;
	}
	
	//!-----------------------------------------------------------------
	// @function	AbstractNode::&getChildNodes
	// @desc		Retorna o vetor de filhos do nodo
	// @access		public
	// @return		array Vetor de nodos, que retornará na forma de um array vazio 
	//				caso o nodo não possua filhos
	//!-----------------------------------------------------------------
	function &getChildNodes() {
		return $this->childrenCount > 0 ? $this->children : array();
	}
	
	//!-----------------------------------------------------------------
	// @function	AbstractNode::&getFirstChild
	// @desc		Busca o primeiro filho do nodo
	// @access		public
	// @return		AbstractNode object Primeiro filho do nodo, ou NULL se ele não existir
	//!-----------------------------------------------------------------
	function &getFirstChild() {
		if ($this->childrenCount > 0) {
			return $this->firstChild;
		} else {
			return NULL;
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	AbstractNode::&getLastChild
	// @desc		Busca o útlimo filho do nodo
	// @access		public
	// @return		AbstractNode object Último filho do nodo. Se este elemento não existir, retorna NULL
	//!-----------------------------------------------------------------
	function &getLastChild() {
		if ($this->childrenCount > 0) {
			return $this->lastChild;
		} else {
			return NULL;
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	AbstractNode::&getPreviousSibling
	// @desc		Retorna o nodo anterior na cadeia de nodos no mesmo nível
	// @access		public
	// @return		AbstractNode object	Nodo anterior
	//!-----------------------------------------------------------------
	function &getPreviousSibling() {
		return $this->previousSibling;
	}
	
	//!-----------------------------------------------------------------
	// @function	AbstractNode::&getNextSibling
	// @desc		Retorna o próximo nodo na cadeia de nodos no mesmo nível
	// @access		public
	// @return		AbstractNode object	Próximo nodo
	//!-----------------------------------------------------------------
	function &getNextSibling() {
		return $this->nextSibling;
	}

	//!-----------------------------------------------------------------
	// @function	AbstractNode::&getChild
	// @desc 		Retorna o filho de índice $index do nodo, se existir
	// @param 		index int			Índice do nodo buscado
	// @return	 	AbstractNode object Filho de índice $index ou FALSE se ele não existir
	//!-----------------------------------------------------------------
	function &getChild($index) {
		if (isset($this->children[$index])) {
			return $this->children[$index];
		} else {
			return FALSE;
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	AbstractNode::getNodeIndex
	// @desc		Procura por um determinado nodo nos filhos do nodo atual
	// @access		protected
	// @param		node AbstractNode object	Nodo buscado
	// @return		int
	//!-----------------------------------------------------------------
	function getNodeIndex($Node) {
		if (!$this->hasChildren() || !TypeUtils::isInstanceOf($Node, 'abstractnode')) {
			return -1;
		} else {
			$result = array_search($Node->getId(), $this->hashIndex);
			if (!TypeUtils::isFalse($result))
				return $result;
			else
				return -1;
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	AbstractNode::&addChild
	// @desc		Adiciona um filho ao nodo XML
	// @access		public
	// @param		childNode AbstractNode object	Objeto XmlNode a ser inserido
	// @return		AbstractNode object	Nodo inserido
	//!-----------------------------------------------------------------
	function &addChild($childNode) {
		if (TypeUtils::isInstanceOf($childNode, 'abstractnode')) {
			if (!$this->hasChildren()) {
				$this->children[0] =& $childNode;
				$this->childrenCount = 1;
				$this->firstChild =& $childNode;
				$childNode->previousSibling = NULL;
				$childNode->nextSibling = NULL;
				$this->hashIndex[0] = $childNode->getId();
			} else {
				$index = $this->getNodeIndex($childNode);
				if (!TypeUtils::isNull($index) && $index != -1)
					$this->removeChild($index);
				$this->children[$this->childrenCount] =& $childNode;
				$this->childrenCount++;
				$this->lastChild->nextSibling =& $childNode;
				$Child->previousSibling =& $this->lastChild;
				$this->hashIndex[$this->childrenCount] = $childNode->getId();
			}
			$this->lastChild =& $childNode;
			$childNode->nextSibling = NULL;
			$childNode->setParentNode($this);
			return $childNode;
		} else {
			return FALSE;
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	AbstractNode::addChildList
	// @desc		Adiciona uma lista de filhos ao nodo XML
	// @access		public
	// @return		void	
	// @note		Este método recebe N parâmetros, que são interpretados
	//				como N filhos a serem adicionados ao nodo atual
	//!-----------------------------------------------------------------
	function addChildList() {
		$args = func_get_args();		
		if (func_num_args() > 0) {
			foreach($args as $Child) {
				$this->addChild($Child);
			}
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	AbstractNode::removeChild
	// @desc		Remove um filho do nodo atual, através de seu índice
	// @access		public
	// @param		index int		Índice do nodo a ser removido
	// @return		bool
	//!-----------------------------------------------------------------
	function removeChild($index) {
		if (array_key_exists($index, $this->children)) {
			$OldChild =& $this->getChild($index);
			if ($OldChild->previousSibling != NULL && $OldChild->nextSibling != NULL) {
				$OldChild->previousSibling->nextSibling =& $OldChild->nextSibling;
				$OldChild->nextSibling->previousSibling =& $OldChild->previousSibling;
			} elseif ($OldChild->previousSibling == NULL && $OldChild->nextSibling != NULL) {
				$OldChild->nextSibling->previousSibling = NULL;
				$this->firstChild =& $OldChild->nextSibling;
			} elseif ($OldChild->previousSibling != NULL && $OldChild->nextSibling == NULL) {
				$OldChild->previousSibling->nextSibling = NULL;
				$this->lastChild =& $OldChild->previousSibling;
			} else {
				$this->firstChild = NULL;
				$this->lastChild = NULL;
			}			
			for ($i=$index; $i<($this->childrenCount-1); $i++) {
				$this->children[$i] = $this->children[$i+1];
				$this->hashIndex[$i] = $this->hashIndex[$i+1];
			}
			$this->childrenCount--;
			if ($this->childrenCount == 0) {
				$this->children = array();
				$this->hashIndex = array();
			}
			return TRUE;
		} else {
			return FALSE;
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	AbstractNode::cloneNode
	// @desc		Retorna um clone do nodo
	// @access		public
	// @param		deep bool		"TRUE" Se igual a TRUE, retorna os 
	//								filhos do nodo recursivamente. Do contrário,
	//								retorna apenas o nodo atual
	// @return		AbstractNode object Objeto que representa a cópia da instância atual
	//!-----------------------------------------------------------------
	function cloneNode($deep = TRUE) {
		$Clone =& $this->createClone();
		if ($deep) {
			for ($i=0; $i<$this->children; $i++)
				$Clone->addChild($this->children[$i]);
		}
		return $Clone;
	}	
	
	//!-----------------------------------------------------------------
	// @function	AbstractNode::&cloneNode
	// @desc		Constrói um clone do objeto atual
	// @access		public
	// @return		AbstractNode object
	//!-----------------------------------------------------------------
	function &createClone() {
		$Clone = new AbstractNode($this->name, $this->attrs, NULL);
		return $Clone;
	}	
}
?>