function changesearchTab(id)
{

    var tab0 = document.getElementById("tab0");
    var tab1 = document.getElementById("tab1");

    tab0.className = "tab";
    tab1.className = "tabRight";
  	//	var trName= "trComm_pro"+id;

    if (id==0)//customers
    {

		document.getElementById("trSrchCM").style.display="block";
		document.getElementById("isWine").value="0";
		document.getElementById("trSrchWine").style.display="none";
		tab0.className = "tabActive";
		document.getElementById("isWine").value=0;
		
		document.getElementById("search_field").focus();
    }
    else if(id==1)//wine
    {
		document.getElementById("trSrchWine").style.display="block";
		document.getElementById("isWine").value="1";
		document.getElementById("trSrchCM").style.display="none";
		tab1.className = "tabActive";
		document.getElementById("isWine").value=1;
		
		initSearchWineForm();
			
			
		if(document.getElementById("search_id_w").value!=2)
			document.getElementById("search_field_w").focus();
    }

}

function changeSearchPeriod()
{
	if(document.getElementById("chkQut").checked) 
	{
		document.getElementById("sales_month").disabled=true;
		document.getElementById("sales_qut").disabled=false;
	}
	else
	{
		document.getElementById("sales_month").disabled=false;
		document.getElementById("sales_qut").disabled=true;
		document.getElementById("quarter_desc").value = "";
	}
}

function setQuarterDesc()
{
 	var period =0;
 	var qut_desc ="";
 	
	period = document.getElementById("sales_qut").value;
	if(period ==1)
	      qut_desc="January - March";
	
	if(period ==2)
         qut_desc="April - June";
	
	if(period ==3)
	      qut_desc="July - September";
	
	if(period ==4)
	      qut_desc="October - December";

  document.getElementById("quarter_desc").value = qut_desc;
	  
}

function setWithoutDate()
{
	if(document.getElementById("chkNoDate").checked)
	{
		document.getElementById("sales_year").disabled =true;
		document.getElementById("sales_month").disabled =true;
		document.getElementById("sales_qut").disabled =true;
		document.getElementById("chkQut").disabled =true;		
	}
	else
	{
	 	document.getElementById("sales_year").disabled =false;
		document.getElementById("sales_month").disabled =true;
		document.getElementById("sales_qut").disabled =false;
		document.getElementById("chkQut").disabled =false;
		document.getElementById("chkQut").check=true;
	}
}

function changeKey_w(id)
{
	document.getElementById('search_id_w').value = id;
	if(id==2)
	{
		document.getElementById('search_field_w').disabled=true;
		document.getElementById("search_field_w").style.borderColor ="#A9A9A9";
	}
	else
	{
		document.getElementById('search_field_w').disabled=false;
		document.getElementById("search_field_w").style.borderColor ="#7F9DB9";
		if(id!=2)
			document.getElementById('search_field_w').focus();			
	}
}
function setProductId(id)
{


	document.getElementById("product_id").value=id;
	

	xajax_setProductTypes(id);
	
}
function searchWines()
{
	var month="";
	var year=document.getElementById("sales_year").value;
	
	var isQtr=0;
	var period="";
	var startwith="0";
	
	var search_adt ="";
	
	var wine_type ="";
	var city="";
	
	
	var store_type_id =document.getElementById("lkup_store_type_id_w").value;
	var product_id = document.getElementById("product_id").value;
	
	
	
	if(store_type_id =="")
		store_type_id=-1;
		
	var user_id=document.getElementById("user_id_w").value;
	if(user_id =="")
		user_id=-1;
	
	
	var search_id = document.getElementById("search_id_w").value;
	
	var search_key=document.getElementById("search_field_w").value;
		
	var start_with=0;
	
	if(document.getElementById("startwith_w").checked)
	{
	 	start_with=1;
	}
	
	if(document.getElementById("chkQut").checked)
	{
		isQtr =1;
		period = document.getElementById("sales_qut").value;
		
	}
	else
	{
		period=document.getElementById("sales_month").value;
	}
	
	if( search_id ==0 )// who has purchased/not wines
	{
		search_adt = 	document.getElementById("is_purchased").value;
		city = document.getElementById("city").value;
	}
	if( search_id ==1 )// top 5 customers
	{
		search_adt = 	document.getElementById("cm_number").value;
	}
	if( search_id ==2 )//top 5 selling wines
	{
		search_adt = 	document.getElementById("wine_number").value;
		wine_type = 	document.getElementById("lkup_wine_color_type_id").value;2
		if(wine_type=="")
			wine_type ="-1";
	}
	
	if( search_id ==3 ) //total sales
	{
		search_adt = 	document.getElementById("sku_name").value;
	}
	
	var link="";
	

	
	if(search_id<2)
	{
		if(search_key=="")
		{
			link	= "main.php?page_name=f60SearchWinesReports&search_id="+search_id+"&year="+year+"&period="+period+"&isQtr="+isQtr+"&store_type="+store_type_id+"&user_id="+user_id+"&search_adt1="+"&search_adt2="+search_adt+"&city="+city;
		}
		else
		{
			link = "main.php?page_name=f60PreSearch&search_id="+search_id+"&search_key="+search_key+"&year="+year+"&sale_period="+period+"&isQtr="+isQtr+"&store_type="+store_type_id+"&user_id="+user_id+"&isStart="+start_with+"&search_adt="+search_adt+"&city="+city;
		}
	
	}
	else if(search_id==2)
	{
		link	= "main.php?page_name=f60SearchWinesReports&search_id="+search_id+"&year="+year+"&period="+period+"&isQtr="+isQtr+"&store_type="+store_type_id+"&user_id="+user_id+"&search_adt1="+search_adt+"&search_adt2="+wine_type;
	}
	else if(search_id==3)
	{
		link = "main.php?page_name=f60PreSearch&search_id="+search_id+"&search_key="+search_key+"&year="+year+"&sale_period="+period+"&isQtr="+isQtr+"&store_type="+store_type_id+"&user_id="+user_id+"&isStart="+start_with+"&search_adt="+search_adt;
	}
	
	link =link +"&product_id="+product_id;
//	alert(link);
	document.location = link;
    stopEvt();

   return false;
   
}


function getWinesReport()
{
	var period=document.getElementById("period").value;
	var year=document.getElementById("year").value;
	var isQtr=document.getElementById("isQtr").value;
	var start_with=document.getElementById("start_with").value;
	
	var search_adt =document.getElementById("search_adt").value;
	
	var wine_type =document.getElementById("wine_type").value;
	
	var store_type_id =document.getElementById("store_type_id").value;
	

	var user_id=document.getElementById("user_id").value;
	
	var search_id = document.getElementById("search_id").value;
	
	var search_key=document.getElementById("search_key").value;
	var cspc_code = document.getElementById("wine_id").value;
	var city = document.getElementById("city").value;
	var product_id = document.getElementById("product_id").value;	
		
	var link ="";
	link="main.php?page_name=f60SearchWinesReports&search_id="+search_id+"&search_key="+search_key+"&year="+year+"&period="+period+"&isQtr="+isQtr+"&store_type="+store_type_id+"&user_id="+user_id+"&search_adt1="+cspc_code+"&search_adt2="+search_adt+"&start_with="+start_with+"&city="+city+"&product_id="+product_id;


	document.location = link;
   	stopEvt();
}
function back2Search()
{	
	var search_id = document.getElementById("search_id").value;
	var period=1;
	var isQtr=document.getElementById("isQtr").value;

	var period=document.getElementById("period").value;
		
	var year=document.getElementById("year").value;
	var start_with=document.getElementById("start_with").value;
	var search_adt =document.getElementById("search_adt").value;
	var wine_type =document.getElementById("wine_type").value;
	var store_type_id =document.getElementById("store_type_id").value;
	var user_id=document.getElementById("user_id").value;
	
	
	var search_key=document.getElementById("search_key").value;
	
	var city=document.getElementById("city").value;
	var product_id=document.getElementById("product_id").value;
	
		
	var link ="";
	link="main.php?page_name=searchf60&isWine=1&search_id="+search_id+"&search_key="+search_key+"&year="+year+"&sale_period="+period+"&isQtr="+isQtr+"&store_type="+store_type_id+"&user_id="+user_id+"&search_adt="+search_adt+"&isStart="+start_with+"&city="+city+"&product_id="+product_id;

	document.location = link;
   	stopEvt();
	
}

function initSearchWineForm()
{

	if(document.getElementById("isStart").value==0)
	{
		document.getElementById("startwith_w").checked=false;
	}
	else
	{
		
		document.getElementById("startwith_w").checked=true;
	}
		

	xajax_setProductTypes(document.getElementById("product_id").value);		
		
   if(document.getElementById("isQtr").value==0)
   {
		document.getElementById("sales_qut").disabled=true;
		document.getElementById("sales_month").disabled=false;
		document.getElementById("chkQut").checked=false;


	}
	else
	{
		document.getElementById("sales_qut").disabled=false;
		document.getElementById("sales_month").disabled=true;
		document.getElementById("chkQut").checked=true;
		setQuarterDesc();
		
	}	

		var search_id = document.getElementById("search_id_w").value;
	//	alert(search_id);
		searchf60.searchKey_w[search_id].checked=true;
		changeKey_w(search_id);

}
function forminit()
{

 	document.getElementById("wine_id").selectedIndex = 0;	
 
 	if(document.getElementById("isNoWine").value == 0)
 	{
		document.getElementById("showWine").style.display="block";
		document.getElementById("trNowine").style.display="none";
	}
	else
	{
		document.getElementById("showWine").style.display="none";
		document.getElementById("trNowine").style.display="block";
	}  
}










