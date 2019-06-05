
//used by winelist control

function clearWineTable()
{
    var gridTable = document.getElementById('winelistTable');
    if (gridTable) gridTable.style.display = 'none';
}

function showWinelistLoading()
{
   /* clearWineTable();
    var divLoading = document.getElementById('loadingMsgwines');
    if (divLoading)
    {
        divLoading.innerHTML = 'Loading ...';
        divLoading.style.display = 'block';
    }*/
}

function sortWines(order_by)
{
    if (order_by)
    {
        var order_type;
        var fldOrderBy = document.getElementById('winelistOrderBy');
        var fldOrderType = document.getElementById('winelistOrderType');
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
    refreshWines();
}

function refreshWines()
{
    var customer_id = document.getElementById('winelistCustomerID').value;
    var order_by = document.getElementById('winelistOrderBy').value;
    var order_type = document.getElementById('winelistOrderType').value;
    var estate_id = document.getElementById('estate_id').value;
    xajax.doneLoadingFunction  = function(){;}; //empty function
    if (estate_id)
    {
        xajax_refreshWines(customer_id, order_by, order_type, estate_id);
    }
    else clearWineTable();
    tglOrderButton(false);
}

function canCreateOrder()
{
    var retVal = false;
    if (parseInt(document.getElementById('wineCount').value) > 0)
    {
        var arOrders = getElementsByClass('orderinput', null, 'input');
        for (i=0; i<arOrders.length; i++)
        {
            var qty = parseInt(arOrders[i].value);
            if (qty > 0)
            {
                var lblID = arOrders[i].name.replace('Order', 'Avl');
                var avl = parseInt(document.getElementById(lblID).innerText);
                if (qty > avl)
                    arOrders[i].value = avl;
               retVal = true;
            }
        }
    }
    return retVal;
}


function tglOrderButton(bEnable)
{
    var btn = document.getElementById('buttonF60');
    if (btn)
        btn.disabled = !bEnable;
}

function createOrder4f60()
{
	if (!canCreateOrder())
		{
		  alert("You must order at least one wine.");
		  tglOrderButton(false);
		  return false;
		}
		else
		{
		  var customer_id = document.getElementById('winelistCustomerID').value;
		  var estate_id = document.getElementById('estate_id').value;
		//      showWinelistLoading();
		  xajax_createOrder(customer_id, estate_id, xajax.getFormValues('customerAdd', false, 'Order['));
		  return true;
		}
}


function crtOrder(retVal)
{ 
 	if(retVal == parent.IDYES)
 	{
		createOrder4f60();
	}
}

function openpage()
{
	
}
function createOrd()
{
 	var msg ="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;There is an outstanding unpaid invoice on file for this customer. <br> <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Are you sure you still want to proceed with the creation of this new order?";
	if(document.getElementById('paid_status').value ==1)
	{
	 	
		 parent.showMsgBox( msg, parent.MBYESNO + parent.ICONQUESTION, crtOrder);
	}
	else
	{
		createOrder4f60();
	}
}

function orderKeyPress(ctl, event)
{
    var allow = chkMaskINTEGER(this, event);
    if (allow) tglOrderButton(true);
    return allow;
}
