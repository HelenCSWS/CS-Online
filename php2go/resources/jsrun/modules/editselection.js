function EditSelection(formName,srcField,trgField,addedfield,removedfield,countfield){this.formName=formName;this.sourceFieldName=srcField;this.targetFieldName=trgField;this.addedFieldName=addedfield;this.removedFieldName=removedfield;this.countFieldName=countfield;this.source=null;this.target=null;this.added=null;this.addedStr="";this.removed=null;this.removedStr="";this.hiddenFieldsReload=false;this.count=null;this.pre="";this.preCount=0;this.getElement=function(fieldName){return eval("document."+this.formName+".elements['"+fieldName+"']");};this.onReset=function(){this.addedStr=this.added.value;this.removedStr=this.removed.value;};this.inString=function(needle,haystack){haystack='#'+haystack+'#';return(haystack.indexOf('#'+needle+'#'));}this.initialize();}EditSelection.prototype.initialize=function(){this.source=this.getElement(this.sourceFieldName);this.target=this.getElement(this.targetFieldName);this.count=document.getElementById(this.countFieldName);this.added=this.getElement(this.addedFieldName);this.removed=this.getElement(this.removedFieldName);this.count.innerHTML=this.target.options.length-1;for(var i=1;i<this.target.options.length;i++){if(this.pre.length>0){this.pre=this.pre+'#'+this.target.options[i].value;}else{this.pre=this.target.options[i].value;}this.preCount++;}};EditSelection.prototype.add=function(){if(!this.hiddenFieldsReload){this.added.value="";this.removed.value="";this.hiddenFieldsReload=true;}if(this.added.value==""&&this.addedStr!=""){this.added.value=this.addedStr;this.addedStr="";}if(!this.source||!this.target){return false;}else{if(!isEmpty(this.formName,this.sourceFieldName)){if((this.inString(this.source.value,this.added.value)==-1)&&(this.inString(this.source.value,this.pre)==-1||this.inString(this.source.value,this.removed.value)!=-1)){this.target.options[this.target.options.length]=new Option(this.source.value,this.source.value);this.addMark(this.source.value);this.count.innerHTML=this.target.length-1;}else{alert(insValueMsg);this.source.focus();}}return true;}};EditSelection.prototype.remove=function(){if(!this.hiddenFieldsReload){this.added.value="";this.removed.value="";this.hiddenFieldsReload=true;}if(this.removed.value==""&&this.removedStr!=""){this.removed.value=this.removedStr;this.removedStr="";}var del=0;if(!this.target){return false;}else{var cont=0;for(var z=0;z<this.target.length;z++){if((this.target.options[z].selected==true)&&(z!=0)){if(this.target.length>1){var i=z;var j=z;while((i<=this.target.length-2)&&(this.target.options[i].selected==true)){this.removeMark(this.target.options[i].value);del++;cont++;i++;}if(cont>0){for(i=j;i<this.target.length-cont;i++){this.target.options[i].value=this.target.options[i+cont].value;this.target.options[i].text=this.target.options[i+cont].text;this.target.options[i].selected=this.target.options[i+cont].selected;}this.target.length=this.target.length-cont;}cont=0;}}}if(this.target.length>1&&this.target.options[this.target.length-1].selected==true){del++;this.removeMark(this.target.options[this.target.length-1].value);this.target.options[this.target.length-1].value=-1;this.target.options[this.target.length-1].text="                          ";if((this.target.length)>1){this.target.length--;}}this.count.innerHTML=this.target.length-1;}};EditSelection.prototype.removeAll=function(){if(!this.hiddenFieldsReload){this.added.value="";this.removed.value="";this.hiddenFieldsReload=true;}if(this.removed.value==""&&this.removedStr!=""){this.removed.value=this.removedStr;this.removedStr="";}var del=0;if(!this.target){return false;}else{if(this.target.options.length>100){alert(selRemAllMsg);}for(var i=1;i<this.target.options.length;i++){this.removeMark(this.target.options[i].value);del++;}this.target.options.length=1;this.count.innerHTML=this.target.length-1;return true;}};EditSelection.prototype.addMark=function(optValue){var pos=this.inString(optValue,this.removed.value);if(pos!=-1){this.removed.value=this.removed.value.substr(0,pos-1)+this.removed.value.substr(pos+optValue.length+1);}else{if(this.added.value.length>0){this.added.value=this.added.value+'#'+optValue;}else{this.added.value=optValue;}}};EditSelection.prototype.removeMark=function(optValue){var in_add=this.inString(optValue,this.added.value);var in_pre=this.inString(optValue,this.pre);if(in_add!=-1&&in_pre==-1){this.added.value=this.added.value.substr(0,in_add-1)+this.added.value.substr(in_add+optValue.length+1);}else{if(this.removed.value.length>0){this.removed.value=this.removed.value+"#"+optValue;}else{this.removed.value=optValue;}}};