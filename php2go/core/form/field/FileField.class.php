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
// $Header: /www/cvsroot/php2go/core/form/field/FileField.class.php,v 1.17 2005/06/27 22:39:05 mpont Exp $
// $Date: 2005/06/27 22:39:05 $

//------------------------------------------------------------------
import('php2go.form.field.FormField');
//------------------------------------------------------------------

//!-----------------------------------------------------------------
// @class		FileField
// @desc		Esta classe constrói um campo de formulário do tipo FILE,
//				para upload de arquivos gravados na máquina do usuário
// @package		php2go.form.field
// @extends		FormField
// @author		Marcos Pont
// @version		$Revision: 1.17 $
//!-----------------------------------------------------------------
class FileField extends FormField
{
	//!-----------------------------------------------------------------
	// @function	FileField::FileField
	// @desc		Construtor da classe FileField, inicializa os atributos do campo
	// @access		public
	// @param		&Form Form object	Formulário no qual o campo é inserido
	//!-----------------------------------------------------------------
	function FileField(&$Form) {
		parent::FormField($Form);
		$this->htmlType = 'FILE';
		$this->searchable = FALSE;
	}
	
	//!-----------------------------------------------------------------
	// @function	FileField::getCode
	// @desc		Monta o código HTML do campo
	// @access		public
	// @return		string Código HTML do campo
	//!-----------------------------------------------------------------
	function getCode() {
		parent::onPreRender();
		$this->htmlCode = sprintf("<INPUT TYPE=\"file\" ID=\"%s\" NAME=\"%s\" SIZE=\"%s\" TITLE=\"%s\"%s%s%s%s%s%s%s>",
			$this->name, $this->name, $this->attributes['SIZE'], $this->label, $this->attributes['SCRIPT'], 
			$this->attributes['TABINDEX'], $this->attributes['STYLE'], $this->attributes['READONLY'], 
			$this->attributes['DISABLED'], $this->attributes['DATASRC'], $this->attributes['DATAFLD']);
		return $this->htmlCode;
	}
	
	//!-----------------------------------------------------------------
	// @function	FileField::getValue
	// @desc		Sobrescreve o método getValue da classe FormField
	// @access		public
	// @return		string Valor do campo
	// @note		O valor de um campo do tipo FileField é buscado do vetor global $_FILES
	//!-----------------------------------------------------------------
	function getValue() {
		if (empty($_FILES) || !isset($_FILES[$this->getName()]))
			return '';
		return $_FILES[$this->getName()]['name'];
	}
	
	//!-----------------------------------------------------------------
	// @function	FileField::setSize
	// @desc		Altera ou define o tamanho do campo
	// @access		public
	// @param		size int	Tamanho para o campo
	// @return		void
	//!-----------------------------------------------------------------
	function setSize($size) {
		$this->attributes['SIZE'] = TypeUtils::parseInteger($size);
	}
	
	//!-----------------------------------------------------------------
	// @function	FileField::setMaxFileSize
	// @desc		Define o tamanho máximo permitido para o upload do arquivo
	// @access		public
	// @param		maxSize string	Tamanho máximo para o arquivo
	// @return		void
	// @note		Este atributo aceita valores no padrão 500K, 2M ou números inteiros	
	//!-----------------------------------------------------------------
	function setMaxFileSize($maxSize) {
		if (!empty($maxSize))
			$this->attributes['MAXFILESIZE'] = $maxSize;
	}
	
	//!-----------------------------------------------------------------
	// @function	FileField::setAllowedTypes
	// @desc		Define os tipos mime aceitos para o upload deste arquivo
	// @access		public
	// @param		types string	Lista de tipos mime aceitos
	// @return		void
	// @note		O parâmetro deve ser uma lista de tipos mime separados por vírgula
	//!-----------------------------------------------------------------
	function setAllowedTypes($types) {
		if (!empty($types)) {
			$types = explode(',', TypeUtils::parseString($types));
			$this->attributes['ALLOWEDTYPES'] = $types;
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	FileField::setSaveFunction
	// @desc		Define a função customizada de gravação do arquivo
	// @access		public
	// @param		function mixed	Função, método estático ou dinâmico a ser utilizado
	// @return		void
	//!-----------------------------------------------------------------
	function setSaveFunction($function) {
		if (!empty($function))
			$this->attributes['SAVEFUNCTION'] = $function;
		else
			$this->attributes['SAVEFUNCTION'] = NULL;
	}
	
	//!-----------------------------------------------------------------
	// @function	FileField::setSavePath
	// @desc		Define o caminho onde o arquivo deve ser salvo
	// @access		public
	// @param		path string		Caminho relativo (em relação ao script atual)
	// @return		void	
	//!-----------------------------------------------------------------
	function setSavePath($path) {
		if (!empty($path))
			$this->attributes['SAVEPATH'] = $path;
		else
			$this->attributes['SAVEPATH'] = getcwd();
	}
	
	//!-----------------------------------------------------------------
	// @function	FileField::setSaveName
	// @desc		Seta o nome de gravação do arquivo
	// @access		public
	// @param		name string		Nome de gravação do arquivo de upload
	// @return		void
	// @note		Este atributo aceita variáveis no padrão ~variavel~
	//!-----------------------------------------------------------------
	function setSaveName($name) {
		if (!empty($name))
			$this->attributes['SAVENAME'] = $name;
		else
			$this->attributes['SAVENAME'] = '';
	}
	
	//!-----------------------------------------------------------------
	// @function	FileField::setSaveMode
	// @desc		Seta o modo de criação do arquivo de upload
	// @access		public
	// @param		mode int		Modo de gravação do arquivo (Ex: 0755)
	// @return		void
	//!-----------------------------------------------------------------
	function setSaveMode($mode) {
		if (!empty($mode)) {
			$mode = ereg_replace("[^0-9]+", "", TypeUtils::parseString($mode));
			eval("\$this->attributes['SAVEMODE'] = {$mode};");
		}
	}
	
	//!-----------------------------------------------------------------
	// @function	FileField::setOverwrite
	// @desc		Habilita ou impede que arquivos existentes sejam sobrescritos na operação de upload
	// @access		public
	// @param		overwrite bool	Habilitar ou desabilitar sobrescrita
	// @return		void
	//!-----------------------------------------------------------------
	function setOverwrite($overwrite) {
		$this->attributes['OVERWRITE'] = TypeUtils::toBoolean($overwrite);
	}
	
	//!-----------------------------------------------------------------
	// @function	FileField::isValid
	// @desc		Este método permite validar e executar a operação completa
	//				de upload do arquivo, se o POST do form for tratado utilizando
	//				os métodos isPosted() e isValid()
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function isValid() {
		$result = parent::isValid();
		if ($this->attributes['ONVALIDATE'] === TRUE) {
			$attrs = $this->attributes;
			$attrs['FIELDNAME'] = $this->getName();
			$result &= Validator::validate('php2go.validation.UploadValidator', $attrs);
			// define o handler de upload (dados do arquivo original e do destino) como o valor submetido para o campo
			$Uploader =& FileUpload::getInstance();
			if ($handler = $Uploader->getHandlerByName($this->getName()))
				parent::setSubmittedValue($handler);
		}
		return TypeUtils::toBoolean($result);
	}
	
	//!-----------------------------------------------------------------
	// @function	FileField::onLoadNode
	// @desc		Método responsável por processar atributos e nodos filhos
	//				provenientes da especificação XML do campo
	// @access		protected
	// @param		attrs array		Atributos do nodo
	// @param		children array	Vetor de nodos filhos
	// @return		void
	//!-----------------------------------------------------------------
	function onLoadNode($attrs, $children) {
		parent::onLoadNode($attrs, $children);
		// habilita upload no formulário
		$this->_Form->hasUpload = TRUE;
		// tamanho do campo
		// 1) atributo SIZE
		if (isset($attrs['SIZE']) && TypeUtils::isInteger($attrs['SIZE']))
			$this->setSize($attrs['SIZE']);
		// 2) atributo LENGTH
		elseif (isset($attrs['LENGTH']) && TypeUtils::isInteger($attrs['LENGTH']))
			$this->setSize($attrs['LENGTH']);
		// 3) constante da classe
		else
			$this->setSize(15);
		// tamanho máximo de arquivo
		$this->setMaxFileSize(@$attrs['MAXFILESIZE']);
		// tipos MIME permitidos
		$this->setAllowedTypes(@$attrs['ALLOWEDTYPES']);
		// callback para gravação do arquivo
		$this->setSaveFunction(@$attrs['SAVEFUNCTION']);
		// caminho de gravação do arquivo
		$this->setSavePath(@$attrs['SAVEPATH']);
		// nome de gravação do arquivo
		$name = @$attrs['SAVENAME'];
		if (ereg("~[^~]+~", $name))
			$name = Statement::evaluate($name);
		$this->setSaveName($name);
		// modo de gravação do arquivo
		$this->setSaveMode(@$attrs['SAVEMODE']);
		// sobrescrita de arquivos existentes
		if (isset($attrs['OVERWRITE']))
			$this->setOverwrite(Form::resolveBooleanChoice($attrs['OVERWRITE']));
		// upload na validação
		if (isset($attrs['UPLOADONVALIDATE']))
			$this->attributes['ONVALIDATE'] = Form::resolveBooleanChoice($attrs['UPLOADONVALIDATE']);
		else
			$this->attributes['ONVALIDATE'] = TRUE;		
	}
}
?>