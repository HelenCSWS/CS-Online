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
// $Header: /www/cvsroot/php2go/core/gui/LayerMenu.class.php,v 1.16 2005/06/03 13:08:41 mpont Exp $
// $Date: 2005/06/03 13:08:41 $

//------------------------------------------------------------------
import('php2go.gui.Menu');
//------------------------------------------------------------------

// @const LAYER_MENU_SIDE "1"
// Os itens de menu s�o dispostos lado a lado, cada qual com seu tamanho
define('LAYER_MENU_SIDE', 1);

// @const LAYER_MENU_EQUAL "2"
// Todos os itens s�o criados com a largura do maior item (baseada no comprimento da caption)
define('LAYER_MENU_EQUAL', 2);

//!-----------------------------------------------------------------
// @class		LayerMenu
// @desc		A partir da estrutura de dados definida na classe pai Menu,
//				esta classe monta o c�digo de gera��o dos menus utilizando
//				camadas DHTML para representar os n�veis
// @package		php2go.gui
// @extends		Menu
// @author		Marcos Pont
// @version		$Revision: 1.16 $
//!-----------------------------------------------------------------
class LayerMenu extends Menu
{
	var $width; 					// @var width int				Largura do menu
	var $height = 20; 				// @var height int				"20" Altura do menu na orienta��o horizontal ou da op��o de menu na orienta��o vertical
	var $offsetX = 0; 				// @var offsetX int				"0" Deslocamento do menu em rela��o ao extremo esquerdo da p�gina
	var $offsetY = 0; 				// @var offsetY int				"0" Deslocamento do menu em rela��o ao topo
	var $itemSpacing = 0;			// @var itemSpacing int			"0" Espa�amento entre os itens da raiz do menu
	var $isHorizontal = TRUE; 		// @var isHorizontal bool		"TRUE" Indica se a orienta��o dos menus � horizontal - TRUE - ou vertical - FALSE
	var $addressPrefix = ''; 		// @var addressPrefix string	"" Prefixo a ser adicionado em todos os links do menu
	var $rootStyles = array(); 		// @var rootStyles array		"array()" Estilos para os elementos da raiz do menu
	var $rootDisposition;			// @var rootDisposition int		Forma como os itens da raiz s�o dispostos (somente para menu horizontal)
	var $childrenStyles = array(); 	// @var childrenStyles array	"array()" Estilos para os outros n�veis do menu
	var $childrenHeight = 18; 		// @var childrenHeight int		"18" Altura dos n�veis de menu abaixo da raiz
	var $childrenTimeout = 100; 	// @var childrenTimeout int		"100" N�mero de milisegundos para fechamento da op��o do menu ap�s perder o foco do mouse
	var $minimumChildWidth = 0;		// @var minimumChildWidth int		"0" Largura m�nima para um item de menu
	var $menuCode = ''; 			// @var menuCode string			"" C�digo JavaScript resultante para gera��o do menu

	//!-----------------------------------------------------------------
	// @function	LayerMenu::LayerMenu
	// @desc		Construtor da classe, inicializa a classe superior
	// @access		public
	// @param		&Document Document object	Documento onde o menu ser� constru�do
	//!-----------------------------------------------------------------
	function LayerMenu(&$Document) {
		Menu::Menu($Document);
		$this->rootDisposition = LAYER_MENU_EQUAL;
	}

	//!-----------------------------------------------------------------
	// @function 	LayerMenu::setSize
	// @desc 		Ajusta o tamanho do menu
	// @access 		public
	// @param 		width int		Largura para o menu
	// @param 		height int		"0" Altura para o menu. Se n�o informada, utilizar� o padr�o pr�-definido
	// @return		void
	// @note 		Na orienta��o horizontal, a largura ser� interpretada como
	// 				o tamanho geral do menu (todas as op��es lado a lado). J�
	// 				na orienta��o vertical, ser� interpretada como a largura
	// 				do n�vel zero do menu (raiz)
	//!-----------------------------------------------------------------
	function setSize($width, $height = 0) {
		$this->width = abs($width);
		if ($height) $this->height = abs($height);
	}

	//!-----------------------------------------------------------------
	// @function 	LayerMenu::setStartPoint
	// @desc 		Configura o ponto (X,Y) a partir do qual o menu dever�
	// 				ser gerado
	// @access 		public
	// @param 		left int		Posi��o X inicial, em rela��o ao extremo esquerdo
	// @param 		top int	Posi��o Y inicial, em rela��o ao topo
	// @return		void	
	//!-----------------------------------------------------------------
	function setStartPoint($left, $top) {
		$this->offsetX = TypeUtils::parseIntegerPositive($left);
		$this->offsetY = TypeUtils::parseIntegerPositive($top);
	}

	//!-----------------------------------------------------------------
	// @function 	LayerMenu::setVertical
	// @desc 		Configura o menu para ser gerado verticalmente
	// @access 		public
	// @return		void	
	//!-----------------------------------------------------------------
	function setVertical() {
		$this->isHorizontal = false;
	}

	//!-----------------------------------------------------------------
	// @function 	LayerMenu::setAddressPrefix
	// @desc 		Configura um prefixo a ser inserido em todos os links
	// 				presentes no menu
	// @access		public
	// @return		void	
	//!-----------------------------------------------------------------
	function setAddressPrefix($prefix) {
		$this->addressPrefix = $prefix;
	}
	
	//!-----------------------------------------------------------------
	// @function	LayerMenu::setRootDisposition
	// @desc		Define como deve ser dispostos os itens do primeiro n�vel do menu
	// @access		public
	// @param		disposition int		Tipo de disposi��o de elementos
	// @note		Este m�todo somente tem efeito em menus horizontais
	// @note		Valores poss�veis:<BR>
	//				LAYER_MENU_SIDE - os itens s�o dispostos lado a lado, cada qual com seu tamanho<BR>
	//				LAYER_MENU_EQUAL - todos os itens s�o criados com a largura do maior item
	//!-----------------------------------------------------------------
	function setRootDisposition($disposition) {
		if ($disposition == LAYER_MENU_SIDE || $disposition == LAYER_MENU_EQUAL) {
			$this->rootDisposition = $disposition;
		}
	}

	//!-----------------------------------------------------------------
	// @function 	LayerMenu::setRootStyles
	// @desc 		Permite fornecer uma lista de configura��es para as
	// 				op��es do n�vel raiz do menu
	// @access 		public
	// @param 		reg string		Estilo tipo CLASS padr�o para a op��o de menu
	// @param 		over string		"" Estilo tipo CLASS para a op��o logo ap�s o evento onMouseOver
	// @param 		border string	"" Estilo para a borda, se existir
	// @param 		borderX int	"1" Tamanho das bordas inferior/superior da op��o de menu
	// @param 		borderY int	"1" Tamanho das bordas laterais da op��o de menu
	// @return		void	
	// @see 		Menu::setChildrenStyles
	//!-----------------------------------------------------------------
	function setRootStyles($reg, $over = '', $border = '', $borderX = '', $borderY = '') {
		$this->rootStyles['reg'] = $reg;
		$this->rootStyles['over'] = ($over == '') ? $reg : $over;
		$this->rootStyles['border'] = $border;
		$this->rootStyles['borderX'] = !empty($borderX) ? $borderX : '0.00001';
		$this->rootStyles['borderY'] = !empty($borderY) ? $borderY : '0.00001';
	}

	//!-----------------------------------------------------------------
	// @function 	LayerMenu::setChildrenStyles
	// @desc 		Permite fornecer uma lista de configura��es para as
	// 				op��es dos outros n�veis do menu, independentemente das
	// 				configura��es da raiz
	// @access 		public
	// @param 		reg string		Estilo tipo CLASS padr�o para as op��es
	// @param 		over string		"" Estilo tipo CLASS para as op��es logo ap�s o evento onMouseOver
	// @param 		border string	"" Estilo para as bordas, se existir
	// @param 		borderX int	"1" Tamanho das bordas inferior/superior das op��es de menu
	// @param 		borderY int	"1" Tamanho das bordas laterais das op��es de menu
	// @return		void	
	// @see 		Menu::setRootStyles
	//!-----------------------------------------------------------------
	function setChildrenStyles($reg, $over = '', $border = '', $borderX = '', $borderY = '') {
		$this->childrenStyles['reg'] = $reg;
		$this->childrenStyles['over'] = ($over == '') ? $reg : $over;
		$this->childrenStyles['border'] = $border;
		$this->childrenStyles['borderX'] = !empty($borderX) ? $borderX : '0.00001';
		$this->childrenStyles['borderY'] = !empty($borderY) ? $borderY : '0.00001';
	}
	
	//!-----------------------------------------------------------------
	// @function 	LayerMenu::setChildrenHeight
	// @desc 		Configura a altura das op��es de menu abaixo da raiz
	// @access 		public
	// @param 		height int		Altura desejada
	// @return		void	
	//!-----------------------------------------------------------------
	function setChildrenHeight($height) {
		$this->childrenHeight = TypeUtils::parseIntegerPositive($height);
	}

	//!-----------------------------------------------------------------
	// @function 	LayerMenu::setChildrenTimeout
	// @desc 		Configura o tempo em milisegundos que uma op��o de menu
	// 				se mant�m aberta ap�s a perda do foco do mouse
	// @access 		public
	// @param 		timeout int		Timeout desejado
	// @return		void	
	//!-----------------------------------------------------------------
	function setChildrenTimeout($timeout) {
		$this->childrenTimeout = TypeUtils::parseIntegerPositive($timeout);
	}
	
	//!-----------------------------------------------------------------
	// @function	LayerMenu::setMinimumChildWidth
	// @desc		Define a largura m�nima para qualquer dos itens 
	//				do menu, a partir do segundo n�vel
	// @access		public
	// @param		min int			Largura m�nima para itens de menu, em pixels
	// @return		void
	//!-----------------------------------------------------------------
	function setMininumChildWidth($min) {
		$this->minimumChildWidth = $min;
	}

	//!-----------------------------------------------------------------
	// @function	LayerMenu::setItemSpacing
	// @desc		Define o espa�amento entre os itens da raiz do menu, em pixels
	// @access		public
	// @param		itemSpacing int	Espa�amento entre os itens do menu
	// @return		void
	//!-----------------------------------------------------------------
	function setItemSpacing($itemSpacing) {
		$this->itemSpacing = TypeUtils::parseIntegerPositive($itemSpacing);
	}

	//!-----------------------------------------------------------------
	// @function 	LayerMenu::display
	// @desc 		Constr�i e imprime o c�digo do menu
	// @access 		public
	// @return		void	
	//!-----------------------------------------------------------------
	function display() {
		Menu::buildMenu();
		$this->_buildCode();
		print $this->menuCode;
	}

	//!-----------------------------------------------------------------
	// @function 	LayerMenu::getContent
	// @desc 		Constr�i e retorna o c�digo do menu
	// @access 		public
	// @return		string Conte�do respons�vel pela gera��o do menu de camadas
	//!-----------------------------------------------------------------
	function getContent() {
		Menu::buildMenu();
		$this->_buildCode();
		return $this->menuCode;
	}

	//!-----------------------------------------------------------------
	// @function 	LayerMenu::_buildCode
	// @desc 		Constr�i o c�digo JavaScript de constru��o do menu a
	// 				partir dos dados j� coletados e a partir das configura��es
	// 				existentes no objeto
	// @access 		private
	// @return		void	
	//!-----------------------------------------------------------------
	function _buildCode() {
		
		$this->_Document->addScript(PHP2GO_JAVASCRIPT_PATH . "ext/coolmenus/coolmenus4.js");
		$this->_Document->addScript(PHP2GO_JAVASCRIPT_PATH . "ext/coolmenus/cm_addins.js");
		
		// c�digo inicial do menu
		$this->_Document->appendHeaderContent("
		<style type=\"text/css\">
		.clCMEvent { position:absolute; z-index:300; width:100%; height:100%; clip:rect(0,100%,100%,0); left:0; top:0; visibility:hidden }
		.clCMAbs { position:absolute; width:10; height:10; left:0; top:0; visibility:hidden }
		</style>		
		");		
		$this->menuCode .= "		
		<SCRIPT TYPE=\"text/javascript\">
		{$this->name} = new makeCM(\"{$this->name}\");
		{$this->name}.resizeCheck = 1;
		{$this->name}.openOnClick = 1;
		{$this->name}.rows = " . ((int)$this->isHorizontal) . ";
		{$this->name}.onlineRoot = \"" . $this->addressPrefix . "\";
		{$this->name}.menuPlacement = 0;
		{$this->name}.pxBetween = {$this->itemSpacing};
		{$this->name}.fromLeft = " . $this->offsetX . ";
		{$this->name}.fromTop = " . $this->offsetY . ";
		{$this->name}.wait = " . $this->childrenTimeout . ";
		{$this->name}.zIndex = 400;		
		";
		
		// cria��o dos n�veis para o menu horizontal
		if ($this->isHorizontal) {
			
			// altura da raiz
			$rootHeight = $this->height+1;
			
			// os itens da raiz devem ser dispostos em larguras iguais
			if ($this->rootDisposition == LAYER_MENU_EQUAL) {
				$textSize = 1;
				for ($i = 0; $i < sizeOf($this->tree); $i++)
					$textSize = (strlen($this->tree[$i]['CAPTION']) > $textSize) ? strlen($this->tree[$i]['CAPTION']) : $textSize;
				$textSize = ($textSize * 6) + 20;
				for ($i=0; $i<sizeOf($this->tree); $i++)
					$rootWidth[$i] = $textSize;
			}
			// os itens da raiz s�o dispostos lado a lado
			elseif ($this->rootDisposition == LAYER_MENU_SIDE) {
				for ($i=0; $i<sizeOf($this->tree); $i++)
					$rootWidth[$i] = (strlen($this->tree[$i]['CAPTION']) * 6) + 20;
			}
			
			$this->menuCode .= "     			
			var menuSize = " . (isset($this->width) ? $this->width : "screen.width") . "-" . $this->offsetX . "-22;\n
			{$this->name}.useBar = 1;
			{$this->name}.barWidth = menuSize;
			{$this->name}.barHeight = {$this->height};
			{$this->name}.barX = {$this->offsetX};
			{$this->name}.barY = {$this->offsetY};
			{$this->name}.barClass = \"" . $this->_getStyle(0, 'reg') . "\";
			{$this->name}.barBorderX = 0;
			{$this->name}.barBorderY = 0;			
			";
			for ($i = 0; $i <= $this->lastLevel; $i++) {
				$this->menuCode .= "
				{$this->name}.level[$i] = new cm_makeLevel(80, " . (($i==0) ? $this->height : $this->childrenHeight) . ", \"" . $this->_getStyle($i, 'reg') . "\", \"" . $this->_getStyle($i, 'over') . "\", " . $this->_getStyle($i, 'borderX') . ", " . $this->_getStyle($i, 'borderY') . ", \"" . $this->_getStyle($i, 'border') . "\" , 0, \"bottom\", -1, -1, \"\", 10, 10, 0);
				";				
			}
			
		} else {
			
			// altura da raiz
			$rootHeight = $this->childrenHeight;
			
			// c�lculo da largura m�xima dos itens da raiz
			if (isset($this->width)) {
				$textSize = $this->width;
			} else {
				$textSize = 1;
				for ($i = 0; $i < sizeOf($this->tree); $i++)
					$textSize = (strlen($this->tree[$i]['CAPTION']) > $textSize) ? strlen($this->tree[$i]['CAPTION']) : $textSize;
				$textSize = ($textSize * 6) + 20;
			}
			// define posi��es para os itens da raiz
			$placement = $this->offsetY;
			$rootWidth[0] = $textSize;
			for ($i = 1; $i < $this->rootSize; $i++) {
				$rootWidth[$i] = $textSize;
				$placement .= "," . ($this->offsetY + ($i*$this->childrenHeight));
			}
			$this->menuCode .= "
				{$this->name}.menuPlacement = new Array($placement);
			";
			// constr�i os n�veis do menu
			for ($i = 0; $i <= $this->lastLevel; $i++) {
				$this->menuCode .= "
				{$this->name}.level[$i] = new cm_makeLevel(" . (isset($this->width) ? $this->width : 100) . ", " . $this->childrenHeight . ", \"" . $this->_getStyle($i, 'reg') . "\", \"" . $this->_getStyle($i, 'over') . "\", " . $this->_getStyle($i, 'borderX') . ", " . $this->_getStyle($i, 'borderY') . ", \"" . $this->_getStyle($i, 'border') . "\" , 0, \"right\", 0, -1, \"\", 10, 10);";
			}
						
		}
		
		// cria��o dos itens do menu
		for ($i = 0; $i < sizeOf($this->tree); $i++) {
			$thisId = "m" . $i;
			$this->menuCode .= "
			{$this->name}.makeMenu('" . $thisId . "','','" . $this->tree[$i]['CAPTION'] . "','" . $this->tree[$i]['LINK'] . "','" . $this->tree[$i]['TARGET'] . "', " . $rootWidth[$i] . ", " . $rootHeight . ");
			";
			if (!empty($this->tree[$i]['CHILDREN']))
				$this->_buildChildrenCode($this->tree[$i]['CHILDREN'], $thisId, 0);
		}
		$this->menuCode .= "     {$this->name}.construct();\n" . "</SCRIPT>\n";
		
	}

	//!-----------------------------------------------------------------
	// @function 	LayerMenu::_buildChildrenCode
	// @desc 		Fun��o recursiva que monta as cria��es de inst�ncias de
	// 				menu DHTML para a �rvore de op��es de menu da classe
	// @access 		private
	// @param 		children array	Vetor atual/inicial de elementos da �rvore
	// @param 		parentId mixed	�ndice do nodo superior ao vetor $children fornecido
	// @param 		parentLevel int	N�vel onde se encontra o nodo superior ao atual
	// @return		void	
	//!-----------------------------------------------------------------
	function _buildChildrenCode($children, $parentId, $parentLevel) {
		$textSize = 1;
		for ($i = 0; $i < sizeOf($children); $i++) {
			$textSize = (strlen($children[$i]['CAPTION']) > $textSize) ? strlen($children[$i]['CAPTION']) : $textSize;
		}
		$itemWidth = max((($textSize * 6) + 20), $this->minimumChildWidth);
		for ($i = 0; $i < sizeOf($children); $i++) {
			$thisId = $parentId . "_c" . $i;
			$this->menuCode .= "     " . $this->name . ".makeMenu('" . $thisId . "','" . $parentId . "','" . $children[$i]['CAPTION'] . "','" . $children[$i]['LINK'] . "','" . $children[$i]['TARGET'] . "'," . $itemWidth . "," . $this->childrenHeight . ",'','','','','right');\n";
			if (!empty($children[$i]['CHILDREN'])) {
				$this->_buildChildrenCode($children[$i]['CHILDREN'], $thisId, ($parentLevel + 1));
			}
		}
	}

	//!-----------------------------------------------------------------
	// @function 	LayerMenu::_getStyle
	// @desc 		Retorna um estilo nas configura��es passadas para a montagem
	// 				da raiz e dos demais n�veis do menu
	// @access 		private
	// @param 		level int			N�vel onde est� sendo feita a consulta, para
	// 									que se possa diferenciar entre raiz e demais n�veis
	// @param 		element string    Elemento buscado
	// @return 		string Valor do elemento buscado, '' se n�o encontrado e o tipo do valor
	// 				� texto/string, 1 se n�o encontrado e o tipo do valor � num�rico
	//!-----------------------------------------------------------------
	function _getStyle($level, $element) {
		$repository = ($level > 0) ? $this->childrenStyles : $this->rootStyles;
		$value = (isset($repository[$element])) ? $repository[$element] : ($element == 'borderX' || $element == 'borderY' ? 1 : '');
		return $value;
	}
}
?>