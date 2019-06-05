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
// $Header: /www/cvsroot/php2go/resources/javascript/modules/visibilitycontrol.js,v 1.8 2005/08/30 14:41:31 mpont Exp $
// $Date: 2005/08/30 14:41:31 $
// $Revision: 1.8 $

//!--------------------------------------------------------------
// @function	VisibilityControl
// @desc		Construtor do objeto VisibilityControl
// @return		VisibilityControl object
//!--------------------------------------------------------------
function VisibilityControl() {
	this.hash = new Array();
	this.affected = new Array();	
}
VisibilityControl.affected = new Array();

//!--------------------------------------------------------------
// @function	findObject
// @desc		Função utilitária para armazenar os objetos já processados
// @param		objId String	ID do objeto
// @return		object Referência para o objeto ou null
//!--------------------------------------------------------------
VisibilityControl.prototype.findObject = function(objId){
	if(!this.hash[objId])
		this.hash[objId]=document.getElementById(objId);
	return this.hash[objId];
};

//!--------------------------------------------------------------
// @function	show
// @desc		Torna um determinado objeto visível
// @param		objId String		ID do objeto
// @param		hideCovrd Boolean	Esconder os elementos que conflitam com o posicionamento do objeto
// @return		void
//!--------------------------------------------------------------
VisibilityControl.prototype.show = function(objId, hideCovrd, centralize) {
	o = this.findObject(objId);
	if (o != null) {
		if (centralize == true) {
			x = (Math.floor(getWindowWidth()/2)-Math.floor(o.offsetWidth/2)) + getWinXOffset();
			y = (Math.floor(getWindowHeight()/2)-Math.floor(o.offsetHeight/2)) + getWinYOffset();
			moveDivTo(o,x,y);
		} else {
			x = o.offsetLeft;
			y = o.offsetTop;
		}
		setDivVisibility(o, true);
		if (hideCovrd == true && o.offsetWidth && o.offsetHeight)
			VisibilityControl.affected[objId] = hideCoveredElements(x, x+o.offsetWidth, y, y+o.offsetHeight);
	}
};

//!--------------------------------------------------------------
// @function	showAtPosition
// @desc		Torna um determinado elemento visível em uma dada posição
// @param		objId String		ID do objeto
// @param		x Integer			Posição x
// @param		y Integer			Posição y
// @param		hideCovered Boolean	Esconder os elementos que conflitam com o posicionamento do objeto
// @return		void
//!--------------------------------------------------------------
VisibilityControl.prototype.showAtPosition = function(objId, x, y, hideCovered) {
	o = this.findObject(objId);
	if (o != null) {
		moveDivTo(o, x, y);
		setDivVisibility(o, true);
		if (hideCovered == true && o.offsetWidth && o.offsetHeight)
			VisibilityControl.affected[objId] = hideCoveredElements(x, x+o.offsetWidth, y, y+o.offsetHeight);
	}
};

//!--------------------------------------------------------------
// @function	hide
// @desc		Torna um determinado objeto invisível
// @param		objId String		ID do objeto
// @return		void
//!--------------------------------------------------------------
VisibilityControl.prototype.hide = function(objId) {
	o=this.findObject(objId);
	if(o!=null){
		setDivVisibility(o,false);
		if(VisibilityControl.affected[objId]){
			for(var i=0;i<VisibilityControl.affected[objId].length;i++){
				setDivVisibility(VisibilityControl.affected[objId][i],true);
			}
		}
	}
};