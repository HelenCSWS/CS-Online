<div id="head">
    <iframe id="top" name="top" width = "100%" height="63px" FRAMEBORDER="0" scrolling="no" src="header.php"></iframe>
    <div class="rightMenuBar">
        <A class="menuButton" title="Logout" href="login.php?logoff=1" onmouseover="buttonMouseover(event, 'menu10');">Logout</A>
    </div>
    {menu}
</div>
<!--div id="content"-->
<iframe id="middle" name="middle"  width="100%" height="100%" FRAMEBORDER="0" scrolling="no" src="{main}"></iframe>
<!--/div-->
<div>
    {error}
</div>
<script language="javascript">
function setMiddle() 
{
    var windowHeight=getWindowHeight();
    if (windowHeight>0)
    {
        var headerHeight = document.getElementById('head').offsetHeight;
        var middleElement= document.getElementById('middle');
        if ((windowHeight-headerHeight)>=0) 
            middleElement.height=(windowHeight-headerHeight)+'px';
        else 
            middleElement.height='85%';
    }
}
setMiddle();
window.onresize = function() {
  setMiddle();
}
</script>