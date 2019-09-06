//============================================
//New way 
//============================================

function test()
{
	alert("just a test");
}
function $(id)
{
	return document.getElementById(id);
}
//==============================================================================
//wine allocated fucntion
//==============================================================================

function hidelast()
{
	 document.getElementById("tdOverdue").style.display ="block";
	 document.getElementById("tdSalesReport").style.display ="block";
     document.getElementById("tdCSSalesReport").style.display ="block";
}
function getCtlChar(ctl_name)
{
 
    if ( document.getElementById(ctl_name).value.length!="" && document.getElementById(ctl_name).value!=null)
    {
        return document.getElementById(ctl_name).value;
    }
    else
        return "";
}

function closePage()
{
	var link ="main.php";
	document.location = link;
	
	stopEvent(window.event);
	return false;
}



/*
isInt: true:interger; false: float

*/
function getCtlValue(ctl_name,isInt)
{
    if ( document.getElementById(ctl_name).value!="" && document.getElementById(ctl_name).value!=null)
    {
        if(isInt==0)
            return parseInt(document.getElementById(ctl_name).value);
        else
            return parseFloat(document.getElementById(ctl_name).value);
    }
    else
        return 0;
}

function setValue2Ctrl(ctlName,nVal)
{
    document.getElementById(ctlName).value = nVal;
}

function setFocus(ctlName)
{
    document.getElementById(ctlName).focus();
}

function disabledCtrl(ctlName,isDis)
{
    document.getElementById(ctlName).disabled =isDis;
    if (isDis)
        document.getElementById(ctlName).style.borderColor ="#A9A9A9";

    else
        document.getElementById(ctlName).style.borderColor ="#7F9DB9";
}
function initReport(id)
{
    var province_id =  document.getElementById("login_pro").value;
 
    if( province_id==1 || province_id==2)
    {
    	changeRepTab(id);
    	setAnaSalesDate();
    	
    	var tDate=new Date();
        
    //    $(".not-ava").hide();
        document.getElementById("no_ava").style.display="none";
         
       //   $("#no_ava").hide();
    }
    else
    {
       // $("#westen_table").hide();
        document.getElementById("westen_table").style.display="none";   
    }
}
function setAnaSalesDate()
{
	var sales_year =  document.getElementById("bi_sale_year").value;
	

	var province_id =  document.getElementById("login_pro").value;


	xajax_getAnaSalesMonths(province_id, sales_year);
}
function getAnaUsers()
{
		var sales_year =  document.getElementById("bi_sale_year").value;
		var sales_month=  document.getElementById("bi_month_year").value;
	var province_id =  document.getElementById("login_pro").value;
		xajax_getAnaSalesUsers(province_id,sales_year,sale_month);
}
function setAnaUsers(province_id,sales_year,sale_month)
{

	xajax_getAnaSalesUsers(province_id,sales_year,sale_month);
}

function loadReports(totalcs)
{
 
     var str ="Total cases: " + totalcs + " ";
    document.getElementById('spTotalCS').innerHTML=str;
/*	if (document.layers)
	document.captureEvents(Event.MOUSEOVER | Event.MOUSEOUT | Event.MOUSEDOWN | Event.MOUSEUP | Event.MOUSECLICK)
	
	document.onmouseover=hidestatus
	document.onmouseout=hidestatus
	document.onmousedown=hidestatus
	document.onmouseup=hidestatus
	document.onmouseclick=hidestatus*/
}


function changeRepTab(id)
{
	var tab0 = document.getElementById("tab0");
	var tab1 = document.getElementById("tab1");
	var tab2 = document.getElementById("tab2");

    tab0.className = "tab";
    tab1.className = "tab";
    tab2.className = "tabRight";
	//	var trName= "trComm_pro"+id;
	
    document.getElementById("tab1").style.display="none";
    if (id==0)//bc estate
    {
		document.getElementById("trUser").style.display="block";
		document.getElementById("trBCE1").style.display="block";
		document.getElementById("trBCE2").style.display="block";
		document.getElementById("trBCE3").style.display="block";
		document.getElementById("trBCE4").style.display="none";
		document.getElementById("trBCE5").style.display="none";
		document.getElementById("trBCE6").style.display="block";
		document.getElementById("trBCE9").style.display="block";
	
	    document.getElementById("trBC1").style.display="none";
		document.getElementById("trBC2").style.display="none";
		
		document.getElementById("trBCE7").style.display="none";
		document.getElementById("trBCE8").style.display="none";
		document.getElementById("trBCE10").style.display="none";
        document.getElementById("trBCE11").style.display="none";
		document.getElementById("trM1").style.display="none";
		document.getElementById("trCCInfo").style.display="none";
		document.getElementById("trExpCCInfo").style.display="none";
		document.getElementById("trCaseValue").style.display="none";
		
		tab0.className = "tabActive";
		reportsMain.searchKey[0].checked = true;
		changeReportType("1");
		
    }
    else if(id==1)//bcldb
    {
     
		document.getElementById("trUser").style.display="none";
		document.getElementById("trBCE1").style.display="none";
		document.getElementById("trBCE2").style.display="none";
		document.getElementById("trBCE3").style.display="none";
		document.getElementById("trBCE4").style.display="none";
		document.getElementById("trBCE5").style.display="none";
		document.getElementById("trBCE6").style.display="none";
		document.getElementById("trBCE9").style.display="none";
			
		document.getElementById("trBC1").style.display="block";
		document.getElementById("trBC2").style.display="block";
		
		document.getElementById("trBCE7").style.display="none";
		document.getElementById("trBCE8").style.display="none";
		document.getElementById("trBCE10").style.display="none";
        document.getElementById("trBCE11").style.display="none";
		document.getElementById("trM1").style.display="none";
		document.getElementById("trCCInfo").style.display="none";
		document.getElementById("trExpCCInfo").style.display="none";
		document.getElementById("trCaseValue").style.display="none";

		
		tab1.className = "tabActive";
	//	reportsMain.searchKey[9].checked = true;
		changeReportType("9");
		
    }
 	else//miscellaneous
    {
     	document.getElementById("trUser").style.display="none";
		document.getElementById("trBCE1").style.display="none";
		document.getElementById("trBCE2").style.display="none";
		document.getElementById("trBCE3").style.display="none";
		document.getElementById("trBCE4").style.display="none";
		document.getElementById("trBCE5").style.display="none";
		document.getElementById("trBCE6").style.display="none";
		document.getElementById("trBCE9").style.display="none";
	
		document.getElementById("trBC1").style.display="none";
		document.getElementById("trBC2").style.display="none";
		
		document.getElementById("trBCE7").style.display="block";
		document.getElementById("trBCE8").style.display="block";
		document.getElementById("trBCE10").style.display="block";
        document.getElementById("trBCE11").style.display="block";
        
		if(	document.getElementById("login_user_level").value>1)
			document.getElementById("td_bi_user_id").style.display = "none";
		else
			document.getElementById("td_bi_user_id").style.display = "block";
		document.getElementById("trM1").style.display="block";
		document.getElementById("trCCInfo").style.display="block";
		document.getElementById("trExpCCInfo").style.display="block";
		document.getElementById("trCaseValue").style.display="block";
		
		tab2.className = "tabActive";
	//	reportsMain.searchKey[13].checked = true;
		changeReportType("13");
		
		if(id==7)
		{
			changeReportType("7");	
			reportsMain.searchKeyM[1].checked = true;
		}
    }
}


function changeReportType(keyValue)
{


    setValue2Ctrl("changekey",keyValue);

 
    if(keyValue > 3 && keyValue!=7 )
    {
        document.getElementById("chkAssign").disabled = true;
        document.getElementById("user_id").disabled = true;
    }
    else
    {
        document.getElementById("chkAssign").disabled = false;
        document.getElementById("user_id").disabled = false;
    }
}
function exportOverduReport()
{
/*	if(document.getElementById("totalCount").value==0 ||document.getElementById("totalCount").value==""||document.getElementById("totalCount").value==null)
	{
	 	alert("Nothing was found matching your select criteria, please try again.");
	}
	else
	{
*/		var estate_id = document.getElementById('estate_id').value;
		var order_by;
		var order_type;
	
		var store_type_id=0;
		var overdue_type =0;
		var user_id =0;
				
		if(document.getElementById('user_id').value!="")
		{
			user_id = document.getElementById('user_id').value;
		}
		
		if(document.getElementById('lkup_store_type_id').value!="")
		{
			store_type_id = document.getElementById('lkup_store_type_id').value;
		}
		
		if( document.getElementById('overdue_type').value!="")
			overdue_type =document.getElementById('overdue_type').value;
			
		var sURL = "main.php?report_page_name=excelOverdueReport&estate_id=" + estate_id +  "&store_type_id=" + store_type_id + "&user_id=" + user_id+ "&overdue_type=" + overdue_type;
			
		document.location = sURL;
}

function changeSalesReportType(keyValue)
{
 	var orgKeyValue = getCtlValue("changekey");
 	
 
    setValue2Ctrl("changekey",keyValue);
  
  	
  		
  		disabledCtrl("sales_month",false);
		disabledCtrl("sales_year",false);
  	if(keyValue==2)//monthly sales break down
  	{
	  	getSalesYears(keyValue);
		disableBreakTypeCombo(false);
		disableEstate4ABCombo(true);
		disableBICombo(true);
	
	}
	else if (keyValue==3)//bc sales in ab
	{
		//	getSalesYears(keyValue);
		disableBreakTypeCombo(true);
		disableEstate4ABCombo(false);
		disableBICombo(true);
		
	}
	else if (keyValue==16)//BI
	{
		disableBreakTypeCombo(true);
		disableEstate4ABCombo(true);
		disableBICombo(false);
		
		disabledCtrl("sales_month",true);
		disabledCtrl("sales_year",true);
	}
	//if()
//	disableBreakTypeCombo(!(keyValue==16));!(keyValue==3)
	
	
}




function checkSPLocation()
{
	if(document.getElementById("chkSPLocation").checked)
	{
		document.getElementById("sp_location_type").disabled =false;
		document.getElementById("sp_location_name").disabled =false;
	}
	else
	{
		document.getElementById("sp_location_type").disabled =true;
		document.getElementById("sp_location_name").disabled =true;		
	}
}

//getSpLocation
function exportToExcel(searchKeys)
{ 	
	var searchKeys =searchKeys.split("|");	       
	
	var slink = "main.php?report_page_name=excelF60Reports&searchType="+searchKeys[0]+"&user_id="+searchKeys[1]+"&estateid="+searchKeys[2]+"&from="+searchKeys[3]+"&to="+searchKeys[4]+"&wine_id="+searchKeys[5]+"&store_type_id="+searchKeys[6]+"&searchAdt="+searchKeys[7]; 
  
	
  	document.location = slink;
	stopEvt();
    return false;
}

function LastDayOfMonth(Year, Month)
{
    return(new Date((new Date(Year, Month+1,1))-1)).getDate();
}


function exportCurrentSP()
{ 	 	 
 	var currentAva = getCtlValue("is_sp_current_ava");	
 	var currentDate = new Date(); 	
 	var currentDay = currentDate.getDate();
 	var lastDay = LastDayOfMonth(currentDate.getYear(),currentDate.getMonth());
	
 	var reportType=getCtlValue("current_sp_type");	
	if(currentDay ==1 || currentDay ==15  ||currentDay ==lastDay)
	{
		if(reportType ==0) //bcldb team
		{
			var report_year = getCtlValue("sp_report_year", 0);
			var report_month = getCtlValue("sp_report_month", 0);
			
			if (report_month>0 && report_year>0)
			{	
			 	var location_group_id_slink ="";
				if(document.getElementById('chkSPLocation').checked) 
					 location_group_id_slink = "&location_group_id="+document.getElementById('sp_location_type').value;
					 
				
				var reportURL = "main.php?page_name=excelStorePenetrationReport&report_month=" + report_month + "&report_year=" + report_year+location_group_id_slink;
				document.location = reportURL;
			}
		}
		else
		{
		 
			exportTodaySP(); 
		
		}
	}
	else
	{	 	
		exportTodaySP();
	}
	

	stopEvt();
	return false;

}

function exportTodaySP()
{
 	setValue2Ctrl("is_sp_current_ava","1");
 	var reportType=getCtlValue("current_sp_type");	


  
  	var location_group_id_slink ="";
	if(document.getElementById('chkSPLocation').checked) 
		 location_group_id_slink = "&location_group_id="+document.getElementById('sp_location_type').value;
   var slink = "main.php?report_page_name=excelCurrentStorePenReport&report_type="+reportType+location_group_id_slink; 


	document.location = slink;
	stopEvt();
   return false;
}

function disableWaitImg()
{
	document.getElementById("tdWait").style.display="none";
 	exportTodaySP();	
}

function initForm4AB()
{
 	disableBreakTypeCombo(true);
 	disableEstate4ABCombo(true);
 	disableBICombo(true);
 
 	
	getSalesYears(1);
		setAnaSalesDate();
			if(	document.getElementById("login_user_level").value>1)
				document.getElementById("td_bi_user_id").style.display = "none";
			else
				document.getElementById("td_bi_user_id").style.display = "block";
}

function disableBreakTypeCombo(isDisable)
{
	document.getElementById("break_type").disabled=isDisable;
}
function disableEstate4ABCombo(isDisable)
{
	document.getElementById("estate_id").disabled=isDisable;
}
function disableBICombo(isDisable)
{
	document.getElementById("bi_sale_month").disabled=isDisable;
	document.getElementById("bi_sale_year").disabled=isDisable;
	document.getElementById("bi_user_id").disabled=isDisable;
}
function createABSalesReports(isCancel)
{


 	/*	document.location = "main.php?report_page_name=excelABMonthlyAlloReport";
 		//document.location = "main.php?report_page_name=excelABVenderSalesReport&sale_year=2009&sale_month=12";
 	//	document.location = "main.php?report_page_name=excelBCInABVenderReport&sale_year=2009&sale_month=12&estate_id=96";
		stopEvt();
		return false;*/
    
	var keyValue =getCtlValue("changekey");
	var break_type = getCtlValue("break_type");
	
    var slink ="";
  
  	var sale_year =  getCtlValue("sales_year");
  	var sale_month =  getCtlValue("sales_month");    
  	var estate_id =  getCtlValue("estate_id");  




 	if(parseInt(isCancel)==1)
    {
         document.location = "main.php";
         stopEvt();
         return false;
    }
    else
    {     	
		if (parseInt(keyValue)==1)// AB sales report from liqurconnection
		{	 			 	 
			slink = "main.php?report_page_name=excelABVenderSalesReport&sale_month=" + sale_month + "&sale_year=" + sale_year;	
		}	
		else if (parseInt(keyValue)==2)// AB sales reports break down
		{	 			 	 
			slink = "main.php?report_page_name=excelABBreakDownReport&sale_month=" + sale_month + "&sale_year=" + sale_year+"&break_type="+break_type;	
		}
		else if (parseInt(keyValue)==3)// BC Estates sales in Alberta
		{	 			 	 
         
             sale_year =  getCtlValue("bc_sale_year");
  	          sale_month =  getCtlValue("bc_sale_month");  
			slink = "main.php?report_page_name=excelBCInABVenderReport&sale_month=" + sale_month + "&sale_year=" + sale_year+"&estate_id="+estate_id;	
		}
		else if (parseInt(keyValue)==4)// Alberta store penetration
		{	 			 	 
			slink = "main.php?report_page_name=excelABStorePenReport&sale_month=" + sale_month + "&sale_year=" + sale_year;	
		}	
		else if (parseInt(keyValue)==5)// BC Estates sales in Alberta
		{	 			 	 
			slink = "main.php?report_page_name=excelABMonthlyAlloReport&sale_month=" + sale_month + "&sale_year=" + sale_year;	
		}	
		else if (parseInt(keyValue)==15)// case value
		{	 			 	 
	 		var login_user_id =$("login_user_id").value;		
			slink = "main.php?report_page_name=excelCaseValueReport&login_user_id="+login_user_id;		
		}	
		else if (parseInt(keyValue)==16) //BI monthly sales analysis
			{
			 
				var user_id =-1;
				var month = document.getElementById("bi_sale_month").value;	
				var year = document.getElementById("bi_sale_year").value;	
		
			if(	document.getElementById("login_user_level").value>1)
				user_id =document.getElementById("login_user_id").value;
			else
				user_id =document.getElementById("bi_user_id").value;
			
				slink = "main.php?report_page_name=BI_excelMonthlySalesAnalysisReport&user_id="+user_id+"&report_month="+month+"&report_year="+year;;
		
			}          


		document.location = slink;
		stopEvt();
		return false;	
    }

}
	function changeEstate4BC()
	{
		var reportTypeId = getCtlValue("changekey");
		getSalesYears(reportTypeId);
	}
	function getSalesYears(reportTypeId)
	{
	 	var estate_id = "";
	 	
	 	if(reportTypeId == 3)
	 	{
			estate_id = document.getElementById("estate_id").value;
		}
		
		xajax_getABVenderSalesYears(reportTypeId, estate_id);
	}

	function getSalesMonthsByYear(sYear)
	{
	 	var reportTypeId = getCtlValue("changekey");
	 	var estate_id = "";
	 	
	 	if(reportTypeId == 3)
	 	{
			estate_id = document.getElementById("estate_id").value;
		}
	 
		xajax_getABVenderSalesMonths(reportTypeId, sYear, estate_id);
	}
	
	function createSalesReport4Cities(estate_id,cities,store_type,user_id,from,to)
	{	
			var sURL = "main.php?report_page_name=excelF60Reports&searchType=14"+"&user_id="+user_id+"&estateid="+estate_id+"&from="+from+"&to="+to+"&searchAdt="+cities+"&store_type_id="+store_type; 

			document.location = sURL;
			stopEvt();
			return false;
	}
	function messageNoRec4City()
	{
		alert("Nothing was found matching your search criteria, please try again.");
		document.getElementById("city").focus();
		stopEvt();
		return false;
	}
	function createCaseValueReport()
	{
		var login_user_id =$("login_user_id").value;
		
	//	alert(login_user_id);
		var sURL = "main.php?report_page_name=excelCaseValueReport&login_user_id="+login_user_id;
		
	//	alert(sURL);
		document.location = sURL;
		stopEvt();
		return false;
	}

        
	function createReports(isCancel)
	{
	    var keyValue =getCtlValue("changekey");
	    var searchAdt = "searchType_" + keyValue;
	    var estateidCtl ="estate_id_"+ keyValue;
	    var fromCtl = "from_"+keyValue;
	    var toCtl = "to_"+keyValue;
	    var slink = "main.php?page_name=F60Reports&";
	
	    var sblink ="";
	    var searchValue="";
	    var from ="";
	    var to="";
	    var froms ;
	    var tos;
	    var tests
	    var wine_id;
	    var estate_id;



      
	    if(parseInt(isCancel)==3)
	    {
			slink = "main.php?report_page_name=excelF60Reports&";
		}
		
	 	if(parseInt(isCancel)==2)
	    {
	         document.location = "main.php";
	         stopEvt();
	         return false;
	    }
	    else
	    {
			if (parseInt(keyValue)==9)// store penetration
			{
				var report_month = getCtlValue("sp_report_month", 0);
				var report_year = getCtlValue("sp_report_year", 0);
				if (report_month>0 && report_year>0)
				{				 	
	   			 	var location_group_id_slink ="";
					if(document.getElementById('chkSPLocation').checked) 
						 location_group_id_slink = "&location_group_id="+document.getElementById('sp_location_type').value;
						 
				    var reportURL = "main.php?page_name=excelStorePenetrationReport&report_month=" + report_month + "&report_year=" + report_year+  location_group_id_slink;
				    
				    document.location = reportURL;
				}
				stopEvt();
				return false;
			}
			if (parseInt(keyValue)==10) //credit card information list
			{
				var estate_id = document.getElementById('estate_id_cc').value;
				var sURL = "main.php?report_page_name=excelCCReport&isExpiry=0&estate_id="+estate_id;
				
				document.location = sURL;
				stopEvt();
				return false;
			}         
			
			if (parseInt(keyValue)==12) //Expiry credit card information list
			{
				var sURL = "main.php?report_page_name=excelCCReport&isExpiry=1";
			
			//	var sURL = "main.php?report_page_name=excelBCEstateSalesReport&estate_id=2";
				document.location = sURL;
				stopEvt();
				return false;
			}            
		
			if (parseInt(keyValue)==13) //monthly sales report for bC estate
			{
				var estate_id =document.getElementById("estate_id_sales").value;
				var month = document.getElementById("bc_sale_month").value;	
				var year = document.getElementById("bc_sale_year").value;	
				
				var	isOpenReport=true;
				if(year<2016 && estate_id ==175)
				{
						alert("Selected Year's sales is not avalaible.");
						isOpenReport=false;
				}
				if(year==new Date().getFullYear())
				{
					if(month>(new Date().getMonth()+1))
					{
						alert("Selected Month's sales is not avalaible yet.");
						isOpenReport=false;
					}
				}
				if(isOpenReport)
				{
					var sURL = "main.php?report_page_name=excelBCEstateSalesReport&estate_id="+estate_id+"&report_month="+month+"&report_year="+year;;
					document.location = sURL;
					stopEvt();
					return false;
				}
			}          
			
			if (parseInt(keyValue)==17) //monthly sales report for bC estate
			{
				var estate_id =document.getElementById("estate_id_sm").value;
				var month = document.getElementById("bc_sale_month_sm").value;	
				var year = document.getElementById("bc_sale_year_sm").value;	
							
				var isOpenReport=true;
				
					if(year<2016 && estate_id ==175)
				{
						alert("Selected Year's sales is not avalaible.");
						isOpenReport=false;
				}
				if(year==new Date().getFullYear())
				{
					if(month>(new Date().getMonth()+1))
					{
						alert("Selected Month's sales is not avalaible yet.");
						isOpenReport=false;
					}
				}
				
				if(isOpenReport)
				{
					var sURL = "main.php?report_page_name=excelBCSalesCustomReports&estate_id="+estate_id+"&report_month="+month+"&report_year="+year;;
					
					document.location = sURL;
					stopEvt();
					return false;
				}
				else
				{
				 	stopEvt();
					return false;
				}
			}     
  	         if (parseInt(keyValue)==18) //monthly sales report for bC estate
			{
				var estate_id =document.getElementById("cs_estate_id_sales").value;
				froms =getCtlChar("from_cs");
	            froms =froms.split("/");
	            from = froms[2]+"-"+froms[0]+"-"+froms[1];
	
	            tos =getCtlChar("to_cs");
	            tos =tos.split("/");
	            to = tos[2]+"-"+tos[0]+"-"+tos[1];				
			
              
        		var sURL = "main.php?report_page_name=excelCSSalesSummary&estate_id="+estate_id+"&start_date="+from+"&end_date="+to;
					
					document.location = sURL;
					stopEvt();
					return false;
			
			}          			     			
			if (parseInt(keyValue)==16) //BI monthly sales analysis
			{
	
				var user_id =-1;
				var month = document.getElementById("bi_sale_month").value;	
				var year = document.getElementById("bi_sale_year").value;	
		//		alert("test");
		
			/*var estate_id =document.getElementById("estate_id_sales").value;
				var month = document.getElementById("bc_sale_month").value;	
				var year = document.getElementById("bc_sale_year").value;	
			*/		
				if(	document.getElementById("login_user_level").value>1)
					user_id =document.getElementById("login_user_id").value;
				else
					user_id =document.getElementById("bi_user_id").value;
			
            //  user_id =134;
            	var sURL = "main.php?report_page_name=BI_excelMonthlySalesAnalysisReport&user_id="+user_id+"&report_month="+month+"&report_year="+year;;
			//	var sURL = "main.php?report_page_name=BI_excelMonthlySalesAnalysisReport_t&estate_id="+estate_id+"&report_month="+month+"&report_year="+year;;
			
			//	var sURL = "main.php?report_page_name=excelBCEstateSalesReport&estate_id=97";
				
				document.location = sURL;
				stopEvt();
				return false;
			}          
	
	
			if (parseInt(keyValue)==14) //monthly sales report for cities
			{
	
				var estate_id =$("estate_id_city").value;
				var cities = $("city").value;
				var store_type=$("store_type_id_city").value;
				var cities=$("city").value;
	
		 		froms =getCtlChar("from_city");
	            froms =froms.split("/");
	            from = froms[2]+froms[0]+froms[1];
	
	            tos =getCtlChar("to_city");
	            tos =tos.split("/");
	            to = tos[2]+tos[0]+tos[1];
	               
	
				if(cities.search("Input")>=0)
				{
					cities="";				
				}
					
				var user_id = getCtlValue("user_id");
				if(user_id==0)
					user_id="";
					
			
				 //	alert("herer");
					xajax_checkAvaForm604Cities(estate_id,store_type,user_id,from,to,cities);
					stopEvt();
					return false;
			
			}     
			
			if(parseInt(keyValue)==15) // case value list
			{
			 
			 	createCaseValueReport();
			}
		
			if (parseInt(keyValue)==11) //current sp report
			{
				exportCurrentSP();
				
				stopEvt();
				return false;
			}                
            
			if (parseInt(keyValue)==8)
			{
			
				var reportid = getCtlValue(estateidCtl);
				var pdfpath ="PDF/";
				var pdfname=pdfpath+"both-sheet.pdf";
				
				if(reportid==2)
					pdfname=pdfpath+"inventorysheet.pdf";
				else if(reportid==3)
					pdfname=pdfpath+"sampleinventorysheet.pdf";
				else if(reportid==4)
					pdfname=pdfpath+"ImportedWineSampleInventoryControlSheet.pdf";
				
				window.open(pdfname,"PDF","height=600,width=800,status=yes,toolbar=no,menubar=yes,location=no,resizable=yes,scrollbars=yes");
				
				stopEvt();
				return false;
			}
			if (parseInt(keyValue)==7) // overdue invoices
			{
			
				var estate_id = getCtlValue("estate_id_overdue");
				var over_due =getCtlValue("overdue_type");
				var user_id = getCtlValue("user_id");
				var slink = "main.php?page_name=f60OverDueReports&user_id="+user_id+"&estate_id="+estate_id+"&overdays="+over_due; 
				
			  	document.location = slink;
				stopEvt();
				return false;
			}
	        else
	        {
		
	            if (keyValue != 7)
	                searchValue =getCtlChar(estateidCtl);
	
	            if (keyValue<4 || keyValue==6 )
	            {
	               froms =getCtlChar(fromCtl);
	               froms =froms.split("/");
	               from = froms[2]+froms[0]+froms[1];
	
	               tos =getCtlChar(toCtl);
	               tos =tos.split("/");
	               to = tos[2]+tos[0]+tos[1];
	           }

				if (keyValue==1)
					sblink = "searchType="+keyValue+"&estateid="+searchValue+"&from="+from+"&to="+to;
				else if ( keyValue==2 || keyValue==3)
					sblink = "searchType="+keyValue+"&searchAdt="+getCtlChar(searchAdt)+"&estateid="+searchValue+"&from="+from+"&to="+to;
				else if(keyValue ==4||keyValue ==5 )
				{
					sblink = "searchType="+keyValue+"&estateid="+searchValue;
					if(keyValue==5)
					{
						wine_id =getCtlChar("wine_id_5");
						sblink  =sblink +"&wine_id="+wine_id;
					}
				}
				else if (keyValue==6)
				{
				// alert("here");
					wine_id =getCtlChar("wine_id_6");
					sblink = "searchType="+keyValue+"&store_type_id="+getCtlValue("store_type_id")+"&estateid="+searchValue+"&from="+from+"&to="+to+"&wine_id="+wine_id;
				}
					
				slink=slink+sblink;
			
				if (document.getElementById("chkAssign").checked)
				{
					var user_id =document.getElementById("user_id").value;
					slink = slink + "&user_id="+user_id;
				}			
		
				document.location = slink;
				stopEvt();
				return false;
        	}
    	}

	}

function getWines(control_id, estate_id)
{
     xajax_getWines(control_id, estate_id);
}

function getSPReportMonths(control_id, reportYear)
{
   //  xajax_getSPReportMonths(control_id, reportYear);
}


function printf60report(sparas)//searchType,from,to,estateid,store_type_id,searchAdt)
{
	var spara=sparas.split("|");
	
	var slink = "main.php?page_name=F60PrintReport&searchType="+spara[0]+"&from="+spara[1]+"&to="+spara[2];
	   slink = slink +"&estateid="+spara[3]+"&store_type_id="+spara[4]+"&searchAdt="+spara[5]+"&wine_id="+spara[6]+"&user_id="+spara[7];
	   
	  window.open(slink,"test","height=600,width=800,status=yes,toolbar=no,menubar=yes,location=no,resizable=yes,scrollbars=yes");

}

function refreshOverdueList(isGoButton)
{
	
	var estate_id = 0;

	var order_by;
	var order_type;

	var store_type_id=0;
	
	var user_id =0;
	
	if(document.getElementById('user_id').value!="")
	{
		user_id = document.getElementById('user_id').value;
	}
	
	if(document.getElementById('lkup_store_type_id').value!="")
	{
		store_type_id = document.getElementById('lkup_store_type_id').value;
	}

	if(document.getElementById('estate_id').value!="")
	{
		estate_id = document.getElementById('estate_id').value;
	}
	
	var overdue_type = document.getElementById('overdue_type').value;
	
	var currentpage = 1;
	 
	order_by =document.getElementById('orderlistSortBy').value;
	order_type = document.getElementById('orderlistSortType').value;
	
	if(isGoButton) // click button to refresh the list
	{
		order_by="overdays";
		order_type ="a";	
	}
	else
	{
		if(document.getElementById('currentPage').value!="")
		{
			currentpage = document.getElementById('currentPage').value;
		}
	}

	xajax_refreshOverdueList(order_by,order_type,estate_id, store_type_id, user_id,overdue_type, currentpage);		
}


function sortOverdueList(order_by)
{
    if (order_by)
    {
        var order_type;
        var fldOrderBy = document.getElementById('orderlistSortBy');
        var fldOrderType = document.getElementById('orderlistSortType');
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
    }
    refreshOverdueList(false);
}

function getOverdueNextPage()
{

	document.getElementById("currentPage").value = parseInt(document.getElementById("currentPage").value)+1;


	refreshOverdueList(false);

}

function getOverduePrevPage()
{
// 		var   display_page = document.getElementById("currentPage").value-1;
	document.getElementById("currentPage").value = parseInt(document.getElementById("currentPage").value)-1;
	

   	refreshOverdueList(false);
}

function back2Report(reportID)
{
	//history.go(-1);
	
	var slink="main.php?page_name=reportsMain&reportId="+reportID;
	document.location = slink;
	stopEvt();
	return false;
}


function updateInvocieStatus(order_id)
	{	
	    var ht = 500;
	    var wd = 750;
	
		var link="main.php?page_name=invoiceViewSP&id="+order_id+"&isInner=1";
		
		var left=(screen.availWidth-wd)/2;
		
		var top=(screen.availHeight-ht)/2;		
	
	    var printWindow = window.open(link, "Form60", "menubar=yes,scrollbars=yes, resizable=no, left="+left+",top="+top+",height=" + ht + ",width=" + wd);
	}

function refreshPage()
{	
	history.go(0);
}
//Hides all status bar messages

function hidestatus(){
window.status=''
return true
}






function setCalendar()
{
//	changeRepTab(0) ;
     Calendar.setup( {
        inputField:"from_1", ifFormat:"%m/%d/%Y", button:"from_1_calendar", singleClick:true, align:"Bl", cache:true, showOthers:true, onClose: dateCalendarClose
    } );
    Calendar.setup( {
        inputField:"from_2", ifFormat:"%m/%d/%Y", button:"from_2_calendar", singleClick:true, align:"Bl", cache:true, showOthers:true, onClose: dateCalendarClose
    } );
    Calendar.setup( {
        inputField:"from_3", ifFormat:"%m/%d/%Y", button:"from_3_calendar", singleClick:true, align:"Bl", cache:true, showOthers:true, onClose: dateCalendarClose
    } );
    Calendar.setup( {
        inputField:"from_6", ifFormat:"%m/%d/%Y", button:"from_6_calendar", singleClick:true, align:"Bl", cache:true, showOthers:true, onClose: dateCalendarClose
    } );
     Calendar.setup( {
        inputField:"from_city", ifFormat:"%m/%d/%Y", button:"from_city_calendar", singleClick:true, align:"Bl", cache:true, showOthers:true, onClose: dateCalendarClose
    } );
    
     Calendar.setup( {
        inputField:"from_cs", ifFormat:"%m/%d/%Y", button:"from_cs_calendar", singleClick:true, align:"Bl", cache:true, showOthers:true, onClose: dateCalendarClose
    } );
    
     Calendar.setup( {
        inputField:"to_1", ifFormat:"%m/%d/%Y", button:"to_1_calendar", singleClick:true, align:"Bl", cache:true, showOthers:true, onClose: dateCalendarClose
    } );
    Calendar.setup( {
        inputField:"to_2", ifFormat:"%m/%d/%Y", button:"to_2_calendar", singleClick:true, align:"Bl", cache:true, showOthers:true, onClose: dateCalendarClose
    } );
    Calendar.setup( {
        inputField:"to_3", ifFormat:"%m/%d/%Y", button:"to_3_calendar", singleClick:true, align:"Bl", cache:true, showOthers:true, onClose: dateCalendarClose
    } );
    Calendar.setup( {
        inputField:"to_6", ifFormat:"%m/%d/%Y", button:"to_6_calendar", singleClick:true, align:"Bl", cache:true, showOthers:true, onClose: dateCalendarClose
    } );
    Calendar.setup( {
        inputField:"to_city", ifFormat:"%m/%d/%Y", button:"to_city_calendar", singleClick:true, align:"Bl", cache:true, showOthers:true, onClose: dateCalendarClose
    } );
    
     Calendar.setup( {
        inputField:"to_cs", ifFormat:"%m/%d/%Y", button:"to_cs_calendar", singleClick:true, align:"Bl", cache:true, showOthers:true, onClose: dateCalendarClose
    } );

}

