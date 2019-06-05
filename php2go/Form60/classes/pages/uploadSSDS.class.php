<?php

import('Form60.base.F60FormBase');
import('Form60.bll.bllSSDSData');
import('Form60.util.F60Date');

class uploadSSDS extends F60FormBase
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
	
    function uploadSSDS()
    {
   
			if($_REQUEST["province_id"]!="")
			{
				$this->province_id= $_REQUEST["province_id"];
				
			}
	     if (F60FormBase::getCached()) exit(0);
    
        if(isset($_POST['SSDS_step']))	//form submitted
        {
            $this->step = $_REQUEST['SSDS_step'];
        }
        else
        {
            $this->step = 1;
        }
   
   	
        switch ($this->step)
        {
            case 1: 
                $stepTemplate = 'uploadSSDS_step1.tpl';
                break;
            case 2: 
                $stepTemplate = 'uploadSSDS_step2.tpl';
                break;
            case 3: 
                $stepTemplate = 'uploadSSDS_step3.tpl';
                break;
        }
                
        F60FormBase::F60FormBase('uploadSSDS', 'Data upload', 'uploadSSDS.xml', $stepTemplate);
        $this->addScript('resources/js/javascript.uploadSSDS.js');
       

        import('Form60.base.F60PageStack');

        F60PageStack::addtoPageStack();
                           
		  if($this->province_id==2)       
		  {		  
				$this->registerActionhandler(array("bttnStart", array($this, uploadFile), "URL", "main.php?page_name=uploadSSDS&province_id=2"));
				$this->registerActionhandler(array("bttnUpload", array($this, importData), "URL", "main.php?page_name=uploadSSDS&province_id=2"));
				
				$this->registerActionhandler(array("btnBack", array($this, startOver), "SELF", NULL));
			}                                                                                    
		  else
		  { 
	        $this->registerActionhandler(array("bttnStart", array($this, uploadFile), "URL", "main.php?page_name=uploadSSDS"));
	        $this->registerActionhandler(array("bttnUpload", array($this, importData), "URL", "main.php?page_name=uploadSSDS"));
	        $this->registerActionhandler(array("btnBack", array($this, startOver), "SELF", NULL));
        }

        $form = & $this->getForm();
        $form->setButtonStyle('btnOK');
        $form->setInputStyle('input');
        $form->setLabelStyle('label');
        
        $edtProvince=& $form->getField("province_id");
        $edtProvince->setValue($this->province_id);
  
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
			$cmbProvince = & $form->getField("province_id");
			if($cmbProvince->getValue()==1)
			{
				//print here;
				return $this->importBcData();
			}
			else
			{
			 //print ab;
				return $this->importAbData();
			}
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
			
			$SSDSData = new SSDSData();

            $SSDSData->setSales_UploadedFile($this->uploadfile,"");
                    
            $SSDSData->setUserID($this->getCurrentUserID());
            
            //check and read content
            if (!$SSDSData->isValidAbFile(false)) //licensee's
            {
                $bRet = false;
            }
            else
            {
		     	
	                //we are here means succes, go to step 2
	                
	                //Add for monthly data
	                $form = & $this->getForm(); 
	                $form->Template->assign("year", ($SSDSData->sale_year));
	                $form->Template->assign("month", (F60Date::getMonthTxt($SSDSData->sale_month)));
	                $form->Template->assign("ab_SSDS_file_name", ($this->file_name));
	               /// $form->Template->assign("bcldb_SSDS_file_name", ($this->bcldb_file_name));
	                
	                $file_field = & $form->getField("ab_file_name");
	                $file_field->setRequired(False);
	                
	               // $file_field = & $form->getField("bcldb_file_name");
	              //  $file_field->setRequired(False);
	                
	              	$uploaded_file = & $form->getField("uploaded_file");
	                $uploaded_file->setValue($SSDSData->uploadfile);//$this->uploadfile
	                
	               // $bcldb_uploaded_file = & $form->getField("bcldb_uploaded_file");
	               // $bcldb_uploaded_file->setValue($SSDSData->bcldb_uploadfile);//$this->bcldb_uploadfile
	                
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
	            $form->switchTemplate("uploadSSDS_step1.tpl");
	            $form->Template->assign("file_format_error", $SSDSData->file_format_error);
	        }
	      
    
            return false;
    }
    function uploadFile()
    {
     
    // print what;
     
     	$form = & $this->getForm();
     	$edtProvince = & $form->getField("province_id");
     	
     //	print $edtProvince->getValue();
     	if( $edtProvince->getValue()==1)
     	{
			$this->uploadBcFile();
		}
		else
		{
			$this->uploadAbFile();
		}
     	
     	
		
	 }
    function uploadBcFile()
    {
     	
		
		
        $bRet = true;
   
        
            $this->file_name = basename($_FILES['file_name']['name']);
            
  			$this->bcldb_file_name = basename($_FILES['bcldb_file_name']['name']);	
  			
          //move/rename the file
            $this->uploaddir = ROOT_PATH . "upload";
            $oldumask = umask(0);
            if (!file_exists($this->uploaddir))
                mkdir($this->uploaddir, 0777);
            $this->uploaddir = $this->uploaddir . "/" . md5(time());
            mkdir($this->uploaddir, 0777);
            umask($oldumask);
            
			$this->uploadfile = $this->uploaddir . "/" . $this->file_name;
			$this->bcldb_uploadfile = $this->uploaddir . "/" . $this->bcldb_file_name;
            
			move_uploaded_file($_FILES['file_name']['tmp_name'], $this->uploadfile);
	        
			move_uploaded_file($_FILES['bcldb_file_name']['tmp_name'], $this->bcldb_uploadfile);
            
			chmod($this->uploadfile, 0777);
			
			$SSDSData = new SSDSData();

            $SSDSData->setSales_UploadedFile($this->uploadfile,$this->bcldb_uploadfile);
                    
            $SSDSData->setUserID($this->getCurrentUserID());
            
            //check and read content
            if (!$SSDSData->isValidFile(false)) //licensee's
            {
                $bRet = false;
            }
            else
            {
		     	//check if bcldb files valide
             	 if (!$SSDSData->isValidFile(true)) //bcldb
             	 {
					 $bRet = false;
				 }
				 else
				 {
	                //we are here means succes, go to step 2
	                
	                //Add for monthly data
	                $form = & $this->getForm(); 
	                $form->Template->assign("year", ($SSDSData->sale_year));
	                $form->Template->assign("month", (F60Date::getMonthTxt($SSDSData->sale_month)));
	                $form->Template->assign("SSDS_file_name", ($this->file_name));
	                $form->Template->assign("bcldb_SSDS_file_name", ($this->bcldb_file_name));
	                
	                $file_field = & $form->getField("file_name");
	                $file_field->setRequired(False);
	                
	                $file_field = & $form->getField("bcldb_file_name");
	                $file_field->setRequired(False);
	                
	              	$uploaded_file = & $form->getField("uploaded_file");
	                $uploaded_file->setValue($SSDSData->uploadfile);//$this->uploadfile
	                
	                $bcldb_uploaded_file = & $form->getField("bcldb_uploaded_file");
	                $bcldb_uploaded_file->setValue($SSDSData->bcldb_uploadfile);//$this->bcldb_uploadfile
	                
	            }
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
            $form->switchTemplate("uploadSSDS_step1.tpl");
            $form->Template->assign("file_format_error", $SSDSData->file_format_error);
        }
        else
        {
			
		}
    
            return false;
    }

function importAbData()
    {
        //do the actual import here   
       $bRet = true;        
        
       $dataFile = $_POST["uploaded_file"];
      // $bcldb_dataFile = $_POST["bcldb_uploaded_file"];
              

       $SSDSData = new SSDSData();
       $SSDSData->setSales_DataFile($dataFile,"");
       $SSDSData->setUserID($this->getCurrentUserID());
       if (!$SSDSData->import_ab_monthy_Data())
       {
        	//print heree;
            $bRet = false;
       }
       else
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
        }
        return false;
    }
    
    
    function importBcData()
    {
        //do the actual import here   
       $bRet = true;        
        
       $dataFile = $_POST["uploaded_file"];
       $bcldb_dataFile = $_POST["bcldb_uploaded_file"];
              

       $SSDSData = new SSDSData();
       $SSDSData->setSales_DataFile($dataFile,$bcldb_dataFile);
       $SSDSData->setUserID($this->getCurrentUserID());
       if (!$SSDSData->import_salesData())
       {
            $bRet = false;
       }
       else
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
            
          //   $edtMonth = & $form->getField("test1");
//           $edtMonth ->setValue(2);
        	//	$edtMonth ->setValue($SSDSData->sale_month);
        
        
            $form->switchTemplate("uploadSSDS_step1.tpl");
            $form->Template->assign("file_format_error", $SSDSData->file_format_error);
        }
        return false;
    }
    
    function startOver()
    {
        //render the form from start
        return true;
    }
}
?>
