//functions for first page
function comparecustomers_start()
{
	if(customerCompare_submit())
	{
//        var nomsg =;
        document.getElementById("nomessage").style.display = "none";
         document.getElementById("tdmessage").style.display = "block";
      // var msg = document.getElementById("tdmessage");
      //  msg

	//	msg.innerHTML = "Please wait while the file is being uploaded...";
		var btn = document.getElementById("bttnStart");
		btn.style.visibility = "hidden";
		window.cursor = "wait";
		var form = document.getElementById("customerCompare");
//		alert(form);
		form.submit();
//		customerCompare.submit();
	}
}
//clear the error message while reslect another file
function delMsg()
{
      //  alert(document.getElementById("errmsg").value);
        document.getElementById("errmsg").value = "";
}
//functions for second page
var comparecustomers_req = false;
var comparecustomers_stop = true;
var comparecustomers_session_id = false;

function comparecustomers_run()
{
	var btn = document.getElementById("bttnAction");
	if(btn.value == "Start" || btn.value == "Continue") {
		//alert("start");
		btn.value = "Stop";
		comparecustomers_updatemsg("Click on Stop to pause.");
		//var pct = document.getElementById("percentage")
		//pct.innerHTML = "xx%";
		comparecustomers_stop = false;
		if(!comparecustomers_session_id) {
			var cntl = document.getElementById("cc_session_id");
			comparecustomers_session_id = cntl.value;
		}
		//send first request
		comparecustomers_sendrequest();
	} else if(btn.value == "Stop") {
		//alert("stop");
		btn.value = "Continue";
		comparecustomers_stop = true;
		comparecustomers_updatemsg("Click on Continue to resume.");
	} else if(btn.value == "Next") {
		var cc = document.getElementById("cc_session_id");
		document.location="main.php?page_name=customercpreport&cc_session_id=" + cc.value;
	}
	return false;
}

function comparecustomers_process_result(res)
{
	//alert(res);
	var value = comparecustomers_getnodevalue(res, "result");
	if(value)
	{
	//	alert(value);
		var pct = document.getElementById("percentage")
		pct.innerHTML = value + "%";
		comparecustomers_updatepbar(value);
		if(value < 100)
		{
			//continue sending requests
			if(!comparecustomers_stop)
				comparecustomers_sendrequest();
		}
		else
		{
			//alert("continue");
			var btn = document.getElementById("bttnAction");
			btn.value = "Next";
			comparecustomers_updatemsg("Click on the Next button to view the results of the compare process.");
			pct.innerHTML = "Done";
		}
	}
/*	value = comparecustomers_getnodevalue(res, "total");
	if(value)
	{
		//alert(value);
		var ttl = document.getElementById("valid_records");
		ttl.innerHTML = value;
	}*/
	if(res.indexOf("error") != -1)
	{
		var ttl = document.getElementById("form_client_errors");
		ttl.innerHTML = res;
		ttl.style.display = "block";
	}
}

function comparecustomers_processReqChange()
{
    // only if req shows "loaded"
    if (comparecustomers_req.readyState == 4) {
        // only if "OK"
        if (comparecustomers_req.status == 200) {
        	var res = comparecustomers_req.responseText;
        	comparecustomers_process_result(res);
        } else {
            alert("There was a problem retrieving the XML data:\n" +
                comparecustomers_req.statusText);
        }
    }
}

function comparecustomers_sendrequest()
{
	comparecustomers_getreqobj();
	if(comparecustomers_req)
	{
		var url = document.URL;
		var pos = url.indexOf("main.php");
		if(pos != -1) {
			url = url.substring(0, pos+8);
			url += "?page_name=customerComapreExec&cc_session_id=" + comparecustomers_session_id;
			url += "&random=" + comparecustomers_getrandom();
		}
	//	alert(url);
		comparecustomers_req.onreadystatechange = comparecustomers_processReqChange;
		comparecustomers_req.open("GET", url, true);
		comparecustomers_req.send("");
	}
}

function comparecustomers_getreqobj()
{
    // branch for native XMLHttpRequest object
    if(window.XMLHttpRequest) {
    	try {
			comparecustomers_req = new XMLHttpRequest();
        } catch(e) {
			comparecustomers_req = false;
        }
    // branch for IE/Windows ActiveX version
    } else if(window.ActiveXObject) {
       	try {
        	comparecustomers_req = new ActiveXObject("Msxml2.XMLHTTP");
      	} catch(e) {
        	try {
          		comparecustomers_req = new ActiveXObject("Microsoft.XMLHTTP");
        	} catch(e) {
          		comparecustomers_req = false;
        	}
		}
    }
}

function comparecustomers_updatepbar(value)
{
	var bar = document.getElementById("pbar");
	var cont = document.getElementById("pbar_cont");
	var total = cont.style.width;
	var pos = total.indexOf("px");
	total = total.substring(0, pos);
//	alert(total);
//	alert(bar.style.width);
	var w = total * value / 100;
	bar.style.width = w + "px";
}

function comparecustomers_updatemsg(value)
{
	var msg = document.getElementById("msg");
	msg.innerHTML = value;
}

function comparecustomers_getrandom()
{
//	random();
	return Math.round(Math.random()*9999999+1);
}

function comparecustomers_getnodevalue(res, node)
{
	var s;
	var e;
	var otag = "<" + node + ">";
	var ctag = "</" + node + ">";
	var l = otag.length;
	if(res)
	{
		s = res.indexOf(otag);
		e = res.indexOf(ctag);
		if(s != -1 && e != -1 && s+l < e) {
			var value = res.substring(s+l, e);
			return value;
		}
	}
	return false;
}

function printReports()
{
//	showPopWin('main.php?page_name=customercpreport', 800, 600, null, "test");
    window.open("main.php?page_name=mkprintreport","test","height=600,width=800,status=yes,toolbar=no,menubar=yes,location=no,resizable=yes,scrollbars=yes");
}
function printHelp()
{
//	showPopWin('main.php?page_name=customercpreport', 800, 600, null, "test");
    window.open("cc_help.html","test","height=600,width=800,status=yes,toolbar=no,menubar=yes,location=no,resizable=yes,scrollbars=yes");
}

function exprotCPToExcel(session_id)
{
	var url = "main.php?page_name=customerCompare&step=4&cc_session_id=" + session_id;
    window.open(url,"test","height=400,width=800,status=yes,toolbar=no,menubar=yes,location=no,resizable=yes");
}

function setFristFocus()
{
    delMsg();
    document.getElementById("file_name").focus();
}


