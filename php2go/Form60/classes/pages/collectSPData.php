<?php
        //this file collects store penetration data for selected wines
        set_time_limit(1200);
        require_once('config/config.php');
        import('Form60.bll.bllStorePenetrationData');
        
      /*  $fp = fopen("logs/Ajax_logfile.log","a");

		fputs($fp,  "step1"."\n");
		fclose($fp);*/
		
        $storePenData = & new bllStorePenetrationData;
	

        if (!$storePenData->collectData(false))
            echo $storePenData->errorMessage;
        
        
?>
