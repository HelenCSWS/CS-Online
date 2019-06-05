<!-- START BLOCK : action_table_start -->
<div class="ActionMenu"><div>
<!-- END BLOCK : action_table_start -->
    <!-- START BLOCK : action_block -->
    <a title="{action_title}" href="{action_url}">{action_title}</a></BR>
    <!-- END BLOCK : action_block -->
 <!-- START BLOCK : action_table_end -->
</div></div>
 <!-- END BLOCK : action_table_end -->
<!-- START BLOCK : action_page_export_start -->
<div class="ActionMenu"><div>
<!-- END BLOCK : action_page_export_start -->
        <!-- START BLOCK : action_page_print -->
        <div>
                <img src="resources/images/print.gif" alt="Print" width="18" height="18" border="0" align="absmiddle">
                <a title="Print this page" href="javascript:printPage();">Print page</a>
        </div>
        <!-- END BLOCK : action_page_print -->
        <!-- START BLOCK : action_page_pdf -->
        <div>
                <img src="resources/images/pdf.gif" alt="PDF" width="18" height="18" border="0" align="absmiddle">
                <a title="Get PDF format" href="{request_uri}&pdf" target="pdf_view">PDF format</a> 
        </div>
        <!-- END BLOCK : action_page_pdf -->
        <!-- START BLOCK : action_page_timer -->
        {pagetimer}
        <!-- END BLOCK : action_page_timer -->
<!-- START BLOCK : action_page_export_end -->
</div></div>
<!-- END BLOCK : action_page_export_end -->
<div id="MainFrame">
        <div class="MainFrameHeader">{title}</div>
        <div id="MainFrameContent" 
                style="width: 100%;
                       height: 100%;
                       vertical-align: middle;
                       text-align:center;
                       padding:2px 4px 0px 4px;
                       margin:0px;
                       overflow:auto">
                    {error}
                    {main}
        </div>
</div>

<script language="javascript">
function setContentSize() {
var windowHeight= getWindowHeight();
var windowWidth = getWindowWidth();
var contentContainer = document.getElementById('MainFrame');
var contentElement = document.getElementById('MainFrameContent');
if (windowHeight>0) {
var contentHeight= windowHeight - 16;
contentContainer.style.height=contentHeight+"px";
contentElement.style.height=contentHeight - 22 + "px";
}
if (windowWidth>0) {
var contentWidth= windowWidth - 154;
contentContainer.style.width=contentWidth+"px";
//contentElement.style.width=contentWidth - 4 + "px";
}
}
setContentSize();
addEvent(window, 'resize', setContentSize);
</script>