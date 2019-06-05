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
// $Header: /www/cvsroot/php2go/core/form/field/GroupField.class.php,v 1.13 2005/08/30 14:32:35 mpont Exp $
// $Date: 2005/08/30 14:32:35 $

//------------------------------------------------------------------
import('php2go.form.field.FormField');
//------------------------------------------------------------------

//!-----------------------------------------------------------------
// @class		GroupField
// @desc		A classe GroupField serve de base para a construção
//				de um grupo de campos RADIO ou um grupo de campos 
//				CHECKBOX com opções estáticas
// @package		php2go.form.field
// @extends		FormField
// @uses		TypeUtils
// @author		Marcos Pont
// @version		$Revision: 1.13 $		
//!-----------------------------------------------------------------
class GroupField extends FormField
{
	var $optionCount = 0;				// @var optionCount int				"0" Total de opções do grupo
	var $optionAttributes = array();	// @var optionAttributes array		"array()" Vetor de atributos das opções
	var $optionListeners = array();		// @var optionListeners array		"array()" Vetor de tratadores de evento por opção do grupo

	//!-----------------------------------------------------------------
	// @function	GroupField::GroupField
	// @desc		Construtor da classe
	// @access		public
	// @param		&Form Form object	Formulário no qual o campo é inserido
	// @param		child bool			"FALSE" Se for TRUE, indica que o campo é membro de um campo composto	
	//!-----------------------------------------------------------------
	function GroupField(&$Form, $child=FALSE) {
		parent::FormField($Form, $child);
	}
	
	//!-----------------------------------------------------------------
	// @function	GroupField::getOptions
	// @desc		Retorna o vetor de opções inseridas no grupo
	// @access		public
	// @return		array
	//!-----------------------------------------------------------------
	function getOptions() {
		return $this->optionAttributes;
	}
	
	//!-----------------------------------------------------------------
	// @function	GroupField::getOptionCount
	// @desc		Busca o número de opções inseridas
	// @access		public
	// @return		int	Número de itens
	//!-----------------------------------------------------------------
	function getOptionCount() {
		return $this->optionCount;
	}	
	
	//!-----------------------------------------------------------------
	// @function	GroupField::setCols
	// @desc		Seta o número de colunas da tabela que contém os campos,
	//				definindo assim quantos elementos devem ser exibidos por linha
	// @access		public
	// @param		cols int	Número de colunas ou campos por linha
	// @return		void
	//!-----------------------------------------------------------------
	function setCols($cols) {
		$this->attributes['COLS'] = max(1, $cols);
	}
	
	//!-----------------------------------------------------------------
	// @function	GroupField::setTableWidth
	// @desc		Seta o tamanho (valor para o atributo WIDTH) da tabela
	//				construída para o grupo de campos
	// @access		public
	// @param		tableWidth mixed	Tamanho da tabela
	// @return		void
	//!-----------------------------------------------------------------
	function setTableWidth($tableWidth) {
		if ($tableWidth)
			$this->attributes['TABLEWIDTH'] = " WIDTH=\"" . $tableWidth . "\"";
		else			
			$this->attributes['TABLEWIDTH'] = "";
	}
	
	//!-----------------------------------------------------------------
	// @function	GroupField::addEventListener
	// @desc		Sobrescreve a implementação do método na classe FormField
	//				para adicionar a possibilidade de inclusão de listeners individuais
	//				por opção do grupo (um elemento radio, ou um elemento checkbox)
	// @access		public
	// @param		Listener FormEventListener object	Tratador de eventos
	// @param		index int	"NULL" Índice do elemento do grupo ao qual o tratador deve ser associado
	// @return		void
	// @note		Se o parâmetro $index for omitido, o listener será incluído para todas as opções de grupo
	//!-----------------------------------------------------------------
	function addEventListener($Listener, $index=NULL) {
		if (TypeUtils::isNull($index, TRUE)) {
			parent::addEventListener($Listener);
		} elseif ($index < $this->optionCount && $index >= 0) {
			$Listener->setOwner($this, $index);
			if ($Listener->isValid())
				$this->optionListeners[$index][] =& $Listener;
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	GroupField::addOption
	// @desc		Adiciona uma nova opção ao conjunto de OPTIONS do grupo
	// @access		public
	// @param		value mixed		Valor para a opção
	// @param		caption string	Caption da opção
	// @param		alt string		"" Texto alternativo
	// @param		disabled bool	"FALSE" Indica se a opção deve estar desabilitado
	// @param		index int		"NULL" Índice onde a opção deve ser inserida
	// @return		bool
	//!-----------------------------------------------------------------
	function addOption($value, $caption, $alt='', $disabled=FALSE, $index=NULL) {
		$currentCount = $this->optionCount;
		if ($index <= $currentCount && $index >= 0) {
			$newOption = array();
			$value = trim(TypeUtils::parseString($value));
			if ($value == '')
				PHP2Go::raiseError(PHP2Go::getLangVal('ERR_MISSING_OPTION_VALUE', array($index, $this->name)), E_USER_ERROR, __FILE__, __LINE__);
			else
				$newOption['VALUE'] = $value;
			$caption = trim($caption);
			if ($caption == '')
				$newOption['CAPTION'] = $newOption['VALUE'];
			else
				$newOption['CAPTION'] = $caption;
			$newOption['ALT'] = trim($alt);
			if ($this->_Form->readonly || TypeUtils::isTrue($disabled))
				$newOption['DISABLED'] = " DISABLED";
			else
				$newOption['DISABLED'] = (isset($this->attributes['DISABLED']) ? $this->attributes['DISABLED'] : '');
			if ($index == $currentCount || !TypeUtils::isInteger($index)) {
				$this->optionAttributes[$currentCount] = $newOption;
				$this->optionListeners[$currentCount] = array();
			} else {
				for ($i=$currentCount; $i>$index; $i--) {
					$this->optionAttributes[$i] = $this->optionAttributes[$i-1];
					$this->optionListeners[$i] = $this->optionListeners[$i-1];
				}
				$this->optionAttributes[$index] = $newOption;
				$this->optionListeners[$index] = array();
			}
			$this->optionCount++;
			return TRUE;
		}
		return FALSE;
	}	

	//!-----------------------------------------------------------------
	// @function	GroupField::removeOption
	// @desc		Remove uma opção do grupo a partir de seu índice
	// @access		public
	// @param		index int	Índice a ser removido
	// @return		bool
	//!-----------------------------------------------------------------
	function removeOption($index) {
		$currentCount = $this->getOptionCount();
		if ($currentCount == 1 || !TypeUtils::isInteger($index) || $index >= $currentCount || $index < 0) {
			return FALSE;
		} else {
			for ($i=$index; $i<($currentCount-1); $i++) {
				$this->optionAttributes[$i] = $this->optionAttributes[$i+1];
				$this->optionListeners[$i] = $this->optionListeners[$i+1];
			}
			unset($this->optionAttributes[$currentCount-1]);
			unset($this->optionListeners[$currentCount-1]);
			$this->optionCount--;
			return TRUE;
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	GroupField::setDisabled
	// @desc		Modifica o estado de uma das opções do grupo de campos (habilitado, desabilitado)
	// @access		public
	// @param		index int		Índice a ser alterado
	// @param		setting bool	"TRUE" Estado a ser aplicado à opção
	// @return		TRUE
	//!-----------------------------------------------------------------
	function setDisabled($index, $setting=TRUE) {
		$currentCount = $this->getOptionCount();
		if ($currentCount == 1 || !TypeUtils::isInteger($index) || $index >= $currentCount || $index < 0) {
			return FALSE;
		} else {
			if (TypeUtils::isTrue($setting))
				$this->optionAttributes[$index]['DISABLED'] = " DISABLED";
			else
				$this->optionAttributes[$index]['DISABLED'] = "";
			return TRUE;
		}		
	}
	
	//!-----------------------------------------------------------------
	// @function	GroupField::onLoadNode
	// @desc		Método responsável por processar atributos e nodos filhos
	//				provenientes da especificação XML do campo
	// @access		protected
	// @param		attrs array		Atributos do nodo
	// @param		children array	Vetor de nodos filhos
	// @return		void
	//!-----------------------------------------------------------------
	function onLoadNode($attrs, $children) {
		parent::onLoadNode($attrs, $children);
		// número de colunas
		$this->setCols(@$attrs['COLS']);
		// largura da tabela
		$this->setTableWidth(@$attrs['TABLEWIDTH']);
		// opções
		if (isset($children['OPTION'])) {
			$options = TypeUtils::toArray($children['OPTION']);
			for ($i=0,$s=sizeof($options); $i<$s; $i++) {
				$this->addOption($options[$i]->getAttribute('VALUE'), $options[$i]->getAttribute('CAPTION'), $options[$i]->getAttribute('ALT'), ($options[$i]->getAttribute('DISABLED') == 'T'));
				// listeners individuais de cada opção
				$optChildren = $options[$i]->getChildrenTagsArray();
				if (isset($optChildren['LISTENER'])) {
					$listener = TypeUtils::toArray($optChildren['LISTENER']);
					foreach ($listener as $listenerNode)
						$this->addEventListener(FormEventListener::fromNode($listenerNode), $i);
				}
			}			
		} else {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_MISSING_GROUPFIELD_CHILDREN', $this->name), E_USER_ERROR, __FILE__, __LINE__);
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	GroupField::onPreRender
	// @desc		Define o nome de foco para o campo de grupo 
	//				antes da geração do código HTML final
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function onPreRender() {
		$this->focusName = "{$this->name}_0";
		parent::onPreRender();
	}
	
	//!-----------------------------------------------------------------
	// @function	GroupField::renderListeners
	// @desc		A classe GroupField sobrescreve a implementação do método renderListeners
	//				para que os listeners gerais para todas as opções e os listeners individuais por
	//				opção possam ser agrupados na montagem da definição dos eventos das opções do campo
	// @access		protected
	// @return		void
	//!-----------------------------------------------------------------
	function renderListeners() {
		for ($i=0, $s=$this->optionCount; $i<$s; $i++) {				
			$script = '';
			$events = array();
			// listeners globais para todas as opções do grupo
			reset($this->listeners);
			foreach ($this->listeners as $globalListener) {
				$eventName = $globalListener->eventName;
				if (!isset($events[$eventName]))
					$events[$eventName] = array();
				$events[$eventName][] = $globalListener->getScriptCode($i);
			}
			// listeners individuais por opção
			reset($this->optionListeners[$i]);
			foreach ($this->optionListeners[$i] as $optionListener) {
				$eventName = $optionListener->eventName;
				if (!isset($events[$eventName]))
					$events[$eventName] = array();
				$events[$eventName][] = $optionListener->getScriptCode();
			}
			foreach ($events as $event => $action) {
				$action = implode(';', $action);
				$script .= " {$event}=\"" . str_replace('\"', '\'', $action) . ";\"";
			}
			$this->optionAttributes[$i]['SCRIPT'] = $script;
		}
	}	
}
?>