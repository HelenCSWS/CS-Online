<SCRIPT language=javascript1.2 type=text/javascript>
<!--
        gotoTop(); //go to top frame
//-->
function login_mouseOn(oImg)
{
	oImg.src = "/resources/images/btn_login_hover.png";
}

function login_mouseOff(oImg)
{
	oImg.src = "/resources/images/btn_login.png";
}
function login_onClick(oImg)
{

	submitLogin();
}
</SCRIPT>

<STYLE type=text/css>

@import '//fonts.googleapis.com/css?family=Source Sans Pro:100,200,300,400,400i,500,600,700,800,900,300italic, italic';

BODY {
	FONT-SIZE: 12px; MARGIN: 0px; COLOR: #444444; FONT-FAMILY: Arial, Verdana, Helvetica, sans-serif; BACKGROUND-COLOR: #ffffff
}
.body {
	FONT-SIZE: 12px
}

INPUT {
FONT-SIZE: 11px;  font-family: verdana, arial, helvetica, sans-serif;
}

input:-webkit-autofill {
    background-color: white !important;
}

input:-webkit-autofill {
    background-color: white !important;
}

input:-webkit-autofill,
input:-webkit-autofill:hover,
input:-webkit-autofill:focus,
input:-webkit-autofill:active {
    -webkit-box-shadow: 0 0 0px 1000px white inset !important;
}



textarea:focus, input:focus{
    outline: none;
}

body:not(.user-is-tabbing) button:focus,
body:not(.user-is-tabbing) input:focus,
body:not(.user-is-tabbing) select:focus,
body:not(.user-is-tabbing) textarea:focus {
  outline: none;
}



TABLE {
	FONT-SIZE: 9.5px; COLOR: #444444
}
TD {
	FONT-SIZE: 9px; COLOR: #444444
}
P {
	MARGIN-TOP: 0px; MARGIN-BOTTOM: 10px
}
FORM {
	MARGIN: 0px
}
.td_loginLable {
	 width:60px;text-align:left;
}
.dataField {
 VERTICAL-ALIGN: top
}


.logo-holder{
    padding-top:35px;   
}

.psw-user-holder{
    
    padding-top:30px;
    font-size: 22px;
}

.form_holder input, .login-btn{    
   	width:380px;
	height:55px;
	font-size:11pt;
	border: 1px solid #DCDCDC;
	background-color:white;
    border-radius:8px;
    font-weight:500;
    text-align:center;
   
}

 .input-holder{
    padding-top:25px;
    text-algin:center;
}

.login-btn{
    background-color:#8d302b;
    color:white;
}

.chk-input{
    width:14px !important;
	height:14px !important;
}

.chk-table{
    margin:auto;
    width:260px;
}
.rem-text{
    font-size:18px;
    
}


</STYLE>




  <div class="logo-holder"><img width="300" height="75" src="http://www.christopherstewart.com/images/logo.svg"></div>
    <div class="psw-user-holder">Please enter your username and password</div>

    <div class="input-holder">{username}</div>
    <div class="input-holder">{userpass}</div>
    <div class="input-holder login-holder">
    <button class="login-btn" onclick="submitLogin();">Login</button>
    </div>
    

<div class="input-holder"><TABLE cellSpacing="0" cellPadding="0" border="0" class="chk-table" >
			<TR>
			<TD style="padding-top:0px" valign="bottom"><input class="chk-input" type="checkbox" name="uname" id="uname" border="0"></TD>
			<TD class="rem-text">Remember my username </TD>
			</TR>			
			</TABLE> </div>
            
<div class="input-holder"><TABLE cellSpacing="0" cellPadding="0" border="0" class="chk-table">
			<TR>
			<TD style="padding-top:0px" valign="bottom"><input class="chk-input"  type="checkbox" name="pword" id="pword" border="0"></TD>
			<TD class="rem-text">Remember my password</TD>
			</TR>			
			</TABLE> </div>            
            
            
       
  	<!-- TABLE cellSpacing="2" cellPadding=0 width="100%;" align=center height="100%" border="1">
    <TR><TD class=""><img src="/resources/images/logo.png"></TD></TR>
	<TR><TD style="padding-left:0px;padding-top:0px; vertical-align:top;">         
		<TABLE cellSpacing="0" cellPadding=0 width="300px;" height="50px;"  border="0">
		<TR>
		<TD colspan=3 style="FONT-SIZE: 9px; PADDING-BOTTOM: 5px;padding-bottom:15px;"width="100%" colSpan=2 nowrap><b>Please enter your username and password</B></TD>
		</TR>		
		  
		<TR>
		<TD class="td_loginLable">Username: </TD><TD width="220px" style="padding-left:8px;text-align:left;"> {username}</TD>
		</TR>
		
		<TR>
		<TD class="td_loginLable">Password : </TD><TD width="220px" style="padding-left:8px;text-align:left;"> {userpass}</TD>
		</TR>
		
		<TR>
		<TD style="padding-top:15px;padding-left:76px" colspan="2"><img style="cursor:pointer;" onclick="submitLogin();" onmouseover="login_mouseOn(this);" onmouseout="login_mouseOff(this);" src="/resources/images/btn_login.png"/></TD>
		</TR>
		
		<TR>
		<TD style="padding-top:15px;padding-left:70px;" colspan="3" valign="bottom">
			<TABLE cellSpacing="0" cellPadding="0" border="0">
			<TR>
			<TD style="padding-top:0px" valign="bottom"><input type="checkbox" name="uname" id="uname" border="0"></TD>
			<TD>Remember my username </TD>
			</TR>			
			</TABLE>
		</TD>
		</TR>
		
		<TR>
		<TD style="padding-top:0px;padding-left:70px;" colspan="3" valign="top">
			<TABLE cellSpacing="0" cellPadding="0" border="0"><tr><td style="padding-top:0px" valign="top">
			<input type="checkbox" name="pword" id="pword" border="0"></TD>
			<TD>Remember my password</TD></TR></TABLE>
		
		</TD></TR>
		</TABLE>
	
	</TD></TR>
	
	</TABLE>
    
    <TABLE  class="table_img_bkg" width="100%;" cellpadding="0" cellspacing="0"  ><TR>
<TD class="td_center_container" >
	<TABLE  class="table_loginForm" cellpadding="0" cellspacing="0">
	<TR><TD class="td_logo"><img src="/resources/images/logo.png"></TD></TR>
	<TR><TD class="td_main">main</TD></TR>
	<TR><TD class="td_msg">error-msg</TD></TR>
	</TABLE>
</TD></TR>
</TABLE>

-->                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
