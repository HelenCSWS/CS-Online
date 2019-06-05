function testFunction() {
	alert('It worked!!!');
}

function jsrsTestReturn(returnString) {
	alert(returnString);
}

function jsrsTest2Return(returnString) {	
	var lkp = document.getElementById('lookup_field');
	lkp.options.length = 1;
	createOptionsFromString(returnString, 'myForm', 'lookup_field', '|', '~', 1);
	lkp.options[1].selected = true;
}