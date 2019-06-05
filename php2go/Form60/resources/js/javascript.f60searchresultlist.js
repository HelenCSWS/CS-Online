function sortTopCM(order_by)
{
	var sort_column_id =6;

	var imgSpan = "#arrow_"+order_by;
	switch (order_by)
	{
		case 'customer_name':
		{
			sort_column_id = 0;
		}
			break;
		case 'address':
			sort_column_id = 1;
			break;
		case 'licensee_no':
			sort_column_id = 2;
			break;
		case 'store_type':
			sort_column_id = 3;
			break;
		case 'total_cases':
			sort_column_id = 4;
			break;
		case 'wh_sales':
			sort_column_id = 5;
			break;
		case 'total_sales':
			sort_column_id = 6;
			break;
		case 'user_name':
			sort_column_id = 7;
			break;
		
	}
	

             
	
	var odbyname ="ListOrderBy";
	var odtyname ="ListOrderType";
	var lpagename ="ListPage";
	var order_type;
	var fldOrderBy = document.getElementById(odbyname);
	var fldOrderType = document.getElementById(odtyname);
	var order_img = "6";	
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
		order_img = (fldOrderType.value == 'a')?'6':'5';

        fldOrderType.value = order_type;
        
    }
   
    $(imgSpan).text(order_img);

    document.getElementById( 'arrow_'+old_order_by).innerHTML = '';
    document.getElementById(lpagename).value = 1;

    
	 $("#tbResult").tablesorter( {sortList: [[sort_column_id,0]]} ); 
	 
	 
}
function sortf60resaultlist(order_by)
{
    if (order_by)
    {
        var odbyname ="ListOrderBy";
        var odtyname ="ListOrderType";
         var lpagename ="ListPage";
       var order_type;
        var fldOrderBy = document.getElementById(odbyname);
        var fldOrderType = document.getElementById(odtyname);
        
     //   alert(odbyname);
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

        document.getElementById( 'arrow_'+old_order_by).innerHTML = '';
        document.getElementById(lpagename).value = 1;
    }
    refresf60searchResultLists();
}

function refresf60searchResultLists(statusid)
{
    var obname ="ListOrderBy";
    var odtname ="ListOrderType";
    var lpname ="ListPage";
    
    var order_by = document.getElementById(obname).value;
    var order_type = document.getElementById(odtname).value;
    var list_page = document.getElementById(lpname).value;
    
    var search_id=document.getElementById("search_id").value;
    var sales_year=document.getElementById("sales_year").value;
    var sales_period=document.getElementById("sales_period").value;
    var isQtr=document.getElementById("isQtr").value;
    var store_type_id=document.getElementById("store_type_id").value;
    var user_id=document.getElementById("user_id").value;
    var search_adt1=document.getElementById("search_adt1").value;
    var search_adt2=document.getElementById("search_adt2").value;
    var city=document.getElementById("city").value;
    var product_id=document.getElementById("product_id").value;

	 xajax_refresf60searchResultLists(order_by, order_type, list_page,search_id,sales_year,sales_period,isQtr,store_type_id,user_id,search_adt1,search_adt2,city,product_id);



}
function printResults()
{
 	var search_id=document.getElementById("search_id").value;
	var search_key=document.getElementById("search_key").value;
   var sales_year=document.getElementById("sales_year").value;
   var sales_period=document.getElementById("sales_period").value;
   var isQtr=document.getElementById("isQtr").value;
   var store_type_id=document.getElementById("store_type_id").value;
   var user_id=document.getElementById("user_id").value;
   var search_adt1=document.getElementById("search_adt1").value;
   var search_adt2=document.getElementById("search_adt2").value;
   var start_with=document.getElementById("isStart").value;
   var isOneRec=document.getElementById("isOneRec").value;
   var city=document.getElementById("city").value;
   var product_id=document.getElementById("product_id").value;
		   
	
	var isOpen = true;
	if(search_id!=3&&search_id!=1)
	{
	 	var totalRecs=document.getElementById("total_recs").value;
	
	 	if(totalRecs>700) 
	 	{
				alert("Restults are over than 700, please reselect the option to decrease the records.")
				isOpen =false;			
		}
	
	}
	
	if(isOpen)
	{
			link="main.php?page_name=f60SearchWinesReports_print&search_id="+search_id+"&search_key="+search_key+"&year="+sales_year+"&period="+sales_period+"&isQtr="+isQtr+"&store_type="+store_type_id+"&user_id="+user_id+"&search_adt1="+search_adt1+"&search_adt2="+search_adt2+"&start_with="+start_with+"&city="+city+"&product_id="+product_id;
		
	//	alert(link);
		window.open(link,"test","height=600,width=800,status=yes,toolbar=no,menubar=yes,location=no,resizable=yes,scrollbars=yes");
	}

}

function goBack()
{		
	var search_id=document.getElementById("search_id").value;
	var search_key=document.getElementById("search_key").value;
    var sales_year=document.getElementById("sales_year").value;
    var sales_period=document.getElementById("sales_period").value;
    var isQtr=document.getElementById("isQtr").value;
    var store_type_id=document.getElementById("store_type_id").value;
    var user_id=document.getElementById("user_id").value;
    var search_adt1=document.getElementById("search_adt1").value;
    var search_adt2=document.getElementById("search_adt2").value;
    var start_with=document.getElementById("isStart").value;
    var isOneRec=document.getElementById("isOneRec").value;
    var city=document.getElementById("city").value;
    var product_id=document.getElementById("product_id").value;

    var link="";
   
   if(search_id==0)
   {    
    	if(search_id==0&&search_adt2==2)
    	{    	 
			link="main.php?page_name=searchf60&isWine=1&search_id="+search_id+"&search_key="+search_key+"&year="+sales_year+"&sale_period="+sales_period+"&isQtr="+isQtr+"&store_type="+store_type_id+"&user_id="+user_id+"&search_adt="+search_adt2+"&city="+city;
		}
		else if(search_adt1==""||isOneRec==1)
		{
	
			link="main.php?page_name=searchf60&isWine=1&search_id="+search_id+"&search_key="+search_key+"&year="+sales_year+"&sale_period="+sales_period+"&isQtr="+isQtr+"&store_type="+store_type_id+"&user_id="+user_id+"&search_adt=1"+"&city="+city;
	

		}
		else
		{
				link = "main.php?page_name=f60PreSearch&search_id="+search_id+"&search_key="+search_key+"&year="+sales_year+"&sale_period="+sales_period+"&isQtr="+isQtr+"&store_type="+store_type_id+"&user_id="+user_id+"&isStart="+start_with+"&search_adt="+search_adt2+"&city="+city;

		}
	}
	else if(search_id==1)
   {    
		if(search_adt1==""||isOneRec==1)
		{
			link="main.php?page_name=searchf60&isWine=1&search_id="+search_id+"&search_key="+search_key+"&year="+sales_year+"&sale_period="+sales_period+"&isQtr="+isQtr+"&store_type="+store_type_id+"&user_id="+user_id+"&search_adt="+search_adt2;
		}
		else
		{
				link = "main.php?page_name=f60PreSearch&search_id="+search_id+"&search_key="+search_key+"&year="+sales_year+"&sale_period="+sales_period+"&isQtr="+isQtr+"&store_type="+store_type_id+"&user_id="+user_id+"&isStart="+start_with+"&search_adt="+search_adt2;

		}
	}
	else if(search_id==2)
   {
			link="main.php?page_name=searchf60&isWine=1&search_id="+search_id+"&search_key="+search_key+"&year="+sales_year+"&sale_period="+sales_period+"&isQtr="+isQtr+"&store_type="+store_type_id+"&user_id="+user_id+"&search_adt1="+search_adt1+"&search_adt2="+search_adt2;
	}
	else if(search_id==3)
   {
		if(isOneRec==1)
		{
			link="main.php?page_name=searchf60&isWine=1&search_id="+search_id+"&search_key="+search_key+"&year="+sales_year+"&sale_period="+sales_period+"&isQtr="+isQtr+"&store_type="+store_type_id+"&user_id="+user_id+"&search_adt="+search_adt2;
	
		}
		else
		{
				link = "main.php?page_name=f60PreSearch&search_id="+search_id+"&search_key="+search_key+"&year="+sales_year+"&sale_period="+sales_period+"&isQtr="+isQtr+"&store_type="+store_type_id+"&user_id="+user_id+"&isStart="+start_with+"&search_adt="+search_adt2;

		}
	}
	link=link+"&product_id="+product_id;
	document.location = link;
    stopEvt();

}

function getNextPage()
{
    var lpname ="ListPage";
    document.getElementById(lpname).value = parseInt(document.getElementById(lpname).value, 10) + 1;
    refresf60searchResultLists();
}

function getPrevPage()
{
    var lpname ="ListPage";
    document.getElementById(lpname).value = parseInt(document.getElementById(lpname).value, 10) - 1;
    refresf60searchResultLists();
}

function hideSales()
{
	var search_id=document.getElementById("search_id").value;
	 var search_adt2=document.getElementById("search_adt2").value;
	 
	 if(search_id==0 && search_adt2==2)
	 {
			document.getElementById("showCase").style.display="none";
			document.getElementById("showWH").style.display="none";
			document.getElementById("showRT").style.display="none";
	 }
}
function initRestultForm()
{

 	document.getElementById("noresults").style.display="block";
 
   	if(document.getElementById("search_id").value!=3 && document.getElementById("total_recs").value==0) 
 	{
 
		document.getElementById("display_results1").style.display="none";
		document.getElementById("display_results2").style.display="none";
		document.getElementById("noresults").style.display="block";
	}
	else
	{
		document.getElementById("display_results1").style.display="block";
		document.getElementById("display_results2").style.display="block";
		document.getElementById("noresults").style.display="none";

	 	if(document.getElementById("search_id").value==3)
	 	{
			document.getElementById("tdBtns").align="middle";
		
		}
		else
		document.getElementById("tdBtns").align="right";
	}
//	alert( document.getElementById("tbButtons").width);
}