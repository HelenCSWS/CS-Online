/**
 * X-browser event handler attachment and detachment
 *
 * @argument obj - the object to attach event to
 * @argument evType - name of the event - DONT ADD "on", pass only "mouseover", etc
 * @argument fn - function to call
 */
function addEvent(obj, evType, fn){
 if (obj.addEventListener){
    obj.addEventListener(evType, fn, true);
    return true;
 } else if (obj.attachEvent){
    var r = obj.attachEvent("on"+evType, fn);
    return r;
 } else {
    return false;
 }
}
function removeEvent(obj, evType, fn, useCapture){
  if (obj.removeEventListener){
    obj.removeEventListener(evType, fn, useCapture);
    return true;
  } else if (obj.detachEvent){
    var r = obj.detachEvent("on"+evType, fn);
    return r;
  } else {
    alert("Handler could not be removed");
  }
}

/**
 * Code below taken from - http://www.evolt.org/article/document_body_doctype_switching_and_more/17/30655/
 *
 * Modified 4/22/04 to work with Opera/Moz (by webmaster at subimage dot com)
 *
 * Gets the full width/height because it's different for most browsers.
 */
function getViewportHeight() {
	if (window.innerHeight!=window.undefined) return window.innerHeight;
	if (document.compatMode=='CSS1Compat') return document.documentElement.clientHeight;
	if (document.body) return document.body.clientHeight; 
	return window.undefined; 
}
function getViewportWidth() {
	if (window.innerWidth!=window.undefined) return window.innerWidth; 
	if (document.compatMode=='CSS1Compat') return document.documentElement.clientWidth; 
	if (document.body) return document.body.clientWidth; 
	return window.undefined; 
}

/**
 * POPUP WINDOW CODE
 * Used for displaying DHTML only popups instead of using buggy modal windows.
 *
 * By Seth Banks (webmaster at subimage dot com)
 * http://www.subimage.com/
 *
 * Contributions by Eric Angel (tab index code) and Scott (hiding/showing selects for IE users)
 *
 * Up to date code can be found at http://www.subimage.com/dhtml/subModal
 *
 * This code is free for you to use anywhere, just keep this comment block.
 */

// Popup code

//msgbox constants
var BTNOK = 1;
var BTNCANCEL = 2;
var BTNYES = 4;
var BTNNO = 8;

var MBOK = 1;
var MBOKCANCEL = 3;
var MBYESNO = 12;
var MBYESNOCANCEL = 14;

var ICONEXCLAIM = 16;
var ICONERROR = 32;
var ICONQUESTION = 64;

var IDOK = 1;
var IDCANCEL = 2;
var IDYES = 4;
var IDNO = 8;
var IDCANCEL = 16;

var gPopupMask = null;
var gPopupContainer = null;
var gPopupInner = null;
var gPopFrame = null;
var gFrameMask = null;
var gPopupMsg = null;

var gReturnFunc = null;
var gRetVal;
var gPopupIsShown = false;
var gHideSelects = true;


var gTabIndexes = new Array();
// Pre-defined list of tags we want to disable/enable tabbing into
var gTabbableTags = new Array("A","BUTTON","TEXTAREA","INPUT","IFRAME");	

// If using Mozilla or Firefox, use Tab-key trap.
if (!document.all) {
	//document.onkeypress = keyDownHandler;
        addEvent(document, "keypress", keyDownHandler);
}

function createPopupElements(isAlert, isEmpty)
{
        var element = document.createElement('div');
        if (typeof(element) != 'undefined')
        {
            element.id = "popupMask";
            document.body.appendChild(element);
        }
        element = document.createElement('div');
        if (typeof(element) != 'undefined')
        {
            element.id = "popupContainer";
            document.body.appendChild(element);
            
            var e;
            addEvent(element, "mousedown", function(){initializedrag(e);});
            addEvent(element, "mouseup", function(){stopdrag();});
            addEvent(element, "selectstart", function(){return false;});
            addEvent(element, "keypress", keyDownHandler);
            
            var inner = document.createElement('div');
            if (typeof(inner) != 'undefined')
            {
                inner.id = "popupInner";
                element.appendChild(inner);
                var titlebar = document.createElement('div');
                if (typeof(titlebar) != 'undefined')
                {
                    titlebar.id = "popupTitleBar";
                    inner.appendChild(titlebar);
                    var title = document.createElement('div');
                    if (typeof(title) != 'undefined')
                    {
                        title.id = "popupTitle";
                        titlebar.appendChild(title);
                    }
                    var popControls = document.createElement('div');
                    if (typeof(popControls) != 'undefined')
                    {
                        popControls.id = "popupControls";
                        titlebar.appendChild(popControls);
                        var btnClose = document.createElement('img');
                        if (typeof(btnClose) != 'undefined')
                        {
                            btnClose.src = "resources/images/close.gif";
                            addEvent(btnClose, "click", function() {hidePopWin(false);return true; });
                            popControls.appendChild(btnClose);
                        }
                    }
                }
                if (!isAlert)
                {
                    var popFrame;
                    if (!isEmpty)
                        popFrame = document.createElement('iframe');
                    else
                        popFrame = document.createElement('div');
                    if (typeof(popFrame) != 'undefined')
                    {
                        popFrame.id = "popupFrame";
                        popFrame.name = "popupFrame";
                        popFrame.style.width = "100%";
                        popFrame.style.height = "100%";
                        popFrame.style.background.color="transparent";
                        popFrame.scrolling = "auto";
                        popFrame.frameBorder = "0";
                        popFrame.allowtransparency = "true";
                        inner.appendChild(popFrame);
                    }
                }
                else
                {
                    var popMsg = document.createElement("div");
                    if (typeof(popMsg) != 'undefined')
                    {
                        popMsg.id = "popupMsg";
                        popMsg.style.width = "100%";
                        popMsg.style.height = "100%";
                        inner.appendChild(popMsg);
                    }
                }
            }
            var frameMask = document.createElement('div');
            if (typeof(frameMask) != 'undefined')
            {
                frameMask.id = "popupFrameMask";
                element.appendChild(frameMask);
            }
        }
}
/**
 * Initializes popup code on load.	
 */
function initPopUp(isAlert) {
        gPopupMask = document.getElementById("popupMask");
	gPopupContainer = document.getElementById("popupContainer");
        gPopupInner = document.getElementById("popupInner");
        if (!isAlert)
            gPopFrame = document.getElementById("popupFrame");
        else
            gPopupMsg = document.getElementById("popupMsg");
        gFrameMask = document.getElementById("popupFrameMask");
	
	// check to see if this is IE version 6 or lower. hide select boxes if so
	// maybe they'll fix this in version 7?
	var brsVersion = parseInt(window.navigator.appVersion.charAt(0), 10);
	if (brsVersion <= 6 && window.navigator.userAgent.indexOf("MSIE") > -1) {
		gHideSelects = true;
	}
}

//addEvent(window, "load", initPopUp);

 /**
	* @argument width - int in pixels
	* @argument height - int in pixels
	* @argument url - url to display
	* @argument returnFunc - function to call when returning true from the window.
	*/

function showPopWin(url, width, height, returnFunc, sTitle) {
        
        createPopupElements(false, (url==''));
        initPopUp(false);
        
	gPopupIsShown = true;
        gPopupMsg = null;
	disableTabIndexes();
	gPopupMask.style.display = "block";
	gPopupContainer.style.display = "block";
        gPopFrame.style.display = "block";
        
	// calculate where to place the window on screen
	centerPopWin(width, height);
	
	var titleBarHeight = parseInt(document.getElementById("popupTitleBar").offsetHeight, 10);
	
	gPopupContainer.style.width = width + "px";
	gPopupContainer.style.height = (height+titleBarHeight) + "px";
	// need to set the width of the iframe to the title bar width because of the dropshadow
	// some oddness was occuring and causing the frame to poke outside the border in IE6
	gPopFrame.style.width = parseInt(document.getElementById("popupTitleBar").offsetWidth, 10) + "px";
	gPopFrame.style.height = (height) + "px";
        
        gFrameMask.style.width = gPopupContainer.style.width;
        gFrameMask.style.height = gPopupContainer.style.height;
	
	// set the url
	gPopFrame.src = url;
	
	gReturnFunc = returnFunc;
	// for IE
	if (gHideSelects == true) {
		hideSelectBoxes();
	}
        
        if (sTitle != undefined) 
            document.getElementById("popupTitle").innerHTML = sTitle;
        else
            window.setTimeout("setPopTitle();", 800);

            
}


//shows a custom alert box
/**
	* @argument msg - The message to display
	* @argument height - int in pixels
	* @argument url - url to display
	* @argument returnFunc - function to call when returning true from the window.
*/
function showMsgBox(msg, mbType, returnFunc) 
{

  var alertTitle = "Christopher Online";
    
    //create and show divs
    createPopupElements(true);
    initPopUp(true);
    
    gPopupIsShown = true;
    disableTabIndexes();
    gPopupMask.style.display = "block";
    gPopupContainer.style.display = "block";
    gPopupMsg.style.display = "block";
    
    //adjust width based on message length, allow max. 100 chars
    var maxLen = getMaxLineLength(msg);
    
    maxLen = (maxLen>100)?110:maxLen + 20; //Allow for 25 pixel + 20 pixel margin, in 8 pt font
    gPopupMsg.style.width = maxLen + "ex";
    gPopupContainer.style.width = gPopupMsg.offsetWidth + 4; //Allow for 2 pixel border
    
    //add the p element. Convert newlines in message to BR elements
    var para = gPopupMsg.appendChild(document.createElement("p"));
    var regExp = /\n/g;
    para.innerHTML = msg.replace(regExp, "<BR>");
    
    //icons
    if (mbType & ICONEXCLAIM)
        gPopupMsg.style.backgroundImage = "url(/php2go/Form60/resources/images/alert.png)";
    else if (mbType & ICONQUESTION)
        gPopupMsg.style.backgroundImage = "url(/php2go/Form60/resources/images/question.png)";
    else if (mbType & ICONERROR)
        gPopupMsg.style.backgroundImage = "url(/php2go/Form60/resources/images/error.png)";
    else 
        gPopupMsg.style.backgroundImage = "url(/php2go/Form60/resources/images/alert.png)";
        
    //add the buttons
    var btn;
    
    if (mbType & BTNOK)
    {
        btn = addButton("OK");
        addEvent(btn, "click", function() { gRetVal = IDOK; hidePopWin(true);return true; });
        btn.tabIndex = 0;
        btn.setActive();
    }
    
    if (mbType & BTNYES)
    {
        btn = addButton("Yes");
        addEvent(btn, "click", function() { gRetVal = IDYES; hidePopWin(true);return true; });
        btn.tabIndex = 1;
    }
    
    if (mbType & BTNNO)
    {
        btn = addButton("No");
        addEvent(btn, "click", function() { gRetVal = IDNO; hidePopWin(true);return true; });
        btn.tabIndex = 2;
    }
    
    if (mbType & BTNCANCEL)
    {
        btn = addButton("Cancel");
        addEvent(btn, "click", function() { gRetVal = IDCANCEL; hidePopWin(true);return true; });
        btn.tabIndex = 3;
    }
    
    //adjust the drag mask
    gFrameMask.style.width = gPopupContainer.offsetWidth;
    gFrameMask.style.height = gPopupContainer.offsetHeight;
    
    document.getElementById("popupTitle").innerHTML = alertTitle;

    // calculate where to place the window on screen
    var width = gPopupContainer.offsetWidth;
    var height = gPopupContainer.offsetHeight;
    
    centerPopWin(width, height);
    
    gReturnFunc = returnFunc; 
    
    // for IE
    if (gHideSelects == true) {
            hideSelectBoxes();
    }
    
}

function showAlert(msg)
{
    showMsgBox(msg, MBOK, null);
}

function addButton(btnCaption)
{
    var btn = document.createElement("input");
    btn.type = "button";
    btn.value = btnCaption;
    btn.className = "msgButton";
    return gPopupMsg.appendChild(btn);
}

//
var gi = 0;
function centerPopWin(width, height) {
    if (gPopupIsShown == true) {
            if (width == null || isNaN(width)) {
                    width = gPopupContainer.offsetWidth;
            }
            if (height == null) {
                    height = gPopupContainer.offsetHeight;
            }
            
            var fullHeight = getViewportHeight();
            var fullWidth = getViewportWidth();
            
            var theBody = document.documentElement;
            
            var scTop = parseInt(theBody.scrollTop,10);
            var scLeft = parseInt(theBody.scrollLeft,10);
            
            gPopupMask.style.height = fullHeight + "px";
            gPopupMask.style.width = fullWidth + "px";
            gPopupMask.style.top = scTop + "px";
            gPopupMask.style.left = scLeft + "px";
            
            window.status = gPopupMask.style.top + " " + gPopupMask.style.left + " " + gi++;
            
            var titleBarHeight = parseInt(document.getElementById("popupTitleBar").offsetHeight, 10);
            
            gPopupContainer.style.top = (scTop + ((fullHeight - (height+titleBarHeight)) / 2)) + "px";
            gPopupContainer.style.left =  (scLeft + ((fullWidth - width) / 2)) + "px";
            //alert(fullWidth + " " + width + " " + gPopupContainer.style.left);
    }
}

addEvent(window, "resize", centerPopWin);
addEvent(window, "scroll", centerPopWin);

/**
 * @argument callReturnFunc - bool - determines if we call the return function specified
 */
function hidePopWin(callReturnFunc) {
        var callReturn = (callReturnFunc == true && gReturnFunc != null);
        if (callReturn)
        {
            if (window.frames["popupFrame"])
                gRetVal = window.frames["popupFrame"].returnVal;
        }
        
	gPopupIsShown = false;
	restoreTabIndexes();
        removeEvent(document, "keypress", keyDownHandler);
        
        document.body.removeChild(gPopupContainer);
        document.body.removeChild(gPopupMask);
        gPopupMask = null;
        gPopupContainer = null;
        gPopupInner = null;
        gPopFrame = null;
        gFrameMask = null;
        gPopupMsg = null;
        
	if (callReturn) 
        {
            gReturnFunc(gRetVal);
	}
        
	// display all select boxes
	if (gHideSelects == true) {
		displaySelectBoxes();
	}
}

/**
 * Sets the popup title based on the title of the html document it contains.
 * Uses a timeout to keep checking until the title is valid.
 */
function setPopTitle() {
	if (window.frames["popupFrame"].document.title == null) {
		window.setTimeout("setPopTitle();", 10);
	} else {
		document.getElementById("popupTitle").innerHTML = window.frames["popupFrame"].document.title;
	}

}

// Tab key trap. iff popup is shown and key was [TAB], suppress it.
// @argument e - event - keyboard event that caused this function to be called.
function keyDownHandler(e) 
{
    e = getEvent(e);
    if (gPopupIsShown)
    {
        if (e.keyCode == 9)  return false;
        if (e.keyCode == 27)  
        {
            hidePopWin(true)
            return true;
        }
    }
}

// For IE.  Go through predefined tags and disable tabbing into them.
function disableTabIndexes() {
    if (document.all) {
        var i = 0;
        var iframes = document.getElementsByTagName("IFRAME");
        for (var j = 0; j < gTabbableTags.length; j++) {
            for (var l = 0; l < iframes.length; l++) {
                var tagElements = iframes[l].document.getElementsByTagName(gTabbableTags[j]);
                for (var k = 0 ; k < tagElements.length; k++) {
                        gTabIndexes[i] = tagElements[k].tabIndex;
                        tagElements[k].tabIndex="-1";
                        i++;
                }
            }
            tagElements = document.getElementsByTagName(gTabbableTags[j]);
            for (var k = 0 ; k < tagElements.length; k++) {
                    gTabIndexes[i] = tagElements[k].tabIndex;
                    tagElements[k].tabIndex="-1";
                    i++;
            }
        }
    }
}

// For IE. Restore tab-indexes.
function restoreTabIndexes() {
    if (document.all) {
        var i = 0;
        var iframes = document.getElementsByTagName("IFRAME");
        for (var j = 0; j < gTabbableTags.length; j++) {
            for (var l = 0; l < iframes.length; l++) {
                var tagElements = iframes[l].document.getElementsByTagName(gTabbableTags[j]);
                for (var k = 0 ; k < tagElements.length; k++) {
                        tagElements[k].tabIndex = gTabIndexes[i];
                        tagElements[k].tabEnabled = true;
                        i++;
                }
            }
            var tagElements = document.getElementsByTagName(gTabbableTags[j]);
            for (var k = 0 ; k < tagElements.length; k++) {
                    tagElements[k].tabIndex = gTabIndexes[i];
                    tagElements[k].tabEnabled = true;
                    i++;
            }
        }
    }
}


/**
* Hides all drop down form select boxes on the screen so they do not appear above the mask layer.
* IE has a problem with wanted select form tags to always be the topmost z-index or layer
*
* Thanks for the code Scott!
*/
function hideSelectBoxes() {
    var iframes = document.getElementsByTagName("IFRAME");
    for (var k = 0; k < iframes.length; k++) {
        if (iframes[k].document)
        {
            for(var i = 0; i < iframes[k].document.forms.length; i++) {
                    for(var e = 0; e < iframes[k].document.forms[i].length; e++){
                            if(iframes[k].document.forms[i].elements[e].tagName == "SELECT") {
                                    iframes[k].document.forms[i].elements[e].style.visibility="hidden";
                            }
                    }
            }
        }
    }
    for(var i = 0; i < document.forms.length; i++) {
        for(var e = 0; e < document.forms[i].length; e++){
                if(document.forms[i].elements[e].tagName == "SELECT") {
                        document.forms[i].elements[e].style.visibility="hidden";
                }
        }
    }
}

/**
* Makes all drop down form select boxes on the screen visible so they do not reappear after the dialog is closed.
* IE has a problem with wanted select form tags to always be the topmost z-index or layer
*/
function displaySelectBoxes() {
    var iframes = document.getElementsByTagName("IFRAME");
    for (var k = 0; k < iframes.length; k++) {
        if (iframes[k].document)
        {
            for(var i = 0; i < iframes[k].document.forms.length; i++) {
                    for(var e = 0; e < iframes[k].document.forms[i].length; e++){
                            if(iframes[k].document.forms[i].elements[e].tagName == "SELECT") {
                            iframes[k].document.forms[i].elements[e].style.visibility="visible";
                            }
                    }
            }
        }
    }
    for(var i = 0; i < document.forms.length; i++) {
        for(var e = 0; e < document.forms[i].length; e++){
                if(document.forms[i].elements[e].tagName == "SELECT") {
                        document.forms[i].elements[e].style.visibility="visible";
                }
        }
    }
}

var dragapproved=false;
var ie5=document.all&&document.getElementById;
var ns6=document.getElementById&&!document.all;

function drag_drop(e)
{
    e = getEvent(e);
    if (ie5 && dragapproved && event.button==1)
    {
        document.getElementById("popupContainer").style.left=tempx+event.clientX-offsetx+"px";
        document.getElementById("popupContainer").style.top=tempy+event.clientY-offsety+"px";
    }
    else if (ns6 && dragapproved)
    {
        document.getElementById("popupContainer").style.left=tempx+e.clientX-offsetx+"px";
        document.getElementById("popupContainer").style.top=tempy+e.clientY-offsety+"px";
    }
}

function initializedrag(e)
{
    e = getEvent(e);
    var evtSrc = getEventSrc(e);
    if (document.getElementById("popupTitle") == evtSrc ||
        document.getElementById("popupTitleBar") == evtSrc)
    {
        offsetx= e.clientX;
        offsety= e.clientY;
        
        //need to do this to capture mouse move
        //gPopFrame.style.display="none"; 
        gFrameMask.style.display="block";
        
        tempx=parseInt(gPopupContainer.style.left);
        tempy=parseInt(gPopupContainer.style.top);
        
        dragapproved=true;
        gPopupContainer.onmousemove=drag_drop;
        document.onmousemove=drag_drop;
    }
}


function stopdrag()
{
    dragapproved=false;
    gPopupContainer.onmousemove=null;
    
    //release mouse to the contained page
    //gPopFrame.style.display=""; 
    gFrameMask.style.display="none";
}

//gets maximum line length of a string
function getMaxLineLength(msg)
{
        var regExp = /\n/;
        var ar = msg.split(regExp);
        var maxLength = 0;
        if (ar.length > 0)
        {
            for (var i = 0; i<ar.length; i++)
            {
                if (ar[i].length > maxLength)
                    maxLength = ar[i].length;
            }
        }
        else 
            maxLength = msg.length;
        return maxLength;
}

if(document.getElementById) {
	window.alert = function(txt) {
		showAlert(txt);
	}
}
