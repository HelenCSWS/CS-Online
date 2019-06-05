//functions for first page
function uploadFile_start()
{
	 

    if(uploadABDailyFile_submit())
    {
     

        var btnStart = document.getElementById("bttnStart");
        var btnCancel = document.getElementById("btnCancel");
        
				var file_input = document.getElementById("ab_file_name");

				if (file_input.value=="")
				{
					alert("Please select the uploading file sales file.");
					file_input.focus();
					return false;
				}
	
			
        var SSDS_step = document.getElementById("upload_step");
        SSDS_step.value = 2;
        btnStart.disabled = true;
        btnCancel.disabled = true;
        var tdmsg = document.getElementById("tdmessage");
        tdmessage.style.visibility="visible";
    
        submitAction("uploadABDailyFile", "bttnStart");
        file_input.disabled = true;
    
    }
}

function closePage()
{
	var link ="main.php";
	document.location = link;
	
	stopEvent(window.event);
	return false;
}

function uploadImport_start()
{

	var btnStart = document.getElementById("bttnUpload");
	var btnCancel = document.getElementById("btnCancel");
	var btnBack = document.getElementById("btnBack");
	var SSDS_step = document.getElementById("upload_step");
	SSDS_step.value = 3;
	btnStart.disabled = true;
	btnCancel.disabled = true;
	btnBack.disabled = true;
	var tdmsg = document.getElementById("tdmessage");
	tdmessage.style.visibility="visible";
	submitAction("uploadABDailyFile", "bttnUpload");
      
      
     
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

function uploadSSDS_close()
{
    gotoLastPage();
    return true;
}
function changeProvince()
{
	setFristFocus();
}

function setFristFocus()
{
 //alert(document.getElementById("province_id").value);
	if(document.getElementById("upload_step").value==1)
	{	
		document.getElementById("tr_ab").style.display="block";			
		var file_input = document.getElementById("ab_file_name");
	}
	else if(document.getElementById("upload_step").value==2)
	{
		document.getElementById("tr_msg_ab").style.display="none";
	
		var file_input = document.getElementById("file_ab_name");
	
		if (file_input) file_input.focus();	
	}
	else if(document.getElementById("upload_step").value==3)
	{	
	 		if(document.getElementById("tdView")!=null)
				document.getElementById("tdView").style.display="none";
	}
}
function printSSDSHelp()
{
    window.open("SSDS_help.html","test","height=600,width=800,status=yes,toolbar=no,menubar=yes,location=no,resizable=yes,scrollbars=yes");
}

function showExcelReport(sale_month, sale_year, user_id, store_type_id)
{
    var sURL = "main.php?report_page_name=excelSalesReport&sale_month=" + sale_month + "&sale_year=" + sale_year + "&user_id=" + user_id + "&store_type_id=" + store_type_id;

    window.open(sURL, "SalesReport","height=600,width=800,status=yes,toolbar=no,menubar=yes,location=no,resizable=yes,scrollbars=yes");
}

function showExcelReportByNewRule(sale_month, sale_year, user_id, commission_type_id, province_id)
{

 	
    var sURL = "main.php?report_page_name=excelSalesReportByNewRule&sale_month=" + sale_month + "&sale_year=" + sale_year + "&user_id=" + user_id + "&commission_type_id=" + commission_type_id+"&province_id="+province_id;

    window.open(sURL, "SalesReport","height=600,width=800,status=yes,toolbar=no,menubar=yes,location=no,resizable=yes,scrollbars=yes");
}

function runReport()
{
	var fsyear = document.getElementById("sale_year").value;
	var is_recreate=0;
	var sale_month = document.getElementById("sale_month").value;
	is_recreate=document.getElementById("is_recreate").value;
	
	slink = "main.php?page_name=selectSSDSMonth&sale_year="+fsyear+"&is_recreate="+is_recreate+"&sale_month="+sale_month;
		
	document.location = slink;
	stopEvt();
	return false;
    
}