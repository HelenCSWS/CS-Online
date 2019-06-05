//==============================================================================
//Comman fucntion
//==============================================================================
function trim(stringToTrim) {
	return stringToTrim.replace(/^\s+|\s+$/g,"");
}
function ltrim(stringToTrim) {
	return stringToTrim.replace(/^\s+/,"");
}
function rtrim(stringToTrim) {
	return stringToTrim.replace(/\s+$/,"");
}


function roundNumber(rnum,rlength) {
	//var numberField = document.roundform.numberfield; // Field where the number appears
	//var rnum = numberField.value;
//	var rlength = 2; // The number of decimal places to round to
var newnumber =0;
	if (rnum > 8191 && rnum < 10485) {
		rnum = rnum-5000;
		 newnumber = Math.round(rnum*Math.pow(10,rlength))/Math.pow(10,rlength);
		newnumber = newnumber+5000;
	} else 
	{
		 newnumber = Math.round(rnum*Math.pow(10,rlength))/Math.pow(10,rlength);
	}
//	numberField.value = newnumber;


return newnumber;
}


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

function format2Currency(price_element)
{
		var prefix="$";
		var wd;
		var tempnum=price_element.value;
		if (tempnum != "")
		{
			tempnum = tempnum.replace(".00","");
			
			if (price_element.value.charAt(0)=="$")
				tempnum = tempnum.replace("$","");
			wd="w";
			
			for (i=0;i<tempnum.length;i++)
			{
				if (tempnum.charAt(i)==".")
				{
					wd="d";
					break;
				}
			}
		
			if (wd=="w")
				price_element.value=prefix+tempnum+".00";
			else
			{
				if (tempnum.charAt(tempnum.length-2)==".")
				{
				// var test =prefix+tempnum+"0";
					price_element.value=prefix+tempnum+"0";
				}
				else
				{
					tempnum=Math.round(tempnum*100)/100;
					price_element.value=prefix+tempnum;
				}
			}
			var temValue =price_element.value;
			var grpValue =temValue.split(".");
			if (grpValue[1].length==1)
			{
				temValue = temValue+"0";
			}
			price_element.value =temValue;
	}

}

//==============================================================================
//End of common funcion
//==============================================================================




//------------------------------------------------------------------------------
// Add estate page : best connect phone type id :
// 1 : business number
// 2 : cell number
// 3 : fax number
//------------------------------------------------------------------------------
function setCommissionFormat(element, typeid)
{
    var edtNTypeName = "cntype_"+typeid;

    if (document.getElementById(edtNTypeName).value ==1 )
        format2Currency(element);

    if (element.value =="0." )
        element.value=0;
}
function changePaymentInfo(keyValue)
{
   var DEFAULT_PAYMENT_INFO_1="Please call with credit card number, or make cheque payable to ";
	var DEFAULT_PAYMENT_INFO_3=" Unit #2139 -11871 Horseshoe Way Richmond, B.C. V7A 5H5 Telephone 604.274.8481";
	document.getElementById("payment_info").value=DEFAULT_PAYMENT_INFO_1 +keyValue + DEFAULT_PAYMENT_INFO_3;
}

function selectBest(typeid)
{
    document.getElementById("lkup_phone_type_id").value=typeid;

    if (typeid==1)
    {
        document.getElementById("phone_office1").focus();
    }
    if(typeid==2)
    {
        document.getElementById("phone_other1").focus();
    }
    if(typeid==3)
    {
        document.getElementById("phone_fax").focus();
    }

}

function changeNumberType(controlID,ntype_id)
{
	var edtNTypeName = "cntype_"+controlID;
	var edtCtypeName="ctype_"+controlID;
	document.getElementById(edtNTypeName).value=ntype_id;
	document.getElementById(edtCtypeName).value ="";
	document.getElementById(edtCtypeName).focus();
}
//------------------------------------------------------------------------------
// Add customers page : store priority enabled until store type is BCLDB
//------------------------------------------------------------------------------
function changeTab(id)
{
    

    var province_id = document.getElementById("province_id").value;

    var tab0 = document.getElementById("tab0");
    var tab1 = document.getElementById("tab1");
    var tab2= document.getElementById("tab2");
    var tab3 = document.getElementById("tab3");
    var tab4 = document.getElementById("tab4");
    var tab5 = document.getElementById("tab5");
    

    tab0.className = "tab";
    tab1.className = "tab";
    tab4.className = "tab";
    tab3.className = "tab";
    tab5.className = "tab";
    tab2.className = "tabRight";
   

	if(province_id>1)    
	{
		tab3.style.display="none";
		tab2.style.display="none";
		if(province_id!=2)
		{
			tab4.style.display="none";
		
		
			tab0.width="33%";
			tab1.width="33%";
			tab5.width="34%";
		}
		else
		{
			tab0.width="25%";
			tab1.width="25%";
			tab4.width="25%";
			tab5.width="25%";
		}
		//tab4.width="25%";
		document.getElementById("trSelectEstate").style.display="none";
	}

    if (id==0)//store
    { 
        document.getElementById("tab_store").style.display="block";
        document.getElementById("tab_contact").style.display="none";
        
        if(province_id ==1)      
            document.getElementById("tab_note").style.display="none";
        else
            document.getElementById("tab_note").style.display="block";
            
        document.getElementById("tab_orders").style.display="none";
        document.getElementById("tab_sales").style.display="none";
        document.getElementById("tab_pro_order").style.display="none";
        document.getElementById("trCreateCSOrder").style.display="none";
        document.getElementById("trSelectEstate").style.display="block";
        
        tab0.className = "tabActive";
        document.getElementById("customer_name").focus();
    }
    else if(id==1)//contact
    {
        document.getElementById("tab_store").style.display="none";
        document.getElementById("tab_contact").style.display="block";
        if(province_id ==1)      
      		document.getElementById("tab_note").style.display="none";
        else
      		document.getElementById("tab_note").style.display="block";
        document.getElementById("tab_orders").style.display="none";
        document.getElementById("tab_sales").style.display="none";
        if(province_id ==1) 
	        document.getElementById("trSelectEstate").style.display="block";
        document.getElementById("tab_pro_order").style.display="none";
        document.getElementById("trCreateCSOrder").style.display="none";
        tab1.className = "tabActive";
    }
    else if(id==2)//note
    {
        document.getElementById("tab_store").style.display="none";
        document.getElementById("tab_contact").style.display="none";
        document.getElementById("tab_note").style.display="block";
        document.getElementById("tab_orders").style.display="none";
        document.getElementById("tab_sales").style.display="none";
        document.getElementById("tab_pro_order").style.display="none";
        document.getElementById("trCreateCSOrder").style.display="none";
        document.getElementById("trSelectEstate").style.display="block";
        tab2.className = "tabActive";
        if (window.setNoteHeight) setNoteHeight();
    }
    else if(id==3)//orders
    {
        document.getElementById("tab_store").style.display="none";
        document.getElementById("tab_contact").style.display="none";
        document.getElementById("tab_note").style.display="none";
        document.getElementById("tab_sales").style.display="none";
        document.getElementById("tab_orders").style.display="block";
        document.getElementById("tab_pro_order").style.display="none";
        document.getElementById("trCreateCSOrder").style.display="none";
        document.getElementById("trSelectEstate").style.display="block";
        tab3.className = "tabActive";
    
        if (window.setorderListHeight) 
        {
            setorderListHeight();
        }
	   
    }
   else if(id==4)//sales
    {
        document.getElementById("tab_store").style.display="none";
        document.getElementById("tab_contact").style.display="none";
        document.getElementById("tab_note").style.display="none";
        document.getElementById("tab_orders").style.display="none";
        document.getElementById("tab_sales").style.display="block";
        document.getElementById("tab_pro_order").style.display="none";
        document.getElementById("trCreateCSOrder").style.display="none";
        document.getElementById("trSelectEstate").style.display="none";
	    tab4.className = "tabActive";
        if (window.setSalesListHeight) 
			setSalesListHeight();
		
		if(document.getElementById("isload").value=="0") // load first time
		{		
		 	refreshSalesList();
			document.getElementById("isload").value ="1";		
		}
    }
     else if(id==5)//CS Product order
    {
        document.getElementById("tab_store").style.display="none";
        document.getElementById("tab_contact").style.display="none";
        document.getElementById("tab_note").style.display="none";
        document.getElementById("tab_pro_order").style.display="block";
        document.getElementById("trCreateCSOrder").style.display="flex";
        document.getElementById("tab_orders").style.display="none";
        document.getElementById("tab_sales").style.display="none";
        document.getElementById("trSelectEstate").style.display="none";
	    tab5.className = "tabActive";
	    
	 
     	if (window.setCSOrderListHeight)
		{
		   setCSOrderListHeight();
		}
     	
    }

  if(id!==5){
    if (window.setWineListHeight) 
	 	setWineListHeight();
         }
   else if(id==5)
    {
    if (window.setCSProductsListHeight) 
	 	setCSProductsListHeight();
         }

}

function changestoretype(store_id)
{
	if (store_id == 6 )
	{
	//	document.getElementById("lkup_store_priority_id").disabled=false;
		document.getElementById("tdShowMark").style.display="block";
		document.getElementById("tdNoMark").style.display="none";
		document.getElementById("userBCLDB").style.display="block";
		document.getElementById("userOther").style.display="none";
	}
	else
	{
	//	document.getElementById("lkup_store_priority_id").disabled=true;
	//	document.getElementById("lkup_store_priority_id").value="";
		document.getElementById("tdShowMark").style.display="none";
		document.getElementById("tdNoMark").style.display="block";
		document.getElementById("userBCLDB").style.display="none";
		document.getElementById("userOther").style.display="block";
	}
}

function changePaymentType(keyvalue)
{
    if (keyvalue > 2 )
    {
        document.getElementById("cc_number").disabled=false;
        document.getElementById("cc_exp_month").disabled=false;
        document.getElementById("cc_exp_year").disabled=false;
        document.getElementById("cc_digit_code").disabled=false;

        document.getElementById("cc_number").style.borderColor ="#7F9DB9";
        document.getElementById("cc_exp_month").style.borderColor ="#7F9DB9";
        document.getElementById("cc_exp_year").style.borderColor ="#7F9DB9";
        document.getElementById("cc_digit_code").style.borderColor ="#7F9DB9";

        document.getElementById("noCCno").style.display="none";
        document.getElementById("showCCno").style.display="block";

        document.getElementById("noCCexp").style.display="none";
        document.getElementById("showCCexp").style.display="block";
        document.getElementById("cc_number").focus();

    }
    else
    {
        document.getElementById("cc_number").disabled=true;
        document.getElementById("cc_exp_month").disabled=true;
        document.getElementById("cc_exp_year").disabled=true;
        document.getElementById("cc_digit_code").disabled=true;

        document.getElementById("cc_number").style.borderColor ="#A9A9A9";
        document.getElementById("cc_exp_month").style.borderColor ="#A9A9A9";
        document.getElementById("cc_exp_year").style.borderColor ="#A9A9A9";
        document.getElementById("cc_digit_code").style.borderColor ="#A9A9A9";

        document.getElementById("cc_number").value ="";
        document.getElementById("cc_exp_month").value ="";
        document.getElementById("cc_exp_year").value ="";
        document.getElementById("cc_digit_code").value ="";

        document.getElementById("noCCno").style.display="block";
        document.getElementById("showCCno").style.display="none";

        document.getElementById("noCCexp").style.display="block";
        document.getElementById("showCCexp").style.display="none";

    }
}


//------------------------------------------------------------------------------
/*
    upper case all letters in postal code input box when use press key
*/
//------------------------------------------------------------------------------
function upperCaseLetters(keyValue)
{
    document.getElementById("billing_address_postalcode").value=keyValue.toUpperCase();
}

//------------------------------------------------------------------------------
// Add wine page : count total bottles by total cases and bottles/case
//------------------------------------------------------------------------------
function setDateFormat(element,ndate)
{
    var grpdates = ndate.split("/");
    element.value =grpdates[1]+ "/" + grpdates[0] + "/" +grpdates[2];
}
function getBottles()

{
    var nbtlpercase;
    var ncases;


     if (document.getElementById("bottles_per_case").value!="" && document.getElementById("total_cases").value!="")
     {
         var totalbts =document.getElementById("total_bottles").value;
         nbtlpercase =document.getElementById("bottles_per_case").value;
         ncases =document.getElementById("total_cases").value;
          if (document.getElementById("editMode").value != 1)
            document.getElementById("show_total_bottles").value=parseInt(nbtlpercase*ncases);

     }

}
function setAbPrice(element,priceid)
{
	
	var price="";
	price =element.value.replace("$","");
	var idname = "ab_price_per_unit"; //priceid=0
	if (priceid==1)
	   idname = "ab_price_winery";
	   
	   
	if (priceid==2)
	   idname = "ab_profit_per_unit";
	else if (priceid==3)
	   idname = "ab_cost_per_unit";
	else if (priceid==4)
	{
	   idname="bonus";
	}
	
	document.getElementById(idname).value=price;
	format2Currency(element);
	
	var is_inter= document.getElementById("is_international").value;
	if(is_inter==1 && priceid!=0)//internatinal wine, need calculate the profit
	{
		var cost = document.getElementById("ab_cost_per_unit").value;
		var net_profit = document.getElementById("ab_price_winery").value;
		
		var profit = (net_profit-cost)*0.45;
		profit = (roundNumber(profit,2)); 
		
		document.getElementById("ab_profit_per_unit").value = profit;
		document.getElementById("ab_profit").value = profit;
		
	}
	
    
}

function setPrice(element,priceid)
{

 	var pro_id = F60GetCookie("F60_PROVINCE_ID");
  	var suffix = "";
 	if(pro_id==1)
 		suffix = "_bc";
		
	if( priceid==4)
	{
		var bonus=element.value;
		bonus=bonus.replace("$","");
		bonus =parseFloat(bonus);
		if(bonus=="" ||bonus==0 )
		{
			alert("Bonus can't be empty!");
			element.focus();
		}
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
		
	}
}

/*
set some control to disable color for add new delivery
*/
function extDisable(editMode)
{
  if (editMode == 1 )
  {

  }
   document.getElementById("wine_name").style.borderColor="#C0C0C0";
   document.getElementById("cspc_code").style.borderColor="#C0C0C0";
   document.getElementById("vintage").style.borderColor="#C0C0C0";
   document.getElementById("price").style.borderColor="#C0C0C0";

   document.getElementById("wine_name").style.color="gray";
   document.getElementById("cspc_code").style.color="gray";
   document.getElementById("vintage").style.color="gray";
   document.getElementById("price").style.color="gray";
}

//!----------------------------------------------------------------
// @function	chkFLOAT
// @desc		Aplica máscara de número decimal automaticamente
// @param		field Field object		Campo de um formulário
// @param		event Event object		Evento do teclado
// @param		llen Integer			Tamanho da parte inteira do número
// @param		rlen Integer			Tamanho da parte decimal do número
// @return		Boolean
//!----------------------------------------------------------------
//chkFLOAT
function chkffff(f,e,l,r)
{
    chkeFLOAT(f,e,l,r);
}
function chkFLOAT(f,e,l,r) {
 //	return true;

	var keys = '0123456789.';
	var acts = '0,8,13';
	var code = (document.all ? window.event.keyCode : e.which);
	var key = String.fromCharCode(code);
	if (key == ',') key == '.';
	var len = f.value.length;
	var dpos = f.value.indexOf('.');
	var ss = getEditCaretPos(f);
	var se = getSelectionEnd(f);
	var nr = (keys.indexOf(key) != -1 && key != '.' && key != ',');
	var dot = (key == '.');
	if (keys.indexOf(key) == -1 && acts.indexOf(code) == -1) {
		return false;
	} else if (nr) {
		if (key == '0' && ss == 0 && len == 0) {
			f.value = '0.';
			setEditCaretPos(f,2);
			stopEvent(e);
			return true;
		} else if (l != null && dpos == -1 && len == l) {
			if (ss == se && ss < len) {
				f.value = f.value.substring(0,ss)+key+f.value.substring(ss,l-1)+'.'+f.value.substring(l-1);
				setEditCaretPos(f,ss+1);
				stopEvent(e);
				return true;
			} else if (ss < se) {
				f.value = f.value.substring(0,ss)+key+f.value.substring(se);
				setEditCaretPos(f,ss+1);
				stopEvent(e);
				return true;
			} else {
				f.value = f.value+'.';
				return true;
			}
		} else if (l != null && dpos != -1 && f.value.substring(0,dpos).length == l) {
			if (ss == se) {
				if (ss <= dpos) return false;
				if (r != null && f.value.substring(dpos+1).length == r) return false;
				if (ss < len) {
					f.value = f.value.substring(0,ss)+key+f.value.substring(ss);
					setEditCaretPos(f,ss+1);
					stopEvent(e);
				}
				return true;
			} else {
				if (se <= dpos || ss > dpos || (ss+(len-se) < l)) {
					f.value = f.value.substring(0,ss)+key+f.value.substring(se);
					setEditCaretPos(f,ss+1);
					stopEvent(e);
					return true;
				}
				return false;
			}
		} else if (r != null && dpos != -1 && f.value.substring(dpos+1).length == r) {
			if (ss == se) {
				if (ss > dpos) return false;
				if (l != null && f.value.substring(0,dpos).length == l) return false;
				f.value = f.value.substring(0,ss)+key+f.value.substring(ss);
				setEditCaretPos(f,ss+1);
				stopEvent(e);
				return true;
			} else {
				if (se <= dpos || ss > dpos || (ss+(len-se) < l)) {
					f.value = f.value.substring(0,ss)+key+f.value.substring(se);
					setEditCaretPos(f,ss+1);
					stopEvent(e);
					return true;
				}
				return false;
			}
		} else return true;
	} else if (dot) {
		if (ss == 0 && (dpos == -1 || (dpos >= ss && dpos < se))) {
			f.value = (se > ss ? '0.'+f.value.substring(se) : '0.'+f.value);
			setEditCaretPos(f,2);
			stopEvent(e);
			return true;
		} else {
			return ((dpos == -1 && (l == null || ss <= l)) || (dpos >= ss && dpos < se));
		}
	} else {
		return true;
	}
}
function chkFLOAT4B(f,e,l,r) {
 //	return true;

	var keys = '0123456789.';
	var acts = '0,8,13';
	var code = (document.all ? window.event.keyCode : e.which);
	var key = String.fromCharCode(code);
	if (key == ',') key == '.';
	var len = f.value.length;
	var dpos = f.value.indexOf('.');
	var ss = getEditCaretPos(f);
	var se = getSelectionEnd(f);
	var nr = (keys.indexOf(key) != -1 && key != '.' && key != ',');
	var dot = (key == '.');
	if (keys.indexOf(key) == -1 && acts.indexOf(code) == -1) {
		return false;
	} else if (nr) {
		if (key == '0' && ss == 0 && len == 0) {
			f.value = '0.';
			setEditCaretPos(f,2);
			stopEvent(e);
			return true;
		} else if (l != null && dpos == -1 && len == l) {
			if (ss == se && ss < len) {
				f.value = f.value.substring(0,ss)+key+f.value.substring(ss,l-1)+'.'+f.value.substring(l-1);
				setEditCaretPos(f,ss+1);
				stopEvent(e);
				return true;
			} else if (ss < se) {
				f.value = f.value.substring(0,ss)+key+f.value.substring(se);
				setEditCaretPos(f,ss+1);
				stopEvent(e);
				return true;
			} else {
				f.value = f.value+'.';
				return true;
			}
		} else if (l != null && dpos != -1 && f.value.substring(0,dpos).length == l) {
			if (ss == se) {
				if (ss <= dpos) return false;
				if (r != null && f.value.substring(dpos+1).length == r) return false;
				if (ss < len) {
					f.value = f.value.substring(0,ss)+key+f.value.substring(ss);
					setEditCaretPos(f,ss+1);
					stopEvent(e);
				}
				return true;
			} else {
				if (se <= dpos || ss > dpos || (ss+(len-se) < l)) {
					f.value = f.value.substring(0,ss)+key+f.value.substring(se);
					setEditCaretPos(f,ss+1);
					stopEvent(e);
					return true;
				}
				return false;
			}
		} else if (r != null && dpos != -1 && f.value.substring(dpos+1).length == r) {
			if (ss == se) {
				if (ss > dpos) return false;
				if (l != null && f.value.substring(0,dpos).length == l) return false;
				f.value = f.value.substring(0,ss)+key+f.value.substring(ss);
				setEditCaretPos(f,ss+1);
				stopEvent(e);
				return true;
			} else {
				if (se <= dpos || ss > dpos || (ss+(len-se) < l)) {
					f.value = f.value.substring(0,ss)+key+f.value.substring(se);
					setEditCaretPos(f,ss+1);
					stopEvent(e);
					return true;
				}
				return false;
			}
		} else return true;
	} else if (dot) {
		if (ss == 0 && (dpos == -1 || (dpos >= ss && dpos < se))) {
			f.value = (se > ss ? '0.'+f.value.substring(se) : '0.'+f.value);
			setEditCaretPos(f,2);
			stopEvent(e);
			return true;
		} else {
			return ((dpos == -1 && (l == null || ss <= l)) || (dpos >= ss && dpos < se));
		}
	} else {
		return true;
	}
}

//------------------------------------------------------------------------------
// searchf60 : check search id
// search_id = 1 : estate 4:customers
//------------------------------------------------------------------------------

function initForm(search_id)
{
 


    if(document.getElementById("province_id").value>2)
 	{
		document.getElementById("tab1").style.display="none";
        tab0.width="100%";
        	
	}
   
     
     	if(document.getElementById("isWine").value==1)
     	{
     	 
    		changesearchTab(1);
    	
    	}
    	else
    	{
    		if (search_id == 1)
    		{
    			document.getElementById("cmd").style.display="block";
    			document.getElementById("edt").style.display="none";
    			document.getElementById("contact").disabled=true;
    			searchf60.searchKey[2].checked = true;
    		}
    		if (search_id == 4 ||search_id == 9 ) //cutomer
    		{
    			document.getElementById("cmd").style.display="none";
    			document.getElementById("edt").style.display="block";
    			document.getElementById("contact").disabled=true;
    			document.getElementById("lkup_store_type_id").disabled=true;
    			searchf60.searchKey[0].checked = true;
    		}
    	
    		document.getElementById("search_id").value=search_id;
    	
    		changesearchTab(0);
    	}
    

 }

/*==============================================================================
 searchf60 : check search id
 search_id =  4 : search customer
              1 : search estate
------------------------------------------------------------------------------
*/

function checkAdt(chkName,ctlName)
{
		if(document.getElementById(chkName).checked==true)
			document.getElementById(ctlName).focus();
		
	
	
}
function changeName(keyvalue)
{
    document.getElementById("contact_key").value=keyvalue;
}

function changeKey(keyValue)
{
//	document.getElementById("chkstoretype").checked=true;
//	document.getElementById("chkAssign").checked=true;
	document.getElementById("chkAssign").disabled=false;

//	document.getElementById("lkup_store_type_id").disabled=false;
//	document.getElementById("user_id").disabled=false;
	document.getElementById("startwith").disabled=false;
	document.getElementById("chkstoretype").disabled=false;
	document.getElementById("chkisOOB").disabled=false;


	document.getElementById("search_field").disabled=false;
	document.getElementById("estate_id").disabled=true;


	
	if (keyValue==0) //customer
	{
		document.getElementById("cmd").style.display="none";
		document.getElementById("edt").style.display="block";
		document.getElementById("search_id").value="4";
		document.getElementById("contact").disabled=true;
		document.getElementById("search_field").focus();
	}
	else if (keyValue==1) //customer-contact-name
	{
		document.getElementById("cmd").style.display="none";
		document.getElementById("edt").style.display="block";
		document.getElementById("search_id").value="2";
		document.getElementById("contact").disabled=false;
		document.getElementById("search_field").focus();
	}
	else if (keyValue==2) 
	{
	 	
		document.getElementById("cmd").style.display="block";
		document.getElementById("edt").style.display="none";
		document.getElementById("contact").disabled=true;
		document.getElementById("startwith").disabled=true;
		document.getElementById("user_id").disabled=true;
		document.getElementById("chkAssign").disabled=true;
		document.getElementById("search_id").value="1";
		document.getElementById("startwith").checked=false;
		document.getElementById("chkAssign").checked=false;
		document.getElementById("chkisOOB").disabled=true;
		document.getElementById("chkstoretype").disabled=true;
	
	}
	else if (keyValue==4) //License / Agency / LRS number
	{
		document.getElementById("cmd").style.display="none";
		document.getElementById("edt").style.display="block";
		document.getElementById("search_id").value="3";
		document.getElementById("contact").disabled=true;
		document.getElementById("search_field").focus();
	}
	else if (keyValue==3) //invoice_number
	{
	 
		document.getElementById("cmd").style.display="none";
		document.getElementById("edt").style.display="block";
		document.getElementById("search_id").value="5";
		document.getElementById("contact").disabled=true;
		document.getElementById("search_field").focus();
		document.getElementById("estate_id").disabled=false;
	}
	else if (keyValue==5) //customer
	{
		document.getElementById("cmd").style.display="none";
		document.getElementById("edt").style.display="block";
		document.getElementById("search_id").value="6";
		document.getElementById("contact").disabled=true;
		document.getElementById("user_id").disabled=false;
		document.getElementById("search_field").focus();
	}
	else if (keyValue==10) //customer
	{
		document.getElementById("cmd").style.display="none";
		document.getElementById("edt").style.display="block";
		document.getElementById("search_id").value="10";
		document.getElementById("contact").disabled=true;
	//	document.getElementById("user_id").disabled=false;
		document.getElementById("search_field").focus();
	}
	else if (keyValue==11) //customer
	{
		document.getElementById("cmd").style.display="none";
		document.getElementById("edt").style.display="block";
		document.getElementById("search_id").value="11";
		document.getElementById("contact").disabled=true;
	   document.getElementById("search_field").disabled=false;
	   document.getElementById("search_field").focus();	
	}
	else if (keyValue==12) //customer
	{
		document.getElementById("cmd").style.display="none";
		document.getElementById("edt").style.display="block";
		document.getElementById("search_id").value="12";
		document.getElementById("contact").disabled=true;
	   document.getElementById("search_field").disabled=false;
	   document.getElementById("search_field").focus();	
	}
	
}

function enableStoretype(controlID)
{
    //  estateAdd.best1[ncheckID].checked=true;

    if ( controlID == 0)
    {

        if (document.getElementById("chkstoretype").checked)
            document.getElementById("lkup_store_type_id").disabled=false;
        else
            document.getElementById("lkup_store_type_id").disabled=true;
        ;
    }
    else
    {
        if (document.getElementById("chkAssign").checked)
            document.getElementById("user_id").disabled=false;
        else
            document.getElementById("user_id").disabled=true;
        ;

    }

}

//------------------------------------------------------------------------------
/* serachf60
    click "search" button
    id = 1 : estate
    id = 4 : customers
    
    adt: 3-> store number
    		5->invoice number
*/


function gosearch()
{
    var link;
    var searchKey;

    var sStart= "&is_start=1";
    var store_type ="";
    var user_id ="";
    
//	 alert( document.getElementById("search_id").value );
    
    

    
    if (document.getElementById("chkstoretype").checked)
    {
            store_type= "&store_type="+document.getElementById("lkup_store_type_id").value;
    }
    
    if (document.getElementById("chkAssign").checked)
    {
            user_id= "&user_id="+document.getElementById("user_id").value;
    }
    

	 if(document.getElementById("search_field").disabled==false)
	    searchKey = document.getElementById("search_field").value;
	    
//	searchKey = searchKey.trim();
    
   if ( document.getElementById("search_id").value == "2" )//customer's contact
    {

        link = "main.php?page_name=F60SearchResult&search_id=4&search_key=" + searchKey+ "&adt_field=" + document.getElementById("contact_key").value;
 		if (document.getElementById("chkstoretype").checked)
        {
         
            link =link + store_type;
        }
    }
    
 	if ( document.getElementById("search_id").value == "3" )//licensee number
    {
        link = "main.php?page_name=F60SearchResult&search_id=4&search_key=" + searchKey+ "&adt_field=3" ;
        if (document.getElementById("chkstoretype").checked)
        {
         
            link =link + store_type;
        }
    }
    if ( document.getElementById("search_id").value == "4" ) //customer name
    {

        link = "main.php?page_name=F60SearchResult&search_id=4&search_key=" + searchKey;
        if (document.getElementById("chkstoretype").checked)
        {
            link =link + "&store_type="+document.getElementById("lkup_store_type_id").value;
        }
        
    }
    if ( document.getElementById("search_id").value == "5" ) //invoice number
    {
		var estate_id = document.getElementById("estate_id").value;
		
        link = "main.php?page_name=F60SearchResult&search_id=4&search_key=" + searchKey+ "&adt_field=5"+"&estate_id="+estate_id;
        if (document.getElementById("chkstoretype").checked)
        {
         
            link =link + store_type;
        }
    }    

    if ( document.getElementById("search_id").value == "6" ) //oob
    {

        link = "main.php?page_name=F60SearchResult&search_id=4&is_OOB=1";
        if (document.getElementById("chkstoretype").checked)
        {
         
            link =link + store_type;
        }
    }
    
    if ( document.getElementById("search_id").value == "10" ) //phone number
    {

        link = "main.php?page_name=F60SearchResult&search_id=4&adt_field=10&search_key="+searchKey+store_type;
    }
    
     if ( document.getElementById("search_id").value == "11" ) //address
    {		
        link = "main.php?page_name=F60SearchResult&search_id=4&adt_field=11&search_key="+searchKey+store_type;
    }
    if ( document.getElementById("search_id").value == "12" ) //address
    {		
        link = "main.php?page_name=F60SearchResult&search_id=4&adt_field=12&search_key="+searchKey+store_type;
    }
    
    
	if (document.getElementById("startwith").checked)
     {
         link =link + sStart;
     }

	 if(document.getElementById("chkisOOB").checked)
     {
		   link =link + "&is_OOB=1";
     }
     if (document.getElementById("chkAssign").checked)
     {
         //var user_id =document.getElementById("user_id").value;
         link = link + user_id;
     }


    document.location = link;
    stopEvt();

    return false;
}

	function setFocus2Text(id)
	{
	 
	 	var controlName = "search_field";
	 	if(id==1)
	 	{
	 	 	controlName = "search_field_w";
	 	}
	// 	alert(controlName);
	 	if( document.getElementById(controlName).disabled==false)
	    document.getElementById(controlName).focus();
	   
	
	}
//End functions of searchf60 page

/*------------------------------------------------------------------------------
    select wine page
    isAll = 0 : select
    isAll=1: select all
------------------------------------------------------------------------------*/
function checkWine()
{

    if (document.getElementById("isWine").value==0)
    {
     	document.getElementById("showWine").style.display="none";
      	document.getElementById("trNowine").style.display="block";
		
    }
   
    else
    {
       document.getElementById("showWine").style.display="block";
       document.getElementById("trNowine").style.display="none";
    	
		 if(document.getElementById("pageid").value==24||document.getElementById("pageid").value==25||document.getElementById("pageid").value>=45)
     	{
       		document.getElementById("tdWine").style.display="block";
		}
     	else
     	{
     	 	document.getElementById("tdAllocate").style.display="block";
       		document.getElementById("tdWine").style.display="none";
         
      	}
      document.getElementById("wine_id").selectedIndex = 0;	
    }

    if( document.getElementById("pageid").value!=18 && document.getElementById("pageid").value!=20)
    {
      //	if(document.getElementById("btnAlctAll")!=null)
		document.getElementById("btnAllocate").style.display="none";
    }
  
}
function openWine(isNew)
{
	var wine_id = document.getElementById("wine_id").value;
	var estate_id = document.getElementById("estate_id").value;
	var is_international = document.getElementById("is_international").value;
	var pageid = document.getElementById("pageid").value;

	if(isNew==0) // add new wine
	{	 	
		slink = "main.php?page_name=wineAdd&editMode=0&estate_id="+estate_id+"&is_international="+is_international; 
		if(pageid==45 ||pageid==46 ||pageid==50) //beer
		{		 	
			slink = "main.php?page_name=beerAdd&editMode=0&estate_id="+estate_id+"&pageid="+pageid;
		}
        else if(pageid==56 )
            slink = "main.php?page_name=csProductAdd&editMode=0&estate_id="+estate_id+"&pageid="+pageid;
	}
	else // update wine
	{	 	
		slink = "main.php?page_name=wineAdd&editMode=1&wine_id="+wine_id+"&estate_id="+estate_id+"&is_international="+is_international;
		var j=0;
		if(pageid==45 ||pageid==46 ||pageid==50||pageid==56) //beer, sake, spirits
		{
			var indexs = document.getElementById("indexs").value;		 	
			var  beer_ids = "";
			for (i=0;i<=indexs-1;i++)
			{
			    if (document.getElementById("wine_id")[i].selected)
			    {			     
					j++;
			        var beerid =(document.getElementById("wine_id")[i].value);
			        beer_ids = beer_ids + beerid + "|";			
			    }
			}
			if(pageid==56)// cs products
			    slink = "main.php?page_name=csProductAdd&editMode=1&cs_product_id="+wine_id+"&estate_id="+estate_id+"&pageid="+pageid;
			else
			    slink = "main.php?page_name=beerAdd&editMode=1&beer_id="+wine_id+"&estate_id="+estate_id+"&beer_ids="+beer_ids+"&pageid="+pageid;		
		}
	}

	document.location = slink;
	stopEvt();
	return false;
}

function openNextPage(isAll)
{
    var pageid=document.getElementById("pageid").value;
    
    if(pageid==18 || pageid==20) //allocation
    {
        findallocateWine(isAll);
    }
    else //add or update a wine
    {
       var wine_id = document.getElementById("wine_id").value;
       var estate_id = document.getElementById("estate_id").value;
        
        if( pageid==24 || pageid==25||pageid==45||pageid==46||pageid==50||pageid==56)
        {         
         	openWine(1);
        }
        else 
        {
            if(pageid==19)//add delivery
            {
                slink = "main.php?page_name=wineAddCa&editMode=2&id="+wine_id;
                document.location = slink;
                stopEvt();
                return false;

            }
            else if(pageid==29) //update delivery
            {
				/*	slink = "main.php?page_name=wineAddCa&editMode=2&id="+wine_id;
                document.location = slink;
                stopEvt();
                return false;*/
            }
            
            else
            {
                slink = "main.php?page_name=wineAdd&editMode=1&id="+wine_id;
                document.location = slink;
                stopEvt();
                return false;
            }
        }        
    }
}

function findallocateWine(isAll)
{
	var wine_ids ="";
	var estate_id =document.getElementById("estate_id").value;
	var unwine_ids ="";
	var tmpids="";
	var isselect =0;
	var selected_ids="";
        
    if (isAll==0 )
    {
        var indexs =0;
        indexs = document.getElementById("indexs").value;
         var i=100;
        var wineid;

       for (i=0;i<=indexs-1;i++)
       {
            if (document.getElementById("wine_id")[i].selected)
            {
                isselect =1;

                wineid =(document.getElementById("wine_id")[i].value);
                 wine_ids = wine_ids + wineid + "|";

            }
       }

    }
    else
    {

       wine_ids =document.getElementById("wine_ids").value;

    }

    if (wine_ids!="")
   {
       wine_ids = wine_ids.substr(wine_ids,wine_ids.length-1);




    var link ="";

    var pageid =document.getElementById("pageid").value;

  if (pageid!=18)
      link = "main.php?page_name=allocatewine2customer&wine_ids="+wine_ids+"&estate_id="+estate_id+"&pageid="+pageid;
  else
  {
     if(document.getElementById("customer_id").value!="")
        link = "main.php?page_name=allocatewine2customer&wine_ids="+wine_ids+"&estate_id="+estate_id+"&pageid="+pageid+"&customer_id="+document.getElementById("customer_id").value;
     else
        link = "main.php?page_name=customerSelect&wine_ids="+wine_ids+"&estate_id="+estate_id+"&pageid="+pageid+"&searchid = 0";
  }

   document.location = link;

    stopEvt();
    return false;
   }
}



/*------------------------------------------------------------------------------
    select estate page
    double click esate go to change estate page
------------------------------------------------------------------------------*/
function selectEstate(estate_id)
{
    if (estate_id!="")
    {
        var pageid;
        pageid = document.getElementById("pageid").value;

        if(pageid==1)
            link = "main.php?page_name=estateAdd&id="+ estate_id;
        else if (pageid==24)
            link ="main.php?page_name=wineAdd&estate_id="+ estate_id + "&pageid="+pageid;
        else if (pageid==19)
            link ='main.php?page_name=F60SearchResult&search_id=19&search_key='+estate_id;
        else
            link = 'main.php?page_name=F60SearchResult&search_id=25&search_key='+estate_id;

        document.location = link;
        stopEvt();
        return false;
    }
}

function checkEstates()
{
  //  "estate_id_label"
  //  alert($("#pageid").val());
    
    var scountry = document.getElementById("country").value;
    if (scountry == "")
    {
        document.getElementById("trEstate").style.display="none";
        document.getElementById("estate_btns").style.display="none";
        document.getElementById("trNoEstate").style.display="block";
    }
    else
    {
        document.getElementById("estate_id").selectedIndex = 0;
    }
    
    if($("#pageid").val() == 56)
    {
        $("#td_country").hide();
        $("#estate_id_label").html("");
        $("#legen_title").html("Product Category");      
    }
}

//================= set Note's size to fill the window ===============
function setPaymentSize()
{
	var nwidth;

    nwidth =document.getElementById("tdinfo").clientWidth;
    document.getElementById("payment_info").style.width =nwidth-1 ;

}
//------------------------------------------------------------------------------
/*
    set first focus on first control: wineadd, estateAdd, customerAdd

    set best contact number
    estateAdd ,customerAdd pages
------------------------------------------------------------------------------*/
function setForm(pagename)
{

    if (pagename=="wineAdd")
    {
        if(document.getElementById("is_international").value==0 )
        {
            var total_cases =document.getElementById("total_cases").value;
            var bottles_per_case =document.getElementById("bottles_per_case").value;
            
            format2Currency(document.getElementById("price"));
            format2Currency(document.getElementById("wholesale"));
            var editMode =document.getElementById("editMode").value;
            if (editMode <=1 )           
            {
                document.getElementById("show_total_bottles").value =document.getElementById("total_bottles").value;
                
                document.getElementById("totalbtls").style.display="none";
                document.getElementById("not_sold").style.display="block";
                document.getElementById("wine_name").focus();
            }
            else
            {
                document.getElementById("show_total_bottles").value = total_cases*bottles_per_case;
                document.getElementById("totalbtls").style.display="block";
                document.getElementById("not_sold").style.display="none";
                document.getElementById("delivery_date").focus();
            }
            //override php2go date format
            Calendar.setup( {
            inputField:"delivery_date", ifFormat:"%m/%d/%Y", button:"delivery_date_calendar", singleClick:true, align:"Bl", cache:true, showOthers:true, onClose: dateCalendarClose});
        }
        else
        {
             document.getElementById("wine_name").focus();
        }

     }
    else if(pagename=="estateAdd")
    {

        var ncheckID =document.getElementById("lkup_phone_type_id").value-1;
        
        estateAdd.best1[ncheckID].checked=true;
        var cmName;
        var edtName;
        var rdoName;
        
        var typeVal=0;
        var rdoArray;

        if(document.getElementById("is_international").value==0 )
        {
            if(document.getElementById("cntype_1").value==1)
                format2Currency(document.getElementById("ctype_1"));
            if(document.getElementById("cntype_2").value==1)
                format2Currency(document.getElementById("ctype_2"));
            if(document.getElementById("cntype_3").value==1)
                format2Currency(document.getElementById("ctype_3"));
        
            if(document.getElementById("cntype_4").value==1)
                format2Currency(document.getElementById("ctype_4"));
            if(document.getElementById("cntype_5").value==1)
                format2Currency(document.getElementById("ctype_5"));
        
            for (i = 1; i <= 5; i++)
            {
                edtName = "cntype_" + i;
                rdoName = "cntype" + i;
                typeVal = document.getElementById(edtName).value;
                rdoArray = document.getElementsByName(rdoName);
                rdoArray[typeVal].checked=true;        
            }
        }
        else
        {
          var ncheckID =document.getElementById("is_fob").value;
          estateAdd.isfob[ncheckID].checked=true;
        }
       
        document.getElementById("estate_name").focus();
    }
    else if(pagename=="customerAdd") //chkOdQut
    {
       
        var nbestNo =document.getElementById("lkup_phone_type_id").value-1;
        customerAdd.best1[nbestNo].checked=true;
        
        var province_id= document.getElementById("province_id").value;
        
        if(province_id ==2 )
        { 
        	document.getElementById("tdrank").style.display="block";
        	document.getElementById("tddelivery").style.display="none"	
        }
        else
        {
        	document.getElementById("tdrank").style.display="none";
        	document.getElementById("tddelivery").style.display="block"	
        }
        
      
        if(document.getElementById("customer_id").value!="" )
        {
        		document.getElementById("chkOdQut").checked = true;
        		document.getElementById("chkQut").checked = true;
        		document.getElementById("chkCSOdQut").checked = true;
       	}

  		if(document.getElementById("user_id").value=="" || document.getElementById("user_id").value==null)
		{
			document.getElementById("user_id").value=0;
		}
         if(document.getElementById("customer_id").value!="" )
         {         
      
 			if ((document.getElementById("estate_id_order").value!="")||(document.getElementById("isorder").value=="1")
				|| (F60GetCookie("CustomerTab") == "Order")) 
				{
					changeTab(3); // set to ORder 3
				}
				else if((F60GetCookie("CustomerTab") == "Product"))
				{
						changeTab(5);
				}
				else
				{
					changeTab(0);  // set to customer:0 
				}
          
          	if(parseInt(document.getElementById("total_sales_pages").value)>1)   
            {             
				document.getElementById("tdFlip").style.display="block";
			}
			
            if(document.getElementById("isAdmin").value!=1)//not admin
            {
              // disable assign when user is not login, comment by Chris so sales any one can assigned the store to them self
                document.getElementById("user_id").disabled=true;
                document.getElementById("lkup_territory_id").disabled=true;
                
            }
			setCSInventory();
        }
        else
        {
            document.getElementById("customer_name").focus();
 		}
        F60DeleteCookie("CustomerTab");
        
    } 
    else if(pagename=="orderEdit")
    {
        Calendar.setup(
            {
                inputField:"delivery_date", ifFormat:"%m/%d/%Y", button:"delivery_date_calendar",
                singleClick:true, align:"Bl", cache:true, showOthers:true, onClose: dateCalendarClose,
                onUpdate: function(){formDirty = true;}
            }
        );
        
       if(document.getElementById("estateName").value=="Arrowleaf Cellars")
       		document.getElementById("trALInoviceNo").style.display="block";
       else
       		document.getElementById("trALInoviceNo").style.display="none";	
    }
}

function changeAssignUser(user_id)
{
    document.getElementById("assign_user_id").value=user_id;
}

/*
F60searchResult page
click close button to close page
*/
function closePage()
{
    gotoLastPage();
}

function goAddWine(estate_id)
{
    var link ="main.php?page_name=wineAdd&editMode=0&estate_id="+estate_id;
    document.location = link;
    stopEvent(window.event);
    return false;

}

/*internation estate*/
function addCountry()
{
    if (document.getElementById("tdcountry").style.display=="block")
    {
        document.getElementById("tdcountry").style.display="none";
        document.getElementById("tdcountries").style.display="block";
        document.getElementById("isCountry").value=0;
    }
    else
    {
        document.getElementById("tdcountry").style.display="block";
        document.getElementById("tdcountries").style.display="none";
        document.getElementById("billing_address_country_1").focus();
        document.getElementById("isCountry").value=1;
    
    
    }
}

 //select estate

function getEstatsByCountry(control_id, country)
{
    xajax_getEstatesByCountry(control_id, country);
}

function openPage()
{


	var pageid=document.getElementById("pageid").value;
	

	
	var country =document.getElementById("country").value;
	var slink ="";//+estate_id;
	var estateid=document.getElementById("estate_id").value;
	var searchid=document.getElementById("search_id").value;
	var searchKey=document.getElementById("search_key").value;

   if (estateid!="")
   {

        if( country=="Canada")
        {
             if (pageid==1) //goto change estate page
             {

                slink= "main.php?page_name=estateAdd&id="+estateid;
             }
             else if (pageid==24) //add wine
             {
                 //slink= "main.php?page_name=wineAdd&estate_id="+estateid;
                 if(country=="Canada")
                 		slink = "main.php?page_name=wineSelect&pageid=24&is_international=0&estate_id="+estateid;
                 else
                 		slink = "main.php?page_name=wineSelect&pageid=24&is_international=1&estate_id="+estateid;
             }
             else if (pageid==25) //edit wine
             {
                  
               //   slink = "main.php?page_name=wineSelect&is_international=0&estate_id="+estateid;
                if(country=="Canada")
		               slink = "main.php?page_name=wineSelect&pageid=25&is_international=0&estate_id="+estateid;
		          else
		          	 slink = "main.php?page_name=wineSelect&pageid=25&is_international=1&estate_id="+estateid;
             }
             else if (pageid==19)//add wine delivery
             {
                 slink= "main.php?page_name=wineSelect&is_international=0&pageid=19&estate_id="+estateid;
             }
             else if (pageid==29)
             {
                 slink="main.php?page_name=F60SearchResult&search_id=29&pageid='.$this->pageid"+"&search_key="+estateid;
             }
            
     
           
        }
       else//international estate
        {

           if(pageid == 1)
            {
                slink= "main.php?page_name=estateAdd&is_international=1&id="+estateid;
            }
            else
            {
                slink = "main.php?page_name=wineSelect&is_international=1&estate_id="+estateid+"&pageid="+pageid;
            }
        }
        document.location = slink;
        stopEvent(window.event);
    }
    else
    {
        alert("Please select an estate");
    }
}

function setFOB(isfob)
{
    document.getElementById("is_fob").value=isfob;
}

//---------commission levles -------------------

function checkOverComm(control,ctId)
{
	var chkLevel = "chklevel"+ctId;

    if(control.value=="")
    {
     if ( document.getElementById(chkLevel).checked)
        control.value="0";
       // control.focus();
    }
    if(control.value>30)
    {
        alert("Commission rate must smaller than 30");
        control.value=30;
        control.focus();
    }
}

function disableLevels(isdisabled,id)
{
	var nameCheck="chklevel"+id;
	var caseBegin="min_cases"+id;
	var caseEnd="max_cases"+id;
	var comm="comm"+id;
    
	if(!isdisabled)
	{
        document.getElementById(caseBegin).style.borderColor ="#A9A9A9";
        document.getElementById(caseEnd).style.borderColor ="#7F9DB9";
        document.getElementById(comm).style.borderColor ="#7F9DB9";
        document.getElementById(caseBegin).readOnly=true;
        document.getElementById(caseEnd).readOnly=false;
        document.getElementById(comm).readOnly=false;
        document.getElementById(caseEnd).focus();
   }
   else
   {
        document.getElementById(caseBegin).readOnly=true;
        document.getElementById(caseEnd).readOnly=true;
        document.getElementById(comm).readOnly=true;
        document.getElementById(caseBegin).style.borderColor ="#A9A9A9";
        document.getElementById(caseEnd).style.borderColor ="#A9A9A9";
        document.getElementById(comm).style.borderColor ="#A9A9A9";
   }
}
function checkLevels(id,isFirstLoad)
{
	var caseBegin="min_cases"+id;
	var caseEnd="max_cases"+id;
	var comm="comm"+id;	
	var beginCases=parseInt(parseInt(document.getElementById("min_intl_cases").value)+parseInt(document.getElementById("min_canadian_cases").value))+1;
	var nameCheck="chklevel"+id;
        
   if (document.getElementById(nameCheck).checked)
    {
      
         
         disableLevels(false,id)
         if(!isFirstLoad)
         {
            if(id==1)
            {
                document.getElementById(caseBegin).value=beginCases;
                document.getElementById(caseEnd).value="1000";
            }
            else
            {
                     var caseBegin_last="max_cases"+(id-1);

                beginCases =parseInt(document.getElementById(caseBegin_last).value)+1;
                document.getElementById(caseBegin).value=beginCases;
                document.getElementById(caseEnd).value="1000";

            }
         }
         
      
    }
    else
    {
       disableLevels(true,id);
       
       document.getElementById(caseBegin).value="";
       document.getElementById(caseEnd).value="";
       document.getElementById(comm).value="";

    }

}

function disableLevels(isDisabled,id)
{
    var nameCheck="chklevel"+id;
    var caseBegin="min_cases"+id;
    var caseEnd="max_cases"+id;
    var comm="comm"+id;
    var spmin="spmin"+id;
    var spmax="spmax"+id;
    var spcom="spcom"+id;
    if (!isDisabled)
    {
        document.getElementById(caseBegin).style.borderColor ="#A9A9A9";
        document.getElementById(caseEnd).style.borderColor ="#7F9DB9";
        document.getElementById(comm).style.borderColor ="#7F9DB9";
        document.getElementById(caseBegin).readOnly=true;
        document.getElementById(caseEnd).readOnly=false;
        document.getElementById(comm).readOnly=false;
        document.getElementById(spmin).style.color ="red";
        document.getElementById(spmax).style.color ="red";
        document.getElementById(spcom).style.color ="red";
        
        if(document.getElementById(comm).value=="")
            document.getElementById(comm).value=0;
        if(document.getElementById(caseEnd).value=="")
            document.getElementById(caseEnd).value=0;

        document.getElementById(caseEnd).focus();

    }
    else
    {
        document.getElementById(caseBegin).readOnly=true;
        document.getElementById(caseEnd).readOnly=true;
        document.getElementById(comm).readOnly=true;
        document.getElementById(caseBegin).style.borderColor ="#A9A9A9";
        document.getElementById(caseEnd).style.borderColor ="#A9A9A9";
        document.getElementById(comm).style.borderColor ="#A9A9A9";
        
            document.getElementById(spmin).style.color ="#A9A9A9";
            document.getElementById(spmax).style.color ="#A9A9A9";
            document.getElementById(spcom).style.color ="#A9A9A9";
        
        document.getElementById(caseBegin).value="";
       document.getElementById(caseEnd).value="";
       document.getElementById(comm).value="";


    }

}
function changeCommTab(id)
{
    var tab0 = document.getElementById("tab0");
    var tab1 = document.getElementById("tab1");

    tab0.className = "tab";
    tab1.className = "tabRight";

    if (id==0)//store
    {

		document.getElementById("trComm").style.display="block";
		document.getElementById("trComm_bcldb").style.display="none";
		tab0.className = "tabActive";
		document.getElementById("is_bcldb") =0;
		document.getElementById("min_intl_cases").focus();
    }
    else if(id==1)//contact
    {
        document.getElementById("trComm_bcldb").style.display="block";
        document.getElementById("trComm").style.display="none";
        tab1.className = "tabActive";
        document.getElementById("is_bcldb") =1;
        document.getElementById("sales_1").focus();
    }
}


function bcldb_changeLevels(id)
{
    var nameCheck="bcldb_chklevel"+id;
    var caseBegin="sales_"+id;
    var caseEnd="max_cases"+id;
    var comm="comm"+id;



    if (document.getElementById(nameCheck).checked)
    {
        if(id>1)
        {
            var lastCheck="bcldb_chklevel"+(id-1);
            var caseEnd ="bcldb_chklevel"+(id-1);
           
            if(!(document.getElementById(lastCheck).checked))
            {
               var levelName="Level "+(id-1);
               var msg=levelName+" is not check yet!";
               alert(msg);
               
               document.getElementById(nameCheck).checked=false;
            }
            
            else
            {
                document.getElementById("bcldb_levels").value=id;
                checkLevels(id,false);

            }
        }
        else
        {
             document.getElementById("levels").value=id;
            checkLevels(id,false);
            
        }


    }
    else
    {
        checkLevels(id,false);
        
        //disable high levels
        for ( i=id;i<5; i++)
        {
            ctlId =parseInt(i)+1;
             nameCheck="chklevel"+ctlId;
             caseBegin="min_cases"+ctlId;
             caseEnd="max_cases"+ctlId;
             comm="comm"+ctlId;
             
             document.getElementById(nameCheck).checked=false;
             disableLevels(true,ctlId);

        }
         document.getElementById("levels").value=parseInt(id)-1;

    }
}

function changeLevels(id)
{
    var nameCheck="chklevel"+id;
    var caseBegin="min_cases"+id;
    var caseEnd="max_cases"+id;
    var comm="comm"+id;

    if (document.getElementById(nameCheck).checked)
    {
        if(id>1)
        {
            var lastCheck="chklevel"+(id-1);
            var caseEnd ="chklevel"+(id-1);
           
            if(!(document.getElementById(lastCheck).checked))
            {
               var levelName="Level "+(id-1);
               var msg=levelName+" is not check yet!";
               alert(msg);
               
               document.getElementById(nameCheck).checked=false;
            }
            
            else
            {
                document.getElementById("levels").value=id;
                checkLevels(id,false);

            }
        }
        else
        {
             document.getElementById("levels").value=id;
            checkLevels(id,false);
            
        }


    }
    else
    {
        checkLevels(id,false);
        
        //disable high levels
        for ( i=id;i<5; i++)
        {
            ctlId =parseInt(i)+1;
             nameCheck="chklevel"+ctlId;
             caseBegin="min_cases"+ctlId;
             caseEnd="max_cases"+ctlId;
             comm="comm"+ctlId;
             
             document.getElementById(nameCheck).checked=false;
             disableLevels(true,ctlId);

        }
         document.getElementById("levels").value=parseInt(id)-1;
    }
}

function loadCommlevels()
{
    var levels = document.getElementById("levels").value;
    var nameCheck="";
    for (id=1;id<=levels;id++)
    {
    
       	nameCheck ="chklevel"+id;
    	document.getElementById(nameCheck).checked =true;
        checkLevels(id,true);
    }
    if(5-levels>0)
    {
    	for (id=(5-((5-levels-1)));id<=5;id++)
	   {
	        checkLevels(id,true);
	   }
    
    }
    changeCommTab(0);
    document.getElementById("trComm_bcldb").style.display="none";
    document.getElementById("trComm").style.display="block";
    document.getElementById("min_intl_cases").focus();

}


function setFocus(ctlName)
{
    document.getElementById(ctlName).focus();
}

function setCases(ctlCases,ctId)
{

    var ctlMinCases_cm="";
    var nMinCases_cm=0;
    
    var ctlMinCases="";
    var nMinCases=0;
    var nCases=0;
    var chkLevel = "chklevel"+(ctId-1);
    var nextLeve="chklevel"+ctId;

    if(ctlCases.value=="" )
    {
        if(ctId!=1)
        {
            if ( document.getElementById(chkLevel).checked )
                ctlCases.value=0;
        }
        else
          ctlCases.value=0;
    }
   
    if(ctId == 0) //internation
    {
        nCases = parseInt(ctlCases.value)+parseInt(document.getElementById("min_canadian_cases").value)+1;
        ctlMinCases ="min_cases1";
    }
    else if( ctId>0 ) //canadian
    {
        if(ctId==1)
        {
            nCases = parseInt(ctlCases.value) + parseInt(document.getElementById("min_intl_cases").value)+1 ;
        }
        else
        {
            if (ctlCases.value!="" )
                nCases = parseInt(ctlCases.value) + 1;
            else
                nCases=9000000000000000;
        }
        ctlMinCases ="min_cases"+ctId;

        ctlMinCases_cm ="min_cases"+(ctId-1);
    }


        if( ctId>1)
        {
             nMinCases_cm = parseInt(document.getElementById(ctlMinCases_cm).value);// getCtlValue(ctlMinCases_cm,0);
              if( nCases<=nMinCases_cm || nCases==nMinCases_cm)
                {
                  if ( document.getElementById(chkLevel).checked)
                    {
                        alert("Max cases must biger than minimumcases");
        
                        var minCases_next="99999999999";
                        if(ctId!=6)
                        {
                        minCases_next=parseInt(nMinCases_cm)+1;
                        }
                        ctlCases.value=minCases_next;
                        ctlCases.focus();
        
                    }
                }
                else
                {
                    if(ctId<6)
                    {
                        if(nCases!=9000000000000000)
                        {
                             if ( document.getElementById(nextLeve).checked)
                                  document.getElementById(ctlMinCases).value=nCases;
                        }
                        else
                        
                            document.getElementById(ctlMinCases).value="";
                    }
        
                }
        
        }
        else
        {
              document.getElementById(ctlMinCases).value=nCases;
        }
   // }
}












