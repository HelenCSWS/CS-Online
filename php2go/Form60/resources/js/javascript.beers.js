	/*function $(id)
	{
		return document.getElementById(id);
	}*/


	

	function initBeerPage()
	{
	 	var totalPro =2; // BC and AB
	 	
	 	var newCtl ="";
	 	var chkCtl="";
	 	var btnDel="";
	 	var avaPros=0;
	 	var currentProDelBtn ="";
        
		for(i=1; i<=totalPro; i++)
        {
         	newCtl="#new_"+i;	
         	chkCtl="#chk_"+i;
         	
			if($(newCtl).val()==0)
			{
			 	province_table_name = "#table_"+i;

				disanablePanelControls(province_table_name, true);		
			}
			else
			{
			 //	alert(chkCtl);
				$(chkCtl).attr("disabled", true); 
				
				currentProDelBtnName = "#btnDel_"+i;
				avaPros++;	
			}
		}
		
		if(avaPros==1)
			$(currentProDelBtnName).attr("disabled", true); 
		else if (avaPros==0)//add new
		{
			$("#hfPrev").hide();
			$("#hfNext").hide();
			
			
		}
		$("#hfPrev").hide();
		$("#hfNext").hide();
		
		if(avaPros>0)//update
		{
			
			$("#tdAddAnother").hide();
			setRollerdexlink();
			
		
		}
		$("#beer_name").focus();
	
	}
	
	function test()
	{
		//isPannelValidate(1);  
	
		$("#tr_basic td input").each(
			function (i, ctl){
				
				alert(ctl.value);
			}
		);
	}
	function setRollerdexlink()
	{
		var beerIdsCtlName = "#beer_ids";
		var beerIdCtlName="#beer_id";
		
		var arryBeerIds=$("#beer_ids").val().split("|");
		
		var ids= arryBeerIds.length-1; // "1|2|3|" last groupd is empty
	
		if(ids==1)
		{
			$("#hfPrev").hide();
			$("#hfNext").hide();
		}	
	}
	
	function enableControls(province_id)
	{
		ctlName="#chk_"+province_id;
	
		isDisable=true;
		if($(ctlName).is(":checked"))
			isDisable=false;
		
		province_table_name = "#table_"+province_id;
		
		disanablePanelControls(province_table_name, isDisable);
		
		newCtl="#new_"+province_id;
		if(!isDisable) // set focus
		{
			sku="#cspc_code_"+province_id;
			$(sku).focus();
			$(newCtl).val(1);
		}
		else
			$(newCtl).val(0);
		
	}
	
	function disanablePanelControls(panel_name, isDisable)
	{
		//input boxes	
		changeControlStatus("input",panel_name, isDisable);
		
		//LABEL	
		changeControlStatus("label",panel_name, isDisable);
		
		//SPAN	
		changeControlStatus("span",panel_name, isDisable);
		
		//select	
		changeControlStatus("select",panel_name, isDisable);
	}
	
	function delBeer(province_id)
	{
	 	if(province_id!=0)
		{
			var newCtlName="#new_"+province_id;
		
			var chkCtlName = "#chk_"+province_id;		
			if($(newCtlName).val()<2)
			{			 
			 	province_table_name = "#table_"+province_id;
				disanablePanelControls(province_table_name, true);
				
				ctlName="#chk_"+province_id;
				$(ctlName).attr("disabled", false); 
				
				newName="#new_"+province_id;
				$(newName).val(0); 
				
				stopEvt();
				return false;
			}	
		}
			
	 	$("#delete_id").val(province_id);
		
		var msg ="Do you want to delete the beer";
		
	    parent.showMsgBox( msg, parent.MBYESNO + parent.ICONQUESTION, executeDelete);
	
	}
	
	function executeDelete(retVal)
	
	
	
	{
	 	if (retVal == parent.IDYES)
	 	{
	 	 	var province_id=$("#delete_id").val();
			var beer_id =$("#beer_id").val();
			xajax_deleteBeer(beer_id,province_id);
	 	}
	}
	
	function beerDeleted(province_id)
	{
		if(province_id==0)//delete whole beer
		{
			closePage();
		}
		else
		{
		 	province_table_name = "#table_"+province_id;

			disanablePanelControls(province_table_name, true);
			ctlName="#chk_"+province_id;
			$(ctlName).attr("disabled", false); 
			newName="#new_"+province_id;
			$(newName).val(0); 
			
			var other_provinceid=1;
			if(province_id == 1 )
				other_provinceid=2;
			 //enable the delete button to let use delete the whole beer only, because only 1 province ava
			 var delBtnName ="#btnDel_"+other_provinceid;
			 
			 $(delBtnName).attr("disabled", true); 
		//	 $(delBtnName).
		}
	
	}
	//0: prev; 1: next
	function confirmSave(isNext)
	{
		$("#is_next").val(isNext);
		
		var msg ="Do you want to save the current beer";
		
	   parent.showMsgBox( msg, parent.MBYESNO + parent.ICONQUESTION, saveCurrentBeer);
	
	}
	
	function saveCurrentBeer(retVal)
	{
		/*if (retVal == parent.IDYES)
		{
		//	saveBeer();
		}
		else
	
		//	getNextBeer();
	*/;
	}
	
	function saveBeer()
	{
		//if(checkValidation());
			//save beer
	}
	
	function checkValidation()
	{
		//check basice information
		if(!isPannelValidate("#tr_basic"))
			return false;
		
		var i=0;
		
		var pannelName = "";
		var newCtlName="";
		for(i=1; i<=2; i++)//check provinces
		{
		 	pannelName = "#tr_info_"+i;
		 	newCtlName="#new_"+i;
		 	alert(newCtlName);
		 	if($(newCtlName).val()>0)
		 	{
				if(!isPannelValidate(pannelName))
					return false;
			}
			
			profitCtlName = "#profit_"+i;
			
			if($(profitCtlName).val()<=0)
			{
				var msg="Profit is not correct.";
				alert(msg);
				$(profitCtlName).focus();
			}
		}
		
		
					
	}
	
	function isPannelValidate(pannelName)
	{
	 	//province_table_name = "#table_"+province_id;
	 	filter = pannelName+" "+"td input";
	 	
	 //	filter = "#tr_basic td"+" "+"input";
	 
	 
		$(filter).each(
		
						function(i, ctl) 
						{	
						
								if(isControlEmtpy(ctl))
								{
								 	var msg = "Please fill the value in red star field";
		 							alert(msg);
									ctl.focus();
									return false;
								}
						}
					  ) 
	}
	
	function isControlEmtpy(ctl)
	{
	 	alert(ctl.value);
		if(ctl.value=="" || ctl.value==0 )
		{
		 	
			return true;
		}
	}
	
	

	function getNextBeer()
	{
			
		
		var nextBeerID = getNextBeerId(isNext);
		
	}
	//isNext: true: next; false: previous
	function getNextBeerId(isNext)
	{
	
		var current_beer_id =$("#beer_id").val();
			
		var arryBeerIds=$("#beer_ids").val().split("|");
		
		var nextBeeId ="";
		var prevIndex="";
		var nextIndex="";
		
		var idIndexes= arryBeerIds.length-1; // "1|2|3|" last groupd is empty
		
		var i=0;
		var currentIndex;
		for(i=0; i<idIndexes; i++)
		{
		 	beer_id = arryBeerIds[i];
			if(current_beer_id==beer_id);
			{
				currentIndex=i;
				break;
			}
		}
		
		if(currentIndex=1)
		{
		 	prevIndex=idIndexes-1;		
		}
		else
		{			
			prevIndex=currentIndex-1;
		}
		
		if(crrentIndex=(idIndexes-1))
		{
			nextIndex=1;
		}
		else
		{	
			nextIndex=currentIndex+1;
		}
		
		if(isNext)
			nextBeerId = arryBeerIds[nextIndex];
		else
			nextBeerId = arryBeerIds[prevIndex];
		
	}

	function changeControlStatus(ctlType,table_name, isDisable)
	{
	 	
	 	filter = table_name+" "+ctlType;
		$(filter).each(
						function(i, ctl) 
						{
							if(ctlType=='input'||ctlType=='select')
								enableButton(ctl,$(ctl).attr('type'),isDisable);
							else
								enableButton(ctl,ctlType,isDisable);
						}
					  ) 	
	}

	function enableButton(ctl,ctlType,isDisabled)
	{
	 
//	 alert(ctlType);
//	 	if(ctlType=='checkbox')
	 	switch (ctlType)
	 	{
	 	 	//alert(isDisabled);
	 	 	//alert(ctl.name);
	 	 	case 'checkbox':
				ctl.checked =!isDisabled;
				break;
			case 'text':
				
				ctl.disabled =isDisabled;
				
				if(!isDisabled)//profit alwasy disabled
				{
					if($(ctl).attr("id").search("profit")<0)
						setDisableColor(ctl,isDisabled,false);
				}
				else
				{				 
					setDisableColor(ctl,isDisabled,false);
					$(ctl).val("");
				}
				break;
			case 'label':
				ctl.disabled =isDisabled;
				setDisableColor(ctl,isDisabled,false);
				break;
			case 'span':
				ctl.disabled =isDisabled;
				setDisableColor(ctl,isDisabled,true);
				break;
			default:
				ctl.disabled =isDisabled;
			
				if(ctlType=="select-one")
				{				 	
					$(ctl).val("1");
				}
				break;
			//ctl.disabled =true;
		}
	}
	function setDisableColor(ctl,isDisabled, isStar)
	{	 
		if(isDisabled)//disabled
		{
			ctl.style.borderColor ="#A9A9A9";
		}
		else//enabled
		{
		
			if(isStar)
			{
				ctl.style.color="#FF0000";
			}
			else
			{
				ctl.style.borderColor ="#7F9DB9";	
			}
		}
	}
	
	function showElements(f) {
 


 /* var formElements = "";
  for (var n=0; n < beerAdd.elements.length; n++) {
      formElements += n + ":" + f.elements[n] + "\n";
  }
  alert("The elements in the form '" + beerAdd.name + "' are:\n\n" + formElements);*/
	}


	function setPrice(priceCtl,province_id)
	{
		format2Currency(priceCtl);	
		
		wholesaleId="#wholesale_"+province_id;
		costId="#cost_"+province_id;
		profitId="#profit_"+province_id;		
		
		wholesale_price=getCurrencyValue($(wholesaleId));
	
		cost=getCurrencyValue($(costId));
		
		if(wholesale_price!=0&&cost!=0)
		{
		 	profit="$"+(wholesale_price-cost)*0.45;
			$(profitId).val(profit);
				
			/*if(wholesale_price-cost<0)
			{
				alert("Cost is bigger than the CSWS price,please input the correct price.");
				$(costId).val(0);
				$(profitId).val(0);
				$(costId).focus();
			}
			else
			{
			 	profit="$"+(wholesale_price-cost)*0.45;
				$(profitId).val(profit);
			}*/
		}
		
	}
	
	function getCurrencyValue(jqPriceCtl)
	{
		if(jqPriceCtl.val()=="")
			price =0;
		else
			price=parseFloat(jqPriceCtl.val().replace("$",""));	
		

		return parseFloat(price);
	}

	function removeCurrency(priceCtl)
	{
	 
	 	if(priceCtl.value!="")
	 	{
			price=parseFloat(priceCtl.value.replace("$",""));	
			priceCtl.value=price;
		}
	}





















