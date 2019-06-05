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
// $Header: /www/cvsroot/php2go/resources/javascript/modules/simplesearch.js,v 1.10 2005/08/30 14:41:31 mpont Exp $
// $Date: 2005/08/30 14:41:31 $
// $Revision: 1.10 $

var selMask = "";
var searchExpressions = new Array();
var filterViewState = false;
var filterDiv = null;
var onlyFromCookie = false;

//!--------------------------------------------------------------
// @function	searchGetCookie
// @desc		Reconstrói os parâmetros de busca a partir de valores armazenados em cookies
// @return		void
//!--------------------------------------------------------------
function searchGetCookie() {
	var cf, co, cv, cm;
	var flist = document.simple_search.p2g_sfield;
	if ((cf = getCookie('p2g_simple_search_fields')) &&
		(co = getCookie('p2g_simple_search_operators')) &&
		(cv = getCookie('p2g_simple_search_values')))
	{
		cfV = cf.split('|');
		coV = co.split('|');
		cvV = cv.split('|');
		// Para todos os valores de busca encontrados no cookie
		for (var i=0; i<cfV.length; i++) {
			var ok = false;
			// Busca o campo de procura correspondente ao campo vindo do cookie
			for (var j=0; j<flist.options.length; j++) {
				if (cfV[i] == flist.options[j].value) {
					var exp = flist.options[j].text;
					// Busca o operador de procura correspondente ao operador vindo do cookie
					for (var k=0; k<reportOpsA.length; k+=2) {
						if (reportOpsA[k] == coV[i]) {
							exp = exp + '|'+reportOpsA[k+1]+'|'+cvV[i]+'|'+cfV[i]+'|'+reportOpsA[k];
							if (!findExpression(exp)) {
								searchExpressions[searchExpressions.length] = exp;
								ok = true;
							}
						}
					}
					if (!ok) {
						for (var k=0; k<reportOpsB.length; k+=2) {
							if (reportOpsB[k] == coV[i]) {
								exp = exp + '|'+reportOpsB[k+1]+'|'+cvV[i]+'|'+cfV[i]+'|'+reportOpsB[k];
								if (!findExpression(exp)) {
									searchExpressions[searchExpressions.length] = exp;
								}
							}
						}
					}
				}
			}
		}
		if (searchExpressions.length > 0) {
			onlyFromCookie = true;
			viewFilters();
		}
	}
}

//!--------------------------------------------------------------
// @function	findExpression
// @desc		Verifica se uma expressão já foi inserida
// @param		expr String	Expressão a ser verificada
// @return		Boolean
//!--------------------------------------------------------------
function findExpression(expr) {
	for (var i=0; i<searchExpressions.length; i++) {
		if (searchExpressions[i] == expr) return true;
	}
	return false;
}

//!--------------------------------------------------------------
// @function	checkSimpleSearch
// @desc		Valida a submissão da busca no relatório. Verifica
//				se pelo menos uma expressão de busca foi adicionada
//				ou se os campos de busca estão preenchidos, o que
//				permite submeter uma única expressão
// @return		void
//!--------------------------------------------------------------
function checkSimpleSearch() {
	var send   = (searchExpressions.length > 0 && !onlyFromCookie);
	var resend = (!checkFields() && searchExpressions.length > 0 && onlyFromCookie && confirm(reportFilResend));
	if (send || resend) {
		document.simple_search.search_fields.value = "";
		document.simple_search.search_operators.value = "";
		document.simple_search.search_values.value = "";
		var values = Array();
		for (i=0; i<searchExpressions.length; i++) {
			var limiter = i>0 ? '|' : '';
			values = searchExpressions[i].split('|');
			document.simple_search.search_fields.value = document.simple_search.search_fields.value + limiter + values[3];
			document.simple_search.search_operators.value = document.simple_search.search_operators.value + limiter + values[4];
			document.simple_search.search_values.value = document.simple_search.search_values.value + limiter + values[2];
		}
		setCookie('p2g_simple_search_fields', document.simple_search.search_fields.value, buildExpireDate(0, 0, 0, 5));
		setCookie('p2g_simple_search_operators', document.simple_search.search_operators.value, buildExpireDate(0, 0, 0, 5));
		setCookie('p2g_simple_search_values', document.simple_search.search_values.value, buildExpireDate(0, 0, 0, 5));
		setCookie('p2g_simple_search_main_op', (document.simple_search.search_main_op[0].checked ? 'AND' : '0R'), buildExpireDate(0, 0, 0, 10));
		document.simple_search.submit();
	} else if (addFilter(false))
		checkSimpleSearch();
}

//!--------------------------------------------------------------
// @function	checkFields
// @desc		Verifica se os campos de busca foram preenchidos
// @param		doFocus Boolean		Indica se o foco deve ser redirecionado para um campo não preenchido
// @return		Boolean
//!--------------------------------------------------------------
function checkFields(doFocus) {
	var fs = document.simple_search.elements['p2g_sfield'];
	var vs = document.simple_search.elements['p2g_svalue'];
	if (fs.selectedIndex == 0) {
		if (doFocus != null) 
			fs.focus();
		return false;
	} else if (trim(vs.value) == "") {
		if (doFocus != null) 
			vs.focus();
		return false;
	} else
		return true;
}

//!--------------------------------------------------------------
// @function	viewFilters
// @desc		Exibe ou esconde a caixa dos filtros adicionados
// @return		void
//!--------------------------------------------------------------
function viewFilters() {
	if (!filterViewState)
		buildFilterViewContent();
	o = document.getElementById('filter_view');
	if (!filterViewState)
		setStyleAttribute(o, 'display', 'block');
	else
		setStyleAttribute(o, 'display', 'none');
	filterViewState = !filterViewState;
}

//!--------------------------------------------------------------
// @function	addFilter
// @desc		Adiciona uma expressão de busca aos filtros de pesquisa.
//				Verifica se os campos de busca estão preenchidos e realiza
//				checagem de máscara do campo de valor se necessário
// @param		verbose Boolean		Se verdadeiro, exibe confirmação e o status atual dos filtros adicionados
// @return		Boolean
//!--------------------------------------------------------------
function addFilter(verbose) {
	if (checkFields(true)) {
		if (!checkSearchValue()) {
			alert(p2gInvalidVal);
			return false;
		} else {
			var fSI = document.simple_search.p2g_sfield.selectedIndex;
			var fC  = document.simple_search.p2g_sfield;
			var oSI = document.simple_search.p2g_soperator.selectedIndex;
			var oC  = document.simple_search.p2g_soperator;
			var vC  = document.simple_search.p2g_svalue;
			var newExpr = String(fC.options[fSI].text+"|"+oC.options[oSI].text+"|"+vC.value+"|"+fC.options[fSI].value+"|"+oC.options[oSI].value);
			for (var i=0; i<searchExpressions.length; i++) 
				if (searchExpressions[i] == newExpr) 
					return false;
			searchExpressions[searchExpressions.length] = newExpr;
			if (verbose != null && verbose == true) {
				buildFilterViewContent();
				alert(reportFilterOk);
				if (!filterViewState)
					viewFilters();
			}
			document.simple_search.p2g_svalue.value = "";
			onlyFromCookie = false;
			return true;
		}
	} else 
		return false;
}

//!--------------------------------------------------------------
// @function	removeFilter
// @desc		Remove o filtro solicitado
// @param		remove Integer		Índice do filtro a ser removido
// @return		void
//!--------------------------------------------------------------
function removeFilter(remove) {
	if (remove >= 0) {
		for (i=remove; i<(searchExpressions.length-1); i++) 
			searchExpressions[i] = searchExpressions[i+1];
		searchExpressions.length--;
	}
	buildFilterViewContent();
}

//!--------------------------------------------------------------
// @function	buildFilterViewContent
// @desc		onstrói o conteúdo da caixa que exibe os filtros atualmente ativos

//!--------------------------------------------------------------
function buildFilterViewContent() {
	var t = "<table cellpadding=\"0\" cellspacing=\"0\" border=\"1\" bgcolor=\"#ffffff\" width=\"100%\"><tr><td width=\"100%\" valign=\"top\"><table cellpadding=\"6\" cellspacing=\"0\" border=\"0\" width=\"100%\">";
	if (searchExpressions.length > 0) {
		for (var i=0; i<searchExpressions.length; i++) {
			values = searchExpressions[i].split('|');
			t = t + "<tr><td valign=\"top\" width=\"90%\"><span style=\"font-family:verdana;font-size:10px;color:#000000;text-decoration:none\">" + values[0] + " <b>" + values[1].toUpperCase() + "</b> " + values[2] + "</span></td><td align=\"right\" width=\"10%\" valign=\"top\"><a href=\"javascript:removeFilter(" + i + ");\" style=\"font-family:verdana;font-size:10px;color:#000000;text-decoration:none\"><b>["+reportFilRemove+"]</b></a></td></tr>";
		}
	} else
		t = t + "<tr><td valign=\"top\" width=\"100%\" colspan=\"2\"><span style=\"font-family:verdana;font-size:10px;color:#000000;text-decoration:none\">"+reportFilEmpty+"</span></td></tr>";
	t = t + "<tr><td align=\"right\" style=\"font-family:verdana;font-size:10px\" width=\"100%\" valign=\"top\" colspan=\"2\"><a href=\"javascript:viewFilters();\" style=\"font-family:verdana;font-size:10px;color:#000000;text-decoration:none\"><b>["+reportFilClose+"]</b></a></td></table></td></tr></table>";
	writeToDiv(getDivFromName('filter_view'), true, true, t);
}

//!--------------------------------------------------------------
// @function	getActualMask
// @desc		Busca a máscara do campo selecionado
// @return		Boolean Retorna true se a máscara é diferente da que está armazenada
//!--------------------------------------------------------------
function getActualMask() {
	var masksArray = document.simple_search.p2g_masks.value.split(",");
	var selField = document.simple_search.p2g_sfield.selectedIndex;
	if (selMask == masksArray[selField-1])
		return false;
	else {
		selMask = masksArray[selField-1];
		return true;
	}
}

//!--------------------------------------------------------------
// @function	filterOperators
// @desc		Função executada na troca da opção de campo de pesquisa.
// @return		void
// @note		Verifica se a máscara mudou, para que as opções de operadores sejam reconstruídas, se necessário
//!--------------------------------------------------------------
function filterOperators() {
	if (getActualMask())
		rebuildOperators(selMask);
}

//!--------------------------------------------------------------
// @function	rebuildOperators
// @desc		Constrói a lista de operadores para uma máscara
// @param		mask String		Nome da máscara
// @return		void
//!--------------------------------------------------------------
function rebuildOperators(mask) {
	document.simple_search.p2g_svalue.value = "";
	switch(mask) {
		case "DATE"		:
		case "TIME"		:
		case "FLOAT"	:
		case "CURRENCY"	:
		case "INTEGER"	:	list = document.simple_search.p2g_soperator;
							list.length = 0;
							for (var i=0; i<reportOpsA.length; i+=2)
								list[list.length] = new Option(reportOpsA[i+1], reportOpsA[i]);
							list.options[0].selected = true;
							break;
		case "ZIP"		:
		case "URL"		:
		case "EMAIL"	:
		case "LOGIN"	:        
		case "STRING"	:	list = document.simple_search.p2g_soperator;
							list.length = 0;
							for (var i=0; i<reportOpsB.length; i+=2)
								list[list.length] = new Option(reportOpsB[i+1], reportOpsB[i]);
							list.options[0].selected = true;
							break;
		  default		:	list = document.simple_search.p2g_soperator;
							list.length = 0;
							for (var i=0; i<reportOpsB.length; i+=2)
								list[list.length] = new Option(reportOpsB[i+1], reportOpsB[i]);
							list.options[0].selected = true;
							break;
	}
}

//!--------------------------------------------------------------
// @function	checkSearchMask
// @desc		Realiza checagem de máscara no momento da digitação
//				dos valores de pesquisa (campo 'p2g_svalue')
// @param		field Field object	Campo de valor de busca
// @param		event Event object	Evento onKeyPress no campo
// @param		dateFormat string	Formato de data ativo
// @return		Boolean
//!--------------------------------------------------------------
function checkSearchMask(field, event, dateFormat) {
	if (document.simple_search.p2g_sfield.selectedIndex == 0) {
		document.simple_search.p2g_sfield.focus();
		return false;
	} else {
		if (!selMask) 
			getActualMask();
		// máscara de código postal, possui delimitadores
		var rz = new RegExp("ZIP\-?([1-9])\:?([1-9])");
		var mz = rz.exec(selMask);
		if (mz)
			return chkMaskZIP(field, event, mz[1], mz[2]);
		// máscara de número decimal (com ou sem delimitadores)
		var rf = new RegExp("FLOAT(\-([1-9][0-9]*)\:([1-9][0-9]*))?");
		var mf = rf.exec(selMask);
		if (mf)
			return (mf[2] && mf[3] ? chkMaskFLOAT(field, event, mf[2], mf[3]) : chkMaskFLOAT(field, event));
		// outras máscaras
		switch (selMask) {
			case "INTEGER" : return chkMaskINTEGER(field, event); break;
			case "DATE"    : return chkMaskDATE(field, event, filterDateFormat); break;
			case "TIME"    : return chkMaskTIME(field, event); break;
			case "EMAIL"   : return chkMaskEMAIL(field, event); break;
			case "URL"     : return chkMaskURL(field, event); break;
			case "CURRENCY": return chkMaskCURRENCY(field, event); break;
			case "LOGIN"   : return chkMaskLOGIN(field, event); break;
			default        : return true;    
		}
	}
}

//!--------------------------------------------------------------
// @function	checkSearchValue
// @desc		Realiza checagem de máscara no valor de pesquisa
//				no momento da submissão (campo 'p2g_svalue')
// @return		Boolean
//!--------------------------------------------------------------
function checkSearchValue() {
	if (document.simple_search.p2g_sfield.selectedIndex == 0) {
		document.simple_search.p2g_sfield.focus();
		return false;
	} else {
		getActualMask();
		field = document.simple_search.p2g_svalue;
		field.value = field.value.replace(/\|/g, "");
		// máscara de código postal, possui delimitadores
		var rz = new RegExp("ZIP\-?([1-9])\:?([1-9])");
		var mz = rz.exec(selMask);
		if (mz)
			return chkZIP(field, mz[1], mz[2]);
		// máscara de número decimal (com ou sem delimitadores)
		var rf = new RegExp("FLOAT(\-([1-9][0-9]*)\:([1-9][0-9]*))?");
		var mf = rf.exec(selMask);
		if (mf)
			return (mf[2] && mf[3] ? chkFLOAT(field, mf[2], mf[3]) : chkFLOAT(field));
		// outras máscaras
		switch (selMask) {
			case "INTEGER" : return chkINTEGER(field); break;
			case "DATE"    : return chkDATE(field, filterDateFormat); break;
			case "TIME"    : return chkTIME(field); break;
			case "EMAIL"   : return chkEMAIL(field); break;
			case "URL"     : return chkURL(field); break;
			case "CURRENCY": return chkCURRENCY(field); break;
			case "LOGIN"   : return chkLOGIN(field); break;
			case "STRING"  : return true;
			default        : return false;    
		}
	}
}
