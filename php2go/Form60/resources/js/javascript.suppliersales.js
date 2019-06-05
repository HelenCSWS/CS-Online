 function $(id)
	{
		return document.getElementById(id);
	}	
/*onerror = errorHandler
function errorHandler(message, url, line)
{
    out  = "Sorry, an error was encountered.\n\n";
    out += "Error: " + message + "\n";
    out += "URL: "   + url + "\n";
    out += "Line: "  + line + "\n\n";
    out += "Click OK to continue.\n\n";
    alert(out);
    return true;
}*/

	function changeYear()
	{
			var province_id = $("province_id").value;
			var estate_id = document.getElementById("estate").value;
			var sales_year = $("sales_year").value;
		
			xajax_getMonths(province_id,estate_id, sales_year);
	}

	function changeDate()
	{
		estate_id = $("estate").value;
		isBCEstate=$("isBCEstate").value;
		
		if(isBCEstate==0)   
		{
			if(supplierSales.chkdate[0].checked)
			{
				$("from_1").disabled=false;
				$("to_1").disabled=false;
				
				$("sales_month").disabled=true;
				$("sales_qut").disabled=true;
				$("sales_year").disabled=true;
				$("chkQut").disabled=true;
			}
			else
			{		
				$("from_1").disabled=true;
				$("to_1").disabled=true;
				$("sales_year").disabled=false;
				$("chkQut").disabled=false;
			
				changeSearchPeriod();
			}
		}
	
	}
	
	function changeKey_w(id)
	{	 
		$('search_id_w').value = id;
		if(id==2)
		{
			$('search_field_w').disabled=true;
			$("search_field_w").style.borderColor ="#A9A9A9";
		}
		else
		{
			$('search_field_w').disabled=false;
			$("search_field_w").style.borderColor ="#7F9DB9";
			if(id!=2)
				$('search_field_w').focus();				
		}
	}

	function getinvoices()
	{
		getInvoiceList(true);

		stopEvent(window.event);
		return false;
	}
	function changeSearchFeild()
	{
		$('search_field').focus();
	}

	function changeSearchPeriod()
	{
		if($("chkQut").checked) 
		{
			$("sales_month").disabled=true;
			$("sales_qut").disabled=false;
		}
		else
		{
			$("sales_month").disabled=false;
			$("sales_qut").disabled=true;
			$("quarter_desc").value = "";
		}
	}
	
	function checkIfNoRecords()
	{
		if($("totalCount").value==0 ||$("totalCount").value==""||$("totalCount").value==null)
		{
		 
			$("noresults").style.display="block";
		}
		else
		{		 
			$("noresults").style.display="none";
		}
	}
	
	function exportReport()
	{
 			
	 
		if($("totalCount").value==0 ||$("totalCount").value==""||$("totalCount").value==null)
		{
		 	alert("Nothing was found matching your select criteria, please try again.");
		}
		else
		{
			var estate_id = $('estate').value;
			var dateType=0;
			var date1;
			var date2;
			var order_by;
			var order_type;
		
			var store_type_id=-1;
			var wine_id =-1;
			var user_id =-1;
			
			var province_id = $('province_id').value;
			
			var currentpage = 1;
			var isBCEstate=$("isBCEstate").value;

			
			if(isBCEstate==0)
			 	{
			 		if(supplierSales.chkdate[0].checked)
			 		{			 	 	
						dateType =0;
						date1 = format2SqlDate($('from_1').value);  //mm/dd/yyyy should change to yyyy/mm/dd
						date2 = format2SqlDate($('to_1').value);  //mm/dd/yyyy should change to yyyy/mm/dd
					}
					else
					{
						date1 = $('sales_year').value;
		
						if($("chkQut").checked)  //quarter
						{
							dateType = 1;
							date2 = $('sales_qut').value;
						}
						else
						{
							dateType = 2;
							date2 = $('sales_month').value;
						}
					}
				}
				else
				{
					date1 = $('sales_year').value;		
					if($("chkQut").checked)  //quarter
					{
						dateType = 1;
						date2 = $('sales_qut').value;
					}
					else
					{
						dateType = 2;
						date2 = $('sales_month').value;
					}
				}
				order_by =$('orderlistSortBy').value;
				order_type = $('orderlistSortType').value;
			
				if($('user_id').value!="")
				{
					user_id = $('user_id').value;
				}
				
				if($('lkup_store_type_id').value!="")
				{
					store_type_id = $('lkup_store_type_id').value;
				}
				
				if( $('wine_id').value!="")
					wine_id =$('wine_id').value;
		
				var sURL = "main.php?page_name=excelSupplierSalesReport&estate_id=" + estate_id + "&date1=" + date1 + "&date2=" + date2 + "&dateType=" + dateType + "&store_type_id=" + store_type_id + "&province_id=" + province_id+ "&wine_id=" + wine_id;
				
			    document.location = sURL;
	       }	
	}

	function exportCCReport()
	{
		var estate_id = $('estate').value;
		var sURL = "main.php?page_name=excelCCReport&estate_id=0";
		if(estate_id ==1)
		{
			var sURL = "main.php?page_name=excelCCReport&estate_id=1";	
		}
		
	    document.location = sURL;
		
	}
	
	function getSpNextPage()
	{
	
		$("currentPage").value = parseInt($("currentPage").value)+1;
	   if($("search_field")==null)
		    refreshSpSalesList(false);
		else
			getInvoiceList(false);
	
	}

	function getSpPrevPage()
	{
		$("currentPage").value = parseInt($("currentPage").value)-1;
	   	if($("search_field")==null)
		    refreshSpSalesList(false);
		else
			getInvoiceList(false);
	}
function getVintages()
{

/*	var SKU ="";
	
	SKU =$('wine_id').value;
	var province_id = $("province_id").value;
	
	if(SKU!=-1 && province_id == 1)
		xajax_getVintageList(SKU);
	else
		resetVintage();
*/	
	
}

function resetVintage()
{
//	$('vintage').options.length=0;
//	$('vintage').options[0]=new Option("All", "-1", false, false);
}


function getInvoiceList(isGoButton)
{
	
	var estate_id ="";
	var searchType=1;
	var searchValue="";
	var isStartWith=0;
	var order_by="o.deliver_date";
	var order_type="a";

	
	var currentpage = 1;

		
	estate_id = $('current_estate_id').value;
	
	
	if(estate_id ==-1)
	{
		estate_id = $('estate_id').value;	
	}
	
	searchType = $('search_type').value;
	searchValue = $('search_field').value;
	
	if($('startwith').checked)
	{
		isStartWith=1;
	}

	

	if($('orderlistSortBy').value!="")
		order_by =$('orderlistSortBy').value;
	
	if($('orderlistSortType').value!="")
		order_type = $('orderlistSortType').value;
	
	if(isGoButton) // click button to refresh the list
	{
	 
		order_by = "delivery_date";
		order_type ="a";
	
	
	}
	else
	{
		if($('currentPage').value!="")
		{
			currentpage = $('currentPage').value;
		}
	}

	xajax_refreshSupplierSalesList(estate_id, searchValue, isStartWith, order_by,order_type, searchType, -1, -1,1,'',-1, currentpage,true);
	


	

}

	function format2SqlDate(dateValue)
	{	
		dateValue =dateValue.split("/");
	   var sqlDate = dateValue[2]+dateValue[0]+dateValue[1];
	   return sqlDate;
	}

	function initSearchPage(estate_id)
	{
		
		if(estate_id==97 || estate_id ==96) //enotecca: display the combox
		{
			$("tdEstate").style.display="block";
			$("tdEstateId").style.display="block";	
		}
		else //hide the combo box for display multipl estates
		{
			$("tdEstate").style.display="none";
			$("tdEstateId").style.display="none";
		}
		//display the empty list
		$("noresults").style.display="none";
		changeSearchFeild();
	}

	current_sale_year=2019;
	
	function setSalesYear(sale_year)
	{
		current_sale_year=	sale_year;
	}
	function initSpForm()
	{

		var province_id = $("province_id").value;
		var estate_id = $("estate").value;
		var isBCEstate = $("isBCEstate").value;
	//	xajax_getUsers(province_id,estate_id);	
		xajax_getStoreTypes(province_id,estate_id);
		xajax_getYears(province_id,estate_id);
		
		
		
		//get current year*/
		var date =new Date();

//		var sales_year =date.getFullYear() ; temporarlly disabled
		var sales_year =2019 ; // should be reivise to above date
	
		if((isBCEstate==0)&&$("province_id").value==1)
		{
		 	
			if($("estate").value ==-1) //enotecca
			{
					$("tdEstate").style.display="block";
			}
			else
			{
				$("tdEstate").style.display="none";
			}
			changeDate();
		}
		else
		{
		    supplierSales.chkdate[1].style.display="none";
			$("tdEstate").style.display="none";
			$("tdEstate").style.display="none";
			$("chkQut").checked=false;
			
			$("trDateRange1").style.display="none";
			supplierSales.chkdate[0].checked = false;
			$("sales_month").disabled = false;
			$("sales_qut").disabled = true;		 	
		}		

	//	if(isBCEstate==1)//international 
	//	{		 
			$("tdVintage").style.display="none";
	//	}
	//	else
	//	{
		//	$("tdVintage").style.display="block";
	//	}
	
		xajax_getMonths(province_id,estate_id, sales_year);			
		checkIfNoRecords();
			
		setCalendar();
	
	}

	function reloadData(isProvince)
	{
	 	var province_id = $("province_id").value;
	  	var estate_id = $("estate").value;
		var isBCEstate = $("isBCEstate").value;
		
	//	$("vintage").value = -1;
		
	 	if(estate_id =="")
			estate_id =-1;
	
		if(isProvince==0) //change estate
		{
			if($("estate_id").value =="")
			{
	  			$("estate").value="-1";
	  			estate_id = -1;
	  		}
		  	else
		  	{
				$("estate").value=$("estate_id").value;
				estate_id = $("estate_id").value;
			}
		}
		else if(isProvince==1)//change province
	   	{   
	   		xajax_getStoreTypes(province_id,estate_id);	
	  		xajax_getYears(province_id,estate_id);
	  		
	  		var cuurentDate = new Date();
			
			var curr_year = cuurentDate.getFullYear();
	
			var sales_year =curr_year; // when change the provice, the default yeas is always current sales year
			if(estate_id ==99 && province_id ==1) // should find a way to solve the problem
				sales_year = 2019;
		
			xajax_getMonths(province_id,estate_id, sales_year);
	
	  		if(province_id ==1)
	  		{
	  		 	supplierSales.chkdate[0].disabled =false;
				supplierSales.chkdate[0].checked=true;
			}
			else
			{
				supplierSales.chkdate[1].checked=true;
				supplierSales.chkdate[0].disabled =true;
			}
			changeDate();		
	  	}
	  		  
		if(province_id ==1 &&(isBCEstate==0))
		{
			$('orderlistSortBy').value="delivery_date";
		}
		else
			$('orderlistSortBy').value="customer_name";
			xajax_getWines4Supplier(province_id,estate_id);
		
		resetVintage();
		
		
		
		
	}
function refreshSpSalesList(isGoButton)
{
	
	var estate_id = $('estate').value;
	var dateType=0;
	var date1;
	var date2;
	var order_by;
	var order_type;

	var store_type_id=-1;
	var wine_id =-1;
	var user_id =-1;
	var vintage = -1;
	
	var province_id = $('province_id').value;
	var isBCEstate = $('isBCEstate').value;
	var currentpage = 1;

	 	if((isBCEstate==0))
	 	{
	 	 
	 		if(supplierSales.chkdate[0].checked)
	 		{	 	 	
				dateType =0;
				date1 = format2SqlDate($('from_1').value);  //mm/dd/yyyy should change to yyyy/mm/dd
				date2 = format2SqlDate($('to_1').value);  //mm/dd/yyyy should change to yyyy/mm/dd
			}
			else
			{
				date1 = $('sales_year').value;

				if($("chkQut").checked)  //quarter
				{
					dateType = 1;
					date2 = $('sales_qut').value;
				}
				else
				{
					dateType = 2;
					date2 = $('sales_month').value;
				}
			}
		}
		else
		{
			date1 = $('sales_year').value;

			if($("chkQut").checked)  //quarter
			{
				dateType = 1;
				date2 = $('sales_qut').value;
			}
			else
			{
				dateType = 2;
				date2 = $('sales_month').value;
			}
		}
		

	
	order_by =$('orderlistSortBy').value;
	order_type = $('orderlistSortType').value;
	
	if(isGoButton) // click button to refresh the list
	{ 
		if((isBCEstate==0)&&province_id==1) //bc supplier sales in bc
		{
			order_by = "delivery_date";
				order_type ="a";
		}
		else
		{
			order_by="customer_name";
			order_type ="a";
		}
		
		if( $('wine_id').value!="")
		{
			var wine_info =$('wine_id').value;
			if(wine_info==-1)	
			{
				wine_id =-1;
				vintage_id=-1;
			}
			else
			{
				var infos =wine_info.split("|"); //sku|wintage;
				
				wine_id =infos[0];
				vintage= infos[1];
				
			
				
			}
		}
			
	}
	else
	{
	 	if( $('wine_id').value!="")
		wine_id =$('wine_id').value;
	
		if( $('vintage').value!="")
			vintage =$('vintage').value;
	 
		if($('currentPage').value!="")
		{
			currentpage = $('currentPage').value;
		}
	}
	
	if($('user_id').value!="")
	{
		user_id = $('user_id').value;
	}
	
	if($('lkup_store_type_id').value!="")
	{
		store_type_id = $('lkup_store_type_id').value;
	}
	
	
	
	//alert(wine_id);
	//			alert(vintage);
		
	xajax_refreshSupplierSalesList(estate_id, date1, date2, order_by,order_type, dateType, store_type_id, user_id,province_id,wine_id,vintage, currentpage);

}
	
	function sortSpList(order_by)
	{
	    if (order_by)
	    {
	        var order_type;
	        var fldOrderBy = $('orderlistSortBy');
	        var fldOrderType = $('orderlistSortType');
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
	        
	        $('arrow_' + old_order_by).innerHTML = '';
	    }
	    
	    if($("search_field")==null)
		    refreshSpSalesList(false);
		else
			getInvoiceList(false);
	}
	
	function searchInvoices(estate_id)
	{
		var link ="main.php?page_name=supplierInvoices&estate_id="+estate_id;
		
		document.location = link;		
		stopEvent(window.event);
		return false;	
	}

	function setQuarterDesc()
	{
	 	var period =0;
	 	var qut_desc ="";
	 	
		period = $("sales_qut").value;
		
		if(period ==1)
		      qut_desc="January - March";
		
		if(period ==2)
	         qut_desc="April - June";
		
		if(period ==3)
		      qut_desc="July - September";
		
		if(period ==4)
		      qut_desc="October - December";
	
	  $("quarter_desc").value = qut_desc;
	
	}
	function setPaymentType(id)
	{
		$("payment_type").value=id;
	}
	function setCalendar()
	{
	     Calendar.setup( {
	        inputField:"from_1", ifFormat:"%m/%d/%Y", button:"from_1_calendar", singleClick:true, align:"Bl", cache:true, showOthers:true, onClose: dateCalendarClose
	    } );
	   
	    
	     Calendar.setup( {
	        inputField:"to_1", ifFormat:"%m/%d/%Y", button:"to_1_calendar", singleClick:true, align:"Bl", cache:true, showOthers:true, onClose: dateCalendarClose
	    } );
	   
	}
	
	function getParentlistRefresh()
	{
		window.opener.getInvoiceList(false);
		window.close();
	}
	
	function reload_parent_page()
	{
		window.opener.refreshPage();
		window.close();
	}
	
	function saveInvoice()
	{
	 	var order_id=$("order_id").value;
	 	var customer_id=$("customer_id").value;
	 	var inovice_number=$("inovice_number").value;
	 	var payment_type=$("payment_type").value;
	 	

	 	var user_id=$("user_id").value;
	 	var estate_id=$("estate_id").value;
	 	
	 	var isInnerView=$("isInnerView").value;
	 	
	 
	 	if(isInnerView==1)
	 	{
		 	var order_status=$("order_status").value;
	 		xajax_supplierUpdateInvoice(order_id,customer_id,inovice_number, payment_type,user_id,estate_id,order_status);
	 	}
	 	else
	 	{
			xajax_supplierUpdateInvoice(order_id,customer_id,inovice_number, payment_type,user_id,estate_id);			
		}	 	
	}

	function updateInvocie(order_id)
	{
	 	var estate_id ="";
		var searchType=1;
		var searchValue="";
		var isStartWith=0;
		var order_by="o.deliver_date";
		var order_type="a";	
		
		var currentpage = 1;
			
		estate_id = $('current_estate_id').value;
		
		if(estate_id ==-1)
		{
			estate_id = $('estate_id').value;	
		}
		
		searchType = $('search_type').value;
		searchValue = $('search_field').value;
		
		if($('startwith').checked)
		{
			isStartWith=1;
		}
		if($('orderlistSortBy').value!="")
			order_by =$('orderlistSortBy').value;
		
		if($('orderlistSortType').value!="")
			order_type = $('orderlistSortType').value;
		
		if($('currentPage').value!="")
		{
			currentpage = $('currentPage').value;
		}
		
		var user_id =$('user_id').value;
		
	    var ht = 500;
	    var wd = 750;
	
		var link="main.php?page_name=invoiceViewSP&id="+order_id+"&estate_id="+estate_id+"&searchValue="+searchValue+"&isStart="+isStartWith+"&order_by="+order_by+"&order_type="+order_type+"&searchType="+searchType+"&currentpage="+currentpage+"&user_id="+user_id;
		
		var left=(screen.availWidth-wd)/2;
		
		var top=(screen.availHeight-ht)/2;		
	
	    var printWindow = window.open(link, "Form60", "menubar=yes,scrollbars=yes, resizable=no, left="+left+",top="+top+",height=" + ht + ",width=" + wd);
	}


	function viewForm60(order_id)
	{
	    var ht = screen.availHeight;
	    var wd = 725;
	    var printWindow = window.open("main.php?page_name=Form60view&id=" + order_id, "Form60", "menubar,scrollbars,left=0,top=0,height=" + ht + ",width=" + wd);
		
		printWindow.resizeTo(725, screen.availHeight);
	}

	function viewDetails(order_id)
	{
	
	 	if($("search_type")==null)
			viewForm60(order_id);
		else
			updateInvocie(order_id);
	}

	function initInvoiceView(isHidePayment)
	{
	 
		if(isHidePayment==1)
		{
			$("tr_payment_method").style.display="none";
			$("tr_delivery_status").style.display="block";
			
		}
		else
		{
			$("tr_payment_method").style.display="block";
			$("tr_delivery_status").style.display="none";
		}
	}

function exportDSWReport()
{ 	

  
  
  
			var estate_id = $('estate').value;
			var dateType=0;
			var date1;
			var date2;
			var order_by;
			var order_type;
		
			var store_type_id=-1;
			var wine_id =-1;
			var user_id =-1;
			
			var province_id = $('province_id').value;
			
			var currentpage = 1;
			var isBCEstate=$("isBCEstate").value;

	
			/*	if(estate_id ==-1)
				{
					alert("Please select an estate.");
					return false;
				}
			*/
		
			 		if(supplierSales.chkdate[0].checked)
			 		{			 	 	
						dateType =0;
						date1 = format2SqlDate($('from_1').value);  //mm/dd/yyyy should change to yyyy/mm/dd
						date2 = format2SqlDate($('to_1').value);  //mm/dd/yyyy should change to yyyy/mm/dd
					}
					else
					{
						date1 = $('sales_year').value;
		
						if($("chkQut").checked)  //quarter
						{
							dateType = 1;
							date2 = $('sales_qut').value;
						}
						else
						{
							dateType = 2;
							date2 = $('sales_month').value;
						}
					}
				
			
	
				
				var sURL = "main.php?report_page_name=excelDSWRReport&estate_id=" + estate_id + "&date1=" + date1 + "&date2=" + date2 + "&dateType=" + dateType;
				
		
			    document.location = sURL;
	       
	       
	       
	
  
	stopEvt();
    return false;
}











