//functions for first page
function uploadSSDS_start()
{
	 var province_id = document.getElementById("province_id").value;
	 
	 
	 
    if(uploadSSDS_submit())
    {
        var bttnStart = document.getElementById("bttnStart");
        var btnCancel = document.getElementById("btnCancel");
        if(province_id==1)
        {
	        var file_input = document.getElementById("file_name");
	        var bcldb_file_input = document.getElementById("bcldb_file_name");
	        
	        if (bcldb_file_input.value=="")
	        {
					alert("Please select the BCLDB sales file.");
					
					bcldb_file_input.focus();
					return false;
				}
				else if(file_input.value=="")
				{
					alert("Please select the Licensee sales file.");
					file_input.focus();
					return false;
				}
	      }
	      else
	      {
				var file_input = document.getElementById("ab_file_name");
				
				if (file_input.value=="")
				{
					alert("Please select the Alberta licensee sales file.");
					file_input.focus();
					return false;
				}
			}
			
        var SSDS_step = document.getElementById("SSDS_step");
        SSDS_step.value = 2;
        bttnStart.disabled = true;
        btnCancel.disabled = true;
        var tdmsg = document.getElementById("tdmessage");
        tdmessage.style.visibility="visible";
        submitAction("uploadSSDS", "bttnStart");
        file_input.disabled = true;
        if(province_id==1)
		  		bcldb_file_input.disabled = true;   
    }
}

function uploadImport_start()
{
	var bttnStart = document.getElementById("bttnUpload");
	var btnCancel = document.getElementById("btnCancel");
	var btnBack = document.getElementById("btnBack");
	var SSDS_step = document.getElementById("SSDS_step");
	SSDS_step.value = 3;
	bttnStart.disabled = true;
	btnCancel.disabled = true;
	btnBack.disabled = true;
	var tdmsg = document.getElementById("tdmessage");
	tdmessage.style.visibility="visible";
	submitAction("uploadSSDS", "bttnUpload");
        
      
     
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
	 if(document.getElementById("SSDS_step").value==1)
	 {
		if(document.getElementById("province_id").value==1)
		{
			document.getElementById("tr_bcldb").style.display="block";
			document.getElementById("tr_license").style.display="block";
			document.getElementById("tr_ab").style.display="none";
			
			var file_input = document.getElementById("bcldb_file_name");
			
			if (file_input) file_input.focus();
		}
		else if( document.getElementById("province_id").value==2)
		{
			document.getElementById("tr_bcldb").style.display="none";
			document.getElementById("tr_license").style.display="none";
			document.getElementById("tr_ab").style.display="block";
			
			var file_input = document.getElementById("file_ab_name");
			
			if (file_input) file_input.focus();
		}
	}
	else if(document.getElementById("SSDS_step").value==2)
	{
	 	document.getElementById("province_id").style.display="none";
		if(document.getElementById("province_id").value==1)
		{
		document.getElementById("tr_msg_license").style.display="block";
		document.getElementById("tr_msg_bcldb").style.display="block";
		document.getElementById("tr_msg_ab").style.display="none";
		
		var file_input = document.getElementById("bcldb_file_name");
		
		if (file_input) file_input.focus();
		}
		else if( document.getElementById("province_id").value==2)
		{
			document.getElementById("tr_msg_license").style.display="none";
			document.getElementById("tr_msg_bcldb").style.display="none";
			document.getElementById("tr_msg_ab").style.display="block";
		
			var file_input = document.getElementById("file_ab_name");
		
			if (file_input) file_input.focus();
		}
	}
	else if(document.getElementById("SSDS_step").value==3)
	{
		document.getElementById("province_id").style.display="none";
		if(document.getElementById("province_id").value==2)
		{
			document.getElementById("tdView").style.display="none";
		}
		
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