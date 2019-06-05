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
// $Header: /www/cvsroot/php2go/core/form/field/ComboField.class.php,v 1.19 2005/08/30 14:30:46 mpont Exp $
// $Date: 2005/08/30 14:30:46 $

//------------------------------------------------------------------
import('php2go.form.field.FormField');
//------------------------------------------------------------------

//!-----------------------------------------------------------------
// @class		ComboField
// @desc		A classe ComboField monta campos do tipo SELECT com
//				todas as op��es explicitadas na defini��o do arquivo XML
// @package		php2go.form.field
// @uses		TypeUtils
// @extends		FormField
// @author		Marcos Pont
// @version		$Revision: 1.19 $
//!-----------------------------------------------------------------
class ComboField extends FormField
{
	var $optionCount = 0;				// @var optionCount int				"0" Total de op��es do grupo radio
	var $optionAttributes = array();	// @var optionAttributes array		"array()" Vetor de atributos das op��es
	
	//!-----------------------------------------------------------------	
	// @function	ComboField::ComboField
	// @desc		Construtor da classe ComboField
	// @access		public
	// @param		&Form Form object	Formul�rio no qual o campo ser� inserido	
	// @param		child bool			"FALSE" Se for TRUE, indica que o campo � membro de um campo composto
	//!-----------------------------------------------------------------
	function ComboField(&$Form, $child=FALSE) {
		parent::FormField($Form, $child);
		$this->htmlType = 'SELECT';
		$this->searchDefaults['OPERATOR'] = 'EQ';
	}
	
	//!-----------------------------------------------------------------
	// @function	ComboField::getCode
	// @desc		Monta o c�digo HTML do campo
	// @access		public
	// @return		string C�digo HTML do campo
	//!-----------------------------------------------------------------
	function getCode() {
		parent::onPreRender();
		// c�digo do campo SELECT
		$this->htmlCode = sprintf("<SELECT ID=\"%s\" NAME=\"%s%s\" TITLE=\"%s\"%s%s%s%s%s%s%s%s%s>\n",
				$this->name, $this->name, $this->attributes['CLASPS'], $this->label, 
				$this->attributes['SCRIPT'], $this->attributes['TABINDEX'], $this->attributes['STYLE'], 
				$this->attributes['MULTIPLE'], $this->attributes['SIZE'], $this->attributes['WIDTH'], 
				$this->attributes['DISABLED'], $this->attributes['DATASRC'], $this->attributes['DATAFLD']
		);
		// primeira op��o
		if ($this->attributes['NOFIRST'] != 'T' && $this->attributes['SIZE'] == '')
			$this->htmlCode .= sprintf("<OPTION VALUE=\"\">%s</OPTION>\n", $this->attributes['FIRST']);
		// lista de op��es			
		$hasValue = (!empty($this->value));
		$arrayValue = (TypeUtils::isArray($this->value));		
		for ($i=0, $s=$this->optionCount; $i<$s; $i++) {
			$key = $this->optionAttributes[$i]['VALUE'];
			if ($hasValue) {
				if ($arrayValue)
					$optionSelected = in_array($key, $this->value) ? ' SELECTED' : '';
				else
					$optionSelected = !strcasecmp($key, $this->value) ? ' SELECTED' : '';
			} else {
				$optionSelected = '';
			}			
			$this->htmlCode .= sprintf("<OPTION VALUE=\"%s\"%s%s>%s</OPTION>\n", 
					$key, (!empty($this->optionAttributes[$i]['ALT']) ? " TITLE=\"{$this->optionAttributes[$i]['ALT']}\"" : ''),
					$optionSelected, $this->optionAttributes[$i]['CAPTION']
			);
		}
		$this->htmlCode .= "</SELECT>\n";
		return $this->htmlCode;
	}
	
	//!-----------------------------------------------------------------
	// @function	ComboField::getOptions
	// @desc		Retorna o vetor de op��es inseridas no campo
	// @access		public
	// @return		array
	//!-----------------------------------------------------------------
	function getOptions() {
		return $this->optionAttributes;
	}
	
	//!-----------------------------------------------------------------
	// @function	ComboField::getOptionCount
	// @desc		Busca o n�mero de op��es inseridas no campo de sele��o
	// @access		public
	// @return		int	N�mero de op��es inseridas
	//!-----------------------------------------------------------------
	function getOptionCount() {
		return $this->optionCount;
	}
	
	//!-----------------------------------------------------------------
	// @function	ComboField::setFirstOption
	// @desc		O campo constru�do com a classe ComboField possui por padr�o
	//				uma primeira op��o em branco n�o selecion�vel na lista
	//				de op��es. Este m�todo permite definir um texto para este item
	// @param		first string	Texto para a primeira op��o
	// @access		public	
	// @return		void
	//!-----------------------------------------------------------------
	function setFirstOption($first) {
		if ($first)
			$this->attributes['FIRST'] = Form::resolveI18nEntry($first);
		else
			$this->attributes['FIRST'] = "";
	}
	
	//!-----------------------------------------------------------------
	// @function	ComboField::disableFirstOption
	// @desc		Desabilita ou habilita a inser��o de uma primeira op��o
	//				em branco na lista de op��es
	// @param		setting bool	"TRUE" Valor para o atributo
	// @access		public	
	// @return		void
	//!-----------------------------------------------------------------
	function disableFirstOption($setting=TRUE) {
		if (TypeUtils::isTrue($setting)) {
			$this->attributes['NOFIRST'] = 'T';
			$this->attributes['FIRST'] = "";
		} else
			$this->attributes['NOFIRST'] = 'F';
	}
	
	//!-----------------------------------------------------------------
	// @function	ComboField::setMultiple
	// @desc		Habilita ou desabilita a possibilidade de sele��o de m�ltiplas op��es na lista
	// @param		setting bool	"TRUE" Valor para o atributo
	// @access		public	
	// @return		void
	//!-----------------------------------------------------------------
	function setMultiple($setting=TRUE) {
		if (TypeUtils::isTrue($setting)) {
			$this->attributes['MULTIPLE'] = " MULTIPLE";
			$this->attributes['CLASPS'] = "[]";
			$this->searchDefaults['OPERATOR'] = 'IN';
		} else {
			$this->attributes['MULTIPLE'] = "";
			$this->attributes['CLASPS'] = "";
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	ComboField::setSize
	// @desc		O atributo SIZE de um campo do tipo SELECT define o n�mero
	//				de op��es vis�veis na constru��o do campo, ou seja, a altura
	//				do campo em n�mero de linhas
	// @param		size int	Quantidade de op��es v�siveis
	// @access		public	
	// @return		void
	//!-----------------------------------------------------------------
	function setSize($size) {
		if (TypeUtils::isInteger($size))
			$this->attributes['SIZE'] = " SIZE=\"{$size}\"";
		else
			$this->attributes['SIZE'] = "";
	}
	
	//!-----------------------------------------------------------------
	// @function	ComboField::setWidth
	// @desc		Define a largura da lista de op��es, em pixels
	// @param		width int	Largura para o campo
	// @access		public	
	// @return		void
	//!-----------------------------------------------------------------
	function setWidth($width) {
		if (TypeUtils::isInteger($width))
			$this->attributes['WIDTH'] = " STYLE=\"width:{$width}px\"";
		else
			$this->attributes['WIDTH'] = "";
	}
	
	//!-----------------------------------------------------------------
	// @function	ComboField::addOption
	// @desc		Adiciona uma nova op��o na lista de sele��o
	// @access		public
	// @param		value mixed		Valor do item
	// @param		caption string	Caption do item
	// @param		alt string		"" Texto alternativo
	// @param		index int		"NULL" �ndice onde a op��o deve ser inserida
	// @note		O �ndice de inser��o � baseado em zero e deve ser maior que zero
	//				e menor do que o total de op��es j� inseridas. Com valor NULL para
	//				o par�metro $index, a op��o ser� inserida no final da lista
	// @return		bool	
	//!-----------------------------------------------------------------
	function addOption($value, $caption, $alt="", $index=NULL) {
		$currentCount = $this->getOptionCount();
		if ($index > $currentCount || $index < 0) {
			return FALSE;
		} else {
			$newOption = array();
			$value = trim(TypeUtils::parseString($value));
			if ($value == '')
				PHP2Go::raiseError(PHP2Go::getLangVal('ERR_MISSING_COMBOOPTION_VALUE', array(($currentCount-1), $this->name)), E_USER_ERROR, __FILE__, __LINE__);
			else
				$newOption['VALUE'] = $value;
			if (!$caption || trim($caption) == '')
				$newOption['CAPTION'] = $newOption['VALUE'];
			else
				$newOption['CAPTION'] = trim($caption);
			$newOption['ALT'] = trim($alt);
			if ($index == $currentCount || !TypeUtils::isInteger($index)) {
				$this->optionAttributes[$currentCount] = $newOption;
			} else {
				for ($i=$currentCount; $i>$index; $i--) {
					$this->optionAttributes[$i] = $this->optionAttributes[$i-1];
				}
				$this->optionAttributes[$index] = $newOption;
			}
			$this->optionCount++;
			return TRUE;
		}			
	}
	
	//!-----------------------------------------------------------------
	// @function	ComboField::removeOption
	// @desc		Remove uma op��o da lista de sele��o
	// @param		index int	�ndice a ser removido
	// @access		public	
	// @return		bool
	//!-----------------------------------------------------------------
	function removeOption($index) {
		$currentCount = $this->getOptionCount();
		if ($currentCount == 1 || !TypeUtils::isInteger($index) || $index >= $currentCount || $index < 0) {
			return FALSE;
		} else {
			for ($i=$index; $i<($currentCount-1); $i++) {
				$this->optionAttributes[$i] = $this->optionAttributes[$i+1];
			}
			unset($this->optionAttributes[$currentCount-1]);
			$this->optionCount--;
			return TRUE;
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	ComboField::onLoadNode
	// @desc		M�todo respons�vel por processar atributos e nodos filhos
	//				provenientes da especifica��o XML do campo
	// @access		protected
	// @param		attrs array		Atributos do nodo
	// @param		children array	Vetor de nodos filhos
	// @return		void
	//!-----------------------------------------------------------------
	function onLoadNode($attrs, $children) {
		parent::onLoadNode($attrs, $children);
		// texto da primeira op��o
		$this->setFirstOption(@$attrs['FIRST']);
		// primeira op��o (vazia ou n�o) desabilitada
		$this->disableFirstOption(Form::resolveBooleanChoice(@$attrs['NOFIRST']));
		// escolha m�ltipla
		$size = @$attrs['SIZE'];
		$this->setMultiple(Form::resolveBooleanChoice(@$attrs['MULTIPLE']) && TypeUtils::isInteger($size));
		// tamanho
		$this->setSize($size);
		// largura em pixels
		$this->setWidth(@$attrs['WIDTH']);
		// op��es
		if (isset($children['OPTION'])) {
			$options = TypeUtils::toArray($children['OPTION']);
			for ($i=0, $s=sizeof($options); $i<$s; $i++)
				$this->addOption($options[$i]->getAttribute('VALUE'), $options[$i]->getAttribute('CAPTION'), $options[$i]->getAttribute('ALT'));
		}
	}
}
?>