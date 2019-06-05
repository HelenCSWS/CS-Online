<?php

/**
 * Perform the necessary imports
 */

import('Form60.base.F60FormBase');
import('Form60.util.F60Date');
import('php2go.util.Spreadsheet');
import('php2go.file.ZipFile');
require_once('Excel/reader.php');

class customerCompare extends F60FormBase
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
		var $columns = array('row_id', 'license_number', 'customer_name', 'address', 'po_box', 'city', 'postalcode',
						 'phone', 'total_seats', 'estab_type', 'fax', 'contact_name', 'contact_name_2', 'province', 'store_type_id');
		//for each format: name, default_store_type_id, #of columns, column names
		var $file_formats = array (
						1 => array("BC LRS List", 1, 8, array(
												1=>'externalfilenum',
												2=>'establishmentname',
												3=>'address1',
												4=>'city',
												5=>'postalcode',
												6=>'phonenumber',
												7=>'issuedate',
												8=>'faxnumber'
											)
									),
						3 => array("BC Licensee List", 3, 11, array(
												1=>'License #',
												2=>'Name',
												3=>'Address 1',
												4=>'Address 2',
												5=>'City',
												6=>'Postal Code',
												7=>'Phone',
												8=>'Total Seats',
												9=>'Estab Type',
												10=>'Fax Number',
												11=>'Contact Name'
											)
								),
						4 => array("Alberta Liquor Stores", 7, 9, array(
												1=>'license#',
												2=>'name',
												3=>'phone',
												4=>'fax',
												5=>'address1',
												6=>'address2',
												7=>'address3',
												8=>'city',
												9=>'postal code'
											)
								),
						5 => array("Alberta Licensee List", 8, 10, array(
												1=>'License #',
												2=>'Name',
												3=>'Phone',
												4=>'Fax',
												5=>'Address1',
												6=>'Address2',
												7=>'address3',
												8=>'City',
												9=>'Province',
												10=>'Postal Code',
											)
								)
					);

		function customerCompare()
		{
			if (F60FormBase::getCached()) exit(0);

			if(isset($_REQUEST['step']))	//form submitted
				$this->step = $_REQUEST['step'];
			else
				$this->step = 0;
			$this->step++;
//			echo $this->step;

			switch($this->step) {
			case 1:	//select and upload file
				F60FormBase::F60FormBase('customerCompare', 'Market update', 'custcompare1.xml', 'custcompare1.tpl');
				$this->addScript('resources/js/javascript.customercompare.js');
				$form = & $this->getForm();

				$this->form->setButtonStyle('btnOK');
				$this->form->setInputStyle('input');
				$this->form->setLabelStyle('label');

				$cntl = & $form->getField('customercompare.errmsg');
				$cntl->setStyle("hiddenInput");
				$cntl->setValue('&nbsp;');
				$this->attachBodyEvent('onLoad', 'setFristFocus();');
				$this->registerActionhandler(array("btnCancel", array($this, processForm), "URL", "main.php"));

				$action = array("Print help" => "javascript:printHelp();",);
	            $this->setActions($action);
				//  print thisww;
					break;
			case 2:	//save file, show file info, process customers
				$this->writelog("stage 2 start time: " . date('r', time()));
				$this->file_format = $_REQUEST['file_format'];
				$this->file_name = basename($_FILES['file_name']['name']);
				//
				$this->cleanTempTables();
				//createNewSession
				$this->createNewSession();
				//move/rename the file
				$this->uploaddir = ROOT_PATH . "upload/" . $this->session_id;
				$oldumask = umask(0);
				mkdir($this->uploaddir, 0777);
				umask($oldumask);
				$this->uploadfile = $this->uploaddir . "/" . $this->file_name;
				move_uploaded_file($_FILES['file_name']['tmp_name'], $this->uploadfile);
				chmod($this->uploadfile, 0777);
				$this->unzipFile();
				$this->updateSession(2);
				//check and read content
				set_time_limit(300);
				$bValid = true;
				if(strtolower(substr($this->uploadfile, -4) == '.xls'))
				{
					$this->file_type = 'xls';
					$data = new Spreadsheet_Excel_Reader();
					$data->setOutputEncoding('CP1251');
					$this->writeLog("before read file");
					$data->read($this->uploadfile);
					$this->writeLog("after read file");
					if(!$this->isValidFile(&$data))
						$bValid = false;
					$this->writeLog("after validation");
				}
				else if(strtolower(substr($this->uploadfile, -4) == '.csv'))
				{
					$this->file_type = 'csv';
					$data = 0;
					if(!$this->isValidFile(& $data))
						$bValid = false;
				}
				else
				{
					$bValid = false;
					$this->errmessage = 'Uploaded is not an Excel format file.';
				}
				if(!$bValid)
				{
					F60FormBase::F60FormBase('customerCompare', 'Market update', 'custcompare1.xml', 'custcompare1.tpl');
					$this->addScript('resources/js/javascript.customercompare.js');
					$form = & $this->getForm();

					$this->form->setButtonStyle('btnOK');
					$this->form->setInputStyle('input');
					$this->form->setLabelStyle('label');

					$cntl = & $form->getField('customercompare.errmsg');
					$cntl ->setStyle("hiddenInput");
					$cntl->setValue('Error: ' . $this->errmessage . ' Please try again.');
					$action = array("Print help" => "javascript:printHelp();",);
		            $this->setActions($action);
				}
				else {
					if($this->file_type == 'xls')
						$total = $this->saveFileToDB(&$data);
					else
						$total = $this->saveCSVFileToDB();
					$this->updateSession(3, $total);
					unset($data);
					//
					F60FormBase::F60FormBase('customerCompare', 'Market update', 'custcompare2.xml', 'custcompare2.tpl');
					$this->addScript('resources/js/javascript.customercompare.js');
					$this->form->setButtonStyle('btnOK');
					$this->form->setInputStyle('label');
					$this->form->setLabelStyle('label');
					//update info
					$form = & $this->getForm();
					$cntl = & $form->getField('customercompare.file_format');
					$cntl ->setStyle("hiddenInput");
					$cntl->setValue($this->file_format);
					$cntl = & $form->getField('customercompare.compare_type');
					$cntl->setValue($this->file_formats[$this->file_format][0]);
					$cntl = & $form->getField('customercompare.file_name');
					$cntl->setValue($_FILES['file_name']['name']);
					$cntl = & $form->getField('customercompare.file_size');
					$cntl->setValue(number_format($_FILES['file_name']['size']));
					//$cntl = & $form->getField('customercompare.file_records');
					//$cntl->setValue($data->sheets[0]['numRows']-1);
					$cntl = & $form->getField('customercompare.valid_records');
					$cntl->setValue(number_format($total));
					$cntl = & $form->getField('customercompare.cc_session_id');
					$cntl->setValue($this->session_id);
				}
				unlink($this->uploadfile);
				rmdir($this->uploaddir);
				$this->registerActionhandler(array("btnCancel", array($this, processForm), "URL", "main.php"));
				$this->writelog("stage 2 end time: " . date('r', time()));
				break;
			case 3: //result, use customercpreport.class.php
				//F60FormBase::F60FormBase('customerCompare', 'Customer compare', 'custcompare3.xml', 'custcompare3.tpl');
/*				$action = array(
					"Print" => "javascript:callSubmit('customerCompare','btnPrint');",
					"Export" => "javascript:callSubmit('customerCompare','btnExport');"
				);

				$this->setActions($action);
				$this->showPrint=true;
*/				break;
			case 4: //save results
                set_time_limit(300);
				$this->session_id = $_REQUEST['cc_session_id'];
				$sql = 'select * from uploaded_customers_sessions where session_id = ' . $this->session_id;
				$result = $this->runSQL($sql);
				$row = $result->fetchRow();
				$this->file_format = $row['file_format_id'];
				$this->saveSessionResult();
				$this->updateSession(5);
				//delete exported report (if exists)
				$location = ROOT_PATH . 'reports/report_' . $this->session_id . '.xls';
				if(file_exists($location))
				{
					chmod($location, 0777);
					unlink($location);
				}
 				F60FormBase::F60FormBase('customerCompare', 'Market update', 'custcompare3.xml', 'custcompare3.tpl');
				$form = & $this->getForm();
				$this->form->setButtonStyle('btnOK');
				$this->form->setInputStyle('label');
				$this->form->setLabelStyle('label');

				$cntl = & $form->getField('customercompare.message');
				$cntl->setValue('Changes have been successfully saved to the database.');
				$this->registerActionhandler(array("btnCancel", array($this, processForm), "URL", "main.php"));
				break;
			case 5: //export to excel
                set_time_limit(300);
				$this->session_id = $_REQUEST['cc_session_id'];
				$this->writeLog("before export - session_id=" . $this->session_id);
				$filename = $this->exportToExcel();
				$this->writeLog("after export");
				F60FormBase::F60FormBase('customerCompare', 'Market update', 'custcompare4.xml', 'custcompare4.tpl');

				$form = & $this->getForm();
				$this->form->setButtonStyle('btnOK');
				$this->form->setInputStyle('label');
				$this->form->setLabelStyle('label');

				$cntl = & $form->getField('customercompare.file_name');
				$cntl->setValue('Report has been exported to Excel format, please download it from <a href="' . $filename . '" style="text-decoration: underline;">here</a>');
				$this->registerActionhandler(array("btnCancel", array($this, processForm), "URL", "main.php"));
				break;
			}

/*			$form = & $this->getForm();
			$debug = & $form->getField('customercompare.debug');
			$debug->setValue('step: ' . $this->step);
*/
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

	function readCSVTitle()
	{
		$handle = fopen($this->uploadfile, "r");
		if(!$handle)
			return false;
		$data = fgetcsv($handle, 1000, ",", '"');
		if(!$data) {
			fclose ($handle);
			return false;
		}
		fclose ($handle);
		return $data;
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

	function cleanTempTables()
	{
		$sql = 'delete from uploaded_customers';
		$result = $this->runSQL($sql);
		$sql = 'delete from uploaded_customer_changes';
		$result = $this->runSQL($sql);
		$sql = 'delete from uploaded_customer_changes_oob';
		$result = $this->runSQL($sql);
		$sql = 'delete from uploaded_customer_changes_new';
		$result = $this->runSQL($sql);
	}

	function unzipFile()
	{
		if(strtolower(substr($this->uploadfile, -3)) == "zip")
		{
			$foundname = false;
			$zip =& FileCompress::getInstance('zip');
			$zip->saveExtractedFiles($zip->extractFile($this->uploadfile), 0777, $this->uploaddir);
			$res = opendir($this->uploaddir);
			while($fname = readdir($res)) {
				if(!$foundname && (strtolower(substr($fname, -3)) == "xls" || strtolower(substr($fname, -3)) == "csv"))
				{
					$foundname = $fname;
				}
				//delete useless files (not including the original zip file)
				if($fname != '.' && $fname != '..' && $fname != $foundname && $fname != basename($this->uploadfile))
				{
					chmod($this->uploaddir . "/" . $fname, 0777);
					unlink($this->uploaddir . "/" . $fname);
				}
			}
			if($foundname) {
				chmod($this->uploadfile, 0777);
				unlink($this->uploadfile);
				$this->uploadfile = $this->uploaddir . "/" . $foundname;
				chmod($this->uploadfile, 0777);
			}
		}
	}

	function getCellData(& $row, $j)
	{
		if($this->file_type == 'xls') {
			if(isset($row[$j]))
				return trim($row[$j]);
			else
				return '';
		} else if($this->file_type == 'csv') {
			if(isset($row[$j-1]))
				return trim($row[$j-1]);
			else
				return '';
		} else
		    return '';
	}

	function getColumData(& $row, $col_name)
	{
		if($this->file_format == 1) {   //BC LRS
			switch($col_name)
			{
			case 'license_number':
				$ret = "'" . $this->GetNumber($this->getCellData($row, 1)) . "'";
				break;
			case 'customer_name':
				$ret = "'" . str_replace("'", "''", $this->normalizeString($this->getCellData($row, 2))) . "'";
				break;
			case 'address':
				$ret = "'" . str_replace("'", "''", $this->normalizeString($this->getCellData($row, 3))) . "'";
				break;
			case 'po_box':
				$ret = "''";
				break;
			case 'city':
				$ret = "'" . str_replace("'", "''", $this->normalizeString($this->getCellData($row, 4))) . "'";
				break;
			case 'postalcode':
				$ret = "'" . str_replace("'", "''", $this->getCellData($row, 5)) . "'";
				break;
			case 'phone':
				$ret = "'" . str_replace("'", "''", $this->GetPhoneNumber($this->getCellData($row, 6))) . "'";
				break;
			case 'total_seats':
				//$ret = "''";
				$ret = 0;  ////Updated for database upgrade (MYSQL4 to MYSQL5) , by Helen, OCT 23th 2011
				break;
			case 'estab_type':
				$ret = "''";
				break;
			case 'fax':
				$ret = "'" . str_replace("'", "''", $this->GetPhoneNumber($this->getCellData($row, 8))) . "'";
				break;
			case 'contact_name':
				$ret = "''";
				break;
			case 'contact_name_2':
				$ret = "''";
				break;
			case 'province':
				$ret = "'BC'";
				break;
			case 'store_type_id':
				$ret = "'" . $this->file_formats[$this->file_format][1] . "'";
			    break;
			default:
				$ret = '';
			}
		}
		else if($this->file_format == 3) {  //BC licensee
			switch($col_name)
			{
			case 'license_number':
				$ret = "'" . $this->GetNumber($this->getCellData($row, 1)) . "'";
				break;
			case 'customer_name':
				$ret = "'" . str_replace("'", "''", $this->normalizeString($this->getCellData($row, 2))) . "'";
				break;
			case 'address':
				$a1 = $this->getCellData($row, 3);
				$a2 = $this->getCellData($row, 4);
				if($this->IsPoBox($a1))
					$ret = "'" . str_replace("'", "''", $this->normalizeString($a2)) . "'";
				else if($this->IsPoBox($a2))
					$ret = "'" . str_replace("'", "''", $this->normalizeString($a1)) . "'";
				else
                    $ret = $this->CombineAddresses($a1, $a2);
				break;
			case 'po_box':
			    if($this->IsPoBox($this->getCellData($row, 4)))
					$ret = "'" . str_replace("'", "''", $this->NormalizePoBox($this->getCellData($row, 4))) . "'";
				else if($this->IsPoBox($this->getCellData($row, 3)))
					$ret = "'" . str_replace("'", "''", $this->NormalizePoBox($this->getCellData($row, 3))) . "'";
				else
					$ret = "''";
				break;
			case 'city':
				$ret = "'" . str_replace("'", "''", $this->normalizeString($this->getCellData($row, 5))) . "'";
				break;
			case 'postalcode':
				$ret = "'" . str_replace("'", "''", $this->getCellData($row, 6)) . "'";
				break;
			case 'phone':
				$ret = "'" . str_replace("'", "''", $this->GetPhoneNumber($this->getCellData($row, 7))) . "'";
				break;
			case 'total_seats':
				
				$ret = "'" . str_replace("'", "''", $this->getCellData($row, 8)) . "'";
			
				if($ret=="''")  	//Added for database upgrade (MYSQL4 to MYSQL5) , by Helen, OCT 23th 2011
					$ret=0;
				break;
			case 'estab_type':
				$ret = "'" . str_replace("'", "''", $this->normalizeString($this->getCellData($row, 9))) . "'";
				break;
			case 'fax':
				$ret = "'" . str_replace("'", "''", $this->GetPhoneNumber($this->getCellData($row, 10))) . "'";
				break;
			case 'contact_name':
				$ret = "'" . str_replace("'", "''", $this->getPrimaryContactName($this->getCellData($row, 11))) . "'";
				break;
			case 'contact_name_2':
				$ret = "'" . str_replace("'", "''", $this->getSecondaryContactName($this->getCellData($row, 11))) . "'";
				break;
			case 'province':
				$ret = "'BC'";
				break;
			case 'store_type_id':
				$ret = "'" . $this->file_formats[$this->file_format][1] . "'";
				if(trim($this->getCellData($row, 9)) == 'Licensee Retail Store')
					$ret = "'1'";   //this is an LRS
			    break;
			default:
				$ret = '';
			}
		}
		else if($this->file_format == 4) {   //Alberta Liquor Stores
			switch($col_name)
			{
			case 'license_number':
				$ret = "'" . $this->GetNumber($this->getCellData($row, 1)) . "'";
				break;
			case 'customer_name':
				$ret = "'" . str_replace("'", "''", $this->normalizeString($this->getCellData($row, 2))) . "'";
				break;
			case 'address':
				$a1 = $this->getCellData($row, 5);
				$a2 = $this->getCellData($row, 6);
				$a3 = $this->getCellData($row, 7);
				if(strcmp($a1, $a3) == 0)
				    $a3 = '';
				if($this->IsPoBox($a1))
                    $ret = $this->CombineAddresses($a2, $a3);
				else if($this->IsPoBox($a2))
                    $ret = $this->CombineAddresses($a1, $a3);
				else if($this->IsPoBox($a3))
                    $ret = $this->CombineAddresses($a1, $a2);
				else
                    $ret = $this->CombineAddresses($a1, $a2, $a3);
				break;
			case 'po_box':
			    if($this->IsPoBox($this->getCellData($row, 7)))
					$ret = "'" . str_replace("'", "''", $this->NormalizePoBox($this->getCellData($row, 7))) . "'";
				else if($this->IsPoBox($this->getCellData($row, 6)))
					$ret = "'" . str_replace("'", "''", $this->NormalizePoBox($this->getCellData($row, 6))) . "'";
				else if($this->IsPoBox($this->getCellData($row, 5)))
					$ret = "'" . str_replace("'", "''", $this->NormalizePoBox($this->getCellData($row, 5))) . "'";
				else
					$ret = "''";
				break;
			case 'city':
				$ret = "'" . str_replace("'", "''", $this->normalizeString($this->getCellData($row, 8))) . "'";
				break;
			case 'postalcode':
				$ret = "'" . str_replace("'", "''", $this->getCellData($row, 9)) . "'";
				break;
			case 'phone':
				$ret = "'" . str_replace("'", "''", $this->GetPhoneNumber($this->getCellData($row, 3))) . "'";
				break;
			case 'total_seats':
					//$ret = "''";
				$ret = 0;  ////Updated for database upgrade (MYSQL4 to MYSQL5) , by Helen, OCT 23th 2011
			
				break;
			case 'estab_type':
				$ret = "''";
				break;
			case 'fax':
				$ret = "'" . str_replace("'", "''", $this->GetPhoneNumber($this->getCellData($row, 4))) . "'";
				break;
			case 'contact_name':
				$ret = "''";
				break;
			case 'contact_name_2':
				$ret = "''";
				break;
			case 'province':
				$ret = "'AB'";
				break;
			case 'store_type_id':
				$ret = "'" . $this->file_formats[$this->file_format][1] . "'";
			    break;
			default:
				$ret = '';
			}
		}
		else if($this->file_format == 5) {   //Alberta Licensees
			switch($col_name)
			{
			case 'license_number':
				$ret = "'" . $this->GetNumber($this->getCellData($row, 1)) . "'";
				break;
			case 'customer_name':
				$ret = "'" . str_replace("'", "''", $this->normalizeString($this->getCellData($row, 2))) . "'";
				break;
			case 'address':
				$a1 = $this->getCellData($row, 5);
				$a2 = $this->getCellData($row, 6);
				$a3 = $this->getCellData($row, 7);
				if(strcmp($a1, $a3) == 0)
				    $a3 = '';
				if($this->IsPoBox($a1))
                    $ret = $this->CombineAddresses($a2, $a3);
				else if($this->IsPoBox($a2))
                    $ret = $this->CombineAddresses($a1, $a3);
				else if($this->IsPoBox($a3))
                    $ret = $this->CombineAddresses($a1, $a2);
				else
                    $ret = $this->CombineAddresses($a1, $a2, $a3);
				break;
			case 'po_box':
			    if($this->IsPoBox($this->getCellData($row, 6)))
					$ret = "'" . str_replace("'", "''", $this->NormalizePoBox($this->getCellData($row, 6))) . "'";
				else if($this->IsPoBox($this->getCellData($row, 7)))
					$ret = "'" . str_replace("'", "''", $this->NormalizePoBox($this->getCellData($row, 7))) . "'";
				else if($this->IsPoBox($this->getCellData($row, 5)))
					$ret = "'" . str_replace("'", "''", $this->NormalizePoBox($this->getCellData($row, 5))) . "'";
				else
					$ret = "''";
				break;
			case 'city':
				$ret = "'" . str_replace("'", "''", $this->normalizeString($this->getCellData($row, 8))) . "'";
				break;
			case 'postalcode':
				$ret = "'" . str_replace("'", "''", $this->getCellData($row, 10)) . "'";
				break;
			case 'phone':
				$ret = "'" . str_replace("'", "''", $this->GetPhoneNumber($this->getCellData($row, 3))) . "'";
				break;
			case 'total_seats':
			
					//$ret = "''";
				$ret = 0;  ////Updated for database upgrade (MYSQL4 to MYSQL5) , by Helen, OCT 23th 2011
				
				break;
			case 'estab_type':
				$ret = "''";
				break;
			case 'fax':
				$ret = "'" . str_replace("'", "''", $this->GetPhoneNumber($this->getCellData($row, 4))) . "'";
				break;
			case 'contact_name':
				$ret = "''";
				break;
			case 'contact_name_2':
				$ret = "''";
				break;
			case 'province':
				$ret = "'" . str_replace("'", "''", $this->getCellData($row, 9)) . "'";
				break;
			case 'store_type_id':
				$ret = "'" . $this->file_formats[$this->file_format][1] . "'";
			    break;
			default:
				$ret = '';
			}
		}
	
		return $ret;
	}

	function getInsertSQL($id, $row)
	{
		$sql1 = 'insert uploaded_customers (';
		$sql2 = ') values (';
		$c = 0;
		foreach($this->columns as $col_name) {
			if($c != 0) {
				$sql1 .= ", ";
				$sql2 .= ", ";
			}
			$c++;
			$sql1 .= $col_name;
			if($col_name == 'row_id')
			    $sql2 .= $id;
			else
				$sql2 .= $this->getColumData($row, $col_name);
		}
		$sql = $sql1 . $sql2 . ")";
		$this->writeLog("SQL: " . $sql);
		return $sql;
	}

	function saveFileToDB(& $data)
	{
		$total = 0;
		for($i = 2; $i < $data->sheets[0]['numRows']+1; $i++)
		{
			if(isset($data->sheets[0]['cells'][$i][1]))
			{
				$row = & $data->sheets[0]['cells'][$i];
				$sql = $this->getInsertSQL($total, $row);
				$result = $this->runSQL($sql);
				$total++;
			}
		}
		return $total;
	}

	function saveCSVFileToDB()
	{
		$this->writeLog("before saving CSV to DB");
		$handle = fopen($this->uploadfile, "r");
		if(!$handle)
			return 0;
		//skip title line
		if(!fgets($handle)) {
			fclose ($handle);
			return 0;
		}
		$total = 0;
		$pos = ftell($handle);
		while($row = fgetcsv($handle, 1000, ",", '"')) {
			if(isset($row[0]) && $row[0] != '')
			{
				fseek($handle, $pos);
				$line = trim(fgets($handle, 1000));
				if(substr($line, -3) == '"""') {	//work around on a bug of fgetcsv()
					$row[count($row)-1] .= '"';
				}
				$sql = $this->getInsertSQL($total, $row);
				$result = $this->runSQL($sql);
				$total++;
				if($total % 100 == 0)
					$this->writeLog("record #" . $total . " saved to DB");
			}
			$pos = ftell($handle);
		}
		fclose ($handle);
		$this->writeLog("after saving CSV to DB");
		return $total;
	}

	function ucwords1($s)
	{
		$exceptions_upper = array("ab", "bc", "se", "sw", "ne", "nw", "rca");
		$exceptions_lower = array("a", "at", "is", "of", "in", "are", "s", "the", "and", "or");
		$exceptions_lower_th = array("th", "st", "nd");
        $s = strtolower($s);
        $middle = 0;
        $middle_sep = 0;
        $st = 0;
        $letter_first = -1;
        $a = array();
        for($i = 0; $i < strlen($s); $i++)
        {
			$v = ord($s{$i});
			if($v >= 97 && $v <= 122) {	//letters
				if($middle == 0) {	//start of a new word
				    if($i > $st) {
						array_push($a, substr($s, $st, $i-$st));	//save last separator
						$st = $i;
					}
				    $middle = 1;
				}
                $middle_sep = 0;
				if($letter_first == -1)
				    $letter_first = 1;
			} else {
				if($middle_sep == 0) {	//start of new seperator
				    if($i > $st) {
						array_push($a, substr($s, $st, $i-$st));	//save last word
						$st = $i;
					}
					$middle_sep = 1;
				}
				$middle = 0;
				if($letter_first == -1)
				    $letter_first = 0;
			}
		}
	    if($i > $st) {
			array_push($a, substr($s, $st, $i-$st));	//save the last segement
		}
		if($letter_first == -1)
		    return '';
        for($i = 1-$letter_first; $i < count($a); $i+=2) {
			if(in_array($a[$i], $exceptions_lower)) {
				if($i == 0)   //first word
					$a[$i] = ucfirst($a[$i]);
			} else if(in_array($a[$i], $exceptions_lower_th)) {
				if($i != 0) {
					if($a[$i-1] == ' ')
						$a[$i] = ucfirst($a[$i]);
				}
				else
					$a[$i] = ucfirst($a[$i]);
			} else if(in_array($a[$i], $exceptions_upper)) {
				$a[$i] = strtoupper($a[$i]);
			} else {
				$a[$i] = ucfirst($a[$i]);
			}
		}
		return join('', $a);
	}

	function normalizeString($s)
	{
	    //remove excessive spaces
	    $s = trim($s);
		$s = preg_replace('/\s+/', " ", $s);
/*	    $s = str_replace("  ", " ", $s);
	    $s = str_replace("  ", " ", $s);
	    $s = str_replace("  ", " ", $s);
		$ss = explode(" ", $s);
		foreach($ss as $t) {
			$t = trim($t);
		}
		$s = implode(" ", $ss);
*/		//make all text lowercase, then make the first letter of each word uppercase
		//$s = ucwords(strtolower($s));
		//$s = preg_replace('#^[a-z][a-z]#e', "strtoupper('$0')", $s);
		//make Po Box => PO Box
		//$s = str_replace("Po Box", "PO Box", $s);
		$s = $this->ucwords1($s);
		$s = str_replace("Po Box", "PO Box", $s);
		return $s;
	}

	//make sure it's in the "firstname lastname" format
	function normalizeName($name)
	{
//		if(strpos($name, '"'))
//			$this->writeLog('Special name found: ---' . $name . "---");
	    $name = $this->normalizeString($name);
		if(!strpos($name, ","))
			return $name;
		else
		{
			$ns = explode(",", $name);
			foreach($ns as $k => $s) {
				$s = trim($s);
				$ns[$k] = $s;
			}
			//move the first element (supposed to be the LastName) to the end of the array
			array_push($ns, array_shift($ns));
			$name = implode(" ", $ns);
			return trim($name);
		}
	}

	function getPrimaryContactName($name)
	{
	    $name = $this->normalizeString($name);
		$names = explode("/", $name);
		$name = $names[0];
		return $this->normalizeName($name);
	}

	function getSecondaryContactName($name)
	{
	    $name = $this->normalizeString($name);
		$names = explode("/", $name);
		if(count($names) < 2)
		    return '';
		$name = $names[1];
		return $this->normalizeName($name);
	}

	function NormalizePoBox($s)
	{
	    $s = trim($s);
		$s = preg_replace('/\s+/', " ", $s);
		$s = str_replace("PO BOX", "PO Box", $s);
		return $s;
	}

	function CombineAddresses($s1, $s2, $s3='')
	{
		$ret = '';
		if($s1 != '')
			$ret = str_replace("'", "''", $this->normalizeString($s1));
		if($s2 != '') {
			if($ret != '') {
				if(substr($ret, -1) == ',' || substr($ret, -1) == '&')
					$ret .= ' ' . str_replace("'", "''", $this->normalizeString($s2));
				else
					$ret .= ', ' . str_replace("'", "''", $this->normalizeString($s2));
			}
			else
				$ret = str_replace("'", "''", $this->normalizeString($s2));
		}
		if($s3 != '') {
			if($ret != '') {
				if(substr($ret, -1) == ',' || substr($ret, -1) == '&')
					$ret .= ' ' . str_replace("'", "''", $this->normalizeString($s3));
				else
					$ret .= ', ' . str_replace("'", "''", $this->normalizeString($s3));
			}
			else
				$ret = str_replace("'", "''", $this->normalizeString($s3));
		}
		$ret = "'" . $ret . "'";
		return $ret;
	}

	function IsPoBox($s)
	{
	    $s = trim($s);
		$s = preg_replace('/\s+/', " ", $s);
		$s = str_replace(" ", "", $s);
		$s = str_replace(" ", "", $s);
		$s = str_replace(".", "", $s);
		$s = strtoupper($s);
		if(substr($s, 0, 5) == "POBOX")
		    return true;
		if(substr($s, 0, 3) == "BOX")
		    return true;
		return false;
	}

	function getLastInsertedID()
	{
		$dbc = & Db::getInstance();
		return $dbc->lastInsertId();
	}

	function runSQL($sql)
	{
		$dbc = & Db::getInstance();
		$result = $dbc->query($sql);
		if (!$result)
		{
			PHP2Go::raiseError('SQL error:' . $sql, E_USER_ERROR, __FILE__, __LINE__);
			exit;
		}
		return $result;
	}

	function get_current_user_id()
	{
		import('php2go.auth.User');
		$currentUser = & User::getInstance();
		return $currentUser->getPropertyValue('user_id');
	}

	function createNewSession($total = 0)
	{
		$sql = "insert uploaded_customers_sessions (step_id, file_format_id, total_records, current_row_id, upload_date, file_name) values (1, " . $this->file_format . ", " . $total . ", 0, now(), '" . $this->file_name . "' )";
		$result = $this->runSQL($sql);
/*		$sql = 'select session_id from uploaded_customers_sessions order by session_id desc limit 1';
		$result = $this->runSQL($sql);
		$res = $result->fetchRow();
		$this->session_id = $res['session_id'];
*/		$this->session_id = $this->getLastInsertedID();
	}

	function addNoteForCustomer($customer_id, $note)
	{
		$sql = "insert notes (note_text, created_user_id, when_created) values ('" . str_replace("'", "''", $note) . "', " . $this->user_id . ", '" . $this->now . "')";
		$this->runSQL($sql);
		$note_id = $this->getLastInsertedID();
		$sql = "insert customers_notes (customer_id, note_id) values (" . $customer_id . ", " . $note_id . ")";
		$this->runSQL($sql);

	}

	function saveSessionResult()
	{
		$this->user_id = $this->get_current_user_id();
		$this->now = F60Date::sqlDateTime();

		//new customers, all inserts
		$sql = 'select * from uploaded_customer_changes_new ucc inner join uploaded_customers uc on ucc.customer_row_id = uc.row_id';
		$result = $this->runSQL($sql);
//		$custs = $result->GetAll();
//		$result->MoveFirst();
		$this->writeLog("before saving data1 to DB");
		$i = 0;
		while(($cust = & $result->FetchRow()))
//		foreach($custs as $cust)
		{
			$phone_type_id = 1;
			if($cust['phone'] == '' && $cust['fax'] != '')
				$phone_type_id = 3;
			$sql = "insert customers (customer_name, licensee_number, phone_office1, phone_fax, po_box, billing_address_street_number, billing_address_street, billing_address_city, billing_address_state, billing_address_postalcode, lkup_store_type_id, created_user_id, when_entered, lkup_phone_type_id, total_seats) values ('" . str_replace("'", "''", $cust['customer_name']) . "', '" . $cust['license_number'] . "', '" . $cust['phone'] . "', '" . $cust['fax'] . "', '" . str_replace("'", "''", $cust['po_box']) . "', '" . $this->getStreetNumber($cust['address']) . "', '" . $this->getStreet($cust['address']) . "', '" . str_replace("'", "''", $cust['city']) . "', '" . $cust['province'] . "', '" . $cust['postalcode'] . "', " . $cust['store_type_id'] . ", " . $this->user_id . ", '" . $this->now . "', " . $phone_type_id . ", " .$cust['total_seats'] . ")";
			$this->runSQL($sql);
			$customer_id = $this->getLastInsertedID();
			//contact 1
			if($cust['contact_name']) {
				$sql = "insert contacts (first_name, last_name, created_user_id, when_entered) values ('" . $this->getFirstName($cust['contact_name']) . "', '" . $this->getLastName($cust['contact_name']) . "', " . $this->user_id . ", '" . $this->now . "')";
				$this->runSQL($sql);
				$contact_id = $this->getLastInsertedID();
				$sql = "insert customers_contacts (customer_id, contact_id, is_primary) values (" . $customer_id . ", " . $contact_id . ", 1)";
				$this->runSQL($sql);
			}
			//contact 2
			if($cust['contact_name_2']) {
				$sql = "insert contacts (first_name, last_name, created_user_id, when_entered) values ('" . $this->getFirstName($cust['contact_name_2']) . "', '" . $this->getLastName($cust['contact_name_2']) . "', " . $this->user_id . ", '" . $this->now . "')";
				$this->runSQL($sql);
				$contact_id = $this->getLastInsertedID();
				$sql = "insert customers_contacts (customer_id, contact_id, is_primary) values (" . $customer_id . ", " . $contact_id . ", 0)";
				$this->runSQL($sql);
			}
			//note 1
			if($cust['total_seats']) {
				$this->addNoteForCustomer($customer_id, "Total seats - " . $cust['total_seats']);
			}
			//note 2
			if($cust['estab_type']) {
				$this->addNoteForCustomer($customer_id, "Type - " . $cust['estab_type']);
			}
//			$result->MoveNext();
			//$i++;
			//if($i % 100 == 0)
			//	$this->writeLog($i . " records saved");
		}

		$this->writeLog("before saving data2 to DB");
		//changed customers, more complicated
		$sql = 'select * from uploaded_customer_changes ucc inner join uploaded_customers uc on ucc.customer_row_id = uc.row_id';
		$result = $this->runSQL($sql);
//		$custs = $result->GetAll();
//		$result->MoveFirst();
		while(($cust = & $result->FetchRow()))
//		while(!$result->EOF)
//		foreach($custs as $cust)
		{
			$phone_type_id = 1;
			if($cust['phone'] == '' && $cust['fax'] != '')
				$phone_type_id = 3;
			
			//updated by Helen, not update the phone number -2013-0407
			$sql = "update customers set customer_name = '" . str_replace("'", "''", $cust['customer_name']) . 
                    "', phone_fax = '" . $cust['fax'] . "', po_box = '" . str_replace("'", "''", $cust['po_box']) 
                    . "', billing_address_street_number = '" . $this->getStreetNumber($cust['address']) . "', billing_address_street = '" 
                    .  $this->getStreet($cust['address']) . "', billing_address_city = '" . str_replace("'", "''", $cust['city']) 
                    . "', billing_address_state = '" . $cust['province'] . "', billing_address_postalcode = '" . $cust['postalcode'] 
                    . "', deleted = 0, modified_user_id = " . $this->user_id . ", when_modified = '" . $this->now . "', lkup_phone_type_id = " 
                    . $phone_type_id . ", total_seats = " . $cust['total_seats'] . " where customer_id = " . $cust['matching_customer_id'];
			
			/*	old code, update  phone number
			
			$sql = "update customers set customer_name = '" . str_replace("'", "''", $cust['customer_name']) . "', phone_office1 = '" . $cust['phone'] . "', phone_fax = '" . $cust['fax'] . "', po_box = '" . str_replace("'", "''", $cust['po_box']) . "', billing_address_street_number = '" . $this->getStreetNumber($cust['address']) . "', billing_address_street = '" .  $this->getStreet($cust['address']) . "', billing_address_city = '" . str_replace("'", "''", $cust['city']) . "', billing_address_state = '" . $cust['province'] . "', billing_address_postalcode = '" . $cust['postalcode'] . "', deleted = 0, modified_user_id = " . $this->user_id . ", when_modified = '" . $this->now . "', lkup_phone_type_id = " . $phone_type_id . ", total_seats = " . $cust['total_seats'] . " where customer_id = " . $cust['matching_customer_id'];*/
				
				
			$this->writeLog("---\n" . $sql . "\n---");
			$this->runSQL($sql);
			// process back-to-business customers
			if($cust['license_changed'])
			{	// status: see patch 13, 0 - new, 1 - updated, 2 - oob
				$sql = "update customers set status = 1, licensee_number = " . $cust['license_number'] . " where customer_id = " . $cust['matching_customer_id'];
				$this->writeLog("---\n" . $sql . "\n---");
				$this->runSQL($sql);
			}
		//removed by Helen Step 04, 2009 Begin
		
		/*	if($cust['contact_name_changed'])   //need to update contacts
			{
				if($cust['matching_contact_id'] == 0)   //new contact added
				{
					//contact 1
					if($cust['contact_name']) {
						$sql = "insert contacts (first_name, last_name, created_user_id, when_entered) values ('" . $this->getFirstName($cust['contact_name']) . "', '" . $this->getLastName($cust['contact_name']) . "', " . $this->user_id . ", '" . $this->now . "')";
						$this->runSQL($sql);
						$contact_id = $this->getLastInsertedID();
						$sql = "insert customers_contacts (customer_id, contact_id, is_primary) values (" . $cust['matching_customer_id'] . ", " . $contact_id . ", 1)";
						$this->runSQL($sql);
					}
					//contact 2
					if($cust['contact_name_2']) {
						$sql = "insert contacts (first_name, last_name, created_user_id, when_entered) values ('" . $this->getFirstName($cust['contact_name_2']) . "', '" . $this->getLastName($cust['contact_name_2']) . "', " . $this->user_id . ", '" . $this->now . "')";
						$this->runSQL($sql);
						$contact_id = $this->getLastInsertedID();
						$sql = "insert customers_contacts (customer_id, contact_id, is_primary) values (" . $cust['matching_customer_id'] . ", " . $contact_id . ", 0)";
						$this->runSQL($sql);
					}
				}
				else    //existing contact updated
				{
					$sql = "update contacts set first_name = '" . $this->getFirstName($cust[contact_name]) . "', last_name = '" . $this->getLastName($cust[contact_name]) . "', modified_user_id = " . $this->user_id . ", when_modified = '" . $this->now . "' where contact_id = " . $cust['matching_contact_id'];
					$this->runSQL($sql);
				}
			}*/ //removed by Helen Step 04, 2009 End here
			
//			$result->MoveNext();
		}

		$this->writeLog("before saving data3 to DB");
		//oob customers, this one is really simple
		$sql = 'update customers c, uploaded_customer_changes_oob ucco set c.status = 2, modified_user_id = ' . $this->user_id . ", when_modified = '" . $this->now . "' where c.customer_id = ucco.customer_id";
		$result = $this->runSQL($sql);
		$this->writeLog("after saving data3 to DB");
	}

	function updateSession($step, $total = false)
	{
		$sql = 'update uploaded_customers_sessions set step_id = ' . $step . ' where session_id = ' . $this->session_id;
		$result = $this->runSQL($sql);
		if($total) {
			$sql = 'update uploaded_customers_sessions set total_records = ' . $total . ' where session_id = ' . $this->session_id;
			$result = $this->runSQL($sql);
		}
	}

	function getStreetNumber($address)
	{
		$flds = explode(" ", $address);
		if(isset($flds[0]) && ctype_digit($flds[0]))
			return str_replace("'", "''", $flds[0]);
		return '';
	}

	function getStreet($address)
	{
		$flds = explode(" ", $address);
		if(isset($flds[0]) && ctype_digit($flds[0]))
		{	//street number exists
			if(isset($flds[1])) {
				unset($flds[0]);
				return str_replace("'", "''", implode($flds, " "));
			}
		}
		else if(isset($flds[0]))
		{	//no street number
			return str_replace("'", "''", implode($flds, " "));
		}
		//empty street name
		return '';
	}

	function getFirstName($name)
	{
		$names = explode(" ", $name);
		if(isset($names[0]))
			return str_replace("'", "''", $names[0]);
		return '';
	}

	function getLastName($name)
	{
		$names = explode(" ", $name);
		if(isset($names[1])) {
			unset($names[0]);
			return str_replace("'", "''", implode($names," "));
		}
		return '';
	}

	function getReportSQL($typeID)
	{
		switch($typeID)
		{
		case 0:   //new
			$sql ="SELECT * from uploaded_customers uc inner join uploaded_customer_changes_new ucc on uc.row_id = ucc.customer_row_id order by uc.customer_name";
			break;
		case 1: //changed
			$sql ="SELECT * from uploaded_customers uc inner join uploaded_customer_changes ucc on uc.row_id = ucc.customer_row_id order by uc.customer_name";
			break;
		case 2: //oob
			$sql ="SELECT distinct cm.licensee_number as license_number, customer_name,
							   concat(IFNULL(first_name,''),' ',IFNULL(last_name,'')) contact_name,
							   phone_office1 phone, cm.phone_fax fax,
							   concat(IFNULL(billing_address_unit,'') ,
								IFNULL(billing_address_street_number,''), ' ',
								IFNULL(billing_address_street,'')) address,
                                billing_address_postalcode postalcode,
								billing_address_city city,
								cm.po_box,
								cm.total_seats
							 from uploaded_customer_changes_oob ucc
							join customers cm
							on cm.customer_id = ucc.customer_id
							left join customers_contacts cmc
							on cm.customer_id = cmc.customer_id and cmc.is_primary=1
							left join contacts c
							on c.contact_id = cmc.contact_id and c.deleted=0
							 where cm.deleted=0 order by customer_name";
			break;
		}
		return $sql;
	}

	function isCellUpdated($typeID, $col_key, $result)
	{
	 	if($typeID == 1)
	 	{
			switch($col_key)
			{
			 	case 0:	//license_number
			 		return ($result->Fields('license_changed') == 1);
			 		break;
			 	case 1:	//customer_name
			 		return ($result->Fields('name_changed') == 1);
			 		break;
			 	case 2:	//city
			 		return ($result->Fields('city_changed') == 1);
			 		break;
			 	case 3:	//address
			 		return ($result->Fields('address_changed') == 1);
			 		break;
			 	case 4: //phone
			 		return ($result->Fields('phone_changed') == 1);
			 		break;
			 	case 5: //fax
			 		return ($result->Fields('fax_changed') == 1);
			 		break;
			 	case 6: //contact_name
			 		return ($result->Fields('contact_name_changed') == 1);
			 		break;
			 	case 7:	//po_box
			 		return ($result->Fields('pobox_changed') == 1);
			 		break;
			 	case 8:	//total_seat
			 		return ($result->Fields('seat_changed') == 1);
			 		break;
			 	default:
			 		return false;
			}
		}
		return false;
	}
	
	function exportToExcel()
	{
		$types = array('New customers', 'Updated customers', 'Customers out of business');
		$columns = array('license_number', 'customer_name', 'city' ,'address', 'phone', 'fax', 'postalcode', 'po_box', 'total_seats');
		$sp =& new Spreadsheet();
		$this->writeLog("after new spreadsheet object");
		$row = 0;
		foreach($columns as $key=>$col) {
			$sp->writeData($row, $key, $col);
		}
		$row++;
		$this->writeLog("after writing head");
//		$changedFormat = $sp->addCellFormat(array('shaded'=>true));
		$arial = $sp->addFont(array('name'=>'Arial'));
		$arialBold = $sp->addFont(array('bold'=>true, 'italic'=>true, 'name'=>'Arial'));
		foreach($types as $type => $typename)
		{
			$sp->writeData($row, 0, $typename);
			$row++;
			$sql = $this->getReportSQL($type);
			$result = $this->runSQL($sql);
			$this->writeLog("before getting data from DB");
			//$alldata = $result->GetAll();
			//$this->writeLog("after getting data from DB");
			//foreach($alldata as $data)
			$result->MoveFirst();
			while(!$result->EOF)
			{
				foreach($columns as $key=>$col) {
					//$s = $data[$col];
					$s = $result->Fields($col);
					if($col == 'phone' || $col == 'fax')
						$s = $this->formatPhone($s);
					if(strlen(trim($s)) == 0)
						$s = '[_empty_]';
					if($this->isCellUpdated($type, $key, $result))	//changed
					{
						$sp->writeString($row, $key, $s, 0, 0, $arialBold);
//						$sp->writeString($row, $key, $s, 0, 0, $arialBold, $changedFormat);
					}
					else
					{
						$sp->writeString($row, $key, $s, 0, 0, 0, 0);
					}
				}
				$row++;
				if($row % 1000 == 0)
					$this->writeLog("after writing row " . $row);
				$result->MoveNext();
			}
			$row++;
		}
		$this->writeLog("after writing data");
		$location = 'reports/report_' . $this->session_id . '.xls';
		$sp->toFile($location);
		return $location;
	}

	function formatPhone($number)
	{
		$ret = $number;
		$l = strlen($number);
		if($l > 4) {
			if($l < 8)
				$ret = substr($number, 0, -4) . '-' . substr($number, -4);
			else if($l < 11)
				$ret = substr($number, 0, $l-7) . '-' . substr($number, $l-7, -4) . '-' . substr($number, -4);
			else
				$ret = substr($number, 0, $l-10) . '-' . substr($number, $l-10, $l-7) . '-' . substr($number, $l-7, -4) . '-' . substr($number, -4);
		}
		return $ret;
	}

	function GetPhoneNumber($txt)
	{
		$txt = str_replace("-", "", $txt);
		$txt = str_replace("(", "", $txt);
		$txt = str_replace(")", "", $txt);
		$txt = str_replace(" ", "", $txt);
		$txt = str_replace(" ", "", $txt);
		$txt = str_replace("NOFAX", "", $txt);
		return $txt;
	}

	function GetNumber($txt)
	{
		$n = intval($txt);
		return "" . $n;
	}

	function writeLog($txt)
	{
	//	$fp = fopen("logs/CClogfile.log","a");
	//	fputs($fp, $txt."\n");
//		fputs($fp, memory_get_usage() . "\n");
	//	fclose($fp);
	}
}

?>
