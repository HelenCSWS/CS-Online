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
// $Header: /www/cvsroot/php2go/core/form/field/CaptchaField.class.php,v 1.1 2005/08/30 15:00:03 mpont Exp $
// $Date: 2005/08/30 15:00:03 $

//-----------------------------------------
import('php2go.form.field.FormField');
import('php2go.graph.CaptchaImage');
//-----------------------------------------

//!-----------------------------------------------------------------
// @class		CaptchaField
// @desc		Constrói uma imagem de segurança (CAPTCHA) e um input
//				do tipo TEXT onde o usuário deve informar a palavra idêntica
//				à que aparece na imagem gerada
// @package		php2go.form.field
// @extends		FormField
// @uses		CaptchaImage
// @uses		TypeUtils
// @author		Marcos Pont
// @version		$Revision: 1.1 $
//!-----------------------------------------------------------------
class CaptchaField extends FormField
{
	var $Captcha = NULL;	// @var Captcha CaptchaImage object		"NULL" Configura e gera a imagem captcha
	var $imagePath;			// @var imagetPath string				Caminho onde a imagem deve ser salva
	var $imageType;			// @var imageType int					Tipo da imagem a ser gerada
	
	//!-----------------------------------------------------------------
	// @function	CaptchaField::CaptchaField
	// @desc		Construtor da classe
	// @access		public
	// @param		&Form Form object	Formulário no qual o campo é inserido
	//!-----------------------------------------------------------------
	function CaptchaField(&$Form) {
		parent::FormField($Form);
		$this->htmlType = 'TEXT';
	}
	
	//!-----------------------------------------------------------------
	// @function	CaptchaField::getCode
	// @desc		Monta o código HTML do campo
	// @access		public
	// @return		string Código HTML do campo
	//!-----------------------------------------------------------------
	function getCode() {
		parent::onPreRender();
		$this->htmlCode = sprintf("%s&nbsp;&nbsp;<INPUT TYPE=\"text\" ID=\"%s\" NAME=\"%s\" VALUE=\"\" MAXLENGTH=\"%s\" SIZE=\"%s\" TITLE=\"%s\" AUTOCOMPLETE=\"OFF\"%s%s%s%s%s>",
			(isset($this->imageType) ? $this->Captcha->buildHTML($this->imagePath, $this->imageType) : $this->Captcha->buildHTML($this->imagePath)),
			$this->name, $this->name, $this->attributes['LENGTH'], $this->attributes['SIZE'], $this->label, $this->attributes['SCRIPT'], 
			$this->attributes['TABINDEX'], $this->attributes['STYLE'], $this->attributes['READONLY'], $this->attributes['DISABLED']			
		);
		return $this->htmlCode;
	}
	
	//!-----------------------------------------------------------------
	// @function	CaptchaField::setSize
	// @desc		Altera ou define o tamanho do campo texto onde o texto da imagem deve ser reproduzido
	// @param		size int		Tamanho para o campo de texto
	// @access		public	
	// @return		void
	//!-----------------------------------------------------------------
	function setSize($size) {
		if (TypeUtils::isInteger($size))
			$this->attributes['SIZE'] = $size;
	}
	
	//!-----------------------------------------------------------------
	// @function	CaptchaField::setLength
	// @desc		Define número máximo de caracteres do campo
	// @param		length int		Define o tamanho da string captcha
	// @access		public	
	// @return		void
	//!-----------------------------------------------------------------
	function setLength($length) {
		if (TypeUtils::isInteger($length))
			$this->attributes['LENGTH'] = $length;
	}
	
	//!-----------------------------------------------------------------
	// @function	CaptchaField::setReadonly
	// @desc		Permite habilitar ou desabilitar o atributo de somente leitura do campo texto
	// @param		setting bool	"TRUE" Valor para o atributo, TRUE torna o campo texto somente leitura
	// @access		public	
	// @return		void
	//!-----------------------------------------------------------------
	function setReadonly($setting=TRUE) {
		if (TypeUtils::isTrue($setting)) {
			$this->attributes['READONLY'] = " READONLY";
			$this->readOnly = TRUE;
		} else {
			$this->attributes['READONLY'] = "";
			$this->readOnly = FALSE;
		}
	}

	//!-----------------------------------------------------------------
	// @function	CaptchaField::isValid
	// @desc		Sobrecarrega o método isValid da classe FormField para executar
	//				a validação do valor informado contra o conteúdo da mensagem captcha
	// @access		public
	// @return		bool
	//!-----------------------------------------------------------------
	function isValid() {
		$result = parent::isValid();
		$verify = $this->Captcha->verify($this->value);
		if (!$verify)
			Validator::addError(PHP2Go::getLangVal('ERR_FORM_CAPTCHA', $this->label));
		$result &= $verify;
		return TypeUtils::toBoolean($result);
	}
	
	//!-----------------------------------------------------------------
	// @function	CaptchaField::onLoadNode
	// @desc		Processa atributos e nodos filhos provenientes da especificação XML do campo
	// @param		attrs array		Atributos do nodo
	// @param		children array	Vetor de nodos filhos
	// @access		protected	
	// @return		void
	//!-----------------------------------------------------------------
	function onLoadNode($attrs, $children) {
		parent::onLoadNode($attrs, $children);
		// inicializa a imagem captcha
		$this->Captcha = new CaptchaImage($this->name . '_captcha');
		// tamanho do campo
		if (isset($attrs['SIZE']))
			$this->setSize($attrs['SIZE']);
		elseif (isset($attrs['LENGTH']))
			$this->setSize($attrs['LENGTH']);
		else
			$this->setSize($this->Captcha->textLength);
		// tamanho da string captcha
		if ($attrs['LENGTH']) {
			$this->setLength($attrs['LENGTH']);
			$this->Captcha->setTextLength($attrs['LENGTH']);
		} else {
			$this->setLength($this->attributes['SIZE']);
		}
		// somente leitura
		$this->setReadonly(Form::resolveBooleanChoice(@$attrs['READONLY']) || $this->_Form->readonly);
		// dimensões da imagem
		if ($attrs['WIDTH'])
			$this->Captcha->setWidth($attrs['WIDTH']);
		if ($attrs['HEIGHT'])
			$this->Captcha->setHeight($attrs['HEIGHT']);
		// nível de ruído
		if ($attrs['NOISELEVEL'])
			$this->Captcha->setNoiseLevel($attrs['NOISELEVEL']);
		// propriedades da fonte do texto
		if ($attrs['FONTSIZE'])
			$this->Captcha->setFontSize($attrs['FONTSIZE']);
		if ($attrs['FONTSHADOW'])
			$this->Captcha->setFontShadow($attrs['FONTSHADOW']);
		if ($attrs['FONTANGLE'])
			$this->Captcha->setFontAngle($attrs['FONTANGLE']);
		// caminho onde a imagem deve ser salva
		if ($attrs['IMAGEPATH'])
			$this->imagePath = $attrs['IMAGEPATH'];
		// tipo de imagem
		$type = @constant(@$attrs['IMAGETYPE']);
		if (!TypeUtils::isNull($type, TRUE))
			$this->imageType = $type;
	}
}
?>