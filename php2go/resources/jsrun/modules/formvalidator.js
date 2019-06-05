FormValidator.MODE_ALERT=1;FormValidator.MODE_DHTML=2;FormValidator.LIST_FLOW=1;FormValidator.LIST_BULLET=2;FormValidator.FIELD_FIELD=1;FormValidator.FIELD_VALUE=2;function FormValidator(frm){this.frm=frm;this.req=new Array();this.chk=new Array();this.len=new Array();this.rule=new Array();this.ept=new Array();this.fail=new Array();this.editor=null;this.errmsg='';this.errmode=FormValidator.MODE_ALERT;this.errlm=FormValidator.LIST_FLOW;this.errnl="\n";this.errls="---------------------------------------------------------------------------------------\n";this.errdiv=null;this.errhdr=formFieldsInv;}FormValidator.prototype.setErrorOptions=function(mode,div,lm,hdr){if(mode==FormValidator.MODE_DHTML&&div!=null&&div!=""){this.errmode=mode;this.errnl="<br/>";this.errls="";this.errdiv=div;if(lm!=null&&(lm==FormValidator.LIST_FLOW||lm==FormValidator.LIST_BULLET))this.errlm=lm;}else{this.errmode=FormValidator.MODE_ALERT;}if(hdr!=null)this.errhdr=hdr;};FormValidator.prototype.addRequiredField=function(fname,flabel){this.req[this.req.length]={name:fname,label:flabel,minsize:0};};FormValidator.prototype.addLookupCheck=function(fname,flabel,fminsize){this.req[this.req.length]={name:fname,label:flabel,minsize:fminsize};};FormValidator.prototype.addCheckField=function(fname,fmask,emsg,eargs){this.chk[this.chk.length]={name:fname,mask:fmask,msg:emsg,args:(eargs!=null?','+eargs:''),exp:"r = (isEmpty('%1', '%2') || chk%3(document.%4.elements['%5']%6));"};};FormValidator.prototype.addLengthCheck=function(fname,frule,flimit,emsg){this.len[this.len.length]={name:fname,rule:frule,op:(frule=="maxlength"?"<=":">="),limit:flimit,exp:"r = (isEmpty('%1', '%2') || document.%3.elements['%4'].value.length %5 %6)",msg:emsg};};FormValidator.prototype.addRule=function(srcfld,rtype,datatype,trgfld,trgval,emsg){var sl,tl;sl=getFormFieldAttribute(this.frm,srcfld,'title',srcfld);tl=(trgfld!=null?getFormFieldAttribute(this.frm,trgfld,'title',trgfld):null);this.rule[this.rule.length]={source:srcfld,sourcelbl:sl,type:rtype,datatype:datatype,comptype:(trgval!=null?FormValidator.FIELD_VALUE:FormValidator.FIELD_FIELD),target:(trgval!=null?trgval:trgfld),targetfld:trgfld,targetlbl:tl,msg:emsg};};FormValidator.prototype.isValid=function(){var i,o,v,r,f,fe,fw;if(this.errhdr!=''&&this.errmode==FormValidator.MODE_ALERT)this.errhdr+=this.errnl;this.updateEditorValue();for(o in this.req){o=this.req[o];if(o.minsize!=null&&o.minsize>0){f=getDocumentObject(o.name);if(f!=null&&f.options.length<o.minsize){this.ept[this.ept.length]=(this.errmode==FormValidator.MODE_ALERT?o.label:stringReplace(formFieldReq,o.label));if(!fe)fe=o.name;}}else if(isEmpty(this.frm,o.name)){this.ept[this.ept.length]=(this.errmode==FormValidator.MODE_ALERT?o.label:stringReplace(formFieldReq,o.label));if(!fe)fe=o.name;}}for(o in this.len){o=this.len[o];eval(stringReplace(o.exp,this.frm,o.name,this.frm,o.name,o.op,o.limit));if(!r){this.fail[this.fail.length]=o.msg;if(!fw)fw=o.name;}}for(o in this.chk){o=this.chk[o];eval(stringReplace(o.exp,this.frm,o.name,o.mask,this.frm,o.name,o.args));if(!r){this.fail[this.fail.length]=o.msg;if(!fw)fw=o.name;}}for(o in this.rule){o=this.rule[o];if(/^(EQ|NEQ|GT|LT|GOET|LOET)$/.test(o.type)){if(!this.compareValues(o.source,o.target,o.type,o.datatype,o.comptype)){this.fail[this.fail.length]=(o.msg!=null?o.msg:this.getErrorMessage(o.type,o.sourcelbl,(o.comptype==FormValidator.FIELD_VALUE?o.target:o.targetlbl),o.comptype));if(!fw)fw=o.source;}}else if(o.type=='REGEX'&&!isEmpty(this.frm,o.source)){eval("r = "+o.target+".test(\""+getFormFieldValue(this.frm,o.source)+"\");");if(!r){this.fail[this.fail.length]=(o.msg!=null?o.msg:stringReplace(formFieldsRegex,o.sourcelbl));if(!fw)fw=o.source;}}else if(o.type=='REQIF'&&isEmpty(this.frm,o.source)&&!isEmpty(this.frm,o.target)){if(o.msg!=null){if(this.errmode==FormValidator.MODE_ALERT){this.fail[this.fail.length]=o.msg;if(!fw)fw=o.source;}else{this.ept[this.ept.length]=o.msg;if(!fe)fe=o.source;}}else{this.ept[this.ept.length]=(this.errmode==FormValidator.MODE_ALERT?o.sourcelbl:stringReplace(formFieldReq,o.sourcelbl));if(!fe)fe=o.source;}}else if(/^REQIF(EQ|NEQ|GT|LT|GOET|LOET)$/.test(o.type)){if(isEmpty(this.frm,o.source)&&!isEmpty(this.frm,o.targetfld)&&this.compareValues(o.targetfld,o.target,o.type.replace('REQIF',''),o.datatype,o.comptype)){if(o.msg!=null){if(this.errmode==FormValidator.MODE_ALERT){this.fail[this.fail.length]=o.msg;if(!fw)fw=o.source;}else{this.ept[this.ept.length]=o.msg;if(!fe)fe=o.source;}}else{this.ept[this.ept.length]=(this.errmode==FormValidator.MODE_ALERT?o.sourcelbl:stringReplace(formFieldReq,o.sourcelbl));if(!fe)fe=o.source;}}}}if(this.ept.length>0){if(this.errmode==FormValidator.MODE_ALERT){this.errmsg=formFieldsReq+this.errnl+this.errls;for(i=0;i<this.ept.length;i++)this.errmsg+=this.ept[i]+this.errnl;this.errmsg+=this.errls+formComplFields;}else{this.errmsg=this.errhdr+this.errls;if(this.errlm==FormValidator.LIST_BULLET)this.errmsg+='<ul>';for(i=0;i<this.ept.length;i++)this.errmsg+=(this.errlm==FormValidator.LIST_BULLET?'<li>'+this.ept[i]+'</li>':this.ept[i]+this.errnl);if(this.errlm==FormValidator.LIST_BULLET)this.errmsg+='</ul>';this.errmsg+=this.errls;}this.showErrors();if(fe!=null)requestFocus(this.frm,fe);return false;}else if(this.fail.length>0){if(this.errmode==FormValidator.MODE_ALERT){this.errmsg=this.errhdr.replace(/(<([^>]+)>)/ig,'')+this.errls;for(i=0;i<this.fail.length;i++)this.errmsg+=this.fail[i]+this.errnl;this.errmsg+=this.errls+formFixFields;}else{this.errmsg=this.errhdr+this.errls;if(this.errlm==FormValidator.LIST_BULLET)this.errmsg+='<ul>';for(i=0;i<this.fail.length;i++)this.errmsg+=(this.errlm==FormValidator.LIST_BULLET?'<li>'+this.fail[i]+'</li>':this.fail[i]+this.errnl);if(this.errlm==FormValidator.LIST_BULLET)this.errmsg+='</ul>';this.errmsg+=this.errls;}this.showErrors();if(fw!=null)requestFocus(this.frm,fw);return false;}else{this.updateDisabledCheckboxes();this.clearErrors();return true;}};FormValidator.prototype.showErrors=function(){if(this.errmode==1){alert(this.errmsg);}else{var d=document.getElementById(this.errdiv);writeToDiv(d,true,true,this.errmsg);if(d.style.display=="none")d.style.display="block";var pos=getAbsolutePos(d);window.scrollTo(0,pos.y);}};FormValidator.prototype.clearErrors=function(){if(this.errmode==2){var d=document.getElementById(this.errdiv);writeToDiv(d,true,true,"");if(d.style.display=="block")d.style.display="none";}};FormValidator.prototype.compareValues=function(src,trg,op,datatype,comptype){var left,right,srcval,trgval,r;srcval=getFormFieldValue(this.frm,src);if(comptype==FormValidator.FIELD_FIELD){trgval=getFormFieldValue(this.frm,trg);if(trim(String(srcval))==""||trim(String(trgval))=="")return true;}else if(comptype==FormValidator.FIELD_VALUE){trgval=trg;if(trim(String(srcval))=="")return true;}if(datatype=="INTEGER"){left="parseInt(srcval)";right="parseInt(trgval)";}else if(datatype=="FLOAT"){left="parseFloat(srcval)";right="parseFloat(trgval)";}else if(datatype=="DATE"){left="dateToDays(srcval)";right="dateToDays(trgval)";}else{left="srcval";right="trgval";}switch(op){case'EQ':op=' == ';break;case'NEQ':op=' != ';break;case'GT':op=' > ';break;case'LT':op=' < ';break;case'GOET':op=' >= ';break;case'LOET':op=' <= ';break;default:op=' == ';}eval("r = ("+left+op+right+");");return r;};FormValidator.prototype.getErrorMessage=function(op,source,target,comptype){switch(op){case'EQ':return(comptype==FormValidator.FIELD_VALUE?stringReplace(formFieldValueEq,source,target):stringReplace(formFieldsEq,source,target));case'NEQ':return(comptype==FormValidator.FIELD_VALUE?stringReplace(formFieldValueNeq,source,target):stringReplace(formFieldsNeq,source,target));case'GT':return(comptype==FormValidator.FIELD_VALUE?stringReplace(formFieldValueGt,source,target):stringReplace(formFieldsGt,source,target));case'LT':return(comptype==FormValidator.FIELD_VALUE?stringReplace(formFieldValueLt,source,target):stringReplace(formFieldsLt,source,target));case'GOET':return(comptype==FormValidator.FIELD_VALUE?stringReplace(formFieldValueGoet,source,target):stringReplace(formFieldsGoet,source,target));case'LOET':return(comptype==FormValidator.FIELD_VALUE?stringReplace(formFieldValueLoet,source,target):stringReplace(formFieldsLoet,source,target));default:return"";}};FormValidator.prototype.updateEditorValue=function(){if(this.editor!=null)eval("document."+this.frm+".elements['"+this.editor+"'].value = ("+this.editor+"_instance.isEmpty() ? '' : "+this.editor+"_instance.getHtml());");};FormValidator.prototype.updateDisabledCheckboxes=function(){var f,h,e=null;f=getFormObj(this.frm);for(var i=0,s=f.elements.length;i<s;i++){e=f.elements[i];if(e.type=='checkbox'&&e.disabled){h=document.getElementById('V_'+e.name);if(h!=null)h.disabled=true;}}};