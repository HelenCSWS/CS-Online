<?php
        //this file collects store penetration data for selected wines
      	set_time_limit(1200);
        require_once('config/config.php');
        import('Form60.bll.bllSalesAnalysisData');
        
        
        $anaData = & new salesAnalysisData;
        
        $current_year = date(Y);
        $current_month =date(m);
        
        ;
     	if($current_month ==1)
     	{
     		$report_month = 12;
     		$report_year = $current_year-1;
       	}
		else
		{
			$report_month = $current_month-1;
			$report_year =$current_year;
		}
     	
     	//0 to every one
     	//1 to chris only
		
        $province_id =1;
    
    
      //  $isToCP =0; // to every one
	//	$anaData-> emailAnaReport($report_year,$report_month,$province_id,$isToCP);
		
	//	$isToCP =1; // to CP only
	//	$anaData-> emailAnaReport($report_year,$report_month,$province_id,$isToCP);
    
        $isToCP =0; // to every one
		$retVal = $anaData-> emailAnaReport($report_year,$report_month,$province_id,$isToCP);
		
        if($retVal)
        {
		    $isToCP =1; // to CP only with all reports combined
		    $anaData-> emailAnaReport($report_year,$report_month,$province_id,$isToCP);
        }
    	
?>
