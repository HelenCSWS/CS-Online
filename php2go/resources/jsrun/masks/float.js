function chkMaskFLOAT(f,e,l,r){var keyCode=(_dom==1||_dom==2?window.event.keyCode:e.which);var key=String.fromCharCode(keyCode);var isKey=(String('0123456789.').indexOf(key)!=-1);var isAction=(String('0,8,13').indexOf(keyCode)!=-1||e.ctrlKey);var len=f.value.length;var dpos=f.value.indexOf('.');var ss=getEditCaretPos(f);var se=getSelectionEnd(f);var nr=(isKey&&key!='.'&&key!=',');var dot=(key=='.');if(!isKey&&!isAction){return false;}else if(nr){if(key=='0'&&ss==0&&len==0){f.value='0.';setEditCaretPos(f,2);stopEvent(e);return true;}else if(l!=null&&dpos==-1&&len==l){if(ss==se&&ss<len){f.value=f.value.substring(0,ss)+key+f.value.substring(ss,l-1)+'.'+f.value.substring(l-1);setEditCaretPos(f,ss+1);stopEvent(e);return true;}else if(ss<se){f.value=f.value.substring(0,ss)+key+f.value.substring(se);setEditCaretPos(f,ss+1);stopEvent(e);return true;}else{f.value=f.value+'.';return true;}}else if(l!=null&&dpos!=-1&&f.value.substring(0,dpos).length==l){if(ss==se){if(ss<=dpos)return false;if(r!=null&&f.value.substring(dpos+1).length==r)return false;if(ss<len){f.value=f.value.substring(0,ss)+key+f.value.substring(ss);setEditCaretPos(f,ss+1);stopEvent(e);}return true;}else{if(se<=dpos||ss>dpos||(ss+(len-se)<l)){f.value=f.value.substring(0,ss)+key+f.value.substring(se);setEditCaretPos(f,ss+1);stopEvent(e);return true;}return false;}}else if(r!=null&&dpos!=-1&&f.value.substring(dpos+1).length==r){if(ss==se){if(ss>dpos)return false;if(l!=null&&f.value.substring(0,dpos).length==l)return false;f.value=f.value.substring(0,ss)+key+f.value.substring(ss);setEditCaretPos(f,ss+1);stopEvent(e);return true;}else{if(se<=dpos||ss>dpos||(ss+(len-se)<l)){f.value=f.value.substring(0,ss)+key+f.value.substring(se);setEditCaretPos(f,ss+1);stopEvent(e);return true;}return false;}}else return true;}else if(dot){if(ss==0&&(dpos==-1||(dpos>=ss&&dpos<se))){f.value=(se>ss?'0.'+f.value.substring(se):'0.'+f.value);setEditCaretPos(f,2);stopEvent(e);return true;}else{return((dpos==-1&&(l==null||ss<=l))||(dpos>=ss&&dpos<se));}}else{return true;}}function chkFLOAT(field,llen,rlen){var floatValue=parseFloat(field.value);if(field.value.length==0){return true;}else if(floatValue=="0"||floatValue){field.value=floatValue;if(llen!=null&&rlen!=null){var regExp=new RegExp("^\\d{1,"+llen+"}(\\.\\d{1,"+rlen+"})?$");if(!regExp.test(field.value)){return false;}}return true;}else{return false;}}