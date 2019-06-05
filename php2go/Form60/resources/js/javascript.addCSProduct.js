function initCSProductPage()
{
    
  //  alert(accounting.formatMoney(12345678));
    
   $("#product_name").focus();
   
   if ($("#isAdmin").val()==0)
   {
       $("#province_id").hide();
       $("#div_inventory").hide();
       
   }
   else
   {
        $("#div_inventory").show();
        var total_units = $("#total_units").val();
        updateInventoryInfo(total_units);   
   }
}

function setFormChange(isBasicInfo)
{
      
    if(isBasicInfo==0)
    {
        $("#basicChanged").val(1);
    } 
    else
    {
        $("#infoChanged").val(1);            
    }                
    
}

function setPrice(element,isCurrency)
{
  /*  if(isCurrency==1)
	 {  
	   alert(element.value);
       alert(accounting.formatMoney(3.2495));
	   accounting.formatMoney(element.value);
     }
    else
        element.value = element.value.replace("$","");*/
}

function changeProductProvince(province_id)
{
    var slink = "main.php?page_name=csProductAdd&editMode=1&cs_product_id="+$("#product_id").val()+"&estate_id="+$("#estate_id").val()+"&province_id="+$("#province_id").val()+"&pageid=56"
    document.location = slink;

    stopEvt();
    return false;
}

function updateInventoryInfo(total_units)
{
   var  total_cases= total_units/$("#bottles_per_case").val();
   
   $("#sp_total_units").html(total_units);
   $("#sp_total_cases").html(parseInt(total_cases));
   $("#inventory").val(0);

}

function setInventory(units)
{

      var total_units =units;
       $("#total_units").val(units);
       updateInventoryInfo(units);
   
   
   alert("Inventory has been updated!");
  
}

function addProductInventory()
{
    var inventory = $("#inventory").val();
    
    var province_id = $("#province_id").val();

    var cs_product_id = $("#product_id").val();
   
    if(inventory==0 || inventory=="")
        alert("Please valid inventory.");
    else
        
    {
        xajax_fillCSInventory(cs_product_id,province_id,inventory);
    }
        
        
        
   
    return false;
  
}

function setAddAnother()
{
    alert("set");
    $("#isAddNew").val(1);
}