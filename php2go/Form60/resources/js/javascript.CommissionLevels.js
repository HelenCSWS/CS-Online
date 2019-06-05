

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
        //  var caseLastEnd="max_cases"+(id-1);
        // var caseBegin="min_cases"+(id);
        //   document.getElementById(caseBegin).value=parseInt(document.getElementById(caseLastEnd).value)+1;
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

function bcldb_checkLevels(id,isFirstLoad)
{
 
    var nameCheck="bcldb_chklevel"+id;
       
   if (document.getElementById(nameCheck).checked)
    {
      
         
         bcldb_disableLevels(false,id)
    }
    else
    {
       bcldb_disableLevels(true,id);
    

    }

}

function pro_checkLevels(id,isFirstLoad,proid)
{
 
    var nameCheck="pro2_chklevel"+id;
     
   if (document.getElementById(nameCheck).checked)
    {
         
         pro_disableLevels(false,id,proid)
    }
    else
    {
       pro_disableLevels(true,id,proid);   

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
    //alert(spcom);
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

function bcldb_disableLevels(isDisabled,id)
{
    var nameCheck="bcldb_chklevel"+id;
    var nameSale="sales_"+id;
    var nameBonus="bonus_"+id;
    var spmin="bcldb_spmin"+id;  
    var spcom="bcldb_spcom"+id;  
    //alert(spcom);
    if (!isDisabled)
    {
        document.getElementById(nameSale).style.borderColor ="#7F9DB9";
        document.getElementById(nameBonus).style.borderColor ="#7F9DB9";
         document.getElementById(nameSale).readOnly=false;
        document.getElementById(nameBonus).readOnly=false;
        document.getElementById(spmin).style.color ="red";
        document.getElementById(spcom).style.color ="red";
     //   document.getElementById(nameSale).focus();

    }
    else
    {
        document.getElementById(nameSale).style.borderColor ="#A9A9A9";
        document.getElementById(nameBonus).style.borderColor ="#A9A9A9";
         document.getElementById(nameSale).readOnly=true;
        document.getElementById(nameBonus).readOnly=true;
        document.getElementById(spmin).style.color ="A9A9A9";
        document.getElementById(spcom).style.color ="A9A9A9";
        document.getElementById(nameSale).value="";
       document.getElementById(nameBonus).value="";
    }
}

function pro_disableLevels(isDisabled,id,proid)//province_id
{
 	var prefix="pro"+proid;
 	var suffix="pro_"+proid
    var nameCheck=suffix+"_chklevel"+id;
    var nameSale="sales_"+id+"_"+suffix;
    var nameBonus="bonus_"+id+"_"+suffix;
    var spmin=prefix+"_spmin"+id; 
    
    
    var spcom=prefix+"_spcom"+id;  
    
    if (!isDisabled)
    {
        document.getElementById(nameSale).style.borderColor ="#7F9DB9";
        document.getElementById(nameBonus).style.borderColor ="#7F9DB9";
         document.getElementById(nameSale).readOnly=false;
        document.getElementById(nameBonus).readOnly=false;
        document.getElementById(spmin).style.color ="red";
        document.getElementById(spcom).style.color ="red";
     //   document.getElementById(nameSale).focus();

    }
    else
    {
        document.getElementById(nameSale).style.borderColor ="#A9A9A9";
        document.getElementById(nameBonus).style.borderColor ="#A9A9A9";
         document.getElementById(nameSale).readOnly=true;
        document.getElementById(nameBonus).readOnly=true;
        document.getElementById(spmin).style.color ="A9A9A9";
        document.getElementById(spcom).style.color ="A9A9A9";
        document.getElementById(nameSale).value="";
       document.getElementById(nameBonus).value="";
    }

}

function changeCommTab(id)
{
    var tab0 = document.getElementById("tab0");
    var tab1 = document.getElementById("tab1");
	 var tab2 = document.getElementById("tab2");

    tab0.className = "tab";
    tab1.className = "tab";
    tab2.className = "tabRight";
		var trName= "trComm_pro"+id;

    if (id==0)//bc constultant
    {

		document.getElementById("trComm").style.display="block";
		document.getElementById("trComm_bcldb").style.display="none";
		document.getElementById("trComm_pro2").style.display="none";
		tab0.className = "tabActive";
		document.getElementById("is_bcldb").value =0;
		document.getElementById("min_intl_cases").focus();
    }
    else if(id==1)//bcldb
    {
		document.getElementById("trComm_bcldb").style.display="block";
		document.getElementById("trComm").style.display="none";
		document.getElementById("trComm_pro2").style.display="none";
		tab1.className = "tabActive";
		document.getElementById("is_bcldb").value =1;
		document.getElementById("sales_1").focus();
    }
 	else//ab consoultant
    {
		document.getElementById("trComm_bcldb").style.display="none";
		document.getElementById("trComm").style.display="none";
		
		document.getElementById(trName).style.display="block";
//		document.getElementById("trComm").style.display="none";
		tab2.className = "tabActive";
		//document.getElementById("is_bcldb").value =1;
		document.getElementById("sales_1_pro_2").focus();
    }
}


function bcldb_changeLevels(id)
{
 // alert(id);
    var nameCheck="bcldb_chklevel"+id;
    var salesName="sales_"+id;
    var bonusName="bonus"+id;
  



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
                    bcldb_checkLevels(id,false);

                }
            }
            else
            {
                 document.getElementById("bcldb_levels").value=id;
                bcldb_checkLevels(id,false);
                
            }


        }
        else
        {
            bcldb_checkLevels(id,false);
            
            //disable high levels
            for ( i=id;i<10; i++)
            {
                ctlId =parseInt(i)+1;
                 nameCheck="bcldb_chklevel"+ctlId;
                 salesName="sales_"+id;
    			     bonusName="bonus"+id;
                 document.getElementById(nameCheck).checked=false;
                 bcldb_disableLevels(true,ctlId);

            }
             document.getElementById("bcldb_levels").value=parseInt(id)-1;

        }
}
function pro_changeLevels(id,proid)
{
	var prefix = "pro"+proid;
	var suffix="pro_"+proid;
	var nameCheck=prefix+"_chklevel"+id;
	var salesName="sales_"+id+"_"+suffix;
	var bonusName="bonus"+id+"_"+suffix;


	if (document.getElementById(nameCheck).checked)
	{
		if(id>1)
		{
			var lastCheck=prefix+"_chklevel"+(id-1);
			var caseEnd =prefix+"_chklevel"+(id-1);
		
			if(!(document.getElementById(lastCheck).checked))
			{
				var levelName="Level "+(id-1);
				var msg=levelName+" is not check yet!";
				alert(msg);
				
				document.getElementById(nameCheck).checked=false;
			}
			
			else
			{
			 	var levels = "pro"+id+"_levels";
				document.getElementById("pro2_levels").value=id;
				pro_checkLevels(id,false,proid);
			}
		}
		else
		{
		 		var levels = "pro"+id+"_levels";

			document.getElementById("pro2_levels").value=id;
			pro_checkLevels(id,false,proid);
		}
	
	}
        else
        {
            pro_checkLevels(id,false,proid);
            
            //disable high levels
            for ( i=id;i<5; i++)
            {
                ctlId =parseInt(i)+1;
                 nameCheck=prefix+"_chklevel"+ctlId;
                 salesName="sales_"+id+"_"+suffix;
    			     bonusName="bonus"+id+"_"+suffix;
                 document.getElementById(nameCheck).checked=false;
                 pro_disableLevels(true,ctlId,proid);

            }
            var levels = "pro"+id+"_levels";
             document.getElementById("pro2_levels").value=parseInt(id)-1;

        }

}

function changeLevels(id)
{
 // alert(id);
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
 
   loadBcldbLevels();
   loadProvinceLevels(2);
   var levels = document.getElementById("levels").value;
  
   var nameCheck="";
   for (id=1;id<=levels;id++)
   {

	   	nameCheck ="chklevel"+id;
	   //	alert(nameCheck);
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
function loadBcldbLevels()
{
   var levels = document.getElementById("bcldb_levels").value;
   var nameCheck="";
   for (id=1;id<=levels;id++)
   {

	   	nameCheck ="bcldb_chklevel"+id;
	   //	alert(nameCheck);
    	document.getElementById(nameCheck).checked =true;
	    bcldb_checkLevels(id,true);
   }
   if(10-levels>0)
   {
		for (id=(10-((10-levels-1)));id<=10;id++)
		   {
		        bcldb_checkLevels(id,true);
		   }

   }
}

function loadProvinceLevels(pro_id)
{

	var levels = document.getElementById("pro2_levels").value;	
	var nameCheck="";
//	alert(levels);
	if(levels==0)
	{
		for (id=1;id<=5;id++)
		{
		
			pro_checkLevels(id,true,pro_id);
		}
	}
	
	
	for (id=1;id<=levels;id++)
	{
		nameCheck ="pro2_chklevel"+id;

		document.getElementById(nameCheck).checked =true;
		pro_checkLevels(id,true,pro_id);
	}
	
	//alert(levels);
	if(5-levels>0 && levels!=0)
	{
	
		for (id=(5-((5-levels-1)));id<=5;id++)
		{

			pro_checkLevels(id,true,pro_id);
		}
	
	}
   
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












