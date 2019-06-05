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
// $Header: /www/cvsroot/php2go/core/form/field/DataGrid.class.php,v 1.14 2005/08/31 18:05:30 mpont Exp $
// $Date: 2005/08/31 18:05:30 $

//------------------------------------------------------------------
import('php2go.form.field.DbField');
//------------------------------------------------------------------

//!-----------------------------------------------------------------
// @class		DataGrid
// @desc		A classe DataGrid permite que um conjunto de grid de campos seja gerado
//				em um formulário contendo os dados presentes no resultado de uma consulta ao banco de dados.
//				Desta forma, é possível criar um mecanismo de edição simultânea de vários registros de uma
//				tabela, por exemplo. A especificação XML de um DataGrid deve conter uma fonte de dados, ou
//				<I>DATASOURCE</I>, e um conjunto de campos, ou <I>FIELDSET</I>
// @package		php2go.form.field
// @extends		DbField
// @uses		Template
// @uses		TypeUtils
// @author		Marcos Pont
// @version		$Revision: 1.14 $
//!-----------------------------------------------------------------
class DataGrid extends DbField
{
	var $fieldNames = array();		// @var fieldNames array	"array()" Conjunto de nomes dos campos do fieldset
	var $fieldSet = array();		// @var fieldSet array		"array()" Conjunto de campos do grid
	var $cellSizes = array();		// @var cellSizes array		"array()" Conjunto de tamanhos para as colunas do grid
	
	//!-----------------------------------------------------------------
	// @function	DataGrid::DataGrid
	// @desc		Construtor da classe
	// @access		public
	// @param		&Form Form object		Formulário onde o campo será inserido
	//!-----------------------------------------------------------------
	function DataGrid(&$Form) {
		parent::DbField($Form);
		$this->composite = TRUE;		
		$this->searchable = FALSE;
	}
	
	//!-----------------------------------------------------------------
	// @function	DataGrid::getCode
	// @desc		Monta o código HTML da tabela contendo o grid de campos
	// @access		public
	// @return		string Código HTML do grid
	//!-----------------------------------------------------------------
	function getCode() {
		$this->onPreRender();
		// inicializa o template
		$Tpl = new Template(PHP2GO_TEMPLATE_PATH . 'datagrid.tpl');
		$Tpl->parse();
		$Tpl->assign('width', $this->attributes['TABLEWIDTH']);			
		// linha do cabeçalho (nomes dos campos)
		$Tpl->createBlock('loop_line');
		for ($i=1,$s=$this->_Rs->fieldCount(); $i<$s; $i++) {
			$Field =& $this->_Rs->fetchField($i);
			$Tpl->createAndAssign('loop_header_cell', array(
				'style' => $this->attributes['HEADERSTYLE'],
				'width' => (isset($this->cellSizes[$i-1]) ? " WIDTH=\"{$this->cellSizes[$i-1]}%\"" : ''),
				'col_name' => $Field->name
			));
		}			
		// grid com os campos
		$isPosted = ($this->_Form->isPosted() && !empty($this->value));
		while ($dataRow = $this->_Rs->fetchRow()) {
			$submittedRow = ($isPosted ? @$this->value[$dataRow[0]] : NULL);
			$Tpl->createBlock('loop_line');
			$Tpl->createAndAssign('loop_cell', array(
				'align' => 'left',
				'style' => $this->attributes['CELLSTYLE'], 
				'width' => (isset($this->cellSizes[0]) ? " WIDTH=\"{$this->cellSizes[0]}%\"" : ''), 
				'col_data' => $dataRow[1]
			));
			for ($i=0, $s=sizeof($this->fieldSet); $i<$s; $i++) {
				$Field =& $this->fieldSet[$i];				
				$Field->setName("{$this->name}[{$dataRow[0]}][{$this->fieldNames[$i]}]");
				if ($isPosted) {
					// correção especial para checkboxes
					if ($Field->getFieldTag() == 'CHECKFIELD')
						eval("\$submittedRow['{$this->fieldNames[$i]}'] = \$_{$this->_Form->formMethod}['V_{$this->name}'][{$dataRow[0]}]['{$this->fieldNames[$i]}'];");
					// aplica o valor submetido se ele existir, mesmo vazio
					if (isset($submittedRow[$this->fieldNames[$i]]))						
						$Field->setValue($submittedRow[$this->fieldNames[$i]]);							
					else
						$Field->setValue($dataRow[$i+2]);
				} else {
					$Field->setValue($dataRow[$i+2]);
				}
				$Tpl->createAndAssign('loop_cell', array(
					'align' => 'center', 
					'style' => $this->attributes['CELLSTYLE'], 
					'width' => (isset($this->cellSizes[$i+1]) ? " WIDTH=\"{$this->cellSizes[$i+1]}%\"" : ''), 
					'col_data' => $Field->getCode()
				));
			}
		}			
		// retorna o conteúdo
		unset($this->fieldSet);
		return $Tpl->getContent();
	}
	
	//!-----------------------------------------------------------------
	// @function	DataGrid::setHeaderStyle
	// @desc		Define o estilo CSS para as células do cabeçalho da tabela
	// @param		headerStyle string	Estilo para o cabeçalho
	// @note		O valor padrão para esta propriedade é o estilo definido para os rótulos do formulário	
	// @access		public	
	// @return		void	
	//!-----------------------------------------------------------------
	function setHeaderStyle($headerStyle) {
		if ($headerStyle)
			$this->attributes['HEADERSTYLE'] = " CLASS=\"$headerStyle\"";
		else
			$this->attributes['HEADERSTYLE'] = $this->_Form->getLabelStyle();
	}
	
	//!-----------------------------------------------------------------
	// @function	DataGrid::setCellStyle
	// @desc		Seta o estilo CSS das células do conteúdo da tabela
	// @param		cellStyle string	Estilo para o conteúdo
	// @note		O valor padrão para esta propriedade é o estilo definido para os rótulos do formulário
	// @access		public	
	// @return		void	
	//!-----------------------------------------------------------------
	function setCellStyle($cellStyle) {
		if ($cellStyle)
			$this->attributes['CELLSTYLE'] = " CLASS=\"$cellStyle\"";
		else
			$this->attributes['CELLSTYLE'] = $this->_Form->getLabelStyle();
	}
	
	//!-----------------------------------------------------------------
	// @function	DataGrid::setTableWidth
	// @desc		Define o tamanho da tabela que contém o grid de campos
	// @param		tableWidth int		Tamanho para a tabela
	// @access		public	
	// @return		void
	//!-----------------------------------------------------------------
	function setTableWidth($tableWidth) {
		if ($tableWidth)
			$this->attributes['TABLEWIDTH'] = " WIDTH=\"" . $tableWidth . "\"";
		else			
			$this->attributes['TABLEWIDTH'] = "";
	}
 	
	//!-----------------------------------------------------------------
	// @function	DataGrid::setCellSizes
	// @desc		Define um vetor de tamanhos para as células do grid
	// @access		public
	// @param		sizes array			Vetor de tamanhos contendo N+1 valores inteiros
	//									para os tamanhos das colunas da tabela, onde N é
	//									o número de campos definidos no FIELDSET
	// @return		void
	//!-----------------------------------------------------------------
	function setCellSizes($sizes) {
		if (sizeOf($sizes) != (sizeOf($this->fieldSet) + 1) || array_sum($sizes) != 100) {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_DATAGRID_INVALIDcellSizes', $this->name), E_USER_ERROR, __FILE__, __LINE__);
		} else {
			array_walk($sizes, 'trim');
			$this->cellSizes = $sizes;
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	DataGrid::onLoadNode
	// @desc		Método responsável por processar atributos e nodos filhos
	//				provenientes da especificação XML do campo
	// @access		protected
	// @param		attrs array		Atributos do nodo
	// @param		children array	Vetor de nodos filhos
	// @return		void
	//!-----------------------------------------------------------------
	function onLoadNode($attrs, $children) {
		parent::onLoadNode($attrs, $children);		
		// datasource e fieldset obrigatórios
		if (isset($children['DATASOURCE']) && isset($children['FIELDSET']) &&
			TypeUtils::isInstanceOf($children['FIELDSET'], 'xmlnode') && 
			$children['FIELDSET']->hasChildren()) {
			// instancia e adiciona os campos no fieldset
			for ($i=0, $s=$children['FIELDSET']->getChildrenCount(); $i<$s; $i++) {
				$Child =& $children['FIELDSET']->getChild($i);
				switch($Child->getTag()) {
					case 'EDITFIELD' : $fieldClassName = 'EditField'; break;
					case 'PASSWDFIELD' : $fieldClassName = 'PasswdField'; break;						
					case 'MEMOFIELD' : $fieldClassName = 'MemoField'; break;
					case 'CHECKFIELD' : $fieldClassName = 'CheckField'; break;
					case 'FILEFIELD' : $fieldClassName = 'FileField'; break;
					case 'LOOKUPFIELD' : $fieldClassName = 'LookupField'; break;
					case 'COMBOFIELD' : $fieldClassName = 'ComboField'; break;
					case 'RADIOFIELD' : $fieldClassName = 'RadioField'; break;
					case 'DBRADIOFIELD' : $fieldClassName = 'DbRadioField'; break;
					case 'HIDDENFIELD' : $fieldClassName = 'HiddenField'; break;
					case 'TEXTFIELD' : $fieldClassName = 'TextField'; break;
					default : PHP2Go::raiseError(PHP2Go::getLangVal('ERR_DATAGRID_INVALID_FIELDTYPE', $Child->getTag()), E_USER_ERROR, __FILE__, __LINE__); break;
				}
				import("php2go.form.field.{$fieldClassName}");
				$Field = new $fieldClassName($this->_Form, TRUE);
				$Field->onLoadNode($Child->getAttributes(), $Child->getChildrenTagsArray());
				// campos recebem o atributo disabled do grid
				$Field->setDisabled(Form::resolveBooleanChoice(@$attrs['DISABLED']));
				// nenhum campo pode controlar obrigatoriedade individualmente
				$Field->setRequired(FALSE);
				// nenhum campo filho pode possuir regras
				$Field->rules = array();
				// registra o campo e o nome do campos
				$this->fieldSet[] = $Field;
				$this->fieldNames[] = $Field->getName();
			}			
			// estilo do cabeçalho do grid
			$this->setHeaderStyle(@$attrs['HEADERSTYLE']);
			// estilo da célula
			$this->setCellStyle(@$attrs['CELLSTYLE']);
			// largura da tabela
			$this->setTableWidth(@$attrs['TABLEWIDTH']);
			// tamanhos das células
			if (isset($attrs['CELLSIZES']))
				$this->setCellSizes(explode(',', $attrs['CELLSIZES']));
		} else {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_INVALID_DATAGRID_STRUCTURE', $this->name), E_USER_ERROR, __FILE__, __LINE__);
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	DataGrid::onPreRender
	// @desc		Realiza as operações necessárias para a renderização do
	//				código HTML do grid, bem como a validação do número de colunas
	//				retornadas pelo DATASOURCE
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function onPreRender() {
		parent::processDbQuery(ADODB_FETCH_NUM);
		$fieldCount = $this->_Rs->fieldCount();
		if ($fieldCount != (sizeof($this->fieldSet) + 2))
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_DATAGRID_INVALID_FIELDCOUNT', $this->name), E_USER_ERROR, __FILE__, __LINE__);
	}
}
?>