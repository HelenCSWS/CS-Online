//
// +----------------------------------------------------------------------+
// | PHP2Go Web Development Framework                                     |
// +----------------------------------------------------------------------+
// | Copyright (c) Shinichi Hagiwara                                      |
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
// $Header: /www/cvsroot/php2go/resources/javascript/libs/div.js,v 1.10 2005/06/01 16:03:03 mpont Exp $
// $Date: 2005/06/01 16:03:03 $
// $Revision: 1.10 $

// Inicialização da biblioteca cross-browser
_mac=/Mac/i.test(navigator.userAgent);
_ie=/MSIE/i.test(navigator.userAgent);
_ie512=/MSIE 5.12/i.test(navigator.userAgent);
_khtml=/Konqueror|Safari|KHTML/i.test(navigator.userAgent);
_dom = document.all&&_ie?(document.getElementById?2:1):(document.getElementById?4:(document.layers?3:0));
_createLayerNo = 0;

//!--------------------------------------------------------------
// @function	getWindowWidth
// @desc		Busca a largura da janela atual
// @return		Integer Largura da janela atual
//!--------------------------------------------------------------
function getWindowWidth() {
	if(_dom==4 || _dom==3) return window.innerWidth;
	if(_dom==2 || _dom==1) return document.body.clientWidth;
	return 0;
}

//!--------------------------------------------------------------
// @function	getWindowHeight
// @desc		Busca a altura da janela atual
// @return		Integer Altura da janela atual
//!--------------------------------------------------------------
function getWindowHeight() {
	if(_dom==4 || _dom==3) return window.innerHeight;
	if(_dom==2 || _dom==1) return document.body.clientHeight;
	return 0;
}
//!--------------------------------------------------------------
// @function	getWinXOffset
// @desc		Busca o ponto X do topo da janela atual
// @return		Integer O extremo vertical da janela atual
//!--------------------------------------------------------------
function getWinXOffset() {
	if(_dom==4) return window.scrollX;
	if(_dom==2 || _dom==1) return document.body.scrollLeft;
	if(_dom==3) return window.pageXOffset;return 0;
}

//!--------------------------------------------------------------
// @function	getWinYOffset
// @desc		Busca o ponto Y mais à esquerda na janela atual
// @return		Integer O extremo horizontal da janela atual
//!--------------------------------------------------------------
function getWinYOffset() {
	if(_dom==4) return window.scrollY;
	if(_dom==2 || _dom==1) return document.body.scrollTop;
	if(_dom==3) return window.pageYOffset;
	return 0;
}

//!--------------------------------------------------------------
// @function	getDivFromName
// @desc		Retorna o elemento correspondente ao objeto
//				de nome 'nm' passado como parâmetro
// @param		nm String		Nome do objeto
// @return		Object Objeto encontrado
//!--------------------------------------------------------------
function getDivFromName(nm) {
	if(_dom==4 || _dom==2) return document.getElementById(nm);
	if(_dom==1) return document.all(nm);
	if(_dom==3) {
		var s='';
		for(var i=1; i<arguments.length; i++) s+='document.layers.'+arguments[i]+'.';
		return eval(s+'document.layers.'+nm);
	}
	return null;
}

//!--------------------------------------------------------------
// @function	getDivName
// @desc		Retorna o nome de um objeto
// @param		div Object	Objeto cujo nome é buscado
// @return		String Nome ou identificação do objeto
//!--------------------------------------------------------------
function getDivName(div) {
	if(_dom==4 || _dom==2 || _dom==1) return div.id;
	if(_dom==3) return div.name;
	return '';
}

//!--------------------------------------------------------------
// @function	createLayer
// @desc		Cria um elemento layer (DIV)
// @param		left Integer				Posição X para o elemento
// @param		top Integer					Posição Y para o elemento
// @param		width Integer				Largura para o elemento
// @param		height Integer				Altura para o elemento
// @param		parentDiv Object			Elemento pai do que será criado. Se não for fornecido, o pai será document.body
// @return		Object O elemento criado
// @note		A visibilidade (style.visibility) inicial do elemento é 'hidden'
//!--------------------------------------------------------------
function createLayer(left, top, width, height, parentDiv) {
	var s='';
	if(arguments.length>5) {
		for(var i=5; i<arguments.length; i++) s+=arguments[i];
	}
	if(_dom==4) {
		var divName= '_js_layer_'+_createLayerNo;
		_createLayerNo++;
		var pDiv   = parentDiv ? parentDiv:document.body;
		var div    = document.createElement('DIV');
		div.id     = divName;
		div.setAttribute('style','position:absolute;left:'+left+';top:'+top+(width >0?(';width:' +width ):'')+(height>0?(';height:'+height):'')+';visibility:hidden');
		var range=document.createRange();
		range.selectNodeContents(div);
		range.collapse(true);
		var cf=range.createContextualFragment(s);
		div.appendChild(cf);
		pDiv.appendChild(div);
		return div;
	}
	if(_dom==2 || _dom==1) {
		var adj    = (_mac&&!_ie512) ? ' ':'';
		var divName= '_js_layer_'+_createLayerNo;
		_createLayerNo++;
		var ha     = (height>0) ? (';height:'+height):'';
		var pDiv   = parentDiv ? parentDiv : document.body;
		pDiv.insertAdjacentHTML('BeforeEnd','<div id="'+divName+'" style="position:absolute;left:'+left+';top:'+top+(width >0?(';width:' +width ):';width:1')+(height>0?(';height:'+height):'')+';visibility:hidden;">'+s+'<\/div>'+adj);
		return document.all(divName);
	}
	if(_dom==3) {
		var div    = parentDiv ? (new Layer(width,parentDiv)) : (new Layer(width));
		if (height>0) div.resizeTo(width,height);
		div.moveTo(left,top);
		if (s!='') {
			div.document.open('text/html','replace');
			div.document.write(s);
			div.document.close();
		}
		return div;
	}
	return null;
}

//!--------------------------------------------------------------
// @function	createILayer
// @desc		Cria um elemento ilayer (ILAYER)
// @param		url String					URL interna da ilayer
// @param		left Integer				Posição X para o elemento
// @param		top Integer					Posição Y para o elemento
// @param		width Integer				Largura para o elemento
// @param		height Integer				Altura para o elemento
// @param		parentDiv Object			Elemento pai do que será criado. Se não for fornecido, padrão é document.body
// @return		Object ILAYER criado
//!--------------------------------------------------------------
function createILayer(url, left, top, width, height, parentDiv) {
	if(_dom==4) {
		var divName= '_js_layer_'+_createLayerNo;
		_createLayerNo++;
		var pDiv = parentDiv ? parentDiv : document.body;
		var div = document.createElement('IFRAME');
		div.id=divName;
		div.name=divName;
		div.setAttribute('style','position:absolute;left:'+left+';top:'+top+';width:'+width+(height>0?(';height:'+height):'')+';visibility:hidden');
		div.setAttribute('src',url);
		div.setAttribute('frameborder',0);
		div.setAttribute('scrolling','no');
		pDiv.appendChild(div);
		return div;
	}
	if(_dom==2 || _dom==1) {
		var adj    = (_mac&&_ie512) ? ' ':'';
		var bd, divName = '_js_layer_'+_createLayerNo;
		_createLayerNo++;
		var ha     = (height>0) ? (';height:'+height):'';
		if (arguments.length>5 && parentDiv)
			bd=parentDiv;
		else bd=document.body;
			bd.insertAdjacentHTML('BeforeEnd','<div id="'+divName+'" style="position:absolute;left:'+left+';top:'+top+';width:'+width+ha+';visibility:hidden;">'+'<iframe src="'+url+'" name="'+divName+'_if" '+'width='+width+' height='+height+'marginwidth=0 marginheight=0 '+'scrolling="no" frameborder="no">'+'<\/iframe>'+'<\/div>'+adj);
		return document.all(divName);
	}
	if(_dom==3) {
		var div    = parentDiv ? (new Layer(width,parentDiv)) : (new Layer(width));
		if (height>0) div.resizeTo(width,height);
		div.moveTo(left,top);
		div.load(url,width);
		return div;
	}
	return null;
}

//!--------------------------------------------------------------
// @function	getDivImage
// @desc		Retorna uma imagem a partir de seu nome
// @param		div Object			Elemento que contém a imagem
// @param		imgName String		Nome ou identificação da imagem
// @return		Image object O elemento imagem encontrado
//!--------------------------------------------------------------
function getDivImage(div, imgName) {
	if(_dom==4)            return document.images[imgName];
	if(_dom==2 || _dom==1) return document.images(imgName);
	if(_dom==3)            return div.document.images[imgName];
	return null;
}

//!--------------------------------------------------------------
// @function	getDivForm
// @desc		Retorna um formulário a partir de seu nome
// @param		div Object			Elemento que contém o formulário
// @param		frmName String		Nome ou identificação do formulário
// @return		Form object O elemento formulário encontrado
//!--------------------------------------------------------------
function getDivForm(div, frmName) {
	if(_dom==4)            return document.forms[frmName];
	if(_dom==2 || _dom==1) return document.forms(frmName);
	if(_dom==3)            return div.document.forms[frmName];
	return null;
}

//!--------------------------------------------------------------
// @function	initDivPos
// @desc		Normaliza o posicionamento de uma layer a partir
//				do posicionamento 'offset' atual
// @param		div Object	Layer a ser ajustada
// @return		Object A própria layer modificada
//!--------------------------------------------------------------
function initDivPos(div) {
	if(_dom==4) {
		div.style.left=div.offsetLeft+'px';
		div.style.top =div.offsetTop +'px';
	} else if(_dom==2 || _dom==1) {
		div.style.pixelLeft=div.offsetLeft;
		div.style.pixelTop =div.offsetTop;
	}
	return div;
}

//!--------------------------------------------------------------
// @function	initDivSize
// @desc		Normaliza o tamanho de uma layer a partir do
//				tamanho 'offset' atual
// @param		div Object	Layer a ser ajustada
// @return		Object A própria layer modificada
//!--------------------------------------------------------------
function initDivSize(div) {
	if(_dom==4) {
		div.style.width =div.offsetWidth +'px';
		div.style.height=div.offsetHeight+'px';
	} else if(_dom==2 || _dom==1) {
		div.style.pixelWidth =div.offsetWidth;
		div.style.pixelHeight=div.offsetHeight;
	}
	return div;
}

//!--------------------------------------------------------------
// @function	getDivLeft
// @desc		Busca a posição no eixo X de um elemento
// @param		div Object	Elemento do documento
// @return		Integer Posição X do elemento
//!--------------------------------------------------------------
function getDivLeft(div) {
	if(_dom==4 || _dom==2) return div.offsetLeft;
	if(_dom==1)            return div.style.pixelLeft;
	if(_dom==3)            return div.left;
	return 0;
}

//!--------------------------------------------------------------
// @function	getDivTop
// @desc		Busca a posição no eixo Y de um elemento
// @param		div Object	Elemento do documento
// @return		Integer Posição Y do elemento
//!--------------------------------------------------------------
function getDivTop(div) {
	if(_dom==4 || _dom==2) return div.offsetTop;
	if(_dom==1)            return div.style.pixelTop;
	if(_dom==3)            return div.top;
	return 0;
}

//!--------------------------------------------------------------
// @function	getDivWidth
// @desc		Busca a largura de um elemento
// @param		div Object	Elemento do documento
// @return		Integer Largura do elemento
//!--------------------------------------------------------------
function getDivWidth (div) {
	if(_dom==4 || _dom==2) return div.offsetWidth;
	if(_dom==1)            return div.style.pixelWidth;
	if(_dom==3)            return div.clip.width;
	return 0;
}

//!--------------------------------------------------------------
// @function	getDivHeight
// @desc		Busca a altura de um elemento
// @param		div Object	Elemento do documento
// @return		Integer Altura do elemento
//!--------------------------------------------------------------
function getDivHeight(div) {
	if(_dom==4 || _dom==2) return div.offsetHeight;
	if(_dom==1)            return div.style.pixelHeight;
	if(_dom==3)            return div.clip.height;
	return 0;
}

//!--------------------------------------------------------------
// @function	getILayerWidth
// @desc		Busca a largura do documento interno de um ilayer
// @param		objILayer Object	Objeto ILAYER
// @return		Integer Largura do documento interno
//!--------------------------------------------------------------
function getILayerWidth (objILayer) {
	if(_dom==4)              return objILayer.contentDocument.body.offsetWidth;
	if(_dom==2 || _dom==1)   return _mac?frames(objILayer.id).document.body.offsetWidth:frames(objILayer.id).document.body.scrollWidth;
	if(_dom==3)              return objILayer.document.width;
	return 0;
}

//!--------------------------------------------------------------
// @function	getILayerHeight
// @desc		Busca a altura do documento interno de um ilayer
// @param		objILayer Object	Objeto ILAYER
// @return		Integer Altura do documento interno
//!--------------------------------------------------------------
function getILayerHeight(objILayer) {
	if(_dom==4)              return objILayer.contentDocument.body.offsetHeight;
	if(_dom==2 || _dom==1)   return _mac?frames(objILayer.id).document.body.offsetHeight:frames(objILayer.id).document.body.scrollHeight;
	if(_dom==3)              return objILayer.document.height;
	return 0;
}

//!--------------------------------------------------------------
// @function	moveDivTo
// @desc		Move um elemento para uma determinada posição
// @param		objDiv Object		Elemento do documento
// @param		left Integer		Posição X
// @param		top Integer			Posição Y
// @return		void
//!--------------------------------------------------------------
function moveDivTo(objDiv, left, top) {
	if(_dom==4) {
		objDiv.style.left=left+'px';
		objDiv.style.top =top +'px';
		return;
	}
	if(_dom==2 || _dom==1) {
		objDiv.style.pixelLeft=left;
		objDiv.style.pixelTop =top;
		return;
	}
	if(_dom==3) {
		objDiv.moveTo(left,top);
		return;
	}
}

//!--------------------------------------------------------------
// @function	moveDivBy
// @desc		Desloca um elemento do documento
// @param		objDiv Object		Elemento do documento
// @param		left Integer		Deslocamento X
// @param		top Integer			Deslocamento Y
// @return		void
//!--------------------------------------------------------------
function moveDivBy(objDiv, left, top) {
	if(_dom==4) {
		objDiv.style.left=objDiv.offsetLeft+left;
		objDiv.style.top =objDiv.offsetTop +top;
		return;
	}
	if(_dom==2) {
		objDiv.style.pixelLeft=objDiv.offsetLeft+left;
		objDiv.style.pixelTop =objDiv.offsetTop +top;
		return;
	}
	if(_dom==1) {
		objDiv.style.pixelLeft+=left;
		objDiv.style.pixelTop +=top;
		return;
	}
	if(_dom==3) {
		objDiv.moveBy(left,top);
		return;
	}
}

//!--------------------------------------------------------------
// @function	scrollILayerXTo
// @desc		Modifica a posição X de um elemento ILAYER
// @param		objILayer Object	Elemento ILAYER
// @param		x Integer			Posição X para o elemento
// @return		void
//!--------------------------------------------------------------
function scrollILayerXTo(objILayer, x) {
	if(_dom==4) {
		frames[objILayer.id].scrollTo(x,frames[objILayer.id].scrollY);
		return;
	}
	if(_dom==2 || _dom==1) {
		frames(objILayer.id).scrollTo(x,frames(objILayer.id).document.body.scrollTop);
		return;
	}
	if(_dom==3) {
		var dx=x-objILayer.clip.left, ch=objILayer.clip.width;
		objILayer.left-=dx;
		objILayer.clip.left=x;
		objILayer.clip.width=ch;
		return;
	}
	return;
}

//!--------------------------------------------------------------
// @function	scrollILayerYTo
// @desc		Modifica a posição Y de um elemento ILAYER
// @param		objILayer Object	Elemento ILAYER
// @param		y Integer			Posição Y para o elemento
// @return		void
//!--------------------------------------------------------------
function scrollILayerYTo(objILayer, y) {
	if(_dom==4) {
		frames[objILayer.id].scrollTo(frames[objILayer.id].scrollX,y);
		return;
	}
	if(_dom==2 || _dom==1) {
		frames(objILayer.id).scrollTo(frames(objILayer.id).document.body.scrollLeft,y);
		return;
	}
	if(_dom==3) {
		var dy=y-objILayer.clip.top, ch=objILayer.clip.height;
		objILayer.top-=dy;objILayer.clip.top=y;
		objILayer.clip.height=ch;
		return;
	}
	return;
}

//!--------------------------------------------------------------
// @function	changeILayerUrl
// @desc		Altera a URL do documento interno de um elemento ILAYER
// @param		objILayer Object	Elemento ILAYER
// @param		url String			URL para o documento interno do elemento
// @return		void
//!--------------------------------------------------------------
function changeILayerUrl(objILayer, url) {
	if(_dom==4) {
		objILayer.setAttribute('src',url);
		return;
	}
	if(_dom==2 || _dom==1) {
		frames(objILayer.id).location.replace(url);
		return;
	}
	if(_dom==3) {
		objILayer.load(url,objILayer.clip.width);
		return;
	}
	return;
}

//!--------------------------------------------------------------
// @function	resizeDivTo
// @desc		Redimensiona um elemento para um determinado tamanho
// @param		objDiv Object	Elemento do documento
// @param		width Integer	Nova largura para o elemento
// @param		height Integer	Nova altura para o elemento
// @return		void
//!--------------------------------------------------------------
function resizeDivTo(objDiv, width, height) {
	if(_dom==4) {
		objDiv.style.width =width +'px';
		objDiv.style.height=height+'px';
		return;
	}
	if(_dom==2 || _dom==1) {
		objDiv.style.pixelWidth =width;
		objDiv.style.pixelHeight=height;
		return;
	}
	if(_dom==3) {
		objDiv.resizeTo(width,height);
		return;
	}
}

//!--------------------------------------------------------------
// @function	resizeDivBy
// @desc		Aumenta ou diminui o tamanho de um elemento a partir de uma 'diferença'
// @param		objDiv Object	Elemento do documento
// @param		width Integer	Diferença de largura
// @param		height Integer	Diferença de altura
// @return		void
//!--------------------------------------------------------------
function resizeDivBy(objDiv, width, height) {
	if(_dom==4) {
		objDiv.style.width =(objDiv.offsetWidth +width )+'px';
		objDiv.style.height=(objDiv.offsetHeight+height)+'px';
		return;
	}
	if(_dom==2) {
		objDiv.style.pixelWidth =objDiv.offsetWidth +width;
		objDiv.style.pixelHeight=objDiv.offsetHeight+height;
		return;
	}
	if(_dom==1) {
		objDiv.style.pixelWidth +=width;
		objDiv.style.pixelHeight+=height;
		return;
	}
	if(_dom==3) {
		objDiv.resizeBy(width,height);
		return;
	}
}

//!--------------------------------------------------------------
// @function	showHideLayer
// @desc		Alterna a visibilidade de uma DIV/LAYER
// @return		void
// @note		Esta função recebe n parâmetros, agrupados de
//				2 em 2 no formato 'camada1','visibilidade1',
//				'camada2','visibilidade2',...
//!--------------------------------------------------------------
function showHideLayer() {
	var i,p,v,obj,args=showHideLayer.arguments;
	for (i=0; i<(args.length-1); i+=2) {
		if ((obj=getDocumentObject(args[i]))!=null) {
			v=args[i+1];
			if (obj.style) {
				obj=obj.style;
				v=(v=='show')?'visible':(v='hide')?'hidden':v;
			}
			obj.visibility=v;
		}
	}
}

//!--------------------------------------------------------------
// @function	setDivVisibility
// @desc		Seta a visibilidade de um elemento
// @param		objDiv Object		Elemento do documento
// @param		visible Boolean		Visibilidade para o elemento
// @param		hideSel Boolean		Esconder os elementos SELECT que colidirem com a posição do elemento
// @return		void
// @note		Se visible for true, o elemento irá herdar a visibilidade do pai (se existir), com o valor 'inherit'
//!--------------------------------------------------------------
function setDivVisibility(objDiv, visible) {
	var st,v,h;
	st=(objDiv.style?objDiv.style:objDiv);
	v=(visible?'visible':'hidden');
	st.visibility=v;
}

//!--------------------------------------------------------------
// @function	hideSelectBoxes
// @desc		Esconde os elementos do tipo SELECT que estão localizados
//				em um determinado espaço definido pelos parâmetros x,y,w e h
// @param		x Integer	Posição inicial X
// @param		y Integer	Posição inicial Y
// @param		w Integer	Largura
// @param		h Integer	Altura
// @param		l Integer	Level
// @return		Array Vetor com os objetos que foram escondidos
//!--------------------------------------------------------------
function hideSelectBoxes(x,y,w,h,l) {
	var sel,hsel,selx,sely,selw,selh,i;
	sel = document.getElementsByTagName("SELECT"); sel.level=0; hsel=new Array();
    for(i=0;i<sel.length;i++){
		selx=0; sely=0; var selp;
		if(sel[i].offsetParent){
			selp=sel[i]; 
			while(selp.offsetParent){ selp=selp.offsetParent;selx+=selp.offsetLeft;sely+=selp.offsetTop; }
		}
   		selx+=sel[i].offsetLeft; sely+=sel[i].offsetTop;
   		selw=sel[i].offsetWidth; selh=sel[i].offsetHeight;
   		if(selx+selw>x && selx<x+w && sely+selh>y && sely<y+h){
    		if(sel[i].style.visibility!="hidden"){
    			sel[i].level=l; sel[i].style.visibility="hidden";
    			hsel[hsel.length] = sel[i];    			
      		}
		}
	}
	return hsel;
}

//!--------------------------------------------------------------
// @function	hideCoveredElements
// @desc		Esconde os elementos SELECT, IFRAME, APPLET, OBJECT e EMBED cujas
//				posições conflitam com as posições definidas nos parâmetros da função
// @param		ex1 Integer		Posição X da diagonal superior
// @param		ex2 Integer		Posição X da diagonal inferior
// @param		ey1 Integer		Posição Y da diagonal superior
// @param		ey2 Integer		Posição Y da diagonal inferior
// @return		Array Vetor com os objetos que foram escondidos
//!--------------------------------------------------------------
function hideCoveredElements(ex1, ex2, ey1, ey2) {
	var r = new Array();
	if (_dom != 4)  {
		var tags = new Array('select', 'iframe', 'applet', 'object', 'embed');
		var cx1, cx2, cy1, cy2;
		for (i=0;i<tags.length;i++) {
			var list = document.getElementsByTagName(tags[i]);
			for (j=0;j<list.length;j++) {
				pos = getAbsolutePos(list[j]);
				cx1 = pos.x;
				cx2 = list[j].offsetWidth+cx1;
				cy1 = pos.y;
				cy2 = list[j].offsetHeight+cy1;
				if (cx1<ex2 && cx2>ex1 && cy1<ey2 && cy2>ey1) {
					list[j].style.visibility = "hidden";
					r[r.length] = list[j];
				}
			}
		}
	}
	return r;
}

//!--------------------------------------------------------------
// @function	setDivVisibilities
// @desc		Seta a visibilidade para um conjunto de elementos
// @param		divs Array		Vetor de elementos
// @param		visible Boolean	Visibilidade para os elementos
// @return		Array O próprio vetor de elementos modificado
// @note		Se visible for true, os elementos irão herdar a visibilidade do pai (se existir), com o valor 'inherit'
//!--------------------------------------------------------------
function setDivVisibilities(divs, visible) {
	if (!divs.length) return;
	if (_dom==4 || _dom==2 || _dom==1) {
		for(var i=0; i<divs.length; i++) divs[i].style.visibility=(visible)?'inherit':'hidden';
	}
	if (_dom==3) {
		for(var i=0; i<divs.length; i++) divs[i].visibility      =(visible)?'inherit':'hide';
	}
	return divs;
}

//!--------------------------------------------------------------
// @function	setDivClip
// @desc		Configura a região clip interna a um elemento
// @param		objDiv Object	Elemento do documento
// @param		top Integer		Posição Y da aresta superior do clip
// @param		right Integer	Posição X da aresta direita do clip
// @param		bottom Integer	Posição Y da aresta inferior do clip
// @param		left Integer	Posição X da aresta esquerda do clip
// @return		void
//!--------------------------------------------------------------
function setDivClip(objDiv, top, right, bottom, left) {
	if(_dom==4 || _dom==2 || _dom==1) {
		objDiv.style.clip='rect('+top+'px '+right+'px '+bottom+'px '+left+'px)';
		return;
	}
	if(_dom==3) {
		objDiv.clip.top     =top;
		objDiv.clip.right   =right;
		objDiv.clip.bottom  =bottom;
		objDiv.clip.left    =left;
		return;
	}
}

//!--------------------------------------------------------------
// @function	writeToDiv
// @desc		Escreve um conteúdo em um elemento
// @param		objDiv Object	Elemento do documento
// @param		op Boolean		Flag para abrir e resetar o conteúdo do elemento
// @param		cl Boolean		Flag para fechar o conteúdo após a escrita
// @return		void
// @note		Esta função recebe 3 + n parâmetros. A partir do quarto parâmetro,
//				a função interpreta todos os subseqüentes como strings que devem ser
//				incluídas no documento da layer
// @note		Exeplo de uso:<BR>
//				<PRE>
//
//				var div = getDocumentObject('mydiv');
//				writeToDiv(div, true, true, 'String 1', 'String 2', 'String 3');
//
//				</PRE>
//!--------------------------------------------------------------
function writeToDiv(objDiv, op, cl) {
	var s='';
	for(var i=3; i<arguments.length; i++) s+=arguments[i];
	if(_dom==4) {
		if(op) {
			while(objDiv.hasChildNodes()) objDiv.removeChild(objDiv.lastChild);
		}
		var range=document.createRange();
		range.selectNodeContents(objDiv);
		range.collapse(true);
		var cf=range.createContextualFragment(s);
		objDiv.appendChild(cf);
		return;
	}
	if(_dom==2 || _dom==1) {
		if(op) objDiv.innerHTML='';
		if(_mac&&!_ie512) objDiv.innerHTML+=s;
		else objDiv.insertAdjacentHTML('BeforeEnd',s);
		return;
	}
	if(_dom==3) {
		if(op) objDiv.document.open('text/html','replace');
		objDiv.document.write(s);
		if(cl) objDiv.document.close();
		return;
	}
}

//!--------------------------------------------------------------
// @function	setDivBackgroundColor
// @desc		Configura a cor de fundo de um elemento
// @param		objDiv Object	Elemento do documento
// @param		color String	Cor textual ou string RGB para o fundo do elemento
// @return		void
// @note		Se a cor não for passada, a função atribuirá 'transparent' ao elemento
//!--------------------------------------------------------------
function setDivBackgroundColor(objDiv, color) {
	if(color==null) color='transparent';
	if(_dom==3) objDiv.bgColor=color;
	else        objDiv.style.backgroundColor=color;
}

//!--------------------------------------------------------------
// @function	setDivBackgroundImage
// @desc		Configura a imagem de fundo de um elemento
// @param		objDiv Object	Elemento do documento
// @param		url String		URL - caminho completo da imagem
// @return		void
//!--------------------------------------------------------------
function setDivBackgroundImage(objDiv, url) {
	if(_dom==3) objDiv.background.src=url?url:null;
	else        objDiv.style.backgroundImage=url?('url('+url+')'):'none';
}

//!--------------------------------------------------------------
// @function	setDivZIndex
// @desc		Configura a ordem no eixo Z (sobreposição) de um elemento
// @param		objDiv Object	Elemento do documento
// @param		order Integer	Posição no eixo Z para o elemento
// @return		void
//!--------------------------------------------------------------
function setDivZIndex(objDiv, order) {
	if(_dom==4 || _dom==2 || _dom==1) {
		objDiv.style.zIndex=order;
		return;
	}
	if(_dom==3) {
		objDiv.zIndex      =order;
		return;
	}
}

//!--------------------------------------------------------------
// @function	setDivStyleAttribute
// @desc		Configura um determinado atributo de um elemento
// @param		objDiv Object	Elemento do documento
// @param		nm String		Nome do atributo
// @param		value String	Valor para o atributo
// @return		Object O próprio elemento modificado
//!--------------------------------------------------------------
function setDivStyleAttribute(div, nm, value) {
	if(_dom!=0 && _dom!=3) eval('div.style.'+nm+'='+value);
	return div;
}

//!--------------------------------------------------------------
// @function	getLeftFromEvent
// @desc		Retorna a posição X da ocorrência de um evento
// @param		e Event object		Evento ocorrido
// @return		IntegerPosição X do evento
//!--------------------------------------------------------------
function getLeftFromEvent(e) {
	if(_dom==4)          return e.clientX+window.scrollX;
	if(_dom==2||_dom==1) return document.body.scrollLeft+window.event.clientX;
	if(_dom==3)          return e.pageX;
	return 0;
}

//!--------------------------------------------------------------
// @function	getTopFromEvent
// @desc		Retorna a posição Y da ocorrência de um evento
// @param		e Event object		Evento ocorrido
// @return		Integer Posição Y do evento
//!--------------------------------------------------------------
function getTopFromEvent(e){
	if(_dom==4)          return e.clientY+window.scrollY;
	if(_dom==2||_dom==1) return document.body.scrollTop+window.event.clientY;
	if(_dom==3)          return e.pageY;
	return 0;
}