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
// $Header: /www/cvsroot/php2go/core/form/FormEventListener.class.php,v 1.2 2005/06/17 19:33:54 mpont Exp $
// $Date: 2005/06/17 19:33:54 $

// @const FORM_EVENT_JS "JS"
// Eventos que executam funчѕes JavaScript
define('FORM_EVENT_JS', 'JS');
// @const FORM_EVENT_JSRS "JSRS"
// Eventos que executam funчѕes localizadas em scripts PHP utilizando JSRS
define('FORM_EVENT_JSRS', 'JSRS');

//!-----------------------------------------------------------------
// @class		FormEventListener
// @desc		A classe FormEventListener armazena os dados dos tratadores de eventos
//				associados a campos e botѕes de formulсrios. Estes eventos podem ser simples
//				chamadas de funчѕes JavaScript ou chamadas de funчѕes armazenadas em outros scripts
//				PHP. Neste segundo caso, a biblioteca JSRS (JavaScript Remote Scripting) щ utilizada
//				para buscar o retorno da funчуo e devolvъ-la como parтmetro de uma funчуo de callback.
// @package		php2go.form
// @extends		PHP2Go
// @uses		TypeUtils
// @author		Marcos Pont
// @version		$Revision: 1.2 $
//!-----------------------------------------------------------------
class FormEventListener extends PHP2Go
{
	var $type;				// @var type string				Tipo do tratador
	var $eventName;			// @var eventName string		Nome do evento
	var $action;			// @var action string			Aчуo a ser executada (somente JS)
	var $remoteFile;		// @var remoteFile string		Arquivo PHP remoto que contщm a funчуo a ser executada (somente JSRS)
	var $remoteFunction;	// @var remoteFunction string	Nome da funчуo remota a ser executada (somente JSRS)
	var $callback;			// @var callback string			Funчуo JavaScript que deve tratar o retorno da requisiчуo remota (somente JSRS)
	var $params;			// @var params string			String da parтmetros para a funчуo remota, definida em Javascript (somente JSRS)
	var $autoDispatchIf;	// @var autoDispatchIf string	Uma expressуo em Javascript que, se for avaliada para true, irс disparar automaticamente o evento no carregamento da pсgina
	var $debug;				// @var debug bool				"FALSE" Habilita/desabilita debug nos eventos do tipo JSRS	
	var $_valid = FALSE;	// @var _valid bool				"FALSE" Indica que as propriedades do listener sуo vсlidas
	var $_Owner = NULL;		// @var _Owner object			"NULL" Campo ou botуo ao qual o listener estс associado
	var $_ownerIndex;		// @var _ownerIndex int			Эndice da opчуo р qual o listener pertence (RadioField, CheckGroup)
	
	//!-----------------------------------------------------------------
	// @function	FormEventListener::FormEventListener
	// @desc		Construtor da classe
	// @access		public
	// @param		type string				Tipo do tratador
	// @param		eventName string		Nome do evento JavaScript
	// @param		action string			"" Aчуo a ser executada
	// @param		remoteFile string		"" Arquivo remoto
	// @param		remoteFunction string	"" Funчуo remota
	// @param		callback string			"" Funчуo de tratamento do retorno
	// @param		params string			"" Conjunto de parтmetros
	// @param		autoDispatchIf string	"" Expressуo que define se o evento щ disparado automaticamente ou nуo
	// @param		debug bool				"FALSE" Debug do retorno da funчуo remota, somente para listeners do tipo FORM_EVENT_JSRS
	//!-----------------------------------------------------------------
	function FormEventListener($type, $eventName, $action='', $remoteFile='', $remoteFunction='', $callback='', $params='', $autoDispatchIf='', $debug=FALSE) {
		$this->type = $type;
		$this->eventName = $eventName;
		$this->action = $action;
		$this->remoteFile = $remoteFile;
		$this->remoteFunction = $remoteFunction;
		$this->callback = $callback;
		$this->params = $params;
		$this->autoDispatchIf = $autoDispatchIf;
		$this->debug = TypeUtils::toBoolean($debug);
	}
	
	//!-----------------------------------------------------------------
	// @function	FormEventListener::&fromNode
	// @desc		Este mщtodo factory cria uma instтncia da classe
	//				FormEventListener a partir de um nodo XML do tipo
	//				LISTENER, utilizado para definir tratamento de eventos
	//				para campos e botѕes de formulсrios
	// @access		public
	// @param		Node XmlNode object	Nodo da regra na especificaчуo XML
	// @return		FormEventListener object
	// @static
	//!-----------------------------------------------------------------
	function &fromNode($Node) {
		$type = trim($Node->getAttribute('TYPE'));
		$eventName = trim($Node->getAttribute('EVENT'));
		$action = trim($Node->getAttribute('ACTION'));
		$remoteFile = trim($Node->getAttribute('FILE'));
		$remoteFunction = trim($Node->getAttribute('REMOTE'));
		$callback = trim($Node->getAttribute('CALLBACK'));
		$params = trim($Node->getAttribute('PARAMS'));
		$autoDispatchIf = trim($Node->getAttribute('AUTODISPATCHIF'));
		$debug = Form::resolveBooleanChoice($Node->getAttribute('DEBUG'));
		return new FormEventListener($type, $eventName, $action, $remoteFile, $remoteFunction, $callback, $params, $autoDispatchIf, $debug);
	}
	
	//!-----------------------------------------------------------------
	// @function	FormEventListener::setOwner
	// @desc		Define o campo ou botуo ao qual o tratador de evento estс associado
	// @access		public
	// @param		&Owner object	Campo ou botуo
	// @param		ownerIndex int	"NULL" Эndice da opчуo р qual o listener pertence (RadioField, CheckGroup)
	// @return		void
	//!-----------------------------------------------------------------
	function setOwner(&$Owner, $ownerIndex=NULL) {
		$this->_Owner =& $Owner;
		$this->_ownerIndex = $ownerIndex;
	}
	
	//!-----------------------------------------------------------------
	// @function	FormEventListener::getScriptCode
	// @desc		Monta o cѓdigo JavaScript de definiчуo do tratador
	// @access		public
	// @param		targetIndex int		"NULL" Эndice de um grupo de opчѕes
	// @return		string Cѓdigo JS da aчуo a ser executada
	//!-----------------------------------------------------------------
	function getScriptCode($targetIndex=NULL) {
		if (TypeUtils::isInstanceOf($this->_Owner, 'formfield') || TypeUtils::isInstanceOf($this->_Owner, 'formbutton')) {
			$Form =& $this->_Owner->getOwnerForm();
			// inclusуo do client JSRS
			if ($this->type == FORM_EVENT_JSRS)
				$Form->Document->addScript(PHP2GO_JAVASCRIPT_PATH . 'jsrsclient.js');
			// inclusуo da chamada de auto execuчуo no evento onLoad do documento
			if (!empty($this->autoDispatchIf)) {
				if (isset($this->ownerIndex)) {
					$dispatchTest = str_replace("this", "getDocumentObject('" . $this->_Owner->getName() . "_{$this->_ownerIndex}')", $this->autoDispatchIf);
					$dispatchAction = str_replace("this", "getDocumentObject('" . $this->_Owner->getName() . "_{$this->_ownerIndex}')", $this->action);
				} elseif (!TypeUtils::isNull($targetIndex, TRUE)) {
					$dispatchTest = str_replace("this", "getDocumentObject('" . $this->_Owner->getName() . "_{$targetIndex}')", $this->autoDispatchIf);
					$dispatchAction = str_replace("this", "getDocumentObject('" . $this->_Owner->getName() . "_{$targetIndex}')", $this->action);
				} else {
					$dispatchTest = str_replace("this", "document.{$Form->formName}.elements['" . $this->_Owner->getName() . "']", $this->autoDispatchIf);
					$dispatchAction = str_replace("this", "document.{$Form->formName}.elements['" . $this->_Owner->getName() . "']", $this->action);
				}
				$Form->Document->addOnloadCode(sprintf("     if (%s){\n          %s;\n     }", $dispatchTest, $dispatchAction), 'JavaScript');
			}
			return $this->action;
		}
		return '';			
	}
	
	//!-----------------------------------------------------------------
	// @function	FormEventListener::isValid
	// @desc		Verifica se os dados do tratador de evento sуo vсlidos
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function isValid() {
		if ($this->_valid == TRUE)
			return $this->_valid;
		if (TypeUtils::isInstanceOf($this->_Owner, 'formfield') || TypeUtils::isInstanceOf($this->_Owner, 'formbutton')) {
			if ($this->type == FORM_EVENT_JS) {
				if (empty($this->eventName) || empty($this->action)) {
					$this->_valid = FALSE;
				} else {
					$this->action = ereg_replace(";[ ]*$", "", $this->action);
					$this->_valid = TRUE;
				}
			} elseif ($this->type == FORM_EVENT_JSRS) {
				if (empty($this->eventName) || empty($this->remoteFile) || empty($this->remoteFunction) || empty($this->callback)) {
					$this->_valid = FALSE;
				} else {
					$this->action = sprintf("jsrsExecute('%s', %s, '%s', %s%s);window.status=''", 
						$this->remoteFile, $this->callback, $this->remoteFunction,
						(empty($this->params) ? 'null' : $this->params), 
						($this->debug ? ', true' : '')
					);
					$this->_valid = TRUE;
				}
			} else {
				$this->_valid = FALSE;
			}
			if (!$this->_valid)
				PHP2Go::raiseError(PHP2Go::getLangVal('ERR_FORM_WRONG_LISTENER', $this->_getListenerInfo()), E_USER_ERROR, __FILE__, __LINE__);
			return $this->_valid;			
		} else {
			$this->_valid = FALSE;
			return $this->_valid;
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	FormRule::_getListenerInfo
	// @desc		Monta informaчѕes do listener, para exibiчуo de mensagens de erro
	// @access		private
	// @return		string Texto descritivo da regra
	//!-----------------------------------------------------------------
	function _getListenerInfo() {
		$info = $this->_Owner->getName();
		if (isset($this->_ownerIndex))
			$info .= " [option {$this->_ownerIndex}]";
		$info .= " - [{$this->type}";
		if (!empty($this->eventName))
			$info .= "; {$this->eventName}";
		if (!empty($this->action))
			$info .= "; {$this->action}";
		if (!empty($this->remoteFile))
			$info .= "; {$this->remoteFile}";
		if (!empty($this->remoteFunction))
			$info .= "; {$this->remoteFunction}";
		if (!empty($this->callback))
			$info .= "; {$this->callback}";			
		if (!empty($this->params))
			$info .= "; {$this->params}";			
		$info .= ']';
		return $info;
	}	
}
?>