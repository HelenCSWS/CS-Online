/*
This function is only for international wines
*/
	function loadWine()
	{
	

	 	var i=0;
	 	var pros=2;
	 	var suffix=""
	 	var delName="btnDel";
	 	var newName="new";
	 	var chkName="chk"
	 	var cspc_codeName ="cspc_code";
	 	var is_international=document.getElementById("is_international").value;
               /* document.getElementById("chkIncludeInStorePenReport").className="";*/
                
                
       
	 	if(parseInt(document.getElementById("is_international").value)==1)
	 	{
			document.getElementById("tdCaWine").style.display="none";
		//	document.getElementById("tdCost").style.display="block";
		//	document.getElementById("tdProfit").style.display="inline-block";
			document.getElementById("tdDelivery").style.display="none";
		}
		else
        {
	//		document.getElementById("tdCaWine").style.display="block";	
			document.getElementById("tdCost").style.display="none";
			document.getElementById("tdProfit").style.display="none";
		//	document.getElementById("tdDelivery").style.display="block";
			Calendar.setup( {
                inputField:"delivery_date", ifFormat:"%m/%d/%Y", button:"delivery_date_calendar", singleClick:true, align:"Bl", cache:true, showOthers:true, onClose: dateCalendarClose
            }
             );
		}
		
	 	if(document.getElementById("editMode").value==0||document.getElementById("editMode").value=="")   
	 	{
	 	 	for (i=1;i<=pros;i++)
	 	   {
	 	    	delName ="btnDel";
	 	    	newName="new";
	 	    	chkName="chk"
				if(i==1)
				{
					suffix="_bc";
				}
				if(i==2)
				{
					suffix="_ab";
				}
				if(i==3)
				{
					suffix="_mb";
				}
				
				
				delName =delName+suffix;
				newName =newName+suffix;
				chkName =chkName+suffix;
		
				if(document.getElementById(newName).value!=0)
				{
			
					document.getElementById(chkName).checked = true;
					document.getElementById(chkName).disabled =true;
				
				}
				else
				{
				 
					disableWinesCtls(i,true,false);
				}
			//hide delete button for add feature	  
				document.getElementById(delName).style.display="none";
			
			}//end for
			
		   	document.getElementById("btnDeleteWine").style.display="none";
		   	
		   	
		}
		else
		{
		 
			for (i=1;i<=pros;i++)
	 	   {
	 	    	delName ="btnDel";
	 	    	newName="new";
	 	    	chkName="chk"
	 	    	cspc_codeName ="cspc_code";
				if(i==1)
				{
					suffix="_bc";
				}
				if(i==2)
				{
					suffix="_ab";
				}
				if(i==3)
				{
					suffix="_mb";
				}
				
				
				delName =delName+suffix;
				newName =newName+suffix;
				chkName =chkName+suffix;
				cspc_codeName = cspc_codeName+suffix;
			
				//check check box if wine for pro is available
				
				document.getElementById(newName).value==2
				
		
				
				if(document.getElementById(cspc_codeName).value!="")
				{
				 
					document.getElementById(chkName).checked = true;
					document.getElementById(chkName).disabled =true;
					

					if(document.getElementById("is_international").value==0)					
					{
						document.getElementById("total_cases").disabled=true;
						document.getElementById("total_bottles").disabled=true;
						document.getElementById("delivery_date").disabled=true;
						setDisableColor("delivery_date",true,0);
						setDisableColor("total_cases",true,0);
						setDisableColor("total_bottles",true,0);
					}
					
					
					document.getElementById(delName).style.display="block";
				}
				else
				{
				 	disableWinesCtls(i,true,false);
				 	document.getElementById(delName).style.display="none";
				}
				
			}//end for
			
		   	document.getElementById("btnDeleteWine").style.display="none";
		}//end if
	//	else
		document.getElementById("wine_name").focus();
	}

	
   function getSuffix(proid)
   {	var i= proid;
		if(i==1)
			{
				suffix="_bc";
			}
			if(i==2)
			{
				suffix="_ab";
			}
			if(i==3)
			{
				suffix="_mb";
			}
			
			return suffix;
	}
	function setDisableColor(ctlName,isDisabled,isStar)
	{
	 
	 	prefix ="sp_";
	 	var starName = prefix+ctlName;
	 
		if(isDisabled)//disabled
		{
			document.getElementById(ctlName).style.borderColor ="#A9A9A9";
			if(isStar==1)
			{
				document.getElementById(starName).style.color="#A9A9A9";
			}
        
		}
		else//enabled
		{
			document.getElementById(ctlName).style.borderColor ="#7F9DB9";
			if(isStar==1)
			{
				document.getElementById(starName).style.color="#FF0000";
			}
		}
	}
	function disableCtls4Delete(proid)
	{
	
		disableWinesCtls(proid,true,true);	
		var chkName = "chk"+getSuffix(proid); 
		var newName = "new"+getSuffix(proid); 
		document.getElementById(chkName).checked =false;
		document.getElementById(chkName).disabled =false;
		document.getElementById(newName).value =0;
	}
	
   function disableWinesCtls(i,isDisabled, isStopEvent)
   {

	 	var pros=2;
	 	var suffix=""
	 	var delName="btnDel";
	 	var newName="new";
	 	var chkName="chk"
	 	
	 	var firstIndex=1;

 	    	var cspc = "cspc_code";
 	    	var display_price = "display_price";
 	    	var price_winery = "wholesale";
 	    	var profit = "profit";
 	    	var cost = "cost";
 	    	var case_sold = "case_sold";
 	    	var case_value = "case_value";
 	    	
 	    	var total_cases="total_cases";
 	    	var total_bottles ="total_bottles";
 	    	var delivery_date ="delivery_date";
 	    	
 	    	
			suffix = getSuffix(i);
		
			
			display_price = display_price+suffix;
			price_winery = price_winery+suffix;
			profit = profit+suffix;
			cost = cost+suffix;
			cspc = cspc+suffix;
			case_sold = case_sold+suffix;
			case_value = case_value+suffix;
			
		
			document.getElementById(cspc).disabled=isDisabled;
			document.getElementById(display_price).disabled=isDisabled;
			document.getElementById(price_winery).disabled=isDisabled;
			document.getElementById(profit).disabled=true;
			document.getElementById(cost).disabled=isDisabled;
			document.getElementById(delivery_date).disabled=isDisabled;
			document.getElementById(case_sold).disabled=isDisabled;
			document.getElementById(case_value).disabled=isDisabled;
		
			if(parseInt(document.getElementById("is_international").value)==0&&i==1) //bc wine
			{
			
				if(document.getElementById("wine_id").value!="")	
				{
				 
					document.getElementById(case_sold).disabled=isDisabled;
					document.getElementById(case_value).disabled=isDisabled;
					document.getElementById(total_cases).disabled=isDisabled;
					if(isStopEvent)
					{
						document.getElementById(total_cases).value="";
						document.getElementById(total_bottles).value="";
					 }
					setDisableColor(total_cases,isDisabled,0);
					setDisableColor(delivery_date,isDisabled,0);
					setDisableColor(total_bottles,true,0);
				
				
				}
				else
				{
					document.getElementById(total_cases).disabled=isDisabled;
					document.getElementById(total_bottles).disabled=true;
					document.getElementById(delivery_date).disabled=isDisabled;
					
					if(isStopEvent)
					{
						document.getElementById(total_cases).value="";
						document.getElementById(total_bottles).value="";
					 }
					setDisableColor(total_cases,isDisabled,0);
					setDisableColor(delivery_date,isDisabled,0);
					setDisableColor(total_bottles,true,0);
					
				}
			}
			
			document.getElementById(cspc).value="";
			document.getElementById(display_price).value="";
			document.getElementById(price_winery).value="";
			document.getElementById(profit).value="";
			document.getElementById(cost).value="";
			document.getElementById(case_sold).value="1";
			document.getElementById(case_value).value="1";
            if (suffix == "_bc")
            {
              /*  var chkPenReport = document.getElementById("chkIncludeInStorePenReport");
                chkPenReport.disabled=isDisabled;
                chkPenReport.checked=false;<br />*/
            }
			
			setDisableColor(cspc,isDisabled,1);
			setDisableColor(display_price,isDisabled,1);
			setDisableColor(price_winery,isDisabled,1);
			setDisableColor(profit,true,1);
			setDisableColor(cost,isDisabled,1);
			setDisableColor(case_sold,isDisabled,0);
			setDisableColor(case_value,isDisabled,0);
			
			document.getElementById(total_bottles).disabled=true;
			
		//	setDisableColor(cspc,isDisabled);
			
			
			if(isDisabled==false)
			{
			 
				document.getElementById(cspc).focus();
			}
        
        if(isStopEvent)
        {
			stopEvt();
		    return false;
		  }

	}
	function delWine(proid)
	{

	 	var wine_id = document.getElementById("wine_id").value;
	 	var estate_id = document.getElementById("estate_id").value;
	 	var newName = "new"+getSuffix(proid);
	 	var is_international=document.getElementById("is_international").value;
	 	
		document.getElementById(newName).value=0;
	 	if(document.getElementById("new_bc").value==0 && document.getElementById("new_ab").value==0)
	 	{
	 	 
	 		document.getElementById("btnAdd").disabled=true;
	 		document.getElementById("btnCancel").disabled=true;
	 	}	 
		xajax_deleteWineByProId(is_international,wine_id,proid,estate_id);

	 	stopEvt();
	   return false;
	}
	
	function checkPro(proid)
	{
		var newName = "new"+getSuffix(proid);
		var chkName = "chk"+getSuffix(proid);
		if(document.getElementById(newName).value!=2)
		{
			if(document.getElementById(chkName).checked ==true)
			{
			 //alert("checked");
				disableWinesCtls(proid,false); //enabled contorls
				document.getElementById(newName).value = 1;
				document.getElementById("btnAdd").disabled=false;
				document.getElementById("btnCancel").disabled=false;
				
			}
			else
			{
			 //alert("unchecked");
			 document.getElementById(newName).value = 0;
				disableWinesCtls(proid,true); 
			}
		}
		else
		{
			alert("You can uncheck the check box, but you can delete it.")
		}
	}

function setPrice4Pro(element,priceid,pro_id)
{
 	
 	
 	var suffix = "";
 	if(pro_id==1)
 		suffix = "_bc";
 	else if(pro_id==2)
 		suffix = "_ab";
 		
	if( priceid==4)
	{
	
	}
	var price="";
	price =element.value.replace("$","");
	var idname = "price_per_unit"+suffix; //priceid=0
	if (priceid==1)
	   idname = "price_winery"+suffix;   
	else if (priceid==2)
	   idname = "profit_per_unit"+suffix;
	else if (priceid==3)
	   idname = "cost_per_unit"+suffix;
	else if (priceid==4)
	{
	   idname="bonus";
	}
	
	document.getElementById(idname).value=price;
	format2Currency(element);
	
	var is_inter= document.getElementById("is_international").value;

	if(is_inter==1 && priceid!=0)//internatinal wine, need calculate the profit
	{

	 	var costCtl = "cost_per_unit"+suffix;
	 	var priceVCtl= "price_winery"+suffix;
	 	var prfitPerCtl= "profit_per_unit"+suffix;

		var cost = document.getElementById(costCtl).value;
		var net_profit = document.getElementById(priceVCtl).value;
		
		var profit = (net_profit-cost)*0.45;
		profit = (roundNumber(profit,2)); 
		
		var profitCtl = "profit"+suffix;
		document.getElementById(prfitPerCtl).value = profit;
	
		document.getElementById(profitCtl).value = profit;
		//alert(profit);
	}

	if(pro_id==2&&is_inter==0)
	{
			var priceVCtl= "price_winery"+suffix;
		 	var prfitPerCtl= "profit_per_unit"+suffix;
		 	var profitCtl = "profit"+suffix;
		 	var profit =0;
		 	var cspc_code = document.getElementById("cspc_code_ab").value;
		 	
		 	var net_profit = document.getElementById(priceVCtl).value;
			if( document.getElementById("is_international").value==0&&document.getElementById("estate_id").value!=1 )
			{
			 	profit = (net_profit)*0.1;
				
			}
			else if( document.getElementById("estate_id").value==1 )
			{
				if(parseInt(cspc_code)==892158||parseInt(cspc_code)==34)
				{
					profit = (net_profit)*0.125;
				}
				else
				{
						profit = (net_profit)*0.15;	
				}
			}
			profit = (roundNumber(profit,2)); 
			document.getElementById(prfitPerCtl).value = profit;
			document.getElementById(profitCtl).value = profit;
	}
}

function setCaBottles()
{
		if(parseInt(document.getElementById("is_international").value)==0)
	 	{
	 	 	if(document.getElementById("wine_id").value==""||document.getElementById("new_bc").value==1)
	 	 	{
				if(document.getElementById("total_cases").value!="" && document.getElementById("bottles_per_case").value!="")
				{
					document.getElementById("total_bottles").value = document.getElementById("total_cases").value*document.getElementById("bottles_per_case").value;
				}
				else
				{
					document.getElementById("total_bottles").value ="";
				}
			}
	 	}
}



function product4OtherProvince(isWine)
{        
    
    var product_id="";
    if(isWine)
        product_id = document.getElementById("wine_id").value;
    else
        product_id = document.getElementById("beer_id").value;

    
	var estate_id = document.getElementById("estate_id").value;
	var is_international = document.getElementById("is_international").value;
	var pageid = document.getElementById("pageid").value;
    
    var login_user_id =$("#login_user_id").val();

    //	if(pageid==45 ||pageid==46 ||pageid==50||pageid==56) //beer, sake, spirits
    var product_type_id =1; // wine
    switch(pageid){
        case 25: // wine
            product_type_id=1;
            break;
        case 46: //beer
            product_type_id=2;
            break;
        case 50: //spirits
            product_type_id=3;
            break;
        case 45: //sake
            product_type_id=4;
            break;
    }
    //F60SetCookie (name,value,nDays ) 
      
    
    F60SetCookie ("cs_user_id",login_user_id,1 );
    F60SetCookie ("cs_estate_id",estate_id,1 );   
    F60SetCookie ("cs_product_id",product_id,1 );
    F60SetCookie ("product_type_id",product_type_id,2 );
    //var location ="http://csonline.christopherstewart.com:8008/Products/Wine";
    var location ="http://localhost:8999/Products/Wine";

    window.top.location.href = location; 
}

































