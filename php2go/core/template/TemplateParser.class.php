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
// $Header: /www/cvsroot/php2go/core/template/TemplateParser.class.php,v 1.7 2005/08/30 14:13:19 mpont Exp $
// $Date: 2005/08/30 14:13:19 $

//------------------------------------------------------------------
import('php2go.file.FileSystem');
//------------------------------------------------------------------

// @const T_BYFILE "0"
// Template ou include do tipo arquivo
define('T_BYFILE', 0);
// @const T_BYVAR "1"
// Template ou include do tipo string
define('T_BYVAR', 1);
// @const TP_ROOTBLOCK "_ROOT"
// Nome do bloco raiz do template
define('TP_ROOTBLOCK', '_ROOT');

define('TEMPLATE_PATTERN', '~\r?\n?[\t ]*<!--[ ]?(START|END) (IGNORE)[ ]?-->|\r?\n?[\t ]*<!--[ ]?(START|END|INCLUDE|INCLUDESCRIPT|REUSE) (BLOCK) : ([a-zA-Z0-9_\.\-\\\/\~\s]+)[ ]?-->|{[^}]+}~ms');
define('TEMPLATE_QUOTED_STRING', '(?:"[^"\\\\]*(?:\\\\.[^"\\\\]*)*"|\'[^\'\\\\]*(?:\\\\.[^\'\\\\]*)*\')');
define('TEMPLATE_NUMBER', '(?:\-?\d+(?:\.\d+)?)');
define('TEMPLATE_VARIABLE', '([\w\.\-\:\[\]]+)');
define('TEMPLATE_MODIFIER', '((?:\|@?\w+(?::(?:\w+|' . TEMPLATE_NUMBER . '|' . TEMPLATE_QUOTED_STRING .'))*)*)');

//!-----------------------------------------------------------------
// @class		TemplateParser
// @desc		A classe TemplateParser é utilizada na interpretação de templates,
//				e na construção de uma estrutura de dados contendo as definições e declarações
// @package		php2go.template
// @extends		PHP2Go
// @uses		FileSystem
// @author		Marcos Pont
// @version		$Revision: 1.7 $	
//!-----------------------------------------------------------------
class TemplateParser extends PHP2Go
{
	var $tplBase;			// @var tplBase array			Conteúdo e tipo do template base
	var $tplDef;			// @var tplDef array			Definições de variáveis, código e blocos do template
	var $tplRaw;			// @var tplRaw array			Conteúdo original do template e dos includes processados
	var $tplInclude;		// @var tplInclude array		Armazena os includes adicionados na classe
	var $ignore;			// @var ignore bool				Flag de controle para trechos marcados para serem ignorados no template
	var $prepared;			// @var prepared bool			Indica se o template já foi preparado
	var $blockParent;		// @var blockParent array		Armazena a estrutura de relacionamento entre blocos
	var $blockIndex;		// @var blockIndex array		Armazena a estrutura de índice de instâncias de bloco
	var $blockStack;		// @var blockStack array		Armazena a pilha de definição de blocos
	var $varModifiers;		// @var varModifiers array		Conjunto de modificadores registrados para as variáveis do template
	
	//!-----------------------------------------------------------------
	// @function	TemplateParser::TemplateParser
	// @desc		Construtor da classe
	// @param		value string	Nome do arquivo ou conteúdo string para o template
	// @param		type int		T_BYFILE (arquivo) ou T_BYVAR (string)
	// @access		public
	//!-----------------------------------------------------------------
	function TemplateParser($value, $type) {
		parent::PHP2Go();
		// inicializa a estrutura de template base e templates de inclusão
		if ($type != T_BYFILE && $type != T_BYVAR)
			$type = T_BYVAR;		
		$this->tplBase = array($value, $type);
		$this->tplInclude = array();
		// inicializa a estrutura de dados
		$this->_initializeStructure();
	}
	
	//!-----------------------------------------------------------------
	// @function	TemplateParser::getCacheData
	// @desc		Retorna o conjunto de dados para gravação de cache
	// @access		public
	// @return		array
	//!-----------------------------------------------------------------
	function getCacheData() {
		return array(
			'tplDef' => $this->tplDef,
			'blockParent' => $this->blockParent,
			'blockIndex' => $this->blockIndex,
			'varModifiers' => $this->varModifiers
		);
	}
	
	//!-----------------------------------------------------------------
	// @function	TemplateParser::loadCacheData
	// @desc		Carrega para a classe informações carregadas da cache,
	//				evitando que o conteúdo do template seja interpretado novamente
	// @param		data array	Dados carregados do arquivo de cache
	// @access		public
	//!-----------------------------------------------------------------
	function loadCacheData($data) {
		$this->tplDef = $data['tplDef'];
		$this->blockParent = $data['blockParent'];
		$this->blockIndex = $data['blockIndex'];
		$this->varModifiers = $data['varModifiers'];
		$this->prepared = TRUE;
	}
	
	//!-----------------------------------------------------------------
	// @function	TemplateParser::parse
	// @desc		Executa a interpretação do conteúdo do template
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function parse() {
		if (!$this->prepared) {
			$index = $this->_prepareTemplate($this->tplBase[0], $this->tplBase[1]);
			$control = array('block' => TP_ROOTBLOCK, 'code' => 0, 'var' => 0);
			$this->_parseTemplate($index, $control);
			// controle de balanceamento de tags de bloco
			if (!empty($this->blockStack)) {
				$last = array_pop($this->blockStack);			
				PHP2Go::raiseError(PHP2Go::getLangVal('ERR_UNBALANCED_BLOCKDEF', $last), E_USER_ERROR, __FILE__, __LINE__);
			}
			$this->prepared = TRUE;
		} else {
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_TEMPLATE_ALREADY_PREPARED'), E_USER_ERROR, __FILE__, __LINE__);
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	TemplateParser::_prepareTemplate
	// @desc		Executa a etapa de preparação de um template
	// @param		value string	Nome de arquivo ou conteúdo string
	// @param		type int		T_BYFILE (arquivo) ou T_BYVAL (string)
	// @access		private
	// @return		int	Índice na tabela de conteúdo para o template preparado
	//!-----------------------------------------------------------------
	function _prepareTemplate($value, $type) {
		if ($type == T_BYFILE) {
			// nome de arquivo vazio
			if (empty($value))
				PHP2Go::raiseError(PHP2Go::getLangVal('ERR_EMPTY_TEMPLATE_FILE'), E_USER_ERROR, __FILE__, __LINE__);
			$size = @filesize($value);
			$code = FileSystem::getContents($value);
		} else {
			$size = strlen($value);
			$code = $value;
		}
		// processa referências a tabelas de linguagem
		$code = preg_replace_callback(PHP2GO_I18N_PATTERN, array($this, '_i18nPreFilter'), $code);
		$this->tplRaw[] = array('code' => $code, 'size' => $size);
		return sizeof($this->tplRaw) - 1;
	}
	
	//!-----------------------------------------------------------------
	// @function	TemplateParser::_parseTemplate
	// @desc		Método de interpretação de um arquivo template
	// @param		index int		Índice na tabela de conteúdo
	// @param		&control array	Array de controle
	// @return		void
	//!-----------------------------------------------------------------
	function _parseTemplate($index, &$control) {
		$curBlock = $control['block'];
		$code = $control['code'];
		$var = $control['var'];
		$matches = array();
		$matchStart = 0;
		$matchEnd = 0;	
		$textStart = 0;
		$str = $this->tplRaw[$index]['code'];
		$size = $this->tplRaw[$index]['size'];
		$n = preg_match_all(TEMPLATE_PATTERN, $str, $matches, PREG_SET_ORDER|PREG_OFFSET_CAPTURE);
		for ($i=0; $i<$n; $i++) {			
			$matchStart = $matches[$i][0][1];
			$matchEnd = $matchStart + strlen($matches[$i][0][0]);
			// código
			if ($matchStart > $textStart) {
				$codeStr = substr($str, $textStart, $matchStart-$textStart);
				$this->tplDef[$curBlock]["C:$code"] = $codeStr;
				$code++;
			}
			$textStart = $matchEnd;
			// bloco ignore
			if (sizeof($matches[$i] == 3) && @$matches[$i][2][0] == 'IGNORE') {
				$op = $matches[$i][1][0];
				if ($op == 'START') {
					if ($this->ignore)
						PHP2Go::raiseError(PHP2Go::getLangVal('ERR_UNBALANCED_BLOCKDEF', "IGNORE (position {$matchStart})"), E_USER_ERROR, __FILE__, __LINE__);
					$this->ignore = TRUE;
				} else {
					if (!$this->ignore)
						PHP2Go::raiseError(PHP2Go::getLangVal('ERR_UNBALANCED_BLOCKDEF', "IGNORE (position {$matchStart})"), E_USER_ERROR, __FILE__, __LINE__);					
					$this->ignore = FALSE;
				}
			} elseif (!$this->ignore) {
				// variáveis
				if (sizeof($matches[$i]) == 1) {
					$varData = $this->_parseVariable($matches[$i][0][0], $var);
					if ($varData) {						
						$this->tplDef[$curBlock]["V:$var"] = $varData;
						$var++;
					} else {
						$this->tplDef[$curBlock]["C:$code"] = $matches[$i][0][0];
						$code++;
					}
				}
				// operações de bloco
				elseif (sizeof($matches[$i]) == 6 && @$matches[$i][4][0] == 'BLOCK') {
					$op = $matches[$i][3][0];
					$block = trim($matches[$i][5][0]);
					// início de bloco
					if ($op == 'START') {						
						$this->tplDef[$curBlock]["B:$block"] = NULL;
						$this->tplDef[$block] = array();
						$this->blockParent[$block] = $curBlock;
						$this->blockIndex[$block] = 0;
						array_push($this->blockStack, $block);
						$curBlock = $block;
					// final de bloco
					} elseif ($op == 'END') {
						$last = array_pop($this->blockStack);
						if ($last === NULL)				
							PHP2Go::raiseError(PHP2Go::getLangVal('ERR_UNBALANCED_BLOCKDEF', $block), E_USER_ERROR, __FILE__, __LINE__);
						elseif ($block != $last)
							PHP2Go::raiseError(PHP2Go::getLangVal('ERR_UNBALANCED_BLOCKDEF', $last), E_USER_ERROR, __FILE__, __LINE__);
						else
							$curBlock = (empty($this->blockStack) ? TP_ROOTBLOCK : $this->blockStack[sizeof($this->blockStack)-1]);						
					// inclusão de template externo
					} elseif ($op == 'INCLUDE') {
						$defined = TRUE;
						if (isset($this->tplInclude[$block])) {
							$value = $this->tplInclude[$block][0];
							$type = $this->tplInclude[$block][1];
						} elseif (file_exists($block)) {
							$value = $block;
							$type = T_BYFILE;
						} else {
							$defined = FALSE;
						}
						if ($defined) {
							$index = $this->_prepareTemplate($value, $type);
							$control = array('block' => $curBlock, 'code' => $code, 'var' => $var);
							$this->_parseTemplate($index, $control);
							$code = $control['code'];
							$var = $control['var'];							
						}
					// inclusão de script externo
					} elseif ($op == 'INCLUDESCRIPT') {
						$defined = TRUE;
						if (isset($this->tplInclude[$block])) {
							$value = $this->tplInclude[$block][0];
							$type = $this->tplInclude[$block][1];
						} elseif (file_exists($block)) {
							$value = $block;
							$type = T_BYFILE;
						} else {
							$defined = FALSE;
						}
						if ($defined) {
							ob_start();
							if ($type == T_BYFILE) {
								if (!@include_once($value))
									PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CANT_READ_FILE', $value), E_USER_ERROR, __FILE__, __LINE__);
							} else {
								eval("?>" . $value);
							}
							$this->tplDef[$curBlock]["C:$code"] = ob_get_contents();
							$code++;
							ob_end_clean();
						}
					// reutilização de bloco
					} elseif ($op == 'REUSE') {
						$reuse = array();
						if (preg_match("/^(.+) AS (.+)$/", $matches[$i][5][0], $reuse)) {
							$original = trim($reuse[1]);
							$copy = trim($reuse[2]);
							if (isset($this->tplDef[$original])) {
								$this->tplDef[$copy] = $this->tplDef[$original];
								$this->tplDef[$curBlock]["B:$copy"] = NULL;
								$this->blockParent[$copy] = $curBlock;
								$this->blockIndex[$copy] = 0;
							} else {
								PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CANT_FIND_BLOCK_REUSE', array($original, $copy)), E_USER_ERROR, __FILE__, __LINE__);
							}							
						}
					}
				}
			} else {				
				$this->tplDef[$curBlock]["C:$code"] = $matches[$i][0][0];
				$code++;
			}
		}
		// código no final do template
		if ($size > $matchEnd) {
			$codeStr = substr($str, $matchEnd, $size-$matchEnd);
			if (!empty($codeStr)) {				
				$this->tplDef[$curBlock]["C:$code"] = $codeStr;
				$code++;
			}
		}
		$control['code'] = $code;
		$control['var'] = $var;
	}
	
	//!-----------------------------------------------------------------
	// @function	TemplateParser::_parseVariable
	// @desc		Valida o nome de uma variável, separando o nome e os modificadores
	//				de valor, se existentes. Retorna FALSE se a variável não se enquadrar
	//				no padrão
	// @access		private
	// @param		varDef string	Conteúdo original da variável
	// @param		index int		Índice da variável na tabela de definição
	// @return		mixed Nome da variável ou FALSE se ela for inválida
	//!-----------------------------------------------------------------
	function _parseVariable($varDef, $index) {
		// hash de modificadores
		static $modHash;
		if (!isset($modHash)) {
			$modHash = include_once(PHP2GO_ROOT . 'core/template/templateModifiers.php');
		}
		// padrão de detecção de variável e modificadores
		$matches = array();
		$pattern = '~{' . TEMPLATE_VARIABLE . TEMPLATE_MODIFIER . '}~xs';		
		if (preg_match($pattern, $varDef, $matches)) {
			$list = array();
			$variableName = $matches[1];
			if (!empty($matches[2])) {
				// separa os modificadores
				$modifiers = array();
				preg_match_all('~\|(@?\w+)((?>:(?:' . TEMPLATE_QUOTED_STRING . '|[^|]+))*)~', $matches[2], $modifiers);
				list(, $modifiers, $modifierArgs) = $modifiers;
				for ($i=0, $s=sizeof($modifiers); $i<$s; $i++) {
					// o modificador existe?
					if (isset($modHash[$modifiers[$i]])) {
						$modSpec = (array)$modHash[$modifiers[$i]];
						$modSize = sizeof($modSpec);
						if ($modSize == 1) { 
							$modCallback = $modSpec[0];			// função simples
						} elseif ($modSize == 2) {
							$modCallback = $modSpec;			// método estático
						} elseif ($modSize == 3) {
							import(array_shift($modSpec));		// método estático sem import
							$modCallback = $modSpec;
						} else {
							// raise error
							continue;
						}
						$args = array();
						preg_match_all('~:(' . TEMPLATE_QUOTED_STRING . '|[^:]+)~', $modifierArgs[$i], $args);
						$list[$modifiers[$i]] = array($modCallback, ($args[1] ? ', ' . implode(', ', (array)$args[1]) : ''));
					} else {
						PHP2Go::raiseError(PHP2Go::getLangVal('ERR_INVALID_MODIFIER', $modifiers[$i]), E_USER_ERROR, __FILE__, __LINE__);
					}
				}
			}
			$this->varModifiers["V:$index"] = $list;
			return $variableName;
		} else {
			return FALSE;
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	TemplateParser::_initializeStructure
	// @desc		Inicializa a estrutura de dados antes da interpretação do código do template
	// @access		private
	// @return		void
	//!-----------------------------------------------------------------
	function _initializeStructure() {
		$this->tplDef = array(TP_ROOTBLOCK => array());
		$this->tplRaw = array();
		$this->ignore = FALSE;
		$this->prepared = FALSE;
		$this->blockParent = array();
		$this->blockIndex[TP_ROOTBLOCK] = 0;
		$this->blockStack = array();
		$this->varModifiers = array();	
	}
	
	//!-----------------------------------------------------------------
	// @function	TemplateParser::_i18PreFilter
	// @desc		Substitui uma referência de linguagem/internacionalização
	// @access		private	
	// @param		match array		Variável de internacionalização
	// @return		string Variável traduzida para a linguagem ativa	
	// @note		Este método é utilizado dentro do método _prepareTemplate	
	//!-----------------------------------------------------------------
	function _i18nPreFilter($match) {
		return PHP2Go::getLangVal($match[1]);		
	}
}
?>