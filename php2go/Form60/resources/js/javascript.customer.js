function selectHKSubType()
{
    var ht = 500;
    var wd = 750;


    var types = document.getElementById("hk_rank_types").value;
    var customer_id=document.getElementById("customer_id").value;
    
    
	var link="main.php?page_name=selectHKSubType&types="+types+"&customer_id="+customer_id;
	
	var left=(screen.availWidth-wd)/2;
	
	var top=(screen.availHeight-ht)/2;		

    var printWindow = window.open(link, "Form60", "menubar=yes,scrollbars=yes, resizable=no, left="+left+",top="+top+",height=" + ht + ",width=" + wd);
}

function refreshCustomerPage()
{	
	history.go(0);
}