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
// $Header: /www/cvsroot/php2go/core/template/Template.class.php,v 1.23 2005/08/09 18:40:46 mpont Exp $
// $Date: 2005/08/09 18:40:46 $

//------------------------------------------------------------------
import('php2go.cache.CacheManager');
import('php2go.template.TemplateParser');
import('php2go.text.StringUtils');
import('php2go.util.HtmlUtils');
//------------------------------------------------------------------

//!-----------------------------------------------------------------
// @class		Template
// @desc 		Esta classe é responsável por realizar operações sobre
// 				um arquivo template de código HTML, onde podem ser
// 				substituídas variáveis, replicados blocos de código,
// 				incluídos outros scripts/templates, etc...
// @package		php2go.template
// @extends 	PHP2Go
// @uses 		TemplateParser
// @uses		StringUtils
// @author 		Marcos Pont 
// @version		$Revision: 1.23 $
//!-----------------------------------------------------------------
class Template extends PHP2Go
{
	var $cacheEnabled;				// @var cacheEnabled bool				"FALSE" Utilizar cache
	var $cacheId;					// @var cacheId string					ID de cache, baseado no conteúdo do template
	var $cacheGroup;				// @var cacheGroup string				Nome de grupo para os templates gravados em cache
	var $showUnAssigned = FALSE;	// @var showUnAssigned bool				"FALSE" Flag que torna visível ou esconde variáveis não atribuídas
	var $globalVars = array();		// @var globalVars array				"array()" Vetor de variáveis globais do template
	var $currentBlock = NULL;		// @var currentBlock mixed				"NULL" Ponteiro para o bloco ativo no template
	var $content = array();			// @var content array					"array()" Estrutura interna de armazenamento de instâncias de blocos
	var $Parser = NULL;				// @var Parser TemplateParser object	Parser utilizado na interpretação do template
	var $Cache = NULL;				// @var Cache CacheManager object		Utilizado para realizar as operações de consulta/escrita na cache
	
	//!-----------------------------------------------------------------
	// @function	Template::Template
	// @desc 		Construtor da classe
	// @access 		public 
	// @param 		tplFile string	Caminho do arquivo template no servidor ou código do template em formato string
	// @param 		type int		"T_BYFILE" Tipo do template: arquivo (T_BYFILE) ou string (T_BYVAR)
	//!-----------------------------------------------------------------
	function Template($tplFile, $type=T_BYFILE) {
		parent::PHP2Go();
		$this->Parser = new TemplateParser($tplFile, $type);
		$this->Cache =& CacheManager::getInstance();
		$this->globalVars = array(
			'ldelim' => '{',
			'rdelim' => '}'
		);
		parent::registerDestructor($this, '_Template');
	}

	//!-----------------------------------------------------------------
	// @function	Template::_Template
	// @desc		Destrutor da classe
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function _Template() {
		unset($this);
	}

	//!-----------------------------------------------------------------
	// @function	Template::setCacheProperties
	// @desc		Configura a classe para utilizar cache para o template já interpretado,
	//				definindo diretório onde a cache será gravada, prefixo para o arquivo e
	//				tempo de expiração
	// @access		public
	// @param		dir string		Caminho completo para o diretório onde a cache deve ser gravada
	// @param		lifeTime int	"0" Tempo de vida da cache
	// @return		void	
	//!-----------------------------------------------------------------
	function setCacheProperties($dir, $lifeTime=NULL) {
		$this->Cache->setBaseDir($dir);
		if ($lifeTime)
			$this->Cache->setLifeTime($lifeTime);
		$this->cacheEnabled = TRUE;
	}
	
	//!-----------------------------------------------------------------
	// @function	Template::setShowUnAssigned
	// @desc		Habilita ou desabilita a exibição das variáveis sem valor atribuído		
	// @note		O padrão da classe é não exibir variáveis não atribuídas
	// @param 		flag bool		Estado a ser implantado no flag	
	// @access 		public 
	// @return		void
	//!-----------------------------------------------------------------
	function setShowUnAssigned($flag) {
		$this->showUnAssigned = TypeUtils::toBoolean($flag);
	}
	
	//!-----------------------------------------------------------------
	// @function	Template::parse
	// @desc 		Prepara o template para utilização e parseia todo o
	// 				seu conteúdo buscando por variaveís, blocos e outras
	// 				tags reservadas que permitem realizar operações sobre
	// 				o conteúdo do template
	// @note		Se a classe estiver configurada para utilizar cache, um template
	//				já parseado é buscado no diretório de cache
	// @note		Este método somente poderá ser executado somente uma vez. Na segunda 
	//				execução, uma exceção do tipo E_USER_ERROR será disparada	
	// @access 		public
	// @return		bool
	//!-----------------------------------------------------------------
	function parse() {
		if ($this->cacheEnabled) {
			$this->cacheId = ($this->Parser->tplBase[1] == T_BYFILE ? $this->Parser->tplBase[0] : md5($this->Parser->tplBase[0]));
			$this->Cache->loadMemoryState('__memCache', $this->cacheGroup);
			$data = $this->Cache->load($this->cacheId, $this->cacheGroup);
			if ($data && isset($data['tplDef'])) {
				$this->Parser->loadCacheData($data);
			} else {
				$this->Parser->parse();
				$this->Cache->save($this->Parser->getCacheData(), $this->cacheId, $this->cacheGroup);
			}
			$this->Cache->saveMemoryState('__memCache', $this->cacheGroup);
		} else {
			$this->Parser->parse();
		}
		$this->_initializeContent();
	}
	
	//!-----------------------------------------------------------------
	// @function	Template::resetTemplate
	// @desc		Remove todas as variáveis e blocos criados no template, retornando
	//				o objeto ao estado inicial (quando foi parseado)
	// @access		public
	// @return		void
	//!-----------------------------------------------------------------
	function resetTemplate() {
		if ($this->isPrepared()) {
			$this->_initializeContent();
			$keys = array_keys($this->Parser->blockIndex);
			foreach ($keys as $block)
				$this->Parser->blockIndex[$block] = 0;
		}
	}

	//!-----------------------------------------------------------------
	// @function	Template::isPrepared
	// @desc 		Verifica se o template já foi parseado
	// @access 		public 
	// @return 		bool
	//!-----------------------------------------------------------------
	function isPrepared() {
		return $this->Parser->prepared;
	}
	
	//!-----------------------------------------------------------------
	// @function	Template::isBlockDefined
	// @desc		Verifica se um determinado bloco foi definido no template
	// @note		A consulta pode ser realizada por um nome simples de bloco ou
	//				por uma caminho na estrutura de blocos aninhados
	// @access 		public 
	// @param 		block string		Nome do bloco a ser buscado
	// @return		bool
	// @see 		Template::isVariableDefined
	//!-----------------------------------------------------------------
	function isBlockDefined($block) {
		if ($this->isPrepared()) {
			$parts = explode('.', $block);
			if (sizeof($parts) == 1) {
				return (isset($this->Parser->tplDef[$block]));
			} else {
				$i = 1;
				$ptr = $this->Parser->tplDef[$parts[0]];
				while ($i < sizeof($parts)) {
					if (!array_key_exists("B:{$parts[$i]}", $ptr))
						return FALSE;
					$ptr = @$this->Parser->tplDef[$parts[$i]];
					$i++;
				}
				return TRUE;
			}
		}
		return FALSE;
	}
	
	//!-----------------------------------------------------------------
	// @function	Template::getDefinedBlocks
	// @desc 		Retorna a lista de blocos definidos no template
	// @note		Retorna NULL se o template não estiver preparado
	// @return		array Lista de blocos definidos	
	// @access 		public 
	// @see 		Template::getDefinedVariables
	//!-----------------------------------------------------------------
	function getDefinedBlocks() {
		if ($this->isPrepared())
			return array_keys($this->Parser->tplDef);
		return NULL;
	}
	
	//!-----------------------------------------------------------------
	// @function	Template::isVariableDefined
	// @desc 		Verifica se uma variável está definida no template
	// @note		O parâmetro $variable pode representar uma variável no bloco ativo
	//				ou uma referência do tipo bloco.variavel
	// @param 		variable string	Nome da variável a ser buscada
	// @access 		public	
	// @return		bool
	// @see 		Template::getValue
	// @see 		Template::isBlockDefined
	//!-----------------------------------------------------------------
	function isVariableDefined($variable) {
		if ($this->isPrepared()) {
			if (sizeOf($regs = explode('.', $variable)) == 2) {
				if (!$this->isBlockDefined($regs[0])) {
					return FALSE;
				} else {
					$blockName = $regs[0];
					$variable = $regs[1];
				} 
			} else {
				$blockName = TP_ROOTBLOCK;
			}
			foreach($this->Parser->tplDef[$blockName] as $key => $value) {
				if (strpos($key, "V:") === 0 && $value == $variable)
					return TRUE;
			}
		}
		return FALSE;
	}
	
	//!-----------------------------------------------------------------
	// @function	Template::getDefinedVariables
	// @desc 		Busca as variáveis definidas para um determinado bloco
	// @param 		blockName string	"" Nome do bloco
	// @return		array Vetor com os nomes de variáveis definidos 
	// @note		Se um nome de bloco não foi fornecido, o bloco raiz será utilizado
	// @note		Retorna NULL se o template não estiver preparado
	// @access 		public	
	// @see 		Template::getDefinedBlocks
	//!-----------------------------------------------------------------
	function getDefinedVariables($blockName=NULL) {		
		if ($this->isPrepared()) {
			$vars = array();
			if (!TypeUtils::isNull($blockName)) {
				if (!$this->isBlockDefined($blockName)) 
					return $vars;
			} else {
				$blockName = TP_ROOTBLOCK;
			}
			foreach($this->Parser->tplDef[$blockName] as $key => $value) {
				if (strpos($key, "V:") === 0)
					$vars[] = $value;
			}
			return $vars;
		}
		return NULL;
	}
	
	//!-----------------------------------------------------------------
	// @function	Template::getValue
	// @desc 		Busca o valor atribuído a uma variável
	// @note		O parâmetro de consulta pode ser o nome de uma variável simples
	//				no bloco ativo ou uma referência do tipo bloco.variavel
	// @param 		variable string	Nome da variável buscada	
	// @return		mixed Valor da variável se ela estiver definida ou FALSE em caso contrário
	// @access 		public
	// @see 		Template::isVariableDefined
	//!-----------------------------------------------------------------
	function getValue($variable) {
        if (sizeof($regs = explode('.', $variable)) == 2) {
			if (isset($this->Parser->tplDef[$regs[0]])) {
				$block =& $this->_findBlock($regs[0]);
				$variable = $regs[1];
			}
        } else {
            $block =& $this->currentBlock;
        }
		return @$block["V:$variable"];
	}
	
	//!-----------------------------------------------------------------
	// @function	Template::createBlock
	// @desc		Cria uma instância do bloco $block
	// @note		O bloco criado passará a ser o bloco ativo na classe. Isto significa que
	//				todas as atribuições simples de variáveis serão aplicadas a ele
	// @param 		block string	Nome do bloco a ser criado
	// @access 		public 	
	// @return		void	
	// @see 		Template::setCurrentBlock
	//!-----------------------------------------------------------------
	function createBlock($block) {
		if (!isset($this->Parser->tplDef[$block]))
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CANT_FIND_BLOCK', $block), E_USER_ERROR, __FILE__, __LINE__);
		if ($block == TP_ROOTBLOCK)
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CANT_REPLICATE_ROOT_BLOCK'), E_USER_ERROR, __FILE__, __LINE__);
		$parent =& $this->_findBlock($this->Parser->blockParent[$block]);
		if (!isset($parent["B:$block"])) {
			// nenhuma instância do block $block foi criada no bloco pai até o momento
			// o índice de utilização do bloco é incrementado, e uma referência à sua localização é registrada no bloco pai
			$this->Parser->blockIndex[$block]++;
			$index = "$block:" . $this->Parser->blockIndex[$block];
			if (!isset($this->content[$index]))
				$this->content[$index] = array();
			$parent["B:$block"] = $index;
		} else {
			// já existe ao menos uma instância do bloco $block registrada, busca o índice atual
			$index = "$block:" . $this->Parser->blockIndex[$block];
		}
		// cria uma nova cópia do bloco
		$size = sizeof($this->content[$index]);
		$this->content[$index][$size] = array('blockName' => $block);
		$this->currentBlock =& $this->content[$index][$size];		
	}	

	//!-----------------------------------------------------------------
	// @function	Template::setCurrentBlock
	// @desc 		Move o ponteiro do bloco ativo para o bloco indicado pelo nome $block
	// @note		Um erro será gerado se o bloco solicitado não estiver definido
	// @param 		block string	Nome do bloco
	// @access 		public 	
	// @return		void	
	// @see 		Template::createBlock
	//!-----------------------------------------------------------------
	function setCurrentBlock($block) {
		if (!isset($this->Parser->tplDef[$block]))
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CANT_FIND_BLOCK', $block), E_USER_ERROR, __FILE__, __LINE__);
		$this->currentBlock =& $this->_findBlock($block);
	}

	//!-----------------------------------------------------------------
	// @function	Template::createAndAssign
	// @desc		Atalho para criação de uma instância de bloco e atribuição de variáveis à instância criada
	// @param		blockName string	Nome do bloco a ser criado
	// @param		variable mixed		Nome da variável ou vetor de substituições
	// @param		value mixed			"" Valor para a variável, se for simples
	// @note		A semântica dos parâmetros $variable e $value é a mesma do método Template::assign
	// @access		public	
	// @return		void
	// @see			Template::assign
	// @see			Template::globalAssign
	// @see			Template::includeAssign
	//!-----------------------------------------------------------------	
	function createAndAssign($blockName, $variable, $value='') {
		$this->createBlock($blockName);
		$this->assign($variable, $value);
	}

	//!-----------------------------------------------------------------
	// @function	Template::assign
	// @desc 		Atribui valor a uma variável de um bloco do template
	// @note		Aceita um array associativo no parâmetro $variable para atribuir múltiplas variáveis	
	// @note 		A variável, além de poder ser representada por um array associativo,
	//				pode referenciar-se a uma variável do bloco ativo ou usando referência
	//				explícita para um bloco utilizando a sintaxe bloco.variavel
	// @param 		variable mixed		Variável ou variáveis para substituição
	// @param 		value mixed			"" Valor que deverá ser associado à variável
	// @access 		public 	
	// @return		void	
	// @see			Template::createAndAssign
	// @see 		Template::globalAssign	
	// @see			Template::includeAssign
	//!-----------------------------------------------------------------
	function assign($variable, $value='') {
		if (TypeUtils::isArray($variable)) {
			foreach ($variable as $name => $value)
				$this->_assign($name, $value);
		} else {
			$this->_assign($variable, $value);
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	Template::globalAssign
	// @desc		Adiciona uma variável global no template
	// @note		Aceita um array associativo no parâmetro $variable para incluir múltiplas variáveis
	// @param 		variable string		Nome da variável global ou vetor de variáveis globais com seus valores
	// @param 		value string		"" Valor para a variável global
	// @access 		public	
	// @return		void	
	// @see			Template::createAndAssign
	// @see 		Template::assign
	// @see			Template::includeAssign
	//!-----------------------------------------------------------------
	function globalAssign($variable, $value='') {
		if (TypeUtils::isArray($variable)) {
			foreach ($variable as $name => $value)
				$this->_globalAssign($name, $value);
		} else {
			$this->_globalAssign($variable, $value);
		}
	}

	//!-----------------------------------------------------------------
	// @function	Template::includeAssign
	// @desc		Define o valor de um bloco de inclusão definido no template
	// @note		As inclusões de scripts devem ser executadas antes da execução do método Template::parse()	
	// @note		Ao utilizar o método includeAssign para atribuir valor a uma inclusão de 
	//				script (diretiva INCLUDESCRIPT), e desejar utilizar o tipo T_BYVAR, inclua 
	//				os caracteres &lt;? e ?&gt; no início e no final da string	
	// @param		blockName string	Nome do bloco de inclusão
	// @param		value string		Caminho completo para o arquivo de inclusão (T_BYFILE) ou conteúdo string (T_BYVAR)
	// @param		type int			"T_BYFILE" Tipo de inclusão
	// @access		public	
	// @return		void
	// @see			Template::createAndAssign
	// @see 		Template::assign
	// @see			Template::globalAssign
	//!-----------------------------------------------------------------
	function includeAssign($blockName, $value, $type=T_BYFILE) {
		if (!empty($value) && ($type == T_BYFILE || $type == T_BYVAR)) {
			if ($type == T_BYFILE && !is_readable($value))
				PHP2Go::raiseError(PHP2Go::getLangVal('ERR_CANT_READ_FILE', $value), E_USER_ERROR, __FILE__, __LINE__);			
			$this->Parser->tplInclude[$blockName] = array($value, $type);
		}
	}
		
	//!-----------------------------------------------------------------
	// @function	Template::getContent
	// @desc		Monta e retorna o conteúdo HTML do template
	// @note		Não é possível imprimir o conteúdo de um template cujo nome de arquivo é vazio ou cujo conteúdo é vazio	
	// @access 		public 
	// @return		string Código HTML resultante
	// @see 		Template::display	
	//!-----------------------------------------------------------------
	function getContent() {
		if (!$this->isPrepared()) {
			if (empty($this->Parser->tplBase[0])) {
				// conteúdo vazio
				if ($this->Parser->tplBase[1] == T_BYVAR)
					PHP2Go::raiseError(PHP2Go::getLangVal('ERR_EMPTY_TEMPLATE_CONTENT'), E_USER_ERROR, __FILE__, __LINE__);
				// arquivo vazio
				else
					PHP2Go::raiseError(PHP2Go::getLangVal('ERR_EMPTY_TEMPLATE_FILE'), E_USER_ERROR, __FILE__, __LINE__);
			}
			PHP2Go::raiseError(PHP2Go::getLangVal('ERR_TEMPLATE_NOT_PREPARED', array(($this->Parser->tplBase[1] == T_BYFILE ? $this->Parser->tplBase[0] : ''), get_class($this))), E_USER_ERROR, __FILE__, __LINE__);
		} else {		
			return $this->_getContent(TP_ROOTBLOCK . ":0");
		} 
	}
	
	//!-----------------------------------------------------------------
	// @function	Template::display
	// @desc		Monta a envia para a saída padrão o conteúdo HTML do template
	// @note		Não é possível imprimir o conteúdo de um template cujo nome de arquivo é vazio ou cujo conteúdo é vazio
	// @access 		public 
	// @return		void	
	// @see 		Template::getContent
	//!-----------------------------------------------------------------
	function display() {	
		$content = $this->getContent();
		print $content;
	}
	
	//!-----------------------------------------------------------------
	// @function	Template::_assign
	// @desc		Método interno de atribuição de valores a variáveis
	// @param		variable string		Referência para a variável
	// @param		value mixed			Valor de atribuição
	// @access		private	
	// @return		void
	//!-----------------------------------------------------------------
	function _assign($variable, $value) {
        if (sizeof($regs = explode('.', $variable)) == 2) {
			if (isset($this->Parser->tplDef[$regs[0]])) {
				$block =& $this->_findBlock($regs[0]);
				$variable = $regs[1];
			}
        } else {
            $block =& $this->currentBlock;
        }
        $block["V:$variable"] = $value;		
	}
	
	//!-----------------------------------------------------------------
	// @function	Template::_globalAssign
	// @desc		Método interno de registro de variáveis globais
	// @param		variable string		Nome da variável
	// @param		value mixed			Valor
	// @access		private	
	// @return		void
	//!-----------------------------------------------------------------
	function _globalAssign($variable, $value) {
		$this->globalVars[$variable] = $value;
	}
	
	//!-----------------------------------------------------------------
	// @function	Template::_getContent
	// @desc		Método recursivo de construção do conteúdo HTML do template
	// @param		blockName string	Índice de bloco
	// @access		private
	//!-----------------------------------------------------------------
	function _getContent($blockName) {
		$buffer = '';
		$total = sizeof($this->content[$blockName]);
		for ($i=0; $i<$total; $i++) {
			$defName = $this->content[$blockName][$i]['blockName'];
			for (reset($this->Parser->tplDef[$defName]); $k=key($this->Parser->tplDef[$defName]); next($this->Parser->tplDef[$defName])) {
				if ($k{0} == 'C') {
					$buffer .= $this->Parser->tplDef[$defName][$k];
				} elseif ($k{0} == 'V') {					
					$defVar = $this->Parser->tplDef[$defName][$k];
					// algum valor foi atribuído?
					if (!isset($this->content[$blockName][$i]["V:$defVar"])) {
						// existe uma variável global com o nome $defVar?
						if (isset($this->globalVars[$defVar])) {
							$value = $this->globalVars[$defVar];
						// variáveis não atribuídas devem ser exibidas?
						} elseif ($this->showUnAssigned) {
							$value = '{' . $defVar . '}';
						} else {
							$value = '';
						}
					} else {
						if (!empty($this->Parser->varModifiers[$k])) {
							$value = $this->content[$blockName][$i]["V:$defVar"];
							foreach ($this->Parser->varModifiers[$k] as $name => $args)
								eval("\$value = \$this->_modifierHandler(\$args[0], \$value{$args[1]});");
						} else {
							$value = $this->content[$blockName][$i]["V:$defVar"];
						}
					}
					$buffer .= $value;
				} elseif ($k{0} == 'B') {
					// busca a chave do bloco no índice e executa a chamada recursiva
					if (isset($this->content[$blockName][$i][$k]))
						$buffer .= $this->_getContent($this->content[$blockName][$i][$k]);
				}
			}
		}
		return $buffer;
	}
	
	//!-----------------------------------------------------------------
	// @function	Template::_modifierHandler
	// @desc		Executa a chamada para um modificador de valor de variável
	// @access		private
	// @note		Este método recebe como argumentos a callback do modificador,
	//				o valor a ser transformado e os argumentos
	// @return		mixed Retorno gerado pelo modificador
	//!-----------------------------------------------------------------
	function _modifierHandler() {		
		$args = func_get_args();
		$func = array_shift($args);
		return call_user_func_array($func, $args);
	}

	//!-----------------------------------------------------------------
	// @function	Template::_initializeContent
	// @desc		Inicializa a estrutura de conteúdo do template
	// @access		private
	// @return		void
	//!-----------------------------------------------------------------
	function _initializeContent() {
		$this->content = array(
			TP_ROOTBLOCK . ":0" => array(
				array('blockName' => TP_ROOTBLOCK)
			)
		);
		$this->currentBlock =& $this->content[TP_ROOTBLOCK . ":0"][0];
	}
	
	//!-----------------------------------------------------------------
	// @function	Template::&_findBlock
	// @desc		Para um determinado nome de bloco, busca a última instância criada,
	//				considerando o índice ativo do bloco
	// @access		private
	// @param		blockName string	Nome do bloco
	// @return		array Referência para a instância mais recente
	//!-----------------------------------------------------------------
	function &_findBlock($blockName) {
		$index = "$blockName:" . $this->Parser->blockIndex[$blockName];		
		$lastItem = sizeof($this->content[$index]);
		$lastItem = ($lastItem > 1 ? $lastItem-1 : 0);
		return $this->content[$index][$lastItem];
	}
}
?>