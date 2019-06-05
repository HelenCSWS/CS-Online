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
// $Header: /www/cvsroot/php2go/core/graph/shape/ImageCircle.class.php,v 1.1 2005/08/30 14:57:16 mpont Exp $
// $Date: 2005/08/30 14:57:16 $

//!-----------------------------------------------------------------
// @class		ImageCircle
// @desc		C�rculo, com coordenadas X e Y do centro e tamanho do raio
// @package		php2go.graph.shape
// @extends		ImageArc
// @author		Marcos Pont
// @version		$Revision: 1.1 $
//!-----------------------------------------------------------------
class ImageCircle extends ImageArc
{
	var $radius;	// @var radius int	Raio do c�rculo
	
	//!-----------------------------------------------------------------
	// @function	ImageCircle::ImageCircle
	// @desc		Construtor da classe
	// @access		public
	// @param		cx int				Coordenada X do centro
	// @param		cy int				Coordenada Y do centro
	// @param		radiu int			Raio do c�rculo
	// @param		fill bool			"FALSE" Possui ou n�o preenchimento
	// @param		shadow int			"0" Tamanho da sombra do c�rculo
	// @param		shadowColor mixed	"NULL" Cor da sombra
	//!-----------------------------------------------------------------
	function ImageCircle($cx, $cy, $radius, $fill=FALSE, $shadow=0, $shadowColor=NULL) {
		parent::ImageArc($cx, $cy, $radius*2, $radius*2, 0, 360, $fill, $shadow, $shadowColor);
		$this->radius = $radius;
	}	
}
?>