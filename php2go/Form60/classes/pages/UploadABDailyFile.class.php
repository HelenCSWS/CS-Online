<?php

import('Form60.base.F60FormBase');
import('Form60.bll.bllABReports');
import('Form60.util.F60Date');

class uploadABDailyFile extends F60FormBase
{
    var $step;
    var $file_name;
    var $uploaddir;
    
	var $bcldb_file_name;
    var $bcldb_uploaddir;
    
    var $uploadfile;
    var $bcldb_uploadfile;
    var $file_format_error = "";

	var $province_id=1;
	
    function uploadABDailyFile()
    {
   
		
	     if (F60FormBase::getCached()) exit(0);
    
        if(isset($_POST['upload_step']))	//form submitted
        {
            $this->step = $_REQUEST['upload_step'];
        }
        else
        {
         
            $this->step = 1;
        }
   

   	
        switch ($this->step)
        {
            case 1: 
                $stepTemplate = 'uploadABDailyFile_step1.tpl';
                break;
            case 2: 
                $stepTemplate = 'uploadABDailyFile_step2.tpl';
                break;
            case 3: 
                $stepTemplate = 'uploadABDailyFile_step3.tpl';
                break;
        }
                
        F60FormBase::F60FormBase('uploadABDailyFile', 'Data upload', 'uploadABDailyFile.xml', $stepTemplate);
        $this->addScript('resources/js/javascript.ABReports.js');
       

        import('Form60.base.F60PageStack');

        F60PageStack::addtoPageStack();
                           
		 	  
		$this->registerActionhandler(array("bttnStart", array($this, uploadFile), "URL", "main.php?page_name=uploadABDailyFile"));
		$this->registerActionhandler(array("bttnUpload", array($this, importData), "URL", "main.php?page_name=uploadABDailyFile"));
				
		$this->registerActionhandler(array("btnBack", array($this, startOver), "SELF", NULL));
		

        $form = & $this->getForm();
        $form->setButtonStyle('btnOK');
        $form->setInputStyle('input');
        $form->setLabelStyle('label');
        
    
        $form->setFormAction($_SERVER["REQUEST_URI"]);
        
        $this->attachBodyEvent('onLoad', 'setFristFocus();');
        set_time_limit(300);
        
     }

    function display()
    {
        if (!$this->handlePost())
            $this->displayForm();
    }

    function displayForm()
    {    
    	$form = & $this->getForm();
      F60FormBase::display();
    }
    
    function importData()
    {
			$form = & $this->getForm();
	
	
			return $this->importAbData();
		
	 }	
	 
	function uploadFile()
    {
     
  
  
     
     	$form = & $this->getForm();
     	
     
     	 
     
     	 
   		$this->uploadAbFile();
	
     	
     	
		
	 }
   


	function uploadAbFile()
    {	
     
         $bRet = true;
   
        
            $this->file_name = basename($_FILES['ab_file_name']['name']);
          //move/rename the file
            $this->uploaddir = ROOT_PATH . "upload";
            $oldumask = umask(0);
            if (!file_exists($this->uploaddir))
                mkdir($this->uploaddir, 0777);
            $this->uploaddir = $this->uploaddir . "/" . md5(time());
            mkdir($this->uploaddir, 0777);
            umask($oldumask);
            
			$this->uploadfile = $this->uploaddir . "/" . $this->file_name;
			
            
			move_uploaded_file($_FILES['ab_file_name']['tmp_name'], $this->uploadfile);
	        
		
            
			chmod($this->uploadfile, 0777);
			
			$reportData = new ABReportData();

            $reportData->setDaily_UploadedFile($this->uploadfile,"");
                    
         
             	
	                //we are here means succes, go to step 2
	           if (!$reportData->isValidFile()) 
            {
                $bRet = false;
            }
            else
            {      
	           
	                $form = & $this->getForm(); 
	                
	                $file_field = & $form->getField("ab_file_name");
	                $file_field->setRequired(False);
	                
	                 
	              	$uploaded_file = & $form->getField("uploaded_file");
	                $uploaded_file->setValue($reportData->uploadfile);//$this->uploadfile
	                
	                
	     }
            
       
        
	        if (!$bRet)
	        {
	            //delete uploaded files
	         //  $reportData->deleteUploadDir();
			
	            //go back to step 1
	           $form = & $this->getForm();
	            $this->step = 1;
	            $SSDS_step = & $form->getField("upload_step");
	            $SSDS_step->setValue($this->step);
	            $form->switchTemplate("uploadABDailyFile_step1.tpl");
	            $form->Template->assign("file_format_error", $reportData->file_format_error);
	        }
	      
    
            return false;
    }
  

function importAbData()
    {
     
    
   
        //do the actual import here   
      $bRet = true;        
        
       $dataFile = $_POST["uploaded_file"];
      // $bcldb_dataFile = $_POST["bcldb_uploaded_file"];
          

    	$reportData = new ABReportData();
      	
      
  
       if (!$reportData->import_Data($dataFile))
       {
        	//print heree;
        	

            $bRet = false;
       }
       /*  else
       {
        
            //we are here means succes, go to step 3
            $form = & $this->getForm();
            $form->Template->assign("sale_year_desc",$SSDSData->sale_year);
            $form->Template->assign("sale_month_desc", (F60Date::getMonthTxt($SSDSData->sale_month)));
            $form->Template->assign("SSDS_file_name", $SSDSData->baseFileName);
            
            $message="";
            if (strlen($SSDSData->missingCustomers)>0 || strlen($SSDSData->missingWines)>0)
            {
                if (strlen($SSDSData->missingCustomers)>0)
                    $message =  "The following customer numbers could not  be found in the database: </BR> $SSDSData->missingCustomers </BR></BR>";
                
                if (strlen($SSDSData->missingWines)>0)
                    $message .=  "The following SKU's could not be found in the database: </BR> $SSDSData->missingWines";
                    
                $form->Template->assign("miss_data", $message);
            }
                       
            $SSDSData->deleteUploadDir(); //clean up
            
           $edtMonth = & $form->getField("sale_month");
//           $edtMonth ->setValue(2);
        $edtMonth ->setValue($SSDSData->sale_month);
                        
            $edtYear = & $form->getField("sale_year");
            $edtYear ->setValue($SSDSData->sale_year);
            
            
        }
        
        if (!$bRet)
        {
            //delete uploaded files
            $SSDSData->deleteUploadDir();

            //go back to step 1
            $form = & $this->getForm();
            $this->step = 1;
            $SSDS_step = & $form->getField("SSDS_step");
            $SSDS_step->setValue($this->step);
            
           //  $edtMonth = & $form->getField("test1");
//           $edtMonth ->setValue(2);
           //  $edtMonth ->setValue($SSDSData->sale_month);
        
        
            $form->switchTemplate("uploadSSDS_step1.tpl");
            $form->Template->assign("file_format_error", $SSDSData->file_format_error);
        }*/
        
        return false;
    }
    
    
  
    function startOver()
    {
        //render the form from start
        return true;
    }
}
?>
