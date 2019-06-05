<?php
        //this file collects store penetration data for selected wines
      set_time_limit(1200);
        require_once('config/config.php');
        import('Form60.bll.bllSalesAnalysisData');
        
        
        $anaData = & new salesAnalysisDate;
        
   //     $anaData-> generateCustomersSales(6,2014,1,1);

       $users = $anaData-> getUsers(2014,2,2);

        
        foreach ($users as $user)
        {
			$user_id =$user["user_id"];
			
			if (!$anaData-> generateCustomersSales($user_id,2014,2,2))
            	echo "wrong";
        	else
        	{
        		$msg = "Data import for $user_id!";
				echo $msg;
        		
        	}
        	
		}
       
    /*    if (!$anaData-> generateCustomersSales(6,2013,11,1))
            echo "wrong";
        else
        	echo ("Report created!");
      */  	
      
        	
        
        	
?>
