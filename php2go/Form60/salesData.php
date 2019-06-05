<?php
        //this file collects store penetration data for selected wines
      set_time_limit(1200);
        require_once('config/config.php');
        import('Form60.bll.bllTestReports');
        
        
        $venderData = & new bllABVenderData;
        
        if (!$venderData->emailReport())
            echo $venderData->errorMessage;
        else
        	echo ("Report created!");
        	
      
        	
        
        	
?>
