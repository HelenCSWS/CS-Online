<?php
        //this file collects store penetration data for selected wines
        set_time_limit(1200);
        require_once('config/config.php');
        import('Form60.bll.bllf60Reports');
        
      /*  $fp = fopen("logs/Ajax_logfile.log","a");

		fputs($fp,  "step1"."\n");
		fclose($fp);*/
		
        $ccInfo = & new F60ReportsData;
	
		$ccInfo->sendCCEmail(0);
		
        //if (!$storePenData->collectData(false))
            
        
        
?>
