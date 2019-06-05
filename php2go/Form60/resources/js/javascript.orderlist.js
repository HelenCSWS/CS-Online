
//used by orderlist control

function clearOrderTable()
{
    var gridTable = document.getElementById('orderlistTable');
    if (gridTable) gridTable.style.display = 'none';
}

function showOrderlistLoading()
{
    clearOrderTable();
    var divLoading = document.getElementById('loadingMsgorders');
    if (divLoading)
    {
        divLoading.innerHTML = 'Loading ...';
        divLoading.style.display = 'block';
    }
}

function sortOrders(order_by)
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
    refreshOrders();
}

function refreshOrders()
{
    var customer_id = document.getElementById('orderlistCustomerID').value;
    var order_by = document.getElementById('orderlistSortBy').value;
    var order_type = document.getElementById('orderlistSortType').value;
    var order_estate_id = document.getElementById('order_estate_id').value;
    var order_year = document.getElementById('order_year').value;
    
    
    var period ="";
    var isQut =0;
    if(document.getElementById("chkOdQut").checked)
    {
    	period = document.getElementById('order_qut').value;
    	isQut =1;
    	setQuarterDesc(1);
    	
   }
    else
    {
 	 	period = document.getElementById('order_month').value;
   }
   
    showOrderlistLoading();
    xajax_refreshOrders(customer_id, order_by, order_type, order_estate_id, order_year,period,isQut);
}

function changeSalesPeriod()
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
	

	refreshSalesList();
}

function changeOrderPeriod()
{
	if(document.getElementById("chkOdQut").checked) 
	{
		document.getElementById("order_month").disabled=true;
		document.getElementById("order_qut").disabled=false;
		
		setQuarterDesc(1);
	}
	else
	{
		document.getElementById("order_month").disabled=false;
		document.getElementById("order_qut").disabled=true;
		document.getElementById("order_quarter_desc").value = "";
	}
	

	refreshOrders();
}

function setQuarterDesc(isOrder)
{
 	var period =0;
 	var qut_desc ="";
 	
 
 	if (isOrder==0)
		period = document.getElementById("sales_qut").value;
	else
		period = document.getElementById("order_qut").value;
		
		
		if(period ==1)
		      qut_desc="January - March";
 		
		if(period ==2)
	         qut_desc="April - June";
		
		if(period ==3)
		      qut_desc="July - September";
		
		if(period ==4)
		      qut_desc="October - December";
  		

	if(isOrder==0)
	  document.getElementById("quarter_desc").value = qut_desc;
	else
	  document.getElementById("order_quarter_desc").value = qut_desc;
	  
}
function sortSalesList(order_by)
{
    if (order_by)
    {
        var order_type;
        var fldOrderBy = document.getElementById('saleslistSortBy');
        var fldOrderType = document.getElementById('saleslistSortType');
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
    refreshSalesList();
}
function getsales(isNext)
{
 	
 	var currentpage = document.getElementById('current_sales_page').value;
 	var totalPages = document.getElementById('total_sales_pages').value;
	if(isNext == 0) // preivous
	{
		if(currentpage > 1)
		{
			currentpage = currentpage-1;
		}
	}
	else
	{
		if(currentpage < totalPages)
		{
			currentpage = currentpage+1;
		}
	}
	
	refreshSalesList();
}

function refreshSalesList()
{
 //   var customer_id = document.getElementById('saleslistCustomerID').value;
    var customer_id = document.getElementById('customer_id').value;
    var order_by = document.getElementById('saleslistSortBy').value;
    var order_type = document.getElementById('saleslistSortType').value;
    var province_id = document.getElementById('province_id').value;
    var sale_year = document.getElementById('sales_year').value;
    var store_type_id = document.getElementById('lkup_store_type_id').value;
    var current_page = document.getElementById('current_sales_page').value;
    
    var sale_period ="";
    var isQut =0;
    if(document.getElementById("chkQut").checked)
    {
    	sale_period = document.getElementById('sales_qut').value;
    	isQut =1;
    	setQuarterDesc(0);
    	
   }
    else
    {
 	 	sale_period = document.getElementById('sales_month').value;
   }

	 xajax_refreshSalesList(customer_id, order_by, order_type, sale_year, sale_period, isQut,store_type_id, current_page,province_id);
    
   
}
function editOrder(order_id)
{
    showOrderForm(order_id);
}

var g_order_id;

function canDeleteOrder(order_status_id)
{
    //don't allow deletes for delivered order
	
    if (order_status_id == 2)
    {	

        window.parent.showMsgBox("This order has the status of Delivered, \nyou can’t delete a delivered order.\nIf you want to delete this order \nchange the delivery status to Pending.", parent.MBOK + parent.ICONEXCLAIM, null);
       // window.top.showMsgBox("This order\nIf you want to delete this order \nchange the delivery status to Pending.", parent.MBOK + parent.ICONEXCLAIM, null);
        return false;
    }
    else
        return true
}

function deleteOrder(order_id, order_status_id)
{
    if (canDeleteOrder(order_status_id)) 
    {
        g_order_id = order_id;
        parent.showMsgBox("You are about to delete an order.\n Do you want to proceed?", parent.MBYESNO + parent.ICONQUESTION, deleteOrderHandler);
    }
}

function deleteOrderHandler(retVal)
{
    if (retVal == parent.IDYES)
    {
        var order_id = g_order_id;
        g_order_id = 0;
        var customer_id = document.getElementById('orderlistCustomerID').value;
        var order_by = document.getElementById('orderlistSortBy').value;
        var order_type = document.getElementById('orderlistSortType').value;
        var order_estate_id = document.getElementById('order_estate_id').value;
        var order_year = document.getElementById('order_year').value;
        var period ="";
			var isQut =0;
			if(document.getElementById("chkOdQut").checked)
			{
				period = document.getElementById('order_qut').value;
				isQut =1;
				setQuarterDesc(1);
			
			}
			else
			{
				period = document.getElementById('order_month').value;
			}
   
        showOrderlistLoading();
        xajax.doneLoadingFunction  = refreshWines;
       // xajax_deleteOrder(customer_id, order_by, order_type, order_id, order_estate_id, order_year);
        
         xajax_deleteOrder(customer_id, order_by, order_type,order_id, order_estate_id, order_year,period,isQut);
    }
    else
        return true;
}

function showOrderForm(order_id)
{
    parent.gotoPage("main.php?page_name=orderEdit&id=" + order_id);;
}

function showOnlyWineMsg()
{
    alert("Wine orders must be on separate Form 60 invoice from the Olive oil or the Mosto cotto.");
}

function viewForm60(order_id)
{
    var ht = screen.availHeight;
    var wd = 725;
    var printWindow = window.open("main.php?page_name=Form60view&id=" + order_id, "Form60", "menubar=yes,scrollbars,left=0,top=0,height=" + ht + ",width=" + wd);
    printWindow.resizeTo(725, screen.availHeight);
}

var g_order_id;

function saveOrderViewForm60(order_id)
{
    if (formDirty)
    {
        g_order_id = order_id;
        parent.showMsgBox("Do you want to save your changes?" , parent.MBYESNO + parent.ICONQUESTION, saveHandler);
    }
    else
        viewForm60(order_id);
}

function saveHandler(retVal)
{
 	
    if (retVal == parent.IDYES)
    {
        xajax.doneLoadingFunction  = function(){viewForm60(g_order_id); g_order_id = null; xajax.doneLoadingFunction  = function(){;};};
        xajax_saveOrderForm(xajax.getFormValues('orderEdit'));
        formDirty = false;
    }
    else
        viewForm60(g_order_id);
}

function recalculate()
{
	var GST_factor=0.05;
	
    var arWines = getElementsByClass("orderinput", null, "input");
    var orderSubTotal = 0.0;
    var orderGrandTotal = 0.0;
    var GST = 0.0;
    var licenseeFactor = 0.0;
    var litterDepositTotal = 0.0;
    var deposit = 0.0;
    for (i=0; i<arWines.length; i++)
    {
        var qty = parseInt(arWines[i].value);
        var price =  document.getElementById(arWines[i].id).getAttribute("price");
        var litter =  document.getElementById(arWines[i].id).getAttribute("litter");
      
        orderSubTotal += qty * parseFloat(filterNum(price));
        litterDepositTotal += qty * parseFloat(filterNum(litter));
    }

    deposit = parseFloat(filterNum(document.getElementById("deposit").value));
    if (deposit > 0.0)
        litterDepositTotal = deposit;
    GST = GST_factor * orderSubTotal;
    
  
    licenseeFactor = parseFloat(document.getElementById("agency_LRS_factor").value) * orderSubTotal;
    
    orderGrandTotal = orderSubTotal + litterDepositTotal - licenseeFactor+GST;
                
  
   var test = parseFloat(orderSubTotal) + parseFloat(litterDepositTotal) - parseFloat(licenseeFactor)
                - parseFloat(filterNum(document.getElementById("adjustment_1").value));
    
     // alert(document.getElementById("adjustment_1").value);
    document.getElementById("order_subtotal").innerHTML = formatCurrency(orderSubTotal);
    document.getElementById("litter_deposit_total").innerHTML = formatCurrency(litterDepositTotal);
    document.getElementById("total_value").innerHTML = formatCurrency(orderGrandTotal);
    
    document.getElementById("GST").innerHTML =formatCurrency(GST);
    licName = document.getElementById("license_name").innerText;
    if (licName == "Licensee")
        document.getElementById("licensee_factor_total").innerHTML = formatCurrency(licenseeFactor);
    else if (licName == "Agency" || licName == "L.R.S.")
        document.getElementById("Agency_LRS_factor_total").innerHTML = formatCurrency(licenseeFactor);

    return true;
}


// update by Helen for mysql4 upgrade to mysql5
/*function qtyChanged(txtQty)
{
    var oSold = document.getElementById(txtQty.id.replace("Order", "sold"));
    var oAvl = document.getElementById(txtQty.id.replace("Order", "available"));
    var oPrice = document.getElementById(txtQty.id.replace("Order", "product_subtotal"));
    var oldVal = parseInt(txtQty.oldvalue);
    var avl = parseInt(oAvl.innerHTML) + oldVal;
    if (parseInt(txtQty.value) > avl)
        txtQty.value = avl;
    var diff = parseInt(txtQty.value) - parseInt(txtQty.oldvalue);
    
    if (diff != 0)
    {
        oSold.innerHTML = parseInt(oSold.innerHTML) + diff;
        oAvl.innerHTML = parseInt(oAvl.innerHTML) - diff;
        oPrice.innerHTML = formatCurrency(parseInt(txtQty.value) * parseFloat(filterNum(txtQty.price)));
        txtQty.oldvalue = txtQty.value;
        
        recalculate();
    }
}*/


function qtyChanged(txtQty)
{
	var oSold = document.getElementById(txtQty.id.replace("Order", "sold"));
    var oAvl = document.getElementById(txtQty.id.replace("Order", "available"));
    var oPrice = document.getElementById(txtQty.id.replace("Order", "product_subtotal"));
   
   
   var oldVal = parseInt(txtQty.getAttribute("oldvalue"));
   var price =  txtQty.getAttribute("price");
 
    var avlVal = parseInt(oAvl.innerHTML) + oldVal;    
   
	var qtyVal = parseInt((txtQty.value=="")?0:txtQty.value);

	
    if (qtyVal > avlVal)
	{
		alert("Not enough wines available! Please allocate more wines to this customer.");
		txtQty.value=oldVal;
	}
	else
	{
	 	var diff = qtyVal - oldVal;
	 	oSold.innerHTML = parseInt(oSold.innerHTML) + diff;
	 	oAvl.innerHTML = parseInt(oAvl.innerHTML) - diff;
	 	txtQty.setAttribute("oldvalue",txtQty.value);
	 	oPrice.innerHTML = formatCurrency(parseInt(qtyVal) * parseFloat(filterNum(price)));
		recalculate();
	}
  
}