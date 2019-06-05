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
// $Header: /www/cvsroot/php2go/resources/javascript/libs/report.js,v 1.5 2005/05/10 20:55:54 mpont Exp $
// $Date: 2005/05/10 20:55:54 $
// $Revision: 1.5 $

//!---------------------------------------------------
// @function	goToPage
// @desc		Executa a troca de página do relatório através
//				do valor do campo "Ir Para"
// @param		pageField FormField object	Campo do número de página
// @param		totalPages Integer			Total de páginas do relatorio
// @return		void
//!---------------------------------------------------
function goToPage(pageField, totalPages) {
	var frm, intValue;
	if (pageField.value != '') {
		frm = document.forms['reportGoTo'];
		intValue = parseInt(pageField.value);
		if (intValue > 0 && intValue <= totalPages) {
			if (frm.action.indexOf("?") == -1)
				frm.action += "?page=" + intValue;
			else if (frm.action.indexOf("?") == frm.action.length - 1)
				frm.action += "page=" + intValue;
			else
				frm.action += "&page=" + intValue;
			return true;
		} else {
			alert(reportGoToError);
			pageField.value = "";
			pageField.focus();
			return false;
		}
	}	
}
