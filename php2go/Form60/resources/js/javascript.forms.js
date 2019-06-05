function submitAction(frm, actionName)
{

    var fld = 'action_name';
    var frmObject = getFormObj(frm);
 
  
   	//track the orderEdit for ArrowLeaf's invoice number
	if(frm=="orderEdit")
	{
		if(document.getElementById("estateName").value=="Arrowleaf Cellars")
		{
			if(document.getElementById("AL_invoice_no").value=="")
			{
				alert("Please input the Arrowleaf Cellars' invoice number");
				document.getElementById("AL_invoice_no").focus();
				return false;
			}
		}		

	}
	
    var fldAction = getFormFieldObj(frm, fld);
    

    if (fldAction == null)
    {
    
        fldAction = createHiddenField(frm, fld);
    }

    if(frm=="reportsMian") // why the hell it is happened on here csproductadd
    	fldAction = null;  // block the submit for reportMain page
    
    if (fldAction != null)
    {
      
        fldAction.value = actionName;
            
        if (frmObject.onsubmit())
        {
             frmObject.submit();
            return true;
        }
    }
    return false;
}

function cancelForm()
{
    if (formDirty)
    {
        parent.showMsgBox("Do you want to save your changes?", parent.MBYESNO + parent.ICONQUESTION, cancelHandler);
    }
    else
         gotoLastPage();
    return true;
}

function runDelete(deleteid)
{

    var vname = "wine";   
 
    if (deleteid==1)
        vname =document.getElementById("estate_name").value;
    else if (deleteid==4)
        vname =document.getElementById("customer_name").value;
    else if (deleteid==12)
        vname="user";
    else if (deleteid==9)
    {	
        vname="this order";
        var order_status_id = parseInt(document.getElementById('lkup_order_status_id').value);


        if (!canDeleteOrder(order_status_id)) 
            return;
        else
        { 
				xajax_saveOrderForm(xajax.getFormValues('orderEdit'));	
		}	
         
    }
    else if (deleteid==10)
    {	
    	 vname="this order";
    
     
         
    }

   var msg = "Delete "+vname + " ?        "
   if (deleteid == 19 )
        document.getElementById("pageid").value=19;

      parent.showMsgBox( msg, parent.MBYESNO + parent.ICONQUESTION, deleteHandler);


}


function deleteHandler(retVal)
{
    var id = document.getElementById("pageid").value;

    if (retVal == parent.IDYES)
    {
          if (id==1)
          {
              submitAction('estateAdd', 'delete');
           }
          else if (id==4)
          {
               submitAction('customerAdd', 'delete');
          }
         else if (id==12)
          {
               submitAction('userAdd', 'delete');
          }
           else if (id==25)//delete the wine
          {
               submitAction('wineAdd', 'btnDeleteWine');
          }
          else if (id==19)//delete a delivery
          {
               submitAction('wineAddCa', 'delete_delivery');
          }
          else if (id==9)//delete an order
          {
               submitAction('orderEdit', 'delete');
          }
          else if (id==100)//delete an cs order
          {
   
               submitAction('csOrderEdit', 'delete');
          }
    }
}

function cancelHandler(retVal)
{
    if (retVal == parent.IDYES)
    {
        if (saveButton.length > 0)
        {
            var btn = getDocumentObject(saveButton);
            //submit the form if there is one
            if (btn && btn.form)
            {
                submitAction(btn.form.id, btn.id);
            }
        }
    }
    else
        gotoLastPage();
    return true;
}

function createHiddenField(frm, fld)
{
    var frmObject = getFormObj(frm);
    if (frmObject != null)
    {
        var element = document.createElement('input');
        if (typeof(element) != 'undefined')
        {
            element.type = 'hidden';
            element.id = fld;
            element.name = fld;
            var fldObject = frmObject.appendChild(element);
            return fldObject;
        }
    }
    return null;
}

function attachEventHandlers()
{
    var forms = document.forms;
    for (var k=0; k<forms.length; k++)
    {
        //trap on change, this event won't bubble up
        for (var i=0; i<forms[k].elements.length; i++)
        {
		switch(forms[k].elements[i].type) {
			case 'text' :
			case 'password' :
			case 'textarea' :
			case 'select-one':
			case 'select-multiple':
                        case 'select':
                            addEvent(forms[k].elements[i], 'change', formOnChange);
                            break;
		}
	}

        //trap onclick
        addEvent( forms[k], 'click', formOnClick);
    }

}

function formOnLoad(prevPage, saveBtn)
{
    attachEventHandlers();
    formDirty = false;
    lastPage = prevPage;
    saveButton = saveBtn;
    var errDiv = document.getElementById("form_client_errors");
    if (errDiv)
    {
        var txt = errDiv.innerText;
        if (txt && txt.length > 0)
        {
            errDiv.style.display = "none";
            alert(txt);
        }
    }
    return true;
}

function formOnChange(e)
{
    formDirty = true;
}

function formOnClick(e)
{
    if (e = getEvent(e))
    {
        var element = getEventSrc(e);
        if (element && (element.type == 'button' || element.type == 'BUTTON'))
        {
            if (element.title != "Open the calendar") //add by wenling, if click calendar,not submit the form
            {
            	if (element.id.substr(0, 3) == 'btn')	//added by wenling to leave room for non-submit buttons
            	{
            	//alert(element.id);
                    if (element.id == 'btnCancel')
                        cancelForm();
                    else
                    {
                        //submit the form if there is one
                        if (element.form)
                        {
                          //submitAction(element.form.name, element.id);
                          submitAction(element.form.id, element.id); //change by wenling, because we have "name" field in html, form.name will return an object in this case
                       }
                    }
                }
            }
        }
    }
    return true;
}

function formOnUnload(e)
{
    if (formDirty)
    {
        e = getEvent(e)
        e.returnValue = "You have unsaved data in this page.";
        return false;
    }
    return true;
}

var formDirty = false;
var lastPage;
var saveButton;

function callSubmit(frmname,action_name)
{

   if (frmname == "estateAdd")
   {
        if (estateAdd_submit())
        {
            if (action_name =="btnAddWine")
            {
                document.getElementById("is_addwine").value="1";
            }
            submitAction(frmname, action_name);
        }
   }

   else if (frmname == "wineAdd")
   {
       if (wineAdd_submit())
        {
           submitAction(frmname, action_name);
        }
   }
   else if (frmname == "wineAddCa")
   {
       if (wineAdd_submit())
        {
           submitAction(frmname, action_name);
        }
   }
   else if (frmname == "wineDeliveryAdd")
   {
       if (wineAdd_submit())
        {
           submitAction(frmname, action_name);
        }
   }
    else if (frmname == "customerAdd")
    {
       if (customerAdd_submit())
        {
           submitAction(frmname, action_name);
        }
    }
    else if (frmname == "userAdd")
    {
       if (userAdd_submit())
        {
           submitAction(frmname, action_name);
        }
    }
    else if (frmname == "allocatewine2customer")
    {
          submitAction(frmname, action_name);
       
    }

}


function formatCurrency(num) 
{
    num = num.toString().replace(/\$|\,/g,'');
    if(isNaN(num))
        num = "0";
    sign = (num == (num = Math.abs(num)));
    num = Math.floor(num*100+0.50000000001);
    cents = num%100;
    num = Math.floor(num/100).toString();
    if(cents<10)
        cents = "0" + cents;
    for (var i = 0; i < Math.floor((num.length-(1+i))/3); i++)
        num = num.substring(0,num.length-(4*i+3))+','+
                num.substring(num.length-(4*i+3));
    return (((sign)?'':'-') + '$' + num + '.' + cents);
}

function filterNum(str) 
{
    re = /^\$|,/g;
    // remove "$" and ","
    return str.replace(re, "");
}


//pop up
/**
 * X-browser event handler attachment and detachment
 *
 * @argument obj - the object to attach event to
 * @argument evType - name of the event - DONT ADD "on", pass only "mouseover", etc
 * @argument fn - function to call
 */
function addEvent(obj, evType, fn){
 if (obj.addEventListener){
    obj.addEventListener(evType, fn, true);
    return true;
 } else if (obj.attachEvent){
    var r = obj.attachEvent("on"+evType, fn);
    return r;
 } else {
    return false;
 }
}
function removeEvent(obj, evType, fn, useCapture){
  if (obj.removeEventListener){
    obj.removeEventListener(evType, fn, useCapture);
    return true;
  } else if (obj.detachEvent){
    var r = obj.detachEvent("on"+evType, fn);
    return r;
  } else {
    alert("Handler could not be removed");
  }
}

/**
 * Code below taken from - http://www.evolt.org/article/document_body_doctype_switching_and_more/17/30655/
 *
 * Modified 4/22/04 to work with Opera/Moz (by webmaster at subimage dot com)
 *
 * Gets the full width/height because it's different for most browsers.
 */
function getViewportHeight() {
	if (window.innerHeight!=window.undefined) return window.innerHeight;
	if (document.compatMode=='CSS1Compat') return document.documentElement.clientHeight;
	if (document.body) return document.body.clientHeight; 
	return window.undefined; 
}
function getViewportWidth() {
	if (window.innerWidth!=window.undefined) return window.innerWidth; 
	if (document.compatMode=='CSS1Compat') return document.documentElement.clientWidth; 
	if (document.body) return document.body.clientWidth; 
	return window.undefined; 
}

/**
 * POPUP WINDOW CODE
 * Used for displaying DHTML only popups instead of using buggy modal windows.
 *
 * By Seth Banks (webmaster at subimage dot com)
 * http://www.subimage.com/
 *
 * Contributions by Eric Angel (tab index code) and Scott (hiding/showing selects for IE users)
 *
 * Up to date code can be found at http://www.subimage.com/dhtml/subModal
 *
 * This code is free for you to use anywhere, just keep this comment block.
 */

// Popup code
