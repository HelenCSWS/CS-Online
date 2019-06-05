/*
    display popup window
*//*
function addmarketList(owner_type, owner_id)
{
    showmarketListForm('Add marketList', owner_type, owner_id, null, null);
}

function editmarketList(marketList_id)
{
    var owner_type = document.getElementById('marketListOwnerType').value;
    var owner_id = document.getElementById('marketListOwnerID').value;
    showmarketListForm('Edit marketList', owner_type, owner_id, marketList_id, document.getElementById(marketList_id).title);
}

function cancelmarketList()
{
    hidePopWin(false);
}

function savemarketList()
{
    var owner_type = document.getElementById('marketListOwnerType').value;
    var owner_id = document.getElementById('marketListOwnerID').value;
    var order_by = document.getElementById('marketListOrderBy').value;
    var order_type = document.getElementById('marketListOrderType').value;
    var marketListText = parent.document.getElementById('marketList_text').value;
    var marketListID = parent.document.getElementById('marketList_id').value;
    if (marketListText.trim().length>0)
    {
        hidePopWin(false);
        showLoading();
        xajax_savemarketList(marketListID, marketListText, owner_type, owner_id, order_by, order_type);
    }
    else
        parent.document.getElementById('marketList_text').focus();
}

//used by marketLists control
*/
function showLoading()
{
    var divLoading = document.getElementById('loadingMsg');
    if (divLoading)
    {
        divLoading.innerHTML = 'Loading ...';
        divLoading.style.display = 'block';
    }
}

function sortMarketList(order_by,status_id)
{
    if (order_by)
    {
        var odbyname ="ListOrderBy_"+status_id
        var odtyname ="ListOrderType_"+status_id
         var lpagename ="ListPage_"+status_id
       var order_type;
        var fldOrderBy = document.getElementById(odbyname);
        var fldOrderType = document.getElementById(odtyname);
        
     //   alert(odbyname);
        var old_order_by = fldOrderBy.value;
        if (old_order_by != order_by)
        {
            order_type = 'a';
            fldOrderBy.value = order_by;
            fldOrderType.value = order_type;
        }
        else
        {
            order_type = (fldOrderType.value == 'a')?'d':'a';
            fldOrderType.value = order_type;
        }
        
     //  alert(old_order_by);

        document.getElementById( 'arrow_'+old_order_by+"_"+status_id ).innerHTML = '';
        document.getElementById(lpagename).value = 1;
    }
    refreshmarketLists(status_id);
}

function refreshmarketLists(statusid)
{
 //   var otname ="ListOwnerType_"+statusid;
 //   var oiname ="ListOwnerID_"+statusid;
    var obname ="ListOrderBy_"+statusid;
    var odtname ="ListOrderType_"+statusid;
    var lpname ="ListPage_"+statusid;
    
//    var owner_type = document.getElementById(otname).value;
//    var owner_id = document.getElementById(oiname).value;
    var order_by = document.getElementById(obname).value;
    var order_type = document.getElementById(odtname).value;
    var list_page = document.getElementById(lpname).value;
/*	alert(statusid);
	alert(order_by);
	alert(order_type);
	alert(list_page);
*/    xajax_refreshMarketList(order_by, order_type, list_page,statusid);
//	alert('OK');
}

function getNextPage(statusid)
{
    var lpname ="ListPage_"+statusid;
    document.getElementById(lpname).value = parseInt(document.getElementById(lpname).value, 10) + 1;
    refreshmarketLists(statusid);
}

function getPrevPage(statusid)
{
    var lpname ="ListPage_"+statusid;
    document.getElementById(lpname).value = parseInt(document.getElementById(lpname).value, 10) - 1;
    refreshmarketLists(statusid);
}
/*
var g_marketList_id;
function deletemarketList(marketList_id)
{
    g_marketList_id = marketList_id;
    parent.showMsgBox("You are about to delete a marketList.\n Do you want to proceed?", parent.MBYESNO + parent.ICONQUESTION, deletemarketListHandler);
}

function deletemarketListHandler(retVal)
{
    if (retVal == parent.IDYES)
    {
        var marketList_id = g_marketList_id;
        g_marketList_id = 0;
        var owner_type = document.getElementById('marketListOwnerType').value;
        var owner_id = document.getElementById('marketListOwnerID').value;
        var order_by = document.getElementById('marketListOrderBy').value;
        var order_type = document.getElementById('marketListOrderType').value;
        var marketListPage = document.getElementById('marketListPage').value;

        showLoading();
        xajax_deletemarketList(marketList_id, owner_type, owner_id, order_by, order_type, marketListPage);
    }
    else
        return true;
}

function showmarketListForm(formTitle, owner_type, owner_id, marketList_id, marketList_text)
{
    showPopWin('', 500, 300, null, formTitle);
    var marketListForm = '<FORM ID="marketListAdd" NAME="marketListAdd" ACTION="" METHOD="POST" STYLE="display:inline">';
    marketListForm = marketListForm + '<INPUT TYPE="hidden" ID="owner_type" NAME="owner_type" VALUE="{owner_type}">';
    marketListForm = marketListForm + '<INPUT TYPE="hidden" ID="owner_id" NAME="owner_id" VALUE="{owner_id}">';
    marketListForm = marketListForm + '<INPUT TYPE="hidden" ID="marketList_id" NAME="marketList_id" VALUE="{marketList_id}">';
    marketListForm = marketListForm + '<table width="100%" height="100%" border="0" >';
    marketListForm = marketListForm + '<tr><td align="left"><div id="form_client_errors" class="error_style" style="display:none"></div></td>';
    marketListForm = marketListForm + '</tr><tr><td cellpadding="4" cellspacing="0" align="center" valign="top" style="width:100%;padding-top:4px;" >';
    marketListForm = marketListForm + '<TEXTAREA ID="marketList_text" NAME="marketList_text" COLS="75" ROWS="18" TITLE="marketList" TABINDEX="1" CLASS="input">{marketList_text}</TEXTAREA></td></tr>';
    marketListForm = marketListForm + '<tr><td align="right" style="padding-bottom:6px; padding-right:4px">';
    marketListForm = marketListForm + '<INPUT ID="btnOK" NAME="btnOK" TYPE="BUTTON" VALUE="OK" onClick="middle.savemarketList();" CLASS="btnOK" TABINDEX="2">&nbsp;';
    marketListForm = marketListForm + '<INPUT ID="btnPopCancel" NAME="btnPopCancel" TYPE="BUTTON" VALUE="Cancel" onClick="middle.cancelmarketList();" CLASS="btnOK" TABINDEX="3">';
    marketListForm = marketListForm + '</td></tr></table></FORM>';
    marketListForm = marketListForm.replace(/{owner_type}/g, owner_type);
    marketListForm = marketListForm.replace(/{owner_id}/g, owner_id);
    marketListForm = marketListForm.replace(/{marketList_id}/g, (marketList_id)?marketList_id:"0");
    marketListForm = marketListForm.replace(/{marketList_text}/g, (marketList_text)?marketList_text:"");

    parent.gPopFrame.innerHTML = marketListForm;
    parent.document.getElementById('marketList_text').focus();

}
*/

function comparecustomers_save()
{
	var btn = document.getElementById("btnAdd");
	btn.style.visibility = "hidden";
}
