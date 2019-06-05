<?php
import('Form60.dal.dalorders');
import('Form60.bll.bllorderitems');
import('Form60.dal.dalorderitems');
import('Form60.dal.dallkup_store_types');
import('Form60.bll.bllcustomers');
import('Form60.bll.bllestates');
import('Form60.bll.bllwines');
import('Form60.util.F60Date');
import('Form60.dal.dal_alct_wines_customers');
import('Form60.base.F60DbUtil');
import('php2go.base.Php2Go');


class bllorder extends dalorders
{

    var $orderItems = null;
    var $orderSubTotal;
    var $orderGrandTotal;
    var $GST;
    var $licenseeFactor;
    var $litterDepositTotal;
    var $delivery_info;
    var $GST_rate =0.05;
    
    function bllorder()
    {
         parent::dalorders();
    }
    
    function loadByPrimaryKey($keyValues)
    {
        if (parent::loadByPrimaryKey($keyValues))
        {
            //get the order items
            $this->orderItems = & new bllorderitems($this->get_data("order_id"));
            $this->orderItems->load();
            //traceLog("Item count:" . count($this->orderItems->items));
            $this->calculateTotals();
            return true;
        }

        return false;
    }
    
    function loadByInvoiceNo($invoice_number)
    {
        $this->add_filter("invoice_number", "=", $invoice_number);
        if (parent::load())
        {
            //get the order items
            $this->orderItems = & new bllorderitems($this->get_data("order_id"));
            $this->orderItems->load();
            //traceLog("Item count:" . count($this->orderItems->items));
            $this->calculateTotals();
            return true;
        }
        return false;
    }
    
    function getFromDAL($dal)
    {
        if (TypeUtils::isObject($dal) && TypeUtils::isInstanceOf($dal, "dalorders"))
            return $this->loadByPrimaryKey($dal->get_data("order_id")); //extra DB trip here
        else
            return false;
    }
   
   	function getEstateIdByLoginUser($user_id)
   	{
		$db = & Db::getInstance();
        $db->setFetchMode(ADODB_FETCH_ASSOC);
        
        $sql ="Select estate_id From Users where user_id = $user_id";
        
        $rows = $this->db->getAll($sql);
        
        return $rows[0]["estate_id"];
	}
	
	function getEstateIdByOrderId($order_id)
   	{
		$db = & Db::getInstance();
        $db->setFetchMode(ADODB_FETCH_ASSOC);
        
        $sql ="Select estate_id From Orders where order_id = $order_id";
        
        $rows = $db->getAll($sql);
        
        return $rows[0]["estate_id"];
	}
	

    function save($invoice_number="")
    {
        $retVal = true;
        if($invoice_number!='')
        { 	
			$this->set_data("invoice_number",$invoice_number);
		}
		
        if (parent::save())
        {
            if (TypeUtils::isObject($this->orderItems))
            {
                foreach($this->orderItems->items as $orderItem)
                {
                    $bll = & new bllorderitem();
                    $bll->getFromDAL($orderItem); 
                    $orderItem = & $bll;
                    $orderItem->customer_id = $this->get_data("customer_id");
                    if ($this->is_deleted) 
                        $orderItem->mark_deleted();
                    $retVal = $orderItem->save();
                    
                    //if the order is deleted and status is not delivered then return inventory
                    //if the order is cancelled then return inventory
                    if (($this->is_deleted && $this->get_data("lkup_order_status_id") != 2)
                     || ($this->get_data("lkup_order_status_id") == 3))
                    {
                        $quantity = $orderItem->get_data("ordered_quantity");
                        $orderItem->returnToInventory($quantity);
                    }
                }
            }
            
        }    

        return $retVal;
    }
    function updateInvoiceNo($order_id,$invoice_number)
	{
		$db = & Db::getInstance();
        $db->setFetchMode(ADODB_FETCH_ASSOC);
        
        $sql="update orders set invoice_number=$invoice_number where order_id =$order_id";
        return $db->execute($sql);	
        
	}
    function create($customerID, $estateID, $wines, $isImportAL=false,$orders=null) // should remove isAl and invoice_number 
    {
     	$proID = $this->checkIfOliveInOrder($wines);    
//$this->sucTrace($proID);
     	if($proID==3)
     	{
			return 3;
		}
     	
        $customer = & new bllcustomer();
        $estate = & new bllestate();
        if (!$customer->loadByPrimaryKey($customerID) || !$estate->loadByPrimaryKey($estateID))
            return false;

        $this->set_data("created_by_user_name", $this->get_current_user_full_name());
        
        //status info
        $this->set_data("lkup_order_status_id", 1); //pending
        $this->set_data("lkup_payment_status_id", 1); //not paid
        
		//update by Helen, change default delivery date to Friday. Jan 13,2009
	//update by Helen, change default delivery date to Thursday. Oct 17,2011
		
		
			//update by Helen, change default delivery date to Friday. Sept 07,2012
			//update by Helen, change default delivery date to Thursday. March 07,2013
		
		/*		$deliver_week_day = 4;
		
			$today = date("m/d/Y");
			$currentWeekDay=date("w");
		if( $currentWeekDay!=$deliver_week_day )//not Thursday
			{
			
				$nextWed_date= F60Date::getNextWeekDate($deliver_week_day,true); //Coming Thursday:1
				$this->set_data("delivery_date", $nextWed_date); 
					
			}	
			else
				$this->set_data("delivery_date", F60Date::sqlDateTime()); 	*/
				
				
		//update by Helen, change default delivery date to current date, but for SpierHead (estate_id:150) delivery date is on Friday. April 21,2016
		
		$deliveryDate=  F60Date::sqlDateTime();
		
		if($estateID==150)// if estates is SpierHead
		{
	 		$deliver_week_day = 5;// delivery date should be Friday
			$today = date("m/d/Y");
			$currentWeekDay=date("w");
			if( $currentWeekDay!=$deliver_week_day )//not Thursday
			{
				$deliveryDate= F60Date::getNextWeekDate($deliver_week_day,true); //Coming Friday
			}
		}
		$this->set_data("delivery_date", $deliveryDate);      
		
        //customer info
        $this->set_data("customer_id", $customerID);
        $this->set_data("customer_name", $customer->get_data("customer_name"));
        $this->set_data("customer_address",  $customer->getAddress());
        $this->set_data("sst_number", $customer->get_data("sst_number"));
        $this->set_data("licensee_number", $customer->get_data("licensee_number"));
        $this->set_data("lkup_store_type_id", $customer->get_data("lkup_store_type_id"));
        $storeType = & new dallkup_store_types();
        $storeType->loadByPrimaryKey($customer->get_data("lkup_store_type_id"));
                       
        $agency_lrs_factor =0;
		$gst_factor =0;
		$deposit =0;
		
		$i=0;
		$rateInfo= $estate->getRatesByStoreType($estateID,$customer->get_data("lkup_store_type_id")); // need to update here
		
		$agency_lrs_factor = $rateInfo["agency_lrs_factor"];	
		$gst_factor = $rateInfo["gst_factor"];	
	
		
		if($proID ==2) //oliver oil, not hst and discount
		{
			$agency_lrs_factor =0;
			$gst_factor =0;
		
		}
		
		if($estate->get_data("estate_name")=="Paradise Ranch Wines Corp." && $customer->get_data("lkup_store_type_id")==2)
		{
			$this->set_data("agency_LRS_factor", $agency_lrs_factor);
			$this->set_data("GST_factor", $gst_factor);  
			 
		
		}
		else
		{
			$this->set_data("agency_LRS_factor", $agency_lrs_factor);
			$this->set_data("GST_factor", $gst_factor);   
		}

		if($proID ==2) //oliver oil, not hst and discount
		{
			$agency_lrs_factor =0;
			$gst_factor =0;
			$this->set_data("deposit", 0);   
		}
		
        $this->set_data("license_name", $storeType->get_data("license_name"));
        $this->set_data("lkup_payment_type_id", $customer->get_data("lkup_payment_type_id"));
        
        //estate info
        $this->set_data("estate_id", $estateID);
        $this->set_data("estate_name", $estate->get_data("estate_name"));
        $this->set_data("estate_number", $estate->get_data("estate_number"));
        
        $this->set_data("invoice_number", $estate->getNextInvoiceNumber());
		
	/*	if($isAL)
     	{
			$this->set_data("invoice_number", $invoice_number);
		}*/
		
        if ($this->save())
        {         
            $order_id = $this->get_data('order_id');
		 	  
		 	if($isImportAL)
		 	{
				return $order_id;
			}
		 	else
		 	{
	            //create the order items      
	            if ($this->AddUpdateOrderItems($wines)) //[wine_id]=>1;
	            {
	                return $order_id;
	            }
	        }
        }
        
        return false;  //should be false here;
    }
   
    
    /*
	orderInfo: invoice_number, delivery_date
	*/
    function ImportALOrder($estateID,$orderInfo) // should remove isAl and invoice_number 
    {
     	
     	$customerID = $orderInfo["customer_id"];
        $customer = & new bllcustomer();
        $estate = & new bllestate();
        if (!$customer->loadByPrimaryKey($customerID) || !$estate->loadByPrimaryKey($estateID))
        {
         
            return false;
        }

        $this->set_data("created_by_user_name", $this->get_current_user_full_name());
        
        //status info
        $this->set_data("lkup_order_status_id", 2); //delivered
        $this->set_data("lkup_payment_status_id", 1); //not paid
        
		//update by Helen, change default delivery date to Friday. Jan 13,2009
	
		$this->set_data("delivery_date", $orderInfo["delivery_date"]); 	
		
        //customer info
        $this->set_data("customer_id", $customerID);
        $this->set_data("customer_name", $customer->get_data("customer_name"));
        $this->set_data("customer_address",  $customer->getAddress());
        $this->set_data("sst_number", $customer->get_data("sst_number"));
        $this->set_data("licensee_number", $customer->get_data("licensee_number"));
        $this->set_data("lkup_store_type_id", $customer->get_data("lkup_store_type_id"));
        $storeType = & new dallkup_store_types();
        $storeType->loadByPrimaryKey($customer->get_data("lkup_store_type_id"));
        
                 
        $agency_lrs_factor =0;
		$gst_factor =0;
		
		$i=0;
		$rateInfo= $estate->getRatesByStoreTypeForAL($estateID,$customer->get_data("lkup_store_type_id")); // need to update here
		
		$agency_lrs_factor = $rateInfo["agency_lrs_factor"];	
		$gst_factor = $rateInfo["gst_factor"];	
		
		$this->set_data("agency_LRS_factor", $agency_lrs_factor);
		$this->set_data("GST_factor", $gst_factor);   
		
        $this->set_data("license_name", $storeType->get_data("license_name"));
        $this->set_data("lkup_payment_type_id", $customer->get_data("lkup_payment_type_id"));
        
        //estate info
        $this->set_data("estate_id", $estateID);
        $this->set_data("estate_name", "Arrowleaf Cellars");
        $this->set_data("estate_number", "585"); // for Arrowleaf estate number is 585
        
        
        $this->set_data("invoice_number", $orderInfo["invoice_number"]);
        $this->set_data("order_id", null);
		
        if ($this->save())
        {         
            $order_id = $this->get_data('order_id');		 	  
			return $order_id;
        }
        
        return false;  //should be false here;
    }
    
    
	function errorTrace($content)
	{
	   	$fp = fopen("logs/errorAL.log","a");
		fputs($fp, $content);
		fclose($fp);
	}
	
	function sucTrace($content)
	{
	   	$fp = fopen("logs/sucAL.log","a");
		fputs($fp, $content);
		fclose($fp);
	}
	function getDeliveryDate($dateText)
	{
	 	
		//delivery date
		$delivery_date =split("-",$dateText);
		
		$month_name = $delivery_date[0]; 
		
	
		$month_number = "";   

		for($i=1;$i<=12;$i++){   
		    if(strtolower(date("M", mktime(0, 0, 0, $i, 1, 0))) == strtolower($month_name)){   
		        $month_number = $i;   
		        break;   
		    }   
		}  
//		echo $dateText;
		$month_number = str_pad($month_number,2,"0",STR_PAD_LEFT );
		$month_day = str_pad($delivery_date[1],2,"0",STR_PAD_LEFT );
		
		//YYYY-mm-dd
		return "2010-$month_number-$month_day";
	}
/*	import Arrowleaf for May and June in 2010
	

				0		 1		 2							 3			4             
			 Invoice	Date	Store						Units	Unit price
				5237	May-04	Waterfront Wines (195298)	1		
	 					Bacchus	12203						12		14.99

			 
         

	*/
    function importALData($month)
    {
     	$bRet = true;
     
	 	if($month==5)
	 		$dataFile="AL_orders_May.csv";
	 	else
	 		$dataFile="AL_orders_June_5482.csv";
	 		
	 	$dataFile=ROOT_PATH."/reports/$dataFile";
	 	
	 	$estate_id="110";
	 	
	    $handle = fopen($dataFile, "r");
    
    	$totalRec=0;
        if(!$handle)
        {
            $this->errorTrace("Error: Unable to open the data file.");
            return false;
        }
        else
        {
            $data = fgetcsv($handle, 1000, ",");
            
            
            if(!$data) 
            {
                fclose ($handle);
                $this->errorTrace("Error: Unable to read the data file.");
                $bRet = false;
            }
            else
            {
     		
            $row = 0;
            $addOrdItemsFailed=false;
            
            while(($data = fgetcsv($handle, 1000, ","))!==False)
            {
             
                if(!$data) 
                {
                    fclose ($handle);
                    $this->errorTrace("Error: Error reading data file.");
                   
                    return false;
                }   
                else
                {
				 //		print_r($data);
                
				 	
					if($data[0]=="End")// last record
					{
					 
					 	// the end
					 	if(!$addOrdItemsFailed)// insert last order item successed
						{
						 	 	$totalRec++;
						 	 
								F60DbUtil::commTran();
						}
					}
					else // read rows
					{
					 //	print_r($data);
						if($data[0]!="")//inovice number
						{
						 	if($row!=0&&!$addOrdItemsFailed)// insert last order item successed
						 	{
						 	 	$this->sucTrace ("Successe: invoice_number=$invoice_number add completed");
						 	 	$totalRec++;
						 	 	
								F60DbUtil::commTran();
								$order=null;
							}
							$order_id="";
						 	$invoice_number=$data[0];
						
							//get inovice number
							$order["invoice_number"]=$data[0];
							
							//delivery date
							$order["delivery_date"]=$this->getDeliveryDate($data[1]);
							
							//customer_id: customers and store number
							$customer=$data[2];
							//get store number
							$noLength=strpos($customer,")")-strpos($customer,"(");
							$license_no = substr($customer,strpos($customer,"(")+1,$noLength-1);
							
							//getCustomerId
							$rows=$this->getCustomerIds($license_no);
							
							if(count($rows)==0)
							{
							 	$this->errorTrace ("Error: Can't find following licensee: $license_no invoice_number=$invoice_number");
							 	$order["order_id"]="";
							//	return false;
							}
							else if(count($rows)>1)
							{
								$this->errorTrace ("Error: More than on customers have same licensee no following licensee: $license_no invoice_number=$invoice_number");	
								$order["order_id"]="";
							}
							else if(count($rows)==1)//only one customer
							{
							 
								$order["customer_id"]=$rows[0]["customer_id"];
								$order["store_type_id"]=$rows[0]["lkup_store_type_id"];
								$order["estate_id"]=$estate_id;
								
								F60DbUtil::beginTran(); //transaction begins
								$order_id = $this->importALOrder($estate_id,$order);
							
							
								if($order_id==false)
								{
									F60DbUtil::rollbkTran();
									
									$this->errorTrace ("Error: Can't add following order': $license_no invoice_number=$invoice_number");
								}
								else
								{
								 	$order["order_id"]=$order_id;
									$this->sucTrace ("<br> Successe: invoice_number=$invoice_number add order successed");
								}
								
							
							}
							
						}//else if($data[0]!="")//inovice number
						else //order details
						{
 							$wine="";
 						
 							if($order["order_id"]!="")//save order successed, items can be saved
 							{
								//get order items for order
								$sku=$data[2];
								$rows=$this->getWineInfo($sku);
								
								if(count($rows)==0)
								{
								 	$this->errorTrace ("Error: Can't find following sku: $sku invoice_number=$invoice_number");
								 	$wine["wine_id"]="";
								 	F60DbUtil::rollbkTran();
								 	$addOrdItemsFailed = true;
								//	return false;
								}
								else if(count($rows)>1)
								{
									$this->errorTrace ("Error: More than on wines have same sku in following sku: $sku invoice_number=$invoice_number");	
									$wine["wine_id"]="";
									F60DbUtil::rollbkTran();
									$addOrdItemsFailed = true;
								}
								else if(count($rows)==1)//only one customer
								{
									$wine["wine_id"]=$rows[0]["wine_id"];
									$wine["wine_name"]=$rows[0]["wine_name"];
									$wine["vintage"]=$rows[0]["vintage"];
									$wine["sku"]=$rows[0]["cspc_code"];
									$wine["lkup_bottle_size_id"] = $rows[0]["lkup_bottle_size_id"];																		
									$wine["quantity"]= $data[3];
							        $wine["store_type_id"] = $order["store_type_id"];
							        $wine["order_id"] = $order["order_id"];
							        $wine["estate_id"] = $order["estate_id"];
							        $wine["customer_id"] = $order["customer_id"];
							        $wine["estate_id"] = $estate_id;
							        $wine["price_per_unit"] = $data[4];
									
									//add order item
									$orderItem = new bllorderitem();
		                                          
		                            if($orderItem->importOrderITemForAL($wine))
		                            {
										$addOrdItemsFailed = false;	
										$sku=$wine["sku"];
										$$invoice_number = $order["invoice_number"];
										$this->sucTrace ("Success: Adding items successed on sku:  $sku invoice_number=$invoice_number");
									}
									else
									{
									 	$this->errorTrace ("Error: Adding items failed on sku:  $sku invoice_number=$invoice_number");
									 	F60DbUtil::rollbkTran();
										$addOrdItemsFailed = true;
									}
									
								}
								}//end if($order["order_id"]!="")//save order successed
						}//end else if($data[0]!="")//inovice number
					}//
					$row++;
                }
            }
        //sample end herer
    	}
	}
	echo "Total $totalRec add to DB";
}
	
	
    function getWineInfo($sku)
    {
     	$db = & Db::getInstance();
        $db->setFetchMode(ADODB_FETCH_ASSOC);
        
		$sql="select * from wines where cspc_code =$sku";
		
		$rows = $this->db->getAll($sql);
		
		return $rows;
	}
    function getCustomerIds($license_no)
    {
     	$db = & Db::getInstance();
        $db->setFetchMode(ADODB_FETCH_ASSOC);
        
		$sql="select customer_id,lkup_store_type_id from customers where licensee_number=$license_no and deleted=0";
		
		$rows = $this->db->getAll($sql);
		
		return $rows;
	}
	
	function getWineTypeByWineID($wine_id)
	{
		$db = & Db::getInstance();
        $db->setFetchMode(ADODB_FETCH_ASSOC);
        
		$sql="select lkup_wine_color_type_id from wines where wine_id =$wine_id";
		$rows = $this->db->getAll($sql);
		
		$id=$rows[0]["lkup_wine_color_type_id"];
		
		/*$fp = fopen("logs/Ajax_logfile.log","a");
		fputs($fp, $id);
		fclose($fp);*/
		
		return $id;
	}
	
	function checkIfOliveInOrder($wines)
	{
	 	$isWine=0;
	 	$isOliver=0;
	 	
		foreach($wines as $wine_id => $quantity)
        {         	
        	 if($quantity<>""||$quantity>0)
        	 {
        
	             if($this->getWineTypeByWineID($wine_id)!=10)//not oliver
	             {
						$isWine = 1;
				 }
				 else
				 {
						$isOliver = 2;
				}
			}		
        }
        $productFlag = $isWine+$isOliver;
        
        return $productFlag;
	}
	
    function AddUpdateOrderItems($wines)
    {

        $bReturn = false;
        $order_id = $this->get_data('order_id');
        $estate_id = $this->get_data('estate_id');
        $customerID =  $this->get_data("customer_id");
        $store_type_id =  $this->get_data("lkup_store_type_id");
        
        foreach($wines as $wine_id => $quantity)
        {         	
             {
                $bFound = false;
                if ( TypeUtils::isObject($this->orderItems))
                {				
					foreach($this->orderItems->items as $orderItem)
                    {
                        if ($orderItem->get_data("wine_id") == $wine_id)
                        {
                            $bFound = true;
                            $bll = & new bllorderitem();
                            $bll->getFromDAL($orderItem); 
                            $orderItem = & $bll;
                            $orderItem->customer_id = $customerID;
                            $orderItem->estate_id = $estate_id;
                            $orderItem->store_type_id = $store_type_id;
                            
                            $orderItem->set_data("ordered_quantity", $quantity);
                            $bReturn = $orderItem->save();
                            break;
                        }
                    }
                }
                if (!$bFound && ($quantity > 0))
                {
                 	
                    $orderItem = & new bllorderitem();
                    $orderItem->set_data("order_id", $order_id);
                    $orderItem->set_data("wine_id", $wine_id);
                    $orderItem->set_data("ordered_quantity", $quantity);
                    $orderItem->customer_id = $customerID;
                    $orderItem->estate_id = $estate_id;
                    $orderItem->store_type_id = $store_type_id;
                    
                    
                    $bReturn = $orderItem->save();
                }
            }
        }
        
        return $bReturn;
    }
    function get_delivery_date()
    {
		return $this->get_data('delivery_date');
	}
    function calculateTotals()
    {
        $this->orderSubTotal = 0.0;
        $this->orderGrandTotal = 0.0;
        $this->GST = 0.0;
        $this->licenseeFactor = 0.0;
        $this->litterDepositTotal = 0.0;
        $deposit = $this->get_data('deposit');
        
           $delivery_date = $this->get_data('delivery_date');
        
        $when_entered = $this->get_data('when_entered');
        
       
         //2015-04-01 
         $New_Rule_date = "2015-04-01";
         
         $isNewRule =0;
		 if(strtotime($delivery_date)>=strtotime($New_Rule_date))
		{
			$isNewRule =1;
		}

        foreach($this->orderItems->items as $orderItem)
        {
			if($isNewRule == 0)         	
			{
            	$this->orderSubTotal += ($orderItem->get_data("price_per_unit") * $orderItem->get_data("ordered_quantity"));
            
            }
            else
            {
				if( $this->get_data("lkup_store_type_id")==3)
				{
						$this->orderSubTotal += ($orderItem->get_data("price_per_unit") * $orderItem->get_data("ordered_quantity"));
				}
				else
				{
					$this->orderSubTotal += ($orderItem->get_data("price_winery") * $orderItem->get_data("ordered_quantity"));
				}
				
			}
			

			$this->litterDepositTotal += ($orderItem->get_data("ordered_quantity")  * $orderItem->get_data("litter_deposit"));
        }
        if ($deposit > 0)
            $this->litterDepositTotal = $deposit; //override
        
	
  
		if($isNewRule == 0)
		{
	        $this->GST = $this->get_data("GST_factor") * $this->orderSubTotal;
	        $this->licenseeFactor = $this->get_data("agency_LRS_factor") * $this->orderSubTotal;

	        if($this->orderSubTotal!=0)
	       		 $this->orderGrandTotal = $this->orderSubTotal + $this->litterDepositTotal - $this->licenseeFactor 
	              	  - $this->get_data("adjustment_1") + $this->get_data("adjustment_2"); 
	        else
	        		$this->orderGrandTotal = 0;
	      
         }
		 else
		 {

			 $this->GST = 0;
	        $this->licenseeFactor = 0;
	        
	        if($this->orderSubTotal!=0)
		        $this->orderGrandTotal = $this->orderSubTotal + $this->litterDepositTotal +$this->orderSubTotal*$this->GST_rate; 
		    else
		    {
		    	$this->orderGrandTotal = 0;
		    	$this->litterDepositTotal=0;
		    }
	        
	        $this->GST = $this->orderSubTotal*$this->GST_rate;

		}       
      $customer = & new bllcustomer();
      $customerID = $this->get_data("customer_id");
      $customer->loadByPrimaryKey($customerID);
      $this->delivery_info=$customer->get_data("best_time_to_deliver");
      
    }

}

class bllorders extends dalorderscollection
{
    function bllorders()
    {
            parent::dalorderscollection();
    }
	
    function &getByPrimaryKey($keyValues)
    {
        $dal = parent::getByPrimaryKey($keyValues);
        if ($dal)
        {
            $bll = & new bllorder();
            if ($bll->getFromDAL($dal))
                return $bll;
        }
        return nulll;
    }

	function isUniqInvoice($estate_id,$order_id,$invoice_number)
	{
		$db = & Db::getInstance();
        $db->setFetchMode(ADODB_FETCH_ASSOC);
        
        $sql="Select * from orders where estate_id =$estate_id and order_id!=$order_id and invoice_number=$invoice_number and deleted=0";
        $rows = $db->getAll($sql);
        
        if(count($rows)>0)
        	return false;
        else
        	return true;

	}
    function add_new()
    {
        //override collection add_new, return bll class
        $bll = & new bllorder();
        return $bll;
    }
	
}
?>