<?php
        //this file collects store penetration data for selected wines
       //set_time_limit(1200);
        require_once('config/config.php');
        import('Form60.bll.bllStorePenetrationData');
        
        
        $storePenData = & new bllStorePenetrationData;
        
       /*  $fp = fopen("logs/Ajax_logfile.log","a");

		fputs($fp,  "step1"."\n");
		fclose($fp);*/

        if (!$storePenData->collectData(true))
            echo $storePenData->errorMessage;
        
        
?>
