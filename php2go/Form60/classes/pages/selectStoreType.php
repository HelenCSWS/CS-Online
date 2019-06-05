<?php

/**
 * Perform the necessary imports
 */

import('Form60.base.F60FormBase');
import('Form60.util.F60Date');
import('php2go.util.Spreadsheet');
import('php2go.file.ZipFile');
require_once('Excel/reader.php');

class selectStoreType extends F60FormBase
{
		//steps:
		// 1 - upload file
		// 2 - received file
		var $step;
		var $file_format;
		var $file_name;
		var $uploaddir;
		var $uploadfile;
		var $session_id;
		var $errmessage;
		//used in save functions
		var $user_id;
		var $now;
		var $flags = 0;
		var $file_type = '';
		//columns of uploaded_customers table


		function selectStoreType()
		{
			if (F60FormBase::getCached()) exit(0);

			F60FormBase::F60FormBase('selectPeriod', 'Sales summary report', 'selectStoreType.xml', 'selectStoreType.tpl');
			$this->addScript('resources/js/javascript.ssdsreports.js');
			$form = & $this->getForm();
			$this->form->setButtonStyle('btnOK');
			$this->form->setInputStyle('input');
			$this->form->setLabelStyle('label');

		/*	$cntl = & $form->getField('customercompare.errmsg');
			$cntl->setStyle("hiddenInput");
			$cntl->setValue('&nbsp;');*/

            $step_id =$_REQUEST["step_id"];
			if($step_id=="" or $step_id==0)
			{
                $sURL = "main.php?page_name=uploadSSDS&step_id=1";
            }
            else if( $step_id ==1)
            {
                   $sURL = "main.php?page_name=uploadSSDS&step_id=2";
            }

            $this->registerActionhandler(array("btnAdd", array($this, processForm), "URL", $sURL));

			$this->registerActionhandler(array("btnCancel", array($this, processForm), "URL", "main.php"));

			$action = array("Print help" => "javascript:printHelp();",);



                $cntl = & $form->getField("period_desc");
        		$cntl->setStyle("text");


           $this->setActions($action);
           $step_id++;
			//$this->attachBodyEvent('onLoad', 'setFristFocus();');



		}

    function getfiscalYear()
    {
        $sfiscalYear = "Apr 01,2006 - Mar 31,2007";
       return $sfiscalYear;
    }

    function getPeriod()
    {
        $sPeriod="Period 9: Nov 26,2007 - Dec 30,2007";
       return $sPeriod;
    }


	function display()
	{
		if (!$this->handlePost())
			$this->displayForm();
	}

	function displayForm()
	{
/*        if ($this->step==1)
            {
               $action = array(

    	            "Print help" => "javascript:printHelp();",
            	);

	            $this->setActions($action);
            }
*/		F60FormBase::display();
	}

	function processForm()
	{
		return true;
	}



	function isValidFile(& $data)
	{
		if(!array_key_exists($this->file_format, $this->file_formats)) {
			$this->errmessage = "Unsupported file format.";
			return false;
		}
		if($this->file_type == 'xls')
		    $nCol = $data->sheets[0]['numCols'];
		else if($this->file_type == 'csv')
		{
			$data = $this->readCSVTitle();
			if(!$data) {
				$this->errmessage = "Unable to read CSV file: " . $this->uploadfile;
				return false;
			}
		    $nCol = count($data);
		}
		if($nCol != $this->file_formats[$this->file_format][2]) {
			$this->errmessage = $this->file_formats[$this->file_format][0] . " files should have " .  $this->file_formats[$this->file_format][2] . " columns.";
			return false;
		}
		foreach($this->file_formats[$this->file_format][3] as $key=>$col) {
			if($this->file_type == 'xls')
			    $ColName = $data->sheets[0]['cells'][1][$key];
			else if($this->file_type == 'csv')
			    $ColName = $data[$key-1];
			if(strtoupper ($ColName) != strtoupper ($col)) {
				$this->errmessage = "Column #" . $key . " should be '" . $col . "'.";
				return false;
			}
		}
		return true;
	}






}

?>
