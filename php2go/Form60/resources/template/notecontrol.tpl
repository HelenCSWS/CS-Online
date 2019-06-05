<div class="gridContainer">
    <INPUT TYPE="hidden" ID="noteOwnerType" NAME="noteOwnerType" VALUE="{owner_type}"/>
    <INPUT TYPE="hidden" ID="noteOwnerID" NAME="noteOwnerID" VALUE="{owner_id}"/>    
    <INPUT TYPE="hidden" ID="noteOrderBy" NAME="noteOrderBy" VALUE="{order_by}"/>    
    <INPUT TYPE="hidden" ID="noteOrderType" NAME="noteOrderType" VALUE="{order_type}"/>    
    <INPUT TYPE="hidden" ID="notePage" NAME="notePage" VALUE="{page}"/>    
    <div class="gridHeader">
        <!--div id="loadingMsg"></div-->
        <table class="gridTable" cellspacing="0" border="0">
                <tr>
                    <td class="gridHeaderCell" style="width:20px;">&nbsp;</td>
                    <td class="gridHeaderCell" style="width:83px;"><A href="javascript:sortNotes('when_created');">Date</A><span ID="arrow_when_created" class="sortSymbol">{when_created_sort}</span></td>
                    <td class="gridHeaderCell" style="width:200px;"><A href="javascript:sortNotes('user_name');">Added by</A><span ID="arrow_user_name" class="sortSymbol">{user_name_sort}</span></td>
                    <td class="gridHeaderCell" style="width:auto;border:0px"><A href="javascript:sortNotes('note_text');">Note</A><span ID="arrow_note_text" class="sortSymbol">{note_text_sort}</span></td>
                </tr>
        </table>
    </div>
    <div id="noteGrid" style="overflow:auto; height:20px">
        <table class="gridTable" cellspacing="0">
            <!-- START BLOCK : loop_line -->
            <tr class="{row_style}">
                <td nowrap class="gridrowCell" style="width:20px;" valign="top"><A href="javascript:deleteNote('{note_id}')"><img src="resources/images/delete.gif" border="0" title = "Delete note"></A></td>
                <td nowrap class="gridrowCell" style="width:83px;" valign="middle" title="{when_created}">{when_created}</td>
                <td nowrap class="gridrowCell" style="width:200px;" valign="middle" title="{user_name}">{user_name}</td>
                <td nowrap class="gridrowCell" width="auto" valign="middle"><A id="{note_id}" title="{note_text}" href="javascript:void(editNote('{note_id}'));">{note_text}</A></td>
            </tr>
            <!-- END BLOCK : loop_line -->
        </table>
    </div>
   
   <div class="gridLeftLink" style="margin-top:5px">
       Total: {total} notes - Page: {page} of {total_page}
    </div>
    
  

     <div class="gridRightLink" style="margin-top:5px">
 	<select CLASS="input" SIZE="1" STYLE="width:60px" onChange="refreshNotes();"  id="note_year">
		<!-- START BLOCK : year_loop_line -->	<option value="{note_year}" {selected}>{note_year}</option>  <!-- END BLOCK : year_loop_line -->
		
		</select> &nbsp;&nbsp;
		
          <A href="javascript:void(addNote('{owner_type}', '{owner_id}'));">Add the note</A>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
         <!-- START BLOCK : next_page_link -->
    
        <A href="javascript:getNextPage();">Next&gt;&gt;</A>
    
    <!-- END BLOCK : next_page_link -->
    
    <!-- START BLOCK : prev_page_link -->
    
        <A href="javascript:getPrevPage();">&lt;&lt;Back</A>
    
    <!-- END BLOCK : prev_page_link -->
        
    </div>
    
    
</div>
<script language="javascript">

function setNoteHeight() {
var nContainer=document.getElementById('noteGrid');
var windowHeight= getWindowHeight();
if (windowHeight>0) {
var noteHeight= windowHeight - 64 - findPosition(nContainer,0);
if (noteHeight>20)
nContainer.style.height=noteHeight+"px";
}
}
addEvent(window, 'load', setNoteHeight);
addEvent(window, 'resize', setNoteHeight);
</script>

