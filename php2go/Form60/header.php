<html>
<head>

<script language="javascript">

function F60GetCookie(sName) 
{
    var re = new RegExp( "(\;|^)[^;]*(" + sName + ")\=([^;]*)(;|$)" );
    var res = re.exec( document.cookie );
    return res != null ? res[3] : null;
}

function F60SetCookie (name,value,nDays ) 
{
    var expires = "";
    if ( nDays ) {
            var d = new Date();
            d.setTime( d.getTime() + nDays * 24 * 60 * 60 * 1000 );
            expires = "; expires=" + d.toGMTString();
    }

    document.cookie = name + "=" + value+ "; path=/";    // + expires 
}

function F60DeleteCookie (name)
{
    if (F60GetCookie(name))
    {
        F60SetCookie( name, "", -1 );
    }

}


function loadProID()
{

	if(F60GetCookie("F60_USER_PROVINCE_ID")==0)
	{
		document.getElementById("province_id").value = F60GetCookie("F60_PROVINCE_ID");   
	}
	else
	{
		document.getElementById("province_id").value = F60GetCookie("F60_PROVINCE_ID");
		document.getElementById("province_id").style.display="none";
	
	}
	//reset the cookie, bcz there is bug that seems php can't replace the old cookie.
	F60SetCookie("F60_PROVINCE_ID",F60GetCookie("F60_PROVINCE_ID"));	  
	 
    
}

function setProvince(pro_id)
{
	F60SetCookie("F60_PROVINCE_ID",pro_id);
}


if (parent.childPageMousedown && parent.browser)
{
    if (parent.browser.isIE)
    {
        document.onmousedown = parent.childPageMousedown;
    }
    else
        document.addEventListener("mousedown", parent.childPageMousedown, true);
}



</script>
</head>

<body bgcolor="white" text="black" onload=loadProID();>


<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
	   <td align="left" WIDTH="*" style="padding-left:20px"><a href="http://www.christopherstewart.com/" target="_blank"><img id="IMG1" src="resources/images/upperleftcorner_graphic.png" border="0"  >
       </td>
       <!--td align="right" width="406" ><img src="resources/images/upperrigthcorner_graphic.png" border="0">
       </td-->
       
        <td align="right" width="406" height="63" background="resources/images/upperrigthcorner_graphic.png" border="0">
		  <table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
	   <td align="right" style="padding-right:8px">
		  <SELECT ID="province_id" NAME="province_id" TITLE="Select province" onChange="setProvince(this.value);"  STYLE="width:43px; font-size:8pt">

		  <OPTION VALUE="2">AB </OPTION>
			<OPTION VALUE="1" SELECTED>BC</OPTION>
			<OPTION VALUE="3">MB</OPTION>
            <OPTION VALUE="7">NB</OPTION>
            <OPTION VALUE="9">NS</OPTION>
            <OPTION VALUE="5">ON</OPTION>
            <OPTION VALUE="4">SK</OPTION>
            <OPTION VALUE="6">QC</OPTION>
</SELECT>
        
       </td>
    </tr>
        
    </table>
</td>
        </tr>
        
    </table>
		
</body>
</html>


