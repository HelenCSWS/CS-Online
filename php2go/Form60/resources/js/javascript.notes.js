/*
    display popup window
*/
function addNote(owner_type, owner_id)
{
    showNoteForm('Add note', owner_type, owner_id, null, null);
}

function editNote(note_id)
{
    var owner_type = document.getElementById('noteOwnerType').value;
    var owner_id = document.getElementById('noteOwnerID').value;
    showNoteForm('Edit note', owner_type, owner_id, note_id, document.getElementById(note_id).title);
}

function cancelNote()
{
    hidePopWin(false);
}

function saveNote()
{
    var owner_type = document.getElementById('noteOwnerType').value;
    var owner_id = document.getElementById('noteOwnerID').value;
    var order_by = document.getElementById('noteOrderBy').value;
    var order_type = document.getElementById('noteOrderType').value;
    var noteText = parent.document.getElementById('note_text').value;
    var noteID = parent.document.getElementById('note_id').value;
    var notePage = document.getElementById('notePage').value;
    if (noteText.trim().length>0)
    {
        hidePopWin(false);
        showLoading();
        xajax_saveNote(noteID, noteText, owner_type, owner_id, order_by, order_type, notePage);
    }
    else
        parent.document.getElementById('note_text').focus();
}
function insertNote()
{
 	var invText = parent.document.getElementById('note_text').value;
 	
 	var grpTxts = invText.split("|");
 	
 	var grpSize = grpTxts.length;
 	
 	var txtChq = grpTxts[0];
 	var txtInv ="";
 	var txtAmt="";
 	
 	if(grpSize>1)
 	{
		txtInv = grpTxts[1];
	}
	
	if(grpSize>2)
 	{
		txtAmt = grpTxts[2];
	}
	
 	var txtMemo = "chq "+ txtChq + " - inv "+txtInv+" - $"+txtAmt;
 	
 	
	parent.document.getElementById('note_text').value=txtMemo;
	parent.document.getElementById('note_text').focus();
}

//used by notes control

function showLoading()
{
  /*  var divLoading = document.getElementById('loadingMsg');
    if (divLoading)
    {
        divLoading.innerHTML = 'Loading ...';
        divLoading.style.display = 'block';
    }*/
}

function sortNotes(order_by)
{
    if (order_by)
    {
        var order_type;
        var fldOrderBy = document.getElementById('noteOrderBy');
        var fldOrderType = document.getElementById('noteOrderType');
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
        
        document.getElementById('arrow_' + old_order_by).innerHTML = '';
        document.getElementById('notePage').value = 1;
    }
    refreshNotes();
}

function refreshNotes()
{

    var owner_type = document.getElementById('noteOwnerType').value;
    var owner_id = document.getElementById('noteOwnerID').value;
    var order_by = document.getElementById('noteOrderBy').value;
    var order_type = document.getElementById('noteOrderType').value;
    var notePage = document.getElementById('notePage').value;
    var notePage = document.getElementById('notePage').value;
    var note_year = document.getElementById('note_year').value;
    
    showLoading();
    
   
    xajax_refreshNotes(owner_type, owner_id, order_by, order_type, notePage,note_year,null);
}

function getNextPage()
{
    document.getElementById('notePage').value = parseInt(document.getElementById('notePage').value, 10) + 1; 
    refreshNotes();
}

function getPrevPage()
{
    document.getElementById('notePage').value = parseInt(document.getElementById('notePage').value, 10) - 1; 
    refreshNotes();
}

var g_note_id;
function deleteNote(note_id)
{
    g_note_id = note_id;
    parent.showMsgBox("You are about to delete a note.\n Do you want to proceed?", parent.MBYESNO + parent.ICONQUESTION, deleteNoteHandler);
}

function deleteNoteHandler(retVal)
{
    if (retVal == parent.IDYES)
    {
        var note_id = g_note_id;
        g_note_id = 0;
        var owner_type = document.getElementById('noteOwnerType').value;
        var owner_id = document.getElementById('noteOwnerID').value;
        var order_by = document.getElementById('noteOrderBy').value;
        var order_type = document.getElementById('noteOrderType').value;
        var notePage = document.getElementById('notePage').value;
        var note_year = document.getElementById('note_year').value;
        
        showLoading();
        xajax_deleteNote(note_id, owner_type, owner_id, order_by, order_type, notePage,note_year);
    }
    else
        return true;
}

function showNoteForm(formTitle, owner_type, owner_id, note_id, note_text)
{
 

    showPopWin('', 498, 290, null, formTitle);
    var noteForm = '<FORM ID="noteAdd" NAME="noteAdd" ACTION="" METHOD="POST" STYLE="display:inline" border="2">';
      noteForm = noteForm + '<INPUT TYPE="hidden" ID="owner_type" NAME="owner_type" VALUE="{owner_type}">';
    noteForm = noteForm + '<INPUT TYPE="hidden" ID="owner_id" NAME="owner_id" VALUE="{owner_id}">';
    noteForm = noteForm + '<INPUT TYPE="hidden" ID="note_id" NAME="note_id" VALUE="{note_id}">';
    noteForm = noteForm + '<table width="100%" height="100%" border="0" ><td><tr>';
     noteForm = noteForm + '<tr><td align="left"><div id="form_client_errors" class="error_style" style="display:none"></div></td>';
    noteForm = noteForm + '</tr><tr><td cellpadding="0" cellspacing="0" align="center" valign="middle" style="height:100%;width:100%;padding-top:4px;padding-bottom:2px" >';
    noteForm = noteForm + '<TEXTAREA ID="note_text" NAME="note_text" style="width:100%;height:100%"  TABINDEX="1" style="overflow:auto" CLASS="input">{note_text}</TEXTAREA></td></tr>';
    noteForm = noteForm + '<tr><td align="right" style="padding-bottom:6px; padding-right:10px">';
    noteForm = noteForm + '<INPUT ID="btnInsert" NAME="btnInsert" TYPE="BUTTON" VALUE="Add chq" onClick="middle.insertNote();" CLASS="btnOK" TABINDEX="2">&nbsp;';
    noteForm = noteForm + '<INPUT ID="btnOK" NAME="btnOK" TYPE="BUTTON" VALUE="OK" onClick="middle.saveNote();" CLASS="btnOK" TABINDEX="2">&nbsp;';
    noteForm = noteForm + '<INPUT ID="btnPopCancel" NAME="btnPopCancel" TYPE="BUTTON" VALUE="Cancel" onClick="middle.cancelNote();" CLASS="btnOK" TABINDEX="3">';
    noteForm = noteForm + '</td></tr></table></FORM>';
    noteForm = noteForm.replace(/{owner_type}/g, owner_type);
    noteForm = noteForm.replace(/{owner_id}/g, owner_id);
    noteForm = noteForm.replace(/{note_id}/g, (note_id)?note_id:"0");
    noteForm = noteForm.replace(/{note_text}/g, (note_text)?note_text:"");
    
    parent.gPopFrame.innerHTML = noteForm;
    parent.document.getElementById('note_text').focus();
    
}
