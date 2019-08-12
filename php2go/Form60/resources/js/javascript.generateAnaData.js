
//clear the error message while reslect another file
function initpage()
{
    checkASAvaData();
}

function checkASAvaData()
{
	var sale_year = document.getElementById("sale_year").value;
	
	var sale_month = document.getElementById("sale_month").value;
	
	var province_id = document.getElementById("province_id").value;

     xajax_BI_checkASAvaData(sale_year,sale_month,province_id);
}

function setAvaMsg(isAva)
{	
 	document.getElementById('td_msg').innerHTML = "";
    
    var province_id = document.getElementById("province_id").value;
    
    var reportName = "SalesAnalysisEmailToBC"; // BC
    if(province_id !=1)
    {
        reportName = "SalesAnalysisEmailToAB"; // AB
    }
    
   // var hyperLink ="csonline.christopherstewart.com:8088/"+reportName;
    
    var hyperLink =reportName;
    
    hyperLink = "<a target='_blank' href='"+ hyperLink+ "'>available.<a/>";
    
     
	if(isAva)
	{
		document.getElementById('td_msg').innerHTML = "Current month's data is "+hyperLink;
		document.getElementById('td_msg').style.color = "black";
	}
	else
	{
			document.getElementById('td_msg').innerHTML = "Current month's data is not available.";
			document.getElementById('td_msg').style.color = "red";
	}
}

function generateASAnaData()
{
	var sale_year = document.getElementById("sale_year").value;
	
	var sale_month = document.getElementById("sale_month").value;
	
	var province_id = document.getElementById("province_id").value;


     xajax_BI_generateASAnaData(sale_year,sale_month,province_id);
}

function printHelp()
{
//	showPopWin('main.php?page_name=customercpreport', 800, 600, null, "test");
    window.open("cc_help.html","test","height=600,width=800,status=yes,toolbar=no,menubar=yes,location=no,resizable=yes,scrollbars=yes");
}
function openHelp(id)
{
//	showPopWin('main.php?page_name=customercpreport', 800, 600, null, "test");

}


function setFristFocus(msgid)
{
   delMsg();
   
   
   
    document.getElementById("file_name").focus();
}

function showBreakdownReport(user_id, user_name, report_type,sale_month, sale_year)
{
    var sURL = "main.php?report_page_name=excelABBreakDownReport&user_id=" + user_id + "&report_type=" + report_type+"&user_name="+user_name+"&sale_month="+sale_month+"&sale_year="+sale_year;
    
   // var sURL = "main.php?report_page_name=excelABMonthlyAlloReport";
    
	window.open(sURL, "SalesReport","height=600,width=800,status=yes,toolbar=no,menubar=yes,location=no,resizable=yes,scrollbars=yes");
}
function viewReports(pageid)
{
		var openpage=true;
		if(pageid==0)
		{
			var fsyear = document.getElementById("sale_year").value;
			var is_recreate=0;
			var sale_month = document.getElementById("sale_month").value;
			
			is_recreate=document.getElementById("is_recreate").value;
			
			slink = "main.php?page_name=selectSSDSMonth&sale_year="+fsyear+"&is_recreate="+is_recreate+"&sale_month="+sale_month;
			
		}
        
        else if(pageid==1)
        {
            var period_id=document.getElementById("sale_month").value;
            if(period_id.length!=0)
            {
         	                 
             	var fsyear = document.getElementById("sale_year").value;
                var is_recreate=0;
                
                if (document.getElementById("recreate_report").checked)
                    is_recreate=1;

    			//slink = "main.php?page_name=selectPeriod&period_id="+period_id+"&is_recreate="+is_recreate;
                  //slink = "main.php?page_name=selectStoreType&period_id="+period_id+"&is_recreate="+is_recreate+"&fiscal_year="+fsyear;
                  
                  //slink = "main.php?page_name=getCaWines&sale_month="+period_id+"&is_recreate="+is_recreate+"&user_id=-1"+"&store_type=-1"+"&users=3"+"&sale_year="+fsyear;
                  
                  slink="main.php?page_name=summaryreport&user_id=-1&sale_month="+period_id+"&sale_year="+fsyear+"&store_type=-1&users=3&is_recreate="+is_recreate;
                  
                
            }
            else
                openpage=false;

        }
        else if(pageid==2)
        {

            var user_id =document.getElementById("user_id").value;
            var store_type =document.getElementById("store_type").value;
            var period_id =document.getElementById("sale_month").value;
            var is_recreate =document.getElementById("is_recreate").value;
            var users =document.getElementById("users").value;
            var fiscal_year =document.getElementById("fiscal_year").value;
            if(user_id==0)
            {
				 slink="main.php?page_name=summaryreport&user_id="+user_id+"&sale_month="+period_id+"&sale_year="+fiscal_year+"&store_type="+store_type+"&users="+users+"&is_recreate="+is_recreate;
 
			}
			else
			{
             	slink = "main.php?page_name=getCaWines&sale_month="+period_id+"&is_recreate="+is_recreate+"&user_id="+user_id+"&store_type="+store_type+"&users="+users+"&fiscal_year="+fiscal_year;
             }
        }
      

      if(openpage==true)
        {
    
           document.location = slink;
           stopEvt();
           return false;
        }
        else
        {
           // alert("can't open!");
            return true;
        }
}

/*------------------------- select period page ----------------------------*/

function closePage()
{
      document.location = "main.php";
      stopEvt();
      return false;
}


function checkRecreate()
{

	if(document.getElementById("is_recreate").value==1)
	{
		
		document.getElementById("recreate_report").checked=true;
	}
	
}

 function getMonthBySaleYear(fiscal_year)
{

   xajax_getMonthBySaleYear(fiscal_year);
}

/*-----------------------  select storyType -------------------------------*/

function setStoreType(typeId)
{
    document.getElementById("store_type").value=typeId;
}


/*-----------------------  getCacases -------------------------------*/

function setForm(pageid)
{
 	if(pageid==0)
 	{
 // alert("what");
 
		if( document.getElementById("is_recreate").value==1)
	{
		
			document.getElementById("recreate_report").checked=true;
		}
	
	}
	else if (pageid==1)//
	{
	 
	  /* <tr><td class="label"> <input  type="radio" name="rdoType" id="rdoType" checked onclick=setStoreType(-1)>All store types</td></tr>
                <tr><td class="label"> <input type="radio" name="rdoType" id="rdoType"  onclick=setStoreType(3)> Licensee</td></tr>
                <tr><td class="label"> <input type="radio" name="rdoType" id="rdoType"  onclick=setStoreType(1)> LRS</td></tr>
                <tr><td class="label"> <input  type="radio" name="rdoType" id="rdoType" onclick=setStoreType(2)> Agency</td></tr>
*/
			if(document.getElementById("store_type").value=="-1")
				selectStoreType.rdoType[0].checked = true;
			if(document.getElementById("store_type").value=="1")
				selectStoreType.rdoType[2].checked = true;
			if(document.getElementById("store_type").value=="2")
				selectStoreType.rdoType[3].checked = true;
			if(document.getElementById("store_type").value=="3")
				selectStoreType.rdoType[1].checked = true;



	 }
	else if (pageid==2)//get ca wines
	{
	
	    var users= document.getElementById("users").value;
	    var user_id= document.getElementById("user_id").value;
	    var trName ="";
	    var ctlName="";
	    var nStart =1;
	    if(user_id!=-1)
	    {
	        users=1;
	        nStart =1;
	    }
	    
		
		    for(i=nStart;i<=users;i++)
		    {
		        trName = "trUser"+i;
		        ctlName = "total_cases"+i;
		        
		        
		        document.getElementById(trName).style.display="block";
		        document.getElementById(ctlName).style.borderColor ="#A9A9A9";//7F9DB9
		
		
		    }
		
	}

}

function setCases()
{
    var users= document.getElementById("users").value;
    var user_id= document.getElementById("user_id").value;
    if(user_id!=-1)
    {
        users=1;
    }

    var ctlName="";
    if(document.getElementById("chkChange").checked)
    {
      
        for($i=1;$i<=users;$i++)
        {
            ctlName = "total_cases"+$i;
           // alert(ctlName);
            document.getElementById(ctlName).readOnly=false;
            document.getElementById(ctlName).style.borderColor ="#7F9DB9";

        }
        document.getElementById("is_update_CaCases").value="1";

        document.getElementById("total_cases1").focus();
    }
    else
    {
        for($i=1;$i<=users;$i++)
        {
            ctlName = "total_cases"+$i;
           // alert(ctlName);
            document.getElementById(ctlName).readOnly=true;
            document.getElementById(ctlName).style.borderColor ="#A9A9A9";
            document.getElementById("is_update_CaCases").value="0";

        }
    }
}

function setUser()
{
	var user_id =document.getElementById("current_user_id").value;
	
	if( user_id == 0 && user_id !="")
	{
		 document.getElementById("user_id").value=0;
	}
	
	return user_id;
}

//====================== report
function initReport()
{

	var user_id = setUser();
	var store_type_id=document.getElementById("store_type").value;
	var trName="";
	var i;
	var users = document.getElementById("users").value;
	
	var bonus_type = document.getElementById("bonus_type").value;


	if(user_id==-1)
	{
		document.getElementById("store_types").style.display="none";
		document.getElementById("store_type_bcldb").style.display="none";
		document.getElementById("store_type_all").style.display="block";
	}
	else
	{
		if(bonus_type==6 )
		{
			document.getElementById("store_types").style.display="none";
			document.getElementById("store_type_bcldb").style.display="block";
			document.getElementById("store_type_all").style.display="none";
		}
		else
		{
			document.getElementById("store_types").style.display="block";
			document.getElementById("store_type_bcldb").style.display="none";
			document.getElementById("store_type_all").style.display="none";		
		}
	}
	if(store_type_id!=-1) //singel storeType
	{
	 
	 	if(user_id!=-1)
	 	{
			document.getElementById("trSummary").style.display="block";
			document.getElementById("trStoreTypeTotal").style.display="block";
			document.getElementById("trComm1").style.display="none";
			document.getElementById("trComm2").style.display="none";
			document.getElementById("trComm3").style.display="none";
			document.getElementById("trComm1").style.display="none";
			document.getElementById("trTarget").style.display="none";
		}
		else
		{
			document.getElementById("trSummary").style.display="none";
			if(bonus_type==6)
				document.getElementById("trTarget").style.display="none";
		}
	}
	else
	{
	//	document.getElementById("trStoreTypeTotal").style.display="none";
		if(user_id==-1)//all user
		{
			document.getElementById("trSummary").style.display="none";
			for(i=1;i<=users;i++)
			{
				trName = "trComm"+i;
				document.getElementById(trName).style.display="block";
			}
		//	alert("here");
			document.getElementById("tdClose").align="left";
			document.getElementById("tdClose").style.paddingLeft="640px";
			
		}
		else
		{
		 
		
			document.getElementById("trSummary").style.display="block";
			document.getElementById("trComm1").style.display="block";
				
			//users = 2;
			for(i=2;i<=users;i++)
			{
				trName = "trComm"+i;
				document.getElementById(trName).style.display="none";
			}
		}
	
			document.getElementById("trTarget").style.display="block";
	}
	
	if(bonus_type==6)
			document.getElementById("trTarget").style.display="none";
}

function changeType(typeid)
{

	document.getElementById("store_type").value=typeid;
	
	var bonus_type = document.getElementById("bonus_type").value;

   if(bonus_type==6  )
   {
		if(typeid!=6 && typeid!=-1)
		{
			alert("Sorry, Not data for this user!")
			return;
		}
	}
	else
	{
		if(typeid==6 )
		{
			alert("Sorry, Not data for this user!")
			return;
		}
	}

	reloadReport();
}
function changeuser()
{
	document.getElementById("store_type").value=-1;
	reloadReport();
}
function reloadReport()
{
  
 	var sale_year=document.getElementById("sale_year").value;
 	var sale_month=document.getElementById("sale_month").value;
 	var store_type_id = document.getElementById("store_type").value;
 	var user_id = document.getElementById("user_id").value;
 	var users = document.getElementById("users").value;
 	
 	 document.getElementById("current_user_id").value=user_id;
 	 

     var sURL="main.php?page_name=summaryreport&user_id="+user_id+"&sale_year="+sale_year+"&sale_month="+sale_month+"&store_type="+store_type_id+"&users="+users;
     document.location = sURL;
     stopEvt();
     return false;
}
function refreshSummaryLists(display_page)
{
  /*  var obname ="ListOrderBy_"+statusid;
    var odtname ="ListOrderType_"+statusid;
    var lpname ="ListPage_"+statusid;
    
    var order_by = document.getElementById(obname).value;
    var order_type = document.getElementById(odtname).value;
    var list_page = document.getElementById(lpname).value;*/
    
    /*var obname ="user_id"+statusid;
    var odtname ="ListOrderType_"+statusid;
    var lpname ="ListPage_"+statusid;*/
    

	var user_id = document.getElementById("user_id").value;
	var sale_year=document.getElementById("sale_year").value;
	var sale_month=document.getElementById("sale_month").value;
	var store_type = document.getElementById("store_type").value;
	var bonus_type = document.getElementById("bonus_type").value; 
    
   
    xajax_refreshSummaryLists(user_id, sale_month,sale_year, store_type,bonus_type,display_page);
    
}

function getNextPage(current_page)
{
  /*  var lpname ="ListPage_"+statusid;
    document.getElementById(lpname).value = parseInt(document.getElementById(lpname).value, 10) + 1;*/
	var   display_page = current_page+1;

   refreshSummaryLists(display_page);

}

function getPrevPage(current_page)
{
 		var   display_page = current_page-1;
	    refreshSummaryLists(display_page);
}



















