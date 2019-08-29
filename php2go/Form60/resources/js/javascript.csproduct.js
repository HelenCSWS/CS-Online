function initPST()
{
      if(document.getElementById("customer_id").value!="" )
    {
        document.getElementById("chk_PST").onclick=function(){setPST4CM()};
		
	
        if($("#pst_no").val()!="")
        {
           
            $("#chk_PST").attr('checked', true);
        }
        else
        {
           
             $("#chk_PST").attr('checked', false);
             
             
        }
        setPST4CM();
        
        if($("#cs_products_id").val()==188)
        {
        
           // $("#PST_table").hide();
        }
        else{
            $("#PST_table").show(); 
        }       
    }
}

function setOtherDelivery()
{
    if(document.getElementById("chk_other_delivery").checked)
    {
        $("#is_other_delivery").val(1);
    }
    else
    {
        $("#is_other_delivery").val(0);
    }
    this.formDirty=true;
}

function setPST4CM()
{
     if(document.getElementById("chk_PST").checked)
     {
       
        $("#pst_no").removeAttr('disabled');
     }    
     else
     {
       
        $("#pst_no").attr('disabled', 'disabled');
     }
}
function initCSOrder() // init edit cs order page
{
  //override php2go date format
   Calendar.setup( {
       inputField:"delivery_date", ifFormat:"%m/%d/%Y", button:"delivery_date_calendar", singleClick:true, align:"Bl", cache:true, showOthers:true, onClose: dateCalendarClose
    })
    
    //discount
    $( "#chk_Disc" ).prop( "checked", ($("#discType").val()>0)?true:false );  
    enableAllDiscount($("#discType").val());
    
   
    if($("#estate_id").val()==196)
	{
	
    	$("#isPST").val(1); // no PST
		$("#pst_row").hide();
		
	}
	else{
 
		document.getElementById("chk_PST").checked = ($("#isPST").val()==0)?false:true;
	 
		var isPst =  ($("#isPST").val()==0)?false:true;
		setPSTCtls(isPst);        
		//event
		document.getElementById("chk_PST").onclick=function(){resetPST(this)};
		
	  //  alert($("#pst_rate_val").html());
		
		if($("#pst_rate_val").html()==0 || $("#pst_rate_val").html()=="") // not PST provinces
		{      
			$("#chk_PST").prop('disabled', true);
		}   
		else
		{
			$("#chk_PST").prop('disabled', false);
		}  
    }
   
        if($("#is_other_delivery").val()==1)
        {
            $("#chk_other_delivery").prop('checked', true);
        }
        else
        {
            $("#chk_other_delivery").prop('checked', false);
        }
}

function enableAllDiscount(discType)
{    
    $('.discRadio').prop('disabled', (discType==0)?true:false);
    selectDiscRadioButton(discType);
    
    for(i=1; i<=2; i++)
    {       
        var discInput = "#disc_"+i;
        $(discInput).prop('disabled',(i==discType)?false:true);
    }
}

function selectDiscRadioButton(id)
{
    if(id!==0)
        $(".disctype"+id).prop('checked',true);
}

function resetPST(e)
{	 
	orderCalculation();
	
    setPSTCtls(document.getElementById("chk_PST").checked);
    
    this.formDirty=true;		 
}
    
function setPSTCtls(isPST)
{
    
    $("#isPST").val((isPST)?1:0);
    $("#pst_no").prop('disabled', isPST);
    $("#pst_rate_title").css('color', (isPST)?'black':'grey');
    $("#pst_rate_val").css('color', (isPST)?'black':'grey');
    
    
    if(isPST)
    {
        $("#pst_no").val("");
    }
    else
    {
        $("#pst_no").focus();
    }
    
    if($("#pst_no").val()==""&& !isPST)
    {
        $("#btnOK").prop('disabled', true);
    }
    else
    {
     
        $("#btnOK").prop('disabled', false);
    }
}
    
function isEmptyPSTNo(PST_no)
{
    if(PST_no=="")
         $("#btnOK").prop('disabled', true);
    else
        $("#btnOK").prop('disabled', false);
}

function showPSTNOMSG(PST_no)
{   
 //   if(PST_no=="")
      //  alert("Please input PST# or check the PST checkbox.");
}  
    
function tglDiscountCtls(isDiscount)
{              
    enableAllDiscount ((isDiscount)?1:0);
    
    if(!isDiscount)
        $("#discType").val(0);
    
    for(i=1; i<=2; i++)
    {       
        var discInput = "#disc_"+i;
        $(discInput).val("");
    }
    
    if(isDiscount)
    {
        $("#discType").val(1);
        $("#disc_1").focus();
    }
    orderCalculation();
    this.formDirty=true;
}
    
function changeDiscType(discType)
{
    $("#discType").val(discType);
    
    if(discType>0)
    {
        $('#disc_1').prop('disabled', discType==1?false:true);
        $('#disc_2').prop('disabled', discType==2?false:true);
       
      
        $('#disc_1').val("");
        $('#disc_2').val("");
      
        
        if(discType==1)
             $('#disc_1').focus();
        else
            $('#disc_2').focus();
             
        
        orderCalculation();
    }
}

function setCSInventory() // init create cs order page
{
 	tglCSOrderButton(false);
}
	
function showCsOrderForm(order_id)
{	    
    	 parent.gotoPage("main.php?page_name=csOrderEdit&order_id=" + order_id);
}

function saveOrderViewCSInvoice(order_id)
{	
   if (formDirty)
    {
        g_order_id = order_id;
        parent.showMsgBox("Do you want to save your changes?" , parent.MBYESNO + parent.ICONQUESTION, saveHandler);
    }
    else
    {	  

        viewCSOrder(order_id); 			
	}
}
//for view invoice save confirming dialog
function saveHandler(retVal)
{
    if (retVal == parent.IDYES)
    {
        var isPST = $("#isPST").val();
        if(isPST==0)
        {
            
            if($("#pst_no").val()==""||$("#pst_no").val()==null)
            {
                alert("Please input the PST No. Or check PST checkbox.");
            
                return false;
            }
        }
        
        var discType = $("#discType").val();

        if(discType!=0)
        {
            var discValCtl ="#disc_"+discType;
            
            var discVal = $(discValCtl).val();
            
            if(discVal==""||discVal==null)
            {
                alert("Please input the discount. Or uncheck discount checkbox.");
            
                return false;
            }
        }
        
        xajax.doneLoadingFunction  = function(){viewCSOrder(g_order_id,true); g_order_id = null; xajax.doneLoadingFunction  = function(){;};};
        xajax_saveCSOrderForm(xajax.getFormValues('csOrderEdit'));
        formDirty = false;
    }
    else{
   
        viewCSOrder(g_order_id);
    }
}

function updateOldQuantityAfterSaveOrder()
{ 
    var arOrders = getElementsByClass('orderinput', null, 'input');

    var itemSubTotal=0;
    var subTotal =0;
  
    for (i=0; i<arOrders.length; i++)
    {
        var qty = parseInt(arOrders[i].value);
        var oldOrder = arOrders[i].name.replace('CSOrder', 'CSOrder_old');
        
        var oldQty = parseInt(document.getElementById(oldOrder).value);
        document.getElementById(oldOrder).value = qty;     
    }
}

function viewCSOrder(order_id, isSetOld)
{
    if(isSetOld)
        updateOldQuantityAfterSaveOrder();
        
    var ht = screen.availHeight;

    var printWindow = window.open("main.php?page_name=CSOrderView&order_id=" + order_id, "Invoice", "menubar=no,scrollbars=yes,left=0,top=0,height=900, width=700");
 }


function changeCsOrdQty(qtyCtl) {

     var itemVal = qtyCtl.value;
             
     if(itemVal!=""&&itemVal!=null)
     {
        var itemAva = qtyCtl.getAttribute("ava_units");
              
        if(parseFloat(itemVal)>parseFloat(itemAva))
        {
           qtyCtl.value= parseFloat(itemAva);
        }
     }
     else{
        qtyCtl.value=0;
     }
     
     orderCalculation();		
}

function orderCalculation()
{
    var arOrders = getElementsByClass('orderinput', null, 'input');
    var itemSubTotal=0;
    var subTotal =0;
  
    for (i=0; i<arOrders.length; i++)
    {
        var qty = parseInt(arOrders[i].value);
        var itemSubTotalCell = "#"+(i+1)+"_itemSubTotal";
     
        if (qty > 0)
        {
            var unit_price = filterNum(arOrders[i].getAttribute("org_price"));
            
            if((arOrders[i].getAttribute("cs_product_name")=="Winelife")&&qty>=24)
            {
                unit_price = filterNum(arOrders[i].getAttribute("promotion_price"));
            }
         
  //       alert(itemSubTotalCell);
            itemSubTotal = unit_price*qty;
            
            $(itemSubTotalCell).html(formatCurrency(itemSubTotal));
            
             subTotal =  subTotal +itemSubTotal;
        }
        else
        {
             $(itemSubTotalCell).html(formatCurrency(0));
        }
        
    }
    
  	$("#order_subtotal").html(formatCurrency(subTotal));
    
   
    var discType = $("#discType").val();
    var discountTotal = 0;
   
 //  alert (discType);
    if(discType==1)//amout
    {
    
        if($("#disc_1").val()!="" && $("#disc_1").val()!=0)
             discountTotal = subTotal - $("#disc_1").val();
    }
    
    if(discType==2) //rate
    {
       if($("#disc_2").val()!="" && $("#disc_2").val()!=0)
             discountTotal = subTotal - subTotal*$("#disc_2").val()/100;
    }
    

//   alert(discountTotal);
    if(discountTotal>0)
    {
        subTotal = discountTotal;
	    $("#disc_total").html(formatCurrency(subTotal));
    } 
    else
    {
        $("#disc_total").html("");
    }
     
    //Tax
    var pst = 0;
    
    if($("#estate_id").val()!=196)
    {
        
    
    if(document.getElementById("chk_PST").checked)
    {
        pst = $("#pst_rate_val").html();
    }
	 }
     
	var gst = $("#gst_rate").html();
	$("#pst_total").html(formatCurrency(pst*subTotal));   
	$("#gst_total").html(formatCurrency(gst*subTotal));

    var adjust1 = filterNum($("#adjustment_1").val());

	var totalAmount= parseFloat(subTotal)+parseFloat(pst*subTotal)+parseFloat(gst*subTotal)+parseFloat(adjust1);
	$("#total_value").html(formatCurrency(totalAmount));
}

function deleteCSOrder(order_id, order_status_id)
{
    if (canDeleteOrder(order_status_id)) 
    {
        g_order_id = order_id;
        parent.showMsgBox("You are about to delete an order.\n Do you want to proceed?", parent.MBYESNO + parent.ICONQUESTION, deleteCSOrderHandler);
    }
}

function deleteCSOrderHandler(retVal)
{
    if (retVal == parent.IDYES)
    {
        var order_id = g_order_id;
        g_order_id = 0;
        var customer_id = document.getElementById('csorderlistCustomerID').value;
        var order_by = document.getElementById('csorderlistSortBy').value;
        var order_type = document.getElementById('csorderlistSortType').value;
        var order_year = document.getElementById('order_year').value;
        var estate_id =  document.getElementById('cs_products_id').value;
        var province_id =  document.getElementById('province_id').value;
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
   
        showCSOrderlistLoading();
        
        xajax_deleteCSOrder(estate_id,customer_id, order_by, order_type,order_id,  order_year,period,isQut,province_id);
    }
    else
        return true;
}
function resetWLInventory(inventory)
{
    $("#wl_inventory_bottles").html(inventory);
    $("#wl_inventory_cs").html(parseFloat(inventory/12).toFixed(2));
	
}
function changeCSOrderPeriod()
{
	if(document.getElementById("chkCSOdQut").checked) 
	{
		document.getElementById("cs_order_month").disabled=true;
		document.getElementById("cs_order_qut").disabled=false;
		
		setQuarterDesc(1);
	}
	else
	{
		document.getElementById("cs_order_month").disabled=false;
		document.getElementById("cs_order_qut").disabled=true;
		document.getElementById("order_quarter_desc").value = "";
	}
	refreshCSOrders();
}
function refreshCSOrders()
{
    var customer_id = document.getElementById('csorderlistCustomerID').value;
    var estate_id =  document.getElementById('cs_products_id').value;
    var order_by = document.getElementById('csorderlistSortBy').value;
    var order_type = document.getElementById('csorderlistSortType').value;
    var order_year = document.getElementById('cs_order_year').value;
   
    var period ="";
    var isQut =0;
    
    if(document.getElementById("chkCSOdQut").checked)
    {
    	period = document.getElementById('cs_order_qut').value;
    	isQut =1;
    	setQuarterDesc();
    }
    else
    {
     	period = document.getElementById('cs_order_month').value;
    }
   
    showCSOrderlistLoading();
  //($estate_id,$customerID, $orderBy, $orderType,  $order_year,$period, $isQuater)
  
    xajax_refreshCSOrders(estate_id,customer_id, order_by, order_type, order_year,period,isQut);  
						//($customerID, $orderBy, $orderType,  $order_year,$period, $isQuater)
}

function setQuarterDesc()
{
 	var period =0;
 	var qut_desc ="";
 
    period = document.getElementById("cs_order_qut").value;
    
    
    if(period ==1)
          qut_desc="January - March";
    
    if(period ==2)
         qut_desc="April - June";
    
    if(period ==3)
          qut_desc="July - September";
    
    if(period ==4)
          qut_desc="October - December";
    
    document.getElementById("cs_order_quarter_desc").value = qut_desc;
	  
}

//used by orderlist control

function clearCSOrderTable()
{
    var gridTable = document.getElementById('csorderlistTable');
    if (gridTable) gridTable.style.display = 'none';
}

function showCSOrderlistLoading()
{
    clearCSOrderTable();
    var divLoading = document.getElementById('loadingMsgorders');
    if (divLoading)
    {
        divLoading.innerHTML = 'Loading ...';
        divLoading.style.display = 'block';
    }
}

//=====================================================================
function refreshCSProduts4OrderList()
{    
    var estate_id = document.getElementById('cs_products_id').value;
    var province_id = document.getElementById('province_id').value;
    var order_by = document.getElementById('csproductlistOrderBy').value;
    var order_type = document.getElementById('csproductlistOrderType').value;
    
  
    if(estate_id==188)// winelife & Le Verra De Vin
    {
   //     $("#PST_table").hide();
    }
    else
        $("#PST_table").show(); //open up to all products
        
    
    
    xajax.doneLoadingFunction  = function(){;}; //empty function
    
    xajax_refreshCSProList( estate_id,province_id, order_by, order_type);
   
}

function canCreateCSOrder()
{
    var retVal = false;
    if (parseInt(document.getElementById('csproductCount').value) > 0)
    {
        var arOrders = getElementsByClass('csorderinput', null, 'input');
        for (i=0; i<arOrders.length; i++)
        {
            var qty = parseInt(arOrders[i].value);
            if (qty > 0)
            {
                var lblID = arOrders[i].name.replace('CSOrder', 'Avl');
                var avl = parseInt(document.getElementById(lblID).innerText);
                if (qty > avl)
                    arOrders[i].value = avl;
               retVal = true;
            }
        }
    }
    return retVal;
}

function checkPST()
{
    var pst = 0;
    
    var retVal=true;
    if(document.getElementById("chk_PST").checked)
    {
        pst = $("#pst_no").val();
    
    
        if(pst=="")
        {
            alert("Please input PST Exempt number!");
            return false;
        }
    
    }
    
    return true;
    
}
function createCSOrder()
{
  
    if (!canCreateCSOrder())
    {
        alert("You must order at least one unit.");
        tglOrderButton(false);
        return false;
    }
    else
    {
        if(!checkPST())
            return false;
        
        var customer_id = document.getElementById('customer_id').value;
        var estate_id = document.getElementById('cs_products_id').value;
        var province_id = document.getElementById('province_id').value;
         var other_info ="";// document.getElementById('other_info').value;
       
        
        var pst_no = 0;
        if(document.getElementById("chk_PST").checked)
             pst_no = document.getElementById('pst_no').value;
        
    
        if (parseInt(document.getElementById('csproductCount').value) > 0)
        {
            var arOrders = getElementsByClass('csorderinput', null, 'input');
            
            var arProductInfos= new Array();
            var csproduct_id ="";
            var qty =0;
            for (i=0; i<arOrders.length; i++)
            {
                qty = parseInt(arOrders[i].value);
                if (qty > 0)
                {
                    csproduct_id = arOrders[i].getAttribute("cs_id_info");
                    
                    arProductInfos[i] = new Array();
                    arProductInfos[i]["cs_product_id"]=csproduct_id;
                    arProductInfos[i]["quantity"]=qty;
                }
            }
        }
        //      showWinelistLoading();
	
        xajax_createCSOrder(province_id,customer_id,estate_id,pst_no,other_info,arProductInfos);
        return true;
    }
}



function tglCSOrderButton(bEnable)
{
    var btn = document.getElementById('butCSOrd');
    if (btn)
        btn.disabled = !bEnable;
}

function csOrderKeyPress(ctl,event)
{
    var allow = chkMaskINTEGER(this, event);
    if (allow) tglCSOrderButton(true);
    return allow;
}



