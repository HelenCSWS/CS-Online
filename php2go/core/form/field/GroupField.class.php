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
// @desc		A classe GroupField serve de base para a constru��o
//				de um grupo de campos RADIO ou um grupo de campos 
//				CHECKBOX com op��es est�ticas
// @package		php2go.form.field
// @extends		FormField
// @uses		TypeUtils
// @author		Marcos Pont
// @version		$Revision: 1.13 $		
//!-----------------------------------------------------------------
class GroupField extends FormField
{
	var $optionCount = 0;				// @var optionCount int				"0" Total de op��es do grupo
	var $optionAttributes = array();	// @var optionAttributes array		"array()" Vetor de atributos das op��es
	var $optionListeners = array();		// @var optionListeners array		"array()" Vetor de tratadores de evento por op��o do grupo

	//!-----------------------------------------------------------------
	// @function	GroupField::GroupField
	// @desc		Construtor da classe
	// @access		public
	// @param		&Form Form object	Formul�rio no qual o campo � inserido
	// @param		child bool			"FALSE" Se for TRUE, indica que o campo � membro de um campo composto	
	//!-----------------------------------------------------------------
	function GroupField(&$Form, $child=FALSE) {
		parent::FormField($Form, $child);
	}
	
	//!-----------------------------------------------------------------
	// @function	GroupField::getOptions
	// @desc		Retorna o vetor de op��es inseridas no grupo
	// @access		public
	// @return		array
	//!-----------------------------------------------------------------
	function getOptions() {
		return $this->optionAttributes;
	}
	
	//!-----------------------------------------------------------------
	// @function	GroupField::getOptionCount
	// @desc		Busca o n�mero de op��es inseridas
	// @access		public
	// @return		int	N�mero de itens
	//!-----------------------------------------------------------------
	function getOptionCount() {
		return $this->optionCount;
	}	
	
	//!-----------------------------------------------------------------
	// @function	GroupField::setCols
	// @desc		Seta o n�mero de colunas da tabela que cont�m os campos,
	//				definindo assim quantos elementos devem ser exibidos por linha
	// @access		public
	// @param		cols int	N�mero de colunas ou campos por linha
	// @return		void
	//!-----------------------------------------------------------------
	function setCols($cols) {
		$this->attributes['COLS'] = max(1, $cols);
	}
	
	//!-----------------------------------------------------------------
	// @function	GroupField::setTableWidth
	// @desc		Seta o tamanho (valor para o atributo WIDTH) da tabela
	//				constru�da para o grupo de campos
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
	// @desc		Sobrescreve a implementa��o do m�todo na classe FormField
	//				para adicionar a possibilidade de inclus�o de listeners individuais
	//				por op��o do grupo (um elemento radio, ou um elemento checkbox)
	// @access		public
	// @param		Listener FormEventListener object	Tratador de eventos
	// @param		index int	"NULL" �ndice do elemento do grupo ao qual o tratador deve ser associado
	// @return		void
	// @note		Se o par�metro $index for omitido, o listener ser� inclu�do para todas as op��es de grupo
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
	// @desc		Adiciona uma nova op��o ao conjunto de OPTIONS do grupo
	// @access		public
	// @param		value mixed		Valor para a op��o
	// @param		caption string	Caption da op��o
	// @param		alt string		"" Texto alternativo
	// @param		disabled bool	"FALSE" Indica se a op��o deve estar desabilitado
	// @param		index int		"NULL" �ndice onde a op��o deve ser inserida
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
	// @desc		Remove uma op��o do grupo a partir de seu �ndice
	// @access		public
	// @param		index int	�ndice a ser removido
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
	// @desc		Modifica o estado de uma das op��es do grupo de campos (habilitado, desabilitado)
	// @access		public
	// @param		index int		�ndice a ser alterado
	// @param		setting bool	"TRUE" Estado a ser aplicado � op��o
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
	// @desc		M�todo respons�vel por processar atributos e nodos filhos
	//				provenientes da especifica��o XML do campo
	// @access		protected
	// @param		attrs array		Atributos do nodo
	// @param		children array	Vetor de nodos filhos
	// @return		void
	//!-----------------------------------------------------------------
	function onLoadNode($attrs, $children) {
		parent::onLoadNode($attrs, $children);
		// n�mero de colunas
		$this->setCols(@$attrs['COLS']);
		// largura da tabela
		$this->setTableWidth(@$attrs['TABLEWIDTH']);
		// op��es
		if (isset($children['OPTION'])) {
			$options = TypeUtils::toArray($children['OPTION']);
			for ($i=0,$s=sizeof($options); $i<$s; $i++) {
				$this->addOption($options[$i]->getAttribute('VALUE'), $options[$i]->getAttribute('CAPTION'), $options[$i]->getAttribute('ALT'), ($options[$i]->getAttribute('DISABLED') == 'T'));
				// listeners individuais de cada op��o
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
	//				antes da gera��o do c�digo HTML final
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function onPreRender() {
		$this->focusName = "{$this->name}_0";
		parent::onPreRender();
	}
	
	//!-----------------------------------------------------------------
	// @function	GroupField::renderListeners
	// @desc		A classe GroupField sobrescreve a implementa��o do m�todo renderListeners
	//				para que os listeners gerais para todas as op��es e os listeners individuais por
	//				op��o possam ser agrupados na montagem da defini��o dos eventos das op��es do campo
	// @access		protected
	// @return		void
	//!-----------------------------------------------------------------
	function renderListeners() {
		for ($i=0, $s=$this->optionCount; $i<$s; $i++) {				
			$script = '';
			$events = array();
			// listeners globais para todas as op��es do grupo
			reset($this->listeners);
			foreach ($this->listeners as $globalListener) {
				$eventName = $globalListener->eventName;
				if (!isset($events[$eventName]))
					$events[$eventName] = array();
				$events[$eventName][] = $globalListener->getScriptCode($i);
			}
			// listeners individuais por op��o
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