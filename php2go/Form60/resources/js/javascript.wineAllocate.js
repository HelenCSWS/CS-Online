//==============================================================================
//wine allocated fucntion
//==============================================================================
function enablecontrols(chk_name,ctl_name)
{
  if (document.getElementById(chk_name).checked)
            document.getElementById(ctl_name).disabled=false;
        else
            document.getElementById(ctl_name).disabled=true;
        ;
}
function getCtlChar(ctl_name)
{
    if ( document.getElementById(ctl_name).value.length!="" && document.getElementById(ctl_name).value!=null)
    {
        return document.getElementById(ctl_name).value;
    }
    else
        return "";
}

/*
isInt: true:interger; false: float

*/
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

function setValue2Ctrl(ctlName,nVal)
{
    document.getElementById(ctlName).value = nVal;
}

function setFocus(ctlName)
{
    document.getElementById(ctlName).focus();
}
function disabledCtrl(ctlName,isDis)
{
    document.getElementById(ctlName).disabled =isDis;
    if (isDis)
        document.getElementById(ctlName).style.borderColor ="#A9A9A9";

    else
        document.getElementById(ctlName).style.borderColor ="#7F9DB9";

}

function getSolds(wineid)
{
    var cSold = "edt_sold_"+wineid;
    var nSold =getCtlValue(cSold,0);

    return nSold;

}
//========================== select customers begin
/*
set customer focus
*/
function set2Customer()
{
    customerSelect.searchKey[0].checked = true
//    document.getElementById("searchKey")[0].value=1;
     disabledControls(true);
    setFocus("search_field");

}

function setFocus2Control(ctlName)
{
    setFocus(ctlName);
}

function disabledControls(isEnable)
{
    var i=0;
    var chkname ="";
    var edtname="";
    for (i=0;i<5 ;i++)
    {
        chkname ="searchAdt"+i;
        edtname ="adt"+i;
        disabledCtrl(chkname,isEnable);
         if (isEnable)
         {
            document.getElementById(chkname).checked=false;
           disabledCtrl(edtname,isEnable);
         }
        else
        {
           if (i==0)
                   document.getElementById(chkname).checked=true;
        }
      //  disabledCtrl(edtname,isEnable);
    }
}


//click radio button
function changeSelect(keyVal)
{
  if (keyVal< 3)
  {
        disabledCtrl("search_field",false);
        disabledControls(true);
         setFocus("search_field");
 }
  else
  {
        disabledCtrl("search_field",true);
        setValue2Ctrl("search_field","");
        disabledControls(false);
  }
    setValue2Ctrl("search_id",keyVal);
}


// input the text to input box
function checkAdt(keyindex,keyval)
{
    var i=0;
    var chkname ="";
    var edtname="";
    var nindex = parseInt(parseInt(1)+parseInt(keyindex));
     // if (keyindex ==1)
    //{
        if(keyval.indexOf("|")>0)
        {
          for (i=nindex;i<5 ;i++)
          {
            chkname ="searchAdt"+i;
            edtname ="adt"+i;
            disabledCtrl(chkname,true);
            disabledCtrl(edtname,true);
            setValue2Ctrl(edtname,"");
            document.getElementById(chkname).checked=false;
          }
        }
        else
        {
            for (i=nindex;i<5 ;i++)
              {
                chkname ="searchAdt"+i;
               // edtname ="adt"+i;
                disabledCtrl(chkname,false);
               // disabledCtrl(edtname,true);
               // setValue2Ctrl(edtname,"");
               // document.getElementById(chkname).checked=false;
              }
        }
    //}
}

//click address's option check box
function checkAdtKey(keyVal)
{
    var edtname = "adt"+keyVal;
     var chkname ="searchAdt"+ keyVal;
     if ( document.getElementById(chkname).checked==true)
     {
        disabledCtrl(edtname,false);
        setFocus(edtname);
     }
     else
     {
        disabledCtrl(edtname,true);
        setValue2Ctrl(edtname,"");
     }
}
function isChecked(chkname)
{
    return document.getElementById(chkname).checked;
}

//change contact last /first name combox
function changeName(keyvalue)
{
    document.getElementById("contact_key").value=keyvalue;
}

//click next button
function goallocate()
{

    var search_id = getCtlValue("search_id",0);
    var wine_ids =getCtlChar("wine_ids");
    var slink = "main.php?page_name=allocatewine2customer&wine_ids="+wine_ids+"&pageid=18&search_id="+search_id;
    var isOk = true;
    var is_start = "0";
    var useid="";
  //  var sType ="";
    if (isChecked("s_type"))
    {
        if (getCtlValue("adt5")==0)
        {
            isOk = false;
        }
    }

     if (isChecked("adt6"))
    {
          userid=getCtlValue("adt6");
    }

    if (isOk)
    {
        if (search_id <3)
        {
            var contact_key ="";
            var search_key = "";
            search_key =getCtlChar("search_field")
         //   search_key=search_key.replace("'","\\'");
           // alert(search_key);
            if(search_id ==1)
                contact_key =getCtlValue("contact_key",0);

            slink = slink+"&contact_key="+contact_key +"&search_key="+search_key;
        }
        else
        {
            var i =0;
            var chkName="";
            var  edtName="";
            var skey = "";
            var linkstr="";
            var ischk =0;
            var keyVal ="";
            var sfield = "";
            for (i=0;i<5 ;i++)
            {
                chkName = "searchAdt" + i;
                edtName = "adt" + i;
                skey ="&key" + i + "=";
                sfield = "&field"+i+"=";
                ischk =0;
                keyVal = getCtlChar(edtName);
                if (isChecked(chkName))
                    ischk =1;

                linkstr =linkstr + skey +ischk+sfield +keyVal;
            }
            slink = slink+linkstr;

        }


        if(isChecked("startwith"))
        {
           is_start="1";
        }
        slink = slink+"&s_type="+getCtlValue("adt5")+"&s_user="+useid+"&is_start="+is_start;

      //  alert(slink);
        document.location = slink;

    }
    else
    {
          alert ("Please select store type!");
    }
    stopEvt();
}


//===================end of select customer ====================================

//==================================== allocate w 2 c

function getTotal4Wines(wineid)//,nAllocates)
{

     var ctlName = "";
    var totals =0;

    var i =0;
    //if (nAllocates != -1)
      //  i=1;

      //real allocate
      ctlName ="edt_allocate_0_" +wineid;

      var allocate=getCtlValue(ctlName,0);
      //sold
      ctlName ="edt_sold_" +wineid;

      var sold =getCtlValue(ctlName,0);


   //   allocate =getTotal4Alct(wineid)+sold;//parseInt(allocate);
    for (i=1;i<=3;i++)
    {
       ctlName ="edt_allocate_" + i + "_" +wineid;
       totals =parseInt(totals)+getCtlValue(ctlName,0);
    }

    allocate = allocate-sold;
    totals =parseInt(totals)+parseInt(allocate);
  
 // alert(totals);

    return totals
}


/*
cAAA: controlname
nAAA: value

total & avalible is opsite
*/
function checkTotal(wineid)
{
    //get current total wines
   var current_ava = 0;
   current_ava = getTotal4Wines(wineid);

//alert("herer");
//alert(current_ava);

    //get avalible
    var cAvals ="edt_available_"+wineid;
    var nAvals =getCtlValue(cAvals,0);

    //get ctl name of total
   // var cTotal ="edt_total_" + wineid;

    var nTotals =(nAvals)-current_ava;


    if (nTotals)
    {
       // setValue2Ctrl(cTotal,nTotals);
        return nTotals;
    }
    else
        return nTotals;


}



function allocateWines(nTotals,wineid,nindex)
{

    var retVal= true;

    var cTotal ="edt_total_" + wineid;
    //get buffers
    var cBuffer ="edt_allocate_2" + "_" +wineid;
    var nBuffers =getCtlValue(cBuffer,0);

    //get samples
    var cSample ="edt_allocate_1" + "_" +wineid;
    var nSamples =getCtlValue(cSample,0);

    var cSample_bk ="hid_alc_1_" + wineid;
    var nSamples_bk =getCtlValue(cSample_bk,0);

    var cBuffer_bk ="hid_alc_2_" + wineid;

    var ndfval =0;
//alert(nindex);

  if(nindex == 0 || nindex ==3)
     {
         ndfval =nBuffers+nTotals;
          //get from buffer
            if (ndfval>=0)
            {
                retVal = true;
                setValue2Ctrl(cTotal,0);
                setValue2Ctrl(cBuffer,ndfval);
                setValue2Ctrl(cBuffer_bk,ndfval);
            }
            else //get from samples
            {
             /*   ndfval =nSamples+ndfval;
                if (ndfval>=0)
                {
                    retVal = true;
                    setValue2Ctrl(cTotal,0);
                    setValue2Ctrl(cBuffer,0);
                    setValue2Ctrl(cBuffer_bk,0);
                    setValue2Ctrl(cSample_bk,ndfval);
                    setValue2Ctrl(cSample,ndfval);
               }
               else*/
                    retVal = false;
            }
        }
   else if (nindex==1) //sample
   {

            ndfval =nBuffers+nTotals;
            if (ndfval>=0)
            {
                retVal = true;
                setValue2Ctrl(cTotal,0);
                setValue2Ctrl(cBuffer,ndfval);
                setValue2Ctrl(cBuffer_bk,ndfval);

                setValue2Ctrl(cSample_bk,ndfval);
                setValue2Ctrl(cSample,ndfval);
           }
            else //get from samples
            {
              /*  ndfval =nSamples+ndfval;
                if (ndfval>=0)
                {
                    retVal = true;
                    setValue2Ctrl(cTotal,0);
                    setValue2Ctrl(cBuffer,0);
                    setValue2Ctrl(cBuffer_bk,0);
                    setValue2Ctrl(cSample_bk,ndfval);
                    setValue2Ctrl(cSample,ndfval);
               }
               else*/

                  setValue2Ctrl(cSample_bk,nSamples_bk);
                 // setValue2Ctrl(cSample,ndfval);
                     retVal = false;
            }

   }
   else if(nindex ==2)
   {
            //  alert(nTotals);
           ndfval =nTotals;
            //  alert(ndfval);
          if (ndfval>=0)
            {
                retVal = true;
                setValue2Ctrl(cTotal,0);
              //  setValue2Ctrl(cSample,ndfval);
                //setValue2Ctrl(cSample_bk,ndfval);
            }
            else //get from samples
            {
               /* ndfval =nSamples+ndfval;
                if (ndfval>=0)
                {
                    retVal = true;
                    setValue2Ctrl(cTotal,0);
                    setValue2Ctrl(cBuffer,0);
                    setValue2Ctrl(cBuffer_bk,0);
                    setValue2Ctrl(cSample_bk,ndfval);
                    setValue2Ctrl(cSample,ndfval);
               }
			   else*/
	            retVal = false;
			}

    }

    return retVal;
}
/*caculate totals */
function setTotals(wineid,nindex,nfromcm)
{
   // alert(nindex);
     var retVal = true;
    var cTotal ="edt_total_" + wineid;
     var nTotals = checkTotal(wineid); //?????


    var ctlName ="edt_allocate_"+nindex+"_"+wineid;

  //  nindex = parseInt(parseInt(nindex)+parseInt(nfromcm));

    var  ctlName_bk="hid_alc_"+nindex+"_"+wineid;
    if (nindex==0)
         ctlName_bk ="allocated_"+wineid;

    var nVal = 0;
    var nVal_bk=0;


  if (nindex!=0)
    {
        nVal =getCtlValue(ctlName,0);
        nVal_bk =getCtlValue(ctlName_bk,0);
    }

//	alert(ctlName_bk);

 //   alert(nVal);
//    alert(nVal_bk);
//

//alert(nindex);

  //  if (nindex == 0 || nVal!=nVal_bk)
  //  {
        if (nTotals < 0 )
        {
            if ( !allocateWines(nTotals,wineid,nindex))
            {

                retVal = false;
                setValue2Ctrl(ctlName,nVal_bk);
                alert ("You have over allocated this wine, please reduce the number and try again.");
                if (nindex != 0)
                    setFocus(ctlName);
            }


            else
            {

                if (nindex != 0  )
                setValue2Ctrl(ctlName_bk,nVal);
            }
        }
        else
        {
                setValue2Ctrl(ctlName_bk,nVal);
                nTotals = nTotals;//+getSolds(wineid);

                setValue2Ctrl(cTotal,nTotals);
        }
  //   }
    return retVal;
}

/*add all allcota  numbers of cusomters*/
function getTotal4Alct(wine_id)
{
    var customers =getCtlValue("customers",0);
   // alert(customers );

    var ctlAlName="";
    var bottles =0;
    var bottle =0;
    var ctlalt="_allocated"
    for (i=0;i<customers;i++)
    {
        ctlAlName = i + "_"+wine_id +ctlalt;
        bottle = getCtlValue(ctlAlName,0);
        bottles = bottles+bottle;

    }
    return bottles;
}

function exc_allocate(wine_id,row_index)
{
//get control's name by a wine_id
    var ctlCmAlct =row_index +"_"+wine_id+"_allocated";
       var ctlsolds = "edt_sold_" +wine_id;

//get Alct numbers and sold numbers for a CM
    var nCmAlct = getCtlValue(ctlCmAlct,0);
    var nSolds = getCtlValue(ctlsolds,0);

//get old available numbers
    var ctlavailable="edt_total_"+wine_id;
    var nAvailables =getCtlValue(ctlavailable,0);


 //get orignal numbers

    var ctlCmAlct_bk =row_index +"_"+wine_id+"_old_alct";
    var nCmAlct_bk = getCtlValue(ctlCmAlct_bk,0);

   // alert(nCmAlct_bk);

    var ctlCmTotal =wine_id+"_tal_allocate";

//get total Orignal allocate number for all CMs
var ctlAllwineAlct ="edt_allocate_0_"+wine_id;
var nCmTotals =getCtlValue(ctlAllwineAlct,0);

    //orignal customer total allocations - origenal customer allocation
    nCmTotals =nCmTotals -nCmAlct_bk;
 //   alert(nCmTotals);
 //   alert(nCmAlct);
    nCmTotals = nCmTotals+nCmAlct;
//alert(nCmTotals);
    var ctlSbt = "hid_alc_stb_"+wine_id;
    var nOldAlcts =getCtlValue(ctlSbt,0);

    var ctlWnAlcts ="edt_allocate_0_"+wine_id;
    var nWnAlcts = getCtlValue(ctlWnAlcts,0)

    var allAlcts = nCmTotals;

    // alert(nCmAlct_bk);
       if (nCmAlct!= nCmAlct_bk)
    {

        setValue2Ctrl(ctlWnAlcts,allAlcts);
        var isEnough = setTotals(wine_id,0,1);


        if ( !isEnough)
        {

            setValue2Ctrl (ctlCmAlct,nCmAlct_bk);
            setValue2Ctrl (ctlWnAlcts,nWnAlcts);
            setFocus(ctlCmAlct);
       }
             else
       {
       // alert(nCmAlct);
           setValue2Ctrl (ctlCmAlct_bk,nCmAlct);

          setValue2Ctrl (ctlWnAlcts,allAlcts);

       }
    }
}

/*
set total bottles number to Total and Customer form onload
*/
function setTotalVals(isWine)
{
    if (isWine==0)
         document.getElementById("lgdAltc").style.display="none";
      else
    {
        document.getElementById("divWine").style.height="100px";
    }



  var isNoCm =getCtlValue("isNoCm",0);


   if(isNoCm!=1 )
   {

      var nwnumbers = getCtlValue("wine_numbers");
        var i = 0;
        var widName="";
        var wine_id = "";
      /*  var ctlWnTotal="";
        var ctlCmTotal ="";
        var ctlTotal ="";

        var nTotals =0;
        var nwTotals =0;
        var nDbBottles =0;
        var nAvablies = 0;*/

        var firstid =0 ;
        widName ="wine_id_0";
        wine_id =getCtlValue(widName);
         firstid = wine_id;

    //nwnumbers =1;
       /* for (i=0;i<nwnumbers;i++) wenling
        {

            widName ="wine_id_"+i;
             wine_id =getCtlValue(widName);
            if (i==0)
                firstid = wine_id;

            //customer's total
            ctlCmTotal =wine_id+"_tal_allocate";

            //wine customer total
            ctlWnTotal ="edt_allocate_0_"+wine_id;

            //total
             ctlAva ="edt_available_" + wine_id;

             //avalible
             ctlTotal ="edt_total_" + wine_id;


             //sbt conttol
             var ctlSbt = "hid_alc_stb_"+wine_id;

           if(isWine!=0)
           {


              nwTotals= getTotal4Alct(wine_id);

              //get db wines
                var dbWines = getCtlValue(ctlWnTotal,0);
                var sbtWines = dbWines -nwTotals;

                setValue2Ctrl(ctlSbt,sbtWines);

           }
         nTotals =getTotal4Wines(wine_id);

           //get all db's bottles
         nDbBottles =getCtlValue(ctlAva);

            //get avalibel bottles

          nAvablies = nDbBottles -nTotals;
          nAvablies = nAvablies;//-getSolds(wine_id);
        //  alert(nAvablies);
           setValue2Ctrl(ctlTotal,nAvablies);
        }*/
        var focusName = "";

        if (isWine == 0 )
        {
           focusName ="edt_allocate_1_"+firstid ;

        }
        else
        {
             focusName ="0_"+firstid + "_allocated";
        }

         setFocus(focusName);

    }
    else if(isNoCm==1)//no cm selected
    {
        document.getElementById("trAlct").style.display="none";
        document.getElementById("trNoCm").style.display="block";
        document.getElementById("trbtn").style.display="none";
        document.getElementById("lgdAltc").style.display="none";
     }

   if(isNoCm==0)

   {
      if (getCtlValue("total_pages")==1)
        {
                document.getElementById("tdnext").style.display="none";
                document.getElementById("tdfirst").style.display="none";
                document.getElementById("tdpre").style.display="none";
                document.getElementById("tdlast").style.display="none";
                document.getElementById("divCm").height ="1%";
        }
        else
        {

            if (getCtlValue("current_page")==1)
            {
                document.getElementById("tdnext").style.display="block";
                document.getElementById("tdfirst").style.display="none";
                document.getElementById("tdpre").style.display="none";
                document.getElementById("tdlast").style.display="block";
              //  alert("there");
            }
            else
            {
               if(getCtlValue("current_page")<getCtlValue("total_pages"))
               {
                    // alert("there");
                    document.getElementById("tdfirst").style.display="block";
                    document.getElementById("tdnext").style.display="block";
                    document.getElementById("tdpre").style.display="block";
                    document.getElementById("tdlast").style.display="block";
               }
               else
               {
                    document.getElementById("tdfirst").style.display="block";
                    document.getElementById("tdnext").style.display="none";
                    document.getElementById("tdpre").style.display="block";
                    document.getElementById("tdlast").style.display="none";

               }
            }
        }
    }

    /*
    else if (getCtlValue("total_pages")==2)
    {
        current_page
            document.getElementById("tdnext").style.display="none";
            document.getElementById("tdfirst").style.display="none";
            document.getElementById("tdlast").style.display="block";
    }
    else if (getCtlValue("total_pages")>2)
    {
        document.getElementById("tdfirst").style.display="none";
        document.getElementById("tdlast").style.display="block";
    }*/
     if (isWine == 0)
     {
           widName ="wine_id_"+0;
           wine_id =getCtlValue(widName);
           firstid = wine_id;
           focusName ="edt_allocate_1_"+firstid ;
           setFocus(focusName);
     }

}

//new allocate page
function saveAllocate()
{

    if (allocatewine2customer_submit())
    {
         setValue2Ctrl("isCurrentSave",0); //click ok save all page,  current page will not save by "next" or "previous" button
         submitAction("allocatewine2customer", "btnSave");
    }

    stopEvt();
    return false;


}


function getCustomers(statusid)
{
    var currentpage =0;

    if ( statusid ==0 )
        currentpage =1 ;
    else if ( statusid ==1 )
        currentpage = parseInt(parseInt(getCtlValue("current_page"))+1);
    else if ( statusid ==2 )
    {
        currentpage = parseInt(parseInt(getCtlValue("current_page"))-1);

     }
    else if ( statusid ==3 )
        currentpage = parseInt(getCtlValue("total_pages"));


    setValue2Ctrl("current_page",currentpage);

    setValue2Ctrl("status",statusid);
    setValue2Ctrl("isCurrentSave",1); //save current page

   if (allocatewine2customer_submit())
    {
        var mform = document.getElementById("allocatewine2customer");
        mform.submit();
        // submitAction("allocatewine2customer", "saveCurrent");
    }

    stopEvt();
    return false;
}
//init pages: set focus on first control
//0->allocate wine 1->allocate2 customer
function initPage(pageid)
{
  if (pageid == 0 )
  {
    setValue2Ctrl("isSave",0);
    if(document.getElementById("wine_numbers")!=0)
    {
       var wine_id =document.getElementById("wine_id_0").value;
      // alert(wine_id);
       var ctlname ="edt_allocate_0_" + wine_id;
      // alert(ctlname);
        document.getElementById(ctlname).focus();
    }
  }
  else if (pageid==1)
  {

  }




}
