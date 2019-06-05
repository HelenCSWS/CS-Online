var F60_USER_PASS = "fw60_" + btoa("F60_USER_PASS");

var F60_USER_NAME = "fu60_" + btoa("F60_USER_NAME");

var F60_COOKIE_FLAG = "fcook60_" + btoa("F60_COOKIE_FLAG");

function trimString(str) 
{
  str = this != window? this : str;
  return str.replace(/^\s+/g, '').replace(/\s+$/g, '');
}

String.prototype.trim = trimString;

function getWindowHeight() 
{
    var windowHeight=0;
    if (typeof(window.innerHeight)=='number')
        windowHeight=window.innerHeight;
    else 
    {
        if (document.documentElement &&
          document.documentElement.clientHeight) 
            windowHeight = document.documentElement.clientHeight;
        else 
        {
            if (document.body && document.body.clientHeight) 
                windowHeight=document.body.clientHeight;
        }
    }
    return windowHeight;
}

function findPosition(obj,pType) {
	cur = 0;
	if(obj.offsetParent) {		
		while(obj.offsetParent) {
			cur+=pType?obj.offsetLeft:obj.offsetTop;
			obj = obj.offsetParent;
		}
	}
	return cur;
}

function gotoTop()
{
  if (window.parent.frames.length != 0)
    window.open(window.location,'_top');
  else
    return;
}

function getFrame(name) 
{
  for( var i = 0; i < window.frames.length; i++ ) {
   try {
         if( window.frames[ i ].name == name ) {
            return window.frames[ i ];
         }
   } catch( e ) {}
  }
    return null;
}

function gotoURL(URL)
{
    window.location = "main.php";
}

function gotoLastPage()
{
    gotoURL('lastpage.php');
}

function getEvent(e)
{
    //handle w3c vs IE
    return ((e) ? e : (window.event) ? window.event: null);
}

function getEventSrc(e)
{
    return ((e && e.srcElement) ? e.srcElement : (e.target) ? e.target : null);
}

function getElementsByClass(searchClass,node,tag) {
    var classElements = new Array();
    if ( node == null )
        node = document;
    if ( tag == null )
        tag = '*';
    var els = node.getElementsByTagName(tag);
    var elsLen = els.length;
    var pattern = new RegExp("(^|\\s)"+searchClass+"(\\s|$)");
    for (i = 0, j = 0; i < elsLen; i++) {
        if ( pattern.test(els[i].className) ) {
            classElements[j] = els[i];
            j++;
        }
    }
    return classElements;
}

function chkMaskINTEGER(field,evt) {
    evt = getEvent(evt)
    var keyCode = (_dom == 1 || _dom == 2 ? window.event.keyCode : evt.which);
    var key = String.fromCharCode(keyCode);
    var isKey = (String('0123456789/').indexOf(key) != -1);
    var isAction = (String('0,8,13').indexOf(keyCode) != -1 || evt.ctrlKey);
    return (isKey || isAction);
}

function printPage()
{
    var mainTD = document.getElementById('MainFrame');
    var printWindow = window.open();
    printWindow.document.write('<LINK REL="stylesheet" TYPE="text/css" HREF="resources/css/Form60app.css">');
    printWindow.document.write(mainTD.outerHTML);
    printWindow.document.write('<SCRIPT language=javascript>window.print();</SCRIPT>');
}

function showPopWin(url, width, height, returnFunc, sTitle) 
{
    parent.showPopWin(url, width, height, returnFunc, sTitle);
}

function hidePopWin(callReturnFunc)
{
    parent.hidePopWin(callReturnFunc)
}

function handleEnter(f, e, fn)
{
    var charCode = (e.which) ? e.which : e.keyCode;
    if ((charCode == 13) && (fn != null)) fn();
}

function stopEvt()
{
    var e = null;
    e = getEvent(e);
    if (e)
       stopEvent(e);
}


function loadCookie()
{
// console.log(getCookie(F60_COOKIE_FLAG));
    if (getCookie(F60_COOKIE_FLAG) == null) {
        if (getCookie("F60_USER_NAME") != null)
            document.getElementById("username").value = getCookie("F60_USER_NAME");
        else
            document.getElementById("username").value = "";

        if (getCookie("F60_USER_NAME_CHK") == "1") {
            document.getElementById("uname").checked = true;
        }


        if (getCookie("F60_USER_PASS") != null)
            document.getElementById("userpass").value = getCookie("F60_USER_PASS");
        else
            document.getElementById("userpass").value = "";

        if (getCookie("F60_USER_PASS_CHK") == "1") {
            document.getElementById("pword").checked = true;
        }
    }
    else {
        if (getCookie(F60_USER_NAME) != null)
            document.getElementById("username").value = atob(getCookie(F60_USER_NAME));
        else
            document.getElementById("username").value = "";

        if (getCookie("F60_USER_NAME_CHK") == "1") {
            document.getElementById("uname").checked = true;
        }


        if (getCookie(F60_USER_PASS) != null)
            document.getElementById("userpass").value = atob(getCookie(F60_USER_PASS));
        else
            document.getElementById("userpass").value = "";

        if (getCookie("F60_USER_PASS_CHK") == "1") {
            document.getElementById("pword").checked = true;
        }
    }
}

function F60GetCookie(sName) 
{
    var re = new RegExp( "(\;|^)[^;]*(" + sName + ")\=([^;]*)(;|$)" );
    var res = re.exec( document.cookie );
    return res != null ? res[3] : null;
}

function F60SetCookie (name,value,nDays ) 
{
    var expires = "";
    if ( nDays ) {
            var d = new Date();
            d.setTime( d.getTime() + nDays * 24 * 60 * 60 * 1000 );
            expires = "; expires=" + d.toGMTString();
    }

    document.cookie = name + "=" + value + expires + "; path=/";
}

function F60DeleteCookie (name)
{
    if (F60GetCookie(name))
    {
        F60SetCookie( name, "", -1 );
    }

}

function submitLogin()
{
    F60DeleteCookie(F60_COOKIE_FLAG);
    F60SetCookie(F60_COOKIE_FLAG, 1, 30);
    
    F60DeleteCookie("F60_USER_NAME")
    F60DeleteCookie("F60_USER_PASS")

    var username = document.getElementById("username").value;
    if (username=="")
    {
        document.getElementById("username").focus();
        return false;
    }
    var password = document.getElementById("userpass").value;
    if (password=="")
    {
        document.getElementById("userpass").focus();
        return false;
    }
    if (F60LoginForm_submit)
    {
     		
         if( document.getElementById("uname").checked)
         {
            username = btoa(username);
            F60SetCookie(F60_USER_NAME,username, 30);
            F60SetCookie("F60_USER_NAME_CHK",1, 30);
         }
         else
         {
            F60DeleteCookie(F60_USER_NAME);
            F60DeleteCookie("F60_USER_NAME_CHK");
         }
         
         if( document.getElementById("pword").checked)
         {
             password = btoa(password);
            F60SetCookie(F60_USER_PASS,password, 30);
            F60SetCookie("F60_USER_PASS_CHK",1, 30);
         }
         else
         {
            F60DeleteCookie(F60_USER_PASS);
            F60DeleteCookie("F60_USER_PASS_CHK");
         }
         
         document.F60LoginForm.submit();
    }
}

if(document.getElementById) {
        /*if (parent.showAlert != 'undefined')
            window.alert = function(txt) {
                    parent.showAlert(txt);
            }*/
}

if (parent.childPageMousedown && parent.browser)
{
    if (parent.browser.isIE)
    {
        document.onmousedown = parent.childPageMousedown;
    }
    else
        document.addEventListener("mousedown", parent.childPageMousedown, true);
}



//!----------------------------------------------------------------
// @function	chkFLOAT
// @desc		Aplica máscara de número decimal automaticamente
// @param		field Field object		Campo de um formulário
// @param		event Event object		Evento do teclado
// @param		llen Integer			Tamanho da parte inteira do número
// @param		rlen Integer			Tamanho da parte decimal do número
// @return		Boolean
//!----------------------------------------------------------------
//chkFLOAT
function chkffff(f,e,l,r)
{
    chkeFLOAT(f,e,l,r);
}
function chkFLOAT(f,e,l,r) {
 //	return true;

	var keys = '0123456789.';
	var acts = '0,8,13';
	var code = (document.all ? window.event.keyCode : e.which);
	var key = String.fromCharCode(code);
	if (key == ',') key == '.';
	var len = f.value.length;
	var dpos = f.value.indexOf('.');
	var ss = getEditCaretPos(f);
	var se = getSelectionEnd(f);
	var nr = (keys.indexOf(key) != -1 && key != '.' && key != ',');
	var dot = (key == '.');
	if (keys.indexOf(key) == -1 && acts.indexOf(code) == -1) {
		return false;
	} else if (nr) {
		if (key == '0' && ss == 0 && len == 0) {
			f.value = '0.';
			setEditCaretPos(f,2);
			stopEvent(e);
			return true;
		} else if (l != null && dpos == -1 && len == l) {
			if (ss == se && ss < len) {
				f.value = f.value.substring(0,ss)+key+f.value.substring(ss,l-1)+'.'+f.value.substring(l-1);
				setEditCaretPos(f,ss+1);
				stopEvent(e);
				return true;
			} else if (ss < se) {
				f.value = f.value.substring(0,ss)+key+f.value.substring(se);
				setEditCaretPos(f,ss+1);
				stopEvent(e);
				return true;
			} else {
				f.value = f.value+'.';
				return true;
			}
		} else if (l != null && dpos != -1 && f.value.substring(0,dpos).length == l) {
			if (ss == se) {
				if (ss <= dpos) return false;
				if (r != null && f.value.substring(dpos+1).length == r) return false;
				if (ss < len) {
					f.value = f.value.substring(0,ss)+key+f.value.substring(ss);
					setEditCaretPos(f,ss+1);
					stopEvent(e);
				}
				return true;
			} else {
				if (se <= dpos || ss > dpos || (ss+(len-se) < l)) {
					f.value = f.value.substring(0,ss)+key+f.value.substring(se);
					setEditCaretPos(f,ss+1);
					stopEvent(e);
					return true;
				}
				return false;
			}
		} else if (r != null && dpos != -1 && f.value.substring(dpos+1).length == r) {
			if (ss == se) {
				if (ss > dpos) return false;
				if (l != null && f.value.substring(0,dpos).length == l) return false;
				f.value = f.value.substring(0,ss)+key+f.value.substring(ss);
				setEditCaretPos(f,ss+1);
				stopEvent(e);
				return true;
			} else {
				if (se <= dpos || ss > dpos || (ss+(len-se) < l)) {
					f.value = f.value.substring(0,ss)+key+f.value.substring(se);
					setEditCaretPos(f,ss+1);
					stopEvent(e);
					return true;
				}
				return false;
			}
		} else return true;
	} else if (dot) {
		if (ss == 0 && (dpos == -1 || (dpos >= ss && dpos < se))) {
			f.value = (se > ss ? '0.'+f.value.substring(se) : '0.'+f.value);
			setEditCaretPos(f,2);
			stopEvent(e);
			return true;
		} else {
			return ((dpos == -1 && (l == null || ss <= l)) || (dpos >= ss && dpos < se));
		}
	} else {
		return true;
	}
}

function format2Currency(price_element)
{
		var prefix="$";
		var wd;
		var tempnum=price_element.value;
		if (tempnum != "")
		{
			tempnum = tempnum.replace(".00","");
			
			if (price_element.value.charAt(0)=="$")
				tempnum = tempnum.replace("$","");
			wd="w";
			
			for (i=0;i<tempnum.length;i++)
			{
				if (tempnum.charAt(i)==".")
				{
					wd="d";
					break;
				}
			}
		
			if (wd=="w")
				price_element.value=prefix+tempnum+".00";
			else
			{
				if (tempnum.charAt(tempnum.length-2)==".")
				{
				// var test =prefix+tempnum+"0";
					price_element.value=prefix+tempnum+"0";
				}
				else
				{
					tempnum=Math.round(tempnum*100)/100;
					price_element.value=prefix+tempnum;
				}
			}
			var temValue =price_element.value;
			var grpValue =temValue.split(".");
			if (grpValue[1].length==1)
			{
				temValue = temValue+"0";
			}
			price_element.value =temValue;
	}

}

function formatCurrency2Number(price_element)
{
    var price=price_element.value;
    var number =price.replace("$","");
   	price_element.value = number;
}

function closePage()
{
	var link ="main.php";
	document.location = link;
	
	stopEvent(window.event);
	return true;
}