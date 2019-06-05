<?php

/**
 * Perform the necessary imports
 */

require_once('Excel/reader.php');

class customerComapreExec extends php2go
{
	var $percent;
	var $valid_total;
	var $session_id;
	var $batch_size;
//	var $uploaddir;
//	var $upldfname;
	var $file_format;
	var $file_formats = array(1 => array("BC LRS List", 1),
							  3 => array("BC Licensee List", 3),
							  4 => array("Alberta Liquor Stores", 7),
							  5 => array("Alberta Licensee List", 8)
							 );

	function customerCompareExec()
	{
		php2go::php2go();
	}

	function run()
	{
		$this->session_id = $_GET['cc_session_id'];

		if(isset($_GET['batch_size']))
			$this->batch_size = $_GET['batch_size'];
		else {
			//$this->batch_size = 400;
			$sql = 'select count(*) as total from uploaded_customers';
			$result = $this->runSQL($sql);
			$row = $result->fetchRow();
			$total = $row['total'];
			$this->batch_size = $total / 20;
			if($this->batch_size > 400)
			    $this->batch_size = 400;
			if($this->batch_size < 2)
			    $this->batch_size = 2;
		}

		//get resarting point
		$sql = 'select * from uploaded_customers_sessions where session_id = ' . $this->session_id;
		$result = $this->runSQL($sql);
		$row = $result->fetchRow();
		$step = $row['step_id'];
		$this->file_format = $row['file_format_id'];
		if($step == 1) {	//new session, file uploaded but not saved in DB yet
/*			$this->cleanTempTables();
			$this->getUploadedFileName();
			$this->unzipFile();
			$this->updateSession(2);
			$this->writeLog("step1 done");
			$this->percent = 5;	//percentage
*/		} else if($step == 2) {	//temp tables cleaned, ready to save data to DB
			//read content
/*			$this->getUploadedFileName();
			$this->writeLog("get file name:" . $this->upldfname);
			$data = new Spreadsheet_Excel_Reader();
			$this->writeLog("obj created");
			$data->setOutputEncoding('CP1251');
			$this->writeLog("before read file");
			$data->read($this->upldfname);
			$this->writeLog("after read file");
			$this->valid_total = $this->saveFileToDB(&$data);
			$this->writeLog("after save to DB");
			//remove files
			$this->updateSession(3, $this->valid_total);
			unlink($this->upldfname);
			rmdir($this->uploaddir);
			$this->percent = 10;	//percentage
*/		} else if($step == 3) {	//file saved in DB, but records are not processed yet
			$current_row = $row['current_row_id'];
			$this->valid_total = $row['total_records'];
//			echo $this->session_id . "|" . $current_row . "|" . $this->valid_total;
			$this->processCompare($current_row);
		} else {
			$this->percent = 100;	//percentage
		}
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

	function getCustAddress(& $fields)
	{
		$addr = $fields['billing_address_street_number'] . ' ' . $fields['billing_address_street'];
		return trim($addr);
	}

	function appendHeaderContent()
	{
	}

	function display()
	{
		$this->run();
/*		header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header ("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header ("Cache-Control: no-cache, must-revalidate");
		header ("Pragma: no-cache");
		header("Content-type: text/xml");
*/		print "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
		print "<result>";
		print $this->percent;
		print "</result>";
		print "<total>";
		print $this->valid_total;
		print "</total>";
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

/*	function getUploadedFileName()
	{
		$this->uploaddir = ROOT_PATH . "upload/" . $this->session_id;
		$res = opendir($this->uploaddir);
		while($fname = readdir($res)) {
			if($fname != "." && $fname != "..")
				break;
		}
		$this->upldfname = $this->uploaddir . "/" . $fname;
	}
*/

	function processCompare($current_row)
	{
		//$store_type_id = $this->file_format;
		$store_type_id = $this->file_formats[$this->file_format][1];
        set_time_limit(300);
		if($current_row > $this->valid_total - 1)
		{
//			echo "happy";
			//find those out-of-business
			// -- OOB fix --
			// store_type_id is tricky, it has to be checked to make sure only the possible type(s) of customers in the uploaded file is being checked,
			// but it cannot be matched to any uploaded individual customers because it's OOB. This became an issue since we have more than one type of customers
			// in one single file (BC licensee + LRS), we introduced a new column in uploaded_customers to solve this, but also introduced this issue, which we
			// still don't have simple logic to solve.
			$sql = 'insert uploaded_customer_changes_oob select c.customer_id from customers c left outer join uploaded_customers uc on c.licensee_number = uc.license_number 
                    and c.billing_address_state = uc.province 
                    where uc.license_number is null and c.status != 2 and c.deleted = 0';
			if($this->file_format == 3)
				$sql .= ' and (c.lkup_store_type_id = 1 or c.lkup_store_type_id = 3 )'; // only LRS and Licensee , we ingore VQA this time, may be in future we need to deal with this store type
			else
				$sql .= ' and c.lkup_store_type_id = ' . $store_type_id;
			$result = $this->runSQL($sql);
			
			//try to find out those back-to-business customers
			//compare name, address and city only
            /*          
            
            */
			//2007-06-15: we also compare total_seats to match the correct customer when there are multiple with same name and address
			$sql = "select customer_id, row_id, abs(c.total_seats-uc.total_seats) seats_diff,
						(c.customer_name=uc.customer_name) name_match,
					--	(c.phone_office1 = uc.phone) phone_match,
						(trim(concat(c.billing_address_street_number, ' ', c.billing_address_street)) = trim(uc.address) and c.billing_address_city = uc.city) address_match
					from customers c
					join uploaded_customers uc
					on c.lkup_store_type_id = uc.store_type_id
					 and ( -- c.phone_office1 = uc.phone or
					   (c.customer_name = uc.customer_name
						 	 and (trim(concat(c.billing_address_street_number, ' ', c.billing_address_street)) = trim(uc.address)
						 			and c.billing_address_city = uc.city)
						 	)
						)
					-- and c.total_seats = uc.total_seats
					join uploaded_customer_changes_new ucc
					on uc.row_id = ucc.customer_row_id
					where c.status = 2
					order by row_id";
           
			
            $this->writeLog("--- customerCompareExec - get btb\n" . $sql . "\n---");
            
			$result = $this->runSQL($sql);
			$custs = $result->GetAll();
			$row_id = -1;
			//
			$occurrances = 0;
			$customer_id = -1;
			$row_id = -1;
			$total_matches = 0;
			$seat_diff = 9999;
			//
			foreach($custs as $existg)
			{
				if($existg['row_id'] != $row_id) {  //avoid multiple btb if the same customer was oobed multiple times
					//new group with same row_id begins
					//process the last group
					if($customer_id != -1) {	//this is not the beginning of the first group
						//update the last group in DB
						$sql = 'insert uploaded_customer_changes (matching_customer_id, customer_row_id, license_changed) values(' . $customer_id . ', ' . $row_id . ', 1)';
						$this->writeLog("--- customerCompareExec - get btb - 1-1\n" . $sql . "\n---");
						$result = $this->runSQL($sql);
						$sql = 'delete from uploaded_customer_changes_new where customer_row_id = ' . $row_id;
						$this->writeLog("--- customerCompareExec - get btb - 1-2\n" . $sql . "\n---");
						$result = $this->runSQL($sql);
					}
                    $row_id = $existg['row_id'];
                    $customer_id = $existg['customer_id'];
					$total_matches = $existg['phone_match'] + $existg['name_match'] + $existg['address_match'];
                    $seat_diff = $existg['seats_diff'];
					$this->writeLog("--- customerCompareExec - get btb - 1-3\n");
					$this->writeLog("--- total matches = $total_matches, seats_diff = $seat_diff \n");
				} else {	//not the first one in the group, check if it's a better match
					$t_matches = $existg['phone_match'] + $existg['name_match'] + $existg['address_match'];
					if(($t_matches > $total_matches) || ($t_matches == $total_matches && $existg['seats_diff'] < $seat_diff)) {
	                    $row_id = $existg['row_id'];
	                    $customer_id = $existg['customer_id'];
	                    $total_matches = $t_matches;
	                    $seat_diff = $existg['seats_diff'];
						$this->writeLog("--- customerCompareExec - get btb - 1-4\n");
						$this->writeLog("--- total matches = $total_matches, seats_diff = $seat_diff \n");
					}
				}
			}
			//now process the last group
			if($customer_id != -1) {	//there is at least one group
				//update the last group in DB
				$sql = 'insert uploaded_customer_changes (matching_customer_id, customer_row_id, license_changed) values(' . $customer_id . ', ' . $row_id . ', 1)';
				$this->writeLog("--- customerCompareExec - get btb - 2-1\n" . $sql . "\n---");
				$result = $this->runSQL($sql);
				$sql = 'delete from uploaded_customer_changes_new where customer_row_id = ' . $row_id;
				$this->writeLog("--- customerCompareExec - get btb - 2-2\n" . $sql . "\n---");
				$result = $this->runSQL($sql);
			}
			
			//2007-06-15: try to find out those back-to-business within the same week (simply only license number changed) - we call them btb2
			//we need to compare name, address, total_seats of customers in uploaded_customer_changes_oob and uploaded_customer_changes_new
			$sql = "select c.customer_id, row_id from customers c
					join uploaded_customer_changes_oob uco
					on c.customer_id = uco.customer_id
					join uploaded_customers uc
					on c.lkup_store_type_id = uc.store_type_id
					 and c.customer_name = uc.customer_name
					 and trim(concat(c.billing_address_street_number, ' ', c.billing_address_street)) = trim(uc.address)
					 and c.billing_address_city = uc.city
					 -- and c.total_seats = uc.total_seats
					join uploaded_customer_changes_new ucn
					on uc.row_id = ucn.customer_row_id
					order by row_id";
			$this->writeLog("--- customerCompareExec - get btb2\n" . $sql . "\n---");
			$result = $this->runSQL($sql);
			$custs = $result->GetAll();
			$row_id = -1;
			foreach($custs as $existg)
			{
				if($existg['row_id'] != $row_id) {  //avoid multiple btb2 if the same customer was oobed multiple times
                    $row_id = $existg['row_id'];
					$sql = 'insert uploaded_customer_changes (matching_customer_id, customer_row_id, license_changed) values(' . $existg['customer_id'] . ', ' . $existg['row_id'] . ', 1)';
					$this->writeLog("--- customerCompareExec - get btb2 - 1\n" . $sql . "\n---");
					$result = $this->runSQL($sql);
					$sql = 'delete from uploaded_customer_changes_new where customer_row_id = ' . $existg['row_id'];
					$this->writeLog("--- customerCompareExec - get btb2 - 2\n" . $sql . "\n---");
					$result = $this->runSQL($sql);
					$sql = 'delete from uploaded_customer_changes_oob where customer_id = ' . $existg['customer_id'];
					$this->writeLog("--- customerCompareExec - get btb2 - 3\n" . $sql . "\n---");
					$result = $this->runSQL($sql);
				}
			}
			
			
			//update statistics
			$sql = 'select count(*) as cnt from uploaded_customer_changes_oob';
			$result = $this->runSQL($sql);
			$res = $result->fetchRow();
			$oob = $res['cnt'];
			$sql = 'select count(*) as cnt from uploaded_customer_changes_new';
			$result = $this->runSQL($sql);
			$res = $result->fetchRow();
			$new = $res['cnt'];
			$sql = 'select count(*) as cnt from uploaded_customer_changes';
			$result = $this->runSQL($sql);
			$res = $result->fetchRow();
			$upd = $res['cnt'];
			$sql = 'insert uploaded_customers_statistics (upload_date, session_id, new_number, updated_number, oob_number) values (now(), ' . $this->session_id . ', ' . $new . ', ' . $upd . ', ' . $oob . ')';
			$result = $this->runSQL($sql);
			$this->percent = 100;	//percentage
			$this->updateSession(4);
			return;
		}
		

//		$sql = 'select * from uploaded_customers where row_id >= ' . $current_row . ' order by row_id' . ' limit ' . $this->batch_size;

		$intBatchSize = Intval($this->batch_size); // add and updated by Helen for mysql4 to mysql5
		$sql = 'select * from uploaded_customers where row_id >= ' . $current_row . ' order by row_id' . ' limit ' . $intBatchSize;
		$result = $this->runSQL($sql);
		$custs = $result->GetAll();
		$size_p = 0;
		foreach($custs as $cust)
		{
			$size_p++;
			$license_number = $cust['license_number'];
		/*	if($cust['store_type_id'] == 3)	//BC licensee
			{
				//some BC licensees are actually LRSs, they still appear as licensees in the file, but people have already manually updated them to LRSs in the database.
				//we need to identify these customers and keep them as LRSs (not new licensees).
				//we try to match the licensee number with both store types, and will ignore it if any one exists.
				//$sql = "select * from customers where licensee_number = '" . $license_number . "' and (lkup_store_type_id = 3 or lkup_store_type_id = 1 ) and deleted=0 and status!=2";
                
                //give up compare the store type for BC, since it kind of complicate in reports ( no one can tells exactly store type for, we update the store type in CS Online)
                $sql = "00select * from customers where licensee_number = '" . $license_number . "' and (lkup_store_type_id = 3 or lkup_store_type_id = 1 ) and deleted=0 and status!=2";
			}
			else
			{
				$sql = "00select * from customers where licensee_number = '" . $license_number . "' and lkup_store_type_id = " . $cust['store_type_id'] ." and deleted=0 and status!=2";
			}*/
            
            //update by Helen, 07-2018 for igonring store type in BC
            
           	if($cust['store_type_id'] < 6)	//BC licensee are all small the 6, in the govenment report, we asume they are all licensee, and we will update store type manuly in cs online
			{
				//some BC licensees are actually LRSs, they still appear as licensees in the file, but people have already manually updated them to LRSs in the database.
				//we need to identify these customers and keep them as LRSs (not new licensees).
				//we try to match the licensee number with both store types, and will ignore it if any one exists.
				//$sql = "select * from customers where licensee_number = '" . $license_number . "' and (lkup_store_type_id = 3 or lkup_store_type_id = 1 ) and deleted=0 and status!=2";
                
                //give up compare the store type for BC, since it kind of complicate in reports ( no one can tells exactly store type for, we update the store type in CS Online)
                $sql = "select * from customers where licensee_number = '" . $license_number . "' and (lkup_store_type_id = 3 or lkup_store_type_id = 1  or lkup_store_type_id = 2 or lkup_store_type_id = 5) and deleted=0 and status!=2";
			}
			else
			{
				$sql = "select * from customers where licensee_number = '" . $license_number . "' and lkup_store_type_id = " . $cust['store_type_id'] ." and deleted=0 and status!=2";
			}
            
//			$this->writeLog("--- customerCompareExec - find existing customer -\n" . $sql . "\n---");
			$result = $this->runSQL($sql);
			
			if($result->RecordCount() == 0)
			{
				//new customer
				$sql = 'insert uploaded_customer_changes_new (customer_row_id) values(' . $cust['row_id'] . ')';
				$result = $this->runSQL($sql);
			}
			else {
				//existing customer
				$changed = 0;
				$contact_changed = 0;
				$name_changed = 0;
				$address_changed = 0;
				$city_changed = 0;
				$pobox_changed = 0;
				$phone_changed = 0;
				$fax_changed = 0;
				$seats_changed = 0;
				$existg = $result->fetchRow();
				//compare info
				if(strcmp($cust['customer_name'], $existg['customer_name']) != 0) {
				 	$name_changed = 1;
					$changed = 1;
					$this->writeLog("customer name changed for " . $license_number . ":\ndata#1:|" . $cust['customer_name'] . "|");
					$this->writeLog("data#2:|" . $existg['customer_name'] . "|");
				}
				if(strcmp($cust['address'], $this->getCustAddress($existg)) != 0) {
					$changed = 1;
					$address_changed = 1;
					$this->writeLog("address changed for " . $license_number . ":\ndata#1:|" . $cust['address'] . "|");
					$this->writeLog("data#2:|" . $this->getCustAddress($existg) . "|");
				}
				if(strcmp($cust['city'], $existg['billing_address_city']) != 0) {
					$changed = 1;
					$city_changed = 1;
					$this->writeLog("city changed for " . $license_number . ":\ndata#1:|" . $cust['city'] . "|");
					$this->writeLog("data#2:|" . $existg['city'] . "|");
				}
				if(strcasecmp($cust['po_box'], $existg['po_box']) != 0) {
					$changed = 1;
					$pobox_changed = 1;
					$this->writeLog("po_box changed for " . $license_number . ":\ndata#1:|" . $cust['po_box'] . "|");
					$this->writeLog("data#2:|" . $existg['po_box'] . "|");
				}
			/*	if(strcasecmp($cust['phone'], $existg['phone_office1']) != 0) {
					$changed = 1;
					$phone_changed = 1;
					$this->writeLog("phone changed for " . $license_number . ":\ndata#1:|" . $cust['phone'] . "|");
					$this->writeLog("data#2:|" . $existg['phone_office1'] . "|");
				}*/
			/*	if(strcasecmp($cust['fax'], $existg['phone_fax']) != 0) {
					$changed = 1;
					$fax_changed = 1;
					$this->writeLog("fax changed for " . $license_number . ":\ndata#1:|" . $cust['fax'] . "|");
					$this->writeLog("data#2:|" . $existg['phone_fax'] . "|");
				}*/
				if(strcasecmp($cust['total_seats'], $existg['total_seats']) != 0) {
					$changed = 1;
					$seats_changed = 1;
					$this->writeLog("total_seats changed for " . $license_number . ":\ndata#1:|" . $cust['total_seats'] . "|");
					$this->writeLog("data#2:|" . $existg['total_seats'] . "|");
				}
				
				// removed by Helen, government no longer provide contact name due to Freedom of Information/Protection of Privacy concerns Step 04, 2009
				//get contacts
			/*	$sql = 'select * from contacts c, customers_contacts cc where c.contact_id = cc.contact_id and cc.customer_id = ' . $existg['customer_id'] . ' and cc.is_primary = 1 ';
				$result = $this->runSQL($sql);
				$contact_id_existg = 0;
				if($result->RecordCount() == 0)	//customer doesn't have primary contact
				{
					$cname_existg = '';
            }
				else
				{
    				$cntct = $result->fetchRow();
					$cname_existg = trim(trim($cntct['first_name']) . ' ' . trim($cntct['last_name']));
					$contact_id_existg = $cntct['contact_id'];
				}
				$cname = $cust['contact_name'];	//already normalized
				if(strcasecmp($cname, $cname_existg) != 0)//Helen
				{
					$this->writeLog("Contact name changed, names are:|" . $cname . "|" . $cname_existg . "|");
					$changed = 1;
					$contact_changed = 1;
				}*/
				$contact_changed = 0;
				//saved changed record
				if($changed )
				{
				 // update by Helen, government no longer provide contact name due to Freedom of Information/Protection of Privacy concerns Step 04, 2009
					$sql = 'insert uploaded_customer_changes (contact_name_changed, matching_customer_id, matching_contact_id, customer_row_id, license_changed, name_changed, address_changed, city_changed, phone_changed, fax_changed, pobox_changed, seat_changed) values(' . $contact_changed . ', ' . $existg['customer_id'] . ', ' . ($contact_changed ? $contact_id_existg : 0) . ', ' . $cust['row_id'] . ', 0, ' . $name_changed . ', ' . $address_changed . ', ' . $city_changed . ', ' . $phone_changed . ', ' . $fax_changed . ', ' . $pobox_changed . ', ' . $seats_changed . ')';
					
				//	$sql = 'insert uploaded_customer_changes (matching_customer_id, customer_row_id, license_changed, name_changed, address_changed, city_changed, phone_changed, fax_changed, pobox_changed, seat_changed) values( '$existg['customer_id'] . ',' . $cust['row_id'] . ', 0, ' . $name_changed . ', ' . $address_changed . ', ' . $city_changed . ', ' . $phone_changed . ', ' . $fax_changed . ', ' . $pobox_changed . ', ' . $seats_changed . ')';
					
					$result = $this->runSQL($sql);
				}
			}
		}
		//update batch information
		$current_row += $size_p;
		$sql = 'update uploaded_customers_sessions set current_row_id = ' . $current_row . ' where session_id = ' . $this->session_id;
		$result = $this->runSQL($sql);
		//return result (percentage) - from 0% up to 90%
		$this->percent = (int) (($current_row + 1 ) * 90 / $this->valid_total);
		if($this->percent > 90)
			$this->percent = 90;
	}

	function writeLog($txt)
	{
		$fp = fopen("logs/CClogfile.log","a");
		fputs($fp, $txt."\n");
//		fputs($fp, memory_get_usage() . "\n");
		fclose($fp);
 	}
}

?>
